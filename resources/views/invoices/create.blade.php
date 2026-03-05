@extends('layouts.app')

@section('title', 'Create Invoice')

@section('content')
    <h1>Create Invoice for {{ $client->name }}</h1>
    <form method="POST" action="/companies/{{ $company->id }}/clients/{{ $client->id }}/invoices">
        @csrf
        <div>
            <label>Total Amount:</label>
            <input type="number" step="0.01" name="total_amount" required>
            @error('total_amount')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Status:</label>
            <select name="status" required>
                <option value="draft">Draft</option>
                <option value="sent">Sent</option>
                <option value="paid">Paid</option>
                <option value="cancelled">Cancelled</option>
            </select>
            @error('status')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>
                <input type="checkbox" name="send_email" value="1">
                Send invoice by email to {{ $client->email }}
            </label>
        </div>
        <button type="submit">Create Invoice</button>
    </form>
@endsection
