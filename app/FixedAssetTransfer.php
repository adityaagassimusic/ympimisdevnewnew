<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedAssetTransfer extends Model
{
	Use SoftDeletes;

	protected $fillable = [
		'form_number','fixed_asset_id', 'fixed_asset_name', 'fixed_asset_no', 'old_section', 'old_location', 'old_pic', 'new_section', 'new_location', 'new_pic', 'transfer_reason', 'new_picture', 'approval_pic', 'approval_pic_date', 'approval_manager', 'approval_manager_date', 'approval_new_pic', 'approval_new_pic_date', 'approval_new_manager', 'approval_new_manager_date', 'approval_acc_manager', 'approval_acc_manager_date', 'receive_acc', 'receive_acc_date', 'remark', 'status', 'reject_status', 'comment', 'last_status', 'created_by'
	]; 

}
