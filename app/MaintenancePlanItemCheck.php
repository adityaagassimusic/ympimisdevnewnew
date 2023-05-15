<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenancePlanItemCheck extends Model
{
    Use SoftDeletes;

    protected $fillable = [
    	'id','machine_name', 'item_check', 'substance', 'essay_category', 'remark', 'lower_limit', 'upper_limit', 'created_by'
    ];
}
