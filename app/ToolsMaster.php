<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ToolsMaster extends Model
{
    protected $fillable = [
		'rack_code','item_code','description','location','group','category','remark','moq','uom','lifetime','leadtime','lot_kanban','stock_kanban','need_kanban','balance_kanban','note','print_status','no_kanban','allowance','quantity_order','no_pr','status','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
