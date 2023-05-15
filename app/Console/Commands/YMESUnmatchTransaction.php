<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;

class YMESUnmatchTransaction extends Command {

    protected $signature = 'ymes:unmatch';

    protected $description = 'Command description';

    public function __construct() {
        parent::__construct();
    }

    public function handle() {

        $date = date('Y-m-d', strtotime("-1 day", strtotime(date("Y-m-d"))));
        $now = date('Y-m-d');
        $resume_pr_trx = [];
        $resume_gm_trx = [];
        $resume_ymes_trx = [];

        $unmatch_mirai = [];
        $unmatch_ymes = [];


        // PRODUCTION RESULT
        $kitto_pr_trx = db::connection('mysql2')
        ->select("SELECT materials.material_number, histories.completion_location, SUM(histories.lot) AS qty FROM histories
            LEFT JOIN materials ON histories.completion_material_id = materials.id
            WHERE DATE(histories.created_at) = '".$date."'
            AND histories.category LIKE '%completion%'
            GROUP BY materials.material_number, histories.completion_location");


        $mirai_pr_trx = db::select("SELECT material_number, issue_location, IF(movement_type = '102', (quantity * -1), quantity) AS qty FROM transaction_completions
            WHERE DATE(created_at) = '".$date."'
            UNION ALL
            SELECT flo_details.material_number, materials.issue_storage_location, SUM(flo_details.quantity) AS qty FROM flo_details
            LEFT JOIN materials ON materials.material_number = flo_details.material_number
            WHERE DATE(flo_details.created_at) = '".$date."'
            GROUP BY flo_details.material_number, materials.issue_storage_location
            UNION ALL
            SELECT material_number, location, SUM(quantity) AS qty FROM `injection_transactions`
            WHERE DATE(created_at) = '".$date."'
            AND location LIKE '%11%'
            AND `status` = 'IN'
            GROUP BY material_number, location");


        for ($i=0; $i < count($kitto_pr_trx); $i++) { 
            $key = $kitto_pr_trx[$i]->material_number . '#' . $kitto_pr_trx[$i]->completion_location;
            if (!array_key_exists($key, $resume_pr_trx)) {
                $row = array();
                $row['material_number'] = $kitto_pr_trx[$i]->material_number;
                $row['issue'] = $kitto_pr_trx[$i]->completion_location;
                $row['old_trx'] = $kitto_pr_trx[$i]->qty;
                $row['new_trx'] = 0;
                $resume_pr_trx[$key] = $row;
            } else {
                $resume_pr_trx[$key]['old_trx'] = $resume_pr_trx[$key]['old_trx'] + $kitto_pr_trx[$i]->qty;
            }
        }

        for ($i=0; $i < count($mirai_pr_trx); $i++) { 
            $key = $mirai_pr_trx[$i]->material_number . '#' . $mirai_pr_trx[$i]->issue_location;
            if (!array_key_exists($key, $resume_pr_trx)) {
                $row = array();
                $row['material_number'] = $mirai_pr_trx[$i]->material_number;
                $row['issue'] = $mirai_pr_trx[$i]->issue_location;
                $row['old_trx'] = $mirai_pr_trx[$i]->qty;
                $row['new_trx'] = 0;
                $resume_pr_trx[$key] = $row;
            } else {
                $resume_pr_trx[$key]['old_trx'] = $resume_pr_trx[$key]['old_trx'] + $mirai_pr_trx[$i]->qty;
            }       
        }

        $new_pr_trx = db::connection('ympimis_2')
        ->select("SELECT material_number, issue_location, SUM(quantity) AS qty FROM production_results
            WHERE DATE(result_date) = '".$date."'
            AND category NOT IN ('production_result_adjustment', 'production_result_error')
            GROUP BY material_number, issue_location");

        for ($i=0; $i < count($new_pr_trx); $i++) { 
            $key = $new_pr_trx[$i]->material_number . '#' . $new_pr_trx[$i]->issue_location;
            if (!array_key_exists($key, $resume_pr_trx)) {
                $row = array();
                $row['material_number'] = $new_pr_trx[$i]->material_number;
                $row['issue'] = $new_pr_trx[$i]->issue_location;
                $row['old_trx'] = 0;
                $row['new_trx'] = $new_pr_trx[$i]->qty;
                $resume_pr_trx[$key] = $row;
            } else {
                $resume_pr_trx[$key]['new_trx'] = $resume_pr_trx[$key]['new_trx'] + $new_pr_trx[$i]->qty;
            }               
        }
        // END PRODUCTION RESULT


        // GOOD MOVEMENT
        $kitto_gm_trx = db::connection('mysql2')
        ->select("SELECT materials.material_number, IF(transfer_movement_type = '9I3' OR transfer_movement_type = '9P1', histories.transfer_issue_location, histories.transfer_receive_location) AS issue_location, IF(transfer_movement_type = '9I3' OR transfer_movement_type = '9P1', histories.transfer_receive_location, histories.transfer_issue_location) AS receive_location, histories.lot AS qty FROM histories
            LEFT JOIN materials ON histories.transfer_material_id = materials.id
            WHERE DATE(histories.created_at) = '".$date."'
            AND histories.category LIKE '%transfer%'");

        $mirai_gm_trx = db::select("SELECT material_number, IF(movement_type = '9I3' OR movement_type = '9P1', issue_location, receive_location) AS issue_location, IF(movement_type = '9I3' OR movement_type = '9P1', receive_location, issue_location) AS receive_location, quantity AS qty FROM transaction_transfers
            WHERE DATE(created_at) = '".$date."'
            UNION ALL
            SELECT flo_details.material_number, materials.issue_storage_location, 'FSTK' AS receive_location, flo_details.quantity FROM flo_logs
            LEFT JOIN flo_details ON flo_details.flo_number = flo_logs.flo_number
            LEFT JOIN materials ON flo_details.material_number = materials.material_number
            WHERE DATE(flo_logs.created_at) = '".$date."'
            AND flo_logs.status_code = 2
            UNION ALL
            SELECT flo_details.material_number, materials.issue_storage_location, 'FSTK' AS receive_location, flo_details.quantity FROM flos
            LEFT JOIN flo_details ON flo_details.flo_number = flos.flo_number
            LEFT JOIN materials ON flo_details.material_number = materials.material_number
            WHERE flos.`status` >= 2
            AND flos.flo_number NOT IN (
            SELECT flo_number FROM flo_logs
            WHERE flo_logs.status_code = 2)
            UNION ALL
            SELECT material_number, location AS issue_location, 'RC91' AS receive_location, quantity FROM `injection_transactions`
            WHERE DATE(created_at) = '".$date."'
            AND `status` = 'OUT'");

        for ($i=0; $i < count($kitto_gm_trx); $i++) { 
            $key = $kitto_gm_trx[$i]->material_number . '#' . $kitto_gm_trx[$i]->issue_location . '#' . $kitto_gm_trx[$i]->receive_location;
            if (!array_key_exists($key, $resume_gm_trx)) {
                $row = array();
                $row['material_number'] = $kitto_gm_trx[$i]->material_number;
                $row['issue'] = $kitto_gm_trx[$i]->issue_location;
                $row['receive'] = $kitto_gm_trx[$i]->receive_location;
                $row['old_trx'] = $kitto_gm_trx[$i]->qty;
                $row['new_trx'] = 0;
                $resume_gm_trx[$key] = $row;
            } else {
                $resume_gm_trx[$key]['old_trx'] = $resume_gm_trx[$key]['old_trx'] + $kitto_gm_trx[$i]->qty;
            }
        }

        for ($i=0; $i < count($mirai_gm_trx); $i++) { 
            $key = $mirai_gm_trx[$i]->material_number . '#' . $mirai_gm_trx[$i]->issue_location . '#' . $mirai_gm_trx[$i]->receive_location;
            if (!array_key_exists($key, $resume_gm_trx)) {
                $row = array();
                $row['material_number'] = $mirai_gm_trx[$i]->material_number;
                $row['issue'] = $mirai_gm_trx[$i]->issue_location;
                $row['receive'] = $mirai_gm_trx[$i]->receive_location;
                $row['old_trx'] = $mirai_gm_trx[$i]->qty;
                $row['new_trx'] = 0;
                $resume_gm_trx[$key] = $row;
            } else {
                $resume_gm_trx[$key]['old_trx'] = $resume_gm_trx[$key]['old_trx'] + $mirai_gm_trx[$i]->qty;
            }       
        }

        $new_gm_trx = db::connection('ympimis_2')
        ->select("SELECT material_number, issue_location, receive_location, SUM(quantity) AS qty FROM goods_movements
            WHERE DATE(result_date) =  '".$date."'
            AND category NOT IN ('goods_movement_adjustment', 'goods_movement_error')
            GROUP BY material_number, issue_location, receive_location");

        for ($i=0; $i < count($new_gm_trx); $i++) { 
            $key = $new_gm_trx[$i]->material_number . '#' . $new_gm_trx[$i]->issue_location . '#' . $new_gm_trx[$i]->receive_location;
            if (!array_key_exists($key, $resume_gm_trx)) {
                $row = array();
                $row['material_number'] = $new_gm_trx[$i]->material_number;
                $row['issue'] = $new_gm_trx[$i]->issue_location;
                $row['receive'] = $new_gm_trx[$i]->receive_location;
                $row['old_trx'] = 0;
                $row['new_trx'] = $new_gm_trx[$i]->qty;
                $resume_gm_trx[$key] = $row;
            } else {
                $resume_gm_trx[$key]['new_trx'] = $resume_gm_trx[$key]['new_trx'] + $new_gm_trx[$i]->qty;
            }               
        }
        // END GOOD MOVEMENT


        // YMES CHECK
        $ymes = db::connection('ymes')
        ->select("SELECT item_code,
            CASE WHEN move_type LIKE '%SD%' THEN 'Goods Movement' ELSE 'Production Result' END AS category,
            CASE WHEN move_type LIKE '%SD%' THEN issue_loc_code ELSE in_loc_code END AS issue,
            CASE WHEN move_type LIKE '%SD%' THEN in_loc_code ELSE '-' END AS receive,
            inout_qty AS qty
            FROM vd_mes0020
            WHERE move_type IN ('PR01', 'SD01', 'SD02')
            -- AND instid = 'iot'
            AND instdt::date = date '".$date."'");

        $mirai = db::connection('ympimis_2')
        ->select("SELECT 'Production Result' AS category, item_code, dest_location_code AS issue, '-' AS receive, qty FROM i_ext0010
            WHERE DATE(instdt) = '".$date."'
            UNION ALL
            SELECT 'Goods Movement' AS category, item_code, issue_loc_code AS issue, in_loc_code AS receive, qty FROM i_ext0020
            WHERE DATE(instdt) = '".$date."'");

        $error = db::connection('ymes')
        ->select("SELECT 'Production Result' AS category, item_code, dest_location_code AS issue, '-' AS receive, qty FROM e_ext0010
            UNION ALL
            SELECT 'Goods Movement' AS category, item_code, issue_loc_code AS issue, in_loc_code AS receive, qty FROM e_ext0020");

        for ($i=0; $i < count($ymes); $i++) { 
            $key = $ymes[$i]->item_code . '#' . $ymes[$i]->category . '#' . $ymes[$i]->issue . '#' . $ymes[$i]->receive;
            if (!array_key_exists($key, $resume_ymes_trx)) {
                $row = array();
                $row['material_number'] = $ymes[$i]->item_code;
                $row['category'] = $ymes[$i]->category;
                $row['issue'] = $ymes[$i]->issue;
                $row['receive'] = $ymes[$i]->receive;
                $row['mirai'] = 0;
                $row['ymes'] = doubleval($ymes[$i]->qty);
                $row['error'] = 0;
                $resume_ymes_trx[$key] = $row;
            } else {
                $resume_ymes_trx[$key]['ymes'] = doubleval($resume_ymes_trx[$key]['ymes']) + doubleval($ymes[$i]->qty);
            }               
        }

        for ($i=0; $i < count($mirai); $i++) { 
            $key = $mirai[$i]->item_code . '#' . $mirai[$i]->category . '#' . $mirai[$i]->issue . '#' . $mirai[$i]->receive;
            if (!array_key_exists($key, $resume_ymes_trx)) {
                $row = array();
                $row['material_number'] = $mirai[$i]->item_code;
                $row['category'] = $mirai[$i]->category;
                $row['issue'] = $mirai[$i]->issue;
                $row['receive'] = $mirai[$i]->receive;
                $row['mirai'] = doubleval($mirai[$i]->qty);
                $row['ymes'] = 0;
                $row['error'] = 0;
                $resume_ymes_trx[$key] = $row;
            } else {
                $resume_ymes_trx[$key]['mirai'] = doubleval($resume_ymes_trx[$key]['mirai']) + doubleval($mirai[$i]->qty);
            }               
        }

        for ($i=0; $i < count($error); $i++) { 
            $key = $error[$i]->item_code . '#' . $error[$i]->category . '#' . $error[$i]->issue . '#' . $error[$i]->receive;
            if (!array_key_exists($key, $resume_ymes_trx)) {
                $row = array();
                $row['material_number'] = $error[$i]->item_code;
                $row['category'] = $error[$i]->category;
                $row['issue'] = $error[$i]->issue;
                $row['receive'] = $error[$i]->receive;
                $row['mirai'] = 0;
                $row['ymes'] = 0;
                $row['error'] = doubleval($error[$i]->qty);
                $resume_ymes_trx[$key] = $row;
            } else {
                $resume_ymes_trx[$key]['error'] = doubleval($resume_ymes_trx[$key]['error']) + doubleval($error[$i]->qty);
            }               
        }
        // END YMES

        // UNMATCH
        foreach ($resume_pr_trx as $pr) {
            if($pr['old_trx'] != $pr['new_trx']){
                $row = array();
                $row['category'] = 'Production Result';
                $row['material_number'] = $pr['material_number'];
                $row['description'] = '';
                $row['issue'] = $pr['issue'];
                $row['receive'] = '-';
                $row['old_trx'] = $pr['old_trx'];
                $row['new_trx'] = $pr['new_trx'];
                $unmatch_mirai[] = $row;
            }
        }

        foreach ($resume_gm_trx as $gm) {
            if($gm['old_trx'] != $gm['new_trx']){
                $row = array();
                $row['category'] = 'Goods Movement';
                $row['material_number'] = $gm['material_number'];
                $row['description'] = '';
                $row['issue'] = $gm['issue'];
                $row['receive'] = $gm['receive'];
                $row['old_trx'] = $gm['old_trx'];
                $row['new_trx'] = $gm['new_trx'];
                $unmatch_mirai[] = $row;
            }
        }
        // END UNMATCH

        $mpdl = db::table('material_plant_data_lists')->get();

        for ($h=0; $h < count($unmatch_mirai); $h++) {            
            for ($i=0; $i < count($mpdl); $i++) {
                if( $unmatch_mirai[$h]['material_number'] == $mpdl[$i]->material_number ){
                    $unmatch_mirai[$h]['description'] = $mpdl[$i]->material_description;
                    break; 
                }
            }
        }

        foreach ($resume_ymes_trx as $dt) {
            if( ($dt['mirai'] - $dt['error'])  != $dt['ymes']){
                for ($i=0; $i < count($mpdl); $i++) {
                    if( $dt['material_number'] == $mpdl[$i]->material_number ){
                        $row = array();
                        $row['category'] = $dt['category'];
                        $row['material_number'] = $dt['material_number'];
                        $row['description'] = $mpdl[$i]->material_description ;
                        $row['issue'] = $dt['issue'];
                        $row['receive'] = $dt['receive'];
                        $row['mirai'] = $dt['mirai'];
                        $row['error'] = $dt['error'];
                        $row['ymes'] = $dt['ymes'];
                        $unmatch_ymes[] = $row;
                        break;
                    }
                }
            }
        }

        if(count($unmatch_mirai) > 0 || count($unmatch_ymes) > 0 || count($error) > 0){
            $to = [
                // 'silvy.firliani@music.yamaha.com',
                // 'mamluatul.atiyah@music.yamaha.com',
                // 'istiqomah@music.yamaha.com',
                // 'farizca.nurma@music.yamaha.com',
                // 'ali.murdani@music.yamaha.com',
                // 'ade.laksmana.putra@music.yamaha.com'
                // 'mokhamad.khamdan.khabibi@music.yamaha.com',
                'rio.irvansyah@music.yamaha.com',
                // 'nasiqul.ibat@music.yamaha.com'
            ];

            $cc = [
                // 'wachid.hasyim@music.yamaha.com', 
                // 'fudila.isya.arida@music.yamaha.com', 
                // 'putri.airin.sucin@music.yamaha.com', 
                // 'hendri.susilo@music.yamaha.com'
            ];

            $bcc = [
                // 'aditya.agassi@music.yamaha.com',
                'muhammad.ikhlas@music.yamaha.com'
            ];

            $data = [
                'unmatch_mirai' => $unmatch_mirai,
                'unmatch_ymes' => $unmatch_ymes,
                'error' => $error,
                'date_text' => date('l, d M Y', strtotime($date))
            ];

            Mail::to($to)
            ->cc($cc)
            ->bcc($bcc)
            ->send(new SendEmail($data, 'ymes_unmatch'));


        }

    }
}
