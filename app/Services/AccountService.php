<?php

namespace App\Services;

use App\Models\Account;

class AccountService
{
    public function createAccount(array $data): Account
    {
        return Account::create([
            'company_id' => $data['company_id'],
            'name' => $data['name'],
            'balance' => $data['balance'],
            'is_active' => $data['is_active'] ?? false,
        ]);
    }

    public function updateAccount(Account $account, array $data): bool
    {
        return $account->update([
            'name' => $data['name'],
            'is_active' => $data['is_active'] ?? false,
        ]);
    }

    public function deleteOrArchiveAccount(Account $account): array
    {
        if ($account->transactions()->exists()) {
            $account->update(['is_active' => false]);
            return ['success' => true, 'message' => 'Account archived', 'archived' => true];
        }
        
        $account->delete();
        return ['success' => true, 'message' => 'Account deleted', 'archived' => false];
    }
}