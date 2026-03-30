<?php

namespace App\Http\Requests;

use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
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
            'email' => 'required|email',
            'address' => 'required|string',
            'phone' => 'required|string',
        ];
    }
}