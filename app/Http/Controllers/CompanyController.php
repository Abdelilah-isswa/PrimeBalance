<?php

namespace App\Http\Controllers;

use App\Services\CompanyService;
use App\Models\Company;
use App\Models\Invitation;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Requests\InviteUserRequest;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    use HasCompanyAuthorization;
    
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function index()
    {
        $companies = Auth::user()->companies;
        return view('companies.index', compact('companies'));
    }

    public function show(Request $request, $id)
    {
        $company = $this->getAuthorizedCompany($id);
        
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $metrics = $this->companyService->getDashboardMetrics($company, $startDate, $endDate);
        $netProfit = $metrics['totalIncome'] - $metrics['totalExpense'];
        
        return view('companies.show', array_merge(
            compact('company', 'startDate', 'endDate', 'netProfit'),
            $metrics
        ));
    }

    public function edit($id)
    {
        $company = $this->getCompanyAsOwner($id, 'edit company');
        $company->load(['users' => function($query) {
            $query->whereNull('company_user.left_at');
        }]);
        
        $userRole = $company->pivot->role;
        return view('companies.edit', compact('company', 'userRole'));
    }

    public function update(UpdateCompanyRequest $request, $id)
    {
        $company = $this->getAuthorizedCompany($id);
        $this->companyService->updateCompany($company, $request->validated());
        return redirect("/companies/{$id}")->with('success', 'Company updated successfully');
    }

    public function deactivate($id)
    {
        $company = $this->getCompanyAsOwner($id, 'deactivate company');
        $this->companyService->deactivateCompany($company);
        return redirect('/companies')->with('success', 'Company deactivated successfully');
    }

    public function inviteUser(InviteUserRequest $request, $id)
    {
        $company = $this->getAuthorizedCompany($id);
        $result = $this->companyService->inviteUser($company, $request->email, $request->role);
        
        if ($result['success']) {
            return back()->with('success', $result['message']);
        }
        
        return back()->with('error', $result['message']);
    }

    public function showInvitation($token)
    {
        $invitation = \App\Models\Invitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        if ($invitation->isExpired()) {
            $invitation->update(['status' => 'expired']);
            return view('invitations.expired');
        }

        return view('invitations.accept', compact('invitation'));
    }

    public function acceptInvitation($token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        if ($invitation->isExpired()) {
            $invitation->update(['status' => 'expired']);
            return redirect('/login')->with('error', 'This invitation has expired');
        }

        // Check if user is logged in
        if (!Auth::check()) {
            session(['invitation_token' => $token]);
            return redirect('/login')->with('message', 'Please login or register to accept the invitation');
        }

        $result = $this->companyService->acceptInvitation($invitation);
        
        if ($result['success']) {
            return redirect($result['redirect'])->with('success', $result['message']);
        }
        
        return redirect('/')->with('error', $result['message']);
    }

    public function declineInvitation($token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        $invitation->update(['status' => 'expired']);

        return redirect('/login')->with('message', 'Invitation declined');
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(StoreCompanyRequest $request)
    {
        $this->companyService->createCompany($request->validated());
        return redirect('/')->with('success', 'Company created successfully');
    }

    public function removeUser($companyId, $userId)
    {
        $company = $this->getCompanyAsOwner($companyId, 'remove users');
        $result = $this->companyService->removeUser($company, $userId);
        
        if ($result['success']) {
            return back()->with('success', $result['message']);
        }
        
        return back()->with('error', $result['message']);
    }

    public function updateUserRole(UpdateUserRoleRequest $request, $companyId, $userId)
    {
        $company = $this->getAuthorizedCompany($companyId);
        $result = $this->companyService->updateUserRole($company, $userId, $request->role);
        
        if ($result['success']) {
            return back()->with('success', $result['message']);
        }
        
        return back()->with('error', $result['message']);
    }
}
