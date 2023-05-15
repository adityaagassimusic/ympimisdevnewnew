<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndirectMaterialLog extends Model{
	use SoftDeletes;

	protected $fillable = [
		'in_date',
		'mfg_date',
		'exp_date',
		'qr_code',
		'material_number',
		'material_description',
		'license',
		'storage_location',
		'remark',
		'quantity',
		'bun',
		'balance',
		'balance_license',
		'schedule_id',
		'created_by'
	];
	
}
