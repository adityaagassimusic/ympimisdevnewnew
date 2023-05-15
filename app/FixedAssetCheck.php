<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FixedAssetCheck extends Model
{
	Use SoftDeletes;

	protected $fillable = ['period','category','location','sap_number','asset_name','asset_section','asset_map','asset_images','result_images','note','availability','asset_condition','label_condition','usable_condition','map_condition','asset_image_condition','status','audit_type','result_video','check_one_by','check_one_at','check_two_by','check_two_at','appr_chief_by','appr_chief_at','appr_manager_by','appr_manager_at','remark','check_file','appr_status', 'rejected_by','rejected_at', 'comment','created_by'
];
}
