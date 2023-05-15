<?php

namespace App\Console\Commands;

use Excel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ScheduleYMESDay extends Command
{

    protected $signature = 'reminder:scheduledownloadday';
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $from = date('Y-m-d', strtotime("-1 days"));
        $to = date('Y-m-d', strtotime("-1 days"));
        $date_yest = date('Y-m-d', strtotime("-1 days"));
        $date = date('Y-m-d Hi', strtotime("-1 days"));
        $date3 = date('Hi');

        $hour_from = [
            ' 00:00:01',
            ' 10:00:01',
            ' 15:00:01',
            ' 19:00:01',
        ];

        $hour_to = [
            ' 10:00:00',
            ' 15:00:00',
            ' 19:00:00',
            ' 24:00:00',
        ];

        // YMES TRX
        for ($i = 0; $i < count($hour_from); $i++) {
            $out = db::connection('ymes')
            ->table('vd_mes0020')
            ->where('instdt', '>=', $from . $hour_from[$i])
            ->where('instdt', '<', $to . $hour_to[$i])
            ->whereNotNull('item_code')
            ->get();

            $dates1 = $from . $hour_from[$i];
            $dates2 = $hour_to[$i];

            $date1 = date('Y-m-d Hi', strtotime($dates1));
            $date2 = date('Hi', strtotime($dates2));

            $data = [
                'data_ymes' => $out,
                'date' => $date1,
                'date2' => $date2,
                'dates' => $date3,
            ];

            Excel::create('YMES ' . $data['date'] . ' ' . $data['date2'] . ' ' . $data['dates'], function ($excel) use ($data) {
                $excel->sheet('YMES ' . $data['date'] . ' ' . $data['date2'] . ' ' . $data['dates'], function ($sheet) use ($data) {
                    return $sheet->loadView('extra_order.download_data_ymes', $data);
                });
            })->store('xlsx', public_path('files/pc_dowload_data/ymes'));

        }

        // MIRAI TRX
        $pr = db::connection('ympimis_2')
        ->table('production_results')
        ->where('created_at', 'LIKE', '%' . substr($from, 0, 10) . '%')
        ->where('category', 'NOT LIKE', '%error%')
        ->whereNull('deleted_at')
        ->get();

        $gm = db::connection('ympimis_2')
        ->table('goods_movements')
        ->where('created_at', 'LIKE', '%' . substr($from, 0, 10) . '%')
        ->where('category', 'NOT LIKE', '%error%')
        ->whereNull('deleted_at')
        ->get();

        $data1 = [
            'data_pr' => $pr,
            'data_gm' => $gm,
            'date' => $date,
        ];

        Excel::create('MIRAI ' . $data1['date'], function ($excel) use ($data1) {
            $excel->sheet('MIRAI PRD' . $data1['date'], function ($sheet) use ($data1) {
                return $sheet->loadView('extra_order.download_data_mirai_prd', $data1);
            });

            $excel->sheet('MIRAI GDMV ' . $data1['date'], function ($sheet1) use ($data1) {
                return $sheet1->loadView('extra_order.download_data_mirai_good_mv', $data1);
            });

        })->store('xlsx', public_path('files/pc_dowload_data/mirai'));

        // MIRAI TEMP
        $pr_temps = db::connection('ympimis_2')
        ->table('production_result_temps')
        ->whereNull('deleted_at')
        ->get();

        $data3 = [
            'data_pr_temps' => $pr_temps,
            'date' => $date
        ];

        Excel::create('MIRAI_PRST_TMP ' . $data3['date'], function ($excel) use ($data3) {
            $excel->sheet('MIRAI_PRST_TMP ' . $data3['date'], function ($sheet) use ($data3) {
                return $sheet->loadView('extra_order.download_data_mirai_prd_temp', $data3);
            });

        })->store('xlsx', public_path('files/pc_dowload_data/mirai'));

        // ERROR TRX
        $lists_error_prd = [];
        $lists_error_gmv = [];

        $prd_rslt_error = db::connection('ympimis_2')
        ->table('production_results')
        ->where('synced', '=', null)
        ->where('category', 'LIKE', '%error%')
        ->whereNull('deleted_at')
        ->get();

        $ymes_error = db::connection('ymes')
        ->table('e_ext0010')
        ->get();

        if (count($prd_rslt_error) > 0) {

            for ($i = 0; $i < count($prd_rslt_error); $i++) {
                array_push($lists_error_prd, [
                    'category' => $prd_rslt_error[$i]->category,
                    'result_date' => $prd_rslt_error[$i]->result_date,
                    'slip_number' => $prd_rslt_error[$i]->slip_number,
                    'serial_number' => $prd_rslt_error[$i]->serial_number,
                    'material_number' => $prd_rslt_error[$i]->material_number,
                    'material_description' => $prd_rslt_error[$i]->material_description,
                    'issue_location' => $prd_rslt_error[$i]->issue_location,
                    'mstation' => $prd_rslt_error[$i]->mstation,
                    'quantity' => $prd_rslt_error[$i]->quantity,
                    'synced' => $prd_rslt_error[$i]->synced,
                    'synced_by' => $prd_rslt_error[$i]->synced_by,
                    'created_by' => $prd_rslt_error[$i]->created_by,
                    'created_by_name' => $prd_rslt_error[$i]->created_by_name,
                    'created_at' => $prd_rslt_error[$i]->created_at,
                    'updated_at' => $prd_rslt_error[$i]->updated_at,
                ]);
            }
        }

        if (count($ymes_error) > 0) {


            for ($j = 0; $j < count($ymes_error); $j++) {
                array_push($lists_error_prd, [
                    'category' => 'production_result_error',
                    'result_date' => $ymes_error[$j]->proc_date,
                    'slip_number' => '',
                    'serial_number' => $ymes_error[$j]->serial_no,
                    'material_number' => $ymes_error[$j]->item_code,
                    'material_description' => $ymes_error[$j]->item_code,
                    'issue_location' => $ymes_error[$j]->dest_location_code,
                    'mstation' => "",
                    'quantity' => $ymes_error[$j]->qty,
                    'synced' => $ymes_error[$j]->instdt,
                    'synced_by' => $ymes_error[$j]->instid,
                    'created_by' => $ymes_error[$j]->updtid,
                    'created_by_name' => $ymes_error[$j]->updtid,
                    'created_at' => $ymes_error[$j]->updtdt,
                    'updated_at' => $ymes_error[$j]->updtdt,
                ]);

            }

        }


        $gd_mv_error = db::connection('ympimis_2')
        ->table('goods_movements')
        ->where('synced', '=', null)
        ->where('category', 'LIKE', '%error%')
        ->whereNull('deleted_at')
        ->get();

        $ymes_error_gd_mv = db::connection('ymes')
        ->table('e_ext0020')
        ->get();

        if (count($gd_mv_error) > 0) {

            for ($k = 0; $k < count($gd_mv_error); $k++) {
                array_push($lists_error_gmv, [
                    'category' => $gd_mv_error[$k]->category,
                    'result_date' => $gd_mv_error[$k]->result_date,
                    'slip_number' => $gd_mv_error[$k]->slip_number,
                    'serial_number' => $gd_mv_error[$k]->serial_number,
                    'material_number' => $gd_mv_error[$k]->material_number,
                    'material_description' => $gd_mv_error[$k]->material_description,
                    'issue_location' => $gd_mv_error[$k]->issue_location,
                    'receive_location' => $gd_mv_error[$k]->receive_location,
                    'quantity' => $gd_mv_error[$k]->quantity,
                    'synced' => $gd_mv_error[$k]->synced,
                    'synced_by' => $gd_mv_error[$k]->synced_by,
                    'created_by' => $gd_mv_error[$k]->created_by,
                    'created_by_name' => $gd_mv_error[$k]->created_by_name,
                    'created_at' => $gd_mv_error[$k]->created_at,
                    'updated_at' => $gd_mv_error[$k]->updated_at,
                ]);
            }

        }
        if (count($ymes_error_gd_mv) > 0) {

            for ($l = 0; $l < count($ymes_error_gd_mv); $l++) {
                array_push($lists_error_gmv, [
                    'category' => 'goods_movement_error',
                    'result_date' => $ymes_error_gd_mv[$l]->result_date,
                    'slip_number' => '',
                    'serial_number' => $ymes_error_gd_mv[$l]->serial_no,
                    'material_number' => $ymes_error_gd_mv[$l]->item_code,
                    'material_description' => $ymes_error_gd_mv[$l]->item_code,
                    'issue_location' => $ymes_error_gd_mv[$l]->issue_loc_code,
                    'receive_location' => $ymes_error_gd_mv[$l]->in_loc_code,
                    'quantity' => $ymes_error_gd_mv[$l]->qty,
                    'synced' => $ymes_error_gd_mv[$l]->instdt,
                    'synced_by' => $ymes_error_gd_mv[$l]->instid,
                    'created_by' => $ymes_error_gd_mv[$l]->updtid,
                    'created_by_name' => $ymes_error_gd_mv[$l]->updtid,
                    'created_at' => $ymes_error_gd_mv[$l]->updtdt,
                    'updated_at' => $ymes_error_gd_mv[$l]->updtdt,
                ]);
            }
        }


        $data4 = [
            'lists_error_prd' => $lists_error_prd,
            'lists_error_gmv' => $lists_error_gmv,
            'date' => $date,
        ];

        Excel::create('ERROR_TRANSAKSI ' . $data4['date'], function ($excel) use ($data4) {
            $excel->sheet('ERR_TRS_PRD_RSLT' . $data4['date'], function ($sheet) use ($data4) {
                return $sheet->loadView('extra_order.download_error_transk_prd', $data4);
            });
            $excel->sheet('ERR_TRS_GD_MV ' . $data4['date'], function ($sheet4) use ($data4) {
                return $sheet4->loadView('extra_order.download_error_transk_gd_mv', $data4);
            });

        })->store('xlsx', public_path('files/pc_dowload_data/error_transaksi'));

        // STOCK SERIAL NUMBER
        $materials = db::table('material_plant_data_lists')
        ->whereIn('valcl', ['9010'])
        ->get();
        $foramtted_materials = [];
        for ($i = 0; $i < count($materials); $i++) {
            $foramtted_materials[$materials[$i]->material_number] = $materials[$i];
        }

        $mirai = db::select("SELECT material_number, IF(`status` = 2, 'FSTK', storage_location) AS storage_location, serial_number, quantity  FROM extra_order_detail_sequences
            WHERE `status` <= 2
            UNION ALL
            SELECT knock_down_details.material_number, IF(knock_downs.`status` = 2, 'FSTK', knock_down_details.storage_location) AS storage_location, knock_down_details.serial_number, knock_down_details.quantity FROM knock_down_details
            LEFT JOIN knock_downs ON knock_downs.kd_number = knock_down_details.kd_number
            WHERE knock_downs.`status` <= 2
            UNION ALL
            SELECT flo_details.material_number, IF(flos.`status` = 2, 'FSTK', '-') AS storage_location, flo_details.serial_number, flo_details.quantity FROM flo_details
            LEFT JOIN flos ON flos.flo_number = flo_details.flo_number
            WHERE flos.`status` <= 2");

        $ymes = db::connection('ymes')
        ->table('vd_mes0290')
        ->where('serial_no', 'NOT LIKE', '%DUMMY%')
        ->where('shipped_flg', 0)
        ->get();

        $resume = [];
        for ($i = 0; $i < count($mirai); $i++) {
            if (isset($foramtted_materials[$mirai[$i]->material_number])) {
                $material_description = '';
                $material_description = $foramtted_materials[$mirai[$i]->material_number]->material_description;

                $storage_location = $mirai[$i]->storage_location;
                if ($storage_location == '-') {
                    $storage_location = $foramtted_materials[$mirai[$i]->material_number]->storage_location;
                }

                $row = array();
                $row['material_number'] = $mirai[$i]->material_number;
                $row['material_description'] = $material_description;
                $row['storage_location'] = $storage_location;
                $row['serial_number'] = strtoupper($mirai[$i]->serial_number);
                $row['mirai'] = $mirai[$i]->quantity;
                $row['ymes'] = 0;
                $resume[] = (object) $row;

            }

        }

        for ($i = 0; $i < count($ymes); $i++) {
            $material_description = '';
            if (isset($foramtted_materials[$ymes[$i]->item_code])) {
                $material_description = $foramtted_materials[$ymes[$i]->item_code]->material_description;
            }

            $row = array();
            $row['material_number'] = $ymes[$i]->item_code;
            $row['material_description'] = $material_description;
            $row['storage_location'] = $ymes[$i]->location_code;
            $row['serial_number'] = $ymes[$i]->serial_no;
            $row['mirai'] = 0;
            $row['ymes'] = $ymes[$i]->qty;
            $resume[] = (object) $row;
        }

        $resume_new = [];
        for ($i = 0; $i < count($resume); $i++) {

            $key = '';
            $key .= ($resume[$i]->material_number . '#');
            $key .= ($resume[$i]->material_description . '#');
            $key .= ($resume[$i]->storage_location . '#');
            $key .= ($resume[$i]->serial_number . '#');

            if (!array_key_exists($key, $resume_new)) {
                $row = array();
                $row['material_number'] = $resume[$i]->material_number;
                $row['material_description'] = $resume[$i]->material_description;
                $row['storage_location'] = $resume[$i]->storage_location;
                $row['serial_number'] = $resume[$i]->serial_number;
                $row['mirai'] = $resume[$i]->mirai;
                $row['ymes'] = $resume[$i]->ymes;

                $resume_new[$key] = (object) $row;

            } else {
                $resume_new[$key]->mirai = $resume_new[$key]->mirai + $resume[$i]->mirai;
                $resume_new[$key]->ymes = $resume_new[$key]->ymes + $resume[$i]->ymes;

            }

        }

        $ymes_stock = db::connection('ymes')
        ->table('vd_mes0010')
        ->get();

        $data5 = [
            'resume_new' => $resume_new,
            'ymes_stock' => $ymes_stock,
            'date' => $date,
        ];

        Excel::create('STOCK_' . $data5['date'], function ($excel) use ($data5) {
            $excel->sheet('FG_STOCK', function ($sheet) use ($data5) {
                return $sheet->loadView('extra_order.download_data_fg_stock', $data5);
            });
            $excel->sheet('YMES_STOCK', function ($sheet) use ($data5) {
                return $sheet->loadView('extra_order.download_data_ymes_stock', $data5);
            });
        })->store('xlsx', public_path('files/pc_dowload_data/stock'));

    }
}
