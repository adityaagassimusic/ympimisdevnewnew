<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PnCaseLogProccess extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'line',
		'form_number',
		'operator',
		'type',
		'location',
		'qty',
		'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
