<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RcNgBox extends Model
{
    protected $fillable = [
		'date',
		'tray',
		'ng_head',
		'ng_middle',
		'ng_foot',
		'ng_block',
		'created_by'];
}
