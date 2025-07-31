<?php

namespace App\Repositories;

use App\Models\MoneyPoolBlock;

class MoneyPoolBlockRepository implements MoneyPoolBlockRepositoryInterface
{
    public function create(array $data)
    {
        return MoneyPoolBlock::create($data);
    }

    public function findByPoolId(int $moneyPoolId)
    {
        return MoneyPoolBlock::with(['creator', 'moneyPool'])
            ->where('money_pool_id', $moneyPoolId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getTotalBlockedAmount(int $moneyPoolId): float
    {
        return MoneyPoolBlock::where('money_pool_id', $moneyPoolId)->sum('amount');
    }

    public function find(int $id)
    {
        return MoneyPoolBlock::with(['creator', 'moneyPool'])->find($id);
    }

    public function update(int $id, array $data)
    {
        $block = MoneyPoolBlock::find($id);
        if ($block) {
            $block->update($data);
        }

        return $block;
    }
}
