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

            $amountDetails = $this->getAmountDetails($data, $isUpdate);

            if ($amountDetails['total_available'] < $data['amount']) {
                return response()->unprocessableEntity(__('money_pool_blocks.block_not_enough_amount'));
            }

            if ($isUpdate) {
                $block = $this->moneyPoolBlockRepository->update($data['block_id'], $data);
            } else {
                $data['created_by'] = Auth::id();
                $block = $this->moneyPoolBlockRepository->create($data);
            }

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

    private function getAmountDetails(array $data, bool $isUpdate)
    {
        $totalBlocked = $isUpdate ? $this->moneyPoolBlockRepository->getTotalBlockedAmountWithoutCurrentBlock($data['money_pool_id'], $data['block_id'])
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
