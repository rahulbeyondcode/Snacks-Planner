<?php

namespace App\Services;

use App\Repositories\MoneyPoolBlockRepositoryInterface;
use App\Repositories\MoneyPoolRepositoryInterface;

class MoneyPoolService implements MoneyPoolServiceInterface
{
    protected $moneyPoolRepository;

    protected $moneyPoolBlockRepository;

    public function __construct(
        MoneyPoolRepositoryInterface $moneyPoolRepository,
        MoneyPoolBlockRepositoryInterface $moneyPoolBlockRepository
    ) {
        $this->moneyPoolRepository = $moneyPoolRepository;
        $this->moneyPoolBlockRepository = $moneyPoolBlockRepository;
    }

    public function getCurrentMonthMoneyPool()
    {
        return $this->moneyPoolRepository->getCurrentMonthMoneyPool();
    }
}
