<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductionRequest extends Model{

	protected $fillable = [
		'request_month',
		'category',
		'sales_order',
		'material_number',
		'destination_code',
		'priority',
		'quantity',
		'st_plan',
		'remark',
		'created_by'
	];

}
