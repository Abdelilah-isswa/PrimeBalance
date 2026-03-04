@extends('layouts.app')

@section('title', 'Create Company')

@section('content')
    <h1>Create Company</h1>
    <form method="POST" action="/companies">
        @csrf
        <div>
            <label>Name:</label>
            <input type="text" name="name" required>
            @error('name')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Address:</label>
            <input type="text" name="address" required>
            @error('address')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Currency:</label>
            <select name="currency" required>
                <option value="">Select Currency</option>
                <option value="USD">USD - US Dollar</option>
                <option value="EUR">EUR - Euro</option>
                <option value="GBP">GBP - British Pound</option>
                <option value="MAD">MAD - Moroccan Dirham</option>
                <option value="JPY">JPY - Japanese Yen</option>
                <option value="CNY">CNY - Chinese Yuan</option>
                <option value="CAD">CAD - Canadian Dollar</option>
                <option value="AUD">AUD - Australian Dollar</option>
            </select>
            @error('currency')<span>{{ $message }}</span>@enderror
        </div>
        <button type="submit">Create Company</button>
    </form>
@endsection
