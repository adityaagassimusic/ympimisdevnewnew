<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class QaKensaLog extends Model
{
	protected $fillable = [
		'incoming_check_code',
		'location',
		'inspector_id',
		'material_number',
		'material_description',
		'qty_production',
		'qty_check',
		'inspection_level',
		'repair',
		'scrap',
		'total_ok',
		'total_ng',
		'ng_ratio',
		'report_evidence',
		'send_email_status',
		'send_email_at',
		'hpl',
		'note_all',
		'created_by',

	];
}

