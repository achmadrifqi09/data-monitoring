<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/', [PaymentController::class, 'index'])
    ->name('payment.view')
    ->middleware('permission:payment_view');

Route::get('/{id}', [PaymentController::class, 'show'])
    ->name('payment.detail')
    ->middleware('permission:payment_view');

Route::post('/', [PaymentController::class, 'store'])
    ->name('payment.store')
    ->middleware('permission:payment_create');

Route::delete('/{id}', [PaymentController::class, 'destroy'])
    ->name('payment.destroy')
    ->middleware('permission:payment_delete');

Route::post('/installment', [PaymentController::class, 'addPaymentInstallment'])
    ->name('payment.installment')
    ->middleware('permission:payment_create');

Route::patch('{payment_id}/installment/{payment_installment_id}/payment-proof', [PaymentController::class, 'uploadPaymentProof'])
    ->name('payment.installment.upload')
    ->middleware('permission:payment_update');

Route::patch('{payment_id}/installment/{payment_installment_id}', [PaymentController::class, 'updatePaymentInstallment'])
    ->name('payment.installment.update')
    ->middleware('permission:payment_update');

Route::delete('{payment_id}/installment/{payment_installment_id}', [PaymentController::class, 'deletePaymentInstallment'])
    ->name('payment.installment.delete')
    ->middleware('permission:payment_delete');
