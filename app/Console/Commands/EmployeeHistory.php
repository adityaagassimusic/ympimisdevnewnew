<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EmployeeHistory extends Command
{
/**
* The name and signature of the console command.
*
* @var string
*/
protected $signature = 'employee:history';

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
    $date = date('Y-m-t');
    // $date = date('2021-09-30');
    $first = date('Y-m-01', strtotime($date));

    $next_month_first = date('Y-m-01', strtotime($date . "+1 days"));
    $next_month_end = date('Y-m-t', strtotime($date . "+1 days"));

    $hours = db::select("SELECT * 
        FROM
        efficiency_hours AS et 
        WHERE
        valid_date = '".$first."'");

    foreach($hours as $hour){
        DB::table('efficiency_hours')->insert([
            'valid_date' => $next_month_first,
            'jam' => $hour->jam,
            'shift' => $hour->shift,
            'location' => $hour->location,
            'category' => $hour->category,
            'remark' => $hour->remark,
            'created_by' => $hour->created_by,
            'deleted_at' => $hour->deleted_at,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    $targets = db::select("SELECT * 
        FROM
        efficiency_targets AS et 
        WHERE
        valid_date = '".$first."'");

    foreach($targets as $target){
        DB::table('efficiency_targets')->insert([
            'valid_date' => $next_month_first,
            'target' => $target->target,
            'location' => $target->location,
            'category' => $target->category,
            'remark' => $target->remark,
            'created_by' => $target->created_by,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    $weekly_calendars = db::select("SELECT
        week_date
        FROM
        weekly_calendars 
        WHERE
        week_date >= '".$next_month_first."' 
        AND week_date <= '".$next_month_end."'");

    $manpowers = db::select("SELECT
        employee_id,
        employee_name,
        location,
        category,
        remark,
        created_by 
        FROM
        efficiency_manpowers 
        WHERE
        valid_date = '".$date."'");

    $delete = db::table('efficiency_manpowers')->where('valid_date', '>=', $next_month_first)
    ->where('valid_date', '<=', $next_month_end)
    ->delete();

    foreach($weekly_calendars as $weekly_calendar){
        $valid_date = $weekly_calendar->week_date;
        foreach($manpowers as $manpower){
            DB::table('efficiency_manpowers')->insert([
                'valid_date' => $valid_date,
                'employee_id' => $manpower->employee_id,
                'employee_name' => $manpower->employee_name,
                'location' => $manpower->location,
                'category' => $manpower->category,
                'remark' => $manpower->remark,
                'created_by' => $manpower->created_by,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    $insert = array();
    $employees = db::connection('sunfish')->select("select * from dbo.VIEW_YMPI_Emp_OrgUnit");
    $datas = json_decode(json_encode($employees), true);

    foreach ($datas as $data) {
        $row = array();

        $row['period'] = date("Y-m-t");
        $row['Emp_no'] = $data['Emp_no'];
        $row['Full_name'] = $data['Full_name'];
        $row['grade_code'] = $data['grade_code'];
        $row['start_date'] = $data['start_date'];
        $row['end_date'] = $data['end_date'];
        $row['position_id'] = $data['position_id'];
        $row['dept_id'] = $data['dept_id'];
        $row['pos_name_en'] = $data['pos_name_en'];
        $row['pos_code'] = $data['pos_code'];
        $row['parent_path'] = $data['parent_path'];
        $row['BOD'] = $data['BOD'];
        $row['Division'] = $data['Division'];
        $row['Department'] = $data['Department'];
        $row['Section'] = $data['Section'];
        $row['Group'] = $data['Groups'];
        $row['Sub-Group'] = $data['Sub_Groups'];
        $row['status'] = $data['status'];
        $row['employ_code'] = $data['employ_code'];
        $row['photo'] = $data['photo'];
        $row['gender'] = $data['gender'];
        $row['birthplace'] = $data['birthplace'];
        $row['birthdate'] = $data['birthdate'];
        $row['address'] = $data['Current_Address'];
        $row['phone'] = $data['phone'];
        $row['identity_no'] = $data['identity_no'];
        $row['taxfilenumber'] = $data['taxfilenumber'];
        $row['JP'] = $data['JP'];
        $row['BPJS'] = $data['BPJS'];
        $row['cost_center_name'] = $data['cost_center_name'];
        $row['cost_center_code'] = $data['cost_center_code'];
        $row['gradecategory_name'] = $data['gradecategory_name'];
        $row['Penugasan'] = $data['Penugasan'];
        $row['Labour_Union'] = $data['Labour_Union'];
        $row['created_at'] = date('Y-m-d H:i:s');
        $row['updated_at'] = date('Y-m-d H:i:s');

        $insert[] = $row;
    }

    foreach (array_chunk($insert,1000) as $t)  
    {
        DB::table('employee_histories')->insert($t);
    }
}
}
