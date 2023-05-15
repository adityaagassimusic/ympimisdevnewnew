<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\CodeGenerator;
use App\ApprMasters;
use App\ApprApprovals;
use App\EmployeeSync;
use App\User;
use App\ApprSend;
use App\ApprLogs;
use App\ApprCattegorys;
use App\SakurentsuTrialRequest;
use App\SakurentsuTrialStatus;
use App\SakurentsuTrialRequestBom;
use App\ExtraOrderMaterial;
use App\FixedAssetSummary;
// use App\CodeGenerator;
use DataTables;
use Yajra\DataTables\Exception;
use Response;
use File;
use Storage;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use Dompdf\Dompdf;
use Dompdf\Options; 
use Dompdf\FontMetrics;
// use setasign\Fpdi\Fpdi;
// use setasign\Fpdi\PdfParser\StreamReader;
// use iio\libmergepdf\Merger;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;

class AdagioAutoController extends Controller
{
  public function __construct(){
    $this->middleware('auth');
  }

  public function IndexAdagio(Request $request)
  {   
    $emp = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('employee_id','department')
    ->first();

    $user = db::select("SELECT employee_id, name, position FROM employee_syncs where end_date is null and position in ('Foreman', 'Chief', 'Coordinator', 'Senior Coordinator', 'Manager', 'General Manager', 'Director', 'President Director', 'Staff', 'Junior Staff', 'Senior Staff', 'Deputy General Manager', 'Manager Japanese Specialist', 'Specialist') order by name ASC");

    $status = db::select('SELECT DISTINCT remark from appr_sends WHERE remark IN ("Close" , "Open")');

    $approvals = db::table('appr_masters')
    ->whereNull('deleted_at')
    ->select('category')
    ->orderBy('category', 'ASC')
    ->get();

    $dept = db::select("SELECT department_name from departments where department_shortname not in ('JPN')");
    $nama = db::select('SELECT DISTINCT nik from appr_sends');
    $pic = db::select('SELECT DISTINCT
      ( SELECT COALESCE ( b.approver_name ) FROM appr_approvals b WHERE b.request_id = a.no_transaction AND STATUS IS NULL ORDER BY id ASC LIMIT 1 ) AS tanggungan 
      FROM
      (
        SELECT DISTINCT
        no_transaction
        FROM
        appr_sends
        LEFT JOIN appr_approvals ON appr_sends.no_transaction = appr_approvals.request_id
        LEFT JOIN departments ON appr_sends.department = departments.department_name
        LEFT JOIN users ON appr_sends.created_by = users.id 
        WHERE
        appr_sends.remark = "Open"
        GROUP BY
        request_id, no_transaction
        ) AS a
      ORDER BY ( SELECT COALESCE ( b.approver_name ) FROM appr_approvals b WHERE b.request_id = a.no_transaction AND STATUS IS NULL ORDER BY id ASC LIMIT 1 ) asc');

    // $pic = db::select('select distinct no_transaction, approver_name from appr_sends 
    //   left join appr_approvals on appr_approvals.request_id = appr_sends.no_transaction
    //   where appr_sends.remark in ("Open", "Hold & Comment")
    //   and appr_approvals.`status` is null
    //   group by no_transaction
    //   order by (SELECT COALESCE ( b.approver_name ) FROM appr_approvals b WHERE b.request_id = appr_sends.no_transaction AND b.`status` IS NULL ORDER BY id ASC LIMIT 1 )');

    $role = User::where('username', Auth::user()->username)
    ->select('id', 'role_code')
    ->first();

    return view('auto_approve.index_adagio', array(
      'title'     => 'MIRAI Approval System', 
      'title_jp'  => 'MIRAI 承認システム',
      'user'      => $user,
      'approvals' => $approvals,
      'employee'       => $emp,
      'dept'      => $dept,
      'status'    => $status,
      'nama'      => $nama,
      'pic' => $pic,
      'role' => $role
    ));
  }

  public function DokumenApproval(Request $request)
  {   

   $emp = EmployeeSync::where('employee_id', Auth::user()->username)
   ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
   ->first();

   $users = User::where('username', Auth::user()->username)
   ->select('id')
   ->first();

 // $app_cat = db::select('SELECT detail FROM appr_categorys');


 // $user = db::select('SELECT employee_id, name, position FROM employee_syncs where end_date is null');

//  if ($emp->department == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department') {
//   $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Woodwind Instrument - Body Parts Process (WI-BPP) Department" and end_date is null');
// }
// else if ($emp->department == 'Production Control Department') {
//   $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Production Control Department" and end_date is null');
// }
// else if ($emp->department == 'Maintenance Department') {
//   $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Maintenance Department" and end_date is null');
// }
// else if ($emp->department == 'Production Engineering Department') {
//   $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Maintenance Department" and end_date is null');
// }
// else if ($emp->department == 'Woodwind Instrument - Assembly (WI-A) Department') {
//   $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Woodwind Instrument - Assembly (WI-A) Department" and end_date is null');
// }
// else if ($emp->department == 'Human Resources Department') {
//   $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Human Resources Department" and end_date is null');
// }
// else if ($emp->department == 'Quality Assurance Department') {
//   $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Quality Assurance Department" and end_date is null');
// }
// else if ($emp->department == 'Woodwind Instrument - Surface Treatment (WI-ST) Department') {
//   $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Woodwind Instrument - Surface Treatment (WI-ST) Department" and end_date is null');
// }
// else if ($emp->department == 'Procurement Department') {
//   $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Procurement Department" and end_date is null');
// }
// else if ($emp->department == 'Logistic Department') {
//   $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Logistic Department" and end_date is null');
// }
// else if ($emp->department == 'Educational Instrument (EI) Department') {
//   $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Educational Instrument (EI) Department" and end_date is null');
// }
// else if ($emp->department == 'Purchasing Control Department') {
//   $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Procurement Department" and end_date is null');
// }
// else{
//   $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where employee_id = "PI0109004" and end_date is null'); 
// }

//     // $gm = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "General Manager" and end_date is null');
// if ($emp->department == 'Procurement Department' || $emp->department == 'Production Control Department' || $emp->department == 'Logistic Department' || $emp->department == 'Purchasing Control Department') {
//   $gm = db::select('SELECT employee_id, name, position FROM employee_syncs where employee_id = "PI0109004"');
// }else if ($emp->department != 'Human Resources & General Affairs Division') {
//   $gm = db::select('SELECT employee_id, name, position FROM employee_syncs where employee_id = "PI1206001"');
// }


   if ($emp->department == 'Management Information System Department') {
    $approvals = db::table('appr_masters')
    ->whereNull('deleted_at')
    ->where('created_by', $users->id)
    ->select('department', 'judul', 'created_by')
    ->orderBy('judul', 'ASC')
    ->get();

  // $dpt = db::select('SELECT DISTINCT department FROM employee_syncs');
  }else{
    // $approvals = db::table('appr_masters')
    // ->select('department', 'judul')
    // ->where('department', '=', $emp->department)
    // ->orwhere('created_by', '=', $users->id)
    // ->orderBy('judul', 'ASC')
    // ->get();

    $approvals = db::table('appr_masters')
    ->select('department', 'judul', 'created_by')
    ->where('created_by', $users->id)
    ->orderBy('judul', 'ASC')
    ->get();

  // $dpt = db::select('SELECT DISTINCT department FROM employee_syncs WHERE department = "'.$emp->department.'"');    
  }


  // $categorys = db::table('appr_categorys')
  // ->select('detail')
  // ->orderBy('detail', 'ASC')
  // ->get();


  return view('auto_approve.create_dokumen', array(
    'title' => 'MIRAI Approval System', 
    'title_jp' => 'ミューテーション1部門の監視と制御',

  // 'user'      => $user,
    'approvals' => $approvals,
  // 'categorys' => $categorys,
    'employee'  => $emp,
    'users' => $users
  // 'dpt' => $dpt
  // 'manager'   => $manager,
  // 'gm'        => $gm,
  // 'app_cat'   => $app_cat
  ));
}

public function AdagioMonitoring(Request $request)
{   
  $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
  ->select('employee_id', 'department', 'position')
  ->first();

  if ($emp_dept->department == 'Management Information System Department') {
    $approvals = db::table('appr_masters')
    ->whereNull('deleted_at')
    ->select('category')
    ->orderBy('category', 'ASC')
    ->get();
  }else{
    $approvals = db::table('appr_masters')
    ->whereNull('deleted_at')
    ->select('category')
    ->where('created_by', '=', Auth::user()->id)
    ->orderBy('category', 'ASC')
    ->get();
  }



  return view('auto_approve.monitoring',  
    array(
      'title' => 'MIRAI Approval System', 
      'title_jp' => 'MIRAI 承認システム',
      'approvals' => $approvals
    )
  )->with('page', 'File Approval');
}

public function AdagioFetchMonitoring(Request $request)
{   
  $master_app = $request->get('master');
  $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
  ->select('employee_id', 'department', 'position')
  ->first();

  if ($emp_dept->department == 'Management Information System Department') {
    if ($master_app == "") {
      $resumes = DB::select('SELECT * from appr_sends');
      $jml_app = DB::select('SELECT category, COUNT(`category`) as jumlah from appr_masters group by `category` order by jumlah desc limit 1');
    }else{
      $resumes = DB::select('SELECT * from appr_sends where judul = "'.$master_app.'" and remark != "0"');
      $jml_app = DB::select('SELECT category, COUNT(`category`) as jumlah from appr_masters where category = "'.$master_app.'" group by `category`');
    }
  }else{
    if ($master_app == "") {
      $resumes = DB::select('SELECT * from appr_sends where nik like "%'.$emp_dept->employee_id.'%"');
      $jml_app = DB::select('SELECT category, COUNT(`category`) as jumlah from appr_masters group by `category` order by jumlah desc limit 1');
    }else{
      $resumes = DB::select('SELECT * from appr_sends where judul = "'.$master_app.'" and remark != "0" and nik like "%'.$emp_dept->employee_id.'%"');
      $jml_app = DB::select('SELECT category, COUNT(`category`) as jumlah from appr_masters where category = "'.$master_app.'" group by `category`');
    }
  }


  $response = array(
    'status' => true,
    'resumes' => $resumes,
    'master_app' => $master_app,
    'jml_app' => $jml_app
  );
  return Response::json($response);
}


public function IndexAdagioIndexHome(Request $request){

  $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
  ->select('employee_id', 'department')
  ->first();

  $user    = db::select('SELECT employee_id, name, position FROM employee_syncs where end_date is null');
            // $manager = db::selecr('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and end_date is null');

  return view('auto_approve.create_master_app', array(
    'title' => 'MIRAI File Approval', 
    'title_jp' => '??',
    'user' => $user
          // 'manager' => $manager
  ));

}

public function CreateAdagioIndexHome(Request $request)
{
  $id = Auth::id();
  $lop = $request->get('lop');
  $lop_cc = $request->get('lop_cc');
  // dd($lop);
  try{
    for ($i=1; $i <= $lop ; $i++) {
    // $z = $i - 5;
      $description = "description".$i;
      $header = "header".$i;
      $approve = "Approval_".$i;
      $data = new ApprMasters([
        'department' => $request->get('app_dpt'),
        'category' => $request->get('app_ctg'),
        'judul' => $request->get('judul'),
        'jd_japan' => $request->get('jd_japan'),
        'user' => $request->get($description).'/'.$request->get($header),
        'urutan' => $approve,
        'created_by' => $id
      ]);
      $data->save();
      // $master = db::select('select distinct department, judul from appr_masters where department = "'.$request->get('app_dpt').'" and judul = "'.$request->get('judul').'"');
      // if (count($master) > 0) {
      //   return back()->with('error', 'Category Approval Sudah Ada.')->with('page', 'Category Approval');
      // }else{
      // }
    }

    for ($i=1; $i <= $lop_cc ; $i++) {
      $description_cc = "description_cc".$i;
      // $header_cc = "Known by/(承知)";
      $approve = "CC_".$i;
      $data = new ApprMasters([
        'department' => $request->get('app_dpt'),
        'category' => $request->get('app_ctg'),
        'judul' => $request->get('judul'),
        'jd_japan' => $request->get('jd_japan'),
        'user' => $request->get($description_cc).'/Known by/(承知)',
        'urutan' => $approve,
        'created_by' => $id
      ]);
      $data->save();
    }
    return redirect('/index/mirai/approval')->with('status', 'Category Approval Sukses Dibuat.')->with('page', 'Category Approval');
  }
  catch (QueryException $e){
    return redirect('/index/mirai/approval')->with('error', $e->getMessage())->with('page', 'Category Approval');
  }
}

public function DataAdagioaHome(Request $request){
 $master = DB::SELECT("
   SELECT DISTINCT appr_masters.category, appr_masters.created_by, appr_masters.created_at, users.`name`
   FROM `appr_masters` LEFT JOIN users ON users.id = appr_masters.created_by WHERE appr_masters.deleted_at is null");	
 return DataTables::of($master)
 ->addColumn('action', function($master){
  return'
  <a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" onClick="editMaster(id,\''.$master->category.'\')" id="' . $master->category . '"><i class="fa fa-edit">Edit</i></a>
  <a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-danger" onClick="deleteCategory(id,\''.$master->category.'\')" id="' . $master->category . '"><i class="fa fa-trash">Hapus</i></a>';
})
 ->rawColumns(['action' => 'action'])
 ->make(true);
}

public function DataAdagioaEdit(Request $request)
{
  $masters = ApprMasters::where('category', '=', $request->get('category'))->get();

  $response = array(
    'status' => true,
    'masters' => $masters
  );
  return Response::json($response);
}

public function AdagioEmail(Request $request, $id)
{     
  $detail = ApprSend::select('appr_sends.id', 'appr_sends.no_transaction', 'appr_sends.nik', 'appr_sends.department', 'appr_sends.description', 'appr_sends.date', 'appr_sends.summary', 'appr_sends.file', 'appr_sends.approve1', 'appr_sends.date1', 'appr_sends.approve2', 'appr_sends.date2', 'appr_sends.approve3', 'appr_sends.date3', 'appr_sends.approve4', 'appr_sends.date4', 'appr_sends.approve5', 'appr_sends.date5', 'appr_sends.approve6', 'appr_sends.date6', 'appr_sends.approve7', 'appr_sends.date7','appr_sends.approve8', 'appr_sends.date8', 'appr_sends.approve9', 'appr_sends.date9', 'appr_sends.approve10', 'appr_sends.date10', 'remark')
  ->where('appr_sends.id', '=', $id)
  ->first();

         // $data = array(
         //        'detail' => $detail
         //    );

  return view('auto_approve.send_email', array(
    'title' => 'MIRAI Approval System', 
    'title_jp' => 'MIRAI 承認システム',
    'detail' => $detail
  ));
         //    $detail = ApprSend::find($id);

         // try{
         //    $resumes = ApprSend::select(
         //    'appr_sends.id', 'appr_sends.no_transaction', 'appr_sends.nik', 'appr_sends.department', 'appr_sends.description', 'appr_sends.date', 'appr_sends.summary', 'appr_sends.file', 'appr_sends.approve1', 'appr_sends.date1', 'appr_sends.approve2', 'appr_sends.date2', 'appr_sends.approve3', 'appr_sends.date3', 'appr_sends.approve4', 'appr_sends.date4', 'appr_sends.approve5', 'appr_sends.date5', 'appr_sends.approve6', 'appr_sends.date6', 'appr_sends.approve7', 'appr_sends.date7','appr_sends.approve8', 'appr_sends.date8', 'appr_sends.approve9', 'appr_sends.date9', 'appr_sends.approve10', 'appr_sends.date10', 'remark')
         //    ->where('appr_sends.id', '=', $id)
         //    ->get();
         //    $data = array(
         //        'resumes' => $resumes
         //    );

         //    // $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id = 'PI0603019' or employee_id = 'PI1404001'";  
         //    // $mailtoo = DB::select($mails);

         //    $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = ".$detail->id;

         //    $detail = db::select($isimail);
         //    // Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com','mokhamad.khamdan.khabibi@music.yamaha.com'])->send(new SendEmail($detail, 'done_mutasi_ant'));
         //    // return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
         //    }
         //    catch (QueryException $e){
         //    return back()->with('error', 'Error')->with('page', 'Mutasi Error');
         //        // dd($e);
         //    }
}

public function DataAdagioaUpdate(Request $request)
{
  $id = Auth::id();
  $lop = $request->get('lop2');
  try{
    ApprMasters::where('category', '=', $request->get('category2'))->forceDelete();

    for ($i=1; $i <= $lop ; $i++) {
      $description = "description".$i;
      $approve = "Approval_".$i;
            // $duration = "duration".$i; 
      $data = new ApprMasters([
              // 'code_app' => $code_generator->prefix . $number1,
        'category' => $request->get('category2'),
        'user' => $request->get($description),
        'urutan' => $approve,
        'created_by' => $id
      ]);
      $data->save();
    }


    return redirect('/adagio/home/index')->with('status', 'Crete daily report success')->with('page', 'Daily Report');
  }
  catch (QueryException $e){
    return redirect('/adagio/home/index')->with('error', $e->getMessage())->with('page', 'Daily Report');
  }

}

public function DataAdagioaDelete(Request $request)
{
  try{
    $master = ApprMasters::where('id','=' ,$request->get('id'))
    ->delete();
  }catch (QueryException $e){
    return redirect('/adagio/home/index')->with('error', $e->getMessage())->with('page', 'Daily Report');
  }

}

public function DataAdagioaDeleteCategory(Request $request)
{
  try{
    $master = ApprMasters::where('category','=' ,$request->get('category'))
    ->delete();
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
    'message' => 'File Approval Berhasil Di Hapus',
  );
  return Response::json($response);

}

public function AdagioIndexSendFile(Request $request){

  $emp = EmployeeSync::where('employee_id', Auth::user()->username)
  ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
  ->first();


  $user    = db::select('SELECT employee_id, name, position FROM employee_syncs where end_date is null');
  if ($emp->department == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department') {
    $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Woodwind Instrument - Body Parts Process (WI-BPP) Department" and end_date is null');
  }
  else if ($emp->department == 'Production Control Department') {
    $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Production Control Department" and end_date is null');
  }
  else if ($emp->department == 'Maintenance Department') {
    $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Maintenance Department" and end_date is null');
  }
  else if ($emp->department == 'Production Engineering Department') {
    $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Maintenance Department" and end_date is null');
  }
  else if ($emp->department == 'Woodwind Instrument - Assembly (WI-A) Department') {
    $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Woodwind Instrument - Assembly (WI-A) Department" and end_date is null');
  }
  else if ($emp->department == 'Human Resources Department') {
    $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Human Resources Department" and end_date is null');
  }
  else if ($emp->department == 'Quality Assurance Department') {
    $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Quality Assurance Department" and end_date is null');
  }
  else if ($emp->department == 'Woodwind Instrument - Surface Treatment (WI-ST) Department') {
    $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Woodwind Instrument - Surface Treatment (WI-ST) Department" and end_date is null');
  }
  else if ($emp->department == 'Procurement Department') {
    $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Procurement Department" and end_date is null');
  }
  else if ($emp->department == 'Logistic Department') {
    $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Logistic Department" and end_date is null');
  }
  else if ($emp->department == 'Educational Instrument (EI) Department') {
    $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Educational Instrument (EI) Department" and end_date is null');
  }
  else if ($emp->department == 'Purchasing Control Department') {
    $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "Manager" and department = "Procurement Department" and end_date is null');
  }
  else{
    $manager = db::select('SELECT employee_id, name, position FROM employee_syncs where employee_id = "PI0109004" and end_date is null'); 
  }

    // $gm = db::select('SELECT employee_id, name, position FROM employee_syncs where position = "General Manager" and end_date is null');
  if ($emp->department == 'Procurement Department' || $emp->department == 'Production Control Department' || $emp->department == 'Logistic Department' || $emp->department == 'Purchasing Control Department') {
    $gm = db::select('SELECT employee_id, name, position FROM employee_syncs where employee_id = "PI0109004"');
  }else if ($emp->department != 'Human Resources & General Affairs Division') {
    $gm = db::select('SELECT employee_id, name, position FROM employee_syncs where employee_id = "PI1206001"');
  }


  if ($emp->department == 'Management Information System Department') {
    $approvals = db::table('appr_masters')
    ->whereNull('deleted_at')
    ->select('category')
    ->orderBy('category', 'ASC')
    ->get();
  }else{
    $approvals = db::table('appr_masters')
    ->whereNull('deleted_at')
    ->select('category')
    ->where('created_by', '=', Auth::user()->id)
    ->orderBy('category', 'ASC')
    ->get();    
  }


  $categorys = db::table('appr_categorys')
  ->select('detail')
  ->orderBy('detail', 'ASC')
  ->get();





  return view('auto_approve.send_file', array(
    'title' => 'MIRAI Approval System', 
    'title_jp' => '??',

    'user' => $user,
    'approvals' => $approvals,
    'categorys' => $categorys,
    'employee' => $emp,
    'manager' => $manager,
    'gm' => $gm
  ));

}

public function AdagioIndexResume(Request $request){

  return view('auto_approve.resume', array(
    'title' => 'MIRAI Approval System', 
    'title_jp' => '??'
  ));

}

public function AdagioDataResume(Request $request){
  try {
    $emp = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
    ->first();


    $user = User::where('username', Auth::user()->username)
    ->select('id', 'username', 'role_code')
    ->first();

    $select = $request->get('select');
    $dept = $request->get('dpt');
    $stt = $request->get('stt');
    $nm = $request->get('nm');
    $date = $request->get('date_to');
    $pic_progress = $request->get('pic_progress');

    $dep = '';
    if($dept != ''){
      $dep = "and department = '".$dept."'";
    }else{
      $dep = "";
    }

    $st = '';
    if($stt != ''){
      $st = "and remark = '".$stt."'";
    }else{
      $st = "";
    }

    $n = '';
    if($nm != ''){
      $n = "and nik = '".$nm."'";
    }else{
      $n = "";
    }

    $dat = '';
    if ($date != null) {
      $dat = "and date(created_at) = '".date('Y-m-d', strtotime($date))."'";
    }
    else{
      $dat = "";
    }

    $app = '';
    if ($pic_progress != null) {
      $apprr = json_encode($pic_progress);
      $appr = str_replace(array("[","]"),array("(",")"),$apprr);

      $app = ' in ('.$appr.')';
    } else {
      $app = '';
    }
    

    $filter2 = $dep . $st . $n . $dat . $app;
    $filter = $dep . $st . $n . $dat;


    $nik = [];

    $role = Auth::user()->role_code;
    $sl = '';
    $resumes = '';

    $test_1 = Auth::user()->id;
    $test_2 = strtoupper(Auth::user()->username);

    if($select == 'AllResume'){
      if($pic_progress == null){
        $resumes = db::select("SELECT DISTINCT
          a.id,
          a.no_transaction,
          a.date,
          a.no_dokumen,
          a.department_shortname,
          a.judul AS description,
          a.nik,
          a.username,
          a.created_by,
          a.remark,
          ( SELECT GROUP_CONCAT( b.approver_id ) FROM appr_approvals b WHERE b.request_id = a.no_transaction) AS approver_id,
          ( SELECT GROUP_CONCAT( COALESCE ( b.approved_at, '' ) ) FROM appr_approvals b WHERE b.request_id = a.no_transaction) AS approved_at,
          ( SELECT GROUP_CONCAT( COALESCE ( b.approver_name, '' ) ) FROM appr_approvals b WHERE b.request_id = a.no_transaction) AS `name`,
          ( SELECT GROUP_CONCAT( COALESCE ( b.`status`, '' ) ) FROM appr_approvals b WHERE b.request_id = a.no_transaction) AS `status`,
          (
            SELECT COALESCE
            ( b.approver_name, '' ) 
            FROM
            appr_approvals b 
            WHERE
            b.request_id = a.no_transaction 
            AND STATUS IS NULL 
            ORDER BY
            id ASC 
            LIMIT 1 
            ) AS tanggungan,
          a.created_at,
          a.summary
          FROM
          (
            SELECT DISTINCT
            no_transaction,
            appr_sends.id,
            `date`,
            no_dokumen,
            department_shortname,
            judul,
            users.`username`,
            approved_at,
            approver_name,
            nik,
            appr_sends.remark,
            appr_sends.created_by,
            appr_sends.created_at,
            appr_sends.summary
            FROM
            appr_sends
            LEFT JOIN appr_approvals ON appr_sends.no_transaction = appr_approvals.request_id
            LEFT JOIN departments ON appr_sends.department = departments.department_name
            LEFT JOIN users ON appr_sends.created_by = users.id 
            WHERE
            appr_sends.remark = 'Open' 
            OR appr_sends.remark = 'Belum Kirim Email' 
            OR appr_sends.remark like '%Hold & Comment%' UNION ALL
            SELECT
            request_id,
            appr_sends.id,
            `date`,
            no_dokumen,
            department_shortname,
            judul,
            users.username,
            approved_at,
            approver_name,
            nik,
            appr_sends.remark,
            appr_sends.created_by,
            appr_sends.created_at,
            appr_sends.summary
            FROM
            appr_approvals
            LEFT JOIN appr_sends ON appr_approvals.request_id = appr_sends.no_transaction
            LEFT JOIN departments ON appr_sends.department = departments.department_name
            LEFT JOIN users ON appr_sends.created_by = users.id 
            WHERE
            `status` IS NULL 
            GROUP BY
            request_id,
            id,
            `date`,
            no_dokumen,
            department_shortname,
            judul,
            approved_at,
            approver_name,
            nik,
            username,
            created_by,
            remark,
            created_at,
            summary
            ) AS a 
          WHERE
          (
            a.created_by = '".$test_1."' 
            OR ( SELECT COALESCE ( b.approver_id ) FROM appr_approvals b WHERE b.request_id = a.no_transaction AND b.`status` IS NULL LIMIT 1 ) = '".$test_2."' 
            ) 
            AND ( a.remark = 'Open' OR a.remark = 'Belum Kirim Email' OR a.remark like '%Hold & Comment%' ) 
            GROUP BY
            a.id,
            a.no_transaction,
            a.date,
            a.no_dokumen,
            a.department_shortname,
            a.judul,
            a.nik,
            a.username,
            a.created_by,
            a.remark,
            a.created_at,
            a.summary");
        }else{
          $resumes = db::select("SELECT DISTINCT
            a.id,
            a.no_transaction,
            a.date,
            a.no_dokumen,
            a.department_shortname,
            a.judul AS description,
            a.nik,
            a.username,
            a.created_by,
            a.remark,
            ( SELECT GROUP_CONCAT( b.approver_id ) FROM appr_approvals b WHERE b.request_id = a.no_transaction ) AS approver_id,
            ( SELECT GROUP_CONCAT( COALESCE ( b.approved_at, '' ) ) FROM appr_approvals b WHERE b.request_id = a.no_transaction ) AS approved_at,
            ( SELECT GROUP_CONCAT( COALESCE ( b.approver_name, '' ) ) FROM appr_approvals b WHERE b.request_id = a.no_transaction ) AS `name`,
            ( SELECT GROUP_CONCAT( COALESCE ( b.`status`, '' ) ) FROM appr_approvals b WHERE b.request_id = a.no_transaction ) AS `status`,
            (
              SELECT COALESCE
              ( b.approver_name, '' ) 
              FROM
              appr_approvals b 
              WHERE
              b.request_id = a.no_transaction 
              AND STATUS IS NULL 
              ORDER BY
              id ASC 
              LIMIT 1 
              ) AS tanggungan,
            a.created_at,
            a.summary
            FROM
            (
              SELECT DISTINCT
              no_transaction,
              appr_sends.id,
              `date`,
              no_dokumen,
              department_shortname,
              judul,
              users.`username`,
              approved_at,
              approver_name,
              nik,
              appr_sends.remark,
              appr_sends.created_by,
              appr_sends.created_at,
              appr_sends.summary
              FROM
              appr_sends
              LEFT JOIN appr_approvals ON appr_sends.no_transaction = appr_approvals.request_id
              LEFT JOIN departments ON appr_sends.department = departments.department_name
              LEFT JOIN users ON appr_sends.created_by = users.id 
              WHERE
              appr_sends.remark = 'Open' 
              OR appr_sends.remark = 'Belum Kirim Email' 
              OR appr_sends.remark = 'Hold & Comment' UNION ALL
              SELECT
              request_id,
              appr_sends.id,
              `date`,
              no_dokumen,
              department_shortname,
              judul,
              users.username,
              approved_at,
              approver_name,
              nik,
              appr_sends.remark,
              appr_sends.created_by,
              appr_sends.created_at,
              appr_sends.summary 
              FROM
              appr_approvals
              LEFT JOIN appr_sends ON appr_approvals.request_id = appr_sends.no_transaction
              LEFT JOIN departments ON appr_sends.department = departments.department_name
              LEFT JOIN users ON appr_sends.created_by = users.id 
              WHERE
              `status` IS NULL 
              GROUP BY
              request_id,
              id,
              `date`,
              no_dokumen,
              department_shortname,
              judul,
              approved_at,
              approver_name,
              nik,
              username,
              created_by,
              remark,
              created_at,
              summary
              ) AS a 
            WHERE
            (
              a.created_by = '".$test_1."' 
              OR ( SELECT COALESCE ( b.approver_name ) FROM appr_approvals b WHERE b.request_id = a.no_transaction AND b.`status` IS NULL LIMIT 1 ) = '".$pic_progress."' 
              ) 
              AND ( a.remark = 'Open' OR a.remark = 'Belum Kirim Email' OR a.remark = 'Hold & Comment' ) 
              GROUP BY
              a.id,
              a.no_transaction,
              a.date,
              a.no_dokumen,
              a.department_shortname,
              a.judul,
              a.nik,
              a.username,
              a.created_by,
              a.remark,
              a.created_at,
              a.summary");
          }
        }

        else if($select == 'show_all'){
          $resumes = db::select("SELECT DISTINCT
            a.id,
            a.no_transaction,
            a.date,
            a.no_dokumen,
            a.department_shortname,
            a.judul AS description,
            a.nik,
            a.username,
            a.created_by,
            a.remark,
            ( SELECT GROUP_CONCAT( b.approver_id ) FROM appr_approvals b WHERE b.request_id = a.no_transaction ) AS approver_id,
            ( SELECT GROUP_CONCAT( COALESCE ( b.approved_at, '' ) ) FROM appr_approvals b WHERE b.request_id = a.no_transaction ) AS approved_at,
            ( SELECT GROUP_CONCAT( COALESCE ( b.approver_name, '' ) ) FROM appr_approvals b WHERE b.request_id = a.no_transaction ) AS `name`,
            ( SELECT GROUP_CONCAT( COALESCE ( b.`status`, '' ) ) FROM appr_approvals b WHERE b.request_id = a.no_transaction ) AS `status`,
            (
              SELECT COALESCE
              ( b.approver_name, '' ) 
              FROM
              appr_approvals b 
              WHERE
              b.request_id = a.no_transaction 
              AND STATUS IS NULL 
              ORDER BY
              id ASC 
              LIMIT 1 
              ) AS tanggungan,
            a.created_at,
            a.summary
            FROM
            (
              SELECT DISTINCT
              no_transaction,
              appr_sends.id,
              `date`,
              no_dokumen,
              department_shortname,
              judul,
              users.`username`,
              approved_at,
              approver_name,
              nik,
              appr_sends.remark,
              appr_sends.created_by,
              appr_sends.created_at,
              appr_sends.summary
              FROM
              appr_sends
              LEFT JOIN appr_approvals ON appr_sends.no_transaction = appr_approvals.request_id
              LEFT JOIN departments ON appr_sends.department = departments.department_name
              LEFT JOIN users ON appr_sends.created_by = users.id 
              WHERE
              appr_sends.remark = 'Open' 
              OR appr_sends.remark = 'Belum Kirim Email' UNION ALL
              SELECT
              request_id,
              appr_sends.id,
              `date`,
              no_dokumen,
              department_shortname,
              judul,
              users.username,
              approved_at,
              approver_name,
              nik,
              appr_sends.remark,
              appr_sends.created_by,
              appr_sends.created_at,
              appr_sends.summary
              FROM
              appr_approvals
              LEFT JOIN appr_sends ON appr_approvals.request_id = appr_sends.no_transaction
              LEFT JOIN departments ON appr_sends.department = departments.department_name
              LEFT JOIN users ON appr_sends.created_by = users.id 
              WHERE
              `status` IS NULL 
              GROUP BY
              request_id,
              id,
              `date`,
              no_dokumen,
              department_shortname,
              judul,
              approved_at,
              approver_name,
              nik,
              username,
              created_by,
              remark,
              created_at,
              summary
              ) AS a 
            WHERE
            a.remark = 'Open' 
            OR a.remark = 'Belum Kirim Email' 
            OR a.remark = 'Hold & Comment' 
            GROUP BY
            a.id,
            a.no_transaction,
            a.date,
            a.no_dokumen,
            a.department_shortname,
            a.judul,
            a.nik,
            a.username,
            a.created_by,
            a.remark,
            a.created_at,
            a.summary");
        }


        else if($select == 'Completed'){
          $dari_bulan = $request->get('dari_bulan');
          $sampai_bulan = $request->get('sampai_bulan');

      // if (($test_2 == 'PI2101044')) {
      //   $resumes = db::select("SELECT DISTINCT
      //     a.id,
      //     a.no_transaction,
      //     a.date,
      //     a.no_dokumen,
      //     a.department_shortname,
      //     a.judul AS description,
      //     a.nik,
      //     a.username,
      //     a.created_by,
      //     ( SELECT GROUP_CONCAT( b.approver_id ) FROM appr_approvals b WHERE b.request_id = a.no_transaction ) AS approver_id,
      //     ( SELECT GROUP_CONCAT( COALESCE ( b.approved_at, '' ) ) FROM appr_approvals b WHERE b.request_id = a.no_transaction ) AS approved_at,
      //     ( SELECT GROUP_CONCAT( COALESCE ( b.approver_name, '' ) ) FROM appr_approvals b WHERE b.request_id = a.no_transaction ) AS `name`,
      //     ( SELECT GROUP_CONCAT( COALESCE ( b.`status`, '' ) ) FROM appr_approvals b WHERE b.request_id = a.no_transaction ) AS `status`,
      //     a.created_at,
      //     a.summary,
      //     a.remark
      //     FROM
      //     (
      //     SELECT DISTINCT
      //     no_transaction,
      //     appr_sends.id,
      //     `date`,
      //     no_dokumen,
      //     department_shortname,
      //     judul,
      //     users.`username`,
      //     approved_at,
      //     approver_name,
      //     nik,
      //     appr_sends.created_by,
      //     appr_sends.created_at,
      //     appr_sends.summary,
      //     appr_sends.remark
      //     FROM
      //     appr_sends
      //     LEFT JOIN appr_approvals ON appr_sends.no_transaction = appr_approvals.request_id
      //     LEFT JOIN departments ON appr_sends.department = departments.department_name
      //     LEFT JOIN users ON appr_sends.created_by = users.id 
      //     WHERE
      //     appr_sends.remark in ('Close', 'Rejected') 
      //     GROUP BY
      //     no_transaction,
      //     id,
      //     `date`,
      //     no_dokumen,
      //     department_shortname,
      //     judul,
      //     approved_at,
      //     approver_name,
      //     nik,
      //     username,
      //     created_by,
      //     created_at,
      //     appr_sends.summary,
      //     appr_sends.remark
      //     ) AS a 
      //     GROUP BY
      //     a.id,
      //     a.no_transaction,
      //     a.date,
      //     a.no_dokumen,
      //     a.department_shortname,
      //     a.judul,
      //     a.nik,
      //     a.username,
      //     a.created_by,
      //     a.created_at,
      //     a.summary,
      //     a.remark");
      // }
      // else{
          $resumes = db::select("SELECT DISTINCT
            a.id,
            a.no_transaction,
            a.date,
            a.no_dokumen,
            a.department_shortname,
            a.judul AS description,
            a.nik,
            a.username,
            a.created_by,
            ( SELECT GROUP_CONCAT( b.approver_id ) FROM appr_approvals b WHERE b.request_id = a.no_transaction ) AS approver_id,
            ( SELECT GROUP_CONCAT( COALESCE ( b.approved_at, '' ) ) FROM appr_approvals b WHERE b.request_id = a.no_transaction ) AS approved_at,
            ( SELECT GROUP_CONCAT( COALESCE ( b.approver_name, '' ) ) FROM appr_approvals b WHERE b.request_id = a.no_transaction ) AS `name`,
            ( SELECT GROUP_CONCAT( COALESCE ( b.`status`, '' ) ) FROM appr_approvals b WHERE b.request_id = a.no_transaction ) AS `status`,
            a.created_at,
            a.remark,
            a.summary
            FROM
            (
              SELECT DISTINCT
              no_transaction,
              appr_sends.id,
              `date`,
              no_dokumen,
              department_shortname,
              judul,
              users.`username`,
              approved_at,
              approver_name,
              nik,
              appr_sends.created_by,
              appr_sends.created_at,
              appr_sends.remark,
              appr_sends.summary
              FROM
              appr_sends
              LEFT JOIN appr_approvals ON appr_sends.no_transaction = appr_approvals.request_id
              LEFT JOIN departments ON appr_sends.department = departments.department_name
              LEFT JOIN users ON appr_sends.created_by = users.id 
              WHERE
              appr_sends.remark in ('Close', 'Rejected')
              and DATE_FORMAT(`date`, '%Y-%m') >= '".$dari_bulan."'
              and DATE_FORMAT(`date`, '%Y-%m') <= '".$sampai_bulan."'
              and ( appr_sends.created_by = '".$test_1."'
              OR ( SELECT GROUP_CONCAT( b.approver_id ) FROM appr_approvals b WHERE b.request_id = appr_sends.no_transaction ) LIKE '%".$test_2."%' )
              GROUP BY
              no_transaction,
              id,
              `date`,
              no_dokumen,
              department_shortname,
              judul,
              approved_at,
              approver_name,
              nik,
              username,
              created_by,
              created_at,
              remark,
              summary
              ) AS a 
              WHERE
              a.created_by = '".$test_1."' 
              OR ( SELECT GROUP_CONCAT( b.approver_id ) FROM appr_approvals b WHERE b.request_id = a.no_transaction ) LIKE '%".$test_2."%' 
              GROUP BY
              a.id,
              a.no_transaction,
              a.date,
              a.no_dokumen,
              a.department_shortname,
              a.judul,
              a.nik,
              a.username,
              a.created_by,
              a.created_at,
              a.remark,
              a.summary
              ORDER BY
              a.remark DESC");
      // }
          }
          

      //grafik
          $survey = DB::SELECT("
            SELECT
            a.department,
            department_shortname,
            SUM( a.count_sudah ) AS sudah,
            SUM( a.count_belum ) AS belum
            FROM
            (
              SELECT
              sum( CASE WHEN appr_sends.remark = 'Close' THEN 1 ELSE 0 END ) AS count_sudah,
              0 AS count_belum,
              COALESCE ( department, '' ) AS department 
              FROM
              ympimis.appr_sends 
              WHERE ympimis.appr_sends.created_at is not null ".$filter."
              GROUP BY
              department UNION ALL
              SELECT
              0 AS count_sudah,
              sum( CASE WHEN appr_sends.remark = 'Open' THEN 1 ELSE 0 END ) AS count_belum,
              COALESCE ( department, '' ) AS department 
              FROM
              ympimis.appr_sends 
              WHERE ympimis.appr_sends.created_at is not null ".$filter."
              GROUP BY
              department 
              ) a
            LEFT JOIN departments ON a.department = departments.department_name 
            WHERE
            a.department != '' 
            GROUP BY
            a.department,
            departments.department_shortname
            ");

          $response = array(
            'status' => true,
            'resumes' => $resumes,
            'survey' => $survey,
            'emp' => $emp,
            'user' => $user,
            'role' => $role
          );
          return Response::json($response);
        } 
        catch (\Exception $e) {
          $response = array(
            'status' => false,
            'message' => $e->getMessage()
          );
          return Response::json($response);
        }
      }

      public function CekAdagioNoFile(Request $request)
      {
        try {
      // $adagio = ApprSend::where('no_transaction',$request->get('no'))->first();
      // if (count($adagio) > 0) {
      //   $no_trans = $adagio->no_transaction;
      //   $no = substr($no_trans, 2);
      //   $no_doc = ($no+1);
      //   $response = array(
      //     'status' => false,
      //     'message' => 'Sudah Ada',
      //     'no_doc' => $no_doc
      //   );
      //   return Response::json($response);
      // }else{
      //   $response = array(
      //     'status' => true,
      //     'message' => 'No Approval OK'
      //   );
      //   return Response::json($response); 
      // }
          $tahun = date("y");
          $bulan = date("m");
          $prefix_now = $tahun.$bulan;

          $dept = $request->get('dept');

          if ($dept == "Management Information System Department")
          {
            $dept = "IT";
          }
          else if ($dept == "Accounting Department")
          {
            $dept = "AC";
          }
          else if ($dept == "Woodwind Instrument - Assembly (WI-A) Department")
          {
            $dept = "AS";
          }
          else if ($dept == "Educational Instrument (EI) Department")
          {
            $dept = "EI";
          }
          else if ($dept == "General Affairs Department")
          {
            $dept = "GA";
          }
          else if ($dept == "Human Resources Department")
          {
            $dept = "HR";
          }
          else if ($dept == "Logistic Department")
          {
            $dept = "LG";
          }
          else if ($dept == "Maintenance Department")
          {
            $dept = "PM";
          }
          else if ($dept == "Woodwind Instrument - Key Parts Process (WI-KPP) Department")
          {
            $dept = "KP";
          }
          else if ($dept == "Woodwind Instrument - Body Parts Process (WI-BPP) Department")
          {
            $dept = "BP";
          }
          else if ($dept == "Procurement Department" || $dept == "Purchasing Control Department")
          {
            $dept = "PH";
          }
          else if ($dept == "Production Control Department")
          {
            $dept = "PC";
          }
          else if ($dept == "Production Engineering Department")
          {
            $dept = "PE";
          }
          else if ($dept == "Quality Assurance Department")
          {
            $dept = "QC";
          }
          else if ($dept == "Woodwind Instrument - Welding Process (WI-WP) Department")
          {
            $dept = "WP";
          }
          else if ($dept == "Woodwind Instrument - Surface Treatment (WI-ST) Department"){
            $dept = "ST";
          }
          else if ($dept == "General Process Control Department"){
            $dept = "GPC";
          }

          $code_generator = CodeGenerator::where('note','=','appr')->first();
          if ($prefix_now != $code_generator->prefix){
            $code_generator->prefix = $prefix_now;
            $code_generator->index = '0';
            $code_generator->save();
          }

          $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
          $no_appr = $dept . $code_generator->prefix . $numbers;
          $code_generator->index = $code_generator->index+1;
          $code_generator->save();

      // var_dump($appr);
      // die();
          $response = array(
            'status' => true,
            'message' => 'No Approval OK',
            'no_appr' => $no_appr
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

      public function AdagioDataReport(Request $request, $id){
        $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'department')
        ->first();

        $detail = ApprSend::select('appr_sends.id', 'appr_sends.aplication', 'appr_sends.no_transaction', 'appr_sends.category', 'appr_sends.nik', 'appr_sends.department', 'appr_sends.description', 'appr_sends.date', 'appr_sends.summary', 'appr_sends.file', 'appr_sends.approve1', 'appr_sends.date1', 'appr_sends.approve2', 'appr_sends.date2', 'appr_sends.approve3', 'appr_sends.date3', 'appr_sends.approve4', 'appr_sends.date4', 'appr_sends.approve5', 'appr_sends.date5', 'appr_sends.approve6', 'appr_sends.date6', 'appr_sends.approve7', 'appr_sends.date7','appr_sends.approve8', 'appr_sends.date8', 'appr_sends.approve9', 'appr_sends.date9', 'appr_sends.approve10', 'appr_sends.date10', 'remark')
        ->where('appr_sends.id', '=', $id)
        ->first();

        $path = '/adagio/' . $detail->file;            
        $file_path = asset($path);
        // $pdf = \App::make('dompdf.wrapper');
        // $pdf->getDomPDF()->set_option("enable_php", true);
        // $pdf->setPaper('A4', 'potrait');

        // $pdf->loadView('auto_approve.report.report_approve', array(
        //     'detail' => $detail
        // ));

        // $path = "adagio/" . $detail->no_transaction . ".pdf";
        // return $pdf->stream("ADG ".$detail->no_pr. ".pdf");

        return view('auto_approve.report.report_approve', array(
          'title' => 'MIRAI Approval System', 
          'title_jp' => 'MIRAI 承認システム',

          'detail' => $detail,
          'file_path' => $file_path
        ))->with('page', 'Approval File');
      }

      public function AdagioDataDoneReport(Request $request, $no_transaction){
        $detail = ApprSend::select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan', 'comment')
        ->where('no_transaction', '=', $no_transaction)
        ->first();

        $approver = db::select('SELECT request_id, approver_id, approver_name, approver_email, status, approved_at FROM appr_approvals WHERE request_id = "'.$no_transaction.'"');

        $path = '/adagio/' . $detail->file;            
        $path_ttd = '/adagio/ttd/' . $detail->file;            
        $file_path = asset($path);
        $file_path_ttd = asset($path_ttd);

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        return view('auto_approve.report.report', array(
          'title' => 'MIRAI Approval System', 
          'title_jp' => 'MIRAI 承認システム',

          'detail' => $detail,
          'file_path' => $file_path,
          'approver' => $approver,
          'file_path_ttd' => $file_path_ttd
        ))->with('page', 'Approval File');
      }

      public function ResumeDetail()
      {
        $trial = 117;
        $detail = ApprSend::select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
        ->where('id', '=', $trial)
        ->first();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');

        $pdf->loadView('auto_approve.report.resume_report', array(
          'detail' => $detail
        ));

        $pdf->save(public_path() . "/uploads/sakurentsu/trial_req/report/Report_".$trial.".pdf");
        return $pdf->stream("Trial Issue ".$trial.".pdf");
      }

      public function AdagioDataFetch($id){
        $detail = ApprSend::select('appr_sends.id', 'appr_sends.no_transaction', 'appr_sends.nik', 'appr_sends.department', 'appr_sends.description', 'appr_sends.date', 'appr_sends.summary', 'appr_sends.file', 'appr_sends.approve1', 'appr_sends.date1', 'appr_sends.approve2', 'appr_sends.date2', 'appr_sends.approve3', 'appr_sends.date3', 'appr_sends.approve4', 'appr_sends.date4', 'appr_sends.approve5', 'appr_sends.date5', 'appr_sends.approve6', 'appr_sends.date6', 'appr_sends.approve7', 'appr_sends.date7','appr_sends.approve8', 'appr_sends.date8', 'appr_sends.approve9', 'appr_sends.date9', 'appr_sends.approve10', 'appr_sends.date10', 'appr_sends.remark')
        ->where('appr_sends.id', '=', $id)
        ->first();

        $path = '/adagio/' . $detail->file;            
        $file_path = asset($path);


        // $pdf = \App::make('dompdf.wrapper');
        // $pdf->getDomPDF()->set_option("enable_php", true);
        // $pdf->setPaper('A4', 'potrait');

        // $pdf->loadView('auto_approve.report.report_approve', array(
        //     'detail' => $detail
        // ));

        // $path = "adagio/" . $detail->no_transaction . ".pdf";
        // return $pdf->stream("ADG ".$detail->no_pr. ".pdf");

        $response = array(
          'status' => true,
          'detail' => $detail,
          'file_path' => $file_path
        );
        return Response::json($response);
      }


      public function AdagioDataUser( Request $request)
      {
        try {
          $emp = DB::SELECT("select employee_id, `name`, department from employee_syncs where
            `employee_id` = '".$request->get('employee_id')."'
            AND `end_date` IS NULL");

            // $grade = $request->get('jabatan');
            // $jabatan = "where grade_code = '".$grade."' ";

            // $position = db::select("SELECT position FROM jabatan ".$jabatan." ");

          if (count($emp) > 0) {
            $response = array(
              'status' => true,
              'message' => 'Success',
              'employee' => $emp
            );
            return Response::json($response);
          }else{
            $response = array(
              'status' => false,
              'message' => 'Failed',
              'employee' => ''
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

      public function AdagioDataApproval(Request $request){
        $pesan = "";
        $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id','department')
        ->first();

        $lists = db::table('appr_masters')
        ->where('department', $emp_dept->department)
        ->where('created_by', '=', Auth::id())
        ->select('judul', 'user', 'urutan', 'department', 'category', 'jd_japan')
        ->orderBy('judul', 'ASC');

        $a = explode("/", $request->get('cat_app'));
        $n = $a[0];
        $m = $a[1];

        if ($request->get('cat_app') != null) {
          $select = ApprMasters::where('judul', $a[0])->where('created_by', $a[1])->select('user')->select('judul', 'user', 'urutan', 'department', 'category', 'jd_japan');

          $pesan = 'Category Approval Terpilih';

          $a = $select->get();

          $tahun = date("y");
          $bulan = date("m");
          $prefix_now = $tahun.$bulan;

          $dept = $a[0]->department;

          $department = db::table('departments')->where('department_name', $dept)->select('department_name', 'department_shortname')->first();

          $initial = $department->department_shortname;

          $code_generator = CodeGenerator::where('note','=','appr')->first();
          if ($prefix_now != $code_generator->prefix){
            $code_generator->prefix = $prefix_now;
            $code_generator->index = '0';
            $code_generator->save();
          }

          $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
          $no_appr = $initial . $code_generator->prefix . $numbers;
          $code_generator->index = $code_generator->index+1;
          $code_generator->save();

          $no_approval = $no_appr;
        }
        
        $list_all = db::select('SELECT judul, `user`, department, category, jd_japan, SUBSTRING_INDEX( urutan, "_", - 1 ) AS urutan FROM appr_masters WHERE judul = "'.$n.'" AND created_by = "'.$m.'" AND urutan like "%Approval%" ORDER BY CAST(SUBSTRING_INDEX( urutan, "_", - 1 ) AS UNSIGNED) ASC');
        $cc_all = db::select('SELECT judul, `user`, department, category, jd_japan, SUBSTRING_INDEX( urutan, "_", - 1 ) AS urutan FROM appr_masters WHERE judul = "'.$n.'" AND created_by = "'.$m.'" AND urutan like "%CC%" ORDER BY CAST(SUBSTRING_INDEX( urutan, "_", - 1 ) AS UNSIGNED) ASC');

        if(count($list_all) == 0){
          $response = array(
            'status' => false,
            'message' => 'Category Approval Tidak Ditemukan'
          );
          return Response::json($response);
        }

        $response = array(
          'status' => true,
          'lists' => $list_all,
          'message' => $pesan,
          'no_approval' => $no_approval,
          'cc' => $cc_all
        );
        return Response::json($response);
      }

      public function AdagioSendFile(Request $request){
        $id = Auth::id();
        try{
          $a = explode("/", $request->get('cat_app'));
          $no_approval = $request->get('number');

          $files = '';
          $file = new ApprSend();
          $fp = '';

          if ($request->file('file') != NULL)
          {
            if ($files = $request->file('file'))
              $nama_file = 'ADG'.$no_approval.'.pdf';
            $files->move('adagio', $nama_file);
          }
          else
          {
            $fp = NULL;
          }
          $tanggal = date('Y-m-d', strtotime($request->get('tanggal')));
          $master = db::select('SELECT judul, `user`, department, category, jd_japan, SUBSTRING_INDEX( urutan, "_", - 1 ) AS urutan FROM appr_masters WHERE judul = "'.$a[0].'" AND created_by = "'.$a[1].'" AND urutan like "%Approval%" ORDER BY CAST(SUBSTRING_INDEX( urutan, "_", - 1 ) AS UNSIGNED) ASC');

          $master_cc = db::select('SELECT judul, `user`, department, category, jd_japan, SUBSTRING_INDEX( urutan, "_", - 1 ) AS urutan FROM appr_masters WHERE judul = "'.$a[0].'" AND created_by = "'.$a[1].'" AND urutan like "%CC%" ORDER BY CAST(SUBSTRING_INDEX( urutan, "_", - 1 ) AS UNSIGNED) ASC');

          $approval = [];
          $category = [];
          $cc = [];
          $cc_category = [];

          for ($i=0; $i < count($master) ; $i++) {
            array_push($approval, $master[$i]->user);
            array_push($category, $master[$i]->category);
          }

          for ($i=0; $i < count($master_cc) ; $i++) {
            array_push($cc, $master_cc[$i]->user);
            array_push($cc_category, $master_cc[$i]->category);
          }

          $no = '';
          // if (count($request->get('no_dok')) > 0) {
          if ($request->get('no_dok') != null) {
            $no = $request->get('no_dok');
          }else{
            $no = $no_approval;
          }


          $data = new ApprSend([
            'no_transaction'  => $no_approval,
            'category'        => $a[0],
            'judul'           => $request->get('detail'),
            'no_dokumen'      => $no,
            'nik'             => $request->get('emp_id'),
            'department'      => $request->get('dept'),
            'summary'         => $request->get('summary'),
            'date'            => $tanggal,
            'file'            => $nama_file, 
            'file_pdf'        => $nama_file,
            'created_by'      => $id,
            'remark'          => 'Belum Kirim Email',
            'jd_japan'        => $request->get('jd_japan')
          ]);
          $data->save();

          for($i = 0; $i < count($approval) ; $i++){
            $users = User::where('username',explode('/',$approval[$i])[0])->first();
            $appr = new ApprApprovals([
              'request_id'      => $no_approval, 
              'approver_id'     => explode('/',$approval[$i])[0],
              'approver_name'   => explode('/',$approval[$i])[1], 
              'approver_email'  => $users->email,
              'remark'          => explode('/',$approval[$i])[2],
              'header'          => explode('/',$approval[$i])[3].'/'.explode('/',$approval[$i])[4]
            ]);

            $appr->save();
          }

    // for($i = 0; $i < count($cc) ; $i++){
      // $users = User::where('username',explode('/',$approval[$i])[0])->first();
    //   $appr = new ApprApprovals([
    //     'request_id'      => $no_approval, 
    //     'approver_id'     => explode('/',$cc[$i])[0],
    //     'approver_name'   => explode('/',$cc[$i])[1], 
    //     'approver_email'  => $users->email,
    //     'remark'          => explode('/',$cc[$i])[2],
    //     'header'          => explode('/',$cc[$i])[3].'/'.explode('/',$cc[$i])[4]
    //   ]);

    //   $appr->save();
    // }

          $file = ApprSend::where('no_transaction', '=', $no_approval)
          ->select('no_transaction', 'judul', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'created_by', 'created_at', 'jd_japan')
          ->first();

          $isi = ApprApprovals::where('request_id', '=', $no_approval)
          ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
          ->get();

    // $isi_approvals = [];
    // $isi_approval = [];
    // $count = 0;
    // for ($i=0; $i < count($isi); $i++) {
    //   $count+=1;
    //   if ($count == 5) {
    //     array_push($isi_approvals, $isi_approval);
    //     $isi_approval = [];
    //   }elseif ($count == 9) {
    //     array_push($isi_approvals, $isi_approval);
    //     $isi_approval = [];
    //   }elseif ($count == 13) {
    //     array_push($isi_approvals, $isi_approval);
    //     $isi_approval = [];
    //   }else{
    //     array_push($isi_approval, [
    //       'request_id' => $isi[$i]->request_id,
    //       'approver_id' => $isi[$i]->approver_id,
    //       'approver_name' => $isi[$i]->approver_name,
    //       'status' => $isi[$i]->status,
    //       'approved_at' => $isi[$i]->approved_at,
    //       'remark' => $isi[$i]->remark,
    //       'header' => $isi[$i]->header,
    //     ]);
    //   }
    // }

          $pdf = \App::make('dompdf.wrapper');
          $pdf->getDomPDF()->set_option("enable_php", true);
          $pdf->setPaper('A4', 'potrait');
          $pdf->loadView('auto_approve.report.tanda_tangan', array(
            'isi' => $isi,
            'file' => $file
          ));

          $pdf->save(public_path() . "/adagio/ttd/ADG".$data->no_transaction.".pdf");
          // $this->trialMerge($data->no_transaction);

    // Storage::disk('local')->put('/mirai_approval/'.$nama_file, file_get_contents('http://10.109.52.4/mirai/public/adagio/'.$nama_file));
    // $outputFile = Storage::disk('local')->path('mirai_approval/ADG'.$no_approval.'_output.pdf');
    // $this->fillPDF(Storage::disk('local')->path('/mirai_approval/'.$nama_file), $outputFile, $no_approval);

          return redirect('/index/mirai/approval')->with('status', 'Crete file approval success')->with('page', 'File Approve');
        }
        catch (QueryException $e){
          return redirect('/index/mirai/approval')->with('error', $e->getMessage())->with('page', 'File Approve');
        }
      }

      public function trialMerge($no_transaction)
      {
        $pdf = PDFMerger::init();
        $pdf->addPDF(public_path("/adagio/ADG".$no_transaction.".pdf"), 'all');
        $pdf->addPDF(public_path("/adagio/ttd/ADG".$no_transaction.".pdf"), 'all');

        $fileName = 'xxx.pdf';

        $pdf->merge();

        $pdf->save(public_path("/files/reed/label/" . $fileName));

      }

// public function Watermark(Request $request){
//   $no_approval = 'EI2209165';
//   Storage::disk('local')->put('/mirai_approval/ADGEI2209165.pdf', file_get_contents('http://10.109.52.4/mirai/public/adagio/ADGEI2209165.pdf'));
//   $outputFile = Storage::disk('local')->path('mirai_approval/ADGEI2209165_output.pdf');
//   $this->fillPDF(Storage::disk('local')->path('/mirai_approval/ADGEI2209165.pdf'), $outputFile, $no_approval);
//   return response()->file($outputFile);
// }

      public function fillPDF($file, $outputFile, $no_approval){

        $pdfFile1Path = public_path() . '\adagio\ttd\ADG'.$no_approval.'.pdf';
        $pdfFile2Path = public_path() . '\adagio\ADG'.$no_approval.'.pdf';
  // $pdfFile2Path = Storage::disk('local')->path('mirai_approval/ADG'.$no_approval.'_output.pdf');

        $merger = new Merger;
        $merger->addFile($pdfFile1Path);
        $merger->addFile($pdfFile2Path);
        $createdPdf = $merger->merge();

        $pathForTheMergedPdf = public_path() . '\adagio\merger\ADG'.$no_approval.'.pdf';

        file_put_contents($pathForTheMergedPdf, $createdPdf);
      }

      public function CreatePDFUlang(Request $request){
        try{
          $no_approval = $request->get('no_transaction');

          $data = ApprSend::where('no_transaction', $no_approval)->first();

          $file = ApprSend::where('no_transaction', '=', $no_approval)
          ->select('no_transaction', 'judul', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'created_by', 'created_at', 'jd_japan')
          ->first();

          $isi = ApprApprovals::where('request_id', '=', $no_approval)
          ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
          ->get();

          $pdf = \App::make('dompdf.wrapper');
          $pdf->getDomPDF()->set_option("enable_php", true);
          $pdf->setPaper('A4', 'potrait');

          $pdf->loadView('auto_approve.report.tanda_tangan', array(
            'isi' => $isi,
            'file' => $file
          ));

          $pdf->save(public_path() . "/adagio/ttd/ADG".$data->no_transaction.".pdf");

    // $pdfFile1Path = public_path() . '\adagio\ttd\ADG'.$data->no_transaction.'.pdf';
    // $pdfFile2Path = public_path() . '\adagio\ADG'.$data->no_transaction.'.pdf';

    // $merger = new Merger;
    // $merger->addFile($pdfFile1Path);
    // $merger->addFile($pdfFile2Path);
    // $createdPdf = $merger->merge();

    // $pathForTheMergedPdf = public_path() . '\adagio\merger\ADG'.$data->no_transaction.'.pdf';

    // file_put_contents($pathForTheMergedPdf, $createdPdf);

          return redirect('/index/mirai/approval')->with('status', 'Crete file approval success')->with('page', 'File Approve');
        }catch (QueryException $e){
          return redirect('/index/mirai/approval')->with('error', $e->getMessage())->with('page', 'File Approve');
        }
      }

      public function AdagioNoFile(Request $request)
      {
        $datenow = date('Y-m-d');
        $tahun = date('y');
        $bulan = date('m');
        $dept = $request->dept;

        $query = "SELECT no_transaction FROM `appr_sends` where department = '$dept' and DATE_FORMAT(`date`, '%y') = '$tahun' and month(`date`) = '$bulan' order by id DESC LIMIT 1";
        $nomorurut = DB::select($query);

        if ($nomorurut != null)
        {
          $nomor = substr($nomorurut[0]->no_transaction, -3);
          $nomor = $nomor + 1;
          $nomor = sprintf('%03d', $nomor);
        }
        else
        {
          $nomor = "001";
        }

        if ($dept == "Management Information System Department")
        {
          $dept = "IT";
        }
        else if ($dept == "Accounting Department")
        {
          $dept = "AC";
        }
        else if ($dept == "Woodwind Instrument - Assembly (WI-A) Department")
        {
          $dept = "AS";
        }
        else if ($dept == "Educational Instrument (EI) Department")
        {
          $dept = "EI";
        }
        else if ($dept == "General Affairs Department")
        {
          $dept = "GA";
        }
        else if ($dept == "Human Resources Department")
        {
          $dept = "HR";
        }
        else if ($dept == "Logistic Department")
        {
          $dept = "LG";
        }
        else if ($dept == "Maintenance Department")
        {
          $dept = "PM";
        }
        else if ($dept == "Woodwind Instrument - Key Parts Process (WI-KPP) Department")
        {
          $dept = "KP";
        }
        else if ($dept == "Woodwind Instrument - Body Parts Process (WI-BPP) Department")
        {
          $dept = "BP";
        }
        else if ($dept == "Procurement Department" || $dept == "Purchasing Control Department")
        {
          $dept = "PH";
        }
        else if ($dept == "Production Control Department")
        {
          $dept = "PC";
        }
        else if ($dept == "Production Engineering Department")
        {
          $dept = "PE";
        }
        else if ($dept == "Quality Assurance Department")
        {
          $dept = "QC";
        }
        else if ($dept == "Woodwind Instrument - Welding Process (WI-WP) Department")
        {
          $dept = "WP";
        }
        else if ($dept == "Woodwind Instrument - Surface Treatment (WI-ST) Department"){
          $dept = "ST";
        }
        else if ($dept == "General Process Control Department"){
          $dept = "GPC";
        }

        $result['tahun'] = $tahun;
        $result['bulan'] = $bulan;
        $result['dept'] = $dept;
        $result['no_urut'] = $nomor;

        return json_encode($result);
      }

      public function AdagioNoFileEO(Request $request)
      {
        $datenow = date('Y-m-d');
        $tahun = date('y');
        $bulan = date('m');

        $query = "SELECT no_transaction FROM `appr_sends` where no_transaction LIKE 'TR%' and DATE_FORMAT(`date`, '%y') = '$tahun' and month(`date`) = '$bulan' order by id DESC LIMIT 1";
        $nomorurut = DB::select($query);

        if ($nomorurut != null)
        {
          $nomor = substr($nomorurut[0]->no_transaction, -3);
          $nomor = $nomor + 1;
          $nomor = sprintf('%03d', $nomor);
        } else {
          $nomor = "001";
        }

        $response = array(
          'no_appr' => 'TR'.$tahun.$bulan.$nomor,
        );
        return Response::json($response);
      }

      public function AdagioDelete(Request $request){
        $auth_id = Auth::id();
        $file = ApprSend::where('id', '=', $request->get('id'))->first();
        try{
          $logs_list = new ApprLogs([
            'no_transaction' => $file->no_transaction,
            'nik' => $file->nik,
            'department' => $file->department,
            'description' => $file->description,
            'date' => $file->date,
            'summary' => $file->summary,
            'file' => $file->file,
            'file_pdf' => $file->file_pdf,
            'created_by' => $file->created_by,
            'created_at' => $file->created_at,
            'logs_at' => date("Y-m-d H:i:s"),
            'deleted_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            'remark' => 'deleted',

          ]);
          $logs_list->save();
          $file->forceDelete();
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
          'message' => 'File Approval Berhasil Di Delete',
        );
        return Response::json($response);
      }

      public function AdagioSendEmail($no_transaction){
       try{

        $file = ApprSend::where('no_transaction', '=', $no_transaction)->first();
        $file->remark = 'Open';
        $file->save();   

        $mail_to = ApprApprovals::where('request_id', '=', $no_transaction)
        ->wherenull('status')
        ->select('approver_email', 'approver_id')
        ->first();

        $mail = [];
        array_push($mail, $mail_to->approver_email);

        $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
        ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
        ->first();

        $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
        ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
        ->get();

        $data = [
          'appr_sends' => $appr_sends,
          'appr_approvals' => $appr_approvals,
          'mail_to' => $mail_to
        ];

        Mail::to($mail)
        ->bcc(['ympi-mis-ML@music.yamaha.com'])
        ->send(new SendEmail($data, 'send_email'));

        $response = array(
          'status' => true,
          'message' => 'Email berhasil terkirim.',
        );
        return Response::json($response);

  // return redirect('/index/mirai/approval')->with('status', 'MIRAI Approval System')->with('page', 'MIRAI Approval System');
      } catch(\Exception $e){
        $response = array(
          'status' => false,
          'message' => $e->getMessage(),
        );
        return Response::json($response);
  // return redirect('/index/mirai/approval')->with('error', 'MIRAI Approval System')->with('page', 'MIRAI Approval System');
      }
    }

    //approval
    public function AdagioConfirmation($no_transaction, $approver_id){
      try{
        $id = strtoupper(Auth::user()->username);
        $a = ApprApprovals::where('request_id', '=', $no_transaction)->wherenull('status')->take(1)->first();
        $b = ApprApprovals::where('request_id', '=', $no_transaction)->where('approver_id', '=', $id)->whereNotNull('status')->first();

    // var_dump($b->status);
    // die();

    //jika sudah selesai semua
        if ($a == null){
          $cek = db::select('select approver_id, approver_name, status from appr_approvals where request_id = "'.$no_transaction.'" and approver_id = "'.$id.'" and status is not null');

          $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
          ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
          ->first();

          $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
          ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
          ->get();

          $user = db::select('select employee_id, es.`name`, u.email, es.position from employee_syncs as es left join users as u on u.username = es.employee_id where end_date is null and u.id = "'.$appr_sends->created_by.'"');

          $aplicant = ApprApprovals::where('request_id', $no_transaction)
          ->select('approver_email')
          ->get();

          if (count($cek) > 0) {
            $p = 'Already Approved by '.$cek[0]->approver_name.'';

            return view('auto_approve.done_approve', array(
              'title'    => 'MIRAI Approval System', 
              'title_jp' => 'MIRAI 承認システム',
              'data'     => $appr_sends,
              'p' => $p,
            ))->with('page', 'MIRAI Approval System');
          }else{
            return view('auto_approve.done', array(
              'title'    => 'MIRAI Approval System', 
              'title_jp' => 'MIRAI 承認システム',
              'data'     => $appr_sends
            ))->with('page', 'MIRAI Approval System');
          }
        }

        if ($a->approver_id == $id) {
          $approvers = ApprApprovals::where('request_id', '=', $no_transaction)->wherenull('status')->take(1)->update([
            'status'    => 'Approved',
            'approved_at' => date('Y-m-d H:i:s')]);

          $clear_answer = ApprSend::where('no_transaction', '=', $no_transaction)->update([
            'comment' => null,
            'answer' => null
          ]);

          $file = ApprSend::where('no_transaction', '=', $no_transaction)
          ->select('no_transaction', 'judul', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'created_by', 'created_at', 'summary')
          ->first();

          $isi = ApprApprovals::where('request_id', '=', $no_transaction)
          ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
          ->get();

          $pdf = \App::make('dompdf.wrapper');
          $pdf->getDomPDF()->set_option("enable_php", true);
          $pdf->setPaper('A4', 'potrait');

          $pdf->loadView('auto_approve.report.tanda_tangan', array(
            'isi' => $isi,
            'file' => $file
          ));

          $pdf->save(public_path() . "/adagio/ttd/ADG".$no_transaction.".pdf");

      // Storage::disk('local')->put('ADG'.$no_transaction.'.pdf', file_get_contents('http://10.109.52.4/mirai/public/adagio/ADG'.$no_transaction.'.pdf'));
      // $outputFile = Storage::disk('local')->path('ADG'.$no_transaction.'_output.pdf');
      // $this->fillPDF(Storage::disk('local')->path('ADG'.$no_transaction.'.pdf'), $outputFile, $no_transaction);

          $email_next = ApprApprovals::where('request_id', '=', $no_transaction)->wherenull('status')->select('approver_email')->first();

          if ($email_next != NULL) {
            $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
            ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
            ->first();

            $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
            ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
            ->get();

            $header = 'Fully Approved';

            $data = [
              'appr_sends' => $appr_sends,
              'appr_approvals' => $appr_approvals,
              'header' => $header
            ];

            Mail::to($email_next->approver_email)
            ->bcc(['ympi-mis-ML@music.yamaha.com'])
            ->send(new SendEmail($data, 'send_email'));
          }

          if ($email_next == NULL) {
            $end = ApprSend::where('no_transaction', '=', $no_transaction)->update([
              'remark' => 'Close'
            ]);

    //  --------- Extra Order -----------
            if (strpos($no_transaction, 'TR') !== FALSE) {
             $data_email = ExtraOrderMaterial::where('extra_order_materials.remark', '=', $no_transaction)
             ->select('extra_order_materials.material_number', 'extra_order_materials.material_number_buyer', 'description', 'reference_number', 'remark', 'eo_number')
             ->first();

             $eo = ExtraOrderMaterial::where('extra_order_materials.remark', '=', $no_transaction)->update([
              'status' => 'Complete BOM'
            ]);

             $eo_file = db::select('SELECT attachment from extra_orders where eo_number = "'.$data_email->eo_number.'"');

             $data = [
              "materials" => $data_email,
              "position" => 'Complete BOM',
              "filename" => $eo_file[0]->attachment
            ];

            Mail::to(['mamluatul.atiyah@music.yamaha.com'])->cc(['darma.bagus@music.yamaha.com'])->bcc(['nasiqul.ibat@music.yamaha.com','muhammad.ikhlas@music.yamaha.com'])->send(new SendEmail($data, 'eo_material'));

            $data2 = [
              "materials" => $data_email,
              "position" => 'Request Sales Price',
              "filename" => $eo_file[0]->attachment
            ];

            Mail::to(['fathor.rahman@music.yamaha.com'])->bcc(['nasiqul.ibat@music.yamaha.com', 'muhammad.ikhlas@music.yamaha.com'])->send(new SendEmail($data2, 'eo_material'));
          }

          $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
          ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
          ->first();

          $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
          ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
          ->get();

          $user = db::select('select employee_id, es.`name`, u.email, es.position from employee_syncs as es left join users as u on u.username = es.employee_id where end_date is null and u.id = "'.$appr_sends->created_by.'"');

          $aplicant = ApprApprovals::where('request_id', $no_transaction)
          ->select('approver_email')
          ->get();

       // -------- Fixed Asset Summary --------
          if ($appr_sends->judul == 'Result Audit Fixed Asset') {
            $period = date('Y-m-d', strtotime('01-'.explode(' ',$appr_sends->summary)[3]));
            $location = explode(' ',$appr_sends->summary)[1];

            $update = FixedAssetSummary::where('period', '=', $period)->update([
              'prepare_date' => $appr_approvals[0]->approved_at,
              'acc_manager_at' => $appr_approvals[1]->approved_at,
              'finance_director_at' => $appr_approvals[2]->approved_at,
              'president_director_at' => $appr_approvals[3]->approved_at
            ]);


        // $summary = db::select("SELECT asset_section,
        //   count(fixed_asset_audits.id) as total_asset,
        //   SUM(IF(fixed_asset_audits.availability = 'Ada', 1, 0)) as ada_asset,
        //   SUM(IF(fixed_asset_audits.availability = 'Tidak Ada', 1, 0)) as tidak_ada_asset,
        //   SUM(IF(fixed_asset_audits.asset_condition = 'Rusak', 1, 0)) as rusak_asset,
        //   SUM(IF(fixed_asset_audits.usable_condition = 'Tidak Digunakan', 1, 0)) as tidak_digunakan_asset,
        //   SUM(IF(fixed_asset_audits.label_condition = 'Rusak', 1, 0)) as label_asset,
        //   SUM(IF(fixed_asset_audits.map_condition = 'Tidak Sesuai', 1, 0)) as tidak_map_asset,
        //   SUM(IF(fixed_asset_audits.asset_image_condition = 'Tidak Sesuai', 1, 0)) as tidak_foto_asset
        //   FROM `fixed_asset_audits` where period = '".$period."'
        //   and category = '".$location."' 
        //   GROUP BY asset_section");

        // $sign = FixedAssetSummary::where('period', $period)
        // ->select('location', 'status', 'prepared_by', db::raw('DATE_FORMAT(prepare_date, "%d %b %Y") as prepare_date'), 'acc_manager', db::raw('DATE_FORMAT(acc_manager_at, "%d %b %Y") as acc_manager_at'), 'finance_director', db::raw('DATE_FORMAT(finance_director_at, "%d %b %Y") as finance_director_at'), 'president_director', db::raw('DATE_FORMAT(president_director_at, "%d %b %Y") as president_director_at'), db::raw('DATE_FORMAT(created_at, "%d %b %Y") as create_date'))
        // ->first();

        // $pdf = \App::make('dompdf.wrapper');
        // $pdf->getDomPDF()->set_option("enable_php", true);
        // $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);    
        // $pdf->setPaper('A4', 'landscape');

        // $pdf->loadView('fixed_asset.report_file.summary_report', array(
        //   'datas' => $summary,
        //   'sign' => $sign,
        //   'periode' => date('M Y', strtotime($period))
        // ));

        // $pdf->save(public_path() . "/files/fixed_asset/report_summary/".$location."_".$period.".pdf");
          }

          $header = 'Fully Approved';

          $data = [
            'appr_sends' => $appr_sends,
            'appr_approvals' => $appr_approvals,
            'header' => $header
          ];
      //disini ya
          if (($user[0]->position == 'Sub Leader') || ($user[0]->position == 'Leader')) {
            Mail::to($aplicant[0]->approver_email)
            ->bcc(['ympi-mis-ML@music.yamaha.com'])
            ->send(new SendEmail($data, 'send_email_done'));
          }else{
            Mail::to($user[0]->email)
            ->bcc(['ympi-mis-ML@music.yamaha.com'])
            ->send(new SendEmail($data, 'send_email_done'));
          }
        }

        return view('auto_approve.done', array(
          'title'    => 'MIRAI Approval System', 
          'title_jp' => 'MIRAI 承認システム',
          'data'     => $appr_sends
        ))->with('page', 'MIRAI Approval System');
      }
      else{
        $id = strtoupper(Auth::user()->username);
        $a = ApprApprovals::where('request_id', '=', $no_transaction)->wherenull('status')->take(1)->first();
        $b = ApprApprovals::where('request_id', '=', $no_transaction)->where('approver_id', '=', $id)->whereNotNull('status')->first();

        if ($b == null) {
          $p = 'Sorry, You Don`t Have Access To Approval';
          $l = 'すみません、承認権限がありません。';

          $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
          ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
          ->first();

          $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
          ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
          ->get();

          $data = [
            'appr_sends' => $appr_sends,
            'appr_approvals' => $appr_approvals
          ];
          return view('auto_approve.report.salah', array(
            'title'    => 'MIRAI Approval System', 
            'title_jp' => 'MIRAI 承認システム',
            'data'     => $appr_sends,
            'p' => $p,
            'l' => $l
          ))->with('page', 'MIRAI Approval System');
        }else{
        // $p = "You don't have authorization (権限がありません)";
          if ($approver_id != $a->approver_id) {
            $cek = db::select('select approver_id, approver_name, status from appr_approvals where request_id = "'.$no_transaction.'" and approver_id = "'.$id.'" and status is not null');

            if (count($cek) > 0) {
              $p = 'Already Approved by '.$cek[0]->approver_name.'';
            }else{
              $p = 'Previous Approver Has Not Approved (以前の承認者がまだ承認していません)';
            }
          }else{
            $p = "You don't have authorization (権限がありません)";
          }

          $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
          ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
          ->first();

          $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
          ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
          ->get();

          $data = [
            'appr_sends' => $appr_sends,
            'appr_approvals' => $appr_approvals
          ];
          return view('auto_approve.done_approve', array(
            'title'    => 'MIRAI Approval System', 
            'title_jp' => 'MIRAI 承認システム',
            'data'     => $appr_sends,
            'p' => $p,
          ))->with('page', 'MIRAI Approval System');
        }
      }

    // if (($a->status == null) && ($a->approver_id != $id)){
    //   $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
    //   ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
    //   ->first();

    //   $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
    //   ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
    //   ->get();

    //   $data = [
    //     'appr_sends' => $appr_sends,
    //     'appr_approvals' => $appr_approvals
    //   ];
    //   return view('auto_approve.report.salah', array(
    //     'title'    => 'MIRAI Approval System', 
    //     'title_jp' => 'MIRAI 承認システム',
    //     'data'     => $appr_sends
    //   ))->with('page', 'MIRAI Approval System');
    // }
    }
    catch(\Exception $e){
      $response = array(
        'status' => false,
        'message' => $e->getMessage(),
      );
      return Response::json($response);
    }
  }

  public function ConfirmationEmail($no_transaction){
    $approver_id = db::select('select approver_id from appr_approvals where request_id = "'.$no_transaction.'" and status is null order by id asc');

    try{
      $id = strtoupper(Auth::user()->username);
      $a = ApprApprovals::where('request_id', '=', $no_transaction)->wherenull('status')->take(1)->first();
      $b = ApprApprovals::where('request_id', '=', $no_transaction)->where('approver_id', '=', $id)->whereNotNull('status')->first();

      // if (count($a) > 0) {
      if ($a != null) {
        if ($a->approver_id == $id) {
          $approvers = ApprApprovals::where('request_id', '=', $no_transaction)->wherenull('status')->take(1)->update([
            'status'    => 'Approved',
            'approved_at' => date('Y-m-d H:i:s')]);

          $mail_to = ApprApprovals::where('request_id', '=', $no_transaction)
          ->wherenull('status')
          ->select('approver_email', 'approver_id')
          ->first();

          $clear_answer = ApprSend::where('no_transaction', '=', $no_transaction)->update([
            'comment' => null,
            'answer' => null
          ]);

          $file = ApprSend::where('no_transaction', '=', $no_transaction)
          ->select('no_transaction', 'judul', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'created_by', 'created_at', 'summary')
          ->first();

          $isi = ApprApprovals::where('request_id', '=', $no_transaction)
          ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
          ->get();

          $pdf = \App::make('dompdf.wrapper');
          $pdf->getDomPDF()->set_option("enable_php", true);
          $pdf->setPaper('A4', 'potrait');

          $pdf->loadView('auto_approve.report.tanda_tangan', array(
            'isi' => $isi,
            'file' => $file
          ));

          $pdf->save(public_path() . "/adagio/ttd/ADG".$no_transaction.".pdf");

        // Storage::disk('local')->put('ADG'.$no_transaction.'.pdf', file_get_contents('http://10.109.52.4/mirai/public/adagio/ADG'.$no_transaction.'.pdf'));
        // $outputFile = Storage::disk('local')->path('ADG'.$no_transaction.'_output.pdf');
        // $this->fillPDF(Storage::disk('local')->path('ADG'.$no_transaction.'.pdf'), $outputFile, $no_transaction);

          $email_next = ApprApprovals::where('request_id', '=', $no_transaction)->wherenull('status')->select('approver_email', 'header')->first();

          if ($email_next != NULL) {
            $header = explode('/', $email_next->header);
          // if ($header[0] == 'Known by') {
          //   $pp = db::select('select header, approver_email from appr_approvals where request_id = "'.$no_transaction.'" and header = "Known by/(承知)"');
          //   $approvers = ApprApprovals::where('request_id', '=', $no_transaction)->where('header', '=', 'Known by/(承知)')->update([
          //     'status'    => 'Approved',
          //     'approved_at' => date('Y-m-d H:i:s')]);
          //   $end = ApprSend::where('no_transaction', '=', $no_transaction)->update([
          //     'remark' => 'Close'
          //   ]);

          //   $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
          //    ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
          //    ->first();

          //    $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
          //    ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
          //    ->get(); 
          //    $header = 'Fully Approved';

          //    $data = [
          //     'appr_sends' => $appr_sends,
          //     'appr_approvals' => $appr_approvals,
          //     'header' => $header
          //   ];

          //   for ($i=0; $i < count($pp); $i++) {
          //     Mail::to($pp[$i]->approver_email)
          //     ->bcc(['ympi-mis-ML@music.yamaha.com'])
          //     ->send(new SendEmail($data, 'send_email_done'));
          //   }

          //   $user = User::where('id', '=', $file->created_by)
          //   ->select('email')
          //   ->first();

          //   Mail::to($user->email)
          //   ->bcc(['ympi-mis-ML@music.yamaha.com'])
          //   ->send(new SendEmail($data, 'send_email_done'));
          // }else{
            $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
            ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
            ->first();

            $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
            ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
            ->get();


            $data = [
              'appr_sends' => $appr_sends,
              'appr_approvals' => $appr_approvals,
              'mail_to' => $mail_to
            ];

            Mail::to($email_next->approver_email)
            ->bcc(['ympi-mis-ML@music.yamaha.com'])
            ->send(new SendEmail($data, 'send_email'));
          // }
          }

          if ($email_next == NULL) {
            $end = ApprSend::where('no_transaction', '=', $no_transaction)->update([
              'remark' => 'Close'
            ]);

            $user = User::where('id', '=', $file->created_by)
            ->select('email')
            ->first();

            $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
            ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
            ->first();

            $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
            ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
            ->get();

           // -------- Fixed Asset Summary --------
            if ($appr_sends->judul == 'Result Audit Fixed Asset') {
              $period = date('Y-m-d', strtotime('01-'.explode(' ',$appr_sends->summary)[3]));
              $location = explode(' ',$appr_sends->summary)[1];

              $update = FixedAssetSummary::where('period', '=', $period)->update([
                'prepare_date' => $appr_approvals[0]->approved_at,
                'acc_manager_at' => $appr_approvals[1]->approved_at,
                'finance_director_at' => $appr_approvals[2]->approved_at,
                'president_director_at' => $appr_approvals[3]->approved_at
              ]);


            // $summary = db::select("SELECT asset_section,
            //   count(fixed_asset_audits.id) as total_asset,
            //   SUM(IF(fixed_asset_audits.availability = 'Ada', 1, 0)) as ada_asset,
            //   SUM(IF(fixed_asset_audits.availability = 'Tidak Ada', 1, 0)) as tidak_ada_asset,
            //   SUM(IF(fixed_asset_audits.asset_condition = 'Rusak', 1, 0)) as rusak_asset,
            //   SUM(IF(fixed_asset_audits.usable_condition = 'Tidak Digunakan', 1, 0)) as tidak_digunakan_asset,
            //   SUM(IF(fixed_asset_audits.label_condition = 'Rusak', 1, 0)) as label_asset,
            //   SUM(IF(fixed_asset_audits.map_condition = 'Tidak Sesuai', 1, 0)) as tidak_map_asset,
            //   SUM(IF(fixed_asset_audits.asset_image_condition = 'Tidak Sesuai', 1, 0)) as tidak_foto_asset
            //   FROM `fixed_asset_audits` where period = '".$period."'
            //   and category = '".$location."' 
            //   GROUP BY asset_section");

            // $sign = FixedAssetSummary::where('period', $period)
            // ->select('location', 'status', 'prepared_by', db::raw('DATE_FORMAT(prepare_date, "%d %b %Y") as prepare_date'), 'acc_manager', db::raw('DATE_FORMAT(acc_manager_at, "%d %b %Y") as acc_manager_at'), 'finance_director', db::raw('DATE_FORMAT(finance_director_at, "%d %b %Y") as finance_director_at'), 'president_director', db::raw('DATE_FORMAT(president_director_at, "%d %b %Y") as president_director_at'), db::raw('DATE_FORMAT(created_at, "%d %b %Y") as create_date'))
            // ->first();

            // $pdf = \App::make('dompdf.wrapper');
            // $pdf->getDomPDF()->set_option("enable_php", true);
            // $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);    
            // $pdf->setPaper('A4', 'landscape');

            // $pdf->loadView('fixed_asset.report_file.summary_report', array(
            //   'datas' => $summary,
            //   'sign' => $sign,
            //   'periode' => date('M Y', strtotime($period))
            // ));

            // $pdf->save(public_path() . "/files/fixed_asset/report_summary/".$location."_".$period.".pdf");
            }

            $header = 'Fully Approved';

            $data = [
              'appr_sends' => $appr_sends,
              'appr_approvals' => $appr_approvals,
              'header' => $header
            ];

            Mail::to($user->email)
            ->bcc(['ympi-mis-ML@music.yamaha.com'])
            ->send(new SendEmail($data, 'send_email_done'));


        //  ---------- Extra Order ---------------

            if (strpos($no_transaction, 'TR') !== FALSE) {
              $data_email = ExtraOrderMaterial::where('extra_order_materials.remark', '=', $no_transaction)
              ->select('extra_order_materials.material_number', 'extra_order_materials.material_number_buyer', 'description', 'reference_form_number', 'remark', 'eo_number')
              ->first();

              $eo = ExtraOrderMaterial::where('extra_order_materials.remark', '=', $no_transaction)->update([
                'status' => 'Complete BOM'
              ]);


              $eo_file = db::select('SELECT attachment from extra_orders where eo_number = "'.$data_email->eo_number.'"');

              $data = [
                "materials" => $data_email,
                "position" => 'Complete BOM',
                "filename" => $eo_file[0]->attachment
              ];

              Mail::to(['mamluatul.atiyah@music.yamaha.com'])->bcc(['muhammad.ikhlas@music.yamaha.com', 'nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'eo_material'));

              $data2 = [
                "materials" => $data_email,
                "position" => 'Request Sales Price',
                "filename" => $eo_file[0]->attachment
              ];

              Mail::to(['fathor.rahman@music.yamaha.com'])->bcc(['muhammad.ikhlas@music.yamaha.com', 'nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data2, 'eo_material'));
            }
          }

          return view('auto_approve.done', array(
            'title'    => 'MIRAI Approval System', 
            'title_jp' => 'MIRAI 承認システム',
            'data'     => $appr_sends
          ))->with('page', 'MIRAI Approval System');
        }

        else{
          $cek = db::select('select approver_id, approver_name, status from appr_approvals where request_id = "'.$no_transaction.'" and approver_id = "'.$id.'" and status is not null');

          if (count($cek) > 0) {
            $p = 'Already Approved by '.$cek[0]->approver_name.'';
          }else{
            $p = "You don't have authorization (権限がありません)";
          }

          $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
          ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
          ->first();

          $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
          ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
          ->get();

          $data = [
            'appr_sends' => $appr_sends,
            'appr_approvals' => $appr_approvals
          ];
          return view('auto_approve.done_approve', array(
            'title'    => 'MIRAI Approval System', 
            'title_jp' => 'MIRAI 承認システム',
            'data'     => $appr_sends,
            'p' => $p
          ))->with('page', 'MIRAI Approval System');
        }
      } else{
        $cek = db::select('select approver_id, approver_name, status from appr_approvals where request_id = "'.$no_transaction.'" and approver_id = "'.$id.'" and status is not null');

        if (count($cek) > 0) {
          $p = 'Already Approved by '.$cek[0]->approver_name.'';
        }else{
          $p = "You don't have authorization (権限がありません)";
        }

        $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
        ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
        ->first();

        $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
        ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
        ->get();

        $data = [
          'appr_sends' => $appr_sends,
          'appr_approvals' => $appr_approvals
        ];
        return view('auto_approve.done_approve', array(
          'title'    => 'MIRAI Approval System', 
          'title_jp' => 'MIRAI 承認システム',
          'data'     => $appr_sends,
          'p' => $p
        ))->with('page', 'MIRAI Approval System');
      }
      
    }
    catch(\Exception $e){
      $response = array(
        'status' => false
      );

      return view('auto_approve.gagal', array(
        'title'    => 'MIRAI Approval System', 
        'title_jp' => 'MIRAI 承認システム'
      ))->with('page', 'MIRAI Approval System');
    }
  }

  public function AdagioViewSign($no_transaction, $approver_id){
    $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
    ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
    ->first();

    $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
    ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
    ->get();

    $data = [
      'appr_sends' => $appr_sends,
      'appr_approvals' => $appr_approvals
    ];

    return view('auto_approve.sign_approval', array(
  // return view('mails.sign_approval', array(
      'title'    => 'MIRAI Approval System', 
      'title_jp' => 'MIRAI 承認システム',
      'data'     => $appr_sends,
      'appr_approvals' => $appr_approvals,
      'approver_id' => $approver_id
    ))->with('page', 'MIRAI Approval System');
  }

  public function AdagioRejected($no_transaction, $approver_id){
    try{
      $id = strtoupper(Auth::user()->username);
      $a = ApprApprovals::where('request_id', '=', $no_transaction)->wherenull('status')->take(1)->first();
      $b = ApprApprovals::where('request_id', '=', $no_transaction)->where('approver_id', '=', $id)->whereNotNull('status')->first();

      if ($a->approver_id == $id) {
        $p = 'Rejected';


      // $approvers = ApprApprovals::where('request_id', '=', $no_transaction)->wherenull('status')->update([
      //   'status'    => 'Rejected',
      //   'approved_at' => date('Y-m-d H:i:s')]);

      // $end = ApprSend::where('no_transaction', '=', $no_transaction)->update([
      //   'remark' => 'Rejected'
      // ]);

      // $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
      // ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan', 'reason', 'comment', 'answer')
      // ->first();

      // $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
      // ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
      // ->get();

      // $pdf = \App::make('dompdf.wrapper');
      // $pdf->getDomPDF()->set_option("enable_php", true);
      // $pdf->setPaper('A4', 'landscape');

      // $pdf->loadView('auto_approve.report.tanda_tangan', array(
      //   'file' => $appr_sends,
      //   'isi' => $appr_approvals
      // ));

      // $pdf->save(public_path() . "/adagio/ttd/ADG".$no_transaction.".pdf");

      // $user = db::select('select employee_id, es.`name`, u.email, es.position from employee_syncs as es left join users as u on u.username = es.employee_id where end_date is null and u.id = "'.$appr_sends->created_by.'"');

      // $aplicant = ApprApprovals::where('request_id', $no_transaction)
      // ->select('approver_email')
      // ->get();

      // $header = 'Reject';

      // $data = [
      //   'appr_sends' => $appr_sends,
      //   'appr_approvals' => $appr_approvals,
      //   'header' => $header
      // ];

      // if (($user[0]->position == 'Sub Leader') || ($user[0]->position == 'Leader')) {
      //   Mail::to($aplicant[0]->approver_email)
      //   ->bcc(['ympi-mis-ML@music.yamaha.com'])
      //   ->send(new SendEmail($data, 'send_email_done'));
      // }else{
      //   Mail::to($user[0]->email)
      //   ->bcc(['ympi-mis-ML@music.yamaha.com'])
      //   ->send(new SendEmail($data, 'send_email_done'));
      // }

        $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
        ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan', 'reason', 'comment', 'answer')
        ->first();

        $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
        ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
        ->get();

        $data = [
          'appr_sends' => $appr_sends,
          'appr_approvals' => $appr_approvals
        ];

        return view('auto_approve.index_reject', array(
          'title'    => 'MIRAI Approval System', 
          'title_jp' => 'MIRAI 承認システム',
          'data'     => $appr_sends,
          'p' => $p

        ))->with('page', 'MIRAI Approval System');
      }
      else{
        $id = strtoupper(Auth::user()->username);
        $a = ApprApprovals::where('request_id', '=', $no_transaction)->wherenull('status')->take(1)->first();
        $b = ApprApprovals::where('request_id', '=', $no_transaction)->where('approver_id', '=', $id)->whereNotNull('status')->first();

        if ($b == null) {
          $p = 'Sorry, You Don`t Have Access To Approval';
          $l = '承認へのアクセス権がありません';

          $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
          ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
          ->first();

          $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
          ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
          ->get();

          $data = [
            'appr_sends' => $appr_sends,
            'appr_approvals' => $appr_approvals
          ];
          return view('auto_approve.report.salah', array(
            'title'    => 'MIRAI Approval System', 
            'title_jp' => 'MIRAI 承認システム',
            'data'     => $appr_sends,
            'p' => $p,
            'l' => $l
          ))->with('page', 'MIRAI Approval System');
        }else{
        // $p = "You don't have authorization (権限がありません)";
          if ($approver_id != $a->approver_id) {
            $p = 'Previous Approver Has Not Approved (以前の承認者がまだ承認していません)';
          }else{
            $p = "You don't have authorization (権限がありません)";
          }

          $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
          ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
          ->first();

          $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
          ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
          ->get();

          $data = [
            'appr_sends' => $appr_sends,
            'appr_approvals' => $appr_approvals
          ];
          return view('auto_approve.done_approve', array(
            'title'    => 'MIRAI Approval System', 
            'title_jp' => 'MIRAI 承認システム',
            'data'     => $appr_sends,
            'p' => $p,
          ))->with('page', 'MIRAI Approval System');
        }
      }
      
    }
    catch(\Exception $e){
      $response = array(
        'status' => false,
        'message' => $e->getMessage(),
      );
      return Response::json($response);
    }
  }

  public function AdagioRejectedEmail($no_transaction){
    try{
      $id = strtoupper(Auth::user()->username);
      $a = ApprApprovals::where('request_id', '=', $no_transaction)->wherenull('status')->take(1)->first();
      $b = ApprApprovals::where('request_id', '=', $no_transaction)->where('approver_id', '=', $id)->whereNotNull('status')->first();

      if ($a->approver_id == $id) {
        $approvers = ApprApprovals::where('request_id', '=', $no_transaction)->wherenull('status')->update([
          'status'    => 'Rejected',
          'approved_at' => date('Y-m-d H:i:s')]);

        $end = ApprSend::where('no_transaction', '=', $no_transaction)->update([
          'remark' => 'Rejected'
        ]);

        $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
        ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan', 'reason', 'comment', 'answer')
        ->first();

        $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
        ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
        ->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'landscape');

        $pdf->loadView('auto_approve.report.tanda_tangan', array(
          'file' => $appr_sends,
          'isi' => $appr_approvals
        ));

        $pdf->save(public_path() . "/adagio/ttd/ADG".$no_transaction.".pdf");

        $user = User::where('id', '=', $appr_sends->created_by)
        ->select('email')
        ->first();

        $data = [
          'appr_sends' => $appr_sends,
          'appr_approvals' => $appr_approvals,
          'header' => 'Rejected'
        ];

        Mail::to($user->email)
        ->bcc(['ympi-mis-ML@music.yamaha.com'])
        ->send(new SendEmail($data, 'send_email_done'));

        return view('auto_approve.done', array(
          'title'    => 'MIRAI Approval System', 
          'title_jp' => 'MIRAI 承認システム',
          'data'     => $appr_sends

        ))->with('page', 'MIRAI Approval System');
      }
      else{
        $p = "You don't have authorization (権限がありません)";

        $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
        ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
        ->first();

        $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
        ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
        ->get();

        $data = [
          'appr_sends' => $appr_sends,
          'appr_approvals' => $appr_approvals
        ];
        return view('auto_approve.done_approve', array(
          'title'    => 'MIRAI Approval System', 
          'title_jp' => 'MIRAI 承認システム',
          'data'     => $appr_sends,
          'p' => $p,
        ))->with('page', 'MIRAI Approval System');
      }
    }
    catch(\Exception $e){
      $response = array(
        'status' => false,
        'message' => $e->getMessage(),
      );
      return Response::json($response);
    }
  }

  public function ReasonReject(Request $request, $no_transaction){
    try{
      $text = $request->get('question');
      $end = ApprSend::where('no_transaction', '=', $no_transaction)->update([
        'remark' => 'Send Aplicant Reject',
        'reason' => $text
      ]);

      $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
      ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan', 'reason', 'comment', 'answer')
      ->first();

      $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
      ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
      ->get();

      $pdf = \App::make('dompdf.wrapper');
      $pdf->getDomPDF()->set_option("enable_php", true);
      $pdf->setPaper('A4', 'landscape');

      $pdf->loadView('auto_approve.report.tanda_tangan', array(
        'file' => $appr_sends,
        'isi' => $appr_approvals
      ));

      $pdf->save(public_path() . "/adagio/ttd/ADG".$no_transaction.".pdf");

      $user = User::where('id', '=', $appr_sends->created_by)
      ->select('email')
      ->first();

      $data = [
        'appr_sends' => $appr_sends,
        'appr_approvals' => $appr_approvals
      ];

      Mail::to($user->email)
      ->bcc(['ympi-mis-ML@music.yamaha.com'])
      ->send(new SendEmail($data, 'send_email_done'));

      return view('auto_approve.done', array(
        'title'    => 'MIRAI Approval System', 
        'title_jp' => 'MIRAI 承認システム',
        'data'     => $appr_sends

      ))->with('page', 'MIRAI Approval System');
    }
    catch(\Exception $e){
      $response = array(
        'status' => false,
        'message' => $e->getMessage(),
      );
      return Response::json($response);
    }
  }

  public function AdagioHold($no_transaction, $approver_id){
    try{
      $id = strtoupper(Auth::user()->username);
      $a = ApprApprovals::where('request_id', '=', $no_transaction)->wherenull('status')->take(1)->first();
      $b = ApprApprovals::where('request_id', '=', $no_transaction)->where('approver_id', '=', $id)->whereNotNull('status')->first();

      if ($a->approver_id == $id) {
        $approvers = ApprApprovals::where('request_id', '=', $no_transaction)->wherenull('status')->take(1)->update([
          'status'    => 'Hold & Comment',
          'approved_at' => date('Y-m-d H:i:s')]);

        $end = ApprSend::where('no_transaction', '=', $no_transaction)->update([
          'remark' => 'Hold & Comment'
        ]);

        $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
        ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan', 'reason', 'comment', 'answer')
        ->first();

        $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
        ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
        ->get();

        $data = [
          'appr_sends' => $appr_sends,
          'appr_approvals' => $appr_approvals
        ];

        return view('auto_approve.done', array(
          'title'    => 'MIRAI Approval System', 
          'title_jp' => 'MIRAI 承認システム',
          'data'     => $appr_sends

        ))->with('page', 'MIRAI Approval System');
      }
      else{
        $id = strtoupper(Auth::user()->username);
        $a = ApprApprovals::where('request_id', '=', $no_transaction)->wherenull('status')->take(1)->first();
        $b = ApprApprovals::where('request_id', '=', $no_transaction)->where('approver_id', '=', $id)->whereNotNull('status')->first();

        if ($b == null) {
          $p = 'Sorry, You Don`t Have Access To Approval';
          $l = '承認へのアクセス権がありません';

          $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
          ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
          ->first();

          $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
          ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
          ->get();

          $data = [
            'appr_sends' => $appr_sends,
            'appr_approvals' => $appr_approvals
          ];
          return view('auto_approve.report.salah', array(
            'title'    => 'MIRAI Approval System', 
            'title_jp' => 'MIRAI 承認システム',
            'data'     => $appr_sends,
            'p' => $p,
            'l' => $l
          ))->with('page', 'MIRAI Approval System');
        }else{
        // $p = "You don't have authorization (権限がありません)";
          if ($approver_id != $a->approver_id) {
            $p = 'Previous Approver Has Not Approved (以前の承認者がまだ承認していません)';
          }else{
            $p = "You don't have authorization (権限がありません)";
          }

          $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
          ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
          ->first();

          $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
          ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
          ->get();

          $data = [
            'appr_sends' => $appr_sends,
            'appr_approvals' => $appr_approvals
          ];
          return view('auto_approve.done_approve', array(
            'title'    => 'MIRAI Approval System', 
            'title_jp' => 'MIRAI 承認システム',
            'data'     => $appr_sends,
            'p' => $p,
          ))->with('page', 'MIRAI Approval System');
        }
      }
    }
    catch(\Exception $e){
      $response = array(
        'status' => false,
        'message' => $e->getMessage(),
      );
      return Response::json($response);
    }
  }

  public function AdagioHoldEmail($no_transaction){
    try{
      $id = strtoupper(Auth::user()->username);
      $a = ApprApprovals::where('request_id', '=', $no_transaction)->wherenull('status')->take(1)->first();
      $b = ApprApprovals::where('request_id', '=', $no_transaction)->where('approver_id', '=', $id)->whereNotNull('status')->first();

    // dd($a->approver_id, $id);
      if ($a->approver_id == $id) {
        $p = 'The Comment Has Been Sent To The Aplicant (コメントが申請者に送信されました)';
      // $approvers = ApprApprovals::where('request_id', '=', $no_transaction)->wherenull('status')->take(1)->update([
      //   'status'    => 'Hold & Comment',
      //   'approved_at' => date('Y-m-d H:i:s')]);

      // $end = ApprSend::where('no_transaction', '=', $no_transaction)->update([
      //   'remark' => 'Hold & Comment'
      // ]);

        $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
        ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan', 'reason', 'comment', 'answer')
        ->first();

        $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
        ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
        ->get();

        $data = [
          'appr_sends' => $appr_sends,
          'appr_approvals' => $appr_approvals
        ];

        return view('auto_approve.index_hold', array(
          'title'    => 'MIRAI Approval System', 
          'title_jp' => 'MIRAI 承認システム',
          'data'     => $appr_sends,
          'p' => $p

        ))->with('page', 'MIRAI Approval System');
      }
      else{
        $p = "You don't have authorization (権限がありません)";

        $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
        ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
        ->first();

        $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
        ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
        ->get();

        $data = [
          'appr_sends' => $appr_sends,
          'appr_approvals' => $appr_approvals
        ];
        return view('auto_approve.index_hold', array(
          'title'    => 'MIRAI Approval System', 
          'title_jp' => 'MIRAI 承認システム',
          'data'     => $appr_sends,
          'p' => $p,
        ))->with('page', 'MIRAI Approval System');
      }
    }
    catch(\Exception $e){
      $response = array(
        'status' => false,
        'message' => $e->getMessage(),
      );
      return Response::json($response);
    }
  }

  public function AdagioHoldPost(Request $request){
    try{
      $reason = $request->get('reason');
      $no_transaction = $request->get('no_approval');
      $username = strtoupper(Auth::user()->username);

      $end = ApprSend::where('no_transaction', '=', $no_transaction)->update([
        'remark' => 'Send Aplicant Hold & Comment',
        'comment' => $reason
      ]);

      $approvers = ApprApprovals::where('request_id', '=', $no_transaction)->wherenull('status')->take(1)->update([
        'status'    => 'Hold & Comment',
        'approved_at' => date('Y-m-d H:i:s')]);

      $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
      ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan', 'reason', 'comment', 'answer')
      ->first();

      $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
      ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
      ->get();

      $pdf = \App::make('dompdf.wrapper');
      $pdf->getDomPDF()->set_option("enable_php", true);
      $pdf->setPaper('A4', 'landscape');

      $pdf->loadView('auto_approve.report.tanda_tangan', array(
        'file' => $appr_sends,
        'isi' => $appr_approvals
      ));

      $pdf->save(public_path() . "/adagio/ttd/ADG".$no_transaction.".pdf");

      $user = db::select('select employee_id, es.`name`, u.email, es.position from employee_syncs as es left join users as u on u.username = es.employee_id where end_date is null and u.id = "'.$appr_sends->created_by.'"');

      $aplicant = ApprApprovals::where('request_id', $no_transaction)
      ->select('approver_email')
      ->get();

      $data = [
        'appr_sends' => $appr_sends,
        'appr_approvals' => $appr_approvals,
        'header' => 'Hold & Comment'
      ];

      if (($user[0]->position == 'Sub Leader') || ($user[0]->position == 'Leader')) {
        Mail::to($aplicant[0]->approver_email)
        ->bcc(['ympi-mis-ML@music.yamaha.com'])
        ->send(new SendEmail($data, 'send_email_done'));
      }else{
        Mail::to($user[0]->email)
        ->bcc(['ympi-mis-ML@music.yamaha.com'])
        ->send(new SendEmail($data, 'send_email_done'));
      }

      $response = array(
        'status'        => true
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

  public function AdagioRejectPost(Request $request){
    try{
      $reason = $request->get('reason');
      $no_transaction = $request->get('no_approval');
      $username = strtoupper(Auth::user()->username);

      $end = ApprSend::where('no_transaction', '=', $no_transaction)->update([
        'remark' => 'Rejected',
        'comment' => $reason
      ]);

      $approvers = ApprApprovals::where('request_id', '=', $no_transaction)->wherenull('status')->update([
        'status'    => 'Rejected',
        'approved_at' => date('Y-m-d H:i:s')]);

      $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
      ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan', 'reason', 'comment', 'answer')
      ->first();

      $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
      ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
      ->get();

      $pdf = \App::make('dompdf.wrapper');
      $pdf->getDomPDF()->set_option("enable_php", true);
      $pdf->setPaper('A4', 'landscape');

      $pdf->loadView('auto_approve.report.tanda_tangan', array(
        'file' => $appr_sends,
        'isi' => $appr_approvals
      ));

      $pdf->save(public_path() . "/adagio/ttd/ADG".$no_transaction.".pdf");

      $user = db::select('select employee_id, es.`name`, u.email, es.position from employee_syncs as es left join users as u on u.username = es.employee_id where end_date is null and u.id = "'.$appr_sends->created_by.'"');

      $aplicant = ApprApprovals::where('request_id', $no_transaction)
      ->select('approver_email')
      ->get();

      $data = [
        'appr_sends' => $appr_sends,
        'appr_approvals' => $appr_approvals,
        'header' => 'Rejected'
      ];

      if (($user[0]->position == 'Sub Leader') || ($user[0]->position == 'Leader')) {
        Mail::to($aplicant[0]->approver_email)
        ->bcc(['ympi-mis-ML@music.yamaha.com'])
        ->send(new SendEmail($data, 'send_email_done'));
      }else{
        Mail::to($user[0]->email)
        ->bcc(['ympi-mis-ML@music.yamaha.com'])
        ->send(new SendEmail($data, 'send_email_done'));
      }

      $response = array(
        'status'        => true
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

  public function AdagioViewHold($no_transaction){
    try{
      $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
      ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan', 'reason', 'comment', 'answer')
      ->first();

      $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
      ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
      ->get();

      $data = [
        'appr_sends' => $appr_sends,
        'appr_approvals' => $appr_approvals
      ];

      return view('auto_approve.done', array(
        'title'    => 'MIRAI Approval System', 
        'title_jp' => 'MIRAI 承認システム',
        'data'     => $appr_sends

      ))->with('page', 'MIRAI Approval System');
    }
    catch(\Exception $e){
      $response = array(
        'status' => false,
        'message' => $e->getMessage(),
      );
      return Response::json($response);
    }
  }

  public function AdagioViewAplicantHold($no_transaction){
    try{
      $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
      ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan', 'reason', 'comment', 'answer')
      ->first();

      $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
      ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
      ->get();

      $data = [
        'appr_sends' => $appr_sends,
        'appr_approvals' => $appr_approvals
      ];

      return view('auto_approve.view_hold', array(
        'title'    => 'MIRAI Approval System', 
        'title_jp' => 'MIRAI 承認システム',
        'data'     => $appr_sends

      ))->with('page', 'MIRAI Approval System');
    }
    catch(\Exception $e){
      $response = array(
        'status' => false,
        'message' => $e->getMessage(),
      );
      return Response::json($response);
    }
  }

  public function AdagioIndexTanggapan(Request $request, $no_approval){
    try{
      $appr_sends = ApprSend::where('no_transaction', $no_approval)
      ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan', 'reason', 'comment', 'answer')
      ->first();

      $appr_approvals = ApprApprovals::where('request_id', $no_approval)
      ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
      ->get();

      $data = [
        'appr_sends' => $appr_sends,
        'appr_approvals' => $appr_approvals
      ];

      return view('auto_approve.index_tanggapan', array(
        'title'    => 'MIRAI Approval System', 
        'title_jp' => 'MIRAI 承認システム',
        'data' => $data

      ))->with('page', 'MIRAI Approval System');
    }
    catch(\Exception $e){
      $response = array(
        'status' => false,
        'message' => $e->getMessage(),
      );
      return Response::json($response);
    }
  }

  public function SimpanTanggapan(Request $request){
    $no_approval = $request->get('no_approval');
    $file = $request->file('file_gambar');

    $file_name = 'ADG'.$no_approval.'.pdf';
    $file->move('adagio', $file_name);

    $update_remark = db::table('appr_sends')
    ->where('no_transaction', $no_approval)
    ->update([
      'remark' => 'Open',
      'updated_at' => date('Y-m-d H:i:s')
    ]);

    $update_approver = db::table('appr_approvals')
    ->where('request_id', $no_approval)
    ->where('status', '=', 'Hold & Comment')
    ->update([
      'status' => null,
      'approved_at' => null
    ]);

    $data = ApprSend::where('no_transaction', $no_approval)->first();

    $file = ApprSend::where('no_transaction', '=', $no_approval)
    ->select('no_transaction', 'judul', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'created_by', 'created_at', 'jd_japan')
    ->first();

    $isi = ApprApprovals::where('request_id', '=', $no_approval)
    ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
    ->get();

    $pdf = \App::make('dompdf.wrapper');
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper('A4', 'potrait');

    $pdf->loadView('auto_approve.report.tanda_tangan', array(
      'isi' => $isi,
      'file' => $file
    ));

    $pdf->save(public_path() . "/adagio/ttd/ADG".$data->no_transaction.".pdf");
    $this->AdagioSendEmail($no_approval);

    return redirect('/index/mirai/approval')->with('status', 'Dokumen Berhasil Di Upload')
    ->with('page', 'Mirai Approval');
  }

  public function AdagioSendPost(Request $request, $no_transaction){
    try{
      $n_dok = $request->get('n_dok');
      $j_dok = $request->get('j_dok');
      $summary = $request->get('summary');
      $answer = $request->get('answer');

      $end = ApprSend::where('no_transaction', '=', $no_transaction)->update([
        'remark' => 'Open',
        'description' => $n_dok,
        'judul' => $j_dok,
        'summary' => $summary,
        'answer' => $answer
      ]);

      $p = ApprApprovals::where('request_id', '=', $no_transaction)
      ->where('status', '=', 'Hold & Comment')
      ->update([
        'status' => null,
        'approved_at' => null
      ]);

      $appr_sends = ApprSend::where('no_transaction', '=', $no_transaction)
      ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan', 'reason', 'comment', 'answer')
      ->first();

      $appr_approvals = ApprApprovals::where('request_id', '=', $no_transaction)
      ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
      ->get();

      $pdf = \App::make('dompdf.wrapper');
      $pdf->getDomPDF()->set_option("enable_php", true);
      $pdf->setPaper('A4', 'landscape');

      $pdf->loadView('auto_approve.report.tanda_tangan', array(
        'file' => $appr_sends,
        'isi' => $appr_approvals
      ));

      $pdf->save(public_path() . "/adagio/ttd/ADG".$no_transaction.".pdf");

    // $user = User::where('id', '=', $appr_sends->created_by)
    // ->select('email')
    // ->first();

      $data = [
        'appr_sends' => $appr_sends,
        'appr_approvals' => $appr_approvals
      ];

      $email_next = ApprApprovals::where('request_id', '=', $no_transaction)->wherenull('status')->select('approver_email')->first();
      Mail::to($email_next->approver_email)
      ->bcc(['ympi-mis-ML@music.yamaha.com'])
      ->send(new SendEmail($data, 'send_email'));

    // Mail::to($user->email)
    // ->bcc(['ympi-mis-ML@music.yamaha.com'])
    // ->send(new SendEmail($data, 'send_email_done'));

      return view('auto_approve.done', array(
        'title'    => 'MIRAI Approval System', 
        'title_jp' => 'MIRAI 承認システム',
        'data'     => $appr_sends

      ))->with('page', 'MIRAI Approval System');
    }
    catch(\Exception $e){
      $response = array(
        'status' => false,
        'message' => $e->getMessage(),
      );
      return Response::json($response);
    }
  }

  public function DoneConfimation($no_transaction){
    $data = ApprSend::where('no_transaction', '=', $no_transaction)->first();

    return view('auto_approve.done', array(
      'title'    => 'MIRAI Approval System', 
      'title_jp' => 'MIRAI 承認システム',
      'data'     => $data
                // 'resumes' => $resumes,
                // 'req_id' => $req_id
    ))->with('page', 'MIRAI Approval System');
  }

  public function AdagioVerivikasi(Request $request, $id){     
    try{
      $file = ApprSend::find($id);
      $now = date('Y-m-d H-y-s');
      $isi = "select appr_sends.id, appr_sends.no_transaction, appr_sends.category, appr_sends.nik, appr_sends.department, appr_sends.description, appr_sends.date, appr_sends.summary, appr_sends.file, appr_sends.file_pdf, appr_sends.created_by, appr_sends.created_at, appr_sends.deleted_at, appr_sends.updated_at, appr_sends.approve1, appr_sends.approve2, appr_sends.approve3, appr_sends.approve4, appr_sends.approve5, appr_sends.approve6, appr_sends.approve7, appr_sends.approve8, appr_sends.approve9, appr_sends.approve10, appr_sends.date1, appr_sends.date2, appr_sends.date3, appr_sends.date4, appr_sends.date5, appr_sends.date6, appr_sends.date7, appr_sends.date8, appr_sends.date9, appr_sends.date10, appr_sends.remark, appr_sends.date_reject FROM appr_sends where appr_sends.id = ".$file->id;

      if (($file->approve1 != null) && ($file->date1 == null)) {
        if ($file->approve2 != null) {
          $file->date1 = $now.'/Approved';
          $file->save();

          $user = explode("/", $file->approve2);
          $mails = "select distinct email from users where users.username = '".$user[0]."'";
          $mailtoo = DB::select($mails);

          $isimail = $isi;
          $file_send = db::select($isimail);

          Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email'));
        }else{
          $file->date1 = $now.'/Approved';
          $file->remark = 'All Approved';
          $file->save();

          $user = explode("/", $file->nik);
          $mails = "select distinct email from users where users.username = '".$user[0]."'";
          $mailtoo = DB::select($mails);

          $isimail = $isi;
          $file_send = db::select($isimail);

          Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email_done'));
        }
      }
      else if(($file->approve2 != null) && ($file->date2 == null)){
        if ($file->approve3 != null) {
          $file->date2 = $now.'/Approved';
          $file->save();

          $user = explode("/", $file->approve3);
          $mails = "select distinct email from users where users.username = '".$user[0]."'";
          $mailtoo = DB::select($mails);

          $isimail = $isi;
          $file_send = db::select($isimail);

          Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email'));
        }else{
          $file->date2 = $now.'/Approved';
          $file->remark = 'All Approved';
          $file->save();

          $user = explode("/", $file->nik);
          $mails = "select distinct email from users where users.username = '".$user[0]."'";
          $mailtoo = DB::select($mails);

          $isimail = $isi;
          $file_send = db::select($isimail);

          Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email_done'));
        }
      }
      else if(($file->approve3 != null) && ($file->date3 == null)){
        if ($file->approve4 != null) {
          $file->date3 = $now.'/Approved';
          $file->save();

          $user = explode("/", $file->approve4);
          $mails = "select distinct email from users where users.username = '".$user[0]."'";
          $mailtoo = DB::select($mails);

          $isimail = $isi;
          $file_send = db::select($isimail);

          Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email'));
        }else{
          $file->date3 = $now.'/Approved';
          $file->remark = 'All Approved';
          $file->save();

          $user = explode("/", $file->nik);
          $mails = "select distinct email from users where users.username = '".$user[0]."'";
          $mailtoo = DB::select($mails);

          $isimail = $isi;
          $file_send = db::select($isimail);

          Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email_done'));
        }
      }
      else if(($file->approve4 != null) && ($file->date4 == null)){
        if ($file->approve5 != null) {
          $file->date4 = $now.'/Approved';
          $file->save();

          $user = explode("/", $file->approve5);
          $mails = "select distinct email from users where users.username = '".$user[0]."'";
          $mailtoo = DB::select($mails);

          $isimail = $isi;
          $file_send = db::select($isimail);

          Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email'));
        }else{
          $file->date4 = $now.'/Approved';
          $file->remark = 'All Approved';
          $file->save();

          $user = explode("/", $file->nik);
          $mails = "select distinct email from users where users.username = '".$user[0]."'";
          $mailtoo = DB::select($mails);

          $isimail = $isi;
          $file_send = db::select($isimail);

          Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email_done'));
        }
      }
      else if(($file->approve5 != null) && ($file->date5 == null)){
        if ($file->approve6 != null) {
          $file->date5 = $now.'/Approved';
          $file->save();

          $user = explode("/", $file->approve6);
          $mails = "select distinct email from users where users.username = '".$user[0]."'";
          $mailtoo = DB::select($mails);

          $isimail = $isi;
          $file_send = db::select($isimail);

          Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email'));
        }else{
          $file->date5 = $now.'/Approved';
          $file->remark = 'All Approved';
          $file->save();

          $user = explode("/", $file->nik);
          $mails = "select distinct email from users where users.username = '".$user[0]."'";
          $mailtoo = DB::select($mails);

          $isimail = $isi;
          $file_send = db::select($isimail);

          Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email_done'));
        }
      }
      else if(($file->approve6 != null) && ($file->date6 == null)){
        if ($file->approve7 != null) {
          $file->date6 = $now.'/Approved';
          $file->save();

          $user = explode("/", $file->approve6);
          $mails = "select distinct email from users where users.username = '".$user[0]."'";
          $mailtoo = DB::select($mails);

          $isimail = $isi;
          $file_send = db::select($isimail);

          Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email'));
        }else{
          $file->date6 = $now.'/Approved';
          $file->remark = 'All Approved';
          $file->save();

          $user = explode("/", $file->nik);
          $mails = "select distinct email from users where users.username = '".$user[0]."'";
          $mailtoo = DB::select($mails);

          $isimail = $isi;
          $file_send = db::select($isimail);

          Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email_done'));
        }
      }
      else if(($file->approve7 != null) && ($file->date7 == null)){
        if ($file->approve8 != null) {
          $file->date7 = $now.'/Approved';
          $file->save();

          $user = explode("/", $file->approve6);
          $mails = "select distinct email from users where users.username = '".$user[0]."'";
          $mailtoo = DB::select($mails);

          $isimail = $isi;
          $file_send = db::select($isimail);

          Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email'));
        }else{
          $file->date7 = $now.'/Approved';
          $file->remark = 'All Approved';
          $file->save();

          $user = explode("/", $file->nik);
          $mails = "select distinct email from users where users.username = '".$user[0]."'";
          $mailtoo = DB::select($mails);

          $isimail = $isi;
          $file_send = db::select($isimail);

          Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email_done'));
        }
      }
      else if(($file->approve8 != null) && ($file->date8 == null)){
        if ($file->approve9 != null) {
          $file->date8 = $now.'/Approved';
          $file->save();

          $user = explode("/", $file->approve6);
          $mails = "select distinct email from users where users.username = '".$user[0]."'";
          $mailtoo = DB::select($mails);

          $isimail = $isi;
          $file_send = db::select($isimail);

          Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email'));
        }else{
          $file->date8 = $now.'/Approved';
          $file->remark = 'All Approved';
          $file->save();

          $user = explode("/", $file->nik);
          $mails = "select distinct email from users where users.username = '".$user[0]."'";
          $mailtoo = DB::select($mails);

          $isimail = $isi;
          $file_send = db::select($isimail);

          Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email_done'));
        }
      }
      return redirect('/adagio/detail/'.$id)->with('status', 'Crete file approval success')->with('page', 'File Approve');
    }
    catch (QueryException $e){
      return back()->with('error', 'Error')->with('page', 'File Approve');
    }
  }

    //reject
  public function AdagioReject(Request $request, $id){     
    try{
      $file = ApprSend::find($id);
      $now = date('Y-m-d H-y-s');
      $isi = "select appr_sends.id, appr_sends.no_transaction, appr_sends.category, appr_sends.nik, appr_sends.department, appr_sends.description, appr_sends.date, appr_sends.summary, appr_sends.file, appr_sends.file_pdf, appr_sends.created_by, appr_sends.created_at, appr_sends.deleted_at, appr_sends.updated_at, appr_sends.approve1, appr_sends.approve2, appr_sends.approve3, appr_sends.approve4, appr_sends.approve5, appr_sends.approve6, appr_sends.approve7, appr_sends.approve8, appr_sends.approve9, appr_sends.approve10, appr_sends.date1, appr_sends.date2, appr_sends.date3, appr_sends.date4, appr_sends.date5, appr_sends.date6, appr_sends.date7, appr_sends.date8, appr_sends.date9, appr_sends.date10, appr_sends.remark, appr_sends.date_reject FROM appr_sends where appr_sends.id = ".$file->id;

      if ($file->date1 == null) {
        $file->date1 = $now.'/Rejected';
        $file->remark = 'Rejected';
        $file->date_reject = $now;
        $file->save();

        $user = explode("/", $file->nik);
        $mails = "select distinct email from users where users.username = '".$user[0]."'";
        $mailtoo = DB::select($mails);

        $isimail = $isi;
        $file_send = db::select($isimail);

        Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email'));
      }else if ($file->date2 == null) {
        $file->date2 = $now.'/Rejected';
        $file->remark = 'Rejected';
        $file->date_reject = $now;
        $file->save();

        $user = explode("/", $file->nik);
        $mails = "select distinct email from users where users.username = '".$user[0]."'";
        $mailtoo = DB::select($mails);

        $isimail = $isi;
        $file_send = db::select($isimail);

        Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email'));
      }else if ($file->date3 == null) {
        $file->date3 = $now.'/Rejected';
        $file->remark = 'Rejected';
        $file->date_reject = $now;
        $file->save();

        $user = explode("/", $file->nik);
        $mails = "select distinct email from users where users.username = '".$user[0]."'";
        $mailtoo = DB::select($mails);

        $isimail = $isi;
        $file_send = db::select($isimail);

        Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email'));
      }else if ($file->date4 == null) {
        $file->date4 = $now.'/Rejected';
        $file->remark = 'Rejected';
        $file->date_reject = $now;
        $file->save();

        $user = explode("/", $file->nik);
        $mails = "select distinct email from users where users.username = '".$user[0]."'";
        $mailtoo = DB::select($mails);

        $isimail = $isi;
        $file_send = db::select($isimail);

        Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email'));
      }else if ($file->date5 == null) {
        $file->date5 = $now.'/Rejected';
        $file->remark = 'Rejected';
        $file->date_reject = $now;
        $file->save();

        $user = explode("/", $file->nik);
        $mails = "select distinct email from users where users.username = '".$user[0]."'";
        $mailtoo = DB::select($mails);

        $isimail = $isi;
        $file_send = db::select($isimail);

        Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email'));
      }else if ($file->date6 == null) {
        $file->date6 = $now.'/Rejected';
        $file->remark = 'Rejected';
        $file->date_reject = $now;
        $file->save();

        $user = explode("/", $file->nik);
        $mails = "select distinct email from users where users.username = '".$user[0]."'";
        $mailtoo = DB::select($mails);

        $isimail = $isi;
        $file_send = db::select($isimail);

        Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email'));
      }else if ($file->date7 == null) {
        $file->date7 = $now.'/Rejected';
        $file->remark = 'Rejected';
        $file->date_reject = $now;
        $file->save();

        $user = explode("/", $file->nik);
        $mails = "select distinct email from users where users.username = '".$user[0]."'";
        $mailtoo = DB::select($mails);

        $isimail = $isi;
        $file_send = db::select($isimail);

        Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email'));
      }else if ($file->date8 == null) {
        $file->date8 = $now.'/Rejected';
        $file->remark = 'Rejected';
        $file->date_reject = $now;
        $file->save();

        $user = explode("/", $file->nik);
        $mails = "select distinct email from users where users.username = '".$user[0]."'";
        $mailtoo = DB::select($mails);

        $isimail = $isi;
        $file_send = db::select($isimail);

        Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email'));
      }else if ($file->date9 == null) {
        $file->date9 = $now.'/Rejected';
        $file->remark = 'Rejected';
        $file->date_reject = $now;
        $file->save();

        $user = explode("/", $file->nik);
        $mails = "select distinct email from users where users.username = '".$user[0]."'";
        $mailtoo = DB::select($mails);

        $isimail = $isi;
        $file_send = db::select($isimail);

        Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email'));
      }else if ($file->date10) {
        $file->date10 = $now.'/Rejected';
        $file->remark = 'Rejected';
        $file->date_reject = $now;
        $file->save();

        $user = explode("/", $file->nik);
        $mails = "select distinct email from users where users.username = '".$user[0]."'";
        $mailtoo = DB::select($mails);

        $isimail = $isi;
        $file_send = db::select($isimail);

        Mail::to($mailtoo)->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($file_send, 'send_email'));
      }
      return redirect('/adagio/detail'.$id)->with('status', 'Crete file approval success')->with('page', 'File Approve');
    }
    catch (QueryException $e){
      return back()->with('error', 'Error')->with('page', 'File Approve');
    }
  }

  public function AdagiReport(Request $request, $id){
    $file = ApprSend::find($id);

    $detail = ApprSend::select('appr_sends.id', 'appr_sends.aplication', 'appr_sends.no_transaction', 'appr_sends.category', 'appr_sends.nik', 'appr_sends.department', 'appr_sends.description', 'appr_sends.date', 'appr_sends.summary', 'appr_sends.file', 'appr_sends.approve1', 'appr_sends.date1', 'appr_sends.approve2', 'appr_sends.date2', 'appr_sends.approve3', 'appr_sends.date3', 'appr_sends.approve4', 'appr_sends.date4', 'appr_sends.approve5', 'appr_sends.date5', 'appr_sends.approve6', 'appr_sends.date6', 'appr_sends.approve7', 'appr_sends.date7','appr_sends.approve8', 'appr_sends.date8', 'appr_sends.approve9', 'appr_sends.date9', 'appr_sends.approve10', 'appr_sends.date10', 'remark')
    ->where('appr_sends.id', '=', $id)
    ->first();

    $pdf = \App::make('dompdf.wrapper');
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper('A4', 'potrait');

    $pdf->loadView('auto_approve.report.pdf', array(
      'title' => 'MIRAI Approval System', 
      'title_jp' => 'MIRAI 承認システム',

      'detail' => $detail
    ));

    $path = '/adagio/' . $detail->file; 
    return $pdf->stream("MIRAI Approval System");
  }

  public function DetailFile(Request $request, $id){

    $emp = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('employee_id', 'department')
    ->first();

    $file = ApprSend::find($id);

    $resumes = ApprSend::select('id', 'judul', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary')
    ->where('appr_sends.id', '=', $id )
    ->get();

    $approvers = ApprApprovals::where('request_id', '=', $resumes[0]->no_transaction)->wherenull('status')->first();  

    $path = '/adagio/' . $file->file;            
    $file_path = asset($path);


    return view('auto_approve.detail', array(
      'title' => 'MIRAI Approval System', 
      'title_jp' => 'MIRAI 承認システム',
      'file' => $file,
      'resumes' => $resumes,
      'file_path' => $file_path,
      'emp' => $emp,
      'approvers' => $approvers
    ))->with('page', 'Detail');
  }

  public function deleteFile(Request $request){
    try{
      $file = ApprSend::where('id', '=', $request->get('id'))->first();

      ApprSend::where('no_transaction', '=', $file->no_transaction)->delete();
      ApprApprovals::where('request_id', '=', $file->no_transaction)->delete();

      $response = array(
        'status' => true,
        'message' => 'Pengajuan File berhasil di delete',
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

  public function TestView(Request $request)
  {  
    return view('auto_approve.test',  
      array(
        'title' => 'MIRAI Approval System', 
        'title_jp' => 'ミューテーション1部門の監視と制御'
      )
    )->with('page', 'File Approval');
  }

  public function getNotifMiraiApproval()
  {
    if (Auth::user() !== null) {
      $user = strtoupper(Auth::user()->username);
      $name = Auth::user()->name;
      $role = Auth::user()->role_code;
      $notif = 0;

      $tanggungan_notif = db::select('SELECT DISTINCT
        a.no_transaction,
        ( SELECT COALESCE ( b.approver_name ) FROM appr_approvals b WHERE b.request_id = a.no_transaction AND STATUS IS NULL ORDER BY id ASC LIMIT 1 ) AS tanggungan 
        FROM
        (
          SELECT DISTINCT
          no_transaction
          FROM
          appr_sends
          LEFT JOIN appr_approvals ON appr_sends.no_transaction = appr_approvals.request_id
          LEFT JOIN departments ON appr_sends.department = departments.department_name
          LEFT JOIN users ON appr_sends.created_by = users.id 
          WHERE
          appr_sends.remark = "Open" UNION ALL
          SELECT
          request_id
          FROM
          appr_approvals
          LEFT JOIN appr_sends ON appr_approvals.request_id = appr_sends.no_transaction
          LEFT JOIN departments ON appr_sends.department = departments.department_name
          LEFT JOIN users ON appr_sends.created_by = users.id 
          WHERE
          `status` IS NULL 
          GROUP BY
          request_id
          ) AS a 
        WHERE ( SELECT COALESCE ( b.approver_id ) FROM appr_approvals b WHERE b.request_id = a.no_transaction AND b.`status` IS NULL LIMIT 1 ) = "'.$user.'" 
        GROUP BY
        a.no_transaction');

      $notif = count($tanggungan_notif);
      return $notif;
    }
  }

// public function CekKirimEmail(Request $request){  
//   try{
//     $penerima = db::select('SELECT
//       distinct
//       request_id
//       FROM
//       appr_approvals 
//       WHERE
//       `status` IS NULL
//       GROUP BY
//       request_id
//       ORDER BY
//       id ASC');

//     for ($i=0; $i < count($penerima); $i++) { 
//       $mail_to = [];
//       $select = ApprApprovals::where('request_id', '=', $penerima[$i]->request_id)->wherenull('status')->take(1)->first();
//       array_push($mail_to, $select->approver_email);

//       $appr_sends = ApprSend::where('no_transaction', '=', $penerima[$i]->request_id)
//       ->select('id', 'no_transaction', 'judul', 'category', 'no_dokumen', 'nik', 'department', 'description', 'date', 'file', 'file_pdf', 'created_by', 'created_at', 'deleted_at', 'updated_at', 'remark', 'summary', 'jd_japan')
//       ->first();

//       $appr_approvals = ApprApprovals::where('request_id', '=', $penerima[$i]->request_id)
//       ->select('request_id', 'approver_id', 'approver_name', 'status', 'approved_at', 'remark', 'header')
//       ->get();

//       $data = [
//         'appr_sends' => $appr_sends,
//         'appr_approvals' => $appr_approvals
//       ];

//       Mail::to($mail_to)
//       ->bcc(['ympi-mis-ML@music.yamaha.com'])
//       ->send(new SendEmail($data, 'send_email'));
//     }

//     $response = array(
//       'status' => true,
//       'message' => 'Pengajuan File berhasil di delete',
//     );
//     return Response::json($response);
//   }
//   catch(\Exception $e){
//     $response = array(
//       'status' => false,
//       'message' => $e->getMessage(),
//     );
//     return Response::json($response);
//   }
// }
  public function IndexKategori(Request $request)
  {   
    $user = db::select("SELECT employee_id, name, position FROM employee_syncs where end_date is null and position in ('Foreman', 'Chief', 'Coordinator', 'Senior Coordinator', 'Manager', 'General Manager', 'Director', 'President Director', 'Staff', 'Junior Staff', 'Senior Staff', 'Deputy General Manager', 'Manager Japanese Specialist', 'Specialist') order by name ASC");

    return view('auto_approve.index_kategori',  
      array(
        'title' => ' Edit Kategori', 
        'title_jp' => 'カテゴリを編集',
        'user' => $user
      )
    )->with('page', 'File Approval');
  }

  public function FetchKategori(Request $request){
    try{
      $id = Auth::user()->id;
      $judul = $request->get('judul');
      $data = db::select('SELECT judul, GROUP_CONCAT(COALESCE( `user`, " " ) ORDER BY urut ASC) as `user`, created_by from
        (SELECT *, CAST(SUBSTRING_INDEX( urutan, "_", - 1 ) AS UNSIGNED) as urut from appr_masters where created_by = "'.$id.'") as mstr
          GROUP BY judul, created_by');

        $list = db::select('SELECT id, `user`, SUBSTRING_INDEX( urutan, "_", - 1 ) AS urutan FROM appr_masters WHERE judul = "'.$judul.'" AND created_by = "'.$id.'" ORDER BY CAST(SUBSTRING_INDEX( urutan, "_", - 1 ) AS UNSIGNED) ASC');

        $response = array(
          'status' => true,
          'data' => $data,
          'list' => $list
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

    public function DeleteKategori(Request $request){
      try{
        $jenis = $request->get('jenis');
        $judul = $request->get('judul');
        $created_by = $request->get('created_by');
        $id = $request->get('id');
        $delete = '';

        if ($jenis == 'Hapus Kategori') {
          $delete = db::delete('DELETE FROM appr_masters WHERE judul = "'.$judul.'" and created_by = "'.$created_by.'"');
        }else{
          $auth = Auth::user()->id;
          $delete = db::delete('DELETE FROM appr_masters WHERE id = "'.$id.'"');
          $cek = db::select('select id from appr_masters where judul = "'.$judul.'" and created_by = "'.$auth.'"');

          for ($i=0; $i < count($cek) ; $i++) { 
            $update_urutan = db::table('appr_masters')
            ->where('id', $cek[$i]->id)
            ->update([
              'urutan' => 'Approval_'.($i+1),
              'updated_at' => date('Y-m-d H:i:s')
            ]);
          }
        }

        $response = array(
          'status' => true,
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

    public function AddListKategori(Request $request){
      try{
        $id = Auth::user()->id;
        $judul = $request->get('judul');
        $user = $request->get('user');
        $header = $request->get('header');

        $appr_master = db::select('select * from appr_masters where judul = "'.$judul.'" and created_by = "'.$id.'" order by id desc limit 1');
        $urutan = explode('_', $appr_master[0]->urutan);
        $q = $urutan[1]+1;

        $insert = db::table('appr_masters')
        ->insert([
         'department' => $appr_master[0]->department,
         'judul' => $judul,
         'jd_japan' => $appr_master[0]->jd_japan,
         'user' => $user.'/'.$header,
         'urutan' => 'Approval_'.$q,
         'created_by' => $id,
         'created_at' => date('Y-m-d H:i:s'),
         'updated_at' => date('Y-m-d H:i:s')
       ]);

        $cek = db::select('select id from appr_masters where judul = "'.$judul.'" and created_by = "'.$id.'"');

        for ($i=0; $i < count($cek) ; $i++) { 
          $update_urutan = db::table('appr_masters')
          ->where('id', $cek[$i]->id)
          ->update([
            'urutan' => 'Approval_'.($i+1),
            'updated_at' => date('Y-m-d H:i:s')
          ]);
        }

        $response = array(
          'status' => true,
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

    public function MovePosition(Request $request){
      try{
        $id_user = Auth::user()->id;
        $id = $request->get('id');
        $judul = $request->get('judul');

        if ($request->get('jenis') == 'Naikkan') {
          $select = db::select('select id, urutan from appr_masters where id = "'.$id.'"');
          $urutan = explode('_', $select[0]->urutan);
          $q = $urutan[1]-1;
          $w = $q+1;

          $up = db::table('appr_masters')
          ->where('judul', $judul)
          ->where('created_by', $id_user)
          ->where('urutan', '=', 'Approval_'.$q)
          ->update([
            'urutan' => 'Approval_'.$w,
            'updated_at' => date('Y-m-d H:i:s')
          ]);

          $update_urutan = db::table('appr_masters')
          ->where('id', $id)
          ->update([
            'urutan' => 'Approval_'.$q,
            'updated_at' => date('Y-m-d H:i:s')
          ]);
        }else{
          $select = db::select('select id, urutan from appr_masters where id = "'.$id.'"');
          $urutan = explode('_', $select[0]->urutan);
          $q = $urutan[1]+1;
          $w = $q-1;

          $up = db::table('appr_masters')
          ->where('judul', $judul)
          ->where('created_by', $id_user)
          ->where('urutan', '=', 'Approval_'.$q)
          ->update([
            'urutan' => 'Approval_'.$w,
            'updated_at' => date('Y-m-d H:i:s')
          ]);

          $update_urutan = db::table('appr_masters')
          ->where('id', $id)
          ->update([
            'urutan' => 'Approval_'.$q,
            'updated_at' => date('Y-m-d H:i:s')
          ]);
        }

        $response = array(
          'status' => true,
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

    public function SimpanJudul(Request $request){
      try{
        $id_user = Auth::user()->id;
        $judul_before = $request->get('judul_before');
        $judul_after = $request->get('judul_after');

        $update_judul = db::table('appr_masters')
        ->where('judul', $judul_before)
        ->where('created_by', $id_user)
        ->update([
          'judul' => $judul_after,
          'updated_at' => date('Y-m-d H:i:s')
        ]);

        $response = array(
          'status' => true
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

    function CekUrutanKategori(Request $request){
      try{
        $emp_id = $request->get('emp_id');
        $emp_name = $request->get('emp_name');
        $data = db::table('employee_syncs')->where('employee_id', $emp_id)->first();
        $user = '';

        if ($data->position == 'President Director') {
          $response = array(
            'status' => false,
            'message' => 'Tidak Bisa Menambah Approval, Karena Secara Struktur Organisasi Sudah Paling Tinggi.'
          );
          return Response::json($response);
        }else{
          if ($data->position == 'Staff' || $data->position == 'Senior Staff') {
            $user = db::select("SELECT employee_id, name, position FROM employee_syncs where end_date is null and position in ('Foreman', 'Chief', 'Coordinator', 'Senior Coordinator', 'Manager', 'General Manager', 'Director', 'President Director', 'Staff', 'Junior Staff', 'Senior Staff', 'Deputy General Manager', 'Manager Japanese Specialist', 'Specialist') order by name ASC");
          }else if ($data->position == 'Foreman' || $data->position == 'Chief' || $data->position == 'Coordinator' || $data->position == 'Senior Coordinator') {
            $user = db::select("SELECT employee_id, name, position FROM employee_syncs where end_date is null and position in ('Manager', 'General Manager', 'Director', 'President Director', 'Deputy General Manager', 'Manager Japanese Specialist', 'Specialist', 'Foreman', 'Chief', 'Coordinator', 'Senior Coordinator') order by name ASC");
          }else if ($data->position == 'Manager') {
            $user = db::select("SELECT employee_id, name, position FROM employee_syncs where end_date is null and position in ('General Manager', 'Director', 'President Director', 'Deputy General Manager', 'Manager Japanese Specialist', 'Specialist', 'Manager') order by name ASC");
          }else if ($data->position == 'Deputy General Manager') {
            $user = db::select("SELECT employee_id, name, position FROM employee_syncs where end_date is null and position in ('General Manager', 'Director', 'President Director', 'Manager Japanese Specialist', 'Specialist', 'Deputy General Manager') order by name ASC");
          }else if ($data->position == 'General Manager') {
            $user = db::select("SELECT employee_id, name, position FROM employee_syncs where end_date is null and position in ('Director', 'President Director','Manager Japanese Specialist', 'Specialist', 'General Manager') order by name ASC");
          }else if ($data->position == 'Director') {
            $user = db::select("SELECT employee_id, name, position FROM employee_syncs where end_date is null and position in ('President Director','Manager Japanese Specialist', 'Specialist') order by name ASC");
          }

          $response = array(
            'status' => true,
            'user' => $user
          );
          return Response::json($response);
        }
      }
      catch(\Exception $e){
        $response = array(
          'status' => false,
          'message' => 'Pilih Approval Selanjutnya.',
        );
        return Response::json($response);
      }
    }

    //------------------------ Winds ----------------------
    public function WindsDetail(Request $request)
    {   
      return view('auto_approve.winds.detail',  
        array(
          'title' => 'MIRAI Approval System', 
          'title_jp' => 'ミューテーション1部門の監視と制御',
          'approvals' => $approvals
        )
      )->with('page', 'File Approval');
    }
  }
