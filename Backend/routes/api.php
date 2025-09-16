<?php

use App\Http\Controllers\MoneyPoolController;
use App\Http\Controllers\MoneyPoolSettingsController;
use App\Http\Controllers\SubGroupController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\WorkingDayController;
use App\Http\Controllers\OfficeHolidayController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\SnackItemController;
use App\Http\Controllers\ProfitLossController;
use App\Http\Controllers\GroupWeeklyOperationController;
use App\Http\Controllers\SnackPreferenceController;
use App\Http\Controllers\SnackPlanController;
use App\Http\Controllers\SnackPlanDetailController;
use App\Http\Controllers\SnackSuggestionController;
use App\Http\Controllers\SnackRatingController;
use App\Http\Controllers\NoSnacksDayController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::post('/login', [AuthController::class, 'login']);

    // Permission management routes (admin only)
    Route::middleware(['auth:sanctum', 'permission:permissions,list,account_manager'])->prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index']);
        Route::get('/module/{module}', [PermissionController::class, 'getByModule']);
        Route::post('/', [PermissionController::class, 'store']);
        Route::put('/{id}', [PermissionController::class, 'update']);
        Route::delete('/{id}', [PermissionController::class, 'destroy']);
        Route::post('/bulk-create', [PermissionController::class, 'bulkCreate']);
        Route::post('/roles/{roleId}/assign', [PermissionController::class, 'assignToRole']);
        Route::get('/roles/{roleId}', [PermissionController::class, 'getRolePermissions']);
    });

    // Protected routes with permission middleware
    Route::middleware(['auth:sanctum'])->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::patch('/reset-password', [AuthController::class, 'resetPassword']);

        // Group management with permissions
        Route::middleware(['permission:groups,list,account_manager'])->prefix('groups')->group(function () {
            Route::get('/', [GroupController::class, 'index']);
            Route::get('/{id}', [GroupController::class, 'show']);
            Route::post('/', [GroupController::class, 'store'])->middleware('permission:groups,create,account_manager');
            Route::put('/{id}', [GroupController::class, 'update'])->middleware('permission:groups,update,account_manager');
            Route::delete('/{id}', [GroupController::class, 'destroy'])->middleware('permission:groups,delete,account_manager');
            Route::patch('/{id}/leader', [GroupController::class, 'assignLeader'])->middleware('permission:groups,update,account_manager');
            Route::get('/{id}/members', [GroupController::class, 'members']);
            Route::post('/{id}/members', [GroupController::class, 'addMembers'])->middleware('permission:groups,update,account_manager');
            Route::delete('/{id}/members', [GroupController::class, 'removeMembers'])->middleware('permission:groups,update,account_manager');
            Route::post('/sort-order', [GroupController::class, 'setSortOrder'])->middleware('permission:groups,update,account_manager');
        });

        // User management with permissions
        Route::middleware(['permission:users,list,account_manager'])->prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::get('/{id}', [UserController::class, 'show']);
            Route::post('/', [UserController::class, 'store'])->middleware('permission:users,create,account_manager');
            Route::put('/{id}', [UserController::class, 'update'])->middleware('permission:users,update,account_manager');
            Route::delete('/{id}', [UserController::class, 'destroy'])->middleware('permission:users,delete,account_manager');
        });

        // Shop management with permissions
        Route::middleware(['permission:shops,list,account_manager'])->prefix('shops')->group(function () {
            Route::get('/', [ShopController::class, 'index']);
            Route::get('/{id}', [ShopController::class, 'show']);
            Route::post('/', [ShopController::class, 'store'])->middleware('permission:shops,create,account_manager');
            Route::put('/{id}', [ShopController::class, 'update'])->middleware('permission:shops,update,account_manager');
            Route::delete('/{id}', [ShopController::class, 'destroy'])->middleware('permission:shops,delete,account_manager');
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::patch('/me', [UserController::class, 'updateProfile']);

        // Lookup API - accessible to all authenticated users
        Route::get('/lookup', [LookupController::class, 'index']);

        // Account Manager routes
        Route::middleware(['role:account_manager'])->group(function () {
            // Payment Methods CRUD
            Route::get('/payment-methods', [PaymentMethodController::class, 'index']);
            Route::post('/payment-methods', [PaymentMethodController::class, 'store']);
            Route::put('/payment-methods/{id}', [PaymentMethodController::class, 'update']);
            Route::delete('/payment-methods/{id}', [PaymentMethodController::class, 'destroy']);

            // Working Days Management
            Route::get('/working-days', [WorkingDayController::class, 'show']);
            Route::put('/working-days', [WorkingDayController::class, 'update']);

            // Office Holidays (pill-style CRUD)
            Route::get('/office-holidays', [OfficeHolidayController::class, 'index']);
            Route::post('/office-holidays', [OfficeHolidayController::class, 'store']);
            Route::put('/office-holidays/{id}', [OfficeHolidayController::class, 'update']);
            Route::delete('/office-holidays/{id}', [OfficeHolidayController::class, 'destroy']);

            // Money Pool Settings
            Route::post('/money-pool-settings', [MoneyPoolSettingsController::class, 'store']);
            Route::get('/money-pool-settings', [MoneyPoolSettingsController::class, 'index']);

            // User management (admin only)
            Route::get('/users', [UserController::class, 'index']);
            Route::get('/users/{id}', [UserController::class, 'show']);
            Route::post('/users', [UserController::class, 'store']);
            Route::put('/users/{id}', [UserController::class, 'update']);
            Route::delete('/users/{id}', [UserController::class, 'destroy']);
            Route::patch('/users/{id}/role', [UserController::class, 'assignRole']);

            // Group management (admin only)
            Route::prefix('groups')->group(function () {
                Route::get('/', [GroupController::class, 'index']);
                Route::get('/{id}', [GroupController::class, 'show']);
                Route::post('/', [GroupController::class, 'store']);
                Route::put('/{id}', [GroupController::class, 'update']);
                Route::delete('/{id}', [GroupController::class, 'destroy']);
                Route::put('/update-sort-order', [GroupController::class, 'setSortOrder']);
            });

            // Reporting
            Route::post('/reports/download', [ReportController::class, 'download']);
        });

        // Contribution management (snack_manager and operation only)
        Route::middleware(['role:snack_manager,operation'])->group(function () {
            // Categories CRUD
            Route::get('/categories', [CategoryController::class, 'index']);
            Route::post('/categories', [CategoryController::class, 'store']);
            Route::put('/categories/{id}', [CategoryController::class, 'update']);
            Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
            // Contribution status update
            Route::patch('/contributions/{id}/status', [ContributionController::class, 'updateStatus']);
            // Listing all contributions
            Route::get('/contributions', [ContributionController::class, 'index']);
            // Bulk update contribution status
            Route::post('/contributions/bulk-update-status', [ContributionController::class, 'bulkUpdateStatus']);
            Route::get('/weekly-operations', [GroupWeeklyOperationController::class, 'index']);
            Route::get('/weekly-operations/{id}', [GroupWeeklyOperationController::class, 'show']);
        });

        // Snack Item & Shop CRUD (account_manager, snack_manager, operation)
        Route::middleware(['role:account_manager,snack_manager,operation'])->group(function () {
            // Snack Item CRUD
            Route::get('/snack-items', [SnackItemController::class, 'index']);
            Route::get('/snack-items/{id}', [SnackItemController::class, 'show']);
            Route::post('/snack-items', [SnackItemController::class, 'store']);
            Route::put('/snack-items/{id}', [SnackItemController::class, 'update']);
            Route::delete('/snack-items/{id}', [SnackItemController::class, 'destroy']);
            Route::get('/get-snacks', [SnackItemController::class, 'getSnacks']);

            // Shop CRUD
            Route::get('/shops', [ShopController::class, 'index']);
            Route::get('/shops/{id}', [ShopController::class, 'show']);
            Route::post('/shops', [ShopController::class, 'store']);
            Route::put('/shops/{id}', [ShopController::class, 'update']);
            Route::delete('/shops/{id}', [ShopController::class, 'destroy']);
        });

        // Profit/Loss (account_manager only)
        Route::middleware(['role:account_manager'])->group(function () {
            Route::get('/profit-loss', [ProfitLossController::class, 'index']);
        });

        // Money Pool Access (account_manager and snack_manager only)
        Route::middleware(['role:account_manager,snack_manager'])->group(function () {
            // Money Pool Management
            Route::get('/money-pool', [MoneyPoolController::class, 'index']);
        });

        // Snack Manager routes
        Route::middleware(['role:snack_manager'])->group(function () {
            // Weekly operations staff assignment
            Route::post('/weekly-operations', [GroupWeeklyOperationController::class, 'assign']);

            // Money Pool Blocks
            Route::post('/money-pool-blocks', [MoneyPoolController::class, 'storeBlock']);
            Route::put('/money-pool-blocks/{blockId}', [MoneyPoolController::class, 'updateBlock']);
            Route::delete('/money-pool-blocks/{blockId}', [MoneyPoolController::class, 'deleteBlock']);

            // Sub Group management with permissions
            Route::get('/sub-groups', [SubGroupController::class, 'index']);
            Route::get('/sub-groups/{id}', [SubGroupController::class, 'show']);
            Route::post('/sub-groups', [SubGroupController::class, 'store']);
            Route::put('/sub-groups/{id}', [SubGroupController::class, 'update']);
            Route::delete('/sub-groups/{id}', [SubGroupController::class, 'destroy']);

            // No Snacks Day Management
            Route::get('/no-snacks-days', [NoSnacksDayController::class, 'index']);
            Route::post('/no-snacks-days', [NoSnacksDayController::class, 'store']);
            Route::put('/no-snacks-days/{id}', [NoSnacksDayController::class, 'update']);
            Route::delete('/no-snacks-days/{id}', [NoSnacksDayController::class, 'destroy']);
        });

        // Operations Staff routes
        Route::middleware(['role:operation'])->group(function () {
            // Update status for assigned weekly operations
            Route::patch('/weekly-operations/{id}/status', [GroupWeeklyOperationController::class, 'updateStatus']);
        });

        // Employee routes
        Route::middleware(['role:employee'])->group(function () {
            // View own contributions
            Route::get('/my-contributions', [ContributionController::class, 'myContributions']);

            // Snack suggestion endpoints
            Route::post('/snack-suggestions', [SnackSuggestionController::class, 'store']);
            Route::get('/snack-suggestions', [SnackSuggestionController::class, 'index']);
            // Snack rating endpoints
            Route::post('/snack-ratings', [SnackRatingController::class, 'store']);
            Route::get('/snack-ratings', [SnackRatingController::class, 'index']);
        });


        Route::middleware(['role:snack_manager,operation,account_manager'])->group(function () {
            // Snack Preference Management (all roles except account_manager)
            Route::get('/snack-preferences', [SnackPreferenceController::class, 'index']);
            Route::put('/snack-preferences', [SnackPreferenceController::class, 'update']);
            Route::get('/snack-plans', [SnackPlanController::class, 'index']);
            Route::post('/snack-plans', [SnackPlanController::class, 'store']);
            Route::get('/snack-plans/{id}', [SnackPlanController::class, 'show']);
            // Snack plan detail access
            Route::get('/snack-plan-details', [SnackPlanDetailController::class, 'index']);
            Route::get('/snack-plan-details/{id}', [SnackPlanDetailController::class, 'show']);
            Route::put('/snack-plans/{id}', [SnackPlanController::class, 'update']);
            Route::delete('/snack-plans/{id}', [SnackPlanController::class, 'destroy']);
            Route::patch('/snack-plan-details/{id}/receipt', [SnackPlanController::class, 'uploadReceipt']);
        });
    });
});
