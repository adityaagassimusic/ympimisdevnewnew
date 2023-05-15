<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\AuditGuidance;
use App\WeeklyCalendar;
use App\EmployeeSync;
use App\Approver;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class AuditGuidanceController extends Controller
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
    $audit_guidance = AuditGuidance::where('activity_list_id',$id)->where('audit_guidances.deleted_at',null)
    ->orderBy('audit_guidances.id','desc')->get();

    $activity_name = $activityList->activity_name;
    $departments = $activityList->departments->department_name;
    $id_departments = $activityList->departments->id;
    $activity_alias = $activityList->activity_alias;
    $leader = $activityList->leader_dept;
    $foreman = $activityList->foreman_dept;
    $frequency = $activityList->frequency;

    $documents = DB::connection('ympimis_2')->table('documents')->where('category','IK')->get();

    $bulan = date('Y-m');

    $fynow = DB::select("select DISTINCT(fiscal_year) from weekly_calendars where DATE_FORMAT(week_date,'%Y-%m') = '".$bulan."'");
    foreach($fynow as $fynow){
      $fy = $fynow->fiscal_year;
    }

    $data = array('audit_guidance' => $audit_guidance,
      'departments' => $departments,
      'activity_name' => $activity_name,
      'activity_alias' => $activity_alias,
      'leader' => $leader,
      'foreman' => $foreman,
      'fy' => $fy,
      'id' => $id,
      'documents' => $documents,
      'documents2' => $documents,
      'frequency' => $frequency,
      'id_departments' => $id_departments);
    return view('audit_guidance.index', $data)
    ->with('page', 'Schedule Audit');
  }

  function filter_guidance(Request $request,$id)
  {
    $activityList = ActivityList::find($id);
    if(strlen($request->get('month')) != null){
      $month = $request->get('month');
      $audit_guidance = AuditGuidance::where('activity_list_id',$id)
      ->where('month', '=', $month)
      ->where('audit_guidances.deleted_at',null)
      ->orderBy('audit_guidances.id','desc')
      ->get();
    }
    else{
      $audit_guidance = AuditGuidance::where('activity_list_id',$id)->where('audit_guidances.deleted_at',null)
      ->orderBy('audit_guidances.id','desc')->get();
    }

    $bulan = date('Y-m');

    $fynow = DB::select("select DISTINCT(fiscal_year) from weekly_calendars where DATE_FORMAT(week_date,'%Y-%m') = '".$bulan."'");
    foreach($fynow as $fynow){
      $fy = $fynow->fiscal_year;
    }


        // foreach ($activityList as $activityList) {
    $activity_name = $activityList->activity_name;
    $departments = $activityList->departments->department_name;
    $activity_alias = $activityList->activity_alias;
    $id_departments = $activityList->departments->id;
    $leader = $activityList->leader_dept;
    $foreman = $activityList->foreman_dept;
    $frequency = $activityList->frequency;

    $documents = DB::connection('ympimis_2')->table('documents')->where('category','IK')->get();
        // }
    $data = array(
      'audit_guidance' => $audit_guidance,
      'departments' => $departments,
      'activity_name' => $activity_name,
      'activity_alias' => $activity_alias,
      'leader' => $leader,
      'foreman' => $foreman,
      'fy' => $fy,
      'id' => $id,
      'frequency' => $frequency,
      'documents' => $documents,
      'documents2' => $documents,
      'id_departments' => $id_departments);
    return view('audit_guidance.index', $data
  )->with('page', 'Schedule Audit');
  }

  function show($id,$audit_guidance_id)
  {
    $activityList = ActivityList::find($id);
    $audit_guidance = AuditGuidance::find($audit_guidance_id);
        // foreach ($activityList as $activityList) {
    $activity_name = $activityList->activity_name;
    $departments = $activityList->departments->department_name;
    $activity_alias = $activityList->activity_alias;
    $leader = $activityList->leader_dept;
    $foreman = $activityList->foreman_dept;

        // }
    $data = array('audit_guidance' => $audit_guidance,
      'departments' => $departments,
      'activity_name' => $activity_name,
      'leader' => $leader,
      'foreman' => $foreman,
      'id' => $id);
    return view('audit_guidance.view', $data
  )->with('page', 'Schedule Audit');
  }

  public function destroy($id,$audit_guidance_id)
  {
    $audit_guidance = AuditGuidance::find($audit_guidance_id);
    $audit_guidance->delete();

    return redirect('/index/audit_guidance/index/'.$id)
    ->with('status', 'Schedule has been deleted.')
    ->with('page', 'Schedule Audit');        
  }

  function store(Request $request,$id)
  {
    try{

      $documents = DB::connection('ympimis_2')->table('documents')->where('document_number',$request->get('inputnama_dokumen'))->first();
      $id_user = Auth::id();
      AuditGuidance::create([
        'activity_list_id' => $id,
        'nama_dokumen' => $documents->title,
        'no_dokumen' => $documents->document_number,
        'auditor' => $request->get('auditor'),
        'date' => date('Y-m-d'),
        'month' => $request->get('inputmonth'),
        'periode' => $request->get('inputperiode'),
        'status' => 'Belum Dikerjakan',
        'leader' => $request->get('inputleader'),
        'foreman' => $request->get('inputforeman'),
        'created_by' => $id_user
      ]);

      return redirect('/index/audit_guidance/index/'.$id)->with('status', 'Audit Guidance data has been created.')->with('page', 'Audit Guidance');
    }catch(\Exception $e){
      $response = array(
        'status' => false,
        'message' => $e->getMessage(),
      );
      return Response::json($response);
    }
  }

  public function getdetail(Request $request)
  {
   try{
    $detail = AuditGuidance::find($request->get("id"));
    $data = array('audit_guidance_id' => $detail->id,
      'nama_dokumen' => $detail->nama_dokumen,
      'no_dokumen' => $detail->no_dokumen,
      'month' => $detail->month,
      'periode' => $detail->periode,
      'leader' => $detail->leader,
      'foreman' => $detail->foreman);

    $response = array(
      'status' => true,
      'data' => $data
    );
    return Response::json($response);

  }
  catch (QueryException $audit_guidance){
    $error_code = $audit_guidance->errorInfo[1];
    if($error_code == 1062){
     $response = array(
      'status' => false,
      'datas' => "Name already exist",
    );
     return Response::json($response);
   }
   else{
     $response = array(
      'status' => false,
      'datas' => "Update  Error.",
    );
     return Response::json($response);
   }
 }
}

