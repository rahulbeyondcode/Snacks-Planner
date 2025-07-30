<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\MoneyPoolSettingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'profile']);
        Route::patch('/me', [\App\Http\Controllers\UserController::class, 'updateProfile']);

        // Account Manager routes
        Route::middleware(['role:account_manager'])->group(function () {
            // Working Days Management
            Route::get('/working-days', [\App\Http\Controllers\WorkingDayController::class, 'show']);
            Route::put('/working-days', [\App\Http\Controllers\WorkingDayController::class, 'update']);

            // Office Holidays (pill-style CRUD)
            Route::get('/office-holidays', [\App\Http\Controllers\OfficeHolidayController::class, 'index']);
            Route::post('/office-holidays', [\App\Http\Controllers\OfficeHolidayController::class, 'store']);
            Route::patch('/office-holidays/{id}', [\App\Http\Controllers\OfficeHolidayController::class, 'update']);
            Route::delete('/office-holidays/{id}', [\App\Http\Controllers\OfficeHolidayController::class, 'destroy']);

            // Money Pool Settings
            Route::post('/money-pool-settings', [MoneyPoolSettingsController::class, 'store']);
            Route::get('/money-pool-settings', [MoneyPoolSettingsController::class, 'index']);

            // User management (admin only)
            Route::get('/users', [\App\Http\Controllers\UserController::class, 'index']);
            Route::get('/users/{id}', [\App\Http\Controllers\UserController::class, 'show']);
            Route::post('/users', [\App\Http\Controllers\UserController::class, 'store']);
            Route::put('/users/{id}', [\App\Http\Controllers\UserController::class, 'update']);
            Route::delete('/users/{id}', [\App\Http\Controllers\UserController::class, 'destroy']);
            Route::patch('/users/{id}/role', [\App\Http\Controllers\UserController::class, 'assignRole']);

            // Group management (admin only)
            Route::prefix('groups')->group(function () {
                Route::get('/', [\App\Http\Controllers\GroupController::class, 'index']);
                Route::get('/{id}', [\App\Http\Controllers\GroupController::class, 'show']);
                Route::post('/', [\App\Http\Controllers\GroupController::class, 'store']);
                Route::put('/{id}', [\App\Http\Controllers\GroupController::class, 'update']);
                Route::delete('/{id}', [\App\Http\Controllers\GroupController::class, 'destroy']);
                Route::put('/update-sort-order', [\App\Http\Controllers\GroupController::class, 'setSortOrder']);
            });

            // Reporting
            Route::post('/reports/download', [\App\Http\Controllers\ReportController::class, 'download']);
        });

        // Contribution management (operation_manager and operation only)
        Route::middleware(['role:operation_manager,operation'])->group(function () {
            // Contribution status update
            Route::patch('/contributions/{id}/status', [\App\Http\Controllers\ContributionController::class, 'updateStatus']);
            // Listing all contributions
            Route::get('/contributions', [\App\Http\Controllers\ContributionController::class, 'index']);
            // Bulk update contribution status
            Route::post('/contributions/bulk-update-status', [\App\Http\Controllers\ContributionController::class, 'bulkUpdateStatus']);
        });

        // Snack Item & Shop CRUD (account_manager, operations_manager, operation)
        Route::middleware(['role:account_manager,operations_manager,operation'])->group(function () {
            // Snack Item CRUD
            Route::get('/snack-items', [\App\Http\Controllers\SnackItemController::class, 'index']);
            Route::get('/snack-items/{id}', [\App\Http\Controllers\SnackItemController::class, 'show']);
            Route::post('/snack-items', [\App\Http\Controllers\SnackItemController::class, 'store']);
            Route::put('/snack-items/{id}', [\App\Http\Controllers\SnackItemController::class, 'update']);
            Route::delete('/snack-items/{id}', [\App\Http\Controllers\SnackItemController::class, 'destroy']);

            // Shop CRUD
            Route::get('/shops', [\App\Http\Controllers\ShopController::class, 'index']);
            Route::get('/shops/{id}', [\App\Http\Controllers\ShopController::class, 'show']);
            Route::post('/shops', [\App\Http\Controllers\ShopController::class, 'store']);
            Route::put('/shops/{id}', [\App\Http\Controllers\ShopController::class, 'update']);
            Route::delete('/shops/{id}', [\App\Http\Controllers\ShopController::class, 'destroy']);
        });

        // Profit/Loss (account_manager only)
        Route::middleware(['role:account_manager'])->group(function () {
            Route::get('/profit-loss', [\App\Http\Controllers\ProfitLossController::class, 'index']);
        });

        // Operations Manager routes
        Route::middleware(['role:operations_manager'])->group(function () {
            // Weekly operations staff assignment
            Route::post('/weekly-operations', [\App\Http\Controllers\GroupWeeklyOperationController::class, 'assign']);
            Route::get('/weekly-operations', [\App\Http\Controllers\GroupWeeklyOperationController::class, 'index']);
            Route::get('/weekly-operations/{id}', [\App\Http\Controllers\GroupWeeklyOperationController::class, 'show']);

            // Money Pool Management
            Route::get('/money-pools', [MoneyPoolController::class, 'index']);
            Route::post('/money-pools/{id}/block', [MoneyPoolController::class, 'block']);
            Route::get('/money-pools/{id}/total-collected', [MoneyPoolController::class, 'totalCollected']);
            Route::get('/money-pools/{id}/total-blocked', [MoneyPoolController::class, 'totalBlocked']);
        });

        // Operations Staff routes
        Route::middleware(['role:operation'])->group(function () {
            // Update status for assigned weekly operations
            Route::patch('/weekly-operations/{id}/status', [\App\Http\Controllers\GroupWeeklyOperationController::class, 'updateStatus']);
            Route::get('/weekly-operations', [\App\Http\Controllers\GroupWeeklyOperationController::class, 'index']);
            Route::get('/weekly-operations/{id}', [\App\Http\Controllers\GroupWeeklyOperationController::class, 'show']);
        });

        // Employee routes
        Route::middleware(['role:employee'])->group(function () {
            // View own contributions
            Route::get('/my-contributions', [\App\Http\Controllers\ContributionController::class, 'myContributions']);

            // Snack suggestion endpoints
            Route::post('/snack-suggestions', [\App\Http\Controllers\SnackSuggestionController::class, 'store']);
            Route::get('/snack-suggestions', [\App\Http\Controllers\SnackSuggestionController::class, 'index']);
            // Snack rating endpoints
            Route::post('/snack-ratings', [\App\Http\Controllers\SnackRatingController::class, 'store']);
            Route::get('/snack-ratings', [\App\Http\Controllers\SnackRatingController::class, 'index']);
        });

        // Shared features (all authenticated)
        Route::get('/office-holidays', [\App\Http\Controllers\OfficeHolidayController::class, 'index']);
        Route::get('/snack-plans', [\App\Http\Controllers\SnackPlanController::class, 'index']);
        Route::post('/snack-plans', [\App\Http\Controllers\SnackPlanController::class, 'store']);
        Route::get('/snack-plans/{id}', [\App\Http\Controllers\SnackPlanController::class, 'show']);
        // Snack plan detail access
        Route::get('/snack-plan-details', [\App\Http\Controllers\SnackPlanDetailController::class, 'index']);
        Route::get('/snack-plan-details/{id}', [\App\Http\Controllers\SnackPlanDetailController::class, 'show']);
        // The following routes are only for operations_manager and operation
        Route::middleware(['role:operations_manager,operation'])->group(function () {
            Route::put('/snack-plans/{id}', [\App\Http\Controllers\SnackPlanController::class, 'update']);
            Route::delete('/snack-plans/{id}', [\App\Http\Controllers\SnackPlanController::class, 'destroy']);
            Route::patch('/snack-plan-details/{id}/receipt', [\App\Http\Controllers\SnackPlanController::class, 'uploadReceipt']);
        });
    });
});
