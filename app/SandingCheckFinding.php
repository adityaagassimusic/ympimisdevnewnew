<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SandingCheckFinding extends Model
{
	use SoftDeletes;

	protected $fillable = ['form_number','material_number','material_description','point','point_description','check_point','check_date','molding_evidence', 'material_evidence','status','remark', 'note','created_by',
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}

}
