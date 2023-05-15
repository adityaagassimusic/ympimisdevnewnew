<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InjectionScheduleMolding extends Model
{
     protected $fillable = [
		'id_schedule','machine','material_number','material_description','part','color', 'qty','start_time','end_time','created_by'
	];
}
