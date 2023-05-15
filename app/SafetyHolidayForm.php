<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SafetyHolidayForm extends Model
{
	use SoftDeletes;

	protected $fillable = [ 'form_id', 'pic', 'location', 'date_create', 'check_point', 'condition', 'note', 'photo', 'category', 'status', 'remark', 'pic_sign', 'pic_sign_at', 'superior_sign', 'superior_at', 'created_by'];
}
