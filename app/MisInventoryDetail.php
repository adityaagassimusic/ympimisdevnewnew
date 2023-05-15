<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MisInventoryDetail extends Model
{
    protected $fillable = [
		'date_to','no_pr','no_po','category','nama_item','qty','status','created_by','peruntukan','id_data','no_seri','note','remark','checklist_id','date_receive'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}


