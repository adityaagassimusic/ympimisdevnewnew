<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OvertimeEmployee extends Model
{
    Use SoftDeletes;

	protected $fillable = [
		'id','date', 'employee_id','food','ext_food', 'transport', 'start_time', 'end_time', 'final_hour', 'status', 'created_by','shift','status_ext_food','remark','name'
	];
}
