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

    public function show($id)
    {
        $company = Auth::user()->companies()->findOrFail($id);
        
        // Dashboard metrics
        $totalIncome = $company->transactions()->where('type', 'income')->sum('amount');
        $totalExpense = $company->transactions()->where('type', 'expense')->sum('amount');
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
            'unpaidBills'
        ));
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
