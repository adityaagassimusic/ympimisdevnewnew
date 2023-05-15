<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\ActivityList;
use App\EmployeeSync;
use Response;
use Illuminate\Support\Facades\DB;
use App\User;

class ActivityListController extends Controller
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
      $this->activity_type = ['Audit',
                        'Training',
                        'Laporan Aktivitas',
                        'Sampling Check',
                        'Pengecekan Foto',
                        'Interview',
                        'Pengecekan',
                        'Pemahaman Proses',
                        'Labelisasi',
                        'Cek Area',
                        'Jishu Hozen',
                        'Cek APD',
                        'Weekly Report',
                        'Temuan NG'];

      $this->frequency = ['Daily',
                        'Weekly',
                        'Monthly',
                        'Conditional'];
    }

    function index()
    {
      $emp_id = Auth::user()->username;
      $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

    	$activityList = ActivityList::get();
    	$data = array('activity_list' => $activityList);
    	return view('activity_list.index', $data
    		)->with('page', 'Activity List');
    }

    function filter($id,$no,$frequency)
    {
      try {
        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

        $name = Auth::user()->name;
        $role_code = Auth::user()->role_code;
        $queryDepartments = "SELECT * FROM departments where id='".$id."'";
        $department = DB::select($queryDepartments);
        foreach ($department as $department) {
            $dept_name = $department->department_name;
        }
        if ($no == 1) {
          $activity_type = 'Audit';
          $act_name = 'Audit NG Jelas';
        }
        elseif ($no == 2) {
          $activity_type = 'Training';
          $act_name = 'Training';
        }
        elseif ($no == 3) {
          $activity_type = 'Sampling Check';
          $act_name = 'Sampling Check FG / KD';
        }
        elseif ($no == 4) {
          $activity_type = 'Laporan Aktivitas';
          $act_name = 'Laporan Audit IK';
        }
        elseif ($no == 5) {
          $activity_type = 'Pemahaman Proses';
          $act_name = 'Audit Pemahaman Proses';
        }
        elseif ($no == 6) {
          $activity_type = 'Pengecekan';
          $act_name = 'Cek Produk Pertama';
        }
        elseif ($no == 7) {
          $activity_type = 'Interview';
          $act_name = 'Interview Pointing Call';
        }
        elseif ($no == 8) {
          $activity_type = 'Pengecekan Foto';
          $act_name = 'Cek FG / KD Harian';
        }
        elseif ($no == 9) {
          $activity_type = 'Labelisasi';
          $act_name = 'Audit Label Safety';
        }
        elseif ($no == 10) {
          $activity_type = 'Cek Area';
          $act_name = 'Cek Safety Area Kerja';
        }
        elseif ($no == 11) {
          $activity_type = 'Jishu Hozen';
          $act_name = 'Audit Jishu Hozen';
        }
        elseif ($no == 12) {
          $activity_type = 'Cek APD';
          $act_name = 'Cek APD';
        }
        elseif ($no == 13) {
          $activity_type = 'Weekly Report';
          $act_name = 'Weekly Report';
        }
        elseif ($no == 14) {
          $activity_type = 'Temuan NG';
          $act_name = 'Temuan NG';
        }
        elseif ($no == 15) {
          $activity_type = 'Audit Kanban';
          $act_name = 'Audit Kanban';
        }elseif ($no == 16) {
          $activity_type = 'Daily Audit';
          $act_name = 'Daily Audit';
        }

        if ($frequency == 'Daily') {
          $frekuensi = 'Harian';
        }else if($frequency == 'Weekly'){
          $frekuensi = 'Mingguan';
        }else if($frequency == 'Monthly'){
          $frekuensi = 'Bulanan';
        }else if($frequency == 'Conditional'){
          $frekuensi = 'Kondisional';
        }

        $emp = EmployeeSync::where('employee_id',$emp_id)->first();

        if($emp->position == 'Leader'){
          $activityList = ActivityList::where('department_id',$id)->where('activity_type',$activity_type)->where('leader_dept',$name)->where('activity_name','!=','Null')->where('frequency',$frequency)->get();
        }
        else{
          $activityList = ActivityList::where('department_id',$id)->where('activity_type',$activity_type)->where('activity_name','!=','Null')->where('frequency',$frequency)->distinct()->get();
        }

        $response = array(
          'status' => true,
          'activity_list' => $activityList,
          'department' => $department,
          'dept_name' => strtoupper($dept_name),
          'id' => $id,
          'frekuensi' => $frekuensi,
          'act_name' => $act_name,
          'activity_type' => $activity_type,
          'no' => $no,
        );
        return Response::json($response);
      } catch (\Exception $e) {
        $response = array(
          'status' => false,
          'message' => $e->getMessage()
        );
        return Response::json($response);
      }
      // $data = array('activity_list' => $activityList,
      //               'department' => $department,
      //               'dept_name' => strtoupper($dept_name),
      //               'id' => $id,
      //               'frekuensi' => $frekuensi,
      //               'act_name' => $act_name,
      //               'activity_type' => $activity_type,
      //               'no' => $no,);
      // return view('activity_list.filter', $data
      //   )->with('page', 'Activity List');
    }

    function resume($id)
    {
      $activityList = ActivityList::where('department_id',$id)->where('activity_name','!=','Null')->get();

      $queryDepartments2 = "SELECT * FROM departments where id='".$id."'";
      $department_by_id = DB::select($queryDepartments2);
      foreach ($department_by_id as $department_by_id) {
          $dept_name = $department_by_id->department_name;
      }

      $leader_dept = 'All Leader';
      $frequency = 'Daily, Weekly, Monthly, and Conditional';

      $queryLeader = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            join promotion_logs on employees.employee_id= promotion_logs.employee_id
            where (mutation_logs.department = '".$dept_name."' and promotion_logs.`position` = 'leader') or (mutation_logs.department = '".$dept_name."' and promotion_logs.`position`='foreman')";
      $leader = DB::select($queryLeader);
      $leader2 = DB::select($queryLeader);
      $leader3 = DB::select($queryLeader);

      $data = array('activity_list' => $activityList,
                    'dept_name' => $dept_name,
                    'leader2' => $leader2,
                    'leader3' => $leader3,
                    'leader' => $leader,
                    'leader_dept' => $leader_dept,
                    'frequency_dept' => $frequency,
                    'id' => $id,
                    'frequency' => $this->frequency);
      return view('activity_list.resume', $data
        )->with('page', 'Leader Task');
    }

    function resume_filter(Request $request,$id)
    {
      $queryDepartments2 = "SELECT * FROM departments where id='".$id."'";
      $department_by_id = DB::select($queryDepartments2);
      foreach ($department_by_id as $department_by_id) {
          $dept_name = $department_by_id->department_name;
      }

      if($request->get('frequency') != null && $request->get('leader') != null){
        $leader_dept = $request->get('leader');
        $frequency = $request->get('frequency');
        $activityList = ActivityList::where('department_id',$id)->where('activity_name','!=','Null')->where('frequency',$frequency)->where('leader_dept',$leader_dept)->get();
      }
      elseif($request->get('frequency') == null && $request->get('leader') != null){
        $frequency = 'Daily, Weekly, Monthly, and Conditional';
        $leader_dept = $request->get('leader');
        $activityList = ActivityList::where('department_id',$id)->where('activity_name','!=','Null')->where('leader_dept',$leader_dept)->get();
      }
      elseif($request->get('frequency') != null && $request->get('leader') == null){
        $leader_dept = 'All Leader';
        $frequency = $request->get('frequency');
        $activityList = ActivityList::where('department_id',$id)->where('activity_name','!=','Null')->where('frequency',$frequency)->get();
      }
      else{
        $leader_dept = 'All Leader';
        $frequency = 'Daily, Weekly, Monthly, and Conditional';
        $activityList = ActivityList::where('department_id',$id)->where('activity_name','!=','Null')->get();
      }

      $queryLeader = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            where (mutation_logs.department = '".$dept_name."' and mutation_logs.`group` = 'leader') or (mutation_logs.department = '".$dept_name."' and mutation_logs.`group`='foreman')";
      $leader = DB::select($queryLeader);
      $leader2 = DB::select($queryLeader);
      $leader3 = DB::select($queryLeader);


      $data = array('activity_list' => $activityList,
                    'dept_name' => $dept_name,
                    'leader' => $leader,
                    'leader2' => $leader2,
                    'leader3' => $leader3,
                    'leader_dept' => $leader_dept,
                    'frequency_dept' => $frequency,
                    'id' => $id,
                    'frequency' => $this->frequency);
      return view('activity_list.resume', $data
        )->with('page', 'Leader Task');
    }

    function create()
    {
    	$queryDepartments = "SELECT * FROM departments where id_division=5";
    	$department = DB::select($queryDepartments);

      $queryLeader = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join promotion_logs on employees.employee_id= promotion_logs.employee_id
            where promotion_logs.`position` = 'leader' orpromotion_logs.`position`='foreman'";
      $queryForeman = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join promotion_logs on employees.employee_id= promotion_logs.employee_id
            where promotion_logs.`position`='foreman'";
      $leader = DB::select($queryLeader);
      $foreman = DB::select($queryForeman);

    	$data = array('department' => $department,
    				  'activity_type' => $this->activity_type,
              'id' => 0,
              'leader' => $leader,
              'foreman' => $foreman,
              'dept_name' => null);
    	return view('activity_list.create', $data
    		)->with('page', 'Activity List');
    }

    function create_by_department($id,$no)
    {
      $queryDepartments2 = "SELECT * FROM departments where id='".$id."'";
      $department_by_id = DB::select($queryDepartments2);
      foreach ($department_by_id as $department_by_id) {
          $dept_name = $department_by_id->department_name;
      }

      $queryDepartments = "SELECT * FROM departments where id_division=5";
      $department = DB::select($queryDepartments);

      $queryLeader = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            join promotion_logs on employees.employee_id= promotion_logs.employee_id
            where (mutation_logs.department = '".$dept_name."' and promotion_logs.`position` = 'leader') or (mutation_logs.department = '".$dept_name."' and promotion_logs.`position`='foreman')";
      $queryForeman = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            join promotion_logs on employees.employee_id= promotion_logs.employee_id
            where (mutation_logs.department = '".$dept_name."' and promotion_logs.`position`='foreman')";
      $leader = DB::select($queryLeader);
      $foreman = DB::select($queryForeman);

      $data = array('department' => $department,
                    'activity_type' => $this->activity_type,
                    'dept_name' => $dept_name,
                    'id' => $id,
                    'leader' => $leader,
                    'foreman' => $foreman,
                    'no' => $no);
      return view('activity_list.create', $data
        )->with('page', 'Activity List');
    }

    public function store(request $request)
    {
      try{
          $date = date('Y-m-d');

          $fyQuery = "SELECT DISTINCT(fiscal_year) FROM weekly_calendars where week_date = '".$date."'";
          $fyHasil = DB::select($fyQuery);

          foreach($fyHasil as $fyHasil){
            $fy = $fyHasil->fiscal_year;
          }
          $id = Auth::id();
          $activity_list = new ActivityList([
            'activity_name' => $request->get('activity_name'),
            'activity_alias' => $request->get('activity_alias'),
            'frequency' => $request->get('frequency'),
            'department_id' => $request->get('department_id'),
            'activity_type' => $request->get('activity_type'),
            'leader_dept' => $request->get('leader'),
            'foreman_dept' => $request->get('foreman'),
            'plan_time' => $request->get('plan_time'),
            'created_by' => $id
          ]);

          $activity_list->save();
          return redirect('/index/activity_list')->with('status', 'New Activity has been created.')->with('page', 'Activity List');
      }
      catch (QueryException $e){
        $error_code = $e->errorInfo[1];
        if($error_code == 1062){
          return back()->with('error', 'Activity already exist.')->with('page', 'Activity List');
        }
        else{
          return back()->with('error', $e->getMessage())->with('page', 'Activity List');
        }
      }
    }

    public function store_by_department(request $request,$id,$no)
    {
      try{
          $id_user = Auth::id();
          $activity_list = new ActivityList([
            'activity_name' => $request->get('activity_name'),
            'activity_alias' => $request->get('activity_alias'),
            'frequency' => $request->get('frequency'),
            'department_id' => $request->get('department_id'),
            'activity_type' => $request->get('activity_type'),
            'leader_dept' => $request->get('leader'),
            'foreman_dept' => $request->get('foreman'),
            'plan_time' => $request->get('plan_time'),
            'created_by' => $id_user
          ]);

          $activity_list->save();
          return redirect('/index/activity_list/filter/'.$id.'/'.$no)->with('status', 'New Activity has been created.')->with('page', 'Activity List');
      }
      catch (QueryException $e){
        $error_code = $e->errorInfo[1];
        if($error_code == 1062){
          return back()->with('error', 'Activity already exist.')->with('page', 'Activity List');
        }
        else{
          return back()->with('error', $e->getMessage())->with('page', 'Activity List');
        }
      }
    }

    public function show($id)
    {
      $activity_list = ActivityList::find($id);
      $data = array('activity_list' => $activity_list);
    	return view('activity_list.view', $data
    		)->with('page', 'Activity List');
    }

    public function edit($id)
    {
      $queryDepartments = "SELECT * FROM departments where id_division=5";
      $department = DB::select($queryDepartments);

      $queryLeader = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            where mutation_logs.`group` = 'leader' or mutation_logs.`group`='foreman'";
      $queryForeman = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            where mutation_logs.`group`='foreman'";
      $leader = DB::select($queryLeader);
      $foreman = DB::select($queryForeman);

      $activity_list = ActivityList::find($id);
      $data = array(
              'id_department' => 0,
              'department' => $department,
              'leader' => $leader,
              'foreman' => $foreman,
      				'activity_list' => $activity_list,
  					  'activity_type' => $this->activity_type);
    	return view('activity_list.edit', $data
    		)->with('page', 'Activity List');
    }

    public function edit_by_department($id,$department_id,$no)
    {
      $queryDepartments = "SELECT * FROM departments where id_division=5";
      $department = DB::select($queryDepartments);
      $activity_list = ActivityList::find($id);
      $id_department = $activity_list->department_id;
      $dept_name = $activity_list->departments->department_name;

      $queryLeader = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            join promotion_logs on employees.employee_id= promotion_logs.employee_id
            where (mutation_logs.department = '".$dept_name."' and promotion_logs.`position` = 'leader') or (mutation_logs.department = '".$dept_name."' and promotion_logs.`position`='foreman')";
      $queryForeman = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            where (mutation_logs.department = '".$dept_name."' and mutation_logs.`group`='foreman')";
      $leader = DB::select($queryLeader);
      $foreman = DB::select($queryForeman);

      $data = array(
              'id_department' => $id_department,
              'department' => $department,
              'no' => $no,
              'activity_list' => $activity_list,
              'leader' => $leader,
              'foreman' => $foreman,
              'activity_type' => $this->activity_type);
      return view('activity_list.edit', $data
        )->with('page', 'Activity List');
    }

    public function update(Request $request, $id)
    {
          try{
          	$activity_list = ActivityList::find($id);
            $activity_list->activity_name = $request->get('activity_name');
            $activity_list->activity_alias = $request->get('activity_alias');
            $activity_list->frequency = $request->get('frequency');
            $activity_list->department_id = $request->get('department_id');
            $activity_list->activity_type = $request->get('activity_type');
            $activity_list->leader_dept = $request->get('leader');
            $activity_list->foreman_dept = $request->get('foreman');
            $activity_list->plan_time = $request->get('plan_time');
            $activity_list->save();
            return redirect('/index/activity_list')->with('status', 'Activity data has been updated.')->with('page', 'Activity List');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Activity already exist.')->with('page', 'Activity List');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Activity List');
            }
          }
    }

    public function update_by_department(Request $request, $id,$id_department,$no)
    {
          try{
            $activity_list = ActivityList::find($id);
            $activity_list->activity_name = $request->get('activity_name');
            $activity_list->activity_alias = $request->get('activity_alias');
            $activity_list->frequency = $request->get('frequency');
            $activity_list->department_id = $request->get('department_id');
            $activity_list->activity_type = $request->get('activity_type');
            $activity_list->leader_dept = $request->get('leader');
            $activity_list->foreman_dept = $request->get('foreman');
            $activity_list->plan_time = $request->get('plan_time');
            $activity_list->save();
            return redirect('/index/activity_list/filter/'.$id_department.'/'.$no)->with('status', 'Activity data has been updated.')->with('page', 'Activity List');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'Activity already exist.')->with('page', 'Activity List');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'Activity List');
            }
          }
    }

    public function destroy($id)
    {
      $activity_list = ActivityList::find($id);
      $activity_list->delete();

      return redirect('/index/activity_list')->with('status', 'Activity has been deleted.')->with('page', 'Activity List');
        //
    }

    public function destroy_by_department($id,$department_id,$no)
    {
      $activity_list = ActivityList::find($id);
      $activity_list->delete();

      return redirect('/index/activity_list/filter/'.$department_id.'/'.$no)->with('status', 'Activity has been deleted.')->with('page', 'Activity List');
        //
    }
}
