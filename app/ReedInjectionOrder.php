<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReedInjectionOrder extends Model{

	protected $fillable = [	
		'order_id',
		'due_date',
		'kanban',
		'material_number',
		'material_description',
		'print',
		'quantity',
		'hako',
		'hako_delivered',
		'remark',
		'operator_molding_id',
		'setup_molding',
		'operator_injection_id',
		'start_injection',
		'finish_injection',
		'delivered_by',
		'delivered_at',
		'created_by'
	];

}
