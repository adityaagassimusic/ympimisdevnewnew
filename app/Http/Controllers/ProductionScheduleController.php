<?php

namespace App\Http\Controllers;

use App\CodeGenerator;
use App\FirstInventory;
use App\FloDetail;
use App\Http\Controllers\Controller;
use App\Material;
use App\ProductionCapacity;
use App\ProductionForecast;
use App\ProductionRequest;
use App\ProductionSchedule;
use App\ProductionSchedulesOneStep;
use App\ProductionSchedulesThreeStep;
use App\ProductionSchedulesTwoStep;
use App\PsiCalendar;
use App\WeeklyCalendar;
use DataTables;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

class ProductionScheduleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->ymmj = array(
            "Tuesday" => 3,
            "Wednesday" => 2,
            "Thursday" => 1,
            "Friday" => 4,
            "Saturday" => 3,
            "Sunday" => 2,
            "Monday" => 1,
        );
        $this->xy = array(
            "Wednesday" => 5,
            "Thursday" => 4,
            "Friday" => 3,
            "Saturday" => 2,
            "Sunday" => 1,
            "Monday" => 2,
            "Tuesday" => 1,
        );
    }

    public function fetchViewProductionScheduleKd(Request $request)
    {

        $month = '';
        if (strlen($request->get('month')) > 0) {
            $month = $request->get('month');
        } else {
            $month = date('Y-m');
        }

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

        $dates = WeeklyCalendar::where('week_date', 'like', '%' . $month . '%')->get();

        $materials = DB::select("SELECT DISTINCT ps.material_number, m.material_description, m.hpl, v.lot_completion FROM production_schedules_one_steps ps
            LEFT JOIN materials m ON m.material_number = ps.material_number
            LEFT JOIN material_volumes v ON v.material_number = ps.material_number
            WHERE DATE_FORMAT(ps.due_date, '%Y-%m') = '" . $month . "'
            AND m.category = 'KD'
            " . $hpl . "
            ORDER BY m.hpl, ps.material_number ASC");

        $prod_schedules = DB::select("SELECT ps.due_date, ps.material_number, m.material_description, SUM(ps.quantity) AS quantity FROM production_schedules_one_steps ps
            LEFT JOIN materials m ON m.material_number = ps.material_number
            WHERE DATE_FORMAT(ps.due_date, '%Y-%m') = '" . $month . "'
            " . $hpl . "
            GROUP BY ps.due_date, ps.material_number, m.material_description");

        $response = array(
            'status' => true,
            'dates' => $dates,
            'materials' => $materials,
            'prod_schedules' => $prod_schedules,
            'month' => date('F Y', strtotime($month . "-01")),
        );
        return Response::json($response);
    }

    public function deleteKD(Request $request)
    {
        $month = $request->get('month');
        $hpl = $request->get('hpl');

        $materials = Material::whereIn('materials.hpl', $request->get('hpl'))
            ->select('material_number')
            ->get();

        try {
            $delete = ProductionSchedulesOneStep::where('due_date', 'like', '%' . $month . '%')
                ->whereIn('material_number', $materials)
                ->delete();

            $response = array(
                'status' => true,
                'message' => 'Delete Production Schedule Successful',
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

    public function fetchViewGenerateProductionScheduleKd(Request $request)
    {

        $month = '';
        if (strlen($request->get('month')) > 0) {
            $month = $request->get('month');
        } else {
            $month = date('Y-m');
        }

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

        $dates = WeeklyCalendar::where('week_date', 'like', '%' . $month . '%')->get();

        $materials = DB::select("SELECT DISTINCT ps.material_number, m.material_description, m.hpl, v.lot_completion FROM production_schedules_two_steps ps
            LEFT JOIN materials m ON m.material_number = ps.material_number
            LEFT JOIN material_volumes v ON v.material_number = ps.material_number
            WHERE DATE_FORMAT(ps.due_date, '%Y-%m') = '" . $month . "'
            AND m.category = 'KD'
            " . $hpl . "
            ORDER BY m.hpl, ps.material_number ASC");

        $prod_schedules = DB::select("SELECT ps.due_date, ps.material_number, m.material_description, SUM(ps.quantity) AS quantity FROM production_schedules_two_steps ps
            LEFT JOIN materials m ON m.material_number = ps.material_number
            WHERE DATE_FORMAT(ps.due_date, '%Y-%m') = '" . $month . "'
            AND m.category = 'KD'
            " . $hpl . "
            GROUP BY ps.due_date, ps.material_number, m.material_description");

        $sum_step_one = DB::select("SELECT ps.material_number, m.material_description, SUM(ps.quantity) AS quantity FROM production_schedules_one_steps ps
            LEFT JOIN materials m ON m.material_number = ps.material_number
            WHERE DATE_FORMAT(ps.due_date, '%Y-%m') = '" . $month . "'
            AND m.category = 'KD'
            " . $hpl . "
            GROUP BY ps.material_number, m.material_description");

        $response = array(
            'status' => true,
            'dates' => $dates,
            'materials' => $materials,
            'prod_schedules' => $prod_schedules,
            'sum_step_one' => $sum_step_one,
            'month' => date('F Y', strtotime($month . "-01")),
        );
        return Response::json($response);
    }

    public function fetchGenerateProductionScheduleKd(Request $request)
    {

        $month = '';
        if (strlen($request->get('month')) > 0) {
            $month = $request->get('month');
        } else {
            $month = date('Y-m');
        }

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

        DB::beginTransaction();

        $delete = ProductionSchedulesTwoStep::leftJoin('materials', 'materials.material_number', 'production_schedules_two_steps.material_number')
            ->where(db::raw('date_format(production_schedules_two_steps.due_date, "%Y-%m")'), $month)
            ->whereIn('materials.hpl', $request->get('hpl'))
            ->delete();

        $materials = ProductionSchedulesOneStep::leftJoin('materials', 'materials.material_number', '=', 'production_schedules_one_steps.material_number')
            ->where(db::raw('date_format(due_date, "%Y-%m")'), $month)
            ->whereIn('materials.hpl', $request->get('hpl'))
            ->select('production_schedules_one_steps.material_number')
            ->distinct()
            ->get();

        for ($i = 0; $i < count($materials); $i++) {
            $step_one = ProductionSchedulesOneStep::leftJoin('materials', 'materials.material_number', '=', 'production_schedules_one_steps.material_number')
                ->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'production_schedules_one_steps.material_number')
                ->where(db::raw('date_format(due_date, "%Y-%m")'), $month)
                ->where('production_schedules_one_steps.material_number', $materials[$i]->material_number)
                ->select(
                    'production_schedules_one_steps.due_date',
                    'production_schedules_one_steps.material_number',
                    'material_volumes.lot_completion',
                    'production_schedules_one_steps.quantity'
                )
                ->orderBy('production_schedules_one_steps.due_date', 'ASC')
                ->get();

            $koef = 0;

            for ($j = 0; $j < count($step_one); $j++) {

                $koef += $step_one[$j]->quantity;
                $floor = floor($koef / $step_one[$j]->lot_completion);

                if ($floor > 0) {

                    $insert = new ProductionSchedulesTwoStep([
                        'due_date' => $step_one[$j]->due_date,
                        'material_number' => $step_one[$j]->material_number,
                        'quantity' => $floor * $step_one[$j]->lot_completion,
                        'created_by' => Auth::id(),
                    ]);
                    $insert->save();

                    $koef = $koef % $step_one[$j]->lot_completion;
                }
            }
        }

        if (in_array('TANPO', $hpls)) {
            $this->tanpo($month);
        } elseif (in_array('WELDING', $hpls)) {
            $this->weldingFormatter($month);
        } elseif (in_array('MPRO', $hpls)) {
            $this->mproFormatter($month);
        } elseif (in_array('SUBASSY-FL', $hpls)) {
            $impraboard_check = db::table('knock_downs_impraboards')
                ->where('remark', 'sub-assy-fl')
                ->where('month', 'LIKE', '%' . $month . '%')
                ->get();

            if (count($impraboard_check) == 0) {
                $result = $this->impraboardsFormatter($month, 'SUBASSY-FL');
                if (!$result->status) {
                    DB::rollback();
                    $response = array(
                        'status' => $result->status,
                        'message' => $result->message,
                    );
                    return Response::json($response);
                }
            }

        } elseif (in_array('SUBASSY-CL', $hpls)) {
            $impraboard_check = db::table('knock_downs_impraboards')
                ->where('remark', 'sub-assy-cl')
                ->where('month', 'LIKE', '%' . $month . '%')
                ->get();

            if (count($impraboard_check) == 0) {
                $result = $this->impraboardsFormatter($month, 'SUBASSY-CL');
                if (!$result->status) {
                    DB::rollback();
                    $response = array(
                        'status' => $result->status,
                        'message' => $result->message,
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

    public function tanpo($month)
    {
        $materials = db::table('materials')
            ->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'materials.material_number')
            ->where('materials.hpl', 'TANPO')
            ->select(
                'materials.material_number',
                'materials.material_description',
                'materials.category',
                'materials.hpl',
                'material_volumes.lot_carton'
            )
            ->get();

        $new_production_schedules = [];
        $material_numbers = [];
        for ($i = 0; $i < count($materials); $i++) {
            if (!in_array($materials[$i]->material_number, $material_numbers)) {
                array_push($material_numbers, $materials[$i]->material_number);
            }

            $step_two = ProductionSchedulesTwoStep::where(db::raw('date_format(due_date, "%Y-%m")'), $month)
                ->where('material_number', $materials[$i]->material_number)
                ->orderBy('due_date', 'ASC')
                ->get();

            $koef = 0;
            $last_due_date = '';
            for ($j = 0; $j < count($step_two); $j++) {

                $koef += $step_two[$j]->quantity;
                $floor = floor($koef / $materials[$i]->lot_carton);

                if ($floor > 0) {

                    $row = array();
                    $row['due_date'] = $step_two[$j]->due_date;
                    $row['material_number'] = $step_two[$j]->material_number;
                    $row['quantity'] = $floor * $materials[$i]->lot_carton;
                    $new_production_schedules[] = (object) $row;

                    $koef = $koef % $materials[$i]->lot_carton;
                }

                if ($step_two[$j]->quantity > 0) {
                    $last_due_date = $step_two[$j]->due_date;
                }
            }

            if ($koef > 0) {
                $row = array();
                $row['due_date'] = $last_due_date;
                $row['material_number'] = $materials[$i]->material_number;
                $row['quantity'] = $koef;
                $new_production_schedules[] = (object) $row;
            }
        }

        $delete_step_two = ProductionSchedulesTwoStep::where(db::raw('date_format(due_date, "%Y-%m")'), $month)
            ->whereIn('material_number', $material_numbers)
            ->forceDelete();

        for ($x = 0; $x < count($new_production_schedules); $x++) {
            $insert = new ProductionSchedulesTwoStep([
                'due_date' => $new_production_schedules[$x]->due_date,
                'material_number' => $new_production_schedules[$x]->material_number,
                'quantity' => $new_production_schedules[$x]->quantity,
                'created_by' => Auth::id(),
            ]);
            $insert->save();
        }

        return true;

    }

    public function mproFormatter($month)
    {
        $materials = db::table('materials')
            ->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'materials.material_number')
            ->where('materials.hpl', 'MPRO')
            ->select(
                'materials.material_number',
                'materials.material_description',
                'materials.category',
                'materials.hpl',
                'material_volumes.lot_carton'
            )
            ->get();

        $new_production_schedules = [];
        $material_numbers = [];
        for ($i = 0; $i < count($materials); $i++) {
            if (!in_array($materials[$i]->material_number, $material_numbers)) {
                array_push($material_numbers, $materials[$i]->material_number);
            }

            $step_two = ProductionSchedulesTwoStep::where(db::raw('date_format(due_date, "%Y-%m")'), $month)
                ->where('material_number', $materials[$i]->material_number)
                ->orderBy('due_date', 'ASC')
                ->get();

            $koef = 0;
            $last_due_date = '';
            for ($j = 0; $j < count($step_two); $j++) {

                $koef += $step_two[$j]->quantity;
                $floor = floor($koef / $materials[$i]->lot_carton);

                if ($floor > 0) {

                    $row = array();
                    $row['due_date'] = $step_two[$j]->due_date;
                    $row['material_number'] = $step_two[$j]->material_number;
                    $row['quantity'] = $floor * $materials[$i]->lot_carton;
                    $new_production_schedules[] = (object) $row;

                    $koef = $koef % $materials[$i]->lot_carton;
                }

                if ($step_two[$j]->quantity > 0) {
                    $last_due_date = $step_two[$j]->due_date;
                }
            }

            if ($koef > 0) {
                $row = array();
                $row['due_date'] = $last_due_date;
                $row['material_number'] = $materials[$i]->material_number;
                $row['quantity'] = $koef;
                $new_production_schedules[] = (object) $row;
            }
        }

        $delete_step_two = ProductionSchedulesTwoStep::where(db::raw('date_format(due_date, "%Y-%m")'), $month)
            ->whereIn('material_number', $material_numbers)
            ->forceDelete();

        for ($x = 0; $x < count($new_production_schedules); $x++) {
            $insert = new ProductionSchedulesTwoStep([
                'due_date' => $new_production_schedules[$x]->due_date,
                'material_number' => $new_production_schedules[$x]->material_number,
                'quantity' => $new_production_schedules[$x]->quantity,
                'created_by' => Auth::id(),
            ]);
            $insert->save();
        }

        return true;

    }

    public function impraboardsFormatter($month, $hpl)
    {

        $remark = '';
        if ($hpl == 'SUBASSY-FL') {
            $remark = 'sub-assy-fl';
        } elseif ($hpl == 'SUBASSY-CL') {
            $remark = 'sub-assy-cl';
        }

        $materials = db::table('materials')
            ->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'materials.material_number')
            ->where('materials.hpl', $hpl)
            ->where('material_volumes.cubic_meter', 'LIKE', '%0.33212%')
            ->select(
                'materials.material_number',
                'materials.material_description',
                'materials.category',
                'materials.hpl',
                'materials.issue_storage_location',
                'material_volumes.lot_completion',
                'material_volumes.cubic_meter'
            )
            ->get();

        $material_numbers = [];
        $material_formatted = [];
        for ($i = 0; $i < count($materials); $i++) {
            $material_formatted[$materials[$i]->material_number] = $materials[$i];
            array_push($material_numbers, $materials[$i]->material_number);
        }

        $destinations = db::table('destinations')
            ->whereNotNull('priority')
            ->get();

        $destination_formatted = [];
        for ($i = 0; $i < count($destinations); $i++) {
            $destination_formatted[$destinations[$i]->destination_code] = $destinations[$i];
        }

        $sales_order = db::connection('ympimis_2')
            ->table('sales_orders')
            ->where(db::raw('date_format(sales_month, "%Y-%m")'), $month)
            ->whereIn('material_number', $material_numbers)
            ->select('material_number', 'destination_code', db::raw('SUM(quantity) AS quantity'))
            ->groupBy('material_number', 'destination_code')
            ->get();

        if (count($sales_order) == 0) {
            DB::rollback();
            $response = array(
                'status' => false,
                'message' => 'Sales order belum di upload. Segera hubungi PC',
            );
            return (object) $response;
        }

        $new_sales_order = [];
        for ($i = 0; $i < count($sales_order); $i++) {
            if (isset($destination_formatted[$sales_order[$i]->destination_code])) {
                if (($sales_order[$i]->quantity % $material_formatted[$sales_order[$i]->material_number]->lot_completion) == 0) {
                    $row = array();
                    $row['priority'] = $destination_formatted[$sales_order[$i]->destination_code]->priority;
                    $row['material_number'] = $sales_order[$i]->material_number;
                    $row['material_description'] = $material_formatted[$sales_order[$i]->material_number]->material_description;
                    $row['destination_code'] = $sales_order[$i]->destination_code;
                    $row['destination_shortname'] = $destination_formatted[$sales_order[$i]->destination_code]->destination_shortname;
                    $row['quantity'] = $sales_order[$i]->quantity;
                    $row['plan'] = 0;
                    $new_sales_order[] = (object) $row;

                } else {
                    DB::rollback();
                    $response = array(
                        'status' => false,
                        'message' => 'Qty Sales order dari item ' . $sales_order[$i]->material_number . ' - ' . $material_formatted[$sales_order[$i]->material_number]->material_description . ' tidak sesuai lot request. Segera hubungi PC',
                    );
                    return (object) $response;

                }

            } else {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => 'Destinasi pada sales order tidak sesuai. Segera hubungi PC',
                );
                return (object) $response;
            }
        }

        usort($new_sales_order, function ($a, $b) {return $a->priority - $b->priority;});

        $step_two = ProductionSchedulesTwoStep::where(db::raw('date_format(due_date, "%Y-%m")'), $month)
            ->whereIn('material_number', $material_numbers)
            ->orderBy('due_date', 'ASC')
            ->get();

        $end_stock = db::table('first_inventories')
            ->where(db::raw('date_format(stock_date, "%Y-%m")'), $month)
            ->whereIn('material_number', $material_numbers)
            ->select('material_number', 'stock_date', db::raw('SUM(quantity) AS quantity'))
            ->groupBy('material_number', 'stock_date')
            ->get();

        $ps = [];
        for ($i = 0; $i < count($step_two); $i++) {
            $qty_lot = $step_two[$i]->quantity / $material_formatted[$step_two[$i]->material_number]->lot_completion;
            for ($j = 0; $j < count($qty_lot); $j++) {
                $row = array();
                $row['material_number'] = $step_two[$i]->material_number;
                $row['due_date'] = $step_two[$i]->due_date;
                $row['quantity'] = $material_formatted[$step_two[$i]->material_number]->lot_completion;
                $ps[] = (object) $row;
            }
        }

        for ($i = 0; $i < count($end_stock); $i++) {
            $qty_lot = $end_stock[$i]->quantity / $material_formatted[$end_stock[$i]->material_number]->lot_completion;
            for ($j = 0; $j < count($qty_lot); $j++) {
                $row = array();
                $row['material_number'] = $end_stock[$i]->material_number;
                $row['due_date'] = $end_stock[$i]->stock_date;
                $row['quantity'] = $material_formatted[$end_stock[$i]->material_number]->lot_completion;
                $ps[] = (object) $row;
            }
        }

        usort($ps, function ($a, $b) {return strcmp($a->due_date, $b->due_date);});

        $box = [];
        for ($i = 0; $i < count($ps); $i++) {
            for ($j = 0; $j < count($new_sales_order); $j++) {
                if ($ps[$i]->material_number == $new_sales_order[$j]->material_number && $new_sales_order[$j]->plan < $new_sales_order[$j]->quantity) {
                    $row = array();
                    $row['priority'] = $new_sales_order[$j]->priority;
                    $row['destination_code'] = $new_sales_order[$j]->destination_code;
                    $row['material_number'] = $ps[$i]->material_number;
                    $row['due_date'] = $ps[$i]->due_date;
                    $row['quantity'] = $ps[$i]->quantity;
                    $box[] = (object) $row;

                    $new_sales_order[$j]->plan = $new_sales_order[$j]->plan + $ps[$i]->quantity;

                    break;
                }
            }
        }

        for ($i = 0; $i < count($new_sales_order); $i++) {
            if ($new_sales_order[$i]->plan != $new_sales_order[$i]->quantity) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => 'Shipment tidak mencukupi, Silahkan cek schedule yang dibuat dan ending stock terlebih dahulu',
                );
                return (object) $response;
            }
        }

        usort($box, function ($a, $b) {return $a->priority - $b->priority;});

        $max = 40;
        $curr_box = [];
        $impraboards = [];
        $destination_code = '';

        for ($i = 0; $i < count($box); $i++) {

            if ($destination_code == '') {
                $destination_code = $box[$i]->destination_code;
                $curr_box[] = $box[$i];

            } else {
                if ($destination_code != $box[$i]->destination_code) {

                    $prefix_now = 'PKG-' . date('y');
                    $code_generator = CodeGenerator::where('note', '=', 'packaging standardization')->first();
                    if ($prefix_now != $code_generator->prefix) {
                        $code_generator->prefix = $prefix_now;
                        $code_generator->index = '0';
                        $code_generator->save();
                    }
                    $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
                    $packing_id = $code_generator->prefix . $number;
                    $code_generator->index = $code_generator->index + 1;
                    $code_generator->save();

                    $row = array();
                    $row['packing_id'] = $packing_id;
                    $row['destination_code'] = $destination_code;
                    $row['destination_shortname'] = $destination_formatted[$destination_code]->destination_shortname;
                    $row['max_count'] = count($curr_box);
                    $row['remark'] = $remark;
                    $row['status'] = 0;
                    $row['data'] = $curr_box;
                    $impraboards[] = (object) $row;

                    $destination_code = $box[$i]->destination_code;
                    $curr_box = [];
                    $curr_box[] = $box[$i];

                } else {

                    if (count($curr_box) < $max) {
                        $curr_box[] = $box[$i];

                    } else {
                        $prefix_now = 'PKG-' . date('y');
                        $code_generator = CodeGenerator::where('note', '=', 'packaging standardization')->first();
                        if ($prefix_now != $code_generator->prefix) {
                            $code_generator->prefix = $prefix_now;
                            $code_generator->index = '0';
                            $code_generator->save();
                        }
                        $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
                        $packing_id = $code_generator->prefix . $number;
                        $code_generator->index = $code_generator->index + 1;
                        $code_generator->save();

                        $row = array();
                        $row['packing_id'] = $packing_id;
                        $row['destination_code'] = $destination_code;
                        $row['destination_shortname'] = $destination_formatted[$destination_code]->destination_shortname;
                        $row['max_count'] = count($curr_box);
                        $row['remark'] = $remark;
                        $row['status'] = 0;
                        $row['data'] = $curr_box;
                        $impraboards[] = (object) $row;
                        $curr_box = [];

                        $curr_box[] = $box[$i];

                    }

                }
            }

        }

        if (count($curr_box) > 0) {
            $prefix_now = 'PKG-' . date('y');
            $code_generator = CodeGenerator::where('note', '=', 'packaging standardization')->first();
            if ($prefix_now != $code_generator->prefix) {
                $code_generator->prefix = $prefix_now;
                $code_generator->index = '0';
                $code_generator->save();
            }
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $packing_id = $code_generator->prefix . $number;
            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $row = array();
            $row['packing_id'] = $packing_id;
            $row['destination_code'] = $destination_code;
            $row['destination_shortname'] = $destination_formatted[$destination_code]->destination_shortname;
            $row['max_count'] = count($curr_box);
            $row['remark'] = $remark;
            $row['status'] = 0;
            $row['data'] = $curr_box;
            $impraboards[] = (object) $row;

        }

        $now = date('Y-m-d H:i:s');
        for ($i = 0; $i < count($impraboards); $i++) {
            $insert = db::table('knock_downs_impraboards')
                ->insert([
                    'month' => $month . '-01',
                    'packing_id' => $impraboards[$i]->packing_id,
                    'destination_code' => $impraboards[$i]->destination_code,
                    'destination_shortname' => $impraboards[$i]->destination_shortname,
                    'max_count' => $impraboards[$i]->max_count,
                    'remark' => $impraboards[$i]->remark,
                    'created_by' => Auth::id(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

            for ($x = 0; $x < count($impraboards[$i]->data); $x++) {
                $insert = db::table('knock_down_impraboard_details')
                    ->insert([
                        'packing_id' => $impraboards[$i]->packing_id,
                        'material_number' => $impraboards[$i]->data[$x]->material_number,
                        'material_description' => $material_formatted[$impraboards[$i]->data[$x]->material_number]->material_description,
                        'storage_location' => $material_formatted[$impraboards[$i]->data[$x]->material_number]->issue_storage_location,
                        'quantity' => $impraboards[$i]->data[$x]->quantity,
                        'created_by' => Auth::id(),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

            }
        }

        $response = array(
            'status' => true,
        );
        return (object) $response;

    }

    public function weldingFormatter($month)
    {

        $materials = db::table('materials')
            ->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'materials.material_number')
            ->where('materials.hpl', 'WELDING')
            ->where('materials.kd_name', 'KEY POST')
            ->select(
                'materials.material_number',
                'materials.material_description',
                'materials.category',
                'materials.hpl',
                'material_volumes.lot_carton'
            )
            ->get();

        $new_production_schedules = [];
        $material_numbers = [];
        for ($i = 0; $i < count($materials); $i++) {
            if (!in_array($materials[$i]->material_number, $material_numbers)) {
                array_push($material_numbers, $materials[$i]->material_number);
            }

            $step_two = ProductionSchedulesTwoStep::where(db::raw('date_format(due_date, "%Y-%m")'), $month)
                ->where('material_number', $materials[$i]->material_number)
                ->orderBy('due_date', 'ASC')
                ->get();

            $koef = 0;
            $last_due_date = '';
            for ($j = 0; $j < count($step_two); $j++) {

                $koef += $step_two[$j]->quantity;
                $floor = floor($koef / $materials[$i]->lot_carton);

                if ($floor > 0) {

                    $row = array();
                    $row['due_date'] = $step_two[$j]->due_date;
                    $row['material_number'] = $step_two[$j]->material_number;
                    $row['quantity'] = $floor * $materials[$i]->lot_carton;
                    $new_production_schedules[] = (object) $row;

                    $koef = $koef % $materials[$i]->lot_carton;
                }

                if ($step_two[$j]->quantity > 0) {
                    $last_due_date = $step_two[$j]->due_date;
                }
            }

            if ($koef > 0) {
                $row = array();
                $row['due_date'] = $last_due_date;
                $row['material_number'] = $materials[$i]->material_number;
                $row['quantity'] = $koef;
                $new_production_schedules[] = (object) $row;
            }
        }

        $delete_step_two = ProductionSchedulesTwoStep::where(db::raw('date_format(due_date, "%Y-%m")'), $month)
            ->whereIn('material_number', $material_numbers)
            ->forceDelete();

        for ($x = 0; $x < count($new_production_schedules); $x++) {
            $insert = new ProductionSchedulesTwoStep([
                'due_date' => $new_production_schedules[$x]->due_date,
                'material_number' => $new_production_schedules[$x]->material_number,
                'quantity' => $new_production_schedules[$x]->quantity,
                'created_by' => Auth::id(),
            ]);
            $insert->save();
        }

        return true;

    }

    public function fetchViewGenerateShipmentScheduleKd(Request $request)
    {

        $month = '';
        if (strlen($request->get('month')) > 0) {
            $month = $request->get('month');
        } else {
            $month = date('Y-m');
        }

        $hpl1 = '';
        if ($request->get('hpl') != null) {
            $hpls = $request->get('hpl');
            for ($i = 0; $i < count($hpls); $i++) {
                $hpl1 = $hpl1 . "'" . $hpls[$i] . "'";
                if ($i != (count($hpls) - 1)) {
                    $hpl1 = $hpl1 . ',';
                }
            }
            $hpl1 = "    ";
        }

        $dates = PsiCalendar::where('stuffing_period', 'like', '%' . $month . '%')->get();

        $materials = DB::select("SELECT r.material_number, m.material_description, m.hpl, r.destination_code, d.destination_shortname FROM production_requests r
            LEFT JOIN materials m ON r.material_number = m.material_number
            LEFT JOIN destinations d ON d.destination_code = r.destination_code
            WHERE m.category = 'KD'
            AND r.category = 'REGULER'
            AND r.destination_code IN ('Y31519', 'Y81804')
            AND DATE_FORMAT(r.request_month, '%Y-%m') = '" . $month . "'
            " . $hpl1 . "
            ORDER BY m.hpl ASC, m.material_number ASC, d.destination_shortname DESC");

        $shipments = DB::select("SELECT ps.st_date, ps.material_number, m.hpl, d.destination_shortname, SUM(ps.quantity) AS quantity FROM production_schedules_three_steps ps
            LEFT JOIN materials m ON m.material_number = ps.material_number
            LEFT JOIN destinations d ON d.destination_code = ps.destination_code
            WHERE DATE_FORMAT(ps.st_month, '%Y-%m') = '" . $month . "'
            " . $hpl1 . "
            GROUP BY ps.st_date, ps.material_number, m.hpl, d.destination_shortname
            ORDER BY m.hpl ASC, ps.material_number ASC, d.destination_shortname DESC");

        $requests = DB::select("SELECT r.material_number, d.destination_shortname, r.quantity FROM `production_requests` r
            LEFT JOIN destinations d ON d.destination_code = r.destination_code
            WHERE date_format(request_month, '%Y-%m') = '" . $month . "'
            AND r.category = 'REGULER'
            AND r.destination_code IN ('Y31519', 'Y81804')");

        $response = array(
            'status' => true,
            'dates' => $dates,
            'materials' => $materials,
            'shipments' => $shipments,
            'requests' => $requests,
            'month' => date('F Y', strtotime($month . "-01")),
        );
        return Response::json($response);
    }

    public function fetchGenerateShipmentScheduleKd(Request $request)
    {
        $month = '';
        if (strlen($request->get('month')) > 0) {
            $month = $request->get('month');
        } else {
            $month = date('Y-m');
        }

        $hpl1 = '';
        if ($request->get('hpl') != null) {
            $hpls = $request->get('hpl');
            for ($i = 0; $i < count($hpls); $i++) {
                $hpl1 = $hpl1 . "'" . $hpls[$i] . "'";
                if ($i != (count($hpls) - 1)) {
                    $hpl1 = $hpl1 . ',';
                }
            }
            $hpl1 = "AND materials.hpl IN (" . $hpl1 . ") ";
        }

        $delete_step3 = ProductionSchedulesThreeStep::leftJoin('materials', 'materials.material_number', 'production_schedules_three_steps.material_number')
        // ->where('production_schedules_three_steps.st_month', $month.'-01')
            ->where('production_schedules_three_steps.st_date', '>=', $month . '-01')
            ->whereIn('materials.hpl', $request->get('hpl'))
            ->delete();

        $update_step2 = db::select("UPDATE `production_schedules_two_steps` LEFT JOIN `materials` ON `materials`.`material_number` = `production_schedules_two_steps`.`material_number`
            SET `production_schedules_two_steps`.`st_plan` = 0
            WHERE date_format( production_schedules_two_steps.due_date, '%Y-%m' ) = '" . $month . "'"
            . $hpl1);

        $update_stock = db::select("UPDATE `first_inventories` LEFT JOIN `materials` ON `materials`.`material_number` = `first_inventories`.`material_number`
            SET `first_inventories`.`st_plan` = 0
            WHERE date_format( first_inventories.stock_date, '%Y-%m' ) = '" . $month . "'"
            . $hpl1);

        $update_request = db::select("UPDATE `production_requests` LEFT JOIN `materials` ON `materials`.`material_number` = `production_requests`.`material_number`
            SET `production_requests`.`st_plan` = 0
            WHERE production_requests.request_month = '" . $month . "-01'
            AND `production_requests`.`category` = 'REGULER'"
            . $hpl1);

        $request = ProductionRequest::leftJoin('materials', 'materials.material_number', 'production_requests.material_number')
            ->where(db::raw('date_format(production_requests.request_month, "%Y-%m")'), $month)
            ->where('production_requests.category', 'REGULER')
            ->whereIn('production_requests.destination_code', ['Y31519', 'Y81804'])
            ->whereIn('materials.hpl', $request->get('hpl'))
            ->orderBy('production_requests.material_number', 'ASC')
            ->orderBy('production_requests.priority', 'ASC')
            ->get();

        $psi_start = PsiCalendar::where('sales_period', 'like', '%' . $month . '%')->orderBy('week_date', 'ASC')->first();
        $psi_finish = PsiCalendar::where('sales_period', 'like', '%' . $month . '%')->orderBy('week_date', 'DESC')->first();

        $end_st = PsiCalendar::where('stuffing_period', 'like', '%' . $month . '%')->orderBy('week_date', 'DESC')->first();

        for ($i = 0; $i < count($request); $i++) {

            $st_plan = $request[$i]->quantity;

            $productions = DB::select("SELECT 'stock' AS type, inv.stock_date AS due_date, inv.material_number, (inv.quantity - inv.st_plan) AS quantity, m.hpl FROM first_inventories inv
                LEFT JOIN materials m ON m.material_number = inv.material_number
                WHERE inv.material_number = '" . $request[$i]->material_number . "'
                AND DATE_FORMAT(inv.stock_date,'%Y-%m') = '" . $month . "'
                AND m.category = 'KD'
                HAVING quantity > 0
                UNION
                SELECT 'plan' AS type, ps.due_date, ps.material_number, (ps.quantity - ps.st_plan) AS quantity, m.hpl FROM production_schedules_two_steps ps
                LEFT JOIN materials m ON ps.material_number = m.material_number
                WHERE ps.material_number = '" . $request[$i]->material_number . "'
                AND ps.due_date >= '" . $month . "-01'
                HAVING quantity > 0
                ORDER BY due_date ASC");

            // AND ps.due_date BETWEEN '".$psi_start->week_date."' AND '".$psi_finish->week_date."'

            for ($j = 0; $j < count($productions); $j++) {
                $koef;
                if ($request[$i]->destination_code == 'Y31519') {
                    $st_day = [2, 5];
                    $koef = $this->ymmj[date('l', strtotime($productions[$j]->due_date))];
                } else if ($request[$i]->destination_code == 'Y81804') {
                    $st_day = [1, 3];
                    $koef = $this->xy[date('l', strtotime($productions[$j]->due_date))];
                }

                $st_date = date('Y-m-d', strtotime('+' . $koef . ' day', strtotime($productions[$j]->due_date)));

                $loopAgain = false;
                do {
                    $weekly_calendar = WeeklyCalendar::where('week_date', $st_date)->first();
                    if ($weekly_calendar->remark == 'H') {
                        $st_date = date('Y-m-d', strtotime('+ 1 day', strtotime($st_date)));
                        $loopAgain = true;
                    } else {
                        $day = intval(date('N', strtotime($st_date)));
                        if (in_array($day, $st_day)) {
                            $loopAgain = false;
                        } else {
                            $st_date = date('Y-m-d', strtotime('+ 1 day', strtotime($st_date)));
                            $loopAgain = true;
                        }

                    }
                } while ($loopAgain);

                if ($st_date > $end_st->week_date) {
                    break;
                }

                $bl_date = date('Y-m-d', strtotime('+3 day', strtotime($st_date)));
                $quantity = $productions[$j]->quantity;
                $diff = $st_plan - $productions[$j]->quantity;

                if ($diff < 0) {
                    $quantity = $st_plan;
                }

                $shipment_schedule = ProductionSchedulesThreeStep::where('st_month', $month . '-01')
                    ->where('shipment_condition_code', 'C1')
                    ->where('destination_code', $request[$i]->destination_code)
                    ->where('material_number', $productions[$j]->material_number)
                    ->where('hpl', $productions[$j]->hpl)
                    ->where('st_date', $st_date)
                    ->first();

                if ($shipment_schedule) {
                    $shipment_schedule->quantity = $shipment_schedule->quantity + $quantity;
                    $shipment_schedule->save();
                } else {
                    $insert = new ProductionSchedulesThreeStep([
                        'st_month' => $month . '-01',
                        'sales_order' => $request[$i]->sales_order,
                        'shipment_condition_code' => 'C1',
                        'destination_code' => $request[$i]->destination_code,
                        'material_number' => $productions[$j]->material_number,
                        'hpl' => $productions[$j]->hpl,
                        'st_date' => $st_date,
                        'bl_date' => $bl_date,
                        'quantity' => $quantity,
                        'created_by' => Auth::id(),
                    ]);
                    $insert->save();
                }

                if ($productions[$j]->type == 'plan') {
                    $update_production = ProductionSchedulesTwoStep::where('due_date', $productions[$j]->due_date)
                        ->where('material_number', $productions[$j]->material_number)
                        ->first();

                    $update_production->st_plan = $update_production->st_plan + $quantity;
                    $update_production->save();
                } elseif ($productions[$j]->type == 'stock') {
                    $inv = FirstInventory::where(db::raw('date_format(stock_date, "%Y-%m")'), $month)
                        ->where('material_number', $productions[$j]->material_number)
                        ->orderBy('stock_date')
                        ->first();

                    $inv->st_plan = $inv->st_plan + $quantity;
                    $inv->save();
                }

                $update_request = ProductionRequest::where('request_month', $month . '-01')
                    ->where('material_number', $productions[$j]->material_number)
                    ->where('category', 'REGULER')
                    ->where('destination_code', $request[$i]->destination_code)
                    ->first();

                $update_request->st_plan = $update_request->st_plan + $quantity;
                $update_request->save();

                $st_plan = $st_plan - $quantity;
                if ($st_plan == 0) {
                    break;
                }
            }
        }

        $response = array(
            'status' => true,
        );
        return Response::json($response);
    }

    public function fetchAchievementScheduleKd(Request $request)
    {
        $month = '';
        if (strlen($request->get('month')) > 0) {
            $month = $request->get('month');
        } else {
            $month = date('Y-m');
        }

        $hpl1 = '';
        if ($request->get('hpl') != null) {
            $hpls = $request->get('hpl');
            for ($i = 0; $i < count($hpls); $i++) {
                $hpl1 = $hpl1 . "'" . $hpls[$i] . "'";
                if ($i != (count($hpls) - 1)) {
                    $hpl1 = $hpl1 . ',';
                }
            }
            $hpl1 = "AND m.hpl IN (" . $hpl1 . ") ";
        }

        $data = db::select("SELECT ps.material_number, m.material_description, m.issue_storage_location, m.hpl, SUM(ps.quantity) AS qty, SUM(ps.actual_quantity) AS act, SUM(ps.quantity - ps.actual_quantity) AS diff FROM production_schedules ps
            LEFT JOIN materials m ON ps.material_number = m.material_number
            WHERE ps.due_date LIKE '%" . $month . "%'
            AND m.category = 'KD'
            " . $hpl1 . "
            GROUP BY ps.material_number, m.material_description, m.issue_storage_location, m.hpl
            ORDER BY m.hpl, m.material_description ASC");

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);

    }

    //FG
    public function indexGenerateSchedule()
    {
        return view('production_schedules.generate_schedule')->with('page', 'Production Schedule FG');
    }

    public function generateScheduleStepOne()
    {

        $dates = WeeklyCalendar::where('week_date', 'like', '%2021-01%')
            ->where('remark', '<>', 'H')
            ->get();

        $forecasts = ProductionForecast::where(db::raw('date_format(forecast_month, "%Y-%m")'), '2021-01')
            ->where('quantity', '>', 0)
            ->get();

        $delete_temps = DB::table('production_schedules_one_steps')
            ->where(db::raw('date_format(due_date, "%Y-%m")'), '2021-01')
            ->delete();

        for ($i = 0; $i < count($forecasts); $i++) {
            if ($forecasts[$i]->quantity > 0) {
                $material_number = $forecasts[$i]->material_number;

                $capacity = DB::table('production_capacities')
                    ->where('base_model', $material_number)
                    ->where('remark', 'lot')
                    ->first();

                if ($capacity) {

                    $lot_round = floor($forecasts[$i]->quantity / $capacity->quantity);
                    $lot_mod = $forecasts[$i]->quantity % $capacity->quantity;

                    dd($lot_mod);

                    $up = ceil($lot_round / count($dates));
                    $down = floor($lot_round / count($dates));
                    $mod = $lot_round % count($dates);

                    for ($j = 0; $j < count($dates); $j++) {
                        if ($uninserted > 0) {
                            $quantity = 0;
                            if ($j < $mod) {
                                $quantity = $up * $capacity->quantity;
                            } else {
                                $quantity = $down * $capacity->quantity;
                            }

                            if ($quantity > 0) {
                                $insert = DB::table('production_schedules_one_steps')->insert([
                                    'due_date' => $dates[$j]->week_date,
                                    'material_number' => $material_number,
                                    'quantity' => $quantity,
                                    'created_by' => Auth::id(),
                                ]);

                                $uninserted -= $quantity;
                            }
                        } else {
                            break;
                        }

                    }

                } else {
                    $rounded = floor($forecasts[$i]->quantity / count($dates));
                    $mod = $forecasts[$i]->quantity % count($dates);

                    for ($j = 0; $j < count($dates); $j++) {
                        $quantity = 0;
                        if ($j < $mod) {
                            $quantity = $rounded + 1;
                        } else {
                            $quantity = $rounded;
                        }

                        if ($quantity) {
                            $insert = DB::table('production_schedules_one_steps')->insert([
                                'due_date' => $dates[$j]->week_date,
                                'material_number' => $material_number,
                                'quantity' => $quantity,
                                'created_by' => Auth::id(),
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function generateScheduleStepTwo()
    {

        $step_one = DB::table('production_schedules_one_steps')
            ->leftJoin('materials', 'materials.material_number', '=', 'production_schedules_one_steps.material_number')
            ->where('materials.hpl', 'CLFG')
            ->where(db::raw('date_format(due_date, "%Y-%m")'), '2021-01')
            ->select(
                'production_schedules_one_steps.due_date',
                'production_schedules_one_steps.material_number',
                'production_schedules_one_steps.quantity'
            )
            ->get();

        $delete_temps = DB::table('production_schedules_two_steps')
            ->where(db::raw('date_format(due_date, "%Y-%m")'), '2021-01')
            ->delete();

        $total_request = 0;

        for ($i = 0; $i < count($step_one); $i++) {
            $insert = DB::table('production_schedules_two_steps')->insert([
                'due_date' => $step_one[$i]->due_date,
                'material_number' => $step_one[$i]->material_number,
                'quantity' => $step_one[$i]->quantity,
                'created_by' => Auth::id(),
            ]);

            $total_request += $step_one[$i]->quantity;
        }

        $dates = WeeklyCalendar::where('week_date', 'like', '%2021-01%')
            ->where('remark', '<>', 'H')
            ->get();

        $step_two_silver = DB::table('production_schedules_two_steps')
            ->leftJoin('materials', 'materials.material_number', '=', 'production_schedules_two_steps.material_number')
            ->where(db::raw('date_format(production_schedules_two_steps.due_date, "%Y-%m")'), '2021-01')
            ->where('materials.base_model', 'CL SILVER')
            ->select(
                'production_schedules_two_steps.due_date',
                db::raw('SUM(production_schedules_two_steps.quantity) AS quantity')
            )
            ->groupBy('production_schedules_two_steps.due_date')
            ->get();

        //GET SILVER MAX PRODUCTION
        $silver_capacity = DB::table('production_capacities')
            ->where('base_model', 'CL SILVER')
            ->where('remark', 'capacity product')
            ->first();

        $loop = true;

        do {
            //GET SCHDULE SILVER PER HARI
            $step_two_silver = DB::table('production_schedules_two_steps')
                ->leftJoin('materials', 'materials.material_number', '=', 'production_schedules_two_steps.material_number')
                ->where(db::raw('date_format(production_schedules_two_steps.due_date, "%Y-%m")'), '2021-01')
                ->where('materials.base_model', 'CL SILVER')
                ->select(
                    'production_schedules_two_steps.due_date',
                    db::raw('SUM(production_schedules_two_steps.quantity) AS quantity')
                )
                ->groupBy('production_schedules_two_steps.due_date')
                ->get();

            for ($i = 0; $i < count($step_two_silver); $i++) {
                if ($step_two_silver[$i]->quantity > $silver_capacity->quantity) {

                    //SCHEDULE HARI H
                    $temp = DB::table('production_schedules_two_steps')
                        ->leftJoin('materials', 'materials.material_number', '=', 'production_schedules_two_steps.material_number')
                        ->where('production_schedules_two_steps.due_date', $step_two_silver[$i]->due_date)
                        ->where('materials.base_model', 'CL SILVER')
                        ->orderBy('production_schedules_two_steps.quantity', 'DESC')
                        ->get();

                    $index_date;
                    for ($j = 0; $j < count($dates); $j++) {
                        if ($dates[$j]->week_date == $step_two_silver[$i]->due_date) {
                            if ($j == count($dates) - 1) {
                                $index_date = $j;
                            } else {
                                $index_date = $j + 1;
                            }
                        }
                    }

                    $next_date = $dates[$index_date]->week_date;
                    $diff = $step_two_silver[$i]->quantity - $silver_capacity->quantity;

                    do {
                        for ($x = 0; $x < count($temp); $x++) {
                            if ($diff > 0) {
                                $lot = DB::table('production_capacities')
                                    ->where('base_model', $temp[$x]->material_number)
                                    ->where('remark', 'lot')
                                    ->first();

                                if ($lot) {
                                    $exsiting = DB::table('production_schedules_two_steps')
                                        ->where('material_number', $temp[$x]->material_number)
                                        ->where('due_date', $step_two_silver[$i]->due_date)
                                        ->first();

                                    $update_exsiting = DB::table('production_schedules_two_steps')
                                        ->where('id', $exsiting->id)
                                        ->update([
                                            'quantity' => $exsiting->quantity - $lot->quantity,
                                        ]);

                                    $diff -= $lot->quantity;

                                    $next = DB::table('production_schedules_two_steps')
                                        ->where('material_number', $temp[$x]->material_number)
                                        ->where('due_date', $next_date)
                                        ->first();

                                    if ($next) {
                                        $update_next = DB::table('production_schedules_two_steps')
                                            ->where('id', $next->id)
                                            ->update([
                                                'quantity' => $next->quantity + $lot->quantity,
                                            ]);
                                    } else {
                                        $insert = DB::table('production_schedules_two_steps')->insert([
                                            'due_date' => $next_date,
                                            'material_number' => $temp[$x]->material_number,
                                            'quantity' => $lot->quantity,
                                            'created_by' => Auth::id(),
                                        ]);
                                    }

                                } else {
                                    $exsiting = DB::table('production_schedules_two_steps')
                                        ->where('material_number', $temp[$x]->material_number)
                                        ->where('due_date', $step_two_silver[$i]->due_date)
                                        ->first();

                                    $update_exsiting = DB::table('production_schedules_two_steps')
                                        ->where('id', $exsiting->id)
                                        ->update([
                                            'quantity' => $exsiting->quantity - 1,
                                        ]);

                                    $diff--;

                                    $next = DB::table('production_schedules_two_steps')
                                        ->where('material_number', $temp[$x]->material_number)
                                        ->where('due_date', $next_date)
                                        ->first();

                                    if ($next) {
                                        $update_next = DB::table('production_schedules_two_steps')
                                            ->where('id', $next->id)
                                            ->update([
                                                'quantity' => $next->quantity + 1,
                                            ]);
                                    } else {
                                        $insert = DB::table('production_schedules_two_steps')->insert([
                                            'due_date' => $next_date,
                                            'material_number' => $temp[$x]->material_number,
                                            'quantity' => 1,
                                            'created_by' => Auth::id(),
                                        ]);
                                    }
                                }
                            }
                        }

                    } while ($diff > 0);

                    $loop = true;
                    break;
                } else {
                    $loop = false;
                }
            }
        } while ($loop);

        //GET CLFG MAX PRODUCTION
        $cl_capacity = DB::table('production_capacities')
            ->where('base_model', 'CLFG')
            ->where('remark', 'capacity product')
            ->first();

        $avg_cl_capacity = ceil($total_request / count($dates));

        $loop = true;

        do {
            //GET SCHDULE CLFG PER HARI
            $step_two_cl = DB::table('production_schedules_two_steps')
                ->leftJoin('materials', 'materials.material_number', '=', 'production_schedules_two_steps.material_number')
                ->where(db::raw('date_format(production_schedules_two_steps.due_date, "%Y-%m")'), '2021-01')
                ->where('materials.hpl', 'CLFG')
                ->select(
                    'production_schedules_two_steps.due_date',
                    db::raw('SUM(production_schedules_two_steps.quantity) AS quantity')
                )
                ->groupBy('production_schedules_two_steps.due_date')
                ->get();

            for ($i = 0; $i < count($step_two_cl); $i++) {
                if ($step_two_cl[$i]->quantity > $avg_cl_capacity) {

                    //SCHEDULE HARI H
                    $temp = DB::table('production_schedules_two_steps')
                        ->leftJoin('materials', 'materials.material_number', '=', 'production_schedules_two_steps.material_number')
                        ->where('production_schedules_two_steps.due_date', $step_two_cl[$i]->due_date)
                        ->where('materials.base_model', '<>', 'CL SILVER')
                        ->select(
                            'production_schedules_two_steps.due_date',
                            'production_schedules_two_steps.material_number',
                            'materials.base_model',
                            'production_schedules_two_steps.quantity'
                        )
                        ->orderBy('production_schedules_two_steps.quantity', 'DESC')
                        ->get();

                    $index_date;
                    for ($j = 0; $j < count($dates); $j++) {
                        if ($dates[$j]->week_date == $step_two_cl[$i]->due_date) {
                            if ($j == count($dates) - 1) {
                                $index_date = $j;
                            } else {
                                $index_date = $j + 1;
                            }
                        }
                    }

                    $next_date = $dates[$index_date]->week_date;
                    $diff = $step_two_cl[$i]->quantity - $avg_cl_capacity;

                    do {
                        for ($x = 0; $x < count($temp); $x++) {
                            if ($diff > 0) {
                                $lot = DB::table('production_capacities')
                                    ->where('base_model', $temp[$x]->material_number)
                                    ->where('remark', 'lot')
                                    ->first();

                                if ($lot) {
                                    // $exsiting = DB::table('production_schedules_two_steps')
                                    // ->where('material_number', $temp[$x]->material_number)
                                    // ->where('due_date', $step_two_cl[$i]->due_date)
                                    // ->first();

                                    // $update_exsiting = DB::table('production_schedules_two_steps')
                                    // ->where('id', $exsiting->id)
                                    // ->update([
                                    //     'quantity' => $exsiting->quantity - $lot->quantity
                                    // ]);

                                    // $diff -= $lot->quantity;

                                    // $next = DB::table('production_schedules_two_steps')
                                    // ->where('material_number', $temp[$x]->material_number)
                                    // ->where('due_date', $next_date)
                                    // ->first();

                                    // if($next){
                                    //     $update_next = DB::table('production_schedules_two_steps')
                                    //     ->where('id', $next->id)
                                    //     ->update([
                                    //         'quantity' => $next->quantity + $lot->quantity
                                    //     ]);
                                    // }else{
                                    //     $insert = DB::table('production_schedules_two_steps')->insert([
                                    //         'due_date' => $next_date,
                                    //         'material_number' => $temp[$x]->material_number,
                                    //         'quantity' => $lot->quantity,
                                    //         'created_by' => Auth::id()
                                    //     ]);
                                    // }

                                } else {
                                    $exsiting = DB::table('production_schedules_two_steps')
                                        ->where('material_number', $temp[$x]->material_number)
                                        ->where('due_date', $step_two_cl[$i]->due_date)
                                        ->first();

                                    $update_exsiting = DB::table('production_schedules_two_steps')
                                        ->where('id', $exsiting->id)
                                        ->update([
                                            'quantity' => $exsiting->quantity - 1,
                                        ]);

                                    $diff--;

                                    $next = DB::table('production_schedules_two_steps')
                                        ->where('material_number', $temp[$x]->material_number)
                                        ->where('due_date', $next_date)
                                        ->first();

                                    if ($next) {
                                        $update_next = DB::table('production_schedules_two_steps')
                                            ->where('id', $next->id)
                                            ->update([
                                                'quantity' => $next->quantity + 1,
                                            ]);
                                    } else {
                                        $insert = DB::table('production_schedules_two_steps')->insert([
                                            'due_date' => $next_date,
                                            'material_number' => $temp[$x]->material_number,
                                            'quantity' => 1,
                                            'created_by' => Auth::id(),
                                        ]);
                                    }
                                }
                            }
                        }

                    } while ($diff > 0);

                    $loop = true;
                    break;
                } else {
                    $loop = false;
                }
            }
        } while ($loop);

    }

    // NEW
    public function fetchViewScheduleBi(Request $request)
    {
        $hpls = ['CLFG', 'ASFG', 'TSFG', 'FLFG'];

        $month = $request->get('month');
        if (strlen($month) <= 0) {
            $month = date('Y-m');
        }

        $calendars = WeeklyCalendar::where('week_date', 'LIKE', '%' . $month . '%')->get();

        $forecasts = ProductionForecast::leftJoin('materials', 'materials.material_number', '=', 'production_forecasts.material_number')
            ->where('production_forecasts.forecast_month', 'LIKE', '%' . $month . '%')
            ->whereIn('materials.hpl', $hpls)
            ->where('production_forecasts.quantity', '>', 0)
            ->select(
                db::raw('date_format(production_forecasts.forecast_month, "%Y-%m") AS month'),
                'production_forecasts.material_number',
                'materials.hpl',
                db::raw('SUM(production_forecasts.quantity) AS quantity')
            )
            ->groupBy('month', 'production_forecasts.material_number', 'materials.hpl')
            ->get();

        $materials = Material::where('category', 'FG')
            ->orderBy('material_description', 'ASC')
            ->get();

        $schedules = ProductionSchedule::leftJoin('materials', 'materials.material_number', '=', 'production_schedules.material_number')
            ->where('production_schedules.due_date', 'LIKE', '%' . $month . '%')
            ->whereIn('materials.hpl', $hpls)
            ->get();

        $capacities = DB::table('production_capacities')
            ->whereIn('hpl', $hpls)
            ->where('remark', 'DAILY CAPACITY')
            ->get();

        $response = array(
            'status' => true,
            'month' => date('F Y', strtotime($month . '-01')),
            'calendars' => $calendars,
            'forecasts' => $forecasts,
            'materials' => $materials,
            'schedules' => $schedules,
            'capacities' => $capacities,
        );
        return Response::json($response);

    }

    public function generateScheduleBi(Request $request)
    {
        $hpls = $request->get('hpl');
        $month = $request->get('month');
        if (strlen($month) <= 0) {
            $month = date('Y-m');
        }

        $calendars = WeeklyCalendar::where('week_date', 'LIKE', '%' . $month . '%')
            ->where('remark', '<>', 'H')
            ->get();

        $forecasts = ProductionForecast::leftJoin('materials', 'materials.material_number', '=', 'production_forecasts.material_number')
            ->where('production_forecasts.forecast_month', 'LIKE', '%' . $month . '%')
            ->whereIn('materials.hpl', $hpls)
            ->where('production_forecasts.quantity', '>', 0)
            ->select(
                db::raw('date_format(production_forecasts.forecast_month, "%Y-%m") AS month'),
                'production_forecasts.material_number',
                'materials.hpl',
                db::raw('SUM(production_forecasts.quantity) AS quantity')
            )
            ->groupBy('month', 'production_forecasts.material_number', 'materials.hpl')
            ->get();

        try {
            DB::beginTransaction();

            $delete_temps = ProductionSchedulesOneStep::leftJoin('materials', 'materials.material_number', '=', 'production_schedules_one_steps.material_number')
                ->where(db::raw('date_format(production_schedules_one_steps.due_date, "%Y-%m")'), 'LIKE', '%' . $month . '%')
                ->whereIn('materials.hpl', $hpls)
                ->delete();

            for ($i = 0; $i < count($forecasts); $i++) {
                if ($forecasts[$i]->quantity > 0) {
                    $material_number = $forecasts[$i]->material_number;

                    $capacity = ProductionCapacity::where('base_model', $material_number)
                        ->where('remark', 'lot')
                        ->first();

                    if ($capacity) {
                        $lot_round = floor($forecasts[$i]->quantity / $capacity->quantity);
                        $lot_mod = $forecasts[$i]->quantity % $capacity->quantity;

                        $up = ceil($lot_round / count($calendars));
                        $down = floor($lot_round / count($calendars));
                        $mod = $lot_round % count($calendars);

                        for ($j = 0; $j < count($calendars); $j++) {
                            $quantity = 0;
                            if ($j < $mod) {
                                $quantity = $up * $capacity->quantity;
                            } else {
                                $quantity = $down * $capacity->quantity;
                            }

                            if (($lot_mod > 0) && ($j == ($mod))) {
                                $quantity += $lot_mod;
                            }

                            if ($quantity > 0) {
                                $insert = new ProductionSchedulesOneStep([
                                    'due_date' => $calendars[$j]->week_date,
                                    'material_number' => $material_number,
                                    'quantity' => $quantity,
                                    'created_by' => Auth::id(),
                                ]);
                                $insert->save();
                            }
                        }

                    } else {
                        $rounded = floor($forecasts[$i]->quantity / count($calendars));
                        $mod = $forecasts[$i]->quantity % count($calendars);

                        for ($j = 0; $j < count($calendars); $j++) {
                            $quantity = 0;
                            if ($j < $mod) {
                                $quantity = $rounded + 1;
                            } else {
                                $quantity = $rounded;
                            }

                            if ($quantity) {
                                $insert = new ProductionSchedulesOneStep([
                                    'due_date' => $calendars[$j]->week_date,
                                    'material_number' => $material_number,
                                    'quantity' => $quantity,
                                    'created_by' => Auth::id(),
                                ]);
                                $insert->save();
                            }
                        }
                    }
                }
            }

            if ($this->insertSchedule($hpls, $month)) {
                DB::commit();
                $response = array(
                    'status' => true,
                    'message' => 'Production schedules successfully generated',
                );
                return Response::json($response);

            } else {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => 'Insert Production schedules failed',
                );
                return Response::json($response);
            }

        } catch (Exception $e) {
            DB::rollback();

            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

    }

    public function insertSchedule($hpls, $month)
    {

        try {

            DB::beginTransaction();

            $delete = ProductionSchedule::leftJoin('materials', 'materials.material_number', '=', 'production_schedules.material_number')
                ->where(db::raw('date_format(production_schedules.due_date, "%Y-%m")'), 'LIKE', '%' . $month . '%')
                ->whereIn('materials.hpl', $hpls)
                ->forceDelete();

            $step_ones = ProductionSchedulesOneStep::leftJoin('materials', 'materials.material_number', '=', 'production_schedules_one_steps.material_number')
                ->where(db::raw('date_format(production_schedules_one_steps.due_date, "%Y-%m")'), 'LIKE', '%' . $month . '%')
                ->whereIn('materials.hpl', $hpls)
                ->select('production_schedules_one_steps.*')
                ->get();

            for ($i = 0; $i < count($step_ones); $i++) {
                $insert = new ProductionSchedule([
                    'due_date' => $step_ones[$i]->due_date,
                    'material_number' => $step_ones[$i]->material_number,
                    'quantity' => $step_ones[$i]->quantity,
                    'created_by' => Auth::id(),
                ]);
                $insert->save();
            }

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollback();
            return false;
        }

    }

    public function index()
    {
        $materials = Material::where('category', '=', 'FG')->get();

        $locations = Material::where('category', '=', 'FG')
            ->whereNotNull('hpl')
            ->select('hpl', 'category')
            ->distinct()
            ->orderBy('category', 'asc')
            ->orderBy('issue_storage_location', 'asc')
            ->orderBy('hpl', 'asc')
            ->get();

        return view('production_schedules.index', array(
            'locations' => $locations,
            'materials' => $materials,
        ))->with('page', 'Production Schedule');
    }

    public function indexKD()
    {

        $materials = Material::where('category', '=', 'KD')->get();

        $locations = Material::where('category', '=', 'KD')
            ->whereNotNull('hpl')
            ->select('hpl', 'category')
            ->distinct()
            ->orderBy('category', 'asc')
            ->orderBy('issue_storage_location', 'asc')
            ->get();

        return view('production_schedules.index_kd', array(
            'locations' => $locations,
            'materials' => $materials,
        ))->with('page', 'Production Schedule KD');

    }

    public function fetchSchedule(Request $request)
    {

        $due_date = date('Y-m-d', strtotime("first day of -2 month"));

        $production_schedules = ProductionSchedule::leftJoin("materials", "materials.material_number", "=", "production_schedules.material_number")
            ->leftJoin("origin_groups", "origin_groups.origin_group_code", "=", "materials.origin_group_code")
            ->select('production_schedules.id', 'production_schedules.material_number', 'production_schedules.due_date', 'production_schedules.quantity', 'materials.material_description', 'origin_groups.origin_group_name', 'materials.hpl')
            ->whereRaw('due_date >= "' . $due_date . '"')
            ->orderByRaw('due_date DESC', 'production_schedules.material_number ASC')
            ->where('materials.category', '=', 'FG')
            ->get();

        return DataTables::of($production_schedules)
            ->addColumn('action', function ($production_schedules) {
                return '
            <button class="btn btn-xs btn-info" data-toggle="tooltip" title="Details" onclick="modalView(' . $production_schedules->id . ')">View</button>
            <button class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit" onclick="modalEdit(' . $production_schedules->id . ')">Edit</button>
            <button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete(' . $production_schedules->id . ',\'' . $production_schedules->material_number . '\',\'' . $production_schedules->due_date . '\')">Delete</button>';
            })
            ->rawColumns(['action' => 'action'])
            ->make(true);
    }

    public function fetchScheduleNew(Request $request)
    {

        $month = $request->get('month');
        if (strlen($month) <= 0) {
            $month = date('Y-m');
        }

        $calendars = WeeklyCalendar::where('week_date', 'LIKE', '%' . $month . '%')->get();
        $prod_schedules = ProductionSchedule::where('due_date', 'LIKE', '%' . $month . '%')->get();
        $materials = Material::where('category', 'FG')
            ->orderBy('hpl', 'ASC')
            ->orderBy('material_description', 'ASC')
            ->get();

        $response = array(
            'status' => true,
            'prod_schedules' => $prod_schedules,
            'materials' => $materials,
            'calendars' => $calendars,
            'month' => date('F Y', strtotime($month . '-01')),
        );
        return Response::json($response);

    }

    public function fetchScheduleKD(Request $request)
    {

        $due_date = date('Y-m-d', strtotime("first day of -1 month"));

        $production_schedules = ProductionSchedule::leftJoin("materials", "materials.material_number", "=", "production_schedules.material_number")
            ->leftJoin("origin_groups", "origin_groups.origin_group_code", "=", "materials.origin_group_code")
            ->select('production_schedules.id', 'production_schedules.material_number', 'production_schedules.due_date', 'production_schedules.quantity', 'production_schedules.actual_quantity', 'materials.material_description', 'origin_groups.origin_group_name', 'materials.hpl')
            ->whereRaw('due_date >= "' . $due_date . '"')
            ->orderByRaw('due_date DESC', 'production_schedules.material_number ASC')
            ->where('materials.category', '=', 'KD')
            ->get();

        return DataTables::of($production_schedules)
            ->addColumn('action', function ($production_schedules) {
                return '
            <button class="btn btn-xs btn-info" data-toggle="tooltip" title="Details" onclick="modalView(' . $production_schedules->id . ')">View</button>
            <button class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit" onclick="modalEdit(' . $production_schedules->id . ')">Edit</button>
            <button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete(' . $production_schedules->id . ',\'' . $production_schedules->material_number . '\',\'' . $production_schedules->due_date . '\')">Delete</button>';
            })
            ->rawColumns(['action' => 'action'])
            ->make(true);
    }

    public function create()
    {
        $materials = Material::orderBy('material_number', 'ASC')->get();
        return view('production_schedules.create', array(
            'materials' => $materials,
        ))->with('page', 'Production Schedule');

    }

    public function store(Request $request)
    {

        $due_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->get('due_date'))));
        try
        {
            $id = Auth::id();
            $production_schedule = new ProductionSchedule([
                'material_number' => $request->get('material_number'),
                'due_date' => $due_date,
                'quantity' => $request->get('quantity'),
                'created_by' => $id,
            ]);

            $production_schedule->save();

            $response = array(
                'status' => true,
                'production_schedule' => $production_schedule,
            );
            return Response::json($response);
        } catch (QueryException $e) {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062) {
                return redirect('/index/production_schedule')->with('error', 'Production schedule with preferred due date already exist.')->with('page', 'Production Schedule');
            } else {
                return redirect('/index/production_schedule')->with('error', $e->getMessage())->with('page', 'Production Schedule');
            }
        }
    }

    public function show(Request $request)
    {
        $query = "select production_schedule.material_number, production_schedule.due_date, production_schedule.quantity, users.`name`, material_description, origin_group_name, production_schedule.created_at, production_schedule.updated_at from
 (select material_number, due_date, quantity, created_by, created_at, updated_at from production_schedules where id = "
        . $request->get('id') . ") as production_schedule
 left join materials on materials.material_number = production_schedule.material_number
 left join origin_groups on origin_groups.origin_group_code = materials.origin_group_code
 left join users on production_schedule.created_by = users.id";

        $production_schedule = DB::select($query);

        $response = array(
            'status' => true,
            'datas' => $production_schedule,
        );
        return Response::json($response);
    }

    public function fetchEdit(Request $request)
    {
        // $materials = Material::orderBy('material_number', 'ASC')->get();
        $production_schedule = ProductionSchedule::find($request->get("id"));

        $response = array(
            'status' => true,
            'datas' => $production_schedule,
        );
        return Response::json($response);
    }

    public function editKD(Request $request)
    {
        $due_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->get('due_date'))));

        try {
            $production_schedule = ProductionSchedule::find($request->get('id'));

            if ($prouction_schedule->quantity >= $request->get('actual_quantity')) {
                $production_schedule->quantity = $request->get('quantity');
                $production_schedule->save();

                $response = array(
                    'status' => true,
                    'datas' => $production_schedule,
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'datas' => $production_schedule,
                );
                return Response::json($response);
            }
        } catch (QueryException $e) {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062) {
                return redirect('/index/production_schedule')->with('error', 'Production schedule with preferred due date already exist.')->with('page', 'Production Schedule');
            } else {
                return redirect('/index/production_schedule')->with('error', $e->getMessage())->with('page', 'Production Schedule');
            }
        }
    }

    public function edit(Request $request)
    {
        $due_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->get('due_date'))));

        try {
            $production_schedule = ProductionSchedule::find($request->get('id'));
            $production_schedule->quantity = $request->get('quantity');
            $production_schedule->save();

            $response = array(
                'status' => true,
                'datas' => $production_schedule,
            );
            return Response::json($response);
        } catch (QueryException $e) {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062) {
                return redirect('/index/production_schedule')->with('error', 'Production schedule with preferred due date already exist.')->with('page', 'Production Schedule');
            } else {
                return redirect('/index/production_schedule')->with('error', $e->getMessage())->with('page', 'Production Schedule');
            }
        }
    }

    public function delete(Request $request)
    {
        $production_schedule = ProductionSchedule::find($request->get("id"));
        $production_schedule->forceDelete();

        $response = array(
            'status' => true,
        );
        return Response::json($response);
    }

    public function destroy(Request $request)
    {
        $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
        $date_to = date('Y-m-d', strtotime($request->get('dateto')));

        $materials = Material::select('material_number');

        foreach ($request->get('location') as $location) {
            $locations = explode(",", $location);

            $category = $locations[0];
            $hpl = $locations[1];

            $materials = Material::where('hpl', '=', $hpl)
                ->where('category', $category)
                ->select('material_number')
                ->get();

            try {
                $production_schedules = ProductionSchedule::where('due_date', '>=', $date_from)
                    ->where('due_date', '<=', $date_to)
                    ->whereIn('material_number', $materials)
                    ->forceDelete();

                $production_schedules = db::table('production_schedules_two_steps')
                    ->where('due_date', '>=', $date_from)
                    ->where('due_date', '<=', $date_to)
                    ->whereIn('material_number', $materials)
                    ->delete();

            } catch (\Exception$e) {
                return redirect('/index/production_schedule')->with('error', $e->getMessage())->with('page', 'Production Schedule');
            }
        }

        return redirect('/index/production_schedule')
            ->with('status', 'Production schedules has been deleted.')
            ->with('page', 'Production Schedule');
    }

    public function import(Request $request)
    {
        try {
            if ($request->hasFile('production_schedule')) {
                // ProductionSchedule::truncate();

                $id = Auth::id();

                $file = $request->file('production_schedule');
                $data = file_get_contents($file);

                $rows = explode("\r\n", $data);
                $month_uploaded = [];
                foreach ($rows as $row) {
                    if (strlen($row) > 0) {
                        $row = explode("\t", $row);
                        $production_schedule = new ProductionSchedule([
                            'material_number' => $row[0],
                            'due_date' => date('Y-m-d', strtotime(str_replace('/', '-', $row[1]))),
                            'quantity' => $row[2],
                            'created_by' => $id,
                        ]);
                        $production_schedule->save();

                        $two_step = db::table('production_schedules_two_steps')
                            ->insert([
                                'material_number' => $row[0],
                                'due_date' => date('Y-m-d', strtotime(str_replace('/', '-', $row[1]))),
                                'quantity' => $row[2],
                                'created_by' => $id,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);

                        if (!in_array(date('Y-m-d', strtotime(str_replace('/', '-', $row[1]))), $month_uploaded)) {
                            array_push($month_uploaded, date('Y-m-d', strtotime(str_replace('/', '-', $row[1]))));
                        }

                    }
                }

                if ($this->generateLotFg($month_uploaded)) {
                    return redirect('/index/production_schedule')->with('status', 'New production schedule has been imported.')->with('page', 'Production Schedule');
                } else {
                    return redirect('/index/production_schedule')->with('error', 'Error generate lot FG, tell PC and MIS.')->with('page', 'Production Schedule');
                }

            } else {
                return redirect('/index/production_schedule')->with('error', 'Please select a file.')->with('page', 'Production Schedule');
            }
        } catch (QueryException $e) {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062) {
                return back()->with('error', 'Production schedule with preferred due date already exist.')->with('page', 'Production Schedule');
            } else {
                return back()->with('error', $e->getMessage())->with('page', 'Production Schedule');
            }

        }
    }

    public function generateLotFg($month)
    {

        usort($month, function ($a, $b) {return strcmp($a, $b);});

        $min = $month[0];
        $max = $month[count($month) - 1];

        $materials = db::table('materials')
            ->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'materials.material_number')
            ->where('materials.category', 'FG')
            ->select(
                'materials.material_number',
                'materials.material_description',
                'materials.category',
                'materials.hpl',
                'material_volumes.lot_carton'
            )
            ->get();

        $last_stuffing = db::table('psi_calendars')
            ->where('stuffing_period', 'LIKE', '%' . substr($max, 0, 7) . '%')
            ->whereNotNull('stuffing_period')
            ->orderBy('week_date', 'DESC')
            ->first();

        if ($last_stuffing) {
            $new_production_schedules = [];
            $material_numbers = [];
            for ($i = 0; $i < count($materials); $i++) {
                if (!in_array($materials[$i]->material_number, $material_numbers)) {
                    array_push($material_numbers, $materials[$i]->material_number);
                }

                $ps = ProductionSchedulesTwoStep::whereBetween('due_date', [$min, $max])
                    ->where('due_date', '<=', $last_stuffing->week_date)
                    ->where('material_number', $materials[$i]->material_number)
                    ->orderBy('due_date', 'ASC')
                    ->get();

                $koef = 0;
                $last_due_date = '';
                for ($j = 0; $j < count($ps); $j++) {

                    $koef += $ps[$j]->quantity;
                    $floor = floor($koef / $materials[$i]->lot_carton);

                    if ($floor > 0) {

                        $row = array();
                        $row['due_date'] = $ps[$j]->due_date;
                        $row['material_number'] = $ps[$j]->material_number;
                        $row['quantity'] = $floor * $materials[$i]->lot_carton;
                        $new_production_schedules[] = (object) $row;

                        $koef = $koef % $materials[$i]->lot_carton;
                    }

                    if ($ps[$j]->quantity > 0) {
                        $last_due_date = $ps[$j]->due_date;
                    }
                }

                if ($koef > 0) {
                    $row = array();
                    $row['due_date'] = $last_due_date;
                    $row['material_number'] = $materials[$i]->material_number;
                    $row['quantity'] = $koef;
                    $new_production_schedules[] = (object) $row;
                }
            }

            $delete_ps = ProductionSchedulesTwoStep::whereBetween('due_date', [$min, $max])
                ->whereIn('material_number', $material_numbers)
                ->forceDelete();

            for ($x = 0; $x < count($new_production_schedules); $x++) {
                $insert = new ProductionSchedulesTwoStep([
                    'due_date' => $new_production_schedules[$x]->due_date,
                    'material_number' => $new_production_schedules[$x]->material_number,
                    'quantity' => $new_production_schedules[$x]->quantity,
                    'created_by' => Auth::id(),
                ]);
                $insert->save();
            }

            return true;

        } else {
            return false;
        }

    }

    public function importKd(Request $request)
    {

        try {
            if ($request->hasFile('production_schedule')) {
                // ProductionSchedule::truncate();

                $id = Auth::id();

                $file = $request->file('production_schedule');
                $data = file_get_contents($file);

                $rows = explode("\r\n", $data);
                foreach ($rows as $row) {
                    if (strlen($row) > 0) {
                        $row = explode("\t", $row);
                        $production_schedule = db::table('production_schedules_one_steps')
                            ->insert([
                                'material_number' => $row[0],
                                'due_date' => date('Y-m-d', strtotime(str_replace('/', '-', $row[1]))),
                                'quantity' => $row[2],
                                'created_by' => $id,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);

                    }
                }
                return redirect('/index/production_schedule_kd')->with('status', 'New production schedule has been imported.')->with('page', 'Production Schedule');
            } else {
                return redirect('/index/production_schedule_kd')->with('error', 'Please select a file.')->with('page', 'Production Schedule');
            }
        } catch (QueryException $e) {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062) {
                return back()->with('error', 'Production schedule with preferred due date already exist.')->with('page', 'Production Schedule');
            } else {
                return back()->with('error', $e->getMessage())->with('page', 'Production Schedule');
            }

        }
    }

    public function indexProductionData()
    {
        $origin_groups = DB::table('origin_groups')->get();
        $materials = Material::where('category', '=', 'FG')->orderBy('material_number', 'ASC')->get();
        $models = Material::where('category', '=', 'FG')->orderBy('model', 'ASC')->distinct()->select('model')->get();

        return view('production_schedules.data', array(
            'origin_groups' => $origin_groups,
            'materials' => $materials,
            'models' => $models,
            'title' => 'Production Schedule Data',
            'title_jp' => '生産スケジュールデータ',
        ))->with('page', 'Production Schedule');
    }

    public function fetchProductionData(Request $request)
    {
        $first = date("Y-m-01", strtotime($request->get("dateTo")));
        // $request->get("material_number");
        // $request->get("model");

        // PRODUCTION SCHEDULE
        $production_sch = ProductionSchedule::leftJoin("materials", "materials.material_number", "=", "production_schedules.material_number")
            ->where("due_date", ">=", $first)
            ->where("category", "=", "FG");

        if ($request->get("dateTo")) {
            $production_sch = $production_sch->where("due_date", "<=", $request->get("dateTo"));
        }

        if ($request->get("product_code") != "") {
            $production_sch = $production_sch->where("origin_group_code", "=", $request->get("product_code"));
        }

        $production_sch = $production_sch->select("due_date", "production_schedules.material_number", "material_description", "quantity", "origin_group_code", "model")
            ->get();

        // ACT PACKING
        $flo = FloDetail::leftJoin("materials", "materials.material_number", "=", "flo_details.material_number")
            ->where(db::raw('DATE_FORMAT(flo_details.created_at,"%Y-%m-%d")'), ">=", $first)
            ->where("category", "=", "FG");

        if ($request->get("dateTo")) {
            $flo = $flo->where(db::raw('DATE_FORMAT(flo_details.created_at,"%Y-%m-%d")'), "<=", $request->get("dateTo"));
        }

        $flo = $flo->select("flo_details.material_number", db::raw('sum(flo_details.quantity) as packing'), db::raw('DATE_FORMAT(flo_details.created_at,"%Y-%m-%d") as date'))
            ->groupBy("flo_details.material_number", db::raw('DATE_FORMAT(flo_details.created_at,"%Y-%m-%d")'))
            ->get();

        // DELIVERY
        if ($request->get("dateTo")) {
            $where = ' AND DATE_FORMAT(deliv.created_at, "%Y-%m-%d") <= "' . $request->get("dateTo") . '"';
        } else {
            $where = '';
        }

        if ($request->get("product_code") != "") {
            $product_codes = $request->get('product_code');
            $codelength = count($product_codes);
            $code = "";

            for ($x = 0; $x < $codelength; $x++) {
                $code = $code . "'" . $product_codes[$x] . "'";
                if ($x != $codelength - 1) {
                    $code = $code . ",";
                }
            }
            $where2 = " and origin_group_code in (" . $code . ") ";
        } else {
            $where2 = '';
        }

        $q_deliv = 'select * from (select flomaster.flo_number, flomaster.material_number, sum(flomaster.actual) deliv, flomaster.`status`, DATE_FORMAT(deliv.created_at, "%Y-%m-%d") date from
    (select flos.flo_number, flos.material_number, actual, `status` from flos where `status` NOT IN (0,1,"m")) as flomaster left join
    (select flo_number, created_at from flo_logs where status_code = 2) as deliv on flomaster.flo_number = deliv.flo_number
    where DATE_FORMAT(deliv.created_at, "%Y-%m-%d") >= "' . $first . '" ' . $where . '
    group by flomaster.material_number, DATE_FORMAT(deliv.created_at, "%Y-%m-%d")) alls
    left join materials on materials.material_number = alls.material_number
    where category = "FG" ' . $where2 . '
    order by alls.material_number asc, alls.date asc';

        $deliv = db::select($q_deliv);

        $response = array(
            'status' => true,
            'production_sch' => $production_sch,
            'packing' => $flo,
            'deliv' => $deliv,
        );
        return Response::json($response);
    }

    public function indexProductionMonitoring()
    {
        $origin_groups = DB::table('origin_groups')->get();

        return view('production_schedules.monitoring', array(
            'origin_groups' => $origin_groups,
            'title' => 'Production Schedule Monitoring',
            'title_jp' => '',
        ))->with('page', 'Production Schedule');
    }

    public function updateAdjustmentSchedule(Request $request)
    {

        $date = $request->get("date");

        $query = "SELECT two.material_number, two.hpl, two.qty as two_qty, COALESCE(ps.qty,0) AS ps_qty, COALESCE(ps.qty,0) - two.qty AS diff FROM
    (SELECT two.material_number, m.hpl, SUM(two.quantity) AS qty FROM production_schedules_two_steps two
    LEFT JOIN materials m ON m.material_number = two.material_number
    WHERE two.due_date >= '2021-06-01'
    AND m.hpl IN ('ASSY-SX', 'SUBASSY-SX', 'SUBASSY-FL', 'SUBASSY-CL')
    GROUP BY two.material_number, m.hpl) AS two
    LEFT JOIN
    (SELECT ps.material_number, m.hpl, SUM(ps.quantity) AS qty FROM production_schedules ps
    LEFT JOIN materials m ON m.material_number = ps.material_number
    WHERE ps.due_date >= '2021-06-01'
    AND m.hpl IN ('ASSY-SX', 'SUBASSY-SX', 'SUBASSY-FL', 'SUBASSY-CL')
    GROUP BY ps.material_number, m.hpl) AS ps
    ON ps.material_number = two.material_number
    HAVING diff <> 0
    ORDER BY diff ASC";

        $data = db::select("SELECT adj.*, v.lot_completion FROM production_schedule_adjustments adj
        LEFT JOIN material_volumes v ON v.material_number = adj.material_number");

        DB::beginTransaction();
        for ($i = 0; $i < count($data); $i++) {

            $material_number = $data[$i]->material_number;
            $diff = $data[$i]->diff;

            if ($data[$i]->diff < 0) {
                //SCHEDULE KURANG (TAMBAH DARI DEPAN)
                $production_schedule = ProductionSchedule::where(db::raw("DATE_FORMAT(due_date, '%Y-%m')"), $data[$i]->month)
                    ->where('due_date', '>=', $date)
                    ->where('material_number', $data[$i]->material_number)
                    ->orderBy('due_date', 'ASC')
                    ->get();

                for ($j = 0; $j < count($production_schedule); $j++) {
                    if ($diff < 0) {
                        $update = ProductionSchedule::where('due_date', $production_schedule[$j]->due_date)
                            ->where('material_number', $data[$i]->material_number)
                            ->first();

                        try {
                            $update->quantity = $update->quantity + $data[$i]->lot_completion;
                            $update->save();

                            $diff = $diff + $data[$i]->lot_completion;
                            $adjument = db::table('production_schedule_adjustments')
                                ->where('material_number', $data[$i]->material_number)
                                ->update([
                                    'diff' => $diff,
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
            } else {
                //SCHEDULE KEBANYAKAN (KURANGI DARI BELAKANG)
                $production_schedule = ProductionSchedule::where(db::raw("DATE_FORMAT(due_date, '%Y-%m')"), $data[$i]->month)
                    ->where('material_number', $data[$i]->material_number)
                    ->orderBy('due_date', 'DESC')
                    ->get();

                for ($j = 0; $j < count($production_schedule); $j++) {
                    if ($diff > 0) {
                        $update = ProductionSchedule::where('due_date', $production_schedule[$j]->due_date)
                            ->where('material_number', $data[$i]->material_number)
                            ->first();

                        if (($update->quantity - $update->actual_quantity) >= $data[$i]->lot_completion) {
                            try {
                                $update->quantity = $update->quantity - $data[$i]->lot_completion;
                                $update->save();

                                array_push($error_count, 'Data not number ' . $material);

                                $diff = $diff - $data[$i]->lot_completion;
                                $adjument = db::table('production_schedule_adjustments')
                                    ->where('material_number', $data[$i]->material_number)
                                    ->update([
                                        'diff' => $diff,
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
                }
            }
        }

        DB::commit();
        $response = array(
            'status' => true,
        );
        return Response::json($response);

    }

    public function updateAdjustmentScheduleNew(Request $request)
    {
        $month = $request->get("month");

        $hpl = '';
        if ($request->get('hpl') != null) {
            $hpls = $request->get('hpl');
            for ($i = 0; $i < count($hpls); $i++) {
                $hpl = $hpl . "'" . $hpls[$i] . "'";
                if ($i != (count($hpls) - 1)) {
                    $hpl = $hpl . ',';
                }
            }
        }

        $kd = db::select("SELECT kdd.material_number, m.material_description, m.hpl, SUM(kdd.quantity) AS quantity  FROM knock_down_details kdd
        LEFT JOIN materials m ON m.material_number = kdd.material_number
        WHERE DATE_FORMAT(kdd.created_at, '%Y-%m') = '" . $month . "'
        AND m.hpl IN (" . $hpl . ")
        AND LENGTH(kdd.kd_number) = 10
        AND kdd.created_by != '2690'
        GROUP BY kdd.material_number, m.material_description, m.hpl");

        $delete_ps = db::select("SELECT ps.id, ps.due_date, ps.material_number, m.material_description, m.hpl, ps.quantity, ps.actual_quantity FROM production_schedules ps
        LEFT JOIN materials m ON m.material_number = ps.material_number
        WHERE DATE_FORMAT(ps.due_date, '%Y-%m') = '" . $month . "'
        AND m.hpl IN (" . $hpl . ")");

        $two = db::select("SELECT ps.due_date, ps.material_number, m.material_description, m.hpl, SUM(ps.quantity) AS quantity, SUM(ps.actual_quantity) AS actual_quantity FROM production_schedules_two_steps ps
        LEFT JOIN materials m ON m.material_number = ps.material_number
        WHERE DATE_FORMAT(ps.due_date, '%Y-%m') = '" . $month . "'
        AND m.hpl IN (" . $hpl . ")
        GROUP BY ps.due_date, ps.material_number, m.material_description, m.hpl");

        DB::beginTransaction();
        //Delete Adjustment
        try {
            $delete = DB::table('production_schedule_adjustments')
                ->where('month', $month)
                ->whereIn('hpl', $request->get('hpl'))
                ->delete();

        } catch (Exception $e) {
            DB::rollback();
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        //Insert Adjustment
        for ($i = 0; $i < count($kd); $i++) {
            try {
                $insert_adj = db::table('production_schedule_adjustments')
                    ->insert([
                        'month' => $month,
                        'material_number' => $kd[$i]->material_number,
                        'material_description' => $kd[$i]->material_description,
                        'hpl' => $kd[$i]->hpl,
                        'actual_quantity' => $kd[$i]->quantity,
                        'diff' => $kd[$i]->quantity,
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

        //Delete Production Schedule
        for ($i = 0; $i < count($delete_ps); $i++) {
            try {
                $del = ProductionSchedule::find($delete_ps[$i]->id);
                $del->forceDelete();
            } catch (Exception $e) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }

        // Insert Step Two -> Production Schedule
        for ($i = 0; $i < count($two); $i++) {
            try {
                $insert = new ProductionSchedule([
                    'material_number' => $two[$i]->material_number,
                    'due_date' => $two[$i]->due_date,
                    'quantity' => $two[$i]->quantity,
                    'created_by' => Auth::id(),
                    'created_by' => Auth::id(),
                    'updated_at' => Auth::id(),
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

        $adjustment = db::select("SELECT * FROM production_schedule_adjustments
        WHERE `month` = '" . $month . "'
        AND hpl IN (" . $hpl . ")");

        for ($i = 0; $i < count($adjustment); $i++) {
            $quantity = $adjustment[$i]->actual_quantity;
            $add = $adjustment[$i]->quantity_added;
            $diff = $adjustment[$i]->diff;

            $ps_edit = db::select("SELECT * FROM production_schedules
            WHERE DATE_FORMAT(due_date, '%Y-%m') = '" . $month . "'
            AND material_number = '" . $adjustment[$i]->material_number . "'
            ORDER BY due_date ASC");

            for ($j = 0; $j < count($ps_edit); $j++) {
                if ($diff > 0) {
                    if ($diff > $ps_edit[$j]->quantity) {
                        try {
                            $update = ProductionSchedule::where('due_date', $ps_edit[$j]->due_date)
                                ->where('material_number', $ps_edit[$j]->material_number)
                                ->first();
                            $update->actual_quantity = $update->quantity;
                            $update->save();

                            $add = $add + $ps_edit[$j]->quantity;
                            $diff = $diff - $ps_edit[$j]->quantity;
                        } catch (Exception $e) {
                            DB::rollback();
                            $response = array(
                                'status' => false,
                                'message' => $e->getMessage(),
                            );
                            return Response::json($response);
                        }

                    } else {
                        try {
                            $update = ProductionSchedule::where('due_date', $ps_edit[$j]->due_date)
                                ->where('material_number', $ps_edit[$j]->material_number)
                                ->first();
                            $update->actual_quantity = $update->actual_quantity + $diff;
                            $update->save();

                            $add = $add + $diff;
                            $diff = $diff - $diff;
                        } catch (Exception $e) {
                            DB::rollback();
                            $response = array(
                                'status' => false,
                                'message' => $e->getMessage(),
                            );
                            return Response::json($response);
                        }
                    }

                    try {
                        $update_adj = db::table('production_schedule_adjustments')
                            ->where('month', $month)
                            ->where('material_number', $ps_edit[$j]->material_number)
                            ->update([
                                'quantity_added' => $add,
                                'diff' => $diff,
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
        }

        DB::commit();
        $response = array(
            'status' => true,
        );
        return Response::json($response);

    }

}
