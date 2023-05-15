<?php

namespace App\Http\Controllers;

use App\EfficiencyUpload;
use App\OriginGroup;
use App\ProductionForecastResume;
use App\SalesBudgetResume;
use App\WeeklyCalendar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

class DisplayController extends Controller
{

    public function __construct()
    {
        $this->exp = [
            // GME
            'PI1206001',
            'PI1301001',
            'PI1612005',
            'PI1712018',
            'PI2111044',
            'PI2111045',
            'PI0109004',
            'PI9709001',
            'PI9905001',
            'PI2302030',

            // PC
            'PI0008010',
            'PI0111001',
            'PI0812002',
            'PI2002019',

            // MIS
            'PI0103002',
            'PI0906001',
            'PI1412008',
            'PI1910002',
            'PI1910003',
            'PI2002021',
            'PI2009022',
            'PI2101043',
            'PI2101044',

            // LOGISTIC
            'PI9805006',
            'PI0711002',
            'PI1505003',
            'PI2002020',
            'PI1111001',

            // MTC
            'PI0302001',

            // MANAGER
            'PI0108010',
            'PI0703002',
            'PI9707006',
            'PI9707011',
            'PI9710001',
            'PI9807014',
            'PI9902017',
            'PI9906002',

            'DISPLAY',
        ];
    }

