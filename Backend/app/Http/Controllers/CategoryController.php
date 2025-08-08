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
        $categories = $this->repo->all()->map(function($category) {
            return [
                'id' => $category->id,
                'name' => $category->name
            ];
        });
        
        return response()->json($categories);
    }

    public function store(StoreCategoryRequest $request)
    {
        $cat = $this->repo->create($request->validated());
        $all = $this->repo->all();
        return response()->json(['message' => 'Category added successfully', 'data' => $all], 201);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $cat = $this->repo->update($id, $request->validated());
        if (!$cat) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $all = $this->repo->all();
        return response()->json(['message' => 'Category updated successfully', 'data' => $all]);
    }

    public function destroy($id)
    {
        $deleted = $this->repo->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $all = $this->repo->all();
        return response()->json(['message' => 'Category deleted successfully', 'data' => $all]);
    }
}
