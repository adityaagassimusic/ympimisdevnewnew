<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseLog extends Model
{
    Use SoftDeletes;

	protected $fillable = [
		'lokasi_kirim','kode_request','sloc_name','loc','gmc','description','uom','no_hako','qty_req','qty_kirim','pic_request','pic_pelayanan','pic_pengantaran','remark','created_by','lot','tanggal','status'
	];
		
}
