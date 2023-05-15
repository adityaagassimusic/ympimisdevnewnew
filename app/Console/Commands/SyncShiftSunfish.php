<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Employee;

class SyncShiftSunfish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:shift_sunfish';

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
        $insert = array();
        $month = date('Y-m', strtotime("-6 month", strtotime(date('Y-m-d'))));
        $nextmonth = date('Y-m', strtotime("+1 month", strtotime(date('Y-m-d'))));
        $datas = db::connection('sunfish')->select("SELECT
            VIEW_YMPI_Emp_Attendance.emp_no AS employee_id,
            VIEW_YMPI_Emp_Attendance.shiftdaily_code AS shiftdaily_code,
            VIEW_YMPI_Emp_Attendance.Attend_Code AS attend_code,
            FORMAT ( shiftstarttime, 'yyyy-MM-dd' ) AS shift_date 
        FROM
            VIEW_YMPI_Emp_Attendance 
        WHERE
            VIEW_YMPI_Emp_Attendance.emp_no <> 'sunfish' 
            AND FORMAT ( shiftstarttime, 'yyyy-MM' ) BETWEEN '".$month."'
            AND '".$nextmonth."' 
        GROUP BY
            VIEW_YMPI_Emp_Attendance.emp_no,
                VIEW_YMPI_Emp_Attendance.shiftdaily_code,
                VIEW_YMPI_Emp_Attendance.Attend_Code,
                FORMAT ( shiftstarttime, 'yyyy-MM-dd' )
        ORDER BY
            shift_date");
        $datas2 = json_decode(json_encode($datas), true);

        foreach ($datas2 as $data) {
            $row = array();

            $row['employee_id'] = $data['employee_id'];
            $row['shiftdaily_code'] = $data['shiftdaily_code'];
            $row['attend_code'] = $data['attend_code'];
            $row['shift_date'] = $data['shift_date'];
            $row['created_at'] = date('Y-m-d H:i:s');

            $insert[] = $row;
        }

        DB::table('sunfish_shift_syncs')->truncate();
        foreach (array_chunk($insert,1000) as $t)  
        {
            DB::table('sunfish_shift_syncs')->insert($t);
        }

        $ivms = DB::SELECT("SELECT
            * 
        FROM
            ivms.ivms_attendance_triggers 
        WHERE
            auth_date BETWEEN DATE( NOW() - INTERVAL 60 DAY ) 
            AND DATE(
            NOW() 
            )");
        $suhu = [];

        foreach ($ivms as $data) {
            $suhutinggi = array(
                 'employee_id' => $data->employee_id,
                 'auth_date' => $data->auth_date,
                 'auth_datetime' => $data->auth_datetime,
                 'device' => $data->device,
                 'device_serial' => $data->device_serial,
                 'created_by' => 1,
                 'created_at' => date('Y-m-d H:i:s'),
                 'updated_at' => date('Y-m-d H:i:s'),
            );
            array_push($suhu,$suhutinggi);
        }

        DB::table('ivms_attendance')->truncate();
        foreach (array_chunk($suhu,1000) as $t)  
        {
            DB::table('ivms_attendance')->insert($t);
        }

    }
}
