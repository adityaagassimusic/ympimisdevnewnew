<?php

namespace App\Console\Commands;

use App\Employee;
use App\EmployeeSync;
use App\MeetingDetail;
use App\User;
use App\WeeklyCalendar;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncSunfish extends Command
{
    protected $signature = 'sync:sunfish';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $insert = array();
        $datas = db::connection('sunfish')->select("SELECT DISTINCT
            Emp_no,
            Full_name,
            gender,
            birthplace,
            birthdate,
            Current_Address,
            phone,
            identity_no,
            taxfilenumber,
            BPJS,
            start_date,
            end_date,
            pos_name_en,
            pos_code,
            grade_code,
            gradecategory_name,
            Division,
            Department,
            Section,
            Groups,
            Sub_Groups,
            employ_code,
            cost_center_code,
            Penugasan,
            Labour_Union,
            NIK_Manager,
            Zona,
            job_status_code,
            Stuff(
            ( SELECT ', ' + JP FROM VIEW_YMPI_Emp_OrgUnit t2 WHERE t2.Emp_no = t1.Emp_no FOR XML PATH ( '' ) ),
            1,
            2,
            ''
            ) JP
            FROM
            VIEW_YMPI_Emp_OrgUnit t1
            WHERE
            Emp_no <> 'sunfish'");

        $datas2 = json_decode(json_encode($datas), true);

        foreach ($datas2 as $data) {
            if (array_search($data['Emp_no'], array_column($insert, 'employee_id')) != false) {
                continue;
            }

            // $positions = ucwords(trim($data['pos_name_en']));
            $position_new = $data['pos_name_en'];

            // if (str_contains(ucwords($data['pos_name_en']),ucwords(trim($data['Division'])))) {
            //     $positions = str_replace(ucwords(trim($data['Division'])), '', ucwords($data['pos_name_en']));
            // }
            // else if(str_contains(ucwords($data['pos_name_en']),ucwords(trim($data['Department'])))){
            //     $positions = str_replace(ucwords(trim($data['Department'])), '', ucwords($data['pos_name_en']));
            // }
            // else if(str_contains(ucwords($data['pos_name_en']),ucwords(trim($data['Section'])))){
            //     $positions = str_replace(ucwords(trim($data['Section'])), '', ucwords($data['pos_name_en']));
            // }
            // else if(str_contains(ucwords($data['pos_name_en']),ucwords(trim($data['Groups'])))){
            //     $positions = str_replace(ucwords(trim($data['Groups'])), '', ucwords($data['pos_name_en']));
            // }
            // else if(str_contains(ucwords($data['pos_name_en']),ucwords(trim($data['Sub_Groups'])))){
            //     $positions = str_replace(ucwords(trim($data['Sub_Groups'])), '', ucwords($data['pos_name_en']));
            //

            $position_new = urlencode(trim($position_new));
            $position_new = str_replace('%C2%A0', '+', $position_new);
            $position_new = urldecode($position_new);

            $positions = "";
            if (str_contains(ucwords($position_new), 'Sub Leader')) {
                $positions = 'Sub Leader';
            } else if (str_contains(ucwords($position_new), 'Leader')) {
                $positions = 'Leader';
            } else if (str_contains(ucwords($position_new), 'Senior Staff')) {
                $positions = 'Senior Staff';
            } else if (str_contains(ucwords($position_new), 'Staff')) {
                $positions = 'Staff';
            } else if (str_contains(ucwords($position_new), 'Operator Outsource')) {
                $positions = 'Operator Outsource';
            } else if (str_contains(ucwords($position_new), 'Operator Contract')) {
                $positions = 'Operator Contract';
            } else if (str_contains(ucwords($position_new), 'Senior Operator')) {
                $positions = 'Senior Operator';
            } else if (str_contains(ucwords($position_new), 'Operator')) {
                $positions = 'Operator';
            } else if (str_contains(ucwords($position_new), 'Senior Coordinator')) {
                $positions = 'Senior Coordinator';
            } else if (str_contains(ucwords($position_new), 'Coordinator')) {
                $positions = 'Coordinator';
            } else if (str_contains(ucwords($position_new), 'Junior Specialist')) {
                $positions = 'Junior Specialist';
            } else if (str_contains(ucwords($position_new), 'Senior Specialist')) {
                $positions = 'Senior Specialist';
            } else if (str_contains(ucwords($position_new), 'Specialist')) {
                $positions = 'Specialist';
            } else if (str_contains(ucwords($position_new), 'Senior Foreman')) {
                $positions = 'Senior Foreman';
            } else if (str_contains(ucwords($position_new), 'Deputy Foreman')) {
                $positions = 'Deputy Foreman';
            } else if (str_contains(ucwords($position_new), 'Foreman')) {
                $positions = 'Foreman';
            } else if (str_contains(ucwords($position_new), 'Senior Chief')) {
                $positions = 'Senior Chief';
            } else if (str_contains(ucwords($position_new), 'Deputy Chief')) {
                $positions = 'Deputy Chief';
            } else if (str_contains(ucwords($position_new), 'Chief')) {
                $positions = 'Chief';
            } else if (str_contains(ucwords($position_new), 'Deputy General Manager')) {
                $positions = 'Deputy General Manager';
            } else if (str_contains(ucwords($position_new), 'General Manager')) {
                $positions = 'General Manager';
            } else if (str_contains(ucwords($position_new), 'Asst. Manager')) {
                $positions = 'Asst. Manager';
            } else if (str_contains(ucwords($position_new), 'Assistant Manager')) {
                $positions = 'Assistant Manager';
            } else if (str_contains(ucwords($position_new), 'Manager')) {
                $positions = 'Manager';
            } else if (str_contains(ucwords($position_new), 'President Director')) {
                $positions = 'President Director';
            } else if (str_contains(ucwords($position_new), 'Vice President')) {
                $positions = 'Vice President';
            } else if (str_contains(ucwords($position_new), 'Director')) {
                $positions = 'Director';
            }

            if (str_contains($data['Emp_no'], 'PI')) {
                $row = array();

                $name = preg_replace('/\xc2\xa0/', ' ', $data['Full_name']);

                $row['employee_id'] = $data['Emp_no'];
                $row['name'] = $name;
                $row['gender'] = $data['gender'];
                $row['birth_place'] = $data['birthplace'];
                $row['birth_date'] = $data['birthdate'];
                $row['address'] = $data['Current_Address'];
                $row['phone'] = $data['phone'];
                $row['card_id'] = $data['identity_no'];
                $row['npwp'] = $data['taxfilenumber'];
                $row['JP'] = $data['JP'];
                $row['BPJS'] = $data['BPJS'];
                $row['hire_date'] = $data['start_date'];
                $row['end_date'] = $data['end_date'];
                // $row['position'] = $data['pos_name_en'];
                $row['position'] = $positions;
                $row['position_new'] = $position_new;
                $row['position_code'] = $data['pos_code'];
                $row['grade_code'] = $data['grade_code'];
                $row['grade_name'] = $data['gradecategory_name'];
                $row['division'] = $data['Division'];
                $row['department'] = $data['Department'];
                $row['section'] = $data['Section'];
                $row['group'] = $data['Groups'];
                $row['sub_group'] = $data['Sub_Groups'];
                $row['employment_status'] = $data['employ_code'];
                $row['cost_center'] = $data['cost_center_code'];
                $row['assignment'] = $data['Penugasan'];
                $row['union'] = $data['Labour_Union'];
                $row['created_at'] = date('Y-m-d H:i:s');
                $row['updated_at'] = date('Y-m-d H:i:s');
                $row['nik_manager'] = $data['NIK_Manager'];
                $row['zona'] = $data['Zona'];
                $row['job_status'] = $data['job_status_code'];

                $insert[] = $row;
            }

        }

        DB::table('employee_syncs')->where('employee_id', 'LIKE', 'PI%')->delete();
        DB::table('miraimobile.employee_syncs')->where('employee_id', 'LIKE', 'PI%')->delete();

        foreach (array_chunk($insert, 1000) as $t) {
            DB::table('employee_syncs')->insert($t);
            DB::table('miraimobile.employee_syncs')->insert($t);
        }

        $users = DB::table('users')->get();
        $users_ympicoid = DB::table('miraimobile.users')->get();

        $usernames = array();
        $usernames_ympicoid = array();

        foreach ($users as $user) {
            array_push($usernames, strtoupper($user->username));
        }

        foreach ($users_ympicoid as $user_ympicoid) {
            array_push($usernames_ympicoid, strtoupper($user_ympicoid->username));
        }

        foreach ($insert as $data) {
            if (!in_array($data['employee_id'], $usernames)) {
                $insert_user = new User([
                    'name' => ucwords($data['name']),
                    'email' => strtoupper($data['employee_id']) . '@gmail.com',
                    'password' => bcrypt('123456'),
                    'username' => strtoupper($data['employee_id']),
                    'role_code' => 'emp-srv',
                    'avatar' => strtoupper($data['employee_id'] . 'jpg'),
                    'created_by' => '1',
                ]);
                $insert_user->save();
            } else {
                if ($data['end_date'] <= date('Y-m-d')) {
                    $update_user = User::where('username', '=', $data['employee_id'])->update([
                        'deleted_at' => $data['end_date'],
                    ]);
                }
            }

            if (!in_array($data['employee_id'], $usernames_ympicoid)) {
                $insert_user_ympicoid = DB::table('miraimobile.users')->insert([
                    'name' => ucwords($data['name']),
                    'email' => strtoupper($data['employee_id']) . '@gmail.com',
                    'password' => bcrypt('123456'),
                    'username' => strtoupper($data['employee_id']),
                    'role_code' => 'user',
                    'status_ganti' => '',
                    'token_ganti' => '',
                    'otp' => '',
                    'avatar' => strtoupper($data['employee_id'] . 'jpg'),
                    'created_by' => '1',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                if ($data['end_date']) {
                    $update_user_ympicoid = DB::table('miraimobile.users')
                        ->where('username', '=', $data['employee_id'])->update([
                        'deleted_at' => $data['end_date'],
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        $update_user_otp = DB::table('miraimobile.users')->update([
            'otp' => '',
        ]);

        $employees = DB::table('employees')->get();
        $employee_ids = array();
        foreach ($employees as $employee) {
            array_push($employee_ids, strtoupper($employee->employee_id));
        }

        foreach ($insert as $data) {
            if (!in_array($data['employee_id'], $employee_ids)) {
                $insert_employee = new Employee([
                    'employee_id' => strtoupper($data['employee_id']),
                    'name' => ucwords($data['name']),
                    'gender' => strtoupper($data['gender']),
                    'birth_place' => ucwords($data['birth_place']),
                    'birth_date' => $data['birth_date'],
                    'address' => ucwords($data['address']),
                    'card_id' => $data['card_id'],
                    'hire_date' => $data['hire_date'],
                    'created_by' => '1',
                ]);
                $insert_employee->save();
            } else {
                if (!is_null($data['end_date'])) {
                    $update_employee = Employee::where('employee_id', $data['employee_id'])
                        ->update([
                            'end_date' => $data['end_date'],
                        ]);
                }

                $update_name = Employee::where('employee_id', $data['employee_id'])
                    ->update([
                        'name' => ucwords($data['name']),
                    ]);
            }
        }

        $meetings = MeetingDetail::where('meeting_id', '109')
            ->update([
                'status' => 0,
                'attend_time' => null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        DB::table('plan_employee_contracts')->truncate();
        $employments = db::connection('sunfish')->select("SELECT emp_no, employment_startdate, employment_enddate, end_date FROM TEODEMPCOMPANY WHERE emp_no like '%PI%'");
        foreach ($employments as $employment) {
            $plan_employee = db::table('plan_employee_contracts')
                ->insert([
                    'employee_id' => $employment->emp_no,
                    'hire_date' => $employment->employment_startdate,
                    'end_date' => $employment->end_date,
                    'planing_end_date' => $employment->employment_enddate,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }

        $now = WeeklyCalendar::where('week_date', date('Y-m-d'))->first();
        if ($now->remark != 'H') {

            $mis_member = EmployeeSync::where('department', 'LIKE', '%info%')
                ->whereNull('end_date')
                ->get();

            for ($i = 0; $i < count($mis_member); $i++) {

                $rand = date('Y-m-d') . ' 06:' . rand(45, 53) . ':' . rand(1, 59);
                $user = User::where('username', $mis_member[$i]->employee_id)->first();
                $insert = DB::table('general_attendance_logs')
                    ->insert([
                        'purpose_code' => 'Oxymeter',
                        'due_date' => date('Y-m-d'),
                        'employee_id' => $mis_member[$i]->employee_id,
                        'attend_date' => $rand,
                        'remark' => rand(97, 99),
                        'remark2' => rand(90, 100),
                        'created_by' => $user->id,
                        'created_at' => $rand,
                        'updated_at' => $rand,
                    ]);

            }

        }

        $updateAssy = db::table('assemblies')->update([
            'cycle' => 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

}
