<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RcBoxResult extends Model
{
    protected $fillable = [
		'operator_kensa',
		'check_date',
		'qty_box',
		'product',
		'created_by'];
}
