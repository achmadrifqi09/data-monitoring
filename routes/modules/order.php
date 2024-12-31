<?php

use App\Http\Controllers\OrderController;
use App\Models\ItemReceived;
use Illuminate\Support\Facades\Route;

Route::middleware(['permission:order_view'])->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('order.view');
    Route::get('/{id}', [OrderController::class, 'show'])->name('order.detail');
});

Route::middleware(['permission:order_create'])->group(function () {
    Route::get('/c/form', [OrderController::class, 'add'])->name('order.add');
    Route::post('/', [OrderController::class, 'store'])->name('order.store');
    Route::post('/{id}/items', [OrderController::class, 'addItem'])->name('order.item.add');
});

Route::middleware(['permission:order_update'])->group(function () {
    Route::patch('/{orderId}/items/{itemId}', [OrderController::class, 'updateItem'])->name('order.item.update');
});

Route::middleware(['permission:order_update'])->group(function () {
    Route::delete('/{id}', [OrderController::class, 'destroy'])->name('order.destroy');
    Route::delete('/{orderId}/items/{itemId}', [OrderController::class, 'destroyItem'])->name('order.item.delete');
});
