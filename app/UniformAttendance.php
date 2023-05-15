<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniformAttendance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'periode','employee_id','name','department','gender', 'category', 'size', 'attend_date','employee_id_master','name_master', 'created_by'
    ];
}
