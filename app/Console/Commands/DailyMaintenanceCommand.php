<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\EmployeeSync;
use App\Approver;
use App\User;

class DailyMaintenanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:maintenance';

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
        //TBM REMINDER
        $date = date('Y-m-d');
        $month = date('Y-m');

        if ($date == $month.'-25') {

            $location = DB::connection('ympimis_2')->select("SELECT DISTINCT
                ( category ),
                location,
                point_check,
                scan_index,
                specification,
                CONCAT(
                category,
                COALESCE ( location, '_' ),
                COALESCE ( point_check, '_' ),
                COALESCE ( scan_index, '_' ),
                COALESCE ( specification, '_' )) AS concat 
                FROM
                daily_audit_schedules 
                WHERE
                schedule_status = 'Belum Dikerjakan' 
                AND DATE_FORMAT( schedule_date, '%Y-%m' ) <= '".$month."'");

            $schedule_date = DB::connection('ympimis_2')->select("SELECT DISTINCT
                ( schedule_date ) 
                FROM
                daily_audit_schedules 
                WHERE
                schedule_status = 'Belum Dikerjakan' 
                AND DATE_FORMAT( schedule_date, '%Y-%m' ) <= '".$month."'");

            // $tbm = DB::connection('ympimis_2')->select("SELECT
            //     category,
            //     count( schedule_date ) AS quantity,
            //     schedule_date 
            // FROM
            //     daily_audit_schedules 
            // WHERE
            //     schedule_status = 'Belum Dikerjakan' 
            //     AND DATE_FORMAT( schedule_date, '%Y-%m' ) <= '".$month."' 
            // GROUP BY
            //     category,
            //     schedule_date");
            $tbm = DB::connection('ympimis_2')->select("SELECT
                    *,
                CONCAT(
                category,
                COALESCE ( location, '_' ),
                COALESCE ( point_check, '_' ),
                COALESCE ( scan_index, '_' ),
                COALESCE ( specification, '_' )) AS concat 
                FROM
                daily_audit_schedules 
                WHERE
                schedule_status = 'Belum Dikerjakan' 
                AND DATE_FORMAT( schedule_date, '%Y-%m' ) <= '".$month."'");

            $data = array(
                'location' => $location, 
                'schedule_date' => $schedule_date, 
                'tbm' => $tbm, 
            );

            $mail_to = [];
            array_push($mail_to, 'bambang.supriyadi@music.yamaha.com');
            array_push($mail_to, 'nadif@music.yamaha.com');

            if (count($tbm) > 0) {
                Mail::to($mail_to)
                ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'nasiqul.ibat@music.yamaha.com'])
                ->send(new SendEmail($data, 'tbm_reminder'));
            }
        }

        // --------  DOMESTIC PUMP ----------------

        $mtc_pump = db::table('sensor_data_logs')->whereNull('finish_date')->where('item_name', '=', 'Domestic_Pump')->first();

        if (count($mtc_pump) > 0) {
            if (date('d', strtotime($mtc_pump->start_date)) != date('d')) {

                $day = date('d', strtotime($mtc_pump->start_date));

                DB::table('sensor_data_logs')
                ->whereNull('finish_date')
                ->where('item_name', '=', 'Domestic_Pump')
                ->update(
                    [
                        'finish_date' => date('Y-m-'.$day.' 23:59:59'),
                        'status' => 'OFF'
                    ]
                );

                DB::table('sensor_data_logs')->insert(
                    [
                        'item_name' => 'Domestic_Pump',
                        'category' => 'Volume',
                        'status' => 'ON',
                        'start_date' => date('Y-m-d 00:00:01'),
                        'created_by' => '1',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]
                );
            }

        }
    }
}
