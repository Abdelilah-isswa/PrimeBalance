<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function store(Request $request, $companyId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can create categories');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Category::create([
            'company_id' => $companyId,
            'name' => $request->name,
        ]);

        return redirect("/companies/{$companyId}");
    }
}
