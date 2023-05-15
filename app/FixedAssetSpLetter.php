<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedAssetSpLetter extends Model
{
	Use SoftDeletes;

	protected $fillable = ['form_number','fixed_asset_number','fixed_asset_name','acquisition_date','amount','plan_use','subject', 'subject_jp','reason','reason_jp','pic', 'period','remark','status','app_manager','app_manager_at','app_dgm','app_dgm_at','app_gm','app_gm_at','app_acc_manager','app_acc_manager_at','app_fin_dir','app_fin_dir_at','created_by'
	];
}
