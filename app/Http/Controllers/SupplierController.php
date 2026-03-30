<?php

namespace App\Http\Controllers;

use App\Services\SupplierService;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    public function create($companyId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can create suppliers');
        }

        return view('suppliers.create', compact('company'));
    }

    public function store(Request $request, $companyId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can create suppliers');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'address' => 'required|string',
            'phone' => 'required|string',
        ]);

        $data = array_merge($request->only('name', 'email', 'address', 'phone'), [
            'company_id' => $companyId,
        ]);

        $this->supplierService->createSupplier($data);

        return redirect("/companies/{$companyId}");
    }

    public function edit($companyId, $supplierId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can edit suppliers');
        }

        $supplier = $company->suppliers()->findOrFail($supplierId);
        return view('suppliers.edit', compact('company', 'supplier'));
    }

    public function update(Request $request, $companyId, $supplierId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can update suppliers');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'address' => 'required|string',
            'phone' => 'required|string',
        ]);

        $supplier = $company->suppliers()->findOrFail($supplierId);
        $this->supplierService->updateSupplier($supplier, $request->only('name', 'email', 'address', 'phone'));

        return redirect("/companies/{$companyId}")->with('success', 'Supplier updated');
    }

    public function destroy($companyId, $supplierId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can delete suppliers');
        }

        $supplier = $company->suppliers()->findOrFail($supplierId);
        
        if (!$this->supplierService->deleteSupplier($supplier)) {
            return back()->with('error', 'Cannot delete supplier with bills');
        }
        
        return redirect("/companies/{$companyId}")->with('success', 'Supplier deleted');
    }

    public function balances($companyId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        $suppliers = $company->suppliers()->with('bills')->get();
        $suppliers = $this->supplierService->calculateSupplierBalances($suppliers);
        
        return view('suppliers.balances', compact('company', 'suppliers'));
    }
}
