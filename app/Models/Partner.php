<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'address'];
    protected $dates = ['deleted_at'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