function update(Request $request,$id,$audit_guidance_id)
{
  try{
    $documents = DB::connection('ympimis_2')->table('documents')->where('document_number',$request->get('editnama_dokumen'))->first();

    $audit_guidance = AuditGuidance::find($audit_guidance_id);
    $audit_guidance->no_dokumen = $documents->document_number;
    $audit_guidance->nama_dokumen = $documents->title;
    $audit_guidance->month = $request->get('editmonth');
    $audit_guidance->save();

    $audit = DB::table('audit_report_activities')->where('audit_guidance_id',$audit_guidance_id)->first();
    if ($audit) {
      $updateaudit = DB::table('audit_report_activities')->where('audit_guidance_id',$audit_guidance_id)->update([
        'no_dokumen' => $documents->document_number,
        'nama_dokumen' => $documents->title,
      ]);
    }

    return redirect('index/audit_guidance/index/'.$id)
    ->with('page', 'Audit Guidance')->with('status', 'Audit Guidance has been updated.');
  }
  catch (QueryException $e){
    $error_code = $e->errorInfo[1];
    if($error_code == 1062){
      return back()->with('error', 'Audit Guidance already exist.')->with('page', 'Audit Guidance');
    }
    else{
      return back()->with('error', $e->getMessage())->with('page', 'Audit Guidance');
    }
  }
}

function updateNew(Request $request,$id,$audit_guidance_id)
{
  try{
    $documents = DB::connection('ympimis_2')->table('documents')->where('document_number',$request->get('editnama_dokumen'))->first();

    $audit_guidance = db::table('audit_guidances')->where('id',$audit_guidance_id)->first();
    if ($audit_guidance) {
      $update = db::table('audit_guidances')->where('id',$audit_guidance_id)->update([
        'no_dokumen' => $documents->document_number,
        'nama_dokumen' => $documents->title,
      ]);
    }
    // $audit_guidance->no_dokumen = $documents->document_number;
    // $audit_guidance->nama_dokumen = $documents->title;
    // $audit_guidance->month = $request->get('editmonth');
    // $audit_guidance->save();

    $audit = DB::table('audit_report_activities')->where('audit_guidance_id',$audit_guidance_id)->first();
    if ($audit) {
      $updateaudit = DB::table('audit_report_activities')->where('audit_guidance_id',$audit_guidance_id)->update([
        'no_dokumen' => $documents->document_number,
        'nama_dokumen' => $documents->title,
      ]);
    }

    return redirect('index/audit_report_activity/unmatch');
    // ->with('page', 'Audit Guidance')->with('status', 'Audit Guidance has been updated.');
  }
  catch (QueryException $e){
    $error_code = $e->errorInfo[1];
    if($error_code == 1062){
      return back()->with('error', 'Audit Guidance already exist.')->with('page', 'Audit Guidance');
    }
    else{
      return back()->with('error', $e->getMessage())->with('page', 'Audit Guidance');
    }
  }
}



