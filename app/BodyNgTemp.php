<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BodyNgTemp extends Model
{
    protected $fillable = [
		'tag', 'material_number', 'serial_number', 'location', 'ng_name', 'quantity', 'employee_id', 'remark', 'started_at', 'operator_id'
	];
}
