<?php

namespace App\Http\Controllers;

use App\Http\Resources\MoneyPoolResource;
use App\Services\MoneyPoolServiceInterface;

class MoneyPoolController extends Controller
{
    protected $moneyPoolService;

    public function __construct(MoneyPoolServiceInterface $moneyPoolService)
    {
        $this->moneyPoolService = $moneyPoolService;
    }

    public function index()
    {
        $pool = $this->moneyPoolService->getCurrentMonthMoneyPool();

        return new MoneyPoolResource($pool);
    }

    public function poolBlocks()
    {
        $blocks = $this->moneyPoolService->getPoolBlocks();

        return new MoneyPoolBlockCollection($blocks);
    }
}
