<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\UserDocument;
use App\EmployeeSync;
use Response;
use DataTables;
use File;

class UserDocumentController extends Controller {

	private $category;
	private $status;
	private $condition;

	public function __construct() {
		$this->middleware('auth');
		$this->category = [
			'PASPOR',
			'KITAS',
			'MERP',
			'SKLD',
			'SKJ',
			'NOTIF'
		];
		$this->status = [
			'Active',
			'Inactive'
		];
		$this->condition = [
			'Safe',
			'At Risk',
			'Expired'
		];
	}

	public function index(){
		$document = UserDocument::get();
		
		$users = UserDocument::leftJoin('users', 'users.username', '=', 'user_documents.employee_id')
		->select('user_documents.employee_id', 'users.name')
		->distinct()
		->get();

		$employees = EmployeeSync::whereNull('end_date')->get();

		$exp_paspor = 0;
		$exp_kitas = 0;
		$exp_merp = 0;
		$exp_notif = 0;
		$exp_skj = 0;
		$exp_skld = 0;

		for ($i=0; $i < count($document); $i++) { 
			switch ($document[$i]->category) {
				case "PASPOR":
				$exp_paspor = $document[$i]->reminder;
				break;
				case "KITAS":
				$exp_kitas = $document[$i]->reminder;
				break;
				case "MERP":
				$exp_merp = $document[$i]->reminder;
				break;
				case "NOTIF":
				$exp_notif = $document[$i]->reminder;
				break;
				case "SKJ":
				$exp_skj = $document[$i]->reminder;
				break;
				case "SKLD":
				$exp_skld = $document[$i]->reminder;
				break;	
			}			
		}

		return view('user_documents.index', array(
			'categories' => $this->category,
			'conditions' => $this->condition,
			'documents' => $document,
			'users' => $users,
			'employees' => $employees,
			'exp_paspor' => $exp_paspor,
			'exp_kitas' => $exp_kitas,
			'exp_merp' => $exp_merp,
			'exp_notif' => $exp_notif,
			'exp_skj' => $exp_skj,
			'exp_skld' => $exp_skld,
		))->with('page', 'User Document');
	}

	public function fetchUserDocument(Request $request){
		$document = UserDocument::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'user_documents.employee_id');

		if($request->get('documentNumber') != null){
			$document = $document->whereIn('user_documents.document_number', $request->get('documentNumber'));
		}

		if($request->get('employeId') != null){
			$document = $document->whereIn('user_documents.employee_id', $request->get('employeId'));
		}

		if($request->get('category') != null){
			$document = $document->whereIn('user_documents.category', $request->get('category'));
		}

		if($request->get('condition') != null){
			$document = $document->whereIn('user_documents.condition', $request->get('condition'));
		}

		$document = $document->select(
			'user_documents.document_number',
			'user_documents.employee_id',
			'employee_syncs.name',
			'employee_syncs.position',
			'user_documents.valid_from',
			'user_documents.valid_to',
			'user_documents.category',
			'user_documents.status',
			'user_documents.condition',
			'user_documents.attachment'
		)
		->orderBy(db::raw('FIELD(user_documents.status ,"Active", "Inactive")'))
		->orderBy(db::raw('FIELD(user_documents.condition ,"Expired", "At Risk", "Safe")'))
		->orderBy('employee_id', 'asc')
		->get();

