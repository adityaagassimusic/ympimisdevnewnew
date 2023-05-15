<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RcNgTrend extends Model
{
    protected $fillable = [
		'date',
		'mesin',
		'dryer',
		'resin',
		'person',
		'person_injeksi',
		'part',
		'product',
		'molding',
		'ng_name',
		'qty_ng',
		'created_by',

	];
}
