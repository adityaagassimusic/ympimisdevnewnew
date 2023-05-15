<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaterialOver extends Model{

	protected $fillable = [
		'date',
		'material_number',
		'material_description',
		'pgr',
		'bun',
		'usage',
		'quantity'
	];

}
