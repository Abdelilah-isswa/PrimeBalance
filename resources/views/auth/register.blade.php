@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <h1>Register</h1>
    <form method="POST" action="/register">
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
            <label>Password:</label>
            <input type="password" name="password" required>
            @error('password')<span>{{ $message }}</span>@enderror
        </div>
        <div>
            <label>Confirm Password:</label>
            <input type="password" name="password_confirmation" required>
        </div>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="/login">Login</a></p>
@endsection
