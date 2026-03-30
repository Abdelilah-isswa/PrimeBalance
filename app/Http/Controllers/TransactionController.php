<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

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

        $data = array_merge($request->only('account_id', 'category_id', 'type', 'amount', 'description', 'date'), [
            'company_id' => $companyId,
        ]);

        $this->transactionService->createTransaction($data);

        return redirect("/companies/{$companyId}/transactions")->with('success', 'Transaction created successfully');
    }
}
