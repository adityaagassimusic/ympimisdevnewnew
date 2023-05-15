<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class OculusUser extends Model
{
	use SoftDeletes;
    protected $fillable = [
		'employee_id','name', 'created_by'
	];
}
