<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UangKeluarga extends Model
{
    protected $fillable = [
        'request_id', 'employee', 'sub_group', 'group', 'seksi', 'department', 'jabatan', 'permohonan', 'lampiran','created_by', 'created_at', 'updated_at', 'deleted_at', 'posisi', 'atasan_1', 'date_atasan_1', 'atasan_2', 'date_atasan_2', 'remark'
    ];
}
