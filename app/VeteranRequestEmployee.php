<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VeteranRequestEmployee extends Model
{
    protected $fillable = [
        'id', 'request_id', 'old_nik', 'name', 'address', 'no_whatsapp', 'department', 'section', 'group', 'sub_group', 'end_date', 'remark', 'updated_at', 'created_at', 'deleted_at'
    ];
}