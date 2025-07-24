<?php

namespace App\Repositories;

interface ContributionRepositoryInterface
{
    /**
     * List all contributions with optional filters and pagination.
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function listAll(array $filters = []);
    public function create(array $data);
    public function find(int $id);
    public function findByUser(int $userId);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function getTotalContributions();
}
