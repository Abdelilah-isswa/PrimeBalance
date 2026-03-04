<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{
    public function index($companyId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        $bills = $company->bills()->with('supplier')->get();
        
        return view('bills.index', compact('company', 'bills'));
    }

    public function create($companyId, $supplierId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can create bills');
        }

        $supplier = $company->suppliers()->findOrFail($supplierId);

        return view('bills.create', compact('company', 'supplier'));
    }

    public function store(Request $request, $companyId, $supplierId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can create bills');
        }

        $request->validate([
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,sent,paid,cancelled',
        ]);

        Bill::create([
            'company_id' => $companyId,
            'supplier_id' => $supplierId,
            'total_amount' => $request->total_amount,
            'status' => $request->status,
        ]);

        return redirect("/companies/{$companyId}");
    }

    public function showPayment($companyId, $billId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can pay bills');
        }

        $bill = $company->bills()->findOrFail($billId);
        $accounts = $company->accounts()->where('is_active', true)->get();
        $categories = $company->categories;

        return view('bills.pay', compact('company', 'bill', 'accounts', 'categories'));
    }

    public function pay(Request $request, $companyId, $billId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can pay bills');
        }

        $bill = $company->bills()->findOrFail($billId);

        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'nullable|exists:categories,id',
            'date' => 'required|date',
        ]);

        $account = \App\Models\Account::findOrFail($request->account_id);

        \App\Models\Transaction::create([
            'company_id' => $companyId,
            'account_id' => $request->account_id,
            'category_id' => $request->category_id,
            'type' => 'expense',
            'amount' => $bill->total_amount,
            'description' => 'Payment for bill #' . $bill->id . ' - ' . $bill->supplier->name,
            'date' => $request->date,
            'bill_id' => $bill->id,
        ]);

        $account->decrement('balance', $bill->total_amount);
        $bill->update(['status' => 'paid']);

        return redirect("/companies/{$companyId}/bills")->with('success', 'Bill paid successfully');
    }
}
