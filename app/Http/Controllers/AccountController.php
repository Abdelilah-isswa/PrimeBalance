<?php

namespace App\Http\Controllers;

use App\Services\AccountService;
use App\Models\Account;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function index($companyId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        $accounts = $company->accounts()->withCount('transactions')->get();
        return view('accounts.index', compact('company', 'accounts'));
    }

    public function create($companyId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        return view('accounts.create', compact('company'));
    }

    public function store(StoreAccountRequest $request, $companyId)
    {
        $data = array_merge($request->validated(), ['company_id' => $companyId]);
        $this->accountService->createAccount($data);
        return redirect("/companies/{$companyId}/accounts")->with('success', 'Account created');
    }

    public function edit($companyId, $accountId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        $account = $company->accounts()->findOrFail($accountId);
        return view('accounts.edit', compact('company', 'account'));
    }

    public function update(UpdateAccountRequest $request, $companyId, $accountId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        $account = $company->accounts()->findOrFail($accountId);
        $this->accountService->updateAccount($account, $request->validated());
        return redirect("/companies/{$companyId}/accounts")->with('success', 'Account updated');
    }

    public function destroy($companyId, $accountId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        $account = $company->accounts()->findOrFail($accountId);
        $result = $this->accountService->deleteOrArchiveAccount($account);
        return redirect("/companies/{$companyId}/accounts")->with('success', $result['message']);
    }
}
