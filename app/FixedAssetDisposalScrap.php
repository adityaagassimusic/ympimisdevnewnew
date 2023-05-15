<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedAssetDisposalScrap extends Model
{
	Use SoftDeletes;

	protected $fillable = [ 'form_number', 'form_number_disposal', 'fixed_asset_id', 'fixed_asset_name', 'disposal_date', 'officer_department', 'officer', 'remark', 'picture_before', 'picture_process', 'picture_after', 'status', 'pic_app', 'pic_app_date', 'manager_app', 'manager_app_date', 'gm_app', 'gm_app_date', 'acc_manager_app', 'acc_manager_app_date', 'director_app', 'director_app_date', 'acc_control_app', 'acc_control_app_date', 'last_status', 'reject_status', 'comment', 'created_by'
	];
}
