<?php

namespace App\Http\Controllers;

use File;
use DataTables;
use ZipArchive;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\DailyReport;
use App\MisAudit;
use App\DailyReportAttachment;
use App\EmployeeSync;
use App\MisPointAudit;
use App\CodeGenerator;
use App\User;
use App\MisInventory;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use App\MisInventoryNew;
use App\MisInventoryDetail;
use App\MisInventoryResult;
use App\YmpiLocation;


class DailyReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->cat = [
            'Network Maintenance',
            'Hardware Maintenance',
            'Hardware Installation',
            'Software Installation',
            'Design',
            'System Analysis',
            'System Programming',
            'Bug & Error',
            'Trial & Training'

        ];

        $this->loc = [
            'Management Information System',
            'Accounting',
            'Assembly (WI-A)',
            'Educational Instrument (EI)',
            'General Affairs',
            'Human Resources',
            'Logistic',
            'Maintenance',
            'Parts Process (WI-PP)',
            'Procurement',
            'Production Control',
            'Production Engineering',
            'Purchasing',
            'Quality Assurance',
            'Welding-Surface Treatment (WI-WST)'
        ];
    }
/**
* Display a listing of the resource.
*
* @return \Illuminate\Http\Response
*/
public function index()
{
    $cat = $this->cat;
    $loc = $this->loc;
    return view('daily_reports.index', array(
        'cat' => $cat,
        'loc' => $loc,

    ))->with('page', 'Daily Report');
//
}

/**
* Show the form for creating a new resource.
*
* @return \Illuminate\Http\Response
*/
public function create(Request $request)
{
    $id = Auth::id();

    $code_generator = CodeGenerator::where('note','=','report')->first();
    $number = sprintf("%'.0" . $code_generator->length . "d\n" , $code_generator->index);
    $code = $number+1;
    $number1 = sprintf("%'.0" . $code_generator->length . "d" , $code);
    $lop = $request->get('lop');
    try{
        for ($i=1; $i <= $lop ; $i++) {
            $description = "description".$i;
            $duration = "duration".$i;

            $data = new DailyReport([
                'report_code' => $code_generator->prefix . $number1,
                'category' => $request->get('category'),
                'description' => $request->get($description),
                'location' => $request->get('location'),
                'duration' => $request->get($duration),
                'begin_date' => $request->get('datebegin'),
                'target_date' => $request->get('datetarget'),
                'finished_date' => $request->get('datefinished'), 
                'created_by' => $id
            ]);
            $data->save();
        }
        if($request->hasFile('reportAttachment')){
            $files = $request->file('reportAttachment');
            foreach ($files as $file) 
            {
                $number= $code_generator->prefix . $number1;
                $data = file_get_contents($file);
                $photo_number = $number . $file->getClientOriginalName() ;
                $ext = $file->getClientOriginalExtension();
                $filepath = public_path() . "/uploads/dailyreports/" . $photo_number;
                $attachment = new DailyReportAttachment([
                    'report_code' => $code_generator->prefix . $number1,
                    'file_name' =>  $photo_number,
                    'file_path' => "/uploads/dailyreports/",
                    'created_by' => $id,
                ]);
                $attachment->save();
                File::put($filepath, $data);
            }
        }

        $code_generator->index = $code_generator->index+1;
        $code_generator->save();
        return redirect('/index/daily_report')->with('status', 'Crete daily report success')->with('page', 'Daily Report');
    }
    catch (QueryException $e){
        return redirect('/index/daily_report')->with('error', $e->getMessage())->with('page', 'Daily Report');
    }
}

