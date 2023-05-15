<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EjorFormApprover extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'form_id',
		'approver_id',
		'approver_name',
		'approve_at',
		'status',
		'note',
		'remark',
		'created_by'
	];
}
