<?php

namespace App\Http\Controllers;

use App\Services\BillService;
use App\Models\Bill;
use App\Http\Requests\StoreBillRequest;
use App\Http\Requests\UpdateBillRequest;
use App\Http\Requests\PayBillRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{
    protected $billService;

    public function __construct(BillService $billService)
    {
        $this->billService = $billService;
    }

    public function index($companyId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        $bills = $company->bills()->with('supplier')->get();
        
        return view('bills.index', compact('company', 'bills'));
    }

    public function create($companyId, $supplierId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can create bills');
        }

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

        return redirect("/companies/{$companyId}");
    }

    public function showPayment($companyId, $billId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can pay bills');
        }

        $bill = $company->bills()->findOrFail($billId);
        $accounts = $company->accounts()->where('is_active', true)->get();
        $categories = $company->categories;

        return view('bills.pay', compact('company', 'bill', 'accounts', 'categories'));
    }

    public function pay(PayBillRequest $request, $companyId, $billId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        $bill = $company->bills()->findOrFail($billId);
        $this->billService->payBill($bill, $request->validated());
        return redirect("/companies/{$companyId}/bills")->with('success', 'Bill paid successfully');
    }

    public function show($companyId, $billId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        $bill = $company->bills()->with('supplier')->findOrFail($billId);
        
        return view('bills.show', compact('company', 'bill'));
    }

    public function edit($companyId, $billId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can edit bills');
        }

        $bill = $company->bills()->with('supplier')->findOrFail($billId);
        
        return view('bills.edit', compact('company', 'bill'));
    }

    public function update(UpdateBillRequest $request, $companyId, $billId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        $bill = $company->bills()->findOrFail($billId);
        $this->billService->updateBill($bill, $request->validated());
        return redirect("/companies/{$companyId}/bills/{$billId}")->with('success', 'Bill updated successfully');
    }

    public function destroy($companyId, $billId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can delete bills');
        }

        $bill = $company->bills()->findOrFail($billId);
        $this->billService->deleteBill($bill);

        return redirect("/companies/{$companyId}/bills")->with('success', 'Bill deleted successfully');
    }
}
