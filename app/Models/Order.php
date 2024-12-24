<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = ['po_number', 'partner_id', 'description', 'po_date', 'start_date', 'finish_date'];
    protected $dates = ['deleted_at'];
}
