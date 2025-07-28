<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
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

            return (new MoneyPoolSettingsResource($settings))
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to save money pool settings', 500);
        }
    }

    public function show($id)
    {
        try {
            $settings = $this->moneyPoolSettingsService->getSettings($id);

            if (! $settings) {
                return ApiResponse::error('Money pool settings not found', 404);
            }

            return new MoneyPoolSettingsResource($settings);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to retrieve money pool settings', 500);
        }
    }

    public function latest()
    {
        try {
            $settings = $this->moneyPoolSettingsService->getLatestSettings();

            if (! $settings) {
                return ApiResponse::error('No money pool settings found', 404);
            }

            return new MoneyPoolSettingsResource($settings);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to retrieve latest money pool settings', 500);
        }
    }
}
