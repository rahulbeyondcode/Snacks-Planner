<?php

use App\Http\Controllers\MoneyPoolController;
use App\Http\Controllers\MoneyPoolSettingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/profile', [App\Http\Controllers\AuthController::class, 'profile'])->middleware('auth:sanctum');

    // Permission management routes (admin only)
    Route::middleware(['auth:sanctum', 'permission:permissions,list,account_manager'])->prefix('permissions')->group(function () {
        Route::get('/', [App\Http\Controllers\PermissionController::class, 'index']);
        Route::get('/module/{module}', [App\Http\Controllers\PermissionController::class, 'getByModule']);
        Route::post('/', [App\Http\Controllers\PermissionController::class, 'store']);
        Route::put('/{id}', [App\Http\Controllers\PermissionController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\PermissionController::class, 'destroy']);
        Route::post('/bulk-create', [App\Http\Controllers\PermissionController::class, 'bulkCreate']);
        Route::post('/roles/{roleId}/assign', [App\Http\Controllers\PermissionController::class, 'assignToRole']);
        Route::get('/roles/{roleId}', [App\Http\Controllers\PermissionController::class, 'getRolePermissions']);
    });

    // Protected routes with permission middleware
    Route::middleware(['auth:sanctum'])->group(function () {

        // Group management with permissions
        Route::middleware(['permission:groups,list,account_manager'])->prefix('groups')->group(function () {
            Route::get('/', [App\Http\Controllers\GroupController::class, 'index']);
            Route::get('/{id}', [App\Http\Controllers\GroupController::class, 'show']);
            Route::post('/', [App\Http\Controllers\GroupController::class, 'store'])->middleware('permission:groups,create,account_manager');
            Route::put('/{id}', [App\Http\Controllers\GroupController::class, 'update'])->middleware('permission:groups,update,account_manager');
            Route::delete('/{id}', [App\Http\Controllers\GroupController::class, 'destroy'])->middleware('permission:groups,delete,account_manager');
            Route::patch('/{id}/leader', [App\Http\Controllers\GroupController::class, 'assignLeader'])->middleware('permission:groups,update,account_manager');
            Route::get('/{id}/members', [App\Http\Controllers\GroupController::class, 'members']);
            Route::post('/{id}/members', [App\Http\Controllers\GroupController::class, 'addMembers'])->middleware('permission:groups,update,account_manager');
            Route::delete('/{id}/members', [App\Http\Controllers\GroupController::class, 'removeMembers'])->middleware('permission:groups,update,account_manager');
            Route::post('/sort-order', [App\Http\Controllers\GroupController::class, 'setSortOrder'])->middleware('permission:groups,update,account_manager');
        });

        // User management with permissions
        Route::middleware(['permission:users,list,account_manager'])->prefix('users')->group(function () {
            Route::get('/', [App\Http\Controllers\UserController::class, 'index']);
            Route::get('/{id}', [App\Http\Controllers\UserController::class, 'show']);
            Route::post('/', [App\Http\Controllers\UserController::class, 'store'])->middleware('permission:users,create,account_manager');
            Route::put('/{id}', [App\Http\Controllers\UserController::class, 'update'])->middleware('permission:users,update,account_manager');
            Route::delete('/{id}', [App\Http\Controllers\UserController::class, 'destroy'])->middleware('permission:users,delete,account_manager');
        });

        // Shop management with permissions
        Route::middleware(['permission:shops,list,account_manager'])->prefix('shops')->group(function () {
            Route::get('/', [App\Http\Controllers\ShopController::class, 'index']);
            Route::get('/{id}', [App\Http\Controllers\ShopController::class, 'show']);
            Route::post('/', [App\Http\Controllers\ShopController::class, 'store'])->middleware('permission:shops,create,account_manager');
            Route::put('/{id}', [App\Http\Controllers\ShopController::class, 'update'])->middleware('permission:shops,update,account_manager');
            Route::delete('/{id}', [App\Http\Controllers\ShopController::class, 'destroy'])->middleware('permission:shops,delete,account_manager');
        });

        // Add other module routes with similar permission structure...
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::patch('/me', [\App\Http\Controllers\UserController::class, 'updateProfile']);

        // Account Manager routes
        Route::middleware(['role:account_manager'])->group(function () {
            // Payment Methods CRUD
            Route::get('/payment-methods', [\App\Http\Controllers\PaymentMethodController::class, 'index']);
            Route::post('/payment-methods', [\App\Http\Controllers\PaymentMethodController::class, 'store']);
            Route::put('/payment-methods/{id}', [\App\Http\Controllers\PaymentMethodController::class, 'update']);
            Route::delete('/payment-methods/{id}', [\App\Http\Controllers\PaymentMethodController::class, 'destroy']);


            // Working Days Management
            Route::get('/working-days', [\App\Http\Controllers\WorkingDayController::class, 'show']);
            Route::put('/working-days', [\App\Http\Controllers\WorkingDayController::class, 'update']);

            // Office Holidays (pill-style CRUD)
            Route::get('/office-holidays', [\App\Http\Controllers\OfficeHolidayController::class, 'index']);
            Route::post('/office-holidays', [\App\Http\Controllers\OfficeHolidayController::class, 'store']);
            Route::put('/office-holidays/{id}', [\App\Http\Controllers\OfficeHolidayController::class, 'update']);
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

        // Contribution management (snack_manager and operation only)
        Route::middleware(['role:snack_manager,operation'])->group(function () {
            // Categories CRUD
            Route::get('/categories', [\App\Http\Controllers\CategoryController::class, 'index']);
            Route::post('/categories', [\App\Http\Controllers\CategoryController::class, 'store']);
            Route::put('/categories/{id}', [\App\Http\Controllers\CategoryController::class, 'update']);
            Route::delete('/categories/{id}', [\App\Http\Controllers\CategoryController::class, 'destroy']);
            // Contribution status update
            Route::patch('/contributions/{id}/status', [\App\Http\Controllers\ContributionController::class, 'updateStatus']);
            // Listing all contributions
            Route::get('/contributions', [\App\Http\Controllers\ContributionController::class, 'index']);
            // Bulk update contribution status
            Route::post('/contributions/bulk-update-status', [\App\Http\Controllers\ContributionController::class, 'bulkUpdateStatus']);
        });

        // Snack Item & Shop CRUD (account_manager, operations_manager, operation)
        Route::middleware(['role:account_manager,snack_manager,operation'])->group(function () {
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
        Route::middleware(['role:snack_manager'])->group(function () {
            // Weekly operations staff assignment
            Route::post('/weekly-operations', [\App\Http\Controllers\GroupWeeklyOperationController::class, 'assign']);
            Route::get('/weekly-operations', [\App\Http\Controllers\GroupWeeklyOperationController::class, 'index']);
            Route::get('/weekly-operations/{id}', [\App\Http\Controllers\GroupWeeklyOperationController::class, 'show']);

            // Money Pool Management
            Route::get('/money-pools', [MoneyPoolController::class, 'index']);

            // Money Pool Blocks
            Route::post('/money-pool-blocks', [MoneyPoolController::class, 'block']);
            Route::delete('/money-pool-blocks/{blockId}', [MoneyPoolController::class, 'deleteBlock']);
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

        // Snack Manager routes
        Route::middleware(['role:snack_manager'])->group(function () {
            // No Snacks Day Management
            Route::get('/no-snacks-days', [\App\Http\Controllers\NoSnacksDayController::class, 'index']);
            Route::post('/no-snacks-days', [\App\Http\Controllers\NoSnacksDayController::class, 'store']);
            Route::put('/no-snacks-days/{id}', [\App\Http\Controllers\NoSnacksDayController::class, 'update']);
            Route::delete('/no-snacks-days/{id}', [\App\Http\Controllers\NoSnacksDayController::class, 'destroy']);
        });

        // Shared features (all authenticated)
        Route::get('/snack-plans', [\App\Http\Controllers\SnackPlanController::class, 'index']);
        Route::post('/snack-plans', [\App\Http\Controllers\SnackPlanController::class, 'store']);
        Route::get('/snack-plans/{id}', [\App\Http\Controllers\SnackPlanController::class, 'show']);
        // Snack plan detail access
        Route::get('/snack-plan-details', [\App\Http\Controllers\SnackPlanDetailController::class, 'index']);
        Route::get('/snack-plan-details/{id}', [\App\Http\Controllers\SnackPlanDetailController::class, 'show']);
        // The following routes are only for operations_manager and operation
        Route::middleware(['role:snack_manager,operation'])->group(function () {
            Route::put('/snack-plans/{id}', [\App\Http\Controllers\SnackPlanController::class, 'update']);
            Route::delete('/snack-plans/{id}', [\App\Http\Controllers\SnackPlanController::class, 'destroy']);
            Route::patch('/snack-plan-details/{id}/receipt', [\App\Http\Controllers\SnackPlanController::class, 'uploadReceipt']);
        });
    });
});
