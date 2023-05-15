<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverDetail extends Model
{
	use SoftDeletes;
	protected $fillable = [
		'driver_id','passenger_name','remark','category'
	];
}
