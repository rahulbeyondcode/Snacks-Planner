<?php

namespace App\Repositories;

interface MoneyPoolRepositoryInterface
{
    public function getCurrentMonthMoneyPool();

    public function find(int $id);

    public function update(int $id, array $data);

    public function getTotalAvailableAmount(int $moneyPoolId, float $totalBlocked): float;
}
