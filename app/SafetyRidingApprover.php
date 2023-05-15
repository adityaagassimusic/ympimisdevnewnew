<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SafetyRidingApprover extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'period', 'department', 'employee_id', 'employee_name', 'remark' ,'created_by', 'location'
    ];
}
