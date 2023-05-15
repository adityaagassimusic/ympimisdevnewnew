<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

use App\EmployeeSync;
use App\WeeklyCalendar;
use App\CodeGenerator;
use App\BentoQuota;
use App\GeneralAttendance;

class BirthdayEmployee extends Command
{
    protected $signature = 'reminder:birthday_employee';
    protected $description = 'Command description';
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {   
        $date = date('Y-m-d');
        
        $date_ambil = date('d-m-Y', strtotime($date));
        $datas = db::connection('ympimis_2')->select('select DATE_FORMAT(periode, "%d-%m-%Y") as periode, employee_id, name,date, phone from employee_birthdays where periode = "'.$date.'" and remark is null');
        $data_emps = [];


        if (count($datas) > 0) {
            for ($i=0; $i < count($datas); $i++) { 

             $emps = EmployeeSync::where('employee_id', '=', $datas[$i]->employee_id)->first();

             array_push($data_emps,
                [
                    "employee_id" => $datas[$i]->employee_id,
                    "name" => $datas[$i]->name,
                    "section" => $emps->section,
                    "date" => $datas[$i]->date,
                    "phone" => $datas[$i]->phone,

                ]);

                   // wa
             $phone = substr($datas[$i]->phone, 1, 15);
             $phone = '62' . $phone;

             $message = 'Selamat%20Ulang%20Tahun%0A%0A*'.$datas[$i]->name.'*%0A%0ATerima%20kasih%20atas%20kerjasamanya,%20semoga%20sukses%20dan%20sehat%20selalu%0A%0AAnda%20berhak%20atas%20makan%20siang%20*Bento*%20dan%20*Special%20Gift*%20dari%20YMPI%20bisa%20diambil%20tanggal%20'.$date_ambil.'%20di%20area%20live%20cooking.%0A%0A-YMPI-';

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
                 CURLOPT_POSTFIELDS => 'receiver='.$phone.'&device=6281130561777&message='.$message.'&type=chat',
                 CURLOPT_HTTPHEADER => array(
                     'Accept: application/json',
                     'Content-Type: application/x-www-form-urlencoded',
                     'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
                 )
             ));

             curl_exec($curl);

             $employee_syncs = EmployeeSync::where('employee_id', $datas[$i]->employee_id)->first();
             $employee_data_bentos = GeneralAttendance::where('employee_id', $datas[$i]->employee_id)->where('purpose_code', 'Bento')->where('due_date', $date)->first();

                if ($employee_data_bentos == null) {

                    db::table('general_attendances')->insert([
                        'purpose_code' => 'Bento',
                        'due_date' => $date,
                        'employee_id' => $employee_syncs->employee_id,
                        'created_by' => '2819',
                        'remark' => 'birthday',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }

                
                //update emp 
                $emp_update = db::connection('ympimis_2')->table('employee_birthdays')
                ->where('employee_id', '=', $datas[$i]->employee_id)
                ->where('remark', '=', null)
                ->update([
                    'remark' => "1",
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

         }            


            // send email notif
         $data = array(
            'datas' => $data_emps
        );

         $mail_to = [
            'rianita.widiastuti@music.yamaha.com',
            'putri.sukma.riyanti@music.yamaha.com'
        ];

        Mail::to($mail_to)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($data, 'reminder_employee_birthday'));
    }
}
}
