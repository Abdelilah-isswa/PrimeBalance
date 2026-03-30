<?php

namespace App\Services;

use App\Models\Client;

class ClientService
{
    public function createClient(array $data): Client
    {
        return Client::create([
            'company_id' => $data['company_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'address' => $data['address'],
            'phone' => $data['phone'],
        ]);
    }

    public function updateClient(Client $client, array $data): bool
    {
        return $client->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'address' => $data['address'],
            'phone' => $data['phone'],
        ]);
    }

    public function canDelete(Client $client): bool
    {
        return !$client->invoices()->exists();
    }

    public function deleteClient(Client $client): bool
    {
        if (!$this->canDelete($client)) {
            return false;
        }

        return $client->delete();
    }

    public function calculateClientBalances($clients)
    {
        return $clients->map(function($client) {
            $totalInvoiced = $client->invoices->sum('total_amount');
            $totalPaid = $client->invoices->where('status', 'paid')->sum('total_amount');
            $client->balance = $totalPaid - $totalInvoiced;
            return $client;
        });
    }
}