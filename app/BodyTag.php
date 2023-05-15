<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BodyTag extends Model
{
    protected $fillable = [
		'tag',
		'serial_number',
		'model',
		'material_number',
		'remark',
		'quantity',
		'origin_group_code',
		'created_by',

	];
}