public function fetchDailyReport(){
    $daily_reports = DailyReport::leftJoin('users', 'users.id', '=', 'daily_reports.created_by')
    ->leftJoin('roles', 'users.role_code', '=', 'roles.role_code')
    ->leftJoin(db::raw('(select report_code, count(file_name) as att from daily_report_attachments group by report_code) as daily_report_attachments'), 'daily_report_attachments.report_code', '=', 'daily_reports.report_code')
    ->select('roles.role_code', 'users.name', 'daily_reports.category', 'daily_reports.description', 'daily_reports.location', 'daily_reports.begin_date', 'daily_reports.target_date', 'daily_reports.finished_date', 'daily_reports.report_code', 'daily_report_attachments.att', db::raw('concat(round(time_to_sec(daily_reports.duration)/60, 0), " Min") as duration'))
    ->distinct()
    ->orderByRaw('daily_reports.begin_date desc, users.name asc')
    ->limit(500)
    ->get();

    return DataTables::of($daily_reports)
    ->addColumn('action', function($daily_reports){
        return '<a href="javascript:void(0)" class="btn btn-xs btn-info" onClick="detailReport(id)" id="' . $daily_reports->report_code . '">Details</a>';
    })
    ->addColumn('attach', function($daily_reports){
        if($daily_reports->att > 0){
            return '<a href="javascript:void(0)" id="' . $daily_reports->report_code . '" onClick="downloadAtt(id)" class="fa fa-paperclip"> ' . $daily_reports->att . '</a>';
        }
        else{
            return '-';
        }
    })
    ->addColumn('action', function($daily_reports){
        return '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" onClick="editReport(id)" id="' . $daily_reports->report_code . '"><i class="fa fa-edit"></i></a>';
    })
    ->rawColumns(['action' => 'action', 'attach' => 'attach'])
    ->make(true);
}

public function fetchDailyReportDetail(Request $request){
    $daily_reports = DailyReport::where('report_code', '=', $request->get('report_code'))->select('description', 'duration')->get();

    $response = array(
        'status' => true,
        'daily_reports' => $daily_reports,
    );
    return Response::json($response);
}

public function downloadDailyReport(Request $request){
    $report_attachments = DailyReportAttachment::where('report_code', '=', $request->get('report_code'))->get();

    $zip = new ZipArchive();
    $zip_name = $request->get('report_code').".zip";
    $zip_path = public_path() . '/' . $zip_name;
    File::delete($zip_path);
    $zip->open($zip_name, ZipArchive::CREATE);

    foreach ($report_attachments as $report_attachment) {
        $file_path = public_path() . $report_attachment->file_path . $report_attachment->file_name;
        $file_name = $report_attachment->file_name;
        $zip->addFile($file_path, $file_name);
    }
    $zip->close();

    $path = asset($zip_name);

    $response = array(
        'status' => true,
        'file_path' => $path,
    );
    return Response::json($response);
}


public function store(Request $request)
{
//
}

public function show($id)
{

}

public function edit(Request $request)
{
    $daily_reports = DailyReport::where('report_code', '=', $request->get('report_code'))->get();
    $daily_reportsHead = DailyReport::where('report_code', '=', $request->get('report_code'))->select('report_code','category','location','begin_date','finished_date','target_date')->distinct()->get();
    $response = array(
        'status' => true,
        'daily_reports' => $daily_reports,
        'daily_reportsHead' => $daily_reportsHead,
    );
    return Response::json($response);
}


public function update(Request $request)
{
    try{
        $id_user = Auth::id();
        $ids = $request->get('report_id');
        $lop = $request->get('lop2');
        if($ids != null){
            foreach ($ids as $id) 
            {
                $description = "description".$id;
                $duration = "duration".$id;
                $head = DailyReport::where('id','=', $id)
                ->withTrashed()       
                ->first();
                $head->category = $request->get('category');
                $head->location = $request->get('location');
                $head->begin_date = $request->get('begindate');
                $head->target_date = $request->get('targetdate');
                $head->finished_date = $request->get('finisheddate');
                $head->description = $request->get($description);
                $head->duration = $request->get($duration);
                $head->save();
            }
        }
        else{
            return redirect('/index/daily_report')->with('error', 'All report details must be filled.')->with('page', 'Daily Report');  
        }

        for ($i=2; $i <= $lop ; $i++) {
            $description = "description".$i;
            $duration = "duration".$i;

            $data = new DailyReport([
                'report_code' => $request->get('report_code'),
                'category' => $request->get('category'),
                'description' => $request->get($description),
                'location' => $request->get('location'),
                'duration' => $request->get($duration),
                'begin_date' => $request->get('begindate'),
                'target_date' => $request->get('targetdate'),
                'finished_date' => $request->get('finisheddate'), 
                'created_by' => $id_user
            ]);
            $data->save();
        }

        return redirect('/index/daily_report')->with('status', 'Update daily report success')->with('page', 'Daily Report');
    }
    catch (QueryException $e){
        return redirect('/index/daily_report')->with('error', $e->getMessage())->with('page', 'Daily Report');
    }

}

