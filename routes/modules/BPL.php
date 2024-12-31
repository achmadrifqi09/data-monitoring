<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BPLController;

Route::get('/', [BPLController::class, 'index'])
    ->name('bpl.view')
    ->middleware('permission:bpl_view');
Route::post('/', [BPLController::class, 'store'])
    ->name('bpl.store')
    ->middleware('permission:bpl_create');
Route::patch('/{id}', [BPLController::class, 'update'])
    ->name('bpl.update')
    ->middleware('permission:bpl_update');
Route::delete('/{id}', [BPLController::class, 'destroy'])
    ->name('bpl.destroy')
    ->middleware('permission:bpl_delete');
Route::post('/import', [BPLController::class, 'import'])->name('bpl.import')
    ->middleware('permission:bpl_import');
// Route::post('/export', [BPLController::class, 'import'])->name('bpl.import')
//     ->middleware('permission:bpl_export');
