<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseMaterialMaster extends Model
{
    Use SoftDeletes;

	protected $fillable = [
		'barcode','gmc_material','description','uom','uraian_size','lot','loc','no_hako','keterangan','shift','created_by','sloc_name'
	];
}
