<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Controllers\Api\BaseController;
use App\Http\Traits\HasCompanyAuthorization;
use Illuminate\Http\Request;

class CategoryController extends BaseController
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
        
        return $this->sendResponse(compact('company', 'categories'));
    }

    public function store(StoreCategoryRequest $request, $companyId)
    {
        $data = array_merge($request->validated(), ['company_id' => $companyId]);
        $category = $this->categoryService->createCategory($data);
        return $this->sendCreated($category);
    }

    public function update(UpdateCategoryRequest $request, $companyId, $categoryId)
    {
        $company = $this->getCompanyForMember($companyId);
        $category = $company->categories()->findOrFail($categoryId);
        $this->categoryService->updateCategory($category, $request->validated());
        return $this->sendResponse($category->fresh());
    }

    public function destroy($companyId, $categoryId)
    {
        $company = $this->getCompanyWithRole($companyId, ['owner', 'admin'], 'delete categories');
        $category = $company->categories()->findOrFail($categoryId);
        $this->categoryService->deleteCategory($category);

        return $this->sendResponse([], 'Category deleted');
    }
}

