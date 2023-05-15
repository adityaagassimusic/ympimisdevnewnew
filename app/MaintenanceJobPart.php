<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class MaintenanceJobPart extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'machine_group', 'trouble_part', 'part_inspection', 'remark', 'created_by'
	];
}