public function delete(Request $request)
{
    try{
        $master = DailyReport::where('id','=' ,$request->get('id'))
        ->delete();
    }catch (QueryException $e){
        return redirect('/index/daily_report')->with('error', $e->getMessage())->with('page', 'Daily Report');
    }

}

public function indexAuditMIS()
{
    $title = "MIS DAILY AUDIT";
    $title_jp = "??";

    $employee = EmployeeSync::where('employee_id', '=', Auth::user()->username)
    ->select('employee_id', 'name', 'section', 'group')
    ->first();

    return view('audit_mis.index', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'employee_id' => Auth::user()->username,
        'name' => $employee->name
    ))->with('page', 'MIS Audit');
}

public function fetchAuditCheckList()
{
    $cl = MisPointAudit::select('id','system_name', 'location', 'department', 'item_check')->get();

    $response = array(
        'status' => true,
        'check_list' => $cl
    );
    return Response::json($response);
}

public function postCheckAudit(Request $request)
{
    $ng = $request->get('ng_list');

    $aud = MisAudit::firstOrNew(array('audit_date' => date('Y-m-d'), 'pic' => Auth::user()->username));
    $aud->created_by = Auth::user()->username;
    $aud->save();

    $id = DB::getPdo()->lastInsertId();

// foreach ($ng as $key) {
    $tujuan_upload = 'files/mis';
    $file = $request->file('fileData');
    $filename = md5($id.$ng.date('YmdHisa')).'.'.$request->input('extension');
    $file->move($tujuan_upload,$filename);

    db::table('mis_audit_details')->insert(['point_audit' => $ng, 'created_by' => Auth::user()->username, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'id_audit' => $id, 'remark' => $filename]);
// }

    $response = array(
        'status' => true,
    );
    return Response::json($response);
}


// ----------------------------  INVENTORY MIS ------------------------

