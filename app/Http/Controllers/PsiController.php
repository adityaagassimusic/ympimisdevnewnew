<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;

class PsiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexPsiCalendar()
    {

        $title = "PSI Calendar";
        $title_jp = "";

        return view('weekly_calendars.psi.index', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'PSI Calendar')->with('head', 'PSI Calendar');

    }

    public function indexPsi()
    {
        return view('psi.psi_table');
    }

    public function fetchPsiCalendar(Request $request)
    {

        $fy = $request->get('fy');
        if (strlen($fy) == 0) {
            $now = db::table('weekly_calendars')
                ->where('week_date', date('Y-m-d'))
                ->first();
            $fy = $now->fiscal_year;
        }

        $calendar = db::table('psi_calendars')
            ->where('fiscal_year', $fy)
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

        $count_day = db::table('psi_calendars')
            ->where('fiscal_year', $fy)
            ->select(
                db::raw('DATE_FORMAT(week_date, "%c") AS month'),
                db::raw('count(id) AS count')
            )
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get();

        $response = array(
            'status' => true,
            'calendar' => $calendar,
            'count_day' => $count_day,
        );
        return Response::json($response);

    }

    public function generatePsi(Request $request)
    {

        // $start = date('Y-m', strtotime('first day of +1 month'));

        // START GENERATE MONTH
        $start = date('Y-m');
        $end = date('Y-m', strtotime(date('Y-m') . '-01 +1 year')) . '-01';

        $start = new DateTime($start);
        $end = new DateTime($end);
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);

        $interval_month = [];
        foreach ($period as $dt) {
            $row = array();
            $row['month'] = $dt->format("Y-m-01");
            $row['text_month'] = $dt->format("F");
            $row['text_year'] = $dt->format("Y");

            array_push($interval_month, (object) $row);
        }
        // END GENERATE MONTH

        $next_month = date('Y-m-01', strtotime('first day of +1 month'));

        $material = db::table('materials')
            ->whereIn('category', ['KD', 'FG'])
            ->get();

        $prod_request = db::table('production_requests')
            ->where('request_month', '>=', $start->format("Y-m-d"))
            ->select(
                'request_month',
                'material_number',
                db::raw('SUM(quantity) AS quantity')
            )
            ->groupBy(
                'request_month',
                'material_number'
            )
            ->get();

        $prod_request_formatted = [];
        for ($i = 0; $i < count($prod_request); $i++) {
            $key = $prod_request[$i]->request_month . '_' . $prod_request[$i]->material_number;
            $prod_request_formatted[$key] = $prod_request[$i];
        }
        $prod_request = $prod_request_formatted;

        $stock = db::table('first_inventories')
            ->where('stock_date', $start->format("Y-m-d"))
            ->select(
                'stock_date',
                'material_number',
                db::raw('SUM(quantity) AS quantity')
            )
            ->groupBy(
                'stock_date',
                'material_number'
            )
            ->get();

        $stock_formatted = [];
        for ($i = 0; $i < count($stock); $i++) {
            $key = $stock[$i]->stock_date . '_' . $stock[$i]->material_number;
            $stock_formatted[$key] = $stock[$i];
        }
        $stock = $stock_formatted;

        $lot = db::table('material_volumes')
            ->whereIn('category', ['KD', 'FG'])
            ->get();
        $lot_formatted = [];
        for ($i = 0; $i < count($lot); $i++) {
            $key = $lot[$i]->material_number;
            $lot_formatted[$key] = $lot[$i];
        }
        $lot = $lot_formatted;

        $psi = [];
        for ($i = 0; $i < count($interval_month); $i++) {
            for ($j = 0; $j < count($material); $j++) {

                $request = 0;
                if (isset($prod_request[$interval_month[$i]->month . '_' . $material[$j]->material_number])) {
                    $request = $prod_request[$interval_month[$i]->month . '_' . $material[$j]->material_number]->quantity;
                }

                $plan_inventory = 0;
                $next_month = date('Y-m-01', strtotime('first day of +1 month', strtotime($interval_month[$i]->month)));
                if (isset($prod_request[$next_month . '_' . $material[$j]->material_number])) {
                    $plan_inventory = $prod_request[$next_month . '_' . $material[$j]->material_number]->quantity / 4;
                }

                $ending_stock = 0;
                if (isset($stock[$interval_month[$i]->month . '_' . $material[$j]->material_number])) {
                    $ending_stock = $stock[$interval_month[$i]->month . '_' . $material[$j]->material_number]->quantity;
                } else {
                    if (isset($prod_request[$interval_month[$i]->month . '_' . $material[$j]->material_number])) {
                        $ending_stock = $prod_request[$interval_month[$i]->month . '_' . $material[$j]->material_number]->quantity / 4;
                    }
                }

                $quantity = $request + $plan_inventory - $ending_stock;

                $lot_request = 1;
                if (isset($lot[$material[$j]->material_number])) {
                    $lot_request = $lot[$material[$j]->material_number]->lot_request;
                    if ($lot_request == 0) {
                        $lot_request = 1;
                    }
                }
                $ceil = ceil($quantity / $lot_request);
                $quantity = $ceil * $lot_request;

                $row = array();
                $row['month'] = $interval_month[$i]->month;
                $row['material_number'] = $material[$j]->material_number;
                $row['quantity'] = $quantity;
                $row['request'] = $request;
                $row['plan_inventory'] = $plan_inventory;
                $row['ending_stock'] = $ending_stock;

                $psi[] = (object) $row;

            }
        }

        $response = array(
            'status' => true,
            'interval' => $interval_month,
            'material' => $material,
            'psi' => $psi,
        );
        return Response::json($response);

    }

}
