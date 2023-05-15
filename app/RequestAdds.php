<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestAdds extends Model
{
    protected $fillable = [
        'id', 'month', 'department', 'section', 'group', 'sub_group', 'count', 'remark', 'created_by'
    ];
}
