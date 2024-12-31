<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    protected $table = 'project';
    use SoftDeletes;
    protected $fillable  = ['name', 'planned_date', 'planned_finish'];
    protected $dates = ['deleted_at'];
}
