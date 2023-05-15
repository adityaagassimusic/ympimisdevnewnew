<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccPaymentRequestDetail extends Model
{
    protected $fillable = [
		'id_payment','id_invoice','invoice','amount','ppn','typepph','amount_service','pph','net_payment','acc_payment','acc_date_payment','status_jurnal','status_list_payment','created_by','created_name'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
