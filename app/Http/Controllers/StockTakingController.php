<?php

namespace App\Http\Controllers;

use App\BomOutput;
use App\EmployeeSync;
use App\ErrorLog;
use App\Http\Controllers\Controller;
use App\Lock;
use App\MaterialPlantDataList;
use App\StocktakingCalendar;
use App\StocktakingDailyList;
use App\StocktakingErrorList;
use App\StocktakingInquiryLog;
use App\StocktakingList;
use App\StocktakingLocationStock;
use App\StocktakingMaterialForecast;
use App\StocktakingNewList;
use App\StocktakingOutput;
use App\StocktakingOutputLog;
use App\StocktakingReviseLog;
use App\StocktakingSilverList;
use App\StorageLocation;
use Carbon\Carbon;
use DataTables;
use Excel;
use File;
use FTP;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Response;
use Yajra\DataTables\Exception;

class StockTakingController extends Controller
{

    private $assy_output = array();
    private $cek = array();
    private $temp = array();

    public function __construct()
    {
        $this->middleware('auth');
        $this->storage_location = [
            '203',
            '208',
            '214',
            '216',
            '217',
            '218',
            '401',
            'CL21',
            'CL51',
            'CL61',
            'CL91',
            'CLA0',
            'CLA2',
            'CLB9',
            'CS91',
            'FA0R',
            'FA1R',
            'FL21',
            'FL51',
            'FL61',
            'FL91',
            'FLA0',
            'FLA1',
            'FLA2',
            'FLT9',
            'FSTK',
            'LA0R',
            'MINS',
            'MMJR',
            'MNCF',
            'MS11',
            'MSCR',
            'MSTK',
            'OTHR',
            'PN91',
            'PNR4',
            'PNR9',
            'RC11',
            'RC91',
            'SA0R',
            'SA1R',
            'SX21',
            'SX51',
            'SX61',
            'SX91',
            'SXA0',
            'SXA1',
            'SXA2',
            'SXBR',
            'SXT9',
            'SXWH',
            'VA0R',
            'VN11',
            'VN21',
            'VN51',
            'VN91',
            'VNA0',
            'WCL',
            'WCS',
            'WFL',
            'WFTP',
            'WHST',
            'WLST',
            'WPCS',
            'WPN',
            'WPPN',
            'WPRC',
            'WPRS',
            'WRC',
            'WSCR',
            'WSTP',
            'WSX',
            'YCJP',
            'YCJR',
            'ZPA0',
        ];
        $this->base_unit = [
            'PC',
            'L',
            'SET',
            'KG',
            'G',
            'M',
            'SHT',
            'DS',
            'CAN',
            'CS',
            'BT',
            'DZ',
            'ROL',
            'BAG',
            'PAA',
        ];
        $this->printer_name = [
            'Barcode Printer Sax',
            'Barrel-Printer',
            'FLO Printer 101',
            'FLO Printer 102',
            'FLO Printer 103',
            'FLO Printer 104',
            'FLO Printer 105',
            'FLO Printer LOG',
            'FLO Printer RC',
            'FLO Printer VN',
            'KDO ZPRO',
            'MIS',
            'MIS2',
            'Stockroom-Printer',
            'Welding-Printer',
        ];
    }

