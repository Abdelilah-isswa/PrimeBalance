<?php

namespace App\Http\Traits;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;

trait HasCompanyAuthorization
{
    /**
     * Alias used in older docs/controllers.
     */
    protected function getAuthorizedCompany(int $companyId): Company
    {
        return $this->getCompanyForMember($companyId);
    }

    /**
     * Get company and ensure user has access to it (any member)
     */
    protected function getCompanyForMember(int $companyId): Company
    {
        return Auth::user()->companies()->findOrFail($companyId);
    }

    /**
     * Check if user has one of the given roles in the company.
     */
    protected function isCompanyRole(int $companyId, array|string $roles): bool
    {
        $company = Auth::user()->companies()->find($companyId);
        if (!$company) {
            return false;
        }

        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($company->pivot->role, $roles, true);
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
        return $this->isCompanyRole($companyId, 'owner');
    }

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