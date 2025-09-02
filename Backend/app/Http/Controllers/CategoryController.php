<?php

namespace App\Http\Controllers;

use App\Repositories\CategoryRepository;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;

class CategoryController extends Controller
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
        return response()->json([
            'success' => true,
            'data' => $this->getAllActiveCategories()
        ]);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->repo->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $this->getAllActiveCategories()
        ], 201);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = $this->repo->update($id, $request->validated());
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
                'data' => []
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $this->getAllActiveCategories()
        ]);
    }

    public function destroy($id)
    {
        $category = $this->repo->find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
                'data' => []
            ], 404);
        }
        $this->repo->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
            'data' => $this->getAllActiveCategories()
        ]);
    }
}
