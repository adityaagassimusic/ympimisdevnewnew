<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseTimeOperatorLog extends Model
{
    Use SoftDeletes;

	protected $fillable = [
		'id','employee_id','request_desc','status','start_job','end_job','desc_gmc','joblist'
	];
}