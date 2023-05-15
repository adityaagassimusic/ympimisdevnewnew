<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use Response;
use DataTables;
use PDF;
use File;

use App\User;

use App\Http\Controllers\Controller;
use App\EmployeeSync;
use App\SakurentsuTrialRequest;
use App\SakurentsuTrialRequestMaterial;
use App\Sakurentsu;
use App\SakurentsuTrialStatus;
use App\SakurentsuTrialRequestReceive;
use App\SakurentsuTrialRequestResult;
use App\SakurentsuTrialRequestBom;

use App\ApprSend;
use App\ApprApprovals;
use App\ApprMasters;

use App\ExtraOrderMaterial;
use App\ExtraOrderPrice;

use Carbon\Carbon;
use App\Mail\SendEmail;
use Mails;

class TrialRequestController extends Controller
{
    public function __construct(){
        $this->dgm = ['PI0109004', 'budhi.apriyanto@music.yamaha.com', 'Budhi Apriyanto'];
        $this->gm = ['PI1206001', 'yukitaka.hayakawa@music.yamaha.com', 'Yukitaka Hayakawa'];

        $this->dgm2 = ['PI9905001', 'mei.rahayu@music.yamaha.com', 'Mei Rahayu'];
        $this->gm2 = ['PI0109004', 'budhi.apriyanto@music.yamaha.com', 'Budhi Apriyanto'];
    }

