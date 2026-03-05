@extends('layouts.app')

@section('title', 'Manage Company')

@section('content')
    <h1>Manage Company</h1>
    
    <form method="POST" action="/companies/{{ $company->id }}">
        @csrf
        @method('PUT')
        <div>
            <label>Name:</label>
            <input type="text" name="name" value="{{ $company->name }}" required>
            @error('name')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Address:</label>
            <input type="text" name="address" value="{{ $company->address }}" required>
            @error('address')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Currency:</label>
            <select name="currency" required>
                <option value="USD" {{ $company->currency === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                <option value="EUR" {{ $company->currency === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                <option value="GBP" {{ $company->currency === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                <option value="MAD" {{ $company->currency === 'MAD' ? 'selected' : '' }}>MAD - Moroccan Dirham</option>
                <option value="JPY" {{ $company->currency === 'JPY' ? 'selected' : '' }}>JPY - Japanese Yen</option>
                <option value="CNY" {{ $company->currency === 'CNY' ? 'selected' : '' }}>CNY - Chinese Yuan</option>
                <option value="CAD" {{ $company->currency === 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                <option value="AUD" {{ $company->currency === 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
            </select>
            @error('currency')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Start Date:</label>
            <input type="text" value="{{ $company->start_date }}" disabled>
            <small style="display: block; color: #666;">Start date cannot be changed</small>
        </div>
        <button type="submit">Update Company</button>
    </form>
    
    <a href="/companies/{{ $company->id }}" style="display: inline-block; margin-top: 1rem;">
        <button type="button">Back to Company</button>
    </a>
@endsection
