<?php

namespace App\Http\Controllers;

use App\CodeGenerator;
use App\Http\Controllers\Controller;
use App\InjectionInventory;
use App\Material;
use App\MaterialPlantDataList;
use App\MiddleInventory;
use App\NgList;
use App\ReturnList;
use App\ReturnLog;
use App\ReturnMaterial;
use App\SapCompletion;
use App\StorageLocation;
use App\User;
use Carbon\Carbon;
use DataTables;
use Date;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Response;
use Yajra\DataTables\Exception;

class TransactionController extends Controller
{

    private $storage_location;
    private $repair_location;
    private $thermal_printers;
    public function __construct()
    {
        $this->middleware('auth');
        $this->storage_location = [
            'CL91',
            'CLB9',
            'CL51',
            'CL21',
            'FL91',
            'FL51',
            'FL21',
            'SX91',
            'SX51',
            'SX21',
            'VN91',
            'VN51',
            'VN21',
            'VN11',
            'VNA0',
            'FLA1',
            'FLA2',
            'SXA0',
            'SXA1',
            'SXA2',
            'CLA0',
            'CLA2',
            'FLA0',
            'RC91',
            'RC11',

        ];
        $this->repair_location = [
            'FA0R',
            'LA0R',
            'SA0R',
            'VA0R',
            'FA1R',
            'LA1R',
            'SA1R',
            'CL21',
            'FL21',
            'SX21',
            'CL51',
            'FL51',
            'SX51',
        ];
        $this->thermal_printers = [
            // ['printer_name' => 'Barcode Printer CL', 'location' => 'ASSY', 'location_detail' => 'INCOMING CL'],
            // ['printer_name' => 'SUPERMAN', 'location' => 'ASSY', 'location_detail' => 'INCOMING FL'],
            // ['printer_name' => 'KDO FL', 'location' => 'ASSY', 'location_detail' => 'PACKING KD FL'],
            // ['printer_name' => 'KDO SX', 'location' => 'ASSY', 'location_detail' => 'PACKING KD SX'],
            // ['printer_name' => 'KDO CL', 'location' => 'ASSY', 'location_detail' => 'PACKING KD CL'],
            ['printer_name' => 'MIS', 'location' => 'OFFICE', 'location_detail' => 'MEJA MIS'],
            ['printer_name' => 'Body Process Printer', 'location' => 'BPP', 'location_detail' => 'MEJA TRANSAKSI'],
            ['printer_name' => 'Welding-Printer', 'location' => 'WLD', 'location_detail' => 'MEJA LEADER WLD'],
            ['printer_name' => 'HTS', 'location' => 'WLD', 'location_detail' => 'MEJA LEADER HTS'],
            ['printer_name' => 'Lacquering', 'location' => 'LCQ', 'location_detail' => 'MEJA LEADER'],
            ['printer_name' => 'Plating', 'location' => 'PLT', 'location_detail' => 'MEJA LEADER'],
            // ['printer_name' => 'FLO Printer 102', 'location' => 'ASSY CL', 'location_detail' => 'PACKING FG CL'],
            ['printer_name' => 'FLO Printer 101', 'location' => 'ASSY FL', 'location_detail' => 'PACKING FG FL'],
            ['printer_name' => 'KDO FL', 'location' => 'SUBASSY FL', 'location_detail' => 'PACKING KD FL'],
            ['printer_name' => 'FLO Printer 103', 'location' => 'ASSY SX', 'location_detail' => 'PACKING FG SX'],
            ['printer_name' => 'Barcode Printer Sax', 'location' => 'ASSY SX', 'location_detail' => 'INCOMING ASSY SX'],
        ];
    }

    public function indexSlipUnusual()
    {
        $title = 'Print Slip Khusus';
        $title_jp = '';
        $materials = db::connection('ympimis_2')->select("SELECT DISTINCT
	            *
            FROM
	            (
	            SELECT
		            material_parent AS material_number,
		            material_parent_description AS material_description,
		            material_parent_location AS storage_location,
		            material_parent_location2 AS location
	            FROM
		            kanban_inout_boms
                WHERE material_parent <> ''

                UNION ALL

	            SELECT
		            material_child AS material_description,
		            material_child_description AS material_description,
		            material_child_location AS storage_location,
		            material_child_location2 AS location
	            FROM
		            kanban_inout_boms
                WHERE material_child <> ''
	            ) AS materials
            ORDER BY
	            location ASC,
	            material_number ASC");

        return view('transactions.slip_unusual', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'printers' => $this->thermal_printers,
            'materials' => $materials,
            'now' => date('Y-m-d'),
        ))->with('page', 'Unusual Material Slip');
    }

