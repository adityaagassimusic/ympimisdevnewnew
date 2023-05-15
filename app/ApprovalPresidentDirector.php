<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApprovalPresidentDirector extends Model
{
    protected $connection = 'ympimis_2';
    protected $table = 'ga_president_director';

    // fillable
    protected $fillable = [        
        'request_id', 'status', 'applicant', 'department', 'department_shortname', 'document_name', 'recipient', 'hardcopy_total','purpose', 'remark', 'created_by', 'deleted_at', 'created_at', 'updated_at'
    ];    
    
}
