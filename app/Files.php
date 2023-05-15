<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    protected $connection = 'ympimis_2';
    protected $table = 'file_manager';

    protected $fillable = [
        'id','file_name','file_size','file_extension','file_url','remark','uploader'
    ];
    
}