		return DataTables::of($document)
		->addColumn('button', function($document){
			if($document->status == 'Active'){
				// return '<button style="margin-right: 2px;" onClick="showRenew(this)" id="'.$document->document_number.'" class="btn btn-xs btn-primary form-control">Renew</button><button onClick="showUpdate(this)" id="'.$document->document_number.'+Inactive" class="btn btn-xs btn-warning form-control">Inactive</button>';
				return '<a href="javascript:void(0)" style="padding-top: 2%; padding-bottom: 2%; margin-top: 2%; margin-bottom: 2%;" class="btn btn-sm btn-primary" onClick="showRenew(this)" id="' . $document->document_number . '">Renew</a>&nbsp;<a href="javascript:void(0)" style="padding-top: 2%; padding-bottom: 2%; margin-top: 2%; margin-bottom: 2%;" class="btn btn-sm btn-warning" onClick="showUpdate(this)" id="' . $document->document_number . '+Inactive">Inactive</a>';
			}else if($document->status == 'Inactive'){
				// return '<button style="margin-right: 2px;" onClick="showRenew(this)" id="'.$document->document_number.'" class="btn btn-xs btn-primary form-control">Renew</button><button onClick="showUpdate(this)" id="'.$document->document_number.'+Active" class="btn btn-xs btn-success form-control">Active</button>';
				return '<a href="javascript:void(0)" style="padding-top: 2%; padding-bottom: 2%; margin-top: 2%; margin-bottom: 2%;" class="btn btn-sm btn-primary" onClick="showRenew(this)" id="' . $document->document_number . '">Renew</a>&nbsp;<a href="javascript:void(0)" style="padding-top: 2%; padding-bottom: 2%; margin-top: 2%; margin-bottom: 2%;" class="btn btn-sm btn-success" onClick="showUpdate(this)" id="' . $document->document_number . '+Active">Active</a>';
			}
			
		})
		->addColumn('attachment', function($document){
			if(!is_null($document->attachment)){
				return '<a href="javascript:void(0)" style="padding-top: 2%; padding-bottom: 2%; margin-top: 2%; margin-bottom: 2%;" class="btn btn-sm btn-default" onClick="downloadAtt(id)" id="' . $document->attachment . '"><i class="fa fa-share-square-o"></i>&nbsp;&nbsp;&nbsp;<b>Open</b></a>';
			}else{
				return '<span class="glyphicon glyphicon-minus"></span>';
			}
			
		})
		->rawColumns([
			'button' => 'button',
			'attachment' => 'attachment'
		])
		->make(true);
	}

	public function fetchUserDocumentDetail(Request $request){
		$document = UserDocument::leftJoin('users', 'users.username', '=', 'user_documents.employee_id');

		if($request->get('documentNumber') != null){
			$document = $document->where('user_documents.document_number', '=', $request->get('documentNumber'));
		}

		$document = $document->select('user_documents.document_number', 'user_documents.employee_id', 'users.name', 'user_documents.valid_from', 'user_documents.valid_to', 'user_documents.category', 'user_documents.status')->get();

		$response = array(
			'status' => true,
			'document' => $document,
		);
		return Response::json($response);
	}

	public function fetchResumeUserDocument(){
		$resume =UserDocument::where('status', 'Active')
		->select('category', 'condition', db::raw('COUNT(id) AS quantity'))
		->groupBy('category', 'condition')
		->get();

		$response = array(
			'status' => true,
			'resume' => $resume
		);
		return Response::json($response);
	}

	public function fetchResumeUserDocumentDetail(Request $request){

		$detail =UserDocument::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'user_documents.employee_id')
		->where('user_documents.category', $request->get('category'))
		->where('user_documents.condition', $request->get('condition'))
		->where('user_documents.status', 'Active')
		->select(
			'user_documents.employee_id',
			'employee_syncs.name',
			'employee_syncs.position',
			'user_documents.category',
			'user_documents.document_number',
			'user_documents.valid_from',
			'user_documents.valid_to',
			'user_documents.status',
			'user_documents.condition',
			'user_documents.reminder',
			db::raw('DATEDIFF(user_documents.valid_to, NOW()) as diff')
		)
		->orderBy('valid_to', 'ASC')
		->get();

		$response = array(
			'status' => true,
			'detail' => $detail
		);
		return Response::json($response);
	}

	public function fetchUserDocumentRenew(Request $request){	
		try{
			
			$renew_document = UserDocument::where('employee_id', $request->get('employee_id'))
			->where('category', $request->get('category'))
			->first();

			// $delete_filename = $renew_document->attachment;
			// $delete_path = '/files/user_document/' . $delete_filename;
			// $delete_file_path = asset($delete_path);
			// File::delete($delete_path);

			$directory = 'files\user_document';
			$file = $request->file('file_datas');
			$original = $file->getClientOriginalName();
			$extension = pathinfo($original, PATHINFO_EXTENSION);
			$filename = $request->get('category').'-'.$request->get('employee_id').'.'.$extension;
			$file->move($directory,$filename);

			$renew_document->document_number = $request->get('documentNumber');
			$renew_document->valid_from = $request->get('validFrom');
			$renew_document->valid_to = $request->get('validTo');
			$renew_document->notification = 0;
			$renew_document->attachment = $filename;
			$renew_document->save();

			$safe = db::select("UPDATE user_documents
				SET `condition` = 'Safe'
				WHERE DATEDIFF(valid_to, NOW()) > reminder");

			$at_risk = db::select("UPDATE user_documents
				SET `condition` = 'At Risk'
				WHERE DATEDIFF(valid_to, NOW()) < reminder");

			$expired = db::select("UPDATE user_documents
				SET `condition` = 'Expired'
				WHERE now() > valid_to");

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

	public function fetchUserDocumentUpdate(Request $request){	
		try{		
			$renew_document = UserDocument::where('document_number', '=', $request->get('documentNumber'))->update([
				'status' => $request->get('status'),
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

	public function fetchUserDocumentCreate(Request $request){

		$document_number = $request->get('documentNumber');
		$employe_id = $request->get('employeId');
		$category = $request->get('category');
		$valid_from = date_create($request->get('validFrom'));
		$valid_to = date_create($request->get('validTo'));

		//define reminder
		$reminder = 0;
		if($category == 'PASPOR'){
			$reminder = 210;
		}else{
			$reminder = 90;
		}

		//define condition
		$condition = '';
		$diff = date_diff($valid_to, $valid_from);
		$diff = $diff->format('%a');
		if($diff > $reminder){
			$condition = 'Safe';
		}else{
			$condition = 'At Risk';	
		}

		try{

			$directory = 'files\user_document';
			$file = $request->file('file_datas');
			$original = $file->getClientOriginalName();
			$extension = pathinfo($original, PATHINFO_EXTENSION);
			$filename = $category.'-'.$employe_id.'.'.$extension;
			$file->move($directory,$filename);

			$document = new UserDocument([
				'category' => $category,
				'document_number' => $document_number,
				'employee_id' => $employe_id,
				'valid_from' => $valid_from,
				'valid_to' => $valid_to,
				'status' => 'Active',
				'condition' => $condition,
				'created_by' => Auth::id(),
				'reminder' => $reminder,
				'attachment' => $filename,
			]);
			$document->save();

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

	public function downloadUserDocument(Request $request){
		$name = $request->get('attachment');
		$path = '/files/user_document/' . $name;
		$file_path = asset($path);

		$response = array(
			'status' => true,
			'file_path' => $file_path,
		);
		return Response::json($response); 
	}




}