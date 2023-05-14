<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MiddleLacqueringCheckLog extends  Model{
	
	protected $fillable = [
		'employee_id','tag','material_number','quantity','location','operator_id','buffing_time','sync'
	];
}
