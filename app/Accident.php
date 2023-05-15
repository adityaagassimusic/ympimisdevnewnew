<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Accident extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category','accident_number', 'submission_date', 'employee_id', 'employee_name', 'employee_department', 'position','location','area','date_incident','time_incident','detail_incident','condition','loss_time','recovery_time','loss_cost','illustration_image','illustration_detail','yokotenkai','status','status_foreman','created_by'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
