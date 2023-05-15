<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReminderLoadingLimbah extends Command
{
    protected $signature = 'reminder:loading_limbah';
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $date_now = date('Y-m-d');
        $date_message = date('d-m-Y');
        $cek_loading = db::connection('ympimis_2')->table('waste_logs')->whereNotNull('date_disposal')->where('date_disposal', $date_now)->first();
        
        if (count($cek_loading) > 0) {
            $messages = "*Reminder Loading Limbah WWT !!!*%0A%0A";
            $messages .= "Informasi : %0A";
            $messages .= "Akan ada Loading Limbah B3 ke ".$cek_loading->vendor." pada tanggal ".$date_message.".%0A"; 
            $messages .= "Mohon tim Security untuk mengatur area depan TPS B3 agar tidak dipakai parkir karyawan.%0A%0A";
            $messages .= "*_- Maintenance Department -_*";
            $curl = curl_init();
            $phones = [
                '6287865101302',
                '6281219738502',
                '628123546859',
                '6281357479161',
                '6282234744119',
                '6282233263204',
                '6289681341551',
                '628648069425',
                '6282330709561',
                '6281343217090',
                '6282230786039',
                '6285806464663',
                '6285106865000',
                '62895363771277',
                '6282143475404',
                '6281382766623',
                '6282333942965',
                '6282216552740',
                '6281331844023',
                '62895364857531'
            ];

            for ($i = 0; $i < count($phones); $i++) {
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
                   CURLOPT_POSTFIELDS => 'receiver='.$phones[$i].'&device=6281130561777&message='.$messages.'&type=chat',
                   CURLOPT_HTTPHEADER => array(
                       'Accept: application/json',
                       'Content-Type: application/x-www-form-urlencoded',
                       'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
                   ),

               ));

                curl_exec($curl);
            }

            // $phones_file = [
            //     '6287865101302'
            // ];

            // $filepath = "https://ympi.co.id/ympicoid/public/files/announcement/Pengumuman_Kasus_COC.pdf";

            // for ($q = 0; $q < count($phones_file); $q++) {
            //     $curl = curl_init();

            //     curl_setopt_array($curl, array(
            //       CURLOPT_URL => 'https://api.whatspie.com/messages',
            //       CURLOPT_RETURNTRANSFER => true,
            //       CURLOPT_ENCODING => '',
            //       CURLOPT_MAXREDIRS => 10,
            //       CURLOPT_TIMEOUT => 0,
            //       CURLOPT_FOLLOWLOCATION => true,
            //       CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //       CURLOPT_CUSTOMREQUEST => 'POST',
            //       CURLOPT_POSTFIELDS =>'{
            //         "device": "6281130561777",
            //         "receiver": "'.$phones_file[$q].'",
            //         "type": "file",
            //         "message": "document.pdf",
            //         "file_url": "'.$filepath.'",
            //         "simulate_typing": 1
            //     }',
            //     CURLOPT_HTTPHEADER => array(
            //         'Accept: application/json',
            //         'Content-Type: application/json',
            //         'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
            //     ),
            // ));

            //     curl_exec($curl);

            // }
        }
    }
}