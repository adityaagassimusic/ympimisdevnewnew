<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExtraOrderApproval extends Model {

	protected $fillable = [
		'eo_number',
		'approval_order',
		'approver_id',
		'approver_name',
		'approver_email',
		'role',
		'remark',
		'status',
		'approved_at',
		'note',
		'comment',
		'answer',
		'created_by',
	];

}
