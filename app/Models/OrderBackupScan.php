<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderBackupScan extends Model
{
    protected $fillable = ['order_id', 'document'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
