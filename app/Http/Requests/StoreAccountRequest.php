<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        $company = $this->user()->companies()->find($this->route('companyId'));
        return $company && $company->pivot->role === 'owner';
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'balance' => 'required|numeric',
            'is_active' => 'boolean',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active'),
        ]);
    }
}