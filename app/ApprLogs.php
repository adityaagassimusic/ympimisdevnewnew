<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApprLogs extends Model{
	protected $fillable = [
		'no_transaction', 'nik', 'department', 'description', 'date', 'summary', 'file', 'file_pdf', 'created_by', 'created_at', 'logs_at', 'deleted_at', 'updated_at', 'approve1', 'approve2', 'approve3', 'approve4', 'approve5', 'approve6', 'approve7', 'approve8', 'approve9', 'approve10', 'date1', 'date2', 'date3', 'date4', 'date5', 'date6', 'date7', 'date8', 'date9', 'date10', 'remark'
	];
}
