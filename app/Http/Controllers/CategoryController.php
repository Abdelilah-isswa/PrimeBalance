<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index($companyId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        $categories = $company->categories;
        
        return view('categories.index', compact('company', 'categories'));
    }

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

        return redirect("/companies/{$companyId}/categories");
    }

    public function update(Request $request, $companyId, $categoryId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can update categories');
        }

        $category = $company->categories()->findOrFail($categoryId);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update(['name' => $request->name]);

        return redirect("/companies/{$companyId}/categories");
    }

    public function destroy($companyId, $categoryId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can delete categories');
        }

        $category = $company->categories()->findOrFail($categoryId);
        $category->delete();

        return redirect("/companies/{$companyId}/categories");
    }
}
