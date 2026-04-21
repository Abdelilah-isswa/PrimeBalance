<?php

namespace App\Http\Requests;

use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
        ];
    }
}