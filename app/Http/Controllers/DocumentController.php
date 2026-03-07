<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index(Request $request, $companyId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        $type = $request->get('type', 'invoices');
        
        if ($type === 'bills') {
            $documents = $company->bills()->with('supplier')->orderBy('created_at', 'desc')->get();
        } else {
            $documents = $company->invoices()->with('client')->orderBy('created_at', 'desc')->get();
        }
        
        return view('documents.index', compact('company', 'documents', 'type'));
    }
}
