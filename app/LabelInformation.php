<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabelInformation extends Model
{
	use SoftDeletes;

	protected $fillable = ['material_number','material_description','code','category','hpl','vendor_code','vendor_name','paper','bom','label_picture','bom_change','created_by'
	];
}
