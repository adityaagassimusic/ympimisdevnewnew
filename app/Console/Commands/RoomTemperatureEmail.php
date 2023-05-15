<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use DateTime;

class RoomTemperatureEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:room_temperature';

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
        $datenow = date('Y-m-d');
        // $now = "2022-08-22 11:00:00";
        $now = date('Y-m-d H:i:s');

        $logs = DB::SELECT('
               SELECT DISTINCT
                    location,
                    remark,
                    upper_limit,
                    lower_limit,
                    `value`,
                    created_at 
               FROM
                    temperature_room_logs 
               WHERE created_at is not null
               and deleted_at is null
               and DATE(created_at) = "'.$datenow.'"
               and (remark = "temperature" or remark = "humidity")
               ORDER BY
                    created_at asc'
        );


        $array_temp = [];
        $array_hum = [];
        // dd($logs);

        foreach($logs as $temp_logs){

            $temp_status = 0;
            $hum_status = 0;
            $temp_message = "";
            $hum_message = "";

            $start_date = new DateTime($temp_logs->created_at);
            $since_start = $start_date->diff(new DateTime($now));

            
            if ($since_start->h < 4) {
                if ($temp_logs->upper_limit != null && $temp_logs->lower_limit != null) {
                    if ($temp_logs->value > $temp_logs->upper_limit || $temp_logs->value < $temp_logs->lower_limit ) {
                        if($temp_logs->remark == "temperature"){
                            $temp_status = 1;
                        }

                        else if($temp_logs->remark == "humidity"){
                            $hum_status = 1;
                        }
                    }
                }

                // else if ($temp_logs->upper_limit != null) {
                //     if ($temp_logs->value > $temp_logs->upper_limit){
                //         if($temp_logs->remark == "temperature"){
                //              $temp_status = 1;
                //         } 
                //         else if($temp_logs->remark == "humidity"){
                //              $hum_status = 1;
                //         }
                //     }
                // }
                // else if ($temp_logs->lower_limit != null) {
                //     if ($temp_logs->value < $temp_logs->lower_limit){
                //         if($temp_logs->remark == "temperature"){
                //             $temp_status = 1;
                //         } 

                //         else if($temp_logs->remark == "humidity"){
                //             $hum_status = 1;
                //         }
                //     }
                // }

                if ($temp_status == 1) {
                    $temp_message = "Temperature Lokasi ".$temp_logs->location." Menunjukkan ada ketidaksesuaian. Tolong segera dilakukan pengecekan.\n";

                    array_push($array_temp, $temp_message);
                }

                if ($hum_status == 1) {
                    $hum_message = "Humidity Lokasi ".$temp_logs->location." Menunjukkan ada ketidaksesuaian. Tolong segera dilakukan pengecekan.\n";

                    array_push($array_hum, $hum_message);
                }
            }
        }


        if (count($array_temp) > 0) {
            $temp = array_unique($array_temp);
            $message_temp = urlencode(join($temp));

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
              CURLOPT_POSTFIELDS => 'receiver=62811375232&device=6281130561777&message='.$message_temp.'&type=chat',
              CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
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
              CURLOPT_POSTFIELDS => 'receiver=6281132210008&device=6281130561777&message='.$message_temp.'&type=chat',
              CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
              ),
            ));
            curl_exec($curl);
        }

        if (count($array_hum) > 0) {
            $hum = array_unique($array_hum);
            $message_hum = urlencode(join($hum));

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
              CURLOPT_POSTFIELDS => 'receiver=62811375232&device=6281130561777&message='.$message_hum.'&type=chat',
              CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
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
              CURLOPT_POSTFIELDS => 'receiver=6281132210008&device=6281130561777&message='.$message_hum.'&type=chat',
              CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
              ),
            ));
            curl_exec($curl);
        }

        // $temp = array_unique($array_temp);
        // $hum = array_unique($array_hum);
        // dd($temp);

        // $to = [
        //     'rio.irvansyah@music.yamaha.com',
        //     'nasiqul.ibat@music.yamaha.com',
        // ];


        // Mail::to($to)->send(new SendEmail($audits, 'patrol_reminder'));
    }
}
