<?php

namespace App\Repositories;

use App\Models\MoneyPool;

class MoneyPoolRepository implements MoneyPoolRepositoryInterface
{
    public function query()
    {
        return MoneyPool::query();
    }

    public function getCurrentMonthMoneyPool()
    {
        return MoneyPool::with(['creator', 'settings', 'blocks'])
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function find(int $id)
    {
        return MoneyPool::with(['creator', 'settings', 'blocks'])->find($id);
    }

    public function update(int $id, array $data): ?MoneyPool
    {
        $moneyPool = MoneyPool::find($id);

        if (! $moneyPool) {
            return null;
        }

        $moneyPool->update($data);

        return $moneyPool->fresh();
    }

    public function getTotalAvailableAmount(int $moneyPoolId, float $totalBlocked): float
    {
        return MoneyPool::where('money_pool_id', $moneyPoolId)->first()->total_pool_amount - $totalBlocked;
    }
}
