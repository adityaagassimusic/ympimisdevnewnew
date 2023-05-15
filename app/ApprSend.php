<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApprSend extends Model{
	protected $fillable = [
		'id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'reason', 'comment', 'jd_japan'

	];
}
