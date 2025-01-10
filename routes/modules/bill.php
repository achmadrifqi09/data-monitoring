<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillController;

Route::get('/', [BillController::class, 'index'])
    ->name('bill.view')
    ->middleware('permission:bill_view');

Route::get('/{id}', [BillController::class, 'show'])
    ->name('bill.detail')
    ->middleware('permission:bill_view');

Route::patch('/{id}', [BillController::class, 'update'])
    ->name('bill.update')
    ->middleware('permission:bill_update');

Route::delete('/{id}', [BillController::class, 'destroy'])
    ->name('bill.delete')
    ->middleware('permission:bill_delete');

Route::patch('/{id}/document', [BillController::class, 'uploadDocument'])
    ->name('bill.update.document')
    ->middleware('permission:bill_update');

Route::delete('/{id}/document', [BillController::class, 'destroyDocument'])
    ->name('bill.delete.document')
    ->middleware('permission:bill_delete');

Route::patch('/{bill_id}/bill_item/{bill_item_id}', [BillController::class, 'updateBillItem'])
    ->name('bill.item.update')
    ->middleware('permission:bill_update');

Route::delete('/{bill_id}/bill_item/{bill_item_id}', [BillController::class, 'destroyBillItem'])
    ->name('bill.item.delete')
    ->middleware('permission:bill_delete');

Route::get('/c/form', [BillController::class, 'create'])
    ->name('bill.create')
    ->middleware('permission:bill_create');

Route::post('/', [BillController::class, 'store'])
    ->name('bill.store')
    ->middleware('permission:bill_create');
