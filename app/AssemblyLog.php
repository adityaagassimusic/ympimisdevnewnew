<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssemblyLog extends  Model
{
    protected $fillable = [
		'tag', 'serial_number', 'model', 'location','location_number', 'operator_id', 'sedang_start_date', 'sedang_finish_date', 'origin_group_code','status_material','operator_audited', 'created_by', 'trial', 'note'
	];
}
