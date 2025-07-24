<?php

namespace App\Services;

use App\Repositories\MoneyPoolRepositoryInterface;
use App\Repositories\MoneyPoolBlockRepositoryInterface;

class MoneyPoolService implements MoneyPoolServiceInterface
{
    public function listPools(array $filters = [])
    {
        $query = $this->moneyPoolRepository->query();
        if (isset($filters['from'])) {
            $query->where('created_at', '>=', $filters['from']);
        }
        if (isset($filters['to'])) {
            $query->where('created_at', '<=', $filters['to']);
        }
        if (isset($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }
        return $query->with('blocks')->orderByDesc('created_at')->get();
    }

    protected $moneyPoolRepository;
    protected $moneyPoolBlockRepository;

    public function __construct(
        MoneyPoolRepositoryInterface $moneyPoolRepository,
        MoneyPoolBlockRepositoryInterface $moneyPoolBlockRepository
    ) {
        $this->moneyPoolRepository = $moneyPoolRepository;
        $this->moneyPoolBlockRepository = $moneyPoolBlockRepository;
    }

    public function createPool(array $data)
    {
        return $this->moneyPoolRepository->create($data);
    }

    public function getPool(int $id)
    {
        return $this->moneyPoolRepository->find($id);
    }

    public function blockAmount(int $moneyPoolId, array $blockData)
    {
        $blockData['money_pool_id'] = $moneyPoolId;
        $block = $this->moneyPoolBlockRepository->create($blockData);
        // Optionally update blocked_amount in MoneyPool
        $blocked = $this->getTotalBlocked($moneyPoolId);
        $this->moneyPoolRepository->update($moneyPoolId, ['blocked_amount' => $blocked]);
        return $block;
    }

    public function getTotalCollected(int $moneyPoolId): float
    {
        return $this->moneyPoolRepository->getTotalCollected($moneyPoolId);
    }

    public function getTotalBlocked(int $moneyPoolId): float
    {
        return $this->moneyPoolRepository->getTotalBlocked($moneyPoolId);
    }
}
