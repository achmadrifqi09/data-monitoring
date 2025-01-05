<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'item_id', 'bpl_number', 'partner_id', 'volume', 'price'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

}