public function indexInventoryMIS()
{
    $title = "MIS Inventory";
    $title_jp = "??";

    $emp = EmployeeSync::whereNull('end_date')->select('employee_id', 'name')->get();

    $ctg = db::select('SELECT DISTINCT
        category 
        FROM
        mis_inventories 
        ORDER BY
        category ASC');

    $loc = db::select('SELECT DISTINCT
        location 
        FROM
        mis_inventories 
        WHERE
        location IS NOT NULL 
        AND location != "" 
        ORDER BY
        location ASC');

    $jum = db::select('SELECT
        COUNT(id) as jums
        FROM
        `mis_inventory_details` 
        WHERE
        created_by = "'.Auth::user()->username.'" 
        AND STATUS = 1');

    return view('inventory_mis.inventory_list', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'emp' => $emp,
        'ctg' => $ctg,
        'loc' => $loc,
        'jum' => $jum

    ))->with('page', 'MIS Inventory');
}

public function FetchGrafikCategory(){
    $data = db::select('SELECT COALESCE
        ( category, "Other" ) AS category,
        sum(
        IF
        ( category IS NULL OR category = "Other", 1, qty )) AS jumlah 
        FROM
        mis_inventories 
        GROUP BY
        category 
        ORDER BY
        category ASC');

    $condition = db::select('SELECT
        `condition`,
        count( id ) jumlah 
        FROM
        mis_inventories 
        GROUP BY
        `condition`');

    $response = array(
        'status' => true,
        'data' => $data,
        'condition' => $condition
    );
    return Response::json($response);
}

public function FetchGrafikDetail(Request $request){
    $category = $request->get('category');
    $name = $request->get('name');
    
    $data = '';
    $kondisi = '';

    if ($name == 'OK') {
        $kondisi = db::select('SELECT
            receive_date,
            category,
            description,
            employee_syncs.`name`,
            `condition`, qty
            FROM
            mis_inventories
            LEFT JOIN employee_syncs ON employee_syncs.employee_id = mis_inventories.used_by 
            WHERE
            `condition` = "OK"');
    }else{
        $kondisi = db::select('SELECT
            receive_date,
            category,
            description,
            employee_syncs.`name`,
            `condition`, qty
            FROM
            mis_inventories
            LEFT JOIN employee_syncs ON employee_syncs.employee_id = mis_inventories.used_by 
            WHERE
            `condition` is null');
    }

    if ($category == 'Other') {
        $data = db::select('SELECT
            receive_date,
            category,
            description,
            employee_syncs.`name`,
            `condition` 
            FROM
            mis_inventories
            LEFT JOIN employee_syncs ON employee_syncs.employee_id = mis_inventories.used_by 
            WHERE
            category is null');
    }else{
        $data = db::select('SELECT
            receive_date,
            category,
            description,
            employee_syncs.`name`,
            `condition` 
            FROM
            mis_inventories
            LEFT JOIN employee_syncs ON employee_syncs.employee_id = mis_inventories.used_by 
            WHERE
            category = "'.$category.'"');
    }

    $response = array(
        'status' => true,
        'data' => $data,
        'kondisi' => $kondisi
    );
    return Response::json($response);
}

public function fetchInventoryMIS()
{

    $jum = db::select('SELECT
        COUNT(id) as jums
        FROM
        `mis_inventory_details` 
        WHERE
        created_by = "'.Auth::user()->username.'" 
        AND STATUS = 1');

    $inv = db::select("SELECT
        mis_inventory_news.id,
        DATE_FORMAT( mis_inventory_news.created_at, '%Y-%m-%d' ) AS tanggal,
        mis_inventory_news.id_order,
        mis_inventory_news.no_po,
        mis_inventory_news.no_pr,
        mis_inventory_news.date_to,
        mis_inventory_news.category,
        mis_inventory_news.nama_item,
        mis_inventory_news.qty,
        mis_inventory_news.remark,
        mis_inventory_news.created_at,
        mis_inventory_news.status,
        mis_inventory_news.updated_at,
        acc_purchase_requisition_items.peruntukan,
        acc_purchase_requisition_items.no_pr AS no_prs 
        FROM
        mis_inventory_news
        LEFT JOIN acc_purchase_requisition_items ON mis_inventory_news.no_pr = acc_purchase_requisition_items.no_pr 
        AND mis_inventory_news.nama_item = acc_purchase_requisition_items.item_desc
        WHERE
        mis_inventory_news.deleted_at IS NULL and mis_inventory_news.remark is null OR mis_inventory_news.remark = 'checklist'
        ORDER BY
        mis_inventory_news.remark ASC
        ");

    $data_compl = db::select("
        SELECT
        id,
        checklist_id,
        id_data,
        date_to,
        no_po,
        qty,
        category,
        nama_item,
        pic_pengambil_nik,
        pic_pengambil_name,
        peruntukan,
        receive_date,
        remark 
        FROM
        `mis_inventory_results`
        ORDER BY id
        ");

    $response = array(
        'status' => true,
        'inventory' => $inv,
        'jum' => $jum,
        'data_compl' => $data_compl
    );
    return Response::json($response);
}

public function createInventoryMIS(Request $request)
{

    try {
        $new = [];
        foreach ($request->get('item') as $value) {
            $inv = new MisInventory;

            $inv->category = $value['category'];
            // $inv->device = $value['device'];
            $inv->serial_number = $value['serial'];
            $inv->description = $value['description'];
            $inv->project = $value['project'];
            $inv->location = $value['location'];
            $inv->qty = $value['quantity'];
            $inv->used_by = $value['pic'];
            $inv->receive_date = $request->get('receive_date');
            $inv->created_by = Auth::id();
            $inv->condition = 'OK';

            $inv->save();

            array_push($new, $inv->id);
        }

        $response = array(
            'status' => true,
            'new' => $new

        );
        return Response::json($response);
    } catch (QueryException $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    } 
}

public function fetchInventoryMISbyId(Request $request)
{
    $inv = MisInventory::where('id', '=', $request->get('id'))->first();
    
    $inventory = db::select('SELECT
        category,
        serial_number,
        description,
        project,
        location,
        qty,
        `name`,
        used_by,
        receive_date
        FROM
        mis_inventories
        LEFT JOIN users ON users.username = mis_inventories.used_by 
        WHERE
        mis_inventories.id = "'.$request->get('id').'"');

    $emp = EmployeeSync::whereNull('end_date')->select('employee_id', 'name')->get();

    $response = array(
        'status' => true,
        'inventory' => $inventory,
        'emp' => $emp
    );
    return Response::json($response);
}

public function updateInventoryMIS(Request $request)
{
    MisInventory::where('id', '=', $request->get("id_inv"))
    ->update([
        'category' => $request->get("cat_edit"),
        'serial_number' => $request->get("serial_edit"),
        'description' => $request->get("desc_edit"),
        'project' => $request->get('proj_edit'),
        'location' => $request->get('loc_edit'),
        'qty' => $request->get('qty_edit'),
        'used_by' => $request->get('used_by_edit'),
        'receive_date' => $request->get('receive_date_edit'),
        'condition' => 'OK'
    ]);

    $response = array(
        'status' => true
    );
    return Response::json($response);
}

public function deleteInventoryMIS(Request $request)
{
    MisInventory::where('id', '=', $request->get("id"))->delete();

    $response = array(
        'status' => true
    );
    return Response::json($response);
}

public function printInventory($id)
{
    $printer_name = 'MIS';

    $connector = new WindowsPrintConnector($printer_name);
    $printer = new Printer($connector);

    $datas =  MisInventory::where("id", '=', $id)->first();

    return view('inventory_mis.mis_print_sato', array(
        'device_detail' => $datas,
    ));

    // $data = [
    //     'id' => $datas->id,
    //     'cat' => $datas->category,
    //     'device' => $datas->device,
    //     'desc' => $datas->description,
    //     'proj' => $datas->project,
    //     'loc' => $datas->location,
    //     'qty' => $datas->qty,
    //     'used_by' => $datas->used_by,
    //     'cond' => $datas->condition,
    //     'rc_date' => $datas->receive_date
    // ];

    // $pdf = \App::make('dompdf.wrapper');
    // $pdf->getDomPDF()->set_option("enable_php", true);
    // $pdf->setPaper([0, 0, 70.8661, 232.441], 'landscape');
    // $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

    // $pdf->loadView('inventory_mis.mis_print', array(
    //     'data' => $data
    // ));

    // return $pdf->download("MIS_QR.pdf");

    // $img = EscposImage::load("apar-qr.png");

    // $printer->setTextSize(1, 1);
    // // $printer->text($datas->category. "-". $datas->location);
    // $printer->initialize();
    // $printer->setTextSize(1, 1);
    // $printer->setJustification(Printer::JUSTIFY_LEFT);

    // // $printer->graphics($img);
    // $printer->qrCode('RE'.$id, Printer::QR_ECLEVEL_L, 3, Printer::QR_MODEL_2);

    // $printer->text("MIS-".$id);
    // $printer->feed(1);
    // $printer->cut();
    // $printer->close();
}

public function printInventory2($id)
{
    $device_detail =  MisInventory::where("id", '=', $id)->first();
    
    return view('inventory_mis.mis_print_sato', array(
        'device_detail' => $device_detail,
    ));
}


public function inputInventoryMIS(Request $request)
{
    try
    {

        $ids = $request->get('ids');
        $dates = $request->get('dates');
        $no_po = $request->get('no_po');
        $cat = $request->get('cat');
        $nama_item = $request->get('nama_item');
        $qtys = $request->get('qtys');
        $date_to = $request->get('date_to');

        if ($request->get('peruntukan') == null) {
            $peruntukan = '';
        }else{
            $peruntukan = $request->get('peruntukan');
        }

        $insert_checklist = db::table('mis_inventory_details')
        ->insert([
            'id_data' => $ids, 
            'date_to' => $date_to,
            'date_receive' => $dates,
            'no_po' => $no_po,
            'category' => $cat,
            'nama_item' => $nama_item,
            'qty' => $qtys,
            'status' => 1,
            'peruntukan' => $peruntukan,
            'created_by' => Auth::user()->username,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $update_data = db::table('mis_inventory_news')
        ->where('id','=', $ids)
        ->update([
            'status' => 1,
            'remark' => 'checklist',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

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


public function indexCartInventoryCheck(Request $request)
{

    $checklist = db::table('mis_inventory_details')->where('status','=',1)
    ->where('created_by','=',Auth::user()->username)
    ->get();

    $emps = db::table('employee_syncs')
    ->whereNull('end_date')
    ->get();

    $emps_mis = db::table('employee_syncs')
    ->where('department','=','Management Information System Department')
    ->whereNull('end_date')
    ->get();

    $data_area = db::table('ympi_locations')
    ->whereNull('deleted_at')
    ->get();

    return view('inventory_mis.cart_inventory', array(
        'checklist' => $checklist,
        'emps' => $emps,
        'emps_mis' => $emps_mis,
        'data_area' => $data_area
    ))->with('page', 'Checklist Container');

}

public function inputInventoryChecklist(Request $request)
{

    $now = date('Y-m-d H:i:s');
    $checklist_answer = json_decode($request->input('checklist_answer1'));

    try {

        $areas = $request->get('area');

        $prefix_now = date('Ym');

        $dat = db::raw('date_format($request->input("dates"), "%Y-%m-%d")');

        $code_generator = CodeGenerator::where('note', '=', 'mis_inventory')->first();

        $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
        $checklist_id = $code_generator->prefix . $number;
        $code_generator->index = $code_generator->index + 1;
        $code_generator->save();

        for ($i=0; $i < count($checklist_answer); $i++) { 

            $pic_mis1 = explode('_', $request->input('pic_mis'));
            $pic_penerima = explode('_', $request->input('pic_penerima'));

            $datas = MisInventoryDetail::where('id','=',$checklist_answer[$i]->id)->select('id','id_data', 'no_po','no_pr','category','nama_item','qty','remark','status','created_at','no_seri','note','peruntukan','date_to')->get();

            $insert_checklist = db::table('mis_inventory_results')
            ->insert([
                'checklist_id' => strtoupper($checklist_id),
                'id_data' => $datas[0]->id,
                'no_po' => $datas[0]->no_po,
                'no_pr' => $datas[0]->no_pr,
                'category' => $datas[0]->category,
                'nama_item' => $datas[0]->nama_item,
                'qty' => $datas[0]->qty,
                'pic_pengambil_nik' => $pic_penerima[0],
                'pic_pengambil_name' => $pic_penerima[1],
                'pic_mis_nik' => $pic_mis1[0],
                'pic_mis_name' => $pic_mis1[1],
                'created_at' => $now,
                'updated_at' => $now,
                'receive_date' => date('Y-m-d', strtotime($request->get('dates'))),
                'date_to' => $datas[0]->date_to,
                'remark' => 'sudah diambil',
                'note' => $checklist_answer[$i]->note,
                'peruntukan' => $request->input('note_peruntukan'),
                'no_seri' => $checklist_answer[$i]->no_seri,
                'location' => $areas,
                'updated_at' => date('Y-m-d H:i:s')

            ]);

            $update_checklist = db::table('mis_inventory_details')
            ->where('id','=', $checklist_answer[$i]->id)
            ->update([
                'checklist_id' => strtoupper($checklist_id),
                'no_seri' => $checklist_answer[$i]->no_seri,
                'note' => $checklist_answer[$i]->note,
                'status' => 2,
                'remark' => 'sudah diambil',
                'updated_at' => date('Y-m-d H:i:s')

            ]);

            $update_news = db::table('mis_inventory_news')
            ->where('id','=', $datas[0]->id_data)
            ->update([
                'status' => 2,
                'remark' => 'sudah diambil',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        $response = array(
            'status' => true,
            'message' => 'Tanda Terima Inventory berhasil disimpan',
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

public function deleteItemMIS(Request $request)
{
    try {
        $request_id = $request->get('id');

        $datas = MisInventoryDetail::select('*')
        ->where('id', '=', $request_id)
        ->get();

        $update_new_datas = db::table('mis_inventory_news')
        ->where('id','=', $datas[0]->id_data)
        ->update([
            'status' => null,
            'remark' => null
        ]);

        $jobs = db::table('mis_inventory_details')->where('id', $request_id)->whereNull('deleted_at')->Delete();

        $response = array(
            'status' => true,
            'message' => 'Success Hapus List Item',
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


public function reportInventoryMIS($id){

    $data = MisInventoryResult::select('*')
    ->where('checklist_id', '=', $id)
    ->get();
    $data2 = MisInventoryResult::select('*')
    ->where('checklist_id', '=', $id)
    ->first();

    $pdf = \App::make('dompdf.wrapper');
    $pdf->getDomPDF()->set_option("enable_php", true);
    $pdf->setPaper('A4', 'landscape');

    $pdf->loadView('inventory_mis.inventory_report', array(
        'datas' => $data,
        'datas2' => $data2
    ));
    return $pdf->stream("Tanda Terima ".$data[0]->id. ".pdf");
}


public function indexHistoryInventory()
{

   return view('inventory_mis.index_history_inventory', array(
     'role_code' => Auth::user()->role_code
 ))->with('page', 'History Inventory');

}

public function fetchMisInventory(Request $request)
{

    if (strlen($request->get('check_in_from')) > 0) {
        $check_in_from = date('Y-m-d', strtotime($request->get('check_in_from')));
        $data1 = 'and receive_date >= "'.$check_in_from.'"';
    }else{  
      $data1 = "and receive_date >= '".date('Y-m-d')."'";
  }

  if (strlen($request->get('check_in_to')) > 0) {
    $check_in_to = date('Y-m-d', strtotime($request->get('check_in_to')));
    $data2 = 'and receive_date <= "'.$check_in_to.'"';
}else{
  $data2 = "and receive_date <= '".date('Y-m-d')."'";

}

$checklist = db::select('SELECT
    checklist_id,
    receive_date,
    pic_pengambil_nik,
    pic_pengambil_name,
    COUNT( checklist_id ) AS total_item 
    FROM
    `mis_inventory_results`
    WHERE deleted_at is null '.$data1.' '.$data2.'  
    GROUP BY
    checklist_id,
    pic_pengambil_nik,
    pic_pengambil_name,
    receive_date');

$response = array(
    'status' => true,
    'checklist' => $checklist
);
return Response::json($response);

}


public function fetchMISMonitoringInventory(Request $request)
{

    $first = date("Y-m-d", strtotime('-30 days'));

    $month_data = db::select("
        SELECT
        LEFT(MONTHNAME(created_at), 3) as bulan,
        year(created_at) as tahun,
        sum( CASE WHEN `category` = 'iPad' THEN 1 ELSE 0 END ) AS total_ipad,
        sum( CASE WHEN `category` = 'iPad' AND remark = 'sudah diambil' THEN 1 ELSE 0 END ) AS total_ipad_close,
        sum( CASE WHEN `category` = 'Computer' THEN 1 ELSE 0 END ) AS total_computer,
        sum( CASE WHEN `category` = 'Computer' AND remark = 'sudah diambil' THEN 1 ELSE 0 END ) AS total_computer_close,
        sum( CASE WHEN `category` = 'Mini PC' THEN 1 ELSE 0 END ) AS total_mini_pc,
        sum( CASE WHEN `category` = 'Mini PC' AND remark = 'sudah diambil' THEN 1 ELSE 0 END ) AS total_mini_pc_close,
        sum( CASE WHEN `category` = 'Laptop' THEN 1 ELSE 0 END ) AS total_laptop,
        sum( CASE WHEN `category` = 'Laptop' AND remark = 'sudah diambil' THEN 1 ELSE 0 END ) AS total_laptop_close,
        sum( CASE WHEN `category` = 'Lain-Lain' THEN 1 ELSE 0 END ) AS total_lain,
        sum( CASE WHEN `category` = 'Lain-Lain' AND remark = 'sudah diambil' THEN 1 ELSE 0 END ) AS total_lain_close 
        FROM
        mis_inventory_news
        GROUP BY 
        tahun,bulan
        order by tahun, month(created_at) ASC
        ");

    $data_mis = db::select("
        SELECT
        mis_inventory_news.id,
        mis_inventory_news.date_to,
        mis_inventory_news.no_po,
        mis_inventory_news.category,
        mis_inventory_news.nama_item,
        mis_inventory_news.qty,
        mis_inventory_news.status,
        mis_inventory_details.checklist_id,
        mis_inventory_details.date_receive,
        LEFT ( MONTHNAME( mis_inventory_news.created_at ), 3 ) AS bulans,
        YEAR ( mis_inventory_news.created_at ) AS tahuns 
        FROM
        mis_inventory_news
        LEFT JOIN mis_inventory_details ON mis_inventory_details.id_data =  mis_inventory_news.id
        WHERE
        mis_inventory_news.deleted_at IS NULL
        ORDER BY mis_inventory_details.created_at desc
        ");

    $response = array(
        'status' => true,
        'data_mis' => $data_mis,
        'month_data' => $month_data
        
    );
    return Response::json($response);
}

public function fetchDataInventoryEdit(Request $request)
{
  $checklist_id = $request->get('form_id');

  $data_mis = db::select('SELECT
    id,id_data,
    no_po,
    checklist_id,
    qty,
    no_seri,
    nama_item 
    FROM
    `mis_inventory_results`
    WHERE checklist_id = "'.$checklist_id.'"');

  $response = array(
    'status' => true,
    'datas' => $data_mis,
);
  return Response::json($response);
}

public function updteMisInventory(Request $request)
{
    try {
        // $asset_label = FixedAssetLabel::where('form_number', '=', $request->get('form_number'))->first();

        $datas = $request->get('asset_id');
        $checklist_id = $request->get('form_number');


        if (count($datas) > 0) {
            for ($i=0; $i < count($datas) ; $i++) { 
             $update_new_datas = db::table('mis_inventory_results')
             ->where('id','=', $datas[$i])
             ->update([
                'st_update' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
         }
     }

     $data = MisInventoryResult::select('*')
     ->where('checklist_id', '=', $checklist_id)
     ->where('st_update','=',null)
     ->get();


     if (count($data) > 0) {
        for ($j=0; $j < count($data); $j++) {

            $data2 = MisInventoryDetail::select('*')
            ->where('id', '=', $data[$j]->id_data)
            ->first(); 

            $update_new = db::table('mis_inventory_news')
            ->where('id','=', $data2->id_data)
            ->update([
                'remark' => null,
                'status' => null
            ]);

            $data_details = MisInventoryDetail::where('id', '=', $data[$j]->id_data)->forceDelete();
            $data_results = MisInventoryResult::where('id', '=', $data[$j]->id)->forceDelete();
        }
        
    }

    $update_result_data = db::table('mis_inventory_results')
    ->where('checklist_id','=', $checklist_id)
    ->update([
        'st_update' => null
    ]);

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

public function deleteItemHistoryMIS(Request $request)
{
    try {
        $request_id = $request->get('id');

        $datas = MisInventoryResult::select('*')
        ->where('checklist_id', '=', $request_id)
        ->get();

        for ($i=0; $i < count($datas); $i++) { 

            $data_detail = MisInventoryDetail::select('*')
            ->where('id', '=', $datas[$i]->id_data)
            ->first();

            if (count($data_detail) > 0) {
                $update_new_datas = db::table('mis_inventory_news')
                ->where('id','=', $data_detail->id_data)
                ->update([
                    'status' => null,
                    'remark' => null
                ]);
            $del_detail = MisInventoryDetail::where('id', $datas[$i]->id_data)->whereNull('deleted_at')->delete();
            $del_data = MisInventoryResult::where('id', $datas[$i]->id)->delete();
            }
        }

        $response = array(
            'status' => true,
            'message' => 'Success Hapus List Item',
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



}
