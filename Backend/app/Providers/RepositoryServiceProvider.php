<?php

namespace App\Providers;

use App\Repositories\SnackPlanRepository;
use App\Repositories\SnackPlanRepositoryInterface;
use App\Services\SnackPlanService;
use App\Services\SnackPlanServiceInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Repository bindings
        $this->app->bind(\App\Repositories\SnackPlanRepositoryInterface::class, \App\Repositories\SnackPlanRepository::class);
        $this->app->bind(\App\Repositories\SnackPlanDetailRepositoryInterface::class, \App\Repositories\SnackPlanDetailRepository::class);
        $this->app->bind(\App\Repositories\UserRepositoryInterface::class, \App\Repositories\UserRepository::class);
        $this->app->bind(\App\Repositories\ContributionRepositoryInterface::class, \App\Repositories\ContributionRepository::class);
        $this->app->bind(\App\Repositories\GroupRepositoryInterface::class, \App\Repositories\GroupRepository::class);
        $this->app->bind(\App\Repositories\GroupWeeklyOperationRepositoryInterface::class, \App\Repositories\GroupWeeklyOperationRepository::class);
        $this->app->bind(\App\Repositories\GroupWeeklyOperationDetailRepositoryInterface::class, \App\Repositories\GroupWeeklyOperationDetailRepository::class);
        $this->app->bind(\App\Repositories\MoneyPoolRepositoryInterface::class, \App\Repositories\MoneyPoolRepository::class);
        $this->app->bind(\App\Repositories\MoneyPoolBlockRepositoryInterface::class, \App\Repositories\MoneyPoolBlockRepository::class);
        $this->app->bind(\App\Repositories\MoneyPoolSettingsRepositoryInterface::class, \App\Repositories\MoneyPoolSettingsRepository::class);
        $this->app->bind(\App\Repositories\OfficeHolidayRepositoryInterface::class, \App\Repositories\OfficeHolidayRepository::class);
        $this->app->bind(\App\Repositories\SubGroupRepositoryInterface::class, \App\Repositories\SubGroupRepository::class);
        // Service bindings
        $this->app->bind(\App\Services\SnackPlanServiceInterface::class, \App\Services\SnackPlanService::class);
        $this->app->bind(\App\Services\UserServiceInterface::class, \App\Services\UserService::class);
        $this->app->bind(\App\Services\ContributionServiceInterface::class, \App\Services\ContributionService::class);
        $this->app->bind(\App\Services\GroupServiceInterface::class, \App\Services\GroupService::class);
        $this->app->bind(\App\Services\GroupWeeklyOperationService::class, \App\Services\GroupWeeklyOperationService::class); // No interface found
        $this->app->bind(\App\Services\MoneyPoolServiceInterface::class, \App\Services\MoneyPoolService::class);
        $this->app->bind(\App\Services\MoneyPoolSettingsServiceInterface::class, \App\Services\MoneyPoolSettingsService::class);
        $this->app->bind(\App\Services\MoneyPoolBlockServiceInterface::class, \App\Services\MoneyPoolBlockService::class);
        $this->app->bind(\App\Services\OfficeHolidayServiceInterface::class, \App\Services\OfficeHolidayService::class);
        $this->app->bind(\App\Services\ProfitLossServiceInterface::class, \App\Services\ProfitLossService::class);
        $this->app->bind(\App\Services\ReportServiceInterface::class, \App\Services\ReportService::class);
        $this->app->bind(\App\Services\SubGroupServiceInterface::class, \App\Services\SubGroupService::class);

        $this->app->bind(SnackPlanRepositoryInterface::class, SnackPlanRepository::class);
        $this->app->bind(SnackPlanServiceInterface::class, SnackPlanService::class);
        $this->app->bind(\App\Repositories\SnackPlanDetailRepositoryInterface::class, \App\Repositories\SnackPlanDetailRepository::class);
        $this->app->bind(\App\Repositories\UserRepositoryInterface::class, \App\Repositories\UserRepository::class);
        $this->app->bind(\App\Services\UserServiceInterface::class, \App\Services\UserService::class);
    }

    public function boot()
    {
        //
    }
}
