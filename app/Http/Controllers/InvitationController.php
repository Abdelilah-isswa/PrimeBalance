<?php

namespace App\Http\Controllers;

use App\Services\CompanyService;
use App\Models\Invitation;
use App\Http\Requests\InviteUserRequest;
use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
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
            return back()->with('success', $result['message']);
        }
        
        return back()->with('error', $result['message']);
    }

    public function show($token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        if ($invitation->isExpired()) {
            $invitation->update(['status' => 'expired']);
            return view('invitations.expired');
        }

        return view('invitations.accept', compact('invitation'));
    }

    public function accept($token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        if ($invitation->isExpired()) {
            $invitation->update(['status' => 'expired']);
            return redirect()->route('login')->with('error', 'This invitation has expired');
        }

        if (!Auth::check()) {
            session(['invitation_token' => $token]);
            return redirect()->route('login')->with('message', 'Please login or register to accept the invitation');
        }

        $result = $this->companyService->acceptInvitation($invitation);
        
        if ($result['success']) {
            return redirect()->route('companies.show', $invitation->company_id)->with('success', $result['message']);
        }
        
        return redirect()->route('home')->with('error', $result['message']);
    }

    public function decline($token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        $invitation->update(['status' => 'expired']);

        return redirect()->route('login')->with('message', 'Invitation declined');
    }
}