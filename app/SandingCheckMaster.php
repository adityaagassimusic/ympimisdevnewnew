<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SandingCheckMaster extends Model
{
	use SoftDeletes;

	protected $fillable = ['material_number','material_description','category','point','description','remark','ik','created_by'];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}