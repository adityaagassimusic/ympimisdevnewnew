<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SakurentsuThreeMImplementation extends Model
{
	Use SoftDeletes;
	
	protected $fillable = [
		'form_id', 'form_number', 'form_date', 'section', 'name', 'title', 'reason', 'started_date', 'date_note', 'actual_date', 'check_date', 'checker', 'remark', 'att', 'serial_number', 'created_by'
	];
}
