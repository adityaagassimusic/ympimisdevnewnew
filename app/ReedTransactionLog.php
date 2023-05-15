<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReedTransactionLog extends Model {

	protected $fillable = [	
		'category',
		'material_number',
		'issue_location',
		'receive_location',
		'movement_type',
		'transacted_by',
		'remark',
		'quantity',
		'created_by'
	];

}
