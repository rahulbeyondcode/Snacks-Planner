<?php

namespace App\Services;

use App\Repositories\Traits\TransactionHelperTrait;

/**
 * Base Service Class
 * 
 * Provides common CRUD operations and transaction handling for all services.
 * Services should extend this class to reduce code duplication.
 */
abstract class BaseService
{
    use TransactionHelperTrait;

    /**
     * The repository instance.
     *
     * @var mixed
     */
    protected $repository;

    /**
     * Get a record by ID.
     *
     * @param int $id
     * @return mixed
     */
    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    /**
     * Create a new record.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    /**
     * Update a record by ID.
     *
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete a record by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Get all records with optional filters.
     *
     * @param array $filters
     * @return mixed
     */
    public function all(array $filters = [])
    {
        return $this->repository->all($filters);
    }

    /**
     * List records with optional filters (alias for all).
     *
     * @param array $filters
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->repository->list($filters);
    }
}
