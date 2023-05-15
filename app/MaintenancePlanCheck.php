<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenancePlanCheck extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'item_code', 'part_check', 'item_check', 'substance', 'check', 'check_value', 'check_after', 'description', 'photo_before', 'photo_after', 'remark', 'check_date', 'handling', 'handling_photo', 'handling_by', 'created_by'
	];
}
