<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function create($companyId, $clientId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can create invoices');
        }

        $client = $company->clients()->findOrFail($clientId);

        return view('invoices.create', compact('company', 'client'));
    }

    public function store(Request $request, $companyId, $clientId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can create invoices');
        }

        $request->validate([
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,sent,paid,cancelled',
        ]);

        Invoice::create([
            'company_id' => $companyId,
            'client_id' => $clientId,
            'total_amount' => $request->total_amount,
            'status' => $request->status,
        ]);

        return redirect("/companies/{$companyId}");
    }
}