public function downloadTemplate()
{
  $file_path = public_path('data_file/TemplateScheduleAuditIK.xlsx');
  return response()->download($file_path);
}

    public function uploadTemplate(Request $request)
    {
        $filename = "";
        $file_destination = 'data_file/audit_ik';

        if (count($request->file('newAttachment')) > 0) {
            try {
                $file = $request->file('newAttachment');
                $filename = 'audit_ik_schedule_' . date('YmdHisa') . '.' . $request->input('extension');
                $file->move($file_destination, $filename);

                $excel = 'data_file/audit_ik/' . $filename;
                $rows = Excel::load($excel, function ($reader) {
                    $reader->noHeading();
                    $reader->skipRows(1);

                    $reader->each(function ($row) {
                    });
                })->toObject();

                $activityList = ActivityList::where('id',$request->get('activity_list_id'))->first();

                $fy = WeeklyCalendar::where('week_date',date('Y-m-d'))->first();

                for ($i = 0; $i < count($rows); $i++) {
                  $documents = DB::connection('ympimis_2')->table('documents')->where('document_number',$rows[$i][0])->first();
                  AuditGuidance::create([
                    'activity_list_id' => $request->get('activity_list_id'),
                    'nama_dokumen' => $documents->title,
                    'no_dokumen' => $documents->document_number,
                    'date' => date('Y-m-d'),
                    'month' => date('Y-m',strtotime($rows[$i][2])),
                    'periode' => $fy->fiscal_year,
                    'status' => 'Belum Dikerjakan',
                    'leader' => $activityList->leader_dept,
                    'foreman' => $activityList->foreman_dept,
                    'created_by' => Auth::user()->id
                  ]);
                }

                $response = array(
                    'status' => true,
                    'message' => 'Menu succesfully uploaded',
                );
                return Response::json($response);
            } catch (\Exception$e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        } else {
            $response = array(
                'status' => false,
                'message' => 'Please select file to attach',
            );
            return Response::json($response);
        }
    }

    public function sendEmail(Request $request)
    {
      try {
        $id = $request->get('id');
        $periode = $request->get('periode');
        $guidance = AuditGuidance::where('activity_list_id',$id)->where('periode',$periode)->where('send_status',null)->get();

        $mail_to = [];
        if ($guidance[0]->approval_foreman == null) {
          $users = User::where('name',$guidance[0]->leader)->first();
          $emp = EmployeeSync::where('employee_id',$users->username)->first();
          $foreman = Approver::where('department',$emp->department)->where('section',$emp->section)->where('remark','Foreman')->first();
          if (!$foreman) {
            $foreman = Approver::where('department',$emp->department)->where('section',$emp->section)->where('remark','Chief')->first();
          }
          array_push($mail_to, $foreman->approver_email);

          $data_all = DB::SELECT("SELECT
            * 
          FROM
            audit_guidances 
          WHERE
            periode = '".$periode."'             
            AND deleted_at IS NULL 
            AND send_status IS NULL 
            AND activity_list_id = '".$id."'");

          $month_all = DB::select("SELECT DISTINCT
            ( `month` ) 
          FROM
            audit_guidances 
          WHERE
            periode = '".$periode."'             
            AND deleted_at IS NULL 
            AND send_status IS NULL
            AND activity_list_id = '".$id."'");

          $data = array(
            'datas' => $data_all,
            'month' => $month_all,
            'remark' => 'Foreman',
            'periode' => $periode,
            'id' => $id,
          );

          Mail::to($mail_to)->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'],'BCC')->send(new SendEmail($data, 'audit_guidance'));
          $update = DB::table('audit_guidances')->where('activity_list_id',$id)->where('periode',$periode)->where('send_status',null)->update([
            'send_status' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
          ]);
        }

        if ($guidance[0]->approval_manager == null && $guidance[0]->approval_foreman != null) {
          $users = User::where('name',$guidance[0]->leader)->first();
          $emp = EmployeeSync::where('employee_id',$users->username)->first();
          $manager = Approver::where('department',$emp->department)->where('remark','Manager')->first();

          $update = DB::table('audit_guidances')->where('activity_list_id',$id)->where('periode',$periode)->update([
            'send_status' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
          ]);
          array_push($mail_to, $manager->approver_email);

          $data_all = DB::SELECT("SELECT
            * 
          FROM
            audit_guidances 
          WHERE
            periode = '".$periode."'             
            AND deleted_at IS NULL 
            AND activity_list_id = '".$id."'");

          $month_all = DB::select("SELECT DISTINCT
            ( `month` ) 
          FROM
            audit_guidances 
          WHERE
            periode = '".$periode."'             
            AND deleted_at IS NULL 
            AND activity_list_id = '".$id."'");

          $data = array(
            'datas' => $data_all,
            'month' => $month_all,
            'remark' => 'Manager',
            'periode' => $periode,
            'id' => $id,
          );

          Mail::to($mail_to)->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'],'BCC')->send(new SendEmail($data, 'audit_guidance'));
        }

        $response = array(
            'status' => true,
        );
        return Response::json($response);
      } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage(),
        );
        return Response::json($response);
      }
    }

    public function approvalAuditGuidance($id,$periode,$remark)
    {
      $mail_to = [];
      if ($remark == 'Foreman') {
        $guidance = DB::table('audit_guidances')->where('activity_list_id',$id)->where('periode',$periode)->where('approval_foreman',null)->first();
        if ($guidance) {
          $update = DB::table('audit_guidances')->where('activity_list_id',$id)->where('periode',$periode)->where('approval_foreman',null)->update([
            'approval_foreman' => Auth::user()->username.'_'.Auth::user()->name.'_'.date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ]);

          $users = User::where('name',$guidance->leader)->first();
          $emp = EmployeeSync::where('employee_id',$users->username)->first();
          $manager = Approver::where('department',$emp->department)->where('remark','Manager')->first();
          array_push($mail_to, $manager->approver_email);

          $data_all = DB::SELECT("SELECT
            * 
          FROM
            audit_guidances 
          WHERE
            periode = '".$periode."'             
            AND deleted_at IS NULL 
            AND activity_list_id = '".$id."'");

          $month_all = DB::select("SELECT DISTINCT
            ( `month` ) 
          FROM
            audit_guidances 
          WHERE
            periode = '".$periode."'             
            AND deleted_at IS NULL 
            AND activity_list_id = '".$id."'");

          $data = array(
            'datas' => $data_all,
            'month' => $month_all,
            'remark' => 'Manager',
            'periode' => $periode,
            'id' => $id,
          );

          Mail::to($mail_to)->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'],'BCC')->send(new SendEmail($data, 'audit_guidance'));
          
          return view('audit_guidance.approval')->with('head','Persetujuan Schedule Audit IK oleh Foreman')->with('message','Schedule Audit IK Leader '.$guidance->leader.' Periode '.$guidance->periode.' Telah Disetujui')->with('page','Persetujuan Schedule Audit IK');
        }else{
          return view('audit_guidance.approval')->with('head','Persetujuan Schedule Audit IK oleh Foreman')->with('message','Schedule Audit IK Pernah Disetujui')->with('page','Persetujuan Schedule Audit IK');
        }
      }

      if ($remark == 'Manager') {
        $guidance = DB::table('audit_guidances')->where('activity_list_id',$id)->where('periode',$periode)->where('approval_manager',null)->first();
        if ($guidance) {
          $update = DB::table('audit_guidances')->where('activity_list_id',$id)->where('periode',$periode)->where('approval_manager',null)->update([
            'approval_manager' => Auth::user()->username.'_'.Auth::user()->name.'_'.date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ]);

          return view('audit_guidance.approval')->with('head','Persetujuan Schedule Audit IK oleh Manager')->with('message','Schedule Audit IK Leader '.$guidance->leader.' Periode '.$guidance->periode.' Telah Disetujui')->with('page','Persetujuan Schedule Audit IK');
        }else{
          return view('audit_guidance.approval')->with('head','Persetujuan Schedule Audit IK oleh Manager')->with('message','Schedule Audit IK Pernah Disetujui')->with('page','Persetujuan Schedule Audit IK');
        }
      }
    }
}
