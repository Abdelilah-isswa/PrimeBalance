@extends('layouts.app')

@section('title', $company->name)

@section('content')
    <h1>{{ $company->name }}</h1>
    
    <div style="margin: 2rem 0;">
        <p><strong>Address:</strong> {{ $company->address }}</p>
        <p><strong>Currency:</strong> {{ $company->currency }}</p>
        <p><strong>Start Date:</strong> {{ $company->start_date }}</p>
        <p><strong>Your Role:</strong> {{ $company->pivot->role }}</p>
    </div>

    <h2>Categories</h2>
    @if($company->pivot->role === 'owner')
        <form method="POST" action="/companies/{{ $company->id }}/categories" style="margin-bottom: 1rem;">
            @csrf
            <div style="display: flex; gap: 0.5rem;">
                <input type="text" name="name" placeholder="Category name" required style="flex: 1;">
                <button type="submit">Add Category</button>
            </div>
            @error('name')<span>{{ $message }}</span>@enderror
        </form>
    @endif

    @if($company->categories->isEmpty())
        <p>No categories yet.</p>
    @else
        <ul>
            @foreach($company->categories as $category)
                <li>{{ $category->name }}</li>
            @endforeach
        </ul>
    @endif

    <h2>Clients</h2>
    @if($company->pivot->role === 'owner')
        <a href="/companies/{{ $company->id }}/clients/create" style="display: inline-block; margin-bottom: 1rem;">
            <button type="button">Add Client</button>
        </a>
    @endif

    @if($company->clients->isEmpty())
        <p>No clients yet.</p>
    @else
        <ul>
            @foreach($company->clients as $client)
                <li>{{ $client->name }} - {{ $client->email }} - {{ $client->phone }}</li>
            @endforeach
        </ul>
    @endif
@endsection
