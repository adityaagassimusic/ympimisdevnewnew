<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EfficiencyManpower extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'valid_date', 'employee_id', 'employee_name', 'location', 'category', 'remark', 'created_by'
    ];
}
