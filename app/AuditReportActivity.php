<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditReportActivity extends Model
{
    use SoftDeletes;

    protected $table = 'audit_report_activities';

	protected $fillable = [
        'activity_list_id',
        'audit_guidance_id',
        'department',
        'section',
        'subsection',
        'date',
        'nama_dokumen',
        'no_dokumen',
        'kesesuaian_aktual_proses',
        'tindakan_perbaikan',
        'target',
        'kelengkapan_point_safety',
        'kesesuaian_qc_kouteihyo',
        'result_qc_koteihyo',
        // 'condition',
        'handling',
        'operator',
        'leader',
        'foreman',
        'send_status',
        'send_date',
        'operator_sign',
        'approval_leader',
        'approved_date_leader',
        'approval',
        'approved_date',
        'qa_verification',
        'qa_verification_reason',
        'qa_audit_evidence',
        'qa_audit_result',
        'qa_auditor_id',
        'qa_auditor_name',
        'qa_audited_at',
        'handling_status',
        'handling_result',
        'handling_evidence',
        'handled_id',
        'handled_name',
        'handled_at',
        'audit_effectivity',
        'audit_effectivity_note',
        'auditor_effectivity_id',
        'auditor_effectivity_name',
        'audit_effectivity_at',
        'created_by',

    ];
    
    public function activity_lists()
    {
        return $this->belongsTo('App\ActivityList', 'activity_list_id', 'id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
