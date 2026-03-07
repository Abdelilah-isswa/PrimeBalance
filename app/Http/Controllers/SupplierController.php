<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
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

        Supplier::create([
            'company_id' => $companyId,
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'phone' => $request->phone,
        ]);

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
        $supplier->update([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'phone' => $request->phone,
        ]);

        return redirect("/companies/{$companyId}")->with('success', 'Supplier updated');
    }

    public function destroy($companyId, $supplierId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can delete suppliers');
        }

        $supplier = $company->suppliers()->findOrFail($supplierId);
        
        if ($supplier->bills()->exists()) {
            return back()->with('error', 'Cannot delete supplier with bills');
        }
        
        $supplier->delete();
        return redirect("/companies/{$companyId}")->with('success', 'Supplier deleted');
    }
}
