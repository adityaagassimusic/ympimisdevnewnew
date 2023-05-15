<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Approver extends Model
{
    protected $fillable = [
        'department', 'section', 'approver_id', 'approval_name', 'approval_email', 'position', 'remark', 'created_by', 'deleted_at', 'created_at', 'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
