<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class IndirectMaterialOut extends Model{

	protected $fillable = [
		'in_date',
		'exp_date',
		'mfg_date',
		'qr_code',
		'material_number',
		'material_description',
		'license',
		'location',
		'quantity',
		'bun',
		'created_by'
	];
}
