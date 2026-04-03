<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;

class AuthController extends BaseController
{
    protected $authService;
    
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    
    public function login(LoginRequest $request)
    {
        if ($this->authService->attemptLogin($request->validated())) {
            $token = $request->user()->createToken('api-token')->plainTextToken;
            
            return $this->sendResponse(['token' => $token], 'Logged in successfully');
        }

        return $this->sendError('Invalid credentials', 401);
    }
    
    public function register(RegisterRequest $request)
    {
        $user = $this->authService->createUser($request->validated());
        $token = $user->createToken('api-token')->plainTextToken;
        
        return $this->sendResponse(['token' => $token], 'Registered successfully');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->sendResponse([], 'Logged out successfully');
    }
}
