<?php

namespace App\Http\Controllers;

use App\Services\BillService;
use App\Models\Bill;
use App\Http\Requests\StoreBillRequest;
use App\Http\Requests\UpdateBillRequest;
use App\Http\Requests\PayBillRequest;
use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
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
        $bills = $company->bills()->with('supplier')->get();
        
        return view('bills.index', compact('company', 'bills'));
    }

    public function create($companyId, $supplierId)
    {
        $company = $this->getCompanyForOwner($companyId, 'create bills');
        $supplier = $company->suppliers()->findOrFail($supplierId);

        return view('bills.create', compact('company', 'supplier'));
    }

    public function store(StoreBillRequest $request, $companyId, $supplierId)
    {
        $data = array_merge($request->validated(), [
            'company_id' => $companyId,
            'supplier_id' => $supplierId,
        ]);

        $this->billService->createBill($data);

        return redirect()->route('companies.show', $companyId);
    }

    public function showPayment($companyId, $billId)
    {
        $company = $this->getCompanyForOwner($companyId, 'pay bills');
        $bill = $company->bills()->findOrFail($billId);
        $accounts = $company->accounts()->where('is_active', true)->get();
        $categories = $company->categories;

        return view('bills.pay', compact('company', 'bill', 'accounts', 'categories'));
    }

    public function pay(PayBillRequest $request, $companyId, $billId)
    {
        $company = $this->getCompanyForMember($companyId);
        $bill = $company->bills()->findOrFail($billId);
        $this->billService->payBill($bill, $request->validated());
        return redirect()->route('bills.index', $companyId)->with('success', 'Bill paid successfully');
    }

    public function show($companyId, $billId)
    {
        $company = $this->getCompanyForMember($companyId);
        $bill = $company->bills()->with('supplier')->findOrFail($billId);
        
        return view('bills.show', compact('company', 'bill'));
    }

    public function edit($companyId, $billId)
    {
        $company = $this->getCompanyForOwner($companyId, 'edit bills');
        $bill = $company->bills()->with('supplier')->findOrFail($billId);
        
        return view('bills.edit', compact('company', 'bill'));
    }

    public function update(UpdateBillRequest $request, $companyId, $billId)
    {
        $company = $this->getCompanyForMember($companyId);
        $bill = $company->bills()->findOrFail($billId);
        $this->billService->updateBill($bill, $request->validated());
        return redirect()->route('bills.show', [$companyId, $billId])->with('success', 'Bill updated successfully');
    }

    public function destroy($companyId, $billId)
    {
        $company = $this->getCompanyForOwner($companyId, 'delete bills');
        $bill = $company->bills()->findOrFail($billId);
        $this->billService->deleteBill($bill);

        return redirect()->route('bills.index', $companyId)->with('success', 'Bill deleted successfully');
    }
}
