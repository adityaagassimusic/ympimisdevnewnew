<?php

namespace App\Console\Commands;

use App\Libraries\ActMLEasyIf;
use App\Plc;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RoomTemperatureLog extends Command
{
/**
 * The name and signature of the console command.
 *
 * @var string
 */
    protected $signature = 'log:room_temperature';

/**
 * The console command description.
 *
 * @var string
 */
    protected $description = 'Command description';

/**
 * Create a new command instance.
 *
 * @return void
 */
    public function __construct()
    {
        parent::__construct();
    }

/**
 * Execute the console command.
 *
 * @return mixed
 */
    public function handle()
    {

        $plcs = Plc::where('station', 3)
            ->orderBy('location', 'asc')
            ->get();

        $lists = array();
        $date = date('Y-m-d H:i:s');

        foreach ($plcs as $plc) {
            $cpu = new ActMLEasyIf($plc->station);
            $datas = $cpu->read_data($plc->address, 10);
            $data = $datas[$plc->arr];

            $log = db::table('temperature_room_logs')
                ->insert([
                    'location' => $plc->location,
                    'remark' => $plc->remark,
                    'value' => $data,
                    'upper_limit' => $plc->upper_limit,
                    'lower_limit' => $plc->lower_limit,
                    'created_by' => 1,
                    'created_at' => $date,
                ]);
        }

        $q1 = "INSERT into patient_logs ( idx, employee_id, tanggal, durasi_detik, in_time, out_time, status, id_mesin, uid, first_ref_idx, last_ref_idx, flag, create_ts, note)
                    SELECT idx, employee_id, tanggal, TIMESTAMPDIFF(second,in_time,now()), in_time, now(), status, id_mesin, uid, first_ref_idx, last_ref_idx, flag, create_ts, note from patient_list
                    WHERE TIMESTAMPDIFF(minute,in_time,now()) > 150 and employee_id not like 'PR%'";

        $q2 = "delete from patient_list where TIMESTAMPDIFF(minute,in_time,now()) > 150 and employee_id not like 'PR%'";

        $insert_klinik = db::connection('clinic')->select($q1);
        $delete_klinik = db::connection('clinic')->select($q2);

        $delete_stamp_sx = db::select("DELETE FROM stamp_inventories
            WHERE serial_number IN ( SELECT serial_number FROM flo_details WHERE origin_group_code = '043' )
            AND origin_group_code = '043'");
    }
}
