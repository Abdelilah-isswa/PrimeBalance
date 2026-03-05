@extends('layouts.app')

@section('title', 'Create Transaction')

@section('content')
    <h1>Create Transaction - {{ $company->name }}</h1>
    
    <form method="POST" action="/companies/{{ $company->id }}/transactions">
        @csrf
        <div>
            <label>Type:</label>
            <select name="type" required>
                <option value="">Select Type</option>
                <option value="income">Income</option>
                <option value="expense">Expense</option>
            </select>
            @error('type')<span>{{ $message }}</span>@enderror
        </div>
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
            <label>Amount:</label>
            <input type="number" step="0.01" name="amount" required>
            @error('amount')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Description:</label>
            <input type="text" name="description" required>
            @error('description')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Date:</label>
            <input type="date" name="date" value="{{ date('Y-m-d') }}" required>
            @error('date')<span>{{ $message }}</span>@enderror
        </div>
        <button type="submit">Create Transaction</button>
    </form>
@endsection
