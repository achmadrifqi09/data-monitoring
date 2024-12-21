<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.dashboard');
})->name('dashboard');

Route::get('/bpl', function () {
    return view('pages.bpl.index');
})->name('bpl.index');
