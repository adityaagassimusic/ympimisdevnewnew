<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scrap extends Model
{
	'scrap_id', 
	'material_number', 
	'material_description', 
	'issue_location', 
	'receive_location', 
	'quantity', 
	'remark',
	'created_at'
}
