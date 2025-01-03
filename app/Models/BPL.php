<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BPL extends Model
{
    protected $table = 'bpl';
    use SoftDeletes;
    protected $fillable = ['item_name', 'unit'];
    protected $dates = ['deleted_at'];

    public function item_received(): HasMany
    {
        return $this->hasMany(ItemReceived::class, 'bpl_id');
    }
}
