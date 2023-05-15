<?php

namespace App\Console\Commands;

use App\EmployeeSync;
use App\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\SendEmail;

class SyncEmpYmpicoid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:ympicoid';

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
        $controller = app()->make('App\Http\Controllers\MiraiMobileController');
        $token = app()->call([$controller, 'ympicoid_api_login'], []);
        // $token = app()->call([$controller, 'ympicoid_api_trial'], []);

        $param = '';
        $link = 'delete/employees';
        $method = 'GET';

        $controller = app()->make('App\Http\Controllers\MiraiMobileController');
        $delete_emp = app()->call([$controller, 'ympicoid_api_json'], [
            'token' => $token,
            'link' => $link,
            'method' => $method,
            'param' => $param,
        ]);

        $emp = EmployeeSync::where('employee_id', 'like', '%PI%')->get();

        $insert = array();
        $insert_all = [];

        for ($i = 0; $i < count($emp); $i++) {
            $row = array();

            if ($emp[$i]->employee_id == null) {
                $row['employee_id'] = '';
            } else {
                $row['employee_id'] = $emp[$i]->employee_id;
            }
            if ($emp[$i]->name == null) {
                $row['name'] = '';
            } else {
                $row['name'] = $emp[$i]->name;
            }
            if ($emp[$i]->gender == null) {
                $row['gender'] = '';
            } else {
                $row['gender'] = $emp[$i]->gender;
            }
            if ($emp[$i]->birth_place == null) {
                $row['birth_place'] = '';
            } else {
                $row['birth_place'] = $emp[$i]->birth_place;
            }
            if ($emp[$i]->birth_date == null) {
                $row['birth_date'] = '';
            } else {
                $row['birth_date'] = $emp[$i]->birth_date;
            }
            if ($emp[$i]->address == null) {
                $row['address'] = '';
            } else {
                $row['address'] = $emp[$i]->address;
            }
            if ($emp[$i]->phone == null) {
                $row['phone'] = '';
            } else {
                $row['phone'] = $emp[$i]->phone;
            }
            if ($emp[$i]->card_id == null) {
                $row['card_id'] = '';
            } else {
                $row['card_id'] = $emp[$i]->card_id;
            }
            if ($emp[$i]->npwp == null) {
                $row['npwp'] = '';
            } else {
                $row['npwp'] = $emp[$i]->npwp;
            }
            if ($emp[$i]->JP == null) {
                $row['JP'] = '';
            } else {
                $row['JP'] = $emp[$i]->JP;
            }
            if ($emp[$i]->BPJS == null) {
                $row['BPJS'] = '';
            } else {
                $row['BPJS'] = $emp[$i]->BPJS;
            }
            if ($emp[$i]->hire_date == null) {
                $row['hire_date'] = '';
            } else {
                $row['hire_date'] = $emp[$i]->hire_date;
            }
            if ($emp[$i]->end_date == null) {
                $row['end_date'] = '';
            } else {
                $row['end_date'] = $emp[$i]->end_date;
            }
            if ($emp[$i]->position == null) {
                $row['position'] = '';
            } else {
                $row['position'] = $emp[$i]->position;
            }
            if ($emp[$i]->position_new == null) {
                $row['position_new'] = '';
            } else {
                $row['position_new'] = $emp[$i]->position_new;
            }
            if ($emp[$i]->position_code == null) {
                $row['position_code'] = '';
            } else {
                $row['position_code'] = $emp[$i]->position_code;
            }
            if ($emp[$i]->grade_code == null) {
                $row['grade_code'] = '';
            } else {
                $row['grade_code'] = $emp[$i]->grade_code;
            }
            if ($emp[$i]->grade_name == null) {
                $row['grade_name'] = '';
            } else {
                $row['grade_name'] = $emp[$i]->grade_name;
            }
            if ($emp[$i]->division == null) {
                $row['division'] = '';
            } else {
                $row['division'] = $emp[$i]->division;
            }
            if ($emp[$i]->department == null) {
                $row['department'] = '';
            } else {
                $row['department'] = $emp[$i]->department;
            }
            if ($emp[$i]->section == null) {
                $row['section'] = '';
            } else {
                $row['section'] = $emp[$i]->section;
            }
            if ($emp[$i]->group == null) {
                $row['group'] = '';
            } else {
                $row['group'] = $emp[$i]->group;
            }
            if ($emp[$i]->sub_group == null) {
                $row['sub_group'] = '';
            } else {
                $row['sub_group'] = $emp[$i]->sub_group;
            }
            if ($emp[$i]->employment_status == null) {
                $row['employment_status'] = '';
            } else {
                $row['employment_status'] = $emp[$i]->employment_status;
            }
            if ($emp[$i]->cost_center == null) {
                $row['cost_center'] = '';
            } else {
                $row['cost_center'] = $emp[$i]->cost_center;
            }
            if ($emp[$i]->assignment == null) {
                $row['assignment'] = '';
            } else {
                $row['assignment'] = $emp[$i]->assignment;
            }
            if ($emp[$i]->union == null) {
                $row['union'] = '';
            } else {
                $row['union'] = $emp[$i]->union;
            }
            if ($emp[$i]->nik_manager == null) {
                $row['nik_manager'] = '';
            } else {
                $row['nik_manager'] = $emp[$i]->nik_manager;
            }
            if ($emp[$i]->zona == null) {
                $row['zona'] = '';
            } else {
                $row['zona'] = $emp[$i]->zona;
            }
            if ($emp[$i]->job_status == null) {
                $row['job_status'] = '';
            } else {
                $row['job_status'] = $emp[$i]->job_status;
            }
            $row['created_at'] = date('Y-m-d H:i:s');
            $row['updated_at'] = date('Y-m-d H:i:s');

            $insert[] = $row;

            if (($i % 100 == 0 || $i == (count($emp) - 1)) && $i != 0) {
                array_push($insert_all, $insert);
                $insert = [];
            }
        }

        for ($i = 0; $i < count($insert_all); $i++) {
            $link = 'insert/employees';
            $method = 'POST';
            $param = json_encode($insert_all[$i]);

            $controller = app()->make('App\Http\Controllers\MiraiMobileController');
            $insert_emp = app()->call([$controller, 'ympicoid_api_json'], [
                'token' => $token,
                'link' => $link,
                'method' => $method,
                'param' => $param,
            ]);
        }

        $message = urlencode("YMPICOID Synced at " . date("Y-m-d H:i:s"));

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
            CURLOPT_POSTFIELDS => 'receiver=6282334197238&device=6281130561777&message=' . $message . '&type=chat',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
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
            CURLOPT_POSTFIELDS => 'receiver=6285645896741&device=6281130561777&message=' . $message . '&type=chat',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
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
            CURLOPT_POSTFIELDS => 'receiver=628980198771&device=6281130561777&message=' . $message . '&type=chat',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
            ),
        ));
        curl_exec($curl);

        $get_data = DB::SELECT("SELECT
            REPLACE(CONCAT(SPLIT_STRING(employee_syncs.`name`, ' ', 1),' ',SPLIT_STRING(employee_syncs.`name`, ' ', 2)),'\'','') AS operator_name,
            CONV( COALESCE ( tag, '0123456789' ), 10, 16 ) AS operator_code,
            0 AS department_id,
            0 AS ws_id,
            employee_syncs.employee_id AS operator_nik,
            groups.`group`,
            sub_group,
            IF(sub_group like '%AS%' OR sub_group like '%TS%','sx',IF(sub_group like '%CL%','cl',IF(sub_group like '%FL%','fl',IF(sub_group like '%Kensa%','kensa','kensa')))) as location,
            DATE_FORMAT( NOW(), '%Y-%m-%d %H:%m:%s' ) AS operator_create_date,
            1 AS created_by 
        FROM
            employee_syncs
            JOIN employees ON employees.employee_id = employee_syncs.employee_id
            LEFT JOIN (
            SELECT
                employee_id,
            IF
                ( shiftdaily_code LIKE '%Shift_1%', 'A', IF(shiftdaily_code LIKE '%Shift_2%','B','C') ) AS `group` 
            FROM
                sunfish_shift_syncs 
            WHERE
                shift_date = DATE(
                NOW())) AS groups ON groups.employee_id = employee_syncs.employee_id 
        WHERE
            department LIKE '%Welding%' 
            and section = 'Koshuha Solder Process Section'
            AND employee_syncs.end_date IS NULL");

        if (count($get_data) > 0) {
            DB::connection('ympimis_2')->table('welding_operators')->where('remark',null)->delete();

            for ($i=0; $i < count($get_data); $i++) { 
                $insert_employee = DB::connection('ympimis_2')->table('welding_operators')->insert([
                    'name' => strtoupper($get_data[$i]->operator_name),
                    'tag' => strtoupper($get_data[$i]->operator_code),
                    'employee_id' => strtoupper($get_data[$i]->operator_nik),
                    'shift' => strtoupper($get_data[$i]->group),
                    'location' => $get_data[$i]->location,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_by' => 1,
                ]);
            }
        }

        $yesterday = date('Y-m-d', strtotime(Carbon::yesterday()));

        $welding_queue = DB::connection('ympimis_2')->table('welding_queues')->where(DB::RAW('DATE(created_at)'),'<=',$yesterday)->get();
        if (count($welding_queue) > 0) {
            $delete = DB::connection('ympimis_2')->table('welding_queues')->where(DB::RAW('DATE(created_at)'),'<=',$yesterday)->delete();
        }

        $message = urlencode("SOLDER Synced at " . date("Y-m-d H:i:s"));

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
            CURLOPT_POSTFIELDS => 'receiver=6282334197238&device=6281130561777&message=' . $message . '&type=chat',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
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
            CURLOPT_POSTFIELDS => 'receiver=6285645896741&device=6281130561777&message=' . $message . '&type=chat',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
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
            CURLOPT_POSTFIELDS => 'receiver=628980198771&device=6281130561777&message=' . $message . '&type=chat',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
            ),
        ));
        curl_exec($curl);

        $controller = app()->make('App\Http\Controllers\MiraiMobileController');
        $token = app()->call([$controller, 'ympicoid_api_login'], []);
        $param = '';
        $link = 'fetch/vehicle/validity';
        $method = 'GET';
        $controller = app()->make('App\Http\Controllers\MiraiMobileController');
        $get_all_data = app()->call([$controller, 'ympicoid_api_json'], [
            'token' => $token,
            'link' => $link,
            'method' => $method,
            'param' => $param,
        ]);


        $mail_to = [];
        array_push($mail_to, 'vidiya.chalista@music.yamaha.com');
        array_push($mail_to, 'rani.nurdiyana.sari@music.yamaha.com');
        array_push($mail_to, 'syafrizal.carnov.purwanto@music.yamaha.com');
        array_push($mail_to, 'widura@music.yamaha.com');

        $bcc = [];
        array_push($bcc, 'ympi-mis-ML@music.yamaha.com');

        $data = array(
            'get_all_data' => $get_all_data,
            'title' => 'Reminder Expired SIM & STNK',
        );

        if (count($get_all_data) > 0) {
            Mail::to($mail_to)
            ->bcc($bcc)
            ->send(new SendEmail($data, 'reminder_kendaraan_expired'));
        }

        

        for ($i=0; $i < count($get_all_data); $i++) { 



            $emp = EmployeeSync::where('employee_id',$get_all_data[$i]->employee_id)->first();

            if(substr($emp->phone, 0, 1) == '+' ){
                $phone = substr($emp->phone, 1, 15);
            }
            else if(substr($emp->phone, 0, 1) == '0'){
                $phone = "62".substr($emp->phone, 1, 15);
            }
            else{
                $phone = $emp->phone;
            }
            if ($get_all_data[$i]->validity_sim == 30) {
                $message = '';
                $message .= "PEMBERITAHUAN !!!\n\n";
                $message .= "SIM Anda (".$emp->name.") akan berakhir dalam 30 Hari.\n";
                $message .= "Silakan melakukan pembaruan SIM Anda.\n\n";
                $message .= "-YMPI MIS Dept.-";

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
                    CURLOPT_POSTFIELDS => 'receiver=6285645896741&device=6281130561777&message=' . urlencode($message) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
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
                    CURLOPT_POSTFIELDS => 'receiver=6282334197238&device=6281130561777&message=' . urlencode($message) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
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
                    CURLOPT_POSTFIELDS => 'receiver='.$phone.'&device=6281130561777&message=' . urlencode($message) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);
            }

            if ($get_all_data[$i]->validity_stnk == 30) {
                $message = '';
                $message .= "PEMBERITAHUAN !!!\n\n";
                $message .= "STNK dengan No. Pol. ".$get_all_data[$i]->nopol."  (".$emp->name.") akan berakhir dalam 30 Hari.\n";
                $message .= "Silakan melakukan pembaruan STNK Anda.\n\n";
                $message .= "-YMPI MIS Dept.-";

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
                    CURLOPT_POSTFIELDS => 'receiver=6285645896741&device=6281130561777&message=' . urlencode($message) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
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
                    CURLOPT_POSTFIELDS => 'receiver=6282334197238&device=6281130561777&message=' . urlencode($message) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
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
                    CURLOPT_POSTFIELDS => 'receiver='.$phone.'&device=6281130561777&message=' . urlencode($message) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);
            }

            if ($get_all_data[$i]->validity_stnk_2 != null && $get_all_data[$i]->validity_stnk_2 == 30) {
                $message = '';
                $message .= "PEMBERITAHUAN !!!\n\n";
                $message .= "STNK dengan No. Pol. ".$get_all_data[$i]->nopol_2."  (".$emp->name.") akan berakhir dalam 30 Hari.\n";
                $message .= "Silakan melakukan pembaruan STNK Anda.\n\n";
                $message .= "-YMPI MIS Dept.-";

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
                    CURLOPT_POSTFIELDS => 'receiver=6285645896741&device=6281130561777&message=' . urlencode($message) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
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
                    CURLOPT_POSTFIELDS => 'receiver=6282334197238&device=6281130561777&message=' . urlencode($message) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
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
                    CURLOPT_POSTFIELDS => 'receiver='.$phone.'&device=6281130561777&message=' . urlencode($message) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                ));
                curl_exec($curl);
            }
        }
    }
}
