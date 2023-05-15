<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccReceive extends Model
{
    protected $fillable = [
		'no_po','no_pr','no_item','nama_item','qty','qty_receive','date_receive','surat_jalan','status','dokumen','tanggal_upload','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
