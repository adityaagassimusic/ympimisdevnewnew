<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FixedAssetMissing extends Model
{
	Use SoftDeletes;

	protected $fillable = [ 'form_number', 'request_date', 'fixed_asset_id', 'fixed_asset_name', 'clasification', 'section_control', 'new_picture', 'reason', 'missing_reason', 'improvement_plan', 'acquisition_cost', 'acquisition_date', 'book_value', 'status', 'retire_date', 'remark', 'pic_app', 'pic_app_date', 'fa_app', 'fa_app_date', 'manager_app', 'manager_app_date', 'gm_app', 'gm_app_date', 'acc_manager_app', 'acc_manager_app_date', 'director_app', 'director_app_date', 'presdir_app', 'presdir_app_date', 'upload_doc_date', 'acc_manager_doc_date', 'reject_status', 'comment', 'last_status', 'created_by'
];
}
