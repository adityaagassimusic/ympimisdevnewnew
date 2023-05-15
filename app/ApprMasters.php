<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApprMasters extends Model{
	protected $fillable = [
		'department', 'category', 'judul', 'user', 'urutan', 'position', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'jd_japan'

	];
}
