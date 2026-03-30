<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Account;
use App\Models\Transaction;

class BillService
{
    public function createBill(array $data): Bill
    {
        return Bill::create([
            'company_id' => $data['company_id'],
            'supplier_id' => $data['supplier_id'],
            'total_amount' => $data['total_amount'],
            'status' => $data['status'],
        ]);
    }

    public function updateBill(Bill $bill, array $data): bool
    {
        return $bill->update([
            'total_amount' => $data['total_amount'],
            'status' => $data['status'],
        ]);
    }

    public function payBill(Bill $bill, array $data): bool
    {
        $account = Account::findOrFail($data['account_id']);

        Transaction::create([
            'company_id' => $bill->company_id,
            'account_id' => $data['account_id'],
            'category_id' => $data['category_id'] ?? null,
            'type' => 'expense',
            'amount' => $bill->total_amount,
            'description' => 'Payment for bill #' . $bill->id . ' - ' . $bill->supplier->name,
            'date' => $data['date'],
            'bill_id' => $bill->id,
        ]);

        $account->decrement('balance', $bill->total_amount);
        $bill->update(['status' => 'paid']);

        return true;
    }

    public function deleteBill(Bill $bill): bool
    {
        return $bill->delete();
    }
}