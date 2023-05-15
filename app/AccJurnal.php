<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccJurnal extends Model
{
    protected $fillable = [
        'jurnal_date',
        'supplier_code',
        'supplier_name',
        'bank_id',
        'bank_branch',
        'bank_beneficiary_name',
        'bank_beneficiary_no',
        'currency',
        'swift_code',
        'internal',
        'foreign',
        'bank_charge',
        'invoice',
        'remark',
        'exchange_method',
        'contract_number',
        'iban',
        'purpose_remit',
        'id_payment',
        'amount_bank_charge',
        'amount',
        'created_by',
        'created_name'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
