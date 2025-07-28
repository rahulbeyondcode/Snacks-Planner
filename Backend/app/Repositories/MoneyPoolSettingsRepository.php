<?php

namespace App\Repositories;

use App\Models\MoneyPool;
use App\Models\MoneyPoolBlock;
use App\Models\MoneyPoolSettings;

class MoneyPoolSettingsRepository implements MoneyPoolSettingsRepositoryInterface
{
    public function create(array $data)
    {
        return MoneyPoolSettings::create($data);
    }

    public function find(int $id)
    {
        return MoneyPoolSettings::find($id);
    }

    public function update(int $id, array $data)
    {
        $settings = MoneyPoolSettings::find($id);
        if ($settings) {
            $settings->update($data);
        }

        return $settings;
    }

    public function isUsedInMoneyPools(int $id): bool
    {
        return MoneyPool::where('money_pool_setting_id', $id)->exists();
    }

    public function isUsedInMoneyPoolBlocks(int $id): bool
    {
        return MoneyPoolBlock::whereHas('moneyPool', function ($query) use ($id) {
            $query->where('money_pool_setting_id', $id);
        })->exists();
    }

    public function getLatestSettings()
    {
        return MoneyPoolSettings::latest()->first();
    }
}
