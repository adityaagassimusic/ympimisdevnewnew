<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InjectionVisualCheck extends Model
{
    protected $fillable = [
		'machine',
		'tag_molding',
		'material_number',
		'material_description',
		'part_name',
		'part_type',
		'color',
		'cavity',
		'molding',
		'dryer',
		'lot_number',
		'hour_check',
		'cav_detail',
		'point_check',
		'result_check',
		'note',
		'pic_check',
		'car_description',
		'car_action_now',
		'car_cause',
		'car_action',
		'car_images',
		'car_approver_id',
		'car_approver_name',
		'car_approved_at',
		'created_by'
	];
}
