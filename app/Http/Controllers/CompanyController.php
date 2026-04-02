<?php

namespace App\Http\Controllers;

use App\Services\CompanyService;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    use HasCompanyAuthorization;
    
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function index()
    {
        $companies = Auth::user()->companies;
        return view('companies.index', compact('companies'));
    }

    public function show(Request $request, $id)
    {
        $company = $this->getCompanyForMember($id);
        
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $metrics = $this->companyService->getDashboardMetrics($company, $startDate, $endDate);
        $netProfit = $metrics['totalIncome'] - $metrics['totalExpense'];
        
        return view('companies.show', array_merge(
            compact('company', 'startDate', 'endDate', 'netProfit'),
            $metrics
        ));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(StoreCompanyRequest $request)
    {
        $this->companyService->createCompany($request->validated());
        return redirect()->route('home')->with('success', 'Company created successfully');
    }

    public function edit($id)
    {
        $company = $this->getCompanyForOwner($id, 'edit company');
        $userRole = $company->users()
            ->where('user_id', auth()->id())
            ->first()
            ->pivot->role ?? 'viewer';
        return view('companies.edit', compact('company', 'userRole'));
    }

    public function update(UpdateCompanyRequest $request, $id)
    {
        $company = $this->getCompanyForMember($id);
        $this->companyService->updateCompany($company, $request->validated());
        return redirect()->route('companies.show', $id)->with('success', 'Company updated successfully');
    }

    public function deactivate($id)
    {
        $company = $this->getCompanyForOwner($id, 'deactivate company');
        $this->companyService->deactivateCompany($company);
        return redirect()->route('companies.index')->with('success', 'Company deactivated successfully');
    }
}
