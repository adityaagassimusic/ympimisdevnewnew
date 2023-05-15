<?php

namespace App\Http\Controllers;

use App\CodeGenerator;
use App\Flo;
use App\FloDetail;
use App\FloLog;
use App\Inventory;
use App\LogProcess;
use App\Material;
use App\MaterialVolume;
use App\StampInventory;
use Carbon\Carbon;
use DataTables;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Response;

class MaedaoshiController extends Controller
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

    public function index_bi()
    {
        $flos = Flo::where('flos.status', '=', 'M')->get();
        return view('flos.maedaoshi_bi', array(
            'flos' => $flos,
        ))
            ->with('page', 'FLO Maedaoshi BI');
    }

    public function index_ei()
    {
        $flos = Flo::where('flos.status', '=', 'M')->get();
        return view('flos.maedaoshi_ei', array(
            'flos' => $flos,
        ))
            ->with('page', 'FLO Maedaoshi EI');
    }

    public function index_after_bi()
    {
        return view('flos.maedaoshi_after_bi')->with('page', 'FLO Maedaoshi BI');
    }

    public function index_after_ei()
    {
        return view('flos.maedaoshi_after_ei')->with('page', 'FLO Maedaoshi EI');
    }

    public function scan_after_maedaoshi_material(Request $request)
    {
        if ($request->get('ymj') == 'true') {
            $flo = DB::table('flos')
                ->leftJoin('shipment_schedules', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
                ->where('shipment_schedules.material_number', '=', $request->get('material_number'))
                ->where('shipment_schedules.destination_code', '=', 'Y1000XJ')
                ->where('flos.status', '=', '0')
                ->where(DB::raw('flos.quantity-flos.actual'), '>', 0)
                ->first();
        } else {
            $flo = DB::table('flos')
                ->leftJoin('shipment_schedules', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
                ->where('shipment_schedules.material_number', '=', $request->get('material_number'))
                ->where('flos.status', '=', '0')
                ->where(DB::raw('flos.quantity-flos.actual'), '>', 0)
                ->first();
        }

        if ($flo == null) {
            if ($request->get('type') == 'pd' || Auth::user()->role_code == "OP-Assy-FL") {
                $shipment_schedule = DB::table('shipment_schedules')
                    ->leftJoin('flos', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
                    ->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
                    ->where('shipment_schedules.material_number', '=', $request->get('material_number'))
                    ->orderBy('shipment_schedules.st_date', 'ASC')
                    ->select(DB::raw('if(shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0)) > material_volumes.lot_flo, material_volumes.lot_flo, shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0))) as flo_quantity', 'shipment_schedules.id'))
                    ->groupBy('shipment_schedules.quantity', 'material_volumes.lot_flo', 'shipment_schedules.id')
                    ->having('flo_quantity', '>', 0)
                    ->first();
            } else {
                if ($request->get('ymj') == 'true') {
                    $shipment_schedule = DB::table('shipment_schedules')
                        ->leftJoin('flos', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
                        ->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
                        ->where('shipment_schedules.material_number', '=', $request->get('material_number'))
                        ->where('shipment_schedules.destination_code', '=', 'Y1000XJ')
                        ->orderBy('shipment_schedules.st_date', 'ASC')
                        ->select(DB::raw('if(shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0)) > material_volumes.lot_flo, material_volumes.lot_flo, shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0))) as flo_quantity', 'shipment_schedules.id'))
                        ->groupBy('shipment_schedules.quantity', 'material_volumes.lot_flo', 'shipment_schedules.id')
                        ->having('flo_quantity', '>', 0)
                        ->first();
                } else {
                    $shipment_schedule = DB::table('shipment_schedules')
                        ->leftJoin('flos', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
                        ->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
                        ->where('shipment_schedules.material_number', '=', $request->get('material_number'))
                        ->where('shipment_schedules.destination_code', '<>', 'Y1000XJ')
                        ->orderBy('shipment_schedules.st_date', 'ASC')
                        ->select(DB::raw('if(shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0)) > material_volumes.lot_flo, material_volumes.lot_flo, shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0))) as flo_quantity', 'shipment_schedules.id'))
                        ->groupBy('shipment_schedules.quantity', 'material_volumes.lot_flo', 'shipment_schedules.id')
                        ->having('flo_quantity', '>', 0)
                        ->first();
                }
            }

            if ($shipment_schedule != null) {
                $response = array(
                    'status' => true,
                    'message' => 'Shipment schedule available',
                    'flo_number' => '',
                    'status_code' => 'new',
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'There is no shipment schedule for ' . $request->get('material_number') . ' yet.',
                );
                return Response::json($response);
            }
        } else {
            $response = array(
                'status' => true,
                'message' => 'Open FLO available',
                'flo_number' => $flo->flo_number,
                'status_code' => 'open',
            );
            return Response::json($response);
        }
    }

    public function scan_maedaoshi_material(Request $request)
    {

        $material = DB::table('materials')
            ->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'materials.material_number')
            ->where('materials.material_number', '=', $request->get('material'))
            ->where('material_volumes.material_number', '=', $request->get('material'))
            ->first();

        // $first = date('Y-m-01');
        // $last = date('Y-m-d', strtotime(carbon::now()->endOfMonth()));

        // $query = "select material_number, sum(plan) as plan, sum(actual) as actual from
        // (
        // select material_number, quantity as plan, 0 as actual from production_schedules where due_date >= '".$first."' and due_date <= '".$last."'

        // union all

        // select material_number, 0 as plan, quantity as actual from flo_details where date(created_at) >= '".$first."' and date(created_at) <= '".$last."'
        // ) as result
        // group by result.material_number
        // having plan <= actual and material_number = '".$request->get('material')."'";

        // $productionPlan = DB::select($query);

        // if(!empty($productionPlan)){
        //     $response = array(
        //         'status' => false,
        //         'message' => 'There is no production schedule for material '. $request->get('material') .'.',
        //     );
        //     return Response::json($response);
        // }

        if ($material != null) {

            if ($request->get('type') == 'pd') {
                $flo = DB::table('flos')
                    ->leftJoin('shipment_schedules', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
                    ->where('shipment_schedules.material_number', '=', $request->get('material'))
                    ->where('flos.status', '=', '0')
                    ->where(DB::raw('flos.quantity-flos.actual'), '>', 0)
                    ->first();
            } else {
                $flo = DB::table('flos')
                    ->leftJoin('shipment_schedules', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
                    ->where('shipment_schedules.material_number', '=', $request->get('material'))
                    ->where('shipment_schedules.destination_code', '<>', 'Y1000XJ')
                    ->where('flos.status', '=', '0')
                    ->where(DB::raw('flos.quantity-flos.actual'), '>', 0)
                    ->first();
            }

            if ($flo == null) {

                if ($request->get('type') == 'pd') {
                    $shipment_schedule = DB::table('shipment_schedules')
                        ->leftJoin('flos', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
                        ->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
                        ->where('shipment_schedules.material_number', '=', $request->get('material'))
                        ->orderBy('shipment_schedules.st_date', 'ASC')
                        ->select(DB::raw('if(shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0)) > material_volumes.lot_flo, material_volumes.lot_flo, shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0))) as flo_quantity', 'shipment_schedules.id'))
                        ->groupBy('shipment_schedules.quantity', 'material_volumes.lot_flo', 'shipment_schedules.id')
                        ->having('flo_quantity', '>', 0)
                        ->first();

                    if ($shipment_schedule == null) {

                        $flo2 = DB::table('flos')
                            ->where('flos.flo_number', '=', 'Maedaoshi' . $request->get("material"))
                            ->where('flos.status', '=', 'M')
                            ->first();

                        if ($flo2 == null) {
                            $response = array(
                                'status' => true,
                                'message' => 'New maedaoshi will be created',
                                'maedaoshi' => '',
                            );
                            return Response::json($response);
                        } else {
                            $response = array(
                                'status' => true,
                                'message' => 'Open maedaoshi available',
                                'maedaoshi' => $flo2->flo_number,
                            );
                            return Response::json($response);
                        }
                    } else {
                        $response = array(
                            'status' => false,
                            'message' => 'There is shipment schedule for material ' . $request->get('material') . '.',
                        );
                        return Response::json($response);
                    }

                } else {
                    // $shipment_schedule = DB::table('shipment_schedules')
                    //     ->leftJoin('flos', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
                    //     ->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
                    //     ->where('shipment_schedules.material_number', '=', $request->get('material'))
                    //     ->where('shipment_schedules.destination_code', '<>', 'Y1000XJ')
                    //     ->orderBy('shipment_schedules.st_date', 'ASC')
                    //     ->select(DB::raw('if(shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0)) > material_volumes.lot_flo, material_volumes.lot_flo, shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0))) as flo_quantity', 'shipment_schedules.id'))
                    //     ->groupBy('shipment_schedules.quantity', 'material_volumes.lot_flo', 'shipment_schedules.id')
                    //     ->having('flo_quantity', '>', 0)
                    //     ->first();

                    $flo2 = DB::table('flos')
                        ->where('flos.flo_number', '=', 'Maedaoshi' . $request->get("material"))
                        ->where('flos.status', '=', 'M')
                        ->first();

                    if ($flo2 == null) {
                        $response = array(
                            'status' => true,
                            'message' => 'New maedaoshi will be created',
                            'maedaoshi' => '',
                            'material' => $material,
                        );
                        return Response::json($response);
                    } else {
                        $response = array(
                            'status' => true,
                            'message' => 'Open maedaoshi available',
                            'maedaoshi' => $flo2->flo_number,
                            'material' => $material,
                        );
                        return Response::json($response);
                    }

                }

            } else {
                $response = array(
                    'status' => false,
                    'message' => 'There is open FLO for material ' . $request->get('material') . '.',
                );
                return Response::json($response);
            }
        } else {
            $response = array(
                'status' => false,
                'message' => 'Material ' . $request->get('material') . ' is not registered.',
            );
            return Response::json($response);
        }
    }

    public function scan_after_maedaoshi_serial(Request $request)
    {

        $material_volume = MaterialVolume::where('material_number', '=', $request->get('material_number'))->first();
        $material = Material::where('material_number', '=', $request->get('material_number'))->first();
        $actual = $material_volume->lot_completion;

        // START CEK ITEM TRIAL
        if (in_array($material->hpl, ['ASFG']) && in_array($request->get('serial_number'), $this->sax_trial_serial_number)) {
            $response = array(
                'status' => false,
                'message' => 'Serial number ' . $request->get('serial_number') . ' termasuk FG trial onko hikiage dan tidak boleh diekspor.',
            );
            return Response::json($response);
        }
        // END CEK ITEM TRIAL

        $maedaoshi = FloDetail::where('flo_details.material_number', '=', $request->get('material_number'))
            ->where('flo_details.flo_number', 'like', 'Maedaoshi%');

        if ($request->get('type') == 'pd') {
            $maedaoshi = $maedaoshi->first();
        } else {
            $maedaoshi = $maedaoshi->where('flo_details.serial_number', '=', $request->get('serial_number'))->first();
        }

        if ($maedaoshi == null) {
            $response = array(
                'status' => false,
                'message' => "Material " . $request->get('material_number') . " is not maedaoshi",
            );
            return Response::json($response);
        } else {

            $id = Auth::id();

            if ($request->get('serial_number')) {
                $serial_number = $request->get('serial_number');
            } else {
                $prefix_now_pd = date("y") . date("m") . date("d");
                $code_generator_pd = CodeGenerator::where('note', '=', 'pd')->first();
                if ($prefix_now_pd != $code_generator_pd->prefix) {
                    $code_generator_pd->prefix = $prefix_now_pd;
                    $code_generator_pd->index = '0';
                    $code_generator_pd->save();
                }
                $number_pd = sprintf("%'.0" . $code_generator_pd->length . "d", $code_generator_pd->index + 1);
                $serial_number = $code_generator_pd->prefix . $number_pd;
            }

            $material_number = $request->get('material_number');

            if ($request->get('flo_number') == "") {
                if ($request->get('type') == 'pd') {
                    $shipment_schedule = DB::table('shipment_schedules')
                        ->leftJoin('flos', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')
                        ->leftJoin('shipment_conditions', 'shipment_schedules.shipment_condition_code', '=', 'shipment_conditions.shipment_condition_code')
                        ->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
                        ->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
                        ->leftJoin('materials', 'shipment_schedules.material_number', '=', 'materials.material_number')
                        ->where('shipment_schedules.material_number', '=', $request->get('material_number'))
                        ->orderBy('shipment_schedules.st_date', 'asc')
                        ->select('shipment_schedules.id', 'shipment_conditions.shipment_condition_name', 'destinations.destination_shortname', 'shipment_schedules.material_number', 'materials.material_description', 'shipment_schedules.st_date', DB::raw('if(shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0)) > material_volumes.lot_flo, material_volumes.lot_flo, shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0))) as flo_quantity'), 'shipment_schedules.destination_code')
                        ->groupBy('shipment_schedules.id', 'shipment_conditions.shipment_condition_name', 'destinations.destination_shortname', 'shipment_schedules.material_number', 'shipment_schedules.st_date', 'shipment_schedules.quantity', 'material_volumes.lot_flo', 'shipment_schedules.st_date', 'materials.material_description', 'shipment_schedules.destination_code')
                        ->having('flo_quantity', '>', '0')
                        ->first();
                } else {
                    if ($request->get('ymj') == 'true') {
                        $shipment_schedule = DB::table('shipment_schedules')
                            ->leftJoin('flos', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')
                            ->leftJoin('shipment_conditions', 'shipment_schedules.shipment_condition_code', '=', 'shipment_conditions.shipment_condition_code')
                            ->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
                            ->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
                            ->leftJoin('materials', 'shipment_schedules.material_number', '=', 'materials.material_number')
                            ->where('shipment_schedules.material_number', '=', $request->get('material_number'))
                            ->where('shipment_schedules.destination_code', '=', 'Y1000XJ')
                            ->orderBy('shipment_schedules.st_date', 'asc')
                            ->select('shipment_schedules.id', 'shipment_conditions.shipment_condition_name', 'destinations.destination_shortname', 'shipment_schedules.material_number', 'materials.material_description', 'shipment_schedules.st_date', DB::raw('if(shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0)) > material_volumes.lot_flo, material_volumes.lot_flo, shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0))) as flo_quantity'), 'shipment_schedules.destination_code')
                            ->groupBy('shipment_schedules.id', 'shipment_conditions.shipment_condition_name', 'destinations.destination_shortname', 'shipment_schedules.material_number', 'shipment_schedules.st_date', 'shipment_schedules.quantity', 'material_volumes.lot_flo', 'shipment_schedules.st_date', 'materials.material_description', 'shipment_schedules.destination_code')
                            ->having('flo_quantity', '>', '0')
                            ->first();
                    } else {
                        $shipment_schedule = DB::table('shipment_schedules')
                            ->leftJoin('flos', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')
                            ->leftJoin('shipment_conditions', 'shipment_schedules.shipment_condition_code', '=', 'shipment_conditions.shipment_condition_code')
                            ->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
                            ->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
                            ->leftJoin('materials', 'shipment_schedules.material_number', '=', 'materials.material_number')
                            ->where('shipment_schedules.material_number', '=', $request->get('material_number'))
                            ->where('shipment_schedules.destination_code', '<>', 'Y1000XJ')
                            ->orderBy('shipment_schedules.st_date', 'asc')
                            ->select('shipment_schedules.id', 'shipment_conditions.shipment_condition_name', 'destinations.destination_shortname', 'shipment_schedules.material_number', 'materials.material_description', 'shipment_schedules.st_date', DB::raw('if(shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0)) > material_volumes.lot_flo, material_volumes.lot_flo, shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0))) as flo_quantity'), 'shipment_schedules.destination_code')
                            ->groupBy('shipment_schedules.id', 'shipment_conditions.shipment_condition_name', 'destinations.destination_shortname', 'shipment_schedules.material_number', 'shipment_schedules.st_date', 'shipment_schedules.quantity', 'material_volumes.lot_flo', 'shipment_schedules.st_date', 'materials.material_description', 'shipment_schedules.destination_code')
                            ->having('flo_quantity', '>', '0')
                            ->first();
                    }
                }

                try {
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

                    $flo_maedaoshi = Flo::where('flo_number', '=', $maedaoshi->flo_number)
                        ->first();

                    if (($flo_maedaoshi->actual - $actual) <= 0) {
                        $flo_maedaoshi->forceDelete();
                    } else {
                        $flo_maedaoshi->actual = ($flo_maedaoshi->actual - $actual);
                        $flo_maedaoshi->save();
                    }

                    $maedaoshi->flo_number = $flo_number;
                    $maedaoshi->save();

                    $flo = new Flo([
                        'flo_number' => $flo_number,
                        'shipment_schedule_id' => $shipment_schedule->id,
                        'material_number' => $material->material_number,
                        'quantity' => $shipment_schedule->flo_quantity,
                        'actual' => $actual,
                        'created_by' => $id,
                        'destination_code' => $shipment_schedule->destination_code,
                    ]);
                    $flo->save();

                    $flo_log = FloLog::updateOrCreate(
                        ['flo_number' => $flo_number, 'status_code' => '0'],
                        ['flo_number' => $flo_number, 'created_by' => $id, 'status_code' => '0', 'updated_at' => Carbon::now()]
                    );

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
                    } elseif (Auth::user()->role_code == "OP-WH-Exim") {
                        $printer_name = 'FLO Printer LOG';
                    } elseif (Auth::user()->role_code == "S-MIS") {
                        $printer_name = 'MIS';
                    } elseif (Auth::user()->role_code == "S") {
                        $printer_name = 'SUPERMAN';
                    } else {
                        $response = array(
                            'status' => false,
                            'message' => "You don't have permission to print FLO",
                        );
                        return Response::json($response);
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
                    $printer->text(strtoupper($shipment_schedule->destination_shortname . "\n\n"));
                    $printer->initialize();

                    $printer->setUnderline(true);
                    $printer->text('Shipment Date:');
                    $printer->setUnderline(false);
                    $printer->feed(1);
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->setTextSize(4, 2);
                    $printer->text(date('d-M-Y', strtotime($shipment_schedule->st_date)) . "\n\n");
                    $printer->initialize();

                    $printer->setUnderline(true);
                    $printer->text('By:');
                    $printer->setUnderline(false);
                    $printer->feed(1);
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->setTextSize(4, 2);
                    $printer->text(strtoupper($shipment_schedule->shipment_condition_name) . "\n\n");

                    $printer->initialize();
                    $printer->setTextSize(2, 2);
                    $printer->text("   " . strtoupper($shipment_schedule->material_number) . "\n");
                    $printer->text("   " . strtoupper($shipment_schedule->material_description) . "\n");

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
                    $printer->text("Max Qty:" . $shipment_schedule->flo_quantity . "\n");
                    $printer->cut();
                    $printer->close();

                    $response = array(
                        'status' => true,
                        'message' => 'New FLO has been printed',
                        'flo_number' => $flo_number,
                        'status_code' => 'new',
                    );
                    return Response::json($response);

                } catch (\Exception$e) {
                    $response = array(
                        'status' => false,
                        'message' => "Couldn't print to this printer " . $e->getMessage() . "\n.",
                    );
                    return Response::json($response);
                }
            } else {
                try {
                    $flo_maedaoshi = Flo::where('flo_number', '=', $maedaoshi->flo_number)
                        ->first();

                    if (($flo_maedaoshi->actual - $actual) <= 0) {
                        $flo_maedaoshi->forceDelete();
                    } else {
                        $flo_maedaoshi->actual = ($flo_maedaoshi->actual - $actual);
                        $flo_maedaoshi->save();
                    }

                    $maedaoshi->flo_number = $request->get('flo_number');
                    $maedaoshi->save();

                    $flo = Flo::where('flo_number', '=', $request->get('flo_number'))->first();
                    $flo->actual = $flo->actual + $actual;
                    $flo->save();

                    $response = array(
                        'status' => true,
                        'message' => 'FLO fulfillment from maedaoshi success.',
                        'status_code' => 'open',
                    );
                    return Response::json($response);
                } catch (QueryException $e) {
                    $error_code = $e->errorInfo[2];
                    $response = array(
                        'status' => false,
                        'message' => $error_code,
                    );
                    return Response::json($response);
                }
            }
        }
    }

    public function scan_maedaoshi_serial(Request $request)
    {
        $material_volume = MaterialVolume::where('material_number', '=', $request->get('material'))->first();
        $material = Material::where('material_number', '=', $request->get('material'))->first();
        $actual = $material_volume->lot_completion;

        $sax_trial = false;

        // START CEK SHIP SCH
        if ($request->get('type') != 'pd') {
            if (in_array($material->hpl, ['ASFG']) && in_array($request->get('serial'), $this->sax_trial_serial_number)) {
                $sax_trial = true;
            } else {
                $shipment_schedule = DB::table('shipment_schedules')
                    ->leftJoin('flos', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
                    ->leftJoin('material_volumes', 'shipment_schedules.material_number', '=', 'material_volumes.material_number')
                    ->where('shipment_schedules.material_number', '=', $request->get('material'))
                    ->where('shipment_schedules.destination_code', '<>', 'Y1000XJ')
                    ->orderBy('shipment_schedules.st_date', 'ASC')
                    ->select(DB::raw('if(shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0)) > material_volumes.lot_flo, material_volumes.lot_flo, shipment_schedules.quantity-sum(if(flos.actual > 0, flos.actual, 0))) as flo_quantity', 'shipment_schedules.id'))
                    ->groupBy('shipment_schedules.quantity', 'material_volumes.lot_flo', 'shipment_schedules.id')
                    ->having('flo_quantity', '>', 0)
                    ->first();

                if ($shipment_schedule) {
                    $response = array(
                        'status' => false,
                        'message' => 'There is shipment schedule for material ' . $request->get('material') . '.',
                    );
                    return Response::json($response);
                }
            }
        }
        // END CEK SHIP SCH

        $id = Auth::id();

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
        } elseif (Auth::user()->role_code == "OP-WH-Exim") {
            $printer_name = 'FLO Printer LOG';
        } elseif (Auth::user()->role_code == "S-MIS") {
            $printer_name = 'MIS';
        } else {
            $response = array(
                'status' => false,
                'message' => "You don't have permission to print FLO",
            );
            return Response::json($response);
        }

        if ($request->get('serial')) {
            $serial_number = $request->get('serial');
        } else {
            $prefix_now_pd = date("y") . date("m") . date("d");
            $code_generator_pd = CodeGenerator::where('note', '=', 'pd')->first();
            if ($prefix_now_pd != $code_generator_pd->prefix) {
                $code_generator_pd->prefix = $prefix_now_pd;
                $code_generator_pd->index = '0';
                $code_generator_pd->save();
            }
            $number_pd = sprintf("%'.0" . $code_generator_pd->length . "d", $code_generator_pd->index + 1);
            $serial_number = $code_generator_pd->prefix . $number_pd;
        }

        DB::beginTransaction();
        $scan_maedaoshi_status = false;
        if ($request->get('maedaoshi') != "") {

            try {
                $flo_detail = new FloDetail([
                    'serial_number' => $serial_number,
                    'material_number' => $request->get('material'),
                    'origin_group_code' => $material->origin_group_code,
                    'flo_number' => $request->get('maedaoshi'),
                    'quantity' => $actual,
                    'created_by' => $id,
                ]);
                $flo_detail->save();

                $inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $material->material_number, 'storage_location' => $material->issue_storage_location]);
                $inventory->quantity = ($inventory->quantity + $actual);
                $inventory->save();

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

                $flo = Flo::where('flo_number', '=', $request->get('maedaoshi'))->first();
                $flo->actual = $flo->actual + $actual;
                $flo->save();

                if ($request->get('type') == 'pd') {
                    $code_generator_pd->index = $code_generator_pd->index + 1;
                    $code_generator_pd->save();
                }

                // $log_process = LogProcess::firstOrNew([
                //     'process_code' => '5',
                //     'origin_group_code' => $material->origin_group_code,
                //     'serial_number' => $serial_number,
                //     'model' => $material->model,
                //     'manpower' => 2,
                //     'quantity' => 1,
                //     'created_by' => $id
                // ]);
                // $log_process->save();

                if ($material->origin_group_code == '041') {
                    $log_process = LogProcess::updateOrCreate(
                        [
                            'process_code' => '5',
                            'serial_number' => $serial_number,
                            'origin_group_code' => $material->origin_group_code,
                        ],
                        [
                            'model' => $material->model,
                            'manpower' => 2,
                            'quantity' => $actual,
                            'created_by' => $id,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]
                    );

                    $inventory_stamp = StampInventory::where('serial_number', '=', $serial_number)
                        ->where('origin_group_code', '=', $material->origin_group_code)
                        ->first();
                    if ($inventory_stamp != null) {
                        $inventory_stamp->forceDelete();
                    }
                }

                if ($material->origin_group_code == '043') {
                    $inventory_stamp = StampInventory::where('serial_number', '=', $serial_number)
                        ->where('origin_group_code', '=', $material->origin_group_code)
                        ->first();
                    if ($inventory_stamp != null) {
                        $inventory_stamp->forceDelete();
                    }
                }

                if ($sax_trial) {
                    $connector = new WindowsPrintConnector($printer_name);
                    $printer = new Printer($connector);

                    $printer->feed(2);
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->setTextSize(5, 7);
                    $printer->text('MAEDAOSHI' . "\n\n");
                    $printer->feed(2);

                    $printer->setTextSize(2, 3);
                    $printer->text('TRIAL CNC AS BODY' . "\n");
                    $printer->text('(ONKO HIKIAGE)' . "\n");
                    $printer->setTextSize(2, 4);
                    $printer->text($request->get('serial') . "\n\n");
                    $printer->feed(2);

                    $printer->setTextSize(2, 2);
                    $printer->text($material->material_number . "\n");
                    $printer->text($material->material_description . "\n");

                    $printer->feed(2);
                    $printer->cut();
                    $printer->close();

                }

                $scan_maedaoshi_status = true;
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

            if ($scan_maedaoshi_status) {

                // BODY INOUT
                if ($material->inout_location != "" || $material->inout_location != null) {
                    db::connection('ympimis_2')->table('kanban_inout_logs')->insert([
                        'tag' => $request->get('maedaoshi'),
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

                    $inventory_finish = db::connection('ympimis_2')->table('kanban_inout_inventories')
                        ->where('material_number', '=', $material->material_number)
                        ->where('location', '=', $material->inout_location . '-FINISH-EXPORT')
                        ->first();

                    if ($inventory_finish) {
                        db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->where('material_number', '=', $material->material_number)
                            ->where('location', '=', $material->inout_location . '-FINISH-EXPORT')
                            ->update([
                                'quantity' => $inventory_finish->quantity + $material_volume->lot_completion,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    } else {
                        db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->insert([
                                'material_number' => $material->material_number,
                                'material_description' => $material->material_description,
                                'quantity' => $material_volume->lot_completion,
                                'location' => 'FA-FINISH-EXPORT',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    }

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
                $function = 'scan_maedaoshi_serial';
                $action = 'production_result';
                $result_date = date('Y-m-d H:i:s');
                $slip_number = $request->get('maedaoshi');
                $serial_number = $serial_number;
                $material_number = $request->get('material');
                $material_description = $material->material_description;
                $issue_location = $material->issue_storage_location;
                $mstation = 'W' . $material->mrpc . 'S10';
                $quantity = $actual;
                $remark = 'MIRAI';
                $created_by = Auth::user()->username;
                $created_by_name = Auth::user()->name;
                $synced = null;
                $synced_by = null;

                app(YMESController::class)->production_result(
                    $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                // YMES END

                $response = array(
                    'status' => true,
                    'message' => 'Maedaoshi fulfillment success.',
                    'status_code' => 'open',
                    'maedaoshi' => $request->get('maedaoshi'),
                );
                return Response::json($response);

            }

        } else {

            try {
                $flo_detail = new FloDetail([
                    'serial_number' => $serial_number,
                    'material_number' => $request->get('material'),
                    'origin_group_code' => $material->origin_group_code,
                    'flo_number' => 'Maedaoshi' . $request->get('material'),
                    'quantity' => $actual,
                    'created_by' => $id,
                ]);
                $flo_detail->save();

                $flo = new Flo([
                    'flo_number' => 'Maedaoshi' . $request->get('material'),
                    'shipment_schedule_id' => 0,
                    'material_number' => $request->get('material'),
                    'quantity' => 0,
                    'status' => 'M',
                    'actual' => $actual,
                    'created_by' => $id,
                ]);
                $flo->save();

                $inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $material->material_number, 'storage_location' => $material->issue_storage_location]);
                $inventory->quantity = ($inventory->quantity + $actual);
                $inventory->save();

                //DIGITALISASI INJEKSI
                if ($material->origin_group_code == '072' && $material->category == 'FG') {
                    try {
                        $update_stock = db::select("UPDATE injection_inventories AS ii
							LEFT JOIN injection_part_details AS ipd ON ipd.gmc = ii.material_number
							SET ii.quantity = ii.quantity + " . $material_volume->lot_completion . "
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

                $flo_log = FloLog::updateOrCreate(
                    ['flo_number' => 'Maedaoshi' . $request->get('material'), 'status_code' => 'M'],
                    ['flo_number' => 'Maedaoshi' . $request->get('material'), 'created_by' => $id, 'status_code' => 'M', 'updated_at' => Carbon::now()]
                );

                if ($request->get('type') == 'pd') {
                    $code_generator_pd->index = $code_generator_pd->index + 1;
                    $code_generator_pd->save();
                }

                $connector = new WindowsPrintConnector($printer_name);
                $printer = new Printer($connector);

                if ($sax_trial) {
                    $printer->feed(2);
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->setTextSize(5, 7);
                    $printer->text('MAEDAOSHI' . "\n\n");
                    $printer->feed(2);

                    $printer->setTextSize(2, 4);
                    $printer->text('TRIAL CNC AS BODY' . "\n");
                    $printer->text('(ONKO HIKIAGE)' . "\n");
                    $printer->setTextSize(2, 4);
                    $printer->text($request->get('serial') . "\n\n");
                    $printer->feed(2);

                } else {
                    $printer->feed(2);
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->setTextSize(5, 7);
                    $printer->text('MAEDAOSHI' . "\n\n");
                    $printer->feed(2);

                    $printer->setTextSize(4, 4);
                    $printer->text('No Shipment' . "\n");
                    $printer->text('Schedule' . "\n\n");
                    $printer->feed(2);

                }

                $printer->setTextSize(2, 2);
                $printer->text($material->material_number . "\n");
                $printer->text($material->material_description . "\n");

                $printer->feed(2);
                $printer->cut();
                $printer->close();

                // $log_process = LogProcess::firstOrNew([
                //     'process_code' => '5',
                //     'origin_group_code' => $material->origin_group_code,
                //     'serial_number' => $serial_number,
                //     'model' => $material->model,
                //     'manpower' => 2,
                //     'quantity' => 1,
                //     'created_by' => $id
                // ]);
                // $log_process->save();

                if ($material->origin_group_code == '041') {
                    $log_process = LogProcess::updateOrCreate(
                        [
                            'process_code' => '5',
                            'serial_number' => $serial_number,
                            'origin_group_code' => $material->origin_group_code,
                        ],
                        [
                            'model' => $material->model,
                            'manpower' => 2,
                            'quantity' => $actual,
                            'created_by' => $id,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]
                    );

                    $inventory_stamp = StampInventory::where('serial_number', '=', $serial_number)
                        ->where('origin_group_code', '=', $material->origin_group_code)
                        ->first();
                    if ($inventory_stamp != null) {
                        $inventory_stamp->forceDelete();
                    }
                }

                $scan_maedaoshi_status = true;
                DB::commit();

            } catch (\Exception$e) {
                DB::rollback();
                $error_code = $e->getCode();
                if ($error_code == 1062 || $error_code == 23000) {
                    $message = "Serial number already exist.";
                } else {
                    $message = "Couldn't print to this printer " . $e->getMessage() . "\n.";
                }
                $response = array(
                    'status' => false,
                    'message' => $message,
                );
                return Response::json($response);
            }

            if ($scan_maedaoshi_status) {

                // YMES COMPLETION NEW
                $category = 'production_result';
                $function = 'scan_maedaoshi_serial';
                $action = 'production_result';
                $result_date = date('Y-m-d H:i:s');
                $slip_number = 'Maedaoshi' . $request->get('material');
                $serial_number = $serial_number;
                $material_number = $request->get('material');
                $material_description = $material->material_description;
                $issue_location = $material->issue_storage_location;
                $mstation = 'W' . $material->mrpc . 'S10';
                $quantity = $actual;
                $remark = 'MIRAI';
                $created_by = Auth::user()->username;
                $created_by_name = Auth::user()->name;
                $synced = null;
                $synced_by = null;

                app(YMESController::class)->production_result(
                    $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                // YMES END

                $response = array(
                    'status' => true,
                    'message' => 'New maedaoshi has been printed.',
                    'status_code' => 'new',
                    'maedaoshi' => 'Maedaoshi' . $request->get('material'),
                );
                return Response::json($response);

            }

        }

    }

    public function fetch_maedaoshi(Request $request)
    {
        $flo_details = DB::table('flo_details')
            ->leftJoin('flos', 'flo_details.flo_number', '=', 'flos.flo_number')
            ->leftJoin('materials', 'flo_details.material_number', '=', 'materials.material_number')
            ->where('flo_details.flo_number', '=', $request->get('maedaoshi'))
            ->where('flos.status', '=', 'M')
            ->select('materials.material_number', 'materials.material_description', 'flo_details.serial_number', 'flo_details.id', 'flo_details.quantity')
            ->orderBy('flo_details.id', 'DESC')
            ->get();

        return DataTables::of($flo_details)
            ->addColumn('action', function ($flo_details) {
                return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteConfirmation(id)" id="' . $flo_details->id . '"><i class="glyphicon glyphicon-trash"></i></a>';
            })
            ->make(true);
    }

    public function reprint_maedaoshi(Request $request)
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
        } elseif (Auth::user()->role_code == "S") {
            $printer_name = 'SUPERMAN';
        } elseif (Auth::user()->role_code == "S-MIS") {
            $printer_name = 'MIS';
        } elseif (Auth::user()->role_code == "OP-WH-Exim") {
            $printer_name = 'FLO Printer LOG';
        } else {
            $response = array(
                'status' => false,
                'message' => "You don't have permission to print FLO",
            );
            return Response::json($response);
        }

        $flo = DB::table('flos')
            ->leftJoin('flo_details', 'flo_details.flo_number', '=', 'flos.flo_number')
            ->leftJoin('materials', 'materials.material_number', '=', 'flo_details.material_number')
            ->where('flos.flo_number', '=', $request->get('maedaoshiReprint'))
            ->select('flo_details.material_number', 'materials.material_description')
            ->distinct()
            ->first();

        if ($flo != null) {
            try {
                $connector = new WindowsPrintConnector($printer_name);
                $printer = new Printer($connector);

                $printer->feed(2);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->setTextSize(5, 7);
                $printer->text('MAEDAOSHI' . "\n\n");
                $printer->feed(2);

                $printer->setTextSize(4, 4);
                $printer->text('No Shipment' . "\n");
                $printer->text('Schedule' . "\n\n");
                $printer->feed(2);

                $printer->setTextSize(2, 2);
                $printer->text($flo->material_number . "\n");
                $printer->text($flo->material_description . "\n");

                $printer->feed(2);
                $printer->cut();
                $printer->close();

                return back()->with('status', 'FLO has been reprinted.')->with('page', 'FLO Band Instrument');
            } catch (\Exception$e) {
                return back()->with("error", "Couldn't print to this printer " . $e->getMessage() . "\n");
            }
        } else {
            return back()->with('error', 'FLO number ' . $request->get('flo_number') . 'not found.');
        }
    }

    public function destroy_maedaoshi(Request $request)
    {
        $flo_detail = FloDetail::find($request->get('id'));
        if ($flo_detail->completion == null) {
            $material_volume = DB::table('material_volumes')
                ->where('material_volumes.material_number', '=', $flo_detail->material_number)
                ->first();
            $flo = DB::table('flos')
                ->where('flo_number', '=', $request->get('maedaoshi'))
                ->first();
            $material = DB::table('materials')
                ->where('materials.material_number', '=', $flo_detail->material_number)
                ->first();

            if (($flo->actual - $material_volume->lot_completion) <= 0) {
                $flo_delete = Flo::where('flo_number', '=', $request->get('maedaoshi'));
                $flo_delete->forceDelete();
            } else {
                $flo->actual = ($flo->actual - $material_volume->lot_completion);
                $flo->save();
            }

            $inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $material->material_number, 'storage_location' => $material->issue_storage_location]);
            $inventory->quantity = ($inventory->quantity - $material_volume->lot_completion);
            $inventory->save();

            $flo_detail->forceDelete();

            $response = array(
                'status' => true,
                'message' => "Data has been deleted.",
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'message' => "Data cannot be deleted, because data has been uploaded to SAP.",
            );
            return Response::json($response);
        }
    }
}
