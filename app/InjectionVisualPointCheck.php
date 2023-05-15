<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InjectionVisualPointCheck extends Model
{
    protected $fillable = [
		'part_type','point_check_index','point_check_name', 'point_check_images','created_by'
	];
}
