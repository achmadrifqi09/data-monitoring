<?php

use App\Http\Controllers\BPLController;
use App\Http\Controllers\PartnerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

Route::get('/partner', [PartnerController::class, 'getData'])
    ->name('partner.api.get')
    ->middleware('permission:partner_view');

Route::get('/bpl', [BPLController::class, 'getData'])
    ->name('bpl.api.get')
    ->middleware('permission:bpl_view');

Route::get('/item/{bpl_number}', [ItemController::class, 'getItemByBPLNumber'])
    ->name('item.api.get')
    ->middleware('permission:bpl_view');
