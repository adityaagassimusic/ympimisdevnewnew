<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MesinLogInjection extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'mesin', 'status', 'reason' , 'created_by'
	];
}
