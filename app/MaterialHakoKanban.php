<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialHakoKanban extends Model
{
    Use SoftDeletes;

	protected $fillable = [
		'id','barcode','gmc_material','description','uom','lot','rcvg_sloc','sloc_name','no_hako','keterangan','created_by','buyer','jenis','print'
	];
}
