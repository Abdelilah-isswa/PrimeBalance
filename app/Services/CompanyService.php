<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use App\Mail\CompanyInvitationMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CompanyService
{
    public function getDashboardMetrics(Company $company, ?string $startDate = null, ?string $endDate = null): array
    {
        $transactionsQuery = $company->transactions();
        
        if ($startDate && $endDate) {
            $transactionsQuery->whereBetween('date', [$startDate, $endDate]);
        }
        
        return [
            'totalIncome' => (clone $transactionsQuery)->where('type', 'income')->sum('amount'),
            'totalExpense' => (clone $transactionsQuery)->where('type', 'expense')->sum('amount'),
            'bankBalance' => $company->accounts()->sum('balance'),
            'unpaidInvoices' => $company->invoices()->whereIn('status', ['draft', 'sent'])->count(),
            'unpaidBills' => $company->bills()->whereIn('status', ['draft', 'sent'])->count(),
        ];
    }

    public function createCompany(array $data): Company
    {
        $company = Company::create([
            'name' => $data['name'],
            'address' => $data['address'],
            'currency' => $data['currency'],
            'start_date' => now()->toDateString(),
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
        return $company->update(['end_date' => now()->toDateString()]);
    }

    public function inviteUser(Company $company, string $email, string $role): array
    {
        // Check if user already exists and is in company
        $user = User::where('email', $email)->first();
        if ($user && $company->users()->where('user_id', $user->id)->exists()) {
            return ['success' => false, 'message' => 'User is already a member of this company'];
        }

        // Check if there's already a pending invitation
        $existingInvitation = Invitation::where('company_id', $company->id)
            ->where('email', $email)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if ($existingInvitation) {
            return ['success' => false, 'message' => 'An invitation has already been sent to this email'];
        }

        // Create invitation
        $token = Str::random(64);
        $invitation = Invitation::create([
            'company_id' => $company->id,
            'email' => $email,
            'role' => $role,
            'token' => $token,
            'invited_by' => Auth::id(),
            'expires_at' => now()->addDays(7),
        ]);

        // Send invitation email
        Mail::to($email)->send(new CompanyInvitationMail(
            $company,
            $role,
            Auth::user()->name,
            $token
        ));

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