<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniformLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'date_uniform','employee_id_master','name_master','employee_id','name','gender', 'category', 'size', 'qty', 'created_by'
    ];
}
