<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopJig extends Model
{
	use SoftDeletes;

	protected $fillable = ['jig_code', 'process', 'jig_name', 'material', 'part_number', 'part_name', 'quantity', 'quantity_actual', 'lot_order', 'quantity_finish', 'drawing', 'status_order', 'remark', 'created_by'
	];
}
