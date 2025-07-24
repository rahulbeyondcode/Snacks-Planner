<?php

namespace App\Services;

use App\Repositories\ContributionRepositoryInterface;

class ContributionService implements ContributionServiceInterface
{
    /**
     * List all contributions with optional filters and pagination (admin).
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listAllContributions(array $filters = [])
    {
        return $this->contributionRepository->listAll($filters);
    }
    protected $contributionRepository;

    public function __construct(ContributionRepositoryInterface $contributionRepository)
    {
        $this->contributionRepository = $contributionRepository;
    }

    public function createContribution(array $data)
    {
        return $this->contributionRepository->create($data);
    }

    public function getContribution(int $id)
    {
        return $this->contributionRepository->find($id);
    }

    public function getUserContributions(int $userId)
    {
        return $this->contributionRepository->findByUser($userId);
    }

    public function updateContribution(int $id, array $data)
    {
        return $this->contributionRepository->update($id, $data);
    }

    public function deleteContribution(int $id)
    {
        return $this->contributionRepository->delete($id);
    }
}
