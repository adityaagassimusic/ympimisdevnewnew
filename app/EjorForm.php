<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EjorForm extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'form_id',
		'request_date',
		'job_type',
		'job_category',
		'job_category_note',
		'section',
		'target_date',
		'title',
		'priority',
		'description',
		'purpose',
		'condition_before',
		'condition_after',
		'attachment',
		'remark',
		'status',
		'pic',
		'pic_receive_date',
		'created_by'
	];
}
