<?php

namespace App\Http\Requests;

use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
{
    use HasCompanyAuthorization;
    
    public function authorize(): bool
    {
        return $this->isCompanyRole((int) $this->route('companyId'), ['owner', 'admin']);
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