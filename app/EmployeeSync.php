<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeSync extends Model
{
	protected $fillable = [
		'employee_id', 'name', 'gender', 'birth_place', 'birth_date','address','phone','card_id','account','bpjs','jp','npwp','hire_date','end_date','position','position_new','position_code','grade_code','grade_name','employment_status','cost_center','assignment','division','department','section','sub_section','group','sub_group','wa_number','direct_superior','union'
	];

	public function departments()
	{
		return $this->belongsTo('App\Department', 'department', 'department_name');
	}
}
