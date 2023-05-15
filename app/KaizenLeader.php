<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KaizenLeader extends Model
{
    protected $fillable = [
		'leader_id', 'employee_id','created_by'
	];
}
