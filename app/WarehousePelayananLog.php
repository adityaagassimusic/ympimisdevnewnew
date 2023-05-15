<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehousePelayananLog extends Model
{
    Use SoftDeletes;

	protected $fillable = [
		'id','kode_request','gmc','status_aktual','created_by'
	];
}