<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceFinding extends Model
{
	Use SoftDeletes;

	protected $fillable = [ 'machine_id', 'machine_description', 'machine_group', 'part_machine', 'finding_date', 'finding_description', 'finding_photo', 'handling_description', 'handling_photo', 'handling_date', 'handling_by', 'pic', 'status', 'remark', 'created_by'
	];
}
