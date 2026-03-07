@extends('layouts.app')

@section('title', 'Edit Client')

@section('content')
    <h1>Edit Client</h1>
    
    <form method="POST" action="/companies/{{ $company->id }}/clients/{{ $client->id }}">
        @csrf
        @method('PUT')
        <div>
            <label>Name:</label>
            <input type="text" name="name" value="{{ $client->name }}" required>
            @error('name')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Email:</label>
            <input type="email" name="email" value="{{ $client->email }}" required>
            @error('email')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Address:</label>
            <input type="text" name="address" value="{{ $client->address }}" required>
            @error('address')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Phone:</label>
            <input type="text" name="phone" value="{{ $client->phone }}" required>
            @error('phone')<span>{{ $message }}</span>@enderror
        </div>
        <button type="submit">Update</button>
        <a href="/companies/{{ $company->id }}">
            <button type="button">Cancel</button>
        </a>
    </form>

    @if(!$client->invoices()->exists())
    <form method="POST" action="/companies/{{ $company->id }}/clients/{{ $client->id }}" style="margin-top: 2rem;" onsubmit="return confirm('Delete this client?')">
        @csrf
        @method('DELETE')
        <button type="submit" style="background: #c62828;">Delete Client</button>
    </form>
    @endif
@endsection
