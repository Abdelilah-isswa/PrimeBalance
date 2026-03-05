<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            
            // Check if there's a pending invitation
            if (session('invitation_token')) {
                $token = session('invitation_token');
                session()->forget('invitation_token');
                
                $invitation = \App\Models\Invitation::where('token', $token)
                    ->where('status', 'pending')
                    ->where('email', Auth::user()->email)
                    ->first();
                
                if ($invitation && !$invitation->isExpired()) {
                    if (!$invitation->company->users()->where('user_id', Auth::id())->exists()) {
                        $invitation->company->users()->attach(Auth::id(), ['role' => $invitation->role]);
                    }
                    $invitation->update(['status' => 'accepted']);
                    return redirect('/companies/' . $invitation->company_id)->with('success', 'You have joined ' . $invitation->company->name);
                }
            }
            
            return redirect()->intended('/');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        
        // Check if there's a pending invitation
        if (session('invitation_token')) {
            $token = session('invitation_token');
            session()->forget('invitation_token');
            
            $invitation = \App\Models\Invitation::where('token', $token)
                ->where('status', 'pending')
                ->where('email', $user->email)
                ->first();
            
            if ($invitation && !$invitation->isExpired()) {
                $invitation->company->users()->attach($user->id, ['role' => $invitation->role]);
                $invitation->update(['status' => 'accepted']);
                return redirect('/companies/' . $invitation->company_id)->with('success', 'You have joined ' . $invitation->company->name);
            }
        }
        
        return redirect('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
