<?php

namespace App\Services;

interface MoneyPoolSettingsServiceInterface
{
    public function saveSettings(array $data);

    public function getSettings();
}
