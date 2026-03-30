<?php

namespace App\Http\Requests;

use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class InviteUserRequest extends FormRequest
{
    use HasCompanyAuthorization;
    
    public function authorize(): bool
    {
        return $this->isCompanyOwner($this->route('id'));
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'role' => 'required|in:owner,accountant,standard_user,viewer',
        ];
    }
}