<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

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

        $data = array_merge($request->only('name'), [
            'company_id' => $companyId,
        ]);

        $this->categoryService->createCategory($data);

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

        $this->categoryService->updateCategory($category, $request->only('name'));

        return redirect("/companies/{$companyId}/categories");
    }

    public function destroy($companyId, $categoryId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        
        if ($company->pivot->role !== 'owner') {
            abort(403, 'Only owners can delete categories');
        }

        $category = $company->categories()->findOrFail($categoryId);
        $this->categoryService->deleteCategory($category);

        return redirect("/companies/{$companyId}/categories");
    }
}
