<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QaKensaNgLog extends Model
{
	protected $fillable = [
		'incoming_check_code',
		'incoming_check_log_id',
		'location',
		'inspector_id',
		'material_number',
		'material_description',
		'qty_production',
		'qty_check',
		'qty_ng',
		'inspection_level',
		'ng_name',
		'status_ng',
		'note_ng',
		'created_by'
	];
}
