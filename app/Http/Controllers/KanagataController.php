<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Libraries\ActMLEasyIf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\User;
use Response;
use App\Destination;
use App\SendingAppRequest;
use App\SendingAppMaster;
use File;
use PDF;
use Validator;
use App\PelaporanKanagataApproval;
use App\CodeGenerator;
use App\PelaporanKanagataRequest;
use App\Approver;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\SendingAppDetail;
use App\EmployeeSync;
use App\Employee;
use App\MaterialPlantDataList;
use App\MpKanagata;
use App\KanagataGmcMaster;
// use App\SandingCheckFinding;
// use App\SandingCheck;


class KanagataController extends Controller
{

	public function kanagataControl(){

		$dept = db::select("select DISTINCT department from employee_syncs");
		$pic = PelaporanKanagataApproval::select('approver_name')
		->distinct()->get();
		$gmc = db::select("select DISTINCT gmc_material from kanagata_gmc_masters");
		$emp = EmployeeSync::where('employee_id', Auth::user()->username)->select('employee_id', 'name', 'position', 'department', 'section', 'group')->first();
		$user = EmployeeSync::where('employee_id',Auth::user()->username)->first();

		return view('kanagata.kanagata_control',  
			array(
				'title' => 'Kanagata Monitoring & Control', 
				'title_jp' => '投資監視・管理',
				'department' => $dept,
				'gmc' => $gmc,
				'employee' => $emp,
				'pic' => $pic,
				'user' => $user,
				'role_code' => Auth::user()->role_code

			)
		)->with('page', 'Kanagata Control');

	}

