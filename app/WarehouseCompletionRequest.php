<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseCompletionRequest extends Model
{
    Use SoftDeletes;

	protected $fillable = [
		'id','date_request','kode_request','gmc','description','quantity_total','loc','sloc_name','created_by'
	];
}
