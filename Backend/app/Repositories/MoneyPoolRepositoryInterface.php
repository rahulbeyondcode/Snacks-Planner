<?php

namespace App\Repositories;

interface MoneyPoolRepositoryInterface
{
    public function create(array $data);
    public function find(int $id);
    public function update(int $id, array $data);
    public function getTotalCollected(int $moneyPoolId): float;
    public function getTotalBlocked(int $moneyPoolId): float;
    public function query();
}
