<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SandingCheck extends Model
{
	use SoftDeletes;

	protected $fillable = ['form_number','material_number','material_description','quantity','point','point_description','check_point','checker','check_time','status','remark', 'fu_number','created_by', 'process', 'shift', 'st_time'];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
