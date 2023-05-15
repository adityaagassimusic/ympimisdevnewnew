<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RcQaAudit extends Model
{
    protected $fillable = [
		'date',
		'product',
		'auditor',
		'auditee',
		'kensa_code',
		'pic_injection',
		'defect',
		'area',
		'category',
		'image',
		'counceled_employee',
		'counceled_by',
		'counceled_at',
		'counceled_image',
		'car_description_rc',
		'car_action_now_rc',
		'car_cause_rc',
		'car_action_rc',
		'car_approver_id_rc',
		'car_manager_id_rc',
		'car_approver_name_rc',
		'car_manager_name_rc',
		'car_approved_at_rc',
		'car_approved_at_manager_rc',
		'car_description_inj',
		'car_action_now_inj',
		'car_cause_inj',
		'car_action_inj',
		'car_approver_id_inj',
		'car_manager_id_inj',
		'car_approver_name_inj',
		'car_manager_name_inj',
		'car_approved_at_inj',
		'car_approved_at_manager_inj',

		'created_by',
	];
}
