<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SakurentsuThreeMSpecial extends Model
{
	use SoftDeletes;

	protected $fillable = [ 'form_number', 'sakurentsu_number', 'item_khusus', 'target_change', 'actual_change', 'pic', 'eviden_description', 'eviden_att', 'status', 'remark', 'created_by', 'created_at', 'updated_at'
	];
}
