<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InjectionCdmCheck extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'cdm_code','product','part','type','color','injection_date','machine','machine_injection','cavity','cav','awal_a','awal_b','awal_c','awal_status','awal_employee_id','awal_created_at','ist_1_a','ist_1_b','ist_1_c','ist_1_status','ist_1_employee_id','ist_1_created_at','ist_2_a','ist_2_b','ist_2_c','ist_2_status','ist_2_employee_id','ist_2_created_at','ist_3_a','ist_3_b','ist_3_c','ist_3_status','ist_3_employee_id','ist_3_created_at','judgement','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
