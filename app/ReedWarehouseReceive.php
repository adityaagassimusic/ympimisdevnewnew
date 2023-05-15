<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReedWarehouseReceive extends Model
{
    protected $fillable = [	
		'receive_date',
		'material_number',
		'material_description',
		'remark',
		'quantity',
		'bag_quantity',
		'bag_arranged',
		'bag_delivered',
		'print_status',
		'operator_receive',
		'operator_storage',
		'created_by'
	];
}
