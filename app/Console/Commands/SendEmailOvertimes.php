<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Carbon\Carbon;
use Artisan;

class SendEmailOvertimes extends Command
{
/**
* The name and signature of the console command.
*
* @var string
*/
protected $signature = 'email:overtime';

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

    $mail_to = ['ympi-manager-ML@music.yamaha.com', 'ympi-chief-ML@music.yamaha.com'];
    $bcc_to = ['ympi-mis-ML@music.yamaha.com'];

    $first = date('Y-m-01');
    $now = date('Y-m-d');
    $mon = date('Y-m');
    if($now == $first){
        $first = date('Y-m-d', strtotime(Carbon::now()->subMonth(1)));
        $now = date('Y-m-d', strtotime(Carbon::now()->subDays(1)));
        $mon = date('Y-m', strtotime(Carbon::now()->subDays(1)));
    }

    $overtimes = db::connection('sunfish')->select("SELECT
        ot.emp_no AS nik,
        ot.jam 
        FROM
        (
            SELECT
            ot.emp_no,
            SUM (
                CASE
                
                WHEN ot.total_ot > 0 THEN
                floor( ( ot.total_ot / 60.0 ) * 2 + 0.5 ) / 2 ELSE floor( ( ot.TOTAL_OVT_PLAN / 60.0 ) * 2 + 0.5 ) / 2 
                END 
                ) AS jam 
            FROM
            VIEW_YMPI_Emp_OvertimePlan AS ot 
            WHERE
            ot.emp_no <> 'SUNFISH' 
            AND ot.ovtplanfrom >= '".$first." 00:00:00' 
            AND ot.ovtplanfrom <= '".$now." 23:59:59' 
            GROUP BY
            ot.emp_no 
            ) AS ot 
            WHERE
            ot.jam > 0 
            ORDER BY
            ot.jam DESC");

        $employee_syncs = db::table('employee_syncs')->leftJoin('departments', 'departments.department_name', '=', 'employee_syncs.department')->get();

        $datas = array();

        foreach($overtimes as $overtime){
            $period = $mon;
            $employee_id = $overtime->nik;
            $name = "";
            $department_shortname = "";
            $position = "";
            $ot_hour = $overtime->jam;

            foreach($employee_syncs as $employee){
                if($employee->employee_id == $employee_id){     
                    $name = $employee->name;
                    $department_shortname = $employee->department_shortname;
                    $position = $employee->position;
                    break;
                }
            }

            array_push($datas, [
                'period' => $period,
                'employee_id' => $employee_id,
                'name' => $name,
                'department_shortname' => $department_shortname,
                'position' => $position,
                'ot_hour' => $ot_hour,
            ]);
        }

        $ofc_1 = db::table('office_members')->get();

        $ofc = array();
        $drv = array();
        foreach ($ofc_1 as $of) {
            if($of->remark == 'office'){
                array_push($ofc, $of->employee_id);
            }
            if($of->remark == 'driver' || $of->remark == 'security'){
                array_push($drv, $of->employee_id);
            }
        }

        $offices = array();
        $productions = array();
        $c_ofc = 1;
        $c_prd = 1;

        foreach ($datas as $data) {
            if(in_array($data['employee_id'], $ofc) && $c_ofc <= 20){
                array_push($offices, [
                    'period' => $mon,
                    'department' => strtoupper($data['department_shortname']),
                    'grade' => ucwords($data['position']),
                    'employee_id' => strtoupper($data['employee_id']),
                    'name' => ucwords($data['name']),
                    'overtime' => $data['ot_hour']
                ]);
                $c_ofc += 1;
            }
            else if(!in_array($data['employee_id'], $drv) && $c_prd <= 20){
                array_push($productions, [
                    'period' => $mon,
                    'department' => strtoupper($data['department_shortname']),
                    'grade' => ucwords($data['position']),
                    'employee_id' => strtoupper($data['employee_id']),
                    'name' => ucwords($data['name']),
                    'overtime' => $data['ot_hour']
                ]);
                $c_prd += 1;
            }
            if($c_ofc == 20 && $c_prd == 20){
                break;
            }
        }

        $end_date = date('Y-m-d',strtotime("-1 days")); 

        $start_date = date('Y-m-01');

        $count_day = [];

        $limit = 100;

        $overtimes = [
            'offices' => $offices,
            'productions' => $productions,
            'first' => $first,
            'limit' => $limit
        ];

        if($datas != null){
            Mail::to($mail_to)->bcc($bcc_to)->send(new SendEmail($overtimes, 'overtime'));
        }
    }
}
