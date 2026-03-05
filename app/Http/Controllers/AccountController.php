<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index($companyId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        $accounts = $company->accounts()->withCount('transactions')->get();
        return view('accounts.index', compact('company', 'accounts'));
    }

    public function create($companyId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can create accounts');
        }

        return view('accounts.create', compact('company'));
    }

    public function store(Request $request, $companyId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can create accounts');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'balance' => 'required|numeric',
            'is_active' => 'boolean',
        ]);

        Account::create([
            'company_id' => $companyId,
            'name' => $request->name,
            'balance' => $request->balance,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect("/companies/{$companyId}/accounts")->with('success', 'Account created');
    }

    public function edit($companyId, $accountId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can edit accounts');
        }

        $account = $company->accounts()->findOrFail($accountId);
        return view('accounts.edit', compact('company', 'account'));
    }

    public function update(Request $request, $companyId, $accountId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can update accounts');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $account = $company->accounts()->findOrFail($accountId);
        $account->update([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect("/companies/{$companyId}/accounts")->with('success', 'Account updated');
    }

    public function destroy($companyId, $accountId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can delete accounts');
        }

        $account = $company->accounts()->findOrFail($accountId);
        
        if ($account->transactions()->exists()) {
            $account->update(['is_active' => false]);
            return redirect("/companies/{$companyId}/accounts")->with('success', 'Account archived');
        }
        
        $account->delete();
        return redirect("/companies/{$companyId}/accounts")->with('success', 'Account deleted');
    }
}
