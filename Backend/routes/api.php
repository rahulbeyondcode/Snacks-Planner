<?php

use Illuminate\Support\Facades\Route;

// AUTHENTICATION
Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('logout', [\App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('me', [\App\Http\Controllers\AuthController::class, 'me'])->middleware('auth:sanctum');
Route::get('my-roles', [\App\Http\Controllers\UserController::class, 'getRoles'])->middleware('auth:sanctum');
Route::get('checkrole-admin', [\App\Http\Controllers\UserController::class, 'checkRoleAdmin'])->middleware('auth:sanctum');


// USERS (CRUD & ROLE ASSIGNMENT)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('users', \App\Http\Controllers\UserController::class);
    Route::post('users/{user}/assign-role', [\App\Http\Controllers\UserController::class, 'assignRole']);
});

// EMPLOYEE CONTRIBUTIONS
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('contributions', [\App\Http\Controllers\ContributionController::class, 'index']);
    Route::get('contributions/search', [\App\Http\Controllers\ContributionController::class, 'search']);
    Route::get('contributions/summary', [\App\Http\Controllers\ContributionController::class, 'summary']);
    Route::patch('contributions/{contribution}/mark-paid', [\App\Http\Controllers\ContributionController::class, 'markPaid']);
    Route::patch('contributions/{contribution}/mark-unpaid', [\App\Http\Controllers\ContributionController::class, 'markUnpaid']);
});

// MONEY POOL SETUP
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('money-pool', [\App\Http\Controllers\FundController::class, 'index']);
    Route::post('money-pool', [\App\Http\Controllers\FundController::class, 'store']);
    Route::patch('money-pool/{fund}', [\App\Http\Controllers\FundController::class, 'update']);
    Route::get('money-pool/balance', [\App\Http\Controllers\FundController::class, 'balance']);
});

// SNACK PLANNING
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('snack-plans', \App\Http\Controllers\SnackPlanController::class);
    Route::post('snack-plans/{snack_plan}/upload-receipt', [\App\Http\Controllers\SnackPlanController::class, 'uploadReceipt']);
    Route::get('snack-plans/{snack_plan}/profit-loss', [\App\Http\Controllers\SnackPlanController::class, 'profitLoss']);
});

// HOLIDAY MANAGEMENT
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('holidays', \App\Http\Controllers\HolidayController::class);
});

// SNACK ITEMS MASTERLIST
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('snack-items', \App\Http\Controllers\SnackItemController::class);
});

// SHOPS MASTERLIST
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('shops', \App\Http\Controllers\ShopController::class);
});

// REPORTS
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('reports/summary', [\App\Http\Controllers\ReportController::class, 'summary']);
    Route::get('reports/receipts', [\App\Http\Controllers\ReportController::class, 'receipts']);
    Route::get('reports/export/pdf', [\App\Http\Controllers\ReportController::class, 'exportPdf']);
    Route::get('reports/export/xls', [\App\Http\Controllers\ReportController::class, 'exportXls']);
});

// FUND BLOCKING (SPECIAL EVENTS)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('fund-block', [\App\Http\Controllers\FundBlockController::class, 'block']);
    Route::delete('fund-block/{id}', [\App\Http\Controllers\FundBlockController::class, 'unblock']);
});
