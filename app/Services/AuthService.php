<?php

namespace App\Services;

use App\Models\User;
use App\Models\Invitation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function attemptLogin(array $credentials): bool
    {
        return Auth::attempt($credentials, true);
    }

    public function handlePostLoginInvitation(): ?string
    {
        if (!session('invitation_token')) {
            return null;
        }

        $token = session('invitation_token');
        session()->forget('invitation_token');
        
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->where('email', Auth::user()->email)
            ->first();
        
        if ($invitation && !$invitation->isExpired()) {
            if (!$invitation->company->users()->where('user_id', Auth::id())->exists()) {
                $invitation->company->users()->attach(Auth::id(), ['role' => $invitation->role]);
            }
            $invitation->update(['status' => 'accepted']);
            return '/companies/' . $invitation->company_id;
        }

        return null;
    }

    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function loginUser(User $user): void
    {
        Auth::login($user);
    }

    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}