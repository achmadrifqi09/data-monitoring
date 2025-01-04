<?php

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/order', [DocumentController::class, 'orderDocument'])
    ->name('document.order')
    ->middleware('permission:order_view');
