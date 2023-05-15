<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IndirectMaterial extends Model{
	protected $fillable = [
		'material_number',
		'material_description',
		'bun',
		'lot',
		'storage_location',
		'label',
		'type',
		'expired',
		'license',
		'created_by'
	];
}
