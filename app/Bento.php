<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bento extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'order_id', 'order_by', 'order_by_name', 'charge_to', 'charge_to_name', 'due_date', 'employee_id', 'employee_name', 'department', 'section', 'status', 'remark', 'created_by', 'approver_id', 'approver_name', 'email', 'grade_code', 'location'
	];
}
