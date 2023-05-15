<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\MaterialListByModel;
use App\MaterialUsage;
use App\RawMaterialStock;
use App\StocktakingCalendar;
use App\StocktakingLocationStock;
use App\StocktakingMaterialForecast;
use App\StorageLocationStock;
use Carbon\Carbon;
use DataTables;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

class RawMaterialController extends Controller
{

    private $storage_location;

    public function __construct()
    {

        $this->middleware('auth');
        $this->storage_location = [
            '203',
            '208',
            '214',
            '216',
            '217',
            '218',
            '401',
            'CL21',
            'CL51',
            'CL61',
            'CL91',
            'CLA0',
            'CLA2',
            'CLB9',
            'CS91',
            'FA0R',
            'FA1R',
            'FL21',
            'FL51',
            'FL61',
            'FL91',
            'FLA0',
            'FLA1',
            'FLA2',
            'FLT9',
            'FSTK',
            'LA0R',
            'MINS',
            'MMJR',
            'MNCF',
            'MS11',
            'MSCR',
            'MSTK',
            'OTHR',
            'PN91',
            'PNR4',
            'PNR9',
            'RC11',
            'RC91',
            'SA0R',
            'SA1R',
            'SX21',
            'SX51',
            'SX61',
            'SX91',
            'SXA0',
            'SXA1',
            'SXA2',
            'SXBR',
            'SXT9',
            'SXWH',
            'VA0R',
            'VN11',
            'VN21',
            'VN51',
            'VN91',
            'VNA0',
            'WCL',
            'WCS',
            'WFL',
            'WFTP',
            'WHST',
            'WLST',
            'WPCS',
            'WPN',
            'WPPN',
            'WPRC',
            'WPRS',
            'WRC',
            'WSCR',
            'WSTP',
            'WSX',
            'YCJP',
            'YCJR',
            'ZPA0',
        ];
    }

