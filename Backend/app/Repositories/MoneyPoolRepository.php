<?php

namespace App\Repositories;

use App\Models\MoneyPool;

class MoneyPoolRepository implements MoneyPoolRepositoryInterface
{
    public function query()
    {
        return MoneyPool::query();
    }

    public function getCurrentMonthMoneyPools()
    {
        return MoneyPool::with(['creator', 'settings', 'blocks'])
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
