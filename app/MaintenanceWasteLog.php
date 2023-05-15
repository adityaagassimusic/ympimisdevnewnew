<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceWasteLog extends Model
{
	Use SoftDeletes;

	protected $fillable = [ 'due_date', 'waste_category', 'category', 'quantity', 'remaining_stock', 'pic', 'remark', 'created_by'
	];
}
