<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\BPLController;
use App\Http\Controllers\PartnerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemReceivedController;
use App\Http\Controllers\OrderController;

Route::get('/partner', [PartnerController::class, 'getData'])
    ->name('partner.api.get')
    ->middleware('permission:partner_view');

Route::get('/bpl', [BPLController::class, 'getData'])
    ->name('bpl.api.get')
    ->middleware('permission:bpl_view');

Route::get('/item/{bpl_number}', [ItemController::class, 'getItemByBPLNumber'])
    ->name('item.api.get')
    ->middleware('permission:bpl_view');

Route::get('/order', [OrderController::class, 'getOrder'])
    ->name('order.api.get')
    ->middleware('permission:order_view');

Route::get('/order/{order_id}/item-received', [ItemReceivedController::class, 'getItemReceivedByOrderId'])
    ->name('order.item_received.api.get')
    ->middleware('permission:order_view');

Route::get('/bill', [BillController::class, 'getBill'])
    ->name('bill.api.get')
    ->middleware('permission:bill_view');
