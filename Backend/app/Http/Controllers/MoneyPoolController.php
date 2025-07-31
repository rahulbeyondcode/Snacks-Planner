<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlockMoneyPoolRequest;
use App\Http\Resources\MoneyPoolBlockResource;
use App\Http\Resources\MoneyPoolResource;
use App\Services\MoneyPoolBlockServiceInterface;
use App\Services\MoneyPoolServiceInterface;

class MoneyPoolController extends Controller
{
    protected $moneyPoolService;

    protected $moneyPoolBlockService;

    public function __construct(
        MoneyPoolServiceInterface $moneyPoolService,
        MoneyPoolBlockServiceInterface $moneyPoolBlockService
    ) {
        $this->moneyPoolService = $moneyPoolService;
        $this->moneyPoolBlockService = $moneyPoolBlockService;
    }

    public function index()
    {
        $pool = $this->moneyPoolService->getCurrentMonthMoneyPool();

        return new MoneyPoolResource($pool);
    }

    public function block(BlockMoneyPoolRequest $request)
    {
        try {
            $validated = $request->validated();
            $block = $this->moneyPoolBlockService->blockMoneyPool($validated);

            $statusCode = isset($validated['block_id']) ? 200 : 201;
            $message = isset($validated['block_id']) ? 'Money pool block updated successfully' : 'Money pool block created successfully';

            return (new MoneyPoolBlockResource($block))
                ->response()
                ->setStatusCode($statusCode);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to process money pool block',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getBlock(int $moneyPoolId)
    {
        try {
            $blocks = $this->moneyPoolBlockService->getBlocksByPoolId($moneyPoolId);

            return MoneyPoolBlockResource::collection($blocks);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve money pool blocks',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
