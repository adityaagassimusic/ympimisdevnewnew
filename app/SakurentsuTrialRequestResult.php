<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SakurentsuTrialRequestResult extends Model
{
	use SoftDeletes;

	protected $fillable = [ 'trial_id', 'sakurentsu_number', 'department', 'section', 'trial_method', 'trial_result', 'trial_date_start', 'trial_date_finish', 'comment', 'trial_ok', 'fill_by', 'status', 'created_by',
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
