<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BPL extends Model
{
    protected $table = 'bpl';
    protected $fillable = ['bpl_number', 'description', 'date_of_use'];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'bpl_number', 'bpl_number');
    }

    public function item_received(): HasMany
    {
        return $this->hasMany(ItemReceived::class, 'bpl_id');
    }
}
