<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChemicalControlLog extends Model
{

    protected $fillable = [
        'date', 'solution_name', 'location', 'target_max', 'target_warning', 'note', 'quantity', 'accumulative', 'created_by',
    ];

}
