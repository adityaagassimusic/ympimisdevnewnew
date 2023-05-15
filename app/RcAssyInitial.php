<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RcAssyInitial extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'initial_code',
		'tag',
		'product',
		'material_number',
		'part_name',
		'part_type',
		'color',
		'cavity',
		'location',
		'no_kanban_injection',
		'start_injection',
		'finish_injection',
		'mesin_injection',
		'qty_injection',
		'operator_injection',
		'molding',
		'last_shot_before',
		'last_shot_injection',
		'start_molding',
		'finish_molding',
		'note_molding',
		'operator_molding',
		'material_resin',
		'dryer_resin',
		'lot_number_resin',
		'qty_resin',
		'create_resin',
		'operator_resin',
		'ng_name',
		'ng_count',
		'create_transaction',
		'location_transaction',
		'status_transaction',
		'operator_transaction',
		'status',
		'remark',
		'line',
		'ng_name_kensa',
		'ng_count_kensa',
		'created_by',

	];

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
