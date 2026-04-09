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
            'supplier_id' => 'required|exists:suppliers,id',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'nullable|in:unpaid,partial,paid,cancelled',
        ];
    }
}