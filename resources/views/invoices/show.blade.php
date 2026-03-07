@extends('layouts.app')

@section('title', 'Invoice Details')

@section('content')
    <h1>Invoice #{{ $invoice->id }}</h1>
    
    <div style="margin: 2rem 0; padding: 1rem; background: #f5f5f5; border-radius: 4px;">
        <p><strong>Client:</strong> {{ $invoice->client->name }}</p>
        <p><strong>Email:</strong> {{ $invoice->client->email }}</p>
        <p><strong>Amount:</strong> {{ $company->currency }} {{ number_format($invoice->total_amount, 2) }}</p>
        <p><strong>Status:</strong> {{ ucfirst($invoice->status) }}</p>
        <p><strong>Created:</strong> {{ $invoice->created_at->format('F d, Y') }}</p>
    </div>

    @if($company->pivot->role === 'owner')
        <div style="margin: 1rem 0;">
            <a href="/companies/{{ $company->id }}/invoices/{{ $invoice->id }}/pdf" target="_blank">
                <button type="button" style="background: #1565c0;">Download PDF</button>
            </a>
            <a href="/companies/{{ $company->id }}/invoices/{{ $invoice->id }}/edit">
                <button type="button">Edit Invoice</button>
            </a>
            <form method="POST" action="/companies/{{ $company->id }}/invoices/{{ $invoice->id }}" style="display: inline; margin-left: 0.5rem;" onsubmit="return confirm('Are you sure you want to delete this invoice?');">
                @csrf
                @method('DELETE')
                <button type="submit" style="background: #c62828;">Delete Invoice</button>
            </form>
        </div>
    @endif
    
    <a href="/companies/{{ $company->id }}/invoices" style="display: inline-block; margin-top: 1rem;">
        <button type="button">Back to Invoices</button>
    </a>
@endsection
