<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccItem extends Model
{
    protected $fillable = [
		'kode_item','kategori','deskripsi','uom','spesifikasi','harga','lot','moq','leadtime','currency','remark','peruntukan','kebutuhan','quotation','date_quotation','created_by','img_kode_item'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
