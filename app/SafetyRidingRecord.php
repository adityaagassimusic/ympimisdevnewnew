<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SafetyRidingRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'due_date','department','employee_id','employee_name','safety_riding','location','created_by', 'remark', 'period', 'location', 'department'
    ];
}
