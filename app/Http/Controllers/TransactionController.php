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
}
