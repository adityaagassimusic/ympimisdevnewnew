<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccInvoice extends Model
{
    protected $fillable = [
		'category','invoice_date','supplier_code','supplier_name','kwitansi','invoice_no','surat_jalan','bap','npwp','faktur_pajak','po_number','detail_item','payment_term','currency','amount','mirai_amount','do_date','due_date','distribution_date','payment_status','file','created_by','created_name'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
