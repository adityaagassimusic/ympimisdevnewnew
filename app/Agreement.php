<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agreement extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'category','department','related_department', 'vendor', 'description', 'valid_from', 'valid_to', 'status', 'status_due_date', 'remark', 'company_impact', 'analisis', 'implementation', 'action', 'penalty','created_by'
	];
}
