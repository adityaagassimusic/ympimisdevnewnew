<?php

namespace App\Http\Controllers;

use App\CodeGenerator;
use App\ContainerAttachment;
use App\ErrorLog;
use App\Flo;
use App\FloDetail;
use App\FloLog;
use App\Inventory;
use App\KnockDown;
use App\LogProcess;
use App\LogTransaction;
use App\MasterChecksheet;
use App\Material;
use App\MaterialVolume;
use App\ShipmentReservation;
use App\ShipmentSchedule;
use App\StampInventory;
use App\TransactionTransfer;
use App\User;
use Carbon\Carbon;
use DataTables;
use File;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Response;

class FloController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->sax_trial_serial_number = [
            '21R47365',
            '21R47366',
            '21R47367',
            '21R47368',
            '21R47369',
            '21R47370',
            '21R47371',
            '21R47372',
            '21R47373',
            '21R47374',
            '21R47375',
            '21R47376',
            '21R47377',
            '21R47378',
            '21R47379',
            '21R47380',
            '21R47381',
            '21R47382',
            '21R47383',
            '21R47384',
            '21R47385',
            '21R47386',
            '21R47387',
            '21R47388',
            '21R47389',
            '21R47390',
            '21R47391',
            '21R47392',
            '21R47393',
            '21R47394',
            '21R47666',
            '21R47667',
            '21R47668',
            '21R47669',
            '21R47670',
            '21R47671',
            '21R47672',
            '21R47673',
            '21R47674',
            '21R47675',
            '21R47676',
            '21R47677',
            '21R47678',
            '21R47679',
            '21R47680',
            '21R47681',
            '21R47682',
            '21R47683',
            '21R47684',
            '21R47685',
            '21R47862',
            '21R47863',
            '21R47864',
            '21R47865',
            '21R47866',
            '21R47867',
            '21R47868',
            '21R47869',
            '21R47870',
            '21R47871',
        ];
    }

    public function index_flo_open()
    {
        $title = "FLO Open Destination";
        $title_jp = "";

        $hpl = DB::select("SELECT DISTINCT hpl FROM materials
			WHERE category = 'FG'");

        return view('flos.flo_open', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'hpls' => $hpl,
        ))->with('page', $title)->with('head', $title);
    }

    public function index_bi()
    {
        $flos = Flo::orderBy('flo_number', 'asc')
            ->whereIn('flos.status', ['0', '1'])
            ->get();
        return view('flos.flo_bi', array(
            'flos' => $flos,
        ))->with('page', 'FLO Band Instrument');
    }

    public function index_ei()
    {
        $flos = Flo::orderBy('flo_number', 'asc')
            ->whereIn('status', ['0', '1'])
            ->get();
        return view('flos.flo_ei', array(
            'flos' => $flos,
        ))->with('page', 'FLO Educational Instrument');
    }

    public function index_delivery()
    {
        $flos = Flo::orderBy('flo_number', 'asc')
            ->whereIn('status', ['1', '2'])
            ->get();

        return view('flos.flo_delivery', array(
            'flos' => $flos,
        ))->with('page', 'FLO Delivery');
    }

    public function index_stuffing()
    {
        $first = date('Y-m-01');
        $now = date('Y-m-d');

        $flos = Flo::orderBy('flo_number', 'asc')
            ->where('status', '=', '2')
            ->get();

        // $container_schedules = ContainerSchedule::orderBy('shipment_date', 'asc')
        // ->where('shipment_date', '>=', $first)
        // ->where('shipment_date', '<=', $now)
        // ->where('shipment_date', '>=', DB::raw('DATE_FORMAT(now(), "%Y-%m-%d")'))
        // ->where('shipment_date', '<=', DB::raw('last_day(now())'))
        // ->get();

        $container_schedules = MasterChecksheet::whereNull('status')->leftJoin('shipment_conditions', 'shipment_conditions.shipment_condition_code', '=', 'master_checksheets.carier')->get();

        return view('flos.flo_stuffing', array(
            'flos' => $flos,
            'container_schedules' => $container_schedules,
        ))->with('page', 'FLO Stuffing');
    }

    public function index_shipment()
    {
        return view('flos.flo_shipment')->with('page', 'FLO Shipment');
    }

    public function index_lading()
    {

        $query = "select distinct invoice_number from
		(select invoice_number from flos where bl_date is null
		union all
		select invoice_number from knock_downs where bl_date is null) as invoice where invoice_number is not null";

        $invoices = DB::select($query);

        $destinations = db::table('destinations')->whereNull('deleted_at')->get();

        return view('flos.flo_lading', array(
            'invoices' => $invoices,
            'destinations' => $destinations,
        ))->with('page', 'FLO Lading');
    }

    public function index_deletion()
    {
        return view('flos.flo_deletion')->with('page', 'FLO Deletion');
    }

    public function index_detail()
    {

        $materials = DB::table('materials')->select('material_number', 'material_description')->get();
        $origin_groups = DB::table('origin_groups')->select('origin_groups.origin_group_code', 'origin_groups.origin_group_name')->get();
        $flos = DB::table('flos')->whereIn('flos.status', ['0', '1', 'M', '2'])->select('flos.flo_number')->distinct()->get();
        $statuses = DB::table('statuses')->select('statuses.status_code', 'statuses.status_name')->get();
        $flo_details = [];

        return view('flos.flo_detail', array(
            'materials' => $materials,
            'origin_groups' => $origin_groups,
            'flos' => $flos,
            'statuses' => $statuses,
            'flo_details' => $flo_details,
        ))->with('page', 'FLO Detail');
    }

    public function destroy_flo_attachment(Request $request)
    {
        $container_attachment = ContainerAttachment::where('file_name', '=', $request->get('id'))->first();
        $filepath = public_path() . $container_attachment->file_path . $container_attachment->file_name;
        File::delete($filepath);
        $container_attachment->forceDelete();

        $response = array(
            'status' => true,
            'message' => 'Photo has been deleted.',
        );
        return Response::json($response);
    }

    public function index_flo_invoice(Request $request)
    {

        $month = '';
        if (strlen($request->get('month')) > 0) {
            $month = $request->get('month');
        } else {
            $month = date('Y-m');
        }

        $query = "SELECT DISTINCT *
		FROM
		(
		SELECT
		flos.invoice_number,
		shipment_schedules.st_date,
		shipment_schedules.destination_code,
		destinations.destination_name,
		flos.bl_date AS actual_bl_date,
		shipment_schedules.bl_date AS bl_date
		FROM
		flos
		LEFT JOIN shipment_schedules ON shipment_schedules.id = flos.shipment_schedule_id
		LEFT JOIN destinations ON shipment_schedules.destination_code = destinations.destination_code
		WHERE
		flos.bl_date IS NOT NULL
		AND DATE_FORMAT(shipment_schedules.st_month,'%Y-%m') = '" . $month . "'
		UNION ALL
		SELECT
		knock_downs.invoice_number,
		shipment_schedules.st_date,
		shipment_schedules.destination_code,
		destinations.destination_name,
		knock_downs.bl_date AS actual_bl_date,
		shipment_schedules.bl_date AS bl_date
		FROM
		knock_downs
		LEFT JOIN knock_down_details ON knock_down_details.kd_number = knock_downs.kd_number
		LEFT JOIN shipment_schedules ON shipment_schedules.id = knock_down_details.shipment_schedule_id
		LEFT JOIN destinations ON shipment_schedules.destination_code = destinations.destination_code
		WHERE
		knock_downs.bl_date IS NOT NULL
		AND DATE_FORMAT(shipment_schedules.st_month,'%Y-%m') = '" . $month . "'
		ORDER BY
		bl_date DESC ) AS final";

        $invoices = db::select($query);

        return DataTables::of($invoices)
            ->addColumn('action', function ($invoices) {
                return '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" onClick="editConfirmation(id)" id="' . $invoices->invoice_number . '"><i class="fa fa-edit"></i></a>';
            })
            ->make(true);
    }

    public function filter_flo_detail(Request $request)
    {

        set_time_limit(0);
        ini_set('memory_limit', -1);
        ob_start();

        $flo_detailsTable = DB::table('flo_details')
            ->leftJoin('flos', 'flo_details.flo_number', '=', 'flos.flo_number')
            ->leftJoin('statuses', 'statuses.status_code', '=', 'flos.status')
            ->leftJoin('shipment_schedules', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
            ->leftJoin('materials', 'flos.material_number', '=', 'materials.material_number')
            ->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
            ->select('flo_details.id', 'shipment_schedules.sales_order', 'flo_details.flo_number', db::raw('date_format(shipment_schedules.st_date, "%d-%b-%Y") as st_date'), 'destinations.destination_shortname', 'materials.material_number', 'materials.material_description', 'flo_details.serial_number', 'flo_details.quantity', 'flo_details.created_at', 'statuses.status_name');

        if (strlen($request->get('datefrom')) > 0) {
            $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
            $flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(flo_details.created_at, "%Y-%m-%d")'), '>=', $date_from);
        }

        if (strlen($request->get('dateto')) > 0) {
            $date_to = date('Y-m-d', strtotime($request->get('dateto')));
            $flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(flo_details.created_at, "%Y-%m-%d")'), '<=', $date_to);
        }

        if (strlen($request->get('origin_group')) > 0) {
            $flo_detailsTable = $flo_detailsTable->where('materials.origin_group_code', '=', $request->get('origin_group'));
        }

        if (strlen($request->get('material_number')) > 0) {
            $flo_detailsTable = $flo_detailsTable->where('flo_details.material_number', '=', $request->get('material_number'));
        }

        if (strlen($request->get('serial_number')) > 0) {
            $flo_detailsTable = $flo_detailsTable->where('shipment_schedules.serial_number', '=', $request->get('serial_number'));
        }

        if (strlen($request->get('flo_number')) > 0) {
            $flo_detailsTable = $flo_detailsTable->where('flo_details.flo_number', '=', $request->get('flo_number'));
        }

        if (strlen($request->get('status')) > 0) {
            $flo_detailsTable = $flo_detailsTable->where('flos.status', '=', $request->get('status'));
        }

        $flo_details = $flo_detailsTable->orderBy('flo_details.created_at', 'desc')->get();

        $materials = DB::table('materials')->select('material_number', 'material_description')->get();
        $origin_groups = DB::table('origin_groups')->select('origin_groups.origin_group_code', 'origin_groups.origin_group_name')->get();
        $flos = DB::table('flos')->whereIn('flos.status', ['0', '1', 'M', '2'])->select('flos.flo_number')->distinct()->get();
        $statuses = DB::table('statuses')->select('statuses.status_code', 'statuses.status_name')->get();

        ob_end_flush();
        ob_flush();
        flush();

        return view('flos.flo_detail', array(
            'materials' => $materials,
            'origin_groups' => $origin_groups,
            'flos' => $flos,
            'statuses' => $statuses,
            'flo_details' => $flo_details,
        ))->with('page', 'FLO Detail');

    }

    public function index_flo_detail(Request $request)
    {
        $flo_details = DB::table('flo_details')
            ->leftJoin('flos', 'flo_details.flo_number', '=', 'flos.flo_number')
            ->leftJoin('shipment_schedules', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
            ->leftJoin('materials', 'shipment_schedules.material_number', '=', 'materials.material_number')
            ->where('flo_details.flo_number', '=', $request->get('flo_number'))
            ->where('flos.status', '=', '0')
            ->select('shipment_schedules.material_number', 'materials.material_description', 'flo_details.serial_number', 'flo_details.id', 'flo_details.quantity')
            ->orderBy('flo_details.id', 'DESC')
            ->get();

        return DataTables::of($flo_details)
            ->addColumn('action', function ($flo_details) {
                return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteConfirmation(id)" id="' . $flo_details->id . '"><i class="glyphicon glyphicon-trash"></i></a>';
            })
            ->make(true);
    }

    public function index_flo(Request $request)
    {
        $flos = DB::table('flos')
            ->leftJoin('shipment_schedules', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
            ->leftJoin('materials', 'shipment_schedules.material_number', '=', 'materials.material_number')
            ->leftJoin('shipment_conditions', 'shipment_schedules.shipment_condition_code', '=', 'shipment_conditions.shipment_condition_code')
            ->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
            ->leftJoin('flo_logs', 'flo_logs.flo_number', '=', 'flos.flo_number')
            ->where('flos.status', '=', $request->get('status'))
            ->where('flo_logs.status_code', '=', $request->get('status'))
            ->whereNull('flos.bl_date');

        if (!empty($request->get('originGroup'))) {
            $flos = $flos->whereIn('materials.origin_group_code', $request->get('originGroup'));
        }

        $flos = $flos->select('flos.flo_number', 'destinations.destination_shortname', 'shipment_schedules.st_date', 'shipment_conditions.shipment_condition_name', 'materials.material_number', 'materials.material_description', 'flos.actual', 'flos.id', 'flos.invoice_number', 'flos.invoice_number', 'flos.container_id', 'flo_logs.updated_at')
            ->get();

        return DataTables::of($flos)
            ->addColumn('action', function ($flos) {
                return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="cancelConfirmation(id)" id="' . $flos->id . '"><i class="glyphicon glyphicon-remove-sign"></i></a>';
            })
            ->make(true);
    }

    public function index_flo_container(Request $request)
    {

        $invoices = ShipmentSchedule::leftJoin('flos', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
            ->leftJoin('master_checksheets', 'master_checksheets.id_checkSheet', '=', 'flos.container_id')
            ->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
            ->select('shipment_schedules.st_date', 'destinations.destination_shortname', 'flos.container_id', 'master_checksheets.countainer_number')
            ->whereNotNull('flos.invoice_number')
            ->groupBy('shipment_schedules.st_date', 'destinations.destination_shortname', 'flos.container_id', 'master_checksheets.countainer_number')
            ->orderBy('shipment_schedules.st_date', 'desc')
            ->get();

        return DataTables::of($invoices)
            ->addColumn('action', function ($invoices) {return '<center><a href="javascript:void(0)" class="btn btn-success" data-toggle="modal" onClick="updateConfirmation(id)" id="' . $invoices->container_id . '"><i class="fa fa-upload"></i></a></center>';})
            ->make(true);
    }

    public function fetch_flo_open(Request $request)
    {

        $hpl = '';
        if ($request->get('hpl') != null) {
            $hpls = $request->get('hpl');
            for ($i = 0; $i < count($hpls); $i++) {
                $hpl = $hpl . "'" . $hpls[$i] . "'";
                if ($i != (count($hpls) - 1)) {
                    $hpl = $hpl . ',';
                }
            }
            $hpl = "AND m.hpl IN (" . $hpl . ") ";
        }

        $data = db::select("SELECT st.st_date,
			st.destination_shortname,
			st.hpl,
			st.material_number,
			st.material_description,
			st.quantity,
			st.quantity - COALESCE ( flo.actual, 0 ) AS target,
			COALESCE ( prod.actual, 0 ) AS prod,
			COALESCE ( intransit.actual, 0 ) AS intransit,
			COALESCE ( fstk.actual, 0 ) AS fstk,
			(COALESCE ( prod.actual, 0 ) + COALESCE ( intransit.actual, 0 ) + COALESCE ( fstk.actual, 0 )) - st.quantity AS diff
			FROM
			(SELECT st.id, st.st_date, d.destination_shortname, m.hpl, st.material_number, m.material_description, st.quantity FROM shipment_schedules st
			LEFT JOIN materials m ON m.material_number = st.material_number
			LEFT JOIN destinations d ON d.destination_code = st.destination_code
			WHERE m.category = 'FG'
			" . $hpl . " ) AS st
			LEFT JOIN
			(SELECT shipment_schedule_id, SUM(actual) AS actual FROM flos
			GROUP BY shipment_schedule_id) flo
			ON st.id = flo.shipment_schedule_id
			LEFT JOIN
			(SELECT shipment_schedule_id, SUM(actual) AS actual FROM flos
			WHERE flos.`status` = '0'
			GROUP BY shipment_schedule_id) prod
			ON st.id = prod.shipment_schedule_id
			LEFT JOIN
			(SELECT shipment_schedule_id, SUM(actual) AS actual FROM flos
			WHERE flos.`status` = '1'
			GROUP BY shipment_schedule_id) intransit
			ON st.id = intransit.shipment_schedule_id
			LEFT JOIN
			(SELECT shipment_schedule_id, SUM(actual) AS actual FROM flos
			WHERE flos.`status` = '2'
			GROUP BY shipment_schedule_id) fstk
			ON st.id = fstk.shipment_schedule_id
			HAVING target > 0 AND diff < 0
			ORDER BY st.st_date ASC, diff ASC");

        return DataTables::of($data)->make(true);

    }

    public function fetch_flo_lading(Request $request)
    {
        $invoice_number = $request->input('id');

        $flo = Flo::where('invoice_number', '=', $invoice_number)->first();

        if ($flo == null) {
            $flo = KnockDown::where('invoice_number', '=', $invoice_number)->first();
        }

        $bl_date = date('m/d/Y', strtotime($flo->bl_date));
        $response = array(
            'status' => true,
            'invoice_number' => $invoice_number,
            'bl_date' => $bl_date,
        );
        return Response::json($response);
    }

    public function input_flo_lading(Request $request)
    {
        $bl_date = date('Y-m-d', strtotime($request->get('bl_date')));
        $destination_shortname = $request->get('destination_shortname');
        $id = Auth::id();
        $checksheet = MasterChecksheet::where('invoice', 'like', '%' . $request->get('invoice_number') . '%')->first();

        DB::beginTransaction();
        try {
            $flos = Flo::where('invoice_number', '=', $request->get('invoice_number'))
                ->update([
                    'bl_date' => $bl_date,
                    'status' => 4,
                ]);

            $kd = KnockDown::where('invoice_number', '=', $request->get('invoice_number'))
                ->update([
                    'bl_date' => $bl_date,
                    'status' => 4,
                ]);

            if ($checksheet) {
                $booking = ShipmentReservation::where('period', $checksheet->period)
                    ->where('ycj_ref_number', $checksheet->ycj_ref_number)
                    ->where('status', 'BOOKING CONFIRMED')
                    ->update([
                        'actual_departed' => $bl_date,
                    ]);
            }

            $delete = db::table('sales_resumes')
                ->where('invoice_number', $request->get('invoice_number'))
                ->delete();

            $resume = db::select("SELECT sales.*, price.price FROM
				(SELECT flos.material_number, m.material_description, m.hpl AS category, IF(d.destination_shortname = 'YMID', d.destination_shortname, 'ALL') AS price_category, flos.invoice_number, flos.bl_date, SUM(flos.actual) AS quantity FROM flos
				LEFT JOIN destinations d ON flos.destination_code = d.destination_code
				LEFT JOIN materials m ON m.material_number = flos.material_number
				WHERE flos.invoice_number = '" . $request->get('invoice_number') . "'
				GROUP BY flos.material_number, m.material_description, m.hpl, price_category, flos.invoice_number, flos.bl_date
				) AS sales
				LEFT JOIN
				(SELECT price.material_number, price.sales_category, price.currency, price.price AS original_price, ex.idr_to_usd, IF(price.sales_category = 'YMID', price.price * ex.idr_to_usd, price.price) AS price FROM sales_prices_bk AS price
				LEFT JOIN sales_exchange_rates AS ex ON price.`month` = ex.period
				WHERE price.`month` LIKE '%" . substr($bl_date, 0, 7) . "%'
				) AS price
				ON sales.material_number = price.material_number AND price.sales_category = sales.price_category

				UNION ALL

				SELECT sales_kd.*, price.price FROM
				(SELECT knock_down_details.material_number, materials.material_description, materials.category, IF(destinations.destination_shortname = 'YMID', destinations.destination_shortname, 'ALL') AS price_category, knock_downs.invoice_number, knock_downs.bl_date, SUM(knock_down_details.quantity) AS quantity FROM knock_down_details
				LEFT JOIN knock_downs ON knock_down_details.kd_number = knock_downs.kd_number
				LEFT JOIN materials ON knock_down_details.material_number = materials.material_number
				LEFT JOIN master_checksheets ON knock_downs.container_id = master_checksheets.id_checkSheet
				LEFT JOIN destinations ON destinations.destination_code = master_checksheets.destination_code
				WHERE knock_downs.invoice_number = '" . $request->get('invoice_number') . "'
				GROUP BY knock_down_details.material_number, materials.material_description, materials.category, price_category, knock_downs.invoice_number, knock_downs.bl_date
				) AS sales_kd
				LEFT JOIN
				(SELECT price.material_number, price.sales_category, price.currency, price.price AS original_price, ex.idr_to_usd, IF(price.sales_category = 'YMID', price.price * ex.idr_to_usd, price.price) AS price FROM sales_prices_bk AS price
				LEFT JOIN sales_exchange_rates AS ex ON price.`month` = ex.period
				WHERE price.`month` LIKE '%" . substr($bl_date, 0, 7) . "%'
				) AS price
				ON sales_kd.material_number = price.material_number AND price.sales_category = sales_kd.price_category");

            for ($i = 0; $i < count($resume); $i++) {
                $insert = DB::table('sales_resumes')
                    ->insert([
                        'material_number' => $resume[$i]->material_number,
                        'material_description' => $resume[$i]->material_description,
                        'category' => $resume[$i]->category,
                        'price_category' => $resume[$i]->price_category,
                        'invoice_number' => $resume[$i]->invoice_number,
                        'destination_shortname' => $destination_shortname,
                        'bl_date' => $bl_date,
                        'quantity' => $resume[$i]->quantity,
                        'price' => $resume[$i]->price,
                        'created_by' => $id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

        } catch (QueryException $e) {
            DB::rollback();

            $error_log = new ErrorLog([
                'error_message' => $e->getMessage(),
                'created_by' => $id,
            ]);
            $error_log->save();
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        DB::commit();
        $response = array(
            'status' => true,
            'message' => 'BL date for invoice number "' . $request->get('invoice_number') . '" has been updated',
        );
        return Response::json($response);
    }

    public function fetch_flo_container(Request $request)
    {
        $container_id = $request->input('id');
        // $container_schedule = ContainerSchedule::where('container_id', '=', $container_id)->first();

        $before_attachments = ContainerAttachment::where('container_id', '=', $container_id)
            ->where('file_path', 'like', '%before%')
            ->get();
        $process_attachments = ContainerAttachment::where('container_id', '=', $container_id)
            ->where('file_path', 'like', '%process%')
            ->get();
        $after_attachments = ContainerAttachment::where('container_id', '=', $container_id)
            ->where('file_path', 'like', '%after%')
            ->get();
        $file_before[] = "";
        $file_process[] = "";
        $file_after[] = "";
        foreach ($before_attachments as $before_attachment) {
            $file_before[] = asset($before_attachment->file_path . $before_attachment->file_name);
        }
        foreach ($process_attachments as $process_attachment) {
            $file_process[] = asset($process_attachment->file_path . $process_attachment->file_name);
        }
        foreach ($after_attachments as $after_attachment) {
            $file_after[] = asset($after_attachment->file_path . $after_attachment->file_name);
        }

        $response = array(
            'status' => true,
            'container_id' => $container_id,
            // 'container_number' => $container_schedule->container_number,
            'file_before' => $file_before,
            'file_process' => $file_process,
            'file_after' => $file_after,
        );
        return Response::json($response);
    }

    public function update_flo_container(Request $request)
    {

        $id = Auth::id();

        $checks = db::table('flos')->whereRaw('shipment_schedule_id in (select shipment_schedule_id from flos where container_id = "' . $request->get('container_id') . '")')
            ->whereNull('container_id')
            ->select('flos.flo_number')
            ->groupBy('flos.flo_number')
            ->get();

        // if($request->get('container_number') != ""){
        //     $container_schedule = ContainerSchedule::where('container_id', '=', $request->get('container_id'))->first();
        //     $container_number = $container_schedule->container_number;
        //     $container_schedule->container_number = $request->get('container_number');
        //     $container_schedule->save();
        // }

        if ($request->hasFile('container_before')) {
            $files = $request->file('container_before');
            foreach ($files as $file) {
                $data = file_get_contents($file);
                $code_generator = CodeGenerator::where('note', '=', 'container_att')->first();
                $number = sprintf("%'.0" . $code_generator->length . "d\n", $code_generator->index) + 1;
                $photo_number = "B" . $number;
                $ext = $file->getClientOriginalExtension();
                $filepath = public_path() . "/uploads/containers/before/" . $photo_number . "." . $ext;
                $attachment = new ContainerAttachment([
                    'container_id' => $request->get('container_id'),
                    'file_name' => $photo_number . "." . $ext,
                    'file_path' => "/uploads/containers/before/",
                    'created_by' => $id,
                ]);
                $attachment->save();
                File::put($filepath, $data);
                $code_generator->index = $code_generator->index + 1;
                $code_generator->save();
            }
        }

        if ($request->hasFile('container_process')) {
            $files = $request->file('container_process');
            foreach ($files as $file) {
                $data = file_get_contents($file);
                $code_generator = CodeGenerator::where('note', '=', 'container_att')->first();
                $number = sprintf("%'.0" . $code_generator->length . "d\n", $code_generator->index) + 1;
                $photo_number = "P" . $number;
                $ext = $file->getClientOriginalExtension();
                $filepath = public_path() . "/uploads/containers/process/" . $photo_number . "." . $ext;
                $attachment = new ContainerAttachment([
                    'container_id' => $request->get('container_id'),
                    'file_name' => $photo_number . "." . $ext,
                    'file_path' => "/uploads/containers/process/",
                    'created_by' => $id,
                ]);
                $attachment->save();
                File::put($filepath, $data);
                $code_generator->index = $code_generator->index + 1;
                $code_generator->save();
            }
        }

        if ($request->hasFile('container_after')) {
            $files = $request->file('container_after');
            foreach ($files as $file) {
                $data = file_get_contents($file);
                $code_generator = CodeGenerator::where('note', '=', 'container_att')->first();
                $number = sprintf("%'.0" . $code_generator->length . "d\n", $code_generator->index) + 1;
                $photo_number = "A" . $number;
                $ext = $file->getClientOriginalExtension();
                $filepath = public_path() . "/uploads/containers/after/" . $photo_number . "." . $ext;
                $attachment = new ContainerAttachment([
                    'container_id' => $request->get('container_id'),
                    'file_name' => $photo_number . "." . $ext,
                    'file_path' => "/uploads/containers/after/",
                    'created_by' => $id,
                ]);
                $attachment->save();
                File::put($filepath, $data);
                $code_generator->index = $code_generator->index + 1;
                $code_generator->save();
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Container data has been updated',
        ]);
    }

    public function scan_material_number(Request $request)
    {

        if ($request->get('ymj') == 'false') {
            $destination = "<>";
        } else {
            $destination = "=";
        }

        $flo = db::table('flos')->where('material_number', '=', $request->get('material_number'))
            ->where('destination_code', $destination, 'Y1000XJ')
            ->where('status', '=', 0)
            ->where('quantity', '>', db::raw('actual'))
            ->first();

        if ($flo != null) {
            $response = array(
                'status' => true,
                'message' => 'Open FLO available',
                'flo_number' => $flo->flo_number,
                'status_code' => 1000,
            );
            return Response::json($response);
        }

        $query2 = "select schedule_check.id, sum(plan) as plan, sum(flo) as flo from
		(
		select id as id, quantity as plan, 0 as flo from shipment_schedules where material_number = '" . $request->get('material_number') . "'

		union all

		select shipment_schedule_id as id, 0 as plan, sum(actual) as flo from flos where material_number = '" . $request->get('material_number') . "' group by shipment_schedule_id
		) as schedule_check left join shipment_schedules on shipment_schedules.id = schedule_check.id where shipment_schedules.destination_code " . $destination . " 'Y1000XJ'
		group by schedule_check.id having flo < plan";

        $shipment_schedules = db::select($query2);

        if ($shipment_schedules != null) {
            $response = array(
                'status' => true,
                'message' => 'Shipment schedule available',
                'flo_number' => '',
                'status_code' => 1001,
            );
            return Response::json($response);
        }

        $response = array(
            'status' => false,
            'message' => 'There is no shipment schedule for ' . $request->get('material_number') . ' yet.',
        );
        return Response::json($response);

    }

    public function scan_educational_instrument(Request $request)
    {
        $id = Auth::id();
        $material = Material::where('material_number', '=', $request->get('material_number'))->first();
        $material_volume = MaterialVolume::where('material_number', '=', $request->get('material_number'))->first();

        $query2 = "select schedule_check.id, sum(plan) as plan, sum(flo) as flo from
		(
		select id as id, quantity as plan, 0 as flo from shipment_schedules where material_number = '" . $request->get('material_number') . "'

		union all

		select shipment_schedule_id as id, 0 as plan, sum(actual) as flo from flos where material_number = '" . $request->get('material_number') . "' group by shipment_schedule_id
		) as schedule_check left join shipment_schedules on shipment_schedules.id = schedule_check.id
		group by schedule_check.id having flo < plan";

        $shipment_schedules = db::select($query2);

        if ($shipment_schedules == null) {
            $response = array(
                'status' => false,
                'message' => 'There is no shipment schedule for ' . $request->get('material_number') . ' yet.',
            );
            return Response::json($response);
        }

        $prefix_now_pd = date("y") . date("m") . date("d");
        $code_generator_pd = CodeGenerator::where('note', '=', 'pd')->first();
        if ($prefix_now_pd != $code_generator_pd->prefix) {
            $code_generator_pd->prefix = $prefix_now_pd;
            $code_generator_pd->index = '0';
            $code_generator_pd->save();
        }
        $number_pd = sprintf("%'.0" . $code_generator_pd->length . "d\n", $code_generator_pd->index);
        $serial_number = $code_generator_pd->prefix . $number_pd + 1;

        $flo = Flo::where('material_number', '=', $request->get('material_number'))
            ->where('status', '=', 0)
            ->where('quantity', '>', db::raw('actual'))
            ->first();

        if ($flo != null) {
            try {
                $flo->actual = $flo->actual + $material_volume->lot_completion;

                $inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $material->material_number, 'storage_location' => $material->issue_storage_location]);
                $inventory->quantity = ($inventory->quantity + $material_volume->lot_completion);

                $flo_detail = new FloDetail([
                    'serial_number' => $serial_number,
                    'material_number' => $request->get('material_number'),
                    'origin_group_code' => $material->origin_group_code,
                    'flo_number' => $flo->flo_number,
                    'quantity' => $material_volume->lot_completion,
                    'created_by' => $id,
                ]);

                DB::transaction(function () use ($flo_detail, $inventory, $flo) {
                    $flo_detail->save();
                    $inventory->save();
                    $flo->save();
                });
                $status = 'open';
                $flo_number = $flo->flo_number;

                // YMES COMPLETION NEW

                $category = 'production_result';
                $function = 'scan_educational_instrument';
                $action = 'production_result';
                $result_date = date('Y-m-d H:i:s');
                $slip_number = $flo_number;
                $serial_number = $serial_number;
                $material_number = $request->get('material_number');
                $material_description = $material->material_description;
                $issue_location = $material->issue_storage_location;
                $mstation = 'W' . $material->mrpc . 'S10';
                $quantity = $material_volume->lot_completion;
                $remark = 'MIRAI';
                $created_by = Auth::user()->username;
                $created_by_name = Auth::user()->name;
                $synced = null;
                $synced_by = null;

                app(YMESController::class)->production_result(
                    $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                // YMES END
            } catch (QueryException $e) {
                $error_log = new ErrorLog([
                    'error_message' => $e->getMessage(),
                    'created_by' => $id,
                ]);
                $error_log->save();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        } else {
            $query2 = "select schedule_check.id, shipment_schedules.st_date, destinations.destination_shortname, shipment_conditions.shipment_condition_name, shipment_schedules.destination_code, sum(plan) as plan, sum(flo) as flo from
			(
			select id as id, quantity as plan, 0 as flo from shipment_schedules where material_number = '" . $request->get('material_number') . "'

			union all

			select shipment_schedule_id as id, 0 as plan, sum(actual) as flo from flos where material_number = '" . $request->get('material_number') . "' group by shipment_schedule_id
			) as schedule_check left join shipment_schedules on shipment_schedules.id = schedule_check.id left join destinations on destinations.destination_code = shipment_schedules.destination_code left join shipment_conditions on shipment_conditions.shipment_condition_code = shipment_schedules.shipment_condition_code
			group by schedule_check.id, shipment_schedules.st_date, destinations.destination_shortname, shipment_conditions.shipment_condition_name, shipment_schedules.destination_code having flo < plan order by shipment_schedules.st_date asc";

            $shipment_schedule = db::select($query2);

            if (($shipment_schedule[0]->plan - $shipment_schedule[0]->flo) > $material_volume->lot_flo) {
                $max_flo = $material_volume->lot_flo;
            } else {
                $max_flo = $shipment_schedule[0]->plan - $shipment_schedule[0]->flo;
            }

            $prefix_now = date("y") . date("m");
            $code_generator = CodeGenerator::where('note', '=', 'flo')->first();
            if ($prefix_now != $code_generator->prefix) {
                $code_generator->prefix = $prefix_now;
                $code_generator->index = '0';
                $code_generator->save();
            }

            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $flo_number = $code_generator->prefix . $number;
            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $flo_detail = new FloDetail([
                'serial_number' => $serial_number,
                'material_number' => $request->get('material_number'),
                'origin_group_code' => $material->origin_group_code,
                'flo_number' => $flo_number,
                'quantity' => $material_volume->lot_completion,
                'created_by' => $id,
            ]);

            $flo = new Flo([
                'flo_number' => $flo_number,
                'shipment_schedule_id' => $shipment_schedule[0]->id,
                'material_number' => $request->get('material_number'),
                'quantity' => $max_flo,
                'actual' => $material_volume->lot_completion,
                'destination_code' => $shipment_schedule[0]->destination_code,
                'created_by' => $id,
            ]);

            $inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $material->material_number, 'storage_location' => $material->issue_storage_location]);
            $inventory->quantity = ($inventory->quantity + $material_volume->lot_completion);

            try {
                DB::transaction(function () use ($flo_detail, $inventory, $flo) {
                    $flo_detail->save();
                    $flo->save();
                    $inventory->save();
                });

                // YMES COMPLETION NEW
                $category = 'production_result';
                $function = 'scan_educational_instrument';
                $action = 'production_result';
                $result_date = date('Y-m-d H:i:s');
                $slip_number = $flo_number;
                $serial_number = $serial_number;
                $material_number = $request->get('material_number');
                $material_description = $material->material_description;
                $issue_location = $material->issue_storage_location;
                $mstation = 'W' . $material->mrpc . 'S10';
                $quantity = $material_volume->lot_completion;
                $remark = 'MIRAI';
                $created_by = Auth::user()->username;
                $created_by_name = Auth::user()->name;
                $synced = null;
                $synced_by = null;

                app(YMESController::class)->production_result(
                    $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                // YMES END

                $flo_log = FloLog::updateOrCreate(
                    ['flo_number' => $flo_number, 'status_code' => '0'],
                    ['flo_number' => $flo_number, 'created_by' => $id, 'status_code' => '0', 'updated_at' => Carbon::now()]
                );

                $status = 'new';

                self::printFLO($flo_number, $shipment_schedule[0]->destination_shortname, $shipment_schedule[0]->st_date, $shipment_schedule[0]->shipment_condition_name, $request->get('material_number'), $material->material_description, $max_flo, 0, []);

            } catch (QueryException $e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }

        $code_generator_pd->index = $code_generator_pd->index + 1;
        $code_generator_pd->save();

        //DIGITALISASI INJEKSI
        if ($material->origin_group_code == '072' && $material->category == 'FG') {
            try {
                $update_stock = db::select("UPDATE injection_inventories AS ii
					LEFT JOIN injection_part_details AS ipd ON ipd.gmc = ii.material_number
					SET ii.quantity = ii.quantity - " . $material_volume->lot_completion . ", ii.updated_at = '" . date('Y-m-d H:i:s') . "'
					WHERE
					ipd.model = '" . $material->model . "'
					AND ii.location = '" . $material->issue_storage_location . "'");
            } catch (QueryException $e) {
                $error_log = new ErrorLog([
                    'error_message' => $e->getMessage(),
                    'created_by' => $id,
                ]);
                $error_log->save();
            }
        }

        $response = array(
            'status' => true,
            'message' => 'Scan FLO berhasil dilakukan.',
            'status_code' => $status,
            'flo_number' => $flo_number,
        );
        return Response::json($response);

    }

    public function scan_serial_number(Request $request)
    {
        $id = Auth::id();
        $material = Material::where('material_number', '=', $request->get('material_number'))->first();
        $material_volume = MaterialVolume::where('material_number', '=', $request->get('material_number'))->first();
        $flo_number = $request->get('flo_number');

        // START CEK ITEM TRIAL
        if (in_array($material->hpl, ['ASFG']) && in_array($request->get('serial_number'), $this->sax_trial_serial_number)) {
            $response = array(
                'status' => false,
                'message' => 'Serial number ' . $request->get('serial_number') . ' termasuk FG trial onko hikiage dan tidak boleh diekspor. Untuk pencatatan production result gunakan menu MAEDAOSHI - Band Instrument',
            );
            return Response::json($response);
        }
        // END CEK ITEM TRIAL

        //flo ready
        DB::beginTransaction();
        $scan_flo_status = false;

        if ($request->get('flo_number') != "") {
            try {
                $flo_number = $request->get('flo_number');
                $flo = Flo::where('flo_number', '=', $request->get('flo_number'))->first();
                $flo->actual = $flo->actual + $material_volume->lot_completion;

                $inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $material->material_number, 'storage_location' => $material->issue_storage_location]);
                $inventory->quantity = ($inventory->quantity + $material_volume->lot_completion);

                $flo_detail = new FloDetail([
                    'serial_number' => $request->get('serial_number'),
                    'material_number' => $request->get('material_number'),
                    'origin_group_code' => $material->origin_group_code,
                    'flo_number' => $request->get('flo_number'),
                    'quantity' => $material_volume->lot_completion,
                    'created_by' => $id,
                    'image' => $request->get('base64image'),
                ]);

                DB::transaction(function () use ($flo_detail, $inventory, $flo) {
                    $flo_detail->save();
                    $inventory->save();
                    $flo->save();
                });

                $status = 'open';
                $scan_flo_status = true;
                DB::commit();

            } catch (QueryException $e) {

                DB::rollback();
                $error_code = $e->getCode();
                if ($error_code == 1062 || $error_code == 23000) {
                    $message = "Serial number already exist.";
                } else {
                    $message = $e->getMessage();
                }
                $response = array(
                    'status' => false,
                    'message' => $message,
                );
                return Response::json($response);
            }
        } else {
            if (Auth::user()->role_code == "OP-Assy-FL") {
                $query2 = "select schedule_check.id, shipment_schedules.st_date, destinations.destination_shortname, shipment_conditions.shipment_condition_name, shipment_schedules.destination_code, sum(plan) as plan, sum(flo) as flo from
				(
				select id as id, quantity as plan, 0 as flo from shipment_schedules where material_number = '" . $request->get('material_number') . "'

				union all

				select shipment_schedule_id as id, 0 as plan, sum(actual) as flo from flos where material_number = '" . $request->get('material_number') . "' group by shipment_schedule_id
				) as schedule_check left join shipment_schedules on shipment_schedules.id = schedule_check.id left join destinations on destinations.destination_code = shipment_schedules.destination_code left join shipment_conditions on shipment_conditions.shipment_condition_code = shipment_schedules.shipment_condition_code
				group by schedule_check.id, shipment_schedules.st_date, destinations.destination_shortname, shipment_conditions.shipment_condition_name, shipment_schedules.destination_code having flo < plan order by shipment_schedules.st_date asc, shipment_schedules.id asc";

                $shipment_schedule = db::select($query2);
            } else {
                if ($request->get('ymj') == 'false') {
                    $destination = "<>";
                } else {
                    $destination = "=";
                }

                $query2 = "select schedule_check.id, shipment_schedules.st_date, shipment_schedules.destination_code, destinations.destination_shortname, shipment_conditions.shipment_condition_name, sum(plan) as plan, sum(flo) as flo from
				(
				select id as id, quantity as plan, 0 as flo from shipment_schedules where material_number = '" . $request->get('material_number') . "'

				union all

				select shipment_schedule_id as id, 0 as plan, sum(actual) as flo from flos where material_number = '" . $request->get('material_number') . "' group by shipment_schedule_id
				) as schedule_check left join shipment_schedules on shipment_schedules.id = schedule_check.id left join destinations on destinations.destination_code = shipment_schedules.destination_code left join shipment_conditions on shipment_conditions.shipment_condition_code = shipment_schedules.shipment_condition_code where shipment_schedules.destination_code " . $destination . " 'Y1000XJ'
				group by schedule_check.id, shipment_schedules.st_date, destinations.destination_shortname, shipment_conditions.shipment_condition_name, shipment_schedules.destination_code having flo < plan order by shipment_schedules.st_date asc, shipment_schedules.id asc";

                $shipment_schedule = db::select($query2);
            }

            if (($shipment_schedule[0]->plan - $shipment_schedule[0]->flo) > $material_volume->lot_flo) {
                $max_flo = $material_volume->lot_flo;
            } else {
                $max_flo = $shipment_schedule[0]->plan - $shipment_schedule[0]->flo;
            }

            $prefix_now = date("y") . date("m");
            $code_generator = CodeGenerator::where('note', '=', 'flo')->first();
            if ($prefix_now != $code_generator->prefix) {
                $code_generator->prefix = $prefix_now;
                $code_generator->index = '0';
                $code_generator->save();
            }

            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $flo_number = $code_generator->prefix . $number;
            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $flo_detail = new FloDetail([
                'serial_number' => $request->get('serial_number'),
                'material_number' => $request->get('material_number'),
                'origin_group_code' => $material->origin_group_code,
                'flo_number' => $flo_number,
                'quantity' => $material_volume->lot_completion,
                'created_by' => $id,
                'image' => $request->get('base64image'),
            ]);

            $flo = new Flo([
                'flo_number' => $flo_number,
                'shipment_schedule_id' => $shipment_schedule[0]->id,
                'material_number' => $request->get('material_number'),
                'quantity' => $max_flo,
                'actual' => $material_volume->lot_completion,
                'destination_code' => $shipment_schedule[0]->destination_code,
                'created_by' => $id,
            ]);

            $inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $material->material_number, 'storage_location' => $material->issue_storage_location]);
            $inventory->quantity = ($inventory->quantity + $material_volume->lot_completion);

            try {
                DB::transaction(function () use ($flo_detail, $inventory, $flo) {
                    $flo_detail->save();
                    $flo->save();
                    $inventory->save();
                });

                $flo_log = FloLog::updateOrCreate(
                    ['flo_number' => $flo_number, 'status_code' => '0'],
                    ['flo_number' => $flo_number, 'created_by' => $id, 'status_code' => '0', 'updated_at' => Carbon::now()]
                );

                self::printFLO($flo_number, $shipment_schedule[0]->destination_shortname, $shipment_schedule[0]->st_date, $shipment_schedule[0]->shipment_condition_name, $request->get('material_number'), $material->material_description, $max_flo, 0, []);

                $status = 'new';
                $scan_flo_status = true;
                DB::commit();

            } catch (QueryException $e) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }

        try {
            if ($material->origin_group_code == '041') {
                $log_process = LogProcess::updateOrCreate(
                    [
                        'process_code' => '5',
                        'serial_number' => $request->get('serial_number'),
                        'origin_group_code' => $material->origin_group_code,
                    ],
                    [
                        'model' => $material->model,
                        'manpower' => 2,
                        'quantity' => $material_volume->lot_completion,
                        'created_by' => $id,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]
                );

                $inventory_stamp = StampInventory::where('serial_number', '=', $request->get('serial_number'))
                    ->where('origin_group_code', '=', $material->origin_group_code)
                    ->first();
                if ($inventory_stamp != null) {
                    $inventory_stamp->forceDelete();
                }
            }
            if ($material->origin_group_code == '043') {
                $inventory_stamp = StampInventory::where('serial_number', '=', $request->get('serial_number'))
                    ->where('origin_group_code', '=', '043')
                    ->first();
                if ($inventory_stamp != null) {
                    $inventory_stamp->forceDelete();
                }
            }
        } catch (QueryException $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        //DIGITALISASI INJEKSI
        if ($material->origin_group_code == '072' && $material->category == 'FG') {
            try {
                $update_stock = db::select("UPDATE injection_inventories AS ii
					LEFT JOIN injection_part_details AS ipd ON ipd.gmc = ii.material_number
					SET ii.quantity = ii.quantity - " . $material_volume->lot_completion . "
					WHERE
					ipd.model = '" . $material->model . "'
					AND ii.location = '" . $material->issue_storage_location . "'");
            } catch (QueryException $e) {
                $error_log = new ErrorLog([
                    'error_message' => $e->getMessage(),
                    'created_by' => $id,
                ]);
                $error_log->save();
            }
        }

        if ($scan_flo_status) {
            // BODY INOUT
            if ($material->inout_location != "" || $material->inout_location != null) {
                db::connection('ympimis_2')->table('kanban_inout_logs')->insert([
                    'tag' => $flo_number,
                    'material_number' => $material->material_number,
                    'material_description' => $material->material_description,
                    'issue_location' => $material->issue_storage_location,
                    'receive_location' => 'FSTK',
                    'quantity' => $material_volume->lot_completion,
                    'remark' => 'FA-PR',
                    'category' => 'EXPORT',
                    'transaction_by' => Auth::user()->username,
                    'transaction_by_name' => Auth::user()->name,
                    'created_by' => Auth::user()->username,
                    'created_by_name' => Auth::user()->name,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $boms = db::connection('ympimis_2')->table('kanban_inout_boms')
                    ->where('material_number', '=', $material->material_number)
                    ->first();

                foreach ($boms as $bom) {
                    $inventory_material = db::connection('ympimis_2')->table('kanban_inout_inventories')
                        ->where('material_number', '=', $bom->material_child)
                        ->where('location', '=', $material->inout_location . '-MATERIAL')
                        ->first();

                    if ($inventory_material) {
                        db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->where('material_number', '=', $bom->material_child)
                            ->where('location', '=', $material->inout_location . '-MATERIAL')
                            ->update([
                                'quantity' => $inventory_material->quantity - $material_volume->lot_completion,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    } else {
                        db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->insert([
                                'material_number' => $bom->material_child,
                                'material_description' => $bom->material_child_description,
                                'quantity' => $material_volume->lot_completion * -1,
                                'location' => $material->inout_location . '-MATERIAL',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    }
                }

            }

            // YMES COMPLETION NEW
            $category = 'production_result';
            $function = 'scan_serial_number';
            $action = 'production_result';
            $result_date = date('Y-m-d H:i:s');
            $slip_number = $flo_number;
            $serial_number = $request->get('serial_number');
            $material_number = $request->get('material_number');
            $material_description = $material->material_description;
            $issue_location = $material->issue_storage_location;
            $mstation = 'W' . $material->mrpc . 'S10';
            $quantity = $material_volume->lot_completion;
            $remark = 'MIRAI';
            $created_by = Auth::user()->username;
            $created_by_name = Auth::user()->name;
            $synced = null;
            $synced_by = null;

            app(YMESController::class)->production_result(
                $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
            // YMES END
        }

        $response = array(
            'status' => true,
            'message' => 'Scan FLO berhasil dilakukan.',
            'status_code' => $status,
        );
        return Response::json($response);
    }

    public function printFLO($flo_number, $destination_shortname, $st_date, $shipment_condition_name, $material_number, $material_description, $flo_quantity, $actual, $list)
    {
        if (Auth::user()->role_code == "OP-Assy-FL") {
            $printer_name = 'FLO Printer 101';
        } elseif (Auth::user()->role_code == "OP-Assy-CL") {
            $printer_name = 'FLO Printer 102';
        } elseif (Auth::user()->role_code == "OP-Assy-SX") {
            $printer_name = 'FLO Printer 103';
        } elseif (Auth::user()->role_code == "OP-Assy-PN" && Auth::user()->username == "assy-pn") {
            $printer_name = 'FLO Printer 104';
        } elseif (Auth::user()->role_code == "OP-Assy-PN" && Auth::user()->username == "assy-pn-2") {
            $printer_name = 'FLO Printer 105';
        } elseif (Auth::user()->role_code == "OP-Assy-RC") {
            $printer_name = 'FLO Printer RC';
        } elseif (Auth::user()->role_code == "OP-Assy-VN") {
            $printer_name = 'FLO Printer VN';
        } elseif (Auth::user()->role_code == "OP-WH-Exim" || str_contains(Auth::user()->role_code, 'LOG')) {
            $printer_name = 'FLO Printer LOG';
        } elseif (Auth::user()->role_code == "S-MIS") {
            $printer_name = 'MIS';
        } elseif (Auth::user()->role_code == "S") {
            $printer_name = 'MIS';
        } else {
            $printer_name = 'MIS';
        }

        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        $printer->feed(2);
        $printer->setUnderline(true);
        $printer->text('FLO:');
        $printer->setUnderline(false);
        $printer->feed(1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->barcode($flo_number, Printer::BARCODE_CODE39);
        $printer->setTextSize(3, 1);
        $printer->text($flo_number . "\n\n");
        $printer->initialize();

        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setUnderline(true);
        $printer->text('Destination:');
        $printer->setUnderline(false);
        $printer->feed(1);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(6, 3);
        $printer->text(strtoupper($destination_shortname . "\n\n"));
        $printer->initialize();

        $printer->setUnderline(true);
        $printer->text('Shipment Date:');
        $printer->setUnderline(false);
        $printer->feed(1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(4, 2);
        $printer->text(date('d-M-Y', strtotime($st_date)) . "\n\n");
        $printer->initialize();

        $printer->setUnderline(true);
        $printer->text('By:');
        $printer->setUnderline(false);
        $printer->feed(1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(4, 2);
        $printer->text(strtoupper($shipment_condition_name) . "\n\n");

        $printer->initialize();
        $printer->setTextSize(2, 2);
        $printer->text("   " . strtoupper($material_number) . "\n");
        $printer->text("   " . strtoupper($material_description) . "\n");

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
        $printer->text("Max Qty:" . $flo_quantity . "\n");
        if ($actual > 0) {
            $printer->text("Actual Qty:" . $actual . "\n");
            $printer->text($list . "\n");
        }
        $printer->cut();
        $printer->close();
    }

    public function reprint_flo(Request $request)
    {

        $flo = DB::table('flos')
            ->leftJoin('shipment_schedules', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
            ->leftJoin('shipment_conditions', 'shipment_schedules.shipment_condition_code', '=', 'shipment_conditions.shipment_condition_code')
            ->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
            ->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
            ->leftJoin('materials', 'shipment_schedules.material_number', '=', 'materials.material_number')
            ->where('flos.flo_number', '=', $request->get('flo_number_reprint'))
            ->whereNull('flos.bl_date')
            ->select('flos.flo_number', 'flos.quantity', 'flos.actual', 'destinations.destination_shortname', 'shipment_schedules.st_date', 'shipment_conditions.shipment_condition_name', 'shipment_schedules.material_number', 'materials.material_description', 'flos.status')
            ->first();

        $flo_details = DB::table('flo_details')->where('flo_number', '=', $request->get('flo_number_reprint'))->select('serial_number')->get();

        foreach ($flo_details as $flo_detail) {
            if ($flo_detail->serial_number != '') {
                $lists[] = $flo_detail->serial_number;
            }
        }
        $list = implode(', ', $lists);

        try {
            self::printFLO($flo->flo_number, $flo->destination_shortname, $flo->st_date, $flo->shipment_condition_name, $flo->material_number, $flo->material_description, $flo->quantity, $flo->actual, $list);

            return back()->with('status', 'FLO has been reprinted.')->with('page', 'FLO Band Instrument');
        } catch (QueryException $e) {
            return back()->with("error", "Couldn't print to this printer " . $e->getMessage() . "\n");
        }

    }

    public function destroy_serial_number(Request $request)
    {
        // $flo_detail = FloDetail::find($request->get('id'));
        // if($flo_detail->completion == null){
        //     $flo = Flo::where('flo_number', '=', $flo_detail->flo_number)->first();
        //     $actual = DB::table('flo_details')
        //     ->leftJoin('flos', 'flos.flo_number', '=', 'flo_details.flo_number')
        //     ->leftJoin('shipment_schedules', 'shipment_schedules.id' , '=', 'flos.shipment_schedule_id')
        //     ->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'shipment_schedules.material_number')
        //     ->leftJoin('materials', 'materials.material_number', '=', 'flo_details.material_number')
        //     ->where('flo_details.id', '=', $request->get('id'))
        //     ->select('material_volumes.lot_completion', 'materials.material_number', 'materials.issue_storage_location', 'materials.model', 'materials.category', 'materials.origin_group_code')
        //     ->first();

        //     $flo->actual = $flo->actual-$actual->lot_completion;
        //     $flo->save();

        //     $flo_detail->forceDelete();

        //     $inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $actual->material_number, 'storage_location' => $actual->issue_storage_location]);
        //     $inventory->quantity = ($inventory->quantity-$actual->lot_completion);
        //     $inventory->save();

        // //DIGITALISASI INJEKSI
        //     if($actual->origin_group_code == '072' && $actual->category == 'FG'){
        //         try{
        //             $update_stock = db::select("UPDATE injection_inventories AS ii
        //                 LEFT JOIN injection_part_details AS ipd ON ipd.gmc = ii.material_number
        //                 SET ii.quantity = ii.quantity + ".$actual->lot_completion."
        //                 WHERE
        //                 ipd.model = '".$actual->model."'
        //                 AND ii.location = '".$actual->issue_storage_location."'");
        //         }
        //         catch (QueryException $e){
        //             $error_log = new ErrorLog([
        //                 'error_message' => $e->getMessage(),
        //                 'created_by' => $id
        //             ]);
        //             $error_log->save();
        //         }
        //     }

        //     $response = array(
        //         'status' => true,
        //         'message' => "Data has been deleted.",
        //     );
        //     return Response::json($response);
        // }
        // else{
        $response = array(
            'status' => false,
            'message' => "Data cannot be deleted, because data has been uploaded to YMES.",
        );
        return Response::json($response);
        // }
    }

    public function cancel_flo_settlement(Request $request)
    {

        $id = Auth::id();
        $status = $request->get('status') - 1;
        $flo = Flo::where('id', '=', $request->get('id'))
            ->where('status', '=', $request->get('status'))
            ->first();

        if ($flo != null) {

            $flo->status = $status;

            if ($request->get('status') == '2') {
                $inventoryFSTK = Inventory::firstOrNew(['plant' => '8191', 'material_number' => $flo->shipmentschedule->material_number, 'storage_location' => 'FSTK']);
                $inventoryFSTK->quantity = ($inventoryFSTK->quantity - $flo->actual);
                $inventoryFSTK->save();

                $inventoryWIP = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $flo->shipmentschedule->material_number, 'storage_location' => $flo->shipmentschedule->material->issue_storage_location]);
                $inventoryWIP->quantity = ($inventoryWIP->quantity + $flo->actual);
                $inventoryWIP->save();

                $flo_details = FloDetail::where('flo_number', $flo->flo_number)
                    ->get();

                $flo_detail = FloDetail::where('flo_number', $flo->flo_number)
                    ->whereNotNull('transfer')
                    ->update([
                        'transfer' => null,
                    ]);
                if ($flo->transafer != null) {
                    $transaction_transfer = new TransactionTransfer([
                        'plant' => '8190',
                        'serial_number' => $flo->flo_number,
                        'material_number' => $flo->shipmentschedule->material_number,
                        'issue_plant' => '8190',
                        'issue_location' => $flo->shipmentschedule->material->issue_storage_location,
                        'receive_plant' => '8191',
                        'receive_location' => 'FSTK',
                        'transaction_code' => 'MB1B',
                        'movement_type' => '9P2',
                        'quantity' => $flo->actual,
                        'created_by' => $id,
                    ]);
                    $transaction_transfer->save();
                }
                foreach ($flo_details as $flo_det) {
                    // YMES CANCEL TRANSFER NEW
                    $category = 'goods_movement_cancel';
                    $function = 'cancel_flo_settlement';
                    $action = 'goods_movement';
                    $result_date = date('Y-m-d H:i:s');
                    $slip_number = $flo_det->flo_number;
                    $serial_number = $flo_det->serial_number;
                    $material_number = $flo_det->material_number;
                    $material_description = $flo->shipmentschedule->material->material_description;
                    $issue_location = 'FSTK';
                    $receive_location = $flo->shipmentschedule->material->issue_storage_location;
                    $quantity = $flo_det->quantity;
                    $remark = 'MIRAI';
                    $created_by = Auth::user()->username;
                    $created_by_name = Auth::user()->name;
                    $synced = null;
                    $synced_by = null;

                    app(YMESController::class)->goods_movement(
                        $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $receive_location, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                    // YMES END
                }

            }

            if ($request->get('status') == '3') {
                $inventory = Inventory::firstOrNew(['plant' => '8191', 'material_number' => $flo->shipmentschedule->material_number, 'storage_location' => 'FSTK']);
                $inventory->quantity = ($inventory->quantity + $flo->actual);
                $flo->invoice_number = null;
                $flo->container_id = null;
                $flo->bl_date = null;
                $inventory->save();
            }

            $flo->save();

            $response = array(
                'status' => true,
                'message' => "FLO " . $request->get('flo_number') . " settlement has been canceled.",
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'message' => "FLO " . $request->get('flo_number') . " not found or FLO " . $request->get('flo_number') . " status is invalid.",
            );
            return Response::json($response);
        }
    }

    public function flo_settlement(Request $request)
    {
        $id = Auth::id();
        $status = $request->get('status') - 1;
        $flo = Flo::where('flo_number', '=', $request->get('flo_number'))
            ->where('status', '=', $status)
            ->first();

        $closure = Flo::leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')
            ->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
            ->leftJoin('shipment_conditions', 'shipment_conditions.shipment_condition_code', '=', 'shipment_schedules.shipment_condition_code')
            ->leftJoin('materials', 'materials.material_number', '=', 'flos.material_number')
            ->where('flo_number', '=', $request->get('flo_number'))
            ->where('status', '=', $status)
            ->select('materials.material_number', 'flos.flo_number', 'destinations.destination_shortname', 'shipment_conditions.shipment_condition_name', 'materials.material_description', 'flos.actual', 'shipment_schedules.st_date')
            ->first();

        if ($flo != null) {

            $flo->status = $request->get('status');

            if ($request->get('status') == '3') {
                $checksheets = MasterChecksheet::leftJoin('detail_checksheets', 'detail_checksheets.id_checkSheet', '=', 'master_checksheets.id_checkSheet')
                    ->where('master_checksheets.id_checkSheet', '=', $request->get('container_id'))
                    ->where('detail_checksheets.gmc', '=', $flo->material_number)
                    ->first();

                if ($checksheets == null) {
                    $response = array(
                        'status' => false,
                        'message' => "Maaterial tidak ditemukan pada checksheet",
                    );
                    return Response::json($response);
                }

                $flo->invoice_number = $request->get('invoice_number');
                $flo->container_id = $request->get('container_id');
            }
            $flo->save();

            if ($request->get('status') == '2') {

                $flo_details = FloDetail::where('flo_number', '=', $request->get('flo_number'))->get();

                foreach ($flo_details as $flo_detail) {
                    // YMES TRANSFER NEW

                    $category = 'goods_movement';
                    $function = 'flo_settlement';
                    $action = 'goods_movement';
                    $result_date = date('Y-m-d H:i:s');
                    $slip_number = $request->get('flo_number');
                    $serial_number = $flo_detail->serial_number;
                    $material_number = $flo_detail->material_number;
                    $material_description = $flo->material->material_description;
                    $issue_location = $flo->material->issue_storage_location;
                    $receive_location = 'FSTK';
                    $quantity = $flo_detail->quantity;
                    $remark = 'MIRAI';
                    $created_by = Auth::user()->username;
                    $created_by_name = Auth::user()->name;
                    $synced = null;
                    $synced_by = null;

                    app(YMESController::class)->goods_movement(
                        $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $receive_location, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                    // YMES END
                }

                $inventoryFSTK = Inventory::firstOrNew(['plant' => '8191', 'material_number' => $flo->material_number, 'storage_location' => 'FSTK']);
                $inventoryFSTK->quantity = ($inventoryFSTK->quantity + $flo->actual);
                $inventoryFSTK->save();

                $inventoryWIP = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $flo->material_number, 'storage_location' => $flo->material->issue_storage_location]);
                $inventoryWIP->quantity = ($inventoryWIP->quantity - $flo->actual);
                $inventoryWIP->save();
            }

            if ($request->get('status') == '3') {
                $inventory = Inventory::firstOrNew(['plant' => '8191', 'material_number' => $flo->material_number, 'storage_location' => 'FSTK']);
                $inventory->quantity = ($inventory->quantity - $flo->actual);
                $inventory->save();
            }

            $flo_log = FloLog::updateOrCreate(
                ['flo_number' => $request->get('flo_number'), 'status_code' => $request->get('status')],
                ['created_by' => $id, 'status_code' => $request->get('status'), 'updated_at' => Carbon::now()]
            );

            if ($request->get('type') == 'bi') {
                if (Auth::user()->role_code == "OP-Assy-FL") {
                    $printer_name = 'FLO Printer 101';
                } elseif (Auth::user()->role_code == "OP-Assy-CL") {
                    $printer_name = 'FLO Printer 102';
                } elseif (Auth::user()->role_code == "OP-Assy-SX") {
                    $printer_name = 'FLO Printer 103';
                } elseif (Auth::user()->role_code == "OP-Assy-PN" && Auth::user()->username == "assy-pn") {
                    $printer_name = 'FLO Printer 104';
                } elseif (Auth::user()->role_code == "OP-Assy-PN" && Auth::user()->username == "assy-pn-2") {
                    $printer_name = 'FLO Printer 105';
                } elseif (Auth::user()->role_code == "OP-Assy-RC") {
                    $printer_name = 'FLO Printer RC';
                } elseif (Auth::user()->role_code == "OP-Assy-VN") {
                    $printer_name = 'FLO Printer VN';
                } elseif (Auth::user()->role_code == "S") {
                    $printer_name = 'MIS';
                } elseif (str_contains(Auth::user()->role_code, "MIS")) {
                    $printer_name = 'MIS';
                } elseif (Auth::user()->role_code == "OP-WH-Exim" || str_contains(Auth::user()->role_code, 'LOG')) {
                    $printer_name = 'FLO Printer LOG';
                }
                $flo_details = DB::table('flo_details')->where('flo_number', '=', $request->get('flo_number'))->select('serial_number')->get();

                foreach ($flo_details as $flo_detail) {
                    if ($flo_detail->serial_number != '') {
                        $lists[] = $flo_detail->serial_number;
                    }
                }
                $list = implode(', ', $lists);

                $connector = new WindowsPrintConnector($printer_name);
                $printer = new Printer($connector);

                $printer->feed(2);
                $printer->setUnderline(true);
                $printer->text('FLO:');
                $printer->setUnderline(false);
                $printer->feed(1);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->barcode(intVal($closure->flo_number), Printer::BARCODE_CODE39);
                $printer->setTextSize(3, 1);
                $printer->text($closure->flo_number . "\n\n");
                $printer->initialize();

                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->setUnderline(true);
                $printer->text('Destination:');
                $printer->setUnderline(false);
                $printer->feed(1);

                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->setTextSize(6, 3);
                $printer->text(strtoupper($closure->destination_shortname . "\n\n"));
                $printer->initialize();

                $printer->setUnderline(true);
                $printer->text('Shipment Date:');
                $printer->setUnderline(false);
                $printer->feed(1);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->setTextSize(4, 2);
                $printer->text(date('d-M-Y', strtotime($closure->st_date)) . "\n\n");
                $printer->initialize();

                $printer->setUnderline(true);
                $printer->text('By:');
                $printer->setUnderline(false);
                $printer->feed(1);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->setTextSize(4, 2);
                $printer->text(strtoupper($closure->shipment_condition_name) . "\n\n");

                $printer->initialize();
                $printer->setTextSize(2, 2);
                $printer->text("   " . strtoupper($closure->material_number) . "\n");
                $printer->text("   " . strtoupper($closure->material_description) . "\n");

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
                $printer->text("Qty:" . $closure->actual . "\n");
                $printer->text($list . "\n");
                $printer->initialize();
                $printer->cut();
                $printer->close();
            }

            $response = array(
                'status' => true,
                'message' => "FLO " . $request->get('flo_number') . " has been settled.",
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'message' => "FLO " . $request->get('flo_number') . " not found or FLO " . $request->get('flo_number') . " status is invalid.",
            );
            return Response::json($response);
        }
    }

    public function destroy_flo_deletion(Request $request)
    {
        $flo_detail = FloDetail::find($request->get('id'));
        $material = Material::where('material_number', '=', $flo_detail->material_number)->first();

        $id = Auth::id();

        // BODY INOUT
        if ($material->inout_location != "" || $material->inout_location != null) {
            db::connection('ympimis_2')->table('kanban_inout_logs')->insert([
                'tag' => $flo_number,
                'material_number' => $material->material_number,
                'material_description' => $material->material_description,
                'issue_location' => $material->issue_storage_location,
                'receive_location' => 'FSTK',
                'quantity' => $flo_detail->quantity,
                'remark' => 'FA-PR',
                'category' => 'EXPORT',
                'transaction_by' => Auth::user()->username,
                'transaction_by_name' => Auth::user()->name,
                'created_by' => Auth::user()->username,
                'created_by_name' => Auth::user()->name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $boms = db::connection('ympimis_2')->table('kanban_inout_boms')
                ->where('material_number', '=', $material->material_number)
                ->first();

            foreach ($boms as $bom) {
                $inventory_material = db::connection('ympimis_2')->table('kanban_inout_inventories')
                    ->where('material_number', '=', $bom->material_child)
                    ->where('location', '=', $material->inout_location . '-MATERIAL')
                    ->first();

                if ($inventory_material) {
                    db::connection('ympimis_2')->table('kanban_inout_inventories')
                        ->where('material_number', '=', $bom->material_child)
                        ->where('location', '=', $material->inout_location . '-MATERIAL')
                        ->update([
                            'quantity' => $inventory_material->quantity + $flo_detail->quantity,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                } else {
                    db::connection('ympimis_2')->table('kanban_inout_inventories')
                        ->insert([
                            'material_number' => $bom->material_child,
                            'material_description' => $bom->material_child_description,
                            'quantity' => $flo_detail->quantity,
                            'location' => $material->inout_location . '-MATERIAL',
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }
            }

        }

        // YMES CANCEL COMPLETION NEW
        $category = 'production_result_cancel';
        $function = 'destroy_flo_deletion';
        $action = 'production_result';
        $result_date = date('Y-m-d H:i:s');
        $slip_number = $flo_detail->flo_number;
        $serial_number = $flo_detail->serial_number;
        $material_number = $flo_detail->material_number;
        $material_description = $material->material_description;
        $issue_location = $material->issue_storage_location;
        $mstation = 'W' . $material->mrpc . 'S10';
        $quantity = $flo_detail->quantity * -1;
        $remark = 'YMES';
        $created_by = Auth::user()->username;
        $created_by_name = Auth::user()->name;
        $synced = date('Y-m-d H:i:s');
        $synced_by = 'manual';

        app(YMESController::class)->production_result(
            $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
        // YMES END

        if ($flo_detail->completion != null) {
            $log_transaction = new LogTransaction([
                'material_number' => $flo_detail->material_number,
                'issue_plant' => '8190',
                'issue_storage_location' => $material->issue_storage_location,
                'transaction_code' => 'MB1B',
                'mvt' => '102',
                'transaction_date' => date('Y-m-d H:i:s'),
                'qty' => $flo_detail->quantity,
                'created_by' => $id,
            ]);
            $log_transaction->save();
        }

        if ($flo_detail->transfer != null) {
            $log_transaction = new LogTransaction([
                'material_number' => $flo_detail->material_number,
                'issue_plant' => '8190',
                'issue_storage_location' => $material->issue_storage_location,
                'receive_plant' => '8191',
                'receive_storage_location' => 'FSTK',
                'transaction_code' => 'MB1B',
                'mvt' => '9P2',
                'transaction_date' => date('Y-m-d H:i:s'),
                'qty' => $flo_detail->quantity,
                'created_by' => $id,
            ]);
            $log_transaction->save();
        }

        $flo = Flo::where('flo_number', '=', $flo_detail->flo_number)->first();
        if ($flo != null) {
            if (($flo->actual - $flo_detail->quantity) <= 0) {
                $flo->forceDelete();
            } else {
                $flo->actual = $flo->actual - $flo_detail->quantity;
                if ($flo->status == '1') {
                    $flo->quantity = $flo->quantity - $flo_detail->quantity;
                }
                $flo->save();
            }
            $inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $flo_detail->material_number, 'storage_location' => $material->issue_storage_location]);
            $inventory->quantity = ($inventory->quantity - $flo_detail->quantity);
            $inventory->save();
            $flo_detail->forceDelete();
        } else {
            $flo_detail->forceDelete();
        }

        // BODY INOUT

        if ($material->inout_location != "" || $material->inout_location != null) {
            db::connection('ympimis_2')->table('kanban_inout_logs')->insert([
                'tag' => $flo_number,
                'material_number' => $material->material_number,
                'material_description' => $material->material_description,
                'issue_location' => $material->issue_storage_location,
                'receive_location' => 'FSTK',
                'quantity' => $material_volume->lot_completion * -1,
                'remark' => 'FA-PR',
                'category' => 'EXPORT',
                'transaction_by' => Auth::user()->username,
                'transaction_by_name' => Auth::user()->name,
                'created_by' => Auth::user()->username,
                'created_by_name' => Auth::user()->name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $boms = db::connection('ympimis_2')->table('kanban_inout_boms')
                ->where('material_number', '=', $material->material_number)
                ->first();

            foreach ($boms as $bom) {
                $inventory_material = db::connection('ympimis_2')->table('kanban_inout_inventories')
                    ->where('material_number', '=', $bom->material_child)
                    ->where('location', '=', $material->inout_location . '-MATERIAL')
                    ->first();

                if ($inventory_material) {
                    db::connection('ympimis_2')->table('kanban_inout_inventories')
                        ->where('material_number', '=', $bom->material_child)
                        ->where('location', '=', $material->inout_location . '-MATERIAL')
                        ->update([
                            'quantity' => $inventory_material->quantity + $material_volume->lot_completion,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                } else {
                    db::connection('ympimis_2')->table('kanban_inout_inventories')
                        ->insert([
                            'material_number' => $bom->material_child,
                            'material_description' => $bom->material_child_description,
                            'quantity' => $material_volume->lot_completion,
                            'location' => $material->inout_location . '-MATERIAL',
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }
            }

        }

        $response = array(
            'status' => true,
            'message' => "Item has been deleted.",
        );
        return Response::json($response);
    }

    public function fetch_flo_deletion()
    {
        $flo_details = FloDetail::leftJoin('flos', 'flos.flo_number', '=', 'flo_details.flo_number')
            ->leftJoin('materials', 'materials.material_number', '=', 'flo_details.material_number')
        // ->leftJoin('statuses', 'statuses.status_code', '=', 'flos.status')
            ->whereIn('flos.status', ['M', '0', '1'])
            ->orWhereNull('flos.status')
            ->select(
                'flo_details.id',
                'flo_details.flo_number',
                'flo_details.serial_number',
                'materials.material_number',
                'materials.material_description',
                'flo_details.quantity',
                db::raw('if(flo_details.completion is not null, "Uploaded", "-") as completion'),
                db::raw('if(flo_details.transfer is not null, "Uploaded", "-") as transfer'),
                db::raw('if(flos.status is not null, flos.status, "error") as status'),
                'flo_details.created_at'
            )
            ->get();

        return DataTables::of($flo_details)
            ->addColumn('action', function ($flo_details) {
                return '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-danger" onClick="deleteConfirmation(id)" id="' . $flo_details->id . '"><i class="fa fa-trash"></i></a>';
            })
            ->make(true);
    }

    // public function sendMail($st_date){

    //     $mail_to = db::table('send_emails')
    //     ->where('remark', '=', 'stuffing')
    //     ->WhereNull('deleted_at')
    //     ->orWhere('remark', '=', 'superman')
    //     ->WhereNull('deleted_at')
    //     ->select('email')
    //     ->get();

    //     // if($st_date == date('Y-m-d')){
    //         $query = "select shipment_schedules.st_date, stuffings.container_id, destinations.destination_shortname, stuffings.container_number, stuffings.container_name, coalesce(sum(shipment_schedules.quantity),0) as plan, coalesce(if(stuffings.container_number is not null, sum(shipment_schedules.quantity), sum(stuffings.actual)),0) as actual, max(stuffings.created_at) as finished_at from shipment_schedules left join
    //         (

    //         select flos.shipment_schedule_id, flos.container_id, container_schedules.container_number, containers.container_name, sum(flos.actual) as actual, max(logs.created_at) as created_at from flos left join container_schedules on container_schedules.container_id = flos.container_id left join containers on containers.container_code = container_schedules.container_code
    //         left join
    //         (select flo_logs.flo_number, flo_logs.created_at from flo_logs where flo_logs.status_code = 3) as logs on logs.flo_number = flos.flo_number where flos.`status` in (3,4)
    //         group by flos.shipment_schedule_id, flos.container_id, containers.container_name, container_schedules.container_number

    //         ) as stuffings on stuffings.shipment_schedule_id = shipment_schedules.id
    //         left join destinations on destinations.destination_code = shipment_schedules.destination_code
    //         where shipment_schedules.st_date = '".$st_date."'
    //         group by shipment_schedules.st_date, stuffings.container_id, stuffings.container_number, destinations.destination_shortname, stuffings.container_name
    //         order by finished_at desc";

    //         $stuffings = db::select($query);

    //         if($stuffings != null){
    //             Mail::to($mail_to)->send(new SendEmail($stuffings, 'stuffing'));
    //         }
    //     // }
    // }
}
