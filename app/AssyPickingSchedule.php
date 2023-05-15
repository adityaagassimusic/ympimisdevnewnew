<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssyPickingSchedule extends Model {

    protected $fillable = [
		'remark', 'material_number', 'due_date', 'quantity', 'created_by'
	];
}