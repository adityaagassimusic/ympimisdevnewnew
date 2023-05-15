<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccSuspendDetail extends Model
{
    protected $fillable = [
        'id_suspend','no_pr','detail','amount','received_at','settle','created_by','created_name'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
