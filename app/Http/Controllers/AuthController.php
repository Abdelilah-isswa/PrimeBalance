<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\BaseController;

class AuthController extends BaseController
{
    protected $authService;
    
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    
    public function showLogin()
    {
        return $this->sendResponse([]);
    }
    
    public function login(LoginRequest $request)
    {
        if ($this->authService->attemptLogin($request->validated())) {
            $token = $request->user()->createToken('api-token')->plainTextToken;
            
            $redirectUrl = $this->authService->handlePostLoginInvitation();
            if ($redirectUrl) {
                return $this->sendResponse(['token' => $token, 'redirect' => $redirectUrl], 'Logged in and joined company');
            }
            
            return $this->sendResponse(['token' => $token], 'Logged in successfully');
        }

        return $this->sendError('Invalid credentials', 401);
    }
    
    public function showRegister()
    {
        return $this->sendResponse([]);
    }
    
    public function register(RegisterRequest $request)
    {
        $user = $this->authService->createUser($request->validated());
        $token = $user->createToken('api-token')->plainTextToken;
        
        $redirectUrl = $this->authService->handlePostLoginInvitation();
        if ($redirectUrl) {
            return $this->sendResponse(['token' => $token, 'redirect' => $redirectUrl], 'Registered and joined company');
        }
        
        return $this->sendResponse(['token' => $token], 'Registered successfully');
    }

    public function logout(Request $request)
    {
        $this->authService->logout();
        $request->user()->currentAccessToken()->delete();
        return $this->sendResponse([], 'Logged out successfully');
    }
}

