<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index($companyId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        $invoices = $company->invoices()->with('client')->get();
        
        return view('invoices.index', compact('company', 'invoices'));
    }

    public function create($companyId, $clientId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can create invoices');
        }

        $client = $company->clients()->findOrFail($clientId);

        return view('invoices.create', compact('company', 'client'));
    }

    public function store(Request $request, $companyId, $clientId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can create invoices');
        }

        $request->validate([
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,sent,paid,cancelled',
            'send_email' => 'boolean',
        ]);

        $invoice = Invoice::create([
            'company_id' => $companyId,
            'client_id' => $clientId,
            'total_amount' => $request->total_amount,
            'status' => $request->status,
        ]);

        if ($request->has('send_email') && $request->send_email) {
            \Mail::to($invoice->client->email)->send(new \App\Mail\InvoiceMail($invoice));
        }

        return redirect("/companies/{$companyId}")->with('success', 'Invoice created' . ($request->has('send_email') && $request->send_email ? ' and sent to client' : ''));
    }

    public function showReceivePayment($companyId, $invoiceId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can receive payments');
        }

        $invoice = $company->invoices()->findOrFail($invoiceId);
        $accounts = $company->accounts()->where('is_active', true)->get();
        $categories = $company->categories;

        return view('invoices.receive', compact('company', 'invoice', 'accounts', 'categories'));
    }

    public function receivePayment(Request $request, $companyId, $invoiceId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can receive payments');
        }

        $invoice = $company->invoices()->findOrFail($invoiceId);

        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'nullable|exists:categories,id',
            'date' => 'required|date',
        ]);

        $account = \App\Models\Account::findOrFail($request->account_id);
        
        \App\Models\Transaction::create([
            'company_id' => $companyId,
            'account_id' => $request->account_id,
            'category_id' => $request->category_id,
            'type' => 'income',
            'amount' => $invoice->total_amount,
            'description' => 'Payment received for invoice #' . $invoice->id . ' - ' . $invoice->client->name,
            'date' => $request->date,
            'invoice_id' => $invoice->id,
        ]);

        $account->increment('balance', $invoice->total_amount);
        $invoice->update(['status' => 'paid']);

        return redirect("/companies/{$companyId}/invoices")->with('success', 'Payment received successfully');
    }

    public function show($companyId, $invoiceId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        $invoice = $company->invoices()->with('client')->findOrFail($invoiceId);
        
        return view('invoices.show', compact('company', 'invoice'));
    }

    public function edit($companyId, $invoiceId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can edit invoices');
        }

        $invoice = $company->invoices()->with('client')->findOrFail($invoiceId);
        
        return view('invoices.edit', compact('company', 'invoice'));
    }

    public function update(Request $request, $companyId, $invoiceId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can update invoices');
        }

        $invoice = $company->invoices()->findOrFail($invoiceId);

        $request->validate([
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,sent,paid,cancelled',
        ]);

        $invoice->update([
            'total_amount' => $request->total_amount,
            'status' => $request->status,
        ]);

        return redirect("/companies/{$companyId}/invoices/{$invoiceId}")->with('success', 'Invoice updated successfully');
    }

    public function destroy($companyId, $invoiceId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can delete invoices');
        }

        $invoice = $company->invoices()->findOrFail($invoiceId);
        $invoice->delete();

        return redirect("/companies/{$companyId}/invoices")->with('success', 'Invoice deleted successfully');
    }

    public function downloadPdf($companyId, $invoiceId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        $invoice = $company->invoices()->with('client')->findOrFail($invoiceId);
        
        $pdf = \PDF::loadView('invoices.pdf', compact('company', 'invoice'));
        return $pdf->download('invoice-' . $invoice->id . '.pdf');
    }
}
