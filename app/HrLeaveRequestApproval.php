<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HrLeaveRequestApproval extends Model
{
    protected $fillable = [
        'request_id', 'approver_id', 'approver_name', 'approver_email','real_approvers', 'status', 'approved_at', 'remark'
    ];
}
