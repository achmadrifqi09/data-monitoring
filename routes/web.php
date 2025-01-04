<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/login', [AuthController::class, 'index'])->name('login.view');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware(['auth', 'prevent_back_history'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:view_dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::prefix('bpl')->group(function () {
        require __DIR__ . '/modules/BPL.php';
    });

    Route::prefix('partner')->group(function () {
        require __DIR__ . '/modules/partner.php';
    });

    Route::prefix('order')->group(function () {
        require __DIR__ . '/modules/order.php';
    });

    Route::prefix('control')->group(function () {
        require __DIR__ . '/modules/control.php';
    });

    Route::prefix('item-received')->group(function () {
        require __DIR__ . '/modules/item-received.php';
    });

    Route::prefix('api')->group(function () {
        require __DIR__ . '/modules/api.php';
    });

    Route::prefix('document')->group(function () {
        require __DIR__ . '/modules/document.php';
    });
});
