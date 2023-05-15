<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserActivityLog extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'activity', 'ip', 'url', 'created_by', 'method', 'user_agent', 'remark'
	];
}
