<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EfficiencyMonthly extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'valid_date', 'input', 'output', 'location', 'category', 'remark', 'created_by'
    ];
}
