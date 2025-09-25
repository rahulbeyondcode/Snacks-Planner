<?php

namespace App\Repositories;

interface ContributionRepositoryInterface
{
    /**
     * Bulk update status for multiple contributions.
     * @param array $contributions Array of ['id' => int, 'status' => string]
     * @return int Number of updated records
     */
    public function bulkUpdateStatus(array $paidUserIds, $userId = null);

    /**
     * List all contributions with optional filters and pagination.
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function listAll(array $filters = []);
    public function create(array $data): \Illuminate\Database\Eloquent\Model;
    public function find(int $id, array $columns = ['*'], array $relations = []): ?\Illuminate\Database\Eloquent\Model;
    public function findByUser(int $userId);
    public function update(int $id, array $data): ?\Illuminate\Database\Eloquent\Model;
    public function delete(int $id): bool;
    public function getTotalContributions();
    public function getCurrentMonthCounts();
}
