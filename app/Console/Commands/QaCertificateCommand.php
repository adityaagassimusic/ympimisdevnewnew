<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\EmployeeSync;
use App\Approver;
use App\User;

class QaCertificateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qa:certificate';

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
        $certificate_code = DB::connection('ympimis_2')
            ->select('SELECT
                    *,
                    (
                    SELECT DISTINCT
                        ( periode_to ) 
                    FROM
                        qa_certificates 
                    WHERE
                        qa_certificates.certificate_id = qa_certificate_codes.certificate_id UNION ALL
                    SELECT DISTINCT
                        ( periode_to ) 
                    FROM
                        qa_certificate_inprocesses 
                    WHERE
                        qa_certificate_inprocesses.certificate_id = qa_certificate_codes.certificate_id 
                    ) AS periode_to 
                FROM
                    `qa_certificate_codes` 
                WHERE
                    qa_certificate_codes.`status` = 1');

        $renewal = [];

        for ($i=0; $i < count($certificate_code); $i++) { 
            $day_renewal = date('Y-m-d',strtotime($certificate_code[$i]->periode_to.'- 45 DAYS'));
            $now = date('Y-m-d');
            $day_next = date('Y-m-d',strtotime($now.'+ 45 DAYS'));
            if ($now == $day_renewal) {
                array_push($renewal, $certificate_code[$i]->certificate_id);
            }
        }


        $empsync = EmployeeSync::where('end_date',null)->get();

        if (count($renewal) > 0) {
            $renewalnew = join(',',$renewal);

            $renewalin = '';
            if($renewalnew != null){
              $renewals =  explode(",", $renewalnew);
              for ($i=0; $i < count($renewals); $i++) {
                $renewalin = $renewalin."'".$renewals[$i]."'";
                if($i != (count($renewals)-1)){
                  $renewalin = $renewalin.',';
                }
              }
              $renewalin = "(".$renewalin.") ";
            }
            else{
              $renewalin = "";
            }

            $mail_too = DB::connection('ympimis_2')->select("SELECT
                    a.email 
                FROM
                    (
                    SELECT DISTINCT
                        ((
                            SELECT DISTINCT
                                ( staff_email ) 
                            FROM
                                qa_certificate_points 
                            WHERE
                                qa_certificate_codes.`code` = qa_certificate_points.`code` 
                                AND qa_certificate_codes.`code_number` = qa_certificate_points.`code_number` 
                            )) AS email 
                    FROM
                        qa_certificate_codes 
                    WHERE
                        certificate_id IN ".$renewalin." UNION ALL
                    SELECT DISTINCT
                        ((
                            SELECT DISTINCT
                                ( staff_email ) 
                            FROM
                                qa_certificate_periodes 
                            WHERE
                                qa_certificate_codes.`code` = qa_certificate_periodes.`code` 
                                AND qa_certificate_codes.`code_number` = qa_certificate_periodes.`code_number` 
                            )) AS email 
                    FROM
                        qa_certificate_codes 
                    WHERE
                    certificate_id IN ".$renewalin.") a 
                WHERE
                    a.email IS NOT NULL");

            for ($i=0; $i < count($mail_too); $i++) { 
                $datas = DB::connection('ympimis_2')->select("SELECT DISTINCT
                    ( certificate_id ),
                    periode_from,
                    periode_to,
                    employee_id,
                    `name`,
                    certificate_code,
                    certificate_name,
                    staff_email 
                FROM
                    qa_certificates 
                WHERE
                    certificate_id IN ".$renewalin."
                AND staff_email = '".$mail_too[$i]->email."'
                UNION ALL
                SELECT DISTINCT
                    ( certificate_id ),
                    periode_from,
                    periode_to,
                    employee_id,
                    `name`,
                    certificate_code,
                    certificate_name,
                    staff_email 
                FROM
                    qa_certificate_inprocesses 
                WHERE
                    certificate_id IN ".$renewalin."
                    AND staff_email = '".$mail_too[$i]->email."'");

                $mail_to = [];
                array_push($mail_to, $mail_too[$i]->email);
                array_push($mail_to, 'agustina.hayati@music.yamaha.com');
                array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');

                for ($j=0; $j < count($datas); $j++) { 
                    for ($k=0; $k < count($empsync); $k++) { 
                        if ($datas[$j]->employee_id == $empsync[$k]->employee_id) {
                            if ($empsync[$k]->department != 'Standardization Department') {
                                $approver = Approver::where('remark','Foreman')->where('department',$empsync[$k]->department)->where('section','like','%'.$empsync[$k]->section.'%')->first();
                                array_push($mail_to, $approver->approver_email);
                            }
                        }
                    }

                    $certificate_code_update = DB::connection('ympimis_2')
                    ->table('qa_certificate_codes')
                    ->where('certificate_id',$datas[$j]->certificate_id)
                    ->update([
                        'status' => 2,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    $certificate_code = DB::connection('ympimis_2')
                    ->table('qa_certificates')
                    ->where('certificate_id',$datas[$j]->certificate_id)
                    ->first();

                    if (count($certificate_code) > 0) {
                        $certificate_update = DB::connection('ympimis_2')
                        ->table('qa_certificates')
                        ->where('certificate_id',$datas[$j]->certificate_id)
                        ->update([
                            'status' => 2,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }

                    $certificate_code_inprocess = DB::connection('ympimis_2')
                    ->table('qa_certificate_inprocesses')
                    ->where('certificate_id',$datas[$j]->certificate_id)
                    ->first();

                    if (count($certificate_code_inprocess) > 0) {
                       $certificate_code_inprocess_update = DB::connection('ympimis_2')
                        ->table('qa_certificate_inprocesses')
                        ->where('certificate_id',$datas[$j]->certificate_id)
                        ->update([
                            'status' => 2,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }

                    $datas = DB::connection('ympimis_2')->select("SELECT DISTINCT
                            ( certificate_id ),
                            DATE_FORMAT( periode_from, '%Y-%m-%d' ) AS periode_from,
                            DATE_FORMAT( periode_to, '%Y-%m-%d' ) AS periode_to,
                            employee_id,
                            `name`,
                            certificate_code,
                            certificate_name,
                            staff_email 
                        FROM
                            qa_certificates 
                        WHERE
                            certificate_id IN ".$renewalin."
                        AND staff_email = '".$mail_too[$i]->email."'
                        UNION ALL
                        SELECT DISTINCT
                            ( certificate_id ),
                            DATE_FORMAT( periode_from, '%Y-%m-%d' ) AS periode_from,
                            DATE_FORMAT( periode_to, '%Y-%m-%d' ) AS periode_to,
                            employee_id,
                            `name`,
                            certificate_code,
                            certificate_name,
                            staff_email 
                        FROM
                            qa_certificate_inprocesses 
                        WHERE
                            certificate_id IN ".$renewalin."
                            AND staff_email = '".$mail_too[$i]->email."'");
                }


                Mail::to($mail_to)
                ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
                ->send(new SendEmail($datas, 'qa_certificate_renewal'));
            }
        }

        $certificate_code = DB::connection('ympimis_2')
            ->select('SELECT
                    *,
                    (
                    SELECT DISTINCT
                        ( periode_to ) 
                    FROM
                        qa_certificates 
                    WHERE
                        qa_certificates.certificate_id = qa_certificate_codes.certificate_id UNION ALL
                    SELECT DISTINCT
                        ( periode_to ) 
                    FROM
                        qa_certificate_inprocesses 
                    WHERE
                        qa_certificate_inprocesses.certificate_id = qa_certificate_codes.certificate_id 
                    ) AS periode_to 
                FROM
                    `qa_certificate_codes` 
                WHERE
                    qa_certificate_codes.`status` = 2');

        $expired = [];

        for ($i=0; $i < count($certificate_code); $i++) { 
            $now = date('Y-m-d');
            if ($now == $certificate_code[$i]->periode_to) {
                array_push($expired, $certificate_code[$i]->certificate_id);
            }
        }

        if (count($expired) > 0) {
            $expirednew = join(',',$expired);

            $expiredin = '';
            if($expirednew != null){
              $expireds =  explode(",", $expirednew);
              for ($i=0; $i < count($expireds); $i++) {
                $expiredin = $expiredin."'".$expireds[$i]."'";
                if($i != (count($expireds)-1)){
                  $expiredin = $expiredin.',';
                }
              }
              $expiredin = "(".$expiredin.") ";
            }
            else{
              $expiredin = "";
            }

            $mail_too = DB::connection('ympimis_2')->select("SELECT
                a.email 
            FROM
                (
                SELECT DISTINCT
                    ((
                        SELECT DISTINCT
                            ( staff_email ) 
                        FROM
                            qa_certificate_points 
                        WHERE
                            qa_certificate_codes.`code` = qa_certificate_points.`code` 
                            AND qa_certificate_codes.`code_number` = qa_certificate_points.`code_number` 
                        )) AS email 
                FROM
                    qa_certificate_codes 
                WHERE
                    certificate_id IN ".$expiredin." UNION ALL
                SELECT DISTINCT
                    ((
                        SELECT DISTINCT
                            ( staff_email ) 
                        FROM
                            qa_certificate_periodes 
                        WHERE
                            qa_certificate_codes.`code` = qa_certificate_periodes.`code` 
                            AND qa_certificate_codes.`code_number` = qa_certificate_periodes.`code_number` 
                        )) AS email 
                FROM
                    qa_certificate_codes 
                WHERE
                certificate_id IN ".$expiredin.") a 
            WHERE
                a.email IS NOT NULL");

            for ($i=0; $i < count($mail_too); $i++) { 
                $datas = DB::connection('ympimis_2')->select("SELECT DISTINCT
                    ( certificate_id ),
                    DATE_FORMAT( periode_from, '%Y-%m-%d' ) AS periode_from,
                    DATE_FORMAT( periode_to, '%Y-%m-%d' ) AS periode_to,
                    employee_id,
                    `name`,
                    certificate_code,
                    certificate_name,
                    staff_email 
                FROM
                    qa_certificates 
                WHERE
                    certificate_id IN ".$expiredin."
                AND staff_email = '".$mail_too[$i]->email."'
                UNION ALL
                SELECT DISTINCT
                    ( certificate_id ),
                    DATE_FORMAT( periode_from, '%Y-%m-%d' ) AS periode_from,
                    DATE_FORMAT( periode_to, '%Y-%m-%d' ) AS periode_to,
                    employee_id,
                    `name`,
                    certificate_code,
                    certificate_name,
                    staff_email 
                FROM
                    qa_certificate_inprocesses 
                WHERE
                    certificate_id IN ".$expiredin."
                    AND staff_email = '".$mail_too[$i]->email."'");

                $mail_to = [];
                array_push($mail_to, $mail_too[$i]->email);
                array_push($mail_to, 'agustina.hayati@music.yamaha.com');
                array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');

                for ($j=0; $j < count($datas); $j++) { 
                    for ($k=0; $k < count($empsync); $k++) { 
                        if ($datas[$j]->employee_id == $empsync[$k]->employee_id) {
                            if ($empsync[$k]->department != 'Standardization Department') {
                                $approver = Approver::where('remark','Foreman')->where('department',$empsync[$k]->department)->where('section','like','%'.$empsync[$k]->section.'%')->first();;
                                array_push($mail_to, $approver->approver_email);
                            }
                        }
                    }

                    $certificate_code_update = DB::connection('ympimis_2')
                    ->table('qa_certificate_codes')
                    ->where('certificate_id',$datas[$j]->certificate_id)
                    ->update([
                        'status' => 3,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    $certificate_code = DB::connection('ympimis_2')
                    ->table('qa_certificates')
                    ->where('certificate_id',$datas[$j]->certificate_id)
                    ->first();

                    if (count($certificate_code) > 0) {
                        $certificate_update = DB::connection('ympimis_2')
                        ->table('qa_certificates')
                        ->where('certificate_id',$datas[$j]->certificate_id)
                        ->update([
                            'status' => 3,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }

                    $certificate_code_inprocess = DB::connection('ympimis_2')
                    ->table('qa_certificate_inprocesses')
                    ->where('certificate_id',$datas[$j]->certificate_id)
                    ->first();

                    if (count($certificate_code_inprocess) > 0) {
                        $certificate_inprocess = DB::connection('ympimis_2')
                        ->table('qa_certificate_inprocesses')
                        ->where('certificate_id',$datas[$j]->certificate_id)
                        ->update([
                            'status' => 3,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }

                Mail::to($mail_to)
                ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
                ->send(new SendEmail($datas, 'qa_certificate_expired'));
            }
        }

        $today_name = date('l');
        $today = date('Y-m-d');
        $first = date('Y-m-01');
        $next_month = date('Y-m');

        if ($today_name == 'Monday') {
            $special_audit = DB::connection('ympimis_2')->select("SELECT
                    * 
                FROM
                    qa_process_audit_schedules 
                WHERE
                    qa_process_audit_schedules.schedule_status = 'Belum Dikerjakan' 
                    AND DATE_FORMAT( schedule_date, '%Y-%m' ) <= '".$next_month."'");

            if (count($special_audit) > 0) {
                $mail_to = [];
                $data = [];
                for ($i=0; $i < count($special_audit); $i++) { 
                    if (str_contains($special_audit[$i]->auditor_id,',')) {
                        $auditor = explode(',', $special_audit[$i]->auditor_id);
                        for ($j=0; $j < count($auditor); $j++) { 
                            $user = User::where('username',$auditor[$j])->first();
                            array_push($mail_to, $user->email);
                        }
                    }else{
                        $user = User::where('username',$special_audit[$i]->auditor_id)->first();
                        array_push($mail_to, $user->email);
                    }
                }
                array_push($data, [
                    'title' => 'weekly',
                    'datas' => $special_audit
                ]);
                Mail::to($mail_to)
                ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
                ->send(new SendEmail($data, 'audit_special_process_reminder'));
            }
        }

        if ($today == $first) {
            $special_audit = DB::connection('ympimis_2')->select("SELECT
                    * 
                FROM
                    qa_process_audit_schedules 
                WHERE
                    qa_process_audit_schedules.schedule_status = 'Belum Dikerjakan' 
                    AND DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$next_month."'");

            if (count($special_audit) > 0) {
                $mail_to = [];
                $data = [];
                for ($i=0; $i < count($special_audit); $i++) { 
                    if (str_contains($special_audit[$i]->auditor_id,',')) {
                        $auditor = explode(',', $special_audit[$i]->auditor_id);
                        for ($j=0; $j < count($auditor); $j++) { 
                            $user = User::where('username',$auditor[$j])->first();
                            array_push($mail_to, $user->email);
                        }
                    }else{
                        $user = User::where('username',$special_audit[$i]->auditor_id)->first();
                        array_push($mail_to, $user->email);
                    }
                }
                array_push($data, [
                    'title' => 'first',
                    'datas' => $special_audit
                ]);
                Mail::to($mail_to)
                ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
                ->send(new SendEmail($data, 'audit_special_process_reminder'));
            }
        }

        $due_date_audit = DB::connection('ympimis_2')->select("SELECT
                document_number,
                document_name,
                auditor_id,
                auditor_name,
                auditee_id,
                auditee_name,
                schedule_id,
                due_date,
                schedule_date 
            FROM
                qa_process_audits 
            WHERE
                ( qa_process_audits.handling IS NULL AND decision = 'NG' AND due_date = '".date('Y-m-d',strtotime('+7 days'))."' ) 
                OR ( qa_process_audits.handling IS NULL AND decision = 'NS' AND due_date = '".date('Y-m-d',strtotime('+7 days'))."' ) 
            GROUP BY
                document_number,
                document_name,
                auditor_id,
                auditor_name,
                auditee_id,
                auditee_name,
                schedule_id,
                due_date,
                schedule_date");

        if (count($due_date_audit) > 0) {
            $mail_to = [];
            $cc = [];
            $data = [];
            for ($i=0; $i < count($due_date_audit); $i++) { 
                $user = User::where('username',$due_date_audit[$i]->auditee_id)->first();
                $emp = EmployeeSync::where('employee_id',$due_date_audit[$i]->auditee_id)->first();
                if ($emp->department == 'Standardization Department') {
                    array_push($mail_to, 'agustina.hayati@music.yamaha.com');
                }else{
                    array_push($mail_to, $user->email);
                }

                $emp = EmployeeSync::where('employee_id',$due_date_audit[$i]->auditee_id)->first();
                $manager = Approver::where('department',$emp->department)->where('remark','Manager')->first();
                array_push($cc, $manager->approver_email);
            }

            array_push($data, [
                'title' => 'week_before',
                'datas' => $due_date_audit
            ]);
            Mail::to($mail_to)
            ->cc($cc)
            ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
            ->send(new SendEmail($data, 'handling_audit_special_process'));
        }

        if ($today_name == 'Monday') {
            $due_date_audit = DB::connection('ympimis_2')->select("SELECT
                document_number,
                document_name,
                auditor_id,
                auditor_name,
                auditee_id,
                auditee_name,
                schedule_id,
                due_date,
                schedule_date 
            FROM
                qa_process_audits 
            WHERE
                ( qa_process_audits.handling IS NULL AND decision = 'NG' AND due_date <= DATE( NOW( ) ) ) 
                OR ( qa_process_audits.handling IS NULL AND decision = 'NS' AND due_date <= DATE( NOW( ) ) ) 
            GROUP BY
                document_number,
                document_name,
                auditor_id,
                auditor_name,
                auditee_id,
                auditee_name,
                schedule_id,
                due_date,
                schedule_date");

            if (count($due_date_audit) > 0) {
                $mail_to = [];
                $cc = [];
                $data = [];
                array_push($cc, 'budhi.apriyanto@music.yamaha.com');
                for ($i=0; $i < count($due_date_audit); $i++) { 
                    $user = User::where('username',$due_date_audit[$i]->auditee_id)->first();
                    $emp = EmployeeSync::where('employee_id',$due_date_audit[$i]->auditee_id)->first();
                    if ($emp->department == 'Standardization Department') {
                        array_push($mail_to, 'agustina.hayati@music.yamaha.com');
                    }else{
                        array_push($mail_to, $user->email);
                    }

                    $emp = EmployeeSync::where('employee_id',$due_date_audit[$i]->auditee_id)->first();
                    $manager = Approver::where('department',$emp->department)->where('remark','Manager')->first();
                    array_push($cc, $manager->approver_email);
                }
                array_push($data, [
                'title' => 'out_of_date',
                'datas' => $due_date_audit
            ]);
                Mail::to($mail_to)
                ->cc($cc)
                ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
                ->send(new SendEmail($data, 'handling_audit_special_process'));
            }
        }

        //AUDIT COMPRESSOR

        // $yesterday = date('Y-m-d',strtotime(' -1 days'));
        //   $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars` order by week_date desc");
        //   foreach ($weekly_calendars as $key) {
        //     if ($key->week_date == $yesterday) {
        //       if ($key->remark == 'H') {
        //         $yesterday = date('Y-m-d', strtotime($yesterday.'-1 days'));
        //       }
        //     }
        //   }

        // $activity_compressor = DB::connection('ympimis_2')->select("SELECT DISTINCT
        //         ( activity_list_id ),
        //         location
        //     FROM
        //         `daily_audit_points` 
        //     WHERE
        //         category = 'compressor' 
        //         AND activity_list_id NOT IN ( SELECT DISTINCT ( activity_list_id ) FROM `daily_audits` WHERE category = 'compressor' AND date = '".$yesterday."' )");

        // $mail_to = [];
        // $audit = [];
        // $data = [];

        // if (count($activity_compressor) > 0) {
        //     for ($i=0; $i < count($activity_compressor); $i++) { 
        //         $activity = DB::SELECT("SELECT
        //             '".$yesterday."' as date,
        //             '".$activity_compressor[$i]->location."' as location,
        //             activity_lists.id,
        //             foreman_dept,
        //             email,
        //             leader_dept 
        //         FROM
        //             activity_lists
        //             JOIN users ON users.`name` = foreman_dept 
        //         WHERE
        //             activity_lists.id = '".$activity_compressor[$i]->activity_list_id."'");

        //         array_push($mail_to, $activity[0]->email);
        //         array_push($audit, $activity);
        //     }
        //     array_push($data, [
        //         'audit' => $audit,
        //         'title' => 'AUDIT KEBOCORAN KOMPRESSOR'
        //     ]);
        //     Mail::to($mail_to)
        //         ->bcc(['ympi-mis-ML@music.yamaha.com'])
        //         ->send(new SendEmail($data, 'middle_audit_reminder'));
        // }

        // //AUDIT QC KOTEIHYO

        // $today_name = date('l');
        // $today = date('Y-m-d');
        // $first = date('Y-m-01');
        // $next_month = date('Y-m');

        // if ($today_name == 'Monday') {
        //     $schedule_qc_koteihyo = DB::connection('ympimis_2')->select("SELECT
        //             * 
        //         FROM
        //             qa_qc_koteihyo_schedules 
        //         WHERE
        //             qa_qc_koteihyo_schedules.status = 'Belum Dikerjakan' 
        //             AND DATE_FORMAT( schedule_date, '%Y-%m' ) <= '".$next_month."'");

        //     if (count($schedule_qc_koteihyo) > 0) {
        //         $mail_to = [];
        //         $data = [];
        //         for ($i=0; $i < count($schedule_qc_koteihyo); $i++) { 
        //             if (str_contains($schedule_qc_koteihyo[$i]->employee_id,',')) {
        //                 $auditor = explode(',', $schedule_qc_koteihyo[$i]->employee_id);
        //                 for ($j=0; $j < count($auditor); $j++) { 
        //                     $user = User::where('username',$auditor[$j])->first();
        //                     array_push($mail_to, $user->email);
        //                 }
        //             }else{
        //                 $user = User::where('username',$schedule_qc_koteihyo[$i]->employee_id)->first();
        //                 array_push($mail_to, $user->email);
        //             }
        //         }
        //         array_push($data, [
        //             'title' => 'weekly',
        //             'datas' => $schedule_qc_koteihyo
        //         ]);
        //         Mail::to($mail_to)
        //         ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
        //         ->send(new SendEmail($data, 'audit_qc_koteihyo_reminder'));
        //     }
        // }

        // if ($today == $first) {
        //     $schedule_qc_koteihyo = DB::connection('ympimis_2')->select("SELECT
        //             * 
        //         FROM
        //             qa_qc_koteihyo_schedules 
        //         WHERE
        //             qa_qc_koteihyo_schedules.status = 'Belum Dikerjakan' 
        //             AND DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$next_month."'");

        //     if (count($schedule_qc_koteihyo) > 0) {
        //         $mail_to = [];
        //         $data = [];
        //         for ($i=0; $i < count($schedule_qc_koteihyo); $i++) { 
        //             if (str_contains($schedule_qc_koteihyo[$i]->employee_id,',')) {
        //                 $auditor = explode(',', $schedule_qc_koteihyo[$i]->employee_id);
        //                 for ($j=0; $j < count($auditor); $j++) { 
        //                     $user = User::where('username',$auditor[$j])->first();
        //                     array_push($mail_to, $user->email);
        //                 }
        //             }else{
        //                 $user = User::where('username',$schedule_qc_koteihyo[$i]->employee_id)->first();
        //                 array_push($mail_to, $user->email);
        //             }
        //         }
        //         array_push($data, [
        //             'title' => 'first',
        //             'datas' => $schedule_qc_koteihyo
        //         ]);
        //         Mail::to($mail_to)
        //         ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
        //         ->send(new SendEmail($data, 'audit_qc_koteihyo_reminder'));
        //     }
        // }

        // $due_date_audit = DB::connection('ympimis_2')->select("SELECT
        //         document_number,
        //         document_name,
        //         auditor_id,
        //         auditor_name,
        //         auditee_id,
        //         auditee_name,
        //         schedule_id,
        //         employee_id,
        //         employee_name,
        //         due_date,
        //         schedule_date 
        //     FROM
        //         qa_qc_koteihyo_audits 
        //     WHERE
        //         ( qa_qc_koteihyo_audits.handling IS NULL AND `condition` = 'NG' AND due_date = '".date('Y-m-d',strtotime('+7 days'))."' ) 
        //     GROUP BY
        //         document_number,
        //         document_name,
        //         auditor_id,
        //         auditor_name,
        //         auditee_id,
        //         employee_id,
        //         employee_name,
        //         auditee_name,
        //         schedule_id,
        //         due_date,
        //         schedule_date
        //         ");

        // if (count($due_date_audit) > 0) {
        //     $mail_to = [];
        //     $cc = [];
        //     for ($i=0; $i < count($due_date_audit); $i++) { 
        //         $user = User::where('username',$due_date_audit[$i]->auditee_id)->first();
        //         $emp = EmployeeSync::where('employee_id',$due_date_audit[$i]->auditee_id)->first();
                
        //         if (str_contains($user->email,'@music.yamaha.com')) {
        //             array_push($mail_to, $user->email);
        //         }else{
        //             $foreman = Approver::where('department',$emp->department)->where('section',$emp->section)->where('remark','Foreman')->first();
        //             if ($foreman != null) {
        //                 array_push($mail_to, $foreman->approver_email);
        //             }
        //         }

        //         $emp = EmployeeSync::where('employee_id',$due_date_audit[$i]->auditee_id)->first();
        //         $manager = Approver::where('department',$emp->department)->where('remark','Manager')->first();
        //         array_push($cc, $manager->approver_email);
        //     }
        //     Mail::to($mail_to)
        //     ->cc($cc)
        //     ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
        //     ->send(new SendEmail($due_date_audit, 'handling_audit_qc_koteihyo'));
        // }

        // if ($today_name == 'Monday') {
        //     $due_date_audit = DB::connection('ympimis_2')->select("SELECT
        //         document_number,
        //         document_name,
        //         auditor_id,
        //         auditor_name,
        //         auditee_id,
        //         auditee_name,
        //         schedule_id,
        //         employee_id,
        //         employee_name,
        //         due_date,
        //         schedule_date 
        //     FROM
        //         qa_qc_koteihyo_audits 
        //     WHERE
        //         ( qa_qc_koteihyo_audits.handling IS NULL AND `condition` = 'NG' AND due_date <= DATE(NOW()) ) 
        //     GROUP BY
        //         document_number,
        //         document_name,
        //         auditor_id,
        //         auditor_name,
        //         auditee_id,
        //         employee_id,
        //         employee_name,
        //         auditee_name,
        //         schedule_id,
        //         due_date,
        //         schedule_date");

        //     if (count($due_date_audit) > 0) {
        //         $mail_to = [];
        //         $cc = [];
        //         for ($i=0; $i < count($due_date_audit); $i++) { 
        //             $user = User::where('username',$due_date_audit[$i]->auditee_id)->first();
        //             $emp = EmployeeSync::where('employee_id',$due_date_audit[$i]->auditee_id)->first();
        //             if (str_contains($user->email,'@music.yamaha.com')) {
        //                 array_push($mail_to, $user->email);
        //             }else{
        //                 $foreman = Approver::where('department',$emp->department)->where('section',$emp->section)->where('remark','Foreman')->first();
        //                 if ($foreman != null) {
        //                     array_push($mail_to, $foreman->approver_email);
        //                 }
        //             }

        //             $emp = EmployeeSync::where('employee_id',$due_date_audit[$i]->auditee_id)->first();
        //             $manager = Approver::where('department',$emp->department)->where('remark','Manager')->first();
        //             array_push($cc, $manager->approver_email);
        //         }
        //         Mail::to($mail_to)
        //         ->cc($cc)
        //         ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
        //         ->send(new SendEmail($due_date_audit, 'handling_audit_qc_koteihyo'));
        //     }
        // }

        //AUDIT IK

        $today = date('Y-m-d');
        $first = date('Y-m-01');

        $fys = DB::SELECT("SELECT DISTINCT
                    ( fiscal_year )
                    FROM
                    weekly_calendars
                    WHERE
                    week_date = DATE(
                    NOW())");

        $firstlast = DB::SELECT("( SELECT DISTINCT
                (
                DATE_FORMAT( week_date, '%Y-%m' )) AS `month`,
                DATE_FORMAT( week_date, '%b %Y' ) AS month_name
                FROM
                weekly_calendars
                WHERE
                fiscal_year = '" . $fys[0]->fiscal_year . "'
                ORDER BY
                week_date
                LIMIT 1
                ) UNION ALL
                (
                SELECT DISTINCT
                (
                DATE_FORMAT( week_date, '%Y-%m' )) AS `month`,
                DATE_FORMAT( week_date, '%b %Y' ) AS month_name
                FROM
                weekly_calendars
                WHERE
                fiscal_year = '" . $fys[0]->fiscal_year . "'
                ORDER BY
                week_date DESC
                LIMIT 1)");

        $today_name = date('l');

        if ($today_name == 'Monday') {
            $audit_all = DB::select("SELECT
                    a.* 
                FROM
                    (
                    SELECT
                        audit_report_activities.leader,
                        department,
                        departments.department_shortname,
                        UPPER( username ) AS username,
                        sum( CASE WHEN audit_report_activities.handling = 'Revisi IK' THEN 1 ELSE 0 END ) AS revisi_ik,
                        sum( CASE WHEN audit_report_activities.handling = 'Pembuatan Jig / Repair Jig' THEN 1 ELSE 0 END ) AS revisi_jig,
                        -- sum( CASE WHEN audit_report_activities.handling = 'Training Ulang IK' THEN 1 ELSE 0 END ) AS training_ulang_ik,
                        sum( CASE WHEN audit_report_activities.handling = 'Revisi QC Kouteihyo' THEN 1 ELSE 0 END ) AS revisi_qc_koteihyo,
                        sum( CASE WHEN audit_report_activities.handling = 'IK Tidak Digunakan' THEN 1 ELSE 0 END ) AS ik_obsolete,
                        audit_guidances.`month` 
                    FROM
                        `audit_report_activities`
                        JOIN departments ON departments.department_name = department
                        JOIN users ON users.`name` = leader
                        JOIN audit_guidances ON audit_guidances.id = audit_report_activities.audit_guidance_id 
                    WHERE
                        handling_status IS NULL 
                        AND audit_report_activities.deleted_at IS NULL 
                        AND users.deleted_at IS NULL 
                        AND audit_guidances.deleted_at IS NULL 
                    GROUP BY
                        audit_report_activities.leader,
                        department,
                        audit_report_activities.department,
                        departments.department_shortname,
                        username,
                        audit_guidances.`month` 
                    ) a 
                WHERE
                    a.revisi_ik != 0
                    AND a.`month` >= '".$firstlast[0]->month."' AND a.`month` <= '".$firstlast[1]->month."' 
                ORDER BY
                a.`month`");

            if (count($audit_all) > 0) {
                $depts = [];
                $leader = [];
                for ($i=0; $i < count($audit_all); $i++) { 
                    array_push($leader, $audit_all[$i]->username.' - '.$audit_all[$i]->leader);

                    $audits = DB::SELECT("SELECT
                        audit_report_activities.id
                    FROM
                        audit_report_activities
                        JOIN audit_guidances ON audit_guidances.id = audit_report_activities.audit_guidance_id 
                    WHERE
                        audit_report_activities.leader = '".$audit_all[$i]->leader."' 
                        AND audit_guidances.`month` = '".$audit_all[$i]->month."' 
                        AND handling != 'Tidak Ada Penanganan'");

                    if (count($audits) > 0) {
                        for ($j=0; $j < count($audits); $j++) { 
                            $updates = DB::table('audit_report_activities')->where('id',$audits[$j]->id)->update([
                                'reminder_status' => date('Y-m-d H:i:s')
                            ]);
                        }
                    }

                    array_push($depts, $audit_all[$i]->department_shortname);
                }

                $leaders = array_unique($leader);

                $leader_new = array_values($leaders);

                $month = [];
                for ($i=0; $i < count($audit_all); $i++) { 
                    array_push($month, $audit_all[$i]->month);
                }

                $months = array_unique($month);

                $month_new = array_values($months);

                $data = array(
                    'audit' => $audit_all,
                    'leader' => $leader_new,
                    'month' => $month_new,
                );

                $mail_to = [];
                $cc = [];

                for ($i=0; $i < count($leader_new); $i++) { 
                    $emp = EmployeeSync::where('employee_id',explode(' - ', $leader_new[$i])[0])->first();
                    if ($emp) {
                        $foreman = Approver::where('department',$emp->department)->where('section',$emp->section)->where('remark','Foreman')->first();
                        $manager = Approver::where('department',$emp->department)->where('remark','Manager')->first();
                        if (!$foreman) {
                            $foreman = Approver::where('department',$emp->department)->where('section',$emp->section)->where('remark','Chief')->first();
                        }
                        if ($foreman) {
                            array_push($mail_to, $foreman->approver_email);
                        }
                        if ($manager) {
                            array_push($cc, $manager->approver_email);
                        }
                    }
                }

                array_push($cc, 'sutrisno@music.yamaha.com');
                array_push($cc, 'ertikto.singgih@music.yamaha.com');
                array_push($cc, 'abdissalam.saidi@music.yamaha.com');
                array_push($cc, 'ratri.sulistyorini@music.yamaha.com');
                array_push($cc, 'agustina.hayati@music.yamaha.com');

                array_push($cc, 'widura@music.yamaha.com');
                array_push($cc, 'vidiya.chalista@music.yamaha.com');
                array_push($cc, 'syafrizal.carnov.purwanto@music.yamaha.com');
                array_push($cc, 'rani.nurdiyana.sari@music.yamaha.com');

                Mail::to($mail_to)
                    ->cc($cc)
                    ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
                    ->send(new SendEmail($data, 'audit_ik_reminder'));
            }
        }

        $today_name = date('l');

        if ($today_name == 'Monday') {
            $periode = DB::SELECT("SELECT DISTINCT
                ( fiscal_year ) 
            FROM
                weekly_calendars 
            WHERE
                week_date = DATE(
                NOW())");
            $audit = DB::SELECT("SELECT
                    leader,
                    foreman,
                    `month`,
                    DATE_FORMAT( CONCAT( `month`, '-01' ), '%b-%Y' ) AS month_name,
                    count( no_dokumen ) AS qty 
                FROM
                    audit_guidances 
                WHERE
                    `month` = DATE_FORMAT( NOW( ), '%Y-%m' ) 
                    AND `status` = 'Belum Dikerjakan' 
                    AND periode = '".$periode[0]->fiscal_year."' 
                    AND deleted_at IS NULL 
                GROUP BY
                    leader,
                    foreman,
                    `month`");

            if (count($audit) > 0) {
                $mail_to = [];
                $cc = [];
                for ($i=0; $i < count($audit); $i++) { 
                    $users = User::where('name',$audit[$i]->foreman)->first();
                    array_push($mail_to, $users->email);
                }

                array_push($cc, 'widura@music.yamaha.com');
                array_push($cc, 'syafrizal.carnov.purwanto@music.yamaha.com');

                Mail::to($mail_to)
                    ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
                    ->cc($cc)
                    ->send(new SendEmail($audit, 'audit_ik_schedule_reminder'));
            }
        }

        if ($today_name == 'Monday') {
            $periode = DB::SELECT("SELECT DISTINCT
                ( fiscal_year ) 
            FROM
                weekly_calendars 
            WHERE
                week_date = DATE(
                NOW())");
            $audit = DB::SELECT("SELECT
                    leader,
                    foreman,
                    `month`,
                    DATE_FORMAT( CONCAT( `month`, '-01' ), '%b-%Y' ) AS month_name,
                    count( no_dokumen ) AS qty 
                FROM
                    audit_guidances 
                WHERE
                    `month` < DATE_FORMAT( NOW( ), '%Y-%m' ) 
                    AND `status` = 'Belum Dikerjakan' 
                    AND periode = '".$periode[0]->fiscal_year."' 
                    AND deleted_at IS NULL 
                GROUP BY
                    leader,
                    foreman,
                    `month`");

            if (count($audit) > 0) {
                $mail_to = [];
                $cc = [];
                for ($i=0; $i < count($audit); $i++) { 
                    $users = User::where('name',$audit[$i]->foreman)->first();
                    array_push($mail_to, $users->email);
                }

                array_push($cc, 'widura@music.yamaha.com');
                array_push($cc, 'syafrizal.carnov.purwanto@music.yamaha.com');

                Mail::to($mail_to)
                    ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
                    ->cc($cc)
                    ->send(new SendEmail($audit, 'audit_ik_schedule_reminder_before'));
            }
        }

        if ($today_name == 'Monday') {
            $periode = DB::SELECT("SELECT DISTINCT
                ( fiscal_year ) 
            FROM
                weekly_calendars 
            WHERE
                week_date = DATE(
                NOW())");
            $audit = DB::SELECT("SELECT
                audit_report_activities.id,
                audit_report_activities.leader,
                department,
                departments.department_shortname,
                audit_guidances.`month`,
                audit_report_activities.date,
                audit_report_activities.no_dokumen,
                audit_report_activities.nama_dokumen,
                audit_report_activities.foreman,
                audit_report_activities.handling,
                audit_report_activities.foreman,
                audit_report_activities.kesesuaian_qc_kouteihyo,
                audit_report_activities.result_qc_koteihyo,
                audit_report_activities.qa_verification,
                audit_report_activities.handling_status 
            FROM
                audit_report_activities
                JOIN audit_guidances ON audit_guidances.id = audit_report_activities.audit_guidance_id
                JOIN departments ON departments.department_name = department 
            WHERE
                ( handling = 'Revisi QC Kouteihyo' AND audit_report_activities.deleted_at IS NULL AND audit_guidances.deleted_at IS NULL AND qa_verification IS NULL  ) 
                OR (
                    handling = 'Revisi QC Kouteihyo' 
                    AND audit_report_activities.deleted_at IS NULL 
                AND audit_guidances.deleted_at IS NULL 
                AND handling_status IS NULL)");

            if (count($audit) > 0) {
                $depts = [];
                for ($i=0; $i < count($audit); $i++) { 
                    array_push($depts, $audit[$i]->department_shortname);
                }
                $mail_to = [];
                $cc = [];

                array_push($mail_to, 'sutrisno@music.yamaha.com');
                array_push($mail_to, 'ertikto.singgih@music.yamaha.com');
                array_push($mail_to, 'abdissalam.saidi@music.yamaha.com');
                array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');
                array_push($mail_to, 'agustina.hayati@music.yamaha.com');

                if (str_contains(join(',',$depts),'PP')) {
                    array_push($mail_to, 'tegar.brillian@music.yamaha.com');
                    array_push($mail_to, 'darma.bagus@music.yamaha.com');
                }

                Mail::to($mail_to)
                    ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
                    // ->cc($cc)
                    ->send(new SendEmail($audit, 'audit_ik_qc_koteihyo_reminder'));
            }
        }

        $audit_packing = DB::connection('ympimis_2')->select("SELECT
            DATE( created_at ) AS date_audit,
            DATE_FORMAT( created_at, '%d-%b-%Y' ) AS date_audit_name,
            audit_id,
            product,
            material_audited,
            serial_number,
            material_number,
            material_description,
            GROUP_CONCAT(
            DISTINCT ( send_status )) AS send_status,
            GROUP_CONCAT(
            DISTINCT ( `result_check` )) AS `result_check`,
            GROUP_CONCAT(
            DISTINCT ( handled_id )) AS handled_id,
            GROUP_CONCAT(
            DISTINCT ( handled_at )) AS handled_at,
            GROUP_CONCAT(
            DISTINCT ( due_date )) AS due_date,
            GROUP_CONCAT(
            DISTINCT ( auditor_id )) AS auditor_id,
            GROUP_CONCAT(
            DISTINCT ( auditor_name )) AS auditor_name,
            GROUP_CONCAT(
            DISTINCT ( auditee_id )) AS auditee_id,
            GROUP_CONCAT(
            DISTINCT ( auditee_name )) AS auditee_name 
        FROM
            qa_packing_audits 
        WHERE
            qa_packing_audits.handling IS NULL 
            AND `result_check` = 'NG' 
        GROUP BY
            `date_audit`,
            audit_id,
            date_audit_name,
            product,
            material_audited,
            serial_number,
            material_number,
            material_description 
        ORDER BY
            date_audit");

        $today_name = date('l');

        if ($today_name == 'Monday') {
            if (count($audit_packing) > 0) {
                $mail_to = [];
                $cc = [];
                for ($i=0; $i < count($audit_packing); $i++) {
                    $emp = EmployeeSync::where('employee_id',$audit_packing[$i]->auditee_id)->first();
                    $user = User::where('username',$audit_packing[$i]->auditee_id)->first();
                    if (str_contains($user->email,'@music.yamaha.com')) {
                        array_push($mail_to, $user->email);
                    }else{
                        $foreman = Approver::where('department',$emp->department)->where('remark','Foreman')->first();
                        if ($foreman) {
                            array_push($mail_to, $foreman->approver_email);
                        }else{
                            $foreman = Approver::where('department',$emp->department)->where('remark','Chief')->first();
                            if ($foreman) {
                                array_push($mail_to, $foreman->approver_email);
                            }
                        }
                    }

                    $manager = Approver::where('department',$emp->department)->where('remark','Manager')->first();
                    array_push($cc, $manager->approver_email);
                }
                Mail::to($mail_to)
                ->cc($cc)
                ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
                ->send(new SendEmail($audit_packing, 'handling_audit_packing'));
            }
        }

        $audit_cpar_car = DB::select("SELECT
                DATE( audit_external_claims.created_at ) AS date_audit,
                DATE_FORMAT( audit_external_claims.created_at, '%d-%b-%Y' ) AS date_audit_name,
                audit_id,
                audit_title,
                schedule_id,
                GROUP_CONCAT(
                DISTINCT ( `result_check` )) AS `result_check`,
                GROUP_CONCAT(
                DISTINCT ( handled_by )) AS handled_by,
                GROUP_CONCAT(
                DISTINCT ( auditor )) AS auditor_id,
                GROUP_CONCAT(
                DISTINCT ( employee_syncs.`name` )) AS auditor_name,
                auditee.employee_id AS auditee_id,
                auditee.`name` AS auditee_name,
                SPLIT_STRING ( chief_foreman, ',', 1 ) as chief_foreman,
                auditee.department 
            FROM
                audit_external_claims
                JOIN employee_syncs ON employee_syncs.employee_id = audit_external_claims.auditor
                LEFT JOIN users ON users.email = SPLIT_STRING ( chief_foreman, ',', 1 )
                LEFT JOIN employee_syncs AS auditee ON auditee.employee_id = users.username 
            WHERE
                audit_external_claims.handling IS NULL 
                AND `result_check` = 'NG' 
                AND remark = 'cpar_car' 
            GROUP BY
                `date_audit`,
                audit_id,
                date_audit_name,
                audit_title,
                schedule_id,
                auditee_id,
                auditee_name,
                chief_foreman,
                department 
            ORDER BY
                date_audit");

        $today_name = date('l');

        if ($today_name == 'Monday') {
            if (count($audit_cpar_car) > 0) {
                $mail_to = [];
                $cc = [];
                for ($i=0; $i < count($audit_cpar_car); $i++) {
                    array_push($mail_to, $audit_cpar_car[$i]->chief_foreman);
                    array_push($mail_to, 'agustina.hayati@music.yamaha.com');
                    array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');

                    $manager = Approver::where('department',$audit_cpar_car[$i]->department)->where('remark','Manager')->first();
                    array_push($cc, $manager->approver_email);
                }
                Mail::to($mail_to)
                ->cc($cc)
                ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
                ->send(new SendEmail($audit_cpar_car, 'handling_audit_cpar_car'));
            }
        }
    }
}
