<?php

namespace App\Http\Controllers;

use App\Destination;
use App\FirstInventory;
use App\Http\Controllers\Controller;
use App\Material;
use App\ProductionSchedulesThreeStep;
use App\ProductionSchedulesTwoStep;
use App\PsiCalendar;
use App\ShipmentSchedule;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

class ShipmentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->day = array(
            'ITM-YCA' => [1, 3, 4],
            'ITM-HB' => [1, 3, 4],
            'ITM-HB-CASE' => [1, 3, 4],
            'ITM-LH' => [1, 3, 4],
            'ITM-KOREA' => [1, 3, 4],
            'ITM-SIAM' => [1, 3, 4],
            'YME UK' => [1, 3, 4],
            'JH' => [1, 2, 3, 4, 5],
            'YMID' => [5],
            'YMJ' => [2, 5],
            'YCJ' => [2, 5],
            'YMMJ' => [2, 5],
            'XY' => [1, 3],
            'YEMI' => [5],
        );

        $this->logic = array(
            'ITM-YCA' => [
                'Monday' => 2,
                'Tuesday' => 1,
                'Wednesday' => 1,
                'Thursday' => 4,
                'Friday' => 3,
                'Saturday' => 2,
                'Sunday' => 1,
            ],
            'ITM-HB' => [
                'Monday' => 2,
                'Tuesday' => 1,
                'Wednesday' => 1,
                'Thursday' => 4,
                'Friday' => 3,
                'Saturday' => 2,
                'Sunday' => 1,
            ],
            'ITM-HB-CASE' => [
                'Monday' => 2,
                'Tuesday' => 1,
                'Wednesday' => 1,
                'Thursday' => 4,
                'Friday' => 3,
                'Saturday' => 2,
                'Sunday' => 1,
            ],
            'ITM-LH' => [
                'Monday' => 2,
                'Tuesday' => 1,
                'Wednesday' => 1,
                'Thursday' => 4,
                'Friday' => 3,
                'Saturday' => 2,
                'Sunday' => 1,
            ],
            'ITM-KOREA' => [
                'Monday' => 2,
                'Tuesday' => 1,
                'Wednesday' => 1,
                'Thursday' => 4,
                'Friday' => 3,
                'Saturday' => 2,
                'Sunday' => 1,
            ],
            'ITM-SIAM' => [
                'Monday' => 2,
                'Tuesday' => 1,
                'Wednesday' => 1,
                'Thursday' => 4,
                'Friday' => 3,
                'Saturday' => 2,
                'Sunday' => 1,
            ],
            'YME UK' => [
                'Monday' => 2,
                'Tuesday' => 1,
                'Wednesday' => 1,
                'Thursday' => 4,
                'Friday' => 3,
                'Saturday' => 2,
                'Sunday' => 1,
            ],
            'JH' => [
                'Monday' => 1,
                'Tuesday' => 1,
                'Wednesday' => 1,
                'Thursday' => 1,
                'Friday' => 3,
                'Saturday' => 2,
                'Sunday' => 1,
            ],
            'YMID' => [
                'Monday' => 4,
                'Tuesday' => 3,
                'Wednesday' => 2,
                'Thursday' => 1,
                'Friday' => 7,
                'Saturday' => 6,
                'Sunday' => 5,
            ],
            'YMJ' => [
                'Monday' => 1,
                'Tuesday' => 3,
                'Wednesday' => 2,
                'Thursday' => 1,
                'Friday' => 4,
                'Saturday' => 3,
                'Sunday' => 2,
            ],
            'YCJ' => [
                'Monday' => 1,
                'Tuesday' => 3,
                'Wednesday' => 2,
                'Thursday' => 1,
                'Friday' => 4,
                'Saturday' => 3,
                'Sunday' => 2,
            ],
            'YMMJ' => [
                'Monday' => 4,
                'Tuesday' => 3,
                'Wednesday' => 2,
                'Thursday' => 5,
                'Friday' => 4,
                'Saturday' => 3,
                'Sunday' => 2,
            ],
            'XY' => [
                'Monday' => 2,
                'Tuesday' => 6,
                'Wednesday' => 5,
                'Thursday' => 4,
                'Friday' => 3,
                'Saturday' => 2,
                'Sunday' => 1,
            ],
            'YEMI' => [
                'Monday' => 4,
                'Tuesday' => 3,
                'Wednesday' => 2,
                'Thursday' => 8,
                'Friday' => 7,
                'Saturday' => 6,
                'Sunday' => 5,
            ],
        );

    }

    public function indexBackOrder()
    {
        $title = "Back Order";
        $title_jp = "";

        return view(
            'shipments.back_order',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        );
    }

    public function indexSalesByDestination()
    {
        $title = "Sales By Destination";
        $title_jp = "仕向けによる売り上げ";

        $weeks = db::select("SELECT DISTINCT
               fiscal_year,
               DATE_FORMAT( week_date, '%M' ) AS bulan,
               DATE_FORMAT( week_date, '%Y-%m' ) AS indek
               FROM
               weekly_calendars
               WHERE
               week_date >= '2023-04-01'
               AND week_date <= '" . date('Y-m-d') . "'
               ORDER BY
               week_date DESC");

        return view(
            'displays.shippings.sales_by_destination',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'weeks' => $weeks,
            )
        );
    }

    public function fetchBackOrder(Request $request)
    {

        $month = date('Y-m-01');
        if (strlen($request->get('month')) > 0) {
            $month = date('Y-m-01', strtotime($request->get('month')));
        }

        $backorder = db::table('sales_backorders')
            ->where('sales_month', '=', $month)
            ->select(
                'sales_backorders.*',
                db::raw('DATE_FORMAT(sales_backorders.sales_month, "%b-%Y") as sales_month_txt')
            )
            ->get();

        $response = array(
            'status' => true,
            'backorder' => $backorder,
        );
        return Response::json($response);

    }

    public function fetchSalesByDestination(Request $request)
    {
        try {
            $month = date('Y-m-01');
            if (strlen($request->get('period')) > 0) {
                $month = date('Y-m-01', strtotime($request->get('period')));
            }
            $month_backorder = date('Y-m-01', strtotime("-1 month", strtotime($month)));
            $period_title = "Periode " . date('F Y', strtotime($month));

            $weekly_calendar = db::table('weekly_calendars')->where('week_date', '=', $month)->first();

            $datas = db::select("SELECT
                	r.st_month,
                	IF(r.destination_shortname = 'YMJ' or r.destination_shortname = 'YCJ', 'YMJ & YCJ', r.destination_shortname) as destination_shortname,
                	r.material_number,
                	m.material_description,
                	m.hpl,
                	sum( target_quantity_sales ) AS target_quantity_sales,
                	sum( target_sales ) AS target_sales,
                	sum( target_quantity_backorder ) AS target_quantity_backorder,
                	sum( target_backorder ) AS target_backorder,
                	sum( actual_quantity_st ) AS actual_quantity_st,
                	sum( actual_sales_st ) AS actual_sales_st,
                	sum( actual_quantity_bl ) AS actual_quantity_bl,
                	sum( actual_sales_bl ) AS actual_sales_bl
                FROM
                	(
                	SELECT
                		ss.st_month,
                		IF(d.destination_shortname = 'YMJ' or d.destination_shortname = 'YCJ', 'YMJ & YCJ', d.destination_shortname) AS destination_shortname,
                		ss.material_number,
                		ss.quantity AS target_quantity_sales,
                		IF( d.destination_shortname = 'YMID', ss.quantity * sp.sales_price_ymid, ss.quantity * sp.sales_price_other ) AS target_sales,
                		0 AS target_quantity_backorder,
                		0 AS target_backorder,
                		0 AS actual_quantity_st,
                		0 AS actual_sales_st,
                		0 AS actual_quantity_bl,
                		0 AS actual_sales_bl
                	FROM
                		shipment_schedules AS ss
                		LEFT JOIN (
                		SELECT
                			sales_price.period,
                			sales_price.material_number,
                			sum( sales_price.sales_price_ymid ) AS sales_price_ymid,
                			sum( sales_price.sales_price_other ) AS sales_price_other
                		FROM
                			(
                			SELECT
                				sp.fiscal_year,
                				sp.MONTH AS period,
                				sp.material_number,
                				sp.price * se.idr_to_usd AS sales_price_ymid,
                				0 AS sales_price_other
                			FROM
                				sales_prices_bk AS sp
                				LEFT JOIN sales_exchange_rates AS se ON se.period = sp.MONTH
                			WHERE
                				sp.MONTH = '" . $month . "'
                				AND sp.sales_category = 'YMID' UNION ALL
                			SELECT
                				sp.fiscal_year,
                				sp.MONTH AS period,
                				sp.material_number,
                				0 AS sales_price_ymid,
                				sp.price AS sales_price_other
                			FROM
                				sales_prices_bk AS sp
                			WHERE
                				sp.MONTH = '" . $month . "'
                				AND sp.sales_category = 'ALL'
                			) AS sales_price
                		GROUP BY
                			sales_price.period,
                			sales_price.material_number
                		) AS sp ON ss.material_number = sp.material_number
                		LEFT JOIN destinations AS d ON d.destination_code = ss.destination_code
                		LEFT JOIN materials AS m ON m.material_number = ss.material_number
                	WHERE
                		st_month = '" . $month . "' UNION ALL
                    SELECT
                    	sb.sales_month AS st_month,
                    	sb.destination_shortname,
                    	sb.material_number,
                    	0 AS target_quantity_sales,
                    	0 AS target_sales,
                    	sb.quantity AS target_quantity_backorder,
                    	sb.quantity * sb.price AS target_backorder,
                    	0 AS actual_quantity_st,
                    	0 AS actual_sales_st,
                    	0 AS actual_quantity_bl,
                    	0 AS actual_sales_bl
                    FROM
                    	sales_backorders AS sb
                    WHERE
                    	sb.sales_month = '" . $month_backorder . "' UNION ALL
                	SELECT
                		date_format( sr.bl_date, '%Y-%m-01' ) AS st_month,
                		IF(sr.destination_shortname = 'YMJ' or sr.destination_shortname = 'YCJ', 'YMJ & YCJ', sr.destination_shortname) AS destination_shortname,
                		sr.material_number,
                		IF(sr.category = 'EXTRA ORDER', sr.quantity, 0) AS target_quantity_sales,
                		IF(sr.category = 'EXTRA ORDER', sr.quantity*sr.price, 0) AS target_sales,
                		0 AS target_quantity_backorder,
                		0 AS target_backorder,
                		0 AS actual_quantity_st,
                		0 AS actual_sales_st,
                		sr.quantity AS actual_quantity_bl,
                		sr.quantity*sr.price AS actual_sales_bl
                	FROM
                		sales_resumes AS sr
                	WHERE
                		date_format( sr.bl_date, '%Y-%m-01' ) = '" . $month . "' UNION ALL
                	SELECT
                		date_format( sr.st_date, '%Y-%m-01' ) AS st_month,
                		IF(sr.destination_shortname = 'YMJ' or sr.destination_shortname = 'YCJ', 'YMJ & YCJ', sr.destination_shortname) AS destination_shortname,
                		sr.material_number,
                		0 AS target_quantity_sales,
                		0 AS target_sales,
                		0 AS target_quantity_backorder,
                		0 AS target_backorder,
                		sr.quantity AS actual_quantity_st,
                		sr.quantity*sr.price AS actual_sales_st,
                		0 AS actual_quantity_bl,
                		0 AS actual_sales_bl
                	FROM
                		sales_resumes AS sr
                	WHERE
                		date_format( sr.st_date, '%Y-%m-01' ) = '" . $month . "'
                	) AS r
                	LEFT JOIN materials AS m ON m.material_number = r.material_number
                GROUP BY
                	r.st_month,
                	r.destination_shortname,
                	r.material_number,
                	m.material_description,
                	m.hpl
                ORDER BY
                	destination_shortname ASC,
                	material_number ASC");

            $response = array(
                'status' => true,
                'fiscal_year' => $weekly_calendar->fiscal_year,
                'period' => $month,
                'period_title' => $period_title,
                'datas' => $datas,
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

    public function indexShipmentProgress()
    {
        $title = "Shipment Progress";
        $title_jp = "出荷結果";

        return view(
            'shipments.shipment_progress',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', $title)->with('head', $title);
    }

    public function indexShipmentMenu()
    {
        $title = "Shipment Control";
        $title_jp = "出荷管理";

        return view(
            'shipments.shipment_menu',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', $title)->with('head', $title);

    }

    public function indexShipmentCubication()
    {

        $title = "Shipment Cubication (Draft Shipment Schedule)";
        $title_jp = "";

        $destinations = Destination::whereNotNull('priority')
            ->orderBy('priority', 'asc')
            ->get();

        return view(
            'shipments.generate_cubication',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'destinations' => $destinations,
            )
        )->with('page', $title)->with('head', $title);

    }

    public function indexShipmentSchedule($category)
    {

        if (strtoupper($category) == 'KD') {
            $title = "Generate Shipment KD & SP";
            $title_jp = "";

        } elseif (strtoupper($category) == 'FG') {
            $title = "Generate Shipment FG";
            $title_jp = "";

        }

        $locations = Material::where('category', '=', strtoupper($category))
            ->whereNotNull('hpl')
            ->select('hpl', 'category')
            ->distinct()
            ->orderBy('category', 'asc')
            ->orderBy('hpl', 'asc')
            ->get();

        return view(
            'shipments.generate_shipment',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'locations' => $locations,
                'category' => strtoupper($category),
            )
        )->with('page', $title)->with('head', $title);

    }

    public function fetchShipmentSchedule(Request $request)
    {

        $month = '';
        if (strlen($request->get('month')) > 0) {
            $month = $request->get('month');
        } else {
            $month = date('Y-m');
        }

        $dates = PsiCalendar::where('stuffing_period', 'like', '%' . $month . '%')->get();

        $destinations = Destination::get();

        $materials = Material::where('category', $request->get('category'))->get();

        $shipments = DB::select("SELECT ps.sales_order, ps.st_date, ps.material_number, ps.destination_code, SUM(ps.quantity) AS quantity FROM production_schedules_three_steps ps
            WHERE DATE_FORMAT(ps.st_month, '%Y-%m') = '" . $month . "'
            GROUP BY ps.sales_order, ps.st_date, ps.material_number, ps.destination_code");

        $sales_orders = DB::connection('ympimis_2')
            ->select("SELECT sales_order, material_number, destination_code, SUM(quantity) AS quantity FROM sales_orders
                        WHERE date_format(sales_month, '%Y-%m') = '" . $month . "'
                    GROUP BY sales_order, material_number, destination_code");

        $new_sales_order = [];
        for ($i = 0; $i < count($sales_orders); $i++) {

            $this_materials = false;
            $material_description = '';
            $hpl = '';
            for ($j = 0; $j < count($materials); $j++) {
                if ($sales_orders[$i]->material_number == $materials[$j]->material_number) {
                    $this_materials = true;
                    $material_description = $materials[$j]->material_description;
                    $hpl = $materials[$j]->hpl;
                    break;
                }
            }

            if ($this_materials) {
                $row = array();
                $row['sales_order'] = $sales_orders[$i]->sales_order;
                $row['material_number'] = $sales_orders[$i]->material_number;
                $row['material_description'] = $material_description;
                $row['hpl'] = $hpl;
                $row['destination_code'] = $sales_orders[$i]->destination_code;
                $destination_shortname = '';
                for ($j = 0; $j < count($destinations); $j++) {
                    if ($sales_orders[$i]->destination_code == $destinations[$j]->destination_code) {
                        $destination_shortname = $destinations[$j]->destination_shortname;
                        break;
                    }
                }
                $row['destination_shortname'] = $destination_shortname;
                $row['quantity'] = $sales_orders[$i]->quantity;
                $row['concat'] = $hpl . '_' . $sales_orders[$i]->material_number . '_' . $destination_shortname;

                $new_sales_order[] = (object) $row;
            }
        }
        usort($new_sales_order, function ($a, $b) {
            return strcmp($a->concat, $b->concat);
        });

        $response = array(
            'status' => true,
            'month' => $month,
            'dates' => $dates,
            'shipments' => $shipments,
            'sales_orders' => $new_sales_order,
        );
        return Response::json($response);
    }

    public function fetchShipmentCubication(Request $request)
    {

        $month = '';
        if (strlen($request->get('month')) > 0) {
            $month = $request->get('month');
        } else {
            $month = date('Y-m');
        }

        $dates = PsiCalendar::where('stuffing_period', 'like', '%' . $month . '%')->get();

        $materials = db::table('materials')
            ->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'materials.material_number')
            ->whereIn('materials.category', ['KD', 'FG'])
            ->select(
                'materials.material_number',
                'materials.material_description',
                'materials.category',
                'materials.hpl',
                'material_volumes.lot_pallet',
                'material_volumes.length_pallet',
                'material_volumes.width_pallet',
                'material_volumes.height_pallet',
                'material_volumes.cubic_meter_pallet',
                'material_volumes.lot_carton',
                'material_volumes.length',
                'material_volumes.width',
                'material_volumes.height',
                'material_volumes.cubic_meter'
            )
            ->get();
        $formatted_materials = [];
        for ($i = 0; $i < count($materials); $i++) {
            $formatted_materials[$materials[$i]->material_number] = $materials[$i];
        }

        $destinations = db::table('destinations')
            ->whereNotNull('priority')
            ->get();

        $shipment = db::table('production_schedules_four_steps')
            ->select(
                'st_month',
                'sales_order',
                'destination_code',
                'shipment_condition_code',
                'material_number',
                'hpl',
                'st_date',
                'bl_date',
                db::raw('SUM(quantity) AS quantity'),
                db::raw('SUM(volume) AS volume')
            )
            ->groupBy(
                'st_month',
                'sales_order',
                'destination_code',
                'shipment_condition_code',
                'material_number',
                'hpl',
                'st_date',
                'bl_date'
            )
            ->where('st_month', 'LIKE', '%' . $month . '%')
            ->get();

        $new_shipment = [];
        for ($i = 0; $i < count($shipment); $i++) {
            $destination_shortname = '';
            $remark = '';
            for ($j = 0; $j < count($destinations); $j++) {
                if ($shipment[$i]->destination_code == $destinations[$j]->destination_code) {
                    $destination_shortname = $destinations[$j]->destination_shortname;
                    $remark = $destinations[$j]->remark;
                    break;
                }
            }

            $material_description = '';
            if (isset($formatted_materials[$shipment[$i]->material_number])) {
                $material_description = $formatted_materials[$shipment[$i]->material_number]->material_description;
            }

            $row = array();
            $row['st_month'] = $shipment[$i]->st_month;
            $row['sales_order'] = $shipment[$i]->sales_order;
            $row['destination_code'] = $shipment[$i]->destination_code;
            $row['destination_shortname'] = $destination_shortname;
            $row['remark'] = $remark;
            $row['shipment_condition_code'] = $shipment[$i]->shipment_condition_code;
            $row['material_number'] = $shipment[$i]->material_number;
            $row['material_description'] = $material_description;
            $row['hpl'] = $shipment[$i]->hpl;
            $row['st_date'] = $shipment[$i]->st_date;
            $row['bl_date'] = $shipment[$i]->bl_date;
            $row['quantity'] = $shipment[$i]->quantity;
            $row['volume'] = $shipment[$i]->volume;

            $new_shipment[] = (object) $row;

        }

        $response = array(
            'status' => true,
            'month' => $month,
            'dates' => $dates,
            'shipments' => $new_shipment,
        );
        return Response::json($response);

    }

    public function generateShipmentSchedule(Request $request)
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

        DB::beginTransaction();
        DB::connection('ympimis_2')->beginTransaction();

        try {
            $delete_prod_step3 = db::table('production_schedules_three_steps')
                ->where('production_schedules_three_steps.st_date', 'LIKE', '%' . $month . '%')
                ->whereIn('production_schedules_three_steps.hpl', $request->get('hpl'))
                ->delete();

            $update_prod_step2 = db::table('production_schedules_two_steps')
                ->where('production_schedules_two_steps.due_date', 'LIKE', '%' . $month . '%')
                ->update([
                    'production_schedules_two_steps.st_plan' => 0,
                ]);

            $update_ending_stocks = db::table('first_inventories')
                ->where('first_inventories.stock_date', 'LIKE', '%' . $month . '%')
                ->update([
                    'first_inventories.st_plan' => 0,
                ]);

            $update_sales_order = db::connection('ympimis_2')
                ->table('sales_orders')
                ->where('sales_orders.sales_month', 'LIKE', '%' . $month . '%')
                ->update([
                    'sales_orders.st_plan' => 0,
                ]);

        } catch (Exception $e) {
            DB::rollback();
            DB::connection('ympimis_2')->rollback();

            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        $materials = db::table('materials')
            ->whereIn('category', ['KD', 'FG'])
            ->whereIn('hpl', $request->get('hpl'))
            ->get();

        $destinations = db::table('destinations')
            ->whereNotNull('priority')
            ->get();

        $material_numbers = [];
        for ($i = 0; $i < count($materials); $i++) {
            array_push($material_numbers, $materials[$i]->material_number);
        }

        $sales_order = db::connection('ympimis_2')
            ->table('sales_orders')
            ->where('sales_orders.sales_month', 'LIKE', '%' . $month . '%')
            ->whereIn('sales_orders.material_number', $material_numbers)
            ->orderBy('sales_orders.material_number', 'ASC')
            ->get();

        $new_sales_order = [];
        for ($i = 0; $i < count($sales_order); $i++) {
            $hpl = '';
            $destination_shortname = '';
            $priority = 100;
            for ($j = 0; $j < count($destinations); $j++) {
                if ($sales_order[$i]->destination_code == $destinations[$j]->destination_code) {
                    $destination_shortname = $destinations[$j]->destination_shortname;
                    $priority = $destinations[$j]->priority;
                    break;
                }
            }

            for ($j = 0; $j < count($materials); $j++) {
                if ($sales_order[$i]->material_number == $materials[$j]->material_number) {
                    $hpl = $materials[$j]->hpl;
                    break;
                }
            }

            $row = array();
            $row['sales_month'] = $sales_order[$i]->sales_month;
            $row['sales_order'] = $sales_order[$i]->sales_order;
            $row['material_number'] = $sales_order[$i]->material_number;
            $row['destination_code'] = $sales_order[$i]->destination_code;
            $row['destination_shortname'] = $destination_shortname;
            $row['hpl'] = $hpl;
            $row['priority'] = $priority;
            $row['concat'] = sprintf("%'.0" . 3 . "d", $priority) . '_' . $sales_order[$i]->material_number . '_' . $sales_order[$i]->sales_order;
            $row['quantity'] = $sales_order[$i]->quantity;
            $row['st_plan'] = $sales_order[$i]->st_plan;

            $new_sales_order[] = (object) $row;

        }

        usort($new_sales_order, function ($a, $b) {
            return strcmp($a->concat, $b->concat);
        });

        $end_st = PsiCalendar::where('stuffing_period', 'like', '%' . $month . '%')
            ->orderBy('week_date', 'DESC')
            ->first();

        $psi_calendar = PsiCalendar::where('stuffing_period', 'LIKE', '%' . $month . '%')
            ->orderBy('week_date', 'ASC')
            ->get();

        try {
            for ($i = 0; $i < count($new_sales_order); $i++) {

                $st_plan = $new_sales_order[$i]->quantity;

                $productions = DB::select("
                SELECT 'stock' AS type, stock_date AS due_date, material_number, (quantity - st_plan) AS quantity FROM first_inventories
                    WHERE material_number = '" . $new_sales_order[$i]->material_number . "'
                        AND stock_date LIKE '%" . $month . "%'
                    HAVING quantity > 0
                UNION ALL
                SELECT 'plan' AS type, due_date, material_number, (quantity - st_plan) AS quantity FROM production_schedules_two_steps
                    WHERE material_number = '" . $new_sales_order[$i]->material_number . "'
                        AND due_date LIKE '%" . $month . "%'
                    HAVING quantity > 0
                ORDER BY due_date ASC");

                for ($j = 0; $j < count($productions); $j++) {
                    if (!isset($this->logic[$new_sales_order[$i]->destination_shortname][date('l', strtotime($productions[$j]->due_date))])) {
                        DB::rollback();
                        DB::connection('ympimis_2')->rollback();

                        $response = array(
                            'status' => false,
                            'message' => 'Destination unregistered',
                        );
                        return Response::json($response);
                    }

                    $koef = $this->logic[$new_sales_order[$i]->destination_shortname][date('l', strtotime($productions[$j]->due_date))];
                    $st_day = $this->day[$new_sales_order[$i]->destination_shortname];

                    $st_date = date('Y-m-d', strtotime('+' . $koef . ' day', strtotime($productions[$j]->due_date)));

                    $is_found = false;
                    for ($x = 0; $x < count($psi_calendar); $x++) {
                        if ($psi_calendar[$x]->remark != 'H') {
                            if ($psi_calendar[$x]->week_date == $st_date) {
                                $is_found = true;
                                break;
                            } elseif ($psi_calendar[$x]->week_date > $st_date) {
                                $day = intval(date('N', strtotime($psi_calendar[$x]->week_date)));
                                if (in_array($day, $st_day)) {
                                    $st_date = $psi_calendar[$x]->week_date;
                                    $is_found = true;
                                    break;
                                }
                            }
                        }
                    }

                    if (!$is_found) {
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
                        ->where('sales_order', $new_sales_order[$i]->sales_order)
                        ->where('destination_code', $new_sales_order[$i]->destination_code)
                        ->where('material_number', $productions[$j]->material_number)
                        ->where('hpl', $new_sales_order[$i]->hpl)
                        ->where('st_date', $st_date)
                        ->first();

                    if ($shipment_schedule) {
                        $shipment_schedule->quantity = $shipment_schedule->quantity + $quantity;
                        $shipment_schedule->save();
                    } else {
                        $insert = new ProductionSchedulesThreeStep([
                            'st_month' => $month . '-01',
                            'sales_order' => $new_sales_order[$i]->sales_order,
                            'shipment_condition_code' => 'C1',
                            'destination_code' => $new_sales_order[$i]->destination_code,
                            'material_number' => $productions[$j]->material_number,
                            'hpl' => $new_sales_order[$i]->hpl,
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

                    // $update_sales_order = db::connection('ympimis_2')
                    //     ->table('sales_orders')
                    //     ->where('sales_month', 'LIKE', '%' . $month . '%')
                    //     ->where('sales_order', $new_sales_order[$j]->sales_order)
                    //     ->where('material_number', $productions[$j]->material_number)
                    //     ->where('destination_code', $new_sales_order[$i]->destination_code)
                    //     ->first();

                    // $execute_sales_order = db::connection('ympimis_2')
                    //     ->table('sales_orders')
                    //     ->increment('st_plan', $quantity);

                    $st_plan = $st_plan - $quantity;
                    if ($st_plan == 0) {
                        break;
                    }
                }

                // CEK KEKURANGAN AKHIR DAN RATAKAN
                if ($st_plan > 0) {
                    $last_st = $psi_calendar[(count($psi_calendar) - 1)]->week_date;
                    $bl_date = date('Y-m-d', strtotime('+3 day', strtotime($last_st)));

                    $modulo = db::select("
                    SELECT 'plan' AS type, due_date, material_number, (quantity - st_plan) AS quantity FROM production_schedules_two_steps
                        WHERE material_number = '" . $new_sales_order[$i]->material_number . "'
                            AND due_date LIKE '%" . $month . "%'
                            AND due_date <= '" . $last_st . "'
                        HAVING quantity > 0");

                    for ($x = 0; $x < count($modulo); $x++) {

                        if ($st_plan > 0) {
                            if ($st_plan >= $modulo[$x]->quantity) {
                                $quantity = $modulo[$x]->quantity;
                            } else {
                                $quantity = $st_plan;
                            }

                            $shipment_schedule = ProductionSchedulesThreeStep::where('st_month', $month . '-01')
                                ->where('shipment_condition_code', 'C1')
                                ->where('sales_order', $new_sales_order[$i]->sales_order)
                                ->where('destination_code', $new_sales_order[$i]->destination_code)
                                ->where('material_number', $new_sales_order[$i]->material_number)
                                ->where('hpl', $new_sales_order[$i]->hpl)
                                ->where('st_date', $last_st)
                                ->first();

                            if ($shipment_schedule) {
                                $shipment_schedule->quantity = $shipment_schedule->quantity + $quantity;
                                $shipment_schedule->save();
                            } else {
                                $insert = new ProductionSchedulesThreeStep([
                                    'st_month' => $month . '-01',
                                    'sales_order' => $new_sales_order[$i]->sales_order,
                                    'shipment_condition_code' => 'C1',
                                    'destination_code' => $new_sales_order[$i]->destination_code,
                                    'material_number' => $new_sales_order[$i]->material_number,
                                    'hpl' => $new_sales_order[$i]->hpl,
                                    'st_date' => $last_st,
                                    'bl_date' => $bl_date,
                                    'quantity' => $quantity,
                                    'created_by' => Auth::id(),
                                ]);
                                $insert->save();
                            }

                            $update_production = ProductionSchedulesTwoStep::where('due_date', $modulo[$x]->due_date)
                                ->where('material_number', $modulo[$x]->material_number)
                                ->first();

                            $update_production->st_plan = $update_production->st_plan + $quantity;
                            $update_production->save();

                            $st_plan = $st_plan - $quantity;

                        } else {
                            break;
                        }

                    }

                }

            }

        } catch (Exception $e) {
            DB::rollback();
            DB::connection('ympimis_2')->rollback();

            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);

        }

        DB::commit();
        DB::connection('ympimis_2')->commit();
        $response = array(
            'status' => true,
        );
        return Response::json($response);
    }

    public function generateShipmentCubication(Request $request)
    {

        $month = '';
        if (strlen($request->get('month')) > 0) {
            $month = $request->get('month');
        } else {
            $month = date('Y-m');
        }

        $materials = db::table('materials')
            ->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'materials.material_number')
            ->whereIn('materials.category', ['KD', 'FG'])
            ->select(
                'materials.material_number',
                'materials.material_description',
                'materials.category',
                'materials.hpl',
                'material_volumes.lot_pallet',
                'material_volumes.length_pallet',
                'material_volumes.width_pallet',
                'material_volumes.height_pallet',
                'material_volumes.cubic_meter_pallet',
                'material_volumes.lot_carton',
                'material_volumes.length',
                'material_volumes.width',
                'material_volumes.height',
                'material_volumes.cubic_meter'
            )
            ->get();

        $formatted_materials = [];
        for ($i = 0; $i < count($materials); $i++) {
            $formatted_materials[$materials[$i]->material_number] = $materials[$i];
        }

        $destinations = db::table('destinations')
            ->whereNotNull('priority')
            ->orderBy('priority')
            ->get();

        $remarks = [];
        for ($i = 0; $i < count($destinations); $i++) {
            if (!in_array($destinations[$i]->remark, $remarks)) {
                array_push($remarks, $destinations[$i]->remark);
            }
        }

        $shipment = db::table('production_schedules_three_steps')
            ->where('st_month', 'LIKE', '%' . $month . '%')
            ->orderBy('st_date', 'ASC')
            ->get();

        // START GENERATE LOTTING CARTON
        $lotting_box = [
            'CLFG',
            'FLFG',
            'PN',
            'RC',
            'VENOVA',
            'PN-PART',
        ];

        $temp = [];
        for ($i = 0; $i < count($shipment); $i++) {
            $lot_carton = 0;
            if (isset($formatted_materials[$shipment[$i]->material_number])) {
                $lot_carton = $formatted_materials[$shipment[$i]->material_number]->lot_carton;
            }

            $key = '';
            $key .= ($shipment[$i]->destination_code . '#');
            $key .= ($shipment[$i]->material_number);

            if (in_array($shipment[$i]->hpl, $lotting_box)) {
                $mod = $shipment[$i]->quantity % $lot_carton;
                if ($mod > 0) {
                    $shipment[$i]->quantity = $shipment[$i]->quantity - $mod;

                    if (!array_key_exists($key, $temp)) {
                        $row = array();
                        $row['destination_code'] = $shipment[$i]->destination_code;
                        $row['material_number'] = $shipment[$i]->material_number;
                        $row['date'] = $shipment[$i]->st_date;
                        $row['quantity'] = $mod;
                        $temp[$key] = (object) $row;
                    } else {
                        $temp[$key]->quantity = $temp[$key]->quantity + $mod;
                    }

                }
            }

            if (array_key_exists($key, $temp)) {
                $temp[$key]->date = $shipment[$i]->st_date;
            }

        }

        foreach ($temp as $key) {
            for ($i = 0; $i < count($shipment); $i++) {
                if ($shipment[$i]->destination_code == $key->destination_code && $shipment[$i]->material_number == $key->material_number && $shipment[$i]->st_date == $key->date) {
                    $shipment[$i]->quantity = $shipment[$i]->quantity + $key->quantity;
                    break;
                }
            }
        }

        // START GENERATE SHIPMENT CUBIC
        $new_shipment = [];
        for ($i = 0; $i < count($shipment); $i++) {
            $destination_shortname = '';
            $remark = '';
            for ($j = 0; $j < count($destinations); $j++) {
                if ($shipment[$i]->destination_code == $destinations[$j]->destination_code) {
                    $destination_shortname = $destinations[$j]->destination_shortname;
                    $remark = $destinations[$j]->remark;
                    break;
                }
            }

            $material_description = '';
            $lot_pallet = 0;
            $volume_pallet = 0;
            $lot_carton = 0;
            $volume_carton = 0;

            if (isset($formatted_materials[$shipment[$i]->material_number])) {
                $material_description = $formatted_materials[$shipment[$i]->material_number]->material_description;
                $hpl = $formatted_materials[$shipment[$i]->material_number]->hpl;
                $lot_pallet = $formatted_materials[$shipment[$i]->material_number]->lot_pallet;
                $volume_pallet = $formatted_materials[$shipment[$i]->material_number]->cubic_meter_pallet;
                $lot_carton = $formatted_materials[$shipment[$i]->material_number]->lot_carton;
                $volume_carton = $formatted_materials[$shipment[$i]->material_number]->cubic_meter;

                if ($formatted_materials[$shipment[$i]->material_number]->lot_pallet > 0 && $formatted_materials[$shipment[$i]->material_number]->cubic_meter_pallet > 0) {
                    $volume = $shipment[$i]->quantity / $formatted_materials[$shipment[$i]->material_number]->lot_pallet * $formatted_materials[$shipment[$i]->material_number]->cubic_meter_pallet;
                }

            }

            if ($lot_pallet == 0 || $lot_carton == 0) {
                $response = array(
                    'status' => false,
                    'message' => 'Data volume material ' . $shipment[$i]->material_number . ' - ' . $material_description . ' tidak lengkap',
                );
                return Response::json($response);
            }

            $fixed_cubication = [
                'CLFG',
                'ASFG',
                'TSFG',
                'FLFG',
                'PN',
                'RC',
                'VENOVA',
                'CASE',
            ];

            $ratio_pallet = $shipment[$i]->quantity / $lot_pallet;
            $qty_schedule = $shipment[$i]->quantity;
            $mod = $shipment[$i]->quantity % $lot_pallet;
            $check_requir_pallet = $mod / $lot_pallet;

            $cek = [];

            if (in_array($hpl, $fixed_cubication)) {

                // KUBIKASI PALLET
                if ($check_requir_pallet > 0.7) {
                    $qty_pallet = ceil($shipment[$i]->quantity / $lot_pallet);
                    for ($x = 0; $x < $qty_pallet; $x++) {
                        $qty_plan = $lot_pallet;
                        if ($qty_schedule < $qty_plan) {
                            $qty_plan = $qty_schedule;
                        }

                        $row = array();
                        $row['st_month'] = $shipment[$i]->st_month;
                        $row['sales_order'] = $shipment[$i]->sales_order;
                        $row['destination_code'] = $shipment[$i]->destination_code;
                        $row['destination_shortname'] = $destination_shortname;
                        $row['remark'] = $remark;
                        $row['shipment_condition_code'] = $shipment[$i]->shipment_condition_code;
                        $row['material_number'] = $shipment[$i]->material_number;
                        $row['material_description'] = $material_description;
                        $row['hpl'] = $shipment[$i]->hpl;
                        $row['st_date'] = $shipment[$i]->st_date;
                        $row['bl_date'] = $shipment[$i]->bl_date;
                        $row['lot_pallet'] = $lot_pallet;
                        $row['lot_carton'] = $lot_carton;
                        $row['packing_type'] = 'PALLET';
                        $row['quantity'] = $qty_plan;
                        $row['volume'] = $volume_pallet;
                        $row['concat'] = $remark . '_' . $shipment[$i]->st_date;
                        $new_shipment[] = (object) $row;

                        $qty_schedule -= $qty_plan;

                    }

                } else {
                    $qty_pallet = floor($shipment[$i]->quantity / $lot_pallet);
                    for ($x = 0; $x < $qty_pallet; $x++) {
                        $row = array();
                        $row['st_month'] = $shipment[$i]->st_month;
                        $row['sales_order'] = $shipment[$i]->sales_order;
                        $row['destination_code'] = $shipment[$i]->destination_code;
                        $row['destination_shortname'] = $destination_shortname;
                        $row['remark'] = $remark;
                        $row['shipment_condition_code'] = $shipment[$i]->shipment_condition_code;
                        $row['material_number'] = $shipment[$i]->material_number;
                        $row['material_description'] = $material_description;
                        $row['hpl'] = $shipment[$i]->hpl;
                        $row['st_date'] = $shipment[$i]->st_date;
                        $row['bl_date'] = $shipment[$i]->bl_date;
                        $row['lot_pallet'] = $lot_pallet;
                        $row['lot_carton'] = $lot_carton;
                        $row['packing_type'] = 'PALLET';
                        $row['quantity'] = $lot_pallet;
                        $row['volume'] = $volume_pallet;
                        $row['concat'] = $remark . '_' . $shipment[$i]->st_date;
                        $new_shipment[] = (object) $row;

                        $qty_schedule -= $lot_pallet;

                    }

                }

                // KUBIKASI CARTON
                if ($qty_schedule > 0) {
                    $qty_carton = ceil($qty_schedule / $lot_carton);

                    for ($x = 0; $x < $qty_carton; $x++) {
                        $qty_plan = $lot_carton;
                        if ($qty_schedule < $qty_plan) {
                            $qty_plan = $qty_schedule;
                        }

                        $row = array();
                        $row['st_month'] = $shipment[$i]->st_month;
                        $row['sales_order'] = $shipment[$i]->sales_order;
                        $row['destination_code'] = $shipment[$i]->destination_code;
                        $row['destination_shortname'] = $destination_shortname;
                        $row['remark'] = $remark;
                        $row['shipment_condition_code'] = $shipment[$i]->shipment_condition_code;
                        $row['material_number'] = $shipment[$i]->material_number;
                        $row['material_description'] = $material_description;
                        $row['hpl'] = $shipment[$i]->hpl;
                        $row['st_date'] = $shipment[$i]->st_date;
                        $row['bl_date'] = $shipment[$i]->bl_date;
                        $row['lot_pallet'] = $lot_pallet;
                        $row['lot_carton'] = $lot_carton;
                        $row['packing_type'] = 'CARTON';
                        $row['quantity'] = $qty_plan;
                        $row['volume'] = $volume_carton;
                        $row['concat'] = $remark . '_' . $shipment[$i]->st_date;
                        $new_shipment[] = (object) $row;

                        $qty_schedule -= $qty_plan;
                    }
                }

            } else {

                $qty_carton = ceil($qty_schedule / $lot_carton);

                $row = array();
                $row['st_month'] = $shipment[$i]->st_month;
                $row['sales_order'] = $shipment[$i]->sales_order;
                $row['destination_code'] = $shipment[$i]->destination_code;
                $row['destination_shortname'] = $destination_shortname;
                $row['remark'] = $remark;
                $row['shipment_condition_code'] = $shipment[$i]->shipment_condition_code;
                $row['material_number'] = $shipment[$i]->material_number;
                $row['material_description'] = $material_description;
                $row['hpl'] = $shipment[$i]->hpl;
                $row['st_date'] = $shipment[$i]->st_date;
                $row['bl_date'] = $shipment[$i]->bl_date;
                $row['lot_pallet'] = $lot_pallet;
                $row['lot_carton'] = $lot_carton;
                $row['packing_type'] = 'CARTON';
                $row['quantity'] = $qty_schedule;
                $row['volume'] = $volume_carton * $qty_carton;
                $row['concat'] = $remark . '_' . $shipment[$i]->st_date;
                $new_shipment[] = (object) $row;

            }

        }

        DB::beginTransaction();
        try {
            $delete = db::table('production_schedules_four_steps')
                ->where('st_month', 'LIKE', '%' . $month . '%')
                ->delete();

            $cek = [];

            $temp = [];
            for ($i = 0; $i < count($remarks); $i++) {
                $volume = 0;
                $st_date = '';

                for ($j = 0; $j < count($new_shipment); $j++) {
                    if ($remarks[$i] == $new_shipment[$j]->remark) {
                        if ($volume <= 55) {
                            $volume += $new_shipment[$j]->volume;
                            $st_date = $new_shipment[$j]->st_date;

                            $row = array();
                            $row['st_month'] = $new_shipment[$j]->st_month;
                            $row['sales_order'] = $new_shipment[$j]->sales_order;
                            $row['destination_code'] = $new_shipment[$j]->destination_code;
                            $row['destination_shortname'] = $new_shipment[$j]->destination_shortname;
                            $row['remark'] = $new_shipment[$j]->remark;
                            $row['shipment_condition_code'] = $new_shipment[$j]->shipment_condition_code;
                            $row['material_number'] = $new_shipment[$j]->material_number;
                            $row['material_description'] = $new_shipment[$j]->material_description;
                            $row['hpl'] = $new_shipment[$j]->hpl;
                            $row['quantity'] = $new_shipment[$j]->quantity;
                            $row['volume'] = $new_shipment[$j]->volume;

                            $temp[] = (object) $row;

                        } else {

                            $bl_date = date('Y-m-d', strtotime('+3 day', strtotime($st_date)));
                            $insert_temp = $this->sumGenerateShipment($temp);
                            foreach ($insert_temp as $row) {
                                $insert = db::table('production_schedules_four_steps')
                                    ->insert([
                                        'st_month' => $row->st_month,
                                        'sales_order' => $row->sales_order,
                                        'shipment_condition_code' => $row->shipment_condition_code,
                                        'destination_code' => $row->destination_code,
                                        'material_number' => $row->material_number,
                                        'hpl' => $row->hpl,
                                        'st_date' => $st_date,
                                        'bl_date' => $bl_date,
                                        'quantity' => $row->quantity,
                                        'volume' => $row->volume,
                                        'created_by' => Auth::id(),
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                            }

                            $volume = 0;
                            $st_date = $new_shipment[$j]->st_date;
                            $temp = [];

                            $volume += $new_shipment[$j]->volume;
                            $row = array();
                            $row['st_month'] = $new_shipment[$j]->st_month;
                            $row['sales_order'] = $new_shipment[$j]->sales_order;
                            $row['destination_code'] = $new_shipment[$j]->destination_code;
                            $row['destination_shortname'] = $new_shipment[$j]->destination_shortname;
                            $row['remark'] = $new_shipment[$j]->remark;
                            $row['shipment_condition_code'] = $new_shipment[$j]->shipment_condition_code;
                            $row['material_number'] = $new_shipment[$j]->material_number;
                            $row['material_description'] = $new_shipment[$j]->material_description;
                            $row['hpl'] = $new_shipment[$j]->hpl;
                            $row['quantity'] = $new_shipment[$j]->quantity;
                            $row['volume'] = $new_shipment[$j]->volume;

                            $temp[] = (object) $row;

                        }
                    }
                }

                $bl_date = date('Y-m-d', strtotime('+3 day', strtotime($st_date)));
                $insert_temp = $this->sumGenerateShipment($temp);
                foreach ($insert_temp as $row) {
                    $insert = db::table('production_schedules_four_steps')
                        ->insert([
                            'st_month' => $row->st_month,
                            'sales_order' => $row->sales_order,
                            'shipment_condition_code' => $row->shipment_condition_code,
                            'destination_code' => $row->destination_code,
                            'material_number' => $row->material_number,
                            'hpl' => $row->hpl,
                            'st_date' => $st_date,
                            'bl_date' => $bl_date,
                            'quantity' => $row->quantity,
                            'volume' => $row->volume,
                            'created_by' => Auth::id(),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }
                $temp = [];

            }

        } catch (Exception $e) {
            DB::rollback();

            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        DB::commit();
        $response = array(
            'status' => true,
        );
        return Response::json($response);

    }

    public function sumGenerateShipment($param)
    {
        $sum = [];
        for ($i = 0; $i < count($param); $i++) {
            $key = '';
            $key .= ($param[$i]->st_month . '#');
            $key .= ($param[$i]->sales_order . '#');
            $key .= ($param[$i]->destination_code . '#');
            $key .= ($param[$i]->destination_shortname . '#');
            $key .= ($param[$i]->remark . '#');
            $key .= ($param[$i]->shipment_condition_code . '#');
            $key .= ($param[$i]->material_number . '#');
            $key .= ($param[$i]->material_description . '#');
            $key .= ($param[$i]->hpl . '#');

            if (!array_key_exists($key, $sum)) {
                $row = array();
                $row['st_month'] = $param[$i]->st_month;
                $row['sales_order'] = $param[$i]->sales_order;
                $row['destination_code'] = $param[$i]->destination_code;
                $row['destination_shortname'] = $param[$i]->destination_shortname;
                $row['remark'] = $param[$i]->remark;
                $row['shipment_condition_code'] = $param[$i]->shipment_condition_code;
                $row['material_number'] = $param[$i]->material_number;
                $row['material_description'] = $param[$i]->material_description;
                $row['hpl'] = $param[$i]->hpl;
                $row['quantity'] = $param[$i]->quantity;
                $row['volume'] = $param[$i]->volume;

                $sum[$key] = (object) $row;

            } else {
                $sum[$key]->quantity = $sum[$key]->quantity + $param[$i]->quantity;
                $sum[$key]->volume = $sum[$key]->volume + $param[$i]->volume;

            }
        }

        return $sum;

    }

    public function exportShipmentCubication(Request $request)
    {

        $destination_code = $request->get('destination_code');
        $month = $request->get('month');

        $current_shipment = ShipmentSchedule::where('st_month', 'LIKE', '%' . $month . '%')
            ->whereIn('destination_code', $destination_code)
            ->get();

        if (count($current_shipment) > 0) {
            $response = array(
                'status' => false,
                'message' => 'Draft shipment from the selected destination has been exported',
            );
            return Response::json($response);
        }

        try {

            $draft = DB::table('production_schedules_four_steps')
                ->select(
                    'st_month',
                    'sales_order',
                    'shipment_condition_code',
                    'destination_code',
                    'material_number',
                    'hpl',
                    'st_date',
                    'bl_date',
                    'quantity',
                    'actual_quantity'
                )
                ->where('st_month', 'LIKE', '%' . $month . '%')
                ->whereIn('destination_code', $destination_code)
                ->get();

            for ($i = 0; $i < count($draft); $i++) {
                $insert = new ShipmentSchedule([
                    'st_month' => $draft[$i]->st_month,
                    'sales_order' => $draft[$i]->sales_order,
                    'shipment_condition_code' => $draft[$i]->shipment_condition_code,
                    'destination_code' => $draft[$i]->destination_code,
                    'material_number' => $draft[$i]->material_number,
                    'hpl' => $draft[$i]->hpl,
                    'st_date' => $draft[$i]->st_date,
                    'bl_date' => $draft[$i]->bl_date,
                    'quantity' => $draft[$i]->quantity,
                    'actual_quantity' => 0,
                    'created_by' => Auth::id(),
                ]);
                $insert->save();
            }

            DB::commit();
            $response = array(
                'status' => true,
                'message' => 'Export draft shipment success',
            );
            return Response::json($response);

        } catch (\Exception $e) {
            DB::rollback();
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);

        }
    }

    public function inputBackOrder(Request $request)
    {


        $month = date('Y-m', strtotime('+1 months', strtotime($request->get('month') . '-01')));
        $upload = $request->get('upload');
        $uploadRows = preg_split("/\r?\n/", $upload);

        $materials = db::table('materials')
            ->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'materials.material_number')
            ->whereIn('materials.category', ['KD', 'FG'])
            ->select(
                'materials.material_number',
                'materials.material_description',
                'materials.category',
                'materials.hpl',
                'material_volumes.lot_completion'
            )
            ->get();
        $materials = $materials->keyBy('material_number');

        $destinations = db::table('destinations')
            ->whereNotNull('priority')
            ->get();
        $destinations = $destinations->keyBy('destination_code');

        $sales_prices = db::table('sales_prices_bk')
            ->where('sales_category', 'ALL')
            ->get();
        $sales_prices = $sales_prices->keyBy('material_number');


        DB::beginTransaction();
        $delete = db::table('sales_backorders')
            ->where('sales_month', 'LIKE', '%' . $month . '%')
            ->delete();

        foreach ($uploadRows as $uploadRow) {
            $uploadColumn = preg_split("/\t/", $uploadRow);
            if (count($uploadColumn) == 3) {

                $material_number = $uploadColumn[0];
                $destination_code = $uploadColumn[1];
                $quantity = $uploadColumn[2];

                if (isset($materials[$material_number]) && isset($destinations[$destination_code]) && isset($sales_prices[$material_number])) {
                    $category = $materials[$material_number]->category;
                    if ($category != 'KD') {
                        $category = $materials[$material_number]->hpl;
                    }

                    try {
                        $insert = db::table('sales_backorders')
                            ->insert([
                                'sales_month' => $month . '-01',
                                'material_number' => $material_number,
                                'material_description' => $materials[$material_number]->material_description,
                                'category' => $category,
                                'destination_shortname' => $destinations[$destination_code]->destination_shortname,
                                'quantity' => $quantity,
                                'price' => $sales_prices[$material_number]->price,
                                'created_by' => Auth::id(),
                            ]);

                    } catch (Exception $e) {
                        DB::rollback();
                        $response = array(
                            'status' => false,
                            'message' => $e->getMessage(),
                        );
                        return Response::json($response);
                    }
                } else {
                    DB::rollback();
                    $response = array(
                        'status' => false,
                        'message' => 'Material number or sales to party not found',
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

}
