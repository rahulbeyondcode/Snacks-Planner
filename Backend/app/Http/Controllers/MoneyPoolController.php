<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMoneyPoolBlockRequest;
use App\Http\Requests\UpdateMoneyPoolBlockRequest;
use App\Http\Resources\MoneyPoolBlockResource;
use App\Http\Resources\MoneyPoolResource;
use App\Http\Resources\MoneyPoolSettingsResource;
use App\Services\MoneyPoolBlockServiceInterface;
use App\Services\MoneyPoolServiceInterface;
use App\Services\MoneyPoolSettingsServiceInterface;
use App\Services\ContributionServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class MoneyPoolController extends BaseController
{
    public function __construct(
        private readonly MoneyPoolServiceInterface $moneyPoolService,
        private readonly MoneyPoolBlockServiceInterface $moneyPoolBlockService,
        private readonly MoneyPoolSettingsServiceInterface $moneyPoolSettingsService,
        private readonly ContributionServiceInterface $contributionService
    ) {}

    public function index(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || !$user->role) {
            return $this->unauthorizedResponse('Unauthorized access');
        }

        $roleName = $user->role->name;

        // For account_manager: return only settings
        if ($roleName === 'account_manager') {
            $settings = $this->moneyPoolSettingsService->getSettings();

            if (!$settings) {
                $totalActiveUsers = \App\Models\User::join('roles', 'users.role_id', '=', 'roles.role_id')
                    ->where('roles.name', '!=', 'account_manager')
                    ->whereNull('users.deleted_at')
                    ->count();

                return response()->json([
                    'success' => true,
                    'message' => 'Money pool settings not found',
                    'data' => [
                        'settings' => [
                            'total_users' => $totalActiveUsers,
                        ],
                    ],
                ], 200);
            }

            return $this->successResponse('Money pool settings retrieved successfully', [
                'settings' => new MoneyPoolSettingsResource($settings)
            ]);
        }

        // For snack_manager: return full pool details except creator
        if (in_array($roleName, ['snack_manager'])) {
            $pool = $this->moneyPoolService->getCurrentMonthMoneyPool();

            if (!$pool) {
                $settings = $this->moneyPoolSettingsService->getSettings();
                return $this->successResponse('Money pool not found', [
                    'settings' => new MoneyPoolSettingsResource($settings)
                ]);
            }

            // Get contribution counts similar to getTotalContributions
            $contributionCounts = $this->contributionService->getTotalContributions();

            // Create custom response without creator details
            $poolData = [
                'money_pool_id' => $pool->money_pool_id,
                'total_collected_amount' => (float) $pool->total_collected_amount,
                'employer_contribution' => (float) $pool->employer_contribution,
                'total_pool_amount' => (float) $pool->total_pool_amount,
                'blocked_amount' => (float) $pool->blocked_amount,
                'total_available_amount' => (float) $pool->total_available_amount,
                'settings' => $pool->settings ? new MoneyPoolSettingsResource($pool->settings) : null,
                'blocks' => $pool->blocks ? MoneyPoolBlockResource::collection($pool->blocks) : [],
                'contribution_counts' => [
                    'total_paid' => $contributionCounts['total_paid'] ?? 0,
                    'total_unpaid' => $contributionCounts['total_unpaid'] ?? 0
                ]
            ];

            return $this->successResponse('Money pool retrieved successfully', $poolData);
        }

        // For other roles, deny access
        return $this->forbiddenResponse('Access denied. Insufficient permissions.');
    }

    public function storeBlock(StoreMoneyPoolBlockRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $result = $this->moneyPoolBlockService->createBlock($validated);

            // Handle error responses from service
            if (is_array($result) && isset($result['error']) && $result['error']) {
                return $this->errorResponse($result['message'], [], $result['code']);
            } elseif (! $result) {
                return $this->notFoundResponse(__('money_pool_blocks.block_not_found'));
            }

            return $this->createdResponse($this->getBlocks($result->money_pool_id), 'Money pool block created successfully');
        } catch (\Exception $e) {
            // Log the actual error for debugging
            Log::error('MoneyPoolController::storeBlock Error: ' . $e->getMessage(), [
                'exception' => $e,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return $this->errorResponse(__('messages.error'));
        }
    }

    public function updateBlock(UpdateMoneyPoolBlockRequest $request, int $blockId): JsonResponse
    {
        try {
            $validated = $request->validated();
            $result = $this->moneyPoolBlockService->updateBlock($blockId, $validated);

            // Handle error responses from service
            if (is_array($result) && isset($result['error']) && $result['error']) {
                return $this->errorResponse($result['message'], [], $result['code']);
            } elseif (! $result) {
                return $this->notFoundResponse(__('money_pool_blocks.block_not_found'));
            }

            return $this->updatedResponse($this->getBlocks($result->money_pool_id), 'Money pool block updated successfully');
        } catch (\Exception $e) {
            // Log the actual error for debugging
            Log::error('MoneyPoolController::updateBlock Error: ' . $e->getMessage(), [
                'exception' => $e,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return $this->errorResponse(__('messages.error'));
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
            $moneyPoolId = $this->moneyPoolBlockService->deleteBlock($blockId);

            if (! $moneyPoolId) {
                return $this->notFoundResponse(__('money_pool_blocks.block_not_found'));
            }

            return $this->successResponse('Money pool block deleted successfully', $this->getBlocks($moneyPoolId));
        } catch (\Exception $e) {
            return $this->errorResponse(__('messages.error'));
        }
    }
}
