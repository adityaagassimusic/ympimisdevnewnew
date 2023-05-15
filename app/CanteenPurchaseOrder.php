<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CanteenPurchaseOrder extends Model
{
    use SoftDeletes;
	
    protected $fillable = [
		'remark','no_po','no_po_sap','tgl_po','supplier_code','supplier_name','supplier_due_payment','supplier_status','material','vat','transportation','delivery_term','holding_tax','currency','buyer_id','buyer_name','note','cost_center','authorized2','authorized2_name','approval_authorized2','date_approval_authorized2','autentikasi_2','authorized3','authorized3_name','approval_authorized3','date_approval_authorized3','autentikasi_3','authorized4','authorized4_name','approval_authorized4','date_approval_authorized4','autentikasi_4','reject','datereject','file_pdf','posisi','status','revised','revised_date','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
