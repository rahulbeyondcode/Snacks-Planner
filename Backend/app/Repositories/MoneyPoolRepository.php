<?php

namespace App\Repositories;

use App\Models\MoneyPool;
use App\Repositories\Traits\DateHelperTrait;

class MoneyPoolRepository extends BaseRepository implements MoneyPoolRepositoryInterface
{
    use DateHelperTrait;

    public function __construct(MoneyPool $model)
    {
        parent::__construct($model);
    }

    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->model->query();
    }

    public function getCurrentMonthMoneyPool()
    {
        return $this->model->with(['creator', 'settings', 'blocks.creator'])
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function find(int $id, array $columns = ['*'], array $relations = []): ?\Illuminate\Database\Eloquent\Model
    {
        $defaultRelations = ['creator', 'settings', 'blocks.creator'];
        $relations = !empty($relations) ? $relations : $defaultRelations;
        
        return $this->model->with($relations)->find($id, $columns);
    }

    public function update(int $id, array $data): ?\Illuminate\Database\Eloquent\Model
    {
        $moneyPool = $this->find($id);

        if (!$moneyPool) {
            return null;
        }

        $moneyPool->update($data);

        return $moneyPool->fresh();
    }

    public function getTotalAvailableAmount(int $moneyPoolId, float $totalBlocked): float
    {
        $moneyPool = $this->find($moneyPoolId);
        return $moneyPool ? $moneyPool->total_pool_amount - $totalBlocked : 0;
    }

    /**
     * Get money pools for a specific month
     */
    public function getByMonth(string $month)
    {
        return $this->model->with(['creator', 'settings', 'blocks.creator'])
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$month])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get money pools within date range
     */
    public function getByDateRange(string $dateFrom, string $dateTo)
    {
        return $this->model->with(['creator', 'settings', 'blocks.creator'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get total blocked amount for a money pool
     */
    public function getTotalBlockedAmount(int $moneyPoolId): float
    {
        return $this->model->find($moneyPoolId)
            ->blocks()
            ->sum('amount');
    }

    /**
     * Get money pool with calculated available amount
     */
    public function getWithAvailableAmount(int $moneyPoolId): ?MoneyPool
    {
        $moneyPool = $this->find($moneyPoolId);

        if ($moneyPool) {
            $totalBlocked = $this->getTotalBlockedAmount($moneyPoolId);
            $moneyPool->available_amount = $moneyPool->total_pool_amount - $totalBlocked;
        }

        return $moneyPool;
    }
}
