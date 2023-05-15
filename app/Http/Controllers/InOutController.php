<?php

namespace App\Http\Controllers;

use App\CodeGenerator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

class InOutController extends Controller
{

    // FloController OK
    // MaedaoshiController OK
    // KnockDownController OK
    // CompletionController OK
    // TransactionController - slip khusus OK
    // ScrapController - receive scrap OK

    public function __construct()
    {
        $this->middleware('auth');
        $this->categories = [
            'KANBAN',
            'RETURN',
            'REPAIR',
            'SCRAP',
            'EXPORT',
            'SLIP KHUSUS',
            'EXTRA ORDER',
        ];
        $this->remarks = [
            'BPP-IN',
            'BPP-OUT',
            'WLD-IN',
            'WLD-OUT',
            'BFF-IN',
            'BFF-OUT',
            'LCQ-IN',
            'LCQ-OUT',
            'PLT-IN',
            'PLT-OUT',
            'FA-IN',
            'FA-OUT',
        ];
        $this->locations = [
            'SXA1',
            'FLA1',
            'SX21',
            'FL21',
            'SX51',
            'FL51',
            'SX91',
            'FL91',
            'SA1R',
            'FA1R',
        ];
    }

    public function indexInOutMonitoring()
    {
        $title = "Material IN-OUT Monitoring";
        $title_jp = "";

        return view('transactions.in_out.monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'IN-OUT Monitoring');
    }

    public function indexInOutLog()
    {
        $title = "Material IN-OUT Log";
        $title_jp = "";

        $locations = db::table('storage_locations')
            ->whereNotNull('area')
            ->where('area', '<>', 'SUBCONT')
            ->orderBy('storage_location', 'ASC')
            ->get();

        return view('transactions.in_out.log', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'locations' => $this->locations,
            'categories' => $this->categories,
            'remarks' => $this->remarks,
        ))->with('page', 'IN-OUT Log');
    }

    public function indexInOutStock()
    {
        $title = "Material IN-OUT Stock";
        $title_jp = "";

        return view('transactions.in_out.stock', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'IN-OUT Stock');
    }

    public function indexInOutCompare()
    {
        $title = "Material IN-OUT Comparison";
        $title_jp = "";

        return view('transactions.in_out.compare', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'IN-OUT Compare');
    }

