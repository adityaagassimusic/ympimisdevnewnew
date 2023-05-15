<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QcCpar extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'cpar_no','kategori','employee_id','lokasi','tgl_permintaan','tgl_balas','judul_komplain','kategori_komplain','file','via_komplain','department_id','sumber_komplain','status_code','destination_code','vendor','penemu_ng','email_status','email_send_date','staff','leader','chief','foreman','manager','dgm','gm','dgm_car','gm_car','posisi','checked_chief','checked_foreman','checked_manager','approved_dgm','approved_gm','received_manager','alasan','progress','created_by','tindakan','cost','yokotenkai','kategori_ng','kategori_approval','kategori_meeting','notulen_meeting'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}

	public function verifikasi()
	{
		return $this->belongsTo('App\QcVerifikasi', 'cpar_no', 'cpar_no')->withTrashed();
	}
}
