@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <h1>Welcome, {{ Auth::user()->name }}</h1>
    <p>Email: {{ Auth::user()->email }}</p>
    <p>Role: {{ Auth::user()->role }}</p>
@endsection
