<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SafetyHolidayPic extends Model
{
	use SoftDeletes;

	protected $fillable = ['employee_id','name','category','location','create_form_at','approve_by','approve_at','remark','created_by'];
}
