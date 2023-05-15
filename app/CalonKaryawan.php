<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CalonKaryawan extends Model
{
    protected $fillable = [
        'request_id', 'nama', 'asal', 'created_by', 'created_at', 'updated_at', 'deleted_at', 'remark', 'test_tpa', 'interview_awal', 'interview_user', 'test_psikotest', 'test_kesehatan', 'interview_management', 'induction', 'no_hp', 'institusi', 'email'
    ];
}
