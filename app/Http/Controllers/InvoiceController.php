<?php

namespace App\Http\Controllers;

use App\Services\InvoiceService;
use App\Models\Invoice;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Requests\ReceivePaymentRequest;
use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    use HasCompanyAuthorization;
    
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function index($companyId)
    {
        $company = $this->getAuthorizedCompany($companyId);
        $invoices = $company->invoices()->with('client')->get();
        
        return view('invoices.index', compact('company', 'invoices'));
    }

    public function create($companyId, $clientId)
    {
        $company = $this->getCompanyAsOwner($companyId, 'create invoices');
        $client = $company->clients()->findOrFail($clientId);

        return view('invoices.create', compact('company', 'client'));
    }

    public function store(StoreInvoiceRequest $request, $companyId, $clientId)
    {
        $data = array_merge($request->validated(), [
            'company_id' => $companyId,
            'client_id' => $clientId,
        ]);

        $invoice = $this->invoiceService->createInvoice($data);

        $message = 'Invoice created' . ($request->has('send_email') && $request->send_email ? ' and sent to client' : '');
        return redirect("/companies/{$companyId}")->with('success', $message);
    }

    public function showReceivePayment($companyId, $invoiceId)
    {
        $company = $this->getCompanyAsOwner($companyId, 'receive payments');
        $invoice = $company->invoices()->findOrFail($invoiceId);
        $accounts = $company->accounts()->where('is_active', true)->get();
        $categories = $company->categories;

        return view('invoices.receive', compact('company', 'invoice', 'accounts', 'categories'));
    }

    public function receivePayment(ReceivePaymentRequest $request, $companyId, $invoiceId)
    {
        $company = $this->getAuthorizedCompany($companyId);
        $invoice = $company->invoices()->findOrFail($invoiceId);
        $this->invoiceService->receivePayment($invoice, $request->validated());
        return redirect("/companies/{$companyId}/invoices")->with('success', 'Payment received successfully');
    }

    public function show($companyId, $invoiceId)
    {
        $company = $this->getAuthorizedCompany($companyId);
        $invoice = $company->invoices()->with('client')->findOrFail($invoiceId);
        
        return view('invoices.show', compact('company', 'invoice'));
    }

    public function edit($companyId, $invoiceId)
    {
        $company = $this->getCompanyAsOwner($companyId, 'edit invoices');
        $invoice = $company->invoices()->with('client')->findOrFail($invoiceId);
        
        return view('invoices.edit', compact('company', 'invoice'));
    }

    public function update(UpdateInvoiceRequest $request, $companyId, $invoiceId)
    {
        $company = $this->getAuthorizedCompany($companyId);
        $invoice = $company->invoices()->findOrFail($invoiceId);
        $this->invoiceService->updateInvoice($invoice, $request->validated());
        return redirect("/companies/{$companyId}/invoices/{$invoiceId}")->with('success', 'Invoice updated successfully');
    }

    public function destroy($companyId, $invoiceId)
    {
        $company = $this->getCompanyAsOwner($companyId, 'delete invoices');
        $invoice = $company->invoices()->findOrFail($invoiceId);
        
        if (!$this->invoiceService->deleteInvoice($invoice)) {
            return back()->with('error', 'Cannot delete invoice with payments');
        }

        return redirect("/companies/{$companyId}/invoices")->with('success', 'Invoice deleted successfully');
    }

    public function downloadPdf($companyId, $invoiceId)
    {
        $company = $this->getAuthorizedCompany($companyId);
        $invoice = $company->invoices()->with('client')->findOrFail($invoiceId);
        
        $pdf = \PDF::loadView('invoices.pdf', compact('company', 'invoice'));
        return $pdf->download('invoice-' . $invoice->id . '.pdf');
    }
}
