<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;

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
            'created_by' => Auth::id(),
        ]);

        // Update account balance
        if ($data['type'] === 'income') {
            $account->increment('balance', $data['amount']);
        } else {
            $account->decrement('balance', $data['amount']);
        }

        return $transaction;
    }

    public function updateTransaction(Transaction $transaction, array $data): Transaction
    {
        // Reverse old transaction impact
        $oldAccount = Account::findOrFail($transaction->account_id);
        $oldAmount = (float) $transaction->amount;
        if ($transaction->type === 'income') {
            $oldAccount->decrement('balance', $oldAmount);
        } else {
            $oldAccount->increment('balance', $oldAmount);
        }

        // Apply new transaction
        $transaction->update([
            'account_id' => $data['account_id'],
            'category_id' => $data['category_id'] ?? null,
            'type' => $data['type'],
            'amount' => $data['amount'],
            'description' => $data['description'],
            'date' => $data['date'],
        ]);

        $newAccount = Account::findOrFail($data['account_id']);
        $newAmount = (float) $data['amount'];
        if ($data['type'] === 'income') {
            $newAccount->increment('balance', $newAmount);
        } else {
            $newAccount->decrement('balance', $newAmount);
        }

        return $transaction;
    }

    public function deleteTransaction(Transaction $transaction): void
    {
        $account = Account::findOrFail($transaction->account_id);
        $amount = (float) $transaction->amount;
        if ($transaction->type === 'income') {
            $account->decrement('balance', $amount);
        } else {
            $account->increment('balance', $amount);
        }
        $transaction->delete();
    }
}