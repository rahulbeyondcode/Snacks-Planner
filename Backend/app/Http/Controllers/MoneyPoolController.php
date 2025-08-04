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

    public function index(): MoneyPoolResource
    {
        $pool = $this->moneyPoolService->getCurrentMonthMoneyPool();

        if (! $pool) {
            return response()->notFound(__('money_pool_settings.pool_not_found'));
        }

        return new MoneyPoolResource($pool);
    }

    public function block(BlockMoneyPoolRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $block = $this->moneyPoolBlockService->blockMoneyPool($validated);

            $blocks = $this->getBlocks($block->money_pool_id);

            return MoneyPoolBlockResource::collection($blocks);
        } catch (\Exception $e) {
            return response()->internalServerError(__('messages.error'));
        }
    }

    public function getBlocks(int $moneyPoolId): JsonResponse
    {
        return $this->moneyPoolBlockService->getBlocksByPoolId($moneyPoolId);
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
