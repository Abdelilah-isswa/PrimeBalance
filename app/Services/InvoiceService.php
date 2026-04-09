<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Account;
use App\Models\Transaction;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            // Create invoice with initial total_amount as 0 if items exist
            $totalAmount = isset($data['items']) && !empty($data['items']) ? 0 : ($data['total_amount'] ?? 0);

            $invoice = Invoice::create([
                'company_id' => $data['company_id'],
                'client_id'  => $data['client_id'],
                'total_amount' => $totalAmount,
                'due_date' => $data['due_date'] ?? null,
                'status'     => $data['status'] ?? 'draft',
            ]);

            // Add items if provided
            if (isset($data['items']) && is_array($data['items'])) {
                $totalAmount = 0;
                foreach ($data['items'] as $item) {
                    $itemTotal = (float)$item['price'] * (int)$item['quantity'];
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'description' => $item['description'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'total' => $itemTotal,
                    ]);
                    $totalAmount += $itemTotal;
                }
                $invoice->update(['total_amount' => $totalAmount]);
            }

            if (isset($data['send_email']) && $data['send_email']) {
                Mail::to($invoice->client->email)->send(new InvoiceMail($invoice));
            }

            return $invoice;
        });
    }

    public function updateInvoice(Invoice $invoice, array $data): bool
    {
        return DB::transaction(function () use ($invoice, $data) {
            // If items are provided, recalculate total from items
            if (isset($data['items']) && is_array($data['items'])) {
                // Delete existing items
                $invoice->items()->delete();
                
                $totalAmount = 0;
                foreach ($data['items'] as $item) {
                    $itemTotal = (float)$item['price'] * (int)$item['quantity'];
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'description' => $item['description'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'total' => $itemTotal,
                    ]);
                    $totalAmount += $itemTotal;
                }
                
                return $invoice->update([
                    'total_amount' => $totalAmount,
                    'due_date' => $data['due_date'] ?? $invoice->due_date,
                    'status'       => $data['status'] ?? $invoice->status,
                ]);
            }

            // If no items, allow manual total_amount update
            return $invoice->update([
                'total_amount' => $data['total_amount'] ?? $invoice->total_amount,
                'due_date' => $data['due_date'] ?? $invoice->due_date,
                'status'       => $data['status'] ?? $invoice->status,
            ]);
        });
    }

    /**
     * Mark invoice as paid (full or partial).
     * Creates an income transaction for the amount paid.
     * If amount_paid < total_amount the status becomes 'partial'.
     */
    public function receivePayment(Invoice $invoice, array $data): array
    {
        $amountPaid   = (float) $data['amount_paid'];
        $totalAmount  = (float) $invoice->total_amount;
        $account      = Account::findOrFail($data['account_id']);

        // Determine new status
        $newStatus = $amountPaid >= $totalAmount ? 'paid' : 'partial';

        // Create income transaction
        Transaction::create([
            'company_id'  => $invoice->company_id,
            'account_id'  => $data['account_id'],
            'category_id' => $data['category_id'] ?? null,
            'type'        => 'income',
            'amount'      => $amountPaid,
            'description' => 'Payment received for Invoice #' . $invoice->id . ' (' . $invoice->client->name . ')',
            'date'        => $data['date'],
            'invoice_id'  => $invoice->id,
        ]);

        // Update account balance
        $account->increment('balance', $amountPaid);

        // Update invoice status & track amount paid
        $invoice->update([
            'status'       => $newStatus,
            'amount_paid'  => ($invoice->amount_paid ?? 0) + $amountPaid,
        ]);

        $remaining = max(0, $totalAmount - (($invoice->amount_paid ?? 0) + $amountPaid));

        return [
            'status'    => $newStatus,
            'paid'      => $amountPaid,
            'remaining' => $remaining,
        ];
    }

    public function canDelete(Invoice $invoice): bool
    {
        return !$invoice->transactions()->exists();
    }

    public function deleteInvoice(Invoice $invoice): bool
    {
        if (!$this->canDelete($invoice)) {
            return false;
        }

        return $invoice->delete();
    }
}