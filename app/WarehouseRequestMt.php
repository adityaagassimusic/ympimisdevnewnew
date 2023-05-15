<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseRequestMt extends Model
{
    Use SoftDeletes;

	protected $fillable = [
		'id','department','section','count_request','created_by'
	];
}
