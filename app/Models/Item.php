<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'item_name',
        'unit',
        'bpl_number',
        'volume',
        'brand',
        'price',
        'is_selected',
        'specification',
    ];
    protected $dates = ['deleted_at'];

    public function item_receiveds(): HasMany
    {
        return $this->hasMany(ItemReceived::class, 'item_id');
    }
}