	public function indexHistoryLifeshoot(){

		// $dept = db::select("select DISTINCT department from employee_syncs");
		// $pic = PelaporanKanagataApproval::select('approver_name')
		// ->distinct()->get();
		// $gmc = db::select("select DISTINCT gmc_material from kanagata_gmc_masters");
		// $emp = EmployeeSync::where('employee_id', Auth::user()->username)->select('employee_id', 'name', 'position', 'department', 'section', 'group')->first();
		$materials = DB::SELECT("SELECT DISTINCT
			( gmc_material ),
			desc_material
			FROM
			pelaporan_kanagata_requests
			ORDER BY
			desc_material ASC");


		return view('kanagata.history_gmc',  
			array(
				'title' => 'History Kanagata', 
				'title_jp' => '投資監視・管理',
				'materials' => $materials
			)
		)->with('page', 'History GMC');

	}

	



	public function getNotifKanagata()
	{
		if (Auth::user() !== null) {
			$user = strtoupper(Auth::user()->username);
			$name = Auth::user()->name;
			$role = Auth::user()->role_code;

			$notif = 0;

			$tanggungan = db::select("
				SELECT DISTINCT
				pelaporan_kanagata_requests.request_id 
				FROM
				pelaporan_kanagata_requests
				JOIN pelaporan_kanagata_approvals ON pelaporan_kanagata_requests.request_id = pelaporan_kanagata_approvals.request_id 
				WHERE
				CONCAT( pelaporan_kanagata_approvals.`status` ) IS NULL 
				AND pelaporan_kanagata_requests.remark != 'Rejected'
				");

			$kanagata_request_id = [];
			foreach($tanggungan as $tag){
				array_push($kanagata_request_id, $tag->request_id);
			}

        // dd($ticket);

			$jumlah_tanggungan = 0;

			for ($i=0; $i < count($kanagata_request_id); $i++) { 
            // var_dump($ticket[$i]);
				$tanggungan_user = db::select("
					SELECT
					( SELECT approver_id FROM pelaporan_kanagata_approvals a WHERE a.id = ( pelaporan_kanagata_approvals.id ) ) next 
					FROM
					pelaporan_kanagata_approvals 
					WHERE
					`status` IS NULL 
					AND request_id = '".$kanagata_request_id[$i]."' 
					ORDER BY
					id ASC 
					LIMIT 1
					");

				if (count($tanggungan_user) > 0 && $tanggungan_user[0]->next == $user) {
					$jumlah_tanggungan += 1;
				}
			}

			$notif = $jumlah_tanggungan;


			return $notif;
		}
	}

	
	public function getGmc( Request $request)
	{
		try {
			// $gmc = DB::SELECT("select material_number, material_description from kanagata_gmc_masters where
			// 	`material_number` = '".$request->get('gmc')."'");

			$lifetime = DB::SELECT("select gmc_material, lifetime,part_name,desc_material from kanagata_gmc_masters where
				`gmc_material` = '".$request->get('gmc')."'");

			if (count($lifetime) > 0) {
				$response = array(
					'status' => true,
					'message' => 'Success',
					'lifetime' => $lifetime
				);
				return Response::json($response);
			}else{
				$response = array(
					'status' => false,
					'message' => 'Failed',
					'gmc' => '',
					'lifetime' => ''
				);
				return Response::json($response);
			}
		}   
		catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function createPelaporanKanagata(Request $request)
	{


		$id = Auth::id();
		$applicant_username = Auth::user()->username;
		$emp = EmployeeSync::where('employee_id',$request->get('emp_id'))->first();
		$att_condition_material = "";
		$att_detail_condition_material = "" ;
		$att_detail_defect = "";
		$att_foto_defect = "";

		try
		{
			$validator = Validator::make($request->all(), [
				'foto_kanagata' => 'mimes:jpg,jpeg,png,PNG,JPG,JPEG|max:10240',
				'foto_detail_kanagata' => 'mimes:jpg,jpeg,png,PNG,JPG,JPEG|max:10240'
			]);

			if ($validator->fails()) {
				$response = array(
					'status' => false
				);
			}else{

				$code_generator = CodeGenerator::where('note','=','Pelaporan Kanagata')->first();
				$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
				$code_generator->index = $code_generator->index+1;
				$code_generator->save();

				$request_id = $code_generator->prefix . $number;

				if (count($request->file('foto_kanagata')) > 0) {
					$num = 1;
					$file = $request->file('foto_kanagata');
					$nama = $file->getClientOriginalName();
					$filename = pathinfo($nama, PATHINFO_FILENAME);
					$extension = pathinfo($nama, PATHINFO_EXTENSION);
					$att_kanagata = 'kanagata_'.$request_id.'.'.$extension;
					
					$file->move('images/pelaporan_kanagata/', $att_kanagata);

				}
				if (count($request->file('foto_defect_material')) > 0) {
					$num = 1;
					$file = $request->file('foto_defect_material');
					$nama = $file->getClientOriginalName();
					$filename = pathinfo($nama, PATHINFO_FILENAME);
					$extension = pathinfo($nama, PATHINFO_EXTENSION);

					$att_foto_defect = 'defect_material_'.$request_id.'.'.$extension;
					$file->move('images/pelaporan_kanagata/', $att_foto_defect);
				}
				
				if (count($request->file('foto_detail_kanagata')) > 0) {
					
					$num = 1;
					$file = $request->file('foto_detail_kanagata');
					$nama = $file->getClientOriginalName();
					$filename = pathinfo($nama, PATHINFO_FILENAME);
					$extension = pathinfo($nama, PATHINFO_EXTENSION);

					$att_detail_kanagata = 'detail_kanagata_'.$request_id.'.'.$extension;
					$file->move('images/pelaporan_kanagata/', $att_detail_kanagata);
				}
				if (count($request->file('foto_detail_defect')) > 0) {
					
					$num = 1;
					$file = $request->file('foto_detail_defect');
					$nama = $file->getClientOriginalName();
					$filename = pathinfo($nama, PATHINFO_FILENAME);
					$extension = pathinfo($nama, PATHINFO_EXTENSION);

					$att_detail_defect = 'detail_defect_material_'.$request_id.'.'.$extension;
					$file->move('images/pelaporan_kanagata/', $att_detail_defect);
				}
				if (count($request->file('condition_material')) > 0) {
					
					$num = 1;
					$file = $request->file('condition_material');
					$nama = $file->getClientOriginalName();
					$filename = pathinfo($nama, PATHINFO_FILENAME);
					$extension = pathinfo($nama, PATHINFO_EXTENSION);

					$att_condition_material = 'condition_materiall_'.$request_id.'.'.$extension;
					$file->move('images/pelaporan_kanagata/', $att_condition_material);
				}
				if (count($request->file('detail_condition_material')) > 0) {
					
					$num = 1;
					$file = $request->file('detail_condition_material');
					$nama = $file->getClientOriginalName();
					$filename = pathinfo($nama, PATHINFO_FILENAME);
					$extension = pathinfo($nama, PATHINFO_EXTENSION);
					$att_detail_condition_material = 'detail_condition_material_'.$request_id.'.'.$extension;
					$file->move('images/pelaporan_kanagata/', $att_detail_condition_material);

				}


				

				$users = User::where('username',$request->get('emp_id'))->first();
				if (strpos($users->email, '@music.yamaha.com') !== false) {
					$applicant_email = $users->email;
				}else{
					$applicant_email = '';
				}

				$user_approval = [];
				array_push($user_approval, [$emp->employee_id,$emp->name,$applicant_email,'Applicant','Approved',date('Y-m-d H:i:s')]);
				array_push($user_approval, ['PI1605005','Bondan Satriya Permadi Widjayanto','bondan.satriya@music.yamaha.com','Staff Prod',null,null]);
				array_push($user_approval, ['PI1302001','Rano Anugrawan','rano.anugrawan@music.yamaha.com','Staff PE',null,null]);
				array_push($user_approval, ['PI9903003','Slamet Hariadi','slamet.hariadi@music.yamaha.com','Foreman',null,null]);
				array_push($user_approval, ['PI9707011','Prawoto','prawoto@music.yamaha.com','Manager Prod',null,null]);
				array_push($user_approval, ['PI1106001','Darma Bagus Prasetya','darma.bagus@music.yamaha.com','Chief PE',null,null]);
				array_push($user_approval, ['PI0703002','Susilo Basri Prasetyo','susilo.basri@music.yamaha.com','Manager PE',null,null]);
				array_push($user_approval, ['PI1612005','Takashi Ohkubo','takashi.ohkubo@music.yamaha.com','Manager Japanese Speacialist PE',null,null]);

				$comment = $request->get('comment_create');
				$uploadRows = preg_split("/(\s*<(\/?p|br)\s*\/?>\s*)+/u", $comment);

				$kata = "";

				for ($i=0; $i < count($uploadRows); $i++) {
				if ($uploadRows[$i] != "") {
					$kata .= '<span>'.$uploadRows[$i] .'</span><br>'. ' ';
				}
				}


				for ($i=0; $i < count($user_approval); $i++) { 
					if ($i == 0) {
						$st_com =$kata;
					}else{
						$st_com = null;
					}

					$applicant = New PelaporanKanagataApproval([
						'request_id' => $request_id,
						'approver_id' => $user_approval[$i][0],
						'approver_name' => $user_approval[$i][1],
						'approver_email' => $user_approval[$i][2],
						'status' => $user_approval[$i][4],
						'approved_at' => $user_approval[$i][5],
						'remark' => $user_approval[$i][3],
						'comment' => $st_com
					]);

					$applicant->save();
				}

				if ($request->get('lifetimes') == "-") {
					$lifetimek = 0;
				}else{
					$lifetimek = $request->get('lifetimes');
				}

				$types_process = "";

				if($request->get('type_proses') == "Forging") {
					$types_process = $request->get('forging_ke');
				}else{
					$types_process = null;
				}

				$status_repair = "";
				$status_waktu_repair = "";

				if ($request->get('status') == "Ya") {
					$status_repair = null;
					$status_waktu_repair = null;
				}else{
					$status_repair = $request->get('repair');
					$status_waktu_repair = $request->get('waktu_repair');
				}

				$create_kanagata = New PelaporanKanagataRequest([
					'request_id' => $request_id,
					'problem_desc' => $request->get('problem_desc'),
					'tanggal_kejadian' => $request->get('tanggal_kejadian'),
					'process_type' => $request->get('type_proses'),
					'gmc_material' => $request->get('gmc_material'),
					'desc_material' => $request->get('desc_material'),
					'part_name' => $request->get('desc_product'),
					'type_die' => $request->get('type_die'),
					'no_die' => $request->get('no_die'),
					'making_date' => $request->get('making_date'),
					'total_shoot' => $request->get('total_shoot'),
					'lifetime' => $lifetimek,
					'status_shoot' => $request->get('request_status_lifetime'),
					'spare_die' => $request->get('spare_die'),
					'forging_ke' => $types_process,
					'die_high' => $request->get('die_high'),
					'limit_preasure' => $request->get('limit_preasure'),
					'peak' => $request->get('peak'),
					'cavity' => $request->get('cavity'),
					'retak_ke' => $request->get('retak_ke'),
					'ng_sanding' => $request->get('status'),
					'repair' => $status_repair,
					'waktu_repair' => $status_waktu_repair,
					'foto_kanagata' => $att_kanagata,
					'detail_foto_kanagata' => $att_detail_kanagata,
					'foto_defect_material' => $att_foto_defect,
					'detail_foto_defect_material' => $att_detail_defect,
					'condition_material_repair' => $att_condition_material,
					'detail_condition_material_repair' => $att_detail_condition_material,	
					'created_by' => $applicant_username,
					'position' => 'Foreman',
					'remark' => 'Partially Approved',
					'comment_users' => $kata
				]);

				$create_kanagata->save();

				// ----------- SANDING CHECK -----------------

				// if ($request->get('point_kanagata')) {

				// 	$laporan_sanding = New SandingCheckFinding([
				// 		'material_number' => $request->get('gmc_material'),
				// 		'material_description' => $request->get('desc_material'),
				// 		'point' => $request->get('point_kanagata'),
				// 		'point_description' => $request->get('point_desc_kanagata'),
				// 		'check_date' => $request->get('check_date'),
				// 		'id_kanagata' => $request_id,
				// 		'status' => 'pelaporan_kanagata'
				// 	]);

				// 	$laporan_sanding->save();

				// 	SandingCheck::where('material_number', '=', $request->get('gmc_material'))
				// 	->where('status', '=', 'NG')
				// 	->whereNull('remark')
				// 	->update([
				// 		'remark' => $request_id
				// 	]);
				// }

				$kanagata_request = PelaporanKanagataRequest::where('request_id', '=', $request_id)->first();

				$approval_progress = PelaporanKanagataApproval::where('request_id',$request_id)->get();


				$approval_foreman = PelaporanKanagataApproval::where('request_id',$request_id)->where('remark','Staff Prod')->first();

				$mail_to_foreman = [];

				array_push($mail_to_foreman, $approval_foreman->approver_email);
				
				$data = [
					'kanagata_request' => $kanagata_request,
					'approval_progress' => $approval_progress,
					'remarks' => 'Staff Prod'
				];

				Mail::to($mail_to_foreman)
				->bcc(['lukman.hakim.saputra@music.yamaha.com'])
				->send(new SendEmail($data, 'report_kanagata'));

			}

			// return view('kanagata.kanagata_control', array(
			// ))->with('page', 'Kanagata Control');

			// $response = array(
			// 	'status' => true
			// );
			// return Response::json($response);

			// if ($request->get('point_kanagata')) {
			// 	return redirect('/index/monitoring/material_check/sanding')->with('title', 'Monitoring Visual Check Sanding')->with('title_jp', '??')->with('page', 'Monitoring Check Material Sanding');  
			// } else {
			return redirect('/kanagata/control')->with('status', 'Success')->with('page', 'Kanagata Control');  
			// }



		}
		catch(QueryException $e)
		{
			return redirect('/kanagata/control')->with('error', $e->getMessage())
			->with('page', 'Kanagata Control');
		}
	}

	public function updatePelaporanKanagata(Request $request)
	{
		try
		{

			$get_data = PelaporanKanagataRequest::where('request_id',$request->get('request_id_edit'))->first();

			$att_kanagata_edit = $get_data->foto_kanagata;
			$att_kanagata_detail_edit = $get_data->detail_foto_kanagata;
			$att_defect_edit = $get_data->foto_defect_material;
			$att_defect_detail_edit = $get_data->detail_foto_defect_material;
			$att_condition_edit = $get_data->condition_material_repair;
			$att_condition_detail_edit = $get_data->detail_condition_material_repair;

			$file_destination = 'images/pelaporan_kanagata/';

			if (count($request->file('foto_kanagata_edit')) > 0) {
				if ($get_data->foto_kanagata != null || $get_data->foto_kanagata != "") {
					$file_old = $file_destination.$get_data->foto_kanagata;
					unlink($file_old);
				}
				$file = $request->file('foto_kanagata_edit');
				$nama = $file->getClientOriginalName();
				$filename = pathinfo($nama, PATHINFO_FILENAME);
				$extension = pathinfo($nama, PATHINFO_EXTENSION);
				$att_kanagata_edit = 'kanagata_'.$request->get('request_id_edit').'.'.$extension;
				$file->move('images/pelaporan_kanagata/', $att_kanagata_edit);
			}

			if (count($request->file('foto_detail_kanagata_edit')) > 0) {
				if ($get_data->detail_foto_kanagata != null || $get_data->detail_foto_kanagata != "") {
					$file_old = $file_destination.$get_data->detail_foto_kanagata;
					unlink($file_old);
				}
				$file = $request->file('foto_detail_kanagata_edit');
				$nama = $file->getClientOriginalName();
				$filename = pathinfo($nama, PATHINFO_FILENAME);
				$extension = pathinfo($nama, PATHINFO_EXTENSION);
				$att_kanagata_detail_edit = 'detail_kanagata_'.$request->get('request_id_edit').'.'.$extension;
				$file->move('images/pelaporan_kanagata/', $att_kanagata_detail_edit);
			}

			if (count($request->file('foto_defect_material_edit')) > 0) {
				if ($get_data->foto_defect_material != null || $get_data->foto_defect_material != "") {
					$file_old = $file_destination.$get_data->foto_defect_material;
					unlink($file_old);
				}
				$file = $request->file('foto_defect_material_edit');
				$nama = $file->getClientOriginalName();
				$filename = pathinfo($nama, PATHINFO_FILENAME);
				$extension = pathinfo($nama, PATHINFO_EXTENSION);
				$att_defect_edit = 'defect_material_'.$request->get('request_id_edit').'.'.$extension;
				$file->move('images/pelaporan_kanagata/', $att_defect_edit);
			}

			if (count($request->file('foto_detail_defect_edit')) > 0) {
				if ($get_data->detail_foto_defect_material != null || $get_data->detail_foto_defect_material != "") {
					$file_old = $file_destination.$get_data->detail_foto_defect_material;
					unlink($file_old);
				}
				$file = $request->file('foto_detail_defect_edit');
				$nama = $file->getClientOriginalName();
				$filename = pathinfo($nama, PATHINFO_FILENAME);
				$extension = pathinfo($nama, PATHINFO_EXTENSION);
				$att_defect_detail_edit = 'detail_defect_material_'.$request->get('request_id_edit').'.'.$extension;
				$file->move('images/pelaporan_kanagata/', $att_defect_detail_edit);
			}

			if (count($request->file('condition_material_edit')) > 0) {
				if ($get_data->condition_material_repair != null || $get_data->condition_material_repair != "") {
					$file_old = $file_destination.$get_data->condition_material_repair;
					unlink($file_old);
				}
				$file = $request->file('condition_material_edit');
				$nama = $file->getClientOriginalName();
				$filename = pathinfo($nama, PATHINFO_FILENAME);
				$extension = pathinfo($nama, PATHINFO_EXTENSION);
				$att_condition_edit = 'condition_materiall_'.$request->get('request_id_edit').'.'.$extension;
				$file->move('images/pelaporan_kanagata/', $att_condition_edit);
			}

			if (count($request->file('detail_condition_material_edit')) > 0) {
				if ($get_data->detail_condition_material_repair != null || $get_data->detail_condition_material_repair != "") {
					$file_old = $file_destination.$get_data->detail_condition_material_repair;
					unlink($file_old);
				}
				$file = $request->file('detail_condition_material_edit');
				$nama = $file->getClientOriginalName();
				$filename = pathinfo($nama, PATHINFO_FILENAME);
				$extension = pathinfo($nama, PATHINFO_EXTENSION);
				$att_condition_detail_edit = 'detail_condition_material_'.$request->get('request_id_edit').'.'.$extension;
				$file->move('images/pelaporan_kanagata/', $att_condition_detail_edit);
			}

			$types_process_edit = "";

			if($request->get('type_proses_edit') == "Forging") {
				$types_process_edit = $request->get('forging_ke_edit');
			}else{
				$types_process_edit = null;
			}

			$status_repair_edit = "";
			$status_waktu_repair_edit = "";

			if ($request->get('status_edit') == "Ya") {
				$status_repair_edit = null;
				$status_waktu_repair_edit = null;
			}else{
				$status_repair_edit = $request->get('repair_edit');
				$status_waktu_repair_edit = $request->get('waktu_repair_edit');
			}
			$comment_edit = $request->get('comment_edit');
			$uploadRows = preg_split("/(\s*<(\/?p|br)\s*\/?>\s*)+/u", $comment_edit);

			$kata_edit = "";

			for ($i=0; $i < count($uploadRows); $i++) { 
				if ($uploadRows[$i] != "") {
					$kata_edit .= '<span>'.$uploadRows[$i] .'</span><br>'. ' ';
				}
			}

			PelaporanKanagataRequest::where('request_id', '=', $request->get('request_id_edit'))
			->whereNull('deleted_at')
			->update([
				'tanggal_kejadian' => $request->get('tanggal_kejadian_edit'),
				'problem_desc' => $request->get('problem_desc_edit'),
				'process_type' => $request->get('type_proses_edit'),
				'gmc_material' => $request->get('gmc_material_edit'),
				'desc_material' => $request->get('desc_material_edit'),
				'part_name' => $request->get('desc_product_edit'),
				'type_die' => $request->get('type_die_edit'),
				'no_die' => $request->get('no_die_edit'),
				'making_date' => $request->get('making_date_edit'),
				'total_shoot' => $request->get('total_shoot_edit'),
				'lifetime' => $request->get('lifetimes_edit'),
				'status_shoot' => $request->get('request_status_lifetime_edit'),
				'spare_die' => $request->get('spare_die_edit'),
				'forging_ke' => $types_process_edit,
				'die_high' => $request->get('die_high_edit'),
				'limit_preasure' => $request->get('limit_preasure_edit'),
				'peak' => $request->get('peak_edit'),
				'cavity' => $request->get('cavity_edit'),
				'retak_ke' => $request->get('retak_ke_edit'),
				'ng_sanding' => $request->get('status_edit'),
				'repair' => $status_repair_edit,
				'waktu_repair' => $status_waktu_repair_edit,
				'foto_kanagata' => $att_kanagata_edit,
				'detail_foto_kanagata' => $att_kanagata_detail_edit,
				'foto_defect_material' => $att_defect_edit,
				'detail_foto_defect_material' => $att_defect_detail_edit,
				'condition_material_repair' => $att_condition_edit,
				'detail_condition_material_repair' => $att_condition_detail_edit,
				'comment_users' => $kata_edit
			]);

			PelaporanKanagataApproval::where('request_id', '=', $request->get('request_id_edit'))->where('remark', '=', 'Applicant')
			->whereNull('deleted_at')
			->update([
				'comment' => $kata_edit
			]);

			return redirect('/kanagata/control')->with('status', 'Edit Pelaporan Kanagata Success')->with('page', 'Kanagata Control');

		}
		catch(QueryException $e)
		{
			return redirect('/kanagata/control')->with('error', $e->getMessage())
			->with('page', 'Kanagata Control');
		} 
	}


	
	public function kanagataApproval($id,$remark){

		$kanagata = PelaporanKanagataRequest::find($id);
		// dd($kanagata);
		
		// $approval = PelaporanKanagataApproval::where('request_id', '=', $kanagata->request_id)->where('status', '=',null)->limit(1)->get();
		$approval = PelaporanKanagataApproval::where('request_id',$kanagata->request_id)->where('remark',$remark)->first();
		
		$status_approval = '';


		if (count($approval) != null) {
			$leave_request = PelaporanKanagataRequest::where('request_id', '=', $kanagata->request_id)->first();

			$approval_progress = PelaporanKanagataApproval::where('request_id',$kanagata->request_id)->get();

			$users = User::where('id',$leave_request->created_by)->first();

			$mail_to = [];
			$cc = [];

			if ($remark == 'Applicant') {
				if ($approval->status == null) {
					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					array_push($mail_to, 'bondan.satriya@music.yamaha.com');


					$remarks = 'Staff Prod';
				}else{
					$status_approval = 'Approved';
				}
			}

			if ($remark == 'Staff Prod') {
				if ($approval->status == null) {
					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					array_push($mail_to, 'rano.anugrawan@music.yamaha.com');
					// $remarks = 'Manager Prod';
					$remarks = 'Staff PE';

				}else{
					$status_approval = 'Approved';
				}
			}

			if ($remark == 'Staff PE') {
				if ($approval->status == null) {
					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					array_push($mail_to, 'slamet.hariadi@music.yamaha.com');


					$remarks = 'Foreman';
				}else{
					$status_approval = 'Approved';
				}
			}

			if ($remark == 'Foreman') {
				if ($approval->status == null) {
					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					array_push($mail_to, 'prawoto@music.yamaha.com');

					$remarks = 'Manager Prod';
				}else{
					$status_approval = 'Approved';
				}
			}
			
			if ($remark == 'Manager Prod') {
				if ($approval->status == null) {
					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					array_push($mail_to, 'darma.bagus@music.yamaha.com');

					$remarks = 'Chief PE';
				}else{
					$status_approval = 'Approved';
				}
			}
			

			if ($remark == 'Chief PE') {
				if ($approval->status == null) {
					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					array_push($mail_to, 'susilo.basri@music.yamaha.com');

					$remarks = 'Manager PE';
				}else{
					$status_approval = 'Approved';
				}
			}
			if ($remark == 'Manager PE') {
				if ($approval->status == null) {
					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					array_push($mail_to, 'takashi.ohkubo@music.yamaha.com');

					$remarks = 'Manager Japanese Speacialist PE';
				}else{
					$status_approval = 'Approved';
				}
			}

			if ($remark == 'Manager Japanese Speacialist PE') {
				if ($approval->status == null) {
					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					$remarks = 'Finish';

				}else{
					$status_approval = 'Approved';
				}
			}

		}else{
			$leave_request = null;
		}

		try {
			if ($status_approval == 'Approved') {
				return view('kanagata.approval_status')->with('head','Pelaporan Kanagata Retak')->with('message','Request ID : '.$kanagata->request_id.' Have Been Approved.')->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('reject','done');
			}else{
				if ($leave_request != null) {
					$leave_request->position = strtolower($remarks);
					$leave_request->remark = 'Partially Approved';
					$leave_request->save();
					$approval->save();

					$kanagata_request = PelaporanKanagataRequest::where('request_id', '=', $kanagata->request_id)->first();

					$approval_progress = PelaporanKanagataApproval::where('request_id',$kanagata->request_id)->get();

					$data = [
						'kanagata_request' => $kanagata_request,
						'approval_progress' => $approval_progress,
						'remarks' => $remarks
					];


					Mail::to($mail_to)
					->bcc(['lukman.hakim.saputra@music.yamaha.com'])
					->send(new SendEmail($data, 'report_kanagata'));

					return view('kanagata.approval_status')->with('head','Pelaporan Kanagata Retak')->with('message','Request ID : '.$kanagata->request_id.' Approved Successfully.')->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('reject','done');
				}else{
					return view('kanagata.approval_status')->with('head','Pelaporan Kanagata Retak')->with('message','Request ID : '.$kanagata->request_id.' Have Been Remove.')->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('reject','done');

				}
			}
		} catch (\Exception $e) {
			return view('kanagata.approval_status')->with('head','Pelaporan Kanagata Retak')->with('message','Gagal menyetujui Pelaporan Kanagata Retak dengan Error ='.$e->getMessage())->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('reject','done');
		}
	}


	public function ResendkanagataApproval($id,$remark)
	{

		$kanagata = PelaporanKanagataRequest::find($id);
		// dd($kanagata);
		
		// $approval = PelaporanKanagataApproval::where('request_id', '=', $kanagata->request_id)->where('status', '=',null)->limit(1)->get();
		$approval = PelaporanKanagataApproval::where('request_id',$kanagata->request_id)->where('remark',$remark)->first();
		
		$status_approval = '';

		if ($approval == null) {
			$approval = PelaporanKanagataApproval::where('request_id',$request_id)->where('remark','Applicant')->first();
			if ($approval == null) {
				$approval = PelaporanKanagataApproval::where('request_id',$request_id)->where('remark','Staff Prod')->first();
				if ($approval == null) {
					$approval = PelaporanKanagataApproval::where('request_id',$request_id)->where('remark','Staff PE')->first();
					if ($approval == null) {
						$approval = PelaporanKanagataApproval::where('request_id',$request_id)->where('remark','Foreman')->first();
						if ($approval == null) {
							$approval = PelaporanKanagataApproval::where('request_id',$request_id)->where('remark','Manager Prod')->first();
							if ($approval == null) {
								$approval = PelaporanKanagataApproval::where('request_id',$request_id)->where('remark','Chief PE')->first();
								if ($approval == null) {
									$approval = PelaporanKanagataApproval::where('request_id',$request_id)->where('remark','Manager PE')->first();
									if ($approval == null) {
										$approval = PelaporanKanagataApproval::where('request_id',$request_id)->where('remark','Manager Japanese Speacialist PE')->first();
									}
								}
							}
						}
					}

				}
			}
		}
		$approval->status = null;
		$approval->approved_at = null;
		$approval->save();

		if (count($approval) != null) {
			$leave_request = PelaporanKanagataRequest::where('request_id', '=', $kanagata->request_id)->first();

			$approval_progress = PelaporanKanagataApproval::where('request_id',$kanagata->request_id)->get();

			$users = User::where('id',$leave_request->created_by)->first();

			$mail_to = [];
			$cc = [];

			if ($remark == 'Applicant') {
				if ($approval->status == null) {
					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					array_push($mail_to, 'bondan.satriya@music.yamaha.com');


					$remarks = 'Staff Prod';
				}else{
					$status_approval = 'Approved';
				}
			}

			if ($remark == 'Staff Prod') {
				if ($approval->status == null) {
					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					array_push($mail_to, 'rano.anugrawan@music.yamaha.com');
					
					$remarks = 'Staff PE';
				}else{
					$status_approval = 'Approved';
				}
			}

			if ($remark == 'Staff PE') {
				if ($approval->status == null) {
					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					array_push($mail_to, 'slamet.hariadi@music.yamaha.com');

					$remarks = 'Foreman';
				}else{
					$status_approval = 'Approved';
				}
			}

			if ($remark == 'Foreman') {
				if ($approval->status == null) {
					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					array_push($mail_to, 'prawoto@music.yamaha.com');

					$remarks = 'Manager Prod';
				}else{
					$status_approval = 'Approved';
				}
			}
			
			if ($remark == 'Manager Prod') {
				if ($approval->status == null) {
					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					array_push($mail_to, 'darma.bagus@music.yamaha.com');

					$remarks = 'Chief PE';
				}else{
					$status_approval = 'Approved';
				}
			}
			

			if ($remark == 'Chief PE') {
				if ($approval->status == null) {
					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					array_push($mail_to, 'susilo.basri@music.yamaha.com');

					$remarks = 'Manager PE';
				}else{
					$status_approval = 'Approved';
				}
			}
			if ($remark == 'Manager PE') {
				if ($approval->status == null) {
					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					array_push($mail_to, 'takashi.ohkubo@music.yamaha.com');

					$remarks = 'Manager Japanese Speacialist PE';
				}else{
					$status_approval = 'Approved';
				}
			}

			if ($remark == 'Manager Japanese Speacialist PE') {
				if ($approval->status == null) {
					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					$remarks = 'Finish';

				}else{
					$status_approval = 'Approved';
				}
			}

		}else{
			$leave_request = null;
		}


		try {
			if ($status_approval == 'Approved') {
				$response = array(
					'status' => false,
					'message' => 'Telah Disetujui'
				);
				return Response::json($response);
			}else{
				if ($leave_request != null) {
					$leave_request->position = strtolower($remarks);
					$leave_request->remark = 'Partially Approved';
					$leave_request->save();
					$approval->save();

					$kanagata_request = PelaporanKanagataRequest::where('request_id', '=', $kanagata->request_id)->first();

					$approval_progress = PelaporanKanagataApproval::where('request_id',$kanagata->request_id)->get();

					$data = [
						'kanagata_request' => $kanagata_request,
						'approval_progress' => $approval_progress,
						'remarks' => $remarks
					];


					Mail::to($mail_to)
					->bcc(['lukman.hakim.saputra@music.yamaha.com'])
					->send(new SendEmail($data, 'report_kanagata'));
					$response = array(
						'status' => true,
						'message' => 'Resend Email Berhasil'
					);
					return Response::json($response);
				}else{
					$response = array(
						'status' => false,
						'message' => 'Telah Disetujui'
					);
					return Response::json($response);
				}
			}
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}


	public function rejectKanagataRequest1($request_id,$remark)
	{
		try {
			$leave_request = PelaporanKanagataRequest::where('request_id', '=', $request_id)->first();
			$approval = PelaporanKanagataApproval::where('request_id',$request_id)->where('remark','Manager')->first();

			if ($approval->status == null) {
				return view('human_resource.leave_request.reject')->with('head','Surat Izin Keluar Ditolak')->with('message','Request ID : '.$request_id.' telah ditolak.')->with('page','Surat Izin Keluar Ditolak')->with('remark',$remark)->with('driver_ids',$leave_request->driver_request_id)->with('remark',$remark)->with('request_id',$request_id)->with('reject','Belum');
			}else{
				return view('human_resource.leave_request.reject')->with('head','Surat Izin Keluar Ditolak')->with('message','Request ID : '.$request_id.' pernah ditolak.')->with('page','Surat Izin Keluar Ditolak')->with('remark',$remark)->with('driver_ids',$leave_request->driver_request_id)->with('remark',$remark)->with('request_id',$request_id)->with('reject','Pernah');
			}
		} catch (\Exception $e) {
			return view('human_resource.leave_request.approval')->with('head','Leave Request Approval')->with('message','Gagal menyetujui Surat Izin Keluar dengan Error = '.$e->getMessage())->with('page','Surat Izin Keluar Ditolak')->with('remark',$remark)->with('driver_ids',$leave_request->driver_request_id)->with('remark',$remark)->with('request_id',$request_id);
		}
	}

	public function decisionKanagataRequest($request_id,$remark)
	{
		try {
			$kanagataRequest = PelaporanKanagataRequest::where('id', '=', $request_id)->first();
			$approval = PelaporanKanagataApproval::where('request_id',$kanagataRequest->request_id)->where('remark','Manager Japanese Speacialist PE')->first();

			if ($approval->status == null) {
				return view('kanagata.decision_kanagata')->with('head','Pelaporan Kanagata Retak')->with('message','Request ID : '.$kanagataRequest->request_id.' telah ditolak.')->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('request_id',$kanagataRequest->request_id)->with('reject','Belum');
			}else{
				return view('kanagata.decision_kanagata')->with('head','Pelaporan Kanagata Retak')->with('message','Request ID : '.$kanagataRequest->request_id.' Cracked Kanagata Reporting Approved .')->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('remark',$remark)->with('request_id',$kanagataRequest->request_id);
			}
		} catch (\Exception $e) {
			return view('kanagata.decision_kanagata')->with('head','Pelaporan Kanagata Retak')->with('message','Pelaporan Kanagata Retak = '.$e->getMessage())->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('remark',$remark)->with('request_id',$kanagataRequest->request_id);
		}
	}


	public function approvalCommentKanagata($request_id,$remark)
	{
		try {
			$kanagataRequest = PelaporanKanagataRequest::where('id', '=', $request_id)->first();
			$approval = PelaporanKanagataApproval::where('request_id',$kanagataRequest->request_id)->where('remark',$remark)->first();

			if ($approval->status == null) {
				return view('kanagata.approval_comment')->with('head','Pelaporan Kanagata Retak')->with('message','Request ID : '.$kanagataRequest->request_id.'')->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('request_id',$kanagataRequest->request_id);
			}else{
				return view('kanagata.approval_comment')->with('head','Pelaporan Kanagata Retak')->with('message','Request ID : '.$kanagataRequest->request_id.' Cracked Kanagata Reporting Approved .')->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('remark',$remark)->with('request_id',$kanagataRequest->request_id);
			}
		} catch (\Exception $e) {
			return view('kanagata.approval_comment')->with('head','Pelaporan Kanagata Retak')->with('message','Pelaporan Kanagata Retak = '.$e->getMessage())->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('remark',$remark)->with('request_id',$kanagataRequest->request_id);
		}
	}



	public function approvalRejectKanagata($request_id,$remark)
	{
		try {
			$kanagataRequest = PelaporanKanagataRequest::where('id', '=', $request_id)->first();
			$approval = PelaporanKanagataApproval::where('request_id',$kanagataRequest->request_id)->where('remark',$remark)->first();

			if ($approval->status == null) {
				return view('kanagata.approval_reject')->with('head','Pelaporan Kanagata Retak')->with('message','Request ID : '.$kanagataRequest->request_id.'')->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('request_id',$kanagataRequest->request_id);
			}else{
				
				return view('kanagata.approval_status')->with('head','Pelaporan Kanagata Retak')->with('message','Request ID : '.$kanagataRequest->request_id.' Have Been Rejected.')->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('reject','Pernah');
			}
		} catch (\Exception $e) {
			return view('kanagata.approval_reject')->with('head','Pelaporan Kanagata Retak')->with('message','Pelaporan Kanagata Retak = '.$e->getMessage())->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('remark',$remark)->with('request_id',$kanagataRequest->request_id);
		}
	}

	public function rejectKanagataRequest($id,$remark)
	{
		$kanagataRequest = PelaporanKanagataRequest::find($id);

		try {

			$approval = PelaporanKanagataApproval::where('request_id',$kanagataRequest->request_id)->where('remark',$remark)->first();

			$leave_request = PelaporanKanagataRequest::where('request_id',$kanagataRequest->request_id)->first();
			$leave_request->remark = 'Rejected';
			$by = $leave_request->created_by;
			$leave_request->save();


			$approval->status = 'Rejected';
			$approval->approved_at = date('Y-m-d H:i:s');
			$approval->save();

			$users = User::where('username',$by)->first();


			$mail_to = [];

			if (strpos($users->email, '@music.yamaha.com') !== false) {
				array_push($mail_to, $users->email);
			}else{
				$get_ep_foreman = EmployeeSync::where('employee_id',$kanagataRequest->created_by)->first();

				$chief = Approver::where('department',$get_ep_foreman->department)->where('remark','Chief')->first();
				$foreman = Approver::where('department',$get_ep_foreman->department)->where('remark','Foreman')->first();
				if (count($chief) > 0) {
					if ($chief->approver_email) {
						array_push($mail_to, $chief->approver_email);
					}
				}

				if (count($foreman) > 0) {
					if ($foreman->approver_email) {
						array_push($mail_to, $foreman->approver_email);
					}
				}
			}

			$kanagata_request = PelaporanKanagataRequest::where('request_id', '=', $kanagataRequest->request_id)->first();
			// $detail_emp = HrLeaveRequestDetail::where('request_id',$request_id)->leftjoin('departments','department_name','department')->get();
			$approval_progress = PelaporanKanagataApproval::where('request_id',$kanagataRequest->request_id)->get();

			$data = [
				'kanagata_request' => $kanagata_request,
				'approval_progress' => $approval_progress,
				'remarks' => '',
			];

			Mail::to($mail_to)
			->bcc(['lukman.hakim.saputra@music.yamaha.com'])
			->send(new SendEmail($data, 'report_kanagata_reject'));

			// $response = array(
			// 	'status' => true,
			// 	'message' => 'berhasil direject.'
			// );
			// return Response::json($response);
			return view('kanagata.approval_status')->with('head','Pelaporan Kanagata Retak')->with('message','Request ID : '.$kanagataRequest->request_id.' Have Been Rejected.')->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('reject','done');

		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function fetchKanagataApproval(Request $request)
	{
		try {
			$user = EmployeeSync::where('employee_id',Auth::user()->username)->first();
			$pic_progress = $request->get('pic_progress');
			$dateto = $request->get('dates');
			$now = date('Y-m');
			$datetos = "";

			if ($dateto != null) {
				$datetos = "&& DATE_FORMAT(b.created_at,'%Y-%m') = '".$dateto."'";
			}else if ($request->get('st') != null && $request->get('names') != null) {
				$datetos = "&& DATE_FORMAT(b.created_at,'%Y-%m') = '".$request->get('st')."' && a.remark = '".$request->get('names')."' ";
			}
			else{
				$datetos = "";
			}
			
			$leave_request = db::select('
				SELECT *, DATE_FORMAT(pelaporan_kanagata_requests.created_at,"%Y-%m-%d") as tanggal
				FROM
				pelaporan_kanagata_requests
				WHERE
				DATE_FORMAT(created_at,"%Y-%m") = "'.$now.'"');

			$leave_approve = DB::SELECT("SELECT a.*,COALESCE ( a.position, ''), b.approver_name,b.request_id,DATE_FORMAT(a.created_at,'%Y-%m-%d') as tanggal FROM pelaporan_kanagata_requests a LEFT JOIN pelaporan_kanagata_approvals b on b.request_id = a.request_id WHERE a.position = b.remark ".$datetos." and a.remark != 'Fully Approved' and b.approver_name = '".$pic_progress."' ".$datetos." ORDER BY a.request_id desc");

			$leave_approve1 = DB::SELECT("SELECT a.*,COALESCE ( a.position, ''), b.approver_name,b.request_id, DATE_FORMAT(a.created_at,'%Y-%m-%d') as tanggal FROM pelaporan_kanagata_requests a LEFT JOIN pelaporan_kanagata_approvals b on b.request_id = a.request_id WHERE a.position = b.remark ".$datetos." and a.remark != 'Fully Approved' ORDER BY a.request_id desc");

			$leave_approve_complete = DB::SELECT("SELECT a.*,COALESCE ( a.position, ''), b.approver_name,b.request_id,DATE_FORMAT(a.created_at,'%Y-%m-%d') as tanggal FROM pelaporan_kanagata_requests a LEFT JOIN pelaporan_kanagata_approvals b on b.request_id = a.request_id WHERE a.position = b.remark ".$datetos." and a.remark = 'Fully Approved' and b.approver_name = '".$pic_progress."' ORDER BY a.request_id desc");
			$leave_approve_complete1 = DB::SELECT("SELECT a.*,COALESCE ( a.position, ''), b.approver_name,b.request_id, DATE_FORMAT(a.created_at,'%Y-%m-%d') as tanggal FROM pelaporan_kanagata_requests a LEFT JOIN pelaporan_kanagata_approvals b on b.request_id = a.request_id WHERE a.position = b.remark ".$datetos." and a.remark = 'Fully Approved' ORDER BY a.request_id desc");

			$app = '';
			if ($pic_progress != null) {
				$apprr = json_encode($pic_progress);
				$appr = str_replace(array("[","]"),array("(",")"),$apprr);
				$app = ''.$appr.'';
				$get_data = $leave_approve;
				$get_data_complete = $leave_approve_complete;
			} else {
				$app = '';
				$get_data = $leave_approve1;
				$get_data_complete = $leave_approve_complete1;

			}

			$nik = [];

			$role = Auth::user()->role_code;

			$leave_approvals = [];

			foreach($get_data as $lr){
				$leave_approval = DB::SELECT("SELECT
					pelaporan_kanagata_approvals.id,
					pelaporan_kanagata_approvals.request_id,
					pelaporan_kanagata_approvals.approver_id,
					pelaporan_kanagata_approvals.approver_name,
					pelaporan_kanagata_approvals.approver_email,
					pelaporan_kanagata_approvals.`status`,
					pelaporan_kanagata_approvals.approved_at,
					pelaporan_kanagata_approvals.remark,
					pelaporan_kanagata_approvals.comment,
					DATE_FORMAT( pelaporan_kanagata_requests.created_at, '%Y-%m-%d' ) as tanggal,
					CONCAT(
					DATE_FORMAT( pelaporan_kanagata_approvals.approved_at, '%d-%b-%Y' ),
					'<br>',
					DATE_FORMAT( pelaporan_kanagata_approvals.approved_at, '%H:%i:%s' )) AS approved_ats,
					'sudah' AS keutamaan 
					FROM
					`pelaporan_kanagata_approvals`
					JOIN pelaporan_kanagata_requests ON pelaporan_kanagata_requests.request_id = pelaporan_kanagata_approvals.request_id 
					WHERE
					pelaporan_kanagata_approvals.request_id = '".$lr->request_id."'
					AND pelaporan_kanagata_approvals.`status` IS NOT NULL UNION ALL
					(
					SELECT
					pelaporan_kanagata_approvals.id,
					pelaporan_kanagata_approvals.request_id,
					pelaporan_kanagata_approvals.approver_id,
					pelaporan_kanagata_approvals.approver_name,
					pelaporan_kanagata_approvals.approver_email,
					pelaporan_kanagata_approvals.`status`,
					pelaporan_kanagata_approvals.approved_at,
					pelaporan_kanagata_approvals.remark,
					pelaporan_kanagata_approvals.comment,
					DATE_FORMAT( pelaporan_kanagata_requests.created_at, '%Y-%m-%d' ) as tanggal,
					CONCAT(
					DATE_FORMAT( pelaporan_kanagata_approvals.approved_at, '%d-%b-%Y' ),
					'<br>',
					DATE_FORMAT( pelaporan_kanagata_approvals.approved_at, '%H:%i:%s' )) AS approved_ats,
					'utama' AS keutamaan 
					FROM
					`pelaporan_kanagata_approvals`
					JOIN pelaporan_kanagata_requests ON pelaporan_kanagata_requests.request_id = pelaporan_kanagata_approvals.request_id 
					WHERE
					pelaporan_kanagata_approvals.request_id = '".$lr->request_id."'
					AND pelaporan_kanagata_approvals.`status` IS NULL 
					LIMIT 1 
					) UNION ALL
					(
					SELECT
					pelaporan_kanagata_approvals.id,
					pelaporan_kanagata_approvals.request_id,
					pelaporan_kanagata_approvals.approver_id,
					pelaporan_kanagata_approvals.approver_name,
					pelaporan_kanagata_approvals.approver_email,
					pelaporan_kanagata_approvals.`status`,
					pelaporan_kanagata_approvals.approved_at,
					pelaporan_kanagata_approvals.remark,
					pelaporan_kanagata_approvals.comment,
					DATE_FORMAT( pelaporan_kanagata_requests.created_at, '%Y-%m-%d' ) as tanggal,
					CONCAT(
					DATE_FORMAT( pelaporan_kanagata_approvals.approved_at, '%d-%b-%Y' ),
					'<br>',
					DATE_FORMAT( pelaporan_kanagata_approvals.approved_at, '%H:%i:%s' )) AS approved_ats,
					'belum' AS keutamaan 
					FROM
					`pelaporan_kanagata_approvals`
					JOIN pelaporan_kanagata_requests ON pelaporan_kanagata_requests.request_id = pelaporan_kanagata_approvals.request_id 
					WHERE
					pelaporan_kanagata_requests.request_id = '".$lr->request_id."'
					AND pelaporan_kanagata_approvals.`status` IS NULL 
					AND pelaporan_kanagata_approvals.id != (
					SELECT
					pelaporan_kanagata_approvals.id 
					FROM
					`pelaporan_kanagata_approvals`
					JOIN pelaporan_kanagata_requests ON pelaporan_kanagata_requests.request_id = pelaporan_kanagata_approvals.request_id 
					WHERE
					pelaporan_kanagata_approvals.request_id = '".$lr->request_id."'
					AND pelaporan_kanagata_approvals.`status` IS NULL 
					LIMIT 1 
					) 
					)

					");
				array_push($leave_approvals, $leave_approval);
			}


			$leave_approvals_complete = [];

			foreach($get_data_complete as $lr){
				$leave_approval_completes = DB::SELECT("SELECT
					pelaporan_kanagata_approvals.id,
					pelaporan_kanagata_approvals.request_id,
					pelaporan_kanagata_approvals.approver_id,
					pelaporan_kanagata_approvals.approver_name,
					pelaporan_kanagata_approvals.approver_email,
					pelaporan_kanagata_approvals.`status`,
					pelaporan_kanagata_approvals.approved_at,
					pelaporan_kanagata_approvals.remark,
					pelaporan_kanagata_approvals.comment,
					DATE_FORMAT( pelaporan_kanagata_requests.created_at, '%Y-%m-%d' ) as tanggal,
					CONCAT(
					DATE_FORMAT( pelaporan_kanagata_approvals.approved_at, '%d-%b-%Y' ),
					'<br>',
					DATE_FORMAT( pelaporan_kanagata_approvals.approved_at, '%H:%i:%s' )) AS approved_ats,
					'sudah' AS keutamaan 
					FROM
					`pelaporan_kanagata_approvals`
					JOIN pelaporan_kanagata_requests ON pelaporan_kanagata_requests.request_id = pelaporan_kanagata_approvals.request_id 
					WHERE
					pelaporan_kanagata_approvals.request_id = '".$lr->request_id."'
					AND pelaporan_kanagata_approvals.`status` IS NOT NULL UNION ALL
					(
					SELECT
					pelaporan_kanagata_approvals.id,
					pelaporan_kanagata_approvals.request_id,
					pelaporan_kanagata_approvals.approver_id,
					pelaporan_kanagata_approvals.approver_name,
					pelaporan_kanagata_approvals.approver_email,
					pelaporan_kanagata_approvals.`status`,
					pelaporan_kanagata_approvals.approved_at,
					pelaporan_kanagata_approvals.remark,
					pelaporan_kanagata_approvals.comment,
					DATE_FORMAT( pelaporan_kanagata_requests.created_at, '%Y-%m-%d' ) as tanggal,
					CONCAT(
					DATE_FORMAT( pelaporan_kanagata_approvals.approved_at, '%d-%b-%Y' ),
					'<br>',
					DATE_FORMAT( pelaporan_kanagata_approvals.approved_at, '%H:%i:%s' )) AS approved_ats,
					'utama' AS keutamaan 
					FROM
					`pelaporan_kanagata_approvals`
					JOIN pelaporan_kanagata_requests ON pelaporan_kanagata_requests.request_id = pelaporan_kanagata_approvals.request_id 
					WHERE
					pelaporan_kanagata_approvals.request_id = '".$lr->request_id."'
					AND pelaporan_kanagata_approvals.`status` IS NULL 
					LIMIT 1 
					) UNION ALL
					(
					SELECT
					pelaporan_kanagata_approvals.id,
					pelaporan_kanagata_approvals.request_id,
					pelaporan_kanagata_approvals.approver_id,
					pelaporan_kanagata_approvals.approver_name,
					pelaporan_kanagata_approvals.approver_email,
					pelaporan_kanagata_approvals.`status`,
					pelaporan_kanagata_approvals.approved_at,
					pelaporan_kanagata_approvals.remark,
					pelaporan_kanagata_approvals.comment,
					DATE_FORMAT( pelaporan_kanagata_requests.created_at, '%Y-%m-%d' ) as tanggal,
					CONCAT(
					DATE_FORMAT( pelaporan_kanagata_approvals.approved_at, '%d-%b-%Y' ),
					'<br>',
					DATE_FORMAT( pelaporan_kanagata_approvals.approved_at, '%H:%i:%s' )) AS approved_ats,
					'belum' AS keutamaan 
					FROM
					`pelaporan_kanagata_approvals`
					JOIN pelaporan_kanagata_requests ON pelaporan_kanagata_requests.request_id = pelaporan_kanagata_approvals.request_id 
					WHERE
					pelaporan_kanagata_requests.request_id = '".$lr->request_id."'
					AND pelaporan_kanagata_approvals.`status` IS NULL 
					AND pelaporan_kanagata_approvals.id != (
					SELECT
					pelaporan_kanagata_approvals.id 
					FROM
					`pelaporan_kanagata_approvals`
					JOIN pelaporan_kanagata_requests ON pelaporan_kanagata_requests.request_id = pelaporan_kanagata_approvals.request_id 
					WHERE
					pelaporan_kanagata_approvals.request_id = '".$lr->request_id."'
					AND pelaporan_kanagata_approvals.`status` IS NULL 
					LIMIT 1 
					) 
					)

					");
				array_push($leave_approvals_complete, $leave_approval_completes);
			}


			$response = array(
				'status' => true,
				'leave_request' => $get_data,
				'leave_approvals' => $leave_approvals,
				'get_data_complete' => $get_data_complete,
				'leave_approvals_complete' => $leave_approvals_complete,
				'role' => $role
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

	public function cancelPelaporanKanagataRequest(Request $request)
	{
		try {
			$request_id = $request->get('request_id');

			$leave_request = PelaporanKanagataRequest::where('request_id',$request_id)->first();

			$approval = PelaporanKanagataApproval::where('request_id',$request_id)->forceDelete();

			$leave_request->forceDelete();

			$response = array(
				'status' => true,
				'message' => 'Success Cancel Request'
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

	public function fetchMonitoringKanagata(Request $request)
	{
		$tahun = date('Y');
		$dateto = $request->get('dateto');
		if ($dateto != "") {
			$datetos = $request->get('dateto');
		}else{
			$datetos = date('Y-m');
		}
		$today = date('Y-m');

		$emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
		->select('employee_id', 'department')
		->first();

		if (Auth::user()->role_code == "MIS" || Auth::user()->role_code == "C-MIS" || Auth::user()->role_code == "S-MIS") {
			if ($dateto != "") {
				$data = db::select("
					SELECT
					count( request_id ) AS jumlah,
					monthname( tanggal_kejadian ) AS bulan,
					YEAR ( tanggal_kejadian ) AS tahun,
					DATE_FORMAT( tanggal_kejadian, '%Y-%m' ) AS bulans,
					sum( CASE WHEN `remark` = 'Partially Approved' THEN 1 ELSE 0 END ) AS Signed,
					sum( CASE WHEN `remark` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned,
					sum( CASE WHEN `remark` = 'Fully Approved' THEN 1 ELSE 0 END ) AS finish 
					FROM
					pelaporan_kanagata_requests 
					WHERE
					pelaporan_kanagata_requests.deleted_at IS NULL AND DATE_FORMAT( tanggal_kejadian, '%Y-%m' ) = '".$datetos."'
					GROUP BY
					bulan,
					tahun,
					bulans
					ORDER BY
					tahun,
					MONTH ( tanggal_kejadian ) ASC
					");
			}else{
				$data = db::select("
					SELECT
					count( request_id ) AS jumlah,
					monthname( tanggal_kejadian ) AS bulan,
					YEAR ( tanggal_kejadian ) AS tahun,
					DATE_FORMAT( tanggal_kejadian, '%Y-%m' ) AS bulans,
					sum( CASE WHEN `remark` = 'Partially Approved' THEN 1 ELSE 0 END ) AS Signed,
					sum( CASE WHEN `remark` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned,
					sum( CASE WHEN `remark` = 'Fully Approved' THEN 1 ELSE 0 END ) AS finish
					FROM
					pelaporan_kanagata_requests 
					WHERE
					pelaporan_kanagata_requests.deleted_at IS NULL
					GROUP BY
					bulan,
					tahun,
					bulans
					ORDER BY
					tahun,
					MONTH ( tanggal_kejadian ) ASC
					");
			}
		}else{
			if ($dateto != "") {
				$data = db::select("
					SELECT
					count( request_id ) AS jumlah,
					monthname( tanggal_kejadian ) AS bulan,
					YEAR ( tanggal_kejadian ) AS tahun,
					DATE_FORMAT( tanggal_kejadian, '%Y-%m' ) AS bulans,
					sum( CASE WHEN `remark` = 'Partially Approved' THEN 1 ELSE 0 END ) AS Signed,
					sum( CASE WHEN `remark` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned,
					sum( CASE WHEN `remark` = 'Fully Approved' THEN 1 ELSE 0 END ) AS finish
					FROM
					pelaporan_kanagata_requests 
					WHERE
					pelaporan_kanagata_requests.deleted_at IS NULL AND DATE_FORMAT( tanggal_kejadian, '%Y-%m' ) = '".$datetos."'
					GROUP BY
					bulan,
					tahun,
					bulans
					ORDER BY
					tahun,
					MONTH ( tanggal_kejadian ) ASC
					");
			}else{
				$data = db::select("
					SELECT
					count( request_id ) AS jumlah,
					monthname( tanggal_kejadian ) AS bulan,
					YEAR ( tanggal_kejadian ) AS tahun,
					DATE_FORMAT( tanggal_kejadian, '%Y-%m' ) AS bulans,
					sum( CASE WHEN `remark` = 'Partially Approved' THEN 1 ELSE 0 END ) AS Signed,
					sum( CASE WHEN `remark` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned,
					sum( CASE WHEN `remark` = 'Fully Approved' THEN 1 ELSE 0 END ) AS finish
					FROM
					pelaporan_kanagata_requests 
					WHERE
					pelaporan_kanagata_requests.deleted_at IS NULL
					GROUP BY
					bulan,
					tahun,
					bulans
					ORDER BY
					tahun,
					MONTH ( tanggal_kejadian ) ASC 
					");
			}
		}


		$response = array(
			'status' => true,
			'datas' => $data,
			'tahun' => $tahun,
			'dateto' => $dateto
		);
		return Response::json($response); 
	}

	public function detailKanagataControl($id){

		$kanagata_request = PelaporanKanagataRequest::where('request_id',$id)->first();
		$approval_progress = PelaporanKanagataApproval::select('pelaporan_kanagata_approvals.*',DB::RAW('DATE_FORMAT(pelaporan_kanagata_approvals.approved_at,"%d %b %Y<br>%H:%i:%s") as approved_date'))->where('request_id',$id)->get();

		return view('kanagata.detail_kanagata',  
			array(
				'title' => 'Detail Kanagata', 
				'title_jp' => '投資監視・管理',
				'kanagata' => $kanagata_request,
				'approval_progress' => $approval_progress,
				'role_code' => Auth::user()->role_code

			)
		)->with('page', 'Detail Kanagata');

	}


	public function fetchKanagataDetail(Request $request)
	{

		try {
			$user = EmployeeSync::where('employee_id',Auth::user()->username)->first();

			$leave_request = PelaporanKanagataRequest::select('pelaporan_kanagata_requests.*')->where('request_id','=',$request->get('request_id'))
			->get();


			$leave_approvals = [];

			foreach($leave_request as $lr){
				$leave_approval = DB::SELECT("SELECT
					pelaporan_kanagata_approvals.id,
					pelaporan_kanagata_approvals.request_id,
					pelaporan_kanagata_approvals.approver_id,
					pelaporan_kanagata_approvals.approver_name,
					pelaporan_kanagata_approvals.approver_email,
					pelaporan_kanagata_approvals.`status`,
					pelaporan_kanagata_approvals.approved_at,
					pelaporan_kanagata_approvals.remark,
					pelaporan_kanagata_approvals.comment,
					CONCAT(
					DATE_FORMAT( pelaporan_kanagata_approvals.approved_at, '%d-%b-%Y' ),
					'<br>',
					DATE_FORMAT( pelaporan_kanagata_approvals.approved_at, '%H:%i:%s' )) AS approved_ats,
					'sudah' AS keutamaan 
					FROM
					`pelaporan_kanagata_approvals`
					JOIN pelaporan_kanagata_requests ON pelaporan_kanagata_requests.request_id = pelaporan_kanagata_approvals.request_id 
					WHERE
					pelaporan_kanagata_approvals.request_id = '".$lr->request_id."' 
					AND pelaporan_kanagata_approvals.`status` IS NOT NULL UNION ALL
					(
					SELECT
					pelaporan_kanagata_approvals.id,
					pelaporan_kanagata_approvals.request_id,
					pelaporan_kanagata_approvals.approver_id,
					pelaporan_kanagata_approvals.approver_name,
					pelaporan_kanagata_approvals.approver_email,
					pelaporan_kanagata_approvals.`status`,
					pelaporan_kanagata_approvals.approved_at,
					pelaporan_kanagata_approvals.remark,
					pelaporan_kanagata_approvals.comment,
					CONCAT(
					DATE_FORMAT( pelaporan_kanagata_approvals.approved_at, '%d-%b-%Y' ),
					'<br>',
					DATE_FORMAT( pelaporan_kanagata_approvals.approved_at, '%H:%i:%s' )) AS approved_ats,
					'utama' AS keutamaan 
					FROM
					`pelaporan_kanagata_approvals`
					JOIN pelaporan_kanagata_requests ON pelaporan_kanagata_requests.request_id = pelaporan_kanagata_approvals.request_id 
					WHERE
					pelaporan_kanagata_approvals.request_id = '".$lr->request_id."' 
					AND pelaporan_kanagata_approvals.`status` IS NULL 
					LIMIT 1 
					) UNION ALL
					(
					SELECT
					pelaporan_kanagata_approvals.id,
					pelaporan_kanagata_approvals.request_id,
					pelaporan_kanagata_approvals.approver_id,
					pelaporan_kanagata_approvals.approver_name,
					pelaporan_kanagata_approvals.approver_email,
					pelaporan_kanagata_approvals.`status`,
					pelaporan_kanagata_approvals.approved_at,
					pelaporan_kanagata_approvals.remark,
					pelaporan_kanagata_approvals.comment,
					CONCAT(
					DATE_FORMAT( pelaporan_kanagata_approvals.approved_at, '%d-%b-%Y' ),
					'<br>',
					DATE_FORMAT( pelaporan_kanagata_approvals.approved_at, '%H:%i:%s' )) AS approved_ats,
					'belum' AS keutamaan 
					FROM
					`pelaporan_kanagata_approvals`
					JOIN pelaporan_kanagata_requests ON pelaporan_kanagata_requests.request_id = pelaporan_kanagata_approvals.request_id 
					WHERE
					pelaporan_kanagata_requests.request_id = '".$lr->request_id."' 
					AND pelaporan_kanagata_approvals.`status` IS NULL 
					AND pelaporan_kanagata_approvals.id != (
					SELECT
					pelaporan_kanagata_approvals.id 
					FROM
					`pelaporan_kanagata_approvals`
					JOIN pelaporan_kanagata_requests ON pelaporan_kanagata_requests.request_id = pelaporan_kanagata_approvals.request_id 
					WHERE
					pelaporan_kanagata_approvals.request_id = '".$lr->request_id."' 
					AND pelaporan_kanagata_approvals.`status` IS NULL 
					LIMIT 1 
					) 
					)

					");
				array_push($leave_approvals, $leave_approval);
			}


			$response = array(
				'status' => true,
				'leave_request' => $leave_request,
				'leave_approvals' => $leave_approvals
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


	public function kanagataStatus(){

		return view('kanagata.decision_kanagata',  
			array(
				'title' => 'Kanagata Monitoring & Control', 
				'title_jp' => '投資監視・管理',
				'role_code' => Auth::user()->role_code

			)
		)->with('page', 'Kanagata Control');

	}

	public function decisionPelaporanKanagata(Request $request)
	{
		try {
			$request_id = $request->get('request_id');
			$comment = $request->get('comment');
			$uploadRows = preg_split("/\r?\n/", $comment);

			$kata = "";
			$kata_app = "";

			for ($i=0; $i < count($uploadRows); $i++) { 
				$kata .= '<span>'.$uploadRows[$i] .'</span><br>'. ' ';
				$kata_app .= '<span>'.$uploadRows[$i] .'</span><br>'. ' ';

			}

			$approval = PelaporanKanagataApproval::where('request_id',$request_id)->where('remark','Manager Japanese Speacialist PE')->first();
			if ($approval->status != null) {
				$response = array(
					'status' => false,
					'message' => 'Sudah mengisi Keputusan'
				);
				
			}else{

				$kanagata_request = PelaporanKanagataRequest::where('request_id',$request_id)->first();
				$kanagata_request->remark = 'Fully Approved';
				$kanagata_request->decision = $request->get('decision');
				$kanagata_request->comment = $kata_app;
				$by = $kanagata_request->created_by;
				$kanagata_request->save();

				$approval->status = 'Approved';
				$approval->approved_at = date('Y-m-d H:i:s');
				$approval->comment = $kata;
				$approval->save();


				$approval_progress = PelaporanKanagataApproval::where('request_id',$request_id)->get();
				$mail_to_success = [];

				array_push($mail_to_success, 'bondan.satriya@music.yamaha.com');
				array_push($mail_to_success, 'rano.anugrawan@music.yamaha.com');
				array_push($mail_to_success, 'slamet.hariadi@music.yamaha.com');
				array_push($mail_to_success, 'prawoto@music.yamaha.com');
				array_push($mail_to_success, 'darma.bagus@music.yamaha.com');
				array_push($mail_to_success, 'susilo.basri@music.yamaha.com');

				$data = [
					'kanagata_request' => $kanagata_request,
					'status_email' => 'Successfully'

				];

				Mail::to($mail_to_success)
				->bcc(['lukman.hakim.saputra@music.yamaha.com'])
				->send(new SendEmail($data, 'report_kanagata_done'));
				$response = array(
					'status' => true,
					'message' => 'Decision berhasil diinput.'
				);
			}
			
			return Response::json($response);

		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}


	public function approvalCommentSave(Request $request)
	{
		$request_id = $request->get('request_id');
		$remark = $request->get('position');
		
		$comment = $request->get('comment');
		$uploadRows = preg_split("/\r?\n/", $comment);
		$status_approval = "";

		$kata = "";

		for ($i=0; $i < count($uploadRows); $i++) { 
			$kata .= '<span>'.$uploadRows[$i] .'</span><br>'. ' ';
		}

		$approval = PelaporanKanagataApproval::where('request_id',$request_id)->where('remark',$remark)->first();

		if ($approval->status != null) {
			return view('kanagata.approval_status')->with('head','Pelaporan Kanagata Retak')->with('message','Request ID : '.$request_id.' Have Been Approved.')->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('reject','done');;
		}else{

			$leave_request = PelaporanKanagataRequest::where('request_id', '=', $request_id)->first();

			$approval_progress = PelaporanKanagataApproval::where('request_id',$request_id)->get();

			$users = User::where('id',$leave_request->created_by)->first();

			$mail_to = [];
			$cc = [];


			if ($remark == 'Applicant') {
				if ($approval->status == null) {
					$approval->comment = $kata;
					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					array_push($mail_to, 'bondan.satriya@music.yamaha.com');

					$remarks = 'Staff Prod';

				}else{
					$status_approval = 'Approved';
				}
			}

			if ($remark == 'Staff Prod') {
				if ($approval->status == null) {
					$approval->comment = $kata;

					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					array_push($mail_to, 'rano.anugrawan@music.yamaha.com');

					$remarks = 'Staff PE';
				}else{
					$status_approval = 'Approved';
				}


			}

			if ($remark == 'Staff PE') {
				if ($approval->status == null) {
					$approval->comment = $kata;

					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					array_push($mail_to, 'slamet.hariadi@music.yamaha.com');

					$remarks = 'Foreman';
				}else{
					$status_approval = 'Approved';
				}
			}

			if ($remark == 'Foreman') {
				if ($approval->status == null) {
					$approval->status = 'Approved';
					$approval->comment = $kata;
					$approval->approved_at = date('Y-m-d H:i:s');
					array_push($mail_to, 'prawoto@music.yamaha.com');

					$remarks = 'Manager Prod';
				}else{
					$status_approval = 'Approved';
				}
			}

			
			if ($remark == 'Manager Prod') {
				if ($approval->status == null) {
					$approval->comment = $kata;
					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					array_push($mail_to, 'darma.bagus@music.yamaha.com');
					$remarks = 'Chief PE';

				}else{
					$status_approval = 'Approved';
				}


			}
			

			if ($remark == 'Chief PE') {
				if ($approval->status == null) {
					$approval->comment = $kata;
					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					array_push($mail_to, 'susilo.basri@music.yamaha.com');
					$remarks = 'Manager PE';
				}else{
					$status_approval = 'Approved';
				}
			}
			if ($remark == 'Manager PE') {
				if ($approval->status == null) {
					$approval->comment = $kata;
					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					array_push($mail_to, 'takashi.ohkubo@music.yamaha.com');
					$remarks = 'Manager Japanese Speacialist PE';
				}else{
					$status_approval = 'Approved';
				}
			}

			if ($remark == 'Manager Japanese Speacialist PE') {
				if ($approval->status == null) {
					$approval->comment = $kata;
					$approval->status = 'Approved';
					$approval->approved_at = date('Y-m-d H:i:s');
					$remarks = 'Finish';

				}else{
					$status_approval = 'Approved';
				}
			}



			try {
				if ($status_approval == 'Approved') {
					return view('kanagata.approval_status')->with('head','Pelaporan Kanagata Retak')->with('message','Request ID : '.$kanagata->request_id.' Have Been Approved.')->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('reject','done');
				}else{

					if ($leave_request != null) {
						$leave_request->position = strtolower($remarks);
						$leave_request->remark = 'Partially Approved';
						$leave_request->save();
						$approval->save();

						$kanagata_request = PelaporanKanagataRequest::where('request_id', '=', $request_id)->first();

						$approval_progress = PelaporanKanagataApproval::where('request_id',$request_id)->get();

						$data = [
							'kanagata_request' => $kanagata_request,
							'approval_progress' => $approval_progress,
							'remarks' => $remarks
						];

						Mail::to($mail_to)
						->bcc(['lukman.hakim.saputra@music.yamaha.com'])
						->send(new SendEmail($data, 'report_kanagata'));


						return view('kanagata.approval_status')->with('head','Pelaporan Kanagata Retak')->with('message','Request ID : '.$request_id.' Approved Successfully.')->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('reject','done');
						dd("s");

					}else{
						return view('kanagata.approval_status')->with('head','Pelaporan Kanagata Retak')->with('message','Request ID : '.$request_id.' Have Been Remove.')->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('reject','done');

					}
				}
			} catch (\Exception $e) {
				return view('kanagata.approval_status')->with('head','Pelaporan Kanagata Retak')->with('message','Gagal menyetujui Pelaporan Kanagata Retak dengan Error ='.$e->getMessage())->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('reject','done');
			}

		}

		
	}

	public function approvalRejectSave(Request $request)
	{
		try{
			$request_id = $request->get('request_id');
			$remark = $request->get('position');

			$comment = $request->get('comment');
			$uploadRows = preg_split("/\r?\n/", $comment);
			$status_approval = "";

			$kata = "";

			for ($i=0; $i < count($uploadRows); $i++) { 
				$kata .= '<span>'.$uploadRows[$i] .'</span><br>'. ' ';
			}

			$approval = PelaporanKanagataApproval::where('request_id',$request_id)->where('remark',$remark)->first();

			if ($approval->status != null) {
				return view('kanagata.approval_status')->with('head','Pelaporan Kanagata Retak')->with('message','Request ID : '.$request_id.' Have Been Rejected.')->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('reject','Pernah');;
			}else{

				$leave_request = PelaporanKanagataRequest::where('request_id', '=', $request_id)->first();


				$users = User::where('id',$leave_request->created_by)->first();

				$mail_to = [];
				$cc = [];


				$leave_request->remark = 'Rejected';
				$by = $leave_request->created_by;
				$leave_request->save();


				$approval->status = 'Rejected';
				$approval->comment = $kata;
				$approval->approved_at = date('Y-m-d H:i:s');
				$approval->save();

				$users = User::where('username',$by)->first();




				$mail_to = [];

				if (strpos($users->email, '@music.yamaha.com') !== false) {
					array_push($mail_to, $users->email);
				}else{
					$get_ep_foreman = EmployeeSync::where('employee_id',$leave_request->created_by)->first();

					$chief = Approver::where('department',$get_ep_foreman->department)->where('remark','Chief')->first();
					$foreman = Approver::where('department',$get_ep_foreman->department)->where('remark','Foreman')->first();
					if (count($chief) > 0) {
						if ($chief->approver_email) {
							array_push($mail_to, $chief->approver_email);
						}
					}

					if (count($foreman) > 0) {
						if ($foreman->approver_email) {
							array_push($mail_to, $foreman->approver_email);
						}
					}
				}


				$kanagata_request = PelaporanKanagataRequest::where('request_id', '=', $leave_request->request_id)->first();
				$approval_progress = PelaporanKanagataApproval::where('request_id',$leave_request->request_id)->get();
				$approval_reject = PelaporanKanagataApproval::where('request_id',$leave_request->request_id)->where('status','Rejected')->get();


				$data = [
					'kanagata_request' => $kanagata_request,
					'approval_progress' => $approval_progress,
					'approval_reject' => $approval_reject,
					'remarks' => '',
				];

				Mail::to($mail_to)
				->bcc(['lukman.hakim.saputra@music.yamaha.com'])
				->send(new SendEmail($data, 'report_kanagata_reject'));


				return view('kanagata.approval_status')->with('head','Pelaporan Kanagata Retak')->with('message','Request ID : '.$leave_request->request_id.' Have Been Rejected.')->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('reject','Belum');
			}



		} catch (\Exception $e) {
			return view('kanagata.approval_status')->with('head','Pelaporan Kanagata Retak')->with('message','Gagal Rejected Pelaporan Kanagata Retak dengan Error ='.$e->getMessage())->with('page','Pelaporan Kanagata Retak')->with('remark',$remark)->with('reject','Pernah');
		}

		
	}


	public function fetchHistoryKanagata(Request $request)
	{
		try {
			$gmcs = $request->get('gmc');

			$material_defect = DB::SELECT("SELECT
				gmc_material,total_shoot,tanggal_kejadian
				FROM
				pelaporan_kanagata_requests
				WHERE gmc_material = '".$gmcs."'
				");

			$response = array(
				'status' => true,
				'material_defect' => $material_defect

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
