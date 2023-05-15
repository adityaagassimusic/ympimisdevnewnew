<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccidentDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'accident_id','employee_tag', 'employee_id', 'employee_name', 'attend_time', 'status', 'created_by'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
