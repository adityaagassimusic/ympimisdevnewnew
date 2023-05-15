<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ToolsBom extends Model
{
    protected $fillable = [
        // 'tool','due_date','material_number','material_description','stock','qty_target','created_by'
        'gmc_parent','gmc_desc_parent','gmc_component','gmc_desc_component','base_unit','unit','location','tools_item','tools_description','usage','created_by'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
