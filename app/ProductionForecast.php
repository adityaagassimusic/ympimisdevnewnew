<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductionForecast extends Model{

	protected $fillable = [
		'forecast_month',
		'material_number',
		'quantity',
		'remark',
		'created_by'
	];

}
