<?php

namespace App\Http\Controllers;

use App\CodeGenerator;
use App\Container;
use App\ContainerSchedule;
use App\Destination;
use App\ShipmentNomination;
use App\ShipmentReservation;
use App\ShipmentReservationTemp;
use App\WeeklyCalendar;
use DataTables;
use Excel;
use File;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

class ContainerScheduleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->status = [
            'BOOKING REQUESTED',
            'BOOKING UNACCEPTED',
            'NO ACTION YET',
            'BOOKING CONFIRMED',
            'NO NEED ANYMORE',
            'OTHER',
        ];
        $this->application_rate = [
            'CONTRACTED RATE',
            'SPOT/EXTRA RATE',
        ];
        $this->nomination = [
            'MAIN',
            'SUB',
            'BACK UP',
            'OTHER',
        ];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function indexShippingOrder()
    {

        $title = 'Shipping Booking Management List';
        $title_jp = '船便予約管理リスト';

        $pods = ShipmentNomination::distinct()->select('port_of_delivery', 'country')->orderBy('country')->get();
        $cariers = ShipmentNomination::distinct()->select('carier')->orderBy('carier')->get();
        $nominations = ShipmentNomination::distinct()->select('nomination')->orderBy('nomination')->get();

        return view('container_schedules.shipping_order.index', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'pods' => $pods,
            'cariers' => $cariers,
            'nominations' => $nominations,
            'statuses' => $this->status,
            'application_rates' => $this->application_rate,
        ))->with('page', $title)->with('head', $title);
    }

    public function indexResumeShippingOrder()
    {
        $title = 'Shipping Booking Management List';
        $title_jp = '船便予約管理リスト';

        return view('container_schedules.shipping_order.resume', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Shipping Booking Management List')->with('head', 'Shipping Booking Management List');
    }

    public function indexShippingAgency()
    {
        $title = 'Shipping Line';
        $title_jp = '';

        $port_of_discharge = ShipmentNomination::distinct()->select('port_of_discharge')->orderBy('port_of_discharge')->get();
        $port_of_delivery = ShipmentNomination::distinct()->select('port_of_delivery')->orderBy('port_of_delivery')->get();
        $consignee = ShipmentNomination::distinct()->select('consignee')->orderBy('consignee')->get();

        return view('container_schedules.shipping_order.shipping_agency', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'nominations' => $this->nomination,
            'port_of_discharge' => $port_of_discharge,
            'port_of_delivery' => $port_of_delivery,
            'consignee' => $consignee,
        ))->with('page', $title)->with('head', 'Shipping Booking Management List');
    }

    public function fetchGetRefNumber(Request $request)
    {
        $period = $request->get('period');

        $getRefNumber = ShipmentReservation::where('period', $period)
            ->select('period', 'ycj_ref_number', 'port_of_delivery', 'country', 'stuffing_date')
            ->orderBy('ycj_ref_number', 'ASC')
            ->get();

        echo '<option value=""></option>';

        $ref = array();

        for ($i = 0; $i < count($getRefNumber); $i++) {

            if (!in_array($getRefNumber[$i]['ycj_ref_number'], $ref)) {

                echo '<option value="' . $getRefNumber[$i]['ycj_ref_number'] . '">' . $getRefNumber[$i]['ycj_ref_number'] . ' - ' . $getRefNumber[$i]['port_of_delivery'] . ' (' . $getRefNumber[$i]['country'] . ') - ' . $getRefNumber[$i]['stuffing_date'] . '</option>';

                array_push($ref, $getRefNumber[$i]['ycj_ref_number']);

            }
        }
    }

    public function fetchShippingAgencyDetail(Request $request)
    {

        $agency = ShipmentNomination::where('id', $request->get('id'))->first();

        $response = array(
            'status' => true,
            'agency' => $agency,
        );
        return Response::json($response);

    }

    public function fetchShippingAgency(Request $request)
    {

        $agency = ShipmentNomination::whereNull('deleted_at');

        if ($request->get('consignee') != null) {
            $agency = $agency->where('consignee', $request->get('consignee'));
        }
        if ($request->get('port_of_delivery') != null) {
            $agency = $agency->where('port_of_delivery', $request->get('port_of_delivery'));
        }
        if ($request->get('port_of_discharge') != null) {
            $agency = $agency->where('port_of_discharge', $request->get('port_of_discharge'));
        }

        $agency = $agency->orderBy('port_of_delivery', 'ASC')->get();

        return DataTables::of($agency)
            ->addColumn('action', function ($agency) {
                return '<button style="width: 50%; height: 100%;" onclick="deleteLines(\'' . $agency->id . '\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-trash"></i></span></button><button style="width: 50%; height: 100%;" onclick="editLines(\'' . $agency->id . '\')" class="btn btn-xs btn-warning form-control"><span><i class="fa fa-pencil"></i></span></button>';
            })
            ->rawColumns([
                'action' => 'action',
            ])
            ->make(true);

    }

    public function fetchCarier(Request $request)
    {
        $pod = $request->get('pod');
        $country = $request->get('country');

        $nominations = ShipmentNomination::where('port_of_delivery', $pod)
            ->where('country', $country)
            ->select('carier', 'nomination')
            ->orderBy('nomination', 'ASC')
            ->get();

        echo '<option value=""></option>';
        for ($i = 0; $i < count($nominations); $i++) {
            echo '<option value="' . $nominations[$i]['carier'] . '-' . $nominations[$i]['nomination'] . '">' . $nominations[$i]['carier'] . ' - ' . $nominations[$i]['nomination'] . '</option>';
        }
    }

    public function fetchShipReservation(Request $request)
    {

        $data = ShipmentReservation::whereNull('deleted_at');

        if (strlen($request->get('stuffingFrom')) > 0) {
            $stuffingFrom = date('Y-m-d', strtotime($request->get('stuffingFrom')));
            $data = $data->where('stuffing_date', '>=', $stuffingFrom);
        }
        if (strlen($request->get('stuffingTo')) > 0) {
            $stuffingTo = date('Y-m-d', strtotime($request->get('stuffingTo')));
            $data = $data->where('stuffing_date', '<=', $stuffingTo);
        }
        if (strlen($request->get('etdFrom')) > 0) {
            $etdFrom = date('Y-m-d', strtotime($request->get('etdFrom')));
            $data = $data->where('etd_date', '>=', $etdFrom);
        }
        if (strlen($request->get('etdTo')) > 0) {
            $etdTo = date('Y-m-d', strtotime($request->get('etdTo')));
            $data = $data->where('etd_date', '<=', $etdTo);
        }
        if (strlen($request->get('dueFrom')) > 0) {
            $dueFrom = date('Y-m-d', strtotime($request->get('dueFrom')));
            $data = $data->where('due_date', '>=', $stuffingFrom);
        }
        if (strlen($request->get('dueTo')) > 0) {
            $dueTo = date('Y-m-d', strtotime($request->get('dueTo')));
            $data = $data->where('due_date', '<=', $stuffingTo);
        }

        if ($request->get('search_period') != null) {
            $data = $data->where('period', $request->get('search_period'));
        }
        if ($request->get('search_ycj_ref') != null) {
            $data = $data->where('ycj_ref_number', $request->get('search_ycj_ref'));
        }
        if ($request->get('search_bl') != null) {
            $data = $data->where('booking_number', $request->get('search_bl'));
        }
        if ($request->get('search_invoice') != null) {
            $data = $data->where('invoice_number', $request->get('search_invoice'));
        }

        if ($request->get('search_help') != null) {
            $data = $data->whereIn('help', $request->get('search_help'));
        }
        if ($request->get('search_status') != null) {
            $data = $data->whereIn('status', $request->get('search_status'));
        }
        if ($request->get('serach_application_rate') != null) {
            $data = $data->whereIn('application_rate', $request->get('serach_application_rate'));
        }
        if ($request->get('serach_pod') != null) {
            $data = $data->whereIn('port_of_delivery', $request->get('serach_pod'));
        }

        if ($request->get('serach_carier') != null) {
            $data = $data->whereIn('carier', $request->get('serach_carier'));
        }
        if ($request->get('search_nomination') != null) {
            $data = $data->whereIn('nomination', $request->get('search_nomination'));
        }

        $data = $data->orderBy('ycj_ref_number', 'ASC')
            ->orderBy('stuffing_date', 'ASC')
            ->get();

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);
    }

    public function excelShipReservation(Request $request)
    {

        $period = '';
        $excel = ShipmentReservation::whereNull('deleted_at');

        if (strlen($request->get('stuffingFrom')) > 0) {
            $stuffingFrom = date('Y-m-d', strtotime($request->get('stuffingFrom')));
            $excel = $excel->where('stuffing_date', '>=', $stuffingFrom);
        }
        if (strlen($request->get('stuffingTo')) > 0) {
            $stuffingTo = date('Y-m-d', strtotime($request->get('stuffingTo')));
            $excel = $excel->where('stuffing_date', '<=', $stuffingTo);
        }
        if (strlen($request->get('etdFrom')) > 0) {
            $etdFrom = date('Y-m-d', strtotime($request->get('etdFrom')));
            $excel = $excel->where('etd_date', '>=', $etdFrom);
        }
        if (strlen($request->get('etdTo')) > 0) {
            $etdTo = date('Y-m-d', strtotime($request->get('etdTo')));
            $excel = $excel->where('etd_date', '<=', $etdTo);
        }
        if (strlen($request->get('dueFrom')) > 0) {
            $dueFrom = date('Y-m-d', strtotime($request->get('dueFrom')));
            $excel = $excel->where('due_date', '>=', $stuffingFrom);
        }
        if (strlen($request->get('dueTo')) > 0) {
            $dueTo = date('Y-m-d', strtotime($request->get('dueTo')));
            $excel = $excel->where('due_date', '<=', $dueTo);
        }

        if ($request->get('search_period') != null) {
            $excel = $excel->where('period', $request->get('search_period'));
            $period = ' ' . date('M Y', strtotime($request->get('search_period')));
        }
        if ($request->get('search_ycj_ref') != null) {
            $excel = $excel->where('ycj_ref_number', $request->get('search_ycj_ref'));
        }
        if ($request->get('search_bl') != null) {
            $excel = $excel->where('booking_number', $request->get('search_bl'));
        }
        if ($request->get('search_invoice') != null) {
            $excel = $excel->where('invoice_number', $request->get('search_invoice'));
        }

        if ($request->get('search_help') != null) {
            $excel = $excel->where('help', $request->get('search_help'));
        }
        if ($request->get('search_status') != null) {
            $excel = $excel->where('status', $request->get('search_status'));
        }
        if ($request->get('serach_application_rate') != null) {
            $excel = $excel->where('application_rate', $request->get('serach_application_rate'));
        }
        if ($request->get('serach_pod') != null) {
            $excel = $excel->where('port_of_delivery', $request->get('serach_pod'));
        }

        if ($request->get('serach_carier') != null) {
            $excel = $excel->where('carier', $request->get('serach_carier'));
        }
        if ($request->get('search_nomination') != null) {
            $excel = $excel->where('nomination', $request->get('search_nomination'));
        }

        $excel = $excel->orderBy('period', 'ASC')
            ->orderBy('ycj_ref_number', 'ASC')
            ->get();

        $resumes = [];
        for ($i = 0; $i < count($excel); $i++) {
            $key = $excel[$i]->ycj_ref_number;

            if (!array_key_exists($key, $resumes)) {
                $resumes[$key] = array(
                    'key' => $excel[$i]->ycj_ref_number,
                    'qty' => 1,
                );
            } else {
                $resumes[$key]['qty'] = (int) $resumes[$key]['qty'] + 1;
            }
        }

        $data = array(
            'excel' => $excel,
            'resumes' => $resumes,
        );

        // return view('container_schedules.shipping_order.excel_shipping_order', $data);

        ob_clean();
        Excel::create('Booking Management List' . $period, function ($excel) use ($data) {
            $excel->sheet('Booking Management List', function ($sheet) use ($data) {
                return $sheet->loadView('container_schedules.shipping_order.excel_shipping_order', $data);
            });
        })->export('xlsx');

    }

    public function fetchResumeShippingOrderDetail(Request $request)
    {
        $period = '';
        if (strlen($request->get('period')) > 0) {
            $period = $request->get('period');
        } else {
            $period = date('Y-m');
        }

        $date = $request->get('date');
        $st_date = date('m-d', strtotime($date));

        $resume = db::select("SELECT bml.*, GROUP_CONCAT( cs.invoice ) AS invoice, atd.actual_departed FROM
            ( SELECT DISTINCT period, ycj_ref_number, shipper, port_loading, port_of_delivery, country, stuffing_date, plan, plan_teus FROM shipment_reservations
                WHERE DATE_FORMAT( stuffing_date, '%m-%d' ) = '" . $st_date . "'
                AND period = '" . $period . "'
            ) AS bml
            LEFT JOIN
            ( SELECT DISTINCT period, ycj_ref_number, invoice FROM master_checksheets
                WHERE period = '" . $period . "'
            ) AS cs
            ON bml.ycj_ref_number = cs.ycj_ref_number
            LEFT JOIN
            ( SELECT period, ycj_ref_number, actual_departed FROM shipment_reservations
                WHERE DATE_FORMAT( stuffing_date, '%m-%d' ) = '" . $st_date . "'
                AND period = '" . $period . "'
                AND actual_departed IS NOT NULL
            ) AS atd
            ON bml.ycj_ref_number = atd.ycj_ref_number
            GROUP BY bml.period, bml.ycj_ref_number, bml.shipper, bml.port_loading, bml.port_of_delivery, bml.country, bml.stuffing_date, bml.plan, bml.plan_teus, atd.actual_departed");

        $detail = ShipmentReservation::where(db::raw("DATE_FORMAT(stuffing_date,'%m-%d')"), $st_date)
            ->where('period', $period)
            ->select(
                'period',
                'ycj_ref_number',
                'shipper',
                'port_loading',
                'port_of_delivery',
                'country',
                'plan',
                'plan_teus',
                'fortyhc',
                'forty',
                'twenty',
                'booking_number',
                'carier',
                'nomination',
                'stuffing_date',
                'etd_date',
                'application_rate',
                'status'
            )
            ->orderBy('ycj_ref_number', 'ASC')
            ->orderBy('stuffing_date', 'ASC')
            ->get();

        $response = array(
            'status' => true,
            'resume' => $resume,
            'detail' => $detail,
            'st_date' => $date,
        );
        return Response::json($response);
    }

    public function fetchResumeShippingOrder(Request $request)
    {

        $period = '';
        if (strlen($request->get('period')) > 0) {
            $period = $request->get('period');
        } else {
            $period = date('Y-m');
        }

        $month = date('F Y', strtotime($period));

        $last_query_data = "SELECT plan.port_of_delivery, plan.plan, COALESCE(confirmed.confirmed,0) AS confirmed, plan.plan - COALESCE(confirmed.confirmed,0) AS not_confirmed FROM
        (SELECT port_of_delivery, COUNT(ycj_ref_number) AS plan FROM
        (SELECT DISTINCT ycj_ref_number, port_of_delivery FROM shipment_reservations
        WHERE period = '" . $period . "') shipment
        GROUP BY port_of_delivery) plan
        LEFT JOIN
        (SELECT port_of_delivery, COUNT(ycj_ref_number) AS confirmed FROM
        (SELECT port_of_delivery, ycj_ref_number FROM shipment_reservations
        WHERE period = '" . $period . "'
        AND `status` = 'BOOKING CONFIRMED') shipment
        GROUP BY port_of_delivery) confirmed
        ON plan.port_of_delivery = confirmed.port_of_delivery
        ORDER BY not_confirmed DESC";

        $data = "SELECT port_of_delivery,
        SUM(container) as plan,
        SUM(IF(confirmed > 0, confirmed, 0)) as confirmed,
        SUM(IF(total_container = rejected, container, 0)) as rejected,
        SUM(container-IF(confirmed > 0, confirmed, 0)-IF(total_container = rejected, container, 0)) as not_confirmed
        FROM
        (SELECT ycj_ref_number,
        port_of_delivery,
        max( COALESCE ( fortyhc, 0 ) + COALESCE ( forty, 0 ) + COALESCE ( twenty, 0 ) ) AS container,
        sum( COALESCE ( fortyhc, 0 ) + COALESCE ( forty, 0 ) + COALESCE ( twenty, 0 ) ) AS total_container,
        sum( IF(STATUS = 'BOOKING UNACCEPTED',COALESCE ( fortyhc, 0 ) + COALESCE ( forty, 0 ) + COALESCE ( twenty, 0 ),0 ) ) AS rejected,
        sum( IF(STATUS = 'BOOKING CONFIRMED', COALESCE ( fortyhc, 0 ) + COALESCE ( forty, 0 ) + COALESCE ( twenty, 0 ),0 ) ) AS confirmed
        FROM shipment_reservations
        WHERE period = '" . $period . "'
        AND `status` <> 'NO NEED ANYMORE'
        GROUP BY ycj_ref_number, port_of_delivery ) AS resume
        GROUP BY port_of_delivery
        order by not_confirmed DESC

        SELECT resume.port_of_delivery, SUM(resume.plan) AS plan, SUM(resume.confirm) AS confirmed, SUM(resume.reject) AS rejected, SUM(resume.not_confirm) AS not_confirmed FROM
        (SELECT plan.*, reject.reject, (plan.plan - plan.confirm - reject.reject) AS not_confirm FROM
        (SELECT plan.port_of_delivery, plan.ycj_ref_number, IF(confirm.quantity IS NOT NULL, confirm.quantity, plan.quantity) AS plan, COALESCE(confirm.quantity,0) AS confirm FROM
        (SELECT port_of_delivery, ycj_ref_number,
        MAX((COALESCE(fortyhc,0) + COALESCE(forty,0) + COALESCE(twenty,0))) AS quantity FROM shipment_reservations
        WHERE period = '" . $period . "'
        AND `status` <> 'NO NEED ANYMORE'
        GROUP BY port_of_delivery, ycj_ref_number) plan
        LEFT JOIN
        (SELECT port_of_delivery, ycj_ref_number,
        MAX((COALESCE(fortyhc,0) + COALESCE(forty,0) + COALESCE(twenty,0))) AS quantity FROM shipment_reservations
        WHERE period = '" . $period . "'
        AND `status` = 'BOOKING CONFIRMED'
        GROUP BY port_of_delivery, ycj_ref_number) confirm
        ON plan.ycj_ref_number = confirm.ycj_ref_number) plan
        LEFT JOIN
        (SELECT ycj_ref_number, IF(SUM(count) = SUM(reject), 1, 0) AS reject FROM
        (SELECT stuffing_date, ycj_ref_number, `status`, 1 AS count, IF(`status` = 'BOOKING UNACCEPTED', 1, 0) AS reject, IF(`status` = 'BOOKING CONFIRMED', 1, 0) AS confirm FROM shipment_reservations
        WHERE period = '" . $period . "'
        AND `status` <> 'NO NEED ANYMORE') AS shipment
        GROUP BY ycj_ref_number) AS reject
        ON plan.ycj_ref_number = reject.ycj_ref_number) resume
        GROUP BY port_of_delivery
        ORDER BY not_confirm DESC, plan DESC";

        $data = db::select("SELECT plan.port_of_delivery, plan.plan, COALESCE(resume.departed,0) AS departed, COALESCE(resume.on_board,0) AS on_board, COALESCE(resume.confirmed,0) AS confirmed FROM
            (SELECT port_of_delivery, SUM(plan) AS plan FROM
            (SELECT DISTINCT ycj_ref_number, port_of_delivery, plan FROM `shipment_reservations`
            WHERE period = '" . $period . "') AS shipment
            GROUP BY port_of_delivery) AS plan
            LEFT JOIN
            (SELECT port_of_delivery, SUM(departed) as departed, SUM(on_board) AS on_board, SUM(stuffing) + SUM(confirmed) AS confirmed FROM
            (SELECT ycj_ref_number, port_of_delivery,
            IF(actual_departed IS NOT NULL, plan, 0) AS departed,
            IF(actual_on_board IS NOT NULL, (IF(actual_departed IS NOT NULL, 0, plan)), 0) AS on_board,
            IF(actual_stuffing IS NOT NULL, (IF(actual_on_board IS NOT NULL, 0, plan)), 0) AS stuffing,
            IF(actual_stuffing IS NULL, plan, 0) AS confirmed
            FROM `shipment_reservations`
            WHERE period = '" . $period . "'
            AND `status` = 'BOOKING CONFIRMED') AS shipmemt
            GROUP BY port_of_delivery) AS resume
            ON plan.port_of_delivery = resume.port_of_delivery
            ORDER BY plan.plan DESC");

        $datefrom = ShipmentReservation::where('period', $period)->orderBy('stuffing_date', 'ASC')->first();
        $dateto = WeeklyCalendar::where(db::raw('DATE_FORMAT(week_date,"%Y-%m")'), $period)->orderBy('week_date', 'DESC')->first();

        $lastquery_ship_by_dates = "SELECT plan.week_date, plan.plan, COALESCE(confirmed.confirmed,0) AS confirmed FROM
        (SELECT week_date, SUM(plan) AS plan FROM
        (SELECT week_date, 0 AS plan  FROM weekly_calendars
        WHERE DATE_FORMAT(week_date,'%Y-%m') = '" . $period . "'
        AND remark <> 'H'
        UNION ALL
        SELECT stuffing_date, COUNT(ycj_ref_number) AS plan FROM
        (SELECT DISTINCT stuffing_date, ycj_ref_number FROM shipment_reservations
        WHERE period = '" . $period . "') shipment
        GROUP BY stuffing_date) AS shp_plan
        GROUP BY week_date) plan
        LEFT JOIN
        (SELECT stuffing_date , COUNT(ycj_ref_number) AS confirmed FROM
        (SELECT stuffing_date, ycj_ref_number FROM shipment_reservations
        WHERE period = '" . $period . "'
        AND `status` = 'BOOKING CONFIRMED') shipment
        GROUP BY stuffing_date) confirmed
        ON plan.week_date = confirmed.stuffing_date

        SELECT date.week_date, COALESCE(plan.reject,0) AS reject, COALESCE(plan.confirm,0) AS confirm, COALESCE(plan.not_confirm,0) AS not_confirm FROM
        (SELECT week_date, remark FROM weekly_calendars
        WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '" . $datefrom->stuffing_date . "'
        AND DATE_FORMAT(week_date,'%Y-%m-%d') <= '" . $dateto->week_date . "') AS date
        LEFT JOIN
        (SELECT stuffing_date, SUM(reject) AS reject, SUM(confirm) AS confirm, SUM(not_confirm) AS not_confirm FROM
        (SELECT shipment.stuffing_date, shipment.ycj_ref_number, IF(resume.reject = 1, shipment.quantity, 0) AS reject, IF(resume.confirm = 1, shipment.quantity, 0) AS confirm, IF(resume.reject = 0 AND resume.confirm = 0, shipment.quantity, 0) AS not_confirm FROM
        (SELECT ycj_ref_number, MAX(quantity) AS quantity, MIN(stuffing_date) AS stuffing_date FROM
        (SELECT DISTINCT stuffing_date, ycj_ref_number, (COALESCE(fortyhc,0) + COALESCE(forty,0) + COALESCE(twenty,0)) AS quantity FROM shipment_reservations
        WHERE period = '" . $period . "'
        AND `status` <> 'NO NEED ANYMORE') AS shipment
        GROUP BY ycj_ref_number) AS shipment
        LEFT JOIN
        (SELECT stuffing_date, ycj_ref_number, IF(SUM(count) = SUM(reject), 1, 0) AS reject, IF(SUM(confirm) > 0, 1, 0) AS confirm FROM
        (SELECT stuffing_date, ycj_ref_number, `status`, 1 AS count, IF(`status` = 'BOOKING UNACCEPTED', 1, 0) AS reject, IF(`status` = 'BOOKING CONFIRMED', 1, 0) AS confirm FROM shipment_reservations
        WHERE period = '" . $period . "'
        AND `status` <> 'NO NEED ANYMORE') AS shipment
        GROUP BY stuffing_date, ycj_ref_number) AS resume
        ON shipment.ycj_ref_number = resume.ycj_ref_number AND shipment.stuffing_date = resume.stuffing_date) AS shipment
        GROUP BY stuffing_date) AS plan
        ON plan.stuffing_date = date.week_date
        ORDER BY date.week_date ASC";

        $ship_by_dates = db::select("SELECT date.week_date, COALESCE(plan.plan, 0) AS plan, COALESCE(resume.departed, 0) AS departed, COALESCE(resume.on_board, 0) AS on_board, COALESCE(resume.stuffing, 0) AS stuffing, COALESCE(resume.confirmed, 0) AS confirmed FROM
            (SELECT week_date, remark FROM weekly_calendars
            WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '" . $datefrom->stuffing_date . "'
            AND DATE_FORMAT(week_date,'%Y-%m-%d') <= '" . $dateto->week_date . "') AS date
            LEFT JOIN
            (SELECT stuffing_date, SUM(plan) AS plan FROM
            (SELECT DISTINCT ycj_ref_number, stuffing_date, plan FROM `shipment_reservations`
            WHERE period = '" . $period . "') AS shipment
            GROUP BY stuffing_date) AS plan
            ON date.week_date = plan.stuffing_date
            LEFT JOIN
            (SELECT stuffing_date, SUM(departed) as departed, SUM(on_board) AS on_board, SUM(stuffing) AS stuffing, SUM(confirmed) AS confirmed FROM
            (SELECT ycj_ref_number, stuffing_date,
            IF(actual_departed IS NOT NULL, plan, 0) AS departed,
            IF(actual_on_board IS NOT NULL, (IF(actual_departed IS NOT NULL, 0, plan)), 0) AS on_board,
            IF(actual_stuffing IS NOT NULL, (IF(actual_on_board IS NOT NULL, 0, plan)), 0) AS stuffing,
            IF(actual_stuffing IS NULL, plan, 0) AS confirmed
            FROM `shipment_reservations`
            WHERE period = '" . $period . "'
            AND `status` = 'BOOKING CONFIRMED') AS shipmemt
            GROUP BY stuffing_date) AS resume
            ON date.week_date = resume.stuffing_date
            ORDER BY date.week_date ASC");

        $teus = db::select("SELECT date.week_date, COALESCE(plan_teus.plan_teus, 0) AS plan_teus, COALESCE(resume.departed, 0) AS departed, COALESCE(resume.on_board, 0) AS on_board, COALESCE(resume.stuffing, 0) AS stuffing, COALESCE(resume.confirmed, 0) AS confirmed FROM
            (SELECT week_date, remark FROM weekly_calendars
            WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '" . $datefrom->stuffing_date . "'
            AND DATE_FORMAT(week_date,'%Y-%m-%d') <= '" . $dateto->week_date . "') AS date
            LEFT JOIN
            (SELECT stuffing_date, SUM(plan_teus) AS plan_teus FROM
            (SELECT DISTINCT ycj_ref_number, stuffing_date, plan_teus FROM `shipment_reservations`
            WHERE period = '" . $period . "') AS shipment
            GROUP BY stuffing_date) AS plan_teus
            ON date.week_date = plan_teus.stuffing_date
            LEFT JOIN
            (SELECT stuffing_date, SUM(departed) as departed, SUM(on_board) AS on_board, SUM(stuffing) AS stuffing, SUM(confirmed) AS confirmed FROM
            (SELECT ycj_ref_number, stuffing_date,
            IF(actual_departed IS NOT NULL, plan_teus, 0) AS departed,
            IF(actual_on_board IS NOT NULL, (IF(actual_departed IS NOT NULL, 0, plan_teus)), 0) AS on_board,
            IF(actual_stuffing IS NOT NULL, (IF(actual_on_board IS NOT NULL, 0, plan_teus)), 0) AS stuffing,
            IF(actual_stuffing IS NULL, plan_teus, 0) AS confirmed
            FROM `shipment_reservations`
            WHERE period = '" . $period . "'
            AND `status` = 'BOOKING CONFIRMED') AS shipmemt
            GROUP BY stuffing_date) AS resume
            ON date.week_date = resume.stuffing_date
            ORDER BY date.week_date ASC");

        $application_rate = db::select("SELECT application_rate, SUM(COALESCE(fortyhc,0)+COALESCE(forty,0)+COALESCE(twenty,0)) AS qty FROM shipment_reservations
            WHERE `status` = 'BOOKING CONFIRMED'
            AND period = '" . $period . "'
            GROUP BY application_rate");

        $nomination = db::select("SELECT nomination, SUM(COALESCE(fortyhc,0)+COALESCE(forty,0)+COALESCE(twenty,0)) AS qty FROM shipment_reservations
            WHERE `status` = 'BOOKING CONFIRMED'
            AND period = '" . $period . "'
            GROUP BY nomination");

        $response = array(
            'status' => true,
            'data' => $data,
            'ship_by_dates' => $ship_by_dates,
            'teus' => $teus,
            'application_rate' => $application_rate,
            'nomination' => $nomination,
            'period' => $period,
            'month' => $month,
            'mon' => date("n", strtotime($month)),
            'year' => date("Y", strtotime($month)),
        );
        return Response::json($response);
    }

    public function deleteShippingAgency(Request $request)
    {
        $id = $request->get('id');

        try {
            $agency = ShipmentNomination::where('id', $id)->delete();

            $response = array(
                'status' => true,
                'message' => 'Shipment Line Deleted Successfullly',
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

    public function editShippingAgency(Request $request)
    {
        $id = $request->get('id');
        $ship_id = $request->get('ship_id');
        $shipper = $request->get('shipper');
        $port_loading = $request->get('port_loading');
        $consignee = $request->get('consignee');
        $transship_port = $request->get('transship_port');
        $port_of_discharge = $request->get('port_of_discharge');
        $port_of_delivery = $request->get('port_of_delivery');
        $country = $request->get('country');
        $carier = $request->get('carier');
        $nomination = $request->get('nomination');

        try {
            $agency = ShipmentNomination::where('id', $id)
                ->update([
                    'ship_id' => strtoupper($ship_id),
                    'shipper' => strtoupper($shipper),
                    'port_loading' => strtoupper($port_loading),
                    'consignee' => strtoupper($consignee),
                    'transship_port' => strtoupper($transship_port),
                    'port_of_discharge' => strtoupper($port_of_discharge),
                    'port_of_delivery' => strtoupper($port_of_delivery),
                    'country' => strtoupper($country),
                    'carier' => strtoupper($carier),
                    'nomination' => strtoupper($nomination),
                ]);

            $response = array(
                'status' => true,
                'message' => 'Shipment Line Edited Successfullly',
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

    public function addShippingAgency(Request $request)
    {
        $ship_id = $request->get('ship_id');
        $shipper = $request->get('shipper');
        $port_loading = $request->get('port_loading');
        $consignee = $request->get('consignee');
        $transship_port = $request->get('transship_port');
        $port_of_discharge = $request->get('port_of_discharge');
        $port_of_delivery = $request->get('port_of_delivery');
        $country = $request->get('country');
        $carier = $request->get('carier');
        $nomination = $request->get('nomination');

        try {
            $agency = new ShipmentNomination([
                'ship_id' => strtoupper($ship_id),
                'shipper' => strtoupper($shipper),
                'port_loading' => strtoupper($port_loading),
                'consignee' => strtoupper($consignee),
                'transship_port' => strtoupper($transship_port),
                'port_of_discharge' => strtoupper($port_of_discharge),
                'port_of_delivery' => strtoupper($port_of_delivery),
                'country' => strtoupper($country),
                'carier' => strtoupper($carier),
                'nomination' => strtoupper($nomination),
                'created_by' => Auth::id(),
            ]);
            $agency->save();

            $response = array(
                'status' => true,
                'message' => 'Shipment Line Added Successfullly',
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

    public function addShipReservation(Request $request)
    {
        $period = $request->get('period');
        $ycj_ref_number = $request->get('ycj_ref_no');
        $help = $request->get('help');
        $status = $request->get('status');
        $shipper = $request->get('shipper');
        $pol = $request->get('pol');
        $pod = $request->get('pod');
        $bl = $request->get('bl');
        $fortyhc = $request->get('fortyhc');
        $forty = $request->get('forty');
        $twenty = $request->get('twenty');
        $carier = $request->get('carier');
        $stuffing = $request->get('stuffing');
        $etd = $request->get('etd');
        $application_rate = $request->get('application_rate');
        $plan = $request->get('plan');
        $remark = $request->get('remark');
        $due_date = $request->get('due_date');
        $invoice = $request->get('invoice');
        $ref = $request->get('ref');

        $data_pod = explode('-', $pod);
        $port_of_delivery = $data_pod[1];
        $country = $data_pod[0];

        $data_carier = explode('-', $carier);
        $carier = $data_carier[0];
        $nomination = $data_carier[1];

        try {
            $reservation = new ShipmentReservation([
                'period' => $period,
                'ycj_ref_number' => strtoupper($ycj_ref_number),
                'help' => $help,
                'status' => $status,
                'shipper' => $shipper,
                'port_loading' => $pol,
                'port_of_delivery' => $port_of_delivery,
                'country' => $country,
                'carier' => $carier,
                'nomination' => $nomination,
                'fortyhc' => $fortyhc,
                'forty' => $forty,
                'twenty' => $twenty,
                'booking_number' => $bl,
                'stuffing_date' => $stuffing,
                'etd_date' => $etd,
                'application_rate' => $application_rate,
                'plan' => $plan,
                'remark' => $remark,
                'due_date' => $due_date,
                'invoice_number' => strtoupper($invoice),
                'ref' => $ref,
                'created_by' => Auth::id(),
            ]);
            $reservation->save();

            $response = array(
                'status' => true,
                'message' => 'Shipment Reservation Added Successfullly',
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

    public function deleteShipReservation(Request $request)
    {
        $id = $request->get('shipment_reservation_id');

        try {
            $delete = ShipmentReservation::where('id', $id)->delete();

            $response = array(
                'status' => true,
                'message' => 'Shipment Reservation Deleted Successfullly',
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

    public function editShipReservation(Request $request)
    {
        $id = $request->get('shipment_reservation_id');
        $period = $request->get('period');
        $ycj_ref_number = $request->get('ycj_ref_no');
        $help = $request->get('help');
        $status = $request->get('status');
        $bl = $request->get('bl');
        $fortyhc = $request->get('fortyhc');
        $forty = $request->get('forty');
        $twenty = $request->get('twenty');
        $stuffing = $request->get('stuffing');
        $etd = $request->get('etd');
        $plan_teus = $request->get('plan_teus');
        $plan = $request->get('plan');
        $remark = $request->get('remark');
        $application_rate = $request->get('application_rate');
        $due_date = $request->get('due_date');
        $invoice = $request->get('invoice');
        $ref = $request->get('ref');

        try {
            $update = ShipmentReservation::where('id', $id)
                ->update([
                    'ycj_ref_number' => $ycj_ref_number,
                    'help' => $help,
                    'status' => $status,
                    'fortyhc' => $fortyhc,
                    'forty' => $forty,
                    'twenty' => $twenty,
                    'booking_number' => $bl,
                    'stuffing_date' => $stuffing,
                    'etd_date' => $etd,
                    'application_rate' => $application_rate,
                    'remark' => $remark,
                    'due_date' => $due_date,
                    'invoice_number' => strtoupper($invoice),
                    'ref' => $ref,
                ]);

            $update_ref_number = ShipmentReservation::where('ycj_ref_number', $ycj_ref_number)
                ->where('period', $period)
                ->update([
                    'stuffing_date' => $stuffing,
                    'etd_date' => $etd,
                    'plan_teus' => $plan_teus,
                    'plan' => $plan,
                    'remark' => $remark,
                    'invoice_number' => strtoupper($invoice),
                    'ref' => $ref,
                ]);

            $response = array(
                'status' => true,
                'message' => 'Shipment Reservation Edited Successfullly',
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

    public function uploadShipReservation(Request $request)
    {
        if ($request->hasFile('upload_file')) {
            try {
                $file = $request->file('upload_file');
                $file_name = 'weekly_shipment_' . '(' . $request->get('upload_period') . ')' . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/shipment/'), $file_name);

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

        $excel = public_path('uploads/shipment/') . $file_name;
        $rows = Excel::load($excel, function ($reader) {
            $reader->noHeading();
            $reader->skipRows(1);
        })->get();
        $rows = $rows->toArray();

        DB::beginTransaction();
        $period = $request->get('upload_period');

        $checkTemp = ShipmentReservationTemp::where('period', $period)->get();
        if (count($checkTemp) > 0) {
            $checkTemp = ShipmentReservationTemp::where('period', $period)->delete();
        }

        for ($i = 0; $i < count($rows); $i++) {
            $stuffing = $rows[$i][0];
            $bl_date = $rows[$i][1];
            $destination = $rows[$i][2];
            $transportation = $rows[$i][3];

            if (!is_string($stuffing)) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => "Change Format Cell in Ms Excel to Text. Then Upload Excel File Again",
                );
                return Response::json($response);
            }

            if (!is_string($bl_date)) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => "Change Format Cell in Ms Excel to Text. Then Upload Excel File Again",
                );
                return Response::json($response);
            }

            if ($transportation == 'SEA') {
                $destinations = ShipmentNomination::where('consignee', 'like', '%' . $destination . '%')->first();

                if ($destinations) {
                    try {
                        $temp = new ShipmentReservationTemp([
                            'period' => $period,
                            'stuffing' => $stuffing,
                            'bl_date' => $bl_date,
                            'port_of_delivery' => $destinations->port_of_delivery,
                            'country' => $destinations->country,
                            'created_by' => Auth::id(),
                        ]);
                        $temp->save();

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
                        'message' => "Destination Not Found",
                    );
                    return Response::json($response);
                }
            }
        }

        $checkReservation = ShipmentReservation::where('period', $period)->get();
        if (count($checkTemp) > 0) {
            $checkReservation = ShipmentReservation::where('period', $period)->delete();
        }

        $reservations = ShipmentReservationTemp::where('period', $period)
            ->select('stuffing', 'bl_date', 'port_of_delivery', 'country')
            ->distinct()
            ->get();

        for ($i = 0; $i < count($reservations); $i++) {
            $nominations = ShipmentNomination::where('port_of_delivery', $reservations[$i]->port_of_delivery)
                ->where('country', $reservations[$i]->country)
                ->get();

            $index = $i + 1;

            for ($j = 0; $j < count($nominations); $j++) {

                try {
                    $reservation = new ShipmentReservation([
                        'period' => $period,
                        'ycj_ref_number' => 'YMPI' . sprintf("%'.0" . 3 . "d", $index),
                        'help' => 'NO',
                        'status' => 'OTHER',
                        'shipper' => 'YMPI',
                        'port_loading' => 'SURABAYA',
                        'port_of_delivery' => $nominations[$j]->port_of_delivery,
                        'country' => $nominations[$j]->country,
                        'carier' => $nominations[$j]->carier,
                        'nomination' => $nominations[$j]->nomination,
                        'stuffing_date' => $reservations[$i]->stuffing,
                        'etd_date' => $reservations[$i]->bl_date,
                        'created_by' => Auth::id(),
                    ]);
                    $reservation->save();
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

        DB::commit();
        $response = array(
            'status' => true,
            'message' => 'Upload shipment reservation success',
        );
        return Response::json($response);
    }

    public function indexContainerAttachment()
    {
        return view('container_schedules.attachment')->with('page', 'Container Attachment');
    }

    public function index()
    {
        $container_schedules = ContainerSchedule::OrderBy('id', 'asc')
            ->get();

        $tes = DB::table('container_schedules')->get();

        return view('container_schedules.index', array(
            'container_schedules' => $container_schedules,
        ))->with('page', 'Container Schedule');
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $containers = Container::orderBy('container_code', 'ASC')->get();
        $destinations = Destination::orderBy('destination_code', 'ASC')->get();
        return view('container_schedules.create', array(
            'destinations' => $destinations,
            'containers' => $containers,
        ))->with('page', 'Container Schedule');
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try
        {
            $code_generator = CodeGenerator::where('note', '=', 'container')->first();
            $prefix_now = date("Y") . date("m");

            if ($prefix_now != $code_generator->prefix) {
                $code_generator->prefix = $prefix_now;
                $code_generator->index = '0';
                $code_generator->save();
            }

            $number = sprintf("%'.0" . $code_generator->length . "d\n", $code_generator->index);
            $container_id = $code_generator->prefix . $number + 1;

            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $id = Auth::id();
            $container_schedule = new ContainerSchedule([
                'container_id' => $container_id,
                'container_code' => $request->get('container_code'),
                'destination_code' => $request->get('destination_code'),
                'shipment_date' => date('Y-m-d', strtotime(str_replace('/', '-', $request->get('shipment_date')))),
                'created_by' => $id,
            ]);

            $container_schedule->save();
            return redirect('/index/container_schedule')->with('status', 'New container schedule has been created.')->with('page', 'Container Schedule');
        } catch (QueryException $e) {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062) {
                return back()->with('error', 'Container ID from system is invalid.')->with('page', 'Container Schedule');
            } else {
                return back()->with('error', $e->getMessage())->with('page', 'Container Schedule');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $container_schedule = ContainerSchedule::find($id);
        return view('container_schedules.show', array(
            'container_schedule' => $container_schedule,
        ))->with('page', 'Container Schedule');
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $containers = Container::orderBy('container_code', 'ASC')->get();
        $destinations = Destination::orderBy('destination_code', 'ASC')->get();
        $container_schedule = ContainerSchedule::find($id);
        return view('container_schedules.edit', array(
            'container_schedule' => $container_schedule,
            'containers' => $containers,
            'destinations' => $destinations,
        ))->with('page', 'Container Schedule');
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {

            $container_schedule = ContainerSchedule::find($id);
            $container_schedule->container_code = $request->get('container_code');
            $container_schedule->destination_code = $request->get('destination_code');
            $container_schedule->shipment_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->get('shipment_date'))));
            $container_schedule->save();

            return redirect('/index/container_schedule')->with('status', 'Container schedule data has been edited.')->with('page', 'Container Schedule');

        } catch (QueryException $e) {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062) {
                return back()->with('error', 'Container schedule with preferred destination and shipment date already exist.')->with('page', 'Container Schedule');
            } else {
                return back()->with('error', $e->getMessage())->with('page', 'Container Schedule');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $container_schedule = ContainerSchedule::find($id);
        $container_schedule->forceDelete();

        return redirect('/index/container_schedule')
            ->with('status', 'Container Schedule has been deleted.')
            ->with('page', 'Container Schedule');
        //
    }

    /**
     * Import resource from Text File.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $code_generator2 = CodeGenerator::where('note', '=', 'container')->first();
        $prefix_now = date("Y") . date("m");

        if ($prefix_now != $code_generator2->prefix) {
            $code_generator2->prefix = $prefix_now;
            $code_generator2->index = '0';
            $code_generator2->save();
        }

        try {
            if ($request->hasFile('container_schedule')) {
                // ContainerSchedule::truncate();

                $id = Auth::id();

                $file = $request->file('container_schedule');
                $data = file_get_contents($file);

                $rows = explode("\r\n", $data);
                foreach ($rows as $row) {
                    if (strlen($row) > 0) {

                        $code_generator = CodeGenerator::where('note', '=', 'container')->first();

                        $number = sprintf("%'.0" . $code_generator->length . "d\n", $code_generator->index);
                        $container_id = $code_generator->prefix . $number + 1;

                        $code_generator->index = $code_generator->index + 1;
                        $code_generator->save();

                        $row = explode("\t", $row);
                        $container_schedule = new ContainerSchedule([
                            'container_id' => $container_id,
                            'container_code' => $row[0],
                            'destination_code' => $row[1],
                            'shipment_date' => date('Y-m-d', strtotime(str_replace('/', '-', $row[2]))),
                            'created_by' => $id,
                        ]);

                        $container_schedule->save();
                    }
                }
                return redirect('/index/container_schedule')->with('status', 'New container schedule has been imported.')->with('page', 'Container Schedule');

            } else {
                return redirect('/index/container_schedule')->with('error', 'Please select a file.')->with('page', 'Container Schedule');
            }
        } catch (QueryException $e) {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062) {
                // self::delete($lid);
                return back()->with('error', 'Container with preferred destination and shipment date already exist.')->with('page', 'Container Schedule');
            } else {
                return back()->with('error', $e->getMessage())->with('page', 'Container Schedule');
            }
        }
        //
    }
}
