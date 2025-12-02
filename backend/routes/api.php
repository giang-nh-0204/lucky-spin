<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SpinController;
use App\Http\Controllers\Api\PrizeController;
use App\Http\Controllers\Api\Admin\AdminCodeController;
use App\Http\Controllers\Api\Admin\AdminPrizeController;
use App\Http\Controllers\Api\Admin\AdminStatsController;
use App\Http\Controllers\Api\Admin\AuthController;

/*
|--------------------------------------------------------------------------
| Public APIs (không cần auth)
|--------------------------------------------------------------------------
*/

// Admin login (public)
Route::post('/admin/login', [AuthController::class, 'login']);

// Lấy danh sách giải thưởng
Route::get('/prizes', [PrizeController::class, 'index']);

// Đổi mã code
Route::post('/redeem-code', [SpinController::class, 'redeemCode']);

/*
|--------------------------------------------------------------------------
| User APIs (cần session token)
|--------------------------------------------------------------------------
*/

Route::middleware('spin.session')->group(function () {
    // Thông tin session
    Route::get('/session', [SpinController::class, 'getSession']);

    // Quay
    Route::post('/spin/start', [SpinController::class, 'startSpin']);
    Route::post('/spin/claim/{spinToken}', [SpinController::class, 'claimResult']);

    // Lịch sử
    Route::get('/spin/history', [SpinController::class, 'history']);
});

/*
|--------------------------------------------------------------------------
| Admin APIs (cần auth admin)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Quản lý mã code
    Route::apiResource('codes', AdminCodeController::class);
    Route::post('/codes/generate-batch', [AdminCodeController::class, 'generateBatch']);

    // Quản lý giải thưởng
    Route::apiResource('prizes', AdminPrizeController::class);
    Route::post('/prizes/reorder', [AdminPrizeController::class, 'reorder']);
    Route::post('/prizes/auto-probability', [AdminPrizeController::class, 'autoDistributeProbability']);

    // Thống kê
    Route::get('/stats', [AdminStatsController::class, 'index']);
    Route::get('/stats/results', [AdminStatsController::class, 'results']);
    Route::get('/stats/codes', [AdminStatsController::class, 'codeUsage']);

    // Cập nhật trạng thái giao hàng
    Route::put('/results/{id}/delivery', [AdminStatsController::class, 'updateDeliveryStatus']);
    Route::post('/results/bulk-delivery', [AdminStatsController::class, 'bulkUpdateDeliveryStatus']);
});
