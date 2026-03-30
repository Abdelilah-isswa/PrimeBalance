<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function createCategory(array $data): Category
    {
        return Category::create([
            'company_id' => $data['company_id'],
            'name' => $data['name'],
        ]);
    }

    public function updateCategory(Category $category, array $data): bool
    {
        return $category->update(['name' => $data['name']]);
    }

    public function deleteCategory(Category $category): bool
    {
        return $category->delete();
    }
}