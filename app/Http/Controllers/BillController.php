<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{
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

    public function store(Request $request, $companyId, $supplierId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can create bills');
        }

        $request->validate([
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,sent,paid,cancelled',
        ]);

        Bill::create([
            'company_id' => $companyId,
            'supplier_id' => $supplierId,
            'total_amount' => $request->total_amount,
            'status' => $request->status,
        ]);

        return redirect("/companies/{$companyId}");
    }
}
