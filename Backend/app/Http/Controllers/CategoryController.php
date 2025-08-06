<?php

namespace App\Http\Controllers;

use App\Repositories\CategoryRepository;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $repo;
    public function __construct(CategoryRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        try {
            $categories = $this->repo->all();
            return apiResponse(
                true,
                'Categories retrieved successfully',
                $categories,
                200
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to retrieve categories: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            $cat = $this->repo->create($request->validated());
            $all = $this->repo->all();
            return apiResponse(
                true,
                'Category added successfully',
                $all,
                201
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to create category: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $cat = $this->repo->update($id, $request->validated());
            if (!$cat) {
                return apiResponse(
                    false,
                    'Category not found',
                    [],
                    404
                );
            }
            $all = $this->repo->all();
            return apiResponse(
                true,
                'Category updated successfully',
                $all,
                200
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to update category: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->repo->delete($id);
            if (!$deleted) {
                return apiResponse(
                    false,
                    'Category not found',
                    [],
                    404
                );
            }
            $all = $this->repo->all();
            return apiResponse(
                true,
                'Category deleted successfully',
                $all,
                200
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to delete category: ' . $e->getMessage(),
                [],
                500
            );
        }
    }
}
