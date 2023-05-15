<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BodyLog extends Model
{
    protected $fillable = [
    	'tag',
		'serial_number',
		'model',
		'material_number',
		'location',
		'storage_location',
		'quantity',
		'remark',
		'origin_group_code',
		'operator_id',
		'sedang_start_time',
		'sedang_finish_time',

	];
}
