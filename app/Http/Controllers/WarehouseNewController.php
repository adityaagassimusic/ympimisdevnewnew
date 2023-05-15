<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use DataTables;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Database\Builder;
use Illuminate\Support\Facades\DB;
use App\WarehousePackinglist;
use App\Libraries\ActMLEasyIf;
use Excel;
use File;
use PDF;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\AreaCode;
use App\StocktakingNewList;
use App\WarehouseVendor;
use App\CodeGenerator;
use App\MaterialImportMaster;
use App\WarehousePelayanan;
use App\EmployeeSync;
use App\WarehouseMaterialMaster;
use App\WarehousePackinglistTemp;
use App\WarehouseEmployeeMaster;
use App\MaterialPlantDataList;
use App\AccSupplier;
use App\WarehouseMaterialName;
use App\AbsenceCategory;
use App\MaterialHakoKanban;
use App\WarehouseTimeOperatorLog;
use App\WarehousePelayananLog;
use App\WarehousePelayananIn;
use App\WarehouseMaterialRak;
use App\WarehouseCompletionRequest;
use App\TransactionTransfer;
use App\WarehouseRequestMt;
use App\WarehouseLog;
use Carbon\Carbon;
use DateTime;
use FTP;

class WarehouseNewController extends Controller
{

  public function display_job(){
    $title = "Display Job";
    $title_jp = "";
    $material = db::select('SELECT material_number FROM material_import_masters');

    return view('warehouse_new.display_job', array(
      'title' => $title,
      'title_jp' => $title_jp,
      'material' => $material
    ))->with('head', 'Warehouse')->with('page', 'Warehouse Display');
  }

  public function index_pengantaran1(){
    $title = "Pengantaran Request";
    $title_jp = "";

    $area = AreaCode::select('area_code', 'area', 'remark')->get();

    return view('warehouse_new.index_pengantaran', array(
      'title' => $title,
      'title_jp' => $title_jp,
      'lokasi1' => $area
    ))->with('head', 'Pengantaran')->with('page', 'Pengantaran Request');
  }

