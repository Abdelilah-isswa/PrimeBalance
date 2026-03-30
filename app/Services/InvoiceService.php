<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Account;
use App\Models\Transaction;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;

class InvoiceService
{
    public function createInvoice(array $data): Invoice
    {
        $invoice = Invoice::create([
            'company_id' => $data['company_id'],
            'client_id' => $data['client_id'],
            'total_amount' => $data['total_amount'],
            'status' => $data['status'],
        ]);

        if (isset($data['send_email']) && $data['send_email']) {
            Mail::to($invoice->client->email)->send(new InvoiceMail($invoice));
        }

        return $invoice;
    }

    public function updateInvoice(Invoice $invoice, array $data): bool
    {
        return $invoice->update([
            'total_amount' => $data['total_amount'],
            'status' => $data['status'],
        ]);
    }

    public function receivePayment(Invoice $invoice, array $data): bool
    {
        $account = Account::findOrFail($data['account_id']);
        
        Transaction::create([
            'company_id' => $invoice->company_id,
            'account_id' => $data['account_id'],
            'category_id' => $data['category_id'] ?? null,
            'type' => 'income',
            'amount' => $invoice->total_amount,
            'description' => 'Payment received for invoice #' . $invoice->id . ' - ' . $invoice->client->name,
            'date' => $data['date'],
            'invoice_id' => $invoice->id,
        ]);

        $account->increment('balance', $invoice->total_amount);
        $invoice->update(['status' => 'paid']);

        return true;
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