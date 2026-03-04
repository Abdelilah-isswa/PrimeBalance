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
        return view('companies.show', compact('company'));
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
