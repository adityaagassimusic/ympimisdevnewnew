<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SakurentsuTrialStatus extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'form_number','sakurentsu_number', 'proposer', 'department', 'subject','remark', 'status', 'status_progress', 'app_bom', 'app_price', 'trial_file', 'pss_desc', 'pss_file','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
