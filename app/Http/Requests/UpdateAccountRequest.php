<?php

namespace App\Http\Requests;

use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountRequest extends FormRequest
{
    use HasCompanyAuthorization;
    
    public function authorize(): bool
    {
        return $this->isCompanyOwner($this->route('companyId'));
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
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