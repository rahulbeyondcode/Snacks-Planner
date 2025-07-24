<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\SnackPlanRepositoryInterface;
use App\Repositories\SnackPlanRepository;
use App\Services\SnackPlanServiceInterface;
use App\Services\SnackPlanService;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(SnackPlanRepositoryInterface::class, SnackPlanRepository::class);
        $this->app->bind(SnackPlanServiceInterface::class, SnackPlanService::class);
        $this->app->bind(\App\Repositories\SnackPlanDetailRepositoryInterface::class, \App\Repositories\SnackPlanDetailRepository::class);
    }

    public function boot()
    {
        //
    }
}
