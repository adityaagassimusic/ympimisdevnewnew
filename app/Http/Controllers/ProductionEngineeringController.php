<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Mail\SendEmail;
use Carbon\Carbon;
use Response;
use PDF;
use File;

use App\User;
use App\EmployeeSync;
use App\TicketPic;
use App\EjorForm;
use App\EjorFormApprover;
use App\EjorEvidence;
use App\Approver;

class ProductionEngineeringController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');

		$this->category = [
			'Layout',
			'Jig/Mold',
			'Mesin',
			'Equipment',
			'Tools',
			'Proses',
			'Lain-lain'
		];

		$this->type = [
			'Perbaikan',
			'Desain Baru',
			'Trial'
		];
	}
	public function indexEjor()
	{
		$title = "List EJOR";
		$title_jp = "";

		$emp_id = strtoupper(Auth::user()->username);
		$_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/" . $emp_id);

		$employee = EmployeeSync::where('employee_id', '=', Auth::user()->username)->whereNull('end_date')->first();

		return view('production_engineering.ejor.index', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employee' => $employee,
			'categories' => $this->category,
			'types' => $this->type,
		)
		)->with('page', 'MIS Ticket')->with('head', 'Ticket');
	}

	public function fetchEjor(Request $request)
	{
		$datas = EjorForm::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'ejor_forms.created_by')
			->leftJoin(db::raw('employee_syncs as es'), 'es.employee_id', '=', 'ejor_forms.pic');

		if ($request->get('status') != 'all') {
			$datas = $datas->where('status', '=', $request->get('status'));
		}

		if ($request->get('form_id')) {
			$datas = $datas->where('form_id', '=', $request->get('form_id'));
		}

		if (!$request->get('remark')) {
			if (!str_contains(Auth::user()->role_code, 'MIS')) {
				$dpt = EmployeeSync::where('employee_id', '=', Auth::user()->username)
					->select('department')
					->first();

				$sec = EmployeeSync::where('department', $dpt->department)
					->whereNull('end_date')
					->select('section')
					->groupBy('section')
					->get()
					->toArray();


				$datas = $datas->whereIn('ejor_forms.section', $sec);
			}
		}

		$datas = $datas->select('form_id', 'title', 'job_type', 'job_category', 'job_category_note', 'ejor_forms.section', 'description', 'purpose', 'condition_before', 'condition_after', 'attachment', 'status', 'pic', 'request_date', 'target_date', 'employee_syncs.name', db::raw('es.name as pic_name'), 'ejor_forms.created_by', 'priority', 'priority_reason', 'reason')
			->get();

		$evidence = EjorEvidence::select('form_id', 'note', 'attachment', 'uploaded_by', 'uploaded_at', 'status');

		if ($request->get('form_id')) {
			$evidence = $evidence->where('form_id', $request->get('form_id'));
		}
		$evidence = $evidence->get();


		$response = array(
			'status' => true,
			'datas' => $datas,
			'evidences' => $evidence
		);
		return Response::json($response);
	}

	public function postEjorForm(Request $request)
	{
		try {
			$att = null;
			$tahun = date('y');
			$bulan = date('m');

			$query = "SELECT form_id FROM ejor_forms where DATE_FORMAT(created_at, '%y') = '$tahun' and month(created_at) = '$bulan' order by form_id DESC LIMIT 1";
			$nomorurut = DB::select($query);

			if ($nomorurut != null) {
				$nomor = substr($nomorurut[0]->form_id, -3);
				$nomor = $nomor + 1;
				$nomor = sprintf('%03d', $nomor);
			} else {
				$nomor = "001";
			}

			$result['tahun'] = $tahun;
			$result['bulan'] = $bulan;
			$result['no_urut'] = $nomor;

			$form_number = 'EJ' . $result['tahun'] . $result['bulan'] . $result['no_urut'];

			// dd($form_number);

			if ($request->get('att_count') > 0) {
				for ($i = 0; $i < $request->get('att_count'); $i++) {
					$file = $request->file('att_' . $i);
					$nama = $file->getClientOriginalName();

					$filename = pathinfo($nama, PATHINFO_FILENAME);
					$extension = pathinfo($nama, PATHINFO_EXTENSION);

					$file_name = $form_number . '_' . $i . '.' . $extension;

					$file->move('files/ejor/att/', $file_name);

					$file_names[] = $file_name;
				}

				$att = implode(',', $file_names);
			}

			$ejor = new EjorForm;
			$ejor->form_id = $form_number;
			$ejor->request_date = date('Y-m-d');
			$ejor->job_type = $request->get('type');
			$ejor->job_category = $request->get('category');
			$ejor->job_category_note = $request->get('category_note');
			$ejor->section = $request->get('section');
			$ejor->target_date = $request->get('target');
			$ejor->title = $request->get('title');
			$ejor->description = $request->get('description');
			$ejor->purpose = $request->get('goal');
			$ejor->condition_before = $request->get('before');
			$ejor->condition_after = $request->get('after');
			$ejor->attachment = $att;

			if (strtoupper(Auth::user()->username) == 'PI1106001') {
				$ejor->status = 'Approval';
			} else {
				$ejor->status = 'Created';
			}

			$ejor->priority = $request->get('priority');
			$ejor->priority_reason = $request->get('priority_reason');
			$ejor->reason = $request->get('reason');
			$ejor->created_by = Auth::user()->username;

			$ejor->save();

			$emp = EmployeeSync::whereNUll('end_date')->where('employee_id', '=', Auth::user()->username)->first();

			// ------ APPR  ---------------
			$ejor_appr = new EjorFormApprover;
			$ejor_appr->form_id = $form_number;
			$ejor_appr->approver_id = strtoupper(Auth::user()->username);
			$ejor_appr->approver_name = Auth::user()->name;
			$ejor_appr->approve_at = date('Y-m-d H:i:s');
			$ejor_appr->status = 'Approved';
			$ejor_appr->remark = 'Requester';
			$ejor_appr->created_by = Auth::user()->username;

			$ejor_appr->save();

			// ------ APPR Chief ---------------
			$remark = '';
			if (!str_contains($emp->position, 'Leader')) {
				$remark = 'Foreman';
			} else {
				$remark = 'Chief';
			}

			$chf = Approver::where('section', 'LIKE', '%' . $emp->section . '%')
				->whereIn('remark', ['Foreman', 'Chief'])
				->where('approver_id', '<>', '')
				->first();

			if (count($chf) < 1) {
				$chf = Approver::where('department', '=', $emp->department);

				if ($emp->department == 'Production Engineering Department') {
					$chf = $chf->where('remark', ['Chief']);
				} else {
					$chf = $chf->where('remark', ['Foreman', 'Chief']);
				}

				$chf = $chf->where('approver_id', '<>', '')->first();
			}

			$ejor_appr = new EjorFormApprover;
			$ejor_appr->form_id = $form_number;
			$ejor_appr->approver_id = $chf->approver_id;
			$ejor_appr->approver_name = $chf->approver_name;
			$ejor_appr->remark = 'Chief';
			$ejor_appr->created_by = Auth::user()->username;

			if (strtoupper(Auth::user()->username) == 'PI1106001') {
				$ejor_appr->approve_at = date('Y-m-d H:i:s');
				$ejor_appr->status = 'Approved';
			}

			$ejor_appr->save();

			// ------ APPR Manager ---------------

			$mngr = Approver::where('department', '=', $emp->department)
				->where('remark', '=', 'Manager')
				->where('approver_id', '<>', '')
				->first();

			$ejor_appr = new EjorFormApprover;
			$ejor_appr->form_id = $form_number;
			$ejor_appr->approver_id = $mngr->approver_id;
			$ejor_appr->approver_name = $mngr->approver_name;
			$ejor_appr->remark = 'Manager';
			$ejor_appr->created_by = Auth::user()->username;

			if (strtoupper(Auth::user()->username) == 'PI1106001') {
				$ejor_appr->approve_at = date('Y-m-d H:i:s');
				$ejor_appr->status = 'Approved';
			}

			$ejor_appr->save();

			// ------ APPR Manager PE ---------------

			$mngr_pe = Approver::where('department', '=', 'Production Engineering Department')
				->where('remark', '=', 'Manager')
				->where('approver_id', '<>', '')
				->first();

			$ejor_appr = new EjorFormApprover;
			$ejor_appr->form_id = $form_number;
			$ejor_appr->approver_id = $mngr_pe->approver_id;
			$ejor_appr->approver_name = $mngr_pe->approver_name;
			$ejor_appr->remark = 'Manager PE';
			$ejor_appr->created_by = Auth::user()->username;

			if (strtoupper(Auth::user()->username) == 'PI1106001') {
				$ejor_appr->approve_at = date('Y-m-d H:i:s');
				$ejor_appr->status = 'Approved';
			}

			$ejor_appr->save();

			// ------ APPR Chief PE ---------------

			$chief_pe = Approver::where('department', '=', 'Production Engineering Department')
				->where('remark', '=', 'chief')
				->where('approver_id', '<>', '')
				->first();

			$ejor_appr = new EjorFormApprover;
			$ejor_appr->form_id = $form_number;
			$ejor_appr->approver_id = $chief_pe->approver_id;
			$ejor_appr->approver_name = $chief_pe->approver_name;
			$ejor_appr->remark = 'Chief PE';
			$ejor_appr->created_by = Auth::user()->username;

			$ejor_appr->save();

			$ejors = EjorForm::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'ejor_forms.created_by')
				->where('form_id', '=', $form_number)
				->select('form_id', db::raw('DATE_FORMAT(request_date, "%d %b %Y") req_date'), 'job_type', 'job_category', 'job_category_note', 'ejor_forms.section', db::raw('DATE_FORMAT(target_date, "%d %b %Y") target_date'), 'title', 'attachment', 'status', 'employee_syncs.name', 'pic', db::raw('DATE_FORMAT(pic_receive_date, "%d %b %Y") pic_date'), 'description', 'purpose', 'condition_before', 'condition_after', 'priority', 'priority_reason', 'reason')
				->first();

			$ej_app = EjorFormApprover::where('form_id', '=', $form_number)
				->select('form_id', 'approver_id', 'approver_name', db::raw('DATE_FORMAT(approve_at, "%d %b %Y") appr_at'), 'status', 'note')
				->orderBy('id', 'ASC')
				->get();

			$pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			$pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
			$pdf->setPaper('A4', 'potrait');

			$pdf->loadView('production_engineering.ejor.pdf', array(
				'form_data' => $ejors,
				'approval' => $ej_app,
			)
			);

			$pdf->save(public_path() . "/files/ejor/form/" . $form_number . ".pdf");

			$response = array(
				'status' => true,
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}

	}

	public function editEjorForm(Request $request)
	{
		try {
			$form_number = $request->get('form_id');
			$att = null;

			if ($request->get('att_count') > 0) {
				for ($i = 0; $i < $request->get('att_count'); $i++) {
					$file = $request->file('att_' . $i);
					$nama = $file->getClientOriginalName();

					$filename = pathinfo($nama, PATHINFO_FILENAME);
					$extension = pathinfo($nama, PATHINFO_EXTENSION);

					$file_name = $form_number . '_' . $i . '.' . $extension;

					$file->move('files/ejor/att/', $file_name);

					$file_names[] = $file_name;
				}

				$att = implode(',', $file_names);
			}

			EjorForm::where('form_id', $form_number)
				->update([
					'job_type' => $request->get('type'),
					'job_category' => $request->get('category'),
					'job_category_note' => $request->get('category_note'),
					'section' => $request->get('section'),
					'target_date' => $request->get('target'),
					'title' => $request->get('title'),
					'description' => $request->get('description'),
					'purpose' => $request->get('goal'),
					'condition_before' => $request->get('before'),
					'condition_after' => $request->get('after'),
					'attachment' => $att
				]);

			$ejors = EjorForm::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'ejor_forms.created_by')
				->where('form_id', '=', $form_number)
				->select('form_id', db::raw('DATE_FORMAT(request_date, "%d %b %Y") req_date'), 'job_type', 'job_category', 'job_category_note', 'ejor_forms.section', db::raw('DATE_FORMAT(target_date, "%d %b %Y") target_date'), 'title', 'attachment', 'status', 'employee_syncs.name', 'pic', db::raw('DATE_FORMAT(pic_receive_date, "%d %b %Y") pic_date'), 'description', 'purpose', 'condition_before', 'condition_after', 'priority', 'priority_reason', 'reason')
				->first();

			$ej_app = EjorFormApprover::where('form_id', '=', $form_number)
				->select('form_id', 'approver_id', 'approver_name', db::raw('DATE_FORMAT(approve_at, "%d %b %Y") appr_at'), 'status', 'note')
				->orderBy('id', 'ASC')
				->get();

			$pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			$pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
			$pdf->setPaper('A4', 'potrait');

			$pdf->loadView('production_engineering.ejor.pdf', array(
				'form_data' => $ejors,
				'approval' => $ej_app,
			)
			);

			$pdf->save(public_path() . "/files/ejor/form/" . $form_number . ".pdf");

			$response = array(
				'status' => true,
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}

	}

	public function sendMailEjor(Request $request)
	{
		try {
			$ejors = EjorForm::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'ejor_forms.created_by')
				->where('form_id', '=', $request->get('form_id'))
				->select('form_id', db::raw('DATE_FORMAT(request_date, "%d %b %Y") req_date'), 'job_type', 'job_category', 'job_category_note', 'ejor_forms.section', db::raw('DATE_FORMAT(target_date, "%d %b %Y") target_date'), 'title', 'attachment', 'status', 'employee_syncs.name', 'pic', db::raw('DATE_FORMAT(pic_receive_date, "%d %b %Y") pic_date'), 'priority', 'priority_reason', 'reason')
				->first();

			$ejor_app = EjorFormApprover::where('form_id', '=', $request->get('form_id'))
				->whereNull('approve_at')
				->orderBy('id', 'ASC')
				->first();

			$ej_app = EjorFormApprover::where('form_id', '=', $request->get('form_id'))
				->select('form_id', 'approver_id', 'approver_name', db::raw('DATE_FORMAT(approve_at, "%d %b %Y") appr_at'), 'status', 'note')
				->orderBy('id', 'ASC')
				->get();

			$email = User::where('username', '=', $ejor_app->approver_id)
				->select('email')
				->first();

			EjorForm::where('form_id', $request->get('form_id'))
				->update([
					'status' => 'Approval',
				]);

			$data = [
				"datas" => $ejors,
				"subject" => 'Approval EJOR',
				"position" => 'Chief_Foreman',
				// Chief_Foreman, Manager, Manager_PE, Chief_PE
				"appr" => $ej_app
			];

			Mail::to($email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'approval_ejor'));

			$response = array(
				'status' => true
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);

		}
	}

	public function generatePdfEjor($form_number)
	{
		$ejors = EjorForm::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'ejor_forms.created_by')
			->where('form_id', '=', $form_number)
			->select('form_id', db::raw('DATE_FORMAT(request_date, "%d %b %Y") req_date'), 'job_type', 'job_category', 'job_category_note', 'ejor_forms.section', db::raw('DATE_FORMAT(target_date, "%d %b %Y") target_date'), 'title', 'attachment', 'status', 'employee_syncs.name', 'pic', db::raw('DATE_FORMAT(pic_receive_date, "%d %b %Y") pic_date'), 'description', 'purpose', 'condition_before', 'condition_after', 'priority', 'priority_reason', 'reason')
			->first();

		$ej_app = EjorFormApprover::where('form_id', '=', $form_number)
			->select('form_id', 'approver_id', 'approver_name', db::raw('DATE_FORMAT(approve_at, "%d %b %Y") appr_at'), 'status', 'note')
			->orderBy('id', 'ASC')
			->get();

		$pdf = \App::make('dompdf.wrapper');
		$pdf->getDomPDF()->set_option("enable_php", true);
		$pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
		$pdf->setPaper('A4', 'potrait');

		$pdf->loadView('production_engineering.ejor.pdf', array(
			'form_data' => $ejors,
			'approval' => $ej_app,
		)
		);

		$pdf->save(public_path() . "/files/ejor/form/" . $form_number . ".pdf");
	}

	public function indexEjorMonitoring(Request $request)
	{
		$title = "Monitoring Engineering Job Request (EJOR)";
		$title_jp = "";
		$pics = TicketPic::where('remark', '=', 'ejor')->get();

		$emp_id = strtoupper(Auth::user()->username);
		$_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/" . $emp_id);

		return view('production_engineering.ejor.monitoring', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'pics' => $pics,
			'categories' => $this->category,
			'types' => $this->type
		)
		)->with('page', 'EJOR')->with('head', 'Ejor');
	}

	public function fetchEjorMonitoring(Request $request)
	{
		$ejor = EjorForm::orderBy('ejor_forms.form_id', 'DESC')
			->leftJoin(db::raw('(select section, department from employee_syncs group by section, department) as emp'), 'emp.section', '=', 'ejor_forms.section')
			->leftJoin('departments', 'departments.department_name', '=', 'emp.department')
			->leftJoin('ticket_pics', 'ticket_pics.pic_id', '=', db::raw("SPLIT_STRING(pic, '/', 1)"))
			->leftJoin('ejor_evidences', 'ejor_evidences.form_id', '=', 'ejor_forms.form_id')
			->select(
				'ejor_forms.form_id',
				'ejor_forms.status',
				'ejor_forms.priority',
				'emp.department',
				'ejor_forms.job_type',
				'ejor_forms.job_category',
				'ejor_forms.job_category_note',
				'ejor_forms.title',
				'ejor_forms.description',
				'ejor_forms.purpose',
				'ejor_forms.condition_before',
				'ejor_forms.condition_after',
				'ejor_forms.target_date',
				'ejor_forms.attachment',
				'ejor_forms.pic',
				db::raw("SPLIT_STRING(pic, '/', 1) as pic_id"),
				'ticket_pics.pic_shortname',
				'ejor_forms.remark',
				'ejor_forms.created_by',
				'ejor_forms.created_at',
				'ejor_forms.updated_at',
				'departments.department_shortname',
				db::raw('ejor_evidences.status AS status_ev'),
				db::raw('ejor_evidences.attachment AS att_ev')
			)
			->get();

		$ejor_appr = EjorFormApprover::where('remark', '<>', 'Requester')->orderBy('id', 'asc')->get();

		$counts = db::select('SELECT DATE_FORMAT(target_date,"%Y %b") as mon, DATE_FORMAT(target_date,"%Y-%m") as mon2, `status`, count(id) as jml_ejor from ejor_forms
			where `status` <> "Finished" OR `status` <> "Rejected"
			GROUP BY DATE_FORMAT(target_date,"%Y %b"), DATE_FORMAT(target_date,"%Y-%m"), `status`
			ORDER BY mon2 asc');

		$departments = db::table('departments')
			->where('department_shortname', '!=', 'JPN')
			->orderBy('department_name', 'ASC')
			->get();

		$atts = EjorForm::leftJoin('ejor_evidences', 'ejor_forms.form_id', '=', 'ejor_evidences.form_id')
			->select('ejor_forms.form_id', 'ejor_forms.attachment', 'ejor_evidences.uploaded_at', db::raw('ejor_evidences.attachment as evv'))
			->get();

		$response = array(
			'status' => true,
			'ejors' => $ejor,
			'charts' => $counts,
			'ejor_approvers' => $ejor_appr,
			'departments' => $departments,
			'atts' => $atts,
		);
		return Response::json($response);
	}

	public function approvalEjor($form_id, $status, $position, Request $request)
	{
		$ejorForm = EjorForm::where('form_id', $form_id)->first();
		$title = '';
		$title_jp = '';
		$pic = [];

		if ($status == 'Approved') {
			if ($position == 'Chief_Foreman' || $position == 'Manager' || $position == 'Manager_PE') {
				if ($position == 'Chief_Foreman') {
					$pos = 'Chief';
				} else if ($position == 'Manager') {
					$pos = 'Manager';
				} else if ($position == 'Manager_PE') {
					$pos = 'Manager PE';
				}

				$apr = EjorFormApprover::where('form_id', $form_id)
					->where('remark', $pos)
					->first();

				if (strtoupper(Auth::user()->username) != strtoupper($apr->approver_id)) {
					$msg = 'You are not allowed to access this approval';
					$status = 'Not Allowed';

					return view('production_engineering.ejor.approval_message', array(
						'title' => $title,
						'title_jp' => $title_jp,
						'message' => $msg,
						'status' => $status,
						'ejor' => $ejorForm
					)
					)->with('page', 'EJOR')->with('head', 'Ejor');
				}

				$app_stat = EjorFormApprover::where('form_id', $form_id)
					->where('approver_id', '=', Auth::user()->username)
					->select('approve_at')
					->first();

				if ($app_stat->approve_at) {
					$msg = '';
					$status = 'Already Approved';

					return view('production_engineering.ejor.approval_message', array(
						'title' => $title,
						'title_jp' => $title_jp,
						'message' => $msg,
						'status' => $status,
						'ejor' => $ejorForm
					)
					)->with('page', 'EJOR')->with('head', 'Ejor');
				}


				EjorFormApprover::where('form_id', $form_id)
					->where('approver_id', '=', Auth::user()->username)
					->update([
						'status' => $status,
						'approve_at' => date('Y-m-d H:i:s'),
					]);

				// ------------ PDF -----------

				$ejors = EjorForm::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'ejor_forms.created_by')
					->where('form_id', '=', $form_id)
					->select('form_id', db::raw('DATE_FORMAT(request_date, "%d %b %Y") req_date'), 'job_type', 'job_category', 'job_category_note', 'ejor_forms.section', db::raw('DATE_FORMAT(target_date, "%d %b %Y") target_date'), 'title', 'attachment', 'status', 'employee_syncs.name', 'pic', db::raw('DATE_FORMAT(pic_receive_date, "%d %b %Y") pic_date'), 'description', 'purpose', 'condition_before', 'condition_after', 'priority', 'priority_reason', 'reason')
					->first();

				$ej_app = EjorFormApprover::where('form_id', '=', $form_id)
					->select('form_id', 'approver_id', 'approver_name', db::raw('DATE_FORMAT(approve_at, "%d %b %Y") appr_at'), 'status', 'note')
					->orderBy('id', 'ASC')
					->get();

				$pdf = \App::make('dompdf.wrapper');
				$pdf->getDomPDF()->set_option("enable_php", true);
				$pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
				$pdf->setPaper('A4', 'potrait');

				$pdf->loadView('production_engineering.ejor.pdf', array(
					'form_data' => $ejors,
					'approval' => $ej_app,
				)
				);

				$pdf->save(public_path() . "/files/ejor/form/" . $form_id . ".pdf");

				// --------- SEND MAIL -------

				$get_appr = EjorFormApprover::leftJoin('users', 'users.username', '=', 'ejor_form_approvers.approver_id')
					->whereNull('approve_at')
					->where('form_id', '=', $form_id)
					->orderBy('ejor_form_approvers.id', 'asc')
					->first();

				$status_approval = str_replace(' ', '_', $get_appr->remark);

				$data = [
					"datas" => $ejors,
					"subject" => 'Approval EJOR',
					"position" => $status_approval,
					"appr" => $ej_app
				];

				Mail::to($get_appr->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'approval_ejor'));

				$msg = 'Successfully Approved';
				$status = 'Approved';

				// return view('production_engineering.ejor.approval_message', array(
				// 	'title' => $title,
				// 	'title_jp' => $title_jp,
				// 	'message' => $msg,
				// 	'status' => $status,
				// 	'ejor' => $ejorForm
				// ))->with('page', 'EJOR')->with('head', 'Ejor');
			} else if ($position == 'Chief_PE') {
				$apr = EjorFormApprover::where('form_id', $form_id)
					->where('remark', 'Chief PE')
					->first();

				if (strtoupper(Auth::user()->username) != strtoupper($apr->approver_id)) {
					$msg = 'You are not allowed to access this approval';
					$status = 'Not Allowed';

					return view('production_engineering.ejor.approval_message', array(
						'title' => $title,
						'title_jp' => $title_jp,
						'message' => $msg,
						'status' => $status,
						'ejor' => $ejorForm
					)
					)->with('page', 'EJOR')->with('head', 'Ejor');
				}

				$msg = 'Please Select PIC';
				$status = 'Received';

				$pic = TicketPic::where('remark', 'ejor')
					->select('pic_id', 'pic_name')
					->get();
			} else if ($position == 'Staff_PE') {
				EjorFormApprover::where('form_id', $form_id)
					->where('remark', '=', 'Chief PE')
					->update([
						'status' => $status,
						'approve_at' => date('Y-m-d H:i:s'),
					]);

				EjorForm::where('form_id', $form_id)
					->update([
						'pic' => $request->get('pic'),
						'status' => 'Waiting'
					]);

				// ------------ PDF -----------

				$ejors = EjorForm::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'ejor_forms.created_by')
					->where('form_id', '=', $form_id)
					->select('form_id', db::raw('DATE_FORMAT(request_date, "%d %b %Y") req_date'), 'job_type', 'job_category', 'job_category_note', 'ejor_forms.section', db::raw('DATE_FORMAT(target_date, "%d %b %Y") target_date'), 'title', 'attachment', 'status', 'employee_syncs.name', 'pic', db::raw('DATE_FORMAT(pic_receive_date, "%d %b %Y") pic_date'), 'description', 'purpose', 'condition_before', 'condition_after', 'priority', 'priority_reason', 'reason')
					->first();

				$ej_app = EjorFormApprover::where('form_id', '=', $form_id)
					->select('form_id', 'approver_id', 'approver_name', db::raw('DATE_FORMAT(approve_at, "%d %b %Y") appr_at'), 'status', 'note')
					->orderBy('id', 'ASC')
					->get();

				$pdf = \App::make('dompdf.wrapper');
				$pdf->getDomPDF()->set_option("enable_php", true);
				$pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
				$pdf->setPaper('A4', 'potrait');

				$pdf->loadView('production_engineering.ejor.pdf', array(
					'form_data' => $ejors,
					'approval' => $ej_app,
				)
				);

				$pdf->save(public_path() . "/files/ejor/form/" . $form_id . ".pdf");

				// --------- SEND MAIL -------

				$get_appr = EjorForm::where('form_id', '=', $form_id)->select('pic')->first();

				$email = User::where('users.username', '=', explode('/', $get_appr->pic)[0])->select('email')->first();

				$data = [
					"datas" => $ejors,
					"subject" => 'Approval EJOR',
					"position" => 'Receive_Staff',
					"appr" => $ej_app
				];

				Mail::to($email->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'approval_ejor'));

				$msg = 'Successfully Approved';
				$status = 'Approved';
			} else if ($position == 'Receive_Staff') {
				$apr = EjorForm::where('form_id', $form_id)
					->select(db::raw("SPLIT_STRING(pic, '/', 1) as pic_id"))
					->first();

				if (strtoupper(Auth::user()->username) != strtoupper($apr->pic_id)) {
					$msg = 'You are not allowed to access this approval';
					$status = 'Not Allowed';

					return view('production_engineering.ejor.approval_message', array(
						'title' => $title,
						'title_jp' => $title_jp,
						'message' => $msg,
						'status' => $status,
						'ejor' => $ejorForm
					)
					)->with('page', 'EJOR')->with('head', 'Ejor');
				}


				EjorForm::where('form_id', $form_id)
					->update([
						'pic_receive_date' => date('Y-m-d H:i:s'),
						'status' => 'InProgress'
					]);

				// ------------ PDF -----------

				$ejors = EjorForm::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'ejor_forms.created_by')
					->where('form_id', '=', $form_id)
					->select('form_id', db::raw('DATE_FORMAT(request_date, "%d %b %Y") req_date'), 'job_type', 'job_category', 'job_category_note', 'ejor_forms.section', db::raw('DATE_FORMAT(target_date, "%d %b %Y") target_date'), 'title', 'attachment', 'status', 'employee_syncs.name', 'pic', db::raw('DATE_FORMAT(pic_receive_date, "%d %b %Y") pic_date'), 'description', 'purpose', 'condition_before', 'condition_after', 'priority', 'priority_reason', 'reason')
					->first();

				$ej_app = EjorFormApprover::where('form_id', '=', $form_id)
					->select('form_id', 'approver_id', 'approver_name', db::raw('DATE_FORMAT(approve_at, "%d %b %Y") appr_at'), 'status', 'note')
					->orderBy('id', 'ASC')
					->get();

				$pdf = \App::make('dompdf.wrapper');
				$pdf->getDomPDF()->set_option("enable_php", true);
				$pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
				$pdf->setPaper('A4', 'potrait');

				$pdf->loadView('production_engineering.ejor.pdf', array(
					'form_data' => $ejors,
					'approval' => $ej_app,
				)
				);

				$pdf->save(public_path() . "/files/ejor/form/" . $form_id . ".pdf");

				$msg = 'Successfully Approved';
				$status = 'Approved';
			}

		} else if ($status == 'Hold') {
			if ($position == 'Holded') {
				EjorForm::where('form_id', $form_id)
					->update([
						'status' => 'OnHold'
					]);

				EjorFormApprover::where('form_id', $form_id)
					->where('approver_id', '=', Auth::user()->username)
					->update([
						'approve_at' => date('Y-m-d H:i:s'),
						'status' => 'OnHold',
						'Note' => $request->get('note'),
					]);

				// ------------ PDF -----------

				$ejors = EjorForm::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'ejor_forms.created_by')
					->where('form_id', '=', $form_id)
					->select('form_id', db::raw('DATE_FORMAT(request_date, "%d %b %Y") req_date'), 'job_type', 'job_category', 'job_category_note', 'ejor_forms.section', db::raw('DATE_FORMAT(target_date, "%d %b %Y") target_date'), 'title', 'attachment', 'status', 'employee_syncs.name', 'pic', db::raw('DATE_FORMAT(pic_receive_date, "%d %b %Y") pic_date'), 'description', 'purpose', 'condition_before', 'condition_after', 'priority', 'priority_reason', 'reason')
					->first();

				$ej_app = EjorFormApprover::where('form_id', '=', $form_id)
					->select('form_id', 'approver_id', 'approver_name', db::raw('DATE_FORMAT(approve_at, "%d %b %Y") appr_at'), 'status', 'note')
					->orderBy('id', 'ASC')
					->get();

				$pdf = \App::make('dompdf.wrapper');
				$pdf->getDomPDF()->set_option("enable_php", true);
				$pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
				$pdf->setPaper('A4', 'potrait');

				$pdf->loadView('production_engineering.ejor.pdf', array(
					'form_data' => $ejors,
					'approval' => $ej_app,
				)
				);

				$pdf->save(public_path() . "/files/ejor/form/" . $form_id . ".pdf");

				// --------- SEND MAIL -------

				$get_appr = EjorForm::where('form_id', '=', $form_id)->select('created_by')->first();

				$data = [
					"datas" => $ejors,
					"subject" => 'Approval EJOR',
					"position" => 'Hold',
					"appr" => $ej_app
				];

				$mail = User::where('username', $get_appr->created_by)
					->leftJoin('employee_syncs', 'users.username', '=', 'employee_syncs.employee_id')
					->first();

				if (str_contains($mail->email, '@music.yamaha.com')) {
					Mail::to($mail->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'approval_ejor'));
				} else {
					$ml = Approver::where('section', '=', $mail->section)
						->first();

					Mail::to($ml->approver_email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'approval_ejor'));
				}

				$msg = 'Successfully Holded';
				$status = 'Hold & Comment';
			} else {
				if ($position == 'Chief_Foreman') {
					$pos = 'Chief';
				} else if ($position == 'Manager') {
					$pos = 'Manager';
				} else if ($position == 'Manager_PE') {
					$pos = 'Manager PE';
				}

				$apr = EjorFormApprover::where('form_id', $form_id)
					->where('remark', $pos)
					->first();

				if (strtoupper(Auth::user()->username) != strtoupper($apr->approver_id)) {
					$msg = 'You are not allowed to access this approval';
					$status = 'Not Allowed';

					return view('production_engineering.ejor.approval_message', array(
						'title' => $title,
						'title_jp' => $title_jp,
						'message' => $msg,
						'status' => $status,
						'ejor' => $ejorForm
					)
					)->with('page', 'EJOR')->with('head', 'Ejor');
				}

				$msg = 'Hold & Comment';
				$status = 'Hold';
			}
		} else if ($status == 'Rejected') {
			if ($position == 'Rejected') {
				EjorForm::where('form_id', $form_id)
					->update([
						'status' => 'Rejected'
					]);

				EjorFormApprover::where('form_id', $form_id)
					->where('approver_id', '=', Auth::user()->username)
					->update([
						'approve_at' => date('Y-m-d H:i:s'),
						'status' => 'Rejected',
						'Note' => $request->get('note'),
					]);

				// ------------ PDF -----------

				$ejors = EjorForm::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'ejor_forms.created_by')
					->where('form_id', '=', $form_id)
					->select('form_id', db::raw('DATE_FORMAT(request_date, "%d %b %Y") req_date'), 'job_type', 'job_category', 'job_category_note', 'ejor_forms.section', db::raw('DATE_FORMAT(target_date, "%d %b %Y") target_date'), 'title', 'attachment', 'status', 'employee_syncs.name', 'pic', db::raw('DATE_FORMAT(pic_receive_date, "%d %b %Y") pic_date'), 'description', 'purpose', 'condition_before', 'condition_after', 'priority', 'priority_reason', 'reason')
					->first();

				$ej_app = EjorFormApprover::where('form_id', '=', $form_id)
					->select('form_id', 'approver_id', 'approver_name', db::raw('DATE_FORMAT(approve_at, "%d %b %Y") appr_at'), 'status', 'note')
					->orderBy('id', 'ASC')
					->get();

				$pdf = \App::make('dompdf.wrapper');
				$pdf->getDomPDF()->set_option("enable_php", true);
				$pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
				$pdf->setPaper('A4', 'potrait');

				$pdf->loadView('production_engineering.ejor.pdf', array(
					'form_data' => $ejors,
					'approval' => $ej_app,
				)
				);

				$pdf->save(public_path() . "/files/ejor/form/" . $form_id . ".pdf");

				// --------- SEND MAIL -------

				$get_appr = EjorForm::where('form_id', '=', $form_id)->select('created_by')->first();

				$data = [
					"datas" => $ejors,
					"subject" => 'Approval EJOR',
					"position" => 'Reject',
					"appr" => $ej_app
				];

				$mail = User::where('username', $get_appr->created_by)
					->leftJoin('employee_syncs', 'users.username', '=', 'employee_syncs.employee_id')
					->first();

				if (str_contains($mail->email, '@music.yamaha.com')) {
					Mail::to($mail->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'approval_ejor'));
				} else {
					$ml = Approver::where('section', '=', $mail->section)
						->first();

					Mail::to($ml->approver_email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'approval_ejor'));
				}


				Mail::to($mail->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'approval_ejor'));

				$msg = 'Successfully Rejected';
				$status = 'Reject & Comment';
			} else {
				if ($position == 'Chief_Foreman') {
					$pos = 'Chief';
				} else if ($position == 'Manager') {
					$pos = 'Manager';
				} else if ($position == 'Manager_PE') {
					$pos = 'Manager PE';
				} else if ($position == 'Chief_PE') {
					$pos = 'Chief PE';
				}

				$apr = EjorFormApprover::where('form_id', $form_id)
					->where('remark', $pos)
					->first();

				if (strtoupper(Auth::user()->username) != strtoupper($apr->approver_id)) {
					$msg = 'You are not allowed to access this approval';
					$status = 'Not Allowed';

					return view('production_engineering.ejor.approval_message', array(
						'title' => $title,
						'title_jp' => $title_jp,
						'message' => $msg,
						'status' => $status,
						'ejor' => $ejorForm
					)
					)->with('page', 'EJOR')->with('head', 'Ejor');
				}

				$msg = 'Reject & Comment';
				$status = 'Reject';
			}
		}

		return view('production_engineering.ejor.approval_message', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'message' => $msg,
			'status' => $status,
			'ejor' => $ejorForm,
			'pics' => $pic
		)
		)->with('page', 'EJOR')->with('head', 'Ejor');
	}

	public function indexApprovalEjor($form_id)
	{
		$ejors = EjorForm::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'ejor_forms.created_by')
			->where('form_id', '=', $form_id)
			->select('form_id', db::raw('DATE_FORMAT(request_date, "%d %b %Y") req_date'), 'job_type', 'job_category', 'job_category_note', 'ejor_forms.section', db::raw('DATE_FORMAT(target_date, "%d %b %Y") target_date'), 'title', 'attachment', 'status', 'employee_syncs.name', 'pic', db::raw('DATE_FORMAT(pic_receive_date, "%d %b %Y") pic_date'), 'description', 'purpose', 'condition_before', 'condition_after', 'priority', 'priority_reason', 'reason')
			->first();

		$ej_app = EjorFormApprover::where('form_id', '=', $form_id)
			->select('form_id', 'approver_id', 'approver_name', db::raw('DATE_FORMAT(approve_at, "%d %b %Y") appr_at'), 'status', 'note', 'remark')
			->orderBy('id', 'ASC')
			->get();

		$position = '';

		foreach ($ej_app as $ej) {
			if (!$ej->status) {
				$position = $ej->remark;
				break;
			}
		}

		if ($position == 'Chief') {
			$pos = 'Chief_Foreman';
		} else if ($position == 'Manager') {
			$pos = 'Manager';
		} else if ($position == 'Manager PE') {
			$pos = 'Manager_PE';
		} else if ($position == 'Chief PE') {
			$pos = 'Chief_PE';
		}

		if ($ejors->status == 'Waiting') {
			$pos = 'Receive_Staff';
		}

		$data = [
			"datas" => $ejors,
			"subject" => 'Approval EJOR',
			"position" => $pos,
			"appr" => $ej_app
		];

		return view('production_engineering.ejor.approval_email')->with('page', 'EJOR')->with('data', $data)->with('head', 'Ejor');
	}

	public function postEjorEvidence(Request $request)
	{
		try {

			$att = null;

			if ($request->get('att_count') > 0) {
				for ($i = 0; $i < $request->get('att_count'); $i++) {
					$file = $request->file('att_' . $i);
					$nama = $file->getClientOriginalName();

					$filename = pathinfo($nama, PATHINFO_FILENAME);
					$extension = pathinfo($nama, PATHINFO_EXTENSION);

					$file_name = 'EV_' . $request->get('form_id') . '_' . $i . '.' . $extension;

					$file->move('files/ejor/evidence/', $file_name);

					$file_names[] = $file_name;
				}

				$att = implode(',', $file_names);
			}


			$appr = Approver::where('remark', 'Chief')->where('department', 'Production Engineering Department')
				->first();

			$app = EjorEvidence::firstOrNew(array('form_id' => $request->get('form_id')));
			$app->form_id = $request->get('form_id');
			$app->uploaded_by = strtoupper(Auth::user()->username) . '/' . Auth::user()->name;
			$app->uploaded_at = date('Y-m-d H:i:s');
			$app->note = $request->get('note');
			$app->attachment = $att;
			$app->status = 'Verifying';
			$app->approve_by = $appr->approver_id . '/' . $appr->approver_name;
			$app->created_by = Auth::user()->username;
			$app->save();

			EjorForm::where('form_id', $request->get('form_id'))
				->update([
					'status' => 'Verifying'
				]);

			$ejors = EjorForm::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'ejor_forms.created_by')
				->where('form_id', '=', $request->get('form_id'))
				->select('form_id', db::raw('DATE_FORMAT(request_date, "%d %b %Y") req_date'), 'job_type', 'job_category', 'job_category_note', 'ejor_forms.section', db::raw('DATE_FORMAT(target_date, "%d %b %Y") target_date'), 'title', 'attachment', 'status', 'employee_syncs.name', 'pic', db::raw('DATE_FORMAT(pic_receive_date, "%d %b %Y") pic_date'), 'description', 'purpose', 'condition_before', 'condition_after', 'priority', 'priority_reason', 'reason')
				->first();

			$ej_app = EjorFormApprover::where('form_id', '=', $request->get('form_id'))
				->select('form_id', 'approver_id', 'approver_name', db::raw('DATE_FORMAT(approve_at, "%d %b %Y") appr_at'), 'status', 'note')
				->orderBy('id', 'ASC')
				->get();

			$ev = EjorEvidence::where('form_id', $request->get('form_id'))
				->first();


			$data = [
				"datas" => $ejors,
				"evidence" => $ev,
				"subject" => 'Verification EJOR Evidence',
				"position" => 'Chief PE',
				"appr" => $ej_app
			];

			Mail::to($appr->approver_email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'verify_ejor'));
			$response = array(
				'status' => true,
			);
			return Response::json($response);
		} catch (Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function indexVerifyEjorPage($form_id)
	{

		$ejors = EjorForm::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'ejor_forms.created_by')
			->where('form_id', '=', $form_id)
			->select('form_id', db::raw('DATE_FORMAT(request_date, "%d %b %Y") req_date'), 'job_type', 'job_category', 'job_category_note', 'ejor_forms.section', db::raw('DATE_FORMAT(target_date, "%d %b %Y") target_date'), 'title', 'attachment', 'status', 'employee_syncs.name', 'pic', db::raw('DATE_FORMAT(pic_receive_date, "%d %b %Y") pic_date'), 'description', 'purpose', 'condition_before', 'condition_after', 'priority', 'priority_reason', 'reason')
			->first();

		$ej_app = EjorFormApprover::where('form_id', '=', $form_id)
			->select('form_id', 'approver_id', 'approver_name', db::raw('DATE_FORMAT(approve_at, "%d %b %Y") appr_at'), 'status', 'note')
			->orderBy('id', 'ASC')
			->get();

		$ev = EjorEvidence::where('form_id', $form_id)
			->first();

		$data = [
			"datas" => $ejors,
			"evidence" => $ev,
			"subject" => 'Verification EJOR Evidence',
			"position" => 'Chief PE',
			"appr" => $ej_app,
			'versi' => 'web'
		];

		return view('production_engineering.ejor.verify_email')->with('page', 'EJOR')->with('data', $data)->with('head', 'Ejor');
	}

	public function indexVerifyEjor($form_id, $status, Request $request)
	{
		$ejorForm = EjorForm::where('form_id', $form_id)->first();

		$title = '';
		$title_jp = '';

		$ejors = EjorForm::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'ejor_forms.created_by')
			->where('form_id', '=', $form_id)
			->select('form_id', db::raw('DATE_FORMAT(request_date, "%d %b %Y") req_date'), 'job_type', 'job_category', 'job_category_note', 'ejor_forms.section', db::raw('DATE_FORMAT(target_date, "%d %b %Y") target_date'), 'title', 'attachment', 'status', 'employee_syncs.name', 'pic', db::raw('DATE_FORMAT(pic_receive_date, "%d %b %Y") pic_date'), 'description', 'purpose', 'condition_before', 'condition_after', 'priority', 'priority_reason', 'reason')
			->first();

		$ej_app = EjorFormApprover::where('form_id', '=', $form_id)
			->select('form_id', 'approver_id', 'approver_name', db::raw('DATE_FORMAT(approve_at, "%d %b %Y") appr_at'), 'status', 'note')
			->orderBy('id', 'ASC')
			->get();

		$ev = EjorEvidence::where('form_id', $form_id)
			->first();

		if ($status == 'Approved') {
			$msg = 'EJOR Evidence(s) Successfully Approved';

			EjorForm::where('form_id', $form_id)
				->update([
					'status' => 'Finished'
				]);

			EjorEvidence::where('form_id', $form_id)
				->update([
					'approve_at' => date('Y-m-d H:i:s'),
					'status' => 'Approved'
				]);

			$data = [
				"datas" => $ejors,
				"evidence" => $ev,
				"subject" => 'Engineering Job Request (EJOR)',
				"position" => 'User',
				"appr" => $ej_app
			];

			$mail = User::where('username', $ejorForm->created_by)
				->leftJoin('employee_syncs', 'users.username', '=', 'employee_syncs.employee_id')
				->first();

			if (str_contains($mail->email, '@music.yamaha.com')) {
				Mail::to($mail->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'verify_ejor'));
			} else {
				$ml = Approver::where('section', '=', $mail->section)
					->first();

				Mail::to($ml->approver_email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'verify_ejor'));
			}

		} else if ($status == 'Evidence_Rejected') {
			$msg = 'Please Add Rejection Note';

			// EjorEvidence::where('form_id', $form_id)
			// ->update([
			// 	'approve_at' => date('Y-m-d H:i:s'),
			// 	'status' => 'Rejected',
			// 	'remark' => $request->get('note'),
			// ]);

		} else if ($status == 'Reject') {
			$msg = 'EJOR Evidence(s) Successfully Rejected';

			$updt = EjorEvidence::where('form_id', $form_id)
				->update([
					'remark' => $request->get('note_ev'),
					'status' => 'Rejected',
				]);

			$ev = EjorEvidence::where('form_id', $form_id)
				->first();

			$data = [
				"datas" => $ejors,
				"evidence" => $ev,
				"subject" => 'Engineering Job Request (EJOR)',
				"position" => 'Reject',
				"appr" => $ej_app
			];



			$email = User::where('username', $ev->created_by)->first();

			Mail::to($email->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'verify_ejor'));
		}

		return view('production_engineering.ejor.approval_message', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'message' => $msg,
			'status' => $status,
			'ejor' => $ejorForm,
		)
		)->with('page', 'EJOR')->with('head', 'Ejor');
	}
}