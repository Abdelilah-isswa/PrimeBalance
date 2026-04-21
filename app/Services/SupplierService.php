<?php

namespace App\Services;

use App\Models\Supplier;

class SupplierService
{
    public function createSupplier(array $data): Supplier
    {
        return Supplier::create([
            'company_id' => $data['company_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'address' => $data['address'],
            'phone' => $data['phone'],
        ]);
    }

    public function updateSupplier(Supplier $supplier, array $data): bool
    {
        return $supplier->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'address' => $data['address'],
            'phone' => $data['phone'],
        ]);
    }

    public function canDelete(Supplier $supplier): bool
    {
        return !$supplier->bills()->exists();
    }

    public function deleteSupplier(Supplier $supplier): bool
    {
        if (!$this->canDelete($supplier)) {
            return false;
        }

        return $supplier->delete();
    }

    public function calculateSupplierBalances($suppliers)
    {
        return $suppliers->map(function($supplier) {
            $totalBilled = $supplier->bills->sum('total_amount');
            $totalPaid = $supplier->bills->sum('amount_paid');
            $supplier->balance = $totalPaid - $totalBilled;
            return $supplier;
        });
    }
}