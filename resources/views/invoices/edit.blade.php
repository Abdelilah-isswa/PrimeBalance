@extends('layouts.app')

@section('title', 'Edit Invoice')

@section('content')
    <h1>Edit Invoice #{{ $invoice->id }}</h1>
    
    <p><strong>Client:</strong> {{ $invoice->client->name }}</p>
    
    <form method="POST" action="/companies/{{ $company->id }}/invoices/{{ $invoice->id }}">
        @csrf
        @method('PUT')
        <div>
            <label>Total Amount:</label>
            <input type="number" step="0.01" name="total_amount" value="{{ $invoice->total_amount }}" required>
            @error('total_amount')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Status:</label>
            <select name="status" required>
                <option value="draft" {{ $invoice->status === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="sent" {{ $invoice->status === 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="paid" {{ $invoice->status === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="cancelled" {{ $invoice->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            @error('status')<span>{{ $message }}</span>@enderror
        </div>
        <button type="submit">Update Invoice</button>
    </form>
    
    <a href="/companies/{{ $company->id }}/invoices/{{ $invoice->id }}" style="display: inline-block; margin-top: 1rem;">
        <button type="button">Cancel</button>
    </a>
@endsection
