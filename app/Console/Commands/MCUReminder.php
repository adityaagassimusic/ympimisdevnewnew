<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\EmployeeSync;
use App\Department;
use App\Approver;

class MCUReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mcu:reminder';

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
        // $reminder_whatsapp = DB::connection('ympimis_2')->SELECT("SELECT
        //             * 
        //         FROM
        //             `mcus` 
        //         WHERE
        //             schedule_date_mcu = CURDATE() + INTERVAL 1 DAY 
        //             AND mcu_group_code != 'Tidak Perlu'");

        // $location = DB::connection('ympimis_2')->table('mcu_locations')->get();

        // $empsync = EmployeeSync::select('employee_syncs.*','departments.department_shortname')->where('end_date',null)->leftjoin('departments','departments.department_name','employee_syncs.department')->get();

        // $darah = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        // $thorax = ['A', 'B', 'C', 'D', 'E', 'F'];
        // $ecg = ['A', 'B', 'F', 'G'];
        // $audio = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

        // if (count($reminder_whatsapp) > 0) {
        //     for ($i=0; $i < count($reminder_whatsapp); $i++) { 
        //         for ($j=0; $j < count($empsync); $j++) { 
        //             if ($empsync[$j]->employee_id == $reminder_whatsapp[$i]->employee_id) {

        //                 $due_date_replace = date('d-M-Y',strtotime($reminder_whatsapp[$i]->schedule_date_mcu));

        //                 if(substr($empsync[$j]->phone, 0, 1) == '+' ){
        //                  $phone = substr($empsync[$j]->phone, 1, 15);
        //                 }
        //                 else if(substr($empsync[$j]->phone, 0, 1) == '0'){
        //                  $phone = "62".substr($empsync[$j]->phone, 1, 15);
        //                 }
        //                 else{
        //                  $phone = $empsync[$j]->phone;
        //                 }

        //                 $puasa = '';

        //                 $locs = '';
        //                 for ($k=0; $k < count($location); $k++) { 
        //                   if ($location[$k]->loc_code == $reminder_whatsapp[$i]->loc_code_mcu) {
        //                     $locs = $location[$k]->loc_name;
        //                   }
        //                 }

        //                 $process = [];
        //                 if (in_array($reminder_whatsapp[$i]->mcu_group_code,$darah)) {
        //                   array_push($process, '- Ambil Darah / Urine di Lokasi '.$locs);
        //                 }

        //                 if (in_array($reminder_whatsapp[$i]->mcu_group_code,$thorax)) {
        //                   array_push($process, '- Thorax');
        //                 }

        //                 if (in_array($reminder_whatsapp[$i]->mcu_group_code,$ecg)) {
        //                   array_push($process, '- ECG');
        //                 }

        //                 if ($reminder_whatsapp[$i]->audiometri == 'YA') {
        //                   array_push($process, '- Audiometri');
        //                 }

        //                 if ($reminder_whatsapp[$i]->fasting == 'PUASA') {
        //                     $puasa = "Anda diwajibkan PUASA.\n\n";
        //                 }
        //                 $messages = "";
        //                 $messages .= "PEMBERITAHUAN !!! \n\n";
        //                 $messages .= "Anda telah terjadwal Medical Check Up pada tanggal ".$due_date_replace.".\n\n";
        //                 $messages .= "Proses MCU Anda : \n";
        //                 $messages .= join("\n",$process)."\n\n";
        //                 $messages .= $puasa;
        //                 $messages .= "Mohon datang sesuai schedule.\n\n";
        //                 $messages .= "-YMPI MIS Dept.-";


        //                 $curl = curl_init();

        //                 curl_setopt_array($curl, array(
        //                   CURLOPT_URL => 'https://app.whatspie.com/api/messages',
        // CURLOPT_SSL_VERIFYHOST => FALSE,
        //         CURLOPT_SSL_VERIFYPEER => FALSE,
        //                   CURLOPT_RETURNTRANSFER => true,
        //                   CURLOPT_ENCODING => '',
        //                   CURLOPT_MAXREDIRS => 10,
        //                   CURLOPT_TIMEOUT => 0,
        //                   CURLOPT_FOLLOWLOCATION => true,
        //                   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //                   CURLOPT_CUSTOMREQUEST => 'POST',
        //                   CURLOPT_POSTFIELDS => 'receiver='.$phone.'&device=6281130561777&message='.urlencode($messages).'&type=chat',
        //                   CURLOPT_HTTPHEADER => array(
        //                     'Accept: application/json',
        //                     'Content-Type: application/x-www-form-urlencoded',
        //                     'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
        //                   ),
        //                 ));

        //                 curl_exec($curl);
        //             }
        //         }
        //     }
        // }

        // $reminder_whatsapp = DB::connection('ympimis_2')->SELECT("SELECT
        //             * 
        //         FROM
        //             `mcus` 
        //         WHERE
        //             mcu_group_code = 'A'
        //             and periode = 'FY199'");

        // $empsync = EmployeeSync::select('employee_syncs.*','departments.department_shortname')->where('end_date',null)->leftjoin('departments','departments.department_name','employee_syncs.department')->get();

        // if (count($reminder_whatsapp) > 0) {
        //     for ($i=0; $i < count($reminder_whatsapp); $i++) { 
        //         for ($j=0; $j < count($empsync); $j++) { 
        //             if ($empsync[$j]->employee_id == $reminder_whatsapp[$i]->employee_id) {
        //               $due_date_replace = date('d-M-Y',strtotime('2023-02-17'));
        //               $messages = "";
        //               $messages .= "PEMBERITAHUAN !!! \n\n";
        //               $messages .= "Anda telah terjadwal Medical Check Up AUDIOMETRI pada tanggal ".$due_date_replace.".\n\n";
        //               $messages .= "Mohon datang sesuai schedule.\n\n";
        //               $messages .= "-YMPI MIS Dept.-";

        //               if(substr($empsync[$j]->phone, 0, 1) == '+' ){
        //                  $phone = substr($empsync[$j]->phone, 1, 15);
        //                 }
        //                 else if(substr($empsync[$j]->phone, 0, 1) == '0'){
        //                  $phone = "62".substr($empsync[$j]->phone, 1, 15);
        //                 }
        //                 else{
        //                  $phone = $empsync[$j]->phone;
        //                 }

        //               $curl = curl_init();

        //               curl_setopt_array($curl, array(
        //                 CURLOPT_URL => 'https://app.whatspie.com/api/messages',
        // CURLOPT_SSL_VERIFYHOST => FALSE,
        //         CURLOPT_SSL_VERIFYPEER => FALSE,
        //                 CURLOPT_RETURNTRANSFER => true,
        //                 CURLOPT_ENCODING => '',
        //                 CURLOPT_MAXREDIRS => 10,
        //                 CURLOPT_TIMEOUT => 0,
        //                 CURLOPT_FOLLOWLOCATION => true,
        //                 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //                 CURLOPT_CUSTOMREQUEST => 'POST',
        //                 CURLOPT_POSTFIELDS => 'receiver='.$phone.'&device=6281130561777&message='.urlencode($messages).'&type=chat',
        //                 CURLOPT_HTTPHEADER => array(
        //                   'Accept: application/json',
        //                   'Content-Type: application/x-www-form-urlencoded',
        //                   'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
        //                 ),
        //               ));

        //               curl_exec($curl);
        //             }
        //         }
        //     }
        // }

        // $reminder_whatsapp = DB::connection('ympimis_2')->SELECT("SELECT
        //             * 
        //         FROM
        //             `mcus` 
        //         WHERE
        //             mcu_group_code = 'A'
        //             and periode = 'FY199'");

        // $empsync = EmployeeSync::select('employee_syncs.*','departments.department_shortname')->where('end_date',null)->leftjoin('departments','departments.department_name','employee_syncs.department')->get();

        // if (count($reminder_whatsapp) > 0) {
        //     for ($i=0; $i < count($reminder_whatsapp); $i++) { 
        //         for ($j=0; $j < count($empsync); $j++) { 
        //             if ($empsync[$j]->employee_id == $reminder_whatsapp[$i]->employee_id && $reminder_whatsapp[$i]->plus != null) {
        //               $due_date_replace = date('d-M-Y',strtotime('2023-03-01'));
        //               $messages = "";
        //               $messages .= "PEMBERITAHUAN !!! \n\n";
        //               $messages .= "Anda telah terjadwal Medical Check Up ".$reminder_whatsapp[$i]->plus." pada tanggal ".$due_date_replace.".\n\n";
        //               $messages .= "Lokasi di Klinik YMPI.\n\n";
        //               $messages .= "Mohon datang sesuai schedule.\n\n";
        //               $messages .= "-YMPI MIS Dept.-";

        //               if(substr($empsync[$j]->phone, 0, 1) == '+' ){
        //                  $phone = substr($empsync[$j]->phone, 1, 15);
        //                 }
        //                 else if(substr($empsync[$j]->phone, 0, 1) == '0'){
        //                  $phone = "62".substr($empsync[$j]->phone, 1, 15);
        //                 }
        //                 else{
        //                  $phone = $empsync[$j]->phone;
        //                 }

        //               $curl = curl_init();

        //               curl_setopt_array($curl, array(
        //                 CURLOPT_URL => 'https://app.whatspie.com/api/messages',
        // CURLOPT_SSL_VERIFYHOST => FALSE,
        //         CURLOPT_SSL_VERIFYPEER => FALSE,
        //                 CURLOPT_RETURNTRANSFER => true,
        //                 CURLOPT_ENCODING => '',
        //                 CURLOPT_MAXREDIRS => 10,
        //                 CURLOPT_TIMEOUT => 0,
        //                 CURLOPT_FOLLOWLOCATION => true,
        //                 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //                 CURLOPT_CUSTOMREQUEST => 'POST',
        //                 CURLOPT_POSTFIELDS => 'receiver='.$phone.'&device=6281130561777&message='.urlencode($messages).'&type=chat',
        //                 CURLOPT_HTTPHEADER => array(
        //                   'Accept: application/json',
        //                   'Content-Type: application/x-www-form-urlencoded',
        //                   'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
        //                 ),
        //               ));

        //               curl_exec($curl);
        //             }
        //         }
        //     }
        // }

        // $konsul = [
        // ['6282230302540','09:00'],
        // ['6289681065891','09:10'],
        // ['6282142017553','09:20'],
        // ['6285723930581','09:30'],
        // ['6281333778625','09:40'],
        // ['628125270845','09:50'],
        // ['6285755485504','10.00'],
        // ['6289612576756','10:10'],
        // ['6285755425149','10:20'],
        // ['6281332459652','10:30'],
        // ];

        // for ($i=0; $i < count($konsul); $i++) { 
        //     $messages = "";
        //     $messages .= "PEMBERITAHUAN !!! \n\n";
        //     $messages .= "Anda telah terjadwal Konsultasi dengan Dokter Spesialis pada Tanggal 10 Mei 2023 Jam ".$konsul[$i][1].".\n\n";
        //     $messages .= "Lokasi di Klinik YMPI.\n\n";
        //     $messages .= "Mohon datang sesuai schedule.\n\n";
        //     $messages .= "-YMPI MIS Dept.-";

        //     $curl = curl_init();

        //     curl_setopt_array($curl, array(
        //         CURLOPT_URL => 'https://app.whatspie.com/api/messages',
        //         CURLOPT_SSL_VERIFYHOST => FALSE,
        //         CURLOPT_SSL_VERIFYPEER => FALSE,
        //         CURLOPT_RETURNTRANSFER => true,
        //         CURLOPT_ENCODING => '',
        //         CURLOPT_MAXREDIRS => 10,
        //         CURLOPT_TIMEOUT => 0,
        //         CURLOPT_FOLLOWLOCATION => true,
        //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //         CURLOPT_CUSTOMREQUEST => 'POST',
        //         CURLOPT_POSTFIELDS => 'receiver='.$konsul[$i][0].'&device=6281130561777&message='.urlencode($messages).'&type=chat',
        //         CURLOPT_HTTPHEADER => array(
        //           'Accept: application/json',
        //           'Content-Type: application/x-www-form-urlencoded',
        //           'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
        //         ),
        //     ));

        //     curl_exec($curl);

        // }


    }
}
