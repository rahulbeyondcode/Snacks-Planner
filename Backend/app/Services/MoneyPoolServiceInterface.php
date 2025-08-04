<?php

namespace App\Services;

interface MoneyPoolServiceInterface
{
    public function getCurrentMonthMoneyPool();

    public function find(int $id);
}
