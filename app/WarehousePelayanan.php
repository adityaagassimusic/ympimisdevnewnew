<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehousePelayanan extends Model
{
    Use SoftDeletes;

	protected $fillable = [
		'gmc','description','lot','quantity_request','kode_request','created_by','status_pel','area','status_pengantaran','status_material','tanggal','pic_produksi','end_pel','quantity_check','pic_pelayanan','pic_pengantaran','start_pengantaran','end_pengantaran','sloc_name','loc','no_hako','area_code','status_mt','lokasi_material','lokasi_pengecekan','lokasi_produksi','lokasi_internal','status_all','status_aktual','employee_id_pelayanan','employee_id_pengantaran','area','sloc_name','uom','status','pic_received','qty_req','leader_app','status_approve','pic_approve','reason_urgent','pic_reject','reason_urgent_in','remark'
	];
		
}
