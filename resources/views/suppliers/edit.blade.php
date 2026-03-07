@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
    <h1>Edit Supplier</h1>
    
    <form method="POST" action="/companies/{{ $company->id }}/suppliers/{{ $supplier->id }}">
        @csrf
        @method('PUT')
        <div>
            <label>Name:</label>
            <input type="text" name="name" value="{{ $supplier->name }}" required>
            @error('name')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Email:</label>
            <input type="email" name="email" value="{{ $supplier->email }}" required>
            @error('email')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Address:</label>
            <input type="text" name="address" value="{{ $supplier->address }}" required>
            @error('address')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Phone:</label>
            <input type="text" name="phone" value="{{ $supplier->phone }}" required>
            @error('phone')<span>{{ $message }}</span>@enderror
        </div>
        <button type="submit">Update</button>
        <a href="/companies/{{ $company->id }}">
            <button type="button">Cancel</button>
        </a>
    </form>

    @if(!$supplier->bills()->exists())
    <form method="POST" action="/companies/{{ $company->id }}/suppliers/{{ $supplier->id }}" style="margin-top: 2rem;" onsubmit="return confirm('Delete this supplier?')">
        @csrf
        @method('DELETE')
        <button type="submit" style="background: #c62828;">Delete Supplier</button>
    </form>
    @endif
@endsection
