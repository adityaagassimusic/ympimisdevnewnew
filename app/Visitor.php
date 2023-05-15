<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visitor extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'company', 'purpose', 'detail_purpose', 'status', 'location', 'destination', 'jumlah', 'remark', 'employee', 'reason', 'transport', 'pol', 'remark_manager', 'send_email_manager',
    ];
}
