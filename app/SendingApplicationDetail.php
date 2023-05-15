<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SendingApplicationDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'send_app_no',
        'sequence',
        'material_number',
        'description',
        'uom',
        'quantity',
        'sales_price',
        'po_number',
        'package_no',
        'package_type',
        'length',
        'height',
        'width',
        'weight',
        'created_by',
    ];

}
