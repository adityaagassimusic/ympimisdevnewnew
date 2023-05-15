<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReedApproval extends Model{
	
	protected $fillable = [	
		'order_id',
		'material_number',
		'material_description',
		'status',
		'operator_id',
		'location',
		'process',
		'mesin',
		'resin',
		'parameter',
		'parameter_photo',
		'lot_resin',
		'remark',
		'created_by'
	];
}
