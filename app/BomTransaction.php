<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BomTransaction extends Model {
    
    use SoftDeletes;

	protected $fillable = [
		'material_parent', 'material_child', 'usage', 'remark', 'created_by'
	];

}
