<?php

namespace App\Http\Controllers;

use App\Services\InvoiceService;
use App\Models\Invoice;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Requests\ReceivePaymentRequest;
use App\Http\Controllers\Api\BaseController;
use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Http\Request;

class InvoiceController extends BaseController
{
    use HasCompanyAuthorization;
    
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function index($companyId)
    {
        $company = $this->getCompanyForMember($companyId);
        $invoices = $company->invoices()->with('client', 'items', 'creator')->get();
        
        return $this->sendResponse(compact('company', 'invoices'));
    }

    public function create($companyId, $clientId)
    {
        $company = $this->getCompanyForOwner($companyId, 'create invoices');
        $client = $company->clients()->findOrFail($clientId);

        return $this->sendResponse(compact('company', 'client'));
    }

    public function store(StoreInvoiceRequest $request, $companyId)
    {
        $clientId = $request->input('client_id');
        $data = array_merge($request->validated(), [
            'company_id' => $companyId,
            'client_id' => $clientId,
        ]);

        $invoice = $this->invoiceService->createInvoice($data);
        $invoice->load('client');

        $message = 'Invoice created' . ($request->has('send_email') && $request->send_email ? ' and sent to client' : '');
        return $this->sendCreated($invoice, $message);
    }

    public function showReceivePayment($companyId, $invoiceId)
    {
        $company = $this->getCompanyForOwner($companyId, 'receive payments');
        $invoice = $company->invoices()->findOrFail($invoiceId);
        $accounts = $company->accounts()->where('is_active', true)->get();
        $categories = $company->categories;

        return $this->sendResponse(compact('company', 'invoice', 'accounts', 'categories'));
    }

    public function receivePayment(ReceivePaymentRequest $request, $companyId, $invoiceId)
    {
        $company = $this->getCompanyForMember($companyId);
        $invoice = $company->invoices()->findOrFail($invoiceId);
        $result = $this->invoiceService->receivePayment($invoice, $request->validated());
        return $this->sendResponse($result, 'Payment received successfully');
    }

    public function show($companyId, $invoiceId)
    {
        $company = $this->getCompanyForMember($companyId);
        $invoice = $company->invoices()->with('client', 'items', 'creator')->findOrFail($invoiceId);
        
        return $this->sendResponse(compact('company', 'invoice'));
    }

    public function edit($companyId, $invoiceId)
    {
        $company = $this->getCompanyForOwner($companyId, 'edit invoices');
        $invoice = $company->invoices()->with('client', 'items', 'creator')->findOrFail($invoiceId);
        
        return $this->sendResponse(compact('company', 'invoice'));
    }

    public function update(UpdateInvoiceRequest $request, $companyId, $invoiceId)
    {
        $company = $this->getCompanyForMember($companyId);
        $invoice = $company->invoices()->findOrFail($invoiceId);
        $this->invoiceService->updateInvoice($invoice, $request->validated());
        return $this->sendResponse($invoice->fresh(), 'Invoice updated successfully');
    }

    public function destroy($companyId, $invoiceId)
    {
        $company = $this->getCompanyForOwner($companyId, 'delete invoices');
        $invoice = $company->invoices()->findOrFail($invoiceId);
        
        if (!$this->invoiceService->deleteInvoice($invoice)) {
            return $this->sendError('Cannot delete invoice with payments');
        }

        return $this->sendResponse([], 'Invoice deleted successfully');
    }

    public function downloadPdf($companyId, $invoiceId)
    {
        $company = $this->getCompanyForMember($companyId);
        $invoice = $company->invoices()->with('client')->findOrFail($invoiceId);
        
        $pdf = \PDF::loadView('invoices.pdf', compact('company', 'invoice'));
        return $pdf->download('invoice-' . $invoice->id . '.pdf');
    }
}

