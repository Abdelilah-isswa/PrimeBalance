<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Services\CompanyService;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Http\Request;

class CompanyController extends BaseController
{
    use HasCompanyAuthorization;
    
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function index()
    {
        $companies = auth('sanctum')->user()->companies;
        $companies->loadCount('transactions');
        return $this->sendResponse($companies->toArray());
    }

    public function show(Request $request, $id)
    {
        $company = $this->getCompanyForMember($id);
        
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $metrics = $this->companyService->getDashboardMetrics($company, $startDate, $endDate);
        $netProfit = $metrics['totalIncome'] - $metrics['totalExpense'];
        
        $data = array_merge(
            compact('company', 'startDate', 'endDate', 'netProfit'),
            $metrics
        );

        return $this->sendResponse($data);
    }

    public function store(StoreCompanyRequest $request)
    {
        $company = $this->companyService->createCompany($request->validated());
        return $this->sendCreated($company, 'Company created successfully');
    }

    public function edit($id)
    {
        $company = $this->getCompanyForOwner($id, 'edit company');
        $userRole = $company->users()
            ->where('user_id', auth('sanctum')->id())
            ->first()
            ->pivot->role ?? 'viewer';
        return $this->sendResponse(compact('company', 'userRole'));
    }

    public function update(UpdateCompanyRequest $request, $id)
    {
        $company = $this->getCompanyForMember($id);
        $this->companyService->updateCompany($company, $request->validated());
        return $this->sendResponse($company->fresh(), 'Company updated successfully');
    }

    public function deactivate($id)
    {
        $company = $this->getCompanyForOwner($id, 'deactivate company');

        if ($company->transactions()->exists()) {
            return $this->sendError('You cannot deactivate this company because it has transactions.', 422);
        }

        $this->companyService->deactivateCompany($company);
        return $this->sendResponse([], 'Company deactivated successfully');
    }
}
?>

