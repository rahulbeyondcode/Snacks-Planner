<?php

namespace App\Repositories;

interface MoneyPoolBlockRepositoryInterface
{
    public function create(array $data);

    public function update(int $id, array $data);

    public function findByPoolId(int $moneyPoolId);

    public function getTotalBlockedAmount(int $moneyPoolId): float;

    public function find(int $id);

    public function delete(int $moneyPoolId);
}
