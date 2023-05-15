<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabelEvidence extends Model
{
	use SoftDeletes;

	protected $fillable = ['material_number','material_description', 'product','remark','note','evidence','created_by'
];
}
