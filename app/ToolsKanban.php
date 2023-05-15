<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ToolsKanban extends Model
{
    protected $fillable = [
		'rack_code','item_code','description','location','group','category','remark','moq','uom','no_kanban','print_status','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
