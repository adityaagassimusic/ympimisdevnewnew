<?php

namespace App\Console\Commands;

use App\ErrorLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class YMESInterface extends Command
{

    protected $signature = 'ymes:interface';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $datetime = date('Y-m-d H:i:s');

        // START OFF IF WHEN YMES BATCH JOB OFF SATURDAY 21:00 - MONDAY 12:00
        if ((date('w') == 6) && (intval(date('H')) >= 21)) {
            exit;
        }
        if ((date('w') == 0) && (intval(date('H')) <= 12)) {
            exit;
        }
        // END

        // LIMIT INTERFACE
        $now = date('H:i:s');
        $s4_bj_time = '';

        $get_material_number = db::connection('ympimis_2')
            ->table('ymes_interface_excludes')
            ->where('type', 'material_number')
            ->select('exculde_point AS material_number')
            ->get();

        $exclude_data['material_number'] = [];
        for ($i = 0; $i < count($get_material_number); $i++) {
            array_push($exclude_data['material_number'], $get_material_number[$i]->material_number);
        }

        $get_storage_location = db::connection('ympimis_2')
            ->table('ymes_interface_excludes')
            ->where('type', 'storage_location')
            ->select('exculde_point AS location')
            ->get();

        $exclude_data['storage_location'] = [];
        for ($i = 0; $i < count($get_storage_location); $i++) {
            array_push($exclude_data['storage_location'], $get_storage_location[$i]->location);
        }

        $exclude_data = [
            'material_number' => $exclude_data['material_number'],
            'storage_location' => $exclude_data['storage_location'],
        ];

        $get_max_result_date = db::connection('ympimis_2')
            ->table('ymes_interface_excludes')
            ->where('type', 'result_date')
            ->first();

        if ($get_max_result_date) {
            $datetime = $get_max_result_date->exculde_point;
        }

        $sent_data = db::connection('ympimis_2')->table('i_ext0010');
        if ($now > '00:00:00' && $now <= '09:00:00') {
            $sent_data = $sent_data->where('instdt', '>', date('Y-m-d', strtotime('yesterday')) . ' 21:00:00');
            $sent_data = $sent_data->where('instdt', '<=', date('Y-m-d 09:00:00'));
            $s4_bj_time = '09:00';
        } elseif ($now > '09:00:00' && $now <= '12:00:00') {
            $sent_data = $sent_data->where('instdt', '>', date('Y-m-d 09:00:00'));
            $sent_data = $sent_data->where('instdt', '<=', date('Y-m-d 12:00:00'));
            $s4_bj_time = '12:00';
        } elseif ($now > '12:00:00' && $now <= '16:00:00') {
            $sent_data = $sent_data->where('instdt', '>', date('Y-m-d 12:00:00'));
            $sent_data = $sent_data->where('instdt', '<=', date('Y-m-d 16:00:00'));
            $s4_bj_time = '16:00';
        } elseif ($now > '16:00:00' && $now <= '21:00:00') {
            $sent_data = $sent_data->where('instdt', '>', date('Y-m-d 16:00:00'));
            $sent_data = $sent_data->where('instdt', '<=', date('Y-m-d 21:00:00'));
            $s4_bj_time = '21:00';
        }
        $sent_data = $sent_data->get();

        if (count($sent_data) >= 2000) {
            $body = "I/F OFF : " . count($sent_data) . " records data already sent for YMES S4 batchjob at " . $s4_bj_time;
            self::mailReport($body);

            exit;
        }

        $prepared_data = [];
        $prepared_production_results = db::connection('ympimis_2')->table('production_results')
            ->where('production_results.category', 'not like', "%error%")
            ->whereNull('production_results.synced')
            ->whereNull('production_results.deleted_at')
            ->where('production_results.result_date', '<=', $datetime)
            ->where(function ($query) use ($exclude_data) {
                $query->whereNotIn('production_results.material_number', $exclude_data['material_number'])
                    ->whereNotIn('production_results.issue_location', $exclude_data['storage_location']);
            })
            ->get();

        $prepared_production_result_temps = db::connection('ympimis_2')->table('production_result_temps')
            ->where('production_result_temps.category', 'not like', "%error%")
            ->whereNull('production_result_temps.synced')
            ->whereNull('production_result_temps.deleted_at')
            ->where('production_result_temps.result_date', '<=', $datetime)
            ->where(function ($query) use ($exclude_data) {
                $query->whereNotIn('production_result_temps.material_number', $exclude_data['material_number'])
                    ->whereNotIn('production_result_temps.issue_location', $exclude_data['storage_location']);
            })
            ->get();

        for ($i = 0; $i < count($prepared_production_results); $i++) {
            $row = $prepared_production_results[$i]->material_number;
            if ($prepared_production_results[$i]->serial_number != null) {
                $row = $prepared_production_results[$i]->serial_number;
            }
            if (!in_array($row, $prepared_data)) {
                array_push($prepared_data, $row);
            }
        }

        for ($i = 0; $i < count($prepared_production_result_temps); $i++) {
            $row = $prepared_production_result_temps[$i]->material_number;
            if ($prepared_production_result_temps[$i]->serial_number != null) {
                $row = $prepared_production_result_temps[$i]->serial_number;
            }
            if (!in_array($row, $prepared_data)) {
                array_push($prepared_data, $row);
            }
        }

        $limit_status = false;
        if ((count($sent_data) + count($prepared_data)) > 2000) {
            $limit = 100000;
            $limit_status = true;

            do {
                if ($limit <= 0) {
                    $body = "I/F OFF : Limit 0, " . count($sent_data) . " records data already sent for YMES S4 batchjob at " . $s4_bj_time;
                    self::mailReport($body);

                    exit;
                }

                $prepared_data = [];
                $prepared_production_results = db::connection('ympimis_2')->table('production_results')
                    ->where('production_results.category', 'not like', "%error%")
                    ->whereNull('production_results.synced')
                    ->whereNull('production_results.deleted_at')
                    ->where('production_results.result_date', '<=', $datetime)
                    ->where(function ($query) use ($exclude_data) {
                        $query->whereNotIn('production_results.material_number', $exclude_data['material_number'])
                            ->whereNotIn('production_results.issue_location', $exclude_data['storage_location']);
                    })
                    ->limit($limit)
                    ->get();

                $prepared_production_result_temps = db::connection('ympimis_2')->table('production_result_temps')
                    ->where('production_result_temps.category', 'not like', "%error%")
                    ->whereNull('production_result_temps.synced')
                    ->whereNull('production_result_temps.deleted_at')
                    ->where('production_result_temps.result_date', '<=', $datetime)
                    ->where(function ($query) use ($exclude_data) {
                        $query->whereNotIn('production_result_temps.material_number', $exclude_data['material_number'])
                            ->whereNotIn('production_result_temps.issue_location', $exclude_data['storage_location']);
                    })
                    ->limit($limit)
                    ->get();

                for ($i = 0; $i < count($prepared_production_results); $i++) {
                    $row = $prepared_production_results[$i]->material_number;
                    if ($prepared_production_results[$i]->serial_number != null) {
                        $row = $prepared_production_results[$i]->serial_number;
                    }
                    if (!in_array($row, $prepared_data)) {
                        array_push($prepared_data, $row);
                    }
                }

                for ($i = 0; $i < count($prepared_production_result_temps); $i++) {
                    $row = $prepared_production_result_temps[$i]->material_number;
                    if ($prepared_production_result_temps[$i]->serial_number != null) {
                        $row = $prepared_production_result_temps[$i]->serial_number;
                    }
                    if (!in_array($row, $prepared_data)) {
                        array_push($prepared_data, $row);
                    }
                }

                echo 'Limit ' . $limit . ' => ' . count($sent_data) . '+' . count($prepared_data) . '=' . (count($sent_data) + count($prepared_data)) . "\n";

                if ((count($sent_data) + count($prepared_data)) > 2000) {
                    $limit -= 1000;
                }

            } while ((count($sent_data) + count($prepared_data)) > 2000);
        }

        // START INTERFACE
        $batch_time = date('Y-m-d H:i:s');
        try {
            $this_if_sent = 0;

            $sync_production_results = db::connection('ympimis_2')->table('production_results')
                ->where('production_results.category', 'not like', "%error%")
                ->whereNull('production_results.synced')
                ->whereNull('production_results.deleted_at')
                ->where('production_results.result_date', '<=', $datetime)
                ->where(function ($query) use ($exclude_data) {
                    $query->whereNotIn('production_results.material_number', $exclude_data['material_number'])
                        ->whereNotIn('production_results.issue_location', $exclude_data['storage_location']);
                });
            if ($limit_status) {
                $sync_production_results = $sync_production_results->limit($limit);
            }
            $sync_production_results = $sync_production_results->update([
                'synced' => $batch_time,
                'synced_by' => 'system',
            ]);

            $sync_production_result_temps = db::connection('ympimis_2')->table('production_result_temps')
                ->where('production_result_temps.category', 'not like', "%error%")
                ->whereNull('production_result_temps.synced')
                ->whereNull('production_result_temps.deleted_at')
                ->where('production_result_temps.result_date', '<=', $datetime)
                ->where(function ($query) use ($exclude_data) {
                    $query->whereNotIn('production_result_temps.material_number', $exclude_data['material_number'])
                        ->whereNotIn('production_result_temps.issue_location', $exclude_data['storage_location']);
                });
            if ($limit_status) {
                $sync_production_result_temps = $sync_production_result_temps->limit($limit);
            }
            $sync_production_result_temps = $sync_production_result_temps->update([
                'synced' => $batch_time,
                'synced_by' => 'system',
            ]);

            $interface_production_results = db::connection('ympimis_2')->select("SELECT
                serial_no,
                max( end_work_datetime ) AS end_work_datetime,
                item_code,
                dest_location_code,
                man_stat_cd,
                sum( qty ) AS qty
                FROM
                (
                    SELECT
                    serial_number AS serial_no,
                    result_date AS end_work_datetime,
                    material_number AS item_code,
                    issue_location AS dest_location_code,
                    mstation AS man_stat_cd,
                    quantity AS qty
                    FROM
                    production_results AS pr
                    WHERE
                    synced = '" . $batch_time . "' UNION ALL
                    SELECT
                    serial_number AS serial_no,
                    result_date AS end_work_datetime,
                    material_number AS item_code,
                    issue_location AS dest_location_code,
                    mstation AS man_stat_cd,
                    quantity AS qty
                    FROM
                    production_result_temps AS prt
                    WHERE
                    synced = '" . $batch_time . "'
                    ) AS production_results
                    GROUP BY
                    serial_no,
                    item_code,
                    dest_location_code,
                    man_stat_cd
                    HAVING
                    qty <> 0");

            $production_results = db::connection('ympimis_2')->table('production_results')
                ->where('synced', '=', $batch_time)
                ->get();

            foreach ($production_results as $row) {
                $insert_pr_log = db::connection('ympimis_2')
                    ->table('production_result_interface_logs')
                    ->insert([
                        'category' => $row->category,
                        'function' => $row->function,
                        'action' => $row->action,
                        'result_date' => $row->result_date,
                        'reference_number' => $row->reference_number,
                        'slip_number' => $row->slip_number,
                        'serial_number' => strtoupper($row->serial_number),
                        'material_number' => strtoupper($row->material_number),
                        'issue_location' => strtoupper($row->issue_location),
                        'mstation' => strtoupper($row->mstation),
                        'quantity' => $row->quantity,
                        'synced' => $batch_time,
                        'synced_by' => 'System',
                        'remark' => $row->remark,
                        'created_by' => $row->created_by,
                        'created_by_name' => $row->created_by,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ]);
            }

            $production_result_temps = db::connection('ympimis_2')->table('production_result_temps')
                ->where('synced', '=', $batch_time)
                ->get();

            foreach ($production_result_temps as $row) {
                $insert_pr_log = db::connection('ympimis_2')
                    ->table('production_result_interface_logs')
                    ->insert([
                        'category' => $row->category,
                        'function' => $row->function,
                        'action' => $row->action,
                        'result_date' => $row->result_date,
                        'reference_number' => $row->reference_number,
                        'slip_number' => $row->slip_number,
                        'serial_number' => strtoupper($row->serial_number),
                        'material_number' => strtoupper($row->material_number),
                        'issue_location' => strtoupper($row->issue_location),
                        'mstation' => strtoupper($row->mstation),
                        'quantity' => $row->quantity,
                        'synced' => $batch_time,
                        'synced_by' => 'System',
                        'remark' => $row->remark,
                        'created_by' => $row->created_by,
                        'created_by_name' => $row->created_by,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ]);
            }

            $delete_production_result_temps = db::connection('ympimis_2')->table('production_result_temps')
                ->where('synced', '=', $batch_time)
                ->delete();

            foreach ($interface_production_results as $row) {
                $send_data_no = md5(uniqid('', true));
                $input_type = 1;

                if ($row->qty < 0) {
                    $input_type = 2;
                    $insert_temp = db::connection('ympimis_2')
                        ->table('production_result_temps')
                        ->insert([
                            'production_result_id' => null,
                            'category' => 'production_result_temporary',
                            'function' => 'handle',
                            'action' => 'production_result',
                            'result_date' => $row->end_work_datetime,
                            'reference_number' => null,
                            'slip_number' => null,
                            'serial_number' => $row->serial_no,
                            'material_number' => $row->item_code,
                            'material_description' => null,
                            'issue_location' => $row->dest_location_code,
                            'mstation' => $row->man_stat_cd,
                            'quantity' => $row->qty,
                            'remark' => 'MIRAI',
                            'synced_by' => null,
                            'remark' => null,
                            'created_by' => 'System',
                            'created_by_name' => 'System',
                            'created_at' => $batch_time,
                            'updated_at' => $batch_time,
                        ]);
                } else {
                    $insert_pr = db::connection('ymes')
                        ->table('i_ext0010')
                        ->insert([
                            'plant_code' => '8190',
                            'send_data_no' => $send_data_no,
                            'send_type_id' => 'MIRAI',
                            'send_mgt_no' => null,
                            'ext_result_type' => '21',
                            'prod_individual_id' => null,
                            'work_order_no' => null,
                            'serial_no' => strtoupper($row->serial_no),
                            'item_code' => strtoupper($row->item_code),
                            'input_type' => $input_type,
                            'qty' => $row->qty,
                            'defect_qty' => null,
                            'reason_code' => null,
                            'start_work_datetime' => null,
                            'end_work_datetime' => $row->end_work_datetime,
                            'man_stat_cd' => strtoupper($row->man_stat_cd),
                            'dest_location_code' => strtoupper($row->dest_location_code),
                            'prod_stop_type' => null,
                            'staff_id' => null,
                            'machine_id' => null,
                            'ot_resource_id' => null,
                            'instid' => '',
                            'instdt' => $batch_time,
                            'instterm' => '',
                            'instprgnm' => '',
                            'updtid' => '',
                            'updtdt' => $batch_time,
                            'updtterm' => '',
                            'updtprgnm' => '',
                        ]);

                    $insert_pr_logs = db::connection('ympimis_2')
                        ->table('i_ext0010')
                        ->insert([
                            'plant_code' => '8190',
                            'send_data_no' => $send_data_no,
                            'send_type_id' => 'MIRAI',
                            'send_mgt_no' => null,
                            'ext_result_type' => '21',
                            'prod_individual_id' => null,
                            'work_order_no' => null,
                            'serial_no' => strtoupper($row->serial_no),
                            'item_code' => strtoupper($row->item_code),
                            'input_type' => $input_type,
                            'qty' => $row->qty,
                            'defect_qty' => null,
                            'reason_code' => null,
                            'start_work_datetime' => null,
                            'end_work_datetime' => $row->end_work_datetime,
                            'man_stat_cd' => strtoupper($row->man_stat_cd),
                            'dest_location_code' => strtoupper($row->dest_location_code),
                            'prod_stop_type' => null,
                            'staff_id' => null,
                            'machine_id' => null,
                            'ot_resource_id' => null,
                            'instid' => '',
                            'instdt' => $batch_time,
                            'instterm' => '',
                            'instprgnm' => '',
                            'updtid' => '',
                            'updtdt' => $batch_time,
                            'updtterm' => '',
                            'updtprgnm' => '',
                        ]);

                    $this_if_sent++;
                }
            }

            $sync_goods_movements = db::connection('ympimis_2')->table('goods_movements')
                ->where('goods_movements.category', 'not like', "%error%")
                ->whereNull('goods_movements.synced')
                ->whereNull('goods_movements.deleted_at')
                ->where('goods_movements.result_date', '<=', $datetime)
                ->where(function ($query) use ($exclude_data) {
                    $query->whereNotIn('goods_movements.material_number', $exclude_data['material_number'])
                        ->whereNotIn('goods_movements.issue_location', $exclude_data['storage_location'])
                        ->whereNotIn('goods_movements.receive_location', $exclude_data['storage_location']);
                });
            if ($limit_status) {
                $sync_goods_movements = $sync_goods_movements->limit($limit);
            }
            $sync_goods_movements = $sync_goods_movements->update([
                'synced' => $batch_time,
                'synced_by' => 'system',
            ]);

            $interface_sync_goods_movements = db::connection('ympimis_2')->table('goods_movements')
                ->where('synced', '=', $batch_time)
                ->get();

            foreach ($interface_sync_goods_movements as $row) {
                $insert_gm_log = db::connection('ympimis_2')
                    ->table('goods_movement_interface_logs')->insert([
                    'category' => $row->category,
                    'function' => $row->function,
                    'action' => $row->action,
                    'result_date' => $row->result_date,
                    'reference_number' => $row->reference_number,
                    'slip_number' => $row->slip_number,
                    'serial_number' => strtoupper($row->serial_number),
                    'material_number' => strtoupper($row->material_number),
                    'issue_location' => strtoupper($row->issue_location),
                    'receive_location' => strtoupper($row->receive_location),
                    'quantity' => $row->quantity,
                    'synced' => $batch_time,
                    'synced_by' => 'System',
                    'remark' => $row->remark,
                    'created_by' => 'System',
                    'created_by_name' => 'System',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $send_data_no = md5(uniqid('', true));
                $ext_move_type = '11';
                if (strlen($row->serial_number) > 0) {
                    $ext_move_type = '12';
                }

                $insert_gm = db::connection('ymes')
                    ->table('i_ext0020')
                    ->insert([
                        'plant_code' => '8190',
                        'send_data_no' => $send_data_no,
                        'send_type_id' => $row->remark,
                        'send_mgt_no' => null,
                        'ext_move_type' => $ext_move_type,
                        'result_date' => $row->result_date,
                        'issue_loc_code' => strtoupper($row->issue_location),
                        'in_loc_code' => strtoupper($row->receive_location),
                        'issue_strg_area_id' => null,
                        'in_strg_area_id' => null,
                        'qty' => $row->quantity,
                        'item_code' => strtoupper($row->material_number),
                        'serial_no' => strtoupper($row->serial_number),
                        'idtag_label_no' => null,
                        'trace_label_no' => null,
                        'prod_individual_id' => null,
                        'wrapping_no' => null,
                        'picking_no' => null,
                        'staff_id' => null,
                        'machine_id' => null,
                        'ot_resource_id' => null,
                        'instid' => '',
                        'instdt' => $batch_time,
                        'instterm' => '',
                        'instprgnm' => '',
                        'updtid' => '',
                        'updtdt' => $batch_time,
                        'updtterm' => '',
                        'updtprgnm' => '',
                    ]);

                $insert_gm_logs = db::connection('ympimis_2')
                    ->table('i_ext0020')
                    ->insert([
                        'plant_code' => '8190',
                        'send_data_no' => $send_data_no,
                        'send_type_id' => $row->remark,
                        'send_mgt_no' => null,
                        'ext_move_type' => $ext_move_type,
                        'result_date' => $row->result_date,
                        'issue_loc_code' => strtoupper($row->issue_location),
                        'in_loc_code' => strtoupper($row->receive_location),
                        'issue_strg_area_id' => null,
                        'in_strg_area_id' => null,
                        'qty' => $row->quantity,
                        'item_code' => strtoupper($row->material_number),
                        'serial_no' => strtoupper($row->serial_number),
                        'idtag_label_no' => null,
                        'trace_label_no' => null,
                        'prod_individual_id' => null,
                        'wrapping_no' => null,
                        'picking_no' => null,
                        'staff_id' => null,
                        'machine_id' => null,
                        'ot_resource_id' => null,
                        'instid' => '',
                        'instdt' => $batch_time,
                        'instterm' => '',
                        'instprgnm' => '',
                        'updtid' => '',
                        'updtdt' => $batch_time,
                        'updtterm' => '',
                        'updtprgnm' => '',
                    ]);
            }

            Artisan::call('ymes:error');

            $body = "Success";
            $body .= "\r\n";
            if ($limit_status) {
                $body .= "Limit : " . $limit;
                $body .= "\r\n";
                $body .= "I/F ON : " . (count($sent_data) + $this_if_sent) . " records data already sent for YMES S4 batchjob at " . $s4_bj_time;
            } else {
                $body .= "Limit : " . $limit_status;
                $body .= "\r\n";
                $body .= "I/F ON : " . (count($sent_data) + $this_if_sent) . " records data already sent for YMES S4 batchjob at " . $s4_bj_time;
            }
            self::mailReport($body);

        } catch (Exception $e) {

            $error_log = new ErrorLog([
                'error_message' => $e->getMessage(),
                'created_by' => 'system',
            ]);
            $error_log->save();

            $body = "Error message : " . $e->getMessage();
            self::mailReport($body);

        }

    }
    // END INTERFACE

    public function mailReport($body)
    {
        $mail_to = [
            'mamluatul.atiyah@music.yamaha.com',
            'farizca.nurma@music.yamaha.com',
            'ade.laksmana.putra@music.yamaha.com',
            'istiqomah@music.yamaha.com',
            'ali.murdani@music.yamaha.com',
        ];
        $mail_bcc = ['ympi-mis-ML@music.yamaha.com'];
        $title = "I/F MIRAI to YMES";

        Mail::raw([], function ($message) use ($title, $body, $mail_to, $mail_bcc) {
            $message->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia');
            $message->to($mail_to);
            $message->bcc($mail_bcc);
            $message->subject($title);
            $message->setBody($body, 'text/plain');}
        );
    }

}
