<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaterialOverReason extends Model {

	protected $fillable = [
		'material_over_id',
		'reason',
		'detail',
		'created_by'
	];

}
