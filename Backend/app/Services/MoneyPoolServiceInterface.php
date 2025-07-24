<?php

namespace App\Services;

interface MoneyPoolServiceInterface
{
    public function createPool(array $data);
    public function getPool(int $id);
    public function blockAmount(int $moneyPoolId, array $blockData);
    public function getTotalCollected(int $moneyPoolId): float;
    public function getTotalBlocked(int $moneyPoolId): float;
    public function listPools(array $filters = []);
}
