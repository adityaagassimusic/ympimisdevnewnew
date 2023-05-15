<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HrLeaveRequest extends Model
{
	protected $fillable = [
		
		'request_id',
		'position',
		'department',
		'date',
		'purpose_category',
		'purpose',
		'detail_city',
		'purpose_detail',
		'time_departure',
		'time_arrived',
		'return_or_not',
		'diagnose',
		'action',
		'suggestion',
		'add_driver',
		'reason',
		'driver_request_id',
		'remark',
		'created_by',
	];
   

}
