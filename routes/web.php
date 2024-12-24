<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PartnerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BPLController;
use App\Http\Controllers\OrderController;

Route::get('/login', [AuthController::class, 'index'])->name('login.view');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('pages.dashboard');
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/rekanan', [PartnerController::class, 'index'])->name('partner.view');
    Route::get('/rekanan/data', [PartnerController::class, 'getData'])->name('partner.data');
    Route::post('/rekanan/import', [PartnerController::class, 'import'])->name('partner.import');
    Route::post('/rekanan', [PartnerController::class, 'store'])->name('partner.store');
    Route::patch('/rekanan/{id}', [PartnerController::class, 'update'])->name('partner.update');
    Route::delete('/rekanan/{id}', [PartnerController::class, 'destroy'])->name('partner.destroy');

    Route::get('/bpl', [BPLController::class, 'index'])->name('bpl.view');
    Route::post('/bpl', [BPLController::class, 'store'])->name('bpl.store');
    Route::patch('/bpl/{id}', [BPLController::class, 'update'])->name('bpl.update');
    Route::delete('/bpl/{id}', [BPLController::class, 'destroy'])->name('bpl.destroy');
    Route::post('/bpl/import', [BPLController::class, 'import'])->name('bpl.import');

    Route::get('/order', [OrderController::class, 'index'])->name('order.view');
    Route::get('/order/form', [OrderController::class, 'form'])->name('order.form');
});

