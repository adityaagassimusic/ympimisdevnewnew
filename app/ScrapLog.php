<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScrapLog extends Model
{
    use SoftDeletes;
	protected $fillable = [
		'scrap_id',
		'slip',
		'order_no',
		'material_number',
		'material_description',
		'spt',
		'valcl',
		'category',
		'issue_location',
		'receive_location',
		'remark',
		'quantity',
		'uom',
		'category_reason',
		'reason',
		'summary',
		'slip_created',
		'scraped_by',
		'created_by',
		'canceled_by',
		'created_at', 
		'deleted_at',	
		'updated_at',
		'no_invoice'
	];
}
