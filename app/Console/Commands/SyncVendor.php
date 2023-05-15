<?php

namespace App\Console\Commands;

use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncVendor extends Command
{
    protected $signature = 'sync:bfv_vendor';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {

        // ----- START GET TOKEN
        $token_bfv = app('App\Http\Controllers\BridgeForVendorController')->getToken();

        $controller_ympi_co_id = app()->make('App\Http\Controllers\MiraiMobileController');
        $token_ympi_co_id = app()->call([$controller_ympi_co_id, 'ympicoid_api_login'], []);

        // ----- END GET TOKEN

        // ----- START GENERATE IN OUT
        $messages = "*Vendor Sync Information :*%0A%0A";
        if ((int) date('H') == 4) {
            $from = date('Y-m-d', strtotime("-1 days")) . ' 00:00:01';
            $to = date('Y-m-d', strtotime("-1 days")) . ' 23:59:59';

            $in = db::connection('ymes')
                ->table('vd_sap0120_010')
                ->where('instdt', '>=', $from)
                ->where('instdt', '<=', $to)
                ->whereIn('sap_move_type', ['101', '102'])
                ->whereNull('item_name')
                ->get();

            for ($i = 0; $i < count($in); $i++) {
                $insert = db::table('material_in_outs')
                    ->insert([
                        'po_number' => $in[$i]->po_no,
                        'item_line' => $in[$i]->po_sub_no,
                        'material_number' => $in[$i]->item_code,
                        'movement_type' => $in[$i]->sap_move_type,
                        'issue_location' => $in[$i]->location_code,
                        'quantity' => $in[$i]->qty,
                        'bc_document' => $in[$i]->doc_header_text,
                        'entry_date' => $in[$i]->sap_input_date,
                        'posting_date' => $in[$i]->post_date,
                        'created_by' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                if ($in[$i]->sap_move_type == '101') {
                    $plan_delivery = db::table('material_plan_deliveries')
                        ->where('po_number', $in[$i]->po_no)
                        ->where('item_line', $in[$i]->po_sub_no)
                        ->where('material_number', $in[$i]->item_code)
                        ->select(
                            'id',
                            'actual',
                            db::raw('(quantity - actual) AS quantity')
                        )
                        ->havingRaw('quantity > 0')
                        ->get();

                    $quantity = $in[$i]->qty;
                    for ($x = 0; $x < count($plan_delivery); $x++) {
                        if ($plan_delivery[$x]->quantity >= $quantity) {
                            $update = db::table('material_plan_deliveries')
                                ->where('id', $plan_delivery[$x]->id)
                                ->update([
                                    'actual' => ($plan_delivery[$x]->actual + $quantity),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                            $quantity = 0;
                        } else {
                            $update = db::table('material_plan_deliveries')
                                ->where('id', $plan_delivery[$x]->id)
                                ->update([
                                    'actual' => ($plan_delivery[$x]->actual + $plan_delivery[$x]->quantity),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                            $quantity -= $plan_delivery[$x]->quantity;
                        }

                        if ($quantity == 0) {
                            break;
                        }

                    }

                } else if ($in[$i]->sap_move_type == '102') {
                    $plan_delivery = db::table('material_plan_deliveries')
                        ->where('po_number', $in[$i]->po_no)
                        ->where('item_line', $in[$i]->po_sub_no)
                        ->where('material_number', $in[$i]->item_code)
                        ->where('actual', '>', 0)
                        ->select(
                            'id',
                            db::raw('actual AS quantity')
                        )
                        ->get();

                    $quantity = $in[$i]->qty;
                    for ($x = 0; $x < count($plan_delivery); $x++) {
                        if ($plan_delivery[$x]->quantity >= $quantity) {
                            $update = db::table('material_plan_deliveries')
                                ->where('id', $plan_delivery[$x]->id)
                                ->update([
                                    'actual' => ($plan_delivery[$x]->actual - $quantity),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                            $quantity = 0;
                        } else {
                            $update = db::table('material_plan_deliveries')
                                ->where('id', $plan_delivery[$x]->id)
                                ->update([
                                    'actual' => ($plan_delivery[$x]->actual - $plan_delivery[$x]->quantity),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);

                            $quantity -= $plan_delivery[$x]->quantity;
                        }

                        if ($quantity == 0) {
                            break;
                        }

                    }

                }

            }

            $out = db::connection('ymes')
                ->table('vd_mes0020')
                ->where('instdt', '>=', $from)
                ->where('instdt', '<=', $to)
                ->where('issue_loc_code', 'MSTK')
                ->whereIn('move_type', ['SD01', 'SD02', 'SI91', 'SI92'])
                ->whereNotNull('item_code')
                ->get();

            for ($i = 0; $i < count($out); $i++) {
                $insert = db::table('material_in_outs')
                    ->insert([
                        'material_number' => $out[$i]->item_code,
                        'movement_type' => $out[$i]->move_type,
                        'issue_location' => $out[$i]->issue_loc_code,
                        'receive_location' => $out[$i]->in_loc_code,
                        'cost_center' => $out[$i]->cost_center_code,
                        'quantity' => $out[$i]->inout_qty,
                        'entry_date' => substr($out[$i]->instdt, 0, 10),
                        'posting_date' => $out[$i]->inout_date,
                        'created_by' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            $messages .= "Material In Out%0A";
            $messages .= 'In : ' . count($in) . ' Record(s)%0A';
            $messages .= 'Out : ' . count($out) . ' Record(s)%0A';
            $messages .= '%0A';

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.whatspie.com/api/messages',
                CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => "receiver=6282234955505&device=6281130561777&message=" . $messages . '&type=chat',
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                ),
            ));
            curl_exec($curl);

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.whatspie.com/api/messages',
                CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => "receiver=6281235064249&device=6281130561777&message=" . $messages . '&type=chat',
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                ),
            ));
            curl_exec($curl);

        }

        // ----- START GENERATE STOCK POLICY
        $notification_status = false;
        $messages = "*Vendor Sync Information :*%0A%0A";
        if ((int) date("H") == 1) {
            $link = 'generate_stock_policy';
            $param = '';
            $method = 'GET';

            $controller = app()->make('App\Http\Controllers\BridgeForVendorController');
            $sync = app()->call([$controller, 'getApi'], [
                'token' => $token_bfv,
                'link' => $link,
                'method' => $method,
                'param' => $param,
            ]);

            $messages .= "Generate Stock Policy BFV%0A";
            if ($sync->status) {
                $messages .= 'Success : ' . $sync->count_updated . ' Row(s)%0A';
            } else {
                $messages .= 'Error : ' . substr($sync->message, 0, 50) . '%0A';
            }
            $messages .= '%0A';
            $notification_status = true;

        }

        // ----- START GENERATE REQUIREMENT PLAN
        if ((int) date("H") == 3) {
            $status = true;
            $count_plan = 0;
            DB::beginTransaction();

            DB::table('material_requirement_plans')
                ->where('due_date', 'LIKE', '%' . date('Y-m') . '%')
                ->delete();

            $messages .= "Generate MRP%0A";
            $plans = DB::select("
                SELECT smbmrs.raw_material, ps.due_date, ROUND(SUM(ps.quantity * smbmrs.`usage`),3) AS `usage` FROM
                (SELECT m.category, ps.material_number, ps.due_date, ps.quantity FROM production_schedules ps
                LEFT JOIN materials m ON m.material_number = ps.material_number
                WHERE m.category = 'FG'
                AND due_date LIKE '%" . date('Y-m') . "%'
                UNION ALL
                SELECT m.category, ps.material_number, ps.due_date, ps.quantity FROM production_schedules_one_steps ps
                LEFT JOIN materials m ON m.material_number = ps.material_number
                WHERE m.category = 'KD'
                AND due_date LIKE '%" . date('Y-m') . "%'
                UNION ALL
                SELECT 'EO' AS category, material_number, due_date, quantity FROM extra_order_details
                WHERE due_date LIKE '%" . date('Y-m') . "%'
                ) AS ps
                LEFT JOIN smbmrs ON smbmrs.material_parent = ps.material_number
                WHERE smbmrs.raw_material IS NOT NULL
                GROUP BY smbmrs.raw_material, ps.due_date");
            $count_plan += count($plans);

            try {

                for ($i = 0; $i < count($plans); $i++) {
                    $insert = DB::table('material_requirement_plans')
                        ->insert([
                            'material_number' => $plans[$i]->raw_material,
                            'due_date' => $plans[$i]->due_date,
                            'usage' => $plans[$i]->usage,
                            'created_by' => 1,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                }

            } catch (Exception $e) {
                DB::rollback();
                $messages .= 'Error : ' . substr($e->getMessage(), 0, 50) . '%0A';
                $status = false;
            }

            if ((int) date("N") == 7) {
                $start = date('Y-m') . '-01';
                $end = date('Y-m', strtotime(date('Y-m') . '-01 +7 month')) . '-01';

                $start = new DateTime($start);
                $end = new DateTime($end);
                $interval = DateInterval::createFromDateString('1 month');
                $period = new DatePeriod($start, $interval, $end);

                $interval_month = [];
                foreach ($period as $dt) {
                    $row = array();
                    $row['month'] = $dt->format("Y-m");
                    $row['text_month'] = $dt->format("F");
                    $row['text_year'] = $dt->format("Y");

                    array_push($interval_month, (object) $row);
                }

                for ($i = 1; $i < count($interval_month); $i++) {
                    DB::table('material_requirement_plans')
                        ->where('due_date', 'LIKE', '%' . $interval_month[$i]->month . '%')
                        ->delete();

                    $calendar = DB::table('weekly_calendars')
                        ->where('week_date', 'LIKE', '%' . $interval_month[$i]->month . '%')
                        ->where('remark', '!=', 'H')
                        ->get();

                    if (count($calendar) > 0) {
                        $plans = DB::select("
                            SELECT smbmrs.raw_material, smbmrs.raw_material_description, SUM(forecasts.quantity * smbmrs.`usage`) AS `usage` FROM (
                                SELECT 'Forecast' AS category, material_number, quantity FROM production_forecasts
                                WHERE production_forecasts.forecast_month LIKE '%" . date('Y-m') . "%'
                                UNION ALL
                                SELECT 'EO' AS category, material_number, quantity FROM extra_order_details
                                WHERE due_date LIKE '%" . date('Y-m') . "%') AS forecasts
                            LEFT JOIN smbmrs ON forecasts.material_number = smbmrs.material_parent
                            WHERE smbmrs.raw_material IS NOT NULL
                            GROUP BY smbmrs.raw_material, smbmrs.raw_material_description");

                        $count_plan += count($plans);

                        try {
                            for ($x = 0; $x < count($plans); $x++) {
                                for ($y = 0; $y < count($calendar); $y++) {
                                    $insert = DB::table('material_requirement_plans')
                                        ->insert([
                                            'material_number' => $plans[$x]->raw_material,
                                            'due_date' => $calendar[$y]->week_date,
                                            'usage' => $plans[$x]->usage / count($calendar),
                                            'created_by' => 1,
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s'),
                                        ]);
                                }
                            }

                        } catch (\Throwable$th) {
                            DB::rollback();
                            $messages .= 'Error : ' . substr($e->getMessage(), 0, 50) . '%0A';
                            $status = false;

                        }
                    }

                }

            }

            if ($status) {
                DB::commit();
                $messages .= "Success : " . $count_plan . " Row(s)%0A";
                $notification_status = true;
            }
            $messages .= '%0A';

        }

        // ----- START SYNC PLAN DELIVERY
        $link = 'fetch/sync_plan_delivery';
        $param = '';
        $method = 'GET';

        $controller = app()->make('App\Http\Controllers\MiraiMobileController');
        $sync = app()->call([$controller, 'ympicoid_api'], [
            'token' => $token_ympi_co_id,
            'link' => $link,
            'method' => $method,
            'param' => $param,
        ]);

        if (count($sync)) {
            $messages .= "Sync Plan Delivery YMPI CO ID%0A";
            DB::beginTransaction();
            $status = true;

            for ($i = 0; $i < count($sync); $i++) {
                try {
                    $update = DB::table('material_plan_deliveries')
                        ->where('id', $sync[$i]->id)
                        ->update([
                            'po_confirm' => $sync[$i]->po_confirm,
                            'po_confirm_at' => $sync[$i]->po_confirm_at,
                            'reminder_confirm_at' => $sync[$i]->reminder_confirm_at,
                            'updated_at' => $sync[$i]->updated_at,
                        ]);

                } catch (Exception $e) {
                    DB::rollback();
                    $messages .= 'Error : ' . substr($e->getMessage(), 0, 50) . '%0A';
                    $status = false;
                }
            }

            if ($status) {
                DB::commit();
                $messages .= "Success : " . count($sync) . " Row(s)%0A";
                $notification_status = true;
            }
            $messages .= '%0A';
        }

        // NOTIFICATION
        if ($notification_status) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.whatspie.com/api/messages',
                CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'receiver=6282234955505&device=6281130561777&message=' . $messages . '&type=chat',
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                ),
            ));
            curl_exec($curl);
        }

        // ------------ SYNC PE MOLDING ---------------
        // $link = 'get_data_molding';
        // $param = '';
        // $method = 'GET';

        // $controller = app()->make('App\Http\Controllers\BridgeForVendorController');
        // $sync2 = app()->call([$controller, 'getApi'], [
        //     'token' => $token_bfv,
        //     'link' => $link,
        //     'method' => $method,
        //     'param' => $param,
        // ]);

        // $messages2 = "Syncing Molding Audit Vendor Data from Vendor%0A";
        // if (count($sync2)) {
        //     DB::beginTransaction();
        //     $status = true;

        //     for ($i = 0; $i < count($sync2->molding_check); $i++) {
        //         try {
        //             $update_check = DB::connection('ympimis_2')
        //                 ->table('pe_molding_checks')
        //                 ->insert([
        //                     'check_date' => $sync2['molding_check'][$i]->check_date,
        //                     'molding_name' => $sync2['molding_check'][$i]->molding_name,
        //                     'molding_type' => $sync2['molding_check'][$i]->molding_type,
        //                     'location' => $sync2['molding_check'][$i]->location,
        //                     'pic' => $sync2['molding_check'][$i]->pic,
        //                     'conclusion' => $sync2['molding_check'][$i]->conclusion,
        //                     'status' => $sync2['molding_check'][$i]->status,
        //                     'remark' => $sync2['molding_check'][$i]->remark,
        //                     'sync_at' => $sync2['sync_at'],
        //                     'created_by' => $sync2['molding_check'][$i]->created_by,
        //                     'created_at' => $sync2['molding_check'][$i]->created_at,
        //                     'updated_at' => $sync2['molding_check'][$i]->updated_at,
        //                     'deleted_at' => $sync2['molding_check'][$i]->deleted_at,
        //                 ]);

        //             $update_check = DB::connection('ympimis_2')
        //                 ->table('pe_molding_check_details')
        //                 ->insert([
        //                     'check_id' => $sync2['molding_check_detail'][$i]->check_id,
        //                     'part_name' => $sync2['molding_check_detail'][$i]->part_name,
        //                     'point_check' => $sync2['molding_check_detail'][$i]->point_check,
        //                     'standard' => $sync2['molding_check_detail'][$i]->standard,
        //                     'how_check' => $sync2['molding_check_detail'][$i]->how_check,
        //                     'handle' => $sync2['molding_check_detail'][$i]->handle,
        //                     'photo_before1' => $sync2['molding_check_detail'][$i]->photo_before1,
        //                     'photo_before2' => $sync2['molding_check_detail'][$i]->photo_before2,
        //                     'photo_after1' => $sync2['molding_check_detail'][$i]->photo_after1,
        //                     'photo_after2' => $sync2['molding_check_detail'][$i]->photo_after2,
        //                     'photo_activity1' => $sync2['molding_check_detail'][$i]->photo_activity1,
        //                     'photo_activity2' => $sync2['molding_check_detail'][$i]->photo_activity2,
        //                     'judgement' => $sync2['molding_check_detail'][$i]->judgement,
        //                     'note' => $sync2['molding_check_detail'][$i]->note,
        //                     'status' => $sync2['molding_check_detail'][$i]->status,
        //                     'remark' => $sync2['molding_check_detail'][$i]->remark,
        //                     'sync_at' => $sync2['sync_at'],
        //                     'created_by' => $sync2['molding_check_detail'][$i]->created_by,
        //                     'created_at' => $sync2['molding_check_detail'][$i]->created_at,
        //                     'updated_at' => $sync2['molding_check_detail'][$i]->updated_at,
        //                     'deleted_at' => $sync2['molding_check_detail'][$i]->deleted_at,
        //                 ]);

        //             $update_check = DB::connection('ympimis_2')
        //                 ->table('pe_molding_findings')
        //                 ->insert([
        //                     'id' => $sync2['molding_finding'][$i]->id,
        //                     'check_id' => $sync2['molding_finding'][$i]->check_id,
        //                     'check_date' => $sync2['molding_finding'][$i]->check_date,
        //                     'pic' => $sync2['molding_finding'][$i]->pic,
        //                     'molding_name' => $sync2['molding_finding'][$i]->molding_name,
        //                     'molding_type' => $sync2['molding_finding'][$i]->molding_type,
        //                     'part_name' => $sync2['molding_finding'][$i]->part_name,
        //                     'problem' => $sync2['molding_finding'][$i]->problem,
        //                     'handling_temporary' => $sync2['molding_finding'][$i]->handling_temporary,
        //                     'notes' => $sync2['molding_finding'][$i]->notes,
        //                     'handling_note' => $sync2['molding_finding'][$i]->handling_note,
        //                     'handling_eviden' => $sync2['molding_finding'][$i]->handling_eviden,
        //                     'close_date' => $sync2['molding_finding'][$i]->close_date,
        //                     'status' => $sync2['molding_finding'][$i]->status,
        //                     'note' => $sync2['molding_finding'][$i]->note,
        //                     'remark' => $sync2['molding_finding'][$i]->remark,
        //                     'sync_at' => $sync2['sync_at'],
        //                     'created_by' => $sync2['molding_finding'][$i]->created_by,
        //                     'created_at' => $sync2['molding_finding'][$i]->created_at,
        //                     'updated_at' => $sync2['molding_finding'][$i]->updated_at,
        //                     'deleted_at' => $sync2['molding_finding'][$i]->deleted_at,
        //                 ]);

        //             $update_check = DB::connection('ympimis_2')
        //                 ->table('pe_molding_handlings')
        //                 ->insert([
        //                     'id' => $sync2['molding_handling'][$i]->id,
        //                     'finding_id' => $sync2['molding_handling'][$i]->finding_id,
        //                     'check_date' => $sync2['molding_handling'][$i]->check_date,
        //                     'pic' => $sync2['molding_handling'][$i]->pic,
        //                     'molding_name' => $sync2['molding_handling'][$i]->molding_name,
        //                     'part_name' => $sync2['molding_handling'][$i]->part_name,
        //                     'handling_note' => $sync2['molding_handling'][$i]->handling_note,
        //                     'handling_att1' => $sync2['molding_handling'][$i]->handling_att1,
        //                     'handling_att2' => $sync2['molding_handling'][$i]->handling_att2,
        //                     'status' => $sync2['molding_handling'][$i]->status,
        //                     'remark' => $sync2['molding_handling'][$i]->remark,
        //                     'sync_at' => $sync2['sync_at'],
        //                     'created_by' => $sync2['molding_handling'][$i]->created_by,
        //                     'created_at' => $sync2['molding_handling'][$i]->created_at,
        //                     'updated_at' => $sync2['molding_handling'][$i]->updated_at,
        //                     'deleted_at' => $sync2['molding_handling'][$i]->deleted_at,
        //                 ]);

        //         } catch (Exception $e) {
        //             DB::rollback();
        //             $messages2 .= 'Error : ' . substr($e->getMessage(), 0, 50) . '%0A';
        //             $status = false;
        //         }
        //     }

        //     if ($status) {
        //         DB::commit();
        //         $messages2 .= "Success : Syncing Molding Audit Vendor Data%0A";
        //     }

        // } else {
        //     $messages2 .= "No Synced Data%0A";

        //     $messages2 .= "Syncing Molding Audit Vendor Data to Vendor%0A";

        //     $molding_check = db::connection('ympimis_2')->select("SELECT * from pe_molding_checks where sync_at is null");
        //     $molding_check_detail = db::connection('ympimis_2')->select("SELECT * from pe_molding_check_details where sync_at is null");
        //     $molding_finding = db::connection('ympimis_2')->select("SELECT * from pe_molding_findings where sync_at is null");
        //     $molding_handling = db::connection('ympimis_2')->select("SELECT * from pe_molding_handlings where sync_at is null");

        //     db::connection('ympimis_2')->table('pe_molding_checks')->whereNull('sync_at')
        //         ->update(['sync_at' => date('Y-m-d H:i:s')]);

        //     db::connection('ympimis_2')->table('pe_molding_check_details')->whereNull('sync_at')
        //         ->update(['sync_at' => date('Y-m-d H:i:s')]);

        //     db::connection('ympimis_2')->table('pe_molding_findings')->whereNull('sync_at')
        //         ->update(['sync_at' => date('Y-m-d H:i:s')]);

        //     db::connection('ympimis_2')->table('pe_molding_handlings')->whereNull('sync_at')
        //         ->update(['sync_at' => date('Y-m-d H:i:s')]);

        //     $data = array(
        //         'molding_check' => $molding_check,
        //         'molding_check_detail' => $molding_check_detail,
        //         'molding_finding' => $molding_finding,
        //         'molding_handling' => $molding_handling,
        //         'sync_at' => date('Y-m-d H:i:s'),
        //     );

        //     $link = 'post_data_molding';
        //     $param = json_encode($data);
        //     $method = 'POST';

        //     $controller = app()->make('App\Http\Controllers\BridgeForVendorController');
        //     $sync = app()->call([$controller, 'getApi'], [
        //         'token' => $token_bfv,
        //         'link' => $link,
        //         'method' => $method,
        //         'param' => $param,
        //     ]);
        // }
    }
}
