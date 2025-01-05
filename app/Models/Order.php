<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = ['po_number', 'partner_id', 'description', 'po_date', 'start_date', 'finish_date'];
    protected $dates = ['deleted_at'];

    public function bpl(): HasMany
    {
        return $this->hasMany(BPL::class);
    }

    public function order_items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function item_receiveds(): HasMany
    {
        return $this->hasMany(ItemReceived::class);
    }

    public function order_backup_scans(): HasMany
    {
        return $this->hasMany(OrderBackupScan::class, 'order_id');
    }
}
