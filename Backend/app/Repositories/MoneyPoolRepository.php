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
        return MoneyPool::with(['creator', 'settings'])
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
