<?php

use App\Http\Controllers\ItemReceivedController;
use Illuminate\Support\Facades\Route;

Route::get('/form', [ItemReceivedController::class, 'form'])
    ->name('item-received.order.form')
    ->middleware('permission:order_create');

Route::post('/', [ItemReceivedController::class, 'store'])
    ->name('item-received.store')
    ->middleware('permission:order_create');

Route::delete('/{id}', [ItemReceivedController::class, 'destroy'])
    ->name('item-received.store')
    ->middleware('permission:order_delete');
