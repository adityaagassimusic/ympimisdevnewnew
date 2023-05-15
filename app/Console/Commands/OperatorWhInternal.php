<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\WarehouseTimeOperatorLog;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\WarehouseEmployeeMaster;



class OperatorWhInternal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:updateWhInternal';

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

        $first = date('H:i');
        if ($first >= "16:25" && $first < "16:35") {
            $insert_data = db::select("
                SELECT
                ss.id,
                ss.employee_id,
                ss.end_job 
                FROM
                warehouse_time_operator_logs AS ss 
                WHERE
                ss.end_job is NULL 
                ");
            $insert_up = db::select("
                SELECT
                ss.id,
                ss.employee_id
                FROM
                warehouse_employee_masters AS ss 
                WHERE
                ss.shift = 'Shift_1' || ss.shift = 'Shift_1_Genba' || ss.shift = 'Shift_1_Jumat'
                ");

            foreach($insert_data as $insert_datas){

                for ($i=0; $i < count($insert_up) ; $i++) { 
                    $update = WarehouseTimeOperatorLog::where('id', '=', $insert_datas->id)
                    ->update([
                        'end_job' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            for ($i=0; $i < count($insert_up) ; $i++) { 
                $update_akhir = WarehouseEmployeeMaster::where('id', '=', $insert_up[$i]->id)
                ->update([
                    'status' => "off",
                    'start_time_status' => null
                ]);
            }
        }

        else if($first >= "06:57" && $first <= "07:10") {
            $insert_data2 = db::select("
             SELECT
             es.employee_id AS employee_id,
             es.STATUS AS status1,
             eg.shiftdaily_code AS shift,
             IF
             (
                 eg.shiftdaily_code = 'Shift_1' || eg.shiftdaily_code = 'Shift_1_Genba' || eg.shiftdaily_code = 'Shift_1_Jumat',
                 IFNULL(
                     es.`start_time_status`,
                     DATE_FORMAT( NOW(), '%b %d %Y 07:00:00' )),
                 IFNULL(
                     es.`start_time_status`,
                     DATE_FORMAT( NOW(), '%b %d %Y 16:00:00' ))) start_time_status 
             FROM
             sunfish_shift_syncs AS eg
             LEFT JOIN warehouse_employee_masters AS es ON es.employee_id = eg.employee_id 
             WHERE
             eg.shift_date = date('Y-m-d') 
             AND es.employee_id = eg.employee_id 
             ORDER BY
             shift,
             shift ASC
             ");

            foreach($insert_data2 as $insert_datas2){ 
                for ($i=0; $i < count($insert_data2); $i++) { 
                    $update_shipment = WarehouseEmployeeMaster::where('employee_id', '=', $insert_data2[$i]->employee_id)->where('shift', '=', 'Shift_1')->orwhere('shift', '=', 'Shift_1_Genba')->orwhere('shift', '=', 'Shift_1_Jumat')
                    ->update([
                        'status' => "idle",
                        'start_time_status' => date('Y-m-d H:i:s')

                    ]);

                }
            }

            $insert_da = db::select("
                SELECT id, employee_id FROM warehouse_employee_masters WHERE shift = 'Shift_1' || shift = 'Shift_1_Genba' || shift = 'Shift_1_Jumat'
                ");

            for ($j=0; $j < count($insert_da); $j++) { 
            # code...
              $error_log = new WarehouseTimeOperatorLog([
               'employee_id' => $insert_da[$j]->employee_id,
               'status' => "idle",
               'start_job' => date('Y-m-d H:i:s')
           ]);
              $error_log->save();
          }
      }

      else if($first >= "16:55" && $first <= "17:05") {
        $insert_data2 = db::select("
         SELECT
         es.employee_id AS employee_id,
         es.STATUS AS status1,
         eg.shiftdaily_code AS shift,
         IF
         (
             eg.shiftdaily_code = 'Shift_1' || eg.shiftdaily_code = 'Shift_1_Genba' || eg.shiftdaily_code = 'Shift_1_Jumat',
             IFNULL(
                 es.`start_time_status`,
                 DATE_FORMAT( NOW(), '%b %d %Y 07:00:00' )),
             IFNULL(
                 es.`start_time_status`,
                 DATE_FORMAT( NOW(), '%b %d %Y 16:00:00' ))) start_time_status 
         FROM
         sunfish_shift_syncs AS eg
         LEFT JOIN warehouse_employee_masters AS es ON es.employee_id = eg.employee_id 
         WHERE
         eg.shift_date = date('Y-m-d') 
         AND es.employee_id = eg.employee_id 
         ORDER BY
         shift,
         shift ASC
         ");

        foreach($insert_data2 as $insert_datas2){ 
            for ($i=0; $i < count($insert_data2); $i++) { 
                $update_shipment = WarehouseEmployeeMaster::where('employee_id', '=', $insert_data2[$i]->employee_id)->where('shift', '=', 'Shift_2')->orwhere('shift', '=', 'Shift_2_Genba')->orwhere('shift', '=', 'Shift_2_Jumat')
                ->update([
                    'status' => "idle",
                    'start_time_status' => date('Y-m-d H:i:s')

                ]);

            }
        }

        $insert_da = db::select("
            SELECT id, employee_id FROM warehouse_employee_masters WHERE shift = 'Shift_2' || shift = 'Shift_2_Genba' || shift = 'Shift_2_Jumat'
            ");

        for ($j=0; $j < count($insert_da); $j++) { 
            # code...
          $error_log = new WarehouseTimeOperatorLog([
           'employee_id' => $insert_da[$j]->employee_id,
           'status' => "idle",
           'start_job' => date('Y-m-d H:i:s')
       ]);
          $error_log->save();
      }
  }
  else if ($first >= "01:10" && $first < "01:20") {
    $insert_data = db::select("
        SELECT
        ss.id,
        ss.employee_id,
        ss.end_job 
        FROM
        warehouse_time_operator_logs AS ss 
        WHERE
        ss.end_job is NULL 
        ");
    $insert_up = db::select("
        SELECT
        ss.id,
        ss.employee_id
        FROM
        warehouse_employee_masters AS ss 
        WHERE
        ss.shift = 'Shift_2' || ss.shift = 'Shift_2_Genba' || ss.shift = 'Shift_2_Jumat'
        ");

    foreach($insert_data as $insert_datas){

        for ($i=0; $i < count($insert_up) ; $i++) { 
            $update = WarehouseTimeOperatorLog::where('id', '=', $insert_datas->id)
            ->update([
                'end_job' => date('Y-m-d H:i:s')
            ]);
        }
    }

    for ($i=0; $i < count($insert_up) ; $i++) { 
        $update_akhir = WarehouseEmployeeMaster::where('id', '=', $insert_up[$i]->id)
        ->update([
            'status' => "off",
            'start_time_status' => null
        ]);
    }
}
}
}
