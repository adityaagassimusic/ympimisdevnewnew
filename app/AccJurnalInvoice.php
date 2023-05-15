<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccJurnalInvoice extends Model
{
    protected $fillable = [
        'id',
        'jurnal_id',
        'payment_id',
        'supplier_name',
        'invoice_no',
        'currency',
        'amount',
        'ppn',
        'pph',
        'net_payment',
        'created_by'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
