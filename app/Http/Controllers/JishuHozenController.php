<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\User;
use Illuminate\Support\Facades\DB;
use App\JishuHozenPoint;
use App\JishuHozen;
use App\MaintenanceJishuHozenPoint;
use App\MaintenanceJishuHozen;
use App\EmployeeSync;
use App\AreaCode;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class JishuHozenController extends Controller
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

    function nama_pengecekan($id)
    {
    	$activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;
        $frequency = $activityList->frequency;

        $query_jishu_hozen_point = "select DISTINCT(nama_pengecekan),id from jishu_hozen_points where activity_list_id='".$id."' and leader = '".$leader."'";
        $jishu_hozen_point = DB::select($query_jishu_hozen_point);

        $jishu_hozen = JishuHozen::select('jishu_hozen_points.nama_pengecekan')->distinct()->where('month',date('Y-m'))->join('jishu_hozen_points','jishu_hozens.jishu_hozen_point_id','jishu_hozen_points.id')->where('jishu_hozens.activity_list_id',$id)->get();

        $jishu_hozens = [];
        for ($i=0; $i < count($jishu_hozen); $i++) { 
          array_push($jishu_hozens, $jishu_hozen[$i]->nama_pengecekan);
        }

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

        $querypic = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs  where employee_syncs.department like '%".$departments."%'";
        $pic = DB::select($querypic);
        $pic2 = DB::select($querypic);

        $data = array('jishu_hozen_point' => $jishu_hozen_point,
                      'jishu_hozen_point2' => $jishu_hozen_point,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'jishu_hozen' => $jishu_hozens,
                      'activity_alias' => $activity_alias,
                      'subsection' => $subsection,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'pic' => $pic,
                      'pic2' => $pic2,
                      'id' => $id,
                      'frequency' => $frequency,
                      'leader' => $leader,
                      'id_departments' => $id_departments);
        return view('jishu_hozen.point', $data
            )->with('page', 'Jishu Hozen');
    }

    function index($id,$jishu_hozen_point_id)
    {
        $activityList = ActivityList::find($id);
        // $jishu_hozen = JishuHozen::where('activity_list_id',$id)
        //       ->where('jishu_hozen_point_id',$jishu_hozen_point_id)
        //       ->orderBy('jishu_hozens.id','desc')->get();

        $jishu_hozen_point = JishuHozenPoint::find($jishu_hozen_point_id);

        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

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

        $querypic = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs  where employee_syncs.department like '%".$departments."%'";
        $pic = DB::select($querypic);
        $pic2 = DB::select($querypic);

      $data = array(
        // 'jishu_hozen' => $jishu_hozen,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'subsection' => $subsection,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
                      'jishu_hozen_point' => $jishu_hozen_point,
                      'jishu_hozen_point_id' => $jishu_hozen_point_id,
                      'pic' => $pic,
                      'pic2' => $pic2,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'id' => $id,
                      'id_departments' => $id_departments);
      return view('jishu_hozen.index', $data
        )->with('page', 'Jishu Hozen');
    }

    function filter_jishu_hozen(Request $request,$id,$jishu_hozen_point_id)
    {
        $activityList = ActivityList::find($id);
        $jishu_hozen_point = JishuHozenPoint::find($jishu_hozen_point_id);

        if(strlen($request->get('month')) != null){
            $year = substr($request->get('month'),0,4);
            $month = substr($request->get('month'),-2);
            $jishu_hozen = JishuHozen::where('activity_list_id',$id)
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->where('jishu_hozen_point_id',$jishu_hozen_point_id)
                ->orderBy('jishu_hozens.id','desc')
                ->get();
        }
        else{
            $jishu_hozen = JishuHozen::where('activity_list_id',$id)
            ->where('jishu_hozen_point_id',$jishu_hozen_point_id)
            ->orderBy('jishu_hozens.id','desc')->get();
        }

        // foreach ($activityList as $activityList) {
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;

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

        $querypic = "select DISTINCT(employee_syncs.name),employee_syncs.employee_id from employee_syncs  where employee_syncs.department like '%".$departments."%'";
        $pic = DB::select($querypic);
        $pic2 = DB::select($querypic);

        $data = array(
                      'jishu_hozen' => $jishu_hozen,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'leader' => $leader,
                      'subsection' => $subsection,
                      'subsection2' => $subsection2,
                      'subsection3' => $subsection3,
                      'jishu_hozen_point' => $jishu_hozen_point,
                      'jishu_hozen_point_id' => $jishu_hozen_point_id,
                      'pic' => $pic,
                      'pic2' => $pic2,
                      'foreman' => $foreman,
                      'id' => $id,
                      'id_departments' => $id_departments);
        return view('jishu_hozen.index', $data
            )->with('page', 'Jishu Hozen');
    }

    public function destroy($id,$jishu_hozen_point_id,$jishu_hozen_id)
    {
      $jishu_hozen = JishuHozen::find($jishu_hozen_id);
      $jishu_hozen->delete();

      return redirect('/index/jishu_hozen/nama_pengecekan/'.$id)
        ->with('status', 'Daily Check Mesin has been deleted.')
        ->with('page', 'Jishu Hozen');        
    }

    function store(Request $request,$id,$jishu_hozen_point_id)
    {
            try{

              $id_user = Auth::id();
                JishuHozen::create([
                    'activity_list_id' => $id,
                    'jishu_hozen_point_id' => $jishu_hozen_point_id,
                    'department' => $request->get('department'),
                    'subsection' => $request->get('subsection'),
                    'date' => $request->get('date'),
                    'month' => $request->get('month'),
                    'foto_aktual' => $request->get('foto_aktual'),
                    'pic' => $request->get('pic'),
                    'leader' => $request->get('leader'),
                    'foreman' => $request->get('foreman'),
                    'created_by' => $id_user
                ]);

              $response = array(
                'status' => true,
              );
              // return redirect('index/interview/details/'.$interview_id)
              // ->with('page', 'Interview Details')->with('status', 'New Participant has been created.');
              return Response::json($response);
              // return redirect('/index/jishu_hozen/index/'.$id.'/'.$jishu_hozen_point_id)->with('status', 'Jishu Hozen data has been created.')->with('page', 'Jishu Hozen');
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    public function getjishuhozen(Request $request)
    {
         try{
            $detail = JishuHozen::select('jishu_hozens.*','jishu_hozen_points.nama_pengecekan')->join('jishu_hozen_points','jishu_hozen_points.id','jishu_hozens.jishu_hozen_point_id')->where('jishu_hozens.id',$request->get("id"))->first();
            $data = array('jishu_hozen_id' => $detail->id,
                          'jishu_hozen_point_id' => $detail->jishu_hozen_point_id,
                          'department' => $detail->department,
                          'subsection' => $detail->subsection,
                          'date' => $detail->date,
                          'month' => $detail->month,
                          'nama_pengecekan' => $detail->nama_pengecekan,
                          'foto_aktual' => $detail->foto_aktual,
                          'pic' => $detail->pic,
                          'leader' => $detail->leader,
                          'foreman' => $detail->foreman);

            $response = array(
              'status' => true,
              'data' => $data
            );
            return Response::json($response);

          }
          catch (QueryException $jishu_hozen){
            $error_code = $jishu_hozen->errorInfo[1];
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

    function update(Request $request,$id,$jishu_hozen_point_id,$jishu_hozen_id)
    {
        try{
                $jishu_hozen = JishuHozen::find($jishu_hozen_id);
                $jishu_hozen->department = $request->get('department');
                $jishu_hozen->subsection = $request->get('subsection');
                $jishu_hozen->month = $request->get('month');
                $jishu_hozen->foto_aktual = $request->get('foto_aktual');
                $jishu_hozen->pic = $request->get('pic');
                $jishu_hozen->save();

            // return redirect('index/interview/details/'.$interview_id)
            //   ->with('page', 'Interview Details')->with('status', 'Participant has been updated.');
               $response = array(
                'status' => true,
              );
              // return redirect('index/interview/details/'.$interview_id)
              // ->with('page', 'Interview Details')->with('status', 'New Participant has been created.');
              return Response::json($response);
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    function print_jishu_hozen($id,$jishu_hozen_id,$month)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $id_departments = $activityList->departments->id;

        $queryjishu_hozen = "select *, jishu_hozens.id as id_jishu_hozen
            from jishu_hozens
            join activity_lists on activity_lists.id = jishu_hozens.activity_list_id
            join departments on departments.id =  activity_lists.department_id
            left JOIN jishu_hozen_points ON jishu_hozen_points.id = jishu_hozens.jishu_hozen_point_id 
            where activity_lists.id = '".$id."'
            and jishu_hozens.id = '".$jishu_hozen_id."'
            and activity_lists.department_id = '".$id_departments."'
            and jishu_hozens.month = '".$month."'
            and jishu_hozens.deleted_at is null";
        $jishu_hozen = DB::select($queryjishu_hozen);
        $jishu_hozen2 = DB::select($queryjishu_hozen);

        $monthTitle = date("F Y", strtotime($month));

        // var_dump($subsection);
        $jml_null = 0;
        $jml_null_leader = 0;
        foreach($jishu_hozen2 as $jishu_hozen2){
            // $product = $samplingCheck->product;
            // $proses = $samplingCheck->proses;
            $date = $jishu_hozen2->date;
            $foreman = $jishu_hozen2->foreman;
            $approval_leader = $jishu_hozen2->approval_leader;
            $approved_date_leader = $jishu_hozen2->approved_date_leader;
            $subsection = $jishu_hozen2->subsection;
            $leader = $jishu_hozen2->leader;
            if ($jishu_hozen2->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            if ($jishu_hozen2->approval_leader == Null) {
              $jml_null_leader = $jml_null_leader + 1;
            }
            $approved_date = $jishu_hozen2->approved_date;
            $approved_date_leader = $jishu_hozen2->approved_date_leader;
        }
        if($jishu_hozen == null){
            // return redirect('/index/production_audit/index/'.$id.'/'.$request->get('product').'/'.$request->get('proses'))->with('error', 'Data Tidak Tersedia.')->with('page', 'Production Audit');
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{
            // $data = array(
            //               'subsection' => $subsection,
            //               'leader' => $leader,
            //               'foreman' => $foreman,
            //               'monthTitle' => $monthTitle,
            //               'subsection' => $subsection,
            //               'date' => $date,
            //               'jml_null' => $jml_null,
            //               'jml_null_leader' => $jml_null_leader,
            //               'approved_date' => $approved_date,
            //               'approval_leader' => $approval_leader,
            //               'approved_date_leader' => $approved_date_leader,
            //               'jishu_hozen' => $jishu_hozen,
            //               'departments' => $departments,
            //               'activity_name' => $activity_name,
            //               'activity_alias' => $activity_alias,
            //               'id' => $id,
            //               'id_departments' => $id_departments);
            // return view('jishu_hozen.print', $data
            //     )->with('page', 'Jishu Hozen');

             $pdf = \App::make('dompdf.wrapper');
             $pdf->getDomPDF()->set_option("enable_php", true);
             $pdf->setPaper('A4', 'landscape');

             $pdf->loadView('jishu_hozen.print', array(
                  'subsection' => $subsection,
                  'leader' => $leader,
                  'foreman' => $foreman,
                  'monthTitle' => $monthTitle,
                  'subsection' => $subsection,
                  'date' => $date,
                  'jml_null' => $jml_null,
                  'jml_null_leader' => $jml_null_leader,
                  'approved_date' => $approved_date,
                  'approval_leader' => $approval_leader,
                  'approved_date_leader' => $approved_date_leader,
                  'jishu_hozen' => $jishu_hozen,
                  'departments' => $departments,
                  'activity_name' => $activity_name,
                  'activity_alias' => $activity_alias,
                  'id' => $id,
                  'id_departments' => $id_departments
             ));

             return $pdf->stream("Daily Check Mesin ".$leader." (".$monthTitle.").pdf");
        }
    }

    function print_jishu_hozen_email($id,$jishu_hozen_id,$month)
    {
        $activityList = ActivityList::find($id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        // var_dump($month);die();

        $queryjishu_hozen = "select *, jishu_hozens.id as id_jishu_hozen
            from jishu_hozens
            join activity_lists on activity_lists.id = jishu_hozens.activity_list_id
            join departments on departments.id =  activity_lists.department_id
            JOIN jishu_hozen_points ON jishu_hozen_points.id = jishu_hozens.jishu_hozen_point_id 
            where activity_lists.id = '".$id."'
            and jishu_hozens.id = '".$jishu_hozen_id."'
            and activity_lists.department_id = '".$id_departments."'
            and jishu_hozens.month = '".$month."'
            and jishu_hozens.deleted_at is null";
        $jishu_hozen = DB::select($queryjishu_hozen);
        $jishu_hozen2 = DB::select($queryjishu_hozen);

        $monthTitle = date("F Y", strtotime($month));

        // var_dump($subsection);
        $jml_null = 0;
        $jml_null_leader = 0;
        foreach($jishu_hozen2 as $jishu_hozen2){
            // $product = $samplingCheck->product;
            // $proses = $samplingCheck->proses;
            $date = $jishu_hozen2->date;
            $foreman = $jishu_hozen2->foreman;
            $approval_leader = $jishu_hozen2->approval_leader;
            $approved_date_leader = $jishu_hozen2->approved_date_leader;
            $subsection = $jishu_hozen2->subsection;
            $leader = $jishu_hozen2->leader;
            $approval = $jishu_hozen2->approval;
            if ($jishu_hozen2->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            if ($jishu_hozen2->approval_leader == Null) {
              $jml_null_leader = $jml_null_leader + 1;
            }
            $approved_date = $jishu_hozen2->approved_date;
            $approved_date_leader = $jishu_hozen2->approved_date_leader;
        }
        if($jishu_hozen == null){
            // return redirect('/index/production_audit/index/'.$id.'/'.$request->get('product').'/'.$request->get('proses'))->with('error', 'Data Tidak Tersedia.')->with('page', 'Production Audit');
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{
            $data = array(
                          'subsection' => $subsection,
                          'leader' => $leader,
                          'foreman' => $foreman,
                          'monthTitle' => $monthTitle,
                          'subsection' => $subsection,
                          'date' => $date,
                          'approval' => $approval,
                          'jml_null' => $jml_null,
                          'role_code' => Auth::user()->role_code,
                          'jml_null_leader' => $jml_null_leader,
                          'approved_date' => $approved_date,
                          'approval_leader' => $approval_leader,
                          'approved_date_leader' => $approved_date_leader,
                          'jishu_hozen' => $jishu_hozen,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'id' => $id,
                          'jishu_hozen_id' => $jishu_hozen_id,
                          'month' => $month,
                          'id_departments' => $id_departments);
            return view('jishu_hozen.print_email', $data
                )->with('page', 'Jishu Hozen');
        }
    }

    function print_jishu_hozen_approval($activity_list_id,$month)
    {
        $activityList = ActivityList::find($activity_list_id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $activity_alias = $activityList->activity_alias;
        $id_departments = $activityList->departments->id;

        $queryjishu_hozen = "select *, jishu_hozens.id as id_jishu_hozen
            from jishu_hozens
            join activity_lists on activity_lists.id = jishu_hozens.activity_list_id
            join departments on departments.id =  activity_lists.department_id
            JOIN jishu_hozen_points ON jishu_hozen_points.id = jishu_hozens.jishu_hozen_point_id 
            where activity_lists.id = '".$activity_list_id."'
            and activity_lists.department_id = '".$id_departments."'
            and DATE_FORMAT(jishu_hozens.date,'%Y-%m') = '".$month."'
            and jishu_hozens.deleted_at is null limit 1";
        $jishu_hozen = DB::select($queryjishu_hozen);
        $jishu_hozen2 = DB::select($queryjishu_hozen);

        $monthTitle = date("F Y", strtotime($month));
        $id = $activity_list_id;

        // var_dump($subsection);
        $jml_null = 0;
        $jml_null_leader = 0;
        foreach($jishu_hozen2 as $jishu_hozen2){
            // $product = $samplingCheck->product;
            // $proses = $samplingCheck->proses;
            $date = $jishu_hozen2->date;
            $foreman = $jishu_hozen2->foreman;
            $jishu_hozen_id = $jishu_hozen2->id_jishu_hozen;
            $approval_leader = $jishu_hozen2->approval_leader;
            $approved_date_leader = $jishu_hozen2->approved_date_leader;
            $subsection = $jishu_hozen2->subsection;
            $leader = $jishu_hozen2->leader;
            $approval = $jishu_hozen2->approval;
            if ($jishu_hozen2->approval == Null) {
              $jml_null = $jml_null + 1;
            }
            if ($jishu_hozen2->approval_leader == Null) {
              $jml_null_leader = $jml_null_leader + 1;
            }
            $approved_date = $jishu_hozen2->approved_date;
            $approved_date_leader = $jishu_hozen2->approved_date_leader;
        }
        if($jishu_hozen == null){
            // return redirect('/index/production_audit/index/'.$id.'/'.$request->get('product').'/'.$request->get('proses'))->with('error', 'Data Tidak Tersedia.')->with('page', 'Production Audit');
            echo "<script>
                alert('Data Tidak Tersedia');
                window.close();</script>";
        }else{
            $data = array(
                          'subsection' => $subsection,
                          'leader' => $leader,
                          'foreman' => $foreman,
                          'monthTitle' => $monthTitle,
                          'subsection' => $subsection,
                          'date' => $date,
                          'approval' => $approval,
                          'jml_null' => $jml_null,
                          'role_code' => Auth::user()->role_code,
                          'jml_null_leader' => $jml_null_leader,
                          'approved_date' => $approved_date,
                          'approval_leader' => $approval_leader,
                          'approved_date_leader' => $approved_date_leader,
                          'jishu_hozen' => $jishu_hozen,
                          'departments' => $departments,
                          'activity_name' => $activity_name,
                          'activity_alias' => $activity_alias,
                          'activity_list_id' => $activity_list_id,
                          'id' => $id,
                          'jishu_hozen_id' => $jishu_hozen_id,
                          'month' => $month,
                          'id_departments' => $id_departments);
            return view('jishu_hozen.print_email', $data
                )->with('page', 'Jishu Hozen');
        }
    }

    public function sendemail($id,$jishu_hozen_point_id)
      {
          $query_jishu_hozen = "SELECT
            *,
            jishu_hozens.id AS jishu_hozen_id 
          FROM
            jishu_hozens
            JOIN activity_lists ON activity_lists.id = jishu_hozens.activity_list_id
            JOIN departments ON activity_lists.department_id = departments.id
            JOIN jishu_hozen_points ON jishu_hozen_points.id = jishu_hozens.jishu_hozen_point_id 
          WHERE
            jishu_hozens.id = '".$id."'";
          
          $jishu_hozen = DB::select($query_jishu_hozen);
          $jishu_hozen3 = DB::select($query_jishu_hozen);
          // $training2 = DB::select($query_training);

          if($jishu_hozen != null){
            foreach($jishu_hozen as $jishu_hozen){
              $foreman = $jishu_hozen->foreman;
              $send_status = $jishu_hozen->send_status;
              $activity_list_id = $jishu_hozen->activity_list_id;
              $jishu_hozen2 = JishuHozen::find($id);
              $jishu_hozen2->send_status = "Sent";
              $jishu_hozen2->send_date = date('Y-m-d');
              $jishu_hozen2->approval_leader = "Approved";
              $jishu_hozen2->approved_date_leader = date('Y-m-d');
              $jishu_hozen2->save();
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
            return redirect('/index/jishu_hozen/nama_pengecekan/'.$activity_list_id)->with('error', 'Data tidak tersedia.')->with('page', 'Jishu Hozen');
          }

          if($send_status == "Sent"){
            return redirect('/index/jishu_hozen/nama_pengecekan/'.$activity_list_id)->with('error', 'Data pernah dikirim.')->with('page', 'Jishu Hozen');
          }
          
          elseif($jishu_hozen != null){
              Mail::to($mail_to)->bcc('mokhamad.khamdan.khabibi@music.yamaha.com')->send(new SendEmail($jishu_hozen3, 'jishu_hozen'));
              return redirect('/index/jishu_hozen/nama_pengecekan/'.$activity_list_id)->with('status', 'Your E-mail has been sent.')->with('page', 'Jishu Hozen');
          }
          else{
            return redirect('/index/jishu_hozen/nama_pengecekan/'.$activity_list_id)->with('error', 'Data tidak tersedia.')->with('page', 'Jishu Hozen');
          }
      }

      public function approval(Request $request,$id,$jishu_hozen_id,$month)
      {
          $approve = $request->get('approve');
          $approvecount = count($approve);
          if($approvecount == 0){
            // echo "<script>alert('Data Belum Terverifikasi. Checklist semua poin jika akan verifikasi data.')</script>";
            return redirect('/index/jishu_hozen/print_jishu_hozen_email/'.$id.'/'.$jishu_hozen_id.'/'.$month)->with('error', 'Data Belum Terverifikasi. Checklist semua poin jika akan verifikasi data.')->with('page', 'Jishu Hozen');
          }
          else{
                $jishu_hozen = JishuHozen::find($jishu_hozen_id);
                $jishu_hozen->approval = "Approved";
                $jishu_hozen->approved_date = date('Y-m-d');
                $jishu_hozen->save();
            return redirect('/index/jishu_hozen/print_jishu_hozen_email/'.$id.'/'.$jishu_hozen_id.'/'.$month)->with('status', 'Approved.')->with('page', 'Jishu Hozen');
          }
      }

    public function indexJishuHozen()
    {

      $area = AreaCode::get();

      return view('jishu_hozen.maintenance.index', array(
          'title' => 'Jishu Hozen', 
          'title_jp' => '️自主保全',
          'area' => $area
      ))->with('page', 'Jishu Hozen');
    }

    public function fetchJishuHozenTitle(Request $request)
    {
      try {
        $jishu_hozen_title = DB::SELECT("SELECT DISTINCT
          ( jishu_id ),
          title,
          area_code,
          location,
          -- department,
          machine 
        FROM
          `maintenance_jishu_hozen_points` 
        WHERE
          area_code = '".$request->get('area_code')."'");

        $response = array(
            'status' => true,
            'jishu_hozen_title' => $jishu_hozen_title,
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

    public function fetchJishuHozen(Request $request)
    {
      try {
        $jishu_hozen_point = MaintenanceJishuHozenPoint::where('area_code',$request->get('area_code'))->where('jishu_id',$request->get('jishu_id'))->get();

        $jishu_hozen = null;

        if ($request->get('date_from') == '' && $request->get('date_to') == '') {
          $where = "and DATE(maintenance_jishu_hozens.created_at) = DATE(NOW())";
        }else{
          if ($request->get('date_from') != '' && $request->get('date_to') == '') {
            $where = "and DATE(maintenance_jishu_hozens.created_at) >= '".$request->get('date_from')."' and DATE(maintenance_jishu_hozens.created_at) <= DATE(NOW())";
          }else if($request->get('date_from') == '' && $request->get('date_to') != ''){
            $where = "and DATE(maintenance_jishu_hozens.created_at) >= DATE_FORMAT(NOW(),'%Y-%m-01') and DATE(maintenance_jishu_hozens.created_at) <= '".$request->get('date_to')."'";
          }else{
            $where = "and DATE(maintenance_jishu_hozens.created_at) >= '".$request->get('date_from')."' and DATE(maintenance_jishu_hozens.created_at) <= '".$request->get('date_to')."'";
          }
        }

        $jishu_hozen = DB::SELECT("SELECT
          maintenance_jishu_hozens.*,
          maintenance_jishu_hozen_points.*,
          CONCAT( emppic.employee_id, '<br>', emppic.`name` ) AS pic_check,
          CONCAT( empleader.employee_id, '<br>', empleader.`name` ) AS leader,
          CONCAT( empforeman.employee_id, '<br>', empforeman.`name` ) AS foreman,
          maintenance_jishu_hozens.created_at AS created 
        FROM
          maintenance_jishu_hozens
          LEFT JOIN maintenance_jishu_hozen_points ON maintenance_jishu_hozens.point_id = maintenance_jishu_hozen_points.id
          LEFT JOIN employee_syncs emppic ON emppic.employee_id = maintenance_jishu_hozens.pic_check
          LEFT JOIN employee_syncs empleader ON empleader.employee_id = maintenance_jishu_hozens.pic_check
          LEFT JOIN employee_syncs empforeman ON empforeman.employee_id = maintenance_jishu_hozens.pic_check 
        WHERE
        maintenance_jishu_hozen_points.area_code = '".$request->get('area_code')."' 
        AND maintenance_jishu_hozen_points.jishu_id = '".$request->get('jishu_id')."' ".$where." 
        ORDER BY maintenance_jishu_hozen_points.id");
        

        $response = array(
            'status' => true,
            'jishu_hozen_point' => $jishu_hozen_point,
            'jishu_hozen' => $jishu_hozen,
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

    public function inputJishuHozen(Request $request)
    {
      try {
        $check = MaintenanceJishuHozen:: where('date',$request->get('check_date'))->where('jishu_id',$request->get('jishu_id'))->get();
        if (count($check) > 1) {
          $response = array(
              'status' => false,
              'message' => 'Anda sudah mengisi hari ini'
          );
          return Response::json($response);
        }else{
          $result_check = $request->get('result_check');
          $jishu_id = $request->get('jishu_id');
          $leader = $request->get('leader');
          $foreman = $request->get('foreman');
          $check_date = $request->get('check_date');
          $jishu_hozen_title = $request->get('jishu_hozen_title');

          $employee_id = Auth::user()->username;

          for($i = 0; $i < count($result_check);$i++){
            $jishu_hozen = New MaintenanceJishuHozen([
                'jishu_id' => $jishu_id,
                'date' => $check_date,
                'title' => $jishu_hozen_title,
                'point_id' => $result_check[$i]['point_check_id'],
                'point_result' => $result_check[$i]['result'],
                'pic_check' => $employee_id,
                'leader' => $leader,
                'foreman' => $foreman,
                'created_by' => Auth::user()->id,
            ]);
            $jishu_hozen->save();
          }
          $response = array(
              'status' => true,
          );
          return Response::json($response);
        }
      } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage(),
        );
        return Response::json($response);
      }
    }

    public function editJishuHozen(Request $request)
    {
      try {
        $jishu_hozen_point = MaintenanceJishuHozenPoint::where('area_code',$request->get('area_code'))->where('jishu_id',$request->get('jishu_id'))->get();

        $jishu_hozen = DB::SELECT("SELECT
          maintenance_jishu_hozens.*,
          maintenance_jishu_hozen_points.*
        FROM
          maintenance_jishu_hozens
          LEFT JOIN maintenance_jishu_hozen_points ON maintenance_jishu_hozens.point_id = maintenance_jishu_hozen_points.id
        WHERE
        maintenance_jishu_hozen_points.area_code = '".$request->get('area_code')."' 
        AND maintenance_jishu_hozen_points.jishu_id = '".$request->get('jishu_id')."' 
        AND maintenance_jishu_hozens.date = '".$request->get('date')."'
        ORDER BY maintenance_jishu_hozen_points.id");

        $response = array(
            'status' => true,
            'jishu_hozen' => $jishu_hozen,
            'jishu_hozen_point' => $jishu_hozen_point,
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

    public function updateJishuHozen(Request $request)
    {
      try {
        $result_check = $request->get('result_check');
        $jishu_id = $request->get('jishu_id');
        $leader = $request->get('leader');
        $foreman = $request->get('foreman');
        $check_date = $request->get('check_date');
        $jishu_hozen_title = $request->get('jishu_hozen_title');

        $employee_id = Auth::user()->username;

        for($i = 0; $i < count($result_check);$i++){
          $jishu_hozen = MaintenanceJishuHozen:: where('date',$request->get('check_date'))->where('jishu_id',$request->get('jishu_id'))->where('point_id',$result_check[$i]['point_check_id'])->first();
          $jishu_hozen->point_result = $result_check[$i]['result'];
          $jishu_hozen->save();
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

    public function indexDisplayJishuHozen()
    {
      $area = AreaCode::get();

      return view('jishu_hozen.maintenance.jishu_hozen_monitoring', array(
          'title' => 'Jishu Hozen Monitoring', 
          'title_jp' => '️自主保全',
          'area' => $area
      ))->with('page', 'Jishu Hozen Monitoring');
    }

    public function fetchDisplayJishuHozen(Request $request)
    {
      try {
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        if ($date_from == "") {
             if ($date_to == "") {
                  $first = "'".date('Y-m-01')."'";
                  $last = "'".date('Y-m-d')."'";
                  $dateTitleFirst = date('d M Y',strtotime(date('Y-m-01')));
                  $dateTitleLast = date('d M Y',strtotime(date('Y-m-d')));
             }else{
                  $first = "'".date('Y-m-01')."'";
                  $last = "'".$date_to."'";
                  $dateTitleFirst = date('d M Y',strtotime(date('Y-m-01')));
                  $dateTitleLast = date('d M Y',strtotime($date_to));
             }
        }else{
             if ($date_to == "") {
                  $first = "'".$date_from."'";
                  $last = "'".date('Y-m-d')."'";
                  $dateTitleFirst = date('d M Y',strtotime($date_from));
                  $dateTitleLast = date('d M Y',strtotime(date('Y-m-d')));
             }else{
                  $first = "'".$date_from."'";
                  $last = "'".$date_to."'";
                  $dateTitleFirst = date('d M Y',strtotime($date_from));
                  $dateTitleLast = date('d M Y',strtotime($date_to));
             }
        }
        if ($request->get('area_code') == '') {
          $jishu_hozen = DB::SELECT("SELECT
            week_date,
            ( SELECT GROUP_CONCAT(DISTINCT(jishu_id)) AS plan FROM maintenance_jishu_hozen_points) AS plan,
            (
            SELECT
              GROUP_CONCAT(DISTINCT(maintenance_jishu_hozens.jishu_id)) AS actual 
            FROM
              maintenance_jishu_hozens
              LEFT JOIN maintenance_jishu_hozen_points ON maintenance_jishu_hozens.jishu_id = maintenance_jishu_hozen_points.jishu_id 
            WHERE
               DATE( maintenance_jishu_hozens.created_at ) = weekly_calendars.week_date 
            ) AS actual 
          FROM
            weekly_calendars 
          WHERE
            remark != 'H' 
            AND week_date >= ".$first." 
            AND week_date <= ".$last."");
          $area_name = 'ALL YMPI';
        }else{
          $jishu_hozen = DB::SELECT("SELECT
            week_date,
            ( SELECT GROUP_CONCAT(DISTINCT(jishu_id)) AS plan FROM maintenance_jishu_hozen_points WHERE area_code = '".$request->get('area_code')."' ) AS plan,
            (
            SELECT
              GROUP_CONCAT(DISTINCT(maintenance_jishu_hozens.jishu_id)) AS actual 
            FROM
              maintenance_jishu_hozens
              LEFT JOIN maintenance_jishu_hozen_points ON maintenance_jishu_hozens.jishu_id = maintenance_jishu_hozen_points.jishu_id 
            WHERE
              area_code = '".$request->get('area_code')."' 
              AND DATE( maintenance_jishu_hozens.created_at ) = weekly_calendars.week_date 
            ) AS actual 
          FROM
            weekly_calendars 
          WHERE
            remark != 'H' 
            AND week_date >= ".$first." 
            AND week_date <= ".$last."");
          $area = AreaCode::where('area_code',$request->get('area_code'))->first();
          $area_name = $area->area;
        }
        $response = array(
            'status' => true,
            'jishu_hozen' => $jishu_hozen,
            'dateTitleFirst' => $dateTitleFirst,
            'dateTitleLast' => $dateTitleLast,
            'area_name' => $area_name,
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

    public function fetchJishuHozenProd(Request $request)
    {
      try {
        if ($request->get('month') == null || $request->get('month') == "") {
          $month = date('Y-m');
        }else{
          $month = $request->get('month');
        }
        $point = JishuHozenPoint::where('jishu_hozen_points.activity_list_id',$request->get('id'));
        $jishu_hozen = JishuHozen::select('jishu_hozens.*','jishu_hozen_points.nama_pengecekan')->where('month',$month)->join('jishu_hozen_points','jishu_hozens.jishu_hozen_point_id','jishu_hozen_points.id')->where('jishu_hozens.activity_list_id',$request->get('id'));

        if ($request->get('jishu_hozen_point') != '') {
          $jishu_hozen = $jishu_hozen->wherein('jishu_hozens.jishu_hozen_point_id',explode(',', $request->get('jishu_hozen_point')));
          $point = $point->wherein('id',explode(',', $request->get('jishu_hozen_point')));
        }

        $jishu_hozen = $jishu_hozen->get();
        $point = $point->get();
        $response = array(
            'status' => true,
            'jishu_hozen' => $jishu_hozen,
            'point' => $point,
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
}
