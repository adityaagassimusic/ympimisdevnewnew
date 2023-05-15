<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use DataTables;
use Response;
use File;
use PDF;
use Excel;
use DateTime;
use DateInterval;
use DatePeriod;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\ToolsMaster;
use App\ToolsKanban;
use App\ToolsUsage;
use App\ToolsOrder;
use App\ToolsBom;
use App\EmployeeSync;
use App\AccItem;
use App\AccExchangeRate;
use App\AccPurchaseRequisition;
use App\AccPurchaseRequisitionItem;
use App\AccBudget;
use App\AccBudgetHistory;

class ToolsController extends Controller
{
    public function __construct()
    {
        $this->dgm = 'PI0109004';
        $this->dgm_ps = 'PI9905001';
        $this->gm = 'PI1206001';
        $this->gm_acc = 'PI1712018';
    }

    public function index_tools(){
        return view('tools.index')->with('page', 'Tools');       
    }

    public function index_tools_operator(){
        return view('tools.index_operator')->with('page', 'Tools');       
    }

    //==================================//
    //          Index Tools 	        //
    //==================================//
    public function master_tools()
    {
        $title = 'Master Tools';
        $title_jp = 'ツールマスター';

        $tools = ToolsMaster::select('tools_masters.description')->whereNull('tools_masters.deleted_at')
        ->distinct()
        ->get();

        return view('tools.master_tools', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'tools' => $tools,
        ))->with('page', 'Master Tools')
        ->with('head', 'Master Tools');
    }

    public function fetch_tools(Request $request)
    {
        $tools = ToolsMaster::orderBy('tools_masters.id', 'asc');

        if ($request->get('tools') != null)
        {
            $tools = $tools->whereIn('tools_masters.description', $request->get('tools'));
        }

        $tools = $tools->select('*')
        ->get();

        return DataTables::of($tools)
        ->addColumn('action', function ($tools)
        {
            $id = $tools->id;
            if (Auth::user()->role_code == "S-MIS" || Auth::user()->role_code == "S-PROD") {
                return ' 
                <a href="../index/tools/update/' . $id . '" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> Edit</a> 
                <a href="../index/tools/delete/' . $id . '" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Delete</a>
                ';
            }else{
                return '-';       
            }
        })
        ->rawColumns(['action' => 'action'])

        ->make(true);
    }

    public function create_tools()
    {
        $title = 'Create Tools';
        $title_jp = '';

        $item = ToolsMaster::select('tools_masters.item_code','tools_masters.description')->distinct()->whereNull('tools_masters.deleted_at')
        ->get();

        $location = ToolsMaster::select('tools_masters.location')->distinct()->whereNull('tools_masters.deleted_at')
        ->get();

        $group = ToolsMaster::select('tools_masters.group')->distinct()->whereNull('tools_masters.deleted_at')
        ->get();

        $category = ToolsMaster::select('tools_masters.category')->distinct()->whereNull('tools_masters.deleted_at')
        ->get();

        $remark = ToolsMaster::select('tools_masters.remark')->distinct()->whereNull('tools_masters.deleted_at')
        ->get();

        return view('tools.create_tools', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'item' => $item,
            'location' => $location,
            'group' => $group,
            'category' => $category,            
            'remark' => $remark
        ))
        ->with('page', 'Tools');
    }


    public function update_tools($id)
    {
        $title = 'Edit Tools';
        $title_jp = '購入アイテムを編集';

        $item = ToolsMaster::find($id);

        // $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        // ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
        // ->first();

        // $item_categories = AccItemCategory::select('acc_item_categories.*')->whereNull('acc_item_categories.deleted_at')
        // ->get();

        return view('tools.edit_master_tools', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'item' => $item
        ))
        ->with('page', 'Tools Master');
    }



    //==================================//
    //          Tools BOM               //
    //==================================//
    public function tools_bom()
    {
        $title = 'Tools BOM';
        $title_jp = 'ツール使用量目標';

        $tools = ToolsBom::select('tools_boms.tools_description')->whereNull('tools_boms.deleted_at')
        ->distinct()
        ->get();

        return view('tools.tools_boms', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'tools' => $tools,
        ))->with('page', 'Tools BOM')
        ->with('head', 'Tools BOM');
    }

    public function fetch_tools_bom(Request $request)
    {
        $tools_bom = ToolsBom::orderBy('tools_boms.id', 'asc');

        if ($request->get('tools') != null)
        {
            $tools_bom = $tools_bom->whereIn('tools_boms.tools_description', $request->get('tools'));
        }

        $tools_bom = $tools_bom->select('*')
        ->get();

        return DataTables::of($tools_bom)
        ->addColumn('action', function ($tools_bom)
        {
            $id = $tools_bom->id;
            if (Auth::user()->role_code == "S-MIS" || Auth::user()->role_code == "S-PROD") {
                return ' 
                <a href="javascript:void(0)" class="btn btn-xs btn-warning" onClick="edit_bom(' . $id . ')" data-toggle="tooltip" title="Edit BOM Tools"><i class="fa fa-edit"></i> Edit</a>
                ';

                // <a href="tools/update/' . $id . '" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> Edit</a> 
                // <a href="tools/delete/' . $id . '" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Delete</a>
                // return '-';
            }else{
                return '-';       
            }
        })
        ->rawColumns(['action' => 'action'])

        ->make(true);
    }


    public function edit_tools_bom(Request $request)
    {
        $bom = ToolsBom::find($request->get('id'));
     
        $response = array(
            'status' => true,
            'bom' => $bom
        );
        return Response::json($response);
    }


    
    public function indexToolsAudit(){
        $employees = EmployeeSync::whereNull('end_date')->whereNotNull('department')->get();
        $location = ToolsMaster::select('location')->distinct()->get();
        return view('tools.tools_audit', array(
            'title' => 'Audit Tools',
            'title_jp' => '',
            'employees' => $employees,
            'location' => $location,
        ))->with('page', 'Audit Tools')->with('head', 'Audit Tools');
    }

    public function fetchToolsAudit(Request $request){

        $lists = ToolsMaster::select('*')
        ->whereNull('tools_masters.deleted_at')
        ->where('location',$request->get('loc'))
        ->orderBy('tools_masters.id', 'ASC')
        ->get();

        // $audits = db::connection('ympimis_2')
        // ->table('case_audits')
        // ->whereNull('case_audits.deleted_at')
        // ->where('tanggal',date('Y-m-d'))
        // ->select('case_audits.qty_audit','case_audits.material_number')
        // ->get();


        $response = array(
            'status' => true,
            'lists' => $lists,
            // 'audits' => $audits,
            'message' => 'Data Berhasil Diambil'
        );
        return Response::json($response);
    }


    public function tools_stock_out(){
        $title = 'Pemakaian Tools';
        $title_jp = 'ツール使用量';

        $tools = ToolsMaster::select('tools_masters.rack_code','tools_masters.item_code','tools_masters.description')->whereNull('tools_masters.deleted_at')
        ->distinct()
        ->get();

        return view('tools.tools_stock_out', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'tools' => $tools
        ))->with('page', 'Tools Process');
    }

    public function fetch_tools_data(Request $request){
        try{

            $tools = ToolsMaster::select('tools_masters.*')
            ->where('tools_masters.rack_code','=',$request->get('rack'))
            ->where('tools_masters.item_code','=',$request->get('item_code'))
            ->whereNull('tools_masters.deleted_at')
            ->first();

            if (count($tools) > 0) {
                $response = array(
                    'status' => true,
                    'message' => 'Base Data Ditemukan',
                    'tools' => $tools
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

    public function fetch_tools_order(Request $request){
        $order_data = ToolsUsage::leftJoin('tools_masters', 'tools_usages.item_code', '=', 'tools_masters.item_code');

        if(strlen($request->get('tools')) > 0){
            $order_data = $order_data->where("tools_usages.item_code", "=" ,$request->get('item_code'));
        }

        $order_data = $order_data->select('tools_usages.*')
        ->where(db::raw('date(tanggal)'),'=',date('Y-m-d'))
        ->orderBy('tools_usages.id','DESC')
        ->get();

        $response = array(
            'status' => true,
            'order_data' => $order_data
        );
        return Response::json($response);
    }

    public function scan_operator(Request $request){

        $nik = $request->get('employee_id');

        if(strlen($nik) > 9){
            $nik = substr($nik,0,9);
        }

        $employee = db::table('employees')
        ->where('tag', 'like', '%'.$nik.'%')
        ->orWhere('employee_id', '=', $nik)
        ->first();

        if(count($employee) > 0 ){
            $response = array(
                'status' => true,
                'message' => 'Logged In',
                'employee' => $employee
            );
            return Response::json($response);
        }
        else{
            $response = array(
                'status' => false,
                'message' => 'Employee ID Invalid'
            );
            return Response::json($response);
        }
    } 

    public function post_tools(Request $request)
    {
        try{


            $balance = $request->get('balance_kanban');
            $qty = $request->get('qty');
            $sisa_balance = $balance - $qty;

            $stock = new ToolsUsage([
                'tanggal' => date('Y-m-d H:i:s'),
                'employee_id' => $request->get('employee_id'),
                'employee_name' => $request->get('employee_name'),
                'item_code' => $request->get('item_code'),
                'description' => $request->get('description'),
                'rack_code' => $request->get('rack_code'),
                'kategori' => $request->get('kategori'),
                'lifetime' => $request->get('lifetime'),
                'location' => $request->get('location'),
                'group' => $request->get('group'),
                'lot_kanban' => $request->get('lot_kanban'),
                'stock_kanban' => $request->get('stock_kanban'),
                'balance_kanban' => $request->get('balance_kanban'),
                'no_kanban' => $request->get('no_kanban'),
                'qty' => $qty,
                'note' => null,
                'created_by' => Auth::user()->username
            ]);

            $stock->save();


            if ($sisa_balance == 0) {

                $tool = ToolsMaster::where('description','=',$request->get('description'))->first();

                $updateStock = ToolsMaster::where('description','=',$request->get('description'))
                ->update([
                    'balance_kanban' => $tool->lot_kanban,
                    'stock_kanban' => $tool->stock_kanban - 1
                ]); 

                $tool2 = ToolsMaster::where('description','=',$request->get('description'))->first();

                if($tool2->stock_kanban < $tool2->need_kanban){

                    $stock = new ToolsOrder([
                        'tanggal' => date('Y-m-d H:i:s'),
                        'item_code' => $request->get('item_code'),
                        'description' => $request->get('description'),
                        'rack_code' => $request->get('rack_code'),
                        'kategori' => $request->get('kategori'),
                        'location' => $request->get('location'),
                        'group' => $request->get('group'),
                        'qty' => $tool2->moq,
                        'no_kanban' => $request->get('no_kanban'),
                        'status' => 'waiting_order',
                        'created_by' => Auth::user()->username
                    ]);

                    $stock->save();


                    $updateOrder = ToolsMaster::where('description','=',$request->get('description'))
                    ->update([
                        'quantity_order' => $tool2->moq,
                        'no_kanban' => $stock->no_kanban,
                        'status' => 'waiting_order'
                    ]); 

                    Mail::to('rio.irvansyah@music.yamaha.com')->send(new SendEmail($tool2, 'tools_order'));
                }

            }
            else{
                $updateBalance = ToolsMaster::where('description','=',$request->get('description'))
                ->update([
                    'balance_kanban' => $sisa_balance
                ]);    
            }

            $response = array(
                'status' => true,
                'message' => 'Tools Berhasil Di Di Order',
                'stock' => $stock
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

    //==================================//
    //         Perhitungan Tools        //
    //==================================//
    
    public function tools_calculation()
    {
        $title = 'Tools Calculation';
        $title_jp = 'ツール計算';

        $resume = ToolsMaster::select('tools_masters.description')->whereNull('tools_masters.deleted_at')
        ->distinct()
        ->get();

        $location = ToolsMaster::select('tools_masters.location')->whereNull('tools_masters.deleted_at')
        ->distinct()
        ->get();

        return view('tools.tools_calculation', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'resume' => $resume,
            'location' => $location
        ))->with('page', 'Tools Calculation')
        ->with('head', 'Tools Calculation');
    }

    public function fetch_tools_calculation(Request $request)
    {
        // $resume = db::select("
        // SELECT
        //     tools_masters.*,
        //     (SELECT SUM(tools_targets.qty_target) from tools_targets where tools_targets.tool = tools_masters.description) as qty_target,
        //     (SELECT DISTINCT due_date from tools_targets where tools_targets.tool = tools_masters.description)  as due_date
        // FROM
        //     tools_masters
        // ");


        $resume = db::select("
         SELECT
            tools_masters.*,
                        COALESCE(`status`,'z') as status_new
        FROM
            tools_masters
            WHERE
        rack_code is not null
        ORDER BY `status_new` asc
        ");

        $start = date("Y-m-01");
        $end = date("Y-m-t");

        $tools = db::select("
        SELECT DISTINCT
            tools_boms.tools_item,
            tools_boms.tools_description
        FROM
            ( SELECT DISTINCT material_number FROM production_forecasts WHERE forecast_month BETWEEN '".$start."' 
            AND '".$end."') AS material
            LEFT JOIN tools_boms ON tools_boms.gmc_parent = material.material_number 
        WHERE
            tools_item IS NOT NULL");

        $forecast = db::select("
        SELECT
            tools_item,
            tools_description,
            DATE_FORMAT(forecast_month,'%Y-%m') as `month`,
            SUM(quantity) AS qty_target,
            `usage`,
            SUM(quantity)/`usage` AS total_need 
        FROM
            tools_boms
            JOIN production_forecasts ON production_forecasts.material_number = tools_boms.gmc_parent 
        WHERE
            forecast_month BETWEEN '".$start."'  AND '".$end."'
            GROUP BY
            tools_item, tools_description,forecast_month,`usage`
        ORDER BY
            month ASC");

        $response = array(
          'status' => true,
          'resume' => $resume,
          'tools' => $tools,
          'forecast' => $forecast
      );
        return Response::json($response);
    }

    public function tools_calculation_temp()
    {
        $title = 'Tools Calculation';
        $title_jp = 'ツール計算';

        $resume = ToolsMaster::select('tools_masters.description')->whereNull('tools_masters.deleted_at')
        ->distinct()
        ->get();

        $location = ToolsMaster::select('tools_masters.location')->whereNull('tools_masters.deleted_at')
        ->distinct()
        ->get();

        return view('tools.tools_calculation_new', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'resume' => $resume,
            'location' => $location
        ))->with('page', 'Tools Calculation')
        ->with('head', 'Tools Calculation');
    }

    public function fetch_tools_calculation_temp(Request $request)
    {
        $resume = db::select("
         SELECT
            tools_masters.*,
            COALESCE(`status`,'z') as status_new
        FROM
            tools_masters
            WHERE
            rack_code is not null
        ORDER BY `status_new` asc
        ");


        $response = array(
          'status' => true,
          'resume' => $resume
      );
        return Response::json($response);
    }

    //  public function fetch_tools_calculation(Request $request)
    // {

    //    $resume = db::select("
    //     SELECT
    //         tools_masters.*,
    //         (SELECT SUM(tools_targets.qty_target) from tools_targets where tools_targets.tool = tools_masters.description) as qty_target,
    //         (SELECT DISTINCT due_date from tools_targets where tools_targets.tool = tools_masters.description)  as due_date
    //     FROM
    //         tools_masters
    //     ");

    //     return DataTables::of($resume)


    //     ->editColumn('due_date', function ($resume)
    //     {
    //         return date('M Y', strtotime($resume->due_date));
    //     })

    //     ->addColumn('life_target', function ($resume)
    //     {
    //         $lifetime = $resume->lifetime;
    //         $target = $resume->qty_target;
        
    //         $hasil = $target/$lifetime;
    //         return number_format($hasil,2,".",",") ." tools";       
    //     })

    //     ->addColumn('daily_need', function ($resume)
    //     {
    //         $lifetime = $resume->lifetime;
    //         $target = $resume->qty_target;
        
    //         $hasil = $target/$lifetime;

    //         return number_format($hasil/22,2,".",",") ." tools / hari";       
    //     })


    //     ->rawColumns(['due_date' => 'due_date'])

    //     ->make(true);
    // }

    public function tools_log(){
        $title = 'Tools Usage Log';
        $title_jp = 'ツールローグ';

        $location = db::select("SELECT DISTINCT location FROM tools_masters");

        return view('tools.tools_log', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'location' => $location
        ))->with('page', 'Tools Log');
    }

    
    public function fetch_tools_log(){
        $tools = db::select("SELECT * FROM tools_usages order by id desc");

        $response = array(
            'status' => true,
            'tools' => $tools
        );
        return Response::json($response);
    }

    public function fetch_tools_log_monitoring(Request $request)
    {

        $data = db::select("
            SELECT
            count( id ) AS jumlah,
            monthname(tanggal) as bulan
            FROM
            tools_usages
            GROUP BY
            monthname(tanggal)
        ");

        $response = array(
            'status' => true,
            'datas' => $data,
        );

        return Response::json($response); 
    }

    public function indexKanbanTools(){
        $title = 'Kanban Tools Master Data';
        $title_jp = 'かんばんツールデータ';

        $locations = ToolsMaster::select('location')
        ->distinct()
        ->get();

        $groups = ToolsMaster::select('group')
        ->distinct()
        ->get();

        $categories = ToolsMaster::select('category')
        ->distinct()
        ->get();

    return view('tools.kanban_tools', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'locations' => $locations,
            'groups' => $groups,
            'categories' => $categories,
        ))->with('page', 'Kanban Tools')->with('head', 'Stocktaking');
    }

    public function fetchKanbanTools(Request $request){

        $location = '';
        if($request->get('location') != null){
            $locations =  $request->get('location');
            for ($i=0; $i < count($locations); $i++) {
                $location = $location."'".$locations[$i]."'";
                if($i != (count($locations)-1)){
                    $location = $location.',';
                }
            }
            $location = "location IN (".$location.") ";
        }

        $group = '';
        if($request->get('group') != null){
            $groups =  $request->get('group');
            for ($i=0; $i < count($groups); $i++) {
                $group = $group."'".$groups[$i]."'";
                if($i != (count($groups)-1)){
                    $group = $group.',';
                }
            }
            $group = "`group` IN (".$group.") ";
        }

        $category = '';
        if($request->get('category') != null){
            $categories =  $request->get('category');
            for ($i=0; $i < count($categories); $i++) {
                $category = $category."'".$categories[$i]."'";
                if($i != (count($categories)-1)){
                    $category = $category.',';
                }
            }
            $category = "category IN (".$category.") ";
        }

        $condition = '';
        $and = false;
        if($location != '' || $group != '' || $category != ''){
            $condition = 'WHERE';
        }

        if($location != ''){
            $and = true;
            $condition = $condition. ' ' .$location;
        }

        if($group != ''){
            if($and){
                $condition =  $condition.' OR ';
            }
            $condition = $condition. ' ' .$group;
        }

        if($category != ''){
            if($and){
                $condition =  $condition.' OR ';
            }
            $condition = $condition. ' ' .$category;
        }


        $data = db::select("SELECT * FROM tools_kanbans
            ".$condition." 
            ORDER BY id ASC");

        $response = array(
            'status' => true,
            'data' => $data
        );
        return Response::json($response);
    }

    public function printKanbanTools($id){
        $ids = explode(",",$id);


        $whereID = '';
        $list_id = array();
        $tools_array = [];
        $tools_array2 = [];
        for ($i=0; $i < count($ids); $i++) {
            // array_push($list_id, $ids[$i]); 
            // $whereID = $whereID."'".$ids[$i]."'";
            // if($i != (count($ids)-1)){
            //     $whereID = $whereID.',';
            // }
            $tools = db::select("SELECT
            * from tools_kanbans
            WHERE id = '".$ids[$i]."'
            ORDER BY id aSC");


            array_push($tools_array, $tools);
            if ($i % 3 == 2 || $i == (count($ids)-1)) {
                array_push($tools_array2, $tools_array);
                $tools_array = [];
            }


            $update = ToolsKanban::where('id', $ids[$i])->update(['print_status' => 1]);
        }
        DB::connection()->enableQueryLog();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
       
        $pdf->loadView('tools.print_kanban_tools', array(
            'tools_array2' => $tools_array2
        ));

        // return view('tools.print_kanban_tools', array(
        //  'tools_array2' => $tools_array2,
        // ));

        return $pdf->stream($tools[0]->rack_code."_".$tools[0]->item_code.".pdf");
    }

    //==================================//
    //         Need Order Tools         //
    //==================================//
    
    public function tools_need_order()
    {
        $title = 'Tools Need Order';
        $title_jp = '';

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
        ->first();

        return view('tools.tools_need_order', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $emp
        ))->with('page', 'Tools Need Order')
        ->with('head', 'Tools Need Order');
    }

    public function fetch_tools_need_order(Request $request)
    {
        $resume = db::select("
        SELECT
            tools_orders.*
        FROM
            tools_orders
        WHERE
            `status` != 'received'
        ");

        $response = array(
          'status' => true,
          'resume' => $resume,
      );
        return Response::json($response);
    }

     public function fetchItemList(Request $request)
    {
        $items = AccItem::select('acc_items.kode_item', 'acc_items.deskripsi')
        ->join('tools_orders','tools_orders.item_code','=','acc_items.kode_item')
        ->where('status','waiting_order')
        ->get();

        $response = array(
            'status' => true,
            'item' => $items
        );

        return Response::json($response);
    }

    public function toolsDetailItem(Request $request)
    {
        $html = array();
        $kode_item = AccItem::where('kode_item', $request->kode_item)
        ->join('tools_orders','tools_orders.item_code','=','acc_items.kode_item')
        ->where('status','waiting_order')
        ->get();
        foreach ($kode_item as $item)
        {
            $html = array(
                'deskripsi' => $item->deskripsi,
                'spesifikasi' => $item->spesifikasi,
                'uom' => $item->uom,
                'price' => $item->harga,
                'currency' => $item->currency,
                'moq' => $item->moq,
                'qty' => $item->qty,
                'peruntukan' => $item->peruntukan,
                'kebutuhan' => $item->kebutuhan,
            );

        }

        return json_encode($html);
    }

    public function create_purchase_requisition(Request $request)
    {
        $id = Auth::id();

        $lop = $request->get('lop');

        try
        {
            $staff = null;
            $manager = null;
            $manager_name = null;
            $posisi = null;
            $dgm = null;
            $gm = null;

            //jika PE maka Pak Alok

            if($request->get('department') == "Production Engineering Department")
            {
                $manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Maintenance Department' and position = 'manager'");
            }

            //Jika Pch / Proc maka pak imron
            else if($request->get('department') == "Purchasing Control Department")
            {
                $manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Procurement Department' and position = 'manager'");
            }

            //Jika GA pak arief
            else if($request->get('department') == "General Affairs Department")
            {
                $manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Human Resources Department' and position = 'manager'");
            }

            //Jika Logistic BU MEI
            // else if($request->get('department') == "Logistic Department")
            // {
            //     $manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and employee_id = 'PI9905001'");
            // }

            //Jika KP maka EI
            else if($request->get('department') == "Woodwind Instrument - Key Parts Process (WI-KPP) Department")
            {
                $manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Educational Instrument (EI) Department' and position = 'manager'");
            }

            // //SEMENTARA EDIN
            // else if($request->get('department') == "Educational Instrument (EI) Department")
            // {
            //     $manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Educational Instrument (EI) Department' and position = 'chief'");
            // }

            //Jika BP maka WP
            else if($request->get('department') == "Woodwind Instrument - Welding Process (WI-WP) Department")
            {
                $manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = 'Woodwind Instrument - Body Parts Process (WI-BPP) Department' and position = 'manager'");
            }

            else
            {
                // Get Manager
                $manag = db::select("SELECT employee_id, name, position, section FROM employee_syncs where end_date is null and department = '" . $request->get('department') . "' and position = 'manager'");
            }

            // Jika ada staff
            if ($request->get('staff') != "") {

                $posisi = "staff";
                $staff = $request->get('staff');

                foreach ($manag as $mg)
                {
                    $manager = $mg->employee_id;
                    $manager_name = $mg->name;
                }
            }

            //cek manager ada atau tidak

            else if ($manag != null)
            {
                // $posisi = "manager";
                $posisi = "user";

                foreach ($manag as $mg)
                {
                    $manager = $mg->employee_id;
                    $manager_name = $mg->name;
                }
            }

            else
            {
                // $posisi = "dgm";
                $posisi = "user";
            }

            $submission_date = $request->get('submission_date');
            $po_date = date('Y-m-d', strtotime($submission_date . ' + 7 days'));

            if($request->get('department') == "Human Resources Department" || $request->get('department') == "General Affairs Department"){
                $dgm = null;

                // GM Pak Arief
                $getgm = EmployeeSync::select('employee_id', 'name', 'position')
                ->where('employee_id','=','PI9709001')
                ->first();

                $gm = $getgm->employee_id;

                // $gm = null;
            }
            //if Production Support Division Maka GM Pak Budhi
            else if($request->get('department') == "Logistic Department" || $request->get('department') == "Production Control Department" || $request->get('department') == "Purchasing Control Department" || $request->get('department') == "Procurement Department" ){
                $dgm = $this->dgm_ps;
                $gm = $this->dgm;
            }
            //if accounting maka GM Pak IDA
            else if($request->get('department') == "Accounting Department"){
                $dgm = null;
                $gm = $this->gm_acc;

            }
            //Selain Itu GM Pak Hayakawa
            else{
                $dgm = $this->dgm;
                $gm = $this->gm;
            }


            $data = new AccPurchaseRequisition([
                'no_pr' => $request->get('no_pr') , 
                'emp_id' => $request->get('emp_id') , 
                'emp_name' => $request->get('emp_name') , 
                'department' => $request->get('department') , 
                'section' => $request->get('section') , 
                'submission_date' => $submission_date, 
                'po_due_date' => $po_date, 
                'note' => $request->get('note') , 
                'file_pdf' => 'PR'.$request->get('no_pr').'.pdf', 
                'posisi' => $posisi, 
                'status' => 'approval', 
                'no_budget' => $request->get('budget_no'), 
                'staff' => $staff,
                'manager' => $manager,
                'manager_name' => $manager_name,
                'dgm' => $dgm, 
                'gm' => $gm, 
                'created_by' => $id
            ]);

            $data->save();

            // $mod_date = date('Y-m-d', strtotime($request->get('submission_date') . ' + 21 days'));

            for ($i = 1;$i <= $lop;$i++)
            {
                $item_code = "item_code" . $i;
                $item_desc = "item_desc" . $i;
                $item_spec = "item_spec" . $i;
                $item_stock = "item_stock" . $i;
                $item_request_date = "req_date" . $i;
                $item_currency = "item_currency" . $i;
                $item_currency_text = "item_currency_text" . $i;
                $item_price = "item_price" . $i;
                $item_qty = "qty" . $i;
                $item_uom = "uom" . $i;
                $item_amount = "amount" . $i;
                $peruntukan = "tujuan_peruntukan" . $i;
                $kebutuhan = "tujuan_kebutuhan" . $i;
                // $item_budget = "budget".$i;
                $status = "";
                //Jika ada value kosong
                if ($request->get($item_code) == "kosong")
                {
                    $request->get($item_code) == "";
                }
                if ($request->get($item_currency) != "")
                {
                    $current = $request->get($item_currency);
                }
                else if ($request->get($item_currency_text) != "")
                {
                    $current = $request->get($item_currency_text);
                }

                $data2 = new AccPurchaseRequisitionItem([
                    'no_pr' => $request->get('no_pr') , 
                    'item_code' => $request->get($item_code) ,
                    'item_desc' => $request->get($item_desc) ,
                    'item_spec' => $request->get($item_spec) ,
                    'item_stock' => $request->get($item_stock) , 
                    'item_request_date' => $request->get($item_request_date), 
                    'item_currency' => $current, 
                    'item_price' => $request->get($item_price), 
                    'item_qty' => $request->get($item_qty),
                    'item_uom' => $request->get($item_uom),
                    'item_amount' => $request->get($item_amount),
                    'peruntukan' => $request->get($peruntukan),
                    'kebutuhan' => $request->get($kebutuhan),
                    'status' => 'Tools',
                    'created_by' => $id
                ]);

                $data2->save();

                $dollar = "konversi_dollar" . $i;

                $getbulan = AccBudget::select('budget_no', 'periode')
                ->where('budget_no', $request->get('budget_no'))
                ->first();

                if ($getbulan->periode == "FY198") {
                    $month = strtolower(date('M'));
                }
                else{
                    $month = "apr";
                }

                // $month = strtolower(date("M",strtotime($request->get('submission_date'))));

                $data3 = new AccBudgetHistory([
                    'budget' => $request->get('budget_no'),
                    'budget_month' => $month,
                    'budget_date' => date('Y-m-d'),
                    'category_number' => $request->get('no_pr'),
                    'no_item' => $request->get($item_desc),
                    'beg_bal' => $request->get('budget'),
                    'amount' => $request->get($dollar),
                    'status' => 'PR',
                    'created_by' => $id
                ]);

                $data3->save();


                $updatekebutuhan = AccItem::where('kode_item','=',$request->get($item_code))->update([
                    'peruntukan' => $request->get($peruntukan),
                    'kebutuhan' => $request->get($kebutuhan)
                ]);

                $updateTools = ToolsOrder::where('item_code','=',$request->get($item_code))
                ->where('status','waiting_order')->update([
                    'status' => 'pr_approval',
                    'no_pr' => $request->get('no_pr')
                ]);

            }

            $totalPembelian = $request->get('TotalPembelian');
            if ($totalPembelian != null) {
                // $datePembelian = date('Y-m-d');
                // $fy = db::select("select fiscal_year from weekly_calendars where week_date = '$datePembelian'");
                // foreach ($fy as $fys) {
                //     $fiscal = $fys->fiscal_year;
                // }
                // $bulan = strtolower(date("M",strtotime($datePembelian))); //aug,sep,oct

                $getbulan = AccBudget::select('budget_no', 'periode')
                ->where('budget_no', $request->get('budget_no'))
                ->first();

                if ($getbulan->periode == "FY198") {
                    $bulan = strtolower(date('M'));
                    $fiscal = "FY198";
                }
                else{
                    $bulan = "apr";
                    $fiscal = "FY199";
                }

                $sisa_bulan = $bulan.'_sisa_budget';                    
                //get Data Budget Based On Periode Dan Nomor
                $budget = AccBudget::where('budget_no','=',$request->get('budget_no'))->first();
                //perhitungan 
                $total = $budget->$sisa_bulan - $totalPembelian;

                if ($total < 0 ) {
                    return false;
                }

                $dataupdate = AccBudget::where('budget_no',$request->get('budget_no'))->update([
                    $sisa_bulan => $total
                ]);
            }


            $detail_pr = AccPurchaseRequisition::select('acc_purchase_requisitions.*','acc_purchase_requisition_items.*','acc_budget_histories.beg_bal','acc_budget_histories.amount',DB::raw("(select DATE(created_at) from acc_purchase_order_details where acc_purchase_order_details.no_item = acc_purchase_requisition_items.item_code ORDER BY created_at desc limit 1) as last_order"))
            ->leftJoin('acc_purchase_requisition_items', 'acc_purchase_requisitions.no_pr', '=', 'acc_purchase_requisition_items.no_pr')
            ->join('acc_budget_histories', function($join) {
             $join->on('acc_budget_histories.category_number', '=', 'acc_purchase_requisition_items.no_pr');
             $join->on('acc_budget_histories.no_item','=', 'acc_purchase_requisition_items.item_desc');
         })
            ->where('acc_purchase_requisitions.id', '=', $data->id)
            ->distinct()
            ->get();

            $exchange_rate = AccExchangeRate::select('*')
            ->where('periode','=',date('Y-m-01', strtotime($detail_pr[0]->submission_date)))
            ->where('currency','!=','USD')
            ->orderBy('currency','ASC')
            ->get();

            //SELECT * FROM `acc_purchase_requisitions` left join acc_purchase_requisition_items on acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr join acc_budget_histories on acc_purchase_requisition_items.no_pr = acc_budget_histories.category_number and acc_purchase_requisition_items.item_desc = acc_budget_histories.no_item where acc_purchase_requisitions.id = "45" 

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('A4', 'landscape');

            $pdf->loadView('accounting_purchasing.report.report_pr', array(
                'pr' => $detail_pr,
                'rate' => $exchange_rate
            ));

            $pdf->save(public_path() . "/pr_list/PR".$detail_pr[0]->no_pr.".pdf");

            // $mails = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.staff = users.username where acc_purchase_requisitions.id = " . $data->id;
            // $mailtoo = DB::select($mails);

            // // Jika gaada staff
            // if ($mailtoo == null)
            // {   
            //     //ke manager
            //     $mails = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.manager = users.username where acc_purchase_requisitions.id = " . $data->id;
            //     $mailtoo = DB::select($mails);

            //     // Jika Gaada Manager
            //     if ($mailtoo == null)
            //     { 
            //         // ke DGM
            //         $mails = "select distinct email from acc_purchase_requisitions join users on acc_purchase_requisitions.dgm = users.username where acc_purchase_requisitions.id = " . $data->id;
            //         $mailtoo = DB::select($mails);
            //     }

            // }

            // $isimail = "select acc_purchase_requisitions.*,acc_purchase_requisition_items.item_stock, acc_purchase_requisition_items.item_desc, acc_purchase_requisition_items.kebutuhan, acc_purchase_requisition_items.peruntukan, acc_purchase_requisition_items.item_qty, acc_purchase_requisition_items.item_uom FROM acc_purchase_requisitions join acc_purchase_requisition_items on acc_purchase_requisitions.no_pr = acc_purchase_requisition_items.no_pr where acc_purchase_requisitions.id= " . $data->id;
            // $purchaserequisition = db::select($isimail);

            // Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($purchaserequisition, 'purchase_requisition'));

            return redirect('/purchase_requisition')->with('status', 'PR Berhasil Dibuat')
            ->with('page', 'Purchase Requisition');
        }
        catch(QueryException $e)
        {
            return redirect('/purchase_requisition')->with('error', $e->getMessage())
            ->with('page', 'Purchase Requisition');
        }
    }

    //Monitoring Tools

    public function indexToolsMonitoring()
    {
        $title = 'Tools Monitoring';
        $title_jp = 'ツールモニタリング';

        $location = db::select("SELECT DISTINCT location FROM tools_masters");

        return view('tools.tools_monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'location' => $location
        ))->with('page', 'Tools Monitoring');   
    }

    public function fetchToolsMOnitoring(Request $request)
    {
        try {
        

            $tools_resume = DB::select("
                SELECT COALESCE
                ( location, NULL ) AS location,
                sum( CASE WHEN tools_orders.`status` = 'waiting_order' THEN 1 ELSE 0 END ) AS `waiting_order`,
                sum( CASE WHEN tools_orders.`status` = 'pr_approval' THEN 1 ELSE 0 END ) AS `pr_approval`,
                sum( CASE WHEN tools_orders.`status` = 'po_confirmed' THEN 1 ELSE 0 END ) AS `po_confirmed`,
                sum( CASE WHEN tools_orders.`status` = 'received' THEN 1 ELSE 0 END ) AS `received`    
            FROM
                `tools_orders` 
            GROUP BY
                location");

            // $tools_detail = DB::select("SELECT
            //     *
            //     FROM
            //     `tools_orders`
            // ");

            $tools_orders = DB::select("SELECT * FROM `tools_orders` where `status` != 'received'");
            
            $response = array(
                'status' => true,
                'tools_resume' => $tools_resume,
                // 'tools_detail' => $tools_detail,
                'tools_order' => $tools_orders
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

    public function edit_pr(Request $request)
    {
        try{
            $tools = ToolsMaster::find($request->get("id"));
            $tools->no_pr = $request->get('no_pr');
            $tools->status = 'pr_approval';
            $tools->save();

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
              'datas' => "Tools Already Exist",
          );
             return Response::json($response);
         }
         else{
             $response = array(
              'status' => false,
              'datas' => "Update Tools Error.",
          );
             return Response::json($response);
         }
     }
 }

    public function indexRequest() {
        $title = 'Tools Request';
        $title_jp = '';

        return view('tools.tools_request', array(
            'title' => $title,
            'title_jp' => $title_jp
        ))->with('page', $title)->with('Head', $title); 
    }

    public function fetchRequest(Request $request){     
        
        $start = $request->get('start');
        $end = $request->get('end');

        if(strlen($start) == 0){
            $start = date('Y-m') . '-01';   
        }else{
            $start = $start . '-01';    
        }

        if(strlen($end) == 0) {
            $end = date('Y-m', strtotime(date('Y-m').'-01 +1 year')) . '-01';
        }else{
            $end = $end. '-01';
        }

        $start = new DateTime($start);
        $end = new DateTime($end);
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);

        $interval_month = [];
        foreach ($period as $dt) {
            $row = array();
            $row['month'] = $dt->format("Y-m");
            $row['text_month'] = $dt->format("F");
            $row['text_year'] = $dt->format("Y");

            array_push($interval_month, $row);
        }

        $tools = db::select("
        SELECT DISTINCT
            tools_boms.tools_item,
            tools_boms.tools_description
        FROM
            ( SELECT DISTINCT material_number FROM production_forecasts WHERE forecast_month BETWEEN '".$start->format("Y-m-d")."' 
            AND '".$end->format("Y-m-d")."') AS material
            LEFT JOIN tools_boms ON tools_boms.gmc_parent = material.material_number 
        WHERE
            tools_item IS NOT NULL");

        // $forecast = db::select("
        // SELECT
        //     tools_item,
        //     tools_description,
        //     DATE_FORMAT(forecast_month,'%Y-%m') as `month`,
        //     quantity AS qty_target,
        //     COUNT( tools_boms.id ) AS qty_material,
        //     `usage`,
        //     COUNT( tools_boms.id ) * quantity * `usage` AS total_need 
        // FROM
        //     tools_boms
        //     JOIN production_forecasts ON production_forecasts.material_number = tools_boms.gmc_parent 
        // WHERE
        //     forecast_month BETWEEN '".$start->format("Y-m-d")."' 
        //     AND '".$end->format("Y-m-d")."' 
        // GROUP BY
        //     tools_item, tools_description, `usage`, forecast_month, quantity
        // ORDER BY
        //     month ASC");


        $forecast = db::select("
        SELECT
            tools_item,
            tools_description,
            DATE_FORMAT(forecast_month,'%Y-%m') as `month`,
            SUM(quantity) AS qty_target,
            `usage`,
            SUM(quantity)/`usage` AS total_need,
            acc_items.harga,
            acc_items.currency
        FROM
            tools_boms
            JOIN production_forecasts ON production_forecasts.material_number = tools_boms.gmc_parent
            JOIN acc_items ON acc_items.kode_item = tools_boms.tools_item  
        WHERE
            forecast_month BETWEEN '".$start->format("Y-m-d")."'  AND '".$end->format("Y-m-d")."'
            GROUP BY
            tools_item, tools_description,forecast_month,`usage`,harga,currency
        ORDER BY
            month ASC");

        $response = array(
            'status' => true,
            'interval' => $interval_month,
            'tools' => $tools,
            'forecast' => $forecast
        );
        return Response::json($response);
    }

    public function indexToolsBomProgress()
    {
        $title = 'Tools BOM Progress Monitoring';
        $title_jp = '';

        $location = db::select("SELECT DISTINCT location FROM tools_masters");

        return view('tools.tools_bom_monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'location' => $location
        ))->with('page', 'Tools BOM Monitoring');   
    }

    public function fetchToolsBomProgress(Request $request)
    {
        try {
            $tools_resume = DB::select("
            SELECT
                a.location,
                SUM(a.jumlah) as jumlah,
                SUM(a.jumlah_sudah) as jumlah_sudah,
                SUM(a.jumlah) - SUM(a.jumlah_sudah) as jumlah_belum
            FROM
                (
            SELECT COALESCE
                ( location, NULL ) AS location,
                COUNT( id ) AS jumlah,
                '0' AS jumlah_sudah 
            FROM
                `tools_masters`
            GROUP BY
                location 
                
                UNION ALL

            SELECT
                tools_masters.location,
                0 AS jumlah,
                COUNT( DISTINCT tools_item ) AS jumlah_sudah 
            FROM
                tools_boms
                JOIN tools_masters ON tools_masters.item_code = tools_boms.tools_item 
            GROUP BY
                tools_masters.location 
                ) a
            GROUP BY a.location
            ");

            $tools_detail = DB::select("
            SELECT DISTINCT
                rack_code,
                item_code,
                description,
                location,
                `group`,
                remark,
                category,
                a.tools_item as status
            FROM
                `tools_masters` 
            left join (SELECT DISTINCT
                tools_item,
                tools_description 
            FROM
                tools_boms) a on a.tools_item = tools_masters.item_code
            ");
            
            $response = array(
                'status' => true,
                'tools_resume' => $tools_resume,
                'tools_detail' => $tools_detail
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