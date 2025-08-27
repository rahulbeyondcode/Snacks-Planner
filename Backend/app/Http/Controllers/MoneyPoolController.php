<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlockMoneyPoolRequest;
use App\Http\Resources\MoneyPoolBlockResource;
use App\Http\Resources\MoneyPoolResource;
use App\Http\Resources\MoneyPoolSettingsResource;
use App\Services\MoneyPoolBlockServiceInterface;
use App\Services\MoneyPoolServiceInterface;
use App\Services\MoneyPoolSettingsServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MoneyPoolController extends Controller
{
    public function __construct(
        private readonly MoneyPoolServiceInterface $moneyPoolService,
        private readonly MoneyPoolBlockServiceInterface $moneyPoolBlockService,
        private readonly MoneyPoolSettingsServiceInterface $moneyPoolSettingsService
    ) {}

    public function index(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || !$user->role) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
                'data' => []
            ], 401);
        }

        $roleName = $user->role->name;

        // For account_manager: return only settings
        if ($roleName === 'account_manager') {
            $settings = $this->moneyPoolSettingsService->getSettings();

            if (!$settings) {
                return response()->json([
                    'success' => false,
                    'message' => 'Money pool settings not found',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Money pool settings retrieved successfully',
                'data' => [
                    'settings' => new MoneyPoolSettingsResource($settings)
                ]
            ]);
        }

        // For snack_manager and operation: return full pool details except creator
        if (in_array($roleName, ['snack_manager', 'operation'])) {
            $pool = $this->moneyPoolService->getCurrentMonthMoneyPool();

            if (!$pool) {
                return response()->json([
                    'success' => false,
                    'message' => 'Money pool not found',
                    'data' => []
                ], 404);
            }

            // Create custom response without creator details
            $poolData = [
                'money_pool_id' => $pool->money_pool_id,
                'total_collected_amount' => (float) $pool->total_collected_amount,
                'employer_contribution' => (float) $pool->employer_contribution,
                'total_pool_amount' => (float) $pool->total_pool_amount,
                'blocked_amount' => (float) $pool->blocked_amount,
                'total_available_amount' => (float) $pool->total_available_amount,
                'settings' => $pool->settings ? new MoneyPoolSettingsResource($pool->settings) : null,
                'blocks' => $pool->blocks ? MoneyPoolBlockResource::collection($pool->blocks) : []
            ];

            return response()->json([
                'success' => true,
                'message' => 'Money pool retrieved successfully',
                'data' => $poolData
            ]);
        }

        // For other roles, deny access
        return response()->json([
            'success' => false,
            'message' => 'Access denied. Insufficient permissions.',
            'data' => []
        ], 403);
    }

    public function block(BlockMoneyPoolRequest $request)
    {
        try {
            $validated = $request->validated();
            $block = $this->moneyPoolBlockService->blockMoneyPool($validated);

            if ($block instanceof JsonResponse && $block->getStatusCode() == 422) {
                return response()->unprocessableEntity($block->getData()->message);
            } elseif (! $block) {
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
            if (! $this->moneyPoolBlockService->deleteBlock($blockId)) {
                return response()->notFound(__('money_pool_blocks.block_not_found'));
            }

            return response()->noContent();
        } catch (\Exception $e) {
            return response()->internalServerError(__('messages.error'));
        }
    }
}
