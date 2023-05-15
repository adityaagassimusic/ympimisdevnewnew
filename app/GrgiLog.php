<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GrgiLog extends Model{

	protected $fillable = [
		'month',
		'valcl',
		'location',
		'material_number',
		'receipt_quantity',
		'receipt_amount',
		'issue_quantity',
		'issue_amount',
		'ending_quantity',
		'ending_amount',
		'created_by'
	];

}
