<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BodyQueue extends Model
{
    protected $fillable = [
		'material_number', 'location', 'quantity',
	];
}
