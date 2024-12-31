<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemReceived extends Model
{
    use SoftDeletes;

    protected $fillable = ['bpl_id', 'order_id', 'amount_received', 'date_received'];
    protected $dates = ['deleted_at'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(BPL::class, 'bpl_id');
    }
}
