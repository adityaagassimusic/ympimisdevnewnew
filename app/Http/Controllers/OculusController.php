<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\SendEmail;
use App\Employee;
use App\EmployeeSync;
use App\CodeGenerator;
use App\User;
use App\OculusUser;
use App\OculusResult;
use Auth;
use DataTables;
use Response;
use Illuminate\Support\Facades\DB;

class OculusController extends Controller
{
    function __construct()
    {
    	// $this->middleware('auth');
    }

    public function indexAuth($employee_id)
    {
    	try {
    		$emp = DB::SELECT("SELECT * from oculus_users where employee_id = '".$employee_id."'");
    		if (count($emp) > 0) {
    			$response = array(
					'status' => true,
					'message' => 'Success',
					'emp' => $emp
				);
				return Response::json($response);
    		}else{
    			$response = array(
					'status' => false,
					'message' => 'Failed'
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

    public function indexResult($employee_id,$answer,$sub_answer,$result)
    {
    	try {
    		$emp = DB::SELECT("SELECT * from oculus_users where employee_id = '".$employee_id."'");
    		if (count($emp) > 0) {
    			$oculus_result = new OculusResult([
					'employee_id' => $employee_id,
					'oculus_answer' => $answer,
					'oculus_sub_answer' => $sub_answer,
					'oculus_result' => $result,
					'created_by' => 1,
				]);
				$oculus_result->save();

	    		$response = array(
					'status' => true,
					'message' => 'Success',
					// 'emp' => $emp
				);
				return Response::json($response);
    		}else{
    			$response = array(
					'status' => false,
					'message' => 'Failed'
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

    public function fetchResult($employee_id)
    {
    	try {
    		$emp = DB::SELECT("SELECT * from oculus_users where employee_id = '".$employee_id."'");
    		if (count($emp) > 0) {
    			$score = DB::SELECT("SELECT * from oculus_results where employee_id = '".$employee_id."'");

	    		$response = array(
					'status' => true,
					'message' => 'Success',
					'score' => $score
				);
				return Response::json($response);
    		}else{
    			$response = array(
					'status' => false,
					'message' => 'Failed'
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

    public function indexUser()
    {
    	$emp = EmployeeSync::where('end_date',null)->get();
    	return view('virtual_reality.index_employees')
      	->with('title', 'Virtual Reality User')
      	->with('title_jp', '')
  		->with('page', 'Virtual Reality User')
  		->with('jpn', '')
  		->with('emp', $emp);
    }

    public function fetchUser(Request $request)
    {
    	try {
    		$user = OculusUser::join('employee_syncs','employee_syncs.employee_id','oculus_users.employee_id')->leftjoin('departments','department_name','department')->get();
    		$response = array(
                'status' => true,
                'users' => $user
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

    public function indexTestReport()
    {
    	return view('virtual_reality.test_report')
      	->with('title', 'Virtual Reality Test Report')
      	->with('title_jp', '')
  		->with('page', 'Virtual Reality Test Report')
  		->with('jpn', '');
    }

    public function fetchTestReport(Request $request)
    {
    	try {
    		$tests = OculusResult::select('oculus_results.*','employee_syncs.*','departments.department_shortname','oculus_results.created_at as created')->leftjoin('employee_syncs','employee_syncs.employee_id','oculus_results.employee_id')->leftjoin('departments','departments.department_name','employee_syncs.department')->orderBy('oculus_results.created_at','desc')->get();
    		$response = array(
                'status' => true,
                'tests' => $tests
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

    public function inputUser(Request $request)
    {
    	try {
    		$emp = $request->get('employees');

    		$error_message = 0;

    		for ($i=0; $i < count($emp); $i++) { 
    			$check = OculusUser::where('employee_id',$emp[$i])->first();
    			if(count($check) > 0){
    				$error_message++;
    			}else{
    				$empys = EmployeeSync::where('employee_id',$emp[$i])->first();
	    			$oculus_user = new OculusUser([
						'employee_id' => $emp[$i],
						'name' => $empys->name,
						'created_by' => Auth::user()->id,
					]);
					$oculus_user->save();
    			}
    		}
    		$response = array(
                'status' => true,
                'error_message' => $error_message
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

    public function deleteUser(Request $request)
    {
    	try {
    		$emp = $request->get('employee_id');

    		$users = OculusUser::where('employee_id',$emp)->first();
    		$users->delete();
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
}
