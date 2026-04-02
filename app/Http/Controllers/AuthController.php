<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;
    
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    
    public function showLogin()
    {
        return view('auth.login');
    }
    
    public function login(LoginRequest $request)
    {
        if ($this->authService->attemptLogin($request->validated())) {
            $request->session()->regenerate();
            
            $redirectUrl = $this->authService->handlePostLoginInvitation();
            if ($redirectUrl) {
                return redirect($redirectUrl)->with('success', 'You have joined the company');
            }
            
            return redirect()->intended(route('home'));
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }
    
    public function showRegister()
    {
        return view('auth.register');
    }
    
    public function register(RegisterRequest $request)
    {
        $user = $this->authService->createUser($request->validated());
        $this->authService->loginUser($user);
        
        $redirectUrl = $this->authService->handlePostLoginInvitation();
        if ($redirectUrl) {
            return redirect($redirectUrl)->with('success', 'You have joined the company');
        }
        
        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        $this->authService->logout();
        return redirect()->route('login');
    }
}
