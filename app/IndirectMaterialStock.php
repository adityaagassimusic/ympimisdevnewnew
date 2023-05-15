<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IndirectMaterialStock extends Model{

	protected $fillable = [
		'in_date',
		'exp_date',
		'mfg_date',
		'qr_code',
		'material_number',
		'material_description',
		'license',
		'storage_location',
		'quantity',
		'bun',
		'print_status',
		'created_by',
	];

}
