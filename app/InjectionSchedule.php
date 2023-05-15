<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InjectionSchedule extends Model
{
    protected $fillable = [
		'id_schedule','machine','material_number','material_description','part','color', 'qty','start_time','end_time','reason','molding','created_by'
	];
}
