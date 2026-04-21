<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use App\Mail\CompanyInvitationMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class CompanyService
{
    public function getDashboardMetrics(Company $company, ?string $startDate = null, ?string $endDate = null): array
    {
        $transactionsQuery = $company->transactions();
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();
        $today = now()->toDateString();
        $billsDueSoonEnd = now()->addDays(7)->toDateString();
        
        if ($startDate && $endDate) {
            $transactionsQuery->whereBetween('date', [$startDate, $endDate]);
        }

        $unpaidInvoiceStatuses = ['draft', 'sent', 'partial'];
        $unpaidBillStatuses = ['unpaid', 'partial'];

        $totalIncome = (clone $transactionsQuery)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $transactionsQuery)->where('type', 'expense')->sum('amount');

        $totalIncomeThisMonth = $company->transactions()
            ->where('type', 'income')
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->sum('amount');

        $totalExpenseThisMonth = $company->transactions()
            ->where('type', 'expense')
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->sum('amount');

        $unpaidInvoicesCount = $company->invoices()->whereIn('status', $unpaidInvoiceStatuses)->count();
        $unpaidInvoicesAmount = $company->invoices()->whereIn('status', $unpaidInvoiceStatuses)->sum('total_amount');
        $expectedIncome = $company->invoices()
            ->whereIn('status', $unpaidInvoiceStatuses)
            ->get(['total_amount', 'amount_paid'])
            ->sum(function ($invoice) {
                $total = (float) ($invoice->total_amount ?? 0);
                $paid = (float) ($invoice->amount_paid ?? 0);
                return max(0, $total - $paid);
            });

        $overdueInvoicesCount = $company->invoices()
            ->whereIn('status', $unpaidInvoiceStatuses)
            ->whereDate('due_date', '<', $today)
            ->count();

        $overdueInvoicesAmount = $company->invoices()
            ->whereIn('status', $unpaidInvoiceStatuses)
            ->whereDate('due_date', '<', $today)
            ->sum('total_amount');

        $paidInvoicesThisMonth = $company->invoices()
            ->where('status', 'paid')
            ->whereBetween('updated_at', [$monthStart . ' 00:00:00', $monthEnd . ' 23:59:59'])
            ->count();

        $unpaidBillsCount = $company->bills()->whereIn('status', $unpaidBillStatuses)->count();
        $paidBillsCount = $company->bills()->where('status', 'paid')->count();

        $billsDueSoonCount = $company->bills()
            ->whereIn('status', $unpaidBillStatuses)
            ->whereBetween('due_date', [$today, $billsDueSoonEnd])
            ->count();
        
        return [
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'bankBalance' => $company->accounts()->sum('balance'),
            'unpaidInvoices' => $unpaidInvoicesCount,
            'unpaidBills' => $unpaidBillsCount,
            'totalIncomeThisMonth' => $totalIncomeThisMonth,
            'totalExpenseThisMonth' => $totalExpenseThisMonth,
            'profitThisMonth' => $totalIncomeThisMonth - $totalExpenseThisMonth,
            'expectedIncome' => $expectedIncome,
            'unpaidInvoicesCount' => $unpaidInvoicesCount,
            'unpaidInvoicesAmount' => $unpaidInvoicesAmount,
            'overdueInvoicesCount' => $overdueInvoicesCount,
            'overdueInvoicesAmount' => $overdueInvoicesAmount,
            'paidInvoicesThisMonth' => $paidInvoicesThisMonth,
            'unpaidBillsCount' => $unpaidBillsCount,
            'paidBillsCount' => $paidBillsCount,
            'billsDueSoonCount' => $billsDueSoonCount,
        ];
    }

    public function createCompany(array $data): Company
    {
        $company = Company::create([
            'name' => $data['name'],
            'address' => $data['address'],
            'currency' => $data['currency'],
        ]);
        
        $company->users()->attach(Auth::id(), ['role' => 'owner']);
        
        return $company;
    }

    public function updateCompany(Company $company, array $data): bool
    {
        return $company->update([
            'name' => $data['name'],
            'address' => $data['address'],
            'currency' => $data['currency'],
        ]);
    }

    public function deactivateCompany(Company $company): bool
    {
        return (bool) $company->delete();
    }

    public function inviteUser(Company $company, string $email, string $role): array
    {
        // Check if user already exists and is in company
        $user = User::where('email', $email)->first();
        if ($user && $company->users()->where('user_id', $user->id)->exists()) {
            return ['success' => false, 'message' => 'User is already a member of this company', 'code' => 400];
        }

        // Check if there's already a pending invitation
        $existingInvitation = Invitation::where('company_id', $company->id)
            ->where('email', $email)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if ($existingInvitation) {
            return ['success' => false, 'message' => 'An invitation has already been sent to this email', 'code' => 400];
        }

        $token = Str::random(64);
        try {
            DB::transaction(function () use ($company, $email, $role, $token) {
                Invitation::create([
                    'company_id' => $company->id,
                    'email' => $email,
                    'role' => $role,
                    'token' => $token,
                    'invited_by' => Auth::id(),
                    'expires_at' => now()->addDays(7),
                ]);

                Mail::to($email)->send(new CompanyInvitationMail(
                    $company,
                    $role,
                    Auth::user()->name,
                    $token
                ));
            });
        } catch (Throwable $e) {
            report($e);
            return [
                'success' => false,
                'message' => 'Unable to send invitation email right now. Please check mail configuration and try again.',
                'code' => 500,
            ];
        }

        return ['success' => true, 'message' => 'Invitation sent to ' . $email];
    }

    public function acceptInvitation(Invitation $invitation): array
    {
        if ($invitation->isExpired()) {
            $invitation->update(['status' => 'expired']);
            return ['success' => false, 'message' => 'This invitation has expired'];
        }

        // Check if logged-in user email matches invitation
        if (Auth::user()->email !== $invitation->email) {
            return ['success' => false, 'message' => 'This invitation was sent to ' . $invitation->email];
        }

        // Check if user is already in the company
        if (!$invitation->company->users()->where('user_id', Auth::id())->exists()) {
            $invitation->company->users()->attach(Auth::id(), ['role' => $invitation->role]);
        }
        
        $invitation->update(['status' => 'accepted']);

        return [
            'success' => true, 
            'redirect' => '/companies/' . $invitation->company_id,
            'message' => 'You have joined ' . $invitation->company->name
        ];
    }

    public function removeUser(Company $company, int $userId): array
    {
        if ($userId == Auth::id()) {
            return ['success' => false, 'message' => 'You cannot remove yourself'];
        }

        $company->users()->updateExistingPivot($userId, ['left_at' => now()]);

        return ['success' => true, 'message' => 'User removed from company'];
    }

    public function updateUserRole(Company $company, int $userId, string $role): array
    {
        if ($userId == Auth::id()) {
            return ['success' => false, 'message' => 'You cannot change your own role'];
        }

        $company->users()->updateExistingPivot($userId, ['role' => $role]);

        return ['success' => true, 'message' => 'User role updated'];
    }
}