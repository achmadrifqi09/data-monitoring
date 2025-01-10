<?php

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/order', DocumentController::class)
    ->name('document.order')
    ->middleware('permission:order_view');

Route::get('/bill', DocumentController::class)
    ->name('document.bill')
    ->middleware('permission:bill_view');

Route::get('/payment/proof', DocumentController::class)
    ->name('document.bill')
    ->middleware('permission:bill_view');
