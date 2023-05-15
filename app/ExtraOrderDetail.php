<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExtraOrderDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'eo_number',
        'sequence',
        'material_number_buyer',
        'material_number',
        'description',
        'uom',
        'storage_location',
        'quantity',
        'production_quantity',
        'shipment_quantity',
        'sales_price',
        'shipment_by',
        'urgent',
        'request_date',
        'due_date',
        'st_date',
        'payment_term',
        'invoice_number',
        'bl_date',
        'container_id',
        'status',
        'remark',
        'note',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
