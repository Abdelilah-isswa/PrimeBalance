<?php

namespace App\Http\Controllers;

use App\Services\AccountService;
use App\Models\Account;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\BaseController;

class AccountController extends BaseController
{
    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function index($companyId)
    {
        $company = auth('sanctum')->user()->companies()->findOrFail($companyId);
        $accounts = $company->accounts()->withCount('transactions')->get();
        return $this->sendResponse(compact('company', 'accounts'));
    }

    public function create($companyId)
    {
        $company = auth('sanctum')->user()->companies()->findOrFail($companyId);
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
        $company = auth('sanctum')->user()->companies()->findOrFail($companyId);
        $account = $company->accounts()->findOrFail($accountId);
        return $this->sendResponse(compact('company', 'account'));
    }

    public function update(UpdateAccountRequest $request, $companyId, $accountId)
    {
        $company = auth('sanctum')->user()->companies()->findOrFail($companyId);
        $account = $company->accounts()->findOrFail($accountId);
        $this->accountService->updateAccount($account, $request->validated());
        return $this->sendResponse($account->fresh(), 'Account updated');
    }

    public function destroy($companyId, $accountId)
    {
        $company = auth('sanctum')->user()->companies()->findOrFail($companyId);
        $account = $company->accounts()->findOrFail($accountId);
        $result = $this->accountService->deleteOrArchiveAccount($account);
        return $this->sendResponse($result, $result['message']);
    }
}

