<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SakurentsuTrialRequestMaterial extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'trial_request_id', 'material_name', 'quantity', 'remark', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
