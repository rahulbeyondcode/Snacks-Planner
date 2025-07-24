<?php

namespace App\Http\Controllers;

use App\Services\MoneyPoolServiceInterface;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMoneyPoolRequest;
use App\Http\Requests\BlockMoneyPoolRequest;
use App\Http\Resources\MoneyPoolResource;
use App\Http\Resources\MoneyPoolBlockResource;
use App\Http\Requests\ListMoneyPoolRequest;
use App\Http\Resources\MoneyPoolCollection;

class MoneyPoolController extends Controller
{
    public function index(ListMoneyPoolRequest $request)
    {
        $filters = $request->validated();
        $pools = $this->moneyPoolService->listPools($filters);
        return new MoneyPoolCollection($pools);
    }

    protected $moneyPoolService;

    public function __construct(MoneyPoolServiceInterface $moneyPoolService)
    {
        $this->moneyPoolService = $moneyPoolService;
    }

    public function store(StoreMoneyPoolRequest $request)
    {
        $validated = $request->validated();
        $pool = $this->moneyPoolService->createPool($validated);
        return (new MoneyPoolResource($pool))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $pool = $this->moneyPoolService->getPool($id);
        if (!$pool) {
            return response()->json(['message' => 'Money Pool not found'], 404);
        }
        return new MoneyPoolResource($pool);
    }

    public function block(BlockMoneyPoolRequest $request, $moneyPoolId)
    {
        $validated = $request->validated();
        $block = $this->moneyPoolService->blockAmount($moneyPoolId, $validated);
        return (new MoneyPoolBlockResource($block))->response()->setStatusCode(201);
    }

    public function totalCollected($moneyPoolId)
    {
        $total = $this->moneyPoolService->getTotalCollected($moneyPoolId);
        return response()->json(['total_collected' => $total]);
    }

    public function totalBlocked($moneyPoolId)
    {
        $total = $this->moneyPoolService->getTotalBlocked($moneyPoolId);
        return response()->json(['total_blocked' => $total]);
    }
}
