<?php

namespace App\Http\Controllers;

use App\Services\BillService;
use App\Models\Bill;
use App\Http\Requests\StoreBillRequest;
use App\Http\Requests\UpdateBillRequest;
use App\Http\Requests\PayBillRequest;
use App\Http\Controllers\Api\BaseController;
use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BillController extends BaseController
{
    use HasCompanyAuthorization;
    
    protected $billService;

    public function __construct(BillService $billService)
    {
        $this->billService = $billService;
    }

    public function index($companyId)
    {
        $company = $this->getCompanyForMember($companyId);
        $bills = $company->bills()->with('supplier')->orderByDesc('id')->get();
        
        return $this->sendResponse(compact('company', 'bills'));
    }

    public function create($companyId, $supplierId)
    {
        $company = $this->getCompanyWithRole($companyId, ['owner', 'admin', 'accountant'], 'create bills');
        $supplier = $company->suppliers()->findOrFail($supplierId);

        return $this->sendResponse(compact('company', 'supplier'));
    }

    public function store(StoreBillRequest $request, $companyId)
    {
        $supplierId = $request->input('supplier_id');
        $data = array_merge($request->validated(), [
            'company_id' => $companyId,
            'supplier_id' => $supplierId,
        ]);

        $bill = $this->billService->createBill($data);
        $bill->load('supplier');

        return $this->sendCreated($bill);
    }

    public function showPayment($companyId, $billId)
    {
        $company = $this->getCompanyWithRole($companyId, ['owner', 'admin', 'accountant'], 'pay bills');
        $bill = $company->bills()->findOrFail($billId);
        $accounts = $company->accounts()->where('is_active', true)->get();
        $categories = $company->categories;

        return $this->sendResponse(compact('company', 'bill', 'accounts', 'categories'));
    }

    public function pay(PayBillRequest $request, $companyId, $billId)
    {
        $company = $this->getCompanyForMember($companyId);
        $bill = $company->bills()->findOrFail($billId);
        try {
            $this->billService->payBill($bill, $request->validated());
        } catch (\RuntimeException $e) {
            return $this->sendError($e->getMessage(), 422);
        }

        $bill->load('supplier');
        return $this->sendResponse($bill->fresh(), 'Bill paid successfully');
    }

    public function show($companyId, $billId)
    {
        $company = $this->getCompanyForMember($companyId);
        $bill = $company->bills()->with('supplier')->withCount('transactions')->findOrFail($billId);
        
        return $this->sendResponse(compact('company', 'bill'));
    }

    public function edit($companyId, $billId)
    {
        $company = $this->getCompanyWithRole($companyId, ['owner', 'admin', 'accountant'], 'edit bills');
        $bill = $company->bills()->with('supplier')->withCount('transactions')->findOrFail($billId);
        
        return $this->sendResponse(compact('company', 'bill'));
    }

    public function update(UpdateBillRequest $request, $companyId, $billId)
    {
        $company = $this->getCompanyForMember($companyId);
        $bill = $company->bills()->findOrFail($billId);
        try {
            $this->billService->updateBill($bill, $request->validated());
        } catch (\RuntimeException $e) {
            return $this->sendError($e->getMessage(), 422);
        }

        $bill->load('supplier');
        return $this->sendResponse($bill->fresh(), 'Bill updated successfully');
    }

    public function destroy($companyId, $billId)
    {
        $company = $this->getCompanyWithRole($companyId, ['owner', 'admin', 'accountant'], 'delete bills');
        $bill = $company->bills()->findOrFail($billId);
        try {
            $this->billService->deleteBill($bill);
        } catch (\RuntimeException $e) {
            return $this->sendError($e->getMessage(), 422);
        }

        return $this->sendResponse([], 'Bill deleted successfully');
    }

    public function downloadPdf($companyId, $billId)
    {
        $company = $this->getCompanyForMember($companyId);
        $bill = $company->bills()->with('supplier')->findOrFail($billId);

        $pdf = Pdf::loadView('bills.pdf', compact('company', 'bill'));
        return $pdf->download('bill-' . $bill->id . '.pdf');
    }
}

