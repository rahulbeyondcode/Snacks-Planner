<?php

namespace App\Repositories;

interface MoneyPoolSettingsRepositoryInterface
{
    public function create(array $data);

    public function find(int $id);

    public function update(int $id, array $data);

    public function isUsedInMoneyPools(int $id): bool;

    public function isUsedInMoneyPoolBlocks(int $id): bool;

    public function getLatestSettings();
}
