<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class KanagataGmcMaster extends Model
{
    protected $fillable = [
        'gmc_material', 'prod', 'gmc_material', 'desc_material', 'part_name', 'die_number', 'part','lifetime','price'
    ];
}
