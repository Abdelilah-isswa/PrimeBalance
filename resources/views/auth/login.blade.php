@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <h1>Login</h1>
    <form method="POST" action="/login">
        @csrf
        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
            @error('email')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="/register">Register</a></p>
@endsection
