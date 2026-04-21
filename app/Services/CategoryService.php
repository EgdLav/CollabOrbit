<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use App\Models\Workspace;

class CategoryService
{
    public function create(array $data, Workspace $workspace): Category {
        $data['workspace_id'] = $workspace->id;
        $category = Category::create($data);
        return $category;
    }

    public function update(array $data, Category $category): Category{
        $filteredData = array_filter($data, fn($v) => $v !== null);

        $category->update($filteredData);
        return $category->fresh();
    }
    public function delete(Category $category): bool {
        $category->delete();
        return true;
    }
}
