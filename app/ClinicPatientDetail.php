<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ClinicPatientDetail extends Model{
	use SoftDeletes;

	protected $fillable = [
		'employee_id', 'patient_list_id', 'purpose', 'diagnose', 'body_temperature', 'paramedic', 'doctor', 'family', 'family_name', 'visited_at', 'action', 'suggestion'
	];
}
