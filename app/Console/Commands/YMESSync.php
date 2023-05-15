<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class YMESSync extends Command
{

    protected $signature = 'ymes:sync';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $now = date('Y-m-d');

        $ymes_firmed_orders = db::connection('ymes')->select("SELECT
            item_code AS material_number,
            SUM ( plan_qty - confirm_qty ) AS available_quantity
            FROM
            vd_sap0010
            WHERE
            firm_type = 'X'
            AND confirm_qty < plan_qty
            GROUP BY
            item_code");

        if (count($ymes_firmed_orders) > 0) {
            $truncate_ymes_firmed_orders = db::connection('ympimis_2')->table('ymes_firmed_orders')->truncate();

            foreach ($ymes_firmed_orders as $ymes_firmed_order) {
                $insert_ymes_firmed_order = db::connection('ympimis_2')->table('ymes_firmed_orders')
                    ->insert([
                        'material_number' => $ymes_firmed_order->material_number,
                        'available_quantity' => $ymes_firmed_order->available_quantity,
                        'confirm_quantity' => 0,
                        'created_by' => 'System',
                        'created_by_name' => 'System',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }
        }

        $ymes_stocks = db::connection('ymes')->table('vd_mes0010')
            ->leftJoin('vm_item0010', 'vm_item0010.item_code', '=', 'vd_mes0010.item_code')
            ->where('vd_mes0010.stockqty', '<>', 0)
            ->orWhere('vd_mes0010.inspect_qty', '<>', 0)
            ->orWhere('vd_mes0010.keep_qty', '<>', 0)
            ->select(
                'vd_mes0010.item_code',
                'vm_item0010.item_name',
                'vd_mes0010.location_code',
                'vd_mes0010.stockqty',
                'vd_mes0010.inspect_qty',
                'vd_mes0010.keep_qty'
            )
            ->get();

        $delete_stock = db::select("DELETE FROM storage_location_stocks WHERE stock_date = '" . $now . "'");
        foreach ($ymes_stocks as $ymes_stock) {
            $insert_stock = db::table('storage_location_stocks')
                ->insert([
                    'material_number' => $ymes_stock->item_code,
                    'material_description' => $ymes_stock->item_name,
                    'storage_location' => $ymes_stock->location_code,
                    'unrestricted' => $ymes_stock->stockqty,
                    'inspection' => $ymes_stock->inspect_qty,
                    'blocked' => $ymes_stock->keep_qty,
                    'stock_date' => $now,
                    'download_date' => $now,
                    'download_time' => date('H:i:s'),
                    'created_by' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }

        $ymes_materials = db::connection('ymes')
            ->table('vm_item0010')
            ->whereNull('plant_spitem_status')
            ->get();

        $mpdls = db::table('material_plant_data_lists')->get();
        $no_mpdls = array();

        $kitto_materials = db::connection('mysql2')
            ->table('materials')
            ->get();

        $mirai_materials = db::table('materials')
            ->get();

        $eo_materials = db::table('extra_order_materials')
            ->get();

        // $production_results = db::connection('ympimis_2')->table('production_results')
        //     ->whereNull('material_description')
        //     ->select('material_number')
        //     ->distinct()
        //     ->get();

        // $production_result_temps = db::connection('ympimis_2')->table('production_result_temps')
        //     ->whereNull('material_description')
        //     ->select('material_number')
        //     ->distinct()
        //     ->get();

        // $goods_movements = db::connection('ympimis_2')->table('goods_movements')
        //     ->whereNull('material_description')
        //     ->select('material_number')
        //     ->distinct()
        //     ->get();

        foreach ($ymes_materials as $ymes_material) {
            $found_mpdl = false;
            foreach ($mpdls as $mpdl) {
                if ($mpdl->material_number == $ymes_material->item_code) {
                    $found_mpdl = true;
                    if ($mpdl->material_description != $ymes_material->item_name || $mpdl->pgr != $ymes_material->po_grp || $mpdl->bun != $ymes_material->unit_code || $mpdl->spt != $ymes_material->special_prc_type || $mpdl->mrpc != $ymes_material->mrp_ctrl || $mpdl->storage_location != $ymes_material->issue_loc_code || $mpdl->valcl != $ymes_material->eval_class_code || (float) $mpdl->standard_price != (float) $ymes_material->standard_cost) {

                        $update_mpdl = db::table('material_plant_data_lists')
                            ->where('material_number', '=', $ymes_material->item_code)
                            ->update([
                                'material_description' => $ymes_material->item_name,
                                'pgr' => $ymes_material->po_grp,
                                'bun' => $ymes_material->unit_code,
                                'spt' => $ymes_material->special_prc_type,
                                'mrpc' => $ymes_material->mrp_ctrl,
                                'storage_location' => $ymes_material->issue_loc_code,
                                'valcl' => $ymes_material->eval_class_code,
                                'standard_price' => $ymes_material->standard_cost,
                            ]);
                    }
                    break;
                }
            }
            if ($found_mpdl == false) {
                if (!in_array($ymes_material->item_code, $no_mpdls)) {
                    array_push($no_mpdls, $ymes_material->item_code);
                }
            }

            foreach ($kitto_materials as $kitto_material) {
                if ($ymes_material->item_code == $kitto_material->material_number) {
                    $mstation = 'W' . $ymes_material->mrp_ctrl . 'S10';
                    if ($ymes_material->eval_class_code == '9010') {
                        $workctr_code = 'W' . $ymes_material->mrp_ctrl;
                        $scm = db::connection('ymes')->table('vm_item0080')
                            ->leftJoin('vm_proc0070', 'vm_proc0070.scm_type', '=', 'vm_item0080.scm_type')
                            ->where('vm_item0080.item_code', '=', $kitto_material->material_number)
                            ->where('vm_proc0070.workctr_code', '=', $workctr_code)
                            ->first();
                        if ($scm) {
                            $mstation = $scm->man_stat_cd;
                        }
                    }
                    if ($kitto_material->mstation != $mstation || $kitto_material->description != $ymes_material->item_name) {
                        $update_material = db::connection('mysql2')->table('materials')
                            ->where('material_number', '=', $kitto_material->material_number)
                            ->update([
                                'description' => $ymes_material->item_name,
                                'mstation' => $mstation,
                            ]);
                    }
                    break;
                }
            }

            foreach ($mirai_materials as $mirai_material) {
                if ($ymes_material->item_code == $mirai_material->material_number) {
                    $mstation = 'W' . $ymes_material->mrp_ctrl . 'S10';
                    if ($ymes_material->eval_class_code == '9010') {
                        $workctr_code = 'W' . $ymes_material->mrp_ctrl;
                        $scm = db::connection('ymes')->table('vm_item0080')
                            ->leftJoin('vm_proc0070', 'vm_proc0070.scm_type', '=', 'vm_item0080.scm_type')
                            ->where('vm_item0080.item_code', '=', $mirai_material->material_number)
                            ->where('vm_proc0070.workctr_code', '=', $workctr_code)
                            ->first();
                        if ($scm) {
                            $mstation = $scm->man_stat_cd;
                        }
                    }
                    if ($mirai_material->mstation != $mstation || $mirai_material->material_description != $ymes_material->item_name) {
                        $update_material = db::table('materials')
                            ->where('material_number', '=', $mirai_material->material_number)
                            ->update([
                                'material_description' => $ymes_material->item_name,
                                'mstation' => $mstation,
                            ]);
                    }
                    break;
                }
            }

            foreach ($eo_materials as $eo_material) {
                if ($ymes_material->item_code == $eo_material->material_number) {
                    $mstation = 'W' . $ymes_material->mrp_ctrl . 'S10';
                    if ($ymes_material->eval_class_code == '9010') {
                        $workctr_code = 'W' . $ymes_material->mrp_ctrl;
                        $scm = db::connection('ymes')->table('vm_item0080')
                            ->leftJoin('vm_proc0070', 'vm_proc0070.scm_type', '=', 'vm_item0080.scm_type')
                            ->where('vm_item0080.item_code', '=', $eo_material->material_number)
                            ->where('vm_proc0070.workctr_code', '=', $workctr_code)
                            ->first();
                        if ($scm) {
                            $mstation = $scm->man_stat_cd;
                        }
                    }
                    if ($eo_material->mstation != $mstation || $eo_material->description != $ymes_material->item_name) {
                        $update_material = db::table('extra_order_materials')
                            ->where('material_number', '=', $eo_material->material_number)
                            ->update([
                                'description' => $ymes_material->item_name,
                                'mstation' => $mstation,
                            ]);
                    }
                    break;
                }
            }

            // foreach ($production_results as $production_result) {
            //     if ($ymes_material->item_code == $production_result->material_number) {
            //         $update_pr = db::connection('ympimis_2')->table('production_results')
            //             ->whereNull('material_description')
            //             ->where('material_number', '=', $ymes_material->item_code)
            //             ->update([
            //                 'material_description' => $ymes_material->item_name,
            //             ]);
            //         break;
            //     }
            // }

            // foreach ($production_result_temps as $production_result_temp) {
            //     if ($ymes_material->item_code == $production_result_temp->material_number) {
            //         $update_pr_temp = db::connection('ympimis_2')->table('production_result_temps')
            //             ->whereNull('material_description')
            //             ->where('material_number', '=', $ymes_material->item_code)
            //             ->update([
            //                 'material_description' => $ymes_material->item_name,
            //             ]);
            //         break;
            //     }
            // }

            // foreach ($goods_movements as $goods_movement) {
            //     if ($ymes_material->item_code == $goods_movement->material_number) {
            //         $update_gm = db::connection('ympimis_2')->table('goods_movements')
            //             ->whereNull('material_description')
            //             ->where('material_number', '=', $ymes_material->item_code)
            //             ->update([
            //                 'material_description' => $ymes_material->item_name,
            //             ]);
            //         break;
            //     }
            // }
        }

        foreach ($no_mpdls as $no_mpdl) {
            foreach ($ymes_materials as $ymes_material) {
                if ($ymes_material->item_code == $no_mpdl) {
                    $insert_mpdl = db::table('material_plant_data_lists')
                        ->insert([
                            'material_number' => $ymes_material->item_code,
                            'material_description' => $ymes_material->item_name,
                            'pgr' => $ymes_material->po_grp,
                            'bun' => $ymes_material->unit_code,
                            'spt' => $ymes_material->special_prc_type,
                            'mrpc' => $ymes_material->mrp_ctrl,
                            'storage_location' => $ymes_material->issue_loc_code,
                            'valcl' => $ymes_material->eval_class_code,
                            'standard_price' => $ymes_material->standard_cost,
                            'created_by' => 1,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    break;
                }
            }
        }

        $error_production_results = db::connection('ympimis_2')->table('production_results')
            ->whereNull('synced')
            ->where('category', '=', 'production_result_error')
            ->get();

        $error_goods_movements = db::connection('ympimis_2')->table('goods_movements')
            ->whereNull('synced')
            ->where('category', '=', 'goods_movement_error')
            ->get();

        $title = "MIRAI-YMES I/F Error";
        $body = "";
        $body .= "Count Error Production Result = " . count($error_production_results) . "\n";
        $body .= "Count Error Goods Movement = " . count($error_goods_movements) . "\n\n";

        if (count($error_production_results) > 0) {
            foreach ($error_production_results as $row) {
                $body .= $row->category . "\t" . $row->slip_number . "\t" . $row->material_number . "\t" . $row->issue_location . "\t" . $row->quantity . "\n";
            }
        }

        $body .= "\n\n";

        if (count($error_goods_movements) > 0) {
            foreach ($error_goods_movements as $row) {
                $body .= $row->category . "\t" . $row->slip_number . "\t" . $row->material_number . "\t" . $row->issue_location . "\t" . $row->receive_location . "\t" . $row->quantity . "\n";
            }
        }

        $mail_to = [
            'ympi-mis-ML@music.yamaha.com',
        ];
        $bcc = [];

        self::mailReport($title, $body, $mail_to, $bcc);
    }

    public function mailReport($title, $body, $mail_to, $bcc)
    {
        Mail::raw([], function ($message) use ($title, $body, $mail_to, $bcc) {
            $message->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia');
            $message->to($mail_to);
            $message->bcc($bcc);
            $message->subject($title);
            $message->setBody($body, 'text/plain');
        });
    }
}
