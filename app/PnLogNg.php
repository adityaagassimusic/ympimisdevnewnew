<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PnLogNg extends Model
{
   use SoftDeletes;
    
    protected $fillable = [
        'ng','line','operator','form_id','tag','model','location','created_by','qty','reed'
    ];

    	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
