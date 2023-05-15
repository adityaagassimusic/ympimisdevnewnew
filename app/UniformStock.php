<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniformStock extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'gender', 'category', 'size', 'qty', 'created_by'
    ];
}
