<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MpKanagataLog extends Model
{

	protected $fillable = [
		'date',
        'pic',
        'shift',
        'product',
        'shift',
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


        'punch_total',
        'die_total',
        'plate_total',
        'ppl_total',
        'dp_total',
        'dd_total',
        'snap_total',
        'lower_total',
        'upper_total',
        'half_total',
        'dinsert_total',

        'punch_status',
        'die_status',
        'plate_status',
        'ppl_status',
        'dp_status',
        'dd_status',
        'snap_status',
        'lower_status',
        'upper_status',
        'half_status',
        'dinsert_status',

        'start_time',
        'end_time',
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
