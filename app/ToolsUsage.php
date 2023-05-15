<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ToolsUsage extends Model
{
    protected $fillable = [
		'tanggal','employee_id','employee_name','item_code','description','rack_code','kategori','lifetime','location','group','lot_kanban','stock_kanban','balance_kanban','qty','no_kanban','note','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
