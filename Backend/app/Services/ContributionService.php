<?php

namespace App\Services;

use App\Repositories\ContributionRepositoryInterface;

class ContributionService extends BaseService implements ContributionServiceInterface
{
    public function __construct(ContributionRepositoryInterface $contributionRepository)
    {
        $this->repository = $contributionRepository;
    }

    public function createContribution(array $data)
    {
        return $this->create($data);
    }

    public function getContribution(int $id)
    {
        return $this->find($id);
    }

    public function updateContribution(int $id, array $data)
    {
        return $this->update($id, $data);
    }

    public function deleteContribution(int $id)
    {
        return $this->delete($id);
    }

    /**
     * List all contributions with optional filters and pagination (admin).
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listAllContributions(array $filters = [])
    {
        return $this->repository->listAll($filters);
    }

    public function getUserContributions(int $userId)
    {
        return $this->repository->findByUser($userId);
    }

    /**
     * Bulk update status for all users for the current month.
     * @param array $paidUserIds
     * @param int|null $userId
     * @return int Number of updated records
     */
    public function bulkUpdateStatus(array $paidUserIds, $userId = null)
    {
        return $this->repository->bulkUpdateStatus($paidUserIds, $userId);
    }

    public function getCurrentMonthCounts()
    {
        return $this->repository->getCurrentMonthCounts();
    }

    public function getTotalContributions()
    {
        return $this->repository->getTotalContributions();
    }
}
