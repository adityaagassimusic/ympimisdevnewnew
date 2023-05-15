<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccSettlement extends Model
{
    
    protected $fillable = [
        'submission_date','title','amount','file','pdf','posisi','status','manager','manager_name','status_manager','staff_acc','staff_acc_name','status_staff_acc','manager_acc','manager_acc_name','status_manager_acc','direktur','status_direktur','alasan','datereject','created_by','created_name','department','sudah_settle'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }
}
