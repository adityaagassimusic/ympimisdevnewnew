<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GeneralAffairsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'automatic:general_affairs';

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
        // $week = DB::SELECT("SELECT
        //     week_name,
        //     week_date 
        // FROM
        //     weekly_calendars 
        // WHERE
        //     week_date = DATE(
        //     NOW() + INTERVAL 7 DAY)");

        // $dates = DB::SELECT("SELECT
        //     week_date,DAYNAME(week_date) as day_name,remark,week_name
        // FROM
        //     weekly_calendars 
        // WHERE
        //     week_name = '".$week[0]->week_name."'
        //     and DATE_FORMAT(week_date,'%Y-%m') = '".date('Y-m',strtotime($week[0]->week_date))."'");

        // for ($i=0; $i < count($dates); $i++) { 
        //     if ($dates[$i]->remark != 'H') {
        //         $schedule = DB::connection('ympimis_2')->SELECT("SELECT
        //             * 
        //         FROM
        //             general_gym_schedules 
        //         WHERE
        //             remark LIKE '%".$dates[$i]->day_name."%'");

        //         for ($j=0; $j < count($schedule); $j++) { 
        //             $insert = DB::connection('ympimis_2')->table('general_gym_quotas')->insert([
        //                 'date' => $dates[$i]->week_date,
        //                 'gender' => $schedule[$j]->gender,
        //                 'start_time' => $schedule[$j]->start_time,
        //                 'end_time' => $schedule[$j]->end_time,
        //                 'capacity' => $schedule[$j]->capacity,
        //                 'order' => 0,
        //                 'week_name' => $dates[$i]->week_name,
        //                 'created_by' => 1,
        //                 'created_at' => date('Y-m-d H:i:s'),
        //                 'updated_at' => date('Y-m-d H:i:s'),
        //             ]);
        //         }
        //     }
        // }

        $driver = DB::table('driver_lists')->where('position','CUTI')->get();
        if (count($driver) > 0) {
            for ($i=0; $i < count($driver); $i++) { 
                $update = DB::table('driver_lists')->where('id',$driver[$i]->id)->update([
                    'position' => 'YMPI',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }
}
