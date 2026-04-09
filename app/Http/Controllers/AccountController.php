<?php

namespace App\Http\Controllers;

use App\Services\AccountService;
use App\Models\Account;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Http\Traits\HasCompanyAuthorization;

class AccountController extends BaseController
{
    use HasCompanyAuthorization;

    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function index($companyId)
    {
        $company = $this->getCompanyForMember($companyId);
        $accounts = $company->accounts()->withCount('transactions')->get();
        return $this->sendResponse(compact('company', 'accounts'));
    }

    public function create($companyId)
    {
        $company = $this->getCompanyWithRole($companyId, ['owner', 'admin'], 'create accounts');
        return $this->sendResponse(compact('company'));
    }

    public function store(StoreAccountRequest $request, $companyId)
    {
        $data = array_merge($request->validated(), ['company_id' => $companyId]);
        $account = $this->accountService->createAccount($data);
        return $this->sendCreated($account, 'Account created');
    }

    public function edit($companyId, $accountId)
    {
        $company = $this->getCompanyWithRole($companyId, ['owner', 'admin'], 'edit accounts');
        $account = $company->accounts()->findOrFail($accountId);
        return $this->sendResponse(compact('company', 'account'));
    }

    public function update(UpdateAccountRequest $request, $companyId, $accountId)
    {
        $company = $this->getCompanyForMember($companyId);
        $account = $company->accounts()->findOrFail($accountId);
        $this->accountService->updateAccount($account, $request->validated());
        return $this->sendResponse($account->fresh(), 'Account updated');
    }

    public function destroy($companyId, $accountId)
    {
        $company = $this->getCompanyWithRole($companyId, ['owner', 'admin'], 'delete accounts');
        $account = $company->accounts()->findOrFail($accountId);
        $result = $this->accountService->deleteOrArchiveAccount($account);
        return $this->sendResponse($result, $result['message']);
    }
}

