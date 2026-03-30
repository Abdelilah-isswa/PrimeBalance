<?php

namespace App\Http\Controllers;

use App\Services\SupplierService;
use App\Models\Supplier;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    use HasCompanyAuthorization;
    
    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    public function create($companyId)
    {
        $company = $this->getCompanyAsOwner($companyId, 'create suppliers');
        return view('suppliers.create', compact('company'));
    }

    public function store(StoreSupplierRequest $request, $companyId)
    {
        $data = array_merge($request->validated(), ['company_id' => $companyId]);
        $this->supplierService->createSupplier($data);
        return redirect()->route('companies.show', $companyId);
    }

    public function edit($companyId, $supplierId)
    {
        $company = $this->getCompanyAsOwner($companyId, 'edit suppliers');
        $supplier = $company->suppliers()->findOrFail($supplierId);
        return view('suppliers.edit', compact('company', 'supplier'));
    }

    public function update(UpdateSupplierRequest $request, $companyId, $supplierId)
    {
        $company = $this->getCompanyAsOwner($companyId, 'update suppliers');
        $supplier = $company->suppliers()->findOrFail($supplierId);
        $this->supplierService->updateSupplier($supplier, $request->validated());
        return redirect()->route('companies.show', $companyId)->with('success', 'Supplier updated');
    }

    public function destroy($companyId, $supplierId)
    {
        $company = $this->getCompanyAsOwner($companyId, 'delete suppliers');
        $supplier = $company->suppliers()->findOrFail($supplierId);
        
        if (!$this->supplierService->deleteSupplier($supplier)) {
            return back()->with('error', 'Cannot delete supplier with bills');
        }
        
        return redirect()->route('companies.show', $companyId)->with('success', 'Supplier deleted');
    }

    public function balances($companyId)
    {
        $company = $this->getAuthorizedCompany($companyId);
        $suppliers = $company->suppliers()->with('bills')->get();
        $suppliers = $this->supplierService->calculateSupplierBalances($suppliers);
        
        return view('suppliers.balances', compact('company', 'suppliers'));
    }
}
