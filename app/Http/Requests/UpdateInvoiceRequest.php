<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $company = $this->user()->companies()->find($this->route('companyId'));
        return $company && $company->pivot->role === 'owner';
    }

    public function rules(): array
    {
        return [
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,sent,paid,cancelled',
        ];
    }
}