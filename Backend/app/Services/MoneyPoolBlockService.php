<?php

namespace App\Services;

use App\Repositories\MoneyPoolBlockRepositoryInterface;
use App\Repositories\MoneyPoolRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MoneyPoolBlockService implements MoneyPoolBlockServiceInterface
{
    public function __construct(
        private readonly MoneyPoolBlockRepositoryInterface $moneyPoolBlockRepository,
        private readonly MoneyPoolRepositoryInterface $moneyPoolRepository
    ) {}

    public function blockMoneyPool(array $data)
    {
        return DB::transaction(function () use ($data) {
            $isUpdate = isset($data['block_id']);

            if ($isUpdate) {
                $block = $this->moneyPoolBlockRepository->update($data['block_id'], $data);
            } else {
                $data['created_by'] = Auth::id();
                $block = $this->moneyPoolBlockRepository->create($data);
            }

            dd($block);

            $this->updateMoneyPoolBlockedAmount($data['money_pool_id']);

            return $block;
        });
    }

    public function getBlocksByPoolId(int $moneyPoolId)
    {
        return $this->moneyPoolBlockRepository->findByPoolId($moneyPoolId);
    }

    public function getBlock(int $id)
    {
        $block = $this->moneyPoolBlockRepository->find($id);

        if (! $block) {
            throw new Exception('Money pool block not found');
        }

        return $block;
    }

    public function deleteBlock(int $blockId): bool
    {
        return $this->moneyPoolBlockRepository->delete($blockId);
    }

    private function updateMoneyPoolBlockedAmount(int $moneyPoolId): void
    {
        $totalBlocked = $this->moneyPoolBlockRepository->getTotalBlockedAmount($moneyPoolId);
        $moneyPool = $this->moneyPoolRepository->find($moneyPoolId);

        if ($moneyPool) {
            $moneyPool->update([
                'blocked_amount' => $totalBlocked,
                'total_available_amount' => $moneyPool->total_pool_amount - $totalBlocked,
            ]);
        }
    }
}
