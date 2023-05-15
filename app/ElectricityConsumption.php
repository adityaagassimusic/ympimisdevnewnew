<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ElectricityConsumption extends Model {

	protected $fillable = [
		'date',
		'lbp1',
		'lbp2',
		'bp',
		'kvarh',
		'lwbp1',
		'lwbp2',
		'wbp',
		'consumption_kvarh',
		'outgoing_i',
		'outgoing_ii',
		'outgoing_iii',
		'outgoing_iv',
		'consumption_outgoing_i',
		'consumption_outgoing_ii',
		'consumption_outgoing_iii',
		'consumption_outgoing_iv',
		'created_by',
	];

}
