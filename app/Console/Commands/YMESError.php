<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class YMESError extends Command
{
    protected $signature = 'ymes:error';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $e_ext_0010 = db::connection('ymes')->table('e_ext0010')
            ->leftJoin('vm_item0010', 'vm_item0010.item_code', '=', 'e_ext0010.item_code')
            ->select([
                'vm_item0010.item_name',
                'e_ext0010.send_data_no',
                'e_ext0010.serial_no',
                'e_ext0010.item_code',
                'e_ext0010.input_type',
                'e_ext0010.qty',
                'e_ext0010.end_work_datetime',
                'e_ext0010.man_stat_cd',
                'e_ext0010.dest_location_code',
                'e_ext0010.err_code',
            ])
            ->orderBy('proc_date', 'ASC')->get();
        $e_ext_0020 = db::connection('ymes')->table('e_ext0020')
            ->leftJoin('vm_item0010', 'vm_item0010.item_code', '=', 'e_ext0020.item_code')
            ->select([
                'vm_item0010.item_name',
                'e_ext0020.send_data_no',
                'e_ext0020.result_date',
                'e_ext0020.issue_loc_code',
                'e_ext0020.in_loc_code',
                'e_ext0020.qty',
                'e_ext0020.item_code',
                'e_ext0020.serial_no',
                'e_ext0020.err_code',
            ])
            ->orderBy('proc_date', 'ASC')->get();
        // $e_ext_1010 = db::connection('ymes')->table('e_ext1010')->orderBy('proc_date', 'ASC')->limit(5)->get();

        $pr_error_ids = array();
        $gm_error_ids = array();
        $rs_error_ids = array();

        if (count($e_ext_0010) > 0) {
            foreach ($e_ext_0010 as $row) {
                array_push($pr_error_ids, $row->send_data_no);
                $quantity = $row->qty;
                if ($row->input_type == '2') {
                    $quantity = $row->qty * -1;
                }

                $pr_insert = db::connection('ympimis_2')->table('production_results')->insert([
                    'category' => 'production_result_error',
                    'function' => 'handle',
                    'action' => 'production_result',
                    'result_date' => $row->end_work_datetime,
                    'reference_number' => $row->send_data_no,
                    'slip_number' => $row->err_code,
                    'serial_number' => $row->serial_no,
                    'material_number' => $row->item_code,
                    'material_description' => $row->item_name,
                    'issue_location' => $row->dest_location_code,
                    'mstation' => $row->man_stat_cd,
                    'quantity' => $quantity,
                    'synced' => null,
                    'synced_by' => null,
                    'remark' => 'YMES',
                    'created_by' => 'System',
                    'created_by_name' => 'System',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            foreach ($pr_error_ids as $row2) {
                $pr_delete = db::connection('ymes')->table('e_ext0010')->where('send_data_no', '=', $row2)->delete();
            }
        }

        if (count($e_ext_0020) > 0) {
            foreach ($e_ext_0020 as $row) {
                array_push($gm_error_ids, $row->send_data_no);

                $gm_insert = db::connection('ympimis_2')->table('goods_movements')->insert([
                    'category' => 'goods_movement_error',
                    'function' => 'handle',
                    'action' => 'goods_movement',
                    'result_date' => $row->result_date,
                    'reference_number' => $row->send_data_no,
                    'slip_number' => $row->err_code,
                    'serial_number' => $row->serial_no,
                    'material_number' => $row->item_code,
                    'material_description' => $row->item_name,
                    'issue_location' => $row->issue_loc_code,
                    'receive_location' => $row->in_loc_code,
                    'quantity' => $row->qty,
                    'synced' => null,
                    'synced_by' => null,
                    'remark' => 'YMES',
                    'created_by' => 'System',
                    'created_by_name' => 'System',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            foreach ($gm_error_ids as $row2) {
                $gm_delete = db::connection('ymes')->table('e_ext0020')->where('send_data_no', '=', $row2)->delete();
            }
        }

        // foreach($rs_error_ids as $row){
        //     $rs_delete = db::connection('ymes')->table('e_ext1010')->where('send_data_no', '=', $row)->delete();
        // }
    }
}
