<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MisInventoryNew extends Model
{
    protected $fillable = [
		'nama_item','no_po','id_order','qty','category','remark','no_pr','date_to','checklist_id'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}


