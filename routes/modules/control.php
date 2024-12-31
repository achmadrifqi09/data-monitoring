<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/user', [UserController::class, 'index'])
    ->name('user.manage')
    ->middleware('permission:user_control_view');

Route::post('/user', [UserController::class, 'store'])
    ->name('user.manage.store')
    ->middleware('permission:user_control_create');

Route::patch('/user/{id}/status', [UserController::class, 'update'])
    ->name('user.manage.update')
    ->middleware('permission:user_control_update');

Route::delete('/user/{id}', [UserController::class, 'destroy'])
    ->name('user.manage.destroy')
    ->middleware('permission:user_control_delete');

Route::post('/user/{id}/access', [UserController::class, 'addAccess'])
    ->name('user.manage.access')
    ->middleware('permission:user_control_create');


Route::get('/access', [AccessController::class, 'index'])
    ->name('user.access')
    ->middleware('permission:user_control_acesss_view');

Route::post('/access', [AccessController::class, 'store'])
    ->name('user.access.store')
    ->middleware('permission:user_control_acesss_create');

Route::delete('/access/{id}', [AccessController::class, 'destroy'])
    ->name('user.access.destroy')
    ->middleware('permission:user_control_acesss_delete');


Route::get('/project', [ProjectController::class, 'index'])
    ->name('project')
    ->middleware('permission:project_view');

Route::put('/project/{id}', [ProjectController::class, 'update'])
    ->name('project.update')
    ->middleware('permission:project_update');
