<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Controllers\Api\BaseController;
use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends BaseController
{
    use HasCompanyAuthorization;
    
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index($companyId)
    {
        $company = $this->getCompanyForMember($companyId);
        $transactions = $company->transactions()
            ->with(['account', 'category', 'invoice.client', 'bill.supplier'])
            ->orderBy('date', 'desc')
            ->get();
        
        return $this->sendResponse(compact('company', 'transactions'));
    }

    public function store(StoreTransactionRequest $request, $companyId)
    {
        $data = array_merge($request->validated(), ['company_id' => $companyId]);
        $transaction = $this->transactionService->createTransaction($data);
        return $this->sendCreated($transaction);
    }
    
    public function update(StoreTransactionRequest $request, $companyId, $transactionId)
    {
        $company = $this->getCompanyForMember($companyId);
        $transaction = $company->transactions()->findOrFail($transactionId);
        
        $this->transactionService->updateTransaction($transaction, $request->validated());
        
        return $this->sendResponse($transaction->fresh(['account', 'category', 'invoice.client', 'bill.supplier']));
    }

    public function destroy($companyId, $transactionId)
    {
        $company = $this->getCompanyForMember($companyId);
        $transaction = $company->transactions()->findOrFail($transactionId);

        $this->transactionService->deleteTransaction($transaction);

        return $this->sendResponse([], 'Transaction deleted');
    }
}
