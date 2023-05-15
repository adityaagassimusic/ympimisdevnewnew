<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccidentYokotenkai extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'accident_id','department','group','pekerjaan_serupa','pekerjaan_serupa_detail', 'pekerjaan_serupa_foto', 'peralatan_sejenis', 'peralatan_sejenis_detail', 'peralatan_sejenis_foto', 'standar_k3','kaizen','kaizen_detail','kaizen_sebelum','kaizen_sesudah','tanggal_pengecekan','status', 'created_by'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
