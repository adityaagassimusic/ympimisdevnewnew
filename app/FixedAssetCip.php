<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedAssetCip extends Model
{
	Use SoftDeletes;

	protected $fillable = ['form_number','sap_number','fixed_asset_name','acquisition_date','amount_usd','plan_use','usage_term','usage_estimation','pic','department','period','clasification_category','clasification','usefull_life','remark','status', 'manager_appr', 'manager_appr_at', 'acc_appr', 'acc_appr_at', 'fa_receive', 'fa_receive_at', 'created_by'
	]; 


}
