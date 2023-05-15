<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedAssetRegistration extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'form_number', 'asset_id','asset_name', 'invoice_number', 'invoice_name', 'clasification_id', 'vendor', 'currency', 'amount', 'amount_usd', 'pic', 'pic_control', 'location', 'investment_number', 'budget_number', 'usage_term', 'usage_estimation', 'sap_file', 'status', 'asset_picture', 'request_date', 'category_code', 'category', 'sap_id', 'depreciation_key', 'remark', 'manager_app', 'manager_app_date', 'manager_acc', 'manager_acc_date', 'update_fa_at','reject_status', 'comment', 'last_status', 'created_by'
	];
}
