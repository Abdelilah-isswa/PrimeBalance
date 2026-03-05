@extends('layouts.app')

@section('title', 'Bill Details')

@section('content')
    <h1>Bill #{{ $bill->id }}</h1>
    
    <div style="margin: 2rem 0; padding: 1rem; background: #f5f5f5; border-radius: 4px;">
        <p><strong>Supplier:</strong> {{ $bill->supplier->name }}</p>
        <p><strong>Email:</strong> {{ $bill->supplier->email }}</p>
        <p><strong>Amount:</strong> {{ $company->currency }} {{ number_format($bill->total_amount, 2) }}</p>
        <p><strong>Status:</strong> {{ ucfirst($bill->status) }}</p>
        <p><strong>Created:</strong> {{ $bill->created_at->format('F d, Y') }}</p>
    </div>

    @if($company->pivot->role === 'owner')
        <div style="margin: 1rem 0;">
            <a href="/companies/{{ $company->id }}/bills/{{ $bill->id }}/edit">
                <button type="button">Edit Bill</button>
            </a>
            <form method="POST" action="/companies/{{ $company->id }}/bills/{{ $bill->id }}" style="display: inline; margin-left: 0.5rem;" onsubmit="return confirm('Are you sure you want to delete this bill?');">
                @csrf
                @method('DELETE')
                <button type="submit" style="background: #c62828;">Delete Bill</button>
            </form>
        </div>
    @endif
    
    <a href="/companies/{{ $company->id }}/bills" style="display: inline-block; margin-top: 1rem;">
        <button type="button">Back to Bills</button>
    </a>
@endsection
