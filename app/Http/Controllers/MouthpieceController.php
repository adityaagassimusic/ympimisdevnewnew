<?php

namespace App\Http\Controllers;

use App\CodeGenerator;
use App\Destination;
use App\EmployeeSync;
use App\ErrorLog;
use App\Http\Controllers\Controller;
use App\Inventory;
use App\KnockDown;
use App\KnockDownDetail;
use App\KnockDownLog;
use App\Material;
use App\MaterialPlantDataList;
use App\MouthpieceChecksheet;
use App\MouthpieceChecksheetDetail;
use App\TransactionCompletion;
use App\TransactionTransfer;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Response;

class MouthpieceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $http_user_agent = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) {
                die();
            }
        }
        $this->mesin = [
            'Mesin 1',
            'Mesin 2',
            'Mesin 3',
            'Mesin 4',
            'Mesin 5',
            'Mesin 6',
            'Mesin 7',
            'Mesin 8',
            'Mesin 9',
            'Mesin 11',
        ];

        $this->part = [
            'MJB',
            'MJG',
            'BJ',
            'FJ',
            'HJ',
            'A YRF B',
            'A YRF H',
            'A YRF S',
        ];
    }

    public function indexKdMouthpieceQaCheck()
    {
        $title = 'Mouthpiece QA Check';
        $title_jp = '';

        return view('kd.mouthpiece.qa_check', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('head', 'KD Mouthpiece')->with('page', 'MP QA Check');
    }

    public function scanKdMouthpieceQaCheck(Request $request)
    {
        $checksheets = MouthpieceChecksheet::leftJoin('materials', 'materials.material_number', '=', 'mouthpiece_checksheets.material_number')
        ->where('mouthpiece_checksheets.kd_number', '=', $request->get('kd_number'))
        ->where('mouthpiece_checksheets.remark', '=', '1')
        ->select(
            'mouthpiece_checksheets.kd_number',
            'mouthpiece_checksheets.material_number',
            'materials.issue_storage_location',
            'mouthpiece_checksheets.material_description',
            'mouthpiece_checksheets.quantity',
            'mouthpiece_checksheets.actual_quantity',
            'mouthpiece_checksheets.shipment_schedule_id',
            'mouthpiece_checksheets.remark',
            'mouthpiece_checksheets.employee_id',
            'mouthpiece_checksheets.start_packing',
            'mouthpiece_checksheets.end_packing',
            'mouthpiece_checksheets.destination_shortname',
            'mouthpiece_checksheets.st_date',
            'mouthpiece_checksheets.packing_date',
            'mouthpiece_checksheets.created_by',
            'mouthpiece_checksheets.created_at',
            'mouthpiece_checksheets.updated_at'
        )
        ->get();

        if (count($checksheets) <= 0) {
            $response = array(
                'status' => false,
                'message' => "Checksheet tidak ditemukan",
            );
            return Response::json($response);
        }

        $quantity = 0;
        $actual_quantity = 0;

        foreach ($checksheets as $checksheet) {
            $quantity += $checksheet->quantity;
            $actual_quantity += $checksheet->actual_quantity;
        }

        if ($quantity != $actual_quantity) {
            $response = array(
                'status' => false,
                'message' => "Proses packing mouthpiece belum selesai",
            );
            return Response::json($response);
        }

        foreach ($checksheets as $checksheet) {

            try {
                //Inisiasi Serial Number
                $serial_generator = CodeGenerator::where('note', '=', 'kd_serial_number')->first();
                $serial_number = sprintf("%'.0" . $serial_generator->length . "d", $serial_generator->index + 1);
                $serial_number = $serial_generator->prefix . $serial_number . $this->generateRandomString();
                $serial_generator->index = $serial_generator->index + 1;
                $serial_generator->save();

                $knock_down_details = new KnockDownDetail([
                    'kd_number' => $checksheet->kd_number,
                    'material_number' => $checksheet->material_number,
                    'quantity' => $checksheet->quantity,
                    'shipment_schedule_id' => $checksheet->shipment_schedule_id,
                    'storage_location' => $checksheet->issue_storage_location,
                    'serial_number' => $serial_number,
                    'created_by' => Auth::id(),
                ]);
                $knock_down_details->save();

                $inventory = Inventory::where('plant', '=', '8190')
                ->where('material_number', '=', $checksheet->material_number)
                ->where('storage_location', '=', $checksheet->issue_storage_location)
                ->first();

                if ($inventory) {
                    $inventory->quantity = $inventory->quantity + $checksheet->quantity;
                    $inventory->save();
                } else {
                    $inventory = new Inventory([
                        'plant' => '8190',
                        'material_number' => $checksheet->material_number,
                        'storage_location' => $checksheet->issue_storage_location,
                        'quantity' => $checksheet->quantity,
                    ]);
                    $inventory->save();
                }

                $transaction_completion = new TransactionCompletion([
                    'serial_number' => $checksheet->kd_number,
                    'material_number' => $checksheet->material_number,
                    'issue_plant' => '8190',
                    'issue_location' => $checksheet->issue_storage_location,
                    'quantity' => $checksheet->quantity,
                    'movement_type' => '101',
                    'created_by' => Auth::id(),
                ]);
                $transaction_completion->save();

                // $shipment_schedule = ShipmentSchedule::where('shipment_schedules.id', '=', $checksheet->shipment_schedule_id)
                // ->first();

                // $shipment_schedule->actual_quantity = $shipment_schedule->actual_quantity + $checksheet->quantity;
                // $shipment_schedule->save();

                $mouthpiece_checksheet_log = db::table('mouthpiece_checksheet_logs')->insert([
                    'kd_number' => $checksheet->kd_number,
                    'material_number' => $checksheet->material_number,
                    'material_description' => $checksheet->material_description,
                    'quantity' => $checksheet->quantity,
                    'actual_quantity' => $checksheet->actual_quantity,
                    'remark' => $checksheet->remark,
                    'employee_id' => $checksheet->employee_id,
                    'start_packing' => $checksheet->start_packing,
                    'end_packing' => $checksheet->end_packing,
                    'print_status' => $checksheet->print_status,
                    'destination_shortname' => $checksheet->destination_shortname,
                    'st_date' => $checksheet->st_date,
                    'st_date' => $checksheet->packing_date,
                    'qa_check' => $request->get('employee_id'),
                    'created_by' => $checksheet->created_by,
                    'created_at' => $checksheet->created_at,
                    'updated_at' => $checksheet->updated_at,
                ]);

                $material = Material::where('material_number', '=', $checksheet->material_number)->first();

                // YMES COMPLETION NEW
                $category = 'production_result';
                $function = 'scanKdMouthpieceQaCheck';
                $action = 'production_result';
                $result_date = date('Y-m-d H:i:s');
                $slip_number = $checksheet->kd_number;
                $serial_number = $serial_number;
                $material_number = $checksheet->material_number;
                $material_description = $material->material_description;
                $issue_location = $material->issue_storage_location;
                $mstation = $material->mstation;
                $quantity = $checksheet->quantity;
                $remark = 'MIRAI';
                $created_by = Auth::user()->username;
                $created_by_name = Auth::user()->name;
                $synced = null;
                $synced_by = null;

                app(YMESController::class)->production_result(
                    $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                // YMES END

            } catch (\Exception$e) {
                $error_log = new ErrorLog([
                    'error_message' => $e->getMessage(),
                    'created_by' => Auth::id(),
                ]);
                $error_log->save();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }

        try {
            $knock_down = new KnockDown([
                'kd_number' => $checksheets[0]->kd_number,
                'created_by' => Auth::id(),
                'max_count' => 100,
                'actual_count' => count($checksheets),
                'remark' => 'MP',
                'status' => 1,
            ]);
            $knock_down->save();

            $kd_log1 = KnockDownLog::updateOrCreate(
                ['kd_number' => $checksheets[0]->kd_number, 'status' => 0],
                ['created_by' => Auth::id(), 'status' => 0, 'updated_at' => Carbon::now()]
            );
            $kd_log1->save();

            $kd_log2 = KnockDownLog::updateOrCreate(
                ['kd_number' => $checksheets[0]->kd_number, 'status' => 1],
                ['created_by' => Auth::id(), 'status' => 1, 'updated_at' => Carbon::now()]
            );
            $kd_log2->save();

            $mouthpiece_checksheet_detail_logs = db::select("
                INSERT INTO mouthpiece_checksheet_detail_logs ( kd_number, material_number, material_description, quantity, actual_quantity, remark, end_picking, employee_id, created_by, deleted_at, created_at, updated_at ) SELECT
                mouthpiece_checksheet_details.kd_number,
                mouthpiece_checksheet_details.material_number,
                mouthpiece_checksheet_details.material_description,
                mouthpiece_checksheet_details.quantity,
                mouthpiece_checksheet_details.actual_quantity,
                mouthpiece_checksheet_details.remark,
                mouthpiece_checksheet_details.end_picking,
                mouthpiece_checksheet_details.employee_id,
                mouthpiece_checksheet_details.created_by,
                mouthpiece_checksheet_details.deleted_at,
                mouthpiece_checksheet_details.created_at,
                mouthpiece_checksheet_details.updated_at
                FROM
                mouthpiece_checksheet_details
                WHERE
                mouthpiece_checksheet_details.kd_number = '" . $checksheets[0]->kd_number . "'
                ");

            $del_checksheet = MouthpieceChecksheet::where('kd_number', '=', $checksheets[0]->kd_number)->forceDelete();
            $del_detail = MouthpieceChecksheetDetail::where('kd_number', '=', $checksheets[0]->kd_number)->forceDelete();

        } catch (\Exception$e) {
            $error_log = new ErrorLog([
                'error_message' => $e->getMessage(),
                'created_by' => Auth::id(),
            ]);
            $error_log->save();
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'message' => 'Checksheet berhasil lolos QA Check',
        );
        return Response::json($response);

    }

    public function indexKdMouthpieceLog()
    {
        $title = 'Mouthpiece Checksheet Log';
        $title_jp = '';

        $materials = Material::where('hpl', '=', 'MP')
        ->where('category', '=', 'KD')
        ->orderBy('material_number', 'ASC')
        ->get();

        $employees = EmployeeSync::orderBy('employee_id')
        ->get();

        $destinations = Destination::orderBy('destination_shortname', 'ASC')
        ->get();

        return view('kd.mouthpiece.log', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'materials' => $materials,
            'employees' => $employees,
            'destinations' => $destinations,
        ))->with('head', 'KD Mouthpiece')->with('page', 'MP Log');
    }

    public function fetchKdMouthpieceLog(Request $request)
    {

        $prodDate = "";
        $shipDate = "";
        $kd_number = "";
        $material = "";
        $employee = "";
        $destination = "";

        if (strlen($request->get('prodFrom')) > 0 && strlen($request->get('prodTo')) <= 0) {
            $prodFrom = date('Y-m-d', strtotime($request->get('prodFrom')));
            $prodDate = " AND date(m.created_at) >= '" . $prodFrom . "'";
        }

        if (strlen($request->get('prodFrom')) > 0 && strlen($request->get('prodTo')) > 0) {
            $prodFrom = date('Y-m-d', strtotime($request->get('prodFrom')));
            $prodTo = date('Y-m-d', strtotime($request->get('prodTo')));
            $prodDate = " AND date(m.created_at) >= '" . $prodFrom . "' AND date(m.created_at) <= '" . $prodTo . "'";
        }

        if (strlen($request->get('shipFrom')) > 0 && strlen($request->get('shipTo')) <= 0) {
            $shipFrom = date('Y-m-d', strtotime($request->get('shipFrom')));
            $shipDate = " AND date(m.st_date) >= '" . $shipFrom . "'";
        }

        if (strlen($request->get('shipFrom')) > 0 && strlen($request->get('shipTo')) > 0) {
            $shipFrom = date('Y-m-d', strtotime($request->get('shipFrom')));
            $shipTo = date('Y-m-d', strtotime($request->get('shipTo')));
            $shipDate = " AND date(m.st_date) >= '" . $shipFrom . "' AND date(m.st_date) <= '" . $shipTo . "'";
        }

        if (strlen($request->get('kd_number')) > 0) {
            $kd_number = " AND m.kd_number = '" . $request->get('kd_number') . "'";
        }

        if ($request->get('material_number') != null) {
            $material_numbers = $request->get('material_number');
            $material_number_length = count($material_numbers);
            $material_number = "";

            for ($x = 0; $x < $material_number_length; $x++) {
                $material_number = $material_number . "'" . $material_numbers[$x] . "'";
                if ($x != $material_number_length - 1) {
                    $material_number = $material_number . ",";
                }
            }

            $material = " AND m.material_number in (" . $material_number . ") ";
        }

        if ($request->get('employee_id') != null) {
            $employee_ids = $request->get('employee_id');
            $employee_id_length = count($employee_ids);
            $employee_id = "";

            for ($x = 0; $x < $employee_id_length; $x++) {
                $employee_id = $employee_id . "'" . $employee_ids[$x] . "'";
                if ($x != $employee_id_length - 1) {
                    $employee_id = $employee_id . ",";
                }
            }

            $employee = " AND m.employee_id in (" . $employee_id . ") ";
        }

        if ($request->get('destination_shortname') != null) {
            $destination_shortnames = $request->get('destination_shortname');
            $destination_shortname_length = count($destination_shortnames);
            $destination_shortname = "";

            for ($x = 0; $x < $destination_shortname_length; $x++) {
                $destination_shortname = $destination_shortname . "'" . $destination_shortnames[$x] . "'";
                if ($x != $destination_shortname_length - 1) {
                    $destination_shortname = $destination_shortname . ",";
                }
            }

            $destination = " AND m.destination_shortname in (" . $destination_shortname . ") ";
        }

        $checksheets = db::select("SELECT
           date( m.created_at ) AS created_at,
           m.kd_number,
           m.material_number,
           m.material_description,
           m.quantity,
           m.st_date,
           m.destination_shortname,
           m.employee_id,
           m.qa_check,
           qa.name AS qa_name,
           e.`name`,
           TIMESTAMPDIFF( MINUTE, start_packing, end_packing ) AS packing
           FROM
           mouthpiece_checksheet_logs m
           LEFT JOIN employee_syncs e ON m.employee_id = e.employee_id
           LEFT JOIN employee_syncs qa ON m.qa_check = qa.employee_id
           WHERE
           m.deleted_at IS NULL
           " . $prodDate . "
           " . $shipDate . "
           " . $kd_number . "
           " . $material . "
           " . $employee . "
           " . $destination . "");

        $kd_numbers = array();

        foreach ($checksheets as $checksheet) {
            if (!in_array($checksheet->kd_number, $kd_numbers)) {
                array_push($kd_numbers, $checksheet->kd_number);
            }
        }

        $kd_number_detail_length = count($kd_numbers);
        $kd_number_detail = "";

        for ($x = 0; $x < $kd_number_detail_length; $x++) {
            $kd_number_detail = $kd_number_detail . "'" . $kd_numbers[$x] . "'";
            if ($x != $kd_number_detail_length - 1) {
                $kd_number_detail = $kd_number_detail . ",";
            }
        }

        $kd = "";

        if (strlen($kd_number_detail) > 0) {
            $kd = " AND m.kd_number in (" . $kd_number_detail . ") ";
        }

        $checksheet_details = db::select("SELECT
           m.kd_number,
           m.material_number,
           m.material_description,
           m.quantity,
           m.end_picking,
           m.employee_id,
           e.`name`
           FROM
           mouthpiece_checksheet_detail_logs AS m
           LEFT JOIN employee_syncs AS e ON m.employee_id = e.employee_id
           WHERE
           m.deleted_at IS NULL
           " . $kd . "");

        $response = array(
            'status' => true,
            'checksheets' => $checksheets,
            'checksheet_details' => $checksheet_details,
            'message' => 'Data checksheet berhasil ditemukan',
        );
        return Response::json($response);
    }

    public function indexKdMouthpiecePacking()
    {
        $title = 'Mouthpiece Packing';
        $title_jp = '';

        return view('kd.mouthpiece.packing', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('head', 'KD Mouthpiece')->with('page', 'MP Packing');
    }

    public function checkKdMouthpieceChecksheet(Request $request)
    {
        $checksheet = MouthpieceChecksheet::where('kd_number', '=', $request->get('id'))
        ->select(db::raw('kd_number, st_date, destination_shortname, sum(quantity), sum(actual_quantity), remark'))
        ->groupBy('kd_number', 'st_date', 'destination_shortname', 'remark')
        ->first();

        if (!$checksheet) {
            $response = array(
                'status' => false,
                'message' => "Data checksheet tidak ditemukan",
            );
            return Response::json($response);
        }

        if ($checksheet->remark < $request->get('remark')) {
            $response = array(
                'status' => false,
                'message' => "Proses picking material belum selesai",
            );
            return Response::json($response);
        }

        if ($checksheet->remark > $request->get('remark')) {
            $response = array(
                'status' => false,
                'message' => "Proses mouthpiece packing sudah selesai.",
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'checksheet' => $checksheet,
            'message' => "Data checksheet ditemukan.",
        );
        return Response::json($response);
    }

    public function fetchKdMouthpiecePacking(Request $request)
    {
        $checksheets = MouthpieceChecksheet::where('mouthpiece_checksheets.kd_number', '=', $request->get('id'))
        ->select('mouthpiece_checksheets.id', 'mouthpiece_checksheets.kd_number', 'mouthpiece_checksheets.material_number', 'mouthpiece_checksheets.material_description', 'mouthpiece_checksheets.quantity', 'mouthpiece_checksheets.actual_quantity', 'mouthpiece_checksheets.print_status', 'mouthpiece_checksheets.destination_shortname', 'mouthpiece_checksheets.st_date', db::raw('mouthpiece_checksheets.actual_quantity-mouthpiece_checksheets.quantity as diff'))
        ->get();

        return DataTables::of($checksheets)
        ->addColumn('inner', function ($checksheet) {
            return '<button style="width:100%; font-size: 1.5vw; font-weight:bold;" class="btn btn-info btn-lg" id="' . $checksheet->id . '" onlick="printInner(id)">INNER</button>';
        })
        ->addColumn('outer', function ($checksheet) {
            return '<button style="width:100%; font-size: 1.5vw; font-weight:bold;" class="btn btn-warning btn-lg" id="' . $checksheet->id . '" onlick="printOuter(id)">OUTER</button>';
        })
        ->rawColumns([
            'inner' => 'inner',
            'outer' => 'outer',
        ])
        ->make(true);
    }

    public function scanKdMouthpiecePacking(Request $request)
    {
        $checksheet = MouthpieceChecksheet::where('kd_number', '=', $request->get('kd_number'))
        ->where('material_number', '=', $request->get('material_number'))
        ->first();

        if ($checksheet == "") {
            $response = array(
                'status' => false,
                'message' => "Mouthpiece tidak ada pada checksheet.",
            );
            return Response::json($response);
        }

        if ($checksheet->quantity <= $checksheet->actual_quantity) {
            $response = array(
                'status' => false,
                'message' => "Jumlah mouthpiece pada checksheet sudah terpenuhi.",
            );
            return Response::json($response);
        }

        try {
            $checksheet->actual_quantity = $checksheet->actual_quantity + 1;
            $checksheet->employee_id = $request->get('employee_id');
            if ($checksheet->start_packing == null) {
                $checksheet->start_packing = date('Y-m-d H:i:s');
            }
            $checksheet->end_packing = date('Y-m-d H:i:s');
            $checksheet->save();

            $response = array(
                'status' => true,
                'message' => 'Packing mouthpiece berhasil.',
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $error_log = new ErrorLog([
                'error_message' => $e->getMessage(),
                'created_by' => Auth::id(),
            ]);
            $error_log->save();
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function indexKdMouthpiecePicking()
    {
        $title = 'Mouthpiece Material Picking';
        $title_jp = '';

        // $checksheets = db::select("SELECT
        //     mouthpiece_checksheets.kd_number,
        //     shipment_schedules.st_date,
        //     destinations.destination_shortname,
        //     group_concat(
        //     CONCAT( materials.material_description, ' (', mouthpiece_checksheets.quantity, ')' )) AS item,
        //     sum( mouthpiece_checksheets.quantity ) AS total
        //     FROM
        //     mouthpiece_checksheets
        //     LEFT JOIN materials ON materials.material_number = mouthpiece_checksheets.material_number
        //     LEFT JOIN shipment_schedules ON shipment_schedules.id = mouthpiece_checksheets.shipment_schedule_id
        //     LEFT JOIN destinations ON destinations.destination_code = shipment_schedules.destination_code
        //     WHERE
        //     mouthpiece_checksheets.remark = '0'
        //     GROUP BY
        //     mouthpiece_checksheets.kd_number,
        //     shipment_schedules.st_date,
        //     destinations.destination_shortname");

        return view('kd.mouthpiece.picking', array(
            'title' => $title,
            'title_jp' => $title_jp,
            // 'checksheets' => $checksheets
        ))->with('head', 'KD Mouthpiece')->with('page', 'MP Picking Material');
    }

    public function createKdMouthpiecePicking(Request $request)
    {

        $kd_number = $request->get('kd_number');
        $boms = MouthpieceChecksheetDetail::where('kd_number', '=', $kd_number)->where('remark', 'mouthpiece')->get();
        $employee = EmployeeSync::where('employee_id', '=', $request->get('employee_id'))->first();

        DB::beginTransaction();
        try {

            for ($i = 0; $i < count($boms); $i++) {

                $mpdl = MaterialPlantDataList::where('material_number', '=', $boms[$i]->material_number)->first();

                $mp_stock = db::connection('ympimis_2')
                ->table('mouthpiece_stocks')
                ->where('gmc', '=', $boms[$i]->material_number)
                ->first();

                if ($mp_stock) {
                    $update_mp_stock = db::connection('ympimis_2')
                    ->table('mouthpiece_stocks')
                    ->where('gmc', '=', $boms[$i]->material_number)
                    ->update([
                        'qty' => $mp_stock->qty + ($boms[$i]->quantity * -1),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    $insert_mp_log = db::connection('ympimis_2')
                    ->table('mouthpiece_request_logs')
                    ->insert([
                        'request_id' => $kd_number,
                        'gmc' => $boms[$i]->material_number,
                        'desc' => $mpdl->material_description,
                        'issue' => $mpdl->storage_location,
                        'qty' => $boms[$i]->quantity,
                        'uom' => $mpdl->bun,
                        'created_by' => $employee->employee_id . '/' . $employee->name,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'remark' => 'confirm',
                    ]);

                } else {
                    $mp_stock = db::connection('ympimis_2')
                    ->table('mouthpiece_stocks')
                    ->insert([
                        'gmc' => $boms[$i]->material_child,
                        'desc' => $mpdl->material_description,
                        'issue' => $mpdl->storage_location,
                        'qty' => ($boms[$i]->quantity * -1),
                        'uom' => $mpdl->bun,
                        'created_by' => $employee->employee_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    $insert_mp_log = db::connection('ympimis_2')
                    ->table('mouthpiece_request_logs')
                    ->insert([
                        'request_id' => $kd_number,
                        'gmc' => $boms[$i]->material_child,
                        'desc' => $mpdl->material_description,
                        'issue' => $mpdl->storage_location,
                        'qty' => $boms[$i]->usage * $quantity,
                        'uom' => $mpdl->bun,
                        'created_by' => $employee->employee_id . '/' . $employee->name,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'remark' => 'confirm',
                    ]);

                }

                $gms = new TransactionTransfer([
                    'plant' => '8190',
                    'serial_number' => $kd_number,
                    'material_number' => $boms[$i]->material_number,
                    'issue_plant' => '8190',
                    'issue_location' => 'VN91',
                    'receive_plant' => '8190',
                    'receive_location' => 'PXMP',
                    'transaction_code' => 'MB1B',
                    'movement_type' => '9I3',
                    'quantity' => $boms[$i]->quantity,
                    'created_by' => Auth::id(),
                ]);
                $gms->save();

                // YMES TRANSFER NEW
                $category = 'goods_movement';
                $function = 'createKdMouthpiecePicking';
                $action = 'goods_movement';
                $result_date = date('Y-m-d H:i:s');
                $slip_number = $kd_number;
                $serial_number = null;
                $material_number = $boms[$i]->material_number;
                $material_description = $boms[$i]->material_description;
                $issue_location = 'VN91';
                $receive_location = 'PXMP';
                $quantity = $boms[$i]->quantity;
                $remark = 'MIRAI';
                $created_by = Auth::user()->username;
                $created_by_name = Auth::user()->name;
                $synced = null;
                $synced_by = null;

                app(YMESController::class)->goods_movement(
                    $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $receive_location, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                // YMES END

            }

            $checksheets = MouthpieceChecksheet::where('kd_number', '=', $request->get('kd_number'))
            ->update([
                'remark' => 1,
            ]);

            DB::commit();
            $response = array(
                'status' => true,
                'message' => "Picking telah selesai",
            );
            return Response::json($response);

        } catch (\Exception$e) {
            DB::rollback();
            $error_log = new ErrorLog([
                'error_message' => $e->getMessage(),
                'created_by' => Auth::id(),
            ]);
            $error_log->save();
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

    }

    public function scanKdMouthpiecePicking(Request $request)
    {
        try {
            $checksheet_detail = MouthpieceChecksheetDetail::where('kd_number', '=', $request->get('kd_number'))
            ->where('material_number', '=', $request->get('material_number'))
            ->whereRaw('quantity > actual_quantity')
            ->first();

            if ($checksheet_detail == "") {
                $response = array(
                    'status' => false,
                    'message' => "Material tidak ada pada checklist atau Material sudah dipicking",
                );
                return Response::json($response);
            }

            $checksheet_detail->actual_quantity = $checksheet_detail->quantity;
            $checksheet_detail->employee_id = $request->get('employee_id');
            $checksheet_detail->end_picking = date('Y-m-d H:i:s');

            $checksheet_detail->save();

            $response = array(
                'status' => true,
                'message' => "Material berhasil dipicking",
            );
            return Response::json($response);

        } catch (\Exception$e) {
            $error_log = new ErrorLog([
                'error_message' => $e->getMessage(),
                'created_by' => Auth::id(),
            ]);
            $error_log->save();
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchKdMouthpiecePicking(Request $request)
    {

        $checksheets = MouthpieceChecksheet::where('kd_number', '=', $request->get('id'))
        ->where('remark', '=', '0')
        ->get();

        if (count($checksheets) <= 0) {
            $response = array(
                'status' => false,
                'message' => 'Checksheet tidak ditemukan',
            );
            return Response::json($response);
        }

        $checksheet_details = MouthpieceChecksheetDetail::where('kd_number', '=', $request->get('id'))
        ->orderBy('remark', 'ASC')
        ->orderBy('material_number', 'ASC')
        ->get();

        if (count($checksheet_details) <= 0) {
            $response = array(
                'status' => false,
                'message' => 'Checksheet tidak ditemukan',
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'checksheet_details' => $checksheet_details,
        );
        return Response::json($response);
    }

    public function indexKdMouthpieceChecksheet()
    {
        $title = 'Create Mouthpiece Packing Checksheet';
        $title_jp = '';

        $destinations = db::table('destinations')
        ->whereNull('deleted_at')
        ->where('destination_shortname', '<>', 'ITM')
        ->orderBy('destination_shortname')
        ->get();

        return view('kd.mouthpiece.checksheet', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'destinations' => $destinations,
        ))->with('head', 'KD Mouthpiece')->with('page', 'MP Create Checksheet');
    }

    public function fetchKdMouthpieceMaterial()
    {
        $materials = Material::where('category', '=', 'KD')
        ->where('hpl', '=', 'MP')
        ->where('kd_name', '=', 'SINGLE')
        ->get();

        $response = array(
            'status' => true,
            'target' => $materials,
        );
        return Response::json($response);

    }

    public function scanKdMouthpieceOperator(Request $request)
    {
        $employee_id = db::table('employees')
        ->where('tag', '=', $request->get('employee_id'))
        ->orWhere('employee_id', '=', $request->get('employee_id'))
        ->first();

        if (count($employee_id) == 0) {
            $response = array(
                'status' => false,
                'message' => "ID karyawan tidak ditemukan",
            );
            return Response::json($response);
        }

        // $employee_id = substr($request->get('employee_id'), 0, 9);
        $employee_sync = EmployeeSync::where('employee_id', '=', $employee_id->employee_id)->first();

        if ($employee_sync == "") {
            $response = array(
                'status' => false,
                'message' => "ID karyawan tidak ditemukan",
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'employee' => $employee_sync,
            'tag' => $employee_id
        );
        return Response::json($response);

    }

    public function fetchKdMouthpieceChecksheet()
    {
        $checksheets = db::select("SELECT
           mouthpiece_checksheets.kd_number,
           mouthpiece_checksheets.packing_date,
           mouthpiece_checksheets.st_date,
           mouthpiece_checksheets.destination_shortname,
           mouthpiece_checksheets.print_status,
           group_concat(
            CONCAT( mouthpiece_checksheets.material_description, ' (', mouthpiece_checksheets.quantity, ')' )) AS item,
           sum( mouthpiece_checksheets.quantity ) AS total
           FROM
           mouthpiece_checksheets
           GROUP BY
           mouthpiece_checksheets.kd_number,
           mouthpiece_checksheets.packing_date,
           mouthpiece_checksheets.st_date,
           mouthpiece_checksheets.print_status,
           mouthpiece_checksheets.destination_shortname");

        $response = array(
            'status' => true,
            'checksheets' => $checksheets,
        );
        return Response::json($response);
    }

    public function createKdMouthpieceChecksheet(Request $request)
    {

        $prefix_now = 'KD' . date("y") . date("m");
        $code_generator = CodeGenerator::where('note', '=', 'kd')->first();
        if ($prefix_now != $code_generator->prefix) {
            $code_generator->prefix = $prefix_now;
            $code_generator->index = '0';
            $code_generator->save();
        }

        $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
        $kd_number = $code_generator->prefix . $number;
        $code_generator->index = $code_generator->index + 1;
        $code_generator->save();

        $st_date = '';
        $location = $request->get('location');

        foreach ($request->get('item_list') as $list) {
            try {
                $new_checksheet = new MouthpieceChecksheet([
                    'kd_number' => $kd_number,
                    'material_number' => $list['material_number'],
                    'material_description' => $list['material_description'],
                    'quantity' => $list['quantity'],
                    'actual_quantity' => 0,
                    'remark' => '0',
                    'packing_date' => $list['packing_date'],
                    'destination_shortname' => $list['destination'],
                    'st_date' => $list['shipment_date'],
                    'created_by' => Auth::id(),
                ]);
                $new_checksheet->save();

                $st_date = $list['shipment_date'];

            } catch (\Exception$e) {
                $error_log = new ErrorLog([
                    'error_message' => $e->getMessage(),
                    'created_by' => Auth::id(),
                ]);
                $error_log->save();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }

        $checksheets = db::select("SELECT
           material_number,
           material_description,
           sum( `usage` ) AS quantity,
           remark
           FROM
           (
            SELECT
            bom.material_child AS material_number,
            material_plant_data_lists.material_description,
            m.quantity * bom.`usage` AS `usage`,
            bom.remark
            FROM
            mouthpiece_checksheets m
            LEFT JOIN bom_components bom ON bom.material_parent = m.material_number
            LEFT JOIN material_plant_data_lists ON material_plant_data_lists.material_number = bom.material_child
            WHERE
            m.kd_number = '" . $kd_number . "'
            ) AS pick
            GROUP BY
            material_number,
            material_description,
            remark
            ORDER BY
            remark ASC");

           foreach ($checksheets as $checksheet) {
            try {
                if ($checksheet->remark == 'label outer') {
                    $new_detail = new MouthpieceChecksheetDetail([
                        'kd_number' => $kd_number,
                        'material_number' => $checksheet->material_number,
                        'material_description' => $checksheet->material_description,
                        'quantity' => count($request->get('item_list')),
                        'remark' => $checksheet->remark,
                        'created_by' => Auth::id(),
                    ]);
                } else if ($checksheet->remark == 'outer box') {
                    $new_detail = new MouthpieceChecksheetDetail([
                        'kd_number' => $kd_number,
                        'material_number' => $checksheet->material_number,
                        'material_description' => $checksheet->material_description,
                        'quantity' => 1,
                        'remark' => $checksheet->remark,
                        'created_by' => Auth::id(),
                    ]);
                } else {
                    $new_detail = new MouthpieceChecksheetDetail([
                        'kd_number' => $kd_number,
                        'material_number' => $checksheet->material_number,
                        'material_description' => $checksheet->material_description,
                        'quantity' => $checksheet->quantity,
                        'remark' => $checksheet->remark,
                        'created_by' => Auth::id(),
                    ]);
                }

                $new_detail->save();
            } catch (\Exception$e) {
                $error_log = new ErrorLog([
                    'error_message' => $e->getMessage(),
                    'created_by' => Auth::id(),
                ]);
                $error_log->save();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }

        $details = MouthpieceChecksheet::where('mouthpiece_checksheets.kd_number', $kd_number)
        ->select(
            'mouthpiece_checksheets.kd_number',
            'mouthpiece_checksheets.material_number',
            'mouthpiece_checksheets.material_description',
            'mouthpiece_checksheets.destination_shortname',
            'mouthpiece_checksheets.quantity'
        )
        ->get();

        // $this->printKDO($kd_number, $st_date, $details, $location, 'PRINT', $details[0]->destination_shortname);

        $response = array(
            'status' => true,
            'message' => "Checksheet berhasil dibuat",
        );
        return Response::json($response);
    }

    public function deleteKdMouthpieceChecksheet(Request $request)
    {
        try {
            $checksheet = db::select("DELETE
             FROM
             mouthpiece_checksheets
             WHERE
             kd_number = '" . $request->get('id') . "'");

            $checksheet_detail = db::select("DELETE
             FROM
             mouthpiece_checksheet_details
             WHERE
             kd_number = '" . $request->get('id') . "'");

            $response = array(
                'status' => true,
                'message' => "Checksheet berhasil dihapus",
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $error_log = new ErrorLog([
                'error_message' => $e->getMessage(),
                'created_by' => Auth::id(),
            ]);
            $error_log->save();
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function reprintKdMouthpieceChecksheet(Request $request)
    {

        $kd_number = $request->get('kd_number');
        $location = $request->get('location');

        try {
            $ck = MouthpieceChecksheet::where('kd_number', $kd_number)
            ->update([
                'print_status' => 1,
            ]);

        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        $details = MouthpieceChecksheet::where('mouthpiece_checksheets.kd_number', $kd_number)
        ->select(
            'mouthpiece_checksheets.kd_number',
            'mouthpiece_checksheets.material_number',
            'mouthpiece_checksheets.material_description',
            'mouthpiece_checksheets.quantity',
            'mouthpiece_checksheets.destination_shortname',
            'mouthpiece_checksheets.st_date'
        )
        ->get();

        $this->printKDO($kd_number, $details[0]->st_date, $details, $location, 'REPRINT', $details[0]->destination_shortname);

        $response = array(
            'status' => true,
            'message' => "Reprint KDO Berhasil",
        );
        return Response::json($response);

    }

    public function printKDO($kd_number, $st_date, $knock_down_details, $storage_location, $remark, $destination_shortname)
    {

        if (str_contains(Auth::user()->role_code, 'MIS')) {
            $printer_name = 'MIS';
        } else if (str_contains(Auth::user()->role_code, 'WH')) {
            $printer_name = 'FLO Printer LOG';
        } else {
            $printer_name = 'KDO MP';
        }

        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        if ($remark == 'REPRINT') {
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setReverseColors(true);
            $printer->setTextSize(2, 2);
            $printer->text(" REPRINT " . "\n");
            $printer->feed(1);
        }

        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setUnderline(true);
        $printer->text('Storage Location:');
        $printer->setUnderline(false);
        $printer->feed(1);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(3, 3);
        $printer->text(strtoupper($storage_location . "\n"));
        $printer->initialize();

        $printer->setUnderline(true);
        $printer->text('KDO:');
        $printer->feed(1);
        $printer->setUnderline(false);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->qrCode($kd_number, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
        $printer->text($kd_number . "\n");

        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setUnderline(true);
        $printer->text('Destination:');
        $printer->setUnderline(false);
        $printer->feed(1);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(6, 3);
        $printer->text(strtoupper($destination_shortname . "\n\n"));
        $printer->initialize();

        $printer->initialize();
        $printer->setUnderline(true);
        $printer->text('Shipment Date:');
        $printer->setUnderline(false);
        $printer->feed(1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(4, 2);
        $printer->text(date('d-M-Y', strtotime($st_date)) . "\n\n");
        $printer->initialize();
        $printer->text("No |GMC     | Description                 | Qty ");
        $total_qty = 0;
        for ($i = 0; $i < count($knock_down_details); $i++) {
            $number = $this->writeString($i + 1, 2, ' ');
            $qty = $this->writeString($knock_down_details[$i]->quantity, 4, ' ');
            $material_description = substr($knock_down_details[$i]->material_description, 0, 27);
            $material_description = $this->writeString($material_description, 27, ' ');
            $printer->text($number . " |" . $knock_down_details[$i]->material_number . " | " . $material_description . " | " . $qty);
            $total_qty += $knock_down_details[$i]->quantity;
        }
        $printer->feed(2);
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("------------------------------------");
        $printer->feed(1);
        $printer->text("|Qty:             |Qty:            |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("|Production       |Logistic        |");
        $printer->feed(1);
        $printer->text("------------------------------------");
        $printer->feed(2);
        $printer->initialize();
        $printer->text("Total Qty: " . $total_qty . "\n");
        $printer->feed(2);
        $printer->feed(2);
        $printer->cut();
        $printer->close();
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

    public function generateRandomString()
    {

        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return $characters[rand(0, strlen($characters) - 1)];

    }

    // Mouthpiece Controller
    public function IndexMonitoringMouthpiece()
    {
        $title = 'Mouthpiece Stock Monitoring';
        $title_jp = '';

        return view('mouthpiece.monitoring_mouthpiece', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Mouthpiece');
    }

    public function FetchDataYmesStockMouthpiece(Request $request)
    {
        try {
            $id = $request->get('id');
            $data = db::connection('ymes')->table('d_mes0010')->where('location_code', '=', 'VN91')
            ->whereIn('item_code', [
                'VAR9810',
                'VAY6270',
                'VAR9830',
                'VAR9840',
                'VAR9850',
                'VCW3451',
                'VDV8481',
                'VAR9760',
                'VAR9770',
                'VAR9780',
                'VAR9790',
                'VAR9800',
                'ZY13450',
                'VAR9860',
                'VAR9870',
                'VAR9880',
                'VAR9890',
                'VAR9900',
                'VAR9910',
                'VAR9660',
                'VAY6630',
                'VAY6520',
                'VAR9680'])
            ->orderBy('stockqty', 'asc')
            ->get();

            $data2 = db::connection('ymes')->table('d_mes0010')->where('location_code', '=', 'VN91')
            ->whereIn('item_code', [
                'VAR9690',
                'VAR9700',
                'VAR9520',
                'VAR9530',
                'VAR9540',
                'VAR9550',
                'VAR9560',
                'VAR9570',
                'VAR9580',
                'VAR9590',
                'VAR9600',
                'VAR9610',
                'VAR9710',
                'VAR9720',
                'VAR9730',
                'VAR9740',
                'VAR9750',
                'VAR9640',
                'VAR9650',
                'VFZ9770',
                'VCW3455',
                'ZY13540',
                'VFA4670'])
            ->orderBy('stockqty', 'asc')
            ->get();

            $data_kanban = '';
            $data_detail = '';
            $no_kanban = $request->get('value');
            if ($no_kanban != null) {
                $data_kanban = db::connection('ympimis_2')->select('select gmc, qty from mouthpiece_kanbans where tag_kanban = "' . $no_kanban . '"');
                $data_detail = db::select('select material_number, material_description as `desc`, bun, storage_location from material_plant_data_lists where material_number = "' . $data_kanban[0]->gmc . '"');
            }

            $data_mpdl = db::select("SELECT
                material_number,
                REPLACE(REPLACE ( REPLACE ( material_description, 'MOUTHPIECE', '' ), '(YMPI)', '' ), '//', ' ') AS material_description,
                material_description as `desc`, bun, storage_location
                FROM
                material_plant_data_lists
                WHERE
                material_number IN (
                    'VAQ5640',
                    'VAQ5650',
                    'VAQ5680',
                    'VAQ5690',
                    'VAQ5700',
                    'VAQ5730',
                    'VAQ5740',
                    'VAQ5750',
                    'VAQ5760',
                    'VAQ5800',
                    'VAQ5810',
                    'VAQ5820',
                    'VAQ5840',
                    'VAQ5850',
                    'VAQ5860',
                    'VAQ5870',
                    'VAQ5880',
                    'VAQ5890',
                    'VAQ5900',
                    'VAQ5910',
                    'VAQ5920',
                    'VAQ5930',
                    'VAQ5940',
                    'VAQ5950',
                    'VAQ5960',
                    'VAQ5970',
                    'VAQ5980',
                    'VAQ5990',
                    'VAQ6010',
                    'VAX3290',
                    'VAX3320',
                    'VAZ1590',
                    'W583401',
                    'ZQ66801',
                    'ZQ66821',
                    'ZQ80591'
                )");

            $target1 = db::select("SELECT
                material_number,
                sum( quantity ) AS total
                FROM
                `production_schedules`
                WHERE
                material_number IN (
                    'VAQ5640',
                    'VAQ5650',
                    'VAQ5680',
                    'VAQ5690',
                    'VAQ5700',
                    'VAQ5730',
                    'VAQ5740',
                    'VAQ5750',
                    'VAQ5760',
                    'VAQ5800',
                    'VAQ5810',
                    'VAQ5820',
                    'VAQ5840',
                    'VAQ5850',
                    'VAQ5860',
                    'VAQ5870',
                    'VAQ5880',
                    'VAQ5890'
                    )
                AND date_format( due_date, '%Y-%m' ) >= '2023-02'
                AND date_format( due_date, '%Y-%m' ) <= '2023-03'
                GROUP BY
                material_number ORDER BY total asc");

            $target2 = db::select("SELECT
                material_number,
                sum( quantity ) AS total
                FROM
                `production_schedules`
                WHERE
                material_number IN (
                    'VAQ5900',
                    'VAQ5910',
                    'VAQ5920',
                    'VAQ5930',
                    'VAQ5940',
                    'VAQ5950',
                    'VAQ5960',
                    'VAQ5970',
                    'VAQ5980',
                    'VAQ5990',
                    'VAQ6010',
                    'VAX3290',
                    'VAX3320',
                    'VAZ1590',
                    'W583401',
                    'ZQ66801',
                    'ZQ66821',
                    'ZQ80591'
                    )
                AND date_format( due_date, '%Y-%m' ) >= '2023-02'
                AND date_format( due_date, '%Y-%m' ) <= '2023-03'
                GROUP BY
                material_number ORDER BY total asc");

            $date = $request->get('date');

            $perolehan = db::connection('ympimis_2')->select('SELECT
                ROUND( qty / ideal_stock_1, 1 ) AS jumlah,
                gmc,
                REPLACE(REPLACE ( REPLACE ( `desc`, "MOUTHPIECE", "" ), "(YMPI)", "" ), "//", " ") AS `desc`
                FROM
                mouthpiece_stocks
                GROUP BY
                jumlah, gmc, `desc`');

            $stock_ideals = db::connection('ympimis_2')->select('SELECT
                ROUND( qty / ideal_stock_1, 1 ) AS jumlah,
                gmc,
                REPLACE ( REPLACE ( REPLACE ( `desc`, "MOUTHPIECE", "" ), "(YMPI)", "" ), "//", " " ) AS `desc`,
                qty,
                ideal_stock_1
                FROM
                mouthpiece_stocks 
                GROUP BY
                jumlah,
                gmc,
                `desc`,
                qty,
                ideal_stock_1
                ORDER BY
                `desc` ASC 
                LIMIT 20');

            $stock_ideals1 = db::connection('ympimis_2')->select('SELECT
                ROUND( qty / ideal_stock_1, 1 ) AS jumlah,
                gmc,
                REPLACE ( REPLACE ( REPLACE ( `desc`, "MOUTHPIECE", "" ), "(YMPI)", "" ), "//", " " ) AS `desc`,
                qty,
                ideal_stock_1 
                FROM
                mouthpiece_stocks 
                GROUP BY
                jumlah,
                gmc,
                `desc`,
                qty,
                ideal_stock_1 
                ORDER BY
                `desc` DESC 
                LIMIT 20');

            $response = array(
                'status' => true,
                'data' => $data,
                'data2' => $data2,
                'data_mpdl' => $data_mpdl,
                'target1' => $target1,
                'target2' => $target2,
                'perolehan' => $perolehan,
                'data_kanban' => $data_kanban,
                'data_detail' => $data_detail,
                'stock_ideals' => $stock_ideals,
                'stock_ideals1' => $stock_ideals1,
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

    public function IndexMouthpieceProcess()
    {
        $title = 'Mouthpiece';
        $title_jp = '職長教育の報告';

        return view('mouthpiece.index_mouthpiece_process', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Mouthpiece');
    }

    public function IndexMouthpieceProcessIN()
    {
        $title = 'Mouthpiece';
        $title_jp = '職長教育の報告';
        $user = strtoupper(Auth::user()->username);
        $name = Auth::user()->name;

        return view('mouthpiece.mouthpiece_in', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee_id' => $user,
            'name' => $name,
        ))->with('page', 'Mouthpiece');
    }

    public function IndexReportMouthpieceProcessIN()
    {
        $title = 'Mouthpiece';
        $title_jp = '職長教育の報告';
        $user = strtoupper(Auth::user()->username);
        $name = Auth::user()->name;
        

        return view('mouthpiece.report_in', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee_id' => $user,
            'name' => $name,
            
        ))->with('page', 'Mouthpiece');
    }

    public function FetchReportMouthpieceProcessIN(Request $request)
    {
        try {
            $mulai_tanggal = $request->get('mulai_tanggal');
            $sampai_tanggal = $request->get('sampai_tanggal');

            $list_logs = '';
            $employee_sync = '';

            if ($mulai_tanggal == null && $sampai_tanggal == null) {
                $list_logs = db::connection('ympimis_2')->table('mouthpiece_logs')->get();   
                $employee_sync = db::table('employee_syncs')->where('end_date', null)->get();
            }else{
                $list_logs = db::connection('ympimis_2')->table('mouthpiece_logs')->whereDate('created_at', '>=', $mulai_tanggal)->whereDate('created_at', '<=', $sampai_tanggal)->get();
                $employee_sync = db::table('employee_syncs')->where('end_date', null)->get();
            }

            $response = array(
                'status' => true,
                'list_logs' => $list_logs,
                'employee_sync' => $employee_sync
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

    public function IndexMouthpieceProcessOUT()
    {
        $title = 'Mouthpiece';
        $title_jp = '職長教育の報告';
        $user = strtoupper(Auth::user()->username);
        $name = Auth::user()->name;
        $request = db::connection('ympimis_2')->table('mouthpiece_request_logs')->where('remark', 'list')->get();

        return view('mouthpiece.mouthpiece_out', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee_id' => $user,
            'name' => $name,
            'request' => $request,
        ))->with('page', 'Mouthpiece');
    }

    public function IndexReportMouthpieceProcessOUT()
    {
        $title = 'Mouthpiece';
        $title_jp = '職長教育の報告';
        $user = strtoupper(Auth::user()->username);
        $name = Auth::user()->name;

        return view('mouthpiece.report_out', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee_id' => $user,
            'name' => $name,
        ))->with('page', 'Mouthpiece');
    }

    public function FetchReportMouthpieceProcessOUT(Request $request)
    {
        try {
            $mulai_tanggal = $request->get('mulai_tanggal');
            $sampai_tanggal = $request->get('sampai_tanggal');

            $list_logs = '';
            $employee_sync = '';

            if ($mulai_tanggal == null && $sampai_tanggal == null) {
                $list_logs = db::connection('ympimis_2')->table('mouthpiece_request_logs')->get();   
                $employee_sync = db::table('employee_syncs')->where('end_date', null)->get();
            }else{
                $list_logs = db::connection('ympimis_2')->table('mouthpiece_request_logs')->whereDate('created_at', '>=', $mulai_tanggal)->whereDate('created_at', '<=', $sampai_tanggal)->get();
                $employee_sync = db::table('employee_syncs')->where('end_date', null)->get();
            }

            $response = array(
                'status' => true,
                'list_logs' => $list_logs,
                'employee_sync' => $employee_sync
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

    // public function IndexMouthpieceProcessOUT()
    // {
    //     $title = 'Mouthpiece';
    //     $title_jp = '職長教育の報告';
    //     $user = strtoupper(Auth::user()->username);
    //     $name = Auth::user()->name;

    //     return view('mouthpiece.output_stock', array(
    //         'title' => $title,
    //         'title_jp' => $title_jp,
    //         'employee_id' => $user,
    //         'name' => $name
    //     ))->with('page', 'Mouthpiece');
    // }

    public function IndexMouthpieceStockKanban()
    {
        $title = 'Mouthpiece';
        $title_jp = '職長教育の報告';
        $user = strtoupper(Auth::user()->username);
        $name = Auth::user()->name;
        $mpdl = db::table('material_plant_data_lists')->where('storage_location', 'VN91')->get();

        return view('mouthpiece.index_stock_kanban', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee_id' => $user,
            'name' => $name,
            'mpdl' => $mpdl,
            'mpdl2' => $mpdl,
        ))->with('page', 'Mouthpiece');
    }

    public function SaveStockKanban(Request $request)
    {
        try {
            $gmc = $request->get('gmc');
            $desc = $request->get('desc');
            $issue = $request->get('issue');
            $uom = $request->get('uom');
            $mrpc = $request->get('mrpc');
            $qty = $request->get('qty');
            $kanban = $request->get('kanban');
            $no_kanban = $request->get('no_kanban');

            db::connection('ympimis_2')->table('mouthpiece_kanbans')->insert([
                'tag_kanban' => $kanban,
                'no_kanban' => $no_kanban,
                'gmc' => $gmc,
                'desc' => $desc,
                'issue' => $issue,
                'uom' => $uom,
                'mrpc' => $mrpc,
                'qty' => $qty,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

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

    public function UpdateStockKanban(Request $request)
    {
        try {
            $kanban = $request->get('tag');
            $gmc = $request->get('gmc');
            $desc = $request->get('desc');
            $issue = $request->get('issue');
            $uom = $request->get('uom');
            $qty = $request->get('qty');

            db::connection('ympimis_2')->table('mouthpiece_kanbans')->where('tag_kanban', $kanban)->update([
                'tag_kanban' => $kanban,
                'gmc' => $gmc,
                'desc' => $desc,
                'issue' => $issue,
                'uom' => $uom,
                'qty' => $qty,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

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

    public function UpdateListMouthpiece(Request $request)
    {
        try {
            $id = $request->get('id');
            $value = $request->get('value');

            db::connection('ympimis_2')->table('mouthpiece_lists')->where('id', $id)->update([
                'qty' => $value,
            ]);

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

    public function UpdateReceivedMouthpiece(Request $request)
    {
        try {
            $received_by = $request->get('id_received_by');

            db::connection('ympimis_2')->table('mouthpiece_lists')->update([
                'received_by' => $received_by
            ]);

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

    public function DeleteStockKanban(Request $request)
    {
        try {
            $kanban = $request->get('tag');

            db::connection('ympimis_2')->delete('delete from mouthpiece_kanbans where tag_kanban = "' . $kanban . '"');

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

    public function DeleteListMouthpiece(Request $request)
    {
        try {
            $id = $request->get('id');

            db::connection('ympimis_2')->delete('delete from mouthpiece_lists where id = "' . $id . '"');

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

    public function CheckStockKanban(Request $request)
    {
        try {
            $value = $request->get('value');
            $kanban = db::connection('ympimis_2')->table('mouthpiece_kanbans')->where('tag_kanban', $value)->first();
            $hasil = count($kanban);
            $resume = db::connection('ympimis_2')->select('SELECT
                gmc,
                `desc`,
                issue,
                uom,
                no_kanban,
                tag_kanban,
                qty
                FROM
                mouthpiece_kanbans order by gmc asc');
            $tag = $request->get('tag');
            $detail_kanban = db::connection('ympimis_2')->table('mouthpiece_kanbans')->where('tag_kanban', $tag)->first();

            $response = array(
                'status' => true,
                'kanban' => count($kanban),
                'resume' => $resume,
                'detail_kanban' => $detail_kanban,
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

    public function ListBeforeCSMouthpiece(Request $request)
    {
        try {
            // $data = db::connection('ympimis_2')->table('mouthpiece_lists')->get();
            $data = db::connection('ympimis_2')->select('SELECT
                mo.id,
                mo.gmc,
                mo.`desc`,
                mo.issue,
                mo.uom,
                mo.qty,
                mk.no_kanban
                FROM
                mouthpiece_lists AS mo
                LEFT JOIN mouthpiece_kanbans AS mk ON mk.tag_kanban = mo.tag_kanban');

            $response = array(
                'status' => true,
                'data' => $data,
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

    public function SaveStockMouthpiece(Request $request)
    {
        try {
            $data_list = db::connection('ympimis_2')->table('mouthpiece_lists')->get();
            $employee_name = $request->get('employee_name');

            for ($i = 0; $i < count($data_list); $i++) {
                $data_kanban = db::connection('ympimis_2')->table('mouthpiece_kanbans')->where('tag_kanban', $data_list[$i]->tag_kanban)->first();
                $data_item = db::connection('ympimis_2')->table('mouthpiece_stocks')->where('gmc', $data_list[$i]->gmc)->get();
                $jumlah = $data_item[0]->qty + $data_list[$i]->qty;

                DB::connection('ympimis_2')->table('mouthpiece_stocks')->where('gmc', $data_list[$i]->gmc)->update([
                    'qty' => $jumlah,
                ]);

                // DB::connection('ympimis_2')->table('mouthpiece_stocks')->insert([
                //     'request_id' => $data_list[$i]->request_id,
                //     'gmc' => $data_list[$i]->gmc,
                //     'desc' => $data_list[$i]->desc,
                //     'issue' => $data_list[$i]->issue,
                //     'uom' => $data_list[$i]->uom,
                //     'qty' => $data_list[$i]->qty,
                //     'tag_kanban' => $data_list[$i]->tag_kanban,
                //     'created_by' => $data_list[$i]->created_by,
                //     'created_at' => date('Y-m-d H:i:s'),
                //     'updated_at' => date('Y-m-d H:i:s')
                // ]);

                DB::connection('ympimis_2')->table('mouthpiece_logs')->insert([
                    'request_id' => $data_list[$i]->request_id,
                    'gmc' => $data_list[$i]->gmc,
                    'desc' => $data_list[$i]->desc,
                    'issue' => $data_list[$i]->issue,
                    'uom' => $data_list[$i]->uom,
                    'qty' => $data_list[$i]->qty,
                    'tag_kanban' => $data_list[$i]->tag_kanban,
                    'created_by' => $data_list[$i]->created_by,
                    'received_by' => $data_list[$i]->received_by,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                // DB::connection('ympimis_2')->table('production_results')->insert([
                //     'category' => 'production_result',
                //     'function' => 'completionItem',
                //     'action' => 'production_result',
                //     'result_date' => date('Y-m-d H:i:s'),
                //     'slip_number' => $data_list[$i]->request_id,
                //     'material_number' => $data_list[$i]->gmc,
                //     'material_description' => $data_list[$i]->desc,
                //     'issue_location' => $data_list[$i]->issue,
                //     'mstation' => 'W' . $data_kanban->mrpc . 'S10',
                //     'quantity' => $data_list[$i]->qty,
                //     'remark' => 'MIRAI',
                //     'created_by' => $data_list[$i]->created_by,
                //     'created_by_name' => $employee_name,
                //     'created_at' => date('Y-m-d H:i:s'),
                //     'updated_at' => date('Y-m-d H:i:s'),
                // ]);
            }

            db::connection('ympimis_2')->delete('delete from mouthpiece_lists');
            // $employee_id = $request->get('employee_id');
            // $employee_name = $request->get('employee_name');
            // $tag_kanban = $request->get('tag_kanban');
            // $data_kanban = db::connection('ympimis_2')->table('mouthpiece_kanbans')->where('tag_kanban', $tag_kanban)->first();
            // $tahun = date("y");
            // $bulan = date("m");
            // $prefix_now = $tahun.$bulan;
            // $code_generator = CodeGenerator::where('note','=','STOCK_MOUTHPIECE')->first();
            // $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
            // $request_id = $code_generator->prefix . $prefix_now . $number;
            // $code_generator->index = $code_generator->index+1;
            // $code_generator->save();

            // DB::connection('ympimis_2')->table('mouthpiece_stocks')->insert([
            //     'request_id' => $request_id,
            //     'gmc' => $data_kanban->gmc,
            //     'desc' => $data_kanban->desc,
            //     'issue' => $data_kanban->issue,
            //     'uom' => $data_kanban->uom,
            //     'qty' => $data_kanban->qty,
            //     'created_by' => $employee_id,
            //     'created_at' => date('Y-m-d H:i:s'),
            //     'updated_at' => date('Y-m-d H:i:s')
            // ]);

            // DB::connection('ympimis_2')->table('production_results')->insert([
            //     'category' => 'production_result',
            //     'function' => 'completionItem',
            //     'action' => 'production_result',
            //     'result_date' => date('Y-m-d H:i:s'),
            //     'slip_number' => $request_id,
            //     'material_number' => $data_kanban->gmc,
            //     'material_description' => $data_kanban->desc,
            //     'issue_location' => $data_kanban->issue,
            //     'mstation' => 'W'.$data_kanban->mrpc.'S10',
            //     'quantity' => $data_kanban->qty,
            //     'remark' => 'MIRAI',
            //     'created_by' => $employee_id,
            //     'created_by_name' => $employee_name,
            //     'created_at' => date('Y-m-d H:i:s'),
            //     'updated_at' => date('Y-m-d H:i:s')
            // ]);

            // $this->printStock($request_id, $gmc, $desc, $issue, $quantity, $uom, $time);

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

    public function SaveListMouthpiece(Request $request)
    {
        try {
            $employee_id = $request->get('employee_id');
            $employee_name = $request->get('employee_name');
            $tag_kanban = $request->get('tag_kanban');
            $data_list = db::connection('ympimis_2')->table('mouthpiece_lists')->where('tag_kanban', $tag_kanban)->get();

            // dd(count($data_list));

            if (count($data_list) > 0) {
                $response = array(
                    'status' => false,
                    'message' => 'Kanban Ini Sudah Masuk Kedalam List.',
                );
                return Response::json($response);
            } else {
                $data_kanban = db::connection('ympimis_2')->table('mouthpiece_kanbans')->where('tag_kanban', $tag_kanban)->first();
                $tahun = date("y");
                $bulan = date("m");
                $prefix_now = $tahun . $bulan;
                $code_generator = CodeGenerator::where('note', '=', 'STOCK_MOUTHPIECE')->first();
                $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
                $request_id = $code_generator->prefix . $prefix_now . $number;
                $code_generator->index = $code_generator->index + 1;
                $code_generator->save();

                DB::connection('ympimis_2')->table('mouthpiece_lists')->insert([
                    'request_id' => $request_id,
                    'gmc' => $data_kanban->gmc,
                    'desc' => $data_kanban->desc,
                    'issue' => $data_kanban->issue,
                    'uom' => $data_kanban->uom,
                    'qty' => $data_kanban->qty,
                    'tag_kanban' => $tag_kanban,
                    'created_by' => $employee_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $response = array(
                    'status' => true,
                );
                return Response::json($response);
            }
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
                // 'message' => 'Gagal, Pastikan Kanban Sesuai.',
            );
            return Response::json($response);
        }
    }

    public function printStock($request_id, $gmc, $desc, $issue, $quantity, $uom, $time)
    {

        if (str_contains(Auth::user()->role_code, 'S-MIS')) {
            $printer_name = 'Injection';
        }
        // else if (str_contains(Auth::user()->role_code, 'WH')) {
        //     $printer_name = 'FLO Printer LOG';
        // } else {
        //     $printer_name = 'KDO MP';
        // }

        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        // if ($remark == 'REPRINT') {
        //     $printer->setJustification(Printer::JUSTIFY_CENTER);
        //     $printer->setReverseColors(true);
        //     $printer->setTextSize(2, 2);
        //     $printer->text(" REPRINT " . "\n");
        //     $printer->feed(1);
        // }

        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setUnderline(true);
        $printer->text('Storage Location:');
        $printer->setUnderline(false);
        $printer->feed(1);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(3, 3);
        $printer->text(strtoupper($issue . "\n"));
        $printer->initialize();

        $printer->setUnderline(true);
        $printer->text('SLIP:');
        $printer->feed(1);
        $printer->setUnderline(false);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->qrCode($request_id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
        $printer->text($request_id . '-' . $gmc . "\n");

        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setUnderline(true);
        $printer->text('Destination:');
        $printer->setUnderline(false);
        $printer->feed(1);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(5, 3);
        $printer->text(strtoupper('PENTAGON' . "\n\n"));
        $printer->initialize();

        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setUnderline(true);
        $printer->text('Qty:');
        $printer->setUnderline(false);
        $printer->feed(1);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(5, 3);
        $printer->text(strtoupper($quantity . '(' . $uom . ')' . "\n\n"));
        $printer->initialize();

        $printer->initialize();
        $printer->setUnderline(true);
        $printer->text('Receive Date:');
        $printer->setUnderline(false);
        $printer->feed(1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(4, 2);
        $printer->text(date('d-M-Y', strtotime($time)) . "\n\n");
        $printer->initialize();
        // $printer->text("No |GMC     | Description                 | Qty ");
        $total_qty = 0;
        // for ($i = 0; $i < count($knock_down_details); $i++) {
        // $number = $this->writeString(1 + 1, 2, ' ');
        // $qty = $this->writeString($knock_down_details[$i]->quantity, 4, ' ');
        // $material_description = substr($knock_down_details[$i]->material_description, 0, 27);
        // $material_description = $this->writeString($material_description, 27, ' ');
        // $printer->text($number . " |" . $knock_down_details[$i]->material_number . " | " . $material_description . " | " . $qty);
        // $total_qty += $knock_down_details[$i]->quantity;
        // }
        $printer->feed(2);
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("------------------------------------");
        $printer->feed(1);
        $printer->text("|PIC:             |PIC:            |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("|Production       |Pentagon        |");
        $printer->feed(1);
        $printer->text("------------------------------------");
        $printer->feed(2);
        // $printer->initialize();
        // $printer->text("Total Qty: " . $total_qty . "\n");
        $printer->feed(2);
        $printer->feed(2);
        $printer->cut();
        $printer->close();
    }

    public function FetchMouthpieceStock(Request $request)
    {
        try {
            $date = $request->get('date');

            $data = '';
            if ($date == null) {
                $data = db::connection('ympimis_2')->select('select * from mouthpiece_stocks');
            } else {
                $data = db::connection('ympimis_2')->select('select * from mouthpiece_stocks where DATE_FORMAT(created_at , "%Y-%m-%d") = "' . $date . '"');
            }

            $loc = $request->get('loc');
            if ($loc == 'CL91') {
                $resume = db::connection('ympimis_2')->select('select * from mouthpiece_stocks where gmc = "VAY6630"');
                $list = db::connection('ympimis_2')->select('select * from mouthpiece_request_lists where issue = "CL91" and remark = "list"');
            } else if ($loc == 'SX91') {
                $resume = db::connection('ympimis_2')->select('select * from mouthpiece_stocks where gmc IN ("VAY6270", "VAR9870")');
                $list = db::connection('ympimis_2')->select('select * from mouthpiece_request_lists where issue = "SX91" and remark = "list"');
            }

            $response = array(
                'status' => true,
                'data' => $data,
                'resume' => $resume,
                'list' => $list,
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

    public function FetchMouthpieceStockDetail(Request $request)
    {
        try {
            $gmc = $request->get('gmc');

            $resumes = db::connection('ympimis_2')->table('mouthpiece_stocks')->where('gmc', $gmc)->first();
            $datas = db::connection('ympimis_2')->table('mouthpiece_stocks')->where('gmc', $gmc)->get();

            $response = array(
                'status' => true,
                'resumes' => $resumes,
                'datas' => $datas,
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

    public function IndexRequestMouthpiece($loc)
    {
        $title = 'Mouthpiece Request';
        $title_jp = '職長教育の報告';
        $user = strtoupper(Auth::user()->username);
        $name = Auth::user()->name;

        $emp = db::table('employee_syncs')->whereNull('end_date')
        ->where('department', 'Woodwind Instrument - Assembly (WI-A) Department')->get();

        return view('mouthpiece.index_request', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee_id' => $user,
            'name' => $name,
            'loc' => $loc,
            'emp' => $emp,
        ))->with('page', 'Mouthpiece');
    }

    public function SaveRequestMouthpiece(Request $request)
    {
        try {
            $gmc = $request->get('gmc');
            $loc = $request->get('loc');
            $desc = $request->get('desc');
            $uom = $request->get('uom');
            $quantity = $request->get('quantity');
            $pic = $request->get('pic');
            $emp_id = explode(',', $pic);

            $tahun = date("y");
            $bulan = date("m");
            $prefix_now = $tahun . $bulan;
            $code_generator = CodeGenerator::where('note', '=', 'request_mouthpiece')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $request_id = $code_generator->prefix . $prefix_now . $number;
            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $data_item = db::connection('ympimis_2')->table('mouthpiece_stocks')->where('gmc', $gmc)->get();
            $jumlah = $data_item[0]->qty - $quantity;

            DB::connection('ympimis_2')->table('mouthpiece_stocks')->where('gmc', $gmc)->update([
                'qty' => $jumlah,
            ]);

            DB::connection('ympimis_2')->table('mouthpiece_request_lists')->insert([
                'request_id' => $request_id,
                'gmc' => $gmc,
                'desc' => $desc,
                'issue' => $loc,
                'uom' => $uom,
                'qty' => $quantity,
                // 'tag_kanban' => $tag_kanban,
                'created_by' => $emp_id[0],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'remark' => 'list',
            ]);

            $time = date('Y-m-d H:i:s');

            // $this->printSlipRequest($request_id, $gmc, $desc, $loc, $quantity, $uom, $time);

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

    public function printSlipRequest($request_id, $gmc, $desc, $issue, $quantity, $uom, $time)
    {

        if (str_contains(Auth::user()->role_code, 'S-MIS')) {
            $printer_name = 'MIS';
        }
        // else if (str_contains(Auth::user()->role_code, 'WH')) {
        //     $printer_name = 'FLO Printer LOG';
        // } else {
        //     $printer_name = 'KDO MP';
        // }

        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setReverseColors(true);
        $printer->setTextSize(2, 2);
        $printer->text(" SLIP REQUEST MP " . "\n");
        $printer->feed(1);

        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setUnderline(true);
        $printer->text('From Location:');
        $printer->setUnderline(false);
        $printer->feed(1);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(3, 3);
        $printer->text(strtoupper($issue . "\n"));
        $printer->initialize();

        $printer->setUnderline(true);
        $printer->text('SLIP:');
        $printer->feed(1);
        $printer->setUnderline(false);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->qrCode($request_id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
        $printer->text($request_id . "\n");

        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setUnderline(true);
        $printer->text('Qty:');
        $printer->setUnderline(false);
        $printer->feed(1);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(5, 3);
        $printer->text(strtoupper($quantity . '(' . $uom . ')' . "\n\n"));
        $printer->initialize();

        $printer->initialize();
        $printer->setUnderline(true);
        $printer->text('Created At:');
        $printer->setUnderline(false);
        $printer->feed(1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(4, 2);
        $printer->text(date('d-M-Y', strtotime($time)) . "\n\n");
        $printer->feed(1);

        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setUnderline(true);
        $printer->text('* Serahkan slip ini ke operator pentagon.');
        $printer->setUnderline(false);

        $printer->feed(2);
        $printer->cut();
        $printer->close();
    }

    public function ScanSlipRequestMouthpiece(Request $request)
    {
        try {
            $value = $request->get('slip');

            $data = DB::connection('ympimis_2')->table('mouthpiece_request_lists')->where('request_id', $value)->where('remark', 'list')->first();

            if (count($data) > 0) {

                db::connection('ympimis_2')->table('mouthpiece_request_lists')->where('request_id', $value)
                ->update(['remark' => 'confirm']);
                $response = array(
                    'status' => true,
                    'data' => $data,
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Slip sudah diterima pentagon.',
                );
                return Response::json($response);
            }

        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function ScanIdRequestMouthpiece(Request $request)
    {
        try {
            $request_id = $request->get('request_id');
            $tag = $request->get('tag');

            $data = DB::connection('ympimis_2')->table('mouthpiece_request_lists')->where('request_id', $request_id)->first();
            $emp = explode("/", $data->created_by);

            $employee_id = db::table('employees')
            ->where('tag', $tag)
            ->orWhere('employee_id', $tag)
            ->first();

            $employee_sync = EmployeeSync::where('employee_id', '=', $employee_id->employee_id)->first();

            if ($employee_sync == "") {
                $response = array(
                    'status' => false,
                    'message' => "ID karyawan tidak ditemukan",
                );
                return Response::json($response);
            }

            if ($emp[0] != $employee_sync->employee_id) {
                $response = array(
                    'status' => false,
                    'message' => "PIC pengambilan tidak sesuai",
                );
                return Response::json($response);
            }

            $response = array(
                'status' => true,
                'employee' => $employee_sync,
                'data' => $data,
                'message' => "PIC pengambilan sesuai",
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

    public function UpdateRequestMouthpiece(Request $request)
    {
        try {
            $value = $request->get('value');
            $pic_mp = $request->get('pic_mp');

            // db::connection('ympimis_2')->table('mouthpiece_request_logs')->where('request_id', $value)->update([
            //     'remark' => 'confirm',
            // ]);

            $data = db::connection('ympimis_2')->table('mouthpiece_request_lists')->where('request_id', $value)->first();
            $emp = explode("/", $data->created_by);

            // db::connection('ympimis_2')->table('goods_movements')->insert([
            //     'category' => 'goods_movement',
            //     'function' => 'inputGoodsMovement',
            //     'action' => 'goods_movement',
            //     'result_date' => $data->created_at,
            //     'reference_number' => null,
            //     'slip_number' => $data->request_id,
            //     'serial_number' => null,
            //     'material_number' => $data->gmc,
            //     'material_description' => $data->desc,
            //     'issue_location' => 'VN91',
            //     'receive_location' => $data->issue,
            //     'quantity' => $data->qty,
            //     'synced' => null,
            //     'synced_by' => null,
            //     'remark' => 'MIRAI',
            //     'created_by' => strtoupper($emp[0]),
            //     'created_by_name' => $emp[1],
            //     'created_at' => date('Y-m-d H:i:s'),
            //     'updated_at' => date('Y-m-d H:i:s'),
            // ]);

            db::connection('ympimis_2')->table('mouthpiece_request_logs')->insert([
                'request_id' => $data->request_id,
                'gmc' => $data->gmc,
                'desc' => $data->desc,
                'issue' => $data->issue,
                'uom' => $data->uom,
                'qty' => $data->qty,
                'remark' => $data->remark,
                'created_by' => $data->created_by,
                'received_by' => $pic_mp,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $data_logs = db::connection('ympimis_2')->table('mouthpiece_request_logs')->where('request_id', $value)->first();
            $emp_logs = explode("/", $data_logs->created_by);

            db::connection('ympimis_2')->table('mouthpiece_request_lists')->where('request_id', $value)->delete();

            $request_id = $data_logs->request_id;
            $gmc = $data_logs->gmc;
            $desc = $data_logs->desc;
            $loc = $data_logs->issue;
            $quantity = $data_logs->qty;
            $uom = $data_logs->uom;
            $time = date('Y-m-d H:i:s');

            // $this->printSlipReceive($request_id, $gmc, $desc, $loc, $quantity, $uom, $time, $emp_logs[0]);

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

    public function printSlipReceive($request_id, $gmc, $desc, $issue, $quantity, $uom, $time, $emp)
    {
        $login = Auth::user()->username;
        if (str_contains(Auth::user()->role_code, 'S-MIS')) {
            $printer_name = 'MIS';
        }
        // else if (str_contains(Auth::user()->role_code, 'WH')) {
        //     $printer_name = 'FLO Printer LOG';
        // } else {
        //     $printer_name = 'KDO MP';
        // }

        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setReverseColors(true);
        $printer->setTextSize(2, 2);
        $printer->text(" SLIP RECEIVE MP " . "\n");
        $printer->feed(1);

        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setUnderline(true);
        $printer->text('From Location:');
        $printer->setUnderline(false);
        $printer->feed(1);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(3, 3);
        $printer->text(strtoupper('PN91' . "\n"));
        $printer->initialize();

        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setUnderline(true);
        $printer->text('To Location:');
        $printer->setUnderline(false);
        $printer->feed(1);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(3, 3);
        $printer->text(strtoupper($issue . "\n"));
        $printer->initialize();

        $printer->initialize();
        $printer->setUnderline(true);
        $printer->text('SLIP:');
        $printer->feed(1);
        $printer->setUnderline(false);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->qrCode($request_id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
        $printer->text($request_id . "\n");

        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setUnderline(true);
        $printer->text('Qty:');
        $printer->setUnderline(false);
        $printer->feed(1);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(5, 3);
        $printer->text(strtoupper($quantity . '(' . $uom . ')' . "\n\n"));
        $printer->initialize();

        $printer->initialize();
        $printer->setUnderline(true);
        $printer->text('Created At:');
        $printer->setUnderline(false);
        $printer->feed(1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(4, 2);
        $printer->text(date('d-M-Y', strtotime($time)) . "\n\n");
        $printer->feed(1);

        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("------------------------------------");
        $printer->feed(1);
        $printer->text("|Pentagon:        |Assy:           |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("|" . $login . "        |" . $emp . "       |");
        $printer->feed(1);
        $printer->text("------------------------------------");

        $printer->feed(2);
        $printer->cut();
        $printer->close();
    }

    public function FetchRequestMouthpieceAssy(Request $request)
    {
        try {
            $request = db::connection('ympimis_2')->table('mouthpiece_request_lists')->where('remark', 'list')->get();
            $resume = db::connection('ympimis_2')->table('mouthpiece_request_logs')->where('remark', 'confirm')->orderBy('id', 'desc')->get();

            $cek_request = db::connection('ympimis_2')->table('mouthpiece_request_lists')->where('remark', 'list')->whereNull('packing')->get();

            if (count($request) > 0) {
                $response = array(
                    'status' => true,
                    'request' => $request,
                    'resume' => $resume,
                    'cek_request' => $cek_request,
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'resume' => $resume,
                );
                return Response::json($response);
            }
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function UpdatePersiapanRequest(Request $request)
    {
        try {
            $req_id = $request->get('request_id');
            db::connection('ympimis_2')->table('mouthpiece_request_lists')->where('request_id', $req_id)->update([
                'packing' => 'onprogress',
            ]);
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

    public function UpdateDoneRequest(Request $request)
    {
        try {
            $req_id = $request->get('request_id');
            db::connection('ympimis_2')->table('mouthpiece_request_lists')->where('request_id', $req_id)->update([
                'packing' => 'finished',
            ]);
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

    public function IndexDetailRequest($req_id)
    {
        $title = 'Mouthpiece Request';
        $title_jp = '職長教育の報告';
        $user = strtoupper(Auth::user()->username);
        $name = Auth::user()->name;

        return view('mouthpiece.detail_request', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee_id' => $user,
            'name' => $name,
            'req_id' => $req_id,
        ))->with('page', 'Mouthpiece');
    }

    public function FetchMouthpieceLog(Request $request)
    {
        try {
            $resumes = db::connection('ympimis_2')->table('mouthpiece_logs')->get();

            $emp = db::table('employee_syncs')->where('end_date', null)->select('name', 'employee_id')->get();

            $response = array(
                'status' => true,
                'resumes' => $resumes,
                'emp' => $emp,
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

    public function IndexOperatorFukiage(Request $request)
    {
        $title = 'Operator Fukiage';
        $title_jp = '???';
        $user = strtoupper(Auth::user()->username);
        $name = Auth::user()->name;

        return view('mouthpiece.operator_fukiage', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee_id' => $user,
            'name' => $name,
        ))->with('page', 'Operator Fukiage');
    }

    public function IndexNgRateMouthpiece(Request $request)
    {
        return view('mouthpiece.index_monitoring_ng_rate')
        ->with('title', 'Display NG Rate Recorder')
        ->with('title_jp', 'リコーダー不良傾向の画面')
        ->with('page', 'Display NG Rate Recorder')
        ->with('now', date('Y-m-d'));
    }

    public function FetchNgRateMouthpiece(Request $request)
    {
        try {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = "'" . date('Y-m-01') . "'";
                    $last = "'" . date('Y-m-d') . "'";
                    $dateTitleFirst = date('d M Y', strtotime(date('Y-m-01')));
                    $dateTitleLast = date('d M Y', strtotime(date('Y-m-d')));
                } else {
                    $first = "'" . date('Y-m-01') . "'";
                    $last = "'" . $date_to . "'";
                    $dateTitleFirst = date('d M Y', strtotime(date('Y-m-01')));
                    $dateTitleLast = date('d M Y', strtotime($date_to));
                }
            } else {
                if ($date_to == "") {
                    $first = "'" . $date_from . "'";
                    $last = "'" . date('Y-m-d') . "'";
                    $dateTitleFirst = date('d M Y', strtotime($date_from));
                    $dateTitleLast = date('d M Y', strtotime(date('Y-m-d')));
                } else {
                    $first = "'" . $date_from . "'";
                    $last = "'" . $date_to . "'";
                    $dateTitleFirst = date('d M Y', strtotime($date_from));
                    $dateTitleLast = date('d M Y', strtotime($date_to));
                }
            }

            $week_date = DB::SELECT("SELECT
                week_date,
                fiscal_year,
                remark
                FROM
                weekly_calendars
                WHERE
                week_date <= " . $last . " AND week_date >= " . $first . "
                and remark != 'H'
                and week_date >= '2021-09-16'");

            $first_fy = DB::SELECT("SELECT
              IF
              (( SELECT fiscal_year FROM weekly_calendars WHERE week_date = DATE_ADD( DATE( NOW()), INTERVAL - 1 YEAR )) = 'FY198', '2022-03-01', week_date ) AS week_date
              FROM
              weekly_calendars
              WHERE
              fiscal_year = (
                SELECT
                fiscal_year
                FROM
                weekly_calendars
                WHERE
                week_date = DATE_ADD( DATE( NOW()), INTERVAL - 1 YEAR ))
              ORDER BY
              week_date
              LIMIT 1");

            $last_fy = DB::SELECT("SELECT
              week_date
              FROM
              weekly_calendars
              WHERE
              fiscal_year = (
                  SELECT
                  fiscal_year
                  FROM
                  weekly_calendars
                  WHERE
                  week_date = DATE_ADD( DATE( NOW()), INTERVAL - 1 YEAR ))
              ORDER BY
              week_date DESC
              LIMIT 1");

            $ng_target = DB::SELECT("SELECT COALESCE
              ( SUM( a.qty_ng ), 0 ) AS qty_ng,
              COALESCE ( sum( a.qty_box ), 0 ) AS qty_box
              FROM
              (
                  SELECT
                  date AS week_date,
                  sum( ng_head ) + sum( ng_foot )+ sum( ng_middle )+ sum( ng_block ) AS qty_ng,
                  0 AS qty_box
                  FROM
                  rc_ng_boxes
                  WHERE
                  date >= '" . $first_fy[0]->week_date . "'
                  AND date <= '" . $last_fy[0]->week_date . "'
                  GROUP BY
                  date UNION ALL
                  SELECT
                  DATE( created_at ) AS week_date,
                  0 AS qty_ng,
                  IF
                  ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box
                  FROM
                  rc_box_results
                  JOIN (
                  SELECT
                  operator_kensa AS employee_id,
                  tray
                  FROM
                  rc_kensas
                  WHERE
                  DATE( created_at ) >= '" . $first_fy[0]->week_date . "'
                  AND DATE( created_at ) <= '" . $last_fy[0]->week_date . "' AND qty_ng != 0 GROUP BY operator_kensa, tray ) AS kensas ON kensas.employee_id = rc_box_results.operator_kensa WHERE DATE( created_at ) >= '" . $first_fy[0]->week_date . "'
                  AND DATE( created_at ) <= '" . $last_fy[0]->week_date . "'
                  GROUP BY
                  DATE( created_at ),
                  rc_box_results.product
              ) a");

              $ng_target_head = DB::SELECT("SELECT COALESCE
                  ( SUM( a.qty_ng ), 0 ) AS qty_ng,
                  COALESCE ( sum( a.qty_box ), 0 ) AS qty_box
                  FROM
                  (
                      SELECT
                      date AS week_date,
                      sum( ng_head ) AS qty_ng,
                      0 AS qty_box
                      FROM
                      rc_ng_boxes
                      WHERE
                      date >= '" . $first_fy[0]->week_date . "'
                      AND date <= '" . $last_fy[0]->week_date . "'
                      GROUP BY
                      date UNION ALL
                      SELECT
                      DATE( created_at ) AS week_date,
                      0 AS qty_ng,
                      IF
                      ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box
                      FROM
                      rc_box_results
                      JOIN (
                      SELECT
                      operator_kensa AS employee_id,
                      tray
                      FROM
                      rc_kensas
                      WHERE
                      DATE( created_at ) >= '" . $first_fy[0]->week_date . "'
                      AND DATE( created_at ) <= '" . $last_fy[0]->week_date . "' AND qty_ng != 0 GROUP BY operator_kensa, tray ) AS kensas ON kensas.employee_id = rc_box_results.operator_kensa WHERE DATE( created_at ) >= '" . $first_fy[0]->week_date . "'
                      AND DATE( created_at ) <= '" . $last_fy[0]->week_date . "'
                      GROUP BY
                      DATE( created_at ),
                      rc_box_results.product
                  ) a");

                  $ng_target_middle = DB::SELECT("SELECT COALESCE
                      ( SUM( a.qty_ng ), 0 ) AS qty_ng,
                      COALESCE ( sum( a.qty_box ), 0 ) AS qty_box
                      FROM
                      (
                          SELECT
                          date AS week_date,
                          sum( ng_middle ) AS qty_ng,
                          0 AS qty_box
                          FROM
                          rc_ng_boxes
                          WHERE
                          date >= '" . $first_fy[0]->week_date . "'
                          AND date <= '" . $last_fy[0]->week_date . "'
                          GROUP BY
                          date UNION ALL
                          SELECT
                          DATE( created_at ) AS week_date,
                          0 AS qty_ng,
                          IF
                          ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box
                          FROM
                          rc_box_results
                          JOIN (
                          SELECT
                          operator_kensa AS employee_id,
                          tray
                          FROM
                          rc_kensas
                          WHERE
                          DATE( created_at ) >= '" . $first_fy[0]->week_date . "'
                          AND DATE( created_at ) <= '" . $last_fy[0]->week_date . "' AND qty_ng != 0 GROUP BY operator_kensa, tray ) AS kensas ON kensas.employee_id = rc_box_results.operator_kensa WHERE DATE( created_at ) >= '" . $first_fy[0]->week_date . "'
                          AND DATE( created_at ) <= '" . $last_fy[0]->week_date . "'
                          GROUP BY
                          DATE( created_at ),
                          rc_box_results.product
                      ) a");

                      $ng_target_foot = DB::SELECT("SELECT COALESCE
                          ( SUM( a.qty_ng ), 0 ) AS qty_ng,
                          COALESCE ( sum( a.qty_box ), 0 ) AS qty_box
                          FROM
                          (
                              SELECT
                              date AS week_date,
                              sum( ng_foot ) AS qty_ng,
                              0 AS qty_box
                              FROM
                              rc_ng_boxes
                              WHERE
                              date >= '" . $first_fy[0]->week_date . "'
                              AND date <= '" . $last_fy[0]->week_date . "'
                              GROUP BY
                              date UNION ALL
                              SELECT
                              DATE( created_at ) AS week_date,
                              0 AS qty_ng,
                              IF
                              ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box
                              FROM
                              rc_box_results
                              JOIN (
                              SELECT
                              operator_kensa AS employee_id,
                              tray
                              FROM
                              rc_kensas
                              WHERE
                              DATE( created_at ) >= '" . $first_fy[0]->week_date . "'
                              AND DATE( created_at ) <= '" . $last_fy[0]->week_date . "' AND qty_ng != 0 GROUP BY operator_kensa, tray ) AS kensas ON kensas.employee_id = rc_box_results.operator_kensa WHERE DATE( created_at ) >= '" . $first_fy[0]->week_date . "'
                              AND DATE( created_at ) <= '" . $last_fy[0]->week_date . "'
                              GROUP BY
                              DATE( created_at ),
                              rc_box_results.product
                          ) a");

                          $ng_target_block = DB::SELECT("SELECT COALESCE
                              ( SUM( a.qty_ng ), 0 ) AS qty_ng,
                              COALESCE ( sum( a.qty_box ), 0 ) AS qty_box
                              FROM
                              (
                                  SELECT
                                  date AS week_date,
                                  sum( ng_block ) AS qty_ng,
                                  0 AS qty_box
                                  FROM
                                  rc_ng_boxes
                                  WHERE
                                  date >= '" . $first_fy[0]->week_date . "'
                                  AND date <= '" . $last_fy[0]->week_date . "'
                                  GROUP BY
                                  date UNION ALL
                                  SELECT
                                  DATE( created_at ) AS week_date,
                                  0 AS qty_ng,
                                  IF
                                  ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box
                                  FROM
                                  rc_box_results
                                  JOIN (
                                  SELECT
                                  operator_kensa AS employee_id,
                                  tray
                                  FROM
                                  rc_kensas
                                  WHERE
                                  DATE( created_at ) >= '" . $first_fy[0]->week_date . "'
                                  AND DATE( created_at ) <= '" . $last_fy[0]->week_date . "' AND qty_ng != 0 GROUP BY operator_kensa, tray ) AS kensas ON kensas.employee_id = rc_box_results.operator_kensa WHERE DATE( created_at ) >= '" . $first_fy[0]->week_date . "'
                                  AND DATE( created_at ) <= '" . $last_fy[0]->week_date . "'
                                  GROUP BY
                                  DATE( created_at ),
                                  rc_box_results.product
                              ) a");

                              $ng_rates = [];
                              $ng_rates_head = [];
                              $ng_rates_middle = [];
                              $ng_rates_foot = [];
                              $ng_rates_block = [];
                              for ($i = 0; $i < count($week_date); $i++) {
                                $interval = 1;
                                if ($week_date[$i]->week_date <= '2021-11-19') {
                                    $ng_rate = DB::SELECT("
                                        SELECT
                                        '" . $week_date[$i]->week_date . "' AS week_date,
                                        COALESCE ( SUM( a.qty_ng ), 0 ) AS qty_ng,
                                        COALESCE ( sum( a.qty_box ), 0 ) AS qty_box
                                        FROM
                                        (
                                        SELECT
                                        DATE( created_at ) AS week_date,
                                        SUM_OF_LIST ( GROUP_CONCAT(rc_kensas.ng_count) ) AS qty_ng,
                                        0 AS qty_box
                                        FROM
                                        rc_kensas
                                        WHERE
                                        DATE( created_at ) = '" . $week_date[$i]->week_date . "'
                                        GROUP BY
                                        DATE( created_at ) UNION ALL
                                        SELECT
                                        DATE( created_at ) AS week_date,
                                        0 AS qty_ng,
                                        IF
                                        ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM(qty_box) * 20, SUM(qty_box) * 50 ) AS qty_box
                                        FROM
                                        rc_box_results
                                        WHERE
                                        DATE( created_at ) = '" . $week_date[$i]->week_date . "'
                                        GROUP BY
                                        DATE( created_at ),rc_box_results.product) a");
                                    $ng_rate_head = DB::SELECT("
                                      SELECT
                                      '" . $week_date[$i]->week_date . "' AS week_date,
                                      COALESCE ( SUM( a.qty_ng ), 0 ) AS qty_ng,
                                      COALESCE ( sum( a.qty_box ), 0 ) AS qty_box
                                      FROM
                                      (
                                      SELECT
                                      DATE( rc_kensas.created_at ) AS week_date,
                                      SUM_OF_LIST ( GROUP_CONCAT( rc_kensas.ng_count ) ) AS qty_ng,
                                      0 AS qty_box
                                      FROM
                                      rc_kensas
                                      LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensas.material_number
                                      WHERE
                                      ( DATE( rc_kensas.created_at ) = '" . $week_date[$i]->week_date . "' AND injection_parts.remark = 'injection' AND injection_parts.deleted_at IS NULL AND injection_parts.part_code = 'HJ' )
                                      OR ( DATE( rc_kensas.created_at ) = '" . $week_date[$i]->week_date . "' AND injection_parts.remark = 'injection' AND injection_parts.deleted_at IS NULL AND injection_parts.part_code = 'A YRF H' )
                                      GROUP BY
                                      DATE( rc_kensas.created_at ) UNION ALL
                                      SELECT
                                      DATE( created_at ) AS week_date,
                                      0 AS qty_ng,
                                      IF
                                      ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box
                                      FROM
                                      rc_box_results
                                      WHERE
                                      DATE( created_at ) = '" . $week_date[$i]->week_date . "'
                                      GROUP BY
                                      DATE( created_at ),
                                      rc_box_results.product
                                  ) a");

                                    $ng_rate_middle = DB::SELECT("
                                      SELECT
                                      '" . $week_date[$i]->week_date . "' AS week_date,
                                      COALESCE ( SUM( a.qty_ng ), 0 ) AS qty_ng,
                                      COALESCE ( sum( a.qty_box ), 0 ) AS qty_box
                                      FROM
                                      (
                                      SELECT
                                      DATE( rc_kensas.created_at ) AS week_date,
                                      SUM_OF_LIST ( GROUP_CONCAT( rc_kensas.ng_count ) ) AS qty_ng,
                                      0 AS qty_box
                                      FROM
                                      rc_kensas
                                      LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensas.material_number
                                      WHERE
                                      ( DATE( rc_kensas.created_at ) = '" . $week_date[$i]->week_date . "' AND injection_parts.remark = 'injection' AND injection_parts.deleted_at IS NULL AND injection_parts.part_code like '%MJ%' )
                                      OR ( DATE( rc_kensas.created_at ) = '" . $week_date[$i]->week_date . "' AND injection_parts.remark = 'injection' AND injection_parts.deleted_at IS NULL AND injection_parts.part_code = 'A YRF B' )
                                      GROUP BY
                                      DATE( rc_kensas.created_at ) UNION ALL
                                      SELECT
                                      DATE( created_at ) AS week_date,
                                      0 AS qty_ng,
                                      IF
                                      ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box
                                      FROM
                                      rc_box_results
                                      WHERE
                                      DATE( created_at ) = '" . $week_date[$i]->week_date . "'
                                      GROUP BY
                                      DATE( created_at ),
                                      rc_box_results.product
                                  ) a");

                                    $ng_rate_foot = DB::SELECT("
                                      SELECT
                                      '" . $week_date[$i]->week_date . "' AS week_date,
                                      COALESCE ( SUM( a.qty_ng ), 0 ) AS qty_ng,
                                      COALESCE ( sum( a.qty_box ), 0 ) AS qty_box
                                      FROM
                                      (
                                      SELECT
                                      DATE( rc_kensas.created_at ) AS week_date,
                                      SUM_OF_LIST ( GROUP_CONCAT( rc_kensas.ng_count ) ) AS qty_ng,
                                      0 AS qty_box
                                      FROM
                                      rc_kensas
                                      LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensas.material_number
                                      WHERE
                                      ( DATE( rc_kensas.created_at ) = '" . $week_date[$i]->week_date . "' AND injection_parts.remark = 'injection' AND injection_parts.deleted_at IS NULL AND injection_parts.part_code like '%FJ%' )
                                      GROUP BY
                                      DATE( rc_kensas.created_at ) UNION ALL
                                      SELECT
                                      DATE( created_at ) AS week_date,
                                      0 AS qty_ng,
                                      IF
                                      ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box
                                      FROM
                                      rc_box_results
                                      WHERE
                                      DATE( created_at ) = '" . $week_date[$i]->week_date . "'
                                      GROUP BY
                                      DATE( created_at ),
                                      rc_box_results.product
                                  ) a");

                                    $ng_rate_block = DB::SELECT("
                                      SELECT
                                      '" . $week_date[$i]->week_date . "' AS week_date,
                                      COALESCE ( SUM( a.qty_ng ), 0 ) AS qty_ng,
                                      COALESCE ( sum( a.qty_box ), 0 ) AS qty_box
                                      FROM
                                      (
                                      SELECT
                                      DATE( rc_kensas.created_at ) AS week_date,
                                      SUM_OF_LIST ( GROUP_CONCAT( rc_kensas.ng_count ) ) AS qty_ng,
                                      0 AS qty_box
                                      FROM
                                      rc_kensas
                                      LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensas.material_number
                                      WHERE
                                      ( DATE( rc_kensas.created_at ) = '" . $week_date[$i]->week_date . "' AND injection_parts.remark = 'injection' AND injection_parts.deleted_at IS NULL AND injection_parts.part_code like '%BJ%' )
                                      OR ( DATE( rc_kensas.created_at ) = '" . $week_date[$i]->week_date . "' AND injection_parts.remark = 'injection' AND injection_parts.deleted_at IS NULL AND injection_parts.part_code = 'A YRF S' )
                                      GROUP BY
                                      DATE( rc_kensas.created_at ) UNION ALL
                                      SELECT
                                      DATE( created_at ) AS week_date,
                                      0 AS qty_ng,
                                      IF
                                      ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box
                                      FROM
                                      rc_box_results
                                      WHERE
                                      DATE( created_at ) = '" . $week_date[$i]->week_date . "'
                                      GROUP BY
                                      DATE( created_at ),
                                      rc_box_results.product
                                  ) a");
                                } else if ($week_date[$i]->week_date >= '2021-11-20') {
                                    $ng_rate = DB::SELECT("SELECT
                                      '" . $week_date[$i]->week_date . "' AS week_date,
                                      COALESCE ( SUM( a.qty_ng ), 0 ) AS qty_ng,
                                      COALESCE ( sum( a.qty_box ), 0 ) AS qty_box
                                      FROM
                                      (
                                      SELECT
                                      date AS week_date,
                                      sum( ng_head ) + sum( ng_foot )+ sum( ng_middle )+ sum( ng_block ) AS qty_ng,
                                      0 AS qty_box
                                      FROM
                                      rc_ng_boxes
                                      WHERE
                                      date = '" . $week_date[$i]->week_date . "'
                                      GROUP BY date UNION ALL
                                      SELECT
                                      DATE( created_at ) AS week_date,
                                      0 AS qty_ng,
                                      IF
                                      ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box
                                      FROM
                                      rc_box_results
                                      JOIN ( SELECT operator_kensa AS employee_id, tray FROM rc_kensas WHERE date( created_at ) = '" . $week_date[$i]->week_date . "' AND qty_ng != 0 GROUP BY operator_kensa,tray ) AS kensas ON kensas.employee_id = rc_box_results.operator_kensa
                                      WHERE
                                      DATE( created_at ) = '" . $week_date[$i]->week_date . "'
                                      GROUP BY
                                      DATE( created_at ),
                                      rc_box_results.product
                                  ) a");

                                    $ng_rate_head = DB::SELECT("SELECT
                                      '" . $week_date[$i]->week_date . "' AS week_date,
                                      COALESCE ( SUM( a.qty_ng ), 0 ) AS qty_ng,
                                      COALESCE ( sum( a.qty_box ), 0 ) AS qty_box
                                      FROM
                                      (
                                      SELECT
                                      date AS week_date,
                                      sum( ng_head ) AS qty_ng,
                                      0 AS qty_box
                                      FROM
                                      rc_ng_boxes
                                      WHERE
                                      date = '" . $week_date[$i]->week_date . "'
                                      GROUP BY date UNION ALL
                                      SELECT
                                      DATE( created_at ) AS week_date,
                                      0 AS qty_ng,
                                      IF
                                      ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box
                                      FROM
                                      rc_box_results
                                      JOIN ( SELECT operator_kensa AS employee_id, tray FROM rc_kensas WHERE date( created_at ) = '" . $week_date[$i]->week_date . "' AND qty_ng != 0 GROUP BY operator_kensa,tray ) AS kensas ON kensas.employee_id = rc_box_results.operator_kensa
                                      WHERE
                                      DATE( created_at ) = '" . $week_date[$i]->week_date . "'
                                      GROUP BY
                                      DATE( created_at ),
                                      rc_box_results.product
                                  ) a");

                                    $ng_rate_middle = DB::SELECT("SELECT
                                      '" . $week_date[$i]->week_date . "' AS week_date,
                                      COALESCE ( SUM( a.qty_ng ), 0 ) AS qty_ng,
                                      COALESCE ( sum( a.qty_box ), 0 ) AS qty_box
                                      FROM
                                      (
                                      SELECT
                                      date AS week_date,
                                      sum( ng_middle ) AS qty_ng,
                                      0 AS qty_box
                                      FROM
                                      rc_ng_boxes
                                      WHERE
                                      date = '" . $week_date[$i]->week_date . "'
                                      GROUP BY date UNION ALL
                                      SELECT
                                      DATE( created_at ) AS week_date,
                                      0 AS qty_ng,
                                      IF
                                      ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box
                                      FROM
                                      rc_box_results
                                      JOIN ( SELECT operator_kensa AS employee_id, tray FROM rc_kensas WHERE date( created_at ) = '" . $week_date[$i]->week_date . "' AND qty_ng != 0 GROUP BY operator_kensa,tray ) AS kensas ON kensas.employee_id = rc_box_results.operator_kensa
                                      WHERE
                                      DATE( created_at ) = '" . $week_date[$i]->week_date . "'
                                      GROUP BY
                                      DATE( created_at ),
                                      rc_box_results.product
                                  ) a");

                                    $ng_rate_foot = DB::SELECT("SELECT
                                      '" . $week_date[$i]->week_date . "' AS week_date,
                                      COALESCE ( SUM( a.qty_ng ), 0 ) AS qty_ng,
                                      COALESCE ( sum( a.qty_box ), 0 ) AS qty_box
                                      FROM
                                      (
                                      SELECT
                                      date AS week_date,
                                      sum( ng_foot ) AS qty_ng,
                                      0 AS qty_box
                                      FROM
                                      rc_ng_boxes
                                      WHERE
                                      date = '" . $week_date[$i]->week_date . "'
                                      GROUP BY date UNION ALL
                                      SELECT
                                      DATE( created_at ) AS week_date,
                                      0 AS qty_ng,
                                      IF
                                      ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box
                                      FROM
                                      rc_box_results
                                      JOIN ( SELECT operator_kensa AS employee_id, tray FROM rc_kensas WHERE date( created_at ) = '" . $week_date[$i]->week_date . "' AND qty_ng != 0 GROUP BY operator_kensa,tray ) AS kensas ON kensas.employee_id = rc_box_results.operator_kensa
                                      WHERE
                                      DATE( created_at ) = '" . $week_date[$i]->week_date . "'
                                      GROUP BY
                                      DATE( created_at ),
                                      rc_box_results.product
                                  ) a");

                                    $ng_rate_block = DB::SELECT("SELECT
                                      '" . $week_date[$i]->week_date . "' AS week_date,
                                      COALESCE ( SUM( a.qty_ng ), 0 ) AS qty_ng,
                                      COALESCE ( sum( a.qty_box ), 0 ) AS qty_box
                                      FROM
                                      (
                                      SELECT
                                      date AS week_date,
                                      sum( ng_block ) AS qty_ng,
                                      0 AS qty_box
                                      FROM
                                      rc_ng_boxes
                                      WHERE
                                      date = '" . $week_date[$i]->week_date . "'
                                      GROUP BY date UNION ALL
                                      SELECT
                                      DATE( created_at ) AS week_date,
                                      0 AS qty_ng,
                                      IF
                                      ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box
                                      FROM
                                      rc_box_results
                                      JOIN ( SELECT operator_kensa AS employee_id, tray FROM rc_kensas WHERE date( created_at ) = '" . $week_date[$i]->week_date . "' AND qty_ng != 0 GROUP BY operator_kensa,tray ) AS kensas ON kensas.employee_id = rc_box_results.operator_kensa
                                      WHERE
                                      DATE( created_at ) = '" . $week_date[$i]->week_date . "'
                                      GROUP BY
                                      DATE( created_at ),
                                      rc_box_results.product
                                  ) a");
                                }
                                array_push($ng_rates, $ng_rate);
                                array_push($ng_rates_head, $ng_rate_head);
                                array_push($ng_rates_middle, $ng_rate_middle);
                                array_push($ng_rates_foot, $ng_rate_foot);
                                array_push($ng_rates_block, $ng_rate_block);
                            }

                            $now = date('Y-m-d');

                            $response = array(
                                'status' => true,
                                'ng_rate' => $ng_rates,
                                'ng_rate_head' => $ng_rates_head,
                                'ng_rate_foot' => $ng_rates_foot,
                                'ng_rate_middle' => $ng_rates_middle,
                                'ng_rate_block' => $ng_rates_block,
                                'ng_target' => $ng_target,
                                'ng_target_head' => $ng_target_head,
                                'ng_target_middle' => $ng_target_middle,
                                'ng_target_foot' => $ng_target_foot,
                                'ng_target_block' => $ng_target_block,
                                'dateTitleFirst' => $dateTitleFirst,
                                'dateTitleLast' => $dateTitleLast,
                                'now' => $now,
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
