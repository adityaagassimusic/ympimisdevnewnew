<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExtraOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_by',
        'eo_number',
        'po_by',
        'attention',
        'buyer_email',
        'division',
        'destination_code',
        'destination_name',
        'destination_shortname',
        'currency',
        'attachment',
        'po_sended_at',
        'po_uploaded_at',
        'po_number',
        'status',
        'remark',
        'eoc_status',
        'sendapp_status',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
