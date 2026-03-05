<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Auth::user()->companies;
        return view('companies.index', compact('companies'));
    }

    public function show(Request $request, $id)
    {
        $company = Auth::user()->companies()->findOrFail($id);
        
        // Get date range from request
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Build query based on date range
        $transactionsQuery = $company->transactions();
        
        if ($startDate && $endDate) {
            $transactionsQuery->whereBetween('date', [$startDate, $endDate]);
        }
        
        // Dashboard metrics
        $totalIncome = (clone $transactionsQuery)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $transactionsQuery)->where('type', 'expense')->sum('amount');
        $netProfit = $totalIncome - $totalExpense;
        $bankBalance = $company->accounts()->sum('balance');
        $unpaidInvoices = $company->invoices()->whereIn('status', ['draft', 'sent'])->count();
        $unpaidBills = $company->bills()->whereIn('status', ['draft', 'sent'])->count();
        
        return view('companies.show', compact(
            'company', 
            'totalIncome', 
            'totalExpense', 
            'netProfit', 
            'bankBalance', 
            'unpaidInvoices', 
            'unpaidBills',
            'startDate',
            'endDate'
        ));
    }

    public function edit($id)
    {
        $company = Auth::user()->companies()->findOrFail($id);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can edit company');
        }

        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $company = Auth::user()->companies()->findOrFail($id);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can update company');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'currency' => 'required|string|max:10',
        ]);

        $company->update([
            'name' => $request->name,
            'address' => $request->address,
            'currency' => $request->currency,
        ]);

        return redirect("/companies/{$id}")->with('success', 'Company updated successfully');
    }

    public function deactivate($id)
    {
        $company = Auth::user()->companies()->findOrFail($id);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can deactivate company');
        }

        $company->update(['end_date' => now()->toDateString()]);

        return redirect('/companies')->with('success', 'Company deactivated successfully');
    }

    public function inviteUser(Request $request, $id)
    {
        $company = Auth::user()->companies()->findOrFail($id);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can invite users');
        }

        $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:owner,accountant,standard_user,viewer',
        ]);

        // Check if user already exists and is in company
        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user && $company->users()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'User is already a member of this company');
        }

        // Check if there's already a pending invitation
        $existingInvitation = \App\Models\Invitation::where('company_id', $id)
            ->where('email', $request->email)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if ($existingInvitation) {
            return back()->with('error', 'An invitation has already been sent to this email');
        }

        // Create invitation
        $token = \Str::random(64);
        $invitation = \App\Models\Invitation::create([
            'company_id' => $id,
            'email' => $request->email,
            'role' => $request->role,
            'token' => $token,
            'invited_by' => Auth::id(),
            'expires_at' => now()->addDays(7),
        ]);

        // Send invitation email
        \Mail::to($request->email)->send(new \App\Mail\CompanyInvitationMail(
            $company,
            $request->role,
            Auth::user()->name,
            $token
        ));

        return back()->with('success', 'Invitation sent to ' . $request->email);
    }

    public function showInvitation($token)
    {
        $invitation = \App\Models\Invitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        if ($invitation->isExpired()) {
            $invitation->update(['status' => 'expired']);
            return view('invitations.expired');
        }

        return view('invitations.accept', compact('invitation'));
    }

    public function acceptInvitation($token)
    {
        $invitation = \App\Models\Invitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        if ($invitation->isExpired()) {
            $invitation->update(['status' => 'expired']);
            return redirect('/login')->with('error', 'This invitation has expired');
        }

        // Check if user is logged in
        if (!Auth::check()) {
            // Store invitation token in session and redirect to login
            session(['invitation_token' => $token]);
            return redirect('/login')->with('message', 'Please login or register to accept the invitation');
        }

        // Check if logged-in user email matches invitation
        if (Auth::user()->email !== $invitation->email) {
            return redirect('/')->with('error', 'This invitation was sent to ' . $invitation->email);
        }

        // Check if user is already in the company
        if (!$invitation->company->users()->where('user_id', Auth::id())->exists()) {
            $invitation->company->users()->attach(Auth::id(), ['role' => $invitation->role]);
        }
        
        $invitation->update(['status' => 'accepted']);

        return redirect('/companies/' . $invitation->company_id)->with('success', 'You have joined ' . $invitation->company->name);
    }

    public function declineInvitation($token)
    {
        $invitation = \App\Models\Invitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        $invitation->update(['status' => 'expired']);

        return redirect('/login')->with('message', 'Invitation declined');
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'currency' => 'required|string|max:10',
        ]);

        $company = Company::create([
            'name' => $request->name,
            'address' => $request->address,
            'currency' => $request->currency,
            'start_date' => now()->toDateString(),
        ]);
        
        $company->users()->attach(Auth::id(), ['role' => 'owner']);

        return redirect('/')->with('success', 'Company created successfully');
    }
}
