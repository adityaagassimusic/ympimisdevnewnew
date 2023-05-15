<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopProcessOperator extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'operator_id', 'process_id', 'process_group', 'skill_level', 'created_by'
	];
}
