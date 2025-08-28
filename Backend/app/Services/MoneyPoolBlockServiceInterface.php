<?php

namespace App\Services;

interface MoneyPoolBlockServiceInterface
{
    public function createBlock(array $data);

    public function updateBlock(int $blockId, array $data);

    public function getBlocksByPoolId(int $moneyPoolId);

    public function deleteBlock(int $blockId);
}
