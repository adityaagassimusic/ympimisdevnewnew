<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BomOutput extends Model
{
    protected $fillable = [
        'material_parent',
        'material_child',
        'usage',
        'divider',
        'uom',
        'storage_location',
        'spt',
        'valcl',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
