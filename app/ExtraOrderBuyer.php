<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExtraOrderBuyer extends Model {

	protected $fillable = [
		'uid',
		'attention',
		'email',
		'division',
		'destination_code',
		'destination_name',
		'destination_shortname',
		'currency',
		'created_by'
	];

}
