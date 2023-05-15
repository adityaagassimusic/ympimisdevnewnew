<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SakurentsuThreeMDistribution extends Model
{
	use SoftDeletes;

	protected $fillable = [ 'form_id', 'distribution_to', 'distribute_status', 'remark', 'created_by'
	];

}
