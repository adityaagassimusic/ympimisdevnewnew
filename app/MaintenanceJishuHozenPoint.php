<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaintenanceJishuHozenPoint extends Model
{
    protected $fillable = [
    	'jishu_id',
		'title',
		'area_code',
		'location',
		'department',
		'machine',
		'doc_number',
		'rev',
		'rev_date',
		'check_time',
		'classification',
		'point_check_index',
		'point_check_name',
		'standard',
		'point_check_type',
		'drawing',
		'leader',
		'foreman',
		'created_by',
	];
}
