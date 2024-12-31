<?php

use App\Http\Controllers\ItemReceivedController;
use Illuminate\Support\Facades\Route;

Route::post('/', [ItemReceivedController::class, 'store'])
    ->name('item-received.store')
    ->middleware('permission:order_create');

Route::delete('/{id}', [ItemReceivedController::class, 'destroy'])
    ->name('item-received.store')
    ->middleware('permission:order_delete');
