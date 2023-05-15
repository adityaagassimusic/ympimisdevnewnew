<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\User;
use App\NgList;
use App\QaMaterial;
use App\QaInspectionLevel;
use App\QaIncomingNgTemp;
use App\QaIncomingNgLog;
use App\QaIncomingLog;

use App\QaKensaLog;
use App\QaKensaNgLog;
use App\QaKensaNgTemp;

use App\EmployeeSync;
use App\Employee;
use App\CodeGenerator;
use App\Approver;
use App\WeeklyCalendar;
use App\AreaCode;
use App\StampHierarchy;
use App\OriginGroup;
use App\Department;
use App\AuditExternalClaim;
use Response;
use Excel;
use DataTables;
use Carbon\Carbon;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use iio\libmergepdf\Merger;
use iio\libmergepdf\Driver\TcpdiDriver;
use PDFMerger;
use iio\libmergepdf\Driver\DriverInterface;
use iio\libmergepdf\Source\FileSource;
use iio\libmergepdf\Source\RawSource;
use App\AccItem;
use File;

class QualityAssuranceController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    if (isset($_SERVER['HTTP_USER_AGENT']))
    {
      $http_user_agent = $_SERVER['HTTP_USER_AGENT']; 
      if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) 
      {
                  // Prevent MS office products detecting the upcoming re-direct .. forces them to launch the browser to this link
        die();
      }
    }

    $this->uom = ['CC','mililiter','Liter','gram','KWH','KW','Watt','rad','KJ','joule','Kg/M3','m/s','mol','A','K','jam','menit','detik','m','dm','mm','cm','°C','MPa','CC/Detik','bag', 'bar', 'belt', 'box', 'Btg', 'Btl', 'buah', 'buku', 'Can', 'Case', 'cps', 'day', 'Drum', 'galon', 'gr','job', 'JRG', 'Kg', 'kgm', 'Lbr', 'lbs', 'month', 'License', 'Lot',  'ltr', 'lubang', 'm²', 'm3', 'Mtr', 'Pack', 'package', 'pad', 'pail', 'pair', 'pc', 'Pce', 'Unit', 'Pcs', 'Rim', 'roll', 'sample', 'Set','sheet', 'tbg', 'titik','kgf','cm²','kgf/cm²','gf','μm','°','Volt','mm²'];

    $this->location = ['wi1_Woodwind Instrument (WI) 1',
    'wi2_Woodwind Instrument (WI) 2',
    'ei_Educational Instrument (EI)',
    'sx_Saxophone Body',
    'cs_Case',
    'ps_Pipe Silver',
    '4xx_YCL4XX',];

    $this->onko_ei = [
      'Softcase',
      'Cover Lower',
      'Cover R',
      'Cover L',
      'Case',
      'Handle',
      'Black Key',
      'White Key',
      'Mouthpiece Frame',
      'Button',
      'Head Joint',
      'Middle Joint',
      'Foot Joint',
      'Body Piece',
      'Head Piece',
      'Block',
      'Stopper',
      'Latch',
      'Bumper',
      'Corner Guard',
      'Handle Post',
      'Hinge',
      'Bottom Leg',
      'Pin',
      'Bagian Dalam',
      'Inner Box',
      'Side Protector',
      'Outer Box',
    ];

    $this->all_ng = [
      'Ana (Berlubang)',
      'Arai (Kasar)',
      'Atsui (Tebal)',
      'Bahan Kain Kurang',
      'Bari (Flashing)',
      'Belang',
      'Belum Sanding',
      'Blackmark',
      'Buram',
      'Butsu (Bintik Jarum)',
      'Chiisai (Kecil)',
      'Cloth Gundul',
      'Cloth Kelupas',
      'Contamin',
      'Corner Guard Tidak Ada Pin',
      'Cuttermark',
      'Cutting-Less ',
      'Dakon (Lekukan Pesok)',
      'Daku',
      'Dansa (Step)',
      'Dare (Aus)',
      'Datsu Aen (Gosong Merah)',
      'Deko (Cembung)',
      'Dol',
      'Flowmark',
      'Gess',
      'Gram sisa rautan',
      'Hagare (Kelupas)',
      'Handa Oi (Handa Berlebih)',
      'Handa Suki (Handa Berlubang, Kurang)',
      'Handa Tare (Handa Beleber)',
      'Handa Tobi (Handa Melompat)',
      'Handle Over Cutting',
      'Heko (Cekung)',
      'Hinge Stode Lepas',
      'Hinge tanpa pin',
      'Hokori (Kotoran Terpainting)',
      'Hook Patah',
      'Jamur',
      'Kabi (Berjamur)',
      'Kake (Cuil)',
      'Katai (Kaku)',
      'Kesson (Keropos)',
      'Ketinggian Kunci',
      'Kizu (Scratch)',
      'Koge (Gosong)',
      'Kotor Lem',
      'Kotor Serangga',
      'Kumori (Kusam)',
      'Latch Haka',
      'Magari (bengkok)',
      'Material Kurang',
      'Material Lebih',
      'Material Tercampur',
      'Mekki Nai (Tidak Terplating)',
      'Mekki Tare (Plating Beleber)',
      'Mekki Uki (Plating Kelupas)',
      'Mekki Usui (Plating Tipis)',
      'Mijikai (Pendek)',
      'Nagai (Panjang)',
      'Nami (Bergelombang)',
      'Naname (Miring)',
      'Noise',
      'Ooki (Besar)',
      'Ore (Patah)',
      'Over-Cutting',
      'Produk pertama',
      'Ro Oi (Ro Berlebih)',
      'Ro Suki (Ro Berlubang/Kurang)',
      'Ro Tare (Ro Beleber)',
      'Rome',
      'Rome tembus',
      'Sabi (Karat)',
      'Salah Kunci',
      'Salah Label',
      'Screw (Patah)',
      'Shimi (Painting Terkontamintasi)',
      'Shiny (Berkilau)',
      'Shiwa (Kerut)',
      'Silver',
      'Sinmark',
      'Stamp Yamaha Kelupas',
      'Sukima (Celah)',
      'Toke (Meleleh)',
      'Toso Nai (Tidak Terpainting)',
      'Toso Tare (Painting Beleber)',
      'Toso Usui (Painting Tipis)',
      'Unbalance',
      'Usui (Tipis)',
      'Ware (Retak)',
      'Yabure (Sobek)',
      'Yogore',
      'Yurui (Longgar)',
      'Zara-Zara (Plating Kasar)',
      'Zure (Geser)',
      'Kebutuhan sample',
      'Stamp Tipis',
      'Stamp Putus',
      'Salah spec',
      'Serabut',
      'Bintik Putih',
      'Mizo Nai',
      'Jatuh Larutan',
      'Spec Lama',

    ];
  }

  public function index()
  {
    return view('qa.index')
    ->with('title', 'Quality Assurance')
    ->with('title_jp', '品保')
    ->with('page', 'Quality Assurance')
    ->with('jpn', '品保');
  }

  public function index_cpar()
  {
    return view('cpar.index_cpar')
    ->with('title', 'Quality Assurance')
    ->with('title_jp', '品保')
    ->with('page', 'Quality Assurance')
    ->with('jpn', '品保');
  }

  public function index_ymmj()
  {
    return view('qc_ymmj.index_ymmj')
    ->with('title', 'Quality Assurance')
    ->with('title_jp', '品保')
    ->with('page', 'Quality Assurance')
    ->with('jpn', '品保');
  }

  public function indexIncomingCheck($location)
  {
    $inspection_level = QaInspectionLevel::select('inspection_level')->distinct()->get();
    $nglists = NgList::where('location','qa-incoming')->where('remark',$location)->get();

    if ($location == 'wi1') {
     $loc = 'Woodwind Instrument (WI) 1';
   }else if ($location == 'wi2') {
     $loc = 'Woodwind Instrument (WI) 2';
   }else if($location == 'ei'){
     $loc = 'Educational Instrument (EI)';
   }else if($location == 'sx'){
    $loc = 'Saxophone Body';
  }else if ($location == 'cs'){
   $loc = 'Case';
 }else if($location == 'ps'){
   $loc = 'Pipe Silver';
 }else if($location == '4xx'){
   $loc = 'YCL4XX';
 }

 $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

 $vendor = DB::SELECT("SELECT DISTINCT
  ( vendor ) 
  FROM
  qa_materials 
  ORDER BY
  LENGTH( vendor ) ASC");

 $material = DB::SELECT("SELECT DISTINCT
  ( material_number ),
  material_description 
  FROM
  qa_materials 
  ORDER BY
  material_description ASC");

 $areas = DB::connection('ympimis_2')->table('qa_incoming_ng_areas')->where('location',$location)->get();

 return view('qa.index_incoming_check')
 ->with('ng_lists', $nglists)
 ->with('inspection_level', $inspection_level)
 ->with('vendors', $vendor)
 ->with('materials', $material)
 ->with('loc', $loc)
 ->with('location', $location)
 ->with('areas', $areas)
 ->with('emp', $emp)
 ->with('title', 'Incoming Check QA')
 ->with('title_jp', '受入検査品保')
 ->with('page', 'Quality Assurance')
 ->with('jpn', '品保');
}

public function fetchCheckMaterial(Request $request)
{
  try {
   $material = QaMaterial::where('material_number',$request->get('material_number'))->where('s_loc','like','%'.$request->get('location').'%')->first();

   if (count($material) > 0) {
    $response = array(
     'status' => true,
     'material'=> $material
   );
    return Response::json($response);
  }else{
    $response = array(
     'status' => false,
     'message' => 'Material Tidak Ditemukan'
   );
    return Response::json($response);
  }
} catch (\Exception $e) {
 $response = array(
  'status' => false,
  'message' => $e->getMessage()
);
 return Response::json($response);
}
}

public function fetchCheckSerialNumber(Request $request)
{
  try {
    if (str_contains($request->get('serial_number'),'TRUE')) {
      $material = DB::connection('ympimis_online')->table('qa_outgoing_vendors')->where('serial_number',$request->get('serial_number'))->first();
    }else if(str_contains($request->get('serial_number'),'ARS')){
      $material = DB::connection('ympimis_online')->table('qa_outgoing_vendor_finals')->where('final_serial_number',$request->get('serial_number'))->join('qa_outgoing_vendors','qa_outgoing_vendors.serial_number','qa_outgoing_vendor_finals.final_serial_number')->first();
    }else{
      $material = DB::connection('ympimis_online')->table('qa_outgoing_vendors')->where('serial_number',$request->get('serial_number'))->first();
    }

    if (count($material) > 0) {
      $response = array(
        'status' => true,
        'material'=> $material
      );
      return Response::json($response);
    }else{
      $response = array(
        'status' => false,
        'message' => 'Material Tidak Ditemukan'
      );
      return Response::json($response);
    }
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function inputNgTemp(Request $request)
{
  try {
   $material_number = strtoupper($request->get('material_number'));
   $material_description = $request->get('material_description');
   $vendor = $request->get('vendor');
   $qty_rec = $request->get('qty_rec');
   $lot_number = $request->get('lot_number');
   $qty_check = $request->get('qty_check');
   $invoice = $request->get('invoice');
   $inspection_level = $request->get('inspection_level');
   $ng_name = $request->get('ng_name');
   $qty_ng = $request->get('qty_ng');
   $status_ng = $request->get('status_ng');
   $note_ng = $request->get('note_ng');
   $inspector = $request->get('inspector');
   $location = $request->get('location');
   $area = $request->get('area');
   $incoming_check_code = $location."_".$material_number."_".$vendor."_".$invoice."_".$inspection_level."_".$inspector;

   QaIncomingNgTemp::create([
    'incoming_check_code' => $incoming_check_code,
    'inspector_id' => $inspector,
    'location' => $location,
    'area' => $area,
    'material_number' => $material_number,
    'material_description' => $material_description,
    'vendor' => $vendor,
    'qty_rec' => $qty_rec,
    'lot_number' => $lot_number,
    'qty_check' => $qty_check,
    'invoice' => $invoice,
    'inspection_level' => $inspection_level,
    'ng_name' => $ng_name,
    'qty_ng' => $qty_ng,
    'status_ng' => $status_ng,
    'note_ng' => $note_ng,
    'created_by' => Auth::id()
  ]);

   $response = array(
    'status' => true,
    'message' => 'Input NG Berhasil',
    'incoming_check_code' => $incoming_check_code
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

public function fetchNgTemp(Request $request)
{
  try {
   if ($request->get('incoming_check_code') != "") {
    $ng_temp = QaIncomingNgTemp::where('incoming_check_code',$request->get('incoming_check_code'))->get();
    $response = array(
     'status' => true,
     'incoming_check_code' => $request->get('incoming_check_code'),
     'ng_temp' => $ng_temp
   );
    return Response::json($response);
  }else{
    $response = array(
     'status' => true,

   );
    return Response::json($response);
  }
} catch (\Exception $e) {
 $response = array(
  'status' => false,
  'message' => $e->getMessage()
);
 return Response::json($response);
}
}

public function fetchNgList(Request $request)
{
  try {
    if ($request->get('location') == 'KPP') {
      $ng_list = NgList::where('location','KPP')->where('remark', $request->get('location'))->get();
    } else {
      $ng_list = NgList::where('location','qa-incoming')->where('remark',$request->get('location'))->get();
    }

    $response = array(
      'status' => true,
      'ng_list' => $ng_list
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

public function deleteNgTemp(Request $request)
{
  try {
   $delete = QaIncomingNgTemp::where('id',$request->get('id'))->forceDelete();
   $response = array(
    'status' => true,
    'message' => 'Success Delete NG'
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

public function inputNgLog(Request $request)
{
  try {
   $material_number = strtoupper($request->get('material_number'));
   $material_description = $request->get('material_description');
   $vendor = $request->get('vendor');
   $lot_number = $request->get('lot_number');
   $qty_rec = $request->get('qty_rec');
   $qty_check = $request->get('qty_check');
   $invoice = $request->get('invoice');
   $inspection_level = $request->get('inspection_level');
   $inspector = $request->get('inspector');
   $location = $request->get('location');
   $incoming_check_code = $request->get('incoming_check_code')."_".date('Y-m-d H:i:s');
   $repair = $request->get('repair');
   $scrap = $request->get('scrap');
   $return = $request->get('returns');
   $total_ok = $request->get('total_ok');
   $total_ng = $request->get('total_ng');
   $ng_ratio = $request->get('ng_ratio');
   $status_lot = $request->get('status_lot');
   $serial_number = $request->get('serial_number');
   $note_all = $request->get('note_all');
   $total_ng_pcs = $request->get('total_ng_pcs');

   $materials = QaMaterial::where('material_number',$material_number)->first();

   $remark = null;
   if ($location == '4xx') {
     $remark = 'YCL4XX';
   }

   $log = QaIncomingLog::create([
     'incoming_check_code' => $incoming_check_code,
     'inspector_id' => $inspector,
     'location' => $location,
     'lot_number' => $lot_number,
     'serial_number' => $serial_number,
     'material_number' => $material_number,
     'material_description' => $material_description,
     'vendor' => $vendor,
     'qty_rec' => $qty_rec,
     'qty_check' => $qty_check,
     'invoice' => $invoice,
     'inspection_level' => $inspection_level,
     'repair' => $repair,
     'scrap' => $scrap,
     'return' => $return,
     'total_ok' => $total_ok,
     'total_ng' => $total_ng,
     'remark' => $remark,
     'note_all' => $note_all,
     'ng_ratio' => $ng_ratio,
     'hpl' => $materials->hpl,
     'status_lot' => $status_lot,
     'total_ng_pcs' => $total_ng_pcs,
     'created_by' => Auth::id()
   ]);

   $ng_temp = QaIncomingNgTemp::where('incoming_check_code',$request->get('incoming_check_code'))->get();

   foreach ($ng_temp as $key) {
     QaIncomingNgLog::create([
      'incoming_check_code' => $incoming_check_code,
      'incoming_check_log_id' => $log->id,
      'inspector_id' => $inspector,
      'serial_number' => $serial_number,
      'location' => $key->location,
      'lot_number' => $lot_number,
      'material_number' => $key->material_number,
      'material_description' => $key->material_description,
      'vendor' => $key->vendor,
      'qty_rec' => $key->qty_rec,
      'qty_check' => $key->qty_check,
      'invoice' => $key->invoice,
      'area' => $key->area,
      'inspection_level' => $key->inspection_level,
      'ng_name' => $key->ng_name,
      'qty_ng' => $key->qty_ng,
      'status_ng' => $key->status_ng,
      'note_ng' => $key->note_ng,
      'created_by' => Auth::id()
    ]);
     QaIncomingNgTemp::where('id',$key->id)->forceDelete();
   }

   $response = array(
    'status' => true,
    'message' => 'Success Input Incoming Check'
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

public function indexDisplayIncomingLotStatus()
{
  return view('qa.index_lot_monitoring')
  ->with('title', 'QA Incoming Check Material Lot Out Monitoring')
  ->with('title_jp', 'QA受入検査ロットアウト品の表示')
  ->with('location', $this->location)
  ->with('page', 'QA Incoming Check Material Lot Out Monitoring')
  ->with('jpn', 'QA受入検査ロットアウト品の表示');
}

public function fetchDisplayIncomingLotStatus(Request $request)
{
  try {

    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = "DATE(NOW())";
      $last = "DATE(NOW())";
      $date = date('Y-m-d');
      $monthTitle = date("d-M-Y", strtotime($date));
      $dates_from = date('Y-m-d');
      $dates_to = date('Y-m-d');
    }else{
      $first = "DATE(NOW())";
      $last = "'".$date_to."'";
      $date = date('Y-m-d');
      $monthTitle = date("d-M-Y", strtotime($date)).' to '.date("d-M-Y", strtotime($date_to));
      $dates_from = date('Y-m-d');
      $dates_to = $date_to;
    }
  }else{
   if ($date_to == "") {
    $first = "'".$date_from."'";
    $last = "DATE(NOW())";
    $date = date('Y-m-d');
    $monthTitle = date("d-M-Y", strtotime($date_from)).' to '.date("d-M-Y", strtotime($date));
    $dates_from = $date_from;
    $dates_to = date('Y-m-d');
  }else{
    $first = "'".$date_from."'";
    $last = "'".$date_to."'";
    $monthTitle = date("d-M-Y", strtotime($date_from)).' to '.date("d-M-Y", strtotime($date_to));
    $dates_from = $date_from;
    $dates_to = $date_to;
  }
}

if ($request->get('lot_status') == 'Lot OK') {
  $lot_count_ok = "(
  SELECT
  count( qa_incoming_logs.id ) 
  FROM
  qa_incoming_logs
  JOIN qa_materials ON qa_materials.material_number = qa_incoming_logs.material_number 
  WHERE
  status_lot = 'Lot OK' 
  AND DATE( qa_incoming_logs.created_at ) BETWEEN ".$first."
  AND ".$last."
  AND qa_materials.s_loc_sampling = a.s_loc_sampling 
  AND ( qa_materials.check_category != 3 OR qa_materials.check_category IS NULL ) 
) ";
$lot_count_out = "0";
$lot_status_resume = "and qa_incoming_logs.status_lot = 'Lot OK'";
$lot_ok_vendor = "COALESCE ((
SELECT
COUNT(
DISTINCT ( serial_number )) AS lot_ok 
FROM
qa_outgoing_vendors 
WHERE
lot_status = 'LOT OK' 
AND DATE( qa_outgoing_vendors.created_at ) BETWEEN ".$first." 
AND ".$last."
AND vendor_shortname = a.vendor_shortname 
GROUP BY
vendor_shortname 
),
0 
)";
$lot_out_vendor = "0";
}else if ($request->get('lot_status') == 'Lot Out'){
  $lot_count_ok = "0";
  $lot_count_out = "(
  SELECT
  count( qa_incoming_logs.id ) 
  FROM
  qa_incoming_logs
  JOIN qa_materials ON qa_materials.material_number = qa_incoming_logs.material_number 
  WHERE
  status_lot = 'Lot Out' 
  AND DATE( qa_incoming_logs.created_at ) BETWEEN ".$first."
  AND ".$last."
  AND qa_materials.s_loc_sampling = a.s_loc_sampling 
  AND ( qa_materials.check_category != 3 OR qa_materials.check_category IS NULL ) 
)";
$lot_status_resume = "and qa_incoming_logs.status_lot = 'Lot Out'";
$lot_ok_vendor = "0";
$lot_out_vendor = "COALESCE ((
SELECT
COUNT(
DISTINCT ( serial_number )) AS lot_ok 
FROM
qa_outgoing_vendors 
WHERE
lot_status = 'LOT OUT' 
AND DATE( qa_outgoing_vendors.created_at ) BETWEEN ".$first." 
AND ".$last."
AND vendor_shortname = a.vendor_shortname 
GROUP BY
vendor_shortname 
),
0 
)";
}else{
  $lot_count_ok = "(
  SELECT
  count( qa_incoming_logs.id ) 
  FROM
  qa_incoming_logs
  JOIN qa_materials ON qa_materials.material_number = qa_incoming_logs.material_number 
  WHERE
  status_lot = 'Lot OK' 
  AND DATE( qa_incoming_logs.created_at ) BETWEEN ".$first."
  AND ".$last."
  AND qa_materials.s_loc_sampling = a.s_loc_sampling 
  AND ( qa_materials.check_category != 3 OR qa_materials.check_category IS NULL ) 
) ";
$lot_count_out = "(
SELECT
count( qa_incoming_logs.id ) 
FROM
qa_incoming_logs
JOIN qa_materials ON qa_materials.material_number = qa_incoming_logs.material_number 
WHERE
status_lot = 'Lot Out' 
AND DATE( qa_incoming_logs.created_at ) BETWEEN ".$first."
AND ".$last."
AND qa_materials.s_loc_sampling = a.s_loc_sampling 
AND ( qa_materials.check_category != 3 OR qa_materials.check_category IS NULL ) 
)";
$lot_status_resume = "";
$lot_ok_vendor = "COALESCE ((
SELECT
COUNT(
DISTINCT ( serial_number )) AS lot_ok 
FROM
qa_outgoing_vendors 
WHERE
lot_status = 'LOT OK' 
AND DATE( qa_outgoing_vendors.created_at ) BETWEEN ".$first." 
AND ".$last."
AND vendor_shortname = a.vendor_shortname 
GROUP BY
vendor_shortname 
),
0 
)";
$lot_out_vendor = "COALESCE ((
SELECT
COUNT(
DISTINCT ( serial_number )) AS lot_ok 
FROM
qa_outgoing_vendors 
WHERE
lot_status = 'LOT OUT' 
AND DATE( qa_outgoing_vendors.created_at ) BETWEEN ".$first." 
AND ".$last."
AND vendor_shortname = a.vendor_shortname 
GROUP BY
vendor_shortname 
),
0 
)";
}

$lot_count = DB::SELECT("SELECT DISTINCT
  ( a.s_loc_sampling ) AS location,
  ".$lot_count_ok." AS lot_ok,
  ".$lot_count_out." AS lot_out 
  FROM
  qa_materials a");

$lot_count_vendor = DB::connection('ympimis_online')
->select("SELECT DISTINCT
  ( a.vendor_shortname ) AS location,
  ".$lot_ok_vendor." AS lot_ok,
  ".$lot_out_vendor." AS lot_out 
  FROM
  qa_outgoing_vendors a");

$lot_detail = DB::SELECT("SELECT
        *,
  DATE( qa_incoming_logs.created_at ) AS date_lot,
  ( SELECT GROUP_CONCAT( ng_name ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_name,
  ( SELECT DISTINCT(vendor_shortname) FROM qa_materials WHERE qa_materials.vendor = qa_incoming_logs.vendor ) AS vendor_shortname 
  FROM
  qa_incoming_logs 
  left join employee_syncs on employee_syncs.employee_id = qa_incoming_logs.inspector_id
  JOIN qa_materials ON qa_materials.material_number = qa_incoming_logs.material_number 
  WHERE
        -- qa_incoming_logs.status_lot = 'Lot Out' 
        DATE( qa_incoming_logs.created_at ) BETWEEN ".$first." and ".$last."
        AND ( qa_materials.check_category != 3 OR qa_materials.check_category IS NULL ) 
        ".$lot_status_resume."
        ORDER BY
        status_lot DESC,
        qa_incoming_logs.created_at");

$response = array(
  'status' => true,
  'lot_count' => $lot_count,
  'lot_count_vendor' => $lot_count_vendor,
  'monthTitle' => $monthTitle,
  'timeTitle' => date('H:i:s'),
  'lot_detail' => $lot_detail,
  'dates_from' => $dates_from,
  'dates_to' => $dates_to,
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

public function indexIncomingYmpi()
{
  $vendor = DB::SELECT("SELECT DISTINCT
    ( vendor ) 
    FROM
    qa_materials 
    ORDER BY
    LENGTH( vendor ) ASC");

  $material = DB::SELECT("SELECT DISTINCT
    ( material_number ),
    material_description 
    FROM
    qa_materials 
    ORDER BY
    material_description ASC");

  $firstData = DB::SELECT("SELECT
    DATE( created_at ) AS `first` 
    FROM
    qa_incoming_logs 
    LIMIT 1");
  return view('qa.monitoring_material_ympi')
  ->with('title', 'QA Incoming Material Monitoring')
  ->with('title_jp', 'QA受入検査の監視')
  ->with('vendors', $vendor)
  ->with('vendors2', $vendor)
  ->with('materials', $material)
  ->with('firstData', $firstData)
  ->with('page', 'QA Incoming Material Monitoring')
  ->with('jpn', 'QA受入検査の監視');
}

public function fetchIncomingYmpi(Request $request)
{
  try {
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = "DATE(NOW())";
      $last = "DATE(NOW())";
      $date = date('Y-m-d');
      $monthTitle = date("d M Y", strtotime($date));
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

$vendor = '';
if($request->get('vendor') != null){
  $vendors =  explode(",", $request->get('vendor'));
  for ($i=0; $i < count($vendors); $i++) {
    $vendor = $vendor."'".$vendors[$i]."'";
    if($i != (count($vendors)-1)){
      $vendor = $vendor.',';
    }
  }
  $vendorin = " and qa_incoming_logs.`vendor` in (".$vendor.") ";
  $vendorin2 = " where `vendor` in (".$vendor.") ";
}
else{
  $vendorin = "";
  $vendorin2 = "";
}

$material = '';
if($request->get('material') != null){
  $materials =  explode(",", $request->get('material'));
  for ($i=0; $i < count($materials); $i++) {
    $material = $material."'".$materials[$i]."'";
    if($i != (count($materials)-1)){
      $material = $material.',';
    }
  }
  $materialin = " and qa_incoming_logs.`material_number` in (".$material.") ";
}
else{
  $materialin = "";
}

if ($request->get('lot_status') == 'Lot OK') {
  $lot_count_ok = "(
  SELECT
  count( qa_incoming_logs.id ) 
  FROM
  qa_incoming_logs
  JOIN qa_materials ON qa_materials.material_number = qa_incoming_logs.material_number 
  WHERE
  status_lot = 'Lot OK' 
  AND DATE( qa_incoming_logs.created_at ) BETWEEN ".$first."
  AND ".$last."
  AND qa_materials.vendor = a.vendor 
  ".$materialin."
  AND ( qa_materials.check_category != 3 OR qa_materials.check_category IS NULL ) 
) ";
$lot_count_out = "0";
$lot_status_resume = "and qa_incoming_logs.status_lot = 'Lot OK'";
}else if ($request->get('lot_status') == 'Lot Out'){
  $lot_count_ok = "0";
  $lot_count_out = "(
  SELECT
  count( qa_incoming_logs.id ) 
  FROM
  qa_incoming_logs
  JOIN qa_materials ON qa_materials.material_number = qa_incoming_logs.material_number 
  WHERE
  status_lot = 'Lot Out' 
  AND DATE( qa_incoming_logs.created_at ) BETWEEN ".$first."
  AND ".$last."
  AND qa_materials.vendor = a.vendor 
  ".$materialin."
  AND ( qa_materials.check_category != 3 OR qa_materials.check_category IS NULL ) 
)";
$lot_status_resume = "and qa_incoming_logs.status_lot = 'Lot Out'";
}else{
  $lot_count_ok = "(
  SELECT
  count( qa_incoming_logs.id ) 
  FROM
  qa_incoming_logs
  JOIN qa_materials ON qa_materials.material_number = qa_incoming_logs.material_number 
  WHERE
  status_lot = 'Lot OK' 
  AND DATE( qa_incoming_logs.created_at ) BETWEEN ".$first."
  AND ".$last."
  AND qa_materials.vendor = a.vendor 
  ".$materialin."
  AND ( qa_materials.check_category != 3 OR qa_materials.check_category IS NULL ) 
) ";
$lot_count_out = "(
SELECT
count( qa_incoming_logs.id ) 
FROM
qa_incoming_logs
JOIN qa_materials ON qa_materials.material_number = qa_incoming_logs.material_number 
WHERE
status_lot = 'Lot Out' 
AND DATE( qa_incoming_logs.created_at ) BETWEEN ".$first."
AND ".$last."
AND qa_materials.vendor = a.vendor 
".$materialin."
AND ( qa_materials.check_category != 3 OR qa_materials.check_category IS NULL ) 
)";
$lot_status_resume = "";
}
$lot_count = DB::SELECT("SELECT DISTINCT
  ( vendor_shortname ),
  vendor,
  ".$lot_count_ok." AS lot_ok,
  ".$lot_count_out." AS lot_out 
  FROM
  qa_materials a
  ".$vendorin2."
  ");

$lot_detail = DB::SELECT("SELECT
        *,
  DATE( qa_incoming_logs.created_at ) AS date_lot,
  ( SELECT GROUP_CONCAT( ng_name ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_name,
  ( SELECT DISTINCT(vendor_shortname) FROM qa_materials WHERE qa_materials.vendor = qa_incoming_logs.vendor ) AS vendor_shortname 
  FROM
  qa_incoming_logs 
  left join employee_syncs on employee_syncs.employee_id = qa_incoming_logs.inspector_id
  JOIN qa_materials ON qa_materials.material_number = qa_incoming_logs.material_number 
  WHERE
        -- qa_incoming_logs.status_lot = 'Lot Out' 
        DATE( qa_incoming_logs.created_at ) BETWEEN ".$first." and ".$last."
        AND ( qa_materials.check_category != 3 OR qa_materials.check_category IS NULL ) 
        ".$lot_status_resume."
        ".$vendorin."
        ".$materialin."
        ORDER BY
        status_lot DESC,
        total_ng DESC,
        ng_ratio DESC,
        qa_incoming_logs.created_at
        LIMIT 5");

$response = array(
  'status' => true,
  'lot_count' => $lot_count,
  'monthTitle' => $monthTitle,
  'lot_detail' => $lot_detail,
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

public function indexIncomingVendor()
{
  $material = DB::connection('ympimis_online')->SELECT("SELECT DISTINCT
    ( material_number ),
    material_description 
    FROM
    qa_materials 
    ORDER BY
    material_description ASC");

  $firstData = DB::SELECT("SELECT
    DATE( created_at ) AS `first` 
    FROM
    qa_incoming_logs 
    LIMIT 1");

  $category_arisa = DB::connection('ympimis_online')
  ->select("SELECT DISTINCT
    ( a.material_category ) AS category 
    FROM
    qa_materials a 
    WHERE
    a.vendor_shortname = 'ARISA'");

  $category_true = DB::connection('ympimis_online')
  ->select("SELECT DISTINCT
    ( a.material_category ) AS category 
    FROM
    qa_materials a 
    WHERE
    a.vendor_shortname = 'TRUE'");

  $category_kbi = DB::connection('ympimis_online')
  ->select("SELECT DISTINCT
    ( a.material_category ) AS category 
    FROM
    qa_materials a 
    WHERE
    a.vendor_shortname = 'KYORAKU'");

  $category_crestec = DB::connection('ympimis_online')
  ->select("SELECT DISTINCT
    ( a.material_category ) AS category 
    FROM
    qa_materials a 
    WHERE
    a.vendor_shortname = 'CRESTEC'");

  return view('qa.monitoring_material_vendor')
  ->with('title', 'Vendor Final Inspection Monitoring')
  ->with('title_jp', 'ベンダーファイナル検査の監視')
  ->with('materials', $material)
  ->with('firstData', $firstData)
  ->with('category_arisa1', $category_arisa)
  ->with('category_arisa2', $category_arisa)
  ->with('category_arisa3', $category_arisa)
  ->with('category_true1', $category_true)
  ->with('category_true2', $category_true)
  ->with('category_true3', $category_true)
  ->with('category_kbi1', $category_kbi)
  ->with('category_kbi2', $category_kbi)
  ->with('category_kbi3', $category_kbi)
  ->with('category_crestec1', $category_crestec)
  ->with('category_crestec2', $category_crestec)
  ->with('category_crestec3', $category_crestec)
  ->with('page', 'Vendor Final Inspection Monitoring')
  ->with('jpn', 'ベンダーファイナル検査の監視');
}

public function fetchIncomingVendor(Request $request)
{
  try {
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = "'".date('Y-m-01')."'";
      $last = "'".date('Y-m-d')."'";
      $date = date('Y-m-d');
      $monthTitle = date("01 M Y", strtotime($date)).' to '.date("d M Y");
    }else{
      $first = "'".date('Y-m-01')."'";
      $last = "'".$date_to."'";
      $date = date('Y-m-d');
      $monthTitle = date("d M Y", strtotime($date)).' to '.date("d M Y", strtotime($date_to));
    }
  }else{
   if ($date_to == "") {
    $first = "'".$date_from."'";
    $last = "'".date('Y-m-d')."'";
    $date = date('Y-m-d');
    $monthTitle = date("d M Y", strtotime($date_from)).' to '.date("d M Y", strtotime($date));
  }else{
    $first = "'".$date_from."'";
    $last = "'".$date_to."'";
    $monthTitle = date("d M Y", strtotime($date_from)).' to '.date("d M Y", strtotime($date_to));
  }
}

$material = '';
if($request->get('material') != null){
  $materials =  explode(",", $request->get('material'));
  for ($i=0; $i < count($materials); $i++) {
    $material = $material."'".$materials[$i]."'";
    if($i != (count($materials)-1)){
      $material = $material.',';
    }
  }
  $materialin = " and qa_outgoing_vendors.`material_number` in (".$material.") ";
}
else{
  $materialin = "";
}

if ($request->get('lot_status') == 'Lot OK') {
  $lot_count_ok = "(
  SELECT
  count( DISTINCT ( qa_outgoing_vendors.serial_number ) ) 
  FROM
  qa_outgoing_vendors
  JOIN qa_materials ON qa_materials.material_number = qa_outgoing_vendors.material_number 
  WHERE
  lot_status = 'LOT OK' 
  AND DATE( qa_outgoing_vendors.created_at ) BETWEEN ".$first."
  AND ".$first."
  AND qa_materials.material_category = a.material_category 
) ";
$lot_count_ok_arisa = "(
SELECT
count( DISTINCT ( qa_outgoing_vendor_finals.final_serial_number ) ) 
FROM
qa_outgoing_vendor_finals
JOIN qa_materials ON qa_materials.material_number = qa_outgoing_vendor_finals.material_number 
WHERE
lot_status = 'LOT OK' 
AND DATE( qa_outgoing_vendor_finals.created_at ) BETWEEN ".$first." 
AND ".$last."
AND qa_materials.material_category = a.material_category 
)";
$lot_count_ok_crestec = "(
SELECT
count( DISTINCT ( qa_outgoing_vendor_finals.final_serial_number ) ) 
FROM
qa_outgoing_vendor_finals
JOIN qa_materials ON qa_materials.material_number = qa_outgoing_vendor_finals.material_number 
WHERE
lot_status = 'LOT OK' 
AND DATE( qa_outgoing_vendor_finals.created_at ) BETWEEN ".$first." 
AND ".$last."
AND qa_materials.material_category = a.material_category 
)";
$lot_count_out = "0";
$lot_count_out_arisa = "0";
$lot_count_out_crestec = "0";
$lot_status_resume = "and qa_incoming_logs.status_lot = 'Lot OK'";
}else if ($request->get('lot_status') == 'Lot Out'){
  $lot_count_ok = "0";
  $lot_count_ok_arisa = "0";
  $lot_count_ok_crestec = "0";
  $lot_count_out = "(
  SELECT
  count( DISTINCT ( qa_outgoing_vendors.serial_number ) ) 
  FROM
  qa_outgoing_vendors
  JOIN qa_materials ON qa_materials.material_number = qa_outgoing_vendors.material_number 
  WHERE
  lot_status = 'LOT OUT' 
  AND DATE( qa_outgoing_vendors.created_at ) BETWEEN ".$first."
  AND ".$last."
  AND qa_materials.material_category = a.material_category 
) ";
$lot_count_out_arisa = "(
SELECT
count( DISTINCT ( qa_outgoing_vendor_finals.final_serial_number ) ) 
FROM
qa_outgoing_vendor_finals
JOIN qa_materials ON qa_materials.material_number = qa_outgoing_vendor_finals.material_number 
WHERE
lot_status = 'LOT OUT' 
AND DATE( qa_outgoing_vendor_finals.created_at ) BETWEEN ".$first." 
AND ".$last." 
AND qa_materials.material_category = a.material_category 
)";
$lot_count_out_crestec = "(
SELECT
count( DISTINCT ( qa_outgoing_vendor_finals.final_serial_number ) ) 
FROM
qa_outgoing_vendor_finals
JOIN qa_materials ON qa_materials.material_number = qa_outgoing_vendor_finals.material_number 
WHERE
lot_status = 'LOT OUT' 
AND DATE( qa_outgoing_vendor_finals.created_at ) BETWEEN ".$first." 
AND ".$last." 
AND qa_materials.material_category = a.material_category 
)";
$lot_status_resume = "and qa_incoming_logs.status_lot = 'Lot Out'";
}else{
  $lot_count_ok = "(
  SELECT
  count( DISTINCT ( qa_outgoing_vendors.serial_number ) ) 
  FROM
  qa_outgoing_vendors
  JOIN qa_materials ON qa_materials.material_number = qa_outgoing_vendors.material_number 
  WHERE
  lot_status = 'LOT OK' 
  AND DATE( qa_outgoing_vendors.created_at ) BETWEEN ".$first."
  AND ".$last."
  AND qa_materials.material_category = a.material_category 
) ";
$lot_count_ok_arisa = "(
SELECT
count( DISTINCT ( qa_outgoing_vendor_finals.final_serial_number ) ) 
FROM
qa_outgoing_vendor_finals
JOIN qa_materials ON qa_materials.material_number = qa_outgoing_vendor_finals.material_number 
WHERE
lot_status = 'LOT OK' 
AND DATE( qa_outgoing_vendor_finals.created_at ) BETWEEN ".$first." 
AND ".$last."
AND qa_materials.material_category = a.material_category 
)";
$lot_count_ok_crestec = "(
SELECT
count( DISTINCT ( qa_outgoing_vendor_finals.final_serial_number ) ) 
FROM
qa_outgoing_vendor_finals
JOIN qa_materials ON qa_materials.material_number = qa_outgoing_vendor_finals.material_number 
WHERE
lot_status = 'LOT OK' 
AND DATE( qa_outgoing_vendor_finals.created_at ) BETWEEN ".$first." 
AND ".$last."
AND qa_materials.material_category = a.material_category 
)";
$lot_count_out = "(
SELECT
count( DISTINCT ( qa_outgoing_vendors.serial_number ) ) 
FROM
qa_outgoing_vendors
JOIN qa_materials ON qa_materials.material_number = qa_outgoing_vendors.material_number 
WHERE
lot_status = 'LOT OUT' 
AND DATE( qa_outgoing_vendors.created_at ) BETWEEN ".$first."
AND ".$last."
AND qa_materials.material_category = a.material_category 
) ";
$lot_count_out_arisa = "(
SELECT
count( DISTINCT ( qa_outgoing_vendor_finals.final_serial_number ) ) 
FROM
qa_outgoing_vendor_finals
JOIN qa_materials ON qa_materials.material_number = qa_outgoing_vendor_finals.material_number 
WHERE
lot_status = 'LOT OUT' 
AND DATE( qa_outgoing_vendor_finals.created_at ) BETWEEN ".$first." 
AND ".$last." 
AND qa_materials.material_category = a.material_category 
)";
$lot_count_out_crestec = "(
SELECT
count( DISTINCT ( qa_outgoing_vendor_finals.final_serial_number ) ) 
FROM
qa_outgoing_vendor_finals
JOIN qa_materials ON qa_materials.material_number = qa_outgoing_vendor_finals.material_number 
WHERE
lot_status = 'LOT OUT' 
AND DATE( qa_outgoing_vendor_finals.created_at ) BETWEEN ".$first." 
AND ".$last." 
AND qa_materials.material_category = a.material_category 
)";
$lot_status_resume = "";
}
$lot_count_arisa = DB::connection('ympimis_online')->SELECT("SELECT DISTINCT
  ( a.material_category ),
  ".$lot_count_ok_arisa." AS lot_ok,
  ".$lot_count_out_arisa." AS lot_out
  FROM
  qa_materials a 
  WHERE
  a.vendor_shortname = 'ARISA'");

$lot_count_true = DB::connection('ympimis_online')->SELECT("SELECT DISTINCT
  ( a.material_category ),
  ".$lot_count_ok." AS lot_ok,
  ".$lot_count_out." AS lot_out
  FROM
  qa_materials a 
  WHERE
  a.vendor_shortname = 'TRUE'");

$lot_count_kbi = DB::connection('ympimis_online')->SELECT("SELECT DISTINCT
  ( a.material_category ),
  ".$lot_count_ok." AS lot_ok,
  ".$lot_count_out." AS lot_out
  FROM
  qa_materials a 
  WHERE
  a.vendor_shortname = 'KYORAKU'");

$lot_count_crestec = DB::connection('ympimis_online')->SELECT("SELECT DISTINCT
  ( a.material_category ),
  ".$lot_count_ok." AS lot_ok,
  ".$lot_count_out." AS lot_out
  FROM
  qa_materials a 
  WHERE
  a.vendor_shortname = 'CRESTEC'");

        // $lot_detail_arisa = DB::connection('ympimis_online')->SELECT("SELECT DISTINCT
        //     ( serial_number ),
        //     material_number,
        //     material_description,
        //     qty_check,
        //     total_ng,
        //     ng_ratio,
        //     lot_status,
        //     DATE( qa_outgoing_vendors.created_at ) AS date_lot,
        //     ( SELECT GROUP_CONCAT( a.ng_name ) FROM qa_outgoing_vendors a WHERE a.serial_number = qa_outgoing_vendors.serial_number ) AS ng_name 
        //   FROM
        //     qa_outgoing_vendors 
        //   WHERE
        //     vendor_shortname = 'ARISA' 
        //     AND DATE( qa_outgoing_vendors.created_at ) BETWEEN ".$first." 
        //     AND ".$last."
        //     AND lot_status IS NOT NULL 
        //     ORDER BY
        //     lot_status DESC,
        //     total_ng DESC,
        //     ng_ratio DESC,
        //     created_at DESC 
        //   LIMIT 5");
$lot_detail_arisa = DB::connection('ympimis_online')->select("SELECT DISTINCT
  ( final_serial_number ) AS serial_number,
  material_number,
  material_description,
  qty_check,
  lot_status,
          DATE( qa_outgoing_vendor_finals.created_at ) AS date_lot --   ( SELECT GROUP_CONCAT( a.ng_name ) FROM qa_outgoing_vendor_finals a WHERE a.serial_number = qa_outgoing_vendor_finals.serial_number ) AS ng_name
          
          FROM
          qa_outgoing_vendor_finals 
          WHERE
          vendor_shortname = 'ARISA' 
          AND DATE( qa_outgoing_vendor_finals.created_at ) BETWEEN ".$first." 
          AND ".$last."
          AND lot_status IS NOT NULL 
          ORDER BY
          lot_status DESC,
          qty_check DESC,
          created_at DESC 
          LIMIT 5");

$lot_detail_true = DB::connection('ympimis_online')->SELECT("SELECT DISTINCT
  ( serial_number ),
  material_number,
  material_description,
  qty_check,
  total_ng,
  ng_ratio,
  lot_status,
  DATE( qa_outgoing_vendors.created_at ) AS date_lot,
  ( SELECT GROUP_CONCAT( a.ng_name ) FROM qa_outgoing_vendors a WHERE a.serial_number = qa_outgoing_vendors.serial_number ) AS ng_name 
  FROM
  qa_outgoing_vendors 
  WHERE
  vendor_shortname = 'TRUE' 
  AND DATE( qa_outgoing_vendors.created_at ) BETWEEN ".$first." 
  AND ".$last."
  AND lot_status IS NOT NULL 
  ORDER BY
  lot_status DESC,
  total_ng DESC,
  ng_ratio DESC,
  created_at DESC 
  LIMIT 5");

$lot_detail_kbi = DB::connection('ympimis_online')->SELECT("SELECT DISTINCT
  ( serial_number ),
  material_number,
  material_description,
  qty_check,
  total_ng,
  ng_ratio,
  lot_status,
  DATE( qa_outgoing_vendors.created_at ) AS date_lot,
  ( SELECT GROUP_CONCAT( a.ng_name ) FROM qa_outgoing_vendors a WHERE a.serial_number = qa_outgoing_vendors.serial_number ) AS ng_name 
  FROM
  qa_outgoing_vendors 
  WHERE
  vendor_shortname = 'KYORAKU' 
  AND DATE( qa_outgoing_vendors.created_at ) BETWEEN ".$first." 
  AND ".$last."
  AND lot_status IS NOT NULL 
  ORDER BY
  lot_status DESC,
  total_ng DESC,
  ng_ratio DESC,
  created_at DESC 
  LIMIT 5");

$lot_detail_crestec = DB::connection('ympimis_online')->SELECT("SELECT DISTINCT
  ( serial_number ),
  material_number,
  material_description,
  qty_check,
  total_ng,
  ng_ratio,
  lot_status,
  DATE( qa_outgoing_vendors.created_at ) AS date_lot,
  ( SELECT GROUP_CONCAT( a.ng_name ) FROM qa_outgoing_vendors a WHERE a.serial_number = qa_outgoing_vendors.serial_number ) AS ng_name 
  FROM
  qa_outgoing_vendors 
  WHERE
  vendor_shortname = 'CRESTEC' 
  AND DATE( qa_outgoing_vendors.created_at ) BETWEEN ".$first." 
  AND ".$last."
  AND lot_status IS NOT NULL 
  ORDER BY
  lot_status DESC,
  total_ng DESC,
  ng_ratio DESC,
  created_at DESC 
  LIMIT 5");

$response = array(
  'status' => true,
  'lot_count_arisa' => $lot_count_arisa,
  'lot_count_true' => $lot_count_true,
  'lot_count_kbi' => $lot_count_kbi,
  'lot_count_crestec' => $lot_count_crestec,
  'monthTitle' => $monthTitle,
  'lot_detail_arisa' => $lot_detail_arisa,
  'lot_detail_true' => $lot_detail_true,
  'lot_detail_kbi' => $lot_detail_kbi,
  'lot_detail_crestec' => $lot_detail_crestec,
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

public function indexDisplayIncomingMaterialDefect()
{
  $vendor = DB::SELECT("SELECT DISTINCT
    ( vendor ) 
    FROM
    qa_materials 
    ORDER BY
    LENGTH( vendor ) ASC");

  $material = DB::SELECT("SELECT DISTINCT
    ( material_number ),
    material_description 
    FROM
    qa_materials 
    ORDER BY
    material_description ASC");

  $firstData = DB::SELECT("SELECT
    DATE_FORMAT( created_at, '%Y-%m' ) AS `first` 
    FROM
    qa_incoming_logs 
    LIMIT 1");

  return view('qa.index_material_defect')
  ->with('title', 'QA Pareto Defect Incoming')
  ->with('title_jp', 'QA受入パレット不良')
  ->with('location', $this->location)
  ->with('materials', $material)
  ->with('firstData', $firstData)
  ->with('vendors', $vendor)
  ->with('page', 'QA Pareto Defect Incoming')
  ->with('jpn', 'QA受入パレット不良');
}

public function fetchDisplayIncomingMaterialDefect(Request $request)
{
  try {
    $first_month_ng = DB::SELECT("SELECT
      DATE_FORMAT( week_date, '%Y-%m' ) AS first_month 
      FROM
      weekly_calendars 
      WHERE
      fiscal_year = (
      SELECT
      fiscal_year 
      FROM
      weekly_calendars 
      WHERE
      week_date = DATE(
      NOW())) 
      ORDER BY
      week_date 
      LIMIT 1");
    $month_from = $request->get('month_from');
    $month_to = $request->get('month_to');
    if ($month_from == "") {
     if ($month_to == "") {
      $first = "DATE_FORMAT( NOW(), '%Y-%m' )";
      $last = "DATE_FORMAT( NOW(), '%Y-%m' )";
      $first_ng = "'".$first_month_ng[0]->first_month."'";
      $last_ng = "DATE_FORMAT( NOW(), '%Y-%m' )";
      $firstMonthTitle = date('M Y');
      $lastMonthTitle = date('M Y');
      $firstMonthTitleNg = date('M Y',strtotime($first_month_ng[0]->first_month));
      $lastMonthTitleNg = date('M Y');
    }else{
      $first = "DATE_FORMAT( NOW(), '%Y-%m' )";
      $last = "'".$month_to."'";
      $first_ng = "'".$first_month_ng[0]->first_month."'";
      $last_ng = "'".$month_to."'";
      $firstMonthTitle = date('M Y');
      $lastMonthTitle = date('M Y',strtotime($month_to));
      $firstMonthTitleNg = date('M Y',strtotime($first_month_ng[0]->first_month));
      $lastMonthTitleNg = date('M Y',strtotime($month_to));
    }
  }else{
   if ($month_to == "") {
    $first = "'".$month_from."'";
    $last = "DATE_FORMAT( NOW(), '%Y-%m' )";
    $first_ng = "'".$month_from."'";
    $last_ng = "DATE_FORMAT( NOW(), '%Y-%m' )";
    $firstMonthTitle = date('M Y',strtotime($month_from));
    $lastMonthTitle = date('M Y');
    $firstMonthTitleNg = date('M Y',strtotime($month_from));
    $lastMonthTitleNg = date('M Y');
  }else{
    $first = "'".$month_from."'";
    $last = "'".$month_to."'";
    $first_ng = "'".$month_from."'";
    $last_ng = "'".$month_to."'";
    $firstMonthTitle = date('M Y',strtotime($month_from));
    $lastMonthTitle = date('M Y',strtotime($month_to));
    $firstMonthTitleNg = date('M Y',strtotime($month_from));
    $lastMonthTitleNg = date('M Y',strtotime($month_to));
  }
}

$vendor = '';
if($request->get('vendor') != null){
  $vendors =  explode(",", $request->get('vendor'));
  for ($i=0; $i < count($vendors); $i++) {
    $vendor = $vendor."'".$vendors[$i]."'";
    if($i != (count($vendors)-1)){
      $vendor = $vendor.',';
    }
  }
  $vendorin = " and qa_incoming_logs.`vendor` in (".$vendor.") ";
  $vendorin_ng = " and qa_incoming_ng_logs.`vendor` in (".$vendor.") ";
}
else{
  $vendorin = "";
  $vendorin_ng = "";
}

$material = '';
if($request->get('material') != null){
  $materials =  explode(",", $request->get('material'));
  for ($i=0; $i < count($materials); $i++) {
    $material = $material."'".$materials[$i]."'";
    if($i != (count($materials)-1)){
      $material = $material.',';
    }
  }
  $materialin = " and qa_incoming_logs.`material_number` in (".$material.") ";
  $materialin_ng = " and qa_incoming_ng_logs.`material_number` in (".$material.") ";
}
else{
  $materialin = "";
  $materialin_ng = "";
}

$material_defect = DB::SELECT("SELECT
  ng_name,
  SUM( qty_ng ) AS count,
  SUM( total_ok ) AS count_ok,
  SUM( qa_incoming_ng_logs.qty_check ) AS count_check 
  FROM
  qa_incoming_ng_logs 
  JOIN qa_incoming_logs ON qa_incoming_logs.incoming_check_code = qa_incoming_ng_logs.incoming_check_code 
  WHERE
  DATE_FORMAT( qa_incoming_ng_logs.created_at, '%Y-%m' ) >=  ".$first." AND DATE_FORMAT( qa_incoming_ng_logs.created_at, '%Y-%m' ) <=  ".$last." 
  ".$vendorin."
  ".$materialin."
  GROUP BY
  ng_name
  ORDER BY
  count DESC,count_ok DESC,count_check DESC");

$material_defect_month = DB::SELECT("SELECT
  a.`month`,
  DATE_FORMAT( CONCAT( a.`month`, '-01' ), '%b %Y' ) AS month_name,
  sum( a.count_ok ) AS count_ok,
  sum( a.count_check ) AS count_check,
  sum( a.count_ng ) AS count_ng 
  FROM
  (
  SELECT
  DATE_FORMAT( qa_incoming_logs.created_at, '%Y-%m' ) AS `month`,
  SUM( total_ok ) AS count_ok,
  SUM( qa_incoming_logs.qty_check ) AS count_check,
  0 AS count_ng 
  FROM
  qa_incoming_logs 
  WHERE
  DATE_FORMAT( qa_incoming_logs.created_at, '%Y-%m' ) >= ".$first_ng." 
  AND DATE_FORMAT( qa_incoming_logs.created_at, '%Y-%m' ) <= ".$last_ng." 
  ".$vendorin."
  ".$materialin."
  GROUP BY
  DATE_FORMAT( qa_incoming_logs.created_at, '%Y-%m' ) UNION ALL
  SELECT
  DATE_FORMAT( qa_incoming_ng_logs.created_at, '%Y-%m' ) AS `month`,
  0 AS count_ok,
  0 AS count_check,
  COALESCE ( SUM( qa_incoming_ng_logs.qty_ng ), 0 ) AS count_ng 
  FROM
  qa_incoming_ng_logs 
  WHERE
  DATE_FORMAT( qa_incoming_ng_logs.created_at, '%Y-%m' ) >= ".$first_ng." 
  AND DATE_FORMAT( qa_incoming_ng_logs.created_at, '%Y-%m' ) <= ".$last_ng." 
  ".$vendorin_ng."
  ".$materialin_ng."
  GROUP BY
  DATE_FORMAT( qa_incoming_ng_logs.created_at, '%Y-%m' ) 
  ) a 
  GROUP BY
  a.`month`");

$material_status = DB::SELECT("SELECT
  SUM( a.total ) AS total,
  SUM( a.returnes ) AS `return`,
  SUM( a.scrapes ) AS `scrap`,
  SUM( a.repaires ) AS `repair` 
  FROM
  (
  SELECT
  SUM( qty_check ) AS total,
  0 AS returnes,
  0 AS scrapes,
  0 AS repaires 
  FROM
  qa_incoming_logs 
  WHERE
  DATE_FORMAT( created_at, '%Y-%m' ) >=  ".$first." AND DATE_FORMAT( created_at, '%Y-%m' ) <=  ".$last." ".$vendorin." ".$materialin." UNION ALL
  SELECT
  0 total,
  SUM( `return` ) AS returnes,
  0 AS scrapes,
  0 AS repaires 
  FROM
  qa_incoming_logs 
  WHERE
  DATE_FORMAT( created_at, '%Y-%m' ) >=  ".$first." AND DATE_FORMAT( created_at, '%Y-%m' ) <=  ".$last." ".$vendorin." ".$materialin." UNION ALL
  SELECT
  0 total,
  0 AS returnes,
  SUM( scrap ) AS scrapes,
  0 AS repaires 
  FROM
  qa_incoming_logs 
  WHERE
  DATE_FORMAT( created_at, '%Y-%m' ) >=  ".$first." AND DATE_FORMAT( created_at, '%Y-%m' ) <=  ".$last." ".$vendorin." ".$materialin." UNION ALL
  SELECT
  0 total,
  0 AS returnes,
  0 AS scrapes,
  SUM( `repair` ) AS repaires 
  FROM
  qa_incoming_logs 
  WHERE
  DATE_FORMAT( created_at, '%Y-%m' ) >=  ".$first." AND DATE_FORMAT( created_at, '%Y-%m' ) <=  ".$last." ".$vendorin." ".$materialin.") a");

$response = array(
  'status' => true,
  'material_defect' => $material_defect,
  'material_defect_month' => $material_defect_month,
  'material_status' => $material_status,
  'firstMonthTitle' => $firstMonthTitle,
  'lastMonthTitle' => $lastMonthTitle,
  'firstMonthTitleNg' => $firstMonthTitleNg,
  'lastMonthTitleNg' => $lastMonthTitleNg,
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

public function fetchDisplayIncomingMaterialSelect(Request $request)
{
  try {
    $vendor = '';
    if($request->get('vendor') != null){
      $vendors =  explode(",", $request->get('vendor'));
      for ($i=0; $i < count($vendors); $i++) {
        $vendor = $vendor."'".$vendors[$i]."'";
        if($i != (count($vendors)-1)){
          $vendor = $vendor.',';
        }
      }
      $vendorin = " where `vendor` in (".$vendor.") ";
    }
    else{
      $vendorin = "";
    }

    $material_select = DB::SELECT("SELECT DISTINCT
      ( material_number ),
      material_description 
      FROM
      qa_materials 
      ".$vendorin."
      ORDER BY
      material_description ASC");

    $response = array(
      'status' => true,
      'material_select' => $material_select,
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

public function fetchDisplayIncomingMaterialDefectDetail(Request $request)
{
  try {
    $month_from = $request->get('month_from');
    $month_to = $request->get('month_to');
    if ($month_from == "") {
     if ($month_to == "") {
      $first = "DATE_FORMAT( NOW(), '%Y-%m' )";
      $last = "DATE_FORMAT( NOW(), '%Y-%m' )";
    }else{
      $first = "DATE_FORMAT( NOW(), '%Y-%m' )";
      $last = "'".$month_to."'";
    }
  }else{
   if ($month_to == "") {
    $first = "'".$month_from."'";
    $last = "DATE_FORMAT( NOW(), '%Y-%m' )";
  }else{
    $first = "'".$month_from."'";
    $last = "'".$month_to."'";
  }
}

$vendor = '';
if($request->get('vendor') != null){
  $vendors =  explode(",", $request->get('vendor'));
  for ($i=0; $i < count($vendors); $i++) {
    $vendor = $vendor."'".$vendors[$i]."'";
    if($i != (count($vendors)-1)){
      $vendor = $vendor.',';
    }
  }
  $vendorin = " and `vendor` in (".$vendor.") ";
}
else{
  $vendorin = "";
}

$material = '';
if($request->get('material') != null){
  $materials =  explode(",", $request->get('material'));
  for ($i=0; $i < count($materials); $i++) {
    $material = $material."'".$materials[$i]."'";
    if($i != (count($materials)-1)){
      $material = $material.',';
    }
  }
  $materialin = " and `material_number` in (".$material.") ";
}
else{
  $materialin = "";
}

$detail = DB::SELECT("SELECT
            *,
  date(created_at) as created
  FROM
  qa_incoming_ng_logs 
  WHERE
  DATE_FORMAT( created_at, '%Y-%m' ) >= ".$first." 
  AND DATE_FORMAT( created_at, '%Y-%m' ) <= ".$last." ".$vendorin." ".$materialin." 
  AND ng_name = '".$request->get('categories')."'");

$response = array(
  'status' => true,
  'detail' => $detail,
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

public function fetchDisplayIncomingMaterialNgRateDetail(Request $request)
{
  try {

    $month_from = $request->get('month_from');
    $month_to = $request->get('month_to');
    if ($month_from == "") {
     if ($month_to == "") {
      $first = "DATE_FORMAT( NOW(), '%Y-%m' )";
      $last = "DATE_FORMAT( NOW(), '%Y-%m' )";
    }else{
      $first = "DATE_FORMAT( NOW(), '%Y-%m' )";
      $last = "'".$month_to."'";
    }
  }else{
   if ($month_to == "") {
    $first = "'".$month_from."'";
    $last = "DATE_FORMAT( NOW(), '%Y-%m' )";
  }else{
    $first = "'".$month_from."'";
    $last = "'".$month_to."'";
  }
}

$vendor = '';
if($request->get('vendor') != null){
  $vendors =  explode(",", $request->get('vendor'));
  for ($i=0; $i < count($vendors); $i++) {
    $vendor = $vendor."'".$vendors[$i]."'";
    if($i != (count($vendors)-1)){
      $vendor = $vendor.',';
    }
  }
  $vendorin = " and `vendor` in (".$vendor.") ";
}
else{
  $vendorin = "";
}

$material = '';
if($request->get('material') != null){
  $materials =  explode(",", $request->get('material'));
  for ($i=0; $i < count($materials); $i++) {
    $material = $material."'".$materials[$i]."'";
    if($i != (count($materials)-1)){
      $material = $material.',';
    }
  }
  $materialin = " and `material_number` in (".$material.") ";
}
else{
  $materialin = "";
}

$detail = DB::SELECT("SELECT
            *,
  date(created_at) as created
  FROM
  qa_incoming_ng_logs 
  WHERE
  DATE_FORMAT( created_at, '%Y-%m' ) >= ".$first." 
  AND DATE_FORMAT( created_at, '%Y-%m' ) <= ".$last." ".$vendorin." ".$materialin." 
  AND ng_name = '".$request->get('categories')."'");

$response = array(
  'status' => true,
  'detail' => $detail,
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

public function indexDisplayIncomingNgRate()
{
  $vendor = DB::SELECT("SELECT DISTINCT
    ( vendor ) 
    FROM
    qa_materials 
    ORDER BY
    LENGTH( vendor ) ASC");

  $material = DB::SELECT("SELECT DISTINCT
    ( material_number ),
    material_description 
    FROM
    qa_materials 
    ORDER BY
    material_description ASC");

  $firstData = DB::SELECT("SELECT
    DATE( created_at ) AS `first` 
    FROM
    qa_incoming_logs 
    LIMIT 1");

  return view('qa.index_ng_rate')
  ->with('title', 'Daily NG Rate Incoming Check QA')
  ->with('title_jp', '日次QA受入検査の不良率')
  ->with('location', $this->location)
  ->with('materials', $material)
  ->with('vendors', $vendor)
  ->with('firstData', $firstData)
  ->with('page', 'Daily NG Rate Incoming Check QA')
  ->with('jpn', '日次QA受入検査の不良率');
}

public function fetchDisplayIncomingNgRate(Request $request)
{
  try {

    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
      $last = "LAST_DAY(NOW())";
      $firstDateTitle = date('01 M Y');
      $lastDateTitle = date('d M Y');
    }else{
      $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
      $last = "'".$date_to."'";
      $firstDateTitle = date('01 M Y');
      $lastDateTitle = date('d M Y',strtotime($date_to));
    }
  }else{
   if ($date_to == "") {
    $first = "'".$date_from."'";
    $last = "LAST_DAY(NOW())";
    $firstDateTitle = date('d M Y',strtotime($date_from));
    $lastDateTitle = date('d M Y');
  }else{
    $first = "'".$date_from."'";
    $last = "'".$date_to."'";
    $firstDateTitle = date('d M Y',strtotime($date_from));
    $lastDateTitle = date('d M Y',strtotime($date_to));
  }
}

$vendor = '';
if($request->get('vendor') != null){
  $vendors =  explode(",", $request->get('vendor'));
  for ($i=0; $i < count($vendors); $i++) {
    $vendor = $vendor."'".$vendors[$i]."'";
    if($i != (count($vendors)-1)){
      $vendor = $vendor.',';
    }
  }
  $vendorin = " and `vendor` in (".$vendor.") ";
  $vendorin2 = " and qa_incoming_logs.`vendor` in (".$vendor.") ";
}
else{
  $vendorin = "";
  $vendorin2 = "";
}

$material = '';
if($request->get('material') != null){
  $materials =  explode(",", $request->get('material'));
  for ($i=0; $i < count($materials); $i++) {
    $material = $material."'".$materials[$i]."'";
    if($i != (count($materials)-1)){
      $material = $material.',';
    }
  }
  $materialin = " and `material_number` in (".$material.") ";
}
else{
  $materialin = "";
}

$ng_rate = DB::SELECT("SELECT
  DATE( created_at ) AS months,
  SUM( qty_check ) AS checkes,
  SUM( `return` ) AS returnes,
  SUM( `repair` ) AS repaires,
  ROUND((( SUM( `repair` )+ SUM( `return` ))/ SUM( qty_check )) * 100, 1 ) AS persen 
  FROM
  `qa_incoming_logs` 
  WHERE
  DATE( created_at ) >= ".$first."
  AND DATE( created_at ) <= ".$last."
  ".$vendorin." ".$materialin."
  GROUP BY
  DATE(
  created_at)");

$highest = DB::SELECT("SELECT
  qa_incoming_logs.vendor,
  SUM( qty_check ) AS qty_check,
  SUM( total_ng ) AS total_ng,
  TRUNCATE ( ( COALESCE ( SUM( total_ng )/ SUM( qty_check ), 0 )* 100 ), 3 ) AS ratio,
  vendor_short.vendor_shortname 
  FROM
  qa_incoming_logs
  JOIN ( SELECT DISTINCT ( vendor ), vendor_shortname FROM qa_materials ) vendor_short ON vendor_short.vendor = qa_incoming_logs.vendor 
  WHERE
  DATE( qa_incoming_logs.created_at ) BETWEEN ".$first."
  AND ".$last."
  ".$vendorin2."
  GROUP BY
  vendor,
  vendor_short.vendor_shortname 
  ORDER BY
  ratio DESC");

$response = array(
  'status' => true,
  'ng_rate' => $ng_rate,
  'highest' => $highest,
  'firstDateTitle' => $firstDateTitle,
  'lastDateTitle' => $lastDateTitle,
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

public function fetchDisplayIncomingNgRateDetail(Request $request)
{
  try {

    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
      $last = "LAST_DAY(NOW())";
    }else{
      $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
      $last = "'".$date_to."'";
    }
  }else{
   if ($date_to == "") {
    $first = "'".$date_from."'";
    $last = "LAST_DAY(NOW())";
  }else{
    $first = "'".$date_from."'";
    $last = "'".$date_to."'";
  }
}

$vendor = '';
if($request->get('vendor') != null){
  $vendors =  explode(",", $request->get('vendor'));
  for ($i=0; $i < count($vendors); $i++) {
    $vendor = $vendor."'".$vendors[$i]."'";
    if($i != (count($vendors)-1)){
      $vendor = $vendor.',';
    }
  }
  $vendorin = " and `vendor` in (".$vendor.") ";
}
else{
  $vendorin = "";
}

$material = '';
if($request->get('material') != null){
  $materials =  explode(",", $request->get('material'));
  for ($i=0; $i < count($materials); $i++) {
    $material = $material."'".$materials[$i]."'";
    if($i != (count($materials)-1)){
      $material = $material.',';
    }
  }
  $materialin = " and `material_number` in (".$material.") ";
}
else{
  $materialin = "";
}

$detail = DB::SELECT("SELECT
          *,DATE(created_at) as created
  FROM
  `qa_incoming_logs` 
  JOIN ( SELECT DISTINCT ( vendor ), vendor_shortname FROM qa_materials ) vendor_short ON vendor_short.vendor = qa_incoming_logs.vendor 
  WHERE
  DATE( created_at ) = '".$request->get('categories')."'
  ".$vendorin." ".$materialin."
  order by status_lot DESC");

$response = array(
  'status' => true,
  'detail' => $detail,
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

public function fetchDisplayIncomingNgRateDetailVendor(Request $request)
{
  try {

    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
      $last = "LAST_DAY(NOW())";
    }else{
      $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
      $last = "'".$date_to."'";
    }
  }else{
   if ($date_to == "") {
    $first = "'".$date_from."'";
    $last = "LAST_DAY(NOW())";
  }else{
    $first = "'".$date_from."'";
    $last = "'".$date_to."'";
  }
}

$detail = DB::SELECT("SELECT
          *,DATE(created_at) as created
  FROM
  `qa_incoming_logs` 
  JOIN ( SELECT DISTINCT ( vendor ), vendor_shortname FROM qa_materials ) vendor_short ON vendor_short.vendor = qa_incoming_logs.vendor 
  WHERE
  DATE( created_at ) >= ".$first."
  AND DATE( created_at ) <= ".$last."
  AND vendor_short.vendor_shortname = '".$request->get('vendor')."'
  order by status_lot DESC");

$response = array(
  'status' => true,
  'detail' => $detail,
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

public function indexReportIncomingCheck()
{
  $vendor = DB::SELECT("SELECT DISTINCT
    ( vendor ) 
    FROM
    qa_materials 
    ORDER BY
    LENGTH( vendor ) ASC");

  $material = DB::SELECT("SELECT DISTINCT
    ( material_number ),
    material_description 
    FROM
    qa_materials 
    ORDER BY
    material_description ASC");

  $inspection_level = DB::SELECT("SELECT DISTINCT(inspection_level) FROM `ympimis`.`qa_inspection_levels`");

  return view('qa.report_incoming_check')
  ->with('title', 'Report Incoming Check QA')
  ->with('title_jp', 'QA受入検査の報告')
  ->with('location', $this->location)
  ->with('materials', $material)
  ->with('inspection_levels', $inspection_level)
  ->with('vendors', $vendor)
  ->with('page', 'Report Incoming Check QA')
  ->with('jpn', 'QA受入検査の報告');
}

public function fetchReportIncomingCheck(Request $request)
{
  try {
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = date('Y-m-d',strtotime(date("Y-m-d", strtotime("-7 day"))));
      $last = date('Y-m-d');
    }else{
      $first = date('Y-m-d', strtotime($date_to.'-7 days'));
      $last = $date_to;
    }
  }else{
   if ($date_to == "") {
    $first = $date_from;
    $last = date('Y-m-d');
  }else{
    $first = $date_from;
    $last = $date_to;
  }
}

$vendor = '';
if($request->get('vendor') != null){
  $vendors =  explode(",", $request->get('vendor'));
  for ($i=0; $i < count($vendors); $i++) {
    $vendor = $vendor."'".$vendors[$i]."'";
    if($i != (count($vendors)-1)){
      $vendor = $vendor.',';
    }
  }
  $vendorin = " and `vendor` in (".$vendor.") ";
}
else{
  $vendorin = "";
}

$material = '';
if($request->get('material') != null){
  $materials =  explode(",", $request->get('material'));
  for ($i=0; $i < count($materials); $i++) {
    $material = $material."'".$materials[$i]."'";
    if($i != (count($materials)-1)){
      $material = $material.',';
    }
  }
  $materialin = " and `material_number` in (".$material.") ";
}
else{
  $materialin = "";
}

$location = '';
if($request->get('location') != null){
  $locations =  explode(",", $request->get('location'));
  for ($i=0; $i < count($locations); $i++) {
    $location = $location."'".$locations[$i]."'";
    if($i != (count($locations)-1)){
      $location = $location.',';
    }
  }
  $locationin = " and `location` in (".$location.") ";
}
else{
  $locationin = "";
}

$inspection_level = '';
if($request->get('inspection_level') != null){
  $inspection_levels =  explode(",", $request->get('inspection_level'));
  for ($i=0; $i < count($inspection_levels); $i++) {
    $inspection_level = $inspection_level."'".$inspection_levels[$i]."'";
    if($i != (count($inspection_levels)-1)){
      $inspection_level = $inspection_level.',';
    }
  }
  $inspection_levelin = " and `inspection_level` in (".$inspection_level.") ";
}
else{
  $inspection_levelin = "";
}

$datas = DB::SELECT("SELECT
  qa_incoming_logs.id as id_log,
  qa_incoming_logs.location,
  employee_syncs.employee_id,
  employee_syncs.name,
  qa_incoming_logs.lot_number,
  qa_incoming_logs.material_number,
  qa_incoming_logs.material_description,
  qa_incoming_logs.vendor,
  qa_incoming_logs.invoice,
  qa_incoming_logs.inspection_level,
  qa_incoming_logs.`repair`,
  qa_incoming_logs.`return`,
  qa_incoming_logs.`qty_rec`,
  qa_incoming_logs.`qty_check`,
  qa_incoming_logs.`total_ok`,
  qa_incoming_logs.`total_ng`,
  IF
  (
  qa_incoming_logs.material_number = 'VGN366Z' 
  OR qa_incoming_logs.material_number = 'VGN365Z' 
  OR qa_incoming_logs.material_number = 'W53590Z' 
  OR qa_incoming_logs.material_number = 'W53580Z' 
  OR qa_incoming_logs.material_number = 'WN7115Z' 
  OR qa_incoming_logs.material_number = 'ZE0830Z',
  ROUND(((qa_incoming_logs.total_ng_pcs/qa_incoming_logs.qty_check)*100),1),
  qa_incoming_logs.`ng_ratio`) as ng_ratio,
  qa_incoming_logs.`status_lot`,
  qa_incoming_logs.`total_ng_pcs`,
  qa_incoming_logs.`hpl`,
  qa_incoming_logs.`serial_number`,
  qa_incoming_logs.`note_all`,
  qa_incoming_logs.created_at AS created,
  date(qa_incoming_logs.created_at) AS date_created,
  DATE_FORMAT(qa_incoming_logs.created_at,'%H:%i:%s') AS time_created,
  ( SELECT GROUP_CONCAT( ng_name SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_name,
  ( SELECT GROUP_CONCAT( area SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS area,
  ( SELECT GROUP_CONCAT( qty_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_qty,
  ( SELECT GROUP_CONCAT( status_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS status_ng,
  ( SELECT GROUP_CONCAT( note_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS note_ng 
  FROM
  qa_incoming_logs
  JOIN employee_syncs ON employee_syncs.employee_id = qa_incoming_logs.inspector_id 
  WHERE
  DATE( qa_incoming_logs.created_at ) >= '".$first."'
  AND DATE( qa_incoming_logs.created_at ) <= '".$last."'
  ".$locationin." ".$inspection_levelin." ".$materialin." ".$vendorin." ");


$response = array(
  'status' => true,
  'datas' => $datas,
  'dateTitle' => date('d-M-Y',strtotime($first)).' - '.date('d-M-Y',strtotime($last))
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

public function indexReportLotOut()
{
  $vendor = DB::SELECT("SELECT DISTINCT
    ( vendor ) 
    FROM
    qa_materials 
    ORDER BY
    LENGTH( vendor ) ASC");

  $material = DB::SELECT("SELECT DISTINCT
    ( material_number ),
    material_description 
    FROM
    qa_materials 
    ORDER BY
    material_description ASC");

  $inspection_level = DB::SELECT("SELECT DISTINCT(inspection_level) FROM `ympimis`.`qa_inspection_levels`");

  return view('qa.report_lot_out')
  ->with('title', 'Report Lot Out Incoming Check QA')
  ->with('title_jp', 'QA受入検査ロットアウトの報告')
  ->with('location', $this->location)
  ->with('materials', $material)
  ->with('inspection_levels', $inspection_level)
  ->with('vendors', $vendor)
  ->with('page', 'Report Lot Out Incoming Check QA')
  ->with('jpn', 'QA受入検査ロットアウトの報告');
}

public function fetchReportLotOut(Request $request)
{
  try {
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
      $last = "LAST_DAY(NOW())";
    }else{
      $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
      $last = "'".$date_to."'";
    }
  }else{
   if ($date_to == "") {
    $first = "'".$date_from."'";
    $last = "LAST_DAY(NOW())";
  }else{
    $first = "'".$date_from."'";
    $last = "'".$date_to."'";
  }
}

$vendor = '';
if($request->get('vendor') != null){
  $vendors =  explode(",", $request->get('vendor'));
  for ($i=0; $i < count($vendors); $i++) {
    $vendor = $vendor."'".$vendors[$i]."'";
    if($i != (count($vendors)-1)){
      $vendor = $vendor.',';
    }
  }
  $vendorin = " and `vendor` in (".$vendor.") ";
}
else{
  $vendorin = "";
}

$material = '';
if($request->get('material') != null){
  $materials =  explode(",", $request->get('material'));
  for ($i=0; $i < count($materials); $i++) {
    $material = $material."'".$materials[$i]."'";
    if($i != (count($materials)-1)){
      $material = $material.',';
    }
  }
  $materialin = " and `material_number` in (".$material.") ";
}
else{
  $materialin = "";
}

$location = '';
if($request->get('location') != null){
  $locations =  explode(",", $request->get('location'));
  for ($i=0; $i < count($locations); $i++) {
    $location = $location."'".$locations[$i]."'";
    if($i != (count($locations)-1)){
      $location = $location.',';
    }
  }
  $locationin = " and `location` in (".$location.") ";
}
else{
  $locationin = "";
}

$inspection_level = '';
if($request->get('inspection_level') != null){
  $inspection_levels =  explode(",", $request->get('inspection_level'));
  for ($i=0; $i < count($inspection_levels); $i++) {
    $inspection_level = $inspection_level."'".$inspection_levels[$i]."'";
    if($i != (count($inspection_levels)-1)){
      $inspection_level = $inspection_level.',';
    }
  }
  $inspection_levelin = " and `inspection_level` in (".$inspection_level.") ";
}
else{
  $inspection_levelin = "";
}

$datas = DB::SELECT("SELECT
  qa_incoming_logs.id as id_log,
  qa_incoming_logs.location,
  employee_syncs.employee_id,
  employee_syncs.name,
  qa_incoming_logs.material_number,
  qa_incoming_logs.lot_number,
  qa_incoming_logs.material_description,
  qa_incoming_logs.vendor,
  qa_incoming_logs.invoice,
  qa_incoming_logs.inspection_level,
  qa_incoming_logs.`repair`,
  qa_incoming_logs.`return`,
  qa_incoming_logs.`qty_rec`,
  qa_incoming_logs.`qty_check`,
  qa_incoming_logs.`total_ok`,
  qa_incoming_logs.`total_ng`,
  qa_incoming_logs.`ng_ratio`,
  qa_incoming_logs.`status_lot`,
  qa_incoming_logs.`report_evidence`,
  qa_incoming_logs.`send_email_status`,
  qa_incoming_logs.`send_email_at`,
  DATE( qa_incoming_logs.created_at ) AS created,
  ( SELECT GROUP_CONCAT( ng_name SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_name,
  ( SELECT GROUP_CONCAT( qty_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_qty,
  ( SELECT GROUP_CONCAT( status_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS status_ng,
  ( SELECT GROUP_CONCAT( note_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS note_ng 
  FROM
  qa_incoming_logs
  JOIN employee_syncs ON employee_syncs.employee_id = qa_incoming_logs.inspector_id 
  WHERE
  DATE( qa_incoming_logs.created_at ) >= ".$first." 
  AND DATE( qa_incoming_logs.created_at ) <= ".$last."
  AND status_lot = 'Lot Out'
  ".$locationin." ".$inspection_levelin." ".$materialin." ".$vendorin." ");

$response = array(
  'status' => true,
  'datas' => $datas,
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

public function inputReportLotOut(Request $request)
{
  try {
    $id_log = $request->get('id_log');
    $report_evidence = $request->get('report_evidence');
    $log = QaIncomingLog::where('id',$id_log)->first();
    $log->report_evidence = $report_evidence;
    $log->save();

    $response = array(
      'status' => true,
      'message' => 'Success Input Evidence'
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

public function excelReportIncomingCheck(Request $request)
{
  try {
    if ($request->get('publish') != null) {
      $stattsss = 'no_merge';
    }else{
      $stattsss = 'merge';
    }
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
      $last = "LAST_DAY(NOW())";
    }else{
      $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
      $last = "'".$date_to."'";
    }
  }else{
   if ($date_to == "") {
    $first = "'".$date_from."'";
    $last = "LAST_DAY(NOW())";
  }else{
    $first = "'".$date_from."'";
    $last = "'".$date_to."'";
  }
}

$vendor = '';
if($request->get('vendor') != null){
  $vendors =  explode(",", $request->get('vendor'));
  for ($i=0; $i < count($vendors); $i++) {
    $vendor = $vendor."'".$vendors[$i]."'";
    if($i != (count($vendors)-1)){
      $vendor = $vendor.',';
    }
  }
  $vendorin = " and `vendor` in (".$vendor.") ";
}
else{
  $vendorin = "";
}

$material = '';
if($request->get('material') != null){
  $materials =  explode(",", $request->get('material'));
  for ($i=0; $i < count($materials); $i++) {
    $material = $material."'".$materials[$i]."'";
    if($i != (count($materials)-1)){
      $material = $material.',';
    }
  }
  $materialin = " and `material_number` in (".$material.") ";
}
else{
  $materialin = "";
}

$location = '';
if($request->get('location') != null){
  $locations =  explode(",", $request->get('location'));
  for ($i=0; $i < count($locations); $i++) {
    $location = $location."'".$locations[$i]."'";
    if($i != (count($locations)-1)){
      $location = $location.',';
    }
  }
  $locationin = " and `location` in (".$location.") ";
}
else{
  $locationin = "";
}

$inspection_level = '';
if($request->get('inspection_level') != null){
  $inspection_levels =  explode(",", $request->get('inspection_level'));
  for ($i=0; $i < count($inspection_levels); $i++) {
    $inspection_level = $inspection_level."'".$inspection_levels[$i]."'";
    if($i != (count($inspection_levels)-1)){
      $inspection_level = $inspection_level.',';
    }
  }
  $inspection_levelin = " and `inspection_level` in (".$inspection_level.") ";
}
else{
  $inspection_levelin = "";
}


        // var_dump($data);
$datas = DB::SELECT("SELECT
  qa_incoming_logs.id as id_log,
  qa_incoming_logs.location,
  employee_syncs.employee_id,
  employee_syncs.name,
  qa_incoming_logs.lot_number,
  qa_incoming_logs.material_number,
  qa_incoming_logs.material_description,
  qa_incoming_logs.vendor,
  qa_incoming_logs.invoice,
  qa_incoming_logs.inspection_level,
  qa_incoming_logs.`repair`,
  qa_incoming_logs.`return`,
  qa_incoming_logs.`qty_rec`,
  qa_incoming_logs.`qty_check`,
  qa_incoming_logs.`total_ok`,
  qa_incoming_logs.`total_ng`,
  IF
  (
  qa_incoming_logs.material_number = 'VGN366Z' 
  OR qa_incoming_logs.material_number = 'VGN365Z' 
  OR qa_incoming_logs.material_number = 'W53590Z' 
  OR qa_incoming_logs.material_number = 'W53580Z' 
  OR qa_incoming_logs.material_number = 'WN7115Z' 
  OR qa_incoming_logs.material_number = 'ZE0830Z',
  ROUND(((qa_incoming_logs.total_ng_pcs/qa_incoming_logs.qty_check)*100),1),
  qa_incoming_logs.`ng_ratio`) as ng_ratio,
  qa_incoming_logs.`status_lot`,
  qa_incoming_logs.`total_ng_pcs`,
  qa_incoming_logs.`hpl`,
  qa_incoming_logs.`serial_number`,
  qa_incoming_logs.`note_all`,
  qa_incoming_logs.created_at AS created,
  date(qa_incoming_logs.created_at) AS date_created,
  DATE_FORMAT(qa_incoming_logs.created_at,'%H:%i:%s') AS time_created,
  ( SELECT GROUP_CONCAT( ng_name SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_name,
  ( SELECT GROUP_CONCAT( area SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS area,
  ( SELECT GROUP_CONCAT( qty_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_qty,
  ( SELECT GROUP_CONCAT( status_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS status_ng,
  ( SELECT GROUP_CONCAT( note_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS note_ng 
  FROM
  qa_incoming_logs
  JOIN employee_syncs ON employee_syncs.employee_id = qa_incoming_logs.inspector_id 
  WHERE
  DATE( qa_incoming_logs.created_at ) >= ".$first." 
  AND DATE( qa_incoming_logs.created_at ) <= ".$last."
  ".$locationin." ".$inspection_levelin." ".$materialin." ".$vendorin." 
  ");

$data = array(
  'datas' => $datas
);

if ($stattsss == 'no_merge') {
  ob_clean();
  Excel::create('Incoming Check QA Report Without Merge', function($excel) use ($data){
    $excel->sheet('Incoming Check QA', function($sheet) use ($data) {
      return $sheet->loadView('qa.excel_incoming_check_without_merge', $data);
    });
  })->export('xlsx');
}else{
  ob_clean();
  Excel::create('Incoming Check QA Report With Merge', function($excel) use ($data){
    $excel->sheet('Incoming Check QA', function($sheet) use ($data) {
      return $sheet->loadView('qa.excel_incoming_check', $data);
    });
  })->export('xlsx');
}
return redirect()->route('report_incoming_qa')->with('status','Success Export Data');
} catch (\Exception $e) {
  return redirect()->route('report_incoming_qa')->with('error',$e->getMessage());
}
}

public function fetchDetailRecord(Request $request)
{
  try {
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = "DATE_FORMAT( NOW() - INTERVAL 7 DAY, '%Y-%m-%d' ) ";
      $last = "DATE(NOW())";
    }else{
      $first = "DATE_FORMAT( NOW() - INTERVAL 7 DAY, '%Y-%m-%d' ) ";
      $last = "'".$date_to."'";
    }
  }else{
   if ($date_to == "") {
    $first = "'".$date_from."'";
    $last = "DATE(NOW())";
  }else{
    $first = "'".$date_from."'";
    $last = "'".$date_to."'";
  }
}

$vendor = '';
if($request->get('vendor') != null){
  $vendors =  explode(",", $request->get('vendor'));
  for ($i=0; $i < count($vendors); $i++) {
    $vendor = $vendor."'".$vendors[$i]."'";
    if($i != (count($vendors)-1)){
      $vendor = $vendor.',';
    }
  }
  $vendorin = " and `vendor` in (".$vendor.") ";
}
else{
  $vendorin = "";
}

$material = '';
if($request->get('material') != null){
  $materials =  explode(",", $request->get('material'));
  for ($i=0; $i < count($materials); $i++) {
    $material = $material."'".$materials[$i]."'";
    if($i != (count($materials)-1)){
      $material = $material.',';
    }
  }
  $materialin = " and `material_number` in (".$material.") ";
}
else{
  $materialin = "";
}
$detail = DB::SELECT("
  SELECT
  qa_incoming_logs.location,
  qa_incoming_logs.lot_number,
  employee_syncs.employee_id,
  employee_syncs.name,
  qa_incoming_logs.material_number,
  qa_incoming_logs.material_description,
  qa_incoming_logs.vendor,
  qa_incoming_logs.invoice,
  qa_incoming_logs.inspection_level,
  qa_incoming_logs.`repair`,
  qa_incoming_logs.`return`,
  qa_incoming_logs.`qty_rec`,
  qa_incoming_logs.`qty_check`,
  qa_incoming_logs.`total_ok`,
  qa_incoming_logs.`total_ng`,
  qa_incoming_logs.`ng_ratio`,
  qa_incoming_logs.`status_lot`,
  DATE( qa_incoming_logs.created_at ) AS created,
  ( SELECT GROUP_CONCAT( ng_name SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_name,
  ( SELECT GROUP_CONCAT( qty_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_qty,
  ( SELECT GROUP_CONCAT( status_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS status_ng,
  ( SELECT GROUP_CONCAT( note_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS note_ng 
  FROM
  qa_incoming_logs
  JOIN employee_syncs ON employee_syncs.employee_id = qa_incoming_logs.inspector_id 
  WHERE
  DATE( qa_incoming_logs.created_at ) >= ".$first."
  AND DATE( qa_incoming_logs.created_at ) <= ".$last."
  ".$materialin." ".$vendorin."
  ORDER BY
  qa_incoming_logs.material_number,
  qa_incoming_logs.created_at desc,
  qa_incoming_logs.lot_number");

$response = array(
  'status' => true,
  'detail' => $detail,
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

public function fetchReportIncomingCheckEdit(Request $request)
{
  try {
    $datas = DB::SELECT("SELECT
      qa_incoming_logs.id as id_log,
      qa_incoming_logs.incoming_check_code,
      qa_incoming_logs.location,
      qa_incoming_logs.lot_number,
      employee_syncs.employee_id,
      employee_syncs.name,
      qa_incoming_logs.material_number,
      qa_incoming_logs.material_description,
      qa_incoming_logs.vendor,
      qa_incoming_logs.invoice,
      qa_incoming_logs.inspection_level,
      qa_incoming_logs.`repair`,
      DATE(qa_incoming_logs.created_at) as date,
      qa_incoming_logs.`return`,
      qa_incoming_logs.`qty_rec`,
      qa_incoming_logs.`qty_check`,
      qa_incoming_logs.`total_ok`,
      qa_incoming_logs.`total_ng`,
      qa_incoming_logs.`ng_ratio`,
      qa_incoming_logs.`status_lot`,
      qa_incoming_logs.`report_evidence`,
      DATE( qa_incoming_logs.created_at ) AS created,
      ( SELECT GROUP_CONCAT( ng_name SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_name,
      ( SELECT GROUP_CONCAT( qty_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_qty,
      ( SELECT GROUP_CONCAT( status_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS status_ng,
      ( SELECT GROUP_CONCAT( note_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS note_ng 
      FROM
      qa_incoming_logs
      JOIN employee_syncs ON employee_syncs.employee_id = qa_incoming_logs.inspector_id 
      WHERE
      qa_incoming_logs.id = '".$request->get('id')."'
      ORDER BY
      qa_incoming_logs.material_number,
      qa_incoming_logs.created_at desc,
      qa_incoming_logs.lot_number");

    $response = array(
      'status' => true,
      'datas' => $datas,
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

public function updateReportIncomingCheck(Request $request)
{
  try {
    $material = $request->get('material');
    $invoice = $request->get('invoice');
    $inspection_level = $request->get('inspection_level');
    $lot_number = $request->get('lot_number');
    $qty_rec = $request->get('qty_rec');
    $status_lot = $request->get('status_lot');
    $incoming_check_code = $request->get('incoming_check_code');
    $id_log = $request->get('id_log');

    $log = QaIncomingLog::where('id',$id_log)->first();
    $materials = QaMaterial::where('material_number',$material)->first();
    $log->material_number = $materials->material_number;
    $log->material_description = $materials->material_description;
    $log->vendor = $materials->vendor;
    $log->lot_number = $lot_number;
    $log->invoice = $invoice;
    $log->inspection_level = $inspection_level;
    $log->qty_rec = $qty_rec;
    $log->status_lot = $status_lot;

    $ng_log = QaIncomingNgLog::where('incoming_check_log_id',$id_log)->get();
    if (count($ng_log) > 0) {
      foreach($ng_log as $ng_logs){
        $nglogs = QaIncomingNgLog::where('id',$ng_logs->id)->first();
        $nglogs->material_number = $materials->material_number;
        $nglogs->material_description = $materials->material_description;
        $nglogs->vendor = $materials->vendor;
        $nglogs->lot_number = $lot_number;
        $nglogs->invoice = $invoice;
        $nglogs->inspection_level = $inspection_level;
        $nglogs->qty_rec = $qty_rec;
        $nglogs->save();
      }
    }

    $log->save();

    $response = array(
      'status' => true,
      'message' => 'Success Update Data'
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

public function deleteReportIncomingCheck(Request $request)
{
  try {
    $log = QaIncomingLog::where('id',$request->get('id'))->forceDelete();
    $ng_log = QaIncomingNgLog::where('incoming_check_log_id',$request->get('id'))->get();
    if (count($ng_log) > 0) {
      foreach ($ng_log as $key) {
        QaIncomingNgLog::where('id',$key->id)->forceDelete();
      }
    }

    $response = array(
      'status' => true,
      'message' => 'Success Delete Data'
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

public function sendReportLotOut(Request $request)
{
  try {
    $id = $request->get('id');
    $log = QaIncomingLog::where('qa_incoming_logs.id',$id)->first();
    $log->send_email_status = 'Sent';
    $log->send_email_at = date('Y-m-d H:i:s');

    $datas = DB::SELECT("SELECT
      qa_incoming_logs.id as id_log,
      qa_incoming_logs.incoming_check_code,
      qa_incoming_logs.location,
      qa_incoming_logs.lot_number,
      employee_syncs.employee_id,
      employee_syncs.name,
      qa_incoming_logs.material_number,
      qa_incoming_logs.material_description,
      qa_incoming_logs.vendor,
      qa_incoming_logs.invoice,
      qa_incoming_logs.inspection_level,
      qa_incoming_logs.`repair`,
      DATE(qa_incoming_logs.created_at) as date,
      qa_incoming_logs.`return`,
      qa_incoming_logs.`qty_rec`,
      qa_incoming_logs.`qty_check`,
      qa_incoming_logs.`total_ok`,
      qa_incoming_logs.`total_ng`,
      qa_incoming_logs.`ng_ratio`,
      qa_incoming_logs.`status_lot`,
      qa_incoming_logs.`report_evidence`,
      DATE( qa_incoming_logs.created_at ) AS created,
      ( SELECT GROUP_CONCAT( ng_name SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_name,
      ( SELECT GROUP_CONCAT( qty_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS ng_qty,
      ( SELECT GROUP_CONCAT( status_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS status_ng,
      ( SELECT GROUP_CONCAT( note_ng SEPARATOR '_' ) FROM qa_incoming_ng_logs WHERE qa_incoming_ng_logs.incoming_check_code = qa_incoming_logs.incoming_check_code ) AS note_ng 
      FROM
      qa_incoming_logs
      JOIN employee_syncs ON employee_syncs.employee_id = qa_incoming_logs.inspector_id 
      WHERE
      qa_incoming_logs.id = '".$request->get('id')."'
      ORDER BY
      qa_incoming_logs.material_number,
      qa_incoming_logs.created_at desc,
      qa_incoming_logs.lot_number");

    $mailto = QaMaterial::select('email')->where('material_number',$log->material_number)->first();
    $mail_to = $mailto->email;

    $cc = [];
    $cc[0] = 'nasiqul.ibat@music.yamaha.com';
        // $cc[0] = 'yayuk.wahyuni@music.yamaha.com';
        // $cc[1] = 'agustina.hayati@music.yamaha.com';
        // $cc[2] = 'ratri.sulistyorini@music.yamaha.com';
        // $cc[3] = 'abdissalam.saidi@music.yamaha.com';

    $bcc = [];
    $bcc[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';

    // Mail::to($mail_to)->cc($cc,'CC')->bcc($bcc,'BCC')->send(new SendEmail($datas, 'qa_incoming_check'));

    $log->save();

    $response = array(
      'status' => true,
      'message' => 'Email Berhasil Terkirim'
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

public function indexReportOutgoingVendor($vendor)
{

  if ($vendor == 'true') {
    $title = 'Report Vendor Final Inspection - PT. TRUE INDONESIA';
    $vendor_shortname = 'TRUE';
    $title_jp = '';
  }else if($vendor == 'kbi'){
    $title = 'Report Vendor Final Inspection - PT. KBI';
    $vendor_shortname = 'KYORAKU';
    $title_jp = '';
  }else if($vendor == 'arisa'){
    $title = 'Report Vendor Final Inspection - PT. ARISAMANDIRI PRATAMA';
    $vendor_shortname = 'ARISA';
    $title_jp = '';
  }

  $material = DB::SELECT("SELECT DISTINCT
    ( material_number ),
    material_description 
    FROM
    qa_materials 
    where vendor_shortname = '".$vendor_shortname."'
    ORDER BY
    material_description ASC");

  return view('qa.report_outgoing_vendor')
  ->with('title', $title)
  ->with('title_jp', $title_jp)
  ->with('material', $material)
  ->with('vendor', $vendor)
  ->with('vendor_shortname', $vendor_shortname);
}

public function fetchReportOutgoingVendor(Request $request)
{
  try {
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = date('Y-m-d',strtotime('-1 week'));
      $last = date('Y-m-d');
    }else{
      $first = date('Y-m-d',strtotime('-1 week'));
      $last = $date_to;
    }
  }else{
   if ($date_to == "") {
    $first = $date_from;
    $last = date('Y-m-d');
  }else{
    $first = $date_from;
    $last = $date_to;
  }
}

$datas = DB::CONNECTION('ympimis_online')
->table('qa_outgoing_vendors')
->where('vendor_shortname',$request->get('vendor_shortname'));

$materialin = [];
if($request->get('material') != null){
  $materials =  explode(",", $request->get('material'));
  for ($i=0; $i < count($materials); $i++) {
    array_push($materialin, $materials[$i]);
  }
}
else{
  $materialin = [];
}

if (count($materialin) > 0) {
  $datas = $datas->wherein('material_number',$materialin);
}
$datas = $datas->orderBy('id','desc')
->whereDate('created_at','>=',$first)
->whereDate('created_at','<=',$last)
->get();

$response = array(
  'status' => true,
  'datas' => $datas
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

public function indexLotOutVendor()
{
  $title = 'Vendor Lot Out Monitoring';
  $title_jp = '業者ロットアウトの管理';
  return view('qa.vendor_lot_out_monitoring')
  ->with('title', $title)
  ->with('title_jp', $title_jp);
}

public function fetchLotOutVendor(Request $request)
{
  try {
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == NULL) {
     if ($date_to == NULL) {
      $first = date('Y-m-01');
      $last = date('Y-m-d');
    }else{
      $first = date('Y-m-01');
      $last = $date_to;
    }
  }else{
   if ($date_to == NULL) {
    $first = $date_from;
    $last = date('Y-m-d');
  }else{
    $first = $date_from;
    $last = $date_to;
  }
}

$datas = DB::connection('ympimis_online')
->select("SELECT
          * 
  FROM
  `qa_outgoing_vendors` 
  WHERE
  lot_status = 'LOT OUT' 
  AND vendor_shortname = 'TRUE'
  AND check_date >= '".$first."'
  AND check_date <= '".$last."'
  AND serial_number NOT IN ( SELECT DISTINCT ( serial_number ) FROM qa_outgoing_vendor_rechecks WHERE vendor_shortname = 'TRUE' )
  UNION ALL
  SELECT
          * 
  FROM
  `qa_outgoing_vendors` 
  WHERE
  lot_status = 'LOT OUT' 
  AND vendor_shortname = 'ARISA'
  AND check_date >= '".$first."'
  AND check_date <= '".$last."'
  AND serial_number NOT IN (
  SELECT DISTINCT
  ( serial_number ) 
  FROM
  qa_outgoing_vendor_rechecks 
  WHERE
  vendor_shortname = 'ARISA')");

$data_recheck = DB::connection('ympimis_online')
->select("SELECT
          * 
  FROM
  `qa_outgoing_vendor_rechecks` 
  WHERE
  vendor_shortname = 'TRUE'
  AND check_date >= '".$first."'
  AND check_date <='".$last."'
  UNION ALL
  SELECT
          * 
  FROM
  `qa_outgoing_vendor_rechecks` 
  WHERE
  vendor_shortname = 'ARISA'
  AND check_date >= '".$first."'
  AND check_date <='".$last."'");

$count = DB::connection('ympimis_online')
->select("SELECT
  a.check_date,
  a.check_dates,
  SUM( a.belum_true ) as belum_true,
  SUM( a.sudah_true ) as sudah_true,
  SUM( a.belum_arisa ) as belum_arisa,
  SUM( a.sudah_arisa ) as sudah_arisa 
  FROM
  (
  SELECT
  check_date,
  DATE_FORMAT( check_date, '%d %b %Y' ) AS check_dates,
  count(
  DISTINCT ( serial_number )) AS belum_true,
  ( SELECT count( DISTINCT ( serial_number )) FROM qa_outgoing_vendor_rechecks WHERE serial_number = qa_outgoing_vendors.serial_number ) AS sudah_true,
  0 AS belum_arisa,
  0 AS sudah_arisa 
  FROM
  `qa_outgoing_vendors` 
  WHERE
  lot_status = 'LOT OUT' 
  AND vendor_shortname = 'TRUE' 
  AND check_date >= '".$first."' 
  AND check_date <= '".$last."' 
  GROUP BY
  check_date UNION ALL
  SELECT
  check_date,
  DATE_FORMAT( check_date, '%d %b %Y' ) AS check_dates,
  0 AS belum_true,
  0 AS sudah_true,
  count(
  DISTINCT ( serial_number )) AS belum_arisa,
  ( SELECT count( DISTINCT ( serial_number )) FROM qa_outgoing_vendor_rechecks WHERE serial_number = qa_outgoing_vendors.serial_number ) AS sudah_arisa 
  FROM
  `qa_outgoing_vendors` 
  WHERE
  lot_status = 'LOT OUT' 
  AND vendor_shortname = 'ARISA' 
  AND check_date >= '".$first."' 
  AND check_date <= '".$last."' 
  GROUP BY
  check_date 
  ) a 
  GROUP BY
  a.check_date,
  a.check_dates");

$periode = strtoupper(date('d M Y',strtotime($first)).' - '.date('d M Y',strtotime($last)));
$response = array(
  'status' => true,
  'data_recheck' => $data_recheck,
  'datas' => $datas,
  'count' => $count,
  'periode' => $periode,
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

public function indexDisplayQaMeeting()
{

  $vendor = QaMaterial::select('vendor','vendor_shortname')->distinct()->get();
  $material = QaMaterial::select('material_number','material_description')->distinct()->get();
  $title = 'QA Meeting';
  $title_jp = 'QA会議';
  return view('qa.qa_meeting')
  ->with('title', $title)
  ->with('title_jp', $title_jp)
  ->with('vendor', $vendor)
  ->with('vendo3', $vendor)
  ->with('material', $material)
  ->with('vendor2', $vendor)
  ->with('vendor3', $vendor);
}

public function fetchDisplayQaMeetingWorstVendor(Request $request)
{
  try {
    if ($request->get('month') == '') {
      $month = date('Y-m',strtotime('- 30 days'));
      $monthTitle = date('M Y',strtotime($month));
    }else{
      $month = $request->get('month');
      $monthTitle = date('M Y',strtotime($month));
    }

    $vendor = '';
    if($request->get('vendor') != null){
      $vendors =  explode(",", $request->get('vendor'));
      for ($i=0; $i < count($vendors); $i++) {
        $vendor = $vendor."'".$vendors[$i]."'";
        if($i != (count($vendors)-1)){
          $vendor = $vendor.',';
        }
      }
      $vendorin = " and material.`vendor_shortname` in (".$vendor.") ";
      $vendorin2 = " and qa_materials.`vendor_shortname` in (".$vendor.") ";
    }
    else{
      $vendorin = "";
      $vendorin2 = "";
    }

    $union_incoming = "(
    SELECT
    qa_incoming_logs.vendor,
    material.vendor_shortname,
    sum( qty_rec ) AS qty_rec,
    sum( qty_check ) AS qty_check,
    COALESCE ( SUM( b.`repair` ), 0 ) AS `repair`,
    COALESCE ( SUM( b.`return` ), 0 ) AS `return`,
    COALESCE ( SUM( b.total ), 0 ) AS total_ng,
    ROUND(( COALESCE ( SUM( b.total ), 0 )/ sum( qty_check ))* 100, 2 ) AS ratio 
    FROM
    qa_incoming_logs
    JOIN ( SELECT DISTINCT ( vendor ), vendor_shortname FROM qa_materials ) material ON material.vendor = qa_incoming_logs.vendor
    LEFT JOIN (
    SELECT
    a.incoming_check_code,
    sum( a.`repair` ) AS `repair`,
    sum( a.`return` ) AS `return`,
    sum( a.`return` ) + sum( a.`repair` ) AS total 
    FROM
    (
    SELECT
    incoming_check_code,
    sum( qty_ng ) `repair`,
    0 AS `return` 
    FROM
    qa_incoming_ng_logs 
    WHERE
    status_ng = 'Repair' 
    GROUP BY
    incoming_check_code,
    `return` UNION ALL
    SELECT
    incoming_check_code,
    0 `repair`,
    sum( qty_ng ) AS `return` 
    FROM
    qa_incoming_ng_logs 
    WHERE
    status_ng = 'Return' 
    GROUP BY
    incoming_check_code,
    `repair` 
    ) a 
    GROUP BY
    a.incoming_check_code 
    ) AS b ON b.incoming_check_code = qa_incoming_logs.incoming_check_code 
    WHERE
    DATE_FORMAT( created_at, '%Y-%m' ) = '".$month."' 
    ".$vendorin."
    GROUP BY
    qa_incoming_logs.vendor,
    material.vendor_shortname 
  )";
  $union_vendor = "(SELECT
  a.vendor,
  a.vendor_shortname,
  sum( a.qty_check ) AS qty_rec,
  sum( a.qty_check ) + COALESCE ( plan.qty, 0 ) AS qty_check,
  0 AS `repair`,
  sum( a.qty_ng ) AS `return`,
  sum( a.qty_ng ) AS `total_ng`,
  ROUND(( sum( a.qty_ng )/ ( sum( a.qty_check ) + COALESCE ( plan.qty, 0 )))* 100, 2 ) AS ratio 
  FROM
  (
  SELECT
  vendors.material_number,
  sum( vendors.qty_check ) AS qty_check,
  sum( vendors.qty_ng ) AS qty_ng,
  vendors.vendor,
  vendors.vendor_shortname 
  FROM
  (
  SELECT
  scrap_logs.material_number,
  scrap_logs.quantity AS qty_check,
  scrap_logs.quantity AS qty_ng,
  vendor,
  vendor_shortname 
  FROM
  scrap_logs
  JOIN qa_materials ON qa_materials.material_number = scrap_logs.material_number 
  WHERE
  (
  scrap_logs.material_number IN ( SELECT material_number FROM qa_materials ) 
  AND scrap_logs.`category` LIKE '%Material awal%' 
  AND scrap_logs.`material_number` NOT LIKE '%NO SAP%' 
  AND scrap_logs.`receive_location` LIKE '%OTHR%' 
  AND scrap_logs.`remark` LIKE '%received%' 
  AND scrap_logs.deleted_at IS NULL 
  AND DATE_FORMAT( scrap_logs.created_at, '%Y-%m' ) = '".$month."' 
  ".$vendorin2."
  )
  OR
  (
  scrap_logs.material_number IN ( SELECT material_number FROM qa_materials ) 
  AND scrap_logs.`category` LIKE '%Material awal%' 
  AND scrap_logs.`material_number` NOT LIKE '%NO SAP%' 
  AND scrap_logs.`receive_location` LIKE '%MMJR%' 
  AND scrap_logs.`remark` LIKE '%received%' 
  AND scrap_logs.deleted_at IS NULL 
  AND DATE_FORMAT( scrap_logs.created_at, '%Y-%m' ) = '".$month."' 
  ".$vendorin2."
  ) ) vendors
  GROUP BY
  vendors.material_number,
  vendors.vendor,
  vendors.vendor_shortname
  ) a 
  LEFT JOIN (
  SELECT
  qa_materials.vendor,
  ROUND( sum( `usage` ), 2 ) AS qty 
  FROM
  `material_requirement_plans`
  JOIN qa_materials ON qa_materials.material_number = material_requirement_plans.material_number 
  WHERE
  DATE_FORMAT( due_date, '%Y-%m' ) = '".$month."' 
  ".$vendorin2."
  GROUP BY
  qa_materials.vendor 
  ) AS plan ON plan.vendor = a.vendor 
  GROUP BY
  a.vendor,
  a.vendor_shortname,
  plan.qty
  ORDER BY
  ratio DESC,
  total_ng DESC)";

  if ($request->get('vendor_origin') == '') {
    $worst_vendor = DB::SELECT("SELECT
      c.vendor,
      c.vendor_shortname,
      sum(c.qty_rec) as qty_rec,
      sum(c.qty_check) as qty_check,
      sum(c.`repair`) as `repair`,
      sum(c.`return`) as `return`,
      sum(c.`total_ng`) as `total_ng`,
      ROUND((
      COALESCE ( SUM( c.total_ng ), 0 )/ sum( qty_check ))* 100,2) AS ratio 
      FROM
      (".$union_incoming." UNION ALL ".$union_vendor.") c
      GROUP BY c.vendor, c.vendor_shortname
      ORDER BY
      ratio DESC,
      total_ng DESC
      LIMIT 10");
  }

  if ($request->get('vendor_origin') == 'INCOMING CHECK') {
    $worst_vendor = DB::SELECT("SELECT
      c.vendor,
      c.vendor_shortname,
      sum(c.qty_rec) as qty_rec,
      sum(c.qty_check) as qty_check,
      sum(c.`repair`) as `repair`,
      sum(c.`return`) as `return`,
      sum(c.`total_ng`) as `total_ng`,
      ROUND((
      COALESCE ( SUM( c.total_ng ), 0 )/ sum( qty_check ))* 100,2) AS ratio 
      FROM
      (".$union_incoming.") c
      GROUP BY c.vendor, c.vendor_shortname
      ORDER BY
      ratio DESC,
      total_ng DESC
      LIMIT 10");
  }

  if ($request->get('vendor_origin') == 'PRODUCTION FINDING') {
    $worst_vendor = DB::SELECT("SELECT
      c.vendor,
      c.vendor_shortname,
      sum(c.qty_rec) as qty_rec,
      sum(c.qty_check) as qty_check,
      sum(c.`repair`) as `repair`,
      sum(c.`return`) as `return`,
      sum(c.`total_ng`) as `total_ng`,
      ROUND((
      COALESCE ( SUM( c.total_ng ), 0 )/ sum( qty_check ))* 100,2) AS ratio 
      FROM
      (".$union_vendor.") c
      GROUP BY c.vendor, c.vendor_shortname
      ORDER BY
      ratio DESC,
      total_ng DESC
      LIMIT 10");
  }

  $response = array(
    'status' => true,
    'worst_vendor' => $worst_vendor,
    'monthTitle' => $monthTitle,
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

public function fetchDisplayQaMeetingNgRate(Request $request)
{
  try {
    $month_from = $request->get('month_from');
    $month_to = $request->get('month_to');
    if ($month_from == "") {
      if ($month_to == "") {
        $first = date('Y-m',strtotime('- 12 month'));
        $last = date('Y-m',strtotime('-1 month'));
        $firstTitle = date('M Y',strtotime($first));
        $lastTitle = date("M Y", strtotime($last));
      }else{
        $first = date('Y-m',strtotime('- 12 month'));
        $last = $month_to;
        $firstTitle = date('M Y',strtotime($first));
        $lastTitle = date("M Y", strtotime($last));
      }
    }else{
     if ($month_to == "") {
      $first = $month_from;
      $last = date('Y-m',strtotime('-1 month'));
      $firstTitle = date('M Y',strtotime($first));
      $lastTitle = date("M Y", strtotime($last));
    }else{
      $first = $month_from;
      $last = $month_to;
      $firstTitle = date('M Y',strtotime($first));
      $lastTitle = date("M Y", strtotime($last));
    }
  }
  $vendor = '';
  if($request->get('vendor') != null){
    $vendors =  explode(",", $request->get('vendor'));
    for ($i=0; $i < count($vendors); $i++) {
      $vendor = $vendor."'".$vendors[$i]."'";
      if($i != (count($vendors)-1)){
        $vendor = $vendor.',';
      }
    }
    $vendorin = "AND material.vendor_shortname IN ( ".$vendor." ) ";
    $vendorin2 = "AND qa_materials.vendor_shortname IN ( ".$vendor." ) ";
  }
  else{
    $vendorin = "";
    $vendorin2 = "";
  }
  $union_incoming = "(
  SELECT
  DATE_FORMAT( created_at, '%Y-%m' ) AS `month`,
  DATE_FORMAT( created_at, '%b %Y' ) AS `month_name`,
  sum( qty_rec ) AS qty_rec,
  sum( qty_check ) AS qty_check,
  COALESCE ( SUM( b.`repair` ), 0 ) AS `repair`,
  COALESCE ( SUM( b.`return` ), 0 ) AS `return`,
  COALESCE ( SUM( b.total ), 0 ) AS total_ng 
  FROM
  qa_incoming_logs
  JOIN ( SELECT DISTINCT ( vendor ), vendor_shortname FROM qa_materials ) material ON material.vendor = qa_incoming_logs.vendor
  LEFT JOIN (
  SELECT
  a.incoming_check_code,
  sum( a.`repair` ) AS `repair`,
  sum( a.`return` ) AS `return`,
  sum( a.`return` ) + sum( a.`repair` ) AS total 
  FROM
  (
  SELECT
  incoming_check_code,
  sum( qty_ng ) `repair`,
  0 AS `return` 
  FROM
  qa_incoming_ng_logs 
  WHERE
  status_ng = 'Repair' 
  GROUP BY
  incoming_check_code,
  `return` UNION ALL
  SELECT
  incoming_check_code,
  0 `repair`,
  sum( qty_ng ) AS `return` 
  FROM
  qa_incoming_ng_logs 
  WHERE
  status_ng = 'Return' 
  GROUP BY
  incoming_check_code,
  `repair` 
  ) a 
  GROUP BY
  a.incoming_check_code 
  ) AS b ON b.incoming_check_code = qa_incoming_logs.incoming_check_code 
  WHERE
  DATE_FORMAT( created_at, '%Y-%m' ) >= '".$first."' 
  AND DATE_FORMAT( created_at, '%Y-%m' ) <= '".$last."' 
  ".$vendorin."
  GROUP BY
  `month`,
  `month_name` 
)";

$union_vendor_log = "(
select a.`month`,a.month_name,a.qty_rec,a.qty_check+plan.qty as qty_check,a.`repair`,a.`return`,a.total_ng from ( SELECT
DATE_FORMAT( scrap_logs.created_at, '%Y-%m' ) AS `month`,
DATE_FORMAT( scrap_logs.created_at, '%b %Y' ) AS `month_name`,
sum( quantity ) AS qty_rec,
sum( quantity ) AS qty_check,
0 AS `repair`,
sum( quantity ) AS `return`,
sum( quantity ) AS `total_ng` 
FROM
scrap_logs 
JOIN qa_materials ON qa_materials.material_number = scrap_logs.material_number 
WHERE
(
scrap_logs.material_number IN ( SELECT material_number FROM qa_materials ) 
AND scrap_logs.`category` LIKE '%Material awal%' 
AND scrap_logs.`material_number` NOT LIKE '%NO SAP%' 
AND scrap_logs.`receive_location` LIKE '%OTHR%' 
AND scrap_logs.`remark` LIKE '%received%' 
AND scrap_logs.deleted_at IS NULL 
AND DATE_FORMAT( scrap_logs.created_at, '%Y-%m' ) >= '".$first."' 
AND DATE_FORMAT( scrap_logs.created_at, '%Y-%m' ) <= '".$last."' 
".$vendorin2."
)
OR
(
scrap_logs.material_number IN ( SELECT material_number FROM qa_materials ) 
AND scrap_logs.`category` LIKE '%Material awal%' 
AND scrap_logs.`material_number` NOT LIKE '%NO SAP%' 
AND scrap_logs.`receive_location` LIKE '%MMJR%' 
AND scrap_logs.`remark` LIKE '%received%' 
AND scrap_logs.deleted_at IS NULL 
AND DATE_FORMAT( scrap_logs.created_at, '%Y-%m' ) >= '".$first."' 
AND DATE_FORMAT( scrap_logs.created_at, '%Y-%m' ) <= '".$last."' 
".$vendorin2."
) 
GROUP BY
`month`,
`month_name` ) a
left join (SELECT
DATE_FORMAT( due_date, '%Y-%m' ) AS `month`,
ROUND( sum( `usage` ), 2 ) AS qty 
FROM
`material_requirement_plans` 
GROUP BY
`month`) as plan on plan.`month` = a.`month`
)";

if ($request->get('vendor_origin') == '') {
  $ng_rate = DB::SELECT("SELECT
    c.`month`,
    c.month_name,
    sum( c.qty_rec ) AS qty_rec,
    sum( c.qty_check ) AS qty_check,
    sum( c.`repair` ) AS `repair`,
    sum( c.`return` ) AS `return`,
    sum( c.`total_ng` ) AS `total_ng`,
    ROUND(( COALESCE ( SUM( c.total_ng ), 0 )/ sum( qty_check ))* 100, 2 ) AS ratio 
    FROM
    (".$union_incoming." UNION ALL ".$union_vendor_log.") c 
    GROUP BY
    c.`month`,
    c.month_name 
    ORDER BY
    c.`month`");
}

if ($request->get('vendor_origin') == 'INCOMING CHECK') {
  $ng_rate = DB::SELECT("SELECT
    c.`month`,
    c.month_name,
    sum( c.qty_rec ) AS qty_rec,
    sum( c.qty_check ) AS qty_check,
    sum( c.`repair` ) AS `repair`,
    sum( c.`return` ) AS `return`,
    sum( c.`total_ng` ) AS `total_ng`,
    ROUND(( COALESCE ( SUM( c.total_ng ), 0 )/ sum( qty_check ))* 100, 2 ) AS ratio 
    FROM
    (".$union_incoming.") c 
    GROUP BY
    c.`month`,
    c.month_name 
    ORDER BY
    c.`month`");
}

if ($request->get('vendor_origin') == 'PRODUCTION FINDING') {
  $ng_rate = DB::SELECT("SELECT
    c.`month`,
    c.month_name,
    sum( c.qty_rec ) AS qty_rec,
    sum( c.qty_check ) AS qty_check,
    sum( c.`repair` ) AS `repair`,
    sum( c.`return` ) AS `return`,
    sum( c.`total_ng` ) AS `total_ng`,
    ROUND(( COALESCE ( SUM( c.total_ng ), 0 )/ sum( qty_check ))* 100, 2 ) AS ratio 
    FROM
    (".$union_vendor_log.") c 
    GROUP BY
    c.`month`,
    c.month_name 
    ORDER BY
    c.`month`");
}
if ($request->get('vendor') == '') {
  $vendorTitle = 'All Vendor';
}else{
  $vendorTitle = $request->get('vendor');
}
$response = array(
  'status' => true,
  'ng_rate' => $ng_rate,
  'vendorTitle' => $vendorTitle,
  'firstTitle' => $firstTitle,
  'lastTitle' => $lastTitle,

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

public function fetchDisplayQaMeetingWorstMaterial(Request $request)
{
  try {
    if ($request->get('month') == '') {
      $month = date('Y-m',strtotime('- 30 days'));
      $monthTitle = date('M Y',strtotime($month));
    }else{
      $month = $request->get('month');
      $monthTitle = date('M Y',strtotime($month));
    }

    $vendor = '';
    if($request->get('vendor') != null){
      $vendors =  explode(",", $request->get('vendor'));
      for ($i=0; $i < count($vendors); $i++) {
        $vendor = $vendor."'".$vendors[$i]."'";
        if($i != (count($vendors)-1)){
          $vendor = $vendor.',';
        }
      }
      $vendorin = " and material.`vendor_shortname` in (".$vendor.") ";
      $vendorin2 = " and qa_materials.`vendor_shortname` in (".$vendor.") ";
    }
    else{
      $vendorin = "";
      $vendorin2 = "";
    }

    $material = '';
    if($request->get('material') != null){
      $materials =  explode(",", $request->get('material'));
      for ($i=0; $i < count($materials); $i++) {
        $material = $material."'".$materials[$i]."'";
        if($i != (count($materials)-1)){
          $material = $material.',';
        }
      }
      $materialin = " and qa_incoming_logs.`material_number` in (".$material.") ";
      $materialin2 = " and qa_materials.`material_number` in (".$material.") ";
    }
    else{
      $materialin = "";
      $materialin2 = "";
    }

      // $ymes = DB::connection('ymes')->SELECT("SELECT
      //   item_code,
      //   SUM ( inout_qty ) AS qty  
      // FROM
      //   telas.vd_mes0020 
      // WHERE
      //   move_type LIKE'%SD01%' 
      //   AND issue_loc_code LIKE'%MSTK%' 
      //   AND to_char( inout_date, 'YYYY-MM' ) = '".$month."'
      //   GROUP BY
      //   item_code;");

      // $plan_usage = DB::SELECT("SELECT
      //   material_number,
      //   ROUND( sum( `usage` ), 2 ) AS qty 
      // FROM
      //   `material_requirement_plans` 
      // WHERE
      //   DATE_FORMAT( due_date, '%Y-%m' ) = '".$month."' 
      // GROUP BY
      //   material_number");

    $union_vendor_log = "(SELECT
    vendors.material_number,
    vendors.material_description,
    vendors.qty_rec,
    vendors.qty_check + COALESCE ( plan.qty, 0 ) AS qty_check,
    vendors.`repair`,
    vendors.`return`,
    vendors.total_ng,
    vendors.ng 
    FROM
    ((SELECT
    f.material_number,
    f.material_description,
    sum( f.qty_rec ) AS qty_rec,
    sum( f.qty_check ) AS qty_check,
    sum( f.`repair` ) AS `repair`,
    sum( f.`return` ) AS `return`,
    sum( f.`total_ng` ) AS `total_ng`,
    GROUP_CONCAT( g.ng_detail ) AS ng 
    FROM
    ((
    SELECT
    scrap_logs.material_number,
    scrap_logs.material_description,
    sum( quantity ) AS qty_rec,
    sum( quantity ) AS qty_check,
    0 AS `repair`,
    sum( quantity ) AS `return`,
    sum( quantity ) AS total_ng 
    FROM
    scrap_logs
    JOIN qa_materials ON qa_materials.material_number = scrap_logs.material_number 
    WHERE
    (
    scrap_logs.material_number IN ( SELECT material_number FROM qa_materials ) 
    AND scrap_logs.`category` LIKE '%Material awal%' 
    AND scrap_logs.`material_number` NOT LIKE '%NO SAP%' 
    AND scrap_logs.`receive_location` LIKE '%OTHR%' 
    AND scrap_logs.`remark` LIKE '%received%' 
    AND scrap_logs.deleted_at IS NULL 
    AND DATE_FORMAT( scrap_logs.created_at, '%Y-%m' ) = '".$month."' 
    ".$vendorin2."
    ".$materialin2."
    ) 
    OR
    (
    scrap_logs.material_number IN ( SELECT material_number FROM qa_materials ) 
    AND scrap_logs.`category` LIKE '%Material awal%' 
    AND scrap_logs.`material_number` NOT LIKE '%NO SAP%' 
    AND scrap_logs.`receive_location` LIKE '%MMJR%' 
    AND scrap_logs.`remark` LIKE '%received%' 
    AND scrap_logs.deleted_at IS NULL 
    AND DATE_FORMAT( scrap_logs.created_at, '%Y-%m' ) = '".$month."' 
    ".$vendorin2."
    ".$materialin2."
    ) 
    GROUP BY
    material_number,
    material_description 
    ) 



    ) AS f
    JOIN (select h.material_number,h.`month`,GROUP_CONCAT(ng_detail) as ng_detail from ((
    SELECT
    d.material_number,
    CONCAT( d.ng_name, ' (', sum( d.qty_ng ), ')' ) AS ng_detail,
    d.`month` 
    FROM
    (
    SELECT
    material_number,
    `month`,
    SPLIT_STRING ( SUBSTRING_INDEX( SUBSTRING_INDEX( summary, '/', n.digit + 1 ), '/', - 1 ), '_', 1 ) AS ng_name,
    SPLIT_STRING ( SUBSTRING_INDEX( SUBSTRING_INDEX( summary, '/', n.digit + 1 ), '/', - 1 ), '_', 2 ) AS qty_ng 
    FROM
    ((
    SELECT
    scrap_logs.material_number,
    DATE_FORMAT( scrap_logs.created_at, '%Y-%m' ) AS `month`,
    GROUP_CONCAT( summary SEPARATOR '/' ) AS summary 
    FROM
    scrap_logs
    JOIN qa_materials ON qa_materials.material_number = scrap_logs.material_number 
    WHERE
    (
    scrap_logs.material_number IN ( SELECT material_number FROM qa_materials ) 
    AND scrap_logs.`category` LIKE '%Material awal%' 
    AND scrap_logs.`material_number` NOT LIKE '%NO SAP%' 
    AND scrap_logs.`receive_location` LIKE '%OTHR%' 
    AND scrap_logs.`remark` LIKE '%received%' 
    AND scrap_logs.deleted_at IS NULL 
    AND DATE_FORMAT( scrap_logs.created_at, '%Y-%m' ) = '".$month."' 
    ".$vendorin2."
    ".$materialin2."
    AND DATE( scrap_logs.created_at ) >= '2023-01-12' 
    ) 
    OR
    (
    scrap_logs.material_number IN ( SELECT material_number FROM qa_materials ) 
    AND scrap_logs.`category` LIKE '%Material awal%' 
    AND scrap_logs.`material_number` NOT LIKE '%NO SAP%' 
    AND scrap_logs.`receive_location` LIKE '%MMJR%' 
    AND scrap_logs.`remark` LIKE '%received%' 
    AND scrap_logs.deleted_at IS NULL 
    AND DATE_FORMAT( scrap_logs.created_at, '%Y-%m' ) = '".$month."' 
    ".$vendorin2."
    ".$materialin2."
    AND DATE( scrap_logs.created_at ) >= '2023-01-12' 
    ) 
    GROUP BY
    material_number,
    `month` 
    ) 
    ) a3
    INNER JOIN (
    SELECT
    0 digit UNION ALL
    SELECT
    1 UNION ALL
    SELECT
    2 UNION ALL
    SELECT
    3 UNION ALL
    SELECT
    4 UNION ALL
    SELECT
    5 UNION ALL
    SELECT
    6 UNION ALL
    SELECT
    7 UNION ALL
    SELECT
    8 UNION ALL
    SELECT
    9 UNION ALL
    SELECT
    10 UNION ALL
    SELECT
    11 UNION ALL
    SELECT
    12 UNION ALL
    SELECT
    13 UNION ALL
    SELECT
    14 UNION ALL
    SELECT
    15 UNION ALL
    SELECT
    16 UNION ALL
    SELECT
    17 UNION ALL
    SELECT
    18 UNION ALL
    SELECT
    19 UNION ALL
    SELECT
    20 UNION ALL
    SELECT
    21 UNION ALL
    SELECT
    22 UNION ALL
    SELECT
    23 UNION ALL
    SELECT
    24 UNION ALL
    SELECT
    25 
    ) n ON LENGTH(
    REPLACE ( summary, '/', '' )) <= LENGTH( summary )- n.digit 
    ORDER BY
    n.digit 
    ) d 
    WHERE
    d.ng_name != '' 
    AND d.qty_ng != '' 
    GROUP BY
    d.material_number,
    d.`month`,
    d.ng_name 
    ) UNION ALL
    (
    (
    SELECT
    scrap_logs.material_number,
    GROUP_CONCAT( summary SEPARATOR '/' ) AS ng_detail,
    DATE_FORMAT( scrap_logs.created_at, '%Y-%m' ) AS `month` 
    FROM
    scrap_logs
    JOIN qa_materials ON qa_materials.material_number = scrap_logs.material_number 
    WHERE
    (
    scrap_logs.material_number IN ( SELECT material_number FROM qa_materials ) 
    AND scrap_logs.`category` LIKE '%Material awal%' 
    AND scrap_logs.`material_number` NOT LIKE '%NO SAP%' 
    AND scrap_logs.`receive_location` LIKE '%OTHR%' 
    AND scrap_logs.`remark` LIKE '%received%' 
    AND scrap_logs.deleted_at IS NULL 
    AND DATE_FORMAT( scrap_logs.created_at, '%Y-%m' ) = '".$month."' 
    ".$vendorin2."
    ".$materialin2."
    AND DATE( scrap_logs.created_at ) < '2023-01-12' 
    ) 
    OR
    (
    scrap_logs.material_number IN ( SELECT material_number FROM qa_materials ) 
    AND scrap_logs.`category` LIKE '%Material awal%' 
    AND scrap_logs.`material_number` NOT LIKE '%NO SAP%' 
    AND scrap_logs.`receive_location` LIKE '%MMJR%' 
    AND scrap_logs.`remark` LIKE '%received%' 
    AND scrap_logs.deleted_at IS NULL 
    AND DATE_FORMAT( scrap_logs.created_at, '%Y-%m' ) = '".$month."' 
    ".$vendorin2."
    ".$materialin2."
    AND DATE( scrap_logs.created_at ) < '2023-01-12' 
    ) 
    GROUP BY
    material_number,
    `month` 
    ) 
    )) as h
    GROUP BY h.material_number,h.`month`) g ON g.material_number = f.material_number 
    GROUP BY
    f.material_number,
    f.material_description) ) vendors
    LEFT JOIN (
    SELECT
    material_number,
    ROUND( sum( `usage` ), 2 ) AS qty 
    FROM
    `material_requirement_plans` 
    WHERE
    DATE_FORMAT( due_date, '%Y-%m' ) = '".$month."' 
    GROUP BY
    material_number 
  ) AS plan ON plan.material_number = vendors.material_number)";

  $union_incoming = "( SELECT
  yy.material_number,
  yy.material_description,
  sum( yy.qty_rec ) AS qty_rec,
  sum( yy.qty_check ) AS qty_check,
  sum( yy.`repair` ) AS `repair`,
  sum( yy.`return` ) AS `return`,
  sum( yy.total_ng ) AS total_ng,
  GROUP_CONCAT( DISTINCT(yy.ng) ) AS ng 
  FROM
  ((
  SELECT
  qa_incoming_logs.material_number,
  material_description,
  qa_incoming_logs.qty_rec AS qty_rec,
  qty_check AS qty_check,
  IF
  (
  qa_incoming_logs.material_number = 'VGN366Z' 
  OR qa_incoming_logs.material_number = 'VGN365Z' 
  OR qa_incoming_logs.material_number = 'W53590Z' 
  OR qa_incoming_logs.material_number = 'W53580Z' 
  OR qa_incoming_logs.material_number = 'WN7115Z' 
  OR qa_incoming_logs.material_number = 'ZE0830Z',
  qa_incoming_logs.total_ng_pcs,
  COALESCE ( b.`repair`, 0 )) AS `repair`,
  COALESCE ( b.`repair`, 0 ),
  COALESCE ( b.`return`, 0 ) AS `return`,
  IF
  (
  qa_incoming_logs.material_number = 'VGN366Z' 
  OR qa_incoming_logs.material_number = 'VGN365Z' 
  OR qa_incoming_logs.material_number = 'W53590Z' 
  OR qa_incoming_logs.material_number = 'W53580Z' 
  OR qa_incoming_logs.material_number = 'WN7115Z' 
  OR qa_incoming_logs.material_number = 'ZE0830Z',
  qa_incoming_logs.total_ng_pcs,
  COALESCE ( b.total, 0 )) AS `total_ng`,
  GROUP_CONCAT(
  DISTINCT ( c.ng_detail )) AS ng 
  FROM
  qa_incoming_logs
  JOIN ( SELECT DISTINCT ( vendor ), vendor_shortname FROM qa_materials ) material ON material.vendor = qa_incoming_logs.vendor
  LEFT JOIN (
  SELECT
  a.incoming_check_code,
  sum( a.`repair` ) AS `repair`,
  sum( a.`return` ) AS `return`,
  sum( a.`return` ) + sum( a.`repair` ) AS total 
  FROM
  (
  SELECT
  incoming_check_code,
  sum( qty_ng ) `repair`,
  0 AS `return` 
  FROM
  qa_incoming_ng_logs 
  WHERE
  status_ng = 'Repair' 
  GROUP BY
  incoming_check_code,
  `return` UNION ALL
  SELECT
  incoming_check_code,
  0 `repair`,
  sum( qty_ng ) AS `return` 
  FROM
  qa_incoming_ng_logs 
  WHERE
  status_ng = 'Return' 
  GROUP BY
  incoming_check_code,
  `repair` 
  ) a 
  GROUP BY
  a.incoming_check_code 
  ) AS b ON b.incoming_check_code = qa_incoming_logs.incoming_check_code
  JOIN (
  SELECT
  qa_incoming_ng_logs.material_number,
  CONCAT( ng_name, ' (', sum( qty_ng ), ')' ) AS ng_detail,
  DATE_FORMAT( created_at, '%Y-%m' ) AS `month` 
  FROM
  qa_incoming_ng_logs 
  GROUP BY
  material_number,
  `month`,
  ng_name 
  ) AS c ON c.material_number = qa_incoming_logs.material_number 
  AND c.`month` = DATE_FORMAT( qa_incoming_logs.created_at, '%Y-%m' ) 
  WHERE
  DATE_FORMAT( created_at, '%Y-%m' ) = '".$month."' 
  ".$vendorin."
  ".$materialin."
  GROUP BY
  qa_incoming_logs.material_number,
  material_description,
  qa_incoming_logs.incoming_check_code,
  qa_incoming_logs.qty_rec,
  qty_check,
  qa_incoming_logs.total_ng_pcs,
  b.repair,
  b.return,
  b.total
  )) yy 
  GROUP BY
  yy.material_number,
  yy.material_description)";

  if ($request->get('vendor_origin') == '') {
    $worst = DB::SELECT("SELECT
      h.`material_number`,
      h.material_description,
      sum( h.qty_rec ) AS qty_rec,
      sum( h.qty_check ) AS qty_check,
      sum( h.`repair` ) AS `repair`,
      sum( h.`return` ) AS `return`,
      sum( h.`total_ng` ) AS `total_ng`,
      ROUND(( COALESCE ( SUM( h.total_ng ), 0 )/ sum( qty_check ))* 100, 2 ) AS ratio,
      GROUP_CONCAT( h.ng ) AS ng 
      FROM
      (".$union_incoming." UNION ALL ".$union_vendor_log.") h 
      GROUP BY
      h.`material_number`,
      h.material_description 
      ORDER BY
      ratio DESC,
      total_ng DESC ");
  }

  if ($request->get('vendor_origin') == 'INCOMING CHECK') {
    $worst = DB::SELECT("SELECT
      h.`material_number`,
      h.material_description,
      sum( h.qty_rec ) AS qty_rec,
      sum( h.qty_check ) AS qty_check,
      sum( h.`repair` ) AS `repair`,
      sum( h.`return` ) AS `return`,
      sum( h.`total_ng` ) AS `total_ng`,
      ROUND(( COALESCE ( SUM( h.total_ng ), 0 )/ sum( qty_check ))* 100, 2 ) AS ratio,
      GROUP_CONCAT( h.ng ) AS ng 
      FROM
      (".$union_incoming.") h 
      GROUP BY
      h.`material_number`,
      h.material_description 
      ORDER BY
      ratio DESC,
      total_ng DESC ");
  }

  if ($request->get('vendor_origin') == 'PRODUCTION FINDING') {
    $worst = DB::SELECT("SELECT
      h.`material_number`,
      h.material_description,
      sum( h.qty_rec ) AS qty_rec,
      sum( h.qty_check ) AS qty_check,
      sum( h.`repair` ) AS `repair`,
      sum( h.`return` ) AS `return`,
      sum( h.`total_ng` ) AS `total_ng`,
      ROUND(( COALESCE ( SUM( h.total_ng ), 0 )/ sum( qty_check ))* 100, 2 ) AS ratio,
      GROUP_CONCAT( h.ng ) AS ng 
      FROM
      (".$union_vendor_log.") h 
      GROUP BY
      h.`material_number`,
      h.material_description 
      ORDER BY
      ratio DESC,
      total_ng DESC ");
  }

  $worst_material = [];

  for ($i=0; $i < count($worst); $i++) { 
    $qty = 0;
              // for ($j=0; $j < count($plan_usage); $j++) { 
              //   if ($plan_usage[$j]->material_number == $worst[$i]->material_number) {
              //     $qty = $plan_usage[$j]->qty;
              //   }
              // }
    array_push($worst_material, [
      'material_number' => $worst[$i]->material_number,
      'material_description' => $worst[$i]->material_description,
      'qty_rec' => $worst[$i]->qty_rec,
      'qty_check' => $worst[$i]->qty_check+$qty,
      'repair' => $worst[$i]->repair,
      'return' => $worst[$i]->return,
      'total_ng' => $worst[$i]->total_ng,
      'ratio' => (($worst[$i]->total_ng/($worst[$i]->qty_check+$qty))*100),
      'ng' => $worst[$i]->ng,
    ]);
  }

  usort($worst_material, function ($a, $b) {
    if ($a['total_ng'] == $b['total_ng']) {
      if ($a['ratio'] < $b['ratio']) {
        return 1;
      }
    }
    return $a['total_ng'] < $b['total_ng'] ? 1 : -1;
  });

  $response = array(
    'status' => true,
    'worst_material' => $worst_material,
    'monthTitle' => $monthTitle,
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

function order_by_worst_material($a, $b) {
  if ($a['ratio'] == $b['ratio']) {
                // score is the same, sort by endgame
    if ($a['total_ng'] > $b['total_ng']) {
      return 1;
    }
  }

            // sort the higher score first:
  return $a['ratio'] < $b['ratio'] ? 1 : -1;
}

//KENSA CERTIFICATE

public function indexCertificate()
{
  return view('qa.certificate.index')
  ->with('title', 'QA Kensa Certificate')
  ->with('title_jp', '品質保証検査認定')
  ->with('page', 'QA Kensa Certificate')
  ->with('jpn', '');
}

public function indexCertificateCode()
{

      // $code_number = DB::connection('ympimis_2')
      // ->table('qa_certificate_codes')
      // ->select('qa_certificate_codes.code','qa_certificate_codes.code_number','qa_certificate_codes.description')
      // ->distinct()
      // ->get();

  $role = Auth::user()->role_code;

  $code_number = DB::CONNECTION('ympimis_2')
  ->select("SELECT DISTINCT
    (
    SPLIT_STRING ( certificate_name, ',', 1 )) AS product,
    description 
    FROM
    qa_certificate_codes 
    WHERE
    `code` = 'I' 
    AND SPLIT_STRING ( certificate_name, ',', 1 ) != '' UNION ALL
    SELECT DISTINCT
    (
    SPLIT_STRING ( certificate_name, ',', 2 )) AS product,
    description 
    FROM
    qa_certificate_codes 
    WHERE
    `code` = 'I' 
    AND SPLIT_STRING ( certificate_name, ',', 2 ) != ''");

  return view('qa.certificate.index_code')
  ->with('title', 'QA Kensa Certificate Monitoring')
  ->with('title_jp', '品質保証検査認定監視')
  ->with('code_number', $code_number)
  ->with('role', $role)
  ->with('page', 'QA Kensa Certificate')
  ->with('jpn', '');
}

public function fetchCertificateCode(Request $request)
{
  try {
    $status = $request->get('status');
    $code = $request->get('code');

    $datas = DB::connection('ympimis_2')
    ->table('qa_certificate_codes')
    ->select('qa_certificate_codes.*','a.certificate_code','a.periode_from','a.periode_to','a.certificate_name as names')
    ->leftjoin(DB::RAW('(SELECT DISTINCT
      ( qa_certificates.certificate_id ),
      certificate_code,
      certificate_name,
      periode_from,
      periode_to,
      `status` 
      FROM
      qa_certificates) as a'),function($join){
      $join->on('a.certificate_id', '=', 'qa_certificate_codes.certificate_id');
      $join->on('a.status','qa_certificate_codes.status');
    })
    ->leftjoin(DB::RAW('(SELECT DISTINCT
      ( qa_certificate_approvals.certificate_id ),
      approver_id,
      created_at,
      updated_at
      FROM
      qa_certificate_approvals
      where priority = 1) as b'),function($join){
      $join->on('b.certificate_id', '=', 'qa_certificate_codes.certificate_id');
    })
    ->orderBy('qa_certificate_codes.status','desc')
    ->orderBy('b.updated_at','desc');

    if ($status != '') {
      $datas = $datas->where('qa_certificate_codes.status',$status);
      $datas = $datas->where('qa_certificate_codes.code','I');
    }else{
      $datas = $datas->where('qa_certificate_codes.status','!=','0');
      $datas = $datas->where('qa_certificate_codes.code','I');
    }

    $where_grafik = "";
    if ($code != '') {
      $datas = $datas->where('a.certificate_name',$code);
      $where_grafik = "WHERE a.product = '".$code."'";
    }

    $datas = $datas->get();

    $approvals = [];

    foreach ($datas as $key) {
      $approval = DB::connection('ympimis_2')
      ->select("SELECT
        qa_certificate_approvals.id,
        qa_certificate_approvals.certificate_id,
        qa_certificate_approvals.approver_id,
        qa_certificate_approvals.approver_name,
        qa_certificate_approvals.approver_email,
        qa_certificate_approvals.`approver_status`,
        qa_certificate_approvals.approved_at,
        qa_certificate_approvals.remark,
        CONCAT(
        DATE_FORMAT( qa_certificate_approvals.approved_at, '%d-%b-%Y' ),
        '<br>',
        DATE_FORMAT( qa_certificate_approvals.approved_at, '%H:%i:%s' )) AS approved_ats,
        'sudah' AS keutamaan,
        CONCAT(SPLIT_STRING(approver_name,' ',1),' ',SPLIT_STRING(approver_name,' ',2)) as approver_names,
        certificate_approval_id
        FROM
        `qa_certificate_approvals` 
        WHERE
        qa_certificate_approvals.certificate_id = '".$key->certificate_id."' 
        AND qa_certificate_approvals.`approver_status` IS NOT NULL UNION ALL
        (
        SELECT
        qa_certificate_approvals.id,
        qa_certificate_approvals.certificate_id,
        qa_certificate_approvals.approver_id,
        qa_certificate_approvals.approver_name,
        qa_certificate_approvals.approver_email,
        qa_certificate_approvals.`approver_status`,
        qa_certificate_approvals.approved_at,
        qa_certificate_approvals.remark,
        CONCAT(
        DATE_FORMAT( qa_certificate_approvals.approved_at, '%d-%b-%Y' ),
        '<br>',
        DATE_FORMAT( qa_certificate_approvals.approved_at, '%H:%i:%s' )) AS approved_ats,
        'utama' AS keutamaan,
        CONCAT(SPLIT_STRING(approver_name,' ',1),' ',SPLIT_STRING(approver_name,' ',2)) as approver_names,
        certificate_approval_id
        FROM
        `qa_certificate_approvals` 
        WHERE
        qa_certificate_approvals.certificate_id = '".$key->certificate_id."' 
        AND qa_certificate_approvals.`approver_status` IS NULL 
        LIMIT 1 
        ) UNION ALL
        (
        SELECT
        qa_certificate_approvals.id,
        qa_certificate_approvals.certificate_id,
        qa_certificate_approvals.approver_id,
        qa_certificate_approvals.approver_name,
        qa_certificate_approvals.approver_email,
        qa_certificate_approvals.`approver_status`,
        qa_certificate_approvals.approved_at,
        qa_certificate_approvals.remark,
        CONCAT(
        DATE_FORMAT( qa_certificate_approvals.approved_at, '%d-%b-%Y' ),
        '<br>',
        DATE_FORMAT( qa_certificate_approvals.approved_at, '%H:%i:%s' )) AS approved_ats,
        'belum' AS keutamaan,
        CONCAT(SPLIT_STRING(approver_name,' ',1),' ',SPLIT_STRING(approver_name,' ',2)) as approver_names,
        certificate_approval_id
        FROM
        `qa_certificate_approvals` 
        WHERE
        qa_certificate_approvals.certificate_id = '".$key->certificate_id."' 
        AND qa_certificate_approvals.`approver_status` IS NULL 
        AND qa_certificate_approvals.id != ( SELECT qa_certificate_approvals.id FROM `qa_certificate_approvals` WHERE qa_certificate_approvals.certificate_id = '".$key->certificate_id."' AND qa_certificate_approvals.`approver_status` IS NULL LIMIT 1 ))");

      array_push($approvals, $approval);
    }

    $charts = DB::connection('ympimis_2')
    ->select("SELECT
      a.product,
      description,
      ( SELECT count( DISTINCT ( certificate_id )) AS count FROM qa_certificates WHERE qa_certificates.`status` = 1 AND qa_certificates.certificate_name = a.product ) AS active,
      ( SELECT count( DISTINCT ( certificate_id )) AS count FROM qa_certificates WHERE qa_certificates.`status` = 2 AND qa_certificates.certificate_name = a.product ) AS renewal,
      ( SELECT count( DISTINCT ( certificate_id )) AS count FROM qa_certificates WHERE qa_certificates.`status` = 3 AND qa_certificates.certificate_name = a.product ) AS expired 
      FROM
      (
      SELECT DISTINCT
      (
      SPLIT_STRING ( certificate_name, ',', 1 )) AS product,
      description 
      FROM
      qa_certificate_codes 
      WHERE
      `code` = 'I' 
      AND SPLIT_STRING ( certificate_name, ',', 1 ) != '' UNION ALL
      SELECT DISTINCT
      (
      SPLIT_STRING ( certificate_name, ',', 2 )) AS product,
      description 
      FROM
      qa_certificate_codes 
      WHERE
      `code` = 'I' 
      AND SPLIT_STRING ( certificate_name, ',', 2 ) != '' 
      ) a 
      ORDER BY
      active DESC,
      renewal DESC,
      expired DESC");

    $utamas = DB::connection('ympimis_2')->select("SELECT
      certificate_approval_id,
      GROUP_CONCAT( certificate_id ) AS certificate_id 
      FROM
      qa_certificate_approvals 
      WHERE
      priority = 1 
      GROUP BY
      certificate_approval_id");


    $response = array(
      'status' => true,
      'datas' => $datas,
      'approval' => $approvals,
      'charts' => $charts,
      'utamas' => $utamas,
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

public function printCertificate($certificate_id)
{
  $datas = DB::connection('ympimis_2')
  ->table('qa_certificate_codes')
  ->join('qa_certificates','qa_certificates.certificate_id','qa_certificate_codes.certificate_id')
  ->where('qa_certificate_codes.certificate_id',$certificate_id)
  ->get();

      //------------------------------------------------//

  $data_subject = DB::connection('ympimis_2')
  ->table('qa_certificates')
  ->select('qa_certificates.subject')
  ->distinct()
  ->where('qa_certificates.certificate_id',$certificate_id)
  ->get();

  $data_approval = DB::connection('ympimis_2')
  ->table('qa_certificate_approvals')
  ->where('qa_certificate_approvals.certificate_id',$certificate_id)
  ->orderBy('id','desc')
  ->get();

      // return view('qa.certificate.print_landscape')->with('datas',$datas)->with('data_subject',$data_subject)->with('data_approval',$data_approval);

  $pdf = \App::make('dompdf.wrapper');
  $pdf->getDomPDF()->set_option("enable_php", true);
  $pdf->setPaper($datas[0]->certificate_paper, 'potrait');

  $pdf->loadView('qa.certificate.print', array(
    'datas' => $datas,
    'data_approval' => $data_approval,
  ));

  $depan = "QA Certificate - ".$certificate_id." (".$datas[0]->certificate_code.").pdf";

  $pdf->save(public_path() . "/data_file/qa/certificate/".$depan);

  $pdf2 = \App::make('dompdf.wrapper');
  $pdf2->getDomPDF()->set_option("enable_php", true);
  $pdf2->getDomPDF()->set_option("enable_css_float", true);
  $pdf2->setPaper('A4', 'landscape');

  $pdf2->loadView('qa.certificate.print_landscape', array(
    'datas' => $datas,
    'data_subject' => $data_subject,
    'data_approval' => $data_approval,
  ));

      // return view('qa.certificate.print')->with('datas',$datas)->with('data_approval',$data_approval);

  $belakang = "QA Certificate Belakang - ".$certificate_id." (".$datas[0]->certificate_code.").pdf";

  $pdf2->save(public_path() . "/data_file/qa/certificate/".$belakang);

          // return $pdf->stream("QA Certificate.pdf");
      // return $pdf2->stream("QA Certificate.pdf");

  $pdfFile1Path = public_path() . "/data_file/qa/certificate/".$depan;
  $pdfFile2Path = public_path() . "/data_file/qa/certificate/".$belakang;

  $merger = new Merger;
  $merger->addFile($pdfFile1Path);
  $merger->addFile($pdfFile2Path);
  $createdPdf = $merger->merge();

  $pathForTheMergedPdf = public_path() . "/data_file/qa/certificate_fix/".$certificate_id.".pdf";

  file_put_contents($pathForTheMergedPdf, $createdPdf);

      //-----------------------------------//

  $pathForTheMergedPdf = public_path() . "/data_file/qa/certificate_fix/".$certificate_id.".pdf";


  $fileNameFromDb = "QA Certificate - ".$certificate_id." (".$datas[0]->certificate_code.")";

  return response()->file($pathForTheMergedPdf,[
    'Content-Disposition' => 'inline; filename="'. $fileNameFromDb .'"'
  ]);
}

public function renewCertificate($certificate_id)
{
  $certificate = DB::connection('ympimis_2')
  ->table('qa_certificates')
  ->select('qa_certificates.certificate_id','qa_certificates.employee_id','qa_certificates.name','qa_certificates.periode_from','qa_certificates.periode_to','qa_certificates.certificate_code','qa_certificates.certificate_name','qa_certificates.status');

  $code_number = DB::connection('ympimis_2')
  ->table('qa_certificate_codes')
  ->select('qa_certificate_codes.code','qa_certificate_codes.code_number','qa_certificate_codes.description');

  if ($certificate_id != '000') {
    $code_number = $code_number->where('certificate_id',$certificate_id)->where('status','!=','0');
    $certificate = $certificate->where('certificate_id',$certificate_id)->where('status','!=','0');
  }else{
    $code_number = $code_number->where('status','!=','0')->distinct();
    $certificate = $certificate->where('status','!=','0');
  }
  $code_number = $code_number->get();
  $certificate = $certificate->distinct()->get();

  $periode = DB::connection('ympimis_2')
  ->table('qa_certificate_periodes')->select('code','code_number','description','certificate_name','periode')->get();

  return view('qa.certificate.index_renew')
  ->with('title', 'QA Kensa Certificate Renewal')
  ->with('title_jp', '品質保証検査認定更新')
  ->with('code_number', $code_number)
  ->with('certificate', $certificate)
  ->with('certificate_id', $certificate_id)
  ->with('periode', $periode)
  ->with('auditor_id', Auth::user()->username)
  ->with('auditor_name', Auth::user()->name)
  ->with('page', 'QA Kensa Certificate')
  ->with('jpn', '');
}

public function fetchRenewCertificate(Request $request)
{
  try {
    $code = $request->get('code');
    $code_number = $request->get('code_number');

    $data_subject = DB::connection('ympimis_2')
    ->table('qa_certificate_points')
    ->select('qa_certificate_points.subject')
    ->distinct()
    ->where('code',$code)
    ->where('code_number',$code_number)
    ->get();

    $point = DB::connection('ympimis_2')
    ->table('qa_certificate_points')
    ->where('code',$code)
    ->where('code_number',$code_number)
    ->get();

    $response = array(
      'status' => true,
      'point' => $point,
      'data_subject' => $data_subject,
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

public function inputRenewCertificate(Request $request)
{
  try {
        $subject = $request->get('subject'); //all
        $test_type = $request->get('test_type');
        $category = $request->get('category');
        $question = $request->get('question');
        $weight = $request->get('weight');
        $question_result = $request->get('question_result');
        $count_point = $request->get('count_point');
        $subjects = $request->get('subjects'); //distinct
        $subject_select = $request->get('subject_select');
        $presentase_result = $request->get('presentase_result');
        $presentase_a = $request->get('presentase_a');
        $note = $request->get('note');
        $auditor_id = $request->get('auditor_id');
        $auditor_name = $request->get('auditor_name');
        $certificate_id = $request->get('certificate_id');
        $certificate_code = $request->get('certificate_code');
        $code_desc = $request->get('code_desc');
        $employee_id = $request->get('employee_id');
        $employee_name = $request->get('employee_name');
        $periode_from = $request->get('periode_from');
        $periode_to = $request->get('periode_to');
        $certificate_name = $request->get('certificate_name');
        $standard = $request->get('standard');
        $staff_id = $request->get('staff_id');
        $staff_name = $request->get('staff_name');
        $staff_email = $request->get('staff_email');

        $empsync = EmployeeSync::where('employee_id',$employee_id)->first();

        if ($empsync->department == 'Standardization Department') {
          $approval_type = 'QA';
        }else{
          $approval_type = 'PRODUKSI';
        }

        $code_generator = CodeGenerator::where('note', '=', 'qa_certificate')->first();
        $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

        $serial_number = $code_generator->prefix.$number;

        $code_generator->index = $code_generator->index+1;

        // for ($i=0; $i < count($subjects); $i++) { 
        $index_j = 5;
        $indexj2 = 3;
        for ($j=0; $j < $count_point; $j++) { 
            // if ($subject[$j] == $subjects[$i]) {
          if (!ISSET($presentase_result[$j])) {
            $presentase_results = null;
            $lulus = null;
          }else{
            if (end($subjects) == $subject[$j]) {
              $presentase_results = $presentase_result[$j+$indexj2];
              if ((int)$presentase_result[$j+$indexj2] < (int)str_replace('%', '', str_replace('>= / = ', '', $standard[$j+$indexj2]))) {
                $lulus = 'TIDAK LULUS';
              }else{
                $lulus = 'LULUS';
              }
              $indexj2--;
            }else{
              if ($j%6 != 0) {
                $presentase_results = $presentase_result[$j+$index_j];
                if ((int)$presentase_result[$j] < (int)str_replace('%', '', str_replace('>= / = ', '', $standard[$j])) || (int)$presentase_result[$j+$index_j] < (int)str_replace('%', '', str_replace('>= / = ', '', $standard[$j+$index_j]))) {
                  $lulus = 'TIDAK LULUS';
                }else{
                  $lulus = 'LULUS';
                }
              }else{
                $index_j = 5;
                $presentase_results = $presentase_result[$j];
                if ((int)$presentase_result[$j] < (int)str_replace('%', '', str_replace('>= / = ', '', $standard[$j])) || (int)$presentase_result[$j+$index_j] < (int)str_replace('%', '', str_replace('>= / = ', '', $standard[$j+$index_j]))) {
                  $lulus = 'TIDAK LULUS';
                }else{
                  $lulus = 'LULUS';
                }
              }
            }
          }
          if (!ISSET($presentase_a[$j])) {
            $presentase_as = null;
          }else{
            $presentase_as = $presentase_a[$j];
          }
          if (!ISSET($note[$j])) {
            $notes = 'Tidak Sertifikasi';
          }else{
            $notes = $note[$j];
          }
          $log = DB::connection('ympimis_2')->table('qa_certificates')->insert([
            'certificate_id' => $serial_number,
            'employee_id' => $employee_id,
            'name' => $employee_name,
            'periode_from' => $periode_from,
            'periode_to' => $periode_to,
            'certificate_code' => $certificate_code,
            'certificate_name' => $certificate_name,
            'subject' => $subject[$j],
            'test_type' => $test_type[$j],
            'category' => $category[$j],
            'weight' => $weight[$j],
            'question' => $question[$j],
            'question_result' => $question_result[$j],
            'presentase_result' => $presentase_results,
            'presentase_a' => $presentase_as,
            'note' => $notes,
            'standard' => str_replace('%', '', str_replace('>= / = ', '', $standard[$j])),
            'result_grade' => $lulus,
            'auditor_id' => $auditor_id,
            'auditor_name' => $auditor_name,
            'approval_type' => $approval_type,
            'staff_id' => $staff_id,
            'staff_name' => $staff_name,
            'staff_email' => $staff_email,
            'status' => 1,
            'created_by' => Auth::id(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
          ]);

          $index_j--;
            // }
        }

        $certificate_lama = DB::connection('ympimis_2')
        ->table('qa_certificates')
        ->where('certificate_id',$certificate_id)
        ->get();

        if (count($certificate_lama) > 0) {
          for ($k=0; $k < count($certificate_lama); $k++) { 
            $certificate_lama_update = DB::connection('ympimis_2')
            ->table('qa_certificates')
            ->where('id',$certificate_lama[$k]->id)
            ->update([
              'status' => "0",
              'updated_at' => date('Y-m-d H:i:s')
            ]);
          }
        }

        $certificate_code_old = DB::connection('ympimis_2')
        ->table('qa_certificate_codes')
        ->where('certificate_id',$certificate_id)
        ->first();

        if (count($certificate_code_old) > 0) {
         $certificate_code_old = DB::connection('ympimis_2')
         ->table('qa_certificate_codes')
         ->where('certificate_id',$certificate_id)
         ->update([
          'certificate_id' => $serial_number,
          'employee_id' => $employee_id,
          'name' => $employee_name,
          'status' => '1',
          'updated_at' => date('Y-m-d H:i:s')
        ]); 
       }
        // }

       $code_generator->save();

       self :: masterCertificateApproval($serial_number,$approval_type,$staff_id,$staff_name,$staff_email,$auditor_id,$auditor_name,$empsync->department,$empsync->section);

       $datas = DB::connection('ympimis_2')
       ->table('qa_certificate_codes')
       ->join('qa_certificates','qa_certificates.certificate_id','qa_certificate_codes.certificate_id')
       ->where('qa_certificate_codes.certificate_id',$serial_number)
       ->get();

       $data_subject = DB::connection('ympimis_2')
       ->table('qa_certificates')
       ->select('qa_certificates.subject')
       ->distinct()
       ->where('qa_certificates.certificate_id',$serial_number)
       ->get();

       $data_approval = DB::connection('ympimis_2')
       ->table('qa_certificate_approvals')
       ->where('qa_certificate_approvals.certificate_id',$serial_number)
       ->orderBy('id','desc')
       ->get();

       $pdf = \App::make('dompdf.wrapper');
       $pdf->getDomPDF()->set_option("enable_php", true);
       $pdf->setPaper($datas[0]->certificate_paper, 'potrait');

       $pdf->loadView('qa.certificate.print', array(
        'datas' => $datas,
        'data_approval' => $data_approval,
      ));

       $depan = "QA Certificate - ".$serial_number." (".$datas[0]->certificate_code.").pdf";

       $pdf->save(public_path() . "/data_file/qa/certificate/".$depan);

       $pdf2 = \App::make('dompdf.wrapper');
       $pdf2->getDomPDF()->set_option("enable_php", true);
       $pdf2->getDomPDF()->set_option("enable_css_float", true);
       $pdf2->setPaper('A4', 'landscape');


       $pdf2->loadView('qa.certificate.print_landscape', array(
        'datas' => $datas,
        'data_subject' => $data_subject,
        'data_approval' => $data_approval,
      ));

        // return $pdf2->stream("QA Certificate.pdf");

       $belakang = "QA Certificate Belakang - ".$serial_number." (".$datas[0]->certificate_code.").pdf";

       $pdf2->save(public_path() . "/data_file/qa/certificate/".$belakang);

       $pdfFile1Path = public_path() . "/data_file/qa/certificate/".$depan;
       $pdfFile2Path = public_path() . "/data_file/qa/certificate/".$belakang;

       $merger = new Merger;
       $merger->addFile($pdfFile1Path);
       $merger->addFile($pdfFile2Path);
       $createdPdf = $merger->merge();

       $pathForTheMergedPdf = public_path() . "/data_file/qa/certificate_fix/".$serial_number.".pdf";

       file_put_contents($pathForTheMergedPdf, $createdPdf);

       $response = array(
        'status' => true,
        'message' => 'Renewal Success',
        'certificate_id' => $serial_number
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

  public function indexNewCertificate()
  {
    $code_number = DB::connection('ympimis_2')
    ->table('qa_certificate_codes')
    ->select('qa_certificate_codes.code','qa_certificate_codes.code_number','qa_certificate_codes.description')->distinct()->where('remark','1')->where('code','I')->get();

    $periode = DB::connection('ympimis_2')
    ->table('qa_certificate_periodes')->select('code','code_number','description','certificate_name','periode')->get();

    $empsync = EmployeeSync::where('end_date',null)->get();

    $fy_now = WeeklyCalendar::where('week_date',date('Y-m-d'))->first();

    $fy = WeeklyCalendar::select(DB::RAW("DATE_FORMAT( week_date, '%Y-%m' ) as months"))->distinct()->where('fiscal_year',$fy_now->fiscal_year)->orderby('week_date')->get();

    return view('qa.certificate.index_new')
    ->with('title', 'New QA Kensa Certificate')
    ->with('title_jp', '新しい品質保証検査認証')
    ->with('code_number', $code_number)
    ->with('periode', $periode)
    ->with('fy', $fy)
    ->with('employees', $empsync)
    ->with('auditor_id', Auth::user()->username)
    ->with('auditor_name', Auth::user()->name)
    ->with('page', 'QA Kensa Certificate')
    ->with('jpn', '');
  }

  public function inputNewCertificate(Request $request)
  {
    try {
        $subject = $request->get('subject'); //all
        $test_type = $request->get('test_type');
        $category = $request->get('category');
        $question = $request->get('question');
        $weight = $request->get('weight');
        $question_result = $request->get('question_result');
        $count_point = $request->get('count_point');
        $subjects = $request->get('subjects'); //distinct
        $subject_select = $request->get('subject_select');
        $presentase_result = $request->get('presentase_result');
        $presentase_a = $request->get('presentase_a');
        $note = $request->get('note');
        $auditor_id = $request->get('auditor_id');
        $auditor_name = $request->get('auditor_name');
        $code = $request->get('code');
        $code_number = $request->get('code_number');
        $code_desc = $request->get('code_desc');
        $employee_id = $request->get('employee_id');
        $employee_name = $request->get('employee_name');
        $standard = $request->get('standard');
        $staff_id = $request->get('staff_id');
        $staff_name = $request->get('staff_name');
        $staff_email = $request->get('staff_email');

        $empsync = EmployeeSync::where('employee_id',$employee_id)->first();

        $certificate_codes = DB::connection('ympimis_2')
        ->table('qa_certificate_codes')
        ->where('code',$code)
        ->where('code_number',$code_number)
        ->first();

        $certificate_codes_empty = DB::connection('ympimis_2')
        ->table('qa_certificate_codes')
        ->where('code',$code)
        ->where('code_number',$code_number)
        ->where('certificate_id',null)
        ->get();

        $code_generator = CodeGenerator::where('note', '=', 'qa_certificate')->first();
        $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

        $serial_number = $code_generator->prefix.$numbers;

        $code_generator->index = $code_generator->index+1;

        if ($empsync->department == 'Standardization Department') {
          $approval_type = 'QA';
          $certificate_name = explode(',', $certificate_codes->certificate_name)[0];
        }else{
          $approval_type = 'PRODUKSI';
          $certificate_name = explode(',', $certificate_codes->certificate_name)[1];
        }

        if (count($certificate_codes_empty) > 0) {
          $number = $certificate_codes_empty[0]->number;
          $certificate_code_old = DB::connection('ympimis_2')
          ->table('qa_certificate_codes')
          ->where('id',$certificate_codes_empty[0]->id)
          ->update([
            'certificate_id' => $serial_number,
            'employee_id' => $employee_id,
            'name' => $employee_name,
            'status' => 1,
            'updated_at' => date('Y-m-d H:i:s')
          ]); 
        }else{
          $last_certificate_codes = DB::connection('ympimis_2')
          ->table('qa_certificate_codes')
          ->where('code',$code)
          ->where('code_number',$code_number)
          ->orderBy('number','desc')
          ->first();
          $number = $last_certificate_codes->number+1;
          $number = sprintf('%03d', $last_certificate_codes->number+1);
          $certificate_code_new = DB::connection('ympimis_2')
          ->table('qa_certificate_codes')
          ->insert([
            'certificate_id' => $serial_number,
            'employee_id' => $employee_id,
            'name' => $employee_name,
            'code' => $code,
            'code_number' => $code_number,
            'number' => $number,
            'description' => $code_desc,
            'certificate_name' => $last_certificate_codes->certificate_name,
            'certificate_paper' => $last_certificate_codes->certificate_paper,
            'certificate_color' => $last_certificate_codes->certificate_color,
            'status' => 1,
            'remark' => 1,
            'created_by' => Auth::id(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ]); 
        }

        // $cert_code = DB::connection('ympimis_2')->table('qa_certificates')->where('')

        // for ($i=0; $i < count($subjects); $i++) { 
        $index_j = 5;
        $indexj2 = 3;
        for ($j=0; $j < $count_point; $j++) { 
            // if ($subject[$j] == $subjects[$i]) {
          if (!ISSET($presentase_result[$j])) {
            $presentase_results = null;
            $lulus = null;
          }else{
            if (end($subjects) == $subject[$j]) {
              $presentase_results = $presentase_result[$j+$indexj2];
              if ((int)$presentase_result[$j+$indexj2] < (int)str_replace('%', '', str_replace('>= / = ', '', $standard[$j+$indexj2]))) {
                $lulus = 'TIDAK LULUS';
              }else{
                $lulus = 'LULUS';
              }
              $indexj2--;
            }else{
              if ($j%6 != 0) {
                $presentase_results = $presentase_result[$j+$index_j];
                if ((int)$presentase_result[$j] < (int)str_replace('%', '', str_replace('>= / = ', '', $standard[$j])) || (int)$presentase_result[$j+$index_j] < (int)str_replace('%', '', str_replace('>= / = ', '', $standard[$j+$index_j]))) {
                  $lulus = 'TIDAK LULUS';
                }else{
                  $lulus = 'LULUS';
                }
              }else{
                $index_j = 5;
                $presentase_results = $presentase_result[$j];
                if ((int)$presentase_result[$j] < (int)str_replace('%', '', str_replace('>= / = ', '', $standard[$j])) || (int)$presentase_result[$j+$index_j] < (int)str_replace('%', '', str_replace('>= / = ', '', $standard[$j+$index_j]))) {
                  $lulus = 'TIDAK LULUS';
                }else{
                  $lulus = 'LULUS';
                }
              }
            }
          }
          if (!ISSET($presentase_a[$j])) {
            $presentase_as = null;
          }else{
            $presentase_as = $presentase_a[$j];
          }
          if (!ISSET($note[$j])) {
            $notes = 'Tidak Sertifikasi';
          }else{
            $notes = $note[$j];
          }
          $log = DB::connection('ympimis_2')->table('qa_certificates')->insert([
            'certificate_id' => $serial_number,
            'employee_id' => $employee_id,
            'name' => $employee_name,
            'periode_from' => $request->get('issued_date'),
            'periode_to' => $request->get('expired_date'),
            'certificate_code' => 'YMPI-QA-'.$code.'-'.$code_number.$number,
            'certificate_name' => $certificate_name,
            'subject' => $subject[$j],
            'test_type' => $test_type[$j],
            'category' => $category[$j],
            'weight' => $weight[$j],
            'question' => $question[$j],
            'question_result' => $question_result[$j],
            'presentase_result' => $presentase_results,
            'presentase_a' => $presentase_as,
            'note' => $notes,
            'standard' => str_replace('%', '', str_replace('>= / = ', '', $standard[$j])),
            'result_grade' => $lulus,
            'auditor_id' => $auditor_id,
            'auditor_name' => $auditor_name,
            'approval_type' => $approval_type,
            'staff_id' => $staff_id,
            'staff_name' => $staff_name,
            'staff_email' => $staff_email,
            'status' => 1,
            'created_by' => Auth::id(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
          ]);

          $index_j--;
            // }
        }


        $code_generator->save();

        self :: masterCertificateApproval($serial_number,$approval_type,$staff_id,$staff_name,$staff_email,$auditor_id,$auditor_name,$empsync->department,$empsync->section);

        //Save Merger
        $datas = DB::connection('ympimis_2')
        ->table('qa_certificate_codes')
        ->join('qa_certificates','qa_certificates.certificate_id','qa_certificate_codes.certificate_id')
        ->where('qa_certificate_codes.certificate_id',$serial_number)
        ->get();

        $data_subject = DB::connection('ympimis_2')
        ->table('qa_certificates')
        ->select('qa_certificates.subject')
        ->distinct()
        ->where('qa_certificates.certificate_id',$serial_number)
        ->get();

        $data_approval = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('qa_certificate_approvals.certificate_id',$serial_number)
        ->orderBy('id','desc')
        ->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper($datas[0]->certificate_paper, 'potrait');

        $pdf->loadView('qa.certificate.print', array(
          'datas' => $datas,
          'data_approval' => $data_approval,
        ));

        $depan = "QA Certificate - ".$serial_number." (".$datas[0]->certificate_code.").pdf";

        $pdf->save(public_path() . "/data_file/qa/certificate/".$depan);

        $pdf2 = \App::make('dompdf.wrapper');
        $pdf2->getDomPDF()->set_option("enable_php", true);
        $pdf2->getDomPDF()->set_option("enable_css_float", true);
        $pdf2->setPaper('A4', 'landscape');


        $pdf2->loadView('qa.certificate.print_landscape', array(
          'datas' => $datas,
          'data_subject' => $data_subject,
          'data_approval' => $data_approval,
        ));

        // return $pdf2->stream("QA Certificate.pdf");

        $belakang = "QA Certificate Belakang - ".$serial_number." (".$datas[0]->certificate_code.").pdf";

        $pdf2->save(public_path() . "/data_file/qa/certificate/".$belakang);

        $pdfFile1Path = public_path() . "/data_file/qa/certificate/".$depan;
        $pdfFile2Path = public_path() . "/data_file/qa/certificate/".$belakang;

        $merger = new Merger;
        $merger->addFile($pdfFile1Path);
        $merger->addFile($pdfFile2Path);
        $createdPdf = $merger->merge();

        $pathForTheMergedPdf = public_path() . "/data_file/qa/certificate_fix/".$serial_number.".pdf";

        file_put_contents($pathForTheMergedPdf, $createdPdf);

        $response = array(
          'status' => true,
          'message' => 'New Certificate Has Been Created',
          'certificate_id' => $serial_number
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
    public function masterCertificateApproval($certificate_id,$approval_type,$staff_id,$staff_name,$staff_email,$leader_id,$leader_name,$department,$section)
    {
      $app_leader = DB::connection('ympimis_2')->table('qa_certificate_approvals')->insert([
        'certificate_id' => $certificate_id,
        'approver_id' => $leader_id,
        'approver_name' => $leader_name,
        'approver_email' => '',
        'remark' => 'Leader QA',
        'approver_header' => 'Disusun',
        'priority' => '1',
        'created_by' => Auth::id(),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
      $app_staff = DB::connection('ympimis_2')->table('qa_certificate_approvals')->insert([
        'certificate_id' => $certificate_id,
        'approver_id' => $staff_id,
        'approver_name' => $staff_name,
        'approver_email' => $staff_email,
        'remark' => 'Staff QA',
        'approver_header' => 'Dicek',
        'created_by' => Auth::id(),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);

      $mail_to = $staff_email;

      $foreman = Approver::where('department','Standardization Department')->where('remark','Foreman')->first();
      $app_foreman = DB::connection('ympimis_2')->table('qa_certificate_approvals')->insert([
        'certificate_id' => $certificate_id,
        'approver_id' => $foreman->approver_id,
        'approver_name' => $foreman->approver_name,
        'approver_email' => $foreman->approver_email,
        'remark' => 'Foreman QA',
        'approver_header' => 'Dicek',
        'created_by' => Auth::id(),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
      if ($approval_type == 'PRODUKSI') {
        $foreman_produksi = Approver::where('department',$department)->where('remark','Foreman')->where('section','like','%'.$section.'%')->first();
        if (count($foreman_produksi) > 0) {
          $app_foreman_produksi = DB::connection('ympimis_2')->table('qa_certificate_approvals')->insert([
            'certificate_id' => $certificate_id,
            'approver_id' => $foreman_produksi->approver_id,
            'approver_name' => $foreman_produksi->approver_name,
            'approver_email' => $foreman_produksi->approver_email,
            'remark' => 'Foreman Produksi',
            'approver_header' => 'Dicek',
            'created_by' => Auth::id(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
          ]);
        }else{
          $chief_produksi = Approver::where('department',$department)->where('remark','Chief')->first();
          $app_chief_produksi = DB::connection('ympimis_2')->table('qa_certificate_approvals')->insert([
            'certificate_id' => $certificate_id,
            'approver_id' => $chief_produksi->approver_id,
            'approver_name' => $chief_produksi->approver_name,
            'approver_email' => $chief_produksi->approver_email,
            'remark' => 'Chief Produksi',
            'approver_header' => 'Dicek',
            'created_by' => Auth::id(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
          ]);
        }
      }

      $chief = Approver::where('department','Standardization Department')->where('remark','Chief')->where('section','QA Process Control Section')->first();
      $app_chief = DB::connection('ympimis_2')->table('qa_certificate_approvals')->insert([
        'certificate_id' => $certificate_id,
        'approver_id' => $chief->approver_id,
        'approver_name' => $chief->approver_name,
        'approver_email' => $chief->approver_email,
        'remark' => 'Chief QA',
        'approver_header' => 'Dicek',
        'created_by' => Auth::id(),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
      if ($approval_type == 'PRODUKSI') {
        $manager_produksi = Approver::where('department',$department)->where('remark','Manager')->first();
        if (count($manager_produksi) > 0) {
          $app_manager_produksi = DB::connection('ympimis_2')->table('qa_certificate_approvals')->insert([
            'certificate_id' => $certificate_id,
            'approver_id' => $manager_produksi->approver_id,
            'approver_name' => $manager_produksi->approver_name,
            'approver_email' => $manager_produksi->approver_email,
            'remark' => 'Manager Produksi',
            'approver_header' => 'Disetujui',
            'created_by' => Auth::id(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
          ]);
        }
      }

      $app_manager_qa = DB::connection('ympimis_2')->table('qa_certificate_approvals')->insert([
        'certificate_id' => $certificate_id,
        'approver_id' => 'PI2304052',
        'approver_name' => 'Toshiki Hayashi',
        'approver_email' => 'toshiki.hayashi@music.yamaha.com',
        'remark' => 'Manager In Charge QA',
        'created_by' => Auth::id(),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);

      $manager_std = Approver::where('department','Standardization Department')->where('remark','Manager')->first();
      $app_manager_std = DB::connection('ympimis_2')->table('qa_certificate_approvals')->insert([
        'certificate_id' => $certificate_id,
        'approver_id' => $manager_std->approver_id,
        'approver_name' => $manager_std->approver_name,
        'approver_email' => $manager_std->approver_email,
        'remark' => 'Manager STD',
        'created_by' => Auth::id(),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);

      $app_gm = DB::connection('ympimis_2')->table('qa_certificate_approvals')->insert([
        'certificate_id' => $certificate_id,
        'approver_id' => 'PI9709001',
        'approver_name' => 'Arief Soekamto',
        'approver_email' => 'arief.soekamto@music.yamaha.com',
        'remark' => 'Director',
        'created_by' => Auth::id(),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);

      $app_presdir = DB::connection('ympimis_2')->table('qa_certificate_approvals')->insert([
        'certificate_id' => $certificate_id,
        'approver_id' => 'PI2111044',
        'approver_name' => 'Hiromichi Ichimura',
        'approver_email' => 'hiromichi.ichimura@music.yamaha.com',
        'remark' => 'President Director',
        'created_by' => Auth::id(),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);

      // Mail::to($mail_to)
      // ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
      // ->send(new SendEmail($data, 'qa_certificate'));


    }

    public function reviewCertificate($certificate_id,$remark)
    {
      $approval_now = DB::connection('ympimis_2')
      ->table('qa_certificate_approvals')
      ->where('certificate_id',$certificate_id)
      ->where('remark',$remark)
      ->first();

      // $datas = DB::connection('ympimis_2')
      // ->table('qa_certificate_codes')
      // ->join('qa_certificates','qa_certificates.certificate_id','qa_certificate_codes.certificate_id')
      // ->where('qa_certificate_codes.certificate_id',$certificate_id)
      // ->get();

      // $data_subject = DB::connection('ympimis_2')
      // ->table('qa_certificates')
      // ->select('qa_certificates.subject')
      // ->distinct()
      // ->where('qa_certificates.certificate_id',$certificate_id)
      // ->get();

      // $data_approval = DB::connection('ympimis_2')
      // ->table('qa_certificate_approvals')
      // ->where('qa_certificate_approvals.certificate_id',$certificate_id)
      // ->orderBy('id','desc')
      // ->get();

      // // return view('qa.certificate.print_landscape')->with('datas',$datas)->with('data_subject',$data_subject)->with('data_approval',$data_approval);

      // $pdf = \App::make('dompdf.wrapper');
      // $pdf->getDomPDF()->set_option("enable_php", true);
      // $pdf->setPaper($datas[0]->certificate_paper, 'potrait');

      // $pdf->loadView('qa.certificate.print', array(
      //     'datas' => $datas,
      //     'data_approval' => $data_approval,
      // ));

      // $depan = "QA Certificate - ".$certificate_id." (".$datas[0]->certificate_code.").pdf";

      // $pdf->save(public_path() . "/data_file/qa/certificate/".$depan);

      // $pdf2 = \App::make('dompdf.wrapper');
      // $pdf2->getDomPDF()->set_option("enable_php", true);
      // $pdf2->getDomPDF()->set_option("enable_css_float", true);
      // $pdf2->setPaper('A4', 'landscape');

      // $pdf2->loadView('qa.certificate.print_landscape', array(
      //     'datas' => $datas,
      //     'data_subject' => $data_subject,
      //     'data_approval' => $data_approval,
      // ));

      // // return view('qa.certificate.print')->with('datas',$datas)->with('data_approval',$data_approval);

      // // return $pdf->stream("QA Certificate.pdf");
      // // return $pdf2->stream("QA Certificate.pdf");

      // $belakang = "QA Certificate Belakang - ".$certificate_id." (".$datas[0]->certificate_code.").pdf";

      // $pdf2->save(public_path() . "/data_file/qa/certificate/".$belakang);

      // $pdfFile1Path = public_path() . "/data_file/qa/certificate/".$depan;
      // $pdfFile2Path = public_path() . "/data_file/qa/certificate/".$belakang;

      // $merger = new Merger;
      // $merger->addFile($pdfFile1Path);
      // $merger->addFile($pdfFile2Path);
      // $createdPdf = $merger->merge();

      // $pathForTheMergedPdf = public_path() . "/data_file/qa/certificate_fix/".$certificate_id.".pdf";

      // file_put_contents($pathForTheMergedPdf, $createdPdf);

      $file_path = asset("/data_file/qa/certificate_fix/".$certificate_id.".pdf");

      return view('qa.certificate.review')->with('file',$file_path)->with('approval_now',$approval_now)->with('certificate_id',$certificate_id)->with('remark',$remark);
    }

    public function resendCertificateCode($certificate_approval_id,$remark)
    {
      try {
        $data_all = [];

        $now = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->select(DB::RAW('DISTINCT(certificate_id)'),'approver_email')
        ->where('remark',$remark)
        ->where('certificate_approval_id',$certificate_approval_id)
        ->get();

        $mail_to = $now[0]->approver_email;
        for ($i=0; $i < count($now); $i++) { 
          $datas = DB::connection('ympimis_2')
          ->table('qa_certificates')
          ->where('certificate_id',$now[$i]->certificate_id)
          ->first();

          $datas_nilai = DB::connection('ympimis_2')
          ->select("SELECT DISTINCT
            ( a.`certificate_id` ),
            `subject`,
            ( SELECT MIN( presentase_result ) FROM qa_certificates WHERE qa_certificates.certificate_id = a.certificate_id AND qa_certificates.`subject` = a.`subject` ) AS presentase_result,
            (
            SELECT DISTINCT
            ( result_grade ) 
            FROM
            qa_certificates 
            WHERE
            qa_certificates.certificate_id = a.certificate_id 
            AND qa_certificates.`subject` = a.`subject` 
            AND note = '-' 
            AND category LIKE '%Total%' 
            ) AS result_grade 
            FROM
            `qa_certificates` AS a 
            WHERE
            a.certificate_id = '".$now[$i]->certificate_id."' 
            AND a.note = '-' 
            GROUP BY
            a.`subject`,
            a.certificate_id");

          $data = array(
            'datas' => $datas,
            'datas_nilai' => $datas_nilai,
            'certificate_id' => $now[$i]->certificate_id,
            'remark' => $remark,
            'next_remark' => $remark
          );
          array_push($data_all, $data);
        }

        Mail::to($mail_to)
        ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
        ->send(new SendEmail($data_all, 'qa_certificate_collective'));

        $response = array(
          'status' => true,
          'message' => 'Resend Email Succeeded'
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

    public function certificateApproval(Request $request)
    {
      try {
        $certificate_id = [];
        if ($request->get('remark') == 'Leader QA') {
          $code_generator = CodeGenerator::where('note', '=', 'qa_certificate_approval')->first();
          $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

          $serial_number = $code_generator->prefix.$numbers;

          $code_generator->index = $code_generator->index+1;

          $certificate_id = $request->get('certificate_id');

          $data_all = [];

          for ($i=0; $i < count($certificate_id); $i++) { 
            $next = DB::connection('ympimis_2')
            ->table('qa_certificate_approvals')
            ->where('remark','Staff QA')
            ->where('certificate_id',$certificate_id[$i])
            ->first();

            $appr_code = DB::connection('ympimis_2')
            ->table('qa_certificate_approvals')
            ->where('certificate_id',$certificate_id[$i])
            ->update([
              'certificate_approval_id' => $serial_number
            ]);

            $mail_to = $next->approver_email;

            $next = DB::connection('ympimis_2')
            ->table('qa_certificate_approvals')
            ->where('remark','Staff QA')
            ->where('certificate_id',$certificate_id[$i])
            ->update([
              'priority' => 1,
              'updated_at' => date('Y-m-d H:i:s')
            ]);

            $certificate_lama_update = DB::connection('ympimis_2')
            ->table('qa_certificate_approvals')
            ->where('certificate_id',$certificate_id[$i])
            ->where('remark',$request->get('remark'))
            ->update([
              'approver_status' => "Approved",
              'approved_at' => date('Y-m-d H:i:s'),
              'priority' => null,
              'updated_at' => date('Y-m-d H:i:s')
            ]);

            $next_remark = 'Staff QA';

            $datas = DB::connection('ympimis_2')
            ->table('qa_certificates')
            ->where('certificate_id',$certificate_id[$i])
            ->first();

            $datas_nilai = DB::connection('ympimis_2')
            ->select("SELECT DISTINCT
              ( a.`certificate_id` ),
              `subject`,
              ( SELECT MIN( presentase_result ) FROM qa_certificates WHERE qa_certificates.certificate_id = a.certificate_id AND qa_certificates.`subject` = a.`subject` ) AS presentase_result,
              (
              SELECT DISTINCT
              ( result_grade ) 
              FROM
              qa_certificates 
              WHERE
              qa_certificates.certificate_id = a.certificate_id 
              AND qa_certificates.`subject` = a.`subject` 
              AND note = '-' 
              AND category LIKE '%Total%' 
              ) AS result_grade 
              FROM
              `qa_certificates` AS a 
              WHERE
              a.certificate_id = '".$certificate_id[$i]."' 
              AND a.note = '-' 
              GROUP BY
              a.`subject`,
              a.certificate_id");

            $data = array(
              'datas' => $datas,
              'datas_nilai' => $datas_nilai,
              'certificate_id' => $certificate_id[$i],
              'remark' => $request->get('remark'),
              'next_remark' => $next_remark
            );
            array_push($data_all, $data);
          }

          $code_generator->save();

          Mail::to($mail_to)
          ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
          ->send(new SendEmail($data_all, 'qa_certificate_collective'));
        }

        if ($request->get('remark') == 'Staff QA') {
          $certificate_id = [];
          $code_generator = CodeGenerator::where('note', '=', 'qa_certificate_approval')->first();
          $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

          $serial_number = $code_generator->prefix.$numbers;

          $code_generator->index = $code_generator->index+1;
          $certificate_id = $request->get('certificate_id');

          $data_all = [];

          for ($i=0; $i < count($certificate_id); $i++) { 
            $next = DB::connection('ympimis_2')
            ->table('qa_certificate_approvals')
            ->where('remark','Foreman QA')
            ->where('certificate_id',$certificate_id[$i])
            ->first();

            $mail_to = $next->approver_email;

            $next = DB::connection('ympimis_2')
            ->table('qa_certificate_approvals')
            ->where('remark','Foreman QA')
            ->where('certificate_id',$certificate_id[$i])
            ->update([
              'priority' => 1,
              'updated_at' => date('Y-m-d H:i:s')
            ]);

            $appr_code = DB::connection('ympimis_2')
            ->table('qa_certificate_approvals')
            ->where('certificate_id',$certificate_id[$i])
            ->update([
              'certificate_approval_id' => $serial_number
            ]);

            $certificate_lama_update = DB::connection('ympimis_2')
            ->table('qa_certificate_approvals')
            ->where('certificate_id',$certificate_id[$i])
            ->where('remark',$request->get('remark'))
            ->update([
              'approver_status' => "Approved",
              'approved_at' => date('Y-m-d H:i:s'),
              'priority' => null,
              'updated_at' => date('Y-m-d H:i:s')
            ]);

            $next_remark = 'Foreman QA';

            $datas = DB::connection('ympimis_2')
            ->table('qa_certificates')
            ->where('certificate_id',$certificate_id[$i])
            ->first();

            $datas_nilai = DB::connection('ympimis_2')
            ->select("SELECT DISTINCT
              ( a.`certificate_id` ),
              `subject`,
              ( SELECT MIN( presentase_result ) FROM qa_certificates WHERE qa_certificates.certificate_id = a.certificate_id AND qa_certificates.`subject` = a.`subject` ) AS presentase_result,
              (
              SELECT DISTINCT
              ( result_grade ) 
              FROM
              qa_certificates 
              WHERE
              qa_certificates.certificate_id = a.certificate_id 
              AND qa_certificates.`subject` = a.`subject` 
              AND note = '-' 
              AND category LIKE '%Total%' 
              ) AS result_grade 
              FROM
              `qa_certificates` AS a 
              WHERE
              a.certificate_id = '".$certificate_id[$i]."' 
              AND a.note = '-' 
              GROUP BY
              a.`subject`,
              a.certificate_id");

            $data = array(
              'datas' => $datas,
              'datas_nilai' => $datas_nilai,
              'certificate_id' => $certificate_id[$i],
              'remark' => $request->get('remark'),
              'next_remark' => $next_remark
            );
            array_push($data_all, $data);
          }

          $code_generator->save();

          Mail::to($mail_to)
          ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
          ->send(new SendEmail($data_all, 'qa_certificate_collective'));
        }

        if ($request->get('remark') == 'Foreman QA') {
          $certificate_id = $request->get('certificate_id');

          $data_all_qa = [];
          $mail_to_qa = [];
          $mail_to_prod = [];
          $data_all_prod = [];

          for ($i=0; $i < count($certificate_id); $i++) { 

            $certificate_lama_update = DB::connection('ympimis_2')
            ->table('qa_certificate_approvals')
            ->where('certificate_id',$certificate_id[$i])
            ->where('remark',$request->get('remark'))
            ->update([
              'approver_status' => "Approved",
              'approved_at' => date('Y-m-d H:i:s'),
              'priority' => null,
              'updated_at' => date('Y-m-d H:i:s')
            ]);

            $datas = DB::connection('ympimis_2')
            ->table('qa_certificates')
            ->where('certificate_id',$certificate_id[$i])
            ->first();

            if ($datas->approval_type == 'QA') {
             $datas_nilai = DB::connection('ympimis_2')
             ->select("SELECT DISTINCT
              ( a.`certificate_id` ),
              `subject`,
              ( SELECT MIN( presentase_result ) FROM qa_certificates WHERE qa_certificates.certificate_id = a.certificate_id AND qa_certificates.`subject` = a.`subject` ) AS presentase_result,
              (
              SELECT DISTINCT
              ( result_grade ) 
              FROM
              qa_certificates 
              WHERE
              qa_certificates.certificate_id = a.certificate_id 
              AND qa_certificates.`subject` = a.`subject` 
              AND note = '-' 
              AND category LIKE '%Total%' 
              ) AS result_grade 
              FROM
              `qa_certificates` AS a 
              WHERE
              a.certificate_id = '".$certificate_id[$i]."' 
              AND a.note = '-' 
              GROUP BY
              a.`subject`,
              a.certificate_id");
             $data = array(
              'datas_nilai' => $datas_nilai,
              'datas' => $datas,
              'certificate_id' => $certificate_id[$i],
              'remark' => $request->get('remark'),
              'next_remark' => 'Chief QA'
            );
             array_push($data_all_qa, $data);

             $next = DB::connection('ympimis_2')
             ->table('qa_certificate_approvals')
             ->where('remark','Chief QA')
             ->where('certificate_id',$certificate_id[$i])
             ->first();

             array_push($mail_to_qa, $next->approver_email);

             $next = DB::connection('ympimis_2')
             ->table('qa_certificate_approvals')
             ->where('remark','Chief QA')
             ->where('certificate_id',$certificate_id[$i])
             ->update([
              'priority' => 1,
              'updated_at' => date('Y-m-d H:i:s')
            ]);
           }else{
             $datas_nilai = DB::connection('ympimis_2')
             ->select("SELECT DISTINCT
              ( a.`certificate_id` ),
              `subject`,
              ( SELECT MIN( presentase_result ) FROM qa_certificates WHERE qa_certificates.certificate_id = a.certificate_id AND qa_certificates.`subject` = a.`subject` ) AS presentase_result,
              (
              SELECT DISTINCT
              ( result_grade ) 
              FROM
              qa_certificates 
              WHERE
              qa_certificates.certificate_id = a.certificate_id 
              AND qa_certificates.`subject` = a.`subject` 
              AND note = '-' 
              AND category LIKE '%Total%' 
              ) AS result_grade 
              FROM
              `qa_certificates` AS a 
              WHERE
              a.certificate_id = '".$certificate_id[$i]."' 
              AND a.note = '-' 
              GROUP BY
              a.`subject`,
              a.certificate_id");

             $datas_approval = DB::connection('ympimis_2')
             ->table('qa_certificate_approvals')
             ->where('certificate_id',$certificate_id[$i])
             ->first();

             $next = DB::connection('ympimis_2')
             ->table('qa_certificate_approvals')
             ->where(function($query) {
              $query->where('remark',"Foreman Produksi")
              ->orWhere('remark',"Chief Produksi");
            })
             ->where('certificate_id',$certificate_id[$i])
             ->first();

             array_push($mail_to_prod, $next->approver_email);

             $data = array(
              'datas' => $datas,
              'datas_nilai' => $datas_nilai,
              'certificate_id' => $certificate_id[$i],
              'remark' => $request->get('remark'),
              'next_remark' => $next->remark
            );
             array_push($data_all_prod, $data);

             $next = DB::connection('ympimis_2')
             ->table('qa_certificate_approvals')
             ->where(function($query) {
              $query->where('remark',"Foreman Produksi")
              ->orWhere('remark',"Chief Produksi");
            })
             ->where('certificate_id',$certificate_id[$i])
             ->update([
              'priority' => 1,
              'updated_at' => date('Y-m-d H:i:s')
            ]);

           }
         }

         if (count($data_all_prod) > 0) {
          $code_generator = CodeGenerator::where('note', '=', 'qa_certificate_approval')->first();
          $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

          $serial_number = $code_generator->prefix.$numbers;

          $code_generator->index = $code_generator->index+1;
          for ($i=0; $i < count($data_all_prod); $i++) { 
            $appr_code = DB::connection('ympimis_2')
            ->table('qa_certificate_approvals')
            ->where('certificate_id',$data_all_prod[$i]['certificate_id'])
            ->update([
              'certificate_approval_id' => $serial_number
            ]);              
          }
          $code_generator->save();
          Mail::to($mail_to_prod)
          ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
          ->send(new SendEmail($data_all_prod, 'qa_certificate_collective'));
        }

        if (count($data_all_qa) > 0) {

          $code_generator = CodeGenerator::where('note', '=', 'qa_certificate_approval')->first();
          $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

          $serial_number = $code_generator->prefix.$numbers;

          $code_generator->index = $code_generator->index+1;
          for ($i=0; $i < count($data_all_qa); $i++) { 
            $appr_code = DB::connection('ympimis_2')
            ->table('qa_certificate_approvals')
            ->where('certificate_id',$data_all_qa[$i]['certificate_id'])
            ->update([
              'certificate_approval_id' => $serial_number
            ]);              
          }
          $code_generator->save();

          Mail::to($mail_to_qa)
          ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
          ->send(new SendEmail($data_all_qa, 'qa_certificate_collective'));
        }
      }

      if ($request->get('remark') == 'Chief QA') {
        $certificate_id = $request->get('certificate_id');

        $data_all_qa = [];
        $data_all_prod = [];
        $mail_to_qa = [];
        $mail_to_prod = [];

        for ($i=0; $i < count($certificate_id); $i++) { 
            // $data_all_prod = [];

          $certificate_lama_update = DB::connection('ympimis_2')
          ->table('qa_certificate_approvals')
          ->where('certificate_id',$certificate_id[$i])
          ->where('remark',$request->get('remark'))
          ->update([
            'approver_status' => "Approved",
            'approved_at' => date('Y-m-d H:i:s'),
            'priority' => null,
            'updated_at' => date('Y-m-d H:i:s')
          ]);

          $datas = DB::connection('ympimis_2')
          ->table('qa_certificates')
          ->where('certificate_id',$certificate_id[$i])
          ->first();

          if ($datas->approval_type == 'QA') {
           $datas_nilai = DB::connection('ympimis_2')
           ->select("SELECT DISTINCT
            ( a.`certificate_id` ),
            `subject`,
            ( SELECT MIN( presentase_result ) FROM qa_certificates WHERE qa_certificates.certificate_id = a.certificate_id AND qa_certificates.`subject` = a.`subject` ) AS presentase_result,
            (
            SELECT DISTINCT
            ( result_grade ) 
            FROM
            qa_certificates 
            WHERE
            qa_certificates.certificate_id = a.certificate_id 
            AND qa_certificates.`subject` = a.`subject` 
            AND note = '-' 
            AND category LIKE '%Total%' 
            ) AS result_grade 
            FROM
            `qa_certificates` AS a 
            WHERE
            a.certificate_id = '".$certificate_id[$i]."' 
            AND a.note = '-' 
            GROUP BY
            a.`subject`,
            a.certificate_id");
           $data = array(
            'datas_nilai' => $datas_nilai,
            'datas' => $datas,
            'certificate_id' => $certificate_id[$i],
            'remark' => $request->get('remark'),
            'next_remark' => 'Manager In Charge QA'
          );
           array_push($data_all_qa, $data);

           $next = DB::connection('ympimis_2')
           ->table('qa_certificate_approvals')
           ->where('remark','Manager In Charge QA')
           ->where('certificate_id',$certificate_id[$i])
           ->first();

           array_push($mail_to_qa, $next->approver_email);

           $next = DB::connection('ympimis_2')
           ->table('qa_certificate_approvals')
           ->where('remark','Manager In Charge QA')
           ->where('certificate_id',$certificate_id[$i])
           ->update([
            'priority' => 1,
            'updated_at' => date('Y-m-d H:i:s')
          ]);
         }else{
           $datas_nilai = DB::connection('ympimis_2')
           ->select("SELECT DISTINCT
            ( a.`certificate_id` ),
            `subject`,
            ( SELECT MIN( presentase_result ) FROM qa_certificates WHERE qa_certificates.certificate_id = a.certificate_id AND qa_certificates.`subject` = a.`subject` ) AS presentase_result,
            (
            SELECT DISTINCT
            ( result_grade ) 
            FROM
            qa_certificates 
            WHERE
            qa_certificates.certificate_id = a.certificate_id 
            AND qa_certificates.`subject` = a.`subject` 
            AND note = '-' 
            AND category LIKE '%Total%' 
            ) AS result_grade 
            FROM
            `qa_certificates` AS a 
            WHERE
            a.certificate_id = '".$certificate_id[$i]."' 
            AND a.note = '-' 
            GROUP BY
            a.`subject`,
            a.certificate_id");
           $data = array(
            'datas' => $datas,
            'datas_nilai' => $datas_nilai,
            'certificate_id' => $certificate_id[$i],
            'remark' => $request->get('remark'),
            'next_remark' => 'Manager Produksi'
          );
           array_push($data_all_prod, $data);
           $next = DB::connection('ympimis_2')
           ->table('qa_certificate_approvals')
           ->where('remark','Manager Produksi')
           ->where('certificate_id',$certificate_id[$i])
           ->first();

           array_push($mail_to_prod, $next->approver_email);

           $next = DB::connection('ympimis_2')
           ->table('qa_certificate_approvals')
           ->where('remark','Manager Produksi')
           ->where('certificate_id',$certificate_id[$i])
           ->update([
            'priority' => 1,
            'updated_at' => date('Y-m-d H:i:s')
          ]);

         }
       }

       if (count($data_all_prod) > 0) {
        $code_generator = CodeGenerator::where('note', '=', 'qa_certificate_approval')->first();
        $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

        $serial_number = $code_generator->prefix.$numbers;

        $code_generator->index = $code_generator->index+1;
        for ($i=0; $i < count($data_all_prod); $i++) { 
          $appr_code = DB::connection('ympimis_2')
          ->table('qa_certificate_approvals')
          ->where('certificate_id',$data_all_prod[$i]['certificate_id'])
          ->update([
            'certificate_approval_id' => $serial_number
          ]);              
        }
        $code_generator->save();
        Mail::to($mail_to_prod)
        ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
        ->send(new SendEmail($data_all_prod, 'qa_certificate_collective'));
      }

      if (count($data_all_qa) > 0) {
        $code_generator = CodeGenerator::where('note', '=', 'qa_certificate_approval')->first();
        $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

        $serial_number = $code_generator->prefix.$numbers;

        $code_generator->index = $code_generator->index+1;
        for ($i=0; $i < count($data_all_qa); $i++) { 
          $appr_code = DB::connection('ympimis_2')
          ->table('qa_certificate_approvals')
          ->where('certificate_id',$data_all_qa[$i]['certificate_id'])
          ->update([
            'certificate_approval_id' => $serial_number
          ]);              
        }
        $code_generator->save();
        Mail::to($mail_to_qa)
        ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
        ->send(new SendEmail($data_all_qa, 'qa_certificate_collective'));
      }
    }

    if ($request->get('remark') == 'Foreman Produksi' || $request->get('remark') == 'Chief Produksi') {
      $certificate_id = $request->get('certificate_id');

      $data_all = [];

      $code_generator = CodeGenerator::where('note', '=', 'qa_certificate_approval')->first();
      $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

      $serial_number = $code_generator->prefix.$numbers;

      $code_generator->index = $code_generator->index+1;

      for ($i=0; $i < count($certificate_id); $i++) { 
        $next = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('remark','Chief QA')
        ->where('certificate_id',$certificate_id[$i])
        ->first();

        $mail_to = $next->approver_email;

        $next = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('remark','Chief QA')
        ->where('certificate_id',$certificate_id[$i])
        ->update([
          'priority' => 1,
          'updated_at' => date('Y-m-d H:i:s')
        ]);

        $certificate_lama_update = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('certificate_id',$certificate_id[$i])
        ->where('remark',$request->get('remark'))
        ->update([
          'approver_status' => "Approved",
          'approved_at' => date('Y-m-d H:i:s'),
          'priority' => null,
          'updated_at' => date('Y-m-d H:i:s')
        ]);

        $appr_code = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('certificate_id',$certificate_id[$i])
        ->update([
          'certificate_approval_id' => $serial_number
        ]);

        $next_remark = 'Chief QA';

        $datas = DB::connection('ympimis_2')
        ->table('qa_certificates')
        ->where('certificate_id',$certificate_id[$i])
        ->first();

        $datas_nilai = DB::connection('ympimis_2')
        ->select("SELECT DISTINCT
          ( a.`certificate_id` ),
          `subject`,
          ( SELECT MIN( presentase_result ) FROM qa_certificates WHERE qa_certificates.certificate_id = a.certificate_id AND qa_certificates.`subject` = a.`subject` ) AS presentase_result,
          (
          SELECT DISTINCT
          ( result_grade ) 
          FROM
          qa_certificates 
          WHERE
          qa_certificates.certificate_id = a.certificate_id 
          AND qa_certificates.`subject` = a.`subject` 
          AND note = '-' 
          AND category LIKE '%Total%' 
          ) AS result_grade 
          FROM
          `qa_certificates` AS a 
          WHERE
          a.certificate_id = '".$certificate_id[$i]."' 
          AND a.note = '-' 
          GROUP BY
          a.`subject`,
          a.certificate_id");

        $data = array(
          'datas' => $datas,
          'datas_nilai' => $datas_nilai,
          'certificate_id' => $certificate_id[$i],
          'remark' => $request->get('remark'),
          'next_remark' => $next_remark
        );
        array_push($data_all, $data);
      }

      $code_generator->save();

      Mail::to($mail_to)
      ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
      ->send(new SendEmail($data_all, 'qa_certificate_collective'));
    }

    if ($request->get('remark') == 'Manager Produksi') {
      $certificate_id = $request->get('certificate_id');

      $data_all = [];

      $code_generator = CodeGenerator::where('note', '=', 'qa_certificate_approval')->first();
      $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

      $serial_number = $code_generator->prefix.$numbers;

      $code_generator->index = $code_generator->index+1;

      for ($i=0; $i < count($certificate_id); $i++) { 
        $next = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('remark','Manager In Charge QA')
        ->where('certificate_id',$certificate_id[$i])
        ->first();

        $mail_to = $next->approver_email;

        $next = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('remark','Manager In Charge QA')
        ->where('certificate_id',$certificate_id[$i])
        ->update([
          'priority' => 1,
          'updated_at' => date('Y-m-d H:i:s')
        ]);

        $certificate_lama_update = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('certificate_id',$certificate_id[$i])
        ->where('remark',$request->get('remark'))
        ->update([
          'approver_status' => "Approved",
          'approved_at' => date('Y-m-d H:i:s'),
          'priority' => null,
          'updated_at' => date('Y-m-d H:i:s')
        ]);

        $appr_code = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('certificate_id',$certificate_id[$i])
        ->update([
          'certificate_approval_id' => $serial_number
        ]);

        $next_remark = 'Manager In Charge QA';

        $datas = DB::connection('ympimis_2')
        ->table('qa_certificates')
        ->where('certificate_id',$certificate_id[$i])
        ->first();

        $datas_nilai = DB::connection('ympimis_2')
        ->select("SELECT DISTINCT
          ( a.`certificate_id` ),
          `subject`,
          ( SELECT MIN( presentase_result ) FROM qa_certificates WHERE qa_certificates.certificate_id = a.certificate_id AND qa_certificates.`subject` = a.`subject` ) AS presentase_result,
          (
          SELECT DISTINCT
          ( result_grade ) 
          FROM
          qa_certificates 
          WHERE
          qa_certificates.certificate_id = a.certificate_id 
          AND qa_certificates.`subject` = a.`subject` 
          AND note = '-' 
          AND category LIKE '%Total%' 
          ) AS result_grade 
          FROM
          `qa_certificates` AS a 
          WHERE
          a.certificate_id = '".$certificate_id[$i]."' 
          AND a.note = '-' 
          GROUP BY
          a.`subject`,
          a.certificate_id");

        $data = array(
          'datas' => $datas,
          'datas_nilai' => $datas_nilai,
          'certificate_id' => $certificate_id[$i],
          'remark' => $request->get('remark'),
          'next_remark' => $next_remark
        );
        array_push($data_all, $data);
      }

      $code_generator->save();

      Mail::to($mail_to)
      ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
      ->send(new SendEmail($data_all, 'qa_certificate_collective'));
    }

    if ($request->get('remark') == 'Manager In Charge QA') {
      $certificate_id = $request->get('certificate_id');

      $data_all = [];

      $code_generator = CodeGenerator::where('note', '=', 'qa_certificate_approval')->first();
      $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

      $serial_number = $code_generator->prefix.$numbers;

      $code_generator->index = $code_generator->index+1;

      for ($i=0; $i < count($certificate_id); $i++) { 
        $next = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('remark','Manager STD')
        ->where('certificate_id',$certificate_id[$i])
        ->first();

        $mail_to = $next->approver_email;

        $next = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('remark','Manager STD')
        ->where('certificate_id',$certificate_id[$i])
        ->update([
          'priority' => 1,
          'updated_at' => date('Y-m-d H:i:s')
        ]);

        $certificate_lama_update = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('certificate_id',$certificate_id[$i])
        ->where('remark',$request->get('remark'))
        ->update([
          'approver_status' => "Approved",
          'approved_at' => date('Y-m-d H:i:s'),
          'priority' => null,
          'updated_at' => date('Y-m-d H:i:s')
        ]);

        $appr_code = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('certificate_id',$certificate_id[$i])
        ->update([
          'certificate_approval_id' => $serial_number
        ]);

        $next_remark = 'Manager STD';

        $datas = DB::connection('ympimis_2')
        ->table('qa_certificates')
        ->where('certificate_id',$certificate_id[$i])
        ->first();

        $datas_nilai = DB::connection('ympimis_2')
        ->select("SELECT DISTINCT
          ( a.`certificate_id` ),
          `subject`,
          ( SELECT MIN( presentase_result ) FROM qa_certificates WHERE qa_certificates.certificate_id = a.certificate_id AND qa_certificates.`subject` = a.`subject` ) AS presentase_result,
          (
          SELECT DISTINCT
          ( result_grade ) 
          FROM
          qa_certificates 
          WHERE
          qa_certificates.certificate_id = a.certificate_id 
          AND qa_certificates.`subject` = a.`subject` 
          AND note = '-' 
          AND category LIKE '%Total%' 
          ) AS result_grade 
          FROM
          `qa_certificates` AS a 
          WHERE
          a.certificate_id = '".$certificate_id[$i]."' 
          AND a.note = '-' 
          GROUP BY
          a.`subject`,
          a.certificate_id");

        $data = array(
          'datas' => $datas,
          'datas_nilai' => $datas_nilai,
          'certificate_id' => $certificate_id[$i],
          'remark' => $request->get('remark'),
          'next_remark' => $next_remark
        );
        array_push($data_all, $data);
      }
      $code_generator->save();

      Mail::to($mail_to)
      ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
      ->send(new SendEmail($data_all, 'qa_certificate_collective'));
    }

    if ($request->get('remark') == 'Manager STD') {
      $certificate_id = $request->get('certificate_id');

      $data_all = [];

      $code_generator = CodeGenerator::where('note', '=', 'qa_certificate_approval')->first();
      $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

      $serial_number = $code_generator->prefix.$numbers;

      $code_generator->index = $code_generator->index+1;

      for ($i=0; $i < count($certificate_id); $i++) { 
        $next = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('remark','Director')
        ->where('certificate_id',$certificate_id[$i])
        ->first();

        $mail_to = $next->approver_email;

        $next = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('remark','Director')
        ->where('certificate_id',$certificate_id[$i])
        ->update([
          'priority' => 1,
          'updated_at' => date('Y-m-d H:i:s')
        ]);

        $certificate_lama_update = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('certificate_id',$certificate_id[$i])
        ->where('remark',$request->get('remark'))
        ->update([
          'approver_status' => "Approved",
          'approved_at' => date('Y-m-d H:i:s'),
          'priority' => null,
          'updated_at' => date('Y-m-d H:i:s')
        ]);

        $appr_code = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('certificate_id',$certificate_id[$i])
        ->update([
          'certificate_approval_id' => $serial_number
        ]);

        $next_remark = 'Director';

        $datas = DB::connection('ympimis_2')
        ->table('qa_certificates')
        ->where('certificate_id',$certificate_id[$i])
        ->first();

        $datas_nilai = DB::connection('ympimis_2')
        ->select("SELECT DISTINCT
          ( a.`certificate_id` ),
          `subject`,
          ( SELECT MIN( presentase_result ) FROM qa_certificates WHERE qa_certificates.certificate_id = a.certificate_id AND qa_certificates.`subject` = a.`subject` ) AS presentase_result,
          (
          SELECT DISTINCT
          ( result_grade ) 
          FROM
          qa_certificates 
          WHERE
          qa_certificates.certificate_id = a.certificate_id 
          AND qa_certificates.`subject` = a.`subject` 
          AND note = '-' 
          AND category LIKE '%Total%' 
          ) AS result_grade 
          FROM
          `qa_certificates` AS a 
          WHERE
          a.certificate_id = '".$certificate_id[$i]."' 
          AND a.note = '-' 
          GROUP BY
          a.`subject`,
          a.certificate_id");

        $data = array(
          'datas' => $datas,
          'datas_nilai' => $datas_nilai,
          'certificate_id' => $certificate_id[$i],
          'remark' => $request->get('remark'),
          'next_remark' => $next_remark
        );
        array_push($data_all, $data);
      }
      $code_generator->save();

      Mail::to($mail_to)
      ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
      ->send(new SendEmail($data_all, 'qa_certificate_collective'));
    }

    if ($request->get('remark') == 'Director') {
      $certificate_id = $request->get('certificate_id');

      $data_all = [];

      $code_generator = CodeGenerator::where('note', '=', 'qa_certificate_approval')->first();
      $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

      $serial_number = $code_generator->prefix.$numbers;

      $code_generator->index = $code_generator->index+1;

      for ($i=0; $i < count($certificate_id); $i++) { 
        $next = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('remark','President Director')
        ->where('certificate_id',$certificate_id[$i])
        ->first();

        $mail_to = $next->approver_email;

        $next = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('remark','President Director')
        ->where('certificate_id',$certificate_id[$i])
        ->update([
          'priority' => 1,
          'updated_at' => date('Y-m-d H:i:s')
        ]);

        $certificate_lama_update = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('certificate_id',$certificate_id[$i])
        ->where('remark',$request->get('remark'))
        ->update([
          'approver_status' => "Approved",
          'approved_at' => date('Y-m-d H:i:s'),
          'priority' => null,
          'updated_at' => date('Y-m-d H:i:s')
        ]);

        $appr_code = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('certificate_id',$certificate_id[$i])
        ->update([
          'certificate_approval_id' => $serial_number
        ]);

        $next_remark = 'President Director';

        $datas = DB::connection('ympimis_2')
        ->table('qa_certificates')
        ->where('certificate_id',$certificate_id[$i])
        ->first();

        $datas_nilai = DB::connection('ympimis_2')
        ->select("SELECT DISTINCT
          ( a.`certificate_id` ),
          `subject`,
          ( SELECT MIN( presentase_result ) FROM qa_certificates WHERE qa_certificates.certificate_id = a.certificate_id AND qa_certificates.`subject` = a.`subject` ) AS presentase_result,
          (
          SELECT DISTINCT
          ( result_grade ) 
          FROM
          qa_certificates 
          WHERE
          qa_certificates.certificate_id = a.certificate_id 
          AND qa_certificates.`subject` = a.`subject` 
          AND note = '-' 
          AND category LIKE '%Total%' 
          ) AS result_grade 
          FROM
          `qa_certificates` AS a 
          WHERE
          a.certificate_id = '".$certificate_id[$i]."' 
          AND a.note = '-' 
          GROUP BY
          a.`subject`,
          a.certificate_id");

        $data = array(
          'datas' => $datas,
          'datas_nilai' => $datas_nilai,
          'certificate_id' => $certificate_id[$i],
          'remark' => $request->get('remark'),
          'next_remark' => $next_remark
        );
        array_push($data_all, $data);
      }

      $code_generator->save();

      Mail::to($mail_to)
      ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
      ->send(new SendEmail($data_all, 'qa_certificate_collective'));
    }

    // if ($request->get('remark') == 'General Manager') {
    //   $certificate_id = $request->get('certificate_id');

    //   $data_all = [];

    //   $code_generator = CodeGenerator::where('note', '=', 'qa_certificate_approval')->first();
    //   $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

    //   $serial_number = $code_generator->prefix.$numbers;

    //   $code_generator->index = $code_generator->index+1;

    //   for ($i=0; $i < count($certificate_id); $i++) { 
    //     $next = DB::connection('ympimis_2')
    //     ->table('qa_certificate_approvals')
    //     ->where('remark','President Director')
    //     ->where('certificate_id',$certificate_id[$i])
    //     ->first();

    //     $mail_to = $next->approver_email;

    //     $next = DB::connection('ympimis_2')
    //     ->table('qa_certificate_approvals')
    //     ->where('remark','President Director')
    //     ->where('certificate_id',$certificate_id[$i])
    //     ->update([
    //       'priority' => 1,
    //       'updated_at' => date('Y-m-d H:i:s')
    //     ]);

    //     $certificate_lama_update = DB::connection('ympimis_2')
    //     ->table('qa_certificate_approvals')
    //     ->where('certificate_id',$certificate_id[$i])
    //     ->where('remark',$request->get('remark'))
    //     ->update([
    //       'approver_status' => "Approved",
    //       'approved_at' => date('Y-m-d H:i:s'),
    //       'priority' => null,
    //       'updated_at' => date('Y-m-d H:i:s')
    //     ]);

    //     $appr_code = DB::connection('ympimis_2')
    //     ->table('qa_certificate_approvals')
    //     ->where('certificate_id',$certificate_id[$i])
    //     ->update([
    //       'certificate_approval_id' => $serial_number
    //     ]);

    //     $next_remark = 'President Director';

    //     $datas = DB::connection('ympimis_2')
    //     ->table('qa_certificates')
    //     ->where('certificate_id',$certificate_id[$i])
    //     ->first();

    //     $datas_nilai = DB::connection('ympimis_2')
    //     ->select("SELECT DISTINCT
    //       ( a.`certificate_id` ),
    //       `subject`,
    //       ( SELECT MIN( presentase_result ) FROM qa_certificates WHERE qa_certificates.certificate_id = a.certificate_id AND qa_certificates.`subject` = a.`subject` ) AS presentase_result,
    //       (
    //         SELECT DISTINCT
    //         ( result_grade ) 
    //         FROM
    //         qa_certificates 
    //         WHERE
    //         qa_certificates.certificate_id = a.certificate_id 
    //         AND qa_certificates.`subject` = a.`subject` 
    //         AND note = '-' 
    //         AND category LIKE '%Total%' 
    //         ) AS result_grade 
    //       FROM
    //       `qa_certificates` AS a 
    //       WHERE
    //       a.certificate_id = '".$certificate_id[$i]."' 
    //       AND a.note = '-' 
    //       GROUP BY
    //       a.`subject`,
    //       a.certificate_id");

    //     $data = array(
    //       'datas' => $datas,
    //       'datas_nilai' => $datas_nilai,
    //       'certificate_id' => $certificate_id[$i],
    //       'remark' => $request->get('remark'),
    //       'next_remark' => $next_remark
    //     );
    //     array_push($data_all, $data);
    //   }

    //   $code_generator->save();

    //   Mail::to($mail_to)
    //   ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
    //   ->send(new SendEmail($data_all, 'qa_certificate_collective'));
    // }

    if ($request->get('remark') == 'President Director') {
      $certificate_id = $request->get('certificate_id');

      $code_generator = CodeGenerator::where('note', '=', 'qa_certificate_approval')->first();
      $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

      $serial_number = $code_generator->prefix.$numbers;

      $code_generator->index = $code_generator->index+1;

      for ($i=0; $i < count($certificate_id); $i++) { 
        $next = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('remark','Staff QA')
        ->where('certificate_id',$certificate_id[$i])
        ->first();

        $mail_to = $next->approver_email;

        $certificate_lama_update = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('certificate_id',$certificate_id[$i])
        ->where('remark',$request->get('remark'))
        ->update([
          'approver_status' => "Approved",
          'approved_at' => date('Y-m-d H:i:s'),
          'priority' => null,
          'updated_at' => date('Y-m-d H:i:s')
        ]);

        $appr_code = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('certificate_id',$certificate_id[$i])
        ->update([
          'certificate_approval_id' => $serial_number
        ]);

        $next_remark = 'Staff QA';

        $datas = DB::connection('ympimis_2')
        ->table('qa_certificates')
        ->where('certificate_id',$certificate_id[$i])
        ->first();

        $data = array(
          'datas' => $datas,
          'certificate_id' => $certificate_id[$i],
          'remark' => $request->get('remark'),
          'next_remark' => $next_remark,
          'complete' => 'complete',
        );

        Mail::to($mail_to)
        ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
        ->send(new SendEmail($data, 'qa_certificate'));
      }

      $code_generator->save();
    }

    // for ($i=0; $i < count($certificate_id); $i++) { 
    //   $datas = DB::connection('ympimis_2')
    //   ->table('qa_certificate_codes')
    //   ->join('qa_certificates','qa_certificates.certificate_id','qa_certificate_codes.certificate_id')
    //   ->where('qa_certificate_codes.certificate_id',$certificate_id[$i])
    //   ->get();

    //   $data_subject = DB::connection('ympimis_2')
    //   ->table('qa_certificates')
    //   ->select('qa_certificates.subject')
    //   ->distinct()
    //   ->where('qa_certificates.certificate_id',$certificate_id[$i])
    //   ->get();

    //   $data_approval = DB::connection('ympimis_2')
    //   ->table('qa_certificate_approvals')
    //   ->where('qa_certificate_approvals.certificate_id',$certificate_id[$i])
    //   ->orderBy('id','desc')
    //   ->get();

    //         // return view('qa.certificate.print_landscape')->with('datas',$datas)->with('data_subject',$data_subject)->with('data_approval',$data_approval);

    //   $pdf = \App::make('dompdf.wrapper');
    //   $pdf->getDomPDF()->set_option("enable_php", true);
    //   $pdf->setPaper($datas[0]->certificate_paper, 'potrait');

    //   $pdf->loadView('qa.certificate.print', array(
    //     'datas' => $datas,
    //     'data_approval' => $data_approval,
    //   ));

    //   $depan = "QA Certificate - ".$certificate_id[$i]." (".$datas[0]->certificate_code.").pdf";

    //   $pdf->save(public_path() . "/data_file/qa/certificate/".$depan);

    //   $pdf2 = \App::make('dompdf.wrapper');
    //   $pdf2->getDomPDF()->set_option("enable_php", true);
    //   $pdf2->getDomPDF()->set_option("enable_css_float", true);
    //   $pdf2->setPaper('A4', 'landscape');

    //   $pdf2->loadView('qa.certificate.print_landscape', array(
    //     'datas' => $datas,
    //     'data_subject' => $data_subject,
    //     'data_approval' => $data_approval,
    //   ));

    //   $belakang = "QA Certificate Belakang - ".$certificate_id[$i]." (".$datas[0]->certificate_code.").pdf";

    //   $pdf2->save(public_path() . "/data_file/qa/certificate/".$belakang);

    //   $pdfFile1Path = public_path() . "/data_file/qa/certificate/".$depan;
    //   $pdfFile2Path = public_path() . "/data_file/qa/certificate/".$belakang;

    //   $merger = new Merger;
    //   $merger->addFile($pdfFile1Path);
    //   $merger->addFile($pdfFile2Path);
    //   $createdPdf = $merger->merge();

    //   $pathForTheMergedPdf = public_path() . "/data_file/qa/certificate_fix/".$certificate_id[$i].".pdf";

    //   file_put_contents($pathForTheMergedPdf, $createdPdf);
    // }

    $response = array(
      'status' => true,
      'message' => 'Success Approve Certificate<br>認定承認済み'
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

public function approvalCertificate($remark)
{

  if (str_contains(Auth::user()->role_code,'MIS')) {
    $users = "";
  }else{
    $users = "AND approver_id = '".Auth::user()->username."' ";
  }

  $approval_now = DB::CONNECTION('ympimis_2')
  ->select("SELECT
        *,
    ( SELECT DISTINCT ( CONCAT( periode_from, '_', periode_to )) FROM qa_certificates WHERE qa_certificates.certificate_id = qa_certificate_approvals.certificate_id ) AS periode 
    FROM
    `qa_certificate_approvals`
    JOIN qa_certificate_codes ON qa_certificate_codes.certificate_id = qa_certificate_approvals.certificate_id 
    WHERE
    qa_certificate_approvals.remark = '".$remark."' 
    ".$users." 
    and qa_certificate_approvals.priority = 1
    AND qa_certificate_approvals.approver_status IS NULL
    AND qa_certificate_codes.`code` = 'I'");

  return view('qa.certificate.approval')
  ->with('approval_now',$approval_now)
  ->with('remark',$remark)
  ->with('title', 'QA Kensa Certificate Approval')
  ->with('title_jp', '品質保証検査認定承認')
  ->with('page', 'QA Kensa Certificate Approval')
  ->with('jpn', '品質保証検査認定承認');
}

public function approvalAllCertificate($remark,$certificate_id)
{
  $certid = explode(',', $certificate_id);
  $certids = [];
  $cer_no = 0;;
  for ($i=0; $i < count($certid); $i++) { 
    $approval_now = DB::CONNECTION('ympimis_2')
    ->table("qa_certificate_approvals")
    ->where('certificate_id',$certid[$i])
    ->where('remark',$remark)
    ->where('approver_status',null)
    ->first();
    if (count($approval_now) > 0) {
      $cer_no++;
    }
    array_push($certids, $certid[$i]);
  }
  return view('qa.certificate.approval_all')
  ->with('certificate_id',$certids)
  ->with('remark',$remark)
  ->with('cer_no',$cer_no)
  ->with('head','QA Kensa Certificate Approval')
  ->with('sub_head','(品質保証検査認定承認)')
  ->with('message','The Certificate Has Been Approved')
  ->with('sub_message','(すでに承認済み)')
  ->with('title', 'QA Kensa Certificate Approval')
  ->with('title_jp', '品質保証検査認定承認')
  ->with('page', 'QA Kensa Certificate Approval')
  ->with('jpn', '品質保証検査認定承認');
}

public function rejectCertificate($remark,$certificate_id)
{
  $certid = explode(',', $certificate_id);
  $certids = [];
  for ($i=0; $i < count($certid); $i++) { 
    $approval_now = DB::CONNECTION('ympimis_2')
    ->table("qa_certificate_approvals")
    ->where('certificate_id',$certid[$i])
    ->update([
      'approver_status' => null,
      'approved_at' => null,
      'priority' => null,
    ]);
    $approval_leader = DB::CONNECTION('ympimis_2')
    ->table("qa_certificate_approvals")
    ->where('certificate_id',$certid[$i])
    ->where('remark','Leader QA')
    ->update([
      'priority' => 1,
    ]);

    $approval_reject = DB::CONNECTION('ympimis_2')
    ->table("qa_certificate_approvals")
    ->where('certificate_id',$certid[$i])
    ->where('remark',$remark)
    ->first();

    $approval_staff = DB::CONNECTION('ympimis_2')
    ->table("qa_certificate_approvals")
    ->where('certificate_id',$certid[$i])
    ->where('remark','Staff QA')
    ->first();

    $datas = DB::connection('ympimis_2')
    ->table('qa_certificates')
    ->where('certificate_id',$certid[$i])
    ->first();

    if ($remark != 'Staff QA') {
      $data = array(
        'datas' => $datas,
        'certificate_id' => $certid[$i],
        'remark' => $remark,
        'next_remark' => 'Staff QA',
        'reject' => 'reject',
        'reject_by' => $approval_reject
      );

      Mail::to($approval_staff->approver_email)
      ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
      ->send(new SendEmail($data, 'qa_certificate'));
    }
  }
  return view('qa.certificate.reject')
  ->with('head','QA Kensa Certificate Rejection')
  ->with('sub_head','(品質保証検査認証の拒否)')
  ->with('message','The Certificate Has Been Rejected')
  ->with('sub_message','(拒否された)')
  ->with('title', 'QA Kensa Certificate Rejection')
  ->with('title_jp', '品質保証検査認証の拒否')
  ->with('page', 'QA Kensa Certificate Rejection')
  ->with('jpn', '品質保証検査認証の拒否');
}

public function deactivateCertificate(Request $request)
{
  try {

    $code = DB::connection('ympimis_2')
    ->table('qa_certificate_codes')
    ->where('certificate_id',$request->get('certificate_id'))
    ->update([
      'certificate_id' => null,
      'employee_id' => null,
      'name' => null,
      'status' => 0,
    ]);

    $certificate = DB::connection('ympimis_2')
    ->table('qa_certificates')
    ->where('certificate_id',$request->get('certificate_id'))
    ->update([
      'status' => 0,
    ]);

    $response = array(
      'status' => true,
      'message' => 'Successfully Deactivate Certificate'
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

public function editCertificate(Request $request)
{
  try {
    $certificate = DB::connection('ympimis_2')
    ->table('qa_certificates')
    ->where('certificate_id',$request->get('certificate_id'))
    ->get();

    $response = array(
      'status' => true,
      'certificate' => $certificate
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

public function updateCertificate(Request $request)
{
  try {
    $id = $request->get('id');
    $question = $request->get('question');
    $weight = $request->get('weight');
    $question_result = $request->get('question_result');
    $presentase_result = $request->get('presentase_result');
    $presentase_a = $request->get('presentase_a');
    $result_grade = $request->get('result_grade');
    $note = $request->get('note');
    for ($i=0; $i < count($id); $i++) { 
      $update = DB::connection('ympimis_2')
      ->table('qa_certificates')
      ->where('id',$id[$i])
      ->update([
        'question' => $question[$i],
        'weight' => $weight[$i],
        'question_result' => $question_result[$i],
        'presentase_result' => $presentase_result[$i],
        'presentase_a' => $presentase_a[$i],
        'result_grade' => $result_grade[$i],
        'note' => $note[$i],
        'updated_at' => date('Y-m-d H:i:s')
      ]);
    }

    $datas = DB::connection('ympimis_2')
    ->table('qa_certificate_codes')
    ->join('qa_certificates','qa_certificates.certificate_id','qa_certificate_codes.certificate_id')
    ->where('qa_certificate_codes.certificate_id',$request->get('certificate_id'))
    ->get();

    $data_subject = DB::connection('ympimis_2')
    ->table('qa_certificates')
    ->select('qa_certificates.subject')
    ->distinct()
    ->where('qa_certificates.certificate_id',$request->get('certificate_id'))
    ->get();

    $data_approval = DB::connection('ympimis_2')
    ->table('qa_certificate_approvals')
    ->where('qa_certificate_approvals.certificate_id',$request->get('certificate_id'))
    ->orderBy('id','desc')
    ->get();

        // return view('qa.certificate.print_landscape')->with('datas',$datas)->with('data_subject',$data_subject)->with('data_approval',$data_approval);

    $pdf = \App::make('dompdf.wrapper');
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper($datas[0]->certificate_paper, 'potrait');

    $pdf->loadView('qa.certificate.print', array(
      'datas' => $datas,
      'data_approval' => $data_approval,
    ));

    $depan = "QA Certificate - ".$request->get('certificate_id')." (".$datas[0]->certificate_code.").pdf";

    $pdf->save(public_path() . "/data_file/qa/certificate/".$depan);

    $pdf2 = \App::make('dompdf.wrapper');
    $pdf2->getDomPDF()->set_option("enable_php", true);
    $pdf2->getDomPDF()->set_option("enable_css_float", true);
    $pdf2->setPaper('A4', 'landscape');

    $pdf2->loadView('qa.certificate.print_landscape', array(
      'datas' => $datas,
      'data_subject' => $data_subject,
      'data_approval' => $data_approval,
    ));

    $belakang = "QA Certificate Belakang - ".$request->get('certificate_id')." (".$datas[0]->certificate_code.").pdf";

    $pdf2->save(public_path() . "/data_file/qa/certificate/".$belakang);

    $pdfFile1Path = public_path() . "/data_file/qa/certificate/".$depan;
    $pdfFile2Path = public_path() . "/data_file/qa/certificate/".$belakang;

    $merger = new Merger;
    $merger->addFile($pdfFile1Path);
    $merger->addFile($pdfFile2Path);
    $createdPdf = $merger->merge();

    $pathForTheMergedPdf = public_path() . "/data_file/qa/certificate_fix/".$request->get('certificate_id').".pdf";    


    file_put_contents($pathForTheMergedPdf, $createdPdf);
    $response = array(
      'status' => true,
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

public function indexNgRateVendor($vendor)
{

  if ($vendor == 'true') {
    $title = 'Report Vendor Final Inspection - PT. TRUE INDONESIA';
    $vendor_name = 'PT. TRUE INDONESIA';
    $vendor_shortname = 'TRUE';
    $title_jp = '業者最終検査の報告';
  }else if($vendor == 'kbi'){
    $title = 'Report Vendor Final Inspection - PT. KBI';
    $vendor_name = 'PT. KBI';
    $vendor_shortname = 'KYORAKU';
    $title_jp = '業者最終検査の報告';
  }else if($vendor == 'arisa'){
    $title = 'Report Vendor Final Inspection - PT. ARISAMANDIRI PRATAMA';
    $vendor_name = 'PT. ARISAMANDIRI PRATAMA';
    $vendor_shortname = 'ARISA';
    $title_jp = '業者最終検査の報告';
  }

  $material = DB::SELECT("SELECT DISTINCT
    ( material_number ),
    material_description 
    FROM
    qa_materials 
    where vendor_shortname = '".$vendor_shortname."'
    ORDER BY
    material_description ASC");

  return view('qa.ng_rate_vendor')
  ->with('title', $title)
  ->with('title_jp', $title_jp)
  ->with('material', $material)
  ->with('vendor', $vendor)
  ->with('vendor_name', $vendor_name)
  ->with('vendor_shortname', $vendor_shortname);
}

public function fetchNgRateVendor(Request $request)
{
  try {
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
      $last = "LAST_DAY(NOW())";
      $firstDateTitle = date('01 M Y');
      $lastDateTitle = date('d M Y');
    }else{
      $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
      $last = "'".$date_to."'";
      $firstDateTitle = date('01 M Y');
      $lastDateTitle = date('d M Y',strtotime($date_to));
    }
  }else{
   if ($date_to == "") {
    $first = "'".$date_from."'";
    $last = "LAST_DAY(NOW())";
    $firstDateTitle = date('d M Y',strtotime($date_from));
    $lastDateTitle = date('d M Y');
  }else{
    $first = "'".$date_from."'";
    $last = "'".$date_to."'";
    $firstDateTitle = date('d M Y',strtotime($date_from));
    $lastDateTitle = date('d M Y',strtotime($date_to));
  }
}

$material = '';
if($request->get('material') != null){
  $materials =  explode(",", $request->get('material'));
  for ($i=0; $i < count($materials); $i++) {
    $material = $material."'".$materials[$i]."'";
    if($i != (count($materials)-1)){
      $material = $material.',';
    }
  }
  $materialin = " and `material_number` in (".$material.") ";
}
else{
  $materialin = "";
}

$ng_rate = DB::connection('ympimis_online')->SELECT("SELECT
  c.check_date,
  GROUP_CONCAT(
  DISTINCT ( c.serial_number )) AS serial_number,
  SUM( c.qty_check ) AS qty_check,
  SUM( c.qty_ng ) AS qty_ng,
  ROUND(( SUM( c.qty_ng ) / SUM( c.qty_check ) )* 100, 2 ) AS ng_ratio 
  FROM
  (
  SELECT DISTINCT
  ( serial_number ),
  DATE( created_at ) AS check_date,
  ( SELECT a.qty_check FROM `qa_outgoing_vendors` AS a WHERE a.serial_number = qa_outgoing_vendors.serial_number LIMIT 1 ) AS qty_check,
  ( SELECT sum( b.ng_qty ) FROM `qa_outgoing_vendors` AS b WHERE b.serial_number = qa_outgoing_vendors.serial_number ) AS qty_ng 
  FROM
  `qa_outgoing_vendors` 
  WHERE
  vendor_shortname = '".$request->get('vendor')."' 
  ".$materialin."
  AND DATE( created_at ) >= ".$first."
  AND DATE( created_at ) <= ".$last." 
  ) c 
  GROUP BY
  c.check_date");

$ng_rate_ympi_all = [];

for ($i=0; $i < count($ng_rate); $i++) { 
  $serial_number = explode(',',$ng_rate[$i]->serial_number);
  for ($j=0; $j < count($serial_number); $j++) { 
    $ng_rate_ympi = DB::SELECT("SELECT DISTINCT
      ( serial_number ),
      SUM( qty_check ) AS qty_check,
      SUM( total_ng ) AS qty_ng,
      SUM( total_ng ) / SUM( qty_check ) AS ng_ratio 
      FROM
      qa_incoming_logs 
      WHERE
      serial_number = '".$serial_number[$j]."' 
      GROUP BY
      serial_number
      ");

    if (count($ng_rate_ympi) > 0) {
      array_push($ng_rate_ympi_all, $ng_rate_ympi);
    }
  }
}


        // $ng_rate_ympi = DB::connection('ympimis_online')->SELECT("SELECT
        //   c.check_date,
        //   GROUP_CONCAT(
        //   DISTINCT ( c.serial_number )) AS serial_number,
        //   SUM( c.qty_check ) AS qty_check,
        //   SUM( c.qty_ng ) AS qty_ng,
        //   ROUND(( SUM( c.qty_ng ) / SUM( c.qty_check ) )* 100, 2 ) AS ng_ratio 
        // FROM
        //   (
        //   SELECT DISTINCT
        //     ( serial_number ),
        //     DATE( created_at ) AS check_date,
        //     ( SELECT a.qty_check FROM `qa_outgoing_vendors` AS a WHERE a.serial_number = qa_outgoing_vendors.serial_number LIMIT 1 ) AS qty_check,
        //     ( SELECT sum( b.ng_qty ) FROM `qa_outgoing_vendors` AS b WHERE b.serial_number = qa_outgoing_vendors.serial_number ) AS qty_ng 
        //   FROM
        //     `qa_outgoing_vendors` 
        //   WHERE
        //     vendor_shortname = '".$request->get('vendor')."' 
        //     ".$materialin."
        //     AND DATE( created_at ) >= ".$first."
        //     AND DATE( created_at ) <= ".$last." 
        //   ) c 
        // GROUP BY
        //   c.check_date");



$response = array(
  'status' => true,
  'ng_rate' => $ng_rate,
  'ng_rate_ympi_all' => $ng_rate_ympi_all,
  'firstDateTitle' => $firstDateTitle,
  'lastDateTitle' => $lastDateTitle,
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

public function indexCertificateSchedule()
{
  $fy_all = WeeklyCalendar::select('fiscal_year')->distinct()->get();
  return view('qa.certificate.schedule_monitoring')
  ->with('title', 'QA Kensa Certificate Schedule Monitoring')
  ->with('fy_all',$fy_all)
  ->with('title_jp', 'QA検査認定証スケジュール監視')
  ->with('page', 'QA Kensa Certificate')
  ->with('jpn', 'QA検査認定証スケジュール監視');
}

public function fetchCertificateSchedule(Request $request)
{
  try {
    if ($request->get('fiscal_year') == '') {
      $fys = WeeklyCalendar::where('week_date',date('Y-m-d'))->first();
      $fy_now = $fys->fiscal_year;
    }else{
      $fy_now = $request->get('fiscal_year');
    }

    $periode = DB::connection('ympimis_2')
    ->table('qa_certificate_periodes')->get();

    $fy = WeeklyCalendar::select(DB::RAW("DATE_FORMAT( week_date, '%Y-%m' ) as months"),DB::RAW("DATE_FORMAT( week_date, '%b %Y' ) as month_name"))->distinct()->where('fiscal_year',$fy_now)->orderby('week_date')->get();

    $periode_all = [];
    $renewals = [];
    $news = [];

        // for ($i=0; $i < count($periode); $i++) { 
    // for ($j=0; $j < count($fy); $j++) { 
            // if ($periode[$i]->periode == explode('-', $fy[$j]->months)[1]) {
      // $renewal = DB::connection('ympimis_2')
      // ->select("SELECT
      //   periode,
      //   periode_name,
      //   leader_id,
      //   leader_name,
      //   CONCAT( `code`, '-', code_number ) AS code_alls,
      //   a.* 
      //   FROM
      //   qa_certificate_periodes
      //   JOIN (
      //     SELECT DISTINCT
      //     ( certificate_code ),
      //     CONCAT(
      //       SPLIT_STRING ( certificate_code, '-', 3 ),
      //       '-',
      //       SUBSTRING( SPLIT_STRING ( certificate_code, '-', 4 ), 1, LENGTH( SPLIT_STRING ( certificate_code, '-', 4 ))- 3 )) AS code_all,
      //     DATE_FORMAT( qa_certificates.periode_to, '%Y-%m' ) AS months,
      //     periode_to,
      //     employee_id,
      //     `name`,
      //     certificate_name 
      //     FROM
      //     `qa_certificates` 
      //     ) a ON a.code_all = CONCAT( `code`, '-', code_number ) 
      //   WHERE
      //   periode = '".explode('-', $fy[$j]->months)[1]."' 
      //   AND a.months = '".$fy[$j]->months."'");

      // $new = DB::connection('ympimis_2')
      // ->select("SELECT
      //   periode,
      //   periode_name,
      //   leader_id,
      //   leader_name,
      //   CONCAT( `code`, '-', code_number ) AS code_alls,
      //   a.* 
      //   FROM
      //   qa_certificate_periodes
      //   JOIN (
      //     SELECT DISTINCT
      //     ( certificate_code ),
      //     CONCAT(
      //       SPLIT_STRING ( certificate_code, '-', 3 ),
      //       '-',
      //       SUBSTRING( SPLIT_STRING ( certificate_code, '-', 4 ), 1, LENGTH( SPLIT_STRING ( certificate_code, '-', 4 ))- 3 )) AS code_all,
      //     DATE_FORMAT( qa_certificates.periode_from, '%Y-%m' ) AS months,
      //     periode_from,
      //     employee_id,
      //     `name`,
      //     certificate_name 
      //     FROM
      //     `qa_certificates` 
      //     where `status` != 0
      //     ) a ON a.code_all = CONCAT( `code`, '-', code_number ) 
      //   WHERE
      //   periode = '".explode('-', $fy[$j]->months)[1]."'
      //   and a.months = '".$fy[$j]->months."'");

      // $schedule_belum = DB::connection('ympimis_2')->select("SELECT
      //   qa_certificate_schedules.*,
      //   'belum' AS `status`,
      //   description,
      //   DATE_FORMAT( schedule_date, '%Y-%m' ) as months
      //   FROM
      //   `qa_certificate_schedules`
      //   JOIN qa_certificate_periodes ON qa_certificate_periodes.code_number = SUBSTRING( SPLIT_STRING ( certificate_code, '-', 4 ) FROM 1 FOR LENGTH( SPLIT_STRING ( certificate_code, '-', 4 ))- 3 ) 
      //   AND qa_certificate_periodes.CODE = SPLIT_STRING ( certificate_code, '-', 3 ) 
      //   WHERE
      //   DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$fy[$j]->months."' ");

    //   $schedule_sudah = DB::connection('ympimis_2')->select("SELECT
    //     qa_certificate_schedules.*,
    //     'belum' AS `status`,
    //     description,
    //     DATE_FORMAT( schedule_date, '%Y-%m' ) as months
    //     FROM
    //     `qa_certificate_schedules`
    //     JOIN qa_certificate_periodes ON qa_certificate_periodes.code_number = SUBSTRING( SPLIT_STRING ( certificate_code, '-', 4 ) FROM 1 FOR LENGTH( SPLIT_STRING ( certificate_code, '-', 4 ))- 3 ) 
    //     AND qa_certificate_periodes.CODE = SPLIT_STRING ( certificate_code, '-', 3 ) 
    //     WHERE
    //     DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$fy[$j]->months."' 
    //     AND `status` = 1");

    //   array_push($renewals, $schedule_belum);
    //   array_push($news, $schedule_sudah);
    // }
          // }
        // }

    $schedules = DB::connection('ympimis_2')->SELECT("SELECT
      qa_certificates.certificate_id,
      DATE_FORMAT( periode_from, '%Y-%m' ) AS periode_from,
      DATE_FORMAT( periode_to, '%Y-%m' ) AS periode_to,
      qa_certificates.certificate_name,
      GROUP_CONCAT( DISTINCT ( `certificate_code` ) ) AS certificate_code,
      GROUP_CONCAT( DISTINCT ( qa_certificates.`employee_id` ) ) AS employee_id,
      GROUP_CONCAT( DISTINCT ( qa_certificates.`name` ) ) AS `name`,
      GROUP_CONCAT( DISTINCT ( `auditor_id` ) ) AS `auditor_id`,
      GROUP_CONCAT( DISTINCT ( `auditor_name` ) ) AS `auditor_name`,
      GROUP_CONCAT( DISTINCT ( `staff_id` ) ) AS `staff_id`,
      GROUP_CONCAT( DISTINCT ( `staff_name` ) ) AS `staff_name` 
      FROM
      qa_certificates
      JOIN qa_certificate_codes ON qa_certificate_codes.certificate_id = qa_certificates.certificate_id 
      WHERE
      qa_certificates.`status` = 1 
      OR qa_certificates.`status` = 2 
      GROUP BY
      qa_certificates.certificate_id,
      periode_from,
      periode_to,
      qa_certificates.certificate_name UNION ALL
      SELECT
      qa_certificate_inprocesses.certificate_id,
      DATE_FORMAT( periode_from, '%Y-%m' ) AS periode_from,
      DATE_FORMAT( periode_to, '%Y-%m' ) AS periode_to,
      qa_certificate_codes.description AS certificate_name,
      GROUP_CONCAT( DISTINCT ( `certificate_code` ) ) AS certificate_code,
      GROUP_CONCAT( DISTINCT ( qa_certificate_inprocesses.`employee_id` ) ) AS employee_id,
      GROUP_CONCAT( DISTINCT ( qa_certificate_inprocesses.`name` ) ) AS `name`,
      GROUP_CONCAT( DISTINCT ( `auditor_id` ) ) AS `auditor_id`,
      GROUP_CONCAT( DISTINCT ( `auditor_name` ) ) AS `auditor_name`,
      GROUP_CONCAT( DISTINCT ( `staff_id` ) ) AS `staff_id`,
      GROUP_CONCAT( DISTINCT ( `staff_name` ) ) AS `staff_name` 
      FROM
      qa_certificate_inprocesses
      JOIN qa_certificate_codes ON qa_certificate_codes.certificate_id = qa_certificate_inprocesses.certificate_id 
      WHERE
      qa_certificate_inprocesses.`status` = 1 
      OR qa_certificate_inprocesses.`status` = 2 
      GROUP BY
      qa_certificate_inprocesses.certificate_id,
      periode_from,
      periode_to,
      qa_certificate_codes.description
      ORDER BY
      certificate_name");

    $response = array(
      'status' => true,
      // 'renewals' => $renewals,
      // 'news' => $news,
      'schedules' => $schedules,
      'fy' => $fy,
      'periode' => $periode,
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

    //SUBMISSION
public function indexSubmissionCertificate()
{


  $certificate_codes = DB::connection('ympimis_2')->table('qa_certificate_codes')->select('qa_certificate_codes.certificate_name')->distinct()->get();

  $certificate_name = [];
  for ($i=0; $i < count($certificate_codes); $i++) { 
    $certificate_names = explode(',', $certificate_codes[$i]->certificate_name);
    for ($j=0; $j < count($certificate_names); $j++) { 
      array_push($certificate_name, $certificate_names[$j]);
    }
  }

  $employees = EmployeeSync::where('end_date',null)->get();
  return view('qa.certificate.index_submission')
  ->with('title', 'QA Kensa Certificate Submission')
  ->with('title_jp', 'QA検査認定証申請')
  ->with('certificate_name', $certificate_name)
  // ->with('certificate_code', $certificate_code)
  ->with('certificate_name2', $certificate_name)
  ->with('employees', $employees)
  ->with('page', 'QA Kensa Certificate')
  ->with('jpn', 'QA検査認定証申請');
}

public function fetchSubmissionCertificate(Request $request)
{
  try {

    $certificate_code = DB::connection('ympimis_2')->select("SELECT DISTINCT
      ( qa_certificates.certificate_id ),
      qa_certificates.certificate_name,
      qa_certificates.employee_id,
      qa_certificates.`name`,
      qa_certificates.certificate_code 
      FROM
      qa_certificates 
      WHERE
      `status` != 0 UNION ALL
      SELECT DISTINCT
      ( qa_certificate_inprocesses.certificate_id ),
      qa_certificate_inprocesses.certificate_name,
      qa_certificate_inprocesses.employee_id,
      qa_certificate_inprocesses.`name`,
      qa_certificate_inprocesses.certificate_code 
      FROM
      qa_certificate_inprocesses 
      WHERE
      `status` != 0");

    $new_submission = DB::connection('ympimis_2')
    ->table('qa_certificate_submissions')
    ->where('request_type','new')
    ->orderBy('request_status','desc');

    $deactivation = DB::connection('ympimis_2')
    ->table('qa_certificate_submissions')
    ->where('request_type','deactivate')
    ->orderBy('request_status','desc');

    if (str_contains(Auth::user()->role_code,'MIS') || str_contains(Auth::user()->role_code,'QA')) {
      $new_submission = $new_submission->get();
      $deactivation = $deactivation->get();
      $wherenotif = "AND SPLIT_STRING ( staff_qa, '_', 1 ) = '".strtoupper(Auth::user()->username)."'";
      $notif = DB::connection('ympimis_2')
      ->select("SELECT
            *
        FROM
        qa_certificate_submissions 
        WHERE
        `request_status` = 'Requested' 
        and SPLIT_STRING ( staff_qa, '_', 3 ) = ''
        ".$wherenotif."");
    }else{
      $new_submission = $new_submission->
      where(function($query) {
        $query->where('created_by','=',Auth::user()->id)
        ->orWhere(DB::RAW("SPLIT_STRING (leader_qa,'_',1)"),'=',Auth::user()->username);
      })->get();
      $deactivation = $deactivation->where('created_by',Auth::user()->id)->where(DB::RAW("SPLIT_STRING (leader_qa,'_',1)"),Auth::user()->username)->get();
      $wherenotif = "AND SPLIT_STRING ( leader_qa, '_', 1 ) = '".strtoupper(Auth::user()->username)."'";
      $notif = DB::connection('ympimis_2')
      ->select("SELECT
            *
        FROM
        qa_certificate_submissions 
        WHERE
        `request_status` = 'Receive Leader QA' 
        and SPLIT_STRING ( leader_qa, '_', 3 ) = ''
        ".$wherenotif."");
    }

    $emp = EmployeeSync::where('end_date',null)->get();
    $department = Department::get();

    $response = array(
      'status' => true,
      'new_submission' => $new_submission,
      'deactivation' => $deactivation,
      'emp' => $emp,
      'department' => $department,
      'certificate_code' => $certificate_code,
      'notif' => $notif,
      'employee_id' => Auth::user()->username,
      'role' => Auth::user()->role_code
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

public function inputSubmissionCertificate(Request $request)
{
  try {

    $reason = $request->get('reason');
    $request_date = $request->get('request_date');
    $certificate_name = $request->get('certificate_name');
    $employees = $request->get('employees');
    $request_type = $request->get('request_type');

    $emp = EmployeeSync::select('employee_syncs.*','departments.department_shortname')->where('end_date',null)->leftjoin('departments','departments.department_name','employee_syncs.department')->get();

    if ($request_type == 'new') {
      $employee_id_foreman = '';
      $name_foreman = '';
      $news = [];
      for ($i=0; $i < count($employees); $i++) { 
        $code_generator = CodeGenerator::where('note','=','qa_certificate_submission')->first();
        $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
        $request_id = $code_generator->prefix . $number;

        $emps = EmployeeSync::where('employee_id',$employees[$i])->first();

        $applicant = EmployeeSync::where('employee_id',Auth::user()->username)->first();

        $employee_id_foreman = '';
        $name_foreman = '';

        if ($emps->department == 'Standardization Department') {
          $employee_id_foreman = 'PI9707001';
          $name_foreman = 'Agustina Hayati';
        }else{
          $foreman_produksi = Approver::where('department',$emps->department)->where('remark','Foreman')->where('section',$emps->section)->first();
          if (count($foreman_produksi) > 0) {
            $employee_id_foreman = $foreman_produksi->approver_id;
            $name_foreman = $foreman_produksi->approver_name;
          }else{
            $chief_produksi = Approver::where('department',$emps->department)->where('remark','Chief')->first();
            $employee_id_foreman = $chief_produksi->approver_id;
            $name_foreman = $chief_produksi->approver_name;
          }
        }

        $staff = DB::connection('ympimis_2')->table('qa_certificate_periodes')->where('certificate_name','like','%'.$certificate_name.'%')->first();

        $foreman = Approver::where('department','Standardization Department')->where('remark','Foreman')->first();

        $leader = DB::connection('ympimis_2')->table('qa_certificate_periodes')->where('certificate_name','like','%'.$certificate_name.'%')->first();

        $insert_new = DB::connection('ympimis_2')
        ->table('qa_certificate_submissions')
        ->insert([
          'request_id' => $request_id,
          'request_type' => $request_type,
          'request_status' => 'Requested',
          'request_date' => $request_date,
          'employee_id' => $employees[$i],
          'name' => $emps->name,
          'certificate_name' => $certificate_name,
          'reason' => $reason,
          'applicant' => $applicant->employee_id.'_'.$applicant->name.'_'.date('Y-m-d H:i:s'),
          'foreman_prod' => $employee_id_foreman.'_'.$name_foreman.'_',
          'staff_qa' => $staff->staff_id.'_'.$staff->staff_name.'_',
          'foreman_qa' => $foreman->approver_id.'_'.$foreman->approver_name.'_',
          'leader_qa' => $leader->leader_id.'_'.$leader->leader_name.'_',
          'created_by' => Auth::user()->id,
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $insert_news = DB::connection('ympimis_2')
        ->table('qa_certificate_submissions')
        ->where('request_id',$request_id)
        ->first();

        array_push($news, $insert_news);

        $code_generator->index = $code_generator->index+1;
        $code_generator->save();
      }

      $data = array(
        'employees' => $employees,
        'news' => $news,
        'certificate_name' => $certificate_name,
        'request_type' => $request_type,
        'request_date' => $request_date,
        'reason' => $reason,
        'emp' => $emp,
        'next_remark' => 'staff_qa',
      );

      $mail_to = Approver::where('approver_id',$employee_id_foreman)->first();

      Mail::to($mail_to->approver_email)
      ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com','rio.irvansyah@music.yamaha.com'])
      ->send(new SendEmail($data, 'qa_certificate_submission_new'));
    }

    if ($request_type == 'deactivate') {
      $certificate_code = $request->get('certificate_code');
      $certificate_id = $request->get('certificate_id');

      $employee_id_foreman = '';
      $name_foreman = '';
      $nons = [];
      for ($i=0; $i < count($certificate_id); $i++) { 
        $code_generator = CodeGenerator::where('note','=','qa_certificate_submission')->first();
        $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
        $request_id = $code_generator->prefix . $number;

        $certificate = DB::connection('ympimis_2')
        ->table('qa_certificate_codes')
        ->where('certificate_id',$certificate_id[$i])->first();

        $emps = EmployeeSync::where('employee_id',$certificate->employee_id)->first();

        $applicant = EmployeeSync::where('employee_id',Auth::user()->username)->first();

        $foreman_produksi = Approver::where('department',$emps->department)->where('remark','Foreman')->where('section',$emps->section)->first();
        $employee_id_foreman = '';
        $name_foreman = '';

        if (count($foreman_produksi) > 0) {
          $employee_id_foreman = $foreman_produksi->approver_id;
          $name_foreman = $foreman_produksi->approver_name;
        }else{
          $chief_produksi = Approver::where('department',$emps->department)->where('remark','Chief')->first();
          $employee_id_foreman = $chief_produksi->approver_id;
          $name_foreman = $chief_produksi->approver_name;
        }

        $staff = DB::connection('ympimis_2')->table('qa_certificate_periodes')->where('certificate_name','like','%'.$certificate_name.'%')->first();

        $foreman = Approver::where('department','Standardization Department')->where('remark','Foreman')->first();

        $leader = DB::connection('ympimis_2')->table('qa_certificate_periodes')->where('certificate_name','like','%'.$certificate_name.'%')->first();

        $insert_non = DB::connection('ympimis_2')
        ->table('qa_certificate_submissions')
        ->insert([
          'request_id' => $request_id,
          'request_type' => $request_type,
          'request_status' => 'Requested',
          'request_date' => $request_date,
          'employee_id' => $emps->employee_id,
          'name' => $emps->name,
          'certificate_name' => $certificate_name,
          'certificate_id' => $certificate_id[$i],
          'certificate_code' => $certificate_code[$i],
          'reason' => $reason,
          'applicant' => $applicant->employee_id.'_'.$applicant->name.'_'.date('Y-m-d H:i:s'),
          'foreman_prod' => $employee_id_foreman.'_'.$name_foreman.'_',
          'staff_qa' => $staff->staff_id.'_'.$staff->staff_name.'_',
          'foreman_qa' => $foreman->approver_id.'_'.$foreman->approver_name.'_',
          'leader_qa' => $leader->leader_id.'_'.$leader->leader_name.'_',
          'created_by' => Auth::user()->id,
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $insert_nons = DB::connection('ympimis_2')
        ->table('qa_certificate_submissions')
        ->where('request_id',$request_id)
        ->first();

        array_push($nons, $insert_nons);

        $code_generator->index = $code_generator->index+1;
        $code_generator->save();
      }

      $data = array(
        'employees' => $employees,
        'nons' => $nons,
        'certificate_name' => $certificate_name,
        'certificate_id' => $certificate_id,
        'certificate_code' => $certificate_code,
        'request_type' => $request_type,
        'request_date' => $request_date,
        'reason' => $reason,
        'emp' => $emp,
        'next_remark' => 'staff_qa',
      );

      $mail_to = User::where('username',explode('_', $nons[0]->staff_qa)[0])->first();
      $foreman_prod = Approver::where('approver_id',explode('_', $nons[0]->foreman_prod)[0])->first();
      $foreman_qa = Approver::where('approver_id',explode('_', $nons[0]->foreman_qa)[0])->first();

      $cc = [];
      array_push($cc, $foreman_prod->approver_email);
      array_push($cc, $foreman_qa->approver_email);
      array_push($cc, 'ratri.sulistyorini@music.yamaha.com');

      Mail::to($mail_to->email)
      ->cc($cc)
      ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com','rio.irvansyah@music.yamaha.com'])
      ->send(new SendEmail($data, 'qa_certificate_submission_non'));
    }

    $response = array(
      'status' => true,
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

public function approvalSubmissionCertificateNew($remark,$request_id)
{
  $reqid = '';
  if($request_id != null){
    $reqids =  explode(",", $request_id);
    for ($i=0; $i < count($reqids); $i++) {
      $reqid = $reqid."'".$reqids[$i]."'";
      if($i != (count($reqids)-1)){
        $reqid = $reqid.',';
      }
    }
    $reqidin = " where request_id in (".$reqid.") ";
  }
  else{
    $reqidin = "";
  }
  $cek = DB::connection('ympimis_2')
  ->select("select * from qa_certificate_submissions ".$reqidin);

  if (count($cek) == 0) {
    return view('qa.certificate.approval_submission')
    ->with('title', 'New Kensa Certificate Submission Approval')
    ->with('title_jp', '検査認定証新規申請承認')
    ->with('head','Persetujuan Pengajuan Sertifikat Baru')
    ->with('error','Data Tidak Tersedia / Dibatalkan')
        // ->with('news')
    ->with('page', 'QA Kensa Certificate')
    ->with('jpn', '検査認定証新規申請承認');
  }

  if ($remark == 'staff_qa') {
    $request_ids = explode(',', $request_id);
    $requestss = DB::connection('ympimis_2')
    ->table('qa_certificate_submissions')
    ->where('request_id',$request_ids[0])
    ->first();
    $news = [];

    $approve_status = 0;
    for ($i=0; $i < count($request_ids); $i++) { 
      $requests = DB::connection('ympimis_2')
      ->table('qa_certificate_submissions')
      ->where('request_id',$request_ids[$i])
      ->first();

      if (explode('_', $requests->foreman_prod)[2] == '') {
        $approve = DB::connection('ympimis_2')
        ->table('qa_certificate_submissions')
        ->where('request_id',$request_ids[$i])
        ->update([
          'foreman_prod' => $requests->foreman_prod.date('Y-m-d H:i:s'),
          'request_status' => 'Partially Approved'
        ]);

        $requests = DB::connection('ympimis_2')
        ->table('qa_certificate_submissions')
        ->where('request_id',$request_ids[$i])
        ->first();

        array_push($news, $requests);
      }else{
        $approve_status++;
      }
    }
    if ($approve_status == 0) {
      $next_remark = 'foreman_qa';

      $emp = EmployeeSync::select('employee_syncs.*','departments.department_shortname')->where('end_date',null)->leftjoin('departments','departments.department_name','employee_syncs.department')->get();

      $data = array(
        'news' => $news,
        'emp' => $emp,
        'certificate_name' => $requestss->certificate_name,
        'request_type' => $requestss->request_type,
        'request_date' => $requestss->request_date,
        'reason' => $requestss->reason,
        'next_remark' => $next_remark,
      );

      $mail_to = User::where('username',explode('_', $requestss->staff_qa)[0])->first();

      Mail::to($mail_to->email)
      ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com','rio.irvansyah@music.yamaha.com'])
      ->send(new SendEmail($data, 'qa_certificate_submission_new'));

      $message = 'Pengajuan Nomor '.$request_id.' Berhasil Disetujui.';
    }else{
      $message = 'Pengajuan Nomor '.$request_id.' Pernah Disetujui.';
    }
  }

  if ($remark == 'foreman_qa') {
    $request_ids = explode(',', $request_id);
    $requestss = DB::connection('ympimis_2')
    ->table('qa_certificate_submissions')
    ->where('request_id',$request_ids[0])
    ->first();
    $news = [];

    $approve_status = 0;
    for ($i=0; $i < count($request_ids); $i++) { 
      $requests = DB::connection('ympimis_2')
      ->table('qa_certificate_submissions')
      ->where('request_id',$request_ids[$i])
      ->first();

      if (explode('_', $requests->staff_qa)[2] == '') {
        $approve = DB::connection('ympimis_2')
        ->table('qa_certificate_submissions')
        ->where('request_id',$request_ids[$i])
        ->update([
          'staff_qa' => $requests->staff_qa.date('Y-m-d H:i:s'),
          'request_status' => 'Partially Approved'
        ]);

        $requests = DB::connection('ympimis_2')
        ->table('qa_certificate_submissions')
        ->where('request_id',$request_ids[$i])
        ->first();

        array_push($news, $requests);
      }else{
        $approve_status++;
      }
    }
    if ($approve_status == 0) {
      $next_remark = 'leader_qa';

      $emp = EmployeeSync::select('employee_syncs.*','departments.department_shortname')->where('end_date',null)->leftjoin('departments','departments.department_name','employee_syncs.department')->get();

      $data = array(
        'news' => $news,
        'emp' => $emp,
        'certificate_name' => $requestss->certificate_name,
        'request_type' => $requestss->request_type,
        'request_date' => $requestss->request_date,
        'reason' => $requestss->reason,
        'next_remark' => $next_remark,
      );

      $mail_to = User::where('username',explode('_', $requestss->foreman_qa)[0])->first();

      Mail::to($mail_to->email)
      ->cc('ratri.sulistyorini@music.yamaha.com')
      ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com','rio.irvansyah@music.yamaha.com'])
      ->send(new SendEmail($data, 'qa_certificate_submission_new'));

      $message = 'Pengajuan Nomor '.$request_id.' Berhasil Disetujui.';
    }else{
      $message = 'Pengajuan Nomor '.$request_id.' Pernah Disetujui.';
    }
  }

  if ($remark == 'leader_qa') {
    $request_ids = explode(',', $request_id);
    $requestss = DB::connection('ympimis_2')
    ->table('qa_certificate_submissions')
    ->where('request_id',$request_ids[0])
    ->first();
    $news = [];

    $approve_status = 0;
    for ($i=0; $i < count($request_ids); $i++) { 
      $requests = DB::connection('ympimis_2')
      ->table('qa_certificate_submissions')
      ->where('request_id',$request_ids[$i])
      ->first();

      if (explode('_', $requests->foreman_qa)[2] == '') {
        $approve = DB::connection('ympimis_2')
        ->table('qa_certificate_submissions')
        ->where('request_id',$request_ids[$i])
        ->update([
          'foreman_qa' => $requests->foreman_qa.date('Y-m-d H:i:s'),
          'request_status' => 'Receive Leader QA'
        ]);
      }else{
        $approve_status++;
      }
      $requests = DB::connection('ympimis_2')
      ->table('qa_certificate_submissions')
      ->where('request_id',$request_ids[$i])
      ->first();

      array_push($news, $requests);
    }
    if ($approve_status == 0) {
      $message = 'Pengajuan Nomor '.$request_id.' Berhasil Disetujui.';
    }else{
      $message = 'Pengajuan Nomor '.$request_id.' Pernah Disetujui.';
    }
  }

  if ($remark == 'full') {
    $request_ids = explode(',', $request_id);
    $requestss = DB::connection('ympimis_2')
    ->table('qa_certificate_submissions')
    ->where('request_id',$request_ids[0])
    ->first();
    $news = [];

    $approve_status = 0;
    for ($i=0; $i < count($request_ids); $i++) { 
      $requests = DB::connection('ympimis_2')
      ->table('qa_certificate_submissions')
      ->where('request_id',$request_ids[$i])
      ->first();

      if (explode('_', $requests->leader_qa)[2] == '') {
        $approve = DB::connection('ympimis_2')
        ->table('qa_certificate_submissions')
        ->where('request_id',$request_ids[$i])
        ->update([
          'leader_qa' => $requests->leader_qa.date('Y-m-d H:i:s'),
          'request_status' => 'Fully Approved'
        ]);
      }else{
        $approve_status++;
      }
      $requests = DB::connection('ympimis_2')
      ->table('qa_certificate_submissions')
      ->where('request_id',$request_ids[$i])
      ->first();

          // array_push($news, $requests);
    }
    if ($approve_status == 0) {
      $message = 'Pengajuan Nomor '.$request_id.' Berhasil Disetujui.';
    }else{
      $message = 'Pengajuan Nomor '.$request_id.' Pernah Disetujui.';
    }
  }

  $emp = EmployeeSync::select('employee_syncs.*','departments.department_shortname')->where('end_date',null)->leftjoin('departments','departments.department_name','employee_syncs.department')->get();

  return view('qa.certificate.approval_submission')
  ->with('title', 'New Kensa Certificate Submission Approval')
  ->with('title_jp', '検査認定証新規申請承認')
  ->with('head','Persetujuan Pengajuan Sertifikat Baru')
  ->with('message',$message)
  ->with('news',$news)
  ->with('emp',$emp)
  ->with('page', 'QA Kensa Certificate')
  ->with('jpn', '検査認定証新規申請承認');
}

public function deleteSubmissionCertificateNew($request_id)
{
  $delete = DB::connection('ympimis_2')
  ->table('qa_certificate_submissions')
  ->where('request_id',$request_id)
  ->delete();

  if ($delete) {
    return redirect()->route('subsmission_certificate_qa')->with('status','Success Delete Data');
  }else{
    return redirect()->route('subsmission_certificate_qa')->with('error','Error Delete Data');
  }
}

public function editSubmissionCertificateNew($request_id)
{
  try {
    $new = DB::connection('ympimis_2')
    ->table('qa_certificate_submissions')
    ->where('request_id')
    ->first();

    $response = array(
      'status' => true,
      'new'=> $new
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

public function approvalSubmissionCertificateNon($remark,$request_id)
{
  $reqid = '';
  if($request_id != null){
    $reqids =  explode(",", $request_id);
    for ($i=0; $i < count($reqids); $i++) {
      $reqid = $reqid."'".$reqids[$i]."'";
      if($i != (count($reqids)-1)){
        $reqid = $reqid.',';
      }
    }
    $reqidin = " where request_id in (".$reqid.") ";
  }
  else{
    $reqidin = "";
  }
  $cek = DB::connection('ympimis_2')
  ->select("select * from qa_certificate_submissions ".$reqidin);

  if (count($cek) == 0) {
    return view('qa.certificate.approval_submission')
    ->with('title', 'Non-Active Kensa Certificate Submission Approval')
    ->with('title_jp', '検査認定証無効化承認')
    ->with('head','Persetujuan Pengajuan Sertifikat Non-Aktif')
    ->with('error','Data Tidak Tersedia / Dibatalkan')
        // ->with('news')
    ->with('page', 'QA Kensa Certificate')
    ->with('jpn', '検査認定証無効化承認');
  }

  if ($remark == 'staff_qa') {
    $request_ids = explode(',', $request_id);
    $requestss = DB::connection('ympimis_2')
    ->table('qa_certificate_submissions')
    ->where('request_id',$request_ids[0])
    ->first();
    $news = [];
    $nons = [];

    $approve_status = 0;
    for ($i=0; $i < count($request_ids); $i++) { 
      $requests = DB::connection('ympimis_2')
      ->table('qa_certificate_submissions')
      ->where('request_id',$request_ids[$i])
      ->first();

      if (explode('_', $requests->foreman_prod)[2] == '') {
        $approve = DB::connection('ympimis_2')
        ->table('qa_certificate_submissions')
        ->where('request_id',$request_ids[$i])
        ->update([
          'foreman_prod' => $requests->foreman_prod.date('Y-m-d H:i:s'),
          'request_status' => 'Fully Approved'
        ]);

        $requests = DB::connection('ympimis_2')
        ->table('qa_certificate_submissions')
        ->where('request_id',$request_ids[$i])
        ->first();

        array_push($nons, $requests);
      }else{
        $approve_status++;
      }

      if (explode('_', $requests->staff_qa)[2] == '') {
        $approve = DB::connection('ympimis_2')
        ->table('qa_certificate_submissions')
        ->where('request_id',$request_ids[$i])
        ->update([
          'staff_qa' => $requests->staff_qa.date('Y-m-d H:i:s'),
          'request_status' => 'Fully Approved'
        ]);

        $requests = DB::connection('ympimis_2')
        ->table('qa_certificate_submissions')
        ->where('request_id',$request_ids[$i])
        ->first();

            // array_push($news, $requests);
      }else{
        $approve_status++;
      }

      if (explode('_', $requests->foreman_qa)[2] == '') {
        $approve = DB::connection('ympimis_2')
        ->table('qa_certificate_submissions')
        ->where('request_id',$request_ids[$i])
        ->update([
          'foreman_qa' => $requests->foreman_qa.date('Y-m-d H:i:s'),
          'request_status' => 'Fully Approved'
        ]);

        $requests = DB::connection('ympimis_2')
        ->table('qa_certificate_submissions')
        ->where('request_id',$request_ids[$i])
        ->first();

            // array_push($news, $requests);
      }else{
        $approve_status++;
      }

      if (explode('_', $requests->leader_qa)[2] == '') {
        $approve = DB::connection('ympimis_2')
        ->table('qa_certificate_submissions')
        ->where('request_id',$request_ids[$i])
        ->update([
          'leader_qa' => $requests->leader_qa.date('Y-m-d H:i:s'),
          'request_status' => 'Fully Approved'
        ]);

        $requests = DB::connection('ympimis_2')
        ->table('qa_certificate_submissions')
        ->where('request_id',$request_ids[$i])
        ->first();

            // array_push($news, $requests);
      }else{
        $approve_status++;
      }
    }
    if ($approve_status == 0) {
      $next_remark = 'foreman_prod';

      $emp = EmployeeSync::select('employee_syncs.*','departments.department_shortname')->where('end_date',null)->leftjoin('departments','departments.department_name','employee_syncs.department')->get();

      $data = array(
        'nons' => $nons,
        'certificate_name' => $requestss->certificate_name,
        'request_type' => $requestss->request_type,
        'request_date' => $requestss->request_date,
        'reason' => $requestss->reason,
        'next_remark' => $next_remark,
        'emp' => $emp,
        'complete' => 'complete',
      );

      $mail_to = User::where('username',explode('_', $requestss->foreman_prod)[0])->first();

      Mail::to($mail_to->email)
      ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com','rio.irvansyah@music.yamaha.com'])
      ->send(new SendEmail($data, 'qa_certificate_submission_non'));

      $message = 'Pengajuan Nomor '.$request_id.' Berhasil Disetujui.';
    }else{
      $message = 'Pengajuan Nomor '.$request_id.' Pernah Disetujui.';
    }
  }

  $emp = EmployeeSync::select('employee_syncs.*','departments.department_shortname')->where('end_date',null)->leftjoin('departments','departments.department_name','employee_syncs.department')->get();

  return view('qa.certificate.approval_submission')
  ->with('title', 'Non-Active Kensa Certificate Submission Approval')
  ->with('title_jp', '検査認定証無効化承認')
  ->with('head','Persetujuan Pengajuan Sertifikat Non-Aktif')
  ->with('message',$message)
  ->with('news',$news)
  ->with('emp',$emp)
  ->with('page', 'QA Kensa Certificate')
  ->with('jpn', '検査認定証無効化承認');
}

public function deleteSubmissionCertificateNon($request_id)
{
  $delete = DB::connection('ympimis_2')
  ->table('qa_certificate_submissions')
  ->where('request_id',$request_id)
  ->delete();

  if ($delete) {
    return redirect()->route('subsmission_certificate_qa')->with('status','Success Delete Data');
  }else{
    return redirect()->route('subsmission_certificate_qa')->with('error','Error Delete Data');
  }
}

public function getNotifCertificate()
{
  if (Auth::user() !== null) {
    $user = strtoupper(Auth::user()->username);
    $name = Auth::user()->name;
    $role = Auth::user()->role_code;

    $notif = 0;
    $jumlah_tanggungan = 0;

    $cer_approval = DB::connection('ympimis_2')
    ->select("SELECT
      approver_id 
      FROM
      `qa_certificate_approvals`
      JOIN qa_certificate_codes ON qa_certificate_codes.certificate_id = qa_certificate_approvals.certificate_id 
      WHERE
      priority = 1 
      AND approver_id = '".$user."' 
      AND `code` = 'I'");

    if (count($cer_approval) > 0) {
      $notif = count($cer_approval);
    }

    return $notif;
  }
}

public function getNotifCertificateInprocess()
{
  if (Auth::user() !== null) {
    $user = strtoupper(Auth::user()->username);
    $name = Auth::user()->name;
    $role = Auth::user()->role_code;

    $notif = 0;
    $jumlah_tanggungan = 0;

    $cer_approval = DB::connection('ympimis_2')
    ->select("SELECT
      approver_id 
      FROM
      `qa_certificate_approvals`
      JOIN qa_certificate_codes ON qa_certificate_codes.certificate_id = qa_certificate_approvals.certificate_id 
      WHERE
      priority = 1 
      AND approver_id = '".$user."' 
      AND `code` != 'I'");

    if (count($cer_approval) > 0) {
      $notif = count($cer_approval);
    }

    return $notif;
  }
}

public function getNotifCertificateSubmissionNew()
{
  if (Auth::user() !== null) {
    $user = strtoupper(Auth::user()->username);

    $foreman_prod = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
      ( foreman_prod ) 
      FROM
      qa_certificate_submissions 
      WHERE
      foreman_prod LIKE '%".$user."%' 
      AND SPLIT_STRING ( applicant, '_', 3 ) != '' 
      AND SPLIT_STRING ( foreman_prod, '_', 3 ) = '' 
      AND request_type = 'new' 
      AND deleted_at IS NULL");

    $staff_qa = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
      ( staff_qa ) 
      FROM
      qa_certificate_submissions 
      WHERE
      staff_qa LIKE '%".$user."%' 
      AND SPLIT_STRING ( foreman_prod, '_', 3 ) != '' 
      AND SPLIT_STRING ( staff_qa, '_', 3 ) = '' 
      AND request_type = 'new' 
      AND deleted_at IS NULL");

    $foreman_qa = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
      ( foreman_qa ) 
      FROM
      qa_certificate_submissions 
      WHERE
      foreman_qa LIKE '%".$user."%' 
      AND SPLIT_STRING ( staff_qa, '_', 3 ) != '' 
      AND SPLIT_STRING ( foreman_qa, '_', 3 ) = '' 
      AND request_type = 'new' 
      AND deleted_at IS NULL");

    $leader_qa = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
      ( leader_qa ) 
      FROM
      qa_certificate_submissions 
      WHERE
      leader_qa LIKE '%".$user."%' 
      AND SPLIT_STRING ( foreman_qa, '_', 3 ) != '' 
      AND SPLIT_STRING ( leader_qa, '_', 3 ) = '' 
      AND request_type = 'new' 
      AND deleted_at IS NULL");

    $notif = 0;

    if (count($foreman_prod) > 0 || count($staff_qa) > 0 || count($foreman_qa) > 0 || count($leader_qa) > 0) {
      $notif = count($foreman_prod) + count($staff_qa) + count($foreman_qa) + count($leader_qa);
    }
    return $notif;
  }
}

public function getNotifCertificateSubmissionNon()
{
  if (Auth::user() !== null) {
    $user = strtoupper(Auth::user()->username);

    $staff_qa = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
      ( staff_qa ) 
      FROM
      qa_certificate_submissions 
      WHERE
      staff_qa LIKE '%".$user."%' 
      AND SPLIT_STRING ( applicant, '_', 3 ) != '' 
      AND SPLIT_STRING ( staff_qa, '_', 3 ) = '' 
      AND request_type = 'deactivate' 
      AND deleted_at IS NULL");

    $notif = 0;

    if (count($staff_qa) > 0) {
      $notif = count($staff_qa);
    }
    return $notif;
  }
}

public function indexCertificateQrCode(Request $requests)
{
  $qr_code = DB::CONNECTION('ympimis_2')
  ->select("SELECT
          * 
    FROM
    `qa_certificate_codes` 
    WHERE
    `status` = 1
    and code = 'I'");

  return view('qa.certificate.index_qr_code')
  ->with('qr_code',$qr_code)
  ->with('title', 'QA Kensa Certificate QR Code')
  ->with('title_jp', '')
  ->with('page', 'QA Kensa Certificate QR Code')
  ->with('jpn', '');
}

public function printCertificateQrCode($certificate_id)
{
  $ids = explode(",",$certificate_id);


  $whereID = '';
  $list_id = array();
  $cer_array = [];
  $cer_array2 = [];
  for ($i=0; $i < count($ids); $i++) {
          // array_push($list_id, $ids[$i]); 
          // $whereID = $whereID."'".$ids[$i]."'";
          // if($i != (count($ids)-1)){
          //     $whereID = $whereID.',';
          // }
    $tools = DB::connection('ympimis_2')->select("SELECT
              * 
      FROM
      qa_certificate_codes 
      WHERE
      certificate_id = '".$ids[$i]."' 
      ORDER BY
      certificate_id ASC");


    array_push($cer_array, $tools);
    if ($i % 3 == 2 || $i == (count($ids)-1)) {
      array_push($cer_array2, $cer_array);
      $cer_array = [];
    }


    $update = DB::connection('ympimis_2')->table('qa_certificate_codes')->where('certificate_id', $ids[$i])->update(['print_status' => 1]);
  }

  $pdf = \App::make('dompdf.wrapper');
  $pdf->getDomPDF()->set_option("enable_php", true);
  $pdf->setPaper('A4', 'potrait');
  $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

  $pdf->loadView('qa.certificate.print_qr_code', array(
    'cer_array2' => $cer_array2
  ));

  return $pdf->stream("Certificate QR Code.pdf");
}

public function indexCertificateQrCodeInprocess(Request $requests)
{
  $qr_code = DB::CONNECTION('ympimis_2')
  ->select("SELECT
  *,
    a.certificate_desc 
    FROM
    `qa_certificate_codes`
    JOIN ( SELECT DISTINCT ( certificate_desc ), certificate_id FROM qa_certificate_inprocesses ) a ON a.certificate_id = qa_certificate_codes.certificate_id 
    WHERE
    `status` = 1 
    AND CODE != 'I'");

  return view('qa.certificate_inprocess.index_qr_code')
  ->with('qr_code',$qr_code)
  ->with('title', 'QA Kensa Certificate QR Code')
  ->with('title_jp', '')
  ->with('page', 'QA Kensa Certificate QR Code')
  ->with('jpn', '');
}

public function printCertificateQrCodeInprocess($certificate_id)
{
  $ids = explode(",",$certificate_id);


  $whereID = '';
  $list_id = array();
  $cer_array = [];
  $cer_array2 = [];
  for ($i=0; $i < count($ids); $i++) {
          // array_push($list_id, $ids[$i]); 
          // $whereID = $whereID."'".$ids[$i]."'";
          // if($i != (count($ids)-1)){
          //     $whereID = $whereID.',';
          // }
    $tools = DB::connection('ympimis_2')->select("SELECT
              * 
      FROM
      qa_certificate_codes 
      WHERE
      certificate_id = '".$ids[$i]."' 
      ORDER BY
      certificate_id ASC");


    array_push($cer_array, $tools);
    if ($i % 3 == 2 || $i == (count($ids)-1)) {
      array_push($cer_array2, $cer_array);
      $cer_array = [];
    }


    $update = DB::connection('ympimis_2')->table('qa_certificate_codes')->where('certificate_id', $ids[$i])->update(['print_status' => 1]);
  }

  $pdf = \App::make('dompdf.wrapper');
  $pdf->getDomPDF()->set_option("enable_php", true);
  $pdf->setPaper('A4', 'potrait');
  $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

  $pdf->loadView('qa.certificate_inprocess.print_qr_code', array(
    'cer_array2' => $cer_array2
  ));

  return $pdf->stream("Certificate QR Code Inprocess.pdf");
}

public function indexNewCertificateInprocess()
{
  $code_number = DB::connection('ympimis_2')
  ->table('qa_certificate_codes')
  ->select('qa_certificate_codes.code','qa_certificate_codes.code_number','qa_certificate_codes.description','qa_certificate_codes.certificate_name')->distinct()->where('remark','1')->where('code','!=','I')->get();

  $periode = DB::connection('ympimis_2')
  ->table('qa_certificate_periodes')->select('code','code_number','description','certificate_name','periode')->get();

  $composition = DB::connection('ympimis_2')
  ->table('qa_certificate_compositions')->get();

  $empsync = EmployeeSync::where('end_date',null)->get();

  $fy_now = WeeklyCalendar::where('week_date',date('Y-m-d'))->first();

  $fy = WeeklyCalendar::select(DB::RAW("DATE_FORMAT( week_date, '%Y-%m' ) as months"))->distinct()->where('fiscal_year',$fy_now->fiscal_year)->orderby('week_date')->get();

  return view('qa.certificate_inprocess.index_new_inprocess')
  ->with('title', 'New QA Kensa Certificate Inprocess')
  ->with('title_jp', '工程内検査認証新規発行')
  ->with('code_number', $code_number)
  ->with('periode', $periode)
  ->with('composition', $composition)
  ->with('fy', $fy)
  ->with('employees', $empsync)
  ->with('auditor_id', Auth::user()->username)
  ->with('auditor_name', Auth::user()->name)
  ->with('page', 'QA Kensa Certificate Inprocess')
  ->with('jpn', '');
}

public function indexRenewCertificateInprocess($certificate_id)
{

  $certificate = DB::connection('ympimis_2')
  ->table('qa_certificate_inprocesses')
  ->select('qa_certificate_inprocesses.certificate_id','qa_certificate_inprocesses.employee_id','qa_certificate_inprocesses.name','qa_certificate_inprocesses.periode_from','qa_certificate_inprocesses.periode_to','qa_certificate_inprocesses.certificate_code','qa_certificate_inprocesses.certificate_name','qa_certificate_inprocesses.status');

  $code_number = DB::connection('ympimis_2')
  ->table('qa_certificate_codes')
  ->select('qa_certificate_codes.code','qa_certificate_codes.code_number','qa_certificate_codes.description','qa_certificate_codes.certificate_name')->distinct()->where('code','!=','I');

  if ($certificate_id != '000') {
    $code_number = $code_number->where('certificate_id',$certificate_id)->where('status','!=','0');
    $certificate = $certificate->where('certificate_id',$certificate_id)->where('status','!=','0');
  }else{
    $code_number = $code_number->where('status','!=','0')->distinct();
    $certificate = $certificate->where('status','!=','0');
  }
  $code_number = $code_number->get();
  $certificate = $certificate->distinct()->get();

  $periode = DB::connection('ympimis_2')
  ->table('qa_certificate_periodes')->select('code','code_number','description','certificate_name','periode')->get();

  $composition = DB::connection('ympimis_2')
  ->table('qa_certificate_compositions')->get();

  $empsync = EmployeeSync::where('end_date',null)->get();

  $fy_now = WeeklyCalendar::where('week_date',date('Y-m-d'))->first();

  $fy = WeeklyCalendar::select(DB::RAW("DATE_FORMAT( week_date, '%Y-%m' ) as months"))->distinct()->where('fiscal_year',$fy_now->fiscal_year)->orderby('week_date')->get();

  return view('qa.certificate_inprocess.index_renew_inprocess')
  ->with('title', 'Renew QA Kensa Certificate Inprocess')
  ->with('title_jp', '工程内検査認証再発行')
  ->with('code_number', $code_number)
  ->with('periode', $periode)
  ->with('composition', $composition)
  ->with('certificate', $certificate)
  ->with('fy', $fy)
  ->with('employees', $empsync)
  ->with('auditor_id', Auth::user()->username)
  ->with('auditor_name', Auth::user()->name)
  ->with('page', 'QA Kensa Certificate Inprocess')
  ->with('jpn', '');
}

public function fetchNewCertificateInprocess(Request $request)
{
  try {
    $periode = DB::connection('ympimis_2')->table('qa_certificate_periodes')->where('code',$request->get('code'))->where('code_number',$request->get('code_number'))->first();
    $response = array(
      'status' => true,
      'periode' => $periode
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

public function inputNewCertificateInprocess(Request $request)
{
  try {
    $q_1 = $request->get('q_1');
    $a_1 = $request->get('a_1');
    $q_2 = $request->get('q_2');
    $a_2 = $request->get('a_2');
    $q_3 = $request->get('q_3');
    $a_3 = $request->get('a_3');
    $q_4 = $request->get('q_4');
    $a_4 = $request->get('a_4');

    $total_question = $request->get('total_question');
    $total_answer = $request->get('total_answer');

    $presentase_a = explode(' ', $request->get('presentase_a'))[0];
    $presentase_total = explode(' ', $request->get('presentase_total'))[0];

    $total_ik = $request->get('total_ik');
    $ok_ik = $request->get('ok_ik');
    $ng_ik = $request->get('ng_ik');
    $presentase_ik = explode(' ', $request->get('presentase_ik'))[0];

    $auditor_id = $request->get('auditor_id');
    $auditor_name = $request->get('auditor_name');
    $employee_id = $request->get('employee_id');
    $employee_name = $request->get('employee_name');
    $staff_id = $request->get('staff_id');
    $staff_name = $request->get('staff_name');
    $staff_email = $request->get('staff_email');
    $code = $request->get('code');
    $code_number = $request->get('code_number');
    $code_desc = $request->get('code_desc');
    $issued_date = $request->get('issued_date');
    $expired_date = $request->get('expired_date');

    $code_generator = CodeGenerator::where('note', '=', 'qa_certificate')->first();
    $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

    $serial_number = $code_generator->prefix.$numbers;

    $code_generator->index = $code_generator->index+1;

    $certificate_codes = DB::connection('ympimis_2')
    ->table('qa_certificate_codes')
    ->where('code',$code)
    ->where('code_number',$code_number)
    ->first();

    $certificate_codes_empty = DB::connection('ympimis_2')
    ->table('qa_certificate_codes')
    ->where('code',$code)
    ->where('code_number',$code_number)
    ->where('certificate_id',null)
    ->get();

    $empsync = EmployeeSync::where('employee_id',$employee_id)->first();

    if ($code_desc == 'KEY PART PROCESS in YMPI' || $code_desc == 'Recorder Injection Process in YMPI' || $code_desc == 'Venova Injection Process in YMPI' || $code_desc == 'YDS Injection Process in YMPI' || $code_desc == 'Mouthpiece Injection Process in YMPI') {
      if ($empsync->department == 'Standardization Department') {
        $approval_type = 'QA';
        $certificate_name = explode(',', $certificate_codes->certificate_name)[0];
      }else{
        $approval_type = 'PRODUKSI';
        $certificate_name = explode(',', $certificate_codes->certificate_name)[1];
      }
    }else if($code_desc == 'WIND INSTRUMENT (INCOMING) in YMPI'){
      $approval_type = 'QA';
      $certificate_name = $certificate_codes->certificate_name;
    }else if($code_desc == 'INCOMING CASE in YMPI'){
      $approval_type = 'QA';
      $certificate_name = $certificate_codes->certificate_name;
    }else if($code_desc == 'INCOMING PIANICA in YMPI'){
      $approval_type = 'QA';
      $certificate_name = $certificate_codes->certificate_name;
    }else if($code_desc == 'INCOMING RECORDER in YMPI'){
      $approval_type = 'QA';
      $certificate_name = $certificate_codes->certificate_name;
    }else if($code_desc == 'INCOMING VENOVA in YMPI'){
      $approval_type = 'QA';
      $certificate_name = $certificate_codes->certificate_name;
    }else if(str_contains($code_desc,'BELL YDS')){
      if ($empsync->department == 'Standardization Department') {
        $approval_type = 'QA';
      }else{
        $approval_type = 'PRODUKSI';
      }
      $certificate_name = $certificate_codes->certificate_name;
    }else{
      $approval_type = 'PRODUKSI';
      $certificate_name = $certificate_codes->certificate_name;
    }

    if (count($certificate_codes_empty) > 0) {
      $number = $certificate_codes_empty[0]->number;
      $certificate_code_old = DB::connection('ympimis_2')
      ->table('qa_certificate_codes')
      ->where('id',$certificate_codes_empty[0]->id)
      ->update([
        'certificate_id' => $serial_number,
        'employee_id' => $employee_id,
        'name' => $employee_name,
        'status' => 1,
        'updated_at' => date('Y-m-d H:i:s')
      ]); 
    }else{
      $last_certificate_codes = DB::connection('ympimis_2')
      ->table('qa_certificate_codes')
      ->where('code',$code)
      ->where('code_number',$code_number)
      ->orderBy('number','desc')
      ->first();
      $number = $last_certificate_codes->number+1;
      $number = sprintf('%03d', $last_certificate_codes->number+1);
      $certificate_code_new = DB::connection('ympimis_2')
      ->table('qa_certificate_codes')
      ->insert([
        'certificate_id' => $serial_number,
        'employee_id' => $employee_id,
        'name' => $employee_name,
        'code' => $code,
        'code_number' => $code_number,
        'number' => $number,
        'description' => $last_certificate_codes->description,
        'certificate_name' => $last_certificate_codes->certificate_name,
        'certificate_paper' => $last_certificate_codes->certificate_paper,
        'certificate_color' => $last_certificate_codes->certificate_color,
        'status' => 1,
        'remark' => 1,
        'created_by' => Auth::id(),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
      ]); 
    }

    $input = DB::connection('ympimis_2')->table('qa_certificate_inprocesses')->insert([
      'certificate_id' => $serial_number,
      'employee_id' => $employee_id,
      'name' => $employee_name,
      'periode_from' => $issued_date,
      'periode_to' => $expired_date,
      'certificate_code' => 'YMPI-QA-'.$code.'-'.$code_number.$number,
      'certificate_name' => $certificate_name,
      'certificate_desc' => $code_desc,
      'category' => 'Point IK',
      'question' => $total_ik,
      'total_question' => $total_ik,
      'answer' => $ok_ik,
      'total_answer' => $ng_ik,
      'presentase_a' => $presentase_a,
      'presentase_total' => $presentase_ik,
      'decision' => 'LULUS',
      'status' => 1,
      'auditor_id' => $auditor_id,
      'auditor_name' => $auditor_name,
      'approval_type' => $approval_type,
      'staff_id' => $staff_id,
      'staff_name' => $staff_name,
      'staff_email' => $staff_email,
      'created_by' => Auth::user()->id,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),

    ]);

    $input = DB::connection('ympimis_2')->table('qa_certificate_inprocesses')->insert([
      'certificate_id' => $serial_number,
      'employee_id' => $employee_id,
      'name' => $employee_name,
      'periode_from' => $issued_date,
      'periode_to' => $expired_date,
      'certificate_code' => 'YMPI-QA-'.$code.'-'.$code_number.$number,
      'certificate_name' => $certificate_name,
      'certificate_desc' => $code_desc,
      'category' => 'Standart Produk',
      'question' => $q_1,
      'total_question' => $total_question,
      'answer' => $a_1,
      'total_answer' => $total_answer,
      'presentase_a' => $presentase_a,
      'presentase_total' => $presentase_total,
      'decision' => 'LULUS',
      'status' => 1,
      'auditor_id' => $auditor_id,
      'auditor_name' => $auditor_name,
      'approval_type' => $approval_type,
      'staff_id' => $staff_id,
      'staff_name' => $staff_name,
      'staff_email' => $staff_email,
      'created_by' => Auth::user()->id,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $input = DB::connection('ympimis_2')->table('qa_certificate_inprocesses')->insert([
      'certificate_id' => $serial_number,
      'employee_id' => $employee_id,
      'name' => $employee_name,
      'periode_from' => $issued_date,
      'periode_to' => $expired_date,
      'certificate_code' => 'YMPI-QA-'.$code.'-'.$code_number.$number,
      'certificate_name' => $certificate_name,
      'certificate_desc' => $code_desc,
      'category' => 'Standart Produk',
      'question' => $q_2,
      'total_question' => $total_question,
      'answer' => $a_2,
      'total_answer' => $total_answer,
      'presentase_a' => $presentase_a,
      'presentase_total' => $presentase_total,
      'decision' => 'LULUS',
      'status' => 1,
      'auditor_id' => $auditor_id,
      'auditor_name' => $auditor_name,
      'approval_type' => $approval_type,
      'staff_id' => $staff_id,
      'staff_name' => $staff_name,
      'staff_email' => $staff_email,
      'created_by' => Auth::user()->id,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $input = DB::connection('ympimis_2')->table('qa_certificate_inprocesses')->insert([
      'certificate_id' => $serial_number,
      'employee_id' => $employee_id,
      'name' => $employee_name,
      'periode_from' => $issued_date,
      'periode_to' => $expired_date,
      'certificate_code' => 'YMPI-QA-'.$code.'-'.$code_number.$number,
      'certificate_name' => $certificate_name,
      'certificate_desc' => $code_desc,
      'category' => 'Standart Produk',
      'question' => $q_3,
      'total_question' => $total_question,
      'answer' => $a_3,
      'total_answer' => $total_answer,
      'presentase_a' => $presentase_a,
      'presentase_total' => $presentase_total,
      'decision' => 'LULUS',
      'status' => 1,
      'auditor_id' => $auditor_id,
      'auditor_name' => $auditor_name,
      'approval_type' => $approval_type,
      'staff_id' => $staff_id,
      'staff_name' => $staff_name,
      'staff_email' => $staff_email,
      'created_by' => Auth::user()->id,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $input = DB::connection('ympimis_2')->table('qa_certificate_inprocesses')->insert([
      'certificate_id' => $serial_number,
      'employee_id' => $employee_id,
      'name' => $employee_name,
      'periode_from' => $issued_date,
      'periode_to' => $expired_date,
      'certificate_code' => 'YMPI-QA-'.$code.'-'.$code_number.$number,
      'certificate_name' => $certificate_name,
      'certificate_desc' => $code_desc,
      'category' => 'Standart Produk',
      'question' => $q_4,
      'total_question' => $total_question,
      'answer' => $a_4,
      'total_answer' => $total_answer,
      'presentase_a' => $presentase_a,
      'presentase_total' => $presentase_total,
      'decision' => 'LULUS',
      'status' => 1,
      'auditor_id' => $auditor_id,
      'auditor_name' => $auditor_name,
      'approval_type' => $approval_type,
      'staff_id' => $staff_id,
      'staff_name' => $staff_name,
      'staff_email' => $staff_email,
      'created_by' => Auth::user()->id,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    self :: masterCertificateInprocessApproval($serial_number,$approval_type,$staff_id,$staff_name,$staff_email,$auditor_id,$auditor_name,$empsync->department,$empsync->section);

    $datas = DB::connection('ympimis_2')
    ->table('qa_certificate_codes')
    ->join('qa_certificate_inprocesses','qa_certificate_inprocesses.certificate_id','qa_certificate_codes.certificate_id')
    ->where('qa_certificate_codes.certificate_id',$serial_number)
    ->get();

    $composition = DB::connection('ympimis_2')
    ->table('qa_certificate_compositions')->get();

    $remark_atas = [];

    array_push($remark_atas, 'Foreman Produksi');
    array_push($remark_atas, 'Foreman QA');
    array_push($remark_atas, 'Chief QA');

    $data_approval_atas = DB::connection('ympimis_2')
    ->table('qa_certificate_approvals')
    ->where('qa_certificate_approvals.certificate_id',$serial_number)
    ->wherein('remark',$remark_atas)
    ->orderBy('id','desc')
    ->get();

    $remark_bawah = [];

    array_push($remark_bawah, 'Leader QA');
    array_push($remark_bawah, 'Staff QA');

    $data_approval_bawah = DB::connection('ympimis_2')
    ->table('qa_certificate_approvals')
    ->where('qa_certificate_approvals.certificate_id',$serial_number)
    ->wherein('remark',$remark_bawah)
    ->orderBy('id','desc')
    ->get();

    // return view('qa.certificate_inprocess.print')->with('datas',$datas)->with('data_approval',$data_approval);

    $pdf = \App::make('dompdf.wrapper');
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper($datas[0]->certificate_paper, 'potrait');

    $pdf->loadView('qa.certificate_inprocess.print', array(
      'datas' => $datas,
      'data_approval_atas' => $data_approval_atas,
      'data_approval_bawah' => $data_approval_bawah,
      'composition' => $composition,
    ));

    $depan = $serial_number.".pdf";

    $pdf->save(public_path() . "/data_file/qa/certificate_fix_inprocess/".$depan);

    $code_generator->save();
    $response = array(
      'status' => true,
      'message' => 'Berhasil Membuat Sertifikat',
      'certificate_id' => $serial_number
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

public function inputRenewCertificateInprocess(Request $request)
{
  try {
    $q_1 = $request->get('q_1');
    $a_1 = $request->get('a_1');
    $q_2 = $request->get('q_2');
    $a_2 = $request->get('a_2');
    $q_3 = $request->get('q_3');
    $a_3 = $request->get('a_3');
    $q_4 = $request->get('q_4');
    $a_4 = $request->get('a_4');

    $total_question = $request->get('total_question');
    $total_answer = $request->get('total_answer');

    $presentase_a = explode(' ', $request->get('presentase_a'))[0];
    $presentase_total = explode(' ', $request->get('presentase_total'))[0];

    $total_ik = $request->get('total_ik');
    $ok_ik = $request->get('ok_ik');
    $ng_ik = $request->get('ng_ik');
    $presentase_ik = explode(' ', $request->get('presentase_ik'))[0];

    $auditor_id = $request->get('auditor_id');
    $auditor_name = $request->get('auditor_name');
    $employee_id = $request->get('employee_id');
    $employee_name = $request->get('employee_name');
    $staff_id = $request->get('staff_id');
    $staff_name = $request->get('staff_name');
    $staff_email = $request->get('staff_email');
    $code = $request->get('code');
    $code_number = $request->get('code_number');
    $code_desc = $request->get('code_desc');
    $issued_date = $request->get('issued_date');
    $expired_date = $request->get('expired_date');

    $code_generator = CodeGenerator::where('note', '=', 'qa_certificate')->first();
    $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

    $serial_number = $code_generator->prefix.$numbers;

    $code_generator->index = $code_generator->index+1;

    $certificate_codes = DB::connection('ympimis_2')
    ->table('qa_certificate_codes')
    ->where('code',$code)
    ->where('code_number',$code_number)
    ->first();

    $certificate_codes_empty = DB::connection('ympimis_2')
    ->table('qa_certificate_codes')
    ->where('code',$code)
    ->where('code_number',$code_number)
    ->where('certificate_id',null)
    ->get();

    $empsync = EmployeeSync::where('employee_id',$employee_id)->first();

    if ($code_desc == 'KEY PART PROCESS in YMPI' || $code_desc == 'Recorder Injection Process in YMPI' || $code_desc == 'Venova Injection Process in YMPI' || $code_desc == 'YDS Injection Process in YMPI' || $code_desc == 'Mouthpiece Injection Process in YMPI') {
      if ($empsync->department == 'Standardization Department') {
        $approval_type = 'QA';
        $certificate_name = explode(',', $certificate_codes->certificate_name)[0];
      }else{
        $approval_type = 'PRODUKSI';
        $certificate_name = explode(',', $certificate_codes->certificate_name)[1];
      }
    }else if($code_desc == 'WIND INSTRUMENT (INCOMING) in YMPI'){
      $approval_type = 'QA';
      $certificate_name = $certificate_codes->certificate_name;
    }else if($code_desc == 'INCOMING CASE in YMPI'){
      $approval_type = 'QA';
      $certificate_name = $certificate_codes->certificate_name;
    }else if($code_desc == 'INCOMING PIANICA in YMPI'){
      $approval_type = 'QA';
      $certificate_name = $certificate_codes->certificate_name;
    }else if($code_desc == 'INCOMING RECORDER in YMPI'){
      $approval_type = 'QA';
      $certificate_name = $certificate_codes->certificate_name;
    }else if($code_desc == 'INCOMING VENOVA in YMPI'){
      $approval_type = 'QA';
      $certificate_name = $certificate_codes->certificate_name;
    }else if(str_contains($code_desc,'BELL YDS')){
      if ($empsync->department == 'Standardization Department') {
        $approval_type = 'QA';
      }else{
        $approval_type = 'PRODUKSI';
      }
      $certificate_name = $certificate_codes->certificate_name;
    }else{
      $approval_type = 'PRODUKSI';
      $certificate_name = $certificate_codes->certificate_name;
    }

    // if (count($certificate_codes_empty) > 0) {
      // $number = $certificate_codes_empty[0]->number;
    $certificate_code_old = DB::connection('ympimis_2')
    ->table('qa_certificate_codes')
    ->where('certificate_id',$request->get('certificate_lama'))
    ->update([
      'certificate_id' => $serial_number,
        // 'employee_id' => $employee_id,
        // 'name' => $employee_name,
      'status' => 1,
      'updated_at' => date('Y-m-d H:i:s')
    ]); 

    $certificate_olds = DB::connection('ympimis_2')
    ->table('qa_certificate_inprocesses')
    ->where('certificate_id',$request->get('certificate_lama'))
    ->first();

    $certificate_old = DB::connection('ympimis_2')
    ->table('qa_certificate_inprocesses')
    ->where('certificate_id',$request->get('certificate_lama'))
    ->update([
        // 'employee_id' => $employee_id,
        // 'name' => $employee_name,
      'status' => 0,
      'updated_at' => date('Y-m-d H:i:s')
    ]); 
    // }else{
    //   $last_certificate_codes = DB::connection('ympimis_2')
    //   ->table('qa_certificate_codes')
    //   ->where('code',$code)
    //   ->where('code_number',$code_number)
    //   ->orderBy('number','desc')
    //   ->first();
    //   $number = $last_certificate_codes->number+1;
    //   $number = sprintf('%03d', $last_certificate_codes->number+1);
    //   $certificate_code_new = DB::connection('ympimis_2')
    //   ->table('qa_certificate_codes')
    //   ->insert([
    //     'certificate_id' => $serial_number,
    //     'employee_id' => $employee_id,
    //     'name' => $employee_name,
    //     'code' => $code,
    //     'code_number' => $code_number,
    //     'number' => $number,
    //     'description' => $last_certificate_codes->description,
    //     'certificate_name' => $last_certificate_codes->certificate_name,
    //     'certificate_paper' => $last_certificate_codes->certificate_paper,
    //     'certificate_color' => $last_certificate_codes->certificate_color,
    //     'status' => 1,
    //     'remark' => 1,
    //     'created_by' => Auth::id(),
    //     'created_at' => date('Y-m-d H:i:s'),
    //     'updated_at' => date('Y-m-d H:i:s')
    //   ]); 
    // }

    $input = DB::connection('ympimis_2')->table('qa_certificate_inprocesses')->insert([
      'certificate_id' => $serial_number,
      'employee_id' => $employee_id,
      'name' => $employee_name,
      'periode_from' => $issued_date,
      'periode_to' => $expired_date,
      'certificate_code' => $certificate_olds->certificate_code,
      'certificate_name' => $certificate_name,
      'certificate_desc' => $code_desc,
      'category' => 'Point IK',
      'question' => $total_ik,
      'total_question' => $total_ik,
      'answer' => $ok_ik,
      'total_answer' => $ng_ik,
      'presentase_a' => $presentase_a,
      'presentase_total' => $presentase_ik,
      'decision' => 'LULUS',
      'status' => 1,
      'auditor_id' => $auditor_id,
      'auditor_name' => $auditor_name,
      'approval_type' => $approval_type,
      'staff_id' => $staff_id,
      'staff_name' => $staff_name,
      'staff_email' => $staff_email,
      'created_by' => Auth::user()->id,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),

    ]);

    $input = DB::connection('ympimis_2')->table('qa_certificate_inprocesses')->insert([
      'certificate_id' => $serial_number,
      'employee_id' => $employee_id,
      'name' => $employee_name,
      'periode_from' => $issued_date,
      'periode_to' => $expired_date,
      'certificate_code' => $certificate_olds->certificate_code,
      'certificate_name' => $certificate_name,
      'certificate_desc' => $code_desc,
      'category' => 'Standart Produk',
      'question' => $q_1,
      'total_question' => $total_question,
      'answer' => $a_1,
      'total_answer' => $total_answer,
      'presentase_a' => $presentase_a,
      'presentase_total' => $presentase_total,
      'decision' => 'LULUS',
      'status' => 1,
      'auditor_id' => $auditor_id,
      'auditor_name' => $auditor_name,
      'approval_type' => $approval_type,
      'staff_id' => $staff_id,
      'staff_name' => $staff_name,
      'staff_email' => $staff_email,
      'created_by' => Auth::user()->id,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $input = DB::connection('ympimis_2')->table('qa_certificate_inprocesses')->insert([
      'certificate_id' => $serial_number,
      'employee_id' => $employee_id,
      'name' => $employee_name,
      'periode_from' => $issued_date,
      'periode_to' => $expired_date,
      'certificate_code' => $certificate_olds->certificate_code,
      'certificate_name' => $certificate_name,
      'certificate_desc' => $code_desc,
      'category' => 'Standart Produk',
      'question' => $q_2,
      'total_question' => $total_question,
      'answer' => $a_2,
      'total_answer' => $total_answer,
      'presentase_a' => $presentase_a,
      'presentase_total' => $presentase_total,
      'decision' => 'LULUS',
      'status' => 1,
      'auditor_id' => $auditor_id,
      'auditor_name' => $auditor_name,
      'approval_type' => $approval_type,
      'staff_id' => $staff_id,
      'staff_name' => $staff_name,
      'staff_email' => $staff_email,
      'created_by' => Auth::user()->id,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $input = DB::connection('ympimis_2')->table('qa_certificate_inprocesses')->insert([
      'certificate_id' => $serial_number,
      'employee_id' => $employee_id,
      'name' => $employee_name,
      'periode_from' => $issued_date,
      'periode_to' => $expired_date,
      'certificate_code' => $certificate_olds->certificate_code,
      'certificate_name' => $certificate_name,
      'certificate_desc' => $code_desc,
      'category' => 'Standart Produk',
      'question' => $q_3,
      'total_question' => $total_question,
      'answer' => $a_3,
      'total_answer' => $total_answer,
      'presentase_a' => $presentase_a,
      'presentase_total' => $presentase_total,
      'decision' => 'LULUS',
      'status' => 1,
      'auditor_id' => $auditor_id,
      'auditor_name' => $auditor_name,
      'approval_type' => $approval_type,
      'staff_id' => $staff_id,
      'staff_name' => $staff_name,
      'staff_email' => $staff_email,
      'created_by' => Auth::user()->id,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $input = DB::connection('ympimis_2')->table('qa_certificate_inprocesses')->insert([
      'certificate_id' => $serial_number,
      'employee_id' => $employee_id,
      'name' => $employee_name,
      'periode_from' => $issued_date,
      'periode_to' => $expired_date,
      'certificate_code' => $certificate_olds->certificate_code,
      'certificate_name' => $certificate_name,
      'certificate_desc' => $code_desc,
      'category' => 'Standart Produk',
      'question' => $q_4,
      'total_question' => $total_question,
      'answer' => $a_4,
      'total_answer' => $total_answer,
      'presentase_a' => $presentase_a,
      'presentase_total' => $presentase_total,
      'decision' => 'LULUS',
      'status' => 1,
      'auditor_id' => $auditor_id,
      'auditor_name' => $auditor_name,
      'approval_type' => $approval_type,
      'staff_id' => $staff_id,
      'staff_name' => $staff_name,
      'staff_email' => $staff_email,
      'created_by' => Auth::user()->id,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    self :: masterCertificateInprocessApproval($serial_number,$approval_type,$staff_id,$staff_name,$staff_email,$auditor_id,$auditor_name,$empsync->department,$empsync->section);

    $datas = DB::connection('ympimis_2')
    ->table('qa_certificate_codes')
    ->join('qa_certificate_inprocesses','qa_certificate_inprocesses.certificate_id','qa_certificate_codes.certificate_id')
    ->where('qa_certificate_codes.certificate_id',$serial_number)
    ->get();

    $composition = DB::connection('ympimis_2')
    ->table('qa_certificate_compositions')->get();

    $remark_atas = [];

    array_push($remark_atas, 'Foreman Produksi');
    array_push($remark_atas, 'Foreman QA');
    array_push($remark_atas, 'Chief QA');

    $data_approval_atas = DB::connection('ympimis_2')
    ->table('qa_certificate_approvals')
    ->where('qa_certificate_approvals.certificate_id',$serial_number)
    ->wherein('remark',$remark_atas)
    ->orderBy('id','desc')
    ->get();

    $remark_bawah = [];

    array_push($remark_bawah, 'Leader QA');
    array_push($remark_bawah, 'Staff QA');

    $data_approval_bawah = DB::connection('ympimis_2')
    ->table('qa_certificate_approvals')
    ->where('qa_certificate_approvals.certificate_id',$serial_number)
    ->wherein('remark',$remark_bawah)
    ->orderBy('id','desc')
    ->get();

    // return view('qa.certificate_inprocess.print')->with('datas',$datas)->with('data_approval',$data_approval);

    $pdf = \App::make('dompdf.wrapper');
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper($datas[0]->certificate_paper, 'potrait');

    $pdf->loadView('qa.certificate_inprocess.print', array(
      'datas' => $datas,
      'data_approval_atas' => $data_approval_atas,
      'data_approval_bawah' => $data_approval_bawah,
      'composition' => $composition,
    ));

    $depan = $serial_number.".pdf";

    $pdf->save(public_path() . "/data_file/qa/certificate_fix_inprocess/".$depan);

    $code_generator->save();
    $response = array(
      'status' => true,
      'message' => 'Berhasil Memperbaharui Sertifikat',
      'certificate_id' => $serial_number
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

public function masterCertificateInprocessApproval($certificate_id,$approval_type,$staff_id,$staff_name,$staff_email,$leader_id,$leader_name,$department,$section)
{
  $app_leader = DB::connection('ympimis_2')->table('qa_certificate_approvals')->insert([
    'certificate_id' => $certificate_id,
    'approver_id' => $leader_id,
    'approver_name' => $leader_name,
    'approver_email' => '',
    'remark' => 'Leader QA',
    'approver_header' => 'Disusun',
    'priority' => '1',
    'created_by' => Auth::id(),
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
  ]);
  $app_staff = DB::connection('ympimis_2')->table('qa_certificate_approvals')->insert([
    'certificate_id' => $certificate_id,
    'approver_id' => $staff_id,
    'approver_name' => $staff_name,
    'approver_email' => $staff_email,
    'remark' => 'Staff QA',
    'approver_header' => 'Dicek',
    'created_by' => Auth::id(),
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
  ]);

  if ($approval_type == 'PRODUKSI') {
    $foreman_produksi = Approver::where('department',$department)->where('remark','Foreman')->where('section','like','%'.$section.'%')->first();
    if (count($foreman_produksi) > 0) {
      $app_foreman_produksi = DB::connection('ympimis_2')->table('qa_certificate_approvals')->insert([
        'certificate_id' => $certificate_id,
        'approver_id' => $foreman_produksi->approver_id,
        'approver_name' => $foreman_produksi->approver_name,
        'approver_email' => $foreman_produksi->approver_email,
        'remark' => 'Foreman Produksi',
        'approver_header' => 'Dicek',
        'created_by' => Auth::id(),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }else{
      $chief_produksi = Approver::where('department',$department)->where('remark','Chief')->first();
      $app_chief_produksi = DB::connection('ympimis_2')->table('qa_certificate_approvals')->insert([
        'certificate_id' => $certificate_id,
        'approver_id' => $chief_produksi->approver_id,
        'approver_name' => $chief_produksi->approver_name,
        'approver_email' => $chief_produksi->approver_email,
        'remark' => 'Chief Produksi',
        'approver_header' => 'Dicek',
        'created_by' => Auth::id(),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }
  }

  $foreman = Approver::where('department','Standardization Department')->where('remark','Foreman')->first();
  $app_foreman = DB::connection('ympimis_2')->table('qa_certificate_approvals')->insert([
    'certificate_id' => $certificate_id,
    'approver_id' => $foreman->approver_id,
    'approver_name' => $foreman->approver_name,
    'approver_email' => $foreman->approver_email,
    'remark' => 'Foreman QA',
    'approver_header' => 'Dicek',
    'created_by' => Auth::id(),
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
  ]);

  $chief = Approver::where('department','Standardization Department')->where('remark','Chief')->where('section','QA Process Control Section')->first();
  $app_chief = DB::connection('ympimis_2')->table('qa_certificate_approvals')->insert([
    'certificate_id' => $certificate_id,
    'approver_id' => $chief->approver_id,
    'approver_name' => $chief->approver_name,
    'approver_email' => $chief->approver_email,
    'remark' => 'Chief QA',
    'approver_header' => 'Dicek',
    'created_by' => Auth::id(),
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
  ]);
}

public function reviewCertificateInprocess($certificate_id,$remark)
{
  $approval_now = DB::connection('ympimis_2')
  ->table('qa_certificate_approvals')
  ->where('certificate_id',$certificate_id)
  ->where('remark',$remark)
  ->first();

      // $datas = DB::connection('ympimis_2')
      // ->table('qa_certificate_codes')
      // ->join('qa_certificates','qa_certificates.certificate_id','qa_certificate_codes.certificate_id')
      // ->where('qa_certificate_codes.certificate_id',$certificate_id)
      // ->get();

      // $data_subject = DB::connection('ympimis_2')
      // ->table('qa_certificates')
      // ->select('qa_certificates.subject')
      // ->distinct()
      // ->where('qa_certificates.certificate_id',$certificate_id)
      // ->get();

      // $data_approval = DB::connection('ympimis_2')
      // ->table('qa_certificate_approvals')
      // ->where('qa_certificate_approvals.certificate_id',$certificate_id)
      // ->orderBy('id','desc')
      // ->get();

      // // return view('qa.certificate.print_landscape')->with('datas',$datas)->with('data_subject',$data_subject)->with('data_approval',$data_approval);

      // $pdf = \App::make('dompdf.wrapper');
      // $pdf->getDomPDF()->set_option("enable_php", true);
      // $pdf->setPaper($datas[0]->certificate_paper, 'potrait');

      // $pdf->loadView('qa.certificate.print', array(
      //     'datas' => $datas,
      //     'data_approval' => $data_approval,
      // ));

      // $depan = "QA Certificate - ".$certificate_id." (".$datas[0]->certificate_code.").pdf";

      // $pdf->save(public_path() . "/data_file/qa/certificate/".$depan);

      // $pdf2 = \App::make('dompdf.wrapper');
      // $pdf2->getDomPDF()->set_option("enable_php", true);
      // $pdf2->getDomPDF()->set_option("enable_css_float", true);
      // $pdf2->setPaper('A4', 'landscape');

      // $pdf2->loadView('qa.certificate.print_landscape', array(
      //     'datas' => $datas,
      //     'data_subject' => $data_subject,
      //     'data_approval' => $data_approval,
      // ));

      // // return view('qa.certificate.print')->with('datas',$datas)->with('data_approval',$data_approval);

      // // return $pdf->stream("QA Certificate.pdf");
      // // return $pdf2->stream("QA Certificate.pdf");

      // $belakang = "QA Certificate Belakang - ".$certificate_id." (".$datas[0]->certificate_code.").pdf";

      // $pdf2->save(public_path() . "/data_file/qa/certificate/".$belakang);

      // $pdfFile1Path = public_path() . "/data_file/qa/certificate/".$depan;
      // $pdfFile2Path = public_path() . "/data_file/qa/certificate/".$belakang;

      // $merger = new Merger;
      // $merger->addFile($pdfFile1Path);
      // $merger->addFile($pdfFile2Path);
      // $createdPdf = $merger->merge();

      // $pathForTheMergedPdf = public_path() . "/data_file/qa/certificate_fix/".$certificate_id.".pdf";

      // file_put_contents($pathForTheMergedPdf, $createdPdf);

  $composition = DB::connection('ympimis_2')->table('qa_certificate_compositions')->get();

  $file_path = asset("/data_file/qa/certificate_fix_inprocess/".$certificate_id.".pdf");

  return view('qa.certificate_inprocess.review')->with('file',$file_path)->with('approval_now',$approval_now)->with('certificate_id',$certificate_id)->with('remark',$remark)->with('composition',$composition);
}

public function printCertificateInprocess($certificate_id)
{
  $datas = DB::connection('ympimis_2')
  ->table('qa_certificate_codes')
  ->join('qa_certificate_inprocesses','qa_certificate_inprocesses.certificate_id','qa_certificate_codes.certificate_id')
  ->where('qa_certificate_codes.certificate_id',$certificate_id)
  ->get();

  $composition = DB::connection('ympimis_2')
  ->table('qa_certificate_compositions')->get();

  $remark_atas = [];

  array_push($remark_atas, 'Foreman Produksi');
  array_push($remark_atas, 'Chief Produksi');
  array_push($remark_atas, 'Foreman QA');
  array_push($remark_atas, 'Chief QA');

  $data_approval_atas = DB::connection('ympimis_2')
  ->table('qa_certificate_approvals')
  ->where('qa_certificate_approvals.certificate_id',$certificate_id)
  ->wherein('remark',$remark_atas)
  ->orderBy('id','desc')
  ->get();

  $remark_bawah = [];

  array_push($remark_bawah, 'Leader QA');
  array_push($remark_bawah, 'Staff QA');

  $data_approval_bawah = DB::connection('ympimis_2')
  ->table('qa_certificate_approvals')
  ->where('qa_certificate_approvals.certificate_id',$certificate_id)
  ->wherein('remark',$remark_bawah)
  ->orderBy('id','desc')
  ->get();


  $pdf = \App::make('dompdf.wrapper');
  $pdf->getDomPDF()->set_option("enable_php", true);
  $pdf->setPaper($datas[0]->certificate_paper, 'potrait');

  $pdf->loadView('qa.certificate_inprocess.print', array(
    'datas' => $datas,
    'data_approval_atas' => $data_approval_atas,
    'data_approval_bawah' => $data_approval_bawah,
    'composition' => $composition,
  ));

  $depan = $certificate_id.".pdf";

  $pdf->save(public_path() . "/data_file/qa/certificate_fix_inprocess/".$depan);

  return $pdf->stream($datas[0]->certificate_code.' ('.$datas[0]->employee_id.' - '.$datas[0]->name.").pdf");

}

public function editCertificateInprocess(Request $request)
{
  try {
    $certificate = DB::connection('ympimis_2')
    ->table('qa_certificate_inprocesses')
    ->where('certificate_id',$request->get('certificate_id'))
    ->get();

    $response = array(
      'status' => true,
      'certificate' => $certificate
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

public function updateCertificateInprocess(Request $request)
{
  try {
    $q_1 = $request->get('q_1');
    $a_1 = $request->get('a_1');
    $q_2 = $request->get('q_2');
    $a_2 = $request->get('a_2');
    $q_3 = $request->get('q_3');
    $a_3 = $request->get('a_3');
    $q_4 = $request->get('q_4');
    $a_4 = $request->get('a_4');

    $total_question = $request->get('total_question');
    $total_answer = $request->get('total_answer');

    $presentase_a = $request->get('presentase_a');
    $presentase_total = $request->get('presentase_total');

    $total_ik = $request->get('total_ik');
    $ok_ik = $request->get('ok_ik');
    $ng_ik = $request->get('ng_ik');
    $presentase_ik = $request->get('presentase_ik');

    $certificate_id = $request->get('certificate_id');

    $certificate = DB::connection('ympimis_2')->table('qa_certificate_inprocesses')->where('certificate_id',$certificate_id)->get();

    $update = DB::connection('ympimis_2')->table('qa_certificate_inprocesses')->where('certificate_id',$certificate_id)->where('category','Point IK')->update([
      'question' => $total_ik,
      'total_question' => $total_ik,
      'answer' => $ok_ik,
      'total_answer' => $ng_ik,
      'presentase_a' => explode(' ', $presentase_a)[0],
      'presentase_total' => explode(' ', $presentase_ik)[0],
      'decision' => 'LULUS',
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $update = DB::connection('ympimis_2')->table('qa_certificate_inprocesses')->where('id',$certificate[1]->id)->update([
      'question' => $q_1,
      'total_question' => $total_question,
      'answer' => $a_1,
      'total_answer' => $total_answer,
      'presentase_a' => explode(' ', $presentase_a)[0],
      'presentase_total' => explode(' ', $presentase_total)[0],
      'decision' => 'LULUS',
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $update = DB::connection('ympimis_2')->table('qa_certificate_inprocesses')->where('id',$certificate[2]->id)->update([
      'question' => $q_2,
      'total_question' => $total_question,
      'answer' => $a_2,
      'total_answer' => $total_answer,
      'presentase_a' => explode(' ', $presentase_a)[0],
      'presentase_total' => explode(' ', $presentase_total)[0],
      'decision' => 'LULUS',
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $update = DB::connection('ympimis_2')->table('qa_certificate_inprocesses')->where('id',$certificate[3]->id)->update([
      'question' => $q_3,
      'total_question' => $total_question,
      'answer' => $a_3,
      'total_answer' => $total_answer,
      'presentase_a' => explode(' ', $presentase_a)[0],
      'presentase_total' => explode(' ', $presentase_total)[0],
      'decision' => 'LULUS',
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $update = DB::connection('ympimis_2')->table('qa_certificate_inprocesses')->where('id',$certificate[4]->id)->update([
      'question' => $q_4,
      'total_question' => $total_question,
      'answer' => $a_4,
      'total_answer' => $total_answer,
      'presentase_a' => explode(' ', $presentase_a)[0],
      'presentase_total' => explode(' ', $presentase_total)[0],
      'decision' => 'LULUS',
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $datas = DB::connection('ympimis_2')
    ->table('qa_certificate_codes')
    ->join('qa_certificate_inprocesses','qa_certificate_inprocesses.certificate_id','qa_certificate_codes.certificate_id')
    ->where('qa_certificate_codes.certificate_id',$certificate_id)
    ->get();

    $composition = DB::connection('ympimis_2')
    ->table('qa_certificate_compositions')->get();

    $remark_atas = [];

    array_push($remark_atas, 'Foreman Produksi');
    array_push($remark_atas, 'Foreman QA');
    array_push($remark_atas, 'Chief QA');

    $data_approval_atas = DB::connection('ympimis_2')
    ->table('qa_certificate_approvals')
    ->where('qa_certificate_approvals.certificate_id',$certificate_id)
    ->wherein('remark',$remark_atas)
    ->orderBy('id','desc')
    ->get();

    $remark_bawah = [];

    array_push($remark_bawah, 'Leader QA');
    array_push($remark_bawah, 'Staff QA');

    $data_approval_bawah = DB::connection('ympimis_2')
    ->table('qa_certificate_approvals')
    ->where('qa_certificate_approvals.certificate_id',$certificate_id)
    ->wherein('remark',$remark_bawah)
    ->orderBy('id','desc')
    ->get();

      // return view('qa.certificate_inprocess.print')->with('datas',$datas)->with('data_approval',$data_approval);

    $pdf = \App::make('dompdf.wrapper');
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper($datas[0]->certificate_paper, 'potrait');

    $pdf->loadView('qa.certificate_inprocess.print', array(
      'datas' => $datas,
      'data_approval_atas' => $data_approval_atas,
      'data_approval_bawah' => $data_approval_bawah,
      'composition' => $composition,
    ));

    $depan = $certificate_id.".pdf";

    $pdf->save(public_path() . "/data_file/qa/certificate_fix_inprocess/".$depan);

    $response = array(
      'status' => true,
      'message' => 'Update Data Succeeded'
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

public function indexCertificateCodeInprocess()
{
  $role = Auth::user()->role_code;

  $code_number = DB::CONNECTION('ympimis_2')
  ->select("SELECT
    a.product,
    a.description 
    FROM
    (
    SELECT DISTINCT
    (
    SPLIT_STRING ( certificate_name, ',', 1 )) AS product,
    SPLIT_STRING ( description, ',', 1 ) AS description 
    FROM
    qa_certificate_codes 
    WHERE
    `code` != 'I' 
    AND SPLIT_STRING ( certificate_name, ',', 1 ) != '' 
    AND SPLIT_STRING ( description, ',', 1 ) != '' UNION ALL
    SELECT DISTINCT
    (
    SPLIT_STRING ( certificate_name, ',', 1 )) AS product,
    SPLIT_STRING ( description, ',', 2 ) AS description 
    FROM
    qa_certificate_codes 
    WHERE
    `code` != 'I' 
    AND SPLIT_STRING ( certificate_name, ',', 1 ) != '' 
    AND SPLIT_STRING ( description, ',', 2 ) != '' UNION ALL
    SELECT DISTINCT
    (
    SPLIT_STRING ( certificate_name, ',', 2 )) AS product,
    SPLIT_STRING ( description, ',', 1 ) AS description 
    FROM
    qa_certificate_codes 
    WHERE
    `code` != 'I' 
    AND SPLIT_STRING ( certificate_name, ',', 2 ) != '' 
    AND SPLIT_STRING ( description, ',', 1 ) != '' UNION ALL
    SELECT DISTINCT
    (
    SPLIT_STRING ( certificate_name, ',', 2 )) AS product,
    SPLIT_STRING ( description, ',', 2 ) AS description 
    FROM
    qa_certificate_codes 
    WHERE
    `code` != 'I' 
    AND SPLIT_STRING ( certificate_name, ',', 2 ) != '' 
    AND SPLIT_STRING ( description, ',', 2 ) != '' 
    ) a 
    GROUP BY
    a.product,
    a.description");

  return view('qa.certificate_inprocess.index_code')
  ->with('title', 'QA Kensa Certificate Inprocess Monitoring')
  ->with('title_jp', '工程内検査認証のモニタリング')
  ->with('code_number', $code_number)
  ->with('role', $role)
  ->with('page', 'QA Kensa Certificate')
  ->with('jpn', '');
}

public function fetchCertificateCodeInprocess(Request $request)
{
  try {
    $status = $request->get('status');
    $code = $request->get('code');

    $datas = DB::connection('ympimis_2')
    ->table('qa_certificate_codes')
    ->select('qa_certificate_codes.*','a.certificate_code','a.certificate_desc','a.periode_from','a.periode_to','a.certificate_name as names')
    ->leftjoin(DB::RAW('(SELECT DISTINCT
      ( qa_certificate_inprocesses.certificate_id ),
      certificate_code,
      certificate_desc,
      certificate_name,
      periode_from,
      periode_to,
      `status` 
      FROM
      qa_certificate_inprocesses) as a'),function($join){
      $join->on('a.certificate_id', '=', 'qa_certificate_codes.certificate_id');
      $join->on('a.status','qa_certificate_codes.status');
    })
    ->leftjoin(DB::RAW('(SELECT DISTINCT
      ( qa_certificate_approvals.certificate_id ),
      approver_id,
      created_at,
      updated_at
      FROM
      qa_certificate_approvals
      where priority = 1) as b'),function($join){
      $join->on('b.certificate_id', '=', 'qa_certificate_codes.certificate_id');
    })
    ->orderBy('qa_certificate_codes.status','desc')
    ->orderBy('b.updated_at','desc');

    if ($status != '') {
      $datas = $datas->where('qa_certificate_codes.status',$status);
    }else{
      $datas = $datas->where('qa_certificate_codes.status','!=','0');
    }

    $datas = $datas->where('qa_certificate_codes.code','!=','I');

    $where_grafik = "";
    if ($code != '') {
      $datas = $datas->where('a.certificate_name',$code);
      $where_grafik = "WHERE a.product = '".$code."'";
    }

    $datas = $datas->get();

    $approvals = [];

    foreach ($datas as $key) {
      $approval = DB::connection('ympimis_2')
      ->select("SELECT
        qa_certificate_approvals.id,
        qa_certificate_approvals.certificate_id,
        qa_certificate_approvals.approver_id,
        qa_certificate_approvals.approver_name,
        qa_certificate_approvals.approver_email,
        qa_certificate_approvals.`approver_status`,
        qa_certificate_approvals.approved_at,
        qa_certificate_approvals.remark,
        CONCAT(
        DATE_FORMAT( qa_certificate_approvals.approved_at, '%d-%b-%Y' ),
        '<br>',
        DATE_FORMAT( qa_certificate_approvals.approved_at, '%H:%i:%s' )) AS approved_ats,
        'sudah' AS keutamaan,
        CONCAT(SPLIT_STRING(approver_name,' ',1),' ',SPLIT_STRING(approver_name,' ',2)) as approver_names,
        certificate_approval_id
        FROM
        `qa_certificate_approvals` 
        WHERE
        qa_certificate_approvals.certificate_id = '".$key->certificate_id."' 
        AND qa_certificate_approvals.`approver_status` IS NOT NULL UNION ALL
        (
        SELECT
        qa_certificate_approvals.id,
        qa_certificate_approvals.certificate_id,
        qa_certificate_approvals.approver_id,
        qa_certificate_approvals.approver_name,
        qa_certificate_approvals.approver_email,
        qa_certificate_approvals.`approver_status`,
        qa_certificate_approvals.approved_at,
        qa_certificate_approvals.remark,
        CONCAT(
        DATE_FORMAT( qa_certificate_approvals.approved_at, '%d-%b-%Y' ),
        '<br>',
        DATE_FORMAT( qa_certificate_approvals.approved_at, '%H:%i:%s' )) AS approved_ats,
        'utama' AS keutamaan,
        CONCAT(SPLIT_STRING(approver_name,' ',1),' ',SPLIT_STRING(approver_name,' ',2)) as approver_names,
        certificate_approval_id
        FROM
        `qa_certificate_approvals` 
        WHERE
        qa_certificate_approvals.certificate_id = '".$key->certificate_id."' 
        AND qa_certificate_approvals.`approver_status` IS NULL 
        LIMIT 1 
        ) UNION ALL
        (
        SELECT
        qa_certificate_approvals.id,
        qa_certificate_approvals.certificate_id,
        qa_certificate_approvals.approver_id,
        qa_certificate_approvals.approver_name,
        qa_certificate_approvals.approver_email,
        qa_certificate_approvals.`approver_status`,
        qa_certificate_approvals.approved_at,
        qa_certificate_approvals.remark,
        CONCAT(
        DATE_FORMAT( qa_certificate_approvals.approved_at, '%d-%b-%Y' ),
        '<br>',
        DATE_FORMAT( qa_certificate_approvals.approved_at, '%H:%i:%s' )) AS approved_ats,
        'belum' AS keutamaan,
        CONCAT(SPLIT_STRING(approver_name,' ',1),' ',SPLIT_STRING(approver_name,' ',2)) as approver_names,
        certificate_approval_id
        FROM
        `qa_certificate_approvals` 
        WHERE
        qa_certificate_approvals.certificate_id = '".$key->certificate_id."' 
        AND qa_certificate_approvals.`approver_status` IS NULL 
        AND qa_certificate_approvals.id != ( SELECT qa_certificate_approvals.id FROM `qa_certificate_approvals` WHERE qa_certificate_approvals.certificate_id = '".$key->certificate_id."' AND qa_certificate_approvals.`approver_status` IS NULL LIMIT 1 ))");

      array_push($approvals, $approval);
    }

    $charts = DB::connection('ympimis_2')
    ->select("SELECT
      a.product,
      a.description,
      ( SELECT count( DISTINCT ( certificate_id )) AS count FROM qa_certificate_inprocesses WHERE qa_certificate_inprocesses.`status` = 1 AND qa_certificate_inprocesses.certificate_name = a.product AND qa_certificate_inprocesses.certificate_desc = a.description ) AS active,
      ( SELECT count( DISTINCT ( certificate_id )) AS count FROM qa_certificate_inprocesses WHERE qa_certificate_inprocesses.`status` = 2 AND qa_certificate_inprocesses.certificate_name = a.product AND qa_certificate_inprocesses.certificate_desc = a.description) AS renewal,
      ( SELECT count( DISTINCT ( certificate_id )) AS count FROM qa_certificate_inprocesses WHERE qa_certificate_inprocesses.`status` = 3 AND qa_certificate_inprocesses.certificate_name = a.product AND qa_certificate_inprocesses.certificate_desc = a.description) AS expired 
      FROM
      (
      SELECT
      b.product,
      b.description 
      FROM
      (
      SELECT DISTINCT
      (
      SPLIT_STRING ( certificate_name, ',', 1 )) AS product,
      SPLIT_STRING ( description, ',', 1 ) AS description 
      FROM
      qa_certificate_codes 
      WHERE
      `code` != 'I' 
      AND SPLIT_STRING ( certificate_name, ',', 1 ) != '' 
      AND SPLIT_STRING ( description, ',', 1 ) != '' UNION ALL
      SELECT DISTINCT
      (
      SPLIT_STRING ( certificate_name, ',', 1 )) AS product,
      SPLIT_STRING ( description, ',', 2 ) AS description 
      FROM
      qa_certificate_codes 
      WHERE
      `code` != 'I' 
      AND SPLIT_STRING ( certificate_name, ',', 1 ) != '' 
      AND SPLIT_STRING ( description, ',', 2 ) != '' UNION ALL
      SELECT DISTINCT
      (
      SPLIT_STRING ( certificate_name, ',', 2 )) AS product,
      SPLIT_STRING ( description, ',', 1 ) AS description 
      FROM
      qa_certificate_codes 
      WHERE
      `code` != 'I' 
      AND SPLIT_STRING ( certificate_name, ',', 2 ) != '' 
      AND SPLIT_STRING ( description, ',', 1 ) != '' UNION ALL
      SELECT DISTINCT
      (
      SPLIT_STRING ( certificate_name, ',', 2 )) AS product,
      SPLIT_STRING ( description, ',', 2 ) AS description 
      FROM
      qa_certificate_codes 
      WHERE
      `code` != 'I' 
      AND SPLIT_STRING ( certificate_name, ',', 2 ) != '' 
      AND SPLIT_STRING ( description, ',', 2 ) != '' 
      ) b
      GROUP BY
      b.product,
      b.description
      ) a 
      ORDER BY
      active DESC,
      renewal DESC,
      expired DESC");

    $utamas = DB::connection('ympimis_2')->select("SELECT
      certificate_approval_id,
      GROUP_CONCAT( certificate_id ) AS certificate_id 
      FROM
      qa_certificate_approvals 
      WHERE
      priority = 1 
      GROUP BY
      certificate_approval_id");


    $response = array(
      'status' => true,
      'datas' => $datas,
      'approval' => $approvals,
      'charts' => $charts,
      'utamas' => $utamas,
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

public function approvalCertificateInprocess($remark)
{

  if (str_contains(Auth::user()->role_code,'MIS')) {
    $users = "";
  }else{
    $users = "AND approver_id = '".Auth::user()->username."' ";
  }

  $approval_now = DB::CONNECTION('ympimis_2')
  ->select("SELECT
        *,
    ( SELECT DISTINCT ( CONCAT( periode_from, '_', periode_to, '_', certificate_desc )) FROM qa_certificate_inprocesses WHERE qa_certificate_inprocesses.certificate_id = qa_certificate_approvals.certificate_id ) AS periode 
    FROM
    `qa_certificate_approvals`
    JOIN qa_certificate_codes ON qa_certificate_codes.certificate_id = qa_certificate_approvals.certificate_id 
    WHERE
    qa_certificate_approvals.remark = '".$remark."' 
    ".$users." 
    and qa_certificate_approvals.priority = 1
    AND qa_certificate_approvals.approver_status IS NULL
    AND qa_certificate_codes.`code` != 'I'");

  return view('qa.certificate_inprocess.approval')
  ->with('approval_now',$approval_now)
  ->with('remark',$remark)
  ->with('title', 'QA Kensa Certificate Approval')
  ->with('title_jp', '品質保証検査認定承認')
  ->with('page', 'QA Kensa Certificate Approval')
  ->with('jpn', '品質保証検査認定承認');
}

public function certificateApprovalInprocess(Request $request)
{
  try {
    $certificate_id = [];
    if ($request->get('remark') == 'Leader QA') {
      $code_generator = CodeGenerator::where('note', '=', 'qa_certificate_approval')->first();
      $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

      $serial_number = $code_generator->prefix.$numbers;

      $code_generator->index = $code_generator->index+1;

      $certificate_id = $request->get('certificate_id');

      $data_all = [];

      for ($i=0; $i < count($certificate_id); $i++) { 
        $next = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('remark','Staff QA')
        ->where('certificate_id',$certificate_id[$i])
        ->first();

        $appr_code = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('certificate_id',$certificate_id[$i])
        ->update([
          'certificate_approval_id' => $serial_number
        ]);

        $mail_to = $next->approver_email;

        $next = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('remark','Staff QA')
        ->where('certificate_id',$certificate_id[$i])
        ->update([
          'priority' => 1,
          'updated_at' => date('Y-m-d H:i:s')
        ]);

        $certificate_lama_update = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('certificate_id',$certificate_id[$i])
        ->where('remark',$request->get('remark'))
        ->update([
          'approver_status' => "Approved",
          'approved_at' => date('Y-m-d H:i:s'),
          'priority' => null,
          'updated_at' => date('Y-m-d H:i:s')
        ]);

        $next_remark = 'Staff QA';

        $datas = DB::connection('ympimis_2')
        ->table('qa_certificate_inprocesses')
        ->where('certificate_id',$certificate_id[$i])
        ->first();

        $datas_nilai = DB::connection('ympimis_2')
        ->select("SELECT DISTINCT
          ( a.`certificate_id` ),
          ( SELECT GROUP_CONCAT( DISTINCT ( CONCAT( category, '_', presentase_total ))) FROM qa_certificate_inprocesses WHERE certificate_id = a.certificate_id ) AS result,
          GROUP_CONCAT(
          DISTINCT ( decision )) AS decision 
          FROM
          `qa_certificate_inprocesses` AS a 
          WHERE
          a.certificate_id = '".$certificate_id[$i]."' 
          GROUP BY
          a.certificate_id");

        $data = array(
          'datas' => $datas,
          'datas_nilai' => $datas_nilai,
          'certificate_id' => $certificate_id[$i],
          'remark' => $request->get('remark'),
          'next_remark' => $next_remark
        );
        array_push($data_all, $data);
      }

      $code_generator->save();

      Mail::to($mail_to)
      ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
      ->send(new SendEmail($data_all, 'qa_certificate_collective_inprocess'));
    }

    if ($request->get('remark') == 'Staff QA') {
      $certificate_id = $request->get('certificate_id');

      $data_all_qa = [];
      $mail_to_qa = [];
      $mail_to_prod = [];
      $data_all_prod = [];

      for ($i=0; $i < count($certificate_id); $i++) { 
        $datas = DB::connection('ympimis_2')
        ->table('qa_certificate_inprocesses')
        ->where('certificate_id',$certificate_id[$i])
        ->first();
        if ($datas->approval_type == 'PRODUKSI') {
          $next = DB::connection('ympimis_2')
          ->table('qa_certificate_approvals')
          ->where('remark','Foreman Produksi')
          ->where('certificate_id',$certificate_id[$i])
          ->first();

          if (!$next) {
            $next = DB::connection('ympimis_2')
            ->table('qa_certificate_approvals')
            ->where('remark','Chief Produksi')
            ->where('certificate_id',$certificate_id[$i])
            ->first();

            $next_update = DB::connection('ympimis_2')
            ->table('qa_certificate_approvals')
            ->where('remark','Chief Produksi')
            ->where('certificate_id',$certificate_id[$i])
            ->update([
              'priority' => 1,
              'updated_at' => date('Y-m-d H:i:s')
            ]);
          }else{
            $next_update = DB::connection('ympimis_2')
            ->table('qa_certificate_approvals')
            ->where('remark','Foreman Produksi')
            ->where('certificate_id',$certificate_id[$i])
            ->update([
              'priority' => 1,
              'updated_at' => date('Y-m-d H:i:s')
            ]);
          }

          array_push($mail_to_prod, $next->approver_email);

          $certificate_lama_update = DB::connection('ympimis_2')
          ->table('qa_certificate_approvals')
          ->where('certificate_id',$certificate_id[$i])
          ->where('remark',$request->get('remark'))
          ->update([
            'approver_status' => "Approved",
            'approved_at' => date('Y-m-d H:i:s'),
            'priority' => null,
            'updated_at' => date('Y-m-d H:i:s')
          ]);

          $next_remark = $next->remark;

          $datas = DB::connection('ympimis_2')
          ->table('qa_certificate_inprocesses')
          ->where('certificate_id',$certificate_id[$i])
          ->first();

          $datas_nilai = DB::connection('ympimis_2')
          ->select("SELECT DISTINCT
            ( a.`certificate_id` ),
            ( SELECT GROUP_CONCAT( DISTINCT ( CONCAT( category, '_', presentase_total ))) FROM qa_certificate_inprocesses WHERE certificate_id = a.certificate_id ) AS result,
            GROUP_CONCAT(
            DISTINCT ( decision )) AS decision 
            FROM
            `qa_certificate_inprocesses` AS a 
            WHERE
            a.certificate_id = '".$certificate_id[$i]."' 
            GROUP BY
            a.certificate_id");

          $data = array(
            'datas' => $datas,
            'datas_nilai' => $datas_nilai,
            'certificate_id' => $certificate_id[$i],
            'remark' => $request->get('remark'),
            'next_remark' => $next_remark
          );
          array_push($data_all_prod, $data);
        }else{
          $next = DB::connection('ympimis_2')
          ->table('qa_certificate_approvals')
          ->where('remark','Foreman QA')
          ->where('certificate_id',$certificate_id[$i])
          ->first();

          $next_update = DB::connection('ympimis_2')
          ->table('qa_certificate_approvals')
          ->where('remark','Foreman QA')
          ->where('certificate_id',$certificate_id[$i])
          ->update([
            'priority' => 1,
            'updated_at' => date('Y-m-d H:i:s')
          ]);

          array_push($mail_to_qa, $next->approver_email);

          $certificate_lama_update = DB::connection('ympimis_2')
          ->table('qa_certificate_approvals')
          ->where('certificate_id',$certificate_id[$i])
          ->where('remark',$request->get('remark'))
          ->update([
            'approver_status' => "Approved",
            'approved_at' => date('Y-m-d H:i:s'),
            'priority' => null,
            'updated_at' => date('Y-m-d H:i:s')
          ]);

          $next_remark = $next->remark;

          $datas = DB::connection('ympimis_2')
          ->table('qa_certificate_inprocesses')
          ->where('certificate_id',$certificate_id[$i])
          ->first();

          $datas_nilai = DB::connection('ympimis_2')
          ->select("SELECT DISTINCT
            ( a.`certificate_id` ),
            ( SELECT GROUP_CONCAT( DISTINCT ( CONCAT( category, '_', presentase_total ))) FROM qa_certificate_inprocesses WHERE certificate_id = a.certificate_id ) AS result,
            GROUP_CONCAT(
            DISTINCT ( decision )) AS decision 
            FROM
            `qa_certificate_inprocesses` AS a 
            WHERE
            a.certificate_id = '".$certificate_id[$i]."' 
            GROUP BY
            a.certificate_id");

          $data = array(
            'datas' => $datas,
            'datas_nilai' => $datas_nilai,
            'certificate_id' => $certificate_id[$i],
            'remark' => $request->get('remark'),
            'next_remark' => $next_remark
          );
          array_push($data_all_qa, $data);
        }
      }

      if (count($data_all_prod) > 0) {

        $code_generator = CodeGenerator::where('note', '=', 'qa_certificate_approval')->first();
        $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

        $serial_number = $code_generator->prefix.$numbers;

        $code_generator->index = $code_generator->index+1;
        for ($i=0; $i < count($data_all_prod); $i++) { 
          $appr_code = DB::connection('ympimis_2')
          ->table('qa_certificate_approvals')
          ->where('certificate_id',$data_all_prod[$i]['certificate_id'])
          ->update([
            'certificate_approval_id' => $serial_number
          ]);              
        }

        $code_generator->save();

        Mail::to($mail_to_prod)
        ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
        ->send(new SendEmail($data_all_prod, 'qa_certificate_collective_inprocess'));
      }

      if (count($data_all_qa) > 0) {

        $code_generator = CodeGenerator::where('note', '=', 'qa_certificate_approval')->first();
        $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

        $serial_number = $code_generator->prefix.$numbers;

        $code_generator->index = $code_generator->index+1;
        for ($i=0; $i < count($data_all_qa); $i++) { 
          $appr_code = DB::connection('ympimis_2')
          ->table('qa_certificate_approvals')
          ->where('certificate_id',$data_all_qa[$i]['certificate_id'])
          ->update([
            'certificate_approval_id' => $serial_number
          ]);              
        }

        $code_generator->save();
        Mail::to($mail_to_qa)
        ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
        ->send(new SendEmail($data_all_qa, 'qa_certificate_collective_inprocess'));
      }
    }

    if ($request->get('remark') == 'Foreman Produksi' || $request->get('remark') == 'Chief Produksi') {
      $certificate_id = $request->get('certificate_id');

      $data_all = [];

      $code_generator = CodeGenerator::where('note', '=', 'qa_certificate_approval')->first();
      $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

      $serial_number = $code_generator->prefix.$numbers;

      $code_generator->index = $code_generator->index+1;

      for ($i=0; $i < count($certificate_id); $i++) { 
        $next = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('remark','Foreman QA')
        ->where('certificate_id',$certificate_id[$i])
        ->first();

        $next_update = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('remark','Foreman QA')
        ->where('certificate_id',$certificate_id[$i])
        ->update([
          'priority' => 1,
          'updated_at' => date('Y-m-d H:i:s')
        ]);

        $mail_to = $next->approver_email;

        $appr_code = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('certificate_id',$certificate_id[$i])
        ->update([
          'certificate_approval_id' => $serial_number
        ]);

        $certificate_lama_update = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('certificate_id',$certificate_id[$i])
        ->where('remark',$request->get('remark'))
        ->update([
          'approver_status' => "Approved",
          'approved_at' => date('Y-m-d H:i:s'),
          'priority' => null,
          'updated_at' => date('Y-m-d H:i:s')
        ]);

        $next_remark = $next->remark;

        $datas = DB::connection('ympimis_2')
        ->table('qa_certificate_inprocesses')
        ->where('certificate_id',$certificate_id[$i])
        ->first();

        $datas_nilai = DB::connection('ympimis_2')
        ->select("SELECT DISTINCT
          ( a.`certificate_id` ),
          ( SELECT GROUP_CONCAT( DISTINCT ( CONCAT( category, '_', presentase_total ))) FROM qa_certificate_inprocesses WHERE certificate_id = a.certificate_id ) AS result,
          GROUP_CONCAT(
          DISTINCT ( decision )) AS decision 
          FROM
          `qa_certificate_inprocesses` AS a 
          WHERE
          a.certificate_id = '".$certificate_id[$i]."' 
          GROUP BY
          a.certificate_id");

        $data = array(
          'datas' => $datas,
          'datas_nilai' => $datas_nilai,
          'certificate_id' => $certificate_id[$i],
          'remark' => $request->get('remark'),
          'next_remark' => $next_remark
        );
        array_push($data_all, $data);
      }

      $code_generator->save();

      Mail::to($mail_to)
      ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
      ->send(new SendEmail($data_all, 'qa_certificate_collective_inprocess'));
    }

    if ($request->get('remark') == 'Foreman QA') {
      $certificate_id = $request->get('certificate_id');

      $data_all = [];

      $code_generator = CodeGenerator::where('note', '=', 'qa_certificate_approval')->first();
      $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

      $serial_number = $code_generator->prefix.$numbers;

      $code_generator->index = $code_generator->index+1;

      for ($i=0; $i < count($certificate_id); $i++) { 
        $next = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('remark','Chief QA')
        ->where('certificate_id',$certificate_id[$i])
        ->first();

        $next_update = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('remark','Chief QA')
        ->where('certificate_id',$certificate_id[$i])
        ->update([
          'priority' => 1,
          'updated_at' => date('Y-m-d H:i:s')
        ]);

        $mail_to = $next->approver_email;

        $appr_code = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('certificate_id',$certificate_id[$i])
        ->update([
          'certificate_approval_id' => $serial_number
        ]);

        $certificate_lama_update = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('certificate_id',$certificate_id[$i])
        ->where('remark',$request->get('remark'))
        ->where('priority',1)
        ->update([
          'approver_status' => "Approved",
          'approved_at' => date('Y-m-d H:i:s'),
          'priority' => null,
          'updated_at' => date('Y-m-d H:i:s')
        ]);

        $next_remark = $next->remark;

        $datas = DB::connection('ympimis_2')
        ->table('qa_certificate_inprocesses')
        ->where('certificate_id',$certificate_id[$i])
        ->first();

        $datas_nilai = DB::connection('ympimis_2')
        ->select("SELECT DISTINCT
          ( a.`certificate_id` ),
          ( SELECT GROUP_CONCAT( DISTINCT ( CONCAT( category, '_', presentase_total ))) FROM qa_certificate_inprocesses WHERE certificate_id = a.certificate_id ) AS result,
          GROUP_CONCAT(
          DISTINCT ( decision )) AS decision 
          FROM
          `qa_certificate_inprocesses` AS a 
          WHERE
          a.certificate_id = '".$certificate_id[$i]."' 
          GROUP BY
          a.certificate_id");

        $data = array(
          'datas' => $datas,
          'datas_nilai' => $datas_nilai,
          'certificate_id' => $certificate_id[$i],
          'remark' => $request->get('remark'),
          'next_remark' => $next_remark
        );
        array_push($data_all, $data);
      }

      $code_generator->save();

      Mail::to($mail_to)
      ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
      ->send(new SendEmail($data_all, 'qa_certificate_collective_inprocess'));
    }

    if ($request->get('remark') == 'Chief QA') {
      $certificate_id = $request->get('certificate_id');

      $data_all = [];

      $code_generator = CodeGenerator::where('note', '=', 'qa_certificate_approval')->first();
      $numbers = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

      $serial_number = $code_generator->prefix.$numbers;

      $code_generator->index = $code_generator->index+1;

      for ($i=0; $i < count($certificate_id); $i++) { 
        $next = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('remark','Staff QA')
        ->where('certificate_id',$certificate_id[$i])
        ->first();

        $mail_to = $next->approver_email;

        $appr_code = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('certificate_id',$certificate_id[$i])
        ->update([
          'certificate_approval_id' => $serial_number
        ]);

        $certificate_lama_update = DB::connection('ympimis_2')
        ->table('qa_certificate_approvals')
        ->where('certificate_id',$certificate_id[$i])
        ->where('remark',$request->get('remark'))
        ->where('priority',1)
        ->update([
          'approver_status' => "Approved",
          'approved_at' => date('Y-m-d H:i:s'),
          'priority' => null,
          'updated_at' => date('Y-m-d H:i:s')
        ]);

        $next_remark = $next->remark;

        $datas = DB::connection('ympimis_2')
        ->table('qa_certificate_inprocesses')
        ->where('certificate_id',$certificate_id[$i])
        ->first();

        $datas_nilai = DB::connection('ympimis_2')
        ->select("SELECT DISTINCT
          ( a.`certificate_id` ),
          ( SELECT GROUP_CONCAT( DISTINCT ( CONCAT( category, '_', presentase_total ))) FROM qa_certificate_inprocesses WHERE certificate_id = a.certificate_id ) AS result,
          GROUP_CONCAT(
          DISTINCT ( decision )) AS decision 
          FROM
          `qa_certificate_inprocesses` AS a 
          WHERE
          a.certificate_id = '".$certificate_id[$i]."' 
          GROUP BY
          a.certificate_id");

        $data = array(
          'datas' => $datas,
          'datas_nilai' => $datas_nilai,
          'certificate_id' => $certificate_id[$i],
          'remark' => $request->get('remark'),
          'next_remark' => $next_remark,
          'complete' => 'complete',
        );
        array_push($data_all, $data);
      }

      $code_generator->save();

      Mail::to($mail_to)
      ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
      ->send(new SendEmail($data_all, 'qa_certificate_collective_inprocess'));
    }

    // for ($i=0; $i < count($certificate_id); $i++) { 
    //   $datas = DB::connection('ympimis_2')
    //   ->table('qa_certificate_codes')
    //   ->join('qa_certificate_inprocesses','qa_certificate_inprocesses.certificate_id','qa_certificate_codes.certificate_id')
    //   ->where('qa_certificate_codes.certificate_id',$certificate_id[$i])
    //   ->get();

    //   $composition = DB::connection('ympimis_2')
    //   ->table('qa_certificate_compositions')->get();

    //   $remark_atas = [];

    //   array_push($remark_atas, 'Foreman Produksi');
    //   array_push($remark_atas, 'Foreman QA');
    //   array_push($remark_atas, 'Chief QA');

    //   $data_approval_atas = DB::connection('ympimis_2')
    //   ->table('qa_certificate_approvals')
    //   ->where('qa_certificate_approvals.certificate_id',$certificate_id[$i])
    //   ->wherein('remark',$remark_atas)
    //   ->orderBy('id','desc')
    //   ->get();

    //   $remark_bawah = [];

    //   array_push($remark_bawah, 'Leader QA');
    //   array_push($remark_bawah, 'Staff QA');

    //   $data_approval_bawah = DB::connection('ympimis_2')
    //   ->table('qa_certificate_approvals')
    //   ->where('qa_certificate_approvals.certificate_id',$certificate_id[$i])
    //   ->wherein('remark',$remark_bawah)
    //   ->orderBy('id','desc')
    //   ->get();


    //   $pdf = \App::make('dompdf.wrapper');
    //   $pdf->getDomPDF()->set_option("enable_php", true);
    //   $pdf->setPaper($datas[0]->certificate_paper, 'potrait');

    //   $pdf->loadView('qa.certificate_inprocess.print', array(
    //     'datas' => $datas,
    //     'data_approval_atas' => $data_approval_atas,
    //     'data_approval_bawah' => $data_approval_bawah,
    //     'composition' => $composition,
    //   ));

    //   $depan = $certificate_id[$i].".pdf";

    //   $pdf->save(public_path() . "/data_file/qa/certificate_fix_inprocess/".$depan);

    //     // return $pdf->stream($datas[0]->certificate_code.' ('.$datas[0]->employee_id.' - '.$datas[0]->name.").pdf");

    //     // $pathForTheMergedPdf = public_path() . "/data_file/qa/certificate_fix/".$certificate_id[$i].".pdf";

    //     // file_put_contents($pathForTheMergedPdf, $createdPdf);
    // }

    $response = array(
      'status' => true,
      'message' => 'Success Approve Certificate<br>認定承認済み'
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

public function approvalAllCertificateInprocess($remark,$certificate_id)
{
  $certid = explode(',', $certificate_id);
  $certids = [];
  $cer_no = 0;;
  for ($i=0; $i < count($certid); $i++) { 
    $approval_now = DB::CONNECTION('ympimis_2')
    ->table("qa_certificate_approvals")
    ->where('certificate_id',$certid[$i])
    ->where('remark',$remark)
    ->where('approver_status',null)
    ->first();
    if (count($approval_now) > 0) {
      $cer_no++;
    }
    array_push($certids, $certid[$i]);
  }
  return view('qa.certificate_inprocess.approval_all')
  ->with('certificate_id',$certids)
  ->with('remark',$remark)
  ->with('cer_no',$cer_no)
  ->with('head','QA Kensa Certificate Inprocess Approval')
  ->with('sub_head','(品質保証検査認定承認)')
  ->with('message','The Certificate Has Been Approved')
  ->with('sub_message','(すでに承認済み)')
  ->with('title', 'QA Kensa Certificate Inprocess Approval')
  ->with('title_jp', '品質保証検査認定承認')
  ->with('page', 'QA Kensa Certificate Inprocess Approval')
  ->with('jpn', '品質保証検査認定承認');
}

public function rejectCertificateInprocess($remark,$certificate_id)
{
  $certid = explode(',', $certificate_id);
  $certids = [];
  $data_all = [];
  $mail_to = '';
  for ($i=0; $i < count($certid); $i++) { 
    $approval_now = DB::CONNECTION('ympimis_2')
    ->table("qa_certificate_approvals")
    ->where('certificate_id',$certid[$i])
    ->update([
      'approver_status' => null,
      'approved_at' => null,
      'priority' => null,
    ]);
    $approval_leader = DB::CONNECTION('ympimis_2')
    ->table("qa_certificate_approvals")
    ->where('certificate_id',$certid[$i])
    ->where('remark','Leader QA')
    ->update([
      'priority' => 1,
    ]);

    $approval_reject = DB::CONNECTION('ympimis_2')
    ->table("qa_certificate_approvals")
    ->where('certificate_id',$certid[$i])
    ->where('remark',$remark)
    ->first();

    $approval_staff = DB::CONNECTION('ympimis_2')
    ->table("qa_certificate_approvals")
    ->where('certificate_id',$certid[$i])
    ->where('remark','Staff QA')
    ->first();

    $datas = DB::connection('ympimis_2')
    ->table('qa_certificate_inprocesses')
    ->where('certificate_id',$certid[$i])
    ->first();

    $datas_nilai = DB::connection('ympimis_2')
    ->select("SELECT DISTINCT
      ( a.`certificate_id` ),
      ( SELECT GROUP_CONCAT( DISTINCT ( CONCAT( category, '_', presentase_total ))) FROM qa_certificate_inprocesses WHERE certificate_id = a.certificate_id ) AS result,
      GROUP_CONCAT(
      DISTINCT ( decision )) AS decision 
      FROM
      `qa_certificate_inprocesses` AS a 
      WHERE
      a.certificate_id = '".$certid[$i]."' 
      GROUP BY
      a.certificate_id");

    $data = array(
      'datas' => $datas,
      'datas_nilai' => $datas_nilai,
      'certificate_id' => $certid[$i],
      'remark' => $remark,
      'next_remark' => 'Staff QA',
      'reject' => 'reject',
      'reject_by' => $approval_reject
    );
    array_push($data_all, $data);
    $mail_to = $approval_staff->approver_email;
  }

  if ($remark != 'Staff QA') {
    Mail::to($mail_to)
    ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])
    ->send(new SendEmail($data_all, 'qa_certificate_collective_inprocess'));
  }
  return view('qa.certificate.reject')
  ->with('head','QA Kensa Certificate Rejection')
  ->with('sub_head','(品質保証検査認証の拒否)')
  ->with('message','The Certificate Has Been Rejected')
  ->with('sub_message','(拒否された)')
  ->with('title', 'QA Kensa Certificate Rejection')
  ->with('title_jp', '品質保証検査認証の拒否')
  ->with('page', 'QA Kensa Certificate Rejection')
  ->with('jpn', '品質保証検査認証の拒否');
}
//SERTIFIKAT KENSA QA//


public function indexDocumentSDS(){
  $title = 'SAFETY DATA SHEET MONITORING';
  $gmc = [];

  $gmcs = DB::connection('ympimis_2')
  ->table('sds_masters')
  ->whereNull('deleted_at')
  ->orderBy('gmc_material', 'ASC')
  ->get();

  $item_data = AccItem::select('kode_item','deskripsi')->orwhere('kategori','=','A006')->orWhere('kategori','=','C004')->orWhere('kategori','=','I017')->orWhere('kategori','=','G001')->get();

  for ($i=0; $i < count($gmcs); $i++) { 
    array_push($gmc, [$gmcs[$i]->gmc_material,$gmcs[$i]->decription_material,'GMC']);
  }
  for ($j=0; $j < count($item_data); $j++) { 
    array_push($gmc, [$item_data[$j]->kode_item,$item_data[$j]->deskripsi,'EQ']);
  }

  $role_user = User::where('username', Auth::user()->username)->first();

  return view('chemical.document_sds_index', array(
    'title' => $title,
    'gmc' => $gmc,
    'item_data' => $item_data,
    'role_user' => $role_user,
    'role_code' => Auth::user()->role_code

  ))->with('page', 'SDS Monitoring');;
}


public function inputSdsDocument(Request $request){
  try{
    $code_generator = CodeGenerator::where('note','=','sds_pdf')->first();
    $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
    $document_id = "SDS".$number;

    $no = '1 Year';

    $valid_from = date('Y-m-d', strtotime($request->input('version_date')));

    $valid_to = date('Y-m-d', strtotime('+' . $no, strtotime($valid_from)));


    $file_destination = 'files/chemical/documents';

    if ($request->file('attachment_sds_asli') != null) {  
      $file_pdf = $request->file('attachment_sds_asli');
      $filename_asli = 'SDS_Asli_'.$request->input("version").'_'.$document_id.'.'.$request->input('extension_asli');

      $file_pdf->move($file_destination, $filename_asli);
    }else{
      $filename_asli = null;
    }

    if ($request->file('attachment_new') != null) {
      $file_new = $request->file('attachment_new');
      $filename_new = 'SDS_YMPI_'.$request->input("version").'_'.$document_id.'.'.$request->input('extension_new');

      $file_new->move($file_destination, $filename_new);
    }else{
      $filename_new = null;
    }

    $insert_document_attachment = db::connection('ympimis_2')->table('safety_data_sheets')
    ->insert([
      'document_id' => $document_id,
      'gmc_material' => $request->input("createGmc"),
      'desc_material' => $request->input("desc_material"),
      'title' => $request->input("title"),
      'distribusi' => $request->input("loc"),
      'version' => $request->input("version"),
      'version_date_ympi' => $request->input("version_date_ympi"),
      'last_date_ympi' => $request->input("last_date_ympi"),
      'file_name_sds' => $filename_new,
      'version_date_asli' => $request->input("version_date_asli"),
      'last_date_asli' => $request->input("last_date_asli"),
      'file_name_asli' => $filename_asli,
      'status' => 'Active',
      'created_by' => Auth::user()->username,
      'created_by_name' => Auth::user()->name,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s')
    ]);

    if ($request->file('attachment_sds_asli') != null || $request->file('attachment_new') != null) {
      $insert_document_attachment = db::connection('ympimis_2')->table('document_attachments')
      ->insert([
        'document_id' => $document_id,
        'version' => $request->input("version"),
        'version_date' => $request->input("version_date_ympi"),
        'file_name_pdf' => $filename_asli,
        'file_name_xls' => $filename_new,
        'created_by' => Auth::user()->username,
        'created_by_name' => Auth::user()->name,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
      ]);
    }

    $code_generator->index = $code_generator->index+1;
    $code_generator->save();

    $response = array(
      'status' => true,
      'message' => 'Dokumen baru berhasil tersimpan'
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

public function deleteSDS(Request $request){
  try{
    $path = public_path().'/files/chemical/documents/';
    $sds_attachments =  db::connection('ympimis_2')->table('document_attachments')
    ->where('document_id', '=', $request->get('sds_id'))
    ->get();

    for ($i=0; $i < count($sds_attachments); $i++) { 
     $file_old = $path.$sds_attachments[$i]->file_name_pdf;
     unlink($file_old);

     $file_old_new = $path.$sds_attachments[$i]->file_name_xls;
     unlink($file_old_new);

     $delete_data_sds = db::connection('ympimis_2')->table('document_attachments')
     ->where('id', '=', $sds_attachments[$i]->id)->delete();
   }

   $sds_attachment2 =  db::connection('ympimis_2')->table('document_attachments')
   ->where('document_id', '=', $request->get('sds_id'))
   ->first();

   if ($sds_attachment2 == null) {
     $delete_data_sds = db::connection('ympimis_2')->table('safety_data_sheets')
     ->where('document_id', '=', $request->get('sds_id'))->delete();
   }

   $response = array(
    'status' => true,
    'message' => 'SDS has been deleted.'
  );
   return Response::json($response);
 }
 catch(\Exception $e){
  $response = array(
    'status' => false,
    'message' => $e->getMessage()
  );
  return Response::json($response);
}
}

public function fetchDocumentSDS(){

  $date_now = date('Y-m-d', strtotime(carbon::now()));
  $user = EmployeeSync::where('employee_id',Auth::user()->username)->first();

  $sds_ex = db::connection('ympimis_2')->select("SELECT document_id,
    IFNULL( version_date_asli, '' ) AS version_date_asli,
    IFNULL( version_date_ympi, '' ) AS version_date_ympi,
    IFNULL( last_date_asli, '' ) AS last_date_asli,
    IFNULL( last_date_ympi, '' ) AS last_date_ympi,
    IFNULL( TIMESTAMPDIFF( DAY, now(), last_date_asli ), '' ) AS diff_asli,
    IFNULL( TIMESTAMPDIFF( DAY, now(), last_date_ympi ), '' ) AS diff_ympi,
    IFNULL( DATE_FORMAT( DATE_SUB( last_date_asli, INTERVAL 60 DAY ), '%Y-%m-01' ), '' ) AS month_reminder,
    IFNULL( DATE_FORMAT( DATE_SUB( last_date_ympi, INTERVAL 60 DAY ), '%Y-%m-01' ), '' ) AS month_reminder_ympi 
    FROM
    `safety_data_sheets`");

  $documents = db::connection('ympimis_2')->select('select *,
    IFNULL( DATE_FORMAT( DATE_SUB( last_date_ympi, INTERVAL 60 DAY ), "%Y-%m-01" ), "" ) AS month_reminder_ympi,IFNULL( DATE_FORMAT( DATE_SUB( last_date_asli, INTERVAL 60 DAY ), "%Y-%m-01" ), "" ) AS month_reminder_asli,IFNULL( TIMESTAMPDIFF( DAY, now(), last_date_asli ), "" ) AS diff_asli,IFNULL( TIMESTAMPDIFF( DAY, now(), last_date_ympi ), "" ) AS diff_ympi,DATEDIFF(last_date_asli,now()) as hari_asli,DATEDIFF(last_date_ympi,now()) as hari_ympi from safety_data_sheets ORDER BY updated_at DESC');

  $document_attachments_sds = db::connection('ympimis_2')->table('document_attachments')
  ->whereNull('deleted_at')
  ->orderBy('version', 'DESC')
  ->get();

  $masa_exp = db::connection('ympimis_2')->select('select document_id,gmc_material,desc_material,title,version,file_name_sds,last_date_asli,last_date_ympi,DATEDIFF(last_date_asli,now()) as hari_asli,DATEDIFF(last_date_ympi,now()) as hari_ympi, DATE_FORMAT(last_date_ympi, "%Y-%m" ) AS month_name_ympi,DATE_FORMAT(last_date_asli, "%Y-%m" ) AS month_name_asli,DATE_FORMAT(now(),"%Y-%m") as month_now from safety_data_sheets where DATEDIFF(last_date_asli,now()) <= 60 GROUP BY DATEDIFF(last_date_asli,now()),document_id,hari_asli,hari_ympi,gmc_material,desc_material,title,version,file_name_sds,last_date_asli,last_date_ympi,month_name_ympi,month_name_asli,month_now');

  $get_month = DB::SELECT('SELECT DATE_FORMAT(week_date,"%Y-%m") as mon FROM `weekly_calendars`
    where DATE_FORMAT(week_date,"%Y-%m") >= DATE_FORMAT(now(),"%Y-%m")
    and DATE_FORMAT(week_date,"%Y-%m") <= DATE_FORMAT(DATE_ADD(now(), INTERVAL 2 MONTH),"%Y-%m")
    group by DATE_FORMAT(week_date,"%Y-%m")');

  $response = array(
    'status' => true,
    'documents_sds' => $documents,
    'document_attachments_sds' => $document_attachments_sds,
    'massa_exp' => $masa_exp,
    'get_month' => $get_month,
    'sds_ex' => $sds_ex,
    'user' => $user

  );
  return Response::json($response);

}

public function mailExpaired(){
  $data_expireds = db::connection('ympimis_2')->select("SELECT
    id,
    document_id,
    gmc_material,
    desc_material,
    title,
    version,
    version_date_asli,
    last_date_asli,
    version_date_ympi,   
    last_date_ympi,
    file_name_sds,
    file_name_asli,
    remark,
    status,
    TIMESTAMPDIFF( DAY, now(), last_date_asli ) AS diff,
    TIMESTAMPDIFF( DAY, now(), last_date_ympi ) AS diff_ympi,
    created_by_name,
    created_by,
    deleted_at,
    created_at,
    updated_at 
    FROM
    safety_data_sheets 
    WHERE
    deleted_at IS NULL
    AND TIMESTAMPDIFF( DAY, now(), last_date_asli ) <= 60");

  $expireds = [];

  foreach($data_expireds as $data1){
   array_push($expireds, $data1->document_id);   
 }

 $expa = join(",",$expireds);

 if(count($data_expireds) > 0){

  $data = [
    'datas' => $data_expireds,
    'expa' => $expa,
    'title' => 'Rimender SDS ASLI Expaired'
  ];

  $mail_to_success = [];
  array_push($mail_to_success, 'shega.erik.wicaksono@music.yamaha.com');
  array_push($mail_to_success, 'hanin.hamidi@music.yamaha.com');

  Mail::to($mail_to_success)
  ->bcc('lukman.hakim.saputra@music.yamaha.com','whica.parama@music.yamaha.com','priyo.jatmiko@music.yamaha.com')
  ->send(new SendEmail($data, 'notifikasi_sds'));
}
}


public function fetchSDSMonitoring(Request $request){
  $document_id = $request->get('document_ids');
    // $date_now = date('Y-m-d', strtotime(carbon::now()));
  $detail_sds = [];

  for ($i=0; $i < count($document_id) ; $i++) { 
    $detail_modal = db::connection('ympimis_2')->select('select document_id, gmc_material,last_date from safety_data_sheets where document_id = "'.$document_id[$i].'"');
    array_push($detail_sds,$detail_modal);
  }

  $response = array(
    'status' => true,
    'detail_modal' => $detail_sds
  );
  return Response::json($response);
}


public function versionDocumentSDS(Request $request){
  try{


    $document_id = $request->input('document_id');
    $document_attachment = db::connection('ympimis_2')->table('document_attachments')
    ->where('version', '=', $request->input('version'))
    ->where('document_id', '=', $document_id)
    ->first();

    $file_destination = 'files/chemical/documents';

    $file_ympi = $request->file('attachment_new');
    $filename_ympi = 'SDS_YMPI_'.$request->input("version").'_'.$request->input('document_id').'.'.$request->input('extension_new');
    $file_ympi->move($file_destination, $filename_ympi);

    $filename_asli = "";
    if ($document_attachment != null) {
      $filename_asli = $document_attachment->file_name_pdf;
    }

    if (count($request->file('attachment_sds_asli')) > 0) {
      $file_asli = $request->file('attachment_sds_asli');
      $filename_asli = 'SDS_Asli_'.$request->input("version").'_'.$request->input('document_id').'.'.$request->input('extension_asli');
      $file_asli->move($file_destination, $filename_asli);
    }

    if ($document_attachment != null) {
      $update_document_attachment = db::connection('ympimis_2')->table('document_attachments')
      ->where('version', '=', $request->input('version'))
      ->where('document_id', '=', $document_id)
      ->update([
        'file_name_pdf' => $filename_asli,
        'file_name_xls' => $filename_ympi,
        'created_by' => Auth::user()->username,
        'created_by_name' => Auth::user()->name,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    } else {
      $insert_document_attachment = db::connection('ympimis_2')->table('document_attachments')
      ->insert([
        'document_id' => $document_id,
        'version' => $request->input("version"),
        'version_date' => $request->input("version_date_ympi"),
        'file_name_pdf' => $filename_asli,
        'file_name_xls' => $filename_ympi,
        'created_by' => Auth::user()->username,
        'created_by_name' => Auth::user()->name,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }


    $max_document_attachment = db::connection('ympimis_2')->table('document_attachments')
    ->where('document_id', '=', $document_id)
    ->orderBy('version', 'DESC')
    ->first();


    if ($request->input("version_date_asli") != null) {
     $update_document = db::connection('ympimis_2')->table('safety_data_sheets')
     ->where('document_id', '=', $document_id)
     ->update([
      'version' => $max_document_attachment->version,
      'version_date_ympi' => $request->input("version_date_ympi"),
      'last_date_ympi' => $request->input("last_date_ympi"),
      'version_date_asli' => $request->input("version_date_asli"),
      'last_date_asli' => $request->input("last_date_asli"),
      'file_name_asli' => $max_document_attachment->file_name_pdf,
      'file_name_sds' => $max_document_attachment->file_name_xls,
      'created_by' => Auth::user()->username,
      'created_by_name' => Auth::user()->name,
      'updated_at' => date('Y-m-d H:i:s'),
      'status' => 'Active'
    ]);
   }else{
     $update_document2 = db::connection('ympimis_2')->table('safety_data_sheets')
     ->where('document_id', '=', $document_id)
     ->update([
      'version' => $max_document_attachment->version,
      'version_date_ympi' => $request->input("version_date_ympi"),
      'last_date_ympi' => $request->input("last_date_ympi"),
      'file_name_sds' => $max_document_attachment->file_name_xls,
      'created_by' => Auth::user()->username,
      'created_by_name' => Auth::user()->name,
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

   }    

   $response = array(
    'status' => true,
    'message' => 'Revisi dokumen berhasil diperbaharui.'
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

public function updateDocumentSDSPCH(Request $request){
  try{

    $document_id = $request->input('id_pch');

    $file_destination = 'files/chemical/documents';
    $document_sds = db::connection('ympimis_2')->table('safety_data_sheets')
    ->where('document_id', '=', $document_id)
    ->orderBy('version', 'DESC')
    ->first();

    $no_data = $document_sds->version + 1;

    $file_pdf = $request->file('attachment_asli');
    $filename_asli = 'SDS_Asli_'.$no_data.'_'.$request->input('document_id').'.'.$request->input('extension_asli');

    $file_pdf->move($file_destination, $filename_asli);

    $update_document = db::connection('ympimis_2')->table('safety_data_sheets')
    ->where('document_id', '=', $document_id)
    ->update([
      'file_name_asli' => $filename_asli,
      'created_by' => Auth::user()->username,
      'created_by_name' => Auth::user()->name,
      'updated_at' => date('Y-m-d H:i:s')
    ]);

    $response = array(
      'status' => true,
      'message' => 'Update dokumen Expaired berhasil diperbaharui.'
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

public function updateSDSExpaired(Request $request){
  try{

    $data_exp = [];

    $document_id = $request->input('document_id');

    $update_expireds = [];

    for ($i=0; $i < count($document_id); $i++) {

     $document_sds = db::connection('ympimis_2')->table('safety_data_sheets')
     ->where('document_id', '=', $document_id)
     ->first();

     array_push($data_exp, $document_sds);

     $update_document1 = db::connection('ympimis_2')->table('safety_data_sheets')
     ->where('document_id', '=', $document_id[$i])
     ->update([
      'remark' => '1',
      'updated_at' => date('Y-m-d H:i:s')
    ]);

     $update_document = db::connection('ympimis_2')->table('sds_expaireds')
     ->where('document_id', '=', $document_id[$i])
     ->where('remark', '=', 'PCH')
     ->where('version', '=', $document_sds->version)
     ->update([
      'status' => '1',
      'approver_name' => Auth::user()->name,
      'approved_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s')
    ]);

     array_push($update_expireds, $document_id[$i]);   
   }

   $update_expa = join(",",$update_expireds);

   $data = [
    'datas' => $data_exp,
    'title' => 'Rimender SDS Expaired New Documents ',
    'update_data' => $update_expa
  ];

  $mail_to_success = [];
  array_push($mail_to_success, 'whica.parama@music.yamaha.com');

  Mail::to($mail_to_success)
  ->bcc('lukman.hakim.saputra@music.yamaha.com')
  ->send(new SendEmail($data, 'notifikasi_progress_sds'));

  $response = array(
    'status' => true,
    'message' => 'Update dokumen Expaired berhasil diperbaharui.'
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

public function indexSdsAsli($id){

  $title = 'SAFETY DATA SHEET MONITORING';
  $no_id = explode(",",$id);
  $data = [];

  for ($i=0; $i < count($no_id); $i++) { 
    $document_sds = db::connection('ympimis_2')->table('safety_data_sheets')
    ->where('document_id', '=', $no_id[$i])
    ->where('remark', '=', null)
    ->first();

    if (count($document_sds) > 0) {      
      array_push($data, $document_sds);
    }
  }

  return view('chemical.update_document_sds_asli', array(
    'title' => $title,
    'datas' => $data
  ))->with('page', 'SDS Asli');;
}

public function fetch_sds_document(Request $request)
{
        // DB::connection()->enableQueryLog();
        // $docs = SakurentsuThreeMDocument::where('form_id', '=', $request->get('id'))->where('document_name', '=', $request->get('doc_desc'))->get();
        // $con = $docs->count();

  $data = [];

  $id_doc = explode(",",$request->get("id"));

  for ($i=0; $i < count($id_doc); $i++) { 
    $get_data_sds = db::connection('ympimis_2')->table('safety_data_sheets')->select('document_id','file_name_asli','version')->where('document_id','=',$id_doc[$i])->first();
    array_push($data, $get_data_sds);
  }
  $response = array(
    'status' => true,
    'data' => $data
  );
  return Response::json($response);
}

public function uploadSdsDocument(Request $request)
{

 $document_id = $request->get('id_pch');

 dd($document_id);


 $file_destination = 'files/chemical/documents';
 $document_sds = db::connection('ympimis_2')->table('safety_data_sheets')
 ->where('document_id', '=', $document_id)
 ->orderBy('version', 'DESC')
 ->first();

 $no_data = $document_sds->version + 1;

 dd($no_data);

 $file_pdf = $request->file('doc_upload');
 $filename_asli = 'SDS_Asli_'.$no_data.'_'.$request->get('id_pch').'.'.$file_pdf->getClientOriginalExtension();

 if ($filename_asli != $document_sds->file_name_asli) {

   $file_pdf->move($file_destination, $filename_asli);

   $update_document = db::connection('ympimis_2')->table('safety_data_sheets')
   ->where('document_id', '=', $document_id)
   ->update([
    'file_name_asli' => $filename_asli,
    'created_by' => Auth::user()->username,
    'created_by_name' => Auth::user()->name,
    'updated_at' => date('Y-m-d H:i:s')
  ]);

 }
 else{
 // $path = base_path().'/files/chemical/documents/';
        // $file_pdf->unlink(base_path('fileschemical/documents'), $filename_asli);

   $file_old = $file_destination.$filename_asli;

   unlink($file_old);

   $file_pdf->move($file_destination, $filename_asli);

   $update_document = db::connection('ympimis_2')->table('safety_data_sheets')
   ->where('document_id', '=', $document_id)
   ->update([
    'file_name_asli' => $filename_asli,
    'created_by' => Auth::user()->username,
    'created_by_name' => Auth::user()->name,
    'updated_at' => date('Y-m-d H:i:s')
  ]);

 }

 return redirect()->back()->with(array('alert' => 'Success'));;

}

public function editDocument(Request $request){

  try{

    $update_document = db::connection('ympimis_2')->table('safety_data_sheets')
    ->where('document_id', '=', $request->get("document_id"))
    ->update([
      'gmc_material' => $request->get("editGmc"),
      'desc_material' => $request->get("editDescMaterial"),
      'title' => $request->get("editTitle"),
      'distribusi' => $request->get("distribusi_edit"),
      'created_by' => Auth::user()->username,
      'created_by_name' => Auth::user()->name,
      'updated_at' => date('Y-m-d H:i:s')
    ]);

    $response = array(
      'status' => true,
      'message' => 'Perubahan dokumen berhasil tersimpan'
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



function indexUploadSosialisasiData(Request $request)
{
  try{
    if(strlen($request->get('month')) != null){
      $month = $request->get('month');
      $upload_data_sosialisasi = db::connection('ympimis_2')->table('sds_sosialisasi_masters')
      ->select('*')
      ->where('month', '=', $month)
      ->whereNull('deleted_at')
      ->orderBy('id', 'DESC')
      ->get();
    }
    else{
      $upload_data_sosialisasi = db::connection('ympimis_2')->table('sds_sosialisasi_masters')
      ->select('*')
      ->whereNull('deleted_at')
      ->orderBy('id', 'DESC')
      ->get();

    }

    $get_dokumen = db::connection('ympimis_2')->table('safety_data_sheets')->select('title','id','file_name_sds')
    ->whereNull('deleted_at')
    ->get();

    $role_user = User::where('username', Auth::user()->username)->first();  

    $dept   = db::select('SELECT DISTINCT
      department 
      FROM
      employee_syncs 
      WHERE
      department IS NOT NULL');

    $emp = DB::SELECT("SELECT
      department,
      employee_id,
      name
      FROM
      employee_syncs 
      WHERE
      end_date is null AND employee_id NOT LIKE '%OS%'");

    $data = array('documents_sosialisasi' => $upload_data_sosialisasi,
      'get_dokumen' => $get_dokumen,
      'role_user' => $role_user,
      'depts' => $dept,
      'role_code' => Auth::user()->role_code,
      'emp' => $emp

    );

    return view('chemical.index_upload', $data
  )->with('page', 'Schedule Sosialisasi');

  }catch(\Exception $e){
    $response = array(
      'status' => false,
      'message' => $e->getMessage(),
    );
    return Response::json($response);
  }
}

public function dataOpSds( Request $request)
{
  try {
    $emp = DB::SELECT("SELECT
      department,
      employee_id,
      name
      FROM
      employee_syncs 
      WHERE
      `department` = '".$request->get('dept')."' and end_date is null AND employee_id NOT LIKE '%OS%'");


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

function uploadDataSosialisasi(Request $request)
{
  try{
    $id_user = Auth::id();

    $emp = explode(",",$request->input("employees"));

    $empk1 = explode(",",$request->input("employees_data"));

    $data_emp1 = [];

    $file_destination = 'files/chemical/documents_sosialisasi';

    if ($request->file('attachment_sds_asli') != null) {  
     $file_pdfs = $request->file('attachment_sds_asli');
     $filename_asli1 = 'Materi_Sosialisasi_'.$request->input("nama_dokumens").'.'.$request->input('extension_asli');
     $filename_asli = 'Materi_Sosialisasi_'.$request->input("nama_dokumens");
     $file_pdfs->move($file_destination, $filename_asli1);
   }else{
    $filename_asli = null;
  }

  if ($request->input("doc_sds") == "") {
    $file_sds = null;
  }else{
    $file_sds = $request->input("doc_sds");
  }

  if ($request->input("doc_id") == "undefined") {
    $file_id = null;
  }else{
    $file_id = $request->input("doc_id");
  }

  $insert_data_masters = db::connection('ympimis_2')->table('sds_sosialisasi_masters')
  ->insert([
    'nama_dokumen' => $request->input("nama_dokumens"),
    'nama_sds' => $file_sds,
    'doc_sds_asli' => $request->input("doc_sds_asli"),
    'month' => $request->input("months"),
    'periode' => $request->input("periodes"),
    'file_name_pdf' => $filename_asli,
    'created_by' => Auth::user()->username,
    'created_by_name' => Auth::user()->name,
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
  ]);

  $get_data = db::connection('ympimis_2')->table('sds_sosialisasi_masters')
  ->where('nama_dokumen','=',$request->input("nama_dokumens"))
  ->whereNull('deleted_at')
  ->get();
  if ($request->input("employees") != null) {
    for($i = 0; $i < count($emp); $i++){
      $get_emps = db::select('SELECT
        eg.employee_id,
        eg.name,
        eg.department,
        eg.section,
        ek.tag
        FROM
        employee_syncs as eg
        LEFT JOIN employees AS ek ON eg.employee_id = ek.employee_id
        where eg.employee_id = "'.$emp[$i].'" ');

      if ($get_emps != null) {
        $insert_data_detail = db::connection('ympimis_2')->table('sds_sosialisasi_details')
        ->insert([
          'document_id' => $get_data[0]->id,
          'employee_tag' => $get_emps[0]->tag,
          'employee_id' => $get_emps[0]->employee_id,
          'name' => $get_emps[0]->name,
          'department' => $get_emps[0]->department,
          'section' => $get_emps[0]->section,
          'schedule_date' => $request->input("months").'-01',
          'status' => 0,
          'area' =>  $request->input("area"),
          'created_by' => Auth::user()->username,
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ]);

      }
      else{
        for ($j=0; $j < count($empk1); $j++) { 
         $empk_data1 = explode("_",$empk1[$j]);
         if ($empk_data1[1] == $emp[$i]) {
           array_push($data_emp1, [
            'department_name' => $empk_data1[0],
            'emp' => $empk_data1[1],
            'names' => $empk_data1[2]
          ]);
         }     

         if ($data_emp1[0]["emp"] == $emp[$i]) {
          $insert_data_detail = db::connection('ympimis_2')->table('sds_sosialisasi_details')
          ->insert([
            'document_id' => $get_data[0]->id,
            'employee_tag' => null,
            'employee_id' => $data_emp1[0]["emp"],
            'name' => $data_emp1[0]["names"],
            'department' => $data_emp1[0]["department_name"],
            'section' => null,
            'status' => 0,
            'area' => $request->get("area"),
            'created_by' => Auth::user()->username,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ]);
        }
      }
    }
  }
}

$response = array(
  'status' => true,
  'message' => 'Dokumen berhasil disimpan.'
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


public function editDataSosialisasi(Request $request){

  try{  
   $data_sds = db::connection('ympimis_2')->table('safety_data_sheets')
   ->select('*')
   ->where('title',$request->get('doc_sds_edits'))
   ->get();

   $empk = explode(",",$request->input("employees_data1"));

   $data_emp = [];

   $file_destination = 'files/chemical/documents_sosialisasi';

   $file_olds = $request->get('file_old');

   $file_pdfs = $request->file('attachment_data');

   $path = public_path().'/files/chemical/documents_sosialisasi/';


   if ($file_pdfs != null) {
    $data_sds1 = db::connection('ympimis_2')->table('sds_sosialisasi_masters')
    ->where('id','=',$request->get('dokumen_id'))
    ->get();

    if ($data_sds1[0]->file_name_pdf != null) {
      $file_old = $path.$file_olds;
      unlink($file_old);

    }

    $filename_asli_name = 'Materi_Sosialisasi_'.$request->input("editNama_dokumen");

    $filename_asli = 'Materi_Sosialisasi_'.$request->input("editNama_dokumen").'.'.$request->input('file_data');

    $file_pdfs->move($file_destination, $filename_asli);
    

    $update_document = db::connection('ympimis_2')->table('sds_sosialisasi_masters')
    ->where('id', '=', $request->get("dokumen_id"))
    ->update([
      'nama_dokumen' => $request->get("editNama_dokumen"),
      'month' => $request->get("editMonth"),
      'periode' => $request->get("editPeriode"),
      'file_name_pdf' => $filename_asli_name,
      'nama_sds' => $data_sds[0]->title,
      'doc_sds_asli' => $data_sds[0]->file_name_sds,
      'created_by' => Auth::user()->username,
      'created_by_name' => Auth::user()->name,
      'updated_at' => date('Y-m-d H:i:s')
    ]);
  }else{
    $update_document = db::connection('ympimis_2')->table('sds_sosialisasi_masters')
    ->where('id', '=', $request->get("dokumen_id"))
    ->update([
      'nama_dokumen' => $request->get("editNama_dokumen"),
      'month' => $request->get("editMonth"),
      'nama_sds' => $data_sds[0]->title,
      'doc_sds_asli' => $data_sds[0]->file_name_sds,
      'periode' => $request->get("editPeriode"),
      'created_by' => Auth::user()->username,
      'created_by_name' => Auth::user()->name,
      'updated_at' => date('Y-m-d H:i:s')
    ]);
  }

  $update_periodes = db::connection('ympimis_2')->table('sds_sosialisasi_details')
  ->where('id', '=', $request->get("dokumen_id"))
  ->update([
    'schedule_date' => $request->get("editMonth").'-01',
    'created_by' => Auth::user()->username,
    'updated_at' => date('Y-m-d H:i:s')
  ]);

  $emp = explode(",",$request->input("employees"));

  if ($request->input("employees") != null) {
   for($i = 0; $i < count($emp); $i++){
    $get_emps_edit = db::select('SELECT
      eg.employee_id,
      eg.name,
      eg.department,
      eg.section,
      ek.tag
      FROM
      employee_syncs as eg
      LEFT JOIN employees AS ek ON eg.employee_id = ek.employee_id
      where eg.employee_id = "'.$emp[$i].'" ');

    if ($get_emps_edit != null) {
      $insert_data_edit = db::connection('ympimis_2')->table('sds_sosialisasi_details')
      ->insert([
        'document_id' => $request->get("dokumen_id"),
        'employee_tag' => $get_emps_edit[0]->tag,
        'employee_id' => $get_emps_edit[0]->employee_id,
        'name' => $get_emps_edit[0]->name,
        'department' => $get_emps_edit[0]->department,
        'section' => $get_emps_edit[0]->section,
        'status' => 0,
        'area' => $request->get("edit_area"),
        'created_by' => Auth::user()->username,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
      ]);
    }else{
      for ($k=0; $k < count($empk); $k++) { 
       $empk_data = explode("_",$empk[$k]);
       if ($empk_data[1] == $emp[$i]) {
         array_push($data_emp, [
          'department_name' => $empk_data[0],
          'emp' => $empk_data[1],
          'names' => $empk_data[2]
        ]);     
       }
     }
     if ($data_emp[0]["emp"] == $emp[$i]) {
      $insert_data_edit1 = db::connection('ympimis_2')->table('sds_sosialisasi_details')
      ->insert([
        'document_id' => $request->get("dokumen_id"),
        'employee_tag' => null,
        'employee_id' => $data_emp[0]["emp"],
        'name' => $data_emp[0]["names"],
        'department' => $data_emp[0]["department_name"],
        'section' => null,
        'status' => 0,
        'area' => $request->get("edit_area"),
        'created_by' => Auth::user()->username,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
      ]);
    }
  }
}
}

$response = array(
  'status' => true,
  'message' => 'Perubahan data berhasil tersimpan'
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


public function fetch_chart_sosialisasi_sds(Request $request)
{
  try {
   $document_attachments_sds = db::connection('ympimis_2')->table('sds_sosialisasi_details')
   ->where('document_id','=',$request->get('id'))
   ->get();

   $department_sosil = db::connection('ympimis_2')->table('sds_sosialisasi_details')
   ->select('department')
   ->distinct()
   ->where('document_id','=',$request->get('id'))
   ->get();

   $department = DB::SELECT("SELECT DISTINCT
    ( department ),
    department_name,
    COALESCE ( department_shortname, 'MGT' ) AS department_shortname
    FROM
    employee_syncs
    LEFT JOIN departments ON departments.department_name = employee_syncs.department 
    ORDER BY
    department");

   $departments = [];

   for ($i=0; $i < count($department_sosil); $i++) { 
     for ($j=0; $j < count($department); $j++) { 
       if ($department_sosil[$i]->department == $department[$j]->department_name) {
         array_push($departments, [
          'department_name' => $department[$j]->department_name,
          'department_shortname' => $department[$j]->department_shortname
        ]);

       }
     }
   }


   $response = array(
    'status' => true,
    'sosialisasi' => $document_attachments_sds,
    'department1' => $departments
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

public function destroy(Request $request)
{
 $upload_data_sosialisasi = db::connection('ympimis_2')->table('sds_sosialisasi_masters')
 ->where('id', '=', $request->get('id'))->get();

 $path = public_path().'/files/chemical/documents_sosialisasi/';

 if ($upload_data_sosialisasi[0]->file_name_pdf != null) {
   $file_old = $path.$upload_data_sosialisasi[0]->file_name_pdf.'.pdf';
   unlink($file_old);  
 }

 $delete_data_sosialisasi = db::connection('ympimis_2')->table('sds_sosialisasi_masters')
 ->where('id', '=', $request->get('id'))->delete();

 $delete_data_op = db::connection('ympimis_2')->table('sds_sosialisasi_details')
 ->where('document_id', '=', $request->get('id'))->delete();

 $response = array(
  'status' => true
);
 return Response::json($response); 

}



public function scanEmployeeSDS(Request $request)
{

  if (is_numeric($request->get('tag'))) {
    $nik = $request->get('tag');

    if(strlen($nik) > 9){
      $nik = substr($nik,0,9);
    }
    $employee = db::connection('ympimis_2')->table('sds_sosialisasi_details')->where('employee_tag', 'like', '%'.$nik.'%')
    ->where('document_id', '=', $request->get("id"))->first();

  }else{
    $nik = $request->get('tag');
    $employee = db::connection('ympimis_2')->table('sds_sosialisasi_details')->where('employee_id', 'like', '%'.$nik.'%')
    ->where('document_id', '=', $request->get("id"))->first(); 
  }

  if(count($employee) > 0){

    if($employee->status != 0){
      $response = array(
        'status' => false,
        'message' => 'Already attended / Sudah Pernah Scan',
      );
      return Response::json($response);
    }

    $response = array(
      'status' => true,
      'message' => 'Scan Peserta Berhasil',
      'employee' => $employee
    );
    return Response::json($response);
  }
  else{
    $response = array(
      'status' => false,
      'message' => 'ID Tag tidak terdapat pada list'
    );
    return Response::json($response);
  }
}


public function postEmployeeDataSosil(Request $request)
{
  try {

    $update_document = db::connection('ympimis_2')->table('sds_sosialisasi_details')
    ->where('document_id', '=', $request->get("id"))
    ->where('employee_id', '=', $request->get("employee_id"))
    ->update([
      'status' => 1,
      'attend_time' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s')
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


public function indexSDSSosialisasi($id)
{
  $title = "Sosialiasi Safety Data Sheet";
  $title_jp = "";

  $get_filename = db::connection('ympimis_2')->table('sds_sosialisasi_masters')
  ->where('id','=',$id)
  ->get();

  $path = '/files/chemical/documents_sosialisasi/'.$get_filename[0]->file_name_pdf.'';  

  $file_path = asset($path);

  return view('chemical.index_sosialisasi_sds', array(
    'title' => $title,
    'title_jp' => $title_jp,
    'file_path' => $file_path,
    'id_sosil' => $id
  ));
}

public function fetchEmployeeSosil(Request $request){

 $get_fetch = db::connection('ympimis_2')->table('sds_sosialisasi_details')
 ->where('document_id',$request->get("id"))->orderBy('updated_at','desc')->get();

 $response = array(
  'status' => true,
  'datas' => $get_fetch
);
 return Response::json($response);
}


public function DeleteAudienceSosialisasi(Request $request){
  try{

    $delete_data_sosialisasi = db::connection('ympimis_2')->table('sds_sosialisasi_details')
    ->where('id', '=', $request->input("id"))->delete();

    $response = array(
      'status' => true,
      'message' => 'Data Audience Sosialisasi SDS Berhasil Dihapus'
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

public function fetchEditSosialisasi(Request $request){

 $get_data_edit = db::connection('ympimis_2')->table('sds_sosialisasi_details')->select('id','employee_id','name','department','section','status','attend_time')->where('document_id',$request->input("id"))
 ->where('deleted_at',null)->orderBy('updated_at','desc')->get();

 $response = array(
  'status' => true,
  'get_data_edit' => $get_data_edit
);
 return Response::json($response);
}

public function indexSosialisasiScheduleSDS()
{
  $fy_all = WeeklyCalendar::select('fiscal_year')->distinct()->get();
  return view('chemical.schedule_monitoring_sds')
  ->with('title', 'Sosialisasi SDS Schedule Monitoring')
  ->with('title_jp', '品')
  ->with('fy_all', $fy_all)
  ->with('page', 'SDS Sosialisasi Schedule');
}

public function fetchSosialisasiScheduleSDS(Request $request)
{
  try {
    if ($request->get('fiscal_year') == '') {
      $fys = WeeklyCalendar::where('week_date',date('Y-m-d'))->first();
      $fy_now = $fys->fiscal_year;
    }else{
      $fy_now = $request->get('fiscal_year');
    }


    $get_data = DB::connection('ympimis_2')->select("SELECT
      sds_sosialisasi_details.*,
      DATE_FORMAT( schedule_date, '%b %Y' ) as months,
      DATE_FORMAT( schedule_date, '%Y-%m' ) as months_new
      FROM
      `sds_sosialisasi_details`");



    $fy = WeeklyCalendar::select(DB::RAW("DATE_FORMAT( week_date, '%Y-%m' ) as months"),DB::RAW("DATE_FORMAT( week_date, '%b %Y' ) as month_name"))->distinct()->where('fiscal_year',$fy_now)->orderby('week_date')->get();

    $periode_all = [];
    $renewals = [];
    $news = [];

        // for ($i=0; $i < count($periode); $i++) { 
    for ($j=0; $j < count($fy); $j++) { 

      $schedule_belum = DB::connection('ympimis_2')->select("SELECT
        sds_sosialisasi_details.*,
        'belum' AS `status`,area,
        DATE_FORMAT( schedule_date, '%Y-%m' ) as months
        FROM
        `sds_sosialisasi_details`
        WHERE
        DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$fy[$j]->months."' ");

      $schedule_sudah = DB::connection('ympimis_2')->select("SELECT
        sds_sosialisasi_details.*,
        'belum' AS `status`,area,
        DATE_FORMAT( schedule_date, '%Y-%m' ) as months
        FROM
        `sds_sosialisasi_details`
        WHERE
        DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$fy[$j]->months."' AND `status` = 1");

      array_push($renewals, $schedule_belum);
      array_push($news, $schedule_sudah);

    }

    $response = array(
      'status' => true,
      'renewals' => $renewals,
      'news' => $news,
      'fy' => $fy,
      'get_data'=> $get_data
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

//SPECIAL PROCESS
public function indexSpecialProcess()
{
  $fy_all = DB::SELECT("SELECT DISTINCT
    ( fiscal_year ) 
    FROM
    weekly_calendars");
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();
  return view('qa.special_process.index')
  ->with('title', 'Quality Assurance Process Audit')
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('fy_all',$fy_all)
  ->with('emp',$emp)
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function fetchSpecialProcess(Request $request)
{
  try {
    if ($request->get('fiscal_year') == '') {
      $fys = "(
      SELECT DISTINCT
      ( fiscal_year ) 
      FROM
      weekly_calendars 
      WHERE
      week_date = DATE(
      NOW()))";
    }else{
      $fys = "'".$request->get('fiscal_year')."'";
    }
    $fy = DB::SELECT("SELECT DISTINCT
      (
      DATE_FORMAT( week_date, '%Y-%m' )) AS `month`,
      DATE_FORMAT( week_date, '%b %Y' ) AS month_name 
      FROM
      weekly_calendars 
      WHERE
      fiscal_year = ".$fys." 
      ORDER BY
      week_date");

    $all_doc = DB::CONNECTION('ympimis_2')->select("SELECT DISTINCT
      ( document_number ) 
      FROM
      qa_process_audit_schedules");

    $schedules_sudah = [];
    $schedules_belum = [];
    $resumes = [];
    for ($i=0; $i < count($fy); $i++) { 
      $schedule_sudah = DB::connection('ympimis_2')->select("SELECT
        '".$fy[$i]->month."' AS `month`,
        '".$fy[$i]->month_name."' AS `month_name`,
        qa_process_audit_schedules.id AS id_audit,
        audit.decision,
        audit.handled_id,
        audit.qa_verification,
        audit.qa_verified_id,
        audit.qa_verified_name,
        audit.qa_verified_at,
        audit.due_date,
        auditor_effectivity_id,
        auditor_effectivity_name,
        ng_belum.schedule_id AS ng_belum 
        FROM
        `qa_process_audit_schedules`
        LEFT JOIN documents ON documents.document_number = qa_process_audit_schedules.document_number
        LEFT JOIN (
        SELECT DISTINCT
        ( schedule_id ),
        GROUP_CONCAT(
        DISTINCT ( send_status )) AS send_status,
        GROUP_CONCAT(
        DISTINCT ( decision )) AS decision,
        GROUP_CONCAT(
        DISTINCT ( handled_id )) AS handled_id,
        GROUP_CONCAT(
        DISTINCT ( qa_verification )) AS qa_verification,
        GROUP_CONCAT(
        DISTINCT ( qa_verified_id )) AS qa_verified_id,
        GROUP_CONCAT(
        DISTINCT ( qa_verified_at )) AS qa_verified_at,
        GROUP_CONCAT(
        DISTINCT ( qa_verified_name )) AS qa_verified_name,
        GROUP_CONCAT(
        DISTINCT ( handled_at )) AS handled_at,
        GROUP_CONCAT(
        DISTINCT ( due_date )) AS due_date 
        FROM
        qa_process_audits 
        GROUP BY
        schedule_id 
        ) audit ON audit.schedule_id = qa_process_audit_schedules.id 
        LEFT JOIN ( SELECT DISTINCT ( schedule_id ) AS schedule_id FROM qa_process_audits WHERE handled_id IS NULL AND decision = 'NG' ) AS ng_belum ON ng_belum.schedule_id = qa_process_audit_schedules.id 
        WHERE
        DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$fy[$i]->month."' 
        AND schedule_status = 'Sudah Dikerjakan' 
        GROUP BY
        `month`,
        month_name,
        id_audit,
        ng_belum.schedule_id,
        audit.qa_verified_name,
        audit.qa_verified_at,
        audit.qa_verification,
        audit.qa_verified_id,
        audit.decision,
        auditor_effectivity_id,
        auditor_effectivity_name,
        audit.handled_id,
        audit.due_date");

      $schedule_belum = DB::connection('ympimis_2')->select("SELECT
        *,
        '".$fy[$i]->month."' as `month`,
        '".$fy[$i]->month_name."' as `month_name`,
        qa_process_audit_schedules.id as id_audit
        FROM
        `qa_process_audit_schedules` 
        LEFT JOIN documents ON documents.document_number = qa_process_audit_schedules.document_number 
        WHERE
        DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$fy[$i]->month."'
        And schedule_status = 'Belum Dikerjakan'");
      array_push($schedules_sudah, $schedule_sudah);
      array_push($schedules_belum, $schedule_belum);

      $resume = DB::CONNECTION('ympimis_2')->select("SELECT
        qa_process_audit_schedules.*,
        audit.*,
        '".$fy[$i]->month."' AS `month`,
        '".$fy[$i]->month_name."' AS `month_name`,
        auditor_effectivity_id,
        auditor_effectivity_name,
        qa_process_audit_schedules.id as id_audit,
        ng_belum.schedule_id AS ng_belum 
        FROM
        `qa_process_audit_schedules`
        LEFT JOIN documents ON documents.document_number = qa_process_audit_schedules.document_number 
        LEFT JOIN (
        SELECT DISTINCT
        ( schedule_id ),
        GROUP_CONCAT(
        DISTINCT ( DATE(created_at) )) AS date_audit,
        GROUP_CONCAT(
        DISTINCT ( send_status )) AS send_status,
        GROUP_CONCAT(
        DISTINCT ( decision )) AS decision,
        GROUP_CONCAT(
        DISTINCT ( qa_verification )) AS qa_verification,
        GROUP_CONCAT(
        DISTINCT ( qa_verified_id )) AS qa_verified_id,
        GROUP_CONCAT(
        DISTINCT ( qa_verified_at )) AS qa_verified_at,
        GROUP_CONCAT(
        DISTINCT ( qa_verified_name )) AS qa_verified_name,
        GROUP_CONCAT(
        DISTINCT ( handled_id )) AS handled_id,
        GROUP_CONCAT(
        DISTINCT ( handled_at )) AS handled_at,
        GROUP_CONCAT(
        DISTINCT ( DATE_FORMAT(handled_at,'%Y-%m-%d') )) AS handled_at_short,
        GROUP_CONCAT(
        DISTINCT ( due_date )) AS due_date,
        GROUP_CONCAT(
        DISTINCT ( status_audit )) AS status_audit 
        FROM
        qa_process_audits 
        GROUP BY schedule_id
        ) audit ON audit.schedule_id = qa_process_audit_schedules.id 
        LEFT JOIN ( SELECT DISTINCT ( schedule_id ) AS schedule_id FROM qa_process_audits WHERE handled_id IS NULL AND decision = 'NG' ) AS ng_belum ON ng_belum.schedule_id = qa_process_audit_schedules.id 
        WHERE
        DATE_FORMAT( qa_process_audit_schedules.schedule_date, '%Y-%m' ) = '".$fy[$i]->month."'");

      array_push($resumes, $resume);
    }

    $dept = DB::connection('ympimis_2')->Select("SELECT DISTINCT
      ( department_shortname ),
      department_name 
      FROM
      `qa_process_audit_schedules`
      LEFT JOIN documents ON documents.document_number = qa_process_audit_schedules.document_number 
      ORDER BY
      department_shortname");

    $document = DB::connection('ympimis_2')->select("SELECT DISTINCT
      ( document_number ),
      document_name 
      FROM
      `qa_process_audit_schedules` 
      WHERE
      category = 'khusus'");
    $response = array(
      'status' => true,
      'fy' => $fy,
      'schedules_sudah' => $schedules_sudah,
      'schedules_belum' => $schedules_belum,
      'all_doc' => $all_doc,
      'resumes' => $resumes,
      'document' => $document,
      'dept' => $dept,
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

public function indexAuditSpecialProcessReport()
{
  $process = DB::connection('ympimis_2')->select("SELECT DISTINCT
    ( document_number ),
    document_name 
    FROM
    `qa_process_audit_schedules` 
    WHERE
    category = 'khusus'");
  return view('qa.special_process.report')
  ->with('title', 'Quality Assurance Process Audit ~ Report')
  ->with('title_jp', '品証 工程監査報告')
  ->with('page', 'Quality Assurance')
  ->with('process',$process)
  ->with('jpn', '品保');
}

public function fetchAuditSpecialProcessReport(Request $request)
{
  try {
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = date('Y-m-d',strtotime('- 30 DAYS'));
      $last = date('Y-m-d');
    }else{
      $first = date('Y-m-d',strtotime('- 30 DAYS'));
      $last = $date_to;
    }
  }else{
   if ($date_to == "") {
    $first = $date_from;
    $last = date('Y-m-d');
  }else{
    $first = $date_from;
    $last = $date_to;
  }
}


$report = DB::connection('ympimis_2')->table('qa_process_audits')->select('qa_process_audits.*',DB::RAW("DATE(qa_process_audits.created_at) as date_audit"))->orderBy('point_id','asc');

$report = $report->where(DB::RAW('DATE(created_at)'),'>=',$first);
$report = $report->where(DB::RAW('DATE(created_at)'),'<=',$last);

if ($request->get('document_number') != '') {
  $report = $report->where('document_number',$request->get('document_number'));
}

$report = $report->where('decision','NG');

$report = $report->get();
$response = array(
  'status' => true,
  'report' => $report
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

public function indexSpecialProcessPointCheck($category)
{
  $emp_id = strtoupper(Auth::user()->username);
  $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
  $process = DB::connection('ympimis_2')->select("SELECT DISTINCT
    ( document_number ),
    document_name 
    FROM
    `qa_process_audit_schedules` 
    WHERE
    category = 'khusus'");

  $safety = DB::connection('ympimis_2')->select("SELECT * FROM `qa_process_audit_safeties` WHERE
    category = '".$category."'");
  return view('qa.special_process.point_check')
  ->with('title', 'Quality Assurance Process Audit ~ Point Check')
  ->with('title_jp', '品証 工程監査 ~ チェック項目')
  ->with('process',$process)
  ->with('category',$category)
  ->with('process2',$process)
  ->with('process3',$process)
  ->with('safety',$safety)
  ->with('uom',$this->uom)
  ->with('uom2',$this->uom)
  ->with('page', 'Quality Assurance')
  ->with('jpn', '品保');
}

public function fetchSpecialProcessPointCheck(Request $request)
{
  try {
    $category = $request->get('category');
    if ($request->get('document_number') != '') {
      $where = "AND document_number = '".$request->get('document_number')."'";
    }else{
      $where = "";
    }

    $point_check = DB::connection('ympimis_2')->select("SELECT
      * 
      FROM
      `qa_process_audit_points` 
      WHERE
      category = '".$category."'
      ".$where."");

    $safety = DB::connection('ympimis_2')->select("SELECT * FROM `qa_process_audit_safeties` WHERE
      category = '".$category."'");
    // $point_check = 
    $response = array(
      'status' => true,
      'point_check' => $point_check,
      'safety' => $safety,

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

public function inputSpecialProcessPointCheck(Request $request)
{
  try {
    $category = $request->get('category');
    $document_number = $request->get('document_number');
    $document_name = $request->get('document_name');
    $work_process = $request->get('work_process');
    $work_point = $request->get('work_point');
    $work_safety = $request->get('work_safety');
    $audit_type = $request->get('audit_type');
    $lower = $request->get('lower');
    $upper = $request->get('upper');
    $uom = $request->get('uom');

    $input = DB::connection('ympimis_2')->table('qa_process_audit_points')->insert([
      'category' => $category,
      'document_number' => $document_number,
      'document_name' => $document_name,
      'work_process' => $work_process,
      'work_point' => $work_point,
      'work_safety' => $work_safety,
      'audit_type' => $audit_type,
      'lower' => $lower,
      'upper' => $upper,
      'uom' => $uom,
      'created_by' => Auth::id(),
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $alat = DB::connection('ympimis_2')->table('qa_process_audit_safeties')->updateOrInsert(
      [
        'category' => $category,
        'document_number' => $document_number,
        'document_name' => $document_name,
        'category_safety'=>'Alat Kelengkapan Diri'
      ],
      [
        'category' => $category,
        'document_number' => $document_number,
        'category_safety'=>'Alat Kelengkapan Diri',
        'point_safety' => $request->get('add_alat_kelengkapan'),
        'created_by' => Auth::id(),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]
    );

    $mesin = DB::connection('ympimis_2')->table('qa_process_audit_safeties')->updateOrInsert(
      [
        'category' => $category,
        'document_number' => $document_number,
        'document_name' => $document_name,
        'category_safety'=>'Alat / Mesin yang Digunakan'
      ],
      [
        'category' => $category,
        'document_number' => $document_number,
        'document_name' => $document_name,
        'category_safety'=>'Alat / Mesin yang Digunakan',
        'point_safety' => $request->get('add_alat_mesin'),
        'created_by' => Auth::id(),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]
    );


    $response = array(
      'status' => true,
      'message' => 'Success Add Point Check'

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

public function deleteSpecialProcessPointCheck(Request $request)
{
  try {
    $delete = DB::connection('ympimis_2')->table('qa_process_audit_points')->where('id',$request->get('id'))->delete();
    $response = array(
      'status' => true,
      'message' => 'Success Delete Point Check'
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

public function editSpecialProcessPointCheck(Request $request)
{
  try {
    $point = DB::connection('ympimis_2')->table('qa_process_audit_points')->where('id',$request->get('id'))->first();
    $response = array(
      'status' => true,
      'point' => $point
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

public function updateSpecialProcessPointCheck(Request $request)
{
  try {
    $category = $request->get('category');
    $document_number = $request->get('document_number');
    $document_name = $request->get('document_name');
    $work_process = $request->get('work_process');
    $work_point = $request->get('work_point');
    $work_safety = $request->get('work_safety');
    $audit_type = $request->get('audit_type');
    $lower = $request->get('lower');
    $upper = $request->get('upper');
    $uom = $request->get('uom');

    $update = DB::connection('ympimis_2')->table('qa_process_audit_points')->where('id',$request->get('id'))->update([
      'category' => $category,
      'document_number' => $document_number,
      'document_name' => $document_name,
      'work_process' => $work_process,
      'work_point' => $work_point,
      'work_safety' => $work_safety,
      'audit_type' => $audit_type,
      'lower' => $lower,
      'upper' => $upper,
      'uom' => $uom,
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $alat = DB::connection('ympimis_2')->table('qa_process_audit_safeties')->updateOrInsert(
      [
        'category' => $category,
        'document_number' => $document_number,
        'document_name' => $document_name,
        'category_safety'=>'Alat Kelengkapan Diri'
      ],
      [
        'category' => $category,
        'document_number' => $document_number,
        'category_safety'=>'Alat Kelengkapan Diri',
        'point_safety' => $request->get('edit_alat_kelengkapan'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]
    );

    $mesin = DB::connection('ympimis_2')->table('qa_process_audit_safeties')->updateOrInsert(
      [
        'category' => $category,
        'document_number' => $document_number,
        'document_name' => $document_name,
        'category_safety'=>'Alat / Mesin yang Digunakan'
      ],
      [
        'category' => $category,
        'document_number' => $document_number,
        'document_name' => $document_name,
        'category_safety'=>'Alat / Mesin yang Digunakan',
        'point_safety' => $request->get('edit_alat_mesin'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]
    );

    $response = array(
      'status' => true,
      'message' => 'Success Update Point Check'

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

public function updateSpecialProcessPointSafety(Request $request)
{
  try {
    $mesin = DB::connection('ympimis_2')->table('qa_process_audit_safeties')->where('id',$request->get('id'))->update(
      [
        'point_safety' => $request->get('point_safety'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]
    );

    $response = array(
      'status' => true,
      'message' => 'Success Update Point Safety'

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

public function auditSpecialProcessContinue($id)
{
  $emp_id = strtoupper(Auth::user()->username);
  $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
  $schedule = DB::CONNECTION('ympimis_2')->table('qa_process_audit_schedules')->where('id',$id)->first();
  $point_check = DB::CONNECTION('ympimis_2')->select("SELECT
      * 
    FROM
    `qa_process_audit_points`
    JOIN ( SELECT point_id,document_number, work_process, hasil, note, decision,id AS id_hasil,employee_id FROM qa_process_audits WHERE schedule_id = '".$id."' ) a ON a.document_number = qa_process_audit_points.document_number 
    AND a.point_id = qa_process_audit_points.id 
    WHERE
    qa_process_audit_points.document_number = '".$schedule->document_number."'
    order by qa_process_audit_points.id");
  $apd_alat = DB::CONNECTION('ympimis_2')->table('qa_process_audit_safeties')->where('document_number',$schedule->document_number)->where('category_safety','Alat Kelengkapan Diri')->first();
  $mesin = DB::CONNECTION('ympimis_2')->table('qa_process_audit_safeties')->where('document_number',$schedule->document_number)->where('category_safety','Alat / Mesin yang Digunakan')->first();
  $emp = EmployeeSync::whereNull('end_date')->get();
  // $audit = DB::CONNECTION('ympimis_2')->table('qa_process_audits')->where('schedule_id',$id)->where('status_audit','Belum')->get();
  return view('qa.special_process.audit_continue')
  ->with('title', 'Quality Assurance Process Audit')
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('schedule',$schedule)
  ->with('point_check',$point_check)
  ->with('apd_alat',$apd_alat)
  ->with('emp',$emp)
  ->with('mesin',$mesin)
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function auditSpecialProcess($id)
{
  $emp_id = strtoupper(Auth::user()->username);
  $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
  $schedule = DB::CONNECTION('ympimis_2')->table('qa_process_audit_schedules')->where('id',$id)->first();
  $point_check = DB::CONNECTION('ympimis_2')->table('qa_process_audit_points')->where('document_number',$schedule->document_number)->get();
  $apd_alat = DB::CONNECTION('ympimis_2')->table('qa_process_audit_safeties')->where('document_number',$schedule->document_number)->where('category_safety','Alat Kelengkapan Diri')->first();
  $mesin = DB::CONNECTION('ympimis_2')->table('qa_process_audit_safeties')->where('document_number',$schedule->document_number)->where('category_safety','Alat / Mesin yang Digunakan')->first();
  $emp = EmployeeSync::whereNull('end_date')->get();
  return view('qa.special_process.audit')
  ->with('title', 'Quality Assurance Process Audit')
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('schedule',$schedule)
  ->with('point_check',$point_check)
  ->with('apd_alat',$apd_alat)
  ->with('emp',$emp)
  ->with('mesin',$mesin)
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function inputAuditSpecialProcess(Request $request)
{
  try {
    $schedule_id = $request->get('schedule_id');
    $schedule_date = $request->get('schedule_date');
    $auditor_id = $request->get('auditor_id');
    $auditor_name = $request->get('auditor_name');
    $auditee_id = $request->get('auditee_id');
    $auditee_name = $request->get('auditee_name');
    $document_number = $request->get('document_number');
    $document_name = $request->get('document_name');
    $document_version = $request->get('document_version');
    $alat = $request->get('alat');
    $mesin = $request->get('mesin');
    $point_id = $request->get('point_id');
    $decision = $request->get('decision');
    $note = $request->get('note');
    $audit_type = $request->get('audit_type');
    $lower = $request->get('lower');
    $upper = $request->get('upper');
    $uom = $request->get('uom');
    $hasil = $request->get('hasil');
    $status_audit = $request->get('status_audit');
    $employee_id = $request->get('employee_id');

    $emp = EmployeeSync::where('employee_id',$employee_id)->first();

    // $filename = "";
    // $file_destination = 'data_file/qa/special_process';

    // if (count($request->file('file')) > 0) {
    //   $file = $request->file('file');
    //   $filename = $document_number.'_'.$point_id.'_'.date('YmdHisa').'.'.$request->input('extension');
    //   $file->move($file_destination, $filename);
    // }

    $point = DB::connection('ympimis_2')->table('qa_process_audit_points')->where('id',$point_id)->first();

    $input = DB::connection('ympimis_2')->table('qa_process_audits')->insert([
      'category' => 'khusus',
      'schedule_id' => $schedule_id,
      'schedule_date' => $schedule_date,
      'auditor_id' => $auditor_id,
      'auditor_name' => $auditor_name,
      'auditee_id' => $auditee_id,
      'auditee_name' => $auditee_name,
      'document_number' => $document_number,
      'document_name' => $document_name,
      'document_version' => $document_version,
      'due_date' => date('Y-m-d', strtotime('+14 day')),
      'work_process' => $point->work_process,
      'work_point' => $point->work_point,
      'work_safety' => $point->work_safety,
      'alat' => $alat,
      'mesin' => $mesin,
      'decision' => $decision,
      'point_id' => $point_id,
      'note' => $note,
      'audit_type' => $audit_type,
      'employee_id' => $employee_id,
      'employee_name' => $emp->name,
      'lower' => $lower,
      'upper' => $upper,
      'status_audit' => $status_audit,
      'uom' => $uom,
      'hasil' => $hasil,
      // 'evidence' => $filename,
      'created_by' => Auth::id(),
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    if ($status_audit == 'Sudah') {
      $schedule = DB::connection('ympimis_2')->table('qa_process_audit_schedules')->where('id',$schedule_id)->update([
        'schedule_status' => 'Sudah Dikerjakan',
        'updated_at' => date('Y-m-d H:i:s')
      ]);
    }


    $response = array(
      'status' => true,
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

public function inputAuditSpecialProcessContinue(Request $request)
{
  try {
    $id = $request->get('id');
    $schedule_id = $request->get('schedule_id');
    $decision = $request->get('decision');
    $note = $request->get('note');
    $hasil = $request->get('hasil');
    $status_audit = $request->get('status_audit');
    $point_id = $request->get('point_id');
    $employee_id = $request->get('employee_id');

    $emp = EmployeeSync::where('employee_id',$employee_id)->first();

    // $filename = "";
    // $file_destination = 'data_file/qa/special_process';

    // if (count($request->file('file')) > 0) {
    //   $file = $request->file('file');
    //   $filename = $document_number.'_'.$point_id.'_'.date('YmdHisa').'.'.$request->input('extension');
    //   $file->move($file_destination, $filename);
    // }

    // $point = DB::connection('ympimis_2')->table('qa_process_audit_points')->where('id',$point_id)->first();

    $input = DB::connection('ympimis_2')->table('qa_process_audits')->where('id',$id)->update([
      'due_date' => date('Y-m-d', strtotime('+14 day')),
      'decision' => $decision,
      'note' => $note,
      'status_audit' => $status_audit,
      'point_id' => $point_id,
      'employee_id' => $employee_id,
      'employee_name' => $emp->name,
      'hasil' => $hasil,
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    if ($status_audit == 'Sudah') {
      $schedule = DB::connection('ympimis_2')->table('qa_process_audit_schedules')->where('id',$schedule_id)->update([
        'schedule_status' => 'Sudah Dikerjakan',
        'updated_at' => date('Y-m-d H:i:s')
      ]);
    }


    $response = array(
      'status' => true,
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

public function sendEmailAuditSpecialProcess($id)
{
  try {
    $schedule = DB::connection('ympimis_2')->table('qa_process_audit_schedules')->where('id',$id)->first();
    $audit = DB::connection('ympimis_2')->select("SELECT
        * 
      FROM
      `qa_process_audits` 
      WHERE
      ( schedule_id = '".$id."' AND decision = 'NG' ) 
      OR (
      schedule_id = '".$id."' 
      AND decision = 'NS')");


    for ($i=0; $i < count($audit); $i++) { 
      $mail_to = [];
      $cc = [];

      $bcc = ['mokhamad.khamdan.khabibi@music.yamaha.com'];

      //Auditee
      $emp = EmployeeSync::where('employee_id',$audit[$i]->auditee_id)->first();
      $user = User::where('username',$audit[$i]->auditee_id)->first();
      if ($emp->department == 'Standardization Department') {
        array_push($mail_to, 'agustina.hayati@music.yamaha.com');
      }else{
        if (str_contains($user->email,'music.yamaha.com')) {
          array_push($mail_to, $user->email);
        }
      }

      //Auditor
      if (str_contains($audit[$i]->auditor_id,',')) {
        $auditor = explode(',', $audit[$i]->auditor_id);
        for ($j=0; $j < count($auditor); $j++) { 
          $user = User::where('username',$auditor[$j])->first();
          array_push($mail_to, $user->email);
        }
      }else{
        $user = User::where('username',$audit[$i]->auditor_id)->first();
        array_push($mail_to, $user->email);
      }

      $emp = EmployeeSync::where('employee_id',$audit[$i]->auditee_id)->first();
      $manager = Approver::where('department',$emp->department)->where('remark','Manager')->first();
      array_push($cc, $manager->approver_email);

      Mail::to($mail_to)->cc($cc,'CC')->bcc($bcc,'BCC')->send(new SendEmail($audit[$i], 'audit_special_process'));
    }

    $update = DB::connection('ympimis_2')->table('qa_process_audits')->where('schedule_id',$id)->update([
      'send_status' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $response = array(
      'status' => true,
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

public function sendEmailCekAuditSpecialProcess($id)
{
  try {
    $schedule = DB::connection('ympimis_2')->table('qa_process_audit_schedules')->where('id',$id)->first();
    $audit = DB::connection('ympimis_2')->select("SELECT
      qa_process_audits.*,
      qa_process_audit_schedules.auditor_effectivity_id,
      qa_process_audit_schedules.auditor_effectivity_name
      FROM
      `qa_process_audits`
      LEFT JOIN qa_process_audit_schedules ON qa_process_audit_schedules.id = schedule_id 
      WHERE
      ( schedule_id = '".$id."' AND decision = 'NG' AND handling IS NOT NULL ) 
      OR (
      schedule_id = '".$id."' 
      AND decision = 'NS' 
      AND handling IS NOT NULL)");


    for ($i=0; $i < count($audit); $i++) { 
      $mail_to = [];
      $cc = [];

      $bcc = ['mokhamad.khamdan.khabibi@music.yamaha.com'];

      //Auditee
      if ($schedule->auditor_effectivity_id != null) {
        $emp = EmployeeSync::where('employee_id',$schedule->auditor_effectivity_id)->first();
        $user = User::where('username',$schedule->auditor_effectivity_id)->first();
        if (str_contains($user->email,'music.yamaha.com')) {
          array_push($mail_to, $user->email);
          array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');
        }else {
          if ($emp->department == 'Standardization Department') {
            array_push($mail_to, 'agustina.hayati@music.yamaha.com');
          }else{
            $foreman = Approver::where('remark','Foreman')->where('department',$emp->department)->where('section',$emp->section)->first();
            if (!$foreman) {
              $foreman = Approver::where('remark','Chief')->where('department',$emp->department)->where('section',$emp->section)->first();
              if ($foreman) {
                array_push($mail_to, $foreman->approver_email);
              }
            }else{
              array_push($mail_to, $foreman->approver_email);
            }
          }
        }
      }else{
        array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');
        array_push($mail_to, 'agustina.hayati@music.yamaha.com');
      }

      array_push($cc, 'yayuk.wahyuni@music.yamaha.com');
      Mail::to($mail_to)->cc($cc,'CC')->bcc($bcc,'BCC')->send(new SendEmail($audit[$i], 'audit_special_process_cek'));
    }

    // $update = DB::connection('ympimis_2')->table('qa_process_audits')->where('schedule_id',$id)->update([
    //   'send_status' => date('Y-m-d H:i:s'),
    //   'updated_at' => date('Y-m-d H:i:s'),
    // ]);

    $response = array(
      'status' => true,
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

public function handlingAuditSpecialProcess($id)
{
  $emp_id = strtoupper(Auth::user()->username);
  $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
  $audit = DB::connection('ympimis_2')->select("SELECT
  * 
    FROM
    `qa_process_audits` 
    WHERE
    ( schedule_id = '".$id."' AND decision = 'NG' AND handling IS NULL ) 
    OR (
    schedule_id = '".$id."' 
    AND decision = 'NS' 
    AND handling IS NULL)");
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();
  return view('qa.special_process.handling')
  ->with('title', 'Penanganan Audit Proses Khusus')
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('audit',$audit)
  ->with('emp',$emp)
  ->with('count_audit',count($audit))
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function inputHandlingSpecialProcess(Request $request)
{
  try {
    $id_handling = $request->get('id_handling');
    $handling = $request->get('handling');
    $handled_id = $request->get('handled_id');
    $handled_name = $request->get('handled_name');
    $handling_revision = $request->get('handling_revision');

    $filename = null;

    if (count($request->file('fileData')) > 0) {
      $tujuan_upload = 'data_file/qa/special_process/handling';
      $file = $request->file('fileData');
      $filename = $request->get('id_handling').'_'.date('YmdHisa').'.'.$request->get('extension');
      $file->move($tujuan_upload,$filename);
    }

            // for ($i=0; $i < count($id_handling); $i++) { 
    $update = DB::connection('ympimis_2')->table('qa_process_audits')->where('id',$id_handling)->update([
      'handling' => $handling,
      'handled_id' => $handled_id,
      'handled_name' => $handled_name,
      'handling_revision' => $handling_revision,
      'handling_revision_document' => $filename,
      'handled_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);
    $audit = DB::connection('ympimis_2')->table('qa_process_audits')->select('qa_process_audits.*','qa_process_audit_schedules.auditor_effectivity_id','qa_process_audit_schedules.auditor_effectivity_name')->where('qa_process_audits.id',$id_handling)->join('qa_process_audit_schedules','qa_process_audit_schedules.id','schedule_id')->first();
    $schedule = DB::connection('ympimis_2')->table('qa_process_audit_schedules')->where('id',$audit->schedule_id)->first();

    $mail_to = [];
    $cc = [];

    $bcc = ['mokhamad.khamdan.khabibi@music.yamaha.com'];

      //Auditee
    if ($schedule->auditor_effectivity_id != null) {
      $emp = EmployeeSync::where('employee_id',$schedule->auditor_effectivity_id)->first();
      $user = User::where('username',$schedule->auditor_effectivity_id)->first();
      if (str_contains($user->email,'music.yamaha.com')) {
        array_push($mail_to, $user->email);
        array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');
      }else {
        if ($emp->department == 'Standardization Department') {
          array_push($mail_to, 'agustina.hayati@music.yamaha.com');
        }else{
          $foreman = Approver::where('remark','Foreman')->where('department',$emp->department)->where('section',$emp->section)->first();
          if (!$foreman) {
            $foreman = Approver::where('remark','Chief')->where('department',$emp->department)->where('section',$emp->section)->first();
            if ($foreman) {
              array_push($mail_to, $foreman->approver_email);
            }
          }else{
            array_push($mail_to, $foreman->approver_email);
          }
        }
      }
    }else{
      array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');
      array_push($mail_to, 'agustina.hayati@music.yamaha.com');
    }

    array_push($cc, 'yayuk.wahyuni@music.yamaha.com');

    Mail::to($mail_to)->cc($cc,'CC')->bcc($bcc,'BCC')->send(new SendEmail($audit, 'audit_special_process_cek'));
            // }
    $response = array(
      'status' => true,
      'message' => 'Success Input Penanganan'
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

public function pdfAuditSpecialProcess($id)
{
  $audit = DB::connection('ympimis_2')->table('qa_process_audits')->where('schedule_id',$id)->orderBy('point_id','asc')->get();
  $schedule = DB::connection('ympimis_2')->table('qa_process_audit_schedules')->where('id',$id)->first();
  $safety = DB::connection('ympimis_2')->table('qa_process_audit_safeties')->where('document_number',$audit[0]->document_number)->first();
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

  $pdf = \App::make('dompdf.wrapper');
  $pdf->getDomPDF()->set_option("enable_php", true);
  $pdf->setPaper('A4', 'landscape');

  $pdf->loadView('qa.special_process.pdf_audit', array(
    'audit' => $audit,
  ));

  return $pdf->stream($audit[0]->document_number." - ".$audit[0]->document_name." (".$audit[0]->schedule_date.").pdf");

}

public function indexSpecialProcessSchedule()
{
  $document = DB::connection('ympimis_2')->table('documents')->where('status','Active')->get();
  $emp = EmployeeSync::where('end_date',null)->get();
  return view('qa.special_process.schedule')
  ->with('title', 'Schedule Audit Proses Khusus')
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('jpn', '品保')
  ->with('emp', $emp)
  ->with('emp2', $emp)
  ->with('emp3', $emp)
  ->with('emp4', $emp)
  ->with('emp5', $emp)
  ->with('emp6', $emp)
  ->with('document', $document)
  ->with('document2', $document);
}

public function fetchSpecialProcessSchedule(Request $request)
{
  try {
    $schedule = DB::connection('ympimis_2')->table('qa_process_audit_schedules')->get();
    $response = array(
      'status' => true,
      'schedule' => $schedule,
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

public function inputSpecialProcessSchedule(Request $request)
{
  try {
    $document = $request->get('document');
    $version = $request->get('version');
    $auditor_id = $request->get('auditor_id');
    $auditee_id = $request->get('auditee_id');
    $schedule_date = $request->get('schedule_date');
    $auditor_effectivity_id = $request->get('auditor_effectivity_id');

    $documents = DB::connection('ympimis_2')->table('documents')->where('status','Active')->where('document_number',$document)->first();
    $auditee = EmployeeSync::where('employee_id',$auditee_id)->first();
    $auditor_effectivity = EmployeeSync::where('employee_id',$auditor_effectivity_id)->first();

    if (str_contains($auditor_id,',')) {
      $auditor_all = explode(',', $auditor_id);
      $auditors = [];
      for ($i=0; $i < count($auditor_all); $i++) { 
        $auditor = EmployeeSync::where('employee_id',$auditor_all[$i])->first();
        array_push($auditors, $auditor->name);
      }
      $auditor_name = join(',',$auditors);
    }else{
      $auditor = EmployeeSync::where('employee_id',$auditor_id)->first();
      $auditor_name = $auditor->name;
    }

    $input = DB::connection('ympimis_2')->table('qa_process_audit_schedules')->insert([
      'category' => 'khusus',
      'document_number' => $document,
      'document_name' => $documents->title,
      'document_version' => $version,
      'auditor_id' => $auditor_id,
      'auditor_name' => $auditor_name,
      'auditee_id' => $auditee_id,
      'auditee_name' => $auditee->name,
      'schedule_date' => $schedule_date.'-01',
      'auditor_effectivity_id' => $auditor_effectivity_id,
      'auditor_effectivity_name' => $auditor_effectivity->name,
      'schedule_status' => 'Belum Dikerjakan',
      'created_by' => Auth::id(),
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);
    $response = array(
      'status' => true,
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

public function updateSpecialProcessSchedule(Request $request)
{
  try {
    $document = $request->get('document');
    $version = $request->get('version');
    $auditor_id = $request->get('auditor_id');
    $auditee_id = $request->get('auditee_id');
    $schedule_date = $request->get('schedule_date');
    $id = $request->get('id');
    $auditor_effectivity_id = $request->get('auditor_effectivity_id');

    $documents = DB::connection('ympimis_2')->table('documents')->where('status','Active')->where('document_number',$document)->first();
    $auditee = EmployeeSync::where('employee_id',$auditee_id)->first();
    $auditor_effectivity = EmployeeSync::where('employee_id',$auditor_effectivity_id)->first();

    if (str_contains($auditor_id,',')) {
      $auditor_all = explode(',', $auditor_id);
      $auditors = [];
      for ($i=0; $i < count($auditor_all); $i++) { 
        $auditor = EmployeeSync::where('employee_id',$auditor_all[$i])->first();
        array_push($auditors, $auditor->name);
      }
      $auditor_name = join(',',$auditors);
    }else{
      $auditor = EmployeeSync::where('employee_id',$auditor_id)->first();
      $auditor_name = $auditor->name;
    }

    $update = DB::connection('ympimis_2')->table('qa_process_audit_schedules')->where('id',$id)->update([
      'document_number' => $document,
      'document_name' => $documents->title,
      'document_version' => $version,
      'auditor_id' => $auditor_id,
      'auditor_name' => $auditor_name,
      'auditee_id' => $auditee_id,
      'auditee_name' => $auditee->name,
      'auditor_effectivity_id' => $auditor_effectivity_id,
      'auditor_effectivity_name' => $auditor_effectivity->name,
      'schedule_date' => $schedule_date.'-01',
      'updated_at' => date('Y-m-d H:i:s'),
    ]);
    $response = array(
      'status' => true,
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

public function deleteSpecialProcessSchedule(Request $request)
{
  try {
    $delete = DB::connection('ympimis_2')->table('qa_process_audit_schedules')->where('id',$request->get('id'))->delete();
    $response = array(
      'status' => true,
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

//END SPECIAL PROCESS

//QC KOTEIHYO
public function indexQcKoteihyo()
{
  $fy_all = DB::SELECT("SELECT DISTINCT
    ( fiscal_year ) 
    FROM
    weekly_calendars");
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();
  return view('qa.qc_koteihyo.index')
  ->with('title', 'Quality Assurance QC Koteihyo')
  ->with('title_jp', '品証のQC工程表')
  ->with('emp', $emp)
  ->with('fy_all', $fy_all)
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('page', 'Quality Assurance')
  ->with('jpn', '品保');
}

public function fetchQcKoteihyo(Request $request)
{
  try {
    if ($request->get('fiscal_year') == '') {
      $fys = "(
      SELECT DISTINCT
      ( fiscal_year ) 
      FROM
      weekly_calendars 
      WHERE
      week_date = DATE(
      NOW()))";
    }else{
      $fys = "'".$request->get('fiscal_year')."'";
    }
    $fy = DB::SELECT("SELECT DISTINCT
      (
      DATE_FORMAT( week_date, '%Y-%m' )) AS `month`,
      DATE_FORMAT( week_date, '%b %Y' ) AS month_name 
      FROM
      weekly_calendars 
      WHERE
      fiscal_year = ".$fys." 
      ORDER BY
      week_date");

    $fy_first_last = DB::SELECT("( SELECT DISTINCT
      (
      DATE_FORMAT( week_date, '%Y-%m' )) AS `month`,
      DATE_FORMAT( week_date, '%b %Y' ) AS month_name 
      FROM
      weekly_calendars 
      WHERE
      fiscal_year = ".$fys." 
      ORDER BY
      week_date 
      LIMIT 1 
      ) UNION ALL
      (
      SELECT DISTINCT
      (
      DATE_FORMAT( week_date, '%Y-%m' )) AS `month`,
      DATE_FORMAT( week_date, '%b %Y' ) AS month_name 
      FROM
      weekly_calendars 
      WHERE
      fiscal_year = ".$fys." 
      ORDER BY
      week_date DESC 
      LIMIT 1)");

    $schedule_sudah = DB::CONNECTION('ympimis_2')->select("SELECT
              * 
      FROM
      qa_qc_koteihyo_schedules 
      WHERE
      `status` = 'Sudah Dikerjakan' 
      AND DATE_FORMAT( schedule_date, '%Y-%m' ) >= '".$fy_first_last[0]->month."' 
      AND DATE_FORMAT( schedule_date, '%Y-%m' ) <= '".$fy_first_last[1]->month."'");

    $schedule_belum = DB::CONNECTION('ympimis_2')->select("SELECT
              * 
      FROM
      qa_qc_koteihyo_schedules 
      WHERE
      `status` = 'Belum Dikerjakan' 
      AND DATE_FORMAT( schedule_date, '%Y-%m' ) >= '".$fy_first_last[0]->month."' 
      AND DATE_FORMAT( schedule_date, '%Y-%m' ) <= '".$fy_first_last[1]->month."'");

    $resumes = [];
    for ($i=0; $i < count($fy); $i++) { 
      $resume = DB::CONNECTION('ympimis_2')->select("SELECT
        qa_qc_koteihyo_schedules.*,
        '".$fy[$i]->month_name."' AS `month`,
        '".$fy[$i]->month."' AS `month_name`,
        qa_qc_koteihyo_schedules.id AS id_audit,
        documents.file_name_pdf,
        audit.schedule_id,
        IF
        ( audit.handling LIKE '%null%' || audit.handling = NULL, NULL, audit.handling ) AS handling,
        audit.send_status,
        audit.due_date,
        audit.closing_date,
        audit.handled_at,
        audit.handling_file_auditor,
        audit.handling_file_auditee,
        audit.`condition`,
        qa_qc_koteihyo_schedules.employee_id AS auditor_id,
        qa_qc_koteihyo_schedules.`name` AS auditor_name,
        audit.`file_name_finding` 
        FROM
        `qa_qc_koteihyo_schedules`
        LEFT JOIN documents ON documents.document_number = qa_qc_koteihyo_schedules.document_number
        LEFT JOIN (
        SELECT
        document_number,
        document_name,
        schedule_id,
        GROUP_CONCAT( IF(`condition` = 'NG',COALESCE ( handling, 'null' ),handling) ) AS handling,
        GROUP_CONCAT( DISTINCT ( send_status ) ) AS send_status,
        GROUP_CONCAT( DISTINCT ( due_date ) ) AS due_date,
        GROUP_CONCAT( DISTINCT ( closing_date ) ) AS closing_date,
        GROUP_CONCAT( DISTINCT ( `condition` ) ) AS `condition`,
        GROUP_CONCAT( DISTINCT ( `auditor_id` ) ) AS `auditor_id`,
        GROUP_CONCAT( DISTINCT ( `handled_at` )  SEPARATOR '<br>') AS `handled_at`,
        GROUP_CONCAT( DISTINCT ( `handling_file_auditor` ) ) AS `handling_file_auditor`,
        GROUP_CONCAT( DISTINCT ( `handling_file_auditee` ) ) AS `handling_file_auditee`,
        GROUP_CONCAT( DISTINCT ( `auditor_name` ) ) AS `auditor_name`,
        GROUP_CONCAT( DISTINCT ( `file_name_finding` ) ) AS `file_name_finding` 
        FROM
        qa_qc_koteihyo_audits 
        GROUP BY
        document_number,
        document_name,
        schedule_id 
        ) AS audit ON audit.schedule_id = qa_qc_koteihyo_schedules.id 
        WHERE
        DATE_FORMAT( qa_qc_koteihyo_schedules.schedule_date, '%Y-%m' ) = '".$fy[$i]->month."'");

      array_push($resumes, $resume);
    }

    $all_doc = DB::CONNECTION('ympimis_2')->select("SELECT DISTINCT
      ( document_number ) 
      FROM
      `qa_qc_koteihyo_schedules`");

    $response = array(
      'status' => true,
      'fy' => $fy,
      'fy_first_last' => $fy_first_last,
      'schedule_sudah' => $schedule_sudah,
      'schedule_belum' => $schedule_belum,
      'all_doc' => $all_doc,
      'resumes' => $resumes,
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

public function uploadQcKoteihyoAudit(Request $request)
{
  try {
    $filename = "";
    $file_destination = 'data_file/qc_koteihyo/finding';

    if (count($request->file('file')) > 0) {
      $file = $request->file('file');
      $filename = 'finding_'.$request->get('schedule_date').'_'.$request->get('document_number').'_'.date('YmdHisa').'.'.$request->input('extension');
      $file->move($file_destination, $filename);

      $response = array(
        'status' => true,
        'message' => 'Upload Successful',
        'filename' => $filename
      );
      return Response::json($response);
    }else{
      $response = array(
        'status' => false,
        'message' => 'File Not Found',
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

public function uploadHandlingQcKoteihyoAudit(Request $request)
{
  try {
    $filename = "";
    $file_destination = 'data_file/qc_koteihyo/handling';
    $id = $request->get('id');

    $audit = DB::connection('ympimis_2')->table('qa_qc_koteihyo_audits')->where('id',$id)->first();

    if ($request->get('type') == 'Auditor') {
      if (count($request->file('file_auditor')) > 0) {
        $file = $request->file('file_auditor');
        $filename = 'handling_auditor_'.$audit->schedule_date.'_'.$audit->document_number.'_'.date('YmdHisa').'.'.$request->input('extension');
        $file->move($file_destination, $filename);

        for ($i=0; $i < count($id); $i++) { 
          $update = DB::connection('ympimis_2')->table('qa_qc_koteihyo_audits')->where('id',$id)->update([
            'handling_file_auditor' => $filename
          ]);
        }
      }
    }else{
      if (count($request->file('file_auditee')) > 0) {
        $file = $request->file('file_auditee');
        $filename = 'handling_auditee_'.$audit->schedule_date.'_'.$audit->document_number.'_'.date('YmdHisa').'.'.$request->input('extension');
        $file->move($file_destination, $filename);

        for ($i=0; $i < count($id); $i++) { 
          $update = DB::connection('ympimis_2')->table('qa_qc_koteihyo_audits')->where('id',$id)->update([
            'handling_file_auditee' => $filename
          ]);
        }
      }
    }

    $response = array(
      'status' => true,
      'message' => 'Upload Successful',
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

public function indexQcKoteihyoPointCheck()
{
  $emp_id = strtoupper(Auth::user()->username);
  $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
  $qc_koteihyo = DB::connection('ympimis_2')->select("SELECT
      * 
    FROM
    documents");
  return view('qa.qc_koteihyo.point_check')
  ->with('title', 'Quality Assurance QC Koteihyo')
  ->with('title_jp', '品証のQC工程表')
  ->with('qc_koteihyo',$qc_koteihyo)
  ->with('qc_koteihyo2',$qc_koteihyo)
  ->with('qc_koteihyo3',$qc_koteihyo)
  ->with('page', 'Quality Assurance')
  ->with('jpn', '品保');
}

public function fetchQcKoteihyoPointCheck(Request $request)
{
  try {
    if ($request->get('document_number') != '') {
      $where = "WHERE document_number = '".$request->get('document_number')."'";
    }else{
      $where = "";
    }

    $point_check = DB::connection('ympimis_2')->select("SELECT
      * 
      FROM
      `qa_qc_koteihyo_points` 
      ".$where."");
    $response = array(
      'status' => true,
      'point_check'=> $point_check
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

public function inputQcKoteihyoPointCheck(Request $request)
{
  try {
    $document_number = $request->get('document_number');
    $document_name = $request->get('document_name');
    $category = $request->get('category');
    $jaminan_mutu = $request->get('jaminan_mutu');
    $point_check = $request->get('point_check');
    $specifics = $request->get('specifics');
    $process = $request->get('process');
    $process_number = $request->get('process_number');
    $instruction_number = $request->get('instruction_number');
    $standard = $request->get('standard');
    $control_way = $request->get('control_way');
    $frequency = $request->get('frequency');
    $machine = $request->get('machine');
    $jig = $request->get('jig');
    $control_data = $request->get('control_data');
    $checker = $request->get('checker');
    $keterangan = $request->get('keterangan');

    $filename = "";
    $file_destination = 'data_file/qa/qc_koteihyo/point_check';

    if (count($request->file('images')) > 0) {
      $file = $request->file('images');
      $filename = 'point_check_'.$process_number.'_'.date('YmdHisa').'.'.$request->input('extension');
      $file->move($file_destination, $filename);
    }

    $input = DB::connection('ympimis_2')->table('qa_qc_koteihyo_points')->insert([
      'document_number' => $document_number,
      'document_name' => $document_name,
      'category' => $category,
      'jaminan_mutu' => $jaminan_mutu,
      'point_check' => $point_check,
      'specifics' => $specifics,
      'process' => $process,
      'process_number' => $process_number,
      'instruction_number' => $instruction_number,
      'standard' => $standard,
      'control_way' => $control_way,
      'frequency' => $frequency,
      'machine' => $machine,
      'jig' => $jig,
      'control_data' => $control_data,
      'checker' => $checker,
      'keterangan' => $keterangan,
      'images' => $filename,
      'created_by' => Auth::id(),
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);


    $response = array(
      'status' => true,
      'message' => 'Success Add Point Check'

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

public function editQcKoteihyoPointCheck(Request $request)
{
  try {
    $point = DB::connection('ympimis_2')->table('qa_qc_koteihyo_points')->where('id',$request->get('id'))->first();
    $response = array(
      'status' => true,
      'point' => $point
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

public function deleteQcKoteihyoPointCheck(Request $request)
{
  try {
    $point = DB::connection('ympimis_2')->table('qa_qc_koteihyo_points')->where('id',$request->get('id'))->delete();
    $response = array(
      'status' => true,
      'message' => 'Success Delete Point Check'
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

public function updateQcKoteihyoPointCheck(Request $request)
{
  try {
    $document_number = $request->get('document_number');
    $document_name = $request->get('document_name');
    $category = $request->get('category');
    $jaminan_mutu = $request->get('jaminan_mutu');
    $point_check = $request->get('point_check');
    $specifics = $request->get('specifics');
    $process = $request->get('process');
    $process_number = $request->get('process_number');
    $instruction_number = $request->get('instruction_number');
    $standard = $request->get('standard');
    $control_way = $request->get('control_way');
    $frequency = $request->get('frequency');
    $machine = $request->get('machine');
    $jig = $request->get('jig');
    $control_data = $request->get('control_data');
    $checker = $request->get('checker');
    $keterangan = $request->get('keterangan');
    $id = $request->get('id');

    $filename = "";
    $file_destination = 'data_file/qa/qc_koteihyo/point_check';

    $point = DB::connection('ympimis_2')->table('qa_qc_koteihyo_points')->where('id',$id)->first();

    $filename = $point->images;

    if (count($request->file('images')) > 0) {
      $file = $request->file('images');
      $filename = 'point_check_'.$process_number.'_'.date('YmdHisa').'.'.$request->input('extension');
      $file->move($file_destination, $filename);
    }

    $input = DB::connection('ympimis_2')->table('qa_qc_koteihyo_points')->where('id',$id)->update([
      'document_number' => $document_number,
      'document_name' => $document_name,
      'category' => $category,
      'jaminan_mutu' => $jaminan_mutu,
      'point_check' => $point_check,
      'specifics' => $specifics,
      'process' => $process,
      'process_number' => $process_number,
      'instruction_number' => $instruction_number,
      'standard' => $standard,
      'control_way' => $control_way,
      'frequency' => $frequency,
      'machine' => $machine,
      'jig' => $jig,
      'control_data' => $control_data,
      'checker' => $checker,
      'keterangan' => $keterangan,
      'images' => $filename,
      'updated_at' => date('Y-m-d H:i:s'),
    ]);


    $response = array(
      'status' => true,
      'message' => 'Success Update Point Check'

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

public function downloadQcKoteihyoPointCheck()
{
  $file_path = public_path('data_file/qa/qc_koteihyo/TemplateQCKoteihyo.xlsx');
  return response()->download($file_path);
}

public function uploadQcKoteihyoPointCheck(Request $request)
{
  try {
    $document_number = $request->get('document_number');
    $document_name = $request->get('document_name');

    $filename = "";
    $file_destination = 'data_file/qa/qc_koteihyo/point_check/files';

    if (count($request->file('file_excel')) > 0) {
      $file = $request->file('file_excel');
      $filename = 'point_check_'.Auth::id().'_'.date('YmdHisa').'.'.$request->input('extension');
      $file->move($file_destination, $filename);
    }

    $excel = 'data_file/qa/qc_koteihyo/point_check/files/' . $filename;
    $rows = Excel::load($excel, function($reader) {
      $reader->noHeading();
      $reader->skipRows(1);

      $reader->each(function($row) {
      });
    })->toObject();
    for ($i=0; $i < count($rows); $i++) {
      $input = DB::connection('ympimis_2')->table('qa_qc_koteihyo_points')->insert([
        'document_number' => $document_number,
        'document_name' => $document_name,
        'category' => $rows[$i][2],
        'jaminan_mutu' => $rows[$i][3],
        'point_check' => $rows[$i][4],
        'specifics' => $rows[$i][5],
        'process' => $rows[$i][1],
        'process_number' => $rows[$i][0],
        'instruction_number' => $rows[$i][6],
        'standard' => $rows[$i][7],
        'control_way' => $rows[$i][8],
        'frequency' => $rows[$i][9],
        'machine' => $rows[$i][10],
        'jig' => $rows[$i][11],
        'control_data' => $rows[$i][12],
        'checker' => $rows[$i][13],
        'keterangan' => $rows[$i][14],
        'created_by' => Auth::id(),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }


    $response = array(
      'status' => true,
      'message' => 'Success Upload Point Check. Tekan tombol Edit pada masing-masing point untuk menambahkan gambar.'

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

public function indexQcKoteihyoAudit($id)
{
  $schedule = DB::CONNECTION('ympimis_2')->select("SELECT
    qa_qc_koteihyo_schedules.*,
    documents.file_name_pdf 
    FROM
    qa_qc_koteihyo_schedules
    LEFT JOIN documents ON documents.document_number = qa_qc_koteihyo_schedules.document_number 
    WHERE
    qa_qc_koteihyo_schedules.id = '".$id."'");
  $emp = EmployeeSync::whereNull('end_date')->get();
  $auditee = DB::SELECT("SELECT DISTINCT
    employee_id,
    `name`,
    section,
    position 
    FROM
    employee_syncs 
    WHERE
    end_date IS NULL 
    AND (
    position LIKE '%Staff%' 
    OR position LIKE '%Chief%' 
    OR position LIKE '%Foreman%' 
    OR position LIKE '%Manager%' 
    OR position LIKE '%Coordinator%')");
  $area = AreaCode::select('area')->distinct()->get();
  $all_doc = DB::CONNECTION('ympimis_2')->select("SELECT DISTINCT
    ( document_number ),
    title
    FROM
    `documents`");
  return view('qa.qc_koteihyo.index_audit')
  ->with('title', 'QC Koteihyo Audit')
  ->with('title_jp', '')
  ->with('page', 'Quality Assurance')
  ->with('schedule',$schedule)
  ->with('emp',$emp)
  ->with('emp2',$emp)
  ->with('area',$area)
  ->with('all_doc',$all_doc)
  ->with('all_doc2',$all_doc)
  ->with('auditee',$auditee)
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function inputQcKoteihyoAudit(Request $request)
{
  try {
    $filename = $request->get('filename');
    $extension = $request->get('extension');
    $finding = $request->get('finding');
    $document_numbers = $request->get('document_numbers');
    $process = $request->get('processes');
    $condition = $request->get('conditions');
    $finding = $request->get('findings');
    $evidence = $request->get('evidences');

    $employee_id = $request->get('emp');
    $emp = EmployeeSync::where('employee_id',$employee_id)->first();
    $name = $emp->name;
    $document_number = $request->get('document_number');
    $document_name = $request->get('document_name');
    $auditee_id = $request->get('auditee');
    $emp = EmployeeSync::where('employee_id',$auditee_id)->first();
    $auditee_name = $emp->name;
    $auditor_id = $request->get('auditor_id');
    $auditor_name = $request->get('auditor_name');
    $area = $request->get('area');

    $schedule_date = $request->get('schedule_date');
    $schedule_id = $request->get('schedule_id');

    $schedule = DB::connection('ympimis_2')->table('qa_qc_koteihyo_schedules')->where('id',$schedule_id)->first();

    $department_name = $schedule->department_name;
    $department_shortname = $schedule->department_shortname;
    $category = $schedule->category;

    $filename = "";
    $file_destination = 'data_file/qc_koteihyo/finding';

    if (count($request->file('file')) > 0) {
      $file = $request->file('file');
      $filename = 'finding_'.$request->get('schedule_date').'_'.$request->get('document_number').'.'.$request->input('extension');
      if(file_exists($file_destination.'/'.$filename)){
        unlink($file_destination.'/'.$filename);
      }
      $file->move($file_destination, $filename);
    }

    $check_file = file_exists($file_destination.'/'.$filename);

    $finding_to = 'Auditee';

    if ($employee_id == $auditor_id) {
      $finding_to = 'Auditor';            
    }

          // for ($i=0; $i < count($process); $i++) {
    $documents = DB::connection('ympimis_2')->table('documents')->where('status','Active')->where('document_number',$document_numbers)->first();
    $input = DB::connection('ympimis_2')->table('qa_qc_koteihyo_audits')->insert([
      'evidence' => $evidence,
      'due_date' => date('Y-m-d',strtotime('+ 14 days')),
      'finding' => $finding,
      'document_name_finding' => $documents->title,
      'document_number_finding' => $document_numbers,
      'process' => $process,
      'employee_id' => $employee_id,
      'employee_name' => $name,
      'auditee_id' => $auditee_id,
      'auditee_name' => $auditee_name,
      'file_name_finding' => $filename,
      'auditor_id' => $auditor_id,
      'auditor_name' => $auditor_name,
      'document_number' => $document_number,
      'document_name' => $document_name,
      'condition' => $condition,
      'area' => $area,
      'schedule_id' => $schedule_id,
      'department_name' => $department_name,
      'department_shortname' => $department_shortname,
      'schedule_date' => $schedule_date,
      'finding_to' => $finding_to,
      'category' => 'QC Koteihyo',
      'status_audit' => 'Sudah',
      'created_by' => Auth::id(),
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);
          // }

    $update = DB::connection('ympimis_2')->table('qa_qc_koteihyo_schedules')->where('id',$schedule_id)->update([
      'status' => 'Sudah Dikerjakan'
    ]);
    $response = array(
      'status' => true,
      'message' => 'Sukses Input Audit'
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

public function pdfAuditQcKoteihyo($id)
{
  $audit = DB::connection('ympimis_2')->table('qa_qc_koteihyo_audits')->where('schedule_id',$id)->get();
  $schedule = DB::connection('ympimis_2')->table('qa_qc_koteihyo_schedules')->where('id',$id)->first();
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

  $pdf = \App::make('dompdf.wrapper');
  $pdf->getDomPDF()->set_option("enable_php", true);
  $pdf->setPaper('A4', 'landscape');

  $pdf->loadView('qa.qc_koteihyo.pdf_audit', array(
    'audit' => $audit,
  ));

  return $pdf->stream($audit[0]->document_number." - ".$audit[0]->document_name." (".$audit[0]->schedule_date.").pdf");

}

public function sendEmailAuditQcKoteihyo($id)
{
  try {
    $schedule = DB::connection('ympimis_2')->table('qa_qc_koteihyo_schedules')->where('id',$id)->first();
    $audit = DB::connection('ympimis_2')->select("SELECT
            * 
      FROM
      `qa_qc_koteihyo_audits` 
      WHERE
      ( schedule_id = '".$id."' AND `condition` = 'NG' ) ");


    for ($i=0; $i < count($audit); $i++) { 
      $mail_to = [];
      $cc = [];

      $bcc = ['mokhamad.khamdan.khabibi@music.yamaha.com'];

              //Auditee
      if ($audit[$i]->finding_to == 'Auditee') {
        $emp = EmployeeSync::where('employee_id',$audit[$i]->auditee_id)->first();
        $user = User::where('username',$audit[$i]->auditee_id)->first();

        if (str_contains($user->email,'music.yamaha.com')) {
          array_push($mail_to, $user->email);
        }else{
          $foreman = Approver::where('department',$emp->department)->where('section',$emp->section)->where('remark','Foreman')->first();
          if ($foreman != null) {
            array_push($mail_to, $foreman->approver_email);
          }
        }

              //Auditor
        $emp = EmployeeSync::where('employee_id',$audit[$i]->employee_id)->first();
        $user = User::where('username',$audit[$i]->employee_id)->first();

        if (str_contains($user->email,'music.yamaha.com')) {
          array_push($mail_to, $user->email);
        }else{
          $foreman = Approver::where('department',$emp->department)->where('section',$emp->section)->where('remark','Foreman')->first();
          if ($foreman != null) {
            array_push($mail_to, $foreman->approver_email);
          }
        }

        $emp = EmployeeSync::where('employee_id',$audit[$i]->auditee_id)->first();
        $manager = Approver::where('department',$emp->department)->where('remark','Manager')->first();
        array_push($cc, $manager->approver_email);

        Mail::to($mail_to)->cc($cc,'CC')->bcc($bcc,'BCC')->send(new SendEmail($audit[$i], 'audit_qc_koteihyo'));
      }else{
        $emp = EmployeeSync::where('employee_id',$audit[$i]->auditor_id)->first();
        $user = User::where('username',$audit[$i]->auditor_id)->first();

        if (str_contains($user->email,'music.yamaha.com')) {
          array_push($mail_to, $user->email);
        }else{
          $foreman = Approver::where('department',$emp->department)->where('section',$emp->section)->where('remark','Foreman')->first();
          if ($foreman != null) {
            array_push($mail_to, $foreman->approver_email);
          }
        }

              //Auditor
        $emp = EmployeeSync::where('employee_id',$audit[$i]->employee_id)->first();
        $user = User::where('username',$audit[$i]->employee_id)->first();

        if (str_contains($user->email,'music.yamaha.com')) {
          array_push($mail_to, $user->email);
        }else{
          $foreman = Approver::where('department',$emp->department)->where('section',$emp->section)->where('remark','Foreman')->first();
          if ($foreman != null) {
            array_push($mail_to, $foreman->approver_email);
          }
        }

        array_push($cc, 'ratri.sulistyorini@music.yamaha.com');
        array_push($cc, 'abdissalam.saidi@music.yamaha.com');
        array_push($cc, 'yayuk.wahyuni@music.yamaha.com');

        Mail::to($mail_to)->cc($cc,'CC')->bcc($bcc,'BCC')->send(new SendEmail($audit[$i], 'audit_qc_koteihyo'));
      }
    }

    $update = DB::connection('ympimis_2')->table('qa_qc_koteihyo_audits')->where('schedule_id',$id)->update([
      'send_status' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $response = array(
      'status' => true,
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

public function handlingAuditQcKoteihyo($id,$employee_id)
{
  $emp_id = strtoupper(Auth::user()->username);
  $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
  $audit = DB::connection('ympimis_2')->select("SELECT
            * 
    FROM
    `qa_qc_koteihyo_audits` 
    WHERE
    ( schedule_id = '".$id."' AND `condition` = 'NG' AND handling IS NULL AND employee_id = '".$employee_id."') ");
  if (count($audit) == 0) {
    $audit = DB::connection('ympimis_2')->select("SELECT
              * 
      FROM
      `qa_qc_koteihyo_audits` 
      WHERE
      ( schedule_id = '".$id."' AND `condition` = 'NG' AND handling IS NULL) ");
  }
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();
  return view('qa.qc_koteihyo.handling')
  ->with('title', 'Penanganan Audit QC Koteihyo')
  ->with('title_jp', 'QC工程表 監査')
  ->with('page', 'Quality Assurance')
  ->with('audit',$audit)
  ->with('emp',$emp)
  ->with('count_audit',count($audit))
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function inputHandlingQcKoteihyo(Request $request)
{
  try {
    $id_handling = $request->get('id_handling');
    $handling = $request->get('handling');
    $handled_id = $request->get('handled_id');
    $handled_name = $request->get('handled_name');

    for ($i=0; $i < count($id_handling); $i++) { 
      $update = DB::connection('ympimis_2')->table('qa_qc_koteihyo_audits')->where('id',$id_handling[$i])->update([
        'handling' => $handling[$i],
        'handled_id' => $handled_id[$i],
        'handled_name' => $handled_name[$i],
        'handled_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }
    $response = array(
      'status' => true,
      'message' => 'Success Input Penanganan'
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

public function indexPacking()
{
  $fy_all = DB::SELECT("SELECT DISTINCT
    ( fiscal_year ) 
    FROM
    weekly_calendars");
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

  $point = DB::connection('ympimis_2')->table('qa_packing_point_checks')->select('product','material_number','material_description')->distinct()->get();

  $point_ei = DB::connection('ympimis_2')->table('qa_production_audit_points')->select('product','material_number','material_description')->distinct()->get();

  return view('qa.packing.index')
  ->with('title', 'Quality Assurance Packing FG / KD Audit')
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('fy_all',$fy_all)
  ->with('emp',$emp)
  ->with('point',$point)
  ->with('point_ei',$point_ei)
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function fetchPacking(Request $request)
{
  try {
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = date('Y-m-01');
      $last = date('Y-m-d');
    }else{
      $first = date('Y-m-01');
      $last = $date_to;
    }
  }else{
   if ($date_to == "") {
    $first = $date_from;
    $last = date('Y-m-d');
  }else{
    $first = $date_from;
    $last = $date_to;
  }
}

$product = $request->get('product');
$material_number = $request->get('material_number');

$products = '';
if ($product != '') {
  $products = "AND product = '".$product."'";
}

$material_numbers = '';
if ($material_number != '') {
  $material_numbers = "AND material_number = '".$material_number."'";
}


$audit = DB::connection('ympimis_2')->SELECT("
  select a.* from ((SELECT
  DATE( created_at ) AS date_audit,
  DATE_FORMAT( created_at, '%d-%b-%Y' ) AS date_audit_name,
  1 as session,
  0 as qty_lot,
  1 as qty_check,
  audit_id,
  1 as qty_auditor,
  product,
  serial_number,
  material_audited,
  material_number,
  material_description,
  GROUP_CONCAT(
  DISTINCT ( send_status )) AS send_status,
  GROUP_CONCAT(
  DISTINCT ( `result_check` )) AS `result_check`,
  GROUP_CONCAT(
  DISTINCT ( handled_id )) AS handled_id,
  GROUP_CONCAT(
  DISTINCT ( due_date )) AS due_date,
  GROUP_CONCAT(
  DISTINCT ( handled_at )) AS handled_at,
  GROUP_CONCAT(
  DISTINCT ( auditor_id )) AS auditor_id,
  GROUP_CONCAT(
  DISTINCT ( auditor_name )) AS auditor_name,
  GROUP_CONCAT(
  DISTINCT ( auditee_id )) AS auditee_id,
  GROUP_CONCAT(
  DISTINCT ( auditee_name )) AS auditee_name 
  FROM
  qa_packing_audits 
  WHERE
  DATE( created_at ) >= '".$first."' 
  AND DATE( created_at ) <= '".$last."' 
  ".$products."
  ".$material_numbers."
  GROUP BY
  `date_audit`,
  qty_lot,
  qty_check,
  qty_auditor,
  session,
  audit_id,
  date_audit_name,
  product,
  serial_number,
  material_audited,
  material_number,
  material_description 
  ORDER BY
  date_audit)
  UNION ALL
  (
  SELECT
  DATE( date ) AS date_audit,
  DATE_FORMAT( date, '%d-%b-%Y' ) AS date_audit_name,
  session,
  qty_lot,
  qty_check,
  audit_id,
  qty_auditor,
  product,
  '' AS serial_number,
  material_audited,
  material_number,
  material_description,
  GROUP_CONCAT(
  DISTINCT ( send_status )) AS send_status,
  IF
  ( sum( qty_ng ) > 0, 'NG', 'OK' ) AS `result_check`,
  GROUP_CONCAT(
  DISTINCT ( handling_id )) AS handled_id,
  GROUP_CONCAT(
  DISTINCT ( due_date )) AS due_date,
  GROUP_CONCAT(
  DISTINCT ( handling_at )) AS handled_at,
  GROUP_CONCAT(
  DISTINCT ( auditor_id )) AS auditor_id,
  GROUP_CONCAT(
  DISTINCT ( auditor_name )) AS auditor_name,
  GROUP_CONCAT(
  DISTINCT ( auditee_id )) AS auditee_id,
  GROUP_CONCAT(
  DISTINCT ( auditee_name )) AS auditee_name 
  FROM
  qa_production_audits 
  WHERE
  DATE( created_at ) >= '".$first."' 
  AND DATE( created_at ) <= '".$last."' 
  ".$products."
  ".$material_numbers."
  GROUP BY
  `date_audit`,
  qty_lot,
  qty_check,
  qty_auditor,
  audit_id,
  session,
  date_audit_name,
  product,
  serial_number,
  material_audited,
  material_number,
  material_description 
  ORDER BY
  date_audit)) a");

$dateTitle = date('d M Y',strtotime($first)).' - '.date('d M Y',strtotime($last));
$response = array(
  'status' => true,
  'audit' => $audit,
  'dateTitle' => $dateTitle,
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

public function indexPackingSendEmail($audit_id)
{
  try {
    $audit = DB::connection('ympimis_2')->select("SELECT
        * 
      FROM
      `qa_packing_audits` 
      WHERE
      audit_id = '".$audit_id."'
      and result_check = 'NG'");


    for ($i=0; $i < count($audit); $i++) { 
      $mail_to = [];
      $cc = [];

      $bcc = ['mokhamad.khamdan.khabibi@music.yamaha.com'];

      //Auditee
      $emp = EmployeeSync::where('employee_id',$audit[$i]->auditee_id)->first();
      $user = User::where('username',$audit[$i]->auditee_id)->first();
      if (str_contains($user->email,'music.yamaha.com')) {
        array_push($mail_to, $user->email);
      }else{
        $foreman = Approver::where('remark','Foreman')->where('department',$emp->department)->where('section',$emp->section)->first();
        if (!$foreman) {
          $foreman = Approver::where('remark','Chief')->where('department',$emp->department)->where('section',$emp->section)->first();
          if ($foreman) {
            array_push($mail_to, $foreman->approver_email);
          }
        }else{
          array_push($mail_to, $foreman->approver_email);
        }
      }
      // if ($emp->department == 'Standardization Department') {
      //   array_push($mail_to, 'agustina.hayati@music.yamaha.com');
      // }else{
      //   if (str_contains($user->email,'music.yamaha.com')) {
      //     array_push($mail_to, $user->email);
      //   }
      // }

      //Auditor
      $emp = EmployeeSync::where('employee_id',$audit[$i]->auditor_id)->first();
      $user = User::where('username',$audit[$i]->auditor_id)->first();
      if (str_contains($user->email,'music.yamaha.com')) {
        array_push($mail_to, $user->email);
      }else{
        $foreman = Approver::where('remark','Foreman')->where('department',$emp->department)->where('section',$emp->section)->first();
        if (!$foreman) {
          $foreman = Approver::where('remark','Chief')->where('department',$emp->department)->where('section',$emp->section)->first();
          if ($foreman) {
            array_push($mail_to, $foreman->approver_email);
          }
        }else{
          array_push($mail_to, $foreman->approver_email);
        }
      }

      $emp = EmployeeSync::where('employee_id',$audit[$i]->auditee_id)->first();
      $manager = Approver::where('department',$emp->department)->where('remark','Manager')->first();
      array_push($cc, $manager->approver_email);

      Mail::to($mail_to)->cc($cc,'CC')->bcc($bcc,'BCC')->send(new SendEmail($audit[$i], 'audit_packing'));
    }

    $update = DB::connection('ympimis_2')->table('qa_packing_audits')->where('audit_id',$audit_id)->update([
      'send_status' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $response = array(
      'status' => true,
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

public function indexPackingPointCheck()
{
  $material = DB::SELECT("SELECT
  * 
    FROM
    materials 
    WHERE
    ( category = 'FG' ) 
    OR ( material_description LIKE '%FHJ%' ) 
    OR ( hpl LIKE '%Case%' ) 
    ORDER BY
    origin_group_code");
  $product = OriginGroup::get();
  $emp_id = strtoupper(Auth::user()->username);
  $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
  return view('qa.packing.point_check')
  ->with('title', 'Quality Assurance Packing FG / KD WI Audit Point Check')
  ->with('product',$product)
  ->with('product2',$product)
  ->with('product3',$product)
  ->with('material',$material)
  ->with('material2',$material)
  ->with('material3',$material)
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('jpn', '品保');
}

public function fetchPackingPointCheck(Request $request)
{
  try {

    $point_check = DB::connection('ympimis_2')->table('qa_packing_point_checks');

    if ($request->get('product') != '') {
      $point_check = $point_check->where('product',$request->get('product'));
    }

    $audit_id = null;

    $stamp_hierarchy = StampHierarchy::get();

    $materials_all = [];
    $materials = null;

    if ($request->get('material_number') != '') {
      $point_check = $point_check->where('material_number','like','%'.$request->get('material_number').'%');

      $date = date('Y-m-d');
      $prefix_now = 'PKQA'.date("y").date("m");
      $code_generator = CodeGenerator::where('note','=','qa_packing')->first();
      if ($prefix_now != $code_generator->prefix){
        $code_generator->prefix = $prefix_now;
        $code_generator->index = '0';
        $code_generator->save();
      }

      $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
      $audit_id = $request->get('material_number').'_'.$code_generator->prefix . $number.'_'.date('Y-m-d H:i:s');
      $code_generator->index = $code_generator->index+1;
      $code_generator->save();
    }

    $point_check = $point_check->orderby('ordering')->get();
    $response = array(
      'status' => true,
      'point_check' => $point_check,
      'audit_id' => $audit_id,
      'stamp_hierarchy' => $stamp_hierarchy,
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

public function inputPackingPointCheck(Request $request)
{
  try {

    $product = $request->get('product');
    $material = $request->get('material');
    $qty_point = $request->get('qty_point');
    $point_check = $request->get('point_check');
    $standard = $request->get('standard');
    $point_check_type = $request->get('point_check_type');
    $point_check_details = $request->get('point_check_details');

    $material_number = [];
    $material_description = [];

    if (!str_contains($material,',')) {
      array_push($material_number, explode('_', $material)[0]);
      array_push($material_description, explode('_', $material)[1]);
    }else{
      $materials = explode(',', $material);
      for ($i=0; $i < count($materials); $i++) { 
        array_push($material_number, explode('_', $materials[$i])[0]);
        array_push($material_description, explode('_', $materials[$i])[1]);
      }
    }

    $mats = join(',',$material_number);
    $matsdesc = join(',',$material_description);

    $index = 1;
    $cek = DB::connection('ympimis_2')->table('qa_packing_point_checks')->where('product',$product)->where('material_number','like',"%".join(',',$material_number)."%")->orderby('ordering','desc')->first();
    if ($cek) {
      $index = $cek->ordering+1;
      $mats = $cek->material_number;
      $matsdesc = $cek->material_description;
    }

    for ($i=0; $i < count($point_check); $i++) { 
      $input = DB::connection('ympimis_2')->table('qa_packing_point_checks')->insert([
        'product' => $product,
        'material_number' => $mats,
        'material_description' => $matsdesc,
        'point_check' => $point_check[$i],
        'standard' => $standard[$i],
        'ordering' => $index,
        'point_check_type' => $point_check_type[$i],
        'point_check_details' => $point_check_details[$i],
        'created_by' => Auth::user()->id,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
      $index++;
    }
    $response = array(
      'status' => true,
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

public function editPackingPointCheck(Request $request)
{
  try {
    $point = DB::connection('ympimis_2')->table('qa_packing_point_checks')->where('id',$request->get('id'))->first();
    $response = array(
      'status' => true,
      'point' => $point
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

public function deletePackingPointCheck(Request $request)
{
  try {
    $point = DB::connection('ympimis_2')->table('qa_packing_point_checks')->where('id',$request->get('id'))->first();

    $product = $point->product;
    $material_number = $point->material_number;

    $delete = DB::connection('ympimis_2')->table('qa_packing_point_checks')->where('id',$request->get('id'))->delete();

    $adjust_point = DB::connection('ympimis_2')->table('qa_packing_point_checks')->where('product',$product)->where('material_number',$material_number)->get();
    if (count($adjust_point) > 0) {
      $index = 1;
      for ($i=0; $i < count($adjust_point); $i++) { 
        $update = DB::connection('ympimis_2')->table('qa_packing_point_checks')->where('id',$adjust_point[$i]->id)->update([
          'ordering' => $index
        ]);
        $index++;
      }
    }
    $response = array(
      'status' => true,
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

public function updatePackingPointCheck(Request $request)
{
  try {
    $id = $request->get('id');
    $point_check = $request->get('point_check');
    $standard = $request->get('standard');
    $point_check_type = $request->get('point_check_type');
    $point_check_details = $request->get('point_check_details');

    $update = DB::connection('ympimis_2')->table('qa_packing_point_checks')->where('id',$id)->update([
      'point_check' => $point_check,
      'standard' => $standard,
      'point_check_type' => $point_check_type,
      'point_check_details' => $point_check_details,
      'updated_at' => date('Y-m-d H:i:s')
    ]);
    $response = array(
      'status' => true,
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

public function indexPackingAudit()
{
  $point = DB::connection('ympimis_2')->table('qa_packing_point_checks')->select('product','material_number','material_description')->distinct()->get();

  $emp_all = EmployeeSync::where('end_date',null)->get();

  $auditor = EmployeeSync::where('end_date',null)->whereIn('employee_id',['PI0508002',
    'PI1503003',
    'PI1305001',
    'PI1412006',
    'PI1011015',
    'PI1307004',
    'PI1307009',
    'PI1605002',
    'PI0101030',
    'PI0107011',
    'PI0102010',
    'PI1807030',
    'PI0202017',
    'PI0211001',
    'PI0210001',
    'PI1807031',
    'PI1104003',
    'PI1807031'
  ])->get();

  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();
  return view('qa.packing.audit')
  ->with('title', 'Quality Assurance Audit Packing FG / KD WI')
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('emp',$emp)
  ->with('emp_all',$emp_all)
  ->with('auditor',$auditor)
  ->with('point',$point)
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function fetchPackingSerialNumber(Request $request)
{
  try {
    $product = $request->get('product');
    $serial_number = $request->get('serial_number');

    $auditee = DB::connection('ympimis_2')->table('packing_documentations')->where('serial_number',$serial_number)->where('location',$product)->first();
    $response = array(
      'status' => true,
      'auditee' => $auditee,
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

public function inputPackingAudit(Request $request)
{
  try {
    $auditor_id = $request->get('auditor_id');
    $auditor_name = $request->get('auditor_name');
    $auditee_id = $request->get('auditee_id');
    $auditee_name = $request->get('auditee_name');
    $material_number = $request->get('material_number');
    $material_description = $request->get('material_description');
    $product = $request->get('product');
    $point_id = $request->get('point_id');
    $point_check_details = $request->get('point_check_details');
    $point_check_type = $request->get('point_check_type');
    $ordering = $request->get('ordering');
    $point_check = $request->get('point_check');
    $results = $request->get('results');
    $condition = $request->get('condition');
    $standard = $request->get('standard');
    $audit_id = $request->get('audit_id');
    $note = $request->get('note');
    $material_audited = $request->get('material_audited');
    $serial_number = $request->get('serial_number');

    $input = DB::connection('ympimis_2')->table('qa_packing_audits')->insert([
      'auditor_id' => $auditor_id,
      'auditor_name' => $auditor_name,
      'audit_id' => $audit_id,
      'auditee_id' => $auditee_id,
      'auditee_name' => $auditee_name,
      'ordering' => $ordering,
      'point_check' => $point_check,
      'material_number' => $material_number,
      'material_description' => $material_description,
      'product' => $product,
      'point_check_id' => $point_id,
      'point_check_details' => $point_check_details,
      'point_check_type' => $point_check_type,
      'standard' => $standard,
      'result_check' => $condition,
      'result_details' => $results,
      'note' => $note,
      'material_audited' => $material_audited,
      'due_date' => date('Y-m-d', strtotime('+7 day')),
      'serial_number' => $serial_number,
      'created_by' => Auth::user()->id,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);
    $response = array(
      'status' => true,
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

public function indexPackingHandling($audit_id)
{
  $audit = DB::connection('ympimis_2')->table('qa_packing_audits')->where('audit_id',$audit_id)->get();

  if (count($audit) > 0) {
    $audit = DB::connection('ympimis_2')->table('qa_packing_audits')->where('audit_id',$audit_id)->where('result_check','NG')->get();
    $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();
    return view('qa.packing.handling')
    ->with('title', 'Quality Assurance ~ Penanganan Audit Packing')
    ->with('title_jp', '品証 工程監査')
    ->with('page', 'Quality Assurance')
    ->with('emp',$emp)
    ->with('audit',$audit)
    ->with('count_audit',count($audit))
    ->with('role',Auth::user()->role_code)
    ->with('employee_id',Auth::user()->username)
    ->with('jpn', '品保');
  }else{
    $audit = DB::connection('ympimis_2')->table('qa_production_audits')->where('audit_id',$audit_id)->where('qty_ng','>','0')->get();
    $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();
    return view('qa.audit_fg.handling')
    ->with('title', 'Quality Assurance ~ Penanganan Audit FG / KD')
    ->with('title_jp', '品証 工程監査')
    ->with('page', 'Quality Assurance')
    ->with('emp',$emp)
    ->with('audit',$audit)
    ->with('count_audit',count($audit))
    ->with('role',Auth::user()->role_code)
    ->with('employee_id',Auth::user()->username)
    ->with('jpn', '品保');
  }
}

public function indexAuditFGEdit($audit_id)
{
  $audit = DB::connection('ympimis_2')->table('qa_production_audits')->where('audit_id',$audit_id)->first();
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

  $audit_ng = DB::connection('ympimis_2')->table('qa_production_audits')->where('audit_id',$audit_id)->where('qty_ng','>','0')->get();

  $auditor = EmployeeSync::where('end_date',null)->whereIn('employee_id',[
    'PI0101002',
    'PI0703001',
    'PI1308012',
    'PI0104001',
    'PI0104004',
    'PI1307007',
    'PI1307016',
  ])->get();

  $emp_groups = DB::SELECT("SELECT
    employee_syncs.employee_id,
    employee_syncs.`name`,
    employee_groups.`group` 
    FROM
    employee_groups
    JOIN employee_syncs ON employee_syncs.employee_id = employee_groups.employee_id 
    WHERE
    location = 'rc-assy' 
    AND employee_groups.created_at IS NOT NULL");

  return view('qa.audit_fg.edit')
  ->with('title', 'Quality Assurance ~ Edit Data Audit FG')
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('emp',$emp)
  ->with('audit',$audit)
  ->with('audit_ng',$audit_ng)
  ->with('audit_id',$audit_id)
  ->with('emp_groups',$emp_groups)
  ->with('emp_groups2',$emp_groups)
  ->with('emp_groups3',$emp_groups)
  ->with('auditor',$auditor)
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function indexAuditFGUpdate(Request $request)
{
  try {
    $audit_id = $request->get('audit_id');
    $qty_check = $request->get('qty_check');
    $qty_lot = $request->get('qty_lot');
    $date = $request->get('date');
    $auditor = $request->get('auditor');
    $session = $request->get('session');
    $qty_auditor = $request->get('qty_auditor');

    if (str_contains($auditor,',')) {
      $auditor_all = explode(',', $auditor);
      $auditors = [];
      for ($i=0; $i < count($auditor_all); $i++) { 
        $auditorss = EmployeeSync::where('employee_id',$auditor_all[$i])->first();
        array_push($auditors, $auditorss->name);
      }
      $auditor_name = join(',',$auditors);
    }else{
      $auditorss = EmployeeSync::where('employee_id',$auditor)->first();
      $auditor_name = $auditorss->name;
    }

    $box_qty = $request->get('box_qty');
    $box_pic = $request->get('box_pic');

    $update = DB::CONNECTION('ympimis_2')->table('qa_production_audits')->where('audit_id',$audit_id)->update([
      'qty_check' => $qty_check,
      'qty_lot' => $qty_lot,
      'date' => $date,
      'auditor_id' => $auditor,
      'auditor_name' => $auditor_name,
      'session' => $session,
      'qty_auditor' => $qty_auditor,
      'box_qty' => $box_qty,
      'box_pic' => $box_pic,
      'edited_at' =>date('Y-m-d H:i:s'),
      'edited_id' =>Auth::user()->name,
      'edited_name' =>Auth::user()->username,
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $note = $request->get('note');
    $id_ng = $request->get('id_ng');
    if (count($note) > 0) {
      for ($i=0; $i < count($note); $i++) { 
        $update = DB::CONNECTION('ympimis_2')->table('qa_production_audits')->where('id',$id_ng[$i])->update([
          'note' => $note[$i],
          'edited_at' =>date('Y-m-d H:i:s'),
          'edited_id' =>Auth::user()->name,
          'edited_name' =>Auth::user()->username,
          'updated_at' => date('Y-m-d H:i:s'),
        ]);                  
      }
    }

    $response = array(
      'status' => true,
      'message' => 'Success Update Data',
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

public function inputPackingHandling(Request $request)
{
  try {
    $id_handling = $request->get('id_handling');
    $handling = $request->get('handling');
    $handled_id = $request->get('handled_id');
    $handled_name = $request->get('handled_name');

    for ($i=0; $i < count($id_handling); $i++) { 
      $update = DB::connection('ympimis_2')->table('qa_packing_audits')->where('id',$id_handling[$i])->update([
        'handling' => $handling[$i],
        'handled_id' => $handled_id[$i],
        'handled_name' => $handled_name[$i],
        'handled_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }
    $response = array(
      'status' => true,
      'message' => 'Success Input Penanganan'
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

public function indexPackingPdf($id)
{
  $audit = DB::connection('ympimis_2')->table('qa_packing_audits')->where('audit_id',$id)->orderBy('ordering','asc')->get();
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

  $pdf = \App::make('dompdf.wrapper');
  $pdf->getDomPDF()->set_option("enable_php", true);
  $pdf->setPaper('A4', 'landscape');

  $pdf->loadView('qa.packing.pdf_audit', array(
    'audit' => $audit,
  ));

  return $pdf->stream($audit[0]->audit_id." - ".$audit[0]->material_description." (".date('Y-m-d',strtotime($audit[0]->created_at)).").pdf");

}

public function fetchPackingDetail(Request $request)
{
  try {
    $audit_id = $request->get('audit_id');

    $audit = DB::connection('ympimis_2')->table('qa_packing_audits')->where('audit_id',$audit_id)->orderby('ordering')->get();
    $response = array(
      'status' => true,
      'audit' => $audit,
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

public function fetchAuditFGDetail(Request $request)
{
  try {
    $audit_id = $request->get('audit_id');

    $audit = DB::connection('ympimis_2')->table('qa_production_audits')->where('audit_id',$audit_id)->orderby('ordering')->get();
    $response = array(
      'status' => true,
      'audit' => $audit,
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

public function indexAuditCparCar()
{
  $fy_all = DB::SELECT("SELECT DISTINCT
    ( fiscal_year ) 
    FROM
    weekly_calendars");
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

  $point = DB::table('audit_external_claim_points')->select('audit_id','audit_title')->where('remark','cpar_car')->distinct()->get();

  $fy_all = DB::SELECT("SELECT DISTINCT
    ( fiscal_year ) 
    FROM
    weekly_calendars");

  return view('qa.cpar_car.index')
  ->with('title', 'Quality Assurance CPAR & CAR Audit')
  ->with('title_jp', '品証 是正予防策・是正策監視 監査')
  ->with('page', 'Quality Assurance')
  ->with('fy_all',$fy_all)
  ->with('emp',$emp)
  ->with('point',$point)
  ->with('fy_all',$fy_all)
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function fetchAuditCparCar(Request $request)
{
  try {
    if ($request->get('fy') == '') {
      $fys = "(
      SELECT DISTINCT
      ( fiscal_year ) 
      FROM
      weekly_calendars 
      WHERE
      week_date = DATE(
      NOW()))";
    }else{
      $fys = "'".$request->get('fy')."'";
    }
    $fy = DB::SELECT("SELECT DISTINCT
      (
      DATE_FORMAT( week_date, '%Y-%m' )) AS `month`,
      DATE_FORMAT( week_date, '%b %Y' ) AS month_name 
      FROM
      weekly_calendars 
      WHERE
      fiscal_year = ".$fys." 
      ORDER BY
      week_date");

    $fyss = DB::SELECT("( SELECT DISTINCT
      (
      DATE_FORMAT( week_date, '%Y-%m' )) AS `month`,
      DATE_FORMAT( week_date, '%b %Y' ) AS month_name 
      FROM
      weekly_calendars 
      WHERE
      fiscal_year = ".$fys."  
      ORDER BY
      week_date 
      LIMIT 1 
      ) UNION ALL
      (
      SELECT DISTINCT
      (
      DATE_FORMAT( week_date, '%Y-%m' )) AS `month`,
      DATE_FORMAT( week_date, '%b %Y' ) AS month_name 
      FROM
      weekly_calendars 
      WHERE
      fiscal_year = ".$fys." 
      ORDER BY
      week_date DESC 
      LIMIT 1)");

    $audit = DB::SELECT("SELECT
      DATE_FORMAT( schedule_date, '%Y-%m' ) AS `month`,
      DATE_FORMAT( schedule_date, '%b-%Y' ) AS `month_name`,
      audit_external_claim_schedules.id AS id_schedule,
      audit_external_claim_schedules.employee_id AS employee_id,
      employee_syncs.`name`,
      audit_external_claim_schedules.audit_id AS audit_id,
      point.audit_title,
      audit.result_check,
      schedule_date,
      audit.handled_by,
      schedule_status,
      audit.send_status,
      point.product,
      point.area
      FROM
      `audit_external_claim_schedules`
      JOIN ( SELECT DISTINCT ( audit_id ), audit_title,product,area FROM audit_external_claim_points ) AS point ON audit_external_claim_schedules.audit_id = point.audit_id
      JOIN employee_syncs ON employee_syncs.employee_id = audit_external_claim_schedules.employee_id
      LEFT JOIN (
      SELECT DISTINCT
      ( schedule_id ),
      GROUP_CONCAT(
      DISTINCT ( send_status )) AS send_status,
      GROUP_CONCAT(
      DISTINCT ( result_check )) AS result_check,
      GROUP_CONCAT(
      DISTINCT ( handled_by )) AS handled_by,
      GROUP_CONCAT(
      DISTINCT ( handled_at )) AS handled_at 
      FROM
      audit_external_claims 
      WHERE
      remark = 'cpar_car' 
      GROUP BY
      schedule_id 
      ) audit ON audit.schedule_id = audit_external_claim_schedules.id 
      WHERE
      remark = 'cpar_car' 
      AND DATE_FORMAT( schedule_date, '%Y-%m' ) >= '".$fyss[0]->month."'
      and DATE_FORMAT( schedule_date, '%Y-%m' ) <= '".$fyss[1]->month."'
      GROUP BY
      `month`,
      month_name,
      id_schedule,
      schedule_status,
      audit.result_check,
      audit.send_status,
      audit.handled_by,
      audit_external_claim_schedules.employee_id,
      point.audit_title,
      employee_syncs.`name`,
      audit_external_claim_schedules.audit_id,
      point.product,
      schedule_date,
      point.area");

    $response = array(
      'status' => true,
      'fy' => $fyss,
      'fy_all' => $fy,
      'audit' => $audit
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

public function inputAuditCparCar(Request $request)
{
  try {
    $filename = null;
    if (count($request->file('fileData')) > 0) {
      $tujuan_upload = 'data_file/qa/cpar_car';
      $file = $request->file('fileData');
      $filename = $request->get('schedule_id').'_'.$request->get('audit_id').'_'.$request->get('audit_index').'_'.date('YmdHisa').'.'.$request->get('extension');
      $file->move($tujuan_upload,$filename);
    }

    $audit_id = $request->get('audit_id');
    $schedule_id = $request->get('schedule_id');
    $result_check = $request->get('result_check');
    $note = $request->get('note');
    $audit_index = $request->get('audit_index');
    $auditor = $request->get('auditor');
    $chief_foreman = $request->get('chief_foreman');
    $manager = $request->get('manager');

    $schedule = db::table('audit_external_claim_schedules')->where('id',$schedule_id)->first();
    $point = db::table('audit_external_claim_points')->where('audit_id',$audit_id)->where('audit_index',$audit_index)->first();

    $auditdata = AuditExternalClaim::create([
      'schedule_id' => $schedule_id,
      'audit_id' => $audit_id,
      'audit_title' => $point->audit_title,
      'periode' => $point->periode,
      'email_date' => $point->email_date,
      'incident_date' => $point->incident_date,
      'origin' => $point->origin,
      'department' => $point->department,
      'area' => $point->area,
      'product' => $point->product,
      'audit_index' => $point->audit_index,
      'audit_point' => $point->audit_point,
      'audit_images' => $point->audit_images,
      'auditor' => $auditor,
      'result_check' => $result_check,
      'chief_foreman' => explode('_', $chief_foreman)[2],
      'manager' => explode('_', $manager)[2],
      'note' => $note,
      'remark' => 'cpar_car',
      'result_image' => $filename,
      'created_by' => Auth::user()->id
    ]);

    $schedule = DB::table('audit_external_claim_schedules')->where('id',$schedule_id)->first();
    if ($schedule) {
      $updateschedule = DB::table('audit_external_claim_schedules')->where('id',$schedule_id)->update([
        'schedule_status' => 'Sudah Dikerjakan',
        'updated_at' => date('Y-m-d H:i:s')
      ]);
    }

    $response = array(
      'status' => true,
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

public function indexAuditCparCarPointCheck()
{
  $audit_id = DB::table('audit_external_claim_points')->select('audit_id','audit_title')->distinct()->where('remark','cpar_car')->get();
  $department = Department::get();
  $emp_id = strtoupper(Auth::user()->username);
  $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
  return view('qa.cpar_car.point_check')
  ->with('title', 'Quality Assurance CPAR & CAR Audit Point Check')
  ->with('title_jp', '品証 是正予防策・是正策監視 監査')
  ->with('page', 'Quality Assurance')
  ->with('jpn', '品保')
  ->with('audit_id',$audit_id)
  ->with('audit_id2',$audit_id)
  ->with('department',$department)
  ->with('department2',$department);
}

public function fetchAuditCparCarPointCheck(Request $request)
{
  try {
    $point_check = DB::table('audit_external_claim_points');

    if ($request->get('audit_id') != '') {
      $point_check = $point_check->where('audit_id',$request->get('audit_id'));
    }
    $point_check = $point_check->orderby('audit_id');
    $point_check = $point_check->orderby('audit_index')->where('remark','cpar_car')->get();

    $department = Department::get();
    $response = array(
      'status' => true,
      'point_check' => $point_check,
      'department' => $department,
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

public function inputAuditCparCarPointCheck(Request $request)
{
  try {
    $claim_condition = $request->get('claim_condition');

    if ($claim_condition == 'NEW') {
      $filename = null;

      $code_generator = CodeGenerator::where('note', '=', 'qa_claim')->first();
      $audit_id = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
      $code_generator->index = $code_generator->index+1;

      $audit = DB::table('audit_external_claim_points')->where('audit_title',$request->get('audit_title'))->orderBy('id','desc')->first();
      $audit_index = 1;
      if ($audit) {
        $audit_id = $audit->audit_id;
        $audit_index = $audit->audit_index+1;
      }else{
        $code_generator->save();
      }

      $audit_title = $request->get('audit_title');
      $periode = $request->get('periode');
      $email_date = $request->get('email_date');
      $incident_date = $request->get('incident_date');
      $origin = $request->get('origin');
      $department = $request->get('department');
      $area = $request->get('area');
      $proses = $request->get('proses');
      $product = $request->get('product');
      $audit_point = $request->get('audit_point');
      $extension = $request->get('extension');

      if (count($request->file('fileData')) > 0) {
        $tujuan_upload = 'data_file/qa/ng_jelas_point';
        $file = $request->file('fileData');
        $filename = $audit_id.'_'.$audit_index.'.'.$extension;
        $file->move($tujuan_upload,$filename);
      }

      $input = DB::table('audit_external_claim_points')->insert([
        'audit_id' => $audit_id,
        'audit_title' => $audit_title,
        'periode' => $periode,
        'email_date' => $email_date,
        'incident_date' => $incident_date,
        'origin' => $origin,
        'department' => $department,
        'area' => $area,
        'product' => $product,
        'audit_index' => $audit_index,
        'proses' => $proses,
        'audit_point' => $audit_point,
        'audit_images' => $filename,
        'remark' => 'cpar_car',
        'created_by' => Auth::user()->id,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }else{
      $audit_id = $request->get('audit_id');
      $audit_title = $request->get('audit_title');
      $audit_point = $request->get('audit_point');
      $extension = $request->get('extension');

      $audit = DB::table('audit_external_claim_points')->where('audit_id',$audit_id)->orderBy('id','desc')->first();
      $audit_index = $audit->audit_index+1;

      $filename = null;

      if (count($request->file('fileData')) > 0) {
        $tujuan_upload = 'data_file/qa/ng_jelas_point';
        $file = $request->file('fileData');
        $filename = $audit_id.'_'.$audit_index.'.'.$extension;
        $file->move($tujuan_upload,$filename);
      }

      $input = DB::table('audit_external_claim_points')->insert([
        'audit_id' => $audit_id,
        'audit_title' => $audit_title,
        'periode' => $audit->periode,
        'email_date' => $audit->email_date,
        'incident_date' => $audit->incident_date,
        'origin' => $audit->origin,
        'department' => $audit->department,
        'area' => $audit->area,
        'product' => $audit->product,
        'audit_index' => $audit_index,
        'proses' => $audit->proses,
        'audit_point' => $audit_point,
        'audit_images' => $filename,
        'remark' => 'cpar_car',
        'created_by' => Auth::user()->id,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }

    $response = array(
      'status' => true,
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

public function deleteAuditCparCarPointCheck(Request $request)
{
  try {
    $id = $request->get('id');

    $data = DB::table('audit_external_claim_points')->where('id',$id)->first();
    if ($data) {
      $audit_id = $data->audit_id;
      $delete = DB::table('audit_external_claim_points')->where('id',$id)->delete();
    }

    $audit = DB::table('audit_external_claim_points')->where('audit_id',$audit_id)->get();
    if (count($audit) > 0) {
      $index = 1;
      for ($i=0; $i < count($audit); $i++) { 
        $update = DB::table('audit_external_claim_points')->where('id',$audit[$i]->id)->update([
          'audit_index' => $index
        ]);

        $index++;
      }
    }
    $response = array(
      'status' => true,
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

public function editAuditCparCarPointCheck(Request $request)
{
  try {
    $id = $request->get('id');

    $data = DB::table('audit_external_claim_points')->where('id',$id)->first();

    $response = array(
      'status' => true,
      'audit' => $data
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

public function updateAuditCparCarPointCheck(Request $request)
{
  try {
    $id = $request->get('id');
    $audit_point = $request->get('audit_point');
    $extension = $request->get('extension');

    $filename = null;

    $audit = DB::table('audit_external_claim_points')->where('id',$id)->first();

    if (count($request->file('fileData')) > 0) {
      $tujuan_upload = 'data_file/qa/ng_jelas_point';
      $file = $request->file('fileData');
      $filename = $audit->audit_id.'_'.$audit->audit_index.'.'.$extension;
      $filePath = $tujuan_upload.'/'.$filename;

      if (file_exists($filePath)) {
        unlink($filePath);
      }
      $file->move($tujuan_upload,$filename);
    }else{
      $filename = $request->get('filenames');
    }

    $update = DB::table('audit_external_claim_points')->where('id',$id)->update([
      'audit_point' => $audit_point,
      'audit_images' => $filename,
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $response = array(
      'status' => true
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

public function indexPackingReport()
{
  $point = DB::connection('ympimis_2')->table('qa_packing_point_checks')->select('product','material_number','material_description')->distinct()->get();

  $sn = DB::connection('ympimis_2')->table('qa_packing_audits')->select('product','serial_number')->where('serial_number','!=','Non-Serial-Number')->distinct()->get();

  return view('qa.packing.report')
  ->with('title', 'Quality Assurance Audit Packing Report')
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('point',$point)
  ->with('sn',$sn)
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function fetchPackingReport(Request $request)
{
  try {
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = date('Y-m-d');
      $last = date('Y-m-d');
    }else{
      $first = date('Y-m-d');
      $last = $date_to;
    }
  }else{
   if ($date_to == "") {
    $first = $date_from;
    $last = date('Y-m-d');
  }else{
    $first = $date_from;
    $last = $date_to;
  }
}
$report = DB::connection('ympimis_2')->table('qa_packing_audits');
$report = $report->where(DB::RAW('date(created_at)'),'>=',$first)->where(DB::RAW('date(created_at)'),'<=',$last);

if ($request->get('serial_number') != '') {
  $report = $report->where('product',explode('_', $request->get('serial_number'))[0])->where('serial_number',explode('_', $request->get('serial_number'))[1]);
}

if ($request->get('material_number') != '') {
  $report = $report->where('material_number',$request->get('material_number'));
}

if ($request->get('result_check') != '') {
  $report = $report->where('result_check',$request->get('result_check'));
}

$report = $report->get();
$response = array(
  'status' => true,
  'report' => $report,
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

public function indexAuditFGReport($product)
{
  $point = DB::connection('ympimis_2')->table('qa_production_audits')->select('product','material_number','material_description')->where('product',ucwords($product))->distinct()->get();

  return view('qa.audit_fg.report')
  ->with('title', 'Quality Assurance Audit FG / KD Report')
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('point',$point)
  ->with('product',$product)
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function fetchAuditFGReport(Request $request)
{
  try {
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = date('Y-m-d');
      $last = date('Y-m-d');
    }else{
      $first = date('Y-m-d');
      $last = $date_to;
    }
  }else{
   if ($date_to == "") {
    $first = $date_from;
    $last = date('Y-m-d');
  }else{
    $first = $date_from;
    $last = $date_to;
  }
}
$material = "";
if ($request->get('material_number') != '') {
  $material = "AND material_number = '".$request->get('material_number')."'";
}
$report = DB::CONNECTION('ympimis_2')->select("SELECT
  qa_production_audits.audit_id,
  date,
  material_audited,
  qty_lot,
  qty_check,
  sum(qty_ng) as qty_ng,
  ROUND(( sum( qty_ng ) / qty_check ) * 100 ) AS ng_ratio,
  100-ROUND (( sum( qty_ng ) / qty_check ) * 100 ) AS pass_ratio,
  GROUP_CONCAT(
  DISTINCT ( auditor_name )) AS auditor,
  GROUP_CONCAT(
  DISTINCT ( status_lot )) AS status_lot,
  GROUP_CONCAT(
  DISTINCT ( note )) AS note,
  ng.ng_name,
  ng.area,
  ng.pallet,
  ng.box,
  ng.line,
  ng.emp,
  ng.defect_category,
  ng.cavity_ng
  FROM
  qa_production_audits
  LEFT JOIN (
  SELECT
  audit_id,
  GROUP_CONCAT(
  SPLIT_STRING ( ng_detail, '_', 1 )) AS ng_name,
  GROUP_CONCAT(
  SPLIT_STRING ( ng_detail, '_', 2 )) AS area,
  GROUP_CONCAT(
  SPLIT_STRING ( ng_detail, '_', 3 )) AS pallet,
  GROUP_CONCAT(
  SPLIT_STRING ( ng_detail, '_', 4 )) AS baris,
  GROUP_CONCAT(
  SPLIT_STRING ( ng_detail, '_', 5 )) AS box,
  GROUP_CONCAT(
  SPLIT_STRING ( ng_detail, '_', 6 )) AS line,
  GROUP_CONCAT(
  SPLIT_STRING ( ng_detail, '_', 8 )) AS emp,
  GROUP_CONCAT(
  DISTINCT ( defect_category )) AS defect_category,
  GROUP_CONCAT(
  DISTINCT ( cavity_ng )) AS cavity_ng
  FROM
  qa_production_audits 
  WHERE
  product = '".ucwords($request->get('product'))."' 
  AND qty_ng > 0 
  GROUP BY
  audit_id 
  ) AS ng ON ng.audit_id = qa_production_audits.audit_id 
  WHERE
  product = '".ucwords($request->get('product'))."' 
  AND date >= '".$first."'
  AND date <= '".$last."'
  ".$material."
  GROUP BY
  qa_production_audits.audit_id,
  date,
  material_audited,
  qty_lot,
  qty_check,
  ng.ng_name,
  ng.area,
  ng.pallet,
  ng.box,
  ng.line,
  ng.emp,
  ng.defect_category,
  ng.cavity_ng
  ORDER BY date");
$response = array(
  'status' => true,
  'report' => $report,
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

public function indexAuditCparCarSchedule()
{
  $audit_id = DB::table('audit_external_claim_points')->select('audit_id','audit_title')->distinct()->where('remark','cpar_car')->get();
  $emp = EmployeeSync::where('end_date',null)->get();
  return view('qa.cpar_car.schedule')
  ->with('title', 'Quality Assurance CPAR & CAR Audit Schedule')
  ->with('title_jp', '品証 是正予防策・是正策監視 監査')
  ->with('page', 'Quality Assurance')
  ->with('jpn', '品保')
  ->with('audit_id',$audit_id)
  ->with('audit_id2',$audit_id)
  ->with('audit_id3',$audit_id)
  ->with('emp',$emp)
  ->with('emp2',$emp);
}

public function fetchAuditCparCarSchedule(Request $request)
{
  try {
    $audit_id = '';
    if ($request->get('audit_id') != '') {
      $audit_id = "AND audit_external_claim_schedules.audit_id = '".$request->get('audit_id')."'";
    }
    $schedule = DB::select("SELECT
      audit_external_claim_schedules.*,
      point.audit_title,
      employee_syncs.`name` 
      FROM
      audit_external_claim_schedules
      JOIN ( SELECT DISTINCT ( audit_id ), audit_title FROM audit_external_claim_points ) AS point ON audit_external_claim_schedules.audit_id = point.audit_id
      JOIN employee_syncs ON employee_syncs.employee_id = audit_external_claim_schedules.employee_id 
      WHERE
      audit_external_claim_schedules.remark = 'cpar_car' 
      ".$audit_id."
      ORDER BY
      schedule_date");

    $response = array(
      'status' => true,
      'schedule' => $schedule,
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

public function inputAuditCparCarSchedule(Request $request)
{
  try {
    $audit_id = $request->get('audit_id');
    $employee_id = $request->get('employee_id');
    $schedule_date = $request->get('schedule_date');

    $input = DB::table('audit_external_claim_schedules')->insert([
      'audit_id' => $audit_id,
      'employee_id' => $employee_id,
      'schedule_date' => $schedule_date.'-01',
      'schedule_status' => 'Belum Dikerjakan',
      'remark' => 'cpar_car',
      'created_by' => Auth::user()->id,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);
    $response = array(
      'status' => true,
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

public function updateAuditCparCarSchedule(Request $request)
{
  try {
    $id = $request->get('id');
    $audit_id = $request->get('audit_id');
    $employee_id = $request->get('employee_id');
    $schedule_date = $request->get('schedule_date');

    $update = DB::table('audit_external_claim_schedules')->where('id',$id)->update([
      'audit_id' => $audit_id,
      'employee_id' => $employee_id,
      'schedule_date' => $schedule_date.'-01',
      'updated_at' => date('Y-m-d H:i:s'),
    ]);
    $response = array(
      'status' => true,
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

public function deleteAuditCparCarSchedule(Request $request)
{
  try {
    $id = $request->get('id');

    $audit = DB::table('audit_external_claim_schedules')->where('id',$id)->first();
    if ($audit) {
      $delete = DB::table('audit_external_claim_schedules')->where('id',$id)->delete();
    }
    $response = array(
      'status' => true,
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

public function indexAuditCparCarAudit($schedule_id)
{
  $emp_id = strtoupper(Auth::user()->username);
  $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
  $schedule = DB::table('audit_external_claim_schedules')->where('id',$schedule_id)->first();
  $point_check = DB::table('audit_external_claim_points')->where('audit_id',$schedule->audit_id)->orderby('audit_index')->get();

  $chief_foreman = DB::select("SELECT
      * 
    FROM
    approvers 
    WHERE
    department = '".$point_check[0]->department."' 
    AND remark IN (
    'Manager',
    'Chief',
    'Foreman' 
  )");

  $foreman = '';
  $manager = '';
  for ($i=0; $i < count($chief_foreman); $i++) { 
    if (str_contains($point_check[0]->product,'YFL')) {
      if ($chief_foreman[$i]->section == 'Assembly FL Process Section') {
        if ($chief_foreman[$i]->remark == 'Foreman') {
          $foreman = $chief_foreman[$i]->approver_id.'_'.$chief_foreman[$i]->approver_name.'_'.$chief_foreman[$i]->approver_email;
        }
      }
    }else if (str_contains($point_check[0]->product,'YAS') || str_contains($point_check[0]->product,'YTS')) {
      if ($chief_foreman[$i]->section == 'Assembly Sax Process Section') {
        if ($chief_foreman[$i]->remark == 'Foreman') {
          $foreman = $chief_foreman[$i]->approver_id.'_'.$chief_foreman[$i]->approver_name.'_'.$chief_foreman[$i]->approver_email;
        }
      }
    }else{
      if ($chief_foreman[$i]->remark == 'Foreman') {
        $foreman = $chief_foreman[$i]->approver_id.'_'.$chief_foreman[$i]->approver_name.'_'.$chief_foreman[$i]->approver_email;
      }
      if ($chief_foreman[$i]->remark == 'Chief') {
        $foreman = $chief_foreman[$i]->approver_id.'_'.$chief_foreman[$i]->approver_name.'_'.$chief_foreman[$i]->approver_email;
      }
    }

    if ($chief_foreman[$i]->remark == 'Manager') {
      $manager = $chief_foreman[$i]->approver_id.'_'.$chief_foreman[$i]->approver_name.'_'.$chief_foreman[$i]->approver_email;
    }
  }

  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

  return view('qa.cpar_car.audit')
  ->with('title', 'Quality Assurance Audit CPAR & CAR')
  ->with('title_jp', '品証 是正予防策・是正策監視 監査')
  ->with('page', 'Quality Assurance')
  ->with('schedule',$schedule)
  ->with('point_check',$point_check)
  ->with('foreman',$foreman)
  ->with('manager',$manager)
  ->with('emp',$emp)
  ->with('role',Auth::user()->role_code)
  ->with('jpn', '品保');
}

public function indexAuditCparCarHandling($schedule_id)
{
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

  $audit = DB::table('audit_external_claims')->select('audit_external_claims.*','employee_syncs.name')->where('schedule_id',$schedule_id)->join('employee_syncs','employee_syncs.employee_id','auditor')->where('result_check','NG')->get();

  if (count($audit) > 0) {
    return view('qa.cpar_car.handling')
    ->with('title', 'Quality Assurance CPAR & CAR Audit')
    ->with('title_jp', '品証 是正予防策・是正策監視 監査')
    ->with('page', 'Quality Assurance')
    ->with('emp',$emp)
    ->with('audit',$audit)
    ->with('role',Auth::user()->role_code)
    ->with('employee_id',Auth::user()->username);
  }else{
    alert('Penanganan Sudah Diinput');
    return redirect('/');
  }
}

public function indexKensa($location)
{
  $inspection_level = QaInspectionLevel::select('inspection_level')->distinct()->get();
  $nglists = NgList::where('location', $location)->where('remark', $location)->get();

  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

  $material = db::connection('tpro')->select('SELECT product_gmc as material_number, product_name as material_description from m_product order by product_name ASC');

  return view('qa.qa_kpp.index_check')
  ->with('ng_lists', $nglists)
  ->with('inspection_level', $inspection_level)
  ->with('materials', $material)
  ->with('location', $location)
  ->with('emp', $emp)
  ->with('title', 'KPP Check QA')
  ->with('title_jp', '')
  ->with('page', 'Quality Assurance');
}

public function inputAuditCparCarHandling(Request $request)
{
  try {

    $id_handling = $request->get('id_handling');
    $handling = $request->get('handling');
    $handled_id = $request->get('handled_id');
    $handled_name = $request->get('handled_name');

    for ($i=0; $i < count($id_handling); $i++) { 
      $update = DB::table('audit_external_claims')->where('id',$id_handling[$i])->update([
        'handling' => $handling[$i],
        'handled_by' => $handled_id[$i].' - '.$handled_name[$i],
        'handled_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }

    $response = array(
      'status' => true,
      'message' => 'Success Input Penanganan'
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

public function indexAuditCparCarSendEmail($schedule_id)
{
  try {
    $audit = DB::select("SELECT
        * 
      FROM
      `audit_external_claims` 
      WHERE
      schedule_id = '".$schedule_id."'
      and result_check = 'NG'");


    for ($i=0; $i < count($audit); $i++) { 
      $mail_to = [];
      $cc = [];

      $bcc = ['mokhamad.khamdan.khabibi@music.yamaha.com'];

      array_push($mail_to, $audit[$i]->chief_foreman);
      array_push($cc, $audit[$i]->manager);

      $user = User::where('username',$audit[$i]->auditor)->first();
      array_push($mail_to, $user->email);

      $auditdata = array(
        'schedule_id' => $audit[$i]->schedule_id,
        'audit_id' => $audit[$i]->audit_id,
        'audit_title' => $audit[$i]->audit_title,
        'periode' => $audit[$i]->periode,
        'email_date' => $audit[$i]->email_date,
        'incident_date' => $audit[$i]->incident_date,
        'origin' => $audit[$i]->origin,
        'department' => $audit[$i]->department,
        'area' => $audit[$i]->area,
        'product' => $audit[$i]->product,
        'audit_index' => $audit[$i]->audit_index,
        'audit_point' => $audit[$i]->audit_point,
        'audit_images' => $audit[$i]->audit_images,
        'auditor' => $audit[$i]->auditor,
        'result_check' => $audit[$i]->result_check,
        'chief_foreman' => $audit[$i]->chief_foreman,
        'manager' => $audit[$i]->manager,
        'note' => $audit[$i]->note,
        'result_image' => $audit[$i]->result_image,
        'remark' => $audit[$i]->remark,
      );

      Mail::to($mail_to)->cc($cc,'CC')->bcc($bcc,'BCC')->send(new SendEmail($auditdata, 'audit_ng_jelas'));
    }

    $update = DB::table('audit_external_claims')->where('schedule_id',$schedule_id)->update([
      'send_status' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $response = array(
      'status' => true,
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

public function indexAuditCparCarPdf($id)
{
  $audit = DB::table('audit_external_claims')->select('audit_external_claims.*','employee_syncs.name as auditor_name')->where('schedule_id',$id)->join('employee_syncs','employee_syncs.employee_id','auditor')->orderBy('audit_index','asc')->get();
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

  $chief_foreman = DB::select("SELECT
      * 
    FROM
    approvers 
    WHERE
    department = '".$audit[0]->department."' 
    AND remark IN (
    'Manager',
    'Chief',
    'Foreman' 
  )");

  $foreman = '';
  for ($i=0; $i < count($chief_foreman); $i++) { 
    if (str_contains($audit[0]->product,'YFL')) {
      if ($chief_foreman[$i]->section == 'Assembly FL Process Section') {
        if ($chief_foreman[$i]->remark == 'Foreman') {
          $foreman = $chief_foreman[$i]->approver_id.'_'.$chief_foreman[$i]->approver_name;
        }
      }
    }else if (str_contains($audit[0]->product,'YAS') || str_contains($audit[0]->product,'YTS')) {
      if ($chief_foreman[$i]->section == 'Assembly Sax Process Section') {
        if ($chief_foreman[$i]->remark == 'Foreman') {
          $foreman = $chief_foreman[$i]->approver_id.'_'.$chief_foreman[$i]->approver_name;
        }
      }
    }else{
      if ($chief_foreman[$i]->remark == 'Foreman') {
        $foreman = $chief_foreman[$i]->approver_id.'_'.$chief_foreman[$i]->approver_name;
      }
      if ($chief_foreman[$i]->remark == 'Chief') {
        $foreman = $chief_foreman[$i]->approver_id.'_'.$chief_foreman[$i]->approver_name;
      }
    }
  }

  $pdf = \App::make('dompdf.wrapper');
  $pdf->getDomPDF()->set_option("enable_php", true);
  $pdf->setPaper('A4', 'landscape');

  $pdf->loadView('qa.cpar_car.pdf_audit', array(
    'audit' => $audit,
    'foreman' => $foreman,
  ));

  return $pdf->stream($audit[0]->audit_title." (".date('Y-m-d',strtotime($audit[0]->created_at)).").pdf");

}

public function fetchCheckMaterialKPP(Request $request)
{
  try {
    $number = $request->get('material_number');

    $hexvalues = array('0','1','2','3','4','5','6','7',
      '8','9','A','B','C','D','E','F');
    $mat_num = '';    

    if (strlen($number) == 7) {
      $mat_num = $number;
    } else {
      while($number != '0')
      {
        $mat_num = $hexvalues[bcmod($number,'16')].$mat_num;
        $number = bcdiv($number,'16',0);
      }
    }

    $material = db::connection('tpro')->select('SELECT product_gmc, product_name, product_qty_cs, hpl from m_product_kartu LEFT JOIN m_product on m_product_kartu.product_id = m_product.product_id where kartu_code = "' . $mat_num . '" OR product_gmc = "' . $request->get('material_number') . '" limit 1');

    $material2 = db::select('SELECT material_number AS product_gmc, material_description AS product_name,
      IF
      (
      storage_location = "FLA0" 
      OR storage_location = "FLA2" 
      OR storage_location = "PXF0",
      "FL",
      IF
      (
      storage_location = "SXA0" 
      OR storage_location = "SXA2" 
      OR storage_location = "PXS0",
      "SX",
      IF
      ( storage_location = "CLA0" OR storage_location = "CLA2" OR storage_location = "PXL0", "CL", IF ( storage_location = "VNA0", "VN", "ZPRO" ) ) 
      ) 
      ) AS hpl 
      FROM
      `material_plant_data_lists` 
      WHERE
      storage_location IN ( "FLA0", "FLA2", "SXA0", "SXA2", "CLA0", "CLA2", "ZPA0", "VNA0", "PXL0", "PXF0", "PXS0", "PXZP" ) AND material_number = "'.$request->get('material_number').'"');


    if (count($material) > 0) {
      $response = array(
       'status' => true,
       'material'=> $material
     );
      return Response::json($response);
    } else if(count($material2) > 0) {
      $response = array(
       'status' => true,
       'material'=> $material2
     );
      return Response::json($response);

    } else{
      $response = array(
       'status' => false,
       'message' => 'Material Tidak Ditemukan'
     );
      return Response::json($response);
    }
  } catch (\Exception $e) {
   $response = array(
    'status' => false,
    'message' => $e->getMessage()
  );
   return Response::json($response);
 }
}

public function inputNgKensaTemp(Request $request)
{
  try {
   $material_number = strtoupper($request->get('material_number'));
   $material_description = $request->get('material_description');
   $qty_production = $request->get('qty_production');
   $qty_check = $request->get('qty_check');
   $inspection_level = $request->get('inspection_level');
   $ng_name = $request->get('ng_name');
   $qty_ng = $request->get('qty_ng');
   $status_ng = $request->get('status_ng');
   $note_ng = $request->get('note_ng');
   $inspector = $request->get('inspector');
   $location = $request->get('location');

   $incoming_check_code = $location."_".$material_number."_".$inspection_level."_".$inspector;

   QaKensaNgTemp::create([
    'incoming_check_code' => $incoming_check_code,
    'inspector_id' => $inspector,
    'location' => $location,
    'material_number' => $material_number,
    'material_description' => $material_description,
    'qty_production' => $qty_production,
    'qty_check' => $qty_check,
    'inspection_level' => $inspection_level,
    'ng_name' => $ng_name,
    'qty_ng' => $qty_ng,
    'status_ng' => $status_ng,
    'note_ng' => $note_ng,
    'created_by' => Auth::id()
  ]);

   $response = array(
    'status' => true,
    'message' => 'Input NG Berhasil',
    'incoming_check_code' => $incoming_check_code
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

public function fetchNgKensaTemp(Request $request)
{
  try {
    if ($request->get('incoming_check_code') != "") {
      $ng_temp = QaKensaNgTemp::where('incoming_check_code',$request->get('incoming_check_code'))->get();
      $response = array(
       'status' => true,
       'incoming_check_code' => $request->get('incoming_check_code'),
       'ng_temp' => $ng_temp
     );
      return Response::json($response);
    }else{
      $response = array(
       'status' => true,

     );
      return Response::json($response);
    }
  } catch (\Exception $e) {
   $response = array(
    'status' => false,
    'message' => $e->getMessage()
  );
   return Response::json($response);
 }
}

public function inputKensaNgLog(Request $request)
{
  try {
   $material_number = strtoupper($request->get('material_number'));

   $material_description = $request->get('material_description');
   $qty_production = $request->get('qty_production');
   $qty_check = $request->get('qty_check');
   $inspection_level = $request->get('inspection_level');
   $inspector = $request->get('inspector');
   $location = $request->get('location');
   $incoming_check_code = $request->get('incoming_check_code')."_".date('Y-m-d H:i:s');
   $repair = $request->get('repair');
   $scrap = $request->get('scrap');
   $total_ok = $request->get('total_ok');
   $total_ng = $request->get('total_ng');
   $ng_ratio = $request->get('ng_ratio');
   $note_all = $request->get('note_all');

   $materials = db::connection('tpro')->select('SELECT product_gmc, product_name, product_qty_cs, hpl from m_product WHERE product_gmc = "' . $material_number . '" limit 1');
   $material2 = db::select('SELECT material_number AS product_gmc, material_description AS product_name,
    IF
    (
    storage_location = "FLA0" 
    OR storage_location = "FLA2" 
    OR storage_location = "PXF0",
    "FL",
    IF
    (
    storage_location = "SXA0" 
    OR storage_location = "SXA2" 
    OR storage_location = "PXS0",
    "SX",
    IF
    ( storage_location = "CLA0" OR storage_location = "CLA2" OR storage_location = "PXL0", "CL", IF ( storage_location = "VNA0", "VN", "ZPRO" ) ) 
    ) 
    ) AS hpl 
    FROM
    `material_plant_data_lists` 
    WHERE
    storage_location IN ( "FLA0", "FLA2", "SXA0", "SXA2", "CLA0", "CLA2", "ZPA0", "VNA0", "PXL0", "PXF0", "PXS0", "PXZP" ) AND material_number = "'.$material_number.'"');

   if (count($materials) > 0) {
     $hpl = $materials[0]->hpl;
   } else if (count($material2) > 0){
     $hpl = $material2[0]->hpl;
   }

   $log = QaKensaLog::create([
     'incoming_check_code' => $incoming_check_code,
     'inspector_id' => $inspector,
     'location' => $location,
     'material_number' => $material_number,
     'material_description' => $material_description,
     'qty_production' => $qty_production,
     'qty_check' => $qty_check,
     'inspection_level' => $inspection_level,
     'repair' => $repair,
     'scrap' => $scrap,
     'total_ok' => $total_ok,
     'total_ng' => $total_ng,
     'ng_ratio' => $ng_ratio,
     'hpl' => $hpl,     
     'note_all' => $note_all,
     'created_by' => Auth::id()
   ]);

   $ng_temp = QaKensaNgTemp::where('incoming_check_code',$request->get('incoming_check_code'))->get();

   foreach ($ng_temp as $key) {
     QaKensaNgLog::create([
      'incoming_check_code' => $incoming_check_code,
      'incoming_check_log_id' => $log->id,
      'inspector_id' => $inspector,
      'location' => $key->location,
      'material_number' => $key->material_number,
      'material_description' => $key->material_description,
      'qty_production' => $key->qty_production,
      'qty_check' => $key->qty_check,
      'inspection_level' => $key->inspection_level,
      'ng_name' => $key->ng_name,
      'qty_ng' => $key->qty_ng,
      'status_ng' => $key->status_ng,
      'note_ng' => $key->note_ng,
      'created_by' => Auth::id()
    ]);
     QaKensaNgTemp::where('id',$key->id)->forceDelete();
   }

   $response = array(
    'status' => true,
    'message' => 'Success Input Kensa Check'
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

public function deleteKensaNgTemp(Request $request)
{
  try {
   $delete = QaKensaNgTemp::where('id',$request->get('id'))->forceDelete();
   $response = array(
    'status' => true,
    'message' => 'Success Delete NG'
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

public function fetchKensaDetailRecord(Request $request)
{
  try {
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = "DATE_FORMAT( NOW() - INTERVAL 7 DAY, '%Y-%m-%d' ) ";
      $last = "DATE(NOW())";
    }else{
      $first = "DATE_FORMAT( NOW() - INTERVAL 7 DAY, '%Y-%m-%d' ) ";
      $last = "'".$date_to."'";
    }
  }else{
   if ($date_to == "") {
    $first = "'".$date_from."'";
    $last = "DATE(NOW())";
  }else{
    $first = "'".$date_from."'";
    $last = "'".$date_to."'";
  }
}

$material = '';
if($request->get('material') != null){
  $materials =  explode(",", $request->get('material'));
  for ($i=0; $i < count($materials); $i++) {
    $material = $material."'".$materials[$i]."'";
    if($i != (count($materials)-1)){
      $material = $material.',';
    }
  }
  $materialin = " and `material_number` in (".$material.") ";
}
else{
  $materialin = "";
}
$detail = DB::SELECT("
  SELECT
  qa_kensa_logs.location,
  employee_syncs.employee_id,
  employee_syncs.name,
  qa_kensa_logs.material_number,
  qa_kensa_logs.material_description,
  qa_kensa_logs.inspection_level,
  qa_kensa_logs.`repair`,
  qa_kensa_logs.`scrap`,
  qa_kensa_logs.`qty_production`,
  qa_kensa_logs.`qty_check`,
  qa_kensa_logs.`total_ok`,
  qa_kensa_logs.`total_ng`,
  qa_kensa_logs.`ng_ratio`,
  DATE( qa_kensa_logs.created_at ) AS created,
  ( SELECT GROUP_CONCAT( ng_name SEPARATOR '_' ) FROM qa_kensa_ng_logs WHERE qa_kensa_ng_logs.incoming_check_code = qa_kensa_logs.incoming_check_code ) AS ng_name,
  ( SELECT GROUP_CONCAT( qty_ng SEPARATOR '_' ) FROM qa_kensa_ng_logs WHERE qa_kensa_ng_logs.incoming_check_code = qa_kensa_logs.incoming_check_code ) AS ng_qty,
  ( SELECT GROUP_CONCAT( status_ng SEPARATOR '_' ) FROM qa_kensa_ng_logs WHERE qa_kensa_ng_logs.incoming_check_code = qa_kensa_logs.incoming_check_code ) AS status_ng,
  ( SELECT GROUP_CONCAT( note_ng SEPARATOR '_' ) FROM qa_kensa_ng_logs WHERE qa_kensa_ng_logs.incoming_check_code = qa_kensa_logs.incoming_check_code ) AS note_ng 
  FROM
  qa_kensa_logs
  JOIN employee_syncs ON employee_syncs.employee_id = qa_kensa_logs.inspector_id 
  WHERE
  DATE( qa_kensa_logs.created_at ) >= ".$first."
  AND DATE( qa_kensa_logs.created_at ) <= ".$last."
  ".$materialin."
  ORDER BY
  qa_kensa_logs.material_number,
  qa_kensa_logs.created_at desc");

$response = array(
  'status' => true,
  'detail' => $detail,
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

public function indexReportKensaCheck()
{
  $mats = [];

  $material = db::connection('tpro')->select('SELECT product_gmc as material_number, product_name as material_description from m_product');

  $material2 = db::select('SELECT material_number, material_description
    FROM
    `material_plant_data_lists` 
    WHERE
    storage_location IN ( "FLA0", "FLA2", "SXA0", "SXA2", "CLA0", "CLA2", "ZPA0", "VNA0", "PXL0", "PXF0", "PXS0", "PXZP" )');

  $mats = array_merge_recursive($material, $material2);

  $inspection_level = DB::SELECT("SELECT DISTINCT(inspection_level) FROM `ympimis`.`qa_inspection_levels`");

  return view('qa.report_kensa_kpp')
  ->with('title', 'Report Kensa KPP QA')
  ->with('title_jp', '')
  ->with('materials', $mats)
  ->with('inspection_levels', $inspection_level)
  ->with('page', 'Report Kensa KPP QA');
}

public function fetchReportKensaCheck(Request $request)
{
  try {
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
      $last = "LAST_DAY(NOW())";
    }else{
      $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
      $last = "'".$date_to."'";
    }
  }else{
   if ($date_to == "") {
    $first = "'".$date_from."'";
    $last = "LAST_DAY(NOW())";
  }else{
    $first = "'".$date_from."'";
    $last = "'".$date_to."'";
  }
}

$material = '';
if($request->get('material') != null){
  $materials =  explode(",", $request->get('material'));
  for ($i=0; $i < count($materials); $i++) {
    $material = $material."'".$materials[$i]."'";
    if($i != (count($materials)-1)){
      $material = $material.',';
    }
  }
  $materialin = " and `material_number` in (".$material.") ";
}
else{
  $materialin = "";
}

$inspection_level = '';
if($request->get('inspection_level') != null){
  $inspection_levels =  explode(",", $request->get('inspection_level'));
  for ($i=0; $i < count($inspection_levels); $i++) {
    $inspection_level = $inspection_level."'".$inspection_levels[$i]."'";
    if($i != (count($inspection_levels)-1)){
      $inspection_level = $inspection_level.',';
    }
  }
  $inspection_levelin = " and `inspection_level` in (".$inspection_level.") ";
}
else{
  $inspection_levelin = "";
}

$datas = DB::SELECT("SELECT
  qa_kensa_logs.id as id_log,
  qa_kensa_logs.location,
  employee_syncs.employee_id,
  employee_syncs.name,
  qa_kensa_logs.material_number,
  qa_kensa_logs.material_description,
  qa_kensa_logs.inspection_level,
  qa_kensa_logs.`scrap`,
  qa_kensa_logs.`repair`,
  qa_kensa_logs.`qty_production`,
  qa_kensa_logs.`qty_check`,
  qa_kensa_logs.`total_ok`,
  qa_kensa_logs.`total_ng`,
  qa_kensa_logs.`ng_ratio`,
  qa_kensa_logs.`hpl`,
  qa_kensa_logs.`note_all`,
  qa_kensa_logs.created_at AS created,
  ( SELECT GROUP_CONCAT( ng_name SEPARATOR '_' ) FROM qa_kensa_ng_logs WHERE qa_kensa_ng_logs.incoming_check_code = qa_kensa_logs.incoming_check_code ) AS ng_name,
  ( SELECT GROUP_CONCAT( qty_ng SEPARATOR '_' ) FROM qa_kensa_ng_logs WHERE qa_kensa_ng_logs.incoming_check_code = qa_kensa_logs.incoming_check_code ) AS ng_qty,
  ( SELECT GROUP_CONCAT( status_ng SEPARATOR '_' ) FROM qa_kensa_ng_logs WHERE qa_kensa_ng_logs.incoming_check_code = qa_kensa_logs.incoming_check_code ) AS status_ng,
  ( SELECT GROUP_CONCAT( note_ng SEPARATOR '_' ) FROM qa_kensa_ng_logs WHERE qa_kensa_ng_logs.incoming_check_code = qa_kensa_logs.incoming_check_code ) AS note_ng 
  FROM
  qa_kensa_logs
  JOIN employee_syncs ON employee_syncs.employee_id = qa_kensa_logs.inspector_id 
  WHERE
  DATE( qa_kensa_logs.created_at ) >= ".$first." 
  AND DATE( qa_kensa_logs.created_at ) <= ".$last." ".$inspection_levelin." ".$materialin." ");

$response = array(
  'status' => true,
  'datas' => $datas,
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

public function excelReportKensaCheck(Request $request)
{
  try {
    if ($request->get('publish') != null) {
      $stattsss = 'no_merge';
    }else{
      $stattsss = 'merge';
    }
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
      $last = "LAST_DAY(NOW())";
    }else{
      $first = "DATE_FORMAT( NOW(), '%Y-%m-01' ) ";
      $last = "'".$date_to."'";
    }
  }else{
   if ($date_to == "") {
    $first = "'".$date_from."'";
    $last = "LAST_DAY(NOW())";
  }else{
    $first = "'".$date_from."'";
    $last = "'".$date_to."'";
  }
}

$material = '';
if($request->get('material') != null){
  $materials =  explode(",", $request->get('material'));
  for ($i=0; $i < count($materials); $i++) {
    $material = $material."'".$materials[$i]."'";
    if($i != (count($materials)-1)){
      $material = $material.',';
    }
  }
  $materialin = " and `material_number` in (".$material.") ";
}
else{
  $materialin = "";
}

$inspection_level = '';
if($request->get('inspection_level') != null){
  $inspection_levels =  explode(",", $request->get('inspection_level'));
  for ($i=0; $i < count($inspection_levels); $i++) {
    $inspection_level = $inspection_level."'".$inspection_levels[$i]."'";
    if($i != (count($inspection_levels)-1)){
      $inspection_level = $inspection_level.',';
    }
  }
  $inspection_levelin = " and `inspection_level` in (".$inspection_level.") ";
}
else{
  $inspection_levelin = "";
}


$datas = DB::SELECT("SELECT
  qa_kensa_logs.id as id_log,
  qa_kensa_logs.location,
  employee_syncs.employee_id,
  employee_syncs.name,
  qa_kensa_logs.material_number,
  qa_kensa_logs.material_description,
  qa_kensa_logs.inspection_level,
  qa_kensa_logs.`repair`,
  qa_kensa_logs.`scrap`,
  qa_kensa_logs.`qty_production`,
  qa_kensa_logs.`qty_check`,
  qa_kensa_logs.`total_ok`,
  qa_kensa_logs.`total_ng`,
  qa_kensa_logs.`ng_ratio`,
  qa_kensa_logs.`hpl`,
  qa_kensa_logs.`note_all`,
  qa_kensa_logs.created_at AS created,
  ( SELECT GROUP_CONCAT( ng_name SEPARATOR '_' ) FROM qa_kensa_ng_logs WHERE qa_kensa_ng_logs.incoming_check_code = qa_kensa_logs.incoming_check_code ) AS ng_name,
  ( SELECT GROUP_CONCAT( qty_ng SEPARATOR '_' ) FROM qa_kensa_ng_logs WHERE qa_kensa_ng_logs.incoming_check_code = qa_kensa_logs.incoming_check_code ) AS ng_qty,
  ( SELECT GROUP_CONCAT( status_ng SEPARATOR '_' ) FROM qa_kensa_ng_logs WHERE qa_kensa_ng_logs.incoming_check_code = qa_kensa_logs.incoming_check_code ) AS status_ng,
  ( SELECT GROUP_CONCAT( note_ng SEPARATOR '_' ) FROM qa_kensa_ng_logs WHERE qa_kensa_ng_logs.incoming_check_code = qa_kensa_logs.incoming_check_code ) AS note_ng 
  FROM
  qa_kensa_logs
  JOIN employee_syncs ON employee_syncs.employee_id = qa_kensa_logs.inspector_id 
  WHERE
  DATE( qa_kensa_logs.created_at ) >= ".$first." 
  AND DATE( qa_kensa_logs.created_at ) <= ".$last." ".$inspection_levelin." ".$materialin." ");

$data = array(
  'datas' => $datas
);

if ($stattsss == 'no_merge') {
  ob_clean();
  Excel::create('Kensa Check QA Report Without Merge', function($excel) use ($data){
    $excel->sheet('Kensa Check QA', function($sheet) use ($data) {
      return $sheet->loadView('qa.qa_kpp.excel_kensa_check_without_merge', $data);
    });
  })->export('xlsx');
}else{
  ob_clean();
  Excel::create('Kensa Check QA Report With Merge', function($excel) use ($data){
    $excel->sheet('Kensa Check QA', function($sheet) use ($data) {
      return $sheet->loadView('qa.qa_kpp.excel_kensa_check', $data);
    });
  })->export('xlsx');
}
return redirect()->route('report_kensa_qa')->with('status','Success Export Data');
} catch (\Exception $e) {
  return redirect()->route('report_kensa_qa')->with('error',$e->getMessage());
}
}

public function fetchReportKensaEdit(Request $request)
{
  try {
    $datas = DB::SELECT("SELECT
      qa_kensa_logs.id as id_log,
      qa_kensa_logs.incoming_check_code,
      qa_kensa_logs.location,
      employee_syncs.employee_id,
      employee_syncs.name,
      qa_kensa_logs.material_number,
      qa_kensa_logs.material_description,
      qa_kensa_logs.inspection_level,
      qa_kensa_logs.`repair`,
      DATE(qa_kensa_logs.created_at) as date,
      qa_kensa_logs.`scrap`,
      qa_kensa_logs.`qty_production`,
      qa_kensa_logs.`qty_check`,
      qa_kensa_logs.`total_ok`,
      qa_kensa_logs.`total_ng`,
      qa_kensa_logs.`ng_ratio`,
      qa_kensa_logs.`report_evidence`,
      DATE( qa_kensa_logs.created_at ) AS created,
      ( SELECT GROUP_CONCAT( ng_name SEPARATOR '_' ) FROM qa_kensa_ng_logs WHERE qa_kensa_ng_logs.incoming_check_code = qa_kensa_logs.incoming_check_code ) AS ng_name,
      ( SELECT GROUP_CONCAT( qty_ng SEPARATOR '_' ) FROM qa_kensa_ng_logs WHERE qa_kensa_ng_logs.incoming_check_code = qa_kensa_logs.incoming_check_code ) AS ng_qty,
      ( SELECT GROUP_CONCAT( status_ng SEPARATOR '_' ) FROM qa_kensa_ng_logs WHERE qa_kensa_ng_logs.incoming_check_code = qa_kensa_logs.incoming_check_code ) AS status_ng,
      ( SELECT GROUP_CONCAT( note_ng SEPARATOR '_' ) FROM qa_kensa_ng_logs WHERE qa_kensa_ng_logs.incoming_check_code = qa_kensa_logs.incoming_check_code ) AS note_ng 
      FROM
      qa_kensa_logs
      JOIN employee_syncs ON employee_syncs.employee_id = qa_kensa_logs.inspector_id 
      WHERE
      qa_kensa_logs.id = '".$request->get('id')."'
      ORDER BY
      qa_kensa_logs.material_number,
      qa_kensa_logs.created_at desc");

    $response = array(
      'status' => true,
      'datas' => $datas,
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

public function updateReportKensaCheck(Request $request)
{
  try {
    $material = $request->get('material');
    $material_desc = $request->get('material_desc');
    $inspection_level = $request->get('inspection_level');
    $qty_production = $request->get('qty_production');    
    $incoming_check_code = $request->get('incoming_check_code');
    $id_log = $request->get('id_log');

    $log = QaKensaLog::where('id',$id_log)->first();
    $log->material_number = $material;
    $log->material_description = $material_desc;
    $log->inspection_level = $inspection_level;
    $log->qty_production = $qty_production;

    $ng_log = QaKensaNgLog::where('incoming_check_log_id',$id_log)->get();
    if (count($ng_log) > 0) {
      foreach($ng_log as $ng_logs){
        $nglogs = QaKensaNgLog::where('id',$ng_logs->id)->first();
        $nglogs->material_number = $material;
        $nglogs->material_description = $material_desc;
        $nglogs->inspection_level = $inspection_level;
        $nglogs->qty_production = $qty_production;
        $nglogs->save();
      }
    }

    $log->save();

    $response = array(
      'status' => true,
      'message' => 'Success Update Data'
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

public function deleteReportKensaCheck(Request $request)
{
  try {
    $log = QaKensaLog::where('id',$request->get('id'))->forceDelete();
    $ng_log = QaKensaNgLog::where('incoming_check_log_id',$request->get('id'))->get();
    if (count($ng_log) > 0) {
      foreach ($ng_log as $key) {
        QaKensaNgLog::where('id',$key->id)->forceDelete();
      }
    }

    $response = array(
      'status' => true,
      'message' => 'Success Delete Data'
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

public function indexAuditCparCarReport()
{
  $point = DB::table('audit_external_claim_points')->select('audit_id','audit_title')->distinct()->where('remark','cpar_car')->get();

  return view('qa.cpar_car.report')
  ->with('title', 'Quality Assurance Audit CPAR & CAR Report')
  ->with('title_jp', '品証 是正予防策・是正策監視 監査')
  ->with('page', 'Quality Assurance')
  ->with('point',$point)
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function fetchAuditCparCarReport(Request $request)
{
  try {
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = date('Y-m-01');
      $last = date('Y-m-d');
    }else{
      $first = date('Y-m-01');
      $last = $date_to;
    }
  }else{
   if ($date_to == "") {
    $first = $date_from;
    $last = date('Y-m-d');
  }else{
    $first = $date_from;
    $last = $date_to;
  }
}
$report = DB::table('audit_external_claims')->select('audit_external_claims.*','employee_syncs.name');
$report = $report->where(DB::RAW('date(audit_external_claims.created_at)'),'>=',$first)->where(DB::RAW('date(audit_external_claims.created_at)'),'<=',$last);

if ($request->get('audit_id') != '') {
  $report = $report->where('audit_id',$request->get('audit_id'));
}

if ($request->get('result_check') != '') {
  $report = $report->where('result_check',$request->get('result_check'));
}

$report = $report->join('employee_syncs','employee_syncs.employee_id','auditor')->get();
$response = array(
  'status' => true,
  'report' => $report,
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

public function indexSpecialProcessVerification($schedule_id)
{
  $emp_id = strtoupper(Auth::user()->username);
  $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
  $audit = DB::connection('ympimis_2')->select("SELECT
  * 
    FROM
    `qa_process_audits` 
    WHERE
    ( schedule_id = '".$schedule_id."' AND decision = 'NG' ) 
    OR (
    schedule_id = '".$schedule_id."' 
    AND decision = 'NS' )");
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();
  return view('qa.special_process.verification')
  ->with('title', 'Cek Efektifitas Penanganan Audit Proses Khusus')
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('audit',$audit)
  ->with('emp',$emp)
  ->with('count_audit',count($audit))
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function inputSpecialProcessVerification(Request $request)
{
  try {
    $id_verification = $request->get('id_verification');
    $qa_verification = $request->get('verification');
    $qa_verified_id = $request->get('verification_id');
    $qa_verified_name = $request->get('verification_name');
    $qa_verification_note = $request->get('verification_note');

    for ($i=0; $i < count($id_verification); $i++) { 
      $update = DB::connection('ympimis_2')->table('qa_process_audits')->where('id',$id_verification[$i])->update([
        'qa_verification' => $qa_verification[$i],
        'qa_verification_note' => $qa_verification_note[$i],
        'qa_verified_id' => $qa_verified_id[$i],
        'qa_verified_name' => $qa_verified_name[$i],
        'qa_verified_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }
    $response = array(
      'status' => true,
      'message' => 'Success Input Penanganan'
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

public function indexAuditSpecialProcessReportShort($schedule_id)
{
  $emp_id = strtoupper(Auth::user()->username);
  $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
  $audit = DB::connection('ympimis_2')->select("SELECT
  * 
    FROM
    `qa_process_audits` 
    WHERE
    schedule_id = '".$schedule_id."' ");
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();
  return view('qa.special_process.report_short')
  ->with('title', 'Report Audit Proses Khusus')
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('audit',$audit)
  ->with('emp',$emp)
  ->with('count_audit',count($audit))
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function indexAuditFeeling()
{
  $fy_all = DB::SELECT("SELECT DISTINCT
    ( fiscal_year ) 
    FROM
    weekly_calendars");
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

  return view('qa.feeling.index')
  ->with('title', 'Quality Assurance ~ Penyamaan Feeling Kensa FG')
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('fy_all',$fy_all)
  ->with('emp',$emp)
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function indexAuditFeelingAudit()
{
  $claim = DB::select("SELECT
    cpar_no,
    judul_komplain,
    lokasi,
    tgl_permintaan,
    kategori_komplain 
    FROM
    `qc_cpars` 
    WHERE
    kategori = 'Eksternal'
    AND feeling_status is null");
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

  $leader = DB::select("SELECT
    employee_id
    FROM
    employee_syncs 
    WHERE
    department LIKE '%Quality%'
    and end_date is null
    and position = 'Leader'");

  $origin_group_code = '';

  if (in_array($emp->employee_id, $leader)) {
    if ($emp) {
      if (str_contains($emp->group,'SAX')) {
        $origin_group_code = '043';
      }
      if (str_contains($emp->group,'FL')) {
        $origin_group_code = '041';
      }
      if (str_contains($emp->group,'CL')) {
        $origin_group_code = '042';
      }
    }
  }

  $operator_all = DB::SELECT("SELECT
    assembly_operators.employee_id,
    REPLACE ( `name`, '\'', '' ) AS `name`
    FROM
    assembly_operators 
    join employee_syncs on employee_syncs.employee_id = assembly_operators.employee_id
    WHERE
    origin_group_code = '043' 
    AND location LIKE '%kensa%'");;

  if ($origin_group_code == '041') {
    $operator_all = DB::SELECT("SELECT * FROM `ympimis`.`employee_syncs` WHERE `department` LIKE '%assembly%' AND `section` LIKE '%Assembly FL Process Section%' AND `group` LIKE '%FL Assembly Finishing Group%' AND `sub_group` LIKE '%Cosei - Cek Internal Unit%' and end_date is null");
  }else if($origin_group_code == '042' || $origin_group_code == '043'){
    $operator_all = DB::SELECT("SELECT
      assembly_operators.employee_id,
      REPLACE ( `name`, '\'', '' ) AS `name`
      FROM
      assembly_operators 
      join employee_syncs on employee_syncs.employee_id = assembly_operators.employee_id
      WHERE
      origin_group_code = '".$origin_group_code."' 
      AND location LIKE '%kensa%'");
  }

  $yesterday = date('Y-m-d', strtotime(' -1 days'));
  $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars` order by week_date desc");
  foreach ($weekly_calendars as $key) {
    if ($key->week_date == $yesterday) {
      if (str_contains($key->remark,'H')) {
        $yesterday = date('Y-m-d', strtotime($yesterday . '-1 days'));
      }
    }
  }

  $origin = '';
  if ($origin_group_code != '') {
    $origin = "AND origin_group_code = '".$origin_group_code."' ";
  }

  $ng_before = DB::SELECT("SELECT
    SPLIT_STRING ( ng_name, ' - ', 1 ) AS ng_name,
    ongko,
    model,
    count(
    DISTINCT ( serial_number )) AS qty 
    FROM
    assembly_ng_logs 
    WHERE
    DATE( created_at ) = '".$yesterday."' 
    ".$origin."
    GROUP BY
    ng_name,
    model,
    ongko 
    ORDER BY
    qty DESC
    LIMIT 3");

  $ng_before_pianica = DB::select("SELECT
    ng_name,
    '' AS ongko,
    model,
    count( pn_log_ngs.id ) AS qty 
    FROM
    pn_log_ngs
    JOIN ng_lists ON ng_lists.id = ng 
    WHERE
    DATE( pn_log_ngs.created_at ) = '".$yesterday."' 
    GROUP BY
    model,
    ng_name
    ORDER BY
    qty DESC 
    LIMIT 3");

  $ng_before_recorder = DB::SELECT("SELECT
    product,ng_name,ng_count
    FROM
    rc_kensas 
    WHERE
    DATE( created_at ) = '".$yesterday."' 
    AND ng_name IS NOT NULL");

  $ng_name_rcd = [];
  $ng_names = [];
  for ($i=0; $i < count($ng_before_recorder); $i++) { 
    $ngs = explode(',', $ng_before_recorder[$i]->ng_name);
    $ng_count = explode(',', $ng_before_recorder[$i]->ng_count);
    for ($j=0; $j < count($ngs); $j++) { 
      array_push($ng_name_rcd, [
        'ng_name' => $ngs[$j],
        'ongko' => '',
        'model' => $ng_before_recorder[$i]->product,
        'qty' => $ng_count[$j],
      ]);
      array_push($ng_names, $ng_before_recorder[$i]->product.'_'.$ngs[$j]);
    }
  }

  $ng_name_unik = array_values(array_unique($ng_names));

  $ng_name_rcd2 = [];
  for ($i=0; $i < count($ng_name_unik); $i++) {
    $count = 0;
    for ($j=0; $j < count($ng_name_rcd); $j++) { 
      if ($ng_name_rcd[$j]['ng_name'] == explode('_', $ng_name_unik[$i])[1] && $ng_name_rcd[$j]['model'] == explode('_', $ng_name_unik[$i])[0]) {
        $count = $count + $ng_name_rcd[$j]['qty'];
      }
    }
    array_push($ng_name_rcd2, [
      'ng_name' => explode('_', $ng_name_unik[$i])[1],
      'ongko' => '',
      'model' => explode('_', $ng_name_unik[$i])[0],
      'qty' => $count,
    ]);
  }

  usort($ng_name_rcd2, function ($a, $b) {return $a['qty'] < $b['qty'];});

  $code_certificate = DB::connection('ympimis_2')->SELECT("SELECT
    CONCAT( 'YMPI', '-', 'QA', '-', `code`, '-', code_number ) AS certificate_codes,
    REPLACE ( REPLACE ( description, '(', '' ), ')', '' ) AS description,
    certificate_name,
    subjects.`subject` 
    FROM
    qa_certificate_codes
    LEFT JOIN (
    SELECT
    CONCAT( 'YMPI', '-', 'QA', '-', `code`, '-', code_number ) AS certificate_code,
    `subject` 
    FROM
    qa_certificate_points 
    GROUP BY
    certificate_code,
    `subject` 
    ) AS subjects ON subjects.certificate_code = CONCAT( 'YMPI', '-', 'QA', '-', `code`, '-', code_number ) 
    WHERE
    employee_id IS NOT NULL 
    GROUP BY
    certificate_codes,
    description,
    subjects.`subject`,
    certificate_name");

  $certificate = DB::connection('ympimis_2')->select("SELECT
    a.* 
    FROM
    ((
    SELECT
    employee_id,
    REPLACE ( `name`, '\'', '' ) AS `name`,
    SUBSTR( certificate_code FROM 1 FOR LENGTH( certificate_code )- 3 ) AS certificate_code,
    '' AS certificate_desc,
    certificate_name,
    `subject` 
    FROM
    qa_certificates 
    WHERE
    `status` = 1 
    AND note != 'Tidak Sertifikasi' 
    GROUP BY
    employee_id,
    `name`,
    certificate_name,
    certificate_desc,
    certificate_code,
    `subject` 
    ORDER BY
    certificate_name 
    ) UNION ALL
    (
    SELECT
    employee_id,
    REPLACE ( `name`, '\'', '' ) AS `name`,
    SUBSTR( certificate_code FROM 1 FOR LENGTH( certificate_code )- 3 ) AS certificate_code,
    REPLACE ( REPLACE ( certificate_desc, '(', '' ), ')', '' ) AS certificate_desc,
    certificate_name,
    NULL AS `subject` 
    FROM
    qa_certificate_inprocesses 
    WHERE
    `status` = 1 
    GROUP BY
    employee_id,
    `name`,
    certificate_name,
    certificate_desc,
    certificate_code,
    `subject` 
    ORDER BY
    certificate_name 
  )) a ");

  $qa_audit = DB::SELECT("SELECT
    SPLIT_STRING ( ng_name, ' - ', 1 ) AS ng_name,
    ongko,
    model,
    count(
    DISTINCT ( serial_number )) AS qty 
    FROM
    assembly_ng_logs 
    WHERE
    DATE_FORMAT( created_at, '%Y-%m' ) = '".date('Y-m')."' 
    ".$origin."
    AND location = 'qa-audit' 
    GROUP BY
    ng_name,
    model,
    ongko 
    ORDER BY
    qty DESC");

  $qa_audit_ei = DB::connection('ympimis_2')->select("SELECT
    SPLIT_STRING ( ng_detail, '_', 1 ) AS ng_name,
    SPLIT_STRING ( ng_detail, '_', 2 ) AS ongko,
    SPLIT_STRING ( material_audited, ' - ', 2 ) AS model,
    qty_ng as qty
    FROM
    qa_production_audits 
    WHERE
    DATE_FORMAT( created_at, '%Y-%m' ) = '".date('Y-m')."' 
    AND ng_detail != '______'");

  $ng_lists = DB::select("SELECT DISTINCT
    ( a.ng_name ) 
    FROM
    (
    SELECT DISTINCT
    (
    IF
    (
    ng_name = ng_detail,
    ng_name,
    CONCAT( ng_name, ' ', ng_detail ))) AS ng_name 
    FROM
    `assembly_ng_lists` 
    WHERE
    deleted_at IS NULL 
    ".$origin."
    UNION ALL
    SELECT
    ng_name 
    FROM
    ng_lists 
    WHERE
    remark LIKE '%pianica%' UNION ALL
    SELECT
    ng_name 
    FROM
    ng_lists 
    WHERE
    remark LIKE '%recorder%' UNION ALL
    SELECT
    defect AS ng_name 
    FROM
    scrap_defects 
  ) a");

  $onko = DB::SELECT("SELECT DISTINCT
    ( keynomor ) 
    FROM
    `assembly_onkos` 
    WHERE
    deleted_at IS NULL");

  $date = date('Y-m-d');
  $prefix_now = 'FEEL'.date("y").date("m");
  $code_generator = CodeGenerator::where('note','=','feeling')->first();
  if ($prefix_now != $code_generator->prefix){
    $code_generator->prefix = $prefix_now;
    $code_generator->index = '0';
    $code_generator->save();
  }

  $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
  $feeling_id = $code_generator->prefix . $number.'_'.date('Y-m-d H:i:s');
  $code_generator->index = $code_generator->index+1;
  $code_generator->save();

  return view('qa.feeling.audit')
  ->with('title', 'Quality Assurance ~ Penyamaan Feeling Kensa FG')
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('emp',$emp)
  ->with('claim',$claim)
  ->with('ng_before',$ng_before)
  ->with('ng_before_pianica',$ng_before_pianica)
  ->with('qa_audit',$qa_audit)
  ->with('qa_audit_ei',$qa_audit_ei)
  ->with('operator_all',$operator_all)
  ->with('ng_lists',$ng_lists)
  ->with('onko',$onko)
  ->with('feeling_id',$feeling_id)
  ->with('ng_name_rcd',$ng_name_rcd2)
  ->with('onko_ei',$this->onko_ei)
  ->with('all_ng',$this->all_ng)
  ->with('origin_group_code',$origin_group_code)
  ->with('code_certificate',$code_certificate)
  ->with('certificate',$certificate)
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function fetchAuditFeeling(Request $request)
{
  try {
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
      if ($date_to == "") {
        $first = date('Y-m-01');
        $last = date('Y-m-d');
      }else{
        $first = date('Y-m-01');
        $last = $date_to;
      }
    }else{
     if ($date_to == "") {
      $first = $date_from;
      $last = date('Y-m-d');
    }else{
      $first = $date_from;
      $last = $date_to;
    }
  }

  $auditors = '';
  $auditorsss = null;
  if ($request->get('auditor') != '') {
    $auditors = "AND auditor_id = '".$request->get('auditor')."'";
    $auditorsss = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT(auditor_id),auditor_name from qa_feelings where auditor_id = '".$request->get('auditor')."'");
  }


  $audit = DB::connection('ympimis_2')->SELECT("SELECT
        *,
    DATE_FORMAT( date, '%d-%b-%Y' ) AS date_audit_name
    FROM
    qa_feelings where DATE_FORMAT(date,'%Y-%m-%d') >= '".$first."'
    AND DATE_FORMAT(date,'%Y-%m-%d') <= '".$last."'
    ".$auditors."
    order by date");

  $auditor = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT(auditor_id),auditor_name from qa_feelings");

  $dateTitle = date('d M Y',strtotime($first)).' - '.date('d M Y',strtotime($last));
  $response = array(
    'status' => true,
    'audit' => $audit,
    'auditor' => $auditor,
    'auditorsss' => $auditorsss,
    'dateTitle' => $dateTitle,
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

public function fetchAuditFeelingEmployee(Request $request)
{
  try {
    $tag = $request->get('tag');
    if (str_contains($tag,'PI')) {
      $emp = EmployeeSync::where('employee_id',$tag)->first();
      if ($emp) {
        $response = array(
          'status' => true,
          'emp' => $emp
        );
        return Response::json($response);
      }else{
        $response = array(
          'status' => false,
          'message' => 'NIK Tidak Ditemukan'
        );
        return Response::json($response);
      }
    }else{
      $emps = Employee::where('tag',$tag)->first();
      if ($emps) {
        $emp = EmployeeSync::where('employee_id',$emps->employee_id)->first();
        $response = array(
          'status' => true,
          'emp' => $emp
        );
        return Response::json($response);
      }else{
        $response = array(
          'status' => false,
          'message' => 'ID Card Tidak Terdaftar'
        );
        return Response::json($response);
      }
    }
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage(),
    );
    return Response::json($response);
  }
}

public function fetchFeelingClaim(Request $request)
{
  try {
    $claim = null;
    if ($request->get('category') != '') {
      $claim = DB::select("SELECT
        cpar_no,
        judul_komplain,
        lokasi,
        tgl_permintaan,
        kategori_komplain 
        FROM
        `qc_cpars` 
        WHERE
        kategori = 'Eksternal'
        and kategori_komplain = '".$request->get('category')."'
        and feeling_status is null");
    }
    $response = array(
      'status' => true,
      'claim' => $claim,
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

public function inputAuditFeelingAudit(Request $request)
{
  try {

    $category = $request->get('category');
    $content = $request->get('content');
    $metode = $request->get('metode');
    $index = $request->get('index');
    $ng_name = $request->get('ng_name');
    $area = $request->get('area');
    $gendo = $request->get('gendo');
    $date = $request->get('date');
    $standard = $request->get('standard');
    $auditor_id = $request->get('auditor_id');
    $auditor_name = $request->get('auditor_name');
    $auditee_id = $request->get('auditee_id');
    $auditee_name = $request->get('auditee_name');
    $auditee_status = $request->get('auditee_status');
    $fileData = $request->get('fileData');
    $answer = $request->get('answer');
    $materi = $request->get('materi');
    $note = $request->get('note');
    $feeling_id = $request->get('feeling_id');

    if ($request->get('index') == 0) {
      if (count($request->file('fileData')) > 0) {
        $tujuan_upload = 'data_file/qa/feeling';
        $file = $request->file('fileData');
        $filename = $request->get('auditor_id').'_'.date('YmdHisa').'.'.$request->get('extension');
        $file->move($tujuan_upload,$filename);
      }
    }
    $filename = $request->get('auditor_id').'_'.date('YmdHisa').'.'.$request->get('extension');

    $input = DB::connection('ympimis_2')->table('qa_feelings')->insert([
      'category' => $category,
      'content' => $content,
      'ng_name' => $ng_name,
      'materi' => $materi,
      'metode' => $metode,
      'feeling_id' => $feeling_id,
      'area' => $area,
      'standard' => $standard,
      'date' => $date,
      'gendo' => $gendo,
      'answer' => $answer,
      'auditor_id' => $auditor_id,
      'auditor_name' => $auditor_name,
      'auditee_id' => $auditee_id,
      'auditee_name' => $auditee_name,
      'auditee_status' => $auditee_status,
      'images' => $filename,
      'note' => $note,
      'created_by' => Auth::user()->id,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    if ($category == 'EKSTERNAL KOMPLAIN') {
      $claim = DB::table('qc_cpar_claims')->where('cpar_no',explode(' - ', $content)[0])->first();
      if ($claim) {
        $updateclaim = DB::table('qc_cpar_claims')->where('cpar_no',explode(' - ', $content)[0])->update([
          'feeling_status' => 'Done_'.date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ]);
      }

      $claim = DB::table('qc_cpars')->where('cpar_no',explode(' - ', $content)[0])->first();
      if ($claim) {
        $updateclaim = DB::table('qc_cpars')->where('cpar_no',explode(' - ', $content)[0])->update([
          'feeling_status' => 'Done_'.date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ]);
      }
    }

    $response = array(
      'status' => true,
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

public function indexAuditFG()
{
  $fy_all = DB::SELECT("SELECT DISTINCT
    ( fiscal_year ) 
    FROM
    weekly_calendars");
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

  $point = DB::connection('ympimis_2')->table('qa_production_audit_points')->select('product','material_number','material_description')->distinct()->get();

  return view('qa.audit_fg.index')
  ->with('title', 'Quality Assurance Audit FG')
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('fy_all',$fy_all)
  ->with('emp',$emp)
  ->with('point',$point)
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function fetchAuditFG(Request $request)
{
  try {
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = date('Y-m-01');
      $last = date('Y-m-d');
    }else{
      $first = date('Y-m-01');
      $last = $date_to;
    }
  }else{
   if ($date_to == "") {
    $first = $date_from;
    $last = date('Y-m-d');
  }else{
    $first = $date_from;
    $last = $date_to;
  }
}

$product = $request->get('product');
$material_number = $request->get('material_number');

$products = '';
$products2 = '';
if ($product != '') {
  $products = "AND product = '".$product."'";
  $products2 = "AND assembly_logs.origin_group_code = '".$product."'";
}

$material_numbers = '';
$material_numbers2 = '';
if ($material_number != '') {
  $material_numbers = "AND material_number = '".$material_number."'";
  $material_numbers2 = "AND prod.material_number = '".$material_number."'";
}
$ei = DB::connection('ympimis_2')->select("SELECT
  DATE( date ) AS date_audit,
  DATE_FORMAT( date, '%d-%b-%Y' ) AS date_audit_name,
  `session`,
  qty_lot,
  qty_check,
  audit_id,
  qty_auditor,
  product,
  '' AS serial_number,
  material_audited,
  material_number,
  material_description,
  GROUP_CONCAT(
  DISTINCT ( send_status )) AS send_status,
  IF
  ( sum( qty_ng ) > 0, 'NG', 'OK' ) AS `result_check`,
  GROUP_CONCAT(
  DISTINCT ( handling_id )) AS handled_id,
  GROUP_CONCAT(
  DISTINCT ( due_date )) AS due_date,
  GROUP_CONCAT(
  DISTINCT ( handling_at )) AS handled_at,
  GROUP_CONCAT(
  DISTINCT ( auditor_id )) AS auditor_id,
  GROUP_CONCAT(
  DISTINCT ( auditor_name )) AS auditor_name,
  GROUP_CONCAT(
  DISTINCT ( auditee_id )) AS auditee_id,
  GROUP_CONCAT(
  DISTINCT ( auditee_name )) AS auditee_name 
  FROM
  qa_production_audits 
  WHERE
  DATE( created_at ) >= '".$first."' 
  AND DATE( created_at ) <= '".$last."' 
  ".$products."
  ".$material_numbers."
  GROUP BY
  `date_audit`,
  qty_lot,
  qty_check,
  qty_auditor,
  audit_id,
  `session`,
  date_audit_name,
  product,
  serial_number,
  material_audited,
  material_number,
  material_description 
  ORDER BY
  date_audit");

$wi = DB::select("SELECT
  DATE( created_at ) AS date_audit,
  DATE_FORMAT( created_at, '%d-%b-%Y' ) AS date_audit_name,
  1 AS `session`,
  prod.quantity AS qty_lot,
  1 AS qty_check,
  id AS audit_id,
  1 AS qty_auditor,
  assembly_logs.origin_group_code AS product,
  assembly_logs.serial_number,
  assembly_logs.model AS material_audited,
  prod.material_number,
  prod.material_description,
  NULL AS send_status,
  IF
  ( ng.serial_number IS NOT NULL, 'NG', 'OK' ) AS result_check,
  NULL AS handled_id,
  NULL AS due_date,
  NULL AS handled_at,
  ng,
  operator_id AS auditor_id,
  operator_audited AS auditee_id 
  FROM
  assembly_logs
  JOIN (
  SELECT
  due_date,
  sum( quantity ) AS quantity,
  origin_group_code,
  production_schedules.material_number,
  materials.material_description,
  materials.model 
  FROM
  production_schedules
  LEFT JOIN materials ON materials.material_number = production_schedules.material_number 
  WHERE
  DATE( due_date ) >= '".$first."' 
  AND DATE( due_date ) <= '".$last."' 
  AND category = 'FG' 
  GROUP BY
  due_date,
  origin_group_code,
  production_schedules.material_number,
  materials.material_description,
  materials.model 
  ) AS prod ON prod.origin_group_code = assembly_logs.origin_group_code 
  AND prod.due_date = DATE( assembly_logs.created_at ) 
  AND prod.model = assembly_logs.model
  LEFT JOIN ( SELECT serial_number, model,GROUP_CONCAT(DISTINCT(CONCAT(ng_name,'-',ongko)) SEPARATOR ',') as ng FROM assembly_ng_logs WHERE location = 'qa-audit' GROUP BY serial_number, model ) AS ng ON ng.serial_number = assembly_logs.serial_number 
  AND ng.model = assembly_logs.model 
  WHERE
  location = 'qa-audit' 
  ".$products2."
  ".$material_numbers2."
  AND DATE( created_at ) >= '".$first."' 
  AND DATE( created_at ) <= '".$last."'");

$emp = EmployeeSync::get();

$response = array(
  'status' => true,
  'ei' => $ei,
  'wi' => $wi,
  'emp' => $emp,
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

public function indexAuditFGPointCheck(Request $request)
{
  $material = DB::SELECT("SELECT
      * 
    FROM
    materials 
    WHERE
    (
    category = 'FG' 
    AND origin_group_code IN ( '072', '073' )) 
    OR (
    category = 'KD' 
    AND origin_group_code IN ( '073' )) 
    ORDER BY
    origin_group_code");
  $product = OriginGroup::where('origin_group_code','072')->orwhere('origin_group_code','073')->get();
  $point = DB::connection('ympimis_2')->table('qa_production_audit_points')->select('product','material_number','material_description')->distinct()->get();
  $emp_id = strtoupper(Auth::user()->username);
  $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
  return view('qa.audit_fg.point_check')
  ->with('title', 'Quality Assurance Audit FG / KD EI Point Check')
  ->with('product',$product)
  ->with('product2',$product)
  ->with('product3',$product)
  ->with('material',$material)
  ->with('material2',$material)
  ->with('material3',$material)
  ->with('point',$point)
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('jpn', '品保');
}

public function inputAuditFGPointCheck(Request $request)
{
  try {

    $product = $request->get('product');
    $material_number = $request->get('material_number');
    $material_description = $request->get('material_description');
    $qty_point = $request->get('qty_point');
    $point_check = $request->get('point_check');
    $standard = $request->get('standard');
    $defect_category = $request->get('defect_category');
    $point_check_details = $request->get('point_check_details');

    $index = 1;
    if ($request->get('data_condition') == 'EXISTING') {
      $cek = DB::connection('ympimis_2')->table('qa_production_audit_points')->where('product',$product)->where('material_number',$material_number)->orderby('ordering','desc')->first();
      if ($cek) {
        $index = $cek->ordering+1;
        $material_number = $cek->material_number;
        $material_description = $cek->material_description;
      }
    }

    for ($i=0; $i < count($point_check); $i++) { 
      $input = DB::connection('ympimis_2')->table('qa_production_audit_points')->insert([
        'product' => $product,
        'material_number' => $material_number,
        'material_description' => $material_description,
        'point_check' => $point_check[$i],
        'standard' => $standard[$i],
        'ordering' => $index,
        'defect_category' => $defect_category[$i],
        'point_check_details' => $point_check_details[$i],
        'created_by' => Auth::user()->id,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
      $index++;
    }
    $response = array(
      'status' => true,
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

public function fetchAuditFGPointCheck(Request $request)
{
  try {

    $point_check = DB::connection('ympimis_2')->table('qa_production_audit_points');

    if ($request->get('product') != '') {
      $point_check = $point_check->where('product',$request->get('product'));
    }

    $ng_list_pianica = NgList::where('location','kakuning')->where('remark','pianica')->get();
    $ng_list_recorder = NgList::where('location','kakuning')->where('remark','recorder')->get();

    $audit_id = null;

    if ($request->get('material_number') != '') {
      $point_check = $point_check->where('material_number','like','%'.$request->get('material_number').'%');

      $date = date('Y-m-d');
      $prefix_now = 'PKQA'.date("y").date("m");
      $code_generator = CodeGenerator::where('note','=','qa_packing')->first();
      if ($prefix_now != $code_generator->prefix){
        $code_generator->prefix = $prefix_now;
        $code_generator->index = '0';
        $code_generator->save();
      }

      $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
      $audit_id = $request->get('material_number').'_'.$code_generator->prefix . $number.'_'.date('Y-m-d H:i:s');
      $code_generator->index = $code_generator->index+1;
      $code_generator->save();
    }

    $emp = EmployeeSync::where('end_date',null)->where('department', 'LIKE', '%Educational Instrument (EI) Department%')->get();

    $point_check = $point_check->orderby('ordering')->get();
    $response = array(
      'status' => true,
      'point_check' => $point_check,
      'ng_list_pianica' => $ng_list_pianica,
      'emp' => $emp,
      'ng_list_recorder' => $ng_list_recorder,
      'audit_id' => $audit_id
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

public function editAuditFGPointCheck(Request $request)
{
  try {
    $point = DB::connection('ympimis_2')->table('qa_production_audit_points')->where('id',$request->get('id'))->first();
    $response = array(
      'status' => true,
      'point' => $point
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

public function deleteAuditFGPointCheck(Request $request)
{
  try {
    $point = DB::connection('ympimis_2')->table('qa_production_audit_points')->where('id',$request->get('id'))->first();

    $product = $point->product;
    $material_number = $point->material_number;

    $delete = DB::connection('ympimis_2')->table('qa_production_audit_points')->where('id',$request->get('id'))->delete();

    $adjust_point = DB::connection('ympimis_2')->table('qa_production_audit_points')->where('product',$product)->where('material_number',$material_number)->orderby('ordering')->get();
    if (count($adjust_point) > 0) {
      $index = 1;
      for ($i=0; $i < count($adjust_point); $i++) { 
        $update = DB::connection('ympimis_2')->table('qa_production_audit_points')->where('id',$adjust_point[$i]->id)->update([
          'ordering' => $index
        ]);
        $index++;
      }
    }
    $response = array(
      'status' => true,
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

public function updateAuditFGPointCheck(Request $request)
{
  try {
    $id = $request->get('id');
    $point_check = $request->get('point_check');
    $standard = $request->get('standard');
    $defect_category = $request->get('defect_category');
    $point_check_details = $request->get('point_check_details');

    $update = DB::connection('ympimis_2')->table('qa_production_audit_points')->where('id',$id)->update([
      'point_check' => $point_check,
      'standard' => $standard,
      'defect_category' => $defect_category,
      'point_check_details' => $point_check_details,
      'updated_at' => date('Y-m-d H:i:s')
    ]);
    $response = array(
      'status' => true,
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

public function indexAuditFGAudit(Request $request)
{
  $point = DB::connection('ympimis_2')->table('qa_production_audit_points')->select('product','material_number','material_description')->distinct()->get();

  $emp_all = EmployeeSync::where('end_date',null)->get();

  $auditor = EmployeeSync::where('end_date',null)->whereIn('employee_id',[
    'PI0101002',
    'PI0703001',
    'PI1308012',
    'PI0104001',
    'PI0104004',
    'PI1307007',
    'PI1307016',
  ])->get();

  $emp_groups = DB::SELECT("SELECT
    employee_syncs.employee_id,
    employee_syncs.`name`,
    employee_groups.`group` 
    FROM
    employee_groups
    JOIN employee_syncs ON employee_syncs.employee_id = employee_groups.employee_id 
    WHERE
    location = 'rc-assy' 
    AND employee_groups.created_at IS NOT NULL");

  $inspection_level = DB::SELECT("SELECT
      * 
    FROM
    qa_inspection_levels 
    WHERE
    inspection_level = 'General Inspection G-I Normal'");

  $cavity = DB::SELECT("SELECT
    type,
    GROUP_CONCAT( no_cavity ) AS cavity 
    FROM
    `push_block_masters` 
    GROUP BY
    type");

  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();
  return view('qa.audit_fg.audit')
  ->with('title', 'Quality Assurance Audit FG / KD EI')
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('emp',$emp)
  ->with('emp_all',$emp_all)
  ->with('emp_groups',$emp_groups)
  ->with('emp_groups2',$emp_groups)
  ->with('auditor',$auditor)
  ->with('cavity',$cavity)
  ->with('inspection_level',$inspection_level)
  ->with('point',$point)
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function inputAuditFGAudit(Request $request)
{
  try {
    $auditor = $request->get('auditor');
    $qty_auditor = $request->get('qty_auditor');
    $auditee_id = $request->get('auditee_id');
    $auditee_name = $request->get('auditee_name');
    $material_number = $request->get('material_number');
    $material_description = $request->get('material_description');
    $session = $request->get('session');
    $total_ng = $request->get('total_ng');
    $product = $request->get('product');
    $point_id = $request->get('point_id');
    $ordering = $request->get('ordering');
    $point_checks = $request->get('point_check');
    $standard = $request->get('standard');
    $point_check_details = $request->get('point_check_details');
    $defect_category = $request->get('defect_category');
    $result_check = $request->get('result_check');
    $note = $request->get('note');
    $material_audited = $request->get('material_audited');
    $audit_id = $request->get('audit_id');
    $qty_check = $request->get('qty_check');
    $critical_acc_re = $request->get('critical_acc_re');
    $major_acc_re = $request->get('major_acc_re');
    $minor_acc_re = $request->get('minor_acc_re');
    $critical_qty = $request->get('critical_qty');
    $major_qty = $request->get('major_qty');
    $minor_qty = $request->get('minor_qty');
    $status_lot = $request->get('status_lot');
    $ng_detail = $request->get('ng_detail');
    $qty_lot = $request->get('qty_lot');
    $cavity_head = $request->get('cavity_head');
    $cavity_middle = $request->get('cavity_middle');
    $cavity_foot = $request->get('cavity_foot');
    $cavity_ng = $request->get('cavity_ng');
    $box_qty = $request->get('box_qty');
    $box_pic = $request->get('box_pic');

    if (str_contains($auditor,',')) {
      $auditor_all = explode(',', $auditor);
      $auditors = [];
      for ($i=0; $i < count($auditor_all); $i++) { 
        $auditorss = EmployeeSync::where('employee_id',$auditor_all[$i])->first();
        array_push($auditors, $auditorss->name);
      }
      $auditor_name = join(',',$auditors);
    }else{
      $auditorss = EmployeeSync::where('employee_id',$auditor)->first();
      $auditor_name = $auditorss->name;
    }

    $input = DB::connection('ympimis_2')->table('qa_production_audits')->insert([
      'date' => date('Y-m-d'),
      'auditor_id' => $auditor,
      'auditor_name' => $auditor_name,
      'qty_auditor' => $qty_auditor,
      'auditee_id' => $auditee_id,
      'auditee_name' => $auditee_name,
      'material_number' => $material_number,
      'material_description' => $material_description,
      'session' => $session,
      'total_ng' => $total_ng,
      'product' => $product,
      'point_check_id' => $point_id,
      'box_qty' => $box_qty,
      'box_pic' => $box_pic,
      'ordering' => $ordering,
      'point_check' => $point_checks,
      'standard' => $standard,
      'point_check_details' => $point_check_details,
      'defect_category' => $defect_category,
      'qty_ng' => $result_check,
      'note' => $note,
      'material_audited' => $material_audited,
      'audit_id' => $audit_id,
      'qty_lot' => $qty_lot,
      'qty_check' => $qty_check,
      'cavity_head' => $cavity_head,
      'cavity_middle' => $cavity_middle,
      'cavity_foot' => $cavity_foot,
      'cavity_ng' => $cavity_ng,
      'status_lot' => $status_lot,
      'critical_acc_re' => $critical_acc_re,
      'major_acc_re' => $major_acc_re,
      'minor_acc_re' => $minor_acc_re,
      'critical_qty' => $critical_qty,
      'major_qty' => $major_qty,
      'minor_qty' => $minor_qty,
      'ng_detail' => $ng_detail,
      'due_date' => date('Y-m-d', strtotime('+14 day')),
      'created_by' => Auth::user()->id,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $response = array(
      'status' => true,
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

public function sendEmailAuditFG($id)
{
  try {
    $audit = DB::connection('ympimis_2')->select("SELECT
        * 
      FROM
      `qa_production_audits` 
      WHERE
      ( audit_id = '".$id."' AND qty_ng > 0 ) ");


    for ($i=0; $i < count($audit); $i++) { 
      $mail_to = [];
      $cc = [];

      $bcc = ['mokhamad.khamdan.khabibi@music.yamaha.com'];

      //Auditee
      $emp = EmployeeSync::where('employee_id',$audit[$i]->auditee_id)->first();
      $user = User::where('username',$audit[$i]->auditee_id)->first();
      if (str_contains($user->email,'@music.yamaha.com')) {
        array_push($mail_to, $user->email);
      }else{
        $approver = Approver::where('department',$emp->department)->where('section',$emp->section)->where('remark','Foreman')->first();
        if (!$approver) {
          $approver = Approver::where('department',$emp->department)->where('section',$emp->section)->where('remark','Chief')->first();
          if ($approver) {
            array_push($mail_to, $approver->approver_email);
          }
        }else{
          array_push($mail_to, $approver->approver_email);
        }
      }
      array_push($mail_to, 'ertikto.singgih@music.yamaha.com');
      array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');

        //Auditor
      $emp = EmployeeSync::where('employee_id',explode(',', $audit[$i]->auditor_id))->first();
      $user = User::where('username',explode(',', $audit[$i]->auditor_id))->first();
      if (str_contains($user->email,'@music.yamaha.com')) {
        array_push($mail_to, $user->email);
      }else{
        $approver = Approver::where('department',$emp->department)->where('section',$emp->section)->where('remark','Foreman')->first();
        if (!$approver) {
          $approver = Approver::where('department',$emp->department)->where('section',$emp->section)->where('remark','Chief')->first();
          if ($approver) {
            array_push($mail_to, $approver->approver_email);
          }
        }else{
          array_push($mail_to, $approver->approver_email);
        }
      }

      $emp = EmployeeSync::where('employee_id',$audit[$i]->auditee_id)->first();
      $manager = Approver::where('department',$emp->department)->where('remark','Manager')->first();
      array_push($cc, $manager->approver_email);
      array_push($cc, 'yayuk.wahyuni@music.yamaha.com');

      Mail::to($mail_to)->cc($cc,'CC')->bcc($bcc,'BCC')->send(new SendEmail($audit[$i], 'audit_fg_ei'));
    }

    $update = DB::connection('ympimis_2')->table('qa_production_audits')->where('audit_id',$id)->update([
      'send_status' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $response = array(
      'status' => true,
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

public function inputAuditFGHandling(Request $request)
{
  try {
    $id_handling = $request->get('id_handling');
    $handling = $request->get('handling');
    $handled_id = $request->get('handled_id');
    $handled_name = $request->get('handled_name');

    for ($i=0; $i < count($id_handling); $i++) { 
      $update = DB::connection('ympimis_2')->table('qa_production_audits')->where('id',$id_handling[$i])->update([
        'handling' => $handling[$i],
        'handling_id' => $handled_id[$i],
        'handling_name' => $handled_name[$i],
        'handling_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    }
    $response = array(
      'status' => true,
      'message' => 'Success Input Penanganan'
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

public function indexAuditFGPdf($id)
{
  $audit = DB::connection('ympimis_2')->table('qa_production_audits')->where('audit_id',$id)->orderBy('ordering','asc')->get();
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

  $pdf = \App::make('dompdf.wrapper');
  $pdf->getDomPDF()->set_option("enable_php", true);
  $pdf->setPaper('A4', 'landscape');

  $pdf->loadView('qa.audit_fg.pdf_audit', array(
    'audit' => $audit,
  ));

  return $pdf->stream($audit[0]->audit_id." - ".$audit[0]->material_description." (".date('Y-m-d',strtotime($audit[0]->created_at)).").pdf");

}

public function indexAuditFGDelete($id)
{
  $audit = DB::connection('ympimis_2')->table('qa_production_audits')->where('audit_id',$id)->get();
  if (count($audit) > 0) {
    $audit = DB::connection('ympimis_2')->table('qa_production_audits')->where('audit_id',$id)->delete();
  }
  return redirect()->back()->with(array('alert' => 'Success'));
}

public function indexAuditFeelingReport()
{
  $fy_all = DB::SELECT("SELECT DISTINCT
    ( fiscal_year ) 
    FROM
    weekly_calendars");
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

  $auditor = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT(auditor_name) from qa_feelings");

  return view('qa.feeling.report')
  ->with('title', 'Quality Assurance ~ Report Penyamaan Feeling Kensa FG')
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('fy_all',$fy_all)
  ->with('auditor',$auditor)
  ->with('emp',$emp)
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function fetchAuditFeelingReport(Request $request)
{
  try {
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
      if ($date_to == "") {
        $first = date('Y-m-01');
        $last = date('Y-m-d');
      }else{
        $first = date('Y-m-01');
        $last = $date_to;
      }
    }else{
     if ($date_to == "") {
      $first = $date_from;
      $last = date('Y-m-d');
    }else{
      $first = $date_from;
      $last = $date_to;
    }
  }

  $auditor = $request->get('auditor');

  $auditors = '';
  if ($auditor != '') {
    $auditors = "AND auditor_name = '".$auditor."'";
  }


  $audit = DB::connection('ympimis_2')->SELECT("SELECT
          *,
    DATE_FORMAT( created_at, '%d-%b-%Y' ) AS date_audit_name
    FROM
    qa_feelings where DATE_FORMAT(date,'%Y-%m-%d') >= '".$first."'
    AND DATE_FORMAT(date,'%Y-%m-%d') <= '".$last."'
    ".$auditors."
    order by created_at");
  $response = array(
    'status' => true,
    'audit' => $audit,
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

public function indexAuditIK()
{
  $fy_all = DB::SELECT("SELECT DISTINCT
    ( fiscal_year ) 
    FROM
    weekly_calendars");
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();
  $point = DB::connection('ympimis_2')->table('qa_process_audit_points')->select('document_number','document_name')->distinct()->where('document_number','like','%-QC%')->get();

  return view('qa.audit_ik.index')
  ->with('title', 'Quality Assurance ~ Audit IK')
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('fy_all',$fy_all)
  ->with('emp',$emp)
  ->with('point',$point)
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function fetchAuditIK(Request $request)
{
  try {
    $fy = WeeklyCalendar::select('fiscal_year')->distinct()->where('week_date',date('Y-m-d'))->first();
    $fy_first_last = DB::SELECT("( SELECT DISTINCT
      (
      DATE_FORMAT( week_date, '%Y-%m' )) AS `month`,
      DATE_FORMAT( week_date, '%b %Y' ) AS month_name 
      FROM
      weekly_calendars 
      WHERE
      fiscal_year = '".$fy->fiscal_year."'
      ORDER BY
      week_date 
      LIMIT 1 
      ) UNION ALL
      (
      SELECT DISTINCT
      (
      DATE_FORMAT( week_date, '%Y-%m' )) AS `month`,
      DATE_FORMAT( week_date, '%b %Y' ) AS month_name 
      FROM
      weekly_calendars 
      WHERE
      fiscal_year = '".$fy->fiscal_year."'
      ORDER BY
      week_date DESC 
      LIMIT 1)");
    $month_from = $request->get('month_from');
    $month_to = $request->get('month_to');
    if ($month_from == "") {
     if ($month_to == "") {
      $first = "'".$fy_first_last[0]->month."'";
      $first_month = $fy_first_last[0]->month;
      $last = "'".$fy_first_last[1]->month."'";
      $last_month = $fy_first_last[1]->month;
    }else{
      $first = "'".$fy_first_last[0]->month."'";
      $first_month = $fy_first_last[0]->month;
      $last = "'".$month_to."'";
      $last_month = $month_to;
    }
  }else{
   if ($month_to == "") {
    $first = "'".$month_from."'";
    $first_month = $month_from;
    $last = "'".$fy_first_last[1]->month."'";
    $last_month = $fy_first_last[1]->month;
  }else{
    $first = "'".$month_from."'";
    $first_month = $month_from;
    $last = "'".$month_to."'";
    $last_month = $month_to;
  }
}

$document = "";
if ($request->get('document_number') != '') {
  $document = "AND document_number = '".$request->get('document_number')."'";
}
$audit = DB::connection('ympimis_2')->select("SELECT
  a.`month`,
  a.month_name,
  sum( CASE WHEN a.qty = 'OK' THEN 1 ELSE 0 END ) AS qty_ok,
  sum( CASE WHEN a.qty = 'NG' THEN 1 ELSE 0 END ) AS qty_ng 
  FROM
  (
  SELECT
  DATE_FORMAT( created_at, '%Y-%m' ) AS `month`,
  DATE_FORMAT( created_at, '%b-%Y' ) AS `month_name`,
  IF
  ( GROUP_CONCAT( DISTINCT ( decision )) LIKE '%NG%', 'NG', 'OK' ) AS qty 
  FROM
  `qa_kensa_audits` 
  WHERE
  DATE_FORMAT( created_at, '%Y-%m' ) >= ".$first."
  AND DATE_FORMAT( created_at, '%Y-%m' ) <= ".$last."
  ".$document."
  GROUP BY
  DATE_FORMAT( created_at, '%Y-%m' ),
  DATE_FORMAT( created_at, '%b-%Y' ),
  decision,
  document_number 
  ) a 
  GROUP BY
  a.`month`,
  a.month_name");

$audit_all = DB::connection('ympimis_2')->select("SELECT
  DATE_FORMAT( created_at, '%Y-%m' ) AS `month`,
  DATE_FORMAT( created_at, '%b-%Y' ) AS `month_name`,
  DATE_FORMAT( created_at, '%d-%b-%Y' ) as dates,
  auditor_id,
  auditor_name,
  document_number,
  document_name,
  audit_id,
  IF
  ( GROUP_CONCAT( DISTINCT ( decision )) LIKE '%NG%', 'NG', 'OK' ) AS result 
  FROM
  `qa_kensa_audits` 
  WHERE
  DATE_FORMAT( created_at, '%Y-%m' ) >= ".$first."
  AND DATE_FORMAT( created_at, '%Y-%m' ) <= ".$last."
  ".$document."
  GROUP BY
  DATE_FORMAT( created_at, '%Y-%m' ),
  DATE_FORMAT( created_at, '%b-%Y' ),
  DATE_FORMAT( created_at, '%d-%b-%Y' ),
  auditor_id,
  auditor_name,
  document_number,
  document_name,
  audit_id
  order by created_at");

$month_all = DB::SELECT("SELECT DISTINCT
  (
  DATE_FORMAT( week_date, '%Y-%m' )) AS `month`,
  DATE_FORMAT( week_date, '%b %Y' ) AS month_name 
  FROM
  weekly_calendars 
  WHERE
  DATE_FORMAT( week_date, '%Y-%m' ) >= ".$first."
  AND DATE_FORMAT( week_date, '%Y-%m' ) <= ".$last."
  ORDER BY
  week_date");

$audits = DB::connection('ympimis_2')->table('qa_kensa_audits')->where(DB::RAW("DATE_FORMAT( created_at, '%Y-%m' )"),'>=',$first_month)->where(DB::RAW("DATE_FORMAT( created_at, '%Y-%m' )"),'<=',$last_month);
if ($request->get('document_number') != '') {
  $audits = $audits->where('document_number',$request->get('document_number'));
}
$audits = $audits->get();
$response = array(
  'status' => true,
  'audit' => $audit,
  'audit_all' => $audit_all,
  'month_all' => $month_all,
  'audits' => $audits,
  'monthTitle' => date('M-Y',strtotime($first_month.'-01')).' - '.date('M-Y',strtotime($last_month.'-01'))
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

public function sendEmailAuditIK(Request $request)
{
  $mail_to = [];
  array_push($mail_to, 'eko.prasetyo.wicaksono@music.yamaha.com');
  array_push($mail_to, 'khoirul.umam@music.yamaha.com');

  $cc = [];
  array_push($mail_to, 'agustina.hayati@music.yamaha.com');
  array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');
  $datas = null;

  Mail::to($mail_to)->cc($cc,'CC')->send(new SendEmail($datas, 'audit_ik_qa_reminder'));
}

public function indexAuditIKAudit()
{
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();
  $point = DB::connection('ympimis_2')->table('qa_process_audit_points')->select('document_number','document_name')->distinct()->where('document_number','like','%-QC%')->get();

  $certificate = DB::connection('ympimis_2')->select("
    SELECT
    certificate_name 
    FROM
    qa_certificates 
    WHERE
    ( approval_type = 'QA' AND `status` = '1' AND note = '-' AND `subject` LIKE '%FG%' ) 
    OR ( approval_type = 'QA' AND `status` = '1' AND note = '-' AND `subject` LIKE '%FINISHED%' ) 
    GROUP BY
    certificate_name");

  $certificate_all = DB::connection('ympimis_2')->select("SELECT
    certificate_id,
    certificate_name,
    employee_id,
    `name`,
    GROUP_CONCAT( DISTINCT ( `subject` ) ) AS `subject` 
    FROM
    qa_certificates 
    WHERE
    ( approval_type = 'QA' AND `status` = '1' AND note = '-' AND `subject` LIKE '%FG%' ) 
    OR ( approval_type = 'QA' AND `status` = '1' AND note = '-' AND `subject` LIKE '%FINISHED%' ) 
    GROUP BY
    certificate_id,
    certificate_name,
    employee_id,
    `name`");

  return view('qa.audit_ik.audit')
  ->with('title', 'Quality Assurance ~ Audit IK')
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('emp',$emp)
  ->with('point',$point)
  ->with('certificate',$certificate)
  ->with('certificate2',$certificate)
  ->with('certificate_all',$certificate_all)
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function fetchAuditIKAudit(Request $request)
{
  try {
    $document_number = $request->get('document_number');
    $document_name = $request->get('document_name');

    $point_check = DB::CONNECTION('ympimis_2')->table('qa_process_audit_points')->where('document_number',$document_number)->get();
    $apd_alat = DB::CONNECTION('ympimis_2')->table('qa_process_audit_safeties')->where('document_number',$document_number)->where('category_safety','Alat Kelengkapan Diri')->first();
    $mesin = DB::CONNECTION('ympimis_2')->table('qa_process_audit_safeties')->where('document_number',$document_number)->where('category_safety','Alat / Mesin yang Digunakan')->first();

    $response = array(
      'status' => true,
      'point_check' => $point_check,
      'apd_alat' => $apd_alat,
      'mesin' => $mesin,
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

public function inputAuditIKAudit(Request $request)
{
  try {
    $auditor_id = $request->get('auditor_id');
    $auditor_name = $request->get('auditor_name');
    $auditee = $request->get('auditee');
    $auditee_id = $request->get('auditee_id');
    $auditee_name = $request->get('auditee_name');
    $auditee_status = $request->get('auditee_status');
    $document_number = $request->get('document_number');
    $document_name = $request->get('document_name');
    $alat = $request->get('alat');
    $mesin = $request->get('mesin');
    $point_id = $request->get('point_id');
    $decision = $request->get('decision');
    $note = $request->get('note');
    $audit_type = $request->get('audit_type');
    $upper = $request->get('upper');
    $lower = $request->get('lower');
    $uom = $request->get('uom');
    $hasil = $request->get('hasil');
    $point = DB::connection('ympimis_2')->table('qa_process_audit_points')->where('id',$point_id)->first();

    $code_generator = CodeGenerator::where('note', '=', 'audit_ik_qa')->first();
    $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

    $serial_number = $code_generator->prefix.$number;

    $code_generator->index = $code_generator->index+1;

    $input = DB::connection('ympimis_2')->table('qa_kensa_audits')->insert([
      'audit_id' => $serial_number,
      'auditor_id' => $auditor_id,
      'auditor_name' => $auditor_name,
      'auditee_id' => $auditee_id,
      'auditee_name' => $auditee_name,
      'auditee_status' => $auditee_status,
      'document_number' => $document_number,
      'document_name' => $document_name,
      'work_process' => $point->work_process,
      'work_point' => $point->work_point,
      'work_safety' => $point->work_safety,
      'alat' => $alat,
      'mesin' => $mesin,
      'decision' => $decision,
      'point_id' => $point_id,
      'note' => $note,
      'audit_type' => $audit_type,
      'lower' => $lower,
      'upper' => $upper,
      'uom' => $uom,
      'hasil' => $hasil,
      'created_by' => Auth::id(),
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);
    $code_generator->save();
    $response = array(
      'status' => true,
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

public function indexFirstProduct()
{
  $fy_all = DB::SELECT("SELECT DISTINCT
    ( fiscal_year ) 
    FROM
    weekly_calendars");
  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

  $point = DB::connection('ympimis_2')->table('qa_first_product_points')->select('location','product','material_number','material_description')->distinct()->get();

  return view('qa.first_product.index')
  ->with('title', 'Quality Assurance Audit Produk Pertama')
  ->with('title_jp', '品証 工程監査')
  ->with('page', 'Quality Assurance')
  ->with('fy_all',$fy_all)
  ->with('emp',$emp)
  ->with('point',$point)
  ->with('role',Auth::user()->role_code)
  ->with('employee_id',Auth::user()->username)
  ->with('jpn', '品保');
}

public function fetchFirstProduct(Request $request)
{
  try {
    $response = array(
      'status' => true,
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
}