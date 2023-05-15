<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuditExternalClaim extends Model
{
    protected $fillable = [
		'schedule_id',
		'audit_id',
		'audit_title',
		'periode',
		'email_date',
		'incident_date',
		'origin',
		'department',
		'area',
		'product',
		'audit_index',
		'audit_point',
		'audit_images',
		'auditor',
		'result_check',
		'result_image',
		'note',
		'chief_foreman',
		'manager',
		'send_status',
		'handling',
		'remark',
		'handled_by',
		'handled_at',
		'created_by',


	];
}
