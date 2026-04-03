<?php

namespace App\Http\Controllers;

use App\Services\CompanyService;
use App\Models\Invitation;
use App\Http\Requests\InviteUserRequest;
use App\Http\Controllers\Api\BaseController;
use App\Http\Traits\HasCompanyAuthorization;

class InvitationController extends BaseController
{
    use HasCompanyAuthorization;
    
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function store(InviteUserRequest $request, $companyId)
    {
        $company = $this->getCompanyForMember($companyId);
        $result = $this->companyService->inviteUser($company, $request->email, $request->role);
        
        if ($result['success']) {
            return $this->sendResponse($result);
        }
        
        return $this->sendError($result['message']);
    }

    public function show($token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->with('company')
            ->firstOrFail();

        if ($invitation->isExpired()) {
            $invitation->update(['status' => 'expired']);
            return $this->sendError('Invitation expired');
        }

        return $this->sendResponse($invitation);
    }

    public function accept($token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        if ($invitation->isExpired()) {
            $invitation->update(['status' => 'expired']);
            return $this->sendError('This invitation has expired');
        }

        if (!auth('sanctum')->check()) {
            return $this->sendError('Please login or register to accept the invitation', 401);
        }

        $result = $this->companyService->acceptInvitation($invitation);
        
        if ($result['success']) {
            return $this->sendResponse($result);
        }
        
        return $this->sendError($result['message']);
    }

    public function decline($token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        $invitation->update(['status' => 'expired']);

        return $this->sendResponse([], 'Invitation declined');
    }
}
