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
        return MoneyPoolBlock::where('money_pool_id', $moneyPoolId)->get();
    }
}
