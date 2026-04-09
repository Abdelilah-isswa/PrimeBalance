<?php

namespace App\Http\Requests;

use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    use HasCompanyAuthorization;
    
    public function authorize(): bool
    {
        return $this->isCompanyOwner($this->route('companyId'));
    }

    public function rules(): array
    {
        return [
            'total_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,sent,partial,paid,cancelled',
            'due_date' => 'nullable|date|after:today',
            'send_email' => 'boolean',
            'items' => 'nullable|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'items.min' => 'At least one item is required',
            'items.*.description.required' => 'Item description is required',
            'items.*.price.required' => 'Item price is required',
            'items.*.quantity.required' => 'Item quantity is required',
        ];
    }
}