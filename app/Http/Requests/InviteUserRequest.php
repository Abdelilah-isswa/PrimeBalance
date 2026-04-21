<?php

namespace App\Http\Requests;

use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class InviteUserRequest extends FormRequest
{
    use HasCompanyAuthorization;
    
    public function authorize(): bool
    {
        return $this->isCompanyRole((int) $this->route('id'), ['owner', 'admin']);
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'role' => 'required|in:admin,accountant,viewer',
        ];
    }
}
