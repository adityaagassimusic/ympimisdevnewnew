<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExtraOrderPrice extends Model
{
	use SoftDeletes;

	protected $fillable = ['material_number','sales_price','valid_date','attachment','status','remark','created_by'];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