    public function indexEmptyStock()
    {
        $title = 'Empty Stock';
        $title_jp = '';

        return view('displays.empty_stock', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ));
    }

    public function indexProductionWarehouse()
    {
        $title = 'Pengiriman ke FSTK vs Shipment Schedule';
        $title_jp = '';

        $fiscal_years = db::table('weekly_calendars')
            ->select('fiscal_year')
            ->where('week_date', '>=', '2020-04-01')
            ->orderBy('week_date', 'DESC')
            ->distinct()
            ->get();

        return view('displays.production_to_warehouse', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'fiscal_years' => $fiscal_years,
        ))->with('page', 'Display Production Resume')->with('head', 'Display');
    }

    public function fetchEmptyStock()
    {
        $exclude_location = [
            '401',
            'FA0R',
            'FA1R',
            'FSTK',
            'G0W214',
            'G0W216',
            'G0W217',
            'G0W218',
            'LA0R',
            'MMJR',
            'MSCR',
            'MSNG',
            'MSTK',
            'OTHR',
            'SA0R',
            'SA1R',
            'VA0R',
            'WPCS',
            'WPPN',
            'WPRC',
            'WSCR',
            'YCJP',
            'YCJR',
        ];

        $materials = db::connection('kitto')
            ->table('materials')
            ->whereNotNull('store')
            ->get();

        $stocks = db::connection('ymes_rio')
            ->table('vd_mes0010')
            ->whereNotIn('location_code', $exclude_location)
            ->get();

        $formatted_stocks = [];
        for ($i = 0; $i < count($stocks); $i++) {
            $key = $stocks[$i]->item_code . '_' . $stocks[$i]->location_code;
            $formatted_stocks[$key] = $stocks[$i];
        }

        $stock_materials = [];
        $stores = [];
        for ($i = 0; $i < count($materials); $i++) {
            $stock = 0;
            if (isset($formatted_stocks[$materials[$i]->material_number . '_' . $materials[$i]->location])) {
                $stock = $formatted_stocks[$materials[$i]->material_number . '_' . $materials[$i]->location]->stockqty;
            }

            $row = array();
            $row['material_number'] = $materials[$i]->material_number;
            $row['material_description'] = $materials[$i]->description;
            $row['store'] = $materials[$i]->store;
            $row['stock'] = $stock;
            $stock_materials[] = (object) $row;

            if (!in_array($materials[$i]->store, $stores)) {
                array_push($stores, $materials[$i]->store);
            }

        }

        $response = array(
            'status' => true,
            'stock_materials' => $stock_materials,
            'stores' => $stores,
        );
        return Response::json($response);

    }

    public function fetchProductionWarehouse(Request $request)
    {

        $fiscal_year = $request->get('fiscal_year');

        $weekly_calendar = db::table('weekly_calendars')
            ->where('fiscal_year', '=', $fiscal_year)
            ->select('fiscal_year', db::raw('min(week_date) as min_date'), db::raw('max(week_date) as max_date'))
            ->groupBy('fiscal_year')
            ->first();

        $weekly_calendars = db::table('weekly_calendars')
            ->where('fiscal_year', '=', $fiscal_year)
            ->select('fiscal_year', db::raw('DATE_FORMAT(week_date, "%Y-%m") AS st_month'))
            ->orderBy('week_date', 'ASC')
            ->distinct()
            ->get();

        $min_date = $weekly_calendar->min_date;
        $max_date = $weekly_calendar->max_date;

        if ($max_date >= date('Y-m-d')) {
            $max_date = date('Y-m-d');
        }

        $details = db::select("SELECT
            ss.id,
            flo.fiscal_year,
            DATE_FORMAT(ss.st_month, '%Y-%m') AS st_month,
            flo.material_number,
            flo.material_description,
            flo.hpl,
            ss.st_date,
            d.destination_shortname,
            ss.quantity AS plan_total,
            sum( flo.ok ) AS ok_total,
            sum( flo.ng ) AS ng_total,
            max( flo.ng_date ) AS ng_date
            FROM
            (
                SELECT
                ss.id AS shipment_schedule_id,
                wc.fiscal_year,
                ss.st_date,
                m.material_number,
                m.material_description,
                m.hpl,
                f.flo_number,
                f.actual,
                date( fl.created_at ) AS wh_date,
                IF
                ( date( fl.created_at ) > ss.st_date, f.actual, 0 ) AS ng,
                IF
                ( date( fl.created_at ) > ss.st_date, fl.created_at, '' ) AS ng_date,
                IF
                ( date( fl.created_at ) <= ss.st_date, f.actual, 0 ) AS ok
                FROM
                shipment_schedules AS ss
                LEFT JOIN flos AS f ON f.shipment_schedule_id = ss.id
                LEFT JOIN ( SELECT flo_number, date( created_at ) AS created_at FROM flo_logs WHERE status_code = 2 ) AS fl ON fl.flo_number = f.flo_number
                LEFT JOIN materials AS m ON m.material_number = ss.material_number
                LEFT JOIN weekly_calendars AS wc ON wc.week_date = ss.st_month
                WHERE
                m.category = 'FG'
                AND m.origin_group_code IN ( '041', '042', '043' )
                AND ss.st_month >= '" . $min_date . "'
                AND ss.st_month <= '" . $max_date . "'
                AND ss.st_date <= '" . $max_date . "'
                ) AS flo
                LEFT JOIN shipment_schedules AS ss ON ss.id = flo.shipment_schedule_id
                LEFT JOIN destinations AS d ON d.destination_code = ss.destination_code
                GROUP BY
                ss.id,
                flo.fiscal_year,
                ss.st_month,
                flo.material_number,
                flo.material_description,
                flo.hpl,
                ss.st_date,
                d.destination_shortname,
                ss.quantity
                ORDER BY
                ss.st_date ASC");

        $resumes = array();

        $response = array(
            'status' => true,
            'weekly_calendars' => $weekly_calendars,
            'details' => $details,
            'resumes' => $resumes,
            'min_date' => date('d M Y', strtotime($min_date)),
            'max_date' => date('d M Y', strtotime($max_date)),
        );
        return Response::json($response);
    }

    public function indexProductionResume()
    {
        $title = 'Production Shortage Summary';
        $title_jp = '';

        return view('displays.production_resume', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Production Resume')->with('head', 'Display');
    }

    public function indexEfficiencyMonitoring()
    {

        $title = 'Daily Efficiency Monitoring';
        $title_jp = '日次効率の監視';

        return view('displays.efficiency_monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Display Efficiency Monitoring')->with('head', 'Display');
    }

    public function indexStockroomMonitoring()
    {

        $title = 'Stockroom Monitoring';
        $title_jp = 'ストックルームの監視';

        return view('displays.assys.stockroom_monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Display Stockroom Monitoring')->with('head', 'Display');
    }

    public function indexEfficiencyMonitoringMonthly()
    {

        $title = 'Monthly Efficiency Monitoring';
        $title_jp = '月次効率の監視';

        $weeks = db::select("SELECT DISTINCT
               fiscal_year,
               DATE_FORMAT( week_date, '%M' ) AS bulan,
               DATE_FORMAT( week_date, '%Y-%m' ) AS indek
               FROM
               weekly_calendars
               WHERE
               week_date >= '2020-04-01'
               AND week_date <= '" . date('Y-m-d') . "'
               ORDER BY
               week_date DESC");

        // $cost_centers = db::select("SELECT DISTINCT
        //     cost_center_eff
        //     FROM
        //     cost_centers2
        //     WHERE
        //     cost_center_eff IS NOT NULL
        //     ORDER BY
        //     cost_center_eff ASC");

        $last_datas = db::select("SELECT
               cost_center_name,
               max( total_date ) AS last_date
               FROM
               efficiency_uploads
               WHERE
               cost_center_name IN ( SELECT cost_center_eff_2 FROM cost_centers2 WHERE cost_center_eff_2 IS NOT NULL )
               GROUP BY
               cost_center_name");

        return view('displays.efficiencies.efficiency_monitoring_monthly', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'weeks' => $weeks,
            // 'cost_centers' => $cost_centers,
            'last_datas' => $last_datas,
        ))->with('page', 'Display Efficiency Monitoring')->with('head', 'Display');
    }

    public function fetchProductionResume(Request $request)
    {

        $first = date('Y-m-01');
        $last = date('Y-m-d');

        if (strlen($request->get('filter_date')) > 0) {
            $first = date('Y-m-01', strtotime($request->get('filter_date')));
            $last = date('Y-m-d', strtotime($request->get('filter_date')));
        }

        $work_days = db::select("SELECT * FROM weekly_calendars
               WHERE week_date BETWEEN '" . $first . "' AND '" . $last . "'
               AND remark <> 'H'");

        $finished_goods = db::select("SELECT
               plan.hpl,
               plan,
               actual
               FROM
               (
                   SELECT
                   m.hpl,
                   sum( ps.quantity ) AS plan
                   FROM
                   production_schedules AS ps
                   LEFT JOIN materials AS m ON m.material_number = ps.material_number
                   WHERE
                   ps.due_date >= '" . $first . "'
                   AND ps.due_date <= '" . $last . "'
                   AND m.category = 'FG'
                   GROUP BY
                   m.hpl
                   ) AS plan
                   LEFT JOIN (
                   SELECT
                   m.hpl,
                   sum( fd.quantity ) AS actual
                   FROM
                   flo_details AS fd
                   LEFT JOIN materials AS m ON m.material_number = fd.material_number
                   WHERE
                   fd.created_at >= '" . $first . " 00:00:00'
                   AND fd.created_at <= '" . $last . " 23:59:59'
                   AND m.category = 'FG'
                   GROUP BY
                   m.hpl
                   ) AS actual ON plan.hpl = actual.hpl
                   order by field(plan.hpl, 'FLFG','CLFG','ASFG','TSFG','PN','RC','VN')");

        $knock_downs = db::select("SELECT m.hpl, SUM(ps.quantity) AS plan, SUM(ps.actual_quantity) AS actual FROM production_schedules ps
                   LEFT JOIN materials m ON m.material_number = ps.material_number
                   WHERE ps.due_date BETWEEN '" . $first . "' AND '" . $last . "'
                   AND m.category = 'KD'
                   AND m.hpl IN ('ASSY-SX', 'SUBASSY-SX', 'SUBASSY-FL', 'SUBASSY-CL', 'CL-BODY', 'CASE', 'TANPO')
                   GROUP BY m.hpl
                   ORDER BY FIELD(m.hpl, 'ASSY-SX', 'SUBASSY-SX', 'SUBASSY-FL', 'SUBASSY-CL', 'CL-BODY', 'CASE', 'TANPO')
                   ");

        $response = array(
            'status' => true,
            'work_days' => $work_days,
            'finished_goods' => $finished_goods,
            'knock_downs' => $knock_downs,
            'title_date' => date('d', strtotime($first)) . ' - ' . date('d M Y', strtotime($last)),
        );
        return Response::json($response);

    }

    public function fetchStockroomMonitoring(Request $request)
    {

        $date = date('Y-m-d');

        if ($request->get('period') != "") {
            $date = date('Y-m-d', strtotime($request->get('period')));
        }

        $first = date('Y-m-01', strtotime($date));
        $now = date('Y-m-d', strtotime($date));
        $yesterday = date('Y-m-d', strtotime('-1 day', strtotime($date)));
        $end = date('Y-m-t', strtotime($date));

        // ( histories.transfer_movement_type = '9I4', -( histories.lot ), 0 ))) AS picking,
        // ( histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 6, 0, -(histories.lot)),0))) AS picking,

        // ( histories.transfer_movement_type = '9I4', -( histories.lot ), 0 )))) AS plan,
        // ( histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 6, 0, -(histories.lot)),0)))) AS plan,

        // ( histories.transfer_movement_type = '9I4', ( histories.lot ), 0 )) AS minus,
        // ( histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 6, 0, histories.lot),0)) AS minus,

        // $stockroom_keys = db::select("SELECT
        //     materials.issue_storage_location,
        //     materials.hpl,
        //     materials.model,
        //     materials.`key`,
        //     materials.surface,
        //     sum( plan ) AS plan,
        //     sum( picking ) AS picking,
        //     sum( plus ) AS plus,
        //     sum( minus ) AS minus,
        //     sum( stock ) AS stock,
        //     sum( plan_ori ) AS plan_ori,
        //     (
        //     sum( plan )- sum( picking )) AS diff,
        //     sum( stock ) - (
        //     sum( plan )- sum( picking )) AS diff2,
        //     round( sum( stock ) / sum( plan ), 1 ) AS ava,
        //     IF
        //     (
        //     round( sum( stock ) / sum( plan ), 1 )>= 1,
        //     1,
        //     0
        //     ) AS safe,
        //     IF
        //     (
        //     round( sum( stock ) / sum( plan ), 1 )< 1
        //     AND round( sum( stock ) / sum( plan ), 1 )> 0,
        //     1,
        //     0
        //     ) AS unsafe,
        //     IF
        //     ( sum( stock )<= 0, 1, 0 ) AS zero,
        //     IF
        //     ( round( sum( stock ) / sum( plan ), 1 )> 2, 1, 0 ) AS ava_ultra_safe,
        //     IF
        //     (
        //     round( sum( stock ) / sum( plan ), 1 )>= 1
        //     AND round( sum( stock ) / sum( plan ), 1 )<= 2,
        //     1,
        //     0
        //     ) AS ava_safe,
        //     IF
        //     (
        //     round( sum( stock ) / sum( plan ), 1 )< 1
        //     AND round( sum( stock ) / sum( plan ), 1 )> 0,
        //     1,
        //     0
        //     ) AS ava_unsafe,
        //     IF
        //     ( round( sum( stock ) / sum( plan ), 1 ) <= 0, 1, 0 ) AS ava_zero
        //     FROM
        //     (
        //     SELECT
        //     material_number,
        //     sum( plan ) AS plan,
        //     sum( picking ) AS picking,
        //     sum( plus ) AS plus,
        //     sum( minus ) AS minus,
        //     sum( stock ) AS stock,
        //     sum( plan_ori ) AS plan_ori
        //     FROM
        //     (
        //     SELECT
        //     material_number,
        //     plan,
        //     picking,
        //     plus,
        //     minus,
        //     stock,
        //     plan_ori
        //     FROM
        //     (
        //     SELECT
        //     materials.material_number,
        //     0 AS plan,
        //     sum(
        //     IF
        //     (
        //     histories.transfer_movement_type = '9I3',
        //     histories.lot,
        //     IF
        //     ( histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 5, 0, -(histories.lot)),0))) as picking,
        //     0 AS plus,
        //     0 AS minus,
        //     0 AS stock,
        //     0 AS plan_ori
        //     FROM
        //     (
        //     SELECT
        //     materials.id,
        //     materials.material_number
        //     FROM
        //     kitto.materials
        //     WHERE
        //     materials.location IN ( 'SX51', 'CL51' )
        //     AND category = 'key'
        //     ) AS materials
        //     LEFT JOIN kitto.histories ON materials.id = histories.transfer_material_id
        //     WHERE
        //     date( histories.created_at ) = '".$now."'
        //     AND histories.category IN ( 'transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment' )
        //     GROUP BY
        //     materials.material_number
        //     ) AS pick UNION ALL
        //     SELECT
        //     inventories.material_number,
        //     0 AS plan,
        //     0 AS picking,
        //     0 AS plus,
        //     0 AS minus,
        //     sum( inventories.lot ) AS stock,
        //     0 AS plan_ori
        //     FROM
        //     kitto.inventories
        //     LEFT JOIN kitto.materials ON materials.material_number = inventories.material_number
        //     WHERE
        //     materials.location IN ( 'SX51', 'CL51' )
        //     AND materials.category = 'key'
        //     GROUP BY
        //     inventories.material_number UNION ALL
        //     SELECT
        //     material_number,
        //     sum( plan ) AS plan,
        //     0 AS picking,
        //     0 AS plus,
        //     0 AS minus,
        //     0 AS stock,
        //     sum( plan_ori ) AS plan_ori
        //     FROM
        //     (
        //     SELECT
        //     materials.material_number,
        //     -(
        //     sum(
        //     IF
        //     (
        //     histories.transfer_movement_type = '9I3',
        //     histories.lot,
        //     IF
        //     ( histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 5, 0, -(histories.lot)),0)))) AS plan,
        //     0 AS plan_ori
        //     FROM
        //     (
        //     SELECT
        //     materials.id,
        //     materials.material_number
        //     FROM
        //     kitto.materials
        //     WHERE
        //     materials.location IN ( 'SX51', 'CL51' )
        //     AND category = 'key'
        //     ) AS materials
        //     LEFT JOIN kitto.histories ON materials.id = histories.transfer_material_id
        //     WHERE
        //     date( histories.created_at ) >= '".$first."'
        //     AND date( histories.created_at ) <= '".$yesterday."'
        //     AND histories.category IN ( 'transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment' )
        //     GROUP BY
        //     materials.material_number UNION ALL
        //     SELECT
        //     assy_picking_schedules.material_number,
        //     sum( quantity ) AS plan,
        //     sum( quantity ) AS plan_ori
        //     FROM
        //     assy_picking_schedules
        //     LEFT JOIN materials ON materials.material_number = assy_picking_schedules.material_number
        //     WHERE
        //     due_date >= '".$first."'
        //     AND due_date <= '".$now."'
        //     AND assy_picking_schedules.remark IN ( 'SX51', 'CL51' )
        //     GROUP BY
        //     assy_picking_schedules.material_number
        //     ) AS plan
        //     GROUP BY
        //     material_number UNION ALL
        //     SELECT
        //     materials.material_number,
        //     0 AS plan,
        //     0 AS picking,
        //     sum(
        //     IF
        //     ( histories.transfer_movement_type = '9I3', histories.lot, 0 )) AS plus,
        //     sum(
        //     IF
        //     ( histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 5, 0, histories.lot),0)) AS minus,
        //     0 AS stock,
        //     0 AS plan_ori
        //     FROM
        //     (
        //     SELECT
        //     materials.id,
        //     materials.material_number
        //     FROM
        //     kitto.materials
        //     WHERE
        //     materials.location IN ( 'SX51', 'CL51' )
        //     AND category = 'key'
        //     ) AS materials
        //     LEFT JOIN kitto.histories ON materials.id = histories.transfer_material_id
        //     WHERE
        //     date( histories.created_at ) >= '".$first."'
        //     AND date( histories.created_at ) <= '".$now."'
        //     AND histories.category IN ( 'transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment' )
        //     GROUP BY
        //     materials.material_number
        //     ) AS final
        //     GROUP BY
        //     material_number
        //     HAVING
        //     plan_ori > 0
        //     ) AS final2
        //     JOIN materials ON final2.material_number = materials.material_number
        //     GROUP BY
        //     materials.issue_storage_location,
        //     materials.hpl,
        //     materials.model,
        //     materials.`key`,
        //     materials.surface,
        //     materials.issue_storage_location
        //     ORDER BY
        //     diff DESC");

        $stockroom_keys = db::select("SELECT
               issue_storage_location,
               hpl,
               model,
               `key`,
               surface,
               plan,
               plan_schedule,
               picking,
               plus,
               minus,
               stock,
               plan_ori,
               diff,
               diff2,
               ava,
               IF
               ( ava >= 1, 1, 0 ) AS safe,
               IF
               ( ava < 1 AND ava > 0, 1, 0 ) AS unsafe,
               IF
               ( stock <= 0, 1, 0 ) AS zero,
               IF
               ( ava > 2, 1, 0 ) AS ava_ultra_safe,
               IF
               ( ava >= 1 AND ava <= 2, 1, 0 ) AS ava_safe,
               IF
               ( ava < 1 AND ava > 0, 1, 0 ) AS ava_unsafe,
               IF
               ( ava <= 0, 1, 0 ) AS ava_zero
               FROM
               (
                   SELECT
                   materials.issue_storage_location,
                   materials.hpl,
                   materials.model,
                   materials.`key`,
                   materials.surface,
                   sum( plan ) AS plan,
                   round( sum( plan_schedule ) , 0 ) AS plan_schedule,
                   sum( picking ) AS picking,
                   sum( plus ) AS plus,
                   sum( minus ) AS minus,
                   sum( stock ) AS stock,
                   sum( plan_ori ) AS plan_ori,
                   (
                       sum( plan )- sum( picking )) AS diff,
                   sum( stock ) - (
                       sum( plan )- sum( picking )) AS diff2,
                   IF
                   (
                       sum( plan ) <= 0,
                       round( sum( stock )/ sum( plan_schedule ), 1 ),
                       round( sum( stock ) / sum( plan ), 1 )) AS ava
                   FROM
                   (
                       SELECT
                       material_number,
                       sum( plan_schedule ) AS plan_schedule,
                       sum( plan ) AS plan,
                       sum( picking ) AS picking,
                       sum( plus ) AS plus,
                       sum( minus ) AS minus,
                       sum( stock ) AS stock,
                       sum( plan_ori ) AS plan_ori
                       FROM
                       (
                           SELECT
                           material_number,
                           plan_schedule,
                           plan,
                           picking,
                           plus,
                           minus,
                           stock,
                           plan_ori
                           FROM
                           (
                               SELECT
                               materials.material_number,
                               0 AS plan_schedule,
                               0 AS plan,
                               sum(
                                   IF
                                   (
                                       histories.transfer_movement_type = '9I3',
                                       histories.lot,
                                       IF
                                       ( histories.transfer_movement_type = '9I4', IF ( DAY ( histories.created_at ) < 1, 0, -( histories.lot )), 0 ))) AS picking,
                               0 AS plus,
                               0 AS minus,
                               0 AS stock,
                               0 AS plan_ori
                               FROM
                               ( SELECT materials.id, materials.material_number FROM kitto.materials WHERE materials.location IN ( 'SX51', 'CL51', 'FL51' ) AND category = 'key' ) AS materials
                               LEFT JOIN kitto.histories ON materials.id = histories.transfer_material_id
                               WHERE
                               date( histories.created_at ) = '" . $now . "'
                               AND histories.category IN ( 'transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment' )
                               GROUP BY
                               materials.material_number
                               ) AS pick UNION ALL
                               SELECT
                               inventories.material_number,
                               0 AS plan_schedule,
                               0 AS plan,
                               0 AS picking,
                               0 AS plus,
                               0 AS minus,
                               sum( inventories.lot ) AS stock,
                               0 AS plan_ori
                               FROM
                               kitto.inventories
                               LEFT JOIN kitto.materials ON materials.material_number = inventories.material_number
                               WHERE
                               materials.location IN ( 'SX51', 'CL51', 'FL51' )
                               AND materials.category = 'key'
                               GROUP BY
                               inventories.material_number UNION ALL
                               SELECT
                               material_number,
                               sum( plan_schedule ) AS plan_schedule,
                               sum( plan ) AS plan,
                               0 AS picking,
                               0 AS plus,
                               0 AS minus,
                               0 AS stock,
                               sum( plan_ori ) AS plan_ori
                               FROM
                               (
                               SELECT
                               materials.material_number,
                               0 AS plan_schedule,
                               -(
                               sum(
                               IF
                               (
                               histories.transfer_movement_type = '9I3',
                               histories.lot,
                               IF
                               ( histories.transfer_movement_type = '9I4', IF ( DAY ( histories.created_at ) < 1, 0, -( histories.lot )), 0 )))) AS plan,
                               0 AS plan_ori
                               FROM
                               ( SELECT materials.id, materials.material_number FROM kitto.materials WHERE materials.location IN ( 'SX51', 'CL51', 'FL51' ) AND category = 'key' ) AS materials
                               LEFT JOIN kitto.histories ON materials.id = histories.transfer_material_id
                               WHERE
                               date( histories.created_at ) >= '" . $first . "'
                               AND date( histories.created_at ) <= '" . $yesterday . "'
                               AND histories.category IN ( 'transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment' )
                               GROUP BY
                               materials.material_number UNION ALL
                               SELECT
                               assy_picking_schedules.material_number,
                               AVG( NULLIF( quantity, 0 ) ) AS plan_schedule,
                               sum( quantity ) AS plan,
                               sum( quantity ) AS plan_ori
                               FROM
                               assy_picking_schedules
                               LEFT JOIN materials ON materials.material_number = assy_picking_schedules.material_number
                               WHERE
                               due_date >= '" . $first . "'
                               AND due_date <= '" . $now . "'
                               AND assy_picking_schedules.remark IN ( 'SX51', 'CL51', 'FL51' )
                               GROUP BY
                               assy_picking_schedules.material_number
                               ) AS plan
                               GROUP BY
                               material_number UNION ALL
                               SELECT
                               materials.material_number,
                               0 AS plan_schedule,
                               0 AS plan,
                               0 AS picking,
                               sum(
                               IF
                               ( histories.transfer_movement_type = '9I3', histories.lot, 0 )) AS plus,
                               sum(
                               IF
                               ( histories.transfer_movement_type = '9I4', IF ( DAY ( histories.created_at ) < 1, 0, histories.lot ), 0 )) AS minus,
                               0 AS stock,
                               0 AS plan_ori
                               FROM
                               ( SELECT materials.id, materials.material_number FROM kitto.materials WHERE materials.location IN ( 'SX51', 'CL51', 'FL51' ) AND category = 'key' ) AS materials
                               LEFT JOIN kitto.histories ON materials.id = histories.transfer_material_id
                               WHERE
                               date( histories.created_at ) >= '" . $first . "'
                               AND date( histories.created_at ) <= '" . $now . "'
                               AND histories.category IN ( 'transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment' )
                               GROUP BY
                               materials.material_number
                               ) AS final
                               GROUP BY
                               material_number
                               ) AS final2
                               JOIN materials ON final2.material_number = materials.material_number
                               GROUP BY
                               materials.issue_storage_location,
                               materials.hpl,
                               materials.model,
                               materials.`key`,
                               materials.surface,
                               materials.issue_storage_location
                               ) AS final3
                               WHERE
                               plan_schedule > 0
                               ORDER BY
                               diff DESC");

        $response = array(
            'status' => true,
            'stockroom_keys' => $stockroom_keys,
            'first' => $first,
            'now' => date('D, d M Y', strtotime($now)),
        );
        return Response::json($response);
    }

    public function inputEfficiencyMonitoringMonthly(Request $request)
    {

        $newDate = date('Y-m-d', strtotime(str_replace('/', '-', $request->get('newDate'))));
        $newCost = $request->get('newCost');
        $newInput = $request->get('newInput');
        $newOutput = $request->get('newOutput');

        try {
            $efficiency_uploads = EfficiencyUpload::updateOrCreate(
                ['cost_center_name' => $newCost, 'total_date' => $newDate],
                ['total_input' => $newInput, 'total_output' => $newOutput, 'created_by' => Auth::id(), 'updated_at' => Carbon::now()]
            );
            $efficiency_uploads->save();

            $response = array(
                'status' => true,
                'message' => 'Data berhasil ditambahkan',
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

    public function fetchEfficiencyMonitoringMonthlyAdd(Request $request)
    {

        $month_target = '2021-02';

        $weekly_calendar = WeeklyCalendar::whereRaw("DATE_FORMAT(week_date, '%Y-%m') = '" . $month_target . "'")
            ->whereRaw("week_date <= '" . date('Y-m-d') . "'")
            ->select("fiscal_year", db::raw("date_format(week_date, '%Y-%m') as month_date"), db::raw("date_format(week_date, '%M') as month_name"))
            ->first();

        $weekly_months = db::select("SELECT
      wc.week_date,
      cc.cost_center_name
      FROM
      weekly_calendars AS wc
      CROSS JOIN ( SELECT DISTINCT cost_center_eff AS cost_center_name FROM cost_centers2 WHERE cost_center_eff IS NOT NULL ) AS cc
      WHERE
      DATE_FORMAT( wc.week_date, '%Y-%m') = '" . $weekly_calendar->month_date . "'
      AND cc.cost_center_name IS NOT NULL
      AND wc.week_date <= '" . date('Y-m-d') . "'
      ORDER BY
      field( cc.cost_center_name, 'FINAL', 'MIDDLE', 'SOLDERING', 'INITIAL', 'PN ASSY', 'RC ASSY', 'INJECTION', 'VENOVA', 'MOUTHPIECE', 'CASE', 'PN REED PLATE' ),
      wc.week_date ASC");

        $months = db::select("SELECT
      weekly_calendars.fiscal_year,
      efficiency_uploads.cost_center_name,
      weekly_calendars.week_date,
      efficiency_uploads.total_input,
      efficiency_uploads.total_output
      FROM
      efficiency_uploads
      LEFT JOIN weekly_calendars ON weekly_calendars.week_date = efficiency_uploads.total_date
      WHERE
      date_format(weekly_calendars.week_date, '%Y-%m') = '" . $weekly_calendar->month_date . "'
      AND weekly_calendars.week_date <= '2021-03-31'
      ORDER BY
      weekly_calendars.week_date ASC,
      efficiency_uploads.cost_center_name ASC");

        $result_months = array();

        foreach ($weekly_months as $weekly_month) {
            $week_date = $weekly_month->week_date;
            $cost_center_name = $weekly_month->cost_center_name;
            $total_input = 0;
            $total_output = 0;

            foreach ($months as $month) {
                if ($month->week_date == $week_date && $month->cost_center_name == $cost_center_name) {
                    $total_input = $month->total_input;
                    $total_output = $month->total_output;
                }
            }

            array_push($result_months,
                [
                    'week_date' => $week_date,
                    'cost_center_name' => $cost_center_name,
                    'total_input' => $total_input,
                    'total_output' => $total_output,
                ]);
        }

        $weekly_years = db::select("SELECT
      DISTINCT
      date_format( wc.week_date, '%Y-%m' ) AS month_date,
      cc.cost_center_name
      FROM
      weekly_calendars AS wc
      CROSS JOIN ( SELECT DISTINCT cost_center_eff AS cost_center_name FROM cost_centers2 WHERE cost_center_eff IS NOT NULL ) AS cc
      WHERE
      wc.fiscal_year = '" . $weekly_calendar->fiscal_year . "'
      AND wc.week_date <= '" . date('Y-m-d') . "'
      AND cc.cost_center_name IS NOT NULL
      ORDER BY
      field( cc.cost_center_name, 'FINAL', 'MIDDLE', 'SOLDERING', 'INITIAL', 'PN ASSY', 'RC ASSY', 'INJECTION', 'VENOVA', 'MOUTHPIECE', 'CASE', 'PN REED PLATE' ),
      wc.week_date ASC");

        $years = db::select("SELECT
      weekly_calendars.fiscal_year,
      efficiency_uploads.cost_center_name,
      date_format( weekly_calendars.week_date, '%Y-%m' ) AS month_date,
      sum( efficiency_uploads.total_input ) AS total_input,
      sum( efficiency_uploads.total_output ) AS total_output
      FROM
      efficiency_uploads
      LEFT JOIN weekly_calendars ON weekly_calendars.week_date = efficiency_uploads.total_date
      WHERE
      weekly_calendars.fiscal_year = '" . $weekly_calendar->fiscal_year . "'
      AND weekly_calendars.week_date <= '2021-03-31'
      GROUP BY
      weekly_calendars.fiscal_year,
      efficiency_uploads.cost_center_name,
      date_format( weekly_calendars.week_date, '%Y-%m' )
      ORDER BY
      weekly_calendars.week_date ASC,
      efficiency_uploads.cost_center_name ASC");

        $result_years = array();

        foreach ($weekly_years as $weekly_year) {
            $month_date = $weekly_year->month_date;
            $cost_center_name = $weekly_year->cost_center_name;
            $total_input = 0;
            $total_output = 0;

            foreach ($years as $year) {
                if ($year->month_date == $month_date && $year->cost_center_name == $cost_center_name) {
                    $total_input = $year->total_input;
                    $total_output = $year->total_output;
                }
            }

            array_push($result_years,
                [
                    'month_date' => $month_date,
                    'cost_center_name' => $cost_center_name,
                    'total_input' => $total_input,
                    'total_output' => $total_output,
                ]);
        }

        $response = array(
            'status' => true,
            'months' => $result_months,
            'years' => $result_years,
            'period' => $weekly_calendar->fiscal_year . " " . $weekly_calendar->month_name,
        );
        return Response::json($response);

    }

    public function fetchEfficiencyMonitoringMonthly(Request $request)
    {

        $month_target = date('Y-m');

        if (strlen($request->get('period')) > 0) {
            $month_target = $request->get('period');
        }

        $weekly_calendar = WeeklyCalendar::whereRaw("DATE_FORMAT(week_date, '%Y-%m') = '" . $month_target . "'")
            ->whereRaw("week_date <= '" . date('Y-m-d') . "'")
            ->select("fiscal_year", db::raw("date_format(week_date, '%Y-%m') as month_date"), db::raw("date_format(week_date, '%M') as month_name"))
            ->first();

        $fiscal = WeeklyCalendar::where('fiscal_year', '=', $weekly_calendar->fiscal_year)->select(db::raw('min(week_date) as first_date'))->first();

        if ($weekly_calendar->month_date < '2021-04') {
            $weekly_months = db::select("SELECT
           wc.week_date,
           cc.cost_center_name
           FROM
           weekly_calendars AS wc
           CROSS JOIN ( SELECT DISTINCT cost_center_eff AS cost_center_name FROM cost_centers2 WHERE cost_center_eff IS NOT NULL ) AS cc
           WHERE
           DATE_FORMAT( wc.week_date, '%Y-%m') = '" . $weekly_calendar->month_date . "'
           AND cc.cost_center_name IS NOT NULL
           AND wc.week_date <= '" . date('Y-m-d') . "'
           ORDER BY
           field( cc.cost_center_name, 'FINAL', 'MIDDLE', 'SOLDERING', 'INITIAL', 'PN ASSY', 'RC ASSY', 'INJECTION', 'VENOVA', 'MOUTHPIECE', 'CASE', 'PN REED PLATE' ),
           wc.week_date ASC");
        } else {
            $weekly_months = db::select("SELECT
           wc.week_date,
           cc.cost_center_name
           FROM
           weekly_calendars AS wc
           CROSS JOIN ( SELECT DISTINCT cost_center_eff_2 AS cost_center_name FROM cost_centers2 WHERE cost_center_eff IS NOT NULL ) AS cc
           WHERE
           DATE_FORMAT( wc.week_date, '%Y-%m') = '" . $weekly_calendar->month_date . "'
           AND cc.cost_center_name IS NOT NULL
           AND wc.week_date <= '" . date('Y-m-d') . "'
           ORDER BY
           field( cc.cost_center_name, 'FINAL', 'MIDDLE', 'SOLDERING', 'BODY PARTS PROCESS', 'KEY PARTS PROCESS', 'CASE', 'EDUCATIONAL INSTRUMENT' ),
           wc.week_date ASC");
        }

        $months = db::select("SELECT
      weekly_calendars.fiscal_year,
      efficiency_uploads.cost_center_name,
      weekly_calendars.week_date,
      efficiency_uploads.total_input,
      efficiency_uploads.total_output
      FROM
      efficiency_uploads
      LEFT JOIN weekly_calendars ON weekly_calendars.week_date = efficiency_uploads.total_date
      WHERE
      date_format(weekly_calendars.week_date, '%Y-%m') = '" . $weekly_calendar->month_date . "'
      AND weekly_calendars.week_date <= '" . date('Y-m-d') . "'
      ORDER BY
      weekly_calendars.week_date ASC,
      efficiency_uploads.cost_center_name ASC");

        $result_months = array();

        foreach ($weekly_months as $weekly_month) {
            $week_date = $weekly_month->week_date;
            $cost_center_name = $weekly_month->cost_center_name;
            $total_input = 0;
            $total_output = 0;

            foreach ($months as $month) {
                if ($month->week_date == $week_date && $month->cost_center_name == $cost_center_name) {
                    $total_input = $month->total_input;
                    $total_output = $month->total_output;
                }
            }

            array_push($result_months,
                [
                    'week_date' => $week_date,
                    'cost_center_name' => $cost_center_name,
                    'total_input' => $total_input,
                    'total_output' => $total_output,
                ]);
        }

        if ($weekly_calendar->month_date < '2021-04') {
            $weekly_years = db::select("SELECT
           DISTINCT
           date_format( wc.week_date, '%Y-%m' ) AS month_date,
           cc.cost_center_name
           FROM
           weekly_calendars AS wc
           CROSS JOIN ( SELECT DISTINCT cost_center_eff AS cost_center_name FROM cost_centers2 WHERE cost_center_eff IS NOT NULL ) AS cc
           WHERE
           wc.fiscal_year = '" . $weekly_calendar->fiscal_year . "'
           AND wc.week_date <= '" . date('Y-m-d') . "'
           AND cc.cost_center_name IS NOT NULL
           ORDER BY
           field( cc.cost_center_name, 'FINAL', 'MIDDLE', 'SOLDERING', 'INITIAL', 'PN ASSY', 'RC ASSY', 'INJECTION', 'VENOVA', 'MOUTHPIECE', 'CASE', 'PN REED PLATE' ),
           wc.week_date ASC");
        } else {
            $weekly_years = db::select("SELECT
           DISTINCT
           date_format( wc.week_date, '%Y-%m' ) AS month_date,
           cc.cost_center_name
           FROM
           weekly_calendars AS wc
           CROSS JOIN ( SELECT DISTINCT cost_center_eff_2 AS cost_center_name FROM cost_centers2 WHERE cost_center_eff IS NOT NULL ) AS cc
           WHERE
           DATE_FORMAT(wc.week_date, '%Y-%m') = '2021-02'
           or ( wc.week_date >= '" . $fiscal->first_date . "'
               AND wc.week_date <= '" . date('Y-m-d') . "')
               AND cc.cost_center_name IS NOT NULL
               ORDER BY
               field( cc.cost_center_name, 'FINAL', 'MIDDLE', 'SOLDERING', 'BODY PARTS PROCESS', 'KEY PARTS PROCESS', 'CASE', 'EDUCATIONAL INSTRUMENT' ),
               wc.week_date ASC");
        }

        $years = db::select("SELECT
          weekly_calendars.fiscal_year,
          efficiency_uploads.cost_center_name,
          date_format( weekly_calendars.week_date, '%Y-%m' ) AS month_date,
          sum( efficiency_uploads.total_input ) AS total_input,
          sum( efficiency_uploads.total_output ) AS total_output
          FROM
          efficiency_uploads
          LEFT JOIN weekly_calendars ON weekly_calendars.week_date = efficiency_uploads.total_date
          WHERE
          DATE_FORMAT(weekly_calendars.week_date, '%Y-%m') = '2021-02'
          or ( weekly_calendars.week_date >= '" . $fiscal->first_date . "'
              AND weekly_calendars.week_date <= '" . date('Y-m-d') . "')
              GROUP BY
              weekly_calendars.fiscal_year,
              efficiency_uploads.cost_center_name,
              date_format( weekly_calendars.week_date, '%Y-%m' )
              ORDER BY
              weekly_calendars.week_date ASC,
              efficiency_uploads.cost_center_name ASC");

        $result_years = array();

        foreach ($weekly_years as $weekly_year) {
            $month_date = $weekly_year->month_date;
            $cost_center_name = $weekly_year->cost_center_name;
            $total_input = 0;
            $total_output = 0;

            foreach ($years as $year) {
                if ($year->month_date == $month_date && $year->cost_center_name == $cost_center_name) {
                    $total_input = $year->total_input;
                    $total_output = $year->total_output;
                }
            }

            array_push($result_years,
                [
                    'month_date' => $month_date,
                    'cost_center_name' => $cost_center_name,
                    'total_input' => $total_input,
                    'total_output' => $total_output,
                ]);
        }

        $response = array(
            'status' => true,
            'months' => $result_months,
            'years' => $result_years,
            'period' => $weekly_calendar->fiscal_year . " " . $weekly_calendar->month_name,
        );
        return Response::json($response);
    }

    public function fetchEfficiencyMonitoring(Request $request)
    {

        $first = date('Y-m-01');
        $last = date('Y-m-t');

        if (strlen($request->get('period')) > 0) {
            $first = date('Y-m-01', strtotime($request->get('period')));
            $last = date('Y-m-t', strtotime($request->get('period')));
        }

        $employee_histories = db::select("SELECT
          date_format( date_add( e.period, INTERVAL 5 DAY ), '%Y-%m' ) AS completion_month,
          e.Emp_no AS employee_id,
          e.cost_center_code,
          c.cost_center_eff AS cost_center_name
          FROM
          employee_histories AS e
          LEFT JOIN cost_centers2 AS c ON c.cost_center = e.cost_center_code
          WHERE
          c.cost_center_eff IS NOT NULL
          AND date_format( e.period, '%Y-%m-%d' ) >= '2020-10-01'
          AND date_format( e.period, '%Y-%m-%d' ) <= '2020-10-31'");

        $weekly_calendars = db::select("SELECT
          wc.fiscal_year,
          date_format( wc.week_date, '%Y-%m' ) AS completion_month,
          wc.week_name,
          wc.week_date,
          cc.cost_center_name
          FROM
          weekly_calendars AS wc
          CROSS JOIN ( SELECT DISTINCT cost_center_eff AS cost_center_name FROM cost_centers2 WHERE cost_center_eff IS NOT NULL ) AS cc
          WHERE
          wc.week_date >= '2020-11-01'
          AND wc.week_date <= '2020-11-30'
          AND cc.cost_center_name IS NOT NULL
			-- AND cc.cost_center_name IN ( 'FINAL', 'MIDDLE', 'SOLDERING', 'INITIAL', 'RC ASSY', 'PN ASSY' )
			ORDER BY
			wc.week_date ASC,
			cc.cost_center_name ASC");

        $completion_times = db::select("SELECT
          final.completion_month,
          final.completion_week,
          final.completion_date,
          final.work_center_name,
          sum( final.total_time ) AS total_time
          FROM
          (
              SELECT
              DATE_FORMAT( c.posting_date, '%Y-%m' ) AS completion_month,
              DATE_FORMAT( c.posting_date, '%u' ) AS completion_week,
              c.posting_date AS completion_date,
              c.material_number,
              c.storage_location,
              w.work_center_name,
              IF
              (
                  c.movement_type = '101',
                  c.quantity,
                  -(
                      c.quantity
                      )) * s.std_time AS total_time
              FROM
              sap_completions AS c
              LEFT JOIN sap_standard_times AS s ON s.material_number = c.material_number
              LEFT JOIN work_centers AS w ON w.work_center = s.work_center
              WHERE
              c.movement_type IN ( '101', '102' )
              AND w.work_center_name IS NOT NULL
              AND c.posting_date >= '2020-11-01'
              AND c.posting_date <= '2020-11-30'
              ) AS final
          GROUP BY
          final.completion_month,
          final.completion_week,
          final.completion_date,
          final.work_center_name
          ORDER BY
          final.completion_date ASC,
          final.work_center_name ASC");

        // $completion_times = db::select("SELECT
        //     DATE_FORMAT( c.completion_date, '%Y-%m' ) AS completion_month,
        //     DATE_FORMAT( c.completion_date, '%u' ) AS completion_week,
        //     c.completion_date,
        //     c.work_center_name,
        //     sum( c.total_time ) AS total_time
        //     FROM
        //     (
        //     SELECT
        //     date( kh.created_at ) AS completion_date,
        //     km.material_number,
        //     km.location,
        //     kh.lot,
        //     km.stdval,
        //     kh.lot * km.stdval AS total_time,
        //     yw.work_center_name
        //     FROM
        //     kitto.histories AS kh
        //     LEFT JOIN kitto.materials AS km ON km.id = kh.completion_material_id
        //     LEFT JOIN ympimis.work_centers AS yw ON yw.work_center = km.work_center
        //     WHERE
        //     date( kh.created_at ) >= '2020-10-01'
        //     AND date( kh.created_at ) <= '2020-10-05'
        //     AND kh.category IN ( 'completion', 'completion_cancel', 'completion_return', 'completion_adjustment' ) UNION ALL
        //     SELECT
        //     date( l.transaction_date ) AS completion_date,
        //     l.material_number,
        //     l.issue_storage_location AS location,
        //     IF
        //     (
        //     l.mvt = '101',
        //     l.qty,
        //     -(
        //     l.qty
        //     )) AS lot,
        //     s.std_time AS stdval,
        //     IF
        //     (
        //     l.mvt = '101',
        //     l.qty,
        //     -(
        //     l.qty
        //     )) * s.std_time AS total_time,
        //     w.work_center_name
        //     FROM
        //     log_transactions AS l
        //     LEFT JOIN sap_standard_times AS s ON s.material_number = l.material_number
        //     LEFT JOIN work_centers AS w ON w.work_center = s.work_center
        //     WHERE
        //     date( l.transaction_date ) >= '2020-10-01'
        //     AND date( l.transaction_date ) <= '2020-10-05'
        //     AND l.mvt IN ( '101', '102' ) UNION ALL
        //     SELECT
        //     date( t.created_at ) AS completion_date,
        //     t.material_number,
        //     t.issue_location AS location,
        //     IF
        //     (
        //     t.movement_type = '101',
        //     t.quantity,
        //     -(
        //     t.quantity
        //     )) AS lot,
        //     s.std_time AS stdval,
        //     IF
        //     (
        //     t.movement_type = '101',
        //     t.quantity,
        //     -(
        //     t.quantity
        //     ))* s.std_time AS total_time,
        //     w.work_center_name
        //     FROM
        //     transaction_completions t
        //     LEFT JOIN sap_standard_times AS s ON s.material_number = t.material_number
        //     LEFT JOIN work_centers AS w ON w.work_center = s.work_center
        //     WHERE
        //     date( t.created_at ) >= '2020-10-01'
        //     AND date( t.created_at ) <= '2020-10-05'
        //     ) AS c
        //     GROUP BY
        //     completion_month,
        //     completion_week,
        //     c.completion_date,
        //     c.work_center_name
        //     ORDER BY
        //     c.completion_date ASC,
        //     c.work_center_name ASC");

        $man_times = db::connection('sunfish')->select("SELECT
          format ( a.shiftstarttime, 'yyyy-MM' ) AS completion_month,
          DATEPART( wk, a.shiftstarttime ) AS completion_week,
          format ( a.shiftstarttime, 'yyyy-MM-dd' ) AS completion_date,
          a.emp_no AS employee_id,
          IIF (
              a.shiftdaily_code LIKE '%OFF%',
              0,
              IIF (
                  a.shiftdaily_code LIKE '%Shift_1%'
                  AND a.Attend_Code LIKE '%PRS%',
                  480,
                  IIF (
                      a.shiftdaily_code LIKE '%Shift_2%'
                      AND a.Attend_Code LIKE '%PRS%',
                      450,
                      IIF ( a.shiftdaily_code LIKE '%Shift_1%' AND a.Attend_Code LIKE '%PRS%', 420, 0 )
                      )
                  )
              ) AS work_time,
          COALESCE ( b.break_time, 0 ) AS break_time,
          COALESCE ( a.total_ot, 0 ) AS ot_time,
          IIF (
              a.shiftdaily_code LIKE '%OFF%',
              0,
              IIF (
                  a.shiftdaily_code LIKE '%Shift_1%'
                  AND a.Attend_Code LIKE '%PRS%',
                  480,
                  IIF (
                      a.shiftdaily_code LIKE '%Shift_2%'
                      AND a.Attend_Code LIKE '%PRS%',
                      450,
                      IIF ( a.shiftdaily_code LIKE '%Shift_1%' AND a.Attend_Code LIKE '%PRS%', 420, 0 )
                      )
                  )
              ) + COALESCE ( a.total_ot, 0 ) AS total_time
          FROM
          VIEW_YMPI_Emp_Attendance AS a
          LEFT JOIN ( SELECT shiftdailycode, SUM ( datediff( MINUTE, breakovt_endtime, breakovt_starttime ) ) AS break_time FROM OVT_BREAK_YMPI GROUP BY shiftdailycode ) AS b ON b.shiftdailycode = a.shiftdaily_code
          WHERE
          format ( a.shiftstarttime, 'yyyy-MM-dd' ) >= '2020-11-01'
          AND format ( a.shiftstarttime, 'yyyy-MM-dd' ) <= '2020-11-30'");

        $man_times2 = array();

        foreach ($man_times as $man_time) {
            $cost_center_name = "";
            foreach ($employee_histories as $employee_history) {
                if ($man_time->completion_month == $employee_history->completion_month && $man_time->employee_id == $employee_history->employee_id) {
                    $cost_center_name = $employee_history->cost_center_name;
                }
            }
            if ($cost_center_name != "") {
                array_push($man_times2,
                    [
                        'completion_month' => $man_time->completion_month,
                        'completion_week' => $man_time->completion_week,
                        'completion_date' => $man_time->completion_date,
                        'cost_center_name' => $cost_center_name,
                        'total_time' => $man_time->total_time,
                    ]);
            }
        }

        $groups = array();
        foreach ($man_times2 as $data) {
            $key = $data['completion_month'] . '_' . $data['completion_week'] . '_' . $data['completion_date'] . '_' . $data['cost_center_name'];
            if (!array_key_exists($key, $groups)) {
                $groups[$key] = array(
                    'completion_month' => $data['completion_month'],
                    'completion_week' => $data['completion_week'],
                    'completion_date' => $data['completion_date'],
                    'cost_center_name' => $data['cost_center_name'],
                    'total_time' => (float) $data['total_time'],
                );
            } else {
                $groups[$key]['total_time'] = (float) $groups[$key]['total_time'] + (float) $data['total_time'];
            }
        }

        $results = array();

        foreach ($weekly_calendars as $weekly_calendar) {

            $fiscal = $weekly_calendar->fiscal_year;
            $month = $weekly_calendar->completion_month;
            $week_name = $weekly_calendar->week_name;
            $week_date = $weekly_calendar->week_date;
            $cost_center_name = $weekly_calendar->cost_center_name;
            $output_total = 0;

            foreach ($completion_times as $output) {
                if ($output->completion_date == $week_date && $output->work_center_name == $cost_center_name) {
                    $output_total = $output->total_time;
                }
            }

            array_push($results,
                [
                    'fiscal' => $fiscal,
                    'month_name' => $month,
                    'week_name' => $week_name,
                    'week_date' => $week_date,
                    'cost_center_name' => $cost_center_name,
                    'total_output' => $output_total,
                ]);

        }

        $finals = array();

        foreach ($results as $result) {

            $fiscal = $result['fiscal'];
            $month = $result['month_name'];
            $week_name = $result['week_name'];
            $week_date = $result['week_date'];
            $cost_center_name = $result['cost_center_name'];
            $output_total = $result['total_output'];
            $input_total = 0;

            foreach ($groups as $input) {
                if ($input['completion_date'] == $week_date && $input['cost_center_name'] == $cost_center_name && $output_total > 0) {
                    $input_total = $input['total_time'];
                }
            }

            array_push($finals,
                [
                    'fiscal' => $fiscal,
                    'month_name' => $month,
                    'week_name' => $week_name,
                    'week_date' => $week_date,
                    'cost_center_name' => $cost_center_name,
                    'total_output' => $output_total,
                    'total_input' => $input_total,
                ]);

        }

        // foreach ($completion_times as $output) {
        //     foreach ($groups as $input) {
        //         if($output->completion_date == $input['completion_date'] && $output->work_center_name == $input['cost_center_name']){
        //             array_push($results,
        //                 [
        //                     'completion_month' => $output->completion_month,
        //                     'completion_week' => $output->completion_week,
        //                     'completion_date' => $output->completion_date,
        //                     'cost_center_name' => $output->work_center_name,
        //                     'total_output' => $output->total_time,
        //                     'total_input' => $input['total_time']
        //                 ]);
        //         }
        //     }
        // }

        $response = array(
            'status' => true,
            'datas' => $finals,
            'first' => date('d', strtotime($first)),
            'last' => date('d F Y', strtotime($last)),
        );
        return Response::json($response);

    }

    public function index_dp_production_result()
    {
        $title = "Daily Production Result";
        $title_jp = "日常生産実績";

        $origin_groups = OriginGroup::orderBy('origin_group_name', 'asc')->get();
        return view('displays.production_result', array(
            'origin_groups' => $origin_groups,
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Display Production Result')->with('head', 'Display');
    }

    public function indexAllStock()
    {
        return view('displays.shippings.all_stock')->with('page', 'All Stock')->with('head', 'All Stock');
    }

    public function indexEffScrap()
    {
        $title = 'Scrap Monitoring';
        $title_jp = 'スクラップの監視';

        return view('displays.eff_scrap', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Display Scrap Monitoring')->with('head', 'Display');
    }

    public function fetchEffScrap(Request $request)
    {

        $first = date('Y-m-01');
        $last = date('Y-m-t');

        if (strlen($request->get('period')) > 0) {
            $first = date('Y-m-01', strtotime($request->get('period')));
            $last = date('Y-m-t', strtotime($request->get('period')));
        }

        $targets = db::select("SELECT
      *
          FROM
          scrap_targets
          WHERE
          due_date >= '" . $first . "'
          AND due_date <= '" . $last . "'");

        $actuals = db::select("SELECT
          w.week_date AS posting_date,
          scrap.movement_type,
          scrap.material_number,
          scrap.material_description,
          scrap.quantity,
          scrap.std_price,
          COALESCE ( scrap.amount, 0 ) AS amount,
          scrap.storage_location,
          IF
          ( scrap.receive_location IS NULL OR scrap.receive_location = '', 'no_scrap', scrap.receive_location ) AS receive_location,
          SPLIT_STRING ( scrap.reference, '/', 2 ) AS reason,
          scrap_reasons.reason_name
          FROM
          ( SELECT week_date FROM weekly_calendars WHERE week_date >= '" . $first . "' AND week_date <= '" . $last . "' ) AS w
              LEFT JOIN (
              SELECT
              s.posting_date,
              s.movement_type,
              s.material_number,
              m.material_description,
              s.quantity,
              m.standard_price / 1000 AS std_price,
              IF (
                s.movement_type IN ( '9S2', 'XS04' ),
                - 1 * s.quantity *(
                    m.standard_price / 1000
                ),
                IF
                ( s.movement_type IN ( '9S1', 'XS03' ), s.quantity *( m.standard_price / 1000 ), 0 )
                ) AS amount,
              IF
              ( s.receive_location = '' OR s.receive_location IS NULL, m.storage_location, s.storage_location ) AS storage_location,
              IF
              ( s.receive_location = '' OR s.receive_location IS NULL, s.storage_location, s.receive_location ) AS receive_location,
              s.reference
              FROM
              sap_transactions AS s
              LEFT JOIN material_plant_data_lists AS m ON m.material_number = s.material_number
              WHERE
              (
              s.receive_location IN ( 'MSCR', 'WSCR' )
              OR s.storage_location IN ( 'MSCR', 'WSCR' ))
              AND s.posting_date >= '" . $first . "'
              AND s.posting_date <= '" . $last . "'
              AND s.reference NOT LIKE '%TRI%'
              AND s.reference NOT LIKE '%WAST%'
              ) AS scrap ON scrap.posting_date = w.week_date
              LEFT JOIN scrap_reasons ON scrap_reasons.reason = SPLIT_STRING ( scrap.reference, '/', 2 ) AND scrap_reasons.remark = scrap.receive_location
              ORDER BY
              posting_date ASC");

        $categories = db::select("SELECT
              sum( amount ) AS total_amount,
              receive_location,
              reason
              FROM
              (
                  SELECT
                 IF (
                    s.movement_type IN ( '9S2', 'XS04' ),
                    - 1 * s.quantity *(
                        m.standard_price / 1000
                    ),
                    IF
                    ( s.movement_type IN ( '9S1', 'XS03' ), s.quantity *( m.standard_price / 1000 ), 0 )
                    ) AS amount,
                  IF
                  ( s.receive_location = '' OR s.receive_location IS NULL, s.storage_location, s.receive_location ) AS receive_location,
                  SPLIT_STRING ( s.reference, '/', 2 ) AS reason
                  FROM
                  sap_transactions s
                  LEFT JOIN material_plant_data_lists m ON m.material_number = s.material_number
                  WHERE
                  (
                      s.receive_location IN ( 'MSCR', 'WSCR' )
                      OR s.storage_location IN ( 'MSCR', 'WSCR' ))
                  AND s.posting_date >= '" . $first . "'
                  AND s.posting_date <= '" . $last . "'
                  AND s.reference NOT LIKE '%TRI%'
                  AND s.reference NOT LIKE '%WAST%'
                  ) AS category
                  GROUP BY
                  receive_location,
                  reason
                  ORDER BY
                  receive_location DESC,
                  total_amount DESC");

        $actual_mscr = array();
        $actual_wscr = array();

        foreach ($actuals as $actual) {
            if ($actual->receive_location == 'MSCR' || $actual->receive_location == 'no_scrap') {
                array_push($actual_mscr, [
                    'posting_date' => $actual->posting_date,
                    'movement_type' => $actual->movement_type,
                    'material_number' => $actual->material_number,
                    'material_description' => $actual->material_description,
                    'quantity' => $actual->quantity,
                    'std_price' => $actual->std_price,
                    'amount' => $actual->amount,
                    'storage_location' => $actual->storage_location,
                    'reason' => $actual->reason,
                    'reason_name' => $actual->reason_name,
                    'receive_location' => 'MSCR',
                ]);
            }
            if ($actual->receive_location == 'WSCR' || $actual->receive_location == 'no_scrap') {
                array_push($actual_wscr, [
                    'posting_date' => $actual->posting_date,
                    'movement_type' => $actual->movement_type,
                    'material_number' => $actual->material_number,
                    'material_description' => $actual->material_description,
                    'quantity' => $actual->quantity,
                    'std_price' => $actual->std_price,
                    'amount' => $actual->amount,
                    'storage_location' => $actual->storage_location,
                    'reason' => $actual->reason,
                    'reason_name' => $actual->reason_name,
                    'receive_location' => 'WSCR',
                ]);
            }
        }

        $response = array(
            'status' => true,
            'targets' => $targets,
            'actual_mscr' => $actual_mscr,
            'actual_wscr' => $actual_wscr,
            'categories' => $categories,
            'first' => date('d', strtotime($first)),
            'last' => date('d F Y', strtotime($last)),
        );
        return Response::json($response);
    }

    public function indexBudgetActualSales()
    {

        $employee_id = Auth::user()->username;

        if (in_array(strtoupper($employee_id), $this->exp)) {
            $title = 'Budget VS Actual Sales';
            $title_jp = '売上予算対売り上げ実績';

            return view('displays.shippings.budget_vs_actual_sales', array(
                'title' => $title,
                'title_jp' => $title_jp,
            ))->with('page', 'Budget VS Actual Sales')->with('head', 'Display');
        } else {
            return view('404');
        }
    }

    public function indexShippingAmount()
    {

        $employee_id = Auth::user()->username;

        if (in_array(strtoupper($employee_id), $this->exp)) {
            $title = 'Daily Shipping  (Amount)';
            $title_jp = '日次出荷 「金額」';

            return view('displays.shippings.shipping_amount', array(
                'title' => $title,
                'title_jp' => $title_jp,
            ))->with('page', 'Budget VS Actual Sales')->with('head', 'Display');
        } else {
            return view('404');
        }
    }

    public function indexShippingProdAmount()
    {

        $employee_id = Auth::user()->username;

        if (in_array(strtoupper($employee_id), $this->exp)) {
            $title = 'Daily Shipping & Production (Amount)';
            $title_jp = '日次出荷と生産「金額」';

            return view('displays.shippings.shipping_production_amount', array(
                'title' => $title,
                'title_jp' => $title_jp,
            ))->with('page', 'Budget VS Actual Sales')->with('head', 'Display');
        } else {
            return view('404');
        }
    }

    public function indexShipmentReport()
    {
        $title = 'FG Weekly Shipment';
        $title_jp = 'FG週次出荷';

        return view('displays.shippings.shipment_report', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Display Weekly Shipment')->with('head', 'Display');
    }

    public function indexStuffingProgress()
    {
        $title = 'Container Stuffing Progress';
        $title_jp = 'コンテナ荷積み進捗';

        return view('displays.shippings.stuffing_progress', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Display Stuffing Progress')->with('head', 'Display');
    }

    public function indexStuffingTime()
    {
        $title = 'Container Stuffing Time';
        $title_jp = 'コンテナ荷積み時間';

        return view('displays.shippings.stuffing_time', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Display Stuffing Time')->with('head', 'Display');
    }

    public function indexStuffingMonitoring()
    {
        $title = 'Container Stuffing Monitoring';
        $title_jp = 'コンテナ荷積み監視';

        return view('displays.shippings.stuffing_monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Display Stuffing Monitoring')->with('head', 'Display');
    }

    public function indexShipmentProgress()
    {
        return view('displays.shipment_progress')->with('page', 'Display Shipment Result')->with('head', 'Display');
    }

    public function index_dp_stockroom_stock()
    {
        return view('displays.stockroom_stock')->with('page', 'Display Stockroom Stock')->with('head', 'Display');
    }

    public function index_dp_fg_accuracy()
    {
        $title = 'Finished Goods Accuracy';
        $title_jp = 'FG週次出荷';

        return view('displays.fg_accuracy', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Display FG Accuracy')->with('head', 'Display');
    }

    public function indexSalesByDestination()
    {
        $title = 'Sales By Destination';
        $title_jp = '';

        return view('displays.shippings.sales_by_destination', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ));
    }

    public function fetchSalesByDestination(Request $request)
    {
        $st_month = date('Y-m-01', strtotime($request->get('date')));
        $st_month = '2022-11-01';

        $plans = db::select("SELECT
	st_month,
	hpl,
	material_number,
	destination_shortname,
	sales_category,
	sum( plan_quantity ) AS plan_quantity,
	max( plan_price ) AS plan_price,
	sum( plan_amount ) AS plan_amount,
	sum( actual_quantity ) AS actual_quantity,
	max( actual_price ) AS actual_price,
	sum( actual_amount ) AS actual_amount
FROM
	(
	SELECT
		plans.st_month,
		plans.hpl,
		plans.material_number,
		plans.destination_shortname,
		plans.sales_category,
		plans.quantity AS plan_quantity,
		prices.price AS plan_price,
		plans.quantity * prices.price AS plan_amount,
		0 AS actual_quantity,
		0 AS actual_price,
		0 AS actual_amount
	FROM
		(
		SELECT
			ss.st_month,
		IF
			(
				m.category = 'FG',
				ss.hpl,
			IF
			( m.category = 'KD', 'SP', 'UNDEFINED' )) AS hpl,
			ss.material_number,
			ss.quantity,
			d.destination_shortname,
		IF
			( d.destination_shortname = 'YMID', 'YMID', 'ALL' ) AS sales_category
		FROM
			shipment_schedules AS ss
			LEFT JOIN destinations AS d ON d.destination_code = ss.destination_code
			LEFT JOIN materials AS m ON m.material_number = ss.material_number
		WHERE
			ss.st_month = '2022-11-01' UNION ALL
		SELECT
			DATE_FORMAT( eod.request_date, '%Y-%m-01' ) AS st_month,
			'EO' AS hpl,
			eod.material_number,
			eod.quantity,
			eo.destination_shortname,
		IF
			( eo.destination_shortname = 'YMID', 'YMID', 'ALL' ) AS sales_category
		FROM
			extra_order_details AS eod
			LEFT JOIN extra_orders AS eo ON eod.eo_number = eo.eo_number
		WHERE
			DATE_FORMAT( eod.request_date, '%Y-%m-01' ) = '2022-11-01'
		) AS plans
		LEFT JOIN (
		SELECT
			material_number,
			sales_category,
		IF
			( sales_category = 'YMID', price *( SELECT idr_to_usd FROM sales_exchange_rates WHERE period = '2022-11-01' ), price ) AS price
		FROM
			sales_prices_bk
		WHERE
			MONTH = '2022-11-01'
			AND deleted_at IS NULL
		) AS prices ON plans.material_number = prices.material_number
		AND plans.sales_category = prices.sales_category UNION ALL
	SELECT
		date_format( bl_date, '%Y-%m-01' ) AS st_month,
	IF
		(
			category = 'KD',
			'SP',
		IF
		( category = 'EXTRA ORDER', 'EO', category )) AS hpl,
		material_number,
		destination_shortname,
		price_category AS sales_category,
		0 AS plan_quantity,
		0 AS plan_price,
		0 AS plan_amount,
		quantity AS actual_quantity,
		price AS actual_price,
		quantity * price AS actual_amount
	FROM
		sales_resumes
	) AS final
GROUP BY
	st_month,
	hpl,
	material_number,
	destination_shortname,
	sales_category");

        $plans = array();
        $actuals = array();
        $resumes = array();

        $response = array(
            'status' => true,
            'plans' => $plans,
            'actuals' => $actuals,
        );
        return Response::json($response);
    }

    public function fetchBudgetActualSales(Request $request)
    {

        $resume_forecast = array();
        $resume_budget = array();
        $resume_sales = array();

        $fy = $request->get('fy');
        $now_calendar = WeeklyCalendar::where('week_date', date('Y-m-d'))->first();
        if (strlen($fy) <= 0) {
            $fy = $now_calendar->fiscal_year;
        }

        $start = WeeklyCalendar::where('fiscal_year', $fy)->orderBy('week_date', 'ASC')->first();
        $end = WeeklyCalendar::where('fiscal_year', $fy)->orderBy('week_date', 'DESC')->first();

        $months = db::select("SELECT DISTINCT DATE_FORMAT(week_date,'%Y-%m') AS `month`, DATE_FORMAT(week_date,'%b-%Y') AS text  FROM weekly_calendars
              WHERE fiscal_year = '" . $fy . "'
              ORDER BY `month` ASC");

        // FORECAST ALL
        $resume_forecast = ProductionForecastResume::where('fiscal_year', $fy)
            ->select(db::raw('DATE_FORMAT(`month`,"%Y-%m") AS month'), 'amount')
            ->get();

        // BUDGET ALL
        $resume_budget = SalesBudgetResume::where('fiscal_year', $fy)
            ->select(db::raw('DATE_FORMAT(`month`,"%Y-%m") AS month'), 'amount')
            ->get();

        // SALES ALL
        $resume_sales = db::table('sales_resumes')
            ->where('bl_date', '>=', $start->week_date)
            ->where('bl_date', '<=', $end->week_date)
            ->get();

        $response = array(
            'status' => true,
            'fy' => $fy,
            'months' => $months,
            'resume_forecast' => $resume_forecast,
            'resume_budget' => $resume_budget,
            'resume_sales' => $resume_sales,
        );
        return Response::json($response);
    }

    public function fetchShippingAmount(Request $request)
    {

        $month = $request->get('month');
        if (strlen($month) <= 0) {
            $month = date('Y-m');
        }

        $calendar = db::select("SELECT fiscal_year, week_date, DATE_FORMAT(week_date,'%e %b') AS date_name FROM weekly_calendars
              WHERE DATE_FORMAT(week_date,'%Y-%m') = '" . $month . "'
              ORDER BY week_date ASC");
        $materials = db::select("SELECT * FROM materials WHERE category = 'FG'");
        $prices = db::select("SELECT * FROM sales_prices WHERE fiscal_year = '" . $calendar[0]->fiscal_year . "'");

        //FORECAST
        $forecast = ProductionForecastResume::where(db::raw('DATE_FORMAT(`month`,"%Y-%m")'), '=', $month)->first();

        //BUDGET
        $budget = SalesBudgetResume::where(db::raw('DATE_FORMAT(`month`,"%Y-%m")'), '=', $month)->first();

        //SALES
        $sales = db::table('sales_resumes')->where(db::raw('DATE_FORMAT(bl_date,"%Y-%m")'), '=', $month)->get();

        $response = array(
            'status' => true,
            'now' => date('Y-m-d'),
            'last_update' => date('Y-m-d (H:i:s)'),
            'month_name' => date('F Y', strtotime($month . '-01')),
            'calendar' => $calendar,
            'budget' => $budget,
            'forecast' => $forecast,
            'sales' => $sales,
        );
        return Response::json($response);

    }

    public function fetchShippingProdAmount(Request $request)
    {

        $month = $request->get('month');
        if (strlen($month) <= 0) {
            $month = date('Y-m');
        }

        $calendar = db::select("SELECT fiscal_year, week_date, week_name, DATE_FORMAT(week_date,'%e %b') AS date_name, remark FROM weekly_calendars
              WHERE DATE_FORMAT(week_date,'%Y-%m') = '" . $month . "'
              ORDER BY week_date ASC");

        $resume_calendar = db::select("SELECT week_name, MIN(week_date) AS `start`, MAX(week_date) AS `end`, SUM(IF(remark <> 'H', 1, 0)) AS weekday, SUM(IF(remark = 'H', 1, 0)) AS holiday FROM weekly_calendars
              WHERE DATE_FORMAT(week_date,'%Y-%m') = '" . $month . "'
              GROUP BY week_name
              ORDER BY week_date ASC");

        $kd_materials = db::select("SELECT * FROM materials WHERE category = 'KD'");

        $sales_price = db::table('sales_prices_bk')
            ->where('sales_category', 'ALL')
            ->where('fiscal_year', $calendar[0]->fiscal_year)
            ->where('month', 'LIKE', '%' . $month . '%')
            ->get();

        //FORECAST
        $forecast = ProductionForecastResume::where(db::raw('DATE_FORMAT(`month`,"%Y-%m")'), '=', $month)->first();
        $budget = SalesBudgetResume::where(db::raw('DATE_FORMAT(`month`,"%Y-%m")'), '=', $month)->first();

        //SALES TARGET
        $target_sales_fg = db::select("SELECT weekly_calendars.week_name, shipment_schedules.material_number, shipment_schedules.quantity FROM shipment_schedules
                LEFT JOIN weekly_calendars ON weekly_calendars.week_date = shipment_schedules.bl_date
                WHERE shipment_schedules.bl_date LIKE '%" . $month . "%'");

        $target_sales_eo = db::select("SELECT weekly_calendars.week_name, extra_order_details.material_number, extra_order_details.quantity, extra_order_details.sales_price FROM extra_order_details
                LEFT JOIN weekly_calendars ON weekly_calendars.week_date = extra_order_details.request_date
                WHERE extra_order_details.request_date LIKE '%" . $month . "%'
                AND extra_order_details.due_date IS NOT NULL");

        $sales_resume_final = [];

        for ($i = 0; $i < count($target_sales_fg); $i++) {
            $price = 0;
            $amount = 0;
            for ($j = 0; $j < count($sales_price); $j++) {
                if ($target_sales_fg[$i]->material_number == $sales_price[$j]->material_number) {
                    $price = $sales_price[$j]->price;
                    break;
                }
            }

            $key = $target_sales_fg[$i]->week_name;
            $amount = $price * $target_sales_fg[$i]->quantity;

            if (!array_key_exists($key, $sales_resume_final)) {
                $row = array();
                $row['week_name'] = $target_sales_fg[$i]->week_name;
                $row['week_number'] = (int) str_replace("W", "", $target_sales_fg[$i]->week_name);
                $row['amount'] = $amount;

                $sales_resume_final[$key] = $row;
            } else {
                $sales_resume_final[$key]['amount'] = $sales_resume_final[$key]['amount'] + $amount;
            }
        }

        for ($i = 0; $i < count($target_sales_eo); $i++) {
            $key = $target_sales_eo[$i]->week_name;
            $amount = $target_sales_eo[$i]->sales_price * $target_sales_eo[$i]->quantity;

            if (!array_key_exists($key, $sales_resume_final)) {
                $row = array();
                $row['week_name'] = $target_sales_eo[$i]->week_name;
                $row['week_number'] = (int) str_replace("W", "", $target_sales_eo[$i]->week_name);
                $row['amount'] = $amount;

                $sales_resume_final[$key] = $row;
            } else {
                $sales_resume_final[$key]['amount'] = $sales_resume_final[$key]['amount'] + $amount;
            }
        }

        usort($sales_resume_final, function ($a, $b) {return $a['week_number'] - $b['week_number'];});

        //PRODUCTION TARGET
        $production_fg_eo = db::select("SELECT weekly_calendars.week_name, production_schedules.material_number, production_schedules.quantity FROM production_schedules
                LEFT JOIN materials ON materials.material_number = production_schedules.material_number
                LEFT JOIN weekly_calendars ON weekly_calendars.week_date = production_schedules.due_date
                WHERE production_schedules.due_date LIKE '%" . $month . "%'
                AND materials.category = 'FG'
                UNION ALL
                SELECT weekly_calendars.week_name, extra_order_details.material_number, extra_order_details.quantity FROM extra_order_details
                LEFT JOIN weekly_calendars ON weekly_calendars.week_date = extra_order_details.due_date
                LEFT JOIN extra_order_materials ON extra_order_materials.material_number = extra_order_details.material_number
                WHERE extra_order_details.due_date LIKE '%" . $month . "%'
                AND extra_order_materials.is_completion = 1");

        $production_kd = db::select("SELECT production_schedules_one_steps.due_date, production_schedules_one_steps.material_number, production_schedules_one_steps.quantity FROM production_schedules_one_steps
                WHERE production_schedules_one_steps.due_date LIKE  '%" . $month . "%'");

        $production_resume = [];
        for ($i = 0; $i < count($production_kd); $i++) {
            for ($y = 0; $y < count($kd_materials); $y++) {
                if ($kd_materials[$y]->material_number == $production_kd[$i]->material_number) {
                    $week_name = '';
                    for ($x = 0; $x < count($calendar); $x++) {
                        if ($calendar[$x]->week_date == $production_kd[$i]->due_date) {
                            $week_name = $calendar[$x]->week_name;
                            break;
                        }
                    }

                    $row = array();
                    $row['week_name'] = $week_name;
                    $row['material_number'] = $production_kd[$i]->material_number;
                    $row['quantity'] = $production_kd[$i]->quantity;
                    $production_resume[] = (object) $row;
                    break;
                }
            }
        }

        $production_resume = array_merge($production_resume, $production_fg_eo);
        $production_resume_final = [];

        for ($i = 0; $i < count($production_resume); $i++) {
            $price = 0;
            $amount = 0;
            for ($j = 0; $j < count($sales_price); $j++) {
                if ($production_resume[$i]->material_number == $sales_price[$j]->material_number) {
                    $price = $sales_price[$j]->price;
                    break;
                }
            }

            $key = $production_resume[$i]->week_name;
            $amount = $price * $production_resume[$i]->quantity;

            if (!array_key_exists($key, $production_resume_final)) {
                $row = array();
                $row['week_name'] = $production_resume[$i]->week_name;
                $row['week_number'] = (int) str_replace("W", "", $production_resume[$i]->week_name);
                $row['amount'] = $amount;

                $production_resume_final[$key] = $row;
            } else {
                $production_resume_final[$key]['amount'] = $production_resume_final[$key]['amount'] + $amount;
            }
        }

        usort($production_resume_final, function ($a, $b) {return $a['week_number'] - $b['week_number'];});

        //SALES
        $sales = db::table('sales_resumes')->where(db::raw('DATE_FORMAT(bl_date,"%Y-%m")'), '=', $month)->get();

        //PRODUCTION
        $production = db::connection('ympimis_2')
            ->select("SELECT DATE_FORMAT(result_date, '%Y-%m-%d') AS date, material_number, quantity FROM production_results
                        WHERE result_date LIKE '%" . $month . "%'
                        AND serial_number IS NOT NULL
                        AND category NOT LIKE '%error%'");

        $new_production = [];
        for ($h = 0; $h < count($production); $h++) {
            $price = 0;
            for ($i = 0; $i < count($sales_price); $i++) {
                if ($production[$h]->material_number == $sales_price[$i]->material_number) {
                    $price = $sales_price[$i]->price;
                    break;
                }
            }

            $row = array();
            $row['date'] = $production[$h]->date;
            $row['material_number'] = $production[$h]->material_number;
            $row['quantity'] = $production[$h]->quantity;
            $row['price'] = $price;
            $row['amount'] = $price * $production[$h]->quantity;
            $new_production[] = (object) $row;

        }

        $response = array(
            'status' => true,
            'now' => date('Y-m-d'),
            'last_update' => date('Y-m-d H:i:s'),
            'month_name' => date('F Y', strtotime($month . '-01')),
            'calendar' => $calendar,
            'resume_calendar' => $resume_calendar,
            'forecast' => $forecast,
            'budget' => $budget,
            'sales' => $sales,
            'sales_target' => $sales_resume_final,
            'production' => $new_production,
            'production_target' => $production_resume_final,
        );
        return Response::json($response);

    }

    public function fetchShippingAmountResume(Request $request)
    {

        $category = $request->get('category');
        $fy = $request->get('fy');
        if (strlen($fy) <= 0) {
            $now = WeeklyCalendar::where('week_date', date('Y-m-d'))->first();
            $fy = $now->fiscal_year;
        }

        if ($category == 'budget') {
            $table = 'sales_budget_resumes';
        } elseif ($category == 'forecast') {
            $table = 'production_forecast_resumes';
        }

        $data = db::select("SELECT fiscal_year, `month`, DATE_FORMAT(`month`,'%b-%Y') AS text, amount FROM " . $table . "
              WHERE fiscal_year = '" . $fy . "'
              ORDER BY `month` ASC");

        $response = array(
            'status' => true,
            'fy' => $fy,
            'data' => $data,
        );
        return Response::json($response);

    }

    public function updateShippingAmountResume(Request $request)
    {

        $category = $request->get('category');
        $data = $request->get('update');

        DB::beginTransaction();
        if ($category == 'budget') {

            for ($i = 0; $i < count($data); $i++) {
                try {
                    $update = SalesBudgetResume::where('month', $data[$i]['month'])
                        ->update([
                            'amount' => $data[$i]['amount'],
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
            }

            DB::commit();
            $response = array(
                'status' => true,
            );
            return Response::json($response);

        } elseif ($category == 'forecast') {

            for ($i = 0; $i < count($data); $i++) {
                try {
                    $update = ProductionForecastResume::where('month', $data[$i]['month'])
                        ->update([
                            'amount' => $data[$i]['amount'],
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
            }

            DB::commit();
            $response = array(
                'status' => true,
            );
            return Response::json($response);

        }

    }

    public function fetchShipmentReport(Request $request)
    {
        if (strlen($request->get('date')) > 0) {
            $year = date('Y', strtotime($request->get('date')));
            $date = date('Y-m-d', strtotime($request->get('date')));
            $week_date = date('Y-m-d', strtotime($date . '+ 3 day'));
            $now = date('Y-m-d', strtotime($date));
            $first = date('Y-m-d', strtotime(Carbon::parse('first day of ' . date('F Y', strtotime($date)))));
            $week = DB::table('weekly_calendars')->where('week_date', '=', $week_date)->first();
            $week2 = DB::table('weekly_calendars')->where('week_date', '=', $date)->first();
        } else {
            $year = date('Y');
            $date = date('Y-m-d');
            $now = date('Y-m-d');
            $week_date = date('Y-m-d', strtotime(carbon::now()->addDays(3)));
            $first = date('Y-m-01');
            $week = DB::table('weekly_calendars')->where('week_date', '=', $week_date)->first();
            $week2 = DB::table('weekly_calendars')->where('week_date', '=', $date)->first();
        }

        $query3 = "select hpl, sum(plan)-sum(actual) as plan, sum(actual) as actual, avg(prc1) as prc_actual, 1-avg(prc1) as prc_plan from
            (
            select material_number, hpl, category, plan, coalesce(actual, 0) as actual, coalesce(actual, 0)/plan as prc1 from
            (
            select shipment_schedules.id, shipment_schedules.material_number, materials.hpl, materials.category, shipment_schedules.quantity as plan, if(flos.actual>shipment_schedules.quantity, shipment_schedules.quantity, flos.actual) as actual from shipment_schedules
            left join weekly_calendars on weekly_calendars.week_date = shipment_schedules.bl_date
            left join (select shipment_schedule_id, sum(actual) as actual from flos group by shipment_schedule_id) as flos
            on flos.shipment_schedule_id = shipment_schedules.id
            left join materials on materials.material_number = shipment_schedules.material_number
            where weekly_calendars.week_name = '" . $week->week_name . "' and year(weekly_calendars.week_date) = '" . $year . "' and materials.category = 'FG'

            union all

            select shipment_schedules.id, shipment_schedules.material_number, materials.hpl, materials.category, shipment_schedules.quantity as plan, flos.actual as actual from shipment_schedules
            left join weekly_calendars on weekly_calendars.week_date = shipment_schedules.bl_date
            left join (select shipment_schedule_id, sum(actual) as actual from flos group by shipment_schedule_id) as flos
            on flos.shipment_schedule_id = shipment_schedules.id
            left join materials on materials.material_number = shipment_schedules.material_number
            where weekly_calendars.week_name <> '" . $week->week_name . "' and year(weekly_calendars.week_date) = '" . $year . "' and materials.category = 'FG' and weekly_calendars.week_date < '" . $week_date . "' and flos.actual < shipment_schedules.quantity
            ) as result1
            ) result2
            group by hpl
            order by field(hpl, 'FLFG', 'CLFG', 'ASFG', 'TSFG', 'PN', 'RC', 'VENOVA')";

        $chartResult3 = DB::select($query3);

        $response = array(
            'status' => true,
            'chartResult3' => $chartResult3,
            'week' => 'Week ' . substr($week2->week_name, 1),
            'weekTitle' => 'Week ' . substr($week->week_name, 1),
            'dateTitle' => date('d F Y', strtotime($date)),
            'now' => $now,
        );
        return Response::json($response);
    }

    public function fetchShipmentReportDetail(Request $request)
    {
        $year = date('Y', strtotime($request->get('date')));
        $last_date = DB::table('weekly_calendars')
            ->where('week_name', '=', $request->get('week'))
            ->where(db::raw('year(weekly_calendars.week_date)'), '=', $year)
            ->select(db::raw('min(week_date) as week_date'))
            ->first();

        $query1 = "select material_number, material_description, sum(quantity) as quantity from
            (
            select shipment_schedules.material_number, materials.material_description, if(sum(shipment_schedules.quantity)<sum(flos.actual), sum(shipment_schedules.quantity), sum(flos.actual)) as quantity
            from shipment_schedules
            left join materials on materials.material_number = shipment_schedules.material_number
            left join weekly_calendars on weekly_calendars.week_date = shipment_schedules.bl_date
            left join (select shipment_schedule_id, sum(actual) as actual from flos group by shipment_schedule_id) as flos
            on flos.shipment_schedule_id = shipment_schedules.id
            where weekly_calendars.week_name = '" . $request->get('week') . "' and materials.category = 'FG' and materials.hpl = '" . $request->get('hpl') . "' and year(weekly_calendars.week_date) = '" . $year . "'
            group by shipment_schedules.material_number, materials.material_description
            having if(sum(shipment_schedules.quantity)<sum(flos.actual), sum(shipment_schedules.quantity), sum(flos.actual)) > 0

            union all

            select shipment_schedules.material_number, materials.material_description, if(sum(shipment_schedules.quantity)<sum(flos.actual), sum(shipment_schedules.quantity), sum(flos.actual)) as quantity
            from shipment_schedules
            left join materials on materials.material_number = shipment_schedules.material_number
            left join weekly_calendars on weekly_calendars.week_date = shipment_schedules.bl_date
            left join (select shipment_schedule_id, sum(actual) as actual from flos group by shipment_schedule_id) as flos
            on flos.shipment_schedule_id = shipment_schedules.id
            where weekly_calendars.week_date < '" . $last_date->week_date . "' and materials.category = 'FG' and materials.hpl = '" . $request->get('hpl') . "' and year(weekly_calendars.week_date) = '" . $year . "' and flos.actual < shipment_schedules.quantity
            group by shipment_schedules.material_number, materials.material_description
            ) as result1
            group by material_number, material_description";

        $query2 = "select material_number, material_description, sum(quantity) as quantity from
            (
            select shipment_schedules.material_number, materials.material_description, if(sum(shipment_schedules.quantity)-coalesce(sum(flos.actual), 0) < 0, 0, sum(shipment_schedules.quantity)-coalesce(sum(flos.actual), 0)) as quantity
            from shipment_schedules
            left join materials on materials.material_number = shipment_schedules.material_number
            left join weekly_calendars on weekly_calendars.week_date = shipment_schedules.bl_date
            left join (select shipment_schedule_id, sum(actual) as actual from flos group by shipment_schedule_id) as flos
            on flos.shipment_schedule_id = shipment_schedules.id
            where weekly_calendars.week_name = '" . $request->get('week') . "' and materials.category = 'FG' and materials.hpl = '" . $request->get('hpl') . "' and year(weekly_calendars.week_date) = '" . $year . "'
            group by shipment_schedules.material_number, materials.material_description
            having if(sum(shipment_schedules.quantity)-coalesce(sum(flos.actual), 0) < 0, 0, sum(shipment_schedules.quantity)-coalesce(sum(flos.actual), 0)) > 0

            union all

            select shipment_schedules.material_number, materials.material_description, if(sum(shipment_schedules.quantity)-coalesce(sum(flos.actual), 0) < 0, 0, sum(shipment_schedules.quantity)-coalesce(sum(flos.actual), 0)) as quantity
            from shipment_schedules
            left join materials on materials.material_number = shipment_schedules.material_number
            left join weekly_calendars on weekly_calendars.week_date = shipment_schedules.bl_date
            left join (select shipment_schedule_id, sum(actual) as actual from flos group by shipment_schedule_id) as flos
            on flos.shipment_schedule_id = shipment_schedules.id
            where weekly_calendars.week_date < '" . $last_date->week_date . "' and materials.category = 'FG' and materials.hpl = '" . $request->get('hpl') . "' and year(weekly_calendars.week_date) = '" . $year . "'
            group by shipment_schedules.material_number, materials.material_description
            having if(sum(shipment_schedules.quantity)-coalesce(sum(flos.actual), 0) < 0, 0, sum(shipment_schedules.quantity)-coalesce(sum(flos.actual), 0)) > 0 and sum(flos.actual) < sum(shipment_schedules.quantity)
            ) as result1
            group by material_number, material_description";

        if ($request->get('name') == 'Actual') {
            $blData = db::select($query1);
        }
        if ($request->get('name') == 'Plan') {
            $blData = db::select($query2);
        }

        $response = array(
            'status' => true,
            'blData' => $blData,
            'tes' => $last_date,
        );
        return Response::json($response);
    }

    public function fetchAllStock()
    {

        $query = "select if(stock.destination_code is null, 'Maedaoshi', destinations.destination_shortname) as destination, sum(production) as production, sum(intransit) as intransit, sum(fstk) as fstk, sum(actual) as actual, sum(coalesce(volume,0)) as volume from (

            select shipment_schedules.destination_code, sum(if(flos.status = 'M' or flos.status = '0', flos.actual, 0)) as production, sum(if(flos.status = '2', flos.actual, 0)) as fstk, sum(if(flos.status = '1', flos.actual, 0)) as intransit, sum(flos.actual) as actual, sum(flos.actual*(material_volumes.length*material_volumes.width*material_volumes.height)/material_volumes.lot_carton) as volume
            from flos
            left join shipment_schedules on shipment_schedules.id = flos.shipment_schedule_id
            left join material_volumes on material_volumes.material_number = flos.material_number
            where flos.status in ('0','1','2','M') and flos.actual > 0
            group by shipment_schedules.destination_code

            union all

            select shipment_schedules.destination_code, sum(if(knock_downs.status = 'M' or knock_downs.status = '0', knock_down_details.quantity, 0)) as production, sum(if(knock_downs.status = '2', knock_down_details.quantity, 0)) as fstk, sum(if(knock_downs.status = '1', knock_down_details.quantity, 0)) as intransit, sum(knock_down_details.quantity) as actual, sum(knock_down_details.quantity*(material_volumes.length*material_volumes.width*material_volumes.height)/material_volumes.lot_carton) as volume
            from knock_down_details
            left join knock_downs on knock_downs.kd_number = knock_down_details.kd_number
            left join shipment_schedules on shipment_schedules.id = knock_down_details.shipment_schedule_id
            left join material_volumes on material_volumes.material_number = knock_down_details.material_number
            where knock_downs.status in ('0','1','2','M') and knock_down_details.quantity > 0
            group by shipment_schedules.destination_code) as stock left join destinations on destinations.destination_code = stock.destination_code group by if(stock.destination_code is null, 'Maedaoshi', destinations.destination_shortname)";

        $jsonData = db::select($query);

        $query2 = "select stock.material_number, materials.material_description, materials.hpl, if(stock.status = 'M' or stock.status = '0', 'Production', if(stock.status = '1', 'Intransit', 'FSTK')) as location, if(destinations.destination_shortname is null, 'Maedaoshi', destinations.destination_shortname) as destination, sum(stock.quantity) as quantity from (
            select flos.material_number, flos.destination_code, flos.status, sum(flos.actual) as quantity from flos
            where flos.status in ('M', '0', '1', '2')
            and flos.actual > 0
            group by flos.material_number, flos.destination_code, flos.status

            union all

            select knock_down_details.material_number, shipment_schedules.destination_code, knock_downs.status, sum(knock_down_details.quantity) as quantity from knock_down_details
            left join knock_downs on knock_downs.kd_number = knock_down_details.kd_number
            left join shipment_schedules on shipment_schedules.id = knock_down_details.shipment_schedule_id
            where knock_downs.status in ('M', '0', '1', '2')
            and knock_down_details.quantity > 0
            group by knock_down_details.material_number, shipment_schedules.destination_code, knock_downs.status) as stock
            left join materials on materials.material_number = stock.material_number
            left join destinations on destinations.destination_code = stock.destination_code
            group by stock.material_number, if(destinations.destination_shortname is null, 'Maedaoshi', destinations.destination_shortname), if(stock.status = 'M' or stock.status = '0', 'Production', if(stock.status = '1', 'Intransit', 'FSTK')), stock.status, materials.material_description, materials.hpl";

        $stock = db::select($query2);

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

        $response = array(
            'status' => true,
            'jsonData' => $jsonData,
            'stockData' => $stock,
            'materials' => $materials,
        );
        return Response::json($response);
    }

    public function fetchStuffingProgress(Request $request)
    {

        if ($request->get('date') == "") {
            $now = date('Y-m-d');
            $end = date('Y-m-d', strtotime($now . ' + 7 days'));
        } else {
            $now = $request->get('date');
            $end = date('Y-m-d', strtotime($now . ' + 7 days'));
        }

        $query = "select if(master_checksheets.`departure` is not null, 'DEPARTED', if(actual_stuffing.total_actual > 0, 'LOADING', '-')) as stats, master_checksheets.`departure`, master_checksheets.`status`, master_checksheets.id_checkSheet, master_checksheets.destination, shipment_conditions.shipment_condition_name, actual_stuffing.total_plan, actual_stuffing.total_actual, master_checksheets.reason, master_checksheets.status, master_checksheets.start_stuffing, master_checksheets.finish_stuffing,COALESCE( master_checksheets.deleted_at,'-') as deleted_at from master_checksheets left join shipment_conditions on shipment_conditions.shipment_condition_code = master_checksheets.carier
            left join
            (
            select id_checkSheet, sum(plan_loading) as total_plan, sum(actual_loading) as total_actual from (
            select id_checkSheet, qty_qty as plan_loading, (qty_qty/if(package_qty = '-' or package_qty is null, 1, package_qty))*if(confirm = 0 and bara = 0, 1, confirm) as actual_loading from detail_checksheets where deleted_at is null
            ) as stuffings
            group by id_checkSheet
            ) as actual_stuffing
            on actual_stuffing.id_checkSheet = master_checksheets.id_checkSheet
            where  master_checksheets.Stuffing_date = '" . $now . "'
            order by field(stats, 'LOADING', 'INSPECTION', '-', 'DEPARTED')";

        $stuffing_progress = db::select($query);

        $query2 = "select master_checksheets.stuffing_date, count(if(master_checksheets.carier = 'C1', 1, null)) as 'sea', count(if(master_checksheets.carier = 'C2', 1, null)) as 'air', count(if(master_checksheets.carier = 'C4' or master_checksheets.carier = 'TR', 1, null)) as 'truck', sum(stuffings.total_plan) as total_plan from master_checksheets
            left join
            (
            select id_checkSheet, sum(qty_qty) as total_plan from detail_checksheets where deleted_at is null group by id_checkSheet
            ) as stuffings
            on stuffings.id_checkSheet = master_checksheets.id_checkSheet where master_checksheets.deleted_at is null and master_checksheets.Stuffing_date > '" . $now . "' and master_checksheets.Stuffing_date <= '" . $end . "' group by master_checksheets.Stuffing_date";

        $stuffing_resume = db::select($query2);

        $response = array(
            'status' => true,
            'stuffing_progress' => $stuffing_progress,
            'stuffing_resume' => $stuffing_resume,
        );
        return Response::json($response);
    }

    public function fetchStuffingDetail(Request $request)
    {
        $id_checkSheet = $request->get('id');
        $query = "select order_type, id_checkSheet, invoice, gmc, goods, qty_qty as plan_loading, (qty_qty/if(package_qty = '-' or package_qty is null, 1, package_qty))*if(confirm = 0 and bara = 0, 1, confirm) as actual_loading from detail_checksheets where deleted_at is null and id_checkSheet = '" . $id_checkSheet . "'";
        $stuffing_detail = db::select($query);
        // return DataTables::of($stuffing_detail)->make(true);
        $response = array(
            'status' => true,
            'stuffing_detail' => $stuffing_detail,
        );
        return Response::json($response);
    }

    public function fetch_dp_fg_accuracy_detail(Request $request)
    {
        $first = date('Y-m-d', strtotime(Carbon::parse('first day of ' . date('F Y', strtotime($request->get('date'))))));

        $query = "select materials.material_number, materials.material_description, final.plus+final.minus as qty from
            (
            select result.material_number, if(sum(result.actual)-sum(result.plan)>0,sum(result.actual)-sum(result.plan),0) as plus, if(sum(result.actual)-sum(result.plan)<0,sum(result.actual)-sum(result.plan),0) as minus from
            (
            select material_number, sum(quantity) as plan, 0 as actual
            from production_schedules
            where due_date >= '" . $first . "' and due_date <= '" . $request->get('date') . "'
            group by material_number

            union all

            select material_number, 0 as plan, sum(quantity) as actual
            from flo_details
            where date(created_at) >= '" . $first . "' and date(created_at) <= '" . $request->get('date') . "'
            group by material_number
            ) as result
            group by result.material_number
            ) as final
            left join materials on materials.material_number = final.material_number
            where materials.category = 'FG' and materials.hpl in ('" . $request->get('category') . "') and final.plus+final.minus <> 0 order by qty desc";

        $accuracyDetail = db::select($query);

        $response = array(
            'status' => true,
            'accuracyDetail' => $accuracyDetail,
            'title' => 'Details of ' . $request->get('category'),
        );
        return Response::json($response);
    }

    public function fetchModalShipmentProgress(Request $request)
    {
        $st_date = date('Y-m-d', strtotime($request->get('date')));

        $hpl = " and materials.hpl = '" . $request->get('hpl') . "'";

        if ($request->get('hpl') == 'all') {
            $hpl = "";
        }

        $query = "
            select a.material_number, a.material_description, a.destination_shortname, a.plan, coalesce(b.actual,0) as actual, coalesce(b.actual,0)-a.plan as diff from
            (
            select shipment_schedules.st_date, shipment_schedules.material_number, materials.material_description, shipment_schedules.destination_code, destinations.destination_shortname, sum(shipment_schedules.quantity) as plan from shipment_schedules
            left join materials on materials.material_number = shipment_schedules.material_number
            left join destinations on destinations.destination_code = shipment_schedules.destination_code
            where materials.category = 'FG' and shipment_schedules.st_date = '" . $st_date . "'

            " . $hpl . "

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

        $shipment_progress = DB::select($query);

        $response = array(
            'status' => true,
            'shipment_progress' => $shipment_progress,
        );
        return Response::json($response);
    }

    public function fetchShipmentProgress(Request $request)
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
            COALESCE ( B.act, 0 ) AS act,
            COALESCE ( B.plan, 0 ) AS plan,
            COALESCE ( B.actual, 0 ) AS actual
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

    public function fetch_dp_fg_accuracy()
    {
        $now = date('Y-m-d');
        // $queryAccuracyBI = "select g.week_name, g.week_date, sum(g.minus) as minus, sum(g.plus) as plus from
        // (
        // select f.week_name, f.week_date, f.material_number, f.material_mon, f.plan, f.actual, f.plan_acc, f.actual_acc, if(f.actual_acc-f.plan_acc < 0, f.actual_acc-f.plan_acc, 0) as minus, if(f.actual_acc-f.plan_acc < 0, 0, f.actual_acc-f.plan_acc) as plus from
        // (
        // select e.week_name, e.week_date, e.material_number, e.material_mon, e.plan, e.actual,
        // (@plan:=if(@material = e.material_mon COLLATE utf8mb4_general_ci, @plan+e.plan, if(@material:=e.material_mon COLLATE utf8mb4_general_ci, e.plan, e.plan))) as plan_acc,
        // (@actual:=if(@material2 = e.material_mon COLLATE utf8mb4_general_ci, @actual+e.actual, if(@material2:=e.material_mon COLLATE utf8mb4_general_ci, e.actual, e.actual))) as actual_acc from
        // (
        // select c.week_name, c.week_date, b.material_number, concat(date_format(c.week_date, '%Y%m'), b.material_number) as material_mon, coalesce(production_schedules.quantity, 0) as plan, coalesce(d.actual,0) as actual from
        // (select weekly_calendars.week_name, weekly_calendars.week_date from weekly_calendars where weekly_calendars.week_date >= '2019-01-01' and weekly_calendars.week_date <= '" . $now  . "') as c
        // cross join
        // (
        // select materials.material_number from materials where materials.category = 'FG' and materials.hpl in ('CLFG', 'ASFG', 'TSFG', 'FLFG')
        // ) as b
        // left join
        // production_schedules on production_schedules.material_number = b.material_number and production_schedules.due_date = c.week_date
        // left join
        // (select material_number, date(created_at) as due_date, sum(quantity) as actual from flo_details where date(created_at) >= '2019-01-01' and date(created_at) <= '" . $now  . "' group by material_number, date(created_at)) as d on d.material_number = b.material_number and d.due_date = c.week_date
        // order by b.material_number asc, c.week_date asc limit 999999999999999
        // ) as e
        // cross join
        // (select @material := -1, @plan := 0) as params
        // cross join
        // (select @material2 := -1, @actual := 0) as params2
        // ) as f
        // ) as g
        // group by g.week_name, g.week_date order by g.week_date asc";

        // $queryAccuracyEI = "select g.week_name, g.week_date, sum(g.minus) as minus, sum(g.plus) as plus from
        // (
        // select f.week_name, f.week_date, f.material_number, f.material_mon, f.plan, f.actual, f.plan_acc, f.actual_acc, if(f.actual_acc-f.plan_acc < 0, f.actual_acc-f.plan_acc, 0) as minus, if(f.actual_acc-f.plan_acc < 0, 0, f.actual_acc-f.plan_acc) as plus from
        // (
        // select e.week_name, e.week_date, e.material_number, e.material_mon, e.plan, e.actual,
        // (@plan:=if(@material = e.material_mon COLLATE utf8mb4_general_ci, @plan+e.plan, if(@material:=e.material_mon COLLATE utf8mb4_general_ci, e.plan, e.plan))) as plan_acc,
        // (@actual:=if(@material2 = e.material_mon COLLATE utf8mb4_general_ci, @actual+e.actual, if(@material2:=e.material_mon COLLATE utf8mb4_general_ci, e.actual, e.actual))) as actual_acc from
        // (
        // select c.week_name, c.week_date, b.material_number, concat(date_format(c.week_date, '%Y%m'), b.material_number) as material_mon, coalesce(production_schedules.quantity, 0) as plan, coalesce(d.actual,0) as actual from
        // (select weekly_calendars.week_name, weekly_calendars.week_date from weekly_calendars where weekly_calendars.week_date >= '2019-01-01' and weekly_calendars.week_date <= '" . $now  . "') as c
        // cross join
        // (
        // select materials.material_number from materials where materials.category = 'FG' and materials.hpl in ('RC', 'PN', 'VENOVA')
        // ) as b
        // left join
        // production_schedules on production_schedules.material_number = b.material_number and production_schedules.due_date = c.week_date
        // left join
        // (select material_number, date(created_at) as due_date, sum(quantity) as actual from flo_details where date(created_at) >= '2019-01-01' and date(created_at) <= '" . $now  . "' group by material_number, date(created_at)) as d on d.material_number = b.material_number and d.due_date = c.week_date
        // order by b.material_number asc, c.week_date asc limit 999999999999999
        // ) as e
        // cross join
        // (select @material := -1, @plan := 0) as params
        // cross join
        // (select @material2 := -1, @actual := 0) as params2
        // ) as f
        // ) as g
        // group by g.week_name, g.week_date order by g.week_date asc";

        $queryAccuracy = "select g.week_name, g.week_date, g.hpl, sum(g.minus) as minus, sum(g.plus) as plus from
            (
            select f.week_name, f.week_date, f.material_number, f.hpl, f.material_mon, f.plan, f.actual, f.plan_acc, f.actual_acc, if(f.actual_acc-f.plan_acc < 0, f.actual_acc-f.plan_acc, 0) as minus, if(f.actual_acc-f.plan_acc < 0, 0, f.actual_acc-f.plan_acc) as plus from
            (
            select e.week_name, e.week_date, e.material_number, e.hpl, e.material_mon, e.plan, e.actual,
            (@plan:=if(@material = e.material_mon COLLATE utf8mb4_general_ci, @plan+e.plan, if(@material:=e.material_mon COLLATE utf8mb4_general_ci, e.plan, e.plan))) as plan_acc,
            (@actual:=if(@material2 = e.material_mon COLLATE utf8mb4_general_ci, @actual+e.actual, if(@material2:=e.material_mon COLLATE utf8mb4_general_ci, e.actual, e.actual))) as actual_acc from
            (
            select c.week_name, c.week_date, b.material_number, b.hpl, concat(date_format(c.week_date, '%Y%m'), b.material_number, b.hpl) as material_mon, coalesce(production_schedules.quantity, 0) as plan, coalesce(d.actual,0) as actual from
            (select weekly_calendars.week_name, weekly_calendars.week_date from weekly_calendars where weekly_calendars.week_date >= '2019-01-01' and weekly_calendars.week_date <= '" . $now . "') as c
            cross join
            (
            select materials.material_number, materials.hpl from materials where materials.category = 'FG'
            ) as b
            left join
            production_schedules on production_schedules.material_number = b.material_number and production_schedules.due_date = c.week_date
            left join
            (select material_number, date(created_at) as due_date, sum(quantity) as actual from flo_details where date(created_at) >= '2019-01-01' and date(created_at) <= '" . $now . "' group by material_number, date(created_at)) as d on d.material_number = b.material_number and d.due_date = c.week_date
            order by b.material_number asc, c.week_date asc limit 99999999999
            ) as e
            cross join
            (select @material := -1, @plan := 0) as params
            cross join
            (select @material2 := -1, @actual := 0) as params2
            ) as f
            ) as g
            group by g.week_name, g.week_date, g.hpl order by g.week_date asc";

        $accuracy = db::select($queryAccuracy);
        // $accuracyBI = db::select($queryAccuracyBI);
        // $accuracyEI = db::select($queryAccuracyEI);

        $response = array(
            'status' => true,
            'accuracy' => $accuracy,
            // 'accuracyBI' => $accuracyBI,
            // 'accuracyEI' => $accuracyEI,
        );
        return Response::json($response);
    }

    public function fetch_dp_stockroom_stock(Request $request)
    {
        // $stocks = db::table('kitto.inventories')
        // ->select('kitto.inventories.material_number', db::raw('sum(kitto.inventories.lot) as stock'))
        // ->groupBy('kitto.inventories.material_number')
        // ->get();

        $stock_plt_alto = db::table('ympimis.materials')
            ->leftjoin('kitto.inventories', 'kitto.inventories.material_number', '=', 'ympimis.materials.material_number')
            ->where('ympimis.materials.work_center', '=', 'WS51')
            ->where('ympimis.materials.category', '=', 'WIP')
            ->where('ympimis.materials.model', 'like', '%PLT%')
            ->where('ympimis.materials.material_description', 'like', 'A%')
            ->select('ympimis.materials.model', db::raw('sum(kitto.inventories.lot) as stock'))
            ->groupBy('ympimis.materials.model')
            ->orderBy('ympimis.materials.model', 'asc')
            ->get();

        $response = array(
            'status' => true,
            'stock_plt_alto' => $stock_plt_alto,
        );
        return Response::json($response);
    }

    public function fetch_dp_production_result(Request $request)
    {
        if ($request->get('hpl') == 'all') {
            $hpl = "where materials.category = 'FG'";
        } else {
            $hpl = "where materials.category = 'FG' and materials.origin_group_code = '" . $request->get('hpl') . "'";
        }

        $first = date('Y-m-01');
        if (date('Y-m-d') != date('Y-m-01')) {
            $last = date('Y-m-d', strtotime(Carbon::yesterday()));
        } else {
            $last = date('Y-m-d');
        }
        $now = date('Y-m-d');

        if ($first != $now) {
            $debt = "union all

                select material_number, sum(debt) as debt, 0 as plan, 0 as actual from
                (
                select material_number, -(sum(quantity)) as debt from production_schedules where due_date >= '" . $first . "' and due_date <= '" . $last . "' group by material_number

                union all

                select material_number, sum(quantity) as debt from flo_details where date(created_at) >= '" . $first . "' and date(created_at) <= '" . $last . "' group by material_number
                ) as debt
                group by material_number";
        } else {
            $debt = "";
        }

        $query = "select result.material_number, materials.material_description as model, sum(result.debt) as debt, sum(result.plan) as plan, sum(result.actual) as actual from
            (
            select material_number, 0 as debt, sum(quantity) as plan, 0 as actual
            from production_schedules
            where due_date = '" . $now . "'
            group by material_number

            union all

            select material_number, 0 as debt, 0 as plan, sum(quantity) as actual
            from flo_details
            where date(created_at) = '" . $now . "'
            group by material_number

            " . $debt . "

            ) as result
            left join materials on materials.material_number = result.material_number
            " . $hpl . "
            group by result.material_number, materials.material_description
            having sum(result.debt) <> 0 or sum(result.plan) <> 0 or sum(result.actual) <> 0";

        $tableData = DB::select($query);

        $response = array(
            'status' => true,
            'tableData' => $tableData,
        );
        return Response::json($response);
    }
}
