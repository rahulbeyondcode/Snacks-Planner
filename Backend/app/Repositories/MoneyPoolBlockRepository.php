<?php

namespace App\Repositories;

use App\Models\MoneyPoolBlock;

class MoneyPoolBlockRepository implements MoneyPoolBlockRepositoryInterface
{
    public function create(array $data): MoneyPoolBlock
    {
        return MoneyPoolBlock::create($data);
    }

    public function update(int $id, array $data): ?MoneyPoolBlock
    {
        $block = MoneyPoolBlock::find($id);

        if (! $block) {
            return null;
        }

        $block->update($data);

        return $block->fresh();
    }

    public function find(int $id): ?MoneyPoolBlock
    {
        return MoneyPoolBlock::with(['creator', 'moneyPool'])->find($id);
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

    public function delete(int $blockId): bool
    {
        $block = MoneyPoolBlock::find($blockId);

        if (! $block) {
            throw new Exception('Money pool block not found');
        }

        return $block->delete();
    }
}
