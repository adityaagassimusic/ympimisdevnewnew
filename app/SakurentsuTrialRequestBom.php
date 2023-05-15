<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SakurentsuTrialRequestBom extends Model
{
	use SoftDeletes;

	protected $fillable = [ 'material_number', 'material_description', 'total_quantity', 'total_standard_time', 'total', 'form_number', 'no_doc', 'remark', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
