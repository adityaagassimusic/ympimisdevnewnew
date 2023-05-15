<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IndirectMaterialPick extends Model{

	protected $fillable = [
		'remark',
		'in_date',
		'mfg_date',
		'exp_date',
		'qr_code',
		'schedule_id',
		'material_number',
		'material_description',
		'license',
		'location',
		'quantity',
		'picking_quantity',
		'picking_bun',
		'bun',
		'created_by'
	];

}
