<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SendingApplication extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'category',
        'send_app_no',
        'document_number',
        'attention',
        'destination_code',
        'division',
        'payment_term',
        'shipment_by',
        'freight',
        'condition',
        'sent_email',
        'status',
        'st_date',
        'bl_date',
        'invoice_number',
        'way_bill',
        'note',
        'created_by',
    ];

}
