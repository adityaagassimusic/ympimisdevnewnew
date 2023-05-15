<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccJurnalDetail extends Model
{
    protected $fillable = [
        'id',
        'jurnal_id',
        'seq_id',
        'reference',
        'cost_center',
        'type',
        'gl_account',
        'gl_desc',
        'currency',
        'amount',
        'note',
        'created_by'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
