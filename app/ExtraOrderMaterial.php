<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExtraOrderMaterial extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'material_number',
        'material_number_buyer',
        'description',
        'uom',
        'storage_location',
        'eo_number',
        'reference_form_number',
        'status',
        'remark',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
