<?php

namespace App\Repositories;

use App\Models\MoneyPool;
use App\Models\MoneyPoolBlock;
use App\Models\MoneyPoolSettings;

class MoneyPoolSettingsRepository implements MoneyPoolSettingsRepositoryInterface
{
    public function create(array $data): \Illuminate\Database\Eloquent\Model
    {
        return MoneyPoolSettings::create($data);
    }

    public function find(int $id, array $columns = ['*'], array $relations = []): ?\Illuminate\Database\Eloquent\Model
    {
        $query = MoneyPoolSettings::query();
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->find($id, $columns);
    }

    public function update(int $id, array $data): ?\Illuminate\Database\Eloquent\Model
    {
        $settings = MoneyPoolSettings::find($id);
        if ($settings) {
            $settings->update($data);
            return $settings->fresh();
        }

        return null;
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

    public function destroyMoneyPoolSettings(int $id)
    {
        return MoneyPoolSettings::destroy($id);
    }
}
