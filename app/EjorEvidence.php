<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EjorEvidence extends Model
{
	Use SoftDeletes;

	protected $fillable = ['form_id','uploaded_by','uploaded_at','note','attachment','status','remark','approve_by','approve_at','created_by'
	];
}
