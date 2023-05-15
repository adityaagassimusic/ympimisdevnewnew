<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialPlanDelivery extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'po_number',
        'item_line',
        'material_number',
        'vendor_code',
        'po_send',
        'po_send_at',
        'po_confirm',
        'po_confirm_at',
        'po_reminder_at',
        'send_reminder_confirm_at',
        'reminder_confirm_at',
        'issue_date',
        'eta_date',
        'due_date',
        'quantity',
        'plan',
        'actual',
        'note',
        'status',
        'do_number',
        'invoice',
        'receive_report',
        'bc_document',
        'sppb',
        'bc_send_at',
        'created_by',
    ];
}
