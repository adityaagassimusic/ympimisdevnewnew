<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Response;
use File;
use PDF;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\UangPekerjaan;
use App\UangSimpati;
use App\UangKeluarga;
use App\EmployeeSync;
use App\Employee;
use App\Recruitment;
use App\CodeGenerator;
use App\Approver;
use App\Driver;
use App\User;
use App\DriverList;
use App\DriverDetail;
use App\DriverLog;
use App\RecruitmentApproval;
use App\WeeklyCalendar;
use App\HrLeaveRequest;
use App\HrLeaveRequestApproval;
use App\HrLeaveRequestDetail;
use App\CalonKaryawan;
use App\CalonRekontrak;
use App\VeteranEmployee;
use App\VeteranDataMaster;
use App\VeteranRequestEmployee;
use App\RequestAdds;
use App\EmployeeUpdate;
use App\PlanEmployeeContract;
use App\RequestMagang;
use App\RequestMagangApproval;
use App\HrApproval;
use iio\libmergepdf\Merger;
use iio\libmergepdf\Driver\TcpdiDriver;
use PDFMerger;
use iio\libmergepdf\Driver\DriverInterface;
use iio\libmergepdf\Source\FileSource;
use iio\libmergepdf\Source\RawSource;
use App\AccItem;
use Illuminate\Support\Facades\Validator;

class HumanResourceController extends Controller
{
    private $position;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->position = [
            'Operator',
            // 'Sub Leader',
            // 'Leader',
            'Staff',
            // 'Senior Staff',
            // 'Chief',
            // 'Foreman',
            // 'Manager'
        ];
        $this->leave_status = [
            'Requested',
            'Rejected',
            'Partially Approved',
            'Fully Approved',
        ];
        $this->category = [
            'Dangerous/Chemical Cont',
            'Dust',
            'Hard',
            'Heat',
            'Noizy'
        ];
        $departments = db::table('departments')->orderBy('department_name', 'ASC')->get();
    }

    public function getNotifLeaveRequest()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $notif = 0;

            $tanggungan = db::select("
                SELECT DISTINCT
                hr_leave_requests.request_id 
                FROM
                hr_leave_requests
                JOIN hr_leave_request_approvals ON hr_leave_requests.request_id = hr_leave_request_approvals.request_id 
                WHERE
                CONCAT( hr_leave_request_approvals.`status` ) IS NULL 
                AND hr_leave_requests.remark != 'Rejected'
                ");

            $leave_request_id = [];
            foreach($tanggungan as $tag){
                array_push($leave_request_id, $tag->request_id);
            }

        // dd($ticket);

            $jumlah_tanggungan = 0;

            for ($i=0; $i < count($leave_request_id); $i++) { 
            // var_dump($ticket[$i]);
                $tanggungan_user = db::select("
                    SELECT
                    ( SELECT approver_id FROM hr_leave_request_approvals a WHERE a.id = ( hr_leave_request_approvals.id ) ) next 
                    FROM
                    hr_leave_request_approvals 
                    WHERE
                    `status` IS NULL 
                    AND request_id = '".$leave_request_id[$i]."' 
                    ORDER BY
                    id ASC 
                    LIMIT 1
                    ");

                if (count($tanggungan_user) > 0 && $tanggungan_user[0]->next == $user) {
                    $jumlah_tanggungan += 1;
                }
            }

        // if (count($tanggungan_user) > 0) {
            $notif = $jumlah_tanggungan;
        // }

            return $notif;
        }
    }

    public function indexRequestManpower(Request $request){
        $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id','department')
        ->first();

        $departments = db::table('departments')->orderBy('department_name', 'ASC')->get();
        $jml_reqs1 = db::select('SELECT count(*) as jumlah from recruitments where position = "Staff"');
        $jml_reqs2 = db::select('SELECT count(*) as jumlah from recruitments where position = "Operator"');
        $employee = db::select('SELECT old_nik, `name` FROM veteran_data_masters');
        $section = db::select('select distinct sub_group from employee_syncs where department = "'.$emp_dept->department.'"');

        $date = date("Y-m-d", strtotime('+3 weeks'));

        // var_dump($date);
        // die();

        return view('human_resource.recruitment.request_manpower', array(
            'title' => 'Form Request Manpower', 
            'title_jp' => 'フォームリクエストのマンパワー',
            'positions' => $this->position,
            'departments' => $departments,
            'jml_reqs1' => $jml_reqs1,
            'jml_reqs2' => $jml_reqs2,
            'emp_dept' => $emp_dept,
            'employee' => $employee,
            'sections' => $section,
            'date' => $date
        ))->with('page', 'Human Resource');
    }

    public function inputRequestManpower(Request $request){
        try{
            $code_generator = CodeGenerator::where('note','=','recruitment')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
            $request_id = $code_generator->prefix . $number;
            $code_generator->index = $code_generator->index+1;
            $code_generator->save();

            $employee = $request->get('employee');
            $department = $request->get('department');
            $section = $request->get('section');
            $group = $request->get('group');
            $sub_group = $request->get('sub_group');
            $datein = $request->get('datein');

            for($i = 0; $i < count($employee) ; $i++){
                $karyawan = new CalonRekontrak([
                    'request_id'  => $request_id, 
                    'nik'         => '0',
                    'nama'        => $employee[$i],
                    'section' => $section[$i],
                    'group' => $group[$i],
                    'sub_group' => $sub_group[$i]
                ]);
                $karyawan->save();
            }

            $recruitment = New Recruitment([
                'request_id' => $request_id,
                'position' => 'Operator',
                'department' => $department,
                'employment_status' => 'Request Karyawan Rekontrak',
                'quantity_male' => count($employee),
                'start_date' => $datein,
                'remark' => 'Recruitment HR',
                'created_by' => Auth::id(),
                'status_at' => 'Process Approval',
                'status_req' => 'Request Karyawan Rekontrak',
            ]);

            $m = Approver::where('department', '=', $request->get('department'))
            ->where('remark', '=', 'Manager')
            ->first();

            $dgm = Approver::where('department', '=', $request->get('department'))
            ->where('remark', '=', 'Deputy General Manager')
            ->first();

            $gm = Approver::where('department', '=', $request->get('department'))
            ->where('remark', '=', 'General Manager')
            ->first();

            $pd = Approver::where('remark', '=', 'President Director')
            ->first();

            $d = Approver::where('department', '=', 'Human Resources Department')
            ->where('remark', '=', 'Director')
            ->first();

            $mhr = Approver::where('department', '=', 'Human Resources Department')
            ->where('remark', '=', 'Manager')
            ->first();

            if($m != null){
                $manager = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => $m->approver_id,
                    'approver_name' => $m->approver_name,
                    'approver_email' => $m->approver_email,
                    'remark' => 'Manager',
                    'project_name' => 'Req MP'
                ]);
            }
            else{
                $manager = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => null,
                    'approver_name' => "",
                    'approver_email' => "",
                    'status' => "none",
                    'approved_at' => date('Y-m-d H:i:s'),
                    'remark' => 'Manager',
                    'project_name' => 'Req MP'
                ]);
            }

            if($dgm != null){
                $deputy_general_manager = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => $dgm->approver_id,
                    'approver_name' => $dgm->approver_name,
                    'approver_email' => $dgm->approver_email,
                    'remark' => 'DGM',
                    'project_name' => 'Req MP'
                ]);
            }
            else{
                $deputy_general_manager = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => null,
                    'approver_name' => "",
                    'approver_email' => "",
                    'status' => "none",
                    'approved_at' => date('Y-m-d H:i:s'),
                    'remark' => 'DGM',
                    'project_name' => 'Req MP'
                ]);
            }

            if($gm != null){
                $general_manager = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => $gm->approver_id,
                    'approver_name' => $gm->approver_name,
                    'approver_email' => $gm->approver_email,
                    'remark' => 'GM',
                    'project_name' => 'Req MP'
                ]);
            }
            else{
                $general_manager = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => null,
                    'approver_name' => "",
                    'approver_email' => "",
                    'status' => 'none',
                    'approved_at' => date('Y-m-d H:i:s'),
                    'remark' => 'GM',
                    'project_name' => 'Req MP'
                ]);
            }

            if($pd != null){
                $president_director = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => $pd->approver_id,
                    'approver_name' => $pd->approver_name,
                    'approver_email' => $pd->approver_email,
                    'remark' => 'Presiden Direktur',
                    'project_name' => 'Req MP'
                ]);
            }
            else{
                $president_director = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => null,
                    'approver_name' => "",
                    'approver_email' => "",
                    'status' => "none",
                    'approved_at' => date('Y-m-d H:i:s'),
                    'remark' => 'Presiden Direktur',
                    'project_name' => 'Req MP'
                ]);
            }

            if($d != null){
                $director = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => $d->approver_id,
                    'approver_name' => $d->approver_name,
                    'approver_email' => $d->approver_email,
                    'remark' => 'Direktur HR',
                    'project_name' => 'Req MP'
                ]);
            }
            else{
                $director = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => null,
                    'approver_name' => "",
                    'approver_email' => "",
                    'status' => "none",
                    'approved_at' => date('Y-m-d H:i:s'),
                    'remark' => 'Direktur HR',
                    'project_name' => 'Req MP'
                ]);
            }

            if($mhr != null){
                $manager_hr = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => $mhr->approver_id,
                    'approver_name' => $mhr->approver_name,
                    'approver_email' => $mhr->approver_email,
                    'remark' => 'Manager HR',
                    'project_name' => 'Req MP'
                ]);
            }
            else{
                $manager_hr = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => null,
                    'approver_name' => "",
                    'approver_email' => "",
                    'status' => "none",
                    'approved_at' => date('Y-m-d H:i:s'),
                    'remark' => 'Manager HR',
                    'project_name' => 'Req MP'
                ]);
            }

            $manager->save();
            $deputy_general_manager->save();
            $general_manager->save();
            if ($request->get('position') == 'Staff') {
                $president_director->save();
            }
            $director->save();
            $manager_hr->save();
            $recruitment->save();

            $response = array(
                'status' => true,
                'message' => 'Request manpower berhasil diajukan.',
            );
            return Response::json($response);


            dd('sukses');

            if($request->get('position') == 'Operator'){
                $employment_status = 'Kontrak';
            }
            else{
                $employment_status = 'Percobaan';
            }
            
            $status = '';
            if ($request->get('new_employee') == null) {
                $status = 'Request Karyawan Rekontrak';
                $emp = $request->get('employee');

                for($i = 0; $i < count($emp) ; $i++){
                    $nama = explode('/',$emp[$i]);

                    $karyawan = new CalonRekontrak([
                        'request_id'  => $request_id, 
                        'nik'        => $nama[0],
                        'nama'        => $nama[1]
                    ]);
                    $karyawan->save();
                }
            }else{
                $status = 'Request Karyawan Baru';
            }

            $recruitment = New Recruitment([
                'request_id' => $request_id,
                'position' => $request->get('position'),
                'department' => $request->get('department'),
                'employment_status' => $employment_status,
                'quantity_male' => $request->get('quantity_male'),
                'quantity_female' => $request->get('quantity_female'),
                'reason' => $request->get('reason'),
                'start_date' => $request->get('start_date'),
                'min_age' => $request->get('min_age'),
                'max_age' => $request->get('max_age'),
                'marriage_status' => $request->get('marriage_status'),
                'domicile' => $request->get('domicile'),
                'work_experience' => $request->get('work_experience'),
                'skill' => $request->get('skill'),
                'educational_level' => $request->get('educational_level'),
                'major' => $request->get('major'),
                'requirement' => $request->get('requirement'),
                'note' => $request->get('note'),
                'progress' => $request->get('progress'),
                'remark' => 'Recruitment HR',
                'created_by' => Auth::id(),
                'status_at' => 'Process Approval',
                'status_req' => $status,
                'section' => $request->get('create_section'),
                'group' => $request->get('create_group'),
                'sub_group' => $request->get('create_sub_group')
            ]);

            $m = Approver::where('department', '=', $request->get('department'))
            ->where('remark', '=', 'Manager')
            ->first();

            // $hr = Approver::where('department', '=', 'Human Resources Department')
            // ->where('remark', '=', 'Director')
            // ->first();

            $dgm = Approver::where('department', '=', $request->get('department'))
            ->where('remark', '=', 'Deputy General Manager')
            ->first();

            $gm = Approver::where('department', '=', $request->get('department'))
            ->where('remark', '=', 'General Manager')
            ->first();

            $pd = Approver::where('remark', '=', 'President Director')
            ->first();

            $d = Approver::where('department', '=', 'Human Resources Department')
            ->where('remark', '=', 'Director')
            ->first();

            $mhr = Approver::where('department', '=', 'Human Resources Department')
            ->where('remark', '=', 'Manager')
            ->first();

            if($m != null){
                $manager = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => $m->approver_id,
                    'approver_name' => $m->approver_name,
                    'approver_email' => $m->approver_email,
                    'remark' => 'Manager'
                ]);
            }
            else{
                $manager = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => null,
                    'approver_name' => "",
                    'approver_email' => "",
                    'status' => "none",
                    'approved_at' => date('Y-m-d H:i:s'),
                    'remark' => 'Manager'
                ]);
            }

            // if ($request->get('department') == 'Human Resources Department' || $request->get('department') == 'General Affairs Department') {
            //     $director_hr = New RecruitmentApproval([
            //         'request_id' => $request_id,
            //         'approver_id' => $hr->approver_id,
            //         'approver_name' => $hr->approver_name,
            //         'approver_email' => $hr->approver_email,
            //         'remark' => 'Direktur HR'
            //     ]);
            // }

            if($dgm != null){
                $deputy_general_manager = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => $dgm->approver_id,
                    'approver_name' => $dgm->approver_name,
                    'approver_email' => $dgm->approver_email,
                    'remark' => 'DGM'
                ]);
            }
            else{
                $deputy_general_manager = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => null,
                    'approver_name' => "",
                    'approver_email' => "",
                    'status' => "none",
                    'approved_at' => date('Y-m-d H:i:s'),
                    'remark' => 'DGM'
                ]);
            }

            if($gm != null){
                $general_manager = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => $gm->approver_id,
                    'approver_name' => $gm->approver_name,
                    'approver_email' => $gm->approver_email,
                    'remark' => 'GM'
                ]);
            }
            else{
                $general_manager = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => null,
                    'approver_name' => "",
                    'approver_email' => "",
                    'status' => 'none',
                    'approved_at' => date('Y-m-d H:i:s'),
                    'remark' => 'GM'
                ]);
            }

            if($pd != null){
                $president_director = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => $pd->approver_id,
                    'approver_name' => $pd->approver_name,
                    'approver_email' => $pd->approver_email,
                    'remark' => 'Presiden Direktur'
                ]);
            }
            else{
                $president_director = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => null,
                    'approver_name' => "",
                    'approver_email' => "",
                    'status' => "none",
                    'approved_at' => date('Y-m-d H:i:s'),
                    'remark' => 'Presiden Direktur'
                ]);
            }

            if($d != null){
                $director = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => $d->approver_id,
                    'approver_name' => $d->approver_name,
                    'approver_email' => $d->approver_email,
                    'remark' => 'Direktur HR'
                ]);
            }
            else{
                $director = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => null,
                    'approver_name' => "",
                    'approver_email' => "",
                    'status' => "none",
                    'approved_at' => date('Y-m-d H:i:s'),
                    'remark' => 'Direktur HR'
                ]);
            }

            if($mhr != null){
                $manager_hr = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => $mhr->approver_id,
                    'approver_name' => $mhr->approver_name,
                    'approver_email' => $mhr->approver_email,
                    'remark' => 'Manager HR'
                ]);
            }
            else{
                $manager_hr = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => null,
                    'approver_name' => "",
                    'approver_email' => "",
                    'status' => "none",
                    'approved_at' => date('Y-m-d H:i:s'),
                    'remark' => 'Manager HR'
                ]);
            }

            // $emp = $request->get('employee');
            // $group = $request->get('group');
            // $sub_group = $request->get('sub_group');
            // $penempatan = $request->get('penempatan');
            // $prc_penempatan = $request->get('prc_penempatan');
            // $durasi = $request->get('durasi');

            // for($i = 0; $i < count($emp) ; $i++){
            //     $nama = explode('/',$emp[$i]);

            //     $karyawan = new CalonRekontrak([
            //     'request_id'  => $request_id, 
            //     'nik'        => $nama[0],
            //     'nama'        => $nama[1],
            //     'group' => $group[$i],
            //     'sub_group' => $sub_group[$i],
            //     'penempatan' => $penempatan[$i],
            //     'process_penempatan' => $prc_penempatan[$i],
            //     'durasi' => $durasi[$i]
            //     ]);
            //     $karyawan->save();
            // }

            $code_generator->index = $code_generator->index+1;
            $manager->save();
            // $director_hr->save();
            $deputy_general_manager->save();
            $general_manager->save();
            if ($request->get('position') == 'Staff') {
                $president_director->save();
            }
            $director->save();
            $manager_hr->save();
            
            
            

            $mail_to = [];
            $mail_cc = [];

            array_push($mail_to, $manager->approver_email);

            $recruitment = Recruitment::where('request_id', '=', $request_id)->first();

            $data = db::select("select r.id, request_id, r.position, r.department, r.employment_status, quantity_male, quantity_female, reason, DATE_FORMAT(start_date, '%d-%m-%Y') as start_date, min_age, max_age, marriage_status, domicile, work_experience, skill, educational_level, r.major, requirement, note, progress, remark, e.name, es.employee_id, status_req, 'process_penempatan', r.reason_reject, r.posisi, r.comment_note, r.reply from recruitments as r left join users as e on r.created_by = e.id left join employee_syncs as es on e.username = es.employee_id where request_id = '".$request_id."'");

            $app_email = RecruitmentApproval::
            where('request_id', '=', $request_id)
            ->wherenull('status')
            ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
            ->first();

            $app_progress = RecruitmentApproval::where('request_id', '=', $request_id)
            ->whereNotNull('approver_id')
            ->get();

            $data = [
                'data' => $data,
                'app_email' => $app_email,
                'app_progress' => $app_progress
            ];

            // $isi = Recruitment::where('request_id', $request_id)
            // ->select('request_id', 'position', 'department', 'create_section', 'create_group', 'create_sub_group', 'employment_status', 'quantity_male', 'quantity_female', 'reason', 'start_date', 'min_age', 'max_age', 'marriage_status', 'recruitments.major', 'status_at', 'status_req', 'recruitments.created_at', 'users.name')
            // ->leftJoin('users', 'users.id', '=', 'recruitments.created_by')
            // ->first();

            // $pdf = \App::make('dompdf.wrapper');
            // $pdf->getDomPDF()->set_option("enable_php", true);
            // $pdf->setPaper('A4', 'potrait');
            // $pdf->loadView('human_resource.recruitment.report_pdf', array(
            //     'isi' => $isi
            // ));
            // $pdf->save(public_path() . "/smart_recruitment/HR-".$request_id.".pdf");

            // return view('human_resource.recruitment.mail_request_manpower_approval')->with('data', $data);

            Mail::to($mail_to)
            ->bcc(['ympi-mis-ML@music.yamaha.com'])
            ->send(new SendEmail($data, 'request_manpower_approval'));

            // $response = array(
            //     'status' => true,
            //     'message' => 'Request manpower berhasil diajukan.',
            // );
            // return Response::json($response);
        }
        catch(\Exception $e){
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }        
    }

    public function RequestMagang(Request $request){
     try{
        $code_generator = CodeGenerator::where('note','=','magang')->first();
        $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
        $request_id = $code_generator->prefix . $number;
        $code_generator->index = $code_generator->index+1;

        $request_magang = New RequestMagang([
            'request_id' => $request_id,
            'position' => $request->get('position'),
            'department' => $request->get('department'),
            'section' => $request->get('section'),
            'group' => $request->get('group'),
            'sub_group' => $request->get('sub_group'),
            'qty_male' => $request->get('qty_male'),
            'qty_female' => $request->get('qty_female'),
            'hire_date' => $request->get('hire_date'),
            'end_date' => $request->get('end_date'),
            'remark' => $request->get('remark'),
            'created_by' => Auth::id(),
            'status' => 'Proses Approval'
        ]);

        $f = User::where('username', Auth::user()->username)
        ->select('name', 'username', 'email')
        ->first();

        $foreman = New RequestMagangApproval([
            'request_id' => $request_id,
            'approver_id' => strtoupper($f->username),
            'approver_name' => $f->name,
            'approver_email' => $f->email,
            'status' => 'Approved',
            'approved_at' => date('Y-m-d H:i:s'),
            'remark' => 'Foreman'
        ]);


        $m = Approver::where('department', '=', $request->get('department'))
        ->where('remark', '=', 'Manager')
        ->first();

        if($m != null){
            $manager = New RequestMagangApproval([
                'request_id' => $request_id,
                'approver_id' => $m->approver_id,
                'approver_name' => $m->approver_name,
                'approver_email' => $m->approver_email,
                'remark' => 'Manager'
            ]);
        }
        else{
            $manager = New RequestMagangApproval([
                'request_id' => $request_id,
                'approver_id' => null,
                'approver_name' => "",
                'approver_email' => "",
                'status' => "none",
                'approved_at' => date('Y-m-d H:i:s'),
                'remark' => 'Manager'
            ]);
        }

        $m_hr = new RequestMagangApproval([
            'request_id' => $request_id,
            'approver_id' => 'PI9906002',
            'approver_name' => 'Khoirul Umam',
            'approver_email' => 'khoirul.umam@music.yamaha.com',
            'remark' => 'Manager HR'
        ]);

        $hr_staff = new RequestMagangApproval([
            'request_id' => $request_id,
            'approver_id' => 'PI1906001',
            'approver_name' => 'Linda Rahmadhani Febrian',
            'approver_email' => 'linda.febrian@music.yamaha.com',
            'remark' => 'Staff HR'
        ]);

        $code_generator->save();
        $foreman->save();
        $manager->save();
        $m_hr->save();
        $hr_staff->save();
        $request_magang->save();

        $mail_to = [];
        $mail_cc = [];

        array_push($mail_to, $manager->approver_email);

        $request_magang = RequestMagang::where('request_id', '=', $request_id)->first();

        $data = db::select("select rm.id, request_id, rm.position, rm.department, rm.section, rm.`group`, rm.sub_group, qty_male, qty_female, rm.hire_date, rm.end_date, rm.remark, u.`name` as created_by, es.employee_id from request_magangs as rm left join users as u on rm.created_by = u.id left join employee_syncs as es on u.username = es.employee_id where request_id = '".$request_id."'");

        $approver = RequestMagangApproval::
        where('request_id', '=', $request_id)
        ->wherenull('status')
        ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
        ->first();

        $approver_progress = RequestMagangApproval::where('request_id', '=', $request_id)
        ->whereNotNull('approver_id')
        ->get();

        $data = [
            'data' => $data,
            'approver' => $approver,
            'approver_progress' => $approver_progress
        ];

            // $isi = RequestMagang::where('request_id', $request_id)
            // ->select('request_id', 'department', 'section', 'group', 'sub_group', 'qty_male', 'qty_female', 'hire_date', 'end_date', 'users.name')
            // ->leftJoin('users', 'users.id', '=', 'request_magangs.created_by')
            // ->first();

            // $pdf = \App::make('dompdf.wrapper');
            // $pdf->getDomPDF()->set_option("enable_php", true);
            // $pdf->setPaper('A4', 'potrait');
            // $pdf->loadView('human_resource.magang.report_pdf', array(
            //     'isi' => $isi
            // ));
            // $pdf->save(public_path() . "/smart_recruitment/HR-".$request_id.".pdf");

        Mail::to($mail_to)
        ->bcc(['ympi-mis-ML@music.yamaha.com'])
        ->send(new SendEmail($data, 'request_magang_approval'));

        $response = array(
            'status' => true,
            'message' => 'Request manpower berhasil diajukan.',
        );
        return Response::json($response);
    }
    catch(\Exception $e){
        $response = array(
            'status' => false,
            'message' => $e->getMessage(),
        );
        return Response::json($response);
    }    
}

public function VeteranRequestEmployeeInsert(Request $request){
    try{
        $emp = $request->get('employee');
        for($i = 0; $i < count($emp) ; $i++){
              // $nama = User::where('username',explode('/',$approval[$i])[0])->first();
            $nama = explode('/',$emp[$i]);
            $data = new VeteranRequestEmployee([
                'request_id'      => $request->get('req_id'), 
                'name'     => $nama[1]
            ]);
            $data->save();
        }

        //     $insert_employee = New VeteranRequestEmployee([
        //         'request_id' => $request->get('req_id'), 
        //         'name' => $request->get('employee')
        //     ]);
        // $insert_employee->save();
        $response = array(
            'status' => true,
            'message' => 'Man Power Berhasil Ditambah.',
        );
        return Response::json($response);
    }catch(\Exception $e){
        $response = array(
            'status' => false,
            'message' => $e->getMessage(),
        );
        return Response::json($response);
    }        

}

public function adminRequestManpower(Request $request){
    $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('employee_id','department')
    ->first();

    $departments = db::table('departments')->orderBy('department_name', 'ASC')->get();
    $jml_reqs1 = db::select('SELECT count(*) as jumlah from recruitments where remark = "Menunggu Persetujuan"');
    $jml_rec = db::select('SELECT count(*) as jumlah from recruitments where remark = "Recruitment HR"');
    $a = db::select('select status_req from recruitments');
    $insert = db::select('SELECT remark, status_at FROM recruitments');

    $employee = db::select('SELECT old_nik, `name` FROM veteran_data_masters');


        // var_dump($insert[0]->status_at);

    return view('human_resource.recruitment.dashboard_admin', array(
        'title' => 'Dashboard Request Manpower', 
        'title_jp' => '??',
        'positions' => $this->position,
        'departments' => $departments,
        'jml_reqs1' => $jml_reqs1,
        'jml_rec' => $jml_rec,
            // 'jml_reqs2' => $jml_reqs2,
        'emp_dept' => $emp_dept,
        'insert' => $insert,
        'employee' => $employee,
        'a' => $a
    ))->with('page', 'Human Resource');
}

public function MonitoringManPower(){

    return view('human_resource.recruitment.monitoring', array(
        'title' => 'Monitoring Man Power', 
        'title_jp' => '??',
    ))->with('page', 'Human Resource');
}

public function FetchMonitoringManPower(Request $request)
{
  $tahun = date('Y');
  $dateto = $request->get('dateto');
  $today = date('Y-m');
          // dd($dpts_st2);
  if ($dateto != "") {
    $data = db::select("
        SELECT
        count( nik ) AS jumlah,
        monthname( tanggal ) AS bulan,
        YEAR ( tanggal ) AS tahun,
        sum( CASE WHEN `status` is null THEN 1 ELSE 0 END ) AS Proces,
        sum( CASE WHEN `status` = 'All Approved' THEN 1 ELSE 0 END ) AS Signed,
        sum( CASE WHEN `status` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned 
        FROM
        mutasi_depts 
        WHERE
        mutasi_depts.deleted_at IS NULL 
        AND DATE_FORMAT( tanggal, '%Y-%m' ) = '".$dateto."'
        GROUP BY
        bulan,
        tahun 
        ORDER BY
        tahun,
        MONTH ( tanggal ) ASC
        ");
}else{
        // $data = db::select("
        //     SELECT
        //     department,
        //     count( employee_id ) AS jumlah,
        //     sum( CASE WHEN `employment_status` = 'OUTSOURCING' THEN 1 ELSE 0 END ) AS outsource,
        //     sum( CASE WHEN `employment_status` = 'PERMANENT' THEN 1 ELSE 0 END ) AS permanent,
        //     sum( CASE WHEN `employment_status` = 'CONTRACT1' THEN 1 ELSE 0 END ) AS contract1,
        //     sum( CASE WHEN `employment_status` = 'CONTRACT2' THEN 1 ELSE 0 END ) AS contract2,
        //     sum( CASE WHEN `employment_status` = 'CONTRACT3' THEN 1 ELSE 0 END ) AS contract3,
        //     sum( CASE WHEN `employment_status` = 'PROBATION' THEN 1 ELSE 0 END ) AS probation
        //     FROM
        //     employee_syncs
        //     GROUP BY
        //     department
        //     ");
    $data = db::select("select
        department,
        sum( quantity_male + quantity_female ) AS jumlah,
        sum( CASE WHEN remark = 'Menunggu Persetujuan' THEN 1 ELSE 0 END ) AS menunggu_persetujuan,
        sum( CASE WHEN remark = 'Recruitment HR' THEN 1 ELSE 0 END ) AS recruitment_hr
        from
        recruitments
        group by
        department");

        // $pgr = db::select("select sum(quantity_male + quantity_female) as jumlah from recruitments where remark = 'Recruitment HR' and (status_at = 'Test Potensi Akademik' or status_at = 'Test Wawancara' or status_at = 'Test Kesehatan')");
        // $ind = db::select("select sum(i.jumlah) as jumlah from ( select request_id, count(`status`) as jumlah from calon_karyawans where `status` = 'induction' group by request_id union all select request_id, count(`status`) as jumlah from calon_rekontraks where `status` = 'induction' group by request_id ) as i");
        // $mg = db::select("select sum(i.jumlah) as jumlah from ( select request_id, count(`status`) as jumlah from calon_karyawans where `status` = 'magang' group by request_id union all select request_id, count(`status`) as jumlah from calon_rekontraks where `status` = 'magang' group by request_id ) as i");
    $ct1 = db::select('select sum(quantity_male+quantity_female) as jm_ct1 from recruitments where status_at = "Test Potensi Akademik"');
    $ct2 = db::select('select sum(quantity_male+quantity_female) as jm_ct2 from recruitments where status_at = "Test Wawancara"');
    $ct3 = db::select('select sum(quantity_male+quantity_female) as jm_ct3 from recruitments where status_at = "Test Kesehatan"');

    $induction = db::select("select sum(i.jumlah) as jumlah from ( select request_id, count(`status`) as jumlah from calon_karyawans where `status` = 'Induction Training' group by request_id union all select request_id, count(`status`) as jumlah from calon_rekontraks where `status` = 'Induction Training' group by request_id ) as i");
}

$response = array(
    'status' => true,
    'datas' => $data,
    'tahun' => $tahun,
    'dateto' => $dateto,
        // 'pgr' => $pgr,
        // 'ind' => $ind,
        // 'mg' => $mg
    'ct1' => $ct1,
    'ct2' => $ct2,
    'ct3' => $ct3,
    'induction' => $induction
);

return Response::json($response); 
}

public function indexCobaApprove(Request $request, $request_id){
    // $data = db::select("select us.id, us.request_id, us.employee, e.`name`, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, u.`name` as pembuat, us.remark from uang_simpatis as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by where request_id = '".$request_id."'");
    $data = db::select("select request_id, up.department, up.employee, e.name, e.section, e.sub_group, in_out, tanggal, keterangan from uang_pekerjaans as up left join employee_syncs as e on e.employee_id = up.employee where request_id = '".$request_id."'");

    $approver = HrApproval::
    where('request_id', '=', $request_id)
    ->wherenull('status')
    ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
    ->first();

    $approver_progress = HrApproval::where('request_id', '=', $request_id)
    ->whereNotNull('approver_id')
    ->get();

    $role = User::where('username', Auth::user()->username)
    ->select('username','role_code')
    ->first();

    $data = [
        'data' => $data,
        'approver' => $approver,
        'approver_progress' => $approver_progress,
        'role' => $role
    ];

    $request_bulan = '2022-02';
    $tgl_pertama = date('Y-m-01', strtotime($request_bulan));
    $tgl_terakhir = date('Y-m-t', strtotime($request_bulan));

    // dd($tgl_pertama, $tgl_terakhir);
    
    return view('human_resource.mails_approver_kerja', array(
        'title' => 'Form Request Manpower', 
        'title_jp' => 'フォームリクエストのマンパワー',
        'data' => $data
    ))->with('page', 'Human Resource');
}

public function indexCoba(Request $request){
    $dpt = db::select('SELECT DISTINCT
        es.department 
        FROM
        plan_employee_contracts AS pc
        LEFT JOIN employee_syncs AS es ON es.employee_id = pc.employee_id 
        WHERE
        DATE_FORMAT( planing_end_date, "%m-%Y" ) = DATE_FORMAT( NOW() + INTERVAL 1 MONTH, "%m-%Y" ) 
        AND job_status IN ( "DIRECT", "INDIRECT", "STAFF" ) 
        AND pc.end_date IS NULL 
        GROUP BY
        es.department');

    for ($i=0; $i < count($dpt); $i++) {
        $count = db::select('SELECT DISTINCT
            count(pc.employee_id)
            FROM
            plan_employee_contracts AS pc
            LEFT JOIN employee_syncs AS es ON es.employee_id = pc.employee_id 
            WHERE
            DATE_FORMAT( planing_end_date, "%m-%Y" ) = DATE_FORMAT( NOW() + INTERVAL 1 MONTH, "%m-%Y" ) 
            AND job_status IN ("DIRECT", "INDIRECT", "STAFF") 
            AND pc.end_date IS NULL
            AND es.department = "'.$dpt[$i]->department.'"');

        $data = db::select('SELECT DISTINCT
            es.`name`,
            es.department,
            es.section,
            es.`group`,
            es.sub_group,
            DATE_FORMAT( planing_end_date, "%Y-%m" ) AS bulan,
            pc.planing_end_date 
            FROM
            plan_employee_contracts AS pc
            LEFT JOIN employee_syncs AS es ON es.employee_id = pc.employee_id 
            WHERE
            DATE_FORMAT( planing_end_date, "%m-%Y" ) = DATE_FORMAT( NOW() + INTERVAL 1 MONTH, "%m-%Y" ) 
            AND job_status IN ( "DIRECT", "INDIRECT", "STAFF" ) 
            AND pc.end_date IS NULL 
            AND es.department = "'.$dpt[$i]->department.'"');

        $mail_to = Approver::where('department', $dpt[$i]->department)
        ->select('approver_email')
        ->first();
        $mail = [];
        array_push($mail, $mail_to->approver_email);

            // if ($count[$i]->jumlah != '0') {
        Mail::to($mail)
        ->bcc(['lukmannul.arif@music.yamaha.com'])
        ->send(new SendEmail($data, 'end_contract'));
            // }
    }
    $response = array(
        'status'        => true,
        'dpt' => $dpt,
        'data' => $data
    );
    return Response::json($response);
}

public function ApproveManager(Request $request, $request_id){
    try{
        $req = RecruitmentApproval::where('request_id',$request_id)->first();;
            // dd($request_id);

        if(($req->status == null) && ($req->remark == 'Manager')){
            $req->status = 'Approved';
            $req->save();
        }

        return redirect('/index/hr/request_manpower')->with('status', 'Success')->with('page', 'Human Resources');  
    }
    catch (QueryException $e){
        return back()->with('error', 'Error')->with('page', 'Mutasi Error');
    }
}

public function ApproveDGM(Request $request, $request_id){
    try{
        $req = RecruitmentApproval::where('request_id',$request_id)->first();
            // dd($request_id);

        if(($req->status == null) && ($req->remark == 'DGM')){
            $req->status = 'Approved';
            $req->save();
        }

        return redirect('/index/hr/request_manpower')->with('status', 'Success')->with('page', 'Human Resources');  
    }
    catch (QueryException $e){
        return back()->with('error', 'Error')->with('page', 'Mutasi Error');
    }
}

public function ResumeRequest(Request $request){

    $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('employee_id','department')
    ->first();

    if ($emp_dept->department == 'Management Information System Department') {
        $resume = db::select("
            SELECT DISTINCT
            apr.request_id, DATE_FORMAT(r.created_at, '%Y-%m-%d') as created_at, r.start_date, u.name, r.remark, r.status_at,
            r.position, r.department, r.employment_status, r.quantity_male, r.quantity_female, r.reason,
            ( SELECT GROUP_CONCAT( a.remark ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id   ) as approval,
            ( SELECT GROUP_CONCAT( a.approver_id ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as id,
            ( SELECT GROUP_CONCAT( COALESCE(a.status,'') ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as status,
            ( SELECT b.department FROM recruitments b WHERE b.request_id = apr.request_id  ) as department,
            ( SELECT count(a.request_id) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as cnt,
            ( SELECT count(a.`status`) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as app
            FROM
            `recruitment_approvals` as apr
            left join recruitments as r on r.request_id = apr.request_id
            left join users as u on u.id = r.created_by
            ");

        $resume_detail = db::select("
            SELECT DISTINCT
            apr.request_id, DATE_FORMAT(r.created_at, '%Y-%m-%d') as created_at, r.start_date, u.name, r.remark, r.status_at,
            r.position, r.department, r.employment_status, r.quantity_male, r.quantity_female, r.reason, r.start_date, r.marriage_status, r.domicile, r.note, r.status_req, r.section, r.group, r.sub_group,
            ( SELECT GROUP_CONCAT( a.remark ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id   ) as approval,
            ( SELECT GROUP_CONCAT( a.approver_id ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as id,
            ( SELECT GROUP_CONCAT( COALESCE(a.status,'') ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as status,
            ( SELECT b.department FROM recruitments b WHERE b.request_id = apr.request_id  ) as department,
            ( SELECT count(a.request_id) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as cnt,
            ( SELECT count(a.`status`) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as app
            FROM
            `recruitment_approvals` as apr
            left join recruitments as r on r.request_id = apr.request_id
            left join users as u on u.id = r.created_by
            WHERE
            apr.request_id = '".$request->get('req_id')."'
            ");

        $resume_off = db::select("
            SELECT DISTINCT
            apr.request_id, DATE_FORMAT(r.created_at, '%Y-%m-%d') as created_at, r.start_date, u.name, r.remark, r.status_at,
            r.position, r.department, r.employment_status, r.quantity_male, r.quantity_female, r.reason, r.start_date, r.marriage_status, r.domicile, r.status_req,
            ( SELECT GROUP_CONCAT( a.remark ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id   ) as approval,
            ( SELECT GROUP_CONCAT( a.approver_id ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as id,
            ( SELECT GROUP_CONCAT( COALESCE(a.status,'') ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as status,
            ( SELECT b.department FROM recruitments b WHERE b.request_id = apr.request_id  ) as department,
            ( SELECT count(a.request_id) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as cnt,
            ( SELECT count(a.`status`) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as app
            FROM
            `recruitment_approvals` as apr
            left join recruitments as r on r.request_id = apr.request_id
            left join users as u on u.id = r.created_by
            WHERE r.status_at <> 'Process Approval'
            ORDER BY status_at DESC
            ");

        $resume_prd = db::select("
            SELECT DISTINCT
            apr.request_id, DATE_FORMAT(r.created_at, '%Y-%m-%d') as created_at, r.start_date, u.name, r.remark, r.status_at,
            r.position, r.department, r.employment_status, r.quantity_male, r.quantity_female, r.reason,
            ( SELECT GROUP_CONCAT( a.remark ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id   ) as approval,
            ( SELECT GROUP_CONCAT( a.approver_id ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as id,
            ( SELECT GROUP_CONCAT( COALESCE(a.status,'') ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as status,
            ( SELECT b.department FROM recruitments b WHERE b.request_id = apr.request_id  ) as department,
            ( SELECT count(a.request_id) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as cnt,
            ( SELECT count(a.`status`) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as app
            FROM
            `recruitment_approvals` as apr
            left join recruitments as r on r.request_id = apr.request_id
            left join users as u on u.id = r.created_by
            WHERE
            r.position = 'Operator'
            ");
        // var_dump($resume);

    //grafik
        $grafik = DB::SELECT("
          SELECT
          a.department,
          department_shortname,
          SUM( a.count_sudah ) AS sudah,
          SUM( a.count_belum ) AS belum 
          FROM
          (
              SELECT
              sum( CASE WHEN recruitments.status_at = 'Process' THEN 1 ELSE 0 END ) AS count_belum,
              sum( CASE WHEN recruitments.status_at = 'Recruitment HR' THEN 1 ELSE 0 END ) AS count_sudah,
              COALESCE ( department, '' ) AS department 
              FROM
              ympimis.recruitments 
              GROUP BY
              department
              ) a
          LEFT JOIN departments ON a.department = departments.department_name 
          GROUP BY
          a.department,
          departments.department_shortname
          ");
    }
    else{
        $resume = db::select("
            SELECT DISTINCT
            apr.request_id, DATE_FORMAT(r.created_at, '%Y-%m-%d') as created_at, r.start_date, u.name, r.remark, r.status_at,
            r.position, r.department, r.employment_status, r.quantity_male, r.quantity_female, r.reason,
            ( SELECT GROUP_CONCAT( a.remark ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id   ) as approval,
            ( SELECT GROUP_CONCAT( a.approver_id ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as id,
            ( SELECT GROUP_CONCAT( COALESCE(a.status,'') ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as status,
            ( SELECT b.department FROM recruitments b WHERE b.request_id = apr.request_id  ) as department,
            ( SELECT count(a.request_id) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as cnt,
            ( SELECT count(a.`status`) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as app
            FROM
            `recruitment_approvals` as apr
            left join recruitments as r on r.request_id = apr.request_id
            left join users as u on u.id = r.created_by
            WHERE
            r.department = '".$emp_dept->department."'
            ");

        $resume_detail = db::select("
            SELECT DISTINCT
            apr.request_id, DATE_FORMAT(r.created_at, '%Y-%m-%d') as created_at, r.start_date, u.name, r.remark, r.status_at,
            r.position, r.department, r.employment_status, r.quantity_male, r.quantity_female, r.reason, r.start_date, r.marriage_status, r.domicile, r.note, r.status_req, r.section, r.group, r.sub_group,
            ( SELECT GROUP_CONCAT( a.remark ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id   ) as approval,
            ( SELECT GROUP_CONCAT( a.approver_id ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as id,
            ( SELECT GROUP_CONCAT( COALESCE(a.status,'') ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as status,
            ( SELECT b.department FROM recruitments b WHERE b.request_id = apr.request_id  ) as department,
            ( SELECT count(a.request_id) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as cnt,
            ( SELECT count(a.`status`) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as app
            FROM
            `recruitment_approvals` as apr
            left join recruitments as r on r.request_id = apr.request_id
            left join users as u on u.id = r.created_by
            WHERE
            apr.request_id = '".$request->get('req_id')."'
            ");

        $resume_off = db::select("
            SELECT DISTINCT
            apr.request_id, DATE_FORMAT(r.created_at, '%Y-%m-%d') as created_at, r.start_date, u.name, r.remark, r.status_at,
            r.position, r.department, r.employment_status, r.quantity_male, r.quantity_female, r.reason, r.start_date, r.marriage_status, r.domicile, r.status_req,
            ( SELECT GROUP_CONCAT( a.remark ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id   ) as approval,
            ( SELECT GROUP_CONCAT( a.approver_id ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as id,
            ( SELECT GROUP_CONCAT( COALESCE(a.status,'') ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as status,
            ( SELECT b.department FROM recruitments b WHERE b.request_id = apr.request_id  ) as department,
            ( SELECT count(a.request_id) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as cnt,
            ( SELECT count(a.`status`) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as app
            FROM
            `recruitment_approvals` as apr
            left join recruitments as r on r.request_id = apr.request_id
            left join users as u on u.id = r.created_by
            WHERE r.status_at <> 'Process'
            ORDER BY status_at DESC
            ");

        $resume_prd = db::select("
            SELECT DISTINCT
            apr.request_id, DATE_FORMAT(r.created_at, '%Y-%m-%d') as created_at, r.start_date, u.name, r.remark, r.status_at,
            r.position, r.department, r.employment_status, r.quantity_male, r.quantity_female, r.reason,
            ( SELECT GROUP_CONCAT( a.remark ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id   ) as approval,
            ( SELECT GROUP_CONCAT( a.approver_id ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as id,
            ( SELECT GROUP_CONCAT( COALESCE(a.status,'') ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as status,
            ( SELECT b.department FROM recruitments b WHERE b.request_id = apr.request_id  ) as department,
            ( SELECT count(a.request_id) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as cnt,
            ( SELECT count(a.`status`) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as app
            FROM
            `recruitment_approvals` as apr
            left join recruitments as r on r.request_id = apr.request_id
            left join users as u on u.id = r.created_by
            WHERE
            r.position = 'Operator'
            ");
        // var_dump($resume);

    //grafik
        $grafik = DB::SELECT("
          SELECT
          a.department,
          department_shortname,
          SUM( a.count_sudah ) AS sudah,
          SUM( a.count_belum ) AS belum 
          FROM
          (
              SELECT
              sum( CASE WHEN recruitments.status_at = 'Process' THEN 1 ELSE 0 END ) AS count_belum,
              sum( CASE WHEN recruitments.status_at = 'Recruitment HR' THEN 1 ELSE 0 END ) AS count_sudah,
              COALESCE ( department, '' ) AS department 
              FROM
              ympimis.recruitments 
              GROUP BY
              department
              ) a
          LEFT JOIN departments ON a.department = departments.department_name 
          WHERE
          a.department = '".$emp_dept->department."' 
          GROUP BY
          a.department,
          departments.department_shortname
          ");
    }

    //employee
    $employee = db::select('SELECT old_nik, `name` FROM veteran_data_masters');

    //proses rekrut
    $rekrut = db::select("
        SELECT DISTINCT
        ( SELECT GROUP_CONCAT( a.`process_rekrut` ) FROM recruitment_details a WHERE a.request_id = apr.request_id ) AS approval,
        ( SELECT GROUP_CONCAT( COALESCE(a.`status`,'') ) FROM recruitment_details a WHERE a.request_id = apr.request_id  ) as `status`
        FROM
        `recruitment_details` as apr
        WHERE
        apr.request_id = '".$request->get('req_id')."'
        ");

    $detail_rekrut = db::select("SELECT * FROM recruitments WHERE request_id = '".$request->get('req_id')."'");

    $detail_magang = db::select("SELECT * FROM request_magangs WHERE request_id = '".$request->get('req_id')."'");

    $request_karyawan = db::select("SELECT
        cr.nik,
        cr.nama,
        r.department,
        cr.`group`,
        cr.sub_group 
        FROM
        calon_rekontraks AS cr
        LEFT JOIN recruitments AS r ON r.request_id = cr.request_id  WHERE cr.request_id = '".$request->get('req_id')."'");

    $response = array(
      'status'        => true,
      'resumes'       =>$resume,
      'resumes_off'   => $resume_off,
      'resumes_prd'   => $resume_prd,
      'resume_detail' => $resume_detail,
      'grafik'        => $grafik,
      'employee'      => $employee,
      'rekrut'        => $rekrut,
      'detail_rekrut' => $detail_rekrut,
      'request_karyawan' => $request_karyawan,
      'detail_magang' => $detail_magang
  );
    return Response::json($response);
}

public function DataKaryawanKontrak(Request $request){

    $data = db::select("SELECT * FROM plan_employee_contracts WHERE employee_id = '".$request->get('employee_id')."'");

    $response = array(
        'status' => true,
        'data' => $data
    );
    return Response::json($response);
}

public function UpdateKaryawanContrac(Request $request){
    try{
      $department = $request->get('department');
      $month_next = $request->get('month_next');
      $employee_id = $request->get('employee_id');
      $ket = $request->get('ket');
      $st = explode(' ', $request->get('status'));
      $status = $st[1]+1;
      $pc = $request->get('nilai1');
      $sd = $request->get('nilai2');
      $izin = $request->get('nilai3');
      $alpha = $request->get('nilai4');
      $note = $request->get('nilai5');
      $score = $request->get('score');
      $kategory_nilai = $request->get('kategory_nilai');

      // dd($ket);

      if ($ket == '1') {
          $update_periode = PlanEmployeeContract::where('employee_id', '=', $employee_id)->update([
            'planing_end_date' => $month_next,
            'remark' => $ket,
            'score_akhir' => $score,
            'kategori_nilai' => $kategory_nilai,
            'status' => 'Contrac '.$status
            // 'pc_t' => $pc,
            // 'surat_dokter' => $sd,
            // 'izin' => $izin,
            // 'alpha' => $alpha, 
            // 'note_hr' => $note
        ]);
          $count = db::select('select count(id) as jumlah from plan_employee_contracts where department = "'.$department.'" and DATE_FORMAT( end_date, "%Y-%m" ) = DATE_FORMAT( now() + INTERVAL 1 MONTH, "%Y-%m" ) and remark is null');
          if ($count[0]->jumlah == '0') {
            $butuh = db::select('select count(id) as jumlah from plan_employee_contracts where department = "'.$department.'" and DATE_FORMAT( end_date, "%Y-%m" ) = DATE_FORMAT( now() + INTERVAL 1 MONTH, "%Y-%m" ) and remark = "2"');
            $sisa = db::select('select count(employee_id) as jumlah from plan_employee_contracts where department = "'.$department.'" and DATE_FORMAT( end_date, "%Y-%m" ) = DATE_FORMAT( now() + INTERVAL 1 MONTH, "%Y-%m" ) and remark is null');
            if ($sisa[0]->jumlah == '0') {

                $code_generator = CodeGenerator::where('note','=','NILAI MANPOWER')->first();
                $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
                $request_id = $code_generator->prefix . $number;

                $m = Approver::where('department', '=', $request->get('department'))
                ->where('remark', '=', 'Manager')
                ->first();

                $dgm = Approver::where('department', '=', $request->get('department'))
                ->where('remark', '=', 'Deputy General Manager')
                ->first();

                $gm = Approver::where('department', '=', $request->get('department'))
                ->where('remark', '=', 'General Manager')
                ->first();

                $pd = Approver::where('remark', '=', 'President Director')
                ->first();

                $d = Approver::where('department', '=', 'Human Resources Department')
                ->where('remark', '=', 'Director')
                ->first();

                $mhr = Approver::where('department', '=', 'Human Resources Department')
                ->where('remark', '=', 'Manager')
                ->first();

                if($m != null){
                    $manager = New RecruitmentApproval([
                        'request_id' => $request_id,
                        'approver_id' => $m->approver_id,
                        'approver_name' => $m->approver_name,
                        'approver_email' => $m->approver_email,
                        'remark' => 'Manager',
                        'project_name' => 'Penilaian MP'
                    ]);
                }
                else{
                    $manager = New RecruitmentApproval([
                        'request_id' => $request_id,
                        'approver_id' => null,
                        'approver_name' => "",
                        'approver_email' => "",
                        'status' => "none",
                        'approved_at' => date('Y-m-d H:i:s'),
                        'remark' => 'Manager',
                        'project_name' => 'Penilaian MP'
                    ]);
                }

                if($dgm != null){
                    $deputy_general_manager = New RecruitmentApproval([
                        'request_id' => $request_id,
                        'approver_id' => $dgm->approver_id,
                        'approver_name' => $dgm->approver_name,
                        'approver_email' => $dgm->approver_email,
                        'remark' => 'DGM',
                        'project_name' => 'Penilaian MP'
                    ]);
                }
                else{
                    $deputy_general_manager = New RecruitmentApproval([
                        'request_id' => $request_id,
                        'approver_id' => null,
                        'approver_name' => "",
                        'approver_email' => "",
                        'status' => "none",
                        'approved_at' => date('Y-m-d H:i:s'),
                        'remark' => 'DGM',
                        'project_name' => 'Penilaian MP'
                    ]);
                }

                if($gm != null){
                    $general_manager = New RecruitmentApproval([
                        'request_id' => $request_id,
                        'approver_id' => $gm->approver_id,
                        'approver_name' => $gm->approver_name,
                        'approver_email' => $gm->approver_email,
                        'remark' => 'GM',
                        'project_name' => 'Penilaian MP'
                    ]);
                }
                else{
                    $general_manager = New RecruitmentApproval([
                        'request_id' => $request_id,
                        'approver_id' => null,
                        'approver_name' => "",
                        'approver_email' => "",
                        'status' => 'none',
                        'approved_at' => date('Y-m-d H:i:s'),
                        'remark' => 'GM',
                        'project_name' => 'Penilaian MP'
                    ]);
                }

                if($pd != null){
                    $president_director = New RecruitmentApproval([
                        'request_id' => $request_id,
                        'approver_id' => $pd->approver_id,
                        'approver_name' => $pd->approver_name,
                        'approver_email' => $pd->approver_email,
                        'remark' => 'Presiden Direktur',
                        'project_name' => 'Penilaian MP'
                    ]);
                }
                else{
                    $president_director = New RecruitmentApproval([
                        'request_id' => $request_id,
                        'approver_id' => null,
                        'approver_name' => "",
                        'approver_email' => "",
                        'status' => "none",
                        'approved_at' => date('Y-m-d H:i:s'),
                        'remark' => 'Presiden Direktur',
                        'project_name' => 'Penilaian MP'
                    ]);
                }

                if($d != null){
                    $director = New RecruitmentApproval([
                        'request_id' => $request_id,
                        'approver_id' => $d->approver_id,
                        'approver_name' => $d->approver_name,
                        'approver_email' => $d->approver_email,
                        'remark' => 'Direktur HR',
                        'project_name' => 'Penilaian MP'
                    ]);
                }
                else{
                    $director = New RecruitmentApproval([
                        'request_id' => $request_id,
                        'approver_id' => null,
                        'approver_name' => "",
                        'approver_email' => "",
                        'status' => "none",
                        'approved_at' => date('Y-m-d H:i:s'),
                        'remark' => 'Direktur HR',
                        'project_name' => 'Penilaian MP'
                    ]);
                }

                if($mhr != null){
                    $manager_hr = New RecruitmentApproval([
                        'request_id' => $request_id,
                        'approver_id' => $mhr->approver_id,
                        'approver_name' => $mhr->approver_name,
                        'approver_email' => $mhr->approver_email,
                        'remark' => 'Manager HR',
                        'project_name' => 'Penilaian MP'
                    ]);
                }
                else{
                    $manager_hr = New RecruitmentApproval([
                        'request_id' => $request_id,
                        'approver_id' => null,
                        'approver_name' => "",
                        'approver_email' => "",
                        'status' => "none",
                        'approved_at' => date('Y-m-d H:i:s'),
                        'remark' => 'Manager HR',
                        'project_name' => 'Penilaian MP'
                    ]);
                }

                $admin_hr = New RecruitmentApproval([
                    'request_id' => $request_id,
                    'approver_id' => "PI0811002",
                    'approver_name' => "Mahendra Putra",
                    'approver_email' => "mahendra.putra@music.yamaha.com",
                    'remark' => 'Staff HR',
                    'project_name' => 'Penilaian MP'
                ]);

                $code_generator->index = $code_generator->index+1;
                $manager->save();
                $deputy_general_manager->save();
                $general_manager->save();
                $president_director->save();
                $director->save();
                $manager_hr->save();
                $admin_hr->save();
                $code_generator->save();

                $mail_to = [];
                // $mail_cc = [];

                array_push($mail_to, $manager->approver_email);

                $data = db::select("select employee_id, name, department, section, `group`, sub_group, DATE_FORMAT(end_date, '%Y-%m') as bulan, hire_date, end_date, planing_end_date, status, remark, pc_t, surat_dokter, izin, alpha, note_hr, score_akhir, kategori_nilai, TIMESTAMPDIFF(MONTH,hire_date, end_date) AS selisih_bulan, TIMESTAMPDIFF(MONTH,end_date, planing_end_date) AS rencana_kontrak from plan_employee_contracts where department = '".$department."' and DATE_FORMAT( end_date, '%Y-%m' ) = DATE_FORMAT( now() + INTERVAL 1 MONTH, '%Y-%m' ) ORDER BY name ASC");

                $end = db::select('select count(employee_id) as end_contract from plan_employee_contracts where remark = "2" and department = "'.$department.'"');

                $app_email = RecruitmentApproval::
                where('request_id', '=', $request_id)
                ->wherenull('status')
                ->select('request_id', 'approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
                ->first();

                $app_progress = RecruitmentApproval::where('request_id', '=', $request_id)
                ->whereNotNull('approver_id')
                ->get();

                $mp_end = db::select("select employee_id, name, department, section, `group`, sub_group, DATE_FORMAT(end_date, '%Y-%m') as bulan, hire_date, end_date, planing_end_date, status, remark, pc_t, surat_dokter, izin, alpha, note_hr, score_akhir, kategori_nilai, TIMESTAMPDIFF(MONTH,hire_date, end_date) AS selisih_bulan, TIMESTAMPDIFF(MONTH,end_date, planing_end_date) AS rencana_kontrak from plan_employee_contracts where department = '".$department."' and remark = '2' and DATE_FORMAT( end_date, '%Y-%m' ) = DATE_FORMAT( now() + INTERVAL 1 MONTH, '%Y-%m' ) ORDER BY name ASC");

                $mail_foreman = Approver::where('department', $mp_end[0]->department)
                ->where('remark', '=', 'Foreman')
                ->orWhere('remark', '=', 'Chief')
                ->select('approver_email')
                ->first();

                $mail_atasan = [];
                array_push($mail_atasan, $mail_foreman->approver_email);

                $data = [
                    'data' => $data,
                    'app_email' => $app_email,
                    'app_progress' => $app_progress,
                    'end' => $end,
                    'mp_end' => $mp_end
                ];

                Mail::to($mail_to[0])
                ->bcc(['ympi-mis-ML@music.yamaha.com'])
                ->send(new SendEmail($data, 'approval_nilai_mp'));

                //email ke foreman cari pengganti putus kontrak
                Mail::to($mail_atasan[0])
                ->bcc(['ympi-mis-ML@music.yamaha.com'])
                ->send(new SendEmail($data, 'pengganti_mp'));
            }
        }
    }else if($ket == 'nilai hr'){
        $update_periode = PlanEmployeeContract::where('employee_id', '=', $employee_id)->update([
        //     'planing_end_date' => $month_next,
            // 'remark' => $ket,
            // 'status' => 'Contrac '.$status,
            'pc_t' => $pc,
            'surat_dokter' => $sd,
            'izin' => $izin,
            'alpha' => $alpha, 
            'note_hr' => $note
        ]);

        $count = db::select('select count(id) as jumlah from plan_employee_contracts where department = "'.$department.'" and DATE_FORMAT( end_date, "%Y-%m" ) = DATE_FORMAT( now() + INTERVAL 1 MONTH, "%Y-%m" ) and note_hr is null');
        if ($count[0]->jumlah == '0') {
            $dpt = db::select('select DISTINCT department from plan_employee_contracts where DATE_FORMAT( end_date, "%Y-%m" ) = DATE_FORMAT( now() + INTERVAL 1 MONTH, "%Y-%m" )');
            // for ($i=0; $i < count($dpt); $i++) {
                // $count = db::select('select count(id) as jumlah from plan_employee_contracts where department = "'.$department.'" and DATE_FORMAT( end_date, "%Y-%m" ) = DATE_FORMAT( now() + INTERVAL 1 MONTH, "%Y-%m" )');
            $data = db::select('select name, department, section, `group`, sub_group, DATE_FORMAT(end_date, "%Y-%m") as bulan, end_date from plan_employee_contracts where department = "'.$department.'" and DATE_FORMAT( end_date, "%Y-%m" ) = DATE_FORMAT( now() + INTERVAL 1 MONTH, "%Y-%m" ) ORDER BY name ASC');

            $mail_to = Approver::where('department', $department)
            ->select('approver_email')
            ->first();

            $mail = [];
            array_push($mail, $mail_to->approver_email);
            Mail::to($mail[0])->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($data, 'end_contract'));
            // }
        }
    }else{
        $update_periode = PlanEmployeeContract::where('employee_id', '=', $employee_id)->update([
            'remark' => $ket,
            'score_akhir' => $score,
            'kategori_nilai' => $kategory_nilai
        ]);
    }

    // $count = db::select('select count(id) as jumlah from plan_employee_contracts where department = "'.$department.'" and DATE_FORMAT( end_date, "%Y-%m" ) = DATE_FORMAT( now() + INTERVAL 1 MONTH, "%Y-%m" ) and note_hr is null');

    // else{
    //     dd('send email approval');
        // if ($count[0]->jumlah == '0') {
        //     $butuh = db::select('select count(id) as jumlah from plan_employee_contracts where department = "'.$department.'" and DATE_FORMAT( end_date, "%Y-%m" ) = DATE_FORMAT( now() + INTERVAL 1 MONTH, "%Y-%m" ) and remark = "2"');
        //     $sisa = db::select('select count(employee_id) as jumlah from plan_employee_contracts where department = "'.$department.'" and DATE_FORMAT( end_date, "%Y-%m" ) = DATE_FORMAT( now() + INTERVAL 1 MONTH, "%Y-%m" ) and remark is null');
        //     if ($sisa[0]->jumlah == '0') {

        //         $code_generator = CodeGenerator::where('note','=','NILAI MANPOWER')->first();
        //         $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
        //         $request_id = $code_generator->prefix . $number;

        //         $m = Approver::where('department', '=', $request->get('department'))
        //         ->where('remark', '=', 'Manager')
        //         ->first();

        //         $dgm = Approver::where('department', '=', $request->get('department'))
        //         ->where('remark', '=', 'Deputy General Manager')
        //         ->first();

        //         $gm = Approver::where('department', '=', $request->get('department'))
        //         ->where('remark', '=', 'General Manager')
        //         ->first();

        //         $pd = Approver::where('remark', '=', 'President Director')
        //         ->first();

        //         $d = Approver::where('department', '=', 'Human Resources Department')
        //         ->where('remark', '=', 'Director')
        //         ->first();

        //         $mhr = Approver::where('department', '=', 'Human Resources Department')
        //         ->where('remark', '=', 'Manager')
        //         ->first();

        //         if($m != null){
        //             $manager = New RecruitmentApproval([
        //                 'request_id' => $request_id,
        //                 'approver_id' => $m->approver_id,
        //                 'approver_name' => $m->approver_name,
        //                 'approver_email' => $m->approver_email,
        //                 'remark' => 'Manager',
        //                 'project_name' => 'Penilaian MP'
        //             ]);
        //         }
        //         else{
        //             $manager = New RecruitmentApproval([
        //                 'request_id' => $request_id,
        //                 'approver_id' => null,
        //                 'approver_name' => "",
        //                 'approver_email' => "",
        //                 'status' => "none",
        //                 'approved_at' => date('Y-m-d H:i:s'),
        //                 'remark' => 'Manager',
        //                 'project_name' => 'Penilaian MP'
        //             ]);
        //         }

        //         if($dgm != null){
        //             $deputy_general_manager = New RecruitmentApproval([
        //                 'request_id' => $request_id,
        //                 'approver_id' => $dgm->approver_id,
        //                 'approver_name' => $dgm->approver_name,
        //                 'approver_email' => $dgm->approver_email,
        //                 'remark' => 'DGM',
        //                 'project_name' => 'Penilaian MP'
        //             ]);
        //         }
        //         else{
        //             $deputy_general_manager = New RecruitmentApproval([
        //                 'request_id' => $request_id,
        //                 'approver_id' => null,
        //                 'approver_name' => "",
        //                 'approver_email' => "",
        //                 'status' => "none",
        //                 'approved_at' => date('Y-m-d H:i:s'),
        //                 'remark' => 'DGM',
        //                 'project_name' => 'Penilaian MP'
        //             ]);
        //         }

        //         if($gm != null){
        //             $general_manager = New RecruitmentApproval([
        //                 'request_id' => $request_id,
        //                 'approver_id' => $gm->approver_id,
        //                 'approver_name' => $gm->approver_name,
        //                 'approver_email' => $gm->approver_email,
        //                 'remark' => 'GM',
        //                 'project_name' => 'Penilaian MP'
        //             ]);
        //         }
        //         else{
        //             $general_manager = New RecruitmentApproval([
        //                 'request_id' => $request_id,
        //                 'approver_id' => null,
        //                 'approver_name' => "",
        //                 'approver_email' => "",
        //                 'status' => 'none',
        //                 'approved_at' => date('Y-m-d H:i:s'),
        //                 'remark' => 'GM',
        //                 'project_name' => 'Penilaian MP'
        //             ]);
        //         }

        //         if($pd != null){
        //             $president_director = New RecruitmentApproval([
        //                 'request_id' => $request_id,
        //                 'approver_id' => $pd->approver_id,
        //                 'approver_name' => $pd->approver_name,
        //                 'approver_email' => $pd->approver_email,
        //                 'remark' => 'Presiden Direktur',
        //                 'project_name' => 'Penilaian MP'
        //             ]);
        //         }
        //         else{
        //             $president_director = New RecruitmentApproval([
        //                 'request_id' => $request_id,
        //                 'approver_id' => null,
        //                 'approver_name' => "",
        //                 'approver_email' => "",
        //                 'status' => "none",
        //                 'approved_at' => date('Y-m-d H:i:s'),
        //                 'remark' => 'Presiden Direktur',
        //                 'project_name' => 'Penilaian MP'
        //             ]);
        //         }

        //         if($d != null){
        //             $director = New RecruitmentApproval([
        //                 'request_id' => $request_id,
        //                 'approver_id' => $d->approver_id,
        //                 'approver_name' => $d->approver_name,
        //                 'approver_email' => $d->approver_email,
        //                 'remark' => 'Direktur HR',
        //                 'project_name' => 'Penilaian MP'
        //             ]);
        //         }
        //         else{
        //             $director = New RecruitmentApproval([
        //                 'request_id' => $request_id,
        //                 'approver_id' => null,
        //                 'approver_name' => "",
        //                 'approver_email' => "",
        //                 'status' => "none",
        //                 'approved_at' => date('Y-m-d H:i:s'),
        //                 'remark' => 'Direktur HR',
        //                 'project_name' => 'Penilaian MP'
        //             ]);
        //         }

        //         if($mhr != null){
        //             $manager_hr = New RecruitmentApproval([
        //                 'request_id' => $request_id,
        //                 'approver_id' => $mhr->approver_id,
        //                 'approver_name' => $mhr->approver_name,
        //                 'approver_email' => $mhr->approver_email,
        //                 'remark' => 'Manager HR',
        //                 'project_name' => 'Penilaian MP'
        //             ]);
        //         }
        //         else{
        //             $manager_hr = New RecruitmentApproval([
        //                 'request_id' => $request_id,
        //                 'approver_id' => null,
        //                 'approver_name' => "",
        //                 'approver_email' => "",
        //                 'status' => "none",
        //                 'approved_at' => date('Y-m-d H:i:s'),
        //                 'remark' => 'Manager HR',
        //                 'project_name' => 'Penilaian MP'
        //             ]);
        //         }

        //         $admin_hr = New RecruitmentApproval([
        //             'request_id' => $request_id,
        //             'approver_id' => "PI0811002",
        //             'approver_name' => "Mahendra Putra",
        //             'approver_email' => "mahendra.putra@music.yamaha.com",
        //             'remark' => 'Staff HR',
        //             'project_name' => 'Penilaian MP'
        //         ]);

        //         $code_generator->index = $code_generator->index+1;
        //         $manager->save();
        //         $deputy_general_manager->save();
        //         $general_manager->save();
        //         $president_director->save();
        //         $director->save();
        //         $manager_hr->save();
        //         $admin_hr->save();
        //         $code_generator->save();

        //         $mail_to = [];
        //         $mail_cc = [];

        //         array_push($mail_to, $manager->approver_email);

        //         $data = db::select("select employee_id, name, department, section, `group`, sub_group, DATE_FORMAT(end_date, '%Y-%m') as bulan, hire_date, end_date, planing_end_date, status, remark, pc_t, surat_dokter, izin, alpha, note_hr, score_akhir, kategori_nilai, TIMESTAMPDIFF(MONTH,hire_date, end_date) AS selisih_bulan, TIMESTAMPDIFF(MONTH,end_date, planing_end_date) AS rencana_kontrak from plan_employee_contracts where department = '".$department."' and DATE_FORMAT( end_date, '%Y-%m' ) = DATE_FORMAT( now() + INTERVAL 1 MONTH, '%Y-%m' ) ORDER BY name ASC");

        //         $app_email = RecruitmentApproval::
        //         where('request_id', '=', $request_id)
        //         ->wherenull('status')
        //         ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
        //         ->first();

        //         $app_progress = RecruitmentApproval::where('request_id', '=', $request_id)
        //         ->whereNotNull('approver_id')
        //         ->get();

        //         $data = [
        //             'data' => $data,
        //             'app_email' => $app_email,
        //             'app_progress' => $app_progress
        //         ];

            // return view('human_resource.recruitment.mail_approval_penilaian_mp', array(
            //     'data' => $data,
            //     'app_email' => $app_email,
            //     'app_progress' => $app_progress
            // ));

            // $pdf = \App::make('dompdf.wrapper');
            // $pdf->getDomPDF()->set_option("enable_php", true);
            // $pdf->setPaper('A3', 'landscape');
            // $pdf->loadView('human_resource.recruitment.report_pdf_penilaian', array(
            //     'data' => $data,
            //     'app_email' => $app_email,
            //     'app_progress' => $app_progress
            // ));
            // $pdf->save(public_path() . "/smart_recruitment/HR-SR-".$department.".pdf");

            //     Mail::to($mail_to[0])
            //     ->bcc(['ympi-mis-ML@music.yamaha.com'])
            //     ->send(new SendEmail($data, 'approval_nilai_mp'));
            // }

        // }
    // $count = db::select('select count(id) as jumlah from plan_employee_contracts where department = "'.$department.'" and DATE_FORMAT( end_date, "%Y-%m" ) = DATE_FORMAT( now() + INTERVAL 1 MONTH, "%Y-%m" ) and remark is null');


          // $data = db::select('select name, department, section, `group`, sub_group, DATE_FORMAT(end_date, "%Y-%m") as bulan, end_date from plan_employee_contracts where department = "'.$department.'" and DATE_FORMAT( end_date, "%Y-%m" ) = DATE_FORMAT( now() + INTERVAL 1 MONTH, "%Y-%m" ) ORDER BY name ASC');
          // $mail_to = Approver::where('department', $dpt[$i]->department)
          // ->select('approver_email')
          // ->first();

          // $mail = [];
          // array_push($mail, $mail_to->approver_email);

          // Mail::to(['ympi-mis-ML@music.yamaha.com'])
          // ->bcc(['ympi-mis-ML@music.yamaha.com'])
          // ->send(new SendEmail($data, 'end_contract'));
    // }


    $response = array(
        'status' => true,
        'message' => 'Karyawan Berhasil Diperpanjang Kontrak.',
    );
    return Response::json($response);
}catch (Exception $e) {
 $response = array(
  'status' => false,
  'message' => $e->getMessage(),
);
 return Response::json($response);
}
}

public function UpdateKaryawanContracUpload(Request $request){
    $filename = "";
    $file_destination = 'data_file/update_karyawan';

    if ($request->file('newAttachment') != null) {
        try{
            $file = $request->file('newAttachment');
            $filename = 'UploadPriodeKaryawan'.date('YmdHisa').'.'.$request->input('extension');
            $file->move($file_destination, $filename);

            $excel = 'data_file/update_karyawan/' . $filename;
            $rows = Excel::load($excel, function($reader) {
                $reader->noHeading();
                $reader->skipRows(1);
            })->toObject();

            for ($i=0; $i < count($rows); $i++) {
                $update_periode = PlanEmployeeContract::firstOrNew(array('employee_id' => $rows[$i][0]));
                $update_periode->employee_id = $rows[$i][0];
                $update_periode->name = $rows[$i][1];
                $update_periode->department = $rows[$i][2];
                $update_periode->section = $rows[$i][3];
                $update_periode->group = $rows[$i][4];
                $update_periode->sub_group = $rows[$i][5];
                $update_periode->hire_date = $rows[$i][6];
                $update_periode->end_date = $rows[$i][7];
                $update_periode->status = $rows[$i][8];
                $update_periode->save();
            }
            $response = array(
                'status' => true,
                'message' => 'Data Berhasil Di Update'
            );
            return Response::json($response);

        }
        catch(\Exception $e){
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }
    else{
        $response = array(
            'status' => false,
            'message' => 'Please select file to attach'
        );
        return Response::json($response);
    }
}



public function IndexHr(Request $request){
    $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('employee_id', 'name', 'department', 'section', 'group', 'position')
    ->first();

    // $role = User::where('user', Auth::user()->username)
    // ->first();

    $user = '';
    if ((strtoupper(Auth::user()->username) == 'PI0109004') || (strtoupper(Auth::user()->username) == 'PI9905001')) {
        $user = db::select('SELECT employee_id,name FROM employee_syncs where employee_id = "'.$emp_dept->employee_id.'" and `end_date` is null');
        $user_tk = db::select('SELECT employee_id,name FROM employee_syncs where employee_id = "'.$emp_dept->employee_id.'" and `end_date` is null and position != "Operator Contract" and position != "Operator Outsource"');
    }else if ((strtoupper(Auth::user()->username) == 'PI0603019') || (strtoupper(Auth::user()->username) == 'PI2101044')){
        $user = db::select('SELECT employee_id,name FROM employee_syncs where `end_date` is null');
        $user_tk = db::select('SELECT employee_id,name FROM employee_syncs where `end_date` is null and position != "Operator Contract" and position != "Operator Outsource"');
    }else if ($emp_dept->position == 'Leader') {
        if (strtoupper(Auth::user()->username) == 'PI9807008') {
            $user    = db::select('SELECT employee_id,name FROM employee_syncs where `group` = "CL Kensa Group" or `group` = "Incoming · Case Kensa Group" and `end_date` is null');
            $user_tk    = db::select('SELECT employee_id,name FROM employee_syncs where `group` = "CL Kensa Group" or `group` = "Incoming · Case Kensa Group" and `end_date` is null and position != "Operator Contract" and position != "Operator Outsource"');
        }else if(strtoupper(Auth::user()->username) == 'PI9909004'){
            $user    = db::select('SELECT employee_id,name FROM employee_syncs where `group` in ("Spot & Solder Burner A Group", "Kensa Solder Group") and `end_date` is null');
            $user_tk    = db::select('SELECT employee_id,name FROM employee_syncs where `group` in ("Spot & Solder Burner A Group", "Kensa Solder Group") and `end_date` is null and position != "Operator Contract" and position != "Operator Outsource"');
        }else if((strtoupper(Auth::user()->username) == 'PI0004011')||(strtoupper(Auth::user()->username) == 'PI0210005')||(strtoupper(Auth::user()->username) == 'PI9809012')||(strtoupper(Auth::user()->username) == 'PI9810014')||(strtoupper(Auth::user()->username) == 'PI0109002')){
            $user    = db::select('SELECT employee_id,name FROM employee_syncs where section = "Buffing Key Process Section" and `end_date` is null');
            $user_tk    = db::select('SELECT employee_id,name FROM employee_syncs where section = "Buffing Key Process Section" and `end_date` is null and position != "Operator Contract" and position != "Operator Outsource"');
        }else if((strtoupper(Auth::user()->username) == 'PI0203012')){
            $user    = db::select('SELECT employee_id,name FROM employee_syncs where section = "Assembly CL . Tanpo . Case Process Section" and `end_date` is null');
            $user_tk    = db::select('SELECT employee_id,name FROM employee_syncs where section = "Assembly CL . Tanpo . Case Process Section" and `end_date` is null and position != "Operator Contract" and position != "Operator Outsource"');
        }else if((strtoupper(Auth::user()->username) == 'PI0704009') || (strtoupper(Auth::user()->username) == 'PI9811006')){
            $user    = db::select('SELECT employee_id,name FROM employee_syncs where section = "Press and Sanding Process Section" and `end_date` is null');
            $user_tk    = db::select('SELECT employee_id,name FROM employee_syncs where section = "Press and Sanding Process Section" and `end_date` is null and position != "Operator Contract" and position != "Operator Outsource"');
        }else if((strtoupper(Auth::user()->username) == 'PI9807015') || (strtoupper(Auth::user()->username) == 'PI0102003') || (strtoupper(Auth::user()->username) == 'PI9909004')){
            $user    = db::select('SELECT employee_id,name FROM employee_syncs where section = "Handatsuke . Support Process Section" and `end_date` is null');
            $user_tk    = db::select('SELECT employee_id,name FROM employee_syncs where section = "Handatsuke . Support Process Section" and `end_date` is null and position != "Operator Contract" and position != "Operator Outsource"');
        }else if((strtoupper(Auth::user()->username) == 'PI0604009') || (strtoupper(Auth::user()->username) == 'PI9904001')){
            $user    = db::select('SELECT employee_id,name FROM employee_syncs where section = "NC Process Section" and `end_date` is null');
            $user_tk    = db::select('SELECT employee_id,name FROM employee_syncs where section = "NC Process Section" and `end_date` is null and position != "Operator Contract" and position != "Operator Outsource"');
        }else if((strtoupper(Auth::user()->username) == 'PI0008008')){
            $user    = db::select('SELECT employee_id,name FROM employee_syncs where section = "Body Buffing-Barrel Process Section" and `end_date` is null');
            $user_tk    = db::select('SELECT employee_id,name FROM employee_syncs where section = "Body Buffing-Barrel Process Section" and `end_date` is null and position != "Operator Contract" and position != "Operator Outsource"');
        }else if((strtoupper(Auth::user()->username) == 'PI0509003')){
            $user    = db::select('SELECT employee_id,name FROM employee_syncs where section = "Body Parts Process Section" and `end_date` is null');
            $user_tk    = db::select('SELECT employee_id,name FROM employee_syncs where section = "Body Parts Process Section" and `end_date` is null and position != "Operator Contract" and position != "Operator Outsource"');
        }else{  
            $user    = db::select('SELECT employee_id,name FROM employee_syncs where `group` = "'.$emp_dept->group.'" and `end_date` is null');
            $user_tk    = db::select('SELECT employee_id,name FROM employee_syncs where `group` = "'.$emp_dept->group.'" and `end_date` is null and position != "Operator Contract" and position != "Operator Outsource"');
        }
    }else{
        $user    = db::select('SELECT employee_id,name FROM employee_syncs where department = "'.$emp_dept->department.'" and `end_date` is null');
        $user_tk    = db::select('SELECT employee_id,name FROM employee_syncs where department = "'.$emp_dept->department.'" and `end_date` is null and position != "Operator Contract" and position != "Operator Outsource"');
    }
    
    $test = $emp_dept->employee_id;
    $department = db::select('SELECT DISTINCT department FROM employee_syncs where department = "'.$emp_dept->department.'"');
        // $department = db::select('select distinct department from employee_syncs where department is not null order by department asc');
    $section = DB::SELECT("select DISTINCT(section) from employee_syncs where `end_date` IS NULL order by department");
    $pic = db::select('select distinct approver_name from hr_approvals where `status` is null and project_name = "Uang Simpati" or "Tunjangan Keluarga" order by approver_name ASC');

    return view('human_resource.index_hr',  
        array(
            'title' => 'Human Resource Department', 
            'title_jp' => '人材ダッシュボード',
            'user' => $user,
            'department' => $department,
            'section' => $section,
            'emp_dept' => $emp_dept,
            'pic' => $pic,
            'test' => $test,
            'user_tk' => $user_tk
        )
    )->with('page', 'Human Resource');
}

public function GetEmployee(Request $request){
    try {
        $bulan_now = DATE('Y-m');
        if ($request->get('employee_id_us') != null) {



            // $cek = db::select("select employee, created_at from uang_simpatis where employee = '".$request->get('employee_id_us')."' and date(created_at) BETWEEN date(DATE_ADD(now(), INTERVAL -1 year)) and date(now())");
            // if (count($cek) >= 1) {
            //     $response = array(
            //         'status' => false,
            //         'message' => 'Karyawan Sudah Pernah Mengajukan Permohonan Simpati.',
            //         'employee' => ''
            //     );
            //     return Response::json($response);
            // }else{
            if ($request->get('jenis') == null) {
                $emp = DB::SELECT("select employee_id, `name`, sub_group, `group`, section, department, position, grade_code from employee_syncs where `employee_id` = '".$request->get('employee_id_us')."' AND `end_date` IS NULL");
            }else{
                $cek = db::select("select employee, created_at from uang_simpatis where permohonan like '%".$request->get('jenis')."%' and employee = '".$request->get('employee_id_us')."' and date(created_at) BETWEEN date(DATE_ADD(now(), INTERVAL -1 year)) and date(now())");

                if (count($cek) >= 1) {
                    $response = array(
                        'status' => false,
                        'message' => 'Karyawan Sudah Pernah Mengajukan Permohonan Simpati.'
                    );
                    return Response::json($response);
                }else{
                    $response = array(
                        'status' => true,
                        'message' => 'Silahkan Melanjutkan Permohonan Simpati.'
                    );
                    return Response::json($response);
                }
            }



            // $response = array(
            //     'status' => true,
            //     'message' => 'Silahkan Melanjutkan Permohonan Simpati.',
            //     'employee' => $emp

            // );
            // return Response::json($response);
            // }
        }
        else if ($request->get('employee_id_tk') != null) {
            if ($request->get('jenis') == null) {
                $emp = DB::SELECT("select employee_id, `name`, sub_group, `group`, section, department, position, grade_code from employee_syncs where `employee_id` = '".$request->get('employee_id_tk')."' AND `end_date` IS NULL");
            }else{
                $emp = DB::SELECT("select employee_id, `name`, sub_group, `group`, section, department, position, grade_code from employee_syncs where `employee_id` = '".$request->get('employee_id_tk')."' AND `end_date` IS NULL");

                $response = array(
                    'status' => true,
                    'message' => 'Silahkan Melanjutkan Permohonan Tunjangan Keluarga.'

                );
                return Response::json($response);
            }
        }

        if (count($emp) > 0) {
            $response = array(
                'status' => true,
                'message' => 'Success',
                'employee' => $emp

            );
            return Response::json($response);
        }else{
            $response = array(
                'status' => false,
                'message' => 'Failed',
                'employee' => ''
            );
            return Response::json($response);
        }
    }   
    catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function GetHariKerja(Request $request){
    try{
        $emp_no = $request->get('emp_id');
        $bulan = $request->get('bulan');
        $department = $request->get('department');
        $tanggal_awal = $request->get('tanggal_awal');
        $tanggal_akhir = $request->get('tanggal');
        // dd($tanggal_awal, $tanggal_akhir);
        // $tgl_terakhir = date('Y-m-t', strtotime($bulan));

        $resumes = db::connection('sunfish')->select("SELECT A.emp_no, B.Full_name, COUNT ( A.Attend_Code ) AS jumlah_kehadiran FROM VIEW_YMPI_Emp_Attendance A LEFT JOIN VIEW_YMPI_Emp_OrgUnit B ON A.emp_no = B.emp_no WHERE A.emp_no IS NOT NULL AND A.emp_no = '".$emp_no."' AND A.shiftstarttime >= '".$tanggal_awal." 00:00:00' AND A.shiftstarttime <= '".$tanggal_akhir." 23:59:59' AND B.Department IN ( '".$department."' ) AND A.Attend_Code LIKE '%PRS%' GROUP BY A.emp_no, B.Full_name");

        $response = array(
            'status' => true,
            'message' => 'Success',
            'resumes' => $resumes
        );
        return Response::json($response);

    }catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }

}

public function GetSection(Request $request){
    try {
        if ($request->get('department_tp') != null) {
            $sect = db::select("select distinct section from employee_syncs where department = '".$request->get('department_tp')."' and section is not null order by section asc");
        }

        if (count($sect) > 0) {
            $response = array(
                'status' => true,
                'message' => 'Success',
                'section' => $sect
            );
            return Response::json($response);
        }else{
            $response = array(
                'status' => false,
                'message' => 'Failed',
                'section' => ''
            );
            return Response::json($response);
        }
    }   
    catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function AllApprove(Request $request){
    $user    = db::select('SELECT employee_id,name FROM employee_syncs where `end_date` is null');
    $department = db::select('select distinct department from employee_syncs where department is not null order by department asc');
    $section = DB::SELECT("select DISTINCT(section) from employee_syncs where `end_date` IS NULL order by department");

    return view('human_resource.all_approve_hr',  
        array(
            'title' => 'Dashboard Human Resource', 
            'title_jp' => '人材ダッシュボード',
            'user' => $user,
            'department' => $department,
            'section' => $section
        )
    )->with('page', 'Human Resource');
}

    //------------------ Tunjangan Pekerjaan -----------------

public function AddUangPekerjaan(Request $request)
{
    // try {
    $new = [];

    $created_by = Auth::id();
    $created_at = date("Y-m-d H:i:s");

    $code_generator = CodeGenerator::where('note','=','tunjangan kerja')->first();
    $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
    $request_id = $code_generator->prefix . $number;
    $code_generator->index = $code_generator->index+1;


    foreach ($request->get('item') as $value) {
        $pkj = new UangPekerjaan;
        $pkj->department = $request->get('department_tp');
        $pkj->seksi = $request->get('section_tp');
        $pkj->bulan = $request->get('bulan_tp');
        $pkj->request_id = $request_id;
        $pkj->employee = $value['employee_id_tp'];
        $pkj->category = $value['category'];
        $pkj->tanggal_in = $value['tanggal_in'];
        $pkj->tanggal_out = $value['tanggal_tp'];
        $pkj->jumlah_kehadiran = $value['keterangan_tp'];
        $pkj->remark = 'Open';
        $pkj->created_by = $created_by;
        $pkj->created_at = $created_at;
        $pkj->updated_at = $created_at;
        $pkj->save();
        array_push($new, $pkj->id);
    }

    $a = New HrApproval([
        'request_id' => $request_id,
        'status' => "none",
        'approved_at' => date('Y-m-d H:i:s'),
        'remark' => 'none',
        'project_name' => 'Tunjangan Kerja'
    ]);

    $b = New HrApproval([
        'request_id' => $request_id,
        'status' => "none",
        'approved_at' => date('Y-m-d H:i:s'),
        'remark' => 'none',
        'project_name' => 'Tunjangan Kerja'
    ]);

    $ur = EmployeeSync::where('employee_id', Auth::user()->username)->first();
    $user = New HrApproval([
        'request_id' => $request_id,
        'approver_id' => $ur->employee_id,
        'approver_name' => $ur->name,
        'approver_email' => '',
        'status' => "Pembuat",
        'approved_at' => date('Y-m-d H:i:s'),
        'remark' => 'Dibuat Oleh, '.$ur->position.'',
        'project_name' => 'Tunjangan Kerja'
    ]);

    $m = Approver::where('department', '=', $request->get('department_tp'))
    ->where('remark', '=', 'Manager')
    ->first();
    if($m != null){
        $manager = New HrApproval([
            'request_id' => $request_id,
            'approver_id' => $m->approver_id,
            'approver_name' => $m->approver_name,
            'approver_email' => $m->approver_email,
            'status' => "Mengetahui",
            'approved_at' => date('Y-m-d H:i:s'),
            'remark' => 'Mengetahui, Manager',
            'project_name' => 'Tunjangan Kerja'
        ]);
    }
    else{
        $manager = New HrApproval([
            'request_id' => $request_id,
            'status' => "none",
            'approved_at' => date('Y-m-d H:i:s'),
            'remark' => 'Mengetahui, Manager',
            'project_name' => 'Tunjangan Kerja'
        ]);
    }

    $manager_none = New HrApproval([
        'request_id' => $request_id,
        'status' => 'none',
        'approved_at' => date('Y-m-d H:i:s'),
        'remark' => 'none',
        'project_name' => 'Tunjangan Kerja'
    ]);

    $hr = New HrApproval([
        'request_id' => $request_id,
        'approver_id' => 'PI9906002',
        'approver_name' => 'Khoirul Umam',
        'approver_email' => 'khoirul.umam@music.yamaha.com',
        'remark' => 'Menyetujui, HR',
        'project_name' => 'Tunjangan Kerja'
    ]);

    $code_generator->save();
    $a->save();
    $b->save();
    $user->save();
    $manager->save();
    $hr->save();

    $data = db::select("select request_id, up.department, up.employee, bulan, e.name, e.section, e.sub_group, category, tanggal_in, tanggal_out, jumlah_kehadiran from uang_pekerjaans as up left join employee_syncs as e on e.employee_id = up.employee where request_id = '".$request_id."'");

    $approver = HrApproval::
    where('request_id', '=', $request_id)
    ->wherenull('status')
    ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
    ->first();

    $approver_progress = HrApproval::where('request_id', '=', $request_id)
    ->whereNotNull('approver_id')
    ->get();

    $role = User::where('username', Auth::user()->username)
    ->select('username','role_code')
    ->first();

    $data = [
        'data' => $data,
        'approver' => $approver,
        'approver_progress' => $approver_progress,
        'role' => $role
    ];


    $mail_to = [];
    $mail_cc = [];

    array_push($mail_cc, 'ympi-mis-ML@music.yamaha.com');
    // array_push($mail_cc, $manager->approver_email);
    array_push($mail_to, 'khoirul.umam@music.yamaha.com');

    Mail::to($mail_to)->cc($mail_cc)->send(new SendEmail($data, 'request_tunjangan_pekerjaan'));

    return redirect('/human_resource')->with('status', 'Permohonan Tunajnagan Pekerjaan Berhasil')->with('page', 'Human Resources');
}

public function ResumeUangPekerjaan(Request $request){
    $filter = $request->get('filter_tp');

    if ($filter != null) {
        if ($filter == 'sudah') {
            $resume = db::select("select distinct up.department, up.seksi, up.bulan, u.`name` from uang_pekerjaans as up left join users as u on u.id = up.created_by where remark = 'sudah' order by up.bulan ASC");
        }
        else if ($filter == 'belum') {
            $resume = db::select("select distinct up.department, up.seksi, up.bulan, u.`name` from uang_pekerjaans as up left join users as u on u.id = up.created_by where remark = 'belum' order by up.bulan ASC");
        }
    }
    else{
        $resume = db::select("select distinct up.department, up.seksi, up.bulan, u.`name` from uang_pekerjaans as up left join users as u on u.id = up.created_by order by up.bulan ASC");
    }

    $response = array(
      'status' => true,
      'resumes' => $resume
  );
    return Response::json($response);
}

public function ResumeDetailUangPekerjaan(Request $request){

    $resume = db::select("select up.employee, up.department, e.name as nama, up.seksi, up.in_out, up.tanggal, up.keterangan, up.bulan, u.`name` from uang_pekerjaans as up left join employee_syncs as e on e.employee_id = up.employee left join users as u on u.id = up.created_by where up.department = '".$request->get('department')."' and up.bulan = '".$request->get('bulan')."'");


    $response = array(
      'status' => true,
      'resumes' => $resume
  );
    return Response::json($response);
}

public function DetailUangPekerjaan(Request $request, $department, $bulan){

    $uang_pekerjaans = UangPekerjaan::find($department);

    $resumes = UangPekerjaan::select('department', 'seksi', 'employee', 'in_out', 'tanggal', 'keterangan', 'created_by', 'created_at', 'updated_at', 'deleted_at', 'bulan')
    ->where('department', '=', $department)
    ->where('bulan', '=', $bulan)
    ->get();


    return view('human_resource.detail_pekerjaan', array(
        'title' => 'Detail Penerima Tunjangan Pekerjaan', 
        'title_jp' => '監視・管理',

        'uang_pekerjaans' => $uang_pekerjaans,
        'resumes' => $resumes,
        'department' => $department,
        'bulan' => $bulan
    ))->with('page', 'Pekerjaan');
}

public function DownloadPekerjaan($department, $bulan){
    $time = date('Y-m-d H:i:s');
        // $simpatis = UangSimpati::where('remark', '=', 'belum')->get();
    $d = $department;
    $b = $bulan;

    $pekerjaan = UangPekerjaan::where('remark', '=', 'belum')->get();
    foreach ($pekerjaan as $update) {
        $update->remark = 'sudah';
        $update->updated_at = $time;
        $update->save();
    }

    $download = db::select("select up.employee, up.department, e.name as nama, up.seksi, up.in_out, up.tanggal, up.keterangan, up.bulan, u.`name` from uang_pekerjaans as up left join employee_syncs as e on e.employee_id = up.employee left join users as u on u.id = up.created_by where up.department = '".$d."' and up.bulan = '".$b."'");
    $data = array(
        'download' => $download
    );

    ob_clean();

    Excel::create('uang_pekerjaan '.$time, function($excel) use ($data){
        $excel->sheet('1', function($sheet) use ($data) {
          return $sheet->loadView('human_resource.download_pekerjaan', $data);
      });
    })->export('xlsx');
        // return Response::json($response);
    return redirect('/human_resource')->with('status', 'Permohonan Uang Simpati Berhasil Di Download')->with('page', 'Human Resources');
}

    //------------------ Uang Simpati -----------------

public function AddUangSimpati(Request $request){
    $chief = null;
    $nama_chief = null;

    $created_by = Auth::id();
    $created_at = date("Y-m-d H:i:s");

    $files = '';
    $file = new UangSimpati();
    $file_name = '';

    $permohonan = '';
    if ($request->permohonan_us == 'Uang Simpati Kelahiran') {
        if ($request->simp_anak == 'Anak Kembar') {
            $permohonan = $request->permohonan_us.' '.$request->simp_anak.'/700000';
        }else if (($request->simp_anak == 'Anak Ke 1') || ($request->simp_anak == 'Anak Ke 2') || ($request->simp_anak == 'Anak Ke 3')) {
            $permohonan = $request->permohonan_us.' '.$request->simp_anak.'/350000';
        }
    }else if ($request->permohonan_us == 'Uang Simpati Kematian') {
        if ($request->simp_kematian == 'Isteri | Suami') {
            $permohonan = $request->permohonan_us.' '.$request->simp_kematian.'/750000';
        }else if ($request->simp_kematian == 'Anak') {
            $permohonan = $request->permohonan_us.' '.$request->simp_kematian.'/750000';
        }else if ($request->simp_kematian == 'Orang Tua | Mertua') {
            $permohonan = $request->permohonan_us.' '.$request->simp_kematian.'/500000';
        }else if ($request->simp_kematian == 'Pekerja Sendiri') {
            $permohonan = $request->permohonan_us.' '.$request->simp_kematian.'/4000000';
        }
    }else if ($request->permohonan_us == 'Uang Simpati Musibah') {
        if ($request->simp_musibah == 'Kebanjiran') {
            $permohonan = $request->permohonan_us.' '.$request->simp_musibah.'/1250000';
        }else if ($request->simp_musibah == 'Kebakaran Rumah') {
            $permohonan = $request->permohonan_us.' '.$request->simp_musibah.'/1250000';
        }
    }else{
        $permohonan = $request->permohonan_us.'/850000';
    }



    $code_generator = CodeGenerator::where('note','=','uang simpati')->first();
    $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
    $request_id = $code_generator->prefix . $number;
    $code_generator->index = $code_generator->index+1;

    $department = $request->get('department_us');
    $section = $request->get('section_us');

    if ($request->file('surat_nikah_us') != NULL)
    {
        $file = $request->file('surat_nikah_us');
        $file_name = 'LAMPIRAN_'.$request_id.'.jpg';
        $file->move(public_path('hr/uang_simpati/'), $file_name);
    }
    else if ($request->file('surat_akte_us') != NULL)
    {
        $file = $request->file('surat_akte_us');
        $file_name = 'LAMPIRAN_'.$request_id.'.jpg';
        $file->move(public_path('hr/uang_simpati/'), $file_name);
    }
    else if ($request->file('surat_kematian_us') != NULL)
    {
        $file = $request->file('surat_kematian_us');
        $file_name = 'LAMPIRAN_'.$request_id.'.jpg';
        $file->move(public_path('hr/uang_simpati/'), $file_name);

        if($request->file('surat_kematian_kk') != NULL){
            $file1 = $request->file('surat_kematian_kk');
            $file_name1 = 'KK_Kematian_'.$request_id.'.jpg';
            $file1->move(public_path('hr/uang_simpati/'), $file_name1);
        }else{
            return back()->with('error', 'Error, Masukkan File Kartu Keluarga')->with('page', 'Permohonan Tunjangan');
        }
    }
    else if ($request->file('surat_lain_us') != NULL)
    {
        $file = $request->file('surat_lain_us');
        $file_name = 'LAMPIRAN_'.$request_id.'.jpg';
        $file->move(public_path('hr/uang_simpati/'), $file_name);
    }
    // else if ($request->file('surat_kematian_kk') != NULL)
    // {
    //     $file = $request->file('surat_kematian_kk');
    //     $file_name = 'KK_Kematian_'.$request_id.'.jpg';
    //     $file->move(public_path('hr/uang_simpati/'), $file_name);
    // }
    else
    {
        // $fp = NULL;
        return back()->with('error', 'Error, Masukkan File Permohonan Simpati')->with('page', 'Permohonan Tunjangan');
    }

    $department = $request->get('department_us');
    $section = $request->get('section_us');
    $position = $request->get('position_us');

    if (($position == 'Staff') || ($position == 'Senior Staff')) {
        $chf = db::select("select employee_id, `name` from employee_syncs where position = 'chief' and section = '".$section."'");

        if ($chf != null)
        {
            foreach ($chf as $cf)
            {
                $chief = $cf->employee_id;
                $nama_chief = $cf->name;
            }
        }
        else{
            if($section == 'Software Section') {
                $chief = 'PI0103002';
                $nama_chief = 'Agus Yulianto';
            }
        }
    }
    else if(($position == 'Operator Outsource') || ($position == 'Operator') || ($position == 'Senior Operator') || ($position == 'Operator Contract') || ($position == 'Sub Leader') || ($position == 'Leader')){
        $chf = db::select("select employee_id, `name` from employee_syncs where position = 'foreman' and section = '".$section."'");

        if ($chf != null)
        {
            foreach ($chf as $cf)
            {
                $chief = $cf->employee_id;
                $nama_chief = $cf->name;
            }
        }

        else if ($chf == null) {
            if($department == 'Educational Instrument (EI) Department') {
                $chief = 'PI1110001';
                $nama_chief = 'Eko Prasetyo Wicaksono';
            }
            else if($department == 'Woodwind Instrument - Welding Process (WI-WP) Department'){
                $chief = 'PI9809008';
                $nama_chief = 'Mey Indah Astuti';
            }
            else if($section == 'Press and Sanding Process Section'){
                $chief = 'PI9903003';
                $nama_chief = 'Slamet Hariadi';
            }
            else if($section == 'Assembly CL . Tanpo . Case Process Section'){
                $chief = 'PI9707008';
                $nama_chief = 'Imbang Prasetyo';
            }
            else if($section == 'Assembly CL . Tanpo . Case Process Section'){
                $chief = 'PI9707008';
                $nama_chief = 'Imbang Prasetyo';
            }
            else if($section == 'Body Parts Process Section'){
                $chief = 'PI0004003';
                $nama_chief = 'Hadi Firmansyah';
            }else if($section == 'Body Buffing-Barrel Process Section'){
                $chief = 'PI9707010';
                $nama_chief = 'Mawan Sujianto';
            }

        }
    }

    $ur = EmployeeSync::where('employee_id', $request->employee_id_us)->first();
    $user = New HrApproval([
        'request_id' => $request_id,
        'approver_id' => $ur->employee_id,
        'approver_name' => $ur->name,
        'approver_email' => '',
        'status' => "Pemohon",
        'approved_at' => date('Y-m-d H:i:s'),
        'remark' => 'Pemohon',
        'project_name' => 'Uang Simpati'
    ]);

    // $l = User::where('id', $created_by)->first();\
    $l = EmployeeSync::where('position', '=', 'Leader')->where('group', $request->group_us)->whereNull('end_date')->select('employee_id', 'name', 'position')->first();

    // $l_pos = EmployeeSync::where('employee_id', $l->username)->first();
    // if($l != null){
    $ldr = User::where('username', Auth::user()->username)
    ->select('username','name')
    ->first();

    $leader = New HrApproval([
        'request_id' => $request_id,
        'approver_id' => $ldr->username,
        'approver_name' => $ldr->name,
        'approver_email' => '',
        'status' => "Menyetujui",
        'approved_at' => date('Y-m-d H:i:s'),
        'remark' => 'Menyetujui, Leader',
        'project_name' => 'Uang Simpati'
    ]);

        // $select_leader = Employee::where('employee_id',$l->employee_id)->first();

        // if(substr($select_leader->wa_number, 0, 1) == '+' ){
        //     $phone = substr($select_leader->wa_number, 1, 15);
        // }
        // else if(substr($select_leader->wa_number, 0, 1) == '0'){
        //     $phone = "62".substr($select_leader->wa_number, 1, 15);
        // }
        // else{
        //     $phone = $select_leader->wa_number;
        // }

        // $request_name = str_replace(" ", "%20", $ur->name);
        // $jenis_tunjangan = explode('/', $permohonan);

        // $message = 'Karyawan%20'.$request_name.'%0AMengajukan%20Permohonan%20Tunjangan%20'.$jenis_tunjangan[0].'%0A%0A-YMPI%20MIS%20Dept.-';

       //  $curl = curl_init();

       //  curl_setopt_array($curl, array(
       //     CURLOPT_URL => 'https://app.whatspie.com/api/messages',
    // CURLOPT_SSL_VERIFYHOST => FALSE,
    //             CURLOPT_SSL_VERIFYPEER => FALSE,
       //     CURLOPT_RETURNTRANSFER => true,
       //     CURLOPT_ENCODING => '',
       //     CURLOPT_MAXREDIRS => 10,
       //     CURLOPT_TIMEOUT => 0,
       //     CURLOPT_FOLLOWLOCATION => true,
       //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
       //     CURLOPT_CUSTOMREQUEST => 'POST',
       //     CURLOPT_POSTFIELDS => 'receiver='.$phone.'&device=6281130561777&message='.$message.'&type=chat',
       //     CURLOPT_POSTFIELDS => 'receiver=628980198771&device=6281130561777&message='.$message.'&type=chat',
       //     CURLOPT_HTTPHEADER => array(
       //         'Accept: application/json',
       //         'Content-Type: application/x-www-form-urlencoded',
       //         'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
       //     ),
       // ));

    // }else{
    //     $leader = New HrApproval([
    //         'request_id' => $request_id,
    //         'status' => "none",
    //         'project_name' => 'Uang Simpati'
    //     ]);
    // }

    $leader_none = New HrApproval([
        'request_id' => $request_id,
        'status' => 'none',
        'approved_at' => date('Y-m-d H:i:s'),
        'remark' => 'none',
        'project_name' => 'Uang Simpati'
    ]);

    $at = User::where('username', $chief)->first();
    $pos = EmployeeSync::where('employee_id', $chief)->first();
    if($at != null){
        $atasan = New HrApproval([
            'request_id' => $request_id,
            'approver_id' => $chief,
            'approver_name' => $at->name,
            'approver_email' => $at->email,
            'status' => "Waiting",
            'approved_at' => null,
            'remark' => 'Mengetahui, '.$pos->position,
            'project_name' => 'Uang Simpati'
        ]);
    }
    else{
        $atasan = New HrApproval([
            'request_id' => $request_id,
            'status' => "none",
            'approved_at' => null,
            'remark' => 'none',
            'project_name' => 'Uang Simpati'
        ]);
    }

    $atasan_none = New HrApproval([
        'request_id' => $request_id,
        'status' => 'none',
        'approved_at' => null,
        'remark' => 'none',
        'project_name' => 'Uang Simpati'
    ]);

    $m = Approver::where('department', '=', $request->get('department_us'))
    ->where('remark', '=', 'Manager')
    ->first();
    if($m != null){
        $manager = New HrApproval([
            'request_id' => $request_id,
            'approver_id' => $m->approver_id,
            'approver_name' => $m->approver_name,
            'approver_email' => $m->approver_email,
            'status' => "Waiting",
            'approved_at' => null,
            'remark' => 'Mengetahui, Manager',
            'project_name' => 'Uang Simpati'
        ]);
    }
    else{
        $manager = New HrApproval([
            'request_id' => $request_id,
            'status' => "none",
            'approved_at' => null,
            'remark' => 'Mengetahui, Manager',
            'project_name' => 'Uang Simpati'
        ]);
    }

    $manager_none = New HrApproval([
        'request_id' => $request_id,
        'status' => 'none',
        'approved_at' => null,
        'remark' => 'none',
        'project_name' => 'Uang Simpati'
    ]);

    $hr = New HrApproval([
        'request_id' => $request_id,
        'approver_id' => 'PI9906002',
        'approver_name' => 'Khoirul Umam',
        'approver_email' => 'khoirul.umam@music.yamaha.com',
        'remark' => 'Menyetujui, HR',
        'project_name' => 'Uang Simpati',
        'status' => "Waiting"
    ]);

    $tahun = date('Y');
    $ada = db::select('select count(employee) as count from uang_simpatis where employee = "'.$request->employee_id_us.'" and permohonan = "Uang Simpati Musibah" and DATE_FORMAT(created_at, "%Y") = "'.$tahun.'"');
    if ($ada[0]->count == '0') {
        $db_insert = new UangSimpati([
            'request_id'    => $request_id,
            'employee'      => $request->employee_id_us,
            'sub_group'     => $request->sub_group_us, 
            'group'         => $request->group_us, 
            'seksi'         => $request->section_us, 
            'department'    => $request->department_us, 
            'jabatan'       => $request->position_us, 
            'permohonan'    => $permohonan,
            'lampiran'      => $file_name, 
            'created_by'    => $created_by,
            'created_at'    => $created_at,
            'remark'        => 'Open'
        ]);
        $code_generator->save();
        $db_insert->save();
        $test = EmployeeSync::select('position')->where('employee_id', Auth::user()->username)->first();

        if ($test->position == 'Director' || $test->position == 'General Manager' || $test->position == 'Deputy General Manager' || $test->position == 'Foreman' || $test->position == 'Chief' || $test->position == 'Coordinator' || $test->position == 'Staff' || $test->position == 'Senior Staff') {
            $user->save();
            $leader_none->save();
            $atasan->save();
            $manager->save();
            $hr->save();
        }else if($test->position == 'Manager'){
            $user->save();
            $leader_none->save();
            $atasan_none->save();
            $manager_none->save();
            $hr->save();
        }else if($test->position == 'Leader'){
            $user->save();
            $leader->save();
            $atasan->save();
            $manager->save();
            $hr->save();
        }else{
            $user->save();
            $leader->save();
            $atasan->save();
            $manager->save();
            $hr->save();
        }
        // $user->save();
        // $leader->save();
        // $atasan->save();
        // $manager->save();
        // $hr->save();

        $mail_to = [];
        // $mail_cc = [];
        $mail_cc_mis = [];

        // array_push($mail_cc, $atasan->approver_email);
        // array_push($mail_cc, $m->approver_email);
        array_push($mail_cc_mis, 'ympi-mis-ML@music.yamaha.com');
        array_push($mail_to, $atasan->approver_email);
        // array_push($mail_to, 'khoirul.umam@music.yamaha.com');

        $data = db::select("select us.id, us.request_id, us.employee, e.`name`, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, u.`name` as pembuat, us.remark from uang_simpatis as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by where request_id = '".$request_id."'");

        $approver = HrApproval::
        where('request_id', '=', $request_id)
        ->where('status', 'Waiting')
        ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
        ->orderBy('id', 'ASC')
        ->first();

        $approver_progress = HrApproval::where('request_id', '=', $request_id)
        ->whereNotNull('approver_id')
        ->get();

        $role = User::where('username', Auth::user()->username)
        ->select('username','role_code')
        ->first();

        $data = [
            'data' => $data,
            'approver' => $approver,
            'approver_progress' => $approver_progress,
            'role' => $role
        ];


        if ($request->permohonan_us == 'Uang Simpati Kematian') {
            // if ($test->position == 'Director' || $test->position == 'General Manager' || $test->position == 'Deputy General Manager' || $test->position == 'Manager') {
            Mail::to($mail_to)->bcc($mail_cc_mis)->send(new SendEmail($data, 'request_uang_simpati_kematian'));
            // }else{
            //     Mail::to($mail_to)->bcc($mail_cc_mis)->send(new SendEmail($data, 'request_uang_simpati_kematian'));
            // }
        }else{
            // if ($test->position == 'Director' || $test->position == 'General Manager' || $test->position == 'Deputy General Manager' || $test->position == 'Manager') {
            Mail::to($mail_to)->bcc($mail_cc_mis)->send(new SendEmail($data, 'request_uang_simpati'));
            // }else{
            //     Mail::to($mail_to)->bcc($mail_cc_mis)->send(new SendEmail($data, 'request_uang_simpati'));
            // }
        }

        // Mail::to($mail_cc)
        // ->send(new SendEmail($data, 'done_uang_simpati'));
    }else{
        return redirect('/human_resource/gagal')->with('status', 'Permohonan Uang Simpati Berhasil')->with('page', 'Human Resources');
    }
    return redirect('/human_resource')->with('status', 'Permohonan Uang Simpati Berhasil')->with('page', 'Human Resources');
}

public function ResendEmailTunjangan(Request $request, $request_id){
    try{
        $project_name = $request->get('project_name');

        $mail_to = [];
        $mail_cc = [];

        $penerima = HrApproval::where('request_id', $request_id)->where('status', 'Waiting')->orderBy('id', 'asc')->take(1)->first();

        array_push($mail_cc, 'ympi-mis-ML@music.yamaha.com');
        array_push($mail_to, $penerima->approver_email);

        if ($project_name == 'Tunjangan Keluarga') {
            $data = db::select("select us.id, us.request_id, us.employee, e.`name`, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, u.`name` as pembuat, us.remark from uang_keluargas as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by where request_id = '".$request_id."'");

            $approver = HrApproval::
            where('request_id', '=', $request_id)
            ->where('status', 'Waiting')
            ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
            ->first();

            $approver_progress = HrApproval::where('request_id', '=', $request_id)
            ->whereNotNull('approver_id')
            ->get();

            $role = User::where('username', Auth::user()->username)
            ->select('username','role_code')
            ->first();

            $data = [
                'data' => $data,
                'approver' => $approver,
                'approver_progress' => $approver_progress,
                'role' => $role
            ];

            Mail::to($mail_to)->bcc($mail_cc)->send(new SendEmail($data, 'request_tunjangan_keluarga'));

        }else if ($project_name == 'Uang Simpati'){
            $data = db::select("select us.id, us.request_id, us.employee, e.`name`, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, u.`name` as pembuat, us.remark from uang_simpatis as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by where request_id = '".$request_id."'");

            $permohonan = explode("/", $data[0]->permohonan);

            $approver = HrApproval::
            where('request_id', '=', $request_id)
            ->where('status', 'Waiting')
            ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
            ->first();

            $approver_progress = HrApproval::where('request_id', '=', $request_id)
            ->whereNotNull('approver_id')
            ->get();

            $role = User::where('username', Auth::user()->username)
            ->select('username','role_code')
            ->first();

            $data = [
                'data' => $data,
                'approver' => $approver,
                'approver_progress' => $approver_progress,
                'role' => $role
            ];

            if ($permohonan[0] == 'Uang Simpati Kematian Orang Tua | Mertua') {
                Mail::to($mail_to)->bcc($mail_cc)->send(new SendEmail($data, 'request_uang_simpati_kematian'));
            }else{
                Mail::to($mail_to)->bcc($mail_cc)->send(new SendEmail($data, 'request_uang_simpati'));
            }

        }
        $response = array(
          'status' => true,
          'message' => 'Email berhasil terkirim.',
      );
        return Response::json($response);
    }catch(\Exception $e){
        $response = array(
          'status' => false,
          'message' => $e->getMessage(),
      );
        return Response::json($response);
    }
}

public function DeletePermohonanTunjangan(Request $request, $request_id){
    try{
        $project_name = $request->get('project_name');
        if ($project_name == 'Tunjangan Keluarga') {
            $delete_list = UangKeluarga::where('request_id', $request_id)->forceDelete();
            $delete_approver = HrApproval::where('request_id', $request_id)->forceDelete();
        }else if ($project_name == 'Uang Simpati'){
            $delete_list = UangSimpati::where('request_id', $request_id)->forceDelete();
            $delete_approver = HrApproval::where('request_id', $request_id)->forceDelete();
        }
        $response = array(
          'status' => true,
          'message' => 'Permohonan '.$project_name.' Berhasil Di Hapus.',
      );
        return Response::json($response);
    }catch(\Exception $e){
        $response = array(
          'status' => false,
          'message' => $e->getMessage(),
      );
        return Response::json($response);
    }
}

public function IndexHrGagal(Request $request){
    return view('human_resource.gagal',  
        array(
            'title' => 'Human Resource Department', 
            'title_jp' => '人材ダッシュボード'
        )
    )->with('page', 'Human Resource');
}

public function App_Simpati_1(Request $request, $id){     
    try{
        $created_at = date("Y-m-d H:i:s");
        $simpati = UangSimpati::find($id);

        $chief = null;
        $nama_chief = null;
        $manager = null;
        $nama_manager = null;
        $dgm = null;
        $nama_dgm = null;

        $manager = db::select("select employee_id, `name` from employee_syncs where position = 'Manager' and department ='".$simpati->department."'"); 
        if ($manager != null)
        {
            foreach ($manager as $mgr)
            {
                $manager = $mgr->employee_id;
                $nama_manager = $mgr->name;
            }
        }
        else
        {
            if ($simpati->department == 'Woodwind Instrument - Welding Process (WI-WP) Department') {
                $manager = 'PI0108010';
                $nama_manager = 'Yudi Abtadipa';
            }
            elseif 
                ($simpati->department == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
                    $manager = 'PI9906002';
                    $nama_manager = 'Khoirul Umam';
                }
                elseif 
                    ($simpati->department == 'Purchasing Control Department') {
                        $manager = 'PI9807014';
                        $nama_manager = 'Imron Faizal';
                    }
                    else{
                        $manager = 'PI0109004';
                        $nama_manager = 'Budhi Apriyanto'; 
                    }
                }


                $simpati->posisi = 'mgr';
                $simpati->date_atasan_1 = $created_at;
                $simpati->atasan_2 = $manager;
                $simpati->save();

                $mails = "select distinct u.email from uang_simpatis as us join users as u on us.atasan_2 = u.username where us.id =".$simpati->id;
                $mailtoo = DB::select($mails);

                $isimail = "select us.id, us.employee, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, us.lampiran, DATE_FORMAT(us.created_at, '%d-%m-%Y') as tanggal, us.posisi,e.`name`, u.`name` as lead, atasan_1.`name` as atasan_1, atasan_2.`name` as atasan_2, DATE_FORMAT(us.date_atasan_1, '%d-%m-%Y') as tanggal_atasan_1, DATE_FORMAT(us.date_atasan_2, '%d-%m-%Y') as tanggal_atasan_2 from uang_simpatis as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by left join employee_syncs as atasan_1 on atasan_1.employee_id = us.atasan_1 left join employee_syncs as atasan_2 on atasan_2.employee_id = us.atasan_2 where us.id =".$simpati->id;

                $uang_simpati = db::select($isimail);
                Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($uang_simpati, 'permohonan_uang_simpati'));

                return redirect('/human_resource')->with('status', 'Permohonan Uang Simpati Berhasil')->with('page', 'Human Resources'); 

            }
            catch (QueryException $e){
                return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }

        public function App_Simpati_2(Request $request, $id){     
            try{
                $created_at = date("Y-m-d H:i:s");
                $simpati = UangSimpati::find($id);

                $simpati->posisi = 'hr';
                $simpati->date_atasan_2 = $created_at;
                $simpati->remark = 'belum';
                $simpati->save();

                $mails = "select distinct email from users where username = 'PI0603019'";
                $mailtoo = DB::select($mails);

                $isimail = "select us.id, us.employee, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, us.lampiran, DATE_FORMAT(us.created_at, '%d-%m-%Y') as tanggal, us.posisi,e.`name`, u.`name` as lead, atasan_1.`name` as atasan_1, atasan_2.`name` as atasan_2, DATE_FORMAT(us.date_atasan_1, '%d-%m-%Y') as tanggal_atasan_1, DATE_FORMAT(us.date_atasan_2, '%d-%m-%Y') as tanggal_atasan_2 from uang_simpatis as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by left join employee_syncs as atasan_1 on atasan_1.employee_id = us.atasan_1 left join employee_syncs as atasan_2 on atasan_2.employee_id = us.atasan_2 where us.id =".$simpati->id;

                $uang_simpati = db::select($isimail);
                Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($uang_simpati, 'permohonan_uang_simpati'));


                return redirect('/human_resource')->with('status', 'Permohonan Uang Simpati Berhasil')->with('page', 'Human Resources'); 

            }
            catch (QueryException $e){
                return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }

        public function ResumeApprovalTunjangan(Request $request){
                // $filter = $request->get('filter_us');

            // $tanggal = $request->get('date');
            // $time = date('d-m-Y H;i;s');

            $dept = EmployeeSync::where('employee_id', Auth::user()->username)
            ->select('employee_id', 'name', 'department', 'section', 'group', 'position')
            ->first();

            $role = User::where('username', Auth::user()->username)
            ->select('role_code')
            ->first();

            $value = $request->get('value');
            $pic_progress = $request->get('pic');

            $app = '';
            if ($pic_progress != null) {
              $apprr = json_encode($pic_progress);
              $appr = str_replace(array("[","]"),array("(",")"),$apprr);

              $app = ' in ('.$appr.')';
          } else {
              $app = '';
          }

          if (($role->role_code == 'S-MIS') || ($role->role_code == 'S-HR') || ($role->role_code == 'M-HR')) {
            //   if ($pic_progress != "") {
            //     $resume = db::select('SELECT DISTINCT
            //         p.request_id,
            //         ( SELECT GROUP_CONCAT( COALESCE(a.approver_name,"") ) FROM hr_approvals a WHERE a.request_id = p.request_id) AS approver,
            //         ( SELECT GROUP_CONCAT( COALESCE(a.approver_id,"") ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver_nik,
            //         ( SELECT GROUP_CONCAT( COALESCE(a.`status`,"") ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS `status`,
            //         ( SELECT GROUP_CONCAT( COALESCE(DATE_FORMAT( a.approved_at, "%d-%m-%Y" ),"") ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approved_at,
            //         ra.project_name 
            //         FROM
            //         uang_simpatis AS p
            //         LEFT JOIN hr_approvals AS ra ON ra.request_id = p.request_id 
            //         WHERE
            //         p.remark = "Open" 
            //         AND (
            //             SELECT COALESCE
            //             ( a.approver_name, "" ) 
            //             FROM
            //             hr_approvals a 
            //             WHERE
            //             a.request_id = p.request_id 
            //             AND a.`status` IS NULL 
            //             LIMIT 1 
            //             ) = "'.$pic_progress.'" UNION ALL
            //         SELECT DISTINCT
            //         p.request_id,
            //         ( SELECT GROUP_CONCAT( COALESCE(a.approver_name,"") ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver,
            //         ( SELECT GROUP_CONCAT( COALESCE(a.approver_id,"") ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver_nik,
            //         ( SELECT GROUP_CONCAT( COALESCE(a.`status`,"") ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS `status`,
            //         ( SELECT GROUP_CONCAT( COALESCE(DATE_FORMAT( a.approved_at, "%d-%m-%Y" ),"") ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approved_at,
            //         ra.project_name 
            //         FROM
            //         uang_keluargas AS p
            //         LEFT JOIN hr_approvals AS ra ON ra.request_id = p.request_id 
            //         WHERE
            //         p.remark = "Open" 
            //         AND (
            //         SELECT COALESCE
            //         ( a.approver_name, "" ) 
            //         FROM
            //         hr_approvals a 
            //         WHERE
            //         a.request_id = p.request_id 
            //         AND a.`status` IS NULL 
            //         LIMIT 1 
            //     ) = "'.$pic_progress.'"');
            // }else{
            $resume = db::select('SELECT DISTINCT
                p.request_id,
                ( SELECT GROUP_CONCAT( COALESCE ( a.approver_name, "" ) ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver,
                ( SELECT GROUP_CONCAT( COALESCE ( a.approver_id, "" ) ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver_nik,
                ( SELECT GROUP_CONCAT( COALESCE ( a.`status`, "" ) ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS `status`,
                (
                    SELECT
                    GROUP_CONCAT( COALESCE ( DATE_FORMAT( a.approved_at, "%d-%m-%Y" ), "" ) ) 
                    FROM
                    hr_approvals a 
                    WHERE
                    a.request_id = p.request_id 
                    ) AS approved_at,
                ra.project_name 
                FROM
                uang_simpatis AS p
                LEFT JOIN hr_approvals AS ra ON ra.request_id = p.request_id 
                WHERE
                p.remark = "Open" UNION ALL
                SELECT DISTINCT
                p.request_id,
                ( SELECT GROUP_CONCAT( COALESCE ( a.approver_name, "" ) ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver,
                ( SELECT GROUP_CONCAT( COALESCE ( a.approver_id, "" ) ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver_nik,
                ( SELECT GROUP_CONCAT( COALESCE ( a.`status`, "" ) ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS `status`,
                (
                    SELECT
                    GROUP_CONCAT( COALESCE ( DATE_FORMAT( a.approved_at, "%d-%m-%Y" ), "" ) ) 
                    FROM
                    hr_approvals a 
                    WHERE
                    a.request_id = p.request_id 
                    ) AS approved_at,
                ra.project_name 
                FROM
                uang_keluargas AS p
                LEFT JOIN hr_approvals AS ra ON ra.request_id = p.request_id 
                WHERE
                p.remark = "Open"UNION ALL
                SELECT DISTINCT
                p.request_id,
                ( SELECT GROUP_CONCAT( COALESCE ( a.approver_name, "" ) ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver,
                ( SELECT GROUP_CONCAT( COALESCE ( a.approver_id, "" ) ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver_nik,
                ( SELECT GROUP_CONCAT( COALESCE ( a.`status`, "" ) ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS `status`,
                (
                    SELECT
                    GROUP_CONCAT( COALESCE ( DATE_FORMAT( a.approved_at, "%d-%m-%Y" ), "" ) ) 
                    FROM
                    hr_approvals a 
                    WHERE
                    a.request_id = p.request_id 
                    ) AS approved_at,
                ra.project_name 
                FROM
                uang_pekerjaans AS p
                LEFT JOIN hr_approvals AS ra ON ra.request_id = p.request_id 
                WHERE
                p.remark = "Open"');
            // }
        }else{
            // if ($pic_progress != "") {
            //     $resume = db::select('SELECT DISTINCT
            //         p.request_id,
            //         ( SELECT GROUP_CONCAT( COALESCE(a.approver_name,"") ) FROM hr_approvals a WHERE a.request_id = p.request_id) AS approver,
            //         ( SELECT GROUP_CONCAT( COALESCE(a.approver_id,"") ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver_nik,
            //         ( SELECT GROUP_CONCAT( COALESCE(a.`status`,"") ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS `status`,
            //         ( SELECT GROUP_CONCAT( COALESCE(DATE_FORMAT( a.approved_at, "%d-%m-%Y" ),"") ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approved_at,
            //         ra.project_name 
            //         FROM
            //         uang_simpatis AS p
            //         LEFT JOIN hr_approvals AS ra ON ra.request_id = p.request_id 
            //         WHERE
            //         p.remark = "Open" 
            //         AND (
            //             SELECT COALESCE
            //             ( a.approver_name, "" ) 
            //             FROM
            //             hr_approvals a 
            //             WHERE
            //             a.request_id = p.request_id 
            //             AND a.`status` IS NULL 
            //             LIMIT 1 
            //             ) = "'.$pic_progress.'" and p.department = "'.$dept->department.'" UNION ALL
            //         SELECT DISTINCT
            //         p.request_id,
            //         ( SELECT GROUP_CONCAT( COALESCE(a.approver_name,"") ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver,
            //         ( SELECT GROUP_CONCAT( COALESCE(a.approver_id,"") ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver_nik,
            //         ( SELECT GROUP_CONCAT( COALESCE(a.`status`,"") ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS `status`,
            //         ( SELECT GROUP_CONCAT( COALESCE(DATE_FORMAT( a.approved_at, "%d-%m-%Y" ),"") ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approved_at,
            //         ra.project_name 
            //         FROM
            //         uang_keluargas AS p
            //         LEFT JOIN hr_approvals AS ra ON ra.request_id = p.request_id 
            //         WHERE
            //         p.remark = "Open" 
            //         AND (
            //         SELECT COALESCE
            //         ( a.approver_name, "" ) 
            //         FROM
            //         hr_approvals a 
            //         WHERE
            //         a.request_id = p.request_id 
            //         AND a.`status` IS NULL 
            //         LIMIT 1 
            //     ) = "'.$pic_progress.'" and p.department = "'.$dept->department.'"');
            // }else{
            $resume = db::select('SELECT DISTINCT
                p.request_id,
                ( SELECT GROUP_CONCAT( COALESCE(a.approver_name,"") ) FROM hr_approvals a WHERE a.request_id = p.request_id) AS approver,
                ( SELECT GROUP_CONCAT( COALESCE(a.approver_id,"") ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver_nik,
                ( SELECT GROUP_CONCAT( COALESCE(a.`status`,"") ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS `status`,
                ( SELECT GROUP_CONCAT( COALESCE(DATE_FORMAT( a.approved_at, "%d-%m-%Y" ),"") ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approved_at,
                ra.project_name 
                FROM
                uang_simpatis AS p
                LEFT JOIN hr_approvals AS ra ON ra.request_id = p.request_id 
                WHERE
                p.remark = "Open" and p.department = "'.$dept->department.'"
                UNION ALL
                SELECT DISTINCT
                p.request_id,
                ( SELECT GROUP_CONCAT( COALESCE(a.approver_name,"") ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver,
                ( SELECT GROUP_CONCAT( COALESCE(a.approver_id,"") ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver_nik,
                ( SELECT GROUP_CONCAT( COALESCE(a.`status`,"") ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS `status`,
                ( SELECT GROUP_CONCAT( COALESCE(DATE_FORMAT( a.approved_at, "%d-%m-%Y" ),"") ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approved_at,
                ra.project_name 
                FROM
                uang_keluargas AS p
                LEFT JOIN hr_approvals AS ra ON ra.request_id = p.request_id 
                WHERE
                p.remark = "Open" and p.department = "'.$dept->department.'"');
            // }
        }



       //      if ($role->role_code == 'MIS') {
       //          if ($value == null) {
       //              $resume = db::select('select DISTINCT p.request_id, (SELECT GROUP_CONCAT( a.approver_name ) FROM hr_approvals a WHERE a.request_id = p.request_id) as approver, ( SELECT GROUP_CONCAT( a.approver_id ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver_nik, (SELECT GROUP_CONCAT( a.`status` ) FROM hr_approvals a WHERE a.request_id = p.request_id) as `status`, (SELECT GROUP_CONCAT( DATE_FORMAT(a.approved_at,"%d-%m-%Y") ) FROM hr_approvals a WHERE a.request_id = p.request_id) as approved_at, ra.project_name from uang_simpatis as p 
       //                  left join hr_approvals as ra on ra.request_id = p.request_id where p.remark = "Open" union all
       //                  select DISTINCT p.request_id, (SELECT GROUP_CONCAT( a.approver_name ) FROM hr_approvals a WHERE a.request_id = p.request_id) as approver, ( SELECT GROUP_CONCAT( a.approver_id ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver_nik, (SELECT GROUP_CONCAT( a.`status` ) FROM hr_approvals a WHERE a.request_id = p.request_id) as `status`, (SELECT GROUP_CONCAT( DATE_FORMAT(a.approved_at,"%d-%m-%Y") ) FROM hr_approvals a WHERE a.request_id = p.request_id) as approved_at, ra.project_name from uang_keluargas as p
       //                  left join hr_approvals as ra on ra.request_id = p.request_id where p.remark = "Open"');
       //          }else{
       //             $resume = db::select('select DISTINCT p.request_id, (SELECT GROUP_CONCAT( a.approver_name ) FROM hr_approvals a WHERE a.request_id = p.request_id) as approver, ( SELECT GROUP_CONCAT( a.approver_id ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver_nik, (SELECT GROUP_CONCAT( a.`status` ) FROM hr_approvals a WHERE a.request_id = p.request_id) as `status`, (SELECT GROUP_CONCAT( DATE_FORMAT(a.approved_at,"%d-%m-%Y") ) FROM hr_approvals a WHERE a.request_id = p.request_id) as approved_at, ra.project_name from uang_simpatis as p 
       //              left join hr_approvals as ra on ra.request_id = p.request_id where ra.project_name = "'.$value.'" and p.remark = "Open" union all
       //              select DISTINCT p.request_id, (SELECT GROUP_CONCAT( a.approver_name ) FROM hr_approvals a WHERE a.request_id = p.request_id) as approver, ( SELECT GROUP_CONCAT( a.approver_id ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver_nik, (SELECT GROUP_CONCAT( a.`status` ) FROM hr_approvals a WHERE a.request_id = p.request_id) as `status`, (SELECT GROUP_CONCAT( DATE_FORMAT(a.approved_at,"%d-%m-%Y") ) FROM hr_approvals a WHERE a.request_id = p.request_id) as approved_at, ra.project_name from uang_keluargas as p
       //              left join hr_approvals as ra on ra.request_id = p.request_id where ra.project_name = "'.$value.'" and p.remark = "Open"');
       //         }
       //     }else{
       //      if ($value == null) {
       //          $resume = db::select('select DISTINCT p.request_id, (SELECT GROUP_CONCAT( a.approver_name ) FROM hr_approvals a WHERE a.request_id = p.request_id) as approver, ( SELECT GROUP_CONCAT( a.approver_id ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver_nik, (SELECT GROUP_CONCAT( a.`status` ) FROM hr_approvals a WHERE a.request_id = p.request_id) as `status`, (SELECT GROUP_CONCAT( DATE_FORMAT(a.approved_at,"%d-%m-%Y") ) FROM hr_approvals a WHERE a.request_id = p.request_id) as approved_at, ra.project_name from uang_simpatis as p 
       //              left join hr_approvals as ra on ra.request_id = p.request_id where p.department = "'.$dept->department.'" and p.remark = "Open" union all
       //              select DISTINCT p.request_id, (SELECT GROUP_CONCAT( a.approver_name ) FROM hr_approvals a WHERE a.request_id = p.request_id) as approver, ( SELECT GROUP_CONCAT( a.approver_id ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver_nik, (SELECT GROUP_CONCAT( a.`status` ) FROM hr_approvals a WHERE a.request_id = p.request_id) as `status`, (SELECT GROUP_CONCAT( DATE_FORMAT(a.approved_at,"%d-%m-%Y") ) FROM hr_approvals a WHERE a.request_id = p.request_id) as approved_at, ra.project_name from uang_keluargas as p
       //              left join hr_approvals as ra on ra.request_id = p.request_id where p.department = "'.$dept->department.'" and p.remark = "Open"');
       //      }else{
       //         $resume = db::select('select DISTINCT p.request_id, (SELECT GROUP_CONCAT( a.approver_name ) FROM hr_approvals a WHERE a.request_id = p.request_id) as approver, ( SELECT GROUP_CONCAT( a.approver_id ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver_nik, (SELECT GROUP_CONCAT( a.`status` ) FROM hr_approvals a WHERE a.request_id = p.request_id) as `status`, (SELECT GROUP_CONCAT( DATE_FORMAT(a.approved_at,"%d-%m-%Y") ) FROM hr_approvals a WHERE a.request_id = p.request_id) as approved_at, ra.project_name from uang_simpatis as p 
       //          left join hr_approvals as ra on ra.request_id = p.request_id where ra.project_name = "'.$value.'" and p.department = "'.$dept->department.'" and p.remark = "Open" union all
       //          select DISTINCT p.request_id, (SELECT GROUP_CONCAT( a.approver_name ) FROM hr_approvals a WHERE a.request_id = p.request_id) as approver, ( SELECT GROUP_CONCAT( a.approver_id ) FROM hr_approvals a WHERE a.request_id = p.request_id ) AS approver_nik, (SELECT GROUP_CONCAT( a.`status` ) FROM hr_approvals a WHERE a.request_id = p.request_id) as `status`, (SELECT GROUP_CONCAT( DATE_FORMAT(a.approved_at,"%d-%m-%Y") ) FROM hr_approvals a WHERE a.request_id = p.request_id) as approved_at, ra.project_name from uang_keluargas as p
       //          left join hr_approvals as ra on ra.request_id = p.request_id where ra.project_name = "'.$value.'" and p.department = "'.$dept->department.'" and p.remark = "Open"');
       //     }
       // }

            // dd($value);
            // if ($tanggal == null) {
                // $resume = db::select("select us.id, us.employee, e.`name`, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, u.`name` as pembuat, us.remark from uang_simpatis as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by where remark = 'belum' or remark is null order by us.created_at desc");
            // }else{
            //     $resume = db::select("select us.id, us.employee, e.`name`, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, u.`name` as pembuat, us.created_at, us.remark from uang_simpatis as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by 
            //         where remark = 'belum' or remark is null and DATE_FORMAT(us.created_at, '%Y-%m-%d') = '".$tanggal."' 
            //         order by us.created_at desc");
            // }

        $response = array(
          'status' => true,
          'resumes' => $resume
      );
        return Response::json($response);
    }

    public function DetailUangSimpati(Request $request, $id){

        $uang_simpatis = UangSimpati::find($id);

        $resumes = UangSimpati::select('uang_simpatis.id', 'employee', 'uang_simpatis.sub_group', 'uang_simpatis.group', 'uang_simpatis.seksi', 'uang_simpatis.department', 'uang_simpatis.jabatan', 'permohonan', 'lampiran', 'uang_simpatis.created_by', 'uang_simpatis.created_at', 'uang_simpatis.updated_at', 'uang_simpatis.deleted_at', 'posisi', 
            db::raw('e.name as name'),
            db::raw('DATE_FORMAT(uang_simpatis.created_at, "%d-%m-%Y") as tanggal'),
            db::raw('lead.name as lead'), 
            db::raw('atasan_1.name as atasan_1'),
            db::raw('DATE_FORMAT(uang_simpatis.date_atasan_1, "%d-%m-%Y") as tanggal_atasan_1'),
            db::raw('atasan_2.name as atasan_2'),
            db::raw('DATE_FORMAT(uang_simpatis.date_atasan_2, "%d-%m-%Y") as tanggal_atasan_2'))
        ->leftJoin(db::raw('employee_syncs as e'), 'e.employee_id', '=', 'uang_simpatis.employee')
        ->leftJoin(db::raw('users as lead'), 'lead.id', '=', 'uang_simpatis.created_by')
        ->leftJoin(db::raw('employee_syncs as atasan_1'), 'atasan_1.employee_id', '=', 'uang_simpatis.atasan_1')
        ->leftJoin(db::raw('employee_syncs as atasan_2'), 'atasan_2.employee_id', '=', 'uang_simpatis.atasan_2')
        ->where('uang_simpatis.id', '=', $id)
        ->get();


        return view('human_resource.detail_simpati', array(
            'title' => 'Detail Pengajuan Uang Simpati', 
            'title_jp' => '監視・管理',

            'uang_simpatis' => $uang_simpatis,
            'resumes' => $resumes
        ))->with('page', 'Simpati');
    }

    public function DownloadSimpati(Request $request){
        $tanggal = $request->get('date');
        $time = date('Y-m-d H:i:s');

        if ($tanggal == null) {
            $resumes = UangSimpati::select(
                'uang_simpatis.employee', 'uang_simpatis.sub_group', 'uang_simpatis.group', 'uang_simpatis.seksi', 'uang_simpatis.department', 'uang_simpatis.jabatan', 'uang_simpatis.permohonan', 'uang_simpatis.lampiran','uang_simpatis.created_by', 'uang_simpatis.created_at', 'uang_simpatis.updated_at', 'uang_simpatis.deleted_at', 'uang_simpatis.posisi', 'uang_simpatis.atasan_1', 'uang_simpatis.date_atasan_1', 'uang_simpatis.atasan_2', 'uang_simpatis.date_atasan_2', 'uang_simpatis.remark',
                db::raw('e.name as nama'), db::raw('u.name as pembuat'))
            ->leftJoin(db::raw('employee_syncs as e'), 'uang_simpatis.employee', '=', 'e.employee_id')
            ->leftJoin(db::raw('users as u'), 'uang_simpatis.created_by', '=', 'u.id')
            ->where('uang_simpatis.remark', '=', 'belum')
            ->orderBy('uang_simpatis.created_at', 'asc')
            ->get();
        }else{
            $resumes = UangSimpati::select(
                'uang_simpatis.employee', 'uang_simpatis.sub_group', 'uang_simpatis.group', 'uang_simpatis.seksi', 'uang_simpatis.department', 'uang_simpatis.jabatan', 'uang_simpatis.permohonan', 'uang_simpatis.lampiran','uang_simpatis.created_by', 'uang_simpatis.created_at', 'uang_simpatis.updated_at', 'uang_simpatis.deleted_at', 'uang_simpatis.posisi', 'uang_simpatis.atasan_1', 'uang_simpatis.date_atasan_1', 'uang_simpatis.atasan_2', 'uang_simpatis.date_atasan_2', 'uang_simpatis.remark',
                db::raw('e.name as nama'), db::raw('u.name as pembuat'))
            ->leftJoin(db::raw('employee_syncs as e'), 'uang_simpatis.employee', '=', 'e.employee_id')
            ->leftJoin(db::raw('users as u'), 'uang_simpatis.created_by', '=', 'u.id')
            ->where('uang_simpatis.remark', '=', 'belum')
            ->where(db::raw('date(created_at)'),'=', $tanggal)
            ->orderBy('uang_simpatis.created_at', 'asc')
            ->get();
        }
        $data = array(
            'resumes' => $resumes
        );

                // return redirect('/human_resource')->with('status', 'Permohonan Uang Simpati Berhasil Di Download')->with('page', 'Human Resources');

        $simpati = UangSimpati::where('remark','=', 'belum')->get();
        foreach ($simpati as $update) {
            $update->remark = 'sudah';
            $update->updated_at = $time;
            $update->save();
        }
        ob_clean();

        Excel::create('Permohonan Uang Simpati '.$time, function($excel) use ($data){
            $excel->sheet('HR', function($sheet) use ($data) {
              return $sheet->loadView('human_resource.download_simpati', $data);
          });
        })->export('xls');
    }

    //training filosofi

    public function indexTrainingFilosofi()
    {
        $tr_status = DB::connection('ympimis_2')
        ->table('tr_filosofi_questions')
        ->select('tr_filosofi_questions.status')
        ->where('tr_filosofi_questions.status',1)
        ->first();

        $tr_filos_question = DB::connection('ympimis_2')
        ->table('tr_filosofi_questions')
        ->get();

        $check_emp_tr = DB::connection('ympimis_2')
        ->table('tr_filosofi_answers')
        ->where('tr_filosofi_answers.employee_id',Auth::user()->username)
        ->first();

        $empsync = DB::select('select * from employee_syncs where employee_id = "'.Auth::user()->username.'" and end_date is null limit 1');

        $employee_sync = db::table('employee_syncs')->leftJoin('departments', 'departments.department_name', '=', 'employee_syncs.department')->select('departments.department_name', 'departments.department_shortname')->where('employee_id', '=', Auth::user()->username)->first();


        return view('training.training_filosofi.training_filosofi_index', array(
            'tr_status' => $tr_status,
            'tr_filos_question' => $tr_filos_question,
            'tgl' => date('Y-m-d H:i:s'),
            'check_emp_tr' => $check_emp_tr,
            'empsync' => $empsync,
            'page' => 'Training Filosofi Yamaha',
            'employee_sync' => $employee_sync

        ));
    }

    public function inpuTrFilosofi(Request $request)
    {
        try {
            $employee_id = $request->get('employee_id');
            $question = $request->get('question');
            $answer = $request->get('answer');
            $department_name = $request->get('department_name');
            $department_shortname = $request->get('department_shortname');

            $TrCheck = DB::connection('ympimis_2')
            ->table('tr_filosofi_answers')
            ->where('tr_filosofi_answers.employee_id',$employee_id)
            ->first();

            if (count($TrCheck) > 0) {
                $response = array(
                    'status' => false,
                    'datas' => 'Anda sudah pernah Melakukan Training Filosofi Yamaha'
                );
                return Response::json($response);   
            }else{
                $create_filosofi = db::connection('ympimis_2')->table('tr_filosofi_answers')
                ->insert([
                 'employee_id' => $employee_id,
                 'question' => join('_',$question),
                 'answer' => join('_',$answer),
                 'department_name' => $department_name,
                 'department_shortname' => $department_shortname,
                 'created_by' => 1,
                 'created_at' => date('Y-m-d H:i:s'),
                 'updated_at' => date('Y-m-d H:i:s')
             ]);

                $response = array(
                    'status' => true,
                    'datas' => 'Berhasil Disimpan',
                    'datetime' => date('Y-m-d H:i:s')

                );
                return Response::json($response);
            }
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'datas' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function updateTrFilososiOpen(Request $request)
    {
        try {
            $st = $request->get('status');


            if($st == "open"){
                $st_op = "1";
            }else{
                $st_op = "0";
            }

            $st_training = db::connection('ympimis_2')->table('tr_filosofi_questions')
            ->where('deleted_at', '=', null)
            ->update([
                'status' => $st_op,

            ]);

            $response = array(
                'status' => true,
                'datas' => 'Berhasil diupdate'
            );
            return Response::json($response);

        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'datas' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function indexMonitoringFilosofi()
    {
        $title = 'Training Filosofi Yamaha';
        $title_jp = '';

        $departments = db::table('departments')->orderBy('department_name', 'ASC')->get();

        return view('training.training_filosofi.monitoring_filosofi_index', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'departments' => $departments,

        ))->with('page', 'Monitoring Filosofi Yamaha')->with('head','Monitoring Filosofi Yamaha');
    }

    public function fetchTrainingFilosofi(Request $request)
    {
        try {

            $departments = DB::SELECT("SELECT DISTINCT
                ( department ),
                department_name,
                department_shortname 
                FROM
                employee_syncs
                LEFT JOIN departments ON departments.department_name = employee_syncs.department
                WHERE departments.department_name IS NOT NULL
                ORDER BY
                department");

            $tr_filos_answers = DB::connection('ympimis_2')
            ->table('tr_filosofi_answers')
            ->get();   
              

            $emp_data = DB::SELECT("SELECT
                employee_id,
                name,
                position,
                department,
                section,
                `group`,
                sub_group,
                department_name,
                department_shortname 
                FROM
                departments
                JOIN employee_syncs ON employee_syncs.department = departments.department_name 
                WHERE
                employee_syncs.end_date IS NULL AND employee_id NOT LIKE '%OS%'
                AND departments.department_name != 'General Manager - Expatriate (GME)'");

            $employees = EmployeeSync::where('end_date',null)->get();



            $response = array(
                'status' => true,
                'departments' => $departments,
                'tr_filos_answers' => $tr_filos_answers,
                'employees' => $emp_data


            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

//------------------ Tunjangan Keluarga -----------------

    public function AddUangKeluarga(Request $request){
        $chief = null;
        $nama_chief = null;

        $created_by = Auth::id();
        $created_at = date("Y-m-d H:i:s");
        $file = new UangKeluarga();

        $pmh = '';

        if ($request->get('tunj_kel') == 'Tunjangan Pasangan')
        {
            $pmh = 'Tunjangan Pasangan';
        }
        else if ($request->get('tunj_kel') == 'Tunjangan Anak')
        {
            $pmh = 'Tunjangan '.$request->get('anak_tk');
        }
        else
        {
            $pmh = NULL;
        }

        $code_generator = CodeGenerator::where('note','=','tunjangan keluarga')->first();
        $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
        $request_id = $code_generator->prefix . $number;
        $code_generator->index = $code_generator->index+1;

        $files = '';
        $file = new UangKeluarga();

        if ($request->file('surat_nikah_tk') != NULL)
        {
            if ($request->file('surat_nikah_tk') == NULL) {
                return back()->with('error', 'Error, Masukkan File Surat Nikah')->with('page', 'Permohonan Tunjangan');
            }else{
                $file = $request->file('surat_nikah_tk');
                $file_name = 'LAMPIRAN_'.$request_id.'.jpg';
                $file->move(public_path('hr/uang_keluarga/'), $file_name);
            }
        }
        else if ($request->file('surat_akte_tk') != NULL)
        {   
            if ($request->file('surat_akte_tk') == NULL) {
                return back()->with('error', 'Error, Masukkan File Akta Kelahiran')->with('page', 'Permohonan Tunjangan');
            }else{
                $file = $request->file('surat_akte_tk');
                $file_name = 'LAMPIRAN_'.$request_id.'.jpg';
                $file->move(public_path('hr/uang_keluarga/'), $file_name);
            }
        }

        if ($request->file('surat_lain_tk') == null) {
         return back()->with('error', 'Error, Masukkan File Kartu Keluarga')->with('page', 'Permohonan Tunjangan');
     }else{
        $file1 = $request->file('surat_lain_tk');
        $file_name1 = 'KK_'.$request_id.'.jpg';
        $file1->move(public_path('hr/uang_keluarga/'), $file_name1);
    }

    $department = $request->get('department_tk');
    $section = $request->get('section_tk');
    $position = $request->get('position_tk');

    if (($position == 'Staff') || ($position == 'Senior Staff')) {
        $chf = db::select("select employee_id, `name` from employee_syncs where position = 'chief' and section = '".$section."'");

        if ($chf != null)
        {
            foreach ($chf as $cf)
            {
                $chief = $cf->employee_id;
                $nama_chief = $cf->name;
            }
        }
        else{
            if($section == 'Software Section') {
                $chief = 'PI0103002';
                $nama_chief = 'Agus Yulianto';
            }
        }
    }
    else if(($position == 'Operator Outsource') || ($position == 'Operator') || ($position == 'Senior Operator') || ($position == 'Operator Contract') || ($position == 'Sub Leader')){

        $chf = db::select("select employee_id, `name` from employee_syncs where position = 'foreman' and section = '".$section."'");

        if ($chf != null)
        {
            foreach ($chf as $cf)
            {
                $chief = $cf->employee_id;
                $nama_chief = $cf->name;
            }
        }

        else if ($chf == null) {
            if($department == 'Educational Instrument (EI) Department') {
                $chief = 'PI1110001';
                $nama_chief = 'Eko Prasetyo Wicaksono';
            }
            else if($department == 'Woodwind Instrument - Welding Process (WI-WP) Department'){
                $chief = 'PI9809008';
                $nama_chief = 'Mey Indah Astuti';
            }
            else if($section == 'Press and Sanding Process Section'){
                $chief = 'PI9903003';
                $nama_chief = 'Slamet Hariadi';
            }
            else if($section == 'Assembly CL . Tanpo . Case Process Section'){
                $chief = 'PI9707008';
                $nama_chief = 'Imbang Prasetyo';
            }else if($section == 'Body Buffing-Barrel Process Section'){
                $chief = 'PI9707010';
                $nama_chief = 'Mawan Sujianto';
            }

        }
    }



    $ur = EmployeeSync::where('employee_id', $request->employee_id_tk)->first();
    $user = New HrApproval([
        'request_id' => $request_id,
        'approver_id' => $request->employee_id_tk,
        'approver_name' => $ur->name,
        'approver_email' => '',
        'status' => "Pemohon",
        'approved_at' => date('Y-m-d H:i:s'),
        'remark' => 'Pemohon',
        'project_name' => 'Tunjangan Keluarga'
    ]);

        // $l = User::where('id', $created_by)->first();\
    $l = EmployeeSync::where('employee_id', Auth::user()->username)->select('employee_id', 'name', 'position')->first();
        // $l_pos = EmployeeSync::where('employee_id', $l->username)->first();
    if($l != null){
        $leader = New HrApproval([
            'request_id' => $request_id,
            'approver_id' => $l->employee_id,
            'approver_name' => $l->name,
            'approver_email' => '',
            'status' => "Menyetujui",
            'approved_at' => date('Y-m-d H:i:s'),
            'remark' => 'Menyetujui, '.$l->position,
            'project_name' => 'Tunjangan Keluarga'
        ]);

            // $select_leader = Employee::where('employee_id',$l->employee_id)->first();

            // if(substr($select_leader->wa_number, 0, 1) == '+' ){
            //     $phone = substr($select_leader->wa_number, 1, 15);
            // }
            // else if(substr($select_leader->wa_number, 0, 1) == '0'){
            //     $phone = "62".substr($select_leader->wa_number, 1, 15);
            // }
            // else{
            //     $phone = $select_leader->wa_number;
            // }

            // $request_name = str_replace(" ", "%20", $ur->name);

            // $message = 'Karyawan%20'.$request_name.'%0AMengajukan%20Permohonan%20Tunjangan%20'.$pmh.'%0A%0A-YMPI%20MIS%20Dept.-';

       //  $curl = curl_init();

       //  curl_setopt_array($curl, array(
       //     CURLOPT_URL => 'https://app.whatspie.com/api/messages',
        // CURLOPT_SSL_VERIFYHOST => FALSE,
        //         CURLOPT_SSL_VERIFYPEER => FALSE,
       //     CURLOPT_RETURNTRANSFER => true,
       //     CURLOPT_ENCODING => '',
       //     CURLOPT_MAXREDIRS => 10,
       //     CURLOPT_TIMEOUT => 0,
       //     CURLOPT_FOLLOWLOCATION => true,
       //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
       //     CURLOPT_CUSTOMREQUEST => 'POST',
       //     CURLOPT_POSTFIELDS => 'receiver='.$phone.'&device=6281130561777&message='.$message.'&type=chat',
       //     CURLOPT_POSTFIELDS => 'receiver=628980198771&device=6281130561777&message='.$message.'&type=chat',
       //     CURLOPT_HTTPHEADER => array(
       //         'Accept: application/json',
       //         'Content-Type: application/x-www-form-urlencoded',
       //         'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
       //     ),
       // ));
        
    }else{
        $leader = New HrApproval([
            'request_id' => $request_id,
            'status' => "none",
            'project_name' => 'Tunjangan Keluarga'
        ]);
    }

    $leader_none = New HrApproval([
        'request_id' => $request_id,
        'status' => 'none',
        'approved_at' => null,
        'remark' => 'none',
        'project_name' => 'Tunjangan Keluarga'
    ]);

        // $l = User::where('id', $created_by)->first();
        // $l_pos = EmployeeSync::where('employee_id', $l->username)->first();
        // $leader = New HrApproval([
        //     'request_id' => $request_id,
        //     'approver_id' => $l->username,
        //     'approver_name' => $l->name,
        //     'approver_email' => '',
        //     'status' => "Approved",
        //     'approved_at' => date('Y-m-d H:i:s'),
        //     'remark' => 'Menyetujui, '.$l_pos->position,
        //     'project_name' => 'Tunjangan Keluarga'
        // ]);

    $at = User::where('username', $chief)->first();
    $pos = EmployeeSync::where('employee_id', $chief)->first();
    if($at != null){
        $atasan = New HrApproval([
            'request_id' => $request_id,
            'approver_id' => $chief,
            'approver_name' => $at->name,
            'approver_email' => $at->email,
            'status' => "Waiting",
            'approved_at' => null,
            'remark' => 'Mengetahui, '.$pos->position,
            'project_name' => 'Tunjangan Keluarga'
        ]);
    }
    else{
        $atasan = New HrApproval([
            'request_id' => $request_id,
            'status' => "none",
            'approved_at' => null,
            'remark' => 'none',
            'project_name' => 'Tunjangan Keluarga'
        ]);
    }

    $m = Approver::where('department', '=', $request->get('department_tk'))
    ->where('remark', '=', 'Manager')
    ->first();
    if($m != null){
        $manager = New HrApproval([
            'request_id' => $request_id,
            'approver_id' => $m->approver_id,
            'approver_name' => $m->approver_name,
            'approver_email' => $m->approver_email,
            'status' => "Waiting",
            'approved_at' => null,
            'remark' => 'Mengetahui, Manager',
            'project_name' => 'Tunjangan Keluarga'
        ]);
    }
    else{
        $manager = New HrApproval([
            'request_id' => $request_id,
            'status' => "none",
            'approved_at' => null,
            'remark' => 'Mengetahui, Manager',
            'project_name' => 'Tunjangan Keluarga'
        ]);
    }

    $manager_none = New HrApproval([
        'request_id' => $request_id,
        'status' => 'none',
        'approved_at' => date('Y-m-d H:i:s'),
        'remark' => 'none',
        'project_name' => 'Tunjangan Keluarga'
    ]);

    $hr = New HrApproval([
        'request_id' => $request_id,
        'approver_id' => 'PI9906002',
        'approver_name' => 'Khoirul Umam',
        'approver_email' => 'khoirul.umam@music.yamaha.com',
        'remark' => 'Menyetujui, HR',
        'project_name' => 'Tunjangan Keluarga',
        'status' => "Waiting",
        'approved_at' => null
    ]);

    $nama_pp = "";
    $tempat_pp = "";
    $tanggal_pp = "";
    $ket_pp = "";

    if ($pmh == 'Tunjangan Pasangan') {
        $nama_pp = strtoupper($request->get('nama_pasangan'));
        $tempat_pp = strtoupper($request->get('tempat_lahir'));
        $tanggal_pp = $request->get('tgl_lahir');
        $ket_pp = $request->get('ket');
    }else{
        $nama_pp = strtoupper($request->get('anak'));
        $tempat_pp = strtoupper($request->get('tempat_lahir_anak'));
        $tanggal_pp = $request->get('tgl_lahir_anak');
        $ket_pp = $request->get('ket_anak');
    }

    $db_insert = new UangKeluarga([
        'request_id'    => $request_id,
        'employee'      => $request->employee_id_tk,
        'sub_group'     => $request->sub_group_tk, 
        'group'         => $request->group_tk, 
        'seksi'         => $request->section_tk, 
        'department'    => $request->department_tk, 
        'jabatan'       => $request->position_tk, 
        'permohonan'    => $pmh,
        'lampiran'      => $file_name, 
        'created_by'    => $created_by,
        'created_at'    => $created_at,
        'remark'        => 'Open',
        'nama_pasangan' => $nama_pp,
        'tempat_lahir'  => $tempat_pp,
        'tanggal_lahir' => $tanggal_pp,
        'status' => $ket_pp
    ]);


    $code_generator->save();
    $db_insert->save();
    $test = EmployeeSync::select('position')->where('employee_id', Auth::user()->username)->first();

    if ($test->position == 'Director' || $test->position == 'General Manager' || $test->position == 'Deputy General Manager' || $test->position == 'Foreman' || $test->position == 'Chief' || $test->position == 'Coordinator' || $test->position == 'Staff' || $test->position == 'Senior Staff') {
        $user->save();
        $leader_none->save();
        $atasan->save();
        $manager->save();
        $hr->save();
    }else if($test->position == 'Manager'){
        $user->save();
        $leader_none->save();
        $atasan->save();
        $manager_none->save();
        $hr->save();
    }else if($test->position == 'Leader'){
        $user->save();
        $leader->save();
        $atasan->save();
        $manager->save();
        $hr->save();
    }else{
        $user->save();
        $leader->save();
        $atasan->save();
        $manager->save();
        $hr->save();
    }

        // $user->save();
        // $leader->save();
        // $atasan->save();
        // $manager->save();
        // $hr->save();
    // dd($user->approver_name, $leader->approver_name, $atasan->approver_email, $manager->approver_name, $hr->approver_name);

    // $mail_to = [];
    $mail_cc = [];
    // $mail_test = [];

    // array_push($mail_cc, $atasan->approver_email);
    // array_push($mail_cc, $m->approver_email);
    array_push($mail_cc, 'ympi-mis-ML@music.yamaha.com');
    // array_push($mail_to, $atasan->approver_email);
    // array_push($mail_to, 'khoirul.umam@music.yamaha.com');
    // array_push($mail_test, 'ympi-mis-ML@music.yamaha.com');

    $data = db::select("select us.id, us.request_id, us.employee, e.`name`, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, u.`name` as pembuat, us.remark from uang_keluargas as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by where request_id = '".$request_id."'");

    $approver = HrApproval::
    where('request_id', '=', $request_id)
    ->where('status', 'Waiting')
    ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
    ->orderBy('id', 'ASC')
    ->first();

    $approver_progress = HrApproval::where('request_id', '=', $request_id)
    ->whereNotNull('approver_id')
    ->get();

    $role = User::where('username', Auth::user()->username)
    ->select('username','role_code')
    ->first();

    $data = [
        'data' => $data,
        'approver' => $approver,
        'approver_progress' => $approver_progress,
        'role' => $role
    ];

    Mail::to($atasan->approver_email)->bcc($mail_cc)->send(new SendEmail($data, 'request_tunjangan_keluarga'));

        // Mail::to($mail_cc)
        // ->send(new SendEmail($data, 'done_tunjangan_keluarga'));

                // $mails = "select distinct u.email from uang_keluargas as uk join users as u on uk.atasan_1 = u.username where uk.id =".$db_insert->id;
                // $mailtoo = DB::select($mails);

                // $isimail = "select uk.id, uk.employee, uk.sub_group, uk.`group`, uk.seksi, uk.department, uk.jabatan, uk.permohonan, uk.lampiran, DATE_FORMAT(uk.created_at, '%d-%m-%Y') as tanggal, uk.posisi,e.`name`, u.`name` as lead, atasan_1.`name` as atasan_1, atasan_2.`name` as atasan_2, DATE_FORMAT(uk.date_atasan_1, '%d-%m-%Y') as tanggal_atasan_1, DATE_FORMAT(uk.date_atasan_2, '%d-%m-%Y') as tanggal_atasan_2 from uang_keluargas as uk left join employee_syncs as e on e.employee_id = uk.employee left join users as u on u.id = uk.created_by left join employee_syncs as atasan_1 on atasan_1.employee_id = uk.atasan_1 left join employee_syncs as atasan_2 on atasan_2.employee_id = uk.atasan_2 where uk.id =".$db_insert->id;
                // $uang_keluarga = db::select($isimail);
                // Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($uang_keluarga, 'permohonan_uang_keluarga'));

    return redirect('/human_resource')->with('status', 'Permohonan Tunjangan Keluarga Berhasil Diajukan')->with('page', 'Human Resources');
}

public function App_Keluarga_1(Request $request, $id){     
    try{
        $created_at = date("Y-m-d H:i:s");
        $keluarga = UangKeluarga::find($id);

        $chief = null;
        $nama_chief = null;
        $manager = null;
        $nama_manager = null;
        $dgm = null;
        $nama_dgm = null;


        $manager = db::select("select employee_id, `name` from employee_syncs where position = 'Manager' and department ='".$keluarga->department."'"); 
        if ($manager != null)
        {
            foreach ($manager as $mgr)
            {
                $manager = $mgr->employee_id;
                $nama_manager = $mgr->name;
            }
        }
        else
        {
            if ($keluarga->department == 'Woodwind Instrument - Welding Process (WI-WP) Department') {
                $manager = 'PI0108010';
                $nama_manager = 'Yudi Abtadipa';
            }
            elseif 
                ($keluarga->department == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
                    $manager = 'PI9906002';
                    $nama_manager = 'Khoirul Umam';
                }
                elseif 
                    ($keluarga->department == 'Purchasing Control Department') {
                        $manager = 'PI9807014';
                        $nama_manager = 'Imron Faizal';
                    }
                    else{
                        $manager = 'PI0109004';
                        $nama_manager = 'Budhi Apriyanto'; 
                    }
                }


                $keluarga->posisi = 'mgr';
                $keluarga->date_atasan_1 = $created_at;
                $keluarga->atasan_2 = $manager;
                $keluarga->save();
                return redirect('/human_resource')->with('status', 'Permohonan Tunjangan Keluarga Berhasil')->with('page', 'Human Resources'); 

            }
            catch (QueryException $e){
                return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }

        public function App_Keluarga_2(Request $request, $id){     
            try{
                $created_at = date("Y-m-d H:i:s");
                $keluarga = UangKeluarga::find($id);

                $keluarga->posisi = 'hr';
                $keluarga->remark = 'belum';
                $keluarga->date_atasan_2 = $created_at;
                $keluarga->save();
                return redirect('/human_resource')->with('status', 'Permohonan Uang Simpati Berhasil')->with('page', 'Human Resources'); 

            }
            catch (QueryException $e){
                return back()->with('error', 'Error')->with('page', 'Mutasi Error');
            }
        }

        public function ResumeUangKeluarga(Request $request){
            $tanggal = $request->get('datem');
            $time = date('d-m-Y H;i;s');

            if ($tanggal == null) {
                $resume = db::select("select us.id, us.employee, e.`name`, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, u.`name` as pembuat, us.remark from uang_keluargas as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by where remark = 'belum' or remark is null order by us.created_at desc");
            }else{
                $resume = db::select("select us.id, us.employee, e.`name`, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, u.`name` as pembuat, us.created_at, us.remark from uang_keluargas as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by 
                    where remark = 'belum' or remark is null and DATE_FORMAT(us.created_at, '%Y-%m-%d') = '".$tanggal."' 
                    order by us.created_at desc");
            }


            $response = array(
              'status' => true,
              'resumes' => $resume
          );
            return Response::json($response);
        }

        public function DetailUangKeluarga(Request $request, $id){

            $uang_keluargas = UangKeluarga::find($id);

            $resumes = UangKeluarga::select('uang_keluargas.id', 'employee', 'uang_keluargas.sub_group', 'uang_keluargas.group', 'uang_keluargas.seksi', 'uang_keluargas.department', 'uang_keluargas.jabatan', 'permohonan', 'lampiran', 'uang_keluargas.created_by', 'uang_keluargas.created_at', 'uang_keluargas.updated_at', 'uang_keluargas.deleted_at', 'posisi', 
                db::raw('e.name as name'),
                db::raw('DATE_FORMAT(uang_keluargas.created_at, "%d-%m-%Y") as tanggal'),
                db::raw('lead.name as lead'), 
                db::raw('atasan_1.name as atasan_1'),
                db::raw('DATE_FORMAT(uang_keluargas.date_atasan_1, "%d-%m-%Y") as tanggal_atasan_1'),
                db::raw('atasan_2.name as atasan_2'),
                db::raw('DATE_FORMAT(uang_keluargas.date_atasan_2, "%d-%m-%Y") as tanggal_atasan_2'))
            ->leftJoin(db::raw('employee_syncs as e'), 'e.employee_id', '=', 'uang_keluargas.employee')
            ->leftJoin(db::raw('users as lead'), 'lead.id', '=', 'uang_keluargas.created_by')
            ->leftJoin(db::raw('employee_syncs as atasan_1'), 'atasan_1.employee_id', '=', 'uang_keluargas.atasan_1')
            ->leftJoin(db::raw('employee_syncs as atasan_2'), 'atasan_2.employee_id', '=', 'uang_keluargas.atasan_2')
            ->where('uang_keluargas.id', '=', $id)
            ->get();

            return view('human_resource.detail_keluarga', array(
                'title' => 'Detail Pengajuan Tunjangan Keluarga', 
                'title_jp' => '監視・管理',

                'uang_keluargas' => $uang_keluargas,
                'resumes' => $resumes
            ))->with('page', 'Tunjangan Keluarga');
        }

        public function DownloadKeluarga(Request $request){
            $tanggal = $request->get('datem');
            $time = date('Y-m-d H:i:s');

            if ($tanggal == null) {
                $resumes = UangKeluarga::select(
                    'uang_keluargas.employee', 'uang_keluargas.sub_group', 'uang_keluargas.group', 'uang_keluargas.seksi', 'uang_keluargas.department', 'uang_keluargas.jabatan', 'uang_keluargas.permohonan', 'uang_keluargas.lampiran','uang_keluargas.created_by', 'uang_keluargas.created_at', 'uang_keluargas.updated_at', 'uang_keluargas.deleted_at', 'uang_keluargas.posisi', 'uang_keluargas.atasan_1', 'uang_keluargas.date_atasan_1', 'uang_keluargas.atasan_2', 'uang_keluargas.date_atasan_2', 'uang_keluargas.remark',
                    db::raw('e.name as nama'), db::raw('u.name as pembuat'))
                ->leftJoin(db::raw('employee_syncs as e'), 'uang_keluargas.employee', '=', 'e.employee_id')
                ->leftJoin(db::raw('users as u'), 'uang_keluargas.created_by', '=', 'u.id')
                ->where('uang_keluargas.remark', '=', 'belum')
                ->orderBy('uang_keluargas.created_at', 'asc')
                ->get();
            }else{
                $resumes = UangKeluarga::select(
                    'uang_keluargas.employee', 'uang_keluargas.sub_group', 'uang_keluargas.group', 'uang_keluargas.seksi', 'uang_keluargas.department', 'uang_keluargas.jabatan', 'uang_keluargas.permohonan', 'uang_keluargas.lampiran','uang_keluargas.created_by', 'uang_keluargas.created_at', 'uang_keluargas.updated_at', 'uang_keluargas.deleted_at', 'uang_keluargas.posisi', 'uang_keluargas.atasan_1', 'uang_keluargas.date_atasan_1', 'uang_keluargas.atasan_2', 'uang_keluargas.date_atasan_2', 'uang_keluargas.remark',
                    db::raw('e.name as nama'), db::raw('u.name as pembuat'))
                ->leftJoin(db::raw('employee_syncs as e'), 'uang_keluargas.employee', '=', 'e.employee_id')
                ->leftJoin(db::raw('users as u'), 'uang_keluargas.created_by', '=', 'u.id')
                ->where('uang_keluargas.remark', '=', 'belum')
                ->where(db::raw('date(created_at)'),'=', $tanggal)
                ->orderBy('uang_keluargas.created_at', 'asc')
                ->get();
            }
            $data = array(
                'resumes' => $resumes
            );

                        // return redirect('/human_resource')->with('status', 'Permohonan Uang Simpati Berhasil Di Download')->with('page', 'Human Resources');

            $simpati = UangKeluarga::where('remark','=', 'belum')->get();
            foreach ($simpati as $update) {
                $update->remark = 'sudah';
                $update->updated_at = $time;
                $update->save();
            }
            ob_clean();

            Excel::create('Permohonan Uang Tunjangan Keluarga '.$time, function($excel) use ($data){
                $excel->sheet('HR', function($sheet) use ($data) {
                  return $sheet->loadView('human_resource.download_keluarga', $data);
              });
            })->export('xls');
        }
        public function indexLeaveRequest()
        {
            $user = EmployeeSync::where('employee_id',Auth::user()->username)->first();
            if($user){
                if ($user->department == 'General Affairs Department' || $user->department == 'Management Information System Department' || $user->department == 'Human Resources Department' || $user->department == null) {
                    $emp = EmployeeSync::where('end_date',null)->get();
                }
                else{
                    $emp = EmployeeSync::where('department',$user->department)->where('end_date',null)->get();
                }
            }
            else{
                $emp = EmployeeSync::where('end_date',null)->get();
                $user = array();
            }

            return view('human_resource.leave_request.index', array(
                'title' => 'Surat Izin Keluar', 
                'title_jp' => '外出申請書',
                'employees' => $emp,
                'user' => $user,
                'user2' => $user,
                'role_code' => Auth::user()->role_code,
            ))->with('page', 'Human Resource');
        }

        public function fetchLeaveRequestEmployees(Request $request)
        {
            try {
                $user = EmployeeSync::where('employee_id',Auth::user()->username)->first();
                if ($user->department == 'General Affairs Department' || $user->department == 'Management Information System Department' || $user->department == 'Human Resources Department' || $user->department == null) {
                    if ($request->get('purpose_detail') == 'SAKIT') {
                        $emp = DB::SELECT("SELECT DISTINCT
                            ( clinic_patient_details.employee_id ),
                            employee_syncs.`name` 
                            FROM
                            clinic_patient_details
                            LEFT JOIN employee_syncs ON employee_syncs.employee_id = clinic_patient_details.employee_id 
                            WHERE
                            DATE( clinic_patient_details.created_at ) = DATE(
                                NOW()) 
                            AND purpose = 'Pulang (Sakit)'");
                    }else{
                        $emp = EmployeeSync::where('end_date',null)->get();
                    }
                }else{
                    if ($request->get('purpose_detail') == 'SAKIT') {
                        $emp = DB::SELECT("SELECT DISTINCT
                            ( clinic_patient_details.employee_id ),
                            employee_syncs.`name` 
                            FROM
                            clinic_patient_details
                            LEFT JOIN employee_syncs ON employee_syncs.employee_id = clinic_patient_details.employee_id 
                            WHERE
                            DATE( clinic_patient_details.created_at ) = DATE(
                                NOW()) 
                            AND purpose = 'Pulang (Sakit)'
                            AND employee_syncs.department = '".$user->department."'");
                    }else{
                        $emp = EmployeeSync::where('department',$user->department)->where('end_date',null)->get();
                    }
                }

                $response = array(
                    'status' => true,
                    'emp' => $emp
                );
                return Response::json($response);
            } catch (\Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage()
                );
                return Response::json($response);
            }
        }

        public function fetchLeaveRequest(Request $request)
        {
            try {
                $user = EmployeeSync::where('employee_id',Auth::user()->username)->first();
                if ($user->department == 'General Affairs Department' || $user->department == 'Management Information System Department' || $user->department == 'Human Resources Department' || $user->department == null) {
                    $leave_request = HrLeaveRequest::select('hr_leave_requests.*','departments.department_shortname','departments.department_name')->leftjoin('departments','departments.department_name','hr_leave_requests.department')->where('hr_leave_requests.remark','Requested')->orwhere('hr_leave_requests.remark','Partially Approved')
                    ->orderByRaw('hr_leave_requests.time_departure desc,hr_leave_requests.remark desc,hr_leave_requests.request_id desc')->
                    get();
                }else if(str_contains($user->position,'Manager')){
                    if ($user->department == 'Educational Instrument (EI) Department' || $user->department == 'Woodwind Instrument - Assembly (WI-A) Department') {
                        $leave_request = HrLeaveRequest::select('hr_leave_requests.*','departments.department_shortname','departments.department_name')->
                        leftjoin('departments','departments.department_name','hr_leave_requests.department')
                        ->where('hr_leave_requests.remark','Requested')
                        ->where(function ($query) {
                            $query->where('department_name', 'Educational Instrument (EI) Department')
                            ->orwhere('department_name', 'Woodwind Instrument - Assembly (WI-A) Department');
                        })
                        ->where(function ($query) {
                            $query->where('hr_leave_requests.remark', 'Requested')
                            ->orwhere('hr_leave_requests.created_by',Auth::user()->id);
                        })
                        ->orderByRaw('hr_leave_requests.time_departure desc,hr_leave_requests.remark desc,hr_leave_requests.request_id desc')->
                        get();
                    }
                    // else if ($user->department == 'Woodwind Instrument - Assembly (WI-A) Department' || $user->department == 'Woodwind Instrument - Surface Treatment (WI-ST) Department') {
                    //     $leave_request = HrLeaveRequest::select('hr_leave_requests.*','departments.department_shortname','departments.department_name')->
                    //     leftjoin('departments','departments.department_name','hr_leave_requests.department')
                    //     ->where('hr_leave_requests.remark','Requested')
                    //     ->where(function ($query) {
                    //         $query->where('department_name', 'Woodwind Instrument - Assembly (WI-A) Department')
                    //         ->orwhere('department_name', 'Woodwind Instrument - Surface Treatment (WI-ST) Department');
                    //     })
                    //     ->where(function ($query) {
                    //         $query->where('hr_leave_requests.remark', 'Requested')
                    //         ->orwhere('hr_leave_requests.created_by',Auth::user()->id);
                    //     })
                    //     ->orderByRaw('hr_leave_requests.time_departure desc,hr_leave_requests.remark desc,hr_leave_requests.request_id desc')->
                    //     get();
                    // }
                    else if ($user->department == 'Procurement Department' || $user->department == 'Purchasing Control Department') {
                        $leave_request = HrLeaveRequest::select('hr_leave_requests.*','departments.department_shortname','departments.department_name')->
                        leftjoin('departments','departments.department_name','hr_leave_requests.department')
                        ->where('hr_leave_requests.remark','Requested')
                        ->where(function ($query) {
                            $query->where('department_name', 'Procurement Department')
                            ->orwhere('department_name', 'Purchasing Control Department');
                        })
                        ->where(function ($query) {
                            $query->where('hr_leave_requests.remark', 'Requested')
                            ->orwhere('hr_leave_requests.created_by',Auth::user()->id);
                        })
                        ->orderByRaw('hr_leave_requests.time_departure desc,hr_leave_requests.remark desc,hr_leave_requests.request_id desc')->
                        get();
                    }else{
                        $leave_request = HrLeaveRequest::select('hr_leave_requests.*','departments.department_shortname','departments.department_name')->
                        leftjoin('departments','departments.department_name','hr_leave_requests.department')
                        ->where('hr_leave_requests.remark','Requested')->where('department_name',$user->department)
                        ->where(function ($query) {
                            $query->where('hr_leave_requests.remark', 'Requested')
                            ->orwhere('hr_leave_requests.created_by',Auth::user()->id);
                        })
                        ->orderByRaw('hr_leave_requests.time_departure desc,hr_leave_requests.remark desc,hr_leave_requests.request_id desc')->
                        get();
                    }
                }else{
                    $leave_request = HrLeaveRequest::select('hr_leave_requests.*','departments.department_shortname','departments.department_name')->where('hr_leave_requests.created_by',Auth::user()->id)
                    ->leftjoin('departments','departments.department_name','hr_leave_requests.department')
                    ->where(function ($query) {
                        $query->where('hr_leave_requests.remark', 'Requested')
                        ->orwhere('hr_leave_requests.remark', 'Partially Approved');
                    })
                            // ->where('remark','Requested')->where('remark','Partially Approved')
                    ->orderByRaw('hr_leave_requests.time_departure desc,hr_leave_requests.remark desc,hr_leave_requests.request_id desc')->
                            // ->orderBy('hr_leave_requests.request_id','desc')->
                    get();
                }

                $user = EmployeeSync::where('employee_id',Auth::user()->username)->first();
                if ($user->department == 'General Affairs Department' || $user->department == 'Management Information System Department' || $user->department == 'Human Resources Department' || $user->department == null) {
                    $leave_request_complete = HrLeaveRequest::select('hr_leave_requests.*','departments.department_shortname','departments.department_name')->leftjoin('departments','departments.department_name','hr_leave_requests.department')->where('hr_leave_requests.remark','Fully Approved')
                    ->orderByRaw('hr_leave_requests.time_departure desc,hr_leave_requests.remark desc,hr_leave_requests.request_id desc')
                    ->whereDate('hr_leave_requests.created_at','<=',date('Y-m-d'))
                    ->whereDate('hr_leave_requests.created_at','>=',date('Y-m-d',strtotime('- 1 DAYS')))
                    ->get();
                }else if(str_contains($user->position,'Manager')){
                    $leave_request_complete = HrLeaveRequest::select('hr_leave_requests.*','departments.department_shortname','departments.department_name')->
                    leftjoin('departments','departments.department_name','hr_leave_requests.department')
                    ->where('hr_leave_requests.remark','Fully Approved')->where('department_name',$user->department)
                    ->whereDate('hr_leave_requests.created_at','<=',date('Y-m-d'))
                    ->whereDate('hr_leave_requests.created_at','>=',date('Y-m-d',strtotime('- 1 DAYS')))
                    ->orwhere('hr_leave_requests.created_by',Auth::user()->id)
                    ->orderByRaw('hr_leave_requests.time_departure desc,hr_leave_requests.remark desc,hr_leave_requests.request_id desc')->
                    get();
                }else{
                    $leave_request_complete = HrLeaveRequest::select('hr_leave_requests.*','departments.department_shortname','departments.department_name')->where('hr_leave_requests.created_by',Auth::user()->id)
                    ->leftjoin('departments','departments.department_name','hr_leave_requests.department')
                    ->where('hr_leave_requests.remark','Fully Approved')
                    ->whereDate('hr_leave_requests.created_at','<=',date('Y-m-d'))
                    ->whereDate('hr_leave_requests.created_at','>=',date('Y-m-d',strtotime('- 1 DAYS')))
                    ->orderBy('hr_leave_requests.remark','desc')
                    ->orderByRaw('hr_leave_requests.time_departure desc,hr_leave_requests.remark desc,hr_leave_requests.request_id desc')->
                    get();
                }

                $leave_details = [];

                foreach($leave_request as $lr){
                    $leave_detail = HrLeaveRequestDetail::where('request_id',$lr->request_id)->get();
                    array_push($leave_details, $leave_detail);
                }

                $leave_details_complete = [];

                foreach($leave_request_complete as $lr){
                    $leave_detail_complete = HrLeaveRequestDetail::where('request_id',$lr->request_id)->get();
                    array_push($leave_details_complete, $leave_detail_complete);
                }

                $leave_approvals = [];

                foreach($leave_request as $lr){
                    $leave_approval = DB::SELECT("SELECT
                        hr_leave_request_approvals.id,
                        hr_leave_request_approvals.request_id,
                        hr_leave_request_approvals.approver_id,
                        hr_leave_request_approvals.approver_name,
                        hr_leave_request_approvals.approver_email,
                        hr_leave_request_approvals.`status`,
                        hr_leave_request_approvals.approved_at,
                        hr_leave_request_approvals.remark,
                        CONCAT(
                            DATE_FORMAT( hr_leave_request_approvals.approved_at, '%d-%b-%Y' ),
                            '<br>',
                            DATE_FORMAT( hr_leave_request_approvals.approved_at, '%H:%i:%s' )) AS approved_ats,
                        'sudah' AS keutamaan 
                        FROM
                        `hr_leave_request_approvals`
                        LEFT JOIN hr_leave_requests ON hr_leave_requests.request_id = hr_leave_request_approvals.request_id 
                        WHERE
                        hr_leave_request_approvals.request_id = '".$lr->request_id."' 
                        AND hr_leave_request_approvals.`status` IS NOT NULL UNION ALL
                        (
                        SELECT
                        hr_leave_request_approvals.id,
                        hr_leave_request_approvals.request_id,
                        hr_leave_request_approvals.approver_id,
                        hr_leave_request_approvals.approver_name,
                        hr_leave_request_approvals.approver_email,
                        hr_leave_request_approvals.`status`,
                        hr_leave_request_approvals.approved_at,
                        hr_leave_request_approvals.remark,
                        CONCAT(
                        DATE_FORMAT( hr_leave_request_approvals.approved_at, '%d-%b-%Y' ),
                        '<br>',
                        DATE_FORMAT( hr_leave_request_approvals.approved_at, '%H:%i:%s' )) AS approved_ats,
                        'utama' AS keutamaan 
                        FROM
                        `hr_leave_request_approvals`
                        LEFT JOIN hr_leave_requests ON hr_leave_requests.request_id = hr_leave_request_approvals.request_id 
                        WHERE
                        hr_leave_request_approvals.request_id = '".$lr->request_id."' 
                        AND hr_leave_request_approvals.`status` IS NULL 
                        LIMIT 1 
                        ) UNION ALL
                        (
                        SELECT
                        hr_leave_request_approvals.id,
                        hr_leave_request_approvals.request_id,
                        hr_leave_request_approvals.approver_id,
                        hr_leave_request_approvals.approver_name,
                        hr_leave_request_approvals.approver_email,
                        hr_leave_request_approvals.`status`,
                        hr_leave_request_approvals.approved_at,
                        hr_leave_request_approvals.remark,
                        CONCAT(
                        DATE_FORMAT( hr_leave_request_approvals.approved_at, '%d-%b-%Y' ),
                        '<br>',
                        DATE_FORMAT( hr_leave_request_approvals.approved_at, '%H:%i:%s' )) AS approved_ats,
                        'belum' AS keutamaan 
                        FROM
                        `hr_leave_request_approvals`
                        LEFT JOIN hr_leave_requests ON hr_leave_requests.request_id = hr_leave_request_approvals.request_id 
                        WHERE
                        hr_leave_request_approvals.request_id = '".$lr->request_id."' 
                        AND hr_leave_request_approvals.`status` IS NULL 
                        AND hr_leave_request_approvals.id != (
                        SELECT
                        hr_leave_request_approvals.id 
                        FROM
                        `hr_leave_request_approvals`
                        LEFT JOIN hr_leave_requests ON hr_leave_requests.request_id = hr_leave_request_approvals.request_id 
                        WHERE
                        hr_leave_request_approvals.request_id = '".$lr->request_id."' 
                        AND hr_leave_request_approvals.`status` IS NULL 
                        LIMIT 1 
                        ) 
                    )");
array_push($leave_approvals, $leave_approval);
}

$leave_approvals_complete = [];

foreach($leave_request_complete as $lr){
    $leave_approval_complete = DB::SELECT("SELECT
        hr_leave_request_approvals.id,
        hr_leave_request_approvals.request_id,
        hr_leave_request_approvals.approver_id,
        hr_leave_request_approvals.approver_name,
        hr_leave_request_approvals.approver_email,
        hr_leave_request_approvals.`status`,
        hr_leave_request_approvals.approved_at,
        hr_leave_request_approvals.remark,
        CONCAT(
            DATE_FORMAT( hr_leave_request_approvals.approved_at, '%d-%b-%Y' ),
            '<br>',
            DATE_FORMAT( hr_leave_request_approvals.approved_at, '%H:%i:%s' )) AS approved_ats,
        'sudah' AS keutamaan 
        FROM
        `hr_leave_request_approvals`
        LEFT JOIN hr_leave_requests ON hr_leave_requests.request_id = hr_leave_request_approvals.request_id 
        WHERE
        hr_leave_request_approvals.request_id = '".$lr->request_id."' 
        AND hr_leave_request_approvals.`status` IS NOT NULL UNION ALL
        (
        SELECT
        hr_leave_request_approvals.id,
        hr_leave_request_approvals.request_id,
        hr_leave_request_approvals.approver_id,
        hr_leave_request_approvals.approver_name,
        hr_leave_request_approvals.approver_email,
        hr_leave_request_approvals.`status`,
        hr_leave_request_approvals.approved_at,
        hr_leave_request_approvals.remark,
        CONCAT(
        DATE_FORMAT( hr_leave_request_approvals.approved_at, '%d-%b-%Y' ),
        '<br>',
        DATE_FORMAT( hr_leave_request_approvals.approved_at, '%H:%i:%s' )) AS approved_ats,
        'utama' AS keutamaan 
        FROM
        `hr_leave_request_approvals`
        LEFT JOIN hr_leave_requests ON hr_leave_requests.request_id = hr_leave_request_approvals.request_id 
        WHERE
        hr_leave_request_approvals.request_id = '".$lr->request_id."' 
        AND hr_leave_request_approvals.`status` IS NULL 
        LIMIT 1 
        ) UNION ALL
        (
        SELECT
        hr_leave_request_approvals.id,
        hr_leave_request_approvals.request_id,
        hr_leave_request_approvals.approver_id,
        hr_leave_request_approvals.approver_name,
        hr_leave_request_approvals.approver_email,
        hr_leave_request_approvals.`status`,
        hr_leave_request_approvals.approved_at,
        hr_leave_request_approvals.remark,
        CONCAT(
        DATE_FORMAT( hr_leave_request_approvals.approved_at, '%d-%b-%Y' ),
        '<br>',
        DATE_FORMAT( hr_leave_request_approvals.approved_at, '%H:%i:%s' )) AS approved_ats,
        'belum' AS keutamaan 
        FROM
        `hr_leave_request_approvals`
        LEFT JOIN hr_leave_requests ON hr_leave_requests.request_id = hr_leave_request_approvals.request_id 
        WHERE
        hr_leave_request_approvals.request_id = '".$lr->request_id."' 
        AND hr_leave_request_approvals.`status` IS NULL 
        AND hr_leave_request_approvals.id != (
        SELECT
        hr_leave_request_approvals.id 
        FROM
        `hr_leave_request_approvals`
        LEFT JOIN hr_leave_requests ON hr_leave_requests.request_id = hr_leave_request_approvals.request_id 
        WHERE
        hr_leave_request_approvals.request_id = '".$lr->request_id."' 
        AND hr_leave_request_approvals.`status` IS NULL 
        LIMIT 1 
        ) 
    )");
    array_push($leave_approvals_complete, $leave_approval_complete);
}

$response = array(
    'status' => true,
    'leave_request' => $leave_request,
    'leave_details' => $leave_details,
    'leave_approvals' => $leave_approvals,
    'leave_request_complete' => $leave_request_complete,
    'leave_details_complete' => $leave_details_complete,
    'leave_approvals_complete' => $leave_approvals_complete,
);
return Response::json($response);
} catch (\Exception $e) {
    $response = array(
        'status' => false,
        'message' => $e->getMessage()
    );
    return Response::json($response);
}
}

public function fetchLeaveRequestDetail(Request $request)
{
    try {
        $leave_request = HrLeaveRequest::where('request_id', '=', $request->get('request_id'))->first();
        $detail_emp = HrLeaveRequestDetail::where('request_id',$request->get('request_id'))->leftjoin('departments','department_name','department')->get();
        $approval_progress = HrLeaveRequestApproval::select('hr_leave_request_approvals.*',DB::RAW('DATE_FORMAT(hr_leave_request_approvals.approved_at,"%d %b %Y<br>%H:%i:%s") as approved_date'))->where('request_id',$request->get('request_id'))->get();

        $destinations = null;
        if ($leave_request->add_driver == 'YES') {
            $destinations = DriverDetail::where('driver_id',$leave_request->driver_request_id)->where('category','destination')->get();
        }

        $driver = driver::where('id',$leave_request->driver_request_id)->first();

        $response = array(
            'status' => true,
            'leave_request' => $leave_request,
            'detail_emp' => $detail_emp,
            'approval_progress' => $approval_progress,
            'driver' => $driver,
            'destinations' => $destinations,
        );
        return Response::json($response);
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function inputLeaveRequest(Request $request)
{
    try {
        $id = Auth::user()->username;
        $emp = EmployeeSync::where('employee_id',$id)->first();
        $emps = Employee::where('employee_id',$id)->first();

        $date = $request->get('date');
        $purpose_category = $request->get('purpose_category');
        $purpose = $request->get('purpose');
        $purpose_detail = $request->get('purpose_detail');
        $time_departure = $request->get('time_departure');
        $time_arrived = $request->get('time_arrived');
        $return_or_not = $request->get('return_or_not');
        $add_driver = $request->get('add_driver');
        $destinations = $request->get('destinations');
        $employees = $request->get('employees');
        $detail_city = $request->get('detail_city');
        $city = $request->get('city');

        $dept = [];
        $pos = [];
        for ($i=0; $i < count($employees); $i++) { 
            $empcheck = EmployeeSync::where('employee_id',$employees[$i])->first();
            array_push($dept, $empcheck->department);
            array_push($pos, $empcheck->position);
        }
        $depts = array_unique($dept);
        if (count($depts) > 1) {
            $response = array(
                'status' => false,
                'message' => 'Tidak boleh lintas Department.'.join(",",$depts)
            );
            return Response::json($response);
        }

        $departure = explode(':', $time_departure);
        if (strlen($departure[0]) == 1) {
            $time0 = '0'.$departure[0];
        }else{
            $time0 = $departure[0];
        }
        $time1 = $departure[1];
        $time_departure_new = $date.' '.$time0.':'.$time1.':00';

        $arrived = explode(':', $time_arrived);
        if (strlen($arrived[0]) == 1) {
            $times0 = '0'.$arrived[0];
        }else{
            $times0 = $arrived[0];
        }
        $times1 = $arrived[1];
        $time_arrived_new = $date.' '.$times0.':'.$times1.':00';
        if ($return_or_not == 'NO') {
            $mengantar = '(Mengantar Saja)';
            if (date('Y-m-d H:i:s') >= date('Y-m-d 17:00:00') && date('Y-m-d H:i:s') <= date('Y-m-d', strtotime("+1 day")).' 05:00:00' && $time_departure_new >= $date. ' 17:00:00' && $time_arrived_new == $date.' 00:00:00') {
                $time_arrived_new = date('Y-m-d', strtotime("+1 day")).' 05:00:00';
            }else{
                $time_arrived_new = $date.' 16:00:00';
            }
        }else{
            $time_arrived_new = $date.' '.$times0.':'.$times1.':00';
            $mengantar = '';
        }
        if ($add_driver == 'YES') {
            $driver = new Driver([
                'purpose' => ucwords(strtolower($purpose_category.' Ke '.$purpose.' '.$purpose_detail.' '.$mengantar)),
                'destination_city' => $city,
                'date_from' => $time_departure_new,
                'date_to' => $time_arrived_new,
                'created_by' => $id,
                'remark' => 'pending'
            ]);
            $driver->save();

            for ($i=0; $i < count($employees); $i++) { 
                $passenger_detail = new DriverDetail([
                    'driver_id' => $driver->id,
                    'remark' => $employees[$i],
                    'category' => 'passenger'
                ]);
                $passenger_detail->save();
            }

            for ($i=0; $i < count($destinations); $i++) { 
                $destination_detail = new DriverDetail([
                    'driver_id' => $driver->id,
                    'remark' => $destinations[$i],
                    'category' => 'destination'     
                ]);
                $destination_detail->save();
            }
            $driver_request_id = $driver->id;
        }else{
            $driver_request_id = null;
        }


        $code_generator = CodeGenerator::where('note','=','leave_request')->first();
        $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
        
        $request_id = $code_generator->prefix . $number;

        $diagnose = null;
        $action = null;
        $suggestion = null;

        $paramedic = null;

        if ($purpose == 'SAKIT') {
            $position = 'clinic';
            $clinic_details = DB::SELECT("SELECT DISTINCT
                clinic_patient_details.employee_id,
                employee_syncs.`name`,
                diagnose,
                action,
                suggestion,
                paramedic 
                FROM
                clinic_patient_details
                LEFT JOIN employee_syncs ON employee_syncs.employee_id = clinic_patient_details.employee_id 
                WHERE
                DATE( clinic_patient_details.created_at ) = DATE(
                    NOW()) 
                AND purpose = 'Pulang (Sakit)' 
                AND employee_syncs.employee_id = '".$employees[0]."'");
            $diagnose_arr = [];
            $action_arr = [];
            $suggestion_arr = [];
            for($j = 0; $j < count($clinic_details);$j++){
                array_push($diagnose_arr, $clinic_details[$j]->diagnose);
                array_push($action_arr, $clinic_details[$j]->action);
                array_push($suggestion_arr, $clinic_details[$j]->suggestion);
                $paramedic = $clinic_details[$j]->paramedic;
            }

            $diagnose = join(', ',$diagnose_arr);
            $action = join(', ',$action_arr);
            $suggestion = join(', ',$suggestion_arr);
        }else{
            $position = 'applicant';
        }

        $leave_request = new HrLeaveRequest([
            'request_id' => $request_id,
            'position' => $position,
            'department' => $depts[0],
            'date' => $date,
            'purpose_category' => $purpose_category,
            'purpose' => $purpose,
            'purpose_detail' => $purpose_detail,
            'time_departure' => $time_departure_new,
            'time_arrived' => $time_arrived_new,
            'return_or_not' => $return_or_not,
            'add_driver' => $add_driver,
            'detail_city' => $detail_city,
            'diagnose' => $diagnose,
            'action' => $action,
            'suggestion' => $suggestion,
            'driver_request_id' => $driver_request_id,
            'remark' => 'Requested',
            'created_by' => Auth::user()->id,
        ]);
        $leave_request->save();

        for($i = 0; $i < count($employees); $i++){
            $empys = EmployeeSync::where('employee_id',$employees[$i])->leftjoin('departments','department_name','employee_syncs.department')->first();
            $detail = new HrLeaveRequestDetail([
                'request_id' => $request_id,
                'employee_id' => $employees[$i],
                'name' => $empys->name,
                'department' => $empys->department,
                'section' => $empys->section,
                'group' => $empys->group,
                'sub_group' => $empys->sub_group,
                'created_by' => Auth::user()->id,
            ]);
            $detail->save();

            if (date('Y-m-d H:i:s') >= date('Y-m-d 17:00:00') && date('Y-m-d H:i:s') <= date('Y-m-d', strtotime("+1 day")).' 05:00:00' && $time_departure_new >= date('Y-m-d 17:00:00') && $time_arrived_new <= date('Y-m-d', strtotime("+1 day")).' 05:00:00') {
                $time_arrival_new = explode(' ', $time_departure_new)[0].' 23:59:59';
            }else{
                $time_arrival_new = $time_arrived_new;
            }

            $start = strtotime($time_departure_new);
            $end = strtotime($time_arrival_new);
            $mins = ($end - $start) / 60 / 60;

            $check_mp = DB::connection('ympimis_2')->table('efficiency_manpowers')->where(DB::RAW('DATE_FORMAT(period,"%Y-%m")'),date('Y-m',strtotime($date)))->where('employee_id',$employees[$i])->first();
            if ($check_mp) {
                $input = DB::connection('ympimis_2')->table('efficiency_work_hours')->insert([
                    'employee_id' => $employees[$i],
                    'shift_date' => $date,
                    'department' => $check_mp->department,
                    'remark_2' => $check_mp->remark,
                    'work_hour' => '-'.round($mins,1),
                    'category' => 'Attendance',
                    'remark' => 'Izin Keluar',
                    'updated_by' => Auth::user()->username,
                    'updated_by_name' => Auth::user()->name,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $users = User::where('username',$emp->employee_id)->first();
        if (strpos($users->email, '@music.yamaha.com') !== false) {
            $applicant_email = $users->email;
        }else{
            $applicant_email = '';
        }

        $applicant = New HrLeaveRequestApproval([
            'request_id' => $request_id,
            'approver_id' => $emp->employee_id,
            'approver_name' => $emp->name,
            'approver_email' => $applicant_email,
            'status' => "Approved",
            'approved_at' => date('Y-m-d H:i:s'),
            'remark' => 'Applicant'
        ]);

        if (str_contains(join(',',$employees),'OS')) {
            $mail_to = [];
            $managers = null;
            $manager = null;
            $cc = [];
            $dgm = null;
            $manager = New HrLeaveRequestApproval([
                'request_id' => $request_id,
                'approver_id' => 'PI1106002',
                'approver_name' => 'Putri Sukma Riyanti',
                'approver_email' => 'putri.sukma.riyanti@music.yamaha.com',
                'remark' => 'Chief'
            ]);
            array_push($mail_to, 'putri.sukma.riyanti@music.yamaha.com');
        }else{
            $mail_to = [];
            $cc = [];
            $dgm = null;
            $managers = null;
            $manager = null;
            if (str_contains(join(',',$pos),'Deputy General Manager')) {
                                // $m = Approver::where('department', '=', $emp->department)
                                // ->where('remark', '=', 'Deputy General Manager')
                                // ->first();
                $div_manager = '';
                for ($i=0; $i < count($employees); $i++) { 
                    $empcheck = EmployeeSync::where('employee_id',$employees[$i])->first();
                    if (str_contains($empcheck->position,'Deputy General Manager')) {
                        $div_manager = $empcheck->division;
                        break;
                    }
                }
                if ($div_manager == 'Production Support Division'){
                    // $dgm = New HrLeaveRequestApproval([
                    //     'request_id' => $request_id,
                    //     'approver_id' => 'PI9905001',
                    //     'approver_name' => 'Mei Rahayu',
                    //     'approver_email' => 'mei.rahayu@music.yamaha.com',
                    //     'remark' => 'Deputy General Manager'
                    // ]);
                    $manager = New HrLeaveRequestApproval([
                        'request_id' => $request_id,
                        'approver_id' => 'PI0109004',
                        'approver_name' => 'Budhi Apriyanto',
                        'approver_email' => 'budhi.apriyanto@music.yamaha.com',
                        'remark' => 'General Manager'
                    ]);
                    array_push($mail_to, 'budhi.apriyanto@music.yamaha.com');
                }else {
                    $manager = New HrLeaveRequestApproval([
                        'request_id' => $request_id,
                        'approver_id' => 'PI9709001',
                        'approver_name' => 'Arief Soekamto',
                        'approver_email' => 'arief.soekamto@music.yamaha.com',
                        'remark' => 'Director'
                    ]);
                    array_push($mail_to, 'arief.soekamto@music.yamaha.com');
                }
            }else if (str_contains(join(',',$pos),'Manager')) {
                                // $m = Approver::where('department', '=', $emp->department)
                                // ->where('remark', '=', 'Deputy General Manager')
                                // ->first();
                $div_manager = '';
                for ($i=0; $i < count($employees); $i++) { 
                    $empcheck = EmployeeSync::where('employee_id',$employees[$i])->first();
                    if (str_contains($empcheck->position,'Manager')) {
                        $div_manager = $empcheck->division;
                        break;
                    }
                }
                if ($div_manager == 'Production Division') {
                    $manager = New HrLeaveRequestApproval([
                        'request_id' => $request_id,
                        'approver_id' => 'PI0109004',
                        'approver_name' => 'Budhi Apriyanto',
                        'approver_email' => 'budhi.apriyanto@music.yamaha.com',
                        'remark' => 'Deputy General Manager'
                    ]);
                    array_push($mail_to, 'budhi.apriyanto@music.yamaha.com');
                }else if ($div_manager == 'Production Support Division'){
                    // $dgm = New HrLeaveRequestApproval([
                    //     'request_id' => $request_id,
                    //     'approver_id' => 'PI9905001',
                    //     'approver_name' => 'Mei Rahayu',
                    //     'approver_email' => 'mei.rahayu@music.yamaha.com',
                    //     'remark' => 'Deputy General Manager'
                    // ]);
                    $manager = New HrLeaveRequestApproval([
                        'request_id' => $request_id,
                        'approver_id' => 'PI0109004',
                        'approver_name' => 'Budhi Apriyanto',
                        'approver_email' => 'budhi.apriyanto@music.yamaha.com',
                        'remark' => 'General Manager'
                    ]);
                    array_push($mail_to, 'budhi.apriyanto@music.yamaha.com');
                }else {
                    $manager = New HrLeaveRequestApproval([
                        'request_id' => $request_id,
                        'approver_id' => 'PI9709001',
                        'approver_name' => 'Arief Soekamto',
                        'approver_email' => 'arief.soekamto@music.yamaha.com',
                        'remark' => 'Director'
                    ]);
                    array_push($mail_to, 'arief.soekamto@music.yamaha.com');
                }
            }else if (str_contains(join(',',$pos),'Coordinator') || str_contains(join(',',$pos),'Chief') || str_contains(join(',',$pos),'Foreman')) {
                $div_manager = '';
                $dept_manager = '';
                for ($i=0; $i < count($employees); $i++) { 
                    $empcheck = EmployeeSync::where('employee_id',$employees[$i])->first();
                    if (str_contains($empcheck->position,'Coordinator') || str_contains($empcheck->position,'Chief') || str_contains($empcheck->position,'Foreman')) {
                        $div_manager = $empcheck->division;
                        $dept_manager = $empcheck->department;
                        break;
                    }
                }

                if ($dept_manager != 'Management Information System Department') {
                    // if ($dept_manager == 'Woodwind Instrument - Assembly (WI-A) Department' || $dept_manager == 'Woodwind Instrument - Surface Treatment (WI-ST) Department') {
                    //     $manager = New HrLeaveRequestApproval([
                    //         'request_id' => $request_id,
                    //         'approver_id' => 'PI0109004',
                    //         'approver_name' => 'Budhi Apriyanto',
                    //         'approver_email' => 'budhi.apriyanto@music.yamaha.com',
                    //         'remark' => 'Deputy General Manager'
                    //     ]);
                    //     array_push($mail_to, 'budhi.apriyanto@music.yamaha.com');
                    // }else{
                    $m = Approver::where('department', '=', $dept_manager)
                    ->where('remark', '=', 'Manager')
                    ->first();
                    if($m != null){
                        $managers = New HrLeaveRequestApproval([
                            'request_id' => $request_id,
                            'approver_id' => $m->approver_id,
                            'approver_name' => $m->approver_name,
                            'approver_email' => $m->approver_email,
                            'remark' => 'Manager'
                        ]);
                        array_push($mail_to, $m->approver_email);
                    }
                    else{
                        $managers = New HrLeaveRequestApproval([
                            'request_id' => $request_id,
                            'approver_id' => "",
                            'approver_name' => "",
                            'approver_email' => "",
                            'status' => "none",
                            'approved_at' => date('Y-m-d H:i:s'),
                            'remark' => 'Manager'
                        ]);
                    }
                    // }
                }
                if ($div_manager == 'Production Division') {
                    // $manager = New HrLeaveRequestApproval([
                    //     'request_id' => $request_id,
                    //     'approver_id' => 'PI0109004',
                    //     'approver_name' => 'Budhi Apriyanto',
                    //     'approver_email' => 'budhi.apriyanto@music.yamaha.com',
                    //     'remark' => 'Deputy General Manager'
                    // ]);
                    if ($dept_manager == 'Management Information System Department') {
                        $manager = New HrLeaveRequestApproval([
                            'request_id' => $request_id,
                            'approver_id' => 'PI0109004',
                            'approver_name' => 'Budhi Apriyanto',
                            'approver_email' => 'budhi.apriyanto@music.yamaha.com',
                            'remark' => 'Manager'
                        ]);
                        array_push($mail_to, 'budhi.apriyanto@music.yamaha.com');
                    }
                }else if ($div_manager == 'Production Support Division'){
                    // $dgm = New HrLeaveRequestApproval([
                    //     'request_id' => $request_id,
                    //     'approver_id' => 'PI9905001',
                    //     'approver_name' => 'Mei Rahayu',
                    //     'approver_email' => 'mei.rahayu@music.yamaha.com',
                    //     'remark' => 'Deputy General Manager'
                    // ]);
                    // $manager = New HrLeaveRequestApproval([
                    //     'request_id' => $request_id,
                    //     'approver_id' => 'PI0109004',
                    //     'approver_name' => 'Budhi Apriyanto',
                    //     'approver_email' => 'budhi.apriyanto@music.yamaha.com',
                    //     'remark' => 'General Manager'
                    // ]);
                    // array_push($mail_to, 'mei.rahayu@music.yamaha.com');
                }else {
                    // $manager = New HrLeaveRequestApproval([
                    //     'request_id' => $request_id,
                    //     'approver_id' => 'PI9709001',
                    //     'approver_name' => 'Arief Soekamto',
                    //     'approver_email' => 'arief.soekamto@music.yamaha.com',
                    //     'remark' => 'Director'
                    // ]);
                    // array_push($mail_to, 'arief.soekamto@music.yamaha.com');
                }
            }else{
                $m = Approver::where('department', '=', $depts[0])
                ->where('remark', '=', 'Manager')
                ->first();

                if ($emps->remark == 'OFC') {
                    $fc = Approver::where('department', '=', $depts[0])
                    ->where('remark', '=', 'Chief')
                    ->get();
                    $fcs = 'Chief';
                }else{
                    $fc = Approver::where('department', '=', $depts[0])
                    ->where('remark', '=', 'Foreman')
                    ->get();
                    $fcs = 'Foreman';
                }

                if ($depts[0] == 'Production Engineering Department') {
                    $manager = New HrLeaveRequestApproval([
                        'request_id' => $request_id,
                        'approver_id' => 'PI0703002',
                        'approver_name' => 'Susilo Basri Prasetyo',
                        'approver_email' => 'susilo.basri@music.yamaha.com',
                        'remark' => 'Manager'
                    ]);
                    array_push($mail_to, 'susilo.basri@music.yamaha.com');
                }else{
                    if($m != null){
                        $manager = New HrLeaveRequestApproval([
                            'request_id' => $request_id,
                            'approver_id' => $m->approver_id,
                            'approver_name' => $m->approver_name,
                            'approver_email' => $m->approver_email,
                            'remark' => 'Manager'
                        ]);
                        array_push($mail_to, $m->approver_email);
                    }
                    else{
                        $manager = New HrLeaveRequestApproval([
                            'request_id' => $request_id,
                            'approver_id' => "",
                            'approver_name' => "",
                            'approver_email' => "",
                            'status' => "none",
                            'approved_at' => date('Y-m-d H:i:s'),
                            'remark' => 'Manager'
                        ]);
                    }
                }

                if (count($fc) > 0) {
                    for($o = 0; $o < count($fc);$o++){
                        array_push($cc, $fc[$o]->approver_email);
                    }
                }
            }
        }

        $hr = New HrLeaveRequestApproval([
            'request_id' => $request_id,
            'approver_id' => "PI0811002",
            'approver_name' => "Mahendra Putra",
            'approver_email' => "mahendra.putra@music.yamaha.com",
            'remark' => 'HR'
        ]);

        $security = New HrLeaveRequestApproval([
            'request_id' => $request_id,
            'approver_id' => null,
            'approver_name' => null,
            'approver_email' => null,
            'remark' => 'Security'
        ]);


        $code_generator->index = $code_generator->index+1;
        $code_generator->save();
        $applicant->save();
        if ($purpose == 'SAKIT') {
            $clinic = New HrLeaveRequestApproval([
                'request_id' => $request_id,
                'approver_id' => "",
                'approver_name' => $paramedic,
                'approver_email' => "",
                'status' => 'Approved',
                'approved_at' => date('Y-m-d H:i:s'),
                'remark' => 'Clinic'
            ]);
            $clinic->save();
        }
        if ($managers != null) {
            $managers->save();
        }
        if ($dgm != null) {
            $dgm->save();
        }
        if ($manager != null) {
            $manager->save();
        }
        $hr->save();
        $destination = null;
        if ($add_driver == 'YES') {
            $ga = New HrLeaveRequestApproval([
                'request_id' => $request_id,
                'approver_id' => "PI0904002",
                'approver_name' => "Heriyanto",
                'approver_email' => "heriyanto@music.yamaha.com",
                'remark' => 'GA'
            ]);
            $ga->save();
            $destination = DriverDetail::where('driver_id',$driver_request_id)->get();
        }
        $security->save();

        $leave_request = HrLeaveRequest::where('request_id', '=', $request_id)->first();

        $detail_emp = HrLeaveRequestDetail::where('request_id',$request_id)->leftjoin('departments','department_name','department')->get();

        $approval_progress = HrLeaveRequestApproval::where('request_id',$request_id)->get();

        if (date('Y-m-d H:i:s') >= date('Y-m-d 17:00:00') && date('Y-m-d H:i:s') <= date('Y-m-d', strtotime("+1 day")).' 05:00:00' && $time_departure_new >= date('Y-m-d 17:00:00') && $time_arrived_new <= date('Y-m-d', strtotime("+1 day")).' 05:00:00') {
            $mail_to_user = [];
            $cc = [];
            $users = User::where('id',$leave_request->created_by)->first();
            $approval_manager = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Manager')->first();
            if ($approval_manager == null) {
                $approval_manager = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Deputy General Manager')->first();
                if ($approval_manager == null) {
                    $approval_manager = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','General Manager')->first();
                    if ($approval_manager == null) {
                        $approval_manager = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Director')->first();
                        if ($approval_manager == null) {
                            $approval_manager = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Chief')->first();
                        }
                    }
                }
            }
            $mail_to_manager = [];
            $cc = [];
            array_push($mail_to_manager, $approval_manager->approver_email);
                            // if (count($fc) > 0) {
                            //     for($o = 0; $o < count($fc);$o++){
                            //         array_push($cc, $fc[$o]->approver_email);
                            //     }
                            // }
            $approval_manager->status = 'Approved';
            $approval_manager->approved_at = date('Y-m-d H:i:s');
            $approval_manager->save();

            $mail_to_gm = [];
            if ($detail_emp[0]->id_division == 4) {
                $approval_gm = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','General Manager')->first();
                array_push($mail_to_gm, $approval_gm->approver_email);
                                // if (count($fc) > 0) {
                                //     for($o = 0; $o < count($fc);$o++){
                                //         array_push($cc, $fc[$o]->approver_email);
                                //     }
                                // }
                $approval_gm->status = 'Approved';
                $approval_gm->approved_at = date('Y-m-d H:i:s');
                $approval_gm->save();
            }

            $approval_hr = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','HR')->first();
            $mail_to_hr = [];
            array_push($mail_to_hr, 'mahendra.putra@music.yamaha.com');
            array_push($mail_to_hr, 'achmad.riski.bayu@music.yamaha.com');
            array_push($mail_to_hr, 'ummi.ernawati@music.yamaha.com');
            array_push($mail_to_hr, 'adhi.satya.indradhi@music.yamaha.com');
            array_push($mail_to_hr, 'linda.febrian@music.yamaha.com');
            array_push($mail_to_hr, 'mukhamad.khoirul.anam@music.yamaha.com');
            array_push($mail_to_hr, 'dicky.kurniawan@music.yamaha.com');
            $approval_hr->status = 'Approved';
            $approval_hr->approved_at = date('Y-m-d H:i:s');
            $approval_hr->save();

            if ($add_driver == 'YES') {
                $approval_ga = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','GA')->first();
                $mail_to_ga = [];
                array_push($mail_to_ga, 'heriyanto@music.yamaha.com');
                array_push($mail_to_ga, 'rianita.widiastuti@music.yamaha.com');
                array_push($mail_to_ga, 'putri.sukma.riyanti@music.yamaha.com');
                $approval_ga->status = 'Approved';
                $approval_ga->approved_at = date('Y-m-d H:i:s');
                $approval_ga->save();

                $leave_request = HrLeaveRequest::where('request_id', '=', $request_id)->first();

                $detail_emp = HrLeaveRequestDetail::where('request_id',$request_id)->leftjoin('departments','department_name','department')->get();

                $approval_progress = HrLeaveRequestApproval::where('request_id',$request_id)->get();

                $data = [
                    'leave_request' => $leave_request,
                    'detail_emp' => $detail_emp,
                    'approval_progress' => $approval_progress,
                    'destination' => $destination,
                    'driver' => null,
                ];
                Mail::to($mail_to_ga)
                ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
                ->send(new SendEmail($data, 'leave_request_shift'));

                $select_driver = DB::SELECT("SELECT
                                    * 
                    FROM
                    driver_lists
                    JOIN sunfish_shift_syncs ON sunfish_shift_syncs.employee_id = driver_lists.driver_id 
                    WHERE
                    sunfish_shift_syncs.shift_date = '".date('Y-m-d')."' 
                    AND shiftdaily_code NOT LIKE '%Shift_1%'");

                if (count($select_driver) > 0) {
                    $driver = Driver::where('id', '=', $leave_request->driver_request_id)->first();
                    $driver->driver_id = $select_driver[0]->driver_id;
                    $driver->name = $select_driver[0]->name;
                    $driver->remark = 'received';
                    $driver->save();

                    $driver_list = DriverList::where('driver_id',$select_driver[0]->driver_id)->first();

                    if(substr($driver_list->whatsapp_no, 0, 1) == '+' ){
                        $phone = substr($driver_list->whatsapp_no, 1, 15);
                    }
                    else if(substr($driver_list->whatsapp_no, 0, 1) == '0'){
                        $phone = "62".substr($driver_list->whatsapp_no, 1, 15);
                    }
                    else{
                        $phone = $driver_list->whatsapp_no;
                    }

                    $hrRequest = HrLeaveRequest::where('hr_leave_requests.driver_request_id',$leave_request->driver_request_id)->leftjoin('users','users.id','hr_leave_requests.created_by')->first();

                    $destination = DriverDetail::where('driver_id',$hrRequest->driver_request_id)->where('category','destination')->get();
                    $dests = [];
                    for($i = 0; $i < count($destination);$i++){
                        array_push($dests, $destination[$i]->remark);
                    }

                    $day = str_replace(' ', '%20', date('l, d F Y',strtotime($driver->date_from)));

                    $start_time = date('H:i a',strtotime($driver->date_from));
                    $start_time_replace = str_replace(" ","%20",$start_time);

                    $end_time = date('H:i a',strtotime($driver->date_to));
                    $end_time_replace = str_replace(" ","%20",$end_time);

                    if ($hrRequest->return_or_not == 'NO') {
                        $end_time_replace = 'Hanya Mengantar';
                    }

                    $destinations = str_replace(" ", "%20", $driver->destination_city.'%20('.join(",%20",$dests).')');
                    $name = str_replace(" ", "%20", $driver_list->name);

                    $request_name = str_replace(" ", "%20", $hrRequest->name);

                    $message = 'Driver%20Order%0A%0ARequest%20by%20:%20'.$request_name.'%0ADay%20and%20Date%20:%20'.$day.'%0ADestination%20City%20:%20'.$destinations.'%0ADeparture%20:%20'.$start_time_replace.'%0AArrive%20:%20'.$end_time_replace.'%0APilot%20:%20'.$name.'%0A%0A-YMPI%20MIS%20Dept.-';
                }
            }

            $leave_request = HrLeaveRequest::where('request_id', '=', $request_id)->first();

            $detail_emp = HrLeaveRequestDetail::where('request_id',$request_id)->leftjoin('departments','department_name','department')->get();

            $approval_progress = HrLeaveRequestApproval::where('request_id',$request_id)->get();

            $data = [
                'leave_request' => $leave_request,
                'detail_emp' => $detail_emp,
                'approval_progress' => $approval_progress,
                'destination' => $destination,
                'driver' => null,
            ];
            Mail::to($mail_to_hr)
            ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
            ->send(new SendEmail($data, 'leave_request_shift'));

            $data = [
                'leave_request' => $leave_request,
                'detail_emp' => $detail_emp,
                'approval_progress' => $approval_progress,
                'destination' => $destination,
                'driver' => null,
            ];
            Mail::to($mail_to_manager)
                            // ->cc($cc)
            ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
            ->send(new SendEmail($data, 'leave_request_shift'));

            if (count($mail_to_gm) > 0) {
                Mail::to($mail_to_gm)
                            // ->cc($cc)
                ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
                ->send(new SendEmail($data, 'leave_request_shift'));
            }

            if (strpos($users->email, '@music.yamaha.com') !== false) {
                array_push($mail_to_user, $users->email);
                $empwahr = EmployeeSync::join('users','users.username','employee_syncs.employee_id')->where('users.id',$leave_request->created_by)->first();

                if(substr($empwahr->phone, 0, 1) == '+' ){
                    $phone = substr($empwahr->phone, 1, 15);
                }
                else if(substr($empwahr->phone, 0, 1) == '0'){
                    $phone = "62".substr($empwahr->phone, 1, 15);
                }
                else{
                    $phone = $empwahr->phone;
                }

                $message = 'Surat%20Izin%20Keluar%0A%0ARequest%20ID%20:%20'.$leave_request->request_id.'%0ATelah%20disetujui%20oleh%20HR.%0ATunjukkan%20Request%20ID%20pada%20Security%20saat%20akan%20meninggalkan%20perusahaan.%0A%0A-YMPI%20HR%20Dept.-';
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
                 ),
             ));

                curl_exec($curl);
            }else{
                $chief = Approver::where('department',$leave_request->department)->where('remark','Chief')->first();
                $foreman = Approver::where('department',$leave_request->department)->where('remark','Foreman')->first();
                if ($chief != null) {
                    if ($chief->approver_email) {
                        array_push($mail_to_user, $chief->approver_email);
                    }
                }
                if ($foreman != null) {
                    if ($foreman->approver_email) {
                        array_push($mail_to_user, $foreman->approver_email);
                    }
                }

                $empwahr = EmployeeSync::join('users','users.username','employee_syncs.employee_id')->where('users.id',$leave_request->created_by)->first();

                if(substr($empwahr->phone, 0, 1) == '+' ){
                    $phone = substr($empwahr->phone, 1, 15);
                }
                else if(substr($empwahr->phone, 0, 1) == '0'){
                    $phone = "62".substr($empwahr->phone, 1, 15);
                }
                else{
                    $phone = $empwahr->phone;
                }

                $message = 'Surat%20Izin%20Keluar%0A%0ARequest%20ID%20:%20'.$leave_request->request_id.'%0ATelah%20disetujui%20oleh%20HR.%0ATunjukkan%20Request%20ID%20pada%20Security%20saat%20akan%20meninggalkan%20perusahaan.%0A%0A-YMPI%20HR%20Dept.-';
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
                 ),
             ));

                curl_exec($curl);
            }

            if ($add_driver == 'YES') {
                $leave_request = HrLeaveRequest::where('request_id', '=', $request_id)->first();
                $leave_request->position = 'security';
                $leave_request->remark = 'Partially Approved';
                $leave_request->save();
                $driver = Driver::where('drivers.id', '=', $leave_request->driver_request_id)->join('driver_lists','drivers.driver_id','driver_lists.driver_id')->first();
                $leave_request = HrLeaveRequest::where('request_id', '=', $request_id)->first();
                $approval_progress = HrLeaveRequestApproval::where('request_id',$request_id)->get();
                $data = [
                    'leave_request' => $leave_request,
                    'detail_emp' => $detail_emp,
                    'approval_progress' => $approval_progress,
                    'remarks' => 'Security',
                    'driver' => $driver->name,
                    'phone_no' => $driver->phone_no,
                    'destination' => $destination,
                ];
                Mail::to($mail_to_user)
                                // ->cc($cc)
                ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
                ->send(new SendEmail($data, 'leave_request'));
            }else{
                $leave_request = HrLeaveRequest::where('request_id', '=', $request_id)->first();
                $leave_request->position = 'security';
                $leave_request->remark = 'Partially Approved';
                $leave_request->save();
                $leave_request = HrLeaveRequest::where('request_id', '=', $request_id)->first();
                $approval_progress = HrLeaveRequestApproval::where('request_id',$request_id)->get();
                $data = [
                    'leave_request' => $leave_request,
                    'detail_emp' => $detail_emp,
                    'approval_progress' => $approval_progress,
                    'remarks' => 'Security',
                    'driver' => null,
                ];
                Mail::to($mail_to_user)
                                // ->cc($cc)
                ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
                ->send(new SendEmail($data, 'leave_request'));
            }
        }else{
            $data = [
                'leave_request' => $leave_request,
                'detail_emp' => $detail_emp,
                'approval_progress' => $approval_progress,
                'destination' => $destination,
                'remarks' => 'Manager',
            ];

            Mail::to($mail_to)
                            // ->cc($cc)
            ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
            ->send(new SendEmail($data, 'leave_request'));
        }

        $response = array(
            'status' => true,
        );
        return Response::json($response);
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function updateLeaveRequest(Request $request)
{
    try {
        $id = Auth::user()->username;
        $emp = EmployeeSync::where('employee_id',$id)->first();
        $emps = Employee::where('employee_id',$id)->first();

        $request_id = $request->get('request_id');
        $date = $request->get('date');
        $purpose_category = $request->get('purpose_category');
        $purpose = $request->get('purpose');
        $purpose_detail = $request->get('purpose_detail');
        $time_departure = $request->get('time_departure');
        $time_arrived = $request->get('time_arrived');
        $return_or_not = $request->get('return_or_not');
        $detail_city = $request->get('detail_city');
        $add_driver = $request->get('add_driver');
        $destinations = $request->get('destinations');
        $employees = $request->get('employees');
        $city = $request->get('city');

        $dept = [];
        $pos = [];
        for ($i=0; $i < count($employees); $i++) { 
            $empcheck = EmployeeSync::where('employee_id',$employees[$i])->first();
            array_push($dept, $empcheck->department);
            array_push($pos, $empcheck->position);
        }
        $depts = array_unique($dept);
        if (count($depts) > 1) {
            $response = array(
                'status' => false,
                'message' => 'Tidak boleh lintas Department.'.join(",",$depts)
            );
            return Response::json($response);
        }

        $departure = explode(':', $time_departure);
        if (strlen($departure[0]) == 1) {
            $time0 = '0'.$departure[0];
        }else{
            $time0 = $departure[0];
        }
        $time1 = $departure[1];
        $time_departure_new = $date.' '.$time0.':'.$time1.':00';

        $arrived = explode(':', $time_arrived);
        if (strlen($arrived[0]) == 1) {
            $times0 = '0'.$arrived[0];
        }else{
            $times0 = $arrived[0];
        }
        $times1 = $arrived[1];
        $time_arrived_new = $date.' '.$times0.':'.$times1.':00';
        if ($return_or_not == 'NO') {
            $mengantar = '(Mengantar Saja)';
            if (date('Y-m-d H:i:s') >= date('Y-m-d 17:00:00') && date('Y-m-d H:i:s') <= date('Y-m-d', strtotime("+1 day")).' 05:00:00' && $time_departure_new >= $date. '17:00:00' && $time_arrived_new == $date.' 00:00:00') {
                $time_arrived_new = date('Y-m-d', strtotime("+1 day")).' 05:00:00';
            }else{
                $time_arrived_new = $date.' 16:00:00';
            }
        }else{
            $time_arrived_new = $date.' '.$times0.':'.$times1.':00';
            $mengantar = '';
        }

        $request = HrLeaveRequest::where('request_id',$request_id)->first();

        $delDriver = Driver::where('id',$request->driver_request_id)->forceDelete();
        $delDriverDetail = DriverDetail::where('driver_id',$request->driver_request_id)->forceDelete();

        $delApproval = HrLeaveRequestApproval::where('request_id',$request_id)->forceDelete();

        $destination = null;

        if ($add_driver == 'YES') {
            $driver = new Driver([
                'purpose' => ucwords(strtolower($purpose_category.' Ke '.$purpose.' '.$purpose_detail.' '.$mengantar)),
                'destination_city' => $city,
                'date_from' => $time_departure_new,
                'date_to' => $time_arrived_new,
                'created_by' => $id,
                'remark' => 'pending'
            ]);
            $driver->save();

            for ($i=0; $i < count($employees); $i++) { 
                $passenger_detail = new DriverDetail([
                    'driver_id' => $driver->id,
                    'remark' => $employees[$i],
                    'category' => 'passenger'
                ]);
                $passenger_detail->save();
            }

            for ($i=0; $i < count($destinations); $i++) { 
                $destination_detail = new DriverDetail([
                    'driver_id' => $driver->id,
                    'remark' => $destinations[$i],
                    'category' => 'destination'     
                ]);
                $destination_detail->save();
            }
            $driver_request_id = $driver->id;
            $destination = DriverDetail::where('driver_id',$driver_request_id)->get();
        }else{
            $driver_request_id = null;
        }

        $diagnose = null;
        $action = null;
        $suggestion = null;

        $paramedic = null;

        if ($purpose == 'SAKIT') {
            $position = 'clinic';
            $clinic_details = DB::SELECT("SELECT DISTINCT
                clinic_patient_details.employee_id,
                employee_syncs.`name`,
                diagnose,
                action,
                suggestion,
                paramedic 
                FROM
                clinic_patient_details
                LEFT JOIN employee_syncs ON employee_syncs.employee_id = clinic_patient_details.employee_id 
                WHERE
                DATE( clinic_patient_details.created_at ) = DATE(
                    NOW()) 
                AND purpose = 'Pulang (Sakit)' 
                AND employee_syncs.employee_id = '".$employees[0]."'");
            $diagnose_arr = [];
            $action_arr = [];
            $suggestion_arr = [];
            for($j = 0; $j < count($clinic_details);$j++){
                array_push($diagnose_arr, $clinic_details[$j]->diagnose);
                array_push($action_arr, $clinic_details[$j]->action);
                array_push($suggestion_arr, $clinic_details[$j]->suggestion);
                $paramedic = $clinic_details[$j]->paramedic;
            }

            $diagnose = join(', ',$diagnose_arr);
            $action = join(', ',$action_arr);
            $suggestion = join(', ',$suggestion_arr);
        }else{
            $position = 'applicant';
        }

        $request->diagnose = $diagnose;
        $request->action = $action;
        $request->suggestion = $suggestion;
        $request->purpose_category = $purpose_category;
        $request->purpose = $purpose;
        $request->purpose_detail = $purpose_detail;
        $request->time_departure = $time_departure_new;
        $request->time_arrived = $time_arrived_new;
        $request->return_or_not = $return_or_not;
        $request->add_driver = $add_driver;
        $request->detail_city = $detail_city;
        $request->driver_request_id = $driver_request_id;
        $request->remark = 'Requested';
        $request->save();

        $delemp = HrLeaveRequestDetail::where('request_id',$request_id)->forceDelete();

        for($i = 0; $i < count($employees); $i++){
            $empys = EmployeeSync::where('employee_id',$employees[$i])->leftjoin('departments','department_name','employee_syncs.department')->first();
            $detail = new HrLeaveRequestDetail([
                'request_id' => $request_id,
                'employee_id' => $employees[$i],
                'name' => $empys->name,
                'department' => $empys->department,
                'section' => $empys->section,
                'group' => $empys->group,
                'sub_group' => $empys->sub_group,
                'created_by' => Auth::user()->id,
            ]);
            $detail->save();

            if (date('Y-m-d H:i:s') >= date('Y-m-d 17:00:00') && date('Y-m-d H:i:s') <= date('Y-m-d', strtotime("+1 day")).' 05:00:00' && $time_departure_new >= date('Y-m-d 17:00:00') && $time_arrived_new <= date('Y-m-d', strtotime("+1 day")).' 05:00:00') {
                $time_arrival_new = explode(' ', $time_departure_new)[0].' 23:59:59';
            }else{
                $time_arrival_new = $time_arrived_new;
            }

            $start = strtotime($time_departure_new);
            $end = strtotime($time_arrival_new);
            $mins = ($end - $start) / 60 / 60;

            $check_mp = DB::connection('ympimis_2')->table('efficiency_manpowers')->where(DB::RAW('DATE_FORMAT(period,"%Y-%m")'),date('Y-m',strtotime($date)))->where('employee_id',$employees[$i])->first();
            if ($check_mp) {
                $input = DB::connection('ympimis_2')->table('efficiency_work_hours')->insert([
                    'employee_id' => $employees[$i],
                    'shift_date' => $date,
                    'department' => $check_mp->department,
                    'remark_2' => $check_mp->remark,
                    'work_hour' => '-'.round($mins,1),
                    'category' => 'Attendance',
                    'remark' => 'Izin Keluar',
                    'updated_by' => Auth::user()->username,
                    'updated_by_name' => Auth::user()->name,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $users = User::where('username',$emp->employee_id)->first();
        if (strpos($users->email, '@music.yamaha.com') !== false) {
            $applicant_email = $users->email;
        }else{
            $applicant_email = '';
        }

        $applicant = New HrLeaveRequestApproval([
            'request_id' => $request_id,
            'approver_id' => $emp->employee_id,
            'approver_name' => $emp->name,
            'approver_email' => $applicant_email,
            'status' => "Approved",
            'approved_at' => date('Y-m-d H:i:s'),
            'remark' => 'Applicant'
        ]);

        $mail_to = [];
        $cc = [];
        $dgm = null;
        $managers = null;
        $manager = null;
        if (str_contains(join(',',$pos),'Manager')) {
                            // $m = Approver::where('department', '=', $emp->department)
                            // ->where('remark', '=', 'Deputy General Manager')
                            // ->first();
            $div_manager = '';
            for ($i=0; $i < count($employees); $i++) { 
                $empcheck = EmployeeSync::where('employee_id',$employees[$i])->first();
                if (str_contains($empcheck->position,'Manager') || str_contains($empcheck->position,'Coordinator') || str_contains($empcheck->position,'Chief') || str_contains($empcheck->position,'Foreman')) {
                    $div_manager = $empcheck->division;
                    break;
                }
            }
            if ($div_manager == 'Production Division') {
                $manager = New HrLeaveRequestApproval([
                    'request_id' => $request_id,
                    'approver_id' => 'PI0109004',
                    'approver_name' => 'Budhi Apriyanto',
                    'approver_email' => 'budhi.apriyanto@music.yamaha.com',
                    'remark' => 'Deputy General Manager'
                ]);
                array_push($mail_to, 'budhi.apriyanto@music.yamaha.com');
            }else if ($div_manager == 'Production Support Division'){
                // $dgm = New HrLeaveRequestApproval([
                //     'request_id' => $request_id,
                //     'approver_id' => 'PI9905001',
                //     'approver_name' => 'Mei Rahayu',
                //     'approver_email' => 'mei.rahayu@music.yamaha.com',
                //     'remark' => 'Deputy General Manager'
                // ]);
                // array_push($mail_to, 'mei.rahayu@music.yamaha.com');
                $manager = New HrLeaveRequestApproval([
                    'request_id' => $request_id,
                    'approver_id' => 'PI0109004',
                    'approver_name' => 'Budhi Apriyanto',
                    'approver_email' => 'budhi.apriyanto@music.yamaha.com',
                    'remark' => 'General Manager'
                ]);
                array_push($mail_to, 'budhi.apriyanto@music.yamaha.com');
            }else {
                $manager = New HrLeaveRequestApproval([
                    'request_id' => $request_id,
                    'approver_id' => 'PI9709001',
                    'approver_name' => 'Arief Soekamto',
                    'approver_email' => 'arief.soekamto@music.yamaha.com',
                    'remark' => 'Director'
                ]);
                array_push($mail_to, 'arief.soekamto@music.yamaha.com');
            }
        }else if (str_contains(join(',',$pos),'Coordinator') || str_contains(join(',',$pos),'Chief') || str_contains(join(',',$pos),'Foreman')) {
            $div_manager = '';
            $dept_manager = '';
            for ($i=0; $i < count($employees); $i++) { 
                $empcheck = EmployeeSync::where('employee_id',$employees[$i])->first();
                if (str_contains($empcheck->position,'Manager') || str_contains($empcheck->position,'Coordinator') || str_contains($empcheck->position,'Chief') || str_contains($empcheck->position,'Foreman')) {
                    $div_manager = $empcheck->division;
                    $dept_manager = $empcheck->department;
                    break;
                }
            }
            if ($dept_manager != 'Management Information System Department') {
                // if ($dept_manager == 'Woodwind Instrument - Assembly (WI-A) Department' || $dept_manager == 'Woodwind Instrument - Surface Treatment (WI-ST) Department') {
                //     $manager = New HrLeaveRequestApproval([
                //         'request_id' => $request_id,
                //         'approver_id' => 'PI0109004',
                //         'approver_name' => 'Budhi Apriyanto',
                //         'approver_email' => 'budhi.apriyanto@music.yamaha.com',
                //         'remark' => 'Deputy General Manager'
                //     ]);
                //     array_push($mail_to, 'budhi.apriyanto@music.yamaha.com');
                // }else{
                $m = Approver::where('department', '=', $dept_manager)
                ->where('remark', '=', 'Manager')
                ->first();
                if($m != null){
                    $managers = New HrLeaveRequestApproval([
                        'request_id' => $request_id,
                        'approver_id' => $m->approver_id,
                        'approver_name' => $m->approver_name,
                        'approver_email' => $m->approver_email,
                        'remark' => 'Manager'
                    ]);
                    array_push($mail_to, $m->approver_email);
                }
                else{
                    $managers = New HrLeaveRequestApproval([
                        'request_id' => $request_id,
                        'approver_id' => "",
                        'approver_name' => "",
                        'approver_email' => "",
                        'status' => "none",
                        'approved_at' => date('Y-m-d H:i:s'),
                        'remark' => 'Manager'
                    ]);
                }
                // }
            }
            if ($div_manager == 'Production Division') {
                // $manager = New HrLeaveRequestApproval([
                //     'request_id' => $request_id,
                //     'approver_id' => 'PI0109004',
                //     'approver_name' => 'Budhi Apriyanto',
                //     'approver_email' => 'budhi.apriyanto@music.yamaha.com',
                //     'remark' => 'Deputy General Manager'
                // ]);
                if ($dept_manager == 'Management Information System Department') {
                    $manager = New HrLeaveRequestApproval([
                        'request_id' => $request_id,
                        'approver_id' => 'PI0109004',
                        'approver_name' => 'Budhi Apriyanto',
                        'approver_email' => 'budhi.apriyanto@music.yamaha.com',
                        'remark' => 'Manager'
                    ]);
                    array_push($mail_to, 'budhi.apriyanto@music.yamaha.com');
                }
            }else if ($div_manager == 'Production Support Division'){
                // $dgm = New HrLeaveRequestApproval([
                //     'request_id' => $request_id,
                //     'approver_id' => 'PI9905001',
                //     'approver_name' => 'Mei Rahayu',
                //     'approver_email' => 'mei.rahayu@music.yamaha.com',
                //     'remark' => 'Deputy General Manager'
                // ]);
                // $manager = New HrLeaveRequestApproval([
                //     'request_id' => $request_id,
                //     'approver_id' => 'PI0109004',
                //     'approver_name' => 'Budhi Apriyanto',
                //     'approver_email' => 'budhi.apriyanto@music.yamaha.com',
                //     'remark' => 'General Manager'
                // ]);
                // array_push($mail_to, 'mei.rahayu@music.yamaha.com');
            }else {
                // $manager = New HrLeaveRequestApproval([
                //     'request_id' => $request_id,
                //     'approver_id' => 'PI9709001',
                //     'approver_name' => 'Arief Soekamto',
                //     'approver_email' => 'arief.soekamto@music.yamaha.com',
                //     'remark' => 'Director'
                // ]);
                // array_push($mail_to, 'arief.soekamto@music.yamaha.com');
            }
        }else{
            $m = Approver::where('department', '=', $depts[0])
            ->where('remark', '=', 'Manager')
            ->first();

            if ($emps->remark == 'OFC') {
                $fc = Approver::where('department', '=', $depts[0])
                ->where('remark', '=', 'Chief')
                ->get();
                $fcs = 'Chief';
            }else{
                $fc = Approver::where('department', '=', $depts[0])
                ->where('remark', '=', 'Foreman')
                ->get();
                $fcs = 'Foreman';
            }

            if ($depts[0] == 'Production Engineering Department') {
                $manager = New HrLeaveRequestApproval([
                    'request_id' => $request_id,
                    'approver_id' => 'PI0703002',
                    'approver_name' => 'Susilo Basri Prasetyo',
                    'approver_email' => 'susilo.basri@music.yamaha.com',
                    'remark' => 'Manager'
                ]);
                array_push($mail_to, 'susilo.basri@music.yamaha.com');
            }else{
                if($m != null){
                    $manager = New HrLeaveRequestApproval([
                        'request_id' => $request_id,
                        'approver_id' => $m->approver_id,
                        'approver_name' => $m->approver_name,
                        'approver_email' => $m->approver_email,
                        'remark' => 'Manager'
                    ]);
                    array_push($mail_to, $m->approver_email);
                }
                else{
                    $manager = New HrLeaveRequestApproval([
                        'request_id' => $request_id,
                        'approver_id' => "",
                        'approver_name' => "",
                        'approver_email' => "",
                        'status' => "none",
                        'approved_at' => date('Y-m-d H:i:s'),
                        'remark' => 'Manager'
                    ]);
                }
            }

            if (count($fc) > 0) {
                for($o = 0; $o < count($fc);$o++){
                    array_push($cc, $fc[$o]->approver_email);
                }
            }
        }

        if ($purpose == 'SAKIT') {
            $clinic = New HrLeaveRequestApproval([
                'request_id' => $request_id,
                'approver_id' => "",
                'approver_name' => $paramedic,
                'approver_email' => "",
                'status' => 'Approved',
                'approved_at' => date('Y-m-d H:i:s'),
                'remark' => 'Clinic'
            ]);
            $clinic->save();
        }
        $applicant->save();



        $hr = New HrLeaveRequestApproval([
            'request_id' => $request_id,
            'approver_id' => "PI0811002",
            'approver_name' => "Mahendra Putra",
            'approver_email' => "mahendra.putra@music.yamaha.com",
            'remark' => 'HR'
        ]);

        $security = New HrLeaveRequestApproval([
            'request_id' => $request_id,
            'approver_id' => null,
            'approver_name' => null,
            'approver_email' => null,
            'remark' => 'Security'
        ]);
        if ($managers != null) {
            $managers->save();
        }
        if ($dgm != null) {
            $dgm->save();
        }
        if ($manager != null) {
            $manager->save();
        }
        $hr->save();
        $destination = null;
        if ($add_driver == 'YES') {
            $ga = New HrLeaveRequestApproval([
                'request_id' => $request_id,
                'approver_id' => "PI0904002",
                'approver_name' => "Heriyanto",
                'approver_email' => "heriyanto@music.yamaha.com",
                'remark' => 'GA'
            ]);
            $ga->save();
            $destination = DriverDetail::where('driver_id',$driver_request_id)->get();
        }
        $security->save();

        $leave_request = HrLeaveRequest::where('request_id', '=', $request_id)->first();

        $detail_emp = HrLeaveRequestDetail::where('request_id',$request_id)->leftjoin('departments','department_name','department')->get();

        $approval_progress = HrLeaveRequestApproval::where('request_id',$request_id)->get();

        if (date('Y-m-d H:i:s') >= date('Y-m-d 17:00:00') && date('Y-m-d H:i:s') <= date('Y-m-d', strtotime("+1 day")).' 05:00:00' && $time_departure_new >= date('Y-m-d 17:00:00') && $time_arrived_new <= date('Y-m-d', strtotime("+1 day")).' 05:00:00') {
            $mail_to_user = [];
            $cc = [];
            $users = User::where('id',$leave_request->created_by)->first();
            $approval_manager = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Manager')->first();
            if ($approval_manager == null) {
                $approval_manager = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Deputy General Manager')->first();
                if ($approval_manager == null) {
                    $approval_manager = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','General Manager')->first();
                    if ($approval_manager == null) {
                        $approval_manager = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Director')->first();
                    }
                }
            }
            $mail_to_manager = [];
            $cc = [];
            array_push($mail_to_manager, $approval_manager->approver_email);
            if (count($fc) > 0) {
                for($o = 0; $o < count($fc);$o++){
                    array_push($cc, $fc[$o]->approver_email);
                }
            }
            $approval_manager->status = 'Approved';
            $approval_manager->approved_at = date('Y-m-d H:i:s');
            $approval_manager->save();

            $approval_hr = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','HR')->first();
            $mail_to_hr = [];
            array_push($mail_to_hr, 'mahendra.putra@music.yamaha.com');
            array_push($mail_to_hr, 'achmad.riski.bayu@music.yamaha.com');
            array_push($mail_to_hr, 'ummi.ernawati@music.yamaha.com');
            array_push($mail_to_hr, 'adhi.satya.indradhi@music.yamaha.com');
            array_push($mail_to_hr, 'linda.febrian@music.yamaha.com');
            array_push($mail_to_hr, 'mukhamad.khoirul.anam@music.yamaha.com');
            array_push($mail_to_hr, 'dicky.kurniawan@music.yamaha.com');
            $approval_hr->status = 'Approved';
            $approval_hr->approved_at = date('Y-m-d H:i:s');
            $approval_hr->save();

            if ($add_driver == 'YES') {
                $approval_ga = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','GA')->first();
                $mail_to_ga = [];
                array_push($mail_to_ga, 'heriyanto@music.yamaha.com');
                array_push($mail_to_ga, 'rianita.widiastuti@music.yamaha.com');
                array_push($mail_to_ga, 'putri.sukma.riyanti@music.yamaha.com');
                $approval_ga->status = 'Approved';
                $approval_ga->approved_at = date('Y-m-d H:i:s');
                $approval_ga->save();

                $leave_request = HrLeaveRequest::where('request_id', '=', $request_id)->first();

                $detail_emp = HrLeaveRequestDetail::where('request_id',$request_id)->leftjoin('departments','department_name','department')->get();

                $approval_progress = HrLeaveRequestApproval::where('request_id',$request_id)->get();

                $data = [
                    'leave_request' => $leave_request,
                    'detail_emp' => $detail_emp,
                    'approval_progress' => $approval_progress,
                    'destination' => $destination,
                    'driver' => null,
                ];
                Mail::to($mail_to_ga)
                ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
                ->send(new SendEmail($data, 'leave_request_shift'));

                $select_driver = DB::SELECT("SELECT
                                    * 
                    FROM
                    driver_lists
                    JOIN sunfish_shift_syncs ON sunfish_shift_syncs.employee_id = driver_lists.driver_id 
                    WHERE
                    sunfish_shift_syncs.shift_date = '".date('Y-m-d')."' 
                    AND shiftdaily_code NOT LIKE '%Shift_1%'");

                if (count($select_driver) > 0) {
                    $driver = Driver::where('id', '=', $leave_request->driver_request_id)->first();
                    $driver->driver_id = $select_driver[0]->driver_id;
                    $driver->name = $select_driver[0]->name;
                    $driver->remark = 'received';
                    $driver->save();

                    $driver_list = DriverList::where('driver_id',$select_driver[0]->driver_id)->first();

                    if(substr($driver_list->whatsapp_no, 0, 1) == '+' ){
                        $phone = substr($driver_list->whatsapp_no, 1, 15);
                    }
                    else if(substr($driver_list->whatsapp_no, 0, 1) == '0'){
                        $phone = "62".substr($driver_list->whatsapp_no, 1, 15);
                    }
                    else{
                        $phone = $driver_list->whatsapp_no;
                    }

                    $hrRequest = HrLeaveRequest::where('hr_leave_requests.driver_request_id',$leave_request->driver_request_id)->leftjoin('users','users.id','hr_leave_requests.created_by')->first();

                    $destination = DriverDetail::where('driver_id',$hrRequest->driver_request_id)->where('category','destination')->get();
                    $dests = [];
                    for($i = 0; $i < count($destination);$i++){
                        array_push($dests, $destination[$i]->remark);
                    }

                    $day = str_replace(' ', '%20', date('l, d F Y',strtotime($driver->date_from)));

                    $start_time = date('H:i a',strtotime($driver->date_from));
                    $start_time_replace = str_replace(" ","%20",$start_time);

                    $end_time = date('H:i a',strtotime($driver->date_to));
                    $end_time_replace = str_replace(" ","%20",$end_time);

                    if ($hrRequest->return_or_not == 'NO') {
                        $end_time_replace = 'Hanya Mengantar';
                    }

                    $destinations = str_replace(" ", "%20", $driver->destination_city.'%20('.join(",%20",$dests).')');
                    $name = str_replace(" ", "%20", $driver_list->name);

                    $request_name = str_replace(" ", "%20", $hrRequest->name);

                    $message = 'Driver%20Order%0A%0ARequest%20by%20:%20'.$request_name.'%0ADay%20and%20Date%20:%20'.$day.'%0ADestination%20City%20:%20'.$destinations.'%0ADeparture%20:%20'.$start_time_replace.'%0AArrive%20:%20'.$end_time_replace.'%0APilot%20:%20'.$name.'%0A%0A-YMPI%20MIS%20Dept.-';


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
                     CURLOPT_POSTFIELDS => 'receiver=6282334197238&device=6281130561777&message='.$message.'&type=chat',
                     CURLOPT_HTTPHEADER => array(
                         'Accept: application/json',
                         'Content-Type: application/x-www-form-urlencoded',
                         'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
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
                     CURLOPT_POSTFIELDS => 'receiver=628113669869&device=6281130561777&message='.$message.'&type=chat',
                     CURLOPT_HTTPHEADER => array(
                         'Accept: application/json',
                         'Content-Type: application/x-www-form-urlencoded',
                         'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
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
                     CURLOPT_POSTFIELDS => 'receiver=628113669871&device=6281130561777&message='.$message.'&type=chat',
                     CURLOPT_HTTPHEADER => array(
                         'Accept: application/json',
                         'Content-Type: application/x-www-form-urlencoded',
                         'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
                     ),
                 ));

                    curl_exec($curl);
                }
            }

            $leave_request = HrLeaveRequest::where('request_id', '=', $request_id)->first();

            $detail_emp = HrLeaveRequestDetail::where('request_id',$request_id)->leftjoin('departments','department_name','department')->get();

            $approval_progress = HrLeaveRequestApproval::where('request_id',$request_id)->get();

            $data = [
                'leave_request' => $leave_request,
                'detail_emp' => $detail_emp,
                'approval_progress' => $approval_progress,
                'destination' => $destination,
                'driver' => null,
            ];
            Mail::to($mail_to_hr)
            ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
            ->send(new SendEmail($data, 'leave_request_shift'));

            $data = [
                'leave_request' => $leave_request,
                'detail_emp' => $detail_emp,
                'approval_progress' => $approval_progress,
                'destination' => $destination,
                'driver' => null,
            ];
            Mail::to($mail_to_manager)
            ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
            ->send(new SendEmail($data, 'leave_request_shift'));

            if (strpos($users->email, '@music.yamaha.com') !== false) {
                array_push($mail_to_user, $users->email);
                $empwahr = EmployeeSync::join('users','users.username','employee_syncs.employee_id')->where('users.id',$leave_request->created_by)->first();

                if(substr($empwahr->phone, 0, 1) == '+' ){
                    $phone = substr($empwahr->phone, 1, 15);
                }
                else if(substr($empwahr->phone, 0, 1) == '0'){
                    $phone = "62".substr($empwahr->phone, 1, 15);
                }
                else{
                    $phone = $empwahr->phone;
                }

                $message = 'Surat%20Izin%20Keluar%0A%0ARequest%20ID%20:%20'.$leave_request->request_id.'%0ATelah%20disetujui%20oleh%20HR.%0ATunjukkan%20Request%20ID%20pada%20Security%20saat%20akan%20meninggalkan%20perusahaan.%0A%0A-YMPI%20HR%20Dept.-';
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
                 ),
             ));

                curl_exec($curl);
            }else{
                $chief = Approver::where('department',$leave_request->department)->where('remark','Chief')->first();
                $foreman = Approver::where('department',$leave_request->department)->where('remark','Foreman')->first();
                if ($chief != null) {
                    if ($chief->approver_email) {
                        array_push($mail_to_user, $chief->approver_email);
                    }
                }
                if ($foreman != null) {
                    if ($foreman->approver_email) {
                        array_push($mail_to_user, $foreman->approver_email);
                    }
                }

                $empwahr = EmployeeSync::join('users','users.username','employee_syncs.employee_id')->where('users.id',$leave_request->created_by)->first();

                if(substr($empwahr->phone, 0, 1) == '+' ){
                    $phone = substr($empwahr->phone, 1, 15);
                }
                else if(substr($empwahr->phone, 0, 1) == '0'){
                    $phone = "62".substr($empwahr->phone, 1, 15);
                }
                else{
                    $phone = $empwahr->phone;
                }

                $message = 'Surat%20Izin%20Keluar%0A%0ARequest%20ID%20:%20'.$leave_request->request_id.'%0ATelah%20disetujui%20oleh%20HR.%0ATunjukkan%20Request%20ID%20pada%20Security%20saat%20akan%20meninggalkan%20perusahaan.%0A%0A-YMPI%20HR%20Dept.-';
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
                 ),
             ));

                curl_exec($curl);
            }

            if ($add_driver == 'YES') {
                $leave_request = HrLeaveRequest::where('request_id', '=', $request_id)->first();
                $leave_request->position = 'security';
                $leave_request->remark = 'Partially Approved';
                $leave_request->save();
                $driver = Driver::where('drivers.id', '=', $leave_request->driver_request_id)->join('driver_lists','drivers.driver_id','driver_lists.driver_id')->first();
                $leave_request = HrLeaveRequest::where('request_id', '=', $request_id)->first();
                $approval_progress = HrLeaveRequestApproval::where('request_id',$request_id)->get();
                $data = [
                    'leave_request' => $leave_request,
                    'detail_emp' => $detail_emp,
                    'approval_progress' => $approval_progress,
                    'remarks' => 'Security',
                    'driver' => $driver->name,
                    'phone_no' => $driver->phone_no,
                    'destination' => $destination,
                ];
                Mail::to($mail_to_user)
                                // ->cc($cc)
                ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
                ->send(new SendEmail($data, 'leave_request'));
            }else{
                $leave_request = HrLeaveRequest::where('request_id', '=', $request_id)->first();
                $leave_request->position = 'security';
                $leave_request->remark = 'Partially Approved';
                $leave_request->save();
                $leave_request = HrLeaveRequest::where('request_id', '=', $request_id)->first();
                $approval_progress = HrLeaveRequestApproval::where('request_id',$request_id)->get();
                $data = [
                    'leave_request' => $leave_request,
                    'detail_emp' => $detail_emp,
                    'approval_progress' => $approval_progress,
                    'remarks' => 'Security',
                    'driver' => null,
                ];
                Mail::to($mail_to_user)
                                // ->cc($cc)
                ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
                ->send(new SendEmail($data, 'leave_request'));
            }
        }else{
            $data = [
                'leave_request' => $leave_request,
                'detail_emp' => $detail_emp,
                'approval_progress' => $approval_progress,
                'remarks' => 'Manager',
                'destination' => $destination,
                'edited' => 'Edited',
            ];

            Mail::to($mail_to)
            ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
            ->send(new SendEmail($data, 'leave_request'));
        }

        $response = array(
            'status' => true,
        );
        return Response::json($response);
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function deleteLeaveRequest(Request $request)
{
    try {
        $request_id = $request->get('request_id');
        $leave_request = HrLeaveRequest::where('request_id',$request_id)->first();
        if ($leave_request) {
            $approval = HrLeaveRequestApproval::where('request_id',$request_id)->first();
            if ($approval) {
                $delete_approval = HrLeaveRequestApproval::where('request_id',$request_id)->forceDelete();
            }
            $detail = HrLeaveRequestDetail::where('request_id',$request_id)->first();
            if ($detail) {
                $delete_detail = HrLeaveRequestDetail::where('request_id',$request_id)->forceDelete();
            }
            if ($leave_request->driver_request_id != null) {
                $driver = Driver::where('id',$leave_request->driver_request_id)->first();
                if ($driver) {
                    $delete_driver = Driver::where('id',$leave_request->driver_request_id)->forceDelete();
                }
                $driver_detail = Driver::where('driver_id',$leave_request->driver_request_id)->first();
                if ($driver_detail) {
                    $delete_driver_detail = Driver::where('driver_id',$leave_request->driver_request_id)->forceDelete();
                }
            }
            $delete_leave_request = HrLeaveRequest::where('request_id',$request_id)->forceDelete();
        }
        $response = array(
            'status' => true,
        );
        return Response::json($response);
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function approvalLeaveRequest($request_id,$remark)
{
    $approval = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark',$remark)->first();
    if ($approval == null) {
        $approval = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Deputy General Manager')->first();
        if ($approval == null) {
            $approval = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','General Manager')->first();
            if ($approval == null) {
                $approval = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Director')->first();
                if ($approval == null) {
                    $approval = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Chief')->first();
                }
            }
        }
    }
    $status_approval = '';
    $driver_lists = DriverList::orderBy('name', 'asc')->get();
    if ($approval != null) {
        $leave_request = HrLeaveRequest::where('request_id', '=', $request_id)->first();
        $detail_emp = HrLeaveRequestDetail::where('request_id',$request_id)->leftjoin('departments','department_name','department')->get();
        $approval_progress = HrLeaveRequestApproval::where('request_id',$request_id)->get();

        $div_manager = '';
        for ($i=0; $i < count($detail_emp); $i++) { 
            $empcheck = EmployeeSync::where('employee_id',$detail_emp[$i]->employee_id)->first();
            if (str_contains($empcheck->position,'Manager') || str_contains($empcheck->position,'Coordinator') || str_contains($empcheck->position,'Chief') || str_contains($empcheck->position,'Foreman')) {
                $div_manager = 'Termasuk';
                break;
            }
        }

        $users = User::where('id',$leave_request->created_by)->first();

        $mail_to = [];
        $cc = [];

        $destination = null;

        if ($leave_request->add_driver == 'YES') {
            $destination = DriverDetail::where('driver_id',$leave_request->driver_request_id)->get();
        }

        $status_approval = '';

        if ($remark == 'Applicant') {
            if ($approval->status == null) {
                $approval->status = 'Approved';
                $approval->approved_at = date('Y-m-d H:i:s');
                if (Auth::user()->username != null) {
                    $approval->real_approvers = Auth::user()->username;
                }
                $app_manager = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Manager')->first();
                if ($app_manager == null) {
                    $app_manager = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Deputy General Manager')->first();
                    if ($app_manager == null) {
                        $app_manager = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','General Manager')->first();
                        if ($app_manager == null) {
                            $app_manager = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Director')->first();
                        }
                    }
                }
                array_push($mail_to, $app_manager->approver_email);
                                // $emp = EmployeeSync::where('employee_id',$users->username)->first();
                                // $emps = Employee::where('employee_id',$users->username)->first();
                                // if ($emps->remark == 'OFC') {
                                //     $fc = Approver::where('department', '=', $emp->department)
                                //     ->where('remark', '=', 'Chief')
                                //     ->get();
                                //     $fcs = 'Chief';
                                // }else{
                                //     $fc = Approver::where('department', '=', $emp->department)
                                //     ->where('remark', '=', 'Foreman')
                                //     ->get();
                                //     $fcs = 'Foreman';
                                // }
                                // if (count($fc) > 0) {
                                //     for($o = 0; $o < count($fc);$o++){
                                //         array_push($cc, $fc[$o]->approver_email);
                                //     }
                                // }
                $remarks = 'Manager';
            }else{
                $status_approval = 'Approved';
            }
        }

        if ($remark == 'Manager' || $remark == 'Chief') {
            // if ($detail_emp[0]->id_division == 4 && $approval->remark == 'Manager' && $approval->status == null && $div_manager != '') {
            //     if ($approval->status == null) {
            //         $approval->status = 'Approved';
            //         $approval->approved_at = date('Y-m-d H:i:s');
            //         if (Auth::user()->username != null) {
            //             $approval->real_approvers = Auth::user()->username;
            //         }
            //         array_push($mail_to, 'mei.rahayu@music.yamaha.com');
            //         $remarks = 'Deputy General Manager';
            //     }else{
            //         $status_approval = 'Approved';
            //     }
            // }else if ($detail_emp[0]->id_division == 4 && $approval->remark == 'Deputy General Manager' && $approval->status == null && $div_manager != '') {
            //     if ($approval->status == null) {
            //         $approval->status = 'Approved';
            //         $approval->approved_at = date('Y-m-d H:i:s');
            //         if (Auth::user()->username != null) {
            //             $approval->real_approvers = Auth::user()->username;
            //         }
            //         array_push($mail_to, 'budhi.apriyanto@music.yamaha.com');
            //         $remarks = 'General Manager';
            //     }else{
            //         $status_approval = 'Approved';
            //     }
            // }else if ($detail_emp[0]->id_division == 4 && $approval->remark == 'General Manager' && $approval->status == null && $div_manager != '') {
            //     if ($approval->status == null) {
            //         $approval->status = 'Approved';
            //         $approval->approved_at = date('Y-m-d H:i:s');
            //         if (Auth::user()->username != null) {
            //             $approval->real_approvers = Auth::user()->username;
            //         }
            //         array_push($mail_to, 'mahendra.putra@music.yamaha.com');
            //         array_push($mail_to, 'ummi.ernawati@music.yamaha.com');
            //         array_push($mail_to, 'adhi.satya.indradhi@music.yamaha.com');
            //         array_push($mail_to, 'linda.febrian@music.yamaha.com');
            //         array_push($mail_to, 'mukhamad.khoirul.anam@music.yamaha.com');
            //         array_push($mail_to, 'dicky.kurniawan@music.yamaha.com');
            //         $remarks = 'HR';
            //     }else{
            //         $status_approval = 'Approved';
            //     }
            // }else if ($detail_emp[0]->id_division == 5 && $approval->remark == 'Manager' && $approval->status == null && $div_manager != '') {
            //     if ($approval->status == null) {
            //         $approval->status = 'Approved';
            //         $approval->approved_at = date('Y-m-d H:i:s');
            //         if (Auth::user()->username != null) {
            //             $approval->real_approvers = Auth::user()->username;
            //         }
            //         array_push($mail_to, 'budhi.apriyanto@music.yamaha.com');
            //         $remarks = 'Deputy General Manager';
            //     }else{
            //         $status_approval = 'Approved';
            //     }
            // }else if ($detail_emp[0]->id_division == 1 && $approval->remark == 'Manager' && $approval->status == null && $div_manager != '') {
            //     if ($approval->status == null) {
            //         $approval->status = 'Approved';
            //         $approval->approved_at = date('Y-m-d H:i:s');
            //         if (Auth::user()->username != null) {
            //             $approval->real_approvers = Auth::user()->username;
            //         }
            //         array_push($mail_to, 'arief.soekamto@music.yamaha.com');
            //         $remarks = 'Director';
            //     }else{
            //         $status_approval = 'Approved';
            //     }
            // }else if ($detail_emp[0]->id_division == 2 && $approval->remark == 'Manager' && $approval->status == null && $div_manager != '') {
            //     if ($approval->status == null) {
            //         $approval->status = 'Approved';
            //         $approval->approved_at = date('Y-m-d H:i:s');
            //         if (Auth::user()->username != null) {
            //             $approval->real_approvers = Auth::user()->username;
            //         }
            //         array_push($mail_to, 'arief.soekamto@music.yamaha.com');
            //         $remarks = 'Director';
            //     }else{
            //         $status_approval = 'Approved';
            //     }
            // }else{
            if ($approval->status == null) {
                $approval->status = 'Approved';
                $approval->approved_at = date('Y-m-d H:i:s');
                if (Auth::user()->username != null) {
                    $approval->real_approvers = Auth::user()->username;
                }
                array_push($mail_to, 'mahendra.putra@music.yamaha.com');
                array_push($mail_to, 'achmad.riski.bayu@music.yamaha.com');
                array_push($mail_to, 'ummi.ernawati@music.yamaha.com');
                array_push($mail_to, 'adhi.satya.indradhi@music.yamaha.com');
                array_push($mail_to, 'linda.febrian@music.yamaha.com');
                array_push($mail_to, 'mukhamad.khoirul.anam@music.yamaha.com');
                array_push($mail_to, 'dicky.kurniawan@music.yamaha.com');
                $remarks = 'HR';
            }else{
                $status_approval = 'Approved';
            }
            // }
        }

        if ($remark == 'Deputy General Manager') {
            // if ($detail_emp[0]->id_division == 4 && $approval->remark == 'Manager' && $approval->status == null && $div_manager != '') {
            //     if ($approval->status == null) {
            //         $approval->status = 'Approved';
            //         $approval->approved_at = date('Y-m-d H:i:s');
            //         if (Auth::user()->username != null) {
            //             $approval->real_approvers = Auth::user()->username;
            //         }
            //         array_push($mail_to, 'mei.rahayu@music.yamaha.com');
            //         $remarks = 'Deputy General Manager';
            //     }else{
            //         $status_approval = 'Approved';
            //     }
            // }else if ($detail_emp[0]->id_division == 4 && $approval->remark == 'Deputy General Manager' && $approval->status == null && $div_manager != '') {
            //     if ($approval->status == null) {
            //         $approval->status = 'Approved';
            //         $approval->approved_at = date('Y-m-d H:i:s');
            //         if (Auth::user()->username != null) {
            //             $approval->real_approvers = Auth::user()->username;
            //         }
            //         array_push($mail_to, 'budhi.apriyanto@music.yamaha.com');
            //         $remarks = 'General Manager';
            //     }else{
            //         $status_approval = 'Approved';
            //     }
            // }else if ($detail_emp[0]->id_division == 4 && $approval->remark == 'General Manager' && $approval->status == null && $div_manager != '') {
            //     if ($approval->status == null) {
            //         $approval->status = 'Approved';
            //         $approval->approved_at = date('Y-m-d H:i:s');
            //         if (Auth::user()->username != null) {
            //             $approval->real_approvers = Auth::user()->username;
            //         }
            //         array_push($mail_to, 'mahendra.putra@music.yamaha.com');
            //         array_push($mail_to, 'ummi.ernawati@music.yamaha.com');
            //         array_push($mail_to, 'adhi.satya.indradhi@music.yamaha.com');
            //         array_push($mail_to, 'linda.febrian@music.yamaha.com');
            //         array_push($mail_to, 'mukhamad.khoirul.anam@music.yamaha.com');
            //         array_push($mail_to, 'dicky.kurniawan@music.yamaha.com');
            //         $remarks = 'HR';
            //     }else{
            //         $status_approval = 'Approved';
            //     }
            // }else if ($detail_emp[0]->id_division == 5 && $approval->remark == 'Deputy General Manager' && $approval->status == null && $div_manager != '') {
            //     if ($approval->status == null) {
            //         $approval->status = 'Approved';
            //         $approval->approved_at = date('Y-m-d H:i:s');
            //         if (Auth::user()->username != null) {
            //             $approval->real_approvers = Auth::user()->username;
            //         }
            //         array_push($mail_to, 'mahendra.putra@music.yamaha.com');
            //         array_push($mail_to, 'ummi.ernawati@music.yamaha.com');
            //         array_push($mail_to, 'adhi.satya.indradhi@music.yamaha.com');
            //         array_push($mail_to, 'linda.febrian@music.yamaha.com');
            //         array_push($mail_to, 'mukhamad.khoirul.anam@music.yamaha.com');
            //         array_push($mail_to, 'dicky.kurniawan@music.yamaha.com');
            //         $remarks = 'HR';
            //     }else{
            //         $status_approval = 'Approved';
            //     }
            // }else if ($detail_emp[0]->id_division == 1 && $approval->remark == 'Manager' && $approval->status == null && $div_manager != '') {
            //     if ($approval->status == null) {
            //         $approval->status = 'Approved';
            //         $approval->approved_at = date('Y-m-d H:i:s');
            //         if (Auth::user()->username != null) {
            //             $approval->real_approvers = Auth::user()->username;
            //         }
            //         array_push($mail_to, 'arief.soekamto@music.yamaha.com');
            //         $remarks = 'Director';
            //     }else{
            //         $status_approval = 'Approved';
            //     }
            // }else if ($detail_emp[0]->id_division == 2 && $approval->remark == 'Manager' && $approval->status == null && $div_manager != '') {
            //     if ($approval->status == null) {
            //         $approval->status = 'Approved';
            //         $approval->approved_at = date('Y-m-d H:i:s');
            //         if (Auth::user()->username != null) {
            //             $approval->real_approvers = Auth::user()->username;
            //         }
            //         array_push($mail_to, 'arief.soekamto@music.yamaha.com');
            //         $remarks = 'Director';
            //     }else{
            //         $status_approval = 'Approved';
            //     }
            // }else{
            if ($approval->status == null) {
                $approval->status = 'Approved';
                $approval->approved_at = date('Y-m-d H:i:s');
                if (Auth::user()->username != null) {
                    $approval->real_approvers = Auth::user()->username;
                }
                array_push($mail_to, 'mahendra.putra@music.yamaha.com');
                array_push($mail_to, 'achmad.riski.bayu@music.yamaha.com');
                array_push($mail_to, 'ummi.ernawati@music.yamaha.com');
                array_push($mail_to, 'adhi.satya.indradhi@music.yamaha.com');
                array_push($mail_to, 'linda.febrian@music.yamaha.com');
                array_push($mail_to, 'mukhamad.khoirul.anam@music.yamaha.com');
                array_push($mail_to, 'dicky.kurniawan@music.yamaha.com');
                $remarks = 'HR';
            }else{
                $status_approval = 'Approved';
            }
            // }
        }

        if ($remark == 'General Manager') {
            // if ($detail_emp[0]->id_division == 4 && $approval->remark == 'Manager' && $approval->status == null && $div_manager != '') {
            //     if ($approval->status == null) {
            //         $approval->status = 'Approved';
            //         $approval->approved_at = date('Y-m-d H:i:s');
            //         if (Auth::user()->username != null) {
            //             $approval->real_approvers = Auth::user()->username;
            //         }
            //         array_push($mail_to, 'mei.rahayu@music.yamaha.com');
            //         $remarks = 'Deputy General Manager';
            //     }else{
            //         $status_approval = 'Approved';
            //     }
            // }else if ($detail_emp[0]->id_division == 4 && $approval->remark == 'Deputy General Manager' && $approval->status == null && $div_manager != '') {
            //     if ($approval->status == null) {
            //         $approval->status = 'Approved';
            //         $approval->approved_at = date('Y-m-d H:i:s');
            //         if (Auth::user()->username != null) {
            //             $approval->real_approvers = Auth::user()->username;
            //         }
            //         array_push($mail_to, 'budhi.apriyanto@music.yamaha.com');
            //         $remarks = 'General Manager';
            //     }else{
            //         $status_approval = 'Approved';
            //     }
            // }else if ($detail_emp[0]->id_division == 4 && $approval->remark == 'General Manager' && $approval->status == null && $div_manager != '') {
            //     if ($approval->status == null) {
            //         $approval->status = 'Approved';
            //         $approval->approved_at = date('Y-m-d H:i:s');
            //         if (Auth::user()->username != null) {
            //             $approval->real_approvers = Auth::user()->username;
            //         }
            //         array_push($mail_to, 'mahendra.putra@music.yamaha.com');
            //         array_push($mail_to, 'ummi.ernawati@music.yamaha.com');
            //         array_push($mail_to, 'adhi.satya.indradhi@music.yamaha.com');
            //         array_push($mail_to, 'linda.febrian@music.yamaha.com');
            //         array_push($mail_to, 'mukhamad.khoirul.anam@music.yamaha.com');
            //         array_push($mail_to, 'dicky.kurniawan@music.yamaha.com');
            //         $remarks = 'HR';
            //     }else{
            //         $status_approval = 'Approved';
            //     }
            // }else if ($detail_emp[0]->id_division == 1 && $approval->remark == 'Manager' && $approval->status == null && $div_manager != '') {
            //     if ($approval->status == null) {
            //         $approval->status = 'Approved';
            //         $approval->approved_at = date('Y-m-d H:i:s');
            //         if (Auth::user()->username != null) {
            //             $approval->real_approvers = Auth::user()->username;
            //         }
            //         array_push($mail_to, 'arief.soekamto@music.yamaha.com');
            //         $remarks = 'Director';
            //     }else{
            //         $status_approval = 'Approved';
            //     }
            // }else if ($detail_emp[0]->id_division == 2 && $approval->remark == 'Manager' && $approval->status == null && $div_manager != '') {
            //     if ($approval->status == null) {
            //         $approval->status = 'Approved';
            //         $approval->approved_at = date('Y-m-d H:i:s');
            //         array_push($mail_to, 'arief.soekamto@music.yamaha.com');
            //         $remarks = 'Director';
            //     }else{
            //         $status_approval = 'Approved';
            //     }
            // }else{
            if ($approval->status == null) {
                $approval->status = 'Approved';
                $approval->approved_at = date('Y-m-d H:i:s');
                if (Auth::user()->username != null) {
                    $approval->real_approvers = Auth::user()->username;
                }
                array_push($mail_to, 'mahendra.putra@music.yamaha.com');
                array_push($mail_to, 'achmad.riski.bayu@music.yamaha.com');
                array_push($mail_to, 'ummi.ernawati@music.yamaha.com');
                array_push($mail_to, 'adhi.satya.indradhi@music.yamaha.com');
                array_push($mail_to, 'linda.febrian@music.yamaha.com');
                array_push($mail_to, 'mukhamad.khoirul.anam@music.yamaha.com');
                array_push($mail_to, 'dicky.kurniawan@music.yamaha.com');
                $remarks = 'HR';
            }else{
                $status_approval = 'Approved';
            }
            // }
        }

        if ($remark == 'Director') {
            if ($approval->status == null) {
                $approval->status = 'Approved';
                $approval->approved_at = date('Y-m-d H:i:s');
                if (Auth::user()->username != null) {
                    $approval->real_approvers = Auth::user()->username;
                }
                array_push($mail_to, 'mahendra.putra@music.yamaha.com');
                array_push($mail_to, 'achmad.riski.bayu@music.yamaha.com');
                array_push($mail_to, 'ummi.ernawati@music.yamaha.com');
                array_push($mail_to, 'adhi.satya.indradhi@music.yamaha.com');
                array_push($mail_to, 'linda.febrian@music.yamaha.com');
                array_push($mail_to, 'mukhamad.khoirul.anam@music.yamaha.com');
                array_push($mail_to, 'dicky.kurniawan@music.yamaha.com');
                $remarks = 'HR';
            }else{
                $status_approval = 'Approved';
            }
        }

        // if ($remark == 'Deputy General Manager' || $remark == 'General Manager') {

        // }

        if ($remark == 'HR') {
            if ($approval->status == null) {
                $employee_id = Auth::user()->username;
                if ($employee_id != null) {
                    $emp = EmployeeSync::where('employee_id',$employee_id)->first();
                    $approval->approver_id = strtoupper($employee_id);
                    $approval->approver_name = $emp->name;
                    $approval->status = 'Approved';
                    $approval->approved_at = date('Y-m-d H:i:s');
                    if (Auth::user()->username != null) {
                        $approval->real_approvers = Auth::user()->username;
                    }
                    $remarks = 'Security';
                }else{
                    $approval->approver_id = 'PI0811002';
                    $approval->approver_name = 'Mahendra Putra';
                    $approval->status = 'Approved';
                    $approval->approval_id = date('Y-m-d H:i:s');
                    if (Auth::user()->username != null) {
                        $approval->real_approvers = Auth::user()->username;
                    }
                    $remarks = 'Security';
                }
                if ($leave_request->add_driver == 'YES') {
                    array_push($mail_to, 'heriyanto@music.yamaha.com');
                    array_push($mail_to, 'rianita.widiastuti@music.yamaha.com');
                    array_push($mail_to, 'putri.sukma.riyanti@music.yamaha.com');
                    $remarks = 'GA';
                }else{
                    if (strpos($users->email, '@music.yamaha.com') !== false) {
                        array_push($mail_to, $users->email);
                        $empwahr = EmployeeSync::join('users','users.username','employee_syncs.employee_id')->where('users.id',$leave_request->created_by)->first();

                        if(substr($empwahr->phone, 0, 1) == '+' ){
                            $phone = substr($empwahr->phone, 1, 15);
                        }
                        else if(substr($empwahr->phone, 0, 1) == '0'){
                            $phone = "62".substr($empwahr->phone, 1, 15);
                        }
                        else{
                            $phone = $empwahr->phone;
                        }

                        $message = 'Surat%20Izin%20Keluar%0A%0ARequest%20ID%20:%20'.$leave_request->request_id.'%0ATelah%20disetujui%20oleh%20HR.%0ATunjukkan%20Request%20ID%20pada%20Security%20saat%20akan%20meninggalkan%20perusahaan.%0A%0A-YMPI%20HR%20Dept.-';
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
                         ),
                     ));

                        curl_exec($curl);
                    }else{
                        $chief = Approver::where('department',$leave_request->department)->where('remark','Chief')->first();
                        $foreman = Approver::where('department',$leave_request->department)->where('remark','Foreman')->first();
                        if ($chief != null) {
                            if ($chief->approver_email) {
                                array_push($mail_to, $chief->approver_email);
                            }
                        }
                        if ($foreman != null) {
                            if ($foreman->approver_email) {
                                array_push($mail_to, $foreman->approver_email);
                            }
                        }

                        $empwahr = EmployeeSync::join('users','users.username','employee_syncs.employee_id')->where('users.id',$leave_request->created_by)->first();

                        if(substr($empwahr->phone, 0, 1) == '+' ){
                            $phone = substr($empwahr->phone, 1, 15);
                        }
                        else if(substr($empwahr->phone, 0, 1) == '0'){
                            $phone = "62".substr($empwahr->phone, 1, 15);
                        }
                        else{
                            $phone = $empwahr->phone;
                        }

                        $message = 'Surat%20Izin%20Keluar%0A%0ARequest%20ID%20:%20'.$leave_request->request_id.'%0ATelah%20disetujui%20oleh%20HR.%0ATunjukkan%20Request%20ID%20pada%20Security%20saat%20akan%20meninggalkan%20perusahaan.%0A%0A-YMPI%20HR%20Dept.-';
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
                         ),
                     ));

                        curl_exec($curl);
                        $remarks = 'Security';
                    }
                }
            }else{
                $status_approval = 'Approved';
            }
        }
        if ($remark == 'GA') {
            if ($approval->status == null) {
                $employee_id = Auth::user()->username;
                if ($employee_id != null) {
                    $emp = EmployeeSync::where('employee_id',$employee_id)->first();
                    $approval->approver_id = strtoupper($employee_id);
                    $approval->approver_name = $emp->name;
                    $approval->status = 'Approved';
                    $approval->approved_at = date('Y-m-d H:i:s');
                    if (Auth::user()->username != null) {
                        $approval->real_approvers = Auth::user()->username;
                    }
                }else{
                    $approval->approver_id = 'PI0904002';
                    $approval->approver_name = 'Heriyanto';
                    $approval->status = 'Approved';
                    $approval->approval_id = date('Y-m-d H:i:s');
                    if (Auth::user()->username != null) {
                        $approval->real_approvers = Auth::user()->username;
                    }
                }
                if (strpos($users->email, '@music.yamaha.com') !== false) {
                    array_push($mail_to, $users->email);
                    $remarks = 'Security';
                }else{
                    $chief = Approver::where('department',$leave_request->department)->where('remark','Chief')->first();
                    $foreman = Approver::where('department',$leave_request->department)->where('remark','Foreman')->first();
                    if ($chief != null) {
                        if ($chief->approver_email) {
                            array_push($mail_to, $chief->approver_email);
                        }
                    }
                    if ($foreman != null) {
                        if ($foreman->approver_email) {
                            array_push($mail_to, $foreman->approver_email);
                        }
                    }
                    $remarks = 'Security';
                }
            }else{
                $status_approval = 'Approved';
            }
        }
    }else{
        $leave_request = null;
    }

    $car_detail = DB::SELECT("SELECT
        plat_no,
        car 
        FROM
        driver_lists ");

    try {
        if ($status_approval == 'Approved') {
            $driver_lists = DriverList::orderBy('name', 'asc')->get();
            return view('human_resource.leave_request.approval')->with('head','Persetujuan Surat Izin Keluar')->with('message','Request ID : '.$request_id.' pernah disetujui.')->with('page','Persetujuan Surat Izin Keluar')->with('driver',$driver_lists)->with('remark',$remark)->with('driver_ids',0)->with('cars',$car_detail);
        }else{
            if ($leave_request != null) {
                $leave_request->position = strtolower($remarks);
                $leave_request->remark = 'Partially Approved';
                $leave_request->save();
                $approval->save();

                $leave_request = HrLeaveRequest::where('request_id', '=', $request_id)->first();
                $detail_emp = HrLeaveRequestDetail::where('request_id',$request_id)->leftjoin('departments','department_name','department')->get();
                $approval_progress = HrLeaveRequestApproval::where('request_id',$request_id)->get();

                $data = [
                    'leave_request' => $leave_request,
                    'detail_emp' => $detail_emp,
                    'approval_progress' => $approval_progress,
                    'remarks' => $remarks,
                    'destination' => $destination,
                    'driver' => null
                ];
                if ($remarks != 'Security') {
                    if (count($cc) > 0 && $cc[0] != '') {
                        Mail::to($mail_to)
                        ->cc($cc)
                        ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
                        ->send(new SendEmail($data, 'leave_request'));
                    }else{
                        Mail::to($mail_to)
                                        // ->cc($cc)
                        ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
                        ->send(new SendEmail($data, 'leave_request'));
                    }
                }
                $driver_lists = DriverList::orderBy('name', 'asc')->get();

                return view('human_resource.leave_request.approval')->with('head','Persetujuan Surat Izin Keluar')->with('message','Request ID : '.$request_id.' telah disetujui.')->with('page','Persetujuan Surat Izin Keluar')->with('driver',$driver_lists)->with('remark',$remark)->with('driver_ids',$leave_request->driver_request_id)->with('cars',$car_detail);
            }else{
                $driver_lists = DriverList::orderBy('name', 'asc')->get();
                return view('human_resource.leave_request.approval')->with('head','Persetujuan Surat Izin Keluar')->with('message','Request ID : '.$request_id.' telah dihapus.')->with('page','Persetujuan Surat Izin Keluar')->with('driver',$driver_lists)->with('remark',$remark)->with('driver_ids',0)->with('cars',$car_detail);
            }
        }
    } catch (\Exception $e) {
        $driver_lists = DriverList::orderBy('name', 'asc')->get();
        return view('human_resource.leave_request.approval')->with('head','Persetujuan Surat Izin Keluar')->with('message','Gagal menyetujui Surat Izin Keluar dengan Error = '.$e->getMessage())->with('page','Persetujuan Surat Izin Keluar')->with('driver',$driver_lists)->with('remark',$remark)->with('driver_ids',0)->with('cars',$car_detail);
    }
}

public function resendLeaveRequest($request_id,$remark)
{
    $employee_id = Auth::user()->username;
    $approval = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark',$remark)->first();
    // if ($approval == null) {
    //     $approval = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Deputy General Manager')->first();
    //     if ($approval == null) {
    //         $approval = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','General Manager')->first();
    //         if ($approval == null) {
    //             $approval = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Director')->first();
    //         }
    //     }
    // }

    // var_dump($approval);
    // die();
    // $approval->status = null;
    // $approval->approved_at = null;
    // $approval->save();

    // $approval = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark',$remark)->first();
    // if ($approval == null) {
    //     $approval = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Deputy General Manager')->first();
    //     if ($approval == null) {
    //         $approval = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','General Manager')->first();
    //         if ($approval == null) {
    //             $approval = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Director')->first();
    //         }
    //     }
    // }

    // $status_approval = '';
    $mail_to = [];

    // if ($remark == 'Manager') {
    if ($remark == 'HR') {
        array_push($mail_to, 'mahendra.putra@music.yamaha.com');
        array_push($mail_to, 'achmad.riski.bayu@music.yamaha.com');
        array_push($mail_to, 'ummi.ernawati@music.yamaha.com');
        array_push($mail_to, 'adhi.satya.indradhi@music.yamaha.com');
        array_push($mail_to, 'linda.febrian@music.yamaha.com');
        array_push($mail_to, 'mukhamad.khoirul.anam@music.yamaha.com');
        array_push($mail_to, 'dicky.kurniawan@music.yamaha.com');
        $remarks = 'HR';
    }else if($remark == 'GA'){
        array_push($mail_to, 'heriyanto@music.yamaha.com');
        array_push($mail_to, 'rianita.widiastuti@music.yamaha.com');
        array_push($mail_to, 'putri.sukma.riyanti@music.yamaha.com');
        $remarks = 'GA';
    }else{
        $app_manager = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark',$remark)->first();
        array_push($mail_to, $app_manager->approver_email);
        $remarks = $remark;
    }

    $leave_request = HrLeaveRequest::where('request_id', '=', $request_id)->first();
    $detail_emp = HrLeaveRequestDetail::where('request_id',$request_id)->leftjoin('departments','department_name','department')->get();
    $approval_progress = HrLeaveRequestApproval::where('request_id',$request_id)->get();

    try {
        $destination = null;

        if ($leave_request->add_driver == 'YES') {
            $destination = DriverDetail::where('driver_id',$leave_request->driver_request_id)->get();
        }

        $data = [
            'leave_request' => $leave_request,
            'detail_emp' => $detail_emp,
            'approval_progress' => $approval_progress,
            'remarks' => $remarks,
            'destination' => $destination,
            'driver' => null,
        ];
        Mail::to($mail_to)
        ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
        ->send(new SendEmail($data, 'leave_request'));

        $response = array(
            'status' => true,
            'message' => 'Success Resend Email'
        );
        return Response::json($response);
        
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function cancelPelaporanKanagataRequest(Request $request)
{
    try {
        $request_id = $request->get('request_id');

        $leave_request = HrLeaveRequest::where('request_id',$request_id)->first();

        if ($leave_request->add_driver == 'YES') {
            $driver = Driver::where('id',$leave_request->driver_request_id)->first();
            $driver_detail = DriverDetail::where('driver_id',$driver->id)->forceDelete();

            $driver->forceDelete();
        }



        $approval = HrLeaveRequestApproval::where('request_id',$request_id)->forceDelete();
        $leave_request_detail = HrLeaveRequestDetail::where('request_id',$request_id)->forceDelete();

        $leave_request->forceDelete();

        $response = array(
            'status' => true,
            'message' => 'Success Cancel Request'
        );
        return Response::json($response);
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function rejectLeaveRequest($request_id,$remark)
{
    try {
        $leave_request = HrLeaveRequest::where('request_id', '=', $request_id)->first();
        $approval = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Manager')->first();
        if ($approval == null) {
            $approval = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Deputy General Manager')->first();
            if ($approval == null) {
                $approval = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','General Manager')->first();
                if ($approval == null) {
                    $approval = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Director')->first();
                }
            }
        }
        if ($approval->status == null) {
            return view('human_resource.leave_request.reject')->with('head','Surat Izin Keluar Ditolak')->with('message','Request ID : '.$request_id.' telah ditolak.')->with('page','Surat Izin Keluar Ditolak')->with('remark',$remark)->with('driver_ids',$leave_request->driver_request_id)->with('remark',$remark)->with('request_id',$request_id)->with('reject','Belum');
        }else{
            return view('human_resource.leave_request.reject')->with('head','Surat Izin Keluar Ditolak')->with('message','Request ID : '.$request_id.' pernah ditolak.')->with('page','Surat Izin Keluar Ditolak')->with('remark',$remark)->with('driver_ids',$leave_request->driver_request_id)->with('remark',$remark)->with('request_id',$request_id)->with('reject','Pernah');
        }
    } catch (\Exception $e) {
        return view('human_resource.leave_request.approval')->with('head','Leave Request Approval')->with('message','Gagal menyetujui Surat Izin Keluar dengan Error = '.$e->getMessage())->with('page','Surat Izin Keluar Ditolak')->with('remark',$remark)->with('driver_ids',$leave_request->driver_request_id)->with('remark',$remark)->with('request_id',$request_id);
    }
}

public function reasonRejectLeaveRequest(Request $request)
{
    try {
        $request_id = $request->get('request_id');
        $approval = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Manager')->first();
        if ($approval == null) {
            $approval = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Deputy General Manager')->first();
            if ($approval == null) {
                $approval = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','General Manager')->first();
                if ($approval == null) {
                    $approval = HrLeaveRequestApproval::where('request_id',$request_id)->where('remark','Director')->first();
                }
            }
        }
        $leave_request = HrLeaveRequest::where('request_id',$request_id)->first();
        $leave_request->remark = 'Rejected';
        $leave_request->reason = $request->get('reason');
        $by = $leave_request->created_by;
        $leave_request->save();

        $approval->status = 'Rejected';
        $approval->approved_at = date('Y-m-d H:i:s');
        $approval->save();

        $users = User::where('id',$by)->first();

        $mail_to = [];

        if (strpos($users->email, '@music.yamaha.com') !== false) {
            array_push($mail_to, $users->email);
        }else{
            $chief = Approver::where('department',$leave_request->department)->where('remark','Chief')->first();
            $foreman = Approver::where('department',$leave_request->department)->where('remark','Foreman')->first();
            if ($chief != null) {
                if ($chief->approver_email) {
                    array_push($mail_to, $chief->approver_email);
                }
            }
            if ($foreman != null) {
                if ($foreman->approver_email) {
                    array_push($mail_to, $foreman->approver_email);
                }
            }
        }

        $leave_request = HrLeaveRequest::where('request_id', '=', $request_id)->first();
        $detail_emp = HrLeaveRequestDetail::where('request_id',$request_id)->leftjoin('departments','department_name','department')->get();
        $approval_progress = HrLeaveRequestApproval::where('request_id',$request_id)->get();

        $destination = null;

        if ($leave_request->add_driver == 'YES') {
            $destination = DriverDetail::where('driver_id',$leave_request->driver_request_id)->get();
        }

        $data = [
            'leave_request' => $leave_request,
            'detail_emp' => $detail_emp,
            'approval_progress' => $approval_progress,
            'remarks' => 'Security',
            'destination' => $destination,
            'reason' => $request->get('reason'),
            'driver' => null,
        ];

        Mail::to($mail_to)
        ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
        ->send(new SendEmail($data, 'leave_request_reject'));

        $response = array(
            'status' => true,
            'message' => 'Reason berhasil diinput.'
        );
        return Response::json($response);
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function indexLeaveRequestSecurity()
{

    $security = EmployeeSync::where('group','Security Group')->where('end_date',null)->get();
    return view('human_resource.leave_request.index_security', array(
        'title' => 'Surat Izin Keluar di Security', 
        'title_jp' => '外出申請書',
        'security' => $security
    ))->with('page', 'Human Resource');
}

public function fetchLeaveRequestSecurity(Request $request)
{
    try {
        $leave_request = HrLeaveRequest::join('departments','departments.department_name','hr_leave_requests.department')->where('hr_leave_requests.position','security')->orwhere('hr_leave_requests.position','out')->orderBy('hr_leave_requests.time_departure','desc')->get();

        $leave_details = [];
        for ($i=0; $i < count($leave_request); $i++) { 
            $leave_detail = HrLeaveRequestDetail::where('request_id',$leave_request[$i]->request_id)->get();
            array_push($leave_details, $leave_detail);
        }

        $response = array(
            'status' => true,
            'leave_request' => $leave_request,
            'leave_details' => $leave_details
        );
        return Response::json($response);
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function fetchLeaveRequestSecurityDetail(Request $request)
{
    try {
        $leave_details = HrLeaveRequestDetail::where('request_id',$request->get('request_id'))->join('departments','departments.department_name','hr_leave_request_details.department')->get();

        $response = array(
            'status' => true,
            'leave_details' => $leave_details
        );
        return Response::json($response);
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function scanLeaveRequestSecurity(Request $request)
{
    try {
        if (str_contains($request->get('tag'), 'PI') || str_contains($request->get('tag'), 'OS')) {
            $emp = Employee::
            join('hr_leave_request_details','hr_leave_request_details.employee_id','employees.employee_id')
            ->where('employees.employee_id',$request->get('tag'))
            ->where('request_id',$request->get('request_id'))
            ->where('employees.employee_id',$request->get('employee_id'))
            ->first();
        }else{
            $emp = Employee::
            join('hr_leave_request_details','hr_leave_request_details.employee_id','employees.employee_id')
            ->where('tag',$request->get('tag'))
            ->where('request_id',$request->get('request_id'))
            ->where('employees.employee_id',$request->get('employee_id'))
            ->first();
        }

        if ($emp != null) {
            $req = HrLeaveRequestDetail::where('request_id',$request->get('request_id'))->where('employee_id',$emp->employee_id)->first();
            if ($request->get('position') == 'Keluar') {
                $req->confirmed_at = date('Y-m-d H:i:s');
            }else{
                $req->returned_at = date('Y-m-d H:i:s');
            }
            $req->save();

            $response = array(
                'status' => true,
                'message' => 'Success Scan Tag',
                'request_id' => $request->get('request_id')
            );
            return Response::json($response);
        }else{
            $response = array(
                'status' => false,
                'message' => 'Tag Invalid'
            );
            return Response::json($response);
        }
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function confirmLeaveRequestSecurity(Request $request)
{
    try {
        $req = HrLeaveRequest::where('request_id',$request->get('request_id'))->first();
        if ($request->get('position') == 'Keluar') {
            $req->position = 'out';
        }else{
            $req->position = 'closed';
        }
        $req->remark = 'Fully Approved';

        $approval = HrLeaveRequestApproval::where('request_id',$request->get('request_id'))->where('remark','Security')->first();
        $approval->approver_id = $request->get('employee_id');
        $approval->approver_name = $request->get('name');
        $approval->status = 'Approved';
        $approval->approved_at = date('Y-m-d H:i:s');


        $req->save();
        $approval->save();

        $response = array(
            'status' => true,
            'message' => 'Confirmation Success',
        );
        return Response::json($response);
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function confirmLeaveRequestDriver(Request $request)
{
    try {
        $driver = Driver::find($request->get('id'));
        $driver_list = DriverList::where('driver_id', '=', $request->get('driver_id'))->first();

        $driver->driver_id = $request->get('driver_id');
        $driver->name = $driver_list->name;
        $driver->plat_no = explode('_', $request->get('car'))[0];
        $driver->car = explode('_', $request->get('car'))[1];
        $driver->remark = 'received';
        $driver->save();

        $emp = Employee::where('employee_id',$request->get('driver_id'))->first();

        if(substr($driver_list->whatsapp_no, 0, 1) == '+' ){
            $phone = substr($driver_list->whatsapp_no, 1, 15);
        }
        else if(substr($driver_list->whatsapp_no, 0, 1) == '0'){
            $phone = "62".substr($driver_list->whatsapp_no, 1, 15);
        }
        else{
            $phone = $driver_list->whatsapp_no;
        }

        $hrRequest = HrLeaveRequest::where('hr_leave_requests.driver_request_id',$request->get('id'))->leftjoin('users','users.id','hr_leave_requests.created_by')->first();

        $destination = DriverDetail::where('driver_id',$hrRequest->driver_request_id)->where('category','destination')->get();
        $dests = [];
        for($i = 0; $i < count($destination);$i++){
            array_push($dests, $destination[$i]->remark);
        }

        $day = str_replace(' ', '%20', date('l, d F Y',strtotime($driver->date_from)));

        $start_time = date('H:i a',strtotime($driver->date_from));
        $start_time_replace = str_replace(" ","%20",$start_time);

        $end_time = date('H:i a',strtotime($driver->date_to));
        $end_time_replace = str_replace(" ","%20",$end_time);

        if ($hrRequest->return_or_not == 'NO') {
            $end_time_replace = 'Hanya Mengantar';
        }

        $destinations = str_replace(" ", "%20", $driver->destination_city.'%20('.join(",%20",$dests).')');
        $name = str_replace(" ", "%20", $driver_list->name);

        $request_name = str_replace(" ", "%20", $hrRequest->name);

        $message = 'Driver%20Order%0A%0ARequest%20by%20:%20'.$request_name.'%0ADay%20and%20Date%20:%20'.$day.'%0ADestination%20City%20:%20'.$destinations.'%0ADeparture%20:%20'.$start_time_replace.'%0AArrive%20:%20'.$end_time_replace.'%0APilot%20:%20'.$name.'%0A%0A-YMPI%20MIS%20Dept.-';


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
         CURLOPT_POSTFIELDS => 'receiver=6282334197238&device=6281130561777&message='.$message.'&type=chat',
         CURLOPT_HTTPHEADER => array(
             'Accept: application/json',
             'Content-Type: application/x-www-form-urlencoded',
             'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
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
         CURLOPT_POSTFIELDS => 'receiver=628113669869&device=6281130561777&message='.$message.'&type=chat',
         CURLOPT_HTTPHEADER => array(
             'Accept: application/json',
             'Content-Type: application/x-www-form-urlencoded',
             'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
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
         CURLOPT_POSTFIELDS => 'receiver=628113669871&device=6281130561777&message='.$message.'&type=chat',
         CURLOPT_HTTPHEADER => array(
             'Accept: application/json',
             'Content-Type: application/x-www-form-urlencoded',
             'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
         ),
     ));

        curl_exec($curl);

        $mail_to = [];

        if (strpos($hrRequest->email, '@music.yamaha.com') !== false) {
            array_push($mail_to, $hrRequest->email);
        }else{
            $chief = Approver::where('department',$hrRequest->department)->where('remark','Chief')->first();
            $foreman = Approver::where('department',$hrRequest->department)->where('remark','Foreman')->first();
            if ($chief != null) {
                if ($chief->approver_email) {
                    array_push($mail_to, $chief->approver_email);
                }
            }
            if ($foreman != null) {
                if ($foreman->approver_email) {
                    array_push($mail_to, $foreman->approver_email);
                }
            }
        }
        $leave_request = HrLeaveRequest::where('request_id', '=', $hrRequest->request_id)->first();

        $empwaga = EmployeeSync::join('users','users.username','employee_syncs.employee_id')->where('users.id',$leave_request->created_by)->first();

        if(substr($empwaga->phone, 0, 1) == '+' ){
            $phone = substr($empwaga->phone, 1, 15);
        }
        else if(substr($empwaga->phone, 0, 1) == '0'){
            $phone = "62".substr($empwaga->phone, 1, 15);
        }
        else{
            $phone = $empwaga->phone;
        }

        $name = str_replace(" ", "%20", $driver_list->name);
        $phone_driver = $driver_list->phone_no;

        $message2 = 'Surat%20Izin%20Keluar%0A%0ARequest%20ID%20:%20'.$leave_request->request_id.'%0A.Telah%20disetujui%20oleh%20GA.%0ATunjukkan%20Request%20ID%20pada%20Security%20saat%20akan%20meninggalkan%20perusahaan.%0ADriver%20Anda%20:%20'.$name.'.%0ANo.%20HP%20Driver%20:%20'.$phone_driver.'%0A%0A-YMPI%20HR%20dan%20GA%20Dept.-';
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
         CURLOPT_POSTFIELDS => 'receiver='.$phone.'&device=6281130561777&message='.$message2.'&type=chat',
         CURLOPT_HTTPHEADER => array(
             'Accept: application/json',
             'Content-Type: application/x-www-form-urlencoded',
             'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
         ),
     ));

        curl_exec($curl);


        $detail_emp = HrLeaveRequestDetail::where('request_id',$hrRequest->request_id)->leftjoin('departments','department_name','department')->get();
        $approval_progress = HrLeaveRequestApproval::where('request_id',$hrRequest->request_id)->get();

        $data = [
            'leave_request' => $leave_request,
            'detail_emp' => $detail_emp,
            'approval_progress' => $approval_progress,
            'remarks' => 'Security',
            'driver' => $driver_list->name,
            'phone_no' => $driver_list->phone_no,
            'destination' => $destination,
        ];
        Mail::to($mail_to)
                        // ->cc($cc)
        ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
        ->send(new SendEmail($data, 'leave_request'));
        $response = array(
            'status' => true,
            'message' => 'Confirmation Success',
        );
        return Response::json($response);
    } catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function Confirmation($request_id, $approver_id){

                    // $app_email = RecruitmentApproval::where('request_id', '=', $request_id)
                    // ->where('id', '=', $approver_id)
                    // ->wherenull('status')
                    // ->select('approver_email', 'approver_id', 'id', 'approver_name', 'status', 'approved_at')
                    // ->first();

                         // $rec_approval = recruitment::where('request_id', '=', $request_id)->first();

                    // if ($app_email == null) {
                    //     return view('human_resource.recruitment.done_confirm')->with('head','Smart Recruitment')->with('message','Request ini sudah pernah disetujui.');
                    // }else{
                    //     if ($app_email->remark == 'Manager HR') {
                    //         $rec_approval->remark = 'Recruitment HR';
                    //         $rec_approval->save();
                    //         $app_emails = RecruitmentApproval::where('id',$approver_id)->first();
                    //         $app_emails->status = 'Approved';
                    //         $app_emails->approved_at = date('Y-m-d h:i:s');
                    //         $app_emails->save();
                    //     }
                    // }
    $id = strtoupper(Auth::user()->username);
    $a = RecruitmentApproval::where('request_id', '=', $request_id)->wherenull('status')->take(1)->first();
    $b = RecruitmentApproval::where('request_id', '=', $request_id)->where('approver_id', '=', $id)->whereNotNull('status')->first();

    if ($a != null) {
        if ($a->approver_id == $id) {
            $p = 'Request Berhasil Disetujui';
            if ($a->remark == 'DGM') {
                $approve_at = RecruitmentApproval::where('request_id', '=', $request_id)->wherenull('status')->where('id',$approver_id)->update([
                    'status' => 'Approved',
                    'approved_at' => date('Y-m-d h:i:s')
                ]);

                // $pres_dir = RecruitmentApproval::where('request_id', '=', $request_id)->where('approver_id','=', 'PI1301001')->update([
                //     'status' => 'Mengetahui',
                //     'approved_at' => date('Y-m-d h:i:s')
                $ab = Recruitment::where('request_id', $request_id)->select('position')->first();

                if ($ab->position == 'Staff') {
                    $pres_dir = RecruitmentApproval::where('request_id', '=', $request_id)->where('approver_id','=', 'PI1301001')->update([
                        'status' => 'Mengetahui',
                        'approved_at' => date('Y-m-d h:i:s')
                    ]);
                }else{
                    $pres_dir = RecruitmentApproval::where('request_id', '=', $request_id)->where('approver_id','=', 'PI1301001')->update([
                        'status' => '-',
                        'approved_at' => date('Y-m-d h:i:s')
                    ]);
                }

                $data = db::select("select r.id, request_id, r.position, r.department, r.employment_status, quantity_male, quantity_female, reason, DATE_FORMAT(start_date, '%d-%m-%Y') as start_date, min_age, max_age, marriage_status, domicile, work_experience, skill, educational_level, r.major, requirement, note, progress, remark, status_req, e.name, es.employee_id, comment_note, reply from recruitments as r left join users as e on r.created_by = e.id left join employee_syncs as es on e.username = es.employee_id where request_id = '".$request_id."'");

                $mail_next = RecruitmentApproval::where('request_id', $request_id)->wherenull('status')->take(1)->first();

                $app_email = RecruitmentApproval::where('request_id', '=', $request_id)
                ->where('id', '=', $mail_next->id)
                ->wherenull('status')
                ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
                ->first();


                $app_progress = RecruitmentApproval::where('request_id', '=', $request_id)
                ->whereNotNull('approver_id')
                ->get();

                $data = [
                    'data' => $data,
                    'app_email' => $app_email,
                    'app_progress' => $app_progress
                ];

                if ($ab->position == 'Staff') {
                    Mail::to(['hiroshi.ura@music.yamaha.com'])
                    ->send(new SendEmail($data, 'request_manpower_approval_pak_ura'));

                    Mail::to($mail_next->approver_email)
                    ->bcc(['ympi-mis-ML@music.yamaha.com'])
                    ->send(new SendEmail($data, 'request_manpower_approval'));
                }else{
                   Mail::to($mail_next->approver_email)
                   ->bcc(['ympi-mis-ML@music.yamaha.com'])
                   ->send(new SendEmail($data, 'request_manpower_approval'));
               }

           }else if($a->remark == 'Direktur HR'){
            $approve_at = RecruitmentApproval::where('request_id', '=', $request_id)->wherenull('status')->where('id',$approver_id)->update([
                'status' => 'Approved',
                'approved_at' => date('Y-m-d h:i:s')
            ]);

            $mgr_hr = RecruitmentApproval::where('request_id', '=', $request_id)->wherenull('status')->where('approver_id','=', 'PI9906002')->update([
                'status' => 'Mengetahui',
                'approved_at' => date('Y-m-d h:i:s')
            ]);


            $ab = Recruitment::where('request_id', $request_id)->select('status_req')->first();
            if ($ab->status_req == 'Request Karyawan Rekontrak') {
                $form_req = Recruitment::where('request_id', $request_id)->update([
                    'status_at' => 'Test Potensi Akademik'
                ]);
            }else{
                $form_req = Recruitment::where('request_id', $request_id)->update([
                    'status_at' => 'Full Approve'
                ]);
            }

            $req = db::select("select r.id, request_id, r.position, r.department, r.employment_status, quantity_male, quantity_female, reason, DATE_FORMAT(start_date, '%d-%m-%Y') as start_date, min_age, max_age, marriage_status, domicile, work_experience, skill, educational_level, r.major, requirement, note, progress, remark, status_req, e.name, es.employee_id, comment_note, reply, r.created_by from recruitments as r left join users as e on r.created_by = e.id left join employee_syncs as es on e.username = es.employee_id where request_id = '".$request_id."'");

                // $mail_next = RecruitmentApproval::where('request_id', $request_id)->wherenull('status')->take(1)->first();

                // $app_email = RecruitmentApproval::where('request_id', '=', $request_id)
                // ->where('id', '=', $mail_next->id)
                // ->wherenull('status')
                // ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
                // ->first();

            $app_progress = RecruitmentApproval::where('request_id', '=', $request_id)
            ->whereNotNull('approver_id')
            ->get();

            $data = [
                'data' => $req,
                'app_progress' => $app_progress
            ];


            $before = db::select('select distinct email from users where users.id = "'.$req[0]->created_by.'"');

            Mail::to(['khoirul.umam@music.yamaha.com', 'linda.febrian@music.yamaha.com', $before[0]->email])
            ->bcc(['ympi-mis-ML@music.yamaha.com'])
            ->send(new SendEmail($data, 'request_manpower_approval_pak_ura'));

        }else{
            $approve_at = RecruitmentApproval::where('request_id', '=', $request_id)->wherenull('status')->where('id',$approver_id)->update([
                'status' => 'Approved',
                'approved_at' => date('Y-m-d h:i:s')
            ]);

            $data = db::select("select r.id, request_id, r.position, r.department, r.employment_status, quantity_male, quantity_female, reason, DATE_FORMAT(start_date, '%d-%m-%Y') as start_date, min_age, max_age, marriage_status, domicile, work_experience, skill, educational_level, r.major, requirement, note, progress, remark, status_req, e.name, es.employee_id, comment_note, reply from recruitments as r left join users as e on r.created_by = e.id left join employee_syncs as es on e.username = es.employee_id where request_id = '".$request_id."'");

            $mail_next = RecruitmentApproval::where('request_id', $request_id)->wherenull('status')->take(1)->first();

            $app_email = RecruitmentApproval::where('request_id', '=', $request_id)
            ->where('id', '=', $mail_next->id)
            ->wherenull('status')
            ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
            ->first();


            $app_progress = RecruitmentApproval::where('request_id', '=', $request_id)
            ->whereNotNull('approver_id')
            ->get();

            $data = [
                'data' => $data,
                'app_email' => $app_email,
                'app_progress' => $app_progress
            ];

            Mail::to($mail_next->approver_email)
            ->bcc(['ympi-mis-ML@music.yamaha.com'])
            ->send(new SendEmail($data, 'request_manpower_approval'));
        }
    }else{
     $p = 'Maaf Anda Tidak Memiliki Akses Approval Ini';
 }

 $req = Recruitment::where('request_id', $request_id)
 ->select('request_id')
 ->first();

 return view('human_resource.recruitment.confirm_approval', array(
    'title'    => 'Smart Recruitment', 
    'title_jp' => '??',
    'data' => $req,
    'p' => $p
))->with('page', 'Smart Recruitment');



    // $app_email = RecruitmentApproval::where('request_id', '=', $request_id)
    // ->where('id', '=', $approver_id)
    // ->wherenull('status')
    // ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
    // ->first();

    // $app_progress = RecruitmentApproval::where('request_id', '=', $request_id)
    // ->whereNotNull('approver_id')
    // ->get();


    // if (count($app_email) > 0) {
    //     $approve_at = RecruitmentApproval::where('request_id', '=', $request_id)->wherenull('status')->where('id',$approver_id)->update([
    //         'status' => 'Approved',
    //         'approved_at' => date('Y-m-d h:i:s')
    //     ]);

    //     $data = db::select("select r.id, request_id, r.position, r.department, r.employment_status, quantity_male, quantity_female, reason, DATE_FORMAT(start_date, '%d-%m-%Y') as start_date, min_age, max_age, marriage_status, domicile, work_experience, skill, educational_level, r.major, requirement, note, progress, remark, status_req, e.name, es.employee_id, comment_note, reply from recruitments as r left join users as e on r.created_by = e.id left join employee_syncs as es on e.username = es.employee_id where request_id = '".$request_id."'");

    //     $app_email = RecruitmentApproval::where('request_id', '=', $request_id)
    //     ->wherenull('status')
    //     ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
    //     ->first();

    //     if ($app_email->approver_email == 'hiroshi.ura@music.yamaha.com') {
    //         $approve_at = RecruitmentApproval::where('request_id', '=', $request_id)->wherenull('status')->where('id',$approver_id)->update([
    //         'status' => 'Cek',
    //         'approved_at' => date('Y-m-d h:i:s')
    //     ]);
    //     }

        // if ($app_email->approver_email) {
        //     // $app_email->status = 'Mengetahui';
        //     // $app_email->save();

        //     $rec_approval = recruitment::where('request_id', '=', $request_id)->first();
        //     $rec_approval->remark = 'Recruitment HR';
        //     $rec_approval->save();
        // }

        // $app_progress = RecruitmentApproval::where('request_id', '=', $request_id)
        // ->whereNotNull('approver_id')
        // ->get();

        // $data = [
        //     'data' => $data,
        //     'app_email' => $app_email,
        //     'app_progress' => $app_progress,
        //     'remark' => 'Recruitment HR'
        // ];

        // if ($app_email == null) {
        //     $rec_approval->remark = 'Recruitment HR';
        //     $rec_approval->save();

        //     $before = db::select('select distinct email from users where users.id = "'.$rec_approval->created_by.'"');

        //     Mail::to(['linda.febrian@music.yamaha.com', $before[0]->email])
        //     ->bcc(['ympi-mis-ML@music.yamaha.com','anton.budi.santoso@music.yamaha.com'])
        //     ->send(new SendEmail($data, 'request_manpower_approval'));

        // }
        // else if($app_email->approver_email == 'khoirul.umam@music.yamaha.com'){
        //     Mail::to(['khoirul.umam@music.yamaha.com'])
        //     ->send(new SendEmail($data, 'request_manpower_approval_pak_ura'));
        // }
        // else{
        //     if ($app_email->approver_email == 'arief.soekamto@music.yamaha.com') {
        //         Mail::to(['hiroshi.ura@music.yamaha.com'])
        //         ->send(new SendEmail($data, 'request_manpower_approval_pak_ura'));

        //         Mail::to($app_email->approver_email)
        //         ->bcc(['ympi-mis-ML@music.yamaha.com'])
        //         ->send(new SendEmail($data, 'request_manpower_approval'));
        //     }else{
        //         Mail::to($app_email->approver_email)
        //         ->bcc(['ympi-mis-ML@music.yamaha.com'])
        //         ->send(new SendEmail($data, 'request_manpower_approval'));
        //     }
        // }
        // return view('human_resource.recruitment.done_confirm')->with('head','Smart Recruitment')->with('message','Request ini berhasil disetujui.');
}
else{
    $p = 'Request Ini Telah Disetujui Semua';

    $req = Recruitment::where('request_id', $request_id)
    ->select('request_id')
    ->first();

    return view('human_resource.recruitment.confirm_approval', array(
        'title'    => 'Smart Recruitment', 
        'title_jp' => '??',
        'data' => $req,
        'p' => $p
    ))->with('page', 'Smart Recruitment');
        // if ($rec_approval->remark == 'Menunggu Persetujuan') {
        //     return view('human_resource.recruitment.done_confirm')->with('head','Smart Recruitment')->with('message','Request ini sudah pernah disetujui.');
        // }else{
        //     $progres = Recruitment::where('request_id', '=', $request_id)->update([
        //         'remark' => 'Recruitment HR'
        //     ]);

        //     $data = db::select("select r.id, request_id, r.position, r.department, r.employment_status, quantity_male, quantity_female, reason, DATE_FORMAT(start_date, '%d-%m-%Y') as start_date, min_age, max_age, marriage_status, domicile, work_experience, skill, educational_level, r.major, requirement, note, progress, remark, status_req, e.name, es.employee_id, comment_note, reply from recruitments as r left join users as e on r.created_by = e.id left join employee_syncs as es on e.username = es.employee_id where request_id = '".$request_id."'"); 

        //     $email_pengirim = db::select('select u.email from recruitments as r left join users as u on u.id = r.created_by where request_id = "'.$request_id.'"');

        //     $app_email = RecruitmentApproval::where('request_id', '=', $request_id)
        //     ->wherenull('status')
        //     ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
        //     ->first();

        //     $app_progress = RecruitmentApproval::where('request_id', '=', $request_id)
        //     ->whereNotNull('approver_id')
        //     ->get();

        //     $data = [
        //         'data' => $data,
        //         'app_email' => $app_email,
        //         'remark' => 'Recruitment HR',
        //         'app_progress' => '$app_progress'
        //     ];
        //     $mail_to = [];
        //     array_push($mail_to, $email_pengirim[0]->email);
        //     array_push($mail_to, 'linda.febrian@music.yamaha.com');

            // Mail::to(['linda.febrian@music.yamaha.com',$email_pengirim[0]->email])
            // ->bcc(['ympi-mis-ML@music.yamaha.com'])
            // ->send(new SendEmail($data, 'request_manpower_approval'));

            // return view('human_resource.recruitment.done_confirm')->with('head','Smart Recruitment')->with('message','Request ini berhasil disetujui.');
        // }
}

}

public function ConfirmationRequestMagang($request_id, $approver_id){
    $id = strtoupper(Auth::user()->username);
    $a = RequestMagangApproval::where('request_id', '=', $request_id)->wherenull('status')->take(1)->first();
    $b = RequestMagangApproval::where('request_id', '=', $request_id)->where('approver_id', '=', $id)->whereNotNull('status')->first();

    if ($a != null) {
        if ($a->approver_id == $id) {
            $approved = RequestMagangApproval::where('request_id', '=', $request_id)->wherenull('status')->where('id',$approver_id)->update([
                'status' => 'Approved',
                'approved_at' => date('Y-m-d h:i:s')
            ]);

            $mgr_hr = RequestMagangApproval::where('request_id', '=', $request_id)->where('approver_id', '=', 'PI9906002')->update([
                'status' => 'Mengetahui'
            ]);

            $staff_hr = RequestMagangApproval::where('request_id', '=', $request_id)->where('approver_id', '=', 'PI1906001')->update([
                'status' => 'Mengetahui'
            ]);

            $update_req = RequestMagang::where('request_id', $request_id)->update([
                'status' => 'All Approve'
            ]);

            $data = db::select("select rm.id, request_id, rm.position, rm.department, rm.section, rm.`group`, rm.sub_group, qty_male, qty_female, rm.hire_date, rm.end_date, rm.remark, u.`name` as created_by, es.employee_id from request_magangs as rm left join users as u on rm.created_by = u.id left join employee_syncs as es on u.username = es.employee_id where request_id = '".$request_id."'");

            $approver = RequestMagangApproval::
            where('request_id', '=', $request_id)
            ->wherenull('status')
            ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
            ->first();

            $approver_progress = RequestMagangApproval::where('request_id', '=', $request_id)
            ->whereNotNull('approver_id')
            ->get();

            $data = [
                'data' => $data,
                'approver' => $approver,
                'approver_progress' => $approver_progress
            ];

            $foreman = RequestMagangApproval::where('request_id', $request_id)->where('remark', '=', 'Foreman')
            ->select('approver_email')
            ->first();

            Mail::to(['khoirul.umam@music.yamaha.com', 'linda.febrian@music.yamaha.com', $foreman->approver_email])
            ->bcc(['ympi-mis-ML@music.yamaha.com'])
            ->send(new SendEmail($data, 'request_magang_done'));

            $p = 'Request Berhasil Disetujui';
        }else{
         $p = 'Maaf Anda Tidak Memiliki Akses Approval Ini';
     }

     $req = RequestMagang::where('request_id', $request_id)
     ->select('request_id')
     ->first();

     return view('human_resource.magang.confirm_approval', array(
        'title'    => 'Smart Recruitment', 
        'title_jp' => '??',
        'data' => $req,
        'p' => $p
    ))->with('page', 'Smart Recruitment');
 }
 else{
    $p = 'Request Ini Telah Disetujui Semua';

    $req = RequestMagang::where('request_id', $request_id)
    ->select('request_id')
    ->first();

    return view('human_resource.magang.confirm_approval', array(
        'title'    => 'Smart Recruitment', 
        'title_jp' => '??',
        'data' => $req,
        'p' => $p
    ))->with('page', 'Smart Recruitment');
}
}

public function ConfirmationRequestTunjangan($request_id, $approver_id){
    $id = strtoupper(Auth::user()->username);
    // $a = HrApproval::where('request_id', '=', $request_id)->where('status', 'Waiting')->take(1)->first();
    $a = HrApproval::where('id', $approver_id)->first();
    $b = HrApproval::where('request_id', '=', $request_id)->where('approver_id', '=', $id)->whereNotNull('status')->first();

    if (count($a) > 0) {
        if ($a->approver_id == $id) {
            if ($a->project_name == 'Uang Simpati') {
                if ($a->status == 'Waiting') {
                    $data = db::select("select us.id, us.request_id, us.employee, e.`name`, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, u.`name` as pembuat, us.remark from uang_simpatis as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by where request_id = '".$request_id."'");

                    if ($a->remark == 'Mengetahui, Chief' || $a->remark == 'Mengetahui, Foreman' || $a->remark == 'Mengetahui, Manager') {
                        $approved = HrApproval::where('request_id', '=', $request_id)
                        ->whereIn('remark',  ['Mengetahui, Chief', 'Mengetahui, Manager', 'Mengetahui, Foreman'])
                        ->update([
                            'status' => 'Mengetahui',
                            'approved_at' => date('Y-m-d h:i:s')
                        ]);

                        $approver = HrApproval::
                        where('request_id', '=', $request_id)
                        ->where('status', 'Waiting')
                        ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
                        ->orderBy('id', 'ASC')
                        ->first();

                        $approver_progress = HrApproval::where('request_id', '=', $request_id)
                        ->whereNotNull('approver_id')
                        ->get();

                        $role = User::where('username', Auth::user()->username)
                        ->select('username','role_code')
                        ->first();

                        $data = [
                            'data' => $data,
                            'approver' => $approver,
                            'approver_progress' => $approver_progress,
                            'role' => $role
                        ];

                        $mail_mgr =  HrApproval::where('request_id', '=', $request_id)->where('remark', 'Mengetahui, Manager')->first();
                        if ($data['data'][0]->permohonan == 'Uang Simpati Kelahiran Anak Ke 1/350000' || $data['data'][0]->permohonan == 'Uang Simpati Musibah Kebanjiran/1250000' || $data['data'][0]->permohonan == 'Uang Simpati Kelahiran Anak Ke 2/350000' || $data['data'][0]->permohonan == 'Uang Simpati Pernikahan/850000' || $data['data'][0]->permohonan == 'Uang Simpati Kelahiran Anak Ke 3/350000' || $data['data'][0]->permohonan == 'Uang Simpati Musibah Kebakaran Rumah/1250000' || $data['data'][0]->permohonan == 'Uang Simpati Kelahiran Anak Kembar/700000') {
                            Mail::to($mail_mgr->approver_email)->send(new SendEmail($data, 'done_uang_simpati'));
                            Mail::to(['khoirul.umam@music.yamaha.com'])->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($data, 'request_uang_simpati'));
                        }else{    
                            Mail::to($mail_mgr->approver_email)->send(new SendEmail($data, 'done_uang_simpati_kematian'));
                            Mail::to(['khoirul.umam@music.yamaha.com'])->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($data, 'request_uang_simpati_kematian'));
                        }
                    }else{
                        $approved = HrApproval::where('request_id', '=', $request_id)->where('remark', 'Menyetujui, HR')->where('id',$approver_id)->update([
                            'status' => 'Menyetujui',
                            'approved_at' => date('Y-m-d h:i:s')
                        ]);

                        $update = UangSimpati::where('request_id', $request_id)->update([
                            'remark' => 'Close'
                        ]);

                        $approver = HrApproval::
                        where('request_id', '=', $request_id)
                        ->where('status', 'Waiting')
                        ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
                        ->orderBy('id', 'ASC')
                        ->first();

                        $approver_progress = HrApproval::where('request_id', '=', $request_id)
                        ->whereNotNull('approver_id')
                        ->get();

                        $role = User::where('username', Auth::user()->username)
                        ->select('username','role_code')
                        ->first();

                        $data = [
                            'data' => $data,
                            'approver' => $approver,
                            'approver_progress' => $approver_progress,
                            'role' => $role
                        ];

                        if ($data['data'][0]->permohonan == 'Uang Simpati Kelahiran Anak Ke 1/350000' || $data['data'][0]->permohonan == 'Uang Simpati Musibah Kebanjiran/1250000' || $data['data'][0]->permohonan == 'Uang Simpati Kelahiran Anak Ke 2/350000' || $data['data'][0]->permohonan == 'Uang Simpati Pernikahan/850000' || $data['data'][0]->permohonan == 'Uang Simpati Kelahiran Anak Ke 3/350000' || $data['data'][0]->permohonan == 'Uang Simpati Musibah Kebakaran Rumah/1250000' || $data['data'][0]->permohonan == 'Uang Simpati Kelahiran Anak Kembar/700000') {
                            Mail::to(['ummi.ernawati@music.yamaha.com'])->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($data, 'done_uang_simpati'));

                            $datas = db::select('select employee_id, name, phone from employee_syncs where employee_id = "PI2101044"');
                            $phone = substr($datas[0]->phone, 1, 15);
                            $phone = '62' . $phone;

                            $message = 'Permohonan%20Uang%20Simpati%20Anda%20Telah%20Disetujui.%0A%0AAtas%20Nama%20:%20*'.$datas[0]->name.'*%0A%0A%0A%0A-YMPI-';

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
                        }else{  
                            Mail::to(['ummi.ernawati@music.yamaha.com'])->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($data, 'done_uang_simpati_kematian'));

                            $datas = db::select('select employee_id, name, phone from employee_syncs where employee_id = "PI2101044"');
                            $phone = substr($datas[0]->phone, 1, 15);
                            $phone = '62' . $phone;

                            $message = 'Permohonan%20Uang%20Simpati%20Anda%20Telah%20Disetujui.%0A%0AAtas%20Nama%20:%20*'.$datas[0]->name.'*%0A%0A%0A%0A-YMPI-';

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
                        }
                    }
                }else{
                    $p = 'Terimakasih Anda Telah Melakukan Persetujuan, Sekarang Saatnya Approval Berikutnya.';

                    $req = HrApproval::where('request_id', $request_id)
                    ->select('request_id', 'project_name')
                    ->first();

                    return view('human_resource.magang.confirm_approval', array(
                        'title'    => 'Pengajuan '.$req->project_name, 
                        'title_jp' => '??',
                        'data' => $req,
                        'p' => $p
                    ))->with('page', 'Pengajuan '.$req->project_name);
                }
            }else if ($a->project_name == 'Tunjangan Keluarga'){
                if ($a->status == 'Waiting') {
                    $data = db::select("select us.id, us.request_id, us.employee, e.`name`, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, u.`name` as pembuat, us.remark, us.nama_pasangan, us.tempat_lahir, us.tanggal_lahir, us.status from uang_keluargas as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by where request_id = '".$request_id."'");

                    if ($a->remark == 'Mengetahui, Chief' || $a->remark == 'Mengetahui, Foreman') {
                        $approved = HrApproval::where('request_id', '=', $request_id)
                        ->whereIn('remark',  ['Mengetahui, Chief', 'Mengetahui, Manager', 'Mengetahui, Foreman'])
                        ->update([
                            'status' => 'Mengetahui',
                            'approved_at' => date('Y-m-d h:i:s')
                        ]);

                        $approver = HrApproval::
                        where('request_id', '=', $request_id)
                        ->where('status', 'Waiting')
                        ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
                        ->orderBy('id', 'ASC')
                        ->first();

                        $approver_progress = HrApproval::where('request_id', '=', $request_id)
                        ->whereNotNull('approver_id')
                        ->get();

                        $role = User::where('username', Auth::user()->username)
                        ->select('username','role_code')
                        ->first();

                        $data = [
                            'data' => $data,
                            'approver' => $approver,
                            'approver_progress' => $approver_progress,
                            'role' => $role
                        ];

                        $mail_mgr =  HrApproval::where('request_id', '=', $request_id)->where('remark', 'Mengetahui, Manager')->first();
                        Mail::to($mail_mgr->approver_email)->send(new SendEmail($data, 'done_tunjangan_keluarga'));
                        Mail::to(['khoirul.umam@music.yamaha.com'])->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($data, 'request_tunjangan_keluarga'));
                    }else{
                        $approved = HrApproval::where('request_id', '=', $request_id)->where('remark', 'Menyetujui, HR')->where('id',$approver_id)->update([
                            'status' => 'Menyetujui',
                            'approved_at' => date('Y-m-d h:i:s')
                        ]);

                        $update = UangKeluarga::where('request_id', $request_id)->update([
                            'remark' => 'Close'
                        ]);

                        $approver = HrApproval::
                        where('request_id', '=', $request_id)
                        ->where('status', 'Waiting')
                        ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
                        ->orderBy('id', 'ASC')
                        ->first();

                        $approver_progress = HrApproval::where('request_id', '=', $request_id)
                        ->whereNotNull('approver_id')
                        ->get();

                        $role = User::where('username', Auth::user()->username)
                        ->select('username','role_code')
                        ->first();

                        $data = [
                            'data' => $data,
                            'approver' => $approver,
                            'approver_progress' => $approver_progress,
                            'role' => $role
                        ];

                        // $data_bpjs = db::connection('ympimis_2')->select('select id, employee_id from employee_bpjskes where employee_id = "'.$data['data'][0]->employee.'"');
                        // $gender = "";
                        // if ($data['data'][0]->status == 'ISTRI') {
                        //     $gender = "P";
                        // }else{
                        //     $gender = "L";
                        // }

                        // db::connection('ympimis_2')->table('employee_detail_bpjskes')->insert([
                        //     'id_reg' => $data_bpjs[0]->id,
                        //     'name' => $data['data'][0]->nama_pasangan,
                        //     'tempat_lahir' => $data['data'][0]->tempat_lahir,
                        //     'tanggal_lahir' => $data['data'][0]->tanggal_lahir,
                        //     'status' => '1',
                        //     'kelas_rawat' => 'Kelas 1',
                        //     'created_at' => date('Y-m-d h:i:s'),
                        //     'updated_at' => date('Y-m-d h:i:s'),
                        //     'remark' => $data['data'][0]->status,
                        //     'hubungan' => $data['data'][0]->status,
                        //     'jenis_kelamin' => $gender,
                        //     'status_kawin' => 'Kawin',
                        //     'upload_kk' => 'kk_lukman.jpg'
                        // ]);

                        Mail::to(['ummi.ernawati@music.yamaha.com'])->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($data, 'done_tunjangan_keluarga'));

                        $datas = db::select('select employee_id, name, phone from employee_syncs where employee_id = "PI2101044"');
                        $phone = substr($datas[0]->phone, 1, 15);
                        $phone = '62' . $phone;

                        $message = 'Permohonan%20Tunjangan%20Keluarga%20Anda%20Telah%20Disetujui.%0A%0AAtas%20Nama%20:%20*'.$datas[0]->name.'*%0A%0A%0A%0A-YMPI-';

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
                    }
                }else{
                    $p = 'Terimakasih Anda Telah Melakukan Persetujuan, Sekarang Saatnya Approval Berikutnya.';

                    $req = HrApproval::where('request_id', $request_id)
                    ->select('request_id', 'project_name')
                    ->first();

                    return view('human_resource.magang.confirm_approval', array(
                        'title'    => 'Pengajuan '.$req->project_name, 
                        'title_jp' => '??',
                        'data' => $req,
                        'p' => $p
                    ))->with('page', 'Pengajuan '.$req->project_name);
                }
            }

            // $approver = HrApproval::where('request_id', '=', $request_id)
            // ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at', 'project_name')
            // ->first();

            // $approver_progress = HrApproval::where('request_id', '=', $request_id)
            // ->whereNotNull('approver_id')
            // ->get();

            // $role = User::where('username', Auth::user()->username)
            // ->select('username','role_code')
            // ->first();

            // $mail_to = [];
            // $mail_cc = [];

            // $next_approver = HrApproval::where('request_id', '=', $request_id)->where('status', 'Waiting')->take(1)->first();

            // if (count($next_approver) > 0) {
            //     array_push($mail_to, $next_approver->approver_email);

            //     $view = 'request_uang_simpati';
            // }else{
            //     array_push($mail_to, 'ummi.ernawati@music.yamaha.com');

            //     $update = UangSimpati::where('request_id', $request_id)->update([
            //         'remark' => 'Close'
            //     ]); 

            //     $view = 'done_uang_simpati';
            // }

            // array_push($mail_cc, 'ympi-mis-ML@music.yamaha.com');


            // if ($approver->project_name == 'Uang Simpati') {
            //        $data = db::select("select us.id, us.request_id, us.employee, e.`name`, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, u.`name` as pembuat, us.remark from uang_simpatis as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by where request_id = '".$request_id."'");

            //        $data = [
            //         'data' => $data,
            //         'approver' => $approver,
            //         'approver_progress' => $approver_progress,
            //         'role' => $role
            //     ];

            //     Mail::to($mail_to)->bcc($mail_cc)->send(new SendEmail($data, $view));
            // }else if($approver->project_name == 'Tunjangan Keluarga'){
            //     $data = db::select("select us.id, us.request_id, us.employee, e.`name`, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, u.`name` as pembuat, us.remark from uang_keluargas as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by where request_id = '".$request_id."'");

            //     $update = UangKeluarga::where('request_id', $request_id)->update([
            //         'remark' => 'Close'
            //     ]);

            //     $data = [
            //         'data' => $data,
            //         'approver' => $approver,
            //         'approver_progress' => $approver_progress,
            //         'role' => $role
            //     ];

            //     Mail::to($mail_to)->bcc($mail_cc)->send(new SendEmail($data, 'done_tunjangan_keluarga'));
            // }else if($approver->project_name == 'Tunjangan Kerja'){
            //     $data = db::select("select request_id, up.department, up.employee, bulan, e.name, e.section, e.sub_group, category, tanggal_in, tanggal_out, jumlah_kehadiran from uang_pekerjaans as up left join employee_syncs as e on e.employee_id = up.employee where request_id = '".$request_id."'");

            //     $update = UangPekerjaan::where('request_id', $request_id)->update([
            //         'remark' => 'Close'
            //     ]);

            //     $data = [
            //         'data' => $data,
            //         'approver' => $approver,
            //         'approver_progress' => $approver_progress,
            //         'role' => $role
            //     ];

            //     Mail::to($mail_to)->bcc($mail_cc)->send(new SendEmail($data, 'tunjangan_kerja_tanpa_ttd'));
            // }

            $p = 'Request Berhasil Disetujui';
        }else{
         $p = 'Maaf Anda Tidak Memiliki Akses Approval Ini';
     }

     $req = HrApproval::where('request_id', $request_id)
     ->select('request_id', 'project_name')
     ->first();

     return view('human_resource.magang.confirm_approval', array(
        'title'    => 'Pengajuan '.$req->project_name, 
        'title_jp' => '??',
        'data' => $req,
        'p' => $p
    ))->with('page', 'Pengajuan '.$req->project_name);
 }
 else{
    $p = 'Request Ini Telah Disetujui Semua';

    $req = HrApproval::where('request_id', $request_id)
    ->select('request_id')
    ->first();

    return view('human_resource.magang.confirm_approval', array(
        'title'    => 'Pengajuan '.$req->project_name, 
        'title_jp' => '??',
        'data' => $req,
        'p' => $p
    ))->with('page', 'Pengajuan '.$req->project_name);
}
}

public function RejectedRequestTunjangan($request_id, $approver_id){
    $id = strtoupper(Auth::user()->username);
    $a = HrApproval::where('request_id', '=', $request_id)->wherenull('status')->take(1)->first();
    $b = HrApproval::where('request_id', '=', $request_id)->where('approver_id', '=', $id)->whereNotNull('status')->first();

    if (count($a) > 0) {
        if ($a->approver_id == $id) {
            $approved = HrApproval::where('request_id', '=', $request_id)->wherenull('status')->where('id',$approver_id)->update([
                'status' => 'Rejected',
                'approved_at' => date('Y-m-d h:i:s')
            ]);

            $approver_progress = HrApproval::where('request_id', '=', $request_id)
            ->whereNotNull('approver_id')
            ->get();

            $approver = HrApproval::where('request_id', '=', $request_id)
            ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at', 'project_name')
            ->first();

            $role = User::where('username', Auth::user()->username)
            ->select('username','role_code')
            ->first();

            $mail_to = [];
            $mail_bcc = [];

            array_push($mail_to, 'ummi.ernawati@music.yamaha.com');
            array_push($mail_bcc, 'ympi-mis-ML@music.yamaha.com');

            if ($approver->project_name == 'Uang Simpati') {
                $data = db::select("select us.id, us.request_id, us.employee, e.`name`, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, u.`name` as pembuat, us.remark from uang_simpatis as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by where request_id = '".$request_id."'");

                $update = UangSimpati::where('request_id', $request_id)->update([
                    'remark' => 'Rejected'
                ]); 

                $data = [
                    'data' => $data,
                    'approver' => $approver,
                    'approver_progress' => $approver_progress,
                    'role' => $role
                ];

                Mail::to($mail_to)->cc($mail_bcc)
                ->send(new SendEmail($data, 'rejected_uang_simpati'));
            }else if($approver->project_name == 'Tunjangan Keluarga'){
                $data = db::select("select us.id, us.request_id, us.employee, e.`name`, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, u.`name` as pembuat, us.remark from uang_keluargas as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by where request_id = '".$request_id."'");

                $update = UangKeluarga::where('request_id', $request_id)->update([
                    'remark' => 'Rejected'
                ]);

                $data = [
                    'data' => $data,
                    'approver' => $approver,
                    'approver_progress' => $approver_progress,
                    'role' => $role
                ];

                Mail::to($mail_to)->cc($mail_bcc)
                ->send(new SendEmail($data, 'rejected_tunjangan_keluarga'));
            }else if($approver->project_name == 'Tunjangan Kerja'){
                $data = db::select("select request_id, up.department, up.employee, bulan, e.name, e.section, e.sub_group, category, tanggal_in, tanggal_out, jumlah_kehadiran from uang_pekerjaans as up left join employee_syncs as e on e.employee_id = up.employee where request_id = '".$request_id."'");

                $update = UangPekerjaan::where('request_id', $request_id)->update([
                    'remark' => 'Rejected'
                ]);

                $data = [
                    'data' => $data,
                    'approver' => $approver,
                    'approver_progress' => $approver_progress,
                    'role' => $role
                ];

                Mail::to($mail_to)->cc($mail_bcc)
                ->send(new SendEmail($data, 'tunjangan_kerja_tanpa_ttd'));
            }
            $p = 'Request Berhasil Direject';
        }else{
         $p = 'Maaf Anda Tidak Memiliki Akses Approval Ini';
     }

     $req = HrApproval::where('request_id', $request_id)
     ->select('request_id', 'project_name')
     ->first();

     return view('human_resource.magang.confirm_approval', array(
        'title'    => 'Pengajuan '.$req->project_name, 
        'title_jp' => '??',
        'data' => $req,
        'p' => $p
    ))->with('page', 'Pengajuan '.$req->project_name);
 }
 else{
    $p = 'Request Ini Telah Disetujui Semua';

    $req = HrApproval::where('request_id', $request_id)
    ->select('request_id')
    ->first();

    return view('human_resource.magang.confirm_approval', array(
        'title'    => 'Pengajuan '.$req->project_name, 
        'title_jp' => '??',
        'data' => $req,
        'p' => $p
    ))->with('page', 'Pengajuan '.$req->project_name);
}
}

public function SignApproval($request_id, $approver_id){
  $data = db::select("select r.id, request_id, r.position, r.department, r.employment_status, quantity_male, quantity_female, reason, DATE_FORMAT(start_date, '%d-%m-%Y') as start_date, min_age, max_age, marriage_status, domicile, work_experience, skill, educational_level, r.major, requirement, note, progress, remark, status_req, e.name, es.employee_id, comment_note, reply from recruitments as r left join users as e on r.created_by = e.id left join employee_syncs as es on e.username = es.employee_id where request_id = '".$request_id."'");

  $app_email = RecruitmentApproval::where('request_id', '=', $request_id)
  ->wherenull('status')
  ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
  ->first();

  $app_progress = RecruitmentApproval::where('request_id', '=', $request_id)
  ->whereNotNull('approver_id')
  ->get();

  $data = [
    'data' => $data,
    'app_email' => $app_email,
    'app_progress' => $app_progress,
    'remark' => 'Recruitment HR'
];

return view('human_resource.recruitment.mail_request_manpower_approval', array(
  // return view('mails.sign_approval', array(
    'title'    => 'MIRAI Approval System', 
    'title_jp' => 'MIRAI 承認システム',
    'data'     => $data,
    'app_email' => $app_email,
    'app_progress' => $app_progress
))->with('page', 'MIRAI Approval System');
}

public function SignApprovalTunjangan($request_id, $approver_id){
  $role = User::where('username', Auth::user()->username)
  ->select('username', 'role_code')
  ->first();

  $approver = HrApproval::
  where('request_id', '=', $request_id)
  ->where('status', 'Waiting')
  ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at', 'project_name')
  ->orderBy('id', 'ASC') 
  ->first();

  $approver_progress = HrApproval::where('request_id', '=', $request_id)
  ->whereNotNull('approver_id')
  ->get();

  if (count($approver) > 0) {
      if ($approver->project_name == 'Uang Simpati') {
          $data = db::select("select us.id, us.request_id, us.employee, e.`name`, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, u.`name` as pembuat, us.remark from uang_simpatis as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by where request_id = '".$request_id."'");
      }else if ($approver->project_name == 'Tunjangan Keluarga') {
          $data = db::select("select us.id, us.request_id, us.employee, e.`name`, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, u.`name` as pembuat, us.remark from uang_keluargas as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by where request_id = '".$request_id."'");
      }else if($approver->project_name == 'Tunjangan Kerja'){
        $data = db::select("select request_id, up.department, up.employee, e.name, e.section, e.sub_group, category, tanggal_in, tanggal_out, jumlah_kehadiran, bulan from uang_pekerjaans as up left join employee_syncs as e on e.employee_id = up.employee where request_id = '".$request_id."'");
    }

    $data = [
        'data' => $data,
        'approver' => $approver,
        'approver_progress' => $approver_progress,
        'role' => $role
    ];

    if ($approver->project_name == 'Uang Simpati') {
        return view('human_resource.mails_uang_simpati', array(
        // return view('human_resource.mails_approver_simpati', array(
            'title'    => 'MIRAI Approval System', 
            'title_jp' => 'MIRAI 承認システム',
            'data'     => $data,
            'approver' => $approver,
            'approver_progress' => $approver_progress,
            'role' => $role,
            'approver_id' => $approver_id
        ))->with('page', 'MIRAI Approval System');
    }else if($approver->project_name == 'Tunjangan Keluarga'){
        return view('human_resource.mails_tunjangan_keluarga', array(
            'title'    => 'MIRAI Approval System', 
            'title_jp' => 'MIRAI 承認システム',
            'data'     => $data,
            'approver' => $approver,
            'approver_progress' => $approver_progress,
            'role' => $role,
            'approver_id' => $approver_id
        ))->with('page', 'MIRAI Approval System');
    }else if($approver->project_name == 'Tunjangan Kerja'){
        return view('human_resource.mails_approver_kerja', array(
            'title' => 'Form Request Manpower', 
            'title_jp' => 'フォームリクエストのマンパワー',
            'data' => $data
        ))->with('page', 'Human Resource');
    }
}else{
    // dd('Approval sudah disetujui semua');
    $p = 'Request Ini Telah Disetujui';

    $req = HrApproval::where('request_id', $request_id)
    ->select('request_id', 'project_name')
    ->first();

    return view('human_resource.magang.confirm_approval', array(
        'title'    => 'Pengajuan '.$req->project_name, 
        'title_jp' => '??',
        'data' => $req,
        'p' => $p
    ))->with('page', 'Pengajuan '.$req->project_name);
}


}

public function RequestMagangSignApproval($request_id, $approver_id){
  $data = db::select("select rm.id, request_id, rm.position, rm.department, rm.section, rm.`group`, rm.sub_group, qty_male, qty_female, rm.hire_date, rm.end_date, rm.remark, u.`name` as created_by, es.employee_id from request_magangs as rm left join users as u on rm.created_by = u.id left join employee_syncs as es on u.username = es.employee_id where request_id = '".$request_id."'");

  $approver = RequestMagangApproval::
  where('request_id', '=', $request_id)
  ->wherenull('status')
  ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
  ->first();

  $approver_progress = RequestMagangApproval::where('request_id', '=', $request_id)
  ->whereNotNull('approver_id')
  ->get();

  $data = [
    'data' => $data,
    'approver' => $approver,
    'approver_progress' => $approver_progress
];

return view('human_resource.magang.mail_request_magang_approval', array(
  // return view('mails.sign_approval', array(
    'title'    => 'MIRAI Approval System', 
    'title_jp' => 'MIRAI 承認システム',
    'data'     => $data,
    'approver' => $approver,
    'approver_progress' => $approver_progress
))->with('page', 'MIRAI Approval System');
}
        //reject
public function RejectedRequest($request_id){
  $approvers = RecruitmentApproval::where('request_id', '=', $request_id)->wherenull('status')->take(1)->update([
      'status'    => 'Rejected',
      'approved_at' => date('Y-m-d h:i:s')]);

  $rec = Recruitment::where('request_id', '=', $request_id)
  ->select('id','request_id', 'position', 'department', 'employment_status', 'quantity_male', 'quantity_female', 'reason', 'start_date', 'min_age', 'max_age', 'marriage_status', 'domicile', 'work_experience', 'skill', 'educational_level', 'major', 'requirement', 'note', 'progress', 'remark', 'created_by', 'status_at', 'status_req', 'reason_reject', 'process_penempatan', 'posisi', 'comment_note', 'reply')
  ->first();

  $data = [
      'rec' => $rec
  ];
  try{
    return view('human_resource.verifikasi.request_reject', array(
        'data' => $rec
    ))->with('page', 'Request');

} catch (Exception $e) {
    return view('human_resource.verifikasi.request_reject', array(
        'head' => $req->request_id,
        'message' => 'Error',
        'message2' => $e->getMessage(),
    ))->with('page', 'Request');
}
}

public function RejectedRequest_Post(Request $request, $request_id)
{
  $rec = Recruitment::where('request_id', '=', $request_id)
  ->select('id','request_id', 'position', 'department', 'employment_status', 'quantity_male', 'quantity_female', 'reason', 'start_date', 'min_age', 'max_age', 'marriage_status', 'domicile', 'work_experience', 'skill', 'educational_level', 'major', 'requirement', 'note', 'progress', 'remark', 'created_by', 'status_at', 'status_req', 'reason_reject', 'process_penempatan', 'posisi', 'comment_note', 'reply')
  ->first();

              // $rec_next = RecruitmentApproval::where('request_id', '=', $request_id)
              // ->where('status', '=', 'Hold & Comment')
              // ->select('request_id', 'approver_id', 'approver_name', 'approver_email', 'status', 'approved_at', 'remark')
              // ->first();


  $comment = $request->get('question');
  $rec->reason_reject = $comment;
  $rec->posisi = 'Rejected';
  $rec->save();

            // kirim email ke Applicant

  $data = db::select("select r.id, request_id, r.position, r.department, r.employment_status, quantity_male, quantity_female, reason, DATE_FORMAT(start_date, '%d-%m-%Y') as start_date, min_age, max_age, marriage_status, domicile, work_experience, skill, educational_level, r.major, requirement, note, progress, remark, e.name, es.employee_id, r.posisi, r.reason_reject, comment_note, reply, status_req from recruitments as r left join users as e on r.created_by = e.id left join employee_syncs as es on e.username = es.employee_id where request_id = '".$request_id."'");

  $app_email = RecruitmentApproval::where('request_id', '=', $request_id)
  ->wherenull('status')
  ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
  ->first();

  $app_progress = RecruitmentApproval::where('request_id', '=', $request_id)
  ->whereNotNull('approver_id')
  ->get();

  $data = [
    'data' => $data,
    'app_email' => $app_email,
    'app_progress' => $app_progress,
    'posisi' => 'Rejected'
];

$mails = "select distinct email from users where users.id = '".$rec->created_by."'";
$mailtoo = DB::select($mails);

Mail::to($mailtoo)
->bcc(['ympi-mis-ML@music.yamaha.com','anton.budi.santoso@music.yamaha.com'])
->send(new SendEmail($data, 'request_manpower_approval'));

return view('human_resource.verifikasi.request_comment_msg', array(
    'data'     => $rec
                // 'rec_next' => $rec_next,
                // 'app_hold' => $app_hold
))->with('page', 'Request Manpower');
}

        //comment
public function RequestComment($request_id){
    $approvers = RecruitmentApproval::where('request_id', '=', $request_id)->wherenull('status')->take(1)->update([
      'status'    => 'Hold & Comment',
      'approved_at' => date('Y-m-d h:i:s')]);

    $rec = Recruitment::where('request_id', '=', $request_id)
    ->select('id','request_id', 'position', 'department', 'employment_status', 'quantity_male', 'quantity_female', 'reason', 'start_date', 'min_age', 'max_age', 'marriage_status', 'domicile', 'work_experience', 'skill', 'educational_level', 'major', 'requirement', 'note', 'progress', 'remark', 'created_by', 'status_at', 'status_req', 'reason_reject', 'process_penempatan', 'posisi', 'comment_note', 'reply')
    ->first();

    $data = [
      'rec' => $rec
  ];
  try{
    return view('human_resource.verifikasi.request_comment', array(
        'data' => $rec
    ))->with('page', 'Request');

} catch (Exception $e) {
    return view('human_resource.verifikasi.request_comment', array(
        'head' => $req->request_id,
        'message' => 'Error',
        'message2' => $e->getMessage(),
    ))->with('page', 'Request');
}
}

public function RequestCommentReply($request_id){
    $rec = Recruitment::where('request_id', '=', $request_id)
    ->select('id','request_id', 'position', 'department', 'employment_status', 'quantity_male', 'quantity_female', 'reason', 'start_date', 'min_age', 'max_age', 'marriage_status', 'domicile', 'work_experience', 'skill', 'educational_level', 'major', 'requirement', 'note', 'progress', 'remark', 'created_by', 'status_at', 'status_req', 'reason_reject', 'process_penempatan', 'posisi', 'comment_note', 'reply')
    ->first();

    $data = [
      'rec' => $rec
  ];
  try{
    return view('human_resource.verifikasi.request_comment', array(
        'data' => $rec
    ))->with('page', 'Request');

} catch (Exception $e) {
    return view('human_resource.verifikasi.request_comment', array(
        'head' => $req->request_id,
        'message' => 'Error',
        'message2' => $e->getMessage(),
    ))->with('page', 'Request');
}
}

public function RequestComment_Post(Request $request, $request_id)
{
  $rec = Recruitment::where('request_id', '=', $request_id)
  ->select('id','request_id', 'position', 'department', 'employment_status', 'quantity_male', 'quantity_female', 'reason', 'start_date', 'min_age', 'max_age', 'marriage_status', 'domicile', 'work_experience', 'skill', 'educational_level', 'major', 'requirement', 'note', 'progress', 'remark', 'created_by', 'status_at', 'status_req', 'reason_reject', 'process_penempatan', 'posisi', 'comment_note', 'reply')
  ->first();

  $rec_next = RecruitmentApproval::where('request_id', '=', $request_id)
  ->where('status', '=', 'Hold & Comment')
  ->select('request_id', 'approver_id', 'approver_name', 'approver_email', 'status', 'approved_at', 'remark')
  ->first();

  if ($rec->posisi != "user") {
    $comment = $request->get('question');
    $rec->comment_note = $comment;
    $rec->posisi = "user";
    $rec->save();

            // kirim email dari approver ke selanjutnya

    $data = db::select("select r.id, request_id, r.position, r.department, r.employment_status, quantity_male, quantity_female, reason, DATE_FORMAT(start_date, '%d-%m-%Y') as start_date, min_age, max_age, marriage_status, domicile, work_experience, skill, educational_level, r.major, requirement, note, progress, remark, status_req, e.name, es.employee_id, r.posisi, comment_note, reply from recruitments as r left join users as e on r.created_by = e.id left join employee_syncs as es on e.username = es.employee_id where request_id = '".$request_id."'");

    $app_email = RecruitmentApproval::where('request_id', '=', $request_id)
    ->wherenull('status')
    ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
    ->first();

    $app_progress = RecruitmentApproval::where('request_id', '=', $request_id)
    ->whereNotNull('approver_id')
    ->get();

    $data = [
        'data' => $data,
        'app_email' => $app_email,
        'app_progress' => $app_progress,
        'posisi' => 'user'
    ];

    $before = '';

    $before_email = RecruitmentApproval::where('request_id', $request_id)
    ->whereNotNull('status')
    ->select('approver_email', 'remark')
    ->first();


    if ($rec_next->status == 'Hold & Comment' && $rec_next->remark == 'Manager') {
        $before = db::select('select distinct email from users where users.id = "'.$rec->created_by.'"');

        Mail::to($mailtoo)
        ->bcc(['ympi-mis-ML@music.yamaha.com','anton.budi.santoso@music.yamaha.com'])
        ->send(new SendEmail($data, 'request_manpower_approval'));

    }else if ($rec_next->status == 'Hold & Comment' && $rec_next->remark == 'DGM') {
        $before = RecruitmentApproval::where('request_id', '=', $request_id)
        ->where('remark', '=', 'Manager')
        ->select('approver_email')
        ->first();

        Mail::to($before->approver_email)
        ->bcc(['ympi-mis-ML@music.yamaha.com','anton.budi.santoso@music.yamaha.com'])
        ->send(new SendEmail($data, 'request_manpower_approval'));

    }else if ($rec_next->status == 'Hold & Comment' && $rec_next->remark == 'GM') {
        $before = RecruitmentApproval::where('request_id', '=', $request_id)
        ->where('remark', '=', 'DGM')
        ->select('approver_email')
        ->first();

        Mail::to($before->approver_email)
        ->bcc(['ympi-mis-ML@music.yamaha.com','anton.budi.santoso@music.yamaha.com'])
        ->send(new SendEmail($data, 'request_manpower_approval'));

    }else if ($rec_next->status == 'Hold & Comment' && $rec_next->remark == 'Presiden Direktur') {
        if ($rec_next->request_id == null && $rec_next->remark == 'GM') {
            $before = RecruitmentApproval::where('request_id', '=', $request_id)
            ->where('remark', '=', 'GM')
            ->select('approver_email')
            ->first();
        }
        else if ($rec_next->request_id == null && $rec_next->remark == 'DGM') {
            $before = RecruitmentApproval::where('request_id', '=', $request_id)
            ->where('remark', '=', 'GM')
            ->select('approver_email')
            ->first();
        }
        else{
            $before = RecruitmentApproval::where('request_id', '=', $request_id)
            ->where('remark', '=', 'Direktur HR')
            ->select('approver_email')
            ->first();
        }

        Mail::to($before->approver_email)
        ->bcc(['ympi-mis-ML@music.yamaha.com','anton.budi.santoso@music.yamaha.com'])
        ->send(new SendEmail($data, 'request_manpower_approval'));

    }else if ($rec_next->status == 'Hold & Comment' && ($rec_next->remark == 'Direktur HR' || $rec_next->remark == 'Manager HR')) {
        $before = RecruitmentApproval::where('request_id', '=', $request_id)
        ->where('remark', '=', 'Manager')
        ->select('approver_email')
        ->first();

        Mail::to($before->approver_email)
        ->bcc(['ympi-mis-ML@music.yamaha.com','anton.budi.santoso@music.yamaha.com'])
        ->send(new SendEmail($data, 'request_manpower_approval'));

    }




            // var_dump($before->approver_email);
            // die();

    return view('human_resource.verifikasi.request_comment_msg', array(
        'data'     => $rec,
        'rec_next' => $rec_next,
        'before'    => $before
    ))->with('page', 'Request Manpower');

} else if($rec->posisi == "user"){
    $approvers = RecruitmentApproval::where('request_id', '=', $request_id)->where('status', '=', 'Hold & Comment')->update([
      'status'    => null,
      'approved_at' => null]);

    $rec->reply = $request->get('answer');
    $rec->posisi = "approver";
    $rec->save();

            //kirim email ke Penanya
    $data = db::select("select r.id, request_id, r.position, r.department, r.employment_status, quantity_male, quantity_female, reason, DATE_FORMAT(start_date, '%d-%m-%Y') as start_date, min_age, max_age, marriage_status, domicile, work_experience, skill, educational_level, r.major, requirement, note, progress, remark, status_req, e.name, es.employee_id, comment_note, reply from recruitments as r left join users as e on r.created_by = e.id left join employee_syncs as es on e.username = es.employee_id where request_id = '".$request_id."'");

    $app_email = RecruitmentApproval::where('request_id', '=', $request_id)
    ->wherenull('status')
    ->select('approver_email', 'approver_id', 'id', 'remark', 'approver_name', 'status', 'approved_at')
    ->first();

    $app_progress = RecruitmentApproval::where('request_id', '=', $request_id)
    ->whereNotNull('approver_id')
    ->get();

    $data = [
        'data' => $data,
        'app_email' => $app_email,
        'app_progress' => $app_progress
    ];
    $app_hold = RecruitmentApproval::where('request_id', '=', $request_id)->wherenull('status')->take(1)->first();

    Mail::to($app_hold->approver_email)
    ->bcc(['ympi-mis-ML@music.yamaha.com','anton.budi.santoso@music.yamaha.com'])
    ->send(new SendEmail($data, 'request_manpower_approval'));
    return view('human_resource.verifikasi.request_comment_msg', array(
        'data'     => $rec,
        'rec_next' => $rec_next
    ))->with('page', 'Request Manpower');

}
return view('human_resource.verifikasi.request_comment_msg', array(
    'data'     => $rec,
    'rec_next' => $rec_next,
    'app_hold' => $app_hold
))->with('page', 'Request Manpower');
}

public function RequestCommentMsg($request_id){
 $rec = Recruitment::where('request_id', '=', $request_id)
 ->select('id','request_id', 'position', 'department', 'employment_status', 'quantity_male', 'quantity_female', 'reason', 'start_date', 'min_age', 'max_age', 'marriage_status', 'domicile', 'work_experience', 'skill', 'educational_level', 'major', 'requirement', 'note', 'progress', 'remark', 'created_by', 'status_at', 'status_req', 'reason_reject', 'process_penempatan', 'posisi', 'comment_note', 'reply')
 ->first();
 $data = [
  'rec' => $rec
];
try{
    return view('human_resource.verifikasi.request_comment_msg', array(
        'data' => $rec
    ))->with('page', 'Request');

} catch (Exception $e) {
    return view('human_resource.verifikasi.request_comment_msg', array(
        'head' => $req->request_id,
        'message' => 'Error',
        'message2' => $e->getMessage(),
    ))->with('page', 'Request');
}

}

public function ImportCalonKaryawan(Request $request)
{
    $request_id = $request->get('req_id');

    if($request->hasFile('newAttachment')) {
       try{
         $id_user = Auth::id();

         $file = $request->file('newAttachment');
         $file_name = 'calon_karyawan_'. date("ymd_h.i") .'.'.$file->getClientOriginalExtension();
         $file->move(public_path('data_file/human_resource/'), $file_name);
         $excel = public_path('data_file/human_resource/') . $file_name;

         $rows = Excel::load($excel, function($reader) {
            $reader->noHeading();
                        // $reader->skipRows(1);
        })->get();


         $rows = $rows->toArray();
                       // dd($rows);

         foreach ($rows as $r ) {
           $calon = CalonKaryawan::create([
            'request_id' => $request_id,
            'nama' => $r[0],      
            'asal' => $r[1],    
            'no_hp' => $r[2],
            'institusi' => $r[3],
            'email' => $r[4],
            'created_by' => $id_user,
            'created_at' => date('Y-m-d h:i:s'),
            'updated_at' => date('Y-m-d h:i:s')      
        ]);
       }

       $approve_at = Recruitment::where('request_id', '=', $request_id)->update([
        'status_at' => 'Upload Done'
    ]);



       $response = array(
        'status' => true,
        'message' => 'Upload Berhasil',
    );
       return Response::json($response);
   }catch (\Exception $e) {
      $response = array(
        'error' => false,
        'message' => $e->getMessage()
    );
      return Response::json($response);
  }
}else{
  $response = array(
    'status' => false,
    'message' => 'Upload failed, File not found',
);
  return Response::json($response);
}
}

public function CalonKaryawan($req_id){
    $resumes = db::select('SELECT id, request_id, nama, asal, no_hp, institusi, email, remark, created_by, created_at, updated_at, deleted_at, test_tpa, interview_awal, interview_user, test_psikotest, test_kesehatan, interview_management, induction FROM calon_karyawans WHERE request_id = "'.$req_id.'"');
    $recruitment = db::select('select request_id, department, section, `group`, sub_group, (quantity_male+quantity_female) as jumlah from recruitments where request_id = "'.$req_id.'"');

    return view('human_resource.recruitment.list_peserta', array(
        'title' => '', 
        'title_jp' => '',
        'resumes' => $resumes,
        'req_id' => $req_id,
        'recruitment' => $recruitment
    ))->with('page', 'Human Resource');
}

public function ResumeCalonKaryawan(Request $request){
    $req_id = $request->get('req_id');

    $resumes = db::select('SELECT c.id, c.request_id, c.nama, c.asal, c.no_hp, c.institusi, c.email, c.remark, c.created_by, c.created_at, c.updated_at, c.deleted_at, c.test_tpa, c.interview_awal, c.interview_user, c.test_psikotest, c.test_kesehatan, c.interview_management, c.induction, r.status_at FROM calon_karyawans c LEFT JOIN recruitments as r on r.request_id = c.request_id WHERE c.request_id = "'.$req_id.'"');

    $response = array(
        'status' => true,
        'message' => 'Upload Berhasil',
        'resumes' => $resumes
    );
    return Response::json($response);
}

public function CalonRekontrak($req_id){
    $resumes = db::select('SELECT id, request_id, nik, nama, penempatan, durasi FROM calon_rekontraks WHERE request_id = "'.$req_id.'"');

    $recruitment = db::select('select request_id, department, section, `group`, sub_group, (quantity_male+quantity_female) as jumlah from recruitments where request_id = "'.$req_id.'"');

    $b = db::select('SELECT request_id, count(status) as jumlah FROM calon_rekontraks where `status` = "LOLOS" and request_id = "'.$req_id.'" group by request_id');

    $jumlah = db::select('select count(`status`) as p from calon_rekontraks where request_id = "'.$req_id.'" and `status` = "TIDAK LOLOS"');

    return view('human_resource.recruitment.list_rekontrak', array(
        'title' => 'Form Request Manpower', 
        'title_jp' => 'フォームリクエストのマンパワー',
        'resumes' => $resumes,
        'req_id' => $req_id,
        'recruitment' => $recruitment,
        'b' => $b,
        'jumlah' => $jumlah
    ))->with('page', 'Human Resource');
}

public function KaryawanEndContract($department){
    $resumes = db::select('SELECT DISTINCT
        pc.employee_id,
        es.`name`,
        es.department,
        es.section,
        es.`group`,
        es.sub_group,
        DATE_FORMAT( planing_end_date, "%Y-%m" ) AS bulan,
        planing_end_date,
        es.hire_date 
        FROM
        plan_employee_contracts AS pc
        LEFT JOIN employee_syncs AS es ON es.employee_id = pc.employee_id 
        WHERE
        DATE_FORMAT( planing_end_date, "%m-%Y" ) = DATE_FORMAT( NOW() + INTERVAL 1 MONTH, "%m-%Y" ) 
        AND job_status IN ( "DIRECT", "INDIRECT", "STAFF" ) 
        AND pc.end_date IS NULL
        AND es.department = "'.$department.'"');

    $count = db::select('SELECT DISTINCT
        count(pc.employee_id) as jumlah
        FROM
        plan_employee_contracts AS pc
        LEFT JOIN employee_syncs AS es ON es.employee_id = pc.employee_id 
        WHERE
        DATE_FORMAT( planing_end_date, "%m-%Y" ) = DATE_FORMAT( NOW() + INTERVAL 1 MONTH, "%m-%Y" ) 
        AND job_status IN ( "DIRECT", "INDIRECT", "STAFF" ) 
        AND pc.end_date IS NULL
        AND es.department = "'.$department.'"');  
    $end = db::select('select count(id) as jumlah from plan_employee_contracts where remark = "2" and department = "'.$department.'"');

    // $recruitment = db::select('select request_id, department, section, `group`, sub_group, (quantity_male+quantity_female) as jumlah from recruitments where request_id = "'.$req_id.'"');

    // $b = db::select('SELECT request_id, count(status) as jumlah FROM calon_rekontraks where `status` = "LOLOS" and request_id = "'.$req_id.'" group by request_id');

    // $jumlah = db::select('select count(`status`) as p from calon_rekontraks where request_id = "'.$req_id.'" and `status` = "TIDAK LOLOS"');

    return view('human_resource.recruitment.karyawan_end_contract', array(
        'title' => 'Penilaian Karyawan Kontrak', 
        'title_jp' => '??',
        'resumes' => $resumes,
        'department' => $department,
        'count' => $count,
        'end' => $end
        // 'req_id' => $req_id,
        // 'recruitment' => $recruitment,
        // 'b' => $b,
        // 'jumlah' => $jumlah
    ))->with('page', 'Human Resource');
}

public function IndexPenggantiMp($department){
    $resumes = db::select('select name, department, section, `group`, sub_group, DATE_FORMAT(end_date, "%Y-%m") as bulan, end_date, hire_date from plan_employee_contracts where department = "'.$department.'" and DATE_FORMAT( end_date, "%Y-%m" ) = DATE_FORMAT( now() + INTERVAL 1 MONTH, "%Y-%m" ) ORDER BY name ASC');
    $count = db::select('select count(id) as jumlah from plan_employee_contracts where department = "'.$department.'" and DATE_FORMAT( end_date, "%Y-%m" ) = DATE_FORMAT( now() + INTERVAL 1 MONTH, "%Y-%m" ) and planing_end_date is null and remark is null');  
    $end = db::select('select count(id) as jumlah from plan_employee_contracts where remark = "2" and department = "'.$department.'"');

    // $mp = db::select("select old_nik, `name`, address, no_whatsapp, department, section, `group`, sub_group, end_date from veteran_data_masters where department = '".$department."'");

    $mp = VeteranDataMaster::select('name')->where('department',$department)->get();
    // dd($mp);

    // dd($mp);
    // $recruitment = db::select('select request_id, department, section, `group`, sub_group, (quantity_male+quantity_female) as jumlah from recruitments where request_id = "'.$req_id.'"');

    // $b = db::select('SELECT request_id, count(status) as jumlah FROM calon_rekontraks where `status` = "LOLOS" and request_id = "'.$req_id.'" group by request_id');

    // $jumlah = db::select('select count(`status`) as p from calon_rekontraks where request_id = "'.$req_id.'" and `status` = "TIDAK LOLOS"');

    return view('human_resource.recruitment.index_pengganti_mp', array(
        'title' => 'Penilaian Karyawan Kontrak', 
        'title_jp' => '??',
        'resumes' => $resumes,
        'department' => $department,
        'count' => $count,
        'end' => $end,
        'mp' => $mp,
        'mp2' => $mp
        // 'req_id' => $req_id,
        // 'recruitment' => $recruitment,
        // 'b' => $b,
        // 'jumlah' => $jumlah
    ))->with('page', 'Human Resource');
}

public function FetchKaryawanEndContract(Request $request)
{
    $dept = $request->get('department');
    $employee_id = $request->get('employee_id');
    $role = User::where('username', Auth::user()->username)
    ->select('username','role_code')
    ->first();
    try {
        // $date = [date('Y-m', strtotime("+1 months")), date('Y-m', strtotime("+2 months")), date('Y-m', strtotime("+3 months")), date('Y-m', strtotime("+4 months")), date('Y-m', strtotime("+5 months"))];
        // $employee = db::select('select count(employee_id) as jumlah from employee_syncs where end_date is null');
        // $data = db::select('select DATE_FORMAT(`month`, "%Y-%m") as bulan, sum(count) as jumlah from request_adds group by `month`');
        // $end_date = db::select('SELECT DATE_FORMAT(end_date,"%Y-%m") as end_date, count( id ) as jumlah FROM plan_employee_contracts GROUP BY DATE_FORMAT(end_date,"%Y-%m")');
        $resumes = db::select('SELECT DISTINCT
            es.employee_id,
            es.`name`,
            es.department,
            es.section,
            es.`group`,
            es.sub_group,
            DATE_FORMAT( planing_end_date, "%Y-%m" ) AS bulan,
            es.hire_date,
            es.end_date,
            planing_end_date,
            pc.`status`,
            pc.remark,
            pc.pc_t,
            pc.surat_dokter,
            pc.izin,
            pc.alpha,
            pc.note_hr,
            pc.score_akhir,
            pc.kategori_nilai 
            FROM
            plan_employee_contracts AS pc
            LEFT JOIN employee_syncs AS es ON es.employee_id = pc.employee_id 
            WHERE
            DATE_FORMAT( planing_end_date, "%m-%Y" ) = DATE_FORMAT( NOW() + INTERVAL 1 MONTH, "%m-%Y" ) 
            AND job_status IN ( "DIRECT", "INDIRECT", "STAFF" ) 
            AND pc.end_date IS NULL 
            AND es.department = "'.$dept.'"');
        $data = db::select('select * from plan_employee_contracts where employee_id = "'.$employee_id.'"');
        $sisa = db::select('select count(employee_id) as jumlah from plan_employee_contracts where department = "'.$dept.'" and DATE_FORMAT( end_date, "%Y-%m" ) = DATE_FORMAT( now() + INTERVAL 1 MONTH, "%Y-%m" ) and remark is null');

        $response = array(
            'status' => true,
            // 'data' => $data,
            // 'date' => $date,
            // 'employee' => $employee,
            // 'end_date' => $end_date,
            'resumes' => $resumes,
            'data' => $data,
            'sisa' => $sisa,
            'role' => $role
                // 'nilai' => $nilai
        );
        return Response::json($response);
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

// public function CalonKaryawanBaru($req_id){
//     $resumes = db::select('SELECT id, request_id, nik, nama, penempatan, durasi FROM calon_rekontraks WHERE request_id = "'.$req_id.'" ');

//     $recruitment = db::select('select request_id, department, create_section, create_group, create_sub_group from recruitments where request_id = "'.$req_id.'"');

//     return view('human_resource.recruitment.list_peserta', array(
//         'title' => 'Form Request Manpower', 
//         'title_jp' => 'フォームリクエストのマンパワー',
//         'resumes' => $resumes,
//         'req_id' => $req_id,
//         'recruitment' => $recruitment
//     ))->with('page', 'Human Resource');
// }

public function ResumeCalonRekontrak(Request $request){
    $req_id = $request->get('req_id');

    $resumes = db::select('SELECT cr.id, cr.request_id, cr.nik, cr.nama, cr.penempatan, cr.durasi, cr.tpa, cr.kesehatan, cr.remark, vd.end_date, vd.no_whatsapp, (cr.tpa + cr.kesehatan) as jumlah, cr.status, r.status_at, cr.induction FROM calon_rekontraks as cr LEFT JOIN veteran_data_masters as vd ON vd.old_nik = cr.nik LEFT JOIN recruitments as r on r.request_id = cr.request_id  WHERE cr.request_id = "'.$req_id.'" ORDER BY jumlah DESC');

            // dd($request_id);

    $response = array(
        'status' => true,
        'message' => 'Upload Berhasil',
        'resumes' => $resumes
    );
    return Response::json($response);
}

public function InputNilaiPeserta(Request $request){
    try{
        $id = $request->get('id');
        $status_at = $request->get('status_at');
        $request_id = $request->get('request_id');
        $test_tpa = $request->get('array_tpa');
        $int_awal = $request->get('int_awal');
        $int_user = $request->get('int_user');
        $psikotest = $request->get('psikotest');
        $test_kes = $request->get('test_kes');
        $int_management = $request->get('int_management');
        $induction = $request->get('induction');

        if ($status_at == 'Test Potensi Akademik') {
            for ($i=0; $i < count($test_tpa) ; $i++) { 
                $update = CalonKaryawan::where('id',$id[$i])->update([
                    'test_tpa' => $test_tpa[$i]
                ]);
            }

            $update_request = Recruitment::where('request_id', $request_id)->update([
                'status_at' => 'Test Wawancara'
            ]);
        }

        if ($status_at == 'Test Wawancara') {
            if (count($int_awal) > 0) {
                for ($i=0; $i < count($int_awal) ; $i++) { 
                    $update = CalonKaryawan::where('id',$id[$i])->update([
                        'interview_awal' => $int_awal[$i]
                    ]);
                }
            }

            if (count($int_user) > 0) {
                for ($i=0; $i < count($int_user) ; $i++) { 
                    $update = CalonKaryawan::where('id',$id[$i])->update([
                        'interview_user' => $int_user[$i]
                    ]);
                }

                $update_request = Recruitment::where('request_id', $request_id)->update([
                    'status_at' => 'Psikotest'
                ]);
            }
        }

        if ($status_at == 'Psikotest') {
            for ($i=0; $i < count($psikotest) ; $i++) { 
                $update = CalonKaryawan::where('id',$id[$i])->update([
                    'test_psikotest' => $psikotest[$i]
                ]);
            }

            $update_request = Recruitment::where('request_id', $request_id)->update([
                'status_at' => 'Test Kesehatan'
            ]);
        }

        if ($status_at == 'Test Kesehatan') {
            for ($i=0; $i < count($test_kes) ; $i++) { 
                $update = CalonKaryawan::where('id',$id[$i])->update([
                    'test_kesehatan' => $test_kes[$i]
                ]);
            }

            $update_request = Recruitment::where('request_id', $request_id)->update([
                'status_at' => 'Interview Management'
            ]);
        }

        if ($status_at == 'Interview Management') {
            for ($i=0; $i < count($int_management) ; $i++) { 
                $update = CalonKaryawan::where('id',$id[$i])->update([
                    'interview_management' => $int_management[$i]
                ]);
            }

            $update_request = Recruitment::where('request_id', $request_id)->update([
                'status_at' => 'Induction Training'
            ]);
        }

        if ($status_at == 'Induction Training') {
            for ($i=0; $i < count($induction) ; $i++) { 
                $update = CalonKaryawan::where('id',$id[$i])->update([
                    'induction' => $induction[$i]
                ]);
            }
        }

        $response = array(
            'status' => true,
            'message' => 'Berhasil',
        );
        return Response::json($response);
    }catch (\Exception $e) {
      $response = array(
        'error' => false,
        'message' => $e->getMessage()
    );
      return Response::json($response);
  }
}

public function InputNilaiRekontrak(Request $request){
    try{
        $id = $request->get('id');
        $status_at = $request->get('status_at');
        $request_id = $request->get('request_id');
        $test_tpa = $request->get('test_tpa');
        $test_kes = $request->get('test_kes');
        $induction = $request->get('induction');

        if ($status_at == 'Test Potensi Akademik') {
            for ($i=0; $i < count($test_tpa) ; $i++) { 
                if ($test_tpa[$i] > 50) {
                    $update = CalonRekontrak::where('id',$id[$i])->update([
                        'tpa' => $test_tpa[$i],
                        'status' => 'Test Kesehatan'
                    ]);

                    $update_request = Recruitment::where('request_id', $request_id)->update([
                        'status_at' => 'Test Kesehatan'
                    ]);
                }
                else{
                    $update = CalonRekontrak::where('id',$id[$i])->update([
                        'tpa' => $test_tpa[$i],
                        'status' => 'TIDAK LOLOS'
                    ]);
                }
            }
        }

        if ($status_at == 'Test Kesehatan') {
            for ($i=0; $i < count($test_kes) ; $i++) {
                if ($test_kes[$i] == 'LOLOS'){
                    $update = CalonRekontrak::where('id',$id[$i])->update([
                        'kesehatan' => $test_kes[$i],
                        'status' => 'Induction Training'
                    ]);

                    $update_request = Recruitment::where('request_id', $request_id)->update([
                        'status_at' => 'Induction Training'
                    ]);
                }else{
                    $update = CalonRekontrak::where('id',$id[$i])->update([
                        'kesehatan' => $test_kes[$i]
                    ]);
                }
            }

            // $update_request = Recruitment::where('request_id', $request_id)->update([
            //     'status_at' => 'Induction Training'
            // ]);
        }

        if ($status_at == 'Induction Training') {
            for ($i=0; $i < count($induction) ; $i++) { 

                if ($induction[$i] == 'LOLOS'){
                    $employee_update = CalonRekontrak::where('id', $id[$i])->first();
                    $data_veteran = VeteranDataMaster::where('old_nik', $employee_update->nik)->first();

                    $update = CalonRekontrak::where('id',$id[$i])->update([
                        'induction' => $induction[$i],
                        'status' => 'DONE'
                    ]);

                    $update_request = Recruitment::where('request_id', $request_id)->update([
                        'status_at' => 'DONE'
                    ]);

                    
                    $insert_employee_update = new EmployeeUpdate([
                        'nik' => $employee_update->nik,
                        'name' => $data_veteran->name,
                        'handphone' => $data_veteran->no_whatsapp,
                        'address' => $data_veteran->address,
                        'current_address' => $data_veteran->address
                    ]);
                    $insert_employee_update->save();

                }else{
                    $update = CalonRekontrak::where('id',$id[$i])->update([
                        'induction' => $induction[$i],
                        'status' => 'TIDAK LOLOS'
                    ]);
                }
            }

            // $update_request = Recruitment::where('request_id', $request_id)->update([
            //     'status_at' => 'DONE'
            // ]);
        }

        $response = array(
            'status' => true,
            'message' => 'Berhasil',
        );
        return Response::json($response);
    }catch (\Exception $e) {
      $response = array(
        'error' => false,
        'message' => $e->getMessage()
    );
      return Response::json($response);
  }
}

public function saveNilai(Request $request){
    try{
        $id = $request->get('id');
        $nilai = $request->get('nilai');
        $kesehatan = $request->get('kesehatan');

        $request_id = CalonRekontrak::where('id', $id)
        ->select('request_id')
        ->first();

        if ($nilai != null) {
            $nl = CalonRekontrak::where('id', '=', $id)->wherenull('tpa')->update([
                'tpa' => $nilai
            ]);

            if ($nilai >= 50) {
                $update_status = CalonRekontrak::where('id', '=', $id)->update([
                    'status' => 'Test Kesehatan'
                ]);

                $update_request = Recruitment::where('request_id', $request_id->request_id)->update([
                    'status_at' => 'Test Kesehatan'
                ]);
            }else{
                $update_status = CalonRekontrak::where('id', '=', $id)->update([
                    'status' => 'TIDAK LOLOS'
                ]);
            }
        }

        if ($kesehatan != null) {
            $nl = CalonRekontrak::where('id', '=', $id)->wherenull('kesehatan')->update([
                'kesehatan' => $kesehatan
            ]);

            if ($kesehatan == 'OK') {
                $update_status = CalonRekontrak::where('id', '=', $id)->update([
                    'status' => 'Induction'
                ]);

                $update_request = Recruitment::where('request_id', $request_id->request_id)->update([
                    'status_at' => 'Induction'
                ]);
            }else{
                $update_status = CalonRekontrak::where('id', '=', $id)->update([
                    'status' => 'TIDAK LOLOS'
                ]);
            }
        }
        // $nl = CalonRekontrak::where('id', '=', $id)->wherenull('tpa')->wherenull('kesehatan')->update([
        //     'tpa' => $nilai,
        //     'kesehatan' => $kesehatan
        // ]);

        // $request_id = CalonRekontrak::where('id', $id)
        // ->select('request_id')
        // ->first();


        // if ($nilai >= 50) {
        //     $update_status = CalonRekontrak::where('id', '=', $id)->update([
        //         'status' => 'LOLOS',
        //         'remark' => 'Test Kesehatan'
        //     ]);

        //     $update_request = Recruitment::where('request_id', $request_id->request_id)->update([
        //         'status_at' => 'Test Kesehatan'
        //     ]);
        // }else{
        //     $update_status = CalonRekontrak::where('id', '=', $id)->update([
        //         'status' => 'TIDAK LOLOS'
        //     ]);
        // }

            // if ($jenis == 'TPA') {
            //     for ($i=0; $i < count($id); $i++) { 
            //         $nl = CalonKaryawan::where('id', '=', $id[$i])->wherenull('test_tpa')->update([
            //             'test_tpa' => $nilai[$i]
            //         ]);
            //     }
            // }
            // if ($jenis == 'Interview Awal') {
            //     for ($i=0; $i < count($id); $i++) { 
            //         $nl = CalonKaryawan::where('id', '=', $id[$i])->wherenull('interview_awal')->update([
            //             'interview_awal' => $nilai[$i]
            //         ]);
            //     }
            // }
            // if ($jenis == 'Test Wawancara') {
            //     for ($i=0; $i < count($id); $i++) { 
            //         $nl = CalonKaryawan::where('id', '=', $id[$i])->wherenull('interview_user')->update([
            //             'interview_user' => $nilai[$i]
            //         ]);
            //     }
            // }
            // if ($jenis == 'Psikotest') {
            //     for ($i=0; $i < count($id); $i++) { 
            //         $nl = CalonKaryawan::where('id', '=', $id[$i])->wherenull('test_psikotest')->update([
            //             'test_psikotest' => $nilai[$i]
            //         ]);
            //     }
            // }
            // if ($jenis == 'Test Kesehatan') {
            //     for ($i=0; $i < count($id); $i++) { 
            //         $nl = CalonKaryawan::where('id', '=', $id[$i])->wherenull('test_kesehatan')->update([
            //             'test_kesehatan' => $nilai[$i]
            //         ]);
            //     }
            // }
            // if ($jenis == 'Interview Management') {
            //     for ($i=0; $i < count($id); $i++) { 
            //         $nl = CalonKaryawan::where('id', '=', $id[$i])->wherenull('interview_management')->update([
            //             'interview_management' => $nilai[$i]
            //         ]);
            //     }
            // }

        $response = array(
            'status' => true,
            'message' => 'Berhasil',
        );
        return Response::json($response);
    }catch (\Exception $e) {
      $response = array(
        'error' => false,
        'message' => $e->getMessage()
    );
      return Response::json($response);
  }
}

public function accKontrak(Request $request){
    try{
        $nama = $request->get('nama');
        $nik = $request->get('nik');
        $id = $request->get('id');

        $request_id = CalonRekontrak::where('id', $id)
        ->select('request_id', 'nama')
        ->first();

        $update_status = CalonRekontrak::where('id', '=', $id)->update([
            'status' => 'LOLOS'
        ]);

        $update_request = Recruitment::where('request_id', $request_id->request_id)->update([
            'status_at' => 'TTD Kontrak'
        ]);

        $input = new EmployeeUpdate ([
            'name' => $request_id->nama,
            'address' => "/////",
            'current_address' => "/////",
        ]);
        $input->save();

        $response = array(
            'status' => true,
            'message' => 'Berhasil',
        );
        return Response::json($response);
    }catch (\Exception $e) {
      $response = array(
        'error' => false,
        'message' => $e->getMessage()
    );
      return Response::json($response);
  }
}

public function indexLeaveRequestReport()
{
    $user = EmployeeSync::where('employee_id',Auth::user()->username)->first();
    $departments = db::table('departments')->orderBy('department_name', 'ASC')->get();
    return view('human_resource.leave_request.report', array(
        'title' => 'Report Surat Izin Keluar', 
        'title_jp' => '外出申請書',
        'user' => $user,
        'leave_status' => $this->leave_status,
        'department' => $departments
    ))->with('page', 'Human Resource');
}

public function fetchLeaveRequestReport(Request $request)
{   
    try {
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        if ($date_from == "") {
           if ($date_to == "") {
              $first = date('Y-m-01');
              $last = date('Y-m-d');
          }else{
              $first = date('Y-m-01');
              $last = $date_to;
          }
      }else{
       if ($date_to == "") {
          $first = $date_from;
          $last = date('Y-m-d');
      }else{
          $first = $date_from;
          $last = $date_to;
      }
  }

  $emp = EmployeeSync::where('employee_id',Auth::user()->id)->first();

  $leave_request = HrLeaveRequest::select('hr_leave_requests.*','hr_leave_request_details.*','hr_leave_request_details.*','departments.department_name','departments.department_shortname')->
  join('hr_leave_request_details','hr_leave_request_details.request_id','hr_leave_requests.request_id')
  ->join('departments','hr_leave_request_details.department','departments.department_name')
        // ->where('hr_leave_requests.created_by',Auth::user()->id)
  ->orderBy('hr_leave_requests.created_at','desc');

  $leave_request2 = HrLeaveRequest::
        // where('hr_leave_requests.created_by',Auth::user()->id)
  orderBy('hr_leave_requests.created_at','desc');

  if ($request->get('leave_status') != '') {
    $leave_request = $leave_request->where('hr_leave_requests.remark',$request->get('leave_status'));
    $leave_request2 = $leave_request2->where('hr_leave_requests.remark',$request->get('leave_status'));
}

if ($request->get('department') != '') {
    $leave_request = $leave_request->where('hr_leave_request_details.department',$request->get('department'));
}

if ($request->get('category') == 'Pulang Cepat') {
    $leave_request = $leave_request->where('hr_leave_requests.purpose_category','PRIBADI')->where('return_or_not','NO');
}
if ($request->get('category') == 'Dinas Makan Siang') {
    $leave_request = $leave_request->where('hr_leave_requests.purpose_category','DINAS')->where(DB::RAW("DATE_FORMAT(time_departure,'%H:%i:%s')"),'<=','11:45:00')->where(DB::RAW("DATE_FORMAT(time_arrived,'%H:%i:%s')"),'>=','12:55:00');
}

$leave_request = $leave_request->leftjoin('drivers','drivers.id','hr_leave_requests.driver_request_id');
$leave_request2 = $leave_request2->leftjoin('drivers','drivers.id','hr_leave_requests.driver_request_id');

$leave_request = $leave_request->where('hr_leave_requests.date','>=',$first);
$leave_request2 = $leave_request2->where('hr_leave_requests.date','>=',$first);

$leave_request = $leave_request->where('hr_leave_requests.date','<=',$last);
$leave_request2 = $leave_request2->where('hr_leave_requests.date','<=',$last);


$leave_request = $leave_request->get();
$leave_request2 = $leave_request2->get();



$leave_approvals = [];

foreach($leave_request2 as $lr){
    $leave_approval = HrLeaveRequestApproval::where('request_id',$lr->request_id)->get();
    array_push($leave_approvals, $leave_approval);
}



$response = array(
    'status' => true,
    'leave_request' => $leave_request,
    'leave_approvals' => $leave_approvals,
);
return Response::json($response);
} catch (\Exception $e) {
    $response = array(
        'error' => false,
        'message' => $e->getMessage()
    );
    return Response::json($response);
}
}
//monitoring
public function MonitoringRequestManpower()
{   
    $data_veteran = VeteranDataMaster::where('deleted_at', null)
    ->get();

    $role = User::where('username', Auth::user()->username)
    ->select('username','role_code')
    ->first();

    $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('employee_id','department')
    ->first();

    $departments = db::table('departments')->orderBy('department_name', 'ASC')->get();
    $jml_reqs1 = db::select('SELECT count(*) as jumlah from recruitments where position = "Staff"');
    $jml_reqs2 = db::select('SELECT count(*) as jumlah from recruitments where position = "Operator"');
    $employee = db::select('SELECT old_nik, `name` FROM veteran_data_masters');
    $section = db::select('select distinct sub_group from employee_syncs where department = "'.$emp_dept->department.'"');
    $month_now = date('Y-m');
    $username = User::where('username', Auth::user()->username)
    ->select('username')
    ->first();
    $dpt = EmployeeSync::where('employee_id', $username->username)
    ->select('department')
    ->first();

    $budget = db::select('select sum(p.diff-p.req) as diff from (
        SELECT
        department,
        section,
        `group`,
        sub_group,
        con_emp_sync,
        con_upload,
        ( con_upload - con_emp_sync ) AS diff,
        0 as req
        FROM
        (
        SELECT
        department,
        section,
        `group`,
        sub_group,
        SUM( con ) con_emp_sync,
        SUM( count ) AS con_upload 
        FROM
        (
        SELECT
        department,
        section,
        `group`,
        sub_group,
        0 AS con,
        count
        FROM
        `request_adds` 
        WHERE
        DATE_FORMAT( `month`, "%Y-%m" ) = DATE_FORMAT( NOW() + INTERVAL 1 MONTH, "%Y-%m" ) UNION ALL
        SELECT
        department,
        section,
        `group`,
        sub_group,
        count( employee_id ) AS con,
        0 AS count 
        FROM
        employee_syncs 
        WHERE
        end_date IS NULL 
        AND department IS NOT NULL 
        GROUP BY
        department,
        section,
        `group`,
        sub_group 
        ) AS semua 
        GROUP BY
        department,
        section,
        `group`,
        sub_group 
        ) semua2 
        WHERE
        con_emp_sync <> con_upload 
        AND department = "'.$dpt->department.'"
        UNION ALL
        select department, create_section as section, create_group as `group`, create_sub_group as sub_group, 0 as con_emp_sync, 0 as con_upload, 0 as diff, 1 as req from recruitments where department = "'.$dpt->department.'" and DATE_FORMAT( start_date, "%Y-%m" ) = DATE_FORMAT( NOW() + INTERVAL 1 MONTH, "%Y-%m" )
        ) as p
        GROUP BY department, section, `group`, sub_group');

    $date = date("Y-m-d", strtotime('+3 weeks'));

    $title = 'Monitoring Request Manpower';
    $title_jp = 'コロナ調査報告';

    return view('human_resource.recruitment.monitoring_request', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'positions' => $this->position,
        'data_veteran' => $data_veteran,
        'role' => $role,
        'departments' => $departments,
        'jml_reqs1' => $jml_reqs1,
        'jml_reqs2' => $jml_reqs2,
        'employee' => $employee,
        'section' => $section,
        'emp_dept' => $emp_dept,
        'date' => $date,
        'budget' => $budget,
        'month_now' => $month_now
    ))->with('page', 'Veteran Employee')->with('head','Veteran Employee');
}

public function UserRequest()
{   
    $data_veteran = VeteranDataMaster::where('deleted_at', null)
    ->get();

    $role = User::where('username', Auth::user()->username)
    ->select('username','role_code')
    ->first();

    $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('employee_id','department')
    ->first();

    // $emp_sect = db::select('SELECT DISTINCT
    //     section 
    //     FROM
    //     employee_syncs 
    //     WHERE
    //     department = "'.$emp_dept->department.'" 
    //     AND section IS NOT NULL');

    $emp_sect = '';
    if ($role->role_code == 'MIS') {
        $emp_sect = EmployeeSync::distinct()
        ->select('section')
        ->orderBy('section', 'ASC')
        ->get();
    }else{
        $emp_sect = EmployeeSync::distinct()
        ->where('department', $emp_dept->department)
        ->select('section')
        ->get();    
    }
    

    $departments = db::table('departments')->orderBy('department_name', 'ASC')->get();
    $jml_reqs1 = db::select('SELECT count(*) as jumlah from recruitments where position = "Staff"');
    $jml_reqs2 = db::select('SELECT count(*) as jumlah from recruitments where position = "Operator"');
    $employee = db::select('SELECT old_nik, `name` FROM veteran_data_masters');
    $section = db::select('select distinct sub_group from employee_syncs where department = "'.$emp_dept->department.'"');
    $month_now = date('Y-m');
    $username = User::where('username', Auth::user()->username)
    ->select('username')
    ->first();

    $dpt = EmployeeSync::where('employee_id', $username->username)
    ->select('department')
    ->first();

    $budget = db::select('select sum(p.diff-p.req) as diff from (
        SELECT
        department,
        section,
        `group`,
        sub_group,
        con_emp_sync,
        con_upload,
        ( con_upload - con_emp_sync ) AS diff,
        0 as req
        FROM
        (
        SELECT
        department,
        section,
        `group`,
        sub_group,
        SUM( con ) con_emp_sync,
        SUM( count ) AS con_upload 
        FROM
        (
        SELECT
        department,
        section,
        `group`,
        sub_group,
        0 AS con,
        count
        FROM
        `request_adds` 
        WHERE
        DATE_FORMAT( `month`, "%Y-%m" ) = DATE_FORMAT( NOW() + INTERVAL 1 MONTH, "%Y-%m" ) UNION ALL
        SELECT
        department,
        section,
        `group`,
        sub_group,
        count( employee_id ) AS con,
        0 AS count 
        FROM
        employee_syncs 
        WHERE
        end_date IS NULL 
        AND department IS NOT NULL 
        GROUP BY
        department,
        section,
        `group`,
        sub_group 
        ) AS semua 
        GROUP BY
        department,
        section,
        `group`,
        sub_group 
        ) semua2 
        WHERE
        con_emp_sync <> con_upload 
        AND department = "'.$dpt->department.'"
        UNION ALL
        select department, create_section as section, create_group as `group`, create_sub_group as sub_group, 0 as con_emp_sync, 0 as con_upload, 0 as diff, 1 as req from recruitments where department = "'.$dpt->department.'" and DATE_FORMAT( start_date, "%Y-%m" ) = DATE_FORMAT( NOW() + INTERVAL 1 MONTH, "%Y-%m" )
        ) as p
        GROUP BY department, section, `group`, sub_group');

    $date = date("Y-m-d", strtotime('+3 weeks'));

    $title = 'Monitoring Request Manpower';
    $title_jp = 'コロナ調査報告';

    return view('human_resource.recruitment.request_user', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'positions' => $this->position,
        'data_veteran' => $data_veteran,
        'role' => $role,
        'departments' => $departments,
        'jml_reqs1' => $jml_reqs1,
        'jml_reqs2' => $jml_reqs2,
        'employee' => $employee,
        'section' => $section,
        'emp_dept' => $emp_dept,
        'date' => $date,
        'budget' => $budget,
        'month_now' => $month_now,
        'emp_sect' => $emp_sect
    ))->with('page', 'Veteran Employee')->with('head','Veteran Employee');
}

public function fetchGrafikUser(Request $request){
    try{ 
        $department = $request->get('dept');
        // dd($department);
        $dept = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'department')
        ->first();

        if ($department == null) {
            $grafik = db::select("SELECT
                DATE_FORMAT( CONCAT(bulan,'-01'), '%b-%Y') as bulan,
                sum( aktual ) AS aktual,
                sum( forecast ) AS forecast,
                sum( kontrak ) AS kontrak,
                sum( request ) AS request,
                ABS((
                    sum( aktual )- sum( forecast )- sum( request ))+ sum( kontrak )) AS selisihA,
                IF((sum( forecast )-sum( aktual ))+sum( kontrak ) > 0, (sum( forecast )-sum( aktual ))+sum( kontrak ), 0) as selisihB,
                sum( forecast )-(sum( aktual )-sum( kontrak )) selisih,
                IF(sum( forecast )-(sum( aktual )-sum( kontrak )) >= 0, null, ABS(sum( forecast )-(sum( aktual )-sum( kontrak )))) as up_minus
                FROM
                (
                    SELECT
                    DATE_FORMAT( week_date, '%Y-%m' ) AS bulan,
                    ( SELECT count( employee_id ) AS aktual FROM employee_syncs WHERE end_date IS NULL and employee_id like '%PI%') AS aktual,
                    0 AS forecast,
                    0 AS kontrak,
                    0 AS request 
                    FROM
                    weekly_calendars 
                    WHERE
                    DATE_FORMAT( week_date, '%Y-%m' ) > DATE_FORMAT( now(), '%Y-%m' ) 
                    AND DATE_FORMAT( week_date, '%Y-%m' ) <= DATE_FORMAT( NOW() + INTERVAL 5 MONTH, '%Y-%m' ) 
                    GROUP BY
                    bulan UNION ALL
                    SELECT
                    DATE_FORMAT( `month`, '%Y-%m' ) AS bulan,
                    0 AS aktual,
                    sum( count ) AS forecast,
                    0 AS kontrak,
                    0 AS request
                    FROM
                    request_adds 
                    WHERE
                    DATE_FORMAT( `month`, '%Y-%m' ) > DATE_FORMAT( now(), '%Y-%m' ) 
                    AND DATE_FORMAT( `month`, '%Y-%m' ) <= DATE_FORMAT( NOW() + INTERVAL 5 MONTH, '%Y-%m' ) 
                    GROUP BY
                    bulan UNION ALL
                    SELECT
                    DATE_FORMAT( end_date, '%Y-%m' ) AS bulan,
                    0 AS aktual,
                    0 AS forecast,
                    count( id ) AS kontrak,
                    0 AS request
                    FROM
                    plan_employee_contracts 
                    WHERE
                    DATE_FORMAT( end_date, '%Y-%m' ) > DATE_FORMAT( now(), '%Y-%m' ) 
                    AND DATE_FORMAT( end_date, '%Y-%m' ) <= DATE_FORMAT( NOW() + INTERVAL 5 MONTH, '%Y-%m' ) 
                    GROUP BY
                    bulan UNION ALL
                    SELECT
                    DATE_FORMAT( start_date, '%Y-%m' ) AS bulan,
                    0 AS aktual,
                    0 AS forecast,
                    0 AS kontrak,
                    sum( quantity_male+quantity_female ) AS request 
                    FROM
                    recruitments 
                    WHERE
                    DATE_FORMAT( start_date, '%Y-%m' ) > DATE_FORMAT( now(), '%Y-%m' ) 
                    AND DATE_FORMAT( start_date, '%Y-%m' ) <= DATE_FORMAT( NOW() + INTERVAL 5 MONTH, '%Y-%m' ) 
                    GROUP BY
                    bulan
                    ) AS `all` 
                GROUP BY
                bulan");

            $resume_mp = '';
            if ($request->get('select_bulan') != null) {
                $resume_mp = db::select('SELECT
                    employee_id,
                    `name`,
                    department,
                    hire_date,
                    planing_end_date 
                    FROM
                    plan_employee_contracts 
                    WHERE
                    DATE_FORMAT( planing_end_date, "%b-%Y" ) = "'.$request->get('select_bulan').'"
                    ORDER BY
                    department ASC');
            }else{
                $resume_mp = db::select('SELECT
                    employee_id,
                    `name`,
                    department,
                    hire_date,
                    planing_end_date 
                    FROM
                    plan_employee_contracts 
                    WHERE
                    DATE_FORMAT( planing_end_date, "%b-%Y" ) = "'.$grafik[0]->bulan.'"
                    ORDER BY
                    department ASC');
            }

            $ct1 = db::select('select sum(quantity_male+quantity_female) as jm_ct1 from recruitments where status_at = "Test Potensi Akademik"');
            $ct2 = db::select('select sum(quantity_male+quantity_female) as jm_ct2 from recruitments where status_at = "Test Wawancara"');
            $ct3 = db::select('select sum(quantity_male+quantity_female) as jm_ct3 from recruitments where status_at = "Test Kesehatan"');

            $induction = db::select("select sum(i.jumlah) as induction from ( select request_id, count(`status`) as jumlah from calon_karyawans where `status` = 'Induction Training' group by request_id union all select request_id, count(`status`) as jumlah from calon_rekontraks where `status` = 'Induction Training' group by request_id ) as i");

            $mp_actual = db::select('select employee_id from employee_syncs where end_date is null and employee_id like "%PI%"');
            $count_mp_actual = count($mp_actual);

            $mp_direct = db::select('SELECT
                es.employee_id 
                FROM
                employee_syncs AS es
                LEFT JOIN plan_employee_contracts AS ec ON ec.employee_id = es.employee_id 
                WHERE
                es.end_date IS NULL 
                AND es.employee_id LIKE "%PI%" 
                AND es.job_status = "DIRECT" 
                GROUP BY
                es.employee_id');
            $count_mp_direct = count($mp_direct);

            $mp_indirect = db::select('SELECT
                es.employee_id 
                FROM
                employee_syncs AS es
                LEFT JOIN plan_employee_contracts AS ec ON ec.employee_id = es.employee_id 
                WHERE
                es.end_date IS NULL 
                AND es.employee_id LIKE "%PI%" 
                AND es.job_status = "INDIRECT" 
                GROUP BY
                es.employee_id');
            $count_mp_indirect = count($mp_indirect);

            $mp_staff =db::select('SELECT
                es.employee_id 
                FROM
                employee_syncs AS es
                LEFT JOIN plan_employee_contracts AS ec ON ec.employee_id = es.employee_id 
                WHERE
                es.end_date IS NULL 
                AND es.employee_id LIKE "%PI%" 
                AND es.job_status = "STAFF" 
                GROUP BY
                es.employee_id');
            $count_mp_staff = count($mp_staff);

            $planing_mp_direct = db::select('SELECT
                pc.employee_id
                FROM
                plan_employee_contracts AS pc
                LEFT JOIN employee_syncs AS es ON es.employee_id = pc.employee_id 
                WHERE
                DATE_FORMAT( planing_end_date, "%m-%Y" ) = DATE_FORMAT( NOW() + INTERVAL 1 MONTH, "%m-%Y" ) 
                AND job_status = "DIRECT"
                AND pc.end_date is null
                GROUP BY
                employee_id');
            $count_planing_mp_direct = count($planing_mp_direct);

            $planing_mp_indirect = db::select('SELECT
                pc.employee_id
                FROM
                plan_employee_contracts AS pc
                LEFT JOIN employee_syncs AS es ON es.employee_id = pc.employee_id 
                WHERE
                DATE_FORMAT( planing_end_date, "%m-%Y" ) = DATE_FORMAT( NOW() + INTERVAL 1 MONTH, "%m-%Y" ) 
                AND job_status = "INDIRECT"
                AND pc.end_date is null
                GROUP BY
                employee_id');
            $count_planing_mp_indirect = count($planing_mp_indirect);

            $planing_mp_staff = db::select('SELECT
                pc.employee_id
                FROM
                plan_employee_contracts AS pc
                LEFT JOIN employee_syncs AS es ON es.employee_id = pc.employee_id 
                WHERE
                DATE_FORMAT( planing_end_date, "%m-%Y" ) = DATE_FORMAT( NOW() + INTERVAL 1 MONTH, "%m-%Y" ) 
                AND job_status = "STAFF"
                AND pc.end_date is null
                GROUP BY
                employee_id');
            $count_planing_mp_staff = count($planing_mp_staff);

            $mp_interview = db::connection('ympimis_2')->select('select id from hr_interviews');
            $mp_magang = db::connection('ympimis_2')->select('select id from hr_internsips');
            $mp_stock = db::connection('ympimis_2')->select('select id from hr_stocks');
        }
        else{
            $grafik = db::select("SELECT
                bulan,
                sum( aktual ) AS aktual,
                sum( forecast ) AS forecast,
                sum( kontrak ) AS kontrak,
                sum( request ) AS request,
                IF((sum( forecast )-(sum( aktual )- sum( kontrak ))- sum( request )) < 0 , 0, sum( forecast )-(sum( aktual )- sum( kontrak ))- sum( request )) as selisih
                FROM
                (
                    SELECT
                    DATE_FORMAT( week_date, '%Y-%m' ) AS bulan,
                    ( SELECT count( employee_id ) AS aktual FROM employee_syncs WHERE end_date IS NULL AND department = '".$department."' ) AS aktual,
                        0 AS forecast,
                        0 AS kontrak,
                        0 AS request 
                        FROM
                        weekly_calendars 
                        WHERE
                        DATE_FORMAT( week_date, '%Y-%m' ) >= DATE_FORMAT( now(), '%Y-%m' ) 
                        AND DATE_FORMAT( week_date, '%Y-%m' ) < DATE_FORMAT( NOW() + INTERVAL 5 MONTH, '%Y-%m' ) 
                        GROUP BY
                        bulan UNION ALL
                        SELECT
                        DATE_FORMAT( `month`, '%Y-%m' ) AS bulan,
                        0 AS aktual,
                        sum( count ) AS forecast,
                        0 AS kontrak,
                        0 AS request 
                        FROM
                        request_adds 
                        WHERE
                        DATE_FORMAT( `month`, '%Y-%m' ) >= DATE_FORMAT( now(), '%Y-%m' ) 
                        AND DATE_FORMAT( `month`, '%Y-%m' ) < DATE_FORMAT( NOW() + INTERVAL 5 MONTH, '%Y-%m' ) 
                        AND department = '".$department."' 
                        GROUP BY
                        bulan UNION ALL
                        SELECT
                        DATE_FORMAT( end_date, '%Y-%m' ) AS bulan,
                        0 AS aktual,
                        0 AS forecast,
                        count( id ) AS kontrak,
                        0 AS request 
                        FROM
                        plan_employee_contracts 
                        WHERE
                        DATE_FORMAT( end_date, '%Y-%m' ) >= DATE_FORMAT( now(), '%Y-%m' ) 
                        AND DATE_FORMAT( end_date, '%Y-%m' ) < DATE_FORMAT( NOW() + INTERVAL 5 MONTH, '%Y-%m' ) 
                        AND department = '".$department."' 
                        GROUP BY
                        bulan UNION ALL
                        SELECT
                        DATE_FORMAT( start_date, '%Y-%m' ) AS bulan,
                        0 AS aktual,
                        0 AS forecast,
                        0 AS kontrak,
                        sum( quantity_male + quantity_female ) AS request 
                        FROM
                        recruitments 
                        WHERE
                        DATE_FORMAT( start_date, '%Y-%m' ) >= DATE_FORMAT( now(), '%Y-%m' ) 
                        AND DATE_FORMAT( start_date, '%Y-%m' ) < DATE_FORMAT( NOW() + INTERVAL 5 MONTH, '%Y-%m' ) 
                        AND department = '".$department."' 
                        GROUP BY
                        bulan 
                        ) AS `all` 
                        GROUP BY
                        bulan");

                    $ct1 = db::select('select sum(quantity_male+quantity_female) as jm_ct1 from recruitments where status_at = "Test Potensi Akademik" and department = "'.$department.'"');
                    $ct2 = db::select('select sum(quantity_male+quantity_female) as jm_ct2 from recruitments where status_at = "Test Wawancara" and department = "'.$department.'"');
                    $ct3 = db::select('select sum(quantity_male+quantity_female) as jm_ct3 from recruitments where status_at = "Test Kesehatan" and department = "'.$department.'"');

                    $induction = db::select("select sum(i.jumlah) as induction from ( select request_id, count(`status`) as jumlah from calon_karyawans where `status` = 'Induction Training' group by request_id union all select request_id, count(`status`) as jumlah from calon_rekontraks where `status` = 'Induction Training' group by request_id ) as i");
                }

        // $grafik = db::select("SELECT
        //     DATE_FORMAT( b.`month`, '%Y-%m' ) AS `month`,
        //     department,
        //     sum( b.produksi ) AS request_produksi,
        //     sum( b.actual ) AS mp_aktual,
        //     sum( b.proses ) AS request,
        //     sum( b.produksi ) - sum( b.actual ) - sum( b.proses ) AS hr_recruitment 
        //     FROM
        //     (
        //         SELECT
        //         `month`,
        //         department,
        //         `section`,
        //         `group`,
        //         sub_group,
        //         SUM( count ) AS produksi,
        //         (
        //             SELECT
        //             count( employee_id ) 
        //             FROM
        //             employee_syncs 
        //             WHERE
        //             department = '".$dept->department."'
        //             AND end_date IS NULL 
        //             AND COALESCE ( sub_group, '' ) = COALESCE ( a.sub_group, '' ) 
        //             AND COALESCE ( `group`, '' )= COALESCE ( a.`group`, '' ) 
        //             AND COALESCE ( `department`, '' ) = COALESCE ( a.`department`, '' ) 
        //             AND COALESCE ( `section`, '' ) = COALESCE ( a.`section`, '' )) AS actual,
        //         COALESCE ((
        //             SELECT
        //             ( quantity_male + quantity_female ) AS process 
        //             FROM
        //             recruitments 
        //             WHERE
        //             remark = 'Recruitment HR' 
        //             AND COALESCE ( create_sub_group, '' ) = COALESCE ( a.sub_group, '' ) 
        //             AND COALESCE ( `create_group`, '' )= COALESCE ( a.`group`, '' ) 
        //             AND COALESCE ( `department`, '' ) = COALESCE ( a.`department`, '' ) 
        //             AND COALESCE ( `create_section`, '' ) = COALESCE ( a.`section`, '' ) 
        //             AND COALESCE ( DATE_FORMAT( start_date, '%Y-%m' ), '' ) = COALESCE ( DATE_FORMAT( a.`month`, '%Y-%m' ), '' )),
        //         0 
        //         ) AS proses 
        //         FROM
        //         request_adds a 
        //         WHERE
        //         department = '".$dept->department."'
        //         GROUP BY
        //         `month`,
        //         department,
        //         `group`,
        //         `section`,
        //         sub_group 
        //         ) b 
        //     GROUP BY
        //     `month`, b.department");
                $first_month = date('Y-m', strtotime("+1 months"));
                $next_month = date('Y-m', strtotime("+5 months"));
                $bulan_sekarang = date('M-Y');
                $emp_aktual = db::select('select count(employee_id) as jumlah from employee_syncs where end_date is null and employee_id like "%PI%"');
        // $emp_end = db::connection('ympimis_2')->select('SELECT count( id ) AS jumlah, periode_end_contract as bulan FROM endcontract_logs where periode_end_contract >= "'.$first_month.'" AND periode_end_contract <= "'.$next_month.'" GROUP BY periode_end_contract');
                $emp_end = db::connection('sunfish')->select("SELECT COUNT
                    ( A.employment_enddate ) AS jumlah,
                    IIF(end_date is null, FORMAT ( A.employment_enddate, 'yyyy-MM' ), FORMAT ( A.end_date, 'yyyy-MM' )) AS bulan 
                    FROM
                    TEODEMPCOMPANY AS A 
                    WHERE 
                    IIF(end_date is null, FORMAT ( A.employment_enddate, 'yyyy-MM' ), FORMAT ( A.end_date, 'yyyy-MM' )) >= '".$first_month."'
                    AND IIF(end_date is null, FORMAT ( A.employment_enddate, 'yyyy-MM' ), FORMAT ( A.end_date, 'yyyy-MM' )) <= '".$next_month."'
                    AND A.employ_code != 'PERMANENT' 
                    AND A.employment_enddate IS NOT NULL
                    GROUP BY
                    IIF(end_date is null, FORMAT ( A.employment_enddate, 'yyyy-MM' ), FORMAT ( A.end_date, 'yyyy-MM' ))");

                $emp_forecast = db::select('select sum(count) as jumlah, DATE_FORMAT(`month`, "%Y-%m") as bulan from request_adds GROUP BY `month`');

                $data_p = db::connection('sunfish')->select("SELECT COUNT
                    ( A.employment_enddate ) AS jumlah,
                    IIF(end_date is null, FORMAT ( A.employment_enddate, 'yyyy-MM' ), FORMAT ( A.end_date, 'yyyy-MM' )) AS bulan 
                    FROM
                    TEODEMPCOMPANY AS A 
                    WHERE
                    A.emp_no LIKE '%PI%' 
                    AND A.employ_code != 'PERMANENT' 
                    AND A.employment_enddate IS NOT NULL
                    GROUP BY
                    IIF(end_date is null, FORMAT ( A.employment_enddate, 'yyyy-MM' ), FORMAT ( A.end_date, 'yyyy-MM' ))");

                $response = array(
                    'status' => true,
                    'grafik' => $grafik,
                    'ct1' => $ct1,
                    'ct2' => $ct2,
                    'ct3' => $ct3,
                    'induction' => $induction,
                    'resume_mp' => $resume_mp,
                    'mp_actual' => $count_mp_actual,
                    'mp_direct' => $count_mp_direct,
                    'mp_indirect' => $count_mp_indirect,
                    'mp_staff' => $count_mp_staff,
                    'mp_interview' => $mp_interview,
                    'mp_magang' => $mp_magang,
                    'mp_stock' => $mp_stock,
                    'planing_mp_direct' => $count_planing_mp_direct,
                    'planing_mp_indirect' => $count_planing_mp_indirect,
                    'planing_mp_staff' => $count_planing_mp_staff,
                    'emp_aktual' => $emp_aktual,
                    'bulan_sekarang' => $bulan_sekarang,
                    'emp_end' => $emp_end,
                    'emp_forecast' => $emp_forecast,
                    'data_p' => $data_p
                );
                return Response::json($response);
            } catch (\Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage()
                );
                return Response::json($response);
            }
        }

        public function fetchMonitoringReqManpower(Request $request)
        {
            try {
                $month_now = date('Y-m');
                $bulan = $request->get('bulan');
                $dept = $request->get('dept');
                $emp = EmployeeSync::where('employee_id', Auth::user()->username)
                ->select('employee_id', 'department', 'position')
                ->first();

                if ($bulan == '') {
                    $grafik = db::select("SELECT
                        DATE_FORMAT( b.`month`, '%Y-%m' ) as `month`,
                        sum( b.produksi ) as request_produksi,
                        sum( b.actual ) as mp_aktual,
                        sum( b.proses ) as request,
                        sum(b.produksi) - sum(b.actual) - sum(b.proses) as hr_recruitment
                        FROM
                        (
                            SELECT
                            `month`,
                            department,
                            `section`,
                            `group`,
                            sub_group,
                            SUM( count ) AS produksi,
                            (
                                SELECT
                                count( employee_id )
                                FROM
                                employee_syncs
                                WHERE
                                department IS NOT NULL
                                AND end_date IS NULL
                                AND COALESCE ( sub_group, '' ) = COALESCE ( a.sub_group, '' )
                                AND COALESCE ( `group`, '' )= COALESCE ( a.`group`, '' )
                                AND COALESCE ( `department`, '' ) = COALESCE ( a.`department`, '' )
                                AND COALESCE ( `section`, '' ) = COALESCE ( a.`section`, '' )) AS actual,
                            COALESCE ((
                                SELECT
                                SUM(( quantity_male + quantity_female )) AS process
                                FROM
                                recruitments
                                WHERE
                                remark = 'Recruitment HR'
                                AND COALESCE ( sub_group, '' ) = COALESCE ( a.sub_group, '' )
                                AND COALESCE ( `group`, '' )= COALESCE ( a.`group`, '' )
                                AND COALESCE ( `department`, '' ) = COALESCE ( a.`department`, '' )
                                AND COALESCE ( `section`, '' ) = COALESCE ( a.`section`, '' )
                                AND COALESCE ( DATE_FORMAT(start_date,'%Y-%m'), '' ) = COALESCE ( DATE_FORMAT(a.`month`,'%Y-%m'), '' )),
                            0
                            ) AS proses
                            FROM
                            request_adds a
                            GROUP BY
                            `month`,
                            department,
                            `group`,
                            `section`,
                            sub_group
                            ) b
                        GROUP BY
                        `month`");

                    $list_open = db::select("
                        SELECT DISTINCT
                        apr.request_id, DATE_FORMAT(r.created_at, '%Y-%m-%d') as created_at, r.start_date, u.name, r.remark, r.status_at,
                        r.position, r.department, r.employment_status, r.quantity_male, r.quantity_female, r.reason, 
                        ( SELECT GROUP_CONCAT( a.approver_name ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id   ) as approval,
                        ( SELECT GROUP_CONCAT( COALESCE ( a.approved_at, '' ) ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id ) AS approved_at,
                        ( SELECT GROUP_CONCAT( a.approver_id ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as id,
                        ( SELECT GROUP_CONCAT( COALESCE(a.status,'') ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as status,
                        ( SELECT count(a.request_id) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as cnt,
                        ( SELECT count(a.`status`) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as app,
                        ( SELECT GROUP_CONCAT( a.approver_id ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id   ) as approver_id 
                        FROM
                        `recruitment_approvals` as apr
                        left join recruitments as r on r.request_id = apr.request_id
                        left join users as u on u.id = r.created_by where DATE_FORMAT( r.created_at, '%Y-%m' ) = '".$month_now."' AND r.remark = 'Recruitment HR' AND r.status_at = 'Process Approval' AND department = '".$dept."'
                        ");
                    $list_close = db::select("
                        SELECT DISTINCT
                        apr.request_id, DATE_FORMAT(r.created_at, '%Y-%m-%d') as created_at, r.start_date, u.name, r.remark, r.status_at,
                        r.position, r.department, r.employment_status, r.quantity_male, r.quantity_female, r.reason,
                        ( SELECT GROUP_CONCAT( a.approver_name ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id   ) as approval,
                        ( SELECT GROUP_CONCAT( COALESCE ( a.approved_at, '' ) ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id ) AS approved_at,
                        ( SELECT GROUP_CONCAT( a.approver_id ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as id,
                        ( SELECT GROUP_CONCAT( COALESCE(a.status,'') ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as status,
                        ( SELECT b.department FROM recruitments b WHERE b.request_id = apr.request_id  ) as department,
                        ( SELECT count(a.request_id) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as cnt,
                        ( SELECT count(a.`status`) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as app
                        FROM
                        `recruitment_approvals` as apr
                        left join recruitments as r on r.request_id = apr.request_id
                        left join users as u on u.id = r.created_by where DATE_FORMAT( r.created_at, '%Y-%m' ) = '".$month_now."' AND r.remark = 'Recruitment HR' AND r.status_at <> 'Process'
                        ");
                    $ct1 = db::select('select sum(quantity_male+quantity_female) as jm_ct1 from recruitments where status_at = "Test Potensi Akademik"');
                    $ct2 = db::select('select sum(quantity_male+quantity_female) as jm_ct2 from recruitments where status_at = "Test Wawancara"');
                    $ct3 = db::select('select sum(quantity_male+quantity_female) as jm_ct3 from recruitments where status_at = "Test Kesehatan"');

                    $induction = db::select("select sum(i.jumlah) as induction from ( select request_id, count(`status`) as jumlah from calon_karyawans where `status` = 'induction' group by request_id union all select request_id, count(`status`) as jumlah from calon_rekontraks where `status` = 'induction' group by request_id ) as i");

                    $magang = db::select("select sum(i.jumlah) as magang from ( select request_id, count(`status`) as jumlah from calon_karyawans where `status` = 'magang' group by request_id union all select request_id, count(`status`) as jumlah from calon_rekontraks where `status` = 'magang' group by request_id ) as i");

                    $list_magang = db::select("SELECT DISTINCT
                        apr.request_id, r.posisi, r.status as st,
                        DATE_FORMAT( r.created_at, '%Y-%m-%d' ) AS created_at,
                        r.department, r.section, r.`group`, r.sub_group, 
                        ( SELECT GROUP_CONCAT( a.approver_name ) FROM request_magang_approvals a WHERE a.request_id = apr.request_id ) AS approval,
                        ( SELECT GROUP_CONCAT( COALESCE ( a.approved_at, '' ) ) FROM request_magang_approvals a WHERE a.request_id = apr.request_id ) AS approved_at,
                        ( SELECT GROUP_CONCAT( a.approver_id ) FROM request_magang_approvals a WHERE a.request_id = apr.request_id ) AS id,
                        ( SELECT GROUP_CONCAT( COALESCE ( a.STATUS, '' ) ) FROM request_magang_approvals a WHERE a.request_id = apr.request_id ) AS status,
                        ( SELECT count( a.request_id ) FROM request_magang_approvals a WHERE a.request_id = apr.request_id ) AS cnt,
                        ( SELECT count( a.`status` ) FROM request_magang_approvals a WHERE a.request_id = apr.request_id ) AS app,
                        ( SELECT GROUP_CONCAT( a.approver_id ) FROM request_magang_approvals a WHERE a.request_id = apr.request_id ) AS approver_id
                        FROM
                        request_magang_approvals AS apr
                        LEFT JOIN request_magangs AS r ON r.request_id = apr.request_id
                        LEFT JOIN users AS u ON u.id = r.created_by 
                        WHERE
                        DATE_FORMAT( r.created_at, '%Y-%m' ) = '".$month_now."'
                        AND department = '".$dept."'
                        AND r.status != 'All Approve'");
                }else{
                    $grafik = db::select("SELECT
                        DATE_FORMAT( b.`month`, '%Y-%m' ) as `month`,
                        sum( b.produksi ) as request_produksi,
                        sum( b.actual ) as mp_aktual,
                        sum( b.proses ) as request,
                        sum(b.produksi) - sum(b.actual) - sum(b.proses) as hr_recruitment
                        FROM
                        (
                            SELECT
                            `month`,
                            department,
                            `section`,
                            `group`,
                            sub_group,
                            SUM( count ) AS produksi,
                            (
                                SELECT
                                count( employee_id )
                                FROM
                                employee_syncs
                                WHERE
                                department IS NOT NULL
                                AND end_date IS NULL
                                AND COALESCE ( sub_group, '' ) = COALESCE ( a.sub_group, '' )
                                AND COALESCE ( `group`, '' )= COALESCE ( a.`group`, '' )
                                AND COALESCE ( `department`, '' ) = COALESCE ( a.`department`, '' )
                                AND COALESCE ( `section`, '' ) = COALESCE ( a.`section`, '' )) AS actual,
                            COALESCE ((
                                SELECT
                                sum( quantity_male + quantity_female ) AS process
                                FROM
                                recruitments
                                WHERE
                                remark = 'Recruitment HR'
                                AND COALESCE ( sub_group, '' ) = COALESCE ( a.sub_group, '' )
                                AND COALESCE ( `group`, '' )= COALESCE ( a.`group`, '' )
                                AND COALESCE ( `department`, '' ) = COALESCE ( a.`department`, '' )
                                AND COALESCE ( `section`, '' ) = COALESCE ( a.`section`, '' )
                                AND COALESCE ( DATE_FORMAT(created_at,'%Y-%m'), '' ) = COALESCE ( DATE_FORMAT(a.`month`,'%Y-%m'), '' )),
                            0
                            ) AS proses
                            FROM
                            request_adds a
                            GROUP BY
                            `month`,
                            department,
                            `group`,
                            `section`,
                            sub_group
                            ) b
                        where DATE_FORMAT(`month`, '%Y-%m' ) = '".$bulan."'
                        GROUP BY
                        `month`");

                    $list_open = db::select("
                        SELECT DISTINCT
                        apr.request_id, DATE_FORMAT(r.created_at, '%Y-%m-%d') as created_at, r.start_date, u.name, r.remark, r.status_at,
                        r.position, r.department, r.employment_status, r.quantity_male, r.quantity_female, r.reason,
                        ( SELECT GROUP_CONCAT( a.approver_name ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id   ) as approval,
                        ( SELECT GROUP_CONCAT( COALESCE ( a.approved_at, '' ) ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id ) AS approved_at,
                        ( SELECT GROUP_CONCAT( a.approver_id ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as id,
                        ( SELECT GROUP_CONCAT( COALESCE(a.status,'') ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as status,
                        ( SELECT b.department FROM recruitments b WHERE b.request_id = apr.request_id  ) as department,
                        ( SELECT count(a.request_id) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as cnt,
                        ( SELECT count(a.`status`) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as app
                        FROM
                        `recruitment_approvals` as apr
                        left join recruitments as r on r.request_id = apr.request_id
                        left join users as u on u.id = r.created_by where DATE_FORMAT( r.created_at, '%Y-%m' ) = '".$bulan."' AND r.remark = 'Recruitment HR' AND r.status_at = 'Process Approval' AND department = '".$dept."'
                        ");
                    $list_close = db::select("
                        SELECT DISTINCT
                        apr.request_id, DATE_FORMAT(r.created_at, '%Y-%m-%d') as created_at, r.start_date, u.name, r.remark, r.status_at,
                        r.position, r.department, r.employment_status, r.quantity_male, r.quantity_female, r.reason,
                        ( SELECT GROUP_CONCAT( a.approver_name ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id   ) as approval,
                        ( SELECT GROUP_CONCAT( COALESCE ( a.approved_at, '' ) ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id ) AS approved_at,
                        ( SELECT GROUP_CONCAT( a.approver_id ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as id,
                        ( SELECT GROUP_CONCAT( COALESCE(a.status,'') ) FROM recruitment_approvals a WHERE a.request_id = apr.request_id  ) as status,
                        ( SELECT b.department FROM recruitments b WHERE b.request_id = apr.request_id  ) as department,
                        ( SELECT count(a.request_id) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as cnt,
                        ( SELECT count(a.`status`) FROM recruitment_approvals a WHERE a.request_id = apr.request_id) as app
                        FROM
                        `recruitment_approvals` as apr
                        left join recruitments as r on r.request_id = apr.request_id
                        left join users as u on u.id = r.created_by where DATE_FORMAT( r.created_at, '%Y-%m' ) = '".$bulan."' AND r.remark = 'Recruitment HR' AND r.status_at <> 'Process'
                        ");

                    $ct1 = db::select('select sum(quantity_male+quantity_female) as jm_ct1 from recruitments where status_at = "Test Potensi Akademik"');
                    $ct2 = db::select('select sum(quantity_male+quantity_female) as jm_ct2 from recruitments where status_at = "Test Wawancara"');
                    $ct3 = db::select('select sum(quantity_male+quantity_female) as jm_ct3 from recruitments where status_at = "Test Kesehatan"');

                    $induction = db::select("select sum(i.jumlah) as induction from ( select request_id, count(`status`) as jumlah from calon_karyawans where `status` = 'induction' group by request_id union all select request_id, count(`status`) as jumlah from calon_rekontraks where `status` = 'induction' group by request_id ) as i");

                    $magang = db::select("select sum(i.jumlah) as magang from ( select request_id, count(`status`) as jumlah from calon_karyawans where `status` = 'magang' group by request_id union all select request_id, count(`status`) as jumlah from calon_rekontraks where `status` = 'magang' group by request_id ) as i");

                    $list_magang = db::select("SELECT DISTINCT
                        apr.request_id, r.posisi, r.status as st,
                        DATE_FORMAT( r.created_at, '%Y-%m-%d' ) AS created_at,
                        r.department, r.section, r.`group`, r.sub_group, 
                        ( SELECT GROUP_CONCAT( a.approver_name ) FROM request_magang_approvals a WHERE a.request_id = apr.request_id ) AS approval,
                        ( SELECT GROUP_CONCAT( COALESCE ( a.approved_at, '' ) ) FROM request_magang_approvals a WHERE a.request_id = apr.request_id ) AS approved_at,
                        ( SELECT GROUP_CONCAT( a.approver_id ) FROM request_magang_approvals a WHERE a.request_id = apr.request_id ) AS id,
                        ( SELECT GROUP_CONCAT( COALESCE ( a.STATUS, '' ) ) FROM request_magang_approvals a WHERE a.request_id = apr.request_id ) AS status,
                        ( SELECT count( a.request_id ) FROM request_magang_approvals a WHERE a.request_id = apr.request_id ) AS cnt,
                        ( SELECT count( a.`status` ) FROM request_magang_approvals a WHERE a.request_id = apr.request_id ) AS app,
                        ( SELECT GROUP_CONCAT( a.approver_id ) FROM request_magang_approvals a WHERE a.request_id = apr.request_id ) AS approver_id
                        FROM
                        request_magang_approvals AS apr
                        LEFT JOIN request_magangs AS r ON r.request_id = apr.request_id
                        LEFT JOIN users AS u ON u.id = r.created_by 
                        WHERE
                        DATE_FORMAT( r.created_at, '%Y-%m' ) = '".$month_now."'
                        AND department = '".$dept."'
                        AND r.status != 'All Approve'");
                }

                $response = array(
                    'status' => true,
                    'grafik' => $grafik,
                    'list_open' => $list_open,
                    'list_close' => $list_close,
                    'ct1' => $ct1,
                    'ct2' => $ct2,
                    'ct3' => $ct3,
                    'induction' => $induction,
                    'magang'    => $magang,
                    'list_magang' => $list_magang
                );
                return Response::json($response);
            } catch (\Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage()
                );
                return Response::json($response);
            }
        }
//trial grafik
        public function indexTrialGrafik()
        {   
            $data_veteran = VeteranDataMaster::where('deleted_at', null)
            ->get();

            $title = 'Veteran Employee';
            $title_jp = 'コロナ調査報告';

            return view('human_resource.coba_grafik', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'data_veteran' => $data_veteran
            ))->with('page', 'Veteran Employee')->with('head','Veteran Employee');
        }

//veteran
        public function indexVeteranEmployee()
        {   
            $data_veteran = VeteranDataMaster::where('deleted_at', null)
            ->get();

            $title = 'Veteran Employee';
            $title_jp = 'コロナ調査報告';

            return view('human_resource.recruitment.veteran.index_veteran', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'data_veteran' => $data_veteran
            ))->with('page', 'Veteran Employee')->with('head','Veteran Employee');
        }

        public function SelectNikVeteran( Request $request)
        {
            try {
            // $emp = DB::SELECT("select employee_id, `name`, sub_group, `group`, section, department, position, grade_code from employee_syncs where
            // `employee_id` = '".$request->get('employee_id')."'
            // AND `end_date` IS NULL");

              //   $old_nik = '';
              //   if(count($request->get('old_nik'))){
              //       $old_niks = join(",",$request->get('old_nik'));
              //       $old_niks =  explode(",", $old_niks);
              //       for ($i=0; $i < count($old_niks); $i++) {
              //           $old_nik = $old_nik."'".$old_niks[$i]."'";
              //           if($i != (count($old_niks)-1)){
              //             $old_nik = $old_nik.',';
              //         }
              //     }
              //     $old_nikin = "where `old_nik` in (".$old_nik.") ";
              // }
              // else{
              //     $old_nikin = "";
              // }

              // var_dump($old_nikin);
              // die();

                // $ection = $request->get('section');
                // dd($section);

        // $dept = $request->get('dept');
        // $sec = $request->get('sec');
        // $group = $request->get('group');
                if ($request->get('nama') == null) {
                    $department = $request->get('department');
                    $mp = VeteranDataMaster::select('name')->where('department',$department)->get();
                    $response = array(
                        'status' => true,
                        'message' => 'Success',
                        'mp' => $mp,
                    );
                    return Response::json($response);
                }else{
                    $sub_group = $request->get('sub_group');
                    $dept = $request->get('dept');
                    $nama = $request->get('nama');

                    $employee = db::select("select old_nik, `name` from veteran_data_masters where department = '".$dept."'");

                    $data = db::select("select old_nik, `name`, address, no_whatsapp, department, section, `group`, sub_group, end_date from veteran_data_masters where `name` = '".$nama."'");
                    // dd($data);


                    $sec = db::select("select distinct section from position_code where department = '".$request->get('createDepartment')."' and section is not null");

                    $group = db::select("select distinct `group` from position_code where section = '".$request->get('sect_penempatan')."'");

                    $po = db::select('select sum(p.diff-p.req) as diff from (
                        SELECT
                        department,
                        section,
                        `group`,
                        sub_group,
                        con_emp_sync,
                        con_upload,
                        ( con_upload - con_emp_sync ) AS diff,
                        0 as req
                        FROM
                        (
                        SELECT
                        department,
                        section,
                        `group`,
                        sub_group,
                        SUM( con ) con_emp_sync,
                        SUM( count ) AS con_upload 
                        FROM
                        (
                        SELECT
                        department,
                        section,
                        `group`,
                        sub_group,
                        0 AS con,
                        count
                        FROM
                        `request_adds` 
                        WHERE
                        DATE_FORMAT( `month`, "%Y-%m" ) = DATE_FORMAT( NOW() + INTERVAL 1 MONTH, "%Y-%m" ) UNION ALL
                        SELECT
                        department,
                        section,
                        `group`,
                        sub_group,
                        count( employee_id ) AS con,
                        0 AS count 
                        FROM
                        employee_syncs 
                        WHERE
                        end_date IS NULL 
                        AND department IS NOT NULL 
                        GROUP BY
                        department,
                        section,
                        `group`,
                        sub_group 
                        ) AS semua 
                        GROUP BY
                        department,
                        section,
                        `group`,
                        sub_group 
                        ) semua2 
                        WHERE
                        con_emp_sync <> con_upload 
                        AND department = "'.$request->get('createDepartment').'"
                        UNION ALL
                        select department, create_section as section, create_group as `group`, create_sub_group as sub_group, 0 as con_emp_sync, 0 as con_upload, 0 as diff, 1 as req from recruitments where department = "'.$request->get('createDepartment').'" and DATE_FORMAT( start_date, "%Y-%m" ) = DATE_FORMAT( NOW() + INTERVAL 1 MONTH, "%Y-%m" )
                        ) as p where diff > 0
                        GROUP BY department, section, `group`, sub_group');

                // if (count($data) > 0) {
                    $response = array(
                        'status' => true,
                        'message' => 'Success',
                        'data' => $data,
                        'employee' => $employee,
                        'sec' => $sec,
                        'group' => $group,
                        'po' => $po
                    );
                    return Response::json($response);
                }
            }   
            catch (\Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage()
                );
                return Response::json($response);
            }
        }

        public function SelectSectionNew( Request $request)
        {
            try {
                $group = db::select("select distinct `group` from position_code where section = '".$request->get('sect_penempatan')."'");

                $po = db::select('select sum(p.diff-p.req) as diff from (
                    SELECT
                    department,
                    section,
                    `group`,
                    sub_group,
                    con_emp_sync,
                    con_upload,
                    ( con_upload - con_emp_sync ) AS diff,
                    0 as req
                    FROM
                    (
                    SELECT
                    department,
                    section,
                    `group`,
                    sub_group,
                    SUM( con ) con_emp_sync,
                    SUM( count ) AS con_upload 
                    FROM
                    (
                    SELECT
                    department,
                    section,
                    `group`,
                    sub_group,
                    0 AS con,
                    count
                    FROM
                    `request_adds` 
                    WHERE
                    DATE_FORMAT( `month`, "%Y-%m" ) = DATE_FORMAT( NOW() + INTERVAL 1 MONTH, "%Y-%m" ) UNION ALL
                    SELECT
                    department,
                    section,
                    `group`,
                    sub_group,
                    count( employee_id ) AS con,
                    0 AS count 
                    FROM
                    employee_syncs 
                    WHERE
                    end_date IS NULL 
                    AND department IS NOT NULL 
                    GROUP BY
                    department,
                    section,
                    `group`,
                    sub_group 
                    ) AS semua 
                    GROUP BY
                    department,
                    section,
                    `group`,
                    sub_group 
                    ) semua2 
                    WHERE
                    con_emp_sync <> con_upload 
                    AND department = "'.$request->get('dpt').'"
                    AND section = "'.$request->get('sect_penempatan').'"
                    UNION ALL
                    select department, create_section as section, create_group as `group`, create_sub_group as sub_group, 0 as con_emp_sync, 0 as con_upload, 0 as diff, 1 as req from recruitments where department = "'.$request->get('dpt').'" and section = "'.$request->get('sect_penempatan').'" and DATE_FORMAT( start_date, "%Y-%m" ) = DATE_FORMAT( NOW() + INTERVAL 1 MONTH, "%Y-%m" )
                    ) as p where diff > 0
                    GROUP BY department, section, `group`, sub_group');

                $response = array(
                    'status' => true,
                    'message' => 'Success',
                    'group' => $group,
                    'po' => $po
                );
                return Response::json($response);
            }   
            catch (\Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage()
                );
                return Response::json($response);
            }
        }

        public function SelectGroupNew( Request $request)
        {
            try {
                $sub_group = db::select("select distinct sub_group from position_code where `group` = '".$request->get('loc_penempatan')."'");

                $department = $request->get('dpt');
                $section = $request->get('sect');
                $group = $request->get('loc_penempatan');

                $po = db::select('select sum(p.diff-p.req) as diff from (
                    SELECT
                    department,
                    section,
                    `group`,
                    sub_group,
                    con_emp_sync,
                    con_upload,
                    ( con_upload - con_emp_sync ) AS diff,
                    0 as req
                    FROM
                    (
                    SELECT
                    department,
                    section,
                    `group`,
                    sub_group,
                    SUM( con ) con_emp_sync,
                    SUM( count ) AS con_upload 
                    FROM
                    (
                    SELECT
                    department,
                    section,
                    `group`,
                    sub_group,
                    0 AS con,
                    count
                    FROM
                    `request_adds` 
                    WHERE
                    DATE_FORMAT( `month`, "%Y-%m" ) = DATE_FORMAT( NOW() + INTERVAL 1 MONTH, "%Y-%m" ) UNION ALL
                    SELECT
                    department,
                    section,
                    `group`,
                    sub_group,
                    count( employee_id ) AS con,
                    0 AS count 
                    FROM
                    employee_syncs 
                    WHERE
                    end_date IS NULL 
                    AND department IS NOT NULL 
                    GROUP BY
                    department,
                    section,
                    `group`,
                    sub_group 
                    ) AS semua 
                    GROUP BY
                    department,
                    section,
                    `group`,
                    sub_group 
                    ) semua2 
                    WHERE
                    con_emp_sync <> con_upload 
                    AND department = "'. $department.'" AND section = "'.$section.'" AND `group` = "'.$group.'"
                    UNION ALL
                    select department, create_section as section, create_group as `group`, create_sub_group as sub_group, 0 as con_emp_sync, 0 as con_upload, 0 as diff, 1 as req from recruitments where department = "'. $department.'" AND section = "'.$section.'" AND `group` = "'.$group.'" AND DATE_FORMAT( start_date, "%Y-%m" ) = DATE_FORMAT( NOW() + INTERVAL 1 MONTH, "%Y-%m" )
                    ) as p where diff > 0
                    GROUP BY department, section, `group`, sub_group');

                $response = array(
                    'status' => true,
                    'message' => 'Success',
                    'sub_group' => $sub_group,
                    'po' => $po
                );
                return Response::json($response);
            }   
            catch (\Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage()
                );
                return Response::json($response);
            }
        }

        public function SelectSubGroupNew( Request $request)
        {
            try {
                $department = $request->get('dpt');
                $section = $request->get('sect');
                $group = $request->get('loc_penempatan');
                $sub_group = $request->get('sub_group');

                $po = db::select('select sum(p.diff-p.req) as diff from (
                    SELECT
                    department,
                    section,
                    `group`,
                    sub_group,
                    con_emp_sync,
                    con_upload,
                    ( con_upload - con_emp_sync ) AS diff,
                    0 as req
                    FROM
                    (
                    SELECT
                    department,
                    section,
                    `group`,
                    sub_group,
                    SUM( con ) con_emp_sync,
                    SUM( count ) AS con_upload 
                    FROM
                    (
                    SELECT
                    department,
                    section,
                    `group`,
                    sub_group,
                    0 AS con,
                    count
                    FROM
                    `request_adds` 
                    WHERE
                    DATE_FORMAT( `month`, "%Y-%m" ) = DATE_FORMAT( NOW() + INTERVAL 1 MONTH, "%Y-%m" ) UNION ALL
                    SELECT
                    department,
                    section,
                    `group`,
                    sub_group,
                    count( employee_id ) AS con,
                    0 AS count 
                    FROM
                    employee_syncs 
                    WHERE
                    end_date IS NULL 
                    AND department IS NOT NULL 
                    GROUP BY
                    department,
                    section,
                    `group`,
                    sub_group 
                    ) AS semua 
                    GROUP BY
                    department,
                    section,
                    `group`,
                    sub_group 
                    ) semua2 
                    WHERE
                    con_emp_sync <> con_upload 
                    AND department = "'. $department.'" AND section = "'.$section.'" AND `group` = "'.$group.'" AND sub_group = "'.$sub_group.'"
                    UNION ALL
                    select department, create_section as section, create_group as `group`, create_sub_group as sub_group, 0 as con_emp_sync, 0 as con_upload, 0 as diff, 1 as req from recruitments where department = "'. $department.'" AND section = "'.$section.'" AND `group` = "'.$group.'" AND sub_group = "'.$sub_group.'" AND DATE_FORMAT( start_date, "%Y-%m" ) = DATE_FORMAT( NOW() + INTERVAL 1 MONTH, "%Y-%m" )
                    ) as p where diff > 0
                    GROUP BY department, section, `group`, sub_group');

                $response = array(
                    'status' => true,
                    'message' => 'Success',
                    'po' => $po
                );
                return Response::json($response);
            }   
            catch (\Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage()
                );
                return Response::json($response);
            }
        }

        public function fetchVeteranEmployee(Request $request)
        {
            try {
                $survey = DB::SELECT("
                    SELECT
                    SUM( a.bersedia ) AS bersedia,
                    a.department as department
                    FROM
                    (
                        SELECT
                        count( ympimis.veteran_employees.old_nik ) AS bersedia,
                        department
                        FROM
                        ympimis.veteran_employees
                        GROUP BY
                        veteran_employees.department 
                        ) a
                    WHERE a.department != ''
                    GROUP BY
                    a.department
                    ");

                $response = array(
                    'status' => true,
                    'survey' => $survey
                // 'nilai' => $nilai
                );
                return Response::json($response);
            } catch (\Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage()
                );
                return Response::json($response);
            }
        }

        public function fetchTrialGrafik(Request $request)
        {
            try {
        // $survey = DB::SELECT("
        //     SELECT
        //     SUM( a.bersedia ) AS bersedia,
        //     a.department as department
        //     FROM
        //     (
        //         SELECT
        //         count( ympimis.veteran_employees.old_nik ) AS bersedia,
        //         department
        //         FROM
        //         ympimis.veteran_employees
        //         GROUP BY
        //         veteran_employees.department 
        //         ) a
        //     WHERE a.department != ''
        //     GROUP BY
        //     a.department
        //     ");
                $date = [date('Y-m', strtotime("+1 months")), date('Y-m', strtotime("+2 months")), date('Y-m', strtotime("+3 months")), date('Y-m', strtotime("+4 months")), date('Y-m', strtotime("+5 months"))];
                $employee = db::select('select count(employee_id) as jumlah from employee_syncs where end_date is null');
                $data = db::select('select DATE_FORMAT(`month`, "%Y-%m") as bulan, sum(count) as jumlah from request_adds group by `month`');
                $end_date = db::select('SELECT DATE_FORMAT(end_date,"%Y-%m") as end_date, count( id ) as jumlah FROM plan_employee_contracts where remark = "2" or remark is null GROUP BY DATE_FORMAT(end_date,"%Y-%m")');
        // $perpanjang = db::select('SELECT DATE_FORMAT(end_date,"%Y-%m") as perpanjang, count( id ) as jumlah FROM plan_employee_contracts where remark = "1" GROUP BY DATE_FORMAT(end_date,"%Y-%m")');
                $resumes = db::select('select DATE_FORMAT( end_date, "%Y-%m" ) as end_date, employee_id, `name` from plan_employee_contracts where DATE_FORMAT( end_date, "%Y-%m" ) = DATE_FORMAT( now() + INTERVAL 2 MONTH, "%Y-%m" )');

                $response = array(
                    'status' => true,
                    'data' => $data,
                    'date' => $date,
                    'employee' => $employee,
                    'end_date' => $end_date,
                    'resumes' => $resumes,
            // 'perpanjang' => $perpanjang
                // 'nilai' => $nilai
                );
                return Response::json($response);
            } catch (\Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage()
                );
                return Response::json($response);
            }
        }

        public function fetchVeteranEmployeeDetail(Request $request)
        {
            try {
                $answer = $request->get('answer');
                $dept = $request->get('department');

                if ($answer == "Bersedia") {
                    $survey = db::select("SELECT old_nik, `name`, address, department, section, `group`, sub_group, proces, remark FROM veteran_employees WHERE department = '".$dept."' ORDER BY name ASC");
                }

                $response = array(
                    'status' => true,
                    'survey' => $survey
                );
                return Response::json($response);
            } catch (\Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage()
                );
                return Response::json($response);
            }
        }

        public function inputVeteranEmployee(Request $request)
        {
            try {
                $id = Auth::user()->username;

                $old_nik = $request->get('old_nik');
                $name = $request->get('name');
                $address = $request->get('address');
                $no_whatsapp = $request->get('no_whatsapp');
                $department = $request->get('department');
                $section = $request->get('section');
                $group = $request->get('group');
                $sub_group = $request->get('sub_group');
                $proces = $request->get('proces');
                $end_date = $request->get('end_date');
                $remark = $request->get('remark');

                $request = new VeteranEmployee([
                    'old_nik' => $old_nik,
                    'name' => $name,
                    'address' => $address,
                    'no_whatsapp' => $no_whatsapp,
                    'department' => $department,
                    'section' => $section,
                    'group' => $group,
                    'sub_group' => $sub_group,
                    'proces' => $proces,
                    'end_date' => $end_date,
                    'remark' => $remark
                ]);
                $request->save();

                $response = array(
                    'status' => true,
                    'request' => $request
                );
                return Response::json($response);
            } catch (\Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage()
                );
                return Response::json($response);
            }
        }

    //monitoring kebutuhan MP
        public function IndexKebutuhanMp(){
            $emp = EmployeeSync::where('employee_id', Auth::user()->username)
            ->select('employee_id', 'department', 'position')
            ->first();

            $section = db::select("SELECT DISTINCT section FROM position_code WHERE department = '".$emp->department."'");
            return view('human_resource.monitoring.m_kebutuhan', array(
                'emp'      => $emp,
                'section'      => $section
            ))->with('page', 'Human Resource')->with('head', 'Human Resource');      
        }

        public function InputKebutuhanMp(Request $request)
        {
            try {
                $id = Auth::user()->username;
                $bulan = date('Y-m-01');

                $month = $request->get('bulan');
                $department = $request->get('department');
                $section = $request->get('section');
                $jumlah = $request->get('jumlah');
                $remark = $request->get('remark');

                $input = new RequestAdds([
                    'month' => $month,
                    'department' => $department,
                    'section' => $section,
                    'count' => $jumlah,
                    'remark' => $remark
                ]);
                $input->save();

                $response = array(
                    'status' => true,
                    'input' => $input
                );
                return Response::json($response);
            } catch (\Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage()
                );
                return Response::json($response);
            }
        }

        public function UploadKebutuhanMp(Request $request)
        {
            $filename = "";
            $file_destination = 'data_file/hr_manpower/kebutuhan_mp';

            if (count($request->file('newAttachment')) > 0) {
                try{
                    $section = '';
                    if ($request->get('section') == 'INDIRECT') {
                        $section = null;
                    }else{    
                        $section = $request->get('section');
                    }

                    $file = $request->file('newAttachment');
                    $filename = 'Kebutuhan_MP_'.date('YmdHisa').'.'.$request->input('extension');
                    $file->move($file_destination, $filename);

                    $excel = 'data_file/hr_manpower/kebutuhan_mp/' . $filename;
                    $rows = Excel::load($excel, function($reader) {
                        $reader->noHeading();
                        $reader->skipRows(1);
                    })->toObject();

            // $dpt = EmployeeSync::where('employee_id', Auth::user()->username)
            // ->select('department')
            // ->first();

                    for ($i=0; $i < count($rows); $i++) {
                        $data = new RequestAdds(
                            [
                                'month' => $rows[$i][0],
                                'count' => $rows[$i][4],
                                'remark' => $rows[$i][5],
                                'created_by' => Auth::id()
                            ]
                        );
                        $data->save();
                    }

            // $mail_to = Approver::where('department', $dpt->department)
            // ->select('approver_email')
            // ->first();

            // $mail = [];
            // array_push($mail, $mail_to->approver_email);

            // $count = db::select('SELECT
            // bulan,
            // ABS((
            // sum( aktual )- sum( forecast )- sum( request ))+ sum( kontrak )) AS count,
            // department
            // FROM
            // (
            // SELECT
            // bulan,
            // aktual,
            // 0 AS forecast,
            // 0 AS kontrak,
            // 0 AS request,
            // department
            // FROM
            // (
            // SELECT
            // DATE_FORMAT( week_date, "%Y-%m" ) AS bulan 
            // FROM
            // weekly_calendars 
            // WHERE
            // DATE_FORMAT( week_date, "%Y-%m" ) >= DATE_FORMAT( now(), "%Y-%m" ) 
            // AND DATE_FORMAT( week_date, "%Y-%m" ) < DATE_FORMAT( NOW() + INTERVAL 5 MONTH, "%Y-%m" ) 
            // GROUP BY
            // bulan 
            // ) AS weekly
            // CROSS JOIN (
            // SELECT
            // count( employee_id ) AS aktual,
            // department
            // FROM
            // employee_syncs 
            // WHERE
            // end_date IS NULL 
            // AND department = "'.$dpt->department.'" 
            // GROUP BY
            // department
            // ) AS aktual 
            // GROUP BY
            // bulan,
            // department,
            // aktual UNION ALL
            // SELECT
            // DATE_FORMAT( `month`, "%Y-%m" ) AS bulan,
            // 0 AS aktual,
            // sum( count ) AS forecast,
            // 0 AS kontrak,
            // 0 AS request,
            // department
            // FROM
            // request_adds 
            // WHERE
            // DATE_FORMAT( `month`, "%Y-%m" ) >= DATE_FORMAT( now(), "%Y-%m" ) 
            // AND DATE_FORMAT( `month`, "%Y-%m" ) < DATE_FORMAT( NOW() + INTERVAL 5 MONTH, "%Y-%m" ) 
            // AND department = "'.$dpt->department.'" 
            // GROUP BY
            // bulan,
            // department UNION ALL
            // SELECT
            // DATE_FORMAT( end_date, "%Y-%m" ) AS bulan,
            // 0 AS aktual,
            // 0 AS forecast,
            // count( id ) AS kontrak,
            // 0 AS request,
            // department
            // FROM
            // plan_employee_contracts 
            // WHERE
            // DATE_FORMAT( end_date, "%Y-%m" ) >= DATE_FORMAT( now(), "%Y-%m" ) 
            // AND DATE_FORMAT( end_date, "%Y-%m" ) < DATE_FORMAT( NOW() + INTERVAL 5 MONTH, "%Y-%m" ) 
            // AND department = "'.$dpt->department.'" 
            // GROUP BY
            // bulan,
            // department UNION ALL
            // SELECT
            // DATE_FORMAT( start_date, "%Y-%m" ) AS bulan,
            // 0 AS aktual,
            // 0 AS forecast,
            // 0 AS kontrak,
            // sum( quantity_male + quantity_female ) AS request,
            // department
            // FROM
            // recruitments 
            // WHERE
            // DATE_FORMAT( start_date, "%Y-%m" ) >= DATE_FORMAT( now(), "%Y-%m" ) 
            // AND DATE_FORMAT( start_date, "%Y-%m" ) < DATE_FORMAT( NOW() + INTERVAL 5 MONTH, "%Y-%m" ) 
            // AND department = "'.$dpt->department.'" 
            // GROUP BY
            // bulan,
            // department
            // ) AS `all` 
            // WHERE
            // bulan = DATE_FORMAT(NOW() + INTERVAL 1 MONTH,"%Y-%m")
            // GROUP BY
            // bulan,
            // department
            // HAVING
            // count > 0');

            // $data = db::select('SELECT
            // bulan,
            // ABS((
            // sum( aktual )- sum( forecast )- sum( request ))+ sum( kontrak )) AS count,
            // department,
            // section,
            // `group`,
            // sub_group 
            // FROM
            // (
            // SELECT
            // bulan,
            // aktual,
            // 0 AS forecast,
            // 0 AS kontrak,
            // 0 AS request,
            // department,
            // section,
            // `group`,
            // sub_group 
            // FROM
            // (
            // SELECT
            // DATE_FORMAT( week_date, "%Y-%m" ) AS bulan 
            // FROM
            // weekly_calendars 
            // WHERE
            // DATE_FORMAT( week_date, "%Y-%m" ) >= DATE_FORMAT( now(), "%Y-%m" ) 
            // AND DATE_FORMAT( week_date, "%Y-%m" ) < DATE_FORMAT( NOW() + INTERVAL 5 MONTH, "%Y-%m" ) 
            // GROUP BY
            // bulan 
            // ) AS weekly
            // CROSS JOIN (
            // SELECT
            // count( employee_id ) AS aktual,
            // department,
            // section,
            // `group`,
            // sub_group 
            // FROM
            // employee_syncs 
            // WHERE
            // end_date IS NULL 
            // AND department = "'.$dpt->department.'" 
            // GROUP BY
            // department,
            // section,
            // `group`,
            // sub_group 
            // ) AS aktual 
            // GROUP BY
            // bulan,
            // department,
            // section,
            // `group`,
            // sub_group,
            // aktual UNION ALL
            // SELECT
            // DATE_FORMAT( `month`, "%Y-%m" ) AS bulan,
            // 0 AS aktual,
            // sum( count ) AS forecast,
            // 0 AS kontrak,
            // 0 AS request,
            // department,
            // section,
            // `group`,
            // sub_group 
            // FROM
            // request_adds 
            // WHERE
            // DATE_FORMAT( `month`, "%Y-%m" ) >= DATE_FORMAT( now(), "%Y-%m" ) 
            // AND DATE_FORMAT( `month`, "%Y-%m" ) < DATE_FORMAT( NOW() + INTERVAL 5 MONTH, "%Y-%m" ) 
            // AND department = "'.$dpt->department.'" 
            // GROUP BY
            // bulan,
            // department,
            // section,
            // `group`,
            // sub_group UNION ALL
            // SELECT
            // DATE_FORMAT( end_date, "%Y-%m" ) AS bulan,
            // 0 AS aktual,
            // 0 AS forecast,
            // count( id ) AS kontrak,
            // 0 AS request,
            // department,
            // section,
            // `group`,
            // sub_group 
            // FROM
            // plan_employee_contracts 
            // WHERE
            // DATE_FORMAT( end_date, "%Y-%m" ) >= DATE_FORMAT( now(), "%Y-%m" ) 
            // AND DATE_FORMAT( end_date, "%Y-%m" ) < DATE_FORMAT( NOW() + INTERVAL 5 MONTH, "%Y-%m" ) 
            // AND department = "'.$dpt->department.'" 
            // GROUP BY
            // bulan,
            // department,
            // section,
            // `group`,
            // sub_group UNION ALL
            // SELECT
            // DATE_FORMAT( start_date, "%Y-%m" ) AS bulan,
            // 0 AS aktual,
            // 0 AS forecast,
            // 0 AS kontrak,
            // sum( quantity_male + quantity_female ) AS request,
            // department,
            // section,
            // `group`,
            // sub_group 
            // FROM
            // recruitments 
            // WHERE
            // DATE_FORMAT( start_date, "%Y-%m" ) >= DATE_FORMAT( now(), "%Y-%m" ) 
            // AND DATE_FORMAT( start_date, "%Y-%m" ) < DATE_FORMAT( NOW() + INTERVAL 5 MONTH, "%Y-%m" ) 
            // AND department = "'.$dpt->department.'" 
            // GROUP BY
            // bulan,
            // department,
            // section,
            // `group`,
            // sub_group 
            // ) AS `all` 
            // WHERE
            // bulan = DATE_FORMAT(NOW() + INTERVAL 1 MONTH,"%Y-%m")
            // GROUP BY
            // bulan,
            // department,
            // section,
            // `group`,
            // sub_group 
            // HAVING
            // count > 0');

          //   if ($count[0]->count != '0') {
          //     Mail::to($mail)
          //     ->bcc(['ympi-mis-ML@music.yamaha.com'])
          //     ->send(new SendEmail($data, 'request_foreman'));
          // }

                    $response = array(
                        'status' => true,
                        'message' => 'Kebutuhan MP Berhasil Di Upload.'
                    );
                    return Response::json($response);

                }
                catch(\Exception $e){
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                    return Response::json($response);
                }
            }
            else{
                $response = array(
                    'status' => false,
                    'message' => 'Please select file to attach'
                );
                return Response::json($response);
            }
        }

        public function UploadMpInterview(Request $request){
            $filename = "";
            $file_destination = 'data_file/hr_manpower/upload_interview';
            if (count($request->file('newAttachment')) > 0) {
                try{
                    $file = $request->file('newAttachment');
                    $filename = 'Upload_interview_MP_'.date('YmdHisa').'.'.$request->input('extension');
                    $file->move($file_destination, $filename);

                    $excel = 'data_file/hr_manpower/upload_interview/' . $filename;
                    $rows = Excel::load($excel, function($reader) {
                        $reader->noHeading();
                        $reader->skipRows(1);
                    })->toObject();

                    for ($i=0; $i < count($rows); $i++) {
                        $data = db::connection('ympimis_2')
                        ->table('hr_interviews')
                        ->insert([
                            'nik' => $rows[$i][0],
                            'nama' => $rows[$i][1],
                            'created_by' => Auth::id(),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                    $response = array(
                        'status' => true,
                        'message' => 'Data Interview MP Berhasil Diupload.'
                    );
                    return Response::json($response);

                }
                catch(\Exception $e){
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                    return Response::json($response);
                }
            }
            else{
                $response = array(
                    'status' => false,
                    'message' => 'Pastikan File Lampiran Ada'
                );
                return Response::json($response);
            }
        }

        public function fetchMpInterview(){
            $resumes = db::connection('ympimis_2')->select('select id, nik, nama from hr_interviews order by id asc');
            $response = array(
                'status' => true,
                'resumes' => $resumes
            );
            return Response::json($response);
        }

        public function fetchMpMagang(){
            $resumes = db::connection('ympimis_2')->select('select id, nik, nama from hr_internsips order by id asc');
            $response = array(
                'status' => true,
                'resumes' => $resumes
            );
            return Response::json($response);
        }

        public function fetchMpStock(){
            $resumes = db::connection('ympimis_2')->select('select id, nik, nama from hr_stocks order by id asc');
            $response = array(
                'status' => true,
                'resumes' => $resumes
            );
            return Response::json($response);
        }

        public function UpdateMpMagang(Request $request){
            try{
                $id = $request->get('id');
                $select = db::connection('ympimis_2')->select('select id, nik, nama from hr_interviews where id = "'.$id.'"');
                $data = db::connection('ympimis_2')->table('hr_internsips')->insert([
                    'nik' => $select[0]->nik,
                    'nama' => $select[0]->nama,
                    'created_by' => Auth::id(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $delete = db::connection('ympimis_2')->delete('delete from hr_interviews where id = "'.$id.'"');
                $response = array(
                    'status' => true,
                    'message' => 'Karyawan Ditambahkan Ke Magang.'
                );
                return Response::json($response);
            }
            catch(\Exception $e){
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }

        public function UpdateMpStock(Request $request){
            try{
                $id = $request->get('id');
                $select = db::connection('ympimis_2')->select('select id, nik, nama from hr_internsips where id = "'.$id.'"');
                $data = db::connection('ympimis_2')->table('hr_stocks')->insert([
                    'nik' => $select[0]->nik,
                    'nama' => $select[0]->nama,
                    'created_by' => Auth::id(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $delete = db::connection('ympimis_2')->delete('delete from hr_internsips where id = "'.$id.'"');
                $response = array(
                    'status' => true,
                    'message' => 'Stock Karyawan Baru Bertambah.'
                );
                return Response::json($response);
            }
            catch(\Exception $e){
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }

        public function FetchChartKebutuhanMp(){
            $bulan = date('m');
            $month = date('F');
        // $jsonData = db::select('SELECT
        //     DATE_FORMAT( a.`month`, "%b" ) AS `month`,
        //     a.direct,
        //     a.indirect,
        //     a.staff,
        //     COALESCE ( a.jumlah, 0 ) AS jumlah,
        //     COALESCE ( a.forecast, 0 ) AS forecast,
        //     IF
        //     (
        //         COALESCE ( a.datas_next, 0 ) - COALESCE ( a.forecast, 0 ) = a.datas_next,
        //         0,
        //         COALESCE ( a.datas_next, 0 ) - COALESCE ( a.forecast, 0 )) AS diff,
        //     COALESCE ( a.t, 0 ) AS tambah,
        //     COALESCE ( a.k, 0 ) AS kurang,
        //     COALESCE ( COALESCE ( a.t, 0 ) - COALESCE ( a.k, 0 ), 0 ) AS recruitment
        //     FROM
        //     (
        //         SELECT
        //         rd.`month`,
        //         SUM( rd.direct + rd.indirect + rd.staff ) AS jumlah,
        //         (
        //             SELECT
        //             SUM( rd1.direct + rd1.indirect + rd1.staff ) as p
        //             FROM
        //             recruitment_datas AS rd1
        //             WHERE
        //             rd1.`month` = DATE_FORMAT( rd.`month` - INTERVAL 1 MONTH, "%Y-%m-%d" )
        //             GROUP BY
        //             rd1.`month`
        //         ) AS datas_next,
        //         rf.forecast,
        //         rd.direct,
        //         rd.indirect,
        //         rd.staff,
        //         ( SELECT sum( ra.count ) FROM request_adds AS ra WHERE ra.`month` = rd.`month`) AS t,
        //         ( SELECT sum( rc.count ) FROM request_cuts AS rc WHERE rc.`month` = rd.`month`) AS k
        //         FROM
        //         recruitment_datas AS rd
        //         LEFT JOIN recruitment_forecasts AS rf ON rf.`month` = rd.`month`
        //         GROUP BY
        //         rd.`month`, rf.forecast, rd.direct, rd.indirect, rd.staff, t, k
        //     ) a
        //     GROUP BY
        //     a.`month`, a.direct, a.indirect, a.staff, jumlah, kurang, recruitment, a.forecast, a.datas_next, diff, tambah');

        // $jsonData = db::select('SELECT
        //     DATE_FORMAT( a.`month`, "%b" ) AS `month`, b.forecasts, a.produksi, (a.produksi - c.rekrut) as req, c.rekrut, d.aktual, (d.aktual - a.produksi) as rec
        //     FROM
        //     ( SELECT `month`, SUM( count ) AS produksi FROM request_adds GROUP BY `month` ) AS a
        //     LEFT JOIN ( SELECT period, SUM( forecast_mp ) AS forecasts FROM manpower_forecasts GROUP BY period ) AS b ON a.`month` = b.period
        //     LEFT JOIN ( select start_date, COUNT(remark) as rekrut from recruitments WHERE remark = "Recruitment HR" GROUP BY start_date) as c on DATE_FORMAT( a.`month`, "%Y-%m" ) = DATE_FORMAT( c.start_date, "%Y-%m" )
        //     LEFT JOIN ( select period, COUNT(period) as aktual from employee_histories GROUP BY period ) as d on DATE_FORMAT( a.`month`, "%Y-%m" ) = DATE_FORMAT( d.period, "%Y-%m" )
        //     ORDER BY a.`month` ASC');

    // $jsonData = db::select('SELECT
    //     DATE_FORMAT( a.`month`, "%b" ) AS `month`,
    //     b.forecasts,
    //     aktual_sync AS mp_aktual,
    //     a.produksi AS request_produksi,
    //     COALESCE ( c.rekrut, 0 ) AS diterima_hr,
    //     ( aktual_sync - a.produksi ) AS hr_recruitment 
    //     FROM
    //     (
    //     SELECT
    //     `month`,
    //     SUM( count ) AS produksi,
    //     ( SELECT count( employee_id ) FROM employee_syncs WHERE employee_id LIKE "%PI%" AND end_date IS NULL GROUP BY DATE_FORMAT(updated_at, "%Y-%m")) AS aktual_sync 
    //     FROM
    //     request_adds 
    //     GROUP BY
    //     `month`, aktual_sync
    //     ) AS a
    //     LEFT JOIN ( SELECT period, SUM( forecast_mp ) AS forecasts FROM manpower_forecasts GROUP BY period ) AS b ON a.`month` = b.period
    //     LEFT JOIN ( SELECT start_date, COUNT( remark ) AS rekrut FROM recruitments WHERE remark = "Recruitment HR" ) AS c ON DATE_FORMAT( a.`month`, "%Y-%m" ) = DATE_FORMAT( c.start_date, "%Y-%m" )
    //     LEFT JOIN ( SELECT period, COUNT( period ) AS aktual FROM employee_histories WHERE Emp_no LIKE "%PI%" GROUP BY period ) AS d ON DATE_FORMAT( a.`month`, "%Y-%m" ) = DATE_FORMAT( d.period, "%Y-%m" ) 
    //     ORDER BY
    //     a.`month` ASC
    //     ');

    // $jml_recruit = db::select('SELECT sum( count ) as jumlah_recruit FROM request_adds where DATE_FORMAT( `month`, "%m" ) = "'.$bulan.'"');

    // $table = db::select('select DATE_FORMAT( `month`, "%M" ) as `month`, department, section, count from request_adds where DATE_FORMAT( `month`, "%m" ) = "'.$bulan.'"');
            $now = date('Y-m');

            $emp_end = db::connection('ympimis_2')->select('SELECT
                count( id ) AS jumlah,
                periode_end_contract as bulan
                FROM
                endcontract_logs 
                GROUP BY
                periode_end_contract');

            $emp_now = db::select('SELECT
                count( employee_id ) AS jumlah,
                "'.$now.'" as bulan
                FROM
                employee_syncs 
                WHERE
                end_date IS NULL');

    //disini ya

            $response = array(
                'status' => true,
        // 'jsonData' => $jsonData,
        // 'jml_recruit' => $jml_recruit,
        // 'table' => $table,
        // 'month' => $month,
                'emp_end' => $emp_end,
                'emp_now' => $emp_now
            );
            return Response::json($response);
        }

        public function fetchGrafikDetail(Request $request)
        {
            try {
                $answer = $request->get('answer');
                $month = $request->get('month');
                $dept = $request->get('dept');
        // $dept = EmployeeSync::where('employee_id', Auth::user()->username)
        // ->select('employee_id', 'department')
        // ->first();

                if ($answer == "Data Forecast") {
                    if ($dept == null) {
                        $detail = db::select('SELECT
                            IF(section is not null, section, "Direct") as section,
                            sum( count ) AS count 
                            FROM
                            request_adds 
                            WHERE
                            DATE_FORMAT( `month`, "%Y-%m" ) = "2022-07" 
                            AND count <> "0" 
                            GROUP BY
                            section 
                            ORDER BY
                            section ASC');
                    }else{
                        $detail = db::select('select department, section, `group`, sub_group, sum(count) as count from request_adds where department = "'.$dept.'" and DATE_FORMAT( `month`, "%Y-%m") = "'.$month.'" and count <> "0" group by department, section, `group`, sub_group');
                    }
                }
                if ($answer == "Request") {
                    $detail = db::select('select department, create_section as section, create_group as `group`, create_sub_group as sub_group, sum(quantity_male + quantity_female) as count from recruitments where department = "'.$dept.'" remark = "Recruitment HR" and status_at = "Process" group by department, create_section, create_group, create_sub_group');
                }
                if ($answer == "Proses Rekrut") {
                    $detail = db::select('select department, section, `group`, sub_group, sum(quantity_male + quantity_female) as count from recruitments where department = "'.$dept.'" remark = "Recruitment HR" group by department, section, `group`, sub_group');
                }
                if ($answer == "Habis Kontrak") {
                    $detail = db::select('select department, section, `group`, sub_group, count(department) as count from plan_employee_contracts where department = "'.$dept.'" and DATE_FORMAT(end_date,"%Y-%m") = "'.$month.'" group by department, section, `group`, sub_group');
                }
                if ($answer == "Data Sunfish") {
                    if ($dept == null) {
                        $detail = db::select('SELECT
                            count( employee_id ) AS count,
                            IF
                            (
                                IF
                                ( section IS NULL, department, section ) IS NULL,
                                "Top Management",
                                IF
                                ( section IS NULL, CONCAT( "Manager ", department ), section )) AS section 
                            FROM
                            employee_syncs 
                            WHERE
                            end_date IS NULL 
                            GROUP BY
                            department,
                            section 
                            ORDER BY
                            section ASC');
                    }else{
                        $detail = db::select('SELECT department, section, count( employee_id ) as count FROM employee_syncs WHERE end_date IS NULL AND department = "'.$dept.'" GROUP BY department, section');
                    }
                }
                if ($answer == 'Rekrut HR') {
                    $detail = db::select("SELECT
                        bulan,
                        sum( forecast ) AS forecast,
                        sum( aktual ) AS aktual,
                        sum( kontrak ) AS kontrak,
                        sum( request ) AS request,
                        sum( forecast )-(
                            sum( aktual )- sum( kontrak ))- sum( request ) AS count,
                        department, section, `group`, sub_group
                        FROM
                        (
                            SELECT
                            '".$month."' AS bulan,
                            count( employee_id ) AS aktual,
                            0 AS forecast,
                            0 AS kontrak,
                            0 AS request,
                            department, section, `group`, sub_group
                            FROM
                            employee_syncs
                            WHERE
                            end_date is null and department = '".$dept."'
                            GROUP BY
                            bulan, department, section, `group`, sub_group UNION ALL
                            SELECT
                            DATE_FORMAT( `month`, '%Y-%m' ) AS bulan,
                            0 AS aktual,
                            sum( count ) AS forecast,
                            0 AS kontrak,
                            0 AS request,
                            department, section, `group`, sub_group
                            FROM
                            request_adds 
                            WHERE
                            DATE_FORMAT( `month`, '%Y-%m' ) >= DATE_FORMAT( now(), '%Y-%m' ) 
                            AND DATE_FORMAT( `month`, '%Y-%m' ) < DATE_FORMAT( NOW() + INTERVAL 5 MONTH, '%Y-%m' ) 
                            AND department = '".$dept."' 
                            GROUP BY
                            bulan, department, section, `group`, sub_group UNION ALL
                            SELECT
                            DATE_FORMAT( end_date, '%Y-%m' ) AS bulan,
                            0 AS aktual,
                            0 AS forecast,
                            count( id ) AS kontrak,
                            0 AS request,
                            department, section, `group`, sub_group
                            FROM
                            plan_employee_contracts 
                            WHERE
                            DATE_FORMAT( end_date, '%Y-%m' ) >= DATE_FORMAT( now(), '%Y-%m' ) 
                            AND DATE_FORMAT( end_date, '%Y-%m' ) < DATE_FORMAT( NOW() + INTERVAL 5 MONTH, '%Y-%m' ) 
                            AND department = '".$dept."' 
                            GROUP BY
                            bulan, department, section, `group`, sub_group UNION ALL
                            SELECT
                            DATE_FORMAT( start_date, '%Y-%m' ) AS bulan,
                            0 AS aktual,
                            0 AS forecast,
                            0 AS kontrak,
                            sum( quantity_male + quantity_female ) AS request,
                            department, section, `group`, sub_group
                            FROM
                            recruitments 
                            WHERE
                            DATE_FORMAT( start_date, '%Y-%m' ) >= DATE_FORMAT( now(), '%Y-%m' ) 
                            AND DATE_FORMAT( start_date, '%Y-%m' ) < DATE_FORMAT( NOW() + INTERVAL 5 MONTH, '%Y-%m' ) 
                            AND department = '".$dept."' 
                            GROUP BY
                            bulan, department, section, `group`, sub_group 
                            ) AS `all` 
                            WHERE bulan = '".$month."'
                            GROUP BY
                            bulan, department, section, `group`, sub_group having count = 1");
                    }
                    if ($answer == "Direkrut") {                    
                        $detail = db::select("SELECT
                            b.`month`,
                            b.department,
                            b.`section`,
                            b.`group`,
                            b.sub_group,
                            CONCAT(
                                COALESCE ( department, '' ),COALESCE ( `section`, '' ),COALESCE ( `group`, '' ),COALESCE ( `sub_group`, '' )),
                            sum( b.produksi ) as foreman_upload,
                            sum( b.actual ) as mp_aktual,
                            sum( b.proses ) as mp_proses,
                            b.produksi - b.actual - b.proses as count
                            FROM
                            (
                                SELECT
                                `month`,
                                department,
                                `section`,
                                `group`,
                                sub_group,
                                SUM( count ) AS produksi,
                                (
                                    SELECT
                                    count( employee_id )
                                    FROM
                                    employee_syncs
                                    WHERE
                                    department = '".$dept."'
                                    AND end_date IS NULL
                                    AND COALESCE ( sub_group, '' ) = COALESCE ( a.sub_group, '' )
                                    AND COALESCE ( `group`, '' )= COALESCE ( a.`group`, '' )
                                    AND COALESCE ( `department`, '' ) = COALESCE ( a.`department`, '' )
                                    AND COALESCE ( `section`, '' ) = COALESCE ( a.`section`, '' )) AS actual,
                                    COALESCE ((
                                    SELECT
                                    sum( quantity_male + quantity_female ) AS process
                                    FROM
                                    recruitments
                                    WHERE
                                    remark = 'Recruitment HR'
                                    AND COALESCE ( create_sub_group, '' ) = COALESCE ( a.sub_group, '' )
                                    AND COALESCE ( `create_group`, '' )= COALESCE ( a.`group`, '' )
                                    AND COALESCE ( `department`, '' ) = COALESCE ( a.`department`, '' )
                                    AND COALESCE ( `create_section`, '' ) = COALESCE ( a.`section`, '' )
                                    AND COALESCE ( DATE_FORMAT(created_at,'%Y-%m'), '' ) = COALESCE ( DATE_FORMAT(a.`month`,'%Y-%m'), '' )),
                                    0
                                    ) AS proses
                                    FROM
                                    request_adds a
                                    GROUP BY
                                    `month`,
                                    department,
                                    `group`,
                                    `section`,
                                    sub_group
                                    ) b
                                    where DATE_FORMAT( `month`, '%Y-%m') = '".$month."' and b.department = '".$dept."'
                                    GROUP BY
                                    `month`,
                                    department,
                                    `group`,
                                    `section`,
                                    sub_group,
                                    produksi, actual, proses");
                            }
                            else{

                            }

                            $response = array(
                                'status' => true,
                                'detail' => $detail,
                                'answer' => $answer,
                                'month' => $month
                            );
                            return Response::json($response);
                        } catch (\Exception $e) {
                            $response = array(
                                'status' => false,
                                'message' => $e->getMessage()
                            );
                            return Response::json($response);
                        }
                    }

                    public function fetchDataAll(Request $request){
                        try{
                           $answer = $request->get('answer');
                           $dept = $request->get('dept');

                           if ($answer == "Test Potensi Akademik") {
                            $detail = db::select('select department, section, `group`, sub_group, (quantity_male+quantity_female) as jumlah from recruitments where status_at = "Test Potensi Akademik" and department = "'.$dept.'"'); 
                        }
                        if ($answer == "Test Wawancara") {
                            $detail = db::select('select department, section, `group`, sub_group, (quantity_male+quantity_female) as jumlah from recruitments where status_at = "Test Wawancara" and department = "'.$dept.'"'); 
                        }
                        if ($answer == "Test Kesehatan") {
                            $detail = db::select('select department, section, `group`, sub_group, (quantity_male+quantity_female) as jumlah from recruitments where status_at = "Test Kesehatan" and department = "'.$dept.'"'); 
                        }
                        if ($answer == "Induction") {
                            $detail = db::select('select department, section, `group`, sub_group, (quantity_male+quantity_female) as jumlah from recruitments where status_at = "Induction Training" and department = "'.$dept.'"'); 
                        }

                        $response = array(
                            'status' => true,
                            'detail' => $detail,
                            'answer' => $answer
                        );
                        return Response::json($response);
                    }catch (\Exception $e) {
                        $response = array(
                            'status' => false,
                            'message' => $e->getMessage()
                        );
                        return Response::json($response);
                    }
                }

                public function ReportPdfRecruitment(Request $request, $request_id){
                    $isi = Recruitment::where('request_id', $request_id)
                    ->select('request_id', 'position', 'department', 'create_section', 'create_group', 'create_sub_group', 'employment_status', 'quantity_male', 'quantity_female', 'reason', 'start_date', 'min_age', 'max_age', 'marriage_status', 'recruitments.major', 'status_at', 'status_req', 'recruitments.created_at', 'users.name')
                    ->leftJoin('users', 'users.id', '=', 'recruitments.created_by')
                    ->first();

        // $pdf = \App::make('dompdf.wrapper');
        // $pdf->getDomPDF()->set_option("enable_php", true);
        // $pdf->setPaper('A4', 'potrait');
        // $pdf->loadView('human_resource.recruitment.report_pdf', array(
        //     'isi' => $isi
        // ));
        // $pdf->save(public_path() . "/smart-recruitment/HR-SR".$request_id.".pdf");

                    return view('human_resource.recruitment.report_pdf', array(
                        'isi' => $isi
                    ));
                }

                public function UploadCalonKaryawan(Request $request)
                {
                    $req_id = $request->get('req_id');

                    $filename = "";
                    $file_destination = 'data_file/smart_recruitment';

                    if (count($request->file('newAttachment')) > 0) {
                        try{
                            $file = $request->file('newAttachment');
                            $filename = 'Upload_Calon_Karyawan_'.date('YmdHisa').'.'.$request->input('extension');
                            $file->move($file_destination, $filename);

                            $excel = 'data_file/smart_recruitment/' . $filename;
                            $rows = Excel::load($excel, function($reader) {
                                $reader->noHeading();
                                $reader->skipRows(1);
                            })->toObject();

                    // $dpt = EmployeeSync::where('employee_id', Auth::user()->username)
                    // ->select('department')
                    // ->first();

                            for ($i=0; $i < count($rows); $i++) {
                                $data = new CalonKaryawan(
                                    [   
                                        'request_id' => $req_id,
                                        'nama' => strtoupper($rows[$i][0]),
                                        'asal' => strtoupper($rows[$i][1]),
                                        'no_hp' => $rows[$i][2],
                                        'institusi' => strtoupper($rows[$i][3]),
                                        'email' => $rows[$i][4],
                                        'created_by' => Auth::id()
                                    ]
                                );
                                $data->save();

                                $update_req = Recruitment::where('request_id', $req_id)->update([
                                    'status_at' => 'Test Potensi Akademik'
                                ]);

                            }
                            $response = array(
                                'status' => true,
                                'message' => 'calon Karyawan Berhasil Diupload'
                            );
                            return Response::json($response);

                        }
                        catch(\Exception $e){
                            $response = array(
                                'status' => false,
                                'message' => $e->getMessage(),
                            );
                            return Response::json($response);
                        }
                    }
                    else{
                        $response = array(
                            'status' => false,
                            'message' => 'Please select file to attach'
                        );
                        return Response::json($response);
                    }
                }

                public function UploadCalonKaryawanRekontrak(Request $request)
                {   
                    $req_id = $request->get('req_id');

                    $filename = "";
                    $file_destination = 'data_file/smart_recruitment';

                    $delete = CalonRekontrak::where('request_id', $req_id)->where('status', '=', 'TIDAK LOLOS')->first();
                    $delete->forceDelete();

                    $update = Recruitment::where('request_id', $req_id)->update([
                        'status_at' => 'Test Potensi Akademik'
                    ]);

                    if (count($request->file('newAttachment')) > 0) {
                        try{
                            $file = $request->file('newAttachment');
                            $filename = 'Upload_Calon_Karyawan_'.date('YmdHisa').'.'.$request->input('extension');
                            $file->move($file_destination, $filename);

                            $excel = 'data_file/smart_recruitment/' . $filename;
                            $rows = Excel::load($excel, function($reader) {
                                $reader->noHeading();
                                $reader->skipRows(1);
                            })->toObject();

                            for ($i=0; $i < count($rows); $i++) {
                                $data = new CalonRekontrak(
                                    [   
                                        'request_id' => $req_id,
                                        'nama' => $rows[$i][0],
                                        'section' => $rows[$i][1],
                                        'group' => $rows[$i][2],
                                        'sub_group' => $rows[$i][3]
                                    ]
                                );
                                $data->save();
                            }
                            $response = array(
                                'status' => true,
                                'message' => 'calon Karyawan Berhasil Diupload'
                            );
                            return Response::json($response);

                        }
                        catch(\Exception $e){
                            $response = array(
                                'status' => false,
                                'message' => $e->getMessage(),
                            );
                            return Response::json($response);
                        }
                    }
                    else{
                        $response = array(
                            'status' => false,
                            'message' => 'Please select file to attach'
                        );
                        return Response::json($response);
                    }
                }

                public function IndexKaryawanKontrak(Request $request){

                    $title = 'Karyawan Kontrak PT. YMPI';
                    $title_jp = '';
                    return view('human_resource.karyawan_kontrak.index_karyawan_kontrak', array(
                       'title' => $title,
                       'title_jp' => $title_jp
                   ));
                }

                public function FetchKaryawanKontrak(Request $request)
                {
                    try {
                      $dateto   = $request->get('dateto');
                      $datefrom = $request->get('datefrom');
                      $department = $request->get('department');
                      $budget_date = $request->get('budget_date');
                      $time = date('Y-m', strtotime('01 '.$budget_date)) ;

                      if (($dateto || $datefrom) == null) {
                        $data = db::select('select employee_id, `name`, department, section, `group`, sub_group, hire_date, end_date, status from plan_employee_contracts');

                    }else{
                        $data = db::select('select employee_id, `name`, department, section, `group`, sub_group, hire_date, end_date, status from plan_employee_contracts where DATE_FORMAT( end_date, "%Y-%m") >= "'.$datefrom.'" AND DATE_FORMAT( end_date, "%Y-%m") <= "'.$dateto.'"');
                    }

                    $resumes_request = db::select('select r.request_id, cr.nama, cr.section, cr.`group`, cr.sub_group, date_format( r.start_date, "%d %M %Y") as start_date from calon_rekontraks as cr left join recruitments as r on r.request_id = cr.request_id where r.request_id is not null and r.department = "'.$department.'" and date_format(r.start_date, "%Y-%m") = "'.$time.'"');

                    $response = array(
                      'status' => true,
                      'data' => $data,
                      'resumes_request' => $resumes_request
                  );
                    return Response::json($response);
                } catch (\Exception $e) {
                    $response = array(
                      'status' => false,
                      'message' => $e->getMessage()
                  );
                    return Response::json($response);
                }
            }

            public function IndexResumeTunjangan(Request $request){

                $title = 'Resume Tunjangan Karyawan';
                $title_jp = '';
                return view('human_resource.index_resume_tunjangan', array(
                   'title' => $title,
                   'title_jp' => $title_jp
               ));
            }

            public function FetchResumeTunjangan(Request $request)
            {
                try {
                  $month = date('Y-m');
                  $dateto   = $request->get('dateto');
                  $datefrom = $request->get('datefrom');
                  $tunjangan = $request->get('tunjangan');
                  $resume = $request->get('resume');

                  if ($resume != null) {
                    $data = db::select('SELECT DISTINCT
                        us.request_id,
                        us.employee,
                        e.`name`,
                        us.sub_group,
                        us.`group`,
                        us.seksi,
                        us.department,
                        us.jabatan,
                        us.permohonan,
                        u.`name` AS pembuat,
                        us.remark,
                        date_format( us.created_at, "%d-%m-%Y" ) AS buat,
                        ra.project_name 
                        FROM
                        uang_keluargas AS us
                        LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee
                        LEFT JOIN users AS u ON u.id = us.created_by
                        LEFT JOIN hr_approvals AS ra ON ra.request_id = us.request_id 
                        WHERE
                        us.remark = "Close" UNION ALL
                        SELECT DISTINCT
                        us.request_id,
                        us.employee,
                        e.`name`,
                        us.sub_group,
                        us.`group`,
                        us.seksi,
                        us.department,
                        us.jabatan,
                        us.permohonan,
                        u.`name` AS pembuat,
                        us.remark,
                        date_format( us.created_at, "%d-%m-%Y" ) AS buat,
                        ra.project_name 
                        FROM
                        uang_simpatis AS us
                        LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee
                        LEFT JOIN users AS u ON u.id = us.created_by
                        LEFT JOIN hr_approvals AS ra ON ra.request_id = us.request_id 
                        WHERE
                        us.remark = "Close" UNION ALL
                        SELECT DISTINCT
                        us.request_id,
                        us.employee,
                        e.`name`,
                        NULL AS sub_group,
                        NULL AS `group`,
                        e.section,
                        e.department,
                        e.position AS jabatan,
                        "Tunjangan Kerja" AS permohonan,
                        u.`name` AS pembuat,
                        us.remark,
                        date_format( us.created_at, "%d-%m-%Y" ) AS buat,
                        ra.project_name 
                        FROM
                        uang_pekerjaans AS us
                        LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee
                        LEFT JOIN users AS u ON u.id = us.created_by
                        LEFT JOIN hr_approvals AS ra ON ra.request_id = us.request_id 
                        WHERE
                        us.remark = "Close"');

                    // $data_resumes = db::select('select DISTINCT us.request_id, us.employee, e.`name`, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, u.`name` as pembuat, us.remark, ra.project_name, date_format(us.created_at, "%d-%m-%Y") as buat, ra.project_name from uang_keluargas as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by left join hr_approvals as ra on ra.request_id = us.request_id where project_name = "'.$request->get('jenis_tunjangan').'" and DATE_FORMAT( us.created_at, "%Y-%m") >= "'.$request->get('dari_tanggal').'" AND DATE_FORMAT( us.created_at, "%Y-%m") <= "'.$request->get('sampai_tanggal').'" and us.remark = "Sudah Download"
                    //     union all
                    //     select DISTINCT us.request_id, us.employee, e.`name`, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, u.`name` as pembuat, us.remark, ra.project_name, date_format(us.created_at, "%d-%m-%Y") as buat, ra.project_name from uang_simpatis as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by left join hr_approvals as ra on ra.request_id = us.request_id where project_name = "'.$request->get('jenis_tunjangan').'" and DATE_FORMAT( us.created_at, "%Y-%m") >= "'.$request->get('dari_tanggal').'" AND DATE_FORMAT( us.created_at, "%Y-%m") <= "'.$request->get('sampai_tanggal').'" and us.remark = "Sudah Download"');
                    $data_resumes = db::select('SELECT DISTINCT
                        us.request_id,
                        us.employee,
                        e.`name`,
                        us.sub_group,
                        us.`group`,
                        us.seksi,
                        us.department,
                        us.jabatan,
                        us.permohonan,
                        u.`name` AS pembuat,
                        us.remark,
                        date_format( us.created_at, "%d-%m-%Y" ) AS buat,
                        ra.project_name 
                        FROM
                        uang_keluargas AS us
                        LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee
                        LEFT JOIN users AS u ON u.id = us.created_by
                        LEFT JOIN hr_approvals AS ra ON ra.request_id = us.request_id 
                        WHERE
                        project_name = "'.$request->get('jenis_tunjangan').'" 
                        AND DATE_FORMAT( us.created_at, "%Y-%m" ) >= "'.$request->get('dari_tanggal').'" 
                        AND DATE_FORMAT( us.created_at, "%Y-%m" ) <= "'.$request->get('sampai_tanggal').'" 
                        AND us.remark = "Sudah Download" UNION ALL
                        SELECT DISTINCT
                        us.request_id,
                        us.employee,
                        e.`name`,
                        us.sub_group,
                        us.`group`,
                        us.seksi,
                        us.department,
                        us.jabatan,
                        us.permohonan,
                        u.`name` AS pembuat,
                        us.remark,
                        date_format( us.created_at, "%d-%m-%Y" ) AS buat,
                        ra.project_name 
                        FROM
                        uang_simpatis AS us
                        LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee
                        LEFT JOIN users AS u ON u.id = us.created_by
                        LEFT JOIN hr_approvals AS ra ON ra.request_id = us.request_id 
                        WHERE
                        project_name = "'.$request->get('jenis_tunjangan').'" 
                        AND DATE_FORMAT( us.created_at, "%Y-%m" ) >= "'.$request->get('dari_tanggal').'" 
                        AND DATE_FORMAT( us.created_at, "%Y-%m" ) <= "'.$request->get('sampai_tanggal').'" 
                        AND us.remark = "Sudah Download" UNION ALL
                        SELECT DISTINCT
                        us.request_id,
                        us.employee,
                        e.`name`,
                        NULL AS sub_group,
                        NULL AS `group`,
                        e.section,
                        e.department,
                        e.position AS jabatan,
                        "Tunjangan Kerja" AS permohonan,
                        u.`name` AS pembuat,
                        us.remark,
                        date_format( us.created_at, "%d-%m-%Y" ) AS buat,
                        ra.project_name 
                        FROM
                        uang_pekerjaans AS us
                        LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee
                        LEFT JOIN users AS u ON u.id = us.created_by
                        LEFT JOIN hr_approvals AS ra ON ra.request_id = us.request_id 
                        WHERE
                        project_name = "'.$request->get('jenis_tunjangan').'" 
                        AND DATE_FORMAT( us.created_at, "%Y-%m" ) >= "'.$request->get('dari_tanggal').'" 
                        AND DATE_FORMAT( us.created_at, "%Y-%m" ) <= "'.$request->get('sampai_tanggal').'" 
                        AND us.remark = "Sudah Download"
                        ');

                    $response = array(
                      'status' => true,
                      'data' => $data,
                      'data_resumes' => $data_resumes
                  );
                    return Response::json($response);

                }else{
                    if (($dateto || $datefrom) == null) {
                        $data = db::select('SELECT DISTINCT
                            us.request_id,
                            us.employee,
                            e.`name`,
                            us.sub_group,
                            us.`group`,
                            us.seksi,
                            us.department,
                            us.jabatan,
                            us.permohonan,
                            u.`name` AS pembuat,
                            us.remark,
                            date_format( us.created_at, "%d-%m-%Y" ) AS buat,
                            ra.project_name 
                            FROM
                            uang_keluargas AS us
                            LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee
                            LEFT JOIN users AS u ON u.id = us.created_by
                            LEFT JOIN hr_approvals AS ra ON ra.request_id = us.request_id 
                            WHERE
                            us.remark = "Close" UNION ALL
                            SELECT DISTINCT
                            us.request_id,
                            us.employee,
                            e.`name`,
                            us.sub_group,
                            us.`group`,
                            us.seksi,
                            us.department,
                            us.jabatan,
                            us.permohonan,
                            u.`name` AS pembuat,
                            us.remark,
                            date_format( us.created_at, "%d-%m-%Y" ) AS buat,
                            ra.project_name 
                            FROM
                            uang_simpatis AS us
                            LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee
                            LEFT JOIN users AS u ON u.id = us.created_by
                            LEFT JOIN hr_approvals AS ra ON ra.request_id = us.request_id 
                            WHERE
                            us.remark = "Close" UNION ALL
                            SELECT DISTINCT
                            us.request_id,
                            us.employee,
                            e.`name`,
                            NULL AS sub_group,
                            NULL AS `group`,
                            e.section,
                            e.department,
                            e.position AS jabatan,
                            "Tunjangan Kerja" AS permohonan,
                            u.`name` AS pembuat,
                            us.remark,
                            date_format( us.created_at, "%d-%m-%Y" ) AS buat,
                            ra.project_name 
                            FROM
                            uang_pekerjaans AS us
                            LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee
                            LEFT JOIN users AS u ON u.id = us.created_by
                            LEFT JOIN hr_approvals AS ra ON ra.request_id = us.request_id 
                            WHERE
                            us.remark = "Close"');

                        $data_done = db::select('SELECT DISTINCT
                            us.request_id,
                            us.employee,
                            e.`name`,
                            us.department,
                            e.position AS jabatan,
                            ra.project_name,
                            date_format( us.created_at, "%d-%m-%Y" ) AS buat,
                            us.remark,
                            permohonan
                            FROM
                            uang_keluargas AS us
                            LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee
                            LEFT JOIN users AS u ON u.id = us.created_by
                            LEFT JOIN hr_approvals AS ra ON ra.request_id = us.request_id 
                            WHERE
                            us.remark = "Done" UNION ALL
                            SELECT DISTINCT
                            us.request_id,
                            us.employee,
                            e.`name`,
                            us.department,
                            e.position AS jabatan,
                            ra.project_name,
                            date_format( us.created_at, "%d-%m-%Y" ) AS buat,
                            us.remark,
                            permohonan 
                            FROM
                            uang_simpatis AS us
                            LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee
                            LEFT JOIN users AS u ON u.id = us.created_by
                            LEFT JOIN hr_approvals AS ra ON ra.request_id = us.request_id 
                            WHERE
                            us.remark = "Done" UNION ALL
                            SELECT DISTINCT
                            us.request_id,
                            us.employee,
                            e.`name`,
                            us.department,
                            e.position AS jabatan,
                            ra.project_name,
                            date_format( us.created_at, "%d-%m-%Y" ) AS buat,
                            us.remark,
                            ra.project_name as permohonan
                            FROM
                            uang_pekerjaans AS us
                            LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee
                            LEFT JOIN users AS u ON u.id = us.created_by
                            LEFT JOIN hr_approvals AS ra ON ra.request_id = us.request_id 
                            WHERE
                            us.remark = "Done"');
                    }else{
                        $data = db::select('SELECT DISTINCT
                            us.request_id,
                            us.employee,
                            e.`name`,
                            us.sub_group,
                            us.`group`,
                            us.seksi,
                            us.department,
                            us.jabatan,
                            us.permohonan,
                            u.`name` AS pembuat,
                            us.remark,
                            date_format( us.created_at, "%d-%m-%Y" ) AS buat,
                            ra.project_name 
                            FROM
                            uang_keluargas AS us
                            LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee
                            LEFT JOIN users AS u ON u.id = us.created_by
                            LEFT JOIN hr_approvals AS ra ON ra.request_id = us.request_id 
                            WHERE
                            project_name = "'.$tunjangan.'" 
                            AND DATE_FORMAT( us.created_at, "%Y-%m" ) >= "'.$datefrom.'" 
                            AND DATE_FORMAT( us.created_at, "%Y-%m" ) <= "'.$dateto.'" 
                            AND us.remark = "Close" UNION ALL
                            SELECT DISTINCT
                            us.request_id,
                            us.employee,
                            e.`name`,
                            us.sub_group,
                            us.`group`,
                            us.seksi,
                            us.department,
                            us.jabatan,
                            us.permohonan,
                            u.`name` AS pembuat,
                            us.remark,
                            date_format( us.created_at, "%d-%m-%Y" ) AS buat,
                            ra.project_name 
                            FROM
                            uang_simpatis AS us
                            LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee
                            LEFT JOIN users AS u ON u.id = us.created_by
                            LEFT JOIN hr_approvals AS ra ON ra.request_id = us.request_id 
                            WHERE
                            project_name = "'.$tunjangan.'" 
                            AND DATE_FORMAT( us.created_at, "%Y-%m" ) >= "'.$datefrom.'" 
                            AND DATE_FORMAT( us.created_at, "%Y-%m" ) <= "'.$dateto.'" 
                            AND us.remark = "Close" UNION ALL
                            SELECT DISTINCT
                            us.request_id,
                            us.employee,
                            e.`name`,
                            NULL AS sub_group,
                            NULL AS `group`,
                            e.section,
                            e.department,
                            e.position AS jabatan,
                            "Tunjangan Kerja" AS permohonan,
                            u.`name` AS pembuat,
                            us.remark,
                            date_format( us.created_at, "%d-%m-%Y" ) AS buat,
                            ra.project_name 
                            FROM
                            uang_pekerjaans AS us
                            LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee
                            LEFT JOIN users AS u ON u.id = us.created_by
                            LEFT JOIN hr_approvals AS ra ON ra.request_id = us.request_id 
                            WHERE
                            project_name = "'.$tunjangan.'" 
                            AND DATE_FORMAT( us.created_at, "%Y-%m" ) >= "'.$datefrom.'" 
                            AND DATE_FORMAT( us.created_at, "%Y-%m" ) <= "'.$dateto.'" 
                            AND us.remark = "Close"
                            ');

                        $data_done = db::select('SELECT DISTINCT
                            us.request_id,
                            us.employee,
                            e.`name`,
                            us.sub_group,
                            us.`group`,
                            us.seksi,
                            us.department,
                            us.jabatan,
                            us.permohonan,
                            u.`name` AS pembuat,
                            us.remark,
                            date_format( us.created_at, "%d-%m-%Y" ) AS buat,
                            ra.project_name 
                            FROM
                            uang_keluargas AS us
                            LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee
                            LEFT JOIN users AS u ON u.id = us.created_by
                            LEFT JOIN hr_approvals AS ra ON ra.request_id = us.request_id 
                            WHERE
                            project_name = "'.$tunjangan.'" 
                            AND DATE_FORMAT( us.created_at, "%Y-%m" ) >= "'.$datefrom.'" 
                            AND DATE_FORMAT( us.created_at, "%Y-%m" ) <= "'.$dateto.'" 
                            AND us.remark = "Done" UNION ALL
                            SELECT DISTINCT
                            us.request_id,
                            us.employee,
                            e.`name`,
                            us.sub_group,
                            us.`group`,
                            us.seksi,
                            us.department,
                            us.jabatan,
                            us.permohonan,
                            u.`name` AS pembuat,
                            us.remark,
                            date_format( us.created_at, "%d-%m-%Y" ) AS buat,
                            ra.project_name 
                            FROM
                            uang_simpatis AS us
                            LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee
                            LEFT JOIN users AS u ON u.id = us.created_by
                            LEFT JOIN hr_approvals AS ra ON ra.request_id = us.request_id 
                            WHERE
                            project_name = "'.$tunjangan.'" 
                            AND DATE_FORMAT( us.created_at, "%Y-%m" ) >= "'.$datefrom.'" 
                            AND DATE_FORMAT( us.created_at, "%Y-%m" ) <= "'.$dateto.'" 
                            AND us.remark = "Done" UNION ALL
                            SELECT DISTINCT
                            us.request_id,
                            us.employee,
                            e.`name`,
                            NULL AS sub_group,
                            NULL AS `group`,
                            e.section,
                            e.department,
                            e.position AS jabatan,
                            "Tunjangan Kerja" AS permohonan,
                            u.`name` AS pembuat,
                            us.remark,
                            date_format( us.created_at, "%d-%m-%Y" ) AS buat,
                            ra.project_name 
                            FROM
                            uang_pekerjaans AS us
                            LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee
                            LEFT JOIN users AS u ON u.id = us.created_by
                            LEFT JOIN hr_approvals AS ra ON ra.request_id = us.request_id 
                            WHERE
                            project_name = "'.$tunjangan.'" 
                            AND DATE_FORMAT( us.created_at, "%Y-%m" ) >= "'.$datefrom.'" 
                            AND DATE_FORMAT( us.created_at, "%Y-%m" ) <= "'.$dateto.'" 
                            AND us.remark = "Done"
                            ');
                    }
                }

                $response = array(
                  'status' => true,
                  'data' => $data,
                  'data_done' => $data_done,
                  'period' => date('F Y', strtotime($month))
              );
                return Response::json($response);
            } catch (\Exception $e) {
                $response = array(
                  'status' => false,
                  'message' => $e->getMessage()
              );
                return Response::json($response);
            }
        }



        public function DownloadResumeTunjangan(Request $request){
            try{
              $time = date('d-m-Y H;i;s');
              $tnj = $request->get('select_tunj');
              $on_month = $request->get('datefrom');
              $until_month = $request->get('dateto');

              $dt_awal = '';
              if ($on_month != null) {
                $dt_awal = 'and DATE_FORMAT(us.created_at, "%Y-%m") >= "'.$on_month.'"';
            }else{
                $dt_awal = '';
            }

            $dt_akhir = '';
            if ($until_month != null) {
              $dt_akhir = 'and DATE_FORMAT(us.created_at, "%Y-%m") <= "'.$until_month.'"';
          }else{
            $dt_akhir = '';
        }

        $filter = $dt_awal.$dt_akhir;


        if ($tnj == 'Uang Simpati') {
            $resumes = db::select('select distinct us.request_id, us.employee, e.`name`, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, u.`name` as pembuat, us.remark, date_format(us.created_at, "%d-%m-%Y") as buat, rp.project_name from uang_simpatis as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by left join hr_approvals as rp on rp.request_id = us.request_id where rp.project_name = "Uang Simpati" '.$filter.' and us.remark = "Done"');

            $data = array(
                'resumes' => $resumes
            );

            $update = UangSimpati::
    // ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") >= "'.$on_month.'"')
    // ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") <= "'.$until_month.'"')
            where('remark', '=', 'Done')
            ->update(['remark' => 'Sudah Download']);

            ob_clean();
            Excel::create('Resumes Tunjangan '.$time, function($excel) use ($data){
                $excel->sheet('Resumes Tunjangan', function($sheet) use ($data) {
                    return $sheet->loadView('human_resource.excel_simpati', $data);
                });
            })->export('xls');
        }else if($tnj == 'Tunjangan Keluarga'){
            $resumes = db::select('select distinct us.request_id, us.employee, e.`name`, us.sub_group, us.`group`, us.seksi, us.department, us.jabatan, us.permohonan, u.`name` as pembuat, us.remark, date_format(us.created_at, "%d-%m-%Y") as buat, rp.project_name from uang_keluargas as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by left join hr_approvals as rp on rp.request_id = us.request_id where rp.project_name = "Tunjangan Keluarga" '.$filter.' and us.remark = "Done"');

            $data = array(
                'resumes' => $resumes
            );

            $update = UangKeluarga::
    // whereRaw('DATE_FORMAT(created_at, "%Y-%m") >= "'.$on_month.'"')
    // ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") <= "'.$until_month.'"')
            where('remark', '=', 'Done')
            ->update(['remark' => 'Sudah Download']);

            ob_clean();
            Excel::create('Resumes Tunjangan '.$time, function($excel) use ($data){
                $excel->sheet('Resumes Tunjangan', function($sheet) use ($data) {
                    return $sheet->loadView('human_resource.excel_keluarga', $data);
                });
            })->export('xls');
        }else if($tnj == 'Tunjangan Kerja'){
            $resumes = db::select('select distinct us.request_id, us.employee, e.`name`, jumlah_kehadiran from uang_pekerjaans as us left join employee_syncs as e on e.employee_id = us.employee left join users as u on u.id = us.created_by left join hr_approvals as rp on rp.request_id = us.request_id where rp.project_name = "Tunjangan Kerja" '.$filter.' and us.remark = "Done"');

            $data = array(
                'resumes' => $resumes
            );

            $update = UangPekerjaan::whereRaw('DATE_FORMAT(created_at, "%Y-%m") >= "'.$on_month.'"')
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") <= "'.$until_month.'"')
            ->where('remark', '=', 'Done')
            ->update(['remark' => 'Sudah Download']);

            ob_clean();
            Excel::create('Resumes Tunjangan Kerja '.$time, function($excel) use ($data){
                $excel->sheet('Resumes Tunjangan Kerja', function($sheet) use ($data) {
                    return $sheet->loadView('human_resource.excel_kerja', $data);
                });
            })->export('xls');
        }else{
            return back()->with('error', 'Error, Pilih Jenis Tunjangan')->with('page', 'Resume Tunjangan');
        }


        // $response = array(
        //     'status' => true,
        //     'message' => 'Calon Karyawan Berhasil Ditambahkan',
        // );

        // return Response::json($response);
    }catch (Exception $e) {
       $response = array(
          'status' => false,
          'message' => $e->getMessage(),
      );
       return Response::json($response);
   }
}

public function UpdateStatusConfirm(Request $request){
    try{
        $value = $request->get('request_id');
        $project_name = $request->get('project_name');

        if ($project_name == 'Tunjangan Keluarga') {
            $update = UangKeluarga::where('request_id', $value)->update([
                'remark' => 'Done'
            ]);
        }else if ($project_name == 'Uang Simpati'){
            $update = UangSimpati::where('request_id', $value)->update([
                'remark' => 'Done'
            ]);
        }else{
            $update = UangPekerjaan::where('request_id', $value)->update([
                'remark' => 'Done'
            ]);
        }

        $response = array(
            'status' => true,
            'message' => 'Permohonan '.$project_name.' Telah Dikonfirmasi',
        );

        return Response::json($response);
    }catch (Exception $e) {
     $response = array(
      'status' => false,
      'message' => $e->getMessage(),
  );
     return Response::json($response);
 }
}

public function fetchGrafikTunjangan(Request $request){
    try{ 
        $dept = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'department', 'section', 'group', 'position')
        ->first();

        $role = User::where('username', Auth::user()->username)
        ->select('role_code')
        ->first();

        $value = $request->get('value');

        if (($role->role_code == 'S-MIS') || ($role->role_code == 'S-HR')) {
            if ($value == null) {
                $tj_kel = db::select('SELECT count(us.request_id) as jumlah , d.department_shortname FROM uang_keluargas AS us LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee LEFT JOIN users AS u ON u.id = us.created_by LEFT JOIN departments as d ON d.department_name = us.department  where us.remark = "Open" GROUP BY d.department_shortname');
                $tj_simp = db::select('SELECT count(us.request_id) as jumlah, d.department_shortname  FROM uang_simpatis AS us LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee LEFT JOIN users AS u ON u.id = us.created_by LEFT JOIN departments as d ON d.department_name = us.department where us.remark = "Open" GROUP BY d.department_shortname');
            }
        }else{
            if ($dept->position == 'Leader') {
                if ($value == null) {
                    $tj_kel = db::select('SELECT count(us.request_id) as jumlah , d.department_shortname FROM uang_keluargas AS us LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee LEFT JOIN users AS u ON u.id = us.created_by LEFT JOIN departments as d ON d.department_name = us.department where us.remark = "Open" and us.department = "'.$dept->department.'" and us.`group` = "'.$dept->group.'" GROUP BY d.department_shortname');
                    $tj_simp = db::select('SELECT count(us.request_id) as jumlah, d.department_shortname  FROM uang_simpatis AS us LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee LEFT JOIN users AS u ON u.id = us.created_by LEFT JOIN departments as d ON d.department_name = us.department where us.remark = "Open" and us.department = "'.$dept->department.'" and us.`group` = "'.$dept->group.'" GROUP BY d.department_shortname');
                }else if ($value == 'Tunjangan Keluarga'){
                    $tj_kel = db::select('SELECT count(us.request_id) as jumlah , d.department_shortname FROM uang_keluargas AS us LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee LEFT JOIN users AS u ON u.id = us.created_by LEFT JOIN departments as d ON d.department_name = us.department where us.remark = "Open" and us.department = "'.$dept->department.'" and us.`group` = "'.$dept->group.'" GROUP BY d.department_shortname');
                    $tj_simp = null;
                }else if ($value == 'Uang Simpati'){
                    $tj_kel = null;
                    $tj_simp = db::select('SELECT count(us.request_id) as jumlah, d.department_shortname  FROM uang_simpatis AS us LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee LEFT JOIN users AS u ON u.id = us.created_by LEFT JOIN departments as d ON d.department_name = us.department where us.remark = "Open" and us.department = "'.$dept->department.'" and us.`group` = "'.$dept->group.'" GROUP BY d.department_shortname');
                }    
            }else{
                if ($value == null) {
                    $tj_kel = db::select('SELECT count(us.request_id) as jumlah , d.department_shortname FROM uang_keluargas AS us LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee LEFT JOIN users AS u ON u.id = us.created_by LEFT JOIN departments as d ON d.department_name = us.department where us.remark = "Open" and us.department = "'.$dept->department.'" GROUP BY d.department_shortname');
                    $tj_simp = db::select('SELECT count(us.request_id) as jumlah, d.department_shortname  FROM uang_simpatis AS us LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee LEFT JOIN users AS u ON u.id = us.created_by LEFT JOIN departments as d ON d.department_name = us.department where us.remark = "Open" and us.department = "'.$dept->department.'" GROUP BY d.department_shortname');
                }else if ($value == 'Tunjangan Keluarga'){
                    $tj_kel = db::select('SELECT count(us.request_id) as jumlah , d.department_shortname FROM uang_keluargas AS us LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee LEFT JOIN users AS u ON u.id = us.created_by LEFT JOIN departments as d ON d.department_name = us.department where us.remark = "Open" and us.department = "'.$dept->department.'" GROUP BY d.department_shortname');
                    $tj_simp = null;
                }else if ($value == 'Uang Simpati'){
                    $tj_kel = null;
                    $tj_simp = db::select('SELECT count(us.request_id) as jumlah, d.department_shortname  FROM uang_simpatis AS us LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee LEFT JOIN users AS u ON u.id = us.created_by LEFT JOIN departments as d ON d.department_name = us.department where us.remark = "Open" and us.department = "'.$dept->department.'" GROUP BY d.department_shortname');
                }    
            }  
        }


        $all_kel = db::select('SELECT d.department_shortname, count(us.request_id) as jumlah FROM uang_keluargas AS us LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee LEFT JOIN users AS u ON u.id = us.created_by LEFT JOIN departments as d ON d.department_name = us.department where us.remark = "Open" GROUP BY department_shortname');
        $all_simp = db::select('SELECT d.department_shortname, count(us.request_id) as jumlah FROM uang_simpatis AS us LEFT JOIN employee_syncs AS e ON e.employee_id = us.employee LEFT JOIN users AS u ON u.id = us.created_by LEFT JOIN departments as d ON d.department_name = us.department where us.remark = "Open" GROUP BY department_shortname');

        $semua = db::select('SELECT d.department_shortname, COALESCE ( count( request_id ), 0) AS simpati, COALESCE (( SELECT count( request_id ) FROM uang_keluargas WHERE remark = "Open"), 0) AS keluarga FROM uang_simpatis as us LEFT JOIN departments as d ON d.department_name = us.department WHERE us.remark = "Open" GROUP BY department_shortname');

        $response = array(
            'status' => true,
            'tj_kel' => $tj_kel,
            'tj_simp' => $tj_simp,
    // 'tj_kerja' => $tj_kerja,
            'all_kel' => $all_kel,
            'all_simp' => $all_simp,
            'semua' => $semua,
            // 'test' => $test
        );
        return Response::json($response);
    } catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function IndexTunjanganKerja(){
    $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('employee_id', 'name', 'department', 'section', 'group', 'position')
    ->first();

    $user = '';
    if ((strtoupper(Auth::user()->username) == 'PI0109004') || (strtoupper(Auth::user()->username) == 'PI9905001')) {
        $user = db::select('SELECT employee_id,name FROM employee_syncs where employee_id = "'.$emp_dept->employee_id.'" and `end_date` is null');
    }else if ((strtoupper(Auth::user()->username) == 'PI0603019') || (strtoupper(Auth::user()->username) == 'PI2101044')){
        $user = db::select('SELECT employee_id,name FROM employee_syncs where `end_date` is null');
    }else if ($emp_dept->position == 'Leader') {
        $user    = db::select('SELECT employee_id,name FROM employee_syncs where `group` = "'.$emp_dept->group.'" and `end_date` is null');
    }else{
        $user    = db::select('SELECT employee_id,name FROM employee_syncs where department = "'.$emp_dept->department.'" and `end_date` is null');
    }

    $department = db::select('SELECT DISTINCT department FROM employee_syncs where department = "'.$emp_dept->department.'"');

    return view('human_resource.index_tunjangan_kerja', array(
        'title' => 'Human Resource Department', 
        'title_jp' => '人材ダッシュボード',
        'department' => $department,
        'emp_dept' => $emp_dept,
        'user' => $user,
        'category' => $this->category
    ))->with('page', 'Human Resource');
}

// ========================   APPRAISAL    ==========================

public function indexAppraisal()
{
    $usr = EmployeeSync::where('employee_id', '=', Auth::user()->username)->select('position')->first();

    return view('human_resource.appraisal.index', array(
        'title' => 'Human Resource Appraisal', 
        'position' => $usr->position, 
        'title_jp' => '',
    ))->with('page', 'Human Resource');
}

public function indexAppraisalBy($category)
{
    return view('human_resource.appraisal.master', array(
        'title' => 'Human Resource Appraisal', 
        'title_jp' => '',
    ))->with('page', 'Human Resource');
}

public function fetchAppraisal(Request $request)
{
    $dept = EmployeeSync::where('employee_id', Auth::user()->username)->select('department')->first();

    // $emp_data = EmployeeSync::whereNull('end_date')->where('grade', '=', $request->get(''))

    $datas = EmployeeSync::whereNull('end_date')->where('employment_status', '=', 'PERMANENT')
    ->select('employee_id', 'name', 'hire_date', 'birth_date', 'grade_code')->get();

    $abs = db::connection('ympimis_2')->table('hr_appraisal_absences')->get();

    $response = array(
        'status' => true,
        'datas' => $datas,
        'absences' => $abs,
    );
    return Response::json($response);
}

public function indexAppraisalContract()
{
 return view('human_resource.appraisal.contract', array(
    'title' => 'Human Resource Appraisal', 
    'title_jp' => '',
))->with('page', 'Human Resource');
}

public function indexAppraisalContractManagement($position)
{
 return view('human_resource.appraisal.contract_management', array(
    'title' => 'Human Resource Appraisal', 
    'title_jp' => '',
))->with('page', 'Human Resource');
}

public function indexEvaluationContract()
{
 return view('human_resource.appraisal.contract_evaluation', array(
    'title' => 'Contract Evaluation', 
    'title_jp' => '',
))->with('page', 'Human Resource');
}

public function indexEvaluationForm($emp_id, $kontrak, $period)
{
    $emp = EmployeeSync::where('employee_id', '=', $emp_id)
    ->select('employee_id', 'name', 'section', 'group', 'sub_group', 'employment_status')
    ->first();

    return view('human_resource.appraisal.evaluation_form', array(
        'title' => 'Contract Evaluation Form', 
        'title_jp' => '',
        'emp' => $emp,
    ))->with('page', 'Human Resource');
}

public function indexContractReport()
{
    return view('human_resource.appraisal.contract_report', array(
        'title' => 'Contract Evaluation Report', 
        'title_jp' => '',        
    ))->with('page', 'Human Resource');
}

public function indexSPForm()
{
    return view('human_resource.appraisal.employee_sp', array(
        'title' => 'Pelanggaran Karyawan', 
        'title_jp' => '',        
    ))->with('page', 'Human Resource');
}

public function indexMonitoringEvaluation()
{
    return view('human_resource.appraisal.evaluation_monitoring', array(
        'title' => 'Contract Evaluation Monitoring', 
        'title_jp' => ''
    ))->with('page', 'Human Resource');
}

public function indexMonitoringApprovalEvaluation()
{
    return view('human_resource.appraisal.evaluation_approval_monitoring', array(
        'title' => 'Contract Evaluation Approval Monitoring', 
        'title_jp' => ''
    ))->with('page', 'Human Resource');
}

public function fetchMonitoringEvaluation(Request $request)
{
    $datas = db::connection('sunfish')->select("SELECT [Section], COUNT(mstr.emp_no) as jml from
        (SELECT emp_no FROM [dbo].[TEODEMPCOMPANY] where end_date is null and (FORMAT(employment_enddate, 'yyyy-MM') = '2022-12')) mstr
        join VIEW_YMPI_Emp_OrgUnit on mstr.emp_no = VIEW_YMPI_Emp_OrgUnit.Emp_no
        group by [Section]");

//     SELECT [Section], mon, COUNT(mstr.emp_no) as jml from
// (SELECT emp_no, FORMAT(employment_enddate, 'yyyy-MM') as mon FROM [dbo].[TEODEMPCOMPANY] where end_date is null and (FORMAT(employment_enddate, 'yyyy-MM') = '2022-12' or FORMAT(employment_enddate, 'yyyy-MM') = '2023-01' or FORMAT(employment_enddate, 'yyyy-MM') = '2023-02')) mstr
// join VIEW_YMPI_Emp_OrgUnit on mstr.emp_no = VIEW_YMPI_Emp_OrgUnit.Emp_no
// group by [Section], mon

    $response = array(
        'status' => true,
        'datas' => $datas,
    );
    return Response::json($response);
}

public function UpdateDurasiKontrak(Request $request){
    try{
        $nik = $request->get('nik');
        // $update = $request->get('update');
        $date = $request->get('date');
        // dd($nik, $date);

        // for ($i=0; $i < count($update); $i++) {
        $update = db::table('plan_employee_contracts')
            // ->where('employee_id',$update[$i])
        ->where('employee_id',$nik)
        ->update([
            'end_date' => $date,
            'planing_end_date' => $date
        ]);
        // }

        $response = array(
            'status' => true,
            'message' => 'Pelanggaran berhasil dihapus'
        );
        return Response::json($response);
    }
    catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function generateEmployee(Request $request)
{
    // ---------------- produksi --------------
    $data_emp = db::connection('sunfish')->select("SELECT Emp_no, Full_name, grade_code, FORMAT(start_date, 'yyyy-MM-dd') as start_date, pos_name_en, Department, [Section], Groups, FORMAT(birthdate, 'yyyy-MM-dd') as birthdate FROM [dbo].[VIEW_YMPI_Emp_OrgUnit] where end_date is null and employ_code = 'PERMANENT' and job_status_code <> 'STAFF' and (grade_code LIKE 'E%' OR grade_code LIKE 'L%')");

    $response = array(
        'status' => true,
        'datas' => $data_emp
    );
    return Response::json($response);
}

public function insertEmployee(Request $request)
{
    foreach ($request->get('data_emp') as $emp) {
        $insert = db::connection('hr')->table('m_2204')->insert([
            [ 'data' => $emp, 'updated_at' => date('Y-m-d H:i:s')]
        ]);
    }

    $data_abs = db::connection('sunfish')->select("
        SELECT
        emp_no,
        official_name,
        SUM ( CASE WHEN Attend_Code LIKE '%CK10%' OR Attend_Code LIKE '%CK12%' THEN 1 ELSE 0 END ) AS ck,
        SUM ( CASE WHEN Attend_Code LIKE '%ABS%' THEN 1 ELSE 0 END ) AS abs,
        SUM ( CASE WHEN Attend_Code LIKE '%Izin%' THEN 1 ELSE 0 END ) AS izin,
        SUM ( CASE WHEN Attend_Code LIKE '%SAKIT%' THEN 1 ELSE 0 END ) AS sakit,
        SUM ( CASE WHEN Attend_Code LIKE '%LTI%' THEN 1 ELSE 0 END ) AS telat,
        SUM ( CASE WHEN Attend_Code LIKE '%CUTI%' THEN 1 ELSE 0 END ) AS cuti 
        FROM
        [dbo].[VIEW_YMPI_Emp_Attendance] 
        WHERE
        FORMAT ( shiftstarttime, 'yyyy-MM-dd' ) >= '2021-08-01' 
        AND FORMAT ( shiftstarttime, 'yyyy-MM-dd' ) <= '2022-03-31' 
        AND Attend_Code IS NOT NULL 
        AND Attend_Code != ' OFF' 
        GROUP BY
        emp_no,
        official_name");

    foreach ($data_abs as $abs) {
        $insert = db::connection('hr')->table('m_2207')->insert([
            [ 
                'period_from' => '2021-08-01',
                'period_to' => '2022-03-31',
                'employee_id' => $abs->emp_no,
                'employee_name' => $abs->official_name,
                'cuti_khusus' => $abs->ck,
                'absence' => $abs->abs,
                'izin' => $abs->izin,
                'sakit' => $abs->sakit,
                'telat_pulang_cepat' => $abs->telat,
                'cuti' => $abs->cuti,
                'updated_at' => date('Y-m-d H:i:s')
            ]

        ]);
    }

    $response = array(
        'status' => true,
    );
    return Response::json($response);  
}

public function fetchAppraisalContract(Request $request)
{
    try {
        $emp = PlanEmployeeContract::leftJoin('employee_syncs', 'plan_employee_contracts.employee_id', '=', 'employee_syncs.employee_id');

        if (strtoupper(Auth::user()->username) != 'PI2002021' && strtoupper(Auth::user()->username) != 'PI0811002' && strtoupper(Auth::user()->username) != 'PI1110002') {
            $usr = EmployeeSync::where('employee_id', '=', Auth::user()->username)->select('department', 'position', 'section')->first();

            // if ($usr->position == 'Leader') {
            $emp = $emp->where('employee_syncs.section', '=', $usr->section);
            // } else {
            //     $emp = $emp->where('employee_syncs.department', '=', $usr->department);
            // }

        }

        $emp = $emp->where(db::raw('DATE_FORMAT(planing_end_date, "%Y-%m") '), '=', $request->get('period'))
        ->select('employee_syncs.employee_id', 'employee_syncs.name', 'employee_syncs.department', 'employee_syncs.section', 'employee_syncs.group', 'employee_syncs.sub_group','plan_employee_contracts.hire_date', 'plan_employee_contracts.planing_end_date', 'employee_syncs.employment_status')
        ->get();

        $emp_in = [];

        foreach ($emp as $kry) {
            array_push($emp_in, $kry->employee_id);
        }

        $abs = db::connection('sunfish')->table('VIEW_YMPI_Emp_Attendance')->whereIn('emp_no', $emp_in)
        ->select('emp_no', db::raw("SUM(IIF(Attend_Code like '%SAKIT%', 1, 0)) as sd"), db::raw("SUM(IIF(Attend_Code like '%IZIN%', 1, 0)) as ijin"), db::raw("SUM(IIF(Attend_Code like '%ABS%', 1, 0)) as abs"), db::raw("SUM(IIF(Attend_Code like '%LTI%', 1, 0)) as telat"), db::raw("SUM(IIF(Attend_Code like '%EAO%', 1, 0)) as pulcep"))
        ->groupBy('emp_no')
        ->get();

        $skor = db::connection('ympimis_2')->table('hr_evaluations')
        ->where('period', '=', $request->get('period'))
        ->get();

        $response = array(
            'status' => true,
            'data_emp' => $emp,
            'data_abs' => $abs,
            'skors' => $skor
        );
        return Response::json($response); 
    } catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function fetchAppraisalContractReport(Request $request)
{
    try {
        // db::connection('sunfish')->select('')->where('');
        $emp = PlanEmployeeContract::leftJoin('employee_syncs', 'plan_employee_contracts.employee_id', '=', 'employee_syncs.employee_id')
        ->where(db::raw('DATE_FORMAT(planing_end_date, "%Y-%m") '), '=', $request->get('period'))
        ->select('employee_syncs.employee_id', 'employee_syncs.name', 'employee_syncs.department', 'employee_syncs.section', 'employee_syncs.group', 'employee_syncs.sub_group','plan_employee_contracts.hire_date', 'plan_employee_contracts.planing_end_date', 'employee_syncs.employment_status')
        ->get();

        $emp_in = [];

        foreach ($emp as $kry) {
            array_push($emp_in, $kry->employee_id);
        }

        $abs = db::connection('sunfish')->table('VIEW_YMPI_Emp_Attendance')->whereIn('emp_no', $emp_in)
        ->select('emp_no', db::raw("SUM(IIF(Attend_Code like '%SAKIT%', 1, 0)) as sd"), db::raw("SUM(IIF(Attend_Code like '%IZIN%', 1, 0)) as ijin"), db::raw("SUM(IIF(Attend_Code like '%ABS%', 1, 0)) as abs"), db::raw("SUM(IIF(Attend_Code like '%LTI%', 1, 0)) as telat"), db::raw("SUM(IIF(Attend_Code like '%EAO%', 1, 0)) as pulcep"))
        ->groupBy('emp_no')
        ->get();

        $skor = db::connection('ympimis_2')->table('hr_evaluations')
        ->where('period', '=', $request->get('period'))
        ->get();

        $response = array(
            'status' => true,
            'data_emp' => $emp,
            'data_abs' => $abs,
            'skors' => $skor,
        );
        return Response::json($response); 
    } catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function postEvaluationForm(Request $request)
{
    try {
        $score_total = 0;

        foreach ($request->get('jml_score_all') as $scr) {
            $score_total += (int) $scr;
        }


        // Form Number
        $tahun = date('y');
        $bulan = date('m');

        $query = "SELECT form_number FROM ympimis_2.hr_evaluations where DATE_FORMAT(created_at, '%y') = '$tahun' and month(created_at) = '$bulan' order by form_number DESC LIMIT 1";
        $nomorurut = DB::select($query);

        if ($nomorurut != null)
        {
            $nomor = substr($nomorurut[0]->form_number, -3);
            $nomor = $nomor + 1;
            $nomor = sprintf('%03d', $nomor);
        }
        else
        {
            $nomor = "001";
        }

        $result['tahun'] = $tahun;
        $result['bulan'] = $bulan;
        $result['no_urut'] = $nomor;

        $form_number = 'EV'.$result['tahun'].$result['bulan'].$result['no_urut'];


        $insert = db::connection('ympimis_2')->table('hr_evaluations')->insert([
            [ 
                'form_number' => $form_number,
                'period' => $request->get('period'),
                'employee_id' => $request->get('employee_id'),
                'employee_name' => $request->get('name'),
                'section' => $request->get('section'),
                'group' => $request->get('group'),
                'sub_group' => $request->get('sub_group'),
                'contract_status' => $request->get('contract_status'),
                'job_level' => $request->get('job_level'),
                'total_score' => $score_total,
                'grade' => $request->get('grade_score'),
                'leader_id' => strtoupper(Auth::user()->username),
                'leader_name' => Auth::user()->name,
                'leader_at' => date('Y-m-d H:i:s'),
                'status' => 'judgement',
                'created_by' => strtoupper(Auth::user()->username),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);

        for ($i=0; $i < 9; $i++) { 
            $insert2 = db::connection('ympimis_2')->table('hr_evaluation_details')->insert([
                [ 
                    'form_number' => $form_number,
                    'period' => $request->get('period'),
                    'employee_id' => $request->get('employee_id'),
                    'employee_name' => $request->get('name'),
                    'contract_status' => $request->get('contract_status'),
                    'item_name' => $request->get('item_penilaian')[$i],
                    'score' => $request->get('score_all')[$i],
                    'sub_total_score' => $request->get('jml_score_all')[$i],
                    'created_by' => strtoupper(Auth::user()->username),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]

            ]);
        }

        $datas = db::connection('ympimis_2')->table('hr_evaluations')
        ->leftJoin('hr_evaluation_details', 'hr_evaluations.form_number', '=', 'hr_evaluation_details.form_number')
        ->where('hr_evaluations.form_number', '=', $form_number)
        ->select('hr_evaluations.form_number', 'hr_evaluations.period', 'hr_evaluations.employee_id', 'hr_evaluations.employee_name', 'hr_evaluations.section', 'hr_evaluations.group', 'hr_evaluations.sub_group', 'hr_evaluations.contract_status', 'hr_evaluations.job_level', 'hr_evaluations.total_score', 'hr_evaluations.grade', 'hr_evaluations.leader_id', 'hr_evaluations.leader_name', 'hr_evaluations.leader_at', 'hr_evaluations.foreman_id', 'hr_evaluations.foreman_name', 'hr_evaluations.foreman_at', 'hr_evaluation_details.item_name', 'hr_evaluation_details.score', 'hr_evaluation_details.sub_total_score')
        ->get();
        
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('human_resource.appraisal.evaluation_pdf', array(
            'data' => $datas,
        ));

        $pdf->save(public_path() . "/files/evaluasi_Karyawan/evaluasi/" . $form_number . ".pdf");

        $response = array(
            'status' => true,
        );
        return Response::json($response); 
    } catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage(),
        );
        return Response::json($response); 
    }
}

public function sendEmailEvaluationForm(Request $request)
{
    $data_resumes = db::connection('ympimis_2')->table('hr_evaluations')
    ->select(db::raw('DATE_FORMAT(CONCAT(period,"-01"), "%M %Y") as periode'), db::raw('COUNT(id) as jml_karyawan'))
    ->whereIn('section', $request->get('section'))
    ->where('period', '=', $request->get('period'))
    ->groupBy('period')
    ->first();

    $datas = [
        'datas' => $data_resumes
    ];

    Mail::to(['khoirul.umam@music.yamaha.com'])
    ->bcc(['lukmannul.arif@music.yamaha.com'])
    ->send(new SendEmail($datas, 'hr_evaluation'));
}

public function postFinalEvaluation(Request $request)
{
    try {
        for ($i=0; $i < count($request->get('rekomendasi')); $i++) { 
            DB::connection('ympimis_2')
            ->table('hr_evaluations')
            ->where('period', '=', $request->get('period'))
            ->where('employee_id', '=', $request->get('rekomendasi')[$i]['employee_id'])
            ->update([
                'recommendation' => $request->get('rekomendasi')[$i]['rekomendasi'],
                'new_contract_period' => $request->get('perpanjangan')[$i]['panjang'],
                'foreman_at' => date('Y-m-d H:i:s'),
                'status' => 'Waiting Approval',
            ]);
        }

        $response = array(
            'status' => true,
        );
        return Response::json($response); 
    } catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function excelContractReport(Request $request)
{
    $data_eva = db::connection('ympimis_2')
    ->table('hr_evaluations')
    ->where('period', '=', $request->get('period2'))
    // ->whereNotNull('director_app_at')
    ->get();

    $data = array(
        'data_eva' => $data_eva
    );
    ob_clean();
    Excel::create('List HR Appraisal '.$request->get('period2'), function($excel) use ($data){
        $excel->sheet('Master', function($sheet) use ($data) {
            return $sheet->loadView('human_resource.appraisal.contract_excel', $data);
        });
    })->export('xlsx');
}

public function FetchTabelResumeTunjangan(Request $request){
    try {
        $user = Auth::user()->role_code;

        $department = EmployeeSync::where('employee_id', Auth::user()->username)->select('department')->first();

        $bulan = $request->get('bulan');

        if ($user == 'S-MIS' || $user == 'S-HR' || $user == 'M-HR') {
            $resumes = db::select('SELECT
                request_id,
                employee AS nik_karyawan,
                es.`name` AS nama_karyawan,
                u.username AS nik_leader,
                u.`name` AS nama_leader,
                permohonan,
                remark AS `status`
                FROM
                uang_simpatis AS us
                LEFT JOIN employee_syncs AS es ON es.employee_id = us.employee
                LEFT JOIN users AS u ON u.id = us.created_by 
                WHERE
                DATE_FORMAT( us.created_at, "%Y-%m" ) = "'.$bulan.'" UNION ALL
                SELECT
                request_id,
                employee AS nik_karyawan,
                es.`name` AS nama_karyawan,
                u.username AS nik_leader,
                u.`name` AS nama_leader,
                permohonan,
                remark AS `status`
                FROM
                uang_keluargas AS uk
                LEFT JOIN employee_syncs AS es ON es.employee_id = uk.employee
                LEFT JOIN users AS u ON u.id = uk.created_by 
                WHERE
                DATE_FORMAT( uk.created_at, "%Y-%m" ) = "'.$bulan.'"');
        }else{
            $resumes = db::select('SELECT
                request_id,
                employee AS nik_karyawan,
                es.`name` AS nama_karyawan,
                u.username AS nik_leader,
                u.`name` AS nama_leader,
                permohonan,
                remark AS `status`
                FROM
                uang_simpatis AS us
                LEFT JOIN employee_syncs AS es ON es.employee_id = us.employee
                LEFT JOIN users AS u ON u.id = us.created_by 
                WHERE
                DATE_FORMAT( us.created_at, "%Y-%m" ) = "'.$bulan.'" and us.department = "'.$department->department.'" and remark != "BC" UNION ALL
                SELECT
                request_id,
                employee AS nik_karyawan,
                es.`name` AS nama_karyawan,
                u.username AS nik_leader,
                u.`name` AS nama_leader,
                permohonan,
                remark AS `status`
                FROM
                uang_keluargas AS uk
                LEFT JOIN employee_syncs AS es ON es.employee_id = uk.employee
                LEFT JOIN users AS u ON u.id = uk.created_by 
                WHERE
                DATE_FORMAT( uk.created_at, "%Y-%m" ) = "'.$bulan.'" and uk.department = "'.$department->department.'" and remark != "BC"');
        }

        $response = array(
            'status' => true,
            'resumes' => $resumes
        );
        return Response::json($response); 
    } catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function IndexShiftSchedule(){
    try{
        $title = 'Shift Schedule Karyawan';
        $title_jp = '(???)';
        $user = Auth::user()->employee;

        return view('human_resource.shift_schedule.index_shift_schedule', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'user' => $user
        ));
    }catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function FetchShiftSchedule(){
    try{
        $data_resumes = db::connection('ympimis_2')->select('select * from hr_shift_notifications where remark = "Salah" order by shift_sf ASC');
        $response = array(
            'status' => true,
            'data_resumes' => $data_resumes
        );
        return Response::json($response);
    }catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function SelectMatchData(Request $request){
    try{
        $tanggal = "";

        if(strlen($request->get('datefrom')) > 0){
         $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
         $tanggal = "and A.shiftstarttime >= '".$datefrom." 00:00:00' ";
         if(strlen($request->get('dateto')) > 0){
            $dateto = date('Y-m-d', strtotime($request->get('dateto')));
            $tanggal = $tanggal."and A.shiftstarttime <= '".$dateto." 23:59:59' ";
        }
    }

    $qry = "SELECT
    format ( A.shiftstarttime, 'yyyy-MM-dd' ) AS tanggal,
    A.emp_no,
    B.Full_name,
    B.Department,
    B.section,
    B.groups,
    B.cost_center_code,
    A.shiftdaily_code,
    A.starttime,
    A.endtime,
    A.Attend_Code 
    FROM
    VIEW_YMPI_Emp_Attendance A
    LEFT JOIN VIEW_YMPI_Emp_OrgUnit B ON A.emp_no = B.emp_no 
    WHERE
    A.emp_no IS NOT NULL ".$tanggal."
    ORDER BY
    A.emp_no ASC";

    $attendances = db::connection('sunfish')->select($qry);

    db::connection('ympimis_2')->table('hr_shift_notifications')->truncate();

    for ($i=0; $i < count($attendances) ; $i++) {
        $sf = $attendances[$i]->shiftdaily_code;
        $a = date('h:i', strtotime($attendances[$i]->starttime));
        $b = date('h:i', strtotime($attendances[$i]->endtime));

        $at = date('H:i:s', strtotime($attendances[$i]->starttime));
        $bt = date('H:i:s', strtotime($attendances[$i]->endtime));


        $ms = db::connection('ympimis_2')->select('select shift_schedule, check_in, check_out from hr_shift_masters');

        for ($z=0; $z <= count($ms) ; $z++) { 
            $sft = $ms[$z]->shift_schedule;
            $in = $ms[$z]->check_in;
            $out = $ms[$z]->check_out;

            $p = '';
                // if (($sf == $sft) && ($a >= $in && $b <= $out)) {
            if ($sf == $sft) {
                if ($a == $in) {
                    $p = 'Terlambat';
                }else{
                    $p = $sft;
                }
            }else{
                $p = 'Salah';
            }
        }
        dd($p);

            // if (str_contains($sf, 'OFF')) {
            //     $p = 'OFF';
            // }else if (($sf == '4G_Sec_Shift_1') && ($a >= '04:00' && $b <= '16:30')) {
            //     if ($a == '06.00') {
            //         $p = 'Terlambat';
            //     }else{
            //         $p = '4G_Sec_Shift_1';
            //     }
            // }else if (($sf == '4G_Sec_Shift_1_jumat') && ($a >= '04:00' && $b <= '17:00')) {
            //     if ($a == '06:00') {
            //         $p = 'Terlambat';
            //     }else{
            //         $p = '4G_Sec_Shift_1_jumat';
            //     }
            // }else if (($sf == '4G_Sec_Shift_2') && ($a >= '13:00' && $b <= '00:00')) {
            //     if ($a == '15:00') {
            //         $p = 'Terlambat';
            //     }else{
            //         $p = '4G_Sec_Shift_2';
            //     }
            // }else if (($sf == '4G_Sec_Shift_2_Jumat') && ($a >= '13:00' && $b <= '00:00')) {
            //     if ($a == '15:00') {
            //         $p = 'Terlambat';
            //     }else{
            //         $p = '4G_Sec_Shift_2';
            //     }
            // }else if (($sf == '4G_Sec_Shift_3') && ($a >= '21:00' && $b <= '08:40')) {
            //     if ($a == '23:00') {
            //         $p = 'Terlambat';
            //     }else{
            //         $p = '4G_Sec_Shift_3';
            //     }
            // }else if (($sf == '4G_Shift_1') && ($a >= '05:00' && $b <= '17:00')) {
            //     if ($a == '07:00') {
            //         $p = 'Terlambat';
            //     }else{
            //         $p = '4G_Shift_1';
            //     }
            // }else if (($sf == '4G_Shift_1_Jumat') && ($a >= '05:00' && $b <= '17:00')) {
            //     if ($a == '07:00') {
            //         $p = 'Terlambat';
            //     }else{
            //         $p = '4G_Shift_1_Jumat';
            //     }
            // }else if (($sf == '4G_Shift_1_Jumat') && ($a >= '05:00' && $b <= '17:00')) {
            //     if ($a == '07:00') {
            //         $p = 'Terlambat';
            //     }else{
            //         $p = '4G_Shift_1_Jumat';
            //     }
            // }else{
            //     $p = 'Salah';
            // }

            // $insert = db::connection('ympimis_2')->table('hr_shift_notifications')->insert([
            //     'karyawan' => $attendances[$i]->emp_no.'/'.$attendances[$i]->Full_name,
            //     'bagian' => $attendances[$i]->Department.'/'.$attendances[$i]->section.'/'.$attendances[$i]->groups,
            //     'shift_sf' => $attendances[$i]->shiftdaily_code,
            //     'jam_in' => $at,
            //     'jam_out' => $bt,
            //     'created_at' => date('Y-m-d H:i:s'),
            //     'updated_at' => date('Y-m-d H:i:s'),
            //     'remark' => $p
            // ]);
    }

        // $data_resumes = db::connection('ympimis_2')->select('select * from hr_shift_notifications where remark = "Salah"');

        // Mail::to(['ummi.ernawati@music.yamaha.com'])
        // ->bcc(['lukmannul.arif@music.yamaha.com'])
        // ->send(new SendEmail($data_resumes, 'hr_notif'));

    $response = array(
        'status' => true,
    );
    return Response::json($response);
}catch (Exception $e) {
    $response = array(
        'status' => false,
        'message' => $e->getMessage()
    );
    return Response::json($response); 
}
}

public function IndexPencabutanTunjangan(){
    try{
        $title = 'Pencabutan Tunjangan Karyawan';
        $title_jp = '(???)';
        $user = Auth::user()->employee;

        return view('human_resource.pencabutan_tunjangan.index_pencabutan', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'user' => $user
        ));
    }catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function FetchPencabutanTunjangan(){
    try{
        $data_resumes = db::select("SELECT
            es.employee_id,
            es.`name`,
            department,
            section,
            CONCAT(
                SPLIT_STRING ( SPLIT_STRING ( m_anak1, '_', 4 ), '-', 3 ),
                ',',
                SPLIT_STRING ( SPLIT_STRING ( m_anak2, '_', 4 ), '-', 3 ),
                ',',
                SPLIT_STRING ( SPLIT_STRING ( m_anak3, '_', 4 ), '-', 3 ),
                ',',
                SPLIT_STRING ( SPLIT_STRING ( m_anak4, '_', 4 ), '-', 3 ),
                ',',
                SPLIT_STRING ( SPLIT_STRING ( m_anak5, '_', 4 ), '-', 3 ),
                ',',
                SPLIT_STRING ( SPLIT_STRING ( m_anak6, '_', 4 ), '-', 3 ),
                ',',
                SPLIT_STRING ( SPLIT_STRING ( m_anak7, '_', 4 ), '-', 3 ) 
                ) AS tahun_anak 
            FROM
            employee_updates as eu
            LEFT JOIN employee_syncs as es on es.employee_id = eu.employee_id
            where eu.employee_id is not null
            order by eu.employee_id asc");
        $tahun_sekarang = date('Y');
        $response = array(
            'status' => true,
            'data_resumes' => $data_resumes,
            'tahun_sekarang' => $tahun_sekarang
        );
        return Response::json($response);
    }catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function IndexKaryawanBermasalah(){
    try{
        $title = 'Indikasi Pelanggaran Kehadiran';
            // $title_jp = '';
        $user = Auth::user()->employee;
        $department = db::select('select * from departments where department_name != "General Manager - Expatriate (GME)" order by department_name asc');
        $fy = db::select('select fiscal_year from weekly_calendars group by fiscal_year order by id desc');

        return view('human_resource.karyawan_bermasalah.index_karyawan_bermasalah', array(
            'title' => $title,
                // 'title_jp' => $title_jp,
            'user' => $user,
            'department' => $department,
            'fy' => $fy
        ));
    }catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function FetchIndikasiPelanggaranKehadiran(Request $request){
    try{
        // $first_30 = date('Y-m-d', strtotime('-35 Days'));
        // $last_30 = date('Y-m-d', strtotime('-5 Days'));

        // $attendances_1 = db::connection('sunfish')->select("SELECT
        //     result.emp_no,
        //     result.full_name,
        //     SUM ( result.SAKIT ) AS sakit,
        //     SUM ( result.MANGKIR ) AS mangkir,
        //     SUM ( result.TERLAMBAT ) AS terlambat
        //     FROM
        //     (
        //     SELECT
        //     a.emp_no,
        //     a.full_name,
        //     SUM ( IIF ( a.attend_code = 'SAKIT', 1, 0 ) ) AS 'SAKIT',
        //     SUM ( IIF ( a.attend_code = 'MANGKIR', 1, 0 ) ) AS 'MANGKIR',
        //     SUM ( IIF ( a.attend_code = 'TERLAMBAT/PULANG CEPAT', 1, 0 ) ) AS 'TERLAMBAT'
        //     FROM
        //     (
        //     SELECT
        //     a.emp_no,
        //     a.full_name,
        //     IIF (
        //     a.Attend_Code LIKE '%SAKIT%',
        //     'SAKIT',
        //     IIF (
        //     a.Attend_Code LIKE '%MANGKIR%',
        //     'MANGKIR',
        //     IIF ( a.Attend_Code LIKE '%LTI%', 'TERLAMBAT/PULANG CEPAT', IIF ( a.Attend_Code LIKE '%EAO%', 'TERLAMBAT/PULANG CEPAT', 'UNDEFINED' ) )
        //     )
        //     ) AS attend_code,
        //     a.shiftstarttime
        //     FROM
        //     VIEW_YMPI_ATTENDANCE AS a
        //     LEFT JOIN VIEW_YMPI_Emp_OrgUnit AS e ON e.Emp_no = a.emp_no
        //     WHERE
        //     format ( a.shiftstarttime, 'yyyy-MM-dd' ) <= '" . $last_30 . "'
        //     AND format ( a.shiftstarttime, 'yyyy-MM-dd' ) >= '" . $first_30 . "'
        //     AND a.shiftdaily_code NOT LIKE '%OFF%'
        //     AND a.emp_no NOT LIKE 'OS%'
        //     AND ( a.Attend_Code LIKE '%SAKIT%' OR a.Attend_Code LIKE '%MANGKIR%' OR a.Attend_Code LIKE '%LTI%' OR a.Attend_Code LIKE '%EAO%' )
        //     AND e.end_date IS NULL
        //     ) AS a
        //     GROUP BY
        //     a.emp_no,
        //     a.full_name
        //     ) AS result
        //     GROUP BY
        //     result.emp_no,
        //     result.full_name");

        // $attendances_2 = db::select("SELECT
        //     hr_leave_request_details.employee_id,
        //     hr_leave_request_details.`name`,
        //     count( hr_leave_request_details.request_id ) AS izin
        //     FROM
        //     hr_leave_request_details
        //     JOIN hr_leave_requests ON hr_leave_requests.request_id = hr_leave_request_details.request_id
        //     JOIN employee_syncs ON employee_syncs.employee_id = hr_leave_request_details.employee_id
        //     WHERE
        //     hr_leave_requests.remark != 'Rejected'
        //     AND hr_leave_requests.purpose_category = 'PRIBADI'
        //     AND hr_leave_request_details.employee_id NOT LIKE 'OS%'
        //     AND hr_leave_requests.time_departure >= '" . $first_30 . "'
        //     AND hr_leave_requests.time_departure <= '" . $last_30 . "'
        //     AND employee_syncs.`group` NOT LIKE '%Driver%'
        //     GROUP BY
        //     employee_id, `name`");

        // $result_1 = array();

        // for ($i = 0; $i < count($attendances_1); $i++) {
        //     if ($attendances_1[$i]->sakit > 5 || $attendances_1[$i]->mangkir > 0 || $attendances_1[$i]->terlambat >= 3) {
        //         array_push($result_1, [
        //             'employee_id' => $attendances_1[$i]->emp_no,
        //             'name' => $attendances_1[$i]->full_name,
        //             'sakit' => $attendances_1[$i]->sakit,
        //             'mangkir' => $attendances_1[$i]->mangkir,
        //             'terlambat' => $attendances_1[$i]->terlambat,
        //             'izin' => 0,
        //         ]);
        //     }
        // }

        // for ($i = 0; $i < count($attendances_2); $i++) {
        //     if ($attendances_2[$i]->izin >= 5) {
        //         array_push($result_1, [
        //             'employee_id' => $attendances_2[$i]->employee_id,
        //             'name' => $attendances_2[$i]->name,
        //             'sakit' => 0,
        //             'mangkir' => 0,
        //             'terlambat' => 0,
        //             'izin' => $attendances_2[$i]->izin,
        //         ]);
        //     }

        // }

        // $result_2 = array();

        // for ($i = 0; $i < count($result_1); $i++) {
        //     $key = '';
        //     $key .= ($result_1[$i]['employee_id'] . '#');
        //     $key .= ($result_1[$i]['name'] . '#');

        //     if (!array_key_exists($key, $result_2)) {
        //         $row = array();
        //         $row['employee_id'] = $result_1[$i]['employee_id'];
        //         $row['name'] = $result_1[$i]['name'];
        //         $row['sakit'] = $result_1[$i]['sakit'];
        //         $row['mangkir'] = $result_1[$i]['mangkir'];
        //         $row['terlambat'] = $result_1[$i]['terlambat'];
        //         $row['izin'] = $result_1[$i]['izin'];

        //         $result_2[$key] = $row;
        //     } else {
        //         $result_2[$key]['sakit'] = $result_2[$key]['sakit'] + $result_1[$i]['sakit'];
        //         $result_2[$key]['mangkir'] = $result_2[$key]['mangkir'] + $result_1[$i]['mangkir'];
        //         $result_2[$key]['terlambat'] = $result_2[$key]['terlambat'] + $result_1[$i]['terlambat'];
        //         $result_2[$key]['izin'] = $result_2[$key]['izin'] + $result_1[$i]['izin'];
        //     }
        // }
        $mont_from = $request->get('mont_from');
        $mont_to = $request->get('mont_to');

        $from = '';
        $to = '';

        if ($mont_from == '' || $mont_to == '') {
            $from = '';
            $to = '';
        }else{
            $from = 'where DATE_FORMAT(created_at, "%Y-%m") >= "'.$mont_from.'"';
            $to = ' and DATE_FORMAT(created_at, "%Y-%m") <= "'.$mont_to.'"';
        }

        $condition = $from . $to;

        $data = db::connection('ympimis_2')->select('select *, DATE_FORMAT(created_at, "%M") as periode_bulan, DATE_FORMAT(created_at, "%Y") as periode_tahun, bukti_foto from problem_employees '.$condition.' order by id asc');
        $emp_sync = db::select('select * from employee_syncs where end_date is null');

        $response = array(
            'status' => true,
            'data' => $data,
            'emp_sync' => $emp_sync
            // 'violations' => $result_2,
            // 'first_30' => $first_30,
            // 'last_30' => $last_30,
        );
        return Response::json($response);
    }catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );  
        return Response::json($response); 
    }
}

public function FetchDataKaryawan(Request $request){
    try{
        $tgl_sekarang   = date('Y-m-d', strtotime("-7 day", strtotime(date('Y-m-d'))));
        $wc = db::table('weekly_calendars')->where('week_date', $tgl_sekarang)->select('week_name', 'fiscal_year')->first();
        $tgl = db::select('select week_date from weekly_calendars where fiscal_year = "'.$wc->fiscal_year.'" and week_name = "'.$wc->week_name.'"');

        $m = [];
        for ($i=0; $i < count($tgl); $i++) { 
            array_push($m, "'".$tgl[$i]->week_date."'");
        }

        $z = implode(',', $m);

        $data_table = db::connection('sunfish')->select("SELECT * FROM(SELECT emp_no, official_name, count(Emp_no) as p, Attend_Code from VIEW_YMPI_Emp_Attendance where FORMAT(shiftstarttime, 'yyyy-MM-dd') in (".$z.") and (Attend_Code LIKE '%sakit%' OR Attend_Code LIKE '%izin%') group by emp_no, official_name, Attend_Code) AS Q WHERE P >= 5 order by P desc, emp_no asc");

        $response = array(
            'status' => true,
            'data' => $data_table
        );
        return Response::json($response);
    }catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );  
        return Response::json($response); 
    }
}


public function EmailKaryawanBermasalah(){
    try{
        $perihal = 'Karyawan Bermasalah';
        $judul = 'Indikasi Karyawan Bermasalah';
        $tgl_sekarang   = date('Y-m-d', strtotime("-7 day", strtotime(date('Y-m-d'))));
        $wc = db::table('weekly_calendars')->where('week_date', $tgl_sekarang)->select('week_name', 'fiscal_year')->first();
        $tgl = db::select('select week_date from weekly_calendars where fiscal_year = "'.$wc->fiscal_year.'" and week_name = "'.$wc->week_name.'"');

        $m = [];
        for ($i=0; $i < count($tgl); $i++) { 
            array_push($m, "'".$tgl[$i]->week_date."'");
        }

        $z = implode(',', $m);

        $data_table = db::connection('sunfish')->select("SELECT * FROM(SELECT emp_no, official_name, count(Emp_no) as p, Attend_Code from VIEW_YMPI_Emp_Attendance where FORMAT(shiftstarttime, 'yyyy-MM-dd') in (".$z.") and (Attend_Code LIKE '%sakit%' OR Attend_Code LIKE '%izin%') group by emp_no, official_name, Attend_Code) AS Q WHERE P >= 5 order by P desc, emp_no asc");

        $data = [
            'perihal' => $perihal,
            'judul' => $judul,
            'data_detail' => $data_detail
        ];

        return view('mails.notif_karyawan_bermasalah', array(
            'data' => $data
        ));
    }catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function FetchKaryawanBermasalahDetail(Request $request){
    try{
        $nik = $request->get('nik');
        $tgl_sekarang   = date('Y-m-d', strtotime("-7 day", strtotime(date('Y-m-d'))));
        $wc = db::table('weekly_calendars')->where('week_date', $tgl_sekarang)->select('week_name', 'fiscal_year')->first();
        $tgl = db::select('select week_date from weekly_calendars where fiscal_year = "'.$wc->fiscal_year.'" and week_name = "'.$wc->week_name.'"');

        $m = [];
        for ($i=0; $i < count($tgl); $i++) { 
            array_push($m, "'".$tgl[$i]->week_date."'");
        }

        $z = implode(',', $m);

        $data_detail = db::connection('sunfish')->select("SELECT emp_no, shiftstarttime, Attend_Code from VIEW_YMPI_Emp_Attendance where FORMAT(shiftstarttime, 'yyyy-MM-dd') in (".$z.") and (Attend_Code LIKE '%sakit%' OR Attend_Code LIKE '%izin%') and emp_no = '".$nik."' order by shiftstarttime asc");

        $employee_sync = db::select('select employee_id, name, department, section, `group`, sub_group from employee_syncs where employee_id = "'.$nik.'"');

        $response = array(
            'status' => true,
            'data_detail' => $data_detail,
            'employee_syncs' => $employee_sync
        );
        return Response::json($response);
    }catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function TestMailKaryawanBermasalah(Request $request){
    try{
        $perihal = 'Karyawan Bermasalah';
        $judul = 'Indikasi Karyawan Bermasalah';
            // $tgl_sekarang   = date('Y-m-d');
            // $tgl_kemarin    = date('Y-m-d', strtotime("-7 day", strtotime(date('Y-m-d'))));
            // $status = 'Sakit';
            // $data_detail = db::connection('sunfish')->select("SELECT * FROM(SELECT emp_no, official_name, count(Emp_no) as p from VIEW_YMPI_Emp_Attendance where shiftstarttime >= '".$tgl_kemarin."' and shiftstarttime <= '".$tgl_sekarang."' and Attend_Code LIKE '%".$status."%' group by emp_no, official_name) AS Q WHERE P > 3 order by P desc");
        $tgl_sekarang   = date('Y-m-d', strtotime("-7 day", strtotime(date('Y-m-d'))));
        $wc = db::table('weekly_calendars')->where('week_date', $tgl_sekarang)->select('week_name', 'fiscal_year')->first();
        $tgl = db::select('select week_date from weekly_calendars where fiscal_year = "'.$wc->fiscal_year.'" and week_name = "'.$wc->week_name.'"');

        $m = [];
        for ($i=0; $i < count($tgl); $i++) { 
            array_push($m, "'".$tgl[$i]->week_date."'");
        }

        $z = implode(',', $m);

        $data_detail = db::connection('sunfish')->select("SELECT * FROM(SELECT emp_no, official_name, count(Emp_no) as p, Attend_Code from VIEW_YMPI_Emp_Attendance where FORMAT(shiftstarttime, 'yyyy-MM-dd') in (".$z.") and (Attend_Code LIKE '%sakit%' OR Attend_Code LIKE '%izin%') group by emp_no, official_name, Attend_Code) AS Q WHERE P >= 5 order by P desc, emp_no asc");

        $data = [
            'perihal' => $perihal,
            'judul' => $judul,
            'data_detail' => $data_detail
        ];


        if (count($data) > 0) {
            Mail::to(['mahendra.putra@music.yamaha.com'])
            ->cc(['lukmannul.arif@music.yamaha.com'])
                // ->bcc('ympi-mis-ML@music.yamaha.com')
            ->send(new SendEmail($data, 'notif_karyawan_bermasalah'));
        }

        $response = array(
            'status' => true,
            'data' => $data
        );
        return Response::json($response);
    }catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function IndexOperatorEndDate()
{
    $title = 'Employee End Contract';
    $title_jp = '';

    $department = db::select('select * from departments where department_name != "General Manager - Expatriate (GME)" order by department_name asc');
    $fy = db::select('select fiscal_year from weekly_calendars group by fiscal_year order by id desc');
    return view('hr_data.end_date.operator_end_date', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'department' => $department,
        'fy' => $fy
    ))->with('page', 'Employee End Contract');
}

public function FetchOperatorEndDate(Request $request){
    try{
        $kode = $request->get('kode');

        if ($kode == 'Cek Data Sunfish') {
            $department_sunfish = $request->get('department_sunfish');
            $month_join_sunfish = $request->get('month_join_sunfish');
            $month_end_sunfish = $request->get('month_end_sunfish');
            $jenis = $request->get('jenis');

            if ($jenis == 'Data Grafik') {
                if (count($department_sunfish) > 0) {
                    $dept = "where Department = '".$department_sunfish."'";
                }else{
                    $dept = "";
                }
                $bulan_join = "";
                    // $bulan_end = "and FORMAT(employment_enddate, 'yyyy-MM') = '".$month_end_sunfish."'";
                $bulan_end = "and IIF(A.end_date is null, FORMAT ( A.employment_enddate, 'yyyy-MM' ), FORMAT ( A.end_date, 'yyyy-MM' )) = '".$month_end_sunfish."'";
            }else{
                if (count($department_sunfish) > 0) {
                    $dept = "where Department = '".$department_sunfish."'";
                }else{
                    $dept = "";
                }

                if (count($month_join_sunfish) > 0) {
                    $bulan_join = "and IIF(A.end_date is null, FORMAT ( A.employment_enddate, 'yyyy-MM' ), FORMAT ( A.end_date, 'yyyy-MM' )) >= '".$month_join_sunfish."'";
                }else{
                    $bulan_join = "";
                }
                if (count($month_end_sunfish) > 0) {
                    $bulan_end = "and IIF(A.end_date is null, FORMAT ( A.employment_enddate, 'yyyy-MM' ), FORMAT ( A.end_date, 'yyyy-MM' )) <= '".$month_end_sunfish."'";
                }else{
                    $bulan_end = "";
                }
            }

                // $data = db::connection('sunfish')->select("SELECT B.Emp_no, Full_name, Department, Section, Groups, Sub_Groups, P.employ_code, FORMAT(employment_startdate, 'yyyy-MM-dd') as employment_startdate, FORMAT(employment_enddate, 'yyyy-MM-dd') as employment_enddate from (SELECT A.emp_no, A.employment_startdate, A.employ_code, A.employment_enddate FROM TEODEMPCOMPANY as A WHERE A.emp_no like '%PI%' and A.employ_code != 'PERMANENT' and end_date is null ".$bulan_join." ".$bulan_end.") as P left join VIEW_YMPI_Emp_OrgUnit as B on B.emp_no = P.emp_no ".$dept." order by FORMAT(employment_enddate, 'yyyy-MM-dd') asc");

            $data = db::connection('sunfish')->select("SELECT
                B.Emp_no,
                Full_name,
                Department,
                SECTION,
                Groups,
                Sub_Groups,
                P.employ_code,
                FORMAT ( P.start_date, 'yyyy-MM-dd' ) AS start_date,
                P.employment_enddate
                FROM
                (
                    SELECT
                    A.emp_no,
                    A.start_date,
                    A.employ_code,
                    IIF(A.end_date is null, FORMAT ( A.employment_enddate, 'yyyy-MM' ), FORMAT ( A.end_date, 'yyyy-MM' )) AS employment_enddate
                    FROM
                    TEODEMPCOMPANY AS A 
                    WHERE
                    A.emp_no LIKE '%PI%' 
                    AND A.employ_code != 'PERMANENT'
                    ".$bulan_join." ".$bulan_end."
                    ) AS P
                LEFT JOIN VIEW_YMPI_Emp_OrgUnit AS B ON B.emp_no = P.emp_no ".$dept."
                ORDER BY
                P.employment_enddate ASC");
        }else{
            $department_mirai = $request->get('department_mirai');
            $month_join_mirai = $request->get('month_join_mirai');
            $month_end_mirai = $request->get('month_end_mirai');

            if (count($department_mirai) > 0) {
                $dept_mirai = 'and department = "'.$department_mirai.'"';
            }else{
                $dept_mirai = '';
            }

            if (count($month_join_mirai) > 0) {
                $bulan_join_mirai = 'and date_format(hire_date, "%Y-%m") = "'.$month_join_mirai.'"';
            }else{
                $bulan_join_mirai = '';
            }

            if (count($month_end_mirai) > 0) {
                $bulan_end_mirai = 'and date_format(end_date, "%Y-%m") = "'.$month_end_mirai.'"';
            }else{
                $bulan_end_mirai = '';
            }

            $data = db::select('select * from employee_syncs where end_date is not null and employment_status != "PERMANENT" and employment_status != "OUTSOURCING" '.$dept_mirai.' '.$bulan_join_mirai.' '.$bulan_end_mirai.' order by hire_date asc');
        }

        $response = array(
            'status' => true,
            'data' => $data
        );
        return Response::json($response);
    }catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function GrafikOperatorEndDate(Request $request){
    try{
     $date_now = date('Y-m-d');
     $fy = db::select('SELECT fiscal_year FROM weekly_calendars WHERE week_date = "'.$date_now.'"');

     $p = '';
     if ( $request->get('fy') == null) {
         $p = $fy[0]->fiscal_year;
     }else{
         $p = $request->get('fy');
     }


            // $data = db::connection('sunfish')->select("SELECT COUNT ( A.employment_enddate ) AS jumlah, FORMAT ( A.employment_enddate, 'yyyy-MM' ) as bulan FROM TEODEMPCOMPANY AS A WHERE A.emp_no LIKE '%PI%' AND A.employ_code != 'PERMANENT' and A.employment_enddate is not null and end_date is null GROUP BY FORMAT ( A.employment_enddate, 'yyyy-MM' )");

     $data = db::connection('sunfish')->select("SELECT COUNT
        ( A.employment_enddate ) AS jumlah,
        IIF(end_date is null, FORMAT ( A.employment_enddate, 'yyyy-MM' ), FORMAT ( A.end_date, 'yyyy-MM' )) AS bulan 
        FROM
        TEODEMPCOMPANY AS A 
        WHERE
        A.emp_no LIKE '%PI%' 
        AND A.employ_code != 'PERMANENT' 
        AND A.employment_enddate IS NOT NULL
        GROUP BY
        IIF(end_date is null, FORMAT ( A.employment_enddate, 'yyyy-MM' ), FORMAT ( A.end_date, 'yyyy-MM' ))");


     $wc = db::select('SELECT DISTINCT date_format( week_date, "%Y-%m" ) as bulan FROM weekly_calendars WHERE fiscal_year = "'.$p.'" ORDER BY id ASC');

            // $data_mirai = db::select('SELECT COUNT( A.end_date ) AS jumlah,  DATE_FORMAT(A.end_date, "%Y-%m") as bulan FROM employee_syncs AS A WHERE A.employee_id LIKE "%PI%" AND A.employment_status != "PERMANENT" and A.end_date is not null AND DATE_FORMAT(A.end_date, "%Y") = "'.$year.'" GROUP BY DATE_FORMAT(A.end_date, "%Y-%m")');

     $response = array(
        'status' => true,
        'data' => $data,
                // 'data_mirai' => $data_mirai,
                // 'year' => $year,
        'wc' => $wc,
        'fy' => $p
    );
     return Response::json($response);
 }catch (Exception $e) {
    $response = array(
        'status' => false,
        'message' => $e->getMessage()
    );
    return Response::json($response); 
}
}

public function InsertEmployeeEndContrect(Request $request){
    try{
            // $first_next_month = date('Y-m', strtotime("-2 months"));
        $next_month = date('Y-m', strtotime("+1 months"));

        $emp_end_sunfish = db::connection('sunfish')->select("SELECT
            emp_no,
            IIF ( end_date IS NULL, FORMAT ( A.employment_enddate, 'yyyy-MM' ), FORMAT ( A.end_date, 'yyyy-MM' ) ) AS bulan 
            FROM
            TEODEMPCOMPANY AS A 
            WHERE
            A.emp_no LIKE '%PI%' 
            AND A.employ_code != 'PERMANENT' 
            AND A.employment_enddate IS NOT NULL 
            AND IIF ( end_date IS NULL, FORMAT ( A.employment_enddate, 'yyyy-MM' ), FORMAT ( A.end_date, 'yyyy-MM' ) ) = '".$next_month."'");

            // for ($i=0; $i < count($emp_end_sunfish); $i++) { 
            //     db::connection('ympimis_2')->table('endcontract_logs')
            //     ->insert([
            //         'employee_id' => $emp_end_sunfish[$i]->emp_no,
            //         'periode_end_contract' => $emp_end_sunfish[$i]->bulan,
            //         'created_at' => date('Y-m-d H:i:s'),
            //         'updated_at' => date('Y-m-d H:i:s')
            //     ]);
            // }

        $emp_sync = db::select('select * from employee_syncs where end_date is null');
        $emp_forecast = db::select('select sum(count) as jumlah, DATE_FORMAT(`month`, "%Y-%m") as bulan from request_adds where DATE_FORMAT(`month`, "%Y-%m") = "'.$next_month.'" GROUP BY `month`');
        $emp_aktual = db::select('select count(employee_id) as jumlah from employee_syncs where end_date is null');

        $kebutuhan = intval($emp_forecast[0]->jumlah) - $emp_aktual[0]->jumlah + count($emp_end_sunfish);

        $emp_putus_kontrak = count($emp_end_sunfish);
        $emp_forecast = intval($emp_forecast[0]->jumlah);
        $emp_kebutuhan = $kebutuhan;

        $data = array(
            'next_month' => $next_month,
            'data_sunfish' => $emp_end_sunfish,
            'data_mirai' => $emp_sync,
            'emp_putus_kontrak' => $emp_putus_kontrak,
            'emp_forecast' => $emp_forecast,
            'emp_kebutuhan' => $emp_kebutuhan,
            'bulan_depan' => date('F Y', strtotime($next_month))
        );

        $mail_to = [
            'dicky.kurniawan@music.yamaha.com',
                // 'linda.febrian@music.yamaha.com'
            'lukmannul.arif@music.yamaha.com'
        ];

            // Mail::to($mail_to)->cc(['khoirul.umam@music.yamaha.com'])->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($data, 'notif_end_contract'));
        Mail::to($mail_to)->send(new SendEmail($data, 'notif_end_contract'));

        $response = array(
            'status' => true
        );
        return Response::json($response);
    }catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function InputKonseling(Request $request){
    try{
        $id = $request->get('id_karyawan');
        $bukti_foto = $request->file('file_gambar');
        $konseling = $request->get('kejadian_karyawan');

        $test = $bukti_foto->getClientOriginalName();
        $extension = pathinfo($test, PATHINFO_EXTENSION);

        $nama_gambar = $id . '_bukti_konseling.' . $extension;
        $bukti_foto->move('hr/konseling_pelanggaran/', $nama_gambar);

        db::connection('ympimis_2')->table('problem_employees')->where('id', $id)->update([
            'bukti_foto' => $nama_gambar,
            'bukti_konseling' => $konseling,
            'bukti_tanggal' => date("Y-m-d")
        ]);

        $select_emp = db::connection('ympimis_2')->select('select id, name, bukti_foto from problem_employees where id = "'.$id.'"');


        $data = array(
           'select_emp' => $select_emp
       );

        $mail_to = [
            'lukmannul.arif@music.yamaha.com'
        ];

        // Mail::to($mail_to)->send(new SendEmail($data, 'notif_penanganan'));

        $response = array(
            'status' => true
        );
        
        return redirect('/index/karyawan_bermasalah')->with('status', 'Konseling Telah Dilakukan')->with('page', 'Success');
    }catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function InsertAnakKaryawan(Request $request){
    try{
        $emp_update = db::select('select employee_id from employee_updates');

        dd($emp_update);

        $response = array(
            'status' => true
        );
        
        return Response::json($response); 
    }catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function CustomField(Request $request){
    $title = 'Data Customfield Sunfish';
    $title_jp = '';

    return view('hr_data.index_data', array(
        'title' => $title,
        'title_jp' => $title_jp
    ))->with('page', 'Data Customfield Sunfish');
}

public function FetchCustomField(Request $request){
    try{
        $data = db::connection('sunfish')->select("SELECT
            o.emp_no,
            c.Full_Name,
            p.customfield1 AS penugasan,
            p.customfield5 AS jml_anak,
            p.customfield4 AS zona,
            p.customfield9 AS spsi,
            p.customfield10 AS spmi 
            FROM
            TEODEMPCUSTOMFIELD AS p
            LEFT JOIN TEODEMPCOMPANY AS o ON o.emp_id = p.emp_id
            LEFT JOIN VIEW_YMPI_Emp_OrgUnit AS c ON c.Emp_no = o.emp_no 
            WHERE
            o.emp_no LIKE 'PI%' 
            AND c.end_date IS NULL 
            ORDER BY
            o.emp_no ASC");

        $response = array(
            'status' => true,
            'data' => $data
        );
        
        return Response::json($response); 
    }catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function IndexPendaftaranKeluargaTambahan(){
    $title = 'Pendaftaran Keluarga Tambahan';
    $title_jp = '';

    // $curl = curl_init();
    // curl_setopt_array($curl, array(
    //     CURLOPT_URL => 'http://kalarau.net/api/v1/kodepos/prov/jawa%20barat',
    //     CURLOPT_RETURNTRANSFER => true,
    //     CURLOPT_ENCODING => '',
    //     CURLOPT_MAXREDIRS => 10,
    //     CURLOPT_TIMEOUT => 0,
    //     CURLOPT_FOLLOWLOCATION => true,
    //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //     CURLOPT_CUSTOMREQUEST => 'GET'
    // ));

    // $response = json_decode(curl_exec($curl));
    // dd($response);

    $emp = Auth::user()->username;
    $syc = db::table('employee_syncs')->where('employee_id', $emp)->first();

    return view('hr_data.index_bpjstk', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'emp' => $syc
    ))->with('page', 'Pendaftaran Keluarga Tambahan');
}

public function InsertDataBpjskes(Request $request){
    try{
        $employee_id = strtoupper(Auth::user()->username);
        $name = Auth::user()->name;
        
        $select_data = db::connection('ympimis_2')->select('select employee_id from employee_bpjskes where employee_id = "'.$employee_id.'"');

        $emp_bpjs = db::connection('ympimis_2')->select('select id, employee_id, name, bpjs_number from employee_bpjskes');

        for ($i=0; $i <= count($emp_bpjs); $i++) { 
            $employee_sync = db::select('select bpjs, card_id, birth_place, birth_date, gender, address from employee_syncs where employee_id = "'.$emp_bpjs[$i]->employee_id.'"');
            $hubungan = '';
            if ($employee_sync[0]->gender == 'L') {
                $hubungan = 'Suami';
            }else{
                $hubungan = 'Isteri';
            }

            db::connection('ympimis_2')->table('employee_detail_bpjskes')->insert([ 
                'id_reg' => $emp_bpjs[$i]->id, 
                'name' => $emp_bpjs[$i]->name,
                'bpjs_number' => $emp_bpjs[$i]->bpjs_number,
                'no_ktp' => $employee_sync[0]->card_id,
                'remark' => $hubungan,
                'status' => '1',
                'hubungan' => $hubungan,
                'tempat_lahir' => $employee_sync[0]->birth_place,
                'tanggal_lahir' => $employee_sync[0]->birth_date,
                'jenis_kelamin' => $employee_sync[0]->gender,
                'alamat' => $employee_sync[0]->address,
                'nama_faskes' => 'BPJS',
                'kelas_rawat' => 'Kelas I',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);        
        }



        // if (count($select_data) > 0) {
        //     db::connection('ympimis_2')->table('employee_bpjskes')
        //     ->where('employee_id', $employee_id)
        //     ->update([
        //         'bpjs_number' => $employee_sync[0]->bpjs,
        //         'updated_at' => date('Y-m-d H:i:s')
        //     ]);
        // }else{
        //     db::connection('ympimis_2')->table('employee_bpjskes')->insert([ 
        //         'employee_id' => $employee_id, 
        //         'name' => $name,
        //         'bpjs_number' => $employee_sync[0]->bpjs,
        //         'created_at' => date('Y-m-d H:i:s'),
        //         'updated_at' => date('Y-m-d H:i:s')
        //     ]);

        //     $id_reg = db::connection('ympimis_2')->select('select id from employee_bpjskes where employee_id = "'.$employee_id.'"');

        //     db::connection('ympimis_2')->table('employee_detail_bpjskes')->insert([ 
        //         'id_reg' => $id_reg[0]->id, 
        //         'name' => $name,
        //         'bpjs_number' => $employee_sync[0]->bpjs,
        //         'no_ktp' => $employee_sync[0]->card_id,
        //         'remark' => $hubungan,
        //         'status' => '1',
        //         'hubungan' => $hubungan,
        //         'tempat_lahir' => $employee_sync[0]->birth_place,
        //         'tanggal_lahir' => $employee_sync[0]->birth_date,
        //         'jenis_kelamin' => $employee_sync[0]->gender,
        //         'alamat' => $employee_sync[0]->address,
        //         'nama_faskes' => 'BPJS',
        //         'kelas_rawat' => 'Kelas I',
        //         'created_at' => date('Y-m-d H:i:s'),
        //         'updated_at' => date('Y-m-d H:i:s')
        //     ]);
        // }

        $response = array(
            'status' => true
        );
        
        return Response::json($response); 
    }catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function InsertTambahanBpjskes(Request $request){
    try{
        $employee_id = strtoupper(Auth::user()->username);
        $name = Auth::user()->name;

        $id_reg = db::connection('ympimis_2')->select('select id from employee_bpjskes where employee_id = "'.$employee_id.'"');

        // dd($request->file('upload_kk'));

        $draw_name = '';

        if ($request->file('upload_kk')) {
            $tujuan_upload = 'hr_bpjs';

            $draw = $request->file('upload_kk');
            $draw_file = $draw->getClientOriginalName();
            $draw_ext = pathinfo($draw_file, PATHINFO_EXTENSION);

            $draw_name = $request->get('no_kk').'.'.$draw_ext;
            $draw->move($tujuan_upload,$draw_name); 
        }

        $hubungan = '';
        if ($request->get('hubungan_keluarga') == 'KELUARGA TAMBAHAN ANAK') {
            $hubungan = 'ANAK';
        }else if ($request->get('hubungan_keluarga') == 'KELUARGA TAMBAHAN ISTRI') {
            $hubungan = 'ISTRI';
        }else if ($request->get('hubungan_keluarga') == 'KELUARGA TAMBAHAN SUAMI') {
            $hubungan = 'SUAMI';
        }else if ($request->get('hubungan_keluarga') == 'KELUARGA TAMBAHAN ORANG TUA/MERTUA') {
            $hubungan = 'ORANG TUA/MERTUA';
        }

        // else {
        //     $hubungan = $request->get('hubungan_keluarga');
        // }

        db::connection('ympimis_2')->table('employee_detail_bpjskes')->insert([ 
            'id_reg' => $id_reg[0]->id, 
            'name' => $request->get('nama_keluarga'),
            'bpjs_number' => $request->get('no_bpjs'),
            'remark' => $request->get('hubungan_keluarga'),
            'status' => '0',
            'no_kk' => $request->get('no_kk'),
            'no_ktp' => $request->get('no_ktp'),
            'hubungan' => $hubungan,
            'tempat_lahir' => $request->get('tempat_lahir'),
            'tanggal_lahir' => $request->get('tanggal_lahir'),
            'jenis_kelamin' => $request->get('jenis_kelamin'),
            'status_kawin' => $request->get('status_perkawinan'),
            'alamat' => $request->get('alamat'),
            'rt' => $request->get('rt'),
            'rw' => $request->get('rw'),
            'kode_post' => $request->get('kode_pos'),
            'kecamatan' => $request->get('kecamatan'),
            'kelurahan' => $request->get('kelurahan'),
            'nama_faskes' => $request->get('nama_faskes'),
            'kelas_rawat' => $request->get('kelas_rawat'),
            'upload_kk' => $draw_name,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $response = array(
            'status' => true
        );
        
        return Response::json($response); 
    }catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function FetchDataBpjskes(Request $request){
    try{
        $employee_id = strtoupper(Auth::user()->username);
        $id_reg = db::connection('ympimis_2')->select('select id from employee_bpjskes where employee_id = "'.$employee_id.'"');

        $jenis = $request->get('data');
        $resumes = "";

        if ($jenis == 'done') {
            $resumes = db::connection('ympimis_2')->select('select * from employee_detail_bpjskes where id_reg = "'.$id_reg[0]->id.'" and remark in ("SUAMI", "ISTRI", "ANAK", "Anggota") order by id ASC');
        }else if ($jenis == 'all'){
            $resumes = db::connection('ympimis_2')->select('select * from employee_detail_bpjskes where id_reg = "'.$id_reg[0]->id.'" order by id ASC');
        }else{
            $resumes = db::connection('ympimis_2')->select('select * from employee_detail_bpjskes where id_reg = "'.$id_reg[0]->id.'" and remark in ("KELUARGA TAMBAHAN", "KELUARGA TAMBAHAN ANAK", "KELUARGA TAMBAHAN ISTRI", "KELUARGA TAMBAHAN SUAMI", "KELUARGA TAMBAHAN ORANG TUA/MERTUA") and `status` = "0" order by id ASC');
        }

        $keluarga_inti = db::connection('ympimis_2')->select('select id from employee_detail_bpjskes where id_reg = "'.$id_reg[0]->id.'" and remark in ("SUAMI", "ISTRI", "ANAK", "Anggota")');
        $keluarga_tambahan = db::connection('ympimis_2')->select('select id from employee_detail_bpjskes where id_reg = "'.$id_reg[0]->id.'" and remark in ("KELUARGA TAMBAHAN", "KELUARGA TAMBAHAN ANAK", "KELUARGA TAMBAHAN ISTRI", "KELUARGA TAMBAHAN SUAMI", "KELUARGA TAMBAHAN ORANG TUA/MERTUA")');

        $report_waiting = db::connection('ympimis_2')->select('SELECT
            b.`name`,
            b.bpjs_number,
            no_ktp,
            hubungan,
            tempat_lahir,
            tanggal_lahir,
            jenis_kelamin,
            alamat,
            rt,
            rw,
            kelurahan,
            kecamatan,
            nama_faskes,
            kelas_rawat,
            a.`name` as karyawan,
            status,
            id_reg
            FROM
            employee_detail_bpjskes AS b
            LEFT JOIN employee_bpjskes AS a ON a.id = b.id_reg WHERE `status` = "0"');

        $report_done = db::connection('ympimis_2')->select('SELECT
            b.`name`,
            b.bpjs_number,
            no_ktp,
            hubungan,
            tempat_lahir,
            tanggal_lahir,
            jenis_kelamin,
            alamat,
            rt,
            rw,
            kelurahan,
            kecamatan,
            nama_faskes,
            kelas_rawat,
            a.`name` as karyawan,
            status,
            id_reg
            FROM
            employee_detail_bpjskes AS b
            LEFT JOIN employee_bpjskes AS a ON a.id = b.id_reg WHERE `status` = "1"');

        $response = array(
            'status' => true,
            'resumes' => $resumes,
            'keluarga_inti' => count($keluarga_inti),
            'keluarga_tambahan' => count($keluarga_tambahan),
            'report_waiting' => $report_waiting,
            'report_done' => $report_done
        );
        
        return Response::json($response); 
    }catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function FetchDataBpjskesAll(Request $request){
    try{
        $report_waiting = db::connection('ympimis_2')->select('SELECT
            b.`name`,
            b.bpjs_number,
            no_ktp,
            hubungan,
            tempat_lahir,
            tanggal_lahir,
            jenis_kelamin,
            alamat,
            rt,
            rw,
            kelurahan,
            kecamatan,
            nama_faskes,
            kelas_rawat,
            a.`name` as karyawan,
            status,
            id_reg,
            upload_kk,
            b.id
            FROM
            employee_detail_bpjskes AS b
            LEFT JOIN employee_bpjskes AS a ON a.id = b.id_reg WHERE `status` = "0"');

        $report_done = db::connection('ympimis_2')->select('SELECT
            b.`name`,
            b.bpjs_number,
            no_ktp,
            hubungan,
            tempat_lahir,
            tanggal_lahir,
            jenis_kelamin,
            alamat,
            rt,
            rw,
            kelurahan,
            kecamatan,
            nama_faskes,
            kelas_rawat,
            a.`name` as karyawan,
            status,
            id_reg,
            upload_kk,
            b.id
            FROM
            employee_detail_bpjskes AS b
            LEFT JOIN employee_bpjskes AS a ON a.id = b.id_reg WHERE `status` = "1"');

        $response = array(
            'status' => true,
            'report_waiting' => $report_waiting,
            'report_done' => $report_done
        );
        
        return Response::json($response); 
    }catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function indexLet()
{
    $title = 'Leader Training Evaluation';
    $title_jp = '職長教育の評価';

    $periode = DB::table('weekly_calendars')->select('fiscal_year')->distinct()->get();

    $point = DB::connection('ympimis_2')->table('hr_let_points')->get();

    if (Auth::user()->role_code == 'M' || Auth::user()->role_code == 'DGM' || Auth::user()->role_code == 'GM' || Auth::user()->role_code == 'D' || Auth::user()->role_code == 'M' || str_contains(Auth::user()->role_code,'HR') || str_contains(Auth::user()->role_code,'MIS') || Auth::user()->username == 'PI1110002') {
        return view('human_resource.let.index', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'periode' => $periode,
            'point' => $point,
            'role' => Auth::user()->role_code,
            'username' => Auth::user()->username,
            'name' => Auth::user()->name,
        ))->with('page', 'Leader Training Evaluation');
    }else{
        return view('404');
    }
}

public function fetchLet(Request $request)
{
    try {
        if ($request->get('periode') == '') {
            $fy = DB::table('weekly_calendars')->where('week_date',date('Y-m-d'))->select('fiscal_year')->distinct()->first();
            $periode = $fy->fiscal_year;
        }else{
            $periode = $request->get('periode');
        }
        $let_participants = DB::connection('ympimis_2')->table('hr_let_participants')->where('periode',$periode)->get();
        $let_results = DB::connection('ympimis_2')->table('hr_let_results')->where('periode',$periode)->get();
        $emp = EmployeeSync::join('departments','departments.department_name','employee_syncs.department')->get();
        $point = DB::connection('ympimis_2')->table('hr_let_points')->get();

        $response = array(
            'status' => true,
            'periode' => $periode,
            'let_participants' => $let_participants,
            'let_results' => $let_results,
            'point' => $point,
            'emp' => $emp,
        );
        return Response::json($response);
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

public function SendPengajuan(Request $request){
    try{
        $employee_id = strtoupper(Auth::user()->username);
        $name = Auth::user()->name;

        $karyawan = db::connection('ympimis_2')->table('employee_bpjskes')->where('employee_id', $employee_id)->first();
        $keluarga = db::connection('ympimis_2')->select('select * from employee_detail_bpjskes where name != "'.$name.'" and remark in ("KELUARGA TAMBAHAN", "KELUARGA TAMBAHAN ANAK", "KELUARGA TAMBAHAN ISTRI", "KELUARGA TAMBAHAN SUAMI", "KELUARGA TAMBAHAN ORANG TUA/MERTUA") and id_reg = "'.$karyawan->id.'"');

        $data = [
            'karyawan' => $karyawan,
            'keluarga' => $keluarga
        ];

        Mail::to('achmad.riski.bayu@music.yamaha.com')
        ->bcc(['ympi-mis-ML@music.yamaha.com'])
        ->send(new SendEmail($data, 'email_pengajuan_bpjskes'));

        $response = array(
            'status' => true,
        );
        
        return Response::json($response); 
    }catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function inputLetEvaluation(Request $request)
{
    try {
        $asesor_id = $request->get('asesor_id');
        $asesor_name = $request->get('asesor_name');
        $employee_id = $request->get('employee_id');
        $name = $request->get('name');
        $criteria = $request->get('criteria');
        $category = $request->get('category');
        $result = $request->get('result');
        $periode = $request->get('periode');
        $title = $request->get('title');

        if ($result == '0') {
            $datas = DB::connection('ympimis_2')->table('hr_let_results')->where('employee_id',$employee_id)->where('asesor_id',$asesor_id)->where('criteria',$criteria)->where('category',$category)->where('periode',$periode)->first();
            if ($datas) {
                $delete_data = DB::connection('ympimis_2')->table('hr_let_results')->where('employee_id',$employee_id)->where('asesor_id',$asesor_id)->where('criteria',$criteria)->where('category',$category)->where('periode',$periode)->delete();

                $response = array(
                    'status' => true,
                );
                return Response::json($response);
            }
        }else{
            $datas = DB::connection('ympimis_2')->table('hr_let_results')->where('employee_id',$employee_id)->where('asesor_id',$asesor_id)->where('criteria',$criteria)->where('category',$category)->where('periode',$periode)->first();
            if ($datas) {
                $delete_data = DB::connection('ympimis_2')->table('hr_let_results')->where('employee_id',$employee_id)->where('asesor_id',$asesor_id)->where('criteria',$criteria)->where('category',$category)->where('periode',$periode)->delete();
            }
        }

        $input = DB::connection('ympimis_2')->table('hr_let_results')->insert([
            'periode' => $periode,
            'assessment_date' => date('Y-m-d H:i:s'),
            'criteria' => $criteria,
            'category' => $category,
            'employee_id' => $employee_id,
            'name' => $name,
            'title' => $title,
            'result' => $result,
            'asesor_id' => $asesor_id,
            'asesor_name' => $asesor_name,
            'created_by' => Auth::user()->id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        $response = array(
            'status' => true,
        );
        return Response::json($response); 
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function IndexResumeAllHr()
{
    $title = 'Report Human Resource';
    $title_jp = '';

    return view('human_resource.resume_all_hr', array(
        'title' => $title,
        'title_jp' => $title_jp
    ))->with('page', 'Report Human Resource');
}

public function IndexReportBPJS()
{
    $title = 'Report BPJS';
    $title_jp = '';

    return view('human_resource.bpjs.report_bpjs', array(
        'title' => $title,
        'title_jp' => $title_jp
    ))->with('page', 'Report BPJS');
}

public function ReportConfirmationHR($id)
{
    $title = 'Report BPJS';
    $title_jp = '';
    $karyawan = db::connection('ympimis_2')->table('employee_bpjskes')->where('id', $id)->first();
    $keluarga = db::connection('ympimis_2')->select('select * from employee_detail_bpjskes where remark in ("KELUARGA TAMBAHAN", "KELUARGA TAMBAHAN ANAK", "KELUARGA TAMBAHAN ISTRI", "KELUARGA TAMBAHAN SUAMI", "KELUARGA TAMBAHAN ORANG TUA/MERTUA") and id_reg = "'.$id.'"');

    $data = [
        'karyawan' => $karyawan,
        'keluarga' => $keluarga
    ];

    return view('hr_data.mails_confirmation_bpjs', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'data' => $data
    ))->with('page', 'Report BPJS');
}

public function ConfirmationAnggotaBpjs(Request $request)
{
    try {
        if ($request->get('status') == 'Approved') {
            db::connection('ympimis_2')->table('employee_detail_bpjskes')
            ->where('id_reg', $request->get('id_reg'))
            ->update([
                'status' => '1',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $emp_id = db::connection('ympimis_2')->select('select employee_id from employee_bpjskes where id = "'.$request->get('id_reg').'"');

            $empwahr = EmployeeSync::where('employee_id', $emp_id[0]->employee_id)->first();
            if(substr($empwahr->phone, 0, 1) == '+' ){
                $phone = substr($empwahr->phone, 1, 15);
            }
            else if(substr($empwahr->phone, 0, 1) == '0'){
                $phone = "62".substr($empwahr->phone, 1, 15);
            }
            else{
                $phone = $empwahr->phone;
            }

            $message = 'Pengajuan%20anggota%20BPJS%20Kesehatan%20telah%20disetujui.%0A%0ASilahkan%20datang%20ke%20Achmad%20Riski%20Bayu%20(Bagian%20HR)%20untuk%20melakukan%20tanda%20tangan.%0A%0A%0AHR%20-%20Dept.';
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

            return redirect('/index/report/bpjs')->with('status', 'Success')->with('page', 'Human Resources');  
        }else{
            db::connection('ympimis_2')->table('employee_detail_bpjskes')
            ->where('id_reg', $request->get('id_reg'))
            ->update([
                'status' => '3',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $emp_id = db::connection('ympimis_2')->select('select employee_id from employee_bpjskes where id = "'.$request->get('id_reg').'"');

            $empwahr = EmployeeSync::where('employee_id', $emp_id[0]->employee_id)->first();
            if(substr($empwahr->phone, 0, 1) == '+' ){
                $phone = substr($empwahr->phone, 1, 15);
            }
            else if(substr($empwahr->phone, 0, 1) == '0'){
                $phone = "62".substr($empwahr->phone, 1, 15);
            }
            else{
                $phone = $empwahr->phone;
            }

            $message = 'Pengajuan%20Penambahan%20Anggota%20BPJS%20Anda%20Ditolak.%0A%0AHR%20-%20Dept.';
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
            
            return redirect('/index/report/bpjs')->with('status', 'Success')->with('page', 'Human Resources');
        }
        
        $response = array(
            'status' => true,
        );
        return Response::json($response); 
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}  

public function indexLetPointCheck()
{
    $title = 'Leader Training Point Check';
    $title_jp = '職長教育の確認項目';

    return view('human_resource.let.point_check', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'role' => Auth::user()->role_code,
    ))->with('page', 'Leader Training Point Check');
}

public function fetchLetPointCheck()
{
    try {
        $point_check = DB::connection('ympimis_2')->table('hr_let_points')->get();
        $response = array(
            'status' => true,
            'point_check' => $point_check
        );
        return Response::json($response); 
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function inputLetPointCheck(Request $request)
{
    try {
        $category = $request->get('category');
        $criteria = $request->get('criteria');
        $result_1 = $request->get('result_1');
        $result_2 = $request->get('result_2');
        $result_3 = $request->get('result_3');
        $result_4 = $request->get('result_4');

        $input = DB::connection('ympimis_2')->table('hr_let_points')->insert([
            'category' => $category,
            'criteria' => $criteria,
            'result_1' => $result_1,
            'result_2' => $result_2,
            'result_3' => $result_3,
            'result_4' => $result_4,
            'created_by' => Auth::user()->id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $response = array(
            'status' => true,
        );
        return Response::json($response); 
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function updateLetPointCheck(Request $request)
{
    try {
        $id = $request->get('id');
        $category = $request->get('category');
        $criteria = $request->get('criteria');
        $result_1 = $request->get('result_1');
        $result_2 = $request->get('result_2');
        $result_3 = $request->get('result_3');
        $result_4 = $request->get('result_4');

        $update = DB::connection('ympimis_2')->table('hr_let_points')->where('id',$id)->update([
            'category' => $category,
            'criteria' => $criteria,
            'result_1' => $result_1,
            'result_2' => $result_2,
            'result_3' => $result_3,
            'result_4' => $result_4,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $response = array(
            'status' => true,
        );
        return Response::json($response); 
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function deleteLetPointCheck(Request $request)
{
    try {
        $id = $request->get('id');

        $delete = DB::connection('ympimis_2')->table('hr_let_points')->where('id',$id)->delete();

        $response = array(
            'status' => true,
        );
        return Response::json($response); 
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function indexLetMaster()
{
    $title = 'Leader Training Master';
    $title_jp = '職長教育のマスター';

    $periode = DB::table("weekly_calendars")->select('fiscal_year')->distinct()->get();

    $emp = EmployeeSync::join('departments','departments.department_name','employee_syncs.department')->where('end_date',null)->get();

    return view('human_resource.let.master', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'role' => Auth::user()->role_code,
        'periode' => $periode,
        'periode2' => $periode,
        'periode3' => $periode,
        'emp' => $emp,
        'emp2' => $emp,
    ))->with('page', 'Leader Training Master');
}

public function fetchLetMaster(Request $request)
{
    try {
        $master = DB::connection('ympimis_2')->table('hr_let_participants');
        if ($request->get('periode') != '') {
            $master = $master->where('periode',$request->get('periode'));
        }
        $master = $master->get();
        $emp = EmployeeSync::join('departments','departments.department_name','employee_syncs.department')->get();

        $response = array(
            'status' => true,
            'master' => $master,
            'emp' => $emp,
        );
        return Response::json($response); 
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function inputLetMaster(Request $request)
{
    try {
        $periode = $request->get('periode');
        $employee = $request->get('employee');
        $title = $request->get('title');

        $input = DB::connection('ympimis_2')->table('hr_let_participants')->insert([
            'periode' => $periode,
            'title' => $title,
            'employee_id' => explode('_', $employee)[0],
            'name' => explode('_', $employee)[1],
            'created_by' => Auth::user()->id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $response = array(
            'status' => true,
        );
        return Response::json($response); 
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function updateLetMaster(Request $request)
{
    try {
        $id = $request->get('id');
        $periode = $request->get('periode');
        $employee = $request->get('employee');
        $title = $request->get('title');

        $update = DB::connection('ympimis_2')->table('hr_let_participants')->where('id',$id)->update([
            'periode' => $periode,
            'title' => $title,
            'employee_id' => explode('_', $employee)[0],
            'name' => explode('_', $employee)[1],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $response = array(
            'status' => true,
        );
        return Response::json($response); 
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function deleteLetMaster(Request $request)
{
    try {
        $id = $request->get('id');

        $delete = DB::connection('ympimis_2')->table('hr_let_participants')->where('id',$id)->delete();

        $response = array(
            'status' => true,
        );
        return Response::json($response); 
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function indexLetReport()
{
    $title = 'Leader Training Report';
    $title_jp = '職長教育の報告';

    $periode = DB::table("weekly_calendars")->select('fiscal_year')->distinct()->get();

    $emp = EmployeeSync::join('departments','departments.department_name','employee_syncs.department')->where('end_date',null)->get();

    return view('human_resource.let.report', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'role' => Auth::user()->role_code,
        'periode' => $periode,
        'emp' => $emp,
    ))->with('page', 'Leader Training Report');
}

public function fetchLetReport(Request $request)
{
    try {
        if ($request->get('periode') == '') {
            $fy = DB::table('weekly_calendars')->where('week_date',date('Y-m-d'))->select('fiscal_year')->distinct()->first();
            $periode = $fy->fiscal_year;
        }else{
            $periode = $request->get('periode');
        }
        $participant = DB::connection('ympimis_2')->table('hr_let_participants')->where('periode',$periode)->get();

        $report = DB::connection('ympimis_2')->table('hr_let_results')->where('periode',$periode)->get();

        $emp = EmployeeSync::join('departments','departments.department_name','employee_syncs.department')->get();

        $response = array(
            'status' => true,
            'report' => $report,
            'participant' => $participant,
            'emp' => $emp,
        );
        return Response::json($response); 
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function FetchDataBpjskesUpdate(Request $request)
{
    try {
        $data = db::connection('ympimis_2')->select('select * from employee_detail_bpjskes where id = "'.$request->get('param').'"');

        $response = array(
            'status' => true,
            'data' => $data
        );
        return Response::json($response); 
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function UpdateDetailDataBpjs(Request $request)
{
    try {
        $id = $request->get('id_update');
        $stt = $request->get('status_perkawinan_update');

        db::connection('ympimis_2')->table('employee_detail_bpjskes')->where('id', $id)->update([
            'no_kk' => $request->get('no_kk_update'),
            'name' => strtoupper($request->get('nama_keluarga_update')),
            'hubungan' => $request->get('hubungan_keluarga_update'),
            'bpjs_number' => $request->get('no_bpjs_update'),
            'no_ktp' => $request->get('no_ktp_update'),
            'tempat_lahir' => $request->get('tempat_lahir_update'),
            'tanggal_lahir' => $request->get('tanggal_lahir_update'),
            'jenis_kelamin' => $request->get('jenis_kelamin_update'),
            'status_kawin' => $request->get('status_perkawinan_update'),
            'alamat' => $request->get('alamat_update'),
            'rt' => $request->get('rt_update'),
            'rw' => $request->get('rw_update'),
            'kode_post' => $request->get('kode_pos_update'),
            'kecamatan' => $request->get('kecamatan_update'),
            'kelurahan' => $request->get('kelurahan_update'),
            'nama_faskes' => $request->get('nama_faskes_update'),
            'kelas_rawat' => 'Kelas 1',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $response = array(
            'status' => true
        );
        return Response::json($response); 
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function DeleteAnggotaTambahan(Request $request)
{
    try {
        $id = $request->get('id');

        db::connection('ympimis_2')->table('employee_detail_bpjskes')->where('id', $id)->delete();

        $response = array(
            'status' => true
        );
        return Response::json($response); 
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function OpenKK(Request $request)
{
    try {
        $id = $request->get('id');

        $resume = db::connection('ympimis_2')->table('employee_detail_bpjskes')->where('id', $id);

        $response = array(
            'status' => true,
            'resume' => $resume
        );
        return Response::json($response); 
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function BerkasTandaTangan(Request $request)
{
    try {
        $resume = db::connection('ympimis_2')->table('employee_detail_bpjskes')->where('id_reg', $request->get('id'))->whereIn('remark',  ['KELUARGA TAMBAHAN', 'KELUARGA TAMBAHAN ANAK', 'KELUARGA TAMBAHAN ISTRI', 'KELUARGA TAMBAHAN SUAMI', 'KELUARGA TAMBAHAN ORANG TUA/MERTUA'])->get();
        $judul = db::connection('ympimis_2')->table('employee_bpjskes')->where('id', $request->get('id'))->first();
        $date = date('d-m-Y');

        if (count($resume) > 0) {
            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'potrait');
            $pdf->loadView('human_resource.bpjs.pdf_ttd', array(
                'resume' => $resume,
                'judul' => $judul,
                'date' => $date
            ));
            $pdf->save(public_path() . "/hr_bpjs/HR Keluarga-".$judul->name.".pdf");

            $pdf1 = \App::make('dompdf.wrapper');
            $pdf1->getDomPDF()->set_option("enable_php", true);
            $pdf1->setPaper('A4', 'potrait');
            $pdf1->loadView('human_resource.bpjs.pdf_kk', array(
                'resume' => $resume
            ));
            $pdf1->save(public_path() . "/hr_bpjs/KK-".$judul->name.".pdf");

            $pdfFile1Path = public_path() . "/hr_bpjs/HR Keluarga-".$judul->name.".pdf";
            $pdfFile2Path = public_path() . "/hr_bpjs/KK-".$judul->name.".pdf";
            
            $merger = new Merger;
              $merger->addFile($pdfFile1Path);
              $merger->addFile($pdfFile2Path);
              $createdPdf = $merger->merge();

              $pathForTheMergedPdf = public_path() . "/hr_bpjs_fix/Data Fix ".$judul->name.".pdf";

              file_put_contents($pathForTheMergedPdf, $createdPdf);

              // return redirect("/hr_bpjs_fix/Data Fix ".$judul->name.".pdf");
              $response = array(
                'status' => true,
                'message' => 'Data Ditemukan',
                'judul' => $judul->name
            );
        }else{
            $response = array(
                'status' => false,
                'message' => 'Data Tidak Ditemukan'
            );
        }
        return Response::json($response); 
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function DownloadReportBpjs(Request $request)
{
    try {
        $dari_bulan = $request->get('dari_bulan');
        $sampai_bulan = $request->get('sampai_bulan');
        $kategori = $request->get('kategori');

        // $data = db::connection('ympimis_2')->select('SELECT * FROM employee_detail_bpjskes WHERE DATE_FORMAT( created_at, "%Y-%m" ) >= "'.$dari_bulan.'" AND DATE_FORMAT( created_at, "%Y-%m" ) <= "'.$sampai_bulan.'"');
        $resumes = db::connection('ympimis_2')->select("SELECT
            UPPER( eb.`name` ) AS employee,
            UPPER( ed.`name` ) AS nama,
            UPPER( ed.bpjs_number ) AS no_bpjs,
            UPPER( ed.remark ) AS remark,
            ed.no_kk,
            ed.no_ktp,
            UPPER( ed.hubungan ) AS hubungan,
            UPPER( ed.tempat_lahir ) AS tempat_lahir,
            ed.tanggal_lahir,
            ed.jenis_kelamin,
            ed.status_kawin,
            UPPER(CONCAT(ed.alamat, ' RT ', ed.rt, ' RW ', ed.rw)) AS alamat,
            ed.kode_post,
            UPPER( ed.kecamatan ) AS kecamatan,
            UPPER( ed.kelurahan ) AS kelurahan,
            UPPER( ed.nama_faskes ) AS nama_faskes,
            ed.kelas_rawat 
            FROM
            employee_detail_bpjskes AS ed
            LEFT JOIN employee_bpjskes AS eb ON eb.id = ed.id_reg 
            WHERE
            DATE_FORMAT( ed.created_at, '%Y-%m' ) >= '".$dari_bulan."' 
            AND DATE_FORMAT( ed.created_at, '%Y-%m' ) <= '".$sampai_bulan."' 
            AND ed.remark NOT IN ( 'SUAMI', 'ISTRI' ) 
            AND ed.upload_kk IS NOT NULL
            AND ed.status = '".$kategori."'");

        $data = array(
            'resumes' => $resumes
        );

        Excel::create('Report BPJS', function($excel) use ($data){
            $excel->sheet('Data Sheet 1', function($sheet) use ($data) {
                return $sheet->loadView('human_resource.bpjs.excel_report_data', $data);
            });
        })->export('xls');

        $response = array(
            'status' => true
        );
        return Response::json($response); 
    } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response); 
    }
}

public function indexTagsEmployee()
{    
    $department = db::table('departments')->select('department_shortname')->get();
    $section = db::table('sections')->select('section_name')->get();

    return view('human_resource.employee_tags.index', array(
        'title' => 'Employee Tags',
        'title_jp' => '??',        
        'departments' => $department,
        'sections' => $section
    ))->with('page', 'Employee Tags');
}

public function fetchTagsEmployee()
{
    $employee = EmployeeSync::    
    leftJoin('employees', 'employee_syncs.employee_id', '=', 'employees.employee_id')
    ->leftJoin('departments', 'employee_syncs.department', '=', 'departments.department_name');    
    
    $employee = $employee->where(function($query) {
        $query->where('employee_syncs.end_date', null)
              ->orWhere('employee_syncs.end_date', '>=', date('Y-m-d'));
    });

    $employee_unregistered = $employee
    ->where('employees.tag',null)
    ->select('employee_syncs.employee_id','employee_syncs.name','departments.department_shortname as department','employee_syncs.section','employee_syncs.group','employee_syncs.position','employees.tag')
    ->get();        

    $response = array(
        'status' => true,
        'employee_unregistered' => $employee_unregistered,        
    );

    return Response::json($response);
}

public function scanTagsEmployee(Request $request)
{        
    $tag = $request->get('tag');

    if($tag == null){
        $response = array(
            'status' => false,
            'message' => 'Tag Not Found'
        );
        return Response::json($response);
    }
    $tag = strtoupper($tag);

    try {        
        if (substr($tag, 0, 2) == 'PI' || substr($tag, 0, 2) == 'OS') {                        
            $employee = Employee::where('employees.employee_id',$tag);
        } else {
            $employee = Employee::where('employees.tag',$tag);
        }        

        $employee = $employee
        ->leftJoin('employee_syncs', 'employees.employee_id', '=', 'employee_syncs.employee_id')
        ->leftJoin('departments', 'employee_syncs.department', '=', 'departments.department_name')
        ->select('employee_syncs.employee_id','employee_syncs.name','departments.department_shortname as department','employee_syncs.section','employee_syncs.group','employee_syncs.position','employees.tag')
        ->first();        

        if($employee == null){
            $response = array(
                'status' => false,
                'message' => 'Tag Not Found'
            );
        }else{
            $response = array(
                'status' => true,
                'message' => 'Tag Found',
                'employee' => $employee
            );
        }   
    } catch (\Throwable $th) {
        $response = array(
            'status' => false,
            'message' => $th->getMessage()
        );
    }    

    return Response::json($response);
}

public function scanTagsEmployeeRegister(Request $request)
{
    // dd($request->all());

    $validator = Validator::make($request->all(), [
        'employee_id' => 'required',
        'tag' => 'required'
    ]);

    if ($validator->fails()) {
        $response = array(
            'status' => false,
            'message' => $validator->errors()->all()
        );
        return Response::json($response);
    }

    $tag = $request->get('tag');
    $employee_id = $request->get('employee_id');
    
    DB::beginTransaction();
    try {
        $employee = Employee::where('employee_id',$employee_id)->first();

        // if tag already registered in other employee id
        $employee_check = Employee::where('tag',$tag)->first();
        if($employee_check != null){
            $response = array(
                'status' => false,
                'message' => 'Tag Already Registered'
            );
            return Response::json($response);
        }
        

        if($employee == null){
            $response = array(
                'status' => false,
                'message' => 'Employee Not Found'
            );
        }else{
            $employee->tag = $tag;
            $employee->save();
            
            DB::commit();
            $response = array(
                'status' => true,
                'message' => 'Tag Registered'
            );

            return Response::json($response);
        }                

    } catch (\Throwable $th) {
        DB::rollback();
        $response = array(
            'status' => false,
            'message' => $th->getMessage()
        );    
        return Response::json($response);
    }   
}

public function scanTagsEmployeeAssign(Request $request)
{
    // dd($request->all());

    $validator = Validator::make($request->all(), [        
        'prev_employee_id' => 'required',
        'new_employee_id' => 'required',
        'tag' => 'required'
    ]);

    if ($validator->fails()) {
        $response = array(
            'status' => false,
            'message' => $validator->errors()->all()
        );
        return Response::json($response);
    }

    $tag = $request->get('tag');
    $prev_employee_id = $request->get('prev_employee_id');
    $new_employee_id = $request->get('new_employee_id');

    DB::beginTransaction();    
    try {
        $employee = Employee::where('employee_id',$prev_employee_id)->first();

        if($employee == null){
            $response = array(
                'status' => false,
                'message' => 'Employee Not Found'
            );
        }else{
            $employee->tag = null;
            $employee->save();

            $employee = Employee::where('employee_id',$new_employee_id)->first();

            if($employee == null){
                $response = array(
                    'status' => false,
                    'message' => 'Employee Not Found'
                );
            }else{
                $employee->tag = $tag;
                $employee->save();
                
                DB::commit();
                $response = array(
                    'status' => true,
                    'message' => 'Tag Assigned'
                );

                return Response::json($response);
            }                
        }                

    } catch (\Throwable $th) {
        DB::rollback();
        $response = array(
            'status' => false,
            'message' => $th->getMessage()
        );    
        return Response::json($response);
    }
}

public function deleteTagsEmployee(Request $request)
{
    // dd($request->all());

    $employee_id = $request->get('employee_id');

    try {
        $employee = Employee::where('employee_id',$employee_id)->first();

        if($employee == null){
            $response = array(
                'status' => false,
                'message' => 'Employee Not Found'
            );
        }else{
            $employee->tag = null;
            $employee->save();

            $response = array(
                'status' => true,
                'message' => 'Tag Deleted'
            );
        }                

    } catch (\Throwable $th) {
        $response = array(
            'status' => false,
            'message' => $th->getMessage()
        );    
    }

    return Response::json($response);



}

}