<?php

namespace App\Http\Controllers;

use App\Services\CompanyService;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Http\Controllers\Api\BaseController;
use App\Http\Traits\HasCompanyAuthorization;

class CompanyUserController extends BaseController
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
        
        return $this->sendResponse($company);
    }

    public function destroy($companyId, $userId)
    {
        $company = $this->getCompanyForOwner($companyId, 'remove users');
        $result = $this->companyService->removeUser($company, $userId);
        
        if ($result['success']) {
            return $this->sendResponse($result);
        }
        
        return $this->sendError($result['message'], 400);
    }

    public function updateRole(UpdateUserRoleRequest $request, $companyId, $userId)
    {
        $company = $this->getCompanyForMember($companyId);
        $result = $this->companyService->updateUserRole($company, $userId, $request->role);
        
        if ($result['success']) {
            return $this->sendResponse($result);
        }
        
        return $this->sendError($result['message'], 400);
    }
}

