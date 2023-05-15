<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MpRecordProd extends Model
{
    use SoftDeletes;

	protected $fillable = [
		'date',
        'pic',
        'shift', 'product',
        'material_number',
        'process',
        'machine',
        'punch_number',
        'die_number',
        'plate_number',
        'ppl_number',
        'dp_number',
        'dd_number',
        'snap_number',
        'lower_number',
        'upper_number',
        'half_number',
        'dinsert_number',
        'start_time',
        'end_time',
        'lepas_molding',
        'pasang_molding',
        'process_time',
        'kensa_time',
        'electric_supply_time',
        'data_ok',
        'punch_value',
        'die_value',
        'plate_value',
        'ppl_value',
        'dp_value',
        'dd_value',
        'snap_value',
        'lower_value',
        'upper_value',
        'half_value',
        'dinsert_value',
        'note',
        'created_by'
	];

	public function employee_pic()
    {
        return $this->belongsTo('App\Employee', 'pic', 'employee_id')->withTrashed();
    }

    public function material()
    {
        return $this->belongsTo('App\MpMaterial', 'material_number', 'material_number')->withTrashed();
    }

    public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
