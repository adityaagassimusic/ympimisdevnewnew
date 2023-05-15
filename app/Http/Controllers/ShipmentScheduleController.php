<?php

namespace App\Http\Controllers;

use App\Destination;
use App\Material;
use App\ShipmentCondition;
use App\ShipmentSchedule;
use DataTables;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

class ShipmentScheduleController extends Controller
{
    private $hpl;

    public function __construct()
    {
        $this->middleware('auth');
        $this->hpl = [
            'ASBELL&BOW',
            'ASBODY',
            'ASFG',
            'ASKEY',
            'ASNECK',
            'ASPAD',
            'ASPART',
            'CASE',
            'CLBARREL',
            'CLBELL',
            'CLFG',
            'CLKEY',
            'CLLOWER',
            'CLPART',
            'CLUPPER',
            'FLBODY',
            'FLFG',
            'FLFOOT',
            'FLHEAD',
            'FLKEY',
            'FLPAD',
            'FLPART',
            'MOUTHPIECE',
            'PN',
            'PN PARTS',
            'RC',
            'TSBELL&BOW',
            'TSBODY',
            'TSFG',
            'TSKEY',
            'TSNECK',
            'TSPART',
            'VENOVA',
            'SX',
            'YDS',

            'ASSY-SX',
            'BPRO',
            'CASE',
            'CL-BODY',
            'MP',
            'MPRO',
            'PN-PART',
            'RC ASSY',
            'SUBASSY-CL',
            'SUBASSY-FL',
            'SUBASSY-SX',
            'TANPO',
            'VN-ASSY',
            'VN-INJECTION',
            'WELDING',
            'ZPRO',

        ];
    }

    public function index()
    {
        $shipment_schedules = ShipmentSchedule::orderByRaw('st_date DESC', 'material_number ASC')
            ->get();

        $materials = Material::orderBy('material_number', 'ASC')->get();
        $destinations = Destination::orderBy('destination_shortname', 'ASC')->get();
        $shipment_conditions = ShipmentCondition::orderBy('shipment_condition_code', 'ASC')->get();

        return view('shipment_schedules.index', array(
            'shipment_schedules' => $shipment_schedules,
            'materials' => $materials,
            'destinations' => $destinations,
            'shipment_conditions' => $shipment_conditions,
            'hpls' => $this->hpl,
        )
        )->with('page', 'Shipment Schedule');

    }

    public function indexShipmentUnmatch()
    {
        return view('shipment_schedules.unmatch')->with('page', 'Shipment Unmatch');

    }

    public function inputShipment(Request $request)
    {
        $period = $request->get('period');
        $upload = $request->get('st_data');
        $uploadRows = preg_split("/\r?\n/", $upload);

        DB::beginTransaction();

        foreach ($uploadRows as $uploadRow) {
            $uploadColumn = preg_split("/\t/", $uploadRow);

            $st_month = $period . '-01';
            $sales_order = $uploadColumn[0];
            $shipment_condition_code = strtoupper($uploadColumn[1]);
            $destination_code = strtoupper($uploadColumn[2]);
            $material_number = strtoupper($uploadColumn[3]);
            $hpl = strtoupper($uploadColumn[4]);
            $st_date = $uploadColumn[5];
            $bl_date = $uploadColumn[6];
            $quantity = $uploadColumn[7];

            if (strlen($material_number) != 7) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => 'GMC Unmatch',
                );
                return Response::json($response);
            }