  public function index()
  {
    $title = "Create Material";
    $title_jp = "??";

    $material_sr = db::select(' SELECT
      material_plant_data_lists.material_number as material_number,
      material_plant_data_lists.material_description as descr
      FROM
      stocktaking_new_lists
      LEFT JOIN material_plant_data_lists ON stocktaking_new_lists.material_number = material_plant_data_lists.material_number 
      WHERE
      stocktaking_new_lists.location = "MSTK";');

    $vendor = AccSupplier::select('acc_suppliers.*')->whereNull('acc_suppliers.deleted_at')
    ->distinct()
    ->get();

    return view('warehouse_new.index_packinglist', array(
     'title' => $title,
     'title_jp' => $title_jp,
     'material_sr' => $material_sr,
     'vendor' => $vendor
   ))->with('page', 'Create Packing List');
  }

  public function importPackinglist(Request $request)
  {
    if($request->hasFile('file')) {
     try{
       $id_user = Auth::id();

       $file = $request->file('file');
       $file_name = 'receive_'. date("ymd_h.i") .'.'.$file->getClientOriginalExtension();
       $file->move(public_path('data_file/packing_list/YMMJ/'), $file_name);
       $excel = public_path('data_file/packing_list/YMMJ/') . $file_name;

       $rows = Excel::load($excel, function($reader) {
        $reader->noHeading();
        $reader->skipRows(1);
      })->get();

       $rows = $rows->toArray();

       $gmc = [];
       $country = [];
       $description = [];
       $number_package = [];
       $quantity = [];
       $no_case = [];
       $index1 = 0;

       for ($i=0; $i < count($rows); $i++) {

        $no  = 2;
        $pkg = $rows[$i][7];
        if ($rows[$i][7] == "") {
         $pkg = 1;
       }

       if ($pkg > 1) {
         $qty = $rows[$i][9] / $pkg;
         $packing = $rows[$i][17];

         for ($z=0; $z < $pkg; $z++) { 
          $ivms = WarehousePackinglist::create([
           'no_invoice' => $rows[$i][0],      
           'no_delivery_order' => $rows[$i][15],      
           'gmc' => $rows[$i][1],
           'country' => $rows[$i][6],
           'description' => $rows[$i][4],
           'package' => $rows[$i][8],
           'number_package' => $rows[$i][7],
           'vendor' => $request->get('vendor'),
           'tanggal_kedatangan' => $request->get('createStart'),  
           'quantity' => $qty,
           'no_case' => $packing--,
           'quantity_check' => 0,
           'status_material' => "ok",
           'status_job' => "not done",
           'status_cek' => "not checked yet",
           'status_receive' => "wainting",
           'status_aktual'  => "wainting",
           'status_all' => "not yet",
           'created_by' => $id_user,
         ]);
        }
      } else {
       $ivms = WarehousePackinglist::create([
        'no_invoice' => $rows[$i][0],
        'no_delivery_order' => $rows[$i][15],     
        'gmc' => $rows[$i][1],
        'country' => $rows[$i][6],
        'description' => $rows[$i][4],
        'package' => $rows[$i][8],
        'number_package' => $rows[$i][7],
        'vendor' => $request->get('vendor'),
        'tanggal_kedatangan' => $request->get('createStart'),
        'quantity' => $rows[$i][9],
        'no_case' => $rows[$i][17],
        'quantity_check' => 0,
        'status_material' => "ok",
        'status_all' => "not yet",
        'status_job' => "not done",
        'status_cek' => "not checked yet",
        'status_receive' => "wainting",
        'status_aktual'  => "wainting",
        'created_by' => $id_user,
      ]);
     }
   }

   $response = array(
    'status' => true,
    'message' => 'Upload Berhasil',
  );
   return Response::json($response);
 }catch (\Exception $e) {
  $response = array(
    'error' => false,
    'message' => $e->getMessage()
  );
  return Response::json($response);
}
}else{
  $response = array(
    'status' => false,
    'message' => 'Upload failed, File not found',
  );
  return Response::json($response);
}

}

public function createNoSuratJalan(Request $request)
{
 try{
   $id_user = Auth::id();
   $lop = $request->get('lop');
   $cb = $request->get("no_surat_jalan");


   $code_generator = CodeGenerator::where('note', '=', 'SRWH')->first();
   $serial_number = sprintf("%'.0" . $code_generator->length ."d", $code_generator->index+1);

   $code_generator->index = $code_generator->index+1;
   $code_generator->save();

   for ($i=1; $i <= $lop ; $i++) {
    $gmc = "gmc".$i;
    $description = "description".$i;
    $qty = "qty".$i;

    $data = new WarehousePackinglist([
      'gmc' => $request->get($gmc),
      'description' => $request->get($description),
      'vendor' => $request->get("supplier_code"),
      'quantity' => $request->get($qty),
      'no_surat_jalan' => $cb,
      'no_case' => $serial_number,
      'status_receive' => "wainting",
      'status_aktual' => "wainting", 
      'status_material' => "ok", 
      'status_job' => "not done",
      'tanggal_kedatangan' => date("Y-m-d"),
      'created_by' => $id_user
    ]);
    $data->save();
  }
  return redirect('/index/create_packinglist')->with('status', 'Update Surat Jalan success')->with('page', 'Create Packing List');

}catch (\Exception $e) {
  return redirect('index/create_packinglist')->with('error', $e->getMessage())->with('page', 'Create Packing List');
}

}

public function fetchPackinglist(Request $request)
{
  try {
   $wr = DB::select("SELECT
    no_surat_jalan,
    COUNT( no_surat_jalan ) AS total,
    no_invoice,
    COUNT( no_invoice ) AS total1,
    vendor,
    tanggal_kedatangan
    FROM
    warehouse_packinglists
    WHERE status_material = 'ok' 
    GROUP BY
    no_surat_jalan,
    no_invoice,
    tanggal_kedatangan,
    vendor
    ORDER BY
    updated_at DESC");

   $detail_material_in = db::select('SELECT
    id,
    no_invoice,
    no_surat_jalan,
    gmc,
    description,
    quantity,tanggal_kedatangan,no_case 
    FROM
    warehouse_packinglists 
    ORDER BY
    id ASC');

    // $total = WarehousePackinglist::where('no_invoice', '=', $request->get('no_sjs'))->where('status_material','=', null)->get();

   $response = array(
    'status' => true,
    'message' => 'Get Data Success',
    'datas' => $wr,
    'det_materials' => $detail_material_in
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

public function deletegmcPk(Request $request){
  try{
    $tes=  $request->get('no_inv');
    $tes1=  $request->get('id');

    if ($tes1 == "null") {
      $delete1 = DB::table('warehouse_packinglists')
      ->where('no_invoice', '=', $request->get('no_inv'))
      ->delete();
      $response = array(
        'status' => true,
        'message' => 'Delete successful',   
      );
      return Response::json($response); 
    }else{
      $delete = DB::table('warehouse_packinglists')
      ->where('no_surat_jalan', '=', $request->get('id'))
      ->delete(); 
      $response = array(
        'status' => true,
        'message' => 'Delete successful',   
      );
      return Response::json($response);
    }
  }catch(\Exception $e){
    $response = array(
      'status' => false,
      'message' => $e->getMessage(),
    );
    return Response::json($response);
  }
}

public function index_internal($invoice){
  $title = 'Warehouse Internal Job';
  $title_jp = '';

  $area = WarehouseMaterialMaster::select('barcode', 'gmc_material','description','keterangan','sloc_name')->get();

  $st = WarehousePackinglist::select('status')
  ->distinct()
  ->get();

  return view('warehouse_new.warehouse_internal', array(
   'title' => $title,
   'title_jp' => $title_jp,
   'name' => Auth::user()->name,
   'area' => $area,
   'status' => $st

 ))->with('page', 'Warehouse Internal Job');
}


public function get_job_new(Request $request){
 $tanggal = date('Y-m-d');
 $pic_job = Auth::user()->username;
 $no_case = $request->get('no_case');



 $cek_job = db::select('SELECT * 
  FROM
  warehouse_packinglists 
  WHERE pic_cek = "'.$pic_job.'" && status_receive = "drop" && no_case = "'.$request->get('no_case').'"');
 $response = array(
  'status' => true,            
  'datasu' => $cek_job,
  'message' => 'Success'
);
 return Response::json($response);
}

public function fetchInternal(Request $request){
  $op_name = Auth::user()->name;
  $no_invoice = $request->get('no_invoice');

  $job = db::select('SELECT DISTINCT
   no_case,
   pic_job,
   SUBSTR(pic_job,1,(LOCATE(" ",pic_job)))  AS FIRSTTNAME
   FROM
   warehouse_packinglists 
   WHERE
   status_receive = "wainting" and no_invoice = "'.$no_invoice.'"  ORDER BY no_case ASC');

  $mp = db::select('SELECT SUBSTR(pic_job,1,(LOCATE(" ",pic_job)))  AS FIRSTTNAME,
   no_case,
   pic_job
   FROM
   warehouse_packinglists 
   WHERE
   status_job = "progress" and pic_job = "'.$op_name.'"');

  $response = array(
   'status' => true,
   'status_emp' => $mp,
   'job' => $job
 );
  return Response::json($response);
}

public function fetch_detail_list(Request $request){
  try{
   $op_name = Auth::user()->name;
   $number = $request->get('number_pallet');

   $detail = WarehousePackinglist::where('no_case', '=', $number)->where('pic_job', '=', $op_name)->get();

   $job = db::select('SELECT DISTINCT
    no_case
    FROM
    warehouse_packinglists 
    WHERE
    no_case = "'.$number.'" ORDER BY no_case ASC ' );


   if (count($detail) > 0) {  
    $response = array(
     'status' => true,
     'message' => 'Data Ditemukan',
     'detail' => $detail,
     'job' => $job
   );
    return Response::json($response);
  }
  else{
    $response = array(
     'status' => false,
     'message' => 'Data Tidak Ditemukan',
   );
    return Response::json($response);
  }

}catch(\Exception $e){
 $response = array(
  'status' => false,
  'message' => $e->getMessage(),
);
 return Response::json($response);
}
}

public function savePenataan(Request $request){

  $warehouse = WarehousePackinglist::find($id[$i]);
  $warehouse->status_job = "finish";
  $warehouse->save();

}


public function createDetail(Request $request)
{
  $id_user = Auth::id();
  $op_name = Auth::user()->username;
  $employee_id = Auth::user()->username;
  $id = $request->get('id');
  // dd($id);
  $quantity_check = $request->get('qty');
  $kds = $request->get('kds');
  $no_gmc = $request->get('no_gmc');
  $no_mt = $request->get('no_mt');
  $tanggal = date('Y-m-d');


  for ($i=0; $i < count($id) ; $i++) {
   $warehouse = WarehousePackinglist::find($id[$i]);
   $no_case = $warehouse->no_case;
   if ($warehouse->quantity > $quantity_check[$i]) {
    $create_detail = new WarehousePackinglist([
     'no_delivery_order' => $warehouse->no_delivery_order,
     'gmc' => $warehouse->no_invoice,
     'tanggal_kedatangan' => $warehouse->tanggal_kedatangan,
     'number_package' => 1,
     'gmc' => $warehouse->gmc,
     'country' =>$warehouse->country,
     'description' =>$warehouse->description ,
     'number_package' => 1,
     'vendor' => $warehouse->vendor,
     'quantity' => $warehouse->quantity - $quantity_check[$i],
     'quantity_check' => 0,
     'status' => $kds[$i],
     'status_all' => "not yet",
     'no_case' => $no_gmc[$i],
     'status_job' => "not done",
     'status_cek' => "not checked yet",
     'status_receive' => "wainting",
     'status_aktual' => "penataan",
     'created_by' => $id_user
   ]);
    $create_detail->save();

    $warehouse->quantity  = $warehouse->quantity;
    $warehouse->status  = $kds[$i];
    $warehouse->quantity_check  = $quantity_check[$i];
    $warehouse->status_aktual = "penataan";
    $warehouse->status_cek = "checked";
    $warehouse->pic_cek = $op_name;
    $warehouse->pic_receive = $op_name;
    $warehouse->pic_job = $op_name;
    $warehouse->employee_id = Auth::user()->username;
    $warehouse->end_check = date('Y-m-d H:i:s');
    $warehouse->start_move = date('Y-m-d H:i:s');
    $warehouse->created_by = $op_name;
    $warehouse->save();

  }
  else{
    $warehouse->quantity_check  = $quantity_check[$i];
    $warehouse->status  = $kds[$i];
    $warehouse->status_cek = "checked";
    $warehouse->pic_cek = $op_name;
    $warehouse->pic_receive = $op_name;
    $warehouse->status_aktual = "penataan";
    $warehouse->pic_job = $op_name;
    $warehouse->employee_id = Auth::user()->username;
    $warehouse->end_check = date('Y-m-d H:i:s');
    $warehouse->start_move = date('Y-m-d H:i:s');
    $warehouse->created_by = $op_name;
    $warehouse->save();

    $warehouset = WarehouseEmployeeMaster::where('employee_id','=',$warehouse->employee_id)->update([
      'start_time_status' => $warehouse->start_move,
      'status' => "work",
      'status_aktual_pekerjaan' => "penataan import"
    ]);
  }
}

$get_mr = db::select('SELECT id,employee_id,status,end_job FROM `warehouse_time_operator_logs` where `status`="pengecekan" and employee_id = "'.$op_name.'" and request_desc = "'.$no_case.'" order by employee_id DESC LIMIT 1');

if ($get_mr[0]->status == "pengecekan" && $get_mr[0]->end_job == null) {
  $get_last1 = db::select('SELECT id,employee_id,status,end_job FROM `warehouse_time_operator_logs` where request_desc = "'.$no_case.'" AND end_job is null AND status = "pengecekan" order by employee_id DESC');

  $job_last1 = WarehouseTimeOperatorLog::where('id','=',$get_last1[0]->id)->where('status','=','pengecekan')->update([
   'end_job' => date('Y-m-d H:i:s')
 ]);

  $group_gmc1 = db::select('SELECT no_case,GROUP_CONCAT(gmc) as gmc,status_aktual
    FROM warehouse_packinglists
    WHERE tanggal_kedatangan_aktual = "'.$tanggal.'" AND no_case = "'.$no_case.'"
    GROUP BY no_case,status_aktual');

  for ($i=0; $i < count($group_gmc1) ; $i++) {

    $data = new WarehouseTimeOperatorLog([
      'employee_id' => $get_last1[0]->employee_id,
      'request_desc' => $group_gmc1[0]->no_case,
      // 'desc_gmc' => $group_gmc1[0]->gmc,
      'status' => $group_gmc1[0]->status_aktual,
      'start_job' => date("Y-m-d H:i:s")
    ]);
    $data->save();
  }
}


$detail_mod = db::select('SELECT
 warehouse_packinglists.id,
 warehouse_packinglists.gmc,
 warehouse_packinglists.no_case,
 warehouse_packinglists.description,
 stocktaking_new_lists.store,
 warehouse_packinglists.quantity
 FROM
 warehouse_packinglists
 LEFT JOIN stocktaking_new_lists ON warehouse_packinglists.gmc = stocktaking_new_lists.material_number 
 WHERE warehouse_packinglists.status_aktual = "penataan"
 AND warehouse_packinglists.no_case = "'.$no_mt.'"
 AND stocktaking_new_lists.location = "MSTK" 
 ORDER BY
 warehouse_packinglists.gmc');

$area = StocktakingNewList::select('store', 'material_number')->get();



$response = array(
 'status' => true,
 'remark' => 'logged_in',
 'detail_mod' => $detail_mod,
 'area' => $area
);
return Response::json($response);
}

public function createJob(Request $request)
{
  $id_user = Auth::id();
  $number_pallet = $request->get('number_pallet');
  $op_name = Auth::user()->name;
  $employee_id = Auth::user()->username;

  $warehouse = WarehousePackinglist::where('no_case','=',$number_pallet)->first();
  $warehouse->update([
   'status_job' => 'progress',
   'tanggal' => date('Y-m-d'),
   'employee_id' => $employee_id,
   'start_check' => date('Y-m-d H:i:s'),
   'status_aktual' => "sedang di check",
   'pic_job' => $op_name
 ]);

  $warehouse = WarehouseEmployeeMaster::where('employee_id','=',$warehouse->employee_id)->update([
    'start_time_status' => $warehouse->start_check,
    'status' => "work",
    'status_aktual_pekerjaan' => "pengecekan import"
  ]);
  
  $response = array(
   'status' => true,
   'remark' => 'logged_in',
 );
  return Response::json($response);
}

public function finish_job(Request $request)
{
  try {
   $id_user = Auth::id();
   $op_name = Auth::user()->name;

      // DATE_FORMAT(auth_datetime,'%H:%i') as time_in

   $job = db::select('SELECT
        *,
    FLOOR( TIMESTAMPDIFF( SECOND, start_check, end_check )/ 60 ) AS `minute`,
    TIMESTAMPDIFF( SECOND, start_check, end_check ) % 60 AS `second` 
    FROM
    warehouse_packinglists 
    WHERE
    status_job = "finish" && pic_job = "'.$op_name.'"');

   $response = array(
    'status' => true,
    'datam' => $job
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
public function fetch_import(Request $request)
{
  try {
   $id_user = Auth::id();
   $op_name = Auth::user()->name;
   $number = $request->get('tanggal');

   $fetch1 = db::select('SELECT
    no_invoice,
    country,
    vendor,id,tanggal_kedatangan,tanggal_kedatangan_aktual,no_case
    FROM
    warehouse_packinglists 
    WHERE
    tanggal_kedatangan_aktual = "'.$number.'" && status_receive = "wainting" && status_all = "not yet"
    GROUP BY no_invoice,
    country,
    vendor,id,tanggal_kedatangan,tanggal_kedatangan_aktual,no_case
    ORDER BY
    no_invoice ASC');

   $response = array(
    'status' => true,
    'fetch1' => $fetch1
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

public function indexDropExim()
{
  $title = "Drop Material Import";
  $title_jp = "??";

  return view('warehouse_new.penurunan_exim', array(
   'title' => $title,
   'title_jp' => $title_jp
 ))->with('page', 'Drop Material Import');
}

public function fetchDropExim(Request $request){
  try {
    if ($request->get('tanggal')) {
      $tanggal = $request->get('tanggal');
    } else {
      $tanggal = date('Y-m-d');
    }
    $exim_list = db::select('SELECT DISTINCT
      no_case,
      no_invoice,
      no_surat_jalan,number_package,package
      FROM
      warehouse_packinglists 
      WHERE
      status_receive = "wainting" && tanggal_kedatangan = "'.$tanggal.'" GROUP BY no_case,no_invoice,no_surat_jalan,number_package,package ORDER BY no_case ASC');

    $response = array(
      'status' => true,
      'exim' => $exim_list
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

public function postDropExim(Request $request)
{
  try {

    // dd($request->get("number_pallet"));

   WarehousePackinglist::where('no_case', '=', $request->get("number_pallet"))
   ->update([
    'status_receive' => "drop",
    'status_exim' => "sudah diturunkan",
    'tanggal_kedatangan_aktual' => date('Y-m-d'),
    'pic_drop' => Auth::user()->username
  ]);

   $response = array(
    'status' => true,
    'message' => 'Success',
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

public function fetchEximFinish(Request $request){
  try {
    $tanggal = date("Y-m-d");
    $exim_drop = db::select('SELECT DISTINCT
      no_case,
      vendor,
      status_exim 
      FROM
      warehouse_packinglists 
      WHERE
      tanggal_kedatangan_aktual = "'.$tanggal.'"
      AND status_exim = "sudah diturunkan" 
      ORDER BY
      no_case ASC');

    $response = array(
      'status' => true,
      'drop' => $exim_drop
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

public function postFinishInter(Request $request)
{
  $id_user = Auth::id();
  $user = Auth::user()->username;

  $gmc = $request->get('gmc');
  $no_case = $request->get('case_pallet');
  $ids = $request->get('ids');
  $tanggal = date("Y-m-d");
  $request_id = [];


  for ($i=0; $i < count($ids); $i++) {
    $warehouse = WarehousePackinglist::where('id','=',$ids[$i])->where('gmc','=',$gmc[$i])
    ->first();

    $warehouse->update([
      'status_job' => 'finish',
      'status_all' => 'finish',
      'created_by' =>  $user,
      'status_aktual' => 'Finish Penataan ',
      'status_receive' => 'finish',
      'end_move' => date('Y-m-d H:i:s')
    ]);

    $warehouset = WarehouseEmployeeMaster::where('employee_id','=',$warehouse->employee_id)->update([
      'start_time_status' => $warehouse->end_move,
      'status' => "idle",
      'status_aktual_pekerjaan' => null

    ]);
  }
  $get_mr = db::select('SELECT id,employee_id,status,end_job FROM `warehouse_time_operator_logs` where `status`="penataan" and employee_id = "'.$user.'" and end_job is null order by employee_id DESC LIMIT 1');

  if ($get_mr[0]->status == "penataan" && $get_mr[0]->end_job == null) {
    $get_last2 = db::select('SELECT id,employee_id,status,end_job FROM `warehouse_time_operator_logs` where request_desc = "'.$no_case[0].'" AND end_job is null AND status = "penataan" order by employee_id DESC');

    $job_last2 = WarehouseTimeOperatorLog::where('id','=',$get_last2[0]->id)->where('status','=','penataan')->update([
     'end_job' => date('Y-m-d H:i:s')
   ]);

    $group_gmc2 = db::select('SELECT no_case,GROUP_CONCAT(gmc) as gmc,status_aktual
      FROM warehouse_packinglists
      WHERE tanggal_kedatangan_aktual = "'.$tanggal.'" AND no_case = "'.$no_case[0].'" GROUP BY no_case,status_aktual');

    for ($j=0; $j < count($group_gmc2); $j++) { 
      array_push($request_id, $group_gmc2[$j]->no_case);
    }

    for ($i=0; $i < count($request_id) ; $i++) {

      $data = new WarehouseTimeOperatorLog([
        'employee_id' => $get_last2[0]->employee_id,
        'status' => "idle",
        'start_job' => date("Y-m-d H:i:s")
      ]);
      $data->save();
    }
  }

  $response = array(
   'status' => true,
   'remark' => 'logged_in',
 );
  return Response::json($response);
}

public function postVendor(Request $request)
{
  $id_user = Auth::id();
  $country = $request->get('country');
  $vendor = $request->get('vendor');

  $code_generator = CodeGenerator::where('note', '=', 'yamaguchi')->first();
  $serial_number = sprintf("%'.0" . $code_generator->length ."d", $code_generator->index+1);
  
  $code_generator->index = $code_generator->index+1;
  $code_generator->save();

  $create_vendor = new WarehousePackinglist([
   'country' => $country,
   'no_case' => $serial_number,
   'vendor' => $vendor,
   'created_by' => $id_user,
   'status_receive' => "wainting",
   'status_aktual' => "wainting",
   'status_job' => "not done",
   'status_cek' => "not checked yet",
   'tanggal_kedatangan' => date('Y-m-d'),
   'status_all' => "not yet"
 ]);
  $create_vendor->save();

  $response = array(
   'status' => true,
   'remark' => 'logged_in',
 );
  return Response::json($response);
}

public function getGmc( Request $request)
{
  try {
   $ven = DB::SELECT('SELECT
    material_number,
    description 
    FROM
    material_import_masters 
    WHERE
    material_number = "'.$request->get('gmc').'"');

   if (count($ven) > 0) {
    $response = array(
     'status' => true,
     'message' => 'Success',
     'ven' => $ven
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
public function updateVendor(Request $request)
{
  $id = Auth::id();
  $lop = $request->get('lop');

  for ($i=1; $i <= $lop ; $i++) {


   $gmc = "gmc".$i;
   $Description = "Description".$i;
   $qty = "qty".$i;

   $data2 = WarehousePackinglist::where('id', $request->get('id_packing'))->update([
    'no_invoice' => $request->get('no_invoice'),
    'gmc' => $request->get($gmc),
    'description' => $request->get($Description),
    'quantity' => $request->get($qty)
  ]); 
   if ($i == 1) {
   }else{
    $data = new WarehousePackinglist([
      'gmc' => $request->get($gmc),
      'description' => $request->get($Description),
      'vendor' => $request->get('vendor1'),
      'country' => $request->get('country1'),
      'no_case' => $request->get('no_case1'),
      'quantity' => $request->get($qty),
      'no_invoice' => $request->get('no_invoice'),
      'status_job' => "not done",
      'status_cek' => "not checked yet",
      'status_all' => "not yet",
      'status_exim' => "sudah diturunkan",
      'status_receive' => "received",
      'tanggal_kedatangan' => $request->get('tanggal_kedatangan1'),
      'tanggal_kedatangan_aktual' => $request->get('tanggal_kedatangan_aktual1'),


      'created_by' => $id
    ]);
    $data->save();
  }

  $response = array(
    'status' => true,
    'remark' => 'logged_in',
  );
}
return redirect('index/display/job')->with('page', 'Warehouse Display')->with('status', 'Tambah Data Berhasil');
}
public function indexPelayanan()
{
  $title = "Request Produksi";
  $title_jp = "??";

  $barcode = WarehouseMaterialMaster::select('barcode', 'gmc_material','description','lot','keterangan','sloc_name','no_hako','loc')->get();

  $area = AreaCode::select('area_code', 'area', 'remark')->get();


  return view('warehouse_new.index_request', array(
   'title' => $title,
   'title_jp' => $title_jp,
   'barcode' => $barcode,
   'area' => $area
 ))->with('page', 'Request Produksi');
}
public function postPelayanan(Request $request)
{
  $id_user = Auth::id();
    // $id = $request->get('data');
  $id = $request->get('qty_cek');
  $gmc = $request->get('items');
  $area = $request->get('areas');
  $code = $request->get('lokasis');

  $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
  ->select('department')
  ->first();

  $code_generator = CodeGenerator::where('note', '=', 'pelayanan')->first();
  $serial_number = sprintf("%'.0" . $code_generator->length ."d", $code_generator->index+1);
  $code_generator->index = $code_generator->index+1;
  $code_generator->save();

  $op_name = Auth::user()->departement;
  $now = intval(date('H'));

  if(($now >= 7 && $now <= 10) || $now >= 10 && $now <= 17 || $now >= 18 && $now <= 19 || $now >= 21 && $now <= 22){
    for ($i=0; $i < count($id); $i++) {
     $create_vendor = new WarehousePelayanan([
      'gmc' => $gmc[$i]['gmc'],
      'description' => $gmc[$i]['desc'],
      'lot' => $gmc[$i]['qty'],
      'sloc_name' => $gmc[$i]['sloc_name'],
      'loc' => $gmc[$i]['lokasi'],
      'no_hako' => $gmc[$i]['no_hako'],
      'area_code' => $code,
      'quantity_request'=> $id[$i],
      'pic_produksi' => Auth::user()->name,
      'area' => $area,
      'kode_request' => $serial_number,
      'tanggal' => date('Y-m-d '),
      'status_pel' => "not yet",
      'status_aktual' => "not yet",
      'status_all' => "not yet",
      'status_pengantaran' => "not yet",
      'created_by' => $id_user
    ]);
     $create_vendor->save();

   }

   $response = array(
     'status' => true,
     'remark' => 'logged_in',
     'data' => $id
   );
   return Response::json($response);

 }else{
   $response = array(
     'status' => false
   );
   return Response::json($response);

 }


}

public function fetch_pelayanan(Request $request)
{
  try {
   $id_user = Auth::id();
   $op_name = Auth::user()->name;
   $tanggal = date('Y-m-d');

      // $number = $request->get('tanggal');

   $pel = db::select('SELECT 
    kode_request, area,tanggal, COUNT(kode_request) as total
    FROM
    warehouse_pelayanans 
    WHERE status_aktual = "proses0" and tanggal = "'.$tanggal.'"
    GROUP BY kode_request, area,tanggal
    ORDER BY
    kode_request ASC');

   $response = array(
    'status' => true,
    'pel' => $pel
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

public function indexDetailPelayanan($kode_request)
{
  $title = "Detail Request Pelayanan";
  $title_jp = "??";

  return view('warehouse_new.detail_pelayanan', array(
   'title' => $title,
   'title_jp' => $title_jp,
 ))->with('page', 'Detail Request Pelayanan');
}

public function fetchDetailPelayanan(Request $request){

  $op_name = Auth::user()->name;
  $kode_request = $request->get('kode_request');

  $job = db::select('SELECT
    gg.id,
    gg.kode_request,
    eg.rak,
    gg.lot,
    gg.gmc,
    gg.description,
    gg.quantity_request,
    gg.no_hako 
    FROM
    warehouse_pelayanans AS gg
    LEFT JOIN warehouse_material_raks AS eg ON gg.gmc = eg.gmc 
    WHERE
    gg.kode_request = "'.$kode_request.'" 
    AND gg.status_aktual = "check" 
    ORDER BY
    gg.gmc ASC, gg.no_hako ASC'); 

  $response = array(
   'status' => true,
   'job' => $job,
   'kode' => $kode_request
 );
  return Response::json($response);
}


public function savepelayanan(Request $request)
{
  $id_user = Auth::id();
  $gmc = $request->get('gmc');
  $status_material = $request->get('status_material');  
  $id = $request->get('id');  

  for ($i=0; $i < count($id); $i++) {
   $warehouse = WarehousePelayanan::where('id','=',$id[$i])->update([
    'status_pengantaran' => 'Wainting Pengantaran',
    'status_material' => $status_material[$i],
  ]);
 }
 $response = array(
   'status' => true,
   'remark' => 'logged_in',
   'id' => $id
 );
 return Response::json($response);
}

public function updatePermintaan(Request $request)
{
  $id_user = Auth::id();
  $kode_request = $request->get('kode_request');

  $warehouse = WarehousePelayanan::where('kode_request','=',$kode_request)->update([
   'status_aktual' => "check",
   'pic_pelayanan' => Auth::user()->username,
   'start_pel' => date('Y-m-d H:i:s')
 ]);
  $response = array(
   'status' => true,
   'remark' => 'logged_in',
   'pel' => $kode_request
 );
  return Response::json($response);
}

public function fetchRequest(Request $request)
{
  try {
   $id_user = Auth::id();
   $op_name = Auth::user()->name;
   $tanggal = date('Y-m-d');
   $job = db::select('SELECT DISTINCT
    kode_request,
    status_pel,status_aktual,
    COUNT(kode_request) as total 
    FROM
    warehouse_pelayanans 
    WHERE
    tanggal = "'.$tanggal.'" 
    GROUP BY
    kode_request,status_pel,status_aktual, ASC');

   $response = array(
    'status' => true,
    'req' => $job


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

public function updatePelayanan(Request $request)
{
  $id_user = Auth::id();
  $op_name = Auth::user()->username;
  $tanggal = date('Y-m-d');
  $id = $request->get('id');
  $qty = $request->get('qty');
  $st = $request->get('status');

  // dd(count($id));
  for ($i=0; $i < count($id) ; $i++) {
    // dd($i);
    $warehouse = WarehousePelayanan::find($id[$i]);

    $warehouse->quantity_check = $qty[$i];
    $warehouse->status_aktual = "proses1";
    $warehouse->status = $st[$i];
    $warehouse->pic_pelayanan = Auth::user()->username;
    $warehouse->end_pel = date('Y-m-d H:i:s');
    $warehouse->save();
    
  }

  // dd('s');
  $response = array(
    'status' => true,
    'remark' => 'logged_in',
    'pel' => $qty
  );
  $employee_groups = DB::table('warehouse_employee_masters')
  ->where('employee_id', '=', $warehouse->pic_pelayanan)          
  ->update([
    'start_time_status' => date('M d Y H:i:s'),
    'status' => "idle",
    'status_aktual_pekerjaan' => null
  ]);

  $pl_1 = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanans` where id = "'.$id[0].'" LIMIT 1');


  $get_pl_1 = db::select('SELECT id,employee_id,status,end_job FROM `warehouse_time_operator_logs` where `status`="check" and employee_id = "'.$op_name.'" and request_desc = "'.$pl_1[0]->kode_request.'" order by employee_id DESC LIMIT 1');

  if ($get_pl_1[0]->status == "check" && $get_pl_1[0]->end_job == null) {
    $get_pl1 = db::select('SELECT id,employee_id,status,end_job FROM `warehouse_time_operator_logs` where request_desc = "'.$pl_1[0]->kode_request.'" AND end_job is null AND status = "check" order by employee_id DESC');

    $job_pl1 = WarehouseTimeOperatorLog::where('id','=',$get_pl1[0]->id)->where('status','=','check')->update([
     'end_job' => date('Y-m-d H:i:s')
   ]);

    $group_gmc_pl1 = db::select('SELECT kode_request,GROUP_CONCAT(gmc) as gmc,status_aktual
      FROM warehouse_pelayanans
      WHERE tanggal = "'.$tanggal.'" AND kode_request = "'.$pl_1[0]->kode_request.'"
      GROUP BY kode_request,status_aktual');

    for ($i=0; $i < 1 ; $i++) {

      $data1 = new WarehouseTimeOperatorLog([
        'employee_id' => $get_pl1[0]->employee_id,
        'status' => "idle",
        'start_job' => date("Y-m-d H:i:s")
      ]);
      $data1->save();
    }

    $get_pl_log = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanan_logs` where kode_request = "'.$pl_1[0]->kode_request.'" LIMIT 1');


    $update_pl = WarehousePelayananLog::where('kode_request','=',$get_pl_log[0]->kode_request)->update([
     'status_aktual' => $get_pl_log[0]->status_aktual.","."check"
   ]);
  }

  return Response::json($response);
}

public function index_pengantaran()
{
  $title = "Pengantaran Request ";
  $title_jp = "??";

  $area = AreaCode::select('area_code', 'area', 'remark')->get();

  return view('warehouse_new.pengantaran', array(
    'title' => $title,
    'title_jp' => $title_jp,
    'lokasi1' => $area
  ))->with('page', 'Pengantaran Request');
}

public function index_pengecekan()
{
  $title = "Pengecekan Material";
  $title_jp = "??";


  return view('warehouse_new.pengecekan_material', array(
    'title' => $title,
    'title_jp' => $title_jp
  ))->with('page', 'Pengecekan');
}


public function fetchPengantaran(Request $request){
  $op_name = Auth::user()->name;

  $mp = db::select('SELECT DISTINCT SUBSTR(pic_pelayanan,1,(LOCATE(" ",pic_pelayanan)))  AS FIRSTNAME,
    kode_request,
    pic_pelayanan,
    departement
    FROM
    warehouse_pelayanans 
    WHERE
    status_pengantaran = "Waiting Pengantaran"');

  $area = AreaCode::select('area_code', 'area', 'remark')->get();

  $response = array(
    'status' => true,
    'status_pen' => $mp,
    'lokasi1' => $area
  );
  return Response::json($response);
}

public function fetchHistoryPelayanan(Request $request)
{
  try {
    $id_user = Auth::id();
    $op_name = Auth::user()->name;
    $tanggal = date('Y-m-d');
    $historya = db::select('SELECT
               * 
      FROM
      warehouse_pelayanans 
      WHERE
      pic_pelayanan = "'.$op_name.'" and tanggal = "'.$tanggal.'"');

    $response = array(
      'status' => true,
      'history' => $historya
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

public function fetchPeng(Request $request)
{
  try {
    $id_user = Auth::id();
    $op_name = Auth::user()->name;
    $tanggal = date('Y-m-d');
    $pengantaran = db::select('SELECT
      gg.id,
      gg.kode_request,
      gg.gmc,
      gg.description,
      gg.lot,
      gg.quantity_check,
      gg.quantity_request,
      gg.status_aktual,
      gg.area,
      eg.name,
      ek.name as nam,
      gg.pic_pelayanan
      FROM
      warehouse_pelayanans AS gg
      LEFT JOIN users AS eg ON gg.pic_produksi = eg.username
      LEFT JOIN employee_syncs AS ek ON gg.pic_pelayanan = ek.employee_id
      WHERE 
      gg.status_aktual = "proses1" 
      ORDER BY
      gg.kode_request ASC');

    $response = array(
      'status' => true,
      'pengantaran' => $pengantaran
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

public function updatePengantaran(Request $request)
{
  try {

    $id_user = Auth::id();
    $id = $request->get('id');
  // $id = explode("_",$request->get('id'));

    $op_name = Auth::user()->username;
    $tanggal = date('Y-m-d');
    $lokasi_awal = $request->get('lokasi_awal');

    $request_id = [];
    $employee_id = [];
    $gmc = [];
    $id_new = [];
    $status = [];

    $emp = 1;


    $pl_1 = db::select('SELECT employee_id,status FROM `warehouse_time_operator_logs` where employee_id = "'.$op_name.'" AND end_job is null LIMIT 1');

   //  if ($pl_1[0]->status == "idle") {
   //    $emp = 1;
   //  }else{
   //   $emp = 0;
   //   $response = array(
   //    'status' => false,
   //    'message' => "Operator Sedang Bekerja"
   //  );
   // }



   if ($emp == 1) {

    for ($i=0; $i < count($id) ; $i++) {
      $warehouse = WarehousePelayanan::find($id[$i]);
    // $warehouse->status_pengantaran = "Proses Pengantaran";
      $update_st = DB::table('warehouse_time_operator_logs')
      ->where('employee_id', '=', $op_name)->where('status', '=', "idle")->where('end_job', '=', null)->limit(1)
      ->update([
        'end_job' => date('Y-m-d H:i:s')
      ]);
      $warehouse->pic_pengantaran = Auth::user()->name;
      $warehouse->employee_id_pengantaran = Auth::user()->username;
      $warehouse->status_pel = "progress";
      $warehouse->status_aktual = "Pengantaran";
      $warehouse->lokasi_awal = $lokasi_awal;
      $warehouse->start_pengantaran = date('Y-m-d H:i:s');


      $updat_emp = DB::table('warehouse_employee_masters')
      ->where('employee_id', '=', $warehouse->pic_pelayanan)          
      ->update([
        'start_time_status' => date('M d Y H:i:s'),
        'status' => "work",
        'status_aktual_pekerjaan' => "pengantaran"
      ]);
    // $get_pl2 = db::select('SELECT id,employee_id,status,end_job FROM `warehouse_time_operator_logs` where `status`="idle" and employee_id = "'.$op_name.'" and end_job is null order by employee_id DESC LIMIT 1');


    // $get = db::select('SELECT id,kode_request FROM `warehouse_pelayanans` where id = "'.$id[$i].'"');

      array_push($id_new, $id[$i]);

      $warehouse->save();

      $response = array(
        'status' => true,
        'remark' => 'logged_in'
      );
    }

    $group_gmc_pl2 = db::select('SELECT kode_request,GROUP_CONCAT(gmc) as gmc,status_aktual
      FROM warehouse_pelayanans
      WHERE id in ('.join(',',$id_new).') and status_aktual = "Pengantaran"
        GROUP BY kode_request,status_aktual');



      for ($j=0; $j < count($group_gmc_pl2); $j++) { 
        array_push($request_id, $group_gmc_pl2[$j]->kode_request);
        array_push($employee_id, $op_name);
        array_push($gmc, $group_gmc_pl2[$j]->gmc);
      }


      for ($l=0; $l < count($request_id); $l++) {

        $get_pl_log4 = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanan_logs` where kode_request = "'.$request_id[$l].'"');

        $ids = explode(",",$get_pl_log4[0]->status_aktual);

        $data1 = new WarehouseTimeOperatorLog([
          'employee_id' => $employee_id[$l],
          'request_desc' => $request_id[$l],
          'status' => $group_gmc_pl2[$l]->status_aktual,
      // 'desc_gmc' => $gmc[$l],
          'start_job' => date("Y-m-d H:i:s")
        ]);
        $data1->save();


        $get_pl_log = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanan_logs` where kode_request = "'.$request_id[$l].'"');

        $ids = explode(",",$get_pl_log[0]->status_aktual);

        if (count($ids) == 4) {

        }else{

          $update_pl = WarehousePelayananLog::where('kode_request','=',$request_id[$l])->update([
           'status_aktual' => $get_pl_log[0]->status_aktual.",proses1"
         ]);
        }

   //  for ($i=0; $i < 1; $i++) { 
   //   $data1 = new WarehouseTimeOperatorLog([
   //    'employee_id' => $employee_id[$i],
   //    'status' => "idle",
   //    'start_job' => date("Y-m-d H:i:s")
   //  ]);
   //   $data1->save();
   // }
      }
    }
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage(),
    );
    return Response::json($response);
  }
}

public function fetchCekPengantaran(Request $request){
  $op_name = Auth::user()->name;
  $op_nik = Auth::user()->username;

  $tanggal = date('Y-m-d');

  $cek = db::select('SELECT DISTINCT
    kode_request,
    pic_pelayanan,
    area,
    area_code,
    lokasi_awal,
    COUNT( kode_request ) AS total_material 
    FROM
    warehouse_pelayanans 
    WHERE
    status_aktual = "Pengantaran" AND employee_id_pengantaran = "'.$op_nik.'"
    GROUP BY
    kode_request,
    area,
    area_code,
    lokasi_awal,
    pic_pelayanan 
    ORDER BY
    kode_request');

  $cek_pen = db::select('SELECT
    ss.id,
    ss.kode_request,
    ss.pic_pelayanan,
    ss.area,
    ss.gmc,
    ss.description,
    ss.lot,
    ss.uom,
    ss.quantity_request,
    ss.quantity_check,
    ss.no_hako,
    es.name
    FROM
    warehouse_pelayanans AS ss
    LEFT JOIN employee_syncs AS es ON ss.pic_produksi = es.employee_id
    LEFT JOIN warehouse_pelayanan_logs AS pl ON ss.kode_request = pl.kode_request 
    WHERE ss.status_aktual = "Pengantaran" 
    ORDER BY
    kode_request ASC');



  $cek_pengantaran = db::select('SELECT
               * 
    FROM
    warehouse_pelayanans
    WHERE
    status_aktual = "proses1"');

  $response = array(
    'status' => true,
    'cek_lokasi' => $cek,
    'cek_pen' => $cek_pen,
    'cek_pengantaran' => $cek_pengantaran
  );
  return Response::json($response);
}



public function updateLokasi(Request $request){

  $loc1 = $request->get('loc1');
  $area = $request->get('areas');
  $op_name = Auth::user()->username;
  $request_id = [];
  $tanggal = date('Y-m-d');

  $warehouse = WarehousePelayanan::where('kode_request','=',$loc1)->where('status_aktual','=','Pengantaran')->update([
   'status_aktual' => 'pengecekan material',
   'lokasi_produksi' => $area,
   'end_pengantaran' => date('Y-m-d H:i:s'),
   'start_pengecekan' => date('Y-m-d H:i:s')
 ]);

  $update = WarehousePelayanan::where('employee_id_pengantaran','=',$op_name)->where('end_pengantaran','=',null)->update([
   'start_pengantaran' => date('Y-m-d H:i:s')
 ]);

  $emp_id = WarehousePelayanan::where('kode_request','=',$loc1)->first();

  $updatlok = WarehouseEmployeeMaster::where('employee_id','=',$emp_id->employee_id)->update([
    'start_time_status' => date('M d Y H:i:s'),
    'status' => "work",
    'status_aktual_pekerjaan' => null
  ]);

  $get_list = db::select('SELECT id FROM `warehouse_time_operator_logs` where employee_id = "'.$op_name.'" and status = "Pengantaran" and end_job is null order by employee_id DESC');

  if (count($get_list) == 1) {

    $job_pl = WarehouseTimeOperatorLog::where('request_desc','=',$loc1)->where('status','=','Pengantaran')->update([
     'end_job' => date('Y-m-d H:i:s')
   ]);
    $data = new WarehouseTimeOperatorLog([
      'employee_id' => $op_name,
      'status' => "idle",
      'start_job' => date("Y-m-d H:i:s")
    ]);
    $data->save();
  }else if (count($get_list) == 0) {
    return false;
  }else{
    $job_pl = WarehouseTimeOperatorLog::where('request_desc','=',$loc1)->where('status','=','Pengantaran')->update([
     'end_job' => date('Y-m-d H:i:s')
   ]);
    $update1 = WarehouseTimeOperatorLog::where('employee_id','=',$op_name)->where('end_job','=',null)->update([
     'start_job' => date('Y-m-d H:i:s')
   ]);

  }



  // $get_pls = db::select('SELECT id,employee_id,status FROM `warehouse_time_operator_logs` where employee_id = "'.$op_name.'" and status = "Pengantaran" and end_job is null and request_desc = "'.$loc1.'" order by employee_id DESC LIMIT 1');

  // if (count($get_pls) != 0) {
  //   $get_list = db::select('SELECT id FROM `warehouse_time_operator_logs` where employee_id = "'.$op_name.'" and status = "Pengantaran" and end_job is null order by employee_id DESC LIMIT 1');


  // }

//     $get_pl = db::select('SELECT id,employee_id,status,end_job FROM `warehouse_time_operator_logs` where `status`="Pengantaran" and employee_id = "'.$op_name.'" and end_job is null order by employee_id DESC LIMIT 1');

//     if ($get_pl[0]->status == "Pengantaran" && $get_pl[0]->end_job == null) {

//       $get_pl = db::select('SELECT id,employee_id,status FROM `warehouse_time_operator_logs` where employee_id = "'.$op_name.'" and `status`="Pengantaran" and end_job is null and request_desc = "'.$loc1.'" order by employee_id DESC LIMIT 1');

//       $job_pl = WarehouseTimeOperatorLog::where('request_desc','=',$loc1[0])->where('status','=','Pengantaran')->update([
//        'end_job' => date('Y-m-d H:i:s')
//      ]);


//       $group_pl = db::select('SELECT kode_request,GROUP_CONCAT(gmc) as gmc,status_aktual
//         FROM warehouse_pelayanans
//         WHERE tanggal = "'.$tanggal.'" AND kode_request = "'.$loc1[0].'"
//         GROUP BY kode_request,status_aktual');

//       for ($i=0; $i < count($group_pl) ; $i++) {

//         $data = new WarehouseTimeOperatorLog([
//           'employee_id' => $get_pl[0]->employee_id,
//           'status' => "idle",
//           'start_job' => date("Y-m-d H:i:s")
//         ]);
//         $data->save();
//       }
//     }

//   }else{

//    $get_pl = db::select('SELECT id,employee_id,status,end_job FROM `warehouse_time_operator_logs` where `status`="idle" and employee_id = "'.$op_name.'" and end_job is null order by employee_id DESC LIMIT 1');

//    if ($get_pl[0]->status == "idle" && $get_pl[0]->end_job == null) {

//     $get_plk = db::select('SELECT id,employee_id,status FROM `warehouse_time_operator_logs` where employee_id = "'.$op_name.'" and `status`="idle" and end_job is null order by employee_id DESC LIMIT 1');

//     $job_del = DB::table('warehouse_time_operator_logs')
//     ->where('id', '=', $get_plk[0]->id)
//     ->delete();

//     $job_pls = WarehouseTimeOperatorLog::where('request_desc','=',$loc1)->where('status','=','Pengantaran')->update([
//      'end_job' => date('Y-m-d H:i:s')
//    ]);
//     $update2 = WarehouseTimeOperatorLog::where('employee_id','=',"pi2101043")->where('end_job','=',null)->update([
//      'start_job' => date('Y-m-d H:i:s')
//    ]);

//     $group_pl = db::select('SELECT kode_request,GROUP_CONCAT(gmc) as gmc,status_aktual
//       FROM warehouse_pelayanans
//       WHERE tanggal = "'.$tanggal.'" AND kode_request = "'.$loc1.'"
//       GROUP BY kode_request,status_aktual');

//     for ($i=0; $i < count($group_pl) ; $i++) {

//       $data = new WarehouseTimeOperatorLog([
//         'employee_id' => $get_pl[0]->employee_id,
//         'status' => "idle",
//         'start_job' => date("Y-m-d H:i:s")
//       ]);
//       $data->save();
//     }
//   }
// }



  $get_pl_log4 = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanan_logs` where kode_request = "'.$loc1.'"');

  $ids = explode(",",$get_pl_log4[0]->status_aktual);

  if (count($ids) == 4) {


  }else{
    $pl_3 = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanans` where kode_request = "'.$loc1.'"');

    $get_pl_log = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanan_logs` where kode_request = "'.$loc1.'"');

    $update_pl = WarehousePelayananLog::where('kode_request','=',$pl_3[0]->kode_request)->update([
     'status_aktual' => $get_pl_log[0]->status_aktual.","."Pengantaran"
   ]);
  }

  $response = array(
   'status' => true,
   'remark' => 'logged_in',
 );
  return Response::json($response);

}

public function getLokasi( Request $request)
{
  try {
   $ven = DB::SELECT('SELECT
    area,
    area_code 
    FROM
    area_codes
    WHERE
    area = "'.$request->get('area').'"');

   if (count($ven) > 0) {
    $response = array(
     'status' => true,
     'message' => 'Success',
     'ven' => $ven
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

public function fetchDetailPengantaran(Request $request){
  try {
   $kode_request = $request->get('kode_request');
   $detail_pengantaran = db::select('SELECT DISTINCT
    id,
    kode_request,
    lot,
    gmc,
    description,
    quantity_request 
    FROM
    warehouse_pelayanans 
    WHERE
    kode_request = "'.$kode_request.'"
    ORDER BY
    id ASC');

   $response = array(
    'status' => true,
    'details' => $detail_pengantaran,
    'data' => $kode_request
  );
   return Response::json($response);
 }catch (\Exception $e) {
   $response = array(
    'status' => false,
    'message' => $e->getMessage(),
  );
   return Response::json($response);
 }
}

public function fetchDetailMaterial(Request $request){
  try {
   $kode_request = $request->get('kode_request');
   $detail_pengantaran = db::select('SELECT
    kode_request,
    lot,
    gmc,
    description,quantity_request,id,status_pengantaran,status_pel,lokasi_produksi,status_aktual 
    FROM
    warehouse_pelayanans 
    WHERE
    kode_request = "'.$request->get('kode_request').'" 
    ORDER BY
    id ASC');

   $response = array(
    'status' => true,
    'material' => $detail_pengantaran
  );
   return Response::json($response);
 }catch (\Exception $e) {
   $response = array(
    'status' => false,
    'message' => $e->getMessage(),
  );
   return Response::json($response);
 }
}

public function updatePengecekanPeng(Request $request)
{
  $stat = [];
  $stat_plus = [];

  try {
    $id_user = Auth::user()->username;
    $ids = Auth::id();

    $id = $request->get('id');
    $qty = $request->get('qty');
    $kode_request = $request->get('kode_request');

    for ($i=0; $i < count($id); $i++) {
      $warehouse = WarehousePelayanan::find($id[$i]);
      $checkmt = db::select('SELECT id,kode_request,no_hako,quantity_request FROM `warehouse_pelayanans` where status = "kurang" and kode_request = "'.$kode_request[$i].'" and no_hako = "'.$warehouse->no_hako.'"');
      $check2 = db::select('SELECT id,kode_request,no_hako,quantity_request FROM `warehouse_pelayanans` where status_pengantaran = "Proses Pengantaran" and kode_request = "'.$kode_request[$i].'" and no_hako = "'.$warehouse->no_hako.'"');

      $warehouse->status_pengantaran = "Finish";
      $warehouse->end_pengecekan = date('Y-m-d H:i:s');
      $warehouse->status_aktual = "finish";
      $warehouse->quantity_check = $qty[$i];
      $warehouse->pic_received = $id_user;
      $warehouse->save();
      $employee_groups = DB::table('warehouse_employee_masters')
      ->where('employee_id', '=', $warehouse->pic_pelayanan)          
      ->update([
        'start_time_status' => date('M d Y H:i:s'),
        'status_aktual_pekerjaan' => null,
        'status' => "idle"
      ]);

      $get_pl_log = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanan_logs` where kode_request = "'.$warehouse->kode_request.'"');
      $ids = explode(",",$get_pl_log[0]->status_aktual);



      if (count($ids) == 6) {

      }else{

        $check_status = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanans` where no_hako = "'.$warehouse->no_hako.'" and kode_request = "'.$warehouse->kode_request.'"'); 
        array_push($stat, $check_status[0]->status_aktual);

      }


    }

    $checkstatus = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanan_logs` where kode_request = "'.$kode_request[0].'"');
    $checkAll = db::select('SELECT id,kode_request,status FROM `warehouse_pelayanans` where kode_request = "'.$kode_request[0].'" and status = "plus" || kode_request = "'.$kode_request[0].'" and status = "kurang"');

    // dd("s");


    if (!empty($stat)) {
      $st = 1;
      foreach ($stat as $key) {
        if ($key != 'finish') {
          $st = 0;
        }
      }

      if (count($checkAll) <= 0) {
        if ($st == 1) {
         $update_pl = WarehousePelayananLog::where('kode_request','=',$kode_request)->update([
           'status_aktual' => $checkstatus[0]->status_aktual.","."pengecekan material".","."finish"
         ]);

         $update_end = DB::table('warehouse_pelayanan_ins')
         ->where('kode_request', '=', $kode_request)          
         ->update([
          'remark' => "close"
        ]);

         $update_completion = db::select('SELECT
          tanggal,
          kode_request,
          gmc,
          description,
          SUM( quantity_check ) AS quantity_check,
          loc,
          sloc_name 
          FROM
          `warehouse_pelayanans` 
          WHERE
          kode_request = "'.$warehouse->kode_request.'" and quantity_check != 0  
          GROUP BY
          gmc,
          description,
          tanggal,
          kode_request,loc,
          sloc_name' );


         for ($j=0; $j < count($update_completion); $j++) { 

          $create_completion = new WarehouseCompletionRequest([
           'date_request' => $update_completion[$j]->tanggal,
           'kode_request' => $update_completion[$j]->kode_request,
           'gmc' => $update_completion[$j]->gmc,
           'description' => $update_completion[$j]->description,
           'quantity_total' => $update_completion[$j]->quantity_check,
           'loc' => $update_completion[$j]->loc,
           'sloc_name' => $update_completion[$j]->sloc_name,
           'created_by' => $id_user 
         ]);
          $create_completion->save();

          $transaction_transfer = new TransactionTransfer([
            'plant' => '8190',
            'serial_number' => $update_completion[$j]->loc.$update_completion[$j]->kode_request,
            'material_number' => $update_completion[$j]->gmc,
            'issue_plant' => '8190',
            'issue_location' => 'MSTK',
            'receive_plant' => '8190',
            'receive_location' => $update_completion[$j]->loc,
            'transaction_code' => 'MB1B',
            'movement_type' => '9I3',
            'quantity' => $update_completion[$j]->quantity_check,
            'created_by' => Auth::id()

          ]);
          $transaction_transfer->save();
        } 



        $materials = WarehousePelayanan::where('kode_request', '=', $warehouse->kode_request)->get();
        for ($i=0; $i < count($materials) ; $i++) { 
          $wh_log = new WarehouseLog([
            'tanggal' => date("Y-m-d"),
            'lokasi_kirim' => $materials[$i]->area,
            'sloc_name' => $materials[$i]->sloc_name,
            'loc' => $materials[$i]->loc,
            'kode_request' => $materials[$i]->kode_request,
            'gmc' => $materials[$i]->gmc,
            'description' => $materials[$i]->description,
            'uom' => $materials[$i]->uom,
            'lot' => $materials[$i]->lot,
            'no_hako' => $materials[$i]->no_hako,
            'qty_req' => $materials[$i]->qty_req,
            'qty_kirim' => $materials[$i]->quantity_check,
            'pic_request' => $materials[$i]->pic_produksi,
            'pic_pelayanan' => $materials[$i]->pic_pelayanan,
            'pic_pengantaran' => $materials[$i]->employee_id_pengantaran,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            'created_by' => Auth::id(),
            'status' => 'sudah dicheck',
            'remark' => 'finish'
          ]);
          $wh_log->save();
        }
        $delete_rqst = db::table('warehouse_pelayanans')
        ->where('kode_request', '=', $warehouse->kode_request)
        ->delete(); 
      }
    }

  }


  $response = array(
    'status' => true,
    'pel' => $stat
  );
  return Response::json($response);
}catch (\Exception $e) {
 $response = array(
   'status' => false,
   'message' => $e->getMessage(),
 );
 return Response::json($response);
}
}

public function updatePengecekanPeng1(Request $request)
{
  $stat = [];
  $stat_plus = [];

  try {
    $id_user = Auth::user()->username;
    $ids = Auth::id();

    $id = $request->get('id');
    $qty = $request->get('qty');
    $kode_request = $request->get('kode_request');

    for ($i=0; $i < count($id); $i++) {
     $warehouse = WarehousePelayanan::find($id[$i]);
     if ($warehouse->quantity_request > $qty[$i]) {

      $create_detail = new WarehousePelayanan([
       'tanggal' => $warehouse->tanggal,
       'kode_request' => $warehouse->kode_request,
       'area' => $warehouse->area,
       'area_code' => $warehouse->area_code,
       'sloc_name' => $warehouse->sloc_name,
       'loc' => $warehouse->loc,
       'gmc' => $warehouse->gmc,
       'description' => $warehouse->description,
       'uom' => $warehouse->uom,
       'lot' => $warehouse->lot,
       'no_hako' => $warehouse->no_hako,
       'pic_produksi' => $warehouse->pic_produksi,
       'pic_pelayanan' => $warehouse->pic_pelayanan,
       'quantity_request' => $warehouse->quantity_request - $qty[$i],
       'status' => "kurang",
       'status_mt' => 1, 
       'status_aktual' => 1

     ]);
      $update_pk = WarehousePelayanan::where('kode_request','=',$kode_request[$i])->where('status_mt','=',1)->where('status_aktual','=','kurang')->update([
       'status_mt' => 1
     ]);

      $check_status = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanans` where no_hako = "'.$warehouse->no_hako.'" and kode_request = "'.$warehouse->kode_request.'"');  

      array_push($stat, $check_status[0]->status_aktual);
      $create_detail->save();

      $warehouse->status_pengantaran = "Finish";
      $warehouse->end_pengecekan = date('Y-m-d H:i:s');
      $warehouse->status_aktual = "kurang";
      $warehouse->status = "kurang";
      $warehouse->status_mt = 1;
      $warehouse->pic_received = $id_user;
      $warehouse->quantity_request = $qty[$i];
      $warehouse->quantity_check = $qty[$i];
      $warehouse->save();

      $update_st = DB::table('warehouse_pelayanan_ins')
      ->where('kode_request', '=', $kode_request)          
      ->update([
        'remark' => "not close"
      ]);

    }else if ($warehouse->quantity_request == $qty[$i]) {
      $warehouse = WarehousePelayanan::find($id[$i]);
      $checkmt = db::select('SELECT id,kode_request,no_hako,quantity_request FROM `warehouse_pelayanans` where status = "kurang" and kode_request = "'.$kode_request[$i].'" and no_hako = "'.$warehouse->no_hako.'"');
      $check2 = db::select('SELECT id,kode_request,no_hako,quantity_request FROM `warehouse_pelayanans` where status_pengantaran = "Proses Pengantaran" and kode_request = "'.$kode_request[$i].'" and no_hako = "'.$warehouse->no_hako.'"');

      if (count($checkmt) > 0 ) {
        $warehousekurang = WarehousePelayanan::find($checkmt[0]->id);
        $warehousekurang->status_pengantaran = "Finish";
        $warehousekurang->end_pengecekan = date('Y-m-d H:i:s');
        $warehousekurang->status_aktual = "finish";
        $warehousekurang->status = null;
        $warehousekurang->status_mt = 1;
        $warehousekurang->quantity_check = $warehousekurang->quantity_check+$qty[$i];
        $warehousekurang->pic_received = $id_user;
        $warehousekurang->save();

        $get_pl_log = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanan_logs` where kode_request = "'.$checkmt[0]->kode_request.'"');

        $ids = explode(",",$get_pl_log[0]->status_aktual);

        if (count($ids) == 6) {

        }else{
          $check_status = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanans` where no_hako = "'.$warehouse->no_hako.'" and kode_request = "'.$warehouse->kode_request.'"');  

          array_push($stat, $check_status[0]->status_aktual);

        }

        $delete = DB::table('warehouse_pelayanans')
        ->where('id', '=', $id[$i])
        ->delete(); 

      }else{
       $warehouse->status_pengantaran = "Finish";
       $warehouse->end_pengecekan = date('Y-m-d H:i:s');
       $warehouse->status_aktual = "finish";
       $warehouse->quantity_check = $qty[$i];
       $warehouse->pic_received = $id_user;
       $warehouse->save();
       $employee_groups = DB::table('warehouse_employee_masters')
       ->where('employee_id', '=', $warehouse->pic_pelayanan)          
       ->update([
        'start_time_status' => date('M d Y H:i:s'),
        'status_aktual_pekerjaan' => null,
        'status' => "idle"
      ]);

       $get_pl_log = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanan_logs` where kode_request = "'.$warehouse->kode_request.'"');
       $ids = explode(",",$get_pl_log[0]->status_aktual);

       

       if (count($ids) == 6) {

       }else{

        $check_status = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanans` where no_hako = "'.$warehouse->no_hako.'" and kode_request = "'.$warehouse->kode_request.'"'); 
        array_push($stat, $check_status[0]->status_aktual);


      }
    }

  }
  else{

    $create_plus = new WarehousePelayanan([
     'tanggal' => $warehouse->tanggal,
     'kode_request' => $warehouse->kode_request,
     'area' => $warehouse->area,
     'area_code' => $warehouse->area_code,
     'sloc_name' => $warehouse->sloc_name,
     'loc' => $warehouse->loc,
     'gmc' => $warehouse->gmc,
     'description' => $warehouse->description,
     'uom' => $warehouse->uom,
     'lot' => $warehouse->lot,
     'no_hako' => $warehouse->no_hako,
     'pic_produksi' => $warehouse->pic_produksi,
     'pic_pelayanan' => $warehouse->pic_pelayanan,
     'quantity_request' => $qty[$i] - $warehouse->quantity_request,
     'quantity_check' => $qty[$i] - $warehouse->quantity_request,
     'status' => "plus",
     'status_mt' => 1, 
     'status_aktual' => 2

   ]);
    $update_pk1 = WarehousePelayanan::where('kode_request','=',$kode_request[$i])->where('status_mt','=',1)->where('status_aktual','=','plus')->update([
     'status_mt' => 1
   ]);

    $check_status = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanans` where no_hako = "'.$warehouse->no_hako.'" and kode_request = "'.$warehouse->kode_request.'"');  

    array_push($stat, $check_status[0]->status_aktual);

    $create_plus->save();

    $warehouse->status_pengantaran = "Finish";
    $warehouse->end_pengecekan = date('Y-m-d H:i:s');
    $warehouse->status_aktual = "plus";
    $warehouse->status = "plus";
    $warehouse->status_mt = 1;
    $warehouse->pic_received = $id_user;
    $warehouse->quantity_request = $qty[$i];
    $warehouse->quantity_check = $qty[$i];
    $warehouse->save();

    $update_st = DB::table('warehouse_pelayanan_ins')
    ->where('kode_request', '=', $kode_request)          
    ->update([
      'remark' => "not close"
    ]);

  }

}

$checkstatus = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanan_logs` where kode_request = "'.$kode_request[0].'"');
$checkAll = db::select('SELECT id,kode_request,status FROM `warehouse_pelayanans` where kode_request = "'.$kode_request[0].'" and status = "plus" || kode_request = "'.$kode_request[0].'" and status = "kurang"');


if (!empty($stat)) {
  $st = 1;
  foreach ($stat as $key) {
    if ($key != 'finish') {
      $st = 0;
    }
  }

  if (count($checkAll) <= 0) {
    if ($st == 1) {
     $update_pl = WarehousePelayananLog::where('kode_request','=',$kode_request)->update([
       'status_aktual' => $checkstatus[0]->status_aktual.","."pengecekan material".","."finish"
     ]);

     $update_end = DB::table('warehouse_pelayanan_ins')
     ->where('kode_request', '=', $kode_request)          
     ->update([
      'remark' => "close"
    ]);



     $update_completion = db::select('SELECT
      tanggal,
      kode_request,
      gmc,
      description,
      SUM( quantity_check ) AS quantity_check,
      loc,
      sloc_name 
      FROM
      `warehouse_pelayanans` 
      WHERE
      kode_request = "'.$warehouse->kode_request.'" 
      GROUP BY
      gmc,
      description,
      tanggal,
      kode_request,loc,
      sloc_name');

     for ($j=0; $j < count($update_completion); $j++) { 

      $create_completion = new WarehouseCompletionRequest([
       'date_request' => $update_completion[$j]->tanggal,
       'kode_request' => $update_completion[$j]->kode_request,
       'gmc' => $update_completion[$j]->gmc,
       'description' => $update_completion[$j]->description,
       'quantity_total' => $update_completion[$j]->quantity_check,
       'loc' => $update_completion[$j]->loc,
       'sloc_name' => $update_completion[$j]->sloc_name,
       'created_by' => $id_user 
     ]);
      $create_completion->save();  
      $transaction_transfer = new TransactionTransfer([
        'plant' => '8190',
        'serial_number' => $update_completion[$j]->loc.$update_completion[$j]->kode_request,
        'material_number' => $update_completion[$j]->gmc,
        'issue_plant' => '8190',
        'issue_location' => 'MSTK',
        'receive_plant' => '8190',
        'receive_location' => $update_completion[$j]->loc,
        'transaction_code' => 'MB1B',
        'movement_type' => '9I3',
        'quantity' => $update_completion[$j]->quantity_check,
        'reference_file' => 'directly_executed_on_sap',
        'created_by' => Auth::id()

      ]);
      $transaction_transfer->save();

    }


    $materials = WarehousePelayanan::where('kode_request', '=', $warehouse->kode_request)->get();
    for ($i=0; $i < count($materials) ; $i++) { 
      $wh_log = new WarehouseLog([
        'tanggal' => date("Y-m-d"),
        'lokasi_kirim' => $materials[$i]->area,
        'sloc_name' => $materials[$i]->sloc_name,
        'loc' => $materials[$i]->loc,
        'kode_request' => $materials[$i]->kode_request,
        'gmc' => $materials[$i]->gmc,
        'description' => $materials[$i]->description,
        'uom' => $materials[$i]->uom,
        'lot' => $materials[$i]->lot,
        'no_hako' => $materials[$i]->no_hako,
        'qty_req' => $materials[$i]->qty_req,
        'qty_kirim' => $materials[$i]->quantity_check,
        'pic_request' => $materials[$i]->pic_produksi,
        'pic_pelayanan' => $materials[$i]->pic_pelayanan,
        'pic_pengantaran' => $materials[$i]->employee_id_pengantaran,
        'created_at' => date("Y-m-d H:i:s"),
        'updated_at' => date("Y-m-d H:i:s"),
        'created_by' => Auth::id(),
        'remark' => 'finish'
      ]);
      $wh_log->save();
    }
    $delete_rqst = db::table('warehouse_pelayanans')
    ->where('kode_request', '=', $warehouse->kode_request)
    ->delete(); 

  }
}

}


$response = array(
  'status' => true,
  'pel' => $stat
);
return Response::json($response);
}catch (\Exception $e) {
 $response = array(
   'status' => false,
   'message' => $e->getMessage(),
 );
 return Response::json($response);
}
}


public function index_monitoring()
{
  $title = "Monitoring Warehouse Internal";
  $title_jp = "??";

  return view('warehouse_new.display_monitoring', array(
    'title' => $title,
    'title_jp' => $title_jp
  ))->with('page', 'Display Monitoring');
}


public function fetchStatus(Request $request){
  try {
   if ($request->get('tanggal')) {
    $tanggal = $request->get('tanggal');
  } else {
    $tanggal = date('Y-m-d');
  }
  $detail_nama = db::select('SELECT
    es.employee_id AS employee_id,
    es.status AS status1,
    es.NAME AS nama,
    eg.shiftdaily_code AS shift,
    es.status_aktual_pekerjaan,
    eg.attend_code AS kategori,
    IF
    (
      eg.shiftdaily_code = "Shift_1" || eg.shiftdaily_code = "Shift_1_Genba" || eg.shiftdaily_code = "Shift_1_Jumat",
      IFNULL(
        es.`start_time_status`,
        DATE_FORMAT( NOW(), "%b %d %Y 07:00:00" )),
      IFNULL(
        es.`start_time_status`,
        DATE_FORMAT( NOW(), "%b %d %Y 16:00:00" ))) start_time_status 
    FROM
    sunfish_shift_syncs AS eg
    LEFT JOIN warehouse_employee_masters AS es ON es.employee_id = eg.employee_id 
    WHERE
    eg.shift_date = "'.$tanggal.'" 
    AND es.employee_id = eg.employee_id order by shift, shift ASC');

  $cek_kategori = db::select('SELECT
    absence_code 
    FROM
    `absence_categories`');

  $response = array(
    'status' => true,
    'namas' => $detail_nama,
    'cek_kategori' => $cek_kategori
  );
  return Response::json($response);
}catch (\Exception $e) {
 $response = array(
   'status' => false,
   'message' => $e->getMessage(),
 );
 return Response::json($response);
}
}

public function fetch_import_display(Request $request)
{
  try {
   $id_user = Auth::id();
   $op_name = Auth::user()->name;

   $fetch = db::select('SELECT
     no_invoice,
     country,
     vendor,
     id,gmc,
     tanggal_kedatangan,
     tanggal_kedatangan_aktual, no_case 
     FROM
     warehouse_packinglists 
     WHERE
     status_exim = "sudah diturunkan" and status_job = "not done"
     ORDER BY
     no_invoice ASC');

   $response = array(
     'status' => true,
     'fetch' => $fetch
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

public function updateLokasiAwalPengantaran(Request $request){

  $loc1 = $request->get('loc1');
  $area = $request->get('areas');

  $warehouse = WarehousePelayanan::where('kode_request','=',$loc1)->update([
    'status_aktual' => 'progress',
    'status_pengantaran' => 'Finih Pengantaran',
    'status_material' => 'material di lokasi',
    'lokasi_produksi' => $area,
    'end_pengantaran' => date('Y-m-d H:i:s'),
    'start_pengecekan' => date('Y-m-d H:i:s')

  ]);

  $emp_id = WarehousePelayanan::where('kode_request','=',$loc1)->first();
  $updatlok = WarehouseEmployeeMaster::where('employee_id','=',$emp_id->employee_id)->update([
    'start_time_status' => date('M d Y H:i:s'),
    'status' => "work",
    'status_aktual_pekerjaan' => "pengecekan material"
  ]);

  $response = array(
    'status' => true,
    'remark' => 'logged_in',
  );
  return Response::json($response);
}

public function fetch_internal_wr(Request $request){

  if ($request->get('tanggal')) {
    $tanggals = $request->get('tanggal');
  } else {
    $tanggals = date('Y-m-d');
  }

  $data_belum = db::select('SELECT
    no_case,
    tanggal_kedatangan_aktual,
    status_job
    FROM
    warehouse_packinglists
    WHERE
    status_aktual = "wainting" 
    AND tanggal_kedatangan_aktual = "'.$tanggals.'"
    GROUP BY
    no_case,tanggal_kedatangan_aktual,status_job');

  $data_progress = db::select('
    SELECT
    no_case AS progress_import ,tanggal_kedatangan_aktual,status_job
    FROM
    warehouse_packinglists 
    WHERE
    status_aktual = "pengecekan" and tanggal_kedatangan_aktual = "'.$tanggals.'" 
    GROUP BY
    no_case,tanggal_kedatangan_aktual,status_job');
  $data_penataan = db::select('
    SELECT
    no_case AS progress_import ,tanggal_kedatangan_aktual,status_job
    FROM
    warehouse_packinglists 
    WHERE
    status_aktual = "penataan" and tanggal_kedatangan_aktual = "'.$tanggals.'" 
    GROUP BY
    no_case,tanggal_kedatangan_aktual,status_job');
  $data_finish = db::select('
    SELECT
    no_case AS finish_import,tanggal_kedatangan_aktual,status_job,status_all
    FROM
    warehouse_packinglists 
    WHERE
    status_all = "finish" and tanggal_kedatangan_aktual = "'.$tanggals.'" GROUP BY
    no_case,tanggal_kedatangan_aktual,status_job,status_all');
  $tanggal = db::select('SELECT DISTINCT
    tanggal_kedatangan_aktual 
    FROM
    warehouse_packinglists 
    WHERE
    tanggal_kedatangan_aktual = "'.$tanggals.'" 
    GROUP BY
    tanggal_kedatangan_aktual');
  $data_belum_pelayanan = db::select('
    SELECT
    kode_request AS belum_pel,
    tanggal 
    FROM
    warehouse_pelayanans 
    WHERE status_aktual = "proses0" 
    GROUP BY
    kode_request, tanggal');

  $data_progress_pelayanan = db::select('
    SELECT DISTINCT
    kode_request AS progress_pel,
    tanggal 
    FROM
    warehouse_pelayanans 
    WHERE
    status_aktual = "check"
    GROUP BY 
    kode_request,tanggal');

  $data_pengantaran = db::select('
    SELECT DISTINCT
    kode_request AS progress_pel,
    tanggal 
    FROM
    warehouse_pelayanans 
    WHERE
    status_aktual = "proses1"
    GROUP BY 
    kode_request,tanggal');

  $proccess_pengantaran = db::select('
    SELECT DISTINCT
    kode_request AS progress_pel,
    tanggal 
    FROM
    warehouse_pelayanans
    WHERE
    status_aktual = "Pengantaran"
    GROUP BY 
    kode_request,tanggal');

  $data_finish_pelayanan = db::select('
    SELECT
    kode_request AS finish_pel
    FROM
    warehouse_logs
    WHERE
    DATE_FORMAT(created_at,"%Y-%m-%d") = "'.$tanggals.'" and remark = "finish"
    GROUP BY
    kode_request');

  $data_checks_pelayanan = db::select('
    SELECT
    kode_request AS finish_pel
    FROM
    warehouse_pelayanans 
    WHERE
    status_aktual = "pengecekan material"
    GROUP BY
    kode_request');

  $response = array(
    'status' => true,
    'data_belum' => $data_belum,
    'data_progress' => $data_progress,
    'data_penataan' => $data_penataan,
    'data_finish' => $data_finish,
    'tanggal' => $tanggal,
    'data_belum_pel' => $data_belum_pelayanan,
    'data_progress_pel' => $data_progress_pelayanan,
    'data_finish_pel' => $data_finish_pelayanan,
    'data_checks_pelayanan' => $data_checks_pelayanan,
    'data_pengantaran' => $data_pengantaran,
    'proccess_pengantaran' => $proccess_pengantaran
  );
  return Response::json($response);
}

public function fetchInternalPenerimaan(Request $request){
  if ($request->get('tanggal')) {
    $tanggal = $request->get('tanggal');
  } else {
    $tanggal = date('Y-m-d');
  }
  if ($request->get('kategori') == "req_masuk_import") {
    $wr = DB::select('SELECT
      vendor,
      gmc,
      description,
      quantity,
      pic_job,
      status_aktual, no_case,status_job, count(no_case) as total
      FROM
      warehouse_packinglists 
      WHERE
      status_job = "not done" 
      AND status_exim = "sudah diturunkan" and tanggal_kedatangan_aktual = "'.$tanggal.'"
      GROUP BY vendor,
      gmc,
      description,
      quantity,
      pic_job,
      status_aktual, no_case,status_job');
  }else if ($request->get('kategori') == "req_progress_import") {
    $wr = DB::select('SELECT
      vendor,
      gmc,
      description,
      quantity,
      pic_job,
      status_aktual,no_case,status_job, count(no_case) as total
      FROM
      warehouse_packinglists 
      WHERE
      status_job = "progress" 
      AND status_exim = "sudah diturunkan" and tanggal_kedatangan_aktual = "'.$tanggal.'"
      GROUP BY
      vendor,
      gmc,
      description,
      quantity,
      pic_job,
      status_aktual,no_case,status_job');
  }else{
    $wr = DB::select('SELECT
      vendor,
      gmc,
      description,
      quantity,
      pic_job,
      status_aktual,no_case,status_job, count(no_case) as total
      FROM
      warehouse_packinglists 
      WHERE
      status_job = "finish" 
      AND status_exim = "sudah diturunkan" and tanggal_kedatangan_aktual = "'.$tanggal.'"
      GROUP BY
      status_aktual,no_case,status_job, count(no_case) as total');
  }

  $response = array(
    'status' => true,
    'detail_bel' => $wr
  );
  return Response::json($response);
}




public function fetchHistoryFinish(Request $request){
  $tanggal = date('Y-m-d');
  $finish = DB::select('SELECT
    no_case,
    "-" AS sa1,
    gmc,
    description,
    quantity,
    "-" AS sa4,
    status_all,
    FLOOR( TIMESTAMPDIFF( SECOND, start_check, end_check )/ 60 ) AS `minute`,
    TIMESTAMPDIFF( SECOND, start_check, end_check ) % 60 AS `second`,
    "-" AS time3,
    "-" AS time4 
    FROM
    warehouse_packinglists 
    WHERE
    status_job = "finish" AND tanggal_kedatangan_aktual = "'.$tanggal.'" UNION ALL
    SELECT
    "-" AS sa2,
    kode_request,
    gmc,
    description,
    "-" AS sa3,
    quantity_request,
    status_pel,
    "-" AS time1,
    "-" AS time2,
    FLOOR( TIMESTAMPDIFF( SECOND, start_pel, end_pel )/ 60 ) AS `minute`,
    TIMESTAMPDIFF( SECOND, start_pel, end_pel ) % 60 AS `second` 
    FROM
    warehouse_pelayanans 
    WHERE
    status_pel = "Finish" AND tanggal= "'.$tanggal.'"');

  $response = array(
    'status' => true,
    'finish_int' => $finih
  );
  return Response::json($response);
}

public function detail_historsy(Request $request){
  $tanggal = date('Y-m-d');
  $detail = DB::select('SELECT DISTINCT
    pic_job,
    tanggal,
    no_case,
    "-" AS depa,
    "-" AS kode,
    status_job 
    FROM
    warehouse_packinglists 
    WHERE
    status_job = "progress" AND tanggal_kedatangan_aktual = "'.$tanggal.'" UNION
    SELECT
    pic_pelayanan,
    tanggal,
    "-" AS no_case1,
    area,
    kode_request,
    status_pel 
    FROM
    warehouse_pelayanans 
    WHERE
    status_all = "progress" AND tanggal = "'.$tanggal.'" ');

  $response = array(
    'status' => true,
    'detail_se' => $detail
  );
  return Response::json($response);
}

public function indexShiffOperator(){
  $employees = db::select("SELECT * FROM employee_syncs
    WHERE end_date is null
    AND employee_id NOT IN (
      SELECT DISTINCT employee_id FROM warehouse_employee_masters)");
  return view('warehouse_new.warehouse_operator', array(
    'emplo' => $employees
  ))->with('page', 'operator')->with('head', 'Warehouse Operator');
}

public function fetch_operator(Request $request){

  $emp = WarehouseEmployeeMaster::select('employee_id', 'name','status')->get();

  $response = array(
    'status' => true,
    'detail_emp' => $emp
  );
  return Response::json($response);
}

public function updateOperatorWarehouse(Request $request){
  try{
    $employee_groups = DB::table('warehouse_employee_masters')
    ->where('employee_id', '=', $request->get('employee_id'))           
    ->update([
      'employee_id' => $request->get('employee_id'),
      'updated_at' => date('Y-m-d H:i:s'),
      'status' => $request->get('group'),
      'start_time_status' => null    
            // if($request->get('group') === "off"){
            // }else{
            // 'status' => $request->get('group')                
            // }
    ]);

    $response = array(
      'status' => true,
      'message' => 'update successful',   
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

public function deleteOperatorWarehouse(Request $request){
  try{
    $delete = DB::table('warehouse_employee_masters')
    ->where('employee_id', '=', $request->get('employee_id'))
    ->delete();
    $response = array(
      'status' => true,
      'message' => 'update successful',   
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

public function insertOperatorWarehouse(Request $request){
  try{

    $ivms = WarehouseEmployeeMaster::create([
      'employee_id' => $request->get('employee_id'),
      'status' => $request->get('group'),
      'name' => $request->get('addname')
    ]);


    $response = array(
      'status' => true,
      'message' => 'update successful',   
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

public function fetchDetaiGmc(Request $request){
  try {

    if ($request->get('tanggal')) {
      $tanggals = $request->get('tanggals');
    } else {
      $tanggals = date('Y-m-d');
    }
    $detail_gmc = db::select('SELECT 
      no_case,
      vendor,
      gmc,
      description,
      quantity,
      status_aktual,
      status_job 
      FROM
      warehouse_packinglists 
      WHERE
      no_case = "'.$request->get('no_case').'"');

    $response = array(
      'status' => true,
      'gmcs' => $detail_gmc
    );
    return Response::json($response);
  }catch (\Exception $e) {
   $response = array(
     'status' => false,
     'message' => $e->getMessage(),
   );
   return Response::json($response);
 }
}

public function StatusOperatorWarehouse(Request $request){
  try{

    $affected2 = DB::table('warehouse_employee_masters')->where('employee_id', '=', $request->get('emp'))->update(array('shift' => $request->get('shift'), 'status' => $request->get('status')));
    $response = array(
      'status' => true,
      'text' => $affected2
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



public function requestProd(){
  $title = "Request Produksi"; 

  $scanQrCode = WarehouseMaterialMaster::select('barcode', 'gmc_material','description','lot','keterangan','sloc_name','no_hako','loc')->get();

  $materialname = WarehouseMaterialName::select('warehouse_material_names.*')->distinct()
  ->get();


  return view('warehouse_new.request_produksi', array(
    'title' => $title,
    'scanQrCode' => $scanQrCode,
    'test' => $materialname

  ))->with('head', 'Request')->with('page', 'Request Produksi');
}

public function ScanQrMaterial(Request $request)
{
 try {
  // dd($request->get('lokasi'));
  $emp = MaterialHakoKanban::where('barcode', '=', $request->get('tag'))->where('sloc_name', '=', $request->get('lokasi'))->first();       

  if (count($emp) > 0) {
   $response = array(
    'status' => true,
    'datamaterial' => $emp
  );
   return Response::json($response);
 } else { 
   $response = array(
    'status' => false,
    'datamaterial' => null
  );
   return Response::json($response);
 }


} catch (QueryException $e) {
  $response = array(
   'status' => false,
   'message' => $e->getMessage()
 );
  return Response::json($response);
}
}

public function ConfirmRequestPrd(Request $request)
{
  try {
    $categorys = $request->get('categorys');
    $reason_urgt = $request->get('reason_urgt');
    $lok_materials = $request->get('lok_materials');
    $op_prd = Auth::user()->username;
    $code_material = $request->get('code_material');
    $quantity_check = $request->get('quantity_check');
    $loc_mtrl = $request->get('loc_mtrl');
    $no_hako = $request->get('no_hako');
    $uoms = $request->get('uoms');
    $tes = [];
    $count = 1;

    $code_generator = CodeGenerator::where('note', '=', 'pelayanan')->first();
    $serial_number = sprintf("%'.0" . $code_generator->length ."d", $code_generator->index+1);
    $code_generator->index = $code_generator->index+1;
    $code_generator->save();

    $get_user = EmployeeSync::where('employee_id', '=', Auth::user()->username)
    ->get();
    $get_sec = WarehouseRequestMt::where('section', '=', $get_user[0]->section)
    ->get();

 //    if ($get_sec[0]->count_request == null) {
 //       $count_request = WarehouseRequestMt::where('section', '=', $get_user[0]->section)
 //       ->update([
 //       'department' => $get_user[0]->department,
 //        'section' => $get_user[0]->section,
 //        'count_request' => 1,
 //        'created_by' => Auth::user()->username
 //      ]);
 //    }else{
 //    $count = 0;
 //    $response = array(
 //   'status' => false
 // );
 //  return Response::json($response);

    // }
    if ($count = 1) {
      if ($categorys == "Normal") {
       for ($i=0; $i < count($code_material); $i++) { 
        array_push($tes, $code_material[$i]);


        $loc = WarehouseMaterialName::where('sloc_name', '=', $lok_materials)->first();
        $check_Qrcode = MaterialHakoKanban::where('barcode', '=', $tes[$i])->first();

        $create = new WarehousePelayanan([
          'tanggal' => date('Y-m-d H:i:s'),
          'kode_request' => $serial_number,
          'sloc_name' => $lok_materials,
          'loc' => $loc_mtrl[$i],
          'no_hako' => $no_hako[$i],
          'uom' => $uoms[$i],
          'gmc' => $check_Qrcode->gmc_material,
          'description' => $check_Qrcode->description, 
          'lot' => $check_Qrcode->lot, 
          'quantity_request' => $quantity_check[$i],
          'qty_req' => $quantity_check[$i],
          'status_aktual' => "proses0",
          'pic_produksi' => $op_prd,
          'area' =>  $loc->location,
          'status_mt' => 1,
          'area_code' => $loc->code_location
        ]);
        $create->save();
        $create_mt_in = new WarehousePelayananIn([
          'tanggal' => date('Y-m-d'),
          'kode_request' => $serial_number,
          'gmc' => $check_Qrcode->gmc_material,
          'no_hako' => $no_hako[$i],
          'quantity_request' => $quantity_check[$i],
          'remark' => "open",
          'created_by' => Auth::user()->username
        ]);
        $create_mt_in->save();
      }

      $pl_1 = db::select('SELECT id,kode_request FROM `warehouse_pelayanans` where kode_request = "'.$serial_number.'" LIMIT 1');
      $group_gmc = db::select('SELECT kode_request,GROUP_CONCAT(gmc) as gmc,status_aktual
        FROM warehouse_pelayanans
        WHERE kode_request = "'.$pl_1[0]->kode_request.'"
        GROUP BY kode_request,status_aktual');


      for ($i=0; $i < 1 ; $i++) {

        $data1 = new WarehousePelayananLog([
          'kode_request' => $group_gmc[0]->kode_request,
        // 'gmc' => $group_gmc[0]->gmc,
          'status_aktual' => null,
          'created_by' => $op_prd
        ]);
        $data1->save();
      }

    }else{


      for ($i=0; $i < count($code_material); $i++) { 
        array_push($tes, $code_material[$i]);

        $loc = WarehouseMaterialName::where('sloc_name', '=', $lok_materials)->first();

        $check_Qrcode = WarehouseMaterialMaster::where('barcode', '=', $tes[$i])->first();

        $create = new WarehousePelayanan([
          'tanggal' => date('Y-m-d H:i:s'),
          'kode_request' => $serial_number,
          'sloc_name' => $lok_materials,
          'loc' => $loc_mtrl[$i],
          'no_hako' => $no_hako[$i],
          'uom' => $uoms[$i],
          'gmc' => $check_Qrcode->gmc_material,
          'description' => $check_Qrcode->description, 
          'lot' => $check_Qrcode->lot, 
          'quantity_request' => $quantity_check[$i],
          'qty_req' => $quantity_check[$i],
          'status_aktual' => "proses0",
          'pic_produksi' => $op_prd,
          'area' =>  $loc->location,
          'status_mt' => 1,
          'area_code' => $loc->code_location,
          'reason_urgent_in' => $reason_urgt
        ]);
        $create->save();
        $create_mt_in = new WarehousePelayananIn([
          'tanggal' => date('Y-m-d'),
          'kode_request' => $serial_number,
          'gmc' => $check_Qrcode->gmc_material,
          'no_hako' => $no_hako[$i],
          'quantity_request' => $quantity_check[$i],
          'remark' => "open",
          'created_by' => Auth::user()->username
        ]);
        $create_mt_in->save();
      }

      $pl_1 = db::select('SELECT id,kode_request FROM `warehouse_pelayanans` where kode_request = "'.$serial_number.'" LIMIT 1');
      $group_gmc = db::select('SELECT kode_request,GROUP_CONCAT(gmc) as gmc,status_aktual
        FROM warehouse_pelayanans
        WHERE kode_request = "'.$pl_1[0]->kode_request.'"
        GROUP BY kode_request,status_aktual');


      for ($i=0; $i < 1 ; $i++) {

        $data1 = new WarehousePelayananLog([
          'kode_request' => $group_gmc[0]->kode_request,
        // 'gmc' => $group_gmc[0]->gmc,
          'status_aktual' => null,
          'created_by' => $op_prd
        ]);
        $data1->save();




      // array_push($mail_to_wh, 'dwi.misnanto@music.yamaha.com','rudianto@music.yamaha.com','nurul.hidayat@music.yamaha.com');
             // array_push($mail_to_wh, 'nasiqul.ibat@music.yamaha.com');

        $rqst_kanban = WarehousePelayanan::select('warehouse_pelayanans.kode_request','warehouse_pelayanans.gmc','warehouse_pelayanans.description','warehouse_pelayanans.lot','warehouse_pelayanans.uom','warehouse_pelayanans.quantity_request','warehouse_pelayanans.created_at','warehouse_pelayanans.no_hako','warehouse_pelayanans.sloc_name','employee_syncs.name','employee_syncs.department','employee_syncs.section')
        ->join('employee_syncs', 'employee_syncs.employee_id', '=', 'warehouse_pelayanans.pic_produksi')
        ->where('warehouse_pelayanans.kode_request', '=', $serial_number)
        ->get();

        $information2 = WarehousePelayanan::select('warehouse_pelayanans.reason_urgent_in','warehouse_pelayanans.kode_request','warehouse_pelayanans.created_at','employee_syncs.name','employee_syncs.department','employee_syncs.section')
        ->join('employee_syncs', 'employee_syncs.employee_id', '=', 'warehouse_pelayanans.pic_produksi')
        ->where('warehouse_pelayanans.kode_request', '=', $serial_number)
        ->distinct()
        ->get();

        $data = [
          'rqst_kanban' => $rqst_kanban,
          'information' => $information2,
          'kode' => $serial_number,
          'status' => "Approval leaderWH" 
        ];

        $mail_to_wh = [];
      // array_push($mail_to_wh, 'mokhamad.khamdan.khabibi@music.yamaha.com');

        array_push($mail_to_wh, 'dwi.misnanto@music.yamaha.com','rudianto@music.yamaha.com','nurul.hidayat@music.yamaha.com');
        Mail::to($mail_to_wh)->bcc(['lukman.hakim.saputra@music.yamaha.com'])->send(new SendEmail($data, 'request_kanban_mt_urgent'));

        $get_email = db::select('select email from send_emails where remark =
          (select distinct section from warehouse_pelayanans 
          left join employee_syncs on warehouse_pelayanans.pic_produksi = employee_syncs.employee_id
          where kode_request = "'.$serial_number.'")');

        $mail_to_wh1 = [];
        array_push($mail_to_wh1, $get_email[0]->email);

      // array_push($mail_to_wh1,'mokhamad.khamdan.khabibi@music.yamaha.com');
        $data1 = [
          'rqst_kanban' => $rqst_kanban,
          'information' => $information2,
          'kode' => $serial_number,
          'status' => "Detail Kirim Users" 
        ];
        Mail::to($mail_to_wh1)->bcc(['lukman.hakim.saputra@music.yamaha.com'])->send(new SendEmail($data1, 'request_kanban_mt_urgent'));
      }
    }


    $response = array(
     'status' => true
   );
    return Response::json($response);
  }
}
catch (QueryException $e) {
  $response = array(
   'status' => false,
   'message' => $e->getMessage()
 );
  return Response::json($response);
}
}

public function approvalMtUrgent($kode_request, $user, $status_app)
{
  $title = 'Approval Request Kanban Urgent';
  $title_jp = '??';

  if ($status_app == 'approve') {
    $message2 = 'Successfully Approved';
    $stat = true;
    $stat2 = "approve";
    $nama = "";

    $asset = WarehousePelayanan::where('warehouse_pelayanans.kode_request', '=', $kode_request)->first();
  } else if($status_app == 'reject') {
    $message2 = 'Reject & Reason';
    $stat = false;
    $stat2 = "reject";

    $asset = WarehousePelayanan::where('warehouse_pelayanans.kode_request', '=', $kode_request)->first();

    // if ($asset->status == 'created') {
    //   $nama = "PI0905001/Ismail Husen/".date('Y-m-d H:i:s');
    // } else if ($asset->status == 'filled') {
    //   $nama = $asset->manager_app."/".date('Y-m-d H:i:s');
    // } else if ($asset->status == 'approved_manager'){
    //   $nama = $asset->manager_acc."/".date('Y-m-d H:i:s');
    // }
  }

  $approval = WarehousePelayanan::where('warehouse_pelayanans.kode_request','=',$kode_request)->first();

  if ($user == 'leaderWH') {
    $message = 'Approval Request Kanban Urgent';
    if ($stat) {
      if ($approval->status_approve == null) {
       $employee_id = Auth::user()->username;
       $emp = EmployeeSync::where('employee_id',$employee_id)->first();
       $mt_urgt = WarehousePelayanan::where('kode_request', '=', $kode_request)
       ->update([
        'leader_app' => date('Y-m-d H:i:s'),
        'status_approve' => 'approved',
        'remark' => 'URGENT',
        'pic_approve' => $employee_id

      ]);

       $get_kode_request = WarehousePelayanan::where('warehouse_pelayanans.kode_request', '=', $kode_request)->first();
       $get_kode = WarehousePelayanan::where('kode_request','=',$kode_request)->get();

       $rqst_kanban = WarehousePelayanan::select('warehouse_pelayanans.kode_request','warehouse_pelayanans.gmc','warehouse_pelayanans.description','warehouse_pelayanans.lot','warehouse_pelayanans.uom','warehouse_pelayanans.quantity_request','warehouse_pelayanans.created_at','warehouse_pelayanans.no_hako','warehouse_pelayanans.sloc_name','employee_syncs.name','employee_syncs.department','employee_syncs.section')
       ->join('employee_syncs', 'employee_syncs.employee_id', '=', 'warehouse_pelayanans.pic_produksi')
       ->where('warehouse_pelayanans.kode_request', '=', $get_kode_request->kode_request)
       ->get();

       $information3 = WarehousePelayanan::select('warehouse_pelayanans.reason_urgent_in','warehouse_pelayanans.pic_produksi','warehouse_pelayanans.kode_request','warehouse_pelayanans.created_at','employee_syncs.name','employee_syncs.department','employee_syncs.section')
       ->join('employee_syncs', 'employee_syncs.employee_id', '=', 'warehouse_pelayanans.pic_produksi')
       ->where('warehouse_pelayanans.kode_request', '=', $get_kode_request->kode_request)
       ->distinct()
       ->get();

       $get_email = db::select('select email from send_emails where remark =
        (select distinct section from warehouse_pelayanans 
        left join employee_syncs on warehouse_pelayanans.pic_produksi = employee_syncs.employee_id
        where kode_request = "'.$kode_request.'")');

       $mail_to_wh = [];
       array_push($mail_to_wh, $get_email[0]->email);

       // array_push($mail_to_wh, 'nasiqul.ibat@music.yamaha.com');

       $data = [
        'rqst_kanban' => $rqst_kanban,
        'information' => $information3,
        'kode' => $kode_request,
        'status' => 'APPROVAL'
      ];

      // $mailto = db::select('SELECT email FROM send_emails where remark = "Accounting Department"');

      Mail::to($mail_to_wh)->bcc(['lukman.hakim.saputra@music.yamaha.com'])->send(new SendEmail($data, 'request_kanban_mt_urgent'));

      return view('warehouse_new.report.approval_mt_urgent', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'message' => $message,
        'message2' => $message2,
        'kode_request' => $get_kode_request,
        'kode' => $kode_request,
        'status' => $stat,
        'status2' => $stat2
      ))->with('page', 'approval_mt_urgent'); 
    }else{
      $get_kode_request1 = WarehousePelayanan::where('warehouse_pelayanans.kode_request', '=', $kode_request)->first();

      return view('warehouse_new.report.approval_mt_urgent')->with('title','Approval Request Kanban Material Urgent')->with('message','Approval Request Kanban Material Urgent')->with('no','Kode Request : URGENT-'.$get_kode_request1->kode_request.' pernah disetujui.')->with('page','approval_mt_urgent')->with('status','ss')->with('status2','finish')->with('message2','')->with('kode',$get_kode_request1->kode_request)->with('kode_request','');
    }
  }else{
    $get_kode_request = WarehousePelayanan::where('warehouse_pelayanans.kode_request', '=', $kode_request)->first();
    if ($get_kode_request->status_approve == null || $get_kode_request->status_approve == "approved") {
      return view('warehouse_new.report.approval_mt_urgent', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'message' => $message,
        'message2' => $message2,
        'kode_request' => $get_kode_request,
        'kode' => $kode_request,
        'status' => $stat,
        'status2' => $stat2
      ))->with('page', 'approval_mt_urgent'); 

    }else{
      return view('warehouse_new.report.reject')->with('head','Approval Request Kanban Material Urgent')->with('message','Kode Request : '.$get_kode_request->kode_request.' pernah ditolak.')->with('page','Approval Request Kanban Material Urgent');
    }


  }

}


}

public function fetchRequestProduksi(Request $request)
{
  try {
   $id_user = Auth::id();
   $op_name = Auth::user()->username;
   $tanggal = date('Y-m-d');
   $time = date('Y-m-d H:i:s');

   $get_op = EmployeeSync::where('employee_syncs.employee_id', '=', $op_name)->first();

   $job = DB::select('SELECT
    ss.tanggal,
    es.name,
    es.department,
    ss.kode_request,
    ss.remark,
    pl.status_aktual as pl_status,
    count(ss.kode_request) as total
    FROM
    warehouse_pelayanan_ins AS ss
    LEFT JOIN employee_syncs AS es ON ss.created_by = es.employee_id
    LEFT JOIN warehouse_pelayanan_logs AS pl ON ss.kode_request = pl.kode_request
    WHERE
    (select distinct section from employee_syncs where employee_id = es.employee_id) = "'.$get_op->section.'" and remark = "open"
    GROUP BY
    ss.kode_request,
    es.name,
    es.department,
    pl.status_aktual,
    ss.tanggal,
    ss.remark');

   $check = DB::select('SELECT
    ss.tanggal,
    SUBSTR(es.`name`,1,(LOCATE(" ",es.`name`))) AS name,
    es.department,
    ss.kode_request,
    ss.status_aktual,
    ss.status_mt,
    pl.status_aktual as pl_status,
    count(ss.kode_request) as total
    FROM
    warehouse_pelayanans AS ss
    LEFT JOIN employee_syncs AS es ON ss.pic_produksi = es.employee_id
    LEFT JOIN warehouse_pelayanan_logs AS pl ON ss.kode_request = pl.kode_request 
    WHERE
    ss.tanggal = "'.$tanggal.'" 
    AND (select distinct section from employee_syncs where employee_id = es.employee_id) = "'.$get_op->section.'"
    AND ss.status_aktual = "pengecekan material"
    GROUP BY
    ss.kode_request,
    ss.status_aktual,
    ss.tanggal,
    es.name,
    es.department,
    pl.status_aktual,
    ss.status_mt');


   $response = array(
    'status' => true,
    'prod' => $job,
    'check' => $check,
    'tanggal' => $tanggal
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

public function fetchDetailRequest(Request $request){
 $op_name = Auth::user()->username;
 try {
   $detail_request_prd = db::select('SELECT
    eg.id,
    eg.kode_request,
    eg.lot,
    eg.gmc,eg.sloc_name,
    eg.description,
    eg.uom,
    eg.status_mt,
    eg.no_hako,
    eg.qty_req,
    eg.quantity_check,
    eg.quantity_request,eg.status_pengantaran,eg.status_pel,eg.lokasi_produksi,eg.status_aktual,eg.pic_received,ek.name
    FROM
    warehouse_pelayanans as eg
    LEFT JOIN employee_syncs AS ek ON eg.pic_received = ek.employee_id
    where eg.status_mt = "1"
    ORDER BY
    eg.id ASC');

   $response = array(
    'status' => true,
    'request_detail' => $detail_request_prd
  );
   return Response::json($response);
 }catch (\Exception $e) {
   $response = array(
    'status' => false,
    'message' => $e->getMessage(),
  );
   return Response::json($response);
 }
}

public function fetchInternalPelayanan(Request $request){

  if ($request->get('tanggals')) {
    $tanggal = $request->get('tanggals');
  } else {
    $tanggal = date('Y-m-d');
  }

  $pels = db::select('SELECT DISTINCT
    eg.kode_request,
    DATE_FORMAT(eg.created_at,"%Y-%m-%d") as tanggal,
    eg.remark,
    count(eg.kode_request) as total,
    es.`name`,
    ek.name as namess,
    we.name as name_peng
    FROM
    warehouse_logs as eg
    LEFT JOIN warehouse_employee_masters AS es ON eg.pic_pelayanan = es.employee_id
    LEFT JOIN warehouse_employee_masters AS we ON eg.pic_pengantaran = we.employee_id
    LEFT JOIN employee_syncs AS ek ON eg.pic_request = ek.employee_id
    WHERE
    remark  = "finish" and DATE_FORMAT(eg.created_at,"%Y-%m-%d") = "'.$tanggal.'"
    GROUP BY
    eg.kode_request,
    eg.remark,
    es.`name`,
    ek.name,
    we.name,
    eg.created_at
    ');

  $detail_gmc_pel = db::select('SELECT 
    kode_request,
    gmc,
    no_hako,
    uom,
    description,qty_kirim,
    lot,remark 
    FROM
    warehouse_logs 
    WHERE
    DATE_FORMAT(created_at,"%Y-%m-%d") = "'.$tanggal.'"
    ');

  $response = array(
    'status' => true,
    'detail_pels' => $pels,
    'det_gmc_finish' => $detail_gmc_pel
  );
  return Response::json($response);
}

public function fetchInternalImport(Request $request){

  if ($request->get('tanggals')) {
    $tanggal = $request->get('tanggals');
  } else {
    $tanggal = date('Y-m-d');
  }

  $kode = $request->get('kode');


  $imports = db::select('SELECT DISTINCT
    eg.no_case,
    count(eg.no_case) as total,
    es.`name`,
    eg.status_aktual
    FROM
    warehouse_packinglists as eg
    LEFT JOIN warehouse_employee_masters AS es ON eg.employee_id = es.employee_id
    WHERE
    tanggal_kedatangan_aktual = "'.$tanggal.'" AND status_aktual = "Finish Penataan "
    GROUP BY no_case,name,status_aktual');

  $detail_finish = db::select('SELECT 
    no_case,
    gmc,
    description,
    quantity
    FROM
    warehouse_packinglists 
    WHERE
    tanggal_kedatangan_aktual = "'.$tanggal.'" && status_receive = "finish"
    && no_case = "'.$kode.'"
    ');

  $response = array(
    'status' => true,
    'imports' => $imports,
    'detail_finish' => $detail_finish
  );
  return Response::json($response);
}

public function fetchCountPel(Request $request){

  if ($request->get('tanggals')) {
    $tanggal = $request->get('tanggals');
  } else {
    $tanggal = date('Y-m-d');
  }

  $material_count = db::select('SELECT DISTINCT
    kode_request,
    COUNT(kode_request) as total, sloc_name,remark
    FROM
    warehouse_pelayanans
    WHERE
    status_aktual = "proses0" 
    GROUP BY
    kode_request,sloc_name,remark
    ORDER BY kode_request asc');

  $detail_request = db::select('SELECT
    ss.id,
    ss.kode_request,
    es.name,
    ss.lot,
    ss.sloc_name,
    ss.gmc,
    ss.description,
    ss.uom,
    ss.no_hako
    FROM
    warehouse_pelayanans AS ss
    LEFT JOIN employee_syncs AS es ON ss.pic_produksi = es.employee_id
    ');

  $response = array(
    'status' => true,
    'count_material' => $material_count,
    'detail_request' => $detail_request 
  );
  return Response::json($response);
}

public function fetchCountPengambilanMt(Request $request){

  if ($request->get('tanggals')) {
    $tanggal = $request->get('tanggals');
  } else {
    $tanggal = date('Y-m-d');
  }

  $pengambilan_mts = db::select('SELECT DISTINCT
    wp.tanggal,
    wp.kode_request,
    wp.sloc_name,
    es.NAME,
    ek.`name`,
    ek.id,
    wp.pic_pelayanan
    FROM
    warehouse_pelayanans AS wp
    LEFT JOIN users AS es ON wp.pic_produksi = es.username 
    LEFT JOIN users AS ek ON wp.pic_pelayanan = ek.username 
    WHERE
    wp.status_aktual = "check"');


  $delivery = db::select('SELECT DISTINCT
    wp.tanggal,
    wp.kode_request,
    wp.sloc_name,
    es.NAME,
    ek.`name`,
    ep.name as names,
    ek.id,
    wp.area
    FROM
    warehouse_pelayanans AS wp
    LEFT JOIN users AS es ON wp.pic_produksi = es.username 
    LEFT JOIN users AS ek ON wp.pic_pelayanan = ek.username
    LEFT JOIN users AS ep ON wp.employee_id_pengantaran = ep.username  
    WHERE
    wp.status_aktual = "Pengantaran"');

  $check = db::select('SELECT DISTINCT
    wp.tanggal,
    wp.kode_request,
    wp.sloc_name,
    es.NAME,
    ek.`name`,
    ep.name as names,
    ek.id,
    wp.area
    FROM
    warehouse_pelayanans AS wp
    LEFT JOIN users AS es ON wp.pic_produksi = es.username 
    LEFT JOIN users AS ek ON wp.pic_pelayanan = ek.username
    LEFT JOIN users AS ep ON wp.employee_id_pengantaran = ep.username   
    WHERE
    wp.status_aktual = "pengecekan material"');


  $detail_request = db::select('SELECT
    ss.id,
    ss.kode_request,
    es.name,
    ss.lot,
    ss.sloc_name,
    ss.gmc,
    ss.description,
    ss.uom,
    ss.no_hako
    FROM
    warehouse_pelayanans AS ss
    LEFT JOIN employee_syncs AS es ON ss.pic_produksi = es.employee_id
    ');

  $response = array(
    'status' => true,
    'pengambilan_mts' => $pengambilan_mts,
    'delivery' => $delivery,
    'check' => $check,
    'detail_request' => $detail_request 
  );
  return Response::json($response);
}

public function fetchCountImport(Request $request){

  if ($request->get('tanggals')) {
    $tanggal = $request->get('tanggals');
  } else {
    $tanggal = date('Y-m-d');
  }

  $material_import = db::select('SELECT
    vendor,
    no_case,
    no_invoice,
    no_surat_jalan,
    COUNT(no_case) as total
    FROM
    warehouse_packinglists
    WHERE tanggal_kedatangan_aktual = "'.$tanggal.'" and status_receive = "drop" and status_aktual = "wainting" group by no_case,vendor,no_invoice,no_surat_jalan');

  $detail_import = db::select('SELECT
    no_case,
    vendor,
    gmc,
    description,
    quantity,
    no_invoice,
    no_surat_jalan
    FROM
    warehouse_packinglists 
    WHERE
    tanggal_kedatangan = "'.$tanggal.'"');

  $response = array(
    'status' => true,
    'material_import' => $material_import,
    'detail_import' => $detail_import 
  );
  return Response::json($response);
}

public function fetchDetailJob(Request $request){
  $detail_job = db::select('SELECT 
    kode_request,
    gmc,
    description,sloc_name,
    lot 
    FROM
    warehouse_pelayanans 
    WHERE
    kode_request = "'.$request->get('id').'"');

  $response = array(
    'status' => true,
    'detail_job' => $detail_job
  );
  return Response::json($response);
}

public function updatePelayananJob(Request $request)
{
  $id_user = Auth::user()->username;
  $kode_request = $request->get('kode_request');
  $tanggal = date('Y-m-d');
  $pelayanan_get = WarehousePelayanan::where('kode_request','=',$kode_request)->first();
  $get = $pelayanan_get->status_aktual;
  $emp = 0;

  $check_emp = db::select('SELECT employee_id,status,end_job
    FROM warehouse_time_operator_logs
    WHERE DATE_FORMAT( created_at, "%Y-%m-%d" ) = "'.$tanggal.'" AND employee_id = "'.$id_user.'" and end_job is NULL');

  for ($i=0; $i < count($check_emp); $i++) { 
    if ($check_emp[$i]->status == "idle" && $check_emp[$i]->end_job == null){
      $emp = 1;
    }else{
      $emp = 0;
      $response = array(
        'status' => false,
        'message' => 'Operator Sedang Bekerja',   
      );
      return Response::json($response);
    }

  }

  if ($emp == 1) {

   $pelayanan1 = WarehousePelayanan::where('kode_request','=',$kode_request)->update([
     'status_aktual' => "check",
     'pic_pelayanan' => Auth::user()->username,
     'start_pel' => date('Y-m-d H:i:s')
   ]);

   $emp_id = WarehousePelayanan::where('kode_request','=',$kode_request)->first();

   $employee_groups = DB::table('warehouse_employee_masters')
   ->where('employee_id', '=', $emp_id->pic_pelayanan)          
   ->update([
    'start_time_status' => date('M d Y H:i:s'),
    'status' => "work",
    'status_aktual_pekerjaan' => "Pengambilan Material"
  ]);

   $get_pl = db::select('SELECT id,employee_id,status,end_job FROM `warehouse_time_operator_logs` where `status`="idle" and employee_id = "'.$id_user.'" and end_job is null order by employee_id DESC LIMIT 1');

   if ($get_pl[0]->status == "idle" && $get_pl[0]->end_job == null) {

    $get_pl = db::select('SELECT id,employee_id,status FROM `warehouse_time_operator_logs` where employee_id = "'.$id_user.'" and `status`="idle" and end_job is null order by employee_id DESC LIMIT 1');

    $job_pl = WarehouseTimeOperatorLog::where('id','=',$get_pl[0]->id)->where('status','=','idle')->update([
     'end_job' => date('Y-m-d H:i:s')
   ]);

    $group_pl = db::select('SELECT kode_request,GROUP_CONCAT(gmc) as gmc,status_aktual
      FROM warehouse_pelayanans
      WHERE tanggal = "'.$tanggal.'" AND kode_request = "'.$kode_request[0].'"
      GROUP BY kode_request, status_aktual');

    for ($i=0; $i < count($group_pl) ; $i++) {

      $data = new WarehouseTimeOperatorLog([
        'employee_id' => $get_pl[0]->employee_id,
        'request_desc' => $group_pl[0]->kode_request,
        // 'desc_gmc' => $group_pl[0]->gmc,
        'status' => $group_pl[0]->status_aktual,
        'start_job' => date("Y-m-d H:i:s")
      ]);
      $data->save();


    }

    $get_pl_log = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanan_logs` where kode_request = "'.$group_pl[0]->kode_request.'"');

    $update_pl = WarehousePelayananLog::where('kode_request','=',$group_pl[0]->kode_request)->update([
     'status_aktual' => "proses0"
   ]);

  }
  $response = array(
   'status' => true,
   'remark' => 'logged_in',
   'pelayanan1' => $pelayanan1
 );
  return Response::json($response);
}

}

public function updatePengecekanImport(Request $request)
{
  $id_user = Auth::user()->username;
  $no_case = $request->get('no_case');
  $tanggal = date('Y-m-d');

  $import_req = WarehousePackinglist::where('no_case','=',$no_case)->update([
   'status_aktual' => "pengecekan",
   'pic_cek' => Auth::user()->username,
   'start_check' => date('Y-m-d H:i:s')
 ]);

  $emp_id = WarehousePackinglist::where('no_case','=',$no_case)->first();
  $material_import = DB::table('warehouse_employee_masters')
  ->where('employee_id', '=', $emp_id->pic_pelayanan)          
  ->update([
    'start_time_status' => date('M d Y H:i:s'),
    'status' => "work",
    'status_aktual_pekerjaan' => "Pengecekan Material"
  ]);

  $get_mr = db::select('SELECT id,employee_id,status,end_job FROM `warehouse_time_operator_logs` where `status`="idle" and employee_id = "'.$id_user.'" and end_job is null order by employee_id DESC LIMIT 1');

  if ($get_mr[0]->status == "idle" && $get_mr[0]->end_job == null) {

    $get_last = db::select('SELECT id,employee_id,status FROM `warehouse_time_operator_logs` where employee_id = "'.$id_user.'" and `status`="idle" and end_job is null order by employee_id DESC LIMIT 1');

    $job_last = WarehouseTimeOperatorLog::where('id','=',$get_last[0]->id)->where('status','=','idle')->update([
     'end_job' => date('Y-m-d H:i:s')
   ]);

    $group_gmc = db::select('SELECT no_case,GROUP_CONCAT(gmc) as gmc,status_aktual
      FROM warehouse_packinglists
      WHERE tanggal_kedatangan_aktual = "'.$tanggal.'" AND no_case = "'.$no_case[0].'"
      GROUP BY no_case,status_aktual');

    for ($i=0; $i < count($group_gmc) ; $i++) {

      $data = new WarehouseTimeOperatorLog([
        'employee_id' => $get_last[0]->employee_id,
        'request_desc' => $group_gmc[0]->no_case,
        // 'desc_gmc' => $group_gmc[0]->gmc,
        'status' => $group_gmc[0]->status_aktual,
        'start_job' => date("Y-m-d H:i:s")
      ]);
      $data->save();
    }
  }



  $response = array(
   'status' => true,
   'remark' => 'logged_in',
   'import_req' => $import_req
 );
  return Response::json($response);
}

public function verifikasi_packinglist($id)
{
  $po = AccPurchaseOrder::find($id);

  $path = '/po_list/' . $po->file_pdf;            
  $file_path = asset($path);

  return view('accounting_purchasing.verifikasi.po_verifikasi', array(
    'po' => $po,
    'file_path' => $file_path,
  ))->with('page', 'Purchase Order');
}

public function check_gmc(Request $request){
  try {
   $check_detail_gmc = db::select('SELECT
    material_description, material_number 
    FROM
    material_plant_data_lists 
    WHERE
    material_number = "'.$request->get('gmc').'"');

   $response = array(
    'status' => true,
    'check_detail_gmc' => $check_detail_gmc
  );
   return Response::json($response);
 }catch (\Exception $e) {
   $response = array(
    'status' => false,
    'message' => $e->getMessage(),
  );
   return Response::json($response);
 }
}

public function fetchMaterialRequest(Request $request)
{
 try {
  $process = db::select('SELECT id, dept, sloc_name,location FROM `warehouse_material_names` WHERE location = "'.$request->get('names').'"');

  $response = array(
    'status' => true,
    'process' => $process
  );
  return Response::json($response);
}catch (\Exception $e) {
 $response = array(
  'status' => false,
  'message' => $e->getMessage(),
);
 return Response::json($response);
}
}

public function testPdf($form_number)
{

 $process = db::select('SELECT gmc,description,qty_req,qty_kirim,no_hako,kode_request,pic_pelayanan,pic_request FROM `warehouse_logs` WHERE kode_request = "'.$form_number.'"');
 $resume = db::select('SELECT
  gmc,
  description,
  SUM(qty_kirim) as qty_kirim
  FROM
  `warehouse_logs` 
  WHERE
  kode_request = "'.$form_number.'"
  GROUP BY gmc,description');
 $pt = db::select('SELECT count(kode_request) as total FROM `warehouse_logs` WHERE kode_request = "'.$form_number.'"');

 $heads = db::select('SELECT DISTINCT
  es.kode_request as kode_request,
  eg.employee_id as employee_id,
  eg.name as names,
  ek.name as name_wh,
  es.pic_request as pic_produksi,
  es.tanggal,
  eg.shift as shift,
  es.loc as loc,
  es.sloc_name as sloc_name
  FROM
  warehouse_logs AS es
  LEFT JOIN warehouse_employee_masters AS eg ON es.pic_pelayanan = eg.employee_id
  LEFT JOIN employee_syncs AS ek ON es.pic_request = ek.employee_id
  WHERE
  es.kode_request = "'.$form_number.'" GROUP BY kode_request,pic_pelayanan,pic_request,shift,sloc_name,loc,names,name_wh,employee_id,es.tanggal');

 $pdf = \App::make('dompdf.wrapper');
 $pdf->getDomPDF()->set_option("enable_php", true);;
 $pdf->setPaper('A4', 'potrait');

 $pdf->loadView('warehouse_new.report.mod_file', array(
  'datas' => $process,
  'pt' => $pt,
  'ket'=> $heads,
  'resume'=> $resume

));

 $pdf->save(public_path() . "/files/request_produksi/MOD_".$form_number.".pdf");
 return $pdf->stream($heads[0]->loc."-".$form_number.".pdf");
}

public function deleteRqsPrd(Request $request){
  try{
    $delete_rqst = db::table('warehouse_pelayanans')
    ->where('kode_request', '=', $request->get('ids'))
    ->delete(); 
    $delete_ins = db::table('warehouse_pelayanan_ins')
    ->where('kode_request', '=', $request->get('ids'))
    ->delete(); 
    $delete_logs = db::table('warehouse_pelayanan_logs')
    ->where('kode_request', '=', $request->get('ids'))
    ->delete(); 
    $delete_logs = db::table('warehouse_time_operator_logs')
    ->where('request_desc', '=', $request->get('ids'))
    ->delete(); 
    $response = array(
      'status' => true,
      'message' => 'Delete successful',   
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

public function indexOperatorAktual(Request $request){
  $title = 'Warehouse Operator Internal Work';
  $title_jp = '??';

  return view('warehouse_new.report.operator_aktual', array(
    'title' => $title,
    'title_jp' => $title_jp
  ))->with('page', 'Workshop Operator Work Aktual')->with('head', 'Warehouse');
}

public function fetchOperatorAktual(Request $request){  
 if(strlen($request->get('date')) > 0){
  $tgl = date('Y-m-d',strtotime($request->get('date')));
  $jam = date('Y-m-d H:i:s',strtotime($request->get('date').date('H:i:s')));
  if ($jam > date('Y-m-d',strtotime($tgl)).' 00:00:01' && $jam < date('Y-m-d',strtotime($tgl)).' 02:00:00' && $tgl == date('Y-m-d',strtotime($tgl))) {
    $nextday =  date('Y-m-d', strtotime($tgl));
    $yesterday = date('Y-m-d',strtotime($tgl." -1 days"));
  }else{
    $nextday =  date('Y-m-d', strtotime($tgl . " +1 days"));
    $yesterday = date('Y-m-d',strtotime($tgl));
  }
}else{
  $tgl = date("Y-m-d");
  $jam = date('Y-m-d H:i:s');
  if ($jam > date('Y-m-d',strtotime($tgl)).' 00:00:01' && $jam < date('Y-m-d',strtotime($tgl)).' 02:00:00') {
    $nextday = date('Y-m-d');
    $yesterday = date('Y-m-d',strtotime("-1 days"));
  }else{
    $nextday = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
    $yesterday = date('Y-m-d');
  }
}


$operators = db::select('SELECT
  es.employee_id,
  es.`name` 
  FROM
  warehouse_employee_masters AS eg
  LEFT JOIN employee_syncs AS es ON eg.employee_id = es.employee_id
  order by eg.employee_id asc');

$op_workloads = db::select("SELECT
  eg.employee_id,
  eg.`name`,
  es.no_case,
  es.start_check,
  es.end_check,
  COUNT( es.no_case ) AS total,
  TIMESTAMPDIFF( minute, es.start_check, es.end_check ) AS second2,
  es.start_move,
  es.end_move,
  TIMESTAMPDIFF( minute, es.start_move, es.end_move ) AS second1 
  FROM
  warehouse_employee_masters AS eg
  LEFT JOIN warehouse_packinglists AS es ON eg.employee_id = es.employee_id 
  WHERE
  es.start_check IS NOT NULL AND es.end_check IS NOT NULL AND es.start_move IS NOT NULL AND es.end_move IS NOT NULL 
  GROUP BY
  eg.employee_id,
  eg.`name`,
  es.start_check,
  es.end_check,
  es.no_case,
  es.start_move,
  es.end_move 
  ORDER BY
  eg.employee_id,
  es.start_check ASC");

$op_pelayanan = db::select("SELECT
  eg.employee_id,
  eg.`name`,
  es.STATUS,
  es.start_job,
  IFNULL(DATE_FORMAT(es.end_job,'%M %d %Y %H:%i:%s'), DATE_FORMAT(NOW(),'%M %d %Y %H:%i:%s')) as end_job,
  DATE_FORMAT(es.start_job,'%M %d %Y %H:%i:%s') as dt,
  es.request_desc,
  es.joblist
  FROM
  warehouse_employee_masters AS eg
  LEFT JOIN warehouse_time_operator_logs AS es ON eg.employee_id = es.employee_id 
  WHERE
  es.start_job IS NOT NULL
  and es.start_job >= '".$yesterday." 06:00:00' && es.start_job <= '".$nextday." 02:00:00'
  GROUP BY
  eg.employee_id,
  eg.`name`,
  es.start_job,
  es.end_job,
  es.STATUS,
  es.request_desc,
  es.joblist
  ORDER BY
  eg.id ASC
  ");

$op_pengantaran = db::select("SELECT
  eg.employee_id,
  eg.`name`,
  es.kode_request,
  es.start_pengantaran,
  es.end_pengantaran,
  COUNT( es.kode_request ) AS total,
  TIMESTAMPDIFF( MINUTE, es.start_pengantaran, es.end_pengantaran ) AS pengantaran
  FROM
  warehouse_employee_masters AS eg
  LEFT JOIN warehouse_pelayanans AS es ON eg.employee_id = es.employee_id_pengantaran 
  WHERE
  es.start_pel IS NOT NULL AND es.end_pengantaran IS NOT NULL
  GROUP BY
  eg.employee_id,
  eg.`name`,
  es.start_pengantaran,
  es.end_pengantaran,
  es.kode_request 
  ORDER BY
  eg.employee_id,
  es.start_pengantaran ASC");

$operators_time = db::select('SELECT
  op.NAME,
  op.shift,
  op.st_check,
  op.st_idle,
  op.st_deliv,
  op.st_Lain,
  op.employee_id
  FROM
  (
    SELECT
    operator.employee_id,
    operator.`name`,
    operator.shift,
    COALESCE ( st_check.time / 60 , 0 ) AS st_check,
    COALESCE ( (st_idle.time / 60) , 0 ) AS st_idle,
    COALESCE ( st_deliv.time / 60 , 0 ) AS st_deliv,
    COALESCE ( st_Lain.time / 60 , 0 ) AS st_Lain
    FROM
    (
      SELECT
      op.employee_id,
      op.shift,
      concat(
        SPLIT_STRING ( e.`name`, " ", 1 ),
        " ",
        SPLIT_STRING ( e.`name`, " ", 2 )) AS `name` 
      FROM
      warehouse_employee_masters op
      LEFT JOIN employee_syncs e ON op.employee_id = e.employee_id 
      ) operator
    LEFT JOIN (
      SELECT
      employee_id,
      sum(
        timestampdiff( SECOND, start_job, end_job )) AS time 
      FROM
      warehouse_time_operator_logs 
      WHERE
      `status` = "check" && created_at >= "'.$yesterday.' 06:00:00" && created_at <= "'.$nextday.' 02:00:00" 
      GROUP BY
      employee_id 
      ) st_check ON operator.employee_id = st_check.employee_id
      LEFT JOIN (
      SELECT
      employee_id,
      sum(
      timestampdiff( SECOND, start_job, end_job )) AS time 
      FROM
      warehouse_time_operator_logs 
      WHERE
      `status` = "idle"  && created_at >= "'.$yesterday.' 06:00:00" && created_at <= "'.$nextday.' 02:00:00"
      GROUP BY
      employee_id 
      ) st_idle ON operator.employee_id = st_idle.employee_id
      LEFT JOIN (
      SELECT
      employee_id,
      sum(
      timestampdiff( SECOND, start_job, end_job )) AS time 
      FROM
      warehouse_time_operator_logs 
      WHERE
      `status` = "Pengantaran"  && created_at >= "'.$yesterday.' 06:00:00" && created_at <= "'.$nextday.' 02:00:00" 
      GROUP BY
      employee_id 
      ) st_deliv ON operator.employee_id = st_deliv.employee_id
      LEFT JOIN (
      SELECT
      employee_id,
      sum(
      timestampdiff( SECOND, start_job, end_job )) AS time 
      FROM
      warehouse_time_operator_logs 
      WHERE
      `status` = "Lain"  && created_at >= "'.$yesterday.' 06:00:00" && created_at <= "'.$nextday.' 02:00:00" 
      GROUP BY
      employee_id 
      ) st_Lain ON operator.employee_id = st_Lain.employee_id 
      ORDER BY
      st_check DESC,
      `name` ASC 
    ) op');

    $response = array(
      'status' => true,
      'operators' => $operators,
      'operators_time' => $operators_time,
      'op_workloads' => $op_workloads,
      'date' => $yesterday,
      'op_pelayanan' => $op_pelayanan,
      'op_pengantaran' => $op_pengantaran,
    );
    return Response::json($response);

  }

  public function fetchDetailOperator(Request $request){
    try{
     $st = $request->get('status');
     if(strlen($request->get('time')) > 0){
      $tgl = date('Y-m-d',strtotime($request->get('time')));
      $jam = date('Y-m-d H:i:s',strtotime($request->get('time').date('H:i:s')));
      if ($jam > date('Y-m-d',strtotime($tgl)).' 00:00:01' && $jam < date('Y-m-d',strtotime($tgl)).' 02:00:00' && $tgl == date('Y-m-d',strtotime($tgl))) {
        $nextday =  date('Y-m-d', strtotime($tgl));
        $yesterday = date('Y-m-d',strtotime($tgl." -1 days"));
      }else{
        $nextday =  date('Y-m-d', strtotime($tgl . " +1 days"));
        $yesterday = date('Y-m-d',strtotime($tgl));
      }
    }else{
      $tgl = date("Y-m-d");
      $jam = date('Y-m-d H:i:s');
      if ($jam > date('Y-m-d',strtotime($tgl)).' 00:00:01' && $jam < date('Y-m-d',strtotime($tgl)).' 02:00:00') {
        $nextday = date('Y-m-d');
        $yesterday = date('Y-m-d',strtotime("-1 days"));
      }else{
        $nextday = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
        $yesterday = date('Y-m-d');
      }
    }
    $detail_op = db::select('SELECT
      eg.employee_id,
      eg.`name`,
      es.no_case,
      es.start_check,
      es.end_check,
      COUNT( es.no_case ) AS total,
      TIMESTAMPDIFF( MINUTE, es.start_check, es.end_check ) AS second2,
      es.start_move,
      es.end_move,
      TIMESTAMPDIFF( MINUTE, es.start_move, es.end_move ) AS second1 
      FROM
      warehouse_employee_masters AS eg
      LEFT JOIN warehouse_packinglists AS es ON eg.employee_id = es.employee_id 
      WHERE
      es.start_check IS NOT NULL 
      AND es.tanggal_kedatangan_aktual = "'.$request->get('time').'" 
      AND eg.NAME = "'.$request->get('name').'" 
      GROUP BY
      eg.employee_id,
      eg.`name`,
      es.start_check,
      es.end_check,
      es.no_case,
      es.start_move,
      es.end_move 
      ORDER BY
      eg.employee_id,
      es.start_check ASC');
    $detail_op_pel = db::select('SELECT
      eg.employee_id,
      eg.`name`,
      es.kode_request,
      es.start_pel,
      es.end_pel,
      COUNT( es.kode_request ) AS total,
      TIMESTAMPDIFF( MINUTE, es.start_pel, es.end_pel ) AS second2 
      FROM
      warehouse_employee_masters AS eg
      LEFT JOIN warehouse_pelayanans AS es ON eg.employee_id = es.pic_pelayanan 
      WHERE
      es.start_pel IS NOT NULL 
      AND es.tanggal = "'.$request->get('time').'" 
      AND eg.NAME = "'.$request->get('name').'" 
      GROUP BY
      eg.employee_id,
      eg.`name`,
      es.start_pel,
      es.end_pel,
      es.kode_request 
      ORDER BY
      eg.employee_id,
      es.start_pel ASC');

    $mt = db::select("SELECT
      employee_id 
      FROM
      warehouse_employee_masters 
      WHERE
      NAME LIKE '%".$request->get('name')."%'");


    $detail_op_pel_delivery = db::select('SELECT
  * ,concat(
      SPLIT_STRING ( es.`name`, " ", 1 ),
      " ",
      SPLIT_STRING ( es.`name`, " ", 2 )) AS `name`,ss.status as sts
  FROM
  warehouse_time_operator_logs as ss 
  LEFT JOIN warehouse_employee_masters AS es ON ss.employee_id = es.employee_id
  WHERE
  ss.STATUS = "'.$request->get('status').'" AND ss.employee_id = "'. $mt[0]->employee_id.'" && ss.created_at >= "'.$yesterday.' 06:00:00" && ss.created_at <= "'.$nextday.' 02:00:00"  ');



    $response = array(
      'status' => true,
      'message' => 'Delete successful', 
      'detail_op' => $detail_op,
      'detail_op_pel' => $detail_op_pel,
      'detail_op_pel_delivery' => $detail_op_pel_delivery
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

public function fetchEditRequest(Request $request)
{
  $flows = WarehousePelayanan::where('kode_request', '=', $request->get('flow_name'))
  ->get();
  $response = array(
    'status' => true,
    'flows' => $flows
  );
  return Response::json($response);
}

public function postEditRequest(Request $request)
{
  $id = ($request->get('ids'));
  $area = $request->get('areas');
  $gmc = $request->get('gmcs');
  $area_code = $request->get('area_codes');
  $sloc_name = $request->get('sloc_names');
  $loc = $request->get('locs');
  $status_aktual = $request->get('status_aktuals');
  $pic_produksi = $request->get('pic_produksis');
  $description = $request->get('descriptions');
  $quantitycheck = $request->get('quantitychecks');
  $no_hako = $request->get('no_hakos');
  $kode_request = $request->get('kode_requests');
  $lot = $request->get('lots');

  $cek = WarehousePelayanan::where('kode_request', '=', $request->get('kode_requests'))
  ->get();
  if (count($cek) > 0) {
   $delete = DB::table('warehouse_pelayanans')
   ->where('kode_request', '=', $request->get('kode_requests'))
   ->delete(); 
 }

 $num = 1;
 for ($i=0; $i < count($request->get('ids')); $i++) { 
  $wjo_pic = new WarehousePelayanan([
    'tanggal' => date('Y-m-d'),
    'kode_request' => $kode_request[$i],
    'area' => $area[$i],
    'area_code' => $area_code[$i],
    'sloc_name' => $sloc_name[$i],
    'loc' => $loc[$i],
    'gmc' => $gmc[$i],
    'description' => $description[$i],
    'uom' => "PC",
    'status_mt' => 1,
    'no_hako' => $no_hako[$i],
    'status_aktual' => $status_aktual[$i],
    'quantity_request' => $quantitycheck[$i],
    'lot' => $lot[$i],
    'pic_produksi' => $pic_produksi[$i],
    'created_by' => Auth::user()->username

  ]);
  $wjo_pic->save();
  $num++;
}

$response = array(
  'status' => true,
  'cek' => $cek
);
return Response::json($response);
}

public function postEditSJ(Request $request)
{
  try {
    $id = $request->get('ids');
    $qty = $request->get('gmcs');
    $no_sjs = $request->get('no_sjs');
    $no_cases = $request->get('no_cases');
    $vendors = $request->get('vendors');
    $tes = $request->get('no_sjks');
    $tes1 = $request->get('no_sjl');
    $description2 = $request->get('description2');
    $quantitychecks2 = $request->get('quantitychecks2');
    $gm = $request->get('gm');

    $ceks = WarehousePackinglist::where('no_surat_jalan', '=', $request->get('no_sjs'))->get();

    for ($i=0; $i < count($ceks); $i++) { 
      $data1 = WarehousePackinglist::where('no_surat_jalan', '=', $request->get('no_sjs'))->update([
        'status_material' => null
      ]);
    }

    for ($i=0; $i < count($request->get('no_sjs')); $i++) { 
      $data2 = WarehousePackinglist::where('id', $id[$i])->update([
        'quantity' => $qty[$i],
        'created_by' => Auth::user()->username,
        'status_material' => "ok"
      ]);
    }

    $cek = WarehousePackinglist::where('no_surat_jalan', '=', $request->get('no_sjs'))->where('status_material','=', null)->get();

    if (count($cek) > 0) {
     $delete = DB::table('warehouse_packinglists')
     ->where('no_surat_jalan', '=', $request->get('no_sjs'))->where('status_material', '=', null)->delete(); 
   }


   $num = 1;
   for ($i=0; $i < count($gm); $i++) { 
    $material2 = new WarehousePackinglist([
      'tanggal_kedatangan' => date('Y-m-d'),
      'gmc' => $request->get('gm')[$i],
      'no_surat_jalan' => $request->get('no_sjl')[$i],
      'status_material' => "ok",
      'no_case' => $no_cases[$i],
      'description' => $description2[$i],
      'quantity' => $quantitychecks2[$i],
      'vendor' => $vendors[$i],
      'status_job' => "not done",
      'status_receive' => "wainting",
      'status_aktual' => "wainting",
      'created_by' => Auth::user()->username
    ]);
    $material2->save();
    $num++;
  }

  $response = array(
    'status' => true
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

public function postEditNoInv(Request $request)
{
  try {
    $date = $request->get('dates');
    $no_invoice = $request->get('no_invoice');
    $data2 = WarehousePackinglist::where('no_invoice', $no_invoice)->update([
      'tanggal_kedatangan' => $date,
      'created_by' => Auth::user()->username
    ]);
    $response = array(
      'status' => true,
      'test' => $data2
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



public function fetchEditMaterial(Request $request)
{
  $no_sj = WarehousePackinglist::where('no_surat_jalan', '=', $request->get('no_sj'))
  ->get();
  $no_pk = WarehousePackinglist::where('no_invoice', '=', $request->get('no_pk'))
  ->get();
  $response = array(
    'status' => true,
    'no_sj' => $no_sj,
    'no_pk' => $no_pk
  );
  return Response::json($response);
}

public function report_mod_file(){
  $title = "History Request Material MOD";
  $title_jp = "";

  return view('warehouse_new.report_mod_file', array(
    'title' => $title,
    'title_jp' => $title_jp
  ))->with('head', 'History Request Material MOD')->with('page', 'History Request Material MOD');
}

public function fetchHistoryRequest(Request $request)
{
  try {
   $date_from = $request->get('tanggal_from');
   $date_to = $request->get('tanggal_to');
   $now = date('Y-m-d');
   $user = Auth::user()->username;

   $get_op = EmployeeSync::where('employee_syncs.employee_id', '=', $user)->first();


   if ($date_from == '') {
    if ($date_to == '') {
     $whereDate = 'AND DATE_FORMAT(ss.tanggal,"%Y-%m-%d")  = "'.$now.'"';
   }else{
     $whereDate = 'AND DATE_FORMAT(ss.tanggal,"%Y-%m-%d") BETWEEN CONCAT(DATE_FORMAT("'.$date_to.'" - INTERVAL 4 DAY,"%Y-%m-%d")) AND "'.$date_to.'"';
   }
 }else{
  if ($date_to == '') {
   $whereDate = 'AND DATE_FORMAT(ss.tanggal,"%Y-%m-%d") BETWEEN "'.$date_from.'" AND DATE(NOW())';
 }else{
   $whereDate = 'AND DATE_FORMAT(ss.tanggal,"%Y-%m-%d") BETWEEN "'.$date_from.'" AND "'.$date_to.'"';
 }
}

if (Auth::user()->role_code == "OP-WH-Exim" || Auth::user()->role_code == "MIS" || Auth::user()->role_code == "F-SPL" || Auth::user()->role_code == "PC" || Auth::user()->role_code == "L-WH" || Auth::user()->username == "pi1710002") { 
  $users = "";
}else{
  $users = 'AND (select distinct section from employee_syncs where employee_id = ss.pic_request) = "'.$get_op->section.'"';
}

$list_request = DB::SELECT('SELECT
  ss.tanggal,
  DATE_FORMAT(ss.created_at,"%H:%i:%s") as times,
  es.name,
  es.department as dep,
  ss.kode_request,
  ss.remark,
  ss.loc,
  ss.status,
  COUNT( ss.kode_request ) AS total
  FROM
  warehouse_logs AS ss
  LEFT JOIN employee_syncs AS es ON ss.pic_request = es.employee_id
  WHERE
  es.end_date IS NULL '.$whereDate.' '.$users.' and ss.remark = "finish" 
  GROUP BY
  ss.kode_request,
  ss.remark,
  es.name,
  ss.loc,
  ss.status,
  es.department,
  ss.created_at,
  ss.tanggal');

$response = array(
  'status' => true,
  'message' => 'Get Data Success',
  'datas' => $list_request
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

public function indexPrintKanban(Request $request){
  $title = 'Design Kanban Material';
  $title_jp = '??';

  $gmcs = MaterialHakoKanban::select('gmc_material')
  ->distinct()
  ->orderBy('gmc_material', 'ASC')
  ->get();

  $loc = MaterialHakoKanban::select('rcvg_sloc')
  ->distinct()
  ->orderBy('rcvg_sloc', 'ASC')
  ->get();

  $loc1 = MaterialHakoKanban::select('rcvg_sloc')
  ->distinct()
  ->orderBy('rcvg_sloc', 'ASC')
  ->get();

  $sloc_name = MaterialHakoKanban::select('sloc_name')
  ->distinct()
  ->orderBy('sloc_name', 'ASC')
  ->get();
  $sloc_name1 = MaterialHakoKanban::select('sloc_name')
  ->distinct()
  ->orderBy('sloc_name', 'ASC')
  ->get();

  $role_user = User::where('username', Auth::user()->username)->first();  

  return view('warehouse_new.report.print_kanban', array(
    'title' => $title,
    'title_jp' => $title_jp,
    'gmcs' => $gmcs,
    'loc' => $loc,
    'loc1' => $loc1,
    'sloc_name' => $sloc_name,
    'sloc_name1' => $sloc_name1,
    'role_user' => $role_user

  ))->with('page', 'Design Kanban Material')->with('head', 'Design Kanban Material');
}

public function fetch_list_material(Request $request){
 try {
  $no_gmc = '';
  if($request->get('gmc') != null){
    $gmc =  $request->get('gmc');

    for ($i=0; $i < count($gmc); $i++) {
      $no_gmc = $no_gmc."'".$gmc[$i]."'";
      if($i != (count($gmc)-1)){
        $no_gmc = $no_gmc.',';
      }
    }

    $no_gmc = "gmc_material IN (".$no_gmc.")";

  }

  $locs = '';
  if($request->get('loc') != null){
    $loc =  $request->get('loc');

    for ($i=0; $i < count($loc); $i++) {
      $locs = $locs."'".$loc[$i]."'";
      if($i != (count($loc)-1)){
        $locs = $locs.',';
      }
    }

    $locs = "rcvg_sloc IN (".$locs.")";

  }
  $sloc_names = '';
  if($request->get('sloc_name') != null){
    $sloc_name =  $request->get('sloc_name');

    for ($i=0; $i < count($sloc_name); $i++) {
      $sloc_names = $sloc_names."'".$sloc_name[$i]."'";
      if($i != (count($sloc_name)-1)){
        $sloc_names = $sloc_names.',';
      }
    }

    $sloc_names = "sloc_name IN (".$sloc_names.")";

  }


  $condition = '';
  $and = false;
  if($no_gmc != '' || $locs != '' || $sloc_names != ''){
    $condition = 'WHERE';
  }

  if($no_gmc != ''){
    $and = true;
    $condition = $condition. ' ' .$no_gmc;
  }

  if($locs != ''){
    if($and){
      $condition =  $condition.' OR ';
    }
    $condition = $condition. ' ' .$locs ;
  }


  if($sloc_names != ''){
    if($and){
      $condition =  $condition.' OR ';
    }
    $condition = $condition. ' ' .$sloc_names;
  }

  if($sloc_names != '' AND $locs != '' ){
   $and = true;
   $condition =  ' WHERE '.$locs .' AND '. ' ' .$sloc_names;
 }

 $print_mt = db::select("SELECT
  id,
  barcode,
  gmc_material,
  description,uom,lot,rcvg_sloc,no_hako,keterangan,sloc_name
  FROM
  material_hako_kanbans
  ".$condition."");

 $role_users = User::where('username', Auth::user()->username)->first();  

 if (count($print_mt) == 0) {
   $response = array(
    'status' => false
  );
 }else{
   $response = array(
    'status' => true,
    'print_mt' => $print_mt,
    'role_users' => $role_users,
    'role' => Auth::user()->role_code
  );
 }


 return Response::json($response);
} catch (\Exception $e) {
 $response = array(
  'status' => false,
  'message' => $e->getMessage(),
);

 return Response::json($response);
}
}
public function reprintIdKanban($id){
  $ids = explode(",",$id);


  $whereID = '';
  $list_id = array();
  for ($i=0; $i < count($ids); $i++) {
    array_push($list_id, $ids[$i]); 
    $whereID = $whereID."'".$ids[$i]."'";
    if($i != (count($ids)-1)){
      $whereID = $whereID.',';
    }
  }
  DB::connection()->enableQueryLog();

  $lists = db::select("SELECT
    id,
    barcode,
    gmc_material,
    description,
    lot,
    buyer,
    no_hako,
    sloc_name,
    rcvg_sloc,
    updated_at 
    FROM
    material_hako_kanbans 
    WHERE
    id IN (
      ".$whereID.")
      ORDER BY gmc_material ASC
      ");

    $update = MaterialHakoKanban::whereIn('id', $list_id)->update(['print' => 1,
      'created_by' => Auth::user()->username]);

    $pdf = \App::make('dompdf.wrapper');
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper('A4', 'potrait');
    $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
    $pdf->loadView('warehouse_new.report.printout_kanban_material', array(
      'lists' => $lists,
      'query' => DB::getQueryLog()
    ));
    return $pdf->stream("Kanban_".$id.".pdf");

  }

  public function fetchDetailHistory(Request $request){
    $user = Auth::user()->username;
    try {
     $detail_request_history = db::select('SELECT
      id,
      kode_request,
      lot,
      gmc,sloc_name,
      description,
      uom,
      no_hako,
      qty_req,
      qty_kirim,lokasi_kirim,remark 
      FROM
      warehouse_logs
      ORDER BY
      id ASC');

     $response = array(
      'status' => true,
      'detail_request_history' => $detail_request_history
    );
     return Response::json($response);
   }catch (\Exception $e) {
     $response = array(
      'status' => false,
      'message' => $e->getMessage(),
    );
     return Response::json($response);
   }
 }

 public function indexMonitoringOperator(){
  $title = "Display Joblist operators";
  $title_jp = "";

  return view('warehouse_new.index_joblist_monitoring', array(
    'title' => $title,
    'title_jp' => $title_jp
  ))->with('head', 'Warehouse')->with('page', 'Display Joblist operators');
}

public function indexMonitoringOperatorImport(){
  $title = "Display Joblist Operators Import";
  $title_jp = "";

  return view('warehouse_new.index_joblist_monitoring_import', array(
    'title' => $title,
    'title_jp' => $title_jp
  ))->with('head', 'Warehouse')->with('page', 'Display Joblist Operators Import');
}


public function fetchRqstJob(Request $request)
{
  try {
   $op_name = Auth::user()->username;
   $tanggal = date('Y-m-d');
   $kode_request = $request->get('kode_request');
   $kode_request1 = $request->get('kode_request1');

   $kon = "";
  


   $date_from = $request->get('date_from');
   $date_to = $request->get('date_to');
   if ($date_from == "") {
     if ($date_to == "") {
      $first = "DATE(NOW())";
      $last = "DATE(NOW())";
      $date = date('Y-m-d');
    }else{
      $first = "DATE(NOW())";
      $last = "'".$date_to."'";
      $date = date('Y-m-d');
      $monthTitle = date("d M Y", strtotime($date)).' to '.date("d M Y", strtotime($date_to));
    }
  }else{
   if ($date_to == "") {
    $first = "'".$date_from."'";
    $last = "DATE(NOW())";
    $date = date('Y-m-d');
    $monthTitle = date("d M Y", strtotime($date_from)).' to '.date("d M Y", strtotime($date));
  }else{
    $first = "'".$date_from."'";
    $last = "'".$date_to."'";
    $monthTitle = date("d M Y", strtotime($date_from)).' to '.date("d M Y", strtotime($date_to));
  }
}
$job = DB::select('SELECT
  ss.tanggal,
  es.name,
  es.department,
  ss.kode_request,
  ss.status_aktual,
  ss.status,
  COUNT( ss.kode_request ) AS total
  FROM
  warehouse_pelayanans AS ss
  LEFT JOIN employee_syncs AS es ON ss.pic_produksi = es.employee_id
  LEFT JOIN warehouse_pelayanan_logs AS pl ON ss.kode_request = pl.kode_request 
  WHERE
  ss.status = "kurang" and ss.status_aktual = "1"
  GROUP BY
  ss.kode_request,
  ss.status_aktual,
  ss.tanggal,
  es.name,
  es.department,
  ss.status');

$mt_plus = DB::select('SELECT
  ss.tanggal,
  es.name,
  es.department,
  ss.kode_request,
  ss.status_aktual,
  ss.status,
  COUNT( ss.kode_request ) AS total
  FROM
  warehouse_pelayanans AS ss
  LEFT JOIN employee_syncs AS es ON ss.pic_produksi = es.employee_id
  LEFT JOIN warehouse_pelayanan_logs AS pl ON ss.kode_request = pl.kode_request 
  WHERE
  ss.status = "plus" and ss.status_aktual = "2"
  GROUP BY
  ss.kode_request,
  ss.status_aktual,
  ss.tanggal,
  es.name,
  es.department,
  ss.status');

$list_mt_kurang = DB::SELECT('SELECT
  ss.tanggal,
  ss.id,
  es.name,
  ss.kode_request,
  ss.gmc,
  ss.description,
  ss.lot,
  ss.quantity_request
  FROM
  warehouse_pelayanans AS ss
  LEFT JOIN employee_syncs AS es ON ss.pic_pelayanan = es.employee_id
  where kode_request = "'.$kode_request.'" and status = "kurang" and ss.status_aktual = "1"');

// $list_check = DB::SELECT('SELECT
//   ss.tanggal,
//   ss.id,
//   SUBSTR(es.`name`,1,(LOCATE(" ",es.`name`))) AS name,
//   ss.kode_request,
//   ss.gmc,
//   ss.description,
//   ss.lot,
//   ss.quantity_request,
//   ss.quantity_check,
//   ss.no_hako
//   FROM
//   warehouse_pelayanans AS ss
//   LEFT JOIN employee_syncs AS es ON ss.pic_pelayanan = es.employee_id
//   where kode_request = "'.$kode_request1.'" and status_aktual = "pengecekan material"');

$list_check = WarehousePelayanan::where('warehouse_pelayanans.kode_request', '=', $kode_request1)
->where('warehouse_pelayanans.status_aktual', '=', 'pengecekan material')->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'warehouse_pelayanans.pic_pelayanan')
->select('warehouse_pelayanans.id', 'warehouse_pelayanans.id', 'warehouse_pelayanans.tanggal', 'warehouse_pelayanans.kode_request', 'warehouse_pelayanans.gmc', 'warehouse_pelayanans.description','warehouse_pelayanans.lot','warehouse_pelayanans.quantity_request','warehouse_pelayanans.quantity_check','warehouse_pelayanans.no_hako', 'employee_syncs.name')
->get();    


$list_mt_plus = DB::SELECT('SELECT
  ss.tanggal,
  ss.id,
  es.name,
  ss.kode_request,
  ss.gmc,
  ss.description,
  ss.lot,
  ss.quantity_request
  FROM
  warehouse_pelayanans AS ss
  LEFT JOIN employee_syncs AS es ON ss.pic_pelayanan = es.employee_id
  where kode_request = "'.$kode_request.'" and status = "plus" and ss.status_aktual = "2"');

$response = array(
  'status' => true,
  'list_request' => $job,
  'list_request_plus' => $mt_plus,
  'list_mt_kurang' => $list_mt_kurang,
  'list_mt_plus' => $list_mt_plus,
  'list_check' => $list_check
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

public function deleteMaterial(Request $request)
{
  try
  {
    $master_item = WarehousePelayanan::find($request->get('id'));

    $wh_log = new WarehouseLog([
      'tanggal' => date("Y-m-d"),
      'lokasi_kirim' => $master_item->area,
      'sloc_name' => $master_item->sloc_name,
      'loc' => $master_item->loc,
      'kode_request' => $master_item->kode_request,
      'gmc' => $master_item->gmc,
      'description' => $master_item->description,
      'uom' => $master_item->uom,
      'lot' => $master_item->lot,
      'no_hako' => $master_item->no_hako,
      'qty_req' => $master_item->qty_req,
      'qty_kirim' => $master_item->quantity_check,
      'pic_request' => $master_item->pic_produksi,
      'pic_pelayanan' => $master_item->pic_pelayanan,
      'pic_pengantaran' => $master_item->employee_id_pengantaran,
      'created_at' => date("Y-m-d H:i:s"),
      'updated_at' => date("Y-m-d H:i:s"),
      'created_by' => Auth::id(),
      'remark' => 'canceled'
    ]);
    $wh_log->save();

    $delete_ins = DB::table('warehouse_pelayanan_ins')
    ->where('kode_request', '=', $master_item->kode_request)->where('no_hako', '=', $master_item->no_hako)->where('gmc', '=', $master_item->gmc)
    ->delete();

    $delete = DB::table('warehouse_pelayanans')
    ->where('id', '=', $request->get('id'))
    ->delete();

    $response = array(
      'status' => true,
    );

    return Response::json($response);

  }
  catch(QueryException $e)
  {
    $response = array(
      'status' => false,
    );

    return Response::json($response);

  }
}


public function postEditMOD(Request $request)
{
  $id = $request->get('id');
  $kode_request = $request->get('kode_request');
  $quantitychecks = $request->get('quantitychecks');
  $stat = [];
  $id_user = Auth::user()->username;
  $ids = Auth::id();

  for ($i=0; $i < count($id) ; $i++) {

    if ($quantitychecks[$i] == 0) {

      $hako = WarehousePelayanan::where('kode_request', '=',  $kode_request[$i])->where('id', '=', $id[$i])->get();

      $warehouset = WarehousePelayanan::where('kode_request','=',$hako[0]->kode_request)->where('no_hako','=',$hako[0]->no_hako)->where('status_aktual','=',"kurang")->update([
        'status' => null,
        'status_aktual' => "finish"      
      ]);

      // dd($warehouset);
      $delete = DB::table('warehouse_pelayanans')
      ->where('id', '=', $request->get('id')[$i])
      ->delete();


    }else{
      $cek_mt = WarehousePelayanan::where('kode_request', '=', $request->get('kode_request')[$i])->where('status', '=', "kurang")->where('status_aktual', '=', "kurang")->get();
      $cek_mt_kurang = WarehousePelayanan::where('id', '=', $request->get('id')[$i])->where('status', '=', "kurang")->where('status_aktual', '=', 1)->get();

      $warehousetambah = WarehousePelayanan::where('id','=',$request->get('id')[$i])->where('status','=',"kurang")->update([
        'status' => null,
        'status_aktual' => "proses1",
        'quantity_request' =>  $quantitychecks[$i],
        'quantity_check' =>  $quantitychecks[$i],
        'status_mt' => 1
      ]);
      $update_remark = DB::table('warehouse_pelayanan_ins')
      ->where('kode_request', '=', $kode_request)          
      ->update([
        'remark' => "open"
      ]); 
    }
  }

  $checkstatus1 = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanan_logs` where kode_request = "'.$kode_request[0].'"');  
  $checkAll = db::select('SELECT id,kode_request,status FROM `warehouse_pelayanans` where kode_request = "'.$kode_request[0].'" and status = "plus" || kode_request = "'.$kode_request[0].'" and status = "kurang"');


  if (!empty($checkstatus1)) {
    $st = 1;

    foreach ($stat as $key) {
      if ($key != 'finish') {
        $st = 0;
      }
    }

    if (count($checkAll) <= 0) {

      if ($st == 1) {
       $update_pl = WarehousePelayananLog::where('kode_request','=',$kode_request)->update([
         'status_aktual' => $checkstatus1[0]->status_aktual.","."pengecekan material".","."finish"
       ]);
       $update_end = DB::table('warehouse_pelayanan_ins')
       ->where('kode_request', '=', $kode_request)          
       ->update([
        'remark' => "close"
      ]);

       $update_completion2 = db::select('SELECT
        tanggal,
        kode_request,
        gmc,
        description,
        SUM( quantity_check ) AS quantity_check,
        loc,
        sloc_name 
        FROM
        `warehouse_pelayanans` 
        WHERE
        kode_request = "'.$request->get('kode_request')[0].'" 
        GROUP BY
        gmc,
        description,
        tanggal,
        kode_request,loc,
        sloc_name');

       for ($j=0; $j < count($update_completion2); $j++) { 

        $create_completion2 = new WarehouseCompletionRequest([
         'date_request' => $update_completion2[$j]->tanggal,
         'kode_request' => $update_completion2[$j]->kode_request,
         'gmc' => $update_completion2[$j]->gmc,
         'description' => $update_completion2[$j]->description,
         'quantity_total' => $update_completion2[$j]->quantity_check,
         'loc' => $update_completion2[$j]->loc,
         'sloc_name' => $update_completion2[$j]->sloc_name,
         'created_by' => $id_user 
       ]);
        $create_completion2->save();

        $transaction_transfer = new TransactionTransfer([
          'plant' => '8190',
          'serial_number' => $update_completion2[$j]->loc.$update_completion2[$j]->kode_request,
          'material_number' => $update_completion2[$j]->gmc,
          'issue_plant' => '8190',
          'issue_location' => 'MSTK',
          'receive_plant' => '8190',
          'receive_location' => $update_completion2[$j]->loc,
          'transaction_code' => 'MB1B',
          'movement_type' => '9I3',
          'quantity' => $update_completion2[$j]->quantity_check,
          'reference_file' => 'directly_executed_on_sap',
          'created_by' => Auth::id()

        ]);
        $transaction_transfer->save();
      }


      $materials = WarehousePelayanan::where('kode_request', '=', $kode_request)->get();
      for ($i=0; $i < count($materials) ; $i++) { 
        $wh_log = new WarehouseLog([
          'tanggal' => date("Y-m-d"),
          'lokasi_kirim' => $materials[$i]->area,
          'sloc_name' => $materials[$i]->sloc_name,
          'loc' => $materials[$i]->loc,
          'kode_request' => $materials[$i]->kode_request,
          'gmc' => $materials[$i]->gmc,
          'description' => $materials[$i]->description,
          'uom' => $materials[$i]->uom,
          'lot' => $materials[$i]->lot,
          'no_hako' => $materials[$i]->no_hako,
          'qty_req' => $materials[$i]->qty_req,
          'qty_kirim' => $materials[$i]->quantity_check,
          'pic_request' => $materials[$i]->pic_produksi,
          'pic_pelayanan' => $materials[$i]->pic_pelayanan,
          'pic_pengantaran' => $materials[$i]->employee_id_pengantaran,
          'created_at' => date("Y-m-d H:i:s"),
          'updated_at' => date("Y-m-d H:i:s"),
          'created_by' => Auth::id(),
          'remark' => 'finish'
        ]);
        $wh_log->save();
      }
      $delete_rqst = db::table('warehouse_pelayanans')
      ->where('kode_request', '=', $kode_request)
      ->delete(); 

    }
  }
}


$response = array(
  'status' => true

);
return Response::json($response);
}


public function postMtPlus(Request $request)
{
  $id = ($request->get('id'));
  $kode_request = $request->get('kode_request');
  $quantitychecks = $request->get('quantitychecks');
  $plus = [];
  $id_user = Auth::user()->username;
  $ids = Auth::id();



  for ($i=0; $i < count($id) ; $i++) {

    if ($quantitychecks[$i] == 0) {
      $hako = WarehousePelayanan::where('kode_request', '=',  $kode_request[$i])->where('id', '=', $id[$i])->get();

      $cek_mt2 = WarehousePelayanan::where('kode_request', '=', $kode_request[$i])->where('status', '=', "plus")->where('status_aktual', '=', "plus")->where('no_hako', '=', $hako[0]->no_hako )->get();
      // dd($cek_mt2[0]->quantity_check,$hako[0]->quantity_check);

      // dd($cek_mt2[0]->quantity_check-$hako[0]->quantity_check);

      $warehouset = WarehousePelayanan::where('kode_request','=',$hako[0]->kode_request)->where('no_hako','=',$hako[0]->no_hako)->where('status_aktual','=',"plus")->where('gmc','=',$hako[0]->gmc)->update([
        'status' => null,
        'quantity_request' => $cek_mt2[0]->quantity_check - $hako[0]->quantity_check,
        'quantity_check' => $cek_mt2[0]->quantity_check - $hako[0]->quantity_check,
        'status_aktual' => "finish"      
      ]);

      $check_status = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanan_logs` where kode_request = "'.$kode_request[0].'"');

      // dd($check_status);
      array_push($plus, $check_status[0]->status_aktual);

      $delete = DB::table('warehouse_pelayanans')
      ->where('id', '=', $id[$i])
      ->delete();

    }else{
      $cek_mt_kurang = WarehousePelayanan::where('id', '=', $request->get('id')[$i])->where('status', '=', "plus")->where('status_aktual', '=', 2)->get();

      $cek_mt = WarehousePelayanan::where('kode_request', '=', $request->get('kode_request')[$i])->where('status', '=', "plus")->where('status_aktual', '=', "plus")->where('no_hako', '=', $cek_mt_kurang[0]->no_hako )->get();

      $check_status = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanan_logs` where kode_request = "'.$kode_request[0].'"');

      // dd($check_status);
      array_push($plus, $check_status[0]->status_aktual);

      // dd($cek_mt[0]->id);

      $warehousetambah = WarehousePelayanan::where('id','=',$cek_mt[0]->id)->where('status','=',"plus")->update([
        'status' => null,
        'quantity_request' => $cek_mt[0]->qty_req + $cek_mt_kurang[0]->quantity_check,
        'quantity_check' => $cek_mt[0]->qty_req + $cek_mt_kurang[0]->quantity_check,
        'status_aktual' => "finish"

      ]);
      // dd($request->get('id')[$i]); 
      $delete = DB::table('warehouse_pelayanans')
      ->where('id', '=', $request->get('id')[$i])
      ->delete();
    }

    $checkstatus = db::select('SELECT id,kode_request,status_aktual FROM `warehouse_pelayanan_logs` where kode_request = "'.$kode_request[0].'"');
    $checkAll = db::select('SELECT id,kode_request,status FROM `warehouse_pelayanans` where kode_request = "'.$kode_request[0].'" and status = "plus" || kode_request = "'.$kode_request[0].'" and status = "kurang"');

    // dd($checkAll);

    if (!empty($plus)) {
      $st = 1;
      foreach ($plus as $key) {
        if ($key != 'finish') {
          $st = 1;
        }else{
          $st = 0;
        }
      }
      if (count($checkAll) <= 0) {


        if ($st == 1) {
         $update_pl = WarehousePelayananLog::where('kode_request','=',$kode_request)->update([
           'status_aktual' => $checkstatus[0]->status_aktual.","."pengecekan material".","."finish"
         ]);
         $update_end = DB::table('warehouse_pelayanan_ins')
         ->where('kode_request', '=', $kode_request)          
         ->update([
          'remark' => "close"
        ]);


         $update_completion3 = db::select('SELECT
          tanggal,
          kode_request,
          gmc,
          description,
          SUM( quantity_check ) AS quantity_check,
          loc,
          sloc_name 
          FROM
          `warehouse_pelayanans` 
          WHERE
          kode_request = "'.$kode_request[0].'" 
          GROUP BY
          gmc,
          description,
          tanggal,
          kode_request,loc,
          sloc_name');

         for ($j=0; $j < count($update_completion3); $j++) { 

          $create_completion = new WarehouseCompletionRequest([
           'date_request' => $update_completion3[$j]->tanggal,
           'kode_request' => $update_completion3[$j]->kode_request,
           'gmc' => $update_completion3[$j]->gmc,
           'description' => $update_completion3[$j]->description,
           'quantity_total' => $update_completion3[$j]->quantity_check,
           'loc' => $update_completion3[$j]->loc,
           'sloc_name' => $update_completion3[$j]->sloc_name,
           'created_by' => $id_user 
         ]);
          $create_completion->save();

          $transaction_transfer = new TransactionTransfer([
            'plant' => '8190',
            'serial_number' => $update_completion3[$j]->loc.$update_completion3[$j]->kode_request,
            'material_number' => $update_completion3[$j]->gmc,
            'issue_plant' => '8190',
            'issue_location' => 'MSTK',
            'receive_plant' => '8190',
            'receive_location' => $update_completion3[$j]->loc,
            'transaction_code' => 'MB1B',
            'movement_type' => '9I3',
            'quantity' => $update_completion3[$j]->quantity_check,
            'reference_file' => 'directly_executed_on_sap',
            'created_by' => Auth::id()

          ]);
          $transaction_transfer->save();
        }

        $materials = WarehousePelayanan::where('kode_request', '=', $kode_request)->get();
        for ($i=0; $i < count($materials) ; $i++) { 
          $wh_log = new WarehouseLog([
           'lokasi_kirim' => $materials[$i]->area,
           'tanggal' => date("Y-m-d"),
           'sloc_name' => $materials[$i]->sloc_name,
           'loc' => $materials[$i]->loc,
           'kode_request' => $materials[$i]->kode_request,
           'gmc' => $materials[$i]->gmc,
           'description' => $materials[$i]->description,
           'uom' => $materials[$i]->uom,
           'lot' => $materials[$i]->lot,
           'no_hako' => $materials[$i]->no_hako,
           'qty_req' => $materials[$i]->qty_req,
           'qty_kirim' => $materials[$i]->quantity_check,
           'pic_request' => $materials[$i]->pic_produksi,
           'pic_pelayanan' => $materials[$i]->pic_pelayanan,
           'pic_pengantaran' => $materials[$i]->employee_id_pengantaran,
           'created_at' => date("Y-m-d H:i:s"),
           'updated_at' => date("Y-m-d H:i:s"),
           'created_by' => Auth::id(),
           'remark' => 'finish'
         ]);
          $wh_log->save();
        }
        $delete_rqst = db::table('warehouse_pelayanans')
        ->where('kode_request', '=', $kode_request)
        ->delete(); 


      }
    }
  }
}

$response = array(
  'status' => true

);
return Response::json($response);
}

public function deleteKanbanPrd(Request $request){
  try{
    $delete_rqst = db::table('material_hako_kanbans')
    ->where('id', '=', $request->get('ids'))
    ->delete(); 
    
    $response = array(
      'status' => true,
      'message' => 'Delete successful',   
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

public function updateKanbanMaterial(Request $request){
  try{
    $employee_groups = DB::table('material_hako_kanbans')
    ->where('id', '=', $request->get('id'))           
    ->update([
      'barcode' => $request->get('barcode'),
      'gmc_material' => $request->get('gmcs'),
      'description' => $request->get('description'),
      'uom' => $request->get('uom'),
      'rcvg_sloc' => $request->get('sloc'),
      'lot' => $request->get('lot'),
      'no_hako' => $request->get('no_hako'),
      'keterangan' => $request->get('keterangan'),
      'sloc_name' => $request->get('sloc_name1')
    ]);

    $response = array(
      'status' => true,
      'message' => 'Update Successful',   
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

public function createKanban(Request $request){
  try{
    $kanban = new MaterialHakoKanban([
     'barcode' => $request->get('barcode'),
     'gmc_material' => $request->get('gmcs'),
     'description' => $request->get('description'),
     'uom' => $request->get('uoms'),
     'rcvg_sloc' => $request->get('sloc'),
     'lot' => $request->get('lot'),
     'no_hako' => $request->get('no_hako'),
     'keterangan' => $request->get('keterangan'),
     'sloc_name' => $request->get('sloc_name1'),
     'created_by' => Auth::id()
   ]);

    $kanban->save();

    $response = array(
      'status' => true,
      'message' => 'Create Kanban Successfully'
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

public function indexCheckKirim(Request $request){
  $title = 'Check Kanban Material Kirim';
  $title_jp = '??';

  return view('warehouse_new.check_material_kirim', array(
    'title' => $title,
    'title_jp' => $title_jp
  ))->with('page', 'Check Kanban Material Kiriml')->with('head', 'Check Kanban Material Kirim');
}

public function importKanban(Request $request)
{
  if($request->hasFile('file')) {
   try{
     $id_user = Auth::id();

     $file = $request->file('file');
     $file_name = 'KanbanMT_'. date("Y-m-d") .'.'.$file->getClientOriginalExtension();
     $file->move(public_path('data_file/kanban_MT/'), $file_name);
     $excel = public_path('data_file/kanban_MT/') . $file_name;

     $rows = Excel::load($excel, function($reader) {
      $reader->noHeading();
      $reader->skipRows(1);
    })->get();

     $rows = $rows->toArray();

     $barcode = [];
     $gmc = [];
     $description = [];
     $uom = [];
     $lot = [];
     $rcvg_sloc = [];
     $sloc_name = [];
     $no_hako = [];
     $keterangan = [];
     $buyer = [];
     $jenis = [];

     for ($i=0; $i < count($rows); $i++) {

       $ivms = MaterialHakoKanban::create([
         'barcode' => $rows[$i][0],      
         'gmc_material' => $rows[$i][1],      
         'description' => $rows[$i][4],
         'uom' => $rows[$i][5],
         'lot' => $rows[$i][6],
         'rcvg_sloc' => $rows[$i][7],
         'sloc_name' => $rows[$i][8],
         'no_hako' => $rows[$i][9],  
         'keterangan' => $rows[$i][10],
         'buyer' => $rows[$i][12],
         'jenis' => $rows[$i][13],
         'created_by' => Auth::id(),
       ]);
     }

     $response = array(
      'status' => true,
      'message' => 'Upload Berhasil',
    );
     return Response::json($response);
   }catch (\Exception $e) {
    $response = array(
      'error' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}else{
  $response = array(
    'status' => false,
    'message' => 'Upload failed, File not found',
  );
  return Response::json($response);
}

}


public function approvalReason(Request $request)
{

  if ($request->get('stat2') == 'reject') {
   $approval_reject = WarehousePelayanan::where('kode_request', $request->get('kode_request'))->first();

   if ($approval_reject->status_approve == "rejected") {
       # code...
   } else{


     $employee_id = Auth::user()->username;
     $asset_upd = WarehousePelayanan::where('kode_request', '=', $request->get('kode_request'))
     ->update([
      'pic_reject' => $employee_id,
      'leader_app' => date('Y-m-d H:i:s'),
      'status_approve' => 'rejected',
      'reason_urgent' => $request->get('comment')
    ]);

     $get_email = db::select('select email from send_emails where remark =
      (select distinct section from warehouse_pelayanans 
      left join employee_syncs on warehouse_pelayanans.pic_produksi = employee_syncs.employee_id
      where kode_request = "'.$request->get('kode_request').'")');

     $rqst_kanban = WarehousePelayanan::select('warehouse_pelayanans.kode_request','warehouse_pelayanans.gmc','warehouse_pelayanans.description','warehouse_pelayanans.lot','warehouse_pelayanans.uom','warehouse_pelayanans.quantity_request','warehouse_pelayanans.created_at','warehouse_pelayanans.no_hako','warehouse_pelayanans.sloc_name','employee_syncs.name','employee_syncs.department','employee_syncs.section')
     ->join('employee_syncs', 'employee_syncs.employee_id', '=', 'warehouse_pelayanans.pic_produksi')
     ->where('warehouse_pelayanans.kode_request', '=', $request->get('kode_request'))
     ->get();

     $information1 = WarehousePelayanan::select('warehouse_pelayanans.reason_urgent','warehouse_pelayanans.reason_urgent_in','warehouse_pelayanans.pic_produksi','warehouse_pelayanans.kode_request','warehouse_pelayanans.created_at','employee_syncs.name','employee_syncs.department','employee_syncs.section')
     ->join('employee_syncs', 'employee_syncs.employee_id', '=', 'warehouse_pelayanans.pic_produksi')
     ->where('warehouse_pelayanans.kode_request', '=', $request->get('kode_request'))
     ->distinct()
     ->get();

     $data = [
       'rqst_kanban' => $rqst_kanban,
       'information' => $information1,
       'status' => 'REJECTED'
     ];
     $mail_to_wh = [];
     array_push($mail_to_wh, $get_email[0]->email);

     // array_push($mail_to_wh, 'nasiqul.ibat@music.yamaha.com');

   // $mailto = db::select('SELECT email FROM users where username = "'.$new_asset->created_by.'"');

     Mail::to($mail_to_wh)->bcc(['lukman.hakim.saputra@music.yamaha.com'])->send(new SendEmail($data, 'request_kanban_mt_urgent'));
   }
 }
}

public function ReportResumeMod(){
  $title = "History Resume Request Material MOD";
  $title_jp = "";

  return view('warehouse_new.report_resume_mod', array(
    'title' => $title,
    'title_jp' => $title_jp
  ))->with('head', 'History Resume Request Material MOD')->with('page', 'History Resume Request Material MOD');
}

public function fetchResumeMod(Request $request)
{
  try {
   $date_from = $request->get('tanggal_from');
   $date_to = $request->get('tanggal_to');
   $now = date('Y-m-d');
   $user = Auth::user()->username;


   if ($date_from == '') {
    if ($date_to == '') {
     $whereDate = 'DATE(date_request)  = "'.$now.'"';
   }else{
     $whereDate = 'DATE(date_request) BETWEEN CONCAT(DATE_FORMAT("'.$date_to.'" - INTERVAL 4 DAY,"%Y-%m-%d")) AND "'.$date_to.'"';
   }
 }else{
  if ($date_to == '') {
   $whereDate = 'DATE(date_request) BETWEEN "'.$date_from.'" AND DATE(NOW())';
 }else{
   $whereDate = 'DATE(date_request) BETWEEN "'.$date_from.'" AND "'.$date_to.'"';
 }
}

// if (Auth::user()->role_code == "MIS" || Auth::user()->role_code == "OP-WH-Exim" ) {
//   $users = "";
// }else{
//   $users = 'AND pic_produksi  = "'.$user.'"';
// }

$list_request = DB::SELECT('SELECT *
  FROM
  warehouse_completion_requests WHERE
  '.$whereDate.'');

$response = array(
  'status' => true,
  'message' => 'Get Data Success',
  'datas' => $list_request
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

public function exportMOD($mod){

  // dd($mod);

  // $time = date('d-m-Y H;i;s');

  // $tanggal = "";

  // if (strlen($request->get('datefrom')) > 0)
  // {
  //   $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
  //   $tanggal = "and tgl_po >= '" . $datefrom . " 00:00:00' ";
  //   if (strlen($request->get('dateto')) > 0)
  //   {
  //     $dateto = date('Y-m-d', strtotime($request->get('dateto')));
  //     $tanggal = $tanggal . "and tgl_po  <= '" . $dateto . " 23:59:59' ";
  //   }
  // }

  $mod_detail = db::select(
    "
    SELECT * FROM warehouse_completion_requests WHERE kode_request = '".$mod."'
    ");

  //       // and acc_purchase_orders.posisi = 'pch' and acc_purchase_orders.`status` = 'not_sap' and no_po_sap is null 

  $data = array(
    'mod_detail' => $mod_detail
  );

  ob_clean();

  Excel::create('MOD '.$mod, function($excel) use ($data){
    $excel->sheet('Location', function($sheet) use ($data) {
      return $sheet->loadView('warehouse_new.report.mod_excel', $data);
    });
  })->export('xlsx');
}


public function fetchCheckMt(Request $request){

  if ($request->get('tanggals')) {
    $tanggal = $request->get('tanggals');
  } else {
    $tanggal = date('Y-m-d');
  }

  $detail_check = db::select('SELECT DISTINCT
    id,no_case,
    number_package,no_invoice,
    package,
    gmc,vendor,tanggal_kedatangan_aktual
    FROM
    warehouse_packinglists 
    WHERE
    status_receive = "wainting" 
    AND tanggal_kedatangan = "'.$tanggal.'" 
    GROUP BY
    no_case
    ');

  $response = array(
    'status' => true,
    'detail_material_import' => $detail_check 
  );
  return Response::json($response);
}

public function SaveJoblistLain(Request $request)
{
  try {

    $user = Auth::user()->username;
    $tot = 0;

    $get_pk_lain = db::select('SELECT
      id,
      employee_id,
      STATUS,
      end_job 
      FROM
      `warehouse_time_operator_logs` 
      WHERE
      status = "Lain" AND end_job IS NULL AND employee_id = "'.$user.'"  || status = "check" AND end_job IS NULL AND employee_id = "'.$user.'"  || status = "Pengantaran" AND end_job IS NULL AND employee_id = "'.$user.'"  
      ORDER BY
      employee_id DESC 
      LIMIT 1');

    if (count($get_pk_lain) > 0) {  
      $response = array(
        'status' => false,
        'message' => 'Operator sedang bekerja'
      );
      return Response::json($response);
    }else{
      $tot = 1;
    }

    

if ($tot == "1") {
  
    $get_mr = db::select('SELECT
      id,
      employee_id,
      STATUS,
      end_job 
      FROM
      `warehouse_time_operator_logs` 
      WHERE
      employee_id = "'.$user.'" 
      AND end_job IS NULL 
      ORDER BY
      employee_id DESC 
      LIMIT 1');

    if (count($get_mr) > 0) {
      // dd($detail_check[0]->employee_id);
      $update_st = DB::table('warehouse_time_operator_logs')
      ->where('employee_id', '=',  $user)->where('status', '=', "idle")->where('end_job', '=', null)->limit(1)
      ->update([
        'end_job' => date('Y-m-d H:i:s')
      ]);

      $data = new WarehouseTimeOperatorLog([
        'employee_id' => Auth::user()->username,
        'status' => "Lain",
        'joblist' => $request->get('joblist'),
        'start_job' => date("Y-m-d H:i:s")
      ]);
      $data->save();

      $warehouset = WarehouseEmployeeMaster::where('employee_id','=', $user)->update([
        'start_time_status' => date("Y-m-d H:i:s"),
        'status' => "work",
        'status_aktual_pekerjaan' => $request->get('joblist')
      ]);

      $response = array(
        'status' => true,
        'message' => 'Data Berhasil Disimpan'
      );
      return Response::json($response);
    }
    else{
      $response = array(
        'status' => false,
        'message' => 'Operator sedang bekerja'
      );
      return Response::json($response);
    }
  }

  }catch (\Exception $e) {
    $response = array(
      'error' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
  
}

public function FetchJoblistLain(Request $request)
{

  $get_op = Auth::user()->username;
  $date = date("Y-m-d");
  $monthTitle = date("d M Y", strtotime($date));

  
  $check = db::select('SELECT
    wp.id,
    wp.employee_id,
    wp.`status`,
    wp.joblist,
    wp.start_job,
    es.`name` 
    FROM
    warehouse_time_operator_logs AS wp
    LEFT JOIN users AS es ON wp.employee_id = es.username 
    WHERE
    wp.STATUS = "Lain" 
    AND wp.employee_id = "'.$get_op.'" 
    AND wp.end_job IS NULL 
    ORDER BY
    wp.employee_id DESC 
    LIMIT 1
    ');
  
  $response = array(
    'status' => true,
    'message' => 'Data Berhasil Disimpan',
    'check' => $check,
    'monthTitle' => $monthTitle
  );
  return Response::json($response);
}

public function fetchCreateJoblistLain(Request $request)
{

  $tanggal = date('2021-12-27');
  $tgl = date('2021-12-27',strtotime($tanggal));
  $get_op = Auth::user()->username;
  if(strlen($request->get('time')) > 0){
    $tgl = date('Y-m-d',strtotime($request->get('time')));
    $jam = date('Y-m-d H:i:s',strtotime($request->get('time').date('H:i:s')));
    if ($jam > date('Y-m-d',strtotime($tgl)).' 00:00:01' && $jam < date('Y-m-d',strtotime($tgl)).' 02:00:00' && $tgl == date('Y-m-d',strtotime($tgl))) {
      $nextday =  date('Y-m-d', strtotime($tgl));
      $yesterday = date('Y-m-d',strtotime($tgl." -1 days"));
    }else{
      $nextday =  date('Y-m-d', strtotime($tgl . " +1 days"));
      $yesterday = date('Y-m-d',strtotime($tgl));
    }
  }else{
    $tgl = date("Y-m-d");
    $jam = date('Y-m-d H:i:s');
    if ($jam > date('Y-m-d',strtotime($tgl)).' 00:00:01' && $jam < date('Y-m-d',strtotime($tgl)).' 02:00:00') {
      $nextday = date('Y-m-d');
      $yesterday = date('Y-m-d',strtotime("-1 days"));
    }else{
      $nextday = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
      $yesterday = date('Y-m-d');
    }
  }
  
  $joblist_lain = db::select("SELECT
    ss.joblist,
    es.name,
    ss.start_job,
    ss.end_job
    FROM
    warehouse_time_operator_logs AS ss
    LEFT JOIN employee_syncs AS es ON ss.employee_id = es.employee_id
    where ss.`status` = 'Lain' and ss.end_job is not null and ss.employee_id = '".$get_op."' and ss.start_job >= '".$yesterday." 06:00:00' && ss.start_job <= '".$nextday." 02:00:00'
    ");
  
  $response = array(
    'status' => true,
    'message' => 'Data Berhasil Disimpan',
    'joblist_lain' => $joblist_lain,
  );
  return Response::json($response);
}

public function UpdateMaterial(Request $request)
{
  $warehouset = WarehouseLog::where('kode_request','=',$request->get('kode_request'))->update([
    'status' => 'sudah dicheck',
  ]);

  $response = array(
    'status' => true,
    'message' => 'Data Berhasil Disimpan'
  );
  return Response::json($response);
}

public function UpdateOperatorJobLain(Request $request)
{
  try {
    $warehouset = WarehouseTimeOperatorLog::where('employee_id','=',$request->get('employee_id'))->where('joblist','=',$request->get('joblist'))->where('status','=','Lain')->where('end_job','=', null)->update([
      'end_job' => date("Y-m-d H:i:s")
    ]);

    $warehouseop = WarehouseEmployeeMaster::where('employee_id','=',$request->get('employee_id'))->update([
      'start_time_status' => date("Y-m-d H:i:s"),
      'status' => "idle",
      'status_aktual_pekerjaan' => null
    ]);

    $data = new WarehouseTimeOperatorLog([
      'employee_id' => $request->get('employee_id'),
      'status' => "idle",
      'start_job' => date("Y-m-d H:i:s")
    ]);
    $data->save();


    $response = array(
      'status' => true,
      'message' => 'Data Berhasil DiUpdate'
    );
    return Response::json($response);
  }catch (\Exception $e) {
    $response = array(
      'error' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function indexMODDelivery(){
  return view('warehouse_new.report.mod_delivery_check')->with('page', 'MOD Delivery');
}


public function fetchMODDetail(Request $request){

  $operator = db::select("SELECT
    wc.ID,
    tt.id,
    tt.serial_number,
    tt.material_number,
    wc.quantity_total,
    wc.updated_at,
    IFNULL( tt.reference_file, 'belum upload' ) AS status 
    FROM
    warehouse_completion_requests AS wc
    LEFT JOIN transaction_transfers AS tt ON wc.kode_request = SUBSTRING( tt.serial_number, 5 ) 
    WHERE 
    tt.serial_number NOT LIKE 'KD%' AND wc.remark is null
    ");
  $st = "";
  $color = "";

  return DataTables::of($operator)
  ->addColumn('deleteMt', function($operator){
    return '<a href="javascript:void(0)" class="btn btn-sm btn-info" onClick="EditMt(this)" id="' . $operator->id .','.$operator->serial_number.','.$operator->material_number.','.$operator->quantity_total.'"><i class="fa fa-pencil-square-o">Edit</i></a>&nbsp;<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteMt(this)" id="' . $operator->id .','.$operator->serial_number.','.$operator->material_number.','.$operator->quantity_total.'"><i class="fa fa-trash"></i></a>';
    
  })

  ->addColumn('statusMt', function($operator){
    if ($operator->status == "belum upload") {
      $st = "Belum Upload";
      $color = "background-color: #3c8dbc;";

    }else if ($operator->status == "directly_executed_on_sap") {
      $st = "Upload Manual";
      $color = "background-color: #dbdb00;";
    }else{
      $st = "Sudah Upload";
      $color = "background-color: #aee571;";  
    }
    return '
    <span class="label" style="color: black; '.$color.'  border: 1px solid black;">' . $st . '</span>';
  })
  ->rawColumns(['deleteMt' => 'deleteMt','statusMt' => 'statusMt'])
  ->make(true);
}

public function deleteMODDelivery(Request $request)
{
  try {

   $detail = TransactionTransfer::where('id', '=', $request->get('id'))->get();

   for ($i=0; $i < count($detail); $i++) { 

    $transaction_transfer = new TransactionTransfer([
      'plant' => '8190',
      'serial_number' => $detail[$i]->serial_number,
      'material_number' => $detail[$i]->material_number,
      'issue_plant' => '8190',
      'issue_location' => 'MSTK',
      'receive_plant' => '8190',
      'receive_location' => $detail[$i]->receive_location,
      'transaction_code' => 'MB1B',
      'movement_type' => '9I4',
      'quantity' => $detail[$i]->quantity,
      'created_by' => Auth::id()

    ]);
    $transaction_transfer->save();


  }

  $response = array(
    'status' => true,
    'message' => 'Data Berhasil DiUpdate'
  );
  return Response::json($response);
}catch (\Exception $e) {
  $response = array(
    'error' => false,
    'message' => $e->getMessage()
  );
  return Response::json($response);
}
}

public function postMaterialUpdate(Request $request)
{

  $gmc = $request->get('gmc')[0];

  $cek = TransactionTransfer::where('serial_number', '=', $request->get('no_mod')[0])->where('material_number', '=', $request->get('gmc')[0])->get();

  
  $ds = substr($cek[0]->serial_number,-4);
  
  $warehouset = WarehouseCompletionRequest::where('kode_request','=',$ds)->update([
    'remark' => "pernah diupdate"
  ]);
  

  $get_comple = WarehouseCompletionRequest::where('kode_request', '=', $ds)->where('gmc', '=', $request->get('gmc')[0])->where('remark', '=','pernah diupdate')->limit(1)->get();


  for ($i=0; $i < count($cek); $i++) {

    $create_completion = new WarehouseCompletionRequest([
     'date_request' => date("Y-m-d"),
     'kode_request' => $get_comple[0]->kode_request,
     'gmc' => $get_comple[0]->gmc,
     'description' => $get_comple[0]->description,
     'quantity_total' => $request->get('quantity_update')[0],
     'loc' => $get_comple[0]->loc,
     'sloc_name' => $get_comple[0]->sloc_name,
     'created_by' => Auth::user()->username 
   ]);
    $create_completion->save();  

    $transaction_transfer_delete = new TransactionTransfer([
      'plant' => '8190',
      'serial_number' => $cek[$i]->serial_number,
      'material_number' => $cek[$i]->material_number,
      'issue_plant' => '8190',
      'issue_location' => 'MSTK',
      'receive_plant' => '8190',
      'receive_location' => $cek[$i]->receive_location,
      'transaction_code' => 'MB1B',
      'movement_type' => '9I4',
      'quantity' => $cek[$i]->quantity,
      'reference_file' => 'directly_executed_on_sap',
      'created_by' => Auth::id()
    ]);
    $transaction_transfer_delete->save();

    $transaction_transfer = new TransactionTransfer([
      'plant' => '8190',
      'serial_number' => $cek[$i]->serial_number,
      'material_number' => $cek[$i]->material_number,
      'issue_plant' => '8190',
      'issue_location' => 'MSTK',
      'receive_plant' => '8190',
      'receive_location' => $cek[$i]->receive_location,
      'transaction_code' => 'MB1B',
      'movement_type' => '9I3',
      'quantity' => $request->get('quantity_update')[0],
      'reference_file' => 'directly_executed_on_sap',
      'created_by' => Auth::id()
    ]);
    $transaction_transfer->save();
  }

  $response = array(
    'status' => true,
  );
  return Response::json($response);
}


public function postMaterialDelete(Request $request)
{

  $cek = TransactionTransfer::where('serial_number', '=', $request->get('no_mod')[0])->where('material_number', '=', $request->get('gmc')[0])->get();

  $ds = substr($cek[0]->serial_number,-4);
  
  $warehouset = WarehouseCompletionRequest::where('kode_request','=',$ds)->update([
    'remark' => "delete"
  ]);

  for ($i=0; $i < count($cek); $i++) {
    $transaction_transfer_delete = new TransactionTransfer([
      'plant' => '8190',
      'serial_number' => $cek[$i]->serial_number,
      'material_number' => $cek[$i]->material_number,
      'issue_plant' => '8190',
      'issue_location' => 'MSTK',
      'receive_plant' => '8190',
      'receive_location' => $cek[$i]->receive_location,
      'transaction_code' => 'MB1B',
      'movement_type' => '9I4',
      'quantity' => $cek[$i]->quantity,
      'reference_file' => 'directly_executed_on_sap',
      'created_by' => Auth::id()
    ]);
    $transaction_transfer_delete->save();

  }

  $response = array(
    'status' => true,
  );
  return Response::json($response);
}

public function indexCheckHistory(){
  $title = "History Check Request Material MOD";
  $title_jp = "";

  return view('warehouse_new.history_check_request', array(
    'title' => $title,
    'title_jp' => $title_jp
  ))->with('head', 'History Check Request Material MOD')->with('page', 'History Check Request Material MOD');

}

public function getOperatorJoblist(Request $request)
{
  try {
   $employee_id = $request->get('employee_id');
   $user = Auth::user()->username;
   $date = date("Y-m-d");
   
   $get_op_work = DB::SELECT('SELECT
    tt.employee_id,
    uu.NAME,
    tt.`status`,
    tt.joblist,
    tt.end_job,
    tt.start_job 
    FROM
    warehouse_time_operator_logs AS tt
    LEFT JOIN users AS uu ON tt.employee_id = uu.username 
    WHERE
    tt.employee_id = "'.$employee_id.'" 
    AND DATE_FORMAT( tt.created_at, "%Y-%m-%d" ) = "'.$date.'" 
    AND tt.end_job IS NULL');

   $get_op_history = DB::SELECT('SELECT
    request_desc,
    STATUS,
    start_job,
    end_job,
    CASE
    WHEN request_desc IS NULL THEN
    "0" ELSE count( tt.id ) 
    END AS kode_request_val,
    ur.name 
    FROM
    warehouse_time_operator_logs AS tt
    LEFT JOIN warehouse_pelayanan_ins AS uu ON tt.request_desc = uu.kode_request
    LEFT JOIN users AS ur ON ur.username = tt.employee_id 
    WHERE
    tt.employee_id = "'.$employee_id.'" and DATE_FORMAT( tt.created_at, "%Y-%m-%d" ) = "2022-01-03" 
    GROUP BY
    kode_request,
    STATUS,ur.name, start_job,end_job,request_desc
    ORDER BY
    start_job ASC');

   $response = array(
    'status' => true,
    'message' => 'Get Data Success',
    'get_op_work' => $get_op_work,
    'get_op_history' => $get_op_history
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

public function fetchCheckHistory(Request $request)
{
  try {
   $gmc = $request->get('gmc');
   $user = Auth::user()->username;

   $list_request = DB::SELECT('SELECT
    *
    FROM
    warehouse_completion_requests AS tt
    LEFT JOIN warehouse_pelayanan_ins AS uu ON tt.kode_request = uu.kode_request
    AND tt.gmc = uu.gmc
    WHERE
    tt.gmc = "'.$gmc.'"');

   $response = array(
    'status' => true,
    'message' => 'Get Data Success',
    'datas' => $list_request
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

public function historyGmcInOut(){
  $title = "History GMC IN OUT";
  $title_jp = "";

  $kd_request = WarehousePelayananIn::select('kode_request')->whereNull('warehouse_pelayanan_ins.deleted_at')
    ->distinct()
    ->get();
  $gmcks = WarehousePelayananIn::select('gmc')->whereNull('warehouse_pelayanan_ins.deleted_at')
    ->distinct()
    ->get();

  return view('warehouse_new.report_gmc_inout', array(
    'title' => $title,
    'title_jp' => $title_jp,
    'kd_request' => $kd_request,
    'gmcks' => $gmcks
  ))->with('head', 'History GMC IN OUT')->with('page', 'History GMC IN OUT');
}

public function fetchGmc(Request $request)
{
  try {
   $gmc = $request->get('gmc');
   $kd = $request->get('kd');
   
   $now = date('Y-m-d');
   $user = Auth::user()->username;

   // $get_op = EmployeeSync::where('employee_syncs.employee_id', '=', $user)->first();

// if (Auth::user()->role_code == "OP-WH-Exim" || Auth::user()->role_code == "MIS" || Auth::user()->role_code == "F-SPL" || Auth::user()->role_code == "PC" || Auth::user()->role_code == "L-WH") { 
//   $users = "";
// }else{
//   $users = 'AND (select distinct section from employee_syncs where employee_id = ss.pic_request) = "'.$get_op->section.'"';
// }

   if ($kd == null && $gmc == null) {
     $st = 'AND kode_request = "" AND gmc = ""';
     $tk = 'tt.kode_request = "" AND tt.gmc = ""';
     $kt = 'ins.gmc = tt.gmc';

   
   }else if ($kd != null && $gmc == null) {
     $st = 'AND kode_request = "'.$kd.'"';
     $tk = 'tt.kode_request = "'.$kd.'"';
     $kt = 'ins.gmc = tt.gmc';



   }else if ($kd == null && $gmc != null) {
     $st = 'AND gmc = "'.$gmc.'"';
     $tk = 'tt.gmc = "'.$gmc.'"';
     $kt = 'ins.kode_request = tt.kode_request';


   }else if ($kd != null && $gmc != null) {
      $st = 'AND kode_request = "'.$kd.'" AND gmc = "'.$gmc.'"';
     $tk = 'tt.kode_request = "'.$kd.'" AND tt.gmc = "'.$gmc.'"';
     $kt = 'ins.gmc = tt.gmc';

   }


   $list_request = DB::SELECT('SELECT
    tt.kode_request,
    tt.gmc,
    tt.no_hako,
    tt.tanggal,
    tt.qty_kirim,
    ins.kode_request AS kd1,
    ins.gmc AS gmc1,
    ins.no_hako AS no_hako1,
    ins.tanggal AS tanggals,
    ins.quantity_request
    FROM
    ( SELECT kode_request, gmc, no_hako, remark, tanggal,quantity_request FROM warehouse_pelayanan_ins WHERE remark = "close"  '.$st.') AS ins
      LEFT JOIN warehouse_logs AS tt ON '.$kt.'
      AND ins.no_hako = tt.no_hako 
      WHERE
      '.$tk.' UNION ALL
      SELECT NULL
      ,
      NULL,
      NULL,
      NULL,
      NULL,
      ins.kode_request,
      ins.gmc,
      ins.no_hako,
      ins.tanggal,
      ins.quantity_request 
      FROM
      ( SELECT kode_request, gmc, no_hako, remark, tanggal,quantity_request FROM warehouse_pelayanan_ins WHERE remark = "open" '.$st.' ) AS ins');

    $response = array(
      'status' => true,
      'message' => 'Get Data Success',
      'datas' => $list_request
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
