<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemReceived extends Model
{
    protected $fillable = ['item_id', 'bpl_number', 'order_id', 'amount_received', 'date_received'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function bpl(): BelongsTo
    {
        return $this->belongsTo(BPL::class, 'bpl_number');
    }
}
