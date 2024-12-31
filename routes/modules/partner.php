<?php

use App\Http\Controllers\PartnerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PartnerController::class, 'index'])
    ->name('partner.view')
    ->middleware('permission:partner_view');
Route::post('/import', [PartnerController::class, 'import'])
    ->name('partner.import')
    ->middleware('permission:partner_import');
Route::post('/', [PartnerController::class, 'store'])
    ->name('partner.store')
    ->middleware('permission:partner_create');
Route::patch('/{id}', [PartnerController::class, 'update'])
    ->name('partner.update')
    ->middleware('permission:partner_update');
Route::delete('/{id}', [PartnerController::class, 'destroy'])
    ->name('partner.destroy')
    ->middleware('permission:partner_delete');
