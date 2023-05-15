<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PnLogProces extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'line','operator','form_id','tag','model','location','started_at','finished_at','created_by','qty'
    ];

    	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
