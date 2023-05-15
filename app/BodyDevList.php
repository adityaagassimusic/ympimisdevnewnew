<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BodyDevList extends Model
{
	protected $fillable = [
		'ip_address',
		'location',
		'origin_group_code',
		'online_time',
		'operator_id',
		'sedang_tag',
		'sedang_model',
		'sedang_serial_number',
		'sedang_time',
		'allowance',
		'remark',
		'dev_type',
		'dev_name',

	];
}
