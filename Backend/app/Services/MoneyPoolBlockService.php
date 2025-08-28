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

    public function createBlock(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Get current month's money pool ID
            $currentMoneyPool = $this->moneyPoolRepository->getCurrentMonthMoneyPool();

            if (!$currentMoneyPool) {
                return [
                    'error' => true,
                    'message' => __('money_pool_blocks.no_active_money_pool'),
                    'code' => 422
                ];
            }

            $data['money_pool_id'] = $currentMoneyPool->money_pool_id;
            $amountDetails = $this->getAmountDetails($data, false);

            if ($amountDetails['total_available'] < $data['amount']) {
                return [
                    'error' => true,
                    'message' => __('money_pool_blocks.block_not_enough_amount'),
                    'code' => 422
                ];
            }

            $data['created_by'] = Auth::id();
            $block = $this->moneyPoolBlockRepository->create($data);

            if (! $block) {
                return null;
            }

            $this->updateMoneyPoolBlockedAmount($data['money_pool_id']);

            return $block;
        });
    }

    public function updateBlock(int $blockId, array $data)
    {
        return DB::transaction(function () use ($blockId, $data) {
            // Get current block to find its money_pool_id
            $existingBlock = $this->moneyPoolBlockRepository->find($blockId);

            if (!$existingBlock) {
                return null;
            }

            $data['money_pool_id'] = $existingBlock->money_pool_id;
            $amountDetails = $this->getAmountDetails($data, true, $blockId);

            if ($amountDetails['total_available'] < $data['amount']) {
                return [
                    'error' => true,
                    'message' => __('money_pool_blocks.block_not_enough_amount'),
                    'code' => 422
                ];
            }

            $block = $this->moneyPoolBlockRepository->update($blockId, $data);

            if (! $block) {
                return null;
            }

            $this->updateMoneyPoolBlockedAmount($data['money_pool_id']);

            return $block;
        });
    }

    public function getBlocksByPoolId(int $moneyPoolId)
    {
        return $this->moneyPoolBlockRepository->findByPoolId($moneyPoolId);
    }

    public function deleteBlock(int $blockId): bool
    {
        return $this->moneyPoolBlockRepository->delete($blockId);
    }

    private function getAmountDetails(array $data, bool $isUpdate, ?int $blockId = null)
    {
        $totalBlocked = $isUpdate ? $this->moneyPoolBlockRepository->getTotalBlockedAmountWithoutCurrentBlock($data['money_pool_id'], $blockId)
            : $this->moneyPoolBlockRepository->getTotalBlockedAmount($data['money_pool_id']);
        $totalAvailable = $this->moneyPoolRepository->getTotalAvailableAmount($data['money_pool_id'], $totalBlocked);

        return [
            'total_blocked' => $totalBlocked,
            'total_available' => $totalAvailable,
        ];
    }

    private function updateMoneyPoolBlockedAmount(int $moneyPoolId): void
    {
        $totalBlocked = $this->moneyPoolBlockRepository->getTotalBlockedAmount($moneyPoolId);
        $totalAvailable = $this->moneyPoolRepository->getTotalAvailableAmount($moneyPoolId, $totalBlocked);

        $this->moneyPoolRepository->update($moneyPoolId, [
            'blocked_amount' => $totalBlocked,
            'total_available_amount' => $totalAvailable,
        ]);
    }
}
