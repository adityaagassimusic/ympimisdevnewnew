<?php

namespace App\Http\Controllers;

use App\Assembly;
use App\AssemblyDetail;
use App\AssemblyFlow;
use App\AssemblyInventory;
use App\AssemblyLog;
use App\AssemblyNgLog;
use App\AssemblyNgTemp;
use App\AssemblySerial;
use App\AssemblyTag;
use App\CodeGenerator;
use App\Employee;
use App\EmployeeSync;
use App\Libraries\ActMLEasyIf;
use App\LogProcess;
use App\Material;
use App\Plc;
use App\PlcCounter;
use App\Process;
use App\StampHierarchy;
use App\StampInventory;
use App\WeeklyCalendar;
use DataTables;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Response;
use Yajra\DataTables\Exception;

class AssemblyProcessController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $http_user_agent = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) {
                // Prevent MS office products detecting the upcoming re-direct .. forces them to launch the browser to this link
                die();
            }
        }
        $this->location_fl = [
            'kariawase-fungsi',
            'kariawase-visual',
            'perakitanawal-kensa',
            'tanpoawase-kensa',
            'tanpoawase-fungsi',
            'kango-fungsi',
            'kango-kensa',
            'renraku-fungsi',
            'qa-fungsi',
            'fukiage1-visual',
            'qa-visual1',
            'qa-visual2',
            'qa-kensasp',
        ];

        $this->sn_trial = ['21R60561',
            '21R60563',
            '21R60564',
            '21R60578',
            '21R60586',
            '21R60587',
            '21R60597',
            '21R60599',
            '21R60605',
            '21R60863'];

        $this->location_fl_display = [
            'fukiage1-process',
            'fukiage1-visual',
            'fukiage2-process',
            'kango-fungsi',
            'kango-kensa',
            'kango-process',
            'kariawase-process',
            'kariawase-repair',
            'kariawase-fungsi',
            'perakitanawal-kensa',
            'perakitan-process',
            'renraku-fungsi',
            'renraku-process',
            'repair-process',
            'seasoning-process',
            'stamp-process',
            'tanpoawase-fungsi',
            'tanpoawase-kensa',
            'tanpoawase-process',
            'tanpoire-process',
            'qa-fungsi',
            'qa-visual1',
            'qa-visual2',
            'kariawase-visual',
        ];

        $this->location_cl = [
            'registration-process',
            'kariawase-upper',
            'kariawase-lower',
            'tanpoawase-upper',
            'tanpoawase-lower',
            'kensa-process',
            'qa-kensa',
            'packing-process',
        ];

        $this->location_sx = [
            'registration-process',
            'kariawase-process',
            'renraku-process',
            'rakit-process',
            'joint-process',
            'tanpoawase-process',
            'packing-process',
            'chousei-process',
            'fukiage-process',
            'repair-process',
            'kensa-process',
            'qa-fungsi',
            'qa-visual',
            'qa-kensa',
        ];

        $this->location_sx2 = [
            'registration-process',
            'preparation-process',
            'Line 1',
            'Line 2',
            'Line 3',
            'Line 4',
            'Line 5',
            'repair-process',
            'kensa-process',
            'qa-fungsi',
            'qa-visual',
            'qa-kensa',
            'qa-audit',
            'packing',
        ];
    }

    public function indexTanpoStockMonitoring()
    {
        $title = 'Tanpo Stock Monitoring';
        $title_jp = 'タンポ在庫モニター';

        return view('processes.assembly.tanpo.stock_monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Tanpo')->with('head', 'Assembly Process');
    }

    public function fetchTanpoStockMonitoring(Request $request)
    {

        $stock_date = date('Y-m-01', strtotime('+3 Days'));
        $now = date('Y-m-d');

        if (strlen($request->get('date') > 0)) {
            $now = $request->get('date');
            $stock_date = date('Y-m-01', strtotime($now . '+3 Days'));
        }

        $safety_stocks = db::select("SELECT
			remark,
			sum( total ) AS total_need,
			( SELECT count( week_date ) FROM weekly_calendars WHERE date_format( week_date, '%Y-%m-01' ) = '" . $stock_date . "' AND remark <> 'H' ) AS work_day,
			ceil(
			sum( total )/((
			SELECT
			count( week_date )
			FROM
			weekly_calendars
			WHERE
			date_format( week_date, '%Y-%m-01' ) = '" . date('Y-m-01') . "'
			AND remark <> 'H'
			)/ 4
			)/ 100
			)* 100 AS safety_stock
			FROM
			(
			SELECT
			tb.remark,
			tb.USAGE * pf.quantity AS total
			FROM
			production_forecasts AS pf
			INNER JOIN tanpo_boms AS tb ON tb.material_parent = pf.material_number
			WHERE
			pf.forecast_month = '" . $stock_date . "'
			) AS target
			GROUP BY
			remark
			ORDER BY
			remark ASC");

        if ($now == date('Y-m-d')) {
            $actual_stocks = db::select("SELECT
            m.remark,
            IFNULL( sum( i.lot ), 0 ) AS stock
            FROM
            kitto.materials AS m
            LEFT JOIN kitto.inventories AS i ON m.material_number = i.material_number
            WHERE
            m.location IN ( 'SXT9', 'FLT9' )
            AND remark IS NOT NULL
            GROUP BY
            m.remark");
        } else {
            $actual_stocks = db::select("SELECT
			s.remark,
			IFNULL( sum( s.quantity ), 0 ) AS stock
			FROM
			daily_stocks AS s
			WHERE
			s.location IN ( 'SXT9', 'FLT9' )
			AND s.remark IS NOT NULL
			AND date( created_at ) = '" . date('Y-m-d', strtotime($now . '-1 Day')) . "'
			GROUP BY
			s.remark");
        }

        $plcs = Plc::orderBy('location', 'asc')->where('location', '=', 'Tanpo')->get();
        $lists = array();

        foreach ($plcs as $plc) {
            $cpu = new ActMLEasyIf($plc->station);
            $datas = $cpu->read_data($plc->address, 10);
            $data = $datas[$plc->arr];

            array_push($lists, [
                'location' => $plc->location,
                'remark' => $plc->remark,
                'value' => $data,
                'upper_limit' => $plc->upper_limit,
                'lower_limit' => $plc->lower_limit,
            ]);
        }

        $response = array(
            'status' => true,
            'safety_stocks' => $safety_stocks,
            'actual_stocks' => $actual_stocks,
            'temps' => $lists,
        );
        return Response::json($response);
    }

    public function stampFluteAdjustSerial(Request $request)
    {
        if ($request->get('adjust') == 'minus') {
            $code_generator = CodeGenerator::where('note', '=', $request->get('origin_group_code'))->first();
            $code_generator->index = $code_generator->index - 1;
            $code_generator->save();

            $response = array(
                'status' => true,
                'message' => 'Serial number adjusted',
            );
            return Response::json($response);
        } else {
            $code_generator = CodeGenerator::where('note', '=', $request->get('origin_group_code'))->first();
            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $response = array(
                'status' => true,
                'message' => 'Serial number adjusted',
            );
            return Response::json($response);
        }
    }

    public function indexClarinetPrintLabel()
    {

        $title = 'Clarinet Print Packing Label';
        $title_jp = '';

        return view('processes.assembly.clarinet.print_label', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Process Assy CL')->with('head', 'Assembly Process');
    }

    public function indexClarinetPrintLabelOuter()
    {

        $title = 'Clarinet Print Packing Label Outer';
        $title_jp = '';

        $models = db::table('materials')->where('origin_group_code', '=', '042')
            ->where('category', '=', 'FG')
            ->orderBy('material_description', 'asc')
            ->select('material_description', 'material_number')
            ->distinct()
            ->get();

        return view('processes.assembly.clarinet.print_label_outer', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'models' => $models,
        ))->with('page', 'Process Assy CL')->with('head', 'Assembly Process');
    }

    public function indexSaxophonePrintLabel()
    {

        $title = 'Saxophone Print Packing Label';
        $title_jp = '';

        return view('processes.assembly.saxophone.print_label', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Process Assy SX')->with('head', 'Assembly Process');
    }

    public function indexClarinetRegistration()
    {

        $title = 'Clarinet Serial Number Registration';
        $title_jp = '';

        $models = db::table('materials')->where('origin_group_code', '=', '042')
            ->where('category', '=', 'FG')
            ->orderBy('model', 'asc')
            ->select('model')
            ->distinct()
            ->get();

        $daisha = DB::connection('ympimis_2')->SELECT("SELECT
				GROUP_CONCAT( DISTINCT ( location ) ) AS daisha
			FROM
				assembly_seasonings
			WHERE
				remark = 'Masuk'
				OR remark = 'Keluar'");

        return view('processes.assembly.clarinet.registration', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'models' => $models,
            'daisha' => $daisha,
        ))->with('page', 'Process Assy SX')->with('head', 'Assembly Process');
    }

    public function indexSaxophoneRegistration()
    {

        $title = 'Saxophone Serial Number Registration';
        $title_jp = '';

        $models = db::table('materials')->where('origin_group_code', '=', '043')
            ->where('category', '=', 'FG')
            ->orderBy('model', 'asc')
            ->select('model')
            ->distinct()
            ->get();

        return view('processes.assembly.saxophone.registration', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'models' => $models,
            'sn_trial' => $this->sn_trial,
        ))->with('page', 'Process Assy SX')->with('head', 'Assembly Process');
    }

    public function editModel(Request $request)
    {
        $serial_number = $request->get('serial_number');
        $origin_group_code = $request->get('origin_group_code');
        $model = $request->get('model');
        $location = $request->get('location');
        $op_id = $request->get('op_id');
        $started_at = $request->get('started_at');

        try {
            $assembly_detail = AssemblyDetail::where('origin_group_code', '=', $origin_group_code)
                ->where('serial_number', '=', $serial_number)
                ->where('location', '=', $location)
                ->update([
                    'model' => $model,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'operator_id' => $op_id,
                    'sedang_start_date' => $started_at,
                    'sedang_finish_date' => date('Y-m-d H:i:s'),
                    'created_by' => $op_id,
                    'is_send_log' => 0,
                ]);

            $assembly_inventory = AssemblyInventory::where('origin_group_code', '=', $origin_group_code)
                ->where('serial_number', '=', $serial_number)
                ->update([
                    'model' => $model,
                    'location' => $location,
                    'created_by' => $op_id,
                    'location_next' => 'perakitan-process',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $stamp_inventory = StampInventory::where('origin_group_code', '=', $origin_group_code)
                ->where('serial_number', '=', $serial_number)
                ->update([
                    'model' => $model,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $log_process = LogProcess::where('origin_group_code', '=', $origin_group_code)
                ->where('serial_number', '=', $serial_number)
                ->update([
                    'model' => $model,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $response = array(
                'status' => true,
                'model' => $model,
                'message' => 'Model berhasil dirubah.',
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

    public function fetchModel(Request $request)
    {
        $serial_number = $request->get('serial_number');
        $origin_group_code = $request->get('origin_group_code');
        $process_code = $request->get('process_code');

        try {
            if ($origin_group_code == '042') {
                $stamp_inventory = StampInventory::where('origin_group_code', '=', $origin_group_code)
                    ->where('serial_number', '=', $serial_number)
                // ->where('process_code', '=', $process_code)
                    ->first();

                // if(!$stamp_inventory){
                //     $response = array(
                //         'status' => false,
                //         'message' => 'Serial Number Tidak Ditemukan'
                //     );
                //     return Response::json($response);
                // }

                $response = array(
                    'status' => true,
                    'stamp_inventory' => $stamp_inventory,
                );
                return Response::json($response);
            } else {
                $stamp_inventory = StampInventory::where('origin_group_code', '=', $origin_group_code)
                    ->where('serial_number', '=', $serial_number)
                // ->where('process_code', '=', $process_code)
                    ->first();

                $log_process = LogProcess::where('origin_group_code', '=', $origin_group_code)
                    ->where('serial_number', '=', $serial_number)
                    ->where('process_code', '=', '1')
                // ->where('process_code', '=', $process_code)
                    ->first();

                if (!$stamp_inventory) {
                    $response = array(
                        'status' => false,
                        'message' => 'Serial Number Tidak Ditemukan',
                    );
                    return Response::json($response);
                }

                $response = array(
                    'status' => true,
                    'stamp_inventory' => $stamp_inventory,
                    'log_process' => $log_process,
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

    public function indexFluteStamp()
    {

        $models = db::table('materials')->where('origin_group_code', '=', '041')
            ->where('category', '=', 'FG')
            ->orderBy('model', 'asc')
            ->select('model')
            ->distinct()
            ->get();
        $otorisasi_adjust = Employee::select('tag')->whereIn('employee_id', ['PI9902018', 'PI1910002', 'PI2009022', 'PI1005001', 'PI9707003'])->get();

        $title = 'Flute Stamp';
        $title_jp = 'フルートの刻印';
        return view('processes.assembly.flute.stamp', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'otorisasi_adjust' => $otorisasi_adjust,
            'models' => $models,
            'models2' => $models,
        ))->with('page', 'Assembly FL')->with('head', 'Assembly Process');
    }

    public function fetchSNReady(Request $request)
    {
        try {
            $origin_group_code = $request->get('origin_group_code');
            $sn = DB::connection('ympimis_2')->table('assembly_serial_plots')->where('status', null)->where('origin_group_code', $origin_group_code)->limit(1000)->get();
            $response = array(
                'status' => true,
                'sn' => $sn,
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

    public function indexClarinetStamp()
    {

        $model2 = StampInventory::where('origin_group_code', '=', '042')->orderBy('created_at', 'desc')
            ->get();
        $title = 'Clarinet Stamp';
        $title_jp = 'クラリネットの刻印';
        return view('processes.assembly.clarinet.stamp', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'model2' => $model2,
        ))->with('page', 'Assembly CL')->with('head', 'Assembly Process');
    }

    public function inputRegistrationProcess(Request $request)
    {

        $origin_group_code = $request->get('origin_group_code');
        $model = $request->get('model');
        $serial_number = $request->get('serial_number');
        $tagName = $request->get('tagName');
        $op_id = $request->get('op_id');
        $started_at = $request->get('started_at');
        $location = $request->get('location');

        try {
            if ($origin_group_code == '042') {
                if ($request->get('vals') == 'ALL' || $request->get('vals') == 'LOWER') {
                    $assembly_tag = AssemblyTag::where('origin_group_code', '=', $origin_group_code)
                        ->where('remark', '=', $tagName)
                        ->where('color', '=', $model)
                        ->first();

                    if (!$assembly_tag) {
                        $response = array(
                            'status' => false,
                            'message' => 'Kartu Tidak Sesuai',
                        );
                        return Response::json($response);
                    }
                }
            } else {
                $assembly_tag = AssemblyTag::where('origin_group_code', '=', $origin_group_code)
                    ->where('remark', '=', $tagName)
                    ->where('color', '=', $model)
                    ->first();

                if (!$assembly_tag) {
                    $response = array(
                        'status' => false,
                        'message' => 'Kartu Tidak Sesuai',
                    );
                    return Response::json($response);
                }
            }

            if ($origin_group_code == '042') {
                if ($request->get('vals') == 'ALL' || $request->get('vals') == 'UPPER') {
                    $tagName2 = $request->get('tagName2');

                    $assembly_tag2 = AssemblyTag::where('origin_group_code', '=', $origin_group_code)
                        ->where('remark', '=', $tagName2)
                        ->where('color', '=', $model)
                        ->first();

                    if (!$assembly_tag2) {
                        $response = array(
                            'status' => false,
                            'message' => 'Kartu Tidak Sesuai',
                        );
                        return Response::json($response);
                    }

                    $assembly_detail2 = AssemblyDetail::firstOrCreate(
                        ['serial_number' => $serial_number . '_U', 'origin_group_code' => $origin_group_code, 'location' => $location],
                        ['tag' => $assembly_tag2->tag, 'model' => $model, 'location' => $location, 'operator_id' => $op_id, 'sedang_start_date' => $started_at, 'sedang_finish_date' => date('Y-m-d H:i:s'), 'created_by' => $op_id, 'is_send_log' => 0]
                    );
                    $assembly_detail2->tag = $assembly_tag2->tag;
                    $assembly_detail2->model = $model;
                    $assembly_detail2->location = $location;
                    $assembly_detail2->operator_id = $op_id;
                    $assembly_detail2->sedang_start_date = $started_at;
                    $assembly_detail2->sedang_finish_date = date('Y-m-d H:i:s');
                    $assembly_detail2->created_by = $op_id;

                    $assembly_inventory2 = AssemblyInventory::firstOrCreate(
                        ['serial_number' => $serial_number . '_U', 'origin_group_code' => $origin_group_code],
                        ['tag' => $assembly_tag2->tag, 'model' => $model, 'location' => $location, 'location_next' => 'perakitan-process', 'created_by' => $op_id]
                    );
                    $assembly_inventory2->tag = $assembly_tag2->tag;
                    $assembly_inventory2->model = $model;
                    $assembly_inventory2->location = $location;
                    $assembly_inventory2->created_by = $op_id;

                    $assembly_tag2->serial_number = $serial_number . '_U';
                    $assembly_tag2->model = $model;

                    $delete_assembly_tag2 = AssemblyTag::where('origin_group_code', '=', $origin_group_code)
                        ->where('remark', '<>', $tagName2)
                        ->where('serial_number', '=', $serial_number . '_U')
                        ->first();

                    if ($delete_assembly_tag2) {
                        $delete_assembly_tag2->serial_number = null;
                        $delete_assembly_tag2->model = null;
                        $delete_assembly_tag2->save();
                    }

                    DB::transaction(function () use ($assembly_detail2, $assembly_inventory2, $assembly_tag2) {
                        $assembly_detail2->save();
                        $assembly_inventory2->save();
                        $assembly_tag2->save();
                    });

                    $this->printStamp($tagName2, $serial_number . '_U', $model, 'print', 'Printer Barcode CL', '', '');
                    // $this->printStamp($tagName2, $serial_number.'_U', $model, 'print', 'MIS', '', '');
                }

                if ($request->get('vals') == 'ALL' || $request->get('vals') == 'LOWER') {
                    $assembly_detail = AssemblyDetail::firstOrCreate(
                        ['serial_number' => $serial_number, 'origin_group_code' => $origin_group_code, 'location' => $location],
                        ['tag' => $assembly_tag->tag, 'model' => $model, 'location' => $location, 'operator_id' => $op_id, 'sedang_start_date' => $started_at, 'sedang_finish_date' => date('Y-m-d H:i:s'), 'created_by' => $op_id, 'is_send_log' => 0]
                    );
                    $assembly_detail->tag = $assembly_tag->tag;
                    $assembly_detail->model = $model;
                    $assembly_detail->location = $location;
                    $assembly_detail->operator_id = $op_id;
                    $assembly_detail->sedang_start_date = $started_at;
                    $assembly_detail->sedang_finish_date = date('Y-m-d H:i:s');
                    $assembly_detail->created_by = $op_id;
                    $cites = $request->get('cites');
                    if (str_contains($model,'YCL450') || $model == 'YCL400AD') {
                        $assembly_detail->status_material = $cites;
                    }

                    $assembly_inventory = AssemblyInventory::firstOrCreate(
                        ['serial_number' => $serial_number, 'origin_group_code' => $origin_group_code],
                        ['tag' => $assembly_tag->tag, 'model' => $model, 'location' => $location, 'location_next' => 'perakitan-process', 'created_by' => $op_id]
                    );
                    $assembly_inventory->tag = $assembly_tag->tag;
                    $assembly_inventory->model = $model;
                    $assembly_inventory->location = $location;
                    if (str_contains($model,'YCL450') || $model == 'YCL400AD') {
                        $assembly_inventory->status_material = $cites;
                    }
                    $assembly_inventory->created_by = $op_id;

                    $emp = EmployeeSync::where('employee_id', $op_id)->first();

                    if (str_contains($model,'YCL450') || $model == 'YCL400AD') {
                        $check_loc = DB::connection('ympimis_2')->table('assembly_seasonings')->where('location', explode('/', $cites)[4])->where('remark', null)->get();
                        if (count($check_loc) == 70) {
                            $response = array(
                                'status' => false,
                                'message' => 'Daisha Sudah Penuh',
                            );
                            return Response::json($response);
                        }

                        $code_generator = CodeGenerator::where('note', 'seasoning')->first();
                        $number_season = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
                        $code_generator->index = $code_generator->index + 1;

                        $insert_loc = DB::connection('ympimis_2')->table('assembly_seasonings')->insert([
                            'origin_group_code' => '042',
                            'seasoning_id' => $code_generator->prefix . $number_season,
                            'location' => explode('/', $cites)[4],
                            'tag' => $assembly_tag->tag,
                            'material' => $assembly_tag->remark . ' - ' . $model . ' - ' . $serial_number,
                            'employee_id' => $op_id,
                            'name' => $emp->name,
                            'remark' => 'QA',
                            'timestamps' => date('Y-m-d H:i:s'),
                            'created_by' => Auth::user()->id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                        $code_generator->save();
                    }

                    $log_process = LogProcess::updateOrCreate(
                        [
                            'process_code' => '1',
                            'serial_number' => $serial_number,
                            'origin_group_code' => $origin_group_code,
                        ],
                        [
                            'model' => $model,
                            'quantity' => 1,
                            'created_by' => Auth::id(),
                            'created_at' => date('Y-m-d H:i:s'),
                        ]
                    );

                    $log_process = LogProcess::updateOrCreate(
                        [
                            'process_code' => '2',
                            'serial_number' => $serial_number,
                            'origin_group_code' => $origin_group_code,
                        ],
                        [
                            'model' => $model,
                            'quantity' => 1,
                            'created_by' => Auth::id(),
                            'created_at' => date('Y-m-d H:i:s'),
                        ]
                    );

                    $stamp_inventory = StampInventory::updateOrCreate(
                        [
                            'serial_number' => $serial_number,
                            'origin_group_code' => $origin_group_code,
                        ],
                        [
                            'process_code' => '1',
                            'model' => $model,
                            'quantity' => 1,
                            'status' => null,
                            'created_by' => Auth::id(),
                            'created_at' => date('Y-m-d H:i:s'),
                        ]
                    );

                    $stamp_inventory = StampInventory::updateOrCreate(
                        [
                            'serial_number' => $serial_number,
                            'origin_group_code' => $origin_group_code,
                        ],
                        [
                            'process_code' => '2',
                            'model' => $model,
                            'quantity' => 1,
                            'status' => null,
                            'created_by' => Auth::id(),
                            'created_at' => date('Y-m-d H:i:s'),
                        ]
                    );

                    $assembly_tag->serial_number = $serial_number;
                    $assembly_tag->model = $model;

                    $delete_assembly_tag = AssemblyTag::where('origin_group_code', '=', $origin_group_code)
                        ->where('remark', '<>', $tagName)
                        ->where('serial_number', '=', $serial_number)
                        ->first();

                    if ($delete_assembly_tag) {
                        $delete_assembly_tag->serial_number = null;
                        $delete_assembly_tag->model = null;
                        $delete_assembly_tag->save();
                    }

                    DB::transaction(function () use ($assembly_detail, $assembly_inventory, $log_process, $stamp_inventory, $assembly_tag) {
                        $assembly_detail->save();
                        $assembly_inventory->save();
                        $log_process->save();
                        $stamp_inventory->save();
                        $assembly_tag->save();
                    });
                    if (str_contains($model,'YCL450') || $model == 'YCL400AD') {
                        $this->printStamp($tagName, $serial_number, $model, 'print', 'Barcode Printer Cl-2', $cites, '');
                    } else {
                        $this->printStamp($tagName, $serial_number, $model, 'print', 'Printer Barcode CL', $cites, '');
                    }
                    // $this->printStamp($tagName, $serial_number, $model, 'print', 'MIS', $cites, '');
                }
            } else {
                $assembly_detail = AssemblyDetail::firstOrCreate(
                    ['serial_number' => $serial_number, 'origin_group_code' => $origin_group_code, 'location' => $location],
                    ['tag' => $assembly_tag->tag, 'model' => $model, 'location' => $location, 'operator_id' => $op_id, 'sedang_start_date' => $started_at, 'sedang_finish_date' => date('Y-m-d H:i:s'), 'created_by' => $op_id, 'is_send_log' => 0]
                );
                $assembly_detail->tag = $assembly_tag->tag;
                $assembly_detail->model = $model;
                $assembly_detail->location = $location;
                $assembly_detail->operator_id = $op_id;
                $assembly_detail->sedang_start_date = $started_at;
                $assembly_detail->sedang_finish_date = date('Y-m-d H:i:s');
                $assembly_detail->created_by = $op_id;

                $assembly_inventory = AssemblyInventory::firstOrCreate(
                    ['serial_number' => $serial_number, 'origin_group_code' => $origin_group_code],
                    ['tag' => $assembly_tag->tag, 'model' => $model, 'location' => $location, 'location_next' => 'perakitan-process', 'created_by' => $op_id]
                );
                $assembly_inventory->tag = $assembly_tag->tag;
                $assembly_inventory->model = $model;
                $assembly_inventory->location = $location;
                $assembly_inventory->created_by = $op_id;

                $logProcessGet = LogProcess::where('serial_number', $serial_number)->where('process_code', '1')->where('origin_group_code', $origin_group_code)->first();
                if ($logProcessGet) {
                    $sernum = substr($serial_number, 3);
                    $keypost = '';
                    if (in_array($serial_number, $this->sn_trial)) {
                        $keypost = '_NEW GAUGE JIG HTS';
                    }
                    $assembly_detail->trial = $logProcessGet->status_material . $keypost;
                    $assembly_inventory->trial = $logProcessGet->status_material;
                } else {
                    if (in_array($serial_number, $this->sn_trial)) {
                        $assembly_detail->trial = 'NEW GAUGE JIG HTS';
                    }
                }

                $status_material = '';

                $log_process_get = LogProcess::where('process_code', '1')->where('serial_number', $serial_number)->where('origin_group_code', $origin_group_code)->first();
                if ($log_process_get) {
                    $status_material = $log_process_get->status_material;
                }

                $log_process = LogProcess::updateOrCreate(
                    [
                        'process_code' => '2',
                        'serial_number' => $serial_number,
                        'origin_group_code' => $origin_group_code,
                    ],
                    [
                        'model' => $model,
                        'quantity' => 1,
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d H:i:s'),
                    ]
                );

                $stamp_inventory = StampInventory::updateOrCreate(
                    [
                        'serial_number' => $serial_number,
                        'origin_group_code' => $origin_group_code,
                    ],
                    [
                        'process_code' => '2',
                        'model' => $model,
                        'quantity' => 1,
                        'status' => null,
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d H:i:s'),
                    ]
                );

                $assembly_tag->serial_number = $serial_number;
                $assembly_tag->model = $model;

                $delete_assembly_tag = AssemblyTag::where('origin_group_code', '=', $origin_group_code)
                    ->where('remark', '<>', $tagName)
                    ->where('serial_number', '=', $serial_number)
                    ->first();

                if ($delete_assembly_tag) {
                    $delete_assembly_tag->serial_number = null;
                    $delete_assembly_tag->model = null;
                    $delete_assembly_tag->save();
                }

                DB::transaction(function () use ($assembly_detail, $assembly_inventory, $log_process, $stamp_inventory, $assembly_tag) {
                    $assembly_detail->save();
                    $assembly_inventory->save();
                    $log_process->save();
                    $stamp_inventory->save();
                    $assembly_tag->save();
                });
                $this->printStamp($tagName, $serial_number, $model, 'print', 'Barcode Printer Sax', $status_material, '');
                $this->printStamp($tagName, $serial_number, $model, 'print', 'Barcode Printer Sax', $status_material, '');
            }

            $response = array(
                'status' => true,
                'message' => 'Serial number berhasil diregistrasi.',
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

    public function stampFlute(Request $request)
    {
        $counter = PlcCounter::where('origin_group_code', '=', $request->get('origin_group_code'))
            ->first();
        $auth_id = Auth::id();

        $plc = new ActMLEasyIf(0);
        $datas = $plc->read_data('D50', 5);

        if ($counter->plc_counter == $datas[0]) {
            // if($counter->plc_counter == 36){
            $response = array(
                'status' => true,
                'status_code' => 'no_stamp',
            );
            return Response::json($response);
        }

        // try{
        $cek_serial = new AssemblySerial([
            'serial_number' => $request->get('serial'),
            'origin_group_code' => $request->get('origin_group_code'),
            'created_by' => $auth_id,
        ]);
        $cek_serial->save();
        // }
        // catch(QueryException $e){
        //     $error_code = $e->errorInfo[1];
        //     if($error_code == 1062){
        //         $response = array(
        //             'status' => false,
        //             'message' => "Serial number sudah pernah discan.",
        //         );
        //         return Response::json($response);
        //     }
        //     else{
        //         $response = array(
        //             'status' => false,
        //             'message' => $e->getMessage(),
        //         );
        //         return Response::json($response);
        //     }
        // }

        $taghex = $this->dec2hex($request->get('tagBody'));

        $tag = AssemblyTag::where('remark', '=', $request->get('tagName'))->where('tag', '=', $taghex)->first();
        $material = db::table('materials')->where('model', '=', $request->get('model'))
            ->where('xy', '=', 'SP')->first();

        $log = new AssemblyDetail([
            'tag' => $tag->tag,
            'serial_number' => $request->get('serial'),
            'model' => $request->get('model'),
            'location' => $request->get('location'),
            'operator_id' => $request->get('op_id'),
            'sedang_start_date' => $request->get('started_at'),
            'sedang_finish_date' => date('Y-m-d H:i:s'),
            'origin_group_code' => $request->get('origin_group_code'),
            'created_by' => $request->get('op_id'),
            'is_send_log' => 0,
            'status_material' => $request->get('trial'),
        ]);

        $sp = '';
        // if(count($material) > 0){
        //     $sp = 'SP';
        // }

        if ($request->get('location') != 'stampkd-process') {
            $inventory = AssemblyInventory::firstOrCreate(
                ['serial_number' => $request->get('serial'), 'origin_group_code' => $request->get('origin_group_code')],
                ['tag' => $tag->tag, 'model' => $request->get('model'), 'location' => $request->get('location'), 'location_next' => 'perakitan-process', 'remark' => $sp, 'created_by' => $request->get('op_id'), 'status_material' => $request->get('trial')]
            );
            $inventory->location = $request->get('location');

            // $logProcess = new LogProcess([
            //     'process_code' => 1,
            //     'serial_number' => $request->get('serial'),
            //     'model' => $request->get('model'),
            //     'manpower' => 1,
            //     'origin_group_code' => $request->get('origin_group_code'),
            //     'created_by' => 1,
            //     'remark' => 'FG'
            // ]);
            $sn_ready = DB::connection('ympimis_2')->table('assembly_serial_plots')->where('serial_number', $request->get('serial'))->where('origin_group_code', $request->get('origin_group_code'))->first();
            if ($sn_ready) {
                $update_sn_ready = DB::connection('ympimis_2')->table('assembly_serial_plots')->where('serial_number', $request->get('serial'))->where('origin_group_code', $request->get('origin_group_code'))->update([
                    'status' => 'Used',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $logProcess = LogProcess::updateOrCreate(
                [
                    'serial_number' => $request->get('serial'),
                    'origin_group_code' => $request->get('origin_group_code'),
                ],
                [
                    'process_code' => 1,
                    'serial_number' => $request->get('serial'),
                    'model' => $request->get('model'),
                    'manpower' => 1,
                    'origin_group_code' => $request->get('origin_group_code'),
                    'created_by' => 1,
                    'remark' => 'FG',
                ]
            );
        } else {
            // $logProcess = new LogProcess([
            //     'process_code' => 1,
            //     'serial_number' => $request->get('serial'),
            //     'model' => $request->get('model'),
            //     'manpower' => 1,
            //     'origin_group_code' => $request->get('origin_group_code'),
            //     'created_by' => 1,
            // ]);

            $sn_ready = DB::connection('ympimis_2')->table('assembly_serial_plots')->where('serial_number', $request->get('serial'))->where('origin_group_code', $request->get('origin_group_code'))->first();
            if ($sn_ready) {
                $update_sn_ready = DB::connection('ympimis_2')->table('assembly_serial_plots')->where('serial_number', $request->get('serial'))->where('origin_group_code', $request->get('origin_group_code'))->update([
                    'status' => 'KD',
                ]);
            }

            $logProcess = LogProcess::updateOrCreate(
                [
                    'serial_number' => $request->get('serial'),
                    'origin_group_code' => $request->get('origin_group_code'),
                ],
                [
                    'process_code' => 1,
                    'serial_number' => $request->get('serial'),
                    'model' => $request->get('model'),
                    'manpower' => 1,
                    'origin_group_code' => $request->get('origin_group_code'),
                    'created_by' => 1,
                ]
            );
        }

        // $stampInventory = new StampInventory([
        //     'process_code' => 1,
        //     'serial_number' => $request->get('serial'),
        //     'model' => $request->get('model'),
        //     'quantity' => 1,
        //     'origin_group_code' => $request->get('origin_group_code'),
        // ]);

        $stampInventory = StampInventory::updateOrCreate(
            [
                'serial_number' => $request->get('serial'),
                'origin_group_code' => $request->get('origin_group_code'),
            ],
            [
                'process_code' => 1,
                'serial_number' => $request->get('serial'),
                'model' => $request->get('model'),
                'quantity' => 1,
                'origin_group_code' => $request->get('origin_group_code'),
                'status' => null,
            ]
        );

        $tag->serial_number = $request->get('serial');
        $tag->model = $request->get('model');
        $serial = CodeGenerator::where('note', '=', $request->get('origin_group_code'))->first();
        $serial->index = $serial->index + 1;
        $counter->plc_counter = $datas[0];
        // $counter->plc_counter = 36;

        try {
            if ($request->get('location') != 'stampkd-process') {
                DB::transaction(function () use ($log, $inventory, $tag, $serial, $counter, $stampInventory, $logProcess) {
                    $inventory->save();
                    $log->save();
                    $tag->save();
                    $serial->save();
                    $counter->save();
                    $stampInventory->save();
                    $logProcess->save();
                });
            } else {
                DB::transaction(function () use ($log, $tag, $serial, $counter, $stampInventory, $logProcess) {
                    $log->save();
                    $tag->save();
                    $serial->save();
                    $counter->save();
                    $stampInventory->save();
                    $logProcess->save();
                });
            }
            $this->printStamp($request->get('tagName'), $request->get('serial'), $request->get('model'), 'print', 'SUPERMAN', $request->get('trial'), $sp);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'status_code' => 'stamp',
            'message' => 'Stamp berhasil dilakukan',
        );
        return Response::json($response);
    }

    public function stampClarinet(Request $request)
    {
        try {
            // $stamp_empty = StampInventory::where('origin_group_code','042')->orderby('created_at','desc')->where('process_code',1)->limit(1)->where('model',null)->first();
            // if (count($stamp_empty) > 0) {
            //     $stamp_empty->model = $request->get('model');
            //     $stamp_empty->save();
            // }

            $code_generator = DB::table('code_generators')->where('note', '=', '042')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index);
            $lastCounter = $code_generator->prefix . $number;

            if ($request->get('statuses') == 'start') {
                $log_empty = LogProcess::where('origin_group_code', '042')->orderby('created_at', 'desc')->where('process_code', 1)->limit(1)->where('model', '')->first();
                if ($log_empty != null) {
                    $log_empty->model = $request->get('model');
                    $log_empty->save();
                    if ($request->get('category') == 'FG') {
                        $stamp_inventory = StampInventory::updateOrCreate(
                            [
                                'serial_number' => $log_empty->serial_number,
                                'origin_group_code' => $log_empty->origin_group_code,
                            ],
                            [
                                'process_code' => $log_empty->process_code,
                                'model' => $request->get('model'),
                                'quantity' => 1,
                            ]
                        );
                        $stamp_inventory->save();
                    }
                    $response = array(
                        'status' => true,
                        'lastCounter' => $lastCounter,
                        'prod_result' => self::fetchClarinetResult(),
                    );
                    return Response::json($response);
                } else {
                    $response = array(
                        'status' => true,
                        'lastCounter' => $lastCounter,
                        'prod_result' => '',
                    );
                    return Response::json($response);
                }
            } else {
                $response = array(
                    'status' => true,
                    'lastCounter' => $lastCounter,
                    'prod_result' => self::fetchClarinetResult(),
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

    public function fetchClarinetResult()
    {
        $prod_result = LogProcess::where('origin_group_code', '042')->wheredate('created_at', '>=', date('Y-m-d', strtotime('- 1 days')))->orderby('created_at', 'desc')->get();
        return $prod_result;
    }

    public function adjustStampClarinet(Request $request)
    {
        try {
            if ($request->get('adjust') == 'minus') {
                $code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();
                $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index - 1);
                $lastCounter = $code_generator->prefix . $number;
                $code_generator->index = $code_generator->index - 1;
                $code_generator->save();
                $message = 'Stamp Adjusted Minus';
            } else if ($request->get('adjust') == 'plus') {
                $code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();
                $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
                $lastCounter = $code_generator->prefix . $number;
                $code_generator->index = $code_generator->index + 1;
                $code_generator->save();
                $message = 'Stamp Adjusted Plus';
            } else if ($request->get('adjust') == 'adjust') {
                $code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();

                $code_generator->index = $request->get('lastIndex');
                $code_generator->prefix = $request->get('prefix');
                $number = sprintf("%'.0" . $code_generator->length . "d", $request->get('lastIndex'));
                $lastCounter = $code_generator->prefix . $number;
                $code_generator->save();
                $message = 'Stamp Adjusted';
            }
            $response = array(
                'status' => true,
                'lastCounter' => $lastCounter,
                'message' => $message,
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

    public function fetchStampClarinet(Request $request)
    {
        try {
            $code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();

            $prefix = $code_generator->prefix;
            $lastIndex = $code_generator->index;

            $response = array(
                'status' => true,
                'prefix' => $prefix,
                'lastIndex' => $lastIndex,
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

    public function editStampClarinet(Request $request)
    {
        $log_process = LogProcess::find($request->get('id'));

        $response = array(
            'status' => true,
            'logProcess' => $log_process,
        );
        return Response::json($response);
    }

    //FLUTE

    public function scanTagStamp(Request $request)
    {
        $taghex = $this->dec2hex($request->get('tag'));

        $tag = AssemblyTag::whereNull('serial_number')
            ->where('origin_group_code', '=', $request->get('origin_group_code'))
            ->where('tag', '=', $taghex)
            ->first();

        $started_at = date('Y-m-d H:i:s');

        if ($tag == null) {
            $response = array(
                'status' => false,
                'message' => 'Tag tidak ditemukan / Tag masih aktif',
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'message' => 'Tag berhasil ditemukan',
            'tag' => $tag,
            'started_at' => $started_at,
        );
        return Response::json($response);
    }

    public function fetchSerialNumber(Request $request)
    {
        $serial = db::table('code_generators')->where('note', '=', $request->get('origin_group_code'))->first();

        if (is_numeric($serial->prefix)) {
            $number = sprintf("%'.0".$serial->length."d", $serial->index);
            $number2 = sprintf("%'.0".$serial->length."d", $serial->index + 1);

            $lastCounter = $serial->prefix . $number;
            $nextCounter = $serial->prefix . $number2;

            $cek_serial = null;

            if ($request->get('location') == 'stamp-process' && $request->get('origin_group_code') == '041') {
                $cek_serial = DB::table('log_processes')->where('serial_number', $nextCounter)->where('origin_group_code', $request->get('origin_group_code'))->first();
            }
        }else{
            $number = sprintf("%'.05d", $serial->index);
            $number2 = sprintf("%'.05d", $serial->index + 1);

            $lastCounter = $serial->prefix . $number;
            $nextCounter = $serial->prefix . $number2;

            $cek_serial = null;

            if ($request->get('location') == 'stamp-process' && $request->get('origin_group_code') == '041') {
                $cek_serial = DB::table('log_processes')->where('serial_number', $nextCounter)->where('origin_group_code', $request->get('origin_group_code'))->first();
            }
        }

        $response = array(
            'status' => true,
            'lastCounter' => $lastCounter,
            'nextCounter' => $nextCounter,
            'cek_serial' => $cek_serial,
        );
        return Response::json($response);
    }

    public function fetchStampResult(Request $request)
    {
        $now = date('Y-m-d');
        $first = date('Y-m-d', strtotime("-3 days"));
        // $date = '2020-06-15';
        $logs_kensa = null;
        $logs_kensa_detail = null;
        $logs_kensa_process = null;
        $logs_kensa_process_detail = null;
        $emp = null;

        if ($request->get('origin_group_code') == '041') {
            $logs = AssemblyDetail::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'assembly_details.operator_id')
                ->where('assembly_details.origin_group_code', '=', $request->get('origin_group_code'))
                ->where(db::raw('date(assembly_details.created_at)'), '>=', $first)
                ->where('assembly_details.location', '=', 'stamp-process')
                ->whereOr('assembly_details.location', '=', 'stampkd-process')
                ->select('assembly_details.serial_number', 'assembly_details.model', db::raw('if(location = "stamp-process", "FG", "KD") as category'), 'employee_syncs.name', 'assembly_details.created_at', 'assembly_details.id as id_details', db::raw('DATE_FORMAT(assembly_details.created_at, "%Y-%m-%d") as now'))
                ->orderBy('assembly_details.created_at', 'desc')
                ->get();

            $logsall = AssemblyInventory::where('assembly_inventories.origin_group_code', '=', $request->get('origin_group_code'))
                ->get();
        }

        if ($request->get('origin_group_code') == '043') {
            if ($request->get('location') == 'packing') {
                $logs = AssemblyLog::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'assembly_logs.operator_id')
                    ->where('assembly_logs.origin_group_code', '=', $request->get('origin_group_code'))
                    ->where(db::raw('date(assembly_logs.created_at)'), '>=', $now)
                    ->where('assembly_logs.location', '=', $request->get('location'))
                    ->join('materials', 'materials.material_description', 'assembly_logs.model')
                    ->select('assembly_logs.serial_number', 'assembly_logs.model', 'assembly_logs.created_at', 'employee_syncs.name', 'assembly_logs.status_material', db::raw('date(assembly_logs.created_at) as now'), 'assembly_logs.location', 'materials.material_number')
                    ->orderBy('assembly_logs.created_at', 'desc')
                    ->get();
                $logsall = array();
            } else {
                $logs = AssemblyDetail::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'assembly_details.operator_id')
                    ->where('assembly_details.origin_group_code', '=', $request->get('origin_group_code'))
                    ->where(db::raw('date(assembly_details.created_at)'), '>=', $first)
                    ->where('assembly_details.location', '=', $request->get('location'))
                    ->select('assembly_details.serial_number', 'assembly_details.model', db::raw('if(location = "stamp-process", "FG", "KD") as category'), 'employee_syncs.name', 'assembly_details.created_at', 'assembly_details.id as id_details', db::raw('DATE_FORMAT(assembly_details.created_at, "%Y-%m-%d") as now'))
                    ->orderBy('assembly_details.created_at', 'desc')
                    ->get();

                // $logs2 = AssemblyLog::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'assembly_logs.operator_id')
                // ->where('assembly_logs.origin_group_code', '=', $request->get('origin_group_code'))
                // ->where(db::raw('date(assembly_logs.created_at)'), '>=', $now)
                // ->where('assembly_logs.location', '=', $request->get('location'))
                // ->select('assembly_logs.serial_number', 'assembly_logs.model', db::raw('if(location = "stamp-process", "FG", "KD") as category'), 'employee_syncs.name', 'assembly_logs.created_at', 'assembly_logs.id as id_details', db::raw('DATE_FORMAT(assembly_logs.created_at, "%Y-%m-%d") as now'))
                // ->orderBy('assembly_logs.created_at', 'desc')
                // ->get();

                $logsall = AssemblyInventory::where('assembly_inventories.origin_group_code', '=', $request->get('origin_group_code'))
                    ->get();
            }
        }
        if ($request->get('origin_group_code') == '042') {
            if ($request->get('location') == 'packing') {
                $logs = AssemblyLog::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'assembly_logs.operator_id')
                    ->where('assembly_logs.origin_group_code', '=', $request->get('origin_group_code'))
                    ->where(db::raw('date(assembly_logs.created_at)'), '>=', $now)
                    ->where('assembly_logs.location', '=', $request->get('location'))
                    ->join('materials', 'materials.material_description', 'assembly_logs.model')
                    ->select('assembly_logs.serial_number', 'assembly_logs.model', 'assembly_logs.created_at', 'employee_syncs.name', 'assembly_logs.status_material', db::raw('date(assembly_logs.created_at) as now'), 'assembly_logs.location', 'materials.material_number')
                    ->orderBy('assembly_logs.created_at', 'desc')
                    ->get();

                $logs_kensa = DB::SELECT("SELECT
					a.operator_id,
					a.model,
					count( DISTINCT(a.serial_number) ) AS count
				FROM
					(
					SELECT
						operator_id,
						model,
						serial_number
					FROM
						assembly_details
					WHERE
						origin_group_code = '" . $request->get('origin_group_code') . "'
						AND DATE( sedang_start_date ) = '" . $now . "'
						AND location = 'qa-kensa'
						AND serial_number NOT LIKE '%_U%'
					GROUP BY
						operator_id,
						model,
						serial_number UNION ALL
					SELECT
						operator_id,
						model,
						serial_number
					FROM
						assembly_logs
					WHERE
						origin_group_code = '" . $request->get('origin_group_code') . "'
						AND DATE( sedang_start_date ) = '" . $now . "'
						AND location = 'qa-kensa'
						AND serial_number NOT LIKE '%_U%'
					GROUP BY
						operator_id,
						model,
						serial_number
					) a
				GROUP BY
					a.operator_id,
					a.model");

                // $logs_kensa_detail = DB::SELECT("SELECT DISTINCT
                //     ( a.serial_number ) AS serial_number,
                //     operator_id,
                //     employee_syncs.`name`,
                //     a.model,
                //     GROUP_CONCAT( a.sedang_start_date ) AS sedang_start_date,
                //     GROUP_CONCAT( a.sedang_finish_date ) sedang_finish_date,
                //     a.loc
                //     FROM
                //     (
                //     SELECT DISTINCT
                //     ( serial_number ),
                //     operator_id,
                //     model,
                //     sedang_start_date,
                //     sedang_finish_date,
                //     'wip' AS loc
                //     FROM
                //     assembly_details
                //     WHERE
                //     origin_group_code = '".$request->get('origin_group_code')."'
                //     AND DATE( sedang_start_date ) = '".$now."'
                //     AND location = 'qa-kensa'
                //     AND serial_number NOT LIKE '%_U%'
                //     GROUP BY
                //     model,
                //     operator_id,
                //     serial_number,
                //     sedang_start_date,
                //     sedang_finish_date,
                //     loc UNION ALL
                //     SELECT DISTINCT
                //     ( serial_number ),
                //     operator_id,
                //     model,
                //     sedang_start_date,
                //     sedang_finish_date,
                //     'log' AS loc
                //     FROM
                //     assembly_logs
                //     WHERE
                //     origin_group_code = '".$request->get('origin_group_code')."'
                //     AND DATE( sedang_start_date ) = '".$now."'
                //     AND location = 'qa-kensa'
                //     AND serial_number NOT LIKE '%_U%'
                //     GROUP BY
                //     model,
                //     operator_id,
                //     serial_number,
                //     sedang_start_date,
                //     sedang_finish_date,
                //     loc
                //     ) AS a
                //     JOIN employee_syncs ON employee_syncs.employee_id = a.operator_id
                //     GROUP BY
                //     employee_syncs.`name`,
                //     a.model,
                //     a.operator_id,
                //     a.loc,
                //     a.serial_number
                //     ORDER BY
                //     a.operator_id");

                $logs_kensa_process = DB::SELECT("SELECT
					a.operator_id,
					a.model,
					count( DISTINCT(a.serial_number) ) AS count
				FROM
					(
					SELECT
						operator_id,
						model,
						serial_number
					FROM
						assembly_details
					WHERE
						origin_group_code = '" . $request->get('origin_group_code') . "'
						AND DATE( sedang_start_date ) = '" . $now . "'
						AND location = 'kensa-process'
						AND serial_number NOT LIKE '%_U%'
					GROUP BY
						operator_id,
						model,
						serial_number UNION ALL
					SELECT
						operator_id,
						model,
						serial_number
					FROM
						assembly_logs
					WHERE
						origin_group_code = '" . $request->get('origin_group_code') . "'
						AND DATE( sedang_start_date ) = '" . $now . "'
						AND location = 'kensa-process'
						AND serial_number NOT LIKE '%_U%'
					GROUP BY
						operator_id,
						model,
						serial_number
					) a
				GROUP BY
					a.operator_id,
					a.model");

                $emp = EmployeeSync::where('end_date', null)->get();

                // $logs_kensa_process_detail = DB::SELECT("SELECT DISTINCT
                //     ( a.serial_number ) AS serial_number,
                //     operator_id,
                //     employee_syncs.`name`,
                //     a.model,
                //     GROUP_CONCAT( a.sedang_start_date ) AS sedang_start_date,
                //     GROUP_CONCAT( a.sedang_finish_date ) sedang_finish_date,
                //     a.loc
                //     FROM
                //     (
                //     SELECT DISTINCT
                //     ( serial_number ),
                //     operator_id,
                //     model,
                //     sedang_start_date,
                //     sedang_finish_date,
                //     'wip' AS loc
                //     FROM
                //     assembly_details
                //     WHERE
                //     origin_group_code = '".$request->get('origin_group_code')."'
                //     AND DATE( sedang_start_date ) = '".$now."'
                //     AND location = 'kensa-process'
                //     AND serial_number NOT LIKE '%_U%'
                //     GROUP BY
                //     model,
                //     operator_id,
                //     serial_number,
                //     sedang_start_date,
                //     sedang_finish_date,
                //     loc UNION ALL
                //     SELECT DISTINCT
                //     ( serial_number ),
                //     operator_id,
                //     model,
                //     sedang_start_date,
                //     sedang_finish_date,
                //     'log' AS loc
                //     FROM
                //     assembly_logs
                //     WHERE
                //     origin_group_code = '".$request->get('origin_group_code')."'
                //     AND DATE( sedang_start_date ) = '".$now."'
                //     AND location = 'kensa-process'
                //     AND serial_number NOT LIKE '%_U%'
                //     GROUP BY
                //     model,
                //     operator_id,
                //     serial_number,
                //     sedang_start_date,
                //     sedang_finish_date,
                //     loc
                //     ) AS a
                //     JOIN employee_syncs ON employee_syncs.employee_id = a.operator_id
                //     GROUP BY
                //     employee_syncs.`name`,
                //     a.model,
                //     a.operator_id,
                //     a.loc,
                //     a.serial_number
                //     ORDER BY
                //     a.operator_id");

                $logsall = array();
            } else {
                $logs = AssemblyDetail::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'assembly_details.operator_id')
                    ->where('assembly_details.origin_group_code', '=', $request->get('origin_group_code'))
                    ->where(db::raw('date(assembly_details.created_at)'), '>=', $first)
                    ->where('assembly_details.location', '=', $request->get('location'))
                    ->select('assembly_details.serial_number', 'assembly_details.model', db::raw('if(location = "stamp-process", "FG", "KD") as category'), 'employee_syncs.name', 'assembly_details.created_at', 'assembly_details.id as id_details', db::raw('DATE_FORMAT(assembly_details.created_at, "%Y-%m-%d") as now'))
                    ->orderBy('assembly_details.created_at', 'desc')
                    ->get();

                // $logs2 = AssemblyLog::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'assembly_logs.operator_id')
                // ->where('assembly_logs.origin_group_code', '=', $request->get('origin_group_code'))
                // ->where(db::raw('date(assembly_logs.created_at)'), '>=', $now)
                // ->where('assembly_logs.location', '=', $request->get('location'))
                // ->select('assembly_logs.serial_number', 'assembly_logs.model', db::raw('if(location = "stamp-process", "FG", "KD") as category'), 'employee_syncs.name', 'assembly_logs.created_at', 'assembly_logs.id as id_details', db::raw('DATE_FORMAT(assembly_logs.created_at, "%Y-%m-%d") as now'))
                // ->orderBy('assembly_logs.created_at', 'desc')
                // ->get();

                $logsall = AssemblyInventory::where('assembly_inventories.origin_group_code', '=', $request->get('origin_group_code'))
                    ->get();
            }
        }

        $model = DB::SELECT("SELECT DISTINCT
				( model )
			FROM
				materials
			WHERE
				origin_group_code = '" . $request->get('origin_group_code') . "'
				AND category = 'FG'");

        $response = array(
            'status' => true,
            'logs' => $logs,
            'logsall' => $logsall,
            'emp' => $emp,
            'logs_kensa' => $logs_kensa,
            'logs_kensa_process' => $logs_kensa_process,
            'model' => $model,
            'logs_kensa_detail' => $logs_kensa_detail,
            'logs_kensa_process_detail' => $logs_kensa_process_detail,
            'now' => $now,
        );
        return Response::json($response);

    }

    public function printStamp($tag, $serial_number, $model, $category, $printer_name, $trial, $sp)
    {
        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        if ($category == 'print') {
            $trials = 0;
            if ($trial == 'TRIAL') {
                $printer->setUnderline(true);
                $printer->setEmphasis(true);
                if (preg_match('/YAS/i', $model) || preg_match('/YTS/i', $model)) {
                    // $printer->setReverseColors(true);
                    $printer->setTextSize(6, 2);
                    $printer->text('TRIAL');
                    $printer->feed(1);
                    $trials = 1;
                } else {
                    $printer->setReverseColors(true);
                    $printer->text('NEW SPEC');
                }
                $printer->setEmphasis(false);
                $printer->setUnderline(false);
                $printer->feed(1);
            }
            if ($trials == 0) {
                // $sernum = substr($serial_number, 3);
                // if (preg_match('/YAS/i', $model)) {
                if (in_array($serial_number, $this->sn_trial)) {
                    $printer->setReverseColors(true);
                    $printer->setTextSize(2, 1);
                    $printer->text('---NEW GAUGE JIG HTS---');
                    $printer->feed(1);
                }
                // }
            }
            $printer->setReverseColors(false);
            // if ($sp == 'SP') {
            //     $printer->setEmphasis(true);
            //     $printer->text('                                            SP');
            //     $printer->setEmphasis(false);
            //     $printer->feed(1);
            // }
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(3, 1);
            if ($sp == 'SP') {
                $printer->text($serial_number . ' (' . $sp . ")\n");
            } else {
                $printer->text($serial_number . "\n");
            }
            if (str_contains($model,'YCL450') || $model == 'YCL400AD') {
                $printer->setTextSize(2, 1);
                $printer->feed(1);
                $printer->text('CITES = ' . explode('/', $trial)[0] . '/' . explode('/', $trial)[1] . '/' . explode('/', $trial)[2] . '/' . explode('/', $trial)[3] . "\n");
                $printer->text('DAISHA = ' . explode('/', $trial)[4] . "\n");
            }
            if (strlen($model) > 7) {
                $printer->setTextSize(2, 1);
            }
            $printer->feed(1);
            $printer->text($tag . ' ' . $model . "\n");
            if (preg_match('/YAS/i', $model) || preg_match('/YTS/i', $model)) {
                $printer->setBarcodeWidth(2);
                $printer->setBarcodeHeight(64);
                $printer->barcode($serial_number, Printer::BARCODE_CODE39);
                $printer->feed(1);
                $printer->setTextSize(1, 1);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text('------------------------------------------------');
                $printer->feed(1);
                $printer->text('|Made In| Body | Bell |Side Cvr|  F-4  |  J-3  |');
                $printer->feed(1);
                $printer->text('|       |      |      |        |       |       |');
                $printer->feed(1);
                $printer->text('------------------------------------------------');
            }
            $printer->feed(1);
            $printer->setTextSize(1, 1);
            $printer->text(date("d-M-Y H:i:s") . "\n");
            $printer->cut();
            $printer->close();
        }
        if ($category == 'reprint') {
            $trials = 0;
            if ($trial == 'TRIAL') {
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->setUnderline(true);
                $printer->setEmphasis(true);
                if (preg_match('/YAS/i', $model) || preg_match('/YTS/i', $model)) {
                    $printer->setTextSize(6, 2);
                    // $printer->setReverseColors(true);
                    $printer->text('TRIAL');
                    $printer->feed(1);
                    $trials = 1;
                } else {
                    $printer->setReverseColors(true);
                    $printer->text('NEW SPEC');
                }
                $printer->setEmphasis(false);
                $printer->setUnderline(false);
                $printer->feed(1);
            }
            if ($trials == 0) {
                // $sernum = substr($serial_number, 3);
                // if (preg_match('/YAS/i', $model)) {
                if (in_array($serial_number, $this->sn_trial)) {
                    $printer->setReverseColors(true);
                    $printer->setTextSize(2, 1);
                    $printer->text('---NEW GAUGE JIG HTS---');
                    $printer->feed(1);
                }
                // }
            }
            $printer->setReverseColors(false);
            // if ($sp == 'SP') {
            //     $printer->setEmphasis(true);
            //     $printer->text('                                            SP');
            //     $printer->setEmphasis(false);
            //     $printer->feed(1);
            // }
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(3, 1);
            if ($sp == 'SP') {
                $printer->text($serial_number . ' (' . $sp . ")\n");
            } else {
                $printer->text($serial_number . "\n");
            }
            if (str_contains($model,'YCL450') || $model == 'YCL400AD') {
                $printer->setTextSize(2, 1);
                $printer->feed(1);
                $printer->text('CITES = ' . explode('/', $trial)[0] . '/' . explode('/', $trial)[1] . '/' . explode('/', $trial)[2] . '/' . explode('/', $trial)[3] . "\n");
                $printer->text('DAISHA = ' . explode('/', $trial)[4] . "\n");
            }
            $printer->feed(1);
            $printer->text($tag . ' ' . $model . "\n");
            if (strlen($model) > 7) {
                $printer->setTextSize(2, 1);
            }
            if (preg_match('/YAS/i', $model) || preg_match('/YTS/i', $model)) {
                $printer->setBarcodeWidth(2);
                $printer->setBarcodeHeight(64);
                $printer->barcode($serial_number, Printer::BARCODE_CODE39);
                $printer->feed(1);
                $printer->setTextSize(1, 1);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text('------------------------------------------------');
                $printer->feed(1);
                $printer->text('|Made In| Body | Bell |Side Cvr|  F-4  |  J-3  |');
                $printer->feed(1);
                $printer->text('|       |      |      |        |       |       |');
                $printer->feed(1);
                $printer->text('------------------------------------------------');
            }
            $printer->feed(1);
            $printer->setTextSize(1, 1);
            $printer->text(date("d-M-Y H:i:s") . "(Reprint)" . "\n");
            $printer->cut();
            $printer->close();
        }
    }

    public function editStamp(Request $request)
    {
        try {
            $details = AssemblyDetail::find($request->get('id'));

            $response = array(
                'status' => true,
                'details' => $details,
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => 'Failed Get Data',
            );
            return Response::json($response);
        }
    }

    public function destroyStamp(Request $request)
    {
        $details = AssemblyDetail::find($request->get('id'));

        $inventories = AssemblyInventory::where('serial_number', $request->get('serial_number'))->where('model', $request->get('model'))->where('location', $details->location)->where('origin_group_code', $request->get('origin_group_code'))->first();

        $serials = AssemblySerial::where('serial_number', $request->get('serial_number'))->where('origin_group_code', $request->get('origin_group_code'))->first();

        $tag = AssemblyTag::where('serial_number', $request->get('serial_number'))->where('model', $request->get('model'))->where('origin_group_code', $request->get('origin_group_code'))->first();
        $tag->serial_number = null;
        $tag->model = null;

        $log_process = LogProcess::where('log_processes.serial_number', '=', $request->get('serial_number'))
            ->where('log_processes.model', '=', $request->get('model'))
            ->where('origin_group_code', $request->get('origin_group_code'));

        $stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $request->get('serial_number'))
            ->where('stamp_inventories.model', '=', $request->get('model'))
            ->where('origin_group_code', $request->get('origin_group_code'));

        try {
            $inventories->forceDelete();
            $serials->forceDelete();
            $tag->save();
            $details->forceDelete();
            $log_process->forceDelete();
            $stamp_inventory->forceDelete();

            $response = array(
                'status' => true,
                'message' => 'Delete Serial Number Berhasil',
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => 'Failed Delete Data',
            );
            return Response::json($response);
        }
    }

    public function updateStamp(Request $request)
    {
        $details = AssemblyDetail::find($request->get('id'));
        $details->model = $request->get('model');

        $inventories = AssemblyInventory::where('serial_number', $request->get('serial_number'))->where('model', $request->get('model_asli'))->where('location', $details->location)->where('origin_group_code', $request->get('origin_group_code'))->first();
        $inventories->model = $request->get('model');

        $tag = AssemblyTag::where('serial_number', $request->get('serial_number'))->where('model', $request->get('model_asli'))->where('origin_group_code', $request->get('origin_group_code'))->first();
        $tag->model = $request->get('model');

        $log_process = LogProcess::where('log_processes.serial_number', '=', $request->get('serial_number'))
            ->where('log_processes.model', '=', $request->get('model_asli'))
            ->where('origin_group_code', $request->get('origin_group_code'))->first();
        $log_process->model = $request->get('model');

        $stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $request->get('serial_number'))
            ->where('stamp_inventories.model', '=', $request->get('model_asli'))
            ->where('origin_group_code', $request->get('origin_group_code'))->first();
        $stamp_inventory->model = $request->get('model');

        try {
            $inventories->save();
            $tag->save();
            $details->save();
            $log_process->save();
            $stamp_inventory->save();

            $response = array(
                'status' => true,
                'message' => 'Update Model Berhasil',
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => 'Failed Delete Data',
            );
            return Response::json($response);
        }
    }

    public function adjustStamp(Request $request)
    {
        try {
            $code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();

            $prefix = $code_generator->prefix;
            $lastIndex = $code_generator->index;

            $response = array(
                'status' => true,
                'prefix' => $prefix,
                'lastIndex' => $lastIndex,
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

    public function adjustStampUpdate(Request $request)
    {
        $code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();

        $code_generator->index = $request->get('lastIndex');
        if ($request->get('prefix') == '') {
            $code_generator->prefix = "";
        }else{
            $code_generator->prefix = $request->get('prefix');
        }
        $code_generator->save();

        $response = array(
            'status' => true,
            'message' => 'Serial number adjustment success',
        );
        return Response::json($response);
    }

    public function reprintStamp(Request $request)
    {
        try {
            $inventories = AssemblyInventory::join('assembly_tags', 'assembly_tags.serial_number', 'assembly_inventories.serial_number')
                ->where('assembly_inventories.serial_number', $request->get('serial_number'))
                ->where('assembly_inventories.origin_group_code', $request->get('origin_group_code'))
                ->select(
                    'assembly_inventories.model',
                    'assembly_tags.serial_number',
                    'assembly_tags.remark',
                    'assembly_inventories.remark as sp',
                    'assembly_inventories.status_material',
                    'assembly_inventories.trial'
                )
                ->first();

            $printer_name = 'SUPERMAN';

            if ($request->get('origin_group_code') == '042') {
                if (str_contains($inventories->model,'YCL450') || $inventories->model == 'YCL400AD') {
                    $printer_name = 'Barcode Printer Cl-2';
                } else {
                    $printer_name = 'Printer Barcode CL';
                }
                // $printer_name = 'MIS';
            }
            if ($request->get('origin_group_code') == '043') {
                $printer_name = 'Barcode Printer Sax';
            }

            $status_material = '';

            if ($request->get('origin_group_code') == '043') {
                $status_material = $inventories->trial;
            } else {
                $status_material = $inventories->status_material;
            }

            $this->printStamp($inventories->remark, $request->get('serial_number'), $inventories->model, 'reprint', $printer_name, $status_material, $inventories->sp);

            $response = array(
                'status' => true,
                'message' => 'Reprint Success',
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

    public function indexFlutePrintLabel()
    {
        $title = 'Flute Print Packing Labels';
        $title_jp = 'FLラベル印刷';
        $otorisasi_packing = Employee::select('tag')->whereIn('employee_id', ['PI9903006', 'PI0303002', 'PI9907003', 'PI9902018', 'PI1910002', 'PI2009022'])->get();
        return view('processes.assembly.flute.print_label', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'otorisasi' => $otorisasi_packing,
        ))->with('page', 'Assembly FL')->with('head', 'Assembly Process');
    }

    public function indexFlutePrintLabelBackup()
    {
        $title = 'Flute Print Packing Labels';
        $title_jp = '(Flute Print Packing Labels)';
        return view('processes.assembly.flute.print_label_backup', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Assembly FL')->with('head', 'Assembly Process');
    }

    public function indexAssemblyBoard($location)
    {
        $loc_code = explode('-', $location);
        $process = $loc_code[0];
        $loc_spec = $loc_code[1];

        if ($location == 'perakitan-process') {
            $title = 'Perakitan Process Flute';
            $title_jp = 'FL組立';
        }
        if ($location == 'kariawase-process') {
            $title = 'Kariawase Process Flute';
            $title_jp = 'FL仮合わせ';
        }
        if ($location == 'tanpoawase-process') {
            $title = 'Tanpo Awase Process Flute';
            $title_jp = 'FLタンポ合わせ';
        }
        if ($location == 'perakitanawal-kensa,tanpoawase-process') {
            $title = 'Perakitan Ulang & Tanpo Awase Process Flute';
            $title_jp = 'フルートの再組立・タンポ合わせ';
        }
        if ($location == 'kariawase-fungsi,kariawase-visual,kariawase-repair,tanpoire-process') {
            $title = 'Kariawase & Tanpoire Process Flute';
            $title_jp = 'フルートの仮合わせ・タンポ入れ';
        }
        if ($location == 'tanpoawase-kensa,tanpoawase-fungsi,repair-process-1,repair-process-2') {
            $title = 'Tanpoawase Kensa & Fungsi Flute';
            $title_jp = 'フルートのタンポ合わせ・機能の検査';
        }
        if ($location == 'seasoning-process') {
            $title = 'Seasoning Process Flute';
            $title_jp = 'フルートのシーズニング';
        }
        if ($location == 'kango-process') {
            $title = 'Kango Process Flute';
            $title_jp = 'フルートの嵌合';
        }
        if ($location == 'kango-fungsi,renraku-process') {
            $title = 'Renraku Process Flute';
            $title_jp = 'フルートの連絡';
        }
        if ($location == 'kango-kensa,renraku-fungsi') {
            $title = 'Cek Fungsi Akhir Flute';
            $title_jp = 'フルートの最終機能検査';
        }
        if ($location == 'renraku-repair,qa-fungsi') {
            $title = 'Cek Fungsi QA Flute';
            $title_jp = 'フルートののQA機能検査';
        }
        if ($location == 'fukiage1-process,repair-ringan') {
            $title = 'Fukiage 1 Flute';
            $title_jp = 'フルートの拭き上げ ①';
        }
        if ($location == 'fukiage1-visual,qa-visual1,fukiage2-process,qa-visual2,pakcing') {
            $title = 'QA Visual, Fukiage 2, & Packing Flute';
            $title_jp = 'フルートのQA外観検査、拭き上げ②、梱包';
        }

        return view('processes.assembly.flute.display.board', array(
            'loc' => $location,
            'loc2' => $location,
            'process' => $process,
            'loc_spec' => $loc_spec,
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Assembly FL')->with('head', 'Assembly Process')->with('location', $location);
    }

    public function indexAssemblyClarinetBoard($location)
    {

        if ($location == '1') {
            $title = 'Assembly Clarinet Line 1';
            $title_jp = '';
            $view = 'processes.assembly.clarinet.display.board_1';
        }
        if ($location == '2') {
            $title = 'Assembly Clarinet Line 2';
            $title_jp = '';
            $view = 'processes.assembly.clarinet.display.board_1';
        }
        if ($location == '3') {
            $title = 'Assembly Clarinet Line 3';
            $title_jp = '';
            $view = 'processes.assembly.clarinet.display.board_1';
        }

        return view($view, array(
            'loc' => $location,
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Assembly Clarinet')->with('head', 'Assembly Process')->with('location', $location);
    }

    public function indexAssemblySaxophoneBoard($location)
    {
        if ($location == 'preparation-process') {
            $title = 'Preparation Process';
            $title_jp = '';
        }

        if ($location == '1') {
            $title = 'Line 1';
            $title_jp = '';
        }

        if ($location == '2') {
            $title = 'Line 2';
            $title_jp = '';
        }

        if ($location == '3') {
            $title = 'Line 3';
            $title_jp = '';
        }

        if ($location == '4') {
            $title = 'Line 4';
            $title_jp = '';
        }

        if ($location == '5') {
            $title = 'Line 5';
            $title_jp = '';
        }

        return view('processes.assembly.saxophone.display.board', array(
            'loc' => $location,
            'loc2' => $location,
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Assembly Saxophone')->with('head', 'Assembly Process')->with('location', $location);
    }

    public function label_carb_fl($id)
    {

        $date = db::select("SELECT DATE_FORMAT( sedang_start_date, '%m-%Y' ) AS tgl FROM assembly_logs
			WHERE location = 'packing'
			AND origin_group_code = '041'
			AND serial_number = '" . $id . "'");

        return view('processes.assembly.flute.label.label_carb_new', array(
            'date' => $date,
        ))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
    }

    public function labelCarbCl($id)
    {

        $date = db::select("SELECT DATE_FORMAT( sedang_start_date, '%m-%Y' ) AS tgl FROM assembly_logs
			WHERE location = 'packing'
			AND origin_group_code = '042'
			AND serial_number = '" . $id . "'");

        return view('processes.assembly.clarinet.label.label_carb_new', array(
            'date' => $date,
        ))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
    }

    public function labelDeskripsiFl($id, $remark)
    {
        $barcode = DB::select("SELECT serial_number, model FROM assembly_logs
			WHERE origin_group_code = '041'
			AND location = 'packing'
			AND serial_number = '" . $id . "'
			ORDER BY created_at DESC
			LIMIT 1");

        return view('processes.assembly.flute.label.label_desc', array(
            'barcode' => $barcode,
            'sn' => $id,
            'remark' => $remark,
        ))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
    }

    public function labelDeskripsiCl($id, $remark)
    {
        $barcode = DB::select("SELECT serial_number, model FROM assembly_logs
			WHERE origin_group_code = '042'
			AND location = 'packing'
			AND serial_number = '" . $id . "'
			ORDER BY created_at DESC
			LIMIT 1");

        return view('processes.assembly.clarinet.label.label_desc', array(
            'barcode' => $barcode,
            'sn' => $id,
            'remark' => $remark,
        ))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
    }

    public function labelKecil2Fl($id, $remark)
    {
        $barcode = DB::select("SELECT week_date, date_code from weekly_calendars
			WHERE week_date = (SELECT DATE_FORMAT(created_at,'%Y-%m-%d') from assembly_logs
			WHERE serial_number = '" . $id . "'
			AND location = 'packing'
			AND origin_group_code = '041'
			ORDER BY created_at DESC
			LIMIT 1)");

        $des = DB::select("SELECT serial_number, model FROM assembly_logs
			WHERE origin_group_code = '041'
			AND location = 'packing'
			AND serial_number = '" . $id . "'
			ORDER BY created_at DESC
			LIMIT 1");

        return view('processes.assembly.flute.label.label_kecil2', array(
            'barcode' => $barcode,
            'sn' => $id,
            'remark' => $remark,
            'des' => $des,
        ))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
    }

    public function labelKecil2Cl($id, $remark)
    {
        $barcode = DB::select("SELECT week_date, date_code from weekly_calendars
			WHERE week_date = (SELECT DATE_FORMAT(created_at,'%Y-%m-%d') from assembly_logs
			WHERE serial_number = '" . $id . "'
			AND location = 'packing'
			AND origin_group_code = '042'
			ORDER BY created_at DESC
			LIMIT 1)");

        $des = DB::select("SELECT serial_number, model FROM assembly_logs
			WHERE origin_group_code = '042'
			AND location = 'packing'
			AND serial_number = '" . $id . "'
			ORDER BY created_at DESC
			LIMIT 1");

        return view('processes.assembly.clarinet.label.label_kecil2', array(
            'barcode' => $barcode,
            'sn' => $id,
            'remark' => $remark,
            'des' => $des,
        ))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
    }

    public function labelDesSx($id)
    {
        $barcode = DB::select("SELECT
			model
			FROM
			assembly_logs
			WHERE
			location = 'packing'
			AND serial_number = '" . $id . "'
			AND origin_group_code = '043'");

        return view('processes.assembly.saxophone.label.print_label_description', array(
            'barcode' => $barcode,
        ))->with('page', 'Process Assy SX')->with('head', 'Assembly Process');
    }

    public function labelKecilSx($id, $remark)
    {
        $remark2 = $remark;
        $sn = $id;

        $barcode = DB::select("SELECT
			week_date,
			date_code
			FROM
			weekly_calendars
			WHERE
			week_date = (
			SELECT
			DISTINCT(DATE_FORMAT( created_at, '%Y-%m-%d' ))
			FROM
			assembly_logs
			WHERE
			serial_number = '" . $sn . "'
			AND location = 'packing'
			AND origin_group_code = '043'
			ORDER BY
				created_at DESC
			LIMIT 1
		)");

        return view('processes.assembly.saxophone.label.print_label_kecil', array(
            'barcode' => $barcode,
            'sn' => $sn,
            'remark' => $remark2,
        ))->with('page', 'Process Assy SX')->with('head', 'Assembly Process');
    }

    public function labelKecilFl($id, $remark)
    {
        $barcode = DB::select("SELECT week_date, date_code from weekly_calendars
			WHERE week_date = (SELECT DATE_FORMAT(created_at,'%Y-%m-%d') from assembly_logs
			WHERE serial_number = '" . $id . "'
			AND location = 'packing'
			AND origin_group_code = '041'
			ORDER BY created_at DESC
			LIMIT 1)");

        $des = DB::select("SELECT serial_number, model FROM assembly_logs
			WHERE origin_group_code = '041'
			AND location = 'packing'
			AND serial_number = '" . $id . "'
			ORDER BY created_at DESC
			LIMIT 1");

        return view('processes.assembly.flute.label.label_kecil', array(
            'barcode' => $barcode,
            'sn' => $id,
            'remark' => $remark,
            'des' => $des,
        ))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
    }

    public function labelKecilCl($id, $remark)
    {
        $barcode = DB::select("SELECT week_date, date_code from weekly_calendars
			WHERE week_date = (SELECT DATE_FORMAT(created_at,'%Y-%m-%d') from assembly_logs
			WHERE serial_number = '" . $id . "'
			AND location = 'packing'
			AND origin_group_code = '042'
			ORDER BY created_at DESC
			LIMIT 1)");

        $des = DB::select("SELECT serial_number, model FROM assembly_logs
			WHERE origin_group_code = '042'
			AND location = 'packing'
			AND serial_number = '" . $id . "'
			ORDER BY created_at DESC
			LIMIT 1");

        return view('processes.assembly.clarinet.label.label_kecil', array(
            'barcode' => $barcode,
            'sn' => $id,
            'remark' => $remark,
            'des' => $des,
        ))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
    }

    public function labelReprintSx($id, $gmc, $remark)
    {
        $japan = "";

        $weekly_calendar = db::table('weekly_calendars')->where('week_date', '=', date('Y-m-d'))->first();

        if ($remark == 'JR') {
            $japan = "AND stamp_hierarchies.remark = 'J'";
        }
        $barcode = db::select("SELECT
			sax.serial_number,
			material.finished,
			material.janean,
			'" . $weekly_calendar->date_code . "' AS date_code,
			material.upc,
			material.remark,
			material.model
			FROM
			(
			SELECT
			stamp_hierarchies.finished,
			materials.material_description AS model,
			stamp_hierarchies.janean,
			stamp_hierarchies.upc,
			stamp_hierarchies.remark
			FROM
			stamp_hierarchies
			LEFT JOIN materials ON stamp_hierarchies.finished = materials.material_number
			WHERE
			stamp_hierarchies.finished = '" . $gmc . "'
			" . $japan . "
			) AS material
			LEFT JOIN ( SELECT serial_number, model FROM assembly_logs WHERE origin_group_code = '043' AND location = 'packing' AND serial_number = '" . $id . "' ) AS sax ON sax.model = material.model");

        $date2 = db::select("SELECT week_date, date_code from weekly_calendars
			WHERE week_date = (SELECT DISTINCT(DATE_FORMAT( created_at, '%Y-%m-%d' ) ) from assembly_logs
			WHERE serial_number = '" . $id . "'
			and location = 'packing'
			and origin_group_code = '043' order by created_at desc limit 1)");

        return view('processes.assembly.saxophone.label.print_label_besar', array(
            'barcode' => $barcode,
            'date2' => $date2,
            'remark' => $remark,
        ))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
    }

    public function labelBesarSx($id, $gmc, $remark, $operator_id)
    {

        $assembly_details = AssemblyDetail::where('serial_number', $id)
            ->where('origin_group_code', '043')
            ->get();

        foreach ($assembly_details as $row) {
            $assembly_log = new AssemblyLog([
                'tag' => $row->tag,
                'serial_number' => $row->serial_number,
                'model' => $row->model,
                'location' => $row->location,
                'location_number' => $row->location_number,
                'operator_audited' => $row->operator_audited,
                'operator_id' => $row->operator_id,
                'sedang_start_date' => $row->sedang_start_date,
                'sedang_finish_date' => $row->sedang_finish_date,
                'origin_group_code' => $row->origin_group_code,
                'note' => $row->note,
                'trial' => $row->trial,
                'status_material' => $row->status_material,
                'created_by' => $row->created_by,
            ]);
            $assembly_log->save();
        }

        $assembly_inventory = AssemblyInventory::where('serial_number', '=', $id)
            ->where('origin_group_code', '043')
            ->first();

        $material = Material::where('material_number', $gmc)->first();

        $assembly_log = new AssemblyLog([
            'tag' => $assembly_inventory->tag,
            'serial_number' => $assembly_inventory->serial_number,
            'model' => $material->material_description,
            'location' => 'packing',
            'location_number' => $assembly_inventory->location_number,
            'operator_id' => $operator_id,
            'sedang_start_date' => date('Y-m-d H:i:s'),
            'sedang_finish_date' => date('Y-m-d H:i:s'),
            'origin_group_code' => '043',
            'status_material' => $remark,
            'created_by' => $operator_id,
        ]);
        $assembly_log->save();

        $tag = AssemblyTag::where('serial_number', $id)
            ->where('origin_group_code', '043')
            ->update([
                'serial_number' => null,
                'model' => null,
            ]);

        $log_process = LogProcess::updateOrCreate(
            [
                'process_code' => '4',
                'serial_number' => $assembly_inventory->serial_number,
                'origin_group_code' => '043',
            ],
            [
                'model' => $material->material_description,
                'quantity' => 1,
                'created_by' => Auth::id(),
                'created_at' => date('Y-m-d H:i:s'),
            ]
        );

        $stamp_inventory = StampInventory::updateOrCreate(
            [
                'serial_number' => $assembly_inventory->serial_number,
                'origin_group_code' => '043',
            ],
            [
                'process_code' => '4',
                'model' => $material->material_description,
                'quantity' => 1,
                'status' => null,
                'created_by' => Auth::id(),
                'created_at' => date('Y-m-d H:i:s'),
            ]
        );

        $inventory = AssemblyInventory::where('serial_number', $id)
            ->where('origin_group_code', '043')
            ->delete();

        $detail = AssemblyDetail::where('serial_number', $id)
            ->where('origin_group_code', '043')
            ->delete();

        $japan = "";

        $weekly_calendar = db::table('weekly_calendars')->where('week_date', '=', date('Y-m-d'))->first();

        if ($remark == 'J') {
            $japan = "AND stamp_hierarchies.remark = 'J'";
        }
        $barcode = db::select("SELECT
			sax.serial_number,
			material.finished,
			material.janean,
			'" . $weekly_calendar->date_code . "' AS date_code,
			material.upc,
			material.remark,
			material.model
			FROM
			(
			SELECT
			stamp_hierarchies.finished,
			materials.material_description AS model,
			stamp_hierarchies.janean,
			stamp_hierarchies.upc,
			stamp_hierarchies.remark
			FROM
			stamp_hierarchies
			LEFT JOIN materials ON stamp_hierarchies.finished = materials.material_number
			WHERE
			stamp_hierarchies.finished = '" . $gmc . "'
			" . $japan . "
			) AS material
			LEFT JOIN ( SELECT serial_number, model FROM assembly_logs WHERE origin_group_code = '043' AND location = 'packing' AND serial_number = '" . $id . "' ) AS sax ON sax.model = material.model");

        $date2 = db::select("SELECT week_date, date_code from weekly_calendars
			WHERE week_date = (SELECT DISTINCT(DATE_FORMAT( created_at, '%Y-%m-%d' ) ) from assembly_logs
			WHERE serial_number = '" . $id . "'
			and location = 'packing'
			and origin_group_code = '043')");

        return view('processes.assembly.saxophone.label.print_label_besar', array(
            'barcode' => $barcode,
            'date2' => $date2,
            'remark' => $remark,
        ))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
    }

    public function labelBesarFl($id, $gmc, $remark)
    {

        if ($remark == 'P') {
            $now = new DateTime();

            $sp = AssemblyInventory::where('serial_number', $id)
                ->where('origin_group_code', '041')
                ->first();

            $material_sp = Material::where('model', $sp->model)->first();

            $material = Material::where('material_number', $gmc)->first();

            if ($material_sp->xy == 'SP') {
                $inventory = AssemblyInventory::where('serial_number', $id)
                    ->where('origin_group_code', '041')
                    ->update([
                        'location' => 'seasoningsp-process',
                        'after_packing' => 'packing',
                    ]);

                $material = Material::where('material_number', $gmc)->first();
                $packing = new AssemblyDetail([
                    'tag' => $sp->tag,
                    'serial_number' => $id,
                    'model' => $material->material_description,
                    'location' => 'packing',
                    'operator_id' => Auth::user()->username,
                    'sedang_start_date' => $now,
                    'sedang_finish_date' => $now,
                    'origin_group_code' => '041',
                    'created_by' => Auth::user()->username,
                ]);
                $packing->save();

                $seasoning = new AssemblyDetail([
                    'tag' => $sp->tag,
                    'serial_number' => $id,
                    'model' => $material->material_description,
                    'location' => 'seasoningsp-process',
                    'operator_id' => Auth::user()->username,
                    'sedang_start_date' => $now,
                    'sedang_finish_date' => $now,
                    'origin_group_code' => '041',
                    'created_by' => Auth::user()->username,
                ]);
                $seasoning->save();
            }

            $details = AssemblyDetail::where('serial_number', $id)
                ->where('origin_group_code', '041')
                ->where('is_send_log', '0')
                ->get();

            if (count($details) > 0) {
                foreach ($details as $detail) {
                    $detail = new AssemblyLog([
                        'tag' => $detail->tag,
                        'serial_number' => $detail->serial_number,
                        'model' => $detail->model,
                        'location' => $detail->location,
                        'operator_audited' => $detail->operator_audited,
                        'operator_id' => $detail->operator_id,
                        'sedang_start_date' => $detail->sedang_start_date,
                        'sedang_finish_date' => $detail->sedang_finish_date,
                        'origin_group_code' => $detail->origin_group_code,
                        'note' => $detail->note,
                        'status_material' => $detail->status_material,
                        'created_by' => $detail->created_by,
                    ]);
                    $detail->save();
                }

                if ($material_sp->xy != 'SP') {
                    $material = Material::where('material_number', $gmc)->first();
                    $dets = new AssemblyLog([
                        'tag' => $details[0]->tag,
                        'serial_number' => $id,
                        'model' => $material->material_description,
                        'location' => 'packing',
                        'operator_id' => Auth::user()->username,
                        'sedang_start_date' => $now,
                        'sedang_finish_date' => $now,
                        'origin_group_code' => '041',
                        'status_material' => $details[0]->status_material,
                        'created_by' => Auth::user()->username,
                    ]);
                    $dets->save();
                }

                $details = AssemblyDetail::where('serial_number', $id)
                    ->where('origin_group_code', '041')
                    ->where('is_send_log', '0')
                    ->update([
                        'is_send_log' => '1',
                    ]);
            }

            $log_process = LogProcess::updateOrCreate(
                [
                    'process_code' => '6',
                    'serial_number' => $sp->serial_number,
                    'origin_group_code' => '041',
                ],
                [
                    'model' => $material->material_description,
                    'quantity' => 1,
                    'created_by' => Auth::id(),
                    'created_at' => date('Y-m-d H:i:s'),
                ]
            );

            $stamp_inventory = StampInventory::updateOrCreate(
                [
                    'serial_number' => $sp->serial_number,
                    'origin_group_code' => '041',
                ],
                [
                    'process_code' => '6',
                    'model' => $material->material_description,
                    'quantity' => 1,
                    'status' => null,
                    'created_by' => Auth::id(),
                    'created_at' => date('Y-m-d H:i:s'),
                ]
            );

            if ($material_sp->xy != 'SP') {
                $tag = AssemblyTag::where('serial_number', $id)
                    ->where('origin_group_code', '041')
                    ->update([
                        'serial_number' => null,
                        'model' => null,
                    ]);

                $inventory = AssemblyInventory::where('serial_number', $id)
                    ->where('origin_group_code', '041')
                    ->delete();

                $detail = AssemblyDetail::where('serial_number', $id)
                    ->where('origin_group_code', '041')
                    ->delete();
            }
        }

        $update_target = DB::connection('ympimis_2')->table('assembly_targets')
            ->where('material_number', $gmc)->where('due_date', date('Y-m-d'))->first();
        if ($update_target != null) {
            $update_target2 = DB::connection('ympimis_2')->table('assembly_targets')
                ->where('material_number', $gmc)->where('due_date', date('Y-m-d'))->update([
                'actual_quantity' => $update_target->actual_quantity + 1,
            ]);
            // $update_target->actual_quantity = $update_target->actual_quantity+1;
            // $update_target->save();
        }

        $barcode = db::select("select flute.serial_number, material.finished, material.janean, material.upc, material.remark, material.model from
			(select stamp_hierarchies.finished, materials.material_description as model, stamp_hierarchies.janean, stamp_hierarchies.upc, stamp_hierarchies.remark from stamp_hierarchies
			left join materials on stamp_hierarchies.finished = materials.material_number
			where stamp_hierarchies.finished = '" . $gmc . "') as material
			left join
			(SELECT serial_number, model FROM assembly_logs
			WHERE origin_group_code = '041'
			AND location = 'packing'
			AND serial_number = '" . $id . "') as flute
			on flute.model = material.model;");

        $date = db::select("SELECT week_date, date_code from weekly_calendars
			WHERE week_date = (SELECT DATE_FORMAT(created_at,'%Y-%m-%d') from assembly_logs
			WHERE serial_number = '" . $id . "'
			and location = 'packing'
			and origin_group_code = '041')");

        return view('processes.assembly.flute.label.label_besar', array(
            'barcode' => $barcode,
            'date' => $date,
            'remark' => $remark,
        ))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
    }

    public function labelBesarCl($id, $gmc, $remark, $employee_id)
    {

        if ($remark == 'P') {
            $now = new DateTime();

            $sp = AssemblyInventory::where('serial_number', $id)
                ->where('origin_group_code', '042')
                ->first();

            $details = AssemblyDetail::where('serial_number', $id)
                ->where('origin_group_code', '042')
                ->where('is_send_log', '0')
                ->get();

            $seasoning = DB::connection('ympimis_2')->table('assembly_seasonings')->where('tag',$sp->tag)->where(DB::RAW("SPLIT_STRING(material, ' - ', 3)"),$id)->where('remark','Masuk_Lagi')->first();
            if ($seasoning) {
                DB::connection('ympimis_2')->table('assembly_seasonings')->insert([
                    'seasoning_tag' => '',
                    'seasoning_id' => $seasoning->seasoning_id,
                    'origin_group_code' => '042',
                    'location' => $seasoning->location,
                    'tag' => $seasoning->tag,
                    'material' => $seasoning->material,
                    'employee_id' => $seasoning->employee_id,
                    'name' => $seasoning->name,
                    'timestamps' => date('Y-m-d H:i:s'),
                    'created_by' => Auth::user()->id,
                    'remark' => 'Keluar_Close',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $out = DB::connection('ympimis_2')->table('assembly_seasonings')->where('tag',$sp->tag)->where(DB::RAW("SPLIT_STRING(material, ' - ', 3)"),$id)->where('remark','Masuk_Lagi')->update([
                    'remark' => 'Masuk_Close',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $material = Material::where('material_number', $gmc)->first();

            $japan = 'NJ';

            if (str_contains($sp->remark, 'SP') && str_contains($sp->model, 'J')) {
                $japan = 'J';
            }

            if (count($details) > 0) {
                foreach ($details as $detail) {
                    $detail = new AssemblyLog([
                        'tag' => $detail->tag,
                        'serial_number' => $detail->serial_number,
                        'model' => $detail->model,
                        'location' => $detail->location,
                        'location_number' => $detail->location_number,
                        'operator_id' => $detail->operator_id,
                        'operator_audited' => $detail->operator_audited,
                        'sedang_start_date' => $detail->sedang_start_date,
                        'sedang_finish_date' => $detail->sedang_finish_date,
                        'origin_group_code' => $detail->origin_group_code,
                        'note' => $detail->note,
                        'status_material' => $detail->status_material,
                        'created_by' => $detail->created_by,
                    ]);
                    $detail->save();
                }

                $material = Material::where('material_number', $gmc)->first();

                $dets = new AssemblyLog([
                    'tag' => $sp->tag,
                    'serial_number' => $id,
                    'model' => $material->material_description,
                    'location' => 'packing',
                    'location_number' => '1',
                    'operator_id' => $employee_id,
                    'sedang_start_date' => $now,
                    'sedang_finish_date' => $now,
                    'origin_group_code' => '042',
                    'status_material' => $japan,
                    'created_by' => $employee_id,
                ]);
                $dets->save();

                $details = AssemblyDetail::where('serial_number', $id)
                    ->where('origin_group_code', '042')
                    ->where('is_send_log', '0')
                    ->update([
                        'is_send_log' => '1',
                    ]);
            }

            $tag = AssemblyTag::where('serial_number', $id)
                ->where('origin_group_code', '042')
                ->update([
                    'serial_number' => null,
                    'model' => null,
                ]);

            $log_process = LogProcess::updateOrCreate(
                [
                    'process_code' => '4',
                    'serial_number' => $sp->serial_number,
                    'origin_group_code' => '042',
                ],
                [
                    'model' => $material->material_description,
                    'quantity' => 1,
                    'created_by' => Auth::id(),
                    'created_at' => date('Y-m-d H:i:s'),
                ]
            );

            $stamp_inventory = StampInventory::updateOrCreate(
                [
                    'serial_number' => $sp->serial_number,
                    'origin_group_code' => '042',
                ],
                [
                    'process_code' => '4',
                    'model' => $material->material_description,
                    'quantity' => 1,
                    'status' => null,
                    'created_by' => Auth::id(),
                    'created_at' => date('Y-m-d H:i:s'),
                ]
            );

            $inventory = AssemblyInventory::where('serial_number', $id)
                ->where('origin_group_code', '042')
                ->delete();

            $detail = AssemblyDetail::where('serial_number', $id)
                ->where('origin_group_code', '042')
                ->delete();
        }

        $update_target = DB::connection('ympimis_2')
            ->table('assembly_targets')
            ->where('material_number', $gmc)
            ->where('due_date', date('Y-m-d'))
            ->first();

        if ($update_target != null) {
            $update_target2 = DB::connection('ympimis_2')
                ->table('assembly_targets')
                ->where('material_number', $gmc)
                ->where('due_date', date('Y-m-d'))
                ->update([
                    'actual_quantity' => $update_target->actual_quantity + 1,
                ]);
        }

        $barcode = db::select("select flute.serial_number, material.finished, material.janean, material.upc, material.remark, material.model from
			(select stamp_hierarchies.finished, materials.material_description as model, stamp_hierarchies.janean, stamp_hierarchies.upc, stamp_hierarchies.remark from stamp_hierarchies
			left join materials on stamp_hierarchies.finished = materials.material_number
			where stamp_hierarchies.finished = '" . $gmc . "') as material
			left join
			(SELECT serial_number, model FROM assembly_logs
			WHERE origin_group_code = '042'
			AND location = 'packing'
			AND serial_number = '" . $id . "') as flute
			on flute.model = material.model;");

        $date = db::select("SELECT week_date, date_code from weekly_calendars
			WHERE week_date = (SELECT DATE_FORMAT(created_at,'%Y-%m-%d') from assembly_logs
			WHERE serial_number = '" . $id . "'
			and location = 'packing'
			and origin_group_code = '042')");

        return view('processes.assembly.clarinet.label.label_besar', array(
            'barcode' => $barcode,
            'date' => $date,
            'remark' => $remark,
        ))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
    }

    public function labelBesarOuterCl($id, $gmc, $codej, $remark)
    {

        if ($remark == 'P') {
            $details = AssemblyDetail::where('serial_number', $id)
                ->where('origin_group_code', '042')
                ->where('is_send_log', '0')
                ->get();
            $now = new DateTime();

            if (count($details) > 0) {
                foreach ($details as $detail) {
                    $detail = new AssemblyLog([
                        'tag' => $detail->tag,
                        'serial_number' => $detail->serial_number,
                        'model' => $detail->model,
                        'location' => $detail->location,
                        'operator_id' => $detail->operator_id,
                        'sedang_start_date' => $detail->sedang_start_date,
                        'sedang_finish_date' => $detail->sedang_finish_date,
                        'origin_group_code' => $detail->origin_group_code,
                        'created_by' => $detail->created_by,
                    ]);
                    $detail->save();
                }

                $material = Material::where('material_number', $gmc)->first();
                $detail = new AssemblyLog([
                    'tag' => $details[0]->tag,
                    'serial_number' => $id,
                    'model' => $material->material_description,
                    'location' => 'packing',
                    'operator_id' => Auth::user()->username,
                    'sedang_start_date' => $now,
                    'sedang_finish_date' => $now,
                    'origin_group_code' => '042',
                    'created_by' => Auth::user()->username,
                ]);
                $detail->save();

                $details = AssemblyDetail::where('serial_number', $id)
                    ->where('origin_group_code', '042')
                    ->where('is_send_log', '0')
                    ->update([
                        'is_send_log' => '1',
                    ]);
            }
        }

        $barcode = db::select("select stamp_hierarchies.finished, materials.material_description as model, stamp_hierarchies.janean, stamp_hierarchies.upc, stamp_hierarchies.remark from stamp_hierarchies
			left join materials on stamp_hierarchies.finished = materials.material_number
			where stamp_hierarchies.finished = '" . $gmc . "'");

        $date = db::select("SELECT week_date, date_code from weekly_calendars
			WHERE week_date = (SELECT DATE_FORMAT(created_at,'%Y-%m-%d') from assembly_logs
			WHERE serial_number = '" . $id . "'
			AND location = 'packing'
			AND origin_group_code = '042'
			ORDER BY created_at DESC
			LIMIT 1)");

        return view('processes.assembly.clarinet.label.label_besar_outer', array(
            'barcode' => $barcode,
            'date' => $date,
            'remark' => $remark,
            'codej' => $codej,
        ))->with('page', 'Process Assy CL')->with('head', 'Assembly Process');
    }

    public function labelBesarOuterClAlone($date, $gmc, $remark)
    {

        $barcode = db::select("select stamp_hierarchies.finished, materials.material_description as model, stamp_hierarchies.janean, stamp_hierarchies.upc, stamp_hierarchies.remark from stamp_hierarchies
			left join materials on stamp_hierarchies.finished = materials.material_number
			where stamp_hierarchies.finished = '" . $gmc . "'");

        $date = db::select("SELECT week_date, date_code from weekly_calendars
			WHERE week_date = '" . $date . "'");

        return view('processes.assembly.clarinet.label.label_besar_outer_alone', array(
            'barcode' => $barcode,
            'date' => $date,
            'remark' => $remark,
        ))->with('page', 'Process Assy CL')->with('head', 'Assembly Process');
    }

    public function labelBesarOuterFl($id, $gmc, $remark)
    {

        if ($remark == 'P') {
            $details = AssemblyDetail::where('serial_number', $id)
                ->where('origin_group_code', '041')
                ->where('is_send_log', '0')
                ->get();
            $now = new DateTime();

            if (count($details) > 0) {
                foreach ($details as $detail) {
                    $detail = new AssemblyLog([
                        'tag' => $detail->tag,
                        'serial_number' => $detail->serial_number,
                        'model' => $detail->model,
                        'location' => $detail->location,
                        'operator_id' => $detail->operator_id,
                        'sedang_start_date' => $detail->sedang_start_date,
                        'sedang_finish_date' => $detail->sedang_finish_date,
                        'origin_group_code' => $detail->origin_group_code,
                        'created_by' => $detail->created_by,
                    ]);
                    $detail->save();
                }

                $material = Material::where('material_number', $gmc)->first();
                $detail = new AssemblyLog([
                    'tag' => $details[0]->tag,
                    'serial_number' => $id,
                    'model' => $material->material_description,
                    'location' => 'packing',
                    'operator_id' => Auth::user()->username,
                    'sedang_start_date' => $now,
                    'sedang_finish_date' => $now,
                    'origin_group_code' => '041',
                    'created_by' => Auth::user()->username,
                ]);
                $detail->save();

                $details = AssemblyDetail::where('serial_number', $id)
                    ->where('origin_group_code', '041')
                    ->where('is_send_log', '0')
                    ->update([
                        'is_send_log' => '1',
                    ]);
            }
        }

        $barcode = db::select("select stamp_hierarchies.finished, materials.material_description as model, stamp_hierarchies.janean, stamp_hierarchies.upc, stamp_hierarchies.remark from stamp_hierarchies
			left join materials on stamp_hierarchies.finished = materials.material_number
			where stamp_hierarchies.finished = '" . $gmc . "'");

        $date = db::select("SELECT week_date, date_code from weekly_calendars
			WHERE week_date = (SELECT DATE_FORMAT(created_at,'%Y-%m-%d') from assembly_logs
			WHERE serial_number = '" . $id . "'
			AND location = 'packing'
			AND origin_group_code = '041'
			ORDER BY created_at DESC
			LIMIT 1)");

        return view('processes.assembly.flute.label.label_besar_outer', array(
            'barcode' => $barcode,
            'date' => $date,
            'remark' => $remark,
        ))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
    }

    public function fetchCheckCarb(Request $request)
    {
        $sn = $request->get('sn');

        $model = AssemblyLog::where('location', 'packing')
            ->where('origin_group_code', '041')
            ->where('serial_number', $sn)
            ->first();

        $response = array(
            'status' => true,
            'model' => $model,
        );
        return Response::json($response);
    }

    public function fetchCheckReprint(Request $request)
    {
        $serial_number = $request->get('serial_number');
        $origin_group_code = $request->get('origin_group');

        $log = AssemblyLog::leftJoin('materials', 'materials.material_description', '=', 'assembly_logs.model')
            ->where('assembly_logs.serial_number', $serial_number)
            ->where('assembly_logs.origin_group_code', $origin_group_code)
            ->where('assembly_logs.location', 'packing')
            ->select('assembly_logs.serial_number', 'assembly_logs.model', 'materials.material_number', 'materials.material_description')
            ->orderBy('assembly_logs.created_at', 'desc')
            ->first();

        if ($log) {
            $response = array(
                'status' => true,
                'log' => $log,
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'message' => 'Serial Number Not Found',
            );
            return Response::json($response);
        }

    }

    public function fetchCheckTag(Request $request)
    {
        $origin_group_code = $request->get('origin_group');
        $tag = $request->get('tag');

        if ($origin_group_code == '043') {
            $tag = strtoupper($this->dec2hex($tag));

            $assembly_inventory = AssemblyInventory::where('tag', '=', $tag)
                ->where('origin_group_code', '=', $origin_group_code)
                ->first();

            if (!$assembly_inventory) {
                $response = array(
                    'status' => false,
                    'message' => 'RFID tidak ditemukan.',
                );
                return Response::json($response);
            }

            if (str_contains($assembly_inventory->model, 'YAS')) {
                $details_qa_fungsi = DB::SELECT("SELECT
				*
				FROM
				assembly_details
				WHERE
				tag = '" . $tag . "'
				AND location = 'qa-fungsi'");

                if (count($details_qa_fungsi) == 0) {
                    $response = array(
                        'status' => false,
                        'message' => 'YAS = Proses Kensa QA Fungsi belum dilakukan.',
                    );
                    return Response::json($response);
                }

                $details_qa_visual = DB::SELECT("SELECT
				*
				FROM
				assembly_details
				WHERE
				tag = '" . $tag . "'
				AND location = 'qa-visual'");

                if (count($details_qa_visual) == 0) {
                    $response = array(
                        'status' => false,
                        'message' => 'Proses Kensa QA Visual belum dilakukan.',
                    );
                    return Response::json($response);
                }
            }

            if (str_contains($assembly_inventory->model, 'YTS')) {
                $details_qa_kensa = DB::SELECT("SELECT
				*
				FROM
				assembly_details
				WHERE
				tag = '" . $tag . "'
				AND location = 'qa-kensa'");

                if (count($details_qa_kensa) == 0) {
                    $response = array(
                        'status' => false,
                        'message' => 'YTS = Proses Kensa QA belum dilakukan.',
                    );
                    return Response::json($response);
                }
            }

            $model = db::select("SELECT
				material_number,
				material_description,
				remark
				FROM
				materials
				LEFT JOIN stamp_hierarchies ON materials.material_number = stamp_hierarchies.finished
				WHERE
				stamp_hierarchies.model = '" . $assembly_inventory->model . "'");

            $response = array(
                'status' => true,
                'assembly_inventory' => $assembly_inventory,
                'model' => $model,
            );
            return Response::json($response);
        }

        if ($origin_group_code == '042') {

            if (str_contains($tag, '21') && strlen($tag) == 8) {

                $assembly_inventory = AssemblyInventory::where('serial_number', '=', $tag)
                    ->where('origin_group_code', '=', $origin_group_code)
                    ->first();

                if (!$assembly_inventory) {
                    $insert_inv = db::table('assembly_inventories')
                        ->insert([
                            'tag' => $tag,
                            'serial_number' => $tag,
                            'model' => 'YCL',
                            'location' => 'registration-process',
                            'location_next' => 'packing',
                            'origin_group_code' => $origin_group_code,
                            'created_by' => $request->get('employee_id'),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                    $assembly_inventory = AssemblyInventory::where('serial_number', '=', $tag)
                        ->where('origin_group_code', '=', $origin_group_code)
                        ->first();
                }

                $assembly_detail = AssemblyDetail::where('serial_number', '=', $tag)
                    ->where('origin_group_code', '=', $origin_group_code)
                    ->first();

                if (!$assembly_detail) {
                    $insert_detail = db::table('assembly_details')
                        ->insert([
                            'serial_number' => $tag,
                            'model' => 'YCL',
                            'location' => 'registration-process',
                            'operator_id' => $request->get('employee_id'),
                            'sedang_start_date' => date('Y-m-d H:i:s'),
                            'sedang_finish_date' => date('Y-m-d H:i:s'),
                            'origin_group_code' => $origin_group_code,
                            'created_by' => $request->get('employee_id'),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }

                $model = db::select("SELECT material_number, material_description FROM materials
					WHERE category = 'FG'
					AND hpl = 'CLFG'");

            } else if (strlen($tag) == 6) {

                $assembly_inventory = AssemblyInventory::where('serial_number', '=', $tag)
                    ->where('origin_group_code', '=', $origin_group_code)
                    ->first();

                if (!$assembly_inventory) {
                    $insert_inv = db::table('assembly_inventories')
                        ->insert([
                            'tag' => $tag,
                            'serial_number' => $tag,
                            'model' => 'YCL',
                            'location' => 'registration-process',
                            'location_next' => 'packing',
                            'origin_group_code' => $origin_group_code,
                            'created_by' => $request->get('employee_id'),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                    $assembly_inventory = AssemblyInventory::where('serial_number', '=', $tag)
                        ->where('origin_group_code', '=', $origin_group_code)
                        ->first();
                }

                $assembly_detail = AssemblyDetail::where('serial_number', '=', $tag)
                    ->where('origin_group_code', '=', $origin_group_code)
                    ->first();

                if (!$assembly_detail) {
                    $insert_detail = db::table('assembly_details')
                        ->insert([
                            'serial_number' => $tag,
                            'model' => 'YCL',
                            'location' => 'registration-process',
                            'operator_id' => $request->get('employee_id'),
                            'sedang_start_date' => date('Y-m-d H:i:s'),
                            'sedang_finish_date' => date('Y-m-d H:i:s'),
                            'origin_group_code' => $origin_group_code,
                            'created_by' => $request->get('employee_id'),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }

                $model = db::select("SELECT material_number, material_description FROM materials
					WHERE category = 'FG'
					AND hpl = 'CLFG'");

            } else {
                $tag = strtoupper($this->dec2hex($tag));

                $assembly_inventory = AssemblyInventory::where('tag', '=', $tag)
                    ->where('origin_group_code', '=', $origin_group_code)
                    ->first();

                $details_kensa_process = DB::select("SELECT
						*
					FROM
						assembly_details
					WHERE
						origin_group_code = '" . $origin_group_code . "'
						AND tag = '" . $tag . "'
						AND location = 'kensa-process'");

                if (count($details_kensa_process) == 0) {
                    $response = array(
                        'status' => false,
                        'message' => 'Proses Kensa Produksi Belum Dilakukan',
                    );
                    return Response::json($response);
                }

                $details_kensa_qa = DB::select("SELECT
						*
					FROM
						assembly_details
					WHERE
						origin_group_code = '" . $origin_group_code . "'
						AND tag = '" . $tag . "'
						AND location = 'qa-kensa'");

                if (count($details_kensa_qa) == 0) {
                    $response = array(
                        'status' => false,
                        'message' => 'Proses Kensa QA Belum Dilakukan',
                    );
                    return Response::json($response);
                }

                if (!$assembly_inventory) {
                    $response = array(
                        'status' => false,
                        'message' => 'RFID tidak ditemukan.',
                    );
                    return Response::json($response);
                }

                if ($assembly_inventory->location != 'qa-kensa') {
                    $response = array(
                        'status' => false,
                        'message' => 'Proses Kensa QA belum dilakukan.',
                    );
                    return Response::json($response);
                }

                if ($assembly_inventory->remark == null) {
                    $response = array(
                        'status' => false,
                        'message' => 'Kensa Proses dan QA Belum Memilih J / NJ',
                    );
                    return Response::json($response);
                }

                if ($assembly_inventory->remark != null) {
                    if (explode('_', $assembly_inventory->remark)[0] == '') {
                        $response = array(
                            'status' => false,
                            'message' => 'Kensa Proses Belum Memilih J / NJ',
                        );
                        return Response::json($response);
                    }
                    if (explode('_', $assembly_inventory->remark)[1] == '') {
                        $response = array(
                            'status' => false,
                            'message' => 'Kensa QA Belum Memilih J / NJ',
                        );
                        return Response::json($response);
                    }
                }

                if (str_contains($assembly_inventory->model,'YCL255J')) {
                    $model = db::select("SELECT
                    material_number,
                    material_description,
                    remark
                    FROM
                    materials
                    LEFT JOIN stamp_hierarchies ON materials.material_number = stamp_hierarchies.finished
                    WHERE
                    stamp_hierarchies.model = 'YCL255'");
                }else{
                    $model = db::select("SELECT
                    material_number,
                    material_description,
                    remark
                    FROM
                    materials
                    LEFT JOIN stamp_hierarchies ON materials.material_number = stamp_hierarchies.finished
                    WHERE
                    stamp_hierarchies.model = '" . $assembly_inventory->model . "'");
                }

            }

            $response = array(
                'status' => true,
                'assembly_inventory' => $assembly_inventory,
                'model' => $model,
            );
            return Response::json($response);
        }

        if ($origin_group_code == '041') {
            $tag = strtoupper($this->dec2hex($tag));

            $data = AssemblyInventory::where('tag', $tag)
            // ->whereIn('location', ['qa-visual2', 'qa-visual1', 'qa-fungsi'])
                ->where('location', 'qa-visual2')
                ->where('origin_group_code', $origin_group_code)
                ->first();

            $data2 = AssemblyDetail::select('location')->distinct()->where('tag', $tag)
                ->whereIn('location', ['qa-visual2', 'qa-visual1', 'qa-fungsi'])
            // ->where('location', 'qa-visual2')
                ->where('origin_group_code', $origin_group_code)
                ->get();

            if (count($data2) >= 3) {
                $remark = "";
                $material_sp = Material::where('model', $data->model)->first();
                if ($material_sp->xy == 'SP') {
                    $remark = "AND stamp_hierarchies.remark = 'SP'";
                }

                $model = db::select("SELECT material_number, material_description, remark FROM materials
					LEFT JOIN stamp_hierarchies ON materials.material_number = stamp_hierarchies.finished
					WHERE stamp_hierarchies.model IN (SELECT model FROM assembly_inventories WHERE tag = '" . $tag . "') " . $remark);

                $response = array(
                    'status' => true,
                    'data' => $data,
                    'model' => $model,
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Kartu Tidak Ditemukan atau Kartu belum melewati proses QA-Fungsi, QA-Visual1, atau QA-Visual2',
                );
                return Response::json($response);
            }
        }

    }

    public function fillModelResult(Request $request)
    {
        $date = date('Y-m-d');
        $origin_group = $request->get('origin_group');

        $data = db::select("SELECT model, COUNT(id) AS quantity FROM assembly_logs
			WHERE location = 'packing'
			AND DATE_FORMAT(created_at,'%Y-%m-%d') = '" . $date . "'
			AND origin_group_code = '" . $origin_group . "'
			GROUP BY model;");

        $target = DB::connection('ympimis_2')->table('assembly_targets')->where('due_date', date('Y-m-d'))->get();

        $response = array(
            'status' => true,
            'data' => $data,
            'target' => $target,
        );
        return Response::json($response);
    }

    public function fillResult(Request $request)
    {
        $date = date('Y-m-d');
        $origin_group = $request->get('origin_group');

        $data = db::select("SELECT serial_number, model, created_at FROM assembly_logs
			where location = 'packing'
			and DATE_FORMAT(created_at,'%Y-%m-%d') = '" . $date . "'
			AND origin_group_code = '" . $origin_group . "'
			ORDER BY created_at DESC");

        return DataTables::of($data)->make(true);
    }

    public function fetchAssemblyBoard(Request $request)
    {
        $loc = $request->get('loc');
        $boards = array();

        $now = date('Y-m-d');

        $locations = explode(",", $loc);
        $location = "";

        for ($x = 0; $x < count($locations); $x++) {
            $location = $location . "'" . $locations[$x] . "'";
            if ($x != count($locations) - 1) {
                $location = $location . ",";
            }
        }
        $addlocation = "assemblies.location in (" . $location . ") ";

        if ($loc == 'perakitanawal-kensa,tanpoawase-process') {
            $work_stations = DB::select("SELECT
				IF
				( assemblies.location = 'perakitanawal-kensa', 'Perakitan Ulang', assemblies.location ) AS location,
				location_number,
				online_time,
				assemblies.operator_id,
				name,
				sedang_serial_number,
				sedang_model,
				TIME( sedang_time ) AS sedang_time,
				DATE( sedang_time ) AS sedang_date,
				( SELECT standard_time FROM assembly_std_times WHERE location = assemblies.location and origin_group_code = 041) AS std_time,
				( SELECT count( DISTINCT ( serial_number )) FROM assembly_logs WHERE operator_id = assemblies.operator_id AND DATE( created_at ) = '" . $now . "' and origin_group_code = 041) + ( SELECT count( DISTINCT ( serial_number )) FROM assembly_details WHERE operator_id = assemblies.operator_id AND DATE( created_at ) = '" . $now . "' and origin_group_code = 041) AS perolehan,
				( SELECT GROUP_CONCAT( ng_name ORDER BY assembly_ng_logs.id DESC) AS ng_name FROM assembly_ng_logs WHERE assembly_ng_logs.serial_number = assemblies.sedang_serial_number and origin_group_code = 041 LIMIT 4 ) AS ng_name,
				( SELECT GROUP_CONCAT( ongko ORDER BY assembly_ng_logs.id DESC) AS ongko FROM assembly_ng_logs WHERE assembly_ng_logs.serial_number = assemblies.sedang_serial_number and origin_group_code = 041 LIMIT 4) AS onko,
				(
				SELECT
				GROUP_CONCAT(
				COALESCE ( value_atas, 0 ),
				'-',
				COALESCE ( value_bawah, 0 ) ORDER BY assembly_ng_logs.id DESC) AS valueses
				FROM
				assembly_ng_logs
				WHERE
				assembly_ng_logs.serial_number = assemblies.sedang_serial_number
				and origin_group_code = 041
				LIMIT 4
				) AS valueses,
				( SELECT GROUP_CONCAT( COALESCE ( value_lokasi, 0 ) ORDER BY assembly_ng_logs.id DESC) AS valueses FROM assembly_ng_logs WHERE assembly_ng_logs.serial_number = assemblies.sedang_serial_number and origin_group_code = 041 LIMIT 4) AS lokasi
				FROM
				assemblies
				LEFT JOIN employee_syncs ON employee_syncs.employee_id = assemblies.operator_id
				WHERE
				" . $addlocation . "
				and location_number in ('1','2','3','4','5','6','7')
				AND assemblies.origin_group_code = '041'
				ORDER BY remark, location_number asc");
        } else if ($loc == 'tanpoawase-process') {
            $work_stations = DB::select("SELECT
				IF
				( assemblies.location = 'perakitanawal-kensa', 'Perakitan Ulang', assemblies.location ) AS location,
				location_number,
				online_time,
				assemblies.operator_id,
				name,
				sedang_serial_number,
				sedang_model,
				TIME( sedang_time ) AS sedang_time,
				DATE( sedang_time ) AS sedang_date,
				( SELECT standard_time FROM assembly_std_times WHERE location = assemblies.location and origin_group_code = 041) AS std_time,
				( SELECT count( DISTINCT ( serial_number )) FROM assembly_logs WHERE operator_id = assemblies.operator_id AND DATE( created_at ) = '" . $now . "' and origin_group_code = 041) + ( SELECT count( DISTINCT ( serial_number )) FROM assembly_details WHERE operator_id = assemblies.operator_id AND DATE( created_at ) = '" . $now . "' and origin_group_code = 041) AS perolehan,
				( SELECT GROUP_CONCAT( ng_name ORDER BY assembly_ng_logs.id DESC) AS ng_name FROM assembly_ng_logs WHERE assembly_ng_logs.serial_number = assemblies.sedang_serial_number and origin_group_code = 041 LIMIT 4 ) AS ng_name,
				( SELECT GROUP_CONCAT( ongko ORDER BY assembly_ng_logs.id DESC) AS ongko FROM assembly_ng_logs WHERE assembly_ng_logs.serial_number = assemblies.sedang_serial_number and origin_group_code = 041 LIMIT 4) AS onko,
				(
				SELECT
				GROUP_CONCAT(
				COALESCE ( value_atas, 0 ),
				'-',
				COALESCE ( value_bawah, 0 ) ORDER BY assembly_ng_logs.id DESC) AS valueses
				FROM
				assembly_ng_logs
				WHERE
				assembly_ng_logs.serial_number = assemblies.sedang_serial_number
				and origin_group_code = 041
				LIMIT 4
				) AS valueses,
				( SELECT GROUP_CONCAT( COALESCE ( value_lokasi, 0 ) ORDER BY assembly_ng_logs.id DESC) AS valueses FROM assembly_ng_logs WHERE assembly_ng_logs.serial_number = assemblies.sedang_serial_number and origin_group_code = 041 LIMIT 4) AS lokasi
				FROM
				assemblies
				LEFT JOIN employee_syncs ON employee_syncs.employee_id = assemblies.operator_id
				WHERE
				" . $addlocation . "
				and location_number in ('8','9','10','11','12','13','14','15','16','17')
				AND assemblies.origin_group_code = '041' and assemblies.deleted_at is null
				ORDER BY remark, location_number asc");
        } else if ($loc == 'tanpoawase-kensa,tanpoawase-fungsi,repair-process-1,repair-process-2') {
            $work_stations = DB::select("SELECT
				IF
				( assemblies.location = 'perakitanawal-kensa', 'Perakitan Ulang', assemblies.location ) AS location,
				location_number,
				online_time,
				assemblies.operator_id,
				name,
				sedang_serial_number,
				sedang_model,
				TIME( sedang_time ) AS sedang_time,
				DATE( sedang_time ) AS sedang_date,
				( SELECT standard_time FROM assembly_std_times WHERE location = assemblies.location and origin_group_code = 041) AS std_time,
				( SELECT count( DISTINCT ( serial_number )) FROM assembly_logs WHERE operator_id = assemblies.operator_id AND DATE( created_at ) = '" . $now . "' and origin_group_code = 041) + ( SELECT count( DISTINCT ( serial_number )) FROM assembly_details WHERE operator_id = assemblies.operator_id AND DATE( created_at ) = '" . $now . "' and origin_group_code = 041) AS perolehan,
				( SELECT GROUP_CONCAT( ng_name ORDER BY assembly_ng_logs.id DESC) AS ng_name FROM assembly_ng_logs WHERE assembly_ng_logs.serial_number = assemblies.sedang_serial_number and origin_group_code = 041 LIMIT 4 ) AS ng_name,
				( SELECT GROUP_CONCAT( ongko ORDER BY assembly_ng_logs.id DESC) AS ongko FROM assembly_ng_logs WHERE assembly_ng_logs.serial_number = assemblies.sedang_serial_number and origin_group_code = 041 LIMIT 4) AS onko,
				(
				SELECT
				GROUP_CONCAT(
				COALESCE ( value_atas, 0 ),
				'-',
				COALESCE ( value_bawah, 0 ) ORDER BY assembly_ng_logs.id DESC) AS valueses
				FROM
				assembly_ng_logs
				WHERE
				assembly_ng_logs.serial_number = assemblies.sedang_serial_number
				and origin_group_code = 041
				LIMIT 4
				) AS valueses,
				( SELECT GROUP_CONCAT( COALESCE ( value_lokasi, 0 ) ORDER BY assembly_ng_logs.id DESC) AS valueses FROM assembly_ng_logs WHERE assembly_ng_logs.serial_number = assemblies.sedang_serial_number and origin_group_code = 041 LIMIT 4) AS lokasi
				FROM
				assemblies
				LEFT JOIN employee_syncs ON employee_syncs.employee_id = assemblies.operator_id
				WHERE
				(assemblies.location in ('tanpoawase-kensa','tanpoawase-fungsi')
				AND assemblies.origin_group_code = '041' and assemblies.deleted_at is null)
				OR
				(assemblies.location in ('repair-process')
				and assemblies.location_number in ('1','2')
				AND assemblies.origin_group_code = '041' and assemblies.deleted_at is null)
				ORDER BY location ASC");
        } else if ($loc == 'fukiage1-process,repair-ringan') {
            $work_stations = DB::select("SELECT
				IF
				( assemblies.location = 'perakitanawal-kensa', 'Perakitan Ulang', assemblies.location ) AS location,
				location_number,
				online_time,
				assemblies.operator_id,
				name,
				sedang_serial_number,
				sedang_model,
				TIME( sedang_time ) AS sedang_time,
				DATE( sedang_time ) AS sedang_date,
				( SELECT standard_time FROM assembly_std_times WHERE location = assemblies.location and origin_group_code = 041) AS std_time,
				( SELECT count( DISTINCT ( serial_number )) FROM assembly_logs WHERE operator_id = assemblies.operator_id AND DATE( created_at ) = '" . $now . "' and origin_group_code = 041) + ( SELECT count( DISTINCT ( serial_number )) FROM assembly_details WHERE operator_id = assemblies.operator_id AND DATE( created_at ) = '" . $now . "' and origin_group_code = 041) AS perolehan,
				( SELECT GROUP_CONCAT( ng_name ORDER BY assembly_ng_logs.id DESC) AS ng_name FROM assembly_ng_logs WHERE assembly_ng_logs.serial_number = assemblies.sedang_serial_number and origin_group_code = 041 LIMIT 4 ) AS ng_name,
				( SELECT GROUP_CONCAT( ongko ORDER BY assembly_ng_logs.id DESC) AS ongko FROM assembly_ng_logs WHERE assembly_ng_logs.serial_number = assemblies.sedang_serial_number and origin_group_code = 041 LIMIT 4) AS onko,
				(
				SELECT
				GROUP_CONCAT(
				COALESCE ( value_atas, 0 ),
				'-',
				COALESCE ( value_bawah, 0 ) ORDER BY assembly_ng_logs.id DESC) AS valueses
				FROM
				assembly_ng_logs
				WHERE
				assembly_ng_logs.serial_number = assemblies.sedang_serial_number
				and origin_group_code = 041
				LIMIT 4
				) AS valueses,
				( SELECT GROUP_CONCAT( COALESCE ( value_lokasi, 0 ) ORDER BY assembly_ng_logs.id DESC) AS valueses FROM assembly_ng_logs WHERE assembly_ng_logs.serial_number = assemblies.sedang_serial_number and origin_group_code = 041 LIMIT 4) AS lokasi
				FROM
				assemblies
				LEFT JOIN employee_syncs ON employee_syncs.employee_id = assemblies.operator_id
				WHERE
				(assemblies.location in ('fukiage1-process')
				AND assemblies.origin_group_code = '041' and assemblies.deleted_at is null)
				OR
				(assemblies.location in ('repair-process')
				and assemblies.location_number in ('3','4','5')
				AND assemblies.origin_group_code = '041' and assemblies.deleted_at is null)
				ORDER BY location,location_number asc");
        } else {
            $work_stations = DB::select("SELECT
				IF
				( assemblies.location = 'perakitanawal-kensa', 'Perakitan Ulang', assemblies.location ) AS location,
				location_number,
				online_time,
				assemblies.operator_id,
				name,
				sedang_serial_number,
				sedang_model,
				TIME( sedang_time ) AS sedang_time,
				DATE( sedang_time ) AS sedang_date,
				( SELECT standard_time FROM assembly_std_times WHERE location = assemblies.location and origin_group_code = 041) AS std_time,
				( SELECT count( DISTINCT ( serial_number )) FROM assembly_logs WHERE operator_id = assemblies.operator_id AND DATE( created_at ) = '" . $now . "' and origin_group_code = 041) + ( SELECT count( DISTINCT ( serial_number )) FROM assembly_details WHERE operator_id = assemblies.operator_id AND DATE( created_at ) = '" . $now . "' and origin_group_code = 041) AS perolehan,
				( SELECT GROUP_CONCAT( ng_name ORDER BY assembly_ng_logs.id DESC) AS ng_name FROM assembly_ng_logs WHERE assembly_ng_logs.serial_number = assemblies.sedang_serial_number and origin_group_code = 041 LIMIT 4 ) AS ng_name,
				( SELECT GROUP_CONCAT( ongko ORDER BY assembly_ng_logs.id DESC) AS ongko FROM assembly_ng_logs WHERE assembly_ng_logs.serial_number = assemblies.sedang_serial_number and origin_group_code = 041 LIMIT 4) AS onko,
				(
				SELECT
				GROUP_CONCAT(
				COALESCE ( value_atas, 0 ),
				'-',
				COALESCE ( value_bawah, 0 ) ORDER BY assembly_ng_logs.id DESC) AS valueses
				FROM
				assembly_ng_logs
				WHERE
				assembly_ng_logs.serial_number = assemblies.sedang_serial_number
				and origin_group_code = 041
				LIMIT 4
				) AS valueses,
				( SELECT GROUP_CONCAT( COALESCE ( value_lokasi, 0 ) ORDER BY assembly_ng_logs.id DESC) AS valueses FROM assembly_ng_logs WHERE assembly_ng_logs.serial_number = assemblies.sedang_serial_number and origin_group_code = 041 LIMIT 4) AS lokasi
				FROM
				assemblies
				LEFT JOIN employee_syncs ON employee_syncs.employee_id = assemblies.operator_id
				WHERE
				" . $addlocation . "
				AND assemblies.origin_group_code = '041'
				ORDER BY location,location_number asc");
        }

        foreach ($work_stations as $ws) {

            $dt_now = new DateTime();

            $dt_sedang = new DateTime($ws->sedang_time);
            $sedang_time = $dt_sedang->diff($dt_now);

            $board_sedang = '';
            if ($ws->sedang_serial_number != null) {
                $board_sedang = '(' . $ws->sedang_serial_number . ')' . '<br>' . $ws->sedang_model;
            } else {
                $board_sedang = '<br>';
            }

            array_push($boards, [
                'ws' => strtoupper($ws->location . ' (' . $ws->location_number . ')'),
                'employee_id' => $ws->operator_id,
                'employee_name' => strtoupper($ws->name),
                'sedang' => $board_sedang,
                'sedang_time' => str_pad($sedang_time->format('%H'), 2, '0', STR_PAD_LEFT) . ":" . str_pad($sedang_time->format('%i'), 2, '0', STR_PAD_LEFT) . ":" . str_pad($sedang_time->format('%s'), 2, '0', STR_PAD_LEFT),
                'std_time' => $ws->std_time,
                'perolehan' => $ws->perolehan,
                'ng_name' => $ws->ng_name,
                'onko' => $ws->onko,
                'valueses' => $ws->valueses,
                'lokasi' => $ws->lokasi,
            ]);
        }

        $ng = DB::SELECT("SELECT
			ng_name,
			count(ng_name) as qty_ng
			FROM
			assembly_ng_logs
			LEFT JOIN assemblies on assembly_ng_logs.operator_id = assemblies.operator_id
			WHERE
			" . $addlocation . "
			AND DATE( assembly_ng_logs.created_at ) = '" . $now . "' GROUP BY ng_name");

        $response = array(
            'status' => true,
            'loc' => $loc,
            'boards' => $boards,
            'ng' => $ng,
        );
        return Response::json($response);
    }

    public function fetchAssemblyClarinetBoard(Request $request)
    {
        $loc = $request->get('loc');
        $boards = array();

        $now = date('Y-m-d');

        $locations = explode(",", $loc);
        $location = "";

        // for ($x = 0; $x < count($locations); $x++) {
        //     $location = $location . "'" . $locations[$x] . "'";
        //     if ($x != count($locations) - 1) {
        //         $location = $location . ",";
        //     }
        // }
        // $addlocation = "AND SUBSTRING( assembly_operators.location, 1, LENGTH( assembly_operators.location )- 2 ) IN ( " . $location . ")  ";
        // $addlocation2 = "AND SUBSTRING( assemblies.location, 1, LENGTH( assemblies.location )- 2 ) IN ( " . $location . ")  ";

   //      $work_stations = DB::select("SELECT
			// assembly_operators.employee_id AS operator_id,
			// employee_syncs.`name`,
			// assemblies.sedang_tag,
			// assemblies.sedang_serial_number,
			// assemblies.sedang_model,
			// COALESCE ( perolehans.perolehan, 0 ) AS perolehan,
			// SUBSTRING( assembly_operators.location, 1, LENGTH( assembly_operators.location )- 2 ) AS location
			// FROM
			// `assembly_operators`
			// LEFT JOIN employee_syncs ON employee_syncs.employee_id = assembly_operators.employee_id
			// LEFT JOIN assemblies ON assemblies.operator_id = assembly_operators.employee_id
			// LEFT JOIN (
			// SELECT
			// a.operator_id,
			// sum( a.perolehan ) AS perolehan
			// FROM
			// (
			// SELECT
			// operator_id,
			// count(
			// DISTINCT ( serial_number )) AS perolehan
			// FROM
			// assembly_details
			// WHERE
			// assembly_details.origin_group_code = 042
			// AND DATE( sedang_start_date ) = DATE(
			// NOW())
			// GROUP BY
			// operator_id UNION ALL
			// SELECT
			// operator_id,
			// count(
			// DISTINCT ( serial_number )) AS perolehan
			// FROM
			// assembly_logs
			// WHERE
			// assembly_logs.origin_group_code = 042
			// AND DATE( sedang_start_date ) = DATE(
			// NOW())
			// GROUP BY
			// operator_id
			// ) a
			// GROUP BY
			// a.operator_id
			// ) AS perolehans ON perolehans.operator_id = assembly_operators.employee_id
			// WHERE
			// assembly_operators.origin_group_code = 042
			// " . $addlocation . "
			// ORDER BY
			// location");

        $work_stations = DB::SELECT("SELECT
            assemblies.operator_id,
            employee_syncs.`name`,
            assemblies.sedang_tag,
            assemblies.sedang_serial_number,
            assemblies.sedang_model,
            COALESCE ( perolehans.perolehan, 0 ) AS perolehan,
            SUBSTRING( assembly_operators.location, 1, LENGTH( assembly_operators.location )- 2 ) AS location 
        FROM
            assemblies
            LEFT JOIN employee_syncs ON employee_syncs.employee_id = assemblies.operator_id
            LEFT JOIN assembly_operators ON assemblies.operator_id = assembly_operators.employee_id
            LEFT JOIN (
            SELECT
                a.operator_id,
                sum( a.perolehan ) AS perolehan 
            FROM
                (
                SELECT
                    operator_id,
                    count(
                    DISTINCT ( serial_number )) AS perolehan 
                FROM
                    assembly_details 
                WHERE
                    assembly_details.origin_group_code = 042 
                    AND DATE( sedang_start_date ) = DATE(
                    NOW()) 
                GROUP BY
                    operator_id UNION ALL
                SELECT
                    operator_id,
                    count(
                    DISTINCT ( serial_number )) AS perolehan 
                FROM
                    assembly_logs 
                WHERE
                    assembly_logs.origin_group_code = 042 
                    AND DATE( sedang_start_date ) = DATE(
                    NOW()) 
                GROUP BY
                    operator_id 
                ) a 
            GROUP BY
                a.operator_id 
            ) AS perolehans ON perolehans.operator_id = assembly_operators.employee_id 
        WHERE
            assemblies.origin_group_code = '042' 
            AND assemblies.location = '".$loc."'");

        foreach ($work_stations as $ws) {

            $dt_now = new DateTime();

            $board_sedang = '';
            if ($ws->sedang_serial_number != null) {
                $board_sedang = $ws->sedang_serial_number . ' - ' . $ws->sedang_model;
            } else {
                $board_sedang = '<br>';
            }

            array_push($boards, [
                'ws' => strtoupper($ws->location),
                'employee_id' => $ws->operator_id,
                'employee_name' => strtoupper($ws->name),
                'sedang' => $board_sedang,
                'perolehan' => $ws->perolehan,
            ]);
        }

        $response = array(
            'status' => true,
            'loc' => $loc,
            'boards' => $boards,
        );
        return Response::json($response);
    }

    public function fetchAssemblySaxophoneBoard(Request $request)
    {
        $loc = $request->get('loc');
        $boards = array();

        $now = date('Y-m-d');

        // $locations = explode(",", $loc);
        // $location = "";

        // for($x = 0; $x < count($locations); $x++) {
        //     $location = $location."'".$locations[$x]."'";
        //     if($x != count($locations)-1){
        //         $location = $location.",";
        //     }
        // }
        // if (str_contains($loc,'1') || str_contains($loc,'2') || str_contains($loc,'3') || str_contains($loc,'4') || str_contains($loc,'5')) {
        //     $addlocation = "AND assembly_operators.location IN ( ".$location.")  ";
        // }else{
        //     $addlocation = "AND SUBSTRING( assembly_operators.location, 1, LENGTH( assembly_operators.location )- 2 ) IN ( ".$location.")  ";
        // }

        $work_stations = DB::select("SELECT
				CONCAT( 'Line ', location ) AS location,
				assemblies.operator_id,
				employee_syncs.`name`,
				sedang_serial_number,
				sedang_model,
				COALESCE ( perolehans.perolehan, 0 ) as perolehan
			FROM
				assemblies
				JOIN employee_syncs ON employee_syncs.employee_id = assemblies.operator_id
				LEFT JOIN (
				SELECT
					a.operator_id,
					sum( a.perolehan ) AS perolehan
				FROM
					(
					SELECT
						operator_id,
						count(
						DISTINCT ( serial_number )) AS perolehan
					FROM
						assembly_details
					WHERE
						assembly_details.origin_group_code = 043
						AND DATE( sedang_start_date ) = DATE(
						NOW())
					GROUP BY
						operator_id UNION ALL
					SELECT
						operator_id,
						count(
						DISTINCT ( serial_number )) AS perolehan
					FROM
						assembly_logs
					WHERE
						assembly_logs.origin_group_code = 043
						AND DATE( sedang_start_date ) = DATE(
						NOW())
					GROUP BY
						operator_id
					) a
				GROUP BY
					a.operator_id
				) AS perolehans ON perolehans.operator_id = assemblies.operator_id
			WHERE
				assemblies.origin_group_code = 043
				AND assemblies.location = '" . $request->get('loc') . "'");

        foreach ($work_stations as $ws) {

            $dt_now = new DateTime();
            $board_sedang = '';
            if ($ws->sedang_serial_number != null) {
                $board_sedang = $ws->sedang_serial_number . ' (' . $ws->sedang_model . ')';
            } else {
                $board_sedang = '<br>';
            }

            array_push($boards, [
                'ws' => strtoupper($ws->location),
                'employee_id' => $ws->operator_id,
                'employee_name' => strtoupper($ws->name),
                'sedang' => $board_sedang,
                'perolehan' => $ws->perolehan,
            ]);
        }

        $response = array(
            'status' => true,
            'loc' => $loc,
            'boards' => $boards,
        );
        return Response::json($response);
    }

    public function kensa($location)
    {
        $loc_code = explode('-', $location);
        $process = $loc_code[0];
        $loc_spec = $loc_code[1];

        if ($location == 'kariawase-fungsi') {
            $title = 'Kariawase Kensa Fungsi Flute';
            $title_jp = 'FL仮合わせ機能検査';
        }
        if ($location == 'kariawase-visual') {
            $title = 'Kariawase Kensa Visual Flute';
            $title_jp = 'FL仮合わせ外観検査';
        }
        if ($location == 'perakitanawal-kensa') {
            $title = 'Perakitan Ulang Kensa Flute';
            $title_jp = 'FL再組立検査';
        }
        if ($location == 'tanpoawase-kensa') {
            $title = 'Tanpo Awase Kensa Flute';
            $title_jp = 'FLタンポ合わせ検査';
        }
        if ($location == 'tanpoawase-fungsi') {
            $title = 'Tanpo Awase Kensa Fungsi Flute';
            $title_jp = 'FLタンポ合わせ検査（機能検査）';
        }
        if ($location == 'kango-fungsi') {
            $title = 'Kango Kensa Fungsi (Gata,Seri) Flute';
            $title_jp = 'FL嵌合機能検査（ガタ、セリ';
        }
        if ($location == 'kango-kensa') {
            $title = 'Kango Kensa Visual Flute';
            $title_jp = 'FL嵌合外観検査';
        }
        if ($location == 'renraku-fungsi') {
            $title = 'Renraku Kensa Fungsi Flute';
            $title_jp = 'FL連絡機能検査';
        }
        if ($location == 'qa-fungsi') {
            $title = 'QA Kensa Fungsi Flute';
            $title_jp = 'FL機能検査（QA';
        }
        if ($location == 'fukiage1-visual') {
            $title = 'Fukiage 1 Kensa Visual Flute';
            $title_jp = 'FL拭き上げ外観検査';
        }
        if ($location == 'qa-visual1') {
            $title = 'QA 1 Kensa Visual Flute';
            $title_jp = 'FL外観検査（QA1';
        }
        if ($location == 'qa-visual2') {
            $title = 'QA 2 Kensa Visual Flute';
            $title_jp = 'FL外観検査（QA2）';
        }
        if ($location == 'qa-audit') {
            $title = 'QA Audit Flute';
            $title_jp = '';
        }
        if ($location == 'qa-kensasp') {
            $title = 'QA Kensa SP';
            $title_jp = '特注品QA検査';
        }
        if ($location == 'seasoning-process') {
            $title = 'Seasoning Process';
            $title_jp = 'シーズニング';
        }
        if ($location == 'repair-process') {
            $title = 'Repair Process';
            $title_jp = '修正';
        }

        $operator_qa = DB::select("SELECT
			DISTINCT(assembly_operators.employee_id),
			name
		FROM
			assembly_operators
			LEFT JOIN employee_syncs ON employee_syncs.employee_id = assembly_operators.employee_id
		WHERE
			origin_group_code = '041'
			AND location LIKE '%qa%'");

        if ($location == 'seasoning-process') {
            return view('processes.assembly.flute.seasoning', array(
                'loc' => $location,
                'loc2' => $location,
                'process' => $process,
                'loc_spec' => $loc_spec,
                'operator_qa' => $operator_qa,
                'title' => $title,
                'title_jp' => $title_jp,
            ))->with('page', 'Assembly FL')->with('head', 'Assembly Process')->with('location', $location);
        } else {
            $ng_lists = DB::select("SELECT DISTINCT(ng_name) FROM assembly_ng_lists where origin_group_code = '041' and location = '" . $loc_spec . "' and process = '" . $process . "' and deleted_at is null");

            return view('processes.assembly.flute.kensa', array(
                'ng_lists' => $ng_lists,
                'loc' => $location,
                'loc2' => $location,
                'process' => $process,
                'operator_qa' => $operator_qa,
                'loc_spec' => $loc_spec,
                'title' => $title,
                'title_jp' => $title_jp,
            ))->with('page', 'Assembly FL')->with('head', 'Assembly Process')->with('location', $location);
        }
    }

    public function scanAssemblyOperator(Request $request)
    {

        if (strlen($request->get('employee_id')) == 9) {
            $employee = db::table('assembly_operators')
                ->join('employee_syncs', 'assembly_operators.employee_id', '=', 'employee_syncs.employee_id')
                ->where('employee_syncs.employee_id', '=', $request->get('employee_id'))
                ->first();
        } else {
            $employee = db::table('assembly_operators')
                ->join('employee_syncs', 'assembly_operators.employee_id', '=', 'employee_syncs.employee_id')
                ->where('tag', '=', $this->dec2hex($request->get('employee_id')))
                ->first();
        }

        if ($employee == null) {
            $response = array(
                'status' => false,
                'message' => 'Tag karyawan tidak ditemukan',
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'message' => 'Tag karyawan ditemukan',
            'employee' => $employee,
        );
        return Response::json($response);
    }

    public function scanAssemblyOperatorKensa(Request $request)
    {

        if (str_contains($request->get('employee_id'), 'PI') || str_contains($request->get('employee_id'), 'pi')) {
            $employee = db::table('assembly_operators')->join('employee_syncs', 'assembly_operators.employee_id', '=', 'employee_syncs.employee_id')->where('assembly_operators.employee_id', '=', strtoupper($request->get('employee_id')))->first();
        } else {
            $employee = db::table('assembly_operators')->join('employee_syncs', 'assembly_operators.employee_id', '=', 'employee_syncs.employee_id')->where('tag', '=', strtoupper($this->dec2hex($request->get('employee_id'))))->first();
        }

        if ($employee == null) {
            $response = array(
                'status' => false,
                'message' => 'Tag karyawan tidak ditemukan',
            );
            return Response::json($response);
        } else {
            if ($employee != null) {
                $location = $employee->location;
                $loc = explode("-", $location);
                $number = $loc[2];
                $locfix = $loc[0] . "-" . $loc[1];
                $assemblies = Assembly::where('location', '=', $locfix)->where('location_number', '=', $number)->where('remark', '=', 'OTHER')->first();
                if ($assemblies != null) {
                    $assemblies->online_time = date('Y-m-d H:i:s');
                    $assemblies->operator_id = $employee->employee_id;
                    $assemblies->save();
                    $response = array(
                        'status' => true,
                        'message' => 'Tag karyawan ditemukan',
                        'employee' => $employee,
                    );
                    return Response::json($response);
                } else {
                    $response = array(
                        'status' => false,
                        'message' => 'Tag karyawan tidak ditemukan',
                    );
                    return Response::json($response);
                }
            } else {
                $response = array(
                    'status' => true,
                    'message' => 'Tag karyawan ditemukan',
                    'employee' => $employee,
                    // 'location' => $location
                );
                return Response::json($response);
            }
        }
    }

    public function scanAssemblyKensa(Request $request)
    {

        $details = db::table('assembly_details')->join('employee_syncs', 'assembly_details.operator_id', '=', 'employee_syncs.employee_id')->where('tag', '=', $this->dec2hex($request->get('tag')))->where('origin_group_code', '=', '041')->where('assembly_details.deleted_at', '=', null)->first();

        $details2 = db::table('assembly_details')->join('employee_syncs', 'assembly_details.operator_id', '=', 'employee_syncs.employee_id')->where('tag', '=', $this->dec2hex($request->get('tag')))->where('origin_group_code', '=', '041')->where('assembly_details.deleted_at', '=', null)->orderBy('assembly_details.id', 'desc')->get();

        $employee = db::table('assembly_operators')->join('employee_syncs', 'assembly_operators.employee_id', '=', 'employee_syncs.employee_id')->where('tag', '=', $this->dec2hex($request->get('employee_id')))->first();

        if ($details == null) {
            $response = array(
                'status' => false,
                'message' => 'Serial Number tidak ditemukan',
            );
            return Response::json($response);
        } else {
            if ($request->get('location') == 'qa-fungsi') {
                $detailsfungsi = AssemblyDetail::where('tag', '=', $this->dec2hex($request->get('tag')))->where('origin_group_code', '=', '041')->where('assembly_details.deleted_at', '=', null)->where('location', 'renraku-fungsi')->first();
                if ($detailsfungsi != null) {
                    if ($employee != null && $employee->location != null && count($employee->location) > 0) {
                        $location = $employee->location;
                        $loc = explode("-", $location);
                        $number = $loc[2];
                        $locfix = $loc[0] . "-" . $loc[1];
                        $assemblies = Assembly::where('location', '=', $locfix)->where('location_number', '=', $number)->where('remark', '=', 'OTHER')->first();
                        $assemblies->sedang_tag = strtoupper($this->dec2hex($request->get('tag')));
                        $assemblies->sedang_serial_number = $details->serial_number;
                        $assemblies->sedang_model = $details->model;
                        $assemblies->sedang_time = date('Y-m-d H:i:s');
                        $assemblies->save();
                    }
                    $response = array(
                        'status' => true,
                        'message' => 'Serial Number ditemukan',
                        'details' => $details,
                        'details2' => $details2,
                        'started_at' => date('Y-m-d H:i:s'),
                    );
                    return Response::json($response);
                } else {
                    $response = array(
                        'status' => false,
                        'message' => 'Produk belum melewati Kensa Fungsi Renraku.',
                    );
                    return Response::json($response);
                }
            } elseif ($request->get('location') == 'fukiage1-visual') {
                $detailsvisual1 = AssemblyDetail::where('tag', '=', $this->dec2hex($request->get('tag')))->where('origin_group_code', '=', '041')->where('assembly_details.deleted_at', '=', null)->where('location', 'qa-fungsi')->first();
                if ($detailsvisual1 != null) {
                    if ($employee != null && $employee->location != null && count($employee->location) > 0) {
                        $location = $employee->location;
                        $loc = explode("-", $location);
                        $number = $loc[2];
                        $locfix = $loc[0] . "-" . $loc[1];
                        $assemblies = Assembly::where('location', '=', $locfix)->where('location_number', '=', $number)->where('remark', '=', 'OTHER')->first();
                        $assemblies->sedang_tag = strtoupper($this->dec2hex($request->get('tag')));
                        $assemblies->sedang_serial_number = $details->serial_number;
                        $assemblies->sedang_model = $details->model;
                        $assemblies->sedang_time = date('Y-m-d H:i:s');
                        $assemblies->save();
                    }
                    $response = array(
                        'status' => true,
                        'message' => 'Serial Number ditemukan',
                        'details' => $details,
                        'details2' => $details2,
                        'started_at' => date('Y-m-d H:i:s'),
                    );
                    return Response::json($response);
                } else {
                    $response = array(
                        'status' => false,
                        'message' => 'Produk belum melewati QA Fungsi.',
                    );
                    return Response::json($response);
                }
            } elseif ($request->get('location') == 'qa-visual1') {
                $detailsvisual1 = AssemblyDetail::where('tag', '=', $this->dec2hex($request->get('tag')))->where('origin_group_code', '=', '041')->where('assembly_details.deleted_at', '=', null)->where('location', 'fukiage1-visual')->first();
                if ($detailsvisual1 != null) {
                    if ($employee != null && $employee->location != null && count($employee->location) > 0) {
                        $location = $employee->location;
                        $loc = explode("-", $location);
                        $number = $loc[2];
                        $locfix = $loc[0] . "-" . $loc[1];
                        $assemblies = Assembly::where('location', '=', $locfix)->where('location_number', '=', $number)->where('remark', '=', 'OTHER')->first();
                        $assemblies->sedang_tag = strtoupper($this->dec2hex($request->get('tag')));
                        $assemblies->sedang_serial_number = $details->serial_number;
                        $assemblies->sedang_model = $details->model;
                        $assemblies->sedang_time = date('Y-m-d H:i:s');
                        $assemblies->save();
                    }
                    $response = array(
                        'status' => true,
                        'message' => 'Serial Number ditemukan',
                        'details' => $details,
                        'details2' => $details2,
                        'started_at' => date('Y-m-d H:i:s'),
                    );
                    return Response::json($response);
                } else {
                    $response = array(
                        'status' => false,
                        'message' => 'Produk belum melewati Kensa Visual Akhir Proses.',
                    );
                    return Response::json($response);
                }
            } elseif ($request->get('location') == 'qa-visual2') {
                $detailsvisual1 = AssemblyDetail::where('tag', '=', $this->dec2hex($request->get('tag')))->where('origin_group_code', '=', '041')->where('assembly_details.deleted_at', '=', null)->where('location', 'qa-visual1')->first();
                if ($detailsvisual1 != null) {
                    if ($employee != null && $employee->location != null && count($employee->location) > 0) {
                        $location = $employee->location;
                        $loc = explode("-", $location);
                        $number = $loc[2];
                        $locfix = $loc[0] . "-" . $loc[1];
                        $assemblies = Assembly::where('location', '=', $locfix)->where('location_number', '=', $number)->where('remark', '=', 'OTHER')->first();
                        $assemblies->sedang_tag = strtoupper($this->dec2hex($request->get('tag')));
                        $assemblies->sedang_serial_number = $details->serial_number;
                        $assemblies->sedang_model = $details->model;
                        $assemblies->sedang_time = date('Y-m-d H:i:s');
                        $assemblies->save();
                    }
                    $response = array(
                        'status' => true,
                        'message' => 'Serial Number ditemukan',
                        'details' => $details,
                        'details2' => $details2,
                        'started_at' => date('Y-m-d H:i:s'),
                    );
                    return Response::json($response);
                } else {
                    $response = array(
                        'status' => false,
                        'message' => 'Produk belum melewati QA Visual 1.',
                    );
                    return Response::json($response);
                }
            } else {
                if ($employee != null && $employee->location != null && count($employee->location) > 0) {
                    $location = $employee->location;
                    $loc = explode("-", $location);
                    $number = $loc[2];
                    $locfix = $loc[0] . "-" . $loc[1];
                    $assemblies = Assembly::where('location', '=', $locfix)->where('location_number', '=', $number)->where('remark', '=', 'OTHER')->first();
                    $assemblies->sedang_tag = strtoupper($this->dec2hex($request->get('tag')));
                    $assemblies->sedang_serial_number = $details->serial_number;
                    $assemblies->sedang_model = $details->model;
                    $assemblies->sedang_time = date('Y-m-d H:i:s');
                    $assemblies->save();
                }
                $response = array(
                    'status' => true,
                    'message' => 'Serial Number ditemukan',
                    'details' => $details,
                    'details2' => $details2,
                    'started_at' => date('Y-m-d H:i:s'),
                );
                return Response::json($response);
            }
        }
    }

    public function deleteAssemblyKensa(Request $request)
    {
        $employee = db::table('assembly_operators')->join('employee_syncs', 'assembly_operators.employee_id', '=', 'employee_syncs.employee_id')->where('tag', '=', $this->dec2hex($request->get('employee_id')))->first();

        if ($employee == null) {
            $response = array(
                'status' => false,
                'message' => 'Gagal Hapus Assemblies',
            );
            return Response::json($response);
        } else {
            $location = $employee->location;
            $loc = explode("-", $location);
            $number = $loc[2];
            $locfix = $loc[0] . "-" . $loc[1];
            $assemblies = Assembly::where('location', '=', $locfix)->where('location_number', '=', $number)->where('remark', '=', 'OTHER')->first();
            $assemblies->sedang_tag = null;
            $assemblies->sedang_serial_number = null;
            $assemblies->sedang_model = null;
            $assemblies->sedang_time = null;
            $assemblies->save();

            $response = array(
                'status' => true,
                'message' => 'Berhasil Hapus Assemblies',
            );
            return Response::json($response);
        }
    }

    public function showNgDetail(Request $request)
    {

        $ng_detail = db::select("select * from assembly_ng_lists where ng_name = '" . $request->get('ng_name') . "' and location = '" . $request->get('location') . "' and process = '" . $request->get('process') . "' and origin_group_code = '041'");

        if ($request->get('ng_name') == 'Renraku') {
            $onko = DB::select("SELECT DISTINCT(assembly_onkos.key),nomor FROM assembly_onkos where origin_group_code = '041' and location = 'renraku'");
            $onko_detail = DB::select("SELECT assembly_onkos.key,nomor,keynomor FROM assembly_onkos where origin_group_code = '041' and location = 'renraku'");
        } else {
            $onko = DB::select("SELECT * FROM assembly_onkos where origin_group_code = '041' and location = 'all'");
            $onko_detail = DB::select("SELECT assembly_onkos.key,nomor,keynomor FROM assembly_onkos where origin_group_code = '041' and location = 'all'");
        }

        if ($ng_detail == null) {
            $response = array(
                'status' => false,
                'message' => 'NG Detail Tidak Ditemukan',
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'ng_detail' => $ng_detail,
            'onko' => $onko,
            'onko_detail' => $onko_detail,
        );
        return Response::json($response);
    }

    public function fetchNgTemp(Request $request)
    {
        $model = $request->get('model');
        $serial_number = $request->get('serial_number');
        $employee_id = $request->get('employee_id');
        $tag = $this->dec2hex($request->get('tag'));

        $ng_temp = db::select("SELECT
			*,
			IF ( assembly_ng_temps.operator_id LIKE '%PI%',( SELECT NAME FROM employee_syncs WHERE employee_syncs.employee_id = assembly_ng_temps.operator_id ),
			assembly_ng_temps.operator_id
			) AS name
			FROM
			`assembly_ng_temps`
			WHERE
			model = '" . $model . "'
			AND serial_number = '" . $serial_number . "'
			AND employee_id = '" . $employee_id . "'
			AND tag = '" . $tag . "'
			AND deleted_at is null");

        if ($ng_temp == null) {
            $response = array(
                'status' => false,
                'message' => 'NG Detail Tidak Ditemukan',
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            // 'message' => 'Tag karyawan ditemukan',
            'ng_temp' => $ng_temp,
        );
        return Response::json($response);
    }

    public function fetchNgLogs(Request $request)
    {
        $model = $request->get('model');
        $serial_number = $request->get('serial_number');
        $employee_id = $request->get('employee_id');
        $tag = $this->dec2hex($request->get('tag'));

        $ng_logs = db::select("SELECT
			*,
			IF ( assembly_ng_logs.operator_id LIKE '%PI%',( SELECT NAME FROM employee_syncs WHERE employee_syncs.employee_id = assembly_ng_logs.operator_id ),
			assembly_ng_logs.operator_id
			) AS name
			FROM
			`assembly_ng_logs`
			WHERE
			model = '" . $model . "'
			AND serial_number = '" . $serial_number . "'
			AND tag = '" . $tag . "'
			AND deleted_at is null
			order by id desc");

        if ($ng_logs == null) {
            $response = array(
                'status' => false,
                'message' => 'NG Detail Tidak Ditemukan',
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            // 'message' => 'Tag karyawan ditemukan',
            'ng_logs' => $ng_logs,
        );
        return Response::json($response);
    }

    public function inputRepairProcess(Request $request)
    {
        try {
            if ($request->get('ganti') == 'repair') {
                $repair = AssemblyNgLog::where('id', $request->get('id'))->first();
                $repair->repair_status = 'Repaired';
                $repair->decision = 'Tidak Ganti';
                $repair->repaired_by = $request->get('employee_id');
                $repair->repaired_at = date('Y-m-d H:i:s');
                $repair->save();
            } else if ($request->get('ganti') == 'verif') {
                $repair = AssemblyNgLog::where('id', $request->get('id'))->first();
                $repair->verified_by = $request->get('employee_id');
                $repair->verified_at = date('Y-m-d H:i:s');
                $repair->save();
            } else {
                $repair = AssemblyNgLog::where('id', $request->get('id'))->first();
                $repair->decision = 'Ganti Kunci';
                $repair->save();
            }

            $response = array(
                'status' => true,
                'message' => 'NG Repaired',
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'details' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function getProcessBefore(Request $request)
    {
        $model = $request->get('model');
        $serial_number = $request->get('serial_number');
        $tag = $this->dec2hex($request->get('tag'));
        $location = $request->get('process_before');

        if ($location == 'tanpoawase-process') {
            $details = db::select("SELECT
				assembly_operators.employee_id AS operator_id,
				employee_syncs.`name`
				FROM
				`assembly_operators`
				JOIN employee_syncs ON employee_syncs.employee_id = assembly_operators.employee_id
				WHERE
				location LIKE '%tanpoawase-process%'
				and assembly_operators.origin_group_code = '041'");
        } else {
            $details = db::select("SELECT
				*
				FROM
				`assembly_details`
				join employee_syncs on employee_syncs.employee_id = assembly_details.operator_id
				WHERE
				model = '" . $model . "'
				AND tag = '" . $tag . "'
				AND serial_number = '" . $serial_number . "'
				and assembly_details.origin_group_code = '041'");
        }

        if ($details == null) {
            $response = array(
                'status' => false,
                'message' => 'Data Tidak Ditemukan',
                'details' => $details,
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'details' => $details,
        );
        return Response::json($response);
    }

    public function fetchOnko(Request $request)
    {
        $location = $request->get('process');

        $onko = DB::select("SELECT * FROM assembly_onkos where origin_group_code = '041' and location = '" . $location . "' ORDER BY `key`");

        if ($onko == null) {
            $response = array(
                'status' => false,
                'message' => 'NG Onko Tidak Ditemukan',
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            // 'message' => 'Tag karyawan ditemukan',
            'onko' => $onko,
        );
        return Response::json($response);
    }

    public function inputNgTemp(Request $request)
    {
        try {
            if ($request->get('ng') == 'Tanpo Awase') {
                $value_atas = $request->get('value_atas');
                $value_bawah = $request->get('value_bawah');
                $ongko = $request->get('onko');
                $lokasi = $request->get('lokasi');
                $operator = $request->get('operator_id');

                // if ($operator == '' || $operator == null || count($operator) == 0) {
                //     $response = array(
                //         'status' => false,
                //         'message' => 'Pilih Operator Penghasil NG',
                //     );
                //     return Response::json($response);
                // }

                for ($i = 0; $i < count($ongko); $i++) {
                    $assembly_ng_temp = new AssemblyNgTemp([
                        'employee_id' => $request->get('employee_id'),
                        'tag' => strtoupper($this->dec2hex($request->get('tag'))),
                        'serial_number' => $request->get('serial_number'),
                        'model' => $request->get('model'),
                        'location' => $request->get('location'),
                        'ng_name' => $request->get('ng'),
                        'ongko' => $ongko[$i],
                        'value_atas' => $value_atas[$i],
                        'value_bawah' => $value_bawah[$i],
                        'value_lokasi' => $lokasi[$i],
                        'operator_id' => $operator[$i],
                        'started_at' => $request->get('started_at'),
                        'origin_group_code' => $request->get('origin_group_code'),
                        'created_by' => Auth::id(),
                    ]);

                    $assembly_ng_temp->save();
                }

                $response = array(
                    'status' => true,
                    'message' => 'Success Input NG',
                );
                return Response::json($response);
            } else {
                if (!str_contains($request->get('location'), 'qa')) {
                    if (str_contains($request->get('ng'), 'Kizu') || str_contains($request->get('ng'), 'kizu')) {
                        if ($request->get('operator_id') == '' || $request->get('operator_id') == null || count($request->get('operator_id')) == 0) {
                            $response = array(
                                'status' => false,
                                'message' => 'Pilih Operator Penghasil NG',
                            );
                            return Response::json($response);
                        }
                    }
                }

                $assembly_ng_temp = new AssemblyNgTemp([
                    'employee_id' => $request->get('employee_id'),
                    'tag' => strtoupper($this->dec2hex($request->get('tag'))),
                    'serial_number' => $request->get('serial_number'),
                    'model' => $request->get('model'),
                    'location' => $request->get('location'),
                    'ng_name' => $request->get('ng'),
                    'value_atas' => 1,
                    'ongko' => $request->get('onko'),
                    'operator_id' => $request->get('operator_id'),
                    'started_at' => $request->get('started_at'),
                    'origin_group_code' => $request->get('origin_group_code'),
                    'created_by' => Auth::id(),
                ]);

                $assembly_ng_temp->save();

                $response = array(
                    'status' => true,
                    'message' => 'Success Input NG',
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

    public function inputGantiKunci(Request $request)
    {
        if ($request->get('ng')) {
            $assembly_ng_temp = new AssemblyNgTemp([
                'employee_id' => $request->get('employee_id'),
                'tag' => strtoupper($this->dec2hex($request->get('tag'))),
                'serial_number' => $request->get('serial_number'),
                'model' => $request->get('model'),
                'location' => $request->get('location'),
                'ng_name' => $request->get('ng'),
                'value_atas' => 1,
                'ongko' => $request->get('onko'),
                'decision' => 'Ganti Kunci',
                'operator_id' => $request->get('employee_id'),
                'started_at' => $request->get('started_at'),
                'origin_group_code' => $request->get('origin_group_code'),
                'created_by' => Auth::id(),
            ]);

            try {
                $assembly_ng_temp->save();
                $response = array(
                    'status' => true,
                    'message' => 'Sukses Input NG',
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
                'message' => 'Gagal Input NG',
            );
            return Response::json($response);
        }
    }

    public function deleteNgTemp(Request $request)
    {
        $model = $request->get('model');
        $serial_number = $request->get('serial_number');
        $employee_id = $request->get('employee_id');
        $tag = $this->dec2hex($request->get('tag'));

        $ng_temp = AssemblyNgTemp::where('model', $model)->where('serial_number', $serial_number)->where('employee_id', $employee_id)->where('tag', $tag)->delete();

        $response = array(
            'status' => true,
            'message' => 'Temp Deleted',
        );
        return Response::json($response);
    }

    public function inputAssemblyKensa(Request $request)
    {
        if ($request->get('tag')) {
            $model = $request->get('model');
            $serial_number = $request->get('serial_number');
            $employee_id = $request->get('employee_id');
            $tag = strtoupper($this->dec2hex($request->get('tag')));

            $started_at = "";
            $finished_at = date('Y-m-d H:i:s');

            $ng_temp = AssemblyNgTemp::where('serial_number', $serial_number)->where('employee_id', $employee_id)->where('tag', $tag)->where('origin_group_code', '041')->get();
            $jumlah_ng = 0;
            foreach ($ng_temp as $ng) {
                if ($ng->ng_name == 'Ganti Kunci - Ganti Kunci') {
                    $assembly_ng_log = new AssemblyNgLog([
                        'employee_id' => $request->get('employee_id'),
                        'tag' => strtoupper($this->dec2hex($request->get('tag'))),
                        'serial_number' => $request->get('serial_number'),
                        'model' => $request->get('model'),
                        'location' => $request->get('location'),
                        'ongko' => $ng->ongko,
                        'ng_name' => $ng->ng_name,
                        'value_atas' => $ng->value_atas,
                        'value_bawah' => $ng->value_bawah,
                        'value_lokasi' => $ng->value_lokasi,
                        'operator_id' => $ng->operator_id,
                        'sedang_start_date' => $ng->started_at,
                        'sedang_finish_date' => $finished_at,
                        'repair_status' => 'Repaired',
                        'repaired_by' => $request->get('employee_id'),
                        'repaired_at' => date("Y-m-d H:i:s"),
                        'origin_group_code' => $request->get('origin_group_code'),
                        'created_by' => Auth::id(),
                    ]);
                } else {
                    $assembly_ng_log = new AssemblyNgLog([
                        'employee_id' => $request->get('employee_id'),
                        'tag' => strtoupper($this->dec2hex($request->get('tag'))),
                        'serial_number' => $request->get('serial_number'),
                        'model' => $request->get('model'),
                        'location' => $request->get('location'),
                        'ongko' => $ng->ongko,
                        'ng_name' => $ng->ng_name,
                        'value_atas' => $ng->value_atas,
                        'value_bawah' => $ng->value_bawah,
                        'value_lokasi' => $ng->value_lokasi,
                        'operator_id' => $ng->operator_id,
                        'sedang_start_date' => $ng->started_at,
                        'sedang_finish_date' => $finished_at,
                        'origin_group_code' => $request->get('origin_group_code'),
                        'created_by' => Auth::id(),
                    ]);
                }

                $started_at = $ng->started_at;

                try {
                    $assembly_ng_log->save();
                } catch (\Exception$e) {
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                    return Response::json($response);
                }
                $jumlah_ng++;
            }

            $assembly_invent = AssemblyInventory::where('serial_number', $serial_number)->where('tag', $tag)->where('origin_group_code', '041')->first();

            $remark = $assembly_invent->remark;

            // if ($remark == 'SP') {
            //     if ($jumlah_ng == 0) {
            //         // $assembly_details = AssemblyDetail::where('serial_number',$serial_number)->where('tag',$tag)->where('origin_group_code','041')->delete();

            //         $detail = new AssemblyLog([
            //             'tag' => strtoupper($this->dec2hex($request->get('tag'))),
            //             'serial_number' => $request->get('serial_number'),
            //             'model' => $request->get('model'),
            //             'location' => $request->get('location'),
            //             'operator_id' => $request->get('employee_id'),
            //             'sedang_start_date' => $request->get('started_at'),
            //             'sedang_finish_date' => $finished_at,
            //             'origin_group_code' => $request->get('origin_group_code'),
            //             'created_by' => $request->get('employee_id')
            //         ]);
            //         try{
            //             $detail->save();
            //         }
            //         catch(\Exception $e){
            //             $response = array(
            //                 'status' => false,
            //                 'message' => $e->getMessage(),
            //             );
            //             return Response::json($response);
            //         }
            //     }else{
            //         $assembly_details = new AssemblyDetail([
            //             'tag' => strtoupper($this->dec2hex($request->get('tag'))),
            //             'serial_number' => $request->get('serial_number'),
            //             'model' => $request->get('model'),
            //             'location' => $request->get('location'),
            //             'operator_id' => $request->get('employee_id'),
            //             'sedang_start_date' => $request->get('started_at'),
            //             'sedang_finish_date' => $finished_at,
            //             'origin_group_code' => $request->get('origin_group_code'),
            //             'created_by' => $request->get('employee_id')
            //         ]);

            //         try{
            //             $assembly_details->save();
            //         }
            //         catch(\Exception $e){
            //             $response = array(
            //                 'status' => false,
            //                 'message' => $e->getMessage(),
            //             );
            //             return Response::json($response);
            //         }
            //     }

            // }else{
            $assembly_details = new AssemblyDetail([
                'tag' => strtoupper($this->dec2hex($request->get('tag'))),
                'serial_number' => $request->get('serial_number'),
                'model' => $request->get('model'),
                'location' => $request->get('location'),
                'operator_id' => $request->get('employee_id'),
                'sedang_start_date' => $request->get('started_at'),
                'sedang_finish_date' => $finished_at,
                'origin_group_code' => $request->get('origin_group_code'),
                'operator_audited' => $request->get('operator_qa'),
                'note' => $request->get('note'),
                'created_by' => $request->get('employee_id'),
            ]);

            try {
                $assembly_details->save();
            } catch (\Exception$e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
            // }

            try {

                $assembly_inventories = AssemblyInventory::where('serial_number', $serial_number)->where('tag', $tag)->where('origin_group_code', '041')->first();
                $assembly_inventories->location = $request->get('location');
                $assembly_inventories->created_by = $request->get('employee_id');

                $location_next = $assembly_inventories->location_next;

                $assembly_flow = AssemblyFlow::where('process', $location_next)->where('origin_group_code', '041')->first();
                $id_flow_now = $assembly_flow->id;
                $id_flow_next = $id_flow_now + 1;
                $assembly_flow_next = AssemblyFlow::where('id', $id_flow_next)->where('origin_group_code', '041')->first();
                $next_process = $assembly_flow_next->process;
                if (count($next_process) > 0 && $next_process != "") {
                    $assembly_inventories->location_next = $next_process;
                }
                $assembly_inventories->save();

            } catch (\Exception$e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }

            $response = array(
                'status' => true,
                'message' => 'Sukses Input NG',
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'message' => 'Gagal Input NG',
            );
            return Response::json($response);
        }
    }

    public function inputAssemblySeasoning(Request $request)
    {
        try {
            $inventories = AssemblyInventory::where('tag', strtoupper($this->dec2hex($request->get('tag'))))->where('origin_group_code', '041')->first();
            $inventories->location = $request->get('location');
            $inventories->created_by = $request->get('employee_id');

            $flow = AssemblyFlow::where('process', $request->get('location'))->where('origin_group_code', '041')->first();
            $next = $flow->flow + 1;
            $flownew = AssemblyFlow::where('flow', $next)->where('origin_group_code', '041')->first();

            if ($flownew != null) {
                $inventories->location_next = $flownew->process;
            }

            $log = new AssemblyDetail([
                'tag' => $inventories->tag,
                'serial_number' => $inventories->serial_number,
                'model' => $inventories->model,
                'location' => $request->get('location'),
                'operator_id' => $request->get('employee_id'),
                'sedang_start_date' => date('Y-m-d H:i:s'),
                'sedang_finish_date' => date('Y-m-d H:i:s'),
                'origin_group_code' => '041',
                'created_by' => $request->get('employee_id'),
                'is_send_log' => 0,
            ]);

            $inventories->save();
            $log->save();

            $response = array(
                'status' => true,
                'message' => 'Sukses Input Seasoning',
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => 'Gagal Input NG',
            );
            return Response::json($response);
        }
    }

    public function fetchAssembly(Request $request)
    {
        try {
            $assembly = DB::SELECT("SELECT DISTINCT
				assembly_details.serial_number,
				assembly_details.model,
				assembly_details.sedang_start_date AS start_at,
				employee_syncs.employee_id,
				employee_syncs.name
				FROM
				assembly_details
				JOIN employee_syncs ON employee_syncs.employee_id = assembly_details.operator_id
				WHERE
				location = '" . $request->get('location') . "'
				and DATE(assembly_details.created_at) = DATE(NOW())");

            $response = array(
                'status' => true,
                'assembly' => $assembly,
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => 'Failed',
            );
            return Response::json($response);
        }
    }

    public function indexRequestDisplay($origin_group_code)
    {
        return view('processes.assembly.flute.display.assembly_request', array(
            'title' => 'Assembly Request Material',
            'title_jp' => '組立依頼材料',
            'origin_group_code' => $origin_group_code))
            ->with('page', 'Assembly Request Material');
    }

    public function fetchRequest(Request $request)
    {
        $origin_group_code = $request->get('origin_group_code');
        $stamps = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%stamp%" and origin_group_code = "' . $origin_group_code . '" GROUP BY model');
        $perakitans = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%perakitan%" and origin_group_code = "' . $origin_group_code . '" GROUP BY model');
        $kariawases = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%kariawase%" and origin_group_code = "' . $origin_group_code . '" GROUP BY model');
        $tanpoires = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%tanpoire%" and origin_group_code = "' . $origin_group_code . '" GROUP BY model');
        $tanpoawases = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%tanpoawase%" and origin_group_code = "' . $origin_group_code . '" GROUP BY model');
        $seasonings = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%seasoning%" and origin_group_code = "' . $origin_group_code . '" GROUP BY model');
        $kangos = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%kango%" and origin_group_code = "' . $origin_group_code . '" GROUP BY model');
        $renrakus = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%renraku%" and origin_group_code = "' . $origin_group_code . '" GROUP BY model');
        $fukiage1s = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%fukiage1%" and origin_group_code = "' . $origin_group_code . '" GROUP BY model');
        $fukiage2s = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%fukiage2%" and origin_group_code = "' . $origin_group_code . '" GROUP BY model');
        $qas = DB::select('select model,count(serial_number) as qty from assembly_inventories where location like "%qa%" and origin_group_code = "' . $origin_group_code . '" GROUP BY model');

        $models = DB::SELECT('SELECT DISTINCT(model) FROM `materials` where origin_group_code = "041" and category = "FG"');

        $log_request = array();

        foreach ($models as $key) {

            $qty_stamp = 0;
            foreach ($stamps as $stamp) {
                if ($key->model == $stamp->model) {
                    $qty_stamp = $stamp->qty;
                }
            }

            $qty_perakitan = 0;
            foreach ($perakitans as $perakitan) {
                if ($key->model == $perakitan->model) {
                    $qty_perakitan = $perakitan->qty;
                }
            }

            $qty_kariawase = 0;
            foreach ($kariawases as $kariawase) {
                if ($key->model == $kariawase->model) {
                    $qty_kariawase = $kariawase->qty;
                }
            }

            $qty_tanpoire = 0;
            foreach ($tanpoires as $tanpoire) {
                if ($key->model == $tanpoire->model) {
                    $qty_tanpoire = $tanpoire->qty;
                }
            }

            $qty_tanpoawase = 0;
            foreach ($tanpoawases as $tanpoawase) {
                if ($key->model == $tanpoawase->model) {
                    $qty_tanpoawase = $tanpoawase->qty;
                }
            }

            $qty_seasoning = 0;
            foreach ($seasonings as $seasoning) {
                if ($key->model == $seasoning->model) {
                    $qty_seasoning = $seasoning->qty;
                }
            }

            $qty_kango = 0;
            foreach ($kangos as $kango) {
                if ($key->model == $kango->model) {
                    $qty_kango = $kango->qty;
                }
            }

            $qty_renraku = 0;
            foreach ($renrakus as $renraku) {
                if ($key->model == $renraku->model) {
                    $qty_renraku = $renraku->qty;
                }
            }

            $qty_fukiage1 = 0;
            foreach ($fukiage1s as $fukiage1) {
                if ($key->model == $fukiage1->model) {
                    $qty_fukiage1 = $fukiage1->qty;
                }
            }

            $qty_fukiage2 = 0;
            foreach ($fukiage2s as $fukiage2) {
                if ($key->model == $fukiage2->model) {
                    $qty_fukiage2 = $fukiage2->qty;
                }
            }

            $qty_qa = 0;
            foreach ($qas as $qa) {
                if ($key->model == $qa->model) {
                    $qty_qa = $qa->qty;
                }
            }

            array_push($log_request, [
                "model" => $key->model,
                "stamp" => $qty_stamp,
                "perakitan" => $qty_perakitan,
                "kariawase" => $qty_kariawase,
                "tanpoire" => $qty_tanpoire,
                "tanpoawase" => $qty_tanpoawase,
                "seasoning" => $qty_seasoning,
                "kango" => $qty_kango,
                "renraku" => $qty_renraku,
                "fukiage1" => $qty_fukiage1,
                "fukiage2" => $qty_fukiage2,
                "qa" => $qty_qa,
            ]);
        }

        $response = array(
            'status' => true,
            'datas' => $log_request,
        );
        return Response::json($response);
    }

    public function indexOngoing($origin_group_code, $line)
    {
        if ($origin_group_code == '043') {
            $view = 'processes.assembly.display.ongoing_sax';
        } else if ($origin_group_code == '042') {
            $view = 'processes.assembly.display.ongoing_cl';
        }
        return view($view, array(
            'title' => 'Ongoing Assembly Process',
            'title_jp' => '',
            'origin_group_code' => $origin_group_code,
            'line' => $line,
        ))->with('page', 'Assembly Process');
    }

    public function fetchOngoing(Request $request)
    {
        try {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = date('Y-m-01');
                    $last = date('Y-m-d');
                } else {
                    $first = date('Y-m-01');
                    $last = $date_to;
                }
            } else {
                if ($date_to == "") {
                    $first = $date_from;
                    $last = date('Y-m-d');
                } else {
                    $first = $date_from;
                    $last = $date_to;
                }
            }
            // $datas = DB::SELECT("SELECT
            //         a.operator_id,
            //         a.location,
            //         employee_syncs.`name`,
            //         sum( a.actual ) AS actual,
            //         sum( a.actual_time ) AS actual_time,
            //     IF
            //         ( a.location = '5', 'Tenor', 'Alto' ) AS base_model,
            //     IF
            //         ( a.location = '5', '13.67', '8.45' ) AS std_time,
            //     IF
            //         ( a.location = '5', plan.plan, ROUND( plan.plan / 4 ) ) AS plan,
            //         tag.tag_time
            //     FROM
            //         (
            //         SELECT
            //             operator_id,
            //             count( serial_number ) AS actual,
            //             location,
            //             SUM((
            //                 TIMESTAMPDIFF( MINUTE, assembly_details.sedang_start_date, assembly_details.sedang_finish_date ))) AS actual_time
            //         FROM
            //             assembly_details
            //         WHERE
            //             origin_group_code = '".$request->get('origin_group_code')."'
            //             AND DATE_FORMAT( sedang_start_date,'%Y-%m-%d' ) = '".date('Y-m-d')."'
            //             AND location IN ( 1, 2, 3, 4, 5 )
            //         GROUP BY
            //             operator_id,
            //             location UNION ALL
            //         SELECT
            //             operator_id,
            //             count( serial_number ) AS actual,
            //             location,
            //             SUM((
            //                 TIMESTAMPDIFF( MINUTE, assembly_logs.sedang_start_date, assembly_logs.sedang_finish_date ))) AS actual_time
            //         FROM
            //             assembly_logs
            //         WHERE
            //             origin_group_code = '".$request->get('origin_group_code')."'
            //             AND DATE_FORMAT( sedang_start_date,'%Y-%m-%d' ) = '".date('Y-m-d')."'
            //             AND location IN ( 1, 2, 3, 4, 5 )
            //         GROUP BY
            //             operator_id,
            //             location
            //         ) AS a
            //         LEFT JOIN (
            //         SELECT
            //         IF
            //             ( SPLIT_STRING ( model, 'S', 1 ) = 'YA', 'Alto', 'Tenor' ) AS models,
            //             sum( quantity ) AS plan
            //         FROM
            //             production_schedules
            //             JOIN materials ON materials.material_number = production_schedules.material_number
            //         WHERE
            //             due_date = '".date('Y-m-d')."'
            //             AND category = 'FG'
            //             AND origin_group_code = '".$request->get('origin_group_code')."'
            //         GROUP BY
            //             models
            //         ) AS plan ON plan.models =
            //     IF
            //         ( a.location = '5', 'Tenor', 'Alto' )
            //         LEFT JOIN employee_syncs ON employee_syncs.employee_id = a.operator_id
            //         LEFT JOIN (SELECT
            //             b.operator_id,
            //             b.location,
            //             sum(b.tag_time) as tag_time
            //         FROM
            //             (
            //             SELECT DISTINCT
            //                 ( operator_id ),
            //                 location,
            //                 COALESCE ( TIMESTAMPDIFF( MINUTE, assemblies.sedang_time, DATE_FORMAT( NOW(), '%Y-%m-%d %H:%i:%s' ) ), 0 ) AS tag_time
            //             FROM
            //                 assemblies
            //             WHERE
            //                 origin_group_code = '043'
            //                 AND operator_id IS NOT NULL
            //             AND location IN ( 1, 2, 3, 4, 5 )) b
            //         GROUP BY
            //             b.location,
            //             b.operator_id) as tag on tag.operator_id = a.operator_id and tag.location = a.location
            //     GROUP BY
            //         a.operator_id,
            //         employee_syncs.`name`,
            //         a.location,
            //         plan.plan,
            //         tag.tag_time");

            $origin_group_code = $request->get('origin_group_code');
            $line = $request->get('line');

            if ($origin_group_code == "043") {
                $cycle = DB::SELECT("SELECT
					location,
					MIN( cycle ) AS cycle
				FROM
					assemblies
				WHERE
					origin_group_code = '" . $origin_group_code . "'
					AND location IN ( 1, 2, 3, 4, 5 )
					AND operator_id IS NOT NULL
				GROUP BY
					location");

                $tact_time = DB::SELECT("SELECT
					b.operator_id,
					b.location,
					cycle,
					COALESCE ( b.elapsed_time, 0 ) AS elapsed_time,
					COALESCE ( b.prev_time, 0 ) AS prev_time,
					employee_syncs.`name`
				FROM
					(
					SELECT
						operator_id,
						location,
						cycle,
						location_number,
						TIMESTAMPDIFF( SECOND, assemblies.sedang_time, DATE_FORMAT( NOW(), '%Y-%m-%d %H:%i:%s' ) )/ 60 AS elapsed_time,
						TIMESTAMPDIFF( SECOND, assemblies.prev_start_time, assemblies.prev_end_time )/ 60 AS prev_time
					FROM
						assemblies
					WHERE
						(
							origin_group_code = '" . $origin_group_code . "'
							AND location = '" . $line . "'
							AND operator_id IS NOT NULL
						AND location IN ( 1, 2, 3, 4, 5 )) ) b
					JOIN employee_syncs ON employee_syncs.employee_id = b.operator_id
				ORDER BY
					b.location,
					b.location_number");

            } else if ($origin_group_code == "042") {
                $cycle = DB::SELECT("SELECT
					location,
					1 AS cycle
				FROM
					assemblies
				WHERE
					origin_group_code = '" . $origin_group_code . "'
					AND operator_id IS NOT NULL
				GROUP BY
					location");

                $tact_time = DB::SELECT("
					SELECT
					b.operator_id,
					b.location,
					cycle,
					COALESCE ( b.elapsed_time, 0 ) AS elapsed_time,
					COALESCE ( b.prev_time, 0 ) AS prev_time,
					employee_syncs.`name`
				FROM
					(
					SELECT
						operator_id,
						assembly_operators.location,
						cycle,
						location_number,
						TIMESTAMPDIFF( SECOND, assemblies.sedang_time, DATE_FORMAT( NOW(), '%Y-%m-%d %H:%i:%s' ) )/ 60 AS elapsed_time,
						TIMESTAMPDIFF( SECOND, assemblies.prev_start_time, assemblies.prev_end_time )/ 60 AS prev_time
					FROM
						assemblies
					JOIN assembly_operators
				on assemblies.operator_id = assembly_operators.employee_id
					WHERE
						(
							assemblies.origin_group_code = '" . $origin_group_code . "'
							AND assembly_operators.location like '%" . $line . "%'
							AND operator_id IS NOT NULL
						)) b
					JOIN employee_syncs ON employee_syncs.employee_id = b.operator_id
				ORDER BY
					b.location,
					b.location_number");
            }

            $average = DB::SELECT("SELECT
				a.operator_id,
				employee_syncs.`name`,
				a.location,
				a.location_number,
				count( a.serial_number ) AS qty,
				sum( a.tact_time ) AS actual_time,
				ROUND( sum( a.tact_time )/ COUNT( a.serial_number ), 1 ) AS tact_time
			FROM
				(
				SELECT
					assembly_logs.operator_id,
					assembly_logs.location,
					COALESCE ( COALESCE ( assembly_logs.location_number, assemblies.location_number ), 1 ) AS location_number,
					serial_number,
					TIMESTAMPDIFF( MINUTE, sedang_start_date, sedang_finish_date ) AS tact_time
				FROM
					assembly_logs
					LEFT JOIN assemblies ON assemblies.operator_id = assembly_logs.operator_id
					AND assemblies.location = assembly_logs.location
				WHERE
					date( sedang_start_date ) = '" . date('Y-m-d') . "'
					AND assembly_logs.origin_group_code = '" . $origin_group_code . "'
					AND assembly_logs.location = '" . $line . "' UNION ALL
				SELECT
					assembly_details.operator_id,
					assembly_details.location,
					COALESCE ( COALESCE ( assembly_details.location_number, assemblies.location_number ), 1 ) AS location_number,
					serial_number,
					TIMESTAMPDIFF( MINUTE, sedang_start_date, sedang_finish_date ) AS tact_time
				FROM
					assembly_details
					LEFT JOIN assemblies ON assemblies.operator_id = assembly_details.operator_id
					AND assemblies.location = assembly_details.location
				WHERE
					date( sedang_start_date ) = '" . date('Y-m-d') . "'
					AND assembly_details.origin_group_code = '" . $origin_group_code . "'
					AND assembly_details.location = '" . $line . "') a
				JOIN employee_syncs ON employee_syncs.employee_id = a.operator_id
			GROUP BY
				a.operator_id,
				a.location_number,
				a.location,
				employee_syncs.`name`
			ORDER BY
				a.location,
				a.location_number");

            $training = db::connection('ympimis_2')
                ->select("SELECT
			        *
			    FROM
			    assembly_trainings
			    WHERE
			    deleted_at IS NULL
				AND date = '" . date('Y-m-d') . "'
			    ORDER BY id desc
			");

            $response = array(
                'status' => true,
                'tact_time' => $tact_time,
                'average' => $average,
                'cycle' => $cycle,
                'training' => $training,
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

    public function indexAssyProductivity($origin_group_code)
    {
        if ($origin_group_code == '042') {
            $view = 'processes.assembly.display.productivity_cl';
        }
        return view($view, array(
            'title' => 'Productivity Operator Clarinet',
            'title_jp' => '',
            'origin_group_code' => $origin_group_code,
        ))->with('page', 'Productivity Operator Clarinet');
    }

    public function fetchAssyProductivity(Request $request)
    {
        try {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            $date = date('Y-m-01');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = date('Y-m-01');
                    $last = date('Y-m-d');
                } else {
                    $first = date('Y-m-01');
                    $last = $date_to;
                }
            } else {
                if ($date_to == "") {
                    $first = $date_from;
                    $last = date('Y-m-d');
                } else {
                    $first = $date_from;
                    $last = $date_to;
                }
            }

            $origin_group_code = $request->get('origin_group_code');
            if ($origin_group_code == "042") {

                $average = DB::SELECT("SELECT
					a.operator_id,
					employee_syncs.`name`,
					a.tanggal,
					count( a.serial_number ) AS qty,
					sum( a.tact_time ) AS actual_time,
					ROUND( sum( a.tact_time )/ COUNT( a.serial_number ), 1 ) AS tact_time
				FROM
					(
					SELECT
						assembly_logs.operator_id,
						date(sedang_start_date) as tanggal,
						serial_number,
						TIMESTAMPDIFF( MINUTE, sedang_start_date, sedang_finish_date ) AS tact_time
					FROM
						assembly_logs
						LEFT JOIN assemblies ON assemblies.operator_id = assembly_logs.operator_id
						AND assemblies.location = assembly_logs.location
					WHERE
					assembly_logs.origin_group_code = '" . $origin_group_code . "'
					and date(sedang_start_date) >= '" . $date . "'

					UNION ALL

					SELECT
						assembly_details.operator_id,
						date(sedang_start_date) as tanggal,
						serial_number,
						TIMESTAMPDIFF( MINUTE, sedang_start_date, sedang_finish_date ) AS tact_time
					FROM
						assembly_details
						LEFT JOIN assemblies ON assemblies.operator_id = assembly_details.operator_id
						AND assemblies.location = assembly_details.location
					WHERE
						assembly_details.origin_group_code = '" . $origin_group_code . "'
					and date(sedang_start_date) >= '" . $date . "'
					) a
					JOIN employee_syncs ON employee_syncs.employee_id = a.operator_id
				GROUP BY
					a.operator_id,
					employee_syncs.name,
					a.tanggal
				ORDER BY
					a.tanggal,
					a.operator_id");
            }

            $response = array(
                'status' => true,
                'average' => $average,
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

    public function inputAssemblyKensaConfirmation(Request $request)
    {
        try {
            $serial_number = $request->get('serial_number');
            $model = $request->get('model');
            $tag = $request->get('tag');
            $location = $request->get('location');
            $location_number = $request->get('location_number');
            $employee_id = $request->get('employee_id');
            $origin_group_code = $request->get('origin_group_code');

            $input = DB::connection('ympimis_2')->table('assembly_serial_confirmations')->insert([
                'serial_number' => $serial_number,
                'model' => $model,
                'tag' => $tag,
                'location' => $location,
                'location_number' => $location_number,
                'created_by' => $employee_id,
                'origin_group_code' => $origin_group_code,
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

    public function indexNgRate()
    {
        $locations = DB::select("SELECT DISTINCT
			(
			SUBSTRING( location, 1, LENGTH( location )- 2 )) AS location
			FROM
			assembly_operators
			WHERE
			origin_group_code = 041
			AND location != 'MIS'
			AND location NOT LIKE '%QA%'");

        $models = db::table('materials')->where('origin_group_code', '=', '041')
            ->where('category', '=', 'FG')
            ->orderBy('model', 'asc')
            ->select('model')
            ->distinct()
            ->get();

        $emp = EmployeeSync::where('employee_id', strtoupper(Auth::user()->username))->first();

        return view('processes.assembly.flute.display.ng_rate', array(
            'title' => 'NG Rate',
            'title_jp' => '不良率',
            'locations' => $locations,
            'emp' => $emp,
            'models' => $models,
        ))->with('page', 'Assembly Flute Process');
    }

    public function fetchNgRate(Request $request)
    {
        try {
            $now = date('Y-m-d');
            $addlocation = "";
            $origin = "";
            $origin2 = "";
            $model = "";

            if (strlen($request->get('tanggal')) > 0) {
                $now = date('Y-m-d', strtotime($request->get('tanggal')));
            }

            $date_from = $request->get('tanggal_from');
            $date_to = $request->get('tanggal_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = date('Y-m-d');
                    $last = date('Y-m-d');
                } else {
                    $first = date('Y-m-d');
                    $last = $date_to;
                }
            } else {
                if ($date_to == "") {
                    $first = $date_from;
                    $last = date('Y-m-d');
                } else {
                    $first = $date_from;
                    $last = $date_to;
                }
            }

            if ($request->get('origin') == "Production") {
                $origin = "and location not like '%qa%'";
                $origin2 = "and location not like '%qa%' and location not like '%process%'";
            } else if ($request->get('origin') == "QA") {
                $origin = "and location like '%qa%'";
                $origin2 = "and location like '%qa%'";
            } else {
                $origin = "";
                $origin2 = "";
            }

            if ($request->get('model') != '') {
                $model = "and model like '%" . $request->get('model') . "%'";
            }

            $ng = db::select("SELECT DISTINCT
				(
				SUBSTRING_INDEX( a.ng_name, '-', 1 )) AS ng_name,(
				SELECT
				COUNT( ng_name )
				FROM
				assembly_ng_logs
				WHERE
				serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '041' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )
				AND origin_group_code='041'
                AND model NOT IN (
                        SELECT DISTINCT
                            ( model )
                        FROM
                            stamp_hierarchies
                        WHERE
                        remark = 'SP'
                        OR model = 'YFL222HD') " . $addlocation . " " . $origin . " " . $model . "
				AND
				SUBSTRING_INDEX( ng_name, '-', 1 ) = SUBSTRING_INDEX( a.ng_name, '-', 1 )) AS jumlah,(
				SELECT
				COUNT( ng_name )
				FROM
				assembly_ng_logs
				WHERE
				serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '041' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )
				AND origin_group_code='041'
                AND model NOT IN (
                        SELECT DISTINCT
                            ( model )
                        FROM
                            stamp_hierarchies
                        WHERE
                        remark = 'SP'
                        OR model = 'YFL222HD') " . $addlocation . " " . $origin . " " . $model . "
				AND
				SUBSTRING_INDEX( ng_name, '-', 1 ) = SUBSTRING_INDEX( a.ng_name, '-', 1 )) / ( SELECT count( DISTINCT ( model )) AS model FROM `assembly_details` WHERE serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '041' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )
				AND origin_group_code='041'
                AND model NOT IN (
                        SELECT DISTINCT
                            ( model )
                        FROM
                            stamp_hierarchies
                        WHERE
                        remark = 'SP'
                        OR model = 'YFL222HD') " . $addlocation . " " . $origin . " " . $model . " ) * 100 AS rate
				FROM
				assembly_ng_logs AS a
				WHERE
				serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '041' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )
				AND origin_group_code='041'
                AND model NOT IN (
                        SELECT DISTINCT
                            ( model )
                        FROM
                            stamp_hierarchies
                        WHERE
                        remark = 'SP'
                        OR model = 'YFL222HD') " . $addlocation . " " . $origin . " " . $model . " ORDER BY jumlah DESC");

            $ngbody = db::select("
				SELECT DISTINCT
				CONCAT( ng_name, '_', ongko ) AS model,
				count( ng_name ) AS ng,
				0 AS rate
			FROM
				assembly_ng_logs
			WHERE
				serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '041' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )
				AND origin_group_code = '041'
                AND model NOT IN (
                        SELECT DISTINCT
                            ( model )
                        FROM
                            stamp_hierarchies
                        WHERE
                        remark = 'SP'
                        OR model = 'YFL222HD')
				" . $addlocation . " " . $origin . " " . $model . "
			GROUP BY
				CONCAT( ng_name, '_', ongko )
			ORDER BY
				ng DESC
				LIMIT 10"
            );

            $ngkey = DB::SELECT("
				SELECT DISTINCT
					( ongko ),
					count( ng_name ) AS ng,
					0 AS rate
				FROM
					assembly_ng_logs
				WHERE
					serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '041' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )
					AND origin_group_code = '041'
                    AND model NOT IN (
                        SELECT DISTINCT
                            ( model )
                        FROM
                            stamp_hierarchies
                        WHERE
                        remark = 'SP'
                        OR model = 'YFL222HD')
					" . $addlocation . " " . $origin . " " . $model . "
				GROUP BY
					ongko
				ORDER BY
					ng DESC
					LIMIT 10");

            $datastat = db::select("SELECT
				sum( alls.total_check ) AS total_check,
				sum( alls.total_ng ) AS total_ng,
				sum( alls.total_check ) - sum( alls.total_ng ) AS total_ok,
				(
				sum( alls.total_ng )/ sum( alls.total_check )) * 100 AS ng_rate
			FROM
				(
				SELECT
					count( DISTINCT ( all_check.serial_number ) ) AS total_check,
					0 AS total_ng
				FROM
					(
					SELECT DISTINCT
						( serial_number )
					FROM
						assembly_details
					WHERE
						serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '041' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )
						" . $origin2 . " " . $model . "
						AND origin_group_code = '041'
                        AND model NOT IN (
                        SELECT DISTINCT
                            ( model )
                        FROM
                            stamp_hierarchies
                        WHERE
                        remark = 'SP'
                        OR model = 'YFL222HD') UNION ALL
					SELECT DISTINCT
						( serial_number )
					FROM
						assembly_logs
					WHERE
						serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '041' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )
						AND origin_group_code = '041'
                        AND model NOT IN (
                        SELECT DISTINCT
                            ( model )
                        FROM
                            stamp_hierarchies
                        WHERE
                        remark = 'SP'
                        OR model = 'YFL222HD')
						" . $origin2 . " " . $model . "
					) AS all_check UNION ALL
				SELECT
					0 AS total_check,
					count(
					DISTINCT ( serial_number )) AS total_ng
				FROM
					(
					SELECT DISTINCT
						( serial_number )
					FROM
						assembly_details
					WHERE
						serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '041' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )
						" . $origin2 . " " . $model . "
						AND origin_group_code = '041'
                        AND model NOT IN (
                        SELECT DISTINCT
                            ( model )
                        FROM
                            stamp_hierarchies
                        WHERE
                        remark = 'SP'
                        OR model = 'YFL222HD') UNION ALL
					SELECT DISTINCT
						( serial_number )
					FROM
						assembly_logs
					WHERE
						serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '041' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )
						AND origin_group_code = '041'
                        AND model NOT IN (
                        SELECT DISTINCT
                            ( model )
                        FROM
                            stamp_hierarchies
                        WHERE
                        remark = 'SP'
                        OR model = 'YFL222HD')
						" . $origin2 . " " . $model . "
					) AS all_check
			WHERE
				all_check.serial_number IN ( SELECT DISTINCT ( serial_number ) FROM assembly_ng_logs WHERE origin_group_code = '041' )) AS alls");

            $dateTitleFirst = date("d M Y", strtotime($first));
            $dateTitleLast = date("d M Y", strtotime($last));

            $response = array(
                'status' => true,
                'ng' => $ng,
                'ngbody' => $ngbody,
                'ngkey' => $ngkey,
                'dateTitleFirst' => $dateTitleFirst,
                'dateTitleLast' => $dateTitleLast,
                'date' => $now,
                'data' => $datastat,
                'title' => $request->get('origin'),
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

    public function fetchNgRateDetail(Request $request)
    {
        try {
            $model = "";
            $ng_name = "";
            if ($request->get('type') == 'ng_name') {
                $ng_name = "AND SUBSTRING_INDEX( ng_name, '-', 1 ) = '" . $request->get('cat') . "'";
            } else if ($request->get('type') == 'ngname_key') {
                $model = "AND ng_name = '" . explode('_', $request->get('cat'))[0] . "' AND ongko = '" . explode('_', $request->get('cat'))[1] . "'";
            } else if ($request->get('type') == 'key') {
                $model = "AND ongko = '" . $request->get('cat') . "'";
            }
            $date_from = $request->get('tanggal_from');
            $date_to = $request->get('tanggal_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = date('Y-m-d');
                    $last = date('Y-m-d');
                } else {
                    $first = date('Y-m-d');
                    $last = $date_to;
                }
            } else {
                if ($date_to == "") {
                    $first = $date_from;
                    $last = date('Y-m-d');
                } else {
                    $first = $date_from;
                    $last = $date_to;
                }
            }

            $dateTitleFirst = date("d M Y", strtotime($first));
            $dateTitleLast = date("d M Y", strtotime($last));

            $addlocation = "";
            $origin = "";
            if ($request->get('location') != null) {
                $locations = explode(",", $request->get('location'));
                $location = "";

                for ($x = 0; $x < count($locations); $x++) {
                    $location = $location . "'" . $locations[$x] . "'";
                    if ($x != count($locations) - 1) {
                        $location = $location . ",";
                    }
                }
                $addlocation = "and location in (" . $location . ") ";
            }

            if ($request->get('origin') == "Production") {
                $origin = "and location not like '%qa%'";
            } else if ($request->get('origin') == "QA") {
                $origin = "and location like '%qa%'";
            } else {
                $origin = "";
            }

            $models = "";
            if ($request->get("model") != '') {
                $models = "AND model = '" . $request->get("model") . "'";
            }

            $detail = DB::SELECT("SELECT
				*,assembly_ng_logs.created_at as created
				FROM
				assembly_ng_logs
				LEFT JOIN employee_syncs ON employee_syncs.employee_id = assembly_ng_logs.employee_id
				WHERE
				serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '041' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )
				and assembly_ng_logs.origin_group_code = '041'
                AND model NOT IN (
                        SELECT DISTINCT
                            ( model )
                        FROM
                            stamp_hierarchies
                        WHERE
                        remark = 'SP'
                        OR model = 'YFL222HD')
				" . $ng_name . "
				" . $model . "
				" . $addlocation . "
				" . $origin . "
				" . $models . "");

            $response = array(
                'status' => true,
                'detail' => $detail,
                'dateTitleFirst' => $dateTitleFirst,
                'dateTitleLast' => $dateTitleLast,
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

    public function indexNgTrend($origin_group_code)
    {
        if ($origin_group_code == '043') {
            $view = 'processes.assembly.display.ng_trend_sax';
        }
        return view($view, array(
            'title' => 'NG Trend',
            'title_jp' => '',
            'origin_group_code' => $origin_group_code,
        ))->with('page', 'Assembly Flute Process');
    }

    public function fetchNgTrend(Request $request)
    {
        try {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = date('Y-m-01');
                    $last = date('Y-m-d');
                } else {
                    $first = date('Y-m-01');
                    $last = $date_to;
                }
            } else {
                if ($date_to == "") {
                    $first = $date_from;
                    $last = date('Y-m-d');
                } else {
                    $first = $date_from;
                    $last = $date_to;
                }
            }
            $calendar = DB::SELECT("SELECT week_date from weekly_calendars where DATE_FORMAT( week_date, '%Y-%m-%d' ) >= '" . $first . "'
					AND DATE_FORMAT( week_date, '%Y-%m-%d' ) <= '" . $last . "' and remark = 'H'");
            $datas = DB::SELECT("SELECT
				a.*
			FROM
				(
				SELECT DISTINCT
					( serial_number ),
					'YAS' AS model,
					DATE_FORMAT( created_at, '%Y-%m-%d' ) AS dates
				FROM
					assembly_logs
				WHERE
					DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '" . $first . "'
					AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '" . $last . "'
					AND origin_group_code = '" . $request->get('origin_group_code') . "'
					and location like '%qa%'
					AND model LIKE '%YAS%'

				UNION ALL

				SELECT DISTINCT
					( serial_number ),
					'YTS' AS model,
					DATE_FORMAT( created_at, '%Y-%m-%d' ) AS dates
				FROM
					assembly_logs
				WHERE
					DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '" . $first . "'
					AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '" . $last . "'
					AND origin_group_code = '" . $request->get('origin_group_code') . "'
					and location like '%qa%'
					AND model LIKE '%YTS%'
				) a
			ORDER BY
				a.dates");

            $ng = DB::SELECT("
			SELECT DISTINCT
				( serial_number ),
				'YAS' AS model
			FROM
				assembly_ng_logs
			WHERE
				origin_group_code = '" . $request->get('origin_group_code') . "'
				and location like '%qa%'
				AND model LIKE '%YAS%'

				UNION ALL

			SELECT DISTINCT
				( serial_number ),
				'YTS' AS model
			FROM
				assembly_ng_logs
			WHERE
				origin_group_code = '" . $request->get('origin_group_code') . "'
				and location like '%qa%'
				AND model LIKE '%YTS%'");

            $monthTitle = date('d M Y', strtotime($first)) . ' TO ' . date('d M Y', strtotime($last));

            $datas_line = DB::SELECT("
				SELECT
				a.*
			FROM
				(
				SELECT DISTINCT
					( assembly_logs.serial_number ),
					'YAS' AS model,
					DATE_FORMAT( created_at, '%Y-%m-%d' ) AS dates,
					b.location
				FROM
					assembly_logs
					JOIN (
					SELECT DISTINCT
						serial_number,
						location
					FROM
						assembly_logs
					WHERE
					DATE(created_at) >= '" . $first . "'
					AND DATE(created_at) <= '" . $last . "'
						AND location in(1,2,3,4,5)
					ORDER BY
						location
					) AS b
					ON assembly_logs.serial_number = b.serial_number
				WHERE
					DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '" . $first . "'
					AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '" . $last . "'
					AND origin_group_code = '" . $request->get('origin_group_code') . "'
					and assembly_logs.location like '%qa%'
					AND model LIKE '%YAS%'

				UNION ALL

				SELECT DISTINCT
					( assembly_logs.serial_number ),
					'YTS' AS model,
					DATE_FORMAT( created_at, '%Y-%m-%d' ) AS dates,
					b.location
				FROM
					assembly_logs
				JOIN (
					SELECT DISTINCT
						serial_number,
						location
					FROM
						assembly_logs
					WHERE
					DATE(created_at) >= '" . $first . "'
					AND DATE(created_at) <= '" . $last . "'
						AND location in(1,2,3,4,5)
					ORDER BY
						location
					) AS b
					ON assembly_logs.serial_number = b.serial_number
				WHERE
					DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '" . $first . "'
					AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '" . $last . "'
					AND origin_group_code = '" . $request->get('origin_group_code') . "'
					and assembly_logs.location like '%qa%'
					AND model LIKE '%YTS%'
				) a
			ORDER BY
				a.dates, a.location
			");

            $ng_line = DB::SELECT("
				SELECT DISTINCT
					( assembly_ng_logs.serial_number ),
					'YAS' AS model,
					 b.location
				FROM
					assembly_ng_logs
				JOIN (
						SELECT DISTINCT
							serial_number,
							location
						FROM
							assembly_logs
						WHERE
						DATE(created_at) >= '" . $first . "'
						AND DATE(created_at) <= '" . $last . "'
							AND location in(1,2,3,4,5)
						ORDER BY
							location
						) AS b
						ON assembly_ng_logs.serial_number = b.serial_number
				WHERE
					origin_group_code = '" . $request->get('origin_group_code') . "'
					and assembly_ng_logs.location like '%qa%'
					AND model LIKE '%YAS%'

					UNION ALL

				SELECT DISTINCT
					( assembly_ng_logs.serial_number ),
					'YTS' AS model,
					b.location
				FROM
					assembly_ng_logs
				JOIN (
						SELECT DISTINCT
							serial_number,
							location
						FROM
							assembly_logs
						WHERE
						DATE(created_at) >= '" . $first . "'
						AND DATE(created_at) <= '" . $last . "'
							AND location in(1,2,3,4,5)
						ORDER BY
							location
						) AS b
						ON assembly_ng_logs.serial_number = b.serial_number
				WHERE
					origin_group_code = '" . $request->get('origin_group_code') . "'
					and assembly_ng_logs.location like '%qa%'
					AND model LIKE '%YTS%'
			");

            // $datas_monthly = DB::SELECT("SELECT
            //     a.*
            // FROM
            //     (
            //     SELECT DISTINCT
            //         ( serial_number ),
            //         'YAS' AS model,
            //         DATE_FORMAT( created_at, '%Y-%m' ) AS dates
            //     FROM
            //         assembly_logs
            //     WHERE
            //         DATE_FORMAT( created_at, '%Y-%m' ) >= '2022-07'
            //         AND DATE_FORMAT( created_at, '%Y-%m' ) <= '".date('Y-m')."'
            //         AND origin_group_code = '".$request->get('origin_group_code')."'
            //         and location like '%qa%'
            //         AND model LIKE '%YAS%' UNION ALL
            //     SELECT DISTINCT
            //         ( serial_number ),
            //         'YTS' AS model,
            //         DATE_FORMAT( created_at, '%Y-%m' ) AS dates
            //     FROM
            //         assembly_logs
            //     WHERE
            //         DATE_FORMAT( created_at, '%Y-%m' ) >= '2022-07'
            //         AND DATE_FORMAT( created_at, '%Y-%m' ) <= '".date('Y-m')."'
            //         AND origin_group_code = '".$request->get('origin_group_code')."'
            //         and location like '%qa%'
            //         AND model LIKE '%YTS%'
            //     ) a
            // ORDER BY
            //     a.dates");

            $response = array(
                'status' => true,
                'datas' => $datas,
                'calendar' => $calendar,
                'ng' => $ng,
                'datas_line' => $datas_line,
                'ng_line' => $ng_line,
                'monthTitle' => $monthTitle,
                // 'datas_monthly' => $datas_monthly,
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

    public function indexNgClarinetRate()
    {
        $locations = DB::select("SELECT DISTINCT
			(
			SUBSTRING( location, 1, LENGTH( location )- 2 )) AS location
			FROM
			assembly_operators
			WHERE
			origin_group_code = 042
			AND location != 'MIS'
			AND location NOT LIKE '%QA%'");

        $models = db::table('materials')->where('origin_group_code', '=', '042')
            ->where('category', '=', 'FG')
            ->orderBy('model', 'asc')
            ->select('model')
            ->distinct()
            ->get();

        $emp = EmployeeSync::where('employee_id', strtoupper(Auth::user()->username))->first();

        return view('processes.assembly.clarinet.display.ng_rate', array(
            'title' => 'Clarinet NG Rate',
            'title_jp' => '不良率',
            'locations' => $locations,
            'emp' => $emp,
            'models' => $models,
        ))->with('page', 'Assembly Clarinet Process');
    }

    public function fetchNgClarinetRate(Request $request)
    {
        try {
            $now = date('Y-m-d');
            $addlocation = "";
            $origin = "";
            $model = "";

            if (strlen($request->get('tanggal')) > 0) {
                $now = date('Y-m-d', strtotime($request->get('tanggal')));
            }

            $date_from = $request->get('tanggal_from');
            $date_to = $request->get('tanggal_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = date('Y-m-d');
                    $last = date('Y-m-d');
                } else {
                    $first = date('Y-m-d');
                    $last = $date_to;
                }
            } else {
                if ($date_to == "") {
                    $first = $date_from;
                    $last = date('Y-m-d');
                } else {
                    $first = $date_from;
                    $last = $date_to;
                }
            }

            if ($request->get('origin') == "Production") {
                $origin = "and location not like '%qa%'";
            } else if ($request->get('origin') == "QA") {
                $origin = "and location like '%qa%'";
            } else {
                $origin = "";
            }

            if ($request->get('model') != '') {
                $model = "and model like '%" . $request->get('model') . "%'";
            }

            $ng = db::select("SELECT DISTINCT
                (
                    SUBSTRING_INDEX( a.ng_name, '-', 1 )) AS ng_name,(
                SELECT
                    COUNT( ng_name )
                FROM
                    assembly_ng_logs
                WHERE
                    serial_number IN (
                    SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        JOIN assembly_logs ON assembly_logs.serial_number = flo_details.serial_number
                        AND assembly_logs.location = 'packing'
                        AND assembly_logs.origin_group_code = '042'
                        AND ( status_material != 'J' OR status_material IS NULL )
                    WHERE
                        (
                            flo_details.origin_group_code = '042'
                            AND DATE( flo_details.created_at ) >= '" . $first . "'
                            AND DATE( flo_details.created_at ) <= '" . $last . "' AND assembly_logs.model LIKE '%200%' ) OR ( flo_details.origin_group_code = '042' AND DATE( flo_details.created_at ) >= '" . $first . "'
                            AND DATE( flo_details.created_at ) <= '" . $last . "'
                            AND assembly_logs.model LIKE '%255%'
                        )
                    )
                    AND origin_group_code = '042'
                    " . $addlocation . " " . $origin . " " . $model . "
                AND ng_name not like '%(PR)%'
                AND SUBSTRING_INDEX( ng_name, '-', 1 ) = SUBSTRING_INDEX( a.ng_name, '-', 1 )) AS jumlah
            FROM
                assembly_ng_logs AS a
            WHERE
                serial_number IN (
                SELECT DISTINCT
                    ( flo_details.serial_number )
                FROM
                    flo_details
                    JOIN assembly_logs ON assembly_logs.serial_number = flo_details.serial_number
                    AND assembly_logs.location = 'packing'
                    AND assembly_logs.origin_group_code = '042'
                    AND ( status_material != 'J' OR status_material IS NULL )
                WHERE
                    (
                        flo_details.origin_group_code = '042'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "' AND assembly_logs.model LIKE '%200%' ) OR ( flo_details.origin_group_code = '042' AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "'
                        AND assembly_logs.model LIKE '%255%'
                    )
                )
                AND origin_group_code = '042'
                " . $addlocation . " " . $origin . " " . $model . "
              AND ng_name not like '%(PR)%'
            ORDER BY
                jumlah DESC");

            // " . $addlocation . " " . $origin . " " . $model . "
            // " . $addlocation . " " . $origin . " " . $model . "
            // " . $addlocation . " " . $origin . " " . $model . "
            // " . $addlocation . " " . $origin . " " . $model . "

            $ngbody = db::select("
				SELECT DISTINCT
				CONCAT( ng_name, '_', ongko ) AS model,
				count( ng_name ) AS ng,
				0 AS rate
			FROM
				assembly_ng_logs
			WHERE
				serial_number IN ( SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        join assembly_logs on assembly_logs.serial_number = flo_details.serial_number and assembly_logs.location = 'packing'
                        and assembly_logs.origin_group_code = '042'
                        and (status_material != 'J' or status_material is null)
                    WHERE
                        (flo_details.origin_group_code = '042'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "'
                        AND assembly_logs.model LIKE '%200%')
                        OR
                        (flo_details.origin_group_code = '042'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "'
                        AND assembly_logs.model LIKE '%255%') )
				AND origin_group_code = '042'
                AND ng_name not like '%(PR)%'
				" . $addlocation . " " . $origin . " " . $model . "
			GROUP BY
				CONCAT( ng_name, '_', ongko )
			ORDER BY
				ng DESC
				LIMIT 10"
            );

            $ngkey = DB::SELECT("
				SELECT DISTINCT
					( ongko ),
					count( ng_name ) AS ng,
					0 AS rate
				FROM
					assembly_ng_logs
				WHERE
					serial_number IN ( SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        join assembly_logs on assembly_logs.serial_number = flo_details.serial_number and assembly_logs.location = 'packing'
                        and assembly_logs.origin_group_code = '042'
                        and (status_material != 'J' or status_material is null)
                    WHERE
                        (flo_details.origin_group_code = '042'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "'
                        AND assembly_logs.model LIKE '%200%')
                        OR
                        (flo_details.origin_group_code = '042'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "'
                        AND assembly_logs.model LIKE '%255%') )
					AND origin_group_code = '042'
                    AND ng_name not like '%(PR)%'
					" . $addlocation . " " . $origin . " " . $model . "
				GROUP BY
					ongko
				ORDER BY
					ng DESC
					LIMIT 10");

            $datastat = db::select("SELECT
				sum( alls.total_check ) AS total_check,
				sum( alls.total_ng ) AS total_ng,
				sum( alls.total_check ) - sum( alls.total_ng ) AS total_ok,
				(
				sum( alls.total_ng )/ sum( alls.total_check )) * 100 AS ng_rate
			FROM
				(
				SELECT
					count( DISTINCT ( all_check.serial_number ) ) AS total_check,
					0 AS total_ng
				FROM
					(
					SELECT DISTINCT
						( serial_number )
					FROM
						assembly_details
					WHERE
						serial_number IN ( SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        join assembly_logs on assembly_logs.serial_number = flo_details.serial_number and assembly_logs.location = 'packing'
                        and assembly_logs.origin_group_code = '042'
                        and (status_material != 'J' or status_material is null)
                    WHERE
                        (flo_details.origin_group_code = '042'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "'
                        AND assembly_logs.model LIKE '%200%')
                        OR
                        (flo_details.origin_group_code = '042'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "'
                        AND assembly_logs.model LIKE '%255%') )
						" . $addlocation . " " . $origin . " " . $model . "
						AND origin_group_code = '042'
                    UNION ALL
					SELECT DISTINCT
						( serial_number )
					FROM
						assembly_logs
					WHERE
						serial_number IN ( SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        join assembly_logs on assembly_logs.serial_number = flo_details.serial_number and assembly_logs.location = 'packing'
                        and assembly_logs.origin_group_code = '042'
                        and (status_material != 'J' or status_material is null)
                    WHERE
                        (flo_details.origin_group_code = '042'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "'
                        AND assembly_logs.model LIKE '%200%')
                        OR
                        (flo_details.origin_group_code = '042'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "'
                        AND assembly_logs.model LIKE '%255%') )
						AND origin_group_code = '042'
						" . $addlocation . " " . $origin . " " . $model . "
					) AS all_check UNION ALL
				SELECT
					0 AS total_check,
					count(
					DISTINCT ( serial_number )) AS total_ng
				FROM
					(
					SELECT DISTINCT
						( serial_number )
					FROM
						assembly_details
					WHERE
						serial_number IN ( SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        join assembly_logs on assembly_logs.serial_number = flo_details.serial_number and assembly_logs.location = 'packing'
                        and assembly_logs.origin_group_code = '042'
                        and (status_material != 'J' or status_material is null)
                    WHERE
                        (flo_details.origin_group_code = '042'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "'
                        AND assembly_logs.model LIKE '%200%')
                        OR
                        (flo_details.origin_group_code = '042'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "'
                        AND assembly_logs.model LIKE '%255%') )
						" . $addlocation . " " . $origin . " " . $model . "
						AND origin_group_code = '042'
                    UNION ALL
					SELECT DISTINCT
						( serial_number )
					FROM
						assembly_logs
					WHERE
						serial_number IN ( SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        join assembly_logs on assembly_logs.serial_number = flo_details.serial_number and assembly_logs.location = 'packing'
                        and assembly_logs.origin_group_code = '042'
                        and (status_material != 'J' or status_material is null)
                    WHERE
                        (flo_details.origin_group_code = '042'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "'
                        AND assembly_logs.model LIKE '%200%')
                        OR
                        (flo_details.origin_group_code = '042'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "'
                        AND assembly_logs.model LIKE '%255%') )
						AND origin_group_code = '042'
						" . $addlocation . " " . $origin . " " . $model . "
					) AS all_check
			WHERE
				all_check.serial_number IN ( SELECT DISTINCT ( serial_number ) FROM assembly_ng_logs WHERE origin_group_code = '042' )) AS alls");

            $dateTitleFirst = date("d M Y", strtotime($first));
            $dateTitleLast = date("d M Y", strtotime($last));

            $response = array(
                'status' => true,
                'ng' => $ng,
                'ngbody' => $ngbody,
                'ngkey' => $ngkey,
                'dateTitleFirst' => $dateTitleFirst,
                'dateTitleLast' => $dateTitleLast,
                'date' => $now,
                'data' => $datastat,
                'title' => $request->get('origin'),
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

    public function fetchNgRateClarinetDetail(Request $request)
    {
        try {
            $model = "";
            $ng_name = "";
            if ($request->get('type') == 'ng_name') {
                $ng_name = "AND SUBSTRING_INDEX( ng_name, '-', 1 ) = '" . $request->get('cat') . "'";
            } else if ($request->get('type') == 'ngname_key') {
                $model = "AND ng_name = '" . explode('_', $request->get('cat'))[0] . "' AND ongko = '" . explode('_', $request->get('cat'))[1] . "'";
            } else if ($request->get('type') == 'key') {
                $model = "AND ongko = '" . $request->get('cat') . "'";
            }
            $date_from = $request->get('tanggal_from');
            $date_to = $request->get('tanggal_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = date('Y-m-d');
                    $last = date('Y-m-d');
                } else {
                    $first = date('Y-m-d');
                    $last = $date_to;
                }
            } else {
                if ($date_to == "") {
                    $first = $date_from;
                    $last = date('Y-m-d');
                } else {
                    $first = $date_from;
                    $last = $date_to;
                }
            }

            $dateTitleFirst = date("d M Y", strtotime($first));
            $dateTitleLast = date("d M Y", strtotime($last));

            $addlocation = "";
            $origin = "";
            if ($request->get('location') != null) {
                $locations = explode(",", $request->get('location'));
                $location = "";

                for ($x = 0; $x < count($locations); $x++) {
                    $location = $location . "'" . $locations[$x] . "'";
                    if ($x != count($locations) - 1) {
                        $location = $location . ",";
                    }
                }
                $addlocation = "and location in (" . $location . ") ";
            }

            if ($request->get('origin') == "Production") {
                $origin = "and location not like '%qa%'";
            } else if ($request->get('origin') == "QA") {
                $origin = "and location like '%qa%'";
            } else {
                $origin = "";
            }

            $models = "";
            if ($request->get("model") != '') {
                $models = "AND model = '" . $request->get("model") . "'";
            }

            $detail = DB::SELECT("SELECT
				*,assembly_ng_logs.created_at as created
				FROM
				assembly_ng_logs
				LEFT JOIN employee_syncs ON employee_syncs.employee_id = assembly_ng_logs.employee_id
				WHERE
                location LIKE '%qa%'
                AND ng_name not like '%(PR)%'
                AND
				serial_number IN ( SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        join assembly_logs on assembly_logs.serial_number = flo_details.serial_number and assembly_logs.location = 'packing'
                        and assembly_logs.origin_group_code = '042'
                        and (status_material != 'J' or status_material is null)
                    WHERE
                        (flo_details.origin_group_code = '042'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "'
                        AND assembly_logs.model LIKE '%200%')
                        OR
                        (flo_details.origin_group_code = '042'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "'
                        AND assembly_logs.model LIKE '%255%') )
				and assembly_ng_logs.origin_group_code = '042'
                AND location LIKE '%qa%'
                AND ng_name not like '%(PR)%'
                " . $addlocation . " " . $origin . "
				" . $ng_name . "
				" . $model . "
				" . $models . "");

            $response = array(
                'status' => true,
                'detail' => $detail,
                'dateTitleFirst' => $dateTitleFirst,
                'dateTitleLast' => $dateTitleLast,
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

    public function indexNgSaxophoneRate()
    {
        $locations = DB::select("SELECT DISTINCT
			(
			SUBSTRING( location, 1, LENGTH( location )- 2 )) AS location
			FROM
			assembly_operators
			WHERE
			origin_group_code = 043
			AND location != 'MIS'
			AND location NOT LIKE '%QA%'");

        $models = db::table('materials')->where('origin_group_code', '=', '043')
            ->where('category', '=', 'FG')
            ->orderBy('model', 'asc')
            ->select('model')
            ->distinct()
            ->get();

        $emp = EmployeeSync::where('employee_id', strtoupper(Auth::user()->username))->first();

        return view('processes.assembly.saxophone.display.ng_rate', array(
            'title' => 'Saxophone NG Rate',
            'title_jp' => '不良率',
            'locations' => $locations,
            'emp' => $emp,
            'models' => $models,
        ))->with('page', 'Assembly Saxophone Process');
    }

    public function fetchNgSaxophoneRate(Request $request)
    {
        try {
            $now = date('Y-m-d');
            $addlocation = "";
            $origin = "";
            $model = "";

            if (strlen($request->get('tanggal')) > 0) {
                $now = date('Y-m-d', strtotime($request->get('tanggal')));
            }

            $date_from = $request->get('tanggal_from');
            $date_to = $request->get('tanggal_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = date('Y-m-d');
                    $last = date('Y-m-d');
                } else {
                    $first = date('Y-m-d');
                    $last = $date_to;
                }
            } else {
                if ($date_to == "") {
                    $first = $date_from;
                    $last = date('Y-m-d');
                } else {
                    $first = $date_from;
                    $last = $date_to;
                }
            }

            if ($request->get('origin') == "Production") {
                $origin = "and location not like '%qa%'";
            } else if ($request->get('origin') == "QA") {
                $origin = "and location like '%qa%'";
            } else {
                $origin = "";
            }

            if ($request->get('model') != "") {
                $model = "and model like '%" . $request->get('model') . "%'";
            }

            $ng = db::select("SELECT DISTINCT
				(
				SUBSTRING_INDEX( a.ng_name, '-', 1 )) AS ng_name,(
				SELECT
				COUNT( ng_name )
				FROM
				assembly_ng_logs
				WHERE
				serial_number IN ( SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        join assembly_logs on assembly_logs.serial_number = flo_details.serial_number and assembly_logs.location = 'packing'
                        and assembly_logs.origin_group_code = '043'
                        and (status_material != 'J' or status_material is null)
                    WHERE
                        flo_details.origin_group_code = '043'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "' )
				AND origin_group_code='043' " . $addlocation . " " . $origin . " " . $model . "
				AND
				SUBSTRING_INDEX( ng_name, '-', 1 ) = SUBSTRING_INDEX( a.ng_name, '-', 1 )) AS jumlah,(
				SELECT
				COUNT( ng_name )
				FROM
				assembly_ng_logs
				WHERE
				serial_number IN ( SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        join assembly_logs on assembly_logs.serial_number = flo_details.serial_number and assembly_logs.location = 'packing'
                        and assembly_logs.origin_group_code = '043'
                        and (status_material != 'J' or status_material is null)
                    WHERE
                        flo_details.origin_group_code = '043'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "' )
				AND origin_group_code='043' " . $addlocation . " " . $origin . " " . $model . "
				AND
				SUBSTRING_INDEX( ng_name, '-', 1 ) = SUBSTRING_INDEX( a.ng_name, '-', 1 )) / ( SELECT count( DISTINCT ( model )) AS model FROM `assembly_details` WHERE serial_number IN ( SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        join assembly_logs on assembly_logs.serial_number = flo_details.serial_number and assembly_logs.location = 'packing'
                        and assembly_logs.origin_group_code = '043'
                        and (status_material != 'J' or status_material is null)
                    WHERE
                        flo_details.origin_group_code = '043'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "' )
				AND origin_group_code='043' " . $addlocation . " " . $origin . " " . $model . " ) * 100 AS rate
				FROM
				assembly_ng_logs AS a
				WHERE
				serial_number IN ( SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        join assembly_logs on assembly_logs.serial_number = flo_details.serial_number and assembly_logs.location = 'packing'
                        and assembly_logs.origin_group_code = '043'
                        and (status_material != 'J' or status_material is null)
                    WHERE
                        flo_details.origin_group_code = '043'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "' )
				AND origin_group_code='043' " . $addlocation . " " . $origin . " " . $model . " ORDER BY jumlah DESC");

            $ngbody = db::select("
				SELECT DISTINCT
				CONCAT( ng_name, '-', ongko ) AS model,
				count( ng_name ) AS ng,
				0 AS rate
			FROM
				assembly_ng_logs
			WHERE
				serial_number IN ( SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        join assembly_logs on assembly_logs.serial_number = flo_details.serial_number and assembly_logs.location = 'packing'
                        and assembly_logs.origin_group_code = '043'
                        and (status_material != 'J' or status_material is null)
                    WHERE
                        flo_details.origin_group_code = '043'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "' )
				AND origin_group_code = '043'
				" . $addlocation . " " . $origin . " " . $model . "
			GROUP BY
				CONCAT( ng_name, '-', ongko )
			ORDER BY
				ng DESC
				LIMIT 10"
            );

            $ngkey = DB::SELECT("
				SELECT DISTINCT
					( ongko ),
					count( ng_name ) AS ng,
					0 AS rate
				FROM
					assembly_ng_logs
				WHERE
					serial_number IN ( SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        join assembly_logs on assembly_logs.serial_number = flo_details.serial_number and assembly_logs.location = 'packing'
                        and assembly_logs.origin_group_code = '043'
                        and (status_material != 'J' or status_material is null)
                    WHERE
                        flo_details.origin_group_code = '043'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "' )
					AND origin_group_code = '043'
					" . $addlocation . " " . $origin . " " . $model . "
				GROUP BY
					ongko
				ORDER BY
					ng DESC
					LIMIT 10");

            $datastat = db::select("SELECT
				sum( alls.total_check ) AS total_check,
				sum( alls.total_ng ) AS total_ng,
				sum( alls.total_check ) - sum( alls.total_ng ) AS total_ok,
				(
				sum( alls.total_ng )/ sum( alls.total_check )) * 100 AS ng_rate
			FROM
				(
				SELECT
					count( DISTINCT ( all_check.serial_number ) ) AS total_check,
					0 AS total_ng
				FROM
					(
					SELECT DISTINCT
						( serial_number )
					FROM
						assembly_details
					WHERE
						serial_number IN ( SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        join assembly_logs on assembly_logs.serial_number = flo_details.serial_number and assembly_logs.location = 'packing'
                        and assembly_logs.origin_group_code = '043'
                        and (status_material != 'J' or status_material is null)
                    WHERE
                        flo_details.origin_group_code = '043'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "' )
						" . $addlocation . " " . $origin . " " . $model . "
						AND location IN ( 'qa-kensa', 'qa-visual', 'qa-fungsi', 'kensa-process' )
						AND origin_group_code = '043' UNION ALL
					SELECT DISTINCT
						( serial_number )
					FROM
						assembly_logs
					WHERE
						serial_number IN ( SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        join assembly_logs on assembly_logs.serial_number = flo_details.serial_number and assembly_logs.location = 'packing'
                        and assembly_logs.origin_group_code = '043'
                        and (status_material != 'J' or status_material is null)
                    WHERE
                        flo_details.origin_group_code = '043'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "' )
						AND origin_group_code = '043'
						" . $addlocation . " " . $origin . " " . $model . "
						AND location IN ( 'qa-kensa', 'qa-visual', 'qa-fungsi', 'kensa-process' )
					) AS all_check UNION ALL
				SELECT
					0 AS total_check,
					count(
					DISTINCT ( serial_number )) AS total_ng
				FROM
					(
					SELECT DISTINCT
						( serial_number )
					FROM
						assembly_details
					WHERE
						serial_number IN ( SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        join assembly_logs on assembly_logs.serial_number = flo_details.serial_number and assembly_logs.location = 'packing'
                        and assembly_logs.origin_group_code = '043'
                        and (status_material != 'J' or status_material is null)
                    WHERE
                        flo_details.origin_group_code = '043'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "' )
						" . $addlocation . " " . $origin . " " . $model . "
						AND location IN ( 'qa-kensa', 'qa-visual', 'qa-fungsi', 'kensa-process' )
						AND origin_group_code = '043' UNION ALL
					SELECT DISTINCT
						( serial_number )
					FROM
						assembly_logs
					WHERE
						serial_number IN ( SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        join assembly_logs on assembly_logs.serial_number = flo_details.serial_number and assembly_logs.location = 'packing'
                        and assembly_logs.origin_group_code = '043'
                        and (status_material != 'J' or status_material is null)
                    WHERE
                        flo_details.origin_group_code = '043'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "' )
						AND origin_group_code = '043'
						" . $addlocation . " " . $origin . " " . $model . "
						AND location IN ( 'qa-kensa', 'qa-visual', 'qa-fungsi', 'kensa-process' )
					) AS all_check
			WHERE
				all_check.serial_number IN ( SELECT DISTINCT ( serial_number ) FROM assembly_ng_logs WHERE origin_group_code = '043' )) AS alls");

            $dateTitleFirst = date("d M Y", strtotime($first));
            $dateTitleLast = date("d M Y", strtotime($last));

            $response = array(
                'status' => true,
                'ng' => $ng,
                'ngbody' => $ngbody,
                'ngkey' => $ngkey,
                'dateTitleFirst' => $dateTitleFirst,
                'dateTitleLast' => $dateTitleLast,
                'date' => $now,
                'data' => $datastat,
                'title' => $request->get('origin'),
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

    public function fetchNgSaxophoneRateLine(Request $request)
    {
        try {
            $now = date('Y-m-d');
            $addlocation = "";
            $origin = "";

            // if($request->get('location') != null) {
            //     $locations = explode(",", $request->get('location'));
            //     $location = "";

            //     for($x = 0; $x < count($locations); $x++) {
            //         $location = $location."'".$locations[$x]."'";
            //         if($x != count($locations)-1){
            //             $location = $location.",";
            //         }
            //     }
            //     $addlocation = "and location in (".$location.") ";
            // }

            if (strlen($request->get('tanggal')) > 0) {
                $now = date('Y-m-d', strtotime($request->get('tanggal')));
            }

            $date_from = $request->get('tanggal_from');
            $date_to = $request->get('tanggal_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = date('Y-m-d');
                    $last = date('Y-m-d');
                } else {
                    $first = date('Y-m-d');
                    $last = $date_to;
                }
            } else {
                if ($date_to == "") {
                    $first = $date_from;
                    $last = date('Y-m-d');
                } else {
                    $first = $date_from;
                    $last = $date_to;
                }
            }

            // if($request->get('origin') == "Production"){
            $origin = "and location not like '%qa%'";
            // }else if($request->get('origin') == "QA"){
            //     $origin = "and location like '%qa%'";
            // }else{
            //     $origin = "";
            // }

            $ngline = DB::SELECT("SELECT DISTINCT
					( SUBSTRING_INDEX( a.ng_name, '-', 1 ) ) AS ng_name,
					(
				SELECT
					COUNT( ng_name )
				FROM
					assembly_ng_logs
					LEFT JOIN (
				SELECT DISTINCT
					serial_number,
					location
				FROM
					assembly_logs
				WHERE
					DATE( created_at ) >= '" . $first . "'
					AND DATE( created_at ) <= '" . $last . "'
					AND location = 1
				ORDER BY
					location
					) AS b ON assembly_ng_logs.serial_number = b.serial_number
				WHERE
					DATE( assembly_ng_logs.created_at ) >= '" . $first . "'
					AND DATE( assembly_ng_logs.created_at ) <= '" . $last . "'
					AND assembly_ng_logs.origin_group_code = '043'
					AND b.location = 1
					AND SUBSTRING_INDEX( ng_name, '-', 1 ) = SUBSTRING_INDEX( a.ng_name, '-', 1 )
					) AS jumlah,
					(
				SELECT
					COUNT( ng_name )
				FROM
					assembly_ng_logs
					JOIN (
				SELECT DISTINCT
					serial_number,
					location
				FROM
					assembly_logs
				WHERE
					DATE( created_at ) >= '" . $first . "'
					AND DATE( created_at ) <= '" . $last . "'
					AND location = 1
				ORDER BY
					location
					) AS b ON assembly_ng_logs.serial_number = b.serial_number
				WHERE
					DATE( assembly_ng_logs.created_at ) >= '" . $first . "'
					AND DATE( assembly_ng_logs.created_at ) <= '" . $last . "'
					AND assembly_ng_logs.origin_group_code = '043'
					AND b.location = 1
					AND SUBSTRING_INDEX( ng_name, '-', 1 ) = SUBSTRING_INDEX( a.ng_name, '-', 1 )
					) / (
				SELECT
					count( DISTINCT ( model ) ) AS model
				FROM
					`assembly_details`
				WHERE
					DATE( created_at ) >= '" . $first . "'
					AND DATE( created_at ) <= '" . $last . "'
					AND origin_group_code = '043'
					AND location = 1
					) * 100 AS rate
				FROM
					assembly_ng_logs AS a
					JOIN (
				SELECT DISTINCT
					serial_number,
					location
				FROM
					assembly_logs
				WHERE
					DATE( created_at ) >= '" . $first . "'
					AND DATE( created_at ) <= '" . $last . "'
					AND location = 1
				ORDER BY
					location
					) AS b ON a.serial_number = b.serial_number
				WHERE
					DATE( a.created_at ) >= '" . $first . "'
					AND DATE( a.created_at ) <= '" . $last . "'
					AND a.origin_group_code = '043'
					AND b.location = 1
				ORDER BY
					jumlah DESC");

            $ngbodyline = db::select("
				SELECT DISTINCT
					CONCAT( ng_name, ' ', ongko ) AS model,
					count( ng_name ) AS ng,
					0 AS rate,
					b.location AS line
				FROM
					assembly_ng_logs
					JOIN (
					SELECT DISTINCT
						serial_number,
						location
					FROM
						assembly_logs
					WHERE
						DATE( created_at ) >=  '" . $first . "'
					AND DATE( created_at ) <= '" . $last . "' AND location IN ( 1 ) ORDER BY location ) AS b ON assembly_ng_logs.serial_number = b.serial_number WHERE DATE( created_at ) >=  '" . $first . "'
					AND DATE( created_at ) <= '" . $last . "'
					AND origin_group_code = '043'
					AND b.location IN ( 1 )
				GROUP BY
					CONCAT( ng_name, ' ', ongko ),
					b.location
				ORDER BY
					b.location ASC,
					ng DESC
					LIMIT 10
			");

            $ngkeyline = DB::SELECT("
					SELECT DISTINCT
					( ongko ),
					count( ng_name ) AS ng,
					0 AS rate
				FROM
					assembly_ng_logs
					JOIN (
					SELECT DISTINCT
						serial_number,
						location
					FROM
						assembly_logs
					WHERE
					DATE(created_at) >= '" . $first . "'
					AND DATE(created_at) <= '" . $last . "'
						AND location in(1)
					ORDER BY
						location
					) AS b
					ON assembly_ng_logs.serial_number = b.serial_number
				WHERE
					DATE(created_at) >= '" . $first . "'
					AND DATE(created_at) <= '" . $last . "'
					AND origin_group_code = '043'
				AND b.location in(1)
				GROUP BY
					ongko
				ORDER BY
					ng DESC
					LIMIT 10
			");

            $datastatline = db::select("SELECT
				sum( alls.total_check ) AS total_check,
				sum( alls.total_ng ) AS total_ng,
				sum( alls.total_check ) - sum( alls.total_ng ) AS total_ok,
				(
				sum( alls.total_ng )/ sum( alls.total_check )) * 100 AS ng_rate
			FROM
				(
				SELECT
					count( DISTINCT ( all_check.serial_number ) ) AS total_check,
					0 AS total_ng
				FROM
					(
					SELECT DISTINCT
						( assembly_details.serial_number )
					FROM
						assembly_details
						JOIN (
						SELECT DISTINCT
							serial_number,
							location
						FROM
							assembly_logs
						WHERE
						date( created_at ) >= '" . $first . "'
						AND date( created_at ) <= '" . $last . "'
							AND location in(1)
						ORDER BY
							location
						) AS b
					ON assembly_details.serial_number = b.serial_number
					WHERE
						date( created_at ) >= '" . $first . "'
						AND date( created_at ) <= '" . $last . "'
						AND origin_group_code = '043'
						AND assembly_details.location IN ( 'qa-kensa', 'qa-visual', 'qa-fungsi', 'kensa-process' )

						UNION ALL

					SELECT DISTINCT
						( assembly_logs.serial_number )
					FROM
						assembly_logs
					JOIN (
						SELECT DISTINCT
							serial_number,
							location
						FROM
							assembly_logs
						WHERE
						date( created_at ) >= '" . $first . "'
						AND date( created_at ) <= '" . $last . "'
							AND location in(1)
						ORDER BY
							location
						) AS b
					ON assembly_logs.serial_number = b.serial_number
					WHERE
						date( sedang_start_date ) >= '" . $first . "'
						AND date( sedang_start_date ) <= '" . $last . "'
						AND origin_group_code = '043'
						AND assembly_logs.location IN ( 'qa-kensa', 'qa-visual', 'qa-fungsi', 'kensa-process' )
					) AS all_check

					UNION ALL

				SELECT
					0 AS total_check,
					count(
					DISTINCT ( serial_number )) AS total_ng
				FROM
					(
					SELECT DISTINCT
						( assembly_details.serial_number )
					FROM
						assembly_details
					JOIN (
						SELECT DISTINCT
							serial_number,
							location
						FROM
							assembly_logs
						WHERE
						date( created_at ) >= '" . $first . "'
						AND date( created_at ) <= '" . $last . "'
							AND location in(1)
						ORDER BY
							location
						) AS b
					ON assembly_details.serial_number = b.serial_number
					WHERE
						date( created_at ) >= '" . $first . "'
						AND date( created_at ) <= '" . $last . "'
						AND assembly_details.location IN ( 'qa-kensa', 'qa-visual', 'qa-fungsi', 'kensa-process' )
						AND origin_group_code = '043'

						UNION ALL

					SELECT DISTINCT
						( assembly_logs.serial_number )
					FROM
						assembly_logs
					JOIN (
						SELECT DISTINCT
							serial_number,
							location
						FROM
							assembly_logs
						WHERE
						date( created_at ) >= '" . $first . "'
						AND date( created_at ) <= '" . $last . "'
							AND location in(1)
						ORDER BY
							location
						) AS b
					ON assembly_logs.serial_number = b.serial_number
					WHERE
						date( sedang_start_date ) >= '" . $first . "'
						AND date( sedang_start_date ) <= '" . $last . "'
						AND origin_group_code = '043'
						AND assembly_logs.location IN ( 'qa-kensa', 'qa-visual', 'qa-fungsi', 'kensa-process' )
					) AS all_check
			WHERE
				all_check.serial_number IN ( SELECT DISTINCT ( serial_number ) FROM assembly_ng_logs WHERE origin_group_code = '043' )) AS alls");

            $dateTitleFirst = date("d M Y", strtotime($first));
            $dateTitleLast = date("d M Y", strtotime($last));

            $response = array(
                'status' => true,
                'ngline' => $ngline,
                'ngbodyline' => $ngbodyline,
                'ngkeyline' => $ngkeyline,
                'dateTitleFirst' => $dateTitleFirst,
                'dateTitleLast' => $dateTitleLast,
                'datastatline' => $datastatline,
                'date' => $now,
                'title' => $request->get('origin'),
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

    public function fetchNgRateSaxophoneDetail(Request $request)
    {
        try {
            $model = "";
            $ng_name = "";
            if ($request->get('type') == 'ng_name') {
                $ng_name = "AND SUBSTRING_INDEX( ng_name, '-', 1 ) = '" . $request->get('cat') . "'";
            } else if ($request->get('type') == 'ngname_key') {
                $model = "AND ng_name = '" . explode('-', $request->get('cat'))[0] . "' AND ongko = '" . explode('-', $request->get('cat'))[1] . "'";
            } else if ($request->get('type') == 'key') {
                $model = "AND ongko = '" . $request->get('cat') . "'";
            }
            $date_from = $request->get('tanggal_from');
            $date_to = $request->get('tanggal_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = date('Y-m-d');
                    $last = date('Y-m-d');
                } else {
                    $first = date('Y-m-d');
                    $last = $date_to;
                }
            } else {
                if ($date_to == "") {
                    $first = $date_from;
                    $last = date('Y-m-d');
                } else {
                    $first = $date_from;
                    $last = $date_to;
                }
            }

            $dateTitleFirst = date("d M Y", strtotime($first));
            $dateTitleLast = date("d M Y", strtotime($last));

            $addlocation = "";
            $origin = "";
            if ($request->get('location') != null) {
                $locations = explode(",", $request->get('location'));
                $location = "";

                for ($x = 0; $x < count($locations); $x++) {
                    $location = $location . "'" . $locations[$x] . "'";
                    if ($x != count($locations) - 1) {
                        $location = $location . ",";
                    }
                }
                $addlocation = "and location in (" . $location . ") ";
            }

            if ($request->get('origin') == "Production") {
                $origin = "and location not like '%qa%'";
            } else if ($request->get('origin') == "QA") {
                $origin = "and location like '%qa%'";
            } else {
                $origin = "";
            }

            $models = "";
            if ($request->get('model') != "") {
                $models = "and model like '%" . $request->get('model') . "%'";
            }

            $detail = DB::SELECT("SELECT
				*,assembly_ng_logs.created_at as created
				FROM
				assembly_ng_logs
				LEFT JOIN employee_syncs ON employee_syncs.employee_id = assembly_ng_logs.employee_id
				WHERE
				serial_number IN ( SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        join assembly_logs on assembly_logs.serial_number = flo_details.serial_number and assembly_logs.location = 'packing'
                        and assembly_logs.origin_group_code = '043'
                        and (status_material != 'J' or status_material is null)
                    WHERE
                        flo_details.origin_group_code = '043'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "' )
				and assembly_ng_logs.origin_group_code = '043'
                " . $origin . "
				" . $ng_name . "
				" . $model . "
				" . $addlocation . "
				" . $models . "");

            $response = array(
                'status' => true,
                'detail' => $detail,
                'dateTitleFirst' => $dateTitleFirst,
                'dateTitleLast' => $dateTitleLast,
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

    public function indexOpRate()
    {
        $title = 'NG Rate by Operator';
        $title_jp = '作業者不良率';

        $location = $this->location_fl_display;

        return view('processes.assembly.flute.display.op_rate', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'location' => $location,
        ))->with('page', 'Assembly Flute Process');
    }

    public function fetchOpRate(Request $request)
    {
        $now = date('Y-m-d');

        $addlocation = "";
        if ($request->get('location') != null) {
            $locations = explode(",", $request->get('location'));
            $location = "";

            for ($x = 0; $x < count($locations); $x++) {
                $location = $location . "'" . $locations[$x] . "'";
                if ($x != count($locations) - 1) {
                    $location = $location . ",";
                }
            }
            $addlocation = "and SUBSTRING_INDEX( assembly_operators.location, '-', 2 ) in (" . $location . ") ";
        }

        if (strlen($request->get('tanggal')) > 0) {
            $now = date('Y-m-d', strtotime($request->get('tanggal')));
        }

        $ng_target = db::table("middle_targets")
            ->where('location', '=', 'assy_fl')
            ->where('target_name', '=', 'NG Rate')
            ->select('target')
            ->first();

        $ng_rate = db::select("SELECT DISTINCT
			( assembly_logs.operator_id ) AS employee_id,
			CONCAT(( assembly_logs.operator_id ),'-',SUBSTRING_INDEX( employee_syncs.NAME, ' ', 2 )) as opname,
			SUBSTRING_INDEX( assembly_operators.location, '-', 2 ) AS `location`,
			SUBSTRING_INDEX( employee_syncs.NAME, ' ', 2 ) AS `name`,
			COALESCE ( ng.`check`, 0 ) AS `check`,
			COALESCE ( ng.ng, 0 ) AS ng,
			COALESCE ( ng2.`check`, 0 ) AS `check2`,
			COALESCE ( ng2.ng, 0 ) AS ng2
			FROM
			assembly_logs
			LEFT JOIN employee_syncs ON employee_syncs.employee_id = assembly_logs.operator_id
			LEFT JOIN assembly_operators ON assembly_operators.employee_id = assembly_logs.operator_id
			LEFT JOIN (
			SELECT
			count( id ) AS ng,
			count( id ) AS `check`,
			operator_id
			FROM
			assembly_ng_logs
			WHERE
			DATE( created_at ) = '" . $now . "'
			GROUP BY
			operator_id
			) ng ON assembly_logs.operator_id = ng.operator_id
			LEFT JOIN (
			SELECT
			count( id ) AS ng,
			count( id ) AS `check`,
			operator_id
			FROM
			assembly_ng_logs
			WHERE
			DATE( created_at ) = '" . $now . "'
			GROUP BY
			operator_id
			) ng2 ON SUBSTRING_INDEX( assembly_operators.location, '-', 2 ) = ng2.operator_id
			WHERE
			employee_syncs.end_date IS NULL
			AND assembly_logs.operator_id != 'null'
			AND assembly_logs.operator_id != 'assy-fl'
			AND DATE( assembly_logs.created_at ) = '" . $now . "'
			AND assembly_logs.origin_group_code = '041'
			" . $addlocation . "");

// $target = db::select("select eg.`group`, eg.employee_id, e.name, ng.material_number, concat(m.model, ' ', m.`key`) as `key`, ng.ng_name, ng.quantity, ng.created_at from employee_groups eg left join
//     (select * from welding_ng_logs where deleted_at is null ".$addlocation." and remark in
//     (select remark.remark from
//     (select operator_id, max(remark) as remark from welding_ng_logs where DATE(welding_time) ='".$now."' ".$addlocation." group by operator_id)
//     remark)
//     ) ng
//     on eg.employee_id = ng.operator_id
//     left join materials m on m.material_number = ng.material_number
//     left join employee_syncs e on e.employee_id = eg.employee_id
//     where eg.location = 'soldering'
//     order by eg.`group`, e.`name` asc");

// $operator = db::select("select g.group, g.employee_id, e.name from employee_groups g
//     left join employee_syncs e on e.employee_id = g.employee_id
//     where g.location = 'soldering'
//     order by g.`group`, e.name asc");

        $dateTitle = date("d M Y", strtotime($now));

        $location = "";
        if ($request->get('location') != null) {
            $locations = explode(",", $request->get('location'));
            for ($x = 0; $x < count($locations); $x++) {
                $location = $location . " " . $locations[$x] . " ";
                if ($x != count($locations) - 1) {
                    $location = $location . "&";
                }
            }
        } else {
            $location = "";
        }
        $location = strtoupper($location);

        $response = array(
            'status' => true,
            'ng_rate' => $ng_rate,
// 'target' => $target,
// 'operator' => $operator,
            'ng_target' => $ng_target->target,
            'dateTitle' => $now,
            'title' => $location,
        );
        return Response::json($response);
    }

    public function indexOpClarinetRate()
    {
        $title = 'Clarinet NG Rate by Operator';
        $title_jp = '作業者不良率';

        $locations = DB::select("SELECT DISTINCT
			(
			SUBSTRING( location, 1, LENGTH( location )- 2 )) AS location
			FROM
			assembly_operators
			WHERE
			origin_group_code = 042
			AND location != 'MIS'
			AND location NOT LIKE '%QA%'");

        return view('processes.assembly.clarinet.display.op_rate', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'locations' => $locations,
        ))->with('page', 'Assembly Clarinet Process');
    }

    public function fetchOpClarinetRate(Request $request)
    {
        $now = date('Y-m-d');

        $addlocation = "";
        if ($request->get('location') != null) {
            $locations = explode(",", $request->get('location'));
            $location = "";

            for ($x = 0; $x < count($locations); $x++) {
                $location = $location . "'" . $locations[$x] . "'";
                if ($x != count($locations) - 1) {
                    $location = $location . ",";
                }
            }
            $addlocation = "and SUBSTRING_INDEX( assembly_operators.location, '-', 2 ) in (" . $location . ") ";
        }

        if (strlen($request->get('tanggal')) > 0) {
            $now = date('Y-m-d', strtotime($request->get('tanggal')));
        }

        $ng_target = db::table("middle_targets")
            ->where('location', '=', 'assy_fl')
            ->where('target_name', '=', 'NG Rate')
            ->select('target')
            ->first();

        $ng_rate = db::select("SELECT
			assembly_operators.employee_id,
			employee_syncs.`name`,
			COALESCE ( ngs.ng, 0 ) AS ng,
			COALESCE ( ngs.ok, 0 ) AS ok,
			ROUND( COALESCE ( COALESCE ( ngs.ng, 0 )/ COALESCE ( ngs.ok, 0 ), 0 ), 2 ) AS rate
			FROM
			assembly_operators
			JOIN employee_syncs ON employee_syncs.employee_id = assembly_operators.employee_id
			LEFT JOIN (
			SELECT
			a.operator_id,
			SUM( a.ng ) AS ng,
			SUM( a.ok ) AS ok
			FROM
			(
			SELECT
			operator_id,
			count( DISTINCT ( serial_number ) ) AS ng,
			0 AS ok
			FROM
			assembly_ng_logs
			WHERE
			origin_group_code = 042
			AND DATE( assembly_ng_logs.sedang_start_date ) = '" . $now . "'
			GROUP BY
			operator_id UNION ALL
			SELECT
			operator_id,
			0 AS ng,
			count( DISTINCT ( serial_number ) ) AS ok
			FROM
			assembly_logs
			WHERE
			origin_group_code = 042
			AND DATE( assembly_logs.sedang_start_date ) = '" . $now . "'
			GROUP BY
			operator_id UNION ALL
			SELECT
			operator_id,
			0 AS ng,
			count( DISTINCT ( serial_number ) ) AS ok
			FROM
			assembly_details
			WHERE
			origin_group_code = 042
			AND DATE( assembly_details.sedang_start_date ) = '" . $now . "'
			GROUP BY
			operator_id
			) a
			WHERE
			a.operator_id != ''
			GROUP BY
			a.operator_id
			) ngs ON ngs.operator_id = assembly_operators.employee_id
			WHERE
			origin_group_code = 042
			AND location != 'MIS'
			AND location NOT LIKE '%QA%'
			" . $addlocation . "
			ORDER BY
			location");

        $dateTitle = date("d M Y", strtotime($now));

        $location = "";
        if ($request->get('location') != null) {
            $locations = explode(",", $request->get('location'));
            for ($x = 0; $x < count($locations); $x++) {
                $location = $location . " " . $locations[$x] . " ";
                if ($x != count($locations) - 1) {
                    $location = $location . "&";
                }
            }
        } else {
            $location = "";
        }
        $location = strtoupper($location);

        $response = array(
            'status' => true,
            'ng_rate' => $ng_rate,
// 'target' => $target,
// 'operator' => $operator,
            'ng_target' => $ng_target->target,
            'dateTitle' => $now,
            'title' => $location,
        );
        return Response::json($response);
    }

    public function indexOpSaxophoneRate()
    {
        $title = 'Saxophone NG Rate by Operator';
        $title_jp = '作業者不良率';

        $locations = DB::select("SELECT DISTINCT
			(
			SUBSTRING( location, 1, LENGTH( location )- 2 )) AS location
			FROM
			assembly_operators
			WHERE
			origin_group_code = 043
			AND location != 'MIS'
			AND location NOT LIKE '%QA%'");

        return view('processes.assembly.saxophone.display.op_rate', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'location' => $locations,
        ))->with('page', 'Assembly Saxophone Process');
    }

    public function fetchOpSaxophoneRate(Request $request)
    {
        $now = date('Y-m-d');

        $addlocation = "";
        if ($request->get('location') != null) {
            $locations = explode(",", $request->get('location'));
            $location = "";

            for ($x = 0; $x < count($locations); $x++) {
                $location = $location . "'" . $locations[$x] . "'";
                if ($x != count($locations) - 1) {
                    $location = $location . ",";
                }
            }
            $addlocation = "and SUBSTRING_INDEX( assembly_operators.location, '-', 2 ) in (" . $location . ") ";
        }

        if (strlen($request->get('tanggal')) > 0) {
            $now = date('Y-m-d', strtotime($request->get('tanggal')));
        }

        $ng_target = db::table("middle_targets")
            ->where('location', '=', 'assy_fl')
            ->where('target_name', '=', 'NG Rate')
            ->select('target')
            ->first();

        $ng_rate = db::select("SELECT
			assembly_operators.employee_id,
			employee_syncs.`name`,
			COALESCE ( ngs.ng, 0 ) AS ng,
			COALESCE ( ngs.ok, 0 ) AS ok,
			ROUND( COALESCE ( COALESCE ( ngs.ng, 0 )/ COALESCE ( ngs.ok, 0 ), 0 ), 2 ) AS rate,
			SPLIT_STRING ( location, '-', 3 ) AS line
			FROM
			assembly_operators
			JOIN employee_syncs ON employee_syncs.employee_id = assembly_operators.employee_id
			LEFT JOIN (
			SELECT
			a.operator_id,
			SUM( a.ng ) AS ng,
			SUM( a.ok ) AS ok
			FROM
			(
			SELECT
			operator_id,
			count( DISTINCT ( serial_number ) ) AS ng,
			0 AS ok
			FROM
			assembly_ng_logs
			WHERE
			origin_group_code = 043
			AND DATE( assembly_ng_logs.sedang_start_date ) = '" . $now . "'
			GROUP BY
			operator_id UNION ALL
			SELECT
			operator_id,
			0 AS ng,
			count( DISTINCT ( serial_number ) ) AS ok
			FROM
			assembly_logs
			WHERE
			origin_group_code = 043
			AND DATE( assembly_logs.sedang_start_date ) = '" . $now . "'
			GROUP BY
			operator_id UNION ALL
			SELECT
			operator_id,
			0 AS ng,
			count( DISTINCT ( serial_number ) ) AS ok
			FROM
			assembly_details
			WHERE
			origin_group_code = 043
			AND DATE( assembly_details.sedang_start_date ) = '" . $now . "'
			GROUP BY
			operator_id
			) a
			WHERE
			a.operator_id != ''
			GROUP BY
			a.operator_id
			) ngs ON ngs.operator_id = assembly_operators.employee_id
			WHERE
			origin_group_code = 043
			AND location != 'MIS'
			AND location NOT LIKE '%QA%'
			" . $addlocation . "
			ORDER BY
			location");

        $dateTitle = date("d M Y", strtotime($now));

        $location = "";
        if ($request->get('location') != null) {
            $locations = explode(",", $request->get('location'));
            for ($x = 0; $x < count($locations); $x++) {
                $location = $location . " " . $locations[$x] . " ";
                if ($x != count($locations) - 1) {
                    $location = $location . "&";
                }
            }
        } else {
            $location = "";
        }
        $location = strtoupper($location);

        $response = array(
            'status' => true,
            'ng_rate' => $ng_rate,
// 'target' => $target,
// 'operator' => $operator,
            'ng_target' => $ng_target->target,
            'dateTitle' => $now,
            'title' => $location,
        );
        return Response::json($response);
    }

    public function indexProductionResult(Request $request)
    {
        $title = 'Production Result';
        $title_jp = '生産結果';
        return view('processes.assembly.flute.display.production_result')
            ->with('page', 'Process Assy FL')
            ->with('head', 'Assembly Process')
            ->with('location_all', $this->location_fl_display)
            ->with('title', $title)
            ->with('title_jp', $title_jp);
    }

    public function fetchProductionResult(Request $request)
    {
        $first = date('Y-m-01');
        $now = date('Y-m-d');

        $location = $request->get('location');
        if ($request->get('location') == 'stamp') {
            $title_location = 'Stamp';
            $next_location = 'perakitan';
        }
        if ($request->get('location') == 'perakitan') {
            $title_location = 'Perakitan';
            $next_location = 'kariawase';
        }
        if ($request->get('location') == 'kariawase') {
            $title_location = 'Kariawase';
            $next_location = 'tanpoire';
        }
        if ($request->get('location') == 'tanpoire') {
            $title_location = 'Tanpoire';
            $next_location = 'perakitanawal';
        }
        if ($request->get('location') == 'perakitanawal') {
            $title_location = 'Perakitan Awal';
            $next_location = 'tanpoawase';
        }
        if ($request->get('location') == 'tanpoawase') {
            $title_location = 'Tanpo Awase';
            $next_location = 'seasoning';
        }
        if ($request->get('location') == 'seasoning') {
            $title_location = 'Seasoning';
            $next_location = 'kango';
        }
        if ($request->get('location') == 'kango') {
            $title_location = 'Kango';
            $next_location = 'renraku';
        }
        if ($request->get('location') == 'renraku') {
            $title_location = 'Renraku (Chousei)';
            $next_location = 'qa-fungsi';
        }
        if ($request->get('location') == 'qa-fungsi') {
            $title_location = 'QA Cek Fungsi';
            $next_location = 'fukiage1';
        }
        if ($request->get('location') == 'fukiage1') {
            $title_location = 'Fukiage Awal';
            $next_location = 'qa-visual1';
        }
        if ($request->get('location') == 'qa-visual1') {
            $title_location = 'QA Cek Visual 1';
            $next_location = 'fukiage2';
        }
        if ($request->get('location') == 'fukiage2') {
            $title_location = 'Fukiage Akhir';
            $next_location = 'qa-visual2';
        }
        if ($request->get('location') == 'qa-visual2') {
            $title_location = 'QA Cek Visual 2';
            $next_location = 'packing';
        }
        if ($request->get('location') == 'packing') {
            $title_location = 'Packing';
            $next_location = 'warehouse';
        }

        if ($location != "") {
            $query = "SELECT
			model,
			sum( plan ) AS plan,
			sum( out_item ) AS out_item,
			sum( in_item ) AS in_item
			FROM
			(
			SELECT
			model,
			quantity AS plan,
			0 AS out_item,
			0 AS in_item
			FROM
			stamp_schedules
			WHERE
			due_date = '" . $now . "' UNION ALL
			SELECT
			model,
			0 AS plan,
			COUNT(
			DISTINCT ( serial_number )) AS out_item,
			0 AS in_item
			FROM
			assembly_details
			WHERE
			location like '%" . $next_location . "%'
			AND date( created_at ) = '" . $now . "'
			GROUP BY
			model UNION ALL
			SELECT
			model,
			0 AS plan,
			0 AS out_item,
			COUNT(
			DISTINCT ( serial_number )) AS in_item
			FROM
			assembly_details
			WHERE
			location like '%" . $location . "%'
			AND date( created_at ) = '" . $now . "'
			GROUP BY
			model
			) AS plan
			GROUP BY
			model
			HAVING
			model LIKE 'YFL%'";

            $chartData = DB::select($query);

            if (date('D') == 'Fri') {
                if (date('Y-m-d h:i:s') >= date('Y-m-d 09:30:00')) {
                    $deduction = 600;
                } elseif (date('Y-m-d h:i:s') >= date('Y-m-d 13:10:00')) {
                    $deduction = 4800;
                } elseif (date('Y-m-d h:i:s') >= date('Y-m-d 15:00:00')) {
                    $deduction = 5400;
                } elseif (date('Y-m-d h:i:s') >= date('Y-m-d 17:30:00')) {
                    $deduction = 5800;
                } elseif (date('Y-m-d h:i:s') >= date('Y-m-d 18:30:00')) {
                    $deduction = 7500;
                } else {
                    $deduction = 0;
                }
            } else {
                if (date('Y-m-d h:i:s') >= date('Y-m-d 09:30:00')) {
                    $deduction = 600;
                } elseif (date('Y-m-d h:i:s') >= date('Y-m-d 12:40:00')) {
                    $deduction = 3000;
                } elseif (date('Y-m-d h:i:s') >= date('Y-m-d 14:30:00')) {
                    $deduction = 3600;
                } elseif (date('Y-m-d h:i:s') >= date('Y-m-d 17:00:00')) {
                    $deduction = 4200;
                } elseif (date('Y-m-d h:i:s') >= date('Y-m-d 18:30:00')) {
                    $deduction = 5700;
                } else {
                    $deduction = 0;
                }
            }

            $query2 = "SELECT
			total.*
			FROM
			(
			SELECT
			max( assembly_details.created_at ) AS last_input,
			count( assembly_details.serial_number ) AS quantity,
			count(
			DISTINCT ( assembly_details.operator_id )) AS manpower,
			ROUND(
			standard_time * count( assembly_details.serial_number )) AS std_time,
			SUM(
			TIMESTAMPDIFF( SECOND, assembly_details.sedang_start_date, assembly_details.sedang_finish_date )) AS act_time
			FROM
			assembly_details
			LEFT JOIN assembly_std_times ON assembly_std_times.model = assembly_details.model
			AND assembly_std_times.location LIKE '%" . $location . "%'
			WHERE
			assembly_details.location LIKE '%" . $location . "%'
			AND DATE( assembly_details.created_at ) = '" . $now . "'
			GROUP BY
			DATE( assembly_details.created_at ),standard_time
		) total";

            $effData = DB::select($query2);
        }
        $response = array(
            'status' => true,
            'chartData' => $chartData,
            'effData' => $effData,
            'title_location' => $title_location,
        );
        return Response::json($response);
    }

    public function indexStampRecord($origin_group_code)
    {

        $code = Process::where('remark', '=', $origin_group_code)->orderBy('id', 'asc')
            ->get();
        if ($origin_group_code == '041') {
            $title = 'Flute';
        } else if ($origin_group_code == '042') {
            $title = 'Clarinet';
        } else {
            $title = 'Saxophone';
        }
        return view('processes.assembly.flute.report.resumes', array(
            'code' => $code,
            'origin_group_code' => $origin_group_code,
            'title' => $title,
        ))
            ->with('page', 'Process Assy')->with('head', 'Assembly Process');
    }

    public function fetchStampRecord(Request $request)
    {
        $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
        $dateto = date('Y-m-d', strtotime($request->get('dateto')));
        $datenow = date('Y-m-d');
        $datefirst = date('Y-m-01');
        $code_details = "";
        $code_log = "";
        $date = "";
        $date2 = "";
        if (strlen($request->get('code')) > 0) {
            if ($request->get('origin_group_code') == '042') {
                $code_log = 'and assembly_operators.location like "%' . $request->get('code') . '%" and assembly_operators.location <> "card-cleaning"';
                $code_details = 'and assembly_operators.location like "%' . $request->get('code') . '%" and assembly_operators.location <> "card-cleaning"';
            } else {
                $code_log = 'and assembly_logs.location like "%' . $request->get('code') . '%" and assembly_logs.location <> "card-cleaning"';
                $code_details = 'and assembly_details.location like "%' . $request->get('code') . '%" and assembly_details.location <> "card-cleaning"';
            }

            if ($request->get('dateto') == null) {
                if ($request->get('datefrom') == null) {
                    $date = "and date(assembly_logs.sedang_start_date) BETWEEN '" . $datefirst . "' and '" . $datenow . "'";
                    $date2 = "and date(assembly_details.sedang_start_date) BETWEEN '" . $datefirst . "' and '" . $datenow . "'";
                } elseif ($request->get('datefrom') != null) {
                    $date = "and date(assembly_logs.sedang_start_date) BETWEEN '" . $datefrom . "' and '" . $datenow . "'";
                    $date2 = "and date(assembly_details.sedang_start_date) BETWEEN '" . $datefrom . "' and '" . $datenow . "'";
                }
            } elseif ($request->get('dateto') != null) {
                if ($request->get('datefrom') == null) {
                    $date = "and date(assembly_logs.sedang_start_date) <= '" . $dateto . "'";
                    $date2 = "and date(assembly_details.sedang_start_date) <= '" . $dateto . "'";
                } elseif ($request->get('datefrom') != null) {
                    $date = "and date(assembly_logs.sedang_start_date) BETWEEN '" . $datefrom . "' and '" . $dateto . "'";
                    $date2 = "and date(assembly_details.sedang_start_date) BETWEEN '" . $datefrom . "' and '" . $dateto . "'";
                }
            }
        } else {
            if ($request->get('serial_number') == '') {
                $code = '';
                if ($request->get('dateto') == null) {
                    if ($request->get('datefrom') == null) {
                        $date = "and date(assembly_logs.sedang_start_date) BETWEEN '" . $datefirst . "' and '" . $datenow . "'";
                        $date2 = "and date(assembly_details.sedang_start_date) BETWEEN '" . $datefirst . "' and '" . $datenow . "'";
                    } elseif ($request->get('datefrom') != null) {
                        $date = "and date(assembly_logs.sedang_start_date) BETWEEN '" . $datefrom . "' and '" . $datenow . "'";
                        $date2 = "and date(assembly_details.sedang_start_date) BETWEEN '" . $datefrom . "' and '" . $datenow . "'";
                    }
                } elseif ($request->get('dateto') != null) {
                    if ($request->get('datefrom') == null) {
                        $date = "and date(assembly_logs.sedang_start_date) <= '" . $dateto . "'";
                        $date2 = "and date(assembly_details.sedang_start_date) <= '" . $dateto . "'";
                    } elseif ($request->get('datefrom') != null) {
                        $date = "and date(assembly_logs.sedang_start_date) BETWEEN '" . $datefrom . "' and '" . $dateto . "'";
                        $date2 = "and date(assembly_details.sedang_start_date) BETWEEN '" . $datefrom . "' and '" . $dateto . "'";
                    }
                }
            }
        }

        // $stamp_detail = DB::SELECT("SELECT
        //     assembly_logs.serial_number,
        //     assembly_logs.model,
        //     assembly_logs.status_material,
        //     1 AS quantity,
        //     assembly_logs.created_at AS st_date,
        //     location AS process_name,
        //     COALESCE ( CONCAT( employee_syncs.employee_id, '<br>', employee_syncs.`name` ), '' ) AS employee
        //     FROM
        //     `assembly_logs`
        //     LEFT JOIN employee_syncs ON employee_syncs.employee_id = assembly_logs.operator_id
        //     where assembly_logs.origin_group_code = '".$request->get('origin_group_code')."'
        //     ".$code." ".$date."
        //     UNION ALL
        //     SELECT
        //     assembly_details.serial_number,
        //     assembly_details.model,
        //     assembly_details.status_material,
        //     1 AS quantity,
        //     assembly_details.created_at AS st_date,
        //     location AS process_name,
        //     COALESCE ( CONCAT( employee_syncs.employee_id, '<br>', employee_syncs.`name` ), '' ) AS employee
        //     FROM
        //     `assembly_details`
        //     LEFT JOIN employee_syncs ON employee_syncs.employee_id = assembly_details.operator_id
        //     where assembly_details.origin_group_code = '".$request->get('origin_group_code')."'
        //     ".$code." ".$date2);

        $sernum_log = "";
        $sernum_detail = "";
        $sernum_locs_log = "";
        $sernum_locs_detail = "";
        if ($request->get('serial_number') != '') {
            $sernum_log = "AND assembly_logs.serial_number = '" . $request->get('serial_number') . "'";
            $sernum_detail = "AND assembly_details.serial_number = '" . $request->get('serial_number') . "'";
        }

        $stamp_detail = DB::SELECT("SELECT
		assembly_details.serial_number,
		assembly_details.model,
		if(assembly_details.origin_group_code = '043',assembly_details.trial,assembly_details.status_material) as status_material,
		1 AS quantity,
		assembly_details.created_at AS st_date,
		assembly_details.location AS process_name,
		COALESCE ( CONCAT( employee_syncs.employee_id, '<br>', employee_syncs.`name` ), '' ) AS employee
	FROM
		`assembly_details`
		LEFT JOIN employee_syncs ON employee_syncs.employee_id = assembly_details.operator_id
		LEFT JOIN assembly_operators ON assembly_operators.employee_id = assembly_details.operator_id
	WHERE
		assembly_details.origin_group_code = '" . $request->get('origin_group_code') . "'
		" . $code_details . " " . $date2 . " " . $sernum_detail . " " . $sernum_locs_detail . " UNION ALL
	SELECT
		assembly_logs.serial_number,
		assembly_logs.model,
		if(assembly_logs.origin_group_code = '043',assembly_logs.trial,assembly_logs.status_material) as status_material,
		1 AS quantity,
		assembly_logs.created_at AS st_date,
		assembly_logs.location AS process_name,
		COALESCE ( CONCAT( employee_syncs.employee_id, '<br>', employee_syncs.`name` ), '' ) AS employee
	FROM
		`assembly_logs`
		LEFT JOIN employee_syncs ON employee_syncs.employee_id = assembly_logs.operator_id
		LEFT JOIN assembly_operators ON assembly_operators.employee_id = assembly_logs.operator_id
	WHERE
		assembly_logs.origin_group_code = '" . $request->get('origin_group_code') . "'
		" . $code_log . " " . $date . " " . $sernum_log . " " . $sernum_locs_log);

        $response = array(
            'status' => true,
            'stamp_detail' => $stamp_detail,
        );
        return Response::json($response);
    }

    public function indexNgReport($process, $origin_group_code)
    {
        if ($process == 'qa') {
            $title = 'NG Report - QA';
            $title_jp = 'QA不良報告';
            $flow = AssemblyFlow::where('process', 'like', '%qa%')->get();

            if (Auth::user()->username == 'PI0904005' || Auth::user()->username == 'PI1910002' || Auth::user()->username == 'PI1108002' || Auth::user()->username == 'PI9710001') {
                return view('processes.assembly.flute.report.ng_report', array(
                    'flow' => $flow,
                    'process' => $process,
                ))->with('page', 'NG Report Assy Fl')
                    ->with('head', 'Assembly Process')
                    ->with('title', $title)
                    ->with('title_jp', $title_jp)
                    ->with('origin_group_code', $origin_group_code);
            } else {
                return view('404');
            }
        } else {
            $title = 'NG Report - Production';
            $title_jp = '生産不良報告';
            $flow = AssemblyFlow::where('process', 'not like', '%qa%')->get();
            return view('processes.assembly.flute.report.ng_report', array(
                'flow' => $flow,
                'process' => $process,
            ))->with('page', 'NG Report Assy Fl')
                ->with('head', 'Assembly Process')
                ->with('title', $title)
                ->with('title_jp', $title_jp)
                ->with('origin_group_code', $origin_group_code);
        }
    }

    public function fetchNgReport($process, $origin_group_code, Request $request)
    {
        $date_from = $request->get('datefrom');
        $date_to = $request->get('dateto');

        if ($date_to == "") {
            if ($date_from == "") {
                $from = date('Y-m') . "-01";
                $now = date('Y-m-d');
            } elseif ($date_from != "") {
                $from = $date_from;
                $now = date('Y-m-d');
            }
        } elseif ($date_to != "") {
            if ($date_from == "") {
                $from = date('Y-m') . "-01";
                $now = $date_to;
            } elseif ($date_from != "") {
                $from = $date_from;
                $now = $date_to;
            }
        }

        if ($process == 'qa') {
            $ng_report = DB::SELECT("SELECT
			*,
			CONCAT( checked.employee_id, '<br>', checked.NAME ) AS checked_by,
			assembly_ng_logs.created_at AS created,
			CONCAT( repaired.employee_id, '<br>', repaired.name ) AS repaires,
			IF
			(
			location LIKE '%qa-fungsi%',(
			SELECT
			GROUP_CONCAT( CONCAT( fungsi_detail.employee_id, '<br>', fungsi_detail.NAME ) )
			FROM
			assembly_details
			LEFT JOIN employee_syncs fungsi_detail ON fungsi_detail.employee_id = assembly_details.operator_id
			WHERE
			location = 'renraku-fungsi'
			AND tag = assembly_ng_logs.tag
			AND serial_number = assembly_ng_logs.serial_number
			),
			(
			SELECT
			GROUP_CONCAT( CONCAT( visual_detail.employee_id, '<br>', visual_detail.NAME ) )
			FROM
			assembly_details
			LEFT JOIN employee_syncs visual_detail ON visual_detail.employee_id = assembly_details.operator_id
			WHERE
			location = 'fukiage1-visual'
			AND tag = assembly_ng_logs.tag
			AND serial_number = assembly_ng_logs.serial_number
			)
			) AS operator_id_details,
			IF
			(
			location LIKE '%qa-fungsi%',(
			SELECT
			GROUP_CONCAT( CONCAT( fungsi_log.employee_id, '<br>', fungsi_log.NAME ) )
			FROM
			assembly_logs
			LEFT JOIN employee_syncs fungsi_log ON fungsi_log.employee_id = assembly_logs.operator_id
			WHERE
			location = 'renraku-fungsi'
			AND tag = assembly_ng_logs.tag
			AND serial_number = assembly_ng_logs.serial_number
			),
			(
			SELECT
			GROUP_CONCAT( CONCAT( visual_log.employee_id, '<br>', visual_log.NAME ) )
			FROM
			assembly_logs
			LEFT JOIN employee_syncs visual_log ON visual_log.employee_id = assembly_logs.operator_id
			WHERE
			location = 'fukiage1-visual'
			AND tag = assembly_ng_logs.tag
			AND serial_number = assembly_ng_logs.serial_number
			)
			) AS operator_id_log
			FROM
			`assembly_ng_logs`
			LEFT JOIN employee_syncs checked ON checked.employee_id = assembly_ng_logs.employee_id
			LEFT JOIN employee_syncs repaired ON repaired.employee_id = assembly_ng_logs.repaired_by
			WHERE
			location LIKE '%qa%'
			AND origin_group_code = '" . $origin_group_code . "'
			AND DATE( assembly_ng_logs.created_at ) BETWEEN '" . $from . "' AND '" . $now . "'");
        } else {
            $ng_report = DB::SELECT("SELECT
			*,
			CONCAT( checked.employee_id, ' - ', checked.NAME ) AS checked_by,
			COALESCE ( CONCAT( caused.employee_id, ' - ', caused.NAME ), operator_id ) AS operator_id_details,
			null as operator_id_log,
			assembly_ng_logs.created_at as created,
			CONCAT( repaired.employee_id, '<br>', repaired.name ) AS repaires
			FROM
			`assembly_ng_logs`
			LEFT JOIN employee_syncs checked ON checked.employee_id = assembly_ng_logs.employee_id
			LEFT JOIN employee_syncs caused ON caused.employee_id = assembly_ng_logs.operator_id
			LEFT JOIN employee_syncs repaired ON repaired.employee_id = assembly_ng_logs.repaired_by
			WHERE
			location NOT LIKE '%qa%'
			AND origin_group_code = '" . $origin_group_code . "'
			AND
			DATE(assembly_ng_logs.created_at) BETWEEN '" . $from . "' AND '" . $now . "'");
        }

        try {
            $response = array(
                'status' => true,
                'ng_report' => $ng_report,
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

    public function indexKdCardCleaning()
    {
        $title = "KD Card Cleaning";
        $title_jp = "";
        return view('processes.assembly.flute.kd_cleaning')
            ->with('page', 'KD Card Cleaning')
            ->with('head', 'Assembly Process')
            ->with('title', $title)
            ->with('title_jp', $title_jp);
    }

    public function scanKdCardCleaning(Request $request)
    {
        try {
            $details = AssemblyDetail::where('tag', $this->dec2hex($request->get('tag')))->first();
            $details2 = AssemblyDetail::where('tag', $this->dec2hex($request->get('tag')))->get();

            if ($details != null) {
                $serials = AssemblySerial::where('serial_number', $details->serial_number)->where('origin_group_code', $details->origin_group_code)->first();
                if ($serials != null) {
                    $serials->forceDelete();
                }

                $stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $details->serial_number)
                    ->where('stamp_inventories.model', '=', $details->model)
                    ->where('origin_group_code', $details->origin_group_code)->first();
                if ($stamp_inventory != null) {
                    $stamp_inventory->forceDelete();
                }
            }

            $inventory = AssemblyInventory::where('tag', $this->dec2hex($request->get('tag')))->first();

            $tag = AssemblyTag::where('tag', $this->dec2hex($request->get('tag')))->first();
            $tag->serial_number = null;
            $tag->model = null;

            $now = date('Y-m-d H:i:s');

            AssemblyDetail::where('tag', $this->dec2hex($request->get('tag')))->forceDelete();

            if ($inventory != null) {
                $inventory->forceDelete();
            }
            $tag->save();

            if ($details != null) {
                $log = new AssemblyLog([
                    'tag' => $details->tag,
                    'serial_number' => $details->serial_number,
                    'model' => $details->model,
                    'location' => 'labelkd-print',
                    'operator_id' => Auth::user()->username,
                    'sedang_start_date' => date('Y-m-d H:i:s'),
                    'sedang_finish_date' => date('Y-m-d H:i:s'),
                    'origin_group_code' => '041',
                    'status_material' => $details->status_material,
                    'created_by' => Auth::user()->username,
                ]);
                $log->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Cleaning Card Success',
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

    public function fetchKdCardCleaning(Request $request)
    {
        try {
            $history = DB::SELECT("SELECT
			*
			FROM
			assembly_logs
			WHERE
			location = 'labelkd-print'
			AND DATE( created_at ) >= DATE_FORMAT( NOW() - INTERVAL 3 DAY, '%Y-%m-%d' )
			AND DATE( created_at ) <= DATE_FORMAT( NOW(), '%Y-%m-%d' )
			order By id desc");
            $response = array(
                'status' => true,
                'history' => $history,
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => 'Failed Get Data',
            );
            return Response::json($response);
        }
    }

    public function indexCardCleaning()
    {
        $title = "Card Cleaning";
        $title_jp = "";
        return view('processes.assembly.flute.card_cleaning')
            ->with('page', 'Card Cleaning')
            ->with('head', 'Assembly Process')
            ->with('title', $title)
            ->with('title_jp', $title_jp);
    }

    public function scanCardCleaning(Request $request)
    {
        try {
            $details = AssemblyDetail::where('tag', $this->dec2hex($request->get('tag')))->first();
            $details2 = AssemblyDetail::where('tag', $this->dec2hex($request->get('tag')))->get();

            if ($details != null) {
                $serials = AssemblySerial::where('serial_number', $details->serial_number)->where('origin_group_code', $details->origin_group_code)->first();
                if ($serials != null) {
                    $serials->forceDelete();
                }

                $stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $details->serial_number)
                    ->where('stamp_inventories.model', '=', $details->model)
                    ->where('origin_group_code', $details->origin_group_code)->first();
                if ($stamp_inventory != null) {
                    $stamp_inventory->forceDelete();
                }
            }

            $inventory = AssemblyInventory::where('tag', $this->dec2hex($request->get('tag')))->first();

            $tag = AssemblyTag::where('tag', $this->dec2hex($request->get('tag')))->first();
            $tag->serial_number = null;
            $tag->model = null;

            $now = date('Y-m-d H:i:s');
            AssemblyDetail::where('tag', $this->dec2hex($request->get('tag')))->forceDelete();

            if ($inventory != null) {
                $inventory->forceDelete();
            }
            $tag->save();

            if ($details != null) {
                $log = new AssemblyLog([
                    'tag' => $details->tag,
                    'serial_number' => $details->serial_number,
                    'model' => $details->model,
                    'location' => 'card-cleaning',
                    'operator_id' => Auth::user()->username,
                    'sedang_start_date' => date('Y-m-d H:i:s'),
                    'sedang_finish_date' => date('Y-m-d H:i:s'),
                    'origin_group_code' => '041',
                    'status_material' => $details->status_material,
                    'created_by' => Auth::user()->username,
                ]);
                $log->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Cleaning Card Success',
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

    public function fetchCardCleaning(Request $request)
    {
        try {
            $history = DB::SELECT("SELECT
			*
			FROM
			assembly_logs
			WHERE
			(location = 'card-cleaning'
			AND DATE( created_at ) >= DATE_FORMAT( NOW() - INTERVAL 3 DAY, '%Y-%m-%d' )
			AND DATE( created_at ) <= DATE_FORMAT( NOW(), '%Y-%m-%d' ))
			or
			(location = 'card-cleaning'
			AND DATE( created_at ) >= DATE_FORMAT( NOW() - INTERVAL 3 DAY, '%Y-%m-%d' )
			AND DATE( created_at ) <= DATE_FORMAT( NOW(), '%Y-%m-%d' ))
			order By id desc");
            $response = array(
                'status' => true,
                'history' => $history,
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => 'Failed Get Data',
            );
            return Response::json($response);
        }
    }

    public function indexSerialNumberReport($process)
    {
        if ($process == 'qa-fungsi') {
            $title = 'Serial Number Report Flute - QA Fungsi';
            $title_jp = '';
        } else if ($process == 'qa-visual1') {
            $title = 'Serial Number Report Flute - QA Visual 1';
            $title_jp = '';
        } else if ($process == 'qa-visual2') {
            $title = 'Serial Number Report Flute - QA Visual 2';
            $title_jp = '';
        }

        $models = db::table('materials')->where('origin_group_code', '=', '041')
            ->where('category', '=', 'FG')
            ->orderBy('model', 'asc')
            ->select('model')
            ->distinct()
            ->get();

        // if (Auth::user()->username == 'PI0904005' || Auth::user()->username == 'PI1910002' || Auth::user()->username == 'PI1108002' || Auth::user()->username == 'PI9710001') {
            return view('processes.assembly.flute.report.serial_number_report', array(
                'process' => $process,
                'models' => $models,
            ))->with('page', 'Serial Number Report Assy Fl')
                ->with('head', 'Assembly Process')
                ->with('title', $title)
                ->with('title_jp', $title_jp);
        // } else {
        //     return view('404');
        // }
    }

    public function fetchSerialNumberReport($process, Request $request)
    {
        try {
            $now = $request->get('datefrom');
            $emp = EmployeeSync::get();

            if ($process == 'qa-fungsi') {
                $report_fungsi = DB::SELECT("SELECT
                    assembly_logs.serial_number,
                    assembly_logs.model AS model_packing,
                    stamp.model AS model_stamp,
                    qa.model AS model_wip,
                    GROUP_CONCAT( qa.note ) AS notes,
                    GROUP_CONCAT(
                    DISTINCT ( prod.operator_id )) AS op_prod,
                    GROUP_CONCAT(
                    DISTINCT ( qa.operator_id )) AS op_qa,
                    GROUP_CONCAT(
                    DISTINCT ( qa.sedang_start_date )) AS datetime_qa,
                    GROUP_CONCAT(
                        DISTINCT (
                            CONCAT(
                                ng.ng_name,
                                '_',
                                ng.ongko,
                                '_',
                                COALESCE ( ng.value_atas, '1' ),
                                '_',
                                COALESCE ( ng.value_bawah, '' ),
                                '_',
                            COALESCE ( ng.value_lokasi, '' ))) 
                    ) AS ng_name,
                    GROUP_CONCAT( ng.decision ) AS ganti_kunci,
                    GROUP_CONCAT(
                    DISTINCT ( ng.created_at )) AS inputed_at,
                    GROUP_CONCAT( ganti_kunci.ongko ) AS ganti_kunci_temuan,
                    DATE( assembly_logs.created_at ) AS packing_date,
                    TIME( assembly_logs.created_at ) AS packing_time 
                FROM
                    assembly_logs
                    JOIN assembly_logs AS stamp ON stamp.serial_number = assembly_logs.serial_number 
                    AND stamp.origin_group_code = assembly_logs.origin_group_code 
                    AND stamp.location = 'stamp-process'
                    JOIN assembly_logs AS prod ON prod.serial_number = assembly_logs.serial_number 
                    AND prod.origin_group_code = assembly_logs.origin_group_code 
                    AND prod.location = 'renraku-fungsi'
                    JOIN assembly_logs AS qa ON qa.serial_number = assembly_logs.serial_number 
                    AND qa.origin_group_code = assembly_logs.origin_group_code 
                    AND qa.location = 'qa-fungsi'
                    LEFT JOIN assembly_ng_logs AS ng ON ng.serial_number = assembly_logs.serial_number 
                    AND ng.origin_group_code = assembly_logs.origin_group_code 
                    AND ng.location = 'qa-fungsi'
                    LEFT JOIN assembly_ng_logs AS ganti_kunci ON ganti_kunci.serial_number = assembly_logs.serial_number 
                    AND ganti_kunci.origin_group_code = assembly_logs.origin_group_code 
                    AND ganti_kunci.ng_name LIKE '%Ganti Kunci%' 
                WHERE
                    DATE( assembly_logs.created_at ) = '" . $now . "'
                    AND assembly_logs.origin_group_code = '041' 
                    AND assembly_logs.location = 'packing' 
                GROUP BY
                    assembly_logs.serial_number,
                    model_packing,
                    model_stamp,
                    model_wip,
                    assembly_logs.created_at");

                $response = array(
                    'status' => true,
                    'report_fungsi' => $report_fungsi,
                    'report_visual1' => null,
                    'report_visual2' => null,
                    'emp' => $emp,
                );
            }

            if ($process == 'qa-visual1') {
                $report_visual1 = DB::SELECT("SELECT
				assembly_logs.serial_number,
				assembly_logs.model AS model_packing,
				stamp.model AS model_stamp,
				qa.model AS model_wip,
				GROUP_CONCAT( qa.note ) AS notes,
				GROUP_CONCAT(
				DISTINCT ( prod.operator_id )) AS op_prod,
				GROUP_CONCAT(
				DISTINCT ( qa.operator_id )) AS op_qa,
				GROUP_CONCAT(
				DISTINCT ( qa.sedang_start_date )) AS datetime_qa,
				GROUP_CONCAT(DISTINCT (
				CONCAT(
				ng.ng_name,
				'_',
				ng.ongko,
				'_',
				COALESCE ( ng.value_atas, '1' ),
				'_',
				COALESCE ( ng.value_bawah, '' ),
				'_',
				COALESCE ( ng.value_lokasi, '' )))
				) AS ng_name,
			GROUP_CONCAT(ng.decision) AS ganti_kunci,
			GROUP_CONCAT(DISTINCT(ng.created_at)) AS inputed_at,
			GROUP_CONCAT(ganti_kunci.ongko) AS ganti_kunci_temuan,
			DATE( assembly_logs.created_at ) AS packing_date,
			TIME( assembly_logs.created_at ) AS packing_time
			FROM
			assembly_logs
			JOIN assembly_logs AS stamp ON stamp.serial_number = assembly_logs.serial_number
			AND stamp.origin_group_code = assembly_logs.origin_group_code
			AND stamp.location = 'stamp-process'
			JOIN assembly_logs AS prod ON prod.serial_number = assembly_logs.serial_number
			AND prod.origin_group_code = assembly_logs.origin_group_code
			AND prod.location = 'fukiage1-visual'
			JOIN assembly_logs AS qa ON qa.serial_number = assembly_logs.serial_number
			AND qa.origin_group_code = assembly_logs.origin_group_code
			AND qa.location = 'qa-visual1'
			LEFT JOIN assembly_ng_logs AS ng ON ng.serial_number = assembly_logs.serial_number
			AND ng.origin_group_code = assembly_logs.origin_group_code
			AND ng.location = 'qa-visual1'
			left join assembly_ng_logs as ganti_kunci on ganti_kunci.serial_number = assembly_logs.serial_number and ganti_kunci.origin_group_code = assembly_logs.origin_group_code and ganti_kunci.ng_name like '%Ganti Kunci%'
			WHERE
			DATE( assembly_logs.created_at ) = '" . $now . "'
			AND assembly_logs.origin_group_code = 041
			AND assembly_logs.location = 'packing'
			GROUP BY
			assembly_logs.serial_number,
			model_packing,
			model_stamp,
			model_wip,
			assembly_logs.created_at");

                $response = array(
                    'status' => true,
                    'report_fungsi' => null,
                    'report_visual1' => $report_visual1,
                    'report_visual2' => null,
                    'emp' => $emp,
                );
            }

            if ($process == 'qa-visual2') {
                $report_visual2 = DB::SELECT("SELECT
				assembly_logs.serial_number,
				assembly_logs.model AS model_packing,
				stamp.model AS model_stamp,
				qa.model AS model_wip,
				GROUP_CONCAT( qa.note ) AS notes,
				GROUP_CONCAT(
				DISTINCT ( prod.operator_id )) AS op_prod,
				GROUP_CONCAT(
				DISTINCT ( qa.operator_id )) AS op_qa,
				GROUP_CONCAT(
				DISTINCT ( qa.sedang_start_date )) AS datetime_qa,
				GROUP_CONCAT(DISTINCT (
				CONCAT(
				ng.ng_name,
				'_',
				ng.ongko,
				'_',
				COALESCE ( ng.value_atas, '1' ),
				'_',
				COALESCE ( ng.value_bawah, '' ),
				'_',
				COALESCE ( ng.value_lokasi, '' )))
				) AS ng_name,
				GROUP_CONCAT(ng.decision) AS ganti_kunci,
				GROUP_CONCAT(DISTINCT(ng.created_at)) AS inputed_at,
				GROUP_CONCAT(ganti_kunci.ongko) AS ganti_kunci_temuan,
				DATE( assembly_logs.created_at ) AS packing_date,
				TIME( assembly_logs.created_at ) AS packing_time
				FROM
				assembly_logs
				JOIN assembly_logs AS stamp ON stamp.serial_number = assembly_logs.serial_number
				AND stamp.origin_group_code = assembly_logs.origin_group_code
				AND stamp.location = 'stamp-process'
				JOIN assembly_logs AS prod ON prod.serial_number = assembly_logs.serial_number
				AND prod.origin_group_code = assembly_logs.origin_group_code
				AND prod.location = 'fukiage1-visual'
				JOIN assembly_logs AS qa ON qa.serial_number = assembly_logs.serial_number
				AND qa.origin_group_code = assembly_logs.origin_group_code
				AND qa.location = 'qa-visual2'
				LEFT JOIN assembly_ng_logs AS ng ON ng.serial_number = assembly_logs.serial_number
				AND ng.origin_group_code = assembly_logs.origin_group_code
				AND ng.location = 'qa-visual2'
				left join assembly_ng_logs as ganti_kunci on ganti_kunci.serial_number = assembly_logs.serial_number and ganti_kunci.origin_group_code = assembly_logs.origin_group_code and ganti_kunci.ng_name like '%Ganti Kunci%'
				WHERE
				DATE( assembly_logs.created_at ) = '" . $now . "'
				AND assembly_logs.origin_group_code = 041
				AND assembly_logs.location = 'packing'
				GROUP BY
				assembly_logs.serial_number,
				model_packing,
				model_stamp,
				model_wip,
				assembly_logs.created_at");

                $response = array(
                    'status' => true,
                    'report_fungsi' => null,
                    'report_visual1' => null,
                    'report_visual2' => $report_visual2,
                    'emp' => $emp,
                );
            }
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function indexSerialNumberReportCl($process)
    {
        if ($process == 'qa-kensa') {
            $title = 'Serial Number Report Clarinet - QA Kensa';
            $title_jp = '';
        }

        $models = db::table('materials')->where('origin_group_code', '=', '042')
            ->where('category', '=', 'FG')
            ->orderBy('model', 'asc')
            ->select('model')
            ->distinct()
            ->get();

        if (Auth::user()->username == 'PI0904005' || Auth::user()->username == 'PI1910002' || Auth::user()->username == 'PI1108002' || Auth::user()->username == 'PI9710001') {
            return view('processes.assembly.clarinet.report.serial_number_report', array(
                'process' => $process,
                'models' => $models,
            ))->with('page', 'Serial Number Report Assy Cl')
                ->with('head', 'Assembly Process')
                ->with('title', $title)
                ->with('title_jp', $title_jp);
        } else {
            return view('404');
        }
    }

    public function fetchSerialNumberReportCl($process, Request $request)
    {
        try {
            $date_from = $request->get('datefrom');
            $date_to = $request->get('dateto');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = "DATE(NOW())";
                    $last = "DATE(NOW())";
                } else {
                    $first = "DATE(NOW())";
                    $last = "'" . $date_to . "'";
                }
            } else {
                if ($date_to == "") {
                    $first = "'" . $date_from . "'";
                    $last = "DATE(NOW())";
                } else {
                    $first = "'" . $date_from . "'";
                    $last = "'" . $date_to . "'";
                }
            }
            $emp = EmployeeSync::get();

            if ($process == 'qa-kensa') {
                $report_fungsi = DB::SELECT("SELECT
				assembly_logs.serial_number,
				assembly_logs.model AS model_packing,
				IF(assembly_logs.model like '%450%' or assembly_logs.model like '%400%',CONCAT(assembly_logs.status_material,',',SPLIT_STRING(stamp.status_material, '/', 1),'/',SPLIT_STRING(stamp.status_material, '/', 2),'/',SPLIT_STRING(stamp.status_material, '/', 3),'/',SPLIT_STRING(stamp.status_material, '/', 4)) ,assembly_logs.status_material) as status_material,
				stamp.model AS model_stamp,
				qa.model AS model_wip,
				GROUP_CONCAT( DISTINCT(qa.note) ) AS notes,
				GROUP_CONCAT(
				DISTINCT ( prod.operator_id )) AS op_prod,
				GROUP_CONCAT(
				DISTINCT ( qa.operator_id )) AS op_qa,
				GROUP_CONCAT(
				DISTINCT ( qa.sedang_start_date )) AS datetime_qa,
				GROUP_CONCAT(
				DISTINCT (
				CONCAT(
				ng.ng_name,
				'_',
				ng.ongko,
				'_',
				COALESCE ( ng.value_atas, '1' ),
				'_',
				COALESCE ( ng.value_bawah, '' ),
				'_',
				COALESCE ( ng.value_lokasi, '' )))
				) AS ng_name,
				GROUP_CONCAT( distinct(ng.decision) ) AS ganti_kunci,
				GROUP_CONCAT(
				DISTINCT ( ng.created_at )) AS inputed_at,
				GROUP_CONCAT( ganti_kunci.ongko ) AS ganti_kunci_temuan,
				DATE( assembly_logs.created_at ) AS packing_date,
				TIME( assembly_logs.created_at ) AS packing_time
				FROM
				assembly_logs
				LEFT JOIN assembly_logs AS stamp ON stamp.serial_number = assembly_logs.serial_number
				AND stamp.origin_group_code = assembly_logs.origin_group_code
				AND stamp.location = 'registration-process'
				LEFT JOIN assembly_logs AS prod ON prod.serial_number = assembly_logs.serial_number
				AND prod.origin_group_code = assembly_logs.origin_group_code
				AND prod.location = 'kensa-process'
				LEFT JOIN assembly_logs AS qa ON qa.serial_number = assembly_logs.serial_number
				AND qa.origin_group_code = assembly_logs.origin_group_code
				AND qa.location = 'qa-kensa'
				LEFT JOIN assembly_ng_logs AS ng ON ng.serial_number = assembly_logs.serial_number
				AND ng.origin_group_code = assembly_logs.origin_group_code
				AND ng.location = 'qa-kensa'
				LEFT JOIN assembly_ng_logs AS ganti_kunci ON ganti_kunci.serial_number = assembly_logs.serial_number
				AND ganti_kunci.origin_group_code = assembly_logs.origin_group_code
				AND ganti_kunci.ng_name LIKE '%Ganti Kunci%'
				WHERE
				DATE( assembly_logs.created_at ) >= " . $first . "
				AND DATE( assembly_logs.created_at ) <= " . $last . "
				AND assembly_logs.origin_group_code = 042
				AND assembly_logs.location = 'packing'
				GROUP BY
				assembly_logs.serial_number,
				status_material,
				model_packing,
				model_stamp,
				model_wip,
				assembly_logs.created_at");

                $response = array(
                    'status' => true,
                    'report_fungsi' => $report_fungsi,
                    'report_visual' => null,
                    'report_tenor' => null,
                    'emp' => $emp,
                );
            }
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function indexSerialNumberReportSax($process)
    {
        if ($process == 'qa-fungsi') {
            $title = 'Serial Number Report Saxophone - QA Fungsi';
            $title_jp = '';
        } else if ($process == 'qa-visual') {
            $title = 'Serial Number Report Saxophone- QA Visual';
            $title_jp = '';
        } else if ($process == 'qa-kensa') {
            $title = 'Serial Number Report Saxophone- QA Tenor';
            $title_jp = '';
        }

        $models = db::table('materials')->where('origin_group_code', '=', '043')
            ->where('category', '=', 'FG')
            ->orderBy('model', 'asc')
            ->select('model')
            ->distinct()
            ->get();

        // if (Auth::user()->username == 'PI0904005' || Auth::user()->username == 'PI1910002' || Auth::user()->username == 'PI1108002' || Auth::user()->username == 'PI9710001') {
            return view('processes.assembly.saxophone.report.serial_number_report', array(
                'process' => $process,
                'models' => $models,
            ))->with('page', 'Serial Number Report Assy Sax')
                ->with('head', 'Assembly Process')
                ->with('title', $title)
                ->with('title_jp', $title_jp);
        // } else {
        //     return view('404');
        // }
    }

    public function fetchSerialNumberReportSax($process, Request $request)
    {
        try {
            $date_from = $request->get('datefrom');
            $date_to = $request->get('dateto');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = "DATE(NOW())";
                    $last = "DATE(NOW())";
                } else {
                    $first = "DATE(NOW())";
                    $last = "'" . $date_to . "'";
                }
            } else {
                if ($date_to == "") {
                    $first = "'" . $date_from . "'";
                    $last = "DATE(NOW())";
                } else {
                    $first = "'" . $date_from . "'";
                    $last = "'" . $date_to . "'";
                }
            }

            $emp = EmployeeSync::get();

            if ($process == 'qa-fungsi') {
                $report_fungsi = DB::SELECT("SELECT
				assembly_logs.serial_number,
				assembly_logs.model AS model_packing,
				assembly_logs.status_material,
				stamp.model AS model_stamp,
				stamp.trial as trials,
				qa.model AS model_wip,
				GROUP_CONCAT( qa.note ) AS notes,
				GROUP_CONCAT(
				DISTINCT ( prod.operator_id )) AS op_prod,
				GROUP_CONCAT(
				DISTINCT ( qa.operator_id )) AS op_qa,
				GROUP_CONCAT(
				DISTINCT ( qa.sedang_start_date )) AS datetime_qa,
				GROUP_CONCAT(
				DISTINCT (
				CONCAT(
				ng.ng_name,
				'_',
				ng.ongko,
				'_',
				COALESCE ( ng.value_atas, '1' ),
				'_',
				COALESCE ( ng.value_bawah, '' ),
				'_',
				COALESCE ( ng.value_lokasi, '' )))
				) AS ng_name,
				GROUP_CONCAT( ng.decision ) AS ganti_kunci,
				GROUP_CONCAT(
				DISTINCT ( ng.created_at )) AS inputed_at,
				GROUP_CONCAT( ganti_kunci.ongko ) AS ganti_kunci_temuan,
				DATE( assembly_logs.created_at ) AS packing_date,
				TIME( assembly_logs.created_at ) AS packing_time
				FROM
				assembly_logs
				LEFT JOIN assembly_logs AS stamp ON stamp.serial_number = assembly_logs.serial_number
				AND stamp.origin_group_code = assembly_logs.origin_group_code
				AND stamp.location = 'registration-process'
				LEFT JOIN assembly_logs AS prod ON prod.serial_number = assembly_logs.serial_number
				AND prod.origin_group_code = assembly_logs.origin_group_code
				AND prod.location = 'kensa-process'
				LEFT JOIN assembly_logs AS qa ON ( qa.serial_number = assembly_logs.serial_number AND qa.origin_group_code = assembly_logs.origin_group_code AND qa.location = 'qa-fungsi' )
				OR ( qa.serial_number = assembly_logs.serial_number AND qa.origin_group_code = assembly_logs.origin_group_code AND qa.location = 'qa-kensa' )
				LEFT JOIN assembly_ng_logs AS ng ON ( ng.serial_number = assembly_logs.serial_number AND ng.origin_group_code = assembly_logs.origin_group_code AND ng.location = 'qa-fungsi' )
				OR ( ng.serial_number = assembly_logs.serial_number AND ng.origin_group_code = assembly_logs.origin_group_code AND ng.location = 'qa-kensa' )
				LEFT JOIN assembly_ng_logs AS ganti_kunci ON ganti_kunci.serial_number = assembly_logs.serial_number
				AND ganti_kunci.origin_group_code = assembly_logs.origin_group_code
				AND ganti_kunci.ng_name LIKE '%Ganti Kunci%'
				WHERE
				DATE( assembly_logs.created_at ) >= " . $first . "
				AND DATE( assembly_logs.created_at ) <= " . $last . "
				AND assembly_logs.origin_group_code = 043
				AND assembly_logs.location = 'packing'
				AND assembly_logs.model LIKE '%YAS%'
				GROUP BY
				assembly_logs.serial_number,
				assembly_logs.status_material,
				model_packing,
				trials,
				model_stamp,
				model_wip,
				assembly_logs.created_at");

                $response = array(
                    'status' => true,
                    'report_fungsi' => $report_fungsi,
                    'report_visual' => null,
                    'report_tenor' => null,
                    'emp' => $emp,
                );
            }

            if ($process == 'qa-visual') {
                $report_visual = DB::SELECT("SELECT
				assembly_logs.serial_number,
				assembly_logs.model AS model_packing,
				stamp.model AS model_stamp,
				stamp.trial as trials,
				qa.model AS model_wip,
				GROUP_CONCAT( qa.note ) AS notes,
				GROUP_CONCAT(
				DISTINCT ( prod.operator_id )) AS op_prod,
				GROUP_CONCAT(
				DISTINCT ( qa.operator_id )) AS op_qa,
				GROUP_CONCAT(
				DISTINCT ( qa.sedang_start_date )) AS datetime_qa,
				GROUP_CONCAT(
				DISTINCT (
				CONCAT(
				ng.ng_name,
				'_',
				ng.ongko,
				'_',
				COALESCE ( ng.value_atas, '1' ),
				'_',
				COALESCE ( ng.value_bawah, '' ),
				'_',
				COALESCE ( ng.value_lokasi, '' )))
				) AS ng_name,
				GROUP_CONCAT( ng.decision ) AS ganti_kunci,
				assembly_logs.status_material,
				GROUP_CONCAT(
				DISTINCT ( ng.created_at )) AS inputed_at,
				GROUP_CONCAT( ganti_kunci.ongko ) AS ganti_kunci_temuan,
				DATE( assembly_logs.created_at ) AS packing_date,
				TIME( assembly_logs.created_at ) AS packing_time
				FROM
				assembly_logs
				LEFT JOIN assembly_logs AS stamp ON stamp.serial_number = assembly_logs.serial_number
				AND stamp.origin_group_code = assembly_logs.origin_group_code
				AND stamp.location = 'registration-process'
				LEFT JOIN assembly_logs AS prod ON prod.serial_number = assembly_logs.serial_number
				AND prod.origin_group_code = assembly_logs.origin_group_code
				AND prod.location = 'kensa-process'
				LEFT JOIN assembly_logs AS qa ON ( qa.serial_number = assembly_logs.serial_number AND qa.origin_group_code = assembly_logs.origin_group_code AND qa.location = 'qa-visual' )
				OR ( qa.serial_number = assembly_logs.serial_number AND qa.origin_group_code = assembly_logs.origin_group_code AND qa.location = 'qa-kensa' )
				LEFT JOIN assembly_ng_logs AS ng ON ( ng.serial_number = assembly_logs.serial_number AND ng.origin_group_code = assembly_logs.origin_group_code AND ng.location = 'qa-visual' )
				OR ( ng.serial_number = assembly_logs.serial_number AND ng.origin_group_code = assembly_logs.origin_group_code AND ng.location = 'qa-kensa' )
				LEFT JOIN assembly_ng_logs AS ganti_kunci ON ganti_kunci.serial_number = assembly_logs.serial_number
				AND ganti_kunci.origin_group_code = assembly_logs.origin_group_code
				AND ganti_kunci.ng_name LIKE '%Ganti Kunci%'
				WHERE
				DATE( assembly_logs.created_at ) >= " . $first . "
				AND DATE( assembly_logs.created_at ) <= " . $last . "
				AND assembly_logs.origin_group_code = 043
				AND assembly_logs.location = 'packing'
				AND assembly_logs.model LIKE '%YAS%'
				GROUP BY
				assembly_logs.serial_number,
				assembly_logs.status_material,
				model_packing,
				trials,
				model_stamp,
				model_wip,
				assembly_logs.created_at");

                $response = array(
                    'status' => true,
                    'report_fungsi' => null,
                    'report_visual' => $report_visual,
                    'report_tenor' => null,
                    'emp' => $emp,
                );
            }

            if ($process == 'qa-kensa') {
                $report_tenor = DB::SELECT("SELECT
				assembly_logs.serial_number,
				assembly_logs.model AS model_packing,
				stamp.model AS model_stamp,
				stamp.trial as trials,
				qa.model AS model_wip,
				GROUP_CONCAT( qa.note ) AS notes,
				GROUP_CONCAT(
				DISTINCT ( prod.operator_id )) AS op_prod,
				GROUP_CONCAT(
				DISTINCT ( qa.operator_id )) AS op_qa,
				GROUP_CONCAT(
				DISTINCT ( qa.sedang_start_date )) AS datetime_qa,
				assembly_logs.status_material,
				GROUP_CONCAT(
				DISTINCT (
				CONCAT(
				ng.ng_name,
				'_',
				ng.ongko,
				'_',
				COALESCE ( ng.value_atas, '1' ),
				'_',
				COALESCE ( ng.value_bawah, '' ),
				'_',
				COALESCE ( ng.value_lokasi, '' )))
				) AS ng_name,

				GROUP_CONCAT( ng.decision ) AS ganti_kunci,
				GROUP_CONCAT(
				DISTINCT ( ng.created_at )) AS inputed_at,
				GROUP_CONCAT( ganti_kunci.ongko ) AS ganti_kunci_temuan,
				DATE( assembly_logs.created_at ) AS packing_date,
				TIME( assembly_logs.created_at ) AS packing_time
				FROM
				assembly_logs
				LEFT JOIN assembly_logs AS stamp ON stamp.serial_number = assembly_logs.serial_number
				AND stamp.origin_group_code = assembly_logs.origin_group_code
				AND stamp.location = 'registration-process'
				LEFT JOIN assembly_logs AS prod ON prod.serial_number = assembly_logs.serial_number
				AND prod.origin_group_code = assembly_logs.origin_group_code
				AND prod.location = 'kensa-process'
				LEFT JOIN assembly_logs AS qa ON ( qa.serial_number = assembly_logs.serial_number AND qa.origin_group_code = assembly_logs.origin_group_code AND qa.location = 'qa-fungsi' )
				OR ( qa.serial_number = assembly_logs.serial_number AND qa.origin_group_code = assembly_logs.origin_group_code AND qa.location = 'qa-kensa' )
				LEFT JOIN assembly_ng_logs AS ng ON ( ng.serial_number = assembly_logs.serial_number AND ng.origin_group_code = assembly_logs.origin_group_code AND ng.location = 'qa-fungsi' )
				OR ( ng.serial_number = assembly_logs.serial_number AND ng.origin_group_code = assembly_logs.origin_group_code AND ng.location = 'qa-kensa' )
				LEFT JOIN assembly_ng_logs AS ganti_kunci ON ganti_kunci.serial_number = assembly_logs.serial_number
				AND ganti_kunci.origin_group_code = assembly_logs.origin_group_code
				AND ganti_kunci.ng_name LIKE '%Ganti Kunci%'
				WHERE
				DATE( assembly_logs.created_at ) >= " . $first . "
				AND DATE( assembly_logs.created_at ) <= " . $last . "
				AND assembly_logs.origin_group_code = 043
				AND assembly_logs.location = 'packing'
				AND assembly_logs.model LIKE '%YTS%'
				GROUP BY
				assembly_logs.serial_number,
				assembly_logs.status_material,
				model_packing,
				trials,
				model_stamp,
				model_wip,
				assembly_logs.created_at");

                $response = array(
                    'status' => true,
                    'report_fungsi' => null,
                    'report_visual' => null,
                    'report_tenor' => $report_tenor,
                    'emp' => $emp,
                );
            }
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function indexReturn($origin_group_code)
    {
        if ($origin_group_code == '041') {
            $title = 'Flute Return';
        } else if ($origin_group_code == '042') {
            $title = 'Clarinet Return';
        } else if ($origin_group_code == '043') {
            $title = 'Saxophone Return';
        }

        return view('processes.assembly.return')
            ->with('page', 'Process Assy')
            ->with('head', 'Assembly Process')
            ->with('title', $title)
            ->with('origin_group_code', $origin_group_code);
    }

    public function scanReturn(Request $request)
    {
        if (strlen($request->get('tag')) < 9) {
            $stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $request->get('tag'))
                ->where('origin_group_code', $request->get('origin_group_code'))->first();
            if ($stamp_inventory) {
                $stamp_inventory->status = 'return';
                $stamp_inventory->process_code = 1;
                $log = LogProcess::where('log_processes.serial_number', '=', $request->get('tag'))
                    ->where('log_processes.process_code', '!=', 1)
                    ->where('origin_group_code', $request->get('origin_group_code'));

                $stamp_inventory->save();
                $log->forceDelete();
            }
        } else {
            $tag = $request->get('tag');

            $asstag = AssemblyTag::where('tag', strtoupper($this->dec2hex($tag)))->where('serial_number', '!=', null)->first();

            if ($asstag) {
                $inventory = AssemblyInventory::where('tag', strtoupper($this->dec2hex($tag)))->first();
                if ($inventory) {
                    $inventory->delete();
                }

                $serials = AssemblySerial::where('serial_number', $asstag->serial_number)->where('origin_group_code', $asstag->origin_group_code)->forceDelete();

                $ng_log = AssemblyNgLog::where('tag', strtoupper($this->dec2hex($tag)))->where('origin_group_code', $request->get('origin_group_code'))->first();
                if ($ng_log) {
                    $ng_log = AssemblyNgLog::where('tag', strtoupper($this->dec2hex($tag)))->where('origin_group_code', $request->get('origin_group_code'))->forceDelete();
                }

                $ng_temp = AssemblyNgTemp::where('tag', strtoupper($this->dec2hex($tag)))->where('origin_group_code', $request->get('origin_group_code'))->first();
                if ($ng_temp) {
                    $ng_temp = AssemblyNgTemp::where('tag', strtoupper($this->dec2hex($tag)))->where('origin_group_code', $request->get('origin_group_code'))->forceDelete();
                }

                $details = AssemblyDetail::where('tag', strtoupper($this->dec2hex($tag)))->where('origin_group_code', $request->get('origin_group_code'))->first();
                if ($details) {
                    $details = AssemblyDetail::where('tag', strtoupper($this->dec2hex($tag)))->where('origin_group_code', $request->get('origin_group_code'))->forceDelete();
                }

                $stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $asstag->serial_number)
                    ->where('origin_group_code', $request->get('origin_group_code'))->first();
                if ($stamp_inventory) {
                    $stamp_inventory->status = 'return';
                    $stamp_inventory->process_code = 1;
                    $log = LogProcess::where('log_processes.serial_number', '=', $asstag->serial_number)
                        ->where('log_processes.process_code', '!=', 1)
                        ->where('origin_group_code', $request->get('origin_group_code'))->get();

                    $stamp_inventory->save();
                    if ($log) {
                        $log = LogProcess::where('log_processes.serial_number', '=', $asstag->serial_number)
                            ->where('log_processes.process_code', '!=', 1)
                            ->where('origin_group_code', $request->get('origin_group_code'))->forceDelete();
                    }
                }

                $seasoning = DB::connection('ympimis_2')->table('assembly_seasonings')->where('material', 'like', '%' . $asstag->serial_number . '%')->first();
                if ($seasoning) {
                    $deleteseasoning = DB::connection('ympimis_2')->table('assembly_seasonings')->where('material', 'like', '%' . $asstag->serial_number . '%')->delete();
                }

                $asstag->serial_number = null;
                $asstag->model = null;
                $asstag->save();
            }
        }
        try {
            $response = array(
                'status' => true,
                'message' => 'Return Success',
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => 'Return Failed',
            );
            return Response::json($response);
        }
    }

    public function fetchReturn(Request $request)
    {
        $stamp_inventories = StampInventory::where('origin_group_code', '=', $request->get('origin_group_code'))
            ->where('status', '=', 'return')
            ->orderBy('updated_at', 'desc')
            ->get();

        return DataTables::of($stamp_inventories)
            ->make(true);
    }

    public function indexSerialNumberJanEan()
    {
        $title = 'JAN / EAN / UPC';
        $title_jp = '';
        // $mpdl = MaterialPlantDataList::get();
        $mpdl = Material::get();

        return view('processes.assembly.serial_number')->with('page', 'Serial Number')->with('head', 'Serial Number')->with('title', $title)->with('title_jp', $title_jp)->with('mpdl', $mpdl)->with('mpdl2', $mpdl);
    }

    public function fetchSerialNumberJanEan(Request $request)
    {
        try {
            $janean = StampHierarchy::select('stamp_hierarchies.*', 'material_plant_data_lists.material_description')->leftjoin('material_plant_data_lists', 'material_plant_data_lists.material_number', 'stamp_hierarchies.finished')->get();

            $response = array(
                'status' => true,
                'janean' => $janean,
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

    public function inputSerialNumberJanEan(Request $request)
    {
        try {
            $model = $request->get('model');
            $finished = $request->get('finished');
            $janean = $request->get('janean');
            $upc = $request->get('upc');
            $remark = $request->get('remark');

            $input = StampHierarchy::insert([
                'model' => $model,
                'finished' => $finished,
                'janean' => $janean,
                'upc' => $upc,
                'remark' => $remark,
                'created_by' => Auth::user()->id,
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

    public function updateSerialNumberJanEan(Request $request)
    {
        try {
            $model = $request->get('model');
            $finished = $request->get('finished');
            $janean = $request->get('janean');
            $upc = $request->get('upc');
            $remark = $request->get('remark');

            $input = StampHierarchy::where('id', $request->get('id'))->update([
                'model' => $model,
                'finished' => $finished,
                'janean' => $janean,
                'upc' => $upc,
                'remark' => $remark,
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

    public function dec2hex($number)
    {

        $hexvalues = array('0', '1', '2', '3', '4', '5', '6', '7',
            '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
        $hexval = '';
        while ($number != '0') {
            $hexval = $hexvalues[bcmod($number, '16')] . $hexval;
            $number = bcdiv($number, '16', 0);
        }
        return $hexval;
    }

    public function indexCaseMenu()
    {
        $title = "Case Menu";
        $title_jp = "";

        return view('case.index_case', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Case');
    }

    public function indexCase()
    {

        $employees = EmployeeSync::whereNull('end_date')->where('department', '=', 'Woodwind Instrument - Final Assembly (WI-FA) Department')->get();

        return view('case.index', array(
            'title' => 'Kontrol Case Final Assy',
            'title_jp' => '',
            'employees' => $employees,
        ))->with('page', 'Case')->with('head', 'Case');
    }

    public function fetchCaseList(Request $request)
    {
        $lists = db::connection('ympimis_2')
            ->table('case_stocks')
            ->whereNull('deleted_at')
            ->select('material_number', 'material_description', 'qty')
            ->orderBy('material_number', 'ASC')
            ->get();

        $response = array(
            'status' => true,
            'lists' => $lists,
            'message' => 'Data Berhasil Diambil',
        );
        return Response::json($response);
    }

    public function confirmCase(Request $request)
    {

        try {
            $log = DB::connection('ympimis_2')->table('case_logs')->insert([
                'material_number' => $request->get('material'),
                'material_description' => $request->get('description'),
                'qty' => $request->get('quantity'),
                'created_by' => Auth::id(),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $stock = DB::connection('ympimis_2')
                ->table('case_stocks')
                ->where('material_number', $request->get('material'))
                ->first();

            $stock_update = DB::connection('ympimis_2')
                ->table('case_stocks')
                ->where('material_number', $request->get('material'))
                ->update([
                    'qty' => $stock->qty + $request->get('quantity'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $response = array(
                'status' => true,
                'message' => 'Sukses Input ID',
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

    public function fetchCaseResume(Request $request)
    {

        $resumes = db::connection('ympimis_2')
            ->table('case_logs')
            ->whereNull('deleted_at')
            ->where(db::raw('date(created_at)'), '=', date('Y-m-d'))
            ->select('material_number', 'material_description', 'qty', 'created_at', 'created_by')
            ->orderBy('material_number', 'ASC')
            ->get();

        $response = array(
            'status' => true,
            'resumes' => $resumes,
        );
        return Response::json($response);
    }

    public function indexCaseAudit()
    {
        $employees = EmployeeSync::whereNull('end_date')->where('department', '=', 'Woodwind Instrument - Final Assembly (WI-FA) Department')->get();

        return view('case.index_case_audit', array(
            'title' => 'Kontrol Case Final Assy',
            'title_jp' => '',
            'employees' => $employees,
        ))->with('page', 'Case')->with('head', 'Case');
    }

    public function fetchCaseAudit(Request $request)
    {

        $lists = db::connection('ympimis_2')
            ->table('case_stocks')
            ->whereNull('case_stocks.deleted_at')
            ->where('hpl', $request->get('loc'))
            ->select('case_stocks.id', 'case_stocks.material_number', 'case_stocks.material_description', 'case_stocks.qty')
            ->orderBy('case_stocks.material_number', 'ASC')
            ->get();

        $audits = db::connection('ympimis_2')
            ->table('case_audits')
            ->whereNull('case_audits.deleted_at')
            ->where('tanggal', date('Y-m-d'))
            ->select('case_audits.qty_audit', 'case_audits.material_number')
            ->get();

        $response = array(
            'status' => true,
            'lists' => $lists,
            'audits' => $audits,
            'message' => 'Data Berhasil Diambil',
        );
        return Response::json($response);
    }

    public function fetchCaseAuditConfirm(Request $request)
    {
        $id = $request->get('id');
        $material_number = $request->get('material_number');
        $material_description = $request->get('material_description');
        $qty = $request->get('qty');
        $qty_audit = $request->get('qty_audit');

        try {
            $log = DB::connection('ympimis_2')->table('case_audits')->updateOrInsert(
                ['material_number' => $material_number, 'tanggal' => date('Y-m-d')],
                [
                    'material_description' => $material_description,
                    'qty' => $qty,
                    'qty_audit' => $qty_audit,
                    'created_by' => Auth::id(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]
            );

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

    public function fetchCaseAuditDelete(Request $request)
    {
        $id = $request->get('id');
        $material_number = $request->get('material_number');

        try {
            $log = DB::connection('ympimis_2')
                ->table('case_audits')
                ->where('material_number', '=', $material_number)
                ->where('tanggal', '=', date('Y-m-d'))
                ->delete();

            $response = array(
                'status' => true,
                'message' => 'Delete Data Berhasil',
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

    public function reportCase()
    {
        $title = 'Report Pengambilan Case';
        $title_jp = '';

        return view('case.report_case', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Report Pengambilan Case');
    }

    public function fetchReportCase(Request $request)
    {
        $loc = $request->get('location');

        if ($request->get('location') == '') {
            $loc = "";
        } else {
            $loc = "AND location = '" . $request->get('location') . "'";
        }

        $lists = db::connection('ympimis_2')
            ->select("
		SELECT
	        *
		FROM
		case_logs
		WHERE
		deleted_at IS NULL
		ORDER BY id desc
		");

        $response = array(
            'status' => true,
            'lists' => $lists,
        );
        return Response::json($response);
    }

    public function reportCaseAudit()
    {
        $title = 'Report Audit Kesesuaian Case';
        $title_jp = '';

        return view('case.report_case_audit', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Report Audit Pengambilan Case');
    }

    public function fetchReportCaseAudit(Request $request)
    {
        $lists = db::connection('ympimis_2')
            ->select("
		SELECT
		case_audits.tanggal,
		case_stocks.hpl,
		COUNT(case_audits.id) as jumlah_audit
		FROM
		case_audits
		JOIN case_stocks ON case_stocks.material_number = case_audits.material_number
		WHERE
		case_audits.deleted_at IS NULL
		GROUP BY
		tanggal, hpl
		ORDER BY
		case_audits.id DESC
		");

        $response = array(
            'status' => true,
            'lists' => $lists,
        );
        return Response::json($response);
    }

    public function fetchReportCaseAuditDetail(Request $request)
    {
        try {
            $detail = db::connection('ympimis_2')->SELECT("
			SELECT
			case_audits.material_number,
			case_audits.material_description,
			case_audits.qty,
			case_audits.qty_audit,
			case_stocks.hpl
			FROM
			case_audits
			JOIN case_stocks ON case_stocks.material_number = case_audits.material_number
			WHERE
			case_audits.deleted_at IS NULL
			AND tanggal = '" . $request->get('tanggal') . "'
			AND hpl = '" . $request->get('hpl') . "'
			");

            $stock = db::connection('ympimis_2')
                ->SELECT("
			SELECT
			material_number,
			material_description,
			'' AS qty,
			'' AS qty_audit,
			hpl
			FROM
			case_stocks
			WHERE
			hpl = '" . $request->get('hpl') . "'
			");

            $response = array(
                'status' => true,
                'detail' => $detail,
                'stock' => $stock,
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

    //ASSEMBLY SAX
    public function indexSaxKensa($location)
    {
        $loc_code = explode('-', $location);
        $process = $loc_code[0];
        $loc_spec = $loc_code[1];

        if ($location == 'kensa-process') {
            $title = 'Kensa Process Saxophone';
            $title_jp = '??';
        }
        if ($location == 'qa-kensa') {
            $title = 'QA Kensa Saxophone';
            $title_jp = '??';
        }
        if ($location == 'repair-process') {
            $title = 'Repair Process';
            $title_jp = '修正';
        }
        if ($location == 'qa-visual') {
            $title = 'QA Visual';
            $title_jp = '??';
        }
        if ($location == 'qa-fungsi') {
            $title = 'QA Fungsi';
            $title_jp = '??';
        }
        if ($location == 'qa-audit') {
            $title = 'QA Audit';
            $title_jp = '??';
        }

        $ng_lists = DB::select("SELECT DISTINCT(ng_name) FROM assembly_ng_lists where origin_group_code = '043' and location = '" . $loc_spec . "' and process = '" . $process . "' and deleted_at is null");

        $operator_qa = DB::select("SELECT
			*
		FROM
			assembly_operators
			LEFT JOIN employee_syncs ON employee_syncs.employee_id = assembly_operators.employee_id
		WHERE
			origin_group_code = '043'
			AND location LIKE '%qa%'");

        return view('processes.assembly.saxophone.kensa', array(
            'ng_lists' => $ng_lists,
            'location' => $location,
            'title' => $title,
            'title_jp' => $title_jp,
            'operator_qa' => $operator_qa,
            'operator_qa2' => $operator_qa,
        ))->with('page', 'Assembly SAX')->with('head', 'Assembly Process');
    }

    public function scanAssemblyOperatorKensaSax(Request $request)
    {
        try {
            if (str_contains($request->get('employee_id'), 'PI')) {
                $employee = db::table('assembly_operators')->join('employee_syncs', 'assembly_operators.employee_id', '=', 'employee_syncs.employee_id')->where('assembly_operators.employee_id', '=', $request->get('employee_id'))->first();
            } else {
                $employee = db::table('assembly_operators')->join('employee_syncs', 'assembly_operators.employee_id', '=', 'employee_syncs.employee_id')->where('tag', '=', strtoupper($this->dec2hex($request->get('employee_id'))))->first();
            }

            if ($employee == null) {
                $response = array(
                    'status' => false,
                    'message' => 'Employee Tidak Ditemukan',
                );
                return Response::json($response);
            } else {
                $assemblies_null = Assembly::where('origin_group_code', '043')->where('location', 'like', '%' . $request->get('location') . '%')->where('location_number', $request->get('line'))->first();
                if ($assemblies_null) {
                    $assemblies_null->online_time = date('Y-m-d H:i:s');
                    $assemblies_null->operator_id = $employee->employee_id;
                    $assemblies_null->save();
                }
                // if ($request->get('location') == 'kensa-process') {
                //     $assemblies = Assembly::where('origin_group_code','043')->where('location','like','%kensa-process%')->where('operator_id',$employee->employee_id)->first();
                //     if (!$assemblies) {
                //         $assemblies_null = Assembly::where('origin_group_code','043')->where('location','like','%kensa-process%')->where('operator_id',null)->first();
                //         if ($assemblies_null) {
                //             $assemblies_null->online_time = date('Y-m-d H:i:s');
                //             $assemblies_null->operator_id = $employee->employee_id;
                //             $assemblies_null->save();
                //         }
                //     }
                // }else if($request->get('location') == 'qa-kensa'){
                //     $assemblies = Assembly::where('origin_group_code','043')->where('location','like','%qa-kensa%')->where('operator_id',$employee->employee_id)->first();
                //     if (!$assemblies) {
                //         $assemblies_null = Assembly::where('origin_group_code','043')->where('location','like','%qa-kensa%')->where('operator_id',null)->first();
                //         if ($assemblies_null) {
                //             $assemblies_null->online_time = date('Y-m-d H:i:s');
                //             $assemblies_null->operator_id = $employee->employee_id;
                //             $assemblies_null->save();
                //         }
                //     }
                // }else if($request->get('location') == 'qa-fungsi'){
                //     $assemblies = Assembly::where('origin_group_code','043')->where('location','like','%qa-fungsi%')->where('operator_id',$employee->employee_id)->first();
                //     if (!$assemblies) {
                //         $assemblies_null = Assembly::where('origin_group_code','043')->where('location','like','%qa-fungsi%')->where('operator_id',null)->first();
                //         if ($assemblies_null) {
                //             $assemblies_null->online_time = date('Y-m-d H:i:s');
                //             $assemblies_null->operator_id = $employee->employee_id;
                //             $assemblies_null->save();
                //         }
                //     }
                // }else if($request->get('location') == 'qa-visual'){
                //     $assemblies = Assembly::where('origin_group_code','043')->where('location','like','%qa-visual%')->where('operator_id',$employee->employee_id)->first();
                //     if (!$assemblies) {
                //         $assemblies_null = Assembly::where('origin_group_code','043')->where('location','like','%qa-visual%')->where('operator_id',null)->first();
                //         if ($assemblies_null) {
                //             $assemblies_null->online_time = date('Y-m-d H:i:s');
                //             $assemblies_null->operator_id = $employee->employee_id;
                //             $assemblies_null->save();
                //         }
                //     }
                // }else if($request->get('location') == 'repair-process'){
                //     $assemblies = Assembly::where('origin_group_code','043')->where('location','like','%repair-process%')->where('operator_id',$employee->employee_id)->first();
                //     if (!$assemblies) {
                //         $assemblies_null = Assembly::where('origin_group_code','043')->where('location','like','%repair-process%')->where('operator_id',null)->first();
                //         if ($assemblies_null) {
                //             $assemblies_null->online_time = date('Y-m-d H:i:s');
                //             $assemblies_null->operator_id = $employee->employee_id;
                //             $assemblies_null->save();
                //         }
                //     }
                // }else if($request->get('location') == 'qa-audit'){
                //     $assemblies = Assembly::where('origin_group_code','043')->where('location','like','%qa-audit%')->where('operator_id',$employee->employee_id)->first();
                //     if (!$assemblies) {
                //         $assemblies_null = Assembly::where('origin_group_code','043')->where('location','like','%qa-audit%')->where('operator_id',null)->first();
                //         if ($assemblies_null) {
                //             $assemblies_null->online_time = date('Y-m-d H:i:s');
                //             $assemblies_null->operator_id = $employee->employee_id;
                //             $assemblies_null->save();
                //         }
                //     }
                // }
                $response = array(
                    'status' => true,
                    'employee' => $employee,
                    'message' => 'Employee Ditemukan',
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

    public function scanAssemblyKensaSax(Request $request)
    {
        try {
            $tag = $request->get('tag');

            $tags = db::table('assembly_tags')->where('assembly_tags.tag', '=', strtoupper($this->dec2hex($tag)))->where('origin_group_code', '043')->first();

            if ($tags != null && $tags->serial_number != null) {
                $details = AssemblyDetail::where('assembly_details.tag', strtoupper($this->dec2hex($tag)))->where('assembly_details.serial_number', $tags->serial_number)->where('assembly_details.origin_group_code', '043')->join('employee_syncs', 'employee_syncs.employee_id', 'assembly_details.operator_id')->join('assembly_operators', 'assembly_operators.employee_id', 'assembly_details.operator_id')->orderby('assembly_details.id', 'desc')->get();

                $inventory = AssemblyInventory::where('tag', strtoupper($this->dec2hex($tag)))->where('serial_number', $tags->serial_number)->where('origin_group_code', '043')->first();

                $history_ng = AssemblyNgLog::where('tag', strtoupper($this->dec2hex($tag)))->where('serial_number', $tags->serial_number)->where('origin_group_code', '043')->join('employee_syncs', 'employee_syncs.employee_id', 'assembly_ng_logs.employee_id')->get();
                $spec_qa = null;
                $spec_qa_now = null;
                $spec_process = null;
                if ($request->get('location') == 'qa-kensa' || $request->get('location') == 'qa-visual') {
                    $spec_qa = DB::connection('ympimis_2')->table('assembly_spec_product_points')->where('origin_group_code', '043')->where('model', $tags->model)->get();
                    $spec_qa_now = DB::connection('ympimis_2')->table('assembly_spec_products')->where('origin_group_code', '043')->where('model', $tags->model)->where('serial_number', $tags->serial_number)->get();
                }

                if ($request->get('location') == 'kensa-process') {
                    $spec_process = DB::connection('ympimis_2')->table('assembly_specs')->where('origin_group_code', '043')->where('model', $tags->model)->where('serial_number', $tags->serial_number)->get();
                }

                $ng_lists = DB::select("SELECT * FROM assembly_ng_lists where origin_group_code = '043' and location = '" . explode('-', $request->get('location'))[1] . "' and process = '" . explode('-', $request->get('location'))[0] . "' and deleted_at is null");

                $operator = DB::select("SELECT assembly_operators.employee_id,employee_syncs.`name`,employee_syncs.sub_group,assembly_operators.location  FROM assembly_operators join employee_syncs on employee_syncs.employee_id = assembly_operators.employee_id where origin_group_code = '043' and assembly_operators.deleted_at is null");

                $assemblies = null;
                $assemblies = Assembly::where('origin_group_code', '043')->where('location', 'like', '%' . $request->get('location') . '%')->where('location_number', $request->get('location_number'))->where('operator_id', $request->get('employee_id'))->first();

                if ($request->get('location') == 'kensa-process') {

                } else if ($request->get('location') == 'qa-kensa') {
                    $cek = AssemblyDetail::where('origin_group_code', '043')->where('location', 'like', '%kensa-process%')->where('serial_number', $tags->serial_number)->first();
                    if ($cek == null) {
                        $response = array(
                            'status' => false,
                            'message' => 'Kensa Proses Belum Dilakukan',
                        );
                        return Response::json($response);
                    }
                } else if ($request->get('location') == 'repair-process') {

                } else if ($request->get('location') == 'qa-fungsi') {
                    $cek = AssemblyDetail::where('origin_group_code', '043')->where('location', 'like', '%kensa-process%')->where('serial_number', $tags->serial_number)->first();
                    if ($cek == null) {
                        $response = array(
                            'status' => false,
                            'message' => 'Kensa Proses Belum Dilakukan',
                        );
                        return Response::json($response);
                    }
                } else if ($request->get('location') == 'qa-visual') {

                    $cek = AssemblyDetail::where('origin_group_code', '043')->where('location', 'like', '%kensa-process%')->where('serial_number', $tags->serial_number)->first();
                    if ($cek == null) {
                        $response = array(
                            'status' => false,
                            'message' => 'Kensa Proses Belum Dilakukan',
                        );
                        return Response::json($response);
                    }
                } else if ($request->get('location') == 'qa-audit') {
                    $cek = AssemblyDetail::where('origin_group_code', '043')->where('location', 'like', '%kensa-process%')->where('serial_number', $tags->serial_number)->first();
                    if ($cek == null) {
                        $response = array(
                            'status' => false,
                            'message' => 'Kensa Proses Belum Dilakukan',
                        );
                        return Response::json($response);
                    }
                }
                if ($assemblies) {
                    $assemblies->sedang_serial_number = $tags->serial_number;
                    $assemblies->sedang_model = $tags->model;
                    $assemblies->sedang_tag = $tags->tag;
                    $assemblies->sedang_time = date('Y-m-d H:i:s');
                    $assemblies->save();
                }

                $onko = DB::select("SELECT
				*
				FROM
				`assembly_onkos`
				WHERE
				origin_group_code = 043");

                $response = array(
                    'status' => true,
                    'tag' => $tags,
                    'details' => $details,
                    'inventory' => $inventory,
                    'spec' => $spec_qa,
                    'spec_now' => $spec_qa_now,
                    'spec_process' => $spec_process,
                    'ng_lists' => $ng_lists,
                    'operator' => $operator,
                    'started_at' => date('Y-m-d H:i:s'),
                    'onko' => $onko,
                    'history_ng' => $history_ng,
                    'message' => 'Tag Ditemukan',
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Tag Tidak Ditemukan',
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

    public function fetchAssemblyNgTempSax(Request $request)
    {
        try {
            $tag = $request->get('tag');
            $temp_ng = null;

            if ($tag != '') {
                $tags = db::table('assembly_tags')->where('assembly_tags.tag', '=', strtoupper($this->dec2hex($tag)))->where('origin_group_code', '043')->first();

                $temp_ng = AssemblyNgTemp::select('assembly_ng_temps.*', 'employee_syncs.*', 'assembly_ng_temps.id as id_ng')->where('tag', strtoupper($this->dec2hex($tag)))->where('serial_number', $tags->serial_number)->where('origin_group_code', '043')->where('location', $request->get('location'))->join('employee_syncs', 'employee_syncs.employee_id', 'assembly_ng_temps.employee_id')->get();
            }

            $qa_audit = null;

            if ($request->get('location') == 'qa-audit') {
                $qa_audit = DB::SELECT("SELECT DISTINCT
					( a.serial_number ),
					a.model,
					a.sedang_start_date,
					a.operator_visual,
					name_visual.`name` AS name_visual,
					a.operator_fungsi,
					COALESCE ( name_fungsi.`name`, '' ) AS name_fungsi
				FROM
					(
					SELECT
						serial_number,
						model,
						sedang_start_date,
						SPLIT_STRING ( operator_audited, '_', 1 ) AS operator_visual,
						SPLIT_STRING ( operator_audited, '_', 2 ) AS operator_fungsi
					FROM
						assembly_details
					WHERE
						location = 'qa-audit'
						AND origin_group_code = 043
						AND DATE( sedang_start_date ) = DATE(
						NOW()) UNION ALL
					SELECT
						serial_number,
						model,
						sedang_start_date,
						SPLIT_STRING ( operator_audited, '_', 1 ) AS operator_visual,
						SPLIT_STRING ( operator_audited, '_', 2 ) AS operator_fungsi
					FROM
						assembly_logs
					WHERE
						location = 'qa-audit'
						AND origin_group_code = 043
						AND DATE( sedang_start_date ) = DATE(
						NOW())) a
					LEFT JOIN employee_syncs AS name_visual ON name_visual.employee_id = a.operator_visual
					LEFT JOIN employee_syncs AS name_fungsi ON name_fungsi.employee_id = a.operator_fungsi");
            }

            $response = array(
                'status' => true,
                'temp_ng' => $temp_ng,
                'qa_audit' => $qa_audit,
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

    public function inputAssemblyNgTempSax(Request $request)
    {
        try {
            $employee_id = $request->get('employee_id');
            $tag = $request->get('tag');
            $serial_number = $request->get('serial_number');
            $model = $request->get('model');
            $location = $request->get('location');
            $ng_name = $request->get('ng_name');
            $ongko = $request->get('ongko');
            $value_atas = $request->get('value_atas');
            $value_bawah = $request->get('value_bawah');
            $value_lokasi = $request->get('value_lokasi');
            $operator_id = $request->get('operator_id');
            $started_at = $request->get('started_at');
            $tag = $request->get('tag');

            if (!str_contains($location, 'qa')) {
                if (str_contains($ng_name, 'Kizu') || str_contains($ng_name, 'kizu')) {
                    if ($operator_id == '' || $operator_id == null) {
                        $response = array(
                            'status' => false,
                            'message' => 'Pilih Operator Penghasil NG',
                        );
                        return Response::json($response);
                    }
                }
            }

            // if ($ng_name != 'Nari') {
            //     $value_atas = 1;
            //     $value_bawah = null;
            // }

            $assembly_ng_temp = new AssemblyNgTemp([
                'employee_id' => $employee_id,
                'tag' => strtoupper($this->dec2hex($request->get('tag'))),
                'serial_number' => $serial_number,
                'model' => $model,
                'location' => $location,
                'ng_name' => $ng_name,
                'ongko' => $ongko,
                'value_atas' => $value_atas,
                'value_bawah' => $value_bawah,
                'value_lokasi' => $value_lokasi,
                'operator_id' => $operator_id,
                'started_at' => $started_at,
                'origin_group_code' => '043',
                'created_by' => Auth::id(),
            ]);

            $assembly_ng_temp->save();

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

    public function deleteAssemblyNgTempSax(Request $request)
    {
        try {
            $id = $request->get('id');
            $delete = AssemblyNgTemp::where('id', $id)->forceDelete();

            $response = array(
                'status' => true,
                'message' => 'Success Delete NG',
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

    public function inputAssemblyKensaSax(Request $request)
    {
        try {
            $tag = $request->get('tag');
            $employee_id = $request->get('employee_id');
            $serial_number = $request->get('serial_number');
            $model = $request->get('model');
            $location = $request->get('location');
            $location_number = $request->get('location_number');
            $started_at = $request->get('started_at');
            $origin_group_code = $request->get('origin_group_code');
            $remark_process = $request->get('remark_process');
            $remark_qa = $request->get('remark_qa');
            $note = $request->get('note');

            $operator_qa = null;
            if ($request->get('operator_qa') != '') {
                $operator_qa = $request->get('operator_qa');
            }

            $operator_qa2 = null;
            if ($request->get('operator_qa2') != '') {
                $operator_qa2 = $request->get('operator_qa2');
            }

            $detail = new AssemblyDetail([
                'tag' => strtoupper($this->dec2hex($tag)),
                'serial_number' => $serial_number,
                'model' => $model,
                'location' => $location,
                'location_number' => $location_number,
                'operator_id' => $employee_id,
                'operator_audited' => $operator_qa . '_' . $operator_qa2,
                'sedang_start_date' => $started_at,
                'sedang_finish_date' => date('Y-m-d H:i:s'),
                'origin_group_code' => $origin_group_code,
                'note' => $note,
                'created_by' => $employee_id,
                'is_send_log' => 0,
            ]);
            $detail->save();

            $assemblies = null;
            $assemblies = Assembly::where('origin_group_code', '043')->where('location', 'like', '%' . $location . '%')->where('location_number', $location_number)->where('operator_id', $employee_id)->first();

            if ($assemblies) {
                $assemblies->prev_start_time = $assemblies->sedang_time;
                $assemblies->prev_end_time = date('Y-m-d H:i:s');
                $assemblies->sedang_serial_number = null;
                $assemblies->sedang_model = null;
                $assemblies->sedang_tag = null;
                $assemblies->sedang_time = null;
                $assemblies->save();
            }

            $inventory = AssemblyInventory::where('tag', strtoupper($this->dec2hex($tag)))->where('serial_number', $serial_number)->where('origin_group_code', '043')->first();
            $inventory->location = $location;
            $inventory->location_number = $location_number;
            if ($location != 'repair-process') {
                $inventory->remark = $remark_process . '_' . $remark_qa;
                $insert_status = DB::connection('ympimis_2')->table('assembly_status_materials')->insert([
                    'tag' => $inventory->tag,
                    'serial_number' => $inventory->serial_number,
                    'model' => $inventory->model,
                    'location' => $location,
                    'location_number' => $inventory->location_number,
                    'origin_group_code' => $inventory->origin_group_code,
                    'status_material' => $remark_process . '_' . $remark_qa,
                    'created_by' => $employee_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => $inventory->updated_at,
                ]);
            }
            $inventory->save();

            $ng_temp = AssemblyNgTemp::where('tag', strtoupper($this->dec2hex($tag)))->where('serial_number', $serial_number)->where('origin_group_code', '043')->get();
            if (count($ng_temp) > 0) {
                for ($i = 0; $i < count($ng_temp); $i++) {
                    $assembly_ng_log = new AssemblyNgLog([
                        'employee_id' => $employee_id,
                        'tag' => strtoupper($this->dec2hex($tag)),
                        'serial_number' => $serial_number,
                        'model' => $model,
                        'location' => $location,
                        'location_number' => $location_number,
                        'ongko' => $ng_temp[$i]->ongko,
                        'ng_name' => $ng_temp[$i]->ng_name,
                        'value_atas' => $ng_temp[$i]->value_atas,
                        'value_bawah' => $ng_temp[$i]->value_bawah,
                        'value_lokasi' => $ng_temp[$i]->value_lokasi,
                        'operator_id' => $ng_temp[$i]->operator_id,
                        'sedang_start_date' => $ng_temp[$i]->started_at,
                        'sedang_finish_date' => date('Y-m-d H:i:s'),
                        'origin_group_code' => $origin_group_code,
                        'created_by' => Auth::id(),
                    ]);
                    $assembly_ng_log->save();

                    $ng = AssemblyNgTemp::where('id', $ng_temp[$i]->id)->delete();
                }
            }

            if ($location == 'qa-visual' || $location == 'qa-kensa') {
                $spec_location = $request->get('spec_location');
                $spec_point = $request->get('spec_point');
                $spec_detail = $request->get('spec_detail');
                $spec_how_to_check = $request->get('spec_how_to_check');
                $spec_results = $request->get('spec_results');

                for ($i = 0; $i < count($spec_location); $i++) {
                    DB::connection('ympimis_2')->table('assembly_spec_products')->updateOrInsert(
                        [
                            'serial_number' => $serial_number,
                            'model' => $model,
                            'origin_group_code' => $origin_group_code,
                            'location' => $spec_location[$i],
                            'point' => $spec_point[$i],
                            'detail' => $spec_detail[$i],
                            'how_to_check' => $spec_how_to_check[$i],
                        ],
                        [
                            'serial_number' => $serial_number,
                            'model' => $model,
                            'origin_group_code' => $origin_group_code,
                            'location' => $spec_location[$i],
                            'point' => $spec_point[$i],
                            'detail' => $spec_detail[$i],
                            'how_to_check' => $spec_how_to_check[$i],
                            'results' => $spec_results[$i],
                            'employee_id' => $employee_id,
                            'created_by' => Auth::id(),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                }
            }

            if ($location == 'kensa-process') {
                $process_made_in = $request->get('process_made_in');
                $process_body = $request->get('process_body');
                $process_bell = $request->get('process_bell');
                $process_side_cover = $request->get('process_side_cover');
                $process_f_4 = $request->get('process_f_4');
                $process_j_3 = $request->get('process_j_3');

                DB::connection('ympimis_2')->table('assembly_specs')->updateOrInsert(
                    [
                        'serial_number' => $serial_number,
                        'model' => $model,
                        'category' => 'Made In',
                        'origin_group_code' => $origin_group_code,
                    ],
                    [
                        'serial_number' => $serial_number,
                        'model' => $model,
                        'category' => 'Made In',
                        'origin_group_code' => $origin_group_code,
                        'results' => $process_made_in,
                        'employee_id' => $employee_id,
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]
                );

                DB::connection('ympimis_2')->table('assembly_specs')->updateOrInsert(
                    [
                        'serial_number' => $serial_number,
                        'model' => $model,
                        'category' => 'Body',
                        'origin_group_code' => $origin_group_code,
                    ],
                    [
                        'serial_number' => $serial_number,
                        'model' => $model,
                        'category' => 'Body',
                        'origin_group_code' => $origin_group_code,
                        'results' => $process_body,
                        'employee_id' => $employee_id,
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]
                );

                DB::connection('ympimis_2')->table('assembly_specs')->updateOrInsert(
                    [
                        'serial_number' => $serial_number,
                        'model' => $model,
                        'category' => 'Bell',
                        'origin_group_code' => $origin_group_code,
                    ],
                    [
                        'serial_number' => $serial_number,
                        'model' => $model,
                        'category' => 'Bell',
                        'origin_group_code' => $origin_group_code,
                        'results' => $process_bell,
                        'employee_id' => $employee_id,
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]
                );

                DB::connection('ympimis_2')->table('assembly_specs')->updateOrInsert(
                    [
                        'serial_number' => $serial_number,
                        'model' => $model,
                        'category' => 'Side Cover',
                        'origin_group_code' => $origin_group_code,
                    ],
                    [
                        'serial_number' => $serial_number,
                        'model' => $model,
                        'category' => 'Side Cover',
                        'origin_group_code' => $origin_group_code,
                        'results' => $process_side_cover,
                        'employee_id' => $employee_id,
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]
                );

                DB::connection('ympimis_2')->table('assembly_specs')->updateOrInsert(
                    [
                        'serial_number' => $serial_number,
                        'model' => $model,
                        'category' => 'F-4',
                        'origin_group_code' => $origin_group_code,
                    ],
                    [
                        'serial_number' => $serial_number,
                        'model' => $model,
                        'category' => 'F-4',
                        'origin_group_code' => $origin_group_code,
                        'results' => $process_f_4,
                        'employee_id' => $employee_id,
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]
                );

                DB::connection('ympimis_2')->table('assembly_specs')->updateOrInsert(
                    [
                        'serial_number' => $serial_number,
                        'model' => $model,
                        'category' => 'J-3',
                        'origin_group_code' => $origin_group_code,
                    ],
                    [
                        'serial_number' => $serial_number,
                        'model' => $model,
                        'category' => 'J-3',
                        'origin_group_code' => $origin_group_code,
                        'results' => $process_j_3,
                        'employee_id' => $employee_id,
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]
                );
            }

            $response = array(
                'status' => true,
                'message' => 'Success',
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

    public function inputAssemblyRepairSax(Request $request)
    {
        try {
            $repaired_by = $request->get('repaired_by');
            $id = $request->get('id');

            $repair = AssemblyNgLog::where('id', $id)->first();
            $repair->repair_status = 'Repaired';
            $repair->repaired_by = $repaired_by;
            $repair->repaired_at = date('Y-m-d H:i:s');
            $repair->save();

            $response = array(
                'status' => true,
                'message' => 'Repaired',
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

    public function inputAssemblyGantiKunciSax(Request $request)
    {
        try {
            $repaired_by = $request->get('repaired_by');
            $id = $request->get('id');

            $repair = AssemblyNgLog::where('id', $id)->first();
            $repair->decision = 'Ganti Kunci';
            $repair->repair_status = 'Repaired';
            $repair->repaired_by = $repaired_by;
            $repair->repaired_at = date('Y-m-d H:i:s');
            $repair->save();

            $response = array(
                'status' => true,
                'message' => 'Repaired',
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

// Assembly CL
    public function indexClKensa($location)
    {
        $loc_code = explode('-', $location);
        $process = $loc_code[0];
        $loc_spec = $loc_code[1];

        if ($location == 'kensa-process') {
            $title = 'Kensa Process Clarinet';
            $title_jp = '??';
        }
        if ($location == 'qa-kensa') {
            $title = 'QA Kensa Clarinet';
            $title_jp = '??';
        }
        if ($location == 'repair-process') {
            $title = 'Repair Process';
            $title_jp = '修正';
        }
        if ($location == 'qa-audit') {
            $title = 'Kensa QA Audit';
            $title_jp = '??';
        }

        $ng_lists = DB::select("SELECT DISTINCT(ng_name) FROM assembly_ng_lists where origin_group_code = '042' and location = '" . $loc_spec . "' and process = '" . $process . "' and deleted_at is null");

        $operator_qa = DB::select("SELECT
			DISTINCT(assembly_operators.employee_id),
			name
		FROM
			assembly_operators
			LEFT JOIN employee_syncs ON employee_syncs.employee_id = assembly_operators.employee_id
		WHERE
			origin_group_code = '042'
			AND location LIKE '%qa%'");

        return view('processes.assembly.clarinet.kensa', array(
            'ng_lists' => $ng_lists,
            'location' => $location,
            'title' => $title,
            'title_jp' => $title_jp,
            'operator_qa' => $operator_qa,
        ))->with('page', 'Assembly CL')->with('head', 'Assembly Process');
    }

    public function scanAssemblyOperatorKensaCl(Request $request)
    {
        try {
            if (str_contains($request->get('employee_id'), 'PI')) {
                $employee = db::table('assembly_operators')->join('employee_syncs', 'assembly_operators.employee_id', '=', 'employee_syncs.employee_id')->where('assembly_operators.employee_id', '=', $request->get('employee_id'))->first();
            } else {
                $employee = db::table('assembly_operators')->join('employee_syncs', 'assembly_operators.employee_id', '=', 'employee_syncs.employee_id')->where('tag', '=', strtoupper($this->dec2hex($request->get('employee_id'))))->first();
            }

            if ($employee == null) {
                $response = array(
                    'status' => false,
                    'message' => 'Employee Tidak Ditemukan',
                );
                return Response::json($response);
            } else {
                $assemblies = null;
                if ($request->get('location') == 'kensa-process') {
                    $assemblies = Assembly::where('origin_group_code', '042')->where('location', 'like', '%kensa-process%')->where('operator_id', $employee->employee_id)->first();
                    if (!$assemblies) {
                        $assemblies_null = Assembly::where('origin_group_code', '042')->where('location', 'like', '%kensa-process%')->where('operator_id', null)->first();
                        if ($assemblies_null) {
                            $assemblies_null->online_time = date('Y-m-d H:i:s');
                            $assemblies_null->operator_id = $employee->employee_id;
                            $assemblies_null->save();
                        }
                    }
                } else if ($request->get('location') == 'qa-kensa') {
                    $assemblies = Assembly::where('origin_group_code', '042')->where('location', 'like', '%qa-kensa%')->where('operator_id', $employee->employee_id)->first();
                    if (!$assemblies) {
                        $assemblies_null = Assembly::where('origin_group_code', '042')->where('location', 'like', '%qa-kensa%')->where('operator_id', null)->first();
                        if ($assemblies_null) {
                            $assemblies_null->online_time = date('Y-m-d H:i:s');
                            $assemblies_null->operator_id = $employee->employee_id;
                            $assemblies_null->save();
                        }
                    }
                } else if ($request->get('location') == 'repair-process') {
                    $assemblies = Assembly::where('origin_group_code', '042')->where('location', 'like', '%kensa-process%')->where('operator_id', $employee->employee_id)->first();
                    if (!$assemblies) {
                        $assemblies_null = Assembly::where('origin_group_code', '042')->where('location', 'like', '%kensa-process%')->where('operator_id', null)->first();
                        if ($assemblies_null) {
                            $assemblies_null->online_time = date('Y-m-d H:i:s');
                            $assemblies_null->operator_id = $employee->employee_id;
                            $assemblies_null->save();
                        }
                    }
                } else if ($request->get('location') == 'qa-audit') {
                    $assemblies = Assembly::where('origin_group_code', '042')->where('location', 'like', '%qa-audit%')->where('operator_id', $employee->employee_id)->first();
                    if (!$assemblies) {
                        $assemblies_null = Assembly::where('origin_group_code', '042')->where('location', 'like', '%qa-audit%')->where('operator_id', null)->first();
                        if ($assemblies_null) {
                            $assemblies_null->online_time = date('Y-m-d H:i:s');
                            $assemblies_null->operator_id = $employee->employee_id;
                            $assemblies_null->save();
                        }
                    }
                }
                if ($assemblies) {
                    $assemblies->online_time = date('Y-m-d H:i:s');
                    $assemblies->operator_id = $employee->employee_id;
                    $assemblies->save();
                }
                $response = array(
                    'status' => true,
                    'employee' => $employee,
                    'message' => 'Employee Ditemukan',
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

    public function scanAssemblyKensacl(Request $request)
    {
        try {
            $tag = $request->get('tag');

            $tags = db::table('assembly_tags')->where('assembly_tags.tag', '=', strtoupper($this->dec2hex($tag)))->where('origin_group_code', '042')->where('remark', 'not like', '%_U%')->first();

            if ($tags != null && $tags->serial_number != null) {

                $details = AssemblyDetail::where('assembly_details.tag', strtoupper($this->dec2hex($tag)))->where('serial_number', $tags->serial_number)->where('assembly_details.origin_group_code', '042')->join('employee_syncs', 'employee_syncs.employee_id', 'assembly_details.operator_id')->join('assembly_operators', 'assembly_operators.employee_id', 'assembly_details.operator_id')->orderby('assembly_details.id', 'desc')->get();

                $inventory = AssemblyInventory::where('tag', strtoupper($this->dec2hex($tag)))->where('serial_number', $tags->serial_number)->where('origin_group_code', '042')->first();

                $history_ng = AssemblyNgLog::where('tag', strtoupper($this->dec2hex($tag)))->where('serial_number', $tags->serial_number)->where('origin_group_code', '042')->join('employee_syncs', 'employee_syncs.employee_id', 'assembly_ng_logs.employee_id')->get();
                $ng_lists = DB::select("SELECT * FROM assembly_ng_lists where origin_group_code = '042' and location = '" . explode('-', $request->get('location'))[1] . "' and process = '" . explode('-', $request->get('location'))[0] . "' and deleted_at is null");

                $operator = DB::select("SELECT assembly_operators.employee_id,employee_syncs.`name`,employee_syncs.sub_group,assembly_operators.location  FROM assembly_operators join employee_syncs on employee_syncs.employee_id = assembly_operators.employee_id where origin_group_code = '042' and assembly_operators.deleted_at is null");

                $onko = DB::select("SELECT
				*
				FROM
				`assembly_onkos`
				WHERE
				origin_group_code = 042");
                $assemblies = null;
                // $cek = null;

                if ($request->get('location') == 'kensa-process') {
                    $assemblies = Assembly::where('origin_group_code', '042')->where('location', 'like', '%kensa-process%')->where('operator_id', $request->get('employee_id'))->first();
                } else if ($request->get('location') == 'qa-kensa') {
                    $assemblies = Assembly::where('origin_group_code', '042')->where('location', 'like', '%qa-kensa%')->where('operator_id', $request->get('employee_id'))->first();
                    $cek = AssemblyDetail::where('origin_group_code', '042')->where('location', 'like', '%kensa-process%')->where('serial_number', $tags->serial_number)->first();
                    if ($cek == null) {
                        $response = array(
                            'status' => false,
                            'message' => 'Kensa Proses Belum Dilakukan',
                        );
                        return Response::json($response);
                    }
                } else if ($request->get('location') == 'repair-process') {
                    $assemblies = Assembly::where('origin_group_code', '042')->where('location', 'like', '%repair-process%')->where('operator_id', $request->get('employee_id'))->first();
                } else if ($request->get('location') == 'qa-audit') {
                    $assemblies = Assembly::where('origin_group_code', '042')->where('location', 'like', '%qa-audit%')->where('operator_id', $request->get('employee_id'))->first();
                    $cek = AssemblyDetail::where('origin_group_code', '042')->where('location', 'like', '%kensa-process%')->where('serial_number', $tags->serial_number)->first();
                    if ($cek == null) {
                        $response = array(
                            'status' => false,
                            'message' => 'Kensa Proses Belum Dilakukan',
                        );
                        return Response::json($response);
                    }
                }
                if ($assemblies) {
                    $assemblies->sedang_serial_number = $tags->serial_number;
                    $assemblies->sedang_model = $tags->model;
                    $assemblies->sedang_tag = $tags->tag;
                    $assemblies->sedang_time = date('Y-m-d H:i:s');
                    $assemblies->save();
                }

                $emp = EmployeeSync::where('end_date', null)->get();

                $response = array(
                    'status' => true,
                    'tag' => $tags,
                    'details' => $details,
                    'emp' => $emp,
                    'inventory' => $inventory,
                    'ng_lists' => $ng_lists,
                    'operator' => $operator,
                    'onko' => $onko,
                    'started_at' => date('Y-m-d H:i:s'),
                    'history_ng' => $history_ng,
                    'message' => 'Tag Ditemukan',
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Tag Tidak Ditemukan',
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

    public function scanAssemblyKensaclUpper(Request $request)
    {
        try {
            $tag = $request->get('tag');

            $tags = db::table('assembly_tags')->where('assembly_tags.tag', '=', strtoupper($this->dec2hex($tag)))->where('origin_group_code', '042')->first();

            if ($tags != null && $tags->serial_number != null) {
                $details = AssemblyDetail::where('assembly_details.tag', strtoupper($this->dec2hex($tag)))->where('serial_number', $tags->serial_number)->where('assembly_details.origin_group_code', '042')->join('employee_syncs', 'employee_syncs.employee_id', 'assembly_details.operator_id')->join('assembly_operators', 'assembly_operators.employee_id', 'assembly_details.operator_id')->orderby('assembly_details.id', 'desc')->get();

                $inventory = AssemblyInventory::where('tag', strtoupper($this->dec2hex($tag)))->where('serial_number', $tags->serial_number)->where('origin_group_code', '042')->first();

                $history_ng = AssemblyNgLog::where('tag', strtoupper($this->dec2hex($tag)))->where('serial_number', $tags->serial_number)->where('origin_group_code', '042')->join('employee_syncs', 'employee_syncs.employee_id', 'assembly_ng_logs.employee_id')->get();
                $ng_lists = DB::select("SELECT * FROM assembly_ng_lists where origin_group_code = '042' and location = '" . explode('-', $request->get('location'))[1] . "' and process = '" . explode('-', $request->get('location'))[0] . "' and deleted_at is null");

                $onko = DB::select("SELECT
				*
				FROM
				`assembly_onkos`
				WHERE
				origin_group_code = 042");

                $response = array(
                    'status' => true,
                    'tag' => $tags,
                    'details' => $details,
                    'inventory' => $inventory,
                    'ng_lists' => $ng_lists,
                    'onko' => $onko,
                    'history_ng' => $history_ng,
                    'message' => 'Tag Ditemukan',
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => true,
                    'message' => 'Tag Tidak Ditemukan',
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

    public function fetchAssemblyNgTempCl(Request $request)
    {
        try {
            $tag = $request->get('tag');

            $temp_ng = null;

            if ($tag != '') {
                $tags = db::table('assembly_tags')->where('assembly_tags.tag', '=', strtoupper($this->dec2hex($tag)))->where('origin_group_code', '042')->first();

                $temp_ng = AssemblyNgTemp::select('assembly_ng_temps.*', 'employee_syncs.*', 'assembly_ng_temps.id as id_ng')->where('tag', strtoupper($this->dec2hex($tag)))->where('serial_number', $tags->serial_number)->where('origin_group_code', '042')->where('location', $request->get('location'))->where('origin_group_code', '042')->join('employee_syncs', 'employee_syncs.employee_id', 'assembly_ng_temps.employee_id')->get();
            }

            $qa_audit = null;

            if ($request->get('location') == 'qa-audit') {
                $qa_audit = DB::SELECT("SELECT DISTINCT
				( a.serial_number ),
				a.model,
				a.sedang_start_date
				FROM
				(
				SELECT
				serial_number,
				model,
				sedang_start_date
				FROM
				assembly_details
				WHERE
				location = 'qa-audit'
				AND origin_group_code = 042
				AND DATE( sedang_start_date ) = DATE(
				NOW()) UNION ALL
				SELECT
				serial_number,
				model,
				sedang_start_date
				FROM
				assembly_logs
				WHERE
				location = 'qa-audit'
				AND origin_group_code = 042
				AND DATE( sedang_start_date ) = DATE(
				NOW())) a");
            }

            $response = array(
                'status' => true,
                'temp_ng' => $temp_ng,
                'qa_audit' => $qa_audit,
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

    public function inputAssemblyNgTempCl(Request $request)
    {
        try {
            $employee_id = $request->get('employee_id');
            $tag = $request->get('tag');
            $serial_number = $request->get('serial_number');
            $model = $request->get('model');
            $location = $request->get('location');
            $ng_name = $request->get('ng_name');
            $ongko = $request->get('ongko');
            $value_atas = $request->get('value_atas');
            $value_bawah = $request->get('value_bawah');
            $value_lokasi = $request->get('value_lokasi');
            $operator_id = $request->get('operator_id');
            $started_at = $request->get('started_at');
            $tag = $request->get('tag');

            if (!str_contains($location, 'qa')) {
                if (str_contains($ng_name, 'Kizu') || str_contains($ng_name, 'kizu')) {
                    if ($operator_id == '' || $operator_id == null) {
                        $response = array(
                            'status' => false,
                            'message' => 'Pilih Operator Penghasil NG',
                        );
                        return Response::json($response);
                    }
                }
            }

            // if ($ng_name != 'Tanpoawase') {
            //     $value_atas = 1;
            //     $value_bawah = null;
            // }

            $assembly_ng_temp = new AssemblyNgTemp([
                'employee_id' => $employee_id,
                'tag' => strtoupper($this->dec2hex($request->get('tag'))),
                'serial_number' => $serial_number,
                'model' => $model,
                'location' => $location,
                'ng_name' => $ng_name,
                'ongko' => $ongko,
                'value_atas' => $value_atas,
                'value_bawah' => $value_bawah,
                'value_lokasi' => $value_lokasi,
                'operator_id' => $operator_id,
                'started_at' => $started_at,
                'origin_group_code' => '042',
                'created_by' => Auth::id(),
            ]);

            $assembly_ng_temp->save();

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

    public function inputAssemblyKensaCl(Request $request)
    {
        try {
            $tag = $request->get('tag');
            $employee_id = $request->get('employee_id');
            $serial_number = $request->get('serial_number');
            $model = $request->get('model');
            $location = $request->get('location');
            $started_at = $request->get('started_at');
            $origin_group_code = $request->get('origin_group_code');
            $remark_process = $request->get('remark_process');
            $remark_qa = $request->get('remark_qa');
            $tag_upper = $request->get('tag_upper');
            $note = $request->get('note');

            $detail = new AssemblyDetail([
                'tag' => strtoupper($this->dec2hex($tag)),
                'serial_number' => $serial_number,
                'model' => $model,
                'location' => $location,
                'operator_id' => $employee_id,
                'operator_audited' => $request->get('operator_qa'),
                'sedang_start_date' => $started_at,
                'sedang_finish_date' => date('Y-m-d H:i:s'),
                'origin_group_code' => $origin_group_code,
                'note' => $note,
                'created_by' => $employee_id,
                'is_send_log' => 0,
            ]);
            $detail->save();

            $inventory = AssemblyInventory::where('tag', strtoupper($this->dec2hex($tag)))->where('serial_number', $serial_number)->where('origin_group_code', '042')->first();
            $inventory->location = $location;
            if ($location != 'repair-process') {
                $inventory->remark = $remark_process . '_' . $remark_qa;
                $insert_status = DB::connection('ympimis_2')->table('assembly_status_materials')->insert([
                    'tag' => $inventory->tag,
                    'serial_number' => $inventory->serial_number,
                    'model' => $inventory->model,
                    'location' => $location,
                    'location_number' => $inventory->location_number,
                    'origin_group_code' => $inventory->origin_group_code,
                    'status_material' => $remark_process . '_' . $remark_qa,
                    'created_by' => $employee_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => $inventory->updated_at,
                ]);
            }
            $inventory->save();

            $ng_temp = AssemblyNgTemp::where('tag', strtoupper($this->dec2hex($tag)))->where('serial_number', $serial_number)->where('origin_group_code', '042')->get();
            if (count($ng_temp) > 0) {
                for ($i = 0; $i < count($ng_temp); $i++) {
                    $assembly_ng_log = new AssemblyNgLog([
                        'employee_id' => $employee_id,
                        'tag' => strtoupper($this->dec2hex($tag)),
                        'serial_number' => $serial_number,
                        'model' => $model,
                        'location' => $location,
                        'ongko' => $ng_temp[$i]->ongko,
                        'ng_name' => $ng_temp[$i]->ng_name,
                        'value_atas' => $ng_temp[$i]->value_atas,
                        'value_bawah' => $ng_temp[$i]->value_bawah,
                        'value_lokasi' => $ng_temp[$i]->value_lokasi,
                        'operator_id' => $ng_temp[$i]->operator_id,
                        'sedang_start_date' => $ng_temp[$i]->started_at,
                        'decision' => $ng_temp[$i]->decision,
                        'repair_status' => $ng_temp[$i]->repair_status,
                        'repaired_by' => $ng_temp[$i]->repaired_by,
                        'repaired_at' => $ng_temp[$i]->repaired_at,
                        'verified_by' => $ng_temp[$i]->verified_by,
                        'verified_at' => $ng_temp[$i]->verified_at,
                        'sedang_finish_date' => date('Y-m-d H:i:s'),
                        'origin_group_code' => $origin_group_code,
                        'created_by' => Auth::id(),
                    ]);
                    $assembly_ng_log->save();

                    $ng = AssemblyNgTemp::where('id', $ng_temp[$i]->id)->delete();
                }
            }

            $inventory_upper = AssemblyInventory::where('tag', strtoupper($this->dec2hex($tag_upper)))->where('origin_group_code', '042')->first();

            if ($inventory_upper) {
                $detail = new AssemblyDetail([
                    'tag' => strtoupper($this->dec2hex($tag_upper)),
                    'serial_number' => $inventory_upper->serial_number,
                    'model' => $inventory_upper->model,
                    'location' => $location,
                    'operator_id' => $employee_id,
                    'sedang_start_date' => $started_at,
                    'sedang_finish_date' => date('Y-m-d H:i:s'),
                    'origin_group_code' => $origin_group_code,
                    'created_by' => $employee_id,
                    'is_send_log' => 0,
                ]);
                $detail->save();
                $inventory_upper->location = $location;
                $inventory_upper->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Success',
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

    public function deleteAssemblyNgTempCl(Request $request)
    {
        try {
            $id = $request->get('id');
            $delete = AssemblyNgTemp::where('id', $id)->forceDelete();

            $response = array(
                'status' => true,
                'message' => 'Success Delete NG',
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

    public function clearKartu(Request $request)
    {
        try {
            $tag = $request->get('tag');

            $asstag = AssemblyTag::where('tag', strtoupper($this->dec2hex($tag)))->where('serial_number', '!=', null)->first();

            if ($asstag) {
                $assembly_details = AssemblyDetail::where('tag', strtoupper($this->dec2hex($tag)))
                    ->get();

                if (count($assembly_details) > 0) {
                    foreach ($assembly_details as $row) {
                        $assembly_log = new AssemblyLog([
                            'tag' => $row->tag,
                            'serial_number' => $row->serial_number,
                            'model' => $row->model,
                            'location' => $row->location,
                            'operator_id' => $row->operator_id,
                            'sedang_start_date' => $row->sedang_start_date,
                            'sedang_finish_date' => $row->sedang_finish_date,
                            'origin_group_code' => $row->origin_group_code,
                            'status_material' => $row->status_material,
                            'note' => 'Lower : ' . $request->get('serial_number'),
                            'created_by' => $row->created_by,
                        ]);
                        $assembly_log->save();

                        $detail = AssemblyDetail::where('id', $row->id)
                            ->where('origin_group_code', '042')
                            ->delete();
                    }
                }

                $inventory = AssemblyInventory::where('tag', strtoupper($this->dec2hex($tag)))->first();
                if ($inventory) {
                    $inventory->delete();
                }

                $asstag->serial_number = null;
                $asstag->model = null;
                $asstag->save();
            }
            $response = array(
                'status' => true,
                'message' => 'Success',
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

    public function inputAssemblyRepairCl(Request $request)
    {
        try {
            $repaired_by = $request->get('repaired_by');
            $id = $request->get('id');

            $repair = AssemblyNgLog::where('id', $id)->first();
            if ($repair) {
                $repair->repair_status = 'Repaired';
                $repair->repaired_by = $repaired_by;
                $repair->repaired_at = date('Y-m-d H:i:s');
                $repair->save();
            }

            $repair = AssemblyNgTemp::where('id', $id)->first();
            if ($repair) {
                $repair->repair_status = 'Repaired';
                $repair->repaired_by = $repaired_by;
                $repair->repaired_at = date('Y-m-d H:i:s');
                $repair->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Repaired',
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

    public function inputAssemblyGantiKunciCl(Request $request)
    {
        try {
            $repaired_by = $request->get('repaired_by');
            $id = $request->get('id');

            $repair = AssemblyNgLog::where('id', $id)->first();
            if ($repair) {
                $repair->decision = 'Ganti Kunci';
                $repair->repair_status = 'Repaired';
                $repair->repaired_by = $repaired_by;
                $repair->repaired_at = date('Y-m-d H:i:s');
                $repair->save();
            }

            $repair = AssemblyNgTemp::where('id', $id)->first();
            if ($repair) {
                $repair->decision = 'Ganti Kunci';
                $repair->repair_status = 'Repaired';
                $repair->repaired_by = $repaired_by;
                $repair->repaired_at = date('Y-m-d H:i:s');
                $repair->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Repaired',
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

    public function indexAssyEfficiency($product)
    {
        if ($product == 'saxophone') {
            $title = 'Operator Efficiency';
            $title_jp = '作業者能率';
            return view('processes.assembly.saxophone.op_efficiency', array(
                'title' => $title,
                'title_jp' => $title_jp,
            ))->with('page', 'Operator Efficiency')->with('head', 'Assembly Process');
        } else if ($product == 'clarinet') {
            $title = 'Operator Efficiency';
            $title_jp = '作業者能率';
            return view('processes.assembly.clarinet.op_efficiency', array(
                'title' => $title,
                'title_jp' => $title_jp,
            ))->with('page', 'Operator Efficiency')->with('head', 'Assembly Process');
        }
    }

    public function fetchAssyEfficiency(Request $request)
    {
        if ($request->get('origin_group') == 'saxophone') {
            $origin = '043';
        } else if ($request->get('origin_group') == 'clarinet') {
            $origin = '042';
        }

        if ($request->get('tanggal') == '') {
            $tanggal = date('Y-m-d');
            $dt2 = date('d m Y');
            $dt = date('d M Y');
        } else {
            $tanggal = $request->get('tanggal');
            $dt2 = date('d m Y', strtotime($request->get('tanggal')));
            $dt = date('d M Y', strtotime($request->get('tanggal')));
        }

        if ($request->get('origin_group') == 'saxophone') {
            $datas = db::select("SELECT
				a.operator_id,
				a.`name`,
				a.location,
				count(
				DISTINCT ( a.serial_number )) AS result,
				sum( a.actual_time ) AS actual,
				count(
				DISTINCT ( a.serial_number ))* 9 AS std,
				((count(
				DISTINCT ( a.serial_number ))* 9)/sum( a.actual_time ))*100 as eff
			FROM
				(
				SELECT
					operator_id,
					employee_syncs.`name`,
					location,
					serial_number,
					ROUND( TIMESTAMPDIFF( SECOND, sedang_start_date, sedang_finish_date )/ 60, 2 ) AS actual_time
				FROM
					assembly_logs
					JOIN employee_syncs ON employee_syncs.employee_id = assembly_logs.operator_id
				WHERE
					origin_group_code = '" . $origin . "'
					AND DATE( assembly_logs.created_at ) = '" . $tanggal . "'
				ORDER BY
					location
				) AS a
			WHERE
				a.location != 'packing'
				and a.location not like '%qa%'
				and a.location not like '%kensa%'
				and a.location not like '%registration%'
			GROUP BY
				a.operator_id,
				a.`name`
			ORDER BY
				a.location");
        } else if ($request->get('origin_group') == 'clarinet') {
            $datas = db::select("SELECT
				a.operator_id,
				a.`name`,
				a.location,
				count(
				DISTINCT ( a.serial_number )) AS result,
				sum( a.actual_time ) AS actual,
				count(
				DISTINCT ( a.serial_number ))* 9 AS std,
				((count(
				DISTINCT ( a.serial_number ))* 9)/sum( a.actual_time ))*100 as eff
			FROM
				(
				SELECT
					operator_id,
					employee_syncs.`name`,
					assembly_logs.location as location_cl,
					assembly_operators.location,
					serial_number,
					ROUND( TIMESTAMPDIFF( SECOND, sedang_start_date, sedang_finish_date )/ 60, 2 ) AS actual_time
				FROM
					assembly_logs
					LEFT JOIN employee_syncs ON employee_syncs.employee_id = assembly_logs.operator_id
					LEFT JOIN assembly_operators ON assembly_operators.employee_id = assembly_logs.operator_id
				WHERE
					assembly_logs.origin_group_code = '" . $origin . "'
					AND DATE( assembly_logs.created_at ) = '" . $tanggal . "'
				ORDER BY
					assembly_operators.location
				) AS a
			WHERE
				a.location != 'packing'
				and a.location not like '%qa%'
				and a.location not like '%kensa%'
				and a.location not like '%registration%'
				and (a.location like '%kariawase%' or a.location like '%tanpoawase%')
			GROUP BY
				a.operator_id,
				a.`name`
			ORDER BY
				a.location");
        }

        $detail = db::select("SELECT
		operator_id,
		employee_syncs.`name`,
		location,
		serial_number,
		ROUND( TIMESTAMPDIFF( SECOND, sedang_start_date, sedang_finish_date )/ 60, 2 ) AS actual_time
	FROM
		assembly_logs
		JOIN employee_syncs ON employee_syncs.employee_id = assembly_logs.operator_id
	WHERE
		origin_group_code = '" . $origin . "'
		AND DATE( assembly_logs.created_at ) = '" . $tanggal . "'
		and location != 'packing'
		and location not like '%qa%'
		and location not like '%kensa%'
		and location not like '%registration%'
		and location not like '%preparation%'
		and location not like '%repair%'
	ORDER BY
		location");

        $training = db::connection('ympimis_2')
            ->select("SELECT
	        *
	    FROM
	    assembly_trainings
	    WHERE
	    deleted_at IS NULL
		AND date = '" . $tanggal . "'
	    ORDER BY id desc
	");

        $response = array(
            'status' => true,
            'datas' => $datas,
            'detail' => $detail,
            'training' => $training,
            'date' => $dt,
        );
        return Response::json($response);
    }

    public function postTrainingEfficiency(Request $request)
    {
        try {
            $tujuan_upload = 'images/training';

            $file = $request->file('evidence');
            $nama = $file->getClientOriginalName();
            // $filename = pathinfo($nama, PATHINFO_FILENAME);
            $extension = pathinfo($nama, PATHINFO_EXTENSION);
            $filename = $request->input('operator') . ' (' . date('d-M-y H-i-s') . ').' . $extension;
            $file->move($tujuan_upload, $filename);

            $training = db::connection('ympimis_2')
                ->table('assembly_trainings')
                ->insert([
                    'date' => date('Y-m-d'),
                    'operator' => $request->input('operator'),
                    'detail' => $request->input('deskripsi'),
                    'action' => $request->input('action'),
                    'evidence' => $filename,
                    'created_by' => Auth::id(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $response = array(
                'status' => true,
                'message' => 'Data Training Berhasil Dimasukkan',
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

    public function indexAssyAllEfficiency($product)
    {
        $title = 'Operator Overall Efficiency';
        $title_jp = '作業者全体能率';
        return view('processes.assembly.saxophone.overall_op_efficiency', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Operator Overall Efficiency')->with('head', 'Assembly Process');

    }

    public function fetchAssyAllEfficiency(Request $request)
    {

        if ($request->get('origin_group') == 'Saxophone') {
            $origin = '043';
        } else {
            $origin = '042';
        }

        if ($request->get('tanggal') == '') {
            $tanggal = date('Y-m-d');
            $dt2 = date('d m Y');
            $dt = date('d M Y');
        } else {
            $tanggal = $request->get('tanggal');
            $dt2 = date('d m Y', strtotime($request->get('tanggal')));
            $dt = date('d M Y', strtotime($request->get('tanggal')));
        }

        $datas = [];
        $detail = [];

        $response = array(
            'status' => true,
            'datas' => $datas,
            'detail' => $detail,
            'date' => $dt,
        );
        return Response::json($response);
    }

    public function indexAssemblyGroupBalance($origin_group_code)
    {
        return view('processes.assembly.display.group_balance1', array(
            'title' => 'Assembly Group Balance',
            'title_jp' => '??',
        ))->with('page', 'Assembly Group Balance')->with('origin_group_code', $origin_group_code);
    }

    public function fetchAssemblyGroupBalance(Request $request)
    {
        if ($request->get('tanggal') == "") {
            $tanggal = date('Y-m-d');
        } else {
            $tanggal = date('Y-m-d', strtotime($request->get('tanggal')));
        }

        $data = null;
        $plan = null;
        if ($request->get('origin_group_code') == '042') {
            $titles = 'Clarinet';
        } else if ($request->get('origin_group_code') == '043') {
            $titles = 'Saxophone';
            $data = DB::SELECT("SELECT
				location,
				count( DISTINCT ( serial_number ) ) AS actual,
				count( DISTINCT ( serial_number ) ) * (select standard_time from assembly_std_times where assembly_std_times.origin_group_code = '043' and assembly_std_times.location = assembly_logs.location and assembly_std_times.deleted_at is null) AS actual_time,
			IF
				( location = '5' OR location = 'qa-kensa', 'Tenor', 'Alto' ) AS base_model,-- 	ROUND( count( DISTINCT ( serial_number ) ) * TIMESTAMPDIFF( SECOND, assembly_logs.sedang_start_date, assembly_logs.sedang_finish_date ), 2 ) AS actual_time
			IF
				(
					location = '1'
					OR location = '2'
					OR location = '3'
					OR location = '4',
					FLOOR( plan.plan / 4 ),
					plan.plan
				) AS plan,
			IF
				(
					location = '1'
					OR location = '2'
					OR location = '3'
					OR location = '4',
					FLOOR( plan.plan / 4 ),
					plan.plan
				) * (select standard_time from assembly_std_times where assembly_std_times.origin_group_code = '043' and assembly_std_times.location = assembly_logs.location and assembly_std_times.deleted_at is null) AS plan_time
			FROM
				`assembly_logs`
				LEFT JOIN (
				SELECT
				IF
					( SPLIT_STRING ( model, 'S', 1 ) = 'YA', 'Alto', 'Tenor' ) AS models,
					sum( quantity ) AS plan
				FROM
					production_schedules
					JOIN materials ON materials.material_number = production_schedules.material_number
				WHERE
					due_date = '" . $tanggal . "'
					AND category = 'FG'
					AND origin_group_code = '043'
				GROUP BY
					models
				) AS plan ON plan.models =
			IF
				( location = '5' OR location = 'qa-kensa', 'Tenor', 'Alto' )
			WHERE
				origin_group_code = '043'
				AND date( created_at ) = '" . $tanggal . "'
				and location != 'registration-process'
			GROUP BY
				location,
				plan.plan");

            // $plan = DB::SELECT("SELECT
            //     IF
            //         ( SPLIT_STRING ( model, 'S', 1 ) = 'YA', 'Alto', 'Tenor' ) AS models,
            //         sum( quantity ) AS plan
            //     FROM
            //         production_schedules
            //         JOIN materials ON materials.material_number = production_schedules.material_number
            //     WHERE
            //         due_date = '".$tanggal."'
            //         AND category = 'FG'
            //         AND origin_group_code = '043'
            //     GROUP BY
            //         models");

            $key = DB::SELECT("SELECT
			location AS `key`,
			count(
			DISTINCT ( operator_id )) AS jml
		FROM
			assembly_logs
		WHERE
			origin_group_code = '043'
			AND date( created_at ) = '" . $tanggal . "'
			and location != 'registration-process'
			and location != 'qa-audit'
		GROUP BY
			location");
        }

        $response = array(
            'status' => true,
            'data' => $data,
            // 'plan' => $plan,
            'key' => $key,
            'tanggal' => $tanggal,
            'titles' => $titles,
            'time' => date('H:i:s'),
        );
        return Response::json($response);

    }

    public function indexAssemblyOperator($origin_group_code)
    {
        if ($origin_group_code == '042') {
            $location = $this->location_cl;
        } else if ($origin_group_code == '043') {
            $location = $this->location_sx;
        }
        $emp = EmployeeSync::whereNull('end_date')->get();
        return view('processes.assembly.master_operator', array(
            'title' => 'Master Operator',
            'title_jp' => '',
        ))
            ->with('page', 'Master Operator')
            ->with('origin_group_code', $origin_group_code)
            ->with('emp', $emp)
            ->with('emp2', $emp)
            ->with('location', $location)
            ->with('location2', $location);
    }

    public function fetchAssemblyOperator(Request $request)
    {
        try {
            $operator = DB::table('assembly_operators')->select('assembly_operators.*', 'employee_syncs.name')->where('origin_group_code', $request->get('origin_group_code'))->leftjoin('employee_syncs', 'employee_syncs.employee_id', 'assembly_operators.employee_id')->get();

            $response = array(
                'status' => true,
                'operator' => $operator,
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

    public function inputAssemblyOperator(Request $request)
    {
        try {
            $input = DB::table('assembly_operators')->insert([
                'employee_id' => $request->get('operator'),
                'tag' => $this->dec2hex($request->get('tag')),
                'location' => $request->get('location') . '-' . $request->get('line'),
                'origin_group_code' => $request->get('origin_group_code'),
                'created_by' => Auth::id(),
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

    public function updateAssemblyOperator(Request $request)
    {
        try {
            $input = DB::table('assembly_operators')->where('id', $request->get('id'))->update([
                'employee_id' => $request->get('operator'),
                'tag' => $this->dec2hex($request->get('tag')),
                'location' => $request->get('location') . '-' . $request->get('line'),
                'origin_group_code' => $request->get('origin_group_code'),
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

    public function indexAssemblyResume()
    {
        return view('processes.assembly.display.resumes', array(
            'title' => 'Assembly Production Resume',
            'title_jp' => '??',
        ))->with('page', 'Assembly Production Resume')->with('origin_group_code');
    }

    public function fetchAssemblyResume(Request $request)
    {
        try {
            $date_from = $request->get('tanggal_from');
            $date_to = $request->get('tanggal_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = "DATE(NOW())";
                    $last = "DATE(NOW())";
                    $date = date('Y-m-d');
                    $monthTitle = date("d M Y", strtotime($date));
                } else {
                    $first = "DATE(NOW())";
                    $last = "'" . $date_to . "'";
                    $date = date('Y-m-d');
                    $monthTitle = date("d M Y", strtotime($date)) . ' to ' . date("d M Y", strtotime($date_to));
                }
            } else {
                if ($date_to == "") {
                    $first = "'" . $date_from . "'";
                    $last = "DATE(NOW())";
                    $date = date('Y-m-d');
                    $monthTitle = date("d M Y", strtotime($date_from)) . ' to ' . date("d M Y", strtotime($date));
                } else {
                    $first = "'" . $date_from . "'";
                    $last = "'" . $date_to . "'";
                    $monthTitle = date("d M Y", strtotime($date_from)) . ' to ' . date("d M Y", strtotime($date_to));
                }
            }

            if ($request->get('location') == '') {
                $locations_cl = "";
                $locations = "";
            } else {
                $locations_cl = "and assembly_operators.location like '%" . $request->get('location') . "%'";
                $locations = "and a.location like '%" . $request->get('location') . "%'";
            }

            if ($request->get('origin_group_code') == '043') {
                $locs = $this->location_sx2;
            } else if ($request->get('origin_group_code') == '042') {
                $locs = $this->location_cl;
            } else if ($request->get('origin_group_code') == '041') {
                $locs = $this->location_fl;
            }

            if ($request->get('origin_group_code') == '042') {
                $resume = DB::SELECT("SELECT
					a.operator_id,
					a.model,
					employee_syncs.`name`,
					assembly_operators.location,
					count( a.serial_number ) AS qty
				FROM
					(
					SELECT
						operator_id,
						serial_number,
						model
					FROM
						assembly_details
					WHERE
						DATE( sedang_start_date ) >= " . $first . "
						AND DATE( sedang_start_date ) <= " . $last . "
						AND assembly_details.origin_group_code = '" . $request->get('origin_group_code') . "'
						AND serial_number NOT LIKE '%_U%'
					GROUP BY
						operator_id,
						serial_number,
						model UNION ALL
					SELECT
						operator_id,
						serial_number,
						model
					FROM
						assembly_logs
					WHERE
						DATE( sedang_start_date ) >= " . $first . "
						AND DATE( sedang_start_date ) <= " . $last . "
						AND assembly_logs.origin_group_code = '" . $request->get('origin_group_code') . "'
						AND serial_number NOT LIKE '%_U%'
					GROUP BY
						operator_id,
						serial_number,
						model
					) a
					JOIN employee_syncs ON employee_syncs.employee_id = a.operator_id
					LEFT JOIN assembly_operators ON assembly_operators.employee_id = a.operator_id
				WHERE
					a.model != ''
					" . $locations_cl . "
				GROUP BY
					a.operator_id,
					employee_syncs.`name`,
					assembly_operators.location,
					a.model");
            } else {
                $resume = DB::SELECT("SELECT
					a.operator_id,
					a.model,
					a.location,
					employee_syncs.`name`,
					count( a.serial_number ) AS qty
				FROM
					(
					SELECT
						operator_id,
						serial_number,
						location,
						model
					FROM
						assembly_details
					WHERE
						DATE( sedang_start_date ) >= " . $first . "
						AND DATE( sedang_start_date ) <= " . $last . "
						AND assembly_details.origin_group_code = '" . $request->get('origin_group_code') . "'
					GROUP BY
						operator_id,
						serial_number,
						location,
						model UNION ALL
					SELECT
						operator_id,
						serial_number,
						location,
						model
					FROM
						assembly_logs
					WHERE
						DATE( sedang_start_date ) >= " . $first . "
						AND DATE( sedang_start_date ) <= " . $last . "
						AND assembly_logs.origin_group_code = '" . $request->get('origin_group_code') . "'
					GROUP BY
						operator_id,
						serial_number,
						location,
						model
					) a
					JOIN employee_syncs ON employee_syncs.employee_id = a.operator_id
				WHERE
					a.model != ''
					" . $locations . "
				GROUP BY
					a.operator_id,
					a.location,
					employee_syncs.`name`,
					a.model");
            }
            $response = array(
                'status' => true,
                'resume' => $resume,
                'location' => $locs,
                'monthTitle' => $monthTitle,
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

    public function indexResumeQA($origin_group_code)
    {
        if ($origin_group_code == '041') {
            $title = 'Flute';
        } else if ($origin_group_code == '042') {
            $title = 'Clarinet';
        } else {
            $title = 'Saxophone';
        }
        if (Auth::user()->username == 'PI0904005' || Auth::user()->username == 'PI1910002' || Auth::user()->username == 'PI1108002' || Auth::user()->username == 'PI9710001') {
            return view('processes.assembly.resume_qa', array(
                'origin_group_code' => $origin_group_code,
                'title' => 'Pass Ratio ' . $title,
                'prod' => $title,
                'title_jp' => '',
            ));
        } else {
            return view('404');
        }
    }

    public function fetchResumeQA(Request $request)
    {
        try {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = date('Y-m-01');
                    $last = date('Y-m-d');
                } else {
                    $first = date('Y-m-01');
                    $last = $date_to;
                }
            } else {
                if ($date_to == "") {
                    $first = $date_from;
                    $last = date('Y-m-d');
                } else {
                    $first = $date_from;
                    $last = $date_to;
                }
            }
            $resume = DB::SELECT("SELECT
				assembly_logs.serial_number,
				model,
				location,
				sedang_finish_date,
				ng.ng_name
			FROM
				assembly_logs
				LEFT JOIN ( SELECT serial_number, GROUP_CONCAT( IF ( ng_name LIKE '% - %', SPLIT_STRING ( ng_name, ' - ', 1 ), ng_name ) ) AS ng_name FROM assembly_ng_logs WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND ng_name NOT LIKE '%PR%' GROUP BY serial_number ) AS ng ON ng.serial_number = assembly_logs.serial_number
			WHERE
				DATE( sedang_finish_date ) >= '" . $first . "'
				AND DATE( sedang_finish_date ) <= '" . $last . "'
				and origin_group_code = '" . $request->get('origin_group_code') . "'
				AND location = 'packing'");
            $dateTitle = date("d M Y", strtotime($first)) . ' - ' . date("d M Y", strtotime($last));

            if ($request->get('fiscal_year') == '') {
                $fys = WeeklyCalendar::where('week_date', date('Y-m-d'))->first();
                $fy = $fys->fiscal_year;
            } else {
                $fy = $request->get('fiscal_year');
            }

            $pass_ratio = DB::SELECT("SELECT DISTINCT
				( assembly_logs.serial_number ),
				DATE_FORMAT( sedang_finish_date, '%Y-%m' ) AS `month`,
				DATE_FORMAT( sedang_finish_date, '%b-%Y' ) AS `month_name`,
				ng.serial_number AS ng
			FROM
				assembly_logs
				LEFT JOIN ( SELECT DISTINCT ( serial_number ) FROM assembly_ng_logs WHERE origin_group_code = '" . $request->get('origin_group_code') . "' ) AS ng ON ng.serial_number = assembly_logs.serial_number
			WHERE
				origin_group_code = '" . $request->get('origin_group_code') . "'
				AND location = 'packing'
				AND DATE_FORMAT( sedang_finish_date, '%Y-%m' ) IN (
				SELECT DISTINCT
					( DATE_FORMAT( week_date, '%Y-%m' ) ) AS `month`
				FROM
					weekly_calendars
			WHERE
				fiscal_year = '" . $fy . "')
				ORDER BY `month`");
            $response = array(
                'status' => true,
                'resumes' => $resume,
                'pass_ratio' => $pass_ratio,
                'dateTitle' => $dateTitle,
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

    public function indexSeasoning($origin_group_code)
    {
        if ($origin_group_code == '042') {
            $product = 'Clarinet';
        } else if ($origin_group_code == '043') {
            $product = 'Saxophone';
        } else {
            $product = 'Flute';
        }
        $page = 'Assembly ' . $product . ' Seasoning OUT';
        $title = 'Assembly ' . $product . ' Seasoning OUT';
        return view('processes.assembly.seasoning', array(
            'title' => $title,
            'title_jp' => '',
            'product' => $product,
        ))->with('page', $page)->with('origin_group_code', $origin_group_code);
    }

    public function indexSeasoningIn($origin_group_code)
    {
        if ($origin_group_code == '042') {
            $product = 'Clarinet';
        } else if ($origin_group_code == '043') {
            $product = 'Saxophone';
        } else {
            $product = 'Flute';
        }
        $page = 'Assembly ' . $product . ' Seasoning IN';
        $title = 'Assembly ' . $product . ' Seasoning IN';
        return view('processes.assembly.seasoning_in', array(
            'title' => $title,
            'title_jp' => '',
            'product' => $product,
        ))->with('page', $page)->with('origin_group_code', $origin_group_code);
    }

    public function fetchSeasoning(Request $request)
    {
        try {
            $origin_group_code = $request->get('origin_group_code');
            // $data = DB::connection('ympimis_2')->table('assembly_seasonings')->where('origin_group_code',$origin_group_code)->get();

            $tray = DB::connection('ympimis_2')->select("SELECT
				location,
				count( id ) AS qty,
				min( timestamps ) AS start_time,
				ROUND( ROUND(( TIMESTAMPDIFF( SECOND, min( timestamps ), NOW())/ 60 ), 2 )/ 60, 2 ) AS diff,
			IF
				( remark = 'Masuk_Lagi', 8, ROUND((( TIMESTAMPDIFF( SECOND, min( timestamps ), NOW())/ 60 )/ 60 )/ 24, 2 ) ) AS days,
				sum( CASE WHEN material LIKE '%YCL450%' THEN 1 ELSE 0 END ) AS ycl450,
				sum( CASE WHEN material LIKE '%YCL400%' THEN 1 ELSE 0 END ) AS ycl400
			FROM
				assembly_seasonings
			WHERE
				remark IS NULL
				OR remark = 'Masuk_Lagi'
				OR remark = 'Masuk'
			GROUP BY
				location,
				remark");

            $all_tray = DB::connection('ympimis_2')->SELECT("SELECT
					*,
                    DATE_FORMAT(DATE_ADD( timestamps, INTERVAL 7 DAY ),'%Y-%m-%d %H:%i:%s') AS plan_out
				FROM
					assembly_seasonings
				WHERE
					remark IS NULL
					OR remark = 'Masuk_Lagi'
					OR remark = 'Masuk'  ");
            $response = array(
                'status' => true,
                // 'data' => $data,
                'tray' => $tray,
                'all_tray' => $all_tray,
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

    public function fetchSeasoningIn(Request $request)
    {
        try {
            $origin_group_code = $request->get('origin_group_code');
            // $data = DB::connection('ympimis_2')->table('assembly_seasonings')->where('origin_group_code',$origin_group_code)->get();

            $tray = DB::connection('ympimis_2')->select("SELECT
				location,
				GROUP_CONCAT(
				DISTINCT ( seasoning_id )) AS seasoning_id
			FROM
				assembly_seasonings
			WHERE
				remark = 'QA'
				OR remark = 'Keluar'
			GROUP BY
				location
			ORDER BY
				created_at DESC");

            $all_tray = DB::connection('ympimis_2')->SELECT("SELECT
					*
				FROM
					assembly_seasonings
				WHERE
					remark = 'Keluar'
					OR remark = 'QA'  ");

            $response = array(
                'status' => true,
                // 'data' => $data,
                'tray' => $tray,
                'all_tray' => $all_tray,
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

    public function scanSeasoning(Request $request)
    {
        try {
            $tag = $this->dec2hex($request->get('tag'));
            $origin_group_code = $request->get('origin_group_code');

            $location = null;

            $tags = DB::table('assembly_tags')->where('tag', $tag)->where('origin_group_code', $origin_group_code)->first();

            $packing = null;
            if ($request->get('statuses') == 'IN') {
                $packing = DB::SELECT("SELECT
					*
				FROM
					assembly_logs
				WHERE
					model LIKE 'YCL-450N//U ID'
					OR model LIKE 'YCL-400AD//U ID'");
            }

            // if ($request->get('statuses') == 'progress') {
            //     # code...
            // }
            if ($tags) {
                // $location_check = DB::connection('ympimis_2')->table('assembly_seasonings')->where('tag',$tag)->orderby('id','desc')->first();
                // if ($location_check) {
                //     $get_location = DB::table('assembly_flows')->where('origin_group_code',$origin_group_code)->where('process',$location_check->location)->orderBy('id','desc')->first();
                //     $loc_next = DB::table('assembly_flows')->where('origin_group_code',$origin_group_code)->where('flow',($get_location->flow+1))->first();
                //     if ($loc_next) {
                //         $location = $loc_next->process;
                //     }
                // }else{
                //     $loc_next = DB::table('assembly_flows')->where('origin_group_code',$origin_group_code)->where('flow','1')->first();
                //     $location = $loc_next->process;
                // }
                $response = array(
                    'status' => true,
                    'tags' => $tags,
                    'packing' => $packing,
                    'message' => 'Scan Success',
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Tag Tidak Terdeteksi',
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

    public function inputSeasoning(Request $request)
    {
        try {
            // $tag = $request->get('tag');
            // $location = $request->get('location');
            // $origin_group_code = $request->get('origin_group_code');
            // $employee_id = $request->get('employee_id');
            // $name = $request->get('name');

            // $tags = DB::table('assembly_tags')->where('tag',$this->dec2hex($tag))->where('origin_group_code',$origin_group_code)->first();

            // $model = $tags->model;

            // if ($request->get('model') == '') {
            //     $model = $tags->model;
            // }else{
            //     $model = $request->get('model');
            // }

            // if ($location == 'Keluar Seasoning') {
            //     $update_assembly = DB::table('assembly_tags')->where('tag',$this->dec2hex($tag))->update([
            //         'model' => $model
            //     ]);
            // }
            // // else if($location == 'Material Selesai'){
            // //     $update_assembly = DB::table('assembly_tags')->where('tag',$this->dec2hex($tag))->update([
            // //         'model' => null
            // //     ]);
            // // }

            // $tags = DB::table('assembly_tags')->where('tag',$this->dec2hex($tag))->where('origin_group_code',$origin_group_code)->first();

            // $input = DB::connection('ympimis_2')->table('assembly_seasonings')->insert([
            //     'tag' => $this->dec2hex($tag),
            //     'location' => $location,
            //     'material' => $tags->remark.' - '.$tags->model,
            //     'origin_group_code' => $origin_group_code,
            //     'employee_id' => $employee_id,
            //     'name' => $name,
            //     'timestamps' => date('Y-m-d H:i:s'),
            //     'created_by' => Auth::user()->id,
            //     'created_at' => date('Y-m-d H:i:s'),
            //     'updated_at' => date('Y-m-d H:i:s'),
            // ]);

            $tray = $request->get('tray');
            $tag = $request->get('tag');
            $inprogress = $request->get('inprogress');

            $update_keluar = DB::connection('ympimis_2')->table('assembly_seasonings')->where('location', $tray)->where('remark', 'Keluar')->update([
                'seasoning_tag' => $tag,
                'remark' => 'Keluar_Close',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $data = DB::connection('ympimis_2')->table('assembly_seasonings')->where('location', $tray);
            $data = $data->where(
                function ($query) {
                    return $query
                        ->where('remark', '=', 'Masuk')
                        ->orWhere('remark', '=', 'Masuk_Lagi');
                });
            $data = $data->get();

            if (count($data) > 0) {
                $code_generator = CodeGenerator::where('note', 'seasoning')->first();
                $number_season = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
                $code_generator->index = $code_generator->index + 1;

                for ($i = 0; $i < count($data); $i++) {
                    DB::connection('ympimis_2')->table('assembly_seasonings')->insert([
                        'seasoning_tag' => $tag,
                        'seasoning_id' => $code_generator->prefix . $number_season,
                        'origin_group_code' => '042',
                        'location' => $data[$i]->location,
                        'tag' => $data[$i]->tag,
                        'material' => $data[$i]->material,
                        'employee_id' => $data[$i]->employee_id,
                        'name' => $data[$i]->name,
                        'timestamps' => date('Y-m-d H:i:s'),
                        'created_by' => Auth::user()->id,
                        'remark' => 'Keluar_Close',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $out = DB::connection('ympimis_2')->table('assembly_seasonings')->where('id', $data[$i]->id)->update([
                        'seasoning_tag' => $tag,
                        'seasoning_id' => $code_generator->prefix . $number_season,
                        'remark' => 'Masuk_Close',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
                $code_generator->save();

                $code_generator = CodeGenerator::where('note', 'seasoning')->first();
                $number_season = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
                $code_generator->index = $code_generator->index + 1;
                for ($i = 0; $i < count($data); $i++) {
                    if (in_array($data[$i]->id, $inprogress)) {
                        $input = DB::connection('ympimis_2')->table('assembly_seasonings')->insert([
                            'seasoning_tag' => '',
                            'seasoning_id' => $code_generator->prefix . $number_season,
                            'origin_group_code' => '042',
                            'location' => 'SET',
                            'tag' => $data[$i]->tag,
                            'material' => $data[$i]->material,
                            'employee_id' => $data[$i]->employee_id,
                            'name' => $data[$i]->name,
                            'remark' => 'Masuk_Lagi',
                            'timestamps' => date('Y-m-d H:i:s'),
                            'created_by' => Auth::user()->id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
                $code_generator->save();

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

    public function inputSeasoningIn(Request $request)
    {
        try {

            if ($request->get('empty') == 'empty') {
                $tray = $request->get('tray');
                $tag = $request->get('tag');
                $update = DB::connection('ympimis_2')->table('assembly_seasonings')->where('location', $tray)->where('remark', 'Keluar')->update([
                    'remark' => 'Keluar_Close',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $tray = $request->get('tray');
                $tag = $request->get('tag');
                $id = $request->get('id');
                $code_generator = CodeGenerator::where('note', 'seasoning')->first();
                $number_season = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
                $code_generator->index = $code_generator->index + 1;

                for ($i = 0; $i < count($id); $i++) {
                    $data = DB::connection('ympimis_2')->table('assembly_seasonings')->where('id', $id[$i])->first();
                    if ($data->remark == 'QA') {
                        $update_qa = DB::connection('ympimis_2')->table('assembly_seasonings')->where('id', $id[$i])->update([
                            'seasoning_tag' => $tag,
                            'seasoning_id' => $code_generator->prefix . $number_season,
                            'remark' => 'Masuk',
                            'timestamps' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    } else {
                        $update = DB::connection('ympimis_2')->table('assembly_seasonings')->where('location', $tray)->where('remark', 'Keluar')->update([
                            'remark' => 'Keluar_Close',
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                        DB::connection('ympimis_2')->table('assembly_seasonings')->insert([
                            'origin_group_code' => '042',
                            'seasoning_tag' => $tag,
                            'seasoning_id' => $code_generator->prefix . $number_season,
                            'location' => $data->location,
                            'tag' => $data->tag,
                            'material' => $data->material,
                            'employee_id' => $data->employee_id,
                            'name' => $data->name,
                            'remark' => 'Masuk_Lagi',
                            'timestamps' => date('Y-m-d H:i:s'),
                            'created_by' => Auth::user()->id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
                $code_generator->save();
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

    public function indexAssemblyResumeNG()
    {
        return view('processes.assembly.resume_ng', array(
            'title' => 'Assembly Resume NG',
            'title_jp' => '',
        ))->with('page', 'Assembly Resume NG');
    }

    public function fetchAssemblyResumeNG(Request $request)
    {
        try {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = date('Y-m-d');
                    $last = date('Y-m-d');
                } else {
                    $first = date('Y-m-d');
                    $last = $date_to;
                }
            } else {
                if ($date_to == "") {
                    $first = $date_from;
                    $last = date('Y-m-d');
                } else {
                    $first = $date_from;
                    $last = $date_to;
                }
            }
            if ($request->get('ng_name') == '') {
                $ng_name = '';
            } else {
                $ng_name = "AND ng_name LIKE '%" . $request->get('ng_name') . "%'";
            }
            $all_process = DB::SELECT("SELECT
				serial_number,
				GROUP_CONCAT(
				DISTINCT ( operator_id ))
			FROM
				assembly_details
			WHERE
				DATE( sedang_start_date ) >= '" . $first . "'
				AND DATE( sedang_start_date ) <= '" . $last . "'
				AND serial_number NOT LIKE '%_U%'
				AND serial_number != ''
			GROUP BY
				serial_number UNION ALL
			SELECT
				serial_number,
				GROUP_CONCAT(
				DISTINCT ( operator_id ))
			FROM
				assembly_logs
			WHERE
				DATE( sedang_start_date ) >= '" . $first . "'
				AND DATE( sedang_start_date ) <= '" . $last . "'
				AND serial_number NOT LIKE '%_U%'
				AND serial_number != ''
			GROUP BY
				serial_number");

            $perolehan = DB::SELECT("SELECT
				a.origin_group_code,
				count(
				DISTINCT ( serial_number )) AS perolehan
			FROM
				(
				SELECT
					serial_number,
					origin_group_code
				FROM
					assembly_logs
				WHERE
					DATE( assembly_logs.sedang_start_date ) >= '" . $first . "'
					AND DATE( assembly_logs.sedang_start_date ) <= '" . $last . "'
					AND location IN (
						'tanpoawase-fungsi',
						'tanpoawase-kensa',
						'qa-fungsi',
						'renraku-fungsi',
						'fukiage1-visual',
						'qa-visual1',
						'qa-visual2',
						'perakitanawal-kensa',
						'kariawase-visual',
						'kango-fungsi',
						'repair-process',
						'kariawase-fungsi',
						'kango-kensa',
						'qa-kensasp',
						'kensa-process',
						'qa-kensa',
						'qa-visual',
						'qa-audit'
					) UNION ALL
				SELECT
					serial_number,
					origin_group_code
				FROM
					assembly_details
				WHERE
					DATE( assembly_details.sedang_start_date ) >= '" . $first . "'
					AND DATE( assembly_details.sedang_start_date ) <= '" . $last . "'
					AND location IN (
						'tanpoawase-fungsi',
						'tanpoawase-kensa',
						'qa-fungsi',
						'renraku-fungsi',
						'fukiage1-visual',
						'qa-visual1',
						'qa-visual2',
						'perakitanawal-kensa',
						'kariawase-visual',
						'kango-fungsi',
						'repair-process',
						'kariawase-fungsi',
						'kango-kensa',
						'qa-kensasp',
						'kensa-process',
						'qa-kensa',
						'qa-visual',
						'qa-audit'
					)) a
			GROUP BY
				a.origin_group_code");

            $ng = DB::SELECT("SELECT
				assembly_ng_logs.*,
				employee_syncs.`name`,
				DATE( assembly_ng_logs.created_at ) AS dates,
				assembly_ng_logs.created_at as created
			FROM
				assembly_ng_logs
				LEFT JOIN employee_syncs ON employee_syncs.employee_id = assembly_ng_logs.employee_id
			WHERE
				DATE( assembly_ng_logs.created_at ) >= '" . $first . "'
				AND DATE( assembly_ng_logs.created_at ) <= '" . $last . "'
				" . $ng_name . "");

            $dateTitleFirst = date('d M Y', strtotime($first));
            $dateTitleLast = date('d M Y', strtotime($last));

            $ng_names = DB::SELECT("SELECT DISTINCT
				( ng_name )
			FROM
				assembly_ng_logs
			WHERE
				origin_group_code = '" . $request->get('origin_group_code') . "'
			ORDER BY
				ng_name");

            $emp = EmployeeSync::where('end_date', null)->get();

            $ng_monthly = DB::SELECT("SELECT
				assembly_ng_logs.*,
				employee_syncs.`name`,
				DATE( assembly_ng_logs.created_at ) AS dates,
				assembly_ng_logs.created_at as created
			FROM
				assembly_ng_logs
				LEFT JOIN employee_syncs ON employee_syncs.employee_id = assembly_ng_logs.employee_id
				WHERE-- 	origin_group_code = '042'
				DATE( assembly_ng_logs.created_at ) >= '" . date('Y-m-d', strtotime('-30 days', strtotime($last))) . "'
				AND DATE( assembly_ng_logs.created_at ) <= '" . $last . "'
				" . $ng_name . "");

            $response = array(
                'status' => true,
                'all_process' => $all_process,
                'ng' => $ng,
                'ng_monthly' => $ng_monthly,
                'perolehan' => $perolehan,
                'ng_name' => $ng_names,
                'emp' => $emp,
                'first' => $first,
                'last' => $last,
                'dateTitleFirst' => $dateTitleFirst,
                'dateTitleLast' => $dateTitleLast,
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

    public function indexAssemblyPareto($origin_group_code)
    {
        if ($origin_group_code == '043') {
            $product = 'Saxophone';
        } else if ($origin_group_code == '041') {
            $product = 'Flute';
        } else {
            $product = 'Clarinet';
        }
        return view('processes.assembly.display.pareto', array(
            'title' => 'Assembly Pareto ' . $product,
            'title_jp' => '',
            'product' => $product,
            'origin_group_code' => $origin_group_code,
        ))->with('page', 'Assembly Pareto');
    }

    public function fetchAssemblyPareto(Request $request)
    {
        try {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = date('Y-m-01');
                    $last = date('Y-m-d');
                } else {
                    $first = date('Y-m-01');
                    $last = $date_to;
                }
            } else {
                if ($date_to == "") {
                    $first = $date_from;
                    $last = date('Y-m-d');
                } else {
                    $first = $date_from;
                    $last = $date_to;
                }
            }

            $pareto2 = null;

            if ($request->get('origin_group_code') == '043') {
                $pareto = DB::SELECT("SELECT
					*,
					DATE_FORMAT( created_at, '%d-%b-%Y' ) AS dates,
					DATE_FORMAT( created_at, '%Y-%m-%d' ) AS date
				FROM
					assembly_ng_logs
				WHERE
					origin_group_code = '" . $request->get('origin_group_code') . "'
					AND location LIKE '%qa%'
					AND serial_number IN (SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        join assembly_logs on assembly_logs.serial_number = flo_details.serial_number and assembly_logs.location = 'packing'
                        and assembly_logs.origin_group_code = '043'
                        and (status_material != 'J' or status_material is null)
                    WHERE
                        flo_details.origin_group_code = '043'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "'
                         )
				ORDER BY
					created_at");
            } else if ($request->get('origin_group_code') == '042') {
                $pareto = DB::SELECT("SELECT
					*,
					DATE_FORMAT( created_at, '%d-%b-%Y' ) AS dates,
					DATE_FORMAT( created_at, '%Y-%m-%d' ) AS date
				FROM
					assembly_ng_logs
				WHERE
					origin_group_code = '" . $request->get('origin_group_code') . "'
					AND location LIKE '%qa%'
					AND ng_name not like '%(PR)%'
					AND serial_number IN ( SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        join assembly_logs on assembly_logs.serial_number = flo_details.serial_number and assembly_logs.location = 'packing'
                        and assembly_logs.origin_group_code = '042'
                        and (status_material != 'J' or status_material is null)
                    WHERE
                        (flo_details.origin_group_code = '042'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "'
                        AND assembly_logs.model LIKE '%200%')
                        OR
                        (flo_details.origin_group_code = '042'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "'
                        AND assembly_logs.model LIKE '%255%') )
				ORDER BY
					created_at");

                $pareto2 = DB::SELECT("SELECT
                    *,
                    DATE_FORMAT( created_at, '%d-%b-%Y' ) AS dates,
                    DATE_FORMAT( created_at, '%Y-%m-%d' ) AS date
                FROM
                    assembly_ng_logs
                WHERE
                    origin_group_code = '" . $request->get('origin_group_code') . "'
                    AND location LIKE '%qa%'
                    AND ng_name not like '%(PR)%'
                    AND serial_number IN ( SELECT DISTINCT
                        ( flo_details.serial_number )
                    FROM
                        flo_details
                        join assembly_logs on assembly_logs.serial_number = flo_details.serial_number and assembly_logs.location = 'packing'
                        and assembly_logs.origin_group_code = '042'
                        and (status_material != 'J' or status_material is null)
                    WHERE
                        (flo_details.origin_group_code = '042'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "'
                        AND assembly_logs.model LIKE '%400%')
                        OR
                        (flo_details.origin_group_code = '042'
                        AND DATE( flo_details.created_at ) >= '" . $first . "'
                        AND DATE( flo_details.created_at ) <= '" . $last . "'
                        AND assembly_logs.model LIKE '%450%') )
                ORDER BY
                    created_at");
            } else if ($request->get('origin_group_code') == '041') {
                $pareto = DB::SELECT("SELECT
						a.*
					FROM
						(
						SELECT
							*,
							DATE_FORMAT( created_at, '%d-%b-%Y' ) AS dates,
							DATE_FORMAT( created_at, '%Y-%m-%d' ) AS date
						FROM
							assembly_ng_logs
						WHERE
							( location = 'qa-visual1' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Kake%' )
							OR ( location = 'qa-visual1' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Nami%' )
							OR ( location = 'qa-visual1' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Aus%' )
							OR ( location = 'qa-visual1' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Oil Mekki%' )
							OR ( location = 'qa-visual1' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Mekki%' )
							OR ( location = 'qa-visual1' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Heko%' )
							OR ( location = 'qa-visual1' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Deko%' )
							OR ( location = 'qa-visual1' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Toke%' )
							OR ( location = 'qa-visual1' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Shaft Keluar%' )
							OR ( location = 'qa-visual1' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Stamp / Marking Putus%' )
							OR ( location = 'qa-visual1' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Handa%' )
							OR ( location = 'qa-visual1' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Nanbutsu - Cork Lem Tare%' )
							OR ( location = 'qa-visual1' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Magari%' )
							OR ( location = 'qa-visual1' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Narabi%' )
							OR ( location = 'qa-visual1' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Tanpo - Zarazure%' )
							OR ( location = 'qa-visual1' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Bari%' ) UNION ALL
						SELECT
							*,
							DATE_FORMAT( created_at, '%d-%b-%Y' ) AS dates,
							DATE_FORMAT( created_at, '%Y-%m-%d' ) AS date
						FROM
							assembly_ng_logs
						WHERE
							( location = 'qa-visual2' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Kake%' )
							OR ( location = 'qa-visual2' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Nami%' )
							OR ( location = 'qa-visual2' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Aus%' )
							OR ( location = 'qa-visual2' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Oil Mekki%' )
							OR ( location = 'qa-visual2' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Mekki%' )
							OR ( location = 'qa-visual2' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Heko%' )
							OR ( location = 'qa-visual2' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Deko%' )
							OR ( location = 'qa-visual2' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Toke%' )
							OR ( location = 'qa-visual2' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Shaft Keluar%' )
							OR ( location = 'qa-visual2' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Stamp / Marking Putus%' )
							OR ( location = 'qa-visual2' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Handa%' )
							OR ( location = 'qa-visual2' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Nanbutsu - Cork Lem Tare%' )
							OR ( location = 'qa-visual2' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Magari%' )
							OR ( location = 'qa-visual2' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Narabi%' )
							OR ( location = 'qa-visual2' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Tanpo - Zarazure%' )
							OR ( location = 'qa-visual2' AND origin_group_code = '041' AND location LIKE '%qa%' AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )  AND ng_name LIKE '%Bari%' ) UNION ALL
						SELECT
							*,
							DATE_FORMAT( created_at, '%d-%b-%Y' ) AS dates,
							DATE_FORMAT( created_at, '%Y-%m-%d' ) AS date
						FROM
							assembly_ng_logs
						WHERE
							location = 'qa-fungsi'
							AND origin_group_code = '041'
							AND location LIKE '%qa%'
							AND serial_number IN ( SELECT DISTINCT ( serial_number ) FROM flo_details WHERE origin_group_code = '" . $request->get('origin_group_code') . "' AND DATE( created_at ) >= '" . $first . "' AND DATE( created_at ) <= '" . $last . "' )
						ORDER BY
							created_at
						) a
					WHERE
						a.model NOT IN (
						SELECT DISTINCT
							( model )
						FROM
							stamp_hierarchies
						WHERE
						remark = 'SP'
						OR model = 'YFL222HD')");
            }

            $dateTitleFirst = date("d M Y", strtotime($first));
            $dateTitleLast = date("d M Y", strtotime($last));

            $emp = EmployeeSync::where('end_date', null)->get();

            $response = array(
                'status' => true,
                'pareto' => $pareto,
                'pareto2' => $pareto2,
                'emp' => $emp,
                'dateTitleFirst' => $dateTitleFirst,
                'dateTitleLast' => $dateTitleLast,
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

    public function indexAssemblyLineBalance($origin_group_code)
    {
        return view('processes.assembly.display.line_balance', array(
            'title' => 'Assembly Line Balance',
            'title_jp' => '??',
        ))->with('page', 'Assembly Line Balance')->with('origin_group_code', $origin_group_code);
    }

    public function fetchAssemblyLineBalance(Request $request)
    {
        try {
            $now = date('Y-m-d');
            if ($request->get('date') != '') {
                $now = $request->get('date');
            }
            $lines = "";
            if ($request->get('line') != '') {
                $lines = "AND a.line = '" . $request->get('line') . "'";
            }
            // $line_balance = DB::SELECT("SELECT
            //     a.line,
            //     a.`group`,
            //     sum( diff ) as diff
            // FROM
            //     (
            //     SELECT COALESCE
            //         ( IF ( location REGEXP '[0-9]+', 'WIP', UPPER( location )), 1 ) AS `group`,
            //         COALESCE ( IF ( location REGEXP '[0-9]+', location, location_number ), 1 ) AS line,
            //         sedang_start_date,
            //         sedang_finish_date,
            //         TIMESTAMPDIFF( SECOND, sedang_start_date, sedang_finish_date ) AS diff
            //     FROM
            //         assembly_details
            //     WHERE
            //         origin_group_code = '".$request->get('origin_group_code')."'
            //         AND date( sedang_start_date ) = '".$now."'
            //     UNION ALL
            //     SELECT COALESCE
            //         ( IF ( location REGEXP '[0-9]+', 'WIP', UPPER( location )), 1 ) AS `group`,
            //         COALESCE ( IF ( location REGEXP '[0-9]+', location, location_number ), 1 ) AS line,
            //         sedang_start_date,
            //         sedang_finish_date,
            //         TIMESTAMPDIFF( SECOND, sedang_start_date, sedang_finish_date ) AS diff
            //     FROM
            //         assembly_logs
            //     WHERE
            //         origin_group_code = '".$request->get('origin_group_code')."'
            //         AND date( sedang_start_date ) = '".$now."'
            //     ) a
            //     WHERE a.`group` not in ('PACKING','REGISTRATION-PROCESS','QA-AUDIT')
            //     ".$lines."
            // GROUP BY
            //     a.line,
            //     a.`group`");

            $operator = DB::SELECT("SELECT DISTINCT
					( a.operator_id ),
					a.`group`,
					a.line
				FROM
					((
						SELECT DISTINCT
							( operator_id ),
							COALESCE ( IF ( location REGEXP '[0-9]+', 'WIP', UPPER( location )), 1 ) AS `group`,
							COALESCE ( IF ( location REGEXP '[0-9]+', location, location_number ), 1 ) AS line
						FROM
							assembly_details
						WHERE
							origin_group_code = '" . $request->get('origin_group_code') . "'
							AND date( sedang_start_date ) = '" . $now . "' UNION ALL
						SELECT DISTINCT
							( operator_id ),
							COALESCE ( IF ( location REGEXP '[0-9]+', 'WIP', UPPER( location )), 1 ) AS `group`,
							COALESCE ( IF ( location REGEXP '[0-9]+', location, location_number ), 1 ) AS line
						FROM
							assembly_logs
						WHERE
							origin_group_code = '" . $request->get('origin_group_code') . "'
							AND date( sedang_start_date ) = '" . $now . "'
						)) a
				WHERE
					a.`group` NOT IN ( 'PACKING', 'REGISTRATION-PROCESS', 'QA-AUDIT', 'PREPARATION-PROCESS' )
					AND a.line = '1'
				ORDER BY
					a.line,
					a.`group`
				LIMIT 14");

            $operator_line2 = DB::SELECT("SELECT DISTINCT
					( a.operator_id ),
					a.`group`,
					a.line
				FROM
					((
						SELECT DISTINCT
							( operator_id ),
							COALESCE ( IF ( location REGEXP '[0-9]+', 'WIP', UPPER( location )), 1 ) AS `group`,
							COALESCE ( IF ( location REGEXP '[0-9]+', location, location_number ), 1 ) AS line
						FROM
							assembly_details
						WHERE
							origin_group_code = '" . $request->get('origin_group_code') . "'
							AND date( sedang_start_date ) = '" . $now . "' UNION ALL
						SELECT DISTINCT
							( operator_id ),
							COALESCE ( IF ( location REGEXP '[0-9]+', 'WIP', UPPER( location )), 1 ) AS `group`,
							COALESCE ( IF ( location REGEXP '[0-9]+', location, location_number ), 1 ) AS line
						FROM
							assembly_logs
						WHERE
							origin_group_code = '" . $request->get('origin_group_code') . "'
							AND date( sedang_start_date ) = '" . $now . "'
						)) a
				WHERE
					a.`group` NOT IN ( 'PACKING', 'REGISTRATION-PROCESS', 'QA-AUDIT', 'PREPARATION-PROCESS' )
					AND a.line = '2'
				ORDER BY
					a.line,
					a.`group`
				LIMIT 14");

            $emp = EmployeeSync::get();
            $response = array(
                'status' => true,
                // 'line_balance' => $line_balance,
                'operator' => $operator,
                'operator_line2' => $operator_line2,
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

    public function indexAssemblyLineBalance2($origin_group_code)
    {
        return view('processes.assembly.display.line_balance2', array(
            'title' => 'Assembly Line Balance',
            'title_jp' => '??',
        ))->with('page', 'Assembly Line Balance')->with('origin_group_code', $origin_group_code);
    }

    public function indexReportQAAudit($origin_group_code)
    {
        if (Auth::user()->username == 'PI0904005' || Auth::user()->username == 'PI1910002' || Auth::user()->username == 'PI1108002' || Auth::user()->username == 'PI9710001') {
            return view('processes.assembly.report_qa_audit', array(
                'title' => 'Assembly QA Audit Report',
                'title_jp' => '??',
            ))->with('page', 'Assembly QA Audit Report')->with('origin_group_code', $origin_group_code);
        } else {
            return view('404');
        }
    }

    public function fetchReportQAAudit(Request $request)
    {
        try {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = date('Y-m-01');
                    $last = date('Y-m-d');
                } else {
                    $first = date('Y-m-01');
                    $last = $date_to;
                }
            } else {
                if ($date_to == "") {
                    $first = $date_from;
                    $last = date('Y-m-d');
                } else {
                    $first = $date_from;
                    $last = $date_to;
                }
            }
            $report = DB::SELECT("SELECT
					assembly_logs.*,
					assembly_ng_logs.ng_name,
					assembly_ng_logs.ongko,
					assembly_ng_logs.value_atas,
					assembly_ng_logs.value_bawah,
					assembly_ng_logs.value_lokasi,
					assembly_ng_logs.decision
				FROM
					`assembly_logs`
					LEFT JOIN assembly_ng_logs ON assembly_ng_logs.serial_number = assembly_logs.serial_number
					AND assembly_logs.location = assembly_ng_logs.location
					AND assembly_logs.origin_group_code = assembly_ng_logs.origin_group_code
				WHERE
					assembly_logs.location = 'qa-audit'
					AND assembly_logs.origin_group_code = '" . $request->get('origin_group_code') . "'
					AND date( assembly_logs.sedang_start_date ) >= '" . $first . "'
					AND date( assembly_logs.sedang_start_date ) <= '" . $last . "'");

            $emp = EmployeeSync::get();
            $response = array(
                'status' => true,
                'first' => date('d-M-Y', strtotime($first)),
                'last' => date('d-M-Y', strtotime($last)),
                'report' => $report,
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

    public function indexSeasoningReport($origin_group_code)
    {
        if ($origin_group_code == '043') {
            $product = 'Saxophone';
        } else if ($origin_group_code == '041') {
            $product = 'Flute';
        } else {
            $product = 'Clarinet';
        }
        return view('processes.assembly.report_seasoning', array(
            'title' => 'Assembly Seasoning ' . $product,
            'title_jp' => '',
            'product' => $product,
            'origin_group_code' => $origin_group_code,
        ))->with('page', 'Assembly Seasoning');
    }

    public function fetchSeasoningReport(Request $request)
    {
        try {
            $model = '';
            $daisha = '';

            if ($request->get('model') != '') {
                $model = "AND material like '%" . $request->get('model') . "%'";
            }

            if ($request->get('daisha') != '') {
                $daisha = "AND location = '" . $request->get('daisha') . "'";
            }
            $report = DB::connection('ympimis_2')->SELECT("SELECT
				b.seasoning_id,
				b.location,
				b.material,
				b.employee_id,
				b.`name`,
				GROUP_CONCAT( waktu_masuk ) AS waktu_masuk,
				GROUP_CONCAT( waktu_keluar ) AS waktu_keluar
			FROM
				(
				SELECT
					seasoning_id,
					location,
					material,
					employee_id,
					`name`,
					remark,
					timestamps AS waktu_masuk,
					NULL AS waktu_keluar
				FROM
					`assembly_seasonings`
				WHERE
					remark != 'TRIAL'
					AND remark LIKE '%Masuk%'
					AND origin_group_code = '" . $request->get('origin_group_code') . "'
					" . $model . "
					" . $daisha . "
					UNION ALL
				SELECT
					seasoning_id,
					location,
					material,
					employee_id,
					`name`,
					remark,
					NULL AS waktu_masuk,
					timestamps AS waktu_keluar
				FROM
					`assembly_seasonings`
				WHERE
					remark != 'TRIAL'
					AND remark LIKE '%Keluar%'
					AND origin_group_code = '" . $request->get('origin_group_code') . "'
					" . $model . "
					" . $daisha . "
				) b
			GROUP BY
				b.seasoning_id,
				b.location,
				b.material,
				b.employee_id,
				b.`name`");

            $inventory = AssemblyInventory::where('model', 'YCL400AD')->orwhere('model','like', '%YCL450%')->get();
            $log = DB::SELECT("SELECT
					*
				FROM
					`ympimis`.`assembly_logs`
				WHERE
					( `model` LIKE '%400%' AND location = 'registration-process' )
					OR (
					`model` LIKE '%450%'
					AND location = 'registration-process')");
            $response = array(
                'status' => true,
                'report' => $report,
                'inventory' => $inventory,
                'log' => $log,
                'origin_group_code' => $request->get('origin_group_code'),
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

    public function indexSeasoningProgress($origin_group_code)
    {
        if ($origin_group_code == '042') {
            $product = 'Clarinet';
        } else if ($origin_group_code == '043') {
            $product = 'Saxophone';
        } else {
            $product = 'Flute';
        }
        $page = 'Assembly ' . $product . ' Seasoning In-Porgress';
        $title = 'Assembly ' . $product . ' Seasoning In-Porgress';
        return view('processes.assembly.seasoning_progress', array(
            'title' => $title,
            'title_jp' => '',
            'product' => $product,
        ))->with('page', $page)->with('origin_group_code', $origin_group_code);
    }

    public function inputSeasoningProgress(Request $request)
    {
        try {
            if ($request->get('process') == 'IN') {
                $serial_numbers_all = $request->get('serial_numbers_all');
                $serial_numbers = $request->get('serial_numbers');
                $condition = $request->get('condition');
                $operator_id = $request->get('operator_id');
                $operator_name = $request->get('operator_name');

                $code_generator = CodeGenerator::where('note', 'seasoning')->first();
                $number_season = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
                $code_generator->index = $code_generator->index + 1;

                for ($i = 0; $i < count($serial_numbers_all); $i++) {
                    $input = DB::connection('ympimis_2')->table('assembly_seasonings')->insert([
                        'seasoning_tag' => '',
                        'seasoning_id' => $code_generator->prefix . $number_season,
                        'origin_group_code' => '042',
                        'location' => $condition[$i],
                        'tag' => $this->dec2hex($serial_numbers_all[$i]['tag']),
                        'material' => $serial_numbers_all[$i]['remark'] . ' - ' . $serial_numbers_all[$i]['model'] . ' - ' . $serial_numbers_all[$i]['serial_number'],
                        'employee_id' => $operator_id,
                        'name' => $operator_name,
                        'remark' => 'Masuk_Lagi',
                        'timestamps' => date('Y-m-d H:i:s'),
                        'created_by' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }

                $code_generator->save();
            } else {
                $serial_numbers_all = $request->get('serial_numbers_all');
                $serial_numbers = $request->get('serial_numbers');
                $operator_id = $request->get('operator_id');
                $operator_name = $request->get('operator_name');

                for ($i = 0; $i < count($serial_numbers_all); $i++) {
                    $data_masuk = DB::connection('ympimis_2')->table('assembly_seasonings')->where('material', 'like', '%' . $serial_numbers_all[$i]['serial_number'] . '%')->where('remark', 'Masuk_Lagi')->where(
                        function ($query) {
                            return $query
                                ->where('location', '=', 'SET')
                                ->orWhere('location', '=', 'KANGO');
                        })->orderBy('created_at', 'desc')->first();
                    if ($data_masuk) {
                        $data_masukss = DB::connection('ympimis_2')->table('assembly_seasonings')->where('material', 'like', '%' . $serial_numbers_all[$i]['serial_number'] . '%')->where('remark', 'Masuk_Lagi')->where(
                            function ($query) {
                                return $query
                                    ->where('location', '=', 'SET')
                                    ->orWhere('location', '=', 'KANGO');
                            })->orderBy('created_at', 'desc')->limit(1)->update([
                            'remark' => 'Masuk_Close',
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                        $input = DB::connection('ympimis_2')->table('assembly_seasonings')->insert([
                            'seasoning_tag' => '',
                            'seasoning_id' => $data_masuk->seasoning_id,
                            'origin_group_code' => '042',
                            'location' => $data_masuk->location,
                            'tag' => $data_masuk->tag,
                            'material' => $data_masuk->material,
                            'employee_id' => $operator_id,
                            'name' => $operator_name,
                            'remark' => 'Keluar_Close',
                            'created_by' => Auth::user()->id,
                            'timestamps' => date('Y-m-d H:i:s'),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
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

    public function indexStatusMaterial($origin_group_code)
    {
        if (Auth::user()->username == 'PI0904005' || Auth::user()->username == 'PI1910002' || Auth::user()->username == 'PI1108002' || Auth::user()->username == 'PI9710001') {
            return view('processes.assembly.status_material', array(
                'title' => 'Assembly Status Material',
                'title_jp' => '??',
            ))->with('page', 'Assembly Status Material')->with('origin_group_code', $origin_group_code);
        } else {
            return view('404');
        }
    }

    public function fetchStatusMaterial(Request $request)
    {
        try {
            $status_material = DB::connection('ympimis_2')->table('assembly_status_materials');
            if ($request->get('product')) {
                $status_material = $status_material->where('origin_group_code',$request->get('product'));
            }
            if ($request->get('serial_number')) {
                $status_material = $status_material->where('serial_number',$request->get('serial_number'));
            }
            if ($request->get('date')) {
                $status_material = $status_material->whereDate('created_at',$request->get('date'));
            }
            $status_material = $status_material->get();

            $emp = EmployeeSync::get();
            $response = array(
                'status' => true,
                'status_material' => $status_material,
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

    public function indexReportSpecProduct()
    {
        return view('processes.assembly.report_spec_product', array(
            'title' => 'Assembly Report Spec Product QA',
            'title_jp' => '??',
        ))->with('page', 'Assembly Report Spec Product QA');
    }

    public function fetchReportSpecProduct(Request $request)
    {
        try {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = date('Y-m-01');
                    $last = date('Y-m-d');
                } else {
                    $first = date('Y-m-01');
                    $last = $date_to;
                }
            } else {
                if ($date_to == "") {
                    $first = $date_from;
                    $last = date('Y-m-d');
                } else {
                    $first = $date_from;
                    $last = $date_to;
                }
            }
            $report = DB::connection('ympimis_2')->table('assembly_spec_products')->select('serial_number','model','employee_id',DB::RAW("DATE_FORMAT(created_at,'%Y-%m-%d') as created"))->distinct()->whereDate('created_at','>=',$first)->whereDate('created_at','<=',$last);
            if ($request->get('serial_number')) {
                $report = $report->where('serial_number',$request->get('serial_number'));
            }
            $report = $report->get();
            $emp = EmployeeSync::get();
            $response = array(
                'status' => true,
                'report' => $report,
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

    public function fetchReportSpecProductDetail(Request $request)
    {
        try {
            $serial_number = $request->get('serial_number');
            $model = $request->get('model');
            $employee_id = $request->get('employee_id');
            $created = $request->get('created');

            $detail = DB::connection('ympimis_2')->table('assembly_spec_products')->where('serial_number',$serial_number)->where('model',$model)->where('employee_id',$employee_id)->whereDate('created_at',$created)->get();

            $spec_qa = DB::connection('ympimis_2')->table('assembly_spec_product_points')->where('origin_group_code', '043')->where('model', $model)->get();
            $spec_qa_now = DB::connection('ympimis_2')->table('assembly_spec_products')->where('origin_group_code', '043')->where('serial_number',$serial_number)->where('model',$model)->where('employee_id',$employee_id)->whereDate('created_at',$created)->get();
            $response = array(
                'status' => true,
                'detail' => $detail,
                'spec_qa' => $spec_qa,
                'spec_qa_now' => $spec_qa_now,
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

    public function indexReportSpecProductProcess()
    {
        return view('processes.assembly.report_spec_product_process', array(
            'title' => 'Assembly Report Spec Product Process',
            'title_jp' => '??',
        ))->with('page', 'Assembly Report Spec Product Process');
    }

    public function fetchReportSpecProductProcess(Request $request)
    {
        try {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            if ($date_from == "") {
                if ($date_to == "") {
                    $first = date('Y-m-01');
                    $last = date('Y-m-d');
                } else {
                    $first = date('Y-m-01');
                    $last = $date_to;
                }
            } else {
                if ($date_to == "") {
                    $first = $date_from;
                    $last = date('Y-m-d');
                } else {
                    $first = $date_from;
                    $last = $date_to;
                }
            }

            $serials = '';
            if ($request->get('serial_number') != '') {
                $serials= "AND serial_number = '".$request->get('serial_number')."'";
            }
            $report = DB::connection('ympimis_2')->SELECT("SELECT
                serial_number,
                model,
                employee_id,
                DATE(created_at) as created
            FROM
                `assembly_specs` 
            WHERE
                DATE( created_at ) >= '".$first."' 
                AND DATE( created_at ) <= '".$last."'
                ".$serials." 
            GROUP BY
                serial_number,
                model");

            $report_all = DB::connection('ympimis_2')->SELECT("SELECT
                *
            FROM
                `assembly_specs` 
            WHERE
                DATE( created_at ) >= '".$first."' 
                AND DATE( created_at ) <= '".$last."'
                ".$serials." ");
            $emp = EmployeeSync::get();
            $response = array(
                'status' => true,
                'report' => $report,
                'report_all' => $report_all,
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

    public function indexSerialNumberControl($origin_group_code)
    {
        if ($origin_group_code == '041') {
            $product = 'Flute';
        } else if ($origin_group_code == '042') {
            $product = 'Clarinet';
        } else {
            $product = 'Saxophone';
        }

        $material = DB::SELECT("SELECT
                * 
            FROM
                materials 
            WHERE
                category = 'FG' 
                AND origin_group_code = '".$origin_group_code."'");

        return view('processes.assembly.report_serial_number', array(
            'title' => 'Finished Goods Serial Number Control - '.$product,
            'title_jp' => '??',
            'product' => $product,
            'origin_group_code' => $origin_group_code,
            'material' => $material,
        ))->with('page', 'Finished Goods Serial Number Control');
    }

    public function fetchSerialNumberControl(Request $request)
    {
        try {
            $date_from = $request->get('datefrom');
            $date_to = $request->get('dateto');
            if ($date_from == "") {
             if ($date_to == "") {
              $first = date('Y-m-01');
              $last = date('Y-m-d');
            }else{
              $first = date('Y-m-01',strtotime($date_to));
              $last = $date_to;
            }
          }else{
           if ($date_to == "") {
            $first = $date_from;
            $last = date('Y-m-t',strtotime($date_from));
          }else{
            $first = $date_from;
            $last = $date_to;
          }
        }
        $material = '';
        if ($request->get('material') != '') {
            $material = "AND flo_details.material_number = '".$request->get('material')."'";
        }
            $report = DB::SELECT("SELECT
                serial_number,
                flo_details.material_number,
                materials.material_description,
                date( flo_details.created_at ) AS date,
                time( flo_details.created_at ) AS time,
                flo_details.flo_number,
                flos.destination_code,
                flos.invoice_number,
                destination_shortname
            FROM
                flo_details
                JOIN materials ON materials.material_number = flo_details.material_number
                JOIN flos ON flos.flo_number = flo_details.flo_number
                JOIN destinations ON destinations.destination_code = flos.destination_code 
            WHERE
                DATE( flo_details.created_at ) >= '".$first."' 
                AND DATE( flo_details.created_at ) <= '".$last."' 
                ".$material."
                AND flo_details.origin_group_code = '".$request->get('origin_group_code')."'");
            $response = array(
                'status' => true,
                'report' => $report,
                'monthTitle' => date('d M Y',strtotime($first)).' - '.date('d M Y',strtotime($last))
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
