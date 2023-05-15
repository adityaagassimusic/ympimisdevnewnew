<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssemblyTarget extends Model
{
	protected $fillable = [
		'material_number',
		'material_description',
		'due_date',
		'quantity',
		'actual_quantity',
		'additional_status',
		'reason',
		'employee_id',
		'created_by',
	];
}