    public function printSlipUnusual(Request $request)
    {
        try {
            DB::connection('ympimis_2')->beginTransaction();

            $code_generator = CodeGenerator::where('note', '=', 'slip_unusual')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $slip_number = $code_generator->prefix . $number;

            $printer_name = $request->get('printer_name');
            $list_materials = $request->get('list_materials');

            $connector = new WindowsPrintConnector($printer_name);
            $printer = new Printer($connector);

            foreach ($list_materials as $list_material) {

                $valid_to = "";
                if (str_contains($list_material['category'], 'REPAIR')) {
                    if ($list_material['category'] == 'REPAIR ROOM (PLT -> BFF)') {
                        $valid_to = date('Y-m-d H:i:s', strtotime('+60 day'));
                    } elseif ($list_material['category'] == 'REPAIR ROOM (FA -> BFF)') {
                        $valid_to = date('Y-m-d H:i:s', strtotime('+60 day'));
                    } elseif ($list_material['category'] == 'REPAIR ROOM (BPP -> BFF)') {
                        $valid_to = date('Y-m-d H:i:s', strtotime('+60 day'));
                    } else {
                        $valid_to = date('Y-m-d H:i:s', strtotime('+48 hours'));
                    }
                } elseif (str_contains($list_material['category'], 'TUKAR')) {
                    $valid_to = date('Y-m-d H:i:s', strtotime('+24 hours'));
                } else {
                    $valid_to = date('Y-m-d 23:59:59', strtotime($list_material['valid_to']));
                }

                $category = explode(' (', $list_material['category']);
                $location = explode(' -> ', explode(')', explode('(', $list_material['category'])[1])[0]);

                if ($category == 'ADJUST AFTER STOCKTAKING') {
                    if (Auth::user()->role_code != 'S-MIS' && Auth::user()->role_code != 'S-PRD') {
                        $response = array(
                            'status' => false,
                            'message' => 'SLIP KHUSUS ADJUST AFTER STOCKTAKING HANYA BISA DICETAK OLEH STAFF ADMIN PRODUKSI',
                        );
                        return Response::json($response);
                    }
                }

                db::connection('ympimis_2')->table('kanban_inout_unusuals')
                    ->insert([
                        'slip_number' => $slip_number,
                        'material_number' => $list_material['material_number'],
                        'material_description' => $list_material['material_description'],
                        'quantity' => $list_material['quantity'],
                        'category' => $category[0],
                        'issue_location' => $location[0],
                        'receive_location' => $location[1],
                        'category' => $category[0],
                        'remark' => $list_material['remark'],
                        'valid_to' => $valid_to,
                        'created_by' => Auth::user()->username,
                        'created_by_name' => Auth::user()->name,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->setTextSize(2, 2);
                $printer->setUnderline(true);
                $printer->text('--- SLIP KHUSUS ---');
                $printer->feed(3);
                $printer->qrCode($slip_number, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
                $printer->setReverseColors(true);
                $printer->setUnderline(false);
                $printer->setTextSize(1, 1);
                $printer->text($slip_number . "\n");
                $printer->feed(1);
                $printer->initialize();
                $printer->setTextSize(2, 1);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->setEmphasis(true);
                $printer->text($list_material['material_number']);
                $printer->feed(1);
                $printer->text($list_material['material_description']);
                $printer->setEmphasis(false);
                $printer->feed(2);
                $printer->text('Jumlah: ' . $list_material['quantity'] . ' PC(s)');
                $printer->feed(1);
                $printer->text('Keperluan: ' . $list_material['category']);
                $printer->feed(1);
                $printer->setTextSize(1, 1);
                $printer->text('Catatan: ' . $list_material['remark']);
                $printer->feed(2);
                $printer->text('Berlaku Mulai: ' . date('Y-m-d H:i:s'));
                $printer->feed(1);
                $printer->text('Berlaku Hingga: ' . $valid_to);
                $printer->feed(2);
                $printer->text('Dicetak Oleh:');
                $printer->feed(1);
                $printer->text(Auth::user()->name);
                $printer->feed(2);
                $printer->cut();
                $printer->close();

            }

            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            DB::connection('ympimis_2')->commit();
            $response = array(
                'status' => true,
                'message' => 'Slip khusus berhasil didaftarkan',
            );
            return Response::json($response);

        } catch (QueryException $e) {
            DB::connetion('ympimis_2')->rollback();
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

    }

    public function indexMb51()
    {
        $title = 'MB51 Transaction';
        $title_jp = '';

        return view('transactions.mb51', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'data' => [],
            'posting_date' => '',
        ))->with('page', 'MB51');

    }

    public function uploadMb51(Request $request)
    {

        $now = date('Y-m-d H:i:s');
        $upload = $request->get('upload');
        $error_count = array();
        $ok_count = array();
        $uploadRows = preg_split("/\r?\n/", $upload);

        // Check Posting Date Exist
        $posting_dates = [];
        foreach ($uploadRows as $uploadRow) {
            $uploadColumn = preg_split("/\t/", $uploadRow);
            $posting_date = str_replace('/', '-', $uploadColumn[21]);

            if (!in_array(date_format(date_create_from_format("m-d-Y", $posting_date), 'Y-m-d'), $posting_dates)) {
                array_push($posting_dates, date_format(date_create_from_format("m-d-Y", $posting_date), 'Y-m-d'));
            }
        }

        $check = DB::connection('ympimis_2')
            ->table('sap_transactions')
            ->whereIn('posting_date', $posting_dates)
            ->select('posting_date')
            ->distinct()
            ->get();

        if (count($check) > 0) {
            DB::rollback();
            $response = array(
                'status' => false,
                'posting_date' => $check,
                'row' => count($uploadRows),
                'message' => 'Posting date already exist',
            );
            return Response::json($response);
        }
        // End Check Posting Date Exist

        DB::beginTransaction();
        $row = 0;
        foreach ($uploadRows as $uploadRow) {
            $uploadColumn = preg_split("/\t/", $uploadRow);
            $row++;

            try {
                $entry_date = date_format(date_create_from_format("m-d-Y", str_replace('/', '-', $uploadColumn[20])), 'Y-m-d');
                $posting_date = date_format(date_create_from_format("m-d-Y", str_replace('/', '-', $uploadColumn[21])), 'Y-m-d');
                $doc_date = date_format(date_create_from_format("m-d-Y", str_replace('/', '-', $uploadColumn[26])), 'Y-m-d');

                $insert = DB::connection('ympimis_2')
                    ->table('sap_transactions')
                    ->insert([
                        'plnt' => $uploadColumn[0],
                        'val_type' => $uploadColumn[1],
                        'mvt' => $uploadColumn[2],
                        'material' => $uploadColumn[3],
                        'material_description' => $uploadColumn[4],
                        'sloc' => $uploadColumn[5],
                        'reference' => $uploadColumn[6],
                        'order' => $uploadColumn[7],
                        'user_name' => $uploadColumn[8],
                        'time' => $uploadColumn[9],
                        'document_header_text' => $uploadColumn[10],
                        'cost_ctr' => $uploadColumn[11],
                        'item_1' => $uploadColumn[12],
                        'mat_doc' => $uploadColumn[13],
                        'item_2' => $uploadColumn[14],
                        'reserv_no' => $uploadColumn[15],
                        'po' => $uploadColumn[16],
                        'vendor' => $uploadColumn[17],
                        'reas' => $uploadColumn[18],
                        'customer' => $uploadColumn[19],
                        'entry_date' => $entry_date,
                        'posting_date' => $posting_date,
                        'quantity' => str_replace(',', '', $uploadColumn[22]),
                        'eun' => $uploadColumn[23],
                        'amount_in_lc' => str_replace(',', '', $uploadColumn[24]),
                        'crcy' => $uploadColumn[25],
                        'doc_date' => $doc_date,
                        'created_by' => Auth::id(),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                array_push($ok_count, 'ok');
            } catch (QueryException $e) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => substr($e->getMessage(), 0, 15),
                );
                return Response::json($response);
            }

        }

        DB::commit();
        $response = array(
            'status' => true,
            'error_count' => $error_count,
            'ok_count' => $ok_count,
            'message' => 'ERROR: ' . count($error_count) . ' OK: ' . count($ok_count),
        );
        return Response::json($response);

    }

    public function fetchMb51(Request $request)
    {

        if (strlen($request->get('posting_date')) > 0) {
            $posting_date = explode(' - ', $request->get('posting_date'));
            $posting_date_from = date('Y-m-d', strtotime($posting_date[0]));
            $posting_date_to = date('Y-m-d', strtotime($posting_date[1]));
        }

        set_time_limit(0);
        ini_set('memory_limit', -1);
        ob_start();

        $data = DB::connection('ympimis_2')
            ->table('sap_transactions')
            ->where('posting_date', '>=', $posting_date_from)
            ->where('posting_date', '<=', $posting_date_to)
            ->get();

        ob_end_flush();
        ob_flush();
        flush();

        return view('transactions.mb51', array(
            'title' => 'MB51 Transaction',
            'title_jp' => '',
            'data' => $data,
            'posting_date' => $request->get('posting_date'),
        ))->with('page', 'MB51');

    }

    public function fetchResumeMb51(Request $request)
    {

        $year = $request->get('year');

        $calendar = db::table('weekly_calendars')
            ->where('week_date', 'LIKE', '%' . $year . '%')
            ->select(
                'week_date',
                db::raw('DATE_FORMAT(week_date, "%e") AS date'),
                db::raw('DATE_FORMAT(week_date, "%c") AS month'),
                db::raw('IF(DATE_FORMAT(week_date, "%w") = "0", "7", DATE_FORMAT(week_date, "%w")) AS day'),
                db::raw('DATE_FORMAT(week_date, "%a") AS day_text'),
                'remark'
            )
            ->orderBy('week_date', 'ASC')
            ->get();

        $count_day = db::table('weekly_calendars')
            ->where('week_date', 'LIKE', '%' . $year . '%')
            ->select(
                db::raw('DATE_FORMAT(week_date, "%c") AS month'),
                db::raw('count(id) AS count')
            )
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get();

        $resume = db::connection('ympimis_2')
            ->table('sap_transactions')
            ->where('posting_date', 'LIKE', '%' . $year . '%')
            ->select(
                db::raw('posting_date'),
                db::raw('count(id) AS count')
            )
            ->groupBy('posting_date')
            ->orderBy('posting_date', 'ASC')
            ->get();

        $response = array(
            'status' => true,
            'calendar' => $calendar,
            'count_day' => $count_day,
            'resume' => $resume,
        );
        return Response::json($response);

    }

    public function downloadMb51(Request $request)
    {

        if (strlen($request->get('txt_posting_date')) > 0) {
            $posting_date = explode(' - ', $request->get('txt_posting_date'));
            $posting_date_from = date('Y-m-d', strtotime($posting_date[0]));
            $posting_date_to = date('Y-m-d', strtotime($posting_date[1]));
        }

        $data = DB::connection('ympimis_2')
            ->table('sap_transactions')
            ->where('posting_date', '>=', $posting_date_from)
            ->where('posting_date', '<=', $posting_date_to)
            ->get();

        // prepare content
        $content = "";
        $content .= "Plnt\t";
        $content .= "Val. Type.\t";
        $content .= "MvT\t";
        $content .= "Material\t";
        $content .= "Material Description\t";
        $content .= "SLoc\t";
        $content .= "Reference\t";
        $content .= "Order\t";
        $content .= "User Name\t";
        $content .= "Time\t";
        $content .= "Doc. Header Text\t";
        $content .= "Cost Ctr\t";
        $content .= "Item\t";
        $content .= "Mat. Doc.\t";
        $content .= "Item\t";
        $content .= "Reserv.No.\t";
        $content .= "PO\t";
        $content .= "Vendor\t";
        $content .= "Reas.\t";
        $content .= "Customer\t";
        $content .= "Entry Date\t";
        $content .= "Pstng Date\t";
        $content .= "Quantity\t";
        $content .= "EUn\t";
        $content .= "Amount in LC\t";
        $content .= "Crcy\t";
        $content .= "Doc. Date\t";
        $content .= "\n";
        foreach ($data as $row) {
            $content .= $row->plnt . "\t";
            $content .= $row->val_type . "\t";
            $content .= $row->mvt . "\t";
            $content .= $row->material . "\t";
            $content .= $row->material_description . "\t";
            $content .= $row->sloc . "\t";
            $content .= $row->reference . "\t";
            $content .= $row->order . "\t";
            $content .= $row->user_name . "\t";
            $content .= $row->time . "\t";
            $content .= $row->document_header_text . "\t";
            $content .= $row->cost_ctr . "\t";
            $content .= $row->item_1 . "\t";
            $content .= $row->mat_doc . "\t";
            $content .= $row->item_2 . "\t";
            $content .= $row->reserv_no . "\t";
            $content .= $row->po . "\t";
            $content .= $row->vendor . "\t";
            $content .= $row->reas . "\t";
            $content .= $row->customer . "\t";
            $content .= date('m-d-Y', strtotime($row->entry_date)) . "\t";
            $content .= date('m-d-Y', strtotime($row->posting_date)) . "\t";
            $content .= $row->quantity . "\t";
            $content .= $row->eun . "\t";
            $content .= $row->amount_in_lc . "\t";
            $content .= $row->crcy . "\t";
            $content .= date('m-d-Y', strtotime($row->doc_date)) . "\t";
            $content .= "\n";
        }

        // file name that will be used in the download
        $file_name = "MB51 " . $request->get('txt_posting_date') . ".txt";

        // use headers in order to generate the download
        $headers = [
            'Content-type' => 'text/plain',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $file_name),
            'Content-Length' => sizeof($content),
        ];

        // make a response, with the content, a 200 response code and the headers
        return Response::make($content, 200, $headers);

    }

    public function production_result($result_date, $reference_number, $slip_number, $serial_number, $material_number, $material_description, $issue_location, $mstation, $quantity, $synced, $remark, $created_by, $created_by_name)
    {
        db::connection('ympimis_2')->table('production_results')->insert([
            'result_date' => $result_date,
            'reference_number' => $reference_number,
            'slip_number' => $slip_number,
            'serial_number' => $serial_number,
            'material_number' => $material_number,
            'material_description' => $material_description,
            'issue_location' => $issue_location,
            'mstation' => $mstation,
            'quantity' => $quantity,
            'synced' => $synced,
            'remark' => $remark,
            'created_by' => $created_by,
            'created_by_name' => $created_by_name,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function goods_movements($result_date, $reference_number, $slip_number, $serial_number, $material_number, $material_description, $issue_location, $receive_location, $quantity, $synced, $remark, $created_by, $created_by_name)
    {
        db::connection('ympimis_2')->table('production_results')->insert([
            'result_date' => $result_date,
            'reference_number' => $reference_number,
            'slip_number' => $slip_number,
            'serial_number' => $serial_number,
            'material_number' => $material_number,
            'material_description' => $material_description,
            'issue_location' => $issue_location,
            'receive_location' => $receive_location,
            'quantity' => $quantity,
            'synced' => $synced,
            'remark' => $remark,
            'created_by' => $created_by,
            'created_by_name' => $created_by_name,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function indexRepairRoom(Request $request)
    {
        $title = "Repair Room Log";
        $title_jp = "";

        $date = date('Y-m-d', strtotime('-3 Days'));

        $materials = array();

        if ($request->get('location') == 'Welding') {
            $materials = db::select("SELECT
                m.material_number AS material_number,
                m.description AS material_description,
                m.location AS storage_location
                FROM
                kitto.materials AS m
                WHERE
                m.location LIKE '%21'
                ORDER BY
                m.material_number ASC");

            $logs = db::connection('ympimis_2')->table('repair_room_logs')
                ->where('location', '=', 'Welding')
                ->orderBy('created_at', 'DESC')->get();

            $employees = db::select("SELECT
                e.employee_id,
                e.name,
                e.tag
                FROM
                employees AS e
                LEFT JOIN employee_syncs AS es ON e.employee_id = es.employee_id
                WHERE
                es.employee_id IN ( 'PI9909004', 'PI9812008', 'PI1103002', 'PI0904001', 'PI1011006', 'PI9912003', 'PI0006023', 'PI9904012', 'PI9806005', 'PI9904004', 'PI0503004')
                OR es.department = 'Management Information System Department'");
        }
        if ($request->get('location') == 'Plating') {
            $materials = db::select("SELECT
                m.material_number AS material_number,
                m.material_description AS material_description,
                m.issue_storage_location AS storage_location
                FROM
                materials AS m
                WHERE
                m.issue_storage_location LIKE '%51'
                AND (m.surface LIKE '%PLT%' or  m.surface LIKE '%LCQ%')
                ORDER BY
                m.material_number ASC");

            $logs = db::connection('ympimis_2')->table('repair_room_logs')
                ->where('location', '=', 'Plating')
                ->orderBy('created_at', 'DESC')->get();

            $employees = db::select("SELECT
                e.employee_id,
                e.name,
                e.tag
                FROM
                employees AS e
                LEFT JOIN employee_syncs AS es ON e.employee_id = es.employee_id
                WHERE
                es.employee_id IN ('PI0005017','PI1103002','PI0904001', 'PI0503004')
                OR es.department = 'Management Information System Department'");
        }

        return view('transactions.repair_room.index', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'location' => $request->get('location'),
            'employees' => $employees,
            'materials' => $materials,
            'logs' => $logs,
        ))->with('page', 'Repair Room Log');
    }

    public function indexRepairRoomMonitoring()
    {
        $title = "Repair Room Monitoring";
        $title_jp = "";

        return view('transactions.repair_room.monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Repair Room Monitoring');
    }

    public function fetchRepairRoomLog(Request $request)
    {
        $date = explode(' - ', $request->get('filterDate'));
        $date_from = date('Y-m-d', strtotime($date[0]));
        $date_to = date('Y-m-d', strtotime($date[1]));

        $logs = db::connection('ympimis_2')->table('repair_room_logs')
            ->where('created_at', '>=', $date_from . ' 00:00:00')
            ->where('created_at', '<=', $date_to . ' 23:59:59')
            ->get();

        $materials = array();

        foreach ($logs as $row) {
            if (!in_array($row->material_number, $materials)) {
                array_push($materials, $row->material_number);
            }
        }

        $mpdl = db::table('material_plant_data_lists')->whereIn('material_number', $materials)->get();

        $records = array();

        for ($i = 0; $i < count($logs); $i++) {
            $id = $logs[$i]->id;
            $material_number = $logs[$i]->material_number;
            $material_description = $logs[$i]->material_description;
            $quantity = $logs[$i]->quantity;
            $balance = $logs[$i]->balance;
            $location = $logs[$i]->location;
            $status = $logs[$i]->status;
            $remark = $logs[$i]->remark;
            $created_by = $logs[$i]->created_by;
            $created_by_name = $logs[$i]->created_by_name;
            $deleted_at = $logs[$i]->deleted_at;
            $created_at = $logs[$i]->created_at;
            $updated_at = $logs[$i]->updated_at;
            $price = 0;
            $amount = 0;

            for ($j = 0; $j < count($mpdl); $j++) {
                if ($logs[$i]->material_number == $mpdl[$j]->material_number) {
                    $price = round($mpdl[$j]->standard_price / 1000, 1);
                    $amount = round($logs[$i]->quantity * ($mpdl[$j]->standard_price / 1000), 1);
                    break;
                }
            }

            array_push($records, [
                'id' => $id,
                'material_number' => $material_number,
                'material_description' => $material_description,
                'quantity' => $quantity,
                'balance' => $balance,
                'location' => $location,
                'status' => $status,
                'remark' => $remark,
                'created_by' => $created_by,
                'created_by_name' => $created_by_name,
                'deleted_at' => $deleted_at,
                'created_at' => $created_at,
                'updated_at' => $updated_at,
                'price' => $price,
                'amount' => $amount,
            ]);
        }

        $response = array(
            'status' => true,
            'logs' => $records,
        );
        return Response::json($response);
    }

    public function fetchRepairRoomMonitoring(Request $request)
    {
        try {

            // $date_from = '2022-09-21';
            $date_from = date('Y-m-01', strtotime('-1 Month'));
            $date_to = date('Y-m-d');

            $weekly_calendars = db::table('weekly_calendars')->where('week_date', '>=', '2022-10-01')
                ->where('week_date', '<', $date_from)
                ->get();

            $date_before = ['2022-09-21'];

            foreach ($weekly_calendars as $row) {
                if (!in_array(date('Y-m-t', strtotime($row->week_date)), $date_before)) {
                    array_push($date_before, date('Y-m-t', strtotime($row->week_date)));
                }
            }

            $where_date = "";

            for ($i = 0; $i < count($date_before); $i++) {
                $where_date = $where_date . "'" . $date_before[$i] . "'";
                if ($i != (count($date_before) - 1)) {
                    $where_date = $where_date . ',';
                }
            }
            $where_date = " stock_date IN (" . $where_date . ") ";

            $inventories = db::connection('ympimis_2')->table('repair_room_inventories')
                ->orderBy('material_number', 'ASC')
                ->get();

            $records = db::connection('ympimis_2')->select("
                SELECT
                	*
                FROM
                	repair_room_records
                WHERE
                	" . $where_date . "
                	OR stock_date >= '" . $date_from . "'
                ORDER BY
                	stock_date ASC,
                	material_number ASC");

            // $logs = db::connection('ympimis_2')->select("SELECT
            //     *,
            //     date( repair_room_logs.created_at ) AS category
            //     FROM
            //     repair_room_logs
            //     WHERE
            //     date( repair_room_logs.created_at ) >= '" . date('Y-m-d', strtotime('-1 months')) . "'
            //     AND date( repair_room_logs.created_at ) <= '" . $date_to . "'
            //     ORDER BY
            //     repair_room_logs.created_at ASC");

            $materials = array();

            foreach ($inventories as $row) {
                if (!in_array($row->material_number, $materials)) {
                    array_push($materials, $row->material_number);
                }
            }

            $mpdl = db::table('material_plant_data_lists')->whereIn('material_number', $materials)->get();

            $resume_inventories = array();

            for ($i = 0; $i < count($inventories); $i++) {
                $material_number = $inventories[$i]->material_number;
                $material_description = $inventories[$i]->material_description;
                $quantity = $inventories[$i]->quantity;
                $location = $inventories[$i]->location;
                $updated_at = $inventories[$i]->updated_at;
                $uom = "";
                $price = "";
                $amount = "";
                $hpl = "";

                for ($j = 0; $j < count($mpdl); $j++) {
                    if ($inventories[$i]->material_number == $mpdl[$j]->material_number) {
                        $uom = $mpdl[$j]->bun;
                        $price = round($mpdl[$j]->standard_price / 1000, 1);
                        $amount = round($inventories[$i]->quantity * ($mpdl[$j]->standard_price / 1000), 1);
                        if (str_contains($mpdl[$j]->storage_location, 'CL')) {
                            $hpl = "CL";
                        }
                        if (str_contains($mpdl[$j]->storage_location, 'FL')) {
                            $hpl = "FL";
                        }
                        if (str_contains($mpdl[$j]->storage_location, 'SX')) {
                            $hpl = "SX";
                        }
                        if (str_contains($mpdl[$j]->storage_location, 'VN')) {
                            $hpl = "SX";
                        }
                        break;
                    }
                }

                if ($quantity > 0) {
                    array_push($resume_inventories, [
                        'material_number' => $material_number,
                        'material_description' => $material_description,
                        'quantity' => $quantity,
                        'location' => $location,
                        'uom' => $uom,
                        'price' => $price,
                        'amount' => $amount,
                        'hpl' => $hpl,
                        'updated_at' => $updated_at,
                    ]);
                }
            }

            $resume_records = array();
            $resume_record_mons = array();

            for ($i = 0; $i < count($records); $i++) {
                $stock_date = $records[$i]->stock_date;
                $stock_mon = date('Y-m', strtotime($records[$i]->stock_date));
                $material_number = $records[$i]->material_number;
                $material_description = $records[$i]->material_description;
                $quantity = $records[$i]->quantity;
                $location = $records[$i]->location;
                $updated_at = $records[$i]->updated_at;
                $uom = "";
                $price = 0;
                $amount = 0;
                $hpl = "";

                for ($j = 0; $j < count($mpdl); $j++) {
                    if ($records[$i]->material_number == $mpdl[$j]->material_number) {
                        $uom = $mpdl[$j]->bun;
                        $price = round($mpdl[$j]->standard_price / 1000, 1);
                        $amount = round($records[$i]->quantity * ($mpdl[$j]->standard_price / 1000), 1);
                        if (str_contains($mpdl[$j]->storage_location, 'CL')) {
                            $hpl = "CL";
                        }
                        if (str_contains($mpdl[$j]->storage_location, 'FL')) {
                            $hpl = "FL";
                        }
                        if (str_contains($mpdl[$j]->storage_location, 'SX')) {
                            $hpl = "SX";
                        }
                        if (str_contains($mpdl[$j]->storage_location, 'VN')) {
                            $hpl = "SX";
                        }
                        break;
                    }
                }

                if ($quantity > 0) {
                    array_push($resume_records, [
                        'stock_date' => $stock_date,
                        'stock_mon' => $stock_mon,
                        'material_number' => $material_number,
                        'material_description' => $material_description,
                        'quantity' => $quantity,
                        'location' => $location,
                        'uom' => $uom,
                        'price' => $price,
                        'amount' => $amount,
                        'hpl' => $hpl,
                        'updated_at' => $updated_at,
                    ]);

                    if ($stock_date == '2022-09-21' || $stock_date == date('Y-m-t', strtotime($stock_date)) || $stock_date == date('Y-m-d', strtotime('-1 Day'))) {
                        array_push($resume_record_mons, [
                            'stock_date' => $stock_date,
                            'stock_mon' => $stock_mon,
                            'material_number' => $material_number,
                            'material_description' => $material_description,
                            'quantity' => $quantity,
                            'location' => $location,
                            'uom' => $uom,
                            'price' => $price,
                            'amount' => $amount,
                            'hpl' => $hpl,
                            'updated_at' => $updated_at,
                        ]);
                    }
                }
            }

            $resume_logs = array();

            // for ($i = 0; $i < count($logs); $i++) {
            //     $category = $logs[$i]->category;
            //     $material_number = $logs[$i]->material_number;
            //     $material_description = $logs[$i]->material_description;
            //     $quantity = $logs[$i]->quantity;
            //     $balance = $logs[$i]->balance;
            //     $location = $logs[$i]->location;
            //     $status = $logs[$i]->status;
            //     $remark = $logs[$i]->remark;
            //     $created_by = $logs[$i]->created_by;
            //     $created_by_name = $logs[$i]->created_by_name;
            //     $updated_at = $logs[$i]->updated_at;
            //     $uom = "";
            //     $price = "";
            //     $amount = "";
            //     $hpl = "";

            //     for ($j = 0; $j < count($mpdl); $j++) {
            //         if ($logs[$i]->material_number == $mpdl[$j]->material_number) {
            //             $uom = $mpdl[$j]->bun;
            //             $price = round($mpdl[$j]->standard_price / 1000, 1);
            //             $amount = round($logs[$i]->quantity * ($mpdl[$j]->standard_price / 1000), 1);
            //             if (str_contains($mpdl[$j]->storage_location, 'CL')) {
            //                 $hpl = "CL";
            //             }
            //             if (str_contains($mpdl[$j]->storage_location, 'FL')) {
            //                 $hpl = "FL";
            //             }
            //             if (str_contains($mpdl[$j]->storage_location, 'SX')) {
            //                 $hpl = "SX";
            //             }
            //             if (str_contains($mpdl[$j]->storage_location, 'VN')) {
            //                 $hpl = "SX";
            //             }
            //             break;
            //         }
            //     }

            //     array_push($resume_logs, [
            //         'category' => $category,
            //         'material_number' => $material_number,
            //         'material_description' => $material_description,
            //         'quantity' => $quantity,
            //         'balance' => $balance,
            //         'location' => $location,
            //         'status' => $status,
            //         'remark' => $remark,
            //         'created_by' => $created_by,
            //         'created_by_name' => $created_by_name,
            //         'updated_at' => $updated_at,
            //         'uom' => $uom,
            //         'price' => $price,
            //         'amount' => $amount,
            //         'hpl' => $hpl,
            //     ]);
            // }

            $resume_logs = DB::connection('ympimis_2')->table('repair_room_log_records')->orderBy('amount', 'desc')->get();

            $response = array(
                'status' => true,
                'logs' => $resume_logs,
                'inventories' => $resume_inventories,
                'records' => $resume_records,
                'resume_logs' => $resume_logs,
                'record_mons' => $resume_record_mons,
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

    public function inputRepairRoom(Request $request)
    {
        try {
            $repair_room_inventory = db::connection('ympimis_2')->table('repair_room_inventories')
                ->where('location', '=', $request->get('location'))
                ->where('material_number', '=', $request->get('material_number'))
                ->first();

            if ($repair_room_inventory) {
                db::connection('ympimis_2')->table('repair_room_inventories')
                    ->where('location', '=', $request->get('location'))
                    ->where('material_number', '=', $request->get('material_number'))
                    ->update([
                        'material_number' => $request->get('material_number'),
                        'material_description' => $request->get('material_description'),
                        'quantity' => $repair_room_inventory->quantity + $request->get('quantity'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            } else {
                db::connection('ympimis_2')->table('repair_room_inventories')->insert([
                    'material_number' => $request->get('material_number'),
                    'material_description' => $request->get('material_description'),
                    'quantity' => $request->get('quantity'),
                    'location' => $request->get('location'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $repair_room_inventory = db::connection('ympimis_2')->table('repair_room_inventories')
                ->where('location', '=', $request->get('location'))
                ->where('material_number', '=', $request->get('material_number'))
                ->first();

            db::connection('ympimis_2')->table('repair_room_logs')->insert([
                'material_number' => $request->get('material_number'),
                'material_description' => $request->get('material_description'),
                'quantity' => $request->get('quantity'),
                'location' => $request->get('location'),
                'balance' => $repair_room_inventory->quantity,
                'created_by' => $request->get('employee_id'),
                'created_by_name' => $request->get('employee_name'),
                'remark' => $request->get('remark'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $response = array(
                'status' => true,
                'material_number' => $request->get('material_number'),
                'material_description' => $request->get('material_description'),
                'quantity' => $request->get('quantity'),
                'remark' => $request->get('remark'),
                'created_at' => date('Y-m-d H:i:s'),
                'message' => 'Material berhasil ' . $request->get('text') . ' ruangan repair',
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

    public function indexCompletionOnly()
    {
        $title = "Completion Only";
        $title_jp = "";
        $employees = DB::select("SELECT
            es.employee_id,
            es.name,
            es.department,
            e.tag
            FROM
            employee_syncs AS es
            LEFT JOIN employees AS e ON e.employee_id = es.employee_id
            WHERE
            (
                es.end_date IS NULL
                OR es.end_date >= date(
                    now()))
            AND es.department IS NOT NULL
            AND es.grade_code <> 'J0-'");

        return view('transactions.completion_only', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employees' => $employees,
        ))->with('page', 'Completion Only');
    }

    public function indexCompletion()
    {
        $title = "Completion";
        $title_jp = "";

        $employees = db::select("
            SELECT
                es.employee_id,
                es.`name`,
                es.department,
                e.tag
            FROM
                employee_syncs AS es
                LEFT JOIN employees AS e ON e.employee_id = es.employee_id
            WHERE
                (
                    es.end_date IS NULL
                    OR es.end_date >= date(
                    now()))
                AND es.department IS NOT NULL
                AND es.grade_code <> 'J0-'
                AND es.grade_code <> 'OS'");

        return view('transactions.completion', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employees' => $employees,
        ))->with('page', 'Completion');
    }

    public function inputCompletion(Request $request)
    {
        try {
            DB::connection('ympimis_2')->beginTransaction();

            $completion = db::connection('ympimis_2')->table('kanban_completions')
                ->where('tag', '=', $request->get('tag'))
                ->first();

            if (!$completion) {
                $response = array(
                    'status' => false,
                    'message' => "Kartu kanban belum terdaftar.",
                );
                return Response::json($response);
            }

            if ($completion->active == 0) {
                $response = array(
                    'status' => false,
                    'message' => "Kartu kanban tidak aktif.",
                );
                return Response::json($response);
            }

            $inventory = db::connection('ympimis_2')->table('kanban_inventories')
                ->where('tag', '=', $request->get('tag'))
                ->first();

            if ($inventory) {
                $date = new \DateTime($inventory->updated_at);
                $date2 = new \DateTime(date('Y-m-d H:i:s'));
                $timediff = $date2->getTimestamp() - $date->getTimestamp();

                if ($timediff <= $completion->lead_time * 60) {
                    $response = array(
                        'status' => false,
                        'message' => "Kartu kanban masih dalam waktu leadtime.",
                        'tes' => $timediff,
                    );
                    return Response::json($response);
                }
                if ($inventory->quantity > 0) {
                    $response = array(
                        'status' => false,
                        'message' => "Jumlah pada inventory masih tersedia " . $inventory->quantity . ".",
                    );
                    return Response::json($response);
                }
            }

            if (!$inventory) {
                $insert_inventory = db::connection('ympimis_2')->table('kanban_inventories')
                    ->insert([
                        'tag' => strtoupper($completion->tag),
                        'material_number' => strtoupper($completion->material_number),
                        'material_description' => strtoupper($completion->material_description),
                        'location' => strtoupper($completion->location),
                        'quantity' => $completion->quantity,
                        'last_action' => 'production_result',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            if ($inventory) {
                $update_inventory = db::connection('ympimis_2')->table('kanban_inventories')
                    ->where('tag', '=', $completion->tag)
                    ->update([
                        'material_number' => strtoupper($completion->material_number),
                        'material_description' => strtoupper($completion->material_description),
                        'location' => strtoupper($completion->location),
                        'quantity' => $completion->quantity,
                        'last_action' => 'production_result',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            $data['barcode_number'] = $completion->tag;
            $data['description'] = $completion->material_description;
            $data['lot'] = $completion->quantity;

            $category = 'production_result';
            $function = 'inputCompletion';
            $action = 'production_result';
            $result_date = date('Y-m-d H:i:s');
            $slip_number = strtoupper($completion->tag);
            $serial_number = null;
            $material_number = strtoupper($completion->material_number);
            $material_description = strtoupper($completion->material_description);
            $issue_location = strtoupper($completion->location);
            $mstation = strtoupper($completion->mstation);
            $quantity = $completion->quantity;
            $remark = 'MIRAI';
            $created_by = 'System';
            $created_by_name = 'System';
            if (Auth::check()) {
                $created_by = strtoupper(Auth::user()->username);
                $created_by_name = strtoupper(Auth::user()->name);
            }
            $synced = null;
            $synced_by = null;

            app(YMESController::class)->production_result(
                $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);

            DB::connection('ympimis_2')->commit();
        } catch (\PDOException$e) {
            DB::connection('ympimis_2')->rollBack();
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        // OTHER
        try {

        } catch (\PDOException$e) {

        }

        $response = array(
            'status' => true,
            'message' => 'Kartu kanban berhasil dicompletion.',
            'data' => json_encode($data),
        );
        return Response::json($response);
    }

    public function inputTransfer(Request $request)
    {

    }

    public function inputCompletionOnly(Request $request)
    {

        $employee_id = $request->get('employee_id');
        $employee_name = $request->get('employee_name');

        $completion = db::connection('mysql2')->table('completions')
            ->leftJoin('materials', 'materials.id', '=', 'completions.material_id')
            ->where('barcode_number', '=', $request->get('barcode_number'))
            ->select(
                'materials.id',
                'materials.material_number',
                'materials.description',
                'materials.location',
                'materials.mstation',
                'completions.lot_completion'
            )
            ->first();

        $material_id = $completion->id;
        $material_number = $completion->material_number;
        $material_description = $completion->description;
        $issue_location = $completion->location;
        $mstation = $completion->mstation;

        if (!$completion) {
            $response = array(
                'status' => false,
                'message' => 'Kanban Tidak Terdaftar',
            );
            return Response::json($response);
        }

        if (in_array($completion->location, ['CL51', 'FL51', 'SX51', 'VN51'])) {
            $bom = db::table('bom_transactions')->where('material_parent', '=', $completion->material_number)->first();

            if ($bom) {
                $material = db::connection('mysql2')
                    ->table('materials')
                    ->where('material_number', '=', $bom->material_child)
                    ->first();

                if (!$material) {
                    $mpdl = MaterialPlantDataList::where('material_number', $bom->material_child)->first();

                    if ($mpdl) {
                        $material = db::connection('mysql2')
                            ->table('materials')
                            ->insert([
                                "material_number" => $mpdl->material_number,
                                "description" => $mpdl->material_description,
                                "location" => $mpdl->storage_location,
                                "mstation" => 'W' . $mpdl->mrpc . 'S10',
                                "lead_time" => 90,
                                "user_id" => 1,
                                'created_at' => date("Y-m-d H:i:s"),
                                'updated_at' => date("Y-m-d H:i:s"),
                            ]);

                        $material = db::connection('mysql2')
                            ->table('materials')
                            ->where('material_number', '=', $bom->material_child)
                            ->first();

                        $material_id = $completion->id;
                        $material_number = $material->material_number;
                        $material_description = $material->description;
                        $issue_location = $material->location;
                        $mstation = $material->mstation;

                    } else {
                        $response = array(
                            'status' => false,
                            'message' => 'Material Tidak Ditemukan',
                        );
                        return Response::json($response);
                    }
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'BOM Material Tidak Ditemukan',
                );
                return Response::json($response);
            }
        }

        $update = db::connection('ympimis_2')->table('completion_only_updates')
            ->where('barcode_number', '=', $request->get('barcode_number'))
            ->select(db::raw('timestampdiff(second, updated_at, "' . date('Y-m-d H:i:s') . '") as leadtime'))
            ->first();

        if (!$update) {
            $update = db::connection('ympimis_2')->table('completion_only_updates')
                ->insert([
                    'barcode_number' => $request->get('barcode_number'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        } else {
            if ($update->leadtime <= (60 * 10)) {
                $response = array(
                    'status' => false,
                    'message' => 'Kanban masih dalam leadtime',
                );
                return Response::json($response);
            }
        }

        if (in_array($completion->location, ['CL51', 'FL51', 'SX51', 'VN51'])) {
            // Update Inventories
            $check_material = Material::where('material_number', $completion->material_number)->first();
            if ($check_material) {
                if (!is_null($check_material->surface)) {
                    if (str_contains($check_material->surface, 'LCQ') || str_contains($check_material->surface, 'W')) {
                        $inv = MiddleInventory::updateOrCreate(
                            [
                                'tag' => $request->get('barcode_number'),
                            ],
                            [
                                'material_number' => $completion->material_number,
                                'location' => 'lcq',
                                'quantity' => $completion->lot_completion,
                                'updated_at' => Carbon::now(),
                            ]
                        );
                        $inv->save();

                    } else if (str_contains($check_material->surface, 'PLT')) {
                        $inv = MiddleInventory::updateOrCreate(
                            [
                                'tag' => $request->get('barcode_number'),
                            ],
                            [
                                'material_number' => $completion->material_number,
                                'location' => 'plt',
                                'quantity' => $completion->lot_completion,
                                'updated_at' => Carbon::now(),
                            ]
                        );
                        $inv->save();

                    }
                }
            }
        }

        $history = db::connection('mysql2')
            ->table('histories')
            ->insert([
                "category" => "completion",
                "completion_barcode_number" => $request->get('barcode_number'),
                "completion_description" => $material->description,
                "completion_location" => $material->location,
                "completion_issue_plant" => "8190",
                "completion_material_id" => $material->id,
                "completion_reference_number" => "",
                "lot" => $completion->lot_completion,
                "reference_file" => $employee_id,
                "synced" => 0,
                'user_id' => "1",
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);

        $update = db::connection('ympimis_2')->table('completion_only_updates')
            ->where('barcode_number', '=', $request->get('barcode_number'))
            ->update([
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $data['barcode_number'] = $request->get('barcode_number');
        $data['description'] = $material->description;
        $data['lot'] = $completion->lot_completion;

        // YMES COMPLETION NEW
        $category = 'production_result';
        $function = 'inputCompletionOnly';
        $action = 'production_result';
        $result_date = date('Y-m-d H:i:s');
        $slip_number = $request->get('barcode_number');
        $serial_number = null;
        $material_number = $material->material_number;
        $material_description = $material->description;
        $issue_location = $material->location;
        $mstation = $material->mstation;
        $quantity = $completion->lot_completion;
        $remark = 'MIRAI';
        $created_by = $employee_id;
        $created_by_name = $employee_name;
        $synced = null;
        $synced_by = null;

        app(YMESController::class)->production_result(
            $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
        // YMES END

        $response = array(
            'status' => true,
            'message' => "Completion berhasil dilakukan",
            'data' => json_encode($data),
        );
        return $response;
    }

    public function indexUploadSapData()
    {
        $title = "Upload SAP Data";
        $title_jp = "";

        $cost_center_names = StorageLocation::whereNotNull('cost_center_name')
            ->select('cost_center_name')
            ->distinct()
            ->orderBy('cost_center_name', 'asc')
            ->get();

        return view('general.sap.upload_data', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'cost_center_names' => $cost_center_names,
        ))->with('page', 'SAP Data');
    }

    public function indexReturnLogs()
    {

        $storage_locations = StorageLocation::select('location', 'storage_location')->distinct()
            ->orderBy('location', 'asc')
            ->get();

        $materials = db::table('return_materials')
            ->whereNull('deleted_at')
            ->select('material_number', 'material_description as description', 'issue_location', 'receive_location')
            ->orderBy('issue_location', 'ASC')
            ->orderBy('material_number', 'ASC')
            ->get();

        return view('return.return_logs', array(
            'title' => 'Return Logs',
            'title_jp' => 'リターンログ',
            'storage_locations' => $storage_locations,
            'materials' => $materials,
        ))->with('page', 'Return Logs')->with('head', 'Material Return');
    }

    public function indexRepairLogs()
    {

        $materials = db::connection('ympimis_2')->table('repair_materials')
            ->whereNull('deleted_at')
            ->select('material_number', 'material_description as description', 'issue_location', 'receive_location')
            ->orderBy('issue_location', 'ASC')
            ->orderBy('material_number', 'ASC')
            ->get();

        return view('return.repair_logs', array(
            'title' => 'Repair Logs',
            'title_jp' => '',
            'storage_locations' => $this->repair_location,
            'materials' => $materials,
        ))->with('page', 'Repair Logs')->with('head', 'Material Repair');
    }

    public function cancelRepair(Request $request)
    {

        try {
            $repair = db::connection('ympimis_2')->table('repair_logs')
                ->where('id', '=', $request->get('id'))
                ->first();

            $material = db::connection('mysql2')->table('materials')
                ->where('material_number', '=', $repair->material_number)
                ->first();

            $repair_location = ['FA0R', 'LA0R', 'SA0R', 'VA0R', 'FA1R', 'LA1R', 'SA1R'];

            if (in_array($repair->issue_location, $repair_location)) {
                //cancel after repair
                $transfer_repair = db::connection('mysql2')->table('histories')->insert([
                    "category" => "transfer_after_repair",
                    "transfer_barcode_number" => "",
                    "transfer_document_number" => "8190",
                    "transfer_material_id" => $material->id,
                    "transfer_issue_plant" => "8190",
                    "transfer_issue_location" => $repair->receive_location,
                    "transfer_receive_plant" => "8190",
                    "transfer_receive_location" => $repair->issue_location,
                    "transfer_cost_center" => "",
                    "transfer_gl_account" => "",
                    "transfer_transaction_code" => "MB1B",
                    "transfer_movement_type" => "9I3",
                    "transfer_reason_code" => "",
                    "lot" => $repair->quantity,
                    "synced" => 0,
                    'user_id' => "1",
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                // YMES TRANSFER NEW
                $category = 'goods_movement_repair';
                $function = 'cancelRepair';
                $action = 'goods_movement';
                $result_date = date('Y-m-d H:i:s');
                $slip_number = $repair->repair_id;
                $serial_number = null;
                $material_number = $repair->material_number;
                $material_description = $repair->material_description;
                $issue_location = $repair->receive_location;
                $receive_location = $repair->issue_location;
                $quantity = $repair->quantity;
                $remark = 'MIRAI';
                $created_by = Auth::user()->username;
                $created_by_name = Auth::user()->name;
                $synced = null;
                $synced_by = null;

                app(YMESController::class)->goods_movement(
                    $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $receive_location, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                // YMES END

                if ($repair->receive_location == $material->location) {
                    $repair_completion = db::connection('mysql2')->table('histories')->insert([
                        "category" => "completion_after_repair",
                        "completion_barcode_number" => "",
                        "completion_description" => "",
                        "completion_location" => $repair->receive_location,
                        "completion_issue_plant" => "8190",
                        "completion_material_id" => $material->id,
                        "completion_reference_number" => "",
                        "lot" => $repair->quantity,
                        "synced" => 0,
                        'user_id' => "1",
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    // YMES COMPLETION NEW
                    $category = 'production_result_repair';
                    $function = 'cancelRepair';
                    $action = 'production_result';
                    $result_date = date('Y-m-d H:i:s');
                    $slip_number = $repair->repair_id;
                    $serial_number = null;
                    $material_number = $repair->material_number;
                    $material_description = $repair->material_description;
                    $issue_location = $repair->receive_location;
                    $mstation = $material->mstation;
                    $quantity = $repair->quantity;
                    $remark = 'MIRAI';
                    $created_by = Auth::user()->username;
                    $created_by_name = Auth::user()->name;
                    $synced = null;
                    $synced_by = null;

                    app(YMESController::class)->production_result(
                        $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                    // YMES END
                }

                $repair_log = db::connection('ympimis_2')->table('repair_logs')->insert([
                    'repair_id' => $repair->repair_id,
                    'material_number' => $repair->material_number,
                    'material_description' => $repair->material_description,
                    'issue_location' => $repair->issue_location,
                    'receive_location' => $repair->receive_location,
                    'quantity' => $repair->quantity,
                    'repaired_by' => $repair->repaired_by,
                    'created_by' => Auth::id(),
                    'slip_created' => $repair->slip_created,
                    'remark' => 'canceled',
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                $response = array(
                    'status' => true,
                    'message' => 'Slip after repair berhasil dicancel',
                );
                return Response::json($response);
            } else {
                //cancel repair

                if ($repair->issue_location == $material->location) {
                    $repair_completion = db::connection('mysql2')->table('histories')->insert([
                        "category" => "completion_repair",
                        "completion_barcode_number" => "",
                        "completion_description" => "",
                        "completion_location" => $repair->issue_location,
                        "completion_issue_plant" => "8190",
                        "completion_material_id" => $material->id,
                        "completion_reference_number" => "",
                        "lot" => $repair->quantity * -1,
                        "synced" => 0,
                        'user_id' => "1",
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    // YMES CANCEL COMPLETION NEW
                    $category = 'production_result_repair';
                    $function = 'cancelRepair';
                    $action = 'production_result';
                    $result_date = date('Y-m-d H:i:s');
                    $slip_number = $repair->repair_id;
                    $serial_number = null;
                    $material_number = $repair->material_number;
                    $material_description = $repair->material_description;
                    $issue_location = $repair->issue_location;
                    $mstation = $material->mstation;
                    $quantity = $repair->quantity * -1;
                    $remark = 'MIRAI';
                    $created_by = Auth::user()->username;
                    $created_by_name = Auth::user()->name;
                    $synced = null;
                    $synced_by = null;

                    app(YMESController::class)->production_result(
                        $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                    // YMES END
                }
                $transfer_repair = db::connection('mysql2')->table('histories')->insert([
                    "category" => "transfer_repair",
                    "transfer_barcode_number" => "",
                    "transfer_document_number" => "8190",
                    "transfer_material_id" => $material->id,
                    "transfer_issue_plant" => "8190",
                    "transfer_issue_location" => $repair->issue_location,
                    "transfer_receive_plant" => "8190",
                    "transfer_receive_location" => $repair->receive_location,
                    "transfer_cost_center" => "",
                    "transfer_gl_account" => "",
                    "transfer_transaction_code" => "MB1B",
                    "transfer_movement_type" => "9I4",
                    "transfer_reason_code" => "",
                    "lot" => $repair->quantity,
                    "synced" => 0,
                    'user_id' => "1",
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                // YMES CANCEL TRANSFER NEW
                $category = 'goods_movement_repair';
                $function = 'cancelRepair';
                $action = 'goods_movement';
                $result_date = date('Y-m-d H:i:s');
                $slip_number = $repair->repair_id;
                $serial_number = null;
                $material_number = $repair->material_number;
                $material_description = $repair->material_description;
                $issue_location = $repair->receive_location;
                $receive_location = $repair->issue_location;
                $quantity = $repair->quantity;
                $remark = 'MIRAI';
                $created_by = Auth::user()->username;
                $created_by_name = Auth::user()->name;
                $synced = null;
                $synced_by = null;

                app(YMESController::class)->goods_movement(
                    $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $receive_location, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                // YMES END

                $repair_log = db::connection('ympimis_2')->table('repair_logs')->insert([
                    'repair_id' => $repair->repair_id,
                    'material_number' => $repair->material_number,
                    'material_description' => $repair->material_description,
                    'issue_location' => $repair->issue_location,
                    'receive_location' => $repair->receive_location,
                    'quantity' => $repair->quantity,
                    'repaired_by' => $repair->created_by,
                    'created_by' => Auth::Id(),
                    'slip_created' => $repair->created_at,
                    'remark' => 'canceled',
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                $response = array(
                    'status' => true,
                    'message' => 'Slip repair berhasil dicancel',
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

    public function fetchRepairLogs(Request $request)
    {

        $date = '';
        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
            $date = "AND date(printed_at) >= '" . $datefrom . "' ";
            if (strlen($request->get('dateto')) > 0) {
                $dateto = date('Y-m-d', strtotime($request->get('dateto')));
                $date = $date . "AND date(printed_at) <= '" . $dateto . "' ";
            }
        }

        $date_pending = '';
        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
            $date_pending = "AND date(created_at) >= '" . $datefrom . "' ";
            if (strlen($request->get('dateto')) > 0) {
                $dateto = date('Y-m-d', strtotime($request->get('dateto')));
                $date_pending = $date_pending . "AND date(created_at) <= '" . $dateto . "' ";
            }
        }

        $issue = '';
        if ($request->get('issue') != null) {
            $issues = $request->get('issue');
            for ($i = 0; $i < count($issues); $i++) {
                $issue = $issue . "'" . $issues[$i] . "'";
                if ($i != (count($issues) - 1)) {
                    $issue = $issue . ',';
                }
            }
            $issue = " AND issue_location IN (" . $issue . ") ";
        }

        $receive = '';
        if ($request->get('receive') != null) {
            $receives = $request->get('receive');
            for ($i = 0; $i < count($receives); $i++) {
                $receive = $receive . "'" . $receives[$i] . "'";
                if ($i != (count($receives) - 1)) {
                    $receive = $receive . ',';
                }
            }
            $receive = " AND receive_location IN (" . $receive . ") ";
        }

        $material = '';
        if ($request->get('material') != null) {
            $materials = $request->get('material');
            for ($i = 0; $i < count($materials); $i++) {
                $material = $material . "'" . $materials[$i] . "'";
                if ($i != (count($materials) - 1)) {
                    $material = $material . ',';
                }
            }
            $material = " AND material_number IN (" . $material . ") ";
        }

        $remark = '';
        if ($request->get('remark') != null) {
            $remark = " AND remark = '" . $request->get('remark') . "' ";
        }

        $condition = $date . $issue . $receive . $material . $remark;
        $users = User::get();

        // $condition = $date_pending . $issue . $receive . $material;
        $logs = db::connection('ympimis_2')->select("SELECT
         *
            FROM
            (
                SELECT
                id,
                id AS repair_id,
                material_number,
                issue_location,
                receive_location,
                material_description,
                quantity,
                'pending' AS remark,
                created_at,
                created_at AS printed_at,
                created_by AS created_by,
                created_by AS act_by,
                '-' AS received_at,
                '-' AS received_by,
                '-' AS rejected_at,
                '-' AS rejected_by,
                '-' AS deleted_at,
                '-' AS deleted_by,
                '-' AS canceled_at,
                '-' AS canceled_by
                FROM
                repair_lists UNION ALL
                SELECT
                id,
                repair_id,
                material_number,
                issue_location,
                receive_location,
                material_description,
                quantity,
                remark,
                created_at,
                slip_created AS printed_at,
                repaired_by AS created_by,
                created_by AS act_by,
                '-' AS received_at,
                '-' AS received_by,
                '-' AS rejected_at,
                '-' AS rejected_by,
                '-' AS deleted_at,
                '-' AS deleted_by,
                '-' AS canceled_at,
                '-' AS canceled_by
                FROM
                repair_logs
                ) AS rl
            WHERE
            repair_id IS NOT NULL " . $condition . "
            ORDER BY
            created_at ASC");

        $result = array();
        $repair_ids = array();

        foreach ($logs as $log) {
            $id = $log->id;
            $repair_id = $log->repair_id;
            $material_number = $log->material_number;
            $issue_location = $log->issue_location;
            $receive_location = $log->receive_location;
            $material_description = $log->material_description;
            $quantity = $log->quantity;
            $remark = $log->remark;
            $printed_at = $log->printed_at;
            $printed_by = "Unregistered";
            $received_at = "-";
            $received_by = "-";
            $rejected_at = "-";
            $rejected_by = "-";
            $deleted_at = "-";
            $deleted_by = "-";
            $canceled_at = "-";
            $canceled_by = "-";

            foreach ($users as $user) {
                if ($user->id == $log->created_by) {
                    $printed_by = $user->name;
                    break;
                }
            }

            foreach ($users as $user) {
                if ($log->remark == 'received') {
                    if ($user->id == $log->act_by) {
                        $received_at = $log->created_at;
                        $received_by = $user->name;
                        break;
                    }
                }
                if ($log->remark == 'rejected') {
                    if ($user->id == $log->act_by) {
                        $rejected_at = $log->created_at;
                        $rejected_by = $user->name;
                        break;
                    }
                }
                if ($log->remark == 'deleted') {
                    if ($user->id == $log->act_by) {
                        $deleted_at = $log->created_at;
                        $deleted_by = $user->name;
                        // break;
                    }
                }
                if ($log->remark == 'canceled') {
                    if ($user->id == $log->act_by) {
                        $canceled_at = $log->created_at;
                        $canceled_by = $user->name;
                        break;
                    }
                }
            }

            if (!in_array($repair_id, $repair_ids)) {
                array_push($result, [
                    "id" => $id,
                    "repair_id" => $repair_id,
                    "material_number" => $material_number,
                    "issue_location" => $issue_location,
                    "receive_location" => $receive_location,
                    "material_description" => $material_description,
                    "quantity" => $quantity,
                    "remark" => $remark,
                    "printed_at" => $printed_at,
                    "printed_by" => $printed_by,
                    "received_at" => $received_at,
                    "received_by" => $received_by,
                    "rejected_at" => $rejected_at,
                    "rejected_by" => $rejected_by,
                    "deleted_at" => $deleted_at,
                    "deleted_by" => $deleted_by,
                    "canceled_at" => $canceled_at,
                    "canceled_by" => $canceled_by,
                ]);
                array_push($repair_ids, $repair_id);
            } else {
                foreach ($result as &$value) {
                    if ($value['repair_id'] == $repair_id) {
                        if ($log->remark == 'canceled') {
                            $value['remark'] = 'canceled';
                            $value['canceled_at'] = $canceled_at;
                            $value['canceled_by'] = $canceled_by;
                        }
                        if ($log->remark == 'received') {
                            $value['remark'] = 'received';
                            $value['received_at'] = $received_at;
                            $value['received_by'] = $received_by;
                        }
                        if ($log->remark == 'rejected') {
                            $value['remark'] = 'rejected';
                            $value['rejected_at'] = $rejected_at;
                            $value['rejected_by'] = $rejected_by;
                        }
                        if ($log->remark == 'deleted') {
                            $value['remark'] = 'deleted';
                            $value['deleted_at'] = $deleted_at;
                            $value['deleted_by'] = $deleted_by;
                        }
                        break;
                    }
                }
            }
        }

        return DataTables::of($result)
            ->addColumn('cancel', function ($data) {
                if (str_contains(Auth::user()->role_code, 'MIS') || str_contains(Auth::user()->role_code, 'PRD')) {
                    if ($data['remark'] == 'pending') {
                        return '<button style="width: 50%; height: 100%;" onclick="deleteRepair(\'' . $data['id'] . '\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-close"></i></span></button>';
                    } else if ($data['remark'] == 'received') {
                        return '<button style="width: 50%; height: 100%;" onclick="cancelRepair(\'' . $data['id'] . '\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-close"></i></span></button>';
                    } else {
                        return '-';
                    }
                } else {
                    return '-';
                }
            })
            ->rawColumns(['cancel' => 'cancel'])
            ->make(true);
    }

    public function indexRepair()
    {
        return view('return.repair', array(
            'title' => 'Repair Material',
            'title_jp' => '??',
            'storage_locations' => $this->repair_location,
        ))->with('page', 'Repair');
    }

    public function reprintRepair(Request $request)
    {
        try {
            $repair = db::connection('ympimis_2')->table('repair_lists')->where('id', '=', $request->get('id'))->first();
            self::repairSlip($repair->id, $repair->material_number, $repair->material_description, $repair->issue_location, $repair->receive_location, $repair->quantity, $repair->created_by);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'message' => 'Cetak ulang slip berhasil',
        );
        return Response::json($response);
    }

    public function confirmRepair(Request $request)
    {
        $id = explode('+', $request->get('id'));
        $auth_id = Auth::id();

        $repair = db::connection('ympimis_2')->table('repair_lists')->where('id', '=', $id[1])->first();
        $material = db::connection('mysql2')->table('materials')->where('material_number', '=', $repair->material_number)->first();

        if ($repair == null) {
            $response = array(
                'status' => false,
                'message' => 'Slip Repair Tidak Ditemukan',
            );
            return Response::json($response);
        }

        $repair_location = ['FA0R', 'LA0R', 'SA0R', 'VA0R', 'FA1R', 'LA1R', 'SA1R'];

        if ($id[0] == 'receive') {
            if (in_array($repair->issue_location, $repair_location)) {
                //after repair
                $transfer_repair = db::connection('mysql2')->table('histories')->insert([
                    "category" => "transfer_after_repair",
                    "transfer_barcode_number" => "",
                    "transfer_document_number" => "8190",
                    "transfer_material_id" => $material->id,
                    "transfer_issue_plant" => "8190",
                    "transfer_issue_location" => $repair->receive_location,
                    "transfer_receive_plant" => "8190",
                    "transfer_receive_location" => $repair->issue_location,
                    "transfer_cost_center" => "",
                    "transfer_gl_account" => "",
                    "transfer_transaction_code" => "MB1B",
                    "transfer_movement_type" => "9I4",
                    "transfer_reason_code" => "",
                    "lot" => $repair->quantity,
                    "synced" => 0,
                    'user_id' => "1",
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                // YMES CANCEL TRANSFER NEW
                $category = 'goods_movement_repair';
                $function = 'confirmRepair';
                $action = 'goods_movement';
                $result_date = date('Y-m-d H:i:s');
                $slip_number = 'RP' . $repair->id;
                $serial_number = null;
                $material_number = $repair->material_number;
                $material_description = $repair->material_description;
                $issue_location = $repair->issue_location;
                $receive_location = $repair->receive_location;
                $quantity = $repair->quantity;
                $remark = 'MIRAI';
                $created_by = Auth::user()->username;
                $created_by_name = Auth::user()->name;
                $synced = null;
                $synced_by = null;

                app(YMESController::class)->goods_movement(
                    $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $receive_location, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                // YMES END

                if ($repair->receive_location == $material->location) {
                    $repair_completion = db::connection('mysql2')->table('histories')->insert([
                        "category" => "completion_after_repair",
                        "completion_barcode_number" => "",
                        "completion_description" => "",
                        "completion_location" => $repair->receive_location,
                        "completion_issue_plant" => "8190",
                        "completion_material_id" => $material->id,
                        "completion_reference_number" => "",
                        "lot" => $repair->quantity * -1,
                        "synced" => 0,
                        'user_id' => "1",
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    // YMES CANCEL COMPLETION NEW
                    $category = 'production_result_repair';
                    $function = 'confirmRepair';
                    $action = 'production_result';
                    $result_date = date('Y-m-d H:i:s');
                    $slip_number = 'RP' . $repair->id;
                    $serial_number = null;
                    $material_number = $repair->material_number;
                    $material_description = $repair->material_description;
                    $issue_location = $repair->receive_location;
                    $mstation = $material->mstation;
                    $quantity = $repair->quantity * -1;
                    $remark = 'MIRAI';
                    $created_by = Auth::user()->username;
                    $created_by_name = Auth::user()->name;
                    $synced = null;
                    $synced_by = null;

                    app(YMESController::class)->production_result(
                        $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                    // YMES END
                }

                $repair_log = db::connection('ympimis_2')->table('repair_logs')->insert([
                    'repair_id' => $repair->id,
                    'material_number' => $repair->material_number,
                    'material_description' => $repair->material_description,
                    'issue_location' => $repair->issue_location,
                    'receive_location' => $repair->receive_location,
                    'quantity' => $repair->quantity,
                    'repaired_by' => $repair->created_by,
                    'created_by' => $auth_id,
                    'slip_created' => $repair->created_at,
                    'remark' => 'received',
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                $repair = db::connection('ympimis_2')->table('repair_lists')->where('id', '=', $id[1])->delete();

                $response = array(
                    'status' => true,
                    'message' => 'Slip after repair berhasil dikonfirmasi',
                );
                return Response::json($response);

            } else {
                //repair
                if ($repair->issue_location == $material->location) {
                    $repair_completion = db::connection('mysql2')->table('histories')->insert([
                        "category" => "completion_repair",
                        "completion_barcode_number" => "",
                        "completion_description" => "",
                        "completion_location" => $repair->issue_location,
                        "completion_issue_plant" => "8190",
                        "completion_material_id" => $material->id,
                        "completion_reference_number" => "",
                        "lot" => $repair->quantity,
                        "synced" => 0,
                        'user_id' => "1",
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    // YMES COMPLETION NEW
                    $category = 'production_result_repair';
                    $function = 'confirmRepair';
                    $action = 'production_result';
                    $result_date = date('Y-m-d H:i:s');
                    $slip_number = 'RP' . $repair->id;
                    $serial_number = null;
                    $material_number = $repair->material_number;
                    $material_description = $repair->material_description;
                    $issue_location = $repair->issue_location;
                    $mstation = $material->mstation;
                    $quantity = $repair->quantity;
                    $remark = 'MIRAI';
                    $created_by = Auth::user()->username;
                    $created_by_name = Auth::user()->name;
                    $synced = null;
                    $synced_by = null;

                    app(YMESController::class)->production_result(
                        $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                    // YMES END
                }
                $transfer_repair = db::connection('mysql2')->table('histories')->insert([
                    "category" => "transfer_repair",
                    "transfer_barcode_number" => "",
                    "transfer_document_number" => "8190",
                    "transfer_material_id" => $material->id,
                    "transfer_issue_plant" => "8190",
                    "transfer_issue_location" => $repair->issue_location,
                    "transfer_receive_plant" => "8190",
                    "transfer_receive_location" => $repair->receive_location,
                    "transfer_cost_center" => "",
                    "transfer_gl_account" => "",
                    "transfer_transaction_code" => "MB1B",
                    "transfer_movement_type" => "9I3",
                    "transfer_reason_code" => "",
                    "lot" => $repair->quantity,
                    "synced" => 0,
                    'user_id' => "1",
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                // YMES TRANSFER NEW
                $category = 'goods_movement_repair';
                $function = 'confirmRepair';
                $action = 'goods_movement';
                $result_date = date('Y-m-d H:i:s');
                $slip_number = 'RP' . $repair->id;
                $serial_number = null;
                $material_number = $repair->material_number;
                $material_description = $repair->material_description;
                $issue_location = $repair->issue_location;
                $receive_location = $repair->receive_location;
                $quantity = $repair->quantity;
                $remark = 'MIRAI';
                $created_by = Auth::user()->username;
                $created_by_name = Auth::user()->name;
                $synced = null;
                $synced_by = null;

                app(YMESController::class)->goods_movement(
                    $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $receive_location, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                // YMES END

                $repair_log = db::connection('ympimis_2')->table('repair_logs')->insert([
                    'repair_id' => $repair->id,
                    'material_number' => $repair->material_number,
                    'material_description' => $repair->material_description,
                    'issue_location' => $repair->issue_location,
                    'receive_location' => $repair->receive_location,
                    'quantity' => $repair->quantity,
                    'repaired_by' => $repair->created_by,
                    'created_by' => $auth_id,
                    'slip_created' => $repair->created_at,
                    'remark' => 'received',
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                $repair = db::connection('ympimis_2')->table('repair_lists')->where('id', '=', $id[1])->delete();

                $response = array(
                    'status' => true,
                    'message' => 'Slip repair berhasil dikonfirmasi',
                );
                return Response::json($response);
            }
        } else {
            try {
                $repair_log = db::connection('ympimis_2')->table('repair_logs')->insert([
                    'repair_id' => $repair->id,
                    'material_number' => $repair->material_number,
                    'material_description' => $repair->material_description,
                    'issue_location' => $repair->issue_location,
                    'receive_location' => $repair->receive_location,
                    'quantity' => $repair->quantity,
                    'repaired_by' => $repair->created_by,
                    'created_by' => $auth_id,
                    'slip_created' => $repair->created_at,
                    'remark' => 'rejected',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $repair = db::connection('ympimis_2')->table('repair_lists')->where('id', '=', $id[1])->delete();
            } catch (\Exception$e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }

            $response = array(
                'status' => true,
                'message' => 'Slip repair berhasil ditolak',
            );
            return Response::json($response);
        }

    }

    public function fetchRepair(Request $request)
    {

        if (substr($request->get('id'), 0, 2) != 'RP') {
            $response = array(
                'status' => false,
                'message' => "QRcode repair salah.",
            );
            return Response::json($response);
        }

        $id = substr($request->get('id'), 2);
        $repair = db::connection('ympimis_2')->table('repair_lists')->where('repair_lists.id', '=', $id)
            ->select('repair_lists.id', 'repair_lists.material_number', 'repair_lists.material_description', 'repair_lists.issue_location', 'repair_lists.receive_location', 'repair_lists.quantity', 'repair_lists.created_at', 'repair_lists.created_by')
            ->first();

        if (!$repair) {
            $response = array(
                'status' => false,
                'message' => "Slip repair sudah diterima.",
            );
            return Response::json($response);
        }

        $user = User::where('id', '=', $repair->created_by)->first();

        $response = array(
            'status' => true,
            'repair' => $repair,
            'user' => $user,
        );
        return Response::json($response);
    }

    public function deleteRepair(Request $request)
    {
        $auth_id = Auth::id();
        $repair = db::connection('ympimis_2')->table('repair_lists')->where('id', '=', $request->get('id'))->first();
        $issue = $repair->issue_location;
        try {
            $repair_log = db::connection('ympimis_2')->table('repair_logs')->insert([
                'repair_id' => $repair->id,
                'material_number' => $repair->material_number,
                'material_description' => $repair->material_description,
                'issue_location' => $repair->issue_location,
                'receive_location' => $repair->receive_location,
                'quantity' => $repair->quantity,
                'repaired_by' => $repair->created_by,
                'created_by' => $auth_id,
                'slip_created' => $repair->created_at,
                'remark' => 'deleted',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $repair = db::connection('ympimis_2')->table('repair_lists')->where('id', '=', $request->get('id'))->delete();
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
        $response = array(
            'status' => true,
            'issue' => $issue,
            'message' => 'Slip return berhasil di delete',
        );
        return Response::json($response);
    }

    public function printRepair(Request $request)
    {
        $id = Auth::id();
        try {
            $repair = db::connection('ympimis_2')->table('repair_lists')->insertGetId([
                'material_number' => $request->get('material'),
                'material_description' => $request->get('description'),
                'issue_location' => $request->get('issue'),
                'receive_location' => $request->get('receive'),
                'quantity' => $request->get('quantity'),
                'created_by' => $id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            self::repairSlip($repair, $request->get('material'), $request->get('description'), $request->get('issue'), $request->get('receive'), $request->get('quantity'), $id);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'message' => 'Cetak slip repair berhasil',
        );
        return Response::json($response);
    }

    public function fetchRepairList(Request $request)
    {
        $lists = db::connection('ympimis_2')->table('repair_materials')
            ->whereNull('deleted_at')
            ->select('material_number', 'material_description as description', 'issue_location', 'receive_location')
            ->where('issue_location', '=', $request->get('loc'))
            ->orderBy('issue_location', 'ASC')
            ->orderBy('material_number', 'ASC')
            ->get();

        $response = array(
            'status' => true,
            'lists' => $lists,
            'message' => 'Lokasi berhasil dipilih',
        );
        return Response::json($response);
    }

    public function fetchRepairResume(Request $request)
    {
        $resumes = db::connection('ympimis_2')->table('repair_lists')->where('issue_location', '=', $request->get('loc'))
            ->orderBy('issue_location', 'asc')
            ->orderBy('material_number', 'asc')
            ->select('repair_lists.id', 'repair_lists.material_number', 'repair_lists.material_description', 'repair_lists.issue_location', 'repair_lists.receive_location', 'repair_lists.quantity', 'repair_lists.created_at', 'repair_lists.created_by')
            ->orderBy('repair_lists.created_at', 'asc')
            ->get();

        $users = User::get();

        $result = array();

        foreach ($resumes as $resume) {
            $id = "";
            $material_number = "";
            $material_description = "";
            $issue_location = "";
            $receive_location = "";
            $quantity = "";
            $name = "";
            $created_at = "";
            $created_by = "";

            foreach ($users as $user) {
                if ($resume->created_by == $user->id) {
                    $id = $resume->id;
                    $material_number = $resume->material_number;
                    $material_description = $resume->material_description;
                    $issue_location = $resume->issue_location;
                    $receive_location = $resume->receive_location;
                    $quantity = $resume->quantity;
                    $name = $user->name;
                    $created_at = $resume->created_at;
                    $created_by = $resume->created_by;
                }
            }

            array_push($result, [
                "id" => $id,
                "material_number" => $material_number,
                "material_description" => $material_description,
                "issue_location" => $issue_location,
                "receive_location" => $receive_location,
                "quantity" => $quantity,
                "name" => $name,
                "created_at" => $created_at,
                "created_by" => $created_by,
            ]);
        }

        $response = array(
            'status' => true,
            'resumes' => $result,
        );
        return Response::json($response);
    }

    public function indexReturn()
    {
        return view('return.index', array(
            'title' => 'Return Material',
            'title_jp' => 'リターン材',
            'storage_locations' => $this->storage_location,
        ))->with('page', 'Return')->with('head', 'Material Return');
    }

    public function indexReturnData()
    {
        $storage_locations = StorageLocation::select('location', 'storage_location')->distinct()
            ->orderBy('location', 'asc')
            ->get();

        return view('return.list', array(
            'title' => 'Data Return Material',
            'title_jp' => 'リターン材データ',
            'storage_locations' => $storage_locations,
        ))->with('page', 'Return');
    }

    public function cancelReturn(Request $request)
    {

        try {

            $return = ReturnLog::where('id', '=', $request->get('id'))->first();

            $material = db::connection('mysql2')->table('materials')
                ->where('material_number', '=', $return->material_number)
                ->first();

            $return_log = new ReturnLog([
                'return_id' => $return->return_id,
                'material_number' => $return->material_number,
                'material_description' => $return->material_description,
                'issue_location' => $return->issue_location,
                'receive_location' => $return->receive_location,
                'quantity' => $return->quantity,
                'returned_by' => $return->returned_by,
                'created_by' => Auth::id(),
                'slip_created' => $return->slip_created,
                'ng' => $return->ng,
                'remark' => 'canceled',
            ]);
            $return_log->save();

            $return_completion = db::connection('mysql2')->table('histories')->insert([
                "category" => "completion_adjustment",
                "completion_barcode_number" => "",
                "completion_description" => "",
                "completion_location" => $return->issue_location,
                "completion_issue_plant" => "8190",
                "completion_material_id" => $material->id,
                "completion_reference_number" => "",
                "lot" => $return->quantity,
                "synced" => 0,
                'user_id' => "1",
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);

            // YMES COMPLETION NEW
            $category = 'production_result_return';
            $function = 'cancelReturn';
            $action = 'production_result';
            $result_date = date('Y-m-d H:i:s');
            $slip_number = $return->id;
            $serial_number = null;
            $material_number = $return->material_number;
            $material_description = $return->material_description;
            $issue_location = $return->issue_location;
            $mstation = $material->mstation;
            $quantity = $return->quantity;
            $remark = 'MIRAI';
            $created_by = Auth::user()->username;
            $created_by_name = Auth::user()->name;
            $synced = null;
            $synced_by = null;

            app(YMESController::class)->production_result(
                $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
            // YMES END

            if ($return->issue_location != $return->receive_location) {
                $return_transfer = db::connection('mysql2')->table('histories')->insert([
                    "category" => "transfer_adjustment",
                    "transfer_barcode_number" => "",
                    "transfer_document_number" => "8190",
                    "transfer_material_id" => $material->id,
                    "transfer_issue_location" => $return->issue_location,
                    "transfer_issue_plant" => "8190",
                    "transfer_receive_plant" => "8190",
                    "transfer_receive_location" => $return->receive_location,
                    "transfer_cost_center" => "",
                    "transfer_gl_account" => "",
                    "transfer_transaction_code" => "MB1B",
                    "transfer_movement_type" => "9I3",
                    "transfer_reason_code" => "",
                    "lot" => $return->quantity,
                    "synced" => 0,
                    'user_id' => "1",
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                // YMES TRANSFER NEW
                $category = 'goods_movement_return';
                $function = 'cancelReturn';
                $action = 'goods_movement';
                $result_date = date('Y-m-d H:i:s');
                $slip_number = $return->return_id;
                $serial_number = null;
                $material_number = $return->material_number;
                $material_description = $return->material_description;
                $issue_location = $return->issue_location;
                $receive_location = $return->receive_location;
                $quantity = $return->quantity;
                $remark = 'MIRAI';
                $created_by = Auth::user()->username;
                $created_by_name = Auth::user()->name;
                $synced = null;
                $synced_by = null;

                app(YMESController::class)->goods_movement(
                    $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $receive_location, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                // YMES END
            }

            $response = array(
                'status' => true,
                'message' => 'Return berhasil dicancel',
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

    public function deleteReturn(Request $request)
    {
        $auth_id = Auth::id();
        $return = ReturnList::where('id', '=', $request->get('id'))->first();
        $receive = $return->receive_location;
        try {
            $return_log = new ReturnLog([
                'return_id' => $return->id,
                'material_number' => $return->material_number,
                'material_description' => $return->material_description,
                'issue_location' => $return->issue_location,
                'receive_location' => $return->receive_location,
                'quantity' => $return->quantity,
                'returned_by' => $return->created_by,
                'created_by' => $auth_id,
                'slip_created' => $return->created_at,
                'remark' => 'deleted',
            ]);
            $return_log->save();
            $return->forceDelete();
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
        $response = array(
            'status' => true,
            'receive' => $receive,
            'message' => 'Slip return berhasil di delete',
        );
        return Response::json($response);
    }

    public function confirmReturn(Request $request)
    {
        $id = explode('+', $request->get('id'));
        $auth_id = Auth::id();
        $return = ReturnList::where('id', '=', $id[1])->first();

        if ($return == null) {
            $response = array(
                'status' => false,
                'message' => 'Slip Return Tidak Ditemukan',
            );
            return Response::json($response);
        }

        if ($id[0] == 'receive') {
            try {

                $material = db::connection('mysql2')->table('materials')
                    ->where('material_number', '=', $return->material_number)
                    ->first();

                if (!$material) {
                    $mpdl = MaterialPlantDataList::where('material_number', $return->material_number)->first();

                    if ($mpdl) {
                        $material = db::connection('mysql2')
                            ->table('materials')
                            ->insert([
                                "material_number" => $mpdl->material_number,
                                "description" => $mpdl->material_description,
                                "location" => $mpdl->storage_location,
                                "mstation" => 'W' . $mpdl->mrpc . 'S10',
                                "lead_time" => 90,
                                "user_id" => 1,
                                'created_at' => date("Y-m-d H:i:s"),
                                'updated_at' => date("Y-m-d H:i:s"),
                            ]);

                        $material = db::connection('mysql2')
                            ->table('materials')
                            ->where('material_number', '=', $return->material_number)
                            ->first();

                    } else {
                        $response = array(
                            'status' => false,
                            'message' => 'Material not found',
                        );
                        return Response::json($response);
                    }

                }

                if ($return->issue_location == $return->receive_location) {
                    $return_completion = db::connection('mysql2')->table('histories')->insert([
                        "category" => "completion_cancel",
                        "completion_barcode_number" => "",
                        "completion_description" => $material->description,
                        "completion_location" => $return->issue_location,
                        "completion_issue_plant" => "8190",
                        "completion_material_id" => $material->id,
                        "completion_reference_number" => "",
                        "lot" => $return->quantity * -1,
                        "synced" => 0,
                        'user_id' => "1",
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    // YMES CANCEL COMPLETION NEW
                    $category = 'production_result_return';
                    $function = 'confirmReturn';
                    $action = 'production_result';
                    $result_date = date('Y-m-d H:i:s');
                    $slip_number = 'RE' . $return->id;
                    $serial_number = null;
                    $material_number = $return->material_number;
                    $material_description = $material->description;
                    $issue_location = $material->location;
                    $mstation = $material->mstation;
                    $quantity = $return->quantity * -1;
                    $remark = 'MIRAI';
                    $created_by = Auth::user()->username;
                    $created_by_name = Auth::user()->name;
                    $synced = null;
                    $synced_by = null;

                    app(YMESController::class)->production_result(
                        $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                    // YMES END

                } else {
                    $return_data = db::connection('mysql2')->table('transfers_return')->insert([
                        'material_id' => $material->id,
                        'issue_location' => $return->issue_location,
                        'issue_plant' => "8190",
                        'receive_location' => $return->receive_location,
                        'receive_plant' => "8190",
                        'movement_type' => "9I4",
                        'transaction_code' => "MB1B",
                        'document_number' => "",
                        'lot' => $return->quantity,
                        'cost_center' => "",
                        'gl_account' => "",
                        'reason_code' => "",
                        'user_id' => "1",
                        'active' => "1",
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    $return_completion = db::connection('mysql2')->table('histories')->insert([
                        "category" => "completion_return",
                        "completion_barcode_number" => "",
                        "completion_description" => "",
                        "completion_location" => $return->issue_location,
                        "completion_issue_plant" => "8190",
                        "completion_material_id" => $material->id,
                        "completion_reference_number" => "",
                        "lot" => $return->quantity * -1,
                        "synced" => 0,
                        'user_id' => "1",
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    // YMES CANCEL COMPLETION NEW
                    $category = 'production_result_return';
                    $function = 'confirmReturn';
                    $action = 'production_result';
                    $result_date = date('Y-m-d H:i:s');
                    $slip_number = 'RE' . $return->id;
                    $serial_number = null;
                    $material_number = $return->material_number;
                    $material_description = $return->material_description;
                    $issue_location = $material->location;
                    $mstation = $material->mstation;
                    $quantity = $return->quantity * -1;
                    $remark = 'MIRAI';
                    $created_by = Auth::user()->username;
                    $created_by_name = Auth::user()->name;
                    $synced = null;
                    $synced_by = null;

                    app(YMESController::class)->production_result(
                        $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                    // YMES END

                    $return_transfer = db::connection('mysql2')->table('histories')->insert([
                        "category" => "transfer_return",
                        "transfer_barcode_number" => "",
                        "transfer_document_number" => "8190",
                        "transfer_material_id" => $material->id,
                        "transfer_issue_location" => $return->issue_location,
                        "transfer_issue_plant" => "8190",
                        "transfer_receive_plant" => "8190",
                        "transfer_receive_location" => $return->receive_location,
                        "transfer_cost_center" => "",
                        "transfer_gl_account" => "",
                        "transfer_transaction_code" => "MB1B",
                        "transfer_movement_type" => "9I4",
                        "transfer_reason_code" => "",
                        "lot" => $return->quantity,
                        "synced" => 0,
                        'user_id' => "1",
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    // YMES CANCEL TRANSFER NEW
                    $category = 'goods_movement_return';
                    $function = 'confirmReturn';
                    $action = 'goods_movement';
                    $result_date = date('Y-m-d H:i:s');
                    $slip_number = 'RE' . $return->id;
                    $serial_number = null;
                    $material_number = $return->material_number;
                    $material_description = $return->material_description;
                    $issue_location = $return->receive_location;
                    $receive_location = $return->issue_location;
                    $quantity = $return->quantity;
                    $remark = 'MIRAI';
                    $created_by = Auth::user()->username;
                    $created_by_name = Auth::user()->name;
                    $synced = null;
                    $synced_by = null;

                    app(YMESController::class)->goods_movement(
                        $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $receive_location, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                    // YMES END
                }

                $ng_quantity = 0;
                $ng = explode(',', $return->ng);
                foreach ($ng as $ng) {
                    $ng_quantity += explode('_', $ng)[1];
                }

                $return_log = new ReturnLog([
                    'return_id' => $return->id,
                    'material_number' => $return->material_number,
                    'material_description' => $return->material_description,
                    'issue_location' => $return->issue_location,
                    'receive_location' => $return->receive_location,
                    'quantity' => $return->quantity,
                    'returned_by' => $return->created_by,
                    'created_by' => $auth_id,
                    'slip_created' => $return->created_at,
                    'ng' => $return->ng,
                    'ng_quantity' => $ng_quantity,
                    'remark' => 'received',

                ]);
                $return_log->save();

                if ($return->receive_location == 'RC91') {
                    $invent = InjectionInventory::where('location', $return->receive_location)->where('material_number', $return->material_number)->first();
                    if ($invent != 0) {
                        if ($invent->quantity <= 0) {
                            $invent->quantity = 0;
                        } else {
                            $new_quantity = $invent->quantity - $return->quantity;
                            $invent->quantity = $new_quantity;
                        }
                        $invent->save();
                    }
                }

                $return->forceDelete();

            } catch (\Exception$e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }

            $response = array(
                'status' => true,
                'message' => 'Slip return berhasil dikonfirmasi',
            );
            return Response::json($response);
        } else {
            try {
                $return_log = new ReturnLog([
                    'return_id' => $return->id,
                    'material_number' => $return->material_number,
                    'material_description' => $return->material_description,
                    'issue_location' => $return->issue_location,
                    'receive_location' => $return->receive_location,
                    'quantity' => $return->quantity,
                    'returned_by' => $return->created_by,
                    'created_by' => $auth_id,
                    'slip_created' => $return->created_at,
                    'remark' => 'rejected',
                ]);
                $return_log->save();
                $return->forceDelete();
            } catch (\Exception$e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }

            $response = array(
                'status' => true,
                'message' => 'Slip return berhasil ditolak',
            );
            return Response::json($response);
        }
    }

    public function fetchReturnLogs(Request $request)
    {

        $date = '';
        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
            $date = "AND date(slip_created) >= '" . $datefrom . "' ";
            if (strlen($request->get('dateto')) > 0) {
                $dateto = date('Y-m-d', strtotime($request->get('dateto')));
                $date = $date . "AND date(slip_created) <= '" . $dateto . "' ";
            }
        }

        $date_pending = '';
        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
            $date_pending = "AND date(rl.created_at) >= '" . $datefrom . "' ";
            if (strlen($request->get('dateto')) > 0) {
                $dateto = date('Y-m-d', strtotime($request->get('dateto')));
                $date_pending = $date_pending . "AND date(rl.created_at) <= '" . $dateto . "' ";
            }
        }

        $issue = '';
        if ($request->get('issue') != null) {
            $issues = $request->get('issue');
            for ($i = 0; $i < count($issues); $i++) {
                $issue = $issue . "'" . $issues[$i] . "'";
                if ($i != (count($issues) - 1)) {
                    $issue = $issue . ',';
                }
            }
            $issue = " AND issue_location IN (" . $issue . ") ";
        }

        $receive = '';
        if ($request->get('receive') != null) {
            $receives = $request->get('receive');
            for ($i = 0; $i < count($receives); $i++) {
                $receive = $receive . "'" . $receives[$i] . "'";
                if ($i != (count($receives) - 1)) {
                    $receive = $receive . ',';
                }
            }
            $receive = " AND receive_location IN (" . $receive . ") ";
        }

        $material = '';
        if ($request->get('material') != null) {
            $materials = $request->get('material');
            for ($i = 0; $i < count($materials); $i++) {
                $material = $material . "'" . $materials[$i] . "'";
                if ($i != (count($materials) - 1)) {
                    $material = $material . ',';
                }
            }
            $material = " AND material_number IN (" . $material . ") ";
        }

        $remark = '';
        if ($request->get('remark') != null) {
            $remark = " AND remark = '" . $request->get('remark') . "' ";
        }

        $condition = $date . $issue . $receive . $material . $remark;

        if ($request->get('remark') == 'pending') {
            $condition = $date_pending . $issue . $receive . $material;
            $log = db::select("SELECT
                rl.id,
                rl.id AS return_id,
                rl.material_number,
                rl.issue_location,
                rl.receive_location,
                rl.material_description,
                rl.quantity,
                'pending' AS remark,
                rl.created_at AS printed_at,
                u.`name` AS printed_by,
                '-' AS received_at,
                '-' AS received_by,
                '-' AS rejected_at,
                '-' AS rejected_by,
                '-' AS deleted_at,
                '-' AS deleted_by,
                '-' AS canceled_at,
                '-' AS canceled_by
                FROM
                return_lists AS rl
                LEFT JOIN users AS u ON u.id = rl.created_by
                WHERE
                rl.deleted_at IS NULL " . $condition . "
                ORDER BY
                rl.created_at");
        } else {
            $log = db::select("SELECT
                non.id,
                non.return_id,
                non.material_number,
                non.issue_location,
                non.receive_location,
                non.material_description,
                non.quantity,
                IF(cancel.remark is null, non.remark, cancel.remark) AS remark,
                non.slip_created AS printed_at,
                return_user.`name` AS printed_by,
                IF(non.remark = 'received', non.created_at, '-') AS received_at,
                IF(non.remark = 'received', non_user.`name`, '-') AS received_by,
                IF(non.remark = 'rejected', non.created_at, '-') AS rejected_at,
                IF(non.remark = 'rejected', non_user.`name`, '-') AS rejected_by,
                IF(non.remark = 'deleted', non.created_at, '-') AS deleted_at,
                IF(non.remark = 'deleted', non_user.`name`, '-') AS deleted_by,
                COALESCE(cancel.created_at, '-') AS canceled_at,
                COALESCE(cancel_user.`name`, '-') AS canceled_by
                FROM
                (SELECT id, return_id, material_number, material_description, issue_location, receive_location, quantity, remark, slip_created, returned_by, created_at, created_by FROM `return_logs`
                 where remark <> 'canceled' " . $condition . " ) AS non
                LEFT JOIN
                (SELECT id, return_id, remark, created_at, created_by FROM `return_logs`
                 where remark = 'canceled' " . $condition . " ) AS cancel
                ON non.return_id = cancel.return_id
                LEFT JOIN (SELECT id, concat(SPLIT_STRING(`name`, ' ', 1), ' ', SPLIT_STRING(`name`, ' ', 2)) as `name` FROM users) AS return_user ON return_user.id = non.returned_by
                LEFT JOIN (SELECT id, concat(SPLIT_STRING(`name`, ' ', 1), ' ', SPLIT_STRING(`name`, ' ', 2)) as `name` FROM users) AS non_user ON non_user.id = non.created_by
                LEFT JOIN (SELECT id, concat(SPLIT_STRING(`name`, ' ', 1), ' ', SPLIT_STRING(`name`, ' ', 2)) as `name` FROM users) AS cancel_user ON cancel_user.id = cancel.created_by
                ORDER BY non.slip_created");
        }

        return DataTables::of($log)
            ->addColumn('cancel', function ($data) {
                if (str_contains(Auth::user()->role_code, "MIS") || str_contains(Auth::user()->role_code, "PRD")) {
                    if ($data->remark == 'pending') {
                        return '<button style="width: 50%; height: 100%;" onclick="deleteReturn(\'' . $data->id . '\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-close"></i></span></button>';
                    } else if ($data->remark == 'received') {
                        return '<button style="width: 50%; height: 100%;" onclick="cancelReturn(\'' . $data->id . '\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-close"></i></span></button>';
                    } else {
                        return '-';
                    }
                } else {
                    return '-';
                }
            })
            ->rawColumns(['cancel' => 'cancel'])
            ->make(true);
    }

    public function fetchReturn(Request $request)
    {

        if (substr($request->get('id'), 0, 2) != 'RE') {
            $response = array(
                'status' => false,
                'message' => "QRcode return salah.",
            );
            return Response::json($response);
        }

        $id = substr($request->get('id'), 2);
        $return = ReturnList::where('return_lists.id', '=', $id)
            ->leftJoin('users', 'users.id', '=', 'return_lists.created_by')
            ->select('return_lists.id', 'return_lists.material_number', 'return_lists.material_description', 'return_lists.issue_location', 'return_lists.receive_location', 'return_lists.quantity', 'users.name', 'return_lists.created_at', 'return_lists.created_by', 'return_lists.ng')
            ->first();

        if ($return) {
            $image = public_path() . '/images/material/' . $return->material_number . '.jpg';
            $image_exist = file_exists($image);

            $response = array(
                'status' => true,
                'return' => $return,
                'image' => asset('/images/material/' . $return->material_number . '.jpg'),
                'image_exist' => $image_exist,
            );
            return Response::json($response);

        } else {
            $response = array(
                'status' => false,
                'message' => 'Slip return sudah diterima',
            );
            return Response::json($response);

        }

    }

    public function fetchReturnList(Request $request)
    {

        $lists = db::table('return_materials')
            ->whereNull('deleted_at')
            ->select('material_number', 'material_description as description', 'issue_location', 'receive_location', 'lot')
            ->where('receive_location', '=', $request->get('loc'))
            ->orderBy('issue_location', 'ASC')
            ->orderBy('material_number', 'ASC')
            ->get();

        $response = array(
            'status' => true,
            'lists' => $lists,
            'message' => 'Lokasi berhasil dipilih',
        );
        return Response::json($response);
    }

    public function fetchReturnResume(Request $request)
    {

        $resumes = ReturnList::where('receive_location', '=', $request->get('loc'))
            ->orderBy('issue_location', 'asc')
            ->orderBy('material_number', 'asc')
            ->leftJoin('users', 'users.id', '=', 'return_lists.created_by')
            ->select('return_lists.id', 'return_lists.material_number', 'return_lists.material_description', 'return_lists.issue_location', 'return_lists.receive_location', 'return_lists.quantity', 'users.name', 'return_lists.created_at', 'return_lists.created_by')
            ->orderBy('return_lists.created_at', 'asc')
            ->get();

        $response = array(
            'status' => true,
            'resumes' => $resumes,
        );
        return Response::json($response);
    }

    public function reprintReturn(Request $request)
    {
        try {
            $return = ReturnList::where('id', '=', $request->get('id'))->first();
            self::returnSlip($return->id, $return->material_number, $return->material_description, $return->issue_location, $return->receive_location, $return->quantity, $return->created_by, $return->ng);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
        $response = array(
            'status' => true,
            'message' => 'Cetak ulang slip return berhasil',
        );
        return Response::json($response);
    }

    public function returnSlipCopy($id, $material, $description, $issue, $receive, $quantity, $created_by, $ng)
    {
        $user = User::where('id', '=', $created_by)->first();
        $return_printer = ReturnMaterial::where('material_number', $material)
            ->where('receive_location', $receive)
            ->first();

        $printer_name = 'MIS';

        if (str_contains(Auth::user()->role_code, 'MIS') || Auth::user()->role_code == 'S') {
            $printer_name = 'MIS';
        } else {
            if ($return_printer) {
                $printer_name = $return_printer->output;
            }
        }

        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->setReverseColors(true);
        $printer->setTextSize(2, 2);
        $printer->text(" SLIP RETURN COPY" . "\n");
        $printer->feed(1);
        $printer->qrCode('RE' . $id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
        $printer->setReverseColors(false);
        $printer->setTextSize(1, 1);
        $printer->text($id . "\n");
        $printer->feed(1);
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(4, 2);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($material . "\n");
        $printer->text($receive . " -> " . $issue . "\n");
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->setTextSize(2, 1);
        $printer->text($description . "\n");
        $printer->feed(1);

        if ($ng) {
            $printer->initialize();
            $printer->setEmphasis(true);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(1, 1);
            $printer->text("NG : \n");

            $ng = explode(',', $ng);
            $ng = array_map(function ($item) {
                $item = explode('_', $item);
                return $item[0] . '(' . $item[1] . ')';
            }, $ng);
            $ng = implode(',', $ng);
            $printer->text($ng . "\n");
        }

        $printer->text("\n");
        $printer->initialize();
        $printer->setReverseColors(true);
        $printer->setTextSize(4, 4);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text(" " . $quantity . " PC(s) \n");
        $printer->feed(1);
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->textRaw($user->name . "\n (" . date("d-M-Y H:i:s") . ")\n");
        $printer->feed(1);
        $printer->cut();
        $printer->close();
    }

    public function returnSlip($id, $material, $description, $issue, $receive, $quantity, $created_by, $ng)
    {
        $user = User::where('id', '=', $created_by)->first();
        $return_printer = ReturnMaterial::where('material_number', $material)
            ->where('receive_location', $receive)
            ->first();

        $printer_name = 'MIS';

        if (str_contains(Auth::user()->role_code, 'MIS') || Auth::user()->role_code == 'S') {
            $printer_name = 'MIS';
        } else {
            if ($return_printer) {
                $printer_name = $return_printer->output;
            }
        }

        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->setTextSize(3, 3);

        if ($receive == $issue) {
            $printer->text("  SLIP RETURN  " . "\n");
            $printer->initialize();

            $printer->setTextSize(1, 1);
            $printer->setUnderline(true);
            $printer->setEmphasis(true);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("          (RETURN SATU LOKASI)          " . "\n");
        } else {
            $printer->setReverseColors(true);
            $printer->text(" SLIP RETURN " . "\n");
        }

        $printer->qrCode('RE' . $id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
        $printer->setReverseColors(false);
        $printer->setTextSize(1, 1);
        $printer->text($id . "\n");
        $printer->feed(1);
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(4, 2);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($material . "\n");
        $printer->text($receive . " -> " . $issue . "\n");
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->setTextSize(2, 1);
        $printer->text($description . "\n");
        $printer->feed(1);

        if ($ng) {
            $printer->initialize();
            $printer->setEmphasis(true);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(1, 1);
            $printer->text("NG : \n");

            $ng = explode(',', $ng);
            $ng = array_map(function ($item) {
                $item = explode('_', $item);
                return $item[0] . '(' . $item[1] . ')';
            }, $ng);
            $ng = implode(',', $ng);
            $printer->text($ng . "\n");
        }

        $printer->text("\n");
        $printer->initialize();
        $printer->setReverseColors(true);
        $printer->setTextSize(4, 4);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text(" " . $quantity . " PC(s) \n");
        $printer->feed(1);
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->textRaw($user->name . "\n (" . date("d-M-Y H:i:s") . ")\n");
        $printer->feed(1);
        $printer->cut();
        $printer->close();
    }

    public function repairSlip($id, $material, $description, $issue, $receive, $quantity, $created_by)
    {

        $user = User::where('id', '=', $created_by)->first();
        $repair_printer = db::connection('ympimis_2')->table('repair_materials')->where('material_number', $material)
            ->where('issue_location', $issue)
            ->first();

        $printer_name = 'MIS';

        if (str_contains(Auth::user()->role_code, 'MIS') || Auth::user()->role_code == 'S') {
            $printer_name = 'MIS';
        } else {
            if ($repair_printer) {
                $printer_name = $repair_printer->output;
            }
        }

        $repair_location = ['FA0R', 'LA0R', 'SA0R', 'VA0R', 'FA1R', 'LA1R', 'SA1R'];

        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->setReverseColors(false);
        if (in_array($issue, $repair_location)) {
            $printer->setTextSize(2, 3);
            $printer->text(" SLIP AFTER REPAIR " . "\n");
        } else {
            $printer->setTextSize(3, 3);
            $printer->text(" SLIP REPAIR " . "\n");
        }
        $printer->qrCode('RP' . $id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);

        $printer->setReverseColors(false);
        $printer->setTextSize(1, 1);
        $printer->text($id . "\n");
        $printer->feed(1);

        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setTextSize(4, 2);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setReverseColors(true);
        $printer->text($material . "\n");
        $printer->text($issue . " -> " . $receive . "\n");
        $printer->initialize();
        $printer->setReverseColors(true);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->setTextSize(2, 1);
        $printer->text($description . "\n");
        $printer->feed(1);
        $printer->setReverseColors(false);
        $printer->setTextSize(4, 4);
        $printer->text(" " . $quantity . " PC(s) \n");
        $printer->feed(1);
        $printer->initialize();
        $printer->setEmphasis(true);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setReverseColors(true);
        $printer->textRaw($user->name . "\n (" . date("d-M-Y H:i:s") . ")\n");
        $printer->feed(1);
        $printer->cut();
        $printer->close();
    }

    // public function printReturn(Request $request)
    // {
    //     $id = Auth::id();
    //     try {
    //         $return = new ReturnList([
    //             'material_number' => $request->get('material'),
    //             'material_description' => $request->get('description'),
    //             'issue_location' => $request->get('issue'),
    //             'receive_location' => $request->get('receive'),
    //             'quantity' => $request->get('quantity'),
    //             'created_by' => $id,
    //         ]);

    //         $return->save();

    //         self::returnSlip($return->id, $return->material_number, $return->material_description, $return->issue_location, $return->receive_location, $return->quantity, $return->created_by);

    //         if ($return->receive_location == 'CL91' || $return->receive_location == 'SX91') {
    //             self::returnSlipCopy($return->id, $return->material_number, $return->material_description, $return->issue_location, $return->receive_location, $return->quantity, $return->created_by);
    //         }
    //     } catch (\Exception$e) {
    //         $response = array(
    //             'status' => false,
    //             'message' => $e->getMessage(),
    //         );
    //         return Response::json($response);
    //     }

    //     $response = array(
    //         'status' => true,
    //         'message' => 'Cetak slip return berhasil',
    //     );
    //     return Response::json($response);
    // }

    public function printReturn(Request $request)
    {
        $ng_lists = $request->get('return_list');

        $ng_lists = array_map(function ($ng_list) {
            return array('ng' => $ng_list['ng'], 'qty' => $ng_list['qty']);
        }, $ng_lists);

        $quantity = array_sum(array_column($ng_lists, 'qty'));

        if (!$quantity) {
            $quantity = $request->get('quantity');
        }

        $ng_lists = array_filter($ng_lists, function ($ng_list) {
            return $ng_list['ng'] != 'OK';
        });

        foreach ($ng_lists as $key => $value) {
            $ng[] = $value['ng'] . '_' . $value['qty'];
        }
        $ng = implode(',', $ng);

        $id = Auth::id();
        try {
            $return = new ReturnList([
                'material_number' => $request->get('material'),
                'material_description' => $request->get('description'),
                'issue_location' => $request->get('issue'),
                'receive_location' => $request->get('receive'),
                'quantity' => $quantity,
                'ng' => $ng,
                'created_by' => $id,
            ]);
            $return->save();

            self::returnSlip($return->id, $return->material_number, $return->material_description, $return->issue_location, $return->receive_location, $return->quantity, $return->created_by, $return->ng);

            if ($return->receive_location == 'CL91' || $return->receive_location == 'SX91') {
                self::returnSlipCopy($return->id, $return->material_number, $return->material_description, $return->issue_location, $return->receive_location, $return->quantity, $return->created_by, $return->ng);
            }
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'message' => 'Cetak slip return berhasil',
        );
        return Response::json($response);
    }

    public function importCompletion(Request $request)
    {
        if ($request->hasFile('completion')) {
            try {
                $file = $request->file('completion');
                $file_name = 'import_cs_' . Auth::id() . '(' . date("y-m-d") . ')' . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('import/completion/'), $file_name);

                $excel = public_path('import/completion/') . $file_name;
                $rows = Excel::load($excel, function ($reader) {
                    $reader->noHeading();
                    $reader->skipRows(1);
                })->get();
                $rows = $rows->toArray();

                // DB::beginTransaction();
                $month = $request->get('date_completion');
                $cc = $request->get('cc');
                $cost_center_name = explode(",", $cc);

                $existing = SapCompletion::leftJoin('storage_locations', 'storage_locations.storage_location', '=', 'sap_completions.storage_location')
                    ->where(db::raw('DATE_FORMAT(sap_completions.posting_date, "%Y-%m")'), $month)
                    ->whereIn('storage_locations.cost_center_name', $cost_center_name)
                    ->delete();

                for ($i = 0; $i < count($rows); $i++) {
                    $entry_date = $rows[$i][0]->format('Y-m-d');
                    $posting_date = $rows[$i][3]->format('Y-m-d');
                    $movement_type = $rows[$i][4];
                    $material_number = $rows[$i][5];
                    $quantity = $rows[$i][8];
                    $storage_location = $rows[$i][12];
                    $reference = $rows[$i][15];

                    $log = new SapCompletion([
                        'entry_date' => $entry_date,
                        'posting_date' => $posting_date,
                        'movement_type' => $movement_type,
                        'material_number' => $material_number,
                        'quantity' => $quantity,
                        'storage_location' => $storage_location,
                        'reference' => $reference,
                        'created_by' => Auth::id(),
                    ]);
                    $log->save();

                }

                $response = array(
                    'status' => true,
                    'message' => 'Upload file success',
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

    public function indexTransferVerification()
    {

        $title = 'Transfer Verification';
        $title_jp = '振込検証';

        return view('transactions.transfer_verification', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Transfer Verification');

    }

    public function fetchTransferVerification(Request $request)
    {

        $tools = db::connection('ympimis_2')
            ->table('transaction_tools')
            ->get();

        $trx = DB::connection('ympimis_2')
            ->table('kitto_transaction_checks')
            ->whereNull('checked_at')
            ->get();

        $new_trx = [];
        for ($i = 0; $i < count($trx); $i++) {
            $key = '';
            $key .= ($trx[$i]->material_number . '#');
            $key .= ($trx[$i]->material_description . '#');
            $key .= ($trx[$i]->issue_location . '#');
            $key .= ($trx[$i]->receive_location . '#');

            if (!array_key_exists($key, $new_trx)) {
                $row = array();
                $row['material_number'] = $trx[$i]->material_number;
                $row['material_description'] = $trx[$i]->material_description;
                $row['issue_location'] = $trx[$i]->issue_location;
                $row['receive_location'] = $trx[$i]->receive_location;
                $row['lot'] = 1;
                $row['quantity'] = $trx[$i]->quantity;

                $new_trx[$key] = (object) $row;

            } else {
                $new_trx[$key]->lot = $new_trx[$key]->lot + 1;
                $new_trx[$key]->quantity = $new_trx[$key]->quantity + $trx[$i]->quantity;
            }
        }

        $response = array(
            'status' => true,
            'tools' => $tools,
            'trx' => $new_trx,
        );
        return Response::json($response);

    }

    public function fetchTransferVerificationLog(Request $request)
    {

        $date = explode(" - ", $request->get('logs_date'));

        $trx = DB::connection('ympimis_2')
            ->table('kitto_transaction_checks')
            ->where(db::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'), '>=', $date[0])
            ->where(db::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'), '<=', $date[1])
            ->get();

        $response = array(
            'status' => true,
            'trx' => $trx,
        );
        return Response::json($response);

    }

    public function inputTransferVerification(Request $request)
    {

        $material_number = $request->get('material_number');

        try {

            $trx = DB::connection('ympimis_2')
                ->table('kitto_transaction_checks')
                ->whereIn('material_number', $material_number)
                ->update([
                    'checked_at' => date('Y-m-d H:i:s'),
                    'checked_by' => null,
                ]);

            $response = array(
                'status' => true,
                'count' => count($material_number),
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

    public function fetchNGList(Request $request)
    {
        try {
            $ng_lists = NgList::select(DB::raw('distinct(ng_name)'))
                ->where('remark', 'return')
                ->orderBy('ng_name', 'asc')
                ->get();
            // DB::raw('group_concat(storage_location) as storage_location'))
            // DB::raw('(group_concat(distinct(storage_location))) as storage_location'))
            // ->groupBy('ng_name')
            // ->get();

            $response = array(
                'status' => true,
                'ng_lists' => $ng_lists,
            );
            return Response::json($response);
        } catch (\Throwable$th) {
            $response = array(
                'status' => false,
                'message' => $th->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function indexReturnMonitoring()
    {
        $title = 'Return Monitoring';
        $title_jp = '??';

        $storage_location = StorageLocation::select('storage_location', 'location')
            ->where('category', 'WIP')
            ->get();

        return view('return.return_monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'storage_location' => $storage_location,
        ))->with('page', 'Return Monitoring');
    }

    public function fetchMonitoringReturnLogs(Request $request)
    {
        try {
            $return_logs = ReturnLog::
                leftJoin('return_materials', 'return_logs.material_number', '=', 'return_materials.material_number')
                ->leftJoin('material_plant_data_lists', 'return_logs.material_number', '=', 'material_plant_data_lists.material_number')
                ->where('return_logs.remark', 'received')
                ->where('return_logs.ng', '!=', '')
                ->select(DB::raw('return_logs.material_number,
                material_plant_data_lists.material_description,
                sum(return_logs.ng_quantity) as quantity,
                sum(return_logs.ng_quantity * material_plant_data_lists.standard_price/1000) as amount,
                material_plant_data_lists.standard_price,
                group_concat(return_logs.ng) as ng,
                return_logs.issue_location,
                return_logs.receive_location,
                return_materials.material_category'))
                ->groupBy('return_logs.material_number', 'return_logs.issue_location', 'return_logs.receive_location', 'return_materials.material_category', 'material_plant_data_lists.standard_price', 'material_plant_data_lists.material_description')
                ->orderBy('amount', 'desc')
                ->limit(5);

            $material_category = $request->get('material_category');
            $location = $request->get('storage_location');
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');

            if ($material_category != null) {
                $return_logs = $return_logs->where('return_materials.material_category', '=', $material_category);
            }

            if ($location != null && $location != 'all') {
                $return_logs = $return_logs->where('return_logs.issue_location', '=', $location);
            }

            if ($date_from != null && $date_to != null) {
                $return_logs = $return_logs
                    ->where(db::raw('date(return_logs.created_at)'), '>=', $date_from)
                    ->where(db::raw('date(return_logs.created_at)'), '<=', $date_to);
            }

            $return_logs = $return_logs->get();

            if ($return_logs == null) {
                $response = array(
                    'status' => false,
                    'message' => 'Data not found',
                );
                return Response::json($response);
            }

            $response = array(
                'status' => true,
                'return_logs' => $return_logs,
            );

            return Response::json($response);
        } catch (\Throwable$th) {
            $response = array(
                'status' => false,
                'message' => $th->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchMPDLList(Request $request)
    {
        $material_number = $request->get('material_number');

        try {
            $mpdl_lists = MaterialPlantDataList::
                select('material_number', 'material_description', 'storage_location', 'valcl', 'standard_price', 'created_by')
                ->whereIn('material_number', $material_number)
                ->get();

            $response = array(
                'status' => true,
                'mpdl_lists' => $mpdl_lists,
            );
            return Response::json($response);
        } catch (\Throwable$th) {
            $response = array(
                'status' => false,
                'message' => $th->getMessage(),
            );
            return Response::json($response);
        }
    }

}
