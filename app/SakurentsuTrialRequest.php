<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SakurentsuTrialRequest extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'form_number','subject', 'submission_date', 'requester', 'requester_name','trial_to','trial_to_name','department','section','trial_date','sakurentsu_number','trial_purpose','material','material_total','trial_location','trial_detail','trial_before','trial_info','chief', 'chief_date','manager', 'manager_date','manager_mechanical','manager_mechanical_date','dgm','dgm_date','gm', 'gm_date', 'dgm2', 'dgm_date2', 'gm2', 'gm_date2','reject','reject_reason','reject_date','position','status', 'status_bom', 'status_price' ,'qc_report_file' ,'qc_report_upload_date' ,'qc_report_status', 'qc_report_uploaded_by','created_by' ,'app_pic_receive' ,'app_pic_receive_date' ,'app_chief_receive' ,'app_chief_receive_date' ,'app_manager_receive' ,'app_manager_receive_date' ,'app_pic_request' ,'app_pic_request_date' ,'app_chief_request' ,'app_chief_request_date' ,'app_manager_request' ,'app_manager_request_date' ,'app_dgm' ,'app_dgm_date' ,'app_gm' ,'app_gm_date' ,'app_gm2' ,'app_gm2_date', 'three_m_status', 'apd_material', 'att'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
