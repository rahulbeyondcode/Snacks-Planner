<?php

namespace App\Http\Controllers;

use App\Repositories\CategoryRepository;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    protected $repo;
    public function __construct(CategoryRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Get all active categories for consistent response format
     */
    private function getAllActiveCategories()
    {
        $categories = $this->repo->all();
        return CategoryResource::collection($categories);
    }

    public function index()
    {
        return $this->resourceCollectionResponse($this->getAllActiveCategories());
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->repo->create($request->validated());
        return $this->createdResponse($this->getAllActiveCategories(), 'Category created successfully');
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = $this->repo->update($id, $request->validated());
        if (!$category) {
            return $this->notFoundResponse('Category not found');
        }
        return $this->updatedResponse($this->getAllActiveCategories(), 'Category updated successfully');
    }

    public function destroy($id)
    {
        $category = $this->repo->find($id);
        if (!$category) {
            return $this->notFoundResponse('Category not found');
        }
        $this->repo->delete($id);
        return $this->successResponse('Category deleted successfully', $this->getAllActiveCategories());
    }
}
