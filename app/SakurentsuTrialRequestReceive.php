<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SakurentsuTrialRequestReceive extends Model
{
	use SoftDeletes;

	protected $fillable = [ 'trial_id', 'sakurentsu_number', 'trial_receive_department', 'trial_receive_section', 'perbaikan', 'chief', 'chief_date', 'manager', 'manager_date', 'position', 'status', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
