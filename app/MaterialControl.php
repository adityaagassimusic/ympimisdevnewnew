<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialControl extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'material_number',
        'material_description',
        'purchasing_group',
        'controlling_group',
        'vendor_code',
        'vendor_name',
        'vendor_shortname',
        'category',
        'pic',
        'control',
        'remark',
        'multiple_order',
        'minimum_order',
        'sample_qty',
        'lead_time',
        'dts',
        'first_reminder',
        'second_reminder',
        'material_category',
        'location',
        'incoming',
        'created_by',
    ];

}
