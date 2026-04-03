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
            ->with('company')
            ->first();

        if (!$invitation) {
            return $this->sendError('Invitation not found');
        }

        if ($invitation->status === 'accepted') {
            return $this->sendError('This invitation has already been accepted');
        }

        if ($invitation->status === 'expired') {
            return $this->sendError('Invitation expired');
        }

        if ($invitation->status !== 'pending') {
            return $this->sendError('Invitation invalid or already processed');
        }

        if ($invitation->isExpired()) {
            $invitation->update(['status' => 'expired']);
            return $this->sendError('Invitation expired');
        }

        return $this->sendResponse($invitation);
    }

    public function accept($token)
    {
        $invitation = Invitation::where('token', $token)->first();

        if (!$invitation) {
            return $this->sendError('Invitation not found');
        }

        if ($invitation->status === 'accepted') {
            return $this->sendError('This invitation has already been accepted');
        }

        if ($invitation->status === 'expired' || $invitation->isExpired()) {
            $invitation->update(['status' => 'expired']);
            return $this->sendError('This invitation has expired');
        }

        if ($invitation->status !== 'pending') {
            return $this->sendError('Invitation invalid or already processed');
        }

        $result = $this->companyService->acceptInvitation($invitation);
        
        if ($result['success']) {
            return $this->sendResponse($result);
        }
        
        return $this->sendError($result['message']);
    }

    public function decline($token)
    {
        $invitation = Invitation::where('token', $token)->first();

        if (!$invitation) {
            return $this->sendError('Invitation not found');
        }

        if ($invitation->status === 'accepted') {
            return $this->sendError('This invitation has already been accepted');
        }

        if ($invitation->status === 'expired' || $invitation->isExpired()) {
            $invitation->update(['status' => 'expired']);
            return $this->sendError('This invitation has expired');
        }

        if ($invitation->status !== 'pending') {
            return $this->sendError('Invitation invalid or already processed');
        }

        $invitation->update(['status' => 'declined']);

        return $this->sendResponse([], 'Invitation declined');
    }
}
