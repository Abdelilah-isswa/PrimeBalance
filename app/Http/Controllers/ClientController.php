<?php

namespace App\Http\Controllers;

use App\Services\ClientService;
use App\Models\Client;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    use HasCompanyAuthorization;
    
    protected $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function create($companyId)
    {
        $company = $this->getCompanyAsOwner($companyId, 'create clients');
        return view('clients.create', compact('company'));
    }

    public function store(StoreClientRequest $request, $companyId)
    {
        $data = array_merge($request->validated(), ['company_id' => $companyId]);
        $this->clientService->createClient($data);
        return redirect("/companies/{$companyId}");
    }

    public function edit($companyId, $clientId)
    {
        $company = $this->getCompanyAsOwner($companyId, 'edit clients');
        $client = $company->clients()->findOrFail($clientId);
        return view('clients.edit', compact('company', 'client'));
    }

    public function update(UpdateClientRequest $request, $companyId, $clientId)
    {
        $company = $this->getCompanyAsOwner($companyId, 'update clients');
        $client = $company->clients()->findOrFail($clientId);
        $this->clientService->updateClient($client, $request->validated());
        return redirect("/companies/{$companyId}")->with('success', 'Client updated');
    }

    public function destroy($companyId, $clientId)
    {
        $company = $this->getCompanyAsOwner($companyId, 'delete clients');
        $client = $company->clients()->findOrFail($clientId);
        
        if (!$this->clientService->deleteClient($client)) {
            return back()->with('error', 'Cannot delete client with invoices');
        }
        
        return redirect("/companies/{$companyId}")->with('success', 'Client deleted');
    }

    public function balances($companyId)
    {
        $company = $this->getAuthorizedCompany($companyId);
        $clients = $company->clients()->with('invoices')->get();
        $clients = $this->clientService->calculateClientBalances($clients);
        
        return view('clients.balances', compact('company', 'clients'));
    }
}
