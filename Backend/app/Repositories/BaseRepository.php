<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all records with optional relationships and filters
     */
    public function all(array $columns = ['*'], array $relations = [], array $filters = []): Collection
    {
        $query = $this->model->query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        if (!empty($filters)) {
            $this->applyFilters($query, $filters);
        }

        return $query->get($columns);
    }

    /**
     * Find a record by ID with optional relationships
     */
    public function find(int $id, array $columns = ['*'], array $relations = []): ?Model
    {
        $query = $this->model->query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->find($id, $columns);
    }

    /**
     * Find a record by ID or fail with optional relationships
     */
    public function findOrFail(int $id, array $columns = ['*'], array $relations = []): Model
    {
        $query = $this->model->query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->findOrFail($id, $columns);
    }

    /**
     * Create a new record
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update a record by ID
     */
    public function update(int $id, array $data): ?Model
    {
        $record = $this->find($id);

        if ($record) {
            $record->update($data);
            return $record->fresh();
        }

        return null;
    }

    /**
     * Update a record by ID or fail
     */
    public function updateOrFail(int $id, array $data): Model
    {
        $record = $this->findOrFail($id);
        $record->update($data);
        return $record->fresh();
    }

    /**
     * Delete a record by ID
     */
    public function delete(int $id): bool
    {
        $record = $this->find($id);

        if ($record) {
            $record->delete();
            return true;
        }

        return false;
    }

    /**
     * Delete a record by ID or fail
     */
    public function deleteOrFail(int $id): bool
    {
        $record = $this->findOrFail($id);
        $record->delete();
        return true;
    }

    /**
     * Find records by a specific column value
     */
    public function findBy(string $column, $value, array $columns = ['*'], array $relations = []): Collection
    {
        $query = $this->model->query()->where($column, $value);

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->get($columns);
    }

    /**
     * Find first record by a specific column value
     */
    public function findFirstBy(string $column, $value, array $columns = ['*'], array $relations = []): ?Model
    {
        $query = $this->model->query()->where($column, $value);

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->first($columns);
    }

    /**
     * Paginate records with optional filters
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = [], array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        if (!empty($filters)) {
            $this->applyFilters($query, $filters);
        }

        return $query->paginate($perPage, $columns);
    }

    /**
     * Apply filters to query (to be overridden by child classes)
     */
    protected function applyFilters($query, array $filters): void
    {
        // Override in child classes to implement specific filtering logic
    }

    /**
     * Get the model instance
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Get a new query builder instance
     */
    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->model->query();
    }
}
