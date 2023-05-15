<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OvertimeExtrafoodAttendance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id','name','section','attend_date', 'created_by','time_in','remark','shift','dates','shiftdaily_code','ot_from','ot_to','status','id'
    ];
}
