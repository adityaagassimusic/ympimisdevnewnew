<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccBudgetNote extends Model
{
    use SoftDeletes;
    
    protected $fillable = [ 
        'department','account_name','month_date','note','created_by'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
