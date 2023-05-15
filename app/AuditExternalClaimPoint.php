<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuditExternalClaimPoint extends Model
{
    protected $fillable = [
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
		'created_by',

	];
}
