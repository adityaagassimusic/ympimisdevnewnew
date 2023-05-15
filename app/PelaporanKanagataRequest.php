<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PelaporanKanagataRequest extends Model
{
    protected $fillable = [
        'request_id', 'tanggal_kejadian', 'gmc_material', 'desc_material', 'no_die', 'making_date', 'total_shoot','spare_die','forging_ke','die_high','limit_preasure','peak','cavity','ng_sanding','repair','waktu_repair','foto_kanagata','detail_foto_kanagata','foto_defect_material','detail_foto_defect_material','created_by','remark','position','decision','type_die','comment','status_shoot','condition_material_repair','detail_condition_material_repair','lifetime','process_type','part_name','retak_ke','problem_desc','comment_users'
    ];
}
