<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssemblyInventory extends Model
{
    protected $fillable = [
		'tag', 'serial_number', 'model', 'location','location_number','location_next','remark','status_material','origin_group_code','trial','after_packing','created_by'
	];
}
