<?php

namespace App\Repositories;

interface MoneyPoolBlockRepositoryInterface
{
    public function create(array $data);
    public function findByPoolId(int $moneyPoolId);
}
