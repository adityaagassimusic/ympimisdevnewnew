<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Smbmr extends Model {

	protected $fillable = [
		'material_parent',
		'material_parent_description',
		'raw_material',
		'raw_material_description',
		'uom',
		'pgr',
		'usage',
		'created_by'
	];


}
