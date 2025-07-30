<?php

namespace App\Services;

use App\Repositories\MoneyPoolSettingsRepositoryInterface;

class MoneyPoolSettingsService implements MoneyPoolSettingsServiceInterface
{
    protected $moneyPoolSettingsRepository;

    public function __construct(MoneyPoolSettingsRepositoryInterface $moneyPoolSettingsRepository)
    {
        $this->moneyPoolSettingsRepository = $moneyPoolSettingsRepository;
    }

    public function saveSettings(array $data)
    {
        // If no settings exist, create a new entry
        $latestSettings = $this->moneyPoolSettingsRepository->getLatestSettings();

        if (! $latestSettings) {
            return $this->moneyPoolSettingsRepository->create($data);
        }

        $settingsId = $latestSettings->money_pool_setting_id;

        // Check if the settings are used in money_pools or money_pool_blocks tables
        $isUsedInMoneyPools = $this->moneyPoolSettingsRepository->isUsedInMoneyPools($settingsId);
        $isUsedInMoneyPoolBlocks = $this->moneyPoolSettingsRepository->isUsedInMoneyPoolBlocks($settingsId);

        // If settings are already used, create a new row
        if ($isUsedInMoneyPools || $isUsedInMoneyPoolBlocks) {
            $this->moneyPoolSettingsRepository->destroyMoneyPoolSettings($settingsId);

            return $this->moneyPoolSettingsRepository->create($data);
        }

        // If settings are not used, update the existing entry
        return $this->moneyPoolSettingsRepository->update($settingsId, $data);
    }

    public function getSettings()
    {
        return $this->moneyPoolSettingsRepository->getLatestSettings();
    }
}
