<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BillService
{
    public function createBill(array $data): Bill
    {
        return Bill::create([
            'company_id' => $data['company_id'],
            'supplier_id' => $data['supplier_id'],
            'description' => $data['description'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'total_amount' => $data['total_amount'],
            'status' => $data['status'] ?? 'unpaid',
            'amount_paid' => $data['amount_paid'] ?? 0,
        ]);
    }

    public function updateBill(Bill $bill, array $data): bool
    {
        if ($bill->transactions()->exists()) {
            throw new RuntimeException('Bills linked to transactions cannot be edited.');
        }

        if ($bill->status === 'paid') {
            throw new RuntimeException('Paid bills cannot be edited.');
        }

        return $bill->update([
            'supplier_id' => $data['supplier_id'] ?? $bill->supplier_id,
            'description' => $data['description'] ?? $bill->description,
            'due_date' => $data['due_date'] ?? $bill->due_date,
            'total_amount' => $data['total_amount'],
            'status' => $data['status'] ?? $bill->status,
        ]);
    }

    public function payBill(Bill $bill, array $data): bool
    {
        if ($bill->status === 'paid') {
            throw new RuntimeException('This bill is already fully paid.');
        }

        $paymentAmount = (float) ($data['amount'] ?? 0);
        if ($paymentAmount <= 0) {
            throw new RuntimeException('Payment amount must be greater than 0.');
        }

        return DB::transaction(function () use ($bill, $data, $paymentAmount) {
            $bill->loadMissing('supplier');

            $bill->refresh();
            $remaining = (float) $bill->total_amount - (float) $bill->amount_paid;
            if ($paymentAmount > $remaining) {
                throw new RuntimeException('Payment amount cannot exceed the remaining balance.');
            }

            $account = Account::findOrFail($data['account_id']);

            Transaction::create([
                'company_id' => $bill->company_id,
                'account_id' => $data['account_id'],
                'category_id' => $data['category_id'] ?? null,
                'type' => 'expense',
                'amount' => $paymentAmount,
                'description' => 'Payment for bill #' . $bill->id . ' - ' . ($bill->supplier->name ?? ''),
                'date' => $data['date'],
                'bill_id' => $bill->id,
            ]);

            $account->decrement('balance', $paymentAmount);

            $newAmountPaid = (float) $bill->amount_paid + $paymentAmount;
            $newStatus = $newAmountPaid >= (float) $bill->total_amount ? 'paid' : 'partial';

            $bill->update([
                'amount_paid' => $newAmountPaid,
                'status' => $newStatus,
            ]);

            return true;
        });
    }

    public function deleteBill(Bill $bill): bool
    {
        if ($bill->transactions()->exists()) {
            throw new RuntimeException('Cannot delete a bill that is linked to transactions.');
        }

        return (bool) $bill->delete();
    }
}