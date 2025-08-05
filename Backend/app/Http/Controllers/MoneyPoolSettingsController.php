<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMoneyPoolSettingsRequest;
use App\Http\Resources\MoneyPoolSettingsResource;
use App\Services\MoneyPoolSettingsServiceInterface;

class MoneyPoolSettingsController extends Controller
{
    protected $moneyPoolSettingsService;

    public function __construct(MoneyPoolSettingsServiceInterface $moneyPoolSettingsService)
    {
        $this->moneyPoolSettingsService = $moneyPoolSettingsService;
    }

    public function store(StoreMoneyPoolSettingsRequest $request)
    {
        try {
            $validated = $request->validated();
            $settings = $this->moneyPoolSettingsService->saveSettings($validated);

            return new MoneyPoolSettingsResource($settings);
        } catch (\Exception $e) {
            return response()->internalServerError(__('messages.error'));

        }
    }

    public function index()
    {
        try {
            $settings = $this->moneyPoolSettingsService->getSettings();

            if (! $settings) {
                return response()->notFound(__('money_pool_settings.pool_settings_not_found'));
            }

            return new MoneyPoolSettingsResource($settings);
        } catch (\Exception $e) {
            return response()->internalServerError(__('messages.error'));
        }
    }
}
