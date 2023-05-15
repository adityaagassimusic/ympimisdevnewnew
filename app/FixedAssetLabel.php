<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedAssetLabel extends Model
{
	Use SoftDeletes;
	protected $fillable = [ 'fixed_asset_id', 'form_number', 'fixed_asset_name', 'fixed_asset_no', 'section', 'location', 'pic', 'reason', 'approval_pic', 'approval_pic_date', 'approval_acc', 'approval_acc_date', 'approval_label_acc', 'approval_label_acc_date', 'receive_pic', 'status', 'last_status', 'remark', 'reject_status', 'comment', 'created_by'
	]; 
}
