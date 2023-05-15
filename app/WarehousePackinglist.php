<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehousePackinglist extends Model
{
    Use SoftDeletes;

	protected $fillable = [
		'gmc', 'country', 'description','number_package','quantity','no_case','created_by','vendor','status_job','status_cek','status_receive','quantity_check','pic_job','pic_cek','pic_receive','status_job','start_check','end_check','start_move','end_move','status_material','no_delivery_order','status_all','tanggal_kedatangan', 'no_invoice','tanggal','status_exim','tanggal_kedatangan_aktual','status_aktual','employee_id','no_surat_jalan','lokasi_material','status_emp','status','pic_drop','package'
	];
}
