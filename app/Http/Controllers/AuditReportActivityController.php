<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\AuditReportActivity;
use App\AuditGuidance;
use App\Approver;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class AuditReportActivityController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
      if (isset($_SERVER['HTTP_USER_AGENT']))
        {
            $http_user_agent = $_SERVER['HTTP_USER_AGENT']; 
            if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) 
            {
                // Prevent MS office products detecting the upcoming re-direct .. forces them to launch the browser to this link
                die();
            }
        }
    }

    function index($id)
    {
        $activityList = ActivityList::find($id);
    	// $auditReportActivity = AuditReportActivity::select('audit_report_activities.*','audit_guidances.*','audit_report_activities.id as audit_id')->where('audit_report_activities.activity_list_id',$id)->leftjoin('audit_guidances','audit_guidances.id','audit_report_activities.audit_guidance_id')->orderBy('audit_report_activities.id','desc')
     //        ->get();


    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $frequency = $activityList->frequency;
        $leader = $activityList->leader_dept;
        // var_dump($productionAudit);
        $querySubSection = "SELECT
            DISTINCT(employee_syncs.group) AS sub_section_name 
          FROM
            employee_syncs 
          WHERE
          employee_syncs.group is not null
          AND
            department LIKE '%".$departments."%'";
        $subsection = DB::select($querySubSection);
        $subsection2 = DB::select($querySubSection);
        $subsection3 = DB::select($querySubSection);

    	$data = array(
        // 'audit_report_activity' => $auditReportActivity,
                      'subsection' => $subsection,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
    				  'departments' => $departments,
                      'frequency' => $frequency,
                      'leader' => $leader,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
    				  'id' => $id,
                      'id_departments' => $id_departments);
    	return view('audit_report_activity.index', $data
    		)->with('page', 'Laporan Aktivitas Audit');
    }

    function filter_audit_report(Request $request,$id)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;
        $frequency = $activityList->frequency;
        $leader = $activityList->leader_dept;
        // var_dump($request->get('product'));
        // var_dump($request->get('date'));
        $querySubSection = "SELECT
            DISTINCT(employee_syncs.group) AS sub_section_name 
          FROM
            employee_syncs 
          WHERE
          employee_syncs.group is not null
          AND
            department LIKE '%".$departments."%'";
        $sub_section = DB::select($querySubSection);
        $subsection2 = DB::select($querySubSection);
        $subsection3 = DB::select($querySubSection);

        if ($request->get('subsection') == null || $request->get('month') == null) {
          return redirect('index/audit_report_activity/index/'.$id)
            ->with('page', 'Audit Report Activity')->with('error', 'Isi semua kolom filter.');
        }

        if($request->get('subsection') != null && strlen($request->get('month')) != null){
            $subsection = $request->get('subsection');
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $auditReportActivity = AuditReportActivity::select('audit_report_activities.*','audit_guidances.*','audit_report_activities.id as audit_id','audit_report_activities.date as date_audited','audit_report_activities.send_status as send_status_audit')->where('audit_report_activities.activity_list_id',$id)
                ->leftjoin('audit_guidances','audit_guidances.id','audit_report_activities.audit_guidance_id')
                ->where('subsection',$subsection)
                ->where('audit_guidances.month', '=', $request->get('month'))
                ->orderBy('audit_report_activities.id','desc')
                ->get();
        }
        elseif ($request->get('month') > null && $request->get('subsection') == null) {
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $auditReportActivity = AuditReportActivity::select('audit_report_activities.*','audit_guidances.*','audit_report_activities.id as audit_id','audit_report_activities.date as date_audited','audit_report_activities.send_status as send_status_audit')->where('audit_report_activities.activity_list_id',$id)
                ->leftjoin('audit_guidances','audit_guidances.id','audit_report_activities.audit_guidance_id')
                ->where('audit_guidances.month', '=', $request->get('month'))
                ->orderBy('audit_report_activities.id','desc')
                ->get();
        }
        elseif($request->get('subsection') > null && strlen($request->get('month')) == null){
            $subsection = $request->get('subsection');
            $auditReportActivity = AuditReportActivity::select('audit_report_activities.*','audit_guidances.*','audit_report_activities.id as audit_id','audit_report_activities.date as date_audited','audit_report_activities.send_status as send_status_audit')->where('audit_report_activities.activity_list_id',$id)
                ->where('subsection',$subsection)
                ->leftjoin('audit_guidances','audit_guidances.id','audit_report_activities.audit_guidance_id')
                ->orderBy('audit_report_activities.id','desc')
                ->get();
        }
        else{
            $auditReportActivity = AuditReportActivity::select('audit_report_activities.*','audit_guidances.*','audit_report_activities.id as audit_id','audit_report_activities.date as date_audited','audit_report_activities.send_status as send_status_audit')->where('audit_report_activities.activity_list_id',$id)->leftjoin('audit_guidances','audit_guidances.id','audit_report_activities.audit_guidance_id')->orderBy('audit_report_activities.id','desc')
            ->get();
        }
        $data = array(
                      'audit_report_activity' => $auditReportActivity,
                      'subsection' => $sub_section,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
                      'departments' => $departments,
                      'frequency' => $frequency,
                      'leader' => $leader,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'id' => $id,
                      'id_departments' => $id_departments);
        return view('audit_report_activity.index', $data
            )->with('page', 'Laporan Aktivitas Audit');
    }

    function show($id,$audit_report_id)
    {
        $activityList = ActivityList::find($id);
        $auditReportActivity = AuditReportActivity::find($audit_report_id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;

        $data = array('audit_report_activity' => $auditReportActivity,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('audit_report_activity.view', $data
            )->with('page', 'Laporan Aktivitas Audit');
    }

    public function destroy($id,$audit_report_id)
    {
      $auditReportActivity = AuditReportActivity::find($audit_report_id);
      $auditReportActivity->delete();

      return redirect('/index/audit_report_activity/index/'.$id)
        ->with('status', 'Laporan Aktivitas Audit has been deleted.')
        ->with('page', 'Laporan Aktivitas Audit');        
    }

    function create($id)
    {
      $emp_id = strtoupper(Auth::user()->username);
      $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $bulan = date('Y-m');

        $guidance = DB::SELECT("SELECT * FROM audit_guidances where activity_list_id = '".$id."' and deleted_at is null and status = 'Belum Dikerjakan'");

        $querySection = "SELECT
            DISTINCT(employee_syncs.section) AS section_name
          FROM
            employee_syncs 
          WHERE
          employee_syncs.section is not null
          AND
            department LIKE '%".$departments."%'";
        $section = DB::select($querySection);

        $querySubSection = "SELECT
            DISTINCT(employee_syncs.group) AS sub_section_name 
          FROM
            employee_syncs 
          WHERE
          employee_syncs.group is not null
          AND
            department LIKE '%".$departments."%'";
        $subsection = DB::select($querySubSection);

        $queryOperator = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs where department LIKE '%".$departments."%'";
        $operator = DB::select($queryOperator);

        $foremans = User::where('name',$foreman)->first();

        $documents = DB::connection('ympimis_2')->table('documents')->select('documents.*')->join('qa_qc_koteihyo_schedules','qa_qc_koteihyo_schedules.document_number','documents.document_number')->where('qa_qc_koteihyo_schedules.employee_id','like','%'.strtoupper($foremans->username).'%')->where('documents.category','!=','IK')->get();

        $data = array(
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'section' => $section,
                      'guidance' => $guidance,
                      'operator' => $operator,
                      'subsection' => $subsection,
                      'activity_name' => $activity_name,
                      'documents' => $documents,
                      'id' => $id);
        return view('audit_report_activity.create', $data
            )->with('page', 'Audit Report Activity');
    }

    function store(Request $request,$id)
    {
            $id_user = Auth::id();
            $audit_guidance_id = explode('][', $request->input('audit_guidance_id'));

            if ($request->input('select_document_qc_koteihyo') == '') {
              $audit = AuditReportActivity::create([
                'activity_list_id' => $id,
                'audit_guidance_id' => $audit_guidance_id[0],
                'department' => $request->input('department'),
                'section' => $request->input('section'),
                'subsection' => $request->input('subsection'),
                'date' => date('Y-m-d'),
                'nama_dokumen' => $request->input('nama_dokumen'),
                'result_qc_koteihyo' => 'Sesuai__',
                'no_dokumen' => $request->input('no_dokumen'),
                'kesesuaian_aktual_proses' => $request->input('kesesuaian_aktual_proses'),
                'tindakan_perbaikan' => $request->input('tindakan_perbaikan'),
                'target' => $request->input('target'),
                'kelengkapan_point_safety' => $request->input('kelengkapan_point_safety'),
                'kesesuaian_qc_kouteihyo' => $request->input('kesesuaian_qc_kouteihyo'),
                // 'condition' => $request->input('condition'),
                'handling' => $request->input('handling'),
                'operator' => $request->input('operator'),
                'leader' => $request->input('leader'),
                'foreman' => $request->input('foreman'),
                'created_by' => $id_user
            ]);
            }else{
              $documents = DB::connection('ympimis_2')->table('documents')->where('document_number',$request->input('select_document_qc_koteihyo'))->first();
              $audit = AuditReportActivity::create([
                'activity_list_id' => $id,
                'audit_guidance_id' => $audit_guidance_id[0],
                'department' => $request->input('department'),
                'section' => $request->input('section'),
                'subsection' => $request->input('subsection'),
                'date' => date('Y-m-d'),
                'nama_dokumen' => $request->input('nama_dokumen'),
                'result_qc_koteihyo' => $request->input('condition_qc_koteihyo').'_'.$request->input('select_document_qc_koteihyo').'_'.$documents->title,
                'no_dokumen' => $request->input('no_dokumen'),
                'kesesuaian_aktual_proses' => $request->input('kesesuaian_aktual_proses'),
                'tindakan_perbaikan' => $request->input('tindakan_perbaikan'),
                'target' => $request->input('target'),
                'kelengkapan_point_safety' => $request->input('kelengkapan_point_safety'),
                'kesesuaian_qc_kouteihyo' => $request->input('kesesuaian_qc_kouteihyo'),
                // 'condition' => $request->input('condition'),
                'handling' => $request->input('handling'),
                'operator' => $request->input('operator'),
                'leader' => $request->input('leader'),
                'foreman' => $request->input('foreman'),
                'created_by' => $id_user
            ]);
            }

            $audit_guidance_id = $audit_guidance_id[0];
            $audit_guidance = AuditGuidance::find($audit_guidance_id);
            $audit_guidance->status = 'Sudah Dikerjakan';
            $audit_guidance->save();

            $audit_guidance = AuditGuidance::where('id',$audit_guidance_id)->first();

            $audits = AuditReportActivity::select('audit_report_activities.id','activity_list_id','no_dokumen','nama_dokumen','result_qc_koteihyo','department','leader','foreman','date','kesesuaian_qc_kouteihyo','audit_guidance_id','departments.department_shortname')->leftjoin('departments','departments.department_name','audit_report_activities.department')->where('audit_report_activities.id',$audit->id)->first();

            $cc = [];

            $foreman = User::where('name',$audits->foreman)->first();
            if ($foreman) {
              array_push($cc, $foreman->email);
            }

            $datas = [
              'category' => $request->get('handling'),
              'data' => $audits,
              'audit_guidance' => $audit_guidance,
              'reason' => null
            ];

            $mail_to_qa = [];
            array_push($mail_to_qa, 'sutrisno@music.yamaha.com');
            array_push($mail_to_qa, 'ertikto.singgih@music.yamaha.com');
            array_push($mail_to_qa, 'abdissalam.saidi@music.yamaha.com');
            array_push($mail_to_qa, 'ratri.sulistyorini@music.yamaha.com');
            array_push($mail_to_qa, 'agustina.hayati@music.yamaha.com');

            $mail_to_std = [];
            array_push($mail_to_std, 'widura@music.yamaha.com');
            array_push($mail_to_std, 'vidiya.chalista@music.yamaha.com');
            array_push($mail_to_std, 'syafrizal.carnov.purwanto@music.yamaha.com');
            array_push($mail_to_std, 'rani.nurdiyana.sari@music.yamaha.com');

            if ($request->input('handling') == 'IK Tidak Digunakan' || $request->input('handling') == 'Revisi IK') {
              Mail::to($mail_to_std)
              ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
              ->cc($cc)
              ->send(new SendEmail($datas, 'ik_obsolete'));
            }

            if ($request->input('select_document_qc_koteihyo') == '') {

            }else{

              if ($request->input('handling') == 'Revisi QC Kouteihyo') {
                if (str_contains($request->input('select_document_qc_koteihyo'),'YMPI-PE')) {
                  array_push($mail_to_qa, 'darma.bagus@music.yamaha.com');
                  array_push($mail_to_qa, 'susilo.basri@music.yamaha.com');
                }
                Mail::to($mail_to_qa)
                ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
                ->cc($cc)
                ->send(new SendEmail($datas, 'ik_obsolete'));
              }
              $document_qc_kotiehyo = DB::connection('ympimis_2')->table('qa_qc_koteihyo_documents')->where('document_number_qc_koteihyo',$request->input('select_document_qc_koteihyo'))->first();
              if ($document_qc_kotiehyo) {
                if (!str_contains($document_qc_kotiehyo->document_number_ik,$request->input('no_dokumen'))) {
                  $update_document_qc_kotiehyo = DB::connection('ympimis_2')->table('qa_qc_koteihyo_documents')->where('document_number_qc_koteihyo',$request->input('select_document_qc_koteihyo'))->update([
                    'document_number_ik' => $document_qc_kotiehyo->document_number_ik.','.$request->input('no_dokumen'),
                    'updated_at' => date('Y-m-d H:i:s')
                  ]);
                }
              }else{
                $input_document_qc_kotiehyo = DB::connection('ympimis_2')->table('qa_qc_koteihyo_documents')->insert([
                  'document_number_qc_koteihyo' => $request->input('select_document_qc_koteihyo'),
                  'document_number_ik' => $request->input('no_dokumen'),
                  'created_by' => Auth::user()->id,
                  'created_at' => date('Y-m-d H:i:s'),
                  'updated_at' => date('Y-m-d H:i:s')
                ]);
              }
            }

        return redirect('index/audit_report_activity/index/'.$id)
            ->with('page', 'Audit Report Activity')->with('status', 'New Audit Report Activity has been created.');
    }

    public function sendEmailTemuan(Request $request)
    {
      try {
        $audit = AuditReportActivity::select('audit_report_activities.id','activity_list_id','no_dokumen','nama_dokumen','result_qc_koteihyo','department','leader','foreman','date','kesesuaian_qc_kouteihyo','audit_guidance_id','departments.department_shortname')->leftjoin('departments','departments.department_name','audit_report_activities.department')->where('audit_report_activities.id',$request->get('id'))->first();
        $audit_guidance = AuditGuidance::where('id',$audit->audit_guidance_id)->first();

        $cc = [];

        $foreman = User::where('name',$audit->foreman)->first();
        if ($foreman) {
          array_push($cc, $foreman->email);
        }

        $datas = [
          'category' => $request->get('handling'),
          'data' => $audit,
          'audit_guidance' => $audit_guidance,
          'reason' => null
        ];

        $mail_to_qa = [];
        array_push($mail_to_qa, 'sutrisno@music.yamaha.com');
        array_push($mail_to_qa, 'ertikto.singgih@music.yamaha.com');
        array_push($mail_to_qa, 'abdissalam.saidi@music.yamaha.com');
        array_push($mail_to_qa, 'ratri.sulistyorini@music.yamaha.com');
        array_push($mail_to_qa, 'agustina.hayati@music.yamaha.com');

        $mail_to_std = [];
        array_push($mail_to_std, 'widura@music.yamaha.com');
        array_push($mail_to_std, 'vidiya.chalista@music.yamaha.com');
        array_push($mail_to_std, 'syafrizal.carnov.purwanto@music.yamaha.com');
        array_push($mail_to_std, 'rani.nurdiyana.sari@music.yamaha.com');

        if ($request->input('handling') == 'IK Tidak Digunakan' || $request->input('handling') == 'Revisi IK') {
          Mail::to($mail_to_std)
          ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
          ->cc($cc)
          ->send(new SendEmail($datas, 'ik_obsolete'));
        }

        if ($request->input('handling') == 'Revisi QC Kouteihyo') {
          Mail::to($mail_to_qa)
          ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
          ->cc($cc)
          ->send(new SendEmail($datas, 'ik_obsolete'));
        }

        // if ($request->input('select_document_qc_koteihyo') == '') {

        // }else{

          
          // $document_qc_kotiehyo = DB::connection('ympimis_2')->table('qa_qc_koteihyo_documents')->where('document_number_qc_koteihyo',$request->input('select_document_qc_koteihyo'))->first();
          // if ($document_qc_kotiehyo) {
          //   if (!str_contains($document_qc_kotiehyo->document_number_ik,$request->input('no_dokumen'))) {
          //     $update_document_qc_kotiehyo = DB::connection('ympimis_2')->table('qa_qc_koteihyo_documents')->where('document_number_qc_koteihyo',$request->input('select_document_qc_koteihyo'))->update([
          //       'document_number_ik' => $document_qc_kotiehyo->document_number_ik.','.$request->input('no_dokumen'),
          //       'updated_at' => date('Y-m-d H:i:s')
          //     ]);
          //   }
          // }else{
          //   $input_document_qc_kotiehyo = DB::connection('ympimis_2')->table('qa_qc_koteihyo_documents')->insert([
          //     'document_number_qc_koteihyo' => $request->input('select_document_qc_koteihyo'),
          //     'document_number_ik' => $request->input('no_dokumen'),
          //     'created_by' => Auth::user()->id,
          //     'created_at' => date('Y-m-d H:i:s'),
          //     'updated_at' => date('Y-m-d H:i:s')
          //   ]);
          // }
        // }
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

    public function sendEmailCekEfektifitas(Request $request)
    {
      try {
        $audit = AuditReportActivity::select('audit_report_activities.*','department_shortname')->leftjoin('departments','departments.department_name','audit_report_activities.department')->where('audit_report_activities.id',$request->get('id'))->first();
        $audit_guidance = AuditGuidance::where('id',$audit->audit_guidance_id)->first();
        $activity = ActivityList::where('id',$audit->activity_list_id)->first();

        $cc = [];
        $mail_to = [];

        $datas = [
          'data' => $audit,
          'audit_guidance' => $audit_guidance,
          'activity' => $activity,
        ];

        array_push($cc, 'yayuk.wahyuni@music.yamaha.com');

        $user = User::where('username',$activity->auditor_effectivity_id)->first();
        $emp = db::table('employee_syncs')->where('employee_id',$activity->auditor_effectivity_id)->first();
        if (str_contains($user->email,'@music.yamaha.com')) {
          array_push($mail_to, $user->email);
        }else{
          $foreman = Approver::where('remark','Foreman')->where('department',$emp->department)->where('section',$emp->section)->first();
          if (!$foreman) {
            $foreman = Approver::where('remark','Chief')->where('department',$emp->department)->where('section',$emp->section)->first();
            if ($foreman) {
              array_push($mail_to, $foreman->approver_email);
            }
          }else{
            array_push($mail_to, $foreman->approver_email);
          }
        }

        Mail::to($mail_to)
        ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
        ->cc($cc)
        ->send(new SendEmail($datas, 'ik_obsolete_cek'));

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

    public function indexCekEfektifitas($id)
    {
      $emp_id = strtoupper(Auth::user()->username);
      $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
      $audit = AuditReportActivity::select('audit_report_activities.*','department_shortname','activity_lists.auditor_effectivity_id','activity_lists.auditor_effectivity_name')->leftjoin('departments','departments.department_name','audit_report_activities.department')->join('activity_lists','activity_list_id','activity_lists.id')->where('audit_report_activities.id',$id)->first();
      return view('production_report.cek_efektifitas')
      ->with('title', 'Cek Efektifitas Penanganan Audit Proses Khsusus')
      ->with('title_jp', '品証 工程監査')
      ->with('page', 'Quality Assurance')
      ->with('audit',$audit)
      ->with('count_audit',count($audit))
      ->with('role',Auth::user()->role_code)
      ->with('employee_id',Auth::user()->username)
      ->with('jpn', '品保');
    }

    public function inputCekEfektifitas(Request $request)
    {
      try {
        $id_verification = $request->get('id_verification');
        $qa_verification = $request->get('verification');
        $qa_verified_id = $request->get('verification_id');
        $qa_verified_name = $request->get('verification_name');
        $qa_verification_note = $request->get('verification_note');

        $update = AuditReportActivity::where('id',$id_verification[0])->first();
        $update->audit_effectivity = $qa_verification[0];
        $update->audit_effectivity_note = $qa_verification_note[0];
        $update->auditor_effectivity_id = $qa_verified_id[0];
        $update->auditor_effectivity_name = $qa_verified_name[0];
        $update->audit_effectivity_at = date('Y-m-d H:i:s');
        $update->save();

        // for ($i=0; $i < count($id_verification); $i++) { 
        //   $update = DB::connection('ympimis_2')->table('qa_process_audits')->where('id',$id_verification[$i])->update([
        //     'qa_verification' => $qa_verification[$i],
        //     'qa_verification_note' => $qa_verification_note[$i],
        //     'qa_verified_id' => $qa_verified_id[$i],
        //     'qa_verified_name' => $qa_verified_name[$i],
        //     'qa_verified_at' => date('Y-m-d H:i:s'),
        //     'updated_at' => date('Y-m-d H:i:s'),
        //   ]);
        // }


        $response = array(
            'status' => true,
            'message' => 'Success Input Cek Efektifitas'
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

    function edit($id,$audit_report_id)
    {
      $emp_id = strtoupper(Auth::user()->username);
      $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
        $activityList = ActivityList::find($id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

        $querySection = "SELECT
            DISTINCT(employee_syncs.section) AS section_name 
          FROM
            employee_syncs 
          WHERE
          employee_syncs.section is not null
          AND
            department LIKE '%".$departments."%'";
        $section = DB::select($querySection);

        $querySubSection = "SELECT
            DISTINCT(employee_syncs.group) AS sub_section_name 
          FROM
            employee_syncs 
          WHERE
          employee_syncs.group is not null
          AND
            department LIKE '%".$departments."%'";
        $subsection = DB::select($querySubSection);

        $queryOperator = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs where department LIKE '%".$departments."%'";
        $operator = DB::select($queryOperator);

        $bulan = date('Y-m');

        $guidance = DB::SELECT("SELECT * FROM audit_guidances where activity_list_id = '".$id."' ");

        $audit_report_activity = AuditReportActivity::find($audit_report_id);

        $data = array('leader' => $leader,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'section' => $section,
                      'guidance' => $guidance,
                      'operator' => $operator,
                      'subsection' => $subsection,
                      'activity_name' => $activity_name,
                      'audit_report_activity' => $audit_report_activity,
                      'id' => $id);
        return view('audit_report_activity.edit', $data
            )->with('page', 'Audit Report Activity');
    }

    function update(Request $request,$id,$audit_report_id)
    {
        try{
                $audit_report_activity = AuditReportActivity::find($audit_report_id);
                $audit_report_activity->activity_list_id = $id;
                $audit_report_activity->audit_guidance_id = $request->get('audit_guidance_id');
                $audit_report_activity->department = $request->get('department');
                $audit_report_activity->section = $request->get('section');
                $audit_report_activity->subsection = $request->get('subsection');
                $audit_report_activity->date = date('Y-m-d');
                $audit_report_activity->nama_dokumen = $request->get('nama_dokumen');
                $audit_report_activity->no_dokumen = $request->get('no_dokumen');
                $audit_report_activity->kesesuaian_aktual_proses = $request->get('kesesuaian_aktual_proses');
                $audit_report_activity->tindakan_perbaikan = $request->get('tindakan_perbaikan');
                $audit_report_activity->target = $request->get('target');
                $audit_report_activity->kelengkapan_point_safety = $request->get('kelengkapan_point_safety');
                $audit_report_activity->kesesuaian_qc_kouteihyo = $request->get('kesesuaian_qc_kouteihyo');
                $audit_report_activity->condition = $request->get('condition');
                $audit_report_activity->handling = $request->get('handling');
                $audit_report_activity->operator = $request->get('operator');
                $audit_report_activity->leader = $request->get('leader');
                $audit_report_activity->foreman = $request->get('foreman');
                $audit_report_activity->save();

            return redirect('/index/audit_report_activity/index/'.$id)->with('status', 'Audit Report Activity data has been updated.')->with('page', 'Audit Report Activity');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Audit Report Activity already exist.')->with('page', 'Training Report');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Audit Report Activity');
            }
          }
    }

    function report_audit_activity($id)
    {
        $queryDepartments = "SELECT * FROM departments where id='".$id."'";
        $department = DB::select($queryDepartments);
        foreach($department as $department){
            $departments = $department->department_name;
        }
        // $data = db::select("select count(*) as jumlah_activity, activity_type from activity_lists where deleted_at is null and department_id = '".$id."' GROUP BY activity_type");
        $bulan = date('Y-m');
        return view('audit_report_activity.report_audit_activity',  array('title' => 'Report Audit Activity',
            'title_jp' => 'Report Audit Activity',
            'id' => $id,
            'departments' => $departments,
            // 'bulan' => $bulan,
        ))->with('page', 'Report Audit Activity');
    }

    public function fetchReport(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
      }
      else{
        $bulan = date('Y-m');
      }

      $data = DB::select("select count(*) as jumlah_laporan, date
                from audit_report_activities
                        join activity_lists as actlist on actlist.id = activity_list_id
                        where actlist.department_id = '".$id."'
                        and DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$bulan."'
                        and audit_report_activities.deleted_at is null GROUP BY date");
      $monthTitle = date("F Y", strtotime($bulan));

      // $monthTitle = date("F Y", strtotime($tgl));

      $response = array(
        'status' => true,
        'datas' => $data,
        'monthTitle' => $monthTitle,
        // 'bulan' => $request->get("tgl")

      );

      return Response::json($response);
    }

    public function detail_laporan_aktivitas(Request $request, $id){
      $week_date = $request->get("week_date");
        $query = "select *,CONCAT(activity_lists.id, '/', audit_report_activities.subsection, '/', DATE_FORMAT(audit_report_activities.date,'%Y-%m')) as linkurl, audit_report_activities.id as laporan_aktivitas_id from audit_report_activities join activity_lists on activity_lists.id = audit_report_activities.activity_list_id where department_id = '".$id."' and activity_type = 'Laporan Aktivitas' and date = '".$week_date."' and audit_report_activities.deleted_at is null";

      $detail = db::select($query);

      return DataTables::of($detail)->make(true);

    }

    function print_audit_report($id,$month)
    {
        $activityList = ActivityList::find($id);
        // var_dump($request->get('product'));
        // var_dump($request->get('date'));
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;


        if($month != null){
            // $month = $request->get('month');
            $queryLaporanAktivitas = "SELECT
              *,
              audit_report_activities.id AS id_audit_report,
              audit_report_activities.date AS date_audit
            FROM
              audit_report_activities
              JOIN activity_lists ON activity_lists.id = audit_report_activities.activity_list_id
              LEFT JOIN audit_guidances ON audit_guidance_id = audit_guidances.id 
            WHERE
              activity_lists.id = '".$id."' 
              AND activity_lists.department_id = '".$id_departments."' 
              AND audit_guidances.`month` = '".$month."' 
              AND audit_guidances.deleted_at IS NULL 
              AND audit_report_activities.deleted_at IS NULL";
            $laporanAktivitas = DB::select($queryLaporanAktivitas);
            $laporanAktivitas2 = DB::select($queryLaporanAktivitas);
        }

        $monthTitle = date("F Y", strtotime($month));

        // var_dump($subsection);
        $jml_null = 0;
        foreach($laporanAktivitas2 as $laporanAktivitas2){
            // $product = $samplingCheck->product;
            // $proses = $samplingCheck->proses;
            $date = $laporanAktivitas2->date;
            $foreman = $laporanAktivitas2->foreman;
            $section = $laporanAktivitas2->section;
            $approval_leader = $laporanAktivitas2->approval_leader;
            $approved_date_leader = $laporanAktivitas2->approved_date_leader;
            $subsection = $laporanAktivitas2->subsection;
            $leader = $laporanAktivitas2->leader;
            if ($laporanAktivitas2->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            $approved_date = $laporanAktivitas2->approved_date;
        }
        if($laporanAktivitas == null){
            // return redirect('/index/production_audit/index/'.$id.'/'.$request->get('product').'/'.$request->get('proses'))->with('error', 'Data Tidak Tersedia.')->with('page', 'Production Audit');
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{
            // $data = array(
            //               'subsection' => $subsection,
            //               'leader' => $leader,
            //               'foreman' => $foreman,
            //               'section' => $section,
            //               'monthTitle' => $monthTitle,
            //               'date' => $date,
            //               'jml_null' => $jml_null,
            //               'approved_date' => $approved_date,
            //               'approval_leader' => $approval_leader,
            //               'approved_date_leader' => $approved_date_leader,
            //               'laporanAktivitas' => $laporanAktivitas,
            //               'departments' => $departments,
            //               'activity_name' => $activity_name,
            //               'activity_alias' => $activity_alias,
            //               'id' => $id,
            //               'id_departments' => $id_departments);
            // return view('audit_report_activity.print', $data
            //     )->with('page', 'Laporan Aktivitas Audit');

            $pdf = \App::make('dompdf.wrapper');
           $pdf->getDomPDF()->set_option("enable_php", true);
           $pdf->setPaper('A4', 'landscape');

           $pdf->loadView('audit_report_activity.print', array(
                'subsection' => $subsection,
                  'leader' => $leader,
                  'foreman' => $foreman,
                  'section' => $section,
                  'monthTitle' => $monthTitle,
                  'date' => $date,
                  'jml_null' => $jml_null,
                  'month' => $month,
                  'approved_date' => $approved_date,
                  'approval_leader' => $approval_leader,
                  'approved_date_leader' => $approved_date_leader,
                  'laporanAktivitas' => $laporanAktivitas,
                  'departments' => $departments,
                  'activity_name' => $activity_name,
                  'activity_alias' => $activity_alias,
                  'id' => $id,
                  'id_departments' => $id_departments
           ));

           return $pdf->stream("Audit IK ".$leader." (".$monthTitle.").pdf");
        }
    }

    function print_audit_report_chart($id,$subsection,$month)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;


        if($subsection != null && $month != null){
            $queryLaporanAktivitas = "select *, audit_report_activities.id as id_audit_report
                from audit_report_activities
                join activity_lists on activity_lists.id = audit_report_activities.activity_list_id
                where activity_lists.id = '".$id."'
                and activity_lists.department_id = '".$id_departments."'
                and audit_report_activities.subsection = '".$subsection."' 
                and DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$month."' 
                and audit_report_activities.deleted_at is null";
            $laporanAktivitas = DB::select($queryLaporanAktivitas);
            $laporanAktivitas2 = DB::select($queryLaporanAktivitas);
        }

        $monthTitle = date("F Y", strtotime($month));

        // var_dump($subsection);
        $jml_null = 0;
        foreach($laporanAktivitas2 as $laporanAktivitas2){
            // $product = $samplingCheck->product;
            // $proses = $samplingCheck->proses;
            $date = $laporanAktivitas2->date;
            $foreman = $laporanAktivitas2->foreman;
            $section = $laporanAktivitas2->section;
            $approval_leader = $laporanAktivitas2->approval_leader;
            $approved_date_leader = $laporanAktivitas2->approved_date_leader;
            $subsection = $laporanAktivitas2->subsection;
            $leader = $laporanAktivitas2->leader;
            if ($laporanAktivitas2->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            $approved_date = $laporanAktivitas2->approved_date;
        }
        if($laporanAktivitas == null){
            // return redirect('/index/production_audit/index/'.$id.'/'.$request->get('product').'/'.$request->get('proses'))->with('error', 'Data Tidak Tersedia.')->with('page', 'Production Audit');
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{
            $data = array(
                          'subsection' => $subsection,
                          'leader' => $leader,
                          'foreman' => $foreman,
                          'section' => $section,
                          'monthTitle' => $monthTitle,
                          'subsection' => $subsection,
                          'date' => $date,
                          'jml_null' => $jml_null,
                          'approved_date' => $approved_date,
                          'approval_leader' => $approval_leader,
                          'approved_date_leader' => $approved_date_leader,
                          'laporanAktivitas' => $laporanAktivitas,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'id_departments' => $id_departments);
            return view('audit_report_activity.print_chart', $data
                )->with('page', 'Laporan Aktivitas Audit');
        }
    }

    function print_audit_report_email($id,$month)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;


        if($month != null){
            $queryLaporanAktivitas = "SELECT
              *,
              audit_report_activities.id AS id_audit_report 
            FROM
              audit_report_activities
              JOIN activity_lists ON activity_lists.id = audit_report_activities.activity_list_id
              LEFT JOIN audit_guidances ON audit_guidance_id = audit_guidances.id 
            WHERE
              activity_lists.id = '".$id."' 
              AND activity_lists.department_id = '".$id_departments."' 
              AND audit_guidances.`month` = '".$month."' 
              AND audit_guidances.deleted_at IS NULL 
              AND audit_report_activities.deleted_at IS NULL";
            $laporanAktivitas = DB::select($queryLaporanAktivitas);
            $laporanAktivitas2 = DB::select($queryLaporanAktivitas);
        }

        $monthTitle = date("F Y", strtotime($month));

        // var_dump($subsection);
        $jml_null = 0;
        foreach($laporanAktivitas2 as $laporanAktivitas2){
            // $product = $samplingCheck->product;
            // $proses = $samplingCheck->proses;
            $date = $laporanAktivitas2->date;
            $foreman = $laporanAktivitas2->foreman;
            $section = $laporanAktivitas2->section;
            $approval_leader = $laporanAktivitas2->approval_leader;
            $approved_date_leader = $laporanAktivitas2->approved_date_leader;
            $subsection = $laporanAktivitas2->subsection;
            $leader = $laporanAktivitas2->leader;
            if ($laporanAktivitas2->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            $approved_date = $laporanAktivitas2->approved_date;
        }
        if($laporanAktivitas == null){
            // return redirect('/index/production_audit/index/'.$id.'/'.$request->get('product').'/'.$request->get('proses'))->with('error', 'Data Tidak Tersedia.')->with('page', 'Production Audit');
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{
            $data = array(
                          'subsection' => $subsection,
                          'leader' => $leader,
                          'foreman' => $foreman,
                          'section' => $section,
                          'role_code' => Auth::user()->role_code,
                          'approval_leader' => $approval_leader,
                          'approved_date_leader' => $approved_date_leader,
                          'monthTitle' => $monthTitle,
                          'date' => $date,
                          'month' => $month,
                          'jml_null' => $jml_null,
                          'approved_date' => $approved_date,
                          'laporanAktivitas' => $laporanAktivitas,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'id_departments' => $id_departments);
            return view('audit_report_activity.print_email', $data
                )->with('page', 'Laporan Aktivitas Audit');
        }
    }

    public function sendemail(Request $request,$id)
      {
          $activityList = ActivityList::find($id);
          $activity_name = $activityList->activity_name;
          $departments = $activityList->departments->department_name;
          $activity_alias = $activityList->activity_alias;
          $id_departments = $activityList->departments->id;

          $subsection = $request->get('subsection');
          $month = $request->get('month');
          // $date = date('Y-m-d', strtotime($request->get('date')));
          $query_laporan_aktivitas = "SELECT
            activity_lists.*,
            departments.*,
            audit_report_activities.*,
            audit_report_activities.id AS id_audit_report 
          FROM
            audit_report_activities
            JOIN activity_lists ON activity_lists.id = audit_report_activities.activity_list_id
            JOIN departments ON departments.id = activity_lists.department_id
            LEFT JOIN audit_guidances ON audit_guidances.id = audit_guidance_id 
          WHERE
            activity_lists.id = '".$id."' 
            AND activity_lists.department_id = '".$id_departments."' 
            AND audit_report_activities.subsection = '".$subsection."' 
            AND audit_guidances.`month` = '".$month."' 
            AND audit_report_activities.deleted_at IS NULL";
          $laporan_aktivitas = DB::select($query_laporan_aktivitas);
          $laporan_aktivitas2 = DB::select($query_laporan_aktivitas);
          $laporan_aktivitas3 = DB::select($query_laporan_aktivitas);

          // var_dump($sampling_check2);

          if($laporan_aktivitas2 != null){
            foreach($laporan_aktivitas2 as $laporan_aktivitas2){
                $foreman = $laporan_aktivitas2->foreman;
                $id_audit_report = $laporan_aktivitas2->id_audit_report;
                $send_status = $laporan_aktivitas2->send_status;
              }

              foreach ($laporan_aktivitas3 as $laporan_aktivitas3) {
                    $laktivitas = AuditReportActivity::find($laporan_aktivitas3->id_audit_report);
                    $laktivitas->send_status = "Sent";
                    $laktivitas->send_date = date('Y-m-d');
                    $laktivitas->approval_leader = "Approved";
                    $laktivitas->approved_date_leader = date('Y-m-d');
                    $laktivitas->save();
              }

              // $queryEmail = "select employee_syncs.employee_id,employee_syncs.name,email from users join employee_syncs on employee_syncs.employee_id = users.username where employee_syncs.name = '".$foreman."'";
              $queryEmail = "select employee_syncs.employee_id,employee_syncs.name,email from users join employee_syncs on employee_syncs.employee_id = users.username where employee_syncs.name = '".$foreman."'";
              $email = DB::select($queryEmail);
              // var_dump($foreman);
              // var_dump($email);
              foreach($email as $email){
                $mail_to = $email->email;            
              }
          }else{
            return redirect('/index/audit_report_activity/index/'.$id)->with('error', 'Data tidak tersedia.')->with('page', 'Sampling Check');
          }

          if($send_status == "Sent"){
            return redirect('/index/audit_report_activity/index/'.$id)->with('error', 'Data pernah dikirim.')->with('page', 'Laporan Aktivitas Audit');
          }
          elseif($laporan_aktivitas != null){
              Mail::to($mail_to)->bcc('mokhamad.khamdan.khabibi@music.yamaha.com')->send(new SendEmail($laporan_aktivitas, 'laporan_aktivitas'));
              return redirect('/index/audit_report_activity/index/'.$id)->with('status', 'Your E-mail has been sent.')->with('page', 'Laporan Aktivitas Audit');
          }
          else{
            return redirect('/index/audit_report_activity/index/'.$id)->with('error', 'Data tidak tersedia.')->with('page', 'Laporan Aktivitas Audit');
          }
      }

      public function approval(Request $request,$id)
      {
          $approve = $request->get('approve');
          foreach($approve as $approve){
            $audit_report_activity = AuditReportActivity::find($approve);
            $subsection = $audit_report_activity->subsection;
            $month = substr($audit_report_activity->date,0,7);
            $date = $audit_report_activity->date;
            $audit_report_activity->approval = "Approved";
            $audit_report_activity->approved_date = date('Y-m-d');
            $audit_report_activity->save();
          }
          return redirect('/index/audit_report_activity/print_audit_report_email/'.$id.'/'.$month)->with('status', 'Approved.')->with('page', 'Laporan Aktivitas Audit');
      }

      public function getemployee(Request $request){

        $queryOperator = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs where employee_id = '".$request->get('employee_id')."'";
        $employee = DB::select($queryOperator);
        foreach ($employee as $key) {
            $name = $key->name;
        }

        $response = array(
            'status' => true,
            'lists' => $employee,
            'name' => $name,
        );
        return Response::json($response);
    }

    public function scanEmployee(Request $request)
    {
      if (is_numeric($request->get('employee_id'))) {
        $nik = $request->get('employee_id');

        if(strlen($nik) > 9){
            $nik = substr($nik,0,9);
        }

        $employee = db::table('employees')->where('tag', 'like', '%'.$nik.'%')->first();
      }else{
        $nik = $request->get('employee_id');

        $employee = db::table('employees')->where('employee_id', 'like', '%'.$nik.'%')->first();
      }

      if(count($employee) > 0){
        $response = array(
            'status' => true,
            'message' => 'Scan Peserta Berhasil',
            'employee' => $employee
        );
        return Response::json($response);
      }
      else{
          $response = array(
              'status' => false,
              'message' => 'Employee ID Invalid'
          );
          return Response::json($response);
      }
    }

    public function fetchAuditIkQcKoteihyo(Request $request)
    {
      try {
        $document_number_ik = $request->get('document_number_ik');
        $document_number_qc_koteihyo = $request->get('document_number_qc_koteihyo');

        // $document = DB::connection('ympimis_2')->table('qa_qc_koteihyo_documents')->where('document_number_ik','like','%'.$document_number_ik.'%')->first();

        // if ($document) {
          $ik = DB::connection('ympimis_2')->table('documents')->where('document_number',$document_number_ik)->first();
          $qc_koteihyo = DB::connection('ympimis_2')->table('documents')->where('document_number',$document_number_qc_koteihyo)->first();
          $response = array(
              'status' => true,
              'ik' => $ik,
              'qc_koteihyo' => $qc_koteihyo,
          );
          return Response::json($response);
        // }else{
        //   $response = array(
        //       'status' => false,
        //       'message' => 'Document Not Found'
        //   );
        //   return Response::json($response);
        // }
      } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
      }
    }

    public function indexAuditQAVerification($status,$id)
    {
      $emp_id = strtoupper(Auth::user()->username);
      $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
      $audit_report = AuditReportActivity::where('id',$id)->first();
      $audit_guidance = AuditGuidance::where('id',$audit_report->audit_guidance_id)->first();
      $statuses = 'Approved';
      $reason = '';
      if ($status == 'approve') {
        if ($audit_report->qa_verification == null) {
          $audit_report->qa_verification = 'Approved_'.Auth::user()->username.'_'.Auth::user()->name.'_'.date('Y-m-d H:i:s');
          $audit_report->save();
          $message =  ' Berhasil Disetujui';
        }else{
          $message =  ' '.explode('_', $audit_report->qa_verification)[0].' by '.explode('_', $audit_report->qa_verification)[1].' - '.explode('_', $audit_report->qa_verification)[2].' pada '.explode('_', $audit_report->qa_verification)[3];
          $reason = $audit_report->qa_verification_reason;
        }
        return view('audit_report_activity.qa_verification')->with('page','Approve Temuan Revisi QC Koteihyo')->with('head','Approve Temuan Revisi QC Koteihyo')->with('message','Temuan Revisi QC Koteihyo Dokumen '.$audit_report->no_dokumen.' - '.$audit_report->nama_dokumen.$message)->with('id',$id)->with('audit_report',$audit_report)->with('statuses',$statuses)->with('reason',$reason);
      }else{
        if ($audit_report->qa_verification == null) {
          $message =  'Masukkan Reason Reject Dokumen '.$audit_report->no_dokumen.' - '.$audit_report->nama_dokumen;
          $statuses = 'Rejected';
        }else{
          $message =  'Temuan Revisi QC Koteihyo Dokumen '.$audit_report->no_dokumen.' - '.$audit_report->nama_dokumen.' '.explode('_', $audit_report->qa_verification)[0].' by '.explode('_', $audit_report->qa_verification)[1].' - '.explode('_', $audit_report->qa_verification)[2].' pada '.explode('_', $audit_report->qa_verification)[3];
          $reason = $audit_report->qa_verification_reason;
        }
        return view('audit_report_activity.qa_verification')->with('page','Reject Temuan Revisi QC Koteihyo')->with('head','Reject Temuan Revisi QC Koteihyo')->with('message',$message)->with('id',$id)->with('audit_report',$audit_report)->with('statuses',$statuses)->with('reason',$reason);
      }
    }

    public function inputAuditQAVerification(Request $request)
    {
      try {
        $id = $request->get('id');
        $reason = $request->get('reason');

        $audit_report = AuditReportActivity::where('id',$id)->first();
        $audit_report->qa_verification = 'Rejected_'.Auth::user()->username.'_'.Auth::user()->name.'_'.date('Y-m-d H:i:s');
        $audit_report->qa_verification_reason = $reason;
        $audit_report->handling = 'Tidak Ada Penanganan';
        $audit_report->save();

        $mail_to = [];

        $foreman = Approver::where('department',$audit_report->department)->where('remark','Foreman')->first();
        if (!$foreman) {
          $foreman = Approver::where('department',$audit_report->department)->where('remark','Chief')->first();
        }

        if ($foreman) {
          array_push($mail_to, $foreman->approver_email);
        }

        $audit = AuditReportActivity::where('id',$id)->first();
        $audit_guidance = AuditGuidance::where('id',$audit->audit_guidance_id)->first();

        $datas = [
          'category' => 'Revisi QC Koteihyo Ditolak oleh QA',
          'data' => $audit,
          'audit_guidance' => $audit_guidance,
          'reason' => $reason,
        ];

        Mail::to($mail_to)
        ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
        ->send(new SendEmail($datas, 'ik_obsolete'));

        $response = array(
            'status' => true,
            'message' => 'Reject Succeeded'
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

    public function indexManageDocument()
    {
      $all_doc = DB::connection('ympimis_2')->table('documents')->get();
      return view('audit_report_activity.manage_document')
      ->with('page','Manage Document')
      ->with('head','Manage Document')
      ->with('title','Manage Document')
      ->with('title_jp','??')
      ->with('all_doc',$all_doc)
      ->with('all_doc2',$all_doc)
      ->with('all_doc3',$all_doc)
      ->with('all_doc4',$all_doc);
    }

    public function fetchManageDocument(Request $request)
    {
      try {
        $document = DB::connection('ympimis_2')->table('qa_qc_koteihyo_documents')->get();
        $document_all = DB::connection('ympimis_2')->table('documents')->get();
        $response = array(
            'status' => true,
            'document' => $document,
            'document_all' => $document_all,
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

    public function inputManageDocument(Request $request)
    {
      try {
        $document_number_qc_koteihyo = $request->get('document_number_qc_koteihyo');
        $document_number_ik = $request->get('document_number_ik');

        $input = DB::connection('ympimis_2')->table('qa_qc_koteihyo_documents')->insert([
          'document_number_qc_koteihyo' => $document_number_qc_koteihyo,
          'document_number_ik' => $document_number_ik,
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

    public function updateManageDocument(Request $request)
    {
      try {
        $document_number_qc_koteihyo = $request->get('document_number_qc_koteihyo');
        $document_number_ik = $request->get('document_number_ik');
        $id = $request->get('id');

        $input = DB::connection('ympimis_2')->table('qa_qc_koteihyo_documents')->where('id',$id)->update([
          'document_number_qc_koteihyo' => $document_number_qc_koteihyo,
          'document_number_ik' => $document_number_ik,
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

    public function deleteManageDocument($id)
    {
      try {
        $input = DB::connection('ympimis_2')->table('qa_qc_koteihyo_documents')->where('id',$id)->delete();
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

    public function indexUnmatchDocument()
    {
      $document = DB::connection('ympimis_2')->SELECT("SELECT
            * 
          FROM
            documents 
          WHERE 
            category = 'IK'");

      return view('audit_report_activity.unmatch_document')
      ->with('page','Unmatch Document')
      ->with('head','Unmatch Document')
      ->with('title','Unmatch Document')
      ->with('document',$document)
      ->with('title_jp','??');
    }

    public function fetchUnmatchDocument(Request $request)
    {
      try {
        $department = DB::SELECT("SELECT DISTINCT
          ( department_name ),
          department_shortname
        FROM
          audit_guidances
          LEFT JOIN activity_lists ON activity_lists.id = audit_guidances.activity_list_id
          JOIN departments ON departments.id = department_id 
        WHERE
          audit_guidances.deleted_at IS NULL 
          AND department_id IS NOT NULL 
        ORDER BY
          department_id");

        $departmentin2 = '';

        if ($request->get('department') == '') {
          $dept = '';
          for ($i=0; $i < count($department); $i++) { 
            $dept = $dept."'".$department[$i]->department_name."'";
            if($i != (count($department)-1)){
              $dept = $dept.',';
            }
          }
          $departmentin = " and department_name in (".$dept.") ";
        }else{
          $departmentin = " and department_name in ('".$request->get('department')."') ";
          $departmentin2 = " and department_name in ('".$request->get('department')."') ";
        }

        $dept_leader = "";
        $where_leader = "";
        if ($request->get('leader') != '') {
          $where_leader = "AND audit_guidances.leader = '".$request->get('leader')."'";
          $dept_leaders = DB::SELECT("SELECT
              employee_syncs.department 
            FROM
              users
              JOIN employee_syncs ON employee_syncs.employee_id = username 
            WHERE
              users.`name` = '".$request->get('leader')."'");
          $dept_leader = "AND department_name = '".$dept_leaders[0]->department."'";
        }

        $ik_std = DB::connection('ympimis_2')->SELECT("SELECT
            * 
          FROM
            documents 
          WHERE
            `status` = 'Active' 
            AND category = 'IK'
            and deleted_at is null
            ".$dept_leader."
          ".$departmentin."");

        $ik_std_all = DB::connection('ympimis_2')->SELECT("SELECT
            * 
          FROM
            documents 
          WHERE
            category = 'IK'
            and deleted_at is null
            ".$dept_leader."
          ".$departmentin."");

        $periode = DB::SELECT("SELECT DISTINCT
                ( fiscal_year ) 
            FROM
                weekly_calendars 
            WHERE
                week_date = DATE(
                NOW())");

        $ik_leader = DB::SELECT("SELECT
          audit_guidances.id,
          audit_guidances.activity_list_id,
          no_dokumen,
          nama_dokumen,
          leader,
          foreman,
          `month`,
          audit_guidances.periode,
          `status`,
          department_name,
          department_shortname 
        FROM
          audit_guidances
          LEFT JOIN activity_lists ON activity_lists.id = audit_guidances.activity_list_id
          LEFT JOIN departments ON departments.id = department_id 
        WHERE
          audit_guidances.periode = '".$periode[0]->fiscal_year."' 
          and audit_guidances.deleted_at IS NULL 
          ".$departmentin2."
        ORDER BY
          `month`");

        $response = array(
            'status' => true,
            'department' => $department,
            'periode' => $periode,
            'ik_std' => $ik_std,
            'ik_std_all' => $ik_std_all,
            'ik_leader' => $ik_leader,
            'depts' => $request->get('department'),
            'leader' => $request->get('leader'),
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
}
