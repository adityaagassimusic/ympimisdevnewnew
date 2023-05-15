<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class MisInventoryResult extends Model
{
    protected $fillable = [
        'checklist_id', 'no_po', 'category', 'nama_item', 'qty', 'pic_pengambil_nik', 'pic_pengambil_name','pic_mis_nik','pic_mis_name','receive_date','note','no_seri','date_to','id_data','peruntukan','st_update','location'
    ];
}
