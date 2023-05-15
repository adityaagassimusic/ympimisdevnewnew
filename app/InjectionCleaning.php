<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InjectionCleaning extends Model
{
    protected $fillable = [
    	'cleaning_id',
		'point_check_index',
		'point_check_type',
		'point_check_machine',
		'point_check_name',
		'point_check_standard',
		'result_check',
		'result_image',
		'check_time',
		'pic_check',
		'note',
		'created_by',

	];
}
