<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuditExternalClaimSchedule extends Model
{
    protected $fillable = [
		'audit_id',
		'employee_id',
		'schedule_date',
		'schedule_status',
		'remark',
		'created_by',

	];
}
