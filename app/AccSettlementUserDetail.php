<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccSettlementUserDetail extends Model
{
    protected $fillable = [
        'id_settlement','description','amount','nota','sudah_settle','created_by','created_name'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
