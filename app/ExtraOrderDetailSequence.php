<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExtraOrderDetailSequence extends Model
{

    protected $fillable = [
        'eo_number_sequence',
        'eo_number',
        'eo_detail_id',
        'sequence',
        'serial_number',
        'material_number_buyer',
        'material_number',
        'description',
        'uom',
        'storage_location',
        'quantity',
        'sales_price',
        'status',
        'container_id',
        'invoice_number',
        'bl_date',
        'remark',
        'created_by',
    ];

}
