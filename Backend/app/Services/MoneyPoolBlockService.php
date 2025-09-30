<?php

namespace App\Services;

use App\Repositories\MoneyPoolBlockRepositoryInterface;
use App\Repositories\MoneyPoolRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MoneyPoolBlockService extends BaseService implements MoneyPoolBlockServiceInterface
{
    private readonly MoneyPoolRepositoryInterface $moneyPoolRepository;

    public function __construct(
        MoneyPoolBlockRepositoryInterface $moneyPoolBlockRepository,
        MoneyPoolRepositoryInterface $moneyPoolRepository
    ) {
        $this->repository = $moneyPoolBlockRepository;
        $this->moneyPoolRepository = $moneyPoolRepository;
    }

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
            $block = $this->create($data);

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
            $existingBlock = $this->find($blockId);

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

            $block = $this->update($blockId, $data);

            if (! $block) {
                return null;
            }

            $this->updateMoneyPoolBlockedAmount($data['money_pool_id']);

            return $block;
        });
    }

    public function getBlocksByPoolId(int $moneyPoolId)
    {
        return $this->repository->findByPoolId($moneyPoolId);
    }

    public function deleteBlock(int $blockId)
    {
        return DB::transaction(function () use ($blockId) {
            $existingBlock = $this->find($blockId);

            if (! $existingBlock) {
                return null;
            }

            $moneyPoolId = $existingBlock->money_pool_id;

            $deleted = $this->delete($blockId);

            if (! $deleted) {
                return null;
            }

            $this->updateMoneyPoolBlockedAmount($moneyPoolId);

            return $moneyPoolId;
        });
    }

    private function getAmountDetails(array $data, bool $isUpdate, ?int $blockId = null)
    {
        $totalBlocked = $isUpdate ? $this->repository->getTotalBlockedAmountWithoutCurrentBlock($data['money_pool_id'], $blockId)
            : $this->repository->getTotalBlockedAmount($data['money_pool_id']);
        $totalAvailable = $this->moneyPoolRepository->getTotalAvailableAmount($data['money_pool_id'], $totalBlocked);

        return [
            'total_blocked' => $totalBlocked,
            'total_available' => $totalAvailable,
        ];
    }

    private function updateMoneyPoolBlockedAmount(int $moneyPoolId): void
    {
        $totalBlocked = $this->repository->getTotalBlockedAmount($moneyPoolId);
        $totalAvailable = $this->moneyPoolRepository->getTotalAvailableAmount($moneyPoolId, $totalBlocked);

        $this->moneyPoolRepository->update($moneyPoolId, [
            'blocked_amount' => $totalBlocked,
            'total_available_amount' => $totalAvailable,
        ]);
    }
}
