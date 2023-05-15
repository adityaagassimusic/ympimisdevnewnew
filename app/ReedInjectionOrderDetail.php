<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReedInjectionOrderDetail extends Model{

	protected $fillable = [	
		'order_id',
		'due_date',
		'kanban',
		'material_number',
		'material_description',
		'quantity',
		'remark',
		'delivered_by',
		'delivered_at',
		'picking_by',
		'picking_at',
		'created_by'
	];

}
