<?php

namespace App\Services;

use App\Repositories\MoneyPoolBlockRepositoryInterface;
use App\Repositories\MoneyPoolRepositoryInterface;

class MoneyPoolService extends BaseService implements MoneyPoolServiceInterface
{
    protected $moneyPoolBlockRepository;

    public function __construct(
        MoneyPoolRepositoryInterface $moneyPoolRepository,
        MoneyPoolBlockRepositoryInterface $moneyPoolBlockRepository
    ) {
        $this->repository = $moneyPoolRepository;
        $this->moneyPoolBlockRepository = $moneyPoolBlockRepository;
    }

    public function getCurrentMonthMoneyPool()
    {
        return $this->repository->getCurrentMonthMoneyPool();
    }
}
