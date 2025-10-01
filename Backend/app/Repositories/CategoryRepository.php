<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository extends BaseRepository
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all categories ordered by ID
     */
    public function all(array $columns = ['*'], array $relations = [], array $filters = []): Collection
    {
        return $this->model->orderBy('id')->get($columns);
    }
}
