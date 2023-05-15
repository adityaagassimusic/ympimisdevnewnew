<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use App\RcNgBox;
use Illuminate\Support\Facades\Mail;

class InjectionVisualCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'injection:visual_check';

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
        // $j = 2;
        // $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
        // // if (date('D')=='Thu') {
        // //     $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays(++$j)));
        // // }
        // $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
        // foreach ($weekly_calendars as $key) {
        //     if ($key->week_date == $nextdayplus1) {
        //         if ($key->remark == 'H') {
        //             $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays(++$j)));
        //         }
        //     }
        // }
        $yesterday = strtotime(date('w') == 0 || date('w') == 6 ? 'last friday' : 'yesterday');
        $yesterdays = date('Y-m-d', $yesterday);
        $visual_check = DB::SELECT("SELECT
          week_date AS date,
          ( SELECT GROUP_CONCAT( hour_check ) FROM injection_visual_check_schedules ) AS schedules,
          ( SELECT GROUP_CONCAT( DISTINCT ( a.hour_check )) FROM injection_visual_checks a WHERE DATE( a.created_at ) = weekly_calendars.week_date ) AS hour_check 
        FROM
          weekly_calendars 
        WHERE
          week_date = '".$yesterdays."'
          AND remark != 'H'");
        $schedule_belum = [];
        if (count($visual_check) > 0) {
            for ($i=0; $i < count($visual_check); $i++) { 
                $schedules = explode(',', $visual_check[$i]->schedules);
                    if ($visual_check[$i]->hour_check != null) {
                        $hour = explode(',', $visual_check[$i]->hour_check);
                        for($m = 0; $m < count($schedules); $m++){
                            if (in_array($schedules[$m],$hour )) {

                            }else{
                                array_push($schedule_belum, [
                                    'date' => $visual_check[$i]->date,
                                    'hour' => $schedules[$m],
                                ]);
                            }
                        }
                    }else{
                        for($o = 0; $o < count($schedules);$o++){
                            array_push($schedule_belum, [
                                'date' => $visual_check[$i]->date,
                                'hour' => $schedules[$o],
                            ]);
                        }
                    }
            }
        }
        if (count($schedule_belum) > 0) {
            $contactList = [];
            $contactList[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
            $mail_to = [];
            $mail_to[0] = 'eko.prasetyo.wicaksono@music.yamaha.com';
            Mail::to($mail_to)->bcc($contactList,'Contact List')->send(new SendEmail($schedule_belum, 'visual_check'));
        }

        $yesterday = strtotime(date('w') == 0 || date('w') == 6 ? 'last friday' : 'yesterday');
        $yesterdays = date('Y-m-d', $yesterday);
        $dayname = date('D', strtotime($yesterdays));

        if ($dayname == 'Mon' || $dayname == 'Wek' || $dayname == 'Fri') {
            $union = "UNION ALL
              SELECT
                  '' AS not_yet,
                  GROUP_CONCAT( DISTINCT ( CONCAT( injection_cleanings.point_check_type, ' ', injection_cleanings.point_check_machine )) ) AS done 
                FROM
                  injection_cleanings
                  JOIN injection_cleaning_points ON CONCAT( injection_cleaning_points.point_check_type, injection_cleaning_points.point_check_machine, injection_cleaning_points.point_check_index ) = CONCAT( injection_cleanings.point_check_type, injection_cleanings.point_check_machine, injection_cleanings.point_check_index ) 
                WHERE
                  injection_cleaning_points.point_check_schedule = 'weekly' 
                AND DATE( injection_cleanings.created_at ) = '".$yesterdays."'";
            $union2 = "UNION ALL SELECT
              GROUP_CONCAT( DISTINCT ( CONCAT( point_check_type, ' ', point_check_machine )) ) AS not_yet,
              '' AS done 
            FROM
              injection_cleaning_points 
              WHERE
            injection_cleaning_points.point_check_schedule = 'weekly'";
        }else{
          $union = "";
          $union2 = "";
        }
        $details = DB::SELECT("SELECT
          GROUP_CONCAT( a.not_yet ) AS not_yet,
          GROUP_CONCAT( a.done ) AS done 
        FROM
          (
          SELECT
            GROUP_CONCAT( DISTINCT ( CONCAT( point_check_type, ' ', point_check_machine )) ) AS not_yet,
            '' AS done 
          FROM
            injection_cleaning_points 
            WHERE
          injection_cleaning_points.point_check_schedule = 'daily'
          ".$union."
          ".$union2." UNION ALL
          SELECT
            '' AS not_yet,
            GROUP_CONCAT( DISTINCT ( CONCAT( injection_cleanings.point_check_type, ' ', injection_cleanings.point_check_machine )) ) AS done 
          FROM
            injection_cleanings
            JOIN injection_cleaning_points ON CONCAT( injection_cleaning_points.point_check_type, injection_cleaning_points.point_check_machine, injection_cleaning_points.point_check_index ) = CONCAT( injection_cleanings.point_check_type, injection_cleanings.point_check_machine, injection_cleanings.point_check_index ) 
          WHERE
            injection_cleaning_points.point_check_schedule = 'daily' 
          AND DATE( injection_cleanings.created_at ) = '".$yesterdays."' 
          ) a");

        $detail = [];
        $not_yet = explode(',', $details[0]->not_yet);
        $done = explode(',', $details[0]->done);
        for ($i=0; $i < count($not_yet); $i++) { 
          if (in_array($not_yet[$i], $done)) {
            
          }else{
            array_push($detail, $not_yet[$i]);
          }
        }

        if (count($detail) > 0) {
          $contactList = [];
          $contactList[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
          $mail_to = [];
          $mail_to[0] = 'eko.prasetyo.wicaksono@music.yamaha.com';
          $data = [
              'detail' => $detail,
              'date' => $yesterdays,
          ];
          Mail::to($mail_to)->bcc($contactList,'Contact List')->send(new SendEmail($data, 'injection_cleaning'));
        }
        for ($i=1; $i <= 6; $i++) { 
          $tray = RcNgBox::create([
            'date' => date('Y-m-d'),
            'tray' => $i,
            'ng_head' => 0,
            'ng_middle' => 0,
            'ng_foot' => 0,
            'ng_block' => 0,
            'created_by' => 1,
          ]);
        }
    }
}
