<?php

namespace App\Http\Traits;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;

use Illuminate\Foundation\Http\FormRequest;

trait HasCompanyAuthorization
{
    /**
     * Get company and ensure user has access to it (any member)
     */
    protected function getCompanyForMember(int $companyId): Company
    {
        return Auth::user()->companies()->findOrFail($companyId);
    }

    /**
     * Get company and ensure user is owner
     */
    protected function getCompanyForOwner(int $companyId, string $action = 'perform this action'): Company
    {
        $company = $this->getCompanyForMember($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, "Only owners can {$action}");
        }

        return $company;
    }

    /**
     * Check if user is owner of the company
     */
    protected function isCompanyOwner(int $companyId): bool
    {
        $company = Auth::user()->companies()->find($companyId);
        return $company && $company->pivot->role === 'owner';
    }

    /**
     * Get company and ensure user has specific role(s)
     */
    /**
     * Get company and ensure user has specific role(s)
     */
    protected function getCompanyWithRole(int $companyId, array|string $roles, string $action = 'perform this action'): Company
    {
        $company = $this->getCompanyForMember($companyId);
        $roles = is_array($roles) ? $roles : [$roles];
        
        if (!in_array($company->pivot->role, $roles)) {
            $rolesList = implode(', ', $roles);
            abort(403, "Only {$rolesList} can {$action}");
        }

        return $company;
    }
}