    public function indexCheckFloKdo()
    {
        $title = "Stocktaking Check FLO & KDO";
        $title_jp = "";

        return view('stocktakings.check_flo_kdo', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', $title)->with('head', 'Stocktaking');
    }

    public function indexSurveyReport()
    {
        $title = "Stocktaking Survey Report";
        $title_jp = "棚卸のサーベイ報告";

        return view('stocktakings.survey_report', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Monthly Stock Taking')->with('head', 'Stocktaking');
    }

    //Stock Taking Bulanan
    public function indexMonthlyStocktakingList()
    {

        $title = "Stocktaking List";
        $title_jp = "";

        $storage_locations = StorageLocation::whereNotNull('area')->orderBy('storage_location', 'asc')->get();
        $stores = StocktakingNewList::select('store')->distinct()->orderBy('store', 'asc')->get();
        $materials = MaterialPlantDataList::orderBy('material_number', 'asc')->get();

        return view('stocktakings.monthly.report.stocktaking_list', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'storage_locations' => $storage_locations,
            'stores' => $stores,
            'materials' => $materials,
        ))->with('page', 'Monthly Stock Taking')->with('head', 'Stocktaking');
    }

    public function indexStocktakingMaterialForecast()
    {
        $title = "Stocktaking Material Forecast";
        $title_jp = "";

        $material = db::select("SELECT * FROM material_plant_data_lists
         WHERE material_number NOT IN (
         SELECT material_number FROM stocktaking_material_forecasts)");

        return view('stocktakings.monthly.material_forecast', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'materials' => $material,
        ))->with('page', 'Monthly Stock Taking')->with('head', 'Stocktaking');

    }

    public function indexStocktakingCalendar()
    {
        $title = "Stocktaking Calendar";
        $title_jp = "";

        return view('stocktakings.monthly.stocktaking_calendar', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Monthly Stock Taking')->with('head', 'Stocktaking');
    }

    public function indexStocktakingMonitoring()
    {
        $title = "Stocktaking Monitoring";
        $title_jp = "";

        return view('stocktakings.monthly.report.monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Monthly Stock Taking')->with('head', 'Stocktaking Monitoring');
    }

    public function fetchCheckFloKdo(Request $request)
    {

        $flo = [];
        $kdo = [];

        $check_data = $request->get('check_data');
        $category = $request->get('category');
        $rows = preg_split("/\r?\n/", $check_data);

        foreach ($rows as $dt) {
            if (strlen($dt) > 0) {
                if (str_contains($dt, 'KD')) {
                    if (!in_array($dt, $kdo)) {
                        array_push($kdo, $dt);
                    }
                } else {
                    if (!in_array($dt, $flo)) {
                        array_push($flo, $dt);
                    }
                }
            }
        }

        if ($category == 'FSTK') {
            $flos = db::table('flos')
                ->where('status', 2);
            if (count($flo) > 0) {
                $flos = $flos->whereIn('flo_number', $flo);
            }
            $flos = $flos->get();

            $flo = [];
            foreach ($flos as $dt) {
                if (!in_array($dt->flo_number, $flo)) {
                    array_push($flo, $dt->flo_number);
                }
            }

            $kdos = db::table('knock_downs')
                ->where('status', 2);
            if (count($kdo) > 0) {
                $kdos = $kdos->whereIn('kd_number', $kdo);
            }
            $kdos = $kdos->get();

            $kdo = [];
            foreach ($kdos as $dt) {
                if (!in_array($dt->kd_number, $kdo)) {
                    array_push($kdo, $dt->kd_number);
                }
            }

        }

        $flo_detail = db::table('flo_details')
            ->whereIn('flo_number', $flo)
            ->select(
                'flo_number',
                'material_number',
                'serial_number',
                'quantity'
            )
            ->get();

        $flo = db::table('flos')
            ->whereIn('flo_number', $flo)
            ->select(
                'material_number',
                db::raw('SUM(actual) AS quantity')
            )
            ->groupBy('material_number')
            ->get();

        $kdo_detail = db::table('knock_down_details')
            ->whereIn('kd_number', $kdo)
            ->select(
                'kd_number',
                'material_number',
                'serial_number',
                'quantity'
            )
            ->get();

        $kdo = db::table('knock_down_details')
            ->whereIn('kd_number', $kdo)
            ->select(
                'material_number',
                db::raw('SUM(quantity) AS quantity')
            )
            ->groupBy('material_number')
            ->get();

        $material = db::table('material_plant_data_lists')
            ->whereIn('valcl', ['9010', '9040'])
            ->get();

        $response = array(
            'status' => true,
            'flo_detail' => $flo_detail,
            'flo' => $flo,
            'kdo_detail' => $kdo_detail,
            'kdo' => $kdo,
            'material' => $material,
        );
        return Response::json($response);
    }

    public function fetchSurveyReport(Request $request)
    {
        $month = $request->get('month');
        if (strlen($month) <= 0) {
            $month = date('Y-m');
        }

        $calendar = StocktakingCalendar::where('date', 'LIKE', '%' . $month . '%')->first();

        if (!$calendar) {
            $response = array(
                'status' => false,
                'message' => 'Stocktaking Data Not Found',
            );
            return Response::json($response);
        }

        if ($calendar->status == 'planned') {
            $surveys = db::connection('ympimis_2')
                ->select("SELECT survey_code, `date`, employee_id, name, department, score, remark FROM stocktaking_surveys
                WHERE survey_code = 'stoctaking_survey'
                AND `date` = '" . $calendar->date . "'");

        } else {
            $surveys = db::connection('ympimis_2')
                ->select("SELECT survey_code, `date`, employee_id, name, department, score, remark FROM stocktaking_survey_logs
                WHERE survey_code = 'stoctaking_survey'
                AND `date` = '" . $calendar->date . "'");

        }

        $employees = db::select("SELECT emp.employee_id, emp.`name`, emp.department, dept.department_shortname, emp.section, emp.group FROM employee_syncs emp
         LEFT JOIN departments dept ON dept.department_name = emp.department
         WHERE ((emp.hire_date <= '" . $calendar->date . "' AND emp.end_date >= '" . $calendar->date . "')
         OR emp.end_date IS NULL)
         AND emp.section IN (
         'Pianica Process Section',
         'Recorder Proces',
         'Body Parts Process Section',
         'Assembly CL . Tanpo . Case Process Section',
         'Assembly FL Process Section',
         'Assembly Sax Process Section',
         'NC Process Section',
         'Press and Sanding Process Section',
         'Body Buffing-Barrel Process Section',
         'Buffing Key Process Section',
         'SurfaceTreatment Section',
         'Handatsuke . Support Process Section',
         'Koshuha Solder Process Section',
         'Warehouse Section'
         )
         AND emp.position IN (
         'Operator Contract',
         'Operator',
         'Senior Operator',
         'Sub Leader',
         'Leader'
         )
         ORDER BY emp.hire_date ASC");

        $response = array(
            'status' => true,
            'now' => date('Y-m-d H:i:s'),
            'month' => date('F Y', strtotime($month . '-01')),
            'surveys' => $surveys,
            'employees' => $employees,
        );
        return Response::json($response);
    }

    public function fetchMonthlyStocktakingList(Request $request)
    {

        $stocktaking_lists = StocktakingNewList::whereNull('stocktaking_new_lists.deleted_at');

        if ($request->get('store') != null) {
            $stocktaking_lists = $stocktaking_lists->whereIn('stocktaking_new_lists.store', $request->get('store'));
        }

        if ($request->get('material_number') != null) {
            $stocktaking_lists = $stocktaking_lists->whereIn('stocktaking_new_lists.material_number', $request->get('material_number'));
        }

        if ($request->get('storage_location') != null) {
            $stocktaking_lists = $stocktaking_lists->whereIn('stocktaking_new_lists.location', $request->get('storage_location'));
        }

        if ($request->get('area') != null) {
            $stocktaking_lists = $stocktaking_lists->whereIn('stocktaking_new_lists.area', $request->get('area'));
        }

        $stocktaking_lists = $stocktaking_lists->select(
            'stocktaking_new_lists.id',
            'stocktaking_new_lists.area',
            'stocktaking_new_lists.store',
            'stocktaking_new_lists.sub_store',
            'stocktaking_new_lists.material_number',
            'stocktaking_new_lists.material_description',
            db::raw('"-" AS bun'),
            'stocktaking_new_lists.location',
            'stocktaking_new_lists.category',
            'stocktaking_new_lists.process',
            'stocktaking_new_lists.print_status'
        )
            ->orderBy('stocktaking_new_lists.area', 'asc')
            ->orderBy('stocktaking_new_lists.store', 'asc')
            ->orderBy('stocktaking_new_lists.material_number', 'asc')
            ->get();

        $response = array(
            'status' => true,
            'stocktaking_lists' => $stocktaking_lists,
        );
        return Response::json($response);
    }

    public function fetchStocktakingCalendar()
    {
        # code...
    }

    public function fetchStocktakingMaterialForecast()
    {

        $material = StocktakingMaterialForecast::leftJoin('material_plant_data_lists', 'material_plant_data_lists.material_number', '=', 'stocktaking_material_forecasts.material_number')
            ->leftJoin('users', 'users.id', '=', 'stocktaking_material_forecasts.created_by')
            ->select('stocktaking_material_forecasts.material_number', 'material_plant_data_lists.material_description', 'users.name')
            ->get();

        return DataTables::of($material)->make(true);
    }

    public function deleteMonthlyStocktakingList(Request $request)
    {
        try {
            $stocktaking_list = StocktakingNewList::where('id', $request->get('id'))->forceDelete();

            $response = array(
                'status' => true,
                'message' => 'Stocktaking list berhasil di delete.',
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

    public function uploadMonthlyStocktakingList(Request $request)
    {

        if ($request->hasFile('file_list')) {
            try {
                $file = $request->file('file_list');
                $file_name = 'st_list_' . Auth::id() . '(' . date("ymdHi") . ')' . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/stocktaking_list/'), $file_name);

                $excel = public_path('uploads/stocktaking_list/') . $file_name;
                $rows = Excel::load($excel, function ($reader) {
                    $reader->noHeading();
                    $reader->skipRows(1);
                })->get();
                $rows = $rows->toArray();

                $success = 0;
                $total = 0;

                for ($i = 0; $i < count($rows); $i++) {
                    $location = preg_replace('/[^a-zA-Z0-9]+/', '', strtoupper($rows[$i][1]));
                    $store = preg_replace('/[^a-zA-Z0-9 &-_]+/', '', strtoupper($rows[$i][2]));
                    $sub_store = preg_replace('/[^a-zA-Z0-9 &-_]+/', '', strtoupper($rows[$i][3]));
                    $material_number = preg_replace('/[^a-zA-Z0-9]+/', '', strtoupper($rows[$i][4]));
                    $material_description = preg_replace('/[^a-zA-Z0-9 ]+/', '', strtoupper($rows[$i][5]));
                    $category = preg_replace('/[^a-zA-Z0-9]+/', '', strtoupper($rows[$i][6]));

                    if ($location != '' || $store != '' || $sub_store != '' || $material_number != '' || $category != '') {
                        $total++;

                        $mpdl = MaterialPlantDataList::where('material_number', $material_number)->first();

                        if ($mpdl) {
                            $check = StocktakingNewList::where('location', $location)
                                ->where('store', $store)
                                ->where('sub_store', $sub_store)
                                ->where('material_number', $material_number)
                                ->where('category', $category)
                                ->first();

                            if ($check) {
                                $error = new StocktakingErrorList([
                                    'file_name' => $file_name,
                                    'location' => $location,
                                    'store' => $store,
                                    'sub_store' => $sub_store,
                                    'material_number' => $material_number,
                                    'material_description' => $mpdl->material_description,
                                    'category' => $category,
                                    'error_message' => 'Item sudah ada di list',
                                    'created_by' => Auth::id(),
                                ]);
                                $error->save();
                            } else {
                                if (($mpdl->valcl == 9040 || $mpdl->valcl == 9041) && $category == 'ASSY') {
                                    $error = new StocktakingErrorList([
                                        'file_name' => $file_name,
                                        'location' => $location,
                                        'store' => $store,
                                        'sub_store' => $sub_store,
                                        'material_number' => $material_number,
                                        'material_description' => $mpdl->material_description,
                                        'category' => $category,
                                        'error_message' => 'Item tidak boleh ASSY',
                                        'created_by' => Auth::id(),
                                    ]);
                                    $error->save();
                                } else if ($mpdl->spt == 50 && $category == 'SINGLE') {
                                    $error = new StocktakingErrorList([
                                        'file_name' => $file_name,
                                        'location' => $location,
                                        'store' => $store,
                                        'sub_store' => $sub_store,
                                        'material_number' => $material_number,
                                        'material_description' => $mpdl->material_description,
                                        'category' => $category,
                                        'error_message' => 'Item tidak boleh SINGLE',
                                        'created_by' => Auth::id(),
                                    ]);
                                    $error->save();
                                } else {
                                    $list = new StocktakingNewList([
                                        'location' => $location,
                                        'store' => $store,
                                        'sub_store' => $sub_store,
                                        'material_number' => $material_number,
                                        'category' => $category,
                                        'created_by' => Auth::id(),
                                    ]);
                                    $list->save();
                                    $success++;
                                }
                            }
                        } else {
                            $error = new StocktakingErrorList([
                                'file_name' => $file_name,
                                'location' => $location,
                                'store' => $store,
                                'sub_store' => $sub_store,
                                'material_number' => $material_number,
                                'material_description' => $material_description,
                                'category' => $category,
                                'error_message' => 'Item tidak ada di MPDL',
                                'created_by' => Auth::id(),
                            ]);
                            $error->save();
                        }

                    }
                }

                $update_desc = db::select("UPDATE stocktaking_new_lists
                    LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = stocktaking_new_lists.material_number
                    SET stocktaking_new_lists.material_description = material_plant_data_lists.material_description
                    WHERE stocktaking_new_lists.material_description IS NULL;");

                $update_area = db::select("UPDATE stocktaking_new_lists
                    LEFT JOIN storage_locations ON storage_locations.storage_location = stocktaking_new_lists.location
                    SET stocktaking_new_lists.area = storage_locations.area
                    WHERE stocktaking_new_lists.area IS NULL;");

                $response = array(
                    'status' => true,
                    'message' => 'Upload file success',
                    'file_name' => $file_name,
                    'total' => $total,
                    'success' => $success,
                );
                return Response::json($response);

            } catch (\Exception$e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        } else {
            $response = array(
                'status' => false,
                'message' => 'Upload failed, File not found',
            );
            return Response::json($response);
        }
    }

    public function importInvKitto(Request $request)
    {
        $now = date('Y-m-d H:i:s');
        $upload = $request->get('upload');
        $uploadRows = preg_split("/\r?\n/", $upload);

        DB::beginTransaction();
        DB::table('kitto_inventories')->truncate();

        $row = 0;
        foreach ($uploadRows as $uploadRow) {
            $uploadColumn = preg_split("/\t/", $uploadRow);

            $material = strtoupper($uploadColumn[0]);
            $sloc = $uploadColumn[1];
            $quantity = $uploadColumn[2];

            if (strlen($material) < 7) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => 'Line ' . ++$row . ' : GMC Unmatch',
                );
                return Response::json($response);
            }

            if ($quantity > 0) {

                try {

                    $insert = DB::table('kitto_inventories')
                        ->insert([
                            'material_number' => $material,
                            'storage_location' => $sloc,
                            'quantity' => $quantity,
                            'created_by' => Auth::id(),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);

                } catch (Exception $e) {
                    DB::rollback();
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                    return Response::json($response);
                }

            }
        }

        DB::commit();
        $response = array(
            'status' => true,
        );
        return Response::json($response);
    }

    public function importbom(Request $request)
    {
        $upload = $request->get('upload');
        $uploadRows = preg_split("/\r?\n/", $upload);

        DB::beginTransaction();
        BomOutput::truncate();

        $row = 0;
        foreach ($uploadRows as $uploadRow) {
            ++$row;
            $uploadColumn = preg_split("/\t/", $uploadRow);

            $material_parent = strtoupper($uploadColumn[0]);
            $material_child = strtoupper($uploadColumn[1]);
            $storage_location = $uploadColumn[2];
            $spt = $uploadColumn[3];
            $valcl = $uploadColumn[4];
            $uom = $uploadColumn[5];
            $usage = $uploadColumn[6];
            $divider = $uploadColumn[7];

            if (strlen($material_parent) < 7 || strlen($material_child) < 7) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => 'Line ' . $row . ' : GMC Unmatch',
                );
                return Response::json($response);
            }

            if (strlen($spt) == 0 || $spt == 0) {
                $spt = null;
            }

            try {
                $bom = new BomOutput([
                    'material_parent' => $material_parent,
                    'material_child' => $material_child,
                    'usage' => $usage,
                    'divider' => $divider,
                    'uom' => $uom,
                    'storage_location' => $storage_location,
                    'spt' => $spt,
                    'valcl' => $valcl,
                    'created_by' => Auth::id(),
                ]);
                $bom->save();

            } catch (Exception $e) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }

        DB::commit();
        $response = array(
            'status' => true,
        );
        return Response::json($response);

    }

    public function importmpdl(Request $request)
    {

        $upload = $request->get('upload');
        $uploadRows = preg_split("/\r?\n/", $upload);

        DB::beginTransaction();
        MaterialPlantDataList::truncate();

        $row = 0;
        foreach ($uploadRows as $uploadRow) {
            ++$row;
            $uploadColumn = preg_split("/\t/", $uploadRow);

            $material = strtoupper($uploadColumn[0]);
            $description = strtoupper($uploadColumn[1]);
            $pgr = $uploadColumn[2];
            $bun = $uploadColumn[3];
            $mrpc = $uploadColumn[4];
            $spt = $uploadColumn[5];
            $sloc = $uploadColumn[6];
            $valcl = $uploadColumn[7];
            $price = $uploadColumn[8];

            if (strlen($material) < 7) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => 'Line ' . $row . ' : GMC Unmatch',
                );
                return Response::json($response);
            }

            if (strlen($spt) == 0 || $spt == 0) {
                $spt = null;
            }

            try {
                $bom = new MaterialPlantDataList([
                    'material_number' => $material,
                    'material_description' => $description,
                    'pgr' => $pgr,
                    'bun' => $bun,
                    'spt' => $spt,
                    'storage_location' => $sloc,
                    'mrpc' => $mrpc,
                    'valcl' => $valcl,
                    'standard_price' => $price,
                    'created_by' => Auth::id(),
                ]);
                $bom->save();

            } catch (Exception $e) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }

        DB::commit();
        $response = array(
            'status' => true,
        );
        return Response::json($response);

    }

    public function exportFstkPi(Request $request)
    {
        $now = date('Y-m-d H:i:s');
        DB::beginTransaction();
        $delete = StocktakingNewList::where('location', '=', 'FSTK')->forceDelete();

        $pi = db::connection('ympimis_2')
            ->select("SELECT material_number, SUM(pi) AS quantity FROM `stocktaking_finish_goods`
            WHERE category = 'PI'
            GROUP BY material_number");

        for ($i = 0; $i < count($pi); $i++) {
            try {
                $insert = new StocktakingNewList([
                    'location' => 'FSTK',
                    'store' => 'FSTK',
                    'sub_store' => 'FSTK',
                    'material_number' => $pi[$i]->material_number,
                    'category' => 'SINGLE',
                    'process' => 1,
                    'print_status' => 1,
                    'remark' => 'USE',
                    'quantity' => $pi[$i]->quantity,
                    'inputed_by' => $request->get('employee_id'),
                    'inputed_at' => $now,
                    'created_by' => Auth::id(),

                ]);
                $insert->save();

            } catch (\Exception$e) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }

        DB::commit();
        $response = array(
            'status' => true,
            'message' => 'Hasil input PI berhasil di ekspor',
        );
        return Response::json($response);

    }

    public function exportMstkPi(Request $request)
    {
        $response = array(
            'status' => false,
            'message' => 'Maaf Masih Belum Bisa Di Gunakan',
        );
        return Response::json($response);
        // $now = date('Y-m-d H:i:s');
        // DB::beginTransaction();
        // $delete = StocktakingNewList::where('location', '=', 'MSCR')->forceDelete();

        // $pi = db::connection('ympimis_2')
        //     ->select("SELECT material_number, SUM(pi) AS quantity FROM `stocktaking_finish_goods`
        //     WHERE category = 'PI'
        //     GROUP BY material_number");

        // for ($i = 0; $i < count($pi); $i++) {
        //     try {
        //         $insert = new StocktakingNewList([
        //             'location' => 'FSTK',
        //             'store' => 'FSTK',
        //             'sub_store' => 'FSTK',
        //             'material_number' => $pi[$i]->material_number,
        //             'category' => 'SINGLE',
        //             'process' => 1,
        //             'print_status' => 1,
        //             'remark' => 'USE',
        //             'quantity' => $pi[$i]->quantity,
        //             'inputed_by' => $request->get('employee_id'),
        //             'inputed_at' => $now,
        //             'created_by' => Auth::id(),

        //         ]);
        //         $insert->save();

        //     } catch (\Exception$e) {
        //         DB::rollback();
        //         $response = array(
        //             'status' => false,
        //             'message' => $e->getMessage(),
        //         );
        //         return Response::json($response);
        //     }
        // }

        // DB::commit();
        // $response = array(
        //     'status' => true,
        //     'message' => 'Hasil input PI berhasil di ekspor',
        // );
        // return Response::json($response);

    }

    public function exportErrorUpload(Request $request)
    {

        $file_name = $request->get('file_name');

        $error = StocktakingErrorList::where('file_name', $file_name)->get();

        $title = 'Error_upload_stocktakinglist_' . str_replace('.xlsx', '', $file_name);

        $data = array(
            'error' => $error,
        );

        ob_clean();
        Excel::create($title, function ($excel) use ($data) {
            $excel->sheet('Error', function ($sheet) use ($data) {
                return $sheet->loadView('stocktakings.monthly.report.error_upload_stocktakinglist', $data);
            });
        })->export('xlsx');

    }

    public function editMonthlyStocktakingList(Request $request)
    {
        try {
            $stocktaking_list = StocktakingNewList::where('id', $request->get('id'))->first();

            $stocktaking_list->store = $request->get('store');
            $stocktaking_list->sub_store = $request->get('substore');
            $stocktaking_list->material_number = $request->get('material');
            $stocktaking_list->location = $request->get('location');
            $stocktaking_list->category = $request->get('category');

            $stocktaking_list->save();

            $response = array(
                'status' => true,
                'message' => 'Stocktaking list berhasil di update.',
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

    public function indexMonthlyStocktaking()
    {
        $title = 'Monthly Stocktaking';
        $title_jp = '月次棚卸';

        $lists = db::table('stocktaking_ymes_lists')
            ->select('location', 'list_no')
            ->distinct()
            ->orderBy('location', 'ASC')
            ->orderBy('list_no', 'ASC')
            ->get();

        $ymes_locations = db::table('storage_locations')
            ->where('area', 'WAREHOUSE')
            ->whereNotNull('category')
            ->orderBy('storage_location', 'ASC')
            ->get();

        $role = Auth::user()->role_code;

        return view('stocktakings.monthly.index', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'role' => $role,
            'lists' => $lists,
            'ymes_locations' => $ymes_locations,
        ))->with('page', 'Monthly Stock Taking')->with('head', 'Stocktaking');
    }

    public function indexManageStore()
    {
        $title = 'Summary of Counting';
        $title_jp = '';

        $printer_names = $this->printer_name;

        $groups = StorageLocation::select('area')
            ->whereNotNull('area')
            ->distinct()
            ->orderBy('area', 'ASC')
            ->get();

        $locations = StocktakingNewList::select('stocktaking_new_lists.location')
            ->distinct()
            ->orderBy('stocktaking_new_lists.location', 'ASC')
            ->get();

        $stores = StocktakingNewList::select('stocktaking_new_lists.store')
            ->distinct()
            ->orderBy('stocktaking_new_lists.store', 'ASC')
            ->get();

        $materials = MaterialPlantDataList::select('material_number', 'material_description')->get();

        return view('stocktakings.monthly.manage_store', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'printer_names' => $printer_names,
            'groups' => $groups,
            'locations' => $locations,
            'stores' => $stores,
            'materials' => $materials,
        ))->with('page', 'Manage Store')->with('head', 'Stocktaking');
    }

    public function indexSummaryNew()
    {
        $title = 'Summary Of Counting';
        $title_jp = '';

        $printer_names = $this->printer_name;

        $groups = StorageLocation::select('area')
            ->whereNotNull('area')
            ->distinct()
            ->orderBy('area', 'ASC')
            ->get();

        $locations = StocktakingNewList::select('location')
            ->distinct()
            ->orderBy('location', 'ASC')
            ->get();

        $stores = StocktakingNewList::select('location', 'store')
            ->distinct()
            ->orderBy('location', 'ASC')
            ->get();

        $substores = StocktakingNewList::select('store', 'sub_store')
            ->distinct()
            ->orderBy('store', 'ASC')
            ->get();

        $materials = MaterialPlantDataList::select('material_number', 'material_description')->get();

        $employee = EmployeeSync::where('employee_id', strtoupper(Auth::user()->username))->first();

        return view('stocktakings.monthly.summary_of_counting_new', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'printer_names' => $printer_names,
            'groups' => $groups,
            'locations' => $locations,
            'stores' => $stores,
            'substores' => $substores,
            'materials' => $materials,
            'employee' => $employee,
        ))->with('page', 'Manage Store')->with('head', 'Stocktaking');
    }

    public function indexRevise()
    {
        $title = 'Revision';
        $title_jp = '改定';

        return view('stocktakings.monthly.revise', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Revise')->with('head', 'Stocktaking');
    }

    public function indexReviseNew()
    {
        $title = 'Revision';
        $title_jp = '改定';

        return view('stocktakings.monthly.revise_new', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Revise')->with('head', 'Stocktaking');
    }

    public function indexReviseUser()
    {
        if (str_contains(strtoupper(Auth::user()->username), 'PI')) {
            $title = 'Revision';
            $title_jp = '改定';

            return view('stocktakings.monthly.revise_photo', array(
                'title' => $title,
                'title_jp' => $title_jp,
            ))->with('page', 'Revise')->with('head', 'Stocktaking');

        } else {
            return view('404');
        }
    }

    public function indexUnmatch($month)
    {
        $title = 'Unmatch';
        $title_jp = 'チェック不適合';

        return view('stocktakings.monthly.unmatch', array(
            'month' => $month,
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Unmatch')->with('head', 'Stocktaking');
    }

    public function indexUnmatchYmesList()
    {
        $title = 'Unmatch';
        $title_jp = 'チェック不適合';

        return view('stocktakings.ymes.unmatch', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Unmatch')->with('head', 'Stocktaking');
    }

    public function indexNoUse()
    {
        $title = 'No Use';
        $title_jp = '使用しない';

        return view('stocktakings.monthly.no_use', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'No Use')->with('head', 'Stocktaking');
    }

    public function indexNoUseNew()
    {
        $title = 'No Use';
        $title_jp = '使用しない';

        return view('stocktakings.monthly.no_use_new', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'No Use')->with('head', 'Stocktaking');
    }

    public function indexCount()
    {
        $title = 'Monthly Stocktaking';
        $title_jp = '月次棚卸';

        $employees = EmployeeSync::whereNull('end_date')->get();

        return view('stocktakings.monthly.count', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employees' => $employees,
        ))->with('page', 'Monthly Stock Taking Count')->with('head', 'Stocktaking');
    }

    public function indexCountNew()
    {
        $title = 'Monthly Stocktaking';
        $title_jp = '月次棚卸';

        $employees = EmployeeSync::whereNull('end_date')->get();

        return view('stocktakings.monthly.count_new', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employees' => $employees,
        ))->with('page', 'New Monthly Stock Taking Count')->with('head', 'Stocktaking');
    }

    public function indexCountFstk()
    {
        $title = 'Stocktaking FSTK';
        $title_jp = 'FSTK（倉庫）棚卸し';

        $mpdl = db::table('material_plant_data_lists')
            ->whereIn('valcl', ['9010', '9040'])
            ->get();

        $department = [
            'Logistic Department',
            'Management Information System Department',
            'Production Control Department',
        ];

        $employees = EmployeeSync::whereIn('department', $department)
            ->whereNull('end_date')
            ->get();

        return view('stocktakings.monthly.count_fstk', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employees' => $employees,
            'mpdl' => $mpdl,
        ))->with('page', 'New Monthly Stock Taking Count')->with('head', 'Stocktaking');
    }

    public function indexCountScrap()
    {
        $title = 'Stocktaking MSTK';
        $title_jp = 'MSTK（倉庫）棚卸し';

        $mpdl = db::table('material_plant_data_lists')->get();

        // $scrap_logs = db::table('scrap_logs')
        // ->whereIn('receive_location', ['MSCR', 'WSCR', 'OTHR', 'MMJR'])
        // ->get();

        $data = db::connection('ympimis_2')->table('stocktaking_scraps')->get();

        $department = [
            'Logistic Department',
            'Management Information System Department',
            'Production Control Department',
        ];

        $employees = EmployeeSync::whereIn('department', $department)
            ->whereNull('end_date')
            ->get();

        return view('stocktakings.monthly.index_scrap', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employees' => $employees,
            'mpdl' => $mpdl,
            // 'scrap_logs' => $scrap_logs,
            'data' => $data,
        ))->with('page', 'New Monthly Stock Taking Count')->with('head', 'Stocktaking');
    }

    public function indexAudit($id)
    {
        $title = 'Audit ' . $id;
        $title_jp = '監査 ' . $id;

        if ($id == 1) {
            $auditors = db::select("SELECT * FROM employee_syncs
                where position like '%Leader%'
                or position = 'Foreman'
                or position like '%Staff%'");

            return view('stocktakings.monthly.audit_1', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'auditors' => $auditors,
            ))->with('page', 'Monthly Stock Audit 1')->with('head', 'Stocktaking');

        } else if ($id == 2) {
            $auditors = EmployeeSync::orwhere('position', 'like', '%Staff%')
                ->orWhere('position', '=', 'Chief')
                ->orWhere('position', '=', 'Foreman')
                ->orWhere('position', '=', 'Coordinator')
                ->orWhere('position', '=', 'Staff')
                ->orWhere('position', '=', 'Senior Staff')
                ->WhereNotNull('end_date')
                ->get();

            return view('stocktakings.monthly.audit_2', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'auditors' => $auditors,
            ))->with('page', 'Monthly Stock Audit 2')->with('head', 'Stocktaking');
        }
    }

    public function indexAuditNew($id)
    {
        $title = 'Audit ' . $id;
        $title_jp = '監査 ' . $id;

        if ($id == 1) {
            $auditors = db::select("SELECT * FROM employee_syncs
                where position like '%Leader%'
                or position = 'Foreman'
                or position like '%Staff%'");

            return view('stocktakings.monthly.audit_1_new', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'auditors' => $auditors,
            ))->with('page', 'Monthly Stock Audit 1')->with('head', 'Stocktaking');

        } else if ($id == 2) {
            $auditors = EmployeeSync::orwhere('position', 'like', '%Staff%')
                ->orWhere('position', '=', 'Chief')
                ->orWhere('position', '=', 'Foreman')
                ->orWhere('position', '=', 'Coordinator')
                ->orWhere('position', '=', 'Staff')
                ->orWhere('position', '=', 'Senior Staff')
                ->WhereNotNull('end_date')
                ->get();

            return view('stocktakings.monthly.audit_2', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'auditors' => $auditors,
            ))->with('page', 'Monthly Stock Audit 2')->with('head', 'Stocktaking');
        }
    }

    public function indexSummaryOfCounting()
    {
        $title = 'Summary of Counting';
        $title_jp = '計算まとめ';

        return view('stocktakings.monthly.summary_of_counting', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Summary Of Counting')->with('head', 'Stocktaking');
    }

    public function countNewStcPISingle($locations)
    {

        $loc = '';
        $location = '';
        for ($i = 0; $i < count($locations); $i++) {
            $location = $location . "'" . $locations[$i] . "'";
            if ($i != (count($locations) - 1)) {
                $location = $location . ',';
            }
        }
        $loc = " AND location IN (" . $location . ") ";

        $single = db::select("SELECT location, material_number, sum(final_count) AS final_count FROM stocktaking_new_lists
         WHERE category = 'SINGLE'
         AND final_count > 0
         " . $loc . "
         GROUP BY location, material_number");

        for ($i = 0; $i < count($single); $i++) {

            $insert = new StocktakingOutput([
                'material_number' => $single[$i]->material_number,
                // 'store' => $single[$i]->store,
                'location' => $single[$i]->location,
                'quantity' => $single[$i]->final_count,
            ]);
            $insert->save();
        }
    }

    public function countNewStcPIAssy($locations)
    {

        $loc = '';
        $location = '';
        for ($i = 0; $i < count($locations); $i++) {
            $location = $location . "'" . $locations[$i] . "'";
            if ($i != (count($locations) - 1)) {
                $location = $location . ',';
            }
        }
        $loc = " AND location IN (" . $location . ") ";

        $assy = db::select("SELECT location, material_number, sum(final_count) AS final_count FROM stocktaking_new_lists
         WHERE category = 'ASSY'
         AND final_count > 0
         " . $loc . "
         GROUP BY location, material_number");

        for ($i = 0; $i < count($assy); $i++) {
            $breakdown = db::select("SELECT b.material_parent, b.material_child, b.`usage`, b.divider, b.spt FROM bom_outputs b
                WHERE b.material_parent = '" . $assy[$i]->material_number . "'");

            for ($j = 0; $j < count($breakdown); $j++) {
                if ($breakdown[$j]->spt == 50) {
                    $row = array();
                    $row['material_number'] = $breakdown[$j]->material_child;
                    // $row['store'] = $assy[$i]->store;
                    $row['location'] = $assy[$i]->location;
                    $row['quantity'] = $assy[$i]->final_count * ($breakdown[$j]->usage / $breakdown[$j]->divider);
                    $row['created_at'] = Carbon::now();
                    $row['updated_at'] = Carbon::now();

                    $this->cek[] = $row;
                } else {
                    $row = array();
                    $row['material_number'] = $breakdown[$j]->material_child;
                    // $row['store'] = $assy[$i]->store;
                    $row['location'] = $assy[$i]->location;
                    $row['quantity'] = $assy[$i]->final_count * ($breakdown[$j]->usage / $breakdown[$j]->divider);
                    $row['created_at'] = Carbon::now();
                    $row['updated_at'] = Carbon::now();

                    $this->assy_output[] = $row;
                }
            }
        }

        while (count($this->cek) > 0) {
            $this->breakdownNew();
        }

        foreach (array_chunk($this->assy_output, 1000) as $t) {
            $output = StocktakingOutput::insert($t);
        }
    }

    public function breakdownNew()
    {

        $this->temp = array();

        for ($i = 0; $i < count($this->cek); $i++) {
            $breakdown = db::select("SELECT b.material_parent, b.material_child, b.`usage`, b.divider, b.spt FROM bom_outputs b
                WHERE b.material_parent = '" . $this->cek[$i]['material_number'] . "'");

            for ($j = 0; $j < count($breakdown); $j++) {

                if ($breakdown[$j]->spt == 50) {
                    $row = array();
                    $row['material_number'] = $breakdown[$j]->material_child;
                    // $row['store'] = $this->cek[$i]['store'];
                    $row['location'] = $this->cek[$i]['location'];
                    $row['quantity'] = $this->cek[$i]['quantity'] * ($breakdown[$j]->usage / $breakdown[$j]->divider);
                    $row['created_at'] = Carbon::now();
                    $row['updated_at'] = Carbon::now();
                    $this->temp[] = $row;
                } else {
                    $row = array();
                    $row['material_number'] = $breakdown[$j]->material_child;
                    // $row['store'] = $this->cek[$i]['store'];
                    $row['location'] = $this->cek[$i]['location'];
                    $row['quantity'] = $this->cek[$i]['quantity'] * ($breakdown[$j]->usage / $breakdown[$j]->divider);
                    $row['created_at'] = Carbon::now();
                    $row['updated_at'] = Carbon::now();
                    $this->assy_output[] = $row;
                }
            }
        }

        $this->cek = array();
        $this->cek = $this->temp;
    }

    public function indexCountPINew(Request $request)
    {
        $group = $request->get('group');

        $lock = Lock::leftJoin('users', 'users.id', '=', 'locks.updated_by')
            ->where('remark', '=', 'breakdown_pi')
            ->first();

        if ($lock->status == 1) {
            $response = array(
                'status' => false,
                'message' => 'Breakdown PI sedang dilakukan oleh ' . $lock->name . ', saat ini breakdown PI tidak bisa dilakukan untuk mencegah hasil double.',
            );
            return Response::json($response);
        }

        try {
            $lock = Lock::where('remark', '=', 'breakdown_pi')
                ->update([
                    'status' => 1,
                    'updated_by' => Auth::id(),
                ]);

        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        $locations = StorageLocation::whereIn('area', $group)
            ->select('storage_location')
            ->get();

        $location = array();
        for ($i = 0; $i < count($locations); $i++) {
            array_push($location, $locations[$i]->storage_location);
        }

        try {
            DB::transaction(function () use ($location) {
                $delete = StocktakingOutput::whereIn('location', $location)
                    ->delete();

                $update = StocktakingNewList::where('process', 2)
                    ->whereIn('location', $location)
                    ->update([
                        'process' => 4,
                    ]);
            });

            $this->countNewStcPISingle($location);
            $this->countNewStcPIAssy($location);

            $lock = Lock::where('remark', '=', 'breakdown_pi')
                ->update([
                    'status' => 0,
                    'updated_by' => Auth::id(),
                ]);

            $response = array(
                'status' => true,
                'message' => 'Count PI Berhasil',
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

    public function indexCountPI(Request $request)
    {
        $group = $request->get('group');

        // $lock = db::table('locks')->where('remark', '=', 'breakdown_pi')->first();

        // if($lock->status == 1){
        //     $response = array(
        //         'status' => false,
        //         'message' => 'Breakdown PI sedang dilakukan, tidak melakukan proses double.',
        //     );
        //     return Response::json($response);
        // }

        // $lock->status = 1;
        // $lock->save();

        $locations = StorageLocation::whereIn('area', $group)
            ->select('storage_location')
            ->get();

        // $location = '';
        // for ($i=0; $i < count($locations); $i++) {
        //     $location = $location."'".$locations[$i]->storage_location."'";
        //     if($i != (count($locations)-1)){
        //         $location = $location.',';
        //     }
        // }
        // $data = db::select("SELECT DISTINCT process FROM stocktaking_lists WEHER location IN (".$location.")");

        $location = array();
        for ($i = 0; $i < count($locations); $i++) {
            array_push($location, $locations[$i]->storage_location);
        }

        //Supaya Bisa Breakdown PI

        // $data = StocktakingList::whereIn('location', $location)
        // ->where('print_status', 1)
        // ->get();

        // for ($i=0; $i < count($data); $i++) {
        //     if($data[$i]->process < 2){
        //         $response = array(
        //             'status' => false,
        //             'message' => 'Ada slip yang belum di input atau di audit.',
        //         );
        //         return Response::json($response);
        //     }
        // }

        try {
            DB::transaction(function () use ($location) {
                $delete = StocktakingOutput::whereIn('location', $location)
                    ->delete();

                $update = StocktakingList::where('final_count', '>', 0)
                    ->whereIn('location', $location)
                    ->update([
                        'process' => 4,
                    ]);
            });

            $this->countPISingle($location);
            $this->countPIAssyNew2($location);

            // $lock->status = 0;
            // $lock->save();

            $response = array(
                'status' => true,
                'message' => 'Count PI Berhasil',
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

    public function indexCheckInput()
    {

        $title = 'Check Physical Inventory Input';
        $title_jp = '実地棚卸入力を確認する';

        $store = db::select("SELECT DISTINCT sl.area, st.location, st.store FROM stocktaking_new_lists st
         LEFT JOIN storage_locations sl ON sl.storage_location = st.location
         ORDER BY sl.area, st.location, st.store ASC");

        return view('stocktakings.monthly.check_input_new', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'stores' => $store,
        ))->with('page', 'Check Input PI')->with('head', 'Stocktaking');
    }

    public function countPISingle($location)
    {
        $single = StocktakingList::whereIn('location', $location)
            ->where('category', 'SINGLE')
            ->where('final_count', '>', 0)
            ->get();

        for ($i = 0; $i < count($single); $i++) {

            $insert = new StocktakingOutput([
                'material_number' => $single[$i]->material_number,
                'store' => $single[$i]->store,
                'location' => $single[$i]->location,
                'quantity' => $single[$i]->final_count,
            ]);
            $insert->save();
        }
    }

    public function countPIAssy()
    {
        $assy = db::select("SELECT s.material_number, s.store, s.location, s.final_count, b.material_child, b.`usage`, b.divider, m.spt, (s.final_count*(b.`usage`/b.divider)) as quantity FROM stocktaking_lists s
         left join bom_outputs b on s.material_number = b.material_parent
         left join material_plant_data_lists m on m.material_number = b.material_child
         where s.category = 'ASSY'");

        for ($i = 0; $i < count($assy); $i++) {
            if ($assy[$i]->spt == 50) {
                $row = array();
                $row['material_number'] = $assy[$i]->material_child;
                $row['store'] = $assy[$i]->store;
                $row['location'] = $assy[$i]->location;
                $row['quantity'] = $assy[$i]->quantity;
                $row['created_at'] = Carbon::now();
                $row['updated_at'] = Carbon::now();

                $this->cek[] = $row;
            } else {
                $row = array();
                $row['material_number'] = $assy[$i]->material_child;
                $row['store'] = $assy[$i]->store;
                $row['location'] = $assy[$i]->location;
                $row['quantity'] = $assy[$i]->quantity;
                $row['created_at'] = Carbon::now();
                $row['updated_at'] = Carbon::now();

                $this->assy_output[] = $row;

            }
        }

        while (count($this->cek) > 0) {
            $this->breakdown();
        }

        foreach (array_chunk($this->assy_output, 1000) as $t) {
            $output = StocktakingOutput::insert($t);
        }
    }

    public function breakdown()
    {

        $this->temp = array();

        for ($i = 0; $i < count($this->cek); $i++) {
            $breakdown = db::select("SELECT b.material_parent, b.material_child, b.`usage`, b.divider, m.spt
                FROM bom_outputs b
                LEFT JOIN material_plant_data_lists m ON m.material_number = b.material_child
                WHERE b.material_parent = '" . $this->cek[$i]['material_number'] . "'");

            for ($j = 0; $j < count($breakdown); $j++) {

                if ($breakdown[$j]->spt == 50) {
                    $row = array();
                    $row['material_number'] = $breakdown[$j]->material_child;
                    $row['store'] = $this->cek[$i]['store'];
                    $row['location'] = $this->cek[$i]['location'];
                    $row['quantity'] = $this->cek[$i]['quantity'] * ($breakdown[$j]->usage / $breakdown[$j]->divider);
                    $row['created_at'] = Carbon::now();
                    $row['updated_at'] = Carbon::now();
                    $this->temp[] = $row;
                } else {
                    $row = array();
                    $row['material_number'] = $breakdown[$j]->material_child;
                    $row['store'] = $this->cek[$i]['store'];
                    $row['location'] = $this->cek[$i]['location'];
                    $row['quantity'] = $this->cek[$i]['quantity'] * ($breakdown[$j]->usage / $breakdown[$j]->divider);
                    $row['created_at'] = Carbon::now();
                    $row['updated_at'] = Carbon::now();
                    $this->assy_output[] = $row;
                }
            }
        }

        $this->cek = array();
        $this->cek = $this->temp;
    }

    public function countPIAssyNew2($location)
    {
        $assy = StocktakingList::whereIn('location', $location)
            ->where('category', 'ASSY')
            ->where('final_count', '>', 0)
            ->get();

        for ($i = 0; $i < count($assy); $i++) {
            $breakdown = db::select("SELECT b.material_parent, b.material_child, b.`usage`, b.divider, m.spt
                FROM bom_outputs b
                LEFT JOIN material_plant_data_lists m ON m.material_number = b.material_child
                WHERE b.material_parent = '" . $assy[$i]->material_number . "'");

            for ($j = 0; $j < count($breakdown); $j++) {
                if ($breakdown[$j]->spt == 50) {
                    $row = array();
                    $row['material_number'] = $breakdown[$j]->material_child;
                    $row['store'] = $assy[$i]->store;
                    $row['location'] = $assy[$i]->location;
                    $row['quantity'] = $assy[$i]->final_count * ($breakdown[$j]->usage / $breakdown[$j]->divider);
                    $row['created_at'] = Carbon::now();
                    $row['updated_at'] = Carbon::now();

                    $this->cek[] = $row;
                } else {
                    $row = array();
                    $row['material_number'] = $breakdown[$j]->material_child;
                    $row['store'] = $assy[$i]->store;
                    $row['location'] = $assy[$i]->location;
                    $row['quantity'] = $assy[$i]->final_count * ($breakdown[$j]->usage / $breakdown[$j]->divider);
                    $row['created_at'] = Carbon::now();
                    $row['updated_at'] = Carbon::now();

                    $this->assy_output[] = $row;
                }
            }
        }

        while (count($this->cek) > 0) {
            $this->breakdown();
        }

        foreach (array_chunk($this->assy_output, 1000) as $t) {
            $output = StocktakingOutput::insert($t);
        }
    }

    public function countPIAssyNew3()
    {
        $assy = StocktakingList::where('category', 'ASSY')
            ->where('final_count', '>', 0)
            ->get();

        $bom = db::select("SELECT b.material_parent, b.material_child, b.`usage`, b.divider, m.spt, ( b.`usage` / b.divider ) AS koef FROM bom_outputs b
         LEFT JOIN material_plant_data_lists m ON m.material_number = b.material_child");

        for ($i = 0; $i < count($assy); $i++) {
            for ($j = 0; $j < count($bom); $j++) {
                if ($assy[$i]->material_number == $bom[$j]->material_parent) {
                    if ($bom[$j]->spt == 50) {
                        $row = array();
                        $row['material_number'] = $bom[$j]->material_child;
                        $row['store'] = $assy[$i]->store;
                        $row['location'] = $assy[$i]->location;
                        $row['quantity'] = $assy[$i]->final_count * $bom[$j]->koef;
                        $row['created_at'] = Carbon::now();
                        $row['updated_at'] = Carbon::now();

                        $this->cek[] = $row;
                    } else {
                        $row = array();
                        $row['material_number'] = $bom[$i]->material_child;
                        $row['store'] = $assy[$i]->store;
                        $row['location'] = $assy[$i]->location;
                        $row['quantity'] = $assy[$i]->final_count * $bom[$j]->koef;
                        $row['created_at'] = Carbon::now();
                        $row['updated_at'] = Carbon::now();

                        $this->assy_output[] = $row;

                    }
                }
            }
        }

        dd($this->cek);

        // while(count($this->cek) > 0) {
        //     foreach (array_chunk($this->assy_output,1000) as $t) {
        //         $output = StocktakingOutput::insert($t);
        //     }
        //     $this->assy_output = array();

        //     $this->breakdownNew3($bom);
        // }
    }

    public function breakdownNew3($bom)
    {

        $this->temp = array();

        for ($i = 0; $i < count($this->cek); $i++) {
            for ($j = 0; $j < count($bom); $j++) {
                if ($bom[$j]->material_parent == $this->cek[$i]['material_number']) {

                    if ($bom[$j]->spt == 50) {
                        $row = array();
                        $row['material_number'] = $bom[$j]->material_child;
                        $row['store'] = $this->cek[$i]['store'];
                        $row['location'] = $this->cek[$i]['location'];
                        $row['quantity'] = $this->cek[$i]['quantity'] * $bom[$j]->koef;
                        $row['created_at'] = Carbon::now();
                        $row['updated_at'] = Carbon::now();
                        $this->temp[] = $row;
                    } else {
                        $row = array();
                        $row['material_number'] = $breakdown[$j]->material_child;
                        $row['store'] = $this->cek[$i]['store'];
                        $row['location'] = $this->cek[$i]['location'];
                        $row['quantity'] = $this->cek[$i]['quantity'] * $bom[$j]->koef;
                        $row['created_at'] = Carbon::now();
                        $row['updated_at'] = Carbon::now();
                        $this->assy_output[] = $row;
                    }
                }
            }
        }

        $this->cek = array();
        $this->cek = $this->temp;
    }

    public function printSummaryOfCounting(Request $request)
    {

        $store = '';
        if (strlen($request->get('store')) > 0) {
            $stores = explode(',', $request->get('store'));
            for ($i = 0; $i < count($stores); $i++) {
                $store = $store . "'" . $stores[$i] . "'";
                if ($i != (count($stores) - 1)) {
                    $store = $store . ',';
                }
            }
            $store = " WHERE s.store in (" . $store . ") ";
        }

        try {
            $lists = db::select("SELECT
                s.id,
                s.print_status,
                s.store,
                s.category,
                s.material_number,
                mpdl.material_description,
                m.`key`,
                m.model,
                m.surface,
                mpdl.bun,
                s.location,
                mpdl.storage_location,
                v.lot_completion,
                v.lot_transfer,
                IF
                ( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot
                FROM
                stocktaking_lists s
                LEFT JOIN materials m ON m.material_number = s.material_number
                LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
                LEFT JOIN material_volumes v ON v.material_number = s.material_number"
                . $store .
                "ORDER BY s.store, s.id ASC");

            $number = 0;
            $store = '';
            foreach ($lists as $list) {

                if ($list->store == $store) {
                    $number++;
                } else {
                    $store = $list->store;
                    $number = 1;
                }

                $print;
                if ($list->print_status == 1) {
                    $print = 'RP';
                } else {
                    $print = 'P';
                }

                $this->printSummary($list, $print, "MIS");
            }

            $response = array(
                'status' => true,
                'message' => 'Print Successful',
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

    }

    public function printStore($get_store)
    {

        $store = '';
        if (strlen($get_store) > 0) {
            $stores = explode(',', $get_store);
            for ($i = 0; $i < count($stores); $i++) {
                $store = $store . "'" . $stores[$i] . "'";
                if ($i != (count($stores) - 1)) {
                    $store = $store . ',';
                }
            }
            $store = " WHERE s.store in (" . $store . ") ";
        }

        // $data = db::select("SELECT IF (l.area = 'ST', 'SURFACE TREATMENT', l.area) AS area, s.location, s.store FROM
        //     (SELECT DISTINCT location, store FROM stocktaking_lists) s
        //     LEFT JOIN storage_locations l
        //     ON s.location = l.storage_location
        //     ".$store."
        //     ORDER BY
        //     l.area DESC,
        //     s.location ASC,
        //     s.store ASC");

        $data = db::select("SELECT DISTINCT IF (l.area = 'ST', 'SURFACE TREATMENT', l.area) AS area, s.store FROM
         (SELECT DISTINCT location, store FROM stocktaking_new_lists) s
         LEFT JOIN storage_locations l
         ON s.location = l.storage_location
         " . $store . "
         ORDER BY
         l.area DESC,
         s.store ASC");

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        $pdf->loadView('stocktakings.monthly.store_label', array(
            'data' => $data,
        ));
        return $pdf->stream("store.pdf");
    }

    public function reprintStoreSoc(Request $request)
    {
        $store = $request->get('store');

        try {
            $lists = db::select("SELECT
                s.id,
                s.store,
                s.category,
                s.material_number,
                mpdl.material_description,
                m.`key`,
                m.model,
                m.surface,
                mpdl.bun,
                s.location,
                mpdl.storage_location,
                v.lot_completion,
                v.lot_transfer,
                IF
                ( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot
                FROM
                stocktaking_lists s
                LEFT JOIN materials m ON m.material_number = s.material_number
                LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
                LEFT JOIN material_volumes v ON v.material_number = s.material_number
                WHERE s.store = '" . $store . "'
                ORDER BY s.store, s.id ASC");

            $number = 0;

            foreach ($lists as $list) {
                $number++;
                $this->printSummary($list, $number);
            }

            $response = array(
                'status' => true,
                'message' => 'Print Successful',
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

    }

    public function reprintIdSoc(Request $request)
    {
        $id = $request->get('id');
        $printer_name = $request->get('printer_name');

        try {

            $whereID = '';
            $list_id = array();
            for ($i = 0; $i < count($id); $i++) {
                array_push($list_id, $id[$i][0]);
                $whereID = $whereID . "'" . $id[$i][0] . "'";
                if ($i != (count($id) - 1)) {
                    $whereID = $whereID . ',';
                }
            }

            $lists = db::select("SELECT
                s.id,
                s.store,
                s.sub_store,
                s.category,
                s.material_number,
                mpdl.material_description,
                m.`key`,
                m.model,
                m.surface,
                mpdl.bun,
                s.location,
                mpdl.storage_location,
                v.lot_completion,
                v.lot_transfer,
                IF
                ( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot
                FROM
                stocktaking_new_lists s
                LEFT JOIN materials m ON m.material_number = s.material_number
                LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
                LEFT JOIN material_volumes v ON v.material_number = s.material_number
                WHERE s.id in (" . $whereID . ")
                ORDER BY s.location, s.store, s.category, s.material_number ASC");

            $update = StocktakingNewList::whereIn('id', $list_id)->update(['print_status' => 1]);

            // $stores = StocktakingList::where('store', $lists[0]->store)->get();

            // $number = 0;
            // foreach ($stores as $store) {
            //     $number++;
            //     if($store->id == $lists[0]->id){
            //         break;
            //     }
            // }

            $index = 0;
            foreach ($lists as $list) {
                $this->printSummary($list, $id[$index][1], $printer_name);
                $index++;
            }

            $response = array(
                'status' => true,
                'message' => 'Print Successful',
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

    }

    public function printSummary($list, $print, $printer_name)
    {
        // $printer_name = 'MIS';
        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        $id = 'ST_' . $list->id;
        $store = $list->store;
        $sub_store = $list->sub_store;
        $category = '(' . $list->category . ')';
        $material_number = $list->material_number;
        $sloc = $list->location;
        $description = $list->material_description;
        $key = $list->key;
        $model = $list->model;
        $surface = $list->surface;
        $uom = $list->bun;
        $lot = $list->lot;

        $stocktaking = StocktakingCalendar::where('status', 'planned')->first();

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->setReverseColors(true);
        $printer->setTextSize(2, 2);
        $printer->text("  Summary of Counting  " . "\n");
        $printer->initialize();
        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("STORE :\n");
        $printer->initialize();
        $printer->setTextSize(2, 2);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($store . "\n");
        $printer->initialize();
        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("SUBSTORE :\n");
        $printer->initialize();
        $printer->setTextSize(2, 2);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($sub_store . "\n");
        $printer->initialize();
        $printer->setTextSize(3, 2);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        if ($list->category == 'ASSY') {
            $printer->setReverseColors(true);
        }

        $printer->text($category . "\n");
        $printer->qrCode($id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($id . "\n");

        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(3, 2);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($material_number . " (" . $sloc . ")\n\n");

        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(1, 1);
        $printer->text($description . "\n");
        // if($model != '' || $key != '' || $surface != ''){
        //     $printer->text($model." - ".$key." - ".$surface."\n");
        // }
        if (strlen($lot) == 0) {
            $printer->text("Uom: " . $uom . "\n");
        } else {
            $printer->text("Lot: " . $lot . " " . $uom . "\n");
        }

        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);

        $printer->setReverseColors(true);
        $printer->textRaw("          HITUNG        " . "|" . "         REVISI        ");
        $printer->setReverseColors(false);
        $printer->textRaw(str_repeat(" ", 12) . "x" . str_repeat(" ", 11) . " " . str_repeat(" ", 11) . "x" . str_repeat(" ", 12));
        $printer->textRaw("\xc0" . str_repeat("\xc4", 21) . "\xd9 \xc0" . str_repeat("\xc4", 21) . "\xd9\n");
        $printer->textRaw(str_repeat(" ", 12) . "x" . str_repeat(" ", 11) . " " . str_repeat(" ", 11) . "x" . str_repeat(" ", 12));
        $printer->textRaw("\xc0" . str_repeat("\xc4", 21) . "\xd9 \xc0" . str_repeat("\xc4", 21) . "\xd9\n");
        $printer->textRaw(str_repeat(" ", 12) . "x" . str_repeat(" ", 11) . " " . str_repeat(" ", 11) . "x" . str_repeat(" ", 12));
        $printer->textRaw("\xc0" . str_repeat("\xc4", 21) . "\xd9 \xc0" . str_repeat("\xc4", 21) . "\xd9\n");
        $printer->textRaw(str_repeat(" ", 12) . "x" . str_repeat(" ", 11) . " " . str_repeat(" ", 11) . "x" . str_repeat(" ", 12));
        $printer->textRaw("\xc0" . str_repeat("\xc4", 21) . "\xd9 \xc0" . str_repeat("\xc4", 21) . "\xd9\n");
        $printer->textRaw(str_repeat(" ", 12) . "x" . str_repeat(" ", 11) . " " . str_repeat(" ", 11) . "x" . str_repeat(" ", 12));
        $printer->textRaw("\xc0" . str_repeat("\xc4", 21) . "\xd9 \xc0" . str_repeat("\xc4", 21) . "\xd9\n");

        // $printer->textRaw(str_repeat(" ", 24)."X".str_repeat(" ", 24));
        // $printer->textRaw("\xc0".str_repeat("\xc4", 45)."\xd9\n");
        // $printer->textRaw(str_repeat(" ", 24)."X".str_repeat(" ", 24));
        // $printer->textRaw("\xc0".str_repeat("\xc4", 45)."\xd9\n");
        // $printer->textRaw(str_repeat(" ", 24)."X".str_repeat(" ", 24));
        // $printer->textRaw("\xc0".str_repeat("\xc4", 45)."\xd9\n");
        // $printer->textRaw(str_repeat(" ", 24)."X".str_repeat(" ", 24));
        // $printer->textRaw("\xc0".str_repeat("\xc4", 45)."\xd9\n");
        // $printer->textRaw(str_repeat(" ", 24)."X".str_repeat(" ", 24));
        // $printer->textRaw("\xc0".str_repeat("\xc4", 45)."\xd9\n");

        // $printer->setReverseColors(true);
        // $printer->text('                   REVISI                   '."\n");
        // $printer->setReverseColors(false);
        // $printer->textRaw(str_repeat(" ", 24)."X".str_repeat(" ", 24));
        // $printer->textRaw("\xc0".str_repeat("\xc4", 45)."\xd9\n");
        // $printer->textRaw(str_repeat(" ", 24)."X".str_repeat(" ", 24));
        // $printer->textRaw("\xc0".str_repeat("\xc4", 45)."\xd9\n");
        // $printer->textRaw(str_repeat(" ", 24)."X".str_repeat(" ", 24));
        // $printer->textRaw("\xc0".str_repeat("\xc4", 45)."\xd9\n");
        // $printer->textRaw(str_repeat(" ", 24)."X".str_repeat(" ", 24));
        // $printer->textRaw("\xc0".str_repeat("\xc4", 45)."\xd9\n");
        // $printer->textRaw(str_repeat(" ", 24)."X".str_repeat(" ", 24));
        // $printer->textRaw("\xc0".str_repeat("\xc4", 45)."\xd9\n");

        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_RIGHT);
        if ($print == 'P') {
            $printer->text("Print at " . Carbon::now());
        } else {
            $printer->text("Reprint at " . Carbon::now());
        }
        $printer->feed(1);
        $printer->cut();
        $printer->close();
    }

    public function printSummaryBackup($list)
    {
        $printer_name = 'MIS';
        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        // $id = '136';
        // $store = 'SUBASSY-CL-2B';
        // $category = '(ASSY)';
        // $material_number = 'W528860';
        // $sloc = 'CL91';
        // $description = 'CL-250N 7 ASSY CORK&PAD PACKED(YMPI) J';
        // $key = '7';
        // $model = 'CL250';
        // $surface = 'NICKEL';
        // $uom = 'PC';
        // $lot = '';

        $id = $list->id;
        $store = $list->store;
        $category = '(' . $list->category . ')';
        $material_number = $list->material_number;
        $sloc = $list->location;
        $description = $list->material_description;
        $key = $list->key;
        $model = $list->model;
        $surface = $list->surface;
        $uom = $list->bun;
        $lot = $list->lot;

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->setReverseColors(true);
        $printer->setTextSize(2, 2);
        $printer->text("  Summary of Counting  " . "\n");
        $printer->initialize();
        $printer->setTextSize(3, 3);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($store . "\n");
        if ($list->category == 'ASSY') {
            $printer->setReverseColors(true);
        }
        $printer->text($category . "\n");
        $printer->feed(1);
        $printer->qrCode($id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
        $printer->feed(1);
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(4, 2);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($material_number . "\n");
        $printer->text($sloc . "\n\n");
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(2, 1);
        $printer->text($description . "\n");
        $printer->feed(1);
        $printer->text($model . "-" . $key . "-" . $surface . "\n");
        if (strlen($lot) == 0) {
            $printer->text("Lot: \xDB\xDB " . $uom . "\n");
            $printer->textRaw("\xda" . str_repeat("\xc4", 22) . "\xbf\n");
            $printer->textRaw("\xb3Lot:" . str_repeat("\xDB", 18) . "\xb3\n");
            $printer->textRaw("\xc0" . str_repeat("\xc4", 22) . "\xd9\n");
        } else {
            $printer->text("Lot: " . $lot . " " . $uom . "\n");
            $printer->textRaw("\xda" . str_repeat("\xc4", 22) . "\xbf\n");
            $printer->textRaw("\xb3Lot:" . str_repeat(" ", 18) . "\xb3\n");
            $printer->textRaw("\xc0" . str_repeat("\xc4", 22) . "\xd9\n");
        }
        $printer->textRaw("\xda" . str_repeat("\xc4", 22) . "\xbf\n");
        $printer->textRaw("\xb3Z1 :" . str_repeat(" ", 18) . "\xb3\n");
        $printer->textRaw("\xc0" . str_repeat("\xc4", 22) . "\xd9\n");
        $printer->feed(1);
        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_RIGHT);
        $printer->text(Carbon::now() . "\n");
        $printer->feed(1);
        $printer->cut();
        $printer->close();
    }

    public function reprintIdSubStore($id)
    {
        $ids = explode(",", $id);

        // try {

        $whereID = '';
        $list_id = array();
        for ($i = 0; $i < count($ids); $i++) {
            array_push($list_id, $ids[$i]);
            $whereID = $whereID . "'" . $ids[$i] . "'";
            if ($i != (count($ids) - 1)) {
                $whereID = $whereID . ',';
            }
        }
        DB::connection()->enableQueryLog();

        $lists = db::select("SELECT
         s.id,
         s.store,
         s.sub_store,
         s.category,
         s.material_number,
         mpdl.material_description,
         m.`key`,
         m.model,
         m.surface,
         mpdl.bun,
         s.location,
         mpdl.storage_location,
         v.lot_completion,
         v.lot_transfer,
         IF
         ( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot
         FROM
         stocktaking_new_lists s
         LEFT JOIN materials m ON m.material_number = s.material_number
         LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
         LEFT JOIN material_volumes v ON v.material_number = s.material_number
         WHERE s.id in (" . $whereID . ")
         ORDER BY s.location, s.store, s.sub_store, s.category, s.material_number ASC");

        $update = StocktakingNewList::whereIn('id', $list_id)->update(['print_status' => 1]);

        // $stores = StocktakingList::where('store', $lists[0]->store)->get();

        // $number = 0;
        // foreach ($stores as $store) {
        //     $number++;
        //     if($store->id == $lists[0]->id){
        //         break;
        //     }
        // }

        // $index = 0;
        // foreach ($lists as $list) {
        //     $this->printSummary($list, $id[$index][1], $printer_name);
        //     $index++;
        // }

        // foreach ($lists as $key) {
        //     $qr_code = base64_encode(QrCode::format('svg')->size(100)->errorCorrection('H')->generate($key->id));

        //     array_push($arr_qr, $qr_code);
        // }

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        if ($lists[0]->material_number == 'W934330' && $lists[0]->location == 'FL51') {
            $pdf->loadView('stocktakings.monthly.print_substore_ag', array(
                'lists' => $lists,
                // 'qr_code' => $arr_qr,
                'query' => DB::getQueryLog(),
            ));

            return $pdf->stream($lists[0]->sub_store . "_" . $lists[0]->store . ".pdf");
        } else if ($lists[0]->material_number == 'ZN93390' && $lists[0]->location == 'FL51') {
            $pdf->loadView('stocktakings.monthly.print_substore_gold', array(
                'lists' => $lists,
                // 'qr_code' => $arr_qr,
                'query' => DB::getQueryLog(),
            ));

            return $pdf->stream($lists[0]->sub_store . "_" . $lists[0]->store . ".pdf");
        } else {
            $pdf->loadView('stocktakings.monthly.print_substore', array(
                'lists' => $lists,
                // 'qr_code' => $arr_qr,
                'query' => DB::getQueryLog(),
            ));

            return $pdf->stream($lists[0]->sub_store . "_" . $lists[0]->store . ".pdf");
        }

        // return view('stocktakings.print_substore', array(
        //     'lists' => $lists
        // ));

    }

    public function exportOfficailVariance(Request $request)
    {

        $month = $request->get('month_official_variance');
        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

        if ($calendar->status == 'finished') {
            $variances = db::select("SELECT plnt, `group`, location, sum(pi_amt) AS sumof_pi_amt, sum(book_amt) AS sumof_book_amt, sum(diff_amt) AS sumof_diff_amt, sum(var_amt_min) AS sumof_var_amt_min, sum(var_amt_plus) AS sumof_var_amt_plus, sum(var_amt_abs) AS sumof_var_amt_abs, sum(var_amt_abs)/sum(book_amt)*100 AS percentage FROM
                (SELECT storage_locations.area AS `group`,
                storage_locations.plnt,
                pi_book.material_number,
                pi_book.location,
                ROUND((material_plant_data_lists.standard_price/1000), 5) AS std,
                pi_book.pi AS pi,
                pi_book.book AS book,
                (pi_book.pi - pi_book.book) AS diff_qty,
                (ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.pi) AS pi_amt,
                (ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.book) AS book_amt,
                (ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) AS diff_amt,
                if((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) < 0, ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))), 0) AS var_amt_min,
                if((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) > 0, (ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)), 0) AS var_amt_plus,
                ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))) AS var_amt_abs
                FROM
                (SELECT location, material_number, sum(pi) AS pi, sum(book) AS book FROM
                (SELECT location, material_number, sum(quantity) AS pi, 0 AS book FROM stocktaking_output_logs
                WHERE stocktaking_date = '" . $calendar->date . "'
                GROUP BY location, material_number
                UNION ALL
                SELECT storage_location AS location, material_number, 0 as pi, sum(unrestricted) AS book FROM storage_location_stocks
                WHERE stock_date = '" . $calendar->date . "'
                GROUP BY storage_location, material_number) AS union_pi_book
                GROUP BY location, material_number) AS pi_book
                LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = pi_book.material_number
                LEFT JOIN storage_locations ON storage_locations.storage_location = pi_book.location
                WHERE storage_locations.area IS NOT NULL
                AND pi_book.location NOT IN ('WCJR','WSCR','MSCR','YCJP','401','MMJR')
                ) AS official_variance
                GROUP BY plnt, `group`, location");

            // AND pi_book.location NOT IN ('WCJR','WSCR','MSCR','YCJP','401','PSTK','203','208','214','216','217','MMJR')

        } else {
            $variances = db::select("SELECT plnt, `group`, location, sum(pi_amt) AS sumof_pi_amt, sum(book_amt) AS sumof_book_amt, sum(diff_amt) AS sumof_diff_amt, sum(var_amt_min) AS sumof_var_amt_min, sum(var_amt_plus) AS sumof_var_amt_plus, sum(var_amt_abs) AS sumof_var_amt_abs, sum(var_amt_abs)/sum(book_amt)*100 AS percentage FROM
                (SELECT storage_locations.area AS `group`,
                storage_locations.plnt,
                pi_book.material_number,
                pi_book.location,
                ROUND((material_plant_data_lists.standard_price/1000), 5) AS std,
                pi_book.pi AS pi,
                pi_book.book AS book,
                (pi_book.pi - pi_book.book) AS diff_qty,
                (ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.pi) AS pi_amt,
                (ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.book) AS book_amt,
                (ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) AS diff_amt,
                if((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) < 0, ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))), 0) AS var_amt_min,
                if((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) > 0, (ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)), 0) AS var_amt_plus,
                ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))) AS var_amt_abs
                FROM
                (SELECT location, material_number, sum(pi) AS pi, sum(book) AS book FROM
                (SELECT location, material_number, sum(quantity) AS pi, 0 as book FROM stocktaking_outputs
                GROUP BY location, material_number
                UNION ALL
                SELECT storage_location AS location, material_number, 0 as pi, sum(unrestricted) AS book FROM stocktaking_location_stocks
                WHERE stock_date = '" . $calendar->date . "'
                GROUP BY storage_location, material_number) AS union_pi_book
                GROUP BY location, material_number) AS pi_book
                LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = pi_book.material_number
                LEFT JOIN storage_locations ON storage_locations.storage_location = pi_book.location
                WHERE storage_locations.area IS NOT NULL
                AND pi_book.location NOT IN ('WCJR','WSCR','MSCR','YCJP','401','MMJR')
                ) AS official_variance
                GROUP BY plnt, `group`, location");
        }

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        $pdf->loadView('stocktakings.monthly.report.official_variance_pdf', array(
            'variances' => $variances,
        ));

        // $pdf = PDF::loadview('qc_report.print_cpar',['cpars'=>$cpars,'parts'=>$parts]);
        return $pdf->stream("OFFICIAL_VARIANCE_STOCKTAKING "+$month+".pdf");

    }

    public function exportInquiry(Request $request)
    {

        $month = $request->get('month_inquiry');
        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

        if ($calendar->status == 'finished') {
            $inquiries = db::select("SELECT
                stocktaking_inquiry_logs.id,
                stocktaking_inquiry_logs.location,
                storage_locations.area AS `group`,
                stocktaking_inquiry_logs.store,
                stocktaking_inquiry_logs.material_number,
                material_plant_data_lists.material_description,
                stocktaking_inquiry_logs.category,
                material_plant_data_lists.bun,
                stocktaking_inquiry_logs.quantity as final_count,
                date_format( stocktaking_inquiry_logs.updated_at, '%d-%M-%y' ) AS updated_at
                FROM
                stocktaking_inquiry_logs
                LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = stocktaking_inquiry_logs.material_number
                LEFT JOIN storage_locations ON storage_locations.storage_location = stocktaking_inquiry_logs.location
                WHERE stocktaking_inquiry_logs.stocktaking_date = '" . $calendar->date . "'
                ORDER BY storage_locations.area, stocktaking_inquiry_logs.location, stocktaking_inquiry_logs.material_number ASC");
        } else {
            $inquiries = db::select("SELECT
                stocktaking_lists.id,
                stocktaking_lists.location,
                storage_locations.area AS `group`,
                stocktaking_lists.store,
                stocktaking_lists.material_number,
                material_plant_data_lists.material_description,
                stocktaking_lists.category,
                material_plant_data_lists.bun,
                stocktaking_lists.final_count,
                date_format( stocktaking_lists.updated_at, '%d-%M-%y' ) AS updated_at
                FROM
                stocktaking_lists
                LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = stocktaking_lists.material_number
                LEFT JOIN storage_locations ON storage_locations.storage_location = stocktaking_lists.location
                where stocktaking_lists.print_status = 1
                ORDER BY storage_locations.area, stocktaking_lists.location, stocktaking_lists.material_number ASC");
        }

        $title = 'Inquiry' . str_replace('-', '', $month) . '_(' . date('ymd H.i') . ')';

        $data = array(
            'inquiries' => $inquiries,
        );

        ob_clean();
        Excel::create($title, function ($excel) use ($data) {
            $excel->sheet('Inquiry', function ($sheet) use ($data) {
                return $sheet->loadView('stocktakings.monthly.report.inquiry_excel', $data);
            });
        })->export('xlsx');

    }

    public function exportInquiryNew(Request $request)
    {

        $month = $request->get('month_inquiry');
        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

        if ($calendar->status == 'finished') {
            $inquiries = db::select("SELECT
                stocktaking_inquiry_logs.id,
                stocktaking_inquiry_logs.location,
                storage_locations.area AS `group`,
                stocktaking_inquiry_logs.store,
                stocktaking_inquiry_logs.material_number,
                material_plant_data_lists.material_description,
                stocktaking_inquiry_logs.category,
                material_plant_data_lists.bun,
                stocktaking_inquiry_logs.quantity as final_count,
                date_format( stocktaking_inquiry_logs.updated_at, '%d-%M-%y' ) AS updated_at
                FROM
                stocktaking_inquiry_logs
                LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = stocktaking_inquiry_logs.material_number
                LEFT JOIN storage_locations ON storage_locations.storage_location = stocktaking_inquiry_logs.location
                WHERE stocktaking_inquiry_logs.stocktaking_date = '" . $calendar->date . "'
                ORDER BY storage_locations.area, stocktaking_inquiry_logs.location, stocktaking_inquiry_logs.material_number ASC");
        } else {
            $inquiries = db::select("SELECT
                stocktaking_new_lists.id,
                stocktaking_new_lists.location,
                storage_locations.area AS `group`,
                stocktaking_new_lists.store,
                stocktaking_new_lists.sub_store,
                stocktaking_new_lists.material_number,
                material_plant_data_lists.material_description,
                stocktaking_new_lists.category,
                material_plant_data_lists.bun,
                stocktaking_new_lists.final_count,
                date_format( stocktaking_new_lists.updated_at, '%d-%M-%y' ) AS updated_at
                FROM
                stocktaking_new_lists
                LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = stocktaking_new_lists.material_number
                LEFT JOIN storage_locations ON storage_locations.storage_location = stocktaking_new_lists.location
                where stocktaking_new_lists.print_status = 1
                and stocktaking_new_lists.final_count > 0
                ORDER BY storage_locations.area, stocktaking_new_lists.location, stocktaking_new_lists.material_number ASC");
        }

        $title = 'Inquiry' . str_replace('-', '', $month) . '_(' . date('ymd H.i') . ')';

        $data = array(
            'inquiries' => $inquiries,
        );

        ob_clean();
        Excel::create($title, function ($excel) use ($data) {
            $excel->sheet('Inquiry', function ($sheet) use ($data) {
                return $sheet->loadView('stocktakings.monthly.report.inquiry_excel', $data);
            });
        })->export('xlsx');

    }

    public function exportVariance(Request $request)
    {

        $month = $request->get('month_variance');
        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

        if ($calendar->status == 'finished') {
            $variances = db::select("SELECT
                storage_locations.area AS `group`,
                storage_locations.plnt,
                material_plant_data_lists.valcl,
                pi_book.material_number,
                material_plant_data_lists.material_description,
                pi_book.location,
                storage_locations.location AS location_name,
                material_plant_data_lists.bun AS uom,
                ROUND((material_plant_data_lists.standard_price/1000), 5) AS std,
                pi_book.pi AS pi,
                pi_book.book AS book,
                (pi_book.pi - pi_book.book) AS diff_qty,
                (ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.pi) AS pi_amt,
                (ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.book) AS book_amt,
                (ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) AS diff_amt,
                if((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) < 0, ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))), 0) AS var_amt_min,
                if((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) > 0, (ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)), 0) AS var_amt_plus,
                ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))) AS var_amt_abs,
                stocktaking_material_notes.note
                FROM
                (SELECT location, material_number, sum(pi) AS pi, sum(book) AS book FROM
                (SELECT location, material_number, sum(quantity) AS pi, 0 AS book FROM stocktaking_output_logs
                WHERE stocktaking_date = '" . $calendar->date . "'
                GROUP BY location, material_number
                UNION ALL
                SELECT storage_location AS location, material_number, 0 as pi, sum(unrestricted) AS book FROM storage_location_stocks
                WHERE stock_date = '" . $calendar->date . "'
                GROUP BY storage_location, material_number) AS union_pi_book
                GROUP BY location, material_number) AS pi_book
                LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = pi_book.material_number
                LEFT JOIN storage_locations ON storage_locations.storage_location = pi_book.location
                LEFT JOIN stocktaking_material_notes ON stocktaking_material_notes.material_number = pi_book.material_number
                WHERE storage_locations.area is not null
                ORDER BY
                storage_locations.area,
                pi_book.location,
                pi_book.material_number ASC");

            // WHERE storage_locations.area is not null and pi_book.location not in ('203', '214', '216', '217', 'MSCR', 'WSCR')
            // and pi_book.location not in ('MSCR', 'WSCR')

        } else {
            $variances = db::select("SELECT
                storage_locations.area AS `group`,
                storage_locations.plnt,
                material_plant_data_lists.valcl,
                pi_book.material_number,
                material_plant_data_lists.material_description,
                pi_book.location,
                storage_locations.location AS location_name,
                material_plant_data_lists.bun AS uom,
                ROUND((material_plant_data_lists.standard_price/1000), 5) AS std,
                pi_book.pi AS pi,
                pi_book.book AS book,
                (pi_book.pi - pi_book.book) AS diff_qty,
                (ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.pi) AS pi_amt,
                (ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.book) AS book_amt,
                (ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) AS diff_amt,
                if((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) < 0, ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))), 0) AS var_amt_min,
                if((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)) > 0, (ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book)), 0) AS var_amt_plus,
                ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))) AS var_amt_abs,
                stocktaking_material_notes.note
                FROM
                (SELECT location, material_number, ROUND(sum(pi),3) AS pi, ROUND(sum(book),3) AS book FROM
                (SELECT location, material_number, sum(quantity) AS pi, 0 as book FROM stocktaking_outputs
                GROUP BY location, material_number
                UNION ALL
                SELECT storage_location AS location, material_number, 0 as pi, sum(unrestricted) AS book FROM stocktaking_location_stocks
                WHERE stock_date = '" . $calendar->date . "'
                GROUP BY storage_location, material_number) AS union_pi_book
                GROUP BY location, material_number) AS pi_book
                LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = pi_book.material_number
                LEFT JOIN storage_locations ON storage_locations.storage_location = pi_book.location
                LEFT JOIN stocktaking_material_notes ON stocktaking_material_notes.material_number = pi_book.material_number
                WHERE storage_locations.area is not null
                ORDER BY
                storage_locations.area,
                pi_book.location,
                pi_book.material_number ASC");

            // and storage_location not in ('MSCR', 'WSCR', 'MMJR')

        }

        // foreach ($variances as $variance) {
        //     if($variance->std == 0){
        //         return redirect('index/stocktaking/menu')->with('error', $month.'(ime)'.'Standart Price '.$variance->material_number.' is 0')->with('page', 'Monthly Stock Taking')->with('head', 'Stocktaking');
        //     }

        // }

        $title = 'VarianceReport' . str_replace('-', '', $month) . '_(' . date('ymd H.i') . ')';

        $data = array(
            'variances' => $variances,
        );

        ob_clean();
        Excel::create($title, function ($excel) use ($data) {
            $excel->sheet('Variance', function ($sheet) use ($data) {
                return $sheet->loadView('stocktakings.monthly.report.variance_excel', $data);
            });
        })->export('xlsx');
    }

    public function exportUploadSAP(Request $request)
    {
        $month = $request->get('month');
        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

        if ($calendar) {
            // $filename = 'ympipi_upload_' . $date . '.txt';
            $filename = 'ympipi_upload_' . str_replace('-', '', $calendar->date) . '.txt';
            $filepath = public_path() . "/uploads/sap/stocktaking/" . $filename;
            $filedestination = "ma/ympipi/" . $filename;

            $datas = db::select("SELECT storage_locations.area AS `group`,
                storage_locations.plnt,
                pi_book.location,
                storage_locations.cost_center,
                pi_book.material_number,
                pi_book.pi AS pi,
                pi_book.book AS book,
                (pi_book.pi - pi_book.book) AS diff_qty,
                ROUND(ABS(pi_book.pi - pi_book.book),3) AS diff_abs,
                if((pi_book.pi - pi_book.book) > 0, '9671003', '9681003') AS type
                FROM
                (SELECT location, material_number, sum(pi) AS pi, sum(book) AS book FROM
                (SELECT location, material_number, sum(quantity) AS pi, 0 as book FROM stocktaking_outputs
                GROUP BY location, material_number
                UNION ALL
                SELECT storage_location AS location, material_number, 0 as pi, sum(unrestricted) AS book FROM stocktaking_location_stocks
                WHERE stock_date = '" . $calendar->date . "'
                GROUP BY storage_location, material_number) AS union_pi_book
                GROUP BY location, material_number) AS pi_book
                LEFT JOIN storage_locations ON storage_locations.storage_location = pi_book.location
                WHERE storage_locations.area IS NOT NULL
                AND pi_book.location NOT IN ('WCJR','WSCR','MSCR','YCJP','401','MMJR')
                AND ROUND(ABS(pi_book.pi - pi_book.book),3) > 0");

            // AND pi_book.location NOT IN ('WCJR','WSCR','MSCR','YCJP','401','203','208','214','216','217','MMJR')

            // $datas = db::select("SELECT storage_locations.area AS `group`,
            //     storage_locations.plnt,
            //     pi_book.location,
            //     storage_locations.cost_center,
            //     pi_book.material_number,
            //     pi_book.pi AS pi,
            //     pi_book.book AS book,
            //     (pi_book.pi - pi_book.book) AS diff_qty,
            //     ROUND(ABS(pi_book.pi - pi_book.book),3) AS diff_abs,
            //     if((pi_book.pi - pi_book.book) > 0, '9671003', '9681003') AS type
            //     FROM stocktaking_ftp_files AS pi_book
            //     LEFT JOIN storage_locations ON storage_locations.storage_location = pi_book.location
            //     WHERE storage_locations.area IS NOT NULL
            //     AND pi_book.location NOT IN ('WCJR','WSCR','MSCR','YCJP','401','MMJR')
            //     AND ROUND(ABS(pi_book.pi - pi_book.book),3) > 0");

            $upload_text = "";
            $count = count($datas);
            $actual = 1;
            foreach ($datas as $data) {
                $upload_text .= $this->writeString($data->plnt, 15, " ");
                $upload_text .= $this->writeString($data->plnt, 4, " ");
                $upload_text .= $this->writeString($data->material_number, 18, " ");
                $upload_text .= $this->writeString($data->location, 4, " ");
                $upload_text .= $this->writeString($data->plnt, 4, " ");
                $upload_text .= $this->writeString($data->location, 4, " ");
                $upload_text .= $this->writeDecimal(round($data->diff_abs, 3), 13, "0");
                $upload_text .= $this->writeStringReserve($data->cost_center, 10, "0");
                $upload_text .= $this->writeString('', 10, " ");
                $upload_text .= $this->writeDate($calendar->date, "transfer");
                $upload_text .= $this->writeString('MB1C', 20, " ");
                $upload_text .= $data->type;

                if ($actual < $count) {
                    $upload_text .= "\r\n";
                }
                $actual++;
            }

            try {
                File::put($filepath, $upload_text);
                $success = self::uploadFTP($filepath, $filedestination);

                $response = array(
                    'status' => true,
                );
                return Response::json($response);
            } catch (\Exception$e) {

                $error_log = new ErrorLog([
                    'error_message' => $e->getMessage(),
                    'created_by' => '1',
                ]);
                $error_log->save();

                $response = array(
                    'status' => false,
                );
                return Response::json($response);
            }
        }

    }

    public function exportLog(Request $request)
    {
        $month = $request->get('month');

        try {
            $calendar = StocktakingCalendar::where(db::raw('date_format(date, "%Y-%m")'), $month)
                ->update([
                    'status' => 'finished',
                ]);

            $lists = StocktakingList::get();
            $outputs = StocktakingOutput::select('material_number', 'store', 'location', db::raw('sum(quantity) as quantity'))
                ->groupBy('material_number', 'store', 'location')
                ->get();

            $calendar = StocktakingCalendar::where(db::raw('date_format(date, "%Y-%m")'), $month)->first();

            $insert_list = array();
            foreach ($lists as $list) {
                $row = array();

                $row['store'] = $list['store'];
                $row['category'] = $list['category'];
                $row['material_number'] = $list['material_number'];
                $row['location'] = $list['location'];
                $row['quantity'] = $list['quantity'];
                $row['stocktaking_date'] = $calendar->date;
                $row['created_at'] = Carbon::now();
                $row['updated_at'] = Carbon::now();

                $insert_list[] = $row;
            }
            foreach ($insert_list as $t) {
                $insert = StocktakingInquiryLog::updateOrCreate(
                    ['store' => $t['store'], 'category' => $t['category'], 'material_number' => $t['material_number'], 'stocktaking_date' => $t['stocktaking_date']],
                    ['location' => $t['location'], 'quantity' => $t['quantity'], 'updated_at' => Carbon::now()]
                );
            }

            $insert_output = array();
            foreach ($outputs as $output) {
                $row = array();

                $row['material_number'] = $output['material_number'];
                $row['store'] = $output['store'];
                $row['location'] = $output['location'];
                $row['quantity'] = $output['quantity'];
                $row['stocktaking_date'] = $calendar->date;
                $row['created_at'] = Carbon::now();
                $row['updated_at'] = Carbon::now();

                $insert_output[] = $row;
            }
            foreach ($insert_output as $t) {
                $insert = StocktakingOutputLog::updateOrCreate(
                    ['store' => $t['store'], 'material_number' => $t['material_number'], 'stocktaking_date' => $t['stocktaking_date']],
                    ['location' => $t['location'], 'quantity' => $t['quantity'], 'updated_at' => Carbon::now()]
                );
            }

            StocktakingOutput::truncate();
            StocktakingLocationStock::truncate();

            $list = StocktakingList::whereNotNull('created_by')
                ->update([
                    'remark' => 'USE',
                    'print_status' => 0,
                    'process' => 0,
                    'quantity' => null,
                    'audit1' => null,
                    'audit2' => null,
                    'final_count' => null,
                    'inputed_by' => null,
                    'audit1_by' => null,
                    'audit2_by' => null,
                    'revised_by' => null,
                    'reason' => null,
                ]);

            $response = array(
                'status' => true,
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
            );
            return Response::json($response);
        }

    }

    public function fetchCountFstk(Request $request)
    {

        // $temp_slip = db::select("SELECT flo_number AS slip, material_number, SUM(actual) AS quantity FROM flos
        //     WHERE `status` = 2
        //     GROUP BY flo_number, material_number
        //     UNION ALL
        //     SELECT knock_down_details.kd_number AS slip, knock_down_details.material_number, SUM(knock_down_details.quantity) AS quantity FROM knock_down_details
        //     LEFT JOIN knock_downs ON knock_downs.kd_number = knock_down_details.kd_number
        //     WHERE knock_downs.`status` = 2
        //     GROUP BY knock_down_details.kd_number, knock_down_details.material_number
        //     UNION ALL
        //     SELECT eo_number_sequence, material_number, SUM(quantity) AS quantity FROM extra_order_detail_sequences
        //     WHERE `status` = 2
        //     GROUP BY eo_number_sequence, material_number");

        $temp_slip = db::connection('ympimis_2')
            ->table('stocktaking_finish_goods')
            ->where('category', 'MIRAI')
            ->select(
                'remark AS slip',
                'material_number',
                db::raw('SUM(mirai) AS quantity')
            )
            ->groupBy(
                'remark',
                'material_number'
            )
            ->get();

        $stocktaking = db::connection('ympimis_2')
            ->table('stocktaking_finish_goods')
            ->select(
                'location',
                'material_number',
                'material_description',
                'remark',
                db::raw('SUM(ymes) AS ymes'),
                db::raw('SUM(mirai) AS mirai'),
                db::raw('SUM(pi) AS pi')
            )
            ->groupBy(
                'location',
                'material_number',
                'material_description',
                'remark'
            )
            ->get();

        $resume = [];
        for ($i = 0; $i < count($stocktaking); $i++) {
            $key = $stocktaking[$i]->material_number;
            if (!array_key_exists($key, $resume)) {
                $row = array();
                $row['location'] = $stocktaking[$i]->material_number;
                $row['material_number'] = $stocktaking[$i]->material_number;
                $row['material_description'] = $stocktaking[$i]->material_description;
                $row['ymes'] = $stocktaking[$i]->ymes;
                $row['pi'] = $stocktaking[$i]->pi;
                $resume[$key] = (object) $row;
            } else {
                $resume[$key]->ymes = $resume[$key]->ymes + $stocktaking[$i]->ymes;
                $resume[$key]->pi = $resume[$key]->pi + $stocktaking[$i]->pi;
            }
        }

        $slip_new = [];
        for ($i = 0; $i < count($temp_slip); $i++) {
            $pi = 0;
            for ($j = 0; $j < count($stocktaking); $j++) {
                if ($stocktaking[$j]->remark == $temp_slip[$i]->slip && $stocktaking[$j]->material_number == $temp_slip[$i]->material_number) {
                    $pi = $stocktaking[$j]->pi;
                    break;
                }
            }

            $category = 'FLO';
            if (str_contains($temp_slip[$i]->slip, 'KD')) {
                $category = 'KDO';
            } else if (str_contains($temp_slip[$i]->slip, 'EO')) {
                $category = 'EO';
            }

            $row = array();
            $row['category'] = $category;
            $row['slip'] = $temp_slip[$i]->slip;
            $row['material_number'] = $temp_slip[$i]->material_number;
            $row['mirai'] = $temp_slip[$i]->quantity;
            $row['pi'] = $pi;
            $slip_new[] = (object) $row;
        }

        $response = array(
            'status' => true,
            'resume' => $resume,
            'slip_new' => $slip_new,
        );
        return Response::json($response);

    }

    public function fetchCountScrap(Request $request)
    {
        $temp_slip = db::connection('ympimis_2')
            ->table('stocktaking_finish_goods')
            ->where('category', 'MIRAI')
            ->select(
                'remark AS slip',
                'material_number',
                db::raw('SUM(mirai) AS quantity')
            )
            ->groupBy(
                'remark',
                'material_number'
            )
            ->get();

        $stocktaking = db::connection('ympimis_2')
            ->table('stocktaking_finish_goods')
            ->select(
                'location',
                'material_number',
                'material_description',
                'remark',
                db::raw('SUM(ymes) AS ymes'),
                db::raw('SUM(mirai) AS mirai'),
                db::raw('SUM(pi) AS pi')
            )
            ->groupBy(
                'location',
                'material_number',
                'material_description',
                'remark'
            )
            ->get();

        $resume = [];
        for ($i = 0; $i < count($stocktaking); $i++) {
            $key = $stocktaking[$i]->material_number;
            if (!array_key_exists($key, $resume)) {
                $row = array();
                $row['location'] = $stocktaking[$i]->material_number;
                $row['material_number'] = $stocktaking[$i]->material_number;
                $row['material_description'] = $stocktaking[$i]->material_description;
                $row['ymes'] = $stocktaking[$i]->ymes;
                $row['pi'] = $stocktaking[$i]->pi;
                $resume[$key] = (object) $row;
            } else {
                $resume[$key]->ymes = $resume[$key]->ymes + $stocktaking[$i]->ymes;
                $resume[$key]->pi = $resume[$key]->pi + $stocktaking[$i]->pi;
            }
        }

        $slip_new = [];
        for ($i = 0; $i < count($temp_slip); $i++) {
            $pi = 0;
            for ($j = 0; $j < count($stocktaking); $j++) {
                if ($stocktaking[$j]->remark == $temp_slip[$i]->slip && $stocktaking[$j]->material_number == $temp_slip[$i]->material_number) {
                    $pi = $stocktaking[$j]->pi;
                    break;
                }
            }

            $category = 'FLO';
            if (str_contains($temp_slip[$i]->slip, 'KD')) {
                $category = 'KDO';
            } else if (str_contains($temp_slip[$i]->slip, 'EO')) {
                $category = 'EO';
            }

            $row = array();
            $row['category'] = $category;
            $row['slip'] = $temp_slip[$i]->slip;
            $row['material_number'] = $temp_slip[$i]->material_number;
            $row['mirai'] = $temp_slip[$i]->quantity;
            $row['pi'] = $pi;
            $slip_new[] = (object) $row;
        }

        $test = db::connection('ympimis_2')->select('select * from stocktaking_scraps');

        $test2 = db::select('select * from stocktaking_location_stocks where storage_location in ("MSCR", "WSCR", "MMJR", "OTHR") order by material_description asc');

        $jumlah_book = db::select('select ROUND(sum(unrestricted), 3) as jumlah from stocktaking_location_stocks where storage_location in ("MSCR", "WSCR", "MMJR", "OTHR")');
        $jumlah_pi = db::connection('ympimis_2')->select('select ROUND(sum(pi), 3) as jumlah from stocktaking_scraps');

        $response = array(
            'status' => true,
            'resume' => $resume,
            'slip_new' => $slip_new,
            'test' => $test,
            'test2' => $test2,
            'jumlah_book' => $jumlah_book,
            'jumlah_pi' => $jumlah_pi,
        );
        return Response::json($response);
    }

    public function fetchCheckSlip(Request $request)
    {

        $stocktaking = db::connection('ympimis_2')
            ->table('stocktaking_finish_goods')
            ->where('category', 'PI')
            ->where('remark', $request->get('slip'))
            ->get();

        if (count($stocktaking) > 0) {
            $response = array(
                'status' => false,
                'stocktaking' => $stocktaking,
                'message' => 'Slip sudah di input',
            );
            return Response::json($response);

        }

        if (str_contains($request->get('slip'), 'KD')) {
            $check = db::table('knock_downs')
                ->where('kd_number', $request->get('slip'))
                ->first();

            $data = db::table('knock_down_details')
                ->where('kd_number', $request->get('slip'))
                ->select(db::raw('kd_number AS slip'), 'material_number', db::raw('sum(quantity) AS quantity'))
                ->groupBy('kd_number', 'material_number')
                ->get();

        } else if (str_contains($request->get('slip'), 'EO')) {
            $check = db::table('extra_order_detail_sequences')
                ->where('eo_number_sequence', $request->get('slip'))
                ->first();

            $data = db::table('extra_order_detail_sequences')
                ->where('eo_number_sequence', $request->get('slip'))
                ->select(db::raw('eo_number_sequence AS slip'), 'material_number', db::raw('sum(quantity) AS quantity'))
                ->groupBy('eo_number_sequence', 'material_number')
                ->get();

        } else {
            $check = db::table('flos')
                ->where('flo_number', $request->get('slip'))
                ->select(db::raw('flo_number AS slip'), 'material_number', 'status')
                ->first();

            $data = db::table('flos')
                ->where('flo_number', $request->get('slip'))
                ->select(db::raw('flo_number AS slip'), 'material_number', 'status', db::raw('actual AS quantity'))
                ->get();
        }

        if ($check) {
            if ($check->status <= 1) {
                $response = array(
                    'status' => false,
                    'message' => 'Belum scan delivery',
                );
                return Response::json($response);

            } else if ($check->status == 2) {
                $response = array(
                    'status' => true,
                    'data' => $data,
                    'message' => 'Data ditemukan',
                );
                return Response::json($response);

            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Sudah stuffing',
                );
                return Response::json($response);

            }

        } else {
            $response = array(
                'status' => false,
                'message' => 'Data tidak ditemukan',
            );
            return Response::json($response);

        }

    }

    public function fetchCheckScrap(Request $request)
    {
        $stocktaking = db::connection('ympimis_2')
            ->table('stocktaking_scraps')
            ->where('category', 'PI')
            ->where('slip', $request->get('slip'))
            ->get();

        if (count($stocktaking) > 0) {
            $response = array(
                'status' => false,
                'stocktaking' => $stocktaking,
                'message' => 'Slip sudah di input',
            );
            return Response::json($response);

        } else {
            $select_data = db::select('select slip, order_no, material_number, material_description, quantity from scrap_logs where order_no = "' . $request->input('slip') . '" and receive_location in ("MSCR", "WSCR", "OTHR", "MMJR")');

            $pi = $select_data[0]->quantity;

            $response = array(
                'status' => true,
                'message' => 'Data ditemukan',
                'data' => $select_data,
                'pi' => $pi,
            );
            return Response::json($response);
        }
    }

    public function fetchDataScrap(Request $request)
    {
        try {
            // $resumes = db::connection('ympimis_2')->select('select * from stocktaking_scraps');

            $test = db::connection('ympimis_2')->select('select * from stocktaking_scraps');

            $test2 = db::select('select * from stocktaking_location_stocks where storage_location in ("MSCR", "WSCR", "MMJR", "OTHR")');

            $jumlah_book = db::select('select ROUND(sum(unrestricted), 3) as jumlah from stocktaking_location_stocks where storage_location in ("MSCR", "WSCR", "MMJR", "OTHR")');

            $jumlah_pi = db::connection('ympimis_2')->select('select ROUND(sum(pi), 3) as jumlah from stocktaking_scraps');

            $response = array(
                'status' => true,
                // 'resumes' => $resumes
                'test' => $test,
                'test2' => $test2,
                'jumlah_book' => $jumlah_book,
                'jumlah_pi' => $jumlah_pi,
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchMiraiFstk(Request $request)
    {
        $now = date('Y-m-d H:i:s');

        $mirai = db::select("SELECT flo_number AS slip, material_number, SUM(actual) AS quantity FROM flos
            WHERE `status` = 2
            GROUP BY flo_number, material_number
            UNION ALL
            SELECT knock_down_details.kd_number AS slip, knock_down_details.material_number, SUM(knock_down_details.quantity) AS quantity FROM knock_down_details
            LEFT JOIN knock_downs ON knock_downs.kd_number = knock_down_details.kd_number
            WHERE knock_downs.`status` = 2
            GROUP BY knock_down_details.kd_number, knock_down_details.material_number
            UNION ALL
            SELECT eo_number_sequence AS slip, material_number, SUM(quantity) AS quantity FROM extra_order_detail_sequences
            WHERE `status` = 2
            GROUP BY eo_number_sequence, material_number");

        $mpdl = db::table('material_plant_data_lists')
            ->whereIn('valcl', ['9010', '9040'])
            ->get();

        DB::beginTransaction();
        $delete = db::connection('ympimis_2')
            ->table('stocktaking_finish_goods')
            ->where('category', '=', 'MIRAI')
            ->delete();

        for ($i = 0; $i < count($mirai); $i++) {

            $material_description = '';
            for ($j = 0; $j < count($mpdl); $j++) {
                if ($mpdl[$j]->material_number == strtoupper($mirai[$i]->material_number)) {
                    $material_description = $mpdl[$j]->material_description;
                    break;
                }
            }

            try {
                $insert = db::connection('ympimis_2')
                    ->table('stocktaking_finish_goods')
                    ->insert([
                        'category' => 'MIRAI',
                        'location' => 'FSTK',
                        'material_number' => strtoupper($mirai[$i]->material_number),
                        'material_description' => $material_description,
                        'ymes' => 0,
                        'mirai' => $mirai[$i]->quantity,
                        'pi' => 0,
                        'remark' => $mirai[$i]->slip,
                        'created_by' => $request->get('employee_id'),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

            } catch (\Exception$e) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }

        }

        DB::commit();
        $response = array(
            'status' => true,
        );
        return Response::json($response);

    }

    public function fetchCheckMaterial(Request $request)
    {
        $material = MaterialPlantDataList::where('material_number', $request->get('material'))->first();

        $response = array(
            'status' => true,
            'material' => $material,
        );
        return Response::json($response);
    }

    public function fetchGetStorageLocation(Request $request)
    {
        $group = $request->get('group');

        $getStorageLocation = StorageLocation::where('storage_locations.area', $group)
            ->select('storage_locations.area', 'storage_locations.storage_location')
            ->orderBy('storage_locations.area', 'ASC')
            ->orderBy('storage_locations.storage_location', 'ASC')
            ->get();

        echo '<option value=""></option>';
        for ($i = 0; $i < count($getStorageLocation); $i++) {
            echo '<option value="' . $getStorageLocation[$i]['storage_location'] . '">' . $getStorageLocation[$i]['storage_location'] . '</option>';
        }

    }

    public function fetchGetStore(Request $request)
    {
        $location = $request->get('location');

        $getStore = StocktakingNewList::leftJoin('storage_locations', 'stocktaking_new_lists.location', '=', 'storage_locations.storage_location')
            ->where('stocktaking_new_lists.location', $location)
            ->distinct()
            ->select('storage_locations.area', 'stocktaking_new_lists.location', 'stocktaking_new_lists.store')
            ->orderBy('storage_locations.area', 'ASC')
            ->orderBy('stocktaking_new_lists.location', 'ASC')
            ->orderBy('stocktaking_new_lists.store', 'ASC')
            ->get();

        echo '<option value=""></option>';
        for ($i = 0; $i < count($getStore); $i++) {
            echo '<option value="' . $getStore[$i]['store'] . '">' . $getStore[$i]['store'] . '</option>';
        }
        echo '<option value="LAINNYA">LAINNYA</option>';

    }

    public function fetchCheckMonth(Request $request)
    {

        $month = $request->get('month');
        $data = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

        if (!$data) {
            $response = array(
                'status' => false,
                'message' => "Stocktaking Data Not Found",
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);

    }

    public function fetchStore(Request $request)
    {

        $area = '';
        if ($request->get('area') != null) {
            $areas = $request->get('area');
            for ($i = 0; $i < count($areas); $i++) {
                $area = $area . "'" . $areas[$i] . "'";
                if ($i != (count($areas) - 1)) {
                    $area = $area . ',';
                }
            }
            $area = "storage_locations.area IN (" . $area . ") ";
        }

        $location = '';
        if ($request->get('location') != null) {
            $locations = $request->get('location');
            for ($i = 0; $i < count($locations); $i++) {
                $location = $location . "'" . $locations[$i] . "'";
                if ($i != (count($locations) - 1)) {
                    $location = $location . ',';
                }
            }
            $location = "stocktaking_lists.location IN (" . $location . ") ";
        }

        $store = '';
        if ($request->get('store') != null) {
            $stores = $request->get('store');
            for ($i = 0; $i < count($stores); $i++) {
                $store = $store . "'" . $stores[$i] . "'";
                if ($i != (count($stores) - 1)) {
                    $store = $store . ',';
                }
            }
            $store = "stocktaking_lists.store IN (" . $store . ") ";
        }

        $condition = '';
        $and = false;
        if ($area != '' || $location != '' || $store != '') {
            $condition = 'WHERE';
        }

        if ($area != '') {
            $and = true;
            $condition = $condition . ' ' . $area;
        }

        if ($location != '') {
            if ($and) {
                $condition = $condition . ' OR ';
            }

            $condition = $condition . ' ' . $location;
        }

        if ($store != '') {
            if ($and) {
                $condition = $condition . ' OR ';
            }

            $condition = $condition . ' ' . $store;
        }

        $data = db::select("SELECT storage_locations.area AS `group`, stocktaking_lists.location, stocktaking_lists.store, count( stocktaking_lists.id ) AS quantity FROM stocktaking_lists
         LEFT JOIN storage_locations ON storage_locations.storage_location = stocktaking_lists.location
         " . $condition . "
         GROUP BY storage_locations.area, stocktaking_lists.location, stocktaking_lists.store
         ORDER BY storage_locations.area, stocktaking_lists.location, stocktaking_lists.store ASC");

        return DataTables::of($data)
            ->addColumn('delete', function ($data) {
                return '<button style="width: 50%; height: 100%;" onclick="deleteStore(\'' . $data->store . '\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-trash"></i></span></button>';
            })
            ->addColumn('reprint', function ($data) {
                return '<button style="width: 50%; height: 100%;" onclick="reprintStore(\'' . $data->store . '\')" class="btn btn-xs btn-primary form-control"><span><i class="fa fa-print"></i></span></button>';
            })
            ->rawColumns([
                'reprint' => 'reprint',
                'delete' => 'delete',
            ])
            ->make(true);
    }

    public function fetchStoreDetail(Request $request)
    {
        $area = '';
        if ($request->get('area') != null) {
            $areas = $request->get('area');
            for ($i = 0; $i < count($areas); $i++) {
                $area = $area . "'" . $areas[$i] . "'";
                if ($i != (count($areas) - 1)) {
                    $area = $area . ',';
                }
            }
            $area = "sl.area IN (" . $area . ") ";
        }

        $location = '';
        if ($request->get('location') != null) {
            $locations = $request->get('location');
            for ($i = 0; $i < count($locations); $i++) {
                $location = $location . "'" . $locations[$i] . "'";
                if ($i != (count($locations) - 1)) {
                    $location = $location . ',';
                }
            }
            $location = "s.location IN (" . $location . ") ";
        }

        $store = '';
        if ($request->get('store') != null) {
            $stores = $request->get('store');
            for ($i = 0; $i < count($stores); $i++) {
                $store = $store . "'" . $stores[$i] . "'";
                if ($i != (count($stores) - 1)) {
                    $store = $store . ',';
                }
            }
            $store = "s.store IN (" . $store . ") ";
        }

        $condition = '';
        $and = false;
        if ($area != '' || $location != '' || $store != '') {
            $condition = 'WHERE';
        }

        if ($area != '') {
            $and = true;
            $condition = $condition . ' ' . $area;
        }

        if ($location != '') {
            if ($and) {
                $condition = $condition . ' OR ';
            }

            $condition = $condition . ' ' . $location;
        }

        if ($store != '') {
            if ($and) {
                $condition = $condition . ' OR ';
            }

            $condition = $condition . ' ' . $store;
        }

        $data = db::select("SELECT
         s.id,
         sl.area AS `group`,
         s.location,
         s.store,
         s.sub_store,
         s.category,
         s.material_number,
         mpdl.material_description,
         mpdl.bun AS uom,
         s.print_status
         FROM
         stocktaking_new_lists s
         LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
         LEFT JOIN storage_locations sl ON sl.storage_location = s.location " . $condition . "
         ORDER BY
         sl.area,
         s.location,
         s.store,
         s.category,
         s.material_number ASC");

        $response = array(
            'status' => true,
            'data' => $data,
            'role' => Auth::user()->role_code,
        );
        return Response::json($response);

        // return DataTables::of($data)
        // ->addColumn('delete', function($data){
        //     return '<button style="width: 75%; height: 100%; vertical_align: middle;" onclick="deleteMaterial(\''.$data->id.'\')" class="btn btn-sm btn-danger form-control"><span><i class="fa fa-trash"></i> Delete</span></button>';
        // })
        // ->addColumn('print', function($data){
        //     if($data->print_status == 0){
        //         return '<span class="label label-sm label-primary">Print</span>';
        //     }else{
        //         return '<span class="label label-sm label-info">Rerint</span>';
        //     }
        // })
        // ->addColumn('check', function($data){
        //     if($data->print_status == 0){
        //         return '<input class="minimal" type="checkbox" id="'.$data->id.'+R'.'" onclick="showSelected(this)">';
        //     }else{
        //         return '<input class="minimal" type="checkbox" id="'.$data->id.'+RP'.'" onclick="showSelected(this)">';
        //     }
        // })
        // ->rawColumns([
        //     'check' => 'check',
        //     'print' => 'print',
        //     'delete' => 'delete'
        // ])
        // ->make(true);
    }

    public function fetchStoreDetailNew(Request $request)
    {

        $area = '';
        if ($request->get('area') != null) {
            $areas = $request->get('area');
            for ($i = 0; $i < count($areas); $i++) {
                $area = $area . "'" . $areas[$i] . "'";
                if ($i != (count($areas) - 1)) {
                    $area = $area . ',';
                }
            }
            $area = "sl.area IN (" . $area . ") ";
        }

        $location = '';
        if ($request->get('location') != null) {
            $locations = $request->get('location');
            for ($i = 0; $i < count($locations); $i++) {
                $location = $location . "'" . $locations[$i] . "'";
                if ($i != (count($locations) - 1)) {
                    $location = $location . ',';
                }
            }
            $location = "s.location IN (" . $location . ") ";
        }

        $store = '';
        if ($request->get('store') != null) {
            $stores = $request->get('store');
            for ($i = 0; $i < count($stores); $i++) {
                $store = $store . "'" . $stores[$i] . "'";
                if ($i != (count($stores) - 1)) {
                    $store = $store . ',';
                }
            }
            $store = "s.store IN (" . $store . ") ";
        }

        $substore = '';
        if ($request->get('substore') != null) {
            $substores = $request->get('substore');
            for ($i = 0; $i < count($substores); $i++) {
                $substore = $substore . "'" . $substores[$i] . "'";
                if ($i != (count($substores) - 1)) {
                    $substore = $substore . ',';
                }
            }
            $substore = "CONCAT_WS(' - ',s.sub_store,s.store) IN (" . $substore . ") ";
        }

        $condition = '';
        $and = false;
        if ($area != '' || $location != '' || $store != '' || $substore != '') {
            $condition = 'WHERE';
        }

        if ($area != '') {
            $and = true;
            $condition = $condition . ' ' . $area;
        }

        if ($location != '') {
            if ($and) {
                $condition = $condition . ' OR ';
            }

            $condition = $condition . ' ' . $location;
        }

        if ($store != '') {
            if ($and) {
                $condition = $condition . ' OR ';
            }

            $condition = $condition . ' ' . $store;
        }

        if ($substore != '') {
            if ($and) {
                $condition = $condition . ' OR ';
            }

            $condition = $condition . ' ' . $substore;
        }

        $data = db::select("
                    SELECT s.id,
                        s.area AS `group`,
                        s.location,
                        s.store,
                        s.category,
                        s.material_number,
                        s.material_description,
                        s.sub_store,
                        s.print_status
                    FROM stocktaking_new_lists s
                    " . $condition . "
                    ORDER BY s.area,
                        s.location,
                        s.store,
                        s.sub_store,
                        s.category,
                        s.material_number ASC");

        $response = array(
            'status' => true,
            'data' => $data,
            'role' => Auth::user()->role_code,
        );
        return Response::json($response);

        // return DataTables::of($data)
        // ->addColumn('delete', function($data){
        //     return '<button style="width: 75%; height: 100%; vertical_align: middle;" onclick="deleteMaterial(\''.$data->id.'\')" class="btn btn-sm btn-danger form-control"><span><i class="fa fa-trash"></i> Delete</span></button>';
        // })
        // ->addColumn('print', function($data){
        //     if($data->print_status == 0){
        //         return '<span class="label label-sm label-primary">Print</span>';
        //     }else{
        //         return '<span class="label label-sm label-info">Rerint</span>';
        //     }
        // })
        // ->addColumn('check', function($data){
        //     if($data->print_status == 0){
        //         return '<input class="minimal" type="checkbox" id="'.$data->id.'+R'.'" onclick="showSelected(this)">';
        //     }else{
        //         return '<input class="minimal" type="checkbox" id="'.$data->id.'+RP'.'" onclick="showSelected(this)">';
        //     }
        // })
        // ->rawColumns([
        //     'check' => 'check',
        //     'print' => 'print',
        //     'delete' => 'delete'
        // ])
        // ->make(true);
    }

    public function fetchUnmatch(Request $request)
    {
        $month = $request->get('month');
        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

        $pis = db::select("SELECT
         pi.material_number,
         pi.location AS storage_location,
         sum( pi.quantity ) AS qty
         FROM
         stocktaking_outputs AS pi
         LEFT JOIN storage_locations AS sl ON sl.storage_location = pi.location
         WHERE
         pi.location NOT IN (
         'WCJR',
         'WSCR',
         'MSCR',
         'YCJP',
         '401',
         'MMJR',
         'SXWH',
         'WPCS',
         'WPPN',
         'WPRC',
         'MSTK',
         'MSCR',
         'OTHR',
         'WSCR',
         'MINS',
         'MNCF',
         'WHRP',
         'FSTK'
         )
         AND sl.area IS NOT NULL
         GROUP BY
         pi.location,
         pi.material_number");

        $storage_location_stocks = db::select("SELECT
         sls.material_number,
         sls.storage_location,
         sls.unrestricted
         FROM
         stocktaking_location_stocks AS sls
         LEFT JOIN storage_locations AS sl ON sl.storage_location = sls.storage_location
         WHERE
         sls.stock_date = '" . $calendar->date . "'
         AND sls.storage_location NOT IN (
         'WCJR',
         'WSCR',
         'MSCR',
         'YCJP',
         '401',
         'MMJR',
         'SXWH',
         'WPCS',
         'WPPN',
         'WPRC',
         'MSTK',
         'MSCR',
         'OTHR',
         'WSCR',
         'MINS',
         'MNCF',
         'WHRP',
         'FSTK'
         )
         AND sl.area IS NOT NULL
         ORDER BY
         sls.material_number ASC,
         sl.storage_location ASC");

        $kitto_inventories = "SELECT
        ki.material_number,
        ki.storage_location AS location,
        sum( ki.quantity ) AS qty
        FROM
        kitto_inventories AS ki
        GROUP BY
        ki.material_number,
        ki.storage_location";

        $mirai_inventories = "SELECT
        fd.serial_number,
        fd.material_number,
        IF
        ( f.STATUS = 2, 'FSTK', mpdl.storage_location ) AS storage_location,
        fd.quantity
        FROM
        flo_details AS fd
        LEFT JOIN flos AS f ON f.flo_number = fd.flo_number
        LEFT JOIN material_plant_data_lists AS mpdl ON mpdl.material_number = fd.material_number
        WHERE
        f.STATUS IN ( '0', '1', 'M' ) UNION ALL
        SELECT
        knock_down_details.serial_number,
        knock_down_details.material_number,
        IF
        ( knock_downs.status = 2, 'FSTK', knock_down_details.storage_location ) AS storage_location,
        knock_down_details.quantity
        FROM
        knock_down_details
        LEFT JOIN knock_downs ON knock_downs.kd_number = knock_down_details.kd_number
        WHERE
        knock_downs.status IN ( 1 ) UNION ALL
        SELECT
        extra_order_detail_sequences.serial_number,
        extra_order_detail_sequences.material_number,
        IF
        ( extra_order_detail_sequences.status = 2, 'FSTK', extra_order_detail_sequences.storage_location ) AS storage_location,
        extra_order_detail_sequences.quantity
        FROM
        extra_order_detail_sequences
        WHERE
        extra_order_detail_sequences.status IN (
        1)";

        $pi_vs_book = array();
        $mirai_vs_pi = array();
        $mirai_vs_book = array();
        $mirai_vs_lot = array();

        $response = array(
            'status' => true,
            'pi_vs_book' => $pi_vs_book,
            'mirai_vs_pi' => $mirai_vs_pi,
            'mirai_vs_book' => $mirai_vs_book,
            'mirai_vs_lot' => $mirai_vs_lot,
        );
        return Response::json($response);
    }

    public function fetchPiVsBook(Request $request)
    {
        $month = $request->get('month');
        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

        if ($calendar) {
            $data = db::select("SELECT storage_locations.area AS `group`, pi_kitto.location, pi_kitto.material_number, material_plant_data_lists.material_description, pi_kitto.pi FROM
                (SELECT pi.location, pi.material_number, pi.qty AS pi, book.qty AS book FROM
                (SELECT location, material_number, sum( quantity ) AS qty FROM stocktaking_outputs
                GROUP BY location, material_number) AS pi
                LEFT JOIN
                (SELECT storage_location AS location, material_number, sum( unrestricted ) AS qty FROM stocktaking_location_stocks
                WHERE stock_date = '" . $calendar->date . "'
                GROUP BY storage_location, material_number) AS book
                ON pi.location = book.location
                AND pi.material_number = book.material_number) AS pi_kitto
                LEFT JOIN material_plant_data_lists ON pi_kitto.material_number = material_plant_data_lists.material_number
                LEFT JOIN storage_locations ON storage_locations.storage_location = pi_kitto.location
                WHERE pi_kitto.book is null
                AND pi_kitto.pi > 0
                AND pi_kitto.location not in ('WCJR','WSCR','MSCR','YCJP','401','MMJR')
                AND storage_locations.area is not null
                ORDER BY storage_locations.area, pi_kitto.location, pi_kitto.material_number");

            return DataTables::of($data)->make(true);
        }

        // AND pi_kitto.location not in ('WCJR','WSCR','MSCR','YCJP','401','PSTK','203','208','214','216','217','MMJR')

    }

    public function fetchBookVsPi(Request $request)
    {
        $month = $request->get('month');
        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

        if ($calendar) {
            $data = db::select("SELECT storage_locations.area AS `group`, pi_kitto.location, pi_kitto.material_number, material_plant_data_lists.material_description, pi_kitto.book FROM
                (SELECT book.location, book.material_number, pi.qty AS pi, book.qty AS book FROM
                (SELECT storage_location AS location, material_number, sum( unrestricted ) AS qty FROM stocktaking_location_stocks
                WHERE stock_date = '" . $calendar->date . "'
                GROUP BY storage_location, material_number) AS book
                LEFT JOIN
                (SELECT location, material_number, sum( quantity ) AS qty FROM stocktaking_outputs
                GROUP BY location, material_number) AS pi
                ON pi.location = book.location
                AND pi.material_number = book.material_number) AS pi_kitto
                LEFT JOIN material_plant_data_lists ON pi_kitto.material_number = material_plant_data_lists.material_number
                LEFT JOIN storage_locations ON storage_locations.storage_location = pi_kitto.location
                WHERE pi_kitto.pi is null
                AND pi_kitto.book > 0
                AND pi_kitto.location not in ('WCJR','WSCR','MSCR','YCJP','401','MMJR')
                AND storage_locations.area is not null
                ORDER BY storage_locations.area, pi_kitto.location, pi_kitto.material_number");

            return DataTables::of($data)->make(true);
        }

        // AND pi_kitto.location not in ('WCJR','WSCR','MSCR','YCJP','401','PSTK','203','208','214','216','217','MMJR')

    }

    public function fetchKittoVsPi()
    {
        $data = db::select("SELECT storage_locations.area AS `group`, kitto_pi.location, kitto_pi.material_number, material_plant_data_lists.material_description, kitto_pi.kitto, kitto_pi.pi FROM
         (SELECT	inventory.location, inventory.material_number, inventory.qty AS kitto,	COALESCE ( pi.qty, 0 ) AS pi FROM
         (SELECT storage_location AS location, material_number, sum( quantity ) AS qty FROM kitto_inventories
         GROUP BY storage_location, material_number) AS inventory
         LEFT JOIN
         (SELECT location, material_number, sum( quantity ) AS qty FROM stocktaking_outputs
         GROUP BY location, material_number) AS pi
         ON inventory.location = pi.location
         AND inventory.material_number = pi.material_number) AS kitto_pi
         LEFT JOIN material_plant_data_lists ON kitto_pi.material_number = material_plant_data_lists.material_number
         LEFT JOIN storage_locations ON storage_locations.storage_location = kitto_pi.location
         WHERE kitto_pi.kitto <> kitto_pi.pi
         AND storage_locations.area is not null
         ORDER BY storage_locations.area, kitto_pi.location, kitto_pi.material_number");

        return DataTables::of($data)->make(true);
    }

    public function fetchKittoVsBook(Request $request)
    {
        $month = $request->get('month');
        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

        if ($calendar) {
            $data = db::select("SELECT storage_locations.area AS `group`, kitto_book.location, kitto_book.material_number, material_plant_data_lists.material_description, kitto_book.kitto, kitto_book.book FROM
                (SELECT inventory.location, inventory.material_number, inventory.qty AS kitto, COALESCE ( book.qty, 0 ) AS book FROM
                (SELECT storage_location AS location, material_number, sum( quantity ) AS qty FROM kitto_inventories
                GROUP BY storage_location, material_number) AS inventory
                LEFT JOIN
                (SELECT storage_location AS location, material_number, sum( unrestricted ) AS qty FROM stocktaking_location_stocks
                WHERE stock_date = '" . $calendar->date . "'
                GROUP BY storage_location, material_number) AS book
                ON inventory.location = book.location
                AND inventory.material_number = book.material_number) AS kitto_book
                LEFT JOIN material_plant_data_lists ON kitto_book.material_number = material_plant_data_lists.material_number
                LEFT JOIN storage_locations ON storage_locations.storage_location = kitto_book.location
                WHERE kitto_book.kitto <> kitto_book.book
                AND storage_locations.area is not null
                ORDER BY storage_locations.area, kitto_book.location, kitto_book.material_number");

            return DataTables::of($data)->make(true);
        }

    }

    public function fetchPiVsLot()
    {
        $data = db::select("SELECT storage_locations.area AS `group`, inventory.location, inventory.material_number, material_plant_data_lists.material_description, inventory.quantity, material_volumes.lot_transfer FROM
         (SELECT pi.location, lot.material_number, pi.quantity FROM
         (SELECT location, material_number, sum(quantity) AS quantity FROM stocktaking_outputs
         GROUP BY location, material_number) AS pi
         JOIN
         (SELECT issue_location AS location, material_number, sum( lot ) AS qty FROM kitto.inventories
         GROUP BY issue_location, material_number) AS lot
         ON pi.location = lot.location AND pi.material_number = lot.material_number) AS inventory
         LEFT JOIN material_volumes ON inventory.material_number = material_volumes.material_number
         LEFT JOIN material_plant_data_lists ON inventory.material_number = material_plant_data_lists.material_number
         LEFT JOIN storage_locations ON inventory.location = storage_locations.storage_location
         WHERE (inventory.quantity % material_volumes.lot_transfer) <> 0
         AND storage_locations.area is not null
         ");

        return DataTables::of($data)->make(true);
    }

    public function fetchVariance(Request $request)
    {
        $month = $request->get('month');

        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();
        if (!$calendar) {
            $response = array(
                'status' => false,
                'message' => "Stocktaking Data Not Found",
            );
            return Response::json($response);
        }

        if ($calendar->status != 'finished') {
            $variance = db::select("SELECT plnt, `group`, sum(var_amt_abs)/sum(book_amt)*100 AS percentage FROM
                (SELECT storage_locations.area AS `group`,
                storage_locations.plnt,
                pi_book.material_number,
                pi_book.location,
                ROUND((material_plant_data_lists.standard_price/1000), 5) AS std,
                pi_book.pi AS pi,
                pi_book.book AS book,
                (ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.book) AS book_amt,
                ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))) AS var_amt_abs
                FROM
                (SELECT location, material_number, sum(pi) AS pi, sum(book) AS book FROM
                (SELECT location, material_number, sum(quantity) AS pi, 0 as book FROM stocktaking_outputs
                GROUP BY location, material_number
                UNION ALL
                SELECT storage_location AS location, material_number, 0 as pi, sum(unrestricted) AS book FROM stocktaking_location_stocks
                WHERE stock_date = '" . $calendar->date . "'
                GROUP BY storage_location, material_number) AS union_pi_book
                GROUP BY location, material_number) AS pi_book
                LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = pi_book.material_number
                LEFT JOIN storage_locations ON storage_locations.storage_location = pi_book.location
                WHERE storage_locations.area IS NOT NULL
                AND pi_book.location NOT IN ('WCJR','WSCR','MSCR','YCJP','401','MMJR')
                ) AS official_variance
                GROUP BY plnt, `group`");

            // AND pi_book.location NOT IN ('WCJR','WSCR','MSCR','YCJP','401','PSTK','203','208','214','216','217','MMJR')

            $ympi = db::select("SELECT ympi, sum(var_amt_abs)/sum(book_amt)*100 AS percentage FROM
                (SELECT storage_locations.area AS `group`,
                'YMPI' as ympi,
                storage_locations.plnt,
                pi_book.material_number,
                pi_book.location,
                ROUND((material_plant_data_lists.standard_price/1000), 5) AS std,
                pi_book.pi AS pi,
                pi_book.book AS book,
                (ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.book) AS book_amt,
                ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))) AS var_amt_abs
                FROM
                (SELECT location, material_number, sum(pi) AS pi, sum(book) AS book FROM
                (SELECT location, material_number, sum(quantity) AS pi, 0 as book FROM stocktaking_outputs
                GROUP BY location, material_number
                UNION ALL
                SELECT storage_location AS location, material_number, 0 as pi, sum(unrestricted) AS book FROM stocktaking_location_stocks
                WHERE stock_date = '" . $calendar->date . "'
                GROUP BY storage_location, material_number) AS union_pi_book
                GROUP BY location, material_number) AS pi_book
                LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = pi_book.material_number
                LEFT JOIN storage_locations ON storage_locations.storage_location = pi_book.location
                WHERE storage_locations.area IS NOT NULL
                AND pi_book.location NOT IN ('WCJR','WSCR','MSCR','YCJP','401','MMJR')
                ) AS official_variance
                GROUP BY ympi");

            // AND pi_book.location NOT IN ('WCJR','WSCR','MSCR','YCJP','401','PSTK','203','208','214','216','217','MMJR')

        } else {
            $variance = db::select("SELECT plnt, `group`, sum(var_amt_abs)/sum(book_amt)*100 AS percentage FROM
                (SELECT storage_locations.area AS `group`,
                storage_locations.plnt,
                pi_book.material_number,
                pi_book.location,
                ROUND((material_plant_data_lists.standard_price/1000), 5) AS std,
                pi_book.pi AS pi,
                pi_book.book AS book,
                (ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.book) AS book_amt,
                ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))) AS var_amt_abs
                FROM
                (SELECT location, material_number, sum(pi) AS pi, sum(book) AS book FROM
                (SELECT location, material_number, sum(quantity) AS pi, 0 as book FROM stocktaking_output_logs
                WHERE stocktaking_date = '" . $calendar->date . "'
                GROUP BY location, material_number
                UNION ALL
                SELECT storage_location AS location, material_number, 0 as pi, sum(unrestricted) AS book FROM storage_location_stocks
                WHERE stock_date = '" . $calendar->date . "'
                GROUP BY storage_location, material_number) AS union_pi_book
                GROUP BY location, material_number) AS pi_book
                LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = pi_book.material_number
                LEFT JOIN storage_locations ON storage_locations.storage_location = pi_book.location
                WHERE storage_locations.area IS NOT NULL
                AND pi_book.location NOT IN ('WCJR','WSCR','MSCR','YCJP','401','MMJR')
                ) AS official_variance
                GROUP BY plnt, `group`");

            // AND pi_book.location NOT IN ('WCJR','WSCR','MSCR','YCJP','401','PSTK','203','208','214','216','217','MMJR')

            $ympi = db::select("SELECT plnt, `group`, sum(var_amt_abs)/sum(book_amt)*100 AS percentage FROM
                (SELECT storage_locations.area AS `group`,
                'YMPI' as ympi,
                storage_locations.plnt,
                pi_book.material_number,
                pi_book.location,
                ROUND((material_plant_data_lists.standard_price/1000), 5) AS std,
                pi_book.pi AS pi,
                pi_book.book AS book,
                (ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.book) AS book_amt,
                ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))) AS var_amt_abs
                FROM
                (SELECT location, material_number, sum(pi) AS pi, sum(book) AS book FROM
                (SELECT location, material_number, sum(quantity) AS pi, 0 as book FROM stocktaking_output_logs
                WHERE stocktaking_date = '" . $calendar->date . "'
                GROUP BY location, material_number
                UNION ALL
                SELECT storage_location AS location, material_number, 0 as pi, sum(unrestricted) AS book FROM storage_location_stocks
                WHERE stock_date = '" . $calendar->date . "'
                GROUP BY storage_location, material_number) AS union_pi_book
                GROUP BY location, material_number) AS pi_book
                LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = pi_book.material_number
                LEFT JOIN storage_locations ON storage_locations.storage_location = pi_book.location
                WHERE storage_locations.area IS NOT NULL
                AND pi_book.location NOT IN ('WCJR','WSCR','MSCR','YCJP','401','MMJR')
                ) AS official_variance
                GROUP BY plnt, `group`");

            // AND pi_book.location NOT IN ('WCJR','WSCR','MSCR','YCJP','401','PSTK','203','208','214','216','217','MMJR')

        }

        $response = array(
            'status' => true,
            'variance' => $variance,
            'ympi' => $ympi,
        );
        return Response::json($response);
    }

    public function fetchVarianceDetail(Request $request)
    {
        $month = $request->get('month');
        $location = $request->get('location');

        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();
        if (!$calendar) {
            $response = array(
                'status' => false,
                'message' => "Stocktaking Data Not Found",
            );
            return Response::json($response);
        }

        if ($calendar->status != 'finished') {
            $variance_detail = db::select("SELECT plnt, `group`, location, sum(var_amt_abs)/sum(book_amt)*100 AS percentage FROM
                (SELECT storage_locations.area AS `group`,
                storage_locations.plnt,
                pi_book.material_number,
                pi_book.location,
                ROUND((material_plant_data_lists.standard_price/1000), 5) AS std,
                pi_book.pi AS pi,
                pi_book.book AS book,
                (pi_book.pi - pi_book.book) AS diff_qty,
                (ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.pi) AS pi_amt,
                (ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.book) AS book_amt,
                ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))) AS var_amt_abs
                FROM
                (SELECT location, material_number, sum(pi) AS pi, sum(book) AS book FROM
                (SELECT location, material_number, sum(quantity) AS pi, 0 as book FROM stocktaking_outputs
                GROUP BY location, material_number
                UNION ALL
                SELECT storage_location AS location, material_number, 0 as pi, sum(unrestricted) AS book FROM stocktaking_location_stocks
                WHERE stock_date = '" . $calendar->date . "'
                GROUP BY storage_location, material_number) AS union_pi_book
                GROUP BY location, material_number) AS pi_book
                LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = pi_book.material_number
                LEFT JOIN storage_locations ON storage_locations.storage_location = pi_book.location
                WHERE storage_locations.area = '" . $location . "'
                AND storage_locations.storage_location not in ('SX91', 'SX51', 'SX21')
                ) AS official_variance
                GROUP BY plnt, `group`, location");
        } else {
            $variance_detail = db::select("SELECT plnt, `group`, location, sum(var_amt_abs)/sum(book_amt)*100 AS percentage FROM
                (SELECT storage_locations.area AS `group`,
                storage_locations.plnt,
                pi_book.material_number,
                pi_book.location,
                ROUND((material_plant_data_lists.standard_price/1000), 5) AS std,
                pi_book.pi AS pi,
                pi_book.book AS book,
                (pi_book.pi - pi_book.book) AS diff_qty,
                (ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.pi) AS pi_amt,
                (ROUND((material_plant_data_lists.standard_price/1000), 5) * pi_book.book) AS book_amt,
                ABS((ROUND((material_plant_data_lists.standard_price/1000), 5) * (pi_book.pi - pi_book.book))) AS var_amt_abs
                FROM
                (SELECT location, material_number, sum(pi) AS pi, sum(book) AS book FROM
                (SELECT location, material_number, sum(quantity) AS pi, 0 as book FROM stocktaking_output_logs
                WHERE stocktaking_date = '" . $calendar->date . "'
                GROUP BY location, material_number
                UNION ALL
                SELECT storage_location AS location, material_number, 0 as pi, sum(unrestricted) AS book FROM storage_location_stocks
                WHERE stock_date = '" . $calendar->date . "'
                GROUP BY storage_location, material_number) AS union_pi_book
                GROUP BY location, material_number) AS pi_book
                LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = pi_book.material_number
                LEFT JOIN storage_locations ON storage_locations.storage_location = pi_book.location
                WHERE storage_locations.area = '" . $location . "') AS official_variance
                GROUP BY plnt, `group`, location");
        }

        $response = array(
            'status' => true,
            'variance_detail' => $variance_detail,
        );
        return Response::json($response);

    }

    public function fetchfilledList(Request $request)
    {
        $month = $request->get('month');

        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

        if (!$calendar) {
            $response = array(
                'status' => false,
                'message' => "Stocktaking Data Not Found",
            );
            return Response::json($response);
        }

        if ($calendar->status != 'finished') {
            $data = db::select("SELECT area, location, sum(total) - sum(qty) AS empty, sum(qty) AS qty, sum(total) AS total FROM
                (SELECT sl.area, s.location, 0 AS qty, count(s.id) AS total FROM stocktaking_lists s
                LEFT JOIN storage_locations sl on sl.storage_location = s.location
                WHERE s.print_status = 1
                GROUP BY sl.area, s.location
                UNION ALL
                SELECT sl.area, s.location, count(s.id) AS qty, 0 AS total FROM stocktaking_lists s
                LEFT JOIN storage_locations sl on sl.storage_location = s.location
                WHERE s.print_status = 1
                AND s.quantity IS NOT NULL
                GROUP BY sl.area, s.location) AS list
                GROUP BY area, location
                ORDER BY area");
        } else {
            $data = db::select("SELECT area, location, sum(total) - sum(qty) AS empty, sum(qty) AS qty, sum(total) AS total FROM
                (SELECT sl.area, s.location, 0 AS qty, count(s.id) AS total FROM stocktaking_inquiry_logs s
                LEFT JOIN storage_locations sl on sl.storage_location = s.location
                WHERE s.stocktaking_date = '" . $calendar->date . "'
                GROUP BY sl.area, s.location
                UNION ALL
                SELECT sl.area, s.location, count(s.id) AS qty, 0 AS total FROM stocktaking_inquiry_logs s
                LEFT JOIN storage_locations sl on sl.storage_location = s.location
                WHERE s.stocktaking_date = '" . $calendar->date . "'
                AND s.quantity IS NOT NULL
                GROUP BY sl.area, s.location) AS list
                GROUP BY area, location
                ORDER BY area");
        }

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);

    }

    public function fetchfilledListNew(Request $request)
    {
        $month = $request->get('month');

        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

        if (!$calendar) {
            $response = array(
                'status' => false,
                'message' => "Stocktaking Data Not Found",
            );
            return Response::json($response);
        }

        if ($calendar->status != 'finished') {
            $data = db::select("SELECT area, location, sum(`use`) AS `use`, sum(no_use) AS no_use FROM
                (SELECT sl.area, s.location, 0 AS `use`, count(s.id) AS no_use FROM stocktaking_new_lists s
                LEFT JOIN storage_locations sl ON sl.storage_location = s.location
                WHERE s.quantity IS NULL
                AND s.print_status = 1
                GROUP BY sl.area, s.location
                UNION ALL
                SELECT sl.area, s.location, count(s.id) AS `use`, 0 AS no_use FROM stocktaking_new_lists s
                LEFT JOIN storage_locations sl ON sl.storage_location = s.location
                WHERE s.quantity IS NOT NULL
                AND s.print_status = 1
                GROUP BY sl.area, s.location
                ) AS list
                GROUP BY area, location
                ORDER BY area");
        } else {
            $data = db::select("SELECT area, location, sum(total) - sum(qty) AS empty, sum(qty) AS qty, sum(total) AS total FROM
                (SELECT sl.area, s.location, 0 AS qty, count(s.id) AS total FROM stocktaking_inquiry_logs s
                LEFT JOIN storage_locations sl on sl.storage_location = s.location
                WHERE s.stocktaking_date = '" . $calendar->date . "'
                GROUP BY sl.area, s.location
                UNION ALL
                SELECT sl.area, s.location, count(s.id) AS qty, 0 AS total FROM stocktaking_inquiry_logs s
                LEFT JOIN storage_locations sl on sl.storage_location = s.location
                WHERE s.stocktaking_date = '" . $calendar->date . "'
                AND s.quantity IS NOT NULL
                GROUP BY sl.area, s.location) AS list
                GROUP BY area, location
                ORDER BY area");
        }

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);

    }

    public function fetchfilledListbByStore(Request $request)
    {
        $month = $request->get('month');

        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

        if (!$calendar) {
            $response = array(
                'status' => false,
                'message' => "Stocktaking Data Not Found",
            );
            return Response::json($response);
        }

        if ($calendar->status != 'finished') {
            $data = db::select("SELECT
                area,
                location,
                store,
                sum( total ) - sum( qty ) AS empty,
                sum( qty ) AS qty,
                sum( total ) AS total
                FROM
                (
                SELECT
                sl.area,
                s.location,
                s.store,
                0 AS qty,
                count( s.id ) AS total
                FROM
                stocktaking_new_lists s
                LEFT JOIN storage_locations sl ON sl.storage_location = s.location
                WHERE
                s.print_status = 1
                GROUP BY
                s.store,
                sl.area,
                s.location

                UNION ALL
                SELECT
                sl.area,
                s.location,
                s.store,
                count( s.id ) AS qty,
                0 AS total
                FROM
                stocktaking_new_lists s
                LEFT JOIN storage_locations sl ON sl.storage_location = s.location
                WHERE
                s.print_status = 1
                AND s.quantity IS NOT NULL
                GROUP BY
                s.store,
                sl.area,
                s.location
                ) AS list
                GROUP BY
                list.store,
                list.area,
                list.location
                ORDER BY
                list.store,
                list.area,
                list.location");
        } else {
            $data = db::select("SELECT area, location, sum(total) - sum(qty) AS empty, sum(qty) AS qty, sum(total) AS total FROM
                (SELECT sl.area, s.location, 0 AS qty, count(s.id) AS total FROM stocktaking_inquiry_logs s
                LEFT JOIN storage_locations sl on sl.storage_location = s.location
                WHERE s.stocktaking_date = '" . $calendar->date . "'
                GROUP BY sl.area, s.location
                UNION ALL
                SELECT sl.area, s.location, count(s.id) AS qty, 0 AS total FROM stocktaking_inquiry_logs s
                LEFT JOIN storage_locations sl on sl.storage_location = s.location
                WHERE s.stocktaking_date = '" . $calendar->date . "'
                AND s.quantity IS NOT NULL
                GROUP BY sl.area, s.location) AS list
                GROUP BY area, location
                ORDER BY area");
        }

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);

    }

    public function fetchfilledListbBySubstore(Request $request)
    {
        $month = $request->get('month');

        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

        if (!$calendar) {
            $response = array(
                'status' => false,
                'message' => "Stocktaking Data Not Found",
            );
            return Response::json($response);
        }

        if ($calendar->status != 'finished') {
            $data = db::select("SELECT
                area,
                location,
                sub_store,
                sum( total ) - sum( qty ) AS empty,
                sum( qty ) AS qty,
                sum( total ) AS total
                FROM
                (
                SELECT
                sl.area,
                s.location,
                s.sub_store,
                0 AS qty,
                count( s.id ) AS total
                FROM
                stocktaking_new_lists s
                LEFT JOIN storage_locations sl ON sl.storage_location = s.location
                WHERE
                s.print_status = 1
                GROUP BY
                s.sub_store,
                sl.area,
                s.location

                UNION ALL
                SELECT
                sl.area,
                s.location,
                s.sub_store,
                count( s.id ) AS qty,
                0 AS total
                FROM
                stocktaking_new_lists s
                LEFT JOIN storage_locations sl ON sl.storage_location = s.location
                WHERE
                s.print_status = 1
                AND s.quantity IS NOT NULL
                GROUP BY
                s.sub_store,
                sl.area,
                s.location
                ) AS list
                GROUP BY
                list.sub_store,
                list.area,
                list.location
                ORDER BY
                list.sub_store,
                list.area,
                list.location");
        } else {
            $data = db::select("SELECT area, location, sum(total) - sum(qty) AS empty, sum(qty) AS qty, sum(total) AS total FROM
                (SELECT sl.area, s.location, 0 AS qty, count(s.id) AS total FROM stocktaking_inquiry_logs s
                LEFT JOIN storage_locations sl on sl.storage_location = s.location
                WHERE s.stocktaking_date = '" . $calendar->date . "'
                GROUP BY sl.area, s.location
                UNION ALL
                SELECT sl.area, s.location, count(s.id) AS qty, 0 AS total FROM stocktaking_inquiry_logs s
                LEFT JOIN storage_locations sl on sl.storage_location = s.location
                WHERE s.stocktaking_date = '" . $calendar->date . "'
                AND s.quantity IS NOT NULL
                GROUP BY sl.area, s.location) AS list
                GROUP BY area, location
                ORDER BY area");
        }

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);

    }

    public function fetchfilledListDetail(Request $request)
    {
        $group = $request->get('group');
        $month = $request->get('month');
        $quantity = '';
        // if($request->get('series') == 'Empty'){
        //     $quantity = 'AND s.quantity IS NULL';
        // }else if ($request->get('series') == 'Inputted') {
        //     $quantity = 'AND s.quantity IS NOT NULL';
        // }

        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();
        if (!$calendar) {
            $response = array(
                'status' => false,
                'message' => "Stocktaking Data Not Found",
            );
            return Response::json($response);
        }

        if ($calendar->status != 'finished') {
            $input_detail = db::select("SELECT sl.area, s.location, s.category, s.store, s.material_number, mpdl.material_description, s.quantity, s.audit1, s.audit2, s.final_count, if(s.quantity is null, 0, 1) as ord FROM stocktaking_lists s
                LEFT JOIN storage_locations sl on sl.storage_location = s.location
                LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
                WHERE s.location = '" . $group . "'
                " . $quantity . "
                AND s.print_status = 1
                ORDER BY ord, sl.area, s.location, s.store, s.material_number ASC");
        } else {
            $input_detail = db::select("
                SELECT sl.area, s.location, s.category, s.store, s.material_number, mpdl.material_description, NULL AS quantity, NULL AS audit1, NULL AS audit2, s.quantity AS final_count FROM stocktaking_inquiry_logs s
                LEFT JOIN storage_locations sl on sl.storage_location = s.location
                LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
                WHERE s.location = '" . $group . "'
                " . $quantity . "
                AND s.stocktaking_date = '" . $calendar->date . "'
                ORDER BY sl.area, s.location, s.store, s.material_number, s.quantity ASC;");
        }

        $response = array(
            'status' => true,
            'input_detail' => $input_detail,
        );
        return Response::json($response);
    }

    public function fetchfilledListDetailNew(Request $request)
    {
        $group = $request->get('group');
        $month = $request->get('month');
        $quantity = '';
        // if($request->get('series') == 'Empty'){
        //     $quantity = 'AND s.quantity IS NULL';
        // }else if ($request->get('series') == 'Inputted') {
        //     $quantity = 'AND s.quantity IS NOT NULL';
        // }

        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();
        if (!$calendar) {
            $response = array(
                'status' => false,
                'message' => "Stocktaking Data Not Found",
            );
            return Response::json($response);
        }

        if ($calendar->status != 'finished') {
            $input_detail = db::select("SELECT s.location, s.category, s.store, s.sub_store, s.material_number, s.remark, s.quantity, s.audit1, s.final_count
                FROM stocktaking_new_lists s
                WHERE s.location = '" . $group . "'
                AND s.print_status = 1
                ORDER BY s.quantity, s.store, s.sub_store, s.material_number ASC");
        } else {
            $input_detail = db::select("
                SELECT sl.area, s.location, s.category, s.store, s.material_number, mpdl.material_description, NULL AS quantity, NULL AS audit1, NULL AS audit2, s.quantity AS final_count FROM stocktaking_inquiry_logs s
                LEFT JOIN storage_locations sl on sl.storage_location = s.location
                LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
                WHERE s.location = '" . $group . "'
                " . $quantity . "
                AND s.stocktaking_date = '" . $calendar->date . "'
                ORDER BY sl.area, s.location, s.store, s.material_number, s.quantity ASC;");
        }

        $mpdl = db::select('SELECT * FROM material_plant_data_lists');

        $response = array(
            'status' => true,
            'mpdl' => $mpdl,
            'input_detail' => $input_detail,
        );
        return Response::json($response);
    }

    public function fetchfilledListDetailByStore(Request $request)
    {
        $group = $request->get('group');
        $month = $request->get('month');
        $quantity = '';
        // if($request->get('series') == 'Empty'){
        //     $quantity = 'AND s.quantity IS NULL';
        // }else if ($request->get('series') == 'Inputted') {
        //     $quantity = 'AND s.quantity IS NOT NULL';
        // }

        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();
        if (!$calendar) {
            $response = array(
                'status' => false,
                'message' => "Stocktaking Data Not Found",
            );
            return Response::json($response);
        }

        if ($calendar->status != 'finished') {
            $input_detail = db::select("SELECT
                sl.area,
                s.location,
                s.category,
                s.store,
                s.sub_store,
                s.material_number,
                mpdl.material_description,
                s.quantity,
                s.audit1,
			-- 	s.audit2,
			s.final_count,
			IF
			( s.quantity IS NULL, 0, 1 ) AS ord
			FROM
			stocktaking_new_lists s
			LEFT JOIN storage_locations sl ON sl.storage_location = s.location
			LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
			WHERE
			s.store = '" . $group . "'
			AND s.print_status = 1
			ORDER BY
			ord,
			sl.area,
			s.location,
			s.store,
			s.sub_store,
			s.material_number ASC");
        } else {
            $input_detail = db::select("
                SELECT sl.area, s.location, s.category, s.store, s.material_number, mpdl.material_description, NULL AS quantity, NULL AS audit1, NULL AS audit2, s.quantity AS final_count FROM stocktaking_inquiry_logs s
                LEFT JOIN storage_locations sl on sl.storage_location = s.location
                LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
                WHERE s.location = '" . $group . "'
                " . $quantity . "
                AND s.stocktaking_date = '" . $calendar->date . "'
                ORDER BY sl.area, s.location, s.store, s.material_number, s.quantity ASC;");
        }

        $response = array(
            'status' => true,
            'input_detail' => $input_detail,
        );
        return Response::json($response);
    }

    public function fetchfilledListDetailBySubstore(Request $request)
    {
        $group = $request->get('group');
        $month = $request->get('month');
        $quantity = '';
        // if($request->get('series') == 'Empty'){
        //     $quantity = 'AND s.quantity IS NULL';
        // }else if ($request->get('series') == 'Inputted') {
        //     $quantity = 'AND s.quantity IS NOT NULL';
        // }

        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();
        if (!$calendar) {
            $response = array(
                'status' => false,
                'message' => "Stocktaking Data Not Found",
            );
            return Response::json($response);
        }

        if ($calendar->status != 'finished') {
            $input_detail = db::select("SELECT
                sl.area,
                s.location,
                s.category,
                s.store,
                s.sub_store,
                s.material_number,
                mpdl.material_description,
                s.quantity,
                s.audit1,
			-- 	s.audit2,
			s.final_count,
			IF
			( s.quantity IS NULL, 0, 1 ) AS ord
			FROM
			stocktaking_new_lists s
			LEFT JOIN storage_locations sl ON sl.storage_location = s.location
			LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
			WHERE
			s.sub_store = '" . $group . "'
			AND s.print_status = 1
			ORDER BY
			ord,
			sl.area,
			s.location,
			s.store,
			s.sub_store,
			s.material_number ASC");
        } else {
            $input_detail = db::select("
                SELECT sl.area, s.location, s.category, s.store, s.material_number, mpdl.material_description, NULL AS quantity, NULL AS audit1, NULL AS audit2, s.quantity AS final_count FROM stocktaking_inquiry_logs s
                LEFT JOIN storage_locations sl on sl.storage_location = s.location
                LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
                WHERE s.location = '" . $group . "'
                " . $quantity . "
                AND s.stocktaking_date = '" . $calendar->date . "'
                ORDER BY sl.area, s.location, s.store, s.material_number, s.quantity ASC;");
        }

        $response = array(
            'status' => true,
            'input_detail' => $input_detail,
        );
        return Response::json($response);
    }

    public function fetchAuditedList(Request $request)
    {
        $month = $request->get('month');

        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

        if (!$calendar) {
            $response = array(
                'status' => false,
                'message' => "Stocktaking Data Not Found",
            );
            return Response::json($response);
        }

        if ($calendar->status != 'finished') {
            $data = db::select("SELECT storage_locations.area, audited.location, sum( audited ) AS audited, sum( not_audited ) AS not_audited FROM
                (SELECT location, store, IF( total = audit, 1, 0 ) AS audited, IF( total <> audit, 1, 0 ) AS not_audited FROM
                (SELECT stocktaking_lists.location, stocktaking_lists.store, count( stocktaking_lists.id ) AS total, SUM( IF ( stocktaking_lists.process >= 2, 1, 0 ) ) AS audit FROM stocktaking_lists
                WHERE print_status = 1
                GROUP BY stocktaking_lists.location, stocktaking_lists.store
                ) audit
                ) AS audited
                LEFT JOIN storage_locations ON audited.location = storage_locations.storage_location
                GROUP BY storage_locations.area, audited.location");
        } else {
            $response = array(
                'status' => false,
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);
    }

    public function fetchAuditedListNew(Request $request)
    {
        $month = $request->get('month');

        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();

        if (!$calendar) {
            $response = array(
                'status' => false,
                'message' => "Stocktaking Data Not Found",
            );
            return Response::json($response);
        }

        if ($calendar->status != 'finished') {
            $data = db::select("SELECT storage_locations.area, audited.location, sum( audited ) AS audited, sum( not_audited ) AS not_audited FROM
                (SELECT location, store, IF( total = audit, 1, 0 ) AS audited, IF( total <> audit, 1, 0 ) AS not_audited FROM
                (SELECT stocktaking_new_lists.location, stocktaking_new_lists.store, count( stocktaking_new_lists.id ) AS total, SUM( IF ( stocktaking_new_lists.process >= 2, 1, 0 ) ) AS audit FROM stocktaking_new_lists
                WHERE quantity IS NOT NULL
                GROUP BY stocktaking_new_lists.location, stocktaking_new_lists.store
                ) audit
                ) AS audited
                LEFT JOIN storage_locations ON audited.location = storage_locations.storage_location
                GROUP BY storage_locations.area, audited.location");
        } else {
            $response = array(
                'status' => false,
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);
    }

    public function fetchAuditedListDetail(Request $request)
    {
        $group = $request->get('group');
        $month = $request->get('month');
        $series = $request->get('series');

        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();
        if (!$calendar) {
            $response = array(
                'status' => false,
                'message' => "Stocktaking Data Not Found",
            );
            return Response::json($response);
        }

        if ($calendar->status != 'finished') {
            $audit_detail = db::select("SELECT storage_locations.area, audited.location, audited.store, IF( audited.audited = 1, 1, 0) AS ord FROM
                (SELECT location, store, IF( total = audit, 1, 0 ) AS audited, IF( total <> audit, 1, 0 ) AS not_audited FROM
                (SELECT stocktaking_lists.location, stocktaking_lists.store, count( stocktaking_lists.id ) AS total, SUM( IF ( stocktaking_lists.process >= 2, 1, 0 ) ) AS audit FROM stocktaking_lists
                WHERE stocktaking_lists.location = '" . $group . "'
                AND stocktaking_lists.print_status = 1
                GROUP BY stocktaking_lists.location, stocktaking_lists.store) audit) AS audited
                LEFT JOIN storage_locations ON audited.location = storage_locations.storage_location
                order by ord asc");
        } else {
            $response = array(
                'status' => false,
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'audit_detail' => $audit_detail,
        );
        return Response::json($response);
    }

    public function fetchAuditedListDetailNew(Request $request)
    {
        $group = $request->get('group');
        $month = $request->get('month');
        $series = $request->get('series');

        $calendar = StocktakingCalendar::where(db::raw("DATE_FORMAT(date,'%Y-%m')"), $month)->first();
        if (!$calendar) {
            $response = array(
                'status' => false,
                'message' => "Stocktaking Data Not Found",
            );
            return Response::json($response);
        }

        if ($calendar->status != 'finished') {
            $audit_detail = db::select("SELECT storage_locations.area, audited.location, audited.store, IF( audited.audited = 1, 1, 0) AS ord FROM
                (SELECT location, store, IF( total = audit, 1, 0 ) AS audited, IF( total <> audit, 1, 0 ) AS not_audited FROM
                (SELECT stocktaking_new_lists.location, stocktaking_new_lists.store, count( stocktaking_new_lists.id ) AS total, SUM( IF ( stocktaking_new_lists.process >= 2, 1, 0 ) ) AS audit FROM stocktaking_new_lists
                WHERE stocktaking_new_lists.location = '" . $group . "'
                AND stocktaking_new_lists.print_status = 1
                AND stocktaking_new_lists.remark IS NOT NULL
                GROUP BY stocktaking_new_lists.location, stocktaking_new_lists.store) audit) AS audited
                LEFT JOIN storage_locations ON audited.location = storage_locations.storage_location
                order by ord asc");
        } else {
            $response = array(
                'status' => false,
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'audit_detail' => $audit_detail,
        );
        return Response::json($response);
    }

    public function fetchCheckAudit(Request $request, $audit)
    {

        $minimum = 0;
        if ($audit == 'audit1') {
            $minimum = 10;
        } else if ($audit == 'audit2') {
            $minimum = 5;
        }

        $actual = db::select("SELECT
         ( SELECT count( id ) AS total FROM stocktaking_lists
         WHERE remark = 'USE'
         AND store = '" . $request->get('store') . "'
         AND print_status = 1
         AND " . $audit . " IS NOT NULL )
         /
         ( SELECT count( id ) AS total
         FROM stocktaking_lists
         WHERE remark = 'USE'
         AND print_status = 1
         AND store = '" . $request->get('store') . "' )
         * 100
         AS percentage");

        if ($actual[0]->percentage >= $minimum) {
            $response = array(
                'status' => true,
                'actual' => $actual[0]->percentage,
                'minimum' => $minimum,
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'actual' => $actual[0]->percentage,
                'minimum' => $minimum,
            );
            return Response::json($response);
        }

    }

    public function fetchCheckAuditNew(Request $request, $audit)
    {

        $minimum = 0;
        if ($audit == 'audit1') {
            $minimum = 10;
        } else if ($audit == 'audit2') {
            $minimum = 5;
        }

        $actual = db::select("SELECT
         ( SELECT count( id ) AS total FROM stocktaking_new_lists
         WHERE remark = 'USE'
         AND store = '" . $request->get('store') . "'
         AND print_status = 1
         AND " . $audit . " IS NOT NULL )
         /
         ( SELECT count( id ) AS total
         FROM stocktaking_new_lists
         WHERE remark = 'USE'
         AND quantity > 0
         AND print_status = 1
         AND store = '" . $request->get('store') . "' )
         * 100
         AS percentage");

        if ($actual[0]->percentage >= $minimum) {
            $response = array(
                'status' => true,
                'actual' => $actual[0]->percentage,
                'minimum' => $minimum,
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'actual' => $actual[0]->percentage,
                'minimum' => $minimum,
            );
            return Response::json($response);
        }

    }

    public function fetchSummaryOfCounting(Request $request)
    {

        $store = '';
        if (strlen($request->get('store')) > 0) {
            $stores = explode(',', $request->get('store'));
            for ($i = 0; $i < count($stores); $i++) {
                $store = $store . "'" . $stores[$i] . "'";
                if ($i != (count($stores) - 1)) {
                    $store = $store . ',';
                }
            }
            $store = " WHERE s.store in (" . $store . ") ";
        }

        $summary = db::select("SELECT
         s.id,
         sl.area,
         s.store,
         s.category,
         s.material_number,
         mpdl.material_description,
         m.`key`,
         m.model,
         m.surface,
         mpdl.bun,
         s.location,
         mpdl.storage_location,
         v.lot_completion,
         v.lot_transfer,
         IF
         ( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot
         FROM
         stocktaking_lists s
         LEFT JOIN materials m ON m.material_number = s.material_number
         LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
         LEFT JOIN material_volumes v ON v.material_number = s.material_number
         LEFT JOIN storage_locations sl ON sl.storage_location = s.location
         " . $store . "
         ORDER BY s.store ASC");

        return DataTables::of($summary)->make(true);
    }

    public function fetchMaterialDetail(Request $request)
    {
        $material = db::select("SELECT
         s.id,
         s.store,
         s.category,
         s.material_number,
         s.process,
         mpdl.material_description,
         m.`key`,
         m.model,
         m.surface,
         mpdl.bun,
         s.location,
         sl.area,
         mpdl.storage_location,
         v.lot_completion,
         v.lot_transfer,
         IF
         ( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot,
         s.quantity,
         s.audit1,
         s.final_count,
         s.remark
         FROM
         stocktaking_lists s
         LEFT JOIN materials m ON m.material_number = s.material_number
         LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
         LEFT JOIN material_volumes v ON v.material_number = s.material_number
         LEFT JOIN storage_locations sl ON sl.storage_location = s.location
         WHERE
         s.id = " . $request->get('id'));

        $response = array(
            'status' => true,
            'material' => $material,
        );
        return Response::json($response);
    }

    public function fetchMaterialDetailAudit(Request $request)
    {
        $material = db::select("SELECT
         s.id,
         s.store,
         s.sub_store,
         s.category,
         s.material_number,
         s.process,
         mpdl.material_description,
         m.`key`,
         m.model,
         m.surface,
         mpdl.bun,
         s.location,
         sl.area,
         mpdl.storage_location,
         v.lot_completion,
         v.lot_transfer,
         IF
         ( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot,
         s.quantity,
         s.audit1,
         s.final_count,
         s.remark
         FROM
         stocktaking_new_lists s
         LEFT JOIN materials m ON m.material_number = s.material_number
         LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
         LEFT JOIN material_volumes v ON v.material_number = s.material_number
         LEFT JOIN storage_locations sl ON sl.storage_location = s.location
         WHERE
         s.id = " . $request->get('id'));

        $response = array(
            'status' => true,
            'material' => $material,
        );
        return Response::json($response);
    }

    public function fetchMaterialDetailNew(Request $request)
    {

        $now = date('Y-m-d H:i:s');
        $id = $request->get('id');
        $idnew = explode('_', $id);

        try {
            if ($idnew[0] == 'ST') {
                $material = db::select("SELECT
                   s.id,
                   s.store,
                   s.sub_store,
                   s.category,
                   s.material_number,
                   s.process,
                   mpdl.material_description,
                   m.`key`,
                   m.model,
                   m.surface,
                   mpdl.bun,
                   s.location,
                   sl.area,
                   sl.stocktaking_time,
                   mpdl.storage_location,
                   v.lot_completion,
                   v.lot_transfer,
                   IF
                   ( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot,
                   s.quantity,
                   s.audit1,
                   s.final_count,
                   s.remark
                   FROM
                   stocktaking_new_lists s
                   LEFT JOIN materials m ON m.material_number = s.material_number
                   LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
                   LEFT JOIN material_volumes v ON v.material_number = s.material_number
                   LEFT JOIN storage_locations sl ON sl.storage_location = s.location
                   WHERE
                   s.id = " . $idnew[1]);

                $response = array(
                    'status' => true,
                    'material' => $material,
                );
                return Response::json($response);

            } else {
                $location = StocktakingNewList::join('storage_locations', 'storage_locations.storage_location', 'stocktaking_new_lists.location')->where('stocktaking_new_lists.id', $id)->first();

                if ($location->area == 'WAREHOUSE' || $location->area == 'FINISHED GOODS') {
                    $material = db::select("SELECT
                      s.id,
                      s.store,
                      s.sub_store,
                      s.category,
                      s.material_number,
                      s.process,
                      mpdl.material_description,
                      m.`key`,
                      m.model,
                      m.surface,
                      mpdl.bun,
                      s.location,
                      sl.area,
                      mpdl.storage_location,
                      v.lot_completion,
                      v.lot_transfer,
                      IF
                      ( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot,
                      s.quantity,
                      s.audit1,
                      s.final_count,
                      s.remark
                      FROM
                      stocktaking_new_lists s
                      LEFT JOIN materials m ON m.material_number = s.material_number
                      LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
                      LEFT JOIN material_volumes v ON v.material_number = s.material_number
                      LEFT JOIN storage_locations sl ON sl.storage_location = s.location
                      WHERE
                      s.id = " . $id);

                    $response = array(
                        'status' => true,
                        'material' => $material,
                    );
                    return Response::json($response);

                } else {
                    $response = array(
                        'status' => false,
                    );
                    return Response::json($response);
                }
            }
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
            );
            return Response::json($response);
        }
    }

    public function fetchStoreList(Request $request)
    {
        $store = db::select("SELECT
         s.id,
         s.store,
         s.category,
         s.material_number,
         mpdl.material_description,
         m.`key`,
         m.model,
         m.surface,
         mpdl.bun,
         s.location,
         sl.area,
         mpdl.storage_location,
         v.lot_completion,
         v.lot_transfer,
         IF
         ( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot,
         s.quantity,
         s.remark,
         s.audit1,
         s.audit2
         FROM
         stocktaking_lists s
         LEFT JOIN materials m ON m.material_number = s.material_number
         LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
         LEFT JOIN material_volumes v ON v.material_number = s.material_number
         LEFT JOIN storage_locations sl ON sl.storage_location = s.location
         WHERE s.print_status = 1
         AND s.store = '" . $request->get('store') . "'
         ORDER BY
         s.id ASC");

        // ORDER BY
        // s.remark DESC,
        // s.category ASC,
        // s.material_number ASC

        $response = array(
            'status' => true,
            'store' => $store,
        );
        return Response::json($response);
    }

    public function fetchStoreListNew(Request $request)
    {
        $store = db::select("SELECT
         s.id,
         s.store,
         s.sub_store,
         s.category,
         s.material_number,
         mpdl.material_description,
         m.`key`,
         m.model,
         m.surface,
         mpdl.bun,
         s.location,
         sl.area,
         mpdl.storage_location,
         v.lot_completion,
         v.lot_transfer,
         IF
         ( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot,
         s.quantity,
         s.remark
         FROM
         stocktaking_new_lists s
         LEFT JOIN materials m ON m.material_number = s.material_number
         LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
         LEFT JOIN material_volumes v ON v.material_number = s.material_number
         LEFT JOIN storage_locations sl ON sl.storage_location = s.location
         WHERE
         s.store = '" . $request->get('store') . "'
         ORDER BY
         s.id ASC");

        $response = array(
            'status' => true,
            'store' => $store,
        );
        return Response::json($response);
    }

    public function fetchReviseId(Request $request)
    {

        $process = $request->get('process');
        $current = StocktakingList::where('id', $request->get('id'))->first();

        //Cek Store
        if ($current == null) {
            $response = array(
                'status' => false,
                'message' => 'Data tidak ditemukan',
            );
            return Response::json($response);
        }

        $null = StocktakingList::where('store', $current->store)
            ->whereNull('final_count')
            ->get();

        //Cek qty sudah terisi ?
        if (count($null) > 0) {
            $response = array(
                'status' => false,
                'message' => 'Proses sebelumnya belum selesai',
            );
            return Response::json($response);
        }

        //Cek proses saat ini
        if ($process > $current->process) {
            $response = array(
                'status' => false,
                'message' => 'Proses sebelumnya belum selesai',
            );
            return Response::json($response);
        }

        $store = db::select("SELECT
         s.id,
         s.store,
         s.category,
         s.material_number,
         mpdl.material_description,
         m.`key`,
         m.model,
         m.surface,
         mpdl.bun,
         s.location,
         mpdl.storage_location,
         v.lot_completion,
         v.lot_transfer,
         IF
         ( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot,
         s.remark,
         s.process,
         s.quantity,
         s.audit1,
         s.audit2,
         s.final_count
         FROM
         stocktaking_lists s
         LEFT JOIN materials m ON m.material_number = s.material_number
         LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
         LEFT JOIN material_volumes v ON v.material_number = s.material_number
         WHERE
         s.store = '" . $current->store . "'
         ORDER BY
         s.id ASC");

        $response = array(
            'status' => true,
            'store' => $store,
            'store_name' => $current->store,
        );
        return Response::json($response);
    }

    public function fetchRevise(Request $request)
    {

        $process = $request->get('process');
        $current = StocktakingList::where('store', $request->get('store'))->first();

        $null = StocktakingList::where('store', $request->get('store'))
            ->whereNull('final_count')
            ->get();

        //Cek Store
        if ($current == null) {
            $response = array(
                'status' => false,
                'message' => 'Store tidak ditemukan',
            );
            return Response::json($response);
        }

        //Cek qty sudah terisi ?
        if (count($null) > 0) {
            $response = array(
                'status' => false,
                'message' => 'Proses sebelumnya belum selesai',
            );
            return Response::json($response);
        }

        //Cek proses saat ini
        if ($process > $current->process) {
            $response = array(
                'status' => false,
                'message' => 'Proses sebelumnya belum selesai',
            );
            return Response::json($response);
        }

        $store = db::select("SELECT
         s.id,
         s.store,
         s.category,
         s.material_number,
         mpdl.material_description,
         m.`key`,
         m.model,
         m.surface,
         mpdl.bun,
         s.location,
         mpdl.storage_location,
         v.lot_completion,
         v.lot_transfer,
         IF
         ( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot,
         s.remark,
         s.process,
         s.quantity,
         s.audit1,
         s.audit2,
         s.final_count
         FROM
         stocktaking_lists s
         LEFT JOIN materials m ON m.material_number = s.material_number
         LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
         LEFT JOIN material_volumes v ON v.material_number = s.material_number
         WHERE
         s.store = '" . $request->get('store') . "'
         ORDER BY
         s.id ASC");

        $response = array(
            'status' => true,
            'store' => $store,
        );
        return Response::json($response);
    }

    public function fetchAuditStoreList(Request $request)
    {

        $process = $request->get('process');
        $current = StocktakingList::where('store', $request->get('store'))
            ->where('print_status', 1)
            ->orderBy('process', 'ASC')
            ->first();

        $null = StocktakingList::where('store', $request->get('store'))
            ->where('print_status', 1)
            ->whereNull('quantity')
            ->get();

        //Cek Store
        if ($current == null) {
            $response = array(
                'status' => false,
                'message' => 'Store tidak ditemukan',
            );
            return Response::json($response);
        }

        //Cek qty sudah terisi ?
        if (count($null) > 0) {
            $response = array(
                'status' => false,
                'message' => 'Proses sebelumnya belum selesai',
            );
            return Response::json($response);
        }

        //Cek proses saat ini
        if ($process > $current->process) {
            $response = array(
                'status' => false,
                'message' => 'Proses sebelumnya belum selesai',
            );
            return Response::json($response);
        }

        $store = db::select("SELECT
         s.id,
         s.store,
         s.category,
         s.material_number,
         mpdl.material_description,
         m.`key`,
         m.model,
         m.surface,
         mpdl.bun,
         s.location,
         mpdl.storage_location,
         v.lot_completion,
         v.lot_transfer,
         IF
         ( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot,
         s.remark,
         s.process,
         s.quantity,
         s.audit1,
         s.audit2
         FROM
         stocktaking_lists s
         LEFT JOIN materials m ON m.material_number = s.material_number
         LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
         LEFT JOIN material_volumes v ON v.material_number = s.material_number
         WHERE s.print_status = 1
         AND s.store = '" . $request->get('store') . "'
         ORDER BY s.id");

        // ORDER BY
        // s.category,
        // s.material_number

        $response = array(
            'status' => true,
            'store' => $store,
        );
        return Response::json($response);
    }

    public function fetchAuditStoreListNew(Request $request)
    {

        $process = $request->get('process');

        $current = StocktakingNewList::where('store', $request->get('store'))
            ->where('print_status', 1)
            ->orderBy('process', 'ASC')
            ->first();

        $null = StocktakingNewList::where('store', $request->get('store'))
            ->where('print_status', 1)
            ->whereNull('quantity')
            ->get();

        //Cek Store
        if ($current == null) {
            $response = array(
                'status' => false,
                'message' => 'Store tidak ditemukan',
            );
            return Response::json($response);
        }

        // Cek qty sudah terisi ?
        if (count($null) > 0) {
            $response = array(
                'status' => false,
                'message' => 'Input PI belum selesai',
            );
            return Response::json($response);
        }

        // Cek proses saat ini
        if ($process > $current->process) {
            $response = array(
                'status' => false,
                'message' => 'Input PI belum selesai',
            );
            return Response::json($response);
        }

        $store = db::select("SELECT
         s.id,
         s.store,
         s.sub_store,
         s.category,
         s.material_number,
         mpdl.material_description,
         m.`key`,
         m.model,
         m.surface,
         mpdl.bun,
         s.location,
         mpdl.storage_location,
         v.lot_completion,
         v.lot_transfer,
         IF
         ( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot,
         s.remark,
         s.process,
         s.quantity,
         s.audit1
         FROM
         stocktaking_new_lists s
         LEFT JOIN materials m ON m.material_number = s.material_number
         LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
         LEFT JOIN material_volumes v ON v.material_number = s.material_number
         WHERE s.print_status = 1
         AND s.quantity > 0
         AND s.store = '" . $request->get('store') . "'
         ORDER BY s.id");

        // ORDER BY
        // s.category,
        // s.material_number

        $response = array(
            'status' => true,
            'store' => $store,
        );
        return Response::json($response);
    }

    public function fetchCheckInputStoreListNew(Request $request)
    {

        $store = db::select("SELECT s.id, s.location, s.store, s.sub_store, s.category, s.material_number, mpdl.material_description, mpdl.bun,
         s.remark, s.process, s.quantity,
         concat(SPLIT_STRING(inputor.`name`, ' ', 1), ' ', SPLIT_STRING(inputor.`name`, ' ', 2)) as inputor,
         concat(SPLIT_STRING(auditor.`name`, ' ', 1), ' ', SPLIT_STRING(auditor.`name`, ' ', 2)) as auditor,
         s.audit1, s.final_count
         FROM stocktaking_new_lists s
         LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
         LEFT JOIN employee_syncs AS inputor ON inputor.employee_id = s.inputed_by
         LEFT JOIN employee_syncs AS auditor ON auditor.employee_id = s.audit1_by
         WHERE s.print_status = 1
         AND s.store = '" . $request->get('store') . "'
         ORDER BY s.id");

        $belum = 0;
        $input = 0;
        $progress_audit = 0;
        $audit = 0;
        $breakdown = 0;

        for ($i = 0; $i < count($store); $i++) {
            if ($store[$i]->process == 0) {
                $belum++;
            }

            if ($store[$i]->quantity == 0) {
                $input++;
            }

            if ($store[$i]->audit1 > 0) {
                $progress_audit++;
            }

            if ($store[$i]->process == 2) {
                $audit++;
            }

            if ($store[$i]->process == 4) {
                $breakdown++;
            }
        }

        $status = '';
        $process = 0;
        if ($breakdown > 0) {
            $status = 'Variance keluar';
            $process = 5;
        } else if ($audit > 0) {
            $status = 'Sudah Audit';
            $process = 4;
        } else if (count($store) == $belum) {
            $status = 'Belum Input PI';
            $process = 1;
        } else {
            if ($progress_audit > 0) {
                $status = 'Progress Audit';
                $process = 3;
            } else {
                $status = 'Progress Input PI';
                $process = 2;
            }
        }

        $response = array(
            'status' => true,
            'store' => $store,
            'process_name' => $status,
            'process' => $process,
        );
        return Response::json($response);
    }

    public function fetchmpdl(Request $request)
    {
        $material_plant_data_lists = MaterialPlantDataList::orderBy('material_plant_data_lists.material_number', 'asc')->get();

        return DataTables::of($material_plant_data_lists)->make(true);
    }

    public function fetch_bom_output(Request $request)
    {
        $bom_outputs = BomOutput::orderBy('bom_outputs.id', 'asc')->get();

        return DataTables::of($bom_outputs)->make(true);
    }

    public function addMaterialForecast(Request $request)
    {
        $material = $request->get('material');

        try {
            $add = new StocktakingMaterialForecast([
                'material_number' => strtoupper($material),
                'created_by' => Auth::id(),
            ]);
            $add->save();

            $response = array(
                'status' => true,
                'message' => 'Add New Material Forecast Successful',
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

    }

    public function addMaterial(Request $request)
    {
        $store = $request->get('store');
        $substore = $request->get('substore');
        $category = $request->get('category');
        $material = $request->get('material');
        $location = $request->get('location');

        $cek = StocktakingNewList::where('store', $store)
            ->where('sub_store', $substore)
            ->where('category', $category)
            ->where('material_number', $material)
            ->where('location', $location)
            ->first();

        if ($cek) {
            $response = array(
                'status' => false,
                'message' => 'Material Already Added',
            );
            return Response::json($response);
        }

        $mpdl = MaterialPlantDataList::where('material_number', $material)->first();

        if (($mpdl->valcl == 9040 || $mpdl->valcl == 9041) && $category == 'ASSY') {
            $response = array(
                'status' => false,
                'message' => 'Indirect Material atau Material Awal tidak boleh ASSY',
            );
            return Response::json($response);
        } else if ($mpdl->spt == 50 && $category == 'SINGLE') {
            $response = array(
                'status' => false,
                'message' => 'Material number dengan spt 50 harus ASSY',
            );
            return Response::json($response);
        }

        try {
            $add = new StocktakingNewList([
                'store' => strtoupper($store),
                'sub_store' => strtoupper($substore),
                'category' => $category,
                'material_number' => strtoupper($material),
                'location' => $location,
                'created_by' => Auth::id(),
            ]);
            $add->save();

            $update_desc = db::select("UPDATE stocktaking_new_lists
                LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = stocktaking_new_lists.material_number
                SET stocktaking_new_lists.material_description = material_plant_data_lists.material_description
                WHERE stocktaking_new_lists.material_description IS NULL;");

            $update_area = db::select("UPDATE stocktaking_new_lists
                LEFT JOIN storage_locations ON storage_locations.storage_location = stocktaking_new_lists.location
                SET stocktaking_new_lists.area = storage_locations.area
                WHERE stocktaking_new_lists.area IS NULL;");

            $response = array(
                'status' => true,
                'message' => 'Add New Material Successful',
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function updateOpenInput(Request $request)
    {
        try {
            $update = StocktakingNewList::where('store', $request->get('store'))
                ->where('print_status', '1')
                ->update([
                    'process' => 1,
                ]);

            $response = array(
                'status' => true,
                'message' => 'Input PI Berhasil diBuka',
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function updateStoreNoUse(Request $request)
    {

        try {
            $update = StocktakingNewList::where('store', $request->get('store'))
                ->where('print_status', '1')
                ->update([
                    'process' => 2,
                    'remark' => 'NO USE',
                    'quantity' => 0,
                    'inputed_by' => Auth::user()->username,
                    'inputed_at' => date('Y-m-d H:i:s'),

                ]);

            $response = array(
                'status' => true,
                'message' => 'Update No Use Berhasil',
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

    }

    public function updateNoUse(Request $request)
    {
        try {

            $update = StocktakingList::whereIn('id', $request->get('id'))
                ->update([
                    'remark' => 'NO USE',
                    'process' => 1,
                    'quantity' => 0,
                    'inputed_by' => Auth::user()->username,
                ]);

            $id = $request->get('id');
            for ($i = 0; $i < count($id); $i++) {
                $storeID = StocktakingList::where('id', $id)->first();

                $stores = StocktakingList::where('store', $storeID->store)
                    ->where('print_status', 1)
                    ->get();

                $no_use = StocktakingList::where('store', $storeID->store)
                    ->where('print_status', 1)
                    ->where('remark', 'NO USE')
                    ->get();

                if (count($stores) == count($no_use)) {
                    $update = StocktakingList::where('store', $storeID->store)
                        ->update([
                            'process' => 2,
                            'final_count' => 0,
                        ]);
                }
            }

            $response = array(
                'status' => true,
                'message' => 'Update No Use Berhasil',
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function updateNoUseNew(Request $request)
    {
        try {

            $update = StocktakingNewList::whereIn('id', $request->get('id'))
                ->update([
                    'remark' => 'NO USE',
                    'process' => 1,
                    'quantity' => 0,
                    'inputed_by' => Auth::user()->username,
                    'inputed_at' => date('Y-m-d H:i:s'),
                ]);

            $id = $request->get('id');
            for ($i = 0; $i < count($id); $i++) {
                $storeID = StocktakingNewList::where('id', $id[$i])->first();

                $stores = StocktakingNewList::where('store', $storeID->store)
                    ->where('print_status', 1)
                    ->get();

                $no_use = StocktakingNewList::where('store', $storeID->store)
                    ->where('print_status', 1)
                    ->where('remark', 'NO USE')
                    ->get();

                if (count($stores) == count($no_use)) {
                    $update = StocktakingNewList::where('store', $storeID->store)
                        ->update([
                            'process' => 2,
                            'final_count' => 0,
                        ]);
                }
            }

            $response = array(
                'status' => true,
                'message' => 'Update No Use Berhasil',
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function byPassAudit()
    {

        $store = StocktakingList::get();

        for ($i = 0; $i < count($store); $i++) {
            $final = 0;
            if ($store[$i]->audit2 > 0) {
                $final = $store[$i]->audit2;
            } else if ($store[$i]->audit1 > 0) {
                $final = $store[$i]->audit1;
            } else {
                $final = $store[$i]->quantity;
            }
            $updateStore = StocktakingList::where('id', $store[$i]->id)
                ->update([
                    'process' => 3,
                    'final_count' => $final,
                ]);
        }

    }

    public function updateProcessAudit(Request $request, $audit)
    {

        if ($audit == 'audit1') {
            $process = 2;
        } else if ($audit == 'audit2') {
            $process = 3;
        }

        try {
            $updateStore = StocktakingList::where('store', $request->get('store'))
                ->update([
                    'process' => $process,
                ]);

            //Audit 1 -> Update Final Count
            if ($audit == 'audit1') {
                $store = StocktakingList::where('store', $request->get('store'))->get();

                for ($i = 0; $i < count($store); $i++) {
                    $final = 0;
                    if ($store[$i]->audit2 > 0) {
                        $final = $store[$i]->audit2;
                    } else if ($store[$i]->audit1 > 0) {
                        $final = $store[$i]->audit1;
                    } else {
                        $final = $store[$i]->quantity;
                    }
                    $updateStore = StocktakingList::where('id', $store[$i]->id)
                        ->update([
                            'final_count' => $final,
                        ]);
                }
            }

            $response = array(
                'status' => true,
                'message' => 'Audit Berhasil',
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function updateProcessAuditNew(Request $request, $audit)
    {

        if ($audit == 'audit1') {
            $process = 2;
        } else if ($audit == 'audit2') {
            $process = 3;
        }

        try {
            $updateStore = StocktakingNewList::where('store', $request->get('store'))
                ->where('print_status', 1)
                ->update([
                    'process' => $process,
                    'remark' => 'USE',
                ]);

            //Audit 1 -> Update Final Count
            if ($audit == 'audit1') {
                $store = StocktakingNewList::where('store', $request->get('store'))
                    ->where('remark', 'USE')
                    ->where('print_status', 1)
                    ->get();

                for ($i = 0; $i < count($store); $i++) {
                    $final = 0;
                    if ($store[$i]->audit2 > 0) {
                        $final = $store[$i]->audit2;
                    } else if ($store[$i]->audit1 > 0) {
                        $final = $store[$i]->audit1;
                    } else {
                        $final = $store[$i]->quantity;
                    }

                    $updateStore = StocktakingNewList::where('id', $store[$i]->id)
                        ->update([
                            'final_count' => $final,
                        ]);
                }
            }

            $response = array(
                'status' => true,
                'message' => 'Audit Berhasil',
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function updateAudit(Request $request, $audit)
    {
        $id = $request->get('id');
        $quantity = $request->get('quantity');
        $auditor = $request->get('auditor');

        $field = '';
        if ($audit == 'audit1') {
            $field = 'audit1_by';
        } else if ($audit == 'audit2') {
            $field = 'audit2_by';
        }

        try {

            $update = StocktakingList::where('id', $id)
                ->update([
                    $audit => $quantity,
                    $field => $auditor,
                ]);

            $response = array(
                'status' => true,
                'message' => 'Update Berhasil',
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function updateAuditNew(Request $request, $audit)
    {
        $id = $request->get('id');
        $quantity = $request->get('quantity');
        $auditor = $request->get('auditor');

        $field = '';
        $field_time = '';
        if ($audit == 'audit1') {
            $field = 'audit1_by';
            $field_time = 'audit1_at';
        } else if ($audit == 'audit2') {
            $field = 'audit2_by';
            $field_time = 'audit2_at';
        }

        try {
            $update = StocktakingNewList::where('id', $id)
                ->update([
                    $audit => $quantity,
                    $field => $auditor,
                    $field_time => date('Y-m-d H:i:s'),
                ]);

            $response = array(
                'status' => true,
                'message' => 'Update Berhasil',
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function uploadRevise(Request $request)
    {
        $upload = $request->get('upload');
        $uploadRows = preg_split("/\r?\n/", $upload);
        $error_count = [];
        $ok_count = [];
        $now = date('Y-m-d H:i:s');

        $row = 0;
        foreach ($uploadRows as $uploadRow) {
            $uploadColumn = preg_split("/\t/", $uploadRow);
            $row++;

            $id = $uploadColumn[0];
            $qty = $uploadColumn[1];
            $reason = $uploadColumn[2];

            if (str_contains($id, 'ST_')) {

                $cek = StocktakingNewList::where('id', explode('_', $id)[1])->first();

                if ($cek) {
                    if (preg_match("/[a-z]/i", $qty)) {
                        array_push($error_count, $row . '#Qty data not number');
                    } else if ($qty < 0) {
                        array_push($error_count, $row . '#Qty < 0');
                    } else {
                        try {

                            $before = $cek->final_count;

                            $cek->process = 4;
                            $cek->final_count = $qty;
                            $cek->revised_by = strtoupper(Auth::user()->username);
                            $cek->revised_at = date('Y-m-d H:i:s');
                            $cek->reason = $reason;
                            $cek->save();

                            $insert_log = db::table('stocktaking_revise_logs')
                                ->insert([
                                    'st_id' => $cek->id,
                                    'area' => $cek->area,
                                    'location' => $cek->location,
                                    'store' => $cek->store,
                                    'sub_store' => $cek->sub_store,
                                    'material_number' => $cek->material_number,
                                    'material_description' => $cek->material_description,
                                    'category' => $cek->category,
                                    'before' => $before,
                                    'final_count' => $qty,
                                    'reason' => $reason,
                                    'revised_by' => strtoupper(Auth::user()->username),
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ]);

                            array_push($ok_count, 'ok');
                        } catch (QueryException $e) {
                            array_push($error_count, $row . '#' . $id . ' -> ' . substr($e->getMessage(), 0, 90));
                        }
                    }
                } else {
                    array_push($error_count, $row . '#ID ' . $id . ' not found in monitored material');
                }
            } else {
                array_push($error_count, $row . '#ID ' . $id . " doesnt match the format");
            }
        }

        $response = array(
            'status' => true,
            'id' => $id,
            'error_count' => $error_count,
            'ok_count' => $ok_count,
            'message' => 'ERROR: ' . count($error_count) . ' OK: ' . count($ok_count),
        );
        return Response::json($response);
    }

    public function updateRevise(Request $request)
    {
        $id = $request->get('id');
        $final_count = $request->get('quantity');
        $reason = $request->get('reason');

        $remark = '';
        if ($final_count > 0) {
            $remark = 'USE';
        } else {
            $remark = 'NO USE';
        }

        $material = StocktakingList::where('id', $id)->first();

        $process;
        if ($material->process == 0) {
            $process = 4;
        } else {
            $process = $material->process;
        }

        $quantity;
        if ($material->quantity == null) {
            $quantity = $final_count;
        } else {
            $quantity = $material->quantity;
        }

        try {

            $update = StocktakingList::where('id', $id)
                ->update([
                    'remark' => $remark,
                    'process' => $process,
                    'quantity' => $quantity,
                    'final_count' => $final_count,
                    'revised_by' => Auth::user()->username,
                    'reason' => $reason,
                ]);

            $response = array(
                'status' => true,
                'message' => 'Update Berhasil',
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function updateReviseNew(Request $request)
    {

        $id = $request->get('id');
        $ids = explode('_', $id);
        $final_count = $request->get('quantity');
        $reason = $request->get('reason');
        $remark = 'USE';

        $material = StocktakingNewList::where('id', $ids[1])->first();

        $process;
        if ($material->process == 0) {
            $process = 4;
        } else {
            $process = $material->process;
        }

        $quantity;
        if ($material->quantity == null) {
            $quantity = $final_count;
        } else {
            $quantity = $material->quantity;
        }

        try {

            $log = new StocktakingReviseLog([
                'st_id' => $material->id,
                'location' => $material->location,
                'store' => $material->store,
                'sub_store' => $material->sub_store,
                'material_number' => $material->material_number,
                'category' => $material->category,
                'before' => $material->final_count,
                'final_count' => $final_count,
                'revised_by' => Auth::user()->username,
                'reason' => $reason,
            ]);
            $log->save();

            $update = StocktakingNewList::where('id', $ids[1])
                ->update([
                    'remark' => $remark,
                    'process' => $process,
                    'quantity' => $quantity,
                    'final_count' => $final_count,
                    'revised_by' => Auth::user()->username,
                    'revised_at' => date('Y-m-d H:i:s'),
                    'reason' => $reason,
                ]);

            $response = array(
                'status' => true,
                'message' => 'Update Berhasil',
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

    }

    public function updateCount(Request $request)
    {

        $id = $request->get('id');
        $quantity = $request->get('quantity');
        $inputor = $request->get('inputor');

        try {

            $update = StocktakingList::where('id', $id)
                ->update([
                    'remark' => 'USE',
                    'process' => 1,
                    'quantity' => $quantity,
                    'inputed_by' => $inputor,
                ]);

            // $store = StocktakingList::where('id', $id)->first();
            // $updateStore = StocktakingList::where('store', $store->store)
            // ->update([
            //     'process' => 1
            // ]);

            $response = array(
                'status' => true,
                'message' => 'PI Berhasil Disimpan',
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function updateCountNew(Request $request)
    {

        $id = $request->get('id');
        $quantity = $request->get('quantity');
        $inputor = $request->get('inputor');

        $remark = 'NO USE';
        if ($quantity > 0) {
            $remark = 'USE';
        }

        DB::beginTransaction();
        try {
            $updatenew = StocktakingNewList::where('id', $id)
                ->update([
                    'remark' => $remark,
                    'process' => 1,
                    'print_status' => 1,
                    'quantity' => $quantity,
                    'inputed_by' => $inputor,
                    'inputed_at' => date('Y-m-d H:i:s'),
                ]);

            if ($remark == 'NO USE') {
                $storeID = StocktakingNewList::where('id', $id)->first();

                $stores = StocktakingNewList::where('store', $storeID->store)
                    ->where('print_status', 1)
                    ->get();

                $no_use = StocktakingNewList::where('store', $storeID->store)
                    ->where('print_status', 1)
                    ->where('remark', 'NO USE')
                    ->get();

                if (count($stores) == count($no_use)) {
                    $update = StocktakingNewList::where('store', $storeID->store)
                        ->update([
                            'process' => 2,
                            'final_count' => 0,
                        ]);
                }

            }

            DB::commit();
            $response = array(
                'status' => true,
                'message' => 'PI Berhasil Disimpan',
            );
            return Response::json($response);
        } catch (Exception $e) {
            DB::rollback();
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function deleteFstkPi(Request $request)
    {

        DB::beginTransaction();
        for ($i = 0; $i < count($request->get('stocktaking')); $i++) {
            try {

                $delete = db::connection('ympimis_2')
                    ->table('stocktaking_finish_goods')
                    ->where('id', $request->get('stocktaking')[$i]['id'])
                    ->delete();

            } catch (Exception $e) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }

        DB::commit();
        $response = array(
            'status' => true,
            'message' => 'PI Berhasil Disimpan',
        );
        return Response::json($response);

    }

    public function deleteMstkPi(Request $request)
    {

        // DB::beginTransaction();
        $delete = db::connection('ympimis_2')
            ->table('stocktaking_scraps')
            ->where('slip', $request->get('slip'))
            ->delete();

        // DB::commit();
        $response = array(
            'status' => true,
            'message' => 'PI Berhasil Disimpan',
        );
        return Response::json($response);

    }

    public function deleteStore(Request $request)
    {
        $store = $request->get('store');

        try {
            $delete = StocktakingList::where('store', $store)->forceDelete();

            $response = array(
                'status' => true,
                'message' => 'Delete Store Berhasil',
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function deleteMaterial(Request $request)
    {
        $id = $request->get('id');

        try {
            $delete = StocktakingList::where('id', $id)->forceDelete();

            $response = array(
                'status' => true,
                'message' => 'Delete Material Berhasil',
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function bom_output()
    {
        $title = 'BOM Output';
        $title_jp = '';

        return view('stocktakings.bomoutput', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'base_units' => $this->base_unit,
        ))->with('page', 'BOM Output')->with('head', 'BOM');
    }

    public function mpdl()
    {
        $title = 'Material Plant Data List';
        $title_jp = '';

        return view('stocktakings.mpdl', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'storage_locations' => $this->storage_location,
            'base_units' => $this->base_unit,
        ))->with('page', 'Material Plant Data List')->with('head', 'MPDL');
    }

    public function uploadFTP($from, $to)
    {
        $upload = FTP::connection()->uploadFile($from, $to, FTP_BINARY);
        return $upload;
    }

    public function writeString($text, $maxLength, $char)
    {
        if ($maxLength > 0) {
            $textLength = 0;
            if ($text != null) {
                $textLength = strlen($text);
            } else {
                $text = "";
            }
            for ($i = 0; $i < ($maxLength - $textLength); $i++) {
                $text .= $char;
            }
        }
        return strtoupper($text);
    }

    public function writeStringReserve($text, $maxLength, $char)
    {

        $output = '';

        if ($maxLength > 0) {
            $textLength = 0;
            if ($text != null) {
                $textLength = strlen($text);
            } else {
                $output = "";
            }

            for ($i = 0; $i < ($maxLength - $textLength); $i++) {
                $output .= $char;
            }

            if ($text != null) {
                $output .= $text;
            }
        }
        return strtoupper($output);
    }

    public function writeDecimal($text, $maxLength, $char)
    {
        $decimal = '';

        if ($maxLength > 0) {
            $textLength = 0;
            if ($text != null) {
                if (fmod($text, 1) > 0) {
                    $decimal = $this->decimal(round(fmod($text, 1), 3));
                    $decimalLength = strlen($decimal);
                    for ($j = 0; $j < (3 - $decimalLength); $j++) {
                        $decimal = $decimal . $char;
                    }
                } else {
                    $decimal = $char . $char . $char;
                }
                $textLength = strlen(floor($text));
                $text = floor($text);
            } else {
                $text = "";
            }
            for ($i = 0; $i < (($maxLength - 4) - $textLength); $i++) {
                $text = $char . $text;
            }
        }
        $text .= "." . $decimal;
        return $text;
    }

    public function writeDate($created_at, $type)
    {
        $datetime = strtotime($created_at);
        if ($type == "completion") {
            $text = date("dmY", $datetime);
            return $text;
        } else {
            $text = date("Ymd", $datetime);
            return $text;
        }
    }

    public function decimal($number)
    {
        $num = explode('.', $number);
        return $num[1];
    }

    //Stock Taking Silver
    public function indexSilver($id)
    {
        if ($id == 'fl_assembly') {
            $title = 'Silver Stock Taking (Flute Assembly)';
            $title_jp = 'FL組み立て職場の銀材棚卸';
            $location = 'FL ASSEMBLY';
        }

        if ($id == 'sx_assembly') {
            $title = 'Body Stock Taking (Saxophone Assembly)';
            $title_jp = 'SX組み立て職場の棚卸';
            $location = 'SX ASSEMBLY';
        }

        if ($id == 'fl_middle') {
            $title = 'Silver Stock Taking (Flute Middle)';
            $title_jp = 'FL中間工程の銀材棚卸';
            $location = 'FL MIDDLE';
        }

        if ($id == 'fl_welding') {
            $title = 'Silver Stock Taking (Flute Welding)';
            $title_jp = 'FL溶接職場の銀材棚卸';
            $location = 'FL WELDING';

        }

        if ($id == 'fl_bpro') {
            $title = 'Silver Stock Taking (Flute Body Process)';
            $title_jp = 'FL管体職場の銀材棚卸';
            $location = 'FL BODY PROCESS';
        }

        if ($id == 'fl_mpro') {
            $title = 'Silver Stock Taking (Flute Material Process)';
            $title_jp = 'FL部品加工職場の銀材棚卸';
            $location = 'FL MATERIAL PROCESS';
        }

        return view('stocktakings.silver', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'location' => $location,
        ))->with('page', 'Stock Taking')->with('head', 'Silver');
    }

    public function indexSilverReport()
    {
        $title = 'Silver Stocktaking Report';
        $title_jp = '銀材棚卸報告';

        return view('stocktakings.silver_report', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Stock Taking')->with('head', 'Silver');
    }

    public function fetchSilverReport(Request $request)
    {
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);

        if ($request->get('datefrom') != "") {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
        } else {
            $datefrom = date('Y-m-d', strtotime(Carbon::now()->subDays(14)));
        }

        if ($request->get('dateto') != "") {
            $dateto = date('Y-m-d', strtotime($request->get('dateto')));
        } else {
            $dateto = date('Y-m-d', strtotime(Carbon::now()->addDays(1)));
        }

        $bk = "select stock_date as order_date, date_format(stock_date, '%d-%b-%Y') as stock_date, storage_location, sum(variance) as variance, sum(ok) as ok from
        (
        select material_number, material_description, storage_location, if(round(sum(pi),3)-sum(book) <> 0, 1, 0) as variance, if(round(sum(pi),3)-sum(book) <> 0, 0, 1) as ok, stock_date from
        (
        select storage_location_stocks.material_number, storage_location_stocks.material_description, storage_location_stocks.storage_location, storage_location_stocks.unrestricted as book, 0 as pi, storage_location_stocks.stock_date from storage_location_stocks where storage_location_stocks.storage_location in (select distinct storage_location from stocktaking_silver_lists) and storage_location_stocks.material_number in (select distinct material_number from stocktaking_silver_lists) and storage_location_stocks.stock_date >= '" . $datefrom . "' and storage_location_stocks.stock_date <= '" . $dateto . "'

        union all

        select stocktaking_silver_logs.material_number, stocktaking_silver_logs.material_description, stocktaking_silver_logs.storage_location, 0 as book, stocktaking_silver_logs.quantity as pi, date(created_at) as stock_date from stocktaking_silver_logs where date(created_at) >= '" . $datefrom . "' and date(created_at) <= '" . $dateto . "') as variance group by material_number, material_description, storage_location, stock_date) as variance_count group by storage_location, stock_date order by order_date desc";

        $query = "SELECT date_location.week_date as order_date, date_format(date_location.week_date, '%d-%b-%Y') as stock_date, date_location.storage_location, COALESCE(variance.variance, 0) AS variance, COALESCE(variance.ok, 0) AS ok FROM
        (SELECT week_date, storage_location FROM
        (SELECT week_date FROM weekly_calendars
        WHERE week_date BETWEEN '" . $datefrom . "' AND '" . $dateto . "'
        AND remark <> 'H') calendar
        CROSS JOIN
        (SELECT DISTINCT storage_location FROM stocktaking_silver_lists) AS location) AS date_location
        LEFT JOIN
        (SELECT date( created_at ) AS date, log.storage_location, SUM(IF(ROUND(log.quantity,3) <> ROUND(log.book,3),1,0)) AS variance, SUM(IF(ROUND(log.quantity,3) = ROUND(log.book,3),1,0)) AS ok FROM stocktaking_silver_logs log
        WHERE date( created_at ) BETWEEN '" . $datefrom . "' AND '" . $dateto . "'
        GROUP BY date, log.storage_location) AS variance
        ON date_location.storage_location = variance.storage_location
        AND date_location.week_date = variance.date
        order by date_location.week_date desc";

        $variances = db::select($query);

        $response = array(
            'status' => true,
            'variances' => $variances,
        );
        return Response::json($response);
    }

    public function fetchSilverReportModal(Request $request)
    {
        $stock_date = date('Y-m-d', strtotime($request->get('date')));

        $loc = " having storage_location = '" . $request->get('loc') . "'";

        if ($request->get('loc') == 'all') {
            $loc = "";
        }

        $query = "SELECT material_number, material_description, storage_location, quantity AS pi, book, ROUND(quantity - book, 3) AS diff_qty, ROUND(ABS(quantity - book), 3) AS diff_abs, date( created_at ) AS stock_date
        FROM stocktaking_silver_logs
        WHERE date( created_at ) = '" . $stock_date . "'
        AND storage_location = '" . $request->get('loc') . "'
        ORDER BY diff_abs DESC, diff_qty ASC";

        $bk = "select material_number, material_description, storage_location, round(sum(pi),3) as pi, sum(book) as book, round(round(sum(pi),3)-sum(book),3) as diff_qty, round(ABS(round(sum(pi),3)-sum(book)),3) as diff_abs from
        (
        select storage_location_stocks.material_number, storage_location_stocks.material_description, storage_location_stocks.storage_location, storage_location_stocks.unrestricted as book, 0 as pi, storage_location_stocks.stock_date from storage_location_stocks where storage_location_stocks.storage_location in (select distinct storage_location from stocktaking_silver_lists) and storage_location_stocks.material_number in (select distinct material_number from stocktaking_silver_lists) and storage_location_stocks.stock_date = '" . $stock_date . "'

        union all

        select stocktaking_silver_logs.material_number, stocktaking_silver_logs.material_description, stocktaking_silver_logs.storage_location, 0 as book, stocktaking_silver_logs.quantity as pi, date(created_at) as stock_date from stocktaking_silver_logs where date(stocktaking_silver_logs.created_at) = '" . $stock_date . "') as variance
        group by material_number, material_description, storage_location
        " . $loc . "
        order by diff_abs desc, diff_qty asc";

        $variance = DB::select($query);

        $response = array(
            'status' => true,
            'variance' => $variance,
        );
        return Response::json($response);
    }

    public function fetchSilverCount(Request $request)
    {

        $count = StocktakingSilverList::where('stocktaking_silver_lists.id', '=', $request->get('id'))
            ->select('stocktaking_silver_lists.material_number', 'stocktaking_silver_lists.category', 'stocktaking_silver_lists.id', 'stocktaking_silver_lists.material_description', 'stocktaking_silver_lists.quantity_check')
            ->first();

        $response = array(
            'status' => true,
            'count' => $count,
        );
        return Response::json($response);
    }

    public function inputSilverCount(Request $request)
    {
        try {

            $count = StocktakingSilverList::where('stocktaking_silver_lists.id', '=', $request->get('id'))
                ->first();

            $count->quantity_check = $request->get('count');
            $count->save();

            $response = array(
                'status' => true,
                'message' => 'PI Count Confirmed',
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

    public function inputSilverFinalBackup(Request $request)
    {
        try {
            $now = date('Y-m-d');
            $id = Auth::id();

            $lists = StocktakingSilverList::where('location', '=', $request->get('location'))
                ->where('stocktaking_silver_lists.quantity_check', '>', 0)
                ->get();

            if (count($lists) <= 0) {
                $response = array(
                    'status' => false,
                    'message' => 'Resume stocktaking kosong.',
                );
                return Response::json($response);
            }

            $zero_quantity_final = DB::table('stocktaking_silver_lists')
                ->where('location', '=', $request->get('location'))
                ->update([
                    'quantity_final' => 0,
                ]);

            $delete_logs = DB::delete('delete from stocktaking_silver_logs where location = "' . $request->get('location') . '" and date(created_at) = "' . $now . '"');

            $update_final = DB::table('stocktaking_silver_lists')
                ->where('location', '=', $request->get('location'))
                ->where('stocktaking_silver_lists.quantity_check', '>', 0)
                ->update([
                    'quantity_final' => db::raw('quantity_check'),
                    'quantity_check' => 0,
                ]);

            $update_log_assy = DB::insert("insert into stocktaking_silver_logs (location, material_number, material_description, storage_location, quantity, created_by, created_at, updated_at)
                select location, material_child, material_child_description, storage_location, round(sum(quantity), 6) as quantity, '" . $id . "' as created_by, '" . date('Y-m-d H:i:s') . "' as created_at, '" . date('Y-m-d H:i:s') . "' as updated_at from
                (
                select stocktaking_silver_lists.location, stocktaking_silver_boms.material_child, stocktaking_silver_boms.material_child_description, stocktaking_silver_lists.storage_location, stocktaking_silver_lists.quantity_final*stocktaking_silver_boms.`usage` as quantity from stocktaking_silver_lists left join stocktaking_silver_boms on stocktaking_silver_boms.material_parent = stocktaking_silver_lists.material_number where stocktaking_silver_lists.quantity_final > 0 and stocktaking_silver_lists.location = '" . $request->get('location') . "' and stocktaking_silver_lists.category = 'ASSY'
            ) as assy group by location, material_child, material_child_description, storage_location");

            $update_log_single = DB::insert("insert into stocktaking_silver_logs (location, material_number, material_description, storage_location, quantity, created_by, created_at, updated_at)
                select location, material_number, material_description, storage_location, quantity, '" . $id . "' as created_by, '" . date('Y-m-d H:i:s') . "' as created_at, '" . date('Y-m-d H:i:s') . "' as updated_at from
                (
                select stocktaking_silver_lists.location, stocktaking_silver_lists.material_number, stocktaking_silver_lists.material_description, stocktaking_silver_lists.storage_location, round(sum(stocktaking_silver_lists.quantity_final),6) as quantity from stocktaking_silver_lists where stocktaking_silver_lists.quantity_final > 0 and stocktaking_silver_lists.location = '" . $request->get('location') . "' and stocktaking_silver_lists.category = 'SINGLE' group by stocktaking_silver_lists.location, stocktaking_silver_lists.material_number, stocktaking_silver_lists.material_description, stocktaking_silver_lists.storage_location
            ) as single");

            $response = array(
                'status' => true,
                'message' => 'PI Calculated',
                'lists' => $lists,
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

    public function inputSilverFinal(Request $request)
    {
        try {
            $now = date('Y-m-d');
            $timestamp = date('Y-m-d H:i:s');
            $id = Auth::id();

            $lists = StocktakingSilverList::where('location', '=', $request->get('location'))
                ->where('stocktaking_silver_lists.quantity_check', '>', 0)
                ->get();

            if (count($lists) <= 0) {
                $response = array(
                    'status' => false,
                    'message' => 'Resume stocktaking kosong.',
                );
                return Response::json($response);
            }

            $zero_quantity_final = DB::table('stocktaking_silver_lists')
                ->where('location', '=', $request->get('location'))
                ->update([
                    'quantity_final' => 0,
                ]);

            $delete_logs = DB::delete('delete from stocktaking_silver_logs where location = "' . $request->get('location') . '" and date(created_at) = "' . $now . '"');

            $update_final = DB::table('stocktaking_silver_lists')
                ->where('location', '=', $request->get('location'))
                ->where('stocktaking_silver_lists.quantity_check', '>', 0)
                ->update([
                    'quantity_final' => db::raw('quantity_check'),
                    'quantity_check' => 0,
                ]);

            $update_log = db::insert("INSERT INTO stocktaking_silver_logs (location, material_number, material_description, storage_location, quantity, book, created_by, created_at, updated_at)
                SELECT resume.location, resume.material_number, resume.material_description, resume.storage_location, ROUND(SUM(resume.pi), 3) AS quantity, SUM(resume.book) AS book , '" . $id . "' as created_by, '" . $timestamp . "' as created_at, '" . $timestamp . "' as updated_at FROM
                (SELECT list.location, bom.material_child AS material_number, bom.material_child_description AS material_description, list.storage_location, (list.quantity_final * bom.`usage`) AS pi, 0 AS book
                FROM stocktaking_silver_lists list LEFT JOIN stocktaking_silver_boms bom
                ON bom.material_parent = list.material_number
                WHERE list.quantity_final > 0
                AND list.location = '" . $request->get('location') . "'
                AND list.category = 'ASSY'
                UNION ALL
                SELECT list.location, list.material_number, list.material_description, list.storage_location, sum(list.quantity_final) AS pi, 0 AS book FROM stocktaking_silver_lists list
                WHERE list.quantity_final > 0
                AND list.location = '" . $request->get('location') . "'
                AND list.category = 'SINGLE'
                GROUP BY list.location, list.material_number, list.material_description, list.storage_location
                UNION ALL
                SELECT '" . $request->get('location') . "' AS location, stock.material_number, stock.material_description, stock.storage_location, 0 AS pi, stock.unrestricted AS book FROM storage_location_stocks stock
                WHERE stock.stock_date = '" . $now . "'
                AND stock.storage_location IN ( SELECT DISTINCT storage_location FROM stocktaking_silver_lists WHERE location = '" . $request->get('location') . "' )
                AND stock.material_number IN ( SELECT DISTINCT material_number FROM stocktaking_silver_lists )
                ) AS resume
                GROUP BY resume.location, resume.material_number, resume.material_description, resume.storage_location");

            $response = array(
                'status' => true,
                'message' => 'PI Calculated',
                'lists' => $lists,
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

    public function fetchSilverResume(Request $request)
    {

        $lists = StocktakingSilverList::where('location', '=', $request->get('location'))
            ->where(db::raw('stocktaking_silver_lists.quantity_check+stocktaking_silver_lists.quantity_final'), '>', 0)
            ->get();

        $response = array(
            'status' => true,
            'lists' => $lists,
        );
        return Response::json($response);
    }

    public function fetchSilverList(Request $request)
    {

        $lists = StocktakingSilverList::where('location', '=', $request->get('location'))
            ->select('stocktaking_silver_lists.material_number', 'stocktaking_silver_lists.category', 'stocktaking_silver_lists.id', 'stocktaking_silver_lists.material_description')
            ->get();

        $response = array(
            'status' => true,
            'lists' => $lists,
        );
        return Response::json($response);
    }

    //Stock Taking Daily
    public function indexDaily($id)
    {
        if ($id == 'sx_assembly') {
            $title = 'Body Stock Taking (Saxophone Assembly)';
            $title_jp = 'SX組み立て職場の棚卸';
            $location = 'SX ASSEMBLY';
        } else if ($id == 'fl_assembly') {
            $title = 'Body Stock Taking (Flute Assembly)';
            $title_jp = 'FL組み立て職場の棚卸';
            $location = 'FL ASSEMBLY';
        }

        return view('stocktakings.daily', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'location' => $location,
        ))->with('page', 'Stock Taking')->with('head', 'Daily');
    }

    public function indexDailyReport()
    {
        $title = 'Daily Stocktaking Report';
        $title_jp = '日次棚卸し報告';

        return view('stocktakings.daily_report', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Stock Taking')->with('head', 'Daily');
    }

    public function indexVideoPakUra()
    {
        $title = 'Video ';
        $title_jp = '';

        return view('stocktakings.video', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Video')->with('head', 'Stock Taking');
    }

    public function fetchDailyReport(Request $request)
    {
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);

        if ($request->get('datefrom') != "") {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
        } else {
            $datefrom = date('Y-m-d', strtotime(Carbon::now()->subDays(14)));
        }

        if ($request->get('dateto') != "") {
            $dateto = date('Y-m-d', strtotime($request->get('dateto')));
        } else {
            $dateto = date('Y-m-d', strtotime(Carbon::now()->addDays(1)));
        }

        $query = "select stock_date as order_date, date_format(stock_date, '%d-%b-%Y') as stock_date, storage_location, sum(variance) as variance, sum(ok) as ok from
        (
        select material_number, material_description, storage_location, if(round(sum(pi),3)-sum(book) <> 0, 1, 0) as variance, if(round(sum(pi),3)-sum(book) <> 0, 0, 1) as ok, stock_date from
        (
        select storage_location_stocks.material_number, storage_location_stocks.material_description, storage_location_stocks.storage_location, storage_location_stocks.unrestricted as book, 0 as pi, storage_location_stocks.stock_date from storage_location_stocks where storage_location_stocks.storage_location in (select distinct storage_location from stocktaking_daily_lists) and storage_location_stocks.material_number in (select distinct material_number from stocktaking_daily_lists) and storage_location_stocks.stock_date >= '" . $datefrom . "' and storage_location_stocks.stock_date <= '" . $dateto . "'

        union all

        select stocktaking_daily_logs.material_number, stocktaking_daily_logs.material_description, stocktaking_daily_logs.storage_location, 0 as book, stocktaking_daily_logs.quantity as pi, date(created_at) as stock_date from stocktaking_daily_logs where date(created_at) >= '" . $datefrom . "' and date(created_at) <= '" . $dateto . "') as variance group by material_number, material_description, storage_location, stock_date) as variance_count group by storage_location, stock_date order by order_date desc";

        $variances = db::select($query);

        $response = array(
            'status' => true,
            'variances' => $variances,
        );
        return Response::json($response);
    }

    public function fetchDailyReportModal(Request $request)
    {
        $stock_date = date('Y-m-d', strtotime($request->get('date')));

        $loc = " having storage_location = '" . $request->get('loc') . "'";

        if ($request->get('loc') == 'all') {
            $loc = "";
        }

        $query = "
        select material_number, material_description, storage_location, round(sum(pi),3) as pi, sum(book) as book, round(round(sum(pi),3)-sum(book),3) as diff_qty, round(ABS(round(sum(pi),3)-sum(book)),3) as diff_abs from
        (
        select storage_location_stocks.material_number, storage_location_stocks.material_description, storage_location_stocks.storage_location, storage_location_stocks.unrestricted as book, 0 as pi, storage_location_stocks.stock_date from storage_location_stocks where storage_location_stocks.storage_location in (select distinct storage_location from stocktaking_daily_lists) and storage_location_stocks.material_number in (select distinct material_number from stocktaking_daily_lists) and storage_location_stocks.stock_date = '" . $stock_date . "'

        union all

        select stocktaking_daily_logs.material_number, stocktaking_daily_logs.material_description, stocktaking_daily_logs.storage_location, 0 as book, stocktaking_daily_logs.quantity as pi, date(created_at) as stock_date from stocktaking_daily_logs where date(stocktaking_daily_logs.created_at) = '" . $stock_date . "') as variance
        group by material_number, material_description, storage_location
        " . $loc . "
        order by diff_abs desc, diff_qty asc";

        $variance = DB::select($query);

        $response = array(
            'status' => true,
            'variance' => $variance,
        );
        return Response::json($response);
    }

    public function fetchDailyCount(Request $request)
    {

        $count = StocktakingDailyList::where('stocktaking_daily_lists.id', '=', $request->get('id'))
            ->select('stocktaking_daily_lists.material_number', 'stocktaking_daily_lists.category', 'stocktaking_daily_lists.id', 'stocktaking_daily_lists.material_description', 'stocktaking_daily_lists.quantity_check')
            ->first();

        $response = array(
            'status' => true,
            'count' => $count,
        );
        return Response::json($response);
    }

    public function inputFstkPi(Request $request)
    {
        $pi = $request->get('pi');
        $now = date('Y-m-d H:i:s');

        DB::beginTransaction();
        for ($i = 0; $i < count($pi); $i++) {

            try {

                $insert = db::connection('ympimis_2')
                    ->table('stocktaking_finish_goods')
                    ->insert([
                        'category' => 'PI',
                        'location' => 'FSTK',
                        'material_number' => $pi[$i]['material_number'],
                        'material_description' => $pi[$i]['material_description'],
                        'ymes' => 0,
                        'mirai' => 0,
                        'pi' => $pi[$i]['quantity'],
                        'remark' => $pi[$i]['slip'],
                        'created_by' => $request->get('employee_id'),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

            } catch (Exception $e) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }

        }

        DB::commit();
        $response = array(
            'status' => true,
        );
        return Response::json($response);

    }

    public function inputMstkPi(Request $request)
    {
        $pi = $request->get('pi');
        try {
            $cek_data = db::connection('ympimis_2')->select('select slip from stocktaking_scraps where slip = "' . $pi[0]['slip'] . '"');
            $cek_logs = db::select('select slip, quantity, receive_location from scrap_logs where slip = "' . $pi[0]['slip'] . '" and receive_location in ("MSCR", "WSCR", "OTHR", "MMJR")');

            if (count($cek_data) > 0) {
                $update_pi = db::connection('ympimis_2')->table('stocktaking_scraps')
                    ->where('slip', '=', $pi[0]['slip'])
                    ->update([
                        'pi' => $pi[0]['quantity'],
                        'created_by' => $request->get('employee_id'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                $response = array(
                    'status' => true,
                    'message' => 'PI Berhasil Di Update',
                );
                return Response::json($response);
            } else {
                $input_inspection = db::connection('ympimis_2')
                    ->table('stocktaking_scraps')
                    ->insert([
                        'category' => 'PI',
                        'location' => $cek_logs[0]->receive_location,
                        'gmc' => $pi[0]['material_number'],
                        'slip' => $pi[0]['slip'],
                        'description' => $pi[0]['material_description'],
                        'mirai' => $cek_logs[0]->quantity,
                        'pi' => $pi[0]['quantity'],
                        'created_by' => $request->get('employee_id'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                $response = array(
                    'status' => true,
                    'message' => 'PI Berhasil Di Input',
                );
                return Response::json($response);
            }
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function inputDailyCount(Request $request)
    {
        try {

            $count = StocktakingDailyList::where('stocktaking_daily_lists.id', '=', $request->get('id'))
                ->first();

            $count->quantity_check = $request->get('count');
            $count->save();

            $response = array(
                'status' => true,
                'message' => 'PI Count Confirmed',
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

    public function inputDailyFinal(Request $request)
    {
        try {
            $now = date('Y-m-d');
            $id = Auth::id();

            $lists = StocktakingDailyList::where('location', '=', $request->get('location'))
                ->where('stocktaking_daily_lists.quantity_check', '>', 0)
                ->get();

            if (count($lists) <= 0) {
                $response = array(
                    'status' => false,
                    'message' => 'Resume stocktaking kosong.',
                );
                return Response::json($response);
            }

            $zero_quantity_final = DB::table('stocktaking_daily_lists')
                ->where('location', '=', $request->get('location'))
                ->update([
                    'quantity_final' => 0,
                ]);

            $delete_logs = DB::delete('delete from stocktaking_daily_logs where location = "' . $request->get('location') . '" and date(created_at) = "' . $now . '"');

            $update_final = DB::table('stocktaking_daily_lists')
                ->where('location', '=', $request->get('location'))
                ->where('stocktaking_daily_lists.quantity_check', '>', 0)
                ->update([
                    'quantity_final' => db::raw('quantity_check'),
                    'quantity_check' => 0,
                ]);

            $update_log_assy = DB::insert("insert into stocktaking_daily_logs (location, material_number, material_description, storage_location, quantity, created_by, created_at, updated_at)
                select location, material_child, material_child_description, storage_location, round(sum(quantity), 6) as quantity, '" . $id . "' as created_by, '" . date('Y-m-d H:i:s') . "' as created_at, '" . date('Y-m-d H:i:s') . "' as updated_at from
                (
                select stocktaking_daily_lists.location, stocktaking_daily_boms.material_child, stocktaking_daily_boms.material_child_description, stocktaking_daily_lists.storage_location, stocktaking_daily_lists.quantity_final*stocktaking_daily_boms.`usage` as quantity from stocktaking_daily_lists left join stocktaking_daily_boms on stocktaking_daily_boms.material_parent = stocktaking_daily_lists.material_number where stocktaking_daily_lists.quantity_final > 0 and stocktaking_daily_lists.location = '" . $request->get('location') . "' and stocktaking_daily_lists.category = 'ASSY'
            ) as assy group by location, material_child, material_child_description, storage_location");

            $update_log_single = DB::insert("insert into stocktaking_daily_logs (location, material_number, material_description, storage_location, quantity, created_by, created_at, updated_at)
                select location, material_number, material_description, storage_location, quantity, '" . $id . "' as created_by, '" . date('Y-m-d H:i:s') . "' as created_at, '" . date('Y-m-d H:i:s') . "' as updated_at from
                (
                select stocktaking_daily_lists.location, stocktaking_daily_lists.material_number, stocktaking_daily_lists.material_description, stocktaking_daily_lists.storage_location, round(sum(stocktaking_daily_lists.quantity_final),6) as quantity from stocktaking_daily_lists where stocktaking_daily_lists.quantity_final > 0 and stocktaking_daily_lists.location = '" . $request->get('location') . "' and stocktaking_daily_lists.category = 'SINGLE' group by stocktaking_daily_lists.location, stocktaking_daily_lists.material_number, stocktaking_daily_lists.material_description, stocktaking_daily_lists.storage_location
            ) as single");

            $response = array(
                'status' => true,
                'message' => 'PI Calculated',
                'lists' => $lists,
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

    public function fetchDailyResume(Request $request)
    {

        $lists = StocktakingDailyList::where('location', '=', $request->get('location'))
            ->where(db::raw('stocktaking_daily_lists.quantity_check+stocktaking_daily_lists.quantity_final'), '>', 0)
            ->get();

        $response = array(
            'status' => true,
            'lists' => $lists,
        );
        return Response::json($response);
    }

    public function fetchDailyList(Request $request)
    {

        $lists = StocktakingDailyList::where('location', '=', $request->get('location'))
            ->select('stocktaking_daily_lists.material_number', 'stocktaking_daily_lists.category', 'stocktaking_daily_lists.id', 'stocktaking_daily_lists.material_description')
            ->get();

        $response = array(
            'status' => true,
            'lists' => $lists,
        );
        return Response::json($response);
    }

    //YMES
    public function indexYmesStocktakingList()
    {

        $title = "YMES Stocktaking List";
        $title_jp = "";

        $ymes_lists = db::table('stocktaking_ymes_lists')
            ->orderby('list_no', 'ASC')
            ->orderby('slip_no', 'ASC')
        // ->limit(100)
            ->get();

        $resumes = db::table('stocktaking_ymes_lists')
            ->select(
                'list_no',
                'location',
                db::raw('count(id) AS count_slip')
            )
            ->groupBy('list_no', 'location')
            ->orderBy('list_no', 'ASC')
            ->get();

        return view('stocktakings.ymes.ymes_list', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'ymes_lists' => $ymes_lists,
            'resumes' => $resumes,
        ))->with('page', 'Monthly Stock Taking')->with('head', 'Stocktaking');

    }

    public function uploadYmesToMirai(Request $request)
    {

        $stocktake_date = $request->get('stocktake_date');
        $storage_location = $request->get('storage_location');

        $ymes_lists = db::connection('ymes_rio')
            ->table('vd_mes1020')
            ->where('stocktake_date', $stocktake_date)
            ->whereIn('location_code', $storage_location)
            ->select(
                'item_code',
                'location_code',
                db::raw('SUM(input_stock_qty + input_inspect_qty + input_keep_qty) AS qty')
            )
            ->groupBy('item_code', 'location_code')
            ->get();

        $mpdl = MaterialPlantDataList::get();
        $mpdl = $mpdl->keyBy('material_number');

        $sloc_master = StorageLocation::get();
        $sloc_master = $sloc_master->keyBy('storage_location');

        DB::beginTransaction();
        try {

            $delete_mirai = db::table('stocktaking_new_lists')
                ->where('location', $storage_location)
                ->delete();

            for ($i = 0; $i < count($ymes_lists); $i++) {
                $list = new StocktakingNewList([
                    'area' => $sloc_master[$ymes_lists[$i]->location_code]->area,
                    'location' => $ymes_lists[$i]->location_code,
                    'store' => $ymes_lists[$i]->location_code,
                    'sub_store' => $ymes_lists[$i]->location_code,
                    'material_number' => $ymes_lists[$i]->item_code,
                    'material_description' => $mpdl[$ymes_lists[$i]->item_code]->material_description,
                    'category' => 'SINGLE',
                    'process' => 4,
                    'print_status' => 1,
                    'remark' => 'USE',
                    'quantity' => $ymes_lists[$i]->qty,
                    'inputed_by' => strtoupper(Auth::user()->username),
                    'inputed_at' => date('Y-m-d H:i:s'),
                    'final_count' => $ymes_lists[$i]->qty,
                    'created_by' => Auth::id(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $list->save();

            }

            DB::commit();
            $response = array(
                'status' => true,
                'message' => 'Export PI YMES to MIRAI success',
            );
            return Response::json($response);

        } catch (Exception $e) {
            DB::rollback();
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

    }

    public function uploadYmesStocktakingList(Request $request)
    {
        $now = date('Y-m-d H:i:s');

        $list_inserted = [];
        $lists = db::table('stocktaking_ymes_lists')
            ->select('list_no')
            ->distinct()
            ->get();

        for ($i = 0; $i < count($lists); $i++) {
            array_push($list_inserted, $lists[$i]->list_no);
        }

        if ($request->hasFile('file_list')) {

            DB::beginTransaction();
            $file = $request->file('file_list');
            $file_name = 'ymes_list_' . Auth::id() . '(' . date("ymdHi") . ')' . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/stocktaking_list/'), $file_name);

            $excel = public_path('uploads/stocktaking_list/') . $file_name;
            $rows = Excel::load($excel, function ($reader) {
                $reader->noHeading();
                $reader->skipRows(1);
            })->get();
            $rows = $rows->toArray();

            $count = 0;
            for ($i = 0; $i < count($rows); $i++) {
                $count++;
                $list_no = preg_replace('/[^a-zA-Z0-9]+/', '', strtoupper($rows[$i][20]));
                $slip_no = preg_replace('/[^a-zA-Z0-9]+/', '', strtoupper($rows[$i][19]));
                $location = preg_replace('/[^a-zA-Z0-9]+/', '', strtoupper($rows[$i][3]));
                $material_number = preg_replace('/[^a-zA-Z0-9]+/', '', strtoupper($rows[$i][7]));
                $material_description = strtoupper($rows[$i][8]);
                $valcl = preg_replace('/[^a-zA-Z0-9]+/', '', strtoupper($rows[$i][9]));
                $category = preg_replace('/[^a-zA-Z0-9]+/', '', strtoupper($rows[$i][18]));
                $uom = preg_replace('/[^a-zA-Z0-9]+/', '', strtoupper($rows[$i][17]));
                $plant_spitem_status = preg_replace('/[^a-zA-Z0-9]+/', '', strtoupper($rows[$i][27]));
                $created_by = Auth::id();

                if (in_array($list_no, $list_inserted)) {
                    DB::rollback();
                    $response = array(
                        'status' => false,
                        'message' => $list_no . ' sudah ditambahkan',
                        'count' => $count,
                    );
                    return Response::json($response);
                }

                try {
                    $insert = db::table('stocktaking_ymes_lists')
                        ->insert([
                            'list_no' => $list_no,
                            'slip_no' => $slip_no,
                            'location' => $location,
                            'material_number' => $material_number,
                            'material_description' => $material_description,
                            'plant_spitem_status' => $plant_spitem_status,
                            'valcl' => $valcl,
                            'category' => $category,
                            'uom' => $uom,
                            'created_by' => $created_by,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);

                } catch (\Exception$e) {
                    DB::rollback();
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                        'count' => $count,
                    );
                    return Response::json($response);
                }

            }

            DB::commit();
            $response = array(
                'status' => true,
                'message' => 'File berhasil ditambahkan ke list',
                'count' => $count,
            );
            return Response::json($response);

        } else {
            $response = array(
                'status' => false,
                'message' => 'Upload gagal, File tidak ditemukan',
                'count' => 0,
            );
            return Response::json($response);
        }
    }

    public function deleteYmesStocktakingList(Request $request)
    {

        $upload = $request->get('delete_list_no');
        $uploadRows = preg_split("/\r?\n/", $upload);

        $list_no = [];
        foreach ($uploadRows as $uploadRow) {
            if (!in_array($uploadRow, $list_no)) {
                array_push($list_no, $uploadRow);
            }
        }

        $lists = db::table('stocktaking_ymes_lists')
            ->whereIn('list_no', $list_no)
            ->get();

        try {

            $delete = db::table('stocktaking_ymes_lists')
                ->whereIn('list_no', $list_no)
                ->delete();

            $response = array(
                'status' => true,
                'message' => 'Data berhasil dihapus',
                'count' => count($lists),
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

    public function fetchUnmatchYmesList()
    {

        $unmatch = db::select("
            SELECT
                stocktaking_ymes_lists.list_no,
                stocktaking_new_lists.location,
                stocktaking_new_lists.material_number,
                stocktaking_new_lists.material_description,
                stocktaking_new_lists.category,
                SUM( stocktaking_new_lists.final_count ) AS final_count
            FROM
                stocktaking_new_lists
            LEFT JOIN stocktaking_ymes_lists
                ON stocktaking_new_lists.location = stocktaking_ymes_lists.location
                AND stocktaking_new_lists.material_number = stocktaking_ymes_lists.material_number
                AND stocktaking_new_lists.category = stocktaking_ymes_lists.category
            WHERE
                stocktaking_new_lists.final_count > 0
                AND stocktaking_ymes_lists.list_no IS NULL
            GROUP BY
                stocktaking_ymes_lists.list_no,
                stocktaking_new_lists.location,
                stocktaking_new_lists.material_number,
                stocktaking_new_lists.material_description,
                stocktaking_new_lists.category");

        return DataTables::of($unmatch)->make(true);

    }

    public function fetchExportYmesList(Request $request)
    {
        $slip_data = db::select("
            SELECT
                ymes.list_no,
                ymes.location,
                ymes.material_number,
                ymes.material_description,
                ymes.slip_no,
                ymes.category,
                COALESCE ( stocktaking.qty, 0 ) AS quantity
            FROM
                stocktaking_ymes_lists ymes
                LEFT JOIN
                ( SELECT location, material_number, category, SUM( final_count ) AS qty FROM stocktaking_new_lists
                    GROUP BY location, material_number, category ) AS stocktaking
                ON ymes.location = stocktaking.location
                AND ymes.material_number = stocktaking.material_number
                AND ymes.category = stocktaking.category
            WHERE
                ymes.list_no = '" . $request->get('list_no') . "'
            ORDER BY ymes.slip_no");

        $tittle = $request->get('list_no');

        $data = array(
            'slip_data' => $slip_data,
        );

        ob_clean();
        Excel::create($tittle, function ($excel) use ($data) {
            $excel->sheet('YMESInquiry', function ($sheet) use ($data) {
                return $sheet->loadView('stocktakings.ymes.ymes_export_list', $data);
            });
        })->export('xlsx');

    }

    public function fetchPrintYmesList(Request $request)
    {
        $month = $request->get('hidden_month');
        $list_no = $request->get('hidden_list_no');

        $st = db::table('stocktaking_calendars')
            ->where('date', 'LIKE', '%' . $month . '%')
            ->select(
                db::raw('date_format(stocktaking_calendars.date, "%d/%m/%Y") AS date'),
                db::raw('date_format(stocktaking_calendars.date, "%M %Y") AS text')
            )
            ->first();

        $report = db::select("
            SELECT
                storage_locations.area,
                storage_locations.location,
                stocktaking_ymes_lists.location AS storage_location,
                stocktaking_ymes_lists.list_no,
                stocktaking_ymes_lists.slip_no,
                stocktaking_ymes_lists.valcl,
                stocktaking_ymes_lists.material_number,
                stocktaking_ymes_lists.material_description,
                stocktaking_ymes_lists.uom,
                stocktaking_ymes_lists.category,
                date_format(stocktaking_ymes_lists.created_at, '%d/%m/%Y') AS created_at,
                COALESCE(mirai.quantity, 0) AS quantity,
                COALESCE(mirai.final, 0) AS final,
                COALESCE(auditor.`name`,'') AS auditor
            FROM stocktaking_ymes_lists
            LEFT JOIN
                (SELECT location, material_number, category, SUM(quantity) AS quantity, SUM(final_count) AS final FROM stocktaking_new_lists
                GROUP BY location, material_number, category) AS mirai
            ON mirai.location = stocktaking_ymes_lists.location
            AND mirai.material_number = stocktaking_ymes_lists.material_number
            AND mirai.category = stocktaking_ymes_lists.category
            LEFT JOIN
                (SELECT location, material_number, category, GROUP_CONCAT(employees.`name`) AS `name` FROM
                (SELECT DISTINCT location, material_number, category, audit1_by FROM stocktaking_new_lists
                WHERE stocktaking_new_lists.audit1_by IS NOT NULL) AS audit
            LEFT JOIN
                (SELECT employee_id, concat(SPLIT_STRING(`name`, ' ', 1), ' ', SPLIT_STRING(`name`, ' ', 2)) AS `name` FROM employee_syncs
                WHERE end_date IS NULL) AS employees
                ON audit.audit1_by = employees.employee_id
                GROUP BY location, material_number, category) AS auditor
            ON auditor.location = stocktaking_ymes_lists.location
            AND auditor.material_number = stocktaking_ymes_lists.material_number
            AND auditor.category = stocktaking_ymes_lists.category
            LEFT JOIN storage_locations
                ON storage_locations.storage_location = stocktaking_ymes_lists.location
            WHERE stocktaking_ymes_lists.list_no = '" . $list_no . "'");

        $data = array(
            'st' => $st,
            'report' => $report,
        );

        ob_clean();
        Excel::create('Report_' . $list_no, function ($excel) use ($data) {
            $excel->sheet('YMESInquiry', function ($sheet) use ($data) {
                return $sheet->loadView('stocktakings.ymes.ymes_export_report', $data);
            });
        })->export('xlsx');

        // $pdf = \App::make('dompdf.wrapper');
        // $pdf->getDomPDF()->set_option("enable_php", true);
        // $pdf->setPaper('A4', 'landscape');
        // $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        // return view('stocktakings.ymes.pdf_list_no', array(
        //     'report' => $report,
        //     'st' => $st,
        // ));

        // $pdf->loadView('stocktakings.ymes.pdf_list_no', array(
        //     'report' => $report,
        //     'st' => $st,
        // ));
        // return $pdf->stream($list_no . ".pdf");

    }

    public function inputReviseUser(Request $request)
    {

        $now = date('Y-m-d H:i:s');
        $now_file_format = date('ymdHis');

        $st_id = $request->input('st_id');
        $quantity = $request->input('quantity');
        $reason = $request->input('reason');
        $note = $request->input('note');
        $extension = $request->input('extension');
        $photo_name = $request->input('photo_name');

        $server = $_SERVER['SERVER_ADDR'];

        if ($server != '10.109.52.4') {
            $response = array(
                'status' => false,
                'message' => 'Untuk revisi PI, wajib menggunakan server IP 10.109.52.4',
            );
            return Response::json($response);
        }

        DB::beginTransaction();
        try {

            $directory = 'files\stocktaking\revise_evidence';

            $file = $request->file('file_datas');
            $name = $file->getClientOriginalName();
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $filename = $now_file_format . '_' . $st_id . '.' . $extension;
            $file->move($directory, $filename);

            $item = db::table('stocktaking_new_lists')
                ->where('id', $st_id)
                ->first();

            $insert = db::table('stocktaking_revises')
                ->insert([
                    'st_id' => $item->id,
                    'area' => $item->area,
                    'location' => $item->location,
                    'store' => $item->store,
                    'sub_store' => $item->sub_store,
                    'material_number' => $item->material_number,
                    'material_description' => $item->material_description,
                    'category' => $item->category,
                    'status' => 'REQUESTED',
                    'before' => $item->final_count,
                    'quantity' => $quantity,
                    'evidence' => $filename,
                    'reason' => $reason,
                    'note' => strtoupper($note),
                    'created_by' => strtoupper(Auth::user()->username),
                    'created_by_name' => strtoupper(Auth::user()->name),
                    'created_at' => $now,
                ]);

            DB::commit();
            $response = array(
                'status' => true,
            );
            return Response::json($response);

        } catch (\Exception$e) {
            DB::rollback();
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);

        }

    }

    public function inputReviseCheck(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');
        $note = $request->input('note');
        $now = date('Y-m-d H:i:s');

        try {
            if ($status == 'REJECTED') {
                $update_revise = db::table('stocktaking_revises')
                    ->where('id', $id)
                    ->update([
                        'status' => 'REJECTED',
                        'note' => strtoupper($note),
                        'checked_by' => strtoupper(Auth::user()->username),
                        'checked_by_name' => strtoupper(Auth::user()->name),
                        'checked_at' => $now,
                    ]);

            } else {
                $update = db::table('stocktaking_revises')
                    ->where('id', $id)
                    ->update([
                        'status' => 'CHECKED',
                        'note' => strtoupper($note),
                        'checked_by' => strtoupper(Auth::user()->username),
                        'checked_by_name' => strtoupper(Auth::user()->name),
                        'checked_at' => $now,
                    ]);

            }

            $response = array(
                'status' => true,
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

    public function inputReviseExecute(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');
        $note = $request->input('note');
        $now = date('Y-m-d H:i:s');

        $revise_data = db::table('stocktaking_revises')
            ->where('id', $id)
            ->first();

        DB::beginTransaction();

        try {

            if ($status == 'REJECTED') {

                if (strlen($note) <= 0) {
                    DB::rollback();
                    $response = array(
                        'status' => false,
                        'message' => 'Reject harus dengan memasukan catatan',
                    );
                    return Response::json($response);
                }

                $update_revise = db::table('stocktaking_revises')
                    ->where('id', $id)
                    ->update([
                        'status' => 'REJECTED',
                        'note' => strtoupper($note),
                        'revised_by' => strtoupper(Auth::user()->username),
                        'revised_by_name' => strtoupper(Auth::user()->name),
                        'revised_at' => $now,
                    ]);

                $to = '';

                $body = '';
                $body .= '<center>';
                $body .= '<p class="status" style="color: #fc4439; font-weight: bold;"></i>REVISI SLIP STOCKTAKING DI TOLAK!</p>';
                $body .= '<br>';

                $body .= '<table style="border:1px solid black; border-collapse: collapse; width: 60%; font-size: 10pt;">';
                $body .= '<thead>';

                $body .= '<tr>';
                $body .= '<th style="border:1px solid black; background-color: #63ccff; vertical-align: top;">Store</th>';
                $body .= '<th style="border:1px solid black; background-color: #ffffff; text-align: left; padding-left: 3px;">ST_' . $revise_data->st_id . '</th>';
                $body .= '</tr>';

                $body .= '<tr>';
                $body .= '<th style="border:1px solid black; background-color: #63ccff; vertical-align: top;">Location</th>';
                $body .= '<th style="border:1px solid black; background-color: #ffffff; text-align: left; padding-left: 3px;">' . $revise_data->area . ' - ' . $revise_data->location . '</th>';
                $body .= '</tr>';

                $body .= '<tr>';
                $body .= '<th style="border:1px solid black; background-color: #63ccff; vertical-align: top;">Store</th>';
                $body .= '<th style="border:1px solid black; background-color: #ffffff; text-align: left; padding-left: 3px;">' . $revise_data->store . '</th>';
                $body .= '</tr>';

                $body .= '<tr>';
                $body .= '<th style="border:1px solid black; background-color: #63ccff; vertical-align: top;">Sub store</th>';
                $body .= '<th style="border:1px solid black; background-color: #ffffff; text-align: left; padding-left: 3px;">' . $revise_data->sub_store . '</th>';
                $body .= '</tr>';

                $body .= '<tr>';
                $body .= '<th style="border:1px solid black; background-color: #63ccff; vertical-align: top;">Material</th>';
                $body .= '<th style="border:1px solid black; background-color: #ffffff; text-align: left; padding-left: 3px;">' . $revise_data->material_number . '<br>' . $revise_data->material_description . '</th>';
                $body .= '</tr>';

                $body .= '<tr>';
                $body .= '<th style="border:1px solid black; background-color: #63ccff; vertical-align: top;">Category</th>';
                $body .= '<th style="border:1px solid black; background-color: #ffffff; text-align: left; padding-left: 3px;">' . $revise_data->category . '</th>';
                $body .= '</tr>';

                $body .= '<tr>';
                $body .= '<th style="border:1px solid black; background-color: #63ccff; vertical-align: top;">Qty Before</th>';
                $body .= '<th style="border:1px solid black; background-color: #ffffff; text-align: left; padding-left: 3px;">' . $revise_data->before . '</th>';
                $body .= '</tr>';

                $body .= '<tr>';
                $body .= '<th style="border:1px solid black; background-color: #63ccff; vertical-align: top;">Qty After</th>';
                $body .= '<th style="border:1px solid black; background-color: #ffffff; text-align: left; padding-left: 3px;">' . $revise_data->quantity . '</th>';
                $body .= '</tr>';

                $body .= '<tr>';
                $body .= '<th style="border:1px solid black; background-color: #63ccff; vertical-align: top;">Reason</th>';
                $body .= '<th style="border:1px solid black; background-color: #ffffff; text-align: left; padding-left: 3px;">' . strtoupper($revise_data->reason) . '</th>';
                $body .= '</tr>';

                $body .= '<tr>';
                $body .= '<th style="border:1px solid black; background-color: #63ccff; vertical-align: top;">Note</th>';
                $body .= '<th style="border:1px solid black; background-color: #ffffff; text-align: left; padding-left: 3px;">' . strtoupper($note) . '</th>';
                $body .= '</tr>';

                $body .= '</thead>';
                $body .= '</table>';

                $evidence_file = public_path() . '/files/stocktaking/revise_evidence/' . $revise_data->evidence;
                $evidence_file_exist = file_exists($evidence_file);
                if ($evidence_file_exist) {
                    $body .= '<br>';
                    $body .= '<img style="width: 300px; height: 300px;" src="data:image/png;base64,' . base64_encode(file_get_contents(public_path('/files/stocktaking/revise_evidence/' . $revise_data->evidence))) . '"><br>';
                }

                $body .= '</center>';

                $user = db::table('users')
                    ->where('username', $revise_data->checked_by)
                    ->first();

                $to = Auth::user()->email;
                if (str_contains($user->email, 'music.yamaha.com')) {
                    $to = $user->email;
                }

                $this->mailReport($body, $to);

            } else {
                $update_tb = db::table('stocktaking_new_lists')
                    ->where('id', $revise_data->st_id)
                    ->update([
                        'process' => 4,
                        'remark' => 'USE',
                        'final_count' => $revise_data->quantity,
                        'revised_by' => strtoupper(Auth::user()->username),
                        'revised_at' => $now,
                        'reason' => $revise_data->reason,
                    ]);

                $revise = db::table('stocktaking_revises')
                    ->where('id', $id)
                    ->first();

                $update_revise = db::table('stocktaking_revises')
                    ->where('id', $id)
                    ->update([
                        'status' => 'UPDATED',
                        'note' => strtoupper($note),
                        'revised_by' => strtoupper(Auth::user()->username),
                        'revised_by_name' => strtoupper(Auth::user()->name),
                        'revised_at' => $now,
                    ]);

                $insert_log = db::table('stocktaking_revise_logs')
                    ->insert([
                        'st_id' => $revise->st_id,
                        'area' => $revise->area,
                        'location' => $revise->location,
                        'store' => $revise->store,
                        'sub_store' => $revise->sub_store,
                        'material_number' => $revise->material_number,
                        'material_description' => $revise->material_description,
                        'category' => $revise->category,
                        'before' => $revise->before,
                        'final_count' => $revise->quantity,
                        'reason' => $revise->reason,
                        'evidence' => $revise->evidence,
                        'revised_by' => $revise->created_by,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

            }

            DB::commit();
            $response = array(
                'status' => true,
            );
            return Response::json($response);

        } catch (\Exception$e) {
            DB::rollback();
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);

        }
    }

    public function fetchReviseUser()
    {
        $data = db::table('stocktaking_revises')
            ->orderByRaw("FIELD(status, 'REQUESTED', 'CHECKED', 'UPDATED', 'REJECTED') ASC")
            ->orderBy('created_at', 'ASC')
            ->get();

        $response = array(
            'status' => true,
            'data' => $data,
            'now' => date('Y-m-d H:i:s'),
        );
        return Response::json($response);

    }

    public function mailReport($body, $to)
    {

        $cc = Auth::user()->email;
        $bcc = ['ympi-mis-ML@music.yamaha.com'];
        $title = "Revisi PI Stocktaking Ditolak";

        Mail::raw([], function ($message) use ($title, $body, $to, $cc, $bcc) {
            $message->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia');
            $message->to($to);
            $message->cc($cc);
            $message->bcc($bcc);
            $message->subject($title);
            $message->setBody($body, 'text/html');}
        );
    }
}
