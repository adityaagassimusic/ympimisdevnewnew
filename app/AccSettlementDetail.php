<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccSettlementDetail extends Model
{
    protected $fillable = [
        'id_settlement','id_settlement_user','description','amount','nota','created_by','created_name'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
