<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PushBlockTorqueTemp extends Model
{

	protected $fillable = [
		'push_block_code','push_block_id_gen','check_date','check_type', 'injection_date_middle', 'mesin_middle', 'injection_date_head_foot', 'mesin_head_foot','product_type','middle','head_foot','torque1','torque2','torque3','torqueavg','judgement','notes','pic_check','created_by'
	];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
