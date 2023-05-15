<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IvmsTemperature extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'person_id','employee_id','shift','name','location', 'date_in','date', 'point','temperature','abnormal_status','check_status','clinic_temperature','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
