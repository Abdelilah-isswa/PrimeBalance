<?php

namespace App\Http\Requests;

use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    use HasCompanyAuthorization;
    
    public function authorize(): bool
    {
        return $this->isCompanyOwner($this->route('id'));
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'currency' => 'required|string|max:10',
        ];
    }
}