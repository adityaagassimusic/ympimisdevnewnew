<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SakurentsuThreeMApproval extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'form_id', 'approver_name', 'approver_id', 'approver_department', 'approver_division', 'status', 'approve_at', 'created_by'
	];
}
