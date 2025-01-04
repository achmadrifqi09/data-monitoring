<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemReceived extends Model
{
    use SoftDeletes;

    protected $fillable = ['item_id', 'bpl_number', 'order_id', 'amount_received', 'date_received'];
    protected $dates = ['deleted_at'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function bpl(): BelongsTo
    {
        return $this->belongsTo(BPL::class, 'bpl_number');
    }
}
