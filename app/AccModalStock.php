<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccModalStock extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'no_po','no_pr','no_item','nama_item','qty','qty_receive','date_receive','pic_receive','pic_date_receive','created_by'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
