<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EfficiencyNonProduction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'valid_date', 'activity', 'duration', 'location', 'category', 'remark', 'created_by', 'note'
    ];
}
