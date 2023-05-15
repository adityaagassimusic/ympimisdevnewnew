<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use App\SkillEmployee;
use App\Skill;
use App\SkillMap;
use App\SkillValue;
use App\EmployeeSync;
use App\SkillMutationLog;
use App\SkillUnfulfilledLog;
use App\UserActivityLog;
use App\SkillMapEvaluation;
use File;
use PDF;

class SkillMapController extends Controller
{
    public function __construct()
    {
      	$this->middleware('auth');
      	if (isset($_SERVER['HTTP_USER_AGENT']))
        {
            $http_user_agent = $_SERVER['HTTP_USER_AGENT']; 
            if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) 
            {
                die();
            }
        }

        $this->location = ['pn-assy-initial',
                      'pn-assy-final'];
    }

    public function indexSkillMap($location)
    {
    	$employee_skill = SkillEmployee::where('location',$location)->orderBy('process','desc')->get();
    	$employees = EmployeeSync::orderBy('name', 'asc')->get();
    	$process = DB::SELECT("SELECT DISTINCT(process) FROM `skills` where location = '".$location."' and skills.deleted_at is null order by process ");

    	if ($location == 'pn-assy-initial') {
    		$dept = 'Educational Instrument (EI) Department';
    		$section = 'Pianica';
    		$title = 'Skill Map - Pianica Assembly Initial';
            $subtitle = 'Pianica Assembly Initial';
    		$title_jp = 'のスキルマップ ~ ピアニカ集成';
    	}else if ($location == 'pn-assy-final') {
            $dept = 'Educational Instrument (EI) Department';
            $section = 'Pianica';
            $title = 'Skill Map - Pianica Assembly Final';
            $subtitle = 'Pianica Assembly Final';
            $title_jp = 'のスキルマップ ~ ピアニカ集成';
        }else if ($location == 'maintenance-mp') {
            $dept = 'Maintenance Department';
            $section = 'Maintenance MP';
            $title = 'Skill Map - Maintenance MP';
            $subtitle = 'Maintenance MP';
            $title_jp = 'のスキルマップ ~ メンテナン';
        }else if ($location == 'maintenance-ut') {
            $dept = 'Maintenance Department';
            $section = 'Maintenance UT';
            $title = 'Skill Map - Maintenance UT';
            $subtitle = 'Maintenance UT';
            $title_jp = 'のスキルマップ ~ メンテナン';
        }

    	return view('skill_map.index', array(
			'title' => $title,
            'subtitle' => $subtitle,
			'title_jp' => $title_jp,
			'location' => $location,
			'process' => $process,
			'dept' => $dept,
			'section' => $section,
			'employee_skill' => $employee_skill,
			'employees' => $employees
		))->with('page', 'Skill Map');
    }

    public function fetchSkillMap(Request $request)
    {
    	try {
    		$location = $request->get('location');
            $addProcess = "";
            if($request->get('process') != null) {
                $processs = explode(",", $request->get('process'));
                $process = "";

                for($x = 0; $x < count($processs); $x++) {
                    $process = $process."'".$processs[$x]."'";
                    if($x != count($processs)-1){
                        $process = $process.",";
                    }
                }
                $addProcess = "and process in (".$process.") ";
            }
    		$process = DB::SELECT("SELECT DISTINCT(process) FROM `skills` where location = '".$location."' ".$addProcess." and skills.deleted_at is null order by process ");

    		$emp = [];
    		$skill_map = [];
            $skill_required = [];
    		foreach ($process as $key) {
    			$emp[] = DB::SELECT("SELECT skill_employees.employee_id,name,skill_employees.process,skill_employees.location from skill_employees left join employee_syncs on skill_employees.employee_id = employee_syncs.employee_id where process = '".$key->process."' and location = '".$location."' and skill_employees.deleted_at is null");                

    			for ($i=0; $i < count($emp); $i++) { 
    				for ($j=0; $j < count($emp[$i]); $j++) { 
	    				$skill_map[$i][$j] = DB::SELECT("SELECT
							skill_maps.employee_id,
							skill_maps.skill_code,
							skills.skill,
							skill_maps.value AS nilai,
							skills.value AS nilai_tetap
						FROM
							skill_maps
							LEFT JOIN skills ON skills.skill_code = skill_maps.skill_code 
						WHERE
                            employee_id = '".$emp[$i][$j]->employee_id."' 
                            AND skill_maps.process = '".$emp[$i][$j]->process."' 
                            AND skills.location = '".$location."' 
							and skill_maps.deleted_at is null
							and skills.deleted_at is null");

                        $skill_required[$i][$j] = DB::SELECT("select skill_code,skill,process,location,value as nilai,(select skill from skill_maps where skill_code = skills.skill_code and employee_id = '".$emp[$i][$j]->employee_id."' and skill_maps.deleted_at is null) as skill_now,(select value from skill_maps where skill_code = skills.skill_code and employee_id = '".$emp[$i][$j]->employee_id."' and skill_maps.deleted_at is null) as nilai_now,(select id from skill_maps where skill_code = skills.skill_code and employee_id = '".$emp[$i][$j]->employee_id."' and skill_maps.deleted_at is null) as id_skill_now from skills where skills.location = '".$location."' and skills.process = '".$emp[$i][$j]->process."' and skills.deleted_at is null");
    				}
    			}
    		}


    		$response = array(
				'status' => true,
				'process' => $process,
				'emp' => $emp,
				'skill_map' => $skill_map,
                'skill_required' => $skill_required,
				'message' => 'Get Data Success.',
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

    public function fetchSkillMapDetail(Request $request)
    {
    	try {
    		$employee_id = $request->get('employee_id');
    		$location = $request->get('location');
    		$process = $request->get('process');
    		$skill_map = DB::SELECT("SELECT
				skill_maps.employee_id,
				employee_syncs.name,
				skill_maps.skill_code,
				skills.skill,
				skill_maps.value AS nilai,
				skills.value AS nilai_tetap,
				skill_maps.process,
                (select description from skill_values where location = skill_maps.location and skill_maps.value = skill_values.value and skill_values.deleted_at is null) as description
			FROM
				skill_maps
				LEFT JOIN skills ON skills.skill_code = skill_maps.skill_code
				LEFT JOIN employee_syncs ON skill_maps.employee_id = employee_syncs.employee_id
			WHERE
				skill_maps.employee_id = '".$employee_id."'
                AND skill_maps.process = '".$process."'
				AND skills.location = '".$location."'
				and skill_maps.deleted_at is null
				and skills.deleted_at is null");

    		$skill_required = DB::SELECT("SELECT
                skill_code,
                skill,
                process,
                location,
                
            VALUE
                AS nilai,(
                SELECT
                    skill_code 
                FROM
                    skill_maps 
                WHERE
                    skill_code = skills.skill_code 
                    AND employee_id = '".$employee_id."' 
                    AND skill_maps.deleted_at IS NULL 
                    AND skill_maps.location = '".$location."' 
                    AND skill_maps.process = '".$process."' 
                    ) AS skill_now,(
                SELECT 
                VALUE
                    
                FROM
                    skill_maps 
                WHERE
                    skill_code = skills.skill_code 
                    AND employee_id = '".$employee_id."' 
                    AND skill_maps.deleted_at IS NULL 
                    AND skill_maps.location = '".$location."' 
                    AND skill_maps.process = '".$process."' 
                    ) AS nilai_now,(
                SELECT
                    id 
                FROM
                    skill_maps 
                WHERE
                    skill_code = skills.skill_code 
                    AND employee_id = '".$employee_id."' 
                    AND skill_maps.deleted_at IS NULL 
                    AND skill_maps.location = '".$location."' 
                    AND skill_maps.process = '".$process."' 
                ) AS id_skill_now 
            FROM
                skills 
            WHERE
                skills.location = '".$location."' 
                AND skills.process = '".$process."' 
                AND skills.deleted_at IS NULL");

    		$other_skill = DB::SELECT("SELECT
                skill_code,
                skill,
                process,
                location,
                
            VALUE
                AS nilai,(
                SELECT
                    skill_code 
                FROM
                    skill_maps 
                WHERE
                    skill_code = skills.skill_code 
                    AND employee_id = '".$employee_id."' 
                    AND skill_maps.deleted_at IS NULL 
                    AND skill_maps.location = '".$location."' 
                    AND skills.process != '".$process."' 
                    ) AS skill_now,(
                SELECT 
                VALUE
                    
                FROM
                    skill_maps 
                WHERE
                    skill_code = skills.skill_code 
                    AND employee_id = '".$employee_id."' 
                    AND skill_maps.deleted_at IS NULL 
                    AND skill_maps.location = '".$location."' 
                    AND skills.process != '".$process."' 
                    ) AS nilai_now,(
                SELECT
                    id 
                FROM
                    skill_maps 
                WHERE
                    skill_code = skills.skill_code 
                    AND employee_id = '".$employee_id."' 
                    AND skill_maps.deleted_at IS NULL 
                    AND skill_maps.location = '".$location."' 
                    AND skills.process != '".$process."' 
                ) AS id_skill_now 
            FROM
                skills 
            WHERE
                skills.location = '".$location."' 
                AND skills.process != '".$process."' 
                AND skills.deleted_at IS NULL");

    		$response = array(
				'status' => true,
				'skill_map' => $skill_map,
				'skill_required' => $skill_required,
				'other_skill' => $other_skill,
				'message' => 'Get Data Success.',
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

    public function inputSkillAdjustment(Request $request)
    {
    	try {
    		$id_user = Auth::id();
    		$employee_id = $request->get('employee_id');
    		$process = $request->get('process');
    		$location = $request->get('location');
    		$skill = $request->get('skill');
    		$count = $request->get('count');
    		$skill_other = $request->get('skill_other');
    		$count_other = $request->get('count_other');

    		$skillmapss = [];
    		$skill_codes = [];


    		if (count($count) >0) {
                // var_dump($skill);
    			for ($t = 0;$t<count($skill);$t++) {
                    // var_dump($location);
                    // var_dump($process);
    				$skills = Skill::where('location',$location)->where('process',$process)->where('skill_code',$skill[$t])->first();
                    // var_dump($skills);
                    if ($skills == null) {
                        $skills = Skill::where('location',$location)->where('process',$process)->where('skill',$skill[$t])->first();
                        $skill_codes[] = $skills->skill_code;
                    }else{
                        $skill_codes[] = $skills->skill_code;
                    }
    			}

    			$skill_map = SkillMap::select('*','skill_maps.id as id_skill_map')->join('skills','skills.skill_code','skill_maps.skill_code')->where('employee_id',$employee_id)->where('skill_maps.location',$location)->where('skill_maps.process',$process)->where('skill_maps.deleted_at',null)->get();
    			if (count($skill_map) > 0) {
    				foreach ($skill_map as $key) {
	    				$skillmapss[] = $key->skill_code;
	    			}
	    			for ($u=0; $u < count($skill_codes); $u++) { 
	    				if (in_array($skill_codes[$u], $skillmapss)) {
	    					for ($k=0;$k<count($skill_map);$k++) {
			    				if ($skill_codes[$u] == $skill_map[$k]->skill_code) {
			    					$id_skill_map = $skill_map[$k]->id_skill_map;
			    				}
			    			}
	    					$skillAdjust = SkillMap::find($id_skill_map);
	    					$skillAdjust->value = $count[$u];
	    					$skillAdjust->save();
	    				}else{
	    					SkillMap::create([
					            'employee_id' => $employee_id,
					            'skill_code' => $skill_codes[$u],
					            'process' => $process,
					            'location' => $location,
					            'value' => $count[$u],				            
					            'created_by' => $id_user
					        ]);
	    				}
	    			}
    			}else{
    				for ($y=0; $y < count($skill_codes); $y++) { 
    					SkillMap::create([
				            'employee_id' => $employee_id,
				            'skill_code' => $skill_codes[$y],
				            'process' => $process,
				            'location' => $location,
				            'value' => $count[$y],				            
				            'created_by' => $id_user
				        ]);
	    			}
    			}
    		}

    		$skill_map2 = SkillMap::select('*','skill_maps.id as id_skill_map')->join('skills','skills.skill_code','skill_maps.skill_code')->where('employee_id',$employee_id)->where('skill_maps.location',$location)->where('skill_maps.process',$process)->where('skill_maps.deleted_at',null)->get();

    		$index = 0;
    		$skill_existing = [];

    		if (count($skill_other) > 0) {
    			foreach ($skill_map2 as $key) {
	    			if (in_array($key->skill, $skill_other)) {
	    				if ($key->skill != $skill) {
	    					$index = array_search($key->skill, $skill_other);
		    				$skillAdjust = SkillMap::find($key->id_skill_map);
							$skillAdjust->value = $count_other[$index];
							$skillAdjust->save();
	    				}
	    			}
	    			$skill_existing[] = $key->skill;
	    		}

	    		for ($i = 0; $i<count($skill_other);$i++) {
	    			if (in_array($skill_other[$i], $skill_existing)) {
	    				
	    			}else{
	    				$skills2 = Skill::where('location',$location)->where('skill',$skill_other[$i])->first();
	    				$skill_code2 = $skills2->skill_code;
	    				SkillMap::create([
				            'employee_id' => $employee_id,
				            'skill_code' => $skill_code2,
				            'process' => $process,
				            'location' => $location,
				            'value' => $count_other[$i],
				            'created_by' => $id_user
				        ]);
	    			}
	    		}
    		}

    		$response = array(
				'status' => true,
				'message' => 'Save Data Success.',
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

    public function destroySkillMaps(Request $request)
    {
    	try {
    		$skill = SkillMap::find($request->get('id_skill'));
    		$skill->delete();

    		$response = array(
				'status' => true,
				'message' => 'Delete Skill Success.',
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

    public function fetchSkillMaster(Request $request)
    {
    	try {
    		$skill = Skill::where('location',$request->get('location'))->get();
    		$process = Skill::select('process')->distinct()->where('location',$request->get('location'))->get();

    		$response = array(
				'status' => true,
				'skill' => $skill,
				'process' => $process,
				'message' => 'Get Skill Success.',
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

    public function inputSkillMaster(Request $request)
    {
    	try {
    		$id_user = Auth::id();
    		if ($request->get('condition') == 'INPUT') {
    			Skill::create([
		            'skill_code' => strtoupper($request->get('skill_code')),
		            'skill' => $request->get('skill'),
		            'process' => $request->get('process'),
		            'location' => $request->get('location'),
		            'value' => $request->get('value'),
		            'created_by' => $id_user
		        ]);
    		}else{
    			$skill = Skill::find($request->get('id_skill'));
    			$skill->skill_code = strtoupper($request->get('skill_code'));
    			$skill->skill = $request->get('skill');
    			$skill->process = $request->get('process');
    			$skill->location = $request->get('location');
    			$skill->value = $request->get('value');
    			$skill->save();
    		}

    		$response = array(
				'status' => true,
				'message' => 'Save Skill Success.',
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

    public function destroySkillMaster(Request $request)
    {
    	try {
    		$id_user = Auth::id();
    		$skill_map = SkillMap::select('skill_code')->where('location',$request->get('location'))->get();

    		$skills = [];
    		foreach ($skill_map as $key) {
    			$skills[] = $key->skill_code;
    		}

    		$skill = Skill::find($request->get('id'));
			if (in_array($skill->skill_code, $skills)) {
				$status = false;
			}else{
				$status = true;
			}

    		if ($status) {
    			$skill->delete();
    			$response = array(
					'status' => true,
					'message' => 'Delete Skill Success',
				);
				return Response::json($response);
    		}else{
    			$response = array(
					'status' => false,
					'message' => 'Data Terpakai di Skill Map',
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

    public function getSkillMaster(Request $request)
    {
    	try {
    		$skill = Skill::find($request->get('id'));

    		$response = array(
				'status' => true,
				'skill' => $skill,
				'message' => 'Get Skill Success.',
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

    public function fetchSkillEmployee(Request $request)
    {
    	try {
    		$skill_employee = SkillEmployee::join('employee_syncs','skill_employees.employee_id','employee_syncs.employee_id')->where('location',$request->get('location'))->get();
    		$process = Skill::select('process')->distinct()->where('location',$request->get('location'))->get();
    		$employees = EmployeeSync::where('end_date','=',null)->get();

    		$response = array(
				'status' => true,
				'employee' => $skill_employee,
				'employees' => $employees,
				'process' => $process,
				'message' => 'Get Employees Success.',
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

    public function inputSkillEmployee(Request $request)
    {
    	try {
    		$id_user = Auth::id();
    		if ($request->get('condition') == 'INPUT') {
    			$employee = SkillEmployee::where('employee_id',$request->get('employee_id'))->first();
    			if (count($employee) > 0) {
    				$status = false;
    				$message = 'Employee is exist';
    			}
    			else{
    				SkillEmployee::create([
			            'employee_id' => $request->get('employee_id'),
			            'process' => $request->get('process'),
			            'location' => $request->get('location'),
			            'created_by' => $id_user
			        ]);

			        $status = true;
    				$message = 'Add Employee Success';
    			}
                $count_failed = 0;
    		}else{
                $skill_current = [];

    			$skills = SkillMap::where('employee_id',$request->get('employee_id'))->where('location',$request->get('location'))->get();
    			if (count($skills) > 0) {
    				foreach ($skills as $key) {
                        $skill2 = SkillMap::find($key->id);
                        $skill_current[] = $skill2->skill_code;
    				}
    			}

                $count_failed = 0;
                $skill_failed = [];
                $count_failed2 = 0;
                $skill_failed2 = [];

                $skill = Skill::where('location',$request->get('location'))->where('process',$request->get('process'))->get();

                foreach ($skill as $key) {
                    if (in_array($key->skill_code, $skill_current)) {
                        
                    }else{
                        $count_failed++;
                        $skill_failed[] = $key->skill;
                    }
                }

                if ($count_failed == 0) {

                    $skills_now = DB::SELECT("select *,skills.value as required,skill_maps.value as current from skill_maps join skills on skills.skill_code = skill_maps.skill_code where employee_id = '".$request->get('employee_id')."' and skill_maps.location  = '".$request->get('location')."' and skills.process = '".$request->get('process')."' and skill_maps.deleted_at is null and skills.deleted_at is null");

                    foreach ($skills_now as $val) {
                        if ($val->current < 3) {
                            $count_failed2++;
                            $skill_failed2[] = $val->skill;
                        }
                    }

                    if ($count_failed2 > 0) {
                        $status = false;
                        $message = 'Karyawan ini memiliki nilai skill yang kurang dari nilai standar.<br>Skill yang belum sesuai adalah <br><br>'.join(", ",$skill_failed2).'. <br><br>Lakukan Upgrade Skill segera.';
                    }else{
                        $employee = SkillEmployee::find($request->get('id_employee'));
                        $process_from = $employee->process;
                        $employee->process = $request->get('process');
                        $employee->location = $request->get('location');
                        $employee->save();

                        if (count($skills) > 0) {
                            foreach ($skills as $key) {
                                $skill3 = SkillMap::find($key->id);
                                $skill3->process = $request->get('process');
                                $skill3->save();
                            }
                        }

                        $remark = 'Sudah Memenuhi';

                        SkillMutationLog::create([
                            'employee_id' => $request->get('employee_id'),
                            'process_from' => $process_from,
                            'process_to' => $request->get('process'),
                            'location' => $request->get('location'),
                            'remark' => $remark,
                            'created_by' => $id_user
                        ]);

                        $status = true;
                        $message = 'Update Employee Success';
                    }
                }else{
                    $status = false;
                    $message = 'Karyawan tidak memiliki skill yang sesuai dengan posisi yang dituju.<br>Skill yang belum sesuai adalah <br><br>'.join(", ",$skill_failed).'. <br><br>Lakukan Upgrade Skill segera.';
                }
    		}

    		$response = array(
				'status' => $status,
				'message' => $message,
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

    public function destroySkillEmployee(Request $request)
    {
    	try {
    		$id_user = Auth::id();
    		$skill_map = SkillMap::select('employee_id')->where('location',$request->get('location'))->get();

    		$employee_id = [];
    		foreach ($skill_map as $key) {
    			$employee_id[] = $key->employee_id;
    		}

    		$employee = SkillEmployee::find($request->get('id'));
			if (in_array($employee->employee_id, $employee_id)) {
				$status = false;
			}else{
				$status = true;
			}

    		if ($status) {
    			$employee->delete();
    			$response = array(
					'status' => true,
					'message' => 'Delete Employee Success',
				);
				return Response::json($response);
    		}else{
    			$response = array(
					'status' => false,
					'message' => 'Data Terpakai di Skill Map',
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

    public function getSkillEmployee(Request $request)
    {
    	try {
    		$employee = SkillEmployee::find($request->get('id'));

    		$response = array(
				'status' => true,
				'employee' => $employee,
				'message' => 'Get Employee Success.',
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

    public function fetchSkillValue(Request $request)
    {
        try {
            $skill_value = SkillValue::where('location',$request->get('location'))->get();

            $response = array(
                'status' => true,
                'skill_value' => $skill_value,
                'message' => 'Get Skill Value Success.',
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

    public function inputSkillValue(Request $request)
    {
        try {
            $id_user = Auth::id();
            if ($request->get('condition_value') == 'INPUT') {
                SkillValue::create([
                    'value' => $request->get('value'),
                    'description' => $request->get('description'),
                    'location' => $request->get('location'),
                    'created_by' => $id_user
                ]);
            }else{
                $skill = SkillValue::find($request->get('id_value'));
                $skill->value = $request->get('value');
                $skill->description = $request->get('description');
                $skill->location = $request->get('location');
                $skill->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Save Skill Value Success.',
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

    public function destroySkillValue(Request $request)
    {
        try {
            $id_user = Auth::id();

            $skill = SkillValue::find($request->get('id'));
            $skill->delete();

            $response = array(
                'status' => true,
                'message' => 'Delete Skill Value Success',
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

    public function getSkillValue(Request $request)
    {
        try {
            $skill = SkillValue::find($request->get('id'));

            $response = array(
                'status' => true,
                'skill' => $skill,
                'message' => 'Get Skill Value Success.',
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

    public function fetchSkillResume(Request $request)
    {
        try {
            $resume = DB::SELECT("SELECT DISTINCT(skill),skill_code,
                (select count(employee_id) from skill_maps where skill_maps.value >= 3 and skill_code = skills.skill_code and skills.location = '".$request->get('location')."') as jumlah_lebih_tiga,
                (select count(employee_id) from skill_maps where skill_maps.value = 1 and skill_code = skills.skill_code and skills.location = '".$request->get('location')."') as jumlah_satu,
                (select count(employee_id) from skill_maps where skill_maps.value >= 2 and skill_code = skills.skill_code and skills.location = '".$request->get('location')."') as jumlah_dua,
                (select count(employee_id) from skill_maps where skill_maps.value >= 3 and skill_code = skills.skill_code and skills.location = '".$request->get('location')."') as jumlah_tiga,
                (select count(employee_id) from skill_maps where skill_maps.value >= 4 and skill_code = skills.skill_code and skills.location = '".$request->get('location')."') as jumlah_empat,
                COALESCE(((select count(employee_id) from skill_maps where skill_maps.value >= 3 and skill_code = skills.skill_code and skills.location = '".$request->get('location')."')/(select count(DISTINCT(employee_id)) from skill_maps where skill_maps.location = '".$request->get('location')."')*100),0) as persen_lebih_tiga,
                (select count(DISTINCT(employee_id)) from skill_maps where skill_maps.location = '".$request->get('location')."') as jumlah_orang,
                (select ROUND(AVG(value),1) from skill_maps where skill_code = skills.skill_code and skills.location = '".$request->get('location')."') as average
                FROM skills where location = '".$request->get('location')."'");

            $unfulfilled = DB::SELECT("SELECT
                skill_unfulfilled_logs.employee_id,employee_syncs.name,skill_unfulfilled_logs.process,skill_unfulfilled_logs.skill_code,skills.skill,skill_unfulfilled_logs.value,skill_unfulfilled_logs.required,skill_unfulfilled_logs.remark as unfulfilled_remark
            FROM
                skill_unfulfilled_logs
                LEFT JOIN employee_syncs ON employee_syncs.employee_id = skill_unfulfilled_logs.employee_id
                LEFT JOIN skills ON skills.skill_code = skill_unfulfilled_logs.skill_code 
            WHERE
                skill_unfulfilled_logs.location = '".$request->get('location')."' 
                AND date( skill_unfulfilled_logs.created_at ) = DATE(
                NOW()) 
                AND skill_unfulfilled_logs.deleted_at IS NULL");

            $mutation = DB::SELECT("SELECT *,skill_mutation_logs.remark as mutation_remark ,skill_mutation_logs.created_at as mutation_created_at,employee_syncs.name as name,users.name as adjusted_by FROM skill_mutation_logs left join employee_syncs on employee_syncs.employee_id = skill_mutation_logs.employee_id left join users on users.id = skill_mutation_logs.created_by where location = '".$request->get('location')."'");

            $response = array(
                'status' => true,
                'resume' => $resume,
                'unfulfilled' => $unfulfilled,
                'mutation' => $mutation,
                'message' => 'Get Skill Success.',
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

    public function fetchSkillResumeOperator(Request $request)
    {
        try {
            $resumes = DB::SELECT("SELECT
                skill_maps.employee_id,
                skill_maps.skill_code,
                skill_maps.process,
                skills.skill,
                skill_maps.`value`,
                skills.`value` AS required,
                employee_syncs.`name` 
            FROM
                skill_maps
                LEFT JOIN employee_syncs ON employee_syncs.employee_id = skill_maps.employee_id
                LEFT JOIN skills ON skills.skill_code = skill_maps.skill_code 
            WHERE
                skill_maps.location = '".$request->get('location')."' 
                AND skills.location = '".$request->get('location')."'");

            $emp = DB::SELECT("SELECT
                skill_employees.employee_id,
                employee_syncs.`name` 
            FROM
                skill_employees
                LEFT JOIN employee_syncs ON employee_syncs.employee_id = skill_employees.employee_id 
            WHERE
                location = '".$request->get('location')."'");

            $skills = DB::SELECT("SELECT
                skill_code,
                skill 
            FROM
                skills 
            WHERE
                location = 'maintenance-ut'");
            $response = array(
                'status' => true,
                'resumes' => $resumes,
                'skills' => $skills,
                'emp' => $emp,
                'message' => 'Get Skill Success.',
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

    public function inputSkillEvaluation(Request $request)
    {
        try {
            $location = $request->get('location');
            $employee_id = $request->get('employee_id');
            $name = $request->get('name');
            $process = $request->get('process');
            $skill_code = $request->get('skill_code');
            $from_value = $request->get('from_value');
            $to_value = $request->get('to_value');
            $evaluation_point = $request->get('evaluation_point');
            $evaluation_value = $request->get('evaluation_value');

            $evaluation_code = MD5($location.'-'.$employee_id.'-'.$name);

            $id_user = Auth::id();

            for($i = 0; $i < count($evaluation_point); $i++){
                SkillMapEvaluation::create([
                    'evaluation_code' => $evaluation_code,
                    'employee_id' => $employee_id,
                    'skill_code' => $skill_code,
                    'process' => $process,
                    'location' => $location,
                    'from_value' => $from_value,
                    'to_value' => $to_value,
                    'evaluation_point' => $evaluation_point[$i],
                    'evaluation_value' => $evaluation_value[$i],
                    'created_by' => $id_user
                ]);
            }

            $response = array(
                'status' => true,
                'message' => 'Success Input Evaluasi',
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

    public function reportSkillMapEvaluation($location)
    {
        if ($location == 'pn-assy-initial') {
            $dept = 'Educational Instrument (EI)';
            $section = 'Pianica';
            $title = 'Report Skill Map Evaluation - Pianica Assembly Initial';
            $subtitle = 'Pianica Assembly Initial';
            $title_jp = 'のスキルマップリポート ~ ピアニカ集成';
        }else if ($location == 'pn-assy-final') {
            $dept = 'Educational Instrument (EI)';
            $section = 'Pianica';
            $title = 'Report Skill Map Evaluation - Pianica Assembly Final';
            $subtitle = 'Pianica Assembly Final';
            $title_jp = 'のスキルマップリポート ~ ピアニカ集成';
        }

        return view('skill_map.report_evaluation', array(
            'title' => $title,
            'subtitle' => $subtitle,
            'title_jp' => $title_jp,
            'location' => $location,
            'dept' => $dept,
            'section' => $section
        ))->with('page', 'Report Skill Map Evaluation');
    }

    public function fetchReportSkillMapEvaluation(Request $request)
    {
        try {
            $evaluation = DB::SELECT("SELECT DISTINCT
                ( a.evaluation_code ),
                a.employee_id,
                employee_syncs.name,
                a.skill_code,
                skills.skill,
                a.process,
                a.location,
                from_value,
                to_value,(
                SELECT
                    ROUND( AVG( evaluation_value ), 1 ) 
                FROM
                    skill_map_evaluations 
                WHERE
                    skill_map_evaluations.evaluation_code = a.evaluation_code 
                ) AS average 
            FROM
                `skill_map_evaluations` a
                JOIN employee_syncs ON employee_syncs.employee_id = a.employee_id
                JOIN skills ON skills.skill_code = a.skill_code 
            WHERE
                a.location = '".$request->get('location')."'");

            $response = array(
                'status' => true,
                'message' => 'Success Get Data',
                'datas' => $evaluation,
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

    public function printSkillMapEvaluation($location,$evaluation_code)
    {

        if ($evaluation_code != "") {
            $detail_evaluation = DB::SELECT("SELECT
                a.evaluation_code,
                a.employee_id,
                employee_syncs.name,
                a.skill_code,
                skills.skill,
                a.process,
                a.location,
                from_value,
                to_value,
                a.created_at,
                a.evaluation_value,
                (
                SELECT
                    ROUND( AVG( evaluation_value ), 1 ) 
                FROM
                    skill_map_evaluations 
                WHERE
                    skill_map_evaluations.evaluation_code = a.evaluation_code 
                ) AS average 
            FROM
                `skill_map_evaluations` a
                JOIN employee_syncs ON employee_syncs.employee_id = a.employee_id
                JOIN skills ON skills.skill_code = a.skill_code 
            WHERE
                a.location = '".$location."'
            AND
                a.evaluation_code = '".$evaluation_code."'");
        }else{

        }

        $evcode = [];
        $detail = [];

        if (count($detail_evaluation) > 0) {
            for($i = 0; $i< count($detail_evaluation);$i++){
                $evcode[] = $detail_evaluation[$i]->evaluation_value;
            }

            $detail = array(
                'evaluation_code' => $detail_evaluation[0]->evaluation_code,
                'employee_id' => $detail_evaluation[0]->employee_id,
                'name' => $detail_evaluation[0]->name,
                'skill_code' => $detail_evaluation[0]->skill_code,
                'process' => $detail_evaluation[0]->process,
                'evaluation_value' => $evcode,
                'average' => $detail_evaluation[0]->average,
                'created_at' => $detail_evaluation[0]->created_at,
            );
        }else{

        }

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'landscape');

        $pdf->loadView('skill_map.print_evaluation', array(
            'detail' => $detail,
            'location' => $location
        ));


        return $pdf->stream("Skill Map Evaluation.pdf");
    }
}
