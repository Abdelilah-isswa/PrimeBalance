<?php

namespace App\Http\Requests;

use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class StoreBillRequest extends FormRequest
{
    use HasCompanyAuthorization;
    
    public function authorize(): bool
    {
        return $this->isCompanyOwner($this->route('companyId'));
    }

    public function rules(): array
    {
        return [
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,sent,paid,cancelled',
        ];
    }
}