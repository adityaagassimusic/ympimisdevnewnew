<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use App\Mail\SendEmail;
use App\GeneralAttendance;
use App\GeneralAttendanceLog;
use App\Employee;
use App\EmployeeSync;
use App\GeneralTransportation;
use App\GeneralTransportationData;
use App\GeneralDoctor;
use App\CodeGenerator;
use App\GeneralShoesLog;
use App\GeneralShoesRequest;
use App\RcNgBox;
use App\GeneralShoesStock;
use App\User;
use App\Agreement;
use App\SafetyRiding;
use App\AgreementAttachment;
use App\StampInventory;
use App\GeneralAirVisualLog;
use App\WeeklyCalendar;
use App\LogProcess;
use PDF;
use Auth;
use Excel;
use DataTables;
use Response;
use Carbon\Carbon;


class GeneralAttendanceController extends Controller
{
	public function indexGeneralAttendanceCheck(){
		$title = "Attendance Check";
		$title_jp = "";

		$purposes = GeneralAttendance::orderBy('purpose_code', 'asc')
		->select('purpose_code')
		->distinct()
		->get();

		return view('general.attendance_check', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'purposes' => $purposes
		))->with('head', 'GA Control')->with('page', 'Driver Control');
	}

	public function scanGeneralAttendanceCheck(Request $request){
		$employee = Employee::where('tag', '=', $request->get('tag'))->first();

		if($employee == ""){
			$response = array(
				'status' => false,
				'message' => 'Tag karyawan tidak terdaftar, hubungi bagian MIS.'
			);
			return Response::json($response);
		}

		$attendance = GeneralAttendance::where('employee_id', '=', $employee->employee_id)
		->where('due_date', '=', date('Y-m-d'))
		->get();
		

		// if($attendance == "" || $attendance->due_date > date('Y-m-d')){
		// 	$response = array(
		// 		'status' => false,
		// 		'message' => 'Karyawan tidak ada pada schedule.'
		// 	);
		// 	return Response::json($response);
		// }

		// if($attendance->attend_date != null){
		// 	$response = array(
		// 		'status' => false,
		// 		'message' => 'Karyawan sudah menghadiri schedule.'
		// 	);
		// 	return Response::json($response);
		// }

		try{
			if (count($attendance) > 0) {
				if ($attendance[0]->due_date > date('Y-m-d')) {
					$response = array(
						'status' => false,
						'message' => 'Karyawan tidak ada pada schedule.'
					);
					return Response::json($response);
				}else{
					if ($attendance[0]->attend_date != null) {
						$response = array(
							'status' => false,
							'message' => 'Karyawan sudah menghadiri schedule.'
						);
						return Response::json($response);
					}

					$attendance = GeneralAttendance::where('employee_id', '=', $employee->employee_id)
					->where('due_date', '=', date('Y-m-d'))
					->update([
						'attend_date' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s')
					]);

					$response = array(
						'status' => true,
						'message' => $employee->name.' berhasil hadir.'
					);
					return Response::json($response);
				}
			}else{
				$response = array(
					'status' => false,
					'message' => 'Karyawan tidak ada pada schedule.'
				);
				return Response::json($response);
			}
			// $attendance->attend_date = date('Y-m-d H:i:s');
			// $attendance->save();
		}
		catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}

		$response = array(
			'status' => true,
			'message' => 'Berhasil'
		);
		return Response::json($response);

	}

	public function fetchGeneralAttendanceCheck(Request $request){

		try{
			$now = date('Y-m-d');

			$attendance_lists = db::select("SELECT
				ga.purpose_code,
				ga.employee_id,
				es.`name`,
				ga.attend_date 
				FROM
				general_attendances AS ga
				LEFT JOIN employee_syncs AS es ON ga.employee_id = es.employee_id 
				WHERE
				ga.due_date = '".$now."' 
				ORDER BY
				attend_date ASC");

			$attendance_lists_bento = db::select("SELECT
				ga.purpose_code,
				ga.employee_id,
				es.`name`,
				ga.attend_date 
				FROM
				general_attendances AS ga
				LEFT JOIN employee_syncs AS es ON ga.employee_id = es.employee_id 
				WHERE
				ga.due_date = '".$now."' 
				AND attend_date IS NOT NULL 
				and purpose_code like '%bento%'
				ORDER BY
				attend_date DESC
				LIMIT 1");

			$attendance_lists_live = db::select("SELECT
				ga.purpose_code,
				ga.employee_id,
				es.`name`,
				ga.attend_date 
				FROM
				general_attendances AS ga
				LEFT JOIN employee_syncs AS es ON ga.employee_id = es.employee_id 
				WHERE
				ga.due_date = '".$now."' 
				AND attend_date IS NOT NULL 
				and purpose_code like '%live cooking%'
				ORDER BY
				attend_date DESC
				LIMIT 1");

			$attendance_lists_extra = db::select("SELECT
				ga.purpose_code,
				ga.employee_id,
				es.`name`,
				ga.attend_date 
				FROM
				general_attendances AS ga
				LEFT JOIN employee_syncs AS es ON ga.employee_id = es.employee_id 
				WHERE
				ga.due_date = '".$now."' 
				AND attend_date IS NOT NULL 
				and purpose_code like '%extra%'
				ORDER BY
				attend_date DESC
				LIMIT 1");
			$attendance_lists_overtime = db::select("SELECT
				ga.purpose_code,
				ga.employee_id,
				es.`name`,
				ga.attend_date 
				FROM
				general_attendances AS ga
				LEFT JOIN employee_syncs AS es ON ga.employee_id = es.employee_id 
				WHERE
				ga.due_date = '".$now."' 
				AND attend_date IS NOT NULL 
				and purpose_code like '%Overtime%'
				ORDER BY
				attend_date DESC
				LIMIT 1");

			// $query = "SELECT DISTINCT
			// purpose_code,
			// employee_id,
			// due_date,
			// NAME,
			// departments.department_shortname AS department,
			// attend_date 
			// FROM
			// (
			// SELECT
			// general_attendances.purpose_code,
			// general_attendances.employee_id,
			// general_attendances.due_date,
			// employee_syncs.`name`,
			// employee_syncs.department,
			// DATE_FORMAT(general_attendances.attend_date, '%H:%i:%s') as attend_date
			// FROM
			// general_attendances
			// LEFT JOIN employee_syncs ON general_attendances.employee_id = employee_syncs.employee_id 
			// WHERE
			// general_attendances.due_date = '".$now."' AND general_attendances.purpose_code = '".$request->get('purpose_code')."' UNION ALL
			// SELECT
			// general_attendances.purpose_code,
			// general_attendances.employee_id,
			// general_attendances.due_date,
			// employee_syncs.`name`,
			// employee_syncs.department,
			// DATE_FORMAT(general_attendances.attend_date, '%H:%i:%s') as attend_date
			// FROM
			// general_attendances
			// LEFT JOIN employee_syncs ON general_attendances.employee_id = employee_syncs.employee_id 
			// WHERE
			// DATE( general_attendances.attend_date ) = '".$now."' AND general_attendances.purpose_code = '".$request->get('purpose_code')."'
			// ) AS attendances 
			// LEFT JOIN
			// departments on departments.department_name = attendances.department
			// WHERE employee_id like 'PI%'
			// ORDER BY
			// attend_date DESC,
			// NAME ASC";

			// $attendance_lists = db::select($query); 

			$response = array(
				'status' => true,
				'attendance_lists' => $attendance_lists,
				'attendance_lists_bento' => $attendance_lists_bento,
				'attendance_lists_live' => $attendance_lists_live,
				'attendance_lists_extra' => $attendance_lists_extra,
				'attendance_lists_overtime' => $attendance_lists_overtime
			);
			return Response::json($response);

		}
		catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}		
	}

	// public function fetchLockerQr(Request $request,$id)
	// {
	// 	$locker = DB::connection('ympimis_2')
	// 	->table('lockers')
	// 	->where('locker_id',$id)
	// 	->first();

	// 	$title = 'Locker Detail';
	// 	$title_jp = '';

	// 	return view('general_affairs.locker.locker_qr', array(
	// 		'locker' => $locker,
	// 		'title' => $title,
	// 		'title_jp' => $title_jp,
	// 	))->with('page', 'Locker Detail')->with('head','Locker Detail');
	// }

	public function inputNgBox(Request $request)
	{
		try {
			$tray = RcNgBox::where('date',date('Y-m-d'))->where('tray',$request->get('tray'))->first();
			if (count($tray) > 0) {
				if ($request->get('ng_head') != 0) {
					$tray->ng_head = $tray->ng_head+1;
				}

				if ($request->get('ng_middle') != 0) {
					$tray->ng_middle = $tray->ng_middle+1;
				}

				if ($request->get('ng_foot') != 0) {
					$tray->ng_foot = $tray->ng_foot+1;
				}

				if ($request->get('ng_block') != 0) {
					$tray->ng_block = $tray->ng_block+1;
				}

				$tray->save();
			}else{
				$tray = RcNgBox::create([
					'date' => date('Y-m-d'),
					'tray' => $request->get('tray'),
					'ng_head' => $request->get('ng_head'),
					'ng_middle' => $request->get('ng_middle'),
					'ng_foot' => $request->get('ng_foot'),
					'ng_block' => $request->get('ng_block'),
					'created_by' => 1,
				]);
			}

			$response = array(
				'status' => true,
				'message' => 'Input Berhasil',
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

	// public function inputPhSensor(Request $request)
	// {
	// 	try {
	// 		DB::table('sensor_datas')->insert([
	// 			'category' => 'Ph', 
	// 			'data_time' => date('Y-m-d H:i:s'), 
	// 			'sensor_value' => round((float) $request->get('ph'), 2), 
	// 			'unit' => '', 
	// 			'created_by' => '1', 
	// 			'created_at' => date('Y-m-d H:i:s'),
	// 			'updated_at' => date('Y-m-d H:i:s')
	// 		]);

	// 		$response = array(
	// 			'status' => true,
	// 			'message' => 'Input Berhasil',
	// 		);
	// 		return Response::json($response);
	// 	} catch (\Exception $e) {
	// 		$response = array(
	// 			'status' => false,
	// 			'message' => $e->getMessage(),
	// 		);
	// 		return Response::json($response);
	// 	}
	// }

	// public function inputTempHum(Request $request)
	// {
	// 	try {
	// 		DB::table('sensor_datas')->insert([
	// 			'category' => 'CO2', 
	// 			'data_time' => date('Y-m-d H:i:s'), 
	// 			'sensor_value' => (int) $request->get('co') - 67,
	// 			'remark' => (float) $request->get('temp'),
	// 			'unit' => $request->get('device'),
	// 			'created_by' => '1',
	// 			'created_at' => date('Y-m-d H:i:s'),
	// 			'updated_at' => date('Y-m-d H:i:s')
	// 		]);

	// 		$response = array(
	// 			'status' => true,
	// 			'message' => 'Input Berhasil',
	// 		);
	// 		return Response::json($response);
	// 	} catch (\Exception $e) {
	// 		$response = array(
	// 			'status' => false,
	// 			'message' => $e->getMessage(),
	// 		);
	// 		return Response::json($response);
	// 	}
	// }

	public function inputStamp(Request $request)
	{
		try {
			$code_generator = CodeGenerator::where('note', '=', $request->get('origin'))->first();
			$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

			$serial_number = $code_generator->prefix.$number;

			// $stamp_inventory = StampInventory::updateOrCreate(
			// 	[
			// 		'serial_number' => $serial_number,
			// 		'origin_group_code' => $request->get('origin')
			// 	],
			// 	[
			// 		'process_code' => $request->get('processCode'), 
			// 		// 'model' => $request->get('model'),
			// 		'quantity' => 1
			// 	]
			// );

			$log_process = LogProcess::updateOrCreate(
				[
					'process_code' => $request->get('processCode'), 
					'serial_number' => $serial_number,
					'origin_group_code' => $request->get('origin')
				],
				[
					'model' => '',
					'manpower' => 27,
					'quantity' => 1,
					'created_by' => 126,
					'created_at' => date('Y-m-d H:i:s'),
					'remark' =>'FG',
				]
			);

			$log_process->save();

			$code_generator->index = $code_generator->index+1;
			$code_generator->save();

			$response = array(
				'status' => true,
				'message' => 'Input Berhasil',
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

	public function indexGSNew()
	{
		$title = "GS Control";
		$title_jp = "";
		$tgl = date('Y-m-d');

		$departments = db::table('departments')->orderBy('department_name', 'ASC')->get();
		$pics = db::connection('ympimis_2')->table('gs_operators')->whereNull('deleted_at')->get();
		$user = User::where('id', '=', Auth::id())->first();

		$cek_cate = db::connection('ympimis_2')
		->select("SELECT DISTINCT
			category,
			area 
			FROM
			`gs_list_job_masters` 
			WHERE
			category != 'Lain-Lain' 
			ORDER BY
			category ASC
			");


		$categorys = DB::connection('ympimis_2')->Select("SELECT DISTINCT
			category
			FROM
			`gs_list_job_masters`
			ORDER BY category ASC
			");

		return view('ga_control.gs_control.index_gs_new', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'departments' => $departments,
			'pics' => $pics,
			'user' => $user,
			'category' => $categorys,
			'cek_cate' => $cek_cate
		))->with('page', 'gscontrol');
	}

	public function fetchCheckOP(Request $request)
	{
		$op_ids = $request->get('id');
		try {
			$cek_op = db::connection('ympimis_2')->table('gs_operators')->where('employee_id',$op_ids)->whereNull('deleted_at')->first();
			$response = array(
				'status' => true,
				'cek_op' => $cek_op
			);
			return Response::json($response);
		} catch (\Exception$e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function fetchjoblistIndex(Request $request)
	{
		try{
			$user = $request->get('emp_id');
			$tgl = date('Y-m-d');
			$cek_listjob_progress = db::connection('ympimis_2')->table('gs_joblist_logs')->where('nik_gs',$user)->where('status','!=',1)->get();
			$job_finished = db::connection('ympimis_2')->table('gs_joblist_logs')->where('nik_gs',$user)->where('status','=',2)->where( db::raw('date_format(created_at, "%Y-%m-%d")'), '=', $tgl)->orderby('updated_at','desc')->get();

			$cek_dailyjob = db::connection('ympimis_2')
			->select("SELECT
				gs_daily_jobs.operator_gs,
				gs_daily_jobs.id,
				gs_daily_jobs.area,
				gs_daily_jobs.list_job,
				st_gs1.img_before,
				IF(st_gs1.STATUS = 0, st_gs1.request_at, null) AS request_at,
				st_gs1.finished_at,
				st_gs1.STATUS 
				FROM
				gs_daily_jobs
				LEFT JOIN ( SELECT *, date_format( created_at, '%Y-%m-%d' ) AS tgl FROM gs_joblist_logs WHERE date_format( created_at, '%Y-%m-%d' ) = '".$tgl."' and nik_gs = '".$user."' ) st_gs1 ON gs_daily_jobs.list_job = st_gs1.list_job 
					WHERE
					gs_daily_jobs.dates = '".$tgl."' and gs_daily_jobs.status != 2 and gs_daily_jobs.operator_gs = '".$user."' 	ORDER BY request_at desc,gs_daily_jobs.created_at desc,area ASC,COALESCE(st_gs1.STATUS,0) desc
					");


				$cek_listjob = db::connection('ympimis_2')
				->select("
					SELECT
					* 
					FROM
					`gs_list_job_masters` 
					WHERE
					remark = 'weekly' 
					AND STATUS IS NULL 
					ORDER BY
					category ASC
					");


				$response = array(
					'status' => true,
					'cek_dailyjob' => $cek_dailyjob,
					'username' => $user,
					'cek_listjob_progress' => $cek_listjob_progress,
					'job_finished' => $job_finished,
					'cek_listjob' => $cek_listjob
				);
				return Response::json($response);
			}catch(\Exception $e) {
				$response = array(
					'status' => false,
					'message' => $e->getMessage()
				);
				return Response::json($response);
			}
		}

		public function updateJobGS(Request $request)
		{
			try {
				$tujuan_upload = 'images/ga/gs_control';
				$att_after = "";
				$att_before = "";


				if (count($request->file('attachment_foto_after')) > 0) {
					$file_after = $request->file('attachment_foto_after');
					$nama_after = $file_after->getClientOriginalName();
					$filename_after = pathinfo($nama_after, PATHINFO_FILENAME);
					$extension_after = pathinfo($nama_after, PATHINFO_EXTENSION);
					// $filename_after = md5($filename_after) . '.' . $extension_after;
					$att_after = 'job_after_'.$request->get('id').date('Ymd').'.'.$extension_after;

					$file_after->move($tujuan_upload, $att_after);
				}

				if (count($request->file('attachment_foto_before')) > 0) {

					$file_before = $request->file('attachment_foto_before');
					$nama_before = $file_before->getClientOriginalName();
					$filename_before = pathinfo($nama_before, PATHINFO_FILENAME);
					$extension_before = pathinfo($nama_before, PATHINFO_EXTENSION);
					// $filename_before = md5($filename_before) . '.' . $extension_before;
					$att_before = 'job_before_'.$request->get('id').date('Ymd').'.'.$extension_before;
					$file_before->move($tujuan_upload, $att_before);

				}

				if (count($request->file('attachment_foto_mix')) > 0) {

					$file_mix = $request->file('attachment_foto_mix');
					$nama_mix = $file_mix->getClientOriginalName();
					$filename_mix = pathinfo($nama_mix, PATHINFO_FILENAME);
					$extension_mix = pathinfo($nama_mix, PATHINFO_EXTENSION);
					// $filename_mix = md5($filename_mix) . '.' . $extension_mix;
					$att_mix = 'job_before_'.$request->get('id').date('Ymd').'.'.$extension_mix;
					$file_mix->move($tujuan_upload, $filename_mix);

				}

				if (count($request->file('attachment_foto_before')) == 0) {
					$att_before = null;

				}


				$firsts = date('Y-m-d');
				$op = db::connection('ympimis_2')->table('gs_actual_jobs')->where('employee_id',$request->get('nik_op'))->where( db::raw('date_format(created_at, "%Y-%m-%d")'), '=', $firsts)->orderBy('id', 'desc')->whereNull('finished_at')->limit(1)->first();

				$jobs = db::connection('ympimis_2')->table('gs_daily_jobs')->where('id',$request->get('id'))->whereNull('deleted_at')->first();

				if ($op->finished_at != null) {
					$response = array(
						'status' => false,
						'message' => 'Operator GS tidak ada jadwal bekerja'
					);
					return Response::json($response);

				}else{
					if ($request->get('status1') == 'before') {
						$input_job = DB::connection('ympimis_2')->table('gs_joblist_logs')->insert([
							'nik_gs' => $jobs->operator_gs,
							'name_gs' => $request->get('names'),
							'category' => $jobs->category,
							'lokasi' => $jobs->area,
							'list_job' => $jobs->list_job,
							'request_at' => date('Y-m-d H:i:s'),
							'finished_at' => null,
							'status' => 0,
							'img_before' => $att_before,
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s'),
						]);
						$update = DB::connection('ympimis_2')->table('gs_daily_jobs')->where('id',$request->get('id'))->update([
							'status' => 1,
							'updated_at' => date('Y-m-d H:i:s'),
						]); 
					}

					if ($request->get('status1') == 'mix') {
						$update_logs = DB::connection('ympimis_2')->table('gs_joblist_logs')->where('id',$op->user_id)->update([
							'img_before' => $att_mix,
							'updated_at' => date('Y-m-d H:i:s'),
						]); 
						$update = DB::connection('ympimis_2')->table('gs_daily_jobs')->where('id',$request->get('id'))->update([
							'status' => 1,
							'updated_at' => date('Y-m-d H:i:s'),
						]); 
					}

					$getdata = db::connection('ympimis_2')->table('gs_joblist_logs')->where('list_job',$jobs->list_job)->where('nik_gs',$request->get('nik_op'))->where( db::raw('date_format(created_at, "%Y-%m-%d")'), '=', $firsts)->whereNull('deleted_at')->first();


					if ($request->get('status1') == 'after') {

						$update_stop = DB::connection('ympimis_2')->table('gs_joblist_logs')->where('nik_gs',$request->get('nik_op'))->where('list_job',$jobs->list_job)->where( db::raw('date_format(created_at, "%Y-%m-%d")'), '=', $firsts)->update([
							'img_after' => $att_after,
							'status' => 2,
							'finished_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s')
						]); 

						$update_stop_finish = DB::connection('ympimis_2')->table('gs_daily_jobs')->where('id',$request->get('id'))->update([
							'status' => 2,
							'updated_at' => date('Y-m-d H:i:s')
						]); 

						$update_stop_finish = DB::connection('ympimis_2')->table('gs_daily_job_logs')->where('list_job',$jobs->list_job)->where('operator_gs',$request->get('nik_op'))->where('dates', '=', $firsts)->update([
							'status' => 2,
							'updated_at' => date('Y-m-d H:i:s')
						]); 

						$update_actual = DB::connection('ympimis_2')->table('gs_actual_jobs')->where('employee_id',$jobs->operator_gs)->where('user_id','=',$getdata->id)->whereNull('finished_at')->update([
							'finished_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s')
						]);

					}


					if ($request->get('status1') == 'after_before') {


						$update_stop_finish = DB::connection('ympimis_2')->table('gs_daily_jobs')->where('id',$request->get('id'))->update([
							'status' => 2,
							'updated_at' => date('Y-m-d H:i:s')
						]); 

						$update_stop_finish = DB::connection('ympimis_2')->table('gs_daily_job_logs')->where('list_job',$jobs->list_job)->where('operator_gs',$request->get('nik_op'))->where('dates', '=', $firsts)->update([
							'status' => 2,
							'updated_at' => date('Y-m-d H:i:s')
						]); 

						$update_actual = DB::connection('ympimis_2')->table('gs_actual_jobs')->where('employee_id',$jobs->operator_gs)->where('user_id','=',$getdata->id)->whereNull('finished_at')->update([
							'finished_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s')
						]);

						$update_stop = DB::connection('ympimis_2')->table('gs_joblist_logs')->where('nik_gs',$request->get('nik_op'))->where('list_job',$jobs->list_job)->where( db::raw('date_format(created_at, "%Y-%m-%d")'), '=', $firsts)->update([
							'img_before' => $att_before,
							'img_after' => $att_after,
							'status' => 2,
							'finished_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s')
						]); 

					}


					$data_off = db::connection('ympimis_2')->table('gs_actual_jobs')->where('employee_id', '=',$jobs->operator_gs)
					->where('status', '=', 'idle')->where( db::raw('date_format(created_at, "%Y-%m-%d")'), '=', $firsts)->whereNull('finished_at')->first();

					if (count($data_off) > 0) {

						$update_actual = DB::connection('ympimis_2')->table('gs_actual_jobs')->where('employee_id',$jobs->operator_gs)->where('status','=','idle')->where( db::raw('date_format(created_at, "%Y-%m-%d")'), '=', $firsts)->whereNull('finished_at')->update([
							'finished_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s'),
						]);

						$input_actual_job = DB::connection('ympimis_2')->table('gs_actual_jobs')->insert([
							'user_id' => $getdata->id,
							'employee_id' => $getdata->nik_gs,
							'status' => $getdata->category,
							'request_at' => $getdata->request_at,
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s'),
						]);

					}else{
						$data_op_off = db::connection('ympimis_2')->table('gs_actual_jobs')->where('employee_id', '=',$jobs->operator_gs)
						->where('status', '!=', 'idle')->where( db::raw('date_format(created_at, "%Y-%m-%d")'), '=', $firsts)->whereNull('finished_at')->get();

						$data_op_off2 = db::connection('ympimis_2')->table('gs_actual_jobs')->where('employee_id', '=',$jobs->operator_gs)
						->where('status', '!=', 'idle')->where( db::raw('date_format(created_at, "%Y-%m-%d")'), '=', $firsts)->where('user_id','=',$getdata->id)->first();

						if (count($data_op_off) == 0) {
							$input_actual_job_idle = DB::connection('ympimis_2')->table('gs_actual_jobs')
							->where(db::raw('date_format(created_at, "%Y-%m-%d")'), '=', $firsts)->insert([
								'employee_id' => $getdata->nik_gs,
								'status' => 'idle',
								'request_at' => date('Y-m-d H:i:s'),
								'created_at' => date('Y-m-d H:i:s'),
								'updated_at' => date('Y-m-d H:i:s'),
							]);
						}

						if (count($data_op_off2) == 0) {
							$input_actual_job2 = DB::connection('ympimis_2')->table('gs_actual_jobs')->insert([
								'user_id' => $getdata->id,
								'employee_id' => $getdata->nik_gs,
								'status' => $getdata->category,
								'request_at' => $getdata->request_at,
								'created_at' => date('Y-m-d H:i:s'),
								'updated_at' => date('Y-m-d H:i:s'),
							]);
						}
					}

					$response = array(
						'status' => true,
						'message' => 'Upload Pekerjaan berhasil tersimpan',
					);
					return Response::json($response);
				}


			} catch (\Exception$e) {
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}

		public function indexProcesGS()
		{
			return view('ga_control.gs_control.index_list', array(
				'role_code' => Auth::user()->role_code

			))->with('page', 'Process GS')->with('head', 'Process GS');
		}


		public function inputReasonPauseGS(Request $request)
		{
			try {

				$tgl = date('Y-m-d');

				$getdata = db::connection('ympimis_2')->table('gs_daily_jobs')->where('id',$request->get('id'))->where( 'dates', '=', $tgl)->whereNull('deleted_at')->first();

				$getJobOn = db::connection('ympimis_2')->table('gs_joblist_logs')->where('list_job',$getdata->list_job)->where('nik_gs',$request->get('op_nik'))->where( db::raw('date_format(request_at, "%Y-%m-%d")'), '=', $tgl)->whereNull('finished_at')->get();

				$getJobUrgent = db::connection('ympimis_2')->table('gs_joblist_logs')->where('nik_gs',$request->get('op_nik'))->where( db::raw('date_format(request_at, "%Y-%m-%d")'), '=', $tgl)->whereNull('finished_at')->get();

				if ($request->get('reason') == "Istirahat") {

					for ($i=0; $i < count($getJobOn) ; $i++) {
						$update_pause2 = DB::connection('ympimis_2')->table('gs_joblist_logs')->where('list_job','=',$getJobOn[$i]->list_job)->where('nik_gs',$request->get('op_nik'))->where( db::raw('date_format(request_at, "%Y-%m-%d")'), '=', $tgl)->whereNull('finished_at')->update([
							'status' => 4,
							'updated_at' => date('Y-m-d H:i:s')
						]);

						$update_act = DB::connection('ympimis_2')->table('gs_actual_jobs')->where('user_id','=',$getJobOn[$i]->id)->where('employee_id','=',$getJobOn[$i]->nik_gs)->where( db::raw('date_format(request_at, "%Y-%m-%d")'), '=', $tgl)->whereNull('finished_at')->update([
							'finished_at' => date('Y-m-d H:i:s')
						]);

						$input_pause = DB::connection('ympimis_2')->table('gs_job_temps')->insert([
							'job_id' => $getJobOn[$i]->id,
							'emp_id' => $request->get('op_nik'),
							'start_time' => date('Y-m-d H:i:s'),
							'reason' => $request->get('reason'),
							'detail' => $request->get('reasondetail'),
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s'),
						]);
					// $idsk = db::connection('ympimis_2')->table('gs_job_temps')->where('emp_id',$request->get('op_nik'))->where( db::raw('date_format(created_at, "%Y-%m-%d")'), '=', $tgl)->where( 'job_id', '=', $getJobOn[$i]->id)->whereNull('end_time')->limit(1)->first();

					}

					$op = db::connection('ympimis_2')->table('gs_actual_jobs')->where('employee_id',$request->get('op_nik'))->where( db::raw('date_format(created_at, "%Y-%m-%d")'), '=', $tgl)->where( 'status', '!=', 'idle')->orderBy('id', 'desc')->whereNull('finished_at')->limit(1)->first();

					if ($op == null) {
						$input_idle = DB::connection('ympimis_2')->table('gs_actual_jobs')->insert([							
							'employee_id' => $request->get('op_nik'),
							'status' => 'idle', 
							'reason' => 'Istirahat', 
							'request_at' => date('Y-m-d H:i:s'),
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s'),
						]);
					}


				}else if ($request->get('reason') == 'Pekerjaan Lain Urgent') {
					for ($i=0; $i < count($getJobUrgent) ; $i++) {
						
						$update_pause2 = DB::connection('ympimis_2')->table('gs_joblist_logs')->where('id','=',$getJobUrgent[$i]->id)->where( db::raw('date_format(request_at, "%Y-%m-%d")'), '=', $tgl)->whereNull('finished_at')->update([
							'status' => 4,
							'updated_at' => date('Y-m-d H:i:s')
						]);

						$update_act2 = DB::connection('ympimis_2')->table('gs_actual_jobs')->where('user_id','=',$getJobUrgent[$i]->id)->where('employee_id','=',$getJobUrgent[$i]->nik_gs)->where( db::raw('date_format(request_at, "%Y-%m-%d")'), '=', $tgl)->whereNull('finished_at')->update([
							'finished_at' => date('Y-m-d H:i:s')
						]);

						$input_pause = DB::connection('ympimis_2')->table('gs_job_temps')->insert([
							'job_id' => $getJobUrgent[$i]->id,
							'emp_id' => $request->get('op_nik'),
							'start_time' => date('Y-m-d H:i:s'),
							'category' => 'Lain-Lain',
							'list_job' => $request->get('reasondetail'),
							'reason' => $request->get('reason'),
							'detail' => $request->get('reasondetail'),
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s'),
						]);
					}

					$input_job_lain = DB::connection('ympimis_2')->table('gs_joblist_logs')->insert([
						'nik_gs' => $request->get('op_nik'),
						'name_gs' => $request->get('op_name'),
						'category' => 'Lain-Lain',
						'list_job' => $request->get('reasondetail'),
						'request_at' => date('Y-m-d H:i:s'),
						'finished_at' => null,
						'status' => 0,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
					]);

					$getJobUrgent2 = db::connection('ympimis_2')->table('gs_joblist_logs')->where('list_job',$request->get('reasondetail'))->where( db::raw('date_format(request_at, "%Y-%m-%d")'), '=', $tgl)->whereNull('finished_at')->first();

					$input_idle2 = DB::connection('ympimis_2')->table('gs_actual_jobs')->insert([
						'user_id' => $getJobUrgent2->id,
						'employee_id' => $request->get('op_nik'),
						'status' => 'Lain-Lain',
						'request_at' => date('Y-m-d H:i:s'),
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
					]);

					$input_job_lain2 = DB::connection('ympimis_2')->table('gs_daily_jobs')->insert([
						'operator_gs' => $request->get('op_nik'),
						'names' => $request->get('op_name'),
						'category' => 'Lain-Lain',
						'list_job' => $request->get('reasondetail'),
						'dates' => date('Y-m-d'),
						'status' => 0,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
					]);

					$input_job_lain2_log = DB::connection('ympimis_2')->table('gs_daily_job_logs')->insert([
						'operator_gs' => $request->get('op_nik'),
						'names' => $request->get('op_name'),
						'category' => 'Lain-Lain',
						'list_job' => $request->get('reasondetail'),
						'dates' => date('Y-m-d'),
						'status' => 0,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
					]);

				}

				$response = array(
					'status' => true,
					'message' => 'Pause Pekerjaan berhasil',
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

		public function UpdatePauseGS(Request $request)
		{
			try {
				$tgl = date('Y-m-d');

				$getdata = db::connection('ympimis_2')->table('gs_daily_jobs')->where('id',$request->get('id'))->where( 'dates', '=', $tgl)->whereNull('deleted_at')->first();

				$getdata2 = db::connection('ympimis_2')->table('gs_joblist_logs')->where('list_job',$getdata->list_job)->where('nik_gs',$getdata->operator_gs)->where( db::raw('date_format(request_at, "%Y-%m-%d")'), '=', $tgl)->whereNull('deleted_at')->first();

				$update_pause = DB::connection('ympimis_2')->table('gs_job_temps')->where('job_id',$getdata2->id)->where( db::raw('date_format(start_time, "%Y-%m-%d")'), '=', $tgl)->whereNull('end_time')->update([
					'end_time' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				]);

				$update_pause2 = DB::connection('ympimis_2')->table('gs_joblist_logs')->where('list_job',$getdata->list_job)->where('nik_gs',$getdata->operator_gs)->where( db::raw('date_format(request_at, "%Y-%m-%d")'), '=', $tgl)->whereNull('finished_at')->update([
					'status' => 0,
					'updated_at' => date('Y-m-d H:i:s')
				]);

				$update_act = DB::connection('ympimis_2')->table('gs_actual_jobs')->where('status','idle')->where('employee_id',$getdata->operator_gs)->where( db::raw('date_format(request_at, "%Y-%m-%d")'), '=', $tgl)->whereNull('finished_at')->update([
					'finished_at' => date('Y-m-d H:i:s')
				]);

				$input_start = DB::connection('ympimis_2')->table('gs_actual_jobs')->insert([
					'user_id' => $getdata2->id,
					'employee_id' => $getdata2->nik_gs,
					'status' => $getdata2->category,
					'request_at' => date('Y-m-d H:i:s'),
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				]);

				$response = array(
					'status' => true,
					'message' => 'Pekerjaan berhasil diupdate',
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

		public function fetchGsAktual(Request $request)
		{

			if (strlen($request->get('date')) > 0) {
				$tgl = date('Y-m-d', strtotime($request->get('date')));
				$jam = date('Y-m-d H:i:s', strtotime($request->get('date') . date('H:i:s')));
				if ($jam > date('Y-m-d', strtotime($tgl)) . ' 00:00:01' && $jam < date('Y-m-d', strtotime($tgl)) . ' 02:00:00' && $tgl == date('Y-m-d', strtotime($tgl))) {
					$nextday = date('Y-m-d', strtotime($tgl));
					$yesterday = date('Y-m-d', strtotime($tgl . " -1 days"));
				} else {
					$nextday = date('Y-m-d', strtotime($tgl . " +1 days"));
					$yesterday = date('Y-m-d', strtotime($tgl));
				}
			} else {
				$tgl = date("Y-m-d");
				$jam = date('Y-m-d H:i:s');
				if ($jam > date('Y-m-d', strtotime($tgl)) . ' 00:00:01' && $jam < date('Y-m-d', strtotime($tgl)) . ' 02:00:00') {
					$nextday = date('Y-m-d');
					$yesterday = date('Y-m-d', strtotime("-1 days"));
				} else {
					$nextday = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
					$yesterday = date('Y-m-d');
				}
			}

			$data_op = DB::connection('ympimis_2')
			->select("SELECT
				employee_id,
				employee_name
				FROM
				gs_operators
				WHERE role = 'GS'");

			$data_all = DB::connection('ympimis_2')
			->select("SELECT
				gs_actual_jobs.id,
				st_gs1.id,
				gs_actual_jobs.employee_id,
				gs_operators.employee_name,
				gs_actual_jobs.`status`,
				IF
				( gs_actual_jobs.reason = 'Istirahat' and gs_actual_jobs.STATUS = 'idle', gs_actual_jobs.reason, st_gs1.list_job ) AS list_job,
				gs_actual_jobs.request_at,
				gs_actual_jobs.finished_at,
				timestampdiff( SECOND, gs_actual_jobs.request_at, gs_actual_jobs.finished_at )/60 AS time,
				IFNULL(
					DATE_FORMAT( gs_actual_jobs.finished_at, '%M %d %Y %H:%i:%s' ),
					DATE_FORMAT( NOW(), '%M %d %Y %H:%i:%s' )) AS end_job,
				DATE_FORMAT( gs_actual_jobs.request_at, '%M %d %Y %H:%i:%s' ) AS dt
				FROM
				gs_actual_jobs
				LEFT JOIN ( SELECT * FROM gs_joblist_logs ) st_gs1 ON gs_actual_jobs.user_id = st_gs1.id 
				LEFT JOIN gs_operators ON gs_operators.employee_id = gs_actual_jobs.employee_id
				WHERE gs_actual_jobs.request_at >= '".$yesterday." 06:00:00' && gs_actual_jobs.request_at <= '".$nextday." 02:00:00'
				");

			$data_actual = DB::connection('ympimis_2')
			->select("SELECT
				gs_operators.employee_id,
				concat(
					SPLIT_STRING ( employee_name, ' ', 1 ),
					' ',
					SPLIT_STRING ( employee_name, ' ', 2 )) AS `name`,
				COALESCE ( st_gs1.time / 60, 0 ) AS st_gs1s,
				COALESCE ( st_gs2.time / 60, 0 ) AS st_gs2s,
				COALESCE ( st_gs3.time / 60, 0 ) AS st_gs3s,
				COALESCE ( st_gs4.time / 60, 0 ) AS st_gs4s,
				COALESCE ( st_gs5.time / 60, 0 ) AS st_gs5s
				FROM
				gs_operators
				LEFT JOIN ( SELECT `employee_id`, sum( timestampdiff( SECOND, request_at, finished_at )) AS time FROM gs_actual_jobs WHERE `status` = 'Area GS 1' && created_at >= '" . $yesterday . " 06:00:00' && created_at <= '" . $nextday . " 02:00:00' GROUP BY `employee_id` ) st_gs1 ON gs_operators.employee_id = st_gs1.`employee_id`
					LEFT JOIN ( SELECT `employee_id`, sum( timestampdiff( SECOND, request_at, finished_at )) AS time FROM gs_actual_jobs WHERE `status` = 'Area GS 2' && created_at >= '" . $yesterday . " 06:00:00' && created_at <= '" . $nextday . " 02:00:00' GROUP BY `employee_id` ) st_gs2 ON gs_operators.employee_id = st_gs2.`employee_id`
					LEFT JOIN ( SELECT `employee_id`, sum( timestampdiff( SECOND, request_at, finished_at )) AS time FROM gs_actual_jobs WHERE `status` = 'Area GS 3' && created_at >= '" . $yesterday . " 06:00:00' && created_at <= '" . $nextday . " 02:00:00' GROUP BY `employee_id` ) st_gs3 ON gs_operators.employee_id = st_gs3.`employee_id`
					LEFT JOIN ( SELECT `employee_id`, sum( timestampdiff( SECOND, request_at, finished_at )) AS time FROM gs_actual_jobs WHERE `status` = 'Lain-Lain' && created_at >= '" . $yesterday . " 06:00:00' && created_at <= '" . $nextday . " 02:00:00' GROUP BY `employee_id` ) st_gs4 ON gs_operators.employee_id = st_gs4.`employee_id`
					LEFT JOIN ( SELECT `employee_id`, sum( timestampdiff( SECOND, request_at, finished_at )) AS time FROM gs_actual_jobs WHERE `status` = 'Idle' && created_at >= '" . $yesterday . " 06:00:00' && created_at <= '" . $nextday . " 02:00:00' GROUP BY `employee_id` ) st_gs5 ON gs_operators.employee_id = st_gs5.`employee_id`
					WHERE
					role = 'GS'");

				$response = array(
					'status' => true,
					'operators_time' => $data_actual,
					'data_op' => $data_op,
					'data_all' => $data_all
				);
				return Response::json($response);

			}


			public function indexMonitoringGS(Request $request){

				$getop = db::connection('ympimis_2')->table('gs_operators')->where('role','GS')->whereNull('deleted_at')->get();

				return view('ga_control.gs_control.gs_daily_check',  
					array(
						'title' => 'GS Monitoring Daily', 
						'title_jp' => '',
						'getop' => $getop
					)
				)->with('page', 'GS Monitoring');
			}


			public function fetchMonitoringGSAll(Request $request){

				try{

					if ($request->get('date_from') == null) {
						$tgl = date('Y-m-d');
					}else{
						$tgl = date('Y-m-d', strtotime($request->get('date_from')));
					}

					$data_all = DB::connection('ympimis_2')
					->select("SELECT
						operator_gs,
						names,
						sum( CASE WHEN `status` = 0 || `status` = 1 THEN 1 ELSE 0 END ) AS jumlah_belum,
						sum( CASE WHEN `status` = 2 THEN 1 ELSE 0 END ) AS jumlah_sudah 
						FROM
						`gs_daily_job_logs`
						WHERE dates = '".$tgl."'	
						GROUP BY
						operator_gs,
						names
						");


					$data_set = DB::connection('ympimis_2')
					->select("SELECT
						*
						FROM
						`gs_daily_job_logs`
						WHERE dates = '".$tgl."'
						");

					$gs_jobs = DB::CONNECTION('ympimis_2')
					->table('gs_joblist_logs')
					->select('id', 'nik_gs', 'name_gs', 'category', 'lokasi', 'list_job', 'request_at', 'finished_at', 'img_before', 'img_after', 'status', DB::RAW(' sum(
						timestampdiff( SECOND, request_at, finished_at )) AS time '), db::raw('date_format(finished_at, "%Y-%m-%d") AS date_finish'), 'created_at', 'updated_at', 'deleted_at')->where(db::raw('date_format(finished_at, "%Y-%m-%d")'), '=', $tgl);
					$gs_jobs = $gs_jobs->groupBy('id', 'nik_gs', 'name_gs', 'category', 'lokasi', 'list_job', 'request_at', 'finished_at', 'img_before', 'img_after', 'status','created_at','deleted_at','updated_at')->orderBy('finished_at', 'desc')->get();

					$gs_data = array();
					$dataworkall1 = [];


					foreach ($gs_jobs as $gs_datas) {


						array_push($gs_data, [
							'id' => $gs_datas->id,
							'nik_gs' => $gs_datas->nik_gs,
							'name_gs' => $gs_datas->name_gs,
							'category' => $gs_datas->category,
							'lokasi' => $gs_datas->lokasi,
							'list_job' => $gs_datas->list_job,
							'img_before' => $gs_datas->img_before,
							'img_after' => $gs_datas->img_after,
							'request_at' => $gs_datas->request_at,
							'finished_at' => $gs_datas->finished_at,
							'status' => $gs_datas->status,
							'deleted_at' => $gs_datas->deleted_at,
							'created_at' => $gs_datas->created_at,
							'updated_at' => $gs_datas->updated_at,
							'times' => $gs_datas->time,
							'date_finish' => $gs_datas->date_finish,
						]);


						$work2 = DB::CONNECTION('ympimis_2')->SELECT("SELECT *,TIMESTAMPDIFF(second,start_time,end_time)/60 as duration FROM `gs_job_temps` where list_job = '".$gs_datas->list_job."' and job_id = '".$gs_datas->id."' and date_format(end_time, '%Y-%m-%d') = '".$tgl."'");

						if (count($work2) > 0) {
							foreach ($work2 as $val) {
								$datawork1 = array(
									'job_id' => $val->job_id,
									'list_job' => $val->list_job,
									'emp_id' => $val->emp_id,
									'start_time' => $val->start_time,
									'end_time' => $val->end_time,
									'duration' => $val->duration,
									'reason' => $val->reason, );
								$dataworkall1[] = join("+",$datawork1);
							}
						}
					}

					$response = array(
						'status' => true,
						'datas' => $data_all,
						'gs_jobs' => $gs_data,
						'dataworkall' => $dataworkall1,
						'data_set' => $data_set
					);

					return Response::json($response);
				}

				catch (\Exception $e) {
					$response = array(
						'status' => false,
						'message' => $e->getMessage(),
					);
					return Response::json($response);
				}
			}

			public function indexScheduleGS(){

				return view('ga_control.gs_control.index_schedule_shift', array(
					'title' => 'GS Schedule',
					'title_jp' => ''
				))->with('page', 'GS Schedule');

			}

			public function createJobNewGS(Request $request)
			{
				try {

					$data_double = db::connection('ympimis_2')->table('gs_daily_jobs')->where('operator_gs', '=',$request->get('op_nik'))
					->where('list_job', '=', $request->get('joblist'))->where('area', '=', $request->get('lokasi'))->get();

					if (count($data_double) == 0) {

						$input_daily_job = DB::connection('ympimis_2')->table('gs_daily_jobs')->insert([
							'category' => $request->get('category'),
							'area' => $request->get('lokasi'),
							'list_job' => $request->get('joblist'),
							'operator_gs' => $request->get('op_nik'),
							'names' => $request->get('op_name'),
							'dates' => date('Y-m-d'),
							'status' => 0,
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s')
						]);

						$input_daily_job_logs = DB::connection('ympimis_2')->table('gs_daily_job_logs')->insert([
							'category' => $request->get('category'),
							'area' => $request->get('lokasi'),
							'list_job' => $request->get('joblist'),
							'operator_gs' => $request->get('op_nik'),
							'names' => $request->get('op_name'),
							'dates' => date('Y-m-d'),
							'status' => 0,
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s')
						]);

						$update_data = DB::connection('ympimis_2')->table('gs_list_job_masters')->where('list_job',$request->get('joblist'))->where('op_nik',$request->get('op_nik'))->where('remark','weekly')->update([
							'status' => 1,
							'updated_at' => date('Y-m-d H:i:s')
						]);

						$response = array(
							'status' => true,
							'message' => 'Tambah Pekerjaan berhasil tersimpan',
						);
						return Response::json($response);
					}

				} catch (\Exception$e) {
					$response = array(
						'status' => false,
						'message' => $e->getMessage(),
					);
					return Response::json($response);
				}
			}

			public function indexOpGSJob()
			{
				$title = "Master Daily GS Joblist";
				$title_jp = "";
				$emp = EmployeeSync::where('group', 'General Service Group')->whereNull('end_date')->get();

				$categorys = DB::connection('ympimis_2')->Select("SELECT DISTINCT
					category
					FROM
					`gs_list_job_masters`
					ORDER BY category ASC
					");

				$joblist = DB::connection('ympimis_2')->Select("SELECT
      *
					FROM
					`gs_list_job_masters`
					ORDER BY category ASC
					");

				$get_area = DB::connection('ympimis_2')->Select("SELECT DISTINCT category,area
					FROM
					`gs_list_job_masters`
					");

				return view('ga_control.gs_control.data_op_joblist', array(
					'title' => $title,
					'title_jp' => $title_jp,
					'emps' => $emp,
					'category' => $categorys,
					'job' => $joblist,
					'area' => $get_area,
				))->with('page', 'Master Daily GS');
			}

			public function inputJobListGS(Request $request)
			{
				try
				{
					$op_nik = $request->get('op_nik');
					$emp_name = $request->get('emp_name');
					$joblist = explode(',', $request->get('joblist'));
					$areas1 = explode(',', $request->get('areas1'));
					$locs1 = explode(',', $request->get('locs1'));

					for ($i = 0; $i < count($joblist); $i++) {
						$data_job = db::connection('ympimis_2')->table('gs_daily_jobs')->where('list_job', $joblist[$i])->where('operator_gs', $emp_name)->where('area', $locs1[$i])->whereNull('deleted_at')->first();
						if ($data_job == null) {
							$input_job = DB::connection('ympimis_2')->table('gs_daily_jobs')->insert([
								'operator_gs' => $op_nik,
								'names' => $emp_name,
								'category' => $areas1[$i],
								'area' => $locs1[$i],
								'dates' => date('Y-m-d'),
								'list_job' => $joblist[$i],
								'status' => 0,
								'created_at' => date('Y-m-d H:i:s'),
								'updated_at' => date('Y-m-d H:i:s'),
							]);
							$input_job = DB::connection('ympimis_2')->table('gs_daily_jobs')->insert([
								'operator_gs' => $op_nik,
								'names' => $emp_name,
								'category' => $areas1[$i],
								'area' => $locs1[$i],
								'list_job' => $joblist[$i],
								'remark' => 'jobs',
								'created_at' => date('Y-m-d H:i:s'),
								'updated_at' => date('Y-m-d H:i:s'),
							]);
						}
					}

					$response = array(
						'status' => true,
					);
					return Response::json($response);

				} catch (QueryException $e) {
					$response = array(
						'status' => false,
						'message' => $e->getMessage(),
					);
					return Response::json($response);

				}
			}

			public function fetchJobGSProcess(Request $request)
			{
				try {
					$gs_jobs = db::connection('ympimis_2')
					->select("SELECT
						gs_daily_jobs.operator_gs,
						gs_daily_jobs.names,
						COUNT( gs_daily_jobs.list_job ) AS total_list 
						FROM
						gs_daily_jobs
						LEFT JOIN gs_operators ON gs_operators.employee_id = gs_daily_jobs.operator_gs 
						WHERE gs_daily_jobs.remark = 'jobs'
						GROUP BY
						gs_daily_jobs.operator_gs,
						gs_daily_jobs.names 
						ORDER BY
						gs_daily_jobs.created_at DESC
						");

					$gs_job_daily = db::connection('ympimis_2')
					->select("SELECT
    *
						FROM
						gs_daily_jobs
						WHERE remark = 'jobs'
						ORDER BY operator_gs ASC
						");

					$response = array(
						'status' => true,
						'message' => 'Success Get Data',
						'gs_jobs' => $gs_jobs,
						'gs_job_daily' => $gs_job_daily,
					);
					return Response::json($response);
				} catch (\Exception$e) {
					$response = array(
						'status' => false,
						'message' => $e->getMessage(),
					);
					return Response::json($response);
				}
			}

			public function deleteDataGS(Request $request)
			{
				try {
					$request_id = $request->get('id');

					$jobs = db::connection('ympimis_2')->table('gs_daily_jobs')->where('list_job', $request_id)->where('operator_gs', $request->get('emps'))->whereNull('deleted_at')->Delete();

					$response = array(
						'status' => true,
						'message' => 'Success Hapus Pekerjaan',
					);
					return Response::json($response);
				} catch (\Exception$e) {
					$response = array(
						'status' => false,
						'message' => $e->getMessage(),
					);
					return Response::json($response);
				}
			}


			public function indexResumeGS()
			{

				$title = "GS Workload Resume";
				$title_jp = "まとめ";
				$areas = db::connection('ympimis_2')->table('gs_list_job_masters')->select('category')
				->distinct()->orderBy('category', 'ASC')->get();

				$pics = db::connection('ympimis_2')->table('gs_operators')->where('role', '=', 'GS')->whereNull('deleted_at')->get();

				return view('ga_control.gs_control.gs_resume', array(
					'title' => $title,
					'title_jp' => $title_jp,
					'pics' => $pics,
					'area' => $areas,
				))->with('page', 'GS Resume')->with('head', 'GS Resume');
			}

			public function fetchResumeGS(Request $request)
			{

				try {
					$gs_jobs = DB::CONNECTION('ympimis_2')
					->table('gs_joblist_logs')
					->select('id', 'nik_gs', 'name_gs', 'category', 'lokasi', 'list_job', 'request_at', 'finished_at', 'img_before', 'img_after', 'status', DB::RAW(' sum(
						timestampdiff( SECOND, request_at, finished_at )) AS time '), db::raw('date_format(finished_at, "%Y-%m-%d") AS date_finish'), 'created_at', 'updated_at', 'deleted_at');

					$first = date('Y-m-01');
					$last = date('Y-m-t');

					if ($request->get('date_from') != "") {
						$first = date('Y-m-d', strtotime($request->get('date_from')));
						$last = date('Y-m-d', strtotime($request->get('date_to')));
						$dateTitleFirst = date("d M Y", strtotime($request->get('date_from')));
						$dateTitleLast = date("d M Y", strtotime($request->get('date_to')));
					} else {
						$first = date('Y-m-d');
						$last = date('Y-m-d');
						$dateTitleFirst = date("d M Y", strtotime(date('Y-m-d')));
						$dateTitleLast = date("d M Y", strtotime(date('Y-m-d')));
					}

					$gs_jobs = $gs_jobs->where('status', '=', 2)
					->where(db::raw('date_format(finished_at, "%Y-%m-%d")'), '>=', $first)
					->where(db::raw('date_format(finished_at, "%Y-%m-%d")'), '<=', $last);

					$gs_jobs = $gs_jobs->groupBy('id', 'nik_gs', 'name_gs', 'category', 'lokasi', 'list_job', 'request_at', 'finished_at', 'img_before', 'img_after', 'status','created_at','updated_at','deleted_at')->orderBy('finished_at', 'desc')->get();

					$gs_data = array();

					$dataworkall = [];

					foreach ($gs_jobs as $gs_datas) {

						array_push($gs_data, [
							'id' => $gs_datas->id,
							'nik_gs' => $gs_datas->nik_gs,
							'name_gs' => $gs_datas->name_gs,
							'category' => $gs_datas->category,
							'lokasi' => $gs_datas->lokasi,
							'list_job' => $gs_datas->list_job,
							'img_before' => $gs_datas->img_before,
							'img_after' => $gs_datas->img_after,
							'request_at' => $gs_datas->request_at,
							'finished_at' => $gs_datas->finished_at,
							'status' => $gs_datas->status,
							'deleted_at' => $gs_datas->deleted_at,
							'created_at' => $gs_datas->created_at,
							'updated_at' => $gs_datas->updated_at,
							'times' => $gs_datas->time,
							'date_finish' => $gs_datas->date_finish,
							'jum' => 1,
						]);

						$work = DB::CONNECTION('ympimis_2')->SELECT("SELECT *,TIMESTAMPDIFF(second,start_time,end_time)/60 as duration FROM `gs_job_temps` where job_id = '".$gs_datas->id."' and date_format(end_time, '%Y-%m-%d') >= '".$first."'and date_format(end_time, '%Y-%m-%d') >= '".$last."'");

						if (count($work) > 0) {
							foreach ($work as $val) {
								$datawork = array(
									'job_id' => $val->job_id,
									'list_job' => $val->list_job,
									'emp_id' => $val->emp_id,
									'start_time' => $val->start_time,
									'end_time' => $val->end_time,
									'duration' => $val->duration,
									'reason' => $val->reason, );
								$dataworkall[] = join("+",$datawork);
							}
						}

					}



					$weekly_calendars = db::table('weekly_calendars')->where('week_date', '>=', $first)
					->where('week_date', '<=', $last)
					->select('week_date', db::raw('date_format(week_date, "%d") as day_date'))
					->orderBy('week_date', 'ASC')
					->get();

					$response = array(
						'status' => true,
						'weekly_calendars' => $weekly_calendars,
						'translations' => $gs_data,
						'dateTitleFirst' => $dateTitleFirst,
						'dateTitleLast' => $dateTitleLast,
						'dataworkall' => $dataworkall
					);
					return Response::json($response);
				} catch (\Exception$e) {
					$response = array(
						'status' => false,
						'message' => $e->getMessage(),
					);
					return Response::json($response);
				}
			}


			public function indexAktualGS()
			{
				return view('ga_control.gs_control.gs_actual')->with('page', 'Actual GS')->with('head', 'Actual GS');
			}

			public function exportGSAll(Request $request){
				$tanggal = "";				

				if (strlen($request->get('date_from')) > 0)
				{

					$tanggal = date('Y-m-d', strtotime($request->get('date_from')));

				}


				$detail = DB::connection('ympimis_2')
				->select("SELECT
					*, date_format( request_at, '%Y-%m-%d' ) as datess,SPLIT_STRING ( img_before, '.', 1 ) as imgs_before,SPLIT_STRING ( img_after, '.', 1 ) as imgs_after
					FROM
					`gs_joblist_logs` 
					WHERE
					date_format( request_at, '%Y-%m-%d' ) = '".$tanggal."' AND STATUS = 2
					");

				$data = array(
					'detail' => $detail
				);

				ob_clean();

				Excel::create('Report Daily GS '.$request->get('date_from'), function($excel) use ($data){
					$excel->sheet('Data', function($sheet) use ($data) {
						return $sheet->loadView('ga_control.gs_control.resume_gs', $data);
					});

					$lastrow = $excel->getActiveSheet()->getHighestRow();    
					$excel->getActiveSheet()->getStyle('A1:G'.$lastrow)->getAlignment()->setWrapText(true); 

				})->export('xlsx');
			}




		}
