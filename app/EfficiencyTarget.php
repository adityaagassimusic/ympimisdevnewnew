<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EfficiencyTarget extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'valid_date', 'target', 'location', 'category', 'remark', 'created_by'
    ];
}
