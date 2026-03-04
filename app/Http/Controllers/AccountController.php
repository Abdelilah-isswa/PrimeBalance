<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
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

        return redirect("/companies/{$companyId}");
    }
}
