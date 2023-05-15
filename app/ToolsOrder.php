<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ToolsOrder extends Model
{
    protected $fillable = [
		'tanggal','item_code','description','rack_code','kategori','location','group','qty','no_kanban','status','no_pr','no_po','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
