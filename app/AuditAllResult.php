<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditAllResult extends Model
{
    use SoftDeletes;

    protected $fillable = [
		'jenis','tanggal','kategori','auditor_id','auditor_name','lokasi','auditee','auditee_name','point_judul','point_judul_sub','foto','tanggal_target','note','penanganan','bukti_penanganan','tanggal_penanganan','status_ditangani','poin_sup','detail_poin_sup','remark','email_status','created_by'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
