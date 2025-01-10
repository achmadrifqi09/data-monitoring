<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bill extends Model
{
    protected $fillable = [
        'order_id',
        'netto',
        'fee_deduction',
        'retention',
        'ppn',
        'ppn_percentage',
        'pph_percentage',
        'pph',
        'receipt_date',
        'due_date',
        'dpp',
        'bap',
        'date_of_bap',
        'bill_total'
    ];
    public $timestamps = true;

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function bill_items(): HasMany
    {
        return $this->hasMany(BillItem::class, 'bill_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(BillDocument::class, 'bill_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'bill_id');
    }
}
