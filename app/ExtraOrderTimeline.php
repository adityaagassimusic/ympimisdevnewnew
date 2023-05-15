<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExtraOrderTimeline extends Model
{
    protected $fillable = [
        'eo_number',
        'timeline_item_icon',
        'timeline_item',
        'timeline_header',
        'timeline_body',
        'timeline_footer',
        'remark',
        'created_by',
    ];
}
