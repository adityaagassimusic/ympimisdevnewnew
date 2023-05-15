<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApprovalPresidentDirectorApprove extends Model
{
    protected $connection = 'ympimis_2';
    protected $table = 'ga_president_director_approvals';
    
    protected $fillable = [
        'request_id', 'remark', 'person_id', 'person_name', 'person_email', 'status', 'approved_at', 'deleted_at', 'created_at', 'updated_at'        
    ];    
}
