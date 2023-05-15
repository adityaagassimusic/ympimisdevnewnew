<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApprApprovals extends Model
{
    protected $fillable = [
        'request_id', 'approver_id', 'approver_name', 'approver_email', 'status', 'approved_at', 'remark', 'header'
    ];
}
