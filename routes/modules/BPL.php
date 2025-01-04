<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BPLController;
use App\Http\Controllers\ItemController;

Route::get('/', [BPLController::class, 'index'])
    ->name('bpl.view')
    ->middleware('permission:bpl_view');
Route::get('/{id}', [BPLController::class, 'show'])
    ->name('bpl.show')
    ->middleware('permission:bpl_view');

Route::post('/', [BPLController::class, 'store'])
    ->name('bpl.bulkStore')
    ->middleware('permission:bpl_create');

Route::patch('/{id}', [BPLController::class, 'update'])
    ->name('bpl.update')
    ->middleware('permission:bpl_update');

Route::delete('/{bpl_number}', [BPLController::class, 'destroy'])
    ->name('bpl.destroy')
    ->middleware('permission:bpl_delete');

Route::post('/import', [BPLController::class, 'import'])->name('bpl.import')
    ->middleware('permission:bpl_import');



Route::get('{bpl_number}/form', [ItemController::class, 'add'])
    ->name('bpl.item.form')
    ->middleware('permission:bpl_create');

Route::post('/{bpl_number}/items', [ItemController::class, 'bulkStore'])
    ->name('bpl.item.bulkStore')
    ->middleware('permission:bpl_create');

Route::post('/{bpl_number}/item', [ItemController::class, 'store'])
    ->name('bpl.item.store')
    ->middleware('permission:bpl_create');

Route::patch('/{bpl_number}/item/{id}', [ItemController::class, 'update'])
    ->name('bpl.item.update')
    ->middleware('permission:bpl_update');

Route::delete('/{bpl_number}/item/{id}', [ItemController::class, 'destroy'])
    ->name('bpl.item.destroy')
    ->middleware('permission:bpl_delete');


