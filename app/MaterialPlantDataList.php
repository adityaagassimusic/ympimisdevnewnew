<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialPlantDataList extends Model
{
    protected $fillable = [
		'material_number', 'material_description', 'pgr', 'bun', 'spt', 'storage_location', 'mrpc', 'valcl', 'standard_price', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
