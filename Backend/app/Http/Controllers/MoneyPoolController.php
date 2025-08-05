<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlockMoneyPoolRequest;
use App\Http\Resources\MoneyPoolBlockResource;
use App\Http\Resources\MoneyPoolResource;
use App\Services\MoneyPoolBlockServiceInterface;
use App\Services\MoneyPoolServiceInterface;
use Illuminate\Http\JsonResponse;

class MoneyPoolController extends Controller
{
    public function __construct(
        private readonly MoneyPoolServiceInterface $moneyPoolService,
        private readonly MoneyPoolBlockServiceInterface $moneyPoolBlockService
    ) {}

    public function index(): MoneyPoolResource|JsonResponse
    {
        $pool = $this->moneyPoolService->getCurrentMonthMoneyPool();

        if (! $pool) {
            return response()->notFound(__('money_pool_settings.pool_not_found'));
        }

        return new MoneyPoolResource($pool);
    }

    public function block(BlockMoneyPoolRequest $request)
    {
        try {
            $validated = $request->validated();
            $block = $this->moneyPoolBlockService->blockMoneyPool($validated);

            if (! $block) {
                return response()->notFound(__('money_pool_blocks.block_not_found'));
            }

            return $this->getBlocks($block->money_pool_id);
        } catch (\Exception $e) {
            return response()->internalServerError(__('messages.error'));
        }
    }

    public function getBlocks(int $moneyPoolId)
    {
        $blocks = $this->moneyPoolBlockService->getBlocksByPoolId($moneyPoolId);

        return MoneyPoolBlockResource::collection($blocks);
    }

    public function deleteBlock(int $blockId): JsonResponse
    {
        try {
            $this->moneyPoolBlockService->deleteBlock($blockId);

            return response()->noContent();
        } catch (\Exception $e) {
            return response()->internalServerError(__('messages.error'));
        }
    }
}
