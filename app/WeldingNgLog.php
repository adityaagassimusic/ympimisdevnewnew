<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeldingNgLog extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'tag', 'material_number', 'location', 'ng_name', 'quantity', 'employee_id', 'remark', 'started_at', 'operator_id', 'welding_time'
	];

	public function material()
	{
		return $this->belongsTo('App\Material', 'material_number', 'material_number')->withTrashed();
	}
}
