<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FixedAssetAudit extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'id',
		'period',
		'category',
		'location',
		'sap_number',
		'asset_name',
		'asset_section',
		'asset_map',
		'asset_images',
		'pic',
		'result_images',
		'result_video',
		'checked_by',
		'checked_date',
		'note',
		'availability',
		'asset_condition',
		'label_condition',
		'usable_condition',
		'map_condition',
		'asset_image_condition',
		'status',
		'audit_type',
		'remark',
		'created_by',

	]; 
}
