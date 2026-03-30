<?php

namespace App\Http\Requests;

use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class ReceivePaymentRequest extends FormRequest
{
    use HasCompanyAuthorization;
    
    public function authorize(): bool
    {
        return $this->isCompanyOwner($this->route('companyId'));
    }

    public function rules(): array
    {
        return [
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'nullable|exists:categories,id',
            'date' => 'required|date',
        ];
    }
}