<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PnOperatorLog extends Model
{
	use SoftDeletes;

	protected $fillable = ['operator_id','operator_name','process_name','date','line','remark','created_by'];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
