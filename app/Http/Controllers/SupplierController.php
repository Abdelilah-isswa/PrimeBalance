<?php

namespace App\Http\Controllers;

use App\Services\SupplierService;
use App\Models\Supplier;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Controllers\Api\BaseController;
use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends BaseController
{
    use HasCompanyAuthorization;
    
    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    public function store(StoreSupplierRequest $request, $companyId)
    {
        $data = array_merge($request->validated(), ['company_id' => $companyId]);
        $supplier = $this->supplierService->createSupplier($data);
        return $this->sendCreated($supplier);
    }

    public function update(UpdateSupplierRequest $request, $companyId, $supplierId)
    {
        $company = $this->getCompanyWithRole($companyId, ['owner', 'admin', 'accountant'], 'update suppliers');
        $supplier = $company->suppliers()->findOrFail($supplierId);
        $this->supplierService->updateSupplier($supplier, $request->validated());
        return $this->sendResponse($supplier->fresh(), 'Supplier updated');
    }

    public function destroy($companyId, $supplierId)
    {
        $company = $this->getCompanyWithRole($companyId, ['owner', 'admin', 'accountant'], 'delete suppliers');
        $supplier = $company->suppliers()->findOrFail($supplierId);
        
        if (!$this->supplierService->deleteSupplier($supplier)) {
            return $this->sendError('Cannot delete supplier with bills');
        }
        
        return $this->sendResponse([], 'Supplier deleted');
    }

    public function balances($companyId)
    {
        $company = $this->getCompanyForMember($companyId);
        $suppliers = $company->suppliers()->with('bills')->get();
        $suppliers = $this->supplierService->calculateSupplierBalances($suppliers);
        
        return $this->sendResponse(compact('company', 'suppliers'));
    }
}
