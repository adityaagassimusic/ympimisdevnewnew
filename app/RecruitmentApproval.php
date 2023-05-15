<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecruitmentApproval extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'request_id', 'approver_id', 'approver_name', 'approver_email', 'status', 'approved_at', 'remark'
    ];
}
