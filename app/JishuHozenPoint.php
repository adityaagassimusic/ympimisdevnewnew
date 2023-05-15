<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JishuHozenPoint extends Model
{

	protected $fillable = [
		'activity_list_id', 'nama_pengecekan','leader','foreman','created_by'
	];

	public function activity_lists()
    {
        return $this->belongsTo('App\ActivityList', 'activity_list_id', 'id')->withTrashed();
    }

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
