<?php

namespace App\Http\Controllers;

use App\Services\ClientService;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    protected $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

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

        $data = array_merge($request->only('name', 'email', 'address', 'phone'), [
            'company_id' => $companyId,
        ]);

        $this->clientService->createClient($data);

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
        $this->clientService->updateClient($client, $request->only('name', 'email', 'address', 'phone'));

        return redirect("/companies/{$companyId}")->with('success', 'Client updated');
    }

    public function destroy($companyId, $clientId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can delete clients');
        }

        $client = $company->clients()->findOrFail($clientId);
        
        if (!$this->clientService->deleteClient($client)) {
            return back()->with('error', 'Cannot delete client with invoices');
        }
        
        return redirect("/companies/{$companyId}")->with('success', 'Client deleted');
    }

    public function balances($companyId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        $clients = $company->clients()->with('invoices')->get();
        $clients = $this->clientService->calculateClientBalances($clients);
        
        return view('clients.balances', compact('company', 'clients'));
    }
}
