<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccSettlementUser extends Model
{
    protected $fillable = [
        'submission_date','title','amount','posisi','created_by','created_name'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
