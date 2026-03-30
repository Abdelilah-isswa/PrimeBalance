<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Account;

class TransactionService
{
    public function createTransaction(array $data): Transaction
    {
        $account = Account::findOrFail($data['account_id']);

        $transaction = Transaction::create([
            'company_id' => $data['company_id'],
            'account_id' => $data['account_id'],
            'category_id' => $data['category_id'] ?? null,
            'type' => $data['type'],
            'amount' => $data['amount'],
            'description' => $data['description'],
            'date' => $data['date'],
        ]);

        // Update account balance
        if ($data['type'] === 'income') {
            $account->increment('balance', $data['amount']);
        } else {
            $account->decrement('balance', $data['amount']);
        }

        return $transaction;
    }
}