    public function index_trial_request()
    {
        $title = 'Trial Request';
        $title_jp = '試作依頼';

        $emp_id = strtoupper(Auth::user()->username);
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
        ->first();

        $dept = EmployeeSync::whereNotNull('department')
        ->select('department')
        ->distinct()
        ->get();

        $section = EmployeeSync::whereNotNull('section')
        ->select('department', 'section')
        ->distinct()
        ->get();

        $pic = EmployeeSync::whereNull('end_date')
        ->whereIn('position', ['Leader', 'Chief', 'Foreman', 'Staff', 'Senior Staff'])
        ->select('employee_id', 'name')
        ->orderBy('name', 'asc')
        ->get();

        return view('sakurentsu.trial_request.master.index_trial', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'emp' => $emp,
            'dept' => $dept,
            'pic' => $pic,
            'section' => $section
        ))->with('page', 'Trial Request')
        ->with('head', 'Trial Request');
    }

    public function index_approval_trial_result($form_number, $section)
    {
        $title = 'Trial Request';
        $title_jp = '試作依頼';

        $trial_result = SakurentsuTrialRequestResult::where('trial_id', '=', $form_number)->select('department', 'section')->get();

        $bagian = EmployeeSync::where('employee_id', '=', Auth::user()->username)->select('department', 'section', 'position')->first();

        $status_appr = 0;

        foreach ($trial_result as $trial) {
            if ($bagian->position == 'Leader' || $bagian->position == 'Foreman' || $bagian->position == 'Chief' || $bagian->position == 'Senior Staff' || $bagian->position == 'Staff') {
                if ($trial->section == $bagian->section) {
                    $status_appr = 1;
                }
            }
        }

        return view('sakurentsu.trial_request.index_approval_result', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'approvals' => $status_appr
        ))->with('page', 'Trial Request Result Approval')
        ->with('head', 'Trial Request');
    }

    public function fetch_trial_request(){
        $dept = EmployeeSync::select('department')->where('employee_id', '=', Auth::user()->username)->first();
        $dept_status = "";

        if ($dept->department == 'Standardization Department' || $dept->department == 'Management Information System Department' || Auth::user()->username == 'pi9905001') {
            $dept_trial = "";
            if ($dept->department == 'Standardization Department') {
                $dept_status = 'QA';
            }
        } else {
            $dept_trial = "and employee_syncs.department = '".$dept->department."'";
        }


        $trial = db::select("SELECT sakurentsu_trial_requests.form_number, sakurentsu_trial_requests.position , sakurentsus.sakurentsu_number, sakurentsus.send_status, sakurentsus.file, sakurentsus.file_translate, sakurentsu_trial_requests.id, sakurentsu_trial_requests.submission_date, sakurentsu_trial_requests.qc_report_status, 
            sakurentsu_trial_requests.three_m_status,
            sakurentsu_trial_requests.subject, sakurentsu_trial_requests.requester_name, sakurentsu_trial_requests.trial_to_name, sakurentsu_trial_requests.department, sakurentsu_trial_requests.trial_purpose, sakurentsu_trial_requests.trial_date, sakurentsu_trial_requests.status_price, (select count(trial_id) from sakurentsu_trial_request_results where trial_id = form_number AND trial_method is not null ) as count_result, sakurentsu_trial_requests.status, sakurentsu_trial_requests.att FROM sakurentsu_trial_requests
            left join sakurentsus on sakurentsus.sakurentsu_number = sakurentsu_trial_requests.sakurentsu_number
            left join employee_syncs on employee_syncs.employee_id = sakurentsu_trial_requests.requester
            where sakurentsu_trial_requests.deleted_at is null 
            ".$dept_trial."
            order by sakurentsu_trial_requests.id desc
            ");


        $trial_sakurentsu = Sakurentsu::where('category', '=', 'Trial');

        if ($dept->department == 'Procurement Department') {
            $trial_sakurentsu = $trial_sakurentsu->where('pic', '=', 'Purchasing Control Department');
        } else {
            $trial_sakurentsu = $trial_sakurentsu->where('pic', '=', $dept->department);
        }

        $trial_sakurentsu = $trial_sakurentsu->where('position', '=', 'PIC2')
        ->where('status', '=', 'determined')
        ->select('sakurentsus.sakurentsu_number', 'sakurentsus.title', 'applicant', 'file_translate', 'upload_date', 'target_date', 'sakurentsus.status', 'pic')
        ->get();


        $response = array(
            'status' => true,
            'trial' => $trial,
            'sk_trial' => $trial_sakurentsu,
            'dept_status' => $dept_status
        );
        return Response::json($response);
    }

    public function fetch_trial_request_leader(){
        $section = EmployeeSync::where('employee_id', '=', Auth::user()->username)->select('section')->first();

        $trial = db::select("SELECT sakurentsu_trial_requests.form_number, sakurentsu_trial_requests.position , sakurentsu_trial_requests.sakurentsu_number, sakurentsu_trial_requests.id, sakurentsu_trial_requests.submission_date, sakurentsu_trial_requests.subject, sakurentsu_trial_requests.requester_name, sakurentsu_trial_requests.trial_to_name, sakurentsu_trial_requests.department, sakurentsu_trial_requests.trial_purpose, sakurentsu_trial_requests.trial_date, sakurentsu_trial_request_results.fill_by, sakurentsu_trial_request_results.section, sakurentsu_trial_requests.status FROM sakurentsu_trial_requests
            LEFT JOIN sakurentsu_trial_request_results on sakurentsu_trial_requests.form_number = sakurentsu_trial_request_results.trial_id
            where sakurentsu_trial_request_results.section = '".$section->section."'
            order by sakurentsu_trial_requests.status desc
            ");

        $response = array(
            'status' => true,
            'trial' => $trial
        );
        return Response::json($response);
    }

    public function index_trial_request_leader()
    {
        $title = 'Trial Request';
        $title_jp = '試作依頼';

        $emp_id = strtoupper(Auth::user()->username);
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

        return view('sakurentsu.trial_request.master.index_trial_leader', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Trial Request Leader')
        ->with('head', 'Trial Request');
    }

    public function create_trial_request(Request $request){
        try{
            $cf = null;
            $manager = null;
            $manager_name = null;
            $dgm = null;
            $gm = null;

            //form_number
            $tahun = date('y');
            $bulan = date('m');

            $query = "SELECT form_number FROM `sakurentsu_trial_requests` where DATE_FORMAT(created_at, '%y') = '$tahun' and month(created_at) = '$bulan' order by form_number DESC LIMIT 1";
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

            $form_number = 'TR'.$result['tahun'].$result['bulan'].$result['no_urut'];
            $requester = explode(' - ', $request->input('requester'))[0];

            $dept = EmployeeSync::where('employee_id', '=', $requester)
            ->select('division', 'department', 'section')
            ->first();

            $q_chief = db::select('SELECT username as employee_id, name from send_emails left join users on users.email = send_emails.email where send_emails.remark = "'.$dept->section.'" and username <> "PI1108003"');

            $chief = $q_chief[0]->employee_id.'/'.$q_chief[0]->name;

            $q_manager = db::select('select users.username, users.name from send_emails left join users on users.email = send_emails.email where remark = "'.$dept->department.'"');
            $manager = $q_manager[0]->username.'/'.$q_manager[0]->name;

            $manager_mecha = null;

            if ($dept->department == 'Production Engineering Department') {
                $q_manager_mecha = db::select('select employee_id, name from employee_syncs where employee_id = "PI1612005"');
                $manager_mecha = $q_manager_mecha[0]->employee_id.'/'.$q_manager_mecha[0]->name;
            }


            if ($dept->division == 'Production Division') {
                $dgm = $this->dgm[0].'/'.$this->dgm[2];
                $gm_pic = $this->gm[0].'/'.$this->gm[2];
                // TMP
                // $gm_pic = $this->dgm[0].'/'.$this->dgm[2];
            } else {
                $dgm = $this->dgm2[0].'/'.$this->dgm2[2];
                $gm_pic = $this->gm2[0].'/'.$this->gm2[2];
            }

            $get_div_trial = EmployeeSync::where('department', '=', $request->input('department'))
            ->select('division')
            ->groupBy('division')
            ->first();

            $dgm2 = null;
            $gm2 = null;

            if ($get_div_trial->division != $dept->division) {
                if ($dept->division == 'Production Division') {
                    $dgm2 = $this->dgm2[0].'/'.$this->dgm2[2];
                    $gm2 = $this->gm2[0].'/'.$this->gm2[2];
                } else {
                    $dgm2 = $this->dgm[0].'/'.$this->dgm[2];
                    $gm2 = $this->gm[0].'/'.$this->gm[2];
                    // TMP
                    // $gm2 = $this->dgm[0].'/'.$this->dgm[2];
                }
            }

            $q_to = db::select('select users.username, users.name from send_emails left join users on users.email = send_emails.email where send_emails.remark = "'.$request->get('department').'"');
            $trial_to = $q_to[0]->username;
            $trial_to_name = $q_to[0]->name;

            $att = null;
            $file_destination = 'uploads/sakurentsu/trial_req/att';
            $filenames = array();

            if ($request->get('att_count') > 0) {
                for ($i=0; $i < $request->get('att_count'); $i++) { 
                   $file = $request->file('att_'.$i);

                   $nama = $file->getClientOriginalName();

                   $filename = $form_number.'_'.$nama;

                   $file->move($file_destination, $filename);
                   array_push($filenames, $filename);
               }
           }

           if (count($filenames) > 0 ) {
            $att = implode(',', $filenames);
        }

        $trial = new SakurentsuTrialRequest([
            'form_number' => $form_number,
            'submission_date' => $request->input('submission_date'),
            'subject' => $request->input('subject'),
            'department' => $request->input('department'),
            'section' => $request->input('section'),
            'trial_before' => $request->input('kondisi_sebelum'),
            'requester' => $request->input('requester'),
            'requester_name' => $request->input('requester_name'),
            'trial_to' => $trial_to,
            'trial_to_name' => $trial_to_name,
            'trial_date' => $request->input('trial_date'),
            'apd_material' => $request->input('apd'),
            'sakurentsu_number' => $request->input('reference_no'),
            'trial_detail' => $request->input('trial'),
            'trial_purpose' => $request->input('trial_purpose'),
            'trial_location' => $request->input('trial_location'),
            'trial_info' => $request->input('trial_info'),
            'att' => $att,
            'chief' => $chief,
            'manager' => $manager,
            'manager_mechanical' => $manager_mecha,
            'dgm' => $dgm,
            'gm' => $gm_pic,
            'dgm2' => $dgm2,
            'gm2' => $gm2,
            'position' => 'user',
            'status' => 'approval',
            'created_by' => Auth::user()->username
        ]);

        $trial->save();


            //MATERIAL
        $arr_mat = explode(',', $request->get('material'));
        $arr_qty = explode(',', $request->get('jumlah'));

        for ($i=0; $i < count($arr_mat); $i++) {
            $trial_mat = new SakurentsuTrialRequestMaterial([
                'trial_request_id' => $trial->form_number,
                'material_name' => $arr_mat[$i],
                'quantity' => $arr_qty[$i],
                'created_by' => Auth::user()->username

            ]);
            $trial_mat->save();
        }

            //PENERIMA
        $arr_dept = explode(',', $request->get('department_receive'));
        $arr_sec = explode(',', $request->get('section_receive'));
        for ($i=0; $i < count($arr_dept); $i++) {
            $q_manager = db::select('select username, name from send_emails left join users on send_emails.email = users.email where send_emails.remark = "'.$arr_dept[$i].'"');

            $manager = $q_manager[0]->username.'/'.$q_manager[0]->name;

            $q_chief = db::select('select username, name from send_emails left join users on send_emails.email = users.email where send_emails.remark = "'.$arr_sec[$i].'"');

            $chief = $q_chief[0]->username.'/'.$q_chief[0]->name;

            $trial_terima = new SakurentsuTrialRequestReceive([
                'trial_id' => $trial->form_number,
                'sakurentsu_number' => $trial->sakurentsu_number,
                'trial_receive_department' => $arr_dept[$i],
                'trial_receive_section' => $arr_sec[$i],
                'position' => 'manager',
                'manager' => $manager,
                'chief' => $chief,
                'status' => 'approval',
                'created_by' => Auth::user()->username
            ]);
            $trial_terima->save();

        }

            // MAKE PDF

        $data_trial_req = SakurentsuTrialRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_trial_requests.requester')
        ->where('form_number', '=', $trial->form_number)
        ->select(db::raw('employee_syncs.department as pic_dept'),'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(trial_date, "%d %b %Y") trial_date'), 'sakurentsu_number', db::raw('DATE_FORMAT(submission_date,"%d %b %Y") submission_date'), 'trial_before', 'trial_detail', 'trial_purpose', 'trial_location', 'trial_info', 'requester', 'requester_name', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'), 'manager_mechanical', db::raw('DATE_FORMAT(manager_mechanical_date, "%d %b %Y") manager_mechanical_date'), 'dgm', db::raw('DATE_FORMAT(dgm_date, "%d %b %Y") dgm_date'), 'gm', db::raw('DATE_FORMAT(gm_date, "%d %b %Y") gm_date'), 'dgm2', db::raw('DATE_FORMAT(dgm_date2, "%d %b %Y") dgm_date2'), 'gm2', db::raw('DATE_FORMAT(gm_date2, "%d %b %Y") gm_date2'), 'form_number', 'trial_to_name', 'qc_report_status', 'apd_material')
        ->first();

        $data_trial_mat = SakurentsuTrialRequestMaterial::where('trial_request_id', '=', $trial->form_number)
        ->select('material_name', 'quantity')
        ->get();

        $data_trial_receive = SakurentsuTrialRequestReceive::where('trial_id', '=', $trial->form_number)
        ->select('trial_receive_department', 'trial_receive_section', 'perbaikan', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'))
        ->get();

        $data_trial_result = SakurentsuTrialRequestResult::where('trial_id', '=', $trial->form_number)
        ->select('trial_method', 'trial_date_start', 'trial_result', 'trial_date_finish', 'comment', 'trial_ok', 'status', 'fill_by')
        ->get();

        $dept = EmployeeSync::where('employee_id', '=', $data_trial_req->requester)->select('department')->first();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('sakurentsu.trial_request.issue_report_pdf', array(
            'data_trial' => $data_trial_req,
            'data_material' => $data_trial_mat,
            'data_receiver' => $data_trial_receive,
            'data_result' => $data_trial_result,
            'department' => $dept
        ));

        $pdf->save(public_path() . "/uploads/sakurentsu/trial_req/report/Report_".$trial->form_number.".pdf");

        if ($request->get("reference_no")) {
            Sakurentsu::where('sakurentsu_number', '=', $request->get("reference_no"))
            ->update([
                'status' => 'created'
            ]);

            SakurentsuTrialStatus::where('sakurentsu_number', '=', $request->get("reference_no"))
            ->update([
                'remark' => 'Trial Created',
                'form_number' => $form_number
            ]);
        } else {
            $trial_status = new SakurentsuTrialStatus([
                'form_number' => $form_number,
                'remark' => 'Trial Created',
                'proposer' => $request->input('requester_name'),
                'department' => $dept->department,
                'created_by' => Auth::user()->username
            ]);
            $trial_status->save();
        }

            // Extra Order

        if ($request->get("extra_order")) {
            ExtraOrderMaterial::where('id', '=', $request->get("extra_order"))
            ->update([
                'reference_form_number' => $form_number,
                'status' => 'Trial'
            ]);
        }

            //Send Mail

        $dept_user = SakurentsuTrialRequest::where('form_number', '=', $form_number)
        ->leftJoin('employee_syncs', 'employee_syncs.employee_id', 'sakurentsu_trial_requests.requester')
        ->select('employee_syncs.department')
        ->first();

        $section_user = EmployeeSync::where('department', $dept_user->department)->select('section')->groupBy('section')->get()->toArray();

        $mail = Mails::whereIn('remark', $section_user)->select('email')->where('email', '<>', 'andik.yayan@music.yamaha.com')->get();

        $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
        ->where('sakurentsu_trial_requests.form_number', '=', $form_number)
        ->select('sakurentsu_trial_requests.form_number','sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', db::raw('DATE_FORMAT(sakurentsus.target_date, "%d %M %Y") as target_date'), db::raw('DATE_FORMAT(sakurentsus.upload_date, "%d %M %Y") as upload_date'), 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'apd_material', 'att', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

        $data = [
            "datas" => $data_trial_req,
            "position" => 'Chief Foreman Issue'
        ];

        Mail::to($mail)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));

        $response = array(
            'status' => true,
            'message' => 'New Trial Request Successfully Added'
        );

        return Response::json($response);
    }
    catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage().' on Line '.$e->getLine()
        );
        return Response::json($response);
    }
}

public function approval_trial_request($id_trial, $position)
{
    $sk_trial = SakurentsuTrialRequest::where('form_number', '=', $id_trial)->first();

    if ($position == 'chief_issue') {

        if ($sk_trial->reject_date) {
            $title = "Approval Trial Request";
            $title_jp = "";

            return view('sakurentsu.trial_request.trial_message', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'data_trial' => $sk_trial
            ));
        }

        SakurentsuTrialRequest::where('form_number', '=', $id_trial)
        ->update([
            'chief' => Auth::user()->username.'/'.Auth::user()->name,
            'chief_date' => date('Y-m-d H:i:s'),
            'position' => 'chief'
        ]);

            // MAKE PDF

        $trial_req = SakurentsuTrialRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_trial_requests.requester')
        ->where('form_number', '=', $id_trial)
        ->select(db::raw('employee_syncs.department as pic_dept'),'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(trial_date, "%d %b %Y") trial_date'), 'sakurentsu_number', db::raw('DATE_FORMAT(submission_date,"%d %b %Y") submission_date'), 'trial_before', 'trial_detail', 'trial_purpose', 'trial_location', 'trial_info', 'requester', 'requester_name', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'), 'manager_mechanical', db::raw('DATE_FORMAT(manager_mechanical_date, "%d %b %Y") manager_mechanical_date'), 'dgm', db::raw('DATE_FORMAT(dgm_date, "%d %b %Y") dgm_date'), 'gm', db::raw('DATE_FORMAT(gm_date, "%d %b %Y") gm_date'), 'dgm2', db::raw('DATE_FORMAT(dgm_date2, "%d %b %Y") dgm_date2'), 'gm2', db::raw('DATE_FORMAT(gm_date2, "%d %b %Y") gm_date2'), 'form_number', 'trial_to_name', 'qc_report_status', 'apd_material')
        ->first();

        $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
        ->where('sakurentsu_trial_requests.form_number', '=', $id_trial)
        ->select('sakurentsu_trial_requests.form_number','sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', db::raw('DATE_FORMAT(sakurentsus.target_date, "%d %M %Y") as target_date') , db::raw('DATE_FORMAT(sakurentsus.upload_date, "%d %M %Y") as upload_date'), 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'apd_material', 'att', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

        $data_trial_mat = SakurentsuTrialRequestMaterial::where('trial_request_id', '=', $id_trial)
        ->select('material_name', 'quantity')
        ->get();

        $data_trial_receive = SakurentsuTrialRequestReceive::where('trial_id', '=', $id_trial)
        ->select('trial_receive_department', 'trial_receive_section', 'perbaikan', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'))
        ->get();

        $data_trial_result = SakurentsuTrialRequestResult::where('trial_id', '=', $id_trial)
        ->select('trial_method', 'trial_date_start', 'trial_result', 'trial_date_finish', 'comment', 'trial_ok', 'status', 'fill_by')
        ->get();

        $dept = EmployeeSync::where('employee_id', '=', $data_trial_req->requester)->select('department')->first();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('sakurentsu.trial_request.issue_report_pdf', array(
            'data_trial' => $trial_req,
            'data_material' => $data_trial_mat,
            'data_receiver' => $data_trial_receive,
            'data_result' => $data_trial_result,
            'department' => $dept
        ));

        $pdf->save(public_path() . "/uploads/sakurentsu/trial_req/report/Report_".$id_trial.".pdf");

            // SEND MAIL

        $data = [
            "datas" => $data_trial_req,
            "position" => 'Manager Issue'
        ];

        $manager = SakurentsuTrialRequest::where('form_number','=', $id_trial)->select('manager')->get();

        $mail = db::select('select email from users where username = "'.explode('/', $manager[0]->manager)[0].'"');

        Mail::to($mail[0]->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
    } else if ($position == 'manager_issue') {
        if ($sk_trial->reject_date) {
            $title = "Approval Trial Request";
            $title_jp = "";

            return view('sakurentsu.trial_request.trial_message', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'data_trial' => $sk_trial
            ));
        }

        SakurentsuTrialRequest::where('form_number', '=', $id_trial)
        ->update([                
            'manager_date' => date('Y-m-d H:i:s'),
            'position' => 'manager'
        ]);

        $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
        ->where('sakurentsu_trial_requests.form_number', '=', $id_trial)
        ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', db::raw('DATE_FORMAT(sakurentsus.target_date, "%d %M %Y") as target_date') , db::raw('DATE_FORMAT(sakurentsus.upload_date, "%d %M %Y") as upload_date'), 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'manager_mechanical', 'dgm', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

            // MAKE PDF

        $trial_req = SakurentsuTrialRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_trial_requests.requester')
        ->where('form_number', '=', $id_trial)
        ->select(db::raw('employee_syncs.department as pic_dept'),'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(trial_date, "%d %b %Y") trial_date'), 'sakurentsu_number', db::raw('DATE_FORMAT(submission_date,"%d %b %Y") submission_date'), 'trial_before', 'trial_detail', 'trial_purpose', 'trial_location', 'trial_info', 'requester', 'requester_name', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'), 'manager_mechanical', db::raw('DATE_FORMAT(manager_mechanical_date, "%d %b %Y") manager_mechanical_date'), 'dgm', db::raw('DATE_FORMAT(dgm_date, "%d %b %Y") dgm_date'), 'gm', db::raw('DATE_FORMAT(gm_date, "%d %b %Y") gm_date'), 'dgm2', db::raw('DATE_FORMAT(dgm_date2, "%d %b %Y") dgm_date2'), 'gm2', db::raw('DATE_FORMAT(gm_date2, "%d %b %Y") gm_date2'), 'form_number', 'trial_to_name', 'qc_report_status', 'apd_material', 'att')
        ->first();

        $data_trial_mat = SakurentsuTrialRequestMaterial::where('trial_request_id', '=', $id_trial)
        ->select('material_name', 'quantity')
        ->get();

        $data_trial_receive = SakurentsuTrialRequestReceive::where('trial_id', '=', $id_trial)
        ->select('trial_receive_department', 'trial_receive_section', 'perbaikan', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'))
        ->get();

        $data_trial_result = SakurentsuTrialRequestResult::where('trial_id', '=', $id_trial)
        ->select('trial_method', 'trial_date_start', 'trial_result', 'trial_date_finish', 'comment', 'trial_ok', 'status', 'fill_by')
        ->get();

        $dept = EmployeeSync::where('employee_id', '=', $data_trial_req->requester)->select('department')->first();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('sakurentsu.trial_request.issue_report_pdf', array(
            'data_trial' => $trial_req,
            'data_material' => $data_trial_mat,
            'data_receiver' => $data_trial_receive,
            'data_result' => $data_trial_result,
            'department' => $dept
        ));

        $pdf->save(public_path() . "/uploads/sakurentsu/trial_req/report/Report_".$id_trial.".pdf");

            // SEND MAIL

        if ($data_trial_req->manager_mechanical) {
            $data = [
                "datas" => $data_trial_req,
                "position" => 'Manager Mechanical'
            ];

            $mgr_mechanical = db::select('SELECT email from users where username = "'.explode('/', $data_trial_req->manager_mechanical)[0].'"');

            $mail = $mgr_mechanical[0]->email;
        } else {
            $data = [
                "datas" => $data_trial_req,
                "position" => 'DGM Issue'
            ];

            $dgm = db::select('SELECT email from users where username = "'.explode('/', $data_trial_req->dgm)[0].'"');

            $mail = $dgm[0]->email;
        }


        Mail::to($mail)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
    } else if ($position == 'manager_mechanical') {
        if ($sk_trial->reject_date) {
            $title = "Approval Trial Request";
            $title_jp = "";

            return view('sakurentsu.trial_request.trial_message', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'data_trial' => $sk_trial
            ));
        }

        SakurentsuTrialRequest::where('form_number', '=', $id_trial)
        ->update([                
            'manager_mechanical_date' => date('Y-m-d H:i:s'),
            'position' => 'manager_mechanical'
        ]);

        $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
        ->where('sakurentsu_trial_requests.form_number', '=', $id_trial)
        ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', db::raw('DATE_FORMAT(sakurentsus.target_date, "%d %M %Y") as target_date') , db::raw('DATE_FORMAT(sakurentsus.upload_date, "%d %M %Y") as upload_date'), 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'dgm', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

            // MAKE PDF

        $trial_req = SakurentsuTrialRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_trial_requests.requester')
        ->where('form_number', '=', $id_trial)
        ->select(db::raw('employee_syncs.department as pic_dept'),'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(trial_date, "%d %b %Y") trial_date'), 'sakurentsu_number', db::raw('DATE_FORMAT(submission_date,"%d %b %Y") submission_date'), 'trial_before', 'trial_detail', 'trial_purpose', 'trial_location', 'trial_info', 'requester', 'requester_name', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'), 'manager_mechanical', db::raw('DATE_FORMAT(manager_mechanical_date, "%d %b %Y") manager_mechanical_date'), 'dgm', db::raw('DATE_FORMAT(dgm_date, "%d %b %Y") dgm_date'), 'gm', db::raw('DATE_FORMAT(gm_date, "%d %b %Y") gm_date'), 'dgm2', db::raw('DATE_FORMAT(dgm_date2, "%d %b %Y") dgm_date2'), 'gm2', db::raw('DATE_FORMAT(gm_date2, "%d %b %Y") gm_date2'), 'form_number', 'trial_to_name', 'qc_report_status', 'apd_material', 'att')
        ->first();

        $data_trial_mat = SakurentsuTrialRequestMaterial::where('trial_request_id', '=', $id_trial)
        ->select('material_name', 'quantity')
        ->get();

        $data_trial_receive = SakurentsuTrialRequestReceive::where('trial_id', '=', $id_trial)
        ->select('trial_receive_department', 'trial_receive_section', 'perbaikan', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'))
        ->get();

        $data_trial_result = SakurentsuTrialRequestResult::where('trial_id', '=', $id_trial)
        ->select('trial_method', 'trial_date_start', 'trial_result', 'trial_date_finish', 'comment', 'trial_ok', 'status', 'fill_by')
        ->get();

        $dept = EmployeeSync::where('employee_id', '=', $data_trial_req->requester)->select('department')->first();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('sakurentsu.trial_request.issue_report_pdf', array(
            'data_trial' => $trial_req,
            'data_material' => $data_trial_mat,
            'data_receiver' => $data_trial_receive,
            'data_result' => $data_trial_result,
            'department' => $dept
        ));

        $pdf->save(public_path() . "/uploads/sakurentsu/trial_req/report/Report_".$id_trial.".pdf");

            // SEND MAIL

        $data = [
            "datas" => $data_trial_req,
            "position" => 'DGM Issue'
        ];

        $dgm = db::select('SELECT email from users where username = "'.explode('/', $data_trial_req->dgm)[0].'"');

        Mail::to($dgm[0]->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
    } else if ($position == 'dgm_issue') {
        if ($sk_trial->reject_date) {
            $title = "Approval Trial Request";
            $title_jp = "";

            return view('sakurentsu.trial_request.trial_message', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'data_trial' => $sk_trial
            ));
        }

        SakurentsuTrialRequest::where('form_number', '=', $id_trial)
        ->update([
            'dgm_date' => date('Y-m-d H:i:s'),
            'position' => 'dgm'
        ]);

        $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
        ->where('sakurentsu_trial_requests.form_number', '=', $id_trial)
        ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', db::raw('DATE_FORMAT(sakurentsus.target_date, "%d %M %Y") as target_date') , db::raw('DATE_FORMAT(sakurentsus.upload_date, "%d %M %Y") as upload_date'), 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'gm', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

            // MAKE PDF

        $trial_req = SakurentsuTrialRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_trial_requests.requester')
        ->where('form_number', '=', $id_trial)
        ->select(db::raw('employee_syncs.department as pic_dept'),'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(trial_date, "%d %b %Y") trial_date'), 'sakurentsu_number', db::raw('DATE_FORMAT(submission_date,"%d %b %Y") submission_date'), 'trial_before', 'trial_detail', 'trial_purpose', 'trial_location', 'trial_info', 'requester', 'requester_name', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'), 'manager_mechanical', db::raw('DATE_FORMAT(manager_mechanical_date, "%d %b %Y") manager_mechanical_date'), 'dgm', db::raw('DATE_FORMAT(dgm_date, "%d %b %Y") dgm_date'), 'gm', db::raw('DATE_FORMAT(gm_date, "%d %b %Y") gm_date'), 'dgm2', db::raw('DATE_FORMAT(dgm_date2, "%d %b %Y") dgm_date2'), 'gm2', db::raw('DATE_FORMAT(gm_date2, "%d %b %Y") gm_date2'), 'form_number', 'trial_to_name', 'qc_report_status', 'apd_material', 'att')
        ->first();

        $data_trial_mat = SakurentsuTrialRequestMaterial::where('trial_request_id', '=', $id_trial)
        ->select('material_name', 'quantity')
        ->get();

        $data_trial_receive = SakurentsuTrialRequestReceive::where('trial_id', '=', $id_trial)
        ->select('trial_receive_department', 'trial_receive_section', 'perbaikan', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'))
        ->get();

        $data_trial_result = SakurentsuTrialRequestResult::where('trial_id', '=', $id_trial)
        ->select('trial_method', 'trial_date_start', 'trial_result', 'trial_date_finish', 'comment', 'trial_ok', 'status', 'fill_by')
        ->get();

        $dept = EmployeeSync::where('employee_id', '=', $data_trial_req->requester)->select('department')->first();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('sakurentsu.trial_request.issue_report_pdf', array(
            'data_trial' => $trial_req,
            'data_material' => $data_trial_mat,
            'data_receiver' => $data_trial_receive,
            'data_result' => $data_trial_result,
            'department' => $dept
        ));

        $pdf->save(public_path() . "/uploads/sakurentsu/trial_req/report/Report_".$id_trial.".pdf");

            // SEND MAIL

        $data = [
            "datas" => $data_trial_req,
            "position" => 'GM Issue'
        ];

        $gm = db::select('SELECT email from users where username = "'.explode('/', $data_trial_req->gm)[0].'"');

        Mail::to($gm[0]->email)->bcc([ 'nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
    } else if ($position == 'gm_issue') {
        if ($sk_trial->reject_date) {
            $title = "Approval Trial Request";
            $title_jp = "";

            return view('sakurentsu.trial_request.trial_message', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'data_trial' => $sk_trial
            ));
        }

        SakurentsuTrialRequest::where('form_number', '=', $id_trial)
        ->update([
            'gm_date' => date('Y-m-d H:i:s'),
            'position' => 'gm'
        ]);

        $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
        ->where('sakurentsu_trial_requests.form_number', '=', $id_trial)
        ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', db::raw('DATE_FORMAT(sakurentsus.target_date, "%d %M %Y") as target_date') , db::raw('DATE_FORMAT(sakurentsus.upload_date, "%d %M %Y") as upload_date'), 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'gm','dgm2', 'gm2', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

        if ($data_trial_req->gm == $data_trial_req->dgm2) {
            SakurentsuTrialRequest::where('form_number', '=', $id_trial)
            ->update([
                'dgm_date2' => date('Y-m-d H:i:s'),
                'position' => 'dgm 2'
            ]);
        }

            // MAKE PDF

        $trial_req = SakurentsuTrialRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_trial_requests.requester')
        ->where('form_number', '=', $id_trial)
        ->select(db::raw('employee_syncs.department as pic_dept'),'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(trial_date, "%d %b %Y") trial_date'), 'sakurentsu_number', db::raw('DATE_FORMAT(submission_date,"%d %b %Y") submission_date'), 'trial_before', 'trial_detail', 'trial_purpose', 'trial_location', 'trial_info', 'requester', 'requester_name', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'), 'manager_mechanical', db::raw('DATE_FORMAT(manager_mechanical_date, "%d %b %Y") manager_mechanical_date'), 'dgm', db::raw('DATE_FORMAT(dgm_date, "%d %b %Y") dgm_date'), 'gm', db::raw('DATE_FORMAT(gm_date, "%d %b %Y") gm_date'), 'dgm2', db::raw('DATE_FORMAT(dgm_date2, "%d %b %Y") dgm_date2'), 'gm2', db::raw('DATE_FORMAT(gm_date2, "%d %b %Y") gm_date2'), 'form_number', 'trial_to_name', 'qc_report_status', 'apd_material')
        ->first();

        $data_trial_mat = SakurentsuTrialRequestMaterial::where('trial_request_id', '=', $id_trial)
        ->select('material_name', 'quantity')
        ->get();

        $data_trial_receive = SakurentsuTrialRequestReceive::where('trial_id', '=', $id_trial)
        ->select('trial_receive_department', 'trial_receive_section', 'perbaikan', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") as chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") as manager_date'))
        ->get();

        $data_trial_result = SakurentsuTrialRequestResult::where('trial_id', '=', $id_trial)
        ->select('trial_method', 'trial_date_start', 'trial_result', 'trial_date_finish', 'comment', 'trial_ok', 'status', 'fill_by')
        ->get();

        $dept = EmployeeSync::where('employee_id', '=', $data_trial_req->requester)->select('department')->first();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('sakurentsu.trial_request.issue_report_pdf', array(
            'data_trial' => $trial_req,
            'data_material' => $data_trial_mat,
            'data_receiver' => $data_trial_receive,
            'data_result' => $data_trial_result,
            'department' => $dept
        ));

        $pdf->save(public_path() . "/uploads/sakurentsu/trial_req/report/Report_".$id_trial.".pdf");

            // SEND MAIL

        if ($data_trial_req->dgm2) {
            if ($data_trial_req->gm == $data_trial_req->dgm2) {
             $data = [
                "datas" => $data_trial_req,
                "position" => 'GM 2 Issue'
            ];

            $gm2 = db::select('SELECT email from users where username = "'.explode('/', $data_trial_req->gm2)[0].'"');

            Mail::to($gm2[0]->email)->bcc([ 'nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
        } else {
            $data = [
                "datas" => $data_trial_req,
                "position" => 'DGM 2 Issue'
            ];

            $dgm2 = db::select('SELECT email from users where username = "'.explode('/', $data_trial_req->dgm2)[0].'"');

            Mail::to($dgm2[0]->email)->bcc([ 'nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
        }
    } else {
        if ($sk_trial->reject_date) {
            $title = "Approval Trial Request";
            $title_jp = "";

            return view('sakurentsu.trial_request.trial_message', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'data_trial' => $sk_trial
            ));
        }

        SakurentsuTrialRequest::where('form_number', '=', $id_trial)
        ->update([
            'status' => 'receiving'
        ]);

        SakurentsuTrialStatus::where('form_number', '=', $id_trial)
        ->update([
            'remark' => 'Trial InProgress',
        ]);

        $data = [
            "datas" => $data_trial_req,
            "position" => 'Manager Receiving Trial'
        ];

        $receiver = SakurentsuTrialRequestReceive::where('trial_id', '=', $id_trial)
        ->leftJoin('send_emails', 'sakurentsu_trial_request_receives.trial_receive_department', '=', 'send_emails.remark')
        ->select('trial_receive_department', 'trial_receive_section', 'send_emails.email')
        ->get();

        $cc = SakurentsuTrialRequest::where('form_number', '=', $id_trial)
        ->leftJoin('users', 'sakurentsu_trial_requests.created_by', '=', 'users.username')
        ->select('email')
        ->first();

        $mail = [];

        foreach ($receiver as $receive) {
            array_push($mail, $receive->email);
        }

        Mail::to($mail)->cc([$cc->email])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
    }
} else if ($position == 'dgm_2_issue') {
    if ($sk_trial->reject_date) {
        $title = "Approval Trial Request";
        $title_jp = "";

        return view('sakurentsu.trial_request.trial_message', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'data_trial' => $sk_trial
        ));
    }

    SakurentsuTrialRequest::where('form_number', '=', $id_trial)
    ->update([
        'gm_date' => date('Y-m-d H:i:s'),
        'position' => 'dgm 2'
    ]);

    $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
    ->where('sakurentsu_trial_requests.form_number', '=', $id_trial)
    ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', db::raw('DATE_FORMAT(sakurentsus.target_date, "%d %M %Y") as target_date') , db::raw('DATE_FORMAT(sakurentsus.upload_date, "%d %M %Y") as upload_date'), 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'gm2', 'dgm2', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

            // MAKE PDF

    $trial_req = SakurentsuTrialRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_trial_requests.requester')
    ->where('form_number', '=', $id_trial)
    ->select(db::raw('employee_syncs.department as pic_dept'),'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(trial_date, "%d %b %Y") trial_date'), 'sakurentsu_number', db::raw('DATE_FORMAT(submission_date,"%d %b %Y") submission_date'), 'trial_before', 'trial_detail', 'trial_purpose', 'trial_location', 'trial_info', 'requester', 'requester_name', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'), 'manager_mechanical', db::raw('DATE_FORMAT(manager_mechanical_date, "%d %b %Y") manager_mechanical_date'), 'dgm', db::raw('DATE_FORMAT(dgm_date, "%d %b %Y") dgm_date'), 'gm', db::raw('DATE_FORMAT(gm_date, "%d %b %Y") gm_date'), 'dgm2', db::raw('DATE_FORMAT(dgm_date2, "%d %b %Y") dgm_date2'), 'gm2', db::raw('DATE_FORMAT(gm_date2, "%d %b %Y") gm_date2'), 'form_number', 'trial_to_name', 'qc_report_status', 'apd_material', 'att')
    ->first();

    $data_trial_mat = SakurentsuTrialRequestMaterial::where('trial_request_id', '=', $id_trial)
    ->select('material_name', 'quantity')
    ->get();

    $data_trial_receive = SakurentsuTrialRequestReceive::where('trial_id', '=', $id_trial)
    ->select('trial_receive_department', 'trial_receive_section', 'perbaikan', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'))
    ->get();

    $data_trial_result = SakurentsuTrialRequestResult::where('trial_id', '=', $id_trial)
    ->select('trial_method', 'trial_date_start', 'trial_result', 'trial_date_finish', 'comment', 'trial_ok', 'status', 'fill_by')
    ->get();

    $dept = EmployeeSync::where('employee_id', '=', $data_trial_req->requester)->select('department')->first();

    $pdf = \App::make('dompdf.wrapper');
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper('A4', 'potrait');

    $pdf->loadView('sakurentsu.trial_request.issue_report_pdf', array(
        'data_trial' => $trial_req,
        'data_material' => $data_trial_mat,
        'data_receiver' => $data_trial_receive,
        'data_result' => $data_trial_result,
        'department' => $dept
    ));

    $pdf->save(public_path() . "/uploads/sakurentsu/trial_req/report/Report_".$id_trial.".pdf");

            // SEND MAIL

    $data = [
        "datas" => $data_trial_req,
        "position" => 'GM 2 Issue'
    ];

    $gm2 = db::select('SELECT email from users where username = "'.explode('/', $data_trial_req->gm2)[0].'"');

    Mail::to($gm2[0]->email)->bcc([ 'nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));

} else if ($position == 'gm_2_issue') {
    if ($sk_trial->reject_date) {
        $title = "Approval Trial Request";
        $title_jp = "";

        return view('sakurentsu.trial_request.trial_message', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'data_trial' => $sk_trial
        ));
    }
    
    SakurentsuTrialRequest::where('form_number', '=', $id_trial)
    ->update([
        'gm_date2' => date('Y-m-d H:i:s'),
        'position' => 'gm 2',
        'status' => 'receiving'
    ]);

    SakurentsuTrialStatus::where('form_number', '=', $id_trial)
    ->update([
        'remark' => 'Trial InProgress',
    ]);


    $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
    ->where('sakurentsu_trial_requests.form_number', '=', $id_trial)
    ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', db::raw('DATE_FORMAT(sakurentsus.target_date, "%d %M %Y") as target_date') , db::raw('DATE_FORMAT(sakurentsus.upload_date, "%d %M %Y") as upload_date'), 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

            // MAKE PDF

    $trial_req = SakurentsuTrialRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_trial_requests.requester')
    ->where('form_number', '=', $id_trial)
    ->select(db::raw('employee_syncs.department as pic_dept'),'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(trial_date, "%d %b %Y") trial_date'), 'sakurentsu_number', db::raw('DATE_FORMAT(submission_date,"%d %b %Y") submission_date'), 'trial_before', 'trial_detail', 'trial_purpose', 'trial_location', 'trial_info', 'requester', 'requester_name', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'), 'manager_mechanical', db::raw('DATE_FORMAT(manager_mechanical_date, "%d %b %Y") manager_mechanical_date'), 'dgm', db::raw('DATE_FORMAT(dgm_date, "%d %b %Y") dgm_date'), 'gm', db::raw('DATE_FORMAT(gm_date, "%d %b %Y") gm_date'), 'dgm2', db::raw('DATE_FORMAT(dgm_date2, "%d %b %Y") dgm_date2'), 'gm2', db::raw('DATE_FORMAT(gm_date2, "%d %b %Y") gm_date2'), 'form_number', 'trial_to_name', 'qc_report_status', 'apd_material', 'att')
    ->first();

    $data_trial_mat = SakurentsuTrialRequestMaterial::where('trial_request_id', '=', $id_trial)
    ->select('material_name', 'quantity')
    ->get();

    $data_trial_receive = SakurentsuTrialRequestReceive::where('trial_id', '=', $id_trial)
    ->select('trial_receive_department', 'trial_receive_section', 'perbaikan', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'))
    ->get();

    $data_trial_result = SakurentsuTrialRequestResult::where('trial_id', '=', $id_trial)
    ->select('trial_method', 'trial_date_start', 'trial_result', 'trial_date_finish', 'comment', 'trial_ok', 'status', 'fill_by')
    ->get();

    $dept = EmployeeSync::where('employee_id', '=', $data_trial_req->requester)->select('department')->first();

    $pdf = \App::make('dompdf.wrapper');
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper('A4', 'potrait');

    $pdf->loadView('sakurentsu.trial_request.issue_report_pdf', array(
        'data_trial' => $trial_req,
        'data_material' => $data_trial_mat,
        'data_receiver' => $data_trial_receive,
        'data_result' => $data_trial_result,
        'department' => $dept
    ));

    $pdf->save(public_path() . "/uploads/sakurentsu/trial_req/report/Report_".$id_trial.".pdf");

            // SEND MAIL

    $data = [
        "datas" => $data_trial_req,
        "position" => 'Manager Receiving Trial'
    ];


    $receiver = SakurentsuTrialRequestReceive::where('trial_id', '=', $id_trial)
    ->leftJoin('send_emails', 'sakurentsu_trial_request_receives.trial_receive_department', '=', 'send_emails.remark')
    ->select('trial_receive_department', 'trial_receive_section', 'send_emails.email')
    ->get();

    $cc = SakurentsuTrialRequest::where('form_number', '=', $id_trial)
    ->leftJoin('users', 'sakurentsu_trial_requests.created_by', '=', 'users.username')
    ->select('email')
    ->first();

    $mail = [];

    foreach ($receiver as $receive) {
        array_push($mail, $receive->email);
    }

    Mail::to($mail)->cc([$cc->email])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
}

$title = "Approval Trial Request";
$title_jp = "";

return view('sakurentsu.trial_request.trial_message', array(
    'title' => $title,
    'title_jp' => $title_jp,
    'data_trial' => $sk_trial
));
}

public function receive_trial_request($id_trial, $position)
{
    $stat = 'Permit';
    $sk_trial = SakurentsuTrialRequest::where('form_number', '=', $id_trial)->first();

    $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
    ->where('sakurentsu_trial_requests.form_number', '=', $id_trial)
    ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', db::raw('DATE_FORMAT(sakurentsus.target_date, "%d %M %Y") as target_date') , db::raw('DATE_FORMAT(sakurentsus.upload_date, "%d %M %Y") as upload_date'), 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location')->first();

    $receiver = SakurentsuTrialRequestReceive::where('trial_id', '=', $id_trial)->select('chief', 'manager')->get();
    $receiver_arr = [];

    foreach ($receiver as $rec) {
        $chief = strtoupper(explode('/', $rec->chief)[0]);
        $manager = strtoupper(explode('/', $rec->manager)[0]);

        array_push($receiver_arr, $chief);
        array_push($receiver_arr, $manager);
    }

    if (!in_array(strtoupper(Auth::user()->username), $receiver_arr)) {
        $stat = 'Not Permit';
    }

    $title = "Receiving Trial Request";
    $title_jp = "";

    return view('sakurentsu.trial_request.trial_receive_message', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'data_trial' => $data_trial_req,
        'status' => $stat
    ));
}

public function reject_trial_request($status, $id_trial, $position)
{
    $title = "Hold & Comment Trial Request";
    $title_jp = "";

    $data_trial = SakurentsuTrialRequest::where('form_number', $id_trial)->first();

    return view('sakurentsu.trial_request.trial_message', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'status' => $status,
        'id_trial' => $id_trial,
        'data_trial' => $data_trial
    ));
}

public function comment_trial_request(Request $request)
{
        // $title = "Hold & Comment Trial Request";
        // $title_jp = "";

    $trial = SakurentsuTrialRequest::where('form_number', '=', $request->get('form_number'))->first();
    $reject = "";

    if ($request->get('position') == 'chief_issue') {
        $reject = $trial->chief;
    } else if ($request->get('position') == 'manager_issue') {
        $reject = $trial->manager;
    } else if ($request->get('position') == 'manager_mechanical') {
        $reject = $trial->manager_mechanical;
    } else if ($request->get('position') == 'dgm_issue') {
        $reject = $trial->dgm;
    } else if ($request->get('position') == 'gm_issue') {
        $reject = $trial->gm;
    } else if ($request->get('position') == 'dgm_2_issue') {
        $reject = $trial->dgm2;
    } else if ($request->get('position') == 'gm_2_issue') {
        $reject = $trial->gm2;
    } 
    else  {
        $reject = strtoupper(Auth::user()->username).'/'.Auth::user()->name;
    } 

    SakurentsuTrialRequest::where('form_number', '=', $request->get('form_number'))
    ->update([
            // 'status' => date('Y-m-d H:i:s'),
        'reject' => $reject,
        'reject_reason' => $request->get('comment'),
        'reject_date' => date('Y-m-d H:i:s')
    ]);

    $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
    ->where('sakurentsu_trial_requests.form_number', '=', $request->get('form_number'))
    ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', db::raw('DATE_FORMAT(sakurentsus.target_date, "%d %M %Y") as target_date') , db::raw('DATE_FORMAT(sakurentsus.upload_date, "%d %M %Y") as upload_date'), 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'gm2', 'dgm2', 'reject', 'reject_reason', 'att', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

    $email = db::select('SELECT email from users where username = "'.$trial->requester.'"');

    $data = [
        "datas" => $data_trial_req,
        "position" => $request->get('status')
    ];

    Mail::to($email[0]->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));

    $response = array(
        'status' => true,
            // 'message' => 'New Trial Request Successfully Added'
    );

    return Response::json($response);
}

public function approval_receive_trial_request(Request $request)
{
    if ($request->get('position') == 'manager') {
        $emp = SakurentsuTrialRequestReceive::where('manager', 'LIKE', Auth::user()->username.'%')
        ->where('trial_id', '=', $request->get('id_trial'))
        ->select( 'trial_receive_department')
        ->get()
        ->toArray();

        SakurentsuTrialRequestReceive::where('trial_id', '=', $request->get('id_trial'))
        ->whereIn('trial_receive_department', $emp)
        ->update([
            'perbaikan' => $request->get('notes'),
            'manager_date' => date('Y-m-d H:i:s'),
            'position' => 'chief'
        ]);

            // MAKE PDF

        $trial_req = SakurentsuTrialRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_trial_requests.requester')
        ->where('form_number', '=', $request->get('id_trial'))
        ->select(db::raw('employee_syncs.department as pic_dept'),'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(trial_date, "%d %b %Y") trial_date'), 'sakurentsu_number', db::raw('DATE_FORMAT(submission_date,"%d %b %Y") submission_date'), 'trial_before', 'trial_detail', 'trial_purpose', 'trial_location', 'trial_info', 'requester', 'requester_name', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'), 'manager_mechanical', db::raw('DATE_FORMAT(manager_mechanical_date, "%d %b %Y") manager_mechanical_date'), 'dgm', db::raw('DATE_FORMAT(dgm_date, "%d %b %Y") dgm_date'), 'gm', db::raw('DATE_FORMAT(gm_date, "%d %b %Y") gm_date'), 'dgm2', db::raw('DATE_FORMAT(dgm_date2, "%d %b %Y") dgm_date2'), 'gm2', db::raw('DATE_FORMAT(gm_date2, "%d %b %Y") gm_date2'), 'form_number', 'trial_to_name', 'qc_report_status', 'apd_material')
        ->first();

        $data_trial_mat = SakurentsuTrialRequestMaterial::where('trial_request_id', '=', $request->get('id_trial'))
        ->select('material_name', 'quantity')
        ->get();

        $data_trial_receive = SakurentsuTrialRequestReceive::where('trial_id', '=', $request->get('id_trial'))
        ->select('trial_receive_department', 'trial_receive_section', 'perbaikan', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'))
        ->get();

        $data_trial_result = SakurentsuTrialRequestResult::where('trial_id', '=', $request->get('id_trial'))
        ->select('trial_method', 'trial_date_start', 'trial_result', 'trial_date_finish', 'comment', 'trial_ok', 'status', 'fill_by')
        ->get();

        $dept = EmployeeSync::where('employee_id', '=', $trial_req->requester)->select('department')->first();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('sakurentsu.trial_request.issue_report_pdf', array(
            'data_trial' => $trial_req,
            'data_material' => $data_trial_mat,
            'data_receiver' => $data_trial_receive,
            'data_result' => $data_trial_result,
            'department' => $dept
        ));

        $pdf->save(public_path() . "/uploads/sakurentsu/trial_req/report/Report_".$request->get('id_trial').".pdf");

        $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
        ->where('sakurentsu_trial_requests.form_number', '=', $request->get('id_trial'))
        ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', db::raw('DATE_FORMAT(sakurentsus.target_date, "%d %M %Y") as target_date') , db::raw('DATE_FORMAT(sakurentsus.upload_date, "%d %M %Y") as upload_date'), 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'att', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

        $data = [
            "datas" => $data_trial_req,
            "position" => 'Chief Receiving Trial'
        ];

        $receiver = SakurentsuTrialRequestReceive::where('trial_id', '=', $data_trial_req->form_number)
        ->whereIn('trial_receive_department', $emp)
        ->leftJoin('send_emails', 'sakurentsu_trial_request_receives.trial_receive_section', '=', 'send_emails.remark')
        ->select('trial_receive_department', 'trial_receive_section', 'send_emails.email')
        ->get();

        $mail = [];

        foreach ($receiver as $receive) {
            array_push($mail, $receive->email);
        }

        Mail::to($mail)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
    } else if ($request->get('position') == 'chief') {
        $emp = SakurentsuTrialRequestReceive::where('chief', 'LIKE', Auth::user()->username.'%')
        ->where('trial_id', '=', $request->get('id_trial'))
        ->select('trial_receive_section')
        ->first();

        if (count($emp) <= 0) {
            $response = array(
                'status' => true,
                'message' => 'Not Allowed'
            );

            return Response::json($response);
        }     

        SakurentsuTrialRequestReceive::where('trial_id', '=', $request->get('id_trial'))
        ->where('trial_receive_section', '=', $emp->trial_receive_section)
        ->update([
            'chief' => strtoupper(Auth::user()->username).'/'.Auth::user()->name,
            'position' => 'complete',
            'status' => 'received',
            'chief_date' => date('Y-m-d H:i:s')
        ]);

             // MAKE PDF

        $trial_req = SakurentsuTrialRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_trial_requests.requester')
        ->where('form_number', '=', $request->get('id_trial'))
        ->select(db::raw('employee_syncs.department as pic_dept'),'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(trial_date, "%d %b %Y") trial_date'), 'sakurentsu_number', db::raw('DATE_FORMAT(submission_date,"%d %b %Y") submission_date'), 'trial_before', 'trial_detail', 'trial_purpose', 'trial_location', 'trial_info', 'requester', 'requester_name', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'), 'manager_mechanical', db::raw('DATE_FORMAT(manager_mechanical_date, "%d %b %Y") manager_mechanical_date'), 'dgm', db::raw('DATE_FORMAT(dgm_date, "%d %b %Y") dgm_date'), 'gm', db::raw('DATE_FORMAT(gm_date, "%d %b %Y") gm_date'), 'dgm2', db::raw('DATE_FORMAT(dgm_date2, "%d %b %Y") dgm_date2'), 'gm2', db::raw('DATE_FORMAT(gm_date2, "%d %b %Y") gm_date2'), 'form_number', 'trial_to_name', 'qc_report_status', 'apd_material')
        ->first();

        $data_trial_mat = SakurentsuTrialRequestMaterial::where('trial_request_id', '=', $request->get('id_trial'))
        ->select('material_name', 'quantity')
        ->get();

        $data_trial_receive = SakurentsuTrialRequestReceive::where('trial_id', '=', $request->get('id_trial'))
        ->select('trial_receive_department', 'trial_receive_section', 'perbaikan', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'))
        ->get();

        $data_trial_result = SakurentsuTrialRequestResult::where('trial_id', '=', $request->get('id_trial'))
        ->select('trial_method', 'trial_date_start', 'trial_result', 'trial_date_finish', 'comment', 'trial_ok', 'status', 'fill_by')
        ->get();

        $dept = EmployeeSync::where('employee_id', '=', $trial_req->requester)->select('department')->first();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('sakurentsu.trial_request.issue_report_pdf', array(
            'data_trial' => $trial_req,
            'data_material' => $data_trial_mat,
            'data_receiver' => $data_trial_receive,
            'data_result' => $data_trial_result,
            'department' => $dept
        ));

        $pdf->save(public_path() . "/uploads/sakurentsu/trial_req/report/Report_".$request->get('id_trial').".pdf");

        $app_left = SakurentsuTrialRequestReceive::where('trial_id', '=', $request->get('id_trial'))
        ->whereNull('chief_date')
        ->get();

        if (count($app_left) == 0) {
            SakurentsuTrialRequest::where('form_number', '=', $request->get('id_trial'))
            ->update([
                'status' => 'received'
            ]);

            $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
            ->where('sakurentsu_trial_requests.form_number', '=', $request->get('id_trial'))
            ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', db::raw('DATE_FORMAT(sakurentsus.target_date, "%d %M %Y") as target_date') , db::raw('DATE_FORMAT(sakurentsus.upload_date, "%d %M %Y") as upload_date'), 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'att', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

            $data = [
                "datas" => $data_trial_req,
                "position" => 'User Receiving Trial',
                "status" => 'received'
            ];

            $requester = SakurentsuTrialRequest::where('form_number', '=', $request->get('id_trial'))
            ->leftJoin('users', 'sakurentsu_trial_requests.requester', '=', 'users.username')
            ->select('users.email')
            ->first();

            Mail::to([$requester->email])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
        }
    }

        // UPDATE REPORT

    $data_trial_req = SakurentsuTrialRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_trial_requests.requester')
    ->where('form_number', '=', $request->get('id_trial'))
    ->select(db::raw('employee_syncs.department as pic_dept'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(trial_date, "%d %b %Y") trial_date'), 'sakurentsu_number', db::raw('DATE_FORMAT(submission_date,"%d %b %Y") submission_date'), 'trial_before', 'trial_detail', 'trial_purpose', 'trial_location', 'trial_info', 'requester', 'requester_name', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager','manager_mechanical', db::raw('DATE_FORMAT(manager_mechanical_date, "%d %b %Y") manager_mechanical_date'), db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'), 'dgm', db::raw('DATE_FORMAT(dgm_date, "%d %b %Y") dgm_date'), 'gm', db::raw('DATE_FORMAT(gm_date, "%d %b %Y") gm_date'), 'dgm2', db::raw('DATE_FORMAT(dgm_date2, "%d %b %Y") dgm_date2'), 'gm2', db::raw('DATE_FORMAT(gm_date2, "%d %b %Y") gm_date2', 'form_number'), 'requester', 'trial_to_name', 'qc_report_status', 'apd_material')
    ->first();

    $data_trial_mat = SakurentsuTrialRequestMaterial::where('trial_request_id', '=', $request->get('id_trial'))
    ->select('material_name', 'quantity')
    ->get();

    $data_trial_receive = SakurentsuTrialRequestReceive::where('trial_id', '=', $request->get('id_trial'))
    ->select('trial_receive_department', 'trial_receive_section', 'perbaikan', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'))
    ->get();

    $data_trial_result = SakurentsuTrialRequestResult::where('trial_id', '=', $request->get('id_trial'))
    ->select('trial_method', 'trial_date_start', 'trial_result', 'trial_date_finish', 'comment', 'trial_ok', 'status', 'fill_by')
    ->first();

    $dept = EmployeeSync::where('employee_id', '=', $data_trial_req->requester)->select('department')->first();

    $pdf = \App::make('dompdf.wrapper');
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper('A4', 'potrait');

    $pdf->loadView('sakurentsu.trial_request.issue_report_pdf', array(
        'data_trial' => $data_trial_req,
        'data_material' => $data_trial_mat,
        'data_receiver' => $data_trial_receive,
        'data_result' => $data_trial_result,
        'department' => $dept
    ));

    $pdf->save(public_path() . "/uploads/sakurentsu/trial_req/report/Report_".$request->get('id_trial').".pdf");

    $response = array(
        'status' => true,
        'message' => 'OK'
    );

    return Response::json($response);
}

public function send_mail_trial_request_result(Request $request)
{
    try {
     $pic = $request->get('pic_arr');

     foreach ($pic as $leader) {
        $pic_id = explode('/', $leader)[0];

        $emp = EmployeeSync::where('employee_id', '=', $pic_id)->select('department', 'section')->first();


        $trial_result = SakurentsuTrialRequestResult::firstOrNew(array('trial_id' => $request->get('form_number'), 'fill_by' => $leader));
        $trial_result->sakurentsu_number = $request->get('sakurentsu_number');
        $trial_result->department = $emp->department;
        $trial_result->section = $emp->section;
        $trial_result->created_by = Auth::user()->username;
        $trial_result->save();
    }

    SakurentsuTrialRequest::where('form_number', '=', $request->get('form_number'))
    ->update([
        'status' => 'resulting',
            // 'three_m_status' => $request->get('three_m_requirement')
    ]);

    $response = array(
        'status' => true,
    );

    return Response::json($response);   
} catch (Exception $e) {
    $response = array(
        'status' => false,
        'message' => $e->getMessage().' On Line '.$e->getLine()
    );

    return Response::json($response);
}
}

public function update_trial_result(Request $request)
{
    try {
        // $get_bagian = EmployeeSync::where('employee_id', '=', Auth::user()->username)->select('department', 'section')->first();

        SakurentsuTrialRequestResult::where('trial_id', '=', $request->get('form_number'))
        ->where('section', '=', $request->get('section'))
        ->where('fill_by', '=', strtoupper(Auth::user()->username).'/'.Auth::user()->name)
        ->update([
            'trial_method' => $request->get('metode_trial'),
            'trial_result' => $request->get('hasil_trial'),
            'fill_by' => strtoupper(Auth::user()->username).'/'.Auth::user()->name,
        ]);

             // UPDATE REPORT

        $data_trial_req = SakurentsuTrialRequest::where('form_number', '=', $request->get('form_number'))
        ->select('department', db::raw('DATE_FORMAT(trial_date, "%d %b %Y") trial_date'), 'sakurentsu_number', db::raw('DATE_FORMAT(submission_date,"%d %b %Y") submission_date'), 'trial_before', 'trial_detail', 'trial_purpose', 'trial_location', 'trial_info', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'), 'dgm', db::raw('DATE_FORMAT(dgm_date, "%d %b %Y") dgm_date'), 'gm', db::raw('DATE_FORMAT(gm_date, "%d %b %Y") gm_date'), 'dgm2', db::raw('DATE_FORMAT(dgm_date2, "%d %b %Y") dgm_date2'), 'gm2', db::raw('DATE_FORMAT(gm_date2, "%d %b %Y") gm_date2', 'form_number'), 'trial_to_name', 'qc_report_status', 'apd_material')
        ->first();

        $data_trial_mat = SakurentsuTrialRequestMaterial::where('trial_request_id', '=', $request->get('form_number'))
        ->select('material_name', 'quantity')
        ->get();

        $data_trial_receive = SakurentsuTrialRequestReceive::where('trial_id', '=', $request->get('form_number'))
        ->select('trial_receive_department', 'trial_receive_section', 'perbaikan', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'))
        ->get();

        $data_trial_result = SakurentsuTrialRequestResult::where('trial_id', '=', $request->get('form_number'))
        ->select('trial_method', 'trial_date_start', 'trial_result', 'trial_date_finish', 'comment', 'trial_ok', 'status', 'fill_by')
        ->get();

        $dept = EmployeeSync::where('employee_id', '=', $data_trial_req->requester)->select('department')->first();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');


        $pdf->loadView('sakurentsu.trial_request.issue_report_pdf', array(
            'data_trial' => $data_trial_req,
            'data_material' => $data_trial_mat,
            'data_receiver' => $data_trial_receive,
            'data_result' => $data_trial_result,
            'department' => $dept
        ));

        $pdf->save(public_path() . "/uploads/sakurentsu/trial_req/report/Report_".$request->get('form_number').".pdf");

        // EMAIL NOTIF
        $status_appr = 0;
        foreach ($data_trial_result as $trial_r) {
            if (!$trial_r->trial_method) {
                $status_appr = 1;
            }
        }

        if ($status_appr == 0 && $data_trial_result[0]->status == '') {
            SakurentsuTrialRequest::where('form_number', '=', $request->get('form_number'))
            ->update([
                'status' => 'reporting',
            ]);

            SakurentsuTrialRequestResult::where('trial_id', '=', $request->get('form_number'))
            ->update([
                'status' => 'send',
            ]);

            $applicant = SakurentsuTrialRequest::where('form_number', '=', $request->get('form_number'))
            ->leftJoin('users', 'users.username', '=', 'sakurentsu_trial_requests.created_by')
            ->select('users.email')
            ->first();

            $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
            ->where('sakurentsu_trial_requests.form_number', '=', $request->get('form_number'))
            ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', 'sakurentsus.target_date', 'sakurentsus.upload_date', 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'att', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

            $data = [
                "datas" => $data_trial_req,
                "position" => 'Reporting Trial',
                "status" => 'Reporting'
            ];

            Mail::to(['abdissalam.saidi@music.yamaha.com', $applicant->email])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
        }

        $response = array(
            'status' => true,
            'message' => 'Data Saved Successfully'
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

public function upload_qc_report(Request $request)
{
    if ($request->file('qc_report_file') != NULL)
    {
        if ($files = $request->file('qc_report_file')) {
            $nama = 'QC_Report_'.$request->get('qc_form_id').'.pdf';
            $files->move('uploads/sakurentsu/qc_report', $nama);

            $file_name = $nama;
        }
    } else {
        $file_name = null;
    }

    $materials = SakurentsuTrialRequest::where('form_number', '=', $request->get('qc_form_id'))
    ->update([
        'qc_report_file' => $file_name,
        'qc_report_upload_date' => date('Y-m-d H:i:s'),
        'qc_report_status' => $request->get('qc_report_status'),
        'qc_report_uploaded_by' => Auth::user()->username.'/'.Auth::user()->name
    ]);

    if ($request->get('qc_report_status') != 'Approval') {
        // UPDATE REPORT

        $data_trial_req = SakurentsuTrialRequest::where('form_number', '=', $request->get('qc_form_id'))
        ->select('department', db::raw('DATE_FORMAT(trial_date, "%d %b %Y") trial_date'), 'sakurentsu_number', db::raw('DATE_FORMAT(submission_date,"%d %b %Y") submission_date'), 'trial_before', 'trial_detail', 'trial_purpose', 'trial_location', 'trial_info', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'), 'dgm', db::raw('DATE_FORMAT(dgm_date, "%d %b %Y") dgm_date'), 'gm', db::raw('DATE_FORMAT(gm_date, "%d %b %Y") gm_date'),  'dgm2', db::raw('DATE_FORMAT(dgm_date2, "%d %b %Y") dgm_date2'), 'gm2', db::raw('DATE_FORMAT(gm_date2, "%d %b %Y") gm_date2', 'form_number'), 'trial_to_name', 'qc_report_status', 'apd_material')
        ->first();

        if ($request->get('qc_report_status') != 'Not OK') {
            SakurentsuTrialRequest::where('form_number', '=', $request->get('qc_form_id'))
            ->update([
                'status' => '3m_need',
            ]);
        } else {
            SakurentsuTrialRequest::where('form_number', '=', $request->get('qc_form_id'))
            ->update([
                'status' => '3m_need',
                'three_m_status' => 'No Need 3M'
            ]);
        }


        $data_trial_mat = SakurentsuTrialRequestMaterial::where('trial_request_id', '=', $request->get('qc_form_id'))
        ->select('material_name', 'quantity')
        ->get();

        $data_trial_receive = SakurentsuTrialRequestReceive::where('trial_id', '=', $request->get('qc_form_id'))
        ->select('trial_receive_department', 'trial_receive_section', 'perbaikan', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'))
        ->get();

        $data_trial_result = SakurentsuTrialRequestResult::where('trial_id', '=', $request->get('qc_form_id'))
        ->select('trial_method', 'trial_date_start', 'trial_result', 'trial_date_finish', 'comment', 'trial_ok', 'status', 'fill_by')
        ->get();

        $dept = EmployeeSync::where('employee_id', '=', $data_trial_req->requester)->select('department')->first();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('sakurentsu.trial_request.issue_report_pdf', array(
            'data_trial' => $data_trial_req,
            'data_material' => $data_trial_mat,
            'data_receiver' => $data_trial_receive,
            'data_result' => $data_trial_result,
            'department' => $dept
        ));

        $pdf->save(public_path() . "/uploads/sakurentsu/trial_req/report/Report_".$request->get('qc_form_id').".pdf");

        if ($request->get('qc_report_status') != 'Not OK') {
             // Send Mail

            $applicant = SakurentsuTrialRequest::where('form_number', '=', $request->get('qc_form_id'))
            ->leftJoin('users', 'users.username', '=', 'sakurentsu_trial_requests.requester')
            ->select('users.email')
            ->first();

            $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
            ->where('sakurentsu_trial_requests.form_number', '=', $request->get('qc_form_id'))
            ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', 'sakurentsus.target_date', 'sakurentsus.upload_date', 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'att', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

            $data = [
                "datas" => $data_trial_req,
                "position" => 'Determine 3M',
            ];

            Mail::to([$applicant->email])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
        } else {
           // email notif  jika NOT OK dan CLose
           // close sakurentsu
        }
    }
}

public function approval_final_trial_request($id_trial, $position)
{
    $sk_trial = SakurentsuTrialRequest::where('form_number', '=', $id_trial)->first();

    if ($position == 'pic_receiver') {
        SakurentsuTrialRequest::where('form_number', '=', $id_trial)
        ->update([
            'app_pic_receive_date' => date('Y-m-d H:i:s'),
            'position' => 'chief_receive'
        ]);

        $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
        ->where('sakurentsu_trial_requests.form_number', '=', $id_trial)
        ->select('sakurentsu_trial_requests.form_number','sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', 'sakurentsus.target_date', 'sakurentsus.upload_date', 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'att', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

        $data = [
            "datas" => $data_trial_req,
            "position" => 'approval final chief receiver'
        ];

        $chief_receive = SakurentsuTrialRequest::where('form_number','=', $id_trial)->select('app_chief_receive')->get();

        $mail = db::select('select email from users where username = "'.explode('/', $chief_receive[0]->app_chief_receive)[0].'"');

        Mail::to($mail[0]->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
    } else if ($position == 'chief_receiver') {
        SakurentsuTrialRequest::where('form_number', '=', $id_trial)
        ->update([                
            'app_chief_receive_date' => date('Y-m-d H:i:s'),
            'position' => 'manager_receiver'
        ]);

        $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
        ->where('sakurentsu_trial_requests.form_number', '=', $id_trial)
        ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', 'sakurentsus.target_date', 'sakurentsus.upload_date', 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'att', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

        $data = [
            "datas" => $data_trial_req,
            "position" => 'approval final manager receiver'
        ];

        $manager_receive = SakurentsuTrialRequest::where('form_number','=', $id_trial)->select('app_manager_receive')->get();

        $mail = db::select('select email from users where username = "'.explode('/', $manager_receive[0]->app_manager_receive)[0].'"');

        Mail::to($mail[0]->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
    } else if ($position == 'manager_receiver') {
        SakurentsuTrialRequest::where('form_number', '=', $id_trial)
        ->update([
            'app_manager_receive_date' => date('Y-m-d H:i:s'),
            'position' => 'pic_request'
        ]);

        $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
        ->where('sakurentsu_trial_requests.form_number', '=', $id_trial)
        ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', 'sakurentsus.target_date', 'sakurentsus.upload_date', 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'att', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

        $data = [
            "datas" => $data_trial_req,
            "position" => 'approval final pic request'
        ];

        $pic_request = SakurentsuTrialRequest::where('form_number','=', $id_trial)->select('app_pic_request')->get();

        $mail = db::select('select email from users where username = "'.explode('/', $pic_request[0]->app_pic_request)[0].'"');

        Mail::to($mail[0]->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
    }  else if ($position == 'pic_request') {
        SakurentsuTrialRequest::where('form_number', '=', $id_trial)
        ->update([
            'app_pic_request_date' => date('Y-m-d H:i:s'),
            'position' => 'chief_request'
        ]);

        $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
        ->where('sakurentsu_trial_requests.form_number', '=', $id_trial)
        ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', 'sakurentsus.target_date', 'sakurentsus.upload_date', 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'att', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

        $data = [
            "datas" => $data_trial_req,
            "position" => 'approval final chief request'
        ];

        $chief_request = SakurentsuTrialRequest::where('form_number','=', $id_trial)->select('app_chief_request')->get();

        $mail = db::select('select email from users where username = "'.explode('/', $chief_request[0]->app_chief_request)[0].'"');

        Mail::to($mail[0]->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
    } else if ($position == 'chief_request') {
        SakurentsuTrialRequest::where('form_number', '=', $id_trial)
        ->update([
            'app_chief_request_date' => date('Y-m-d H:i:s'),
            'position' => 'manager_request'
        ]);

        $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
        ->where('sakurentsu_trial_requests.form_number', '=', $id_trial)
        ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', 'sakurentsus.target_date', 'sakurentsus.upload_date', 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'att', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

        $data = [
            "datas" => $data_trial_req,
            "position" => 'approval final chief request'
        ];

        $manager_request = SakurentsuTrialRequest::where('form_number','=', $id_trial)->select('app_manager_request')->get();

        $mail = db::select('select email from users where username = "'.explode('/', $manager_request[0]->app_manager_request)[0].'"');

        Mail::to($mail[0]->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
    } else if ($position == 'manager_request') {
        SakurentsuTrialRequest::where('form_number', '=', $id_trial)
        ->update([
            'app_manager_request_date' => date('Y-m-d H:i:s'),
            'position' => 'dgm'
        ]);

        $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
        ->where('sakurentsu_trial_requests.form_number', '=', $id_trial)
        ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', 'sakurentsus.target_date', 'sakurentsus.upload_date', 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'att', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

        $data = [
            "datas" => $data_trial_req,
            "position" => 'approval final dgm'
        ];

        $dgm = SakurentsuTrialRequest::where('form_number','=', $id_trial)->select('app_dgm')->get();

        $mail = db::select('select email from users where username = "'.explode('/', $dgm[0]->app_dgm)[0].'"');

        Mail::to($mail[0]->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
    } else if ($position == 'dgm') {
        SakurentsuTrialRequest::where('form_number', '=', $id_trial)
        ->update([
            'app_dgm_date' => date('Y-m-d H:i:s'),
            'position' => 'gm'
        ]);

        $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
        ->where('sakurentsu_trial_requests.form_number', '=', $id_trial)
        ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', 'sakurentsus.target_date', 'sakurentsus.upload_date', 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'att', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

        $data = [
            "datas" => $data_trial_req,
            "position" => 'aproval final gm'
        ];

        $gm1 = SakurentsuTrialRequest::where('form_number','=', $id_trial)->select('app_gm')->get();

        $mail = db::select('select email from users where username = "'.explode('/', $gm1[0]->app_gm)[0].'"');

        Mail::to($mail[0]->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
    } else if ($position == 'approval final gm') {
        SakurentsuTrialRequest::where('form_number', '=', $id_trial)
        ->update([
            'app_gm' => date('Y-m-d H:i:s'),
            'position' => 'gm2'
        ]);

        $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
        ->where('sakurentsu_trial_requests.form_number', '=', $id_trial)
        ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', 'sakurentsus.target_date', 'sakurentsus.upload_date', 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'att', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

        $data = [
            "datas" => $data_trial_req,
            "position" => 'approval final gm2'
        ];

        $gm2 = SakurentsuTrialRequest::where('form_number','=', $id_trial)->select('app_gm2')->get();

        $mail = db::select('select email from users where username = "'.explode('/', $gm2[0]->app_gm2)[0].'"');

        Mail::to($mail[0]->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
    } else if ($position == 'approval final gm2') {
        SakurentsuTrialRequest::where('form_number', '=', $id_trial)
        ->update([
            'app_gm2' => date('Y-m-d H:i:s'),
            'position' => 'complete',
            'status' => 'close',
        ]);

        $trial_request = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
        ->select('sakurentsus.title_jp', 'sakurentsu_trial_requests.sakurentsu_number', 'sakurentsu_trial_requests.three_m_status', 'sakurentsu_trial_requests.qc_report_status')
        ->where('form_number', '=', $id_trial)
        ->first();


        if ($trial_request->three_m_status == 'No Need 3M') {
            Sakurentsu::where('sakurentsu_number', '=', $trial_request->sakurentsu_number)
            ->update([
                'status' => 'close',
            ]);

            SakurentsuTrialRequest::where('form_number', '=', $id_trial)
            ->update([
                'status' => 'close' 
            ]);
        } else if ($trial_request->three_m_status == 'Need 3M') {
            SakurentsuTrialRequest::where('form_number', '=', $id_trial)
            ->update([
                'status' => '3M' 
            ]);
        }


        // $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
        // ->where('sakurentsu_trial_requests.form_number', '=', $id_trial)
        // ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', 'sakurentsus.target_date', 'sakurentsus.upload_date', 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location')->first();

        // $data = [
        //     "datas" => $data_trial_req,
        //     "position" => 'approval final gm2'
        // ];

        // $gm2 = SakurentsuTrialRequest::where('form_number','=', $id_trial)->select('app_gm2')->get();

        // $mail = db::select('select email from users where username = "'.explode('/', $gm2[0]->app_gm2)[0].'"');

        // Mail::to($mail[0]->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
    }

    $title = "Approval Trial Request";
    $title_jp = "";

    return view('sakurentsu.trial_request.trial_message', array(
        'title' => $title,
        'title_jp' => $title_jp
    ));
}

public function fetch_trial_detail_approval($form_number)
{
    $detail = db::select("SELECT CONCAT_WS('_',chief,manager,manager_mechanical,dgm,gm,dgm2,gm2) as approval, CONCAT_WS('_',chief_date,manager_date,manager_mechanical_date,dgm_date,gm_date,dgm_date2,gm_date2) as approved from sakurentsu_trial_requests
        where form_number = '".$form_number."'");

    $position = SakurentsuTrialRequest::where('form_number', '=', $form_number)->select('position')->first();


    $user = strtoupper(Auth::user()->username);
    $position_act = db::select("SELECT IF(chief LIKE '".$user."%','chief', IF(manager LIKE '".$user."%', 'manager', IF(manager_mechanical LIKE '".$user."%', 'manager mechanical', IF(dgm LIKE '".$user."%', 'dgm', IF(gm LIKE '".$user."%', 'gm',IF(dgm2 LIKE '".$user."%', 'dgm2', IF(gm2 LIKE '".$user."%', 'gm2', 'tidak ada'))))))) as posisi from sakurentsu_trial_requests where form_number = '".$form_number."'");

    $response = array(
        'status' => true,
        'detail_approval' => $detail,
        'position' => $position->position,
        'pos_act' => $position_act
    );

    return Response::json($response);
}

public function fetch_trial_detail_receive($form_number)
{
    $detail = db::select("SELECT trial_receive_department, trial_receive_section, perbaikan, chief, chief_date, manager, manager_date from sakurentsu_trial_request_receives
        where trial_id = '".$form_number."'");

    $response = array(
        'status' => true,
        'detail_approval' => $detail
    );

    return Response::json($response);
}

public function fetch_trial_detail_result($form_number)
{
    $detail = SakurentsuTrialRequestResult::where('trial_id', '=', $form_number)
    ->select('department', 'section', 'fill_by', 'trial_method', 'trial_result', db::raw('updated_at as fill_at'))
    ->get();

    $response = array(
        'status' => true,
        'detail_approval' => $detail
    );

    return Response::json($response);
}

public function update_three_m_status(Request $request)
{
    SakurentsuTrialRequest::where('form_number', '=', $request->get('form_id'))
    ->update([
        'three_m_status' => $request->get('three_m_status'),
    ]);

    $trial = SakurentsuTrialRequest::where('form_number', '=', $request->get('form_id'))->first();

    if ($request->get('three_m_status') == 'No Need 3M') {

        SakurentsuTrialRequest::where('form_number', '=', $request->get('form_id'))
        ->update([
            'three_m_status' => $request->get('three_m_status'),
            'status' => 'close'
        ]);

        $sakurentsu = Sakurentsu::where('sakurentsu_number', '=', $trial->sakurentsu_number)->first();

        if (count($sakurentsu) > 0) {
            $sk = Sakurentsu::where('sakurentsu_number', '=', $trial->sakurentsu_number)
            ->update([
                'status' => 'close',
            ]);
        }
    } else if ($request->get('three_m_status') == 'Need 3M') {
        SakurentsuTrialRequest::where('form_number', '=', $request->get('form_id'))
        ->update([
            'three_m_status' => $request->get('three_m_status'),
            'status' => '3M'
        ]);
    }


    SakurentsuTrialRequest::where('form_number', '=', $request->get('form_id'))
    ->update([
        'position' => 'finish',
    ]);


    $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
    ->where('sakurentsu_trial_requests.form_number', '=', $request->get('form_id'))
    ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', 'sakurentsus.target_date', 'sakurentsus.upload_date', 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'three_m_status', 'chief', 'manager', 'manager_mechanical', 'dgm', 'gm', 'dgm2', 'gm2', 'att', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

    $data = [
        "datas" => $data_trial_req,
        "position" => 'Trial Close'
    ];

    $data_trial_receive = SakurentsuTrialRequestReceive::where('trial_id', '=', $request->get('form_id'))
    ->select('chief', 'manager')
    ->get();

    $email = [];

    $gm2 = db::select("SELECT email FROM users where username = '".explode('/', $data_trial_req->gm2)[0]."'");
    if (count($gm2) > 0) {
        array_push($email, $gm2[0]->email);
    }

    $gm = db::select("SELECT email FROM users where username = '".explode('/', $data_trial_req->gm)[0]."'");
    array_push($email, $gm[0]->email);

    $dgm2 = db::select("SELECT email FROM users where username = '".explode('/', $data_trial_req->dgm2)[0]."'");
    if (count($dgm2) > 0) {
        array_push($email, $dgm2[0]->email);
    }

    $dgm = db::select("SELECT email FROM users where username = '".explode('/', $data_trial_req->dgm)[0]."'");
    array_push($email, $dgm[0]->email);

    $manager_mechanical = db::select("SELECT email FROM users where username = '".explode('/', $data_trial_req->manager_mechanical)[0]."'");
    if (count($manager_mechanical) > 0) {
        array_push($email, $manager_mechanical[0]->email);
    }

    $manager = db::select("SELECT email FROM users where username = '".explode('/', $data_trial_req->manager)[0]."'");
    array_push($email, $manager[0]->email);

    $chief = db::select("SELECT email FROM users where username = '".explode('/', $data_trial_req->chief)[0]."'");
    array_push($email, $chief[0]->email);

    // ------- PENERIMA -----
    foreach ($data_trial_receive as $receiver) {
        $chief2 = db::select("SELECT email FROM users where username = '".explode('/', $receiver->chief)[0]."'");
        array_push($email, $chief2[0]->email);

        $manager2 = db::select("SELECT email FROM users where username = '".explode('/', $receiver->manager)[0]."'");
        array_push($email, $manager2[0]->email);
    }

    $email_pembuat = db::select("SELECT email FROM users where username = '".$data_trial_req->requester."'");
    array_push($email, $email_pembuat[0]->email);

    Mail::to($email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));

    $response = array(
        'status' => true,
    );

    return Response::json($response);
}

public function test_pdf($trial_id)
{
    $trial = $trial_id;

    $data_trial_req = SakurentsuTrialRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_trial_requests.requester')
    ->where('form_number', '=', $trial)
    ->select(db::raw('employee_syncs.department as pic_dept'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(trial_date, "%d %b %Y") trial_date'), 'sakurentsu_number', 'requester_name', db::raw('DATE_FORMAT(submission_date,"%d %b %Y") submission_date'), 'trial_before', 'trial_detail', 'trial_purpose', 'trial_location', 'trial_info', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'), 'dgm', db::raw('DATE_FORMAT(dgm_date, "%d %b %Y") dgm_date'), 'gm', db::raw('DATE_FORMAT(gm_date, "%d %b %Y") gm_date'), 'dgm2', db::raw('DATE_FORMAT(dgm_date2, "%d %b %Y") dgm_date2'), 'gm2', db::raw('DATE_FORMAT(gm_date2, "%d %b %Y") gm_date2', 'form_number'), 'requester', 'trial_to_name', 'qc_report_status', 'manager_mechanical', db::raw('DATE_FORMAT(manager_mechanical_date, "%d %b %Y") manager_mechanical_date'), 'apd_material')
    ->first();

    $data_trial_mat = SakurentsuTrialRequestMaterial::where('trial_request_id', '=', $trial)
    ->select('material_name', 'quantity')
    ->get();

    $data_trial_receive = SakurentsuTrialRequestReceive::where('trial_id', '=', $trial)
    ->select('trial_receive_department', 'trial_receive_section', 'perbaikan', 'chief', db::raw('DATE_FORMAT(chief_date, "%d %b %Y") chief_date'), 'manager', db::raw('DATE_FORMAT(manager_date, "%d %b %Y") manager_date'))
    ->get();

    $data_trial_result = SakurentsuTrialRequestResult::where('trial_id', '=', $trial)
    ->select('trial_method', 'trial_date_start', 'trial_result', 'trial_date_finish', 'comment', 'trial_ok', 'status', 'fill_by')
    ->get();

    $dept = EmployeeSync::where('employee_id', '=', $data_trial_req->requester)->select('department')->first();

    $pdf = \App::make('dompdf.wrapper');
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper('A4', 'potrait');

    $pdf->loadView('sakurentsu.trial_request.issue_report_pdf', array(
        'data_trial' => $data_trial_req,
        'data_material' => $data_trial_mat,
        'data_receiver' => $data_trial_receive,
        'data_result' => $data_trial_result,
        'department' => $dept
    ));

    $pdf->save(public_path() . "/uploads/sakurentsu/trial_req/report/Report_".$trial.".pdf");
    
    // return $pdf->stream("Trial Issue ".$trial.".pdf");
    // return view('sakurentsu.trial_request.issue_report_pdf', array(
    //     'data_trial' => $data_trial_req,
    //     'data_material' => $data_trial_mat,
    //     'data_receiver' => $data_trial_receive,
    //     'data_result' => $data_trial_result,
    //     'department' => $dept
    // ));
}

public function save_bom(Request $request)
{
    // $trial = SakurentsuTrialRequest::where('form_number', '=', $request->get('id'))->first();

    $file_name = "";

        // MAKE CODE MIRAI APPROVAL
    $datenow = date('Y-m-d');
    $tahun = date('y');
    $bulan = date('m');
    $dept = $request->dept;

    $query = "SELECT no_transaction FROM `appr_sends` where no_transaction LIKE 'TR%' and DATE_FORMAT(`date`, '%y') = '$tahun' and month(`date`) = '$bulan' order by id DESC LIMIT 1";
    $nomorurut = DB::select($query);

    if ($nomorurut != null)
    {
        $no_doc = substr($nomorurut[0]->no_transaction, -3);
        $no_doc = $no_doc + 1;
        $no_doc = "TR".$tahun.$bulan.sprintf('%03d', $no_doc);
    } else {
        $no_doc = "TR".$tahun.$bulan.'001';
    }

    $files = "";

    if ($request->file('bom_file') != NULL)
    {
        if ($files = $request->file('bom_file')) {
            $nama = 'ADG'.$no_doc.'.pdf';
            $files->move('adagio', $nama);

            $file_name = $nama;
        }
    } else {
        $file_name = null;
    }

        // $trial_bom = SakurentsuTrialRequestBom::firstOrNew(array('material_number' => $request->get('gmc'), 'form_number' => $request->get('id')));
        // $trial_bom->total_quantity = $request->get('qty');
        // $trial_bom->total_standard_time = $request->get('std_time');
        // $trial_bom->total = $request->get('total');
        // $trial_bom->no_doc = $no_doc;
        // $trial_bom->created_by = Auth::user()->username;
        // $trial_bom->save();

        // MAKE MIRAI APPROVAL

    $emp_created = EmployeeSync::where('employee_id', '=', Auth::user()->username)->first();

    $appr = new ApprSend([
        'no_transaction' => $no_doc,
        'judul' => 'BOM Standard Time - '.$request->get('dept'),
        'no_dokumen' => 'EO_BOM_'.$request->get('id'),
        'nik' => $emp_created->employee_id.'/'.$emp_created->name.'/'.$emp_created->position ,
        'department' => 'Production Engineering Department',
        'description' => 'BOM dan Standard Time '.$request->get('gmc'),
        'date' => date('Y-m-d'),
        'remark' => 'Open',
        'file' => 'ADG'.$no_doc.'.pdf',
        'created_by' => Auth::user()->id,
    ]);

    $appr->save();

    $mstr = ApprMasters::where('judul', '=', 'BOM Standard Time - '.$request->get('dept'))->get();

    foreach ($mstr as $m) {
        $email = User::select('email')->where('username', '=', explode('/', $m->user)[0])->first();

        $appr_app = new ApprApprovals([
            'request_id' => $no_doc,
            'approver_id' => explode('/', $m->user)[0] ,
            'approver_name' => explode('/', $m->user)[1] ,
            'approver_email' => $email->email ,
            'remark' => explode('/', $m->user)[2] ,
        ]);

        $appr_app->save();
    }

        // Make TTD

    $file = ApprSend::where('no_transaction', '=', $no_doc)
    ->select('no_transaction', 'judul', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'created_by', 'created_at')
    ->first();

    $isi = ApprApprovals::where('request_id', '=', $no_doc)
    ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark')
    ->get();

    $pdf = \App::make('dompdf.wrapper');
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper('A4', 'landscape');

    $pdf->loadView('auto_approve.report.tanda_tangan', array(
        'isi' => $isi,
        'file' => $file
    ));

    $pdf->save(public_path() . "/adagio/ttd/ADG".$no_doc.".pdf");

        //Email ke Approval Pertama

    $mail_to = ApprApprovals::where('request_id', '=', $no_doc)
    ->wherenull('status')
    ->select('approver_email')
    ->first();

    $mail = [];
    array_push($mail, $mail_to->approver_email);

    $appr_sends = ApprSend::where('no_transaction', '=', $no_doc)
    ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark')
    ->first();

    $appr_approvals = ApprApprovals::where('request_id', '=', $no_doc)
    ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark')
    ->get();

    $data = [
        'appr_sends' => $appr_sends,
        'appr_approvals' => $appr_approvals
    ];

    Mail::to($mail_to->approver_email)
    ->bcc(['lukmannul.arif@music.yamaha.com','nasiqul.ibat@music.yamaha.com'])
    ->send(new SendEmail($data, 'send_email'));

        // Update STATUS
        // SakurentsuTrialRequest::where('id', '=', $request->get('id'))
        // ->update([
        //     'status_bom' => $no_doc
        // ]);


        // $trial_status = SakurentsuTrialStatus::where('form_number', '=', $trial->form_number)
        // ->update([
        //     'status_progress' => 'Approval BOM',
        //     'app_bom' => '0'
        // ]);

    $materials = ExtraOrderMaterial::where('id', '=',$request->get('id_material'))
    ->update([
        'material_number' => strtoupper($request->get('gmc')),
        'status' => 'Approval BOM',
        'remark' => $no_doc
    ]);

    $response = array(
        'status' => true,
    );

    return Response::json($response);
}

public function save_sales_price(Request $request)
{
    try {
        $files = "";

        if ($request->file('sales_file') != NULL)
        {
            if ($files = $request->file('sales_file')) {
                $nama = $request->get('gmc_material').'.pdf';
                $files->move('uploads/sales_price', $nama);

                $file_name = $nama;
            }
        } else {
            $file_name = null;
        }

        $trial_price = ExtraOrderPrice::firstOrNew(array('material_number' => $request->get('gmc_material')));
        $trial_price->sales_price = $request->get('price');
        $trial_price->valid_date = $request->get('valid_date');
        $trial_price->attachment = $file_name;
        $trial_price->status = $request->get('status');
        $trial_price->created_by = Auth::user()->id;
        $trial_price->save();

        $update = db::table('extra_order_details')
        ->where('material_number', $request->get('gmc_material') )
        ->where('sales_price', 0)
        ->update([
            'sales_price' => $request->get('price')
        ]);

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

public function resendemail($form_number)
{
    try {
        $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
        ->where('sakurentsu_trial_requests.form_number', '=', $form_number)
        ->select('sakurentsu_trial_requests.form_number','sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', db::raw('DATE_FORMAT(sakurentsus.target_date, "%d %M %Y") as target_date') , db::raw('DATE_FORMAT(sakurentsus.upload_date, "%d %M %Y") as upload_date'), 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'apd_material', 'sakurentsu_trial_requests.position', 'manager_mechanical', 'dgm2', 'att', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

        if ($data_trial_req->position == 'user') {
            $data = [
                "datas" => $data_trial_req,
                "position" => 'Chief Foreman Issue'
            ];
            $mail_to = SakurentsuTrialRequest::where('form_number','=', $form_number)->select(db::raw('chief as appr'))->get();
        } else if ($data_trial_req->position == 'chief') {
            $data = [
                "datas" => $data_trial_req,
                "position" => 'Manager Issue'
            ];
            $mail_to = SakurentsuTrialRequest::where('form_number','=', $form_number)->select(db::raw('manager as appr'))->get();
        } else if ($data_trial_req->position == 'manager') {
            if ($data_trial_req->manager_mechanical) {
                $data = [
                    "datas" => $data_trial_req,
                    "position" => 'Manager Mechanical'
                ];
                $mail_to = SakurentsuTrialRequest::where('form_number','=', $form_number)->select(db::raw('manager_mechanical as appr'))->get();
            } else {
                $data = [
                    "datas" => $data_trial_req,
                    "position" => 'DGM Issue'
                ];
                $mail_to = SakurentsuTrialRequest::where('form_number','=', $form_number)->select(db::raw('dgm as appr'))->get();
            }
        } else if ($data_trial_req->position == 'manager_mechanical') {
            $data = [
                "datas" => $data_trial_req,
                "position" => 'DGM Issue'
            ]; 
            $mail_to = SakurentsuTrialRequest::where('form_number','=', $form_number)->select(db::raw('dgm as appr'))->get();
        } else if ($data_trial_req->position == 'dgm') {
            $data = [
                "datas" => $data_trial_req,
                "position" => 'GM Issue'
            ];
            $mail_to = SakurentsuTrialRequest::where('form_number','=', $form_number)->select(db::raw('gm as appr'))->get();
        } else if ($data_trial_req->position == 'gm') {
            if ($data_trial_req->dgm2) {
                $data = [
                    "datas" => $data_trial_req,
                    "position" => 'DGM 2 Issue'
                ];  
                $mail_to = SakurentsuTrialRequest::where('form_number','=', $form_number)->select(db::raw('dgm2 as appr'))->get();
            }
        } else if ($data_trial_req->position == 'dgm 2') {
            $data = [
                "datas" => $data_trial_req,
                "position" => 'GM 2 Issue'
            ];
            $mail_to = SakurentsuTrialRequest::where('form_number','=', $form_number)->select(db::raw('gm2 as appr'))->get();
        }

        $mail = db::select('select email from users where username = "'.explode('/', $mail_to[0]->appr)[0].'"');

        Mail::to($mail[0]->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));

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

public function resendemailReceive($form_number)
{
    try {
        $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
        ->where('sakurentsu_trial_requests.form_number', '=', $form_number)
        ->select('sakurentsu_trial_requests.form_number', 'sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', db::raw('DATE_FORMAT(sakurentsus.target_date, "%d %M %Y") as target_date') , db::raw('DATE_FORMAT(sakurentsus.upload_date, "%d %M %Y") as upload_date'), 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'gm','dgm2', 'gm2', 'att', 'sakurentsus.file', 'sakurentsus.file_translate')->first();

        $appr = db::select("SELECT SPLIT_STRING(chief, '/', 1) as chf, chief_date, SPLIT_STRING(manager, '/', 1) as mngr, manager_date FROM `sakurentsu_trial_request_receives` where trial_id = '".$form_number."' and (manager_date is null or chief_date is null)");

        $stat = 'manager sudah';
        $pos = '';
        $ml = [];
        $ml2 = [];

        foreach ($appr as $aps) {
            if (!$aps->manager_date) {
                array_push($ml, $aps->mngr);
            } else if (!$aps->chief_date) {
                array_push($ml2, $aps->chf);
            }
        }

        $cc = SakurentsuTrialRequest::where('form_number', '=', $form_number)
        ->leftJoin('users', 'sakurentsu_trial_requests.created_by', '=', 'users.username')
        ->select('email')
        ->first();

        if (count($ml) > 0) {
            $mail = User::select('email')->whereIn('username', $ml)->get()->toArray();
            $pos = 'Manager Receiving Trial';

            $data = [
                "datas" => $data_trial_req,
                "position" => $pos
            ];

            Mail::to($mail)->cc([$cc->email])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));

        }

        if (count($ml2) > 0) {
            $mail = User::select('email')->whereIn('username', $ml2)->get()->toArray();
            $pos = 'Chief Receiving Trial';

            $data = [
                "datas" => $data_trial_req,
                "position" => $pos
            ];

            Mail::to($mail)->cc([$cc->email])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'trial_approval'));
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

public function getNotifIssue()
{
    if (Auth::user() !== null) {
        $user = strtoupper(Auth::user()->username);

        $notif = 0;

        $chief = db::SELECT("SELECT * from sakurentsu_trial_requests where `status` = 'approval' and chief LIKE '".$user."%' and chief_date is null and reject is null");

        $manager = db::SELECT("SELECT * from sakurentsu_trial_requests where `status` = 'approval' and manager LIKE '".$user."%' and manager_date is null and chief_date is not null and reject is null");

        $manager_mecha = db::SELECT("SELECT * from sakurentsu_trial_requests where `status` = 'approval' and manager_mechanical LIKE '".$user."%' and manager_mechanical_date is null and manager_date is not null and reject is null");

        $dgm = db::SELECT("SELECT * from sakurentsu_trial_requests where `status` = 'approval' and dgm LIKE '".$user."%' and dgm_date is null and (manager_mechanical is null OR manager_mechanical_date is not null) and manager_date is not null and reject is null");

        $gm = db::SELECT("SELECT * from sakurentsu_trial_requests where `status` = 'approval' and gm LIKE '".$user."%' and gm_date is null and dgm_date is not null and reject is null");

        $dgm2 = db::SELECT("SELECT * from sakurentsu_trial_requests where `status` = 'approval' and dgm2 LIKE '".$user."%' and dgm_date2 is null and gm_date is not null and reject is null");

        $gm2 = db::SELECT("SELECT * from sakurentsu_trial_requests where `status` = 'approval' and gm2 LIKE '".$user."%' and gm_date2 is null and reject is null");

        $notif = count($chief) + count($manager) + count($manager_mecha) + count($dgm) + count($gm) + count($dgm2) + count($gm2);

        return $notif;
    }
}

public function getNotifReceive()
{
    // dd(strtoupper(Auth::user()->username));
    if (Auth::user() !== null) {
        $user = strtoupper(Auth::user()->username);
        
        $notif = 0;

        $trial_receive = db::select("SELECT sakurentsu_trial_request_receives.trial_id from sakurentsu_trial_requests 
            left join sakurentsu_trial_request_receives on sakurentsu_trial_requests.form_number = sakurentsu_trial_request_receives.trial_id
            where sakurentsu_trial_requests.status = 'receiving'
            and reject is null
            AND ((sakurentsu_trial_request_receives.chief LIKE '".$user."%' and sakurentsu_trial_request_receives.chief_date is null and sakurentsu_trial_request_receives.manager_date is not null) OR (sakurentsu_trial_request_receives.manager LIKE '".$user."%' and sakurentsu_trial_request_receives.manager_date is null))");

        if (count($trial_receive) > 0) {
            $notif = count($trial_receive);
        }

        return $notif;
    }
}

public function getNotifResult()
{
    if (Auth::user() !== null) {
        $user = strtoupper(Auth::user()->username);
        
        $notif = 0;

        $trial_result = db::select("SELECT sakurentsu_trial_request_results.* from sakurentsu_trial_requests 
            left join sakurentsu_trial_request_results on sakurentsu_trial_requests.form_number = sakurentsu_trial_request_results.trial_id
            where sakurentsu_trial_requests.status = 'resulting'
            and reject is null
            and fill_by LIKE '".$user."%' and trial_method is null");

        if (count($trial_result) > 0) {
            $notif = count($trial_result);
        }

        return $notif;
    }
}

public function index_approval_trial($id_trial, $position)
{
    $title = 'Approval Trial Request Issue';
    $title_jp = '??';

    $data_trial_req = SakurentsuTrialRequest::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
    ->where('sakurentsu_trial_requests.form_number', '=', $id_trial)
    ->select('sakurentsu_trial_requests.form_number','sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'sakurentsus.applicant', db::raw('DATE_FORMAT(sakurentsus.target_date, "%d %M %Y") as target_date') , db::raw('DATE_FORMAT(sakurentsus.upload_date, "%d %M %Y") as upload_date'), 'sakurentsu_trial_requests.id', 'sakurentsu_trial_requests.requester', 'sakurentsu_trial_requests.requester_name', 'sakurentsu_trial_requests.subject', db::raw('DATE_FORMAT(sakurentsu_trial_requests.submission_date, "%d %M %Y") as submit_date'), 'sakurentsu_trial_requests.department', db::raw('DATE_FORMAT(sakurentsu_trial_requests.trial_date, "%d %M %Y") as trial_date'), 'sakurentsu_trial_requests.trial_purpose', 'trial_location', 'apd_material')->first();

    if ($position == 'chief') {
        $pos = 'Chief Foreman Issue';
    } else if ($position == 'manager') {
        $pos = 'Manager Issue';
    } else if ($position == 'manager mechanical') {
        $pos = 'Manager Mechanical';
    } else if ($position == 'dgm') {
        $pos = 'DGM Issue';
    } else if ($position == 'gm') {
        $pos = 'GM Issue';
    } else if ($position == 'dgm2') {
        $pos = 'DGM 2 Issue';
    } else if ($position == 'gm2') {
        $pos = 'GM 2 Issue';
    } else if ($position == 'Manager Receiving Trial') {
        $pos = $position;
    } else if ($position == 'Chief Receiving Trial') {
        $pos = $position;
    }

    $data = [
        "datas" => $data_trial_req,
        "position" => $pos
    ];

    return view('mails.trial_request_approval', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'data' => $data
    ));

}
}