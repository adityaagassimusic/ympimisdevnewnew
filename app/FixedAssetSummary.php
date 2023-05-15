<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FixedAssetSummary extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'id',
		'period',
		'location',
		'status',
		'remark',
		'attachment',
		'prepared_by',
		'prepare_date',
		'acc_manager',
		'acc_manager_at',
		'finance_director',
		'finance_director_at',
		'president_director',
		'president_director_at',
		'created_by'
	]; 
}
