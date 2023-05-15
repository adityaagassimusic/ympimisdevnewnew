<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DataTables;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;
use ZipArchive;

class FinishedGoodsController extends Controller
{

    public function __construct()
    {
        $this->category = [
            'FG',
            'KD',
        ];

        $this->middleware('auth');
    }

    public function index_fg_production()
    {
        return view('finished_goods.production')->with('page', 'FG Production')->with('head', 'Finished Goods');
    }

    public function index_fg_stock()
    {
        return view('finished_goods.stock')->with('page', 'FG Stock')->with('head', 'Finished Goods');
    }

    public function index_fg_container_departure()
    {
        return view('finished_goods.container_departure')->with('page', 'FG Container Departure')->with('head', 'Finished Goods');
    }

    public function index_fg_weekly_summary()
    {

        return view('finished_goods.weekly_summary')->with('page', 'FG Weekly Summary')->with('head', 'Finished Goods');
    }

    public function index_fg_monthly_summary()
    {
        $periods = DB::table('shipment_schedules')->select('st_month')->distinct()->get();

        return view(
            'finished_goods.monthly_summary',
            array(
                'periods' => $periods,
            )
        )->with('page', 'FG Monthly Summary')->with('head', 'Finished Goods');
    }

    public function index_fg_traceability()
    {
        $origin_groups = DB::table('origin_groups')->orderBy('origin_group_code', 'asc')->get();
        $materials = DB::table('materials')->where('category', '=', 'FG')->orderBy('material_number', 'asc')->get();
        $destinations = DB::table('destinations')->orderBy('destination_code', 'asc')->get();
        $flo_details = [];
        $packing = [];

        return view(
            'finished_goods.traceability',
            array(
                'origin_groups' => $origin_groups,
                'materials' => $materials,
                'destinations' => $destinations,
                'flo_details' => $flo_details,
                'packing' => $packing,
            )
        )->with('page', 'FG Traceability')->with('head', 'Finished Goods');
    }

    public function index_fg_shipment_schedule()
    {
        $periods = DB::table('shipment_schedules')->select('st_month')->orderBy('st_month', 'desc')->distinct()->get();
        $origin_groups = DB::table('origin_groups')->get();
        $categories = $this->category;
        $hpls = DB::table('materials')->whereIn('materials.category', ['KD', 'FG'])
            ->select('category', 'hpl')
            ->distinct()
            ->get();

        return view(
            'finished_goods.shipment_schedule',
            array(
                'periods' => $periods,
                'origin_groups' => $origin_groups,
                'categories' => $categories,
                'hpls' => $hpls,
            )
        )->with('page', 'FG Shipment Schedule')->with('head', 'Finished Goods');
    }

    public function index_fg_shipment_result()
    {
        return view('finished_goods.shipment_result')->with('page', 'FG Shipment Result')->with('head', 'Finished Goods');
    }

    public function fetch_fg_shipment_result(Request $request)
    {
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);

        if ($request->get('datefrom') != "") {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
        } else {
            $datefrom = date('Y-m-d', strtotime(Carbon::now()->subDays(1)));
        }

        if ($request->get('dateto') != "") {
            $dateto = date('Y-m-d', strtotime($request->get('dateto')));
        } else {
            $dateto = date('Y-m-d', strtotime(Carbon::now()->addDays(14)));
        }

        $query = "SELECT
        A.hpl,
        A.st_date,
        B.act AS act,
        B.plan AS plan,
        B.actual AS actual
        FROM
        (
         SELECT DISTINCT
         materials.hpl,
         shipment_schedules.st_date
         FROM
         materials
         CROSS JOIN shipment_schedules
         WHERE
         shipment_schedules.st_date >= '" . $datefrom . "'
         AND shipment_schedules.st_date <= '" . $dateto . "'
         AND materials.category = 'FG'
         ) AS A
         LEFT JOIN (
         SELECT
         b.st_date,
         b.hpl,
         a.actual AS act,
         b.plan AS plan,
         round(( COALESCE ( a.actual, 0 )/ b.plan )* 100, 1 ) AS actual
         FROM
         (
         SELECT
         shipment_schedules.st_date,
         materials.hpl,
         sum( flos.actual ) AS actual
         FROM
         flos
         LEFT JOIN shipment_schedules ON flos.shipment_schedule_id = shipment_schedules.id
         LEFT JOIN materials ON materials.material_number = shipment_schedules.material_number
         WHERE
         materials.category = 'FG'
         GROUP BY
         shipment_schedules.st_date,
         materials.hpl
         ) AS a
         RIGHT JOIN (
         SELECT
         shipment_schedules.st_date,
         materials.hpl,
         sum( shipment_schedules.quantity ) AS plan
         FROM
         shipment_schedules
         LEFT JOIN materials ON materials.material_number = shipment_schedules.material_number
         WHERE
         materials.category = 'FG'
         GROUP BY
         shipment_schedules.st_date,
         materials.hpl
         ) AS b ON b.st_date = a.st_date
         AND a.hpl = b.hpl
         WHERE
         b.st_date >= '" . $datefrom . "'
         AND b.st_date <= '" . $dateto . "'
         ) AS B ON A.st_date = B.st_date
         AND A.hpl = B.hpl
         ORDER BY
         A.st_date ASC,
         B.hpl ASC";

