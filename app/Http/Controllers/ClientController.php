<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function create($companyId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can create clients');
        }

        return view('clients.create', compact('company'));
    }

    public function store(Request $request, $companyId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can create clients');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'address' => 'required|string',
            'phone' => 'required|string',
        ]);

        Client::create([
            'company_id' => $companyId,
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'phone' => $request->phone,
        ]);

        return redirect("/companies/{$companyId}");
    }

    public function edit($companyId, $clientId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can edit clients');
        }

        $client = $company->clients()->findOrFail($clientId);
        return view('clients.edit', compact('company', 'client'));
    }

    public function update(Request $request, $companyId, $clientId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can update clients');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'address' => 'required|string',
            'phone' => 'required|string',
        ]);

        $client = $company->clients()->findOrFail($clientId);
        $client->update([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'phone' => $request->phone,
        ]);

        return redirect("/companies/{$companyId}")->with('success', 'Client updated');
    }

    public function destroy($companyId, $clientId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can delete clients');
        }

        $client = $company->clients()->findOrFail($clientId);
        
        if ($client->invoices()->exists()) {
            return back()->with('error', 'Cannot delete client with invoices');
        }
        
        $client->delete();
        return redirect("/companies/{$companyId}")->with('success', 'Client deleted');
    }

    public function balances($companyId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        $clients = $company->clients()->with('invoices')->get()->map(function($client) {
            $totalInvoiced = $client->invoices->sum('total_amount');
            $totalPaid = $client->invoices->where('status', 'paid')->sum('total_amount');
            $client->balance = $totalPaid - $totalInvoiced;
            return $client;
        });
        
        return view('clients.balances', compact('company', 'clients'));
    }
}
