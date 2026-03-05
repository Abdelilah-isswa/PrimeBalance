<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index($companyId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        $transactions = $company->transactions()
            ->with(['account', 'category', 'invoice.client', 'bill.supplier'])
            ->orderBy('date', 'desc')
            ->get();
        
        return view('transactions.index', compact('company', 'transactions'));
    }

    public function create($companyId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can create transactions');
        }

        $accounts = $company->accounts()->where('is_active', true)->get();
        $categories = $company->categories;

        return view('transactions.create', compact('company', 'accounts', 'categories'));
    }

    public function store(Request $request, $companyId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can create transactions');
        }

        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'nullable|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'date' => 'required|date',
        ]);

        $account = \App\Models\Account::findOrFail($request->account_id);

        \App\Models\Transaction::create([
            'company_id' => $companyId,
            'account_id' => $request->account_id,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        if ($request->type === 'income') {
            $account->increment('balance', $request->amount);
        } else {
            $account->decrement('balance', $request->amount);
        }

        return redirect("/companies/{$companyId}/transactions")->with('success', 'Transaction created successfully');
    }
}
