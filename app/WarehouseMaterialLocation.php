<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseMaterialLocation extends Model
{
    Use SoftDeletes;

	protected $fillable = [
		'gmc', 'description','number_package', ,'area_code','area','remark','no_case'
	];
}
