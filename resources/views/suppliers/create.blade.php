@extends('layouts.app')

@section('title', 'Create Supplier')

@section('content')
    <h1>Create Supplier for {{ $company->name }}</h1>
    <form method="POST" action="/companies/{{ $company->id }}/suppliers">
        @csrf
        <div>
            <label>Name:</label>
            <input type="text" name="name" required>
            @error('name')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
            @error('email')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Address:</label>
            <input type="text" name="address" required>
            @error('address')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Phone:</label>
            <input type="text" name="phone" required>
            @error('phone')<span>{{ $message }}</span>@enderror
        </div>
        <button type="submit">Create Supplier</button>
    </form>
@endsection
