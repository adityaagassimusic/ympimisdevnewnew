<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SendingApplicationLog extends Model
{
    protected $fillable = [
        'send_app_no',
        'status',
        'created_by',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

}
