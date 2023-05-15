<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CalonRekontrak extends Model
{
    protected $fillable = [
        'id', 'request_id', 'nik', 'nama', 'penempatan', 'process_penempatan', 'durasi', 'habis_kontrak', 'tpa', 'kesehatan', 'remark', 'created_at', 'updated_at', 'deleted_at'
    ];
}
