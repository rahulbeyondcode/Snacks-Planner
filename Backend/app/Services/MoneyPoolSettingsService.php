<?php

namespace App\Services;

use App\Repositories\MoneyPoolSettingsRepositoryInterface;

class MoneyPoolSettingsService extends BaseService implements MoneyPoolSettingsServiceInterface
{
    public function __construct(MoneyPoolSettingsRepositoryInterface $moneyPoolSettingsRepository)
    {
        $this->repository = $moneyPoolSettingsRepository;
    }

    public function saveSettings(array $data)
    {
        // If no settings exist, create a new entry
        $latestSettings = $this->repository->getLatestSettings();

        if (! $latestSettings) {
            return $this->create($data);
        }

        $settingsId = $latestSettings->money_pool_setting_id;

        // Check if the settings are used in money_pools or money_pool_blocks tables
        $isUsedInMoneyPools = $this->repository->isUsedInMoneyPools($settingsId);
        $isUsedInMoneyPoolBlocks = $this->repository->isUsedInMoneyPoolBlocks($settingsId);

        // If settings are already used, create a new row
        if ($isUsedInMoneyPools || $isUsedInMoneyPoolBlocks) {
            $this->repository->destroyMoneyPoolSettings($settingsId);

            return $this->create($data);
        }

        // If settings are not used, update the existing entry
        return $this->update($settingsId, $data);
    }

    public function getSettings()
    {
        return $this->repository->getLatestSettings();
    }
}
