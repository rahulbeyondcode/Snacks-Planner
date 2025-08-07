<?php

namespace App\Services;

interface MoneyPoolBlockServiceInterface
{
    public function blockMoneyPool(array $data);

    public function getBlocksByPoolId(int $moneyPoolId);

    public function deleteBlock(int $blockId);
}
