<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;

class EmailSafetyRiding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:safety_riding';

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
        $period = date('Y-m-01', strtotime('-4 Days'));
        // $period = '2021-10-01';

        $safety_ridings = db::select("SELECT
            sr.period,
            sr.location,
            sr.department,
            sr.department_shortname,
            sr.total_emp,
            sr.total_sr,
            IFNULL( srr.maru, 0 ) AS maru,
            IFNULL( srr.batsu, 0 ) AS batsu,
            wc.total_date 
            FROM
            (
                SELECT
                sr.period,
                sr.location,
                sr.department,
                d.department_shortname,
                count( sr.employee_id ) AS total_emp,
                count(
                    IF
                    ( sr.safety_riding = '', NULL, 1 )) AS total_sr 
                FROM
                safety_ridings AS sr
                LEFT JOIN departments AS d ON d.department_name = sr.department 
                GROUP BY
                sr.period,
                sr.location,
                sr.department,
                d.department_shortname 
                ) AS sr
            LEFT JOIN (
                SELECT
                srr.period,
                srr.location,
                srr.department,
                count(
                    IF
                    ( srr.remark = 'maru', 1, NULL )) AS maru,
                count(
                    IF
                    ( srr.remark = 'batsu', 1, NULL )) AS batsu 
                FROM
                safety_riding_records AS srr 
                GROUP BY
                period,
                location,
                department 
                ) AS srr ON srr.period = sr.period 
            AND srr.location = sr.location 
            AND srr.department = sr.department
            LEFT JOIN (
                SELECT
                date_format( week_date, '%Y-%m-01' ) AS period,
                count( week_date ) AS total_date 
                FROM
                weekly_calendars 
                GROUP BY
                date_format( week_date, '%Y-%m-01' )) AS wc ON wc.period = sr.period
            HAVING 
            sr.period = '".$period."'
            ORDER BY
            sr.period DESC, sr.department ASC");

        $approvers = db::select("SELECT
            period,
            department,
            employee_id,
            employee_name,
            remark,
            location,
            created_at
            FROM
            safety_riding_approvers
            WHERE
            period = '".$period."'");

        $results = array();

        foreach($safety_ridings as $safety_riding){
            $cb_name = "";
            $cb_at = "";
            $ca_name = "";
            $ca_at = "";
            $mb_name = "";
            $mb_at = "";
            $ma_name = "";
            $ma_at = "";

            foreach($approvers as $approver){
                if($approver->location == $safety_riding->location && $approver->period == $safety_riding->period && $approver->department == $safety_riding->department && $approver->remark == 'chief-before'){
                    $cb_name = $approver->employee_name;
                    $cb_at = $approver->created_at;
                }
                if($approver->location == $safety_riding->location && $approver->period == $safety_riding->period && $approver->department == $safety_riding->department && $approver->remark == 'manager-before'){
                    $mb_name = $approver->employee_name;
                    $mb_at = $approver->created_at;
                }
                if($approver->location == $safety_riding->location && $approver->period == $safety_riding->period && $approver->department == $safety_riding->department && $approver->remark == 'chief-after'){
                    $ca_name = $approver->employee_name;
                    $ca_at = $approver->created_at;
                }
                if($approver->location == $safety_riding->location && $approver->period == $safety_riding->period && $approver->department == $safety_riding->department && $approver->remark == 'manager-after'){
                    $ma_name = $approver->employee_name;
                    $ma_at = $approver->created_at;
                }
            }

            array_push($results, [
                'period' => $safety_riding->period,
                'location' => $safety_riding->location,
                'department' => $safety_riding->department,
                'department_shortname' => $safety_riding->department_shortname,
                'total_emp' => $safety_riding->total_emp,
                'total_sr' => $safety_riding->total_sr,
                'maru' => $safety_riding->maru,
                'batsu' => $safety_riding->batsu,
                'total_date' => $safety_riding->total_date,
                'cb_name' => $cb_name,
                'cb_at' => $cb_at,
                'ca_name' => $ca_name,
                'ca_at' => $ca_at,
                'mb_name' => $mb_name,
                'mb_at' => $mb_at,
                'ma_name' => $ma_name,
                'ma_at' => $ma_at
            ]);
        }

        $data = [
            'safety_ridings' => $results,
            'mon' => date('m', strtotime($period)),
            'year' => date('Y', strtotime($period))
        ];

        $mail = [
            'ympi-chief-ML@music.yamaha.com',
            'ympi-staff-ML@music.yamaha.com',
            'ympi-manager-ML@music.yamaha.com'
        ];

        Mail::to($mail)
        ->send(new SendEmail($data, 'safety_riding'));
    }
}
