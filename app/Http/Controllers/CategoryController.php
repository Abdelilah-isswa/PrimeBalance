<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    use HasCompanyAuthorization;
    
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index($companyId)
    {
        $company = $this->getCompanyForMember($companyId);
        $categories = $company->categories;
        
        return view('categories.index', compact('company', 'categories'));
    }

    public function store(StoreCategoryRequest $request, $companyId)
    {
        $data = array_merge($request->validated(), ['company_id' => $companyId]);
        $this->categoryService->createCategory($data);
        return redirect()->route('categories.index', $companyId);
    }

    public function update(UpdateCategoryRequest $request, $companyId, $categoryId)
    {
        $company = $this->getCompanyForMember($companyId);
        $category = $company->categories()->findOrFail($categoryId);
        $this->categoryService->updateCategory($category, $request->validated());
        return redirect()->route('categories.index', $companyId);
    }

    public function destroy($companyId, $categoryId)
    {
        $company = $this->getCompanyForOwner($companyId, 'delete categories');
        $category = $company->categories()->findOrFail($categoryId);
        $this->categoryService->deleteCategory($category);

        return redirect()->route('categories.index', $companyId);
    }
}
