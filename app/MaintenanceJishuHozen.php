<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaintenanceJishuHozen extends Model
{
    protected $fillable = [
    	'jishu_id',
		'date',
		'title',
		'point_id',
		'point_result',
		'pic_check',
		'leader',
		'foreman',
		'created_by',

	];
}