    public function fetchInOutMonitoring(Request $request)
    {
        $date = date('Y-m-d');
        if (strlen($request->get('date')) > 0) {
            $date = $request->get('date');
        }
        // $date = '2022-12-26';

        $logs = db::connection('ympimis_2')->select("
            SELECT
	            l.tag,
	            l.material_number,
	            m.material_description,
	            l.issue_location,
	            l.receive_location,
	            l.quantity,
	            SPLIT_STRING ( l.remark, '-', 1 ) AS location,
	            SPLIT_STRING ( l.remark, '-', 2 ) AS status,
	            l.category,
	            l.transaction_by,
	            l.transaction_by_name,
	            l.created_by,
	            l.created_by_name,
	            l.created_at,
	            m.category AS material_category,
	            m.model,
	            m.remark
            FROM
	            kanban_inout_logs AS l
	            LEFT JOIN material_masters AS m ON m.material_number = l.material_number
            WHERE
	            l.deleted_at IS NULL
	            AND m.category IS NOT NULL
	            AND date( l.created_at ) = '" . $date . "'
            ORDER BY location ASC");

        $goods_movements = db::connection('ympimis_2')->select("
            SELECT
	            gm.slip_number AS tag,
	            gm.material_number,
	            m.material_description,
	            gm.issue_location,
	            gm.receive_location,
	            gm.quantity,
            IF
	            (
	            	gm.issue_location LIKE '%A1',
	            	'BPP',
	            IF
	            	(
	            		gm.issue_location LIKE '%21',
	            		'WLD',
	            	IF
	            		(
	            			gm.issue_location LIKE '%51'
	            			AND m.surface = 'LCQ',
	            			'LCQ',
	            		IF
	            			(
	            				gm.issue_location LIKE '%51'
	            				AND m.surface = 'PLT',
	            				'PLT',
	            			IF
	            				(
	            					gm.issue_location LIKE '%51'
	            					AND m.surface = 'BFF',
	            					'BFF',
	            				IF
	            					(
	            						gm.issue_location LIKE '%91',
	            						'FA',
	            					IF
	            						(
	            							gm.issue_location LIKE '%51',
	            							'BFF',
	                						IF
	                							(
	                								gm.issue_location LIKE '%A1R',
	                								'BPP',
	                							IF
	                							( gm.issue_location = 'FSTK', 'WH', 'UNIDENTIFIED' ))))))))) AS location,
	                'OUT' AS status,
            IF
	            (
	            	m.category = 'KD'
	            	OR m.category = 'FG',
	            	'EXPORT',
	            IF
	            	(
	            		gm.FUNCTION LIKE '%Return',
	            		'RETURN',
	            	IF
	            		(
	            			gm.FUNCTION LIKE '%Repair',
	            			'REPAIR',
	            		IF
	            		( gm.remark = 'KITTO', 'KANBAN', 'UNIDENTIFIED' )))) AS category,
	                gm.created_by,
	                gm.created_by_name,
	                gm.result_date AS created_at,
	                m.category AS material_category,
	                m.model,
	                m.remark
            FROM
	            goods_movements AS gm
	            LEFT JOIN material_masters AS m ON m.material_number = gm.material_number
            WHERE
	            gm.deleted_at IS NULL
	            AND m.category IS NOT NULL
	            AND gm.category != 'goods_movement_error'
	            AND date( gm.result_date ) = '" . $date . "'
            ORDER BY location ASC");

        $production_results = db::connection('ympimis_2')->select("
            SELECT
	            pr.slip_number AS tag,
	            pr.material_number,
	            m.material_description,
	            pr.issue_location,
	            pr.issue_location AS receive_location,
	            pr.quantity,
            IF
            	(
            		pr.`function` = 'confirmReturn'
            		AND b.material_parent_location2 = 'LCQ',
            		'LCQ',
            	IF
            		(
            			pr.`function` = 'confirmReturn'
            			AND b.material_parent_location2 = 'PLT',
            			'PLT',
            		IF
            		( pr.`function` = 'inputCompletionOnly', 'BFF', '' ))) AS location,
            	'OUT' AS `status`,
            IF
            	(
            		pr.`function` = 'confirmReturn',
            		'RETURN',
            	IF
            	( pr.`function` = 'inputCompletionOnly', 'KANBAN', 'UNIDENTIFIED' )) AS category,
            	pr.created_by,
            	pr.created_by_name,
            	pr.result_date AS created_at,
            	m.category AS material_category,
            	m.model
            FROM
            	production_results AS pr
            	LEFT JOIN material_masters AS m ON m.material_number = pr.material_number
            	LEFT JOIN (
            	SELECT
                DISTINCT
            		material_child,
					material_parent_location2
            	FROM
            		kanban_inout_boms
            	WHERE
            	material_parent_location2 IN ( 'LCQ', 'PLT' )) AS b ON b.material_child = pr.material_number
            WHERE
            	pr.deleted_at IS NULL
            	AND m.category IS NOT NULL
	            AND pr.category != 'production_result_error'
            	AND m.surface NOT IN ( 'LCQ', 'PLT' )
            	AND pr.`function` IN ( 'inputCompletionOnly', 'confirmReturn' )
            	AND date( pr.result_date ) = '" . $date . "'
            ORDER BY location ASC");

        $data_1 = array();

        foreach ($logs as $log) {
            if ($log->status == 'OUT') {
                array_push($data_1, [
                    'tag' => $log->tag,
                    'material_number' => $log->material_number,
                    'material_description' => $log->material_description,
                    'category' => $log->category,
                    'location' => $log->location,
                    'issue_location' => $log->issue_location,
                    'receive_location' => $log->receive_location,
                    'quantity_inout' => abs($log->quantity),
                    'quantity_transaction' => 0,
                    'created_by' => $log->created_by,
                    'created_by_name' => $log->created_by_name,
                    'created_at' => $log->created_at,
                ]);
            }
        }

        foreach ($goods_movements as $goods_movement) {
            if ($goods_movement->status == 'OUT') {
                array_push($data_1, [
                    'tag' => $goods_movement->tag,
                    'material_number' => $goods_movement->material_number,
                    'material_description' => $goods_movement->material_description,
                    'category' => $goods_movement->category,
                    'location' => $goods_movement->location,
                    'issue_location' => $goods_movement->issue_location,
                    'receive_location' => $goods_movement->receive_location,
                    'quantity_inout' => 0,
                    'quantity_transaction' => abs($goods_movement->quantity),
                    'created_by' => $goods_movement->created_by,
                    'created_by_name' => $goods_movement->created_by_name,
                    'created_at' => $goods_movement->created_at,
                ]);
            }
        }

        foreach ($production_results as $production_result) {
            if ($production_result->status == 'OUT') {
                array_push($data_1, [
                    'tag' => $production_result->tag,
                    'material_number' => $production_result->material_number,
                    'material_description' => $production_result->material_description,
                    'category' => $production_result->category,
                    'location' => $production_result->location,
                    'issue_location' => $production_result->issue_location,
                    'receive_location' => $production_result->receive_location,
                    'quantity_inout' => 0,
                    'quantity_transaction' => abs($production_result->quantity),
                    'created_by' => $production_result->created_by,
                    'created_by_name' => $production_result->created_by_name,
                    'created_at' => $production_result->created_at,
                ]);
            }
        }

        $datas = array();
        $datas_2 = array();

        foreach ($data_1 as $row) {
            $key = '';
            $key .= ($row['material_number'] . '#');
            $key .= ($row['material_description'] . '#');
            $key .= ($row['category'] . '#');
            $key .= ($row['location'] . '#');

            if (!array_key_exists($key, $datas)) {
                $data['material_number'] = $row['material_number'];
                $data['material_description'] = $row['material_description'];
                $data['category'] = $row['category'];
                $data['location'] = $row['location'];
                $data['quantity_inout'] = $row['quantity_inout'];
                $data['quantity_transaction'] = $row['quantity_transaction'];
                $data['quantity_diff'] = abs($row['quantity_inout'] - $row['quantity_transaction']);
                $datas[$key] = $data;
            } else {
                $datas[$key]['quantity_inout'] = $datas[$key]['quantity_inout'] + $row['quantity_inout'];
                $datas[$key]['quantity_transaction'] = $datas[$key]['quantity_transaction'] + $row['quantity_transaction'];
                $datas[$key]['quantity_diff'] = abs($datas[$key]['quantity_inout'] - $datas[$key]['quantity_transaction']);
            }

            $key_2 = '';
            $key_2 .= ($row['tag'] . '#');
            $key_2 .= ($row['material_number'] . '#');
            $key_2 .= ($row['material_description'] . '#');
            $key_2 .= ($row['category'] . '#');
            $key_2 .= ($row['location'] . '#');

            if (!array_key_exists($key_2, $datas_2)) {
                $data2['tag'] = $row['tag'];
                $data2['material_number'] = $row['material_number'];
                $data2['material_description'] = $row['material_description'];
                $data2['category'] = $row['category'];
                $data2['location'] = $row['location'];
                $data2['quantity_inout'] = $row['quantity_inout'];
                $data2['quantity_transaction'] = $row['quantity_transaction'];
                $data2['quantity_diff'] = abs($row['quantity_inout'] - $row['quantity_transaction']);
                $datas_2[$key_2] = $data2;
            } else {
                $datas_2[$key_2]['quantity_inout'] = $datas_2[$key_2]['quantity_inout'] + $row['quantity_inout'];
                $datas_2[$key_2]['quantity_transaction'] = $datas_2[$key_2]['quantity_transaction'] + $row['quantity_transaction'];
                $datas_2[$key_2]['quantity_diff'] = abs($datas_2[$key_2]['quantity_inout'] - $datas_2[$key_2]['quantity_transaction']);
            }

        }

        $resumes = array();

        foreach ($datas as $row) {
            $key = '';
            $key .= ($row['category'] . '#');
            $key .= ($row['location'] . '#');

            if (!array_key_exists($key, $resumes)) {
                $data_2['category'] = $row['category'];
                $data_2['location'] = $row['location'];
                $data_2['quantity_inout'] = $row['quantity_inout'];
                $data_2['quantity_transaction'] = $row['quantity_transaction'];
                $data_2['quantity_diff'] = $row['quantity_diff'];

                $resumes[$key] = $data_2;
            } else {
                $resumes[$key]['quantity_inout'] = $resumes[$key]['quantity_inout'] + $row['quantity_inout'];
                $resumes[$key]['quantity_transaction'] = $resumes[$key]['quantity_transaction'] + $row['quantity_transaction'];
                $resumes[$key]['quantity_diff'] = $resumes[$key]['quantity_diff'] + $row['quantity_diff'];
            }
        }

        $response = array(
            'status' => true,
            'data_1' => $data_1,
            'datas' => $datas,
            'datas_2' => $datas_2,
            'resumes' => $resumes,
            'periode' => date('d F Y', strtotime($date)),
        );
        return Response::json($response);
    }

    public function fetchInOutLog(Request $request)
    {
        $date = explode(' - ', $request->get('date'));
        $issue_locations = $request->get('issue_locations');
        $receive_locations = $request->get('receive_locations');
        $categories = $request->get('categories');
        $remarks = $request->get('remarks');
        $material_numbers = explode(',', $request->get('material_numbers'));
        $tags = explode(',', $request->get('tags'));

        $where_date = "";
        $where_issue_location = "";
        $where_receive_location = "";
        $where_category = "";
        $where_remark = "";
        $where_material_number = "";
        $where_tag = "";

        $where_date = "AND DATE(created_at) >= '" . $date[0] . "' AND DATE(created_at) <= '" . $date[1] . "'";
        if (count($issue_locations) > 0) {
            for ($i = 0; $i < count($issue_locations); $i++) {
                $where_issue_location = $where_issue_location . "'" . $issue_locations[$i] . "'";
                if ($i != (count($issue_locations) - 1)) {
                    $where_issue_location = $where_issue_location . ',';
                }
            }
            $where_issue_location = " AND issue_location IN (" . $where_issue_location . ") ";
        }
        if (count($receive_locations) > 0) {
            for ($i = 0; $i < count($receive_locations); $i++) {
                $where_receive_location = $where_receive_location . "'" . $receive_locations[$i] . "'";
                if ($i != (count($receive_locations) - 1)) {
                    $where_receive_location = $where_receive_location . ',';
                }
            }
            $where_receive_location = " AND receive_location IN (" . $where_receive_location . ") ";
        }
        if (count($categories) > 0) {
            for ($i = 0; $i < count($categories); $i++) {
                $where_category = $where_category . "'" . $categories[$i] . "'";
                if ($i != (count($categories) - 1)) {
                    $where_category = $where_category . ',';
                }
            }
            $where_category = " AND category IN (" . $where_category . ") ";
        }
        if (count($remarks) > 0) {
            for ($i = 0; $i < count($remarks); $i++) {
                $where_remark = $where_remark . "'" . $remarks[$i] . "'";
                if ($i != (count($remarks) - 1)) {
                    $where_remark = $where_remark . ',';
                }
            }
            $where_remark = " AND remark IN (" . $where_remark . ") ";
        }
        if (count($material_numbers) > 0) {
            if ($material_numbers[0] != "") {
                for ($i = 0; $i < count($material_numbers); $i++) {
                    $where_material_number = $where_material_number . "'" . $material_numbers[$i] . "'";
                    if ($i != (count($material_numbers) - 1)) {
                        $where_material_number = $where_material_number . ',';
                    }
                }
                $where_material_number = " AND material_number IN (" . $where_material_number . ") ";
            }
        }
        if (count($tags) > 0) {
            if ($tags[0] != "") {
                for ($i = 0; $i < count($tags); $i++) {
                    $where_tag = $where_tag . "'" . $tags[$i] . "'";
                    if ($i != (count($tags) - 1)) {
                        $where_tag = $where_tag . ',';
                    }
                }
                $where_tag = " AND tag IN (" . $where_tag . ") ";
            }
        }

        $datas = db::connection('ympimis_2')->select("
            SELECT
	            *
            FROM
	            kanban_inout_logs
            WHERE
	            deleted_at IS NULL
                " . $where_date . "
                " . $where_issue_location . "
                " . $where_receive_location . "
                " . $where_category . "
                " . $where_remark . "
                " . $where_material_number . "
                " . $where_tag . "
        ");

        $response = array(
            'status' => true,
            'datas' => $datas,
        );
        return Response::json($response);
    }

    public function fetchInOutStock(Request $request)
    {
        $locations = $request->get('locations');
        $categories = $request->get('categories');
        $hpls = $request->get('hpls');
        $remarks = $request->get('remarks');
        $material_numbers = explode(',', $request->get('material_numbers'));

        $where_location = "";
        $where_category = "";
        $where_hpl = "";
        $where_remark = "";
        $where_material_number = "";

        if (count($locations) > 0) {
            for ($i = 0; $i < count($locations); $i++) {
                $where_location = $where_location . "'" . $locations[$i] . "'";
                if ($i != (count($locations) - 1)) {
                    $where_location = $where_location . ',';
                }
            }
            $where_location = " AND location IN (" . $where_location . ") ";
        }

        if (count($categories) > 0) {
            for ($i = 0; $i < count($categories); $i++) {
                $where_category = $where_category . "'" . $categories[$i] . "'";
                if ($i != (count($categories) - 1)) {
                    $where_category = $where_category . ',';
                }
            }
            $where_category = " AND category IN (" . $where_category . ") ";
        }

        if (count($hpls) > 0) {
            for ($i = 0; $i < count($hpls); $i++) {
                $where_hpl = $where_hpl . "'" . $hpls[$i] . "'";
                if ($i != (count($hpls) - 1)) {
                    $where_hpl = $where_hpl . ',';
                }
            }
            $where_hpl = " AND hpl IN (" . $where_hpl . ") ";
        }

        if (count($remarks) > 0) {
            for ($i = 0; $i < count($remarks); $i++) {
                $where_remark = $where_remark . "'" . $remarks[$i] . "'";
                if ($i != (count($remarks) - 1)) {
                    $where_remark = $where_remark . ',';
                }
            }
            $where_remark = " AND m.remark IN (" . $where_remark . ") ";
        }

        if (count($material_numbers) > 0) {
            if ($material_numbers[0] != "") {
                for ($i = 0; $i < count($material_numbers); $i++) {
                    $where_material_number = $where_material_number . "'" . $material_numbers[$i] . "'";
                    if ($i != (count($material_numbers) - 1)) {
                        $where_material_number = $where_material_number . ',';
                    }
                }
                $where_material_number = " AND i.material_number IN (" . $where_material_number . ") ";
            }
        }

        $datas = array();

        $datas = db::connection('ympimis_2')->select("
            SELECT
	            i.material_number,
	            i.material_description,
	            i.quantity,
	            SPLIT_STRING ( i.location, '-', 1 ) AS location,
	            SPLIT_STRING ( i.location, '-', 2 ) AS category,
	            SPLIT_STRING ( i.location, '-', 3 ) AS status,
	            MID( m.location, 1, 2 ) AS hpl,
	            m.remark,
	            m.model,
	            i.deleted_at,
	            i.updated_at
            FROM
	            kanban_inout_inventories AS i
	            LEFT JOIN material_masters AS m ON i.material_number = m.material_number
            WHERE
                i.deleted_at IS NULL
                " . $where_material_number . "
                " . $where_remark . "
            HAVING
                deleted_at IS NULL
                " . $where_location . "
                " . $where_category . "
                " . $where_hpl . "
                ");

        $response = array(
            'status' => true,
            'datas' => $datas,
        );
        return Response::json($response);
    }

    public function fetchInOutCompare()
    {
        $datas = array();

        $response = array(
            'status' => true,
            'datas' => $datas,
        );
        return Response::json($response);
    }

    public function indexInOut(Request $request)
    {
        $remark = strtoupper($request->get('remark'));

        $employees = db::select("
            SELECT
            es.employee_id,
            es.`name`,
            es.department,
            e.tag
            FROM
            employee_syncs AS es
            LEFT JOIN employees AS e ON e.employee_id = es.employee_id
            WHERE
            (
                es.end_date IS NULL
                OR es.end_date >= date(
                    now()))
            AND es.department IS NOT NULL
            AND es.grade_code <> 'J0-'
            AND es.grade_code <> 'OS'");

        if ($remark == 'BPP-IN') {
            $title = "Material Masuk BPP";
            $title_jp = "";
            $view = "transactions.in_out.in_new";
        }
        if ($remark == 'BPP-OUT') {
            $title = "Material Keluar BPP";
            $title_jp = "";
            $view = "transactions.in_out.out_new";
        }
        if ($remark == 'WLD-IN') {
            $title = "Material Masuk WLD";
            $title_jp = "";
            $view = "transactions.in_out.in_new";
        }
        if ($remark == 'WLD-OUT') {
            $title = "Material Keluar WLD";
            $title_jp = "";
            $view = "transactions.in_out.out_new";
        }
        if ($remark == 'BFF-IN') {
            $title = "Material Masuk BFF";
            $title_jp = "";
            $view = "transactions.in_out.in_new";
        }
        if ($remark == 'BFF-OUT') {
            $title = "Material Keluar BFF";
            $title_jp = "";
            $view = "transactions.in_out.out_new";
        }
        if ($remark == 'PLT-IN') {
            $title = "Material Masuk PLT";
            $title_jp = "";
            $view = "transactions.in_out.in_new";
        }
        if ($remark == 'PLT-OUT') {
            $title = "Material Keluar PLT";
            $title_jp = "";
            $view = "transactions.in_out.out_new";
        }
        if ($remark == 'LCQ-IN') {
            $title = "Material Masuk LCQ";
            $title_jp = "";
            $view = "transactions.in_out.in_new";
        }
        if ($remark == 'LCQ-OUT') {
            $title = "Material Keluar LCQ";
            $title_jp = "";
            $view = "transactions.in_out.out_new";
        }
        if ($remark == 'FA-IN') {
            $title = "Material Masuk FA";
            $title_jp = "";
            $view = "transactions.in_out.in_new";
        }
        if ($remark == 'FA-OUT') {
            $title = "Material Keluar FA";
            $title_jp = "";
            $view = "transactions.in_out.out_new";
        }

        return view($view, array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employees' => $employees,
            'remark' => $remark,
        ))->with('page', 'IN-OUT');

    }

    public function fetchInOutTag(Request $request)
    {
        $tag = $request->get('tag');
        // remark = BPP-IN, BPP-OUT, WLD-IN, WLD-OUT, etc
        $remark = $request->get('remark');
        // category = kanban, scrap, return, repair, ekspor
        $category = $request->get('category');

        $intransit = db::connection('ympimis_2')->table('kanban_inout_intransits')
            ->where('tag', '=', $tag)
            ->first();

        if (explode('-', $remark)[1] == 'OUT') {
            if ($intransit) {
                $response = array(
                    'status' => false,
                    'message' => "Kanban sudah pada kondisi INTRANSIT.",
                );
                return Response::json($response);
            }
        }
        if (explode('-', $remark)[1] == 'IN' && $category != 'SCRAP') {
            if (!$intransit) {
                $response = array(
                    'status' => false,
                    'message' => "Kanban tidak pada kondisi INTRANSIT.",
                );
                return Response::json($response);
            }
        }

        if ($category == 'KANBAN') {
            $kanbans = db::connection('kitto')->table('transfers')
                ->leftJoin('completions', 'completions.id', '=', 'transfers.completion_id')
                ->leftJoin('materials', 'materials.id', '=', 'transfers.material_id')
                ->where('transfers.barcode_number_transfer', '=', $tag)
                ->select(
                    'materials.material_number',
                    'materials.description as material_description',
                    'transfers.issue_location',
                    'transfers.receive_location',
                    'transfers.lot_transfer as quantity'
                )
                ->get();

            if (explode('-', $remark)[1] == 'OUT') {
                for ($i = 0; $i < count($kanbans); $i++) {
                    if (explode('-', $remark)[0] == 'BPP') {
                        if (!str_contains($kanbans[$i]->issue_location, 'A1')) {
                            $response = array(
                                'status' => false,
                                'message' => "Issue location kanban tidak sesuai.",
                            );
                            return Response::json($response);
                        }
                    } else if (explode('-', $remark)[0] == 'WLD') {
                        if (!str_contains($kanbans[$i]->issue_location, '21')) {
                            $response = array(
                                'status' => false,
                                'message' => "Issue location kanban tidak sesuai.",
                            );
                            return Response::json($response);
                        }
                    } else if (explode('-', $remark)[0] == 'BFF' || explode('-', $remark)[0] == 'LCQ' || explode('-', $remark)[0] == 'PLT') {
                        if (!str_contains($kanbans[$i]->issue_location, '51')) {
                            $response = array(
                                'status' => false,
                                'message' => "Issue location kanban tidak sesuai.",
                            );
                            return Response::json($response);
                        }
                    }
                }
            }
            if (explode('-', $remark)[1] == 'IN') {
                for ($i = 0; $i < count($kanbans); $i++) {
                    if (explode('-', $remark)[0] == 'BPP') {
                        if (!str_contains($kanbans[$i]->receive_location, 'A1')) {
                            $response = array(
                                'status' => false,
                                'message' => "Receive location kanban tidak sesuai.",
                            );
                            return Response::json($response);
                        }

                    } else if (explode('-', $remark)[0] == 'WLD') {
                        if (!str_contains($kanbans[$i]->receive_location, '21')) {
                            $response = array(
                                'status' => false,
                                'message' => "Receive location kanban tidak sesuai.",
                            );
                            return Response::json($response);
                        }

                    } else if (explode('-', $remark)[0] == 'BFF') {
                        if (!str_contains($kanbans[$i]->receive_location, '51')) {
                            $response = array(
                                'status' => false,
                                'message' => "Receive location kanban tidak sesuai.",
                            );
                            return Response::json($response);
                        }
                    } else if (explode('-', $remark)[0] == 'LCQ' || explode('-', $remark)[0] == 'PLT') {
                        if (!str_contains($kanbans[$i]->receive_location, '51') && !str_contains($kanbans[$i]->receive_location, '91')) {
                            $response = array(
                                'status' => false,
                                'message' => "Receive location kanban tidak sesuai.",
                            );
                            return Response::json($response);
                        }

                    } else if (explode('-', $remark)[0] == 'FA') {
                        if (!str_contains($kanbans[$i]->receive_location, '91')) {
                            $response = array(
                                'status' => false,
                                'message' => "Receive location kanban tidak sesuai.",
                            );
                            return Response::json($response);
                        }
                    }
                }

            }

            if (count($kanbans) > 0) {
                if (!str_contains($kanbans[0]->issue_location, 'A1')) {
                    if (in_array($remark, ['BFF-OUT', 'LCQ-IN', 'PLT-IN'])) {
                        $temps = $kanbans;
                        $kanbans = [];

                        for ($i = 0; $i < count($temps); $i++) {
                            $boms = db::connection('ympimis_2')->table('kanban_inout_boms')
                                ->where('material_parent', '=', $temps[$i]->material_number)
                                ->get();

                            for ($j = 0; $j < count($boms); $j++) {
                                $row = array();
                                $row['material_number'] = $boms[$j]->material_child;
                                $row['material_description'] = $boms[$j]->material_child_description;
                                $row['issue_location'] = $boms[$j]->material_child_location;
                                $row['receive_location'] = $boms[$j]->material_parent_location;
                                $row['quantity'] = $temps[$i]->quantity;
                                $kanbans[] = (object) $row;
                            }
                        }
                    }
                }
            }

        }
        if ($category == 'RETURN') {
            $id = substr($tag, 2);

            $kanbans = db::table('return_lists')
                ->where('id', '=', $id)
                ->select(
                    'return_lists.material_number',
                    'return_lists.material_description',
                    'return_lists.receive_location as issue_location',
                    'return_lists.issue_location as receive_location',
                    'return_lists.quantity'
                )
                ->get();
        }
        if ($category == 'REPAIR') {
            $id = substr($tag, 2);

            $kanbans = db::connection('ympimis_2')->table('repair_lists')
                ->where('id', '=', $id)
                ->select(
                    'repair_lists.material_number',
                    'repair_lists.material_description',
                    'repair_lists.receive_location',
                    'repair_lists.issue_location',
                    'repair_lists.quantity'
                )
                ->get();
        }
        if ($category == 'SCRAP') {

            if (explode('-', $remark)[1] == 'OUT') {
                $kanbans = db::table('scrap_lists')
                    ->where('slip', '=', $tag)
                    ->select(
                        'scrap_lists.material_number',
                        'scrap_lists.material_description',
                        'scrap_lists.issue_location',
                        'scrap_lists.receive_location',
                        'scrap_lists.quantity',
                        'scrap_lists.category'
                    )
                    ->get();
            }

            if (explode('-', $remark)[1] == 'IN') {
                $kanbans = db::table('scrap_penarikan_logs')
                    ->where('scrap_penarikan_logs.slip_penarikan', '=', $tag)
                    ->select(
                        'scrap_penarikan_logs.material_number',
                        'scrap_penarikan_logs.material_description',
                        'scrap_penarikan_logs.issue_location',
                        'scrap_penarikan_logs.receive_location',
                        'scrap_penarikan_logs.quantity',
                        'scrap_penarikan_logs.scrap_by',
                        'scrap_penarikan_logs.category'
                    )
                    ->get();

                if (count($kanbans)) {

                    for ($i = 0; $i < count($kanbans); $i++) {
                        $transact = db::table('users')->where('id', $kanbans[$i]->scrap_by)->first();

                        db::connection('ympimis_2')->table('kanban_inout_intransits')
                            ->insert([
                                'tag' => $tag,
                                'material_number' => $kanbans[$i]->material_number,
                                'material_description' => $kanbans[$i]->material_description,
                                'location' => explode('-', $remark)[0] . '-INTRANSIT-SCRAP',
                                'issue_location' => $kanbans[$i]->receive_location,
                                'receive_location' => $kanbans[$i]->issue_location,
                                'quantity' => $kanbans[$i]->quantity,
                                'transaction_by' => strtoupper($transact->username),
                                'transaction_by_name' => ucwords($transact->name),
                                'created_by' => strtoupper(Auth::user()->username),
                                'created_by_name' => ucwords(Auth::user()->name),
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);

                    }

                }
            }

        }
        if ($category == 'EXPORT') {
            $kanbans = db::table('knock_down_details')
                ->leftJoin('materials', 'materials.material_number', '=', 'knock_down_details.material_number')
                ->where('knock_down_details.kd_number', '=', $tag)
                ->select(
                    'knock_down_details.material_number',
                    'materials.material_description',
                    db::raw('storage_location as issue_location'),
                    db::raw('"FSTK" as receive_location'),
                    'knock_down_details.quantity'
                )
                ->get();
        }
        if ($category == 'SLIP KHUSUS') {
            $kanbans = db::connection('ympimis_2')->table('kanban_inout_unusuals')
                ->where('slip_number', '=', $tag)
                ->select(
                    'kanban_inout_unusuals.material_number',
                    'kanban_inout_unusuals.material_description',
                    'kanban_inout_unusuals.issue_location',
                    'kanban_inout_unusuals.receive_location',
                    'kanban_inout_unusuals.valid_to',
                    'kanban_inout_unusuals.quantity'
                )
                ->get();

            if ($kanbans[0]->valid_to < date('Y-m-d H:i:s')) {
                if ($kanbans[0]->category != 'TRIAL' && $kanbans[0]->category != 'SAMPLE') {
                    $response = array(
                        'status' => false,
                        'message' => "Masa berlaku kanban khusus sudah habis",
                    );
                    return Response::json($response);
                }
            }
        }
        if ($category == 'EXTRA ORDER') {
        }

        if (!$kanbans) {
            $response = array(
                'status' => false,
                'message' => "Data tag tidak ditemukan.",
            );
            return Response::json($response);
        }

        $material_numbers = array();

        foreach ($kanbans as $row) {
            array_push($material_numbers, $row->material_number);
        }

        $materials = db::connection('ympimis_2')->table('material_masters')
            ->whereIn('material_number', $material_numbers)
            ->get();

        foreach ($materials as $material) {
            if ($material->category == null) {
                $response = array(
                    'status' => false,
                    'message' => "Belum termasuk material yang di kontrol IN-OUT.",
                );
                return Response::json($response);
            }
        }

        $response = array(
            'status' => true,
            'kanbans' => $kanbans,
            'materials' => $materials,
        );

        return Response::json($response);

    }

    public function inputInOut(Request $request)
    {
        try {
            DB::connection('ympimis_2')->beginTransaction();

            // remark = BPP-IN, BPP-OUT, WLD-IN, WLD-OUT, ETC
            $remark = $request->get('remark');
            // category = KANBAN, RETURN, REPAIR, SCRAP, EXPORT
            $category = $request->get('category');
            $location = explode('-', $remark);

            $transaction_by = $request->get('transaction_by');
            $transaction_by_name = $request->get('transaction_by_name');
            $created_by = Auth::user()->username;
            $created_by_name = Auth::user()->name;

            foreach ($request->get('list') as $data) {

                $tag = $data['tag'];
                $material_number = $data['material_number'];
                $material_description = $data['material_description'];
                $issue_location = $data['issue_location'];
                $receive_location = $data['receive_location'];
                $quantity = $data['quantity'];
                if (str_contains($remark, 'OUT')) {
                    $quantity = $data['quantity'] * -1;
                }

                if ($category == 'KANBAN') {
                    if ($remark == 'BPP-OUT' || $remark == 'WLD-OUT' || $remark == 'BFF-OUT' || $remark == 'PLT-OUT' || $remark == 'LCQ-OUT') {
                        db::connection('ympimis_2')->table('kanban_inout_logs')->insert([
                            'tag' => $tag,
                            'material_number' => $material_number,
                            'material_description' => $material_description,
                            'issue_location' => $issue_location,
                            'receive_location' => $receive_location,
                            'quantity' => $quantity,
                            'remark' => $remark,
                            'category' => $category,
                            'transaction_by' => $transaction_by,
                            'transaction_by_name' => $transaction_by_name,
                            'created_by' => $created_by,
                            'created_by_name' => $created_by_name,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                        $inventory_finish = db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->where('material_number', '=', $material_number)
                            ->where('location', '=', $location[0] . '-FINISH')
                            ->first();

                        $inventory_intransit = db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->where('material_number', '=', $material_number)
                            ->where('location', '=', $location[0] . '-INTRANSIT')
                            ->first();

                        if ($inventory_finish) {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $material_number)
                                ->where('location', '=', $location[0] . '-FINISH')
                                ->update([
                                    'quantity' => $inventory_finish->quantity - $quantity,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        } else {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->insert([
                                    'material_number' => $material_number,
                                    'material_description' => $material_description,
                                    'quantity' => $quantity * -1,
                                    'location' => $location[0] . '-FINISH',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }

                        if ($inventory_intransit) {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $material_number)
                                ->where('location', '=', $location[0] . '-INTRANSIT-KANBAN')
                                ->update([
                                    'quantity' => $inventory_intransit->quantity + $quantity,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        } else {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->insert([
                                    'material_number' => $material_number,
                                    'material_description' => $material_description,
                                    'quantity' => $quantity,
                                    'location' => $location[0] . '-INTRANSIT-KANBAN',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }

                        db::connection('ympimis_2')->table('kanban_inout_intransits')
                            ->insert([
                                'tag' => $tag,
                                'material_number' => $material_number,
                                'material_description' => $material_description,
                                'location' => $location[0] . '-INTRANSIT-KANBAN',
                                'issue_location' => $issue_location,
                                'receive_location' => $receive_location,
                                'quantity' => $quantity,
                                'transaction_by' => $transaction_by,
                                'transaction_by_name' => $transaction_by_name,
                                'created_by' => $created_by,
                                'created_by_name' => $created_by_name,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    }
                    if ($remark == 'WLD-IN' || $remark == 'BFF-IN' || $remark == 'FA-IN') {
                        db::connection('ympimis_2')->table('kanban_inout_logs')->insert([
                            'tag' => $tag,
                            'material_number' => $material_number,
                            'material_description' => $material_description,
                            'issue_location' => $issue_location,
                            'receive_location' => $receive_location,
                            'quantity' => $quantity,
                            'remark' => $remark,
                            'category' => $category,
                            'transaction_by' => $transaction_by,
                            'transaction_by_name' => $transaction_by_name,
                            'created_by' => $created_by,
                            'created_by_name' => $created_by_name,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                        $inventory_material = db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->where('material_number', '=', $material_number)
                            ->where('location', '=', $location[0] . '-MATERIAL')
                            ->first();

                        $inventory_intransit = db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->where('material_number', '=', $material_number)
                            ->where('location', '=', $location[0] . '-INTRANSIT-KANBAN')
                            ->first();

                        if ($inventory_material) {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $material_number)
                                ->where('location', '=', $location[0] . '-MATERIAL')
                                ->update([
                                    'quantity' => $inventory_material->quantity + $quantity,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        } else {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->insert([
                                    'material_number' => $material_number,
                                    'material_description' => $material_description,
                                    'quantity' => $quantity,
                                    'location' => $location[0] . '-MATERIAL',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }

                        if ($inventory_intransit) {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $material_number)
                                ->where('location', '=', $location[0] . '-INTRANSIT-KANBAN')
                                ->update([
                                    'quantity' => $inventory_intransit->quantity - $quantity,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        } else {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->insert([
                                    'material_number' => $material_number,
                                    'material_description' => $material_description,
                                    'quantity' => $quantity * -1,
                                    'location' => $location[0] . '-INTRANSIT-KANBAN',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }

                        db::connection('ympimis_2')->table('kanban_inout_intransits')
                            ->where('tag', '=', $tag)
                            ->delete();
                    }
                    if ($remark == 'PLT-IN' || $remark == 'LCQ-IN') {

                        db::connection('ympimis_2')->table('kanban_inout_logs')->insert([
                            'tag' => $tag,
                            'material_number' => $material_number,
                            'material_description' => $material_description,
                            'issue_location' => $issue_location,
                            'receive_location' => $receive_location,
                            'quantity' => $quantity,
                            'remark' => $remark,
                            'category' => $category,
                            'transaction_by' => $transaction_by,
                            'transaction_by_name' => $transaction_by_name,
                            'created_by' => $created_by,
                            'created_by_name' => $created_by_name,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                        $inventory_material = db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->where('material_number', '=', $material_number)
                            ->where('location', '=', $location[0] . '-MATERIAL')
                            ->first();

                        $inventory_intransit = db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->where('material_number', '=', $material_number)
                            ->where('location', '=', $location[0] . '-INTRANSIT-KANBAN')
                            ->first();

                        if ($inventory_material) {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $material_number)
                                ->where('location', '=', $location[0] . '-MATERIAL')
                                ->update([
                                    'quantity' => $inventory_material->quantity + $quantity,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        } else {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->insert([
                                    'material_number' => $material_number,
                                    'material_description' => $material_description,
                                    'quantity' => $quantity,
                                    'location' => $location[0] . '-MATERIAL',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }

                        if ($inventory_intransit) {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $material_number)
                                ->where('location', '=', $location[0] . '-INTRANSIT-KANBAN')
                                ->update([
                                    'quantity' => $inventory_intransit->quantity - $quantity,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        } else {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->insert([
                                    'material_number' => $material_number,
                                    'material_description' => $material_description,
                                    'quantity' => $quantity * -1,
                                    'location' => $location[0] . '-INTRANSIT-KANBAN',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }

                        db::connection('ympimis_2')->table('kanban_inout_intransits')
                            ->where('tag', '=', $tag)
                            ->delete();
                    }
                }
                if ($category == 'RETURN') {
                    if ($location[1] == 'OUT') {
                        db::connection('ympimis_2')->table('kanban_inout_logs')->insert([
                            'tag' => $tag,
                            'material_number' => $material_number,
                            'material_description' => $material_description,
                            'issue_location' => $issue_location,
                            'receive_location' => $receive_location,
                            'quantity' => $quantity,
                            'remark' => $remark,
                            'category' => $category,
                            'transaction_by' => $transaction_by,
                            'transaction_by_name' => $transaction_by_name,
                            'created_by' => $created_by,
                            'created_by_name' => $created_by_name,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                        $inventory_material = db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->where('material_number', '=', $material_number)
                            ->where('location', '=', $location[0] . '-MATERIAL')
                            ->first();

                        $inventory_intransit = db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->where('material_number', '=', $material_number)
                            ->where('location', '=', $location[0] . '-INTRANSIT-RETURN')
                            ->first();

                        if ($inventory_material) {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $material_number)
                                ->where('location', '=', $location[0] . '-MATERIAL')
                                ->update([
                                    'quantity' => $inventory_material->quantity - $quantity,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        } else {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->insert([
                                    'material_number' => $material_number,
                                    'material_description' => $material_description,
                                    'quantity' => $quantity * -1,
                                    'location' => $location[0] . '-MATERIAL',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }

                        if ($inventory_intransit) {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $material_number)
                                ->where('location', '=', $location[0] . '-INTRANSIT-RETURN')
                                ->update([
                                    'quantity' => $inventory_intransit->quantity + $quantity,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        } else {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->insert([
                                    'material_number' => $material_number,
                                    'material_description' => $material_description,
                                    'quantity' => $quantity,
                                    'location' => $location[0] . '-INTRANSIT-RETURN',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }

                        db::connection('ympimis_2')->table('kanban_inout_intransits')
                            ->insert([
                                'tag' => $tag,
                                'material_number' => $material_number,
                                'material_description' => $material_description,
                                'location' => $location[0] . '-INTRANSIT-RETURN',
                                'issue_location' => $issue_location,
                                'receive_location' => $receive_location,
                                'quantity' => $quantity,
                                'transaction_by' => $transaction_by,
                                'transaction_by_name' => $transaction_by_name,
                                'created_by' => $created_by,
                                'created_by_name' => $created_by_name,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    }
                    if ($location[1] == 'IN') {

                        db::connection('ympimis_2')->table('kanban_inout_logs')->insert([
                            'tag' => $tag,
                            'material_number' => $material_number,
                            'material_description' => $material_description,
                            'issue_location' => $issue_location,
                            'receive_location' => $receive_location,
                            'quantity' => $quantity,
                            'remark' => $remark,
                            'category' => $category,
                            'transaction_by' => $transaction_by,
                            'transaction_by_name' => $transaction_by_name,
                            'created_by' => $created_by,
                            'created_by_name' => $created_by_name,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                        $boms = db::connection('ympimis_2')->table('kanban_inout_boms')
                            ->where('material_parent', '=', $material_number)
                            ->get();

                        foreach ($boms as $bom) {
                            $inventory_material = db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $bom->material_child)
                                ->where('location', '=', $location[0] . '-MATERIAL')
                                ->first();

                            $inventory_intransit = db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $material_number)
                                ->where('location', '=', $location[0] . '-INTRANSIT-RETURN')
                                ->first();

                            if ($inventory_material) {
                                db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->where('material_number', '=', $bom->material_child)
                                    ->where('location', '=', $location[0] . '-MATERIAL')
                                    ->update([
                                        'quantity' => $inventory_material->quantity + $quantity,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                            } else {
                                db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->insert([
                                        'material_number' => $bom->material_child,
                                        'material_description' => $bom->material_child_description,
                                        'quantity' => $quantity,
                                        'location' => $location[0] . '-MATERIAL',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                            }

                            if ($inventory_intransit) {
                                db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->where('material_number', '=', $material_number)
                                    ->where('location', '=', $location[0] . '-INTRANSIT-RETURN')
                                    ->update([
                                        'quantity' => $inventory_intransit->quantity - $quantity,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                            } else {
                                db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->insert([
                                        'material_number' => $material_number,
                                        'material_description' => $material_description,
                                        'quantity' => $quantity * -1,
                                        'location' => $location[0] . '-INTRANSIT-RETURN',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                            }

                        }

                        db::connection('ympimis_2')->table('kanban_inout_intransits')
                            ->where('tag', '=', $tag)
                            ->delete();

                    }
                }
                if ($category == 'REPAIR') {
                    if ($location[1] == 'OUT') {
                        db::connection('ympimis_2')->table('kanban_inout_logs')
                            ->insert([
                                'tag' => $tag,
                                'material_number' => $material_number,
                                'material_description' => $material_description,
                                'issue_location' => $issue_location,
                                'receive_location' => $receive_location,
                                'quantity' => $quantity,
                                'remark' => $remark,
                                'category' => $category,
                                'transaction_by' => $transaction_by,
                                'transaction_by_name' => $transaction_by_name,
                                'created_by' => $created_by,
                                'created_by_name' => $created_by_name,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);

                        if ($remark == 'WLD-OUT') {
                            $boms = db::connection('ympimis_2')->table('kanban_inout_boms')
                                ->where('material_parent', '=', $material_number)
                                ->get();

                            foreach ($boms as $bom) {
                                $inventory_material = db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->where('material_number', '=', $bom->material_child)
                                    ->where('location', '=', $location[0] . '-MATERIAL')
                                    ->first();

                                if ($inventory_material) {
                                    db::connection('ympimis_2')->table('kanban_inout_inventories')
                                        ->where('material_number', '=', $bom->material_child)
                                        ->where('location', '=', $location[0] . '-MATERIAL')
                                        ->update([
                                            'quantity' => $inventory_material->quantity - $quantity,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                        ]);
                                } else {
                                    db::connection('ympimis_2')->table('kanban_inout_inventories')
                                        ->insert([
                                            'material_number' => $bom->material_child,
                                            'material_description' => $bom->material_child_description,
                                            'quantity' => $quantity * -1,
                                            'location' => $location[0] . '-MATERIAL',
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s'),
                                        ]);
                                }
                            }
                        }

                        if ($remark == 'BFF-OUT') {
                            $inventory_material = db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $material_number)
                                ->where('location', '=', $location[0] . '-MATERIAL')
                                ->first();

                            if ($inventory_material) {
                                db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->where('material_number', '=', $material_number)
                                    ->where('location', '=', $location[0] . '-MATERIAL')
                                    ->update([
                                        'quantity' => $inventory_material->quantity - $quantity,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                            } else {
                                db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->insert([
                                        'material_number' => $material_number,
                                        'material_description' => $material_description,
                                        'quantity' => $quantity * -1,
                                        'location' => $location[0] . '-MATERIAL',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                            }
                        }

                        if ($remark == 'BPP-OUT') {
                            $inventory_material = db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $material_number)
                                ->where('location', '=', $location[0] . '-MATERIAL-REPAIR')
                                ->first();

                            if ($inventory_material) {
                                db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->where('material_number', '=', $material_number)
                                    ->where('location', '=', $location[0] . '-MATERIAL-REPAIR')
                                    ->update([
                                        'quantity' => $inventory_material->quantity - $quantity,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                            } else {
                                db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->insert([
                                        'material_number' => $material_number,
                                        'material_description' => $material_description,
                                        'quantity' => $quantity * -1,
                                        'location' => $location[0] . '-MATERIAL-REPAIR',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                            }
                        }

                        $inventory_intransit = db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->where('material_number', '=', $material_number)
                            ->where('location', '=', $location[0] . '-INTRANSIT-REPAIR')
                            ->first();

                        if ($inventory_intransit) {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $material_number)
                                ->where('location', '=', $location[0] . '-INTRANSIT-REPAIR')
                                ->update([
                                    'quantity' => $inventory_intransit->quantity + $quantity,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        } else {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->insert([
                                    'material_number' => $material_number,
                                    'material_description' => $material_description,
                                    'quantity' => $quantity,
                                    'location' => $location[0] . '-INTRANSIT-REPAIR',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }

                        db::connection('ympimis_2')->table('kanban_inout_intransits')
                            ->insert([
                                'tag' => $tag,
                                'material_number' => $material_number,
                                'material_description' => $material_description,
                                'location' => $location[0] . '-INTRANSIT-REPAIR',
                                'issue_location' => $issue_location,
                                'receive_location' => $receive_location,
                                'quantity' => $quantity,
                                'transaction_by' => $transaction_by,
                                'transaction_by_name' => $transaction_by_name,
                                'created_by' => $created_by,
                                'created_by_name' => $created_by_name,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    }
                    if ($location[1] == 'IN') {
                        db::connection('ympimis_2')->table('kanban_inout_logs')
                            ->insert([
                                'tag' => $tag,
                                'material_number' => $material_number,
                                'material_description' => $material_description,
                                'issue_location' => $issue_location,
                                'receive_location' => $receive_location,
                                'quantity' => $quantity,
                                'remark' => $remark,
                                'category' => $category,
                                'transaction_by' => $transaction_by,
                                'transaction_by_name' => $transaction_by_name,
                                'created_by' => $created_by,
                                'created_by_name' => $created_by_name,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);

                        if ($remark == 'WLD-IN') {
                            $boms = db::connection('ympimis_2')->table('kanban_inout_boms')
                                ->where('material_parent', '=', $material_number)
                                ->get();

                            foreach ($boms as $bom) {
                                $inventory_material = db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->where('material_number', '=', $bom->material_child)
                                    ->where('location', '=', $location[0] . '-MATERIAL')
                                    ->first();

                                if ($inventory_material) {
                                    db::connection('ympimis_2')->table('kanban_inout_inventories')
                                        ->where('material_number', '=', $bom->material_child)
                                        ->where('location', '=', $location[0] . '-MATERIAL')
                                        ->update([
                                            'quantity' => $inventory_material->quantity + $quantity,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                        ]);
                                } else {
                                    db::connection('ympimis_2')->table('kanban_inout_inventories')
                                        ->insert([
                                            'material_number' => $bom->material_child,
                                            'material_description' => $bom->material_child_description,
                                            'quantity' => $quantity,
                                            'location' => $location[0] . '-MATERIAL',
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s'),
                                        ]);
                                }
                            }
                        }

                        if ($remark == 'BFF-IN') {
                            $inventory_material = db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $material_number)
                                ->where('location', '=', $location[0] . '-MATERIAL')
                                ->first();

                            if ($inventory_material) {
                                db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->where('material_number', '=', $material_number)
                                    ->where('location', '=', $location[0] . '-MATERIAL')
                                    ->update([
                                        'quantity' => $inventory_material->quantity + $quantity,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                            } else {
                                db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->insert([
                                        'material_number' => $material_number,
                                        'material_description' => $material_description,
                                        'quantity' => $quantity,
                                        'location' => $location[0] . '-MATERIAL',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                            }
                        }

                        if ($remark == 'BPP-IN') {
                            $inventory_material = db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $material_number)
                                ->where('location', '=', $location[0] . '-MATERIAL-REPAIR')
                                ->first();

                            if ($inventory_material) {
                                db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->where('material_number', '=', $material_number)
                                    ->where('location', '=', $location[0] . '-MATERIAL-REPAIR')
                                    ->update([
                                        'quantity' => $inventory_material->quantity + $quantity,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                            } else {
                                db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->insert([
                                        'material_number' => $material_number,
                                        'material_description' => $material_description,
                                        'quantity' => $quantity,
                                        'location' => $location[0] . '-MATERIAL-REPAIR',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                            }
                        }

                        $inventory_intransit = db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->where('material_number', '=', $material_number)
                            ->where('location', '=', $location[0] . '-INTRANSIT-REPAIR')
                            ->first();

                        if ($inventory_intransit) {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $material_number)
                                ->where('location', '=', $location[0] . '-INTRANSIT-REPAIR')
                                ->update([
                                    'quantity' => $inventory_intransit->quantity + $quantity,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        } else {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->insert([
                                    'material_number' => $material_number,
                                    'material_description' => $material_description,
                                    'quantity' => $quantity,
                                    'location' => $location[0] . '-INTRANSIT-REPAIR',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }

                        db::connection('ympimis_2')->table('kanban_inout_intransits')
                            ->where('tag', '=', $tag)
                            ->delete();
                    }
                }
                if ($category == 'SCRAP') {

                    if ($location[1] == 'OUT') {
                        db::connection('ympimis_2')->table('kanban_inout_logs')
                            ->insert([
                                'tag' => $tag,
                                'material_number' => $material_number,
                                'material_description' => $material_description,
                                'issue_location' => $issue_location,
                                'receive_location' => $receive_location,
                                'quantity' => $quantity,
                                'remark' => $remark,
                                'category' => $category,
                                'transaction_by' => $transaction_by,
                                'transaction_by_name' => $transaction_by_name,
                                'created_by' => $created_by,
                                'created_by_name' => $created_by_name,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);

                        db::connection('ympimis_2')->table('kanban_inout_intransits')
                            ->insert([
                                'tag' => $tag,
                                'material_number' => $material_number,
                                'material_description' => $material_description,
                                'location' => $location[0] . '-INTRANSIT-SCRAP',
                                'issue_location' => $issue_location,
                                'receive_location' => $receive_location,
                                'quantity' => $quantity,
                                'transaction_by' => $transaction_by,
                                'transaction_by_name' => $transaction_by_name,
                                'created_by' => $created_by,
                                'created_by_name' => $created_by_name,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);

                        $scrap_category = $data['scrap_category'];
                        if ($scrap_category == 'FINISH') {
                            $boms = db::connection('ympimis_2')->table('kanban_inout_boms')
                                ->where('material_parent', '=', $material_number)
                                ->get();

                            foreach ($boms as $bom) {
                                $inventory_material = db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->where('material_number', '=', $bom->material_child)
                                    ->where('location', '=', $location[0] . '-MATERIAL')
                                    ->first();

                                if ($inventory_material) {
                                    db::connection('ympimis_2')->table('kanban_inout_inventories')
                                        ->where('material_number', '=', $bom->material_child)
                                        ->where('location', '=', $location[0] . '-MATERIAL')
                                        ->update([
                                            'quantity' => $inventory_material->quantity - $quantity,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                        ]);
                                } else {
                                    db::connection('ympimis_2')->table('kanban_inout_inventories')
                                        ->insert([
                                            'material_number' => $bom->material_child,
                                            'material_description' => $bom->material_child_description,
                                            'quantity' => $quantity * -1,
                                            'location' => $location[0] . '-MATERIAL',
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s'),
                                        ]);
                                }

                                $inventory_intransit = db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->where('material_number', '=', $bom->material_child)
                                    ->where('location', '=', $location[0] . '-INTRANSIT-SCRAP')
                                    ->first();

                                if ($inventory_intransit) {
                                    db::connection('ympimis_2')->table('kanban_inout_inventories')
                                        ->where('material_number', '=', $bom->material_child)
                                        ->where('location', '=', $location[0] . '-INTRANSIT-SCRAP')
                                        ->update([
                                            'quantity' => $inventory_intransit->quantity + $quantity,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                        ]);
                                } else {
                                    db::connection('ympimis_2')->table('kanban_inout_inventories')
                                        ->insert([
                                            'material_number' => $bom->material_child,
                                            'material_description' => $material_description,
                                            'quantity' => $quantity,
                                            'location' => $location[0] . '-INTRANSIT-SCRAP',
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s'),
                                        ]);
                                }

                            }
                        } else {
                            $inventory_material = db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $material_number)
                                ->where('location', '=', $location[0] . '-MATERIAL')
                                ->first();

                            if ($inventory_material) {
                                db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->where('material_number', '=', $material_number)
                                    ->where('location', '=', $location[0] . '-MATERIAL')
                                    ->update([
                                        'quantity' => $inventory_material->quantity - $quantity,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                            } else {
                                db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->insert([
                                        'material_number' => $material_number,
                                        'material_description' => $material_description,
                                        'quantity' => $quantity * -1,
                                        'location' => $location[0] . '-MATERIAL',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                            }

                            $inventory_intransit = db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $material_number)
                                ->where('location', '=', $location[0] . '-INTRANSIT-SCRAP')
                                ->first();

                            if ($inventory_intransit) {
                                db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->where('material_number', '=', $material_number)
                                    ->where('location', '=', $location[0] . '-INTRANSIT-SCRAP')
                                    ->update([
                                        'quantity' => $inventory_intransit->quantity + $quantity,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                            } else {
                                db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->insert([
                                        'material_number' => $material_number,
                                        'material_description' => $material_description,
                                        'quantity' => $quantity,
                                        'location' => $location[0] . '-INTRANSIT-SCRAP',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                            }

                        }
                    }

                    if ($location[1] == 'IN') {
                        db::connection('ympimis_2')->table('kanban_inout_logs')
                            ->insert([
                                'tag' => $tag,
                                'material_number' => $material_number,
                                'material_description' => $material_description,
                                'issue_location' => $receive_location,
                                'receive_location' => $issue_location,
                                'quantity' => $quantity,
                                'remark' => $remark,
                                'category' => $category,
                                'transaction_by' => $transaction_by,
                                'transaction_by_name' => $transaction_by_name,
                                'created_by' => $created_by,
                                'created_by_name' => $created_by_name,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);

                        db::connection('ympimis_2')->table('kanban_inout_intransits')
                            ->where('tag', $tag)
                            ->delete();

                        $inventory_material = db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->where('material_number', '=', $material_number)
                            ->where('location', '=', $location[0] . '-MATERIAL')
                            ->first();

                        if ($inventory_material) {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $material_number)
                                ->where('location', '=', $location[0] . '-MATERIAL')
                                ->update([
                                    'quantity' => $inventory_material->quantity + $quantity,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        } else {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->insert([
                                    'material_number' => $material_number,
                                    'material_description' => $material_description,
                                    'quantity' => $quantity,
                                    'location' => $location[0] . '-MATERIAL',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }

                    }
                }
                if ($category == 'EXPORT') {
                    db::connection('ympimis_2')->table('kanban_inout_logs')
                        ->insert([
                            'tag' => $tag,
                            'material_number' => $material_number,
                            'material_description' => $material_description,
                            'issue_location' => $issue_location,
                            'receive_location' => $receive_location,
                            'quantity' => $quantity,
                            'remark' => $remark,
                            'category' => $category,
                            'transaction_by' => $transaction_by,
                            'transaction_by_name' => $transaction_by_name,
                            'created_by' => $created_by,
                            'created_by_name' => $created_by_name,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                    db::connection('ympimis_2')->table('kanban_inout_intransits')
                        ->insert([
                            'tag' => $tag,
                            'material_number' => $material_number,
                            'material_description' => $material_description,
                            'location' => $location[0] . '-INTRANSIT-EXPORT',
                            'issue_location' => $issue_location,
                            'receive_location' => $receive_location,
                            'quantity' => $quantity,
                            'transaction_by' => $transaction_by,
                            'transaction_by_name' => $transaction_by_name,
                            'created_by' => $created_by,
                            'created_by_name' => $created_by_name,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                    $inventory_finish = db::connection('ympimis_2')->table('kanban_inout_inventories')
                        ->where('material_number', '=', $material_number)
                        ->where('location', '=', $location[0] . '-FINISH-EXPORT')
                        ->first();

                    if ($inventory_finish) {
                        db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->where('material_number', '=', $material_number)
                            ->where('location', '=', $location[0] . '-FINISH-EXPORT')
                            ->update([
                                'quantity' => $inventory_finish->quantity - $quantity,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    } else {
                        db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->insert([
                                'material_number' => $material_number,
                                'material_description' => $material_description,
                                'quantity' => $quantity * -1,
                                'location' => $location[0] . '-FINISH-EXPORT',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    }
                }
                if ($category == 'SLIP KHUSUS') {
                    db::connection('ympimis_2')->table('kanban_inout_logs')
                        ->insert([
                            'tag' => $tag,
                            'material_number' => $material_number,
                            'material_description' => $material_description,
                            'issue_location' => $issue_location,
                            'receive_location' => $receive_location,
                            'quantity' => $quantity,
                            'remark' => $remark,
                            'category' => $category,
                            'transaction_by' => $transaction_by,
                            'transaction_by_name' => $transaction_by_name,
                            'created_by' => $created_by,
                            'created_by_name' => $created_by_name,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                    if ($location[1] == 'OUT') {

                        db::connection('ympimis_2')->table('kanban_inout_intransits')
                            ->insert([
                                'tag' => $tag,
                                'material_number' => $material_number,
                                'material_description' => $material_description,
                                'location' => $location[0] . '-INTRANSIT-KHUSUS',
                                'issue_location' => $issue_location,
                                'receive_location' => $receive_location,
                                'quantity' => $quantity,
                                'transaction_by' => $transaction_by,
                                'transaction_by_name' => $transaction_by_name,
                                'created_by' => $created_by,
                                'created_by_name' => $created_by_name,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);

                        $inventory_intransit = db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->where('material_number', '=', $material_number)
                            ->where('location', '=', $location[0] . '-INTRANSIT-KHUSUS')
                            ->first();

                        if ($inventory_intransit) {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $material_number)
                                ->where('location', '=', $location[0] . '-INTRANSIT-KHUSUS')
                                ->update([
                                    'quantity' => $inventory_intransit->quantity + $quantity,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        } else {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->insert([
                                    'material_number' => $material_number,
                                    'material_description' => $material_description,
                                    'quantity' => $quantity,
                                    'location' => $location[0] . '-INTRANSIT-KHUSUS',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }

                    }

                    if ($location[1] == 'IN') {

                        db::connection('ympimis_2')->table('kanban_inout_intransits')
                            ->where('tag', '=', $tag)
                            ->delete();

                        $inventory_intransit = db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->where('material_number', '=', $material_number)
                            ->where('location', '=', $location[0] . '-INTRANSIT-KHUSUS')
                            ->first();

                        if ($inventory_intransit) {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $material_number)
                                ->where('location', '=', $location[0] . '-INTRANSIT-KHUSUS')
                                ->update([
                                    'quantity' => $inventory_intransit->quantity - $quantity,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        } else {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->insert([
                                    'material_number' => $material_number,
                                    'material_description' => $material_description,
                                    'quantity' => $quantity * -1,
                                    'location' => $location[0] . '-INTRANSIT-KHUSUS',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }

                    }
                }
            }

            DB::connection('ympimis_2')->commit();
            $response = array(
                'status' => true,
                'message' => 'Pencatatan material berhasil disimpan',
            );

            return Response::json($response);

        } catch (\PDOException$e) {
            DB::connection('ympimis_2')->rollBack();
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function indexMaterialInOut(Request $request)
    {
        $type = $request->get('type');

        $employees = db::select("
            SELECT
            es.employee_id,
            es.`name`,
            es.department,
            e.tag
            FROM
            employee_syncs AS es
            LEFT JOIN employees AS e ON e.employee_id = es.employee_id
            WHERE
            (
                es.end_date IS NULL
                OR es.end_date >= date(
                    now()))
            AND es.department IS NOT NULL
            AND es.grade_code <> 'J0-'
            AND es.grade_code <> 'OS'");

        if ($type == 'in') {

            $title = "Pencatatan Material Masuk";
            $title_jp = "きっと";
            $view = "transactions.in_out.in";
            $view = "transactions.in_out.in";

        } else if ($type == 'out') {

            $title = "Pencatatan Material Keluar";
            $title_jp = "きっと";
            $view = "transactions.in_out.out";

        } else if ($type == 'exchange') {

            $title = "Pencatatan Pertukaran Kanban";
            $title_jp = "きっと";
            $view = "transactions.in_out.exchange";

        }

        return view($view, array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employees' => $employees,
        ))->with('page', 'Completion');

    }

    public function fetchInOutIntransitKhusus(Request $request)
    {
        $remark = $request->get('remark');
        $location = explode('-', $remark);

        $intransit = db::connection('ympimis_2')
            ->table('kanban_inout_intransits')
            ->leftJoin('kanban_inout_unusuals', 'kanban_inout_unusuals.slip_number', '=', 'kanban_inout_intransits.tag')
            ->where('kanban_inout_intransits.location', 'LIKE', '%KHUSUS%');

        if ($remark == 'BPP-IN') {
            $intransit = $intransit->where('kanban_inout_intransits.receive_location', 'LIKE', '%A1%');
            $intransit = $intransit->orWhere('kanban_inout_intransits.receive_location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'BPP-OUT') {
            $intransit = $intransit->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'WLD-IN') {
            $intransit = $intransit->where('kanban_inout_intransits.receive_location', 'LIKE', '%21%');
            $intransit = $intransit->orWhere('kanban_inout_intransits.receive_location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'WLD-OUT') {
            $intransit = $intransit->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'BFF-IN') {
            $intransit = $intransit->where('kanban_inout_intransits.receive_location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'BFF-OUT') {
            $intransit = $intransit->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'LCQ-IN') {
            $intransit = $intransit->where('kanban_inout_intransits.receive_location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'LCQ-OUT') {
            $intransit = $intransit->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'PLT-IN') {
            $intransit = $intransit->where('kanban_inout_intransits.receive_location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'PLT-OUT') {
            $intransit = $intransit->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'FA-IN') {
            $intransit = $intransit->where('kanban_inout_intransits.receive_location', 'LIKE', '%91%');
            $intransit = $intransit->orWhere('kanban_inout_intransits.receive_location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'FA-OUT') {
            $intransit = $intransit->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        }
        $intransit = $intransit->select('kanban_inout_intransits.*', 'kanban_inout_unusuals.valid_to');
        $intransit = $intransit->get();

        $response = array(
            'status' => true,
            'intransit' => $intransit,
            'last_updated' => date('Y-m-d H:i:s'),
        );
        return Response::json($response);

    }

    public function fetchInOutIntransit(Request $request)
    {

        $remark = $request->get('remark');
        $location = explode('-', $remark);

        // RETURN
        $intransit_return = db::connection('ympimis_2')->table('kanban_inout_intransits');
        $intransit_return = $intransit_return->leftJoin(
            db::raw("(SELECT DISTINCT material_child, material_child_description, material_child_location2 AS `in`, material_parent_location2 AS `out` FROM `kanban_inout_boms`) AS bom"),
            function ($join) {
                $join->on('bom.material_child', '=', 'kanban_inout_intransits.material_number');
            });
        $intransit_return = $intransit_return->where('kanban_inout_intransits.location', 'LIKE', '%RETURN%');
        if ($remark == 'BPP-IN') {
            $intransit_return = $intransit_return->where('bom.in', 'LIKE', '%BPP%');
        } elseif ($remark == 'BPP-OUT') {
            $intransit_return = $intransit_return->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'WLD-IN') {
            $intransit_return = $intransit_return->where('bom.in', 'LIKE', '%WLD%');
        } elseif ($remark == 'WLD-OUT') {
            $intransit_return = $intransit_return->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'BFF-IN') {
            $intransit_return = $intransit_return->where('bom.in', 'LIKE', '%BFF%');
        } elseif ($remark == 'BFF-OUT') {
            $intransit_return = $intransit_return->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'LCQ-IN') {
            $intransit_return = $intransit_return->where('bom.in', 'LIKE', '%LCQ%');
        } elseif ($remark == 'LCQ-OUT') {
            $intransit_return = $intransit_return->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'PLT-IN') {
            $intransit_return = $intransit_return->where('bom.in', 'LIKE', '%PLT%');
        } elseif ($remark == 'PLT-OUT') {
            $intransit_return = $intransit_return->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'FA-IN') {
            $intransit_return = $intransit_return->where('kanban_inout_intransits.receive_location', 'LIKE', '%91%');
        } elseif ($remark == 'FA-OUT') {
            $intransit_return = $intransit_return->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        }
        $intransit_return = $intransit_return->select('kanban_inout_intransits.*');

        // KANBAN
        $intransit_kanban = db::connection('ympimis_2')->table('kanban_inout_intransits');
        $intransit_kanban = $intransit_kanban->leftJoin(
            db::raw("(SELECT DISTINCT material_child, material_child_description, material_child_location2 AS `in`, material_parent_location2 AS `out` FROM `kanban_inout_boms`) AS bom"),
            function ($join) {
                $join->on('bom.material_child', '=', 'kanban_inout_intransits.material_number');
            });
        $intransit_kanban = $intransit_kanban->where('kanban_inout_intransits.location', 'LIKE', '%KANBAN%');
        if ($remark == 'BPP-IN') {
            $intransit_kanban = $intransit_kanban->where('bom.out', 'LIKE', '%BPP%');
        } elseif ($remark == 'BPP-OUT') {
            $intransit_kanban = $intransit_kanban->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'WLD-IN') {
            $intransit_kanban = $intransit_kanban->where('bom.out', 'LIKE', '%WLD%');
        } elseif ($remark == 'WLD-OUT') {
            $intransit_kanban = $intransit_kanban->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'BFF-IN') {
            $intransit_kanban = $intransit_kanban->where('bom.out', 'LIKE', '%BFF%');
        } elseif ($remark == 'BFF-OUT') {
            $intransit_kanban = $intransit_kanban->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'LCQ-IN') {
            $intransit_kanban = $intransit_kanban->where('bom.out', 'LIKE', '%LCQ%');
        } elseif ($remark == 'LCQ-OUT') {
            $intransit_kanban = $intransit_kanban->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'PLT-IN') {
            $intransit_kanban = $intransit_kanban->where('bom.out', 'LIKE', '%PLT%');
        } elseif ($remark == 'PLT-OUT') {
            $intransit_kanban = $intransit_kanban->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'FA-IN') {
            $intransit_kanban = $intransit_kanban->where('bom.out', 'LIKE', '%FA%');
        } elseif ($remark == 'FA-OUT') {
            $intransit_kanban = $intransit_kanban->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        }
        $intransit_kanban = $intransit_kanban->select('kanban_inout_intransits.*');

        // KECUALI KANBAN DAN RETURN
        $intransit = db::connection('ympimis_2')->table('kanban_inout_intransits');
        $intransit = $intransit->where(function ($query) {
            $query->where('kanban_inout_intransits.location', 'LIKE', '%SCRAP%')
                ->orWhere('kanban_inout_intransits.location', 'LIKE', '%REPAIR%')
                ->orWhere('kanban_inout_intransits.location', 'LIKE', '%KHUSUS%')
                ->orWhere('kanban_inout_intransits.location', 'LIKE', '%EXPORT%');
        });
        if ($remark == 'BPP-IN') {
            $intransit = $intransit->where('kanban_inout_intransits.receive_location', 'LIKE', '%A1%');
            $intransit = $intransit->orWhere('kanban_inout_intransits.receive_location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'BPP-OUT') {
            $intransit = $intransit->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'WLD-IN') {
            $intransit = $intransit->where('kanban_inout_intransits.receive_location', 'LIKE', '%21%');
            $intransit = $intransit->orWhere('kanban_inout_intransits.receive_location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'WLD-OUT') {
            $intransit = $intransit->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'BFF-IN') {
            $intransit = $intransit->where('kanban_inout_intransits.receive_location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'BFF-OUT') {
            $intransit = $intransit->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'LCQ-IN') {
            $intransit = $intransit->where('kanban_inout_intransits.receive_location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'LCQ-OUT') {
            $intransit = $intransit->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'PLT-IN') {
            $intransit = $intransit->where('kanban_inout_intransits.receive_location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'PLT-OUT') {
            $intransit = $intransit->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'FA-IN') {
            $intransit = $intransit->where('kanban_inout_intransits.receive_location', 'LIKE', '%91%');
            $intransit = $intransit->orWhere('kanban_inout_intransits.receive_location', 'LIKE', '%' . $location[0] . '%');
        } elseif ($remark == 'FA-OUT') {
            $intransit = $intransit->where('kanban_inout_intransits.location', 'LIKE', '%' . $location[0] . '%');
        }
        $intransit = $intransit->union($intransit_return);
        $intransit = $intransit->union($intransit_kanban);
        $intransit = $intransit->get();

        $response = array(
            'status' => true,
            'intransit' => $intransit,
            'last_updated' => date('Y-m-d H:i:s'),
        );
        return Response::json($response);

    }

    public function fetchMaterialTagOutReturn(Request $request)
    {

        if (substr($request->get('return_id'), 0, 2) != 'RE') {
            $response = array(
                'status' => false,
                'message' => "QRcode return salah.",
            );
            return Response::json($response);
        }

        $return_id = substr($request->get('return_id'), 2);

        $return = db::table('return_lists')->where('return_lists.id', '=', $return_id)
            ->select(db::raw('id as return_id'), 'material_number', 'material_description', 'issue_location', 'receive_location', 'quantity')
            ->first();

        if (!$return) {
            $return = db::table('return_logs')->where('return_logs.return_id', '=', $return_id)
                ->select('return_id', 'material_number', 'material_description', 'issue_location', 'receive_location', 'quantity')
                ->first();
        }

    }

    public function fetchMaterialTagOutScrap(Request $request)
    {

    }

    public function fetchMaterialTagOutRepair(Request $request)
    {

    }

    public function fetchMaterialTagOut(Request $request)
    {

        $tag = $request->get('tag');

        $transfer = db::connection('kitto')->table('transfers')
            ->leftJoin('completions', 'completions.id', '=', 'transfers.completion_id')
            ->leftJoin('materials', 'materials.id', '=', 'transfers.material_id')
            ->where('completions.barcode_number', '=', $tag)
            ->select(
                'materials.material_number',
                'materials.description',
                'materials.remark',
                'transfers.issue_location',
                'transfers.receive_location',
                'transfers.lot_transfer'
            )
            ->first();

        if (!$transfer) {
            $response = array(
                'status' => false,
                'message' => 'Data kanban tidak ditemukan.',
            );
            return Response::json($response);
        }

        $inventory = [];
        if ($transfer->issue_location != 'SXA1' && $transfer->issue_location != 'FLA1') {
            $inventory = db::connection('ympimis_2')->table('kanban_inout_inventories')
                ->where('tag', '=', $tag)
                ->where('quantity', '>', 0)
                ->first();

            if (!$inventory) {
                $response = array(
                    'status' => false,
                    'message' => 'Stok material tidak ada.',
                );
                return Response::json($response);
            }
        }

        $inout = [];
        if ($transfer->issue_location != 'SXA1' && $transfer->issue_location != 'FLA1') {
            $inout = db::connection('ympimis_2')->table('kanban_inouts')
                ->where('tag', '=', $tag)
                ->orderBy('created_at', 'DESC')
                ->limit(1)
                ->get();

            if (count($inout) <= 0) {
                $response = array(
                    'status' => false,
                    'message' => 'Stok material tidak ada.',
                );
                return Response::json($response);
            }
        }

        $boms = db::connection('ympimis_2')->table('kanban_inout_boms')
            ->where('material_child', '=', $transfer->material_number)
            ->where('material_parent_location', '=', $transfer->receive_location)
            ->get();

        $material_parents = array();

        for ($i = 0; $i < count($boms); $i++) {
            array_push($material_parents, $boms[$i]->material_parent);
        }

        $inventory_parents = db::connection('ympimis_2')->table('kanban_inout_inventories')
            ->whereIn('material_number', $material_parents)
            ->get();

        $response = array(
            'status' => true,
            'transfer' => $transfer,
            'inventory' => $inventory,
            'inout' => $inout,
            'boms' => $boms,
            'inventory_parents' => $inventory_parents,
        );
        return Response::json($response);

    }

    public function updateMaterialTagExchange(Request $request)
    {
        $kanban_before = $request->get('kanban_before');
        $kanban_after = $request->get('kanban_after');

        try {

            $tag_before = db::connection('ympimis_2')
                ->table('kanban_inout_exchanges')
                ->where('tag_after', '=', $kanban_before)
                ->first();

            $tag_after = db::connection('kitto')
                ->table('completions')
                ->leftJoin('materials', 'materials.id', '=', 'completions.material_id')
                ->where('completions.barcode_number', '=', $kanban_after)
                ->select(
                    'materials.material_number',
                    'materials.description',
                    'materials.remark',
                    'materials.location',
                    'completions.lot_completion'
                )
                ->first();

            if (!$tag_before || !$tag_after) {
                $response = array(
                    'status' => false,
                    'message' => 'Data kanban tidak ditemukan.',
                );
                return Response::json($response);
            }

            // INSERT EXCHANGE
            $update_exchanges = db::connection('ympimis_2')->table('kanban_inout_exchanges')
                ->insert([
                    'transaction_number' => $tag_before->transaction_number,
                    'tag_before' => $tag_before->tag_after,
                    'material_number_before' => $tag_before->material_number_after,
                    'material_description_before' => $tag_before->material_description_after,
                    'storage_location_before' => $tag_before->storage_location_after,
                    'location_before' => $tag_before->location_after,
                    'quantity_before' => abs($tag_before->quantity_after),
                    'tag_after' => $kanban_after,
                    'material_number_after' => $tag_after->material_number,
                    'material_description_after' => $tag_after->description,
                    'storage_location_after' => $tag_after->location,
                    'location_after' => $tag_before->storage_location_after,
                    'quantity_after' => abs($tag_after->lot_completion),
                    'transaction_by' => $request->get('transaction_by'),
                    'transaction_by_name' => $request->get('transaction_by_name'),
                    'created_by' => Auth::user()->username,
                    'created_by_name' => Auth::user()->name,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            // DELEATE OLD EXCHANGE
            $delete_before = db::connection('ympimis_2')->table('kanban_inout_exchanges')
                ->where('tag_after', '=', $kanban_before)
                ->delete();

            DB::connection('ympimis_2')->commit();

            $response = array(
                'status' => true,
                'message' => 'Data berhasil dikonfirmasi.',
            );
            return Response::json($response);

        } catch (\PDOException$e) {

            DB::connection('ympimis_2')->rollBack();
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);

        }
    }

    public function inputMaterialTagIn(Request $request)
    {
        $tag = $request->get('tag');

        $inventory = db::connection('ympimis_2')->table('kanban_inout_inventories')
            ->where('tag', '=', $tag)
            ->where('location', '=', 'INTRANSIT')
            ->first();

        if ($inventory == null) {
            $response = array(
                'status' => false,
                'message' => 'Kanban belum dicatat keluar',
            );
            return Response::json($response);
        }

        try {
            DB::connection('ympimis_2')->beginTransaction();

            $location = '';
            if (str_contains(substr($tag, 0, 4), ('51'))) {
                $location = 'MIDDLE-MATERIAL AWAL';

            } else {
                $completion = db::connection('kitto')->table('completions')
                    ->leftJoin('materials', 'materials.id', '=', 'completions.material_id')
                    ->where('barcode_number', '=', $tag)
                    ->select(
                        'completions.barcode_number',
                        'materials.material_number',
                        'materials.description',
                        'materials.remark',
                        'materials.location'
                    )
                    ->first();

                if (!$completion) {
                    $response = array(
                        'status' => false,
                        'message' => 'Data kanban tidak ditemukan.',
                    );
                    return Response::json($response);
                }

                if (str_contains($completion->location, ('A1'))) {
                    $location = 'BPP-MATERIAL AWAL';
                } else if (str_contains($completion->location, ('21'))) {
                    $location = 'WELDING-MATERIAL AWAL';
                }

            }

            // UPDATE INVENTORY MATERIAL MASUK (WLD) TAG BARU
            $exchange = db::connection('ympimis_2')->table('kanban_inout_exchanges')
                ->where('tag_after', '=', $tag)
                ->first();

            if ($inventory) {
                // $update_inventory_after = db::connection('ympimis_2')->table('kanban_inout_inventories')
                //     ->where('tag', '=', $tag)
                //     ->increment('quantity', $exchange->quantity_after);

                $update_inventory_after = db::connection('ympimis_2')->table('kanban_inout_inventories')
                    ->where('tag', '=', $tag)
                    ->update([
                        'location' => $location,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'last_action' => 'IN',
                    ]);

            }

            // INSERT LOG MASUK
            $insert_inout = db::connection('ympimis_2')->table('kanban_inouts')
                ->insert([
                    'transaction_number' => $exchange->transaction_number,
                    'tag' => $exchange->tag_after,
                    'material_number' => $exchange->material_number_after,
                    'material_description' => $exchange->material_description_after,
                    'storage_location' => $exchange->storage_location_after,
                    'location' => $location,
                    'quantity' => $exchange->quantity_after,
                    'transaction_by' => $request->get('transaction_by'),
                    'transaction_by_name' => $request->get('transaction_by_name'),
                    'created_by' => Auth::user()->username,
                    'created_by_name' => Auth::user()->name,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            DB::connection('ympimis_2')->commit();

            $response = array(
                'status' => true,
                'message' => 'Data berhasil dikonfirmasi.',
            );
            return Response::json($response);

        } catch (\PDOException$e) {

            DB::connection('ympimis_2')->rollBack();
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);

        }
    }

    public function inputMaterialTagOut(Request $request)
    {
        $tag_childs = $request->get('tag_childs');
        $tag_parents = $request->get('tag_parents');

        if ($tag_childs[0]['storage_location'] == 'SXA1' || $tag_childs[0]['storage_location'] == 'FLA1') {
            $code_generator = CodeGenerator::where('note', '=', 'kanban_inout')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $transaction_number = $code_generator->prefix . $number;
        } else {
            $transaction_number = $tag_childs[0]['transaction_number'];
        }

        try {

            DB::connection('ympimis_2')->beginTransaction();

            for ($i = 0; $i < count($tag_childs); $i++) {

                // INPUT LOG MATERIAL KELUAR (BPRO) TAG LAMA
                $insert_inout = db::connection('ympimis_2')->table('kanban_inouts')
                    ->insert([
                        'transaction_number' => $transaction_number,
                        'tag' => $tag_childs[$i]['tag'],
                        'material_number' => $tag_childs[$i]['material_number'],
                        'material_description' => $tag_childs[$i]['material_description'],
                        'storage_location' => $tag_childs[$i]['storage_location'],
                        'location' => $tag_childs[$i]['location'],
                        'quantity' => abs($tag_childs[$i]['quantity']) * -1,
                        'transaction_by' => $tag_childs[$i]['transaction_by'],
                        'transaction_by_name' => $tag_childs[$i]['transaction_by_name'],
                        'created_by' => Auth::user()->username,
                        'created_by_name' => Auth::user()->name,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            for ($i = 0; $i < count($tag_parents); $i++) {
                $inventory_parent = db::connection('ympimis_2')->table('kanban_inout_inventories')
                    ->where('tag', '=', $tag_parents[$i]['tag'])
                    ->first();

                // UPDATE INVENTORY MATERIAL KELUAR (BPP) DENGAN TAG BARU
                if ($inventory_parent) {
                    $update_inventory = db::connection('ympimis_2')->table('kanban_inout_inventories')
                        ->where('tag', '=', $tag_parents[$i]['tag'])
                        ->increment('quantity', $tag_parents[$i]['quantity'])
                        ->update([
                            'location' => $tag_parents[$i]['location'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                } else {
                    $update_inventory = db::connection('ympimis_2')->table('kanban_inout_inventories')
                        ->insert([
                            'tag' => $tag_parents[$i]['tag'],
                            'material_number' => $tag_parents[$i]['material_number'],
                            'material_description' => $tag_parents[$i]['material_description'],
                            'storage_location' => $tag_parents[$i]['storage_location'],
                            'quantity' => $tag_parents[$i]['quantity'],
                            'location' => $tag_parents[$i]['location'],
                            'last_action' => $tag_parents[$i]['last_action'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }

                // DELETE EXCHANGE
                $exchange = db::connection('ympimis_2')->table('kanban_inout_exchanges')
                    ->where('tag_after', '=', $tag_parents[$i]['tag'])
                    ->delete();

            }

            for ($i = 0; $i < count($tag_childs); $i++) {
                for ($j = 0; $j < count($tag_parents); $j++) {
                    db::connection('ympimis_2')->table('kanban_inout_exchanges')->insert([
                        'transaction_number' => $transaction_number,
                        'tag_before' => $tag_childs[$i]['tag'],
                        'material_number_before' => $tag_childs[$i]['material_number'],
                        'material_description_before' => $tag_childs[$i]['material_description'],
                        'storage_location_before' => $tag_childs[$i]['storage_location'],
                        'location_before' => $tag_childs[$i]['location'],
                        'quantity_before' => abs($tag_childs[$i]['quantity']),
                        'tag_after' => $tag_parents[$j]['tag'],
                        'material_number_after' => $tag_parents[$j]['material_number'],
                        'material_description_after' => $tag_parents[$j]['material_description'],
                        'storage_location_after' => $tag_parents[$j]['storage_location'],
                        'location_after' => $tag_parents[$j]['location'],
                        'quantity_after' => abs($tag_parents[$j]['quantity']),
                        'transaction_by' => $tag_childs[$i]['transaction_by'],
                        'transaction_by_name' => $tag_childs[$i]['transaction_by_name'],
                        'created_by' => Auth::user()->username,
                        'created_by_name' => Auth::user()->name,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            if ($tag_childs[0]['storage_location'] == 'SXA1' || $tag_childs[0]['storage_location'] == 'FLA1') {
                $code_generator->index = $code_generator->index + 1;
                $code_generator->save();
            }

            DB::connection('ympimis_2')->commit();

            $response = array(
                'status' => true,
                'message' => 'Data berhasil dikonfirmasi.',
            );
            return Response::json($response);

        } catch (\PDOException$e) {

            DB::connection('ympimis_2')->rollBack();
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);

        }

    }

    public function deleteIntransit(Request $request)
    {

        $intransit = DB::connection('ympimis_2')
            ->table('kanban_inout_intransits')
            ->where('id', $request->get('id'))
            ->first();

        try {
            DB::connection('ympimis_2')->beginTransaction();

            $tag = $intransit->tag;
            $material_number = $intransit->material_number;
            $material_description = $intransit->material_description;
            $issue_location = $intransit->issue_location;
            $receive_location = $intransit->receive_location;
            $quantity = $intransit->quantity * -1;
            $transaction_by = $intransit->transaction_by;
            $transaction_by_name = $intransit->transaction_by_name;
            $created_by = Auth::user()->username;
            $created_by_name = Auth::user()->name;

            $location = explode('-', $intransit->location);
            $remark = $location[0] . '-OUT';
            $category = $location[2];

            if ($category == 'KANBAN') {
                if ($remark == 'BPP-OUT' || $remark == 'WLD-OUT' || $remark == 'PLT-OUT' || $remark == 'LCQ-OUT') {
                    db::connection('ympimis_2')->table('kanban_inout_logs')->insert([
                        'tag' => $tag,
                        'material_number' => $material_number,
                        'material_description' => $material_description,
                        'issue_location' => $issue_location,
                        'receive_location' => $receive_location,
                        'quantity' => $quantity,
                        'remark' => $remark,
                        'category' => $category . '-CANCEL',
                        'transaction_by' => $transaction_by,
                        'transaction_by_name' => $transaction_by_name,
                        'created_by' => $created_by,
                        'created_by_name' => $created_by_name,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    $inventory_finish = db::connection('ympimis_2')->table('kanban_inout_inventories')
                        ->where('material_number', '=', $material_number)
                        ->where('location', '=', $location[0] . '-FINISH')
                        ->first();

                    $inventory_intransit = db::connection('ympimis_2')->table('kanban_inout_inventories')
                        ->where('material_number', '=', $material_number)
                        ->where('location', '=', $location[0] . '-INTRANSIT')
                        ->first();

                    if ($inventory_finish) {
                        db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->where('material_number', '=', $material_number)
                            ->where('location', '=', $location[0] . '-FINISH')
                            ->update([
                                'quantity' => $inventory_finish->quantity + $quantity,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    } else {
                        db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->insert([
                                'material_number' => $material_number,
                                'material_description' => $material_description,
                                'quantity' => $quantity,
                                'location' => $location[0] . '-FINISH',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    }

                    if ($inventory_intransit) {
                        db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->where('material_number', '=', $material_number)
                            ->where('location', '=', $location[0] . '-INTRANSIT-KANBAN')
                            ->update([
                                'quantity' => $inventory_intransit->quantity - $quantity,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    } else {
                        db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->insert([
                                'material_number' => $material_number,
                                'material_description' => $material_description,
                                'quantity' => $quantity * -1,
                                'location' => $location[0] . '-INTRANSIT-KANBAN',
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    }

                    DB::connection('ympimis_2')
                        ->table('kanban_inout_intransits')
                        ->where('id', $request->get('id'))
                        ->delete();

                }
                if ($remark == 'BFF-OUT') {
                    $boms = db::connection('ympimis_2')->table('kanban_inout_boms')
                        ->where('material_parent', '=', $material_number)
                        ->get();

                    foreach ($boms as $bom) {
                        db::connection('ympimis_2')->table('kanban_inout_logs')->insert([
                            'tag' => $tag,
                            'material_number' => $bom->material_child,
                            'material_description' => $bom->material_child_description,
                            'issue_location' => $issue_location,
                            'receive_location' => $receive_location,
                            'quantity' => $quantity,
                            'remark' => $remark,
                            'category' => $category . '-CANCEL',
                            'transaction_by' => $transaction_by,
                            'transaction_by_name' => $transaction_by_name,
                            'created_by' => $created_by,
                            'created_by_name' => $created_by_name,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                        $inventory_intransit = db::connection('ympimis_2')->table('kanban_inout_inventories')
                            ->where('material_number', '=', $bom->material_child)
                            ->where('location', '=', $location[0] . '-INTRANSIT-KANBAN')
                            ->first();

                        if ($inventory_intransit) {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $bom->material_child)
                                ->where('location', '=', $location[0] . '-INTRANSIT-KANBAN')
                                ->update([
                                    'quantity' => $inventory_intransit->quantity - $quantity,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        } else {
                            db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->insert([
                                    'material_number' => $bom->material_child,
                                    'material_description' => $bom->material_child_description,
                                    'quantity' => $quantity * -1,
                                    'location' => $location[0] . '-INTRANSIT-KANBAN',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }

                        $boms2 = db::connection('ympimis_2')->table('kanban_inout_boms')
                            ->where('material_parent', '=', $bom->material_child)
                            ->get();

                        foreach ($boms2 as $bom2) {
                            $inventory_material = db::connection('ympimis_2')->table('kanban_inout_inventories')
                                ->where('material_number', '=', $bom2->material_child)
                                ->where('location', '=', $location[0] . '-MATERIAL')
                                ->first();

                            if ($inventory_material) {
                                db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->where('material_number', '=', $bom2->material_child)
                                    ->where('location', '=', $location[0] . '-MATERIAL')
                                    ->update([
                                        'quantity' => $inventory_material->quantity + $quantity,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                            } else {
                                db::connection('ympimis_2')->table('kanban_inout_inventories')
                                    ->insert([
                                        'material_number' => $bom->material_child,
                                        'material_description' => $bom->material_child_description,
                                        'quantity' => $quantity,
                                        'location' => $location[0] . '-MATERIAL',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                            }

                        }

                        DB::connection('ympimis_2')
                            ->table('kanban_inout_intransits')
                            ->where('id', $request->get('id'))
                            ->delete();

                    }

                }

            }
            if ($category == 'RETURN') {}
            if ($category == 'REPAIR') {}
            if ($category == 'SCRAP') {}
            if ($category == 'EXPORT') {}
            if ($category == 'SLIP KHUSUS') {}

            DB::connection('ympimis_2')->commit();
            $response = array(
                'status' => true,
                'message' => 'Perpindahan material dibatalkan',
            );

            return Response::json($response);

        } catch (\PDOException$e) {
            DB::connection('ympimis_2')->rollBack();
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

}
