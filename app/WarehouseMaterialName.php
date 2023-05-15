<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseMaterialName extends Model
{
    Use SoftDeletes;

	protected $fillable = [
		'id', 'dept', 'sloc_name','location','code_location'
	];
}
