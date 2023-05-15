<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccPurchaseRequisition extends Model
{
	// use SoftDeletes;

    protected $fillable = [
		'no_pr','emp_id','emp_name','department','section','submission_date','po_due_date','receive_date','file','file_pdf','note','posisi','status','no_budget','staff','manager','manager_name','dgm','gm','approvalm','dateapprovalm','approvaldgm','dateapprovaldgm','approvalgm','dateapprovalgm','alasan','datereject','comment','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}

	public function budget()
	{
		return $this->belongsTo('App\AccBudget', 'no_budget', 'budget_no')->withTrashed();
	}
	
}