    public function indexRawMaterialDashboard(Request $request)
    {
        $title = "Raw Material Dashboard";
        $title_jp = "素材有無";

        return view('raw_materials.dashboard', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('head', 'Raw Material Availability');

    }

    public function indexAvailability()
    {
        $title = "Raw Material Availability";
        $title_jp = "素材有無";

        return view('raw_materials.material_availability', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('head', 'Raw Material Availability');
    }

    public function indexShortage()
    {
        $title = "Shortage of Materials Availability";
        $title_jp = "材料不足";

        return view('raw_materials.material_shortage', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('head', 'Raw Material Availability');
    }

    public function fetchAvailability(Request $request)
    {

        $now = date('Y-m-d');

        if ($request->get('date')) {
            $now = date('Y-m-d', strtotime($request->get('date')));
        }

        $date = strtotime('+1 month', strtotime($now));

        $first = date('Y-m-01', strtotime($now));
        $last = date('Y-m-d', $date);

        $int_date = intval(date('d', strtotime($now)));
        $policy_period = date('Y-m-01', strtotime($now));
        if ($int_date >= 23) {
            $policy_period = date('Y-m-01', strtotime('+14 day', strtotime($now)));
        }

        // $now = '2022-03-17';
        // $first = '2022-03-01';
        // $last = '2022-03-31';
        // $policy_period = '2022-03-01';

        // $material_controls = db::select("SELECT
        //     mc.material_number,
        //     UPPER( mc.material_description ) AS material_description,
        //     mc.controlling_group,
        //     mc.vendor_code,
        //     mc.vendor_name,
        //     mc.category,
        //     IF(mc.lead_time > 0, mc.lead_time, 60) as lead_time,
        //     es.NAME AS buyer
        //     FROM
        //     material_controls AS mc
        //     LEFT JOIN employee_syncs AS es ON es.employee_id = mc.pic
        //     WHERE mc.lead_time > 0
        //     ORDER BY
        //     controlling_group ASC,
        //     lead_time ASC");

        $reasons = db::select("SELECT
			*
			FROM
			material_availability_reasons
			WHERE
			due_date = '" . $now . "'
			AND reason is not null");

        $material_controls = db::select("SELECT
			mc.material_number,
			UPPER( mc.material_description ) AS material_description,
			mc.controlling_group,
			mc.material_category,
			mc.minimum_order,
            mpdl.standard_price,
			mc.vendor_code,
			mc.vendor_name,
			mc.remark,
			mc.category,
			IF(mc.lead_time > 0, mc.lead_time, 60) as lead_time,
			es.NAME AS buyer
			FROM
			material_controls AS mc
			LEFT JOIN employee_syncs AS es ON es.employee_id = mc.pic
			LEFT JOIN material_plant_data_lists AS mpdl ON mpdl.material_number = mc.material_number
			WHERE mc.lead_time > 0
			ORDER BY
			mc.controlling_group ASC,
			mc.material_category ASC");

        $usages = db::select("SELECT
			mrp.material_number,
			round( AVG( mrp.USAGE ), 2 ) AS policy
			FROM
			material_requirement_plans AS mrp
			WHERE
			mrp.due_date >= '" . $now . "'
			AND mrp.due_date <= '" . $last . "'
			GROUP BY
			mrp.material_number");

        if ($now == date('Y-m-d')) {
            $stocks = db::connection('ymes')
                ->select("
                    SELECT item_code AS material_number,
                        SUM ( stockqty + inspect_qty ) AS stock,
                        SUM (  CASE WHEN location_code IN ( 'MSTK', 'WPCS', 'WPRC', 'WPPN' ) THEN ( stockqty + inspect_qty ) ELSE 0 END ) AS stock_wh,
                        SUM (  CASE WHEN location_code NOT IN ( 'SXWH', 'WPCS', 'WPPN', 'WPRC', 'MSTK', 'MSCR', 'OTHR', 'WSCR', 'MINS', 'MNCF', 'WHRP' ) THEN ( stockqty + inspect_qty ) ELSE 0 END ) AS stock_wip
                    FROM
                        vd_mes0010
                    GROUP BY
                        item_code");

        } else {
            $stocks = db::table('storage_location_stocks')
                ->where('stock_date', $now)
                ->groupBy('material_number')
                ->select(
                    'material_number',
                    db::raw("sum(unrestricted + inspection) AS stock"),
                    db::raw("sum(IF(storage_location IN ( 'MSTK', 'WPCS', 'WPRC', 'WPPN' ), (unrestricted + inspection), 0)) as stock_wh"),
                    db::raw("sum(IF(storage_location NOT IN ( 'SXWH', 'WPCS', 'WPPN', 'WPRC', 'MSTK', 'MSCR', 'OTHR', 'WSCR', 'MINS', 'MNCF', 'WHRP' ), (unrestricted + inspection), 0)) as stock_wip")
                )
                ->get();

        }

        $deliveries = db::select("SELECT
			mpd.material_number,
			GROUP_CONCAT(
				CONCAT( '(', mpd.eta_date, ' = ', mpd.quantity, ')' )) AS plan_deliveries
			FROM
			material_plan_deliveries AS mpd
			WHERE
			mpd.deleted_at IS NULL
			AND mpd.eta_date >= '" . $now . "'
			GROUP BY
			mpd.material_number");

        $policies = db::select("SELECT
			material_number,
            day,
			policy
			FROM
			material_stock_policies
			WHERE
			period = '" . $policy_period . "'");

        $result = array();

        foreach ($material_controls as $material_control) {
            $material_number = $material_control->material_number;
            $material_description = $material_control->material_description;
            $controlling_group = $material_control->controlling_group;
            $material_category = $material_control->material_category;
            $remark = $material_control->remark;
            $vendor_code = $material_control->vendor_code;
            $vendor_name = $material_control->vendor_name;
            $minimum_order = '-';
            if ($material_control->minimum_order != null) {
                $minimum_order = $material_control->minimum_order;
            }
            $category = $material_control->category;
            $standard_price = round($material_control->standard_price / 1000, 4);
            $lead_time = $material_control->lead_time;
            $buyer = $material_control->buyer;
            $stock_wh = 0;
            $stock_wip = 0;
            $plan_delivery = "";
            $policy = 0;
            $stock_policy = 0;
            $stock_policy_day = 0;
            $stock_condition = 1;
            $availability = 0;
            $availability_days = 0;

            foreach ($policies as $row) {
                if ($row->material_number == $material_control->material_number) {
                    $stock_policy = $row->policy;
                    $stock_policy_day = $row->day;
                    break;
                }
            }

            foreach ($deliveries as $delivery) {
                if ($delivery->material_number == $material_control->material_number) {
                    $plan_delivery = $delivery->plan_deliveries;
                    break;
                }
            }

            foreach ($stocks as $stock_row) {
                if ($stock_row->material_number == $material_control->material_number) {
                    $stock_wh = $stock_row->stock_wh;
                    $stock_wip = $stock_row->stock_wip;
                    break;
                }
            }

            foreach ($usages as $usage) {
                if ($usage->material_number == $material_control->material_number) {
                    $policy = $usage->policy;
                    break;
                }
            }

            if ($stock_wh + $stock_wip > 0 && $stock_policy > 0) {
                $stock_condition = round(($stock_wh + $stock_wip) / $stock_policy, 2);
            } else if ($stock_wh + $stock_wip <= 0 && $stock_policy > 0) {
                $stock_condition = 0;
            }

            if ($stock_wh + $stock_wip > 0 && $policy > 0) {
                $availability_days = round(($stock_wh + $stock_wip) / $policy, 1);
            } else {
                $availability_days = 0;
            }

            if ($stock_condition <= 0) {
                $categories_name = '0%';
            } else if ($stock_condition < 0.3) {
                $categories_name = '< 30%';
            } else if ($stock_condition < 0.7) {
                $categories_name = '< 70%';
            } else if ($stock_condition < 1) {
                $categories_name = '< 100%';
            } else if ($stock_condition > 6) {
                $categories_name = '> 600%';
            } else if ($stock_condition > 5) {
                $categories_name = '> 500%';
            } else if ($stock_condition > 4) {
                $categories_name = '> 400%';
            } else if ($stock_condition > 3) {
                $categories_name = '> 300%';
            } else if ($stock_condition > 2) {
                $categories_name = '> 200%';
            } else if ($stock_condition >= 1) {
                $categories_name = '> 100%';
            }

            if (($stock_policy != 0 && $stock_wh != 0) || ($stock_policy != 0 && $stock_wh == 0)) {
                array_push($result, [
                    'material_number' => $material_number,
                    'material_description' => $material_description,
                    'controlling_group' => $controlling_group,
                    'remark' => $remark,
                    'material_category' => $material_category,
                    'vendor_code' => $vendor_code,
                    'vendor_name' => $vendor_name,
                    'category' => $category,
                    'standard_price' => $standard_price,
                    'lead_time' => $lead_time,
                    'minimum_order' => $minimum_order,
                    'buyer' => explode(' ', $buyer)[0],
                    'stock_wh' => $stock_wh,
                    'stock_wip' => $stock_wip,
                    'delivery' => $plan_delivery,
                    'policy' => $policy,
                    'policy_day' => $stock_policy_day,
                    'stock_policy' => ceil($stock_policy),
                    'stock_condition' => $stock_condition,
                    'availability_days' => $availability_days,
                    'categories_name' => $categories_name,
                    'over_quantity' => round(($stock_wh + $stock_wip - $stock_policy), 1),
                    'over_amount' => round($standard_price * ($stock_wh + $stock_wip - $stock_policy), 1),
                ]);
            }
        }

        $controlling_groups = array();
        $material_categories = array();
        $buyers = array();
        $categories_names = ['0%', '< 30%', '< 70%', '< 100%', '> 100%', '> 200%', '> 300%', '> 400%', '> 500%', '> 600%'];

        foreach ($result as $row) {
            if (!in_array($row['controlling_group'], $controlling_groups)) {
                array_push($controlling_groups, $row['controlling_group']);
            }
            if (!in_array($row['material_category'], $material_categories)) {
                array_push($material_categories, $row['material_category']);
            }
            if (!in_array($row['buyer'], $buyers)) {
                array_push($buyers, $row['buyer']);
            }
        }

        $masters = array();

        foreach ($controlling_groups as $controlling_group) {
            foreach ($material_categories as $material_category) {
                foreach ($buyers as $buyer) {
                    foreach ($categories_names as $categories_name) {
                        array_push($masters, [
                            'controlling_group' => $controlling_group,
                            'material_category' => $material_category,
                            'buyer' => $buyer,
                            'categories_name' => $categories_name,
                        ]);
                    }
                }
            }
        }

        $response = array(
            'status' => true,
            'material_controls' => $material_controls,
            'usages' => $usages,
            'stocks' => $stocks,
            'charts' => $result,
            'masters' => $masters,
            'now' => date('d F Y', strtotime($now)),
            'reasons' => $reasons,
        );
        return Response::json($response);

    }

    public function fetchShortage(Request $request)
    {

        $now = date('Y-m-d');
        $first = date('Y-m-01', strtotime($now));
        $date = date('Y-m-d');
        if ($request->get('date') != "") {
            $date = date('Y-m-d', strtotime($request->get('date')));
        }

        $employees = db::table('employee_syncs')
            ->where('division', 'LIKE', '%Production Support%')
            ->get();
        $employees = $employees->keyBy('employee_id');

        $mpdl = db::table('material_plant_data_lists')->get();
        $mpdl = $mpdl->keyBy('material_number');

        if ($date == $now) {
            $stocks = db::connection('ymes')
                ->select("
                    SELECT item_code AS material_number,
                        SUM ( stockqty + inspect_qty ) AS stock,
                        SUM (  CASE WHEN location_code IN ( 'MSTK', 'WPCS', 'WPRC', 'WPPN' ) THEN ( stockqty + inspect_qty ) ELSE 0 END ) AS stock_wh,
                        SUM (  CASE WHEN location_code NOT IN ( 'SXWH', 'WPCS', 'WPPN', 'WPRC', 'MSTK', 'MSCR', 'OTHR', 'WSCR', 'MINS', 'MNCF', 'WHRP' ) THEN ( stockqty + inspect_qty ) ELSE 0 END ) AS stock_wip
                    FROM
                        vd_mes0010
                    GROUP BY
                        item_code");

            $temp = [];
            for ($i = 0; $i < count($stocks); $i++) {
                $temp[$stocks[$i]->material_number] = $stocks[$i];
            }
            $stocks = $temp;

        } else {
            $stocks = db::table('storage_location_stocks')
                ->where('stock_date', $date)
                ->groupBy('material_number')
                ->select(
                    'material_number',
                    db::raw("sum(unrestricted + inspection) AS stock"),
                    db::raw("sum(IF(storage_location IN ( 'MSTK', 'WPCS', 'WPRC', 'WPPN' ), (unrestricted + inspection), 0)) as stock_wh"),
                    db::raw("sum(IF(storage_location NOT IN ( 'SXWH', 'WPCS', 'WPPN', 'WPRC', 'MSTK', 'MSCR', 'OTHR', 'WSCR', 'MINS', 'MNCF', 'WHRP' ), (unrestricted + inspection), 0)) as stock_wip")
                )
                ->get();
            $stocks = $stocks->keyBy('material_number');

        }

        $usages = db::table('material_requirement_plans')
            ->where('due_date', '>=', $first)
            ->where('due_date', '<=', $now)
            ->select(
                'material_number',
                db::raw("AVG(`usage`) as `usage`")
            )
            ->groupBy('material_number')
            ->get();
        $usages = $usages->keyBy('material_number');

        $plan_deliveries = db::table('material_plan_deliveries')
            ->where('due_date', '>=', $date)
            ->get();

        $int_date = intval(date('d', strtotime($date)));
        $policy_period = date('Y-m-01', strtotime($date));
        if ($int_date >= 23) {
            $policy_period = date('Y-m-01', strtotime('+14 day', strtotime($date)));
        }

        $policies = db::table('material_stock_policies')
            ->where('period', $policy_period)
            ->get();
        $policies = $policies->keyBy('material_number');

        $buyers = [];

        $filtered_material = [];
        $material_controls = db::table('material_controls')->get();
        for ($i = 0; $i < count($material_controls); $i++) {
            if (isset($policies[$material_controls[$i]->material_number]) && $policies[$material_controls[$i]->material_number]->policy > 0) {

                (isset($stocks[$material_controls[$i]->material_number])) ? $stock = $stocks[$material_controls[$i]->material_number]->stock : $stock = 0;
                (isset($stocks[$material_controls[$i]->material_number])) ? $stock_wh = $stocks[$material_controls[$i]->material_number]->stock_wh : $stock_wh = 0;
                (isset($stocks[$material_controls[$i]->material_number])) ? $stock_wip = $stocks[$material_controls[$i]->material_number]->stock_wip : $stock_wip = 0;

                if (isset($usages[$material_controls[$i]->material_number]) && $usages[$material_controls[$i]->material_number]->usage > 0) {
                    $availability_days = round($stock / $usages[$material_controls[$i]->material_number]->usage, 1);
                    $avg_usage = round($usages[$material_controls[$i]->material_number]->usage, 1);
                } else {
                    $availability_days = 0;
                    $avg_usage = 0;
                }

                $this_plan_delivery = [];
                for ($j = 0; $j < count($plan_deliveries); $j++) {
                    if ($plan_deliveries[$j]->material_number == $material_controls[$i]->material_number) {
                        $this_plan_delivery[] = $plan_deliveries[$j];
                    }
                }

                $stock_condition = $stock / $policies[$material_controls[$i]->material_number]->policy;
                if ($stock_condition < 1) {
                    $row = array();
                    $row['material_number'] = $material_controls[$i]->material_number;
                    $row['material_description'] = $material_controls[$i]->material_description;
                    $row['controlling_group'] = $material_controls[$i]->controlling_group;
                    $row['vendor_code'] = $material_controls[$i]->vendor_code;
                    $row['vendor_name'] = $material_controls[$i]->vendor_name;
                    $row['bun'] = $mpdl[$material_controls[$i]->material_number]->bun;
                    $row['nickname'] = $material_controls[$i]->remark;
                    $row['buyer_id'] = $material_controls[$i]->pic;
                    $row['buyer'] = $this->generateName($employees[$material_controls[$i]->pic]->name);
                    $row['pch_control_id'] = $material_controls[$i]->control;
                    $row['pch_control'] = $this->generateName($employees[$material_controls[$i]->control]->name);
                    $row['stock'] = (double) $stock;
                    $row['stock_wh'] = (double) $stock_wh;
                    $row['stock_wip'] = (double) $stock_wip;
                    $row['minimum_order'] = $material_controls[$i]->minimum_order;
                    $row['policy_day'] = (double) $policies[$material_controls[$i]->material_number]->day;
                    $row['policy'] = (double) $policies[$material_controls[$i]->material_number]->policy;
                    $row['stock_condition'] = round($stock_condition * 100);
                    $row['avg_usage'] = $avg_usage;
                    $row['availability_days'] = $availability_days;
                    $row['plan_deliveries'] = $this_plan_delivery;

                    $filtered_material[] = (object) $row;

                }
            }

            if (!array_key_exists($material_controls[$i]->pic, $buyers)) {
                $row = array();
                $row['employee_id'] = $material_controls[$i]->pic;
                $row['name'] = $this->generateName($employees[$material_controls[$i]->pic]->name);
                $buyers[$material_controls[$i]->pic] = (object) $row;

            }
        }

        usort($filtered_material, function ($a, $b) {return $a->stock_condition - $b->stock_condition;});

        $response = array(
            'status' => true,
            'now' => date('d F Y', strtotime($date)),
            'materials' => $filtered_material,
            'buyers' => $buyers,
        );
        return Response::json($response);

    }

    public function updateAvailabilityReason(Request $request)
    {
        $date = date('Y-m-d');
        if ($request->get('date') != "") {
            $date = date('Y-m-d', strtotime($request->get('date')));
        }

        $reason = db::table('material_availability_reasons')
            ->where('due_date', '=', $date)
            ->where('controlling_group', '=', explode('_', $request->get('id'))[0])
            ->where('material_category', '=', explode('_', $request->get('id'))[1])
            ->first();

        if ($reason) {
            if ($reason->reason == $request->get('text')) {
                $response = array(
                    'status' => false,
                    'message' => 'Tidak ada perubahan',
                );
                return Response::json($response);
            }

            $update = db::table('material_availability_reasons')
                ->where('due_date', '=', $date)
                ->where('controlling_group', '=', explode('_', $request->get('id'))[0])
                ->where('material_category', '=', explode('_', $request->get('id'))[1])
                ->update([
                    'reason' => $request->get('text'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        } else {
            $insert = db::table('material_availability_reasons')
                ->insert([
                    'due_date' => $date,
                    'controlling_group' => explode('_', $request->get('id'))[0],
                    'material_category' => explode('_', $request->get('id'))[1],
                    'reason' => $request->get('text'),
                    'created_by' => Auth::id(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
        }

        $response = array(
            'status' => true,
            'message' => 'Reason berhasil disimpan',
        );
        return Response::json($response);
    }

    public function rawMaterialMonitoringIndex()
    {
        $title = "Raw Material Monitoring";
        $title_jp = "素材監視";

        return view('materials.material_monitoring_index', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('head', 'Raw Material Monitoring');
    }

    public function indexUsage()
    {
        $storage_locations = $this->storage_location;
        return view('raw_materials.material_usage', array(
            'storage_locations' => $storage_locations,
        ))->with('page', 'Material Usage')->with('head', 'Raw Material Monitoring');
    }

    public function indexStorage()
    {
        $storage_locations = $this->storage_location;
        return view('raw_materials.storage_location_stock', array(
            'storage_locations' => $storage_locations,
        ))->with('page', 'Upload Storage')->with('head', 'Raw Material Monitoring');
    }

    public function indexSmbmr()
    {
        return view('raw_materials.material_list_by_model')->with('page', 'Upload SMBMR')->with('head', 'Raw Material Monitoring');
    }

    public function fetchStorage(Request $request)
    {
        $storage_location_stocks = StorageLocationStock::orderBy('storage_location_stocks.material_number', 'asc');

        if (strlen($request->get('dateFrom')) > 0) {
            $date_from = date('Y-m-d', strtotime($request->get('dateFrom')));
            $storage_location_stocks = $storage_location_stocks->where('storage_location_stocks.stock_date', '>=', $date_from);
        }

        if (strlen($request->get('dateTo')) > 0) {
            $date_to = date('Y-m-d', strtotime($request->get('dateTo')));
            $storage_location_stocks = $storage_location_stocks->where('storage_location_stocks.stock_date', '<=', $date_to);
        }

        if ($request->get('storage_location') != null) {
            $storage_location_stocks = $storage_location_stocks->whereIn('storage_location_stocks.storage_location', $request->get('storage_location'));
        }

        if (strlen($request->get('dateFrom')) <= 0 && strlen($request->get('dateTo')) <= 0 && $request->get('storage_location') == null) {
            $storage_location_stocks = $storage_location_stocks->where('storage_location_stocks.stock_date', '=', date("Y-m-d"));
        }

        $storage_location_stocks = $storage_location_stocks->select('storage_location_stocks.material_number', 'storage_location_stocks.material_description', 'storage_location_stocks.storage_location', 'storage_location_stocks.unrestricted', 'storage_location_stocks.download_date', 'storage_location_stocks.download_time', 'storage_location_stocks.stock_date')
            ->get();

        return DataTables::of($storage_location_stocks)->make(true);
    }

    public function fetchSmbmr(Request $request)
    {
        $material_list_by_models = MaterialListByModel::orderBy('material_list_by_models.material_parent', 'asc');

        if (strlen($request->get('material_parent')) > 0) {
            $material_parent = explode(",", $request->get('material_parent'));
            $material_list_by_models = $material_list_by_models->whereIn('material_list_by_models.material_parent', $material_parent);
        }

        if (strlen($request->get('material_child')) > 0) {
            $material_child = explode(",", $request->get('material_child'));
            $material_list_by_models = $material_list_by_models->whereIn('material_list_by_models.material_child', $material_child);
        }

        if (strlen($request->get('vendor')) > 0) {
            $vendor = explode(",", $request->get('vendor'));
            $material_list_by_models = $material_list_by_models->whereIn('material_list_by_models.vendor', $vendor);
        }

        $material_list_by_models = $material_list_by_models->select('material_list_by_models.material_parent', 'material_list_by_models.material_parent_description', 'material_list_by_models.material_child', 'material_list_by_models.material_child_description', 'material_list_by_models.uom', 'material_list_by_models.purg', 'material_list_by_models.usage', 'material_list_by_models.vendor')
            ->get();

        return DataTables::of($material_list_by_models)->make(true);
    }

    public function fetchUsage(Request $request)
    {
        $material_usages = MaterialUsage::orderBy('material_usages.material_number', 'asc');

        if (strlen($request->get('material_number')) > 0) {
            $material_number = explode(",", $request->get('material_number'));
            $material_usages = $material_usages->whereIn('material_usages.material_number', $material_number);
        }

        if (strlen($request->get('dateFrom')) > 0) {
            $date_from = date('Y-m-d', strtotime($request->get('dateFrom')));
            $material_usages = $material_usages->where('material_usages.due_date', '>=', $date_from);
        }

        if (strlen($request->get('dateTo')) > 0) {
            $date_to = date('Y-m-d', strtotime($request->get('dateTo')));
            $material_usages = $material_usages->where('material_usages.due_date', '<=', $date_to);
        }

        $material_usages = $material_usages->select('material_usages.material_number', 'material_usages.material_description', 'material_usages.due_date', 'material_usages.usage')
            ->get();

        return DataTables::of($material_usages)->make(true);
    }

    public function calculateUsage(Request $request)
    {
        if (strlen($request->get('validMonth')) > 0) {
            try {
                $delete_usage = MaterialUsage::where(db::raw('date_format(material_usages.due_date, "%m-%Y")'), '=', $request->get('validMonth'))->forceDelete();
                $id = Auth::id();
                $now = date("Y-m-d H:i:s");

                DB::insert("insert into material_usages(material_number, material_description, due_date, `usage`, created_by, created_at, updated_at)
					select material_child as material_number, material_child_description as material_description, due_date, round(sum(`usage`),6) as `usage`, '" . $id . "' as created_by, '" . $now . "' as created_at, '" . $now . "' as updated_at from
					(
					select material_list_by_models.material_child, material_list_by_models.material_child_description, production_schedules.due_date, round(production_schedules.quantity*material_list_by_models.`usage`, 6) as `usage` from production_schedules inner join material_list_by_models on production_schedules.material_number = material_list_by_models.material_parent where date_format(production_schedules.due_date, '%m-%Y') = '" . $request->get('validMonth') . "'
				) as materials group by material_child, material_child_description, due_date");

                return redirect('/index/material/usage')->with('success', 'Material usage have been calculated')->with('page', 'Material Usage')->with('head', 'Raw Material Monitoring');
            } catch (\Exception$e) {
                return redirect('/index/material/usage')->with('error', $e->getMessage())->with('page', 'Material Usage')->with('head', 'Raw Material Monitoring');
            }
        } else {
            return redirect('/index/material/usage')->with('error', 'Please select a Month')->with('page', 'Material Usage')->with('head', 'Raw Material Monitoring');
        }
    }

    public function importStorage(Request $request)
    {
        if ($request->hasFile('storage_location_stock') && strlen($request->get('date_stock')) > 0) {
            try {
                $stock_date = date('Y-m-d', strtotime($request->get('date_stock')));
                $delete_storage = StorageLocationStock::where('storage_location_stocks.stock_date', '=', $stock_date)->forceDelete();
                $raw_material = RawMaterialStock::where('raw_material_stocks.stock_date', '=', $stock_date)->forceDelete();
                $forecast = StocktakingMaterialForecast::where('stocktaking_material_forecasts.created_by', '=', 1)->forceDelete();

                $id = Auth::id();

                $file = $request->file('storage_location_stock');
                $data = file_get_contents($file);

                $calendar = StocktakingCalendar::where('date', $stock_date)->first();
                $insert_st_location_stock = false;
                if ($calendar) {
                    if ($calendar->status != 'finished') {
                        StocktakingLocationStock::truncate();
                        $delete = db::connection('ympimis_2')
                            ->table('stocktaking_finish_goods')
                            ->where('category', '=', 'YMES')
                            ->delete();

                        $insert_st_location_stock = true;
                    }
                }

                $month = date('Y-m', strtotime($request->get('date_stock')));
                $calendar = StocktakingCalendar::where(db::raw('date_format(date, "%Y-%m")'), $month)->first();

                $insert_st_forecast = false;
                if ($calendar) {
                    $yesterday_st = date('Y-m-d', strtotime('yesterday', strtotime($calendar->date)));

                    if ($stock_date == $yesterday_st) {
                        $insert_st_forecast = true;
                    }
                }

                $rows = explode("\r\n", $data);
                foreach ($rows as $row) {
                    if (strlen($row) > 0) {
                        $row = explode("\t", $row);
                        if ($row[0] != 'Material' && is_numeric(str_replace('"', '', str_replace(',', '', $row[3])))) {
                            if (strlen($row[0]) == 6) {
                                $material_number = "0" . $row[0];
                            } elseif (strlen($row[0]) == 5) {
                                $material_number = "00" . $row[0];
                            } else {
                                $material_number = $row[0];
                            }

                            $storage_location_stock = new StorageLocationStock([
                                'material_number' => $material_number,
                                'material_description' => $row[1],
                                'storage_location' => $row[2],
                                'unrestricted' => str_replace('"', '', str_replace(',', '', $row[3])),
                                'download_date' => date('Y-m-d', strtotime($row[4])),
                                'download_time' => date('H:i:s', strtotime(str_replace('/', '-', $row[5]))),
                                'stock_date' => $stock_date,
                                'created_by' => $id,
                            ]);
                            $storage_location_stock->save();

                            if ($insert_st_location_stock) {
                                $st_location_stock = new StocktakingLocationStock([
                                    'material_number' => $material_number,
                                    'material_description' => $row[1],
                                    'storage_location' => $row[2],
                                    'unrestricted' => str_replace('"', '', str_replace(',', '', $row[3])),
                                    'download_date' => date('Y-m-d', strtotime($row[4])),
                                    'download_time' => date('H:i:s', strtotime(str_replace('/', '-', $row[5]))),
                                    'stock_date' => $stock_date,
                                    'created_by' => $id,
                                ]);
                                $st_location_stock->save();

                                if ($row[2] == 'FSTK') {
                                    $insert_stocktaking_finish_goods = db::connection('ympimis_2')
                                        ->table('stocktaking_finish_goods')
                                        ->insert([
                                            'category' => 'YMES',
                                            'location' => 'FSTK',
                                            'material_number' => $material_number,
                                            'material_description' => $row[1],
                                            'ymes' => str_replace('"', '', str_replace(',', '', $row[3])),
                                            'mirai' => 0,
                                            'pi' => 0,
                                            'created_by' => strtoupper(Auth::user()->username),
                                            'created_at' => Carbon::now(),
                                            'updated_at' => Carbon::now(),
                                        ]);
                                }

                            }

                            if ($insert_st_forecast) {

                                $upt = StocktakingMaterialForecast::updateOrCreate(
                                    ['material_number' => $material_number],
                                    ['updated_at' => Carbon::now()]
                                );
                                $upt->save();

                            }
                        }
                    }
                }

                $now = date("Y-m-d H:i:s");

                // DB::insert("
                //     insert into raw_material_stocks(material_number, material_description, storage_location, quantity, stock_date, created_by, created_at, updated_at)
                //     select material_number, material_description, storage_location, round(sum(quantity),6) as quantity, stock_date, '".$id."' as created_by, '".$now."' as created_at, '".$now."' as updated_at from
                //     (
                //     select if(material_list_by_models.material_parent is null, storage_location_stocks.material_number, material_list_by_models.material_child) as material_number, if(material_list_by_models.material_parent is null, storage_location_stocks.material_description, material_list_by_models.material_child_description) as material_description, storage_location_stocks.storage_location, if(material_list_by_models.material_parent is null, storage_location_stocks.unrestricted, storage_location_stocks.unrestricted*material_list_by_models.usage) as quantity, storage_location_stocks.stock_date from storage_location_stocks left join material_list_by_models on material_list_by_models.material_parent = storage_location_stocks.material_number where storage_location_stocks.stock_date = '".$stock_date."'
                //     ) as raw_material_stocks group by material_number, material_description, storage_location, stock_date
                //     ");

                return redirect('/index/material/storage')->with('success', 'Storage Location Stock Uploaded')->with('page', 'Upload Storage')->with('head', 'Raw Material Monitoring');
            } catch (\Exception$e) {
                return redirect('/index/material/storage')->with('error', $e->getMessage())->with('page', 'Upload Storage')->with('head', 'Raw Material Monitoring');
            }

        } else {
            return redirect('/index/material/storage')->with('error', 'Stock Date and File must be selected.')->with('page', 'Upload Storage')->with('head', 'Raw Material Monitoring');
        }
    }

    public function importSmbmr(Request $request)
    {
        if ($request->hasFile('smbmr')) {
            try {
                MaterialListByModel::truncate();
                $id = Auth::id();

                $file = $request->file('smbmr');
                $data = file_get_contents($file);

                $rows = explode("\r\n", $data);
                foreach ($rows as $row) {
                    if (strlen($row) > 0) {
                        $row = explode("\t", $row);
                        if ($row[0] != 'Plant' && strlen($row[0] > 0)) {
                            if (strlen($row[5]) == 6) {
                                $material_child = "0" . $row[5];
                            } elseif (strlen($row[5]) == 5) {
                                $material_child = "00" . $row[5];
                            } else {
                                $material_child = $row[5];
                            }

                            $material_list_by_models = new MaterialListByModel([
                                'material_parent' => $row[1],
                                'material_parent_description' => $row[2],
                                'material_child' => $material_child,
                                'material_child_description' => $row[6],
                                'uom' => $row[7],
                                'purg' => $row[8],
                                'usage' => str_replace('"', '', str_replace(',', '', $row[17])),
                                'vendor' => $row[32],
                                'created_by' => $id,
                            ]);
                            $material_list_by_models->save();
                        }
                    }
                }

                return redirect('/index/material/smbmr')->with('success', 'Material List By Model Uploaded')->with('page', 'Upload SMBMR')->with('head', 'Raw Material Monitoring');
            } catch (\Exception$e) {
                return redirect('/index/material/smbmr')->with('error', $e->getMessage())->with('page', 'Upload SMBMR')->with('head', 'Raw Material Monitoring');
            }

        } else {
            return redirect('/index/material/smbmr')->with('error', 'Please select a file.')->with('page', 'Upload SMBMR')->with('head', 'Raw Material Monitoring');
        }
    }

    public function IndexUploadScrap()
    {
        $storage_locations = $this->storage_location;
        return view('raw_materials.scrap_upload', array(
            'storage_locations' => $storage_locations,
        ))->with('page', 'Upload Scrap')->with('head', 'Upload Scrap');
    }

    public function generateName($name)
    {
        $new_name = '';
        $blok_m = ['M.', 'Moch.', 'Mochammad', 'Moh.', 'Mohamad', 'Mokhamad', 'Much.', 'Muchammad', 'Muhamad', 'Muhammaad', 'Muhammad', 'Mukammad', 'Mukhamad', 'Mukhammad'];

        if (strlen($name) > 0) {
            if (str_contains($name, ' ')) {
                $name = explode(' ', $name);
                if (in_array($name[0], $blok_m)) {
                    $new_name = 'M.';
                    for ($i = 1; $i < count($name); $i++) {
                        if ($i == 1) {
                            $new_name .= $name[$i];
                        } else {
                            $new_name .= ' ';
                            $new_name .= substr($name[$i], 0, 1) . '.';
                        }
                    }
                } else {
                    for ($i = 0; $i < count($name); $i++) {
                        if ($i == 0) {
                            $new_name .= $name[$i];
                        } else {
                            $new_name .= ' ';
                            $new_name .= substr($name[$i], 0, 1) . '.';
                        }
                    }
                }
            } else {
                $new_name = $name;
            }
        } else {
            $new_name = '-';
        }

        return $new_name;
    }

}
