<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehousePelayananIn extends Model
{
    Use SoftDeletes;

	protected $fillable = [
		'id','kode_request','gmc','no_hako','quantity_request','remark','created_by','tanggal'
	];
}
