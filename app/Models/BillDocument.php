<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillDocument extends Model
{
    protected $fillable = ['bill_id', 'document'];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }
}
