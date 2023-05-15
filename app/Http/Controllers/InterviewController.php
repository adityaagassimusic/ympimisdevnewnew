<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Interview;
use App\InterviewDetail;
use App\InterviewPicture;
use App\PointingCallItem;
use App\EmployeeSync;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class InterviewController extends Controller
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
      	$interview = Interview::where('activity_list_id',$id)
              ->orderBy('interviews.id','desc')->get();

      	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;
        $frequency = $activityList->frequency;

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
        $subsection4 = DB::select($querySubSection);

        $querySection = "SELECT
            DISTINCT(employee_syncs.section) AS section_name
          FROM
            employee_syncs 
          WHERE
          employee_syncs.section is not null
          AND
            department LIKE '%".$departments."%'";
        $section = DB::select($querySection);

        $status = 'leader';

        // var_dump($productionAudit);
    	$data = array('interview' => $interview,
                      'subsection' => $subsection,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
                      'subsection4' => $subsection4,
            				  'departments' => $departments,
            				  'leader' => $leader,
                      'status' => $status,
                      'foreman' => $foreman,
                      'section' => $section,
                      'frequency' => $frequency,
            				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
            				  'id' => $id,
                      'title' => 'Interview Pointing Call',
                      'id_departments' => $id_departments);
    	return view('interview.index', $data
    		)->with('page', 'Interview')->with('title', 'Interview Pointing Call')->with('title_jp', '');
    }

    function indexPointingCall()
    {
        $employee_id = Auth::user()->username;
        $emp = EmployeeSync::select('activity_lists.id')->where('employee_id',$employee_id)->join('activity_lists','activity_lists.leader_dept','employee_syncs.name')->first();
        if (count($emp) > 0) {
          $activityList = ActivityList::find($emp->id);
          $interview = Interview::where('activity_list_id',$emp->id)
                ->orderBy('interviews.id','desc')->get();

          $activity_name = $activityList->activity_name;
          $departments = $activityList->departments->department_name;
          $id_departments = $activityList->departments->id;
          $activity_alias = $activityList->activity_alias;
          $leader = $activityList->leader_dept;
          $foreman = $activityList->foreman_dept;
          $frequency = $activityList->frequency;

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
          $subsection4 = DB::select($querySubSection);

          $querySection = "SELECT
              DISTINCT(employee_syncs.section) AS section_name
            FROM
              employee_syncs 
            WHERE
            employee_syncs.section is not null
            AND
              department LIKE '%".$departments."%'";
          $section = DB::select($querySection);

          $status = 'chief';

          $queryFY = "select DISTINCT(fiscal_year) from weekly_calendars where week_date = DATE(NOW())";
          $fy = DB::select($queryFY);

          foreach ($fy as $key) {
            $fiscal = $key->fiscal_year;
          }

          // var_dump($productionAudit);
          $data = array('interview' => $interview,
                          'subsection' => $subsection,
                          'subsection2' => $subsection2,
                          'subsection3' => $subsection3,
                          'subsection4' => $subsection4,
                          'departments' => $departments,
                          'leader' => $leader,
                          'fy' => $fiscal,
                          'title' => 'Interview Pointing Call',
                          'foreman' => $foreman,
                          'section' => $section,
                          'status' => $status,
                          'frequency' => $frequency,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $emp->id,
                          'id_departments' => $id_departments);
          return view('interview.index_pointing_call', $data
            )->with('title', 'Interview Pointing Call')->with('title_jp', '');
        }else{
          return view('404');
        }
    }

    function filter_interview(Request $request,$id)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;
        $frequency = $activityList->frequency;

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
        $subsection4 = DB::select($querySubSection);

        $querySection = "SELECT
            DISTINCT(employee_syncs.section) AS section_name
          FROM
            employee_syncs 
          WHERE
          employee_syncs.section is not null
          AND
            department LIKE '%".$departments."%'";
        $section = DB::select($querySection);

        $status = 'leader';

        $interview = Interview::where('activity_list_id',$id)
                ->orderBy('interviews.id','desc');

        if ($request->get('subsection') != '') {
          $interview = $interview->where('subsection',$request->get('subsection'));
        }

        if ($request->get('month') != '') {
          $interview = $interview->where(DB::RAW('DATE_FORMAT(date,"%Y-%m")'),$request->get('month'));
        }

        $interview = $interview->get();
        // if($request->get('subsection') != null && strlen($request->get('month')) != null){
        //     $subsection = $request->get('subsection');
        //     // $date = date('Y-m',$request->get('month'));
        //     // $year = date_format($date, 'Y');
        //     // $month = date_format($date, 'm');
        //     $year = substr($request->get('month'),0,4);
        //     $month = substr($request->get('month'),-2);
        //     $interview = Interview::where('activity_list_id',$id)
        //         ->where('subsection',$subsection)
        //         // ->where(DATE_FORMAT('date',"%Y-%m"),$month)
        //         ->whereYear('date', '=', $year)
        //         ->whereMonth('date', '=', $month)
        //         ->orderBy('interviews.id','desc')
        //         ->get();
        // }
        // elseif ($request->get('month') > null && $request->get('subsection') == null) {
        //     $year = substr($request->get('month'),0,4);
        //     $month = substr($request->get('month'),-2);
        //     $interview = Interview::where('activity_list_id',$id)
        //         // ->where(DATE_FORMAT('date',"%Y-%m"),$month)
        //         ->whereYear('date', '=', $year)
        //         ->whereMonth('date', '=', $month)
        //         ->orderBy('interviews.id','desc')
        //         ->get();
        // }
        // elseif($request->get('subsection') > null && strlen($request->get('month')) == null){
        //     $subsection = $request->get('subsection');
        //     $interview = Interview::where('activity_list_id',$id)
        //         ->where('subsection',$subsection)
        //         ->orderBy('interviews.id','desc')
        //         ->get();
        // }
        // else{
        //     $interview = Interview::where('activity_list_id',$id)
        //         ->orderBy('interviews.id','desc')
        //         ->get();
        // }
        $data = array(
                      'interview' => $interview,
                      'subsection' => $sub_section,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
                      'subsection4' => $subsection4,
                      'departments' => $departments,
                      'status' => $status,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'section' => $section,
                      'frequency' => $frequency,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'id' => $id,
                      'id_departments' => $id_departments);
        return view('interview.index', $data
            )->with('page', 'Interview');
    }

    function show($id,$interview_id)
    {
        $activityList = ActivityList::find($id);
        $interview = Interview::find($interview_id);
        
            $activity_name = $activityList->activity_name;
            $departments = $activityList->departments->department_name;
            $activity_alias = $activityList->activity_alias;

        $data = array('interview' => $interview,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('interview.view', $data
            )->with('page', 'Interview');
    }

    public function destroy($id,$interview_id,$status)
    {
      $interview = Interview::find($interview_id);
      $interview->delete();

      if ($status == 'leader') {
        return redirect('/index/interview/index/'.$id)
        ->with('status', 'Interview has been deleted.')
        ->with('page', 'Interview');
      }else{
        return redirect('/index/interview/pointing_call')
        ->with('status', 'Interview has been deleted.')
        ->with('page', 'Interview');
      }
    }

    function create($id)
    {
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

        $queryFY = "select DISTINCT(fiscal_year) from weekly_calendars where week_date = DATE(NOW())";
        $fy = DB::select($queryFY);

        $data = array(
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'section' => $section,
                      'subsection' => $subsection,
                      'fy' => $fy,
                      'activity_name' => $activity_name,
                      'id' => $id);
        return view('interview.create', $data
            )->with('page', 'Interview');
    }

    function store(Request $request,$id)
    {
            $id_user = Auth::id();
            Interview::create([
                'activity_list_id' => $id,
                'department' => $request->get('department'),
                'section' => $request->get('section'),
                'subsection' => $request->get('subsection'),
                'date' => $request->get('date'),
                'periode' => $request->get('periode'),
                'leader' => $request->get('leader'),
                'foreman' => $request->get('foreman'),
                'created_by' => $id_user
            ]);
        

        if ($request->get('status') == 'leader') {
          return redirect('index/interview/index/'.$id)
            ->with('page', 'Interview')->with('status', 'New Interview has been created.');
        }else{
          $response = array(
            'status' => true,
          );
          return Response::json($response);
        }
    }

    function edit($id,$interview_id)
    {
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

        $queryFY = "select DISTINCT(fiscal_year)from weekly_calendars";
        $fy = DB::select($queryFY);

        $interview = Interview::find($interview_id);

        $data = array(
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'section' => $section,
                      'fy' => $fy,
                      'status' => 'leader',
                      'subsection' => $subsection,
                      'activity_name' => $activity_name,
                      'interview' => $interview,
                      'id' => $id);
        return view('interview.edit', $data
            )->with('page', 'Interview');
    }

    function editPointingCall($id,$interview_id)
    {
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

        $queryFY = "select DISTINCT(fiscal_year) from weekly_calendars where week_date = DATE(NOW())";
        $fy = DB::select($queryFY);

        foreach ($fy as $key) {
          $fiscal = $key->fiscal_year;
        }

        $interview = Interview::find($interview_id);

        $data = array(
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'departments' => $departments,
                      'section' => $section,
                      'fy' => $fiscal,
                      'status' => 'chief',
                      'subsection' => $subsection,
                      'activity_name' => $activity_name,
                      'interview' => $interview,
                      'id' => $id);
        return view('interview.edit_pointing_call', $data
            )->with('page', 'Interview');
    }

    function update(Request $request,$id,$interview_id,$status)
    {
        try{
                $month = date("m",strtotime($request->get('date')));
                $interview = Interview::find($interview_id);
                $interview->activity_list_id = $id;
                $interview->department = $request->get('department');
                $interview->section = $request->get('section');
                $interview->subsection = $request->get('subsection');
                $interview->date = $request->get('date');
                $interview->periode = $request->get('periode');
                $interview->leader = $request->get('leader');
                $interview->foreman = $request->get('foreman');
                $interview->save();

            if ($status == 'leader') {
              return redirect('/index/interview/index/'.$id)->with('status', 'Interview data has been updated.')->with('page', 'Interview');
            }else{
              return redirect('index/interview/pointing_call')->with('status', 'Interview data has been updated.')->with('page', 'Interview');
            }
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Interview already exist.')->with('page', 'Interview');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Interview');
            }
          }
    }

    function details($interview_id)
    {
        $interview_detail = InterviewDetail::where('interview_id',$interview_id)
            ->get();
        $interview_detail2 = InterviewDetail::where('interview_id',$interview_id)
            ->get();
        $interview_picture = InterviewPicture::where('interview_id',$interview_id)
            ->get();

        $interview = Interview::find($interview_id);

        $activity_name = $interview->activity_lists->activity_name;
        $departments = $interview->activity_lists->departments->department_name;
        $id_departments = $interview->activity_lists->departments->id;
        $activity_alias = $interview->activity_lists->activity_alias;
        $activity_id = $interview->activity_lists->id;
        $leader = $interview->activity_lists->leader_dept;

        $pointing_call = PointingCallItem::where('point_title','!=','pic')->where('point_title','!=','janji_safety')->get();
        

        $queryOperator = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs  where employee_syncs.department like '%".$departments."%' and employee_syncs.end_date is null";
        $operator = DB::select($queryOperator);
        $operator2 = DB::select($queryOperator);

        $status = 'leader';

        $data = array('interview_detail' => $interview_detail,
        			        'interview_detail2' => $interview_detail2,
                      'interview_picture' => $interview_picture,
                      'interview' => $interview,
                      'departments' => $departments,
                      'operator' => $operator,
                      'operator2' => $operator2,
                      'activity_name' => $activity_name,
                      'pointing_call' => $pointing_call,
                      'leader' => $leader,
                      'activity_alias' => $activity_alias,
                      'interview_id' => $interview_id,
                      'activity_id' => $activity_id,
                      'status' => $status,
                      'id_departments' => $id_departments);
        return view('interview.details', $data
            )->with('page', 'Interview Details')->with('title', 'Peserta Interview Pointing Call')->with('title_jp', '');
    }

    function detailsPointingCall($interview_id)
    {
        $interview_detail = InterviewDetail::where('interview_id',$interview_id)
            ->get();
        $interview_detail2 = InterviewDetail::where('interview_id',$interview_id)
            ->get();
        $interview_picture = InterviewPicture::where('interview_id',$interview_id)
            ->get();

        $interview = Interview::find($interview_id);

        $activity_name = $interview->activity_lists->activity_name;
        $departments = $interview->activity_lists->departments->department_name;
        $id_departments = $interview->activity_lists->departments->id;
        $activity_alias = $interview->activity_lists->activity_alias;
        $activity_id = $interview->activity_lists->id;
        $leader = $interview->activity_lists->leader_dept;
        $section = $interview->section;

        $pointing_call = PointingCallItem::where('point_title','!=','pic')->where('point_title','!=','janji_safety')->get();
        

        $queryOperator = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs  where employee_syncs.department like '%".$departments."%' and employee_syncs.section like '%".$section."%' and employee_syncs.end_date is null";
        $operator = DB::select($queryOperator);
        $operator2 = DB::select($queryOperator);

        $status = 'chief';

        $data = array('interview_detail' => $interview_detail,
                      'interview_detail2' => $interview_detail2,
                      'interview_picture' => $interview_picture,
                      'interview' => $interview,
                      'departments' => $departments,
                      'operator' => $operator,
                      'operator2' => $operator2,
                      'activity_name' => $activity_name,
                      'pointing_call' => $pointing_call,
                      'leader' => $leader,
                      'activity_alias' => $activity_alias,
                      'interview_id' => $interview_id,
                      'activity_id' => $activity_id,
                      'status' => $status,
                      'id_departments' => $id_departments);
        return view('interview.details_pointing_call', $data
            )->with('page', 'Interview Details')->with('title', 'Peserta Interview Pointing Call')->with('title_jp', '');
    }

    function create_participant(Request $request)
    {
            try{    
              $id_user = Auth::id();
              $interview_id = $request->get('interview_id');
              // if($request->input('pesertascan') == Null){

              $index_filosofi_yamaha = $request->get('index_filosofi_yamaha');
              $index_aturan_k3 = $request->get('index_aturan_k3');
              $index_komitmen_berkendara = $request->get('index_komitmen_berkendara');
              $index_kebijakan_mutu = $request->get('index_kebijakan_mutu');
              $index_budaya_kerja = $request->get('index_budaya_kerja');
              $index_budaya_5s = $request->get('index_budaya_5s');

              $checked_filosofi_yamaha = $request->get('checked_filosofi_yamaha');
              $checked_aturan_k3 = $request->get('checked_aturan_k3');
              $checked_komitmen_berkendara = $request->get('checked_komitmen_berkendara');
              $checked_kebijakan_mutu = $request->get('checked_kebijakan_mutu');
              $checked_budaya_kerja = $request->get('checked_budaya_kerja');
              $checked_budaya_5s = $request->get('checked_budaya_5s');

              $nilai_filosofi_yamaha = 0;
              if ($checked_filosofi_yamaha == 0) {
                $nilai_filosofi_yamaha = 0;
              }else if($checked_filosofi_yamaha < $index_filosofi_yamaha){
                $nilai_filosofi_yamaha = round(($checked_filosofi_yamaha / $index_filosofi_yamaha)*100,0);
              }else{
                $nilai_filosofi_yamaha = 100;
              }

              $nilai_aturan_k3 = 0;
              if ($checked_aturan_k3 == 0) {
                $nilai_aturan_k3 = 0;
              }else if($checked_aturan_k3 < $index_aturan_k3){
                $nilai_aturan_k3 = round(($checked_aturan_k3 / $index_aturan_k3)*100,0);
              }else{
                $nilai_aturan_k3 = 100;
              }

              $nilai_komitmen_berkendara = 0;
              if ($checked_komitmen_berkendara == 0) {
                $nilai_komitmen_berkendara = 0;
              }else if($checked_komitmen_berkendara < $index_komitmen_berkendara){
                $nilai_komitmen_berkendara = round(($checked_komitmen_berkendara / $index_komitmen_berkendara)*100,0);
              }else{
                $nilai_komitmen_berkendara = 100;
              }

              $nilai_kebijakan_mutu = 0;
              if ($request->get('kebijakan_mutu') == 2) {
                $nilai_kebijakan_mutu = 0;
              }else if($request->get('kebijakan_mutu') == 1){
                $nilai_kebijakan_mutu = 100;
              }else{
                $nilai_kebijakan_mutu = 0;
              }

              $nilai_budaya_kerja = 0;
              if ($checked_budaya_kerja == 0) {
                $nilai_budaya_kerja = 0;
              }else if($checked_budaya_kerja < $index_budaya_kerja){
                $nilai_budaya_kerja = round(($checked_budaya_kerja / $index_budaya_kerja)*100,0);
              }else{
                $nilai_budaya_kerja = 100;
              }

              $nilai_budaya_5s = 0;
              if ($checked_budaya_5s == 0) {
                $nilai_budaya_5s = 0;
              }else if($checked_budaya_5s < $index_budaya_5s){
                $nilai_budaya_5s = round(($checked_budaya_5s / $index_budaya_5s)*100,0);
              }else{
                $nilai_budaya_5s = 100;
              }

              InterviewDetail::create([
                  'interview_id' => $interview_id,
                  'nik' => $request->get('nik'),
                  'filosofi_yamaha' => $request->get('filosofi_yamaha').'_'.$nilai_filosofi_yamaha,
                  'aturan_k3' => $request->get('aturan_k3').'_'.$nilai_aturan_k3,
                  'komitmen_berkendara' => $request->get('komitmen_berkendara').'_'.$nilai_komitmen_berkendara,
                  'kebijakan_mutu' => $request->get('kebijakan_mutu').'_'.$nilai_kebijakan_mutu,
                  'budaya_kerja' => $request->get('budaya_kerja').'_'.$nilai_budaya_kerja,
                  'budaya_5s' => $request->get('budaya_5s').'_'.$nilai_budaya_5s,
                  'created_by' => $id_user
              ]);

              $response = array(
                'status' => true,
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

    public function getdetail(Request $request)
    {
         try{
            $detail = InterviewDetail::find($request->get("id"));
            $data = array('detail_id' => $detail->id,
                          'interview_id' => $detail->interview_id,
                          'nik' => $detail->nik,
                          'name' => $detail->participants->name,
                          'filosofi_yamaha' => $detail->filosofi_yamaha,
                          'aturan_k3' => $detail->aturan_k3,
                          'komitmen_berkendara' => $detail->komitmen_berkendara,
                          'kebijakan_mutu' => $detail->kebijakan_mutu,
                          'enam_pasal_keselamatan' => $detail->enam_pasal_keselamatan,
                          'budaya_kerja' => $detail->budaya_kerja,
                          'budaya_5s' => $detail->budaya_5s,
                          'komitmen_hotel_konsep' => $detail->komitmen_hotel_konsep,
                          'janji_tindakan_dasar' => $detail->janji_tindakan_dasar);
            // $name = $beacon->name;
            // $beacon->uuid = $request->get('uuid');
            // $beacon->name = $request->get('name');
           

            $response = array(
              'status' => true,
              'data' => $data
            );
            return Response::json($response);

          }
          catch (QueryException $beacon){
            $error_code = $beacon->errorInfo[1];
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

    function edit_participant(Request $request,$interview_id,$detail_id)
    {
        try{
                $interview = InterviewDetail::find($detail_id);
                $interview->nik = $request->get('nik');
                $interview->filosofi_yamaha = $request->get('filosofi_yamaha');
                $interview->aturan_k3 = $request->get('aturan_k3');
                $interview->komitmen_berkendara = $request->get('komitmen_berkendara');
                $interview->kebijakan_mutu = $request->get('kebijakan_mutu');
                $interview->enam_pasal_keselamatan = $request->get('enam_pasal_keselamatan');
                $interview->budaya_kerja = $request->get('budaya_kerja');
                $interview->budaya_5s = $request->get('budaya_5s');
                $interview->komitmen_hotel_konsep = $request->get('komitmen_hotel_konsep');
                $interview->janji_tindakan_dasar = $request->get('janji_tindakan_dasar');
                $interview->save();

            return redirect('index/interview/details/'.$interview_id)
              ->with('page', 'Interview Details')->with('status', 'Participant has been updated.');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Participant already exist.')->with('page', 'Interview');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Interview');
            }
          }
    }

    public function destroy_participant($interview_id,$detail_id,$status)
    {
      $interview = InterviewDetail::find($detail_id);
      $interview->delete();

      if ($status == 'leader') {
        return redirect('index/interview/details/'.$interview_id)
              ->with('page', 'Interview Details')->with('status', 'Participant has been deleted.');
      }else{
        return redirect('index/interview/pointing_call/details/'.$interview_id)
              ->with('page', 'Interview Details')->with('status', 'Participant has been deleted.');
      }
    }

    function print_interview($interview_id)
    {
        $interview = Interview::find($interview_id);
        $activity_list_id = $interview->activity_list_id;

        $activityList = ActivityList::find($activity_list_id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $id_departments = $activityList->departments->id;

        $interviewDetailQuery = "select * from interview_details join employee_syncs on interview_details.nik = employee_syncs.employee_id where interview_id = '".$interview_id."' and interview_details.deleted_at is null";
        $interviewDetail = DB::select($interviewDetailQuery);

        $interviewPicture = InterviewPicture::where('interview_id',$interview_id)->get();

        $poinalldiamond = DB::SELECT("SELECT point_no FROM `pointing_call_items` where point_title = 'diamond'");
        $poinallk3 = DB::SELECT("SELECT point_no FROM `pointing_call_items` where point_title = 'k3'");
        $poinall10_komitmen = DB::SELECT("SELECT point_no FROM `pointing_call_items` where point_title = '8_pasal'");
        $poinallslogan_mutu = DB::SELECT("SELECT point_no FROM `pointing_call_items` where point_title = 'slogan_mutu'");
        $poinallbudaya = DB::SELECT("SELECT point_no FROM `pointing_call_items` where point_title = 'prinsip_cool_factory'");
        $poinall5s = DB::SELECT("SELECT point_no FROM `pointing_call_items` where point_title = '5s'");

        if($interview == null){
            return redirect('/index/interview/index/'.$activity_list_id)->with('error', 'Data Tidak Tersedia.')->with('page', 'Interview');
        }else{
            // $data = array(
            // 'interview' => $interview,
            //         'interviewDetail' => $interviewDetail,
            //         'activityList' => $activityList,
            //         'departments' => $departments,
            //         'leader' => $leader,
            //         'activity_name' => $activity_name,
            //         'activity_alias' => $activity_alias,
            //         'interview_id' => $interview_id,
            //         'interviewPicture' => $interviewPicture,
            //         'id_departments' => $id_departments,
            //         'poinalldiamond' => $poinalldiamond,
            //         'poinallk3' => $poinallk3,
            //         'poinall10_komitmen' => $poinall10_komitmen,
            //         'poinallslogan_mutu' => $poinallslogan_mutu,
            //         'poinall6_pasal' => $poinall6_pasal,
            //         'poinallbudaya' => $poinallbudaya,
            //         'poinall5s' => $poinall5s,
            //         'poinallkomitmen' => $poinallkomitmen,
            //         'poinalljanji' => $poinalljanji,
            //   );
            // return view('interview.print', $data
            //     )->with('page', 'Interview');
             $pdf = \App::make('dompdf.wrapper');
             $pdf->getDomPDF()->set_option("enable_php", true);
             $pdf->setPaper('A4', 'landscape');

             $pdf->loadView('interview.print', array(
                   'interview' => $interview,
                    'interviewDetail' => $interviewDetail,
                    'activityList' => $activityList,
                    'departments' => $departments,
                    'leader' => $leader,
                    'activity_name' => $activity_name,
                    'activity_alias' => $activity_alias,
                    'interview_id' => $interview_id,
                    'interviewPicture' => $interviewPicture,
                    'id_departments' => $id_departments,
                    'poinalldiamond' => $poinalldiamond,
                    'poinallk3' => $poinallk3,
                    'poinall10_komitmen' => $poinall10_komitmen,
                    'poinallslogan_mutu' => $poinallslogan_mutu,
                    'poinallbudaya' => $poinallbudaya,
                    'poinall5s' => $poinall5s,
             ));

             return $pdf->stream($activity_name." - ".$leader.".pdf");
        }
    }

    function print_email($interview_id)
    {
        $interview = Interview::find($interview_id);
        $activity_list_id = $interview->activity_list_id;

        $activityList = ActivityList::find($activity_list_id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $id_departments = $activityList->departments->id;

        $interviewDetailQuery = "select * from interview_details join employee_syncs on interview_details.nik = employee_syncs.employee_id where interview_id = '".$interview_id."' and interview_details.deleted_at is null";
        $interviewDetail = DB::select($interviewDetailQuery);

        $interviewPicture = InterviewPicture::where('interview_id',$interview_id)->get();

        $poinalldiamond = DB::SELECT("SELECT point_no FROM `pointing_call_items` where point_title = 'diamond'");
        $poinallk3 = DB::SELECT("SELECT point_no FROM `pointing_call_items` where point_title = 'k3'");
        $poinall10_komitmen = DB::SELECT("SELECT point_no FROM `pointing_call_items` where point_title = '8_pasal'");
        $poinallslogan_mutu = DB::SELECT("SELECT point_no FROM `pointing_call_items` where point_title = 'slogan_mutu'");
        $poinallbudaya = DB::SELECT("SELECT point_no FROM `pointing_call_items` where point_title = 'prinsip_cool_factory'");
        $poinall5s = DB::SELECT("SELECT point_no FROM `pointing_call_items` where point_title = '5s'");
        
        if($interview == null){
            return redirect('/index/interview/index/'.$activity_list_id)->with('error', 'Data Tidak Tersedia.')->with('page', 'Interview');
        }else{
            $data = array(
                          'interview' => $interview,
                          'interviewDetail' => $interviewDetail,
                          'activityList' => $activityList,
                          'departments' => $departments,
                          'role_code' => Auth::user()->role_code,
                          'activity_name' => $activity_name,
                          'leader' => $leader,
                          'activity_alias' => $activity_alias,
                          'interview_id' => $interview_id,
                          'interviewPicture' => $interviewPicture,
                          'id_departments' => $id_departments,
                          'poinalldiamond' => $poinalldiamond,
                          'poinallk3' => $poinallk3,
                          'poinall10_komitmen' => $poinall10_komitmen,
                          'poinallslogan_mutu' => $poinallslogan_mutu,
                          'poinallbudaya' => $poinallbudaya,
                          'poinall5s' => $poinall5s,);
            return view('interview.print_email', $data
                )->with('page', 'Interview');
        }
    }

    function print_approval($activity_list_id,$bulan)
    {
        $role_code = Auth::user()->role_code;
        $year = substr($bulan,0,4);
        $month = substr($bulan,-2);
        if($role_code == 'PROD-SPL'){
            $interview = Interview::where('activity_list_id',$activity_list_id)
              ->whereYear('date', '=', $year)
              ->whereMonth('date', '=', $month)
              ->where('send_status','Sent')
              ->where('approval',null)
              ->get();
        }
        else{
            $interview = Interview::where('activity_list_id',$activity_list_id)
              ->whereYear('date', '=', $year)
              ->whereMonth('date', '=', $month)
              ->get();
        }
        //interview
        foreach($interview as $interview){
            $interview_id = $interview->id;
        }
        $role_code = Auth::user()->role_code;

        $activityList = ActivityList::find($activity_list_id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        $interviewDetailQuery = "select * from interview_details join employee_syncs on interview_details.nik = employee_syncs.employee_id where interview_id = '".$interview_id."' and interview_details.deleted_at is null";
        $interviewDetail = DB::select($interviewDetailQuery);
        $interviewPicture = InterviewPicture::where('interview_id',$interview_id)->get();

        if($interview == null){
            return redirect('/index/interview/index/'.$activity_list_id)->with('error', 'Data Tidak Tersedia.')->with('page', 'Interview');
        }else{
            $data = array(
                          'interview' => $interview,
                          'interviewDetail' => $interviewDetail,
                          'activityList' => $activityList,
                          'departments' => $departments,
                          'role_code' => $role_code,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'interview_id' => $interview_id,
                          'interviewPicture' => $interviewPicture,
                          'id_departments' => $id_departments);
            return view('interview.print_email', $data
                )->with('page', 'Interview');
        }
    }

    public function sendemail($interview_id,$status)
      {
          if ($status == 'leader') {
            $query_interview = "select *,interviews.id as interview_id,'leader' as status from interviews join activity_lists on activity_lists.id = interviews.activity_list_id join departments on activity_lists.department_id = departments.id where interviews.id = '".$interview_id."' and interviews.deleted_at is null";
          }else{
            $query_interview = "select *,interviews.id as interview_id,'chief' as status from interviews join activity_lists on activity_lists.id = interviews.activity_list_id join departments on activity_lists.department_id = departments.id where interviews.id = '".$interview_id."' and interviews.deleted_at is null";
          }
          
          $interview = DB::select($query_interview);
          $interview3 = DB::select($query_interview);
          // $training2 = DB::select($query_training);

          if($interview != null){
            foreach($interview as $interview){
              $foreman = $interview->foreman;
              $send_status = $interview->send_status;
              $activity_list_id = $interview->activity_list_id;
              $interview2 = Interview::find($interview_id);
              $interview2->send_status = "Sent";
              $interview2->send_date = date('Y-m-d');
              $interview2->approval_leader = "Approved";
              $interview2->approved_date_leader = date('Y-m-d');
              $interview2->save();
              // var_dump($id);
            }
            $queryEmail = "select employee_syncs.employee_id,employee_syncs.name,email from users join employee_syncs on employee_syncs.employee_id = users.username where employee_syncs.name = '".$foreman."'";
            $email = DB::select($queryEmail);
            foreach($email as $email){
              $mail_to = $email->email;
              // var_dump($mail_to);
            }
          }
          else{
            if ($status == 'leader') {
              return redirect('/index/interview/index/'.$activity_list_id)->with('error', 'Data tidak tersedia.')->with('page', 'Interview');
            }else{
              return redirect('index/interview/pointing_call')->with('error', 'Data tidak tersedia.')->with('page', 'Interview');
            }
          }

          if($send_status == "Sent"){
            if ($status == 'leader') {
              return redirect('/index/interview/index/'.$activity_list_id)->with('error', 'Data Pernah Dikirim.')->with('page', 'Interview');
            }else{
              return redirect('index/interview/pointing_call')->with('error', 'Data Pernah Dikirim.')->with('page', 'Interview');
            }
          }
          
          elseif($interview != null){
              Mail::to($mail_to)->bcc('mokhamad.khamdan.khabibi@music.yamaha.com')->send(new SendEmail($interview3, 'interview'));
              if ($status == 'leader') {
                return redirect('/index/interview/index/'.$activity_list_id)->with('status', 'Email Terkirim')->with('page', 'Interview');
              }else{
                return redirect('index/interview/pointing_call')->with('status', 'Email Terkirim')->with('page', 'Interview');
              }
          }
          else{
            if ($status == 'leader') {
              return redirect('/index/interview/index/'.$activity_list_id)->with('error', 'Data tidak tersedia.')->with('page', 'Interview');
            }else{
              return redirect('index/interview/pointing_call')->with('error', 'Data tidak tersedia.')->with('page', 'Interview');
            }
          }
      }

    public function approval(Request $request,$interview_id)
    {
        $approve = $request->get('approve');
        $interviewDetailQuery = "select * from interview_details join employee_syncs on interview_details.nik = employee_syncs.employee_id where interview_id = '".$interview_id."' and interview_details.deleted_at is null";
        $interviewDetail = DB::select($interviewDetailQuery);
        $jumlahDetail = count($interviewDetail);
        $approvecount = count($approve);
        if($approve < $jumlahDetail){
          return redirect('/index/interview/print_email/'.$interview_id)->with('error', 'Data Belum Terverifikasi. Checklist semua poin jika akan verifikasi data.')->with('page', 'Interview');
        }
        else{
            $interview = Interview::find($interview_id);
            $interview->approval = "Approved";
            $interview->approved_date = date('Y-m-d');
            $interview->save();
            return redirect('/index/interview/print_email/'.$interview_id)->with('status', 'Approved.')->with('page', 'Interview');
        }
    }

    function insertpicture(Request $request, $id,$status)
    {
            $id_user = Auth::id();
            $tujuan_upload = 'data_file/interview';
            $date = date('Y-m-d');

            $file = $request->file('file');
            $nama_file = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $file->getClientOriginalName();
            $filename = md5(date("dmYhisA")).'.'.$file->getClientOriginalExtension();
            $file->move($tujuan_upload,$filename);

            InterviewPicture::create([
                'interview_id' => $id,
                'picture' => $filename,
                'extension' => $extension,
                'created_by' => $id_user
            ]);
        

        if ($status == 'leader') {
          return redirect('index/interview/details/'.$id)
            ->with('page', 'Interview Report')->with('status', 'New Pictrue has been created.');
        }else{
          return redirect('index/interview/pointing_call/details/'.$id)
            ->with('page', 'Interview Report')->with('status', 'New Pictrue has been created.');
        }
    }

    public function destroypicture($id,$picture_id,$status)
    {
      $interview = InterviewPicture::find($picture_id);
      $interview->delete();

      if ($status == 'leader') {
        return redirect('/index/interview/details/'.$id)
        ->with('status', 'Interview Picture has been deleted.')
        ->with('page', 'Interview');
      }else{
        return redirect('/index/interview/pointing_call/details/'.$id)
        ->with('status', 'Interview Picture has been deleted.')
        ->with('page', 'Interview');
        //
      }
    }

    function editpicture(Request $request, $id,$picture_id,$status)
    {
        try{
            $tujuan_upload = 'data_file/interview';
            $date = date('Y-m-d');

            $file = $request->file('file');
            $nama_file = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $file->getClientOriginalName();
            $filename = md5(date("dmYhisA")).'.'.$file->getClientOriginalExtension();
            $file->move($tujuan_upload,$filename);

            $interview_picture = InterviewPicture::find($picture_id);
            $interview_picture->picture = $filename;
            $interview_picture->extension = $extension;
            $interview_picture->save();

            if ($status == 'leader') {
              return redirect('/index/interview/details/'.$id)->with('status', 'Interview Picture data has been updated.')->with('page', 'Interview');
            }else{
              return redirect('/index/interview/pointing_call/details/'.$id)->with('status', 'Interview Picture data has been updated.')->with('page', 'Interview');
            }
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Interview Picture already exist.')->with('page', 'Interview');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Interview');
            }
          }
    }

    public function detailNilai(Request $request)
    {
      try {
        // var_dump($request->get('point'));
        $points = explode(',', $request->get('point'));
        $addpoint = "";
        $point = "";
        for($x = 0; $x < count($points); $x++) {
          $point = $point."'".$points[$x]."'";
          if($x != count($points)-1){
            $point = $point.",";
          }
        }
        $addpoint = "and point_no in (".$point.") ";
        if ($request->get('type') == 'filosofi_yamaha') {
          $type = 'diamond';
          $judul = 'Filosofi Yamaha';
        }else if($request->get('type') == 'aturan_k3') {
          $type = 'k3';
          $judul = 'Aturan K3 YAMAHA';
        }else if($request->get('type') == 'komitmen_berkendara') {
          $type = '8_pasal';
          $judul = '8 Pasal Keselamatan Lalu Lintas YMPI';
        }else if($request->get('type') == 'kebijakan_mutu') {
          $type = 'slogan_mutu';
          $judul = 'Slogan Kualitas';
        }else if($request->get('type') == 'budaya_kerja') {
          $type = 'prinsip_cool_factory';
          $judul = 'Prinsip Cool Factory';
        }else if($request->get('type') == 'budaya_5s') {
          $type = '5s';
          $judul = '5S';
        }

        $pointsall = [];
        $pointbypoint = [];

        for($y = 0; $y < count($points); $y++) {
          $pointtitlecheck = DB::SELECT("SELECT
            * 
          FROM
            `pointing_call_items` 
          WHERE
            point_title = '".$type."' 
            AND point_no = '".$points[$y]."'
            limit 1");
          foreach ($pointtitlecheck as $key) {
            array_push($pointbypoint, array('point_no' => $key->point_no,
            'point_description' => $key->point_description, ));
          }
        }
          $pointtitle = DB::SELECT("SELECT * FROM `pointing_call_items` where point_title = '".$type."'");

        $response = array(
            'status' => true,
            'pointtitle' => $pointtitle,
            'pointbypoint' => $pointbypoint,
            'judul' => $judul,
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
}
