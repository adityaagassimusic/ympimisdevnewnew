<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use PDF;
use Excel;
use DateTime;
use App\QcCpar;
use App\QcCar;
use App\QcCparItem;
use App\QcVerifikasi;
use App\Department;
use App\Employee;
use App\EmployeeSync;
use App\Material;
use App\MaterialQaComplaint;
use App\Status;
use App\WeeklyCalendar;
use App\Destination;
use App\Vendor;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use File;
use App\QcTtdCoba;
use App\UserActivityLog;


class QcReportController extends Controller
{
    public function __construct()
    {
        if (isset($_SERVER['HTTP_USER_AGENT']))
        {
            $http_user_agent = $_SERVER['HTTP_USER_AGENT']; 
            if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) 
            {
                // Prevent MS office products detecting the upcoming re-direct .. forces them to launch the browser to this link
                die();
            }
        }      
      $this->middleware('auth');
      $this->chief = 'PI0311001';
      $this->manager = 'PI9710001';
      $this->dgm = 'PI0109004';
      $this->gm = 'PI1206001';
    }

    public function index()
    {	
        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

        $cpars = QcCpar::select('qc_cpars.*','departments.department_name','employees.name','statuses.status_name')
        ->join('departments','qc_cpars.department_id','=','departments.id')
        ->join('employees','qc_cpars.employee_id','=','employees.employee_id')
        ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
        ->orderBy('qc_cpars.id','DESC')
        ->get();
        
        $departments = Department::select('departments.id', 'departments.department_name')->get();

        // $materials = Material::select('materials.material_number', 'materials.material_description')->get();
        $statuses = QcCpar::select('statuses.status_code','statuses.status_name')
        ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
        ->distinct()
        ->get();
        return view('qc_report.index', array(
            'cpars' => $cpars,
            'departments' => $departments,
            'statuses' => $statuses
        ))->with('page', 'CPAR');
    }

    function filter_cpar(Request $request)
    {
        $cpar_detailsTable = DB::table('qc_cpars')
        ->leftjoin('departments','qc_cpars.department_id','=','departments.id')
        ->leftjoin('employees','qc_cpars.employee_id','=','employees.employee_id')
        ->leftjoin('employees as staff','qc_cpars.staff','=','staff.employee_id')
        ->leftjoin('employees as leader','qc_cpars.leader','=','leader.employee_id')
        ->leftjoin('statuses','qc_cpars.status_code','=','statuses.status_code')
        ->leftjoin('qc_cars','qc_cpars.cpar_no','=','qc_cars.cpar_no')
        ->select('qc_cpars.id','qc_cpars.cpar_no','qc_cpars.kategori','qc_cpars.kategori_komplain', 'staff.name as staffname', 'leader.name as leadername', 'employees.name', 'qc_cpars.lokasi', 'qc_cpars.tgl_permintaan', 'qc_cpars.tgl_balas', 'qc_cpars.via_komplain', 'qc_cpars.judul_komplain', 'qc_cpars.judul_komplain', 'qc_cpars.email_status', 'departments.department_shortname', 'qc_cpars.sumber_komplain', 'qc_cpars.status_code', 'statuses.status_name', 'qc_cpars.created_at','qc_cpars.kategori_meeting', 'qc_cars.id as id_car')
        ->whereNull('qc_cpars.deleted_at');

        if(strlen($request->get('bulandari')) > 0){

          if(strlen($request->get('bulanke')) > 0){
            $bulandari = $request->get('bulandari');
            $bulanke = $request->get('bulanke');

            $cpar_detailsTable = $cpar_detailsTable->whereBetween('qc_cpars.tgl_permintaan',[$bulandari.'-01',$bulanke.'-31']);
          
          }
          
          else{
            $bulandari = $request->get('bulandari');
            $tgl = date('qc_cpars.tgl_permintaan');
            //DateTime::createFromFormat('Y-m', 'qc_cpars.tgl_permintaan')
            // $date_request = date('Y-m-d', strtotime($request->get('bulandari')));
            $cpar_detailsTable = $cpar_detailsTable->where('qc_cpars.tgl_permintaan', 'like','%'.$bulandari.'%');
          }
        }

        if(strlen($request->get('kategori')) > 0){
          $cpar_detailsTable = $cpar_detailsTable->where('qc_cpars.kategori', '=', $request->get('kategori'));
        }

        if(strlen($request->get('department_id')) > 0){
          $cpar_detailsTable = $cpar_detailsTable->where('qc_cpars.department_id', '=', $request->get('department_id'));
        }
        
        if(strlen($request->get('status_code')) > 0){
          $cpar_detailsTable = $cpar_detailsTable->where('qc_cpars.status_code', '=', $request->get('status_code'));
        }
        
        $cpar_detailsTable = $cpar_detailsTable->orderBy('qc_cpars.id', 'DESC');
        $cpar_details = $cpar_detailsTable->get();

        return DataTables::of($cpar_details)

        ->addColumn('penemu',function($cpar_details){
          if ($cpar_details->staffname != null) {
            $fl = $cpar_details->staffname;
          }
          else{
            $fl = $cpar_details->leadername;
          }

          return $fl;
        })

        ->editColumn('tgl_permintaan',function($cpar_details){
            return date('d-M-Y', strtotime($cpar_details->tgl_permintaan));
          })

         ->editColumn('kategori',function($cpar_details){
            return $cpar_details->kategori .' - '.$cpar_details->sumber_komplain .' - '.$cpar_details->kategori_komplain;
          })


        ->editColumn('status_name',function($cpar_details){

            $meeting = "";

            if($cpar_details->status_name == "Unverified CPAR") {
              if ($cpar_details->kategori_meeting == null || $cpar_details->kategori_meeting == '-') {
                $meeting = "";
              }else{
                $meeting = "";
              }

              return '<label class="label label-danger">'.$cpar_details->status_name. '</label> '.$meeting.'';
            }
            // else if($cpar_details->status_name == "QA Verification"){
            //   $meeting = "Meeting Closed";
              
            //   return '<label class="label label-primary">'.$cpar_details->status_name. '</label> '.$meeting.'';
            // }
            // else if($cpar_details->status_name == "Close"){
            //   $meeting = "Meeting Closed";

            //   return '<label class="label label-success">'.$cpar_details->status_name. '</label> '.$meeting.'';
            // }
            else if($cpar_details->status_name == "Unverified CAR"){
              if ($cpar_details->kategori_meeting == null || $cpar_details->kategori_meeting == '-') {
                $meeting = "Belum Meeting";
              }
              else if($cpar_details->kategori_meeting == 'Open'){
                $meeting = "<span style='color:red'>Sudah Meeting (Open)</span>";
              }
              else if($cpar_details->kategori_meeting == 'Close'){
                $meeting = "<span style='color:green'>Sudah Meeting (Closed)</span>";
              }
              else if($cpar_details->kategori_meeting == 'CloseRevised'){
                $meeting = "<span style='color:#f57f17'>Closed Dengan Revisi</span>";
              }

              return '<label class="label label-warning">'.$cpar_details->status_name. '</label> '.$meeting.'';
            }
            else{
               if ($cpar_details->kategori_meeting == null || $cpar_details->kategori_meeting == '-') {
                $meeting = "Belum Meeting";
              }
              else if($cpar_details->kategori_meeting == 'Open'){
                $meeting = "<span style='color:red'>Sudah Meeting (Open)</span>";
              }
              else if($cpar_details->kategori_meeting == 'Close'){
                $meeting = "<span style='color:green'>Sudah Meeting (Closed)</span>";
              }
              else if($cpar_details->kategori_meeting == 'CloseRevised'){
                $meeting = "<span style='color:#f57f17'>Closed Dengan Revisi</span>";
              }

              return '<label class="label label-warning">'.$cpar_details->status_name. '</label> '.$meeting.'';
            }
          })


        ->addColumn('action', function($cpar_details){
          $idcpar = $cpar_details->id;
          $idcar = $cpar_details->id_car;
          $no_cpar = $cpar_details->cpar_no;
          
          // if($cpar_details->email_status != "Sent") {
          //     return '<a href="qc_report/update/'.$idcpar.'" class="btn btn-primary btn-xs">Detail</a>
          //         <a href="javascript:void(0)" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('.$idcpar.');">Delete</a>
          //         <a href="qc_report/sendemail/'.$idcpar.'" class="btn btn-warning btn-xs">Send Email</a>';
          // }

          if($cpar_details->status_name == "Unverified CAR"){
            return '<a href="qc_report/update/'.$idcpar.'" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Detail CPAR</a>
                    <a href="qc_car/detail/'.$idcar.'" class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i> Detail CAR</a>
                    <a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-danger" class="btn btn-primary btn-sm" onClick="editMeeting('.$idcpar.')"><i class="fa fa-file"></i> Kategori Meeting</a>
                    ';
          }

          // <a href="javascript:void(0)" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('.$idcpar.');">Delete</a>

          else if($cpar_details->status_name == "QA Verification" || $cpar_details->status_name == "Closed"){
            return '<a href="qc_report/print_cpar/'.$idcpar.'" class="btn btn-success btn-sm"><i class="fa fa-file-pdf-o"></i> Report CPAR</a>
                    <a href="qc_car/print_car_new/'.$idcar.'" class="btn btn-success btn-sm"><i class="fa fa-file-pdf-o"></i> Report CAR</a><br>
                    <a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-danger" class="btn btn-primary btn-sm" onClick="editMeeting('.$idcpar.')"><i class="fa fa-file"></i> Kategori Meeting</a>
                    ';
          }

          else{
            return '<a href="qc_report/update/'.$idcpar.'" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Detail</a>';
          }

          // <a href="javascript:void(0)" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation('.$idcpar.');">Delete</a>

        })

        ->rawColumns(['penemu' => 'penemu','status_name' => 'status_name','action' => 'action','verif' => 'verif'])
        ->make(true);
    }

    public function get_meeting(Request $request)
    {
        $cpar = QcCpar::find($request->get('id'));

        $response = array(
            'status' => true,
            'cpar' => $cpar
        );
        return Response::json($response);
    }

    public function edit_meeting(Request $request)
    {
        try{


            $meet = QcCpar::find($request->get("id"));
          
            if ($request->get('meeting') == "Close") {

                $car_isi = QcCar::where('cpar_no',$meet->cpar_no)->first();

                if ($car_isi->deskripsi == null || $car_isi->tindakan == null || $car_isi->penyebab == null || $car_isi->perbaikan == null) {
                   $response = array(
                      'status' => false,
                      'message' => "Lengkapi Dulu CAR nya",
                  );
                  return Response::json($response);
                }

                $cpar = QcCpar::select('qc_cpars.cpar_no','qc_cpars.id','qc_cpars.status_code','qc_cpars.posisi')
                 ->join('qc_cars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
                 ->where('qc_cpars.cpar_no','=',$meet->cpar_no)
                 ->get();

                foreach ($cpar as $cpar) {
                    $cpar->status_code = "7";
                    $cpar->posisi = "QA";
                    $cpar->save();
                }

                $cars = QcCar::where('cpar_no',$meet->cpar_no)
                ->update([
                    'email_status' => 'SentQA',
                    'email_send_date' =>  date('Y-m-d'),
                    'posisi' => 'qa',
                    'checked_chief' => 'Checked',
                    'checked_foreman' => 'Checked',
                    'checked_coordinator' => 'Checked',
                    'checked_manager' => 'Checked',
                    'approved_dgm' => 'Checked',
                    'approved_gm' => 'Checked'
                ]);
            }

            $meet->kategori_meeting = $request->get('meeting');
            $meet->notulen_meeting = $request->get('notulen');
            $meet->save();


            $response = array(
              'status' => true,
              'datas' => "Berhasil",
          );
            return Response::json($response);
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
               $response = array(
                  'status' => false,
                  'datas' => "CPAR Already Exist",
              );
               return Response::json($response);
           }
           else{
               $response = array(
                  'status' => false,
                  'datas' => "Update CPAR Error.",
              );
               return Response::json($response);
           }
       }
   }

    public function create()
    {
        $managers = EmployeeSync::select('employee_syncs.employee_id','employee_syncs.name','employee_syncs.position','employee_syncs.department')
        ->join('departments','departments.department_name','=','employee_syncs.department')
        ->whereNotIn('departments.id',['1','2','3','4','11','13'])
        ->where('position','manager')
        ->distinct()
        ->get();

        $productions = Department::select('departments.*')
        ->join('divisions','departments.id_division','=','divisions.id')
        ->where('id_division','=','5')
        ->whereNotIn('departments.id',['10','11','14'])
        ->get();

        $procurements = Department::select('departments.*')
        ->where('id','=','7')
        ->get();

        $others = Department::select('departments.*')
        ->join('divisions','departments.id_division','=','divisions.id')
        ->where('id_division','<>','5')
        ->whereNotIn('departments.id',['1','2','3','4','7'])
        ->get();

        // foreach ($managers as $managers) {
        //   $dept = $man
        // }

        $destinations = Destination::select('destinations.*')->get();

        $vendors = "select id, vendor, name from vendors where vendor != 'Y31504'";
        $vendor = DB::select($vendors);

        return view('qc_report.create', array(
            'managers' => $managers,
            'productions'  => $productions,
            'procurements' => $procurements,
            'others' =>  $others,
            'destinations' => $destinations,
            'vendors' => $vendor
        ))->with('page', 'CPAR');
    }

    public function create_action(request $request)
    {
      try{
          
          $files=array();
          $file = new QcCpar();
          if ($request->file('files') != NULL) {
            // var_dump($request->file('files'));die();
            if($files=$request->file('files')) {
              foreach($files as $file){
                $nama=$file->getClientOriginalName();
                $file->move('files',$nama);
                $data[]=$nama;              
              }
            }            
            $file->filename=json_encode($data);           
          }
          else {
            $file->filename=NULL;
          }

          // $file = $request->file('file');
          // if ($file != NULL) {
          //   $tujuan_upload = 'files';
          //   $file->move($tujuan_upload,$file->getClientOriginalName());
          //   $file = $file->getClientOriginalName();            
          // }
          // else{
          //   $file == "";
          // }

          $id_user = Auth::id();
          $tgl_permintaan = $request->get('tgl_permintaan');
          $tgl_balas = $request->get('tgl_balas');

          $date_permintaan = str_replace('/', '-', $tgl_permintaan);
          $date_balas = str_replace('/', '-', $tgl_balas);

          if ($request->get('kategori') == "Internal") {

            $ceklogin = "select position from employee_syncs where employee_id='".Auth::user()->username."'";
            $loginstaff = DB::select($ceklogin);

            if ($loginstaff != null) {
              if ($loginstaff[0]->position == "Staff" || $loginstaff[0]->position == "Senior Staff") {
                $staff = null;
                $leader = Auth::user()->username;
                $posisi = "leader";
                $chief = null;
                $foreman = $this->chief;
              }
              else{
                $staff = null;
                $leader = Auth::user()->username;
                $posisi = "leader";
                $chief = null;

                $getforeman = "select users.username from employee_syncs join users on users.username = employee_syncs.employee_id where position = 'foreman' and department = 'Quality Assurance Department'";

                $fore = DB::select($getforeman);
                
                if ($fore != null) {
                  foreach ($fore as $for) {
                    $hasilforeman = $for->username;
                  }

                  $foreman = $hasilforeman; 
                  // $foreman = $this->chief;               
                }
                else{
                  $foreman = null;
                } 
              }             
            }
            else
            {
              $staff = null;
              $leader = Auth::user()->username;
              $posisi = "leader";
              $chief = null;

              $getforeman = "select users.username from employee_syncs join users on users.username = employee_syncs.employee_id where position = 'foreman' and department = 'Quality Assurance Department' ";
              $fore = DB::select($getforeman);
              
              if ($fore != null) {
                foreach ($fore as $for) {
                  $hasilforeman = $for->username;
                }

                $foreman = $hasilforeman;
                // $foreman = $this->chief;                
              }
              else{
                $foreman = null;
              }                
            }
          } else {
              $staff = Auth::user()->username;
              $leader = null;
              $foreman = null;
              $posisi = "staff";
              $chief = $this->chief;
          }


          $manager_id = explode("_", $request->get('employee_id'));

          $cpars = new QcCpar([
            'cpar_no' => $request->get('cpar_no'),
            'kategori' => $request->get('kategori'),
            'employee_id' => $manager_id[0],
            'lokasi' => $request->get('lokasi'),
            'department_id' => $request->get('department_id'),
            'tgl_permintaan' => date("Y-m-d", strtotime($date_permintaan)),
            'tgl_balas' => date("Y-m-d", strtotime($date_balas)),
            'judul_komplain' => $request->get('judul_komplain'),
            'kategori_komplain' => $request->get('kategori_komplain'),
            'file' => $file->filename,
            'via_komplain' => $request->get('via_komplain'),
            'sumber_komplain' => $request->get('sumber_komplain'),
            'destination_code' => $request->get('customer'),
            'vendor' => $request->get('supplier'),
            'penemu_ng' => $request->get('penemu'),
            'staff' => $staff,
            'leader' => $leader,
            'chief' => $chief,
            'foreman' => $foreman,
            'manager' => $this->manager,
            'dgm' => $this->dgm,
            'gm' => $this->gm,
            'posisi' => $posisi,
            'status_code' => '5',
            'progress' => '20',
            'kategori_ng' => $request->get('kategori_ng'),
            'kategori_approval' => $request->get('approval_cpar'),
            'created_by' => $id_user
          ]);

          // $cpar_detailsTable = DB::table('qc_cpars')->where('qc_cpars.id');
          // $id = QcCpar::select('qc_cpars.id')

          $cpars->save();

          return redirect('/index/qc_report/update/'.$cpars->id)
          ->with('status', 'New CPAR has been created.')
          ->with('page', 'CPAR List');
      }
      catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'CPAR already exist.')->with('page', 'CPAR');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'CPAR');
            }
        }
    }

    public function update($id)
    {

        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

        $managers = EmployeeSync::select('employee_syncs.employee_id','employee_syncs.name','employee_syncs.position','employee_syncs.department')
        ->join('departments','departments.department_name','=','employee_syncs.department')
        ->whereNotIn('departments.id',['1','2','3','4','11','13','14'])
        ->where('position','manager')
        ->distinct()
        ->get();

        $productions = Department::select('departments.*')
        ->get();
        $cpars = QcCpar::find($id);

        $destinations = Destination::select('destinations.*')->get();

        $vendors = "select id, vendor, name from vendors where vendor != 'Y31504'";
        $vendor = DB::select($vendors);

        $materials = MaterialQaComplaint::select('material_qa_complaints.id','material_qa_complaints.material_number','material_qa_complaints.material_description')
        ->orderBy('material_qa_complaints.id','ASC')
        ->get();

        // $materials = Material::select('materials.id','materials.material_number','materials.material_description')
        // ->orderBy('materials.id','ASC')
        // ->get();

        return view('qc_report.edit', array(
            'cpars' => $cpars,
            'managers' => $managers,
            'productions'  => $productions,
            'destinations' => $destinations,
            'vendors' => $vendor,
            'materials' =>  $materials
        ))->with('page', 'CPAR');
    }

    public function update_action(Request $request, $id)
    {
          try{

            $tgl_permintaan = $request->get('tgl_permintaan');
            $tgl_balas = $request->get('tgl_balas');

            $date_permintaan = str_replace('/', '-', $tgl_permintaan);
            $date_balas = str_replace('/', '-', $tgl_balas);

            $cpars = QcCpar::find($id);

            $files=array();
            
            // $file = new QcCpar();
            if ($request->file('files') != NULL) {
              if($files=$request->file('files')) {
                foreach($files as $file){
                  $nama=$file->getClientOriginalName();
                  $file->move('files',$nama);
                  $data[]=$nama;              
                }
              }

              $cpars->file=json_encode($data);           
            }

            // var_dump($cpars->filename);die();
            // $file = $request->file('file');

            // if($file != NULL){
            //     $tujuan_upload = 'files';
            //     $file->move($tujuan_upload,$file->getClientOriginalName());
            //     $cpars->file = $file->getClientOriginalName();
            // }
            
            $cpars->cpar_no = $request->get('cpar_no');
            $cpars->employee_id = $request->get('employee_id');
            $cpars->lokasi = $request->get('lokasi');
            $cpars->department_id = $request->get('department_id');
            $cpars->tgl_permintaan = date('Y-m-d', strtotime($date_permintaan));
            $cpars->tgl_balas = date("Y-m-d", strtotime($date_balas));
            $cpars->judul_komplain = $request->get('judul_komplain');
            $cpars->kategori_komplain = $request->get('kategori_komplain');
            $cpars->via_komplain = $request->get('via_komplain');
            $cpars->sumber_komplain = $request->get('sumber_komplain');
            $cpars->destination_code = $request->get('customer');
            $cpars->vendor = $request->get('supplier');
            $cpars->penemu_ng = $request->get('penemu');

            $cpars->save();
            return redirect('/index/qc_report/update/'.$cpars->id)->with('status', 'CPAR data has been updated.')->with('page', 'CPAR');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'CPAR already exist.')->with('page', 'CPAR');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'CPAR');
            }
          }
    }

    public function update_deskripsi(Request $request, $id)
    {
          try{
            $cpars = QcCpar::find($id);
            $cpars->tindakan = $request->get('action');
            $cpars->save();
            return redirect('/index/qc_report/update/'.$cpars->id)->with('status', 'Action data has been updated.')->with('page', 'CPAR');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'CPAR already exist.')->with('page', 'CPAR');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'CPAR');
            }
          }
    }

    public function delete($id)
    {
        $cpars = QcCpar::find($id);
        $cpars->delete();

        return redirect('/index/qc_report')
        ->with('status', 'CPAR has been deleted.')
        ->with('page', 'CPAR');
    }

    //part item

    public function fetch_item($id)
    {
        $cpars = QcCpar::find($id);

        $qc_cpar_items = QcCparItem::leftJoin("qc_cpars","qc_cpar_items.cpar_no","=","qc_cpars.cpar_no")
        ->select('qc_cpar_items.*')
        ->where('qc_cpar_items.cpar_no','=',$cpars->cpar_no)
        ->get();

        return DataTables::of($qc_cpar_items)

          ->editColumn('detail_problem',function($qc_cpar_items){
            return $qc_cpar_items->detail_problem;
          })

          ->editColumn('defect_presentase',function($qc_cpar_items){
            return $qc_cpar_items->defect_presentase. ' %';
          })
          
          ->addColumn('action', function($qc_cpar_items){
            return '
            
            <button class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit" onclick="modalEdit('.$qc_cpar_items->id.')">Edit</button>
            <button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete('.$qc_cpar_items->id.',\''.$qc_cpar_items->cpar_no.'\')">Delete</button>';
          })

      ->rawColumns(['detail_problem' => 'detail_problem','action' => 'action'])
      ->make(true);
    }

    public function create_item(Request $request)
    {
        try
        {
            $id_user = Auth::id();

            $parts = new QcCparItem([
                'cpar_no' => $request->get('cpar_no'),
                'part_item' => $request->get('part_item'),
                'no_invoice' => $request->get('no_invoice'),
                'lot_qty' => $request->get('lot_qty'),
                'sample_qty' => $request->get('sample_qty'),
                'detail_problem' => $request->get('detail_problem'),
                'defect_qty' => $request->get('defect_qty'),
                'defect_presentase' => $request->get('defect_presentase'),
                'created_by' => $id_user
            ]);

            $parts->save();

            $response = array(
              'status' => true,
              'parts' => $parts
            );
            return Response::json($response);
        }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'parts' => "Material already exist"
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'parts' => "Material not created."
            );
             return Response::json($response);
           }
        }
    }

    public function fetch_item_edit(Request $request)
    {
      $qc_cpar_items = QcCparItem::find($request->get("id"));

      $response = array(
        'status' => true,
        'datas' => $qc_cpar_items,
      );
      return Response::json($response);
    }

    public function edit_item(Request $request)
    {
        try{
            $qc_cpar_items = QcCparItem::find($request->get("id"));
            $qc_cpar_items->part_item = $request->get('part_item');
            $qc_cpar_items->no_invoice = $request->get('no_invoice');
            $qc_cpar_items->lot_qty = $request->get('lot_qty');
            $qc_cpar_items->sample_qty = $request->get('sample_qty');
            $qc_cpar_items->detail_problem = $request->get('detail_problem');
            $qc_cpar_items->defect_qty = $request->get('defect_qty');
            $qc_cpar_items->defect_presentase = $request->get('defect_presentase');
            $qc_cpar_items->save();

            $response = array(
              'status' => true,
              'datas' => "Berhasil",
            );
            return Response::json($response);

          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'datas' => "Material already exist",
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => "Update Material Error.",
            );
             return Response::json($response);
            }
        }
    }

    public function view_item(Request $request)
    {
      $query = "select qc_cpar_items.cpar_no, qc_cpar_items.part_item, qc_cpar_items.no_invoice, qc_cpar_items.lot_qty, qc_cpar_items.sample_qty, qc_cpar_items.detail_problem, qc_cpar_items.defect_qty, qc_cpar_items.defect_presentase, qc_cpar_items.created_at, qc_cpar_items.updated_at from qc_cpar_items 
        left join qc_cpars on qc_cpar_items.cpar_no = qc_cpars.cpar_no
        where qc_cpar_items.id = ".$request->get('id')."";

      $qc_cpar_items = DB::select($query);

      $response = array(
        'status' => true,
        'datas' => $qc_cpar_items
      );
      return Response::json($response);
    }

    public function delete_item(Request $request)
    {
      $qc_cpars_items = QcCparItem::find($request->get("id"));
      $qc_cpars_items->forceDelete();

      $response = array(
        'status' => true
      );
      return Response::json($response);
    }

    public function get_fiscal(Request $request)
    {
      $datenow = date('Y-m-d');
      $query = "SELECT fiscal_year FROM `weekly_calendars` where week_date = '$datenow'";
      $tahun = DB::select($query);
      foreach ($tahun as $year) {
        $html = $year->fiscal_year;
      }

      return json_encode($html);
    }

    public function getDepartemen(Request $request)
    {
        $html = array();

        $manag = explode("_", $request->manager);

        $manager = Department::select('departments.id','departments.department_name')->where('department_name',$manag[1])->get();
        
        foreach ($manager as $mn)
        {
            $html = array(
                'department' => $mn->department_name,
                'id_department' => $mn->id,
            );
        }

        return json_encode($html);
    }

    public function get_nomor_depan(Request $request)
    {
      $datenow = date('Y-m-d');
      $nomor = '';
      $kategori = $request->kategori;

      $query = "SELECT * FROM `qc_cpars` where kategori = '$kategori' ORDER BY id DESC LIMIT 1";
      $nomordepan = DB::select($query);
      

      $query2 = "SELECT fiscal_year FROM `weekly_calendars` where week_date = '$datenow'";
      $tahun = DB::select($query2);

      foreach ($tahun as $year) {
        $result['year'] = $year->fiscal_year;
      }

      foreach ($nomordepan as $nomors) {
        // $nomor = $nomors->cpar_no;
        $result['nomor'] = $nomors->cpar_no;
      }
      return json_encode($result);
    }

    //grafik CPAR

    public function grafik_cpar(){
      
      $fys = db::select("select DISTINCT fiscal_year from weekly_calendars");
      $bulan = db::select("select DISTINCT MONTH(tgl_permintaan) as bulan, MONTHNAME(tgl_permintaan) as namabulan FROM qc_cpars order by bulan asc;");
      $tahun = db::select("select DISTINCT YEAR(tgl_permintaan) as tahun FROM qc_cpars order by tahun desc");
      $sumber = db::select("select DISTINCT kategori_komplain from qc_cpars where kategori='Eksternal'");
      $sumber_komplain = db::select("select DISTINCT sumber_komplain from qc_cpars");
      $dept = db::select("select id, department_name from departments where departments.id not in (1,2,3,4,11)");
      $statuses = db::select("select distinct qc_cpars.status_code, status_name from statuses join qc_cpars on qc_cpars.status_code = statuses.status_code");
      
       return view('qc_report.grafik',  
        array('title' => 'CPAR CAR Monitoring', 
              'title_jp' => '是正防止処置対策管理',
              'fys' => $fys,
              'bulans' => $bulan,
              'years' => $tahun, 
              'departemens' => $dept,
              'status' => $statuses,
              'sumber' => $sumber,
              'sumber_komplain' => $sumber_komplain
            )
        )->with('page', 'CPAR Graph');
    }

    public function grafik_kategori(){
      $fys = db::select("select DISTINCT fiscal_year from weekly_calendars");
      $tahun = db::select("select DISTINCT YEAR(tgl_permintaan) as tahun FROM qc_cpars order by tahun desc");
      
       return view('qc_report.grafik_kategori',  
        array('title' => 'Report CPAR By Kategori', 
              'title_jp' => '種類別の是正処置報告書',
              'fys' => $fys,
              'years' => $tahun, 
            )
        )->with('page', 'CPAR');
    }

    public function fetchReport(Request $request)
    {

      $tahun = date('Y');
      $tglfrom = $request->get('tglfrom');
      $tglto = $request->get('tglto');

      if ($tglfrom == "") {
          $tglfrom = date('Y-m', strtotime(carbon::now()->subMonth(11)));
      }

      if ($tglto == "") {
          $tglto = date('Y-m', strtotime(carbon::now()));
      }
      // $files=array();

      $kategori = $request->get('kategori');

      if ($kategori != null) {
          $cat = json_encode($kategori);
          $kat = str_replace(array("[","]"),array("(",")"),$cat);

          $kate = 'and qc_cpars.kategori_komplain in'.$kat;
      }else{
          $kate = '';
      }

      $departemen = $request->get('departemen');

      if ($departemen != null) {
          $deptt = json_encode($departemen);
          $dept = str_replace(array("[","]"),array("(",")"),$deptt);

          $dep = 'and qc_cpars.department_id in'.$dept;
      } else {
          $dep = '';
      }

      // $status = $request->get('status');

      // if ($status != null) {
      //     $statt = json_encode($status);
      //     $stat = str_replace(array("[","]"),array("(",")"),$statt);

      //     $sta = 'and qc_cpars.status_code in'.$stat;
      // } else {
      //     $sta = '';
      // }

      $sumber_komplain = $request->get('sumber_komplain');

      if ($sumber_komplain != null) {
          $skkk = json_encode($sumber_komplain);
          $skk = str_replace(array("[","]"),array("(",")"),$skkk);

          $sk = 'and qc_cpars.sumber_komplain in'.$skk;
      } else {
          $sk = '';
      }

      $sumber = $request->get('sumber');

      if ($sumber != null) {
          $sumbb = json_encode($sumber);
          $sumb = str_replace(array("[","]"),array("(",")"),$sumbb);

          $sum = 'and qc_cpars.kategori_komplain in '.$sumb;
      } else {
          $sum = '';
      }      

      $approval_cpar = $request->get('approval_cpar');

      if ($approval_cpar != null) {
          $apprr = json_encode($approval_cpar);
          $appr = str_replace(array("[","]"),array("(",")"),$apprr);

          $app = 'and qc_cpars.kategori_approval in'.$appr;
      } else {
          $app = '';
      }

      $meeting = $request->get('meeting');

      if ($meeting != null) {
          $meett = json_encode($meeting);
          $meet = str_replace(array("[","]"),array("(",")"),$meett);

          $met = 'and qc_cpars.kategori_meeting in'.$meet;
      } else {
          $met = '';
      }

      $data = db::select("select count(cpar_no) as jumlah, monthname(tgl_permintaan) as bulan, year(tgl_permintaan) as tahun, sum(case when qc_cpars.status_code = '5' then 1 else 0 end) as UnverifiedCPAR, sum( CASE WHEN qc_cpars.status_code = '6' and (kategori_meeting is null || kategori_meeting = 'Close' || kategori_meeting = 'Open') THEN 1 ELSE 0 END ) AS UnverifiedCAR, sum( CASE WHEN qc_cpars.status_code = '6' and kategori_meeting = 'CloseRevised' THEN 1 ELSE 0 END ) AS RevisedDocument, sum(case when qc_cpars.status_code = '7' then 1 else 0 end) as qaverification, sum(case when qc_cpars.status_code = '1' then 1 else 0 end) as close from qc_cpars LEFT JOIN statuses on statuses.status_code = qc_cpars.status_code where qc_cpars.deleted_at is null and DATE_FORMAT(tgl_permintaan,'%Y-%m') between '".$tglfrom."' and '".$tglto."' ".$kate." ".$dep." ".$sk." ".$sum." ".$app." ".$met." GROUP BY bulan,tahun order by tahun, month(tgl_permintaan) ASC");

      // $tahun = date('Y');
      // $monthTitle = date("Y", strtotime($bulan));

      $response = array(
        'status' => true,
        'datas' => $data,
        'tahun' => $tahun,
        'tglfrom' => $tglfrom,
        'tglto' => $tglto,
        'kategori' =>  $kate,
        'departemen' => $dep,
        'sumber' => $sk
      );

      return Response::json($response); 
    }



    public function fetchKategori(Request $request)
    {   

      $tglnow = date('Y-m-d');
      // $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$tglnow'");

      // foreach ($fy as $fys) {
      //     $fiscal = $fys->fiscal_year;
      // }

      // $kategori = $request->get('kategori');

      // if ($kategori != null) {
      //     $cat = json_encode($kategori);
      //     $kat = str_replace(array("[","]"),array("(",")"),$cat);

      //     $kate = 'and kategori in'.$kat;
      // }else{
      //     $kate = '';
      // }

      // $departemen = $request->get('departemen');

      // if ($departemen != null) {
      //     $deptt = json_encode($departemen);
      //     $dept = str_replace(array("[","]"),array("(",")"),$deptt);

      //     $dep = 'and department_id in'.$dept;
      // }else{
      //     $dep = '';
      // }

      $fy = $request->get('fy');

      if ($fy != null) {
          $f = 'and fiscal_year = "'.$fy.'"';
          $fiscal = $fy;
      }else{
          $f = null;
          $fiscal = null;
      }       

      $data = db::select("select count(cpar_no) as jumlah, kategori, sum(case when qc_cpars.status_code = '5' then 1 else 0 end) as UnverifiedCPAR, sum(case when qc_cpars.status_code = '6' then 1 else 0 end) as UnverifiedCAR, sum(case when qc_cpars.status_code = '7' then 1 else 0 end) as qaverification, sum(case when qc_cpars.status_code = '1' then 1 else 0 end) as close from qc_cpars LEFT JOIN statuses on statuses.status_code = qc_cpars.status_code LEFT JOIN weekly_calendars on qc_cpars.tgl_permintaan = week_date where qc_cpars.deleted_at is null ".$f." GROUP BY kategori order by fiscal_year,jumlah ASC");

      $eksternal = db::select("select count(cpar_no) as jumlah, kategori, qc_cpars.destination_code, fiscal_year, destination_shortname from qc_cpars LEFT JOIN statuses on statuses.status_code = qc_cpars.status_code LEFT JOIN weekly_calendars on qc_cpars.tgl_permintaan = week_date join destinations on qc_cpars.destination_code = destinations.destination_code where qc_cpars.deleted_at is null ".$f." GROUP BY destination_code,kategori,fiscal_year,destination_shortname order by fiscal_year, jumlah DESC");

      $response = array(
        'status' => true,
        'datas' => $data,
        'fiscal' => $fiscal,
        // 'kategori' =>  $kate,
        // 'departemen' => $dep,
        'eksternal' => $eksternal
      );

      return Response::json($response); 
    }

    public function grafik_tanggungan(){
      // $fys = db::select("select DISTINCT fiscal_year from weekly_calendars");
      // $tahun = db::select("select DISTINCT YEAR(tgl_permintaan) as tahun FROM qc_cpars order by tahun desc");
      
       return view('qc_report.grafik_tanggungan',  
        array(
            'title' => 'Monitoring CPAR By PIC', 
            'title_jp' => ''
        ))->with('page', 'CPAR');
    }

    public function fetch_tanggungan(Request $request)
    {   
      $cpar = db::select("select cpar_no, posisi, staff, leader, chief, foreman, manager, dgm, gm from qc_cpars where status_code = '5' and deleted_at is null");

      $car = db::select("
            SELECT
                qc_cpars.cpar_no,
                qc_cars.posisi,
                qc_cpars.kategori_meeting,
                qc_cars.pic,
                qc_cpars.department_id,
                departments.department_name,
                qc_cpars.employee_id,
                qc_cpars.dgm,
                qc_cpars.gm,
                qc_verifikators.verifikatorchief,
                qc_verifikators.verifikatorforeman,
                qc_verifikators.verifikatorcoordinator
            FROM
                qc_cars
                JOIN qc_cpars 
                on qc_cpars.cpar_no = qc_cars.cpar_no
                JOIN departments 
                on qc_cpars.department_id = departments.id
                JOIN qc_verifikators
                ON qc_cpars.department_id = qc_verifikators.department_id
            WHERE
                qc_cpars.deleted_at IS NULL
        ");

        $verifikasi_qa = db::select("select cpar_no, posisi, staff, leader, chief, foreman, manager,kategori_meeting from qc_cpars where status_code = '7' and deleted_at is null");

        $emp = db::select("Select * from employee_syncs where end_date is null");


      $response = array(
        'status' => true,
        'cpar' => $cpar,
        'car' => $car,
        'verifikasi_qa' => $verifikasi_qa,
        'emp' => $emp
      );

      return Response::json($response); 
    }

    public function fetchEksternal(Request $request)
    {   

      $tglnow = date('Y-m-d');

      $fy = $request->get('fy');
      
      if ($fy != null) {
          $f = 'and fiscal_year = "'.$fy.'"';
          $fiscal = $fy;
      }else{
          $f = null;
          $fiscal = null;
      } 

      $data = db::select("select alias as kategori_komplain, count(cpar_no) as jumlah from qc_cpars LEFT JOIN weekly_calendars on qc_cpars.tgl_permintaan = week_date RIGHT JOIN qc_m_types on qc_cpars.kategori_komplain = qc_m_types.nama_master where qc_cpars.deleted_at is null and qc_m_types.kategori = 'Eksternal'  ".$f." GROUP BY nama_master,alias order by jumlah DESC");

      $response = array(
        'status' => true,
        'datas' => $data,
        'fiscal' => $fiscal,
      );

      return Response::json($response); 
    }

    public function fetchSupplier(Request $request)
    {   

      $tglnow = date('Y-m-d');

      $fy = $request->get('fy');
      
      if ($fy != null) {
          $f = 'and fiscal_year = "'.$fy.'"';
          $fiscal = $fy;
      }else{
          $f = null;
          $fiscal = null;
      } 

      $data = db::select("select kategori_komplain, count(cpar_no) as jumlah from qc_cpars LEFT JOIN weekly_calendars on qc_cpars.tgl_permintaan = week_date where qc_cpars.deleted_at is null and qc_cpars.kategori = 'Supplier' and kategori_komplain is not null ".$f." GROUP BY kategori_komplain 
        UNION 
        select 'YMMJ', count(*) as jumlah from qc_ymmjs 
        order by jumlah DESC");

      $response = array(
        'status' => true,
        'datas' => $data,
        'fiscal' => $fiscal,
      );

      return Response::json($response); 
    }

    public function fetchSource(Request $request)
    {   

      $tglnow = date('Y-m-d');

      $fy = $request->get('fy');
      
      if ($fy != null) {
          $f = 'and fiscal_year = "'.$fy.'"';
          $fiscal = $fy;
      }else{
          $f = null;
          $fiscal = null;
      } 


      // $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$tglnow'");

      // foreach ($fy as $fys) {
      //     $fiscal = $fys->fiscal_year;
      // }

      if ($fy != null) {
        $data = db::select("select kategori_komplain, count(cpar_no) as jumlah from qc_cpars LEFT JOIN weekly_calendars on qc_cpars.tgl_permintaan = week_date where qc_cpars.deleted_at is null and kategori_komplain is not null ".$f." GROUP BY kategori_komplain order by jumlah DESC");
      }
      else{

        $data = db::select("select kategori_komplain, count(cpar_no) as jumlah from qc_cpars LEFT JOIN weekly_calendars on qc_cpars.tgl_permintaan = week_date where qc_cpars.deleted_at is null and kategori_komplain is not null ".$f." GROUP BY kategori_komplain 
          UNION 
          select 'YMMJ', count(*) as jumlah from qc_ymmjs 
          order by jumlah DESC");
      }


      // $ymmj = db::select("select count(*) from qc_ymmjs");

      $response = array(
        'status' => true,
        'datas' => $data,
        'fiscal' => $fiscal,
      );

      return Response::json($response); 
    }

    public function detail_cpar(Request $request){

      $bulan = $request->get("bulan");
      $status = $request->get("status");
      $tglfrom = $request->get("tglfrom");
      $tglto = $request->get("status");
      $kategori = $request->get("kategori");
      $departemen = $request->get("departemen");
      // $sumber = $request->get("sumber");
      $tahun = $request->get("tahun");

      // if ($status == "Open") {
      //     $status = "and (statuses.status_name = 'Unverified CPAR' or statuses.status_name = 'Unverified CAR')";
      // }else{
      //     $status = "and statuses.status_name = '".$status."'";
      // }


      if ($status == "Unverified CAR") {
          $status = "and statuses.status_name = 'Unverified CAR' and (kategori_meeting is null || kategori_meeting = 'Close' || kategori_meeting = 'Open') ";
      } else if ($status == "Revised Document CAR") {
          $status = "and statuses.status_name = 'Unverified CAR' and kategori_meeting = 'CloseRevised' ";
      } else{
          $status = "and statuses.status_name = '".$status."'";
      }

      $query = "select qc_cpars.*,monthname(tgl_permintaan) as bulan,departments.department_name,staff.name as staffname,leader.name as leadername,employees.name,chief.name as chiefname, foreman.name as foremanname, manager.name as managername, dgm.name as dgmname, gm.name as gmname, statuses.status_name, qc_cars.id as id_car, qc_cars.pic, pic.name as picname, qc_cars.posisi as posisi_car, qc_cars.email_status as email_status_car, chiefcar.name as chiefcarname, foremancar.name as foremancarname, coordinatorcar.name as coordinatorcarname, qc_cars.checked_chief as checked_chief_car, qc_cars.checked_foreman as checked_foreman_car, qc_cars.checked_manager as checked_manager_car, qc_cars.approved_dgm as approved_dgm_car, qc_cars.approved_gm as approved_gm_car,destinations.destination_shortname FROM qc_cpars join departments on departments.id = qc_cpars.department_id left join destinations on qc_cpars.destination_code = destinations.destination_code join employees on qc_cpars.employee_id = employees.employee_id join statuses on qc_cpars.status_code = statuses.status_code left join employees as staff on qc_cpars.staff = staff.employee_id left join employees as leader on qc_cpars.leader = leader.employee_id left join employees as chief on qc_cpars.chief = chief.employee_id left join employees as foreman on qc_cpars.foreman = foreman.employee_id left join employees as manager on qc_cpars.manager = manager.employee_id left join employees as dgm on qc_cpars.dgm = dgm.employee_id left join employees as gm on qc_cpars.gm = gm.employee_id left join qc_cars on qc_cars.cpar_no = qc_cpars.cpar_no join qc_verifikators on qc_cpars.department_id = qc_verifikators.department_id left join employees as chiefcar on qc_verifikators.verifikatorchief = chiefcar.employee_id left join employees as foremancar on qc_verifikators.verifikatorforeman = foremancar.employee_id left join employees as coordinatorcar on qc_verifikators.verifikatorcoordinator = coordinatorcar.employee_id left join employees as pic on qc_cars.pic = pic.employee_id where qc_cpars.deleted_at is null and year(tgl_permintaan) = '".$tahun."' and monthname(tgl_permintaan) = '".$bulan."' ".$status." and DATE_FORMAT(tgl_permintaan,'%Y-%m') between '".$tglfrom."' and '".$tglto."'".$kategori." ".$departemen." ";

      $detail = db::select($query);

      return DataTables::of($detail)

        ->addColumn('verif', function($detail){
          if($detail->status_name != "Closed") {
            if ($detail->kategori == "Eksternal") {
              if ($detail->status_code == "5") { // CPAR
                if($detail->posisi == "staff") {
                  if ($detail->chiefname != null) {
                    return '<label class="label label-warning">'.$detail->chiefname.'</label>';                 
                  }
                  else{
                    return '<label class="label label-warning">'.$detail->managername.'</label>'; 
                  }
                }
                else if($detail->posisi == "chief") {
                  if ($detail->checked_chief == null) {
                    return '<label class="label label-warning">'.$detail->chiefname.'</label>';                
                  }
                  else{
                    return '<label class="label label-warning">'.$detail->managername.'</label>';                
                  }
                }            
                else if($detail->posisi == "manager") {
                  if ($detail->checked_manager == null) {
                    return '<label class="label label-warning">'.$detail->managername.'</label>';                
                  }
                  else{
                    return '<label class="label label-warning">'.$detail->dgmname.'</label>';                
                  }
                }
                else if($detail->posisi == "dgm") {
                  if ($detail->approved_dgm == null) {
                    return '<label class="label label-warning">'.$detail->dgmname.'</label>';                
                  }
                  else{
                    return '<label class="label label-warning">'.$detail->gmname.'</label>';                
                  }
                }  
                else if($detail->posisi == "gm") {
                  if ($detail->approved_gm == null) {
                    return '<label class="label label-warning">'.$detail->gmname.'</label>';                
                  }
                  else{
                    return '<label class="label label-warning">'.$detail->name.'</label>';                
                  }
                }
                else if($detail->posisi == "bagian") {
                    return '<label class="label label-success">'.$detail->name.'</label>';     
                }  
              } 
              else if ($detail->status_code == "6") { // CAR
                if($detail->posisi_car == "staff") {
                  return '<label class="label label-primary">'.$detail->chiefcarname.'</label>';
                }
                else if($detail->posisi_car == "chief") {
                  if ($detail->checked_chief_car == null) {
                    return '<label class="label label-primary">'.$detail->chiefcarname.'</label>';                
                  }
                  else{
                    return '<label class="label label-primary">'.$detail->name.'</label>';                
                  }
                }            
                else if($detail->posisi_car == "manager") {
                  if ($detail->checked_manager_car == null) {
                    return '<label class="label label-primary">'.$detail->name.'</label>';                
                  }
                  else{
                    return '<label class="label label-primary">'.$detail->dgmname.'</label>';                
                  }
                }
                else if($detail->posisi_car == "dgm") {
                  if ($detail->approved_dgm_car == null) {
                    return '<label class="label label-primary">'.$detail->dgmname.'</label>';                
                  }
                  else{
                    return '<label class="label label-primary">'.$detail->gmname.'</label>';                
                  }
                }  
                else if($detail->posisi_car == "gm") {
                  if ($detail->approved_gm_car == null) {
                    return '<label class="label label-primary">'.$detail->gmname.'</label>';                
                  }
                  else{
                    return '<label class="label label-primary">QA</label>';                
                  }
                }
                else if($detail->posisi_car == "qa") {
                    return '<label class="label label-success">None</label>';     
                }
              }

              else if ($detail->status_code == "7") { // QA Verification
                if($detail->posisi == "QA") {
                  return '<label class="label label-primary">'.$detail->staffname.'</label>';
                }
                else if($detail->posisi == "QA2") {
                  return '<label class="label label-success">'.$detail->chiefname.'</label>';  
                }            
              }
              
            }
            else if ($detail->kategori == "Supplier") {
              if ($detail->status_code == "5") { // CPAR
                if($detail->posisi == "staff") {
                  return '<label class="label label-warning">'.$detail->chiefname.'</label>';
                }
                else if($detail->posisi == "chief") {
                  if ($detail->checked_chief == null) {
                    return '<label class="label label-warning">'.$detail->chiefname.'</label>';                
                  }
                  else{
                    return '<label class="label label-warning">'.$detail->managername.'</label>';                
                  }
                }            
                else if($detail->posisi == "manager") {
                  if ($detail->checked_manager == null) {
                    return '<label class="label label-warning">'.$detail->managername.'</label>';                
                  }
                  else{
                    return '<label class="label label-warning">'.$detail->dgmname.'</label>';                
                  }
                }
                else if($detail->posisi == "dgm") {
                  if ($detail->approved_dgm == null) {
                    return '<label class="label label-warning">'.$detail->dgmname.'</label>';                
                  }
                  else{
                    return '<label class="label label-warning">'.$detail->gmname.'</label>';                
                  }
                }  
                else if($detail->posisi == "gm") {
                  if ($detail->approved_gm == null) {
                    return '<label class="label label-warning">'.$detail->gmname.'</label>';                
                  }
                  else{
                    return '<label class="label label-warning">'.$detail->name.'</label>';                
                  }
                }
                else if($detail->posisi == "bagian") {
                    return '<label class="label label-success">'.$detail->name.'</label>';     
                }  
              } 
              else if ($detail->status_code == "6") { // CAR
                if($detail->posisi_car == "staff") {
                  return '<label class="label label-primary">'.$detail->coordinatorcarname.'</label>';
                }
                else if($detail->posisi_car == "chief") {
                  if ($detail->checked_chief_car == null) {
                    return '<label class="label label-primary">'.$detail->coordinatorcarname.'</label>';                
                  }
                  else{
                    return '<label class="label label-primary">'.$detail->name.'</label>';                
                  }
                }            
                else if($detail->posisi_car == "manager") {
                  if ($detail->checked_manager_car == null) {
                    return '<label class="label label-primary">'.$detail->name.'</label>';                
                  }
                  else{
                    return '<label class="label label-primary">'.$detail->dgmname.'</label>';                
                  }
                }
                else if($detail->posisi_car == "dgm") {
                  if ($detail->approved_dgm_car == null) {
                    return '<label class="label label-primary">'.$detail->dgmname.'</label>';                
                  }
                  else{
                    return '<label class="label label-primary">'.$detail->gmname.'</label>';                
                  }
                }  
                else if($detail->posisi_car == "gm") {
                  if ($detail->approved_gm_car == null) {
                    return '<label class="label label-primary">'.$detail->gmname.'</label>';                
                  }
                  else{
                    return '<label class="label label-primary">QA</label>';                
                  }
                }
                else if($detail->posisi_car == "qa") {
                    return '<label class="label label-success">None</label>';     
                }
                
              }

              else if ($detail->status_code == "7") { // QA Verification
                if($detail->posisi == "QA") {
                  return '<label class="label label-primary">'.$detail->staffname.'</label>';
                }
                else if($detail->posisi == "QA2") {
                  return '<label class="label label-success">'.$detail->chiefname.'</label>';  
                }            
              }
            }
            else if ($detail->kategori == "Internal") {
              if ($detail->status_code == "5") { // CPAR
                if($detail->posisi == "leader") {
                  if ($detail->foremanname != null) {
                    return '<label class="label label-warning">'.$detail->foremanname.'</label>';                    
                  }
                  else{
                    return '<label class="label label-warning">'.$detail->managername.'</label>'; 
                  }
                }
                else if($detail->posisi == "foreman") {
                  if ($detail->checked_foreman == null) {
                    return '<label class="label label-warning">'.$detail->foremanname.'</label>';                
                  }
                  else{
                    return '<label class="label label-warning">'.$detail->managername.'</label>';                
                  }
                }            
                else if($detail->posisi == "manager") {
                  if ($detail->checked_manager == null) {
                    return '<label class="label label-warning">'.$detail->managername.'</label>';                
                  }
                  else{
                    return '<label class="label label-warning">'.$detail->dgmname.'</label>';                
                  }
                }
                else if($detail->posisi == "dgm") {
                  if ($detail->approved_dgm == null) {
                    return '<label class="label label-warning">'.$detail->dgmname.'</label>';                
                  }
                  else{
                    return '<label class="label label-warning">'.$detail->gmname.'</label>';                
                  }
                }  
                else if($detail->posisi == "gm") {
                  if ($detail->approved_gm == null) {
                    return '<label class="label label-warning">'.$detail->gmname.'</label>';                
                  }
                  else{
                    return '<label class="label label-warning">'.$detail->name.'</label>';                
                  }
                }
                else if($detail->posisi == "bagian") {
                    return '<label class="label label-success">'.$detail->name.'</label>';     
                }     
              } 
              else if ($detail->status_code == "6"){ // CAR
                if($detail->posisi_car == "leader") {
                  return '<label class="label label-warning">'.$detail->foremancarname.'</label>';
                }
                else if($detail->posisi_car == "foreman") {
                  if ($detail->foremancarname == null) {
                     return '<label class="label label-primary">'.$detail->picname.'</label>';
                  }
                  else if ($detail->checked_foreman_car == null) {
                    return '<label class="label label-warning">'.$detail->foremancarname.'</label>';                
                  }
                  else{
                    return '<label class="label label-warning">'.$detail->name.'</label>';                
                  }
                }            
                else if($detail->posisi_car == "manager") {
                  if ($detail->checked_manager_car == null) {
                    return '<label class="label label-warning">'.$detail->name.'</label>';                
                  }
                  else{
                    return '<label class="label label-warning">'.$detail->dgmname.'</label>';                
                  }
                }
                else if($detail->posisi_car == "dgm") {
                  if ($detail->approved_dgm_car == null) {
                    return '<label class="label label-warning">'.$detail->dgmname.'</label>';                
                  }
                  else{
                    return '<label class="label label-warning">'.$detail->gmname.'</label>';                
                  }
                }  
                else if($detail->posisi_car == "gm") {
                  if ($detail->approved_gm_car == null) {
                    return '<label class="label label-warning">'.$detail->gmname.'</label>';                
                  }
                  else{
                    return '<label class="label label-warning">'.$detail->name.'</label>';                
                  }
                }
                else if($detail->posisi_car == "qa") {
                    return '<label class="label label-success">None</label>';     
                }
                else{
                    return '<label class="label label-primary">'.$detail->picname.'</label>';
                } 
              }
              else if ($detail->status_code == "7") { // QA Verification
                if($detail->posisi == "QA") {
                  return '<label class="label label-primary">'.$detail->leadername.'</label>';
                }
                else if($detail->posisi == "QA2") {
                  return '<label class="label label-success">'.$detail->foremanname.'</label>';  
                }            
              }  
            }
          } else {
            return '<label class="label label-success">None</label>';
          }
        })

        ->editColumn('status_name',function($detail){
          if($detail->status_name == "Unverified CPAR") {
            return '<label class="label label-danger">'.$detail->status_name. '</label>';
          }
          else if($detail->status_name == "Unverified CAR"){
            return '<label class="label label-warning">'.$detail->status_name. '</label>';
          }
          else if($detail->status_name == "QA Verification"){
            return '<label class="label label-success">'.$detail->status_name. '</label>';
          }
          else if($detail->status_name == "Closed"){
            return '<label class="label label-success">'.$detail->status_name. '</label>';
          }

          })

        ->editColumn('sumber_komplain',function($detail){
          if($detail->sumber_komplain == "Eksternal Complaint") {
            return $detail->destination_shortname;
          }
          else{
            return $detail->sumber_komplain;
          }
        })

        ->editColumn('kategori',function($detail){
          if($detail->kategori == "Eksternal") {
            if ($detail->kategori_komplain == "FG") {
              $detail->kategori_komplain  = "Finished Goods";
            }
            return $detail->kategori.' - '.$detail->kategori_komplain;
          }
          else{
            return $detail->kategori;
          }
        })

        ->addColumn('action', function($detail){
          $idcpar = $detail->id;
          $idcar = $detail->id_car;

          if($detail->status_name == "Unverified CPAR") {
            return '<a href="update/'.$idcpar.'" class="btn btn-primary btn-xs">Detail CPAR</a>
                    <a href="print_cpar/'.$idcpar.'" class="btn btn-warning btn-xs" target="_blank">Report CPAR</a>';
          } 

          else if ($detail->status_name == "Unverified CAR"){
            return '
                    <a href="print_cpar/'.$idcpar.'" class="btn btn-success btn-xs" target="_blank">Report CPAR</a>
                    <a href="../qc_car/detail/'.$idcar.'" class="btn btn-primary btn-xs">Detail CAR</a>
                    <a href="../qc_car/print_car_new/'.$idcar.'" class="btn btn-warning btn-xs" target="_blank">Report CAR</a>';
          }

          else if ($detail->status_name == "QA Verification"){
            return '<a href="print_cpar/'.$idcpar.'" class="btn btn-warning btn-xs" target="_blank">Report CPAR</a>
                    <a href="../qc_car/print_car_new/'.$idcar.'" class="btn btn-warning btn-xs" target="_blank">Report CAR</a>';
          } 

          else if($detail->status_name == "Closed"){
            return '<a href="print_cpar/'.$idcpar.'" class="btn btn-warning btn-xs" target="_blank">Report CPAR</a>
                    <a href="../qc_car/print_car_new/'.$idcar.'" class="btn btn-warning btn-xs" target="_blank">Report CAR</a>
                    ';
          }

        })

        ->rawColumns(['status_name' => 'status_name','action' => 'action','verif' => 'verif','sumber_komplain' => 'sumber_komplain', 'kategori' =>'kategori'])
        ->make(true);
    }

    public function detail_kategori(Request $request){

      $kategori = $request->get("kategori");
      $fy = $request->get("fy");

      if($fy == "All Years"){
          $fiscal = '';
      }
      else if ($fy != null) {
          $fiscal = 'and fiscal_year = "'.$fy.'"';
      } 
      else {
          $fiscal = '';
      }

      if ($kategori == "Finish Goods") {
          $kategori = "FG";
      }

      $query = "select qc_cpars.*,monthname(tgl_permintaan) as bulan,departments.department_name, qc_cars.id as id_car, statuses.status_name, qc_cars.id as id_car, destinations.destination_shortname  FROM qc_cpars join departments on departments.id = qc_cpars.department_id left join qc_cars on qc_cars.cpar_no = qc_cpars.cpar_no left join destinations on qc_cpars.destination_code = destinations.destination_code join employees on qc_cpars.employee_id = employees.employee_id join statuses on qc_cpars.status_code = statuses.status_code LEFT JOIN weekly_calendars on qc_cpars.tgl_permintaan = week_date where qc_cpars.deleted_at is null and qc_cpars.kategori_komplain ='".$kategori."' ".$fiscal." ";

      $detail = db::select($query);

      return DataTables::of($detail)

        ->editColumn('status_name',function($detail){
          if($detail->status_name == "Unverified CPAR") {
            return '<label class="label label-danger">'.$detail->status_name. '</label>';
          }
          else if($detail->status_name == "Unverified CAR"){
            return '<label class="label label-warning">'.$detail->status_name. '</label>';
          }
          else if($detail->status_name == "QA Verification"){
            return '<label class="label label-success">'.$detail->status_name. '</label>';
          }
          else if($detail->status_name == "Closed"){
            return '<label class="label label-success">'.$detail->status_name. '</label>';
          }

          })

        ->editColumn('sumber_komplain',function($detail){
          if($detail->sumber_komplain == "Eksternal Complaint") {
            return $detail->destination_shortname;
          }
          else{
            return $detail->sumber_komplain;
          }
        })

        ->editColumn('kategori',function($detail){
          if($detail->kategori == "Eksternal") {
            if ($detail->kategori_komplain == "FG") {
              $detail->kategori_komplain  = "Finished Goods";
            }
            return $detail->kategori.' - '.$detail->kategori_komplain;
          }
          else{
            return $detail->kategori;
          }
        })

        ->addColumn('action', function($detail){
          $idcpar = $detail->id;
          $idcar = $detail->id_car;

          if($detail->status_name == "Unverified CPAR") {
            return '<a href="update/'.$idcpar.'" class="btn btn-primary btn-xs">Detail CPAR</a>
                    <a href="print_cpar/'.$idcpar.'" class="btn btn-warning btn-xs" target="_blank">Report CPAR</a>';
          } 

          else if ($detail->status_name == "Unverified CAR"){
            return '
                    <a href="print_cpar/'.$idcpar.'" class="btn btn-success btn-xs" target="_blank">Report CPAR</a>
                    <a href="../qc_car/detail/'.$idcar.'" class="btn btn-primary btn-xs">Detail CAR</a>
                    <a href="../qc_car/print_car_new/'.$idcar.'" class="btn btn-warning btn-xs" target="_blank">Report CAR</a>';
          }

          else if ($detail->status_name == "QA Verification"){
            return '<a href="print_cpar/'.$idcpar.'" class="btn btn-warning btn-xs" target="_blank">Report CPAR</a>
                    <a href="../qc_car/print_car_new/'.$idcar.'" class="btn btn-warning btn-xs" target="_blank">Report CAR</a>';
          } 

          else if($detail->status_name == "Closed"){
            return '<a href="print_cpar/'.$idcpar.'" class="btn btn-warning btn-xs" target="_blank">Report CPAR</a>
                    <a href="../qc_car/print_car_new/'.$idcar.'" class="btn btn-warning btn-xs" target="_blank">Report CAR</a>
                    ';
          }

        })

        ->rawColumns(['status_name' => 'status_name','action' => 'action','verif' => 'verif','sumber_komplain' => 'sumber_komplain', 'kategori' =>'kategori'])
        ->make(true);
    }

    public function detail_cpar_dept(Request $request){
      $departemen = $request->get("departemen");
      $status = $request->get("status");

      $query = "select qc_cpars.*,departments.department_name, employees.name,statuses.status_name FROM qc_cpars join departments on departments.id = qc_cpars.department_id join employees on qc_cpars.employee_id = employees.employee_id join statuses on qc_cpars.status_code = statuses.status_code where qc_cpars.deleted_at is null and departments.department_name = '".$departemen."' and statuses.status_name ='".$status."'";

      $detail = db::select($query);

      return DataTables::of($detail)

        ->editColumn('status_name',function($detail){
          if($detail->status_name == "Open") {
            return '<label class="label label-success">'.$detail->status_name. '</label>';
          }
          else if($detail->status_name == "Closed"){
            return '<label class="label label-danger">'.$detail->status_name. '</label>';
          }
          else if($detail->status_name == "Progress"){
            return '<label class="label label-warning">'.$detail->status_name. '</label>';
          }
          })

        ->addColumn('action', function($detail){
          $idcpar = $detail->id;
          return '
                  <a href="update/'.$idcpar.'" class="btn btn-primary btn-xs">Detail</a>
                  <a href="print_cpar/'.$idcpar.'" class="btn btn-warning btn-xs" target="_blank">Report</a>
          ';
        })

        ->rawColumns(['status_name' => 'status_name','action' => 'action'])
        ->make(true);
    }

    public function detail_monitoring(Request $request){

      $cpar = $request->get("cpar");

      $query = "select qc_cpars.*,monthname(tgl_permintaan) as bulan,departments.department_name, employees.name,statuses.status_name FROM qc_cpars join departments on departments.id = qc_cpars.department_id join employees on qc_cpars.employee_id = employees.employee_id join statuses on qc_cpars.status_code = statuses.status_code where qc_cpars.deleted_at is null and qc_cpars.cpar_no = '".$cpar."'";

      $detail = db::select($query);

      $response = array(
        'status' => true,
        'datas' => $detail
      );

      return Response::json($response);
    }


    public function komplain_monitoring()
    {
      $tglnow = date('Y-m-d');
      $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$tglnow'");

      foreach ($fy as $fys) {
          $fiscal = $fys->fiscal_year;
      }

      $bulan = db::select("select count(week_name) as colspan, monthname(mon) as bulan from (select DISTINCT week_name, DATE_FORMAT(week_date,'%Y-%m-01') as mon from weekly_calendars where fiscal_year='".$fiscal."') as d group by monthname(mon) order by mon asc");

      $week = db::select("select DISTINCT week_name,monthname(week_date) as bulanweek from weekly_calendars where fiscal_year='".$fiscal."' ORDER BY week_date asc");

      $eksternal = db::select("select DISTINCT qc_cpars.cpar_no,qc_cpars.tgl_permintaan, qc_cpars.progress as progresscpar, qc_cpar_items.detail_problem, qc_cars.progress as progresscar from qc_cpars join qc_cpar_items on qc_cpars.cpar_no = qc_cpar_items.cpar_no left join qc_cars on qc_cars.cpar_no = qc_cpars.cpar_no where kategori='Eksternal'");

      return view('qc_report.monitoring',  
        array('title' => 'CPAR CAR MONITORING', 
              'title_jp' => 'Monitor',
              'bulan' => $bulan,
              'week' => $week,
              'eksternal' => $eksternal,
              'fy' => $fy
            )
        )->with('page', 'CPAR Graph');
    }



    public function fetchMonitoring(Request $request)
    {
      $data = db::select("select DISTINCT qc_cpars.cpar_no,qc_cpars.tgl_permintaan,qc_cpars.tgl_balas, qc_cpars.progress as progresscpar, qc_cpar_items.detail_problem, qc_cars.progress as progressca, week_name from qc_cpars join qc_cpar_items on qc_cpars.cpar_no = qc_cpar_items.cpar_no left join qc_cars on qc_cars.cpar_no = qc_cpars.cpar_no join weekly_calendars on weekly_calendars.week_date = qc_cpars.tgl_permintaan where kategori='Eksternal'");

      // $eksternal = db::select("select DISTINCT qc_cpars.cpar_no,qc_cpars.tgl_permintaan, qc_cpars.progress as progresscpar, qc_cpar_items.detail_problem, qc_cars.progress as progresscar from qc_cpars join qc_cpar_items on qc_cpars.cpar_no = qc_cpar_items.cpar_no left join qc_cars on qc_cars.cpar_no = qc_cpars.cpar_no where kategori='Eksternal'");
      

      // $supplier = db::select("select DISTINCT qc_cpars.id, qc_cpars.cpar_no, qc_cpars.progress as progresscpar, qc_cpar_items.detail_problem, qc_cars.progress as progresscar from qc_cpars join qc_cpar_items on qc_cpars.cpar_no = qc_cpar_items.cpar_no left join qc_cars on qc_cars.cpar_no = qc_cpars.cpar_no where kategori='Supplier'");
      // $internal = db::select("select DISTINCT qc_cpars.id, qc_cpars.cpar_no, qc_cpars.progress as progresscpar, qc_cpar_items.detail_problem, qc_cars.progress as progresscar from qc_cpars join qc_cpar_items on qc_cpars.cpar_no = qc_cpar_items.cpar_no left join qc_cars on qc_cars.cpar_no = qc_cpars.cpar_no where kategori='Internal'");

      $response = array(
        'status' => true,
        'datas' => $data
      );
      return Response::json($response); 
    }

    public function fetchtable(Request $request)
    {

      $tglfrom = $request->get('tglfrom');
      $tglto = $request->get('tglto');

      if ($tglfrom != null && $tglto != null) {
          $tanggal = 'and DATE_FORMAT(tgl_permintaan,"%Y-%m") between "'.$tglfrom.'" and "'.$tglto.'"';
      }else{
          $tanggal = '';
      }

      $kategori = $request->get('kategori');

      if ($kategori != null) {
          $cat = json_encode($kategori);
          $kat = str_replace(array("[","]"),array("(",")"),$cat);

          $kate = 'and kategori_komplain in'.$kat;
      }else{
          $kate = '';
      }

      $departemen = $request->get('departemen');

      if ($departemen != null) {
          $deptt = json_encode($departemen);
          $dept = str_replace(array("[","]"),array("(",")"),$deptt);

          $dep = 'and qc_cpars.department_id in'.$dept;
      } else {
          $dep = '';
      }

      $status = $request->get('status');

      if ($status != null) {
          $statt = json_encode($status);
          $stat = str_replace(array("[","]"),array("(",")"),$statt);

          $sta = 'and qc_cpars.status_code in '.$stat;
      } else {
          $sta = 'and qc_cpars.status_code not in (1)';
      }

      $sumber_komplain = $request->get('sumber_komplain');

      if ($sumber_komplain != null) {
          $skkk = json_encode($sumber_komplain);
          $skk = str_replace(array("[","]"),array("(",")"),$skkk);

          $sk = 'and qc_cpars.sumber_komplain in'.$skk;
      } else {
          $sk = '';
      }

      $approval_cpar = $request->get('approval_cpar');

      if ($approval_cpar != null) {
          $apprr = json_encode($approval_cpar);
          $appr = str_replace(array("[","]"),array("(",")"),$apprr);

          $app = 'and qc_cpars.kategori_approval in'.$appr;
      } else {
          $app = '';
      }

      $meeting = $request->get('meeting');

      if ($meeting != null) {
          $meett = json_encode($meeting);
          $meet = str_replace(array("[","]"),array("(",")"),$meett);

          $met = 'and qc_cpars.kategori_meeting in'.$meet;
      } else {
          $met = '';
      }

      // $sumber = $request->get('sumber');

      // if ($sumber != null) {
      //     $sumbb = json_encode($sumber);
      //     $sumb = str_replace(array("[","]"),array("(",")"),$sumbb);

      //     $sum = 'and qc_cpars.kategori_komplain in '.$sumb;
      // } else {
      //     $sum = '';
      // }

      $pic = $request->get('pic');

      $data = db::select("select qc_cpars.id,qc_cars.id as id_car,qc_cpars.kategori, qc_cpars.cpar_no, qc_cpars.status_code, qc_cpars.judul_komplain, departments.department_name, departments.department_shortname, qc_cpars.kategori_approval, qc_cpars.posisi as posisi_cpar, qc_cpars.email_status, qc_cpars.checked_chief, qc_cpars.checked_foreman, qc_cpars.checked_manager, qc_cpars.approved_dgm, qc_cpars.approved_gm, qc_cpars.received_manager, qc_cpars.kategori_meeting, qc_cars.posisi as posisi_car, qc_cars.email_status as email_status_car,qc_cars.checked_chief as checked_chief_car,qc_cars.checked_foreman as checked_foreman_car,qc_cars.checked_coordinator as checked_coordinator_car,qc_cars.checked_manager as checked_manager_car,qc_cars.approved_dgm as approved_dgm_car,qc_cars.approved_gm as approved_gm_car, IF(qc_cpars.leader is null,(select name from employees where employee_id = qc_cpars.staff),(select name from employees where employee_id = qc_cpars.leader)) as namasl, IF(qc_cpars.chief is null,(select name from employees where employee_id = qc_cpars.foreman),(select name from employees where employee_id = qc_cpars.chief)) as namacf, (select name from employees where employee_id = qc_cpars.manager) as namam, (select name from employees where employee_id = qc_cpars.dgm) as namadgm, (select name from employees where employee_id = qc_cpars.gm) as namagm, (select name from employees where employee_id = qc_cpars.employee_id) as namabagian, (select name from employees where employee_id = qc_cars.pic) as namapiccar,
        (CASE WHEN qc_verifikators.verifikatorchief is not null THEN (IF(qc_cpars.kategori = 'internal',(select name from employees where employee_id = qc_verifikators.verifikatorforeman),(select name from employees where employee_id = qc_verifikators.verifikatorchief)))
              WHEN qc_verifikators.verifikatorcoordinator is not null THEN (select name from employees where employee_id = qc_verifikators.verifikatorcoordinator)
              WHEN qc_verifikators.verifikatorforeman is not null THEN (IF(qc_cpars.kategori = 'internal',(select name from employees where employee_id = qc_verifikators.verifikatorforeman),'Tidak Ada'))
              ELSE 'Tidak Ada'
        END) as namacfcar from qc_cpars join departments on departments.id = qc_cpars.department_id left join qc_cars on qc_cpars.cpar_no = qc_cars.cpar_no join qc_verifikators on qc_cpars.department_id = qc_verifikators.department_id where qc_cpars.deleted_at is null ".$tanggal." ".$kate." ".$dep." ".$sk." ".$sta." ".$app." ".$met." order by kategori,id,cpar_no asc");

      $response = array(
        'status' => true,
        'datas' => $data,
        'pic' => $pic
      );

      return Response::json($response); 
    }

    public function fetchGantt(Request $request)
    {

      $tglnow = date('Y-m-d');
      $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$tglnow'");

      foreach ($fy as $fys) {
          $fiscal = $fys->fiscal_year;
      }

      $eksternal = db::select("select DISTINCT qc_cpars.cpar_no,year(qc_cpars.tgl_permintaan) as tahun_permintaan,month(qc_cpars.tgl_permintaan) as bulan_permintaan,day(qc_cpars.tgl_permintaan) as tanggal_permintaan,year(qc_cpars.tgl_balas) as tahun_balas,month(qc_cpars.tgl_balas) as bulan_balas,day(qc_cpars.tgl_balas) as tanggal_balas, qc_cpar_items.detail_problem, qc_cpars.progress from qc_cpars join qc_cpar_items on qc_cpars.cpar_no = qc_cpar_items.cpar_no left join qc_cars on qc_cars.cpar_no = qc_cpars.cpar_no where kategori='Eksternal' order by qc_cpars.id");

      $supplier = db::select("select DISTINCT qc_cpars.cpar_no,year(qc_cpars.tgl_permintaan) as tahun_permintaan,month(qc_cpars.tgl_permintaan) as bulan_permintaan,day(qc_cpars.tgl_permintaan) as tanggal_permintaan,year(qc_cpars.tgl_balas) as tahun_balas,month(qc_cpars.tgl_balas) as bulan_balas,day(qc_cpars.tgl_balas) as tanggal_balas, qc_cpar_items.detail_problem, qc_cpars.progress from qc_cpars join qc_cpar_items on qc_cpars.cpar_no = qc_cpar_items.cpar_no left join qc_cars on qc_cars.cpar_no = qc_cpars.cpar_no where kategori='supplier' order by qc_cpars.id"); 

      $internal = db::select("select DISTINCT qc_cpars.cpar_no,year(qc_cpars.tgl_permintaan) as tahun_permintaan,month(qc_cpars.tgl_permintaan) as bulan_permintaan,day(qc_cpars.tgl_permintaan) as tanggal_permintaan,year(qc_cpars.tgl_balas) as tahun_balas,month(qc_cpars.tgl_balas) as bulan_balas,day(qc_cpars.tgl_balas) as tanggal_balas, qc_cpar_items.detail_problem, qc_cpars.progress from qc_cpars join qc_cpar_items on qc_cpars.cpar_no = qc_cpar_items.cpar_no left join qc_cars on qc_cars.cpar_no = qc_cpars.cpar_no where kategori='Internal' order by qc_cpars.id");

           

      $rangefy = db::select("select awal.tanggal_awal,awal.bulan_awal,awal.tahun_awal,akhir.tanggal_akhir,akhir.bulan_akhir,akhir.tahun_akhir from (select day(week_date) as tanggal_awal, month(week_date) as bulan_awal, year(week_date) as tahun_awal from weekly_calendars where fiscal_year='".$fiscal."' ORDER BY week_date ASC LIMIT 1) as awal,(select day(week_date) as tanggal_akhir, month(week_date) as bulan_akhir, year(week_date) as tahun_akhir from weekly_calendars where fiscal_year='".$fiscal."' ORDER BY week_date DESC LIMIT 1) as akhir");

      $response = array(
        'status' => true,
        'eksternals' => $eksternal,
        'suppliers' => $supplier,
        'internals' => $internal,
        'rangefys'=> $rangefy
      );

      return Response::json($response); 
    }

    public function komplain_monitoring2()
    {
      $tglnow = date('Y-m-d');
      $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$tglnow'");

      foreach ($fy as $fys) {
          $fiscal = $fys->fiscal_year;
      }

      $bulan = db::select("select count(week_name) as colspan, monthname(mon) as bulan from (select DISTINCT week_name, DATE_FORMAT(week_date,'%Y-%m-01') as mon from weekly_calendars where fiscal_year='".$fiscal."') as d group by monthname(mon) order by mon asc");

      $week = db::select("select DISTINCT week_name,monthname(week_date) as bulanweek from weekly_calendars where fiscal_year='".$fiscal."' ORDER BY week_date asc");

      $eksternal = db::select("select DISTINCT qc_cpars.cpar_no,qc_cpars.tgl_permintaan, qc_cpars.progress as progresscpar, qc_cpar_items.detail_problem, qc_cars.progress as progresscar from qc_cpars join qc_cpar_items on qc_cpars.cpar_no = qc_cpar_items.cpar_no left join qc_cars on qc_cars.cpar_no = qc_cpars.cpar_no where kategori='Eksternal'");

      return view('qc_report.monitoring2',  
        array('title' => 'CPAR CAR MONITORING', 
              'title_jp' => 'Monitor',
              'bulan' => $bulan,
              'week' => $week,
              'eksternal' => $eksternal,
              'fy' => $fy
            )
        )->with('page', 'CPAR Graph');
    }

    public function komplain_monitoring3()
    {
      $tglnow = date('Y-m-d');
      $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$tglnow'");

      foreach ($fy as $fys) {
          $fiscal = $fys->fiscal_year;
      }

      $bulan = db::select("select count(week_name) as colspan, monthname(mon) as bulan from (select DISTINCT week_name, DATE_FORMAT(week_date,'%Y-%m-01') as mon from weekly_calendars where fiscal_year='".$fiscal."') as d group by monthname(mon) order by mon asc");

      $week = db::select("select DISTINCT week_name,monthname(week_date) as bulanweek from weekly_calendars where fiscal_year='".$fiscal."' ORDER BY week_date asc");

      $eksternal = db::select("select DISTINCT qc_cpars.cpar_no,qc_cpars.tgl_permintaan, qc_cpars.progress as progresscpar, qc_cpar_items.detail_problem, qc_cars.progress as progresscar from qc_cpars join qc_cpar_items on qc_cpars.cpar_no = qc_cpar_items.cpar_no left join qc_cars on qc_cars.cpar_no = qc_cpars.cpar_no where kategori='Eksternal'");

      return view('qc_report.monitoring3',  
        array('title' => 'CPAR CAR MONITORING', 
              'title_jp' => 'Monitor',
              'bulan' => $bulan,
              'week' => $week,
              'eksternal' => $eksternal,
              'fy' => $fy
            )
        )->with('page', 'CPAR Graph');
    }

    public function komplain_monitoring4()
    {
      $tglnow = date('Y-m-d');
      $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$tglnow'");

      foreach ($fy as $fys) {
          $fiscal = $fys->fiscal_year;
      }

      $dept = db::select("select distinct departments.department_name, count(distinct qc_cpars.department_id)*2 as colspan  from qc_cpars join departments on qc_cpars.department_id = departments.id GROUP BY department_name;");

      $week = db::select("select DISTINCT week_name,monthname(week_date) as bulanweek from weekly_calendars where fiscal_year='".$fiscal."' ORDER BY week_date asc");

      $eksternal = db::select("select DISTINCT qc_cpars.cpar_no,qc_cpars.tgl_permintaan, qc_cpars.progress as progresscpar, qc_cpar_items.detail_problem, qc_cars.progress as progresscar from qc_cpars join qc_cpar_items on qc_cpars.cpar_no = qc_cpar_items.cpar_no left join qc_cars on qc_cars.cpar_no = qc_cpars.cpar_no where kategori='Eksternal'");

      return view('qc_report.monitoring4',  
        array('title' => 'CPAR CAR MONITORING', 
              'title_jp' => 'Monitor',
              'dept' => $dept,
              'week' => $week,
              'eksternal' => $eksternal,
              'fy' => $fy
            )
        )->with('page', 'CPAR Graph');
    }

    public function komplain_monitoring5()
    {
      $tglnow = date('Y-m-d');
      $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$tglnow'");

      foreach ($fy as $fys) {
          $fiscal = $fys->fiscal_year;
      }

      $dept = db::select("select distinct departments.department_name, count(distinct qc_cpars.department_id)*2 as colspan  from qc_cpars join departments on qc_cpars.department_id = departments.id GROUP BY department_name;");

      $week = db::select("select DISTINCT week_name,monthname(week_date) as bulanweek from weekly_calendars where fiscal_year='".$fiscal."' ORDER BY week_date asc");

      $eksternal = db::select("select DISTINCT qc_cpars.cpar_no,qc_cpars.tgl_permintaan, qc_cpars.progress as progresscpar, qc_cpar_items.detail_problem, qc_cars.progress as progresscar from qc_cpars join qc_cpar_items on qc_cpars.cpar_no = qc_cpar_items.cpar_no left join qc_cars on qc_cars.cpar_no = qc_cpars.cpar_no where kategori='Eksternal'");

      return view('qc_report.monitoring5',  
        array('title' => 'CPAR CAR Monitoring', 
              'title_jp' => 'Monitor',
              'dept' => $dept,
              'week' => $week,
              'eksternal' => $eksternal,
              'fy' => $fy
            )
        )->with('page', 'CPAR Graph');
    }

    //cetak PDF

    public function print_cpar($id)
    {
      $qc_cpars = QcCpar::find($id);

      if ($qc_cpars->staff != null) {
          $posisi = "staff";
          $posisi2 = "chief";
      } else if ($qc_cpars->leader != null) {
          $posisi = "leader";
          $posisi2 = "foreman";
      } 

      $cpars = QcCpar::select('qc_cpars.*','destinations.destination_name','vendors.name as vendorname','departments.department_name','employees.name',$posisi.'.name as '.$posisi.'name',$posisi2.'.name as '.$posisi2.'name','manager.name as managername','dgm.name as dgmname','gm.name as gmname','statuses.status_name')
        ->join('departments','qc_cpars.department_id','=','departments.id')
        ->join('employees','qc_cpars.employee_id','=','employees.employee_id')
        ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
        ->leftjoin('destinations','qc_cpars.destination_code','=','destinations.destination_code')
        ->leftjoin('vendors','qc_cpars.vendor','=','vendors.vendor')
        ->leftjoin('employees as '.$posisi,'qc_cpars.'.$posisi,'=',$posisi.'.employee_id')
        ->leftjoin('employees as '.$posisi2,'qc_cpars.'.$posisi2,'=',$posisi2.'.employee_id')
        ->leftjoin('employees as manager','qc_cpars.manager','=','manager.employee_id')
        ->leftjoin('employees as dgm','qc_cpars.dgm','=','dgm.employee_id')
        ->leftjoin('employees as gm','qc_cpars.gm','=','gm.employee_id')
        // ->join('qc_cpars_items','qc_cpars.cpar_no','=')

        ->where('qc_cpars.id','=',$id)
        ->get();

      $parts = QcCparItem::select('qc_cpar_items.*','material_plant_data_lists.material_description')
      ->join('qc_cpars','qc_cpar_items.cpar_no','=','qc_cpars.cpar_no')
      ->join('material_plant_data_lists','qc_cpar_items.part_item','=','material_plant_data_lists.material_number')
      ->where('qc_cpars.id','=',$id)
      ->get();

      // $cparttds = QcCpar::select('qc_cpars.*','qc_ttd_cobas.ttd')
      // ->rightjoin('qc_ttd_cobas','qc_cpars.cpar_no','=','qc_ttd_cobas.cpar_no')
      // ->where('qc_cpars.id','=',$id)
      // ->get();
      
      $cparttds = DB::SELECT("select * from qc_cpars left join qc_ttd_cobas on qc_cpars.cpar_no = qc_ttd_cobas.cpar_no where qc_cpars.id='".$id."'");

      $pdf = \App::make('dompdf.wrapper');
      $pdf->getDomPDF()->set_option("enable_php", true);
      $pdf->setPaper('Legal', 'potrait');
      $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
      
      $pdf->loadView('qc_report.print_cpar', array(
        'cpars' => $cpars,
        'parts' => $parts,
        'cparss' => $cparttds
      ));
      
      $cpar = str_replace("/"," ",$qc_cpars->cpar_no);
      // $pdf = PDF::loadview('qc_report.print_cpar',['cpars'=>$cpars,'parts'=>$parts]);
      return $pdf->stream("CPAR ".$cpar. ".pdf");
    }

     public function print_cpar_new($id)
      {

      $qc_cpars = QcCpar::find($id);

      if ($qc_cpars->staff != null) {
          $posisi = "staff";
          $posisi2 = "chief";
      } else if ($qc_cpars->leader != null) {
          $posisi = "leader";
          $posisi2 = "foreman";
      } 

      $cpars = QcCpar::select('qc_cpars.*','destinations.destination_name','vendors.name as vendorname','departments.department_name','employees.name',$posisi.'.name as '.$posisi.'name',$posisi2.'.name as '.$posisi2.'name','manager.name as managername','dgm.name as dgmname','gm.name as gmname','statuses.status_name')
        ->join('departments','qc_cpars.department_id','=','departments.id')
        ->join('employees','qc_cpars.employee_id','=','employees.employee_id')
        ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
        ->leftjoin('destinations','qc_cpars.destination_code','=','destinations.destination_code')
        ->leftjoin('vendors','qc_cpars.vendor','=','vendors.vendor')
        ->leftjoin('employees as '.$posisi,'qc_cpars.'.$posisi,'=',$posisi.'.employee_id')
        ->leftjoin('employees as '.$posisi2,'qc_cpars.'.$posisi2,'=',$posisi2.'.employee_id')
        ->leftjoin('employees as manager','qc_cpars.manager','=','manager.employee_id')
        ->leftjoin('employees as dgm','qc_cpars.dgm','=','dgm.employee_id')
        ->leftjoin('employees as gm','qc_cpars.gm','=','gm.employee_id')
        // ->join('qc_cpars_items','qc_cpars.cpar_no','=')

        ->where('qc_cpars.id','=',$id)
        ->get();

        $parts = QcCparItem::select('qc_cpar_items.*','material_plant_data_lists.material_description')
        ->join('qc_cpars','qc_cpar_items.cpar_no','=','qc_cpars.cpar_no')
        ->join('material_plant_data_lists','qc_cpar_items.part_item','=','material_plant_data_lists.material_number')
        ->where('qc_cpars.id','=',$id)
        ->get();

        return view('qc_report.print2_cpar', array(
            'cpars' => $cpars,
            'parts' => $parts
        ))->with('page', 'CPAR');
      }

      // Sign Online
      public function verifikasigm($id){
          // $cpar = QcCpar::find($id);

          $qc_cpars = QcCpar::find($id);

          if ($qc_cpars->staff != null) {
              $posisi = "staff";
              $posisi2 = "chief";
          } else if ($qc_cpars->leader != null) {
              $posisi = "leader";
              $posisi2 = "foreman";
          } 

          $cpars = QcCpar::select('qc_cpars.*','destinations.destination_name','vendors.name as vendorname','departments.department_name','employees.name',$posisi.'.name as '.$posisi.'name',$posisi2.'.name as '.$posisi2.'name','manager.name as managername','dgm.name as dgmname','gm.name as gmname','statuses.status_name')
            ->join('departments','qc_cpars.department_id','=','departments.id')
            ->join('employees','qc_cpars.employee_id','=','employees.employee_id')
            ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
            ->leftjoin('destinations','qc_cpars.destination_code','=','destinations.destination_code')
            ->leftjoin('vendors','qc_cpars.vendor','=','vendors.vendor')
            ->leftjoin('employees as '.$posisi,'qc_cpars.'.$posisi,'=',$posisi.'.employee_id')
            ->leftjoin('employees as '.$posisi2,'qc_cpars.'.$posisi2,'=',$posisi2.'.employee_id')
            ->leftjoin('employees as manager','qc_cpars.manager','=','manager.employee_id')
            ->leftjoin('employees as dgm','qc_cpars.dgm','=','dgm.employee_id')
            ->leftjoin('employees as gm','qc_cpars.gm','=','gm.employee_id')
            // ->join('qc_cpars_items','qc_cpars.cpar_no','=')

            ->where('qc_cpars.id','=',$id)
            ->get();

            $parts = QcCparItem::select('qc_cpar_items.*','material_plant_data_lists.material_description')
            ->join('qc_cpars','qc_cpar_items.cpar_no','=','qc_cpars.cpar_no')
            ->join('material_plant_data_lists','qc_cpar_items.part_item','=','material_plant_data_lists.material_number')
            ->where('qc_cpars.id','=',$id)
            ->get();

            $cparttds = QcCpar::select('qc_cpars.*','departments.department_name','employees.name','statuses.status_name','qc_ttd_cobas.ttd')
            ->join('departments','qc_cpars.department_id','=','departments.id')
            ->join('employees','qc_cpars.employee_id','=','employees.employee_id')
            ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
            ->leftjoin('qc_ttd_cobas','qc_cpars.cpar_no','=','qc_ttd_cobas.cpar_no')
            ->where('qc_cpars.id','=',$id)
            ->get();

          return view('qc_report.verifikasi_gm', array(
            'cpar' => $qc_cpars,
            'cparss' =>  $cparttds,
            'cpars' => $cpars,
            'parts' => $parts
          ))->with('page', 'CPAR');
      }

      public function sign()
      {
          return view('qc_report.signature')->with('page', 'CPAR');
      }

      public function save_sign(Request $request)
      {
          $id_user = Auth::id();

          $result = array();
          $imagedata = base64_decode($request->get('img_data'));
          $filename = md5(date("dmYhisA"));
          //Location to where you want to created sign image
          $file_name = './images/sign/'.$filename.'.png';
          file_put_contents($file_name,$imagedata);
          $result['status'] = 1;
          $result['file_name'] = $file_name;
          echo json_encode($result);

          $ttd = new QcTtdCoba([
            'ttd' => $result['file_name'],
            'cpar_no' => $request->get('cpar_no'),
            'created_by' => $id_user
          ]);

          $ttd->save();

          $cpars = QcCpar::find($request->get('id'));
          if ($cpars->posisi == "gm") {            
            $cpars->approved_gm = "Checked"; 

            $mailto = "select distinct employees.name,email,phone from qc_cpars join employees on qc_cpars.employee_id = employees.employee_id join users on employees.employee_id = users.username where qc_cpars.id='".$request->get('id')."'";
            $mails = DB::select($mailto);

            foreach($mails as $mail){
              $mailtoo = $mail->email;
              $number = $mail->phone;
            }

            if (strpos($number, '/') !== false) {
              $number2 = explode("/", $number);
              $mailnumber = '62'.substr($number2[0],1);
            }else{
              $mailnumber = '62'.substr($number, 1);
            }

            $cpars->email_status = "SentBagian";
            $cpars->email_send_date = date('Y-m-d');
            $cpars->posisi = "bagian";
            $cpars->status_code = "6"; // add++
            $cpars->received_manager = "Received";
            $cpars->progress = "60";
            $cpars->save();

            $cars = new QcCar([
              'cpar_no' => $cpars->cpar_no,
              'posisi' => 'bagian',
              'email_status' => 'SentBagian',
              'email_send_date' => date('Y-m-d'),
              'tinjauan' => '0',
              'progress' => '15',
              'created_by' => $id_user
            ]);

            $cars->save();

            $query2 = "select qc_cpars.*, destination_name, vendors.name as vendorname ,qc_cpar_items.part_item, qc_cpar_items.sample_qty, qc_cpar_items.defect_qty, qc_cpar_items.detail_problem,departments.department_name,employee_syncs.name,statuses.status_name, material_description FROM qc_cpars join departments on departments.id = qc_cpars.department_id join employee_syncs on qc_cpars.employee_id = employee_syncs.employee_id join statuses on qc_cpars.status_code = statuses.status_code join qc_cpar_items on qc_cpars.cpar_no = qc_cpar_items.cpar_no join material_qa_complaints on material_qa_complaints.material_number = qc_cpar_items.part_item left join destinations on qc_cpars.destination_code = destinations.destination_code left join vendors on qc_cpars.vendor = vendors.vendor where qc_cpars.id ='".$request->get('id')."'";

            $cpars2 = db::select($query2);

            Mail::to($mailtoo)->send(new SendEmail($cpars2, 'cpar'));
            // return redirect('/index/verifikasigm')->with('status', 'E-mail has Been Sent To Department')->with('page', 'CPAR');


          }
          $cpars->save();

          

      }

      //------ Email ------

      public function sendemail(Request $request,$id,$posisi)
      {
          $id_user = Auth::id();

          $query = "select qc_cpars.*, destination_name, vendors.name as vendorname ,qc_cpar_items.part_item, qc_cpar_items.sample_qty, qc_cpar_items.defect_qty, qc_cpar_items.detail_problem,departments.department_name,employee_syncs.name,statuses.status_name, material_description FROM qc_cpars join departments on departments.id = qc_cpars.department_id join employee_syncs on qc_cpars.employee_id = employee_syncs.employee_id join statuses on qc_cpars.status_code = statuses.status_code join qc_cpar_items on qc_cpars.cpar_no = qc_cpar_items.cpar_no join material_qa_complaints on material_qa_complaints.material_number = qc_cpar_items.part_item left join destinations on qc_cpars.destination_code = destinations.destination_code left join vendors on qc_cpars.vendor = vendors.vendor where qc_cpars.id ='".$id."'";
          $cpars = db::select($query);

          $qc_cpars = QcCpar::find($id);

          if ($posisi == "staff") {
            if ($qc_cpars->chief != NULL || $qc_cpars->foreman != NULL) {
              $to = "chief";              
            }
            else{
             $to = "manager"; 
            }

          } elseif ($posisi == "leader") {
            if ($qc_cpars->chief != NULL || $qc_cpars->foreman != NULL) {
              $to = "foreman";
            }
            else{
              $to = "manager"; 
            }
          } elseif ($posisi == "chief") {
              $to = "manager";
          } elseif ($posisi == "foreman") {
              $to = "manager";
          } elseif ($posisi == "manager") {
              $to = "dgm";
          } elseif ($posisi == "dgm") {
              $to = "gm";
          } elseif ($posisi == "gm") {
              $to = "employee_id"; //manager departemen
          }

          $mailto = "select distinct employees.name,email,phone from qc_cpars join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cpars.id='".$id."'";
          $mails = DB::select($mailto);

          foreach($mails as $mail){
            $mailtoo = $mail->email;
            $number = $mail->phone;
          }

          if (strpos($number, '/') !== false) {
            $number2 = explode("/", $number);
            $mailnumber = '62'.substr($number2[0],1);
          }else{
            $mailnumber = '62'.substr($number, 1);
          }

          if($cpars != null){
          // var_dump($cpars);die();
              if ($qc_cpars->email_status == NULL && $qc_cpars->posisi == "staff") {
                if ($qc_cpars->chief != NULL) {
                  $qc_cpars->email_status = "SentChief";
                  $qc_cpars->email_send_date = date('Y-m-d');
                  $qc_cpars->posisi = "chief";
                  $qc_cpars->progress = "25";
                  $qc_cpars->save();

                  
                  
                  Mail::to($mailtoo)->bcc('rio.irvansyah@music.yamaha.com','Rio Irvansyah')->send(new SendEmail($cpars, 'cpar'));
                  return redirect('/index/qc_report')->with('status', 'E-mail ke Chief berhasil terkirim')->with('page', 'CPAR');
                }
                else{
                  $qc_cpars->email_status = "SentManager";
                  $qc_cpars->email_send_date = date('Y-m-d');
                  $qc_cpars->posisi = "manager";
                  $qc_cpars->progress = "30";
                  $qc_cpars->save();

                  

                  Mail::to($mailtoo)->send(new SendEmail($cpars, 'cpar'));
                  return redirect('/index/qc_report')->with('status', 'E-mail ke Manager berhasil terkirim')->with('page', 'CPAR');
                }
              }

              else if ($qc_cpars->email_status == NULL && $qc_cpars->posisi == "leader") {
                if ($qc_cpars->foreman != NULL) {
                  $qc_cpars->email_status = "SentForeman";
                  $qc_cpars->email_send_date = date('Y-m-d');
                  $qc_cpars->posisi = "foreman";
                  $qc_cpars->progress = "25";
                  $qc_cpars->save();

                  

                  Mail::to($mailtoo)->bcc('rio.irvansyah@music.yamaha.com','Rio Irvansyah')->send(new SendEmail($cpars, 'cpar'));
                  return redirect('/index/qc_report')->with('status', 'E-mail ke Foreman berhasil terkirim')->with('page', 'CPAR');
                }
                else{
                  $qc_cpars->email_status = "SentManager";
                  $qc_cpars->email_send_date = date('Y-m-d');
                  $qc_cpars->posisi = "manager";
                  $qc_cpars->progress = "30";
                  $qc_cpars->save();

                  

                  Mail::to($mailtoo)->send(new SendEmail($cpars, 'cpar'));
                  return redirect('/index/qc_report')->with('status', 'E-mail ke Manager berhasil terkirim')->with('page', 'CPAR');
                }
              }

              else if(($qc_cpars->email_status == "SentChief" && $qc_cpars->posisi == "chief") || ($qc_cpars->email_status == "SentForeman" && $qc_cpars->posisi == "foreman")){
                $qc_cpars->email_status = "SentManager";
                $qc_cpars->email_send_date = date('Y-m-d');
                $qc_cpars->posisi = "manager";
                $qc_cpars->progress = "30";
                $qc_cpars->save();

                

                Mail::to($mailtoo)->send(new SendEmail($cpars, 'cpar'));
                return redirect('/index/qc_report')->with('status', 'E-mail ke Manager berhasil terkirim')->with('page', 'CPAR');
              }

              else if($qc_cpars->email_status == "SentManager" && $qc_cpars->posisi == "manager"){
                $qc_cpars->email_status = "SentDGM";
                $qc_cpars->email_send_date = date('Y-m-d');
                $qc_cpars->posisi = "dgm";
                $qc_cpars->progress = "40";
                $qc_cpars->save();

                

                Mail::to($mailtoo)->send(new SendEmail($cpars, 'cpar'));
                return redirect('/index/qc_report')->with('status', 'E-mail ke DGM berhasil terkirim')->with('page', 'CPAR');
              }

              else if($qc_cpars->email_status == "SentDGM" && $qc_cpars->posisi == "dgm"){
                $qc_cpars->email_status = "SentGM";
                $qc_cpars->email_send_date = date('Y-m-d');
                $qc_cpars->posisi = "gm";
                $qc_cpars->progress = "50";
                $qc_cpars->save();
                // Mail::to($mailtoo)->send(new SendEmail($cpars, 'cpar'));
                return redirect('/index/qc_report')->with('status', 'E-mail ke GM berhasil terkirim')->with('page', 'CPAR');
              }
              else if($qc_cpars->email_status == "SentGM" && $qc_cpars->posisi == "gm"){
                $qc_cpars->email_status = "SentBagian";
                $qc_cpars->email_send_date = date('Y-m-d');
                $qc_cpars->posisi = "bagian";
                $qc_cpars->received_manager = "Received";
                $qc_cpars->progress = "60";
                $qc_cpars->status_code = "6";
                $qc_cpars->save();

                $cars = new QcCar([
                  'cpar_no' => $qc_cpars->cpar_no,
                  'posisi' => 'bagian',
                  'email_status' => 'SentBagian',
                  'email_send_date' => date('Y-m-d'),
                  'tinjauan' => '0',
                  'progress' => '15',
                  'created_by' => $id_user
                ]);

                $cars->save();

                $query2 = "select qc_cpars.*, destination_name, vendors.name as vendorname ,qc_cpar_items.part_item, qc_cpar_items.sample_qty, qc_cpar_items.defect_qty, qc_cpar_items.detail_problem,departments.department_name,employee_syncs.name,statuses.status_name, material_description, qc_cars.id as id_car FROM qc_cpars join departments on departments.id = qc_cpars.department_id join employee_syncs on qc_cpars.employee_id = employee_syncs.employee_id join statuses on qc_cpars.status_code = statuses.status_code join qc_cpar_items on qc_cpars.cpar_no = qc_cpar_items.cpar_no join material_qa_complaints on material_qa_complaints.material_number = qc_cpar_items.part_item join qc_cars on qc_cpars.cpar_no = qc_cars.cpar_no left join destinations on qc_cpars.destination_code = destinations.destination_code left join vendors on qc_cpars.vendor = vendors.vendor where qc_cpars.id='".$id."'";
                $cpars2 = db::select($query2);

                Mail::to($mailtoo)->send(new SendEmail($cpars2, 'cpar'));
                return redirect('/index/qc_report')->with('status', 'E-mail has Been Sent To Department')->with('page', 'CPAR');
              }

              else if($qc_cpars->email_status == "SentChief" || $qc_cpars->email_status == "SentManager" || $qc_cpars->email_status == "SentDGM" || $qc_cpars->email_status == "SentGM" || $qc_cpars->email_status == "SentBagian"){
                return redirect('/index/qc_report')->with('error', 'Email pernah dikirim')->with('page', 'CPAR');
              }
          }

          else{
            return redirect('/index/qc_report')->with('error', 'Data tidak tersedia.')->with('page', 'CPAR');
          }
      }

      public function getmaterialsbymaterialsnumber(Request $request)
      {
          $html = array();
          $materials_number = MaterialQaComplaint::where('material_number',$request->materials_number)->get();
          foreach ($materials_number as $material) {
              $html = array(
                'material_description' => $material->material_description
              );

          }

          return json_encode($html);
      }


      //verifikasi

      public function statuscpar($id){
          $cpars = QcCpar::select('qc_cpars.*')
          ->where('qc_cpars.id',$id)
          ->get();

          return view('qc_report.status_cpar', array(
            'cpars' => $cpars
          ))->with('page', 'CPAR');
      }

      public function verifikasicpar($id){
          $cpar = QcCpar::find($id);

          // if ($cpar->posisi == "chief") {
          //     $from = "staff";
          // }
          // else if ($cpar->posisi == "foreman") {
          //     $from = "leader";
          // }
          // else if ($cpar->posisi == "manager" && $cpar->staff != null) {
          //     $from = "chief";
          // }
          // else if ($cpar->posisi == "manager" && $cpar->leader != null) {
          //     $from = "foreman";
          // }
          // else if ($cpar->posisi == "manager" && $cpar->chief == null && $cpar->foreman == null) {
          //     $from = "leader";
          // }
          // else if ($cpar->posisi == "dgm") {
          //     $from = "manager";
          // }
          // else if ($cpar->posisi == "gm") {
          //     $from = "dgm";
          // }
          // else {
          //     $from = "staff";
          // }

          $cpars = QcCpar::select('qc_cpars.*','departments.department_name','employees.name','statuses.status_name','destinations.destination_name','destinations.destination_shortname','vendors.name as vendorname')
          ->join('departments','qc_cpars.department_id','=','departments.id')
          ->join('employees','qc_cpars.employee_id','=','employees.employee_id')
          ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
          ->leftjoin('destinations','qc_cpars.destination_code','=','destinations.destination_code')
          ->leftjoin('vendors','qc_cpars.vendor','=','vendors.vendor')
          ->where('qc_cpars.id','=',$id)
          ->get();

          $parts = QcCparItem::select('qc_cpar_items.*','material_plant_data_lists.material_description')
          ->join('qc_cpars','qc_cpar_items.cpar_no','=','qc_cpars.cpar_no')
          ->join('material_plant_data_lists','qc_cpar_items.part_item','=','material_plant_data_lists.material_number')
          ->where('qc_cpars.id','=',$id)
          ->get();

          return view('qc_report.verifikasi_cpar', array(
            'cparss' => $cpars,
            'cpar' => $cpar,
            'parts' => $parts
          ))->with('page', 'CPAR');
      }

      public function checked(Request $request,$id)
      {

          $id_user = Auth::id();

          $query = "select qc_cpars.*, destination_name, vendors.name as vendorname ,qc_cpar_items.part_item, qc_cpar_items.sample_qty, qc_cpar_items.defect_qty, qc_cpar_items.detail_problem,departments.department_name,employee_syncs.name,statuses.status_name, material_description FROM qc_cpars join departments on departments.id = qc_cpars.department_id join employee_syncs on qc_cpars.employee_id = employee_syncs.employee_id join statuses on qc_cpars.status_code = statuses.status_code join qc_cpar_items on qc_cpars.cpar_no = qc_cpar_items.cpar_no join material_qa_complaints on material_qa_complaints.material_number = qc_cpar_items.part_item left join destinations on qc_cpars.destination_code = destinations.destination_code left join vendors on qc_cpars.vendor = vendors.vendor where qc_cpars.id ='".$id."'";
          $emailcpar = db::select($query);

          $checked = $request->get('checked');
          if(count($checked) == 7 || count($checked) == 14 || count($checked) == 20 || count($checked) == 26 || count($checked) == 32 || count($checked) == 38){
            $cpars = QcCpar::find($id);
            if ($cpars->posisi == "chief") {
              $cpars->checked_chief = "Checked";         
              $cpars->email_status = "SentManager";
              $cpars->email_send_date = date('Y-m-d');
              $cpars->posisi = "manager";
              $cpars->progress = "30";
              $cpars->save();

              $mailto = "select distinct employees.name,email,phone from qc_cpars join employees on qc_cpars.manager = employees.employee_id join users on employees.employee_id = users.username where qc_cpars.id='".$id."'";
              $mails = DB::select($mailto);

              foreach($mails as $mail){
                $mailtoo = $mail->email;
              }

              $query = "select qc_cpars.*, destination_name, vendors.name as vendorname ,qc_cpar_items.part_item, qc_cpar_items.sample_qty, qc_cpar_items.defect_qty, qc_cpar_items.detail_problem,departments.department_name,employee_syncs.name,statuses.status_name, material_description FROM qc_cpars join departments on departments.id = qc_cpars.department_id join employee_syncs on qc_cpars.employee_id = employee_syncs.employee_id join statuses on qc_cpars.status_code = statuses.status_code join qc_cpar_items on qc_cpars.cpar_no = qc_cpar_items.cpar_no join material_qa_complaints on material_qa_complaints.material_number = qc_cpar_items.part_item left join destinations on qc_cpars.destination_code = destinations.destination_code left join vendors on qc_cpars.vendor = vendors.vendor where qc_cpars.id ='".$id."'";
              $qc_cpars = db::select($query);

              Mail::to($mailtoo)->bcc('rio.irvansyah@music.yamaha.com','Rio Irvansyah')->send(new SendEmail($qc_cpars, 'cpar'));
              return redirect('/index/qc_report/verifikasicpar/'.$id)->with('status', 'CPAR Approved')->with('page', 'CPAR');

              $cpars = db::select($query);
            }
            else if ($cpars->posisi == "foreman") {
              $cpars->checked_foreman = "Checked";    
              $cpars->email_status = "SentManager";
              $cpars->email_send_date = date('Y-m-d');
              $cpars->posisi = "manager";
              $cpars->progress = "30";
              $cpars->save();

              $mailto = "select distinct employees.name,email,phone from qc_cpars join employees on qc_cpars.manager = employees.employee_id join users on employees.employee_id = users.username where qc_cpars.id='".$id."'";
              $mails = DB::select($mailto);

              foreach($mails as $mail){
                $mailtoo = $mail->email;
              }

              $query = "select qc_cpars.*, destination_name, vendors.name as vendorname ,qc_cpar_items.part_item, qc_cpar_items.sample_qty, qc_cpar_items.defect_qty, qc_cpar_items.detail_problem,departments.department_name,employee_syncs.name,statuses.status_name, material_description FROM qc_cpars join departments on departments.id = qc_cpars.department_id join employee_syncs on qc_cpars.employee_id = employee_syncs.employee_id join statuses on qc_cpars.status_code = statuses.status_code join qc_cpar_items on qc_cpars.cpar_no = qc_cpar_items.cpar_no join material_qa_complaints on material_qa_complaints.material_number = qc_cpar_items.part_item left join destinations on qc_cpars.destination_code = destinations.destination_code left join vendors on qc_cpars.vendor = vendors.vendor where qc_cpars.id ='".$id."'";
              $qc_cpars = db::select($query);

              Mail::to($mailtoo)->bcc('rio.irvansyah@music.yamaha.com','Rio Irvansyah')->send(new SendEmail($qc_cpars, 'cpar'));
              return redirect('/index/qc_report/verifikasicpar/'.$id)->with('status', 'CPAR Approved')->with('page', 'CPAR');
            }
            else if ($cpars->posisi == "manager") {
              $cpars->checked_manager = "Checked"; 
              $cpars->approved_dgm = "Checked"; 
              $cpars->approved_gm = "Checked";      
              $cpars->email_status = "SentBagian";
              $cpars->email_send_date = date('Y-m-d');
              $cpars->posisi = "bagian";
              $cpars->received_manager = "Received";
              $cpars->progress = "60";
              $cpars->status_code = "6";
              $cpars->save();

              $cars = new QcCar([
                'cpar_no' => $cpars->cpar_no,
                'posisi' => 'bagian',
                'email_status' => 'SentBagian',
                'email_send_date' => date('Y-m-d'),
                'tinjauan' => '0',
                'progress' => '15',
                'created_by' => $id_user
              ]);

              $cars->save();

              $ttd = new QcTtdCoba([
                'ttd' => 'no_sign',
                'cpar_no' => $cpars->cpar_no,
                'created_by' => $id_user
              ]);

              $ttd->save();

              $mailtocc = "select distinct employees.name,email from qc_cpars join employees on qc_cpars.dgm = employees.employee_id join users on employees.employee_id = users.username where qc_cpars.id='".$id."'";
              $mailscc = DB::select($mailtocc);

              foreach($mailscc as $mailcc){
                $mailtoocc = $mailcc->email;
              }


              $mailto = "select distinct employees.name,email from qc_cpars join employees on qc_cpars.employee_id = employees.employee_id join users on employees.employee_id = users.username where qc_cpars.id='".$id."'";
              $mails = DB::select($mailto);

              foreach($mails as $mail){
                $mailtoo = $mail->email;
              }

              $query2 = "select qc_cpars.*, destination_name, vendors.name as vendorname ,qc_cpar_items.part_item, qc_cpar_items.sample_qty, qc_cpar_items.defect_qty, qc_cpar_items.detail_problem,departments.department_name,employee_syncs.name,statuses.status_name, material_description, qc_cars.id as id_car FROM qc_cpars join departments on departments.id = qc_cpars.department_id join employee_syncs on qc_cpars.employee_id = employee_syncs.employee_id join statuses on qc_cpars.status_code = statuses.status_code join qc_cpar_items on qc_cpars.cpar_no = qc_cpar_items.cpar_no join material_qa_complaints on material_qa_complaints.material_number = qc_cpar_items.part_item join qc_cars on qc_cpars.cpar_no = qc_cars.cpar_no left join destinations on qc_cpars.destination_code = destinations.destination_code left join vendors on qc_cpars.vendor = vendors.vendor where qc_cpars.id='".$id."'";

              $cpars2 = db::select($query2);

              Mail::to($mailtoo)->cc($mailtoocc)->bcc('rio.irvansyah@music.yamaha.com','Rio Irvansyah')->send(new SendEmail($cpars2, 'cpar'));
            }
            else if ($cpars->posisi == "dgm") {
              $cpars->approved_dgm = "Checked";
              $cpars->email_status = "SentGM";
              $cpars->email_send_date = date('Y-m-d');
              $cpars->posisi = "gm";
              $cpars->progress = "50";            
            }
            // else if ($cpars->posisi == "gm") {

            //   $cpars->approved_gm = "Checked"; 
            //   $mailto = "select distinct employees.name,email from qc_cpars join employees on qc_cpars.employee_id = employees.employee_id join users on employees.employee_id = users.username where qc_cpars.id='".$id."'";
            //   $mails = DB::select($mailto);

            //   foreach($mails as $mail){
            //     $mailtoo = $mail->email;
            //   }

            //   $cpars->email_status = "SentBagian";
            //   $cpars->email_send_date = date('Y-m-d');
            //   $cpars->posisi = "bagian";
            //   $cpars->received_manager = "Received";
            //   $cpars->progress = "60";
            //   $cpars->status_code = "6";
            //   $cpars->save();

            //   $cars = new QcCar([
            //     'cpar_no' => $cpars->cpar_no,
            //     'posisi' => 'bagian',
            //     'email_status' => 'SentBagian',
            //     'email_send_date' => date('Y-m-d'),
            //     'tinjauan' => '0',
            //     'progress' => '15',
            //     'created_by' => $id_user
            //   ]);

            //   $cars->save();

            //   $ttd = new QcTtdCoba([
            //     'ttd' => 'no_sign',
            //     'cpar_no' => $cpars->cpar_no,
            //     'created_by' => $id_user
            //   ]);

            //   $ttd->save();

            //   $query2 = "select qc_cpars.*,departments.department_name,employees.name,statuses.status_name, qc_cars.id as id_car FROM qc_cpars join departments on departments.id = qc_cpars.department_id join employees on qc_cpars.employee_id = employees.employee_id join statuses on qc_cpars.status_code = statuses.status_code join qc_cars on qc_cpars.cpar_no = qc_cars.cpar_no where qc_cpars.id='".$id."'";

            //   $cpars2 = db::select($query2);

            //   Mail::to($mailtoo)->send(new SendEmail($cpars2, 'cpar'));
              
            // }
            $cpars->save();
            if ($cpars->posisi == "bagian") {
              return redirect('/index/qc_report/verifikasicpar/'.$id)->with('status', 'CPAR Approved')->with('page', 'CPAR');
            }
            else{
              return redirect('/index/qc_report/verifikasicpar/'.$id)->with('status', 'CPAR Approved')->with('page', 'CPAR');
            }
          }
          else{
            return redirect('/index/qc_report/verifikasicpar/'.$id)->with('error', 'CPAR Not Approved')->with('page', 'CPAR');
          }          
      }

      public function unchecked(Request $request,$id)
      {
          $alasan = $request->get('alasan');

          $cpars = QcCpar::find($id);
          
          $cpars->alasan = $alasan;

          if ($cpars->posisi == "chief") {
            $cpars->checked_chief = null;              
          }
          else if ($cpars->posisi == "foreman") {
            $cpars->checked_foreman = null;              
          }
          else if ($cpars->posisi == "manager") {
            $cpars->checked_chief = null;              
            $cpars->checked_foreman = null;
          }
          else if ($cpars->posisi == "dgm") {
            $cpars->checked_manager = null;
            $cpars->checked_chief = null;              
            $cpars->checked_foreman = null;              
          }
          else if ($cpars->posisi == "gm") {
            $cpars->approved_dgm = null;
            $cpars->checked_manager = null;
            $cpars->checked_chief = null;              
            $cpars->checked_foreman = null; 
          }

          if ($cpars->staff != null) {
            $to = "staff";
            $cpars->posisi = "staff";
            $cpars->email_status = NULL;
          }
          else if ($cpars->leader != null) {
            $to = "leader";
            $cpars->posisi = "leader";
            $cpars->email_status = NULL;
          }


          $cpars->save();

          $query = "select qc_cpars.id, qc_cpars.cpar_no, qc_cpars.alasan, qc_cpars.posisi, qc_cpars.judul_komplain from qc_cpars where qc_cpars.id='".$id."'";
          $querycpar = db::select($query);

          $mailto = "select distinct email,phone from qc_cpars join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cpars.id='".$id."'";
          $mails = DB::select($mailto);

          foreach($mails as $mail){
            $mailtoo = $mail->email;
            $number = $mail->phone;
          }

          if (strpos($number, '/') !== false) {
            $number2 = explode("/", $number);
            $mailnumber = '62'.substr($number2[0],1);
          }else{
            $mailnumber = '62'.substr($number, 1);
          }

          


          Mail::to($mailtoo)->send(new SendEmail($querycpar, 'rejectcpar'));
          return redirect('/index/qc_report/verifikasicpar/'.$id)->with('error', 'CPAR Not Approved')->with('page', 'CPAR');
      }

      public function uncheckedqa(Request $request,$id)
      {
          $alasan = $request->get('alasan');

          $cpars = QcCpar::find($id);
          
          $cpars->alasan = $alasan; 

          if ($cpars->posisi == "QA2") {
            $cpars->posisi = "QA";
            $cpars->email_status = "SentQA";              
          }
          else if ($cpars->posisi == "QAmanager") {
            $cpars->posisi = "QA";
            $cpars->email_status = "SentQA";               
          }

          if ($cpars->staff != null) {
            $to = "staff";
          }
          else if ($cpars->leader != null) {
            $to = "leader";
          }

          $cpars->save();

          $query = "select qc_cpars.id, qc_cpars.cpar_no, qc_cpars.alasan, qc_cpars.posisi, qc_cpars.judul_komplain from qc_cpars where qc_cpars.id='".$id."'";
          $querycpar = db::select($query);

          $mailto = "select distinct email,phone from qc_cpars join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cpars.id='".$id."'";
          $mails = DB::select($mailto);

          foreach($mails as $mail){
            $mailtoo = $mail->email;
          }
          Mail::to($mailtoo)->send(new SendEmail($querycpar, 'rejectcpar'));
          return redirect('/index/qc_report/verifikasiqa/'.$id)->with('success', 'Verification Not Approved')->with('page', 'CPAR');
      }

      //Verifikasi CAR Oleh QA
      public function verifikasiqa($id)
      {
         $cpars = QcCpar::find($id);

         $cars = QcCar::select('qc_cars.*')
         ->join('qc_cpars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
         ->where('qc_cars.cpar_no','=',$cpars->cpar_no)
         ->get();

         $verifikasi = QcCpar::select('qc_verifikasis.id as id_ver','qc_verifikasis.keterangan','qc_verifikasis.tanggal','qc_verifikasis.status')
         ->join('qc_verifikasis','qc_verifikasis.cpar_no','=','qc_cpars.cpar_no')
         ->where('qc_verifikasis.cpar_no','=',$cpars->cpar_no)
         ->get();         
       
          return view('qc_report.verifikasi_qa', array(
            'cars' => $cars,
            'cpars' => $cpars,
            'verifikasi' => $verifikasi
          ))->with('page', 'Verifikasi QA');
      }

      public function close1(Request $request,$id)
      {
        try{
              $id_user = Auth::id();

              $cpars = QcCpar::find($id);
              $cpars->cost = $request->get('cost');
              $cpars->yokotenkai = $request->get('yokotenkai');
              $cpars->save();


             DB::transaction(function() use ($request, $id) {

              $ver = QcVerifikasi::where('cpar_no','=',$request->get("cpar_no"));
              $ver->delete();

              $jumlahVerif = $request->get('jumlahVerif');
              for ($i = 1; $i <= $jumlahVerif; $i++) {
                $tanggal = $request->get('tanggal'.$i);
                $status = $request->get('status'.$i);
                $keterangan = $request->get('verifikasi'.$i);
                $view = QcVerifikasi::create([
                  'cpar_no' => $request->get('cpar_no'),
                  'tanggal' => $tanggal,
                  'status' => $status,
                  'keterangan' => $keterangan,
                  'created_by' => Auth::id()
                ]);
                $view->save();
              }
                // $files=$request->file('gambar_'.$i);
              //   $nama=$files->getClientOriginalName();
              //   $files->move('files/gambar',$nama);
              //   QcVerifikasi::create([
              //       'cpar_no' => $request->get('cpar_no'),
              //       'foto' => $nama,
              //       'keterangan' => $keterangan,
              //       'created_by' => $id_user
              //   ]);
            });
            return redirect('/index/qc_report/verifikasiqa/'.$id)->with('status', 'Data Has Been Updated')->with('page', 'QA verification');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'CPAR already exist.')->with('page', 'QA verification');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'QA verification');
            }
          }
      }

      public function emailverification(Request $request,$id){

          $query = "select qc_cars.*, qc_cpars.lokasi, qc_cpars.kategori, qc_cpars.judul_komplain, qc_cpars.sumber_komplain, employees.name as pic_name, qc_cpars.id as id_cpar from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cars.pic = employees.employee_id where qc_cpars.id='".$id."'";

          $cars = db::select($query);

          $cpars = QcCpar::find($id);

          if ($cpars->posisi == "QA") {
            if ($cpars->staff != null ) {
            $to = "chief";
            }
            else if ($cpars->leader != null ) {
              $to = "foreman";
            }
          } 

          else if($cpars->posisi == "QA2") {
            $to = "manager";
          }
          

          $mailto = "select distinct email from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cpars.id='".$id."'";
          $mails = DB::select($mailto);

          foreach($mails as $mail){
            $mailtoo = $mail->email;
          }

          if($cpars->posisi == "QA") {
            $cpars->posisi = "QA2";
            $cpars->email_status = "SentQA2";
            $cpars->save();
          }
          else if ($cpars->posisi = "QA2"){
            $cpars->posisi = "QAmanager";
            // $cpars->email_status = "SentQAManager";
            $cpars->progress = "100";
            $cpars->save();
          }

          Mail::to($mailtoo)->send(new SendEmail($cars, 'car'));
          return redirect('/index/qc_report/verifikasiqa/'.$cpars->id)->with('status', 'E-mail has Been Sent')->with('page', 'QA verification');
      }

      public function close2(Request $request,$id)
      {

        try{
            $cpars = QcCpar::find($id);
            $cpars->posisi = "QAFIX";
            $cpars->status_code = "1";
            $cpars->save();

            // $query = "select qc_cars.*, qc_cpars.lokasi, qc_cpars.kategori, qc_cpars.sumber_komplain, employees.name as pic_name, qc_cpars.id as id_cpar from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cars.pic = employees.employee_id where qc_cpars.id='".$id."'";

            // $cars = db::select($query);

            // $mailto = "select distinct email from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cpars.manager = employees.employee_id join users on employees.employee_id = users.username where qc_cpars.id='".$id."'";
            // $mails = DB::select($mailto);

            // foreach($mails as $mail){
            //   $mailtoo = $mail->email;
            // }

            // Mail::to($mailtoo)->send(new SendEmail($cars, 'car'));
          
            return redirect('/index/qc_report/verifikasiqa/'.$cpars->id)->with('status', 'CPAR has been closed.')->with('page', 'QA verification');
          }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'CPAR already exist.')->with('page', 'QA verification');
            }
            else{
              return back()->with('error', $e->getMessage())->with('page', 'QA verification');
            }
          }
      }

      public function deleteVerifikasi(Request $request)
      {
          $pantry = QcVerifikasi::find($request->get("id"));
          $pantry->forceDelete();

          $response = array(
          'status' => true
        );
        return Response::json($response);
      }

      public function deletefiles(Request $request)
      {
        try{
          $cpar = QcCpar::find($request->get('idcpar'));
          $namafile = json_decode($cpar->file);
          // var_dump($namafile);
          // foreach ($car as $car) {
          //   $namafile = json_decode($car->file);
          // }
          // $namafilebaru[] = Null;
          $namafilebarubaru = "";
          foreach ($namafile as $key) {
            if ($key == $request->get('nama_file')) {
              File::delete('files/'.$key);
            }
            else{
                if (count($key) > 0) {
                  $namafilebarubaru = $key;
                  $namafilebaru[] = $namafilebarubaru;
                }
            }
          }
          
          if ($namafilebarubaru != "") {
            $cpar->file = json_encode($namafilebaru);
            $cpar->save();
          }
          else{
            $cpar->file = "";
            $cpar->save();
          }

          $response = array(
            'status' => true,
            'message' => 'Berhasil Hapus Data',
            // 'file' => $jumlah
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




      // Resumes
      public function resume(){
        $tglnow = date('Y-m-d');
        
        $fiscaly = db::select("select distinct fiscal_year from weekly_calendars");
 
        $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$tglnow'");

        foreach ($fy as $fys) {
            $fiscal = $fys->fiscal_year;
        }

        return view('qc_report.resume',  
          array('title' => 'CPAR CAR Resume '.$fiscal, 
                'title_jp' => '',
                'fy' => $fy,
                'fiscaly' => $fiscaly
              )
          )->with('page', 'CPAR Resume');

      }

      public function getResumeData(Request $request){

        $fiscal = $request->get('fy');

        if ($fiscal == "") {
            $tglnow = date('Y-m-d');
            $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$tglnow'");

            foreach ($fy as $fys) {
                $fiscal = $fys->fiscal_year;
            }
        }
        
        $data_status = db::select("select sum(case when qc_cpars.status_code = '5' then 1 else 0 end) as CPAR, SUM(case when qc_cpars.status_code = '6' then 1 else 0 end) as CAR, SUM(case when qc_cpars.status_code = '1' then 1 else 0 end) as closed, SUM(case when qc_cpars.status_code = '7' then 1 else 0 end) as QA, count(qc_cpars.status_code) as total from qc_cpars join statuses on statuses.status_code = qc_cpars.status_code LEFT JOIN weekly_calendars on qc_cpars.tgl_permintaan = weekly_calendars.week_date where fiscal_year='".$fiscal."' and qc_cpars.deleted_at is null");

        $kategori = db::select(" 
          select 
          sum(case when qc_cpars.kategori_komplain = 'Ketidaksesuaian Kualitas' then 1 else 0 end) as Kualitas,
          sum(case when qc_cpars.kategori_komplain = 'Non YMMJ' then 1 else 0 end) as NonYMMJ, 
          sum(case when qc_cpars.kategori_komplain = 'FG' then 1 else 0 end) as FG, 
          sum(case when qc_cpars.kategori_komplain = 'KD Parts' then 1 else 0 end) as KD,
          sum(case when qc_cpars.kategori_komplain = 'NG Jelas' then 1 else 0 end) as NG,
          SUM(case when qc_cpars.kategori_komplain = 'Market Claim' then 1 else 0 end) as Claim 
          from qc_cpars LEFT JOIN weekly_calendars on qc_cpars.tgl_permintaan = weekly_calendars.week_date where fiscal_year='".$fiscal."' and qc_cpars.deleted_at is null
        ");

        $ymmj = db::select(" 
          select count(qc_ymmjs.id) as jumlahymmj from qc_ymmjs LEFT JOIN weekly_calendars on qc_ymmjs.tgl_kejadian = weekly_calendars.week_date where fiscal_year='".$fiscal."' and qc_ymmjs.deleted_at is null
        ");


        $response = array(
          'status' => true,
          'data_status' => $data_status,
          'kategori' => $kategori,
          'ymmj' => $ymmj
        );
        return Response::json($response);

      }

      public function exportCPARAll(Request $request){
        $time = date('d-m-Y H;i;s');

        $tanggal = "";
        $kategori = "";

        if (strlen($request->get('date_from')) > 0)
        {

            $date_from = date('Y-m-d', strtotime($request->get('date_from')));
            $tanggal = "and tgl_permintaan = '".$date_from."'";

            if (strlen($request->get('date_to')) > 0) {

                $date_from = date('Y-m-d', strtotime($request->get('date_from')));
                $date_to = date('Y-m-d', strtotime($request->get('date_to')));
      
                $tanggal = "and tgl_permintaan >= '".$date_from."'";
                $tanggal = $tanggal . "and tgl_permintaan  <= '" .$date_to."'";
            }
        }

        if (strlen($request->get('kategori')) > 0)
        {
            $kategori = "and kategori_meeting = '".$request->get('kategori')."'";
        }


        $detail = db::select("SELECT DISTINCT qc_cpars.tgl_permintaan,qc_cpars.cpar_no, qc_cpars.kategori,qc_cpars.judul_komplain,qc_cpars.kategori_meeting,kategori_komplain,kategori_approval,sumber_komplain, department_name from qc_cpars JOIN departments on qc_cpars.department_id = departments.id WHERE qc_cpars.deleted_at IS NULL ".$tanggal." ".$kategori." order by qc_cpars.id ASC");

        $data = array(
            'detail' => $detail
        );

        ob_clean();

        Excel::create('Report CPAR '.$request->get('kategori'), function($excel) use ($data){
            $excel->sheet('Data', function($sheet) use ($data) {
              return $sheet->loadView('qc_report.cpar_excel', $data);
          });

          $lastrow = $excel->getActiveSheet()->getHighestRow();    
          $excel->getActiveSheet()->getStyle('A1:G'.$lastrow)->getAlignment()->setWrapText(true); 
            // $excel->getActiveSheet()->getColumnDimension('A:F')->setAutoSize(false);

        })->export('xlsx');
      }


    public function grafik_meeting()
    {
        $title = 'CPAR By Meeting';
        $title_jp = '??';

        return view('qc_report.grafik_meeting', array(
            'title' => $title,
            'title_jp' => $title_jp
        ))->with('page', 'CPAR Meeting');   
    }

    public function fetch_grafik_meeting(Request $request)
    {
        try {
              $tahun = date('Y');
              $tglfrom = $request->get('tglfrom');
              $tglto = $request->get('tglto');

              if ($tglfrom != null && $tglto != null) {
                  $tanggal = 'and DATE_FORMAT(tgl_permintaan,"%Y-%m") between "'.$tglfrom.'" and "'.$tglto.'"';
              }else{
                  $tanggal = '';
              }

              $kategori = $request->get('kategori');

              if ($kategori != null) {
                  $cat = json_encode($kategori);
                  $kat = str_replace(array("[","]"),array("(",")"),$cat);

                  $kate = 'and qc_cpars.kategori_komplain in'.$kat;
              }else{
                  $kate = '';
              }

              $departemen = $request->get('departemen');

              if ($departemen != null) {
                  $deptt = json_encode($departemen);
                  $dept = str_replace(array("[","]"),array("(",")"),$deptt);

                  $dep = 'and qc_cpars.department_id in'.$dept;
              } else {
                  $dep = '';
              }

              $sumber_komplain = $request->get('sumber_komplain');

              if ($sumber_komplain != null) {
                  $skkk = json_encode($sumber_komplain);
                  $skk = str_replace(array("[","]"),array("(",")"),$skkk);

                  $sk = 'and qc_cpars.sumber_komplain in'.$skk;
              } else {
                  $sk = '';
              }

              $sumber = $request->get('sumber');

              if ($sumber != null) {
                  $sumbb = json_encode($sumber);
                  $sumb = str_replace(array("[","]"),array("(",")"),$sumbb);

                  $sum = 'and qc_cpars.kategori_komplain in '.$sumb;
              } else {
                  $sum = '';
              }      

              $approval_cpar = $request->get('approval_cpar');

              if ($approval_cpar != null) {
                  $apprr = json_encode($approval_cpar);
                  $appr = str_replace(array("[","]"),array("(",")"),$apprr);

                  $app = 'and qc_cpars.kategori_approval in'.$appr;
              } else {
                  $app = '';
              }

              $meeting = $request->get('meeting');

              if ($meeting != null) {
                  $meett = json_encode($meeting);
                  $meet = str_replace(array("[","]"),array("(",")"),$meett);

                  $met = 'and qc_cpars.kategori_meeting in'.$meet;
              } else {
                  $met = '';
              }


            $resume = DB::select("
                SELECT
                  count( cpar_no ) AS jumlah,
                  monthname( tgl_permintaan ) AS bulan,
                  YEAR ( tgl_permintaan ) AS tahun,
                  sum( CASE WHEN qc_cpars.kategori_meeting = 'Open' THEN 1 ELSE 0 END ) AS meeting_open,
                  sum( CASE WHEN qc_cpars.kategori_meeting = 'CloseRevised' THEN 1 ELSE 0 END ) AS meeting_revisi,
                  sum( CASE WHEN qc_cpars.kategori_meeting = 'Close' AND status_code = '7' THEN 1 ELSE 0 END ) AS meeting_close_qa,
                  sum( CASE WHEN qc_cpars.kategori_meeting = 'Close' AND status_code = '1' THEN 1 ELSE 0 END ) AS meeting_close,
                  sum( CASE WHEN qc_cpars.kategori_meeting IS NULL THEN 1 ELSE 0 END ) AS new_cpar
                FROM
                  qc_cpars
                where qc_cpars.deleted_at is null ".$tanggal." ".$kate." ".$dep." ".$sk." ".$sum." ".$app." ".$met."
                GROUP BY
                  bulan,
                  tahun 
                ORDER BY
                  tahun,
                  MONTH (tgl_permintaan) ASC
              ");

            $detail = DB::select("SELECT
                qc_cpars.*,departments.department_name
                FROM
                `qc_cpars`
                JOIN 
                departments
                ON qc_cpars.department_id = departments.id
                where qc_cpars.deleted_at is null ".$tanggal." ".$kate." ".$dep." ".$sk." ".$sum." ".$app." ".$met."
            ");
            
            $response = array(
                'status' => true,
                'resume' => $resume,
                'detail' => $detail
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


    public function market_claim($id){

        $title = 'Market Claim';
        $title_jp = '';

        return view('qc_report.grafik_market_claim', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'id' => $id
        ))->with('page', 'Market Claim')
        ->with('head', 'Market Claim');
    }

    public function fetch_market_claim(Request $request){
        $fy = $request->get('fy');
        $id = $request->get('id');

        $now_calendar = WeeklyCalendar::where('week_date', date('Y-m-d'))->first();
        if(strlen($fy) <= 0){
            $fy = $now_calendar->fiscal_year;
        }

        $start = WeeklyCalendar::where('fiscal_year', $fy)->orderBy('week_date', 'ASC')->first();
        $end = WeeklyCalendar::where('fiscal_year', $fy)->orderBy('week_date', 'DESC')->first();
        $end_resume = date("Y-m-d", strtotime("last day of previous month"));

        if($fy != $now_calendar->fiscal_year){
            $end_resume = $end->week_date;
        }

        $data_bulan = db::select("SELECT
            count( cpar_no ) AS jumlah,
            monthname( cpar_date ) AS bulan,
            YEAR ( cpar_date ) AS tahun 
        FROM
            qc_cpar_claims 
        WHERE
            DATE_FORMAT( cpar_date, '%Y-%m-%d' ) BETWEEN '".$start->week_date."' 
            AND '".$end->week_date."' 
            AND sec = '".$id."'
        GROUP BY
            bulan,
            tahun 
        ORDER BY
            tahun,
            MONTH ( cpar_date ) ASC");

        $data_defect = db::select("
        SELECT
            count( id ) AS jumlah,
            category,
            hpl
        FROM
            qc_cpar_claims 
        WHERE
            DATE_FORMAT( cpar_date, '%Y-%m-%d' ) BETWEEN '".$start->week_date."' 
            AND '".$end->week_date."' 
            AND sec = '".$id."'
        GROUP BY
             hpl, category");

        $data_detail_defect = db::select("
        SELECT
                        hpl,
            count( id ) AS jumlah,
            category,
                        defect
        FROM
            qc_cpar_claims 
        WHERE
            DATE_FORMAT( cpar_date, '%Y-%m-%d' ) BETWEEN '".$start->week_date."' 
            AND '".$end->week_date."' 
            AND sec = '".$id."'
        GROUP BY
            defect, hpl, category
                ORDER BY hpl, category, jumlah DESC
        ");

        $response = array(
            'status' => true,
            'fy' => $fy,
            'data_bulan' => $data_bulan,
            'data_defect' => $data_defect,
            'data_detail_defect' => $data_detail_defect
        );
        return Response::json($response);
    }


    public function indexMarketClaim()
    {   
        $cpars = DB::table('qc_cpar_claims')
        ->orderBy('qc_cpar_claims.id','DESC')
        ->get();

        $no_cpar = QcCpar::select('cpar_no')
        ->distinct()
        ->whereNull('deleted_at')
        ->orderBy('qc_cpars.id','DESC')
        ->get();
        
        return view('qc_report.index_market_claim', array(
            'cpars' => $cpars,
            'no_cpar' => $no_cpar
        ))->with('page', 'Market Claim');
    }

    function filterMarketClaim(Request $request)
    {
        $marketClaimTable = DB::table('qc_cpar_claims')
        ->select('*')
        ->whereNull('qc_cpar_claims.deleted_at');

        $marketClaimTable = $marketClaimTable->orderBy('qc_cpar_claims.id', 'DESC');
        $market_claims = $marketClaimTable->get();

        return DataTables::of($market_claims)

        ->editColumn('cpar_date',function($market_claims){
            return date('d-M-Y', strtotime($market_claims->cpar_date));
          })

        ->rawColumns(['cpar_date' => 'cpar_date'])
        ->make(true);
    }


    public function createMarketClaim(Request $request)
    {
        try
        {
             $now = date('Y-m-d H:i:s');


            $tgl_cpar = $request->get('tgl_cpar');
            $date_cpar = str_replace('/', '-', $tgl_cpar);

            $insert = db::table('qc_cpar_claims')
            ->insert([
                'sec' => $request->get('section'),
                'cpar_no' => $request->get('cpar_no'),
                'cpar_date' => date("Y-m-d", strtotime($date_cpar)),
                'hpl' => $request->get('hpl'),
                'type' => $request->get('type'),
                'subsidiary' => $request->get('subsidiary'),
                'pic' => $request->get('pic'),
                'defect' => $request->get('defect'),
                'location' => $request->get('location'),
                'category' => $request->get('category'),
                'qty' => $request->get('defect_qty'),
                'created_by' => strtoupper(Auth::user()->username),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $response = array(
              'status' => true,
              'insert' => $insert
            );
            return Response::json($response);
        }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'message' => $e->getMessage(),
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'message' => $e->getMessage(),
            );
             return Response::json($response);
           }
        }
    }

}