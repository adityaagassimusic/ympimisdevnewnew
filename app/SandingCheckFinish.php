<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SandingCheckFinish extends Model
{
	use SoftDeletes;

	protected $fillable = ['form_number','material_number','material_description','checker','check_time','point','point_description','check_point','status','remark','created_by'];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
