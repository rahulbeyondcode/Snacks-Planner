<?php

namespace App\Services;

interface ContributionServiceInterface
{
    /**
     * List all contributions with optional filters and pagination (admin).
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listAllContributions(array $filters = []);
    public function createContribution(array $data);
    public function getContribution(int $id);
    public function getUserContributions(int $userId);
    public function updateContribution(int $id, array $data);
    public function deleteContribution(int $id);
}
