<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedAssetPic extends Model
{
	Use SoftDeletes;

	protected $fillable = [ 'employee_id', 'name', 'department', 'section', 'category', 'location', 'remark', 'created_by']; 
}
