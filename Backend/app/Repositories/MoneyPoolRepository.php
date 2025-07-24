<?php

namespace App\Repositories;

use App\Models\MoneyPool;
use App\Models\MoneyPoolBlock;

class MoneyPoolRepository implements MoneyPoolRepositoryInterface
{
    public function query()
    {
        return MoneyPool::query();
    }

    public function create(array $data)
    {
        return MoneyPool::create($data);
    }

    public function find(int $id)
    {
        return MoneyPool::with('blocks')->find($id);
    }

    public function update(int $id, array $data)
    {
        $pool = MoneyPool::find($id);
        if ($pool) {
            $pool->update($data);
        }
        return $pool;
    }

    public function getTotalCollected(int $moneyPoolId): float
    {
        $pool = MoneyPool::find($moneyPoolId);
        return $pool ? (float)$pool->total_collected_amount : 0;
    }

    public function getTotalBlocked(int $moneyPoolId): float
    {
        return MoneyPoolBlock::where('money_pool_id', $moneyPoolId)->sum('amount');
    }
}
