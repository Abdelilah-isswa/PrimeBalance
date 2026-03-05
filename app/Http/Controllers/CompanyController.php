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
