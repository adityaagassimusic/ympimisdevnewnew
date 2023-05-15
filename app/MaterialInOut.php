<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialInOut extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'po_number',
		'item_line',
		'material_number',
		'movement_type',
		'issue_location',
		'receive_location',
		'cost_center',
		'quantity',
		'entry_date',
		'posting_date',
		'bc_document',
		'sppb',
		'created_by'
	];
}
