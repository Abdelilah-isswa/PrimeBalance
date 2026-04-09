<?php

namespace App\Http\Controllers;

use App\Services\ClientService;
use App\Models\Client;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Controllers\Api\BaseController;
use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Http\Request;

class ClientController extends BaseController
{
    use HasCompanyAuthorization;

    protected $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function create($companyId)
    {
        $company = $this->getCompanyWithRole($companyId, ['owner', 'admin', 'accountant'], 'create clients');
        return $this->sendResponse(compact('company'));
    }

    public function store(StoreClientRequest $request, $companyId)
    {
        $data = array_merge($request->validated(), ['company_id' => $companyId]);
        $client = $this->clientService->createClient($data);
        return $this->sendCreated($client);
    }

    public function edit($companyId, $clientId)
    {
        $company = $this->getCompanyWithRole($companyId, ['owner', 'admin', 'accountant'], 'edit clients');
        $client = $company->clients()->findOrFail($clientId);
        return $this->sendResponse(compact('company', 'client'));
    }

    public function update(UpdateClientRequest $request, $companyId, $clientId)
    {
        $company = $this->getCompanyForMember($companyId);
        $client = $company->clients()->findOrFail($clientId);
        $this->clientService->updateClient($client, $request->validated());
        return $this->sendResponse($client->fresh(), 'Client updated');
    }

    public function destroy($companyId, $clientId)
    {
        $company = $this->getCompanyWithRole($companyId, ['owner', 'admin', 'accountant'], 'delete clients');
        $client = $company->clients()->findOrFail($clientId);
        
        if (!$this->clientService->deleteClient($client)) {
            return $this->sendError('Cannot delete client with invoices');
        }
        
        return $this->sendResponse([], 'Client deleted');
    }

    public function balances($companyId)
    {
        $company = $this->getCompanyForMember($companyId);
        $clients = $company->clients()->with('invoices')->get();
        $clients = $this->clientService->calculateClientBalances($clients);
        
        return $this->sendResponse(compact('company', 'clients'));
    }
}

