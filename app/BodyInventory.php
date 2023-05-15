<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BodyInventory extends Model
{
    protected $fillable = [
		'tag',
		'serial_number',
		'model',
		'material_number',
		'quantity',
		'location',
		'storage_location',
		'remark',
		'origin_group_code',
		'operator_id',

	];
}
