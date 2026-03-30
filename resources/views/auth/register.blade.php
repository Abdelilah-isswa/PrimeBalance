@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div style="max-width: 400px; margin: 50px auto; padding: 0 20px;">
    
    {{-- Logo --}}
    <div style="text-align: center; margin-bottom: 30px;">
        <h2 style="color: #13B5EC; font-size: 28px; margin: 0;">YourApp</h2>
    </div>

    {{-- Card --}}
    <div style="background: white; border-radius: 8px; padding: 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <h1 style="font-size: 24px; margin: 0 0 8px 0; color: #1a2c3e;">Create account</h1>
        <p style="color: #6b7a8a; margin-bottom: 30px;">Join us to get started</p>

        {{-- Error Messages --}}
        @if ($errors->any())
            <div style="background: #fee; border: 1px solid #fcc; border-radius: 6px; padding: 12px; margin-bottom: 20px;">
                @foreach ($errors->all() as $error)
                    <div style="color: #c33; font-size: 14px; margin-bottom: 5px;">{{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="/register">
            @csrf

            {{-- Name --}}
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; color: #1a2c3e; font-weight: 500;">
                    Full name
                </label>
                <input 
                    type="text" 
                    name="name" 
                    value="{{ old('name') }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 16px; box-sizing: border-box;"
                    required
                >
                @error('name')
                    <span style="color: #c33; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            {{-- Email --}}
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; color: #1a2c3e; font-weight: 500;">
                    Email address
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
                <label style="display: block; margin-bottom: 8px; color: #1a2c3e; font-weight: 500;">
                    Password
                </label>
                <input 
                    type="password" 
                    name="password" 
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 16px; box-sizing: border-box;"
                    required
                >
                @error('password')
                    <span style="color: #c33; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; color: #1a2c3e; font-weight: 500;">
                    Confirm password
                </label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 16px; box-sizing: border-box;"
                    required
                >
            </div>

            {{-- Register Button --}}
            <button 
                type="submit" 
                style="width: 100%; background: #13B5EC; color: white; border: none; padding: 12px; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer;"
            >
                Create account
            </button>
        </form>

        {{-- Login Link --}}
        <p style="text-align: center; margin-top: 30px; color: #6b7a8a;">
            Already have an account? 
            <a href="/login" style="color: #13B5EC; text-decoration: none;">Sign in</a>
        </p>
    </div>
</div>
@endsection