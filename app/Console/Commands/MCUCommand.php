<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\EmployeeSync;
use App\Department;
use App\Approver;

class MCUCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mcu:schedule';

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
        $periode = DB::select("SELECT DISTINCT
            ( fiscal_year ) 
        FROM
            weekly_calendars 
        WHERE
            week_date = DATE(
            NOW())");
        $over_schedule = DB::connection('ympimis_2')->SELECT("SELECT
                * 
            FROM
                `mcus` 
            WHERE
                doctor_status IS NULL 
                AND clinic_status IS NULL
                AND periode = '".$periode[0]->fiscal_year."'");

        $empsync = EmployeeSync::select('employee_syncs.*','departments.department_shortname')->where('end_date',null)->leftjoin('departments','departments.department_name','employee_syncs.department')->get();

        $dept_all = DB::connection('ympimis_2')->select("SELECT DISTINCT
                ( department ) 
            FROM
                `mcus`
                JOIN ympimis.employee_syncs ON ympimis.employee_syncs.employee_id = mcus.employee_id
                LEFT JOIN ympimis.departments ON ympimis.departments.department_name = ympimis.employee_syncs.employee_id 
            WHERE
                doctor_status IS NULL 
                AND clinic_status IS NULL
                AND periode = '".$periode[0]->fiscal_year."'
                AND department IS NOT NULL");
        
        if (count($over_schedule) > 0) {
            for ($k=0; $k < count($dept_all); $k++) { 
                $emp_dept = [];
                $email = Approver::where('remark','Manager')->where('department',$dept_all[$k]->department)->first();
                for ($i=0; $i < count($over_schedule); $i++) { 
                    for ($j=0; $j < count($empsync); $j++) { 
                        if ($over_schedule[$i]->employee_id == $empsync[$j]->employee_id) {
                            $dept = $empsync[$j]->department;
                            $dept_short = $empsync[$j]->department_shortname;
                            $sect = $empsync[$j]->section;
                            $group = $empsync[$j]->group;
                            $sub_group = $empsync[$j]->sub_group;
                        }
                    }
                    if ($dept == $dept_all[$k]->department) {
                        $emp_per_dept = array(
                            'employee_id' => $over_schedule[$i]->employee_id, 
                            'name' => $over_schedule[$i]->name,
                            'schedule_date' => $over_schedule[$i]->schedule_date,
                            'department' => $dept_short,
                            'section' => $sect,
                            'group' => $group,
                            'sub_group' => $sub_group,
                        );
                        array_push($emp_dept, $emp_per_dept);
                    }
                }
                $contactList = [];
                $contactList[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';

                $cc = [];
                $cc[0] = 'putri.sukma.riyanti@music.yamaha.com';
                Mail::to($email->approver_email)->cc($cc,'cc')
                ->bcc($contactList,'bcc')
                ->send(new SendEmail($emp_dept, 'cek_fisik_reminder'));
            }
        }
        $over_schedule_mcu = DB::connection('ympimis_2')->select("SELECT
                * 
            FROM
                mcus 
            WHERE
                periode = '".$periode[0]->fiscal_year."' 
                AND mcu_attendance_status IS NULL 
                AND schedule_date_mcu <= DATE(
                NOW()) 
                AND schedule_date_mcu != ''");


        if (count($over_schedule_mcu) > 0) {
            $dept = [];
            for ($i=0; $i < count($over_schedule_mcu); $i++) { 
                $emp = EmployeeSync::where('employee_id',$over_schedule_mcu[$i]->employee_id)->first();
                array_push($dept, $emp->department);
            }

            $dept_unik = array_values(array_unique($dept));
            for ($i=0; $i < count($dept_unik); $i++) { 
                $email = Approver::where('remark','Manager')->where('department',$emp->department)->first();
                
                $mail_to = [];

                array_push($mail_to, $email->approver_email);

                $emp_dept = [];

                for ($j=0; $j < count($over_schedule_mcu); $j++) { 
                    $emp = EmployeeSync::where('employee_syncs.employee_id',$over_schedule_mcu[$j]->employee_id)->join('departments','departments.department_name','employee_syncs.department')->first();
                    if ($emp->department == $dept_unik[$i]) {
                        $emp_per_dept = array(
                            'employee_id' => $over_schedule_mcu[$j]->employee_id, 
                            'name' => $over_schedule_mcu[$j]->name,
                            'schedule_date' => $over_schedule_mcu[$j]->schedule_date,
                            'department' => $emp->department_shortname,
                            'section' => $emp->section,
                            'group' => $emp->group,
                            'sub_group' => $emp->sub_group,
                        );
                        array_push($emp_dept, $emp_per_dept);
                    }
                }

                $cc = [];
                $cc[0] = 'putri.sukma.riyanti@music.yamaha.com';
                Mail::to($mail_to)->cc($cc,'cc')->bcc('mokhamad.khamdan.khabibi@music.yamaha.com','bcc')->send(new SendEmail($emp_dept, 'mcu_schedule_reminder'));
            }
        }
    }
}
