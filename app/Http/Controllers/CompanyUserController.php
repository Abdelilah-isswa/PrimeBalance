<?php

namespace App\Http\Controllers;

use App\Services\CompanyService;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Http\Traits\HasCompanyAuthorization;

class CompanyUserController extends Controller
{
    use HasCompanyAuthorization;
    
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function index($companyId)
    {
        $company = $this->getCompanyForMember($companyId);
        $company->load(['users' => function($query) {
            $query->whereNull('company_user.left_at');
        }]);
        
        return view('companies.users.index', compact('company'));
    }

    public function destroy($companyId, $userId)
    {
        $company = $this->getCompanyForOwner($companyId, 'remove users');
        $result = $this->companyService->removeUser($company, $userId);
        
        if ($result['success']) {
            return back()->with('success', $result['message']);
        }
        
        return back()->with('error', $result['message']);
    }

    public function updateRole(UpdateUserRoleRequest $request, $companyId, $userId)
    {
        $company = $this->getCompanyForMember($companyId);
        $result = $this->companyService->updateUserRole($company, $userId, $request->role);
        
        if ($result['success']) {
            return back()->with('success', $result['message']);
        }
        
        return back()->with('error', $result['message']);
    }
}