<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Employee;

class SyncAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:attendance';

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
