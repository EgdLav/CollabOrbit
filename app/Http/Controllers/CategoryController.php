<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\WorkspaceResource;
use App\Http\Responses\ApiResponse;
use App\Models\Category;
use App\Models\Workspace;
use App\Services\CategoryService;
use App\Services\WorkspaceService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request, Workspace $workspace)
    {
        $category = $this->categoryService->create(
            $request->validated(),
            $workspace,
        );
        return ApiResponse::success('Category successfully created', 201, [
            'category' => new CategoryResource($category),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Workspace $workspace, Category $category)
    {
        $this->authorize('view', $workspace);
        return ApiResponse::success(data: [
            'category' => new CategoryResource($category),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryUpdateRequest $request, Workspace $workspace, Category $category)
    {
        $category = $this->categoryService->update(
            $request->validated(),
            $category,
        );
        return ApiResponse::success('Category successfully updated', 200, [
            'category' => new CategoryResource($category),
        ]); // TODO category crud, task category change
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workspace $workspace, Category $category)
    {
        $this->authorize('deleteCategory', $workspace);
        $this->categoryService->delete(
            $category,
        );
        return ApiResponse::success();
    }
}
