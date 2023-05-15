<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class ScheduleGS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:update_gs';

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

     $date_day = date('Y-m-d');
     $today_name = date('l');

     if ($today_name == 'Monday') {
        $date_yest = date('Y-m-d', strtotime("-3 days"));
    }else{
        $date_yest = date('Y-m-d', strtotime("-1 days"));
    }


    if ($first >= "05:00" && $first < "08:00") {
        $get_data = db::connection('ympimis_2')->select("
           SELECT
    * 
           FROM
           `gs_actual_jobs` 
           WHERE
           status = 'idle' AND date_format( request_at, '%Y-%m-%d' ) = '".$date_yest."'
           AND date_format( request_at, '%H:%i:%s' ) BETWEEN '05:25:00' AND '07:00:00'
           ");

        for ($i=0; $i < count($get_data); $i++) { 

            if ($get_data[$i]->employee_id == "OS0009") {

                $insert_data1 = DB::connection('ympimis_2')->table('gs_actual_jobs')
                ->insert([
                    'employee_id' => $get_data[$i]->employee_id,
                    'status' => $get_data[$i]->status,
                    'request_at' => date('Y-m-d 05:25:00'),
                    'updated_at' => date('Y-m-d 05:25:00'),
                    'created_at' => date('Y-m-d 05:25:00')
                ]);
            }else{
             $insert_data = DB::connection('ympimis_2')->table('gs_actual_jobs')
             ->insert([
                'employee_id' => $get_data[$i]->employee_id,
                'status' => $get_data[$i]->status,
                'request_at' => date('Y-m-d 07:00:00'),
                'updated_at' => date('Y-m-d 07:00:00'),
                'created_at' => date('Y-m-d 07:00:00')
            ]);
         }
     }

     $get_data_list = db::connection('ympimis_2')->select("
         SELECT
    * 
         FROM
         `gs_daily_jobs` 
         WHERE
         `dates` = '".$date_yest."' and category is not null and area is not null
         ");

     for ($m=0; $m < count($get_data_list); $m++) { 
         $insert_data_list = DB::connection('ympimis_2')->table('gs_daily_jobs')
         ->insert([
            'category' => $get_data_list[$m]->category,
            'area' => $get_data_list[$m]->area,
            'list_job' => $get_data_list[$m]->list_job,
            'operator_gs' => $get_data_list[$m]->operator_gs,
            'names' => $get_data_list[$m]->names,
            'dates' => $date_day,
            'status' => '0',
            'updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

         $insert_data_list_log = DB::connection('ympimis_2')->table('gs_daily_job_logs')
         ->insert([
            'category' => $get_data_list[$m]->category,
            'area' => $get_data_list[$m]->area,
            'list_job' => $get_data_list[$m]->list_job,
            'operator_gs' => $get_data_list[$m]->operator_gs,
            'names' => $get_data_list[$m]->names,
            'dates' => $date_day,
            'status' => '0',
            'updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        ]);
     }

 }else if ($first >= "16:00" && $first < "16:30") {

     $get_data2 = db::connection('ympimis_2')->select("
         SELECT
    * 
         FROM
         `gs_actual_jobs` 
         WHERE
         finished_at IS NULL 
         AND date_format( request_at, '%Y-%m-%d' ) = '".$date_day."'
         ");

     for ($j=0; $j < count($get_data2); $j++) { 

        if ($get_data2[$j]->status == "idle") {
         $update_data = DB::connection('ympimis_2')
         ->table('gs_actual_jobs')
         ->where('employee_id','=',$get_data2[$j]->employee_id)
         ->where('finished_at','=',null)
         ->update([
           'finished_at' => date('Y-m-d 16:00:00'),
           'updated_at' => date('Y-m-d H:i:s') ,
       ]);

     }else{
         $update_data2 = DB::connection('ympimis_2')
         ->table('gs_actual_jobs')
         ->where('employee_id','=',$get_data2[$j]->employee_id)
         ->where('finished_at','=',null)
         ->update([
           'finished_at' => $get_data2[$j]->request_at,
           'updated_at' => date('Y-m-d H:i:s') ,
       ]);

         $data = DB::connection('ympimis_2')->SELECT("SELECT
    * 
            FROM
            gs_actual_jobs
            WHERE employee_id = '".$get_data2[$j]->employee_id."'    
            AND status != 'idle'
            ORDER BY
            request_at DESC 
            LIMIT 1");

         for ($p=0; $p < count($data); $p++) { 

             $insert_data3 = DB::connection('ympimis_2')->table('gs_actual_jobs')
             ->insert([
                'employee_id' => $data[$p]->employee_id,
                'status' => 'idle',
                'request_at' => $data[$p]->finished_at,
                'finished_at' => date('Y-m-d 16:00:00'),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ]);
         }

     }
 }

}

}
}
