<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SafetyHolidayMaster extends Model
{
	use SoftDeletes;

	protected $fillable = [ 'point_number', 'point_check', 'area', 'remark', 'created_by'];
}
