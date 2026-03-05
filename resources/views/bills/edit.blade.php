@extends('layouts.app')

@section('title', 'Edit Bill')

@section('content')
    <h1>Edit Bill #{{ $bill->id }}</h1>
    
    <p><strong>Supplier:</strong> {{ $bill->supplier->name }}</p>
    
    <form method="POST" action="/companies/{{ $company->id }}/bills/{{ $bill->id }}">
        @csrf
        @method('PUT')
        <div>
            <label>Total Amount:</label>
            <input type="number" step="0.01" name="total_amount" value="{{ $bill->total_amount }}" required>
            @error('total_amount')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Status:</label>
            <select name="status" required>
                <option value="draft" {{ $bill->status === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="sent" {{ $bill->status === 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="paid" {{ $bill->status === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="cancelled" {{ $bill->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            @error('status')<span>{{ $message }}</span>@enderror
        </div>
        <button type="submit">Update Bill</button>
    </form>
    
    <a href="/companies/{{ $company->id }}/bills/{{ $bill->id }}" style="display: inline-block; margin-top: 1rem;">
        <button type="button">Cancel</button>
    </a>
@endsection
