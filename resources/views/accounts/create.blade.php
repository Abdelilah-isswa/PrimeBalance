@extends('layouts.app')

@section('title', 'Create Account')

@section('content')
    <h1>Create Account for {{ $company->name }}</h1>
    <form method="POST" action="/companies/{{ $company->id }}/accounts">
        @csrf
        <div>
            <label>Name:</label>
            <input type="text" name="name" required>
            @error('name')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Balance:</label>
            <input type="number" step="0.01" name="balance" required>
            @error('balance')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>
                <input type="checkbox" name="is_active" checked>
                Active
            </label>
        </div>
        <button type="submit">Create Account</button>
    </form>
@endsection
