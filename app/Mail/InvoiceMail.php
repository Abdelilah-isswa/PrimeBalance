<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $company;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->company = $invoice->company;
    }

    public function build()
    {
        return $this->subject('Invoice #' . $this->invoice->id . ' from ' . $this->company->name)
                    ->view('emails.invoice');
    }
}