        $shipment_results = db::select($query);

        $response = array(
            'status' => true,
            'shipment_results' => $shipment_results,
        );
        return Response::json($response);
    }

    public function fetch_tb_shipment_result(Request $request)
    {
        $st_date = date('Y-m-d', strtotime($request->get('date')));

        $query = "
        select a.material_number, a.material_description, a.destination_shortname, a.plan, coalesce(b.actual,0) as actual, coalesce(b.actual,0)-a.plan as diff from
        (
        select shipment_schedules.st_date, shipment_schedules.material_number, materials.material_description, shipment_schedules.destination_code, destinations.destination_shortname, sum(shipment_schedules.quantity) as plan from shipment_schedules
        left join materials on materials.material_number = shipment_schedules.material_number
        left join destinations on destinations.destination_code = shipment_schedules.destination_code
        where materials.category = 'FG' and shipment_schedules.st_date = '" . $st_date . "' and materials.hpl = '" . $request->get('hpl') . "'
        group by shipment_schedules.st_date, shipment_schedules.material_number, materials.material_description, shipment_schedules.destination_code, destinations.destination_shortname
        ) as a
        left join
        (
        select shipment_schedules.st_date, shipment_schedules.material_number, shipment_schedules.destination_code, sum(flos.actual) as actual from flos
        left join shipment_schedules on shipment_schedules.id = flos.shipment_schedule_id
        group by shipment_schedules.st_date, shipment_schedules.material_number, shipment_schedules.destination_code
        ) as b
        on a.st_date = b.st_date and a.material_number = b.material_number and a.destination_code = b.destination_code
        order by diff asc";

        $shipment_results = DB::select($query);

        $response = array(
            'status' => true,
            'shipment_results' => $shipment_results,
        );
        return Response::json($response);
    }

    public function fetch_fg_shipment_schedule(Request $request)
    {
        $periodFrom = $request->get('periodFrom');
        $periodTo = $request->get('periodTo');
        $st_month = date('Y-m-01');
        if (strlen($request->get('periodFrom')) > 0 && strlen($request->get('periodTo')) == 0) {
            $where1 = " where shipment_schedules.st_month >= '" . $periodFrom . "'";
        } else if (strlen($request->get('periodFrom')) > 0 && strlen($request->get('periodTo')) > 0) {
            $where1 = " where shipment_schedules.st_month >= '" . $periodFrom . "' and shipment_schedules.st_month <= '" . $periodTo . "'";
        } else {
            $where1 = " where shipment_schedules.st_month = '" . $st_month . "'";
        }

        if (strlen($request->get('originGroupCode')) > 0) {
            $where2 = " and materials.origin_group_code = '" . $request->get('originGroupCode') . "'";
        } else {
            $where2 = "";
        }

        if (strlen($request->get('hpl')) > 0) {
            $where3 = " and materials.hpl = '" . $request->get('hpl') . "'";
        } else {
            $where3 = "";
        }

        if (strlen($request->get('category')) > 0) {
            $where4 = " and materials.category = '" . $request->get('category') . "'";
        } else {
            $where4 = "";
        }

        $query = "select materials.category, shipment_schedules.id, date_format(shipment_schedules.st_month, '%b-%Y') as st_month, shipment_schedules.sales_order, destinations.destination_shortname, shipment_schedules.hpl, shipment_conditions.shipment_condition_name, shipment_schedules.material_number, materials.material_description, shipment_schedules.quantity, date_format(shipment_schedules.st_date, '%d-%b-%Y') as st_date, date_format(shipment_schedules.bl_date, '%d-%b-%Y') as bl_date_plan, sum(coalesce(stock.quantity, 0)) as quantity_production, sum(if(stock.status > 1, stock.quantity, 0)) as quantity_delivery FROM `shipment_schedules` left join
        (select shipment_schedule_id, sum(actual) as quantity, status from flos group by shipment_schedule_id, status
        union all
        select knock_down_details.shipment_schedule_id, sum(knock_down_details.quantity) as quantity, knock_downs.`status` from knock_down_details
        left join knock_downs on knock_downs.kd_number = knock_down_details.kd_number
        group by shipment_schedule_id, knock_downs.status) as stock on shipment_schedules.id = stock.shipment_schedule_id
        left join destinations on destinations.destination_code = shipment_schedules.destination_code
        left join shipment_conditions on shipment_conditions.shipment_condition_code = shipment_schedules.shipment_condition_code
        left join materials on materials.material_number = shipment_schedules.material_number
        " . $where1 . "
        " . $where2 . "
        " . $where3 . "
        " . $where4 . "
        group by materials.category, shipment_schedules.id, date_format(shipment_schedules.st_month, '%b-%Y'), shipment_schedules.sales_order, destinations.destination_shortname, shipment_conditions.shipment_condition_name, shipment_schedules.material_number, materials.material_description, shipment_schedules.quantity, date_format(shipment_schedules.st_date, '%d-%b-%Y'), shipment_schedules.hpl, date_format(shipment_schedules.bl_date, '%d-%b-%Y')
        order by shipment_schedules.st_date, destinations.destination_shortname, materials.material_description";

        $shipment_schedules = DB::select($query);

        $response = array(
            'status' => true,
            'tableData' => $shipment_schedules,
        );
        return Response::json($response);
    }

    public function fetch_fg_traceability(Request $request)
    {

        set_time_limit(0);
        ini_set('memory_limit', -1);
        ob_start();

        $flo_details = DB::table('flo_details');

        if (strlen($request->get('prodFrom')) > 0) {
            $prodFrom = date('Y-m-d', strtotime($request->get('prodFrom')));
            $flo_details = $flo_details->where(DB::raw('DATE_FORMAT(flo_details.created_at, "%Y-%m-%d")'), '>=', $prodFrom);
        }
        if (strlen($request->get('prodTo')) > 0) {
            $prodTo = date('Y-m-d', strtotime($request->get('prodTo')));
            $flo_details = $flo_details->where(DB::raw('DATE_FORMAT(flo_details.created_at, "%Y-%m-%d")'), '<=', $prodTo);
        }
        if ($request->get('materialNumber') != null) {
            $flo_details = $flo_details->whereIn('flo_details.material_number', $request->get('materialNumber'));
        }
        if (strlen($request->get('serialNumber')) > 0) {
            $flo_details = $flo_details->where('flo_details.serial_number', '=', $request->get('serialNumber'));
        }
        if (strlen($request->get('floNumber')) > 0) {
            $flo_details = $flo_details->where('flo_details.flo_number', '=', $request->get('floNumber'));
        }

        $flo_details = $flo_details->leftJoin('flos', 'flos.flo_number', '=', 'flo_details.flo_number');

        if (strlen($request->get('blFrom')) > 0) {
            $blFrom = date('Y-m-d', strtotime($request->get('blFrom')));
            $flo_details = $flo_details->where('flos.bl_date', '>=', $blFrom);
        }
        if (strlen($request->get('blTo')) > 0) {
            $blTo = date('Y-m-d', strtotime($request->get('blTo')));
            $flo_details = $flo_details->where('flos.bl_date', '<=', $blTo);
        }
        if (strlen($request->get('invoiceNumber')) > 0) {
            $flo_details = $flo_details->where('flos.invoice_number', '=', $request->get('invoiceNumber'));
        }

        $flo_details = $flo_details->leftJoin('materials', 'materials.material_number', '=', 'flo_details.material_number');

        if ($request->get('originGroup') != null) {
            $flo_details = $flo_details->whereIn('materials.origin_group_code', $request->get('originGroup'));
        }

        $flo_details = $flo_details->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id');

        if ($request->get('destination') != null) {
            $flo_details = $flo_details->whereIn('shipment_schedules.destination_code', $request->get('destination'));
        }
        if (strlen($request->get('shipFrom')) > 0) {
            $shipFrom = date('Y-m-d', strtotime($request->get('shipFrom')));
            $flo_details = $flo_details->where('shipment_schedules.st_date', '>=', $shipFrom);
        }
        if (strlen($request->get('shipTo')) > 0) {
            $shipTo = date('Y-m-d', strtotime($request->get('shipTo')));
            $flo_details = $flo_details->where('shipment_schedules.st_date', '<=', $shipTo);
        }

        // $flo_details = $flo_details->leftJoin('container_attachments', 'container_attachments.container_id', '=', 'flos.container_id')

        $flo_details = $flo_details->leftJoin('container_attachments', 'container_attachments.container_id', '=', 'flos.container_id')
            ->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
            ->leftJoin('origin_groups', 'origin_groups.origin_group_code', '=', 'materials.origin_group_code')
            ->leftJoin(db::raw('(select flo_number, date(created_at) as actual_st_date from flo_logs where flo_logs.status_code = 3) AS act_st'), 'act_st.flo_number', '=', 'flos.flo_number');

        if (strlen($request->get('actualFrom')) > 0) {
            $actualFrom = date('Y-m-d', strtotime($request->get('actualFrom')));
            $flo_details = $flo_details->where('act_st.actual_st_date', '>=', $actualFrom);
        }
        if (strlen($request->get('actualTo')) > 0) {
            $actualTo = date('Y-m-d', strtotime($request->get('actualTo')));
            $flo_details = $flo_details->where('act_st.actual_st_date', '<=', $actualTo);
        }

        $flo_details = $flo_details->select(
            db::raw('date_format(flo_details.created_at, "%d-%b-%Y") as pd_date'),
            'flo_details.flo_number',
            'origin_groups.origin_group_name',
            'materials.material_number',
            'materials.material_description',
            'flo_details.serial_number',
            'flo_details.quantity',
            db::raw('date_format(shipment_schedules.st_date, "%d-%b-%Y") as st_date'),
            db::raw('if(date_format(flos.bl_date, "%d-%b-%Y") is null, "-", date_format(flos.bl_date, "%d-%b-%Y")) as bl_date'),
            'destinations.destination_shortname',
            'shipment_schedules.sales_order',
            'flos.container_id',
            'flos.status',
            'flos.invoice_number',
            'act_st.actual_st_date',
            db::raw('count(container_attachments.container_id) as att'),
            'flo_details.image'
        )
            ->groupBy(
                db::raw('date_format(flo_details.created_at, "%d-%b-%Y")'),
                'flo_details.flo_number',
                'origin_groups.origin_group_name',
                'materials.material_number',
                'materials.material_description',
                'flo_details.quantity',
                'flo_details.serial_number',
                db::raw('date_format(shipment_schedules.st_date, "%d-%b-%Y")'),
                'flos.bl_date',
                'destinations.destination_shortname',
                'shipment_schedules.sales_order',
                'flos.container_id',
                'act_st.actual_st_date',
                'flos.status',
                'flos.invoice_number',
                'flo_details.image'
            )
            ->get();

        $serial_numbers = [];
        for ($i = 0; $i < count($flo_details); $i++) {
            if (!in_array($flo_details[$i]->serial_number, $serial_numbers)) {
                array_push($serial_numbers, $flo_details[$i]->serial_number);
            }
        }

        $where_serial_number = '';
        for ($i = 0; $i < count($serial_numbers); $i++) {
            $where_serial_number = $where_serial_number . "'" . $serial_numbers[$i] . "'";
            if ($i != (count($serial_numbers) - 1)) {
                $where_serial_number = $where_serial_number . ',';
            }
        }
        $where_serial_number = " AND serial_number IN (" . $where_serial_number . ") ";

        $packing = [];
        if (count($flo_details) > 0) {
            $packing = db::connection('ympimis_2')
                ->select("
                SELECT * FROM packing_documentations
                WHERE deleted_at IS NULL
                " . $where_serial_number);
        }

        $origin_groups = DB::table('origin_groups')->orderBy('origin_group_code', 'asc')->get();
        $materials = DB::table('materials')->where('category', '=', 'FG')->orderBy('material_number', 'asc')->get();
        $destinations = DB::table('destinations')->orderBy('destination_code', 'asc')->get();

        ob_end_flush();
        ob_flush();
        flush();

        return view(
            'finished_goods.traceability',
            array(
                'origin_groups' => $origin_groups,
                'materials' => $materials,
                'destinations' => $destinations,
                'flo_details' => $flo_details,
                'packing' => $packing,
            )
        )->with('page', 'FG Traceability')->with('head', 'Finished Goods');

    }

    public function fetch_fg_monthly_summary(Request $request)
    {
        $shipment_schedules = DB::table('shipment_schedules');

        if (strlen($request->get('periodFrom')) > 0) {
            $periodFrom = $request->get('periodFrom');
            $shipment_schedules = $shipment_schedules->where('st_month', '>=', $periodFrom);
        }
        if (strlen($request->get('periodTo')) > 0) {
            $periodTo = $request->get('periodTo');
            $shipment_schedules = $shipment_schedules->where('st_month', '<=', $periodTo);
        }

        $shipment_schedules = $shipment_schedules->leftJoin(DB::raw('(select flos.shipment_schedule_id, sum(if(flos.bl_date > last_day(shipment_schedules.bl_date), flos.actual, 0)) as delay from flos left join shipment_schedules on shipment_schedules.id = flos.shipment_schedule_id group by flos.shipment_schedule_id) as flos'), 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
            ->select(db::raw('date_format(st_month, "%b-%Y") as period, sum(shipment_schedules.quantity) as total, sum(flos.delay) as bo, truncate(((sum(shipment_schedules.quantity)-sum(flos.delay))/sum(shipment_schedules.quantity))*100,2) as percentage'))
            ->groupBy(db::raw('date_format(st_month, "%b-%Y")'))
            ->orderBy('st_month', 'desc')
            ->get();

        $response = array(
            'status' => true,
            'tableData' => $shipment_schedules,
        );
        return Response::json($response);
    }

    public function fetch_tb_monthly_summary(Request $request)
    {
        $period = date('Y-m', strtotime($request->get('period'))) . '-01';

        $query = "select shipment_schedules.sales_order, shipment_schedules.st_date, shipment_schedules.bl_date as bl_plan, flos.bl_date as bl_actual, materials.material_number, materials.material_description, sum(if(flos.bl_date > last_day(shipment_schedules.bl_date), flos.actual, 0)) as actual from flos left join shipment_schedules on shipment_schedules.id = flos.shipment_schedule_id left join materials on materials.material_number = flos.material_number where shipment_schedules.st_month = '" . $period . "' group by materials.material_number, materials.material_description, shipment_schedules.sales_order, shipment_schedules.st_date, shipment_schedules.bl_date, flos.bl_date having actual > 0";

        $flos = db::select($query);

        $response = array(
            'status' => true,
            'resultData' => $flos,
        );
        return Response::json($response);
    }

    public function fetch_fg_weekly_summary(Request $request)
    {

        $last = date('Y-m-d', strtotime(carbon::now()->endOfMonth()));
        $first = date('Y-m-01');

        $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
        $date_to = date('Y-m-d', strtotime($request->get('dateto')));

        if ($request->get('datefrom') != "" && $request->get('dateto') != "") {
            $year = "having week_start >= '" . $date_from . "' and week_start <= '" . $date_to . "' and plan is not null";
        } else {
            $year = "having year = '" . date('Y') . "' and week_start >= '" . $first . "' and week_start <= '" . $last . "' and plan is not null";
        }

        $query = "SELECT YEAR as year
        ,
        week_name,
        week_start,
        week_end,
        sum( plan ) AS plan,
        sum( actual_production ) AS actual_production,
        sum( diff_actual ) AS diff_actual,
        concat( TRUNCATE (( sum( actual_production )/ sum( plan ))* 100, 2 ), '%' ) AS prctg_actual,
        sum( actual_shipment ) AS actual_shipment,
        sum( diff_shipment ) AS diff_shipment,
        concat( TRUNCATE (( sum( actual_shipment )/ sum( plan ))* 100, 2 ), '%' ) AS prctg_shipment,
        sum( delay ) AS delay,
        concat( round((( sum( plan )- sum( delay ))/ sum( plan ))* 100, 2 ), '%' ) AS prctg_delay
        FROM
        (
            SELECT YEAR
            ,
            week_name,
            week_start,
            bl_target AS week_end,
            id,
            material_number,
            plan,
            sum( actual_production ) AS actual_production,
            sum( actual_production )- plan AS diff_actual,
            sum( actual_shipment ) AS actual_shipment,
            sum( actual_shipment )- plan AS diff_shipment,
            sum( delay ) AS delay
            FROM
            (
               SELECT
               a.YEAR,
               a.week_name,
               a.week_start,
               b.bl_actual,
               a.bl_target,
               b.id,
               b.material_number,
               b.quantity AS plan,
               sum( b.actual ) AS actual_production,
               IF
               (
                  b.bl_actual IS NULL,
                  0,
                  sum( b.actual )) AS actual_shipment,
               IF
               ( b.bl_actual > a.bl_target, sum( b.actual ), 0 ) AS delay
               FROM
               (
                  (
                     SELECT YEAR
                     ( week_date ) AS YEAR,
                     week_name,
                     min( week_date ) AS week_start,
                     max( week_date ) AS bl_target
                     FROM
                     weekly_calendars
                     GROUP BY
                     YEAR ( week_date ),
                     week_name
                     ) AS a
                  LEFT JOIN (
                     SELECT YEAR
                     ( shipment_schedules.bl_date ) AS YEAR,
                     concat( 'W', date_format( shipment_schedules.bl_date, '%U' )+ 1 ) AS week_name,
                     shipment_schedules.id,
                     shipment_schedules.material_number,
                     shipment_schedules.quantity,
                     flos.actual,
                     shipment_schedules.bl_date AS bl_plan,
                     flos.bl_date AS bl_actual
                     FROM
                     shipment_schedules
                     LEFT JOIN flos ON flos.shipment_schedule_id = shipment_schedules.id
                     LEFT JOIN materials ON materials.material_number = shipment_schedules.material_number
                     WHERE
                     materials.category = 'FG'
                     ) AS b ON b.YEAR = a.YEAR
                  AND b.week_name = a.week_name
                  )
               GROUP BY
               YEAR,
               week_name,
               week_start,
               bl_target,
               id,
               bl_actual,
               material_number,
               quantity
               ) AS c
            GROUP BY
            YEAR,
            week_name,
            week_start,
            bl_target,
            material_number,
            id,
            plan
            ) AS d
        GROUP BY
        YEAR,
        week_name,
        week_start,
        week_end
        " . $year . "
        ORDER BY
        week_start ASC";

        $weekly_calendars = DB::select($query);
        return DataTables::of($weekly_calendars)->make(true);
    }

    public function fetch_fg_container_departure(Request $request)
    {

        // $container_schedules = DB::table('container_schedules');

        if (strlen($request->get('datefrom')) > 0) {
            $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
        } else {
            $date_from = date('Y-m-01');
        }

        if (strlen($request->get('dateto')) > 0) {
            $date_to = date('Y-m-d', strtotime($request->get('dateto')));
        } else {
            $date_to = date('Y-m-t');
        }
        // else{
        //     $month = date('Y-m');
        //     $container_schedules = $container_schedules->where(DB::raw('DATE_FORMAT(container_schedules.shipment_date, "%Y-%m")'), '>=', $month);
        // }
        // if(strlen($request->get('dateto')) > 0){
        //     $date_to = date('Y-m-d', strtotime($request->get('dateto')));
        //     $container_schedules = $container_schedules->where(DB::raw('DATE_FORMAT(container_schedules.shipment_date, "%Y-%m-%d")'), '<=', $date_to);
        // }

        // $count1 = $container_schedules->select('container_schedules.shipment_date', DB::raw('"Open" as status'), DB::raw('count(container_id)-count(if(container_schedules.container_number is null or container_schedules.container_number = "", null, 1)) as quantity'))
        // ->groupBy('container_schedules.shipment_date')->orderBy('container_schedules.shipment_date')->get();

        // $count2 = $container_schedules->select('container_schedules.shipment_date', DB::raw('"Departed" as status'), DB::raw('count(if(container_schedules.container_number is null or container_schedules.container_number = "", null, 1)) as quantity'))
        // ->groupBy('container_schedules.shipment_date')->orderBy('container_schedules.shipment_date')->get();

        // $table1 = $count1->merge($count2);

        $table1 = db::select("SELECT
            DATE_FORMAT(stuffing_date, '%Y-%m-%d') AS shipment_date,
            'Open' AS `status`,
            SUM( plan )- SUM(
               IF
               ( actual_on_board IS NOT NULL, 1, 0 )) AS quantity
            FROM
            shipment_reservations
            WHERE
            stuffing_date BETWEEN '" . $date_from . "'
            AND '" . $date_to . "'
            AND `status` = 'BOOKING CONFIRMED'
            GROUP BY
            stuffing_date UNION ALL
            SELECT
            stuffing_date AS shipment_date,
            'Departed' AS `status`,
            SUM(
            IF
            ( actual_on_board IS NOT NULL, 1, 0 )) AS quantity
            FROM
            shipment_reservations
            WHERE
            stuffing_date BETWEEN '" . $date_from . "'
            AND '" . $date_to . "'
            AND `status` = 'BOOKING CONFIRMED'
            GROUP BY
            stuffing_date");

        // // $count3 = DB::table('flos')
        // // ->leftJoin('container_schedules', 'container_schedules.container_id', '=', 'flos.container_id')
        // // ->leftJoin('destinations', 'destinations.destination_code', '=', 'container_schedules.destination_code');

        // $container_schedules2 = DB::table('container_schedules');

        // if(strlen($request->get('datefrom')) > 0){
        //     $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
        //     $container_schedules2 = $container_schedules2->where(DB::raw('DATE_FORMAT(container_schedules.shipment_date, "%Y-%m-%d")'), '>=', $date_from);
        // }
        // else{
        //     $month = date('Y-m');
        //     $container_schedules2 = $container_schedules2->where(DB::raw('DATE_FORMAT(container_schedules.shipment_date, "%Y-%m")'), '>=', $month);
        // }
        // if(strlen($request->get('dateto')) > 0){
        //     $date_to = date('Y-m-d', strtotime($request->get('dateto')));
        //     $container_schedules2 = $container_schedules2->where(DB::raw('DATE_FORMAT(container_schedules.shipment_date, "%Y-%m-%d")'), '<=', $date_to);
        // }

        // $total_plan = $container_schedules2
        // ->count('container_id');

        // $table2 = $container_schedules2
        // ->leftJoin('flos', 'container_schedules.container_id', '=', 'flos.container_id')
        // ->leftJoin('destinations', 'destinations.destination_code', '=', 'container_schedules.destination_code')
        // ->whereNotNull('container_schedules.container_number')
        // ->select('destinations.destination_shortname', DB::raw('count(distinct container_schedules.container_id) as quantity'))
        // ->groupBy('destinations.destination_shortname')
        // ->orderBy(db::raw('quantity'), 'desc')
        // ->get();

        // $total_actual = $count2->sum('quantity');

        $table2 = DB::select("SELECT
            port_of_delivery as destination_shortname,
            SUM(
               IF
               ( actual_on_board IS NULL, 0, 1 )) AS `quantity`
            FROM
            shipment_reservations
            WHERE
            stuffing_date BETWEEN '" . $date_from . "'
            AND '" . $date_to . "'
            AND `status` = 'BOOKING CONFIRMED'
            GROUP BY
            port_of_delivery
            ORDER BY
            port_of_delivery ASC");

        $response = array(
            'status' => true,
            'jsonData1' => $table1,
            'jsonData2' => $table2,
        );
        return Response::json($response);
    }

    public function fetch_fg_stock()
    {

        $stocks = db::select("SELECT
            f.material_number,
            m.material_description,
            m.hpl,
            IF
            ( f.STATUS = 'M', 'Maedaoshi', d.destination_shortname ) AS destination,
            f.actual as quantity,
            m.base_unit,
            mv.length,
            mv.width,
            mv.height,
            mv.lot_carton,
            round(( mv.length * mv.width * mv.height )*( f.actual / mv.lot_carton ), 2 ) AS m3,
            IF
            (
                f.STATUS = '1' 
                OR f.STATUS = 'M',
                'Production',
                IF
                ( f.STATUS = '1', 'InTransit', 'Warehouse' )) AS location 
            FROM
            flos AS f
            LEFT JOIN shipment_schedules AS ss ON ss.id = f.shipment_schedule_id
            LEFT JOIN material_volumes AS mv ON mv.material_number = f.material_number
            LEFT JOIN materials AS m ON m.material_number = f.material_number
            LEFT JOIN destinations AS d ON d.destination_code = ss.destination_code 
            WHERE
            f.`STATUS` IN ( '0', '1', '2', 'M' ) 
            AND f.actual > 0 
            AND f.deleted_at IS NULL 
            ORDER BY
            f.material_number ASC");

        $response = array(
            'status' => true,
            'stocks' => $stocks,
        );
        return Response::json($response);
    }

    public function fetch_fg_production()
    {

        $st_month = date('Y-m-01');

        $st_month2 = date('F y');

        $shipment_schedule = DB::table('shipment_schedules')
            ->leftJoin('flos', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
            ->where('st_month', '=', $st_month);

        $total_plan = DB::table('shipment_schedules')->where('st_month', '=', $st_month)->sum('shipment_schedules.quantity');
        $total_production = $shipment_schedule->where('flos.status', '<>', 'M')->sum('flos.actual');
        $total_delivery = $shipment_schedule->whereIn('flos.status', ['2', '3', '4'])->sum('flos.actual');
        $total_shipment = $shipment_schedule->whereIn('flos.status', ['3', '4'])->sum('flos.actual');

        $response = array(
            'status' => true,
            'total_plan' => $total_plan,
            'total_production' => $total_production,
            'total_delivery' => $total_delivery,
            'total_shipment' => $total_shipment,
            'st_month' => $st_month2,
        );
        return Response::json($response);
    }

    public function fetch_tb_container_departure(Request $request)
    {
        $container_schedules = DB::table('container_schedules')
            ->leftJoin('container_attachments', 'container_attachments.container_id', '=', 'container_schedules.container_id')
            ->leftJoin('destinations', 'destinations.destination_code', '=', 'container_schedules.destination_code')
            ->where('container_schedules.shipment_date', '=', $request->get('st_date'))
            ->select('container_schedules.container_id', 'destinations.destination_shortname', 'container_schedules.container_number', 'container_schedules.shipment_date', DB::raw('count(container_attachments.container_id) as att'))
            ->groupBy('container_schedules.container_id', 'destinations.destination_shortname', 'container_schedules.container_number', 'container_schedules.shipment_date')
            ->get();

        $response = array(
            'status' => true,
            'table' => $container_schedules,
            'st_date' => $request->get('st_date'),
        );
        return Response::json($response);
    }

    public function fetch_tb_stock(Request $request)
    {
        if ($request->get('destination') == 'Maedaoshi') {
            $destination = null;
        } else {
            $destination = $request->get('destination');
        }
        $stock = DB::table('flos')
            ->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')
            ->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
            ->leftJoin('material_volumes', 'material_volumes.material_number', 'flos.material_number')
            ->leftJoin('materials', 'materials.material_number', 'flos.material_number')
            ->whereIn('flos.status', $request->get('status'))
            ->where('destinations.destination_shortname', '=', $destination)
            ->select(
                'flos.material_number',
                'materials.material_description',
                'material_volumes.length',
                'material_volumes.height',
                'material_volumes.width',
                'material_volumes.lot_carton',
                'material_volumes.cubic_meter',
                DB::raw('sum(flos.actual) as actual')
            )
            ->groupBy(
                'flos.material_number',
                'materials.material_description',
                'material_volumes.length',
                'material_volumes.height',
                'material_volumes.width',
                'material_volumes.lot_carton',
                'material_volumes.cubic_meter'
            );

        $new_stock = DB::table('knock_downs')
            ->leftJoin('knock_down_details', 'knock_down_details.kd_number', '=', 'knock_downs.kd_number')
            ->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'knock_down_details.shipment_schedule_id')
            ->leftJoin('destinations', 'shipment_schedules.destination_code', '=', 'destinations.destination_code')
            ->leftJoin('material_volumes', 'material_volumes.material_number', 'knock_down_details.material_number')
            ->leftJoin('materials', 'materials.material_number', 'knock_down_details.material_number')
            ->whereIn('knock_downs.status', $request->get('status'))
            ->where('destinations.destination_shortname', '=', $destination)
            ->select(
                'knock_down_details.material_number',
                'materials.material_description',
                'material_volumes.length',
                'material_volumes.height',
                'material_volumes.width',
                'material_volumes.lot_carton',
                'material_volumes.cubic_meter',
                DB::raw('sum(knock_down_details.quantity) as actual')
            )
            ->groupBy(
                'knock_down_details.material_number',
                'materials.material_description',
                'material_volumes.length',
                'material_volumes.height',
                'material_volumes.width',
                'material_volumes.lot_carton',
                'material_volumes.cubic_meter'
            )
            ->union($stock)
            ->get();

        if (in_array('0', $request->get('status')) || in_array('M', $request->get('status'))) {
            $location = 'Production';
        } elseif (in_array('1', $request->get('status'))) {
            $location = 'InTransit';
        } elseif (in_array('2', $request->get('status'))) {
            $location = 'FSTK';
        }
        // else{
        //     $location = 'Production';
        // }

        $response = array(
            'status' => true,
            'table' => $new_stock,
            'title' => $request->get('destination'),
            'location' => $location,
        );
        return Response::json($response);
    }

    public function fetch_tb_production(Request $request)
    {
        $st_month = date('Y-m-01');

        $shipment_schedules = DB::table('shipment_schedules')
            ->leftJoin('flos', 'flos.shipment_schedule_id', '=', 'shipment_schedules.id')
            ->leftJoin('materials', 'materials.material_number', '=', 'shipment_schedules.material_number')
            ->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
            ->where('st_month', '=', $st_month);

        if ($request->get('id') == 'production') {
            $table = $shipment_schedules->select('shipment_schedules.id', 'shipment_schedules.sales_order', db::raw('date_format(shipment_schedules.st_month, "%b-%Y") as st_month'), 'shipment_schedules.material_number', 'materials.material_description', 'destinations.destination_shortname', 'shipment_schedules.st_date', 'shipment_schedules.bl_date', 'shipment_schedules.quantity', DB::raw('if(sum(flos.actual) is null, 0, sum(flos.actual)) as actual'), DB::raw('if(sum(flos.actual) is null, 0, sum(flos.actual))-shipment_schedules.quantity as diff'))->groupBy('shipment_schedules.id', 'shipment_schedules.sales_order', 'shipment_schedules.st_month', 'shipment_schedules.material_number', 'materials.material_description', 'destinations.destination_shortname', 'shipment_schedules.st_date', 'shipment_schedules.bl_date', 'shipment_schedules.quantity')->orderBy('st_date', 'asc')->get();
        } elseif ($request->get('id') == 'delivery') {
            $query = "select id, sales_order, date_format(st_month, '%b-%Y') as st_month, material_number, material_description, destination_shortname, st_date, bl_date, quantity, sum(actual) as actual, sum(actual)-quantity as diff from (select shipment_schedules.id, shipment_schedules.sales_order, shipment_schedules.st_month, shipment_schedules.material_number, materials.material_description, destinations.destination_shortname, shipment_schedules.st_date, shipment_schedules.bl_date, shipment_schedules.quantity, if(sum(flos.actual) is null or flos.status < 2, 0, sum(flos.actual)) as actual from shipment_schedules left join flos on flos.shipment_schedule_id = shipment_schedules.id left join materials on materials.material_number = shipment_schedules.material_number left join destinations on destinations.destination_code = shipment_schedules.destination_code where st_month = :st_month group by shipment_schedules.id, shipment_schedules.sales_order, shipment_schedules.st_month, shipment_schedules.material_number, materials.material_description, destinations.destination_shortname, shipment_schedules.st_date, shipment_schedules.bl_date, shipment_schedules.quantity, flos.status order by st_date desc) A group by id, sales_order, st_month, material_number, material_description, destination_shortname, st_date, bl_date, quantity order by st_date asc";
            $table = DB::select($query, ['st_month' => $st_month]);
        } elseif ($request->get('id') == 'shipment') {
            $query = "select id, sales_order, date_format(st_month, '%b-%Y') as st_month, material_number, material_description, destination_shortname, st_date, bl_date, quantity, sum(actual) as actual, sum(actual)-quantity as diff from (select shipment_schedules.id, shipment_schedules.sales_order, shipment_schedules.st_month, shipment_schedules.material_number, materials.material_description, destinations.destination_shortname, shipment_schedules.st_date, shipment_schedules.bl_date, shipment_schedules.quantity, if(sum(flos.actual) is null or flos.status < 3, 0, sum(flos.actual)) as actual from shipment_schedules left join flos on flos.shipment_schedule_id = shipment_schedules.id left join materials on materials.material_number = shipment_schedules.material_number left join destinations on destinations.destination_code = shipment_schedules.destination_code where st_month = :st_month group by shipment_schedules.id, shipment_schedules.sales_order, shipment_schedules.st_month, shipment_schedules.material_number, materials.material_description, destinations.destination_shortname, shipment_schedules.st_date, shipment_schedules.bl_date, shipment_schedules.quantity, flos.status order by st_date desc) A group by id, sales_order, st_month, material_number, material_description, destination_shortname, st_date, bl_date, quantity order by st_date asc";
            $table = DB::select($query, ['st_month' => $st_month]);
        }

        return DataTables::of($table)->make(true);
    }

    public function download_att_container_departure(Request $request)
    {
        $container_attachments = DB::table('container_attachments')->where('container_id', '=', $request->get('container_id'))->get();

        $zip = new ZipArchive();
        $zip_name = $request->get('container_id') . ".zip";
        $zip_path = public_path() . '/' . $zip_name;
        File::delete($zip_path);
        $zip->open($zip_name, ZipArchive::CREATE);

        foreach ($container_attachments as $container_attachment) {
            $file_path = public_path() . $container_attachment->file_path . $container_attachment->file_name;
            $file_name = $container_attachment->file_name;
            $zip->addFile($file_path, $file_name);
        }
        $zip->close();

        $path = asset($zip_name);

        $response = array(
            'status' => true,
            'file_path' => $path,
        );
        return Response::json($response);
    }
}