<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class PointCheckAudit extends Model
{
	protected $fillable = [
		'activity_list_id', 'product', 'proses', 'point_check', 'cara_cek','leader','foreman', 'created_by'
	];

	public function activity_lists()
    {
        return $this->belongsTo('App\ActivityList', 'activity_list_id', 'id');
    }

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by');
	}
}
