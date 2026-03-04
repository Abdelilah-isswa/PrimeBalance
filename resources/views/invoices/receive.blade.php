@extends('layouts.app')

@section('title', 'Receive Payment')

@section('content')
    <h1>Receive Payment for Invoice #{{ $invoice->id }}</h1>
    
    <div style="margin: 1rem 0; padding: 1rem; background: #f5f5f5; border-radius: 4px;">
        <p><strong>Client:</strong> {{ $invoice->client->name }}</p>
        <p><strong>Amount:</strong> {{ $company->currency }} {{ $invoice->total_amount }}</p>
    </div>

    <form method="POST" action="/companies/{{ $company->id }}/invoices/{{ $invoice->id }}/receive">
        @csrf
        <div>
            <label>Account:</label>
            <select name="account_id" required>
                <option value="">Select Account</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }} (Balance: {{ $account->balance }})</option>
                @endforeach
            </select>
            @error('account_id')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Category (Optional):</label>
            <select name="category_id">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Payment Date:</label>
            <input type="date" name="date" value="{{ date('Y-m-d') }}" required>
            @error('date')<span>{{ $message }}</span>@enderror
        </div>
        <button type="submit">Receive Payment</button>
    </form>
@endsection
