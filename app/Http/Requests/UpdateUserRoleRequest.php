<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $company = $this->user()->companies()->find($this->route('companyId'));
        return $company && $company->pivot->role === 'owner';
    }

    public function rules(): array
    {
        return [
            'role' => 'required|in:owner,accountant,standard_user,viewer',
        ];
    }
}