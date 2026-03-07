@extends('layouts.app')

@section('title', 'Create Bill')

@section('content')
    <h1>Create Bill for {{ $supplier->name }}</h1>
    <form method="POST" action="/companies/{{ $company->id }}/suppliers/{{ $supplier->id }}/bills">
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
                <option value="unpaid">Unpaid</option>
                <option value="paid">Paid</option>
                <option value="overdue">Overdue</option>
                <option value="cancelled">Cancelled</option>
            </select>
            @error('status')<span>{{ $message }}</span>@enderror
        </div>
        <button type="submit">Create Bill</button>
    </form>
@endsection
