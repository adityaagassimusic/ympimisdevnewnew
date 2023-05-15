<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HrLeaveRequestDetail extends Model
{
    protected $fillable = [
		'request_id',
		'employee_id',
		'name',
		'department',
		'section',
		'group',
		'sub_group',
		'confirmed_at',
		'returned_at',
		'created_by',

	];
}
