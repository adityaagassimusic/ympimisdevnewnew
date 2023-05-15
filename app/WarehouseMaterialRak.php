<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseMaterialRak extends Model
{
    Use SoftDeletes;

	protected $fillable = [
		'id', 'gmc', 'description','rak','no_urut'
	];
}
