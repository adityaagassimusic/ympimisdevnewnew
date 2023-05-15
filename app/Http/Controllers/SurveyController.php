<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\WeeklyCalendar;
use App\EmployeeSync;
use App\Department;
use Response;

class SurveyController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
	}

	public function indexSurvey()
	{
		$title = 'Emergency Survey';
		$title_jp = 'エマージェンシーサーベイ';

		return view('survey.index', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Emergency Survey')->with('head','Emergency Survey');
	}

	public function fetchSurvey(Request $request)
	{
		try {
			$token = app(MiraiMobileController::class)->ympicoid_api_login();

			if ($request->get('keterangan') == null) {
				$keterangan = "Kuisioner Compliance";
			}else{
				$keterangan = $request->get('keterangan');
			}

			$param = '';
			$link = 'fetch/emergency';
			$method = 'GET';
			//Contoh $param = 'date_from='.$date_from.'&date_to='.$date_to;

			$datas = app(MiraiMobileController::class)->ympicoid_api($token,$link,$method,$param);

			$emp = EmployeeSync::select('employee_id','name','department')->where('end_date',null)->where('grade_code','!=','J0-')->get();
			$dept = Department::get();
			$keterangan_all = [];

			if (count($datas) > 0 ) {
				for ($j=0; $j < count($datas->emergency); $j++) {
					array_push($keterangan_all, $datas->emergency[$j]->keterangan);
				}
			}

			// $survey = DB::SELECT("SELECT
			// 	SUM( a.count_tidak ) AS tidak,
			// 	sum( a.count_all ) - (
			// 		SUM( a.count_tidak ) + SUM( a.count_iya )) AS belum,
			// 	sum( a.count_iya ) AS iya,
			// 	a.department,
			// 	department_shortname
			// 	FROM
			// 	(
			// 		SELECT
			// 		0 AS count_tidak,
			// 		count( employee_syncs.employee_id ) AS count_all,
			// 		0 AS count_iya,
			// 		COALESCE ( employee_syncs.department, '' ) AS department 
			// 		FROM
			// 		employee_syncs 
			// 		WHERE
			// 		employee_syncs.end_date IS NULL 
			// 		AND employee_syncs.employee_id not in ('PI2111045','PI1612005')
			// 		GROUP BY
			// 		department UNION ALL
			// 		SELECT
			// 		count( miraimobile.emergency_surveys.employee_id ) AS count_tidak,
			// 		0 AS count_all,
			// 		0 AS count_iya,
			// 		COALESCE ( employee_syncs.department, '' ) AS department 
			// 		FROM
			// 		employee_syncs
			// 		LEFT JOIN miraimobile.emergency_surveys ON miraimobile.emergency_surveys.employee_id = employee_syncs.employee_id 
			// 		WHERE
			// 		employee_syncs.end_date IS NULL 
			// 		AND jawaban = 'Tidak' 
			// 		AND miraimobile.emergency_surveys.keterangan = '".$keterangan."'
			// 		AND employee_syncs.employee_id not in ('PI2111045','PI1612005')  
			// 		GROUP BY
			// 		department UNION ALL
			// 		SELECT
			// 		0 AS count_tidak,
			// 		0 AS count_all,
			// 		count( miraimobile.emergency_surveys.employee_id ) AS count_iya,
			// 		COALESCE ( employee_syncs.department, '' ) AS department 
			// 		FROM
			// 		employee_syncs
			// 		LEFT JOIN miraimobile.emergency_surveys ON miraimobile.emergency_surveys.employee_id = employee_syncs.employee_id 
			// 		WHERE
			// 		employee_syncs.end_date IS NULL 
			// 		AND jawaban = 'Iya' 
			// 		AND miraimobile.emergency_surveys.keterangan = '".$keterangan."' 
			// 		AND employee_syncs.employee_id not in ('PI2111045','PI1612005')
			// 		GROUP BY
			// 		department 
			// 		) a
			// 		LEFT JOIN departments ON a.department = departments.department_name 
			// 		WHERE a.department != ''
			// 		GROUP BY
			// 		a.department,
			// 		departments.department_shortname");

			$response = array(
				'status' => true,
				'emp' => $emp,
				'dept' => $dept,
				'keterangan' => $keterangan,
				'keterangan_all' => $keterangan_all,
				'survey' => $datas,
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

	public function fetchSurveyDetail(Request $request)
	{
		try {
			$answer = $request->get('answer');
			$dept = $request->get('dept');
			if ($answer == 'No') {
				$answer = 'Tidak';
			}else if($answer == 'Yes'){
				$answer = 'Iya';
			}else{
				$answer = null;
			}

			if ($request->get('keterangan') == null) {
				$keterangan = "Emergency 4";
			}else{
				$keterangan = $request->get('keterangan');
			}

			if ($dept == "MNGT") {
				if ($answer == null) {
					$survey = DB::SELECT("
						SELECT
						employee_syncs.employee_id,
						employee_syncs.name,
						'' AS department,
						COALESCE ( jawaban, '' ) AS jawaban,
						COALESCE ( hubungan, '' ) AS hubungan,
						COALESCE ( nama, '' ) AS nama
						FROM
						employee_syncs
						LEFT JOIN miraimobile.emergency_surveys ON employee_syncs.employee_id = miraimobile.emergency_surveys.employee_id 
						and keterangan = '".$keterangan."'
						WHERE
						employee_syncs.end_date IS NULL 
						AND employee_syncs.department IS NULL 
						AND miraimobile.emergency_surveys.employee_id IS NULL
						AND employee_syncs.employee_id not in ('PI2111045','PI1612005')
						");
				}else{
					$survey = DB::SELECT("SELECT
						employee_syncs.employee_id,
						employee_syncs.name,
						'' AS department,
						COALESCE ( jawaban, '' ) AS jawaban,
						COALESCE ( hubungan, '' ) AS hubungan,
						COALESCE ( nama, '' ) AS nama
						FROM
						employee_syncs
						LEFT JOIN miraimobile.emergency_surveys ON employee_syncs.employee_id = miraimobile.emergency_surveys.employee_id 
						AND keterangan = '".$keterangan."' 
						WHERE
						employee_syncs.end_date IS NULL 
						AND employee_syncs.department IS NULL 
						AND employee_syncs.employee_id not in ('PI2111045','PI1612005')
						AND jawaban = '".$answer."'");
				}
			}else{
				if ($answer == null) {
					$survey = DB::SELECT("SELECT
						employee_syncs.employee_id,
						employee_syncs.name,
						'' AS department,
						COALESCE ( jawaban, '' ) AS jawaban,
						COALESCE ( hubungan, '' ) AS hubungan,
						COALESCE ( nama, '' ) AS nama
						FROM
						employee_syncs
						LEFT JOIN miraimobile.emergency_surveys ON employee_syncs.employee_id = miraimobile.emergency_surveys.employee_id 
						AND keterangan = '".$keterangan."'
						JOIN departments ON department_name = employee_syncs.department 
						WHERE
						employee_syncs.end_date IS NULL 
						AND miraimobile.emergency_surveys.employee_id IS NULL 
						AND employee_syncs.employee_id not in ('PI2111045','PI1612005')
						AND department_shortname = '".$dept."'");
				}else{
					$survey = DB::SELECT("SELECT
						employee_syncs.employee_id,
						employee_syncs.name,
						'' AS department,
						COALESCE ( jawaban, '' ) AS jawaban,
						COALESCE ( hubungan, '' ) AS hubungan,
						COALESCE ( nama, '' ) AS nama 
						FROM
						employee_syncs
						LEFT JOIN miraimobile.emergency_surveys ON employee_syncs.employee_id = miraimobile.emergency_surveys.employee_id 
						AND keterangan = '".$keterangan."'
						JOIN departments ON department_name = employee_syncs.department 
						WHERE
						employee_syncs.end_date IS NULL 
						AND employee_syncs.employee_id not in ('PI2111045','PI1612005')
						AND department_shortname = '".$dept."' 
						AND jawaban = '".$answer."'");
				}
			}

			$response = array(
				'status' => true,
				'survey' => $survey,
				'keterangan' => $keterangan,
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

	public function indexSurveyCovid()
	{
		$title = 'Survey Covid-19';
		$title_jp = 'コロナ調査';

		return view('survey.index_covid', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Survey Covid')->with('head','Survey Covid');
	}

	public function fetchSurveyCovid(Request $request)
	{
		try {
			$token = app(MiraiMobileController::class)->ympicoid_api_login();

			$param = 'tanggal='.$request->get('tanggal');
			$link = 'fetch/survey_covid/chart';
			$link2 = 'fetch/survey_covid/chart/nilai';
			$method = 'POST';
				//Contoh $param = 'date_from='.$date_from.'&date_to='.$date_to;

			$survey = app(MiraiMobileController::class)->ympicoid_api($token,$link,$method,$param);
			$nilai = app(MiraiMobileController::class)->ympicoid_api($token,$link2,$method,$param);

				// if ($request->get('tanggal') == "") {
				// 	$survey = DB::SELECT("
				// 		SELECT
				// 		SUM( a.count_sudah ) AS sudah,
				// 		SUM( a.count_belum ) AS belum,
				// 		a.department,
				// 		department_shortname
				// 		FROM
				// 		(
				// 			SELECT
				// 			count( miraimobile.survey_logs.employee_id ) AS count_sudah,
				// 			0 AS count_belum,
				// 			COALESCE (employee_syncs.department, '' ) AS department 
				// 			FROM
				// 			miraimobile.survey_logs
				// 			JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id 
				// 			WHERE employee_syncs.employee_id != 'PI1612005'
				// 			GROUP BY
				// 			employee_syncs.department

				// 			UNION ALL
				// 			SELECT
				// 			0 AS count_sudah,
				// 			count( employee_syncs.employee_id ) AS count_belum,
				// 			COALESCE ( employee_syncs.department, '' ) AS department 
				// 			FROM
				// 			miraimobile.survey_logs
				// 			RIGHT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id 
				// 			WHERE
				// 			miraimobile.survey_logs.employee_id IS NULL 
				// 			AND employee_syncs.end_date IS NULL 
				// 			AND employee_syncs.employee_id != 'PI1612005'
				// 			GROUP BY
				// 			employee_syncs.department 
				// 			) a
				// 		LEFT JOIN departments ON a.department = departments.department_name 
				// 		WHERE a.department != ''
				// 		GROUP BY
				// 		a.department,
				// 		departments.department_shortname
				// 		");
				// }
				// else{

				// 	$date = date('Y-m-d', strtotime($request->get("tanggal")));

				// 	$survey = DB::SELECT("
				// 		SELECT
				// 		SUM( a.count_sudah ) AS sudah,
				// 		SUM( a.count_belum ) AS belum,
				// 		a.department,
				// 		department_shortname
				// 		FROM
				// 		(
				// 			SELECT
				// 			count( miraimobile.survey_covid_logs.employee_id ) AS count_sudah,
				// 			0 AS count_belum,
				// 			COALESCE (employee_syncs.department, '' ) AS department 
				// 			FROM
				// 			miraimobile.survey_covid_logs
				// 			JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_covid_logs.employee_id 
				// 			WHERE 
				// 			miraimobile.survey_covid_logs.tanggal = '".$date."' 
				// 			AND employee_syncs.employee_id != 'PI1612005'
				// 			GROUP BY
				// 			employee_syncs.department

				// 			UNION ALL

				// 			SELECT 
				// 			0 AS count_sudah,
				// 			SUM(IF(mobile.employee_id is null,1,0)) as count_belum,
				// 			COALESCE ( employee_syncs.department, '' ) AS department 
				// 			FROM 
				// 			(SELECT * from miraimobile.survey_covid_logs where tanggal = '".$date."') as mobile
				// 			RIGHT JOIN employee_syncs ON employee_syncs.employee_id = mobile.employee_id 
				// 			WHERE
				// 			employee_syncs.end_date IS NULL 
				// 			AND employee_syncs.employee_id != 'PI1612005'
				// 			GROUP BY employee_syncs.department
				// 			) a
				// 			LEFT JOIN departments ON a.department = departments.department_name 
				// 			WHERE a.department != ''
				// 			GROUP BY
				// 			a.department,
				// 			departments.department_shortname
				// 			");
				// 	}

					// if ($request->get('tanggal') == "") {

					// 	$nilai = DB::SELECT("
					// 		SELECT
					// 		sum(CASE WHEN miraimobile.survey_logs.total <= 35 THEN 1 ELSE 0 END) AS jumlah_rendah,
					// 		sum(CASE WHEN miraimobile.survey_logs.total > 35 AND miraimobile.survey_logs.total <= 80 THEN 1 ELSE 0 END) AS jumlah_sedang,
					// 		sum(CASE WHEN miraimobile.survey_logs.total > 80 THEN 1 ELSE 0 END) AS jumlah_tinggi
					// 		FROM
					// 		miraimobile.survey_logs
					// 		JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id 
					// 		WHERE
					// 		miraimobile.survey_logs.survey_code = 'covid' 
					// 		");
					// }
					// else{

					// 	$date = date('Y-m-d', strtotime($request->get("tanggal")));

					// 	$nilai = DB::SELECT("
					// 		SELECT
					// 		sum( CASE WHEN miraimobile.survey_covid_logs.total <= 35 THEN 1 ELSE 0 END ) AS jumlah_rendah,
					// 		sum( CASE WHEN miraimobile.survey_covid_logs.total > 35 AND miraimobile.survey_covid_logs.total <= 80 THEN 1 ELSE 0 END ) AS jumlah_sedang,
					// 		sum( CASE WHEN miraimobile.survey_covid_logs.total > 80 THEN 1 ELSE 0 END ) AS jumlah_tinggi 
					// 		FROM
					// 		miraimobile.survey_covid_logs
					// 		JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_covid_logs.employee_id 
					// 		WHERE
					// 		miraimobile.survey_covid_logs.tanggal = '".$date."'
					// 		");
					// }

			$response = array(
				'status' => true,
				'survey' => $survey,
				'nilai' => $nilai
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

	public function fetchSurveyCovidDetail(Request $request)
	{
		try {
			$answer = $request->get('answer');
			$dept = $request->get('dept');
			$category = $request->get('category');

			$token = app(MiraiMobileController::class)->ympicoid_api_login();

			$param = 'tanggal='.$request->get('tanggal').'&answer='.$answer.'&dept='.$dept;
			$link = 'fetch/survey_covid/chart/detail';
			$link2 = 'fetch/survey_covid/chart/detail_info';


			$param2 = 'tanggal='.$request->get('tanggal').'&category='.$category;
			$link3 = 'fetch/survey_covid/chart/detail_category';

			$method = 'POST';
					//Contoh $param = 'date_from='.$date_from.'&date_to='.$date_to;

			$survey = app(MiraiMobileController::class)->ympicoid_api($token,$link,$method,$param);
			$survey_info = app(MiraiMobileController::class)->ympicoid_api($token,$link2,$method,$param);
			$survey_category = app(MiraiMobileController::class)->ympicoid_api($token,$link3,$method,$param2);



					// if ($answer == "Belum") {
					// 	if ($request->get('tanggal') == "") {
					// 		$survey = DB::SELECT("SELECT
					// 			employee_syncs.employee_id,
					// 			employee_syncs.name,
					// 			COALESCE(department_shortname,'') as department
					// 			FROM
					// 			miraimobile.survey_logs
					// 			RIGHT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id
					// 			join departments on department_name = employee_syncs.department
					// 			WHERE
					// 			department_shortname = '".$dept."'
					// 			and miraimobile.survey_logs.employee_id is null
					// 			and employee_syncs.employee_id != 'PI1612005'
					// 			and employee_syncs.end_date is null");


					// 		$survey_info = DB::SELECT("SELECT
					// 			employee_syncs.employee_id,
					// 			employee_syncs.name,
					// 			COALESCE(department_shortname,'') as department
					// 			FROM
					// 			miraimobile.survey_logs
					// 			RIGHT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id
					// 			join departments on department_name = employee_syncs.department
					// 			WHERE
					// 			miraimobile.survey_logs.employee_id is null
					// 			and employee_syncs.employee_id != 'PI1612005'
					// 			and employee_syncs.end_date is null");
					// 	}
					// 	else{
					// 		$date = date('Y-m-d', strtotime($request->get("tanggal")));

					// 		$survey = DB::SELECT("SELECT
					// 			employee_syncs.employee_id,
					// 			employee_syncs.name,
					// 			COALESCE ( department_shortname, '' ) AS department 
					// 			FROM
					// 			( SELECT * FROM miraimobile.survey_covid_logs WHERE tanggal = '".$date."' ) AS mobile
					// 				RIGHT JOIN employee_syncs ON employee_syncs.employee_id = mobile.employee_id
					// 				JOIN departments ON department_name = employee_syncs.department 
					// 				WHERE
					// 				department_shortname = '".$dept."' 
					// 				AND mobile.employee_id IS NULL 
					// 				AND employee_syncs.employee_id != 'PI1612005'
					// 				AND employee_syncs.end_date IS NULL");

					// 			$survey_info = DB::SELECT("
					// 				SELECT
					// 				employee_syncs.employee_id,
					// 				employee_syncs.name,
					// 				COALESCE ( department_shortname, '' ) AS department 
					// 				FROM
					// 				( SELECT * FROM miraimobile.survey_covid_logs WHERE tanggal = '".$date."' ) AS mobile
					// 					RIGHT JOIN employee_syncs ON employee_syncs.employee_id = mobile.employee_id
					// 					JOIN departments ON department_name = employee_syncs.department 
					// 					WHERE
					// 					mobile.employee_id IS NULL 
					// 					AND employee_syncs.employee_id != 'PI1612005'
					// 					AND employee_syncs.end_date IS NULL
					// 					");
					// 			}


					// 		}else{
					// 			if ($request->get('tanggal') == "") {
					// 				$survey = DB::SELECT("SELECT
					// 					employee_syncs.employee_id,
					// 					employee_syncs.name,
					// 					COALESCE(department_shortname,'') as department
					// 					FROM
					// 					miraimobile.survey_logs
					// 					LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id
					// 					join departments on department_name = employee_syncs.department
					// 					WHERE
					// 					department_shortname = '".$dept."'
					// 					and employee_syncs.employee_id != 'PI1612005'
					// 					and employee_syncs.end_date is null");

					// 				$survey_info = DB::SELECT("SELECT
					// 					employee_syncs.employee_id,
					// 					employee_syncs.name,
					// 					COALESCE(department_shortname,'') as department
					// 					FROM
					// 					miraimobile.survey_logs
					// 					LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id
					// 					join departments on department_name = employee_syncs.department
					// 					WHERE
					// 					employee_syncs.employee_id != 'PI1612005'
					// 					and employee_syncs.end_date is null");

					// 			}
					// 			else{
					// 				$date = date('Y-m-d', strtotime($request->get("tanggal")));

					// 				$survey = DB::SELECT("SELECT
					// 					employee_syncs.employee_id,
					// 					employee_syncs.name,
					// 					COALESCE(department_shortname,'') as department
					// 					FROM
					// 					miraimobile.survey_covid_logs
					// 					LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_covid_logs.employee_id
					// 					join departments on department_name = employee_syncs.department
					// 					WHERE
					// 					department_shortname = '".$dept."'
					// 					and miraimobile.survey_covid_logs.tanggal = '".$date."'
					// 					and employee_syncs.end_date is null");

					// 				$survey_info = DB::SELECT("SELECT
					// 					employee_syncs.employee_id,
					// 					employee_syncs.name,
					// 					COALESCE(department_shortname,'') as department
					// 					FROM
					// 					miraimobile.survey_covid_logs
					// 					LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_covid_logs.employee_id
					// 					join departments on department_name = employee_syncs.department
					// 					WHERE
					// 					miraimobile.survey_covid_logs.tanggal = '".$date."'
					// 					and employee_syncs.end_date is null");
					// 			}
					// 		}



					// 		if ($request->get('tanggal') == "") {
					// 			if ($category == "Rendah") {
					// 				$survey_category = DB::SELECT("SELECT
					// 					employee_syncs.employee_id,
					// 					employee_syncs.name,
					// 					COALESCE(department_shortname,'') as department
					// 					FROM
					// 					miraimobile.survey_logs
					// 					LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id
					// 					join departments on department_name = employee_syncs.department
					// 					WHERE
					// 					employee_syncs.employee_id != 'PI1612005'
					// 					and total <= 35
					// 					and employee_syncs.end_date is null");
					// 			}
					// 			else if ($category == "Sedang"){
					// 				$survey_category = DB::SELECT("SELECT
					// 					employee_syncs.employee_id,
					// 					employee_syncs.name,
					// 					COALESCE(department_shortname,'') as department
					// 					FROM
					// 					miraimobile.survey_logs
					// 					LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id
					// 					join departments on department_name = employee_syncs.department
					// 					WHERE
					// 					employee_syncs.employee_id != 'PI1612005'
					// 					and total > 35 and total <= 80
					// 					and employee_syncs.end_date is null");
					// 			}
					// 			else {
					// 				$survey_category = DB::SELECT("SELECT
					// 					employee_syncs.employee_id,
					// 					employee_syncs.name,
					// 					COALESCE(department_shortname,'') as department
					// 					FROM
					// 					miraimobile.survey_logs
					// 					LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id
					// 					join departments on department_name = employee_syncs.department
					// 					WHERE
					// 					employee_syncs.employee_id != 'PI1612005'
					// 					and total > 80
					// 					and employee_syncs.end_date is null");
					// 			}
					// 		}

					// 		else {
					// 			if ($category == "Rendah") {
					// 				$survey_category = DB::SELECT("SELECT
					// 					employee_syncs.employee_id,
					// 					employee_syncs.name,
					// 					COALESCE(department_shortname,'') as department
					// 					FROM
					// 					miraimobile.survey_covid_logs
					// 					LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_covid_logs.employee_id
					// 					join departments on department_name = employee_syncs.department
					// 					WHERE
					// 					employee_syncs.employee_id != 'PI1612005'
					// 					and total <= 35
					// 					and miraimobile.survey_covid_logs.tanggal = '".$date."'
					// 					and employee_syncs.end_date is null");
					// 			}
					// 			else if ($category == "Sedang"){
					// 				$survey_category = DB::SELECT("SELECT
					// 					employee_syncs.employee_id,
					// 					employee_syncs.name,
					// 					COALESCE(department_shortname,'') as department
					// 					FROM
					// 					miraimobile.survey_covid_logs
					// 					LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_covid_logs.employee_id
					// 					join departments on department_name = employee_syncs.department
					// 					WHERE
					// 					employee_syncs.employee_id != 'PI1612005'
					// 					and total > 35 and total <= 80
					// 					and miraimobile.survey_covid_logs.tanggal = '".$date."'
					// 					and employee_syncs.end_date is null");
					// 			}
					// 			else {
					// 				$survey_category = DB::SELECT("SELECT
					// 					employee_syncs.employee_id,
					// 					employee_syncs.name,
					// 					COALESCE(department_shortname,'') as department
					// 					FROM
					// 					miraimobile.survey_covid_logs
					// 					LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_covid_logs.employee_id
					// 					join departments on department_name = employee_syncs.department
					// 					WHERE
					// 					employee_syncs.employee_id != 'PI1612005'
					// 					and total > 80
					// 					and miraimobile.survey_covid_logs.tanggal = '".$date."'
					// 					and employee_syncs.end_date is null");
					// 			}
					// 		}

			$response = array(
				'status' => true,
				'survey' => $survey,
				'survey_info' => $survey_info,
				'survey_category' => $survey_category
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

	public function indexSurveyCovidReport()
	{
		$title = 'Report Survey Covid-19';
		$title_jp = 'コロナ調査報告';

		return view('survey.report_covid', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Report Survey Covid')
		->with('head','Report Survey Covid');
	}

	public function fetchSurveyCovidReport(Request $request)
	{
		try {
			$date_from = $request->get('tanggal_from');
			$date_to = $request->get('tanggal_to');

							// if ($date_from == "") {
							// 	if ($date_to == "") {
							// 		$where1 = "WHERE DATE( miraimobile.survey_logs.created_at ) BETWEEN DATE(NOW() - INTERVAL 7 DAY) AND DATE(NOW())";
							// 		$where2 = "WHERE DATE( miraimobile.survey_covid_logs.created_at ) BETWEEN DATE(NOW() - INTERVAL 7 DAY) AND DATE(NOW())";
							// 	}else{
							// 		$where1 = "WHERE DATE( miraimobile.survey_logs.created_at ) BETWEEN DATE('".$date_to."' - INTERVAL 7 DAY) AND '".$date_to."'";
							// 		$where2 = "WHERE DATE( miraimobile.survey_covid_logs.created_at ) BETWEEN DATE('".$date_to."' - INTERVAL 7 DAY) AND '".$date_to."'";
							// 	}
							// }else{
							// 	if ($date_to == "") {
							// 		$where1 = "WHERE DATE( miraimobile.survey_logs.created_at ) BETWEEN '".$date_from."' AND DATE(NOW())";
							// 		$where2 = "WHERE DATE( miraimobile.survey_covid_logs.created_at ) BETWEEN '".$date_from."' AND DATE(NOW())";
							// 	}else{
							// 		$where1 = "WHERE DATE( miraimobile.survey_logs.created_at ) BETWEEN '".$date_from."' AND '".$date_to."'";
							// 		$where2 = "WHERE DATE( miraimobile.survey_covid_logs.created_at ) BETWEEN '".$date_from."' AND '".$date_to."'";
							// 	}
							// }

			$token = app(MiraiMobileController::class)->ympicoid_api_login();

			$param = 'date_from='.$date_from.'&date_to='.$date_to;
			$link = 'fetch/survey_covid';
			$method = 'POST';
							//Contoh $param = 'date_from='.$date_from.'&date_to='.$date_to;

			$survey = app(MiraiMobileController::class)->ympicoid_api($token,$link,$method,$param);

							// $survey = DB::SELECT("
							// 	SELECT
							// 	miraimobile.survey_logs.id as id_survey,
							// 	miraimobile.survey_logs.employee_id,
							// 	employee_syncs.name,
							// 	employee_syncs.department,
							// 	employee_syncs.section,
							// 	employee_syncs.`group`,
							// 	employee_syncs.sub_group,
							// 	miraimobile.survey_logs.tanggal,
							// 	miraimobile.survey_logs.question,
							// 	miraimobile.survey_logs.answer,
							// 	miraimobile.survey_logs.poin,
							// 	miraimobile.survey_logs.total,
							// 	miraimobile.survey_logs.keterangan,
							// 	miraimobile.survey_logs.created_at
							// 	FROM
							// 	miraimobile.survey_logs
							// 	LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id
							// 	".$where1."
							// 	UNION 

							// 	SELECT
							// 	miraimobile.survey_covid_logs.id as id_survey,
							// 	miraimobile.survey_covid_logs.employee_id,
							// 	employee_syncs.name,
							// 	employee_syncs.department,
							// 	employee_syncs.section,
							// 	employee_syncs.`group`,
							// 	employee_syncs.sub_group,
							// 	miraimobile.survey_covid_logs.tanggal,
							// 	miraimobile.survey_covid_logs.question,
							// 	miraimobile.survey_covid_logs.answer,
							// 	miraimobile.survey_covid_logs.poin,
							// 	miraimobile.survey_covid_logs.total,
							// 	miraimobile.survey_covid_logs.keterangan,
							// 	miraimobile.survey_covid_logs.created_at
							// 	FROM
							// 	miraimobile.survey_covid_logs
							// 	LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_covid_logs.employee_id 
							// 	".$where2."
							// 	order by created_at desc 
							// 	");
			$response = array(
				'status' => true,
				'survey' => $survey,
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

	public function fetchSurveyCovidReportDetail(Request $request)
	{
		try {

			$employee_id = $request->get('employee_id');
			$tanggal = $request->get('tanggal');

			$token = app(MiraiMobileController::class)->ympicoid_api_login();

			$param = 'employee_id='.$employee_id.'&tanggal='.$tanggal;
			$link = 'fetch/survey_covid/detail';
			$method = 'POST';
							//Contoh $param = 'date_from='.$date_from.'&date_to='.$date_to;

			$survey = app(MiraiMobileController::class)->ympicoid_api($token,$link,$method,$param);

							// $survey = DB::SELECT("
							// 	SELECT
							// 	miraimobile.survey_logs.id as id_survey,
							// 	miraimobile.survey_logs.employee_id,
							// 	employee_syncs.name,
							// 	employee_syncs.department,
							// 	employee_syncs.section,
							// 	employee_syncs.`group`,
							// 	employee_syncs.sub_group,
							// 	miraimobile.survey_logs.tanggal,
							// 	miraimobile.survey_logs.question,
							// 	miraimobile.survey_logs.answer,
							// 	miraimobile.survey_logs.poin,
							// 	miraimobile.survey_logs.total,
							// 	miraimobile.survey_logs.keterangan
							// 	FROM
							// 	miraimobile.survey_logs
							// 	JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_logs.employee_id
							// 	where miraimobile.survey_logs.employee_id = '".$request->get('employee_id')."'
							// 	and miraimobile.survey_logs.tanggal = '".$request->get('tanggal')."'

							// 	UNION 

							// 	SELECT
							// 	miraimobile.survey_covid_logs.id as id_survey,
							// 	miraimobile.survey_covid_logs.employee_id,
							// 	employee_syncs.name,
							// 	employee_syncs.department,
							// 	employee_syncs.section,
							// 	employee_syncs.`group`,
							// 	employee_syncs.sub_group,
							// 	miraimobile.survey_covid_logs.tanggal,
							// 	miraimobile.survey_covid_logs.question,
							// 	miraimobile.survey_covid_logs.answer,
							// 	miraimobile.survey_covid_logs.poin,
							// 	miraimobile.survey_covid_logs.total,
							// 	miraimobile.survey_covid_logs.keterangan 
							// 	FROM
							// 	miraimobile.survey_covid_logs
							// 	JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.survey_covid_logs.employee_id
							// 	where miraimobile.survey_covid_logs.employee_id = '".$request->get('employee_id')."'
							// 	and miraimobile.survey_covid_logs.tanggal = '".$request->get('tanggal')."'
							// ");


			$response = array(
				'status' => true,
				'survey' => $survey,
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

	public function indexPeduliLindungi()
	{
		$title = 'Peduli Lindungi';
		$title_jp = '';

		return view('survey.index_pedulilindungi', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Peduli Lindungi')->with('head','Peduli Lindungi');
	}

	public function fetchPeduliLindungi(Request $request)
	{
		try {

			$survey = DB::SELECT("

				SELECT
				sum( a.count_iya ) AS iya,
				sum( a.count_all ) - (SUM(a.count_iya)) AS belum,
				a.department,
				IF(department_shortname is not null, department_shortname, 'MNGT') as department_shortname
				FROM
				(
					SELECT
					0 AS count_iya,
					count( employee_syncs.employee_id ) AS count_all,
					COALESCE ( employee_syncs.department, '' ) AS department 
					FROM
					employee_syncs 
					WHERE
					employee_syncs.end_date IS NULL 
					GROUP BY
					department 

					UNION ALL

					SELECT
					count( miraimobile.pedulilindungi_surveys.employee_id ) AS count_iya,
					0 AS count_all,
					COALESCE ( employee_syncs.department, '' ) AS department 
					FROM
					employee_syncs
					LEFT JOIN miraimobile.pedulilindungi_surveys ON miraimobile.pedulilindungi_surveys.employee_id = employee_syncs.employee_id 
					WHERE
					employee_syncs.end_date IS NULL 
					AND result_survey = 'Sudah Instal'  
					GROUP BY
					department 

					) a
				LEFT JOIN departments ON a.department = departments.department_name 
			-- WHERE a.department != ''
			GROUP BY
			a.department,
			departments.department_shortname");

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

	public function fetchSPeduliLindungiDetail(Request $request)
	{
		try {
			$answer = $request->get('answer');
			$dept = $request->get('dept');

			if($answer == 'Yes'){
				$answer = 'Sudah Instal';
			}else{
				$answer = null;
			}

			if ($answer == null) {
				if($dept == 'MNGT')
				{
					$survey = DB::SELECT("
						SELECT
						employee_syncs.employee_id,
						employee_syncs.name,
						'Management' as department_shortname
						FROM
						employee_syncs
						LEFT JOIN miraimobile.pedulilindungi_surveys ON employee_syncs.employee_id = miraimobile.pedulilindungi_surveys.employee_id
						WHERE
						employee_syncs.end_date IS NULL 
						AND miraimobile.pedulilindungi_surveys.employee_id IS NULL 
						AND employee_syncs.department is null");
				}
				else
				{
					$survey = DB::SELECT("
						SELECT
						employee_syncs.employee_id,
						employee_syncs.name,
						department_shortname
						FROM
						employee_syncs
						LEFT JOIN miraimobile.pedulilindungi_surveys ON employee_syncs.employee_id = miraimobile.pedulilindungi_surveys.employee_id 
						JOIN departments ON department_name = employee_syncs.department 
						WHERE
						employee_syncs.end_date IS NULL 
						AND miraimobile.pedulilindungi_surveys.employee_id IS NULL 
						AND department_shortname = '".$dept."'");
				}

			}else{
				if($dept == 'MNGT')
				{
					$survey = DB::SELECT("
						SELECT
						employee_syncs.employee_id,
						employee_syncs.name,
						'Management' as department_shortname
						FROM
						employee_syncs
						LEFT JOIN miraimobile.pedulilindungi_surveys ON employee_syncs.employee_id = miraimobile.pedulilindungi_surveys.employee_id  
						WHERE
						employee_syncs.end_date IS NULL 
						AND result_survey = 'Sudah Instal'
						AND employee_syncs.department is null");
				}else{
					$survey = DB::SELECT("
						SELECT
						employee_syncs.employee_id,
						employee_syncs.name,
						department_shortname
						FROM
						employee_syncs
						LEFT JOIN miraimobile.pedulilindungi_surveys ON employee_syncs.employee_id = miraimobile.pedulilindungi_surveys.employee_id 
						JOIN departments ON department_name = employee_syncs.department 
						WHERE
						employee_syncs.end_date IS NULL 
						AND department_shortname = '".$dept."' 
						AND result_survey = 'Sudah Instal'");
				}
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

	public function indexPkb()
	{
		$title = 'Surat Pernyataan PKB';
		$title_jp = '労働契約の宣言書';

		$token = app(MiraiMobileController::class)->ympicoid_api_login();

		$param = '';
		$link = 'fetch/pkb_periode';
		$method = 'GET';
						//Contoh $param = 'date_from='.$date_from.'&date_to='.$date_to;

		$datas = app(MiraiMobileController::class)->ympicoid_api_json($token,$link,$method,$param);

						// $periode = DB::SELECT("SELECT DISTINCT
						// 	( periode ) 
						// 	FROM
						// 	miraimobile.pkb_periodes");

		return view('survey.index_pkb', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'periode' => $datas,
		))->with('page', 'Surat Pernyataan PKB')->with('head','Surat Pernyataan PKB');
	}

	public function fetchPkb(Request $request)
	{
		try {
			$token = app(MiraiMobileController::class)->ympicoid_api_login();
			if ($request->get('periode') == '') {
				$param = '';
				$link = 'fetch/pkb_periode';
				$method = 'GET';

				$datas = app(MiraiMobileController::class)->ympicoid_api_json($token,$link,$method,$param);
				$periodes = '';
				for ($i=0; $i < count($datas); $i++) { 
					if ($datas[$i]->status == 'Active') {
						$periodes = $datas[$i]->periode;
					}
				}
			}else{
				$periodes = $request->get('periode');
			}

			$param = 'periode='.$periodes;
			$link = 'fetch/pkb_monitoring';
			$method = 'POST';
						    //Contoh $param = 'date_from='.$date_from.'&date_to='.$date_to;

			$datas = app(MiraiMobileController::class)->ympicoid_api($token,$link,$method,$param);
							// $pkb = DB::SELECT("SELECT
							// 	sum( a.count_sudah ) AS sudah,
							// 	sum( a.count_all ) - (
							// 		SUM( a.count_sudah )) AS belum,
							// 	a.department,
							// 	IF
							// 	( department_shortname IS NOT NULL, department_shortname, 'MNGT' ) AS department_shortname 
							// 	FROM
							// 	(
							// 		SELECT miraimobile.employee_syncs.department AS department,
							// 		count( miraimobile.pkbs.employee_id ) AS count_sudah,
							// 		0 AS count_all 
							// 		FROM
							// 		miraimobile.pkbs
							// 		JOIN miraimobile.employee_syncs ON miraimobile.employee_syncs.employee_id = miraimobile.pkbs.employee_id 
							// 		WHERE
							// 		miraimobile.pkbs.periode = '".$periodes."' 
							// 		and miraimobile.employee_syncs.end_date is null
							// 		and miraimobile.employee_syncs.employee_id not like '%OS%' 
							// 		GROUP BY
							// 		miraimobile.employee_syncs.department UNION ALL
							// 		SELECT employee_syncs.department AS department,
							// 		0 AS count_sudah,
							// 		count( employee_syncs.employee_id ) AS count_all 
							// 		FROM
							// 		employee_syncs 
							// 		WHERE
							// 		employee_syncs.end_date IS NULL 
							// 		and employee_syncs.employee_id not like '%OS%' 
							// 		GROUP BY
							// 		department 
							// 		) a
							// 		LEFT JOIN departments ON a.department = departments.department_name 
							// 		GROUP BY
							// 		a.department,
							// 		departments.department_shortname");

							// 	$pkb_detail = DB::SELECT("SELECT
							// 		miraimobile.employee_syncs.employee_id,
							// 		miraimobile.employee_syncs.`name`,
							// 		miraimobile.employee_syncs.department,
							// 		miraimobile.employee_syncs.section,
							// 		miraimobile.employee_syncs.`group`,
							// 		miraimobile.employee_syncs.sub_group,
							// 		departments.department_shortname,
							// 		'Sudah' AS status_cek 
							// 		FROM
							// 		miraimobile.pkbs
							// 		JOIN miraimobile.employee_syncs ON miraimobile.employee_syncs.employee_id = miraimobile.pkbs.employee_id
							// 		LEFT JOIN departments ON departments.department_name = miraimobile.employee_syncs.department 
							// 		WHERE
							// 		miraimobile.pkbs.periode = '".$periodes."'
							// 		and miraimobile.employee_syncs.employee_id not like '%OS%'  UNION ALL
							// 		SELECT
							// 		employee_syncs.employee_id,
							// 		employee_syncs.`name`,
							// 		employee_syncs.department,
							// 		employee_syncs.section,
							// 		employee_syncs.`group`,
							// 		employee_syncs.sub_group,
							// 		departments.department_shortname,
							// 		'Belum' AS status_cek 
							// 		FROM
							// 		employee_syncs
							// 		LEFT JOIN departments ON departments.department_name = employee_syncs.department 
							// 		WHERE
							// 		employee_syncs.end_date IS NULL 
							// 		and employee_syncs.employee_id not like '%OS%'
							// 		AND employee_syncs.employee_id NOT IN (
							// 		SELECT
							// 		miraimobile.employee_syncs.employee_id 
							// 		FROM
							// 		miraimobile.pkbs
							// 		JOIN miraimobile.employee_syncs ON miraimobile.employee_syncs.employee_id = miraimobile.pkbs.employee_id
							// 		LEFT JOIN departments ON departments.department_name = miraimobile.employee_syncs.department 
							// 		WHERE
							// 		miraimobile.pkbs.periode = '".$periodes."')");
			$response = array(
				'status' => true,
				'pkb' => $datas->pkb,
				'pkb_detail' => $datas->pkb_detail,
				'periode' => $periodes,
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

	public function indexKodeEtik()
	{
		$title = 'Kode Etik Kepatuhan';
		$title_jp = '';


		return view('survey.index_kode_etik', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Kode Etik Kepatuhan')->with('head','Kode Etik Kepatuhan');
	}

	public function fetchkodeEtik(Request $request)
	{
		try {

			$token = app(MiraiMobileController::class)->ympicoid_api_login();

			$param = '';
			$link = 'fetch/kodeEtik/Monitoring';
			$method = 'POST';

			$datas = app(MiraiMobileController::class)->ympicoid_api($token,$link,$method,$param);

								// $kode_etik = DB::SELECT("SELECT
								// 	sum( a.count_sudah ) AS sudah,
								// 	sum( a.count_all ) - (
								// 		SUM( a.count_sudah )) AS belum,
								// 	a.department,
								// 	IF
								// 	( department_shortname IS NOT NULL, department_shortname, 'MNGT' ) AS department_shortname 
								// 	FROM
								// 	(
								// 		SELECT miraimobile.employee_syncs.department AS department,
								// 		count( miraimobile.kode_etik_answers.employee_id ) AS count_sudah,
								// 		0 AS count_all 
								// 		FROM
								// 		miraimobile.kode_etik_answers
								// 		JOIN miraimobile.employee_syncs ON miraimobile.employee_syncs.employee_id = miraimobile.kode_etik_answers.employee_id 
								// 		WHERE
								// 		miraimobile.employee_syncs.end_date is null
								// 		and miraimobile.employee_syncs.employee_id not like '%OS%'
								// 		and miraimobile.employee_syncs.position != 'General Manager'
								// 		and miraimobile.employee_syncs.position != 'President Director'
								// 		and miraimobile.employee_syncs.position != 'Director'
								// 		and miraimobile.employee_syncs.position != 'Deputy General Manager'
								// 		GROUP BY
								// 		miraimobile.employee_syncs.department UNION ALL
								// 		SELECT employee_syncs.department AS department,
								// 		0 AS count_sudah,
								// 		count( employee_syncs.employee_id ) AS count_all 
								// 		FROM
								// 		employee_syncs 
								// 		WHERE
								// 		employee_syncs.end_date IS NULL 
								// 		and employee_syncs.employee_id not like '%OS%' 
								// 		and employee_syncs.employee_id not like '%OS%' 
								// 		and employee_syncs.position != 'General Manager'
								// 		and employee_syncs.position != 'President Director' 
								// 		and employee_syncs.position != 'Director' 
								// 		and employee_syncs.position != 'Deputy General Manager' 
								// 		GROUP BY
								// 		department 
								// 		) a
								// 	LEFT JOIN departments ON a.department = departments.department_name 
								// 	GROUP BY
								// 	a.department,
								// 	departments.department_shortname");

								// $kode_etik_detail = DB::SELECT("SELECT
								// 	miraimobile.employee_syncs.employee_id,
								// 	miraimobile.employee_syncs.`name`,
								// 	miraimobile.employee_syncs.department,
								// 	miraimobile.employee_syncs.section,
								// 	miraimobile.employee_syncs.`group`,
								// 	miraimobile.employee_syncs.sub_group,
								// 	departments.department_shortname,
								// 	'Sudah' AS status_cek 
								// 	FROM
								// 	miraimobile.kode_etik_answers
								// 	JOIN miraimobile.employee_syncs ON miraimobile.employee_syncs.employee_id = miraimobile.kode_etik_answers.employee_id
								// 	LEFT JOIN departments ON departments.department_name = miraimobile.employee_syncs.department 
								// 	WHERE
								// 	miraimobile.employee_syncs.employee_id not like '%OS%'
								// 	and miraimobile.employee_syncs.position != 'General Manager'
								// 	and miraimobile.employee_syncs.position != 'President Director'
								// 	and miraimobile.employee_syncs.position != 'Director'
								// 	and miraimobile.employee_syncs.position != 'Deputy General Manager'

								// 	UNION ALL
								// 	SELECT
								// 	employee_syncs.employee_id,
								// 	employee_syncs.`name`,
								// 	employee_syncs.department,
								// 	employee_syncs.section,
								// 	employee_syncs.`group`,
								// 	employee_syncs.sub_group,
								// 	departments.department_shortname,
								// 	'Belum' AS status_cek 
								// 	FROM
								// 	employee_syncs
								// 	LEFT JOIN departments ON departments.department_name = employee_syncs.department 
								// 	WHERE
								// 	employee_syncs.end_date IS NULL 
								// 	and employee_syncs.employee_id not like '%OS%'
								// 	and employee_syncs.position != 'General Manager'
								// 	and employee_syncs.position != 'President Director' 
								// 	and employee_syncs.position != 'Director' 
								// 	and employee_syncs.position != 'Deputy General Manager' 
								// 	AND employee_syncs.employee_id NOT IN (
								// 		SELECT
								// 		miraimobile.employee_syncs.employee_id 
								// 		FROM
								// 		miraimobile.kode_etik_answers
								// 		JOIN miraimobile.employee_syncs ON miraimobile.employee_syncs.employee_id = miraimobile.kode_etik_answers.employee_id
								// 		LEFT JOIN departments ON departments.department_name = miraimobile.employee_syncs.department 
								// 	)");
			$response = array(
				'status' => true,
				'kode_etik' => $datas->kode_etik,
				'kode_etik_detail' => $datas->kode_etik_detail

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

	public function indexHasilMCU()
	{
		$title = 'Hasil MCU';
		$title_jp = '';

		return view('survey.index_hasilmcu', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Hasil MCU')->with('head','Hasil MCU');
	}

	public function fetchHasilMCU(Request $request)
	{
		try {

			$survey = DB::SELECT("
				SELECT
				sum( a.count_iya ) AS iya,
				sum( a.count_belum ) AS tidak,
				sum( a.count_all ) - (SUM(a.count_iya) + SUM(a.count_belum)) AS belum,
				a.department,
				IF(department_shortname is not null, department_shortname, 'MNGT') as department_shortname
				FROM
				(
					SELECT
					0 AS count_iya,
					0 AS count_belum,
					count( miraimobile.mcus.employee_id ) AS count_all,
					COALESCE ( employee_syncs.department, '' ) AS department 
					FROM
					miraimobile.mcus 
					JOIN 
					employee_syncs on miraimobile.mcus.employee_id = employee_syncs.employee_id
					GROUP BY
					department 

					UNION ALL

					SELECT
					count( miraimobile.mcu_surveys.employee_id ) AS count_iya,
					0 AS count_belum,
					0 AS count_all,
					COALESCE ( miraimobile.mcu_surveys.department, '' ) AS department 
					FROM
					miraimobile.mcus
					LEFT JOIN miraimobile.mcu_surveys ON miraimobile.mcu_surveys.employee_id = miraimobile.mcus.employee_id 
					WHERE
					jawaban = 'Iya'  
					GROUP BY
					department 

					UNION ALL

					SELECT
					0 AS count_iya,
					count( miraimobile.mcu_surveys.employee_id ) AS count_belum,
					0 AS count_all,
					COALESCE ( miraimobile.mcu_surveys.department, '' ) AS department 
					FROM
					miraimobile.mcus
					LEFT JOIN miraimobile.mcu_surveys ON miraimobile.mcu_surveys.employee_id = miraimobile.mcus.employee_id 
					WHERE
					jawaban = 'Tidak'  
					GROUP BY
					department 

					) a
				LEFT JOIN departments ON a.department = departments.department_name 
								-- WHERE a.department != ''
								GROUP BY
								a.department,
								departments.department_shortname");

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

	public function fetchHasilMCUDetail(Request $request)
	{
		try {
			$answer = $request->get('answer');
			$dept = $request->get('dept');

			if($answer == 'Yes'){
				$answer = 'Iya';
			}
			else if($answer == 'No'){
				$answer = 'Tidak';
			}
			else{
				$answer = null;
			}

			if ($answer == null) {
				if($dept == 'MNGT')
				{
					$survey = DB::SELECT("
						SELECT
						miraimobile.mcus.employee_id,
						miraimobile.mcus.name,
						'Management' AS department_shortname 
						FROM
						miraimobile.mcus
						LEFT JOIN miraimobile.mcu_surveys ON miraimobile.mcus.employee_id = miraimobile.mcu_surveys.employee_id 
						left join employee_syncs on employee_syncs.employee_id = miraimobile.mcus.employee_id
						WHERE
						miraimobile.mcu_surveys.employee_id IS NULL 
						AND employee_syncs.department IS NULL
						");
				}
				else
				{
					$survey = DB::SELECT("
						SELECT
						miraimobile.mcus.employee_id,
						miraimobile.mcus.NAME,
						department_shortname
						FROM
						miraimobile.mcus
						LEFT JOIN miraimobile.mcu_surveys ON miraimobile.mcus.employee_id = miraimobile.mcu_surveys.employee_id 
						left join employee_syncs on employee_syncs.employee_id = miraimobile.mcus.employee_id
						JOIN departments ON department_name = employee_syncs.department 
						WHERE
						miraimobile.mcu_surveys.employee_id IS NULL 
						AND department_shortname = '".$dept."'");
				}

			}else{
				if($dept == 'MNGT')
				{
					if ($answer == "Iya") {
						$survey = DB::SELECT("
							SELECT
							miraimobile.mcus.employee_id,
							miraimobile.mcus.name,
							'Management' as department_shortname
							FROM
							miraimobile.mcus
							LEFT JOIN miraimobile.mcu_surveys ON miraimobile.mcus.employee_id = miraimobile.mcu_surveys.employee_id  
							left join employee_syncs on employee_syncs.employee_id = miraimobile.mcus.employee_id
							WHERE
							jawaban = 'Iya'
							AND employee_syncs.department is null");
					}
					else{
						$survey = DB::SELECT("
							SELECT
							miraimobile.mcus.employee_id,
							miraimobile.mcus.name,
							'Management' as department_shortname
							FROM
							miraimobile.mcus
							LEFT JOIN miraimobile.mcu_surveys ON miraimobile.mcus.employee_id = miraimobile.mcu_surveys.employee_id  
							left join employee_syncs on employee_syncs.employee_id = miraimobile.mcus.employee_id
							WHERE
							jawaban = 'Tidak'
							AND employee_syncs.department is null");
					}
				}else{
					if ($answer == "Iya") {
						$survey = DB::SELECT("
							SELECT
							miraimobile.mcus.employee_id,
							miraimobile.mcus.name,
							department_shortname
							FROM
							miraimobile.mcus
							LEFT JOIN miraimobile.mcu_surveys ON miraimobile.mcus.employee_id = miraimobile.mcu_surveys.employee_id  
							left join employee_syncs on employee_syncs.employee_id = miraimobile.mcus.employee_id
							JOIN departments ON department_name = employee_syncs.department 
							WHERE 
							department_shortname = '".$dept."' 
							AND jawaban = 'Iya'");
					}
					else{
						$survey = DB::SELECT("
							SELECT
							miraimobile.mcus.employee_id,
							miraimobile.mcus.name,
							department_shortname
							FROM
							miraimobile.mcus
							LEFT JOIN miraimobile.mcu_surveys ON miraimobile.mcus.employee_id = miraimobile.mcu_surveys.employee_id  
							left join employee_syncs on employee_syncs.employee_id = miraimobile.mcus.employee_id
							JOIN departments ON department_name = employee_syncs.department 
							WHERE
							department_shortname = '".$dept."' 
							AND jawaban = 'Tidak'");
					}
				}
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

	public function fetchHasilMCUDetailAll(Request $request)
	{
		try {
			$answer = $request->get('answer');

			if ($answer == "Sudah") {
				$mcu = DB::SELECT("
					SELECT
					miraimobile.mcus.employee_id,
					miraimobile.mcus.name,
					department_shortname
					FROM
					miraimobile.mcus
					LEFT JOIN miraimobile.mcu_surveys ON miraimobile.mcus.employee_id = miraimobile.mcu_surveys.employee_id  
					left join employee_syncs on employee_syncs.employee_id = miraimobile.mcus.employee_id
					JOIN departments ON department_name = employee_syncs.department 
					WHERE
					jawaban = 'Iya'
					");
			} 
			else if ($answer == "Tidak"){
				$mcu = DB::SELECT("
					SELECT
					miraimobile.mcus.employee_id,
					miraimobile.mcus.name,
					department_shortname
					FROM
					miraimobile.mcus
					LEFT JOIN miraimobile.mcu_surveys ON miraimobile.mcus.employee_id = miraimobile.mcu_surveys.employee_id  
					left join employee_syncs on employee_syncs.employee_id = miraimobile.mcus.employee_id
					JOIN departments ON department_name = employee_syncs.department 
					WHERE
					jawaban = 'Tidak'
					");
			}else if ($answer == "Belum"){
				$mcu = DB::SELECT("
					SELECT
					miraimobile.mcus.employee_id,
					miraimobile.mcus.name,
					employee_syncs.department as department_shortname
					FROM
					miraimobile.mcus
					LEFT JOIN miraimobile.mcu_surveys ON miraimobile.mcus.employee_id = miraimobile.mcu_surveys.employee_id  
					left join employee_syncs on employee_syncs.employee_id = miraimobile.mcus.employee_id 
					WHERE
					miraimobile.mcu_surveys.employee_id IS NULL
					");
			}

			$response = array(
				'status' => true,
				'mcu' => $mcu
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

	public function indexDataKomunikasi()
	{
		$title = 'Data Komunikasi';
		$title_jp = '';

		return view('survey.index_data_komunikasi', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Data Komunikasi')->with('head','Data Komunikasi');
	}

	public function fetchDataKomunikasi(Request $request)
	{
		try {

			$survey = DB::SELECT("
				SELECT
				sum( a.count_iya ) AS iya,
				sum( a.count_all ) - (SUM(a.count_iya)) AS belum,
				a.department,
				IF(department_shortname is not null, department_shortname, 'MNGT') as department_shortname
				FROM
				(
					SELECT
					0 AS count_iya,
					count( employee_syncs.employee_id ) AS count_all,
					COALESCE ( employee_syncs.department, '' ) AS department 
					FROM
					employee_syncs 
					WHERE
					employee_syncs.end_date IS NULL 
					GROUP BY
					department 

					UNION ALL

					SELECT
					count( miraimobile.employee_communications.employee_id ) AS count_iya,
					0 AS count_all,
					COALESCE ( employee_syncs.department, '' ) AS department 
					FROM
					employee_syncs
					LEFT JOIN miraimobile.employee_communications ON miraimobile.employee_communications.employee_id = employee_syncs.employee_id 
					WHERE
					employee_syncs.end_date IS NULL 
					GROUP BY
					department 

					) a
				LEFT JOIN departments ON a.department = departments.department_name 

				GROUP BY
				a.department,
				departments.department_shortname
				");

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

	public function fetchDataKomunikasiDetail(Request $request)
	{
		try {
			$answer = $request->get('answer');
			$dept = $request->get('dept');

			if($answer == 'Yes'){
				$answer = 'Iya';
			}
			else{
				$answer = null;
			}

			if ($answer == null) {
				if($dept == 'MNGT')
				{
					$survey = DB::SELECT("
						SELECT
						employee_syncs.employee_id,
						employee_syncs.name,
						'Management' as department_shortname,
						employee_communications.no_hp,
						employee_communications.no_alternatif,
						employee_communications.rencana_mudik,
						employee_communications.tanggal_berangkat,
						employee_communications.tanggal_kembali,
						employee_communications.test_type,
						employee_communications.departure,
						employee_communications.tanggal_departure,
						employee_communications.arrived,
						employee_communications.tanggal_arrived
						FROM
						employee_syncs
						LEFT JOIN miraimobile.employee_communications ON employee_syncs.employee_id = miraimobile.employee_communications.employee_id
						WHERE
						employee_syncs.end_date IS NULL 
						AND miraimobile.employee_communications.employee_id IS NULL 
						AND employee_syncs.department is null");
				}
				else
				{
					$survey = DB::SELECT("
						SELECT
						employee_syncs.employee_id,
						employee_syncs.name,
						department_shortname,
						employee_communications.no_hp,
						employee_communications.no_alternatif,
						employee_communications.rencana_mudik,
						employee_communications.tanggal_berangkat,
						employee_communications.tanggal_kembali,
						employee_communications.test_type,
						employee_communications.departure,
						employee_communications.tanggal_departure,
						employee_communications.arrived,
						employee_communications.tanggal_arrived
						FROM
						employee_syncs
						LEFT JOIN miraimobile.employee_communications ON employee_syncs.employee_id = miraimobile.employee_communications.employee_id 
						JOIN departments ON department_name = employee_syncs.department 
						WHERE
						employee_syncs.end_date IS NULL 
						AND miraimobile.employee_communications.employee_id IS NULL 
						AND department_shortname = '".$dept."'");
				}

			}else{
				if($dept == 'MNGT')
				{
					$survey = DB::SELECT("
						SELECT
						employee_syncs.employee_id,
						employee_syncs.name,
						'Management' as department_shortname,
						employee_communications.no_hp,
						employee_communications.no_alternatif,
						employee_communications.rencana_mudik,
						employee_communications.tanggal_berangkat,
						employee_communications.tanggal_kembali,
						employee_communications.test_type,
						employee_communications.departure,
						employee_communications.tanggal_departure,
						employee_communications.arrived,
						employee_communications.tanggal_arrived
						FROM
						employee_syncs
						JOIN miraimobile.employee_communications ON employee_syncs.employee_id = miraimobile.employee_communications.employee_id  
						WHERE
						employee_syncs.end_date IS NULL 
						AND employee_syncs.department is null");
				}else{
					$survey = DB::SELECT("
						SELECT
						employee_syncs.employee_id,
						employee_syncs.name,
						department_shortname,
						employee_communications.no_hp,
						employee_communications.no_alternatif,
						employee_communications.rencana_mudik,
						employee_communications.tanggal_berangkat,
						employee_communications.tanggal_kembali,
						employee_communications.test_type,
						employee_communications.departure,
						employee_communications.tanggal_departure,
						employee_communications.arrived,
						employee_communications.tanggal_arrived
						FROM
						employee_syncs
						JOIN miraimobile.employee_communications ON employee_syncs.employee_id = miraimobile.employee_communications.employee_id 
						JOIN departments ON department_name = employee_syncs.department 
						WHERE
						employee_syncs.end_date IS NULL 
						AND department_shortname = '".$dept."' 
						");
				}
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

	public function fetchDataKomunikasiDetailAll(Request $request)
	{
		try {
			$answer = $request->get('answer');

			if ($answer == "Sudah") {
				$komunikasi = DB::SELECT("
					SELECT
					employee_syncs.employee_id,
					employee_syncs.name,
					department_shortname,
					employee_communications.no_hp,
					employee_communications.no_alternatif,
					employee_communications.rencana_mudik,
					employee_communications.tanggal_berangkat,
					employee_communications.tanggal_kembali,
					employee_communications.test_type,
					employee_communications.departure,
					employee_communications.tanggal_departure,
					employee_communications.arrived,
					employee_communications.tanggal_arrived
					FROM
					employee_syncs
					JOIN miraimobile.employee_communications ON employee_syncs.employee_id = miraimobile.employee_communications.employee_id 
					JOIN departments ON department_name = employee_syncs.department 
					WHERE
					employee_syncs.end_date IS NULL 
					");
			} else if ($answer == "Belum"){
				$komunikasi = DB::SELECT("
					SELECT
					employee_syncs.employee_id,
					employee_syncs.name,
					employee_syncs.department as department_shortname,
					employee_communications.no_hp,
					employee_communications.no_alternatif,
					employee_communications.rencana_mudik,
					employee_communications.tanggal_berangkat,
					employee_communications.tanggal_kembali,
					employee_communications.test_type,
					employee_communications.departure,
					employee_communications.tanggal_departure,
					employee_communications.arrived,
					employee_communications.tanggal_arrived
					FROM
					employee_syncs
					LEFT JOIN miraimobile.employee_communications ON employee_syncs.employee_id = miraimobile.employee_communications.employee_id  
					WHERE
					employee_syncs.end_date IS NULL 
					AND miraimobile.employee_communications.employee_id IS NULL
					");
			}

			$response = array(
				'status' => true,
				'komunikasi' => $komunikasi
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

	public function indexSloganMonitoring()
	{
		$title = 'Monitoring Slogan Mutu YMPI';
		$title_jp = '';

		$fy_all = WeeklyCalendar::select('fiscal_year')->distinct()->get();

		return view('survey.index_slogan_mutu', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'fy_all' => $fy_all,
		))->with('page', 'Slogan Mutu YMPI')->with('head','Slogan Mutu YMPI');
	}

	public function fetchSloganMonitoring(Request $request)
	{
		try {

			if ($request->get('fiscal_year') != '') {
				$fy = $request->get('fiscal_year');
			}else{
				$fys = WeeklyCalendar::where('week_date',date('Y-m-d'))->first();
				$fy = $fys->fiscal_year;
			}
			$slogan = DB::Select("SELECT
				ympimis.employee_syncs.*,
				COALESCE ( department, 'Management' ) AS department_name,
				slogan.slogan_1
				FROM
				ympimis.employee_syncs
				LEFT JOIN ( SELECT * FROM miraimobile.std_slogans WHERE periode = '".$fy."' ) AS slogan ON slogan.employee_id = ympimis.employee_syncs.employee_id
					WHERE
					ympimis.employee_syncs.end_date IS NULL
					AND grade_code != 'J0-'");

				$department = DB::SELECT("SELECT DISTINCT
					( COALESCE ( department, 'Management' ) ) AS department,
					COALESCE ( department_shortname, 'MGT' ) AS department_shortname 
					FROM
					employee_syncs
					LEFT JOIN departments ON departments.department_name = employee_syncs.department");

				$response = array(
					'status' => true,
					'slogan' => $slogan,
					'department' => $department,
					'fy' => $fy,
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
