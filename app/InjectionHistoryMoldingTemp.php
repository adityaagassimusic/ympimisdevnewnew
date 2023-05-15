<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InjectionHistoryMoldingTemp extends Model
{
    protected $fillable = [
		'molding_code','type','pic','mesin', 'part',  'part_name','part_type', 'color','total_shot','start_time','end_time','note','remark','reason','created_by','status_cek_visual','status_approval_qa','status_parameter','status_purging','status_setting_robot'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
