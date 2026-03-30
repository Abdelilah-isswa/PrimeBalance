<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
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

    public function store(StoreCategoryRequest $request, $companyId)
    {
        $data = array_merge($request->validated(), ['company_id' => $companyId]);
        $this->categoryService->createCategory($data);
        return redirect("/companies/{$companyId}/categories");
    }

    public function update(UpdateCategoryRequest $request, $companyId, $categoryId)
    {
        $company = Auth::user()->companies()->findOrFail($companyId);
        $category = $company->categories()->findOrFail($categoryId);
        $this->categoryService->updateCategory($category, $request->validated());
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
