<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PelaporanKanagataApproval extends Model
{
    protected $fillable = [
        'request_id', 'approver_id', 'approver_name', 'approver_email', 'status', 'approved_at', 'remark','position','comment'
    ];
}
