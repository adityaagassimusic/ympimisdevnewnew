<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NgLogBensuki extends Model
{
	use SoftDeletes;

	protected $fillable = ['employee_id','name','total_ng','period_from','period_to','trainer_id','trainer_name','trained_at','status','remark','created_by'

	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
