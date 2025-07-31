<?php

namespace App\Services;

use App\Repositories\MoneyPoolBlockRepositoryInterface;
use App\Repositories\MoneyPoolRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MoneyPoolBlockService implements MoneyPoolBlockServiceInterface
{
    protected $moneyPoolBlockRepository;

    protected $moneyPoolRepository;

    public function __construct(
        MoneyPoolBlockRepositoryInterface $moneyPoolBlockRepository,
        MoneyPoolRepositoryInterface $moneyPoolRepository
    ) {
        $this->moneyPoolBlockRepository = $moneyPoolBlockRepository;
        $this->moneyPoolRepository = $moneyPoolRepository;
    }

    public function blockMoneyPool(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Check if this is an update operation
            if (isset($data['block_id'])) {
                // Update existing block
                $block = $this->moneyPoolBlockRepository->update($data['block_id'], $data);
            } else {
                // Add the authenticated user as creator for new blocks
                $data['created_by'] = Auth::id();

                // Create new money pool block
                $block = $this->moneyPoolBlockRepository->create($data);
            }

            // Update the money pool's blocked amount
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
        return $this->moneyPoolBlockRepository->find($id);
    }

    protected function updateMoneyPoolBlockedAmount(int $moneyPoolId)
    {
        $totalBlocked = $this->moneyPoolBlockRepository->getTotalBlockedAmount($moneyPoolId);

        // Get the money pool by ID using repository
        $moneyPool = $this->moneyPoolRepository->find($moneyPoolId);

        if ($moneyPool) {
            // Update blocked amount
            $moneyPool->blocked_amount = $totalBlocked;

            // Recalculate total available amount
            $moneyPool->total_available_amount = $moneyPool->total_pool_amount - $totalBlocked;

            $moneyPool->save();
        }
    }

    public function deleteBlock(int $moneyPoolId)
    {
        return $this->moneyPoolBlockRepository->delete($moneyPoolId);
    }
}
