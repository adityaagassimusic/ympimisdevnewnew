<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseEmployeeMaster extends Model
{
    Use SoftDeletes;

	protected $fillable = [
		'employee_id', 'name', 'status','start_time_status','status_aktual_pekerjaan'
	];
}
