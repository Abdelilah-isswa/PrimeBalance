@extends('layouts.app')

@section('title', 'Edit Account')

@section('content')
    <h1>Edit Account</h1>
    
    <form method="POST" action="/companies/{{ $company->id }}/accounts/{{ $account->id }}">
        @csrf
        @method('PUT')
        <div>
            <label>Name:</label>
            <input type="text" name="name" value="{{ $account->name }}" required>
            @error('name')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Balance:</label>
            <input type="number" step="0.01" value="{{ $account->balance }}" disabled>
            <small>Balance cannot be edited directly</small>
        </div>
        <div>
            <label>
                <input type="checkbox" name="is_active" {{ $account->is_active ? 'checked' : '' }}>
                Active
            </label>
        </div>
        <button type="submit">Update</button>
        <a href="/companies/{{ $company->id }}/accounts">
            <button type="button">Cancel</button>
        </a>
    </form>
@endsection
