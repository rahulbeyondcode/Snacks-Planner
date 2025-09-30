<?php

namespace App\Repositories;

use App\Models\MoneyPoolBlock;
use Illuminate\Database\Eloquent\Model;

class MoneyPoolBlockRepository extends BaseRepository implements MoneyPoolBlockRepositoryInterface
{
    public function __construct(MoneyPoolBlock $model)
    {
        parent::__construct($model);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?Model
    {
        $block = $this->find($id);

        if (! $block) {
            return null;
        }

        $block->update($data);

        return $block->fresh();
    }

    public function find(int $id, array $columns = ['*'], array $relations = []): ?\Illuminate\Database\Eloquent\Model
    {
        $defaultRelations = ['creator', 'moneyPool'];
        $relations = !empty($relations) ? $relations : $defaultRelations;
        
        return $this->model->with($relations)->find($id, $columns);
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
        $block = $this->find($blockId);

        if (! $block) {
            return false;
        }

        return $block->delete();
    }

    public function getTotalBlockedAmountWithoutCurrentBlock(int $moneyPoolId, int $blockId): float
    {
        return MoneyPoolBlock::where('money_pool_id', $moneyPoolId)->where('block_id', '!=', $blockId)->sum('amount');
    }
}
