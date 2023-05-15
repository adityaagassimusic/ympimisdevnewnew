<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class DetailBensuki extends Model
{
   use SoftDeletes;

	protected $fillable = [
		'id_bensuki','kode_reed','posisi','ng', 'qty','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
