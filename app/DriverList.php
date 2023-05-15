<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverList extends Model
{
	use SoftDeletes;
	protected $fillable = [
		'driver_id',
		'name',
		'phone_no',
		'whatsapp_no',
		'plat_no',
		'category',
		'car',
		'remark',
		'created_by',

	];
}
