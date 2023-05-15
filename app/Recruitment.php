<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recruitment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'request_id', 'position', 'department', 'employment_status', 'quantity_male', 'quantity_female', 'reason', 'start_date', 'min_age', 'max_age', 'marriage_status', 'domicile', 'work_experience', 'skill', 'educational_level', 'major', 'requirement', 'note', 'progress', 'remark', 'created_by', 'status_at', 'status_req', 'reason_reject', 'loc_penempatan', 'process_penempatan', 'posisi', 'comment_note', 'reply'
    ];
    
    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
