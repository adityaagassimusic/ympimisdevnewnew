<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExtraOrderDetailSequenceLog extends Model
{
    protected $fillable = [
        'eo_number_sequence',
        'status',
        'created_by',
    ];
}
