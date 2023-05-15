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
use App\QcCar;
use App\QcCpar;
use App\QcVerifikator;
use App\QcCarDocument;
use App\Department;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use File;
use App\QcTtdCoba;

class QcCarController extends Controller
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
    }

      public function index()
      {
       $cars = QcCar::select('qc_cars.*','qc_cpars.kategori','qc_cpars.lokasi','qc_cpars.tgl_permintaan','qc_cpars.tgl_balas','qc_cpars.sumber_komplain','qc_cpars.judul_komplain','departments.department_name','employees.name','statuses.status_name')
       ->join('qc_cpars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
       ->join('departments','qc_cpars.department_id','=','departments.id')
       ->join('employees','qc_cpars.employee_id','=','employees.employee_id')
       ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
       ->orderBy('qc_cars.id','desc')
       ->get();

       // $id = Auth::id();

       // //get departemen by login

       // $user = "select department_name from users join mutation_logs on users.username = mutation_logs.employee_id join departments on departments.department_name = mutation_logs.department where users.id=14 and valid_to IS NULL;";
       // $users = DB::select($user);

       $departments = Department::select('departments.id', 'departments.department_name')->get();

        // $materials = Material::select('materials.material_number', 'materials.material_description')->get();
        $statuses = QcCpar::select('statuses.status_code','statuses.status_name')
        ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
        ->distinct()
        ->get();

       return view('qc_car.index', array(
        'cars' => $cars,
        'departments' => $departments,
        'statuses' => $statuses

       ))->with('page', 'CAR');
    }

    public function filter_data(Request $request){
        $kategori = $request->get('kategori');
        $department_id = $request->get('department_id');
        $status = $request->get('status');

        $cars = QcCar::select('qc_cars.*','qc_cpars.kategori','qc_cpars.lokasi','qc_cpars.tgl_permintaan','qc_cpars.tgl_balas','qc_cpars.sumber_komplain','qc_cpars.judul_komplain','departments.department_name','employees.name','statuses.status_name')
         ->join('qc_cpars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
         ->join('departments','qc_cpars.department_id','=','departments.id')
         ->join('employees','qc_cpars.employee_id','=','employees.employee_id')
         ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
         ->orderBy('qc_cars.id','desc')
         ->whereNull('qc_cpars.deleted_at');


          if(strlen($request->get('kategori')) > 0){
            $cars = $cars->where('qc_cpars.kategori', '=', $request->get('kategori'));
          }

          if(strlen($request->get('department_id')) > 0){
            $cars = $cars->where('qc_cpars.department_id', '=', $request->get('department_id'));
          }

          if(strlen($request->get('status_code')) > 0){
            $cars = $cars->where('qc_cpars.status_code', '=', $request->get('status_code'));
          }

          $carfix = $cars->get();

          $departments = Department::select('departments.id', 'departments.department_name')->get();

          // $materials = Material::select('materials.material_number', 'materials.material_description')->get();
          $statuses = QcCpar::select('statuses.status_code','statuses.status_name')
          ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
          ->distinct()
          ->get();

         return view('qc_car.index', array(
          'cars' => $carfix,
          'departments' => $departments,
          'statuses' => $statuses

         ))->with('page', 'CAR');
    }

    public function detail($idcar)
    {
      $emp_id = Auth::user()->username;
      $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

       $cars = QcCar::find($idcar);

       $cpar = QcCar::select('qc_cpars.cpar_no','qc_cpars.id','qc_cpars.kategori','qc_cpars.employee_id','qc_cpars.lokasi','qc_cpars.judul_komplain','qc_cpars.sumber_komplain')
       ->join('qc_cpars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
       ->where('qc_cpars.cpar_no','=',$cars->cpar_no)
       ->get();

       $id = Auth::id();

       //get departemen by login

       $user = "select department_name from users join mutation_logs on users.username = mutation_logs.employee_id join departments on departments.department_name = mutation_logs.department where users.id=14 and valid_to IS NULL;";
       $users = DB::select($user);


       $dept = "select department_name from qc_cpars join departments on departments.id = qc_cpars.department_id where qc_cpars.cpar_no='".$cars->cpar_no."'";
       $departemen = DB::select($dept); 


       if ($departemen[0]->department_name == "Production Engineering Department") {

        $getpic = "select employee_id, `name`, department from employee_syncs where department = '".$departemen[0]->department_name."' and (position like '%chief%' or position like '%foreman%') and end_date is null";

         $pic = DB::select($getpic);
       }
       
       else if ($departemen[0]->department_name == "Educational Instrument (EI) Department"){
        $getpic = "select employee_id, `name`, department from employee_syncs where department = '".$departemen[0]->department_name."' and (position LIKE '%staff%' OR position LIKE '%chief%' OR position LIKE '%coordinator%') and end_date is null";

         $pic = DB::select($getpic);
       }

       else if ($departemen[0]->department_name == "Procurement Department"){
        $getpic = "select employee_id, `name`, department from employee_syncs where (department = 'Procurement Department' or department = 'Purchasing Control Department') and (position like '%staff%' or position like '%foreman%' or position like '%Coordinator%') and end_date is null";
        $pic = DB::select($getpic);
       }

       else if ($departemen[0]->department_name == "Purchasing Control Department"){
        $getpic = "select employee_id, `name`, department from employee_syncs where (department = 'Procurement Department' or department = 'Purchasing Control Department') and (position like '%staff%' or position like '%foreman%' or position like '%Coordinator%') and end_date is null";
        $pic = DB::select($getpic);

       }

       else{
         $getpic = "select employee_id, `name`, department from employee_syncs where department = '".$departemen[0]->department_name."' and (position like '%staff%' or position LIKE '%chief%' or position like '%foreman%' and end_date is null)";

         $pic = DB::select($getpic);
       }



       $cfm = "select employees.employee_id, employees.name, mutation_logs.department, promotion_logs.position from employees join mutation_logs on employees.employee_id = mutation_logs.employee_id join promotion_logs on employees.employee_id = promotion_logs.employee_id where mutation_logs.department='".$departemen[0]->department_name."' and (promotion_logs.position like '%chief%' or promotion_logs.position like '%foreman%' or promotion_logs.position = 'manager') and mutation_logs.valid_to IS NULL and promotion_logs.valid_to IS NULL";

       $choosecf = DB::select($cfm);

       // $m = "select employees.employee_id, employees.name, mutation_logs.department from employees join mutation_logs on employees.employee_id = mutation_logs.employee_id join promotion_logs on employees.employee_id = promotion_logs.employee_id where mutation_logs.department='".$departemen[0]->department_name."' and promotion_logs.position = 'manager' and mutation_logs.valid_to IS NULL and promotion_logs.valid_to IS NULL";

       // $choosem = DB::select($m);

       // if($cpar[0]->kategori == "Internal"){
          
       //    $getpic = "select employees.employee_id, employees.name, mutation_logs.department from employees join mutation_logs on employees.employee_id = mutation_logs.employee_id join promotion_logs on employees.employee_id = promotion_logs.employee_id where mutation_logs.department='".$departemen[0]->department_name."' and promotion_logs.position like '%foreman%' and mutation_logs.valid_to IS NULL and promotion_logs.valid_to IS NULL";

       //    $pic = DB::select($getpic);
   
       //    if($pic == NULL){

       //      $getpic = "select employees.employee_id, employees.name, mutation_logs.department from employees join mutation_logs on employees.employee_id = mutation_logs.employee_id join promotion_logs on employees.employee_id = promotion_logs.employee_id where mutation_logs.department='".$departemen[0]->department_name."' and promotion_logs.grade_name like '%staff%' and mutation_logs.valid_to IS NULL and promotion_logs.valid_to IS NULL";

       //      $pic = DB::select($getpic);

       //    }

       // } else if($cpar[0]->kategori == "Eksternal" || $cpar[0]->kategori == "Supplier"){

       //    $getpic = "select employees.employee_id, employees.name, mutation_logs.department from employees join mutation_logs on employees.employee_id = mutation_logs.employee_id join promotion_logs on employees.employee_id = promotion_logs.employee_id where mutation_logs.department='".$departemen[0]->department_name."' and promotion_logs.grade_name like '%staff%' and mutation_logs.valid_to IS NULL and promotion_logs.valid_to IS NULL";

       //    $pic = DB::select($getpic);
       // }

       $documents = DB::SELECT("select qc_car_documents.* from qc_cars join qc_car_documents on qc_cars.cpar_no = qc_car_documents.cpar_no where qc_cars.id='".$idcar."'");

        return view('qc_car.detail', array(
          'cars' => $cars,
          'cpar' => $cpar,
          'users' => $users,
          'idcar' => $idcar,
          'pic' => $pic,
          'cfm' => $choosecf,
          'documents' => $documents
        ))->with('page', 'CAR');
    }

    public function detail_action(Request $request, $id)
    {
       try{

          $cars = QcCar::find($id);

          $tinjauanall= $request->get('tinjauan4m');
          if ($tinjauanall == null ) {
            $carstinjauan = 0;
          }
          else{
            foreach ($tinjauanall as $carstinjauan) {
               $carstinjauan = implode(',', $request->tinjauan4m);          
            }
          }

           $id_user = Auth::id();

           $files=array();
              
            // $file = new QcCpar();
            if ($request->file('files') != NULL) {
              if($files=$request->file('files')) {
                foreach($files as $file){
                  $nama=$file->getClientOriginalName();
                  $file->move('files/car',$nama);
                  $data[]=$nama;              
                }
              }
              $cars->file=json_encode($data);           
            }

           $cars->cpar_no = $request->get('cpar_no');
           $cars->tinjauan = $carstinjauan;
           $cars->deskripsi = $request->get('deskripsi');
           $cars->tindakan = $request->get('tindakan');
           $cars->penyebab = $request->get('penyebab');
           $cars->perbaikan = $request->get('perbaikan');
           $cars->pencegahan = $request->get('pencegahan');
           // if ($request->get('proses_serupa') == "Ada") {
           // $cars->perubahan_dokumen = $request->get('perubahan_dokumen');
           $cars->proses_serupa = $request->get('proses_serupa');
           //   $cars->proses_serupa_detail = $request->get('proses_serupa_detail');
           // }else{
           //   $cars->proses_serupa = $request->get('proses_serupa');
           //   $cars->proses_serupa_detail = null;
           // }
           $cars->created_by = $id_user;
           
           $cars->save();


           $cpars = QcCpar::select('kategori_meeting')
           ->where('cpar_no','=',$request->get('cpar_no'))
           ->first();

           if ($cpars->kategori_meeting == "CloseRevised") {
                $car_isi = QcCar::where('cpar_no',$request->get('cpar_no'))->first();

                if ($car_isi->deskripsi == null || $car_isi->tindakan == null || $car_isi->penyebab == null || $car_isi->perbaikan == null) {
                   $response = array(
                      'status' => false,
                      'message' => "Lengkapi Dulu CAR nya",
                  );
                  return Response::json($response);
                }

                $cpar = QcCpar::select('qc_cpars.cpar_no','qc_cpars.id','qc_cpars.status_code','qc_cpars.posisi', 'qc_cpars.kategori_meeting')
                 ->join('qc_cars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
                 ->where('qc_cpars.cpar_no','=',$request->get('cpar_no'))
                 ->get();

                foreach ($cpar as $cpar) {
                    $cpar->kategori_meeting = "Close";
                    $cpar->status_code = "7";
                    $cpar->posisi = "QA";
                    $cpar->save();
                }

                $carssss = QcCar::where('cpar_no',$request->get('cpar_no'))
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

            $lop = $request->get('lop');

            $tujuan_upload = 'files/car/document';  
            for ($i = 1;$i <= $lop;$i++)
            {
              $detail = $request->get('detail_'.$i);
              $nomor_dokumen = $request->get('dokumen_'.$i);
              $dokumen = $request->get('jenis_dokumen_'.$i);
              $due_date = $request->get('due_date_'.$i);
              $file = $request->file('file_'.$i);

              $data2 = QcCarDocument::firstOrNew([
                  'cpar_no' => $request->get('cpar_no'),
                  'detail' => $detail,
              ]);

              $data2->nomor_dokumen = $nomor_dokumen;
              $data2->dokumen = $dokumen;
              $data2->due_date = $due_date;

              if ($file) {
                $nama = $file->getClientOriginalName();
                $filename = pathinfo($nama, PATHINFO_FILENAME);
                $extension = pathinfo($nama, PATHINFO_EXTENSION);
                $filename = md5($filename.date('YmdHisa')).'.'.$extension;
                $file->move($tujuan_upload,$filename);
                $data2->file = $filename;
              }
              $data2->save();

            }

            if($request->get('index') != null){
              $tujuan_upload = 'files/car/document';

              for ($i = 1;$i <= $request->get('index');$i++)
              {
                $detail = $request->get('detail_'.$i);
                $file = $request->file('file_'. $i);

                if ($file) {
                  $nama = $file->getClientOriginalName();

                  $filename = pathinfo($nama, PATHINFO_FILENAME);
                  $extension = pathinfo($nama, PATHINFO_EXTENSION);
                  $filename = md5($filename.date('YmdHisa')).'.'.$extension;

                  $file->move($tujuan_upload,$filename);

                  $update = QcCarDocument::where('cpar_no',$request->get('cpar_no'))
                  ->where('detail',$detail)
                  ->update(['file' => $filename]);
                }
              }
            }

            if ($request->get('proses_serupa') == "Ada") {
              if($request->get('lop_proses') != null){
                $tujuan_upload_all = 'files/car/yokotenkai';
                $detail_all = array();
                $file_all = array();

                for ($i = 1;$i <= $request->get('lop_proses');$i++)
                {
                  $detail = $request->get('proses_serupa_detail_'.$i);
                  $file = $request->file('proses_serupa_file_'. $i);

                  if ($file) {
                    $nama = $file->getClientOriginalName();
                    $filename = pathinfo($nama, PATHINFO_FILENAME);
                    $extension = pathinfo($nama, PATHINFO_EXTENSION);
                    $filename = md5($filename.date('YmdHisa')).'.'.$extension;

                    $file->move($tujuan_upload_all,$filename);

                    array_push($detail_all, $detail);
                    array_push($file_all, $filename);
                  }
                  // else{
                  //   array_push($detail_all, $detail);

                  //   $update = QcCar::where('cpar_no',$request->get('cpar_no'))
                  //   ->update([
                  //     'proses_serupa_detail' => $detail_all
                  //   ]);
                  // }
                }
                  $update = QcCar::where('cpar_no',$request->get('cpar_no'))
                    ->update([
                      'proses_serupa_detail' => json_encode($detail_all),
                      'proses_serupa_foto' => json_encode($file_all)
                    ]);
              }
            }else{
              $update = QcCar::where('cpar_no',$request->get('cpar_no'))
                    ->update([
                      'proses_serupa_detail' => null,
                      'proses_serupa_foto' => null
                    ]);
            }

            return redirect('/index/qc_car/detail/'.$cars->id)->with('status', 'CAR data has been Inserted.')->with('page', 'CAR');
         }
         catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
              return back()->with('error', 'CAR error.')->with('page', 'CAR');
          }
          else{
              return back()->with('error', $e->getMessage())->with('page', 'CAR');
          }
        }
    }

    public function delete_document(Request $request)
    {
      $car_doc = QcCarDocument::find($request->get("id"));
      $car_doc->forceDelete();

      $response = array(
        'status' => true
      );
      return Response::json($response);
    }

    public function print_car($id)
    {
      $cars = QcCar::select('qc_cars.*','qc_cpars.kategori','mutation_logs.section','qc_cpars.lokasi','qc_cpars.tgl_permintaan','qc_cpars.tgl_balas','qc_cpars.sumber_komplain','departments.department_name','pic.name as picname','manager.name as managername','dgm.name as dgmname','gm.name as gmname','statuses.status_name','chief.name as chiefname','foreman.name as foremanname','coordinator.name as coordinatorname')
      ->join('qc_cpars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
      ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
      ->join('qc_verifikators','qc_cpars.department_id','=','qc_verifikators.department_id')
      ->join('departments','qc_verifikators.department_id','=','departments.id')
      ->join('mutation_logs','qc_cpars.employee_id','=','mutation_logs.employee_id')
      ->leftjoin('employees as chief','qc_verifikators.verifikatorchief','=','chief.employee_id')
      ->leftjoin('employees as foreman','qc_verifikators.verifikatorforeman','=','foreman.employee_id')
      ->leftjoin('employees as coordinator','qc_verifikators.verifikatorcoordinator','=','coordinator.employee_id')
      ->join('employees as manager','qc_cpars.employee_id','=','manager.employee_id')
      ->join('employees as pic','qc_cars.pic','=','pic.employee_id')
      ->join('employees as dgm','qc_cpars.dgm','=','dgm.employee_id')
      ->join('employees as gm','qc_cpars.gm','=','gm.employee_id')
      ->whereNull('mutation_logs.valid_to')
      ->where('qc_cars.id','=',$id)
      ->get();

      $pdf = \App::make('dompdf.wrapper');
      $pdf->getDomPDF()->set_option("enable_php", true);
      $pdf->setPaper('legal', 'potrait');
      $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

      $pdf->loadView('qc_car.print_car', array(
        'cars'=>$cars
    ));
      
      return $pdf->stream("CAR ".$id. ".pdf");
    }

     public function print_car2($id)
    {
      $car = QcCar::find($id);

      $cars = QcCar::select('qc_cars.*','qc_cpars.kategori','mutation_logs.section','qc_cpars.lokasi','qc_cpars.tgl_permintaan','qc_cpars.tgl_balas','qc_cpars.sumber_komplain','qc_cpars.kategori_komplain','qc_cpars.kategori_approval','departments.department_name','pic.name as picname','manager.name as managername','dgm.name as dgmname','gm.name as gmname','statuses.status_name','chief.name as chiefname','foreman.name as foremanname','coordinator.name as coordinatorname','qc_cpars.posisi as posisi_cpar','staffqa.name as staffqaname','leaderqa.name as leaderqaname','chiefqa.name as chiefqaname','foremanqa.name as foremanqaname','managerqa.name as managerqaname','qc_cpars.staff','qc_cpars.leader','qc_cpars.yokotenkai')
      ->join('qc_cpars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
      ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
      ->join('qc_verifikators','qc_cpars.department_id','=','qc_verifikators.department_id')
      ->join('departments','qc_verifikators.department_id','=','departments.id')
      ->join('mutation_logs','qc_cpars.employee_id','=','mutation_logs.employee_id')
      ->leftjoin('employees as chief','qc_verifikators.verifikatorchief','=','chief.employee_id')
      ->leftjoin('employees as foreman','qc_verifikators.verifikatorforeman','=','foreman.employee_id')
      ->leftjoin('employees as coordinator','qc_verifikators.verifikatorcoordinator','=','coordinator.employee_id')
      ->join('employees as manager','qc_cpars.employee_id','=','manager.employee_id')
      ->join('employees as pic','qc_cars.pic','=','pic.employee_id')
      ->join('employees as dgm','qc_cpars.dgm','=','dgm.employee_id')
      ->join('employees as gm','qc_cpars.gm','=','gm.employee_id')
      ->leftjoin('employees as staffqa','qc_cpars.staff','=','staffqa.employee_id')
      ->leftjoin('employees as leaderqa','qc_cpars.leader','=','leaderqa.employee_id')
      ->leftjoin('employees as chiefqa','qc_cpars.chief','=','chiefqa.employee_id')
      ->leftjoin('employees as foremanqa','qc_cpars.foreman','=','foremanqa.employee_id')
      ->leftjoin('employees as managerqa','qc_cpars.manager','=','managerqa.employee_id')
      ->whereNull('mutation_logs.valid_to')
      ->where('qc_cars.id','=',$id)
      ->get();

      $verifikasi = QcCar::select('qc_verifikasis.id as id_ver','qc_verifikasis.keterangan','qc_verifikasis.status','qc_verifikasis.tanggal')
         ->join('qc_verifikasis','qc_verifikasis.cpar_no','=','qc_cars.cpar_no')
         ->where('qc_verifikasis.cpar_no','=',$car->cpar_no)
         ->get(); 

      // $carttds = QcCar::select('qc_cars.*','qc_ttd_cobas.ttd_car')
      //       ->leftjoin('qc_ttd_cobas','qc_cars.cpar_no','=','qc_ttd_cobas.cpar_no')
      //       ->where('qc_cars.id','=',$id)
      //       ->get();

      $documents = DB::SELECT("select qc_car_documents.* from qc_cars join qc_car_documents on qc_cars.cpar_no = qc_car_documents.cpar_no where qc_cars.id='".$id."'");

      $carttds = DB::SELECT("select * from qc_cars left join qc_ttd_cobas on qc_cars.cpar_no = qc_ttd_cobas.cpar_no where qc_cars.id='".$id."'");

       return view('qc_car.print2_car', array(
          'cars' => $cars,
          'verifikasi' => $verifikasi,
          'carss' =>  $carttds,
          'documents' => $documents
        ))->with('page', 'CAR');
      
      return $pdf->stream("CAR ".$id. ".pdf");
    }

    public function coba_print($id)
    {
      $cars = QcCar::select('qc_cars.*','qc_cpars.kategori','qc_cpars.lokasi','mutation_logs.section','qc_cpars.tgl_permintaan','qc_cpars.tgl_balas','qc_cpars.sumber_komplain','departments.department_name','employees.name','statuses.status_name')
      ->join('qc_cpars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
      ->join('departments','qc_cpars.department_id','=','departments.id')
      ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
      ->join('employees','qc_cpars.employee_id','=','employees.employee_id')
      ->join('mutation_logs','qc_cpars.employee_id','=','mutation_logs.employee_id')
      ->where('qc_cars.id','=',$id)
      ->whereNull('mutation_logs.valid_to')
      ->get();

      return view('qc_car.print_car', array(
        'cars' => $cars,
      ))->with('page', 'CPAR');
    }

    public function create_pic(Request $request,$id)
    {
        try{
            $cars = QcCar::find($id);
            $cars->pic = $request->get('pic'); 
            $cars->tgl_car = date('Y-m-d');
            $cars->progress = "20";
            $cars->save();

            $cpar = QcCpar::select('qc_cpars.cpar_no','qc_cpars.id','qc_cpars.status_code','qc_cpars.kategori')
           ->join('qc_cars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
           ->where('qc_cpars.cpar_no','=',$cars->cpar_no)
           ->get();

            foreach ($cpar as $cpar) {
              $idcpar = $cpar->id;
              // $cpar->status_code = "6";
              $cpar->save();
            }

            // $query = "select qc_cpars.*,departments.department_name,employees.name,statuses.status_name, qc_cars.id as id_car FROM qc_cpars join departments on departments.id = qc_cpars.department_id join employees on qc_cpars.employee_id = employees.employee_id join statuses on qc_cpars.status_code = statuses.status_code join qc_cars on qc_cpars.cpar_no = qc_cars.cpar_no where qc_cpars.id='".$idcpar."'";

             $query = "select qc_cpars.*, destination_name, vendors.name as vendorname ,qc_cpar_items.part_item, qc_cpar_items.sample_qty, qc_cpar_items.defect_qty , qc_cars.id as id_car, qc_cpar_items.detail_problem,departments.department_name,employee_syncs.name,statuses.status_name, material_description FROM qc_cpars join departments on departments.id = qc_cpars.department_id join employee_syncs on qc_cpars.employee_id = employee_syncs.employee_id join statuses on qc_cpars.status_code = statuses.status_code join qc_cpar_items on qc_cpars.cpar_no = qc_cpar_items.cpar_no join material_qa_complaints on material_qa_complaints.material_number = qc_cpar_items.part_item left join destinations on qc_cpars.destination_code = destinations.destination_code left join vendors on qc_cpars.vendor = vendors.vendor join qc_cars on qc_cpars.cpar_no = qc_cars.cpar_no where qc_cpars.id ='".$idcpar."'";

            $cparemail = db::select($query);

            // kirim email
            $mailpic = "select qc_cars.pic,email FROM qc_cars join users on qc_cars.pic = users.username where qc_cars.id='".$id."'";
            $mailto = db::select($mailpic);

            foreach($mailto as $mail){
              $mailtoo = $mail->email;
            }

            $cars->email_send_date = date('Y-m-d');

            if($cars->car_cpar->kategori == "Eksternal" || $cars->car_cpar->kategori == "Supplier") {
              $cars->email_status = "SentStaff";            
              $cars->posisi = "staff";                
            }else if ($cars->car_cpar->kategori == "Internal") {
              $cars->email_status = "SentForeman";            
              $cars->posisi = "foreman";                
            }

            // get CPAR Dept
            // $dept = "select department_name from qc_cpars join departments on departments.id = qc_cpars.department_id where qc_cpars.cpar_no='".$cars->cpar_no."'";

            // $departemen = DB::select($dept);

            // // chief/foreman/coordinator berdasarkan departemen

            // if ($departemen[0]->department_name == "assembly (wi-a)") {
            //     if($cars->car_cpar->kategori == "Eksternal" || $cars->car_cpar->kategori == "Supplier") {
            //         $position = "chief";
            //     }else if ($cars->car_cpar->kategori == "Internal") {
            //         $position = "foreman";
            //     }
            //     $posisi2 = "select distinct employees.employee_id, employees.name, mutation_logs.department from employees join mutation_logs on employees.employee_id = mutation_logs.employee_id join promotion_logs on employees.employee_id = promotion_logs.employee_id where mutation_logs.department = '".$departemen[0]->department_name ."' and promotion_logs.position='".$position."' and mutation_logs.valid_to IS NULL and promotion_logs.valid_to IS NULL;";

            //     $gettemp = DB::select($posisi2);

            //     $cars->$position = $gettemp[0]->employee_id;
            // }
            // else if($departemen[0]->department_name == "welding-surface treatment (wi-wst)"){
            //     $posisi2 = "select distinct employees.employee_id, employees.name, mutation_logs.department from employees join mutation_logs on employees.employee_id = mutation_logs.employee_id join promotion_logs on employees.employee_id = promotion_logs.employee_id where mutation_logs.department = '".$departemen[0]->department_name ."' and promotion_logs.position='foreman' and mutation_logs.valid_to IS NULL and promotion_logs.valid_to IS NULL LIMIT 1";

            //     $gettemp = DB::select($posisi2);

            //     $cars->foreman = $gettemp[0]->employee_id;

            // }
            // else if($departemen[0]->department_name == "parts process (wi-pp)"){

            //   $posisi2 = "select distinct employees.employee_id, employees.name, mutation_logs.department from employees join mutation_logs on employees.employee_id = mutation_logs.employee_id join promotion_logs on employees.employee_id = promotion_logs.employee_id where mutation_logs.department = '".$departemen[0]->department_name ."' and promotion_logs.position='foreman' and mutation_logs.valid_to IS NULL and promotion_logs.valid_to IS NULL";

            //     $gettemp = DB::select($posisi2);

            //     $cars->foreman = $gettemp[0]->employee_id;

            // }
            // else if($departemen[0]->department_name == "purchasing"){

            //   $posisi2 = "select distinct employees.employee_id, employees.name, mutation_logs.department from employees join mutation_logs on employees.employee_id = mutation_logs.employee_id join promotion_logs on employees.employee_id = promotion_logs.employee_id where mutation_logs.department =  '".$departemen[0]->department_name ."' and promotion_logs.grade_name='coordinator' and mutation_logs.valid_to IS NULL and promotion_logs.valid_to IS NULL;";
            //     $gettemp = DB::select($posisi2);

            //     $cars->coordinator= $gettemp[0]->employee_id;

            // }
            // else if($departemen[0]->department_name == "educational instrument (ei)"){

            //   $posisi2 = "select distinct employees.employee_id, employees.name, mutation_logs.department,promotion_logs.position from employees join mutation_logs on employees.employee_id = mutation_logs.employee_id join promotion_logs on employees.employee_id = promotion_logs.employee_id where mutation_logs.department = '".$departemen[0]->department_name ."' and promotion_logs.position='Manager' and mutation_logs.valid_to IS NULL and promotion_logs.valid_to IS NULL;";
            //     $gettemp = DB::select($posisi2);
                
            //     $cars->chief= $gettemp[0]->employee_id;

            // }
            // else if($departemen[0]->department_name == "logistic"){
            //     if($cars->car_cpar->kategori == "Eksternal" || $cars->car_cpar->kategori == "Supplier") {
            //         $position = "chief";
            //     }else if ($cars->car_cpar->kategori == "Internal") {
            //         $position = "foreman";
            //     }
            //     $posisi2 = "select distinct employees.employee_id, employees.name, mutation_logs.department from employees join mutation_logs on employees.employee_id = mutation_logs.employee_id join promotion_logs on employees.employee_id = promotion_logs.employee_id where mutation_logs.department = '".$departemen[0]->department_name ."' and promotion_logs.position='".$position."' and mutation_logs.valid_to IS NULL and promotion_logs.valid_to IS NULL;";

            //     $gettemp = DB::select($posisi2);

            //     $cars->$position = $gettemp[0]->employee_id;
            // }

            $cars->save();

            Mail::to($mailtoo)->send(new SendEmail($cparemail, 'cpar'));

            $response = array(
              'status' => true,
              'cars' => $cars,
            );
            return Response::json($response);
        }
          catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'parts' => "PIC already exist"
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'parts' => "PIC not created."
            );
             return Response::json($response);
           }
        }
    }

    public function sendemail(Request $request, $id,$posisi) {

          $id_user = Auth::id();

          $query = "select qc_cars.*, qc_cpars.lokasi, qc_cpars.kategori, qc_cpars.sumber_komplain, qc_cpars.judul_komplain, employees.name as pic_name, qc_cpars.id as id_cpar from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cars.pic = employees.employee_id where qc_cars.id='".$id."'";

          $cars = db::select($query);

          $qc_cars = QcCar::find($id);

          $verifikator = "Select qc_cars.cpar_no,qc_cpars.kategori,departments.department_name,qc_verifikators.verifikatorchief, qc_verifikators.verifikatorforeman, qc_verifikators.verifikatorcoordinator from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join qc_verifikators on qc_cpars.department_id = qc_verifikators.department_id join departments on qc_verifikators.department_id = departments.id where qc_cars.id ='".$id."'";

          $verif = DB::select($verifikator);

          if ($verif[0]->verifikatorchief != null || $verif[0]->verifikatorforeman != null || $verif[0]->verifikatorcoordinator != null) {
            if ($verif[0]->kategori == "Eksternal") {
               if ($qc_cars->checked_chief == NULL) {

                 $mailto = "select distinct email, phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join qc_verifikators on qc_cpars.department_id = qc_verifikators.department_id join departments on qc_verifikators.department_id = departments.id join employees on qc_verifikators.verifikatorchief = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id ='".$id."'";
                 $mails = DB::select($mailto);                 

                 if ($mails == NULL) {
                    $to = 'employee_id';
                    $mailto = "select distinct email, phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join qc_verifikators on qc_cpars.department_id = qc_verifikators.department_id join departments on qc_verifikators.department_id = departments.id join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id ='".$id."'";
                    $mails = DB::select($mailto);
                 }

               } else {
                  if ($posisi == "chief" || $posisi == "coordinator") {
                    $to = "employee_id";
                  } 
                  else if ($posisi == "manager") {
                    $to = "dgm";
                  }
                  else if ($posisi == "dgm") {
                    $to = "gm";
                  } 
                  elseif ($posisi == "gm") {
                    $to = "staff"; //manager departemen
                  }

                  $mailto = "select distinct email, phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join qc_verifikators on qc_cpars.department_id = qc_verifikators.department_id join departments on qc_verifikators.department_id = departments.id join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id ='".$id."'";

                  $mails = DB::select($mailto);
               }

              
            } else if ($verif[0]->kategori == "Internal") {
              
              if ($qc_cars->checked_foreman == NULL) {
                if ($qc_cars->pic == "PI9707010" || $qc_cars->pic == "PI9806004") { //jika pic pak hartono / mawan

                  if ($qc_cars->pic == "PI9707010") { // pak mawan
                    $mailto = "select distinct email, phone from users join employees on employees.employee_id = users.username where users.username = 'PI9707010'";
                    $mails = DB::select($mailto);
                  }
                  else if ($qc_cars->pic == "PI9806004"){ // pak hartono
                    $mailto = "select distinct email, phone from users join employees on employees.employee_id = users.username where users.username = 'PI9806004'";
                    $mails = DB::select($mailto);
                  }
                }

                else{
                  $mailto = "select distinct email, phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join qc_verifikators on qc_cpars.department_id = qc_verifikators.department_id join departments on qc_verifikators.department_id = departments.id join employees on qc_verifikators.verifikatorforeman = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id ='".$id."'"; 
                  $mails = DB::select($mailto);
                }

                
              }
              else {
                  if ($posisi == "foreman2") {
                    $to = "employee_id";
                  } 
                  else if ($posisi == "manager") {
                    $to = "dgm";
                  }
                  else if ($posisi == "dgm") {
                    $to = "gm";
                  } 
                  elseif ($posisi == "gm") {
                    $to = "staff"; //manager departemen
                  }

                  $mailto = "select distinct email, phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join qc_verifikators on qc_cpars.department_id = qc_verifikators.department_id join departments on qc_verifikators.department_id = departments.id join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id ='".$id."'";

                  $mails = DB::select($mailto);
               }


            } else if ($verif[0]->kategori == "Supplier") {

              if ($qc_cars->checked_foreman == NULL) {
                $mailto = "select distinct email, phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join qc_verifikators on qc_cpars.department_id = qc_verifikators.department_id join departments on qc_verifikators.department_id = departments.id join employees on qc_verifikators.verifikatorcoordinator = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id ='".$id."'";
                $mails = DB::select($mailto);
              }else{
                if ($posisi == "coordinator") {
                    $to = "employee_id";
                  } 
                  else if ($posisi == "manager") {
                    $to = "dgm";
                  }
                  else if ($posisi == "dgm") {
                    $to = "gm";
                  } 
                  elseif ($posisi == "gm") {
                    $to = "staff"; //manager departemen
                  }

                  $mailto = "select distinct email, phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join qc_verifikators on qc_cpars.department_id = qc_verifikators.department_id join departments on qc_verifikators.department_id = departments.id join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id ='".$id."'";

                  $mails = DB::select($mailto);
              }
            }
          } else{

            if ($posisi == "staff" || $posisi == "foreman") {
              $to = "employee_id";
            } 
            else if ($posisi == "manager") {
              $to = "dgm";
            }

            $mailto = "select distinct email, phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join qc_verifikators on qc_cpars.department_id = qc_verifikators.department_id join departments on qc_verifikators.department_id = departments.id join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id ='".$id."'";
            $mails = DB::select($mailto);

          }

          foreach($mails as $mail){
            $mailtoo = $mail->email;
          }

          if($cars != null){
            if ($verif[0]->verifikatorchief != null || $verif[0]->verifikatorforeman != null || $verif[0]->verifikatorcoordinator != null) {

              if ($qc_cars->email_status == "SentStaff" && $qc_cars->posisi == "staff") {

                if($verif[0]->kategori == "Supplier"){
                  if ($verif[0]->verifikatorcoordinator != null) {
                      $qc_cars->email_status = "SentCoordinator";
                      $qc_cars->posisi = "coordinator";  
                  }                                  
                }
                else if($verif[0]->kategori == "Eksternal"){
                  if($verif[0]->verifikatorchief != null){
                      $qc_cars->email_status = "SentChief";
                      $qc_cars->posisi = "chief";
                  }
                  else{
                      $qc_cars->email_status = "SentManager";
                      $qc_cars->posisi = "manager";
                      $qc_cars->checked_chief = "Checked";
                      $qc_cars->checked_foreman = "Checked";
                      $qc_cars->checked_coordinator = "Checked";
                  }
                }
                else{
                    $qc_cars->email_status = "SentManager";
                    $qc_cars->posisi = "manager";
                    $qc_cars->checked_chief = "none";
                    $qc_cars->checked_foreman = "none";
                    $qc_cars->checked_coordinator = "none";
                }
                
                $qc_cars->email_send_date = date('Y-m-d');
                $qc_cars->save();

                Mail::to($mailtoo)->bcc('rio.irvansyah@music.yamaha.com','Rio Irvansyah')->send(new SendEmail($cars, 'car'));
                return redirect('/index/qc_car/detail/'.$qc_cars->id)->with('status', 'E-mail ke Chief berhasil terkirim')->with('page', 'CAR');  
              }

              else if($qc_cars->email_status == "SentForeman" && $qc_cars->posisi == "foreman"){
                $qc_cars->email_status = "SentForeman2";
                $qc_cars->email_send_date = date('Y-m-d');
                $qc_cars->posisi = "foreman2";
                $qc_cars->save();

                

                Mail::to($mailtoo)->send(new SendEmail($cars, 'car'));
                return redirect('/index/qc_car/detail/'.$qc_cars->id)->with('status', 'E-mail ke Foreman berhasil terkirim')->with('page', 'CAR');
              }

              else if($qc_cars->email_status == "SentForeman2" && $qc_cars->posisi == "foreman2"){
                $qc_cars->email_status = "SentManager";
                $qc_cars->email_send_date = date('Y-m-d');
                $qc_cars->posisi = "manager";
                $qc_cars->save();

                

                Mail::to($mailtoo)->send(new SendEmail($cars, 'car'));
                return redirect('/index/qc_car/detail/'.$qc_cars->id)->with('status', 'E-mail ke Manager berhasil terkirim')->with('page', 'CAR');
              }

              else if($qc_cars->email_status == "SentChief" && $qc_cars->posisi == "chief"){
                $qc_cars->email_status = "SentManager";
                $qc_cars->email_send_date = date('Y-m-d');
                $qc_cars->posisi = "manager";
                $qc_cars->save();

                

                Mail::to($mailtoo)->send(new SendEmail($cars, 'car'));
                return redirect('/index/qc_car/detail/'.$qc_cars->id)->with('status', 'E-mail ke Manager berhasil terkirim')->with('page', 'CAR');
              }

              else if($qc_cars->email_status == "SentCoordinator" && $qc_cars->posisi == "coordinator"){
                $qc_cars->email_status = "SentManager";
                $qc_cars->email_send_date = date('Y-m-d');
                $qc_cars->posisi = "manager";
                $qc_cars->save();

                

                Mail::to($mailtoo)->send(new SendEmail($cars, 'car'));
                return redirect('/index/qc_car/detail/'.$qc_cars->id)->with('status', 'E-mail ke Manager berhasil terkirim')->with('page', 'CAR');
              }

              else if($qc_cars->email_status == "SentManager" && $qc_cars->posisi == "manager"){
                $qc_cars->email_status = "SentDGM";
                $qc_cars->email_send_date = date('Y-m-d');
                $qc_cars->posisi = "dgm";
                $qc_cars->save();

                

                Mail::to($mailtoo)->send(new SendEmail($cars, 'car'));
                return redirect('/index/qc_car/detail/'.$qc_cars->id)->with('status', 'E-mail ke DGM berhasil terkirim')->with('page', 'CAR');
              }

              else if($qc_cars->email_status == "SentDGM" && $qc_cars->posisi == "dgm"){
                $qc_cars->email_status = "SentGM";
                $qc_cars->email_send_date = date('Y-m-d');
                $qc_cars->posisi = "gm";
                $qc_cars->save();
                // Mail::to('yukitaka.hayakawa@music.yamaha.com')->send(new SendEmail($cars, 'car'));
                return redirect('/index/qc_car/detail/'.$qc_cars->id)->with('status', 'E-mail ke GM berhasil terkirim')->with('page', 'CAR');
              }
              else if($qc_cars->email_status == "SentGM" && $qc_cars->posisi == "gm"){
                $qc_cars->email_status = "SentQA";
                $qc_cars->email_send_date = date('Y-m-d');
                $qc_cars->posisi = "qa";


                $qc_cars->save();

                $cpar = QcCpar::select('qc_cpars.cpar_no','qc_cpars.id','qc_cpars.status_code','qc_cpars.posisi')
                 ->join('qc_cars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
                 ->where('qc_cpars.cpar_no','=',$qc_cars->cpar_no)
                 ->get();

                foreach ($cpar as $cpar) {
                    $cpar->status_code = "7";
                    $cpar->posisi = "QA";
                    $cpar->save();
                }
                
                if ($verif[0]->kategori == "Eksternal" || $verif[0]->kategori == "Supplier") {
                    $to = "staff";
                }
                else if ($verif[0]->kategori == "Internal") {
                    $to = "leader";
                }

                $mailto = "select distinct email,phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id='".$id."'";
                $mails = DB::select($mailto);

                foreach($mails as $mail){
                  $mailtoo2 = $mail->email;
                }


                $query2 = "select qc_cars.*, qc_cpars.lokasi, qc_cpars.kategori, qc_cpars.sumber_komplain, qc_cpars.judul_komplain, employees.name as pic_name, qc_cpars.id as id_cpar from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cars.pic = employees.employee_id where qc_cars.id='".$id."'";

                $cars2 = db::select($query2);

                Mail::to($mailtoo2)->send(new SendEmail($cars2, 'car'));
                return redirect('/index/qc_car/detail/'.$qc_cars->id)->with('status', 'E-mail has Been Sent To QA')->with('page', 'CAR');
              }

              else if($qc_cars->email_status == "SentChief" || $qc_cars->email_status == "SentManager" || $qc_cars->email_status == "SentDGM" || $qc_cars->email_status == "SentGM" || $qc_cars->email_status == "SentQA"){
                return redirect('/index/qc_car/detail/'.$qc_cars->id)->with('error', 'Email pernah dikirim')->with('page', 'CAR');
              }
            } 

            else if($verif[0]->verifikatorchief == null && $verif[0]->verifikatorforeman == null && $verif[0]->verifikatorcoordinator == null) {
                if (($qc_cars->email_status == "SentStaff" && $qc_cars->posisi == "staff") || ($qc_cars->email_status == "SentForeman" && $qc_cars->posisi == "foreman")) {

                  if($qc_cars->car_cpar->employee_id == $qc_cars->car_cpar->dgm){
                    $qc_cars->email_status = "SentDGM";
                    $qc_cars->email_send_date = date('Y-m-d');
                    $qc_cars->posisi = "dgm";
                    $qc_cars->save();

                    

                    Mail::to($mailtoo)->send(new SendEmail($cars, 'car'));
                    return redirect('/index/qc_car/detail/'.$qc_cars->id)->with('status', 'E-mail ke DGM berhasil terkirim')->with('page', 'CAR');
                  }
                  else{
                    $qc_cars->email_status = "SentManager";
                    $qc_cars->email_send_date = date('Y-m-d');
                    $qc_cars->posisi = "manager";
                    $qc_cars->save();

                    

                    Mail::to($mailtoo)->send(new SendEmail($cars, 'car'));
                    return redirect('/index/qc_car/detail/'.$qc_cars->id)->with('status', 'E-mail ke Manager berhasil terkirim')->with('page', 'CAR');                     
                  }

                }

                else if($qc_cars->email_status == "SentManager" && $qc_cars->posisi == "manager"){
                  $qc_cars->email_status = "SentDGM";
                  $qc_cars->email_send_date = date('Y-m-d');
                  $qc_cars->posisi = "dgm";
                  $qc_cars->save();

                  Mail::to($mailtoo)->send(new SendEmail($cars, 'car'));
                  return redirect('/index/qc_car/detail/'.$qc_cars->id)->with('status', 'E-mail ke DGM berhasil terkirim')->with('page', 'CAR');
                }

                else if($qc_cars->email_status == "SentDGM" && $qc_cars->posisi == "dgm"){
                  $qc_cars->email_status = "SentGM";
                  $qc_cars->email_send_date = date('Y-m-d');
                  $qc_cars->posisi = "gm";
                  $qc_cars->save();
                  // Mail::to('yukitaka.hayakawa@music.yamaha.com')->send(new SendEmail($cars, 'car'));
                  return redirect('/index/qc_car/detail/'.$qc_cars->id)->with('status', 'Verifikasi Telah Berhasil')->with('page', 'CAR');
                }
                else if($qc_cars->email_status == "SentGM" && $qc_cars->posisi == "gm"){
                  $qc_cars->email_status = "SentQA";
                  $qc_cars->email_send_date = date('Y-m-d');
                  $qc_cars->posisi = "qa";
                  $qc_cars->save();

                  $cpar = QcCpar::select('qc_cpars.cpar_no','qc_cpars.id','qc_cpars.status_code','qc_cpars.posisi')
                 ->join('qc_cars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
                 ->where('qc_cpars.cpar_no','=',$qc_cars->cpar_no)
                 ->get();

                  foreach ($cpar as $cpar) {
                      $cpar->status_code = "7";
                      $cpar->posisi = "QA";
                      $cpar->save();
                  }

                  $mailto = "select distinct email,phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id='".$id."'";
                  $mails = DB::select($mailto);

                  foreach($mails as $mail){
                    $mailtoo2 = $mail->email;
                  }


                  $query2 = "select qc_cars.*, qc_cpars.lokasi, qc_cpars.kategori, qc_cpars.sumber_komplain, qc_cpars.judul_komplain , employees.name as pic_name, qc_cpars.id as id_cpar from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cars.pic = employees.employee_id where qc_cars.id='".$id."'";

                  $cars2 = db::select($query2);

                  Mail::to($mailtoo2)->send(new SendEmail($cars2, 'car'));
                  return redirect('/index/qc_car/detail/'.$qc_cars->id)->with('status', 'E-mail has Been Sent To QA')->with('page', 'CAR');
                }

                else if($qc_cars->email_status == "SentChief" || $qc_cars->email_status == "SentManager" || $qc_cars->email_status == "SentDGM" || $qc_cars->email_status == "SentGM" || $qc_cars->email_status == "SentQA"){
                  return redirect('/index/qc_car/detail/'.$qc_cars->id)->with('error', 'Email pernah dikirim')->with('page', 'CAR');
                }
            }
          }
          else{
            return redirect('/index/qc_car/detail/'.$qc_cars->id)->with('error', 'Data tidak tersedia.')->with('page', 'CAR');
          }
     }

     public function sendemail2(Request $request, $id,$posisi) {
        $id_user = Auth::id();
        $query = "select qc_cars.*, qc_cpars.lokasi, qc_cpars.kategori, qc_cpars.sumber_komplain, qc_cpars.judul_komplain, employees.name as pic_name, qc_cpars.id as id_cpar from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cars.pic = employees.employee_id where qc_cars.id='".$id."'";

        $cars = db::select($query);

        $qc_cars = QcCar::find($id);



     }

     public function verifikasicar($id){
          $car = QcCar::find($id);

          // if ($car->posisi == "chief") {
          //     $from = "staff";
          // }
          // else if ($car->posisi == "manager") {
          //     $from = "chief";
          // }
          // else if ($car->posisi == "dgm") {
          //     $from = "manager";
          // }
          // else if ($car->posisi == "gm") {
          //     $from = "dgm";
          // }
          // else {
          //     $from = "staff";
          // }

          $cars = QcCar::select('qc_cars.*','qc_cpars.employee_id','qc_cpars.dgm','qc_cpars.gm','qc_cpars.kategori','qc_verifikators.verifikatorchief','qc_verifikators.verifikatorforeman','qc_verifikators.verifikatorcoordinator','qc_cpars.id as id_cpar')
          ->join('qc_cpars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
          ->join('qc_verifikators','qc_cpars.department_id','=','qc_verifikators.department_id')
          ->join('departments','departments.id','=','qc_verifikators.department_id')
          ->where('qc_cars.id',$id)
          ->get();

          return view('qc_car.verifikasi_car', array(
            'cars' => $cars
          ))->with('page', 'CAR');
      }

      public function checked(Request $request,$id)
      {
          $query = "select qc_cars.*, qc_cpars.lokasi, qc_cpars.kategori, qc_cpars.sumber_komplain, qc_cpars.judul_komplain, employees.name as pic_name, qc_cpars.id as id_cpar, qc_cpars.kategori_approval from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cars.pic = employees.employee_id where qc_cars.id='".$id."'";

          $cars = db::select($query);

          $qc_cars = QcCar::find($id);

          $checked = $request->get('checked');

          if(count($checked) == 4){

            if ($qc_cars->posisi == "chief") {
              $qc_cars->checked_chief = "Checked";
              $qc_cars->checked_foreman = "none";
              $qc_cars->checked_coordinator = "none";
            }

            else if ($qc_cars->posisi == "foreman2") {
              $qc_cars->checked_chief = "none";
              $qc_cars->checked_foreman = "Checked";              
              $qc_cars->checked_coordinator = "none";
            }

            else if ($qc_cars->posisi == "coordinator") {
              $qc_cars->checked_chief = "none";
              $qc_cars->checked_foreman = "none";
              $qc_cars->checked_coordinator = "Checked";              
            }

            else if ($qc_cars->posisi == "manager") {
              $qc_cars->checked_manager = "Checked";              
            }

            else if ($qc_cars->posisi == "dgm") {
              $qc_cars->checked_manager = "Checked";
              $qc_cars->approved_dgm = "Checked";  
              // $qc_cars->email_status = "SentGM";
              // $qc_cars->email_send_date = date('Y-m-d');
              // $qc_cars->posisi = "gm";
              $qc_cars->save();
            }

            else if ($qc_cars->posisi == "gm") {
              $qc_cars->approved_gm = "Checked"; 
            }

            $qc_cars->save();
            
          }
          else{
            return redirect('/index/qc_car/verifikasicar/'.$id)->with('error', 'CAR Not Approved')->with('page', 'CAR');
          }         

          $verifikator = "Select qc_cars.cpar_no,qc_cpars.kategori,departments.department_name,qc_verifikators.verifikatorchief, qc_verifikators.verifikatorforeman, qc_verifikators.verifikatorcoordinator from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join qc_verifikators on qc_cpars.department_id = qc_verifikators.department_id join departments on qc_verifikators.department_id = departments.id where qc_cars.id ='".$id."'";

          $verif = DB::select($verifikator);

          if ($verif[0]->verifikatorchief != null || $verif[0]->verifikatorforeman != null || $verif[0]->verifikatorcoordinator != null) {
            if ($verif[0]->kategori == "Eksternal") {
               if ($qc_cars->checked_chief == NULL) {
                 $mailto = "select distinct email,phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join qc_verifikators on qc_cpars.department_id = qc_verifikators.department_id join departments on qc_verifikators.department_id = departments.id join employees on qc_verifikators.verifikatorchief = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id ='".$id."'";
                 $mails = DB::select($mailto);                 

                 if ($mails == NULL) {
                    $to = 'employee_id';
                    $mailto = "select distinct email,phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join qc_verifikators on qc_cpars.department_id = qc_verifikators.department_id join departments on qc_verifikators.department_id = departments.id join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id ='".$id."'";
                    $mails = DB::select($mailto);
                 }

               } else {
                  if ($qc_cars->posisi == "chief") {
                    $to = "employee_id";
                  } 
                  else if ($qc_cars->posisi == "manager") {
                    if ($qc_cars->car_cpar->kategori_approval == "CPAR Manager Terkait") {
                      $to = "staff";
                    }else{
                      $to = "dgm";
                    }
                  }
                  else if ($qc_cars->posisi == "dgm") {
                    if ($qc_cars->car_cpar->kategori_approval == "CPAR DGM Produksi") {
                      $to = "staff";
                    }else{
                      $to = "gm";
                    }
                  } 
                  elseif ($qc_cars->posisi == "gm") {
                    $to = "staff";
                  }

                  $mailto = "select distinct email,phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join qc_verifikators on qc_cpars.department_id = qc_verifikators.department_id join departments on qc_verifikators.department_id = departments.id join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id ='".$id."'";

                  $mails = DB::select($mailto);
               }
            } 

            else if ($verif[0]->kategori == "Internal") {
              
              if ($qc_cars->checked_foreman == NULL) {
                $mailto = "select distinct email,phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join qc_verifikators on qc_cpars.department_id = qc_verifikators.department_id join departments on qc_verifikators.department_id = departments.id join employees on qc_verifikators.verifikatorforeman = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id ='".$id."'"; 
                $mails = DB::select($mailto);
              }
              else {
                  if ($qc_cars->posisi == "foreman2") {
                    $to = "employee_id";
                  } 
                  else if ($qc_cars->posisi == "manager") {
                    if ($qc_cars->car_cpar->kategori_approval == "CPAR Manager Terkait") {
                      $to = "staff";
                    }else{
                      $to = "dgm";
                    }
                  }
                  else if ($qc_cars->posisi == "dgm") {
                    if ($qc_cars->car_cpar->kategori_approval == "CPAR DGM Produksi") {
                      $to = "staff";
                    }else{
                      $to = "gm";
                    }
                  }
                  elseif ($qc_cars->posisi == "gm") {
                    $to = "staff"; //manager departemen
                  }

                  $mailto = "select distinct email,phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join qc_verifikators on qc_cpars.department_id = qc_verifikators.department_id join departments on qc_verifikators.department_id = departments.id join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id ='".$id."'";

                  $mails = DB::select($mailto);
               }


            } else if ($verif[0]->kategori == "Supplier") {

              if ($qc_cars->checked_foreman == NULL) {
                $mailto = "select distinct email,phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join qc_verifikators on qc_cpars.department_id = qc_verifikators.department_id join departments on qc_verifikators.department_id = departments.id join employees on qc_verifikators.verifikatorcoordinator = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id ='".$id."'";
                $mails = DB::select($mailto);
              }else{
                if ($qc_cars->posisi == "coordinator") {
                    $to = "employee_id";
                  } 
                  else if ($qc_cars->posisi == "manager") {
                    if ($qc_cars->car_cpar->kategori_approval == "CPAR Manager Terkait") {
                      $to = "staff";
                    }else{
                      $to = "dgm";
                    }
                  }
                  else if ($qc_cars->posisi == "dgm") {
                    if ($qc_cars->car_cpar->kategori_approval == "CPAR DGM Produksi") {
                      $to = "staff";
                    }else{
                      $to = "gm";
                    }
                  }
                  elseif ($qc_cars->posisi == "gm") {
                    $to = "staff"; //manager departemen
                  }

                  $mailto = "select distinct email,phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join qc_verifikators on qc_cpars.department_id = qc_verifikators.department_id join departments on qc_verifikators.department_id = departments.id join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id ='".$id."'";

                  $mails = DB::select($mailto);
              }
            }
          } else{

            if ($qc_cars->posisi == "staff" || $qc_cars->posisi == "foreman") {
              $to = "employee_id";
            } 
            else if ($qc_cars->posisi == "manager") {
              if ($qc_cars->car_cpar->kategori_approval == "CPAR Manager Terkait") {
                $to = "staff";
              }else{
                $to = "dgm";
              }
            }
            else if ($qc_cars->posisi == "dgm") {
              if ($qc_cars->car_cpar->kategori_approval == "CPAR DGM Produksi") {
                $to = "staff";
              }else{
                $to = "gm";
              }
            }
            else if ($qc_cars->posisi == "gm") {
              $to = "staff";
            }

            $mailto = "select distinct email,phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join qc_verifikators on qc_cpars.department_id = qc_verifikators.department_id join departments on qc_verifikators.department_id = departments.id join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id ='".$id."'";
            $mails = DB::select($mailto);

          }

          foreach($mails as $mail){
            $mailtoo = $mail->email;
          }

          if($cars != null){
            if ($verif[0]->verifikatorchief != null || $verif[0]->verifikatorforeman != null || $verif[0]->verifikatorcoordinator != null) {

              if ($qc_cars->email_status == "SentStaff" && $qc_cars->posisi == "staff") {
                if ($verif[0]->verifikatorcoordinator != null) {
                    $qc_cars->email_status = "SentCoordinator";
                    $qc_cars->posisi = "coordinator";  
                }                
                else if($verif[0]->verifikatorchief != null){
                    $qc_cars->email_status = "SentChief";
                    $qc_cars->posisi = "chief";
                }
                else{
                    $qc_cars->email_status = "SentManager";
                    $qc_cars->posisi = "manager";
                    $qc_cars->checked_chief = "none";
                    $qc_cars->checked_foreman = "none";
                    $qc_cars->checked_coordinator = "none";
                }
                
                $qc_cars->email_send_date = date('Y-m-d');
                
                $qc_cars->save();

                

                Mail::to($mailtoo)->bcc('rio.irvansyah@music.yamaha.com','Rio Irvansyah')->send(new SendEmail($cars, 'car'));
                return redirect('/index/qc_car/verifikasicar/'.$qc_cars->id)->with('status', 'CAR Approved')->with('page', 'CAR');
              }

              else if($qc_cars->email_status == "SentForeman" && $qc_cars->posisi == "foreman"){
                $qc_cars->email_status = "SentForeman2";
                $qc_cars->email_send_date = date('Y-m-d');
                $qc_cars->posisi = "foreman2";
                $qc_cars->save();

                

                Mail::to($mailtoo)->send(new SendEmail($cars, 'car'));
                return redirect('/index/qc_car/verifikasicar/'.$qc_cars->id)->with('status', 'CAR Approved')->with('page', 'CAR');
              }

              else if($qc_cars->email_status == "SentForeman2" && $qc_cars->posisi == "foreman2"){
                $qc_cars->email_status = "SentManager";
                $qc_cars->email_send_date = date('Y-m-d');
                $qc_cars->posisi = "manager";
                $qc_cars->save();

                

                Mail::to($mailtoo)->send(new SendEmail($cars, 'car'));
                return redirect('/index/qc_car/verifikasicar/'.$qc_cars->id)->with('status', 'CAR Approved')->with('page', 'CAR');
              }

              else if($qc_cars->email_status == "SentChief" && $qc_cars->posisi == "chief"){
                $qc_cars->email_status = "SentManager";
                $qc_cars->email_send_date = date('Y-m-d');
                $qc_cars->posisi = "manager";
                $qc_cars->save();

                

                Mail::to($mailtoo)->send(new SendEmail($cars, 'car'));
                return redirect('/index/qc_car/verifikasicar/'.$qc_cars->id)->with('status', 'CAR Approved')->with('page', 'CAR');
              }

              else if($qc_cars->email_status == "SentCoordinator" && $qc_cars->posisi == "coordinator"){
                $qc_cars->email_status = "SentManager";
                $qc_cars->email_send_date = date('Y-m-d');
                $qc_cars->posisi = "manager";
                $qc_cars->save();

                Mail::to($mailtoo)->send(new SendEmail($cars, 'car'));
                return redirect('/index/qc_car/verifikasicar/'.$qc_cars->id)->with('status', 'CAR Approved')->with('page', 'CAR');
              }

              else if($qc_cars->email_status == "SentManager" && $qc_cars->posisi == "manager"){
                if ($qc_cars->car_cpar->kategori_approval == "CPAR Manager Terkait") {
                  $qc_cars->email_status = "SentQA";
                  $qc_cars->email_send_date = date('Y-m-d');
                  $qc_cars->posisi = "qa";
                  $qc_cars->save();

                  $cpar = QcCpar::select('qc_cpars.cpar_no','qc_cpars.id','qc_cpars.status_code','qc_cpars.posisi')
                   ->join('qc_cars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
                   ->where('qc_cpars.cpar_no','=',$qc_cars->cpar_no)
                   ->get();

                  foreach ($cpar as $cpar) {
                      $cpar->status_code = "7";
                      $cpar->posisi = "QA";
                      $cpar->save();
                  }
                  
                  if ($verif[0]->kategori == "Eksternal" || $verif[0]->kategori == "Supplier") {
                      $to = "staff";
                  }
                  else if ($verif[0]->kategori == "Internal") {
                      $to = "leader";
                  }

                  $mailto = "select distinct email,phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id='".$id."'";
                  $mails = DB::select($mailto);

                  foreach($mails as $mail){
                    $mailtoo2 = $mail->email;
                  }


                  $query2 = "select qc_cars.*, qc_cpars.lokasi, qc_cpars.kategori, qc_cpars.sumber_komplain, qc_cpars.judul_komplain, employees.name as pic_name, qc_cpars.id as id_cpar from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cars.pic = employees.employee_id where qc_cars.id='".$id."'";

                  $cars2 = db::select($query2);

                  Mail::to($mailtoo2)->send(new SendEmail($cars2, 'car'));
                  return redirect('/index/qc_car/verifikasicar/'.$qc_cars->id)->with('status', 'E-mail has Been Sent To QA')->with('page', 'CAR');
                }
                else{
                  $qc_cars->email_status = "SentDGM";
                  $qc_cars->email_send_date = date('Y-m-d');
                  $qc_cars->posisi = "dgm";
                  $qc_cars->save();


                  // Mail::to($mailtoo)->send(new SendEmail($cars, 'car'));
                  return redirect('/index/qc_car/verifikasicar/'.$qc_cars->id)->with('status', 'CAR Approved')->with('page', 'CAR');
                  
                }

              }

              else if($qc_cars->email_status == "SentDGM" && $qc_cars->posisi == "dgm"){
                

                if ($qc_cars->car_cpar->kategori_approval == "CPAR DGM Produksi") {
                  $qc_cars->email_status = "SentQA";
                  $qc_cars->email_send_date = date('Y-m-d');
                  $qc_cars->posisi = "qa";
                  $qc_cars->save();

                  $cpar = QcCpar::select('qc_cpars.cpar_no','qc_cpars.id','qc_cpars.status_code','qc_cpars.posisi')
                   ->join('qc_cars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
                   ->where('qc_cpars.cpar_no','=',$qc_cars->cpar_no)
                   ->get();

                  foreach ($cpar as $cpar) {
                      $cpar->status_code = "7";
                      $cpar->posisi = "QA";
                      $cpar->save();
                  }
                  
                  if ($verif[0]->kategori == "Eksternal" || $verif[0]->kategori == "Supplier") {
                      $to = "staff";
                  }
                  else if ($verif[0]->kategori == "Internal") {
                      $to = "leader";
                  }

                  $mailto = "select distinct email,phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id='".$id."'";
                  $mails = DB::select($mailto);

                  foreach($mails as $mail){
                    $mailtoo2 = $mail->email;
                  }


                  $query2 = "select qc_cars.*, qc_cpars.lokasi, qc_cpars.kategori, qc_cpars.sumber_komplain, qc_cpars.judul_komplain, employees.name as pic_name, qc_cpars.id as id_cpar from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cars.pic = employees.employee_id where qc_cars.id='".$id."'";

                  $cars2 = db::select($query2);

                  Mail::to($mailtoo2)->send(new SendEmail($cars2, 'car'));
                  return redirect('/index/qc_car/verifikasicar/'.$qc_cars->id)->with('status', 'E-mail has Been Sent To QA')->with('page', 'CAR');
                }
                else{
                  $qc_cars->email_status = "SentGM";
                  $qc_cars->email_send_date = date('Y-m-d');
                  $qc_cars->posisi = "gm";
                  $qc_cars->save();

                  return redirect('/index/qc_car/verifikasicar/'.$qc_cars->id)->with('status', 'CAR Approved')->with('page', 'CAR');
                }
              }
              else if($qc_cars->email_status == "SentGM" && $qc_cars->posisi == "gm"){
                $qc_cars->email_status = "SentQA";
                $qc_cars->email_send_date = date('Y-m-d');
                $qc_cars->posisi = "qa";

                $qc_cars->save();

                $cpar = QcCpar::select('qc_cpars.cpar_no','qc_cpars.id','qc_cpars.status_code','qc_cpars.posisi')
                 ->join('qc_cars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
                 ->where('qc_cpars.cpar_no','=',$qc_cars->cpar_no)
                 ->get();

                foreach ($cpar as $cpar) {
                    $cpar->status_code = "7";
                    $cpar->posisi = "QA";
                    $cpar->save();
                }
                
                if ($verif[0]->kategori == "Eksternal" || $verif[0]->kategori == "Supplier") {
                    $to = "staff";
                }
                else if ($verif[0]->kategori == "Internal") {
                    $to = "leader";
                }

                $mailto = "select distinct email,phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id='".$id."'";
                $mails = DB::select($mailto);

                foreach($mails as $mail){
                  $mailtoo2 = $mail->email;
                }


                $query2 = "select qc_cars.*, qc_cpars.lokasi, qc_cpars.kategori, qc_cpars.sumber_komplain, qc_cpars.judul_komplain, employees.name as pic_name, qc_cpars.id as id_cpar from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cars.pic = employees.employee_id where qc_cars.id='".$id."'";

                $cars2 = db::select($query2);

                Mail::to($mailtoo2)->send(new SendEmail($cars2, 'car'));
                return redirect('/index/qc_car/verifikasicar/'.$qc_cars->id)->with('status', 'E-mail has Been Sent To QA')->with('page', 'CAR');
              }

              else if($qc_cars->email_status == "SentChief" || $qc_cars->email_status == "SentManager" || $qc_cars->email_status == "SentDGM" || $qc_cars->email_status == "SentGM" || $qc_cars->email_status == "SentQA"){
                return redirect('/index/qc_car/verifikasicar/'.$qc_cars->id)->with('error', 'Email pernah dikirim')->with('page', 'CAR');
              }
            }

            else if($verif[0]->verifikatorchief == null && $verif[0]->verifikatorforeman == null && $verif[0]->verifikatorcoordinator == null) {
                if (($qc_cars->email_status == "SentStaff" && $qc_cars->posisi == "staff") || ($qc_cars->email_status == "SentForeman" && $qc_cars->posisi == "foreman")) {

                  if($qc_cars->car_cpar->employee_id == $qc_cars->car_cpar->dgm){
                    $qc_cars->email_status = "SentDGM";
                    $qc_cars->email_send_date = date('Y-m-d');
                    $qc_cars->posisi = "dgm";
                    $qc_cars->save();


                    // Mail::to($mailtoo)->send(new SendEmail($cars, 'car'));
                    return redirect('/index/qc_car/verifikasicar/'.$qc_cars->id)->with('status', 'CAR Approved')->with('page', 'CAR');
                  }
                  else{
                    $qc_cars->email_status = "SentManager";
                    $qc_cars->email_send_date = date('Y-m-d');
                    $qc_cars->posisi = "manager";
                    $qc_cars->save();


                    Mail::to($mailtoo)->send(new SendEmail($cars, 'car'));
                    return redirect('/index/qc_car/verifikasicar/'.$qc_cars->id)->with('status', 'CAR Approved')->with('page', 'CAR');
                  }

                }

                else if($qc_cars->email_status == "SentManager" && $qc_cars->posisi == "manager"){
                 

                  if ($qc_cars->car_cpar->kategori_approval == "CPAR Manager Terkait") {
                    $qc_cars->email_status = "SentQA";
                    $qc_cars->email_send_date = date('Y-m-d');
                    $qc_cars->posisi = "qa";
                    $qc_cars->save();

                    $cpar = QcCpar::select('qc_cpars.cpar_no','qc_cpars.id','qc_cpars.status_code','qc_cpars.posisi')
                   ->join('qc_cars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
                   ->where('qc_cpars.cpar_no','=',$qc_cars->cpar_no)
                   ->get();

                    foreach ($cpar as $cpar) {
                        $cpar->status_code = "7";
                        $cpar->posisi = "QA";
                        $cpar->save();
                    }

                    $mailto = "select distinct email,phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id='".$id."'";
                    $mails = DB::select($mailto);

                    foreach($mails as $mail){
                      $mailtoo2 = $mail->email;
                    }


                    $query2 = "select qc_cars.*, qc_cpars.lokasi, qc_cpars.kategori, qc_cpars.sumber_komplain, qc_cpars.judul_komplain , employees.name as pic_name, qc_cpars.id as id_cpar from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cars.pic = employees.employee_id where qc_cars.id='".$id."'";

                    $cars2 = db::select($query2);


                    Mail::to($mailtoo2)->send(new SendEmail($cars2, 'car'));
                    return redirect('/index/qc_car/verifikasicar/'.$qc_cars->id)->with('status', 'CAR Approved')->with('page', 'CAR');
                  }
                  else{
                    $qc_cars->email_status = "SentDGM";
                    $qc_cars->email_send_date = date('Y-m-d');
                    $qc_cars->posisi = "dgm";
                    $qc_cars->save();

                    // Mail::to($mailtoo)->send(new SendEmail($cars, 'car'));
                    return redirect('/index/qc_car/verifikasicar/'.$qc_cars->id)->with('status', 'CAR Approved')->with('page', 'CAR');
                  }

                }

                else if($qc_cars->email_status == "SentDGM" && $qc_cars->posisi == "dgm"){
                  if ($qc_cars->car_cpar->kategori_approval == "CPAR DGM Produksi") {
                    $qc_cars->email_status = "SentQA";
                    $qc_cars->email_send_date = date('Y-m-d');
                    $qc_cars->posisi = "qa";
                    $qc_cars->save();

                    $cpar = QcCpar::select('qc_cpars.cpar_no','qc_cpars.id','qc_cpars.status_code','qc_cpars.posisi')
                   ->join('qc_cars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
                   ->where('qc_cpars.cpar_no','=',$qc_cars->cpar_no)
                   ->get();

                    foreach ($cpar as $cpar) {
                        $cpar->status_code = "7";
                        $cpar->posisi = "QA";
                        $cpar->save();
                    }

                    $mailto = "select distinct email,phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id='".$id."'";
                    $mails = DB::select($mailto);

                    foreach($mails as $mail){
                      $mailtoo2 = $mail->email;
                    }


                    $query2 = "select qc_cars.*, qc_cpars.lokasi, qc_cpars.kategori, qc_cpars.sumber_komplain, qc_cpars.judul_komplain , employees.name as pic_name, qc_cpars.id as id_cpar from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cars.pic = employees.employee_id where qc_cars.id='".$id."'";

                    $cars2 = db::select($query2);


                    Mail::to($mailtoo2)->send(new SendEmail($cars2, 'car'));
                    return redirect('/index/qc_car/verifikasicar/'.$qc_cars->id)->with('status', 'CAR Approved')->with('page', 'CAR');
                  }
                  else{
                    $qc_cars->email_status = "SentGM";
                    $qc_cars->email_send_date = date('Y-m-d');
                    $qc_cars->posisi = "gm";
                    $qc_cars->save();
                    return redirect('/index/qc_car/verifikasicar/'.$qc_cars->id)->with('status', 'CAR Approved')->with('page', 'CAR');
                  }
                }
                else if($qc_cars->email_status == "SentGM" && $qc_cars->posisi == "gm"){
                  $qc_cars->email_status = "SentQA";
                  $qc_cars->email_send_date = date('Y-m-d');
                  $qc_cars->posisi = "qa";
                  $qc_cars->save();

                  $cpar = QcCpar::select('qc_cpars.cpar_no','qc_cpars.id','qc_cpars.status_code','qc_cpars.posisi')
                 ->join('qc_cars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
                 ->where('qc_cpars.cpar_no','=',$qc_cars->cpar_no)
                 ->get();

                  foreach ($cpar as $cpar) {
                      $cpar->status_code = "7";
                      $cpar->posisi = "QA";
                      $cpar->save();
                  }

                  $mailto = "select distinct email,phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id='".$id."'";
                  $mails = DB::select($mailto);

                  foreach($mails as $mail){
                    $mailtoo2 = $mail->email;
                  }


                  $query2 = "select qc_cars.*, qc_cpars.lokasi, qc_cpars.kategori, qc_cpars.sumber_komplain, qc_cpars.judul_komplain , employees.name as pic_name, qc_cpars.id as id_cpar from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cars.pic = employees.employee_id where qc_cars.id='".$id."'";

                  $cars2 = db::select($query2);


                  Mail::to($mailtoo2)->send(new SendEmail($cars2, 'car'));
                  return redirect('/index/qc_car/verifikasicar/'.$qc_cars->id)->with('status', 'CAR Approved')->with('page', 'CAR');
                }

                else if($qc_cars->email_status == "SentChief" || $qc_cars->email_status == "SentManager" || $qc_cars->email_status == "SentDGM" || $qc_cars->email_status == "SentGM" || $qc_cars->email_status == "SentQA"){
                  // return redirect('/index/qc_car/detail/'.$qc_cars->id)->with('error', 'Email pernah dikirim')->with('page', 'CAR');
                }
            }
          }
          else{
            return redirect('/index/qc_car/verifikasicar/'.$qc_cars->id)->with('error', 'Data tidak tersedia.')->with('page', 'CAR');
          }
      }

      public function unchecked(Request $request,$id)
      {
          $alasan = $request->get('alasan');
          $cars = QcCar::find($id);
          
          $cars->qa_perbaikan = $alasan;

          if ($cars->posisi == "manager") {
              $cars->checked_chief = null;              
              $cars->checked_foreman = null;
              $cars->checked_coordinator = null;             
          }

          else if ($cars->posisi == "dgm") {
            $cars->checked_chief = null;              
            $cars->checked_foreman = null;
            $cars->checked_coordinator = null;
            $cars->checked_manager = null;              
          }

          else if ($cars->posisi == "gm") {
            $cars->checked_chief = null;              
            $cars->checked_foreman = null;
            $cars->checked_coordinator = null;
            $cars->checked_manager = null;
            $cars->approved_dgm = null; 
          }

          if($cars->car_cpar->kategori == "Eksternal" || $cars->car_cpar->kategori == "Supplier") {
            $cars->email_status = "SentStaff";            
            $cars->posisi = "staff";                
          } 

          else if ($cars->car_cpar->kategori == "Internal") {
            $cars->email_status = "SentForeman";            
            $cars->posisi = "foreman";                
          }

          $cars->save();
          $query = "select qc_cars.id, qc_cars.cpar_no, qc_cars.qa_perbaikan from qc_cars where qc_cars.id='".$id."'";
          $querycar = db::select($query);

          $mailto = "select distinct email, phone from qc_cars join employees on qc_cars.pic = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id='".$id."'";
          $mails = DB::select($mailto);

          foreach($mails as $mail){
            $mailtoo = $mail->email;
          }

          Mail::to($mailtoo)->send(new SendEmail($querycar, 'rejectcar'));
          return redirect('/index/qc_car/verifikasicar/'.$id)->with('success', 'CAR Rejected')->with('page', 'CAR');
      }

      public function uncheckedGM(Request $request,$id)
      {
          $alasan = $request->get('alasan');
          $cars = QcCar::find($id);
          
          $cars->qa_perbaikan = $alasan;

          if ($cars->posisi == "gm") {
            $cars->checked_chief = null;              
            $cars->checked_foreman = null;
            $cars->checked_coordinator = null;
            $cars->checked_manager = null;
            $cars->approved_dgm = null; 
          }

          if($cars->car_cpar->kategori == "Eksternal" || $cars->car_cpar->kategori == "Supplier") {
            $cars->email_status = "SentStaff";            
            $cars->posisi = "staff";                
          } 

          else if ($cars->car_cpar->kategori == "Internal") {
            $cars->email_status = "SentForeman";            
            $cars->posisi = "foreman";                
          }

          $cars->save();
          $query = "select qc_cars.id, qc_cars.cpar_no, qc_cars.qa_perbaikan from qc_cars where qc_cars.id='".$id."'";
          $querycar = db::select($query);

          $mailto = "select distinct email,phone from qc_cars join employees on qc_cars.pic = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id='".$id."'";
          $mails = DB::select($mailto);

          $mailmanager = "select distinct email from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join users on qc_cpars.employee_id = users.username where qc_cars.id='".$id."'";
          $mailmanagers = DB::select($mailmanager);

          foreach($mails as $mail){
            $mailtoo = $mail->email;
          }


          foreach ($mailmanagers as $mailm) {
            $mailmtoo = $mailm->email;        
          }


          Mail::to($mailtoo)->cc($mailmtoo)->send(new SendEmail($querycar, 'rejectcar'));
          return redirect('/index/qc_car/verifikasigm/'.$id)->with('success', 'CAR Rejected')->with('page', 'CAR');
      } 

      //Verifikator QA
      public function verifikator()
      {
       $verifikator = QcVerifikator::select('qc_verifikators.*','departments.department_name','chief.name as chiefname','foreman.name as foremanname','coordinator.name as coordinatorname')
       ->join('departments','qc_verifikators.department_id','=','departments.id')
       ->leftjoin('employees as chief','qc_verifikators.verifikatorchief','=','chief.employee_id')
       ->leftjoin('employees as foreman','qc_verifikators.verifikatorforeman','=','foreman.employee_id')
       ->leftjoin('employees as coordinator','qc_verifikators.verifikatorcoordinator','=','coordinator.employee_id')
       ->get();

       return view('qc_car.verifikator', array(
        'verifikator' => $verifikator
       ))->with('page', 'CAR Verificator');
    }

    public function deletefiles(Request $request)
    {
      try{
        $car = QcCar::find($request->get('idcar'));
        $namafile = json_decode($car->file);
        // var_dump($namafile);
        // foreach ($car as $car) {
        //   $namafile = json_decode($car->file);
        // }
        // $namafilebaru[] = Null;
        $namafilebarubaru = "";
        foreach ($namafile as $key) {
          if ($key == $request->get('nama_file')) {
            File::delete('files/car/'.$key);
          }
          else{
              if (count($key) > 0) {
                $namafilebarubaru = $key;
                $namafilebaru[] = $namafilebarubaru;
              }
          }
        }
        
        if ($namafilebarubaru != "") {
          $car->file = json_encode($namafilebaru);
          $car->save();
        }
        else{
          $car->file = "";
          $car->save();
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


    // Sign Online
      public function verifikasigm($id){

          $car = QcCar::find($id);

          $cars = QcCar::select('qc_cars.*','qc_cpars.kategori','mutation_logs.section','qc_cpars.lokasi','qc_cpars.tgl_permintaan','qc_cpars.tgl_balas','qc_cpars.sumber_komplain','departments.department_name','pic.name as picname','manager.name as managername','dgm.name as dgmname','gm.name as gmname','statuses.status_name','chief.name as chiefname','foreman.name as foremanname','coordinator.name as coordinatorname','qc_cpars.posisi as posisi_cpar','staffqa.name as staffqaname','leaderqa.name as leaderqaname','chiefqa.name as chiefqaname','foremanqa.name as foremanqaname','managerqa.name as managerqaname','qc_cpars.staff','qc_cpars.leader')
          ->join('qc_cpars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
          ->join('statuses','qc_cpars.status_code','=','statuses.status_code')
          ->join('qc_verifikators','qc_cpars.department_id','=','qc_verifikators.department_id')
          ->join('departments','qc_verifikators.department_id','=','departments.id')
          ->join('mutation_logs','qc_cpars.employee_id','=','mutation_logs.employee_id')
          ->leftjoin('employees as chief','qc_verifikators.verifikatorchief','=','chief.employee_id')
          ->leftjoin('employees as foreman','qc_verifikators.verifikatorforeman','=','foreman.employee_id')
          ->leftjoin('employees as coordinator','qc_verifikators.verifikatorcoordinator','=','coordinator.employee_id')
          ->join('employees as manager','qc_cpars.employee_id','=','manager.employee_id')
          ->join('employees as pic','qc_cars.pic','=','pic.employee_id')
          ->join('employees as dgm','qc_cpars.dgm','=','dgm.employee_id')
          ->join('employees as gm','qc_cpars.gm','=','gm.employee_id')
          ->leftjoin('employees as staffqa','qc_cpars.staff','=','staffqa.employee_id')
          ->leftjoin('employees as leaderqa','qc_cpars.leader','=','leaderqa.employee_id')
          ->leftjoin('employees as chiefqa','qc_cpars.chief','=','chiefqa.employee_id')
          ->leftjoin('employees as foremanqa','qc_cpars.foreman','=','foremanqa.employee_id')
          ->leftjoin('employees as managerqa','qc_cpars.manager','=','managerqa.employee_id')
          ->whereNull('mutation_logs.valid_to')
          ->where('qc_cars.id','=',$id)
          ->get();

          $verifikasi = QcCar::select('qc_verifikasis.id as id_ver','qc_verifikasis.keterangan','qc_verifikasis.status','qc_verifikasis.tanggal')
             ->join('qc_verifikasis','qc_verifikasis.cpar_no','=','qc_cars.cpar_no')
             ->where('qc_verifikasis.cpar_no','=',$car->cpar_no)
             ->get(); 

          $carttds = QcCar::select('qc_cars.*','qc_ttd_cobas.ttd_car')
            ->leftjoin('qc_ttd_cobas','qc_cars.cpar_no','=','qc_ttd_cobas.cpar_no')
            ->where('qc_cars.id','=',$id)
            ->get();

          return view('qc_car.verifikasi_gm', array(
            'car' => $car,
            'carss' =>  $carttds,
            'cars' => $cars,
            'verifikasi' => $verifikasi
          ))->with('page', 'CAR');
      }

      public function save_sign(Request $request)
      {
          $id_user = Auth::id();

          $result = array();
          $imagedata = base64_decode($request->get('img_data'));
          $filename = md5(date("dmYhisA"));

          $file_name = './images/sign/'.$filename.'.png';
          file_put_contents($file_name,$imagedata);
          $result['status'] = 1;
          $result['file_name'] = $file_name;
          echo json_encode($result);

          $ttdcar = QcTtdCoba::where('cpar_no','=',$request->get('cpar_no'))->get();

          if(count($ttdcar) == 0){
            $ttd = new QcTtdCoba([
              'ttd_car' => $result['file_name'],
              'cpar_no' => $request->get('cpar_no'),
              'created_by' => $id_user
            ]);

            $ttd->save();

          } else{
            foreach ($ttdcar as $ttdcar) {
              $ttdcar2 = QcTtdCoba::find($ttdcar->id);
              $ttdcar2->ttd_car = $result['file_name'];
              $ttdcar2->save();              
            }
          }

          $cars = QcCar::find($request->get('id'));

          if ($cars->posisi == "gm") {            
            $cars->approved_gm = "Checked"; 
            $cars->email_status = "SentQA";
            $cars->email_send_date = date('Y-m-d');
            $cars->posisi = "qa";

            $cpar = QcCpar::select('qc_cpars.cpar_no','qc_cpars.id','qc_cpars.status_code','qc_cpars.posisi')
            ->join('qc_cars','qc_cars.cpar_no','=','qc_cpars.cpar_no')
            ->where('qc_cpars.cpar_no','=',$cars->cpar_no)
            ->get();

            foreach ($cpar as $cpar) {
                $cpar->status_code = "7";
                $cpar->posisi = "QA";
                $cpar->save();
            }

            if ($cars->car_cpar->kategori == "Eksternal" || $cars->car_cpar->kategori == "Supplier") {
              $to = "staff";
            }
            else if ($cars->car_cpar->kategori == "Internal") {
              $to = "foreman";
            }

            $mailto = "select distinct email,phone from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cpars.".$to." = employees.employee_id join users on employees.employee_id = users.username where qc_cars.id='".$request->get('id')."'";
            $mails = DB::select($mailto);

            foreach($mails as $mail){
              $mailtoo = $mail->email;
            }

            $cars->save();

            $query2 = "select qc_cars.*, qc_cpars.lokasi, qc_cpars.kategori, qc_cpars.sumber_komplain, qc_cpars.judul_komplain, employees.name as pic_name, qc_cpars.id as id_cpar from qc_cars join qc_cpars on qc_cars.cpar_no = qc_cpars.cpar_no join employees on qc_cars.pic = employees.employee_id where qc_cars.id='".$request->get('id')."'";

            $cars2 = db::select($query2);


            Mail::to($mailtoo)->send(new SendEmail($cars2, 'car'));
            // return redirect('/index/qc_car/verifikasigm/'.$request->get('id'))->with('status', 'E-mail has Been Sent To QA')->with('page', 'CAR');
          }
      }


}