            try {
                $insert = new ShipmentSchedule([
                    'st_month' => $st_month,
                    'sales_order' => $sales_order,
                    'shipment_condition_code' => $shipment_condition_code,
                    'destination_code' => $destination_code,
                    'material_number' => $material_number,
                    'hpl' => $hpl,
                    'st_date' => $st_date,
                    'bl_date' => $bl_date,
                    'quantity' => $quantity,
                    'actual_quantity' => 0,
                    'created_by' => Auth::id(),
                ]);
                $insert->save();

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

    public function fetchShipmentUnmatch(Request $request)
    {

        if (strlen($request->get('month')) > 0) {
            $month = $request->get('month') . '-01';
            $month_txt = date('M-Y', strtotime($month));
        } else {
            $month = date('Y-m') . '-01';
            $month_txt = date('M-Y');
        }

        $materials = db::table('materials')
            ->where('valcl', '9010')
            ->get();

        $requests = db::table('production_requests')
            ->leftJoin('destinations', 'destinations.destination_code', '=', 'production_requests.destination_code')
            ->where('request_month', '=', $month)
            ->select(
                'production_requests.material_number',
                db::raw("IF(destinations.destination_shortname LIKE '%ITM%', 'ITM', destinations.destination_shortname) AS destination_shortname"),
                db::raw("production_requests.quantity AS request"),
                db::raw("0 AS so_qty")
            )
            ->get();

        $material_volume = db::table('material_volumes')
            ->whereIn('category', ['KD', 'FG'])
            ->get();

        $shipments = db::table('shipment_schedules')
            ->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
            ->where('shipment_schedules.st_month', '=', $month)
            ->select(
                'shipment_schedules.material_number',
                'destinations.destination_shortname',
                db::raw("SUM(shipment_schedules.quantity) AS shipment"),
                db::raw("0 AS so_qty")
            )
            ->groupBy(
                'shipment_schedules.material_number',
                'destinations.destination_shortname',
                'so_qty'
            )
            ->get();

        $draft_shipments = db::table('production_schedules_four_steps')
            ->leftJoin('destinations', 'destinations.destination_code', '=', 'production_schedules_four_steps.destination_code')
            ->where('production_schedules_four_steps.st_month', '=', $month)
            ->select(
                'production_schedules_four_steps.material_number',
                'destinations.destination_shortname',
                db::raw("SUM(production_schedules_four_steps.quantity) AS shipment"),
                db::raw("0 AS so_qty")
            )
            ->groupBy(
                'production_schedules_four_steps.material_number',
                'destinations.destination_shortname',
                'so_qty'
            )
            ->get();

        $sales_orders = db::connection('ympimis_2')
            ->table('sales_orders')
            ->where('sales_month', '=', $month)
            ->get();

        $destinations = db::table('destinations')->get();

        $temp_so = [];
        for ($i = 0; $i < count($sales_orders); $i++) {
            for ($j = 0; $j < count($destinations); $j++) {
                if ($sales_orders[$i]->destination_code == $destinations[$j]->destination_code) {
                    $destination_shortname = '';
                    if (str_contains($destinations[$j]->destination_shortname, 'ITM')) {
                        $destination_shortname = 'ITM';
                    } else {
                        $destination_shortname = $destinations[$j]->destination_shortname;
                    }

                    $row = array();
                    $row['material_number'] = $sales_orders[$i]->material_number;
                    $row['destination_shortname'] = $destination_shortname;
                    $row['request'] = 0;
                    $row['so_qty'] = $sales_orders[$i]->quantity;
                    $temp_so[] = (object) $row;
                    break;
                }
            }
        }

        $union_request_so = array_merge(json_decode($requests), $temp_so);
        $resume_request_so = [];

        for ($i = 0; $i < count($union_request_so); $i++) {
            $key = $union_request_so[$i]->material_number . '#' . $union_request_so[$i]->destination_shortname;
            if (!array_key_exists($key, $resume_request_so)) {

                $material_description = '';
                $category = '';
                $hpl = '';

                for ($j = 0; $j < count($materials); $j++) {
                    if ($materials[$j]->material_number == $union_request_so[$i]->material_number) {
                        $material_description = $materials[$j]->material_description;
                        $category = $materials[$j]->category;
                        $hpl = $materials[$j]->hpl;
                    }
                }

                $row = array();
                $row['material_number'] = $union_request_so[$i]->material_number;
                $row['destination_shortname'] = $union_request_so[$i]->destination_shortname;
                $row['material_description'] = $material_description;
                $row['category'] = $category;
                $row['hpl'] = $hpl;
                $row['request'] = $union_request_so[$i]->request;
                $row['so_qty'] = $union_request_so[$i]->so_qty;
                $row['diff'] = $union_request_so[$i]->so_qty - $union_request_so[$i]->request;
                $resume_request_so[$key] = (object) $row;
            } else {
                $resume_request_so[$key]->request = doubleval($resume_request_so[$key]->request) + doubleval($union_request_so[$i]->request);
                $resume_request_so[$key]->so_qty = doubleval($resume_request_so[$key]->so_qty) + doubleval($union_request_so[$i]->so_qty);
                $resume_request_so[$key]->diff = doubleval($resume_request_so[$key]->diff) + (doubleval($union_request_so[$i]->so_qty) - doubleval($union_request_so[$i]->request));
            }
        }

        $temp_so = [];
        for ($i = 0; $i < count($sales_orders); $i++) {
            for ($j = 0; $j < count($destinations); $j++) {
                if ($sales_orders[$i]->destination_code == $destinations[$j]->destination_code) {
                    $destination_shortname = '';
                    if (strlen($destinations[$j]->destination_shortname) > 0) {
                        $destination_shortname = $destinations[$j]->destination_shortname;
                    }

                    $row = array();
                    $row['material_number'] = $sales_orders[$i]->material_number;
                    $row['destination_shortname'] = $destination_shortname;
                    $row['shipment'] = 0;
                    $row['so_qty'] = $sales_orders[$i]->quantity;
                    $temp_so[] = (object) $row;
                    break;
                }
            }
        }

        $union_so_shipment = array_merge($temp_so, json_decode($shipments));
        $resume_so_shipment = [];

        for ($i = 0; $i < count($union_so_shipment); $i++) {
            $key = $union_so_shipment[$i]->material_number . '#' . $union_so_shipment[$i]->destination_shortname;
            if (!array_key_exists($key, $resume_so_shipment)) {

                $material_description = '';
                $category = '';
                $hpl = '';

                for ($j = 0; $j < count($materials); $j++) {
                    if ($materials[$j]->material_number == $union_so_shipment[$i]->material_number) {
                        $material_description = $materials[$j]->material_description;
                        $category = $materials[$j]->category;
                        $hpl = $materials[$j]->hpl;
                    }
                }

                $row = array();
                $row['material_number'] = $union_so_shipment[$i]->material_number;
                $row['destination_shortname'] = $union_so_shipment[$i]->destination_shortname;
                $row['material_description'] = $material_description;
                $row['category'] = $category;
                $row['hpl'] = $hpl;
                $row['so_qty'] = $union_so_shipment[$i]->so_qty;
                $row['shipment'] = $union_so_shipment[$i]->shipment;
                $row['diff'] = $union_so_shipment[$i]->so_qty - $union_so_shipment[$i]->shipment;
                $resume_so_shipment[$key] = (object) $row;
            } else {
                $resume_so_shipment[$key]->shipment = doubleval($resume_so_shipment[$key]->shipment) + doubleval($union_so_shipment[$i]->shipment);
                $resume_so_shipment[$key]->so_qty = doubleval($resume_so_shipment[$key]->so_qty) + doubleval($union_so_shipment[$i]->so_qty);
                $resume_so_shipment[$key]->diff = doubleval($resume_so_shipment[$key]->diff) + (doubleval($union_so_shipment[$i]->so_qty) - doubleval($union_so_shipment[$i]->shipment));
            }
        }

        $union_so_draft = array_merge($temp_so, json_decode($draft_shipments));
        $resume_so_draft = [];

        for ($i = 0; $i < count($union_so_draft); $i++) {
            $key = $union_so_draft[$i]->material_number . '#' . $union_so_draft[$i]->destination_shortname;
            if (!array_key_exists($key, $resume_so_draft)) {

                $material_description = '';
                $category = '';
                $hpl = '';

                for ($j = 0; $j < count($materials); $j++) {
                    if ($materials[$j]->material_number == $union_so_draft[$i]->material_number) {
                        $material_description = $materials[$j]->material_description;
                        $category = $materials[$j]->category;
                        $hpl = $materials[$j]->hpl;
                    }
                }

                $row = array();
                $row['material_number'] = $union_so_draft[$i]->material_number;
                $row['destination_shortname'] = $union_so_draft[$i]->destination_shortname;
                $row['material_description'] = $material_description;
                $row['category'] = $category;
                $row['hpl'] = $hpl;
                $row['so_qty'] = $union_so_draft[$i]->so_qty;
                $row['shipment'] = $union_so_draft[$i]->shipment;
                $row['diff'] = $union_so_draft[$i]->so_qty - $union_so_draft[$i]->shipment;
                $resume_so_draft[$key] = (object) $row;
            } else {
                $resume_so_draft[$key]->shipment = doubleval($resume_so_draft[$key]->shipment) + doubleval($union_so_draft[$i]->shipment);
                $resume_so_draft[$key]->so_qty = doubleval($resume_so_draft[$key]->so_qty) + doubleval($union_so_draft[$i]->so_qty);
                $resume_so_draft[$key]->diff = doubleval($resume_so_draft[$key]->diff) + (doubleval($union_so_draft[$i]->so_qty) - doubleval($union_so_draft[$i]->shipment));
            }
        }

        $response = array(
            'status' => true,
            'material_volume' => $material_volume,
            'resume_request_so' => $resume_request_so,
            'resume_so_draft' => $resume_so_draft,
            'resume_so_shipment' => $resume_so_shipment,

        );
        return Response::json($response);

    }

    public function fetchShipment(Request $request)
    {

        $month = $request->get('month');
        if (strlen($month) <= 0) {
            $month = date('Y-m');
        }

        $shipment_schedules = ShipmentSchedule::leftJoin('materials', 'materials.material_number', '=', 'shipment_schedules.material_number')
            ->leftJoin('shipment_conditions', 'shipment_conditions.shipment_condition_code', '=', 'shipment_schedules.shipment_condition_code')
            ->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
            ->leftJoin('weekly_calendars', 'weekly_calendars.week_date', '=', 'shipment_schedules.st_date')
            ->where('st_month', '=', $month . '-01')
            ->select(
                'shipment_schedules.id',
                'shipment_schedules.quantity',
                'shipment_schedules.sales_order',
                'materials.material_description',
                'shipment_conditions.shipment_condition_name',
                'shipment_schedules.hpl',
                'shipment_schedules.st_date',
                'shipment_schedules.bl_date',
                DB::raw('DATE_FORMAT(shipment_schedules.st_month, "%b-%Y") as st_month'),
                'destinations.destination_shortname',
                'shipment_schedules.material_number',
                'weekly_calendars.week_name'
            )
            ->orderByRaw('st_date DESC', 'shipment_schedules.material_number ASC')
            ->get();

        return DataTables::of($shipment_schedules)
            ->addColumn('action', function ($shipment_schedules) {
                return '
            <button class="btn btn-xs btn-info" data-toggle="tooltip" title="Details" onclick="modalView(' . $shipment_schedules->id . ')">View</button>
            <button class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit" onclick="modalEdit(' . $shipment_schedules->id . ')">Edit</button>';

                // --- DELETE BUTTON
                // <button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete('.$shipment_schedules->id.',\''.$shipment_schedules->material_number.'\',\''.$shipment_schedules->st_date.'\')">Delete</button>
            })

            ->rawColumns(['action' => 'action'])
            ->make(true);
    }

    public function fetchEdit(Request $request)
    {
        $shipment_schedule = ShipmentSchedule::select(db::raw("DATE_FORMAT(st_month,'%m/%Y') st_month"), "sales_order", "shipment_condition_code", "destination_code", "material_number", "hpl", db::raw("DATE_FORMAT(st_date,'%d/%m/%Y') st_date"), db::raw("DATE_FORMAT(bl_date,'%d/%m/%Y') bl_date"), "quantity")
            ->find($request->get("id"));

        $response = array(
            'status' => true,
            'datas' => $shipment_schedule,
        );

        return Response::json($response);
    }

    public function create(Request $request)
    {
        try {
            $id = Auth::id();
            $st_month = date('Y-m-d', strtotime(str_replace('/', '-', '01/' . $request->get('st_month'))));

            $shipment_schedule = new ShipmentSchedule([
                'st_month' => $st_month,
                'sales_order' => $request->get('sales_order'),
                'shipment_condition_code' => $request->get('shipment_condition_code'),
                'destination_code' => $request->get('destination_code'),
                'material_number' => $request->get('material_number'),
                'hpl' => $request->get('hpl'),
                'st_date' => date('Y-m-d', strtotime(str_replace('/', '-', $request->get('st_date')))),
                'bl_date' => date('Y-m-d', strtotime(str_replace('/', '-', $request->get('bl_date')))),
                'quantity' => $request->get('quantity'),
                'created_by' => $id,
            ]);

            $shipment_schedule->save();

            $response = array(
                'status' => true,
            );

            return Response::json($response);
        } catch (QueryException $e) {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062) {
                $response = array(
                    'status' => false,
                    'message' => "already exist",
                );

                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );

                return Response::json($response);
            }
        }
    }

    public function show($id)
    {
        $hpls = $this->hpl;

        $shipment_schedule = ShipmentSchedule::find($id);
        return view('shipment_schedules.show', array(
            'shipment_schedule' => $shipment_schedule,
        )
        )->with('page', 'Shipment Schedule');
        //
    }

    public function view(Request $request)
    {
        $query = "select st_month, st_date, sales_order, CONCAT(shipment.material_number,' - ',material_description) material, shipment.quantity, users.`name`, material_description, CONCAT(materials.origin_group_code,' - ',origin_group_name) as origin_group, CONCAT(destinations.destination_code,' - ',destinations.destination_name) as destination, CONCAT(shipment_conditions.shipment_condition_code,' - ',shipment_conditions.shipment_condition_name) shipment_condition, bl_date, weekly_calendars.week_name, shipment.created_at, shipment.hpl, shipment.updated_at from
        (select st_month, sales_order, shipment_condition_code, destination_code, material_number, hpl, st_date, bl_date, quantity, created_by, created_at, updated_at from shipment_schedules where id = "
            . $request->get('id') . ") as shipment
        left join materials on materials.material_number = shipment.material_number
        left join destinations on shipment.destination_code = destinations.destination_code
        left join shipment_conditions on shipment.shipment_condition_code = shipment_conditions.shipment_condition_code
        left join weekly_calendars on shipment.st_date = weekly_calendars.week_date
        left join origin_groups on origin_groups.origin_group_code = materials.origin_group_code
        left join users on shipment.created_by = users.id";

        $shipment = DB::select($query);

        $response = array(
            'status' => true,
            'datas' => $shipment,
        );

        return Response::json($response);
    }

    public function edit(Request $request)
    {
        try {
            $st_month = date('Y-m-d', strtotime(str_replace('/', '-', '01/' . $request->get('st_month'))));
            $shipment_schedule = ShipmentSchedule::find($request->get("id"));

            $shipment_schedule->st_month = $st_month;
            $shipment_schedule->sales_order = $request->get('sales_order');
            $shipment_schedule->shipment_condition_code = $request->get('shipment_condition_code');
            $shipment_schedule->destination_code = $request->get('destination_code');
            $shipment_schedule->material_number = $request->get('material_number');
            $shipment_schedule->hpl = $request->get('hpl');
            $shipment_schedule->st_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->get('st_date'))));
            $shipment_schedule->bl_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->get('bl_date'))));
            $shipment_schedule->quantity = $request->get('quantity');
            $shipment_schedule->save();

            $response = array(
                'status' => true,
                'datas' => $shipment_schedule,
            );

            return Response::json($response);
        } catch (QueryException $e) {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062) {
                $response = array(
                    'status' => true,
                    'datas' => "Shipment Scedule already exist",
                );

                return Response::json($response);
            } else {
                $response = array(
                    'status' => true,
                    'datas' => $e->getMessage(),
                );

                return Response::json($response);
            }
        }
    }

    public function delete(Request $request)
    {
        $shipment_schedule = ShipmentSchedule::find($request->get('id'));
        $shipment_schedule->delete();

        $response = array(
            'status' => true,
        );

        return Response::json($response);
    }

    public function import(Request $request)
    {
        if ($request->hasFile('shipment_schedule')) {

            $id = Auth::id();

            $file = $request->file('shipment_schedule');
            $data = file_get_contents($file);

            $rows = explode("\r\n", $data);
            foreach ($rows as $row) {
                if (strlen($row) > 0) {
                    $row = explode("\t", $row);
                    $shipment_schedule = new ShipmentSchedule([
                        'st_month' => date('Y-m-d', strtotime(str_replace('/', '-', $row[0]))),
                        'sales_order' => $row[1],
                        'shipment_condition_code' => $row[2],
                        'destination_code' => $row[3],
                        'material_number' => $row[4],
                        'hpl' => $row[5],
                        'st_date' => date('Y-m-d', strtotime(str_replace('/', '-', $row[6]))),
                        'bl_date' => date('Y-m-d', strtotime(str_replace('/', '-', $row[7]))),
                        'quantity' => $row[8],
                        'created_by' => $id,
                    ]);

                    $shipment_schedule->save();
                }
            }
            return redirect('/index/shipment_schedule')->with('status', 'New shipment schedules has been imported.')->with('page', 'Shipment Schedule');

        } else {
            return redirect('/index/shipment_schedule')->with('error', 'Please select a file.')->with('page', 'Shipment Schedule');
        }
    }

}