<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Http\Request;

class DocumentController extends BaseController
{
    use HasCompanyAuthorization;
    
    public function index(Request $request, $companyId)
    {
        $company = $this->getAuthorizedCompany($companyId);
        $type = $request->get('type', 'invoices');
        
        if ($type === 'bills') {
            $documents = $company->bills()->with('supplier')->orderBy('created_at', 'desc')->get();
        } else {
            $documents = $company->invoices()->with('client')->orderBy('created_at', 'desc')->get();
        }
        
        return $this->sendResponse(compact('company', 'documents', 'type'));
    }
}

