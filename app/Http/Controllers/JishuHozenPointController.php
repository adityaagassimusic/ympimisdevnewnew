<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use Illuminate\Support\Facades\DB;
use App\User;
use App\JishuHozenPoint;
use App\AreaCode;
use App\MaintenanceJishuHozenPoint;
use App\MaintenanceJishuHozen;
use App\CodeGenerator;
use App\EmployeeSync;
use Response;
use DataTables;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class JishuHozenPointController extends Controller
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
    	$jishu_hozen_point = JishuHozenPoint::where('activity_list_id',$id)
            ->orderBy('jishu_hozen_points.id','desc')->get();

    	$activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $leader = $activityList->leader_dept;
        $foreman = $activityList->foreman_dept;
        $frequency = $activityList->frequency;

    	$data = array('jishu_hozen_point' => $jishu_hozen_point,
    				  'departments' => $departments,
    				  'activity_name' => $activity_name,
                      'activity_alias' => $activity_alias,
                      'leader' => $leader,
                      'foreman' => $foreman,
    				  'id' => $id,
              'frequency' => $frequency,
                      'id_departments' => $id_departments);
    	return view('jishu_hozen_point.index', $data
    		)->with('page', 'Jishu Hozen Point');
    }

    function store(Request $request,$id)
    {
            try{
              $id_user = Auth::id();
                JishuHozenPoint::create([
                    'activity_list_id' => $id,
                    'nama_pengecekan' => $request->get('inputnama_pengecekan'),
                    'leader' => $request->get('inputleader'),
                    'foreman' => $request->get('inputforeman'),
                    'created_by' => $id_user
                ]);

              return redirect('/index/jishu_hozen_point/index/'.$id)->with('status', 'Jishu Hozen Point data has been created.')->with('page', 'Jishu Hozen Point');
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    function show($id,$jishu_hozen_point_id)
    {
        $activityList = ActivityList::find($id);
        $jishu_hozen_point = JishuHozenPoint::find($jishu_hozen_point_id);
        // foreach ($activityList as $activityList) {
            $activity_name = $activityList->activity_name;
            $departments = $activityList->departments->department_name;
            $activity_alias = $activityList->activity_alias;
            $leader = $activityList->leader_dept;
        	$foreman = $activityList->foreman_dept;

        // }
        $data = array('jishu_hozen_point' => $jishu_hozen_point,
                      'departments' => $departments,
                      'activity_name' => $activity_name,
                      'leader' => $leader,
                      'foreman' => $foreman,
                      'id' => $id);
        return view('jishu_hozen_point.view', $data
            )->with('page', 'Jishu Hozen Point');
    }

    public function destroy($id,$jishu_hozen_point_id)
    {
      $jishu_hozen_point = JishuHozenPoint::find($jishu_hozen_point_id);
      $jishu_hozen_point->delete();

      return redirect('/index/jishu_hozen_point/index/'.$id)
        ->with('status', 'Jishu Hozen Point has been deleted.')
        ->with('page', 'Jishu Hozen Point');        
    }

    public function getdetail(Request $request)
    {
         try{
            $detail = JishuHozenPoint::find($request->get("id"));
            $data = array('jishu_hozen_point_id' => $detail->id,
            			  'nama_pengecekan' => $detail->nama_pengecekan,
                          'leader' => $detail->leader,
                          'foreman' => $detail->foreman);

            $response = array(
              'status' => true,
              'data' => $data
            );
            return Response::json($response);

          }
          catch (QueryException $jishu_hozen_point){
            $error_code = $jishu_hozen_point->errorInfo[1];
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

    function update(Request $request,$id,$jishu_hozen_point_id)
    {
      try{
                  $jishu_hozen_point = JishuHozenPoint::find($jishu_hozen_point_id);
                  $jishu_hozen_point->nama_pengecekan = $request->get('editnama_pengecekan');
                  $jishu_hozen_point->save();

            return redirect('index/jishu_hozen_point/index/'.$id)
              ->with('page', 'Jishu Hozen Point')->with('status', 'Jishu Hozen Point has been updated.');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Jishu Hozen Point already exist.')->with('page', 'Jishu Hozen Point');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Jishu Hozen Point');
            }
          }
    }

    public function indexJishuHozenPoint()
    {
      $area = AreaCode::get();
      $leader = EmployeeSync::where('position','Leader')->where('end_date',null)->get();
      $foreman = EmployeeSync::where('position','Foreman')->where('end_date',null)->orwhere('employee_id','PI1110001')->get();

      return view('jishu_hozen_point.maintenance.index', array(
          'title' => 'Jishu Hozen Point', 
          'title_jp' => '️自主保全',
          'area' => $area,
          'area2' => $area,
          'leader' => $leader,
          'foreman' => $foreman,
      ))->with('page', 'Jishu Hozen Point');
    }

    public function fetchJishuHozenPoint(Request $request)
    {
      try {
        if ($request->get('jishu_id') == '') {
          $jishu_hozen_point = MaintenanceJishuHozenPoint::where('area_code',$request->get('area_code'))->get();
        }else{
          $jishu_hozen_point = MaintenanceJishuHozenPoint::where('jishu_id',$request->get('jishu_id'))->get();
        }

        if (count($jishu_hozen_point) > 0) {
          $code_generator = CodeGenerator::where('note','=','jishu_hozen_point')->first();
          $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
          $jishu_id = $code_generator->prefix . $number;

          $response = array(
              'status' => true,
              'jishu_hozen_point' => $jishu_hozen_point,
              'jishu_id' => $jishu_id,
          );
          return Response::json($response);
        }else{
          $response = array(
            'status' => false,
            'message' => 'Not Found',
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

    public function inputJishuHozenPoint(Request $request)
    {
      try {
        $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();
        $area_code = $request->get('area_code');
        $jishu_id = $request->get('jishu_id');
        $machine = $request->get('machine');
        $title = $request->get('title');
        $doc_number = $request->get('doc_number');
        $rev = $request->get('rev');
        $rev_date = $request->get('rev_date');
        $jishu_hozen_point = $request->get('jishu_hozen_point');
        $jishu_hozen_point_id = $request->get('jishu_hozen_point_id');
        $leader = $request->get('leader');
        $foreman = $request->get('foreman');

        $jishu = MaintenanceJishuHozenPoint::where('jishu_id',$jishu_id)->get();
        $id = [];
        for ($i=0; $i < count($jishu); $i++) { 
          array_push($id, $jishu[$i]->id);
        }
        $id_input = [];
        for ($l=0; $l < count($jishu_hozen_point); $l++) { 
          array_push($id_input, $jishu_hozen_point[$l]['id']);
        }

        for ($k=0; $k < count($id); $k++) { 
          if (!in_array($id[$k], $id_input)) {
            $jishudelete = MaintenanceJishuHozenPoint::where('id',$id[$k])->forceDelete();
          }
        }

        for ($j=0; $j < count($jishu_hozen_point); $j++) { 
          if($jishu_hozen_point[$j]['id'] == ''){
            $area = AreaCode::where('area_code',$area_code)->first();
            MaintenanceJishuHozenPoint::create([
              'area_code' => $area_code,
              'location' => $area->area,
              'jishu_id' => $jishu_id,
              'department' => $emp->department,
              'machine' => $machine,
              'title' => $title,
              'doc_number' => $doc_number,
              'rev' => $rev,
              'rev_date' => $rev_date,
              'check_time' => $jishu_hozen_point[$j]['check_time'],
              'classification' => $jishu_hozen_point[$j]['classification'],
              'point_check_index' => $i+1,
              'point_check_name' => $jishu_hozen_point[$j]['point_check_name'],
              'standard' => $jishu_hozen_point[$j]['standard'],
              'point_check_type' => $jishu_hozen_point[$j]['point_check_type'],
              'leader' => $leader,
              'foreman' => $foreman,
              'created_by' => Auth::user()->id,
            ]);
          }
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
}
