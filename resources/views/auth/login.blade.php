@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div style="max-width: 400px; margin: 50px auto; padding: 0 20px;">
    
    {{-- Logo --}}
    <div style="text-align: center; margin-bottom: 30px;">
        <h2 style="color: #13B5EC; font-size: 28px; margin: 0;">YourApp</h2>
    </div>

    {{-- Card --}}
    <div style="background: white; border-radius: 8px; padding: 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <h1 style="font-size: 24px; margin: 0 0 8px 0; color: #1a2c3e;">Sign in</h1>
        <p style="color: #6b7a8a; margin-bottom: 30px;">Welcome back!</p>

        {{-- Error Message --}}
        @if ($errors->any())
            <div style="background: #fee; border: 1px solid #fcc; border-radius: 6px; padding: 12px; margin-bottom: 20px; color: #c33;">
                Invalid email or password
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="/login">
            @csrf

            {{-- Email --}}
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; color: #1a2c3e; font-weight: 500;">
                    Email
                </label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 16px; box-sizing: border-box;"
                    required
                >
                @error('email')
                    <span style="color: #c33; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            {{-- Password --}}
            <div style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <label style="color: #1a2c3e; font-weight: 500;">Password</label>
                    <a href="/forgot-password" style="color: #13B5EC; text-decoration: none; font-size: 14px;">Forgot?</a>
                </div>
                <input 
                    type="password" 
                    name="password" 
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 16px; box-sizing: border-box;"
                    required
                >
            </div>

            {{-- Remember Me --}}
            <div style="margin-bottom: 25px;">
                <label style="color: #6b7a8a; font-size: 14px;">
                    <input type="checkbox" name="remember" style="margin-right: 8px;"> 
                    Remember me
                </label>
            </div>

            {{-- Login Button --}}
            <button 
                type="submit" 
                style="width: 100%; background: #13B5EC; color: white; border: none; padding: 12px; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer;"
            >
                Sign in
            </button>
        </form>

        {{-- Register Link --}}
        <p style="text-align: center; margin-top: 30px; color: #6b7a8a;">
            Don't have an account? 
            <a href="/register" style="color: #13B5EC; text-decoration: none;">Sign up</a>
        </p>
    </div>
</div>
@endsection