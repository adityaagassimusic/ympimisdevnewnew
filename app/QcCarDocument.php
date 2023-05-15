<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QcCarDocument extends Model
{
     use SoftDeletes;

    protected $fillable = [
        'id','cpar_no','detail','nomor_dokumen','dokumen','due_date','file','created_by'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
