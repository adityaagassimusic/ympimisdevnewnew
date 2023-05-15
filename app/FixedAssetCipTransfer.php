<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class FixedAssetCipTransfer extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'cip_form_number',
		'cip_sap_number',
		'cip_asset_name',
		'new_sap_number',
		'new_asset_name',
		'invoice_number',
		'invoice_name',
		'clasification_code',
		'clasification_category',
		'investment_number',
		'budget_number',
		'vendor',
		'currency',
		'amount_use',
		'cip_amount_usd',
		'amount_usd',
		'pic',
		'location',
		'category_code',
		'category_name',
		'registration_date',
		'depreciation_key',
		'usefull_life',
		'sap_file',
		'transfer_date',
		'transfer_by',
		'status',
		'remark',
		'created_by',
	]; 
}
