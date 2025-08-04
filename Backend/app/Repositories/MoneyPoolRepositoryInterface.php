<?php

namespace App\Repositories;

interface MoneyPoolRepositoryInterface
{
    public function getCurrentMonthMoneyPool();

    public function find(int $id);
}
