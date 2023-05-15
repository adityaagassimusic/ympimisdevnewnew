<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PingTemp extends Model
{
    protected $fillable = [
		'ip', 'remark', 'jum', 'status', 'created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
