<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InviteUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $company = $this->user()->companies()->find($this->route('id'));
        return $company && $company->pivot->role === 'owner';
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'role' => 'required|in:owner,accountant,standard_user,viewer',
        ];
    }
}