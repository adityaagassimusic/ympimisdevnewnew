<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ElectricityTarget extends Model {

	protected $fillable = [
		'year',
		'daily_target',
		'monthly_target',
		'yearly_target',
		'created_by'
	];

}
