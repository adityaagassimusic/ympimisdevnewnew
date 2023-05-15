<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PnCaseLogNg extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'log_id',
		'form_number',
		'ng',
		'ng_status',
		'operator',
		'line',
		'type',
		'location',
		'qty',
		'remark',
		'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
