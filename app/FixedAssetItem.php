<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedAssetItem extends Model
{
	Use SoftDeletes;

	protected $fillable = ['sap_number','fixed_asset_name','invoice_number', 'invoice_name','classification_code','classification_category','vendor','currency','original_amount','amount_usd','section','pic','location','investment','budget_number','usage_term','usage_estimation','category_code','category','depreciation_key', 'sap_file','usefull_life','request_date','picture', 'status','registration_status','remark','created_by', 'new_number'

];
}
