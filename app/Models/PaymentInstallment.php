<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PaymentInstallment extends Model
{
    protected $fillable = [
        'payment_id',
        'payment_date',
        'nominal_payment',
        'payment_proof',
    ];
}
