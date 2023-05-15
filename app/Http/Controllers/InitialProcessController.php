<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Employee;
use App\InitialSafetyStock;
use App\Material;
use App\OriginGroup;
use App\SandingCheck;
use App\SandingCheckFinish;
use App\SandingCheckMaster;
use App\SandingCheckFinding;
use App\EmployeeSync;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use DataTables;
use Response;
use DateTime;
use App\CodeGenerator;
use Carbon\Carbon;
use App\Mail\SendEmail;

class InitialProcessController extends Controller
{

	public function __construct(){
		$this->middleware('auth');
		$this->location = [
			'machining',
			'senban',
			'sanding',
			'press'
		];
	}

	public function index($id)
	{
		if ($id == 'bpro_fl') {
			$title = 'Body Process Flute';
			$title_jp = '??';
			return view('processes.initial.index_bpro_fl', array(
				'title' => $title,
				'title_jp' => $title_jp,
			))->with('page', 'Body Process FL');
		}
		if ($id == 'lotting') {
			$title = 'Material Process Lotting';
			$title_jp = '??';
			return view('processes.initial.index_lotting', array(
				'title' => $title,
				'title_jp' => $title_jp,
			))->with('page', 'Material Process Lotting');
		}
		if ($id == 'press') {
			$title = 'Material Process Press';
			$title_jp = 'プレスマテリアルプロセス';
			return view('processes.initial.index_press', array(
				'title' => $title,
				'title_jp' => $title_jp,
			))->with('page', 'Material Process Press');
		}
		if ($id == 'sanding') {
			$title = 'Material Process Sanding';
			$title_jp = '??';
			return view('processes.initial.index_sanding', array(
				'title' => $title,
				'title_jp' => $title_jp,
			))->with('page', 'Material Process Sanding');
		}
		if ($id == "material_process"){
			$title = "Material Process";
			$title_jp = "";

			return view('processes.initial.index_material_process', array(
				'title' => $title,
				'title_jp' => $title_jp,
			))->with('head', 'Material Process');
		}
	}

	public function indexStockMonitoring($id)
	{
		$title = 'Initial Process Stock Monitoring';
		$title_jp = '最初工程の在庫監視';

		if ($id == 'mpro') {
			$location = "'SXA0', 'SXA2', 'FLA0', 'FLA2', 'CLA0', 'CLA2', 'ZPA0', 'VNA0'";
			$locs = ["'SXA0'", "'SXA2'", "'FLA0'", "'FLA2'", "'CLA0'", "'CLA2'", "'ZPA0'", "'VNA0'"];
		}

		return view('processes.initial.display.stock_monitoring', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'location' => $location,
			'locs' => $locs,
		))->with('head', 'Initial Process');
	}

	public function indexTableStockMonitoring($id)
	{
		$title = 'Initial Process Stock Monitoring';
		$title_jp = '最初工程の在庫監視';

		if ($id == 'mpro') {
			$location = "'SXA0', 'SXA2', 'FLA0', 'FLA2', 'CLA0', 'CLA2', 'ZPA0', 'VNA0'";
			$locs = ["'SXA0'", "'SXA2'", "'FLA0'", "'FLA2'", "'CLA0'", "'CLA2'", "'ZPA0'", "'VNA0'"];
		}

		return view('processes.initial.display.stock_table_monitoring', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'location' => $location,
			'locs' => $locs,
		))->with('head', 'Initial Process');
	}

	public function indexStockTrend($id)
	{
		$title = 'Initial Process Stock Trend';
		$title_jp = '最初工程の在庫トレンド';

		if ($id == 'mpro') {
			$location = "'FLA0','CLA0','SXA0','VNA0'";
			$locs = ["'FLA0'", "'CLA0'", "'SXA0'", "'VNA0'"];
		}

		return view('processes.initial.display.stock_trend', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'location' => $location,
			'locs' => $locs,
		))->with('head', 'Initial Process');
	}

	public function indexStockMaster()
	{
		$title = 'Initial Process Stock';
		$title_jp = '?';

		$materials = Material::orderBy('material_number', 'ASC')->get();
		$origin_groups = OriginGroup::orderBy('origin_group_code', 'ASC')->get();

		return view('initial_safety.index', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'materials' => $materials,
			'origin_groups' => $origin_groups,
		))->with('page', 'Safety Stock');
	}

	public function fetchStockTrend(Request $request)
	{
		$query = "select category, count(material_number) as material, date_stock from

		(
		select inventories.material_number, inventories.description, inventories.location, inventories.date_stock, inventories.act_stock, stocks.quantity as safety_stock, if(ceiling(inventories.act_stock/stocks.quantity)=0, 0, if(inventories.act_stock/stocks.quantity>0 and inventories.act_stock/stocks.quantity <= 0.5, 0.5, if(inventories.act_stock/stocks.quantity > 0.5 and inventories.act_stock/stocks.quantity <= 1, 1, if(inventories.act_stock/stocks.quantity>1 and inventories.act_stock/stocks.quantity<=1.5, 1.5, if(inventories.act_stock/stocks.quantity>1.5 and inventories.act_stock/stocks.quantity<=2, 2, null))))) as stock, if(ceiling(inventories.act_stock/stocks.quantity)=0, '0Days', if(inventories.act_stock/stocks.quantity>0 and inventories.act_stock/stocks.quantity <= 0.5, '<0.5Days', if(inventories.act_stock/stocks.quantity > 0.5 and inventories.act_stock/stocks.quantity <= 1, '<1Days', if(inventories.act_stock/stocks.quantity>1 and inventories.act_stock/stocks.quantity<=1.5, '<1.5Days', if(inventories.act_stock/stocks.quantity>1.5 and inventories.act_stock/stocks.quantity<=2, '<2Days', null))))) as category from
		(
		select ympimis.daily_stocks.material_number, kitto.materials.description, ympimis.daily_stocks.location, date(ympimis.daily_stocks.created_at) as date_stock, sum(ympimis.daily_stocks.quantity) as act_stock from ympimis.daily_stocks left join kitto.materials on kitto.materials.material_number = ympimis.daily_stocks.material_number where ympimis.daily_stocks.location in (" . $request->get('location') . ") group by ympimis.daily_stocks.material_number, ympimis.daily_stocks.location, date(ympimis.daily_stocks.created_at), kitto.materials.description
		) as inventories

		inner join

		(
		select initial_safety_stocks.material_number, initial_safety_stocks.quantity, DATE_FORMAT(valid_date, '%Y-%m') as date_stock from initial_safety_stocks where initial_safety_stocks.quantity > 0 and initial_safety_stocks.quantity is not null
		) as stocks on stocks.date_stock = DATE_FORMAT(inventories.date_stock, '%Y-%m') and stocks.material_number = inventories.material_number
		) as final
		group by category, date_stock having category is not null order by date_stock, stock asc
		";

		$stocks = db::select($query);

		$response = array(
			'status' => true,
			'stocks' => $stocks,
		);
		return Response::json($response);

	}

	public function fetchStockTrendDetail(Request $request)
	{

		$query = "select inventories.material_number, inventories.description, inventories.location, inventories.date_stock, inventories.act_stock, stocks.quantity as safety_stock, if(ceiling(inventories.act_stock/stocks.quantity)=0, 0, if(inventories.act_stock/stocks.quantity>0 and inventories.act_stock/stocks.quantity <= 0.5, 0.5, if(inventories.act_stock/stocks.quantity > 0.5 and inventories.act_stock/stocks.quantity <= 1, 1, if(inventories.act_stock/stocks.quantity>1 and inventories.act_stock/stocks.quantity<=1.5, 1.5, if(inventories.act_stock/stocks.quantity>1.5 and inventories.act_stock/stocks.quantity<=2, 2, null))))) as stock, if(ceiling(inventories.act_stock/stocks.quantity)=0, '0Days', if(inventories.act_stock/stocks.quantity>0 and inventories.act_stock/stocks.quantity <= 0.5, '<0.5Days', if(inventories.act_stock/stocks.quantity > 0.5 and inventories.act_stock/stocks.quantity <= 1, '<1Days', if(inventories.act_stock/stocks.quantity>1 and inventories.act_stock/stocks.quantity<=1.5, '<1.5Days', if(inventories.act_stock/stocks.quantity>1.5 and inventories.act_stock/stocks.quantity<=2, '<2Days', null))))) as category from
		(
		select ympimis.daily_stocks.material_number, kitto.materials.description, ympimis.daily_stocks.location, date(ympimis.daily_stocks.created_at) as date_stock, sum(ympimis.daily_stocks.quantity) as act_stock from ympimis.daily_stocks left join kitto.materials on kitto.materials.material_number = ympimis.daily_stocks.material_number where ympimis.daily_stocks.location in (" . $request->get('location') . ") group by ympimis.daily_stocks.material_number, ympimis.daily_stocks.location, date(ympimis.daily_stocks.created_at), kitto.materials.description
		) as inventories
		inner join
		(
		select initial_safety_stocks.material_number, initial_safety_stocks.quantity, DATE_FORMAT(valid_date, '%Y-%m') as date_stock from initial_safety_stocks where initial_safety_stocks.quantity > 0 and initial_safety_stocks.quantity is not null
		) as stocks on stocks.date_stock = DATE_FORMAT(inventories.date_stock, '%Y-%m') and stocks.material_number = inventories.material_number where inventories.date_stock = '" . $request->get('date_stock') . "' having category = '" . $request->get('category') . "' order by date_stock, stock asc
		";

		$stocks = db::select($query);

		$response = array(
			'status' => true,
			'stocks' => $stocks,
		);
		return Response::json($response);

	}

	public function fetchStockMonitoring(Request $request)
	{
		$now = date('Y-m');
		$query = "SELECT
		initial_helpers.orderer AS stock,
		initial_helpers.category,
		initial_helpers.remark,
		COALESCE ( initial.material, 0 ) AS material
		FROM
		initial_helpers
		LEFT JOIN (
		SELECT
		stock,
		category,
		COALESCE ( remark, 'UNIDENTIFIED' ) AS remark,
		count( material_number ) AS material
		FROM
		(
		SELECT
		inventories.material_number,
		inventories.description,
		inventories.remark,
		inventories.quantity,
		stocks.quantity AS safety,
		IF
		(
		ceiling( inventories.quantity / stocks.quantity )= 0,
		0,
		IF
		(
		inventories.quantity / stocks.quantity > 0
		AND inventories.quantity / stocks.quantity <= 0.5, 0.5, IF ( inventories.quantity / stocks.quantity > 0.5
		AND inventories.quantity / stocks.quantity <= 1, 1, IF ( inventories.quantity / stocks.quantity > 1
		AND inventories.quantity / stocks.quantity <= 1.5, 1.5, IF ( inventories.quantity / stocks.quantity > 1.5
		AND inventories.quantity / stocks.quantity <= 2, 2, IF ( inventories.quantity / stocks.quantity > 2
		AND inventories.quantity / stocks.quantity <= 2.5, 2.5, IF ( inventories.quantity / stocks.quantity > 2.5
		AND inventories.quantity / stocks.quantity <= 3, 3, IF ( inventories.quantity / stocks.quantity > 3
		AND inventories.quantity / stocks.quantity <= 3.5, 3.5, IF ( inventories.quantity / stocks.quantity > 3.5
		AND inventories.quantity / stocks.quantity <= 4, 4, IF ( inventories.quantity / stocks.quantity > 4
		AND inventories.quantity / stocks.quantity <= 4.5, 4.5, IF ( inventories.quantity / stocks.quantity > 4.5,
		4.6,
		4.6
		))))))))))) AS stock,
		IF
		(
		ceiling( inventories.quantity / stocks.quantity )= 0,
		'0Days',
		IF
		(
		inventories.quantity / stocks.quantity > 0
		AND inventories.quantity / stocks.quantity <= 0.5, '<0.5Days', IF ( inventories.quantity / stocks.quantity > 0.5
		AND inventories.quantity / stocks.quantity <= 1, '<1Days', IF ( inventories.quantity / stocks.quantity > 1
		AND inventories.quantity / stocks.quantity <= 1.5, '<1.5Days', IF ( inventories.quantity / stocks.quantity > 1.5
		AND inventories.quantity / stocks.quantity <= 2, '<2Days', IF ( inventories.quantity / stocks.quantity > 2
		AND inventories.quantity / stocks.quantity <= 2.5, '<2.5Days', IF ( inventories.quantity / stocks.quantity > 2.5
		AND inventories.quantity / stocks.quantity <= 3, '<3Days', IF ( inventories.quantity / stocks.quantity > 3
		AND inventories.quantity / stocks.quantity <= 3.5, '<3.5Days', IF ( inventories.quantity / stocks.quantity > 3.5
		AND inventories.quantity / stocks.quantity <= 4, '<4Days', IF ( inventories.quantity / stocks.quantity > 4
		AND inventories.quantity / stocks.quantity <= 4.5, '<4.5Days', IF ( inventories.quantity / stocks.quantity > 4.5,
		'>4.5Days',
		'>4.5Days'
		))))))))))) AS category
		FROM
		(
		SELECT
		kitto.inventories.material_number,
		kitto.materials.description,
		kitto.materials.remark,
		sum( kitto.inventories.lot ) AS quantity
		FROM
		kitto.inventories
		LEFT JOIN kitto.materials ON kitto.materials.material_number = kitto.inventories.material_number
		WHERE
		kitto.materials.location IN ('SXA0', 'SXA2', 'FLA0', 'FLA2', 'CLA0', 'CLA2', 'ZPA0', 'VNA0')
		GROUP BY
		kitto.inventories.material_number,
		kitto.materials.remark,
		kitto.materials.description
		) AS inventories
		INNER JOIN ( SELECT initial_safety_stocks.material_number, initial_safety_stocks.quantity FROM initial_safety_stocks WHERE DATE_FORMAT( valid_date, '%Y-%m' ) = '" . $now . "' AND initial_safety_stocks.quantity > 0 ) AS stocks ON stocks.material_number = inventories.material_number
		) AS final
		GROUP BY
		category,
		remark,
		stock
		ORDER BY
		stock ASC
		) AS initial ON initial_helpers.orderer = initial.stock
		AND initial_helpers.category = initial.category
		AND initial_helpers.remark = initial.remark
		WHERE
		initial_helpers.location = 'part process'";

		$stocks = db::select($query);

		$response = array(
			'status' => true,
			'stocks' => $stocks,
		);
		return Response::json($response);
	}

	public function fetchStockMonitoringDetail(Request $request)
	{
		$now = date('Y-m');

		$query = "SELECT
		A.material_number,
		A.description,
		A.remark,
		A.quantity,
		A.safety,
		A.stock,
		A.category,
		A.days,
		B.lot,
		B.kanban
		FROM
		(
		SELECT
		inventories.material_number,
		inventories.description,
		COALESCE ( inventories.remark, 'UNIDENTIFIED' ) AS remark,
		inventories.quantity,
		stocks.quantity AS safety,
		IF
		(
		ceiling( inventories.quantity / stocks.quantity )= 0,
		0,
		IF
		(
		inventories.quantity / stocks.quantity > 0
		AND inventories.quantity / stocks.quantity <= 0.5, 0.5, IF ( inventories.quantity / stocks.quantity > 0.5
		AND inventories.quantity / stocks.quantity <= 1, 1, IF ( inventories.quantity / stocks.quantity > 1
		AND inventories.quantity / stocks.quantity <= 1.5, 1.5, IF ( inventories.quantity / stocks.quantity > 1.5
		AND inventories.quantity / stocks.quantity <= 2, 2, IF ( inventories.quantity / stocks.quantity > 2
		AND inventories.quantity / stocks.quantity <= 2.5, 2.5, IF ( inventories.quantity / stocks.quantity > 2.5
		AND inventories.quantity / stocks.quantity <= 3, 3, IF ( inventories.quantity / stocks.quantity > 3
		AND inventories.quantity / stocks.quantity <= 3.5, 3.5, IF ( inventories.quantity / stocks.quantity > 3.5
		AND inventories.quantity / stocks.quantity <= 4, 4, IF ( inventories.quantity / stocks.quantity > 4
		AND inventories.quantity / stocks.quantity <= 4.5, 4.5, IF ( inventories.quantity / stocks.quantity > 4.5,
		4.6,
		4.6
		))))))))))) AS stock,
		IF
		(
		ceiling( inventories.quantity / stocks.quantity )= 0,
		'0Days',
		IF
		(
		inventories.quantity / stocks.quantity > 0
		AND inventories.quantity / stocks.quantity <= 0.5, '<0.5Days', IF ( inventories.quantity / stocks.quantity > 0.5
		AND inventories.quantity / stocks.quantity <= 1, '<1Days', IF ( inventories.quantity / stocks.quantity > 1
		AND inventories.quantity / stocks.quantity <= 1.5, '<1.5Days', IF ( inventories.quantity / stocks.quantity > 1.5
		AND inventories.quantity / stocks.quantity <= 2, '<2Days', IF ( inventories.quantity / stocks.quantity > 2
		AND inventories.quantity / stocks.quantity <= 2.5, '<2.5Days', IF ( inventories.quantity / stocks.quantity > 2.5
		AND inventories.quantity / stocks.quantity <= 3, '<3Days', IF ( inventories.quantity / stocks.quantity > 3
		AND inventories.quantity / stocks.quantity <= 3.5, '<3.5Days', IF ( inventories.quantity / stocks.quantity > 3.5
		AND inventories.quantity / stocks.quantity <= 4, '<4Days', IF ( inventories.quantity / stocks.quantity > 4
		AND inventories.quantity / stocks.quantity <= 4.5, '<4.5Days', IF ( inventories.quantity / stocks.quantity > 4.5,
		'>4.5Days',
		'>4.5Days'
		))))))))))) AS category,
		inventories.quantity / stocks.quantity AS days
		FROM
		(
		SELECT
		kitto.inventories.material_number,
		kitto.materials.description,
		kitto.materials.remark,
		sum( kitto.inventories.lot ) AS quantity
		FROM
		kitto.inventories
		LEFT JOIN kitto.materials ON kitto.materials.material_number = kitto.inventories.material_number
		WHERE
		kitto.materials.location IN ( " . $request->get('location') . " )
		GROUP BY
		kitto.inventories.material_number,
		kitto.materials.remark,
		kitto.materials.description
		) AS inventories
		INNER JOIN ( SELECT initial_safety_stocks.material_number, initial_safety_stocks.quantity FROM initial_safety_stocks WHERE DATE_FORMAT( valid_date, '%Y-%m' ) = '" . $now . "' AND initial_safety_stocks.quantity > 0 ) AS stocks ON stocks.material_number = inventories.material_number
		HAVING
		category = '" . $request->get('category') . "'
		AND remark = '" . $request->get('remark') . "'
		ORDER BY
		days ASC
		) AS A
		LEFT JOIN (
		SELECT
		kitto.materials.material_number,
		count( kitto.completions.id ) AS kanban,
		min( kitto.completions.lot_completion ) AS lot
		FROM
		kitto.completions
		LEFT JOIN kitto.materials ON kitto.materials.id = kitto.completions.material_id
		WHERE
		kitto.completions.active = 1
		GROUP BY
		kitto.materials.material_number
	) AS B ON A.material_number = B.material_number";

	$stocks = db::select($query);

	$response = array(
		'status' => true,
		'stocks' => $stocks,
	);
	return Response::json($response);
}

public function fetchStockTableMonitoring(Request $request)
{
	$now = date('Y-m');

	$query = "SELECT
	A.material_number,
	A.description,
	A.remark,
	A.quantity,
	A.safety,
	A.stock,
	A.category,
	A.days,
	B.lot,
	B.kanban
	FROM
	(
	SELECT
	inventories.material_number,
	inventories.description,
	COALESCE ( inventories.remark, 'UNIDENTIFIED' ) AS remark,
	inventories.quantity,
	stocks.quantity AS safety,
	IF
	(
	ceiling( inventories.quantity / stocks.quantity )= 0,
	0,
	IF
	(
	inventories.quantity / stocks.quantity > 0
	AND inventories.quantity / stocks.quantity <= 0.5, 0.5, IF ( inventories.quantity / stocks.quantity > 0.5
	AND inventories.quantity / stocks.quantity <= 1, 1, IF ( inventories.quantity / stocks.quantity > 1
	AND inventories.quantity / stocks.quantity <= 1.5, 1.5, IF ( inventories.quantity / stocks.quantity > 1.5
	AND inventories.quantity / stocks.quantity <= 2, 2, IF ( inventories.quantity / stocks.quantity > 2
	AND inventories.quantity / stocks.quantity <= 2.5, 2.5, IF ( inventories.quantity / stocks.quantity > 2.5
	AND inventories.quantity / stocks.quantity <= 3, 3, IF ( inventories.quantity / stocks.quantity > 3
	AND inventories.quantity / stocks.quantity <= 3.5, 3.5, IF ( inventories.quantity / stocks.quantity > 3.5
	AND inventories.quantity / stocks.quantity <= 4, 4, IF ( inventories.quantity / stocks.quantity > 4
	AND inventories.quantity / stocks.quantity <= 4.5, 4.5, IF ( inventories.quantity / stocks.quantity > 4.5,
	4.6,
	4.6
	))))))))))) AS stock,
	IF
	(
	ceiling( inventories.quantity / stocks.quantity )= 0,
	'0Days',
	IF
	(
	inventories.quantity / stocks.quantity > 0
	AND inventories.quantity / stocks.quantity <= 0.5, '<0.5Days', IF ( inventories.quantity / stocks.quantity > 0.5
	AND inventories.quantity / stocks.quantity <= 1, '<1Days', IF ( inventories.quantity / stocks.quantity > 1
	AND inventories.quantity / stocks.quantity <= 1.5, '<1.5Days', IF ( inventories.quantity / stocks.quantity > 1.5
	AND inventories.quantity / stocks.quantity <= 2, '<2Days', IF ( inventories.quantity / stocks.quantity > 2
	AND inventories.quantity / stocks.quantity <= 2.5, '<2.5Days', IF ( inventories.quantity / stocks.quantity > 2.5
	AND inventories.quantity / stocks.quantity <= 3, '<3Days', IF ( inventories.quantity / stocks.quantity > 3
	AND inventories.quantity / stocks.quantity <= 3.5, '<3.5Days', IF ( inventories.quantity / stocks.quantity > 3.5
	AND inventories.quantity / stocks.quantity <= 4, '<4Days', IF ( inventories.quantity / stocks.quantity > 4
	AND inventories.quantity / stocks.quantity <= 4.5, '<4.5Days', IF ( inventories.quantity / stocks.quantity > 4.5,
	'>4.5Days',
	'>4.5Days'
	))))))))))) AS category,
	inventories.quantity / stocks.quantity AS days
	FROM
	(
	SELECT
	kitto.inventories.material_number,
	kitto.materials.description,
	kitto.materials.remark,
	sum( kitto.inventories.lot ) AS quantity
	FROM
	kitto.inventories
	LEFT JOIN kitto.materials ON kitto.materials.material_number = kitto.inventories.material_number
	WHERE
	kitto.materials.location IN ( " . $request->get('location') . " )
	GROUP BY
	kitto.inventories.material_number,
	kitto.materials.remark,
	kitto.materials.description
	) AS inventories
	INNER JOIN ( SELECT initial_safety_stocks.material_number, initial_safety_stocks.quantity FROM initial_safety_stocks WHERE DATE_FORMAT( valid_date, '%Y-%m' ) = '" . $now . "' AND initial_safety_stocks.quantity > 0 ) AS stocks ON stocks.material_number = inventories.material_number
	ORDER BY
	days ASC
	) AS A
	LEFT JOIN (
	SELECT
	kitto.materials.material_number,
	count( kitto.completions.id ) AS kanban,
	min( kitto.completions.lot_completion ) AS lot
	FROM
	kitto.completions
	LEFT JOIN kitto.materials ON kitto.materials.id = kitto.completions.material_id
	WHERE
	kitto.completions.active = 1
	GROUP BY
	kitto.materials.material_number
	) AS B ON A.material_number = B.material_number
	ORDER BY A.days ASC";

	$stocks = db::select($query);

	$response = array(
		'status' => true,
		'stocks' => $stocks,
	);
	return Response::json($response);
}

public function fetchStockMaster()
{
	$initial_safety = InitialSafetyStock::leftJoin("materials", "materials.material_number", "=", "initial_safety_stocks.material_number")
	->leftJoin("origin_groups", "origin_groups.origin_group_code", "=", "materials.origin_group_code")
	->select('initial_safety_stocks.id', 'initial_safety_stocks.material_number', 'initial_safety_stocks.valid_date', 'initial_safety_stocks.quantity', 'materials.material_description', 'origin_groups.origin_group_name')
	->orderByRaw('valid_date DESC', 'initial_safety_stocks.material_number ASC')
	->get();

	return DataTables::of($initial_safety)
	->addColumn('action', function ($initial_safety) {
		return '
		<button class="btn btn-xs btn-info" data-toggle="tooltip" title="Details" onclick="modalView(' . $initial_safety->id . ')">View</button>
		<button class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit" onclick="modalEdit(' . $initial_safety->id . ')">Edit</button>
		<button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete(' . $initial_safety->id . ',\'' . $initial_safety->material_number . '\',\'' . $initial_safety->valid_date . '\')">Delete</button>';
	})

	->rawColumns(['action' => 'action'])
	->make(true);
}

public function view(Request $request)
{
	$query = "select initial_stock.material_number, initial_stock.valid_date, initial_stock.quantity, users.`name`, material_description, origin_group_name, initial_stock.created_at, initial_stock.updated_at from
	(select material_number, valid_date, quantity, created_by, created_at, updated_at from initial_safety_stocks where id = "
	. $request->get('id') . ") as initial_stock
	left join materials on materials.material_number = initial_stock.material_number
	left join origin_groups on origin_groups.origin_group_code = materials.origin_group_code
	left join users on initial_stock.created_by = users.id";

	$intial_stock = DB::select($query);

	$response = array(
		'status' => true,
		'datas' => $intial_stock,
	);

	return Response::json($response);
}

public function fetchEdit(Request $request)
{
	$intial_stock = InitialSafetyStock::where('id', '=', $request->get("id"))
	->first();

	$response = array(
		'status' => true,
		'datas' => $intial_stock,
	);

	return Response::json($response);
}

public function edit(Request $request)
{
	$head = InitialSafetyStock::where('id', '=', $request->get('id'))
	->first();

	$head->quantity = $request->get('quantity');
	$head->save();

	$response = array(
		'status' => true,
	);

	return Response::json($response);
}

public function import(Request $request)
{
	try {
		if ($request->hasFile('intial_stock')) {
                // ProductionSchedule::truncate();

			$id = Auth::id();

			$file = $request->file('intial_stock');
			$data = file_get_contents($file);

			$rows = explode("\r\n", $data);

			$first = explode("\t", $rows[0]);
			$date = date('Y-m-d', strtotime(str_replace('/', '-', $first[1])));

			$delete = InitialSafetyStock::where('valid_date', '=', $date)
			->forceDelete();

			foreach ($rows as $row) {
				if (strlen($row) > 0) {
					$row = explode("\t", $row);
					$intial_stock = new InitialSafetyStock([
						'material_number' => $row[0],
						'valid_date' => date('Y-m-d', strtotime(str_replace('/', '-', $row[1]))),
						'quantity' => $row[2],
						'created_by' => $id,
					]);

					$intial_stock->save();
				}
			}
			return redirect('/index/safety_stock')->with('status', 'New Initial Safety Stock has been imported.')->with('page', 'Safety Stock');
		} else {
			return redirect('/index/safety_stock')->with('error', 'Please select a file.')->with('page', 'Safety Stock');
		}
	} catch (QueryException $e) {
		$error_code = $e->errorInfo[1];
		if ($error_code == 1062) {
			return back()->with('error', 'Initial Safety Stock with preferred due date already exist.')->with('page', 'Safety Stock');
		} else {
			return back()->with('error', $e->getMessage())->with('page', 'Safety Stock');
		}

	}
}

public function createInitial(Request $request)
{
	$valid_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->get('valid_date'))));

	try
	{
		$id = Auth::id();
		$intial_stock = new InitialSafetyStock([
			'material_number' => $request->get('material_number'),
			'valid_date' => $valid_date,
			'quantity' => $request->get('quantity'),
			'created_by' => $id,
		]);

		$intial_stock->save();

		$response = array(
			'status' => true,
		);
		return Response::json($response);
	} catch (QueryException $e) {
		$error_code = $e->errorInfo[1];
		if ($error_code == 1062) {
			$response = array(
				'status' => false,
				'Message' => 'already exist',
			);
			return Response::json($response);
		} else {
			$response = array(
				'status' => false,
			);
			return Response::json($response);
		}
	}
}

public function delete(Request $request)
{
	$intial_stock = InitialSafetyStock::where('id', '=', $request->get("id"))
	->forceDelete();

	$response = array(
		'status' => true,
	);

	return Response::json($response);
}

public function destroy(Request $request)
{
	$valid_date = date('Y-m-d', strtotime('01-' . $request->get('valid_date2')));

	$materials = Material::whereIn('origin_group_code', $request->get('origin_group2'))
	->select('material_number')->get();

	$intial_stock = InitialSafetyStock::where('valid_date', '=', $valid_date)
	->whereIn('material_number', $materials)
	->forceDelete();

	return redirect('/index/safety_stock')
	->with('status', 'Initial Safety Stock has been deleted.')
	->with('page', 'Safety Stock');
}

public function indexResumeKanban()
{
	$title = 'KPP Resume Kanban';
	$title_jp = '';

	return view('processes.initial.resume_kanban', array(
		'title' => $title,
		'title_jp' => $title_jp,
	))->with('page', 'KPP Resume Kanban')
	->with('head', 'Resume Kanban');
}

public function fetchResumeKanban(Request $request)
{

	if (strlen($request->get('bulan')) > 0) {
		$date = date('Y-m', strtotime($request->get('bulan') . '-01'));
	} else {
		$date = date('Y-m');
	}

	try {
		$datas = db::select("
			SELECT
			initial_kanban_resumes.*,
			valid_date,
			quantity,
			actual_kanban 
			FROM
			initial_kanban_resumes
			LEFT JOIN (SELECT * from initial_safety_stocks WHERE
			DATE_FORMAT( valid_date, '%Y-%m' ) = '". $date ."' ) stok ON initial_kanban_resumes.material_number = stok.material_number 
			ORDER BY
			material_description
			");

		$total_kanban = db::connection('tpro')->select('SELECT m_product_kartu.kartu_code, m_product_kartu.kartu_no, m_product.product_gmc, m_product.product_name, 1 as num FROM `m_product_kartu`
			left join m_product on m_product_kartu.product_id = m_product.product_id
			where kartu_warna = "1"
			ORDER BY product_gmc, kartu_no');

		$response = array(
			'status' => true,
			'datas' => $datas,
			'total_kanbans' => $total_kanban,
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

public function indexCheckKanban()
{
	$title = 'KPP Kanban Check';
	$title_jp = '';

	return view('processes.initial.check_kanban', array(
		'title' => $title,
		'title_jp' => $title_jp,
	))->with('page', 'KPP Kanban Check')->with('head', 'Resume Kanban')->with('head2', 'KPP Resume Kanban');
}

public function fetchCheckKanban()
{
	$datas = db::select("
		SELECT material_number, material_description, location, () 0 as kanban from initial_kanban_resumes
		");

	$response = array(
		'status' => true,
		'datas' => $datas,
	);
	return Response::json($response);
}

public function indexVisualCheck()
{
	$title = 'Visual Material Awal Sanding';
	$title_jp = '';

	$op = Employee::whereNull('end_date')->select('tag', 'employee_id', 'name')->get();

	return view('kpp.sanding.index_check', array(
		'title' => $title,
		'title_jp' => $title_jp,
		'op' => $op,
	))->with('page', 'Check Material Sanding');
}

public function fetchMaterialData(Request $request)
{
	try {
		$number = $request->get('material_number');

		$hexvalues = array('0','1','2','3','4','5','6','7',
			'8','9','A','B','C','D','E','F');
		$mat_num = '';		

		if (strlen($number) == 7) {
			$mat_num = $number;
		} else {
			while($number != '0')
			{
				$mat_num = $hexvalues[bcmod($number,'16')].$mat_num;
				$number = bcdiv($number,'16',0);
			}
		}

		$material = db::connection('tpro')->select('SELECT product_gmc, product_name, product_qty_cs from m_product_kartu LEFT JOIN m_product on m_product_kartu.product_id = m_product.product_id where kartu_code = "' . $mat_num . '" OR product_gmc = "' . $request->get('material_number') . '"');

		if (count($material) < 1) {
			$response = array(
				'status' => false,
				'message' => 'Material Number Tidak Terdaftar',
			);
			return Response::json($response);
		}

		$datas = SandingCheckMaster::where('material_number', $material[0]->product_gmc)
		->where('category', $request->get('category'))
		->orderBy('point')
		->get();

		$grafik = db::select("SELECT DATE_FORMAT(check_time, '%d') hari, point, max(check_point) as cp from sanding_checks where material_number = '" . $material[0]->product_gmc . "' AND DATE_FORMAT(check_time,'%Y-%m') = '" . date('Y-m') . "' group by DATE_FORMAT(check_time, '%d'), point ");

		$process = db::connection('ympimis_2')->select('select process, st_minutes from sending_masters where gmc = "'.$number.'"');

		$response = array(
			'status' => true,
			'datas' => $datas,
			'grafik' => $grafik,
			'qty' => $material[0]->product_qty_cs,
			'process' => $process
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

public function postMaterialCheck(Request $request)
{
	try {
		$tahun = date('y');
		$bulan = date('m');

		$query = "SELECT form_number FROM `sanding_checks` where DATE_FORMAT(created_at, '%y') = '$tahun' and month(created_at) = '$bulan' order by id DESC LIMIT 1";
		$nomorurut = DB::select($query);

		if ($nomorurut != null) {
			$nomor = substr($nomorurut[0]->form_number, -4);
			$nomor = $nomor + 1;
			$nomor = sprintf('%04d', $nomor);
		} else {
			$nomor = "0001";
		}

		$result['tahun'] = $tahun;
		$result['bulan'] = $bulan;
		$result['no_urut'] = $nomor;

		$form_number = 'SND' . $result['tahun'] . $result['bulan'] . $result['no_urut'];

		$ck = $request->get('check');

		$shift = $request->get('shift');
		$value_shift = '';
		if ($shift == 'Shift_1') {
			$value_shift = 'Shift 1/07.00/16.00';
		}else if ($shift == 'Shift_2') {
			$value_shift = 'Shift 2/16.00/00.00';
		}else if ($shift == 'Shift_3') {
			$value_shift = 'Shift 3/01.00/07.00';
		}

		$std_time = db::connection('ympimis_2')->select('select st_minutes from sending_masters where gmc = "'.$request->get('material_number').'" and process = "'.$request->get('process').'"');

		foreach ($ck as $cek) {
			$status = '';
			if ($cek['value'] >= 3) {
				$status = 'NG';
			}
			$cek_awal = new SandingCheck([
				'form_number' => $form_number,
				'material_number' => $request->get('material_number'),
				'material_description' => $request->get('material_desc'),
				'point' => $cek['point'],
				'point_description' => $cek['description'],
				'check_point' => $cek['value'],
				'checker' => $request->get('op'),
				'check_time' => date('Y-m-d H:i:s'),
				'process' => $request->get('process'),
				'shift' => $value_shift,
				'quantity' => $request->get('qty'),
				'status' => $status,
				// 'st_time' => $std_time[0]->st_minutes*60,
				'created_by' => Auth::user()->username,
			]);
			$cek_awal->save();
		}

		//insert dailys
		$test_select = db::connection('ympimis_2')->select('select gmc, process from sending_masters where gmc = "'.$request->get('material_number').'"');
		for ($i=0; $i < count($test_select); $i++) { 
			db::connection('ympimis_2')->table('sending_dailys')
			->insert([
				'nik' => 'pi2101044',
				'gmc' => $request->get('material_number'),
				'description' => $request->get('material_desc'),
				'process' => $test_select[$i]->process,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			]);
		}

		$response = array(
			'status' => true,
			'form_number' => $form_number,
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

public function indexVisualCheckFinish($form_number)
{
	$title = 'Visual Material Finish Sanding';
	$title_jp = '';

	$material = SandingCheck::where('form_number', $form_number)->select('material_number', 'material_description', 'checker')->first();

	$check_point = SandingCheckMaster::where('material_number', $material->material_number)
	->where('category', 'Detail Point Cek Material Finish')
	->orderBy('point')
	->get();

	return view('kpp.sanding.index_check_finish', array(
		'title' => $title,
		'title_jp' => $title_jp,
		'check_point' => $check_point,
		'material' => $material,
	))->with('page', 'Check Material Sanding Finish');
}

public function postMaterialCheckFinish(Request $request)
{
	try {
		$cek = $request->get('check');
		foreach ($cek as $ck) {
			$cek_akhir = new SandingCheckFinish([
				'form_number' => $request->get('form_number'),
				'material_number' => $request->get('material_number'),
				'material_description' => $request->get('material_desc'),
				'point' => $ck['point'],
				'point_description' => $ck['description'],
				'check_point' => $ck['value'],
				'checker' => $request->get('op'),
				'check_time' => date('Y-m-d H:i:s'),
				'status' => $request->get('status'),
				'created_by' => Auth::user()->username,
			]);
			$cek_akhir->save();
		}

		$response = array(
			'status' => true,
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

public function indexVisualCheckMonitoring()
{
	$title = 'Monitoring Visual Check Sanding';
	$title_jp = '';
	return view('kpp.sanding.index_check_monitoring', array(
		'title' => $title,
		'title_jp' => $title_jp,
	))->with('page', 'Monitoring Check Material Sanding');
}

public function fetchVisualCheckMonitoring(Request $request)
{
	$mon = $request->get('date');

	if ($request->get('date') == '') {
		$mon = date('Y-m');
	}

	DB::connection()->enableQueryLog();

	$cek = db::select('SELECT hari, SUM(IF(check_point = "3",belum,0)) as tiga_belum, SUM(IF(check_point = "3",sudah,0)) as tiga_sudah, SUM(IF(check_point = "4",belum,0)) as empat_belum, SUM(IF(check_point = "4",sudah,0)) as empat_sudah, SUM(IF(check_point = "5",belum,0)) as lima_belum, SUM(IF(check_point = "5",sudah,0)) as lima_sudah from
		(SELECT hari, material_number, check_point, SUM(IF(remark is null, 1, 0)) as belum, SUM(IF(remark is not null, 1, 0)) as sudah from
		(SELECT DATE_FORMAT(check_time,"%d %M %Y") as hari, check_point, material_number, remark from sanding_checks
		where DATE_FORMAT(check_time,"%Y-%m") = "' . $mon . '"
		and `check_point` >= "3"
		and `sanding_checks`.`deleted_at` is null
		group by DATE_FORMAT(check_time,"%d %M %Y"), check_point, material_number,remark) as cek
		group by hari, check_point, material_number) as monitor
		group by hari');

	$cek_detail = SandingCheck::select('check_point', 'material_number', 'material_description', 'point', 'point_description', db::raw('DATE_FORMAT(created_at, "%d %M %Y") as dt'), 'created_at', 'remark')
	->where('check_point', '>=', '3')
	->where(db::raw('DATE_FORMAT(check_time,"%Y-%m")'), '=', $mon)
	->orderBy('material_number', 'ASC')
	->orderBy('point', 'ASC')
	->get();

	$ctg = '';
	if (strlen($request->get('category')) > 0) {
		if ($request->get('category') == "Open") {
			$ctg = "and (remark is null OR remark = 'Rejected' OR remark = 'Waiting')";
		} else if ($request->get('category') == "Close") {
			$ctg = "and remark = 'Approved'";
		} else if ($request->get('category') == "All") {
			$ctg = "";
		}
	} else {
		$ctg = "(remark is null OR remark = 'Rejected' OR remark = 'Waiting')";
	}

	$resume_temuan = db::select("SELECT material_number, material_description, GROUP_CONCAT(point SEPARATOR '|') as pn, GROUP_CONCAT(point_description SEPARATOR '|') des, max(check_point) cp, max(check_time) as check_time , min(remark) as stat, min(fu_number) as form_number
		FROM sanding_checks 
		WHERE id IN (
		SELECT min(id) 
		FROM sanding_checks 
		where `status` = 'NG' ".$ctg."
		GROUP BY material_number, material_description, point, point_description
		)
		GROUP BY material_number, material_description
		ORDER BY check_time asc, check_point asc");

	$temuan_lists = SandingCheckFinding::whereIn('status', ['Waiting', 'Rejected'])->get();

	$response = array(
		'status' => true,
		'data_cek' => $cek,
		'detail_cek' => $cek_detail,
		'resume_temuan' => $resume_temuan,
		'temuan_lists' => $temuan_lists,
		'query' => DB::getQueryLog(),
	);
	return Response::json($response);
}

public function approvalVisualCheckFollowUp(Request $request)
{
	try {
		SandingCheckFinding::where('form_number', '=', $request->get('form_number'))
		->update(['status' => $request->get('approval'), 'note' => $request->get('note')]);

		SandingCheck::where('material_number', '=', $request->get('material_number'))
		->where('status', '=', 'NG')
		->where('remark', '=', 'Waiting')
		->update(['remark' => $request->get('approval')]);

		$response = array(
			'status' => true
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

public function IndexInputOperator()
{
	$title = 'Input & Monitoring Efesiensi Proses Sanding';
	$title_jp = '';

	$meja = db::connection('tpro')->select('select sanding_nama, sanding_active, sanding_order_id, sanding_order_gmc from m_mesin_sanding where sanding_active = "1" and sanding_nama like "%Meja%"');

	$mesin = db::connection('tpro')->select('select sanding_nama, sanding_active, sanding_order_id, sanding_order_gmc from m_mesin_sanding where sanding_active = "1" and sanding_nama like "%Mesin%"');

	$fy = db::select('SELECT DISTINCT fiscal_year FROM weekly_calendars ORDER BY id DESC');

	// return view('sanding.input_operator.input_sanding', array(
	return view('sanding.monitoring_efesiensi', array(
		'title' => $title,
		'title_jp' => $title_jp,
		'meja' => $meja,
		'mesin' => $mesin,
		'fy' => $fy
	))->with('page', 'Input & Monitoring Efesiensi Proses');
}

public function MonitoringEfesiensi(Request $request){
	try {
		// $emp = db::select('select name from employee_syncs where department = "Management Information System Department" and end_date is null');
		$shift = $request->get('shift');

		$emp = db::select('SELECT DISTINCT checker FROM sanding_checks where shift like "%'.$shift.'%" ORDER BY checker ASC');

		$date_now   = date('Y-m-d');

		$fy = db::select('SELECT fiscal_year FROM weekly_calendars where week_date = "'.$date_now.'" ORDER BY id desc limit 1');

		$p = '';
		if ( $request->get('fy') == null) {
			$p = $fy[0]->fiscal_year;
		}else{
			$p = $request->get('fy');
		}

		$wc = db::select('SELECT DISTINCT date_format( week_date, "%Y-%m" ) as bulan FROM weekly_calendars WHERE fiscal_year = "'.$p.'" ORDER BY id ASC');

		$result_month = db::select('SELECT
			count(id) as jumlah,
			DATE_FORMAT( updated_at, "%Y-%m" ) AS bulan
			FROM
			tickets 
			WHERE
			`status` = "Finished" 
			GROUP BY
			DATE_FORMAT(
			updated_at,
			"%Y-%m")');

		$std_time = db::select('SELECT checker, st_time from sanding_checks where DATE_FORMAT(check_time, "%Y-%m-%d") = "2022-12-20" and `point` = "1"');

		// $tpro = db::connection('tpro')->select('SELECT
		// 	pp.sanding_operator_name, pp.qty_dikerjakan, pp.sanding_nama, pp.sanding_id
		// 	FROM
		// 	(
		// 		SELECT
		// 		lukman_trial.sanding_operator_name,
		// 		count( lukman_trial.sanding_operator_name ) AS jumlah,
		// 		IF
		// 		( sum( sanding_qty ) > 0, sum( sanding_qty ), 0 ) AS qty_dikerjakan,
		// 		m_mesin_sanding.sanding_nama,
		// 		m_mesin_sanding.sanding_id 
		// 		FROM
		// 		lukman_trial
		// 		LEFT JOIN t_sanding_proses ON t_sanding_proses.sanding_id = lukman_trial.sanding_order_id
		// 		LEFT JOIN m_mesin_sanding ON m_mesin_sanding.sanding_id = lukman_trial.id_mesin_sanding 
		// 		WHERE
		// 		lukman_trial.sanding_order = "DONE" 
		// 		AND lukman_trial.sanding_operator_name IS NOT NULL 
		// 		AND DATE_FORMAT( created_at, "%Y-%m-%d" ) = "2022-12-27" 
		// 		GROUP BY
		// 		lukman_trial.sanding_operator_name,
		// 		m_mesin_sanding.sanding_nama,
		// 		m_mesin_sanding.sanding_id 
		// 		) AS pp 
		// 	GROUP BY
		// 	pp.sanding_operator_name, pp.qty_dikerjakan, pp.sanding_nama, pp.sanding_id
		// 	ORDER BY
		// 	pp.sanding_id ASC');

		$tpro = db::connection('tpro')->select(
			'SELECT
			operator_name,
			sum( perolehan_jumlah ) AS perolehan,
			sanding_nama
			FROM
			t_perolehan_sanding
			LEFT JOIN m_operator ON m_operator.operator_id = t_perolehan_sanding.operator_id
			LEFT JOIN m_mesin_sanding ON sanding_id = device_id 
			WHERE
			DATE_FORMAT( perolehan_start_date, "%Y-%m-%d" ) = "'.$date_now.'" 
			AND t_perolehan_sanding.operator_id != 0 
			GROUP BY
			sanding_nama, sanding_id, operator_name
			ORDER BY
			sanding_id ASC'
		);


		$response = array(
			'status' => true,
			'emp' => $emp,
			'fy' => $fy,
			'wc' => $wc,
			'result_month' => $result_month,
			'p' => $p,
			'std_time' => $std_time,
			'tpro' => $tpro
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

public function ScanInputOperator(Request $request){
	try {
		$meja = $request->get('meja');
		$data = db::connection('tpro')->select('select sanding_order_gmc from m_mesin_sanding where sanding_nama = "'.$meja.'"');
		$process = db::connection('ympimis_2')->select('select gmc, process from sending_masters where gmc = "'.$data[0]->sanding_order_gmc.'"');

		$response = array(
			'status' => true,
			'process' => $process
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

public function FetchSendingDaily(Request $request){
	try {
		$gmc = $request->get('gmc');
		$data = db::connection('tpro')->select('select * from m_product where product_gmc = "'.$gmc.'"');
		$data_mesin = db::connection('tpro')->select('select * from m_mesin_sanding where sanding_order_gmc = "'.$gmc.'"');

		$response = array(
			'status' => true,
			'data' => $data,
			'data_mesin' => $data_mesin
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

public function SaveSendingDaily(Request $request){
	try {
		var_dump($qty);

		$nik = $request->get('nik');
		$name = $request->get('name');
		$gmc = $request->get('gmc');
		$desc = $request->get('desc');
		$qty = $request->get('qty');
		$pro = $request->get('pro');

		db::connection('ympimis_2')->table('sending_dailys')->insert([
			'nik' => $nik,
			// 'name' => $name,
			'gmc' => $gmc,
			'description' => $desc,
			'qty' => $qty,
			// 'process' => $pro,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		]);

		$response = array(
			'status' => true,
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

public function TestSimpanDaily(Request $request){
	try {
		$time = $request->get('time');
		$meja = $request->get('meja');
		$proses = $request->get('proses');
		$nik = $request->get('nik');
		$gmc = $request->get('gmc');

		$cek = db::connection('ympimis_2')->table('sending_dailys')
		->where('nik', $nik)
		->where('gmc', $gmc)
		->where('process', $proses)
		->first();

		if (count($cek) == 0) {
			db::connection('ympimis_2')->table('sending_dailys')->insert([
				'nik' => $nik,
				'name' => $time,
				'gmc' => $gmc,
				'qty' => 1,
				'process' => $proses,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			]);
		}else{
			$data = $cek->name;
			$baru = $time;

			$old=explode(":",$data);
			$play=explode(":",$baru);

			$hours=$old[0]+$play[0];
			$minutes=$old[1]+$play[1];

			if($minutes > 59){
				$minutes=$minutes-60;
				$hours++;
			}

			if($minutes < 10){
				$minutes = "0".$minutes;
			}

			if($minutes == 0){
				$minutes = "00";
			}

			$sum=$hours.":".$minutes;

			db::connection('ympimis_2')->table('sending_dailys')
			->where('nik', $nik)
			->where('gmc', $gmc)
			->where('process', $proses)
			->update([
				'qty' => $cek->qty+1,
				'name' => $sum,
				'updated_at' => date('Y-m-d H:i:s')
			]);
		}

		$response = array(
			'status' => true,
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

public function FetchSendingDailyHistory(Request $request){
	try {
		$nik = $request->get('nik');
		$gmc = $request->get('gmc');
		$proses = $request->get('proses');

		$data_history = db::connection('ympimis_2')->select('select * from sending_dailys where nik = "'.$nik.'" and gmc = "'.$gmc.'" and process = "'.$proses.'"');

		$data_operator = db::connection('ympimis_2')->select('SELECT `name`, sum(qty) as perolehan FROM `sending_dailys` group by `name`');

		$response = array(
			'status' => true,
			'data_history' => $data_history,
			'data_operator' => $data_operator
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

public function indexSandingProductivity()
{
	$title = 'Operator Productivity';
	$title_jp = '';
	return view('kpp.sanding.index_operator_productivity', array(
		'title' => $title,
		'title_jp' => $title_jp,
	))->with('page', 'Operator Productivity');
}

public function fetchScanKanban(Request $request)
{
	try {
		$number = $request->get('tag_number');

		if ($request->get('period') != '') {
			$period = $request->get('period').'-01';
		} else {
			$period = date('Y-m-01');
		}

		$hexvalues = array('0','1','2','3','4','5','6','7',
			'8','9','A','B','C','D','E','F');
		$mat_num = '';    

		
		while($number != '0')
		{
			$mat_num = $hexvalues[bcmod($number,'16')].$mat_num;
			$number = bcdiv($number,'16',0);
		}

		$data = db::connection('tpro')->select('SELECT kartu_code, product_name, product_gmc, kartu_no from m_product_kartu left join m_product on m_product_kartu.product_id = m_product.product_id where kartu_code = "'.$mat_num.'"');

		if (count($data) > 0) {
			$resume = db::table('initial_kanban_resumes')->where('material_number', '=', $data[0]->product_gmc)->where('location', '=', $request->get('location'))->first();

			if (count($resume) == 0) {
				$response = array(
					'status' => false,
					'message' => 'Data Kanban Tidak Ditemukan',
				);
				return Response::json($response);
			}

			$get_log = db::table('initial_kanban_logs')
			->where('material_number', '=', $data[0]->product_gmc)
			->where('kanban_code', '=', $mat_num)
			->where('location', '=', $request->get('location'))
			->orderBy('id', 'desc')
			->first();

			if (count($get_log) > 0) {
				if ($get_log->status != $request->get('status')) {

					if ($request->get('status') == 'actual') {
						$update_master = db::table('initial_kanban_resumes')
						->where('material_number', '=', $data[0]->product_gmc)
						->where('location', '=', $request->get('location'))
						->update(['actual_kanban' => (int) $resume->actual_kanban+1]);
					} else {
						$update_master = db::table('initial_kanban_resumes')
						->where('material_number', '=', $data[0]->product_gmc)
						->where('location', '=', $request->get('location'))
						->update(['actual_kanban' => (int) $resume->actual_kanban-1]);
					}

					db::table('initial_kanban_logs')->insert([
						'material_number' => $data[0]->product_gmc,
						'material_description' => $data[0]->product_name,
						'kanban_number' => $data[0]->kartu_no,
						'kanban_code' => $mat_num,
						'period' => $period,
						'location' => $request->get('location'),
						'status' => $request->get('status'),
						'created_by' => Auth::user()->username,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
					]);
				} else {
					$response = array(
						'status' => false,
						'message' => 'Kanban Berstatus '.$request->get('status'),
					);
					return Response::json($response);
				}
			} else {

				if ($request->get('status') == 'actual') {
					$update_master = db::table('initial_kanban_resumes')
					->where('material_number', '=', $data[0]->product_gmc)
					->where('location', '=', $request->get('location'))
					->update(['actual_kanban' => (int) $resume->actual_kanban+1]);
				} else {
					$update_master = db::table('initial_kanban_resumes')
					->where('material_number', '=', $data[0]->product_gmc)
					->where('location', '=', $request->get('location'))
					->update(['actual_kanban' => (int) $resume->actual_kanban-1]);
				}

				db::table('initial_kanban_logs')->insert([
					'material_number' => $data[0]->product_gmc,
					'material_description' => $data[0]->product_name,
					'kanban_number' => $data[0]->kartu_no,
					'kanban_code' => $mat_num,
					'period' => $period,
					'location' => $request->get('location'),
					'status' => $request->get('status'),
					'created_by' => Auth::user()->username,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				]);
			}

			$response = array(
				'status' => true,
				'datas' => $resume
			);
			return Response::json($response);
		} else {
			$response = array(
				'status' => false,
				'message' => 'Data Kanban Tidak Ditemukan',
			);
			return Response::json($response);
		}
	} catch (\Exception $e) {
		$response = array(
			'status' => false,
			'message' => $e->getMessage(),
		);
		return Response::json($response);
	}
}
// DIGITAL KANBAN KPP

public function indexMaterial()
{
	$hpl = DB::connection('ympimis_2')
	->table('kpp_materials')
	->select('hpl')
	->distinct()
	->get();

	$material = db::select('select * from material_plant_data_lists where (storage_location LIKE "%A0%" OR storage_location LIKE "%A2%") order by material_number asc');

	return view('processes.initial.master_material', array(
		'title' => 'Master GMC Material Process',
		'title_jp' => '',
		'hpl' => $hpl,
		'hpl2' => $hpl,
		'material' => $material
	))->with('page', 'Master GMC Material Process');

}

public function fetchMaterial(Request $request)
{
	$material = db::connection('ympimis_2')
	->table('kpp_materials')
	->get();

	$response = array(
		'status' => true,
		'material' => $material,
	);
	return Response::json($response);
}

public function CreateNewMaterial(Request $request)
{
	try{
		$gmc = $request->get('gmc');
		$desc = $request->get('desc');
		$qty_process = $request->get('qty_process');
		$qty_cs = $request->get('qty_cs');
		$select_hpl = $request->get('select_hpl');

		$cek_material = db::connection('ympimis_2')->table('kpp_materials')->where('material_number', $gmc)->get();

		if (count($cek_material) > 0) {
			$response = array(
				'status' => false,
				'message' => 'Material sudah ada di list.'
			);
			return Response::json($response); 
		}else{
			db::connection('ympimis_2')
			->table('kpp_materials')
			->insert([
				'material_number' => $gmc,
				'material_description' => $desc,
				'qty_process' => $qty_process,
				'qty_cs' => $qty_cs,
				'hpl' => $select_hpl,
				'created_by' => Auth::id(),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			]);
			$response = array(
				'status' => true,
				'message' => 'Material berhasil ditambahkan.'
			);

			return Response::json($response); 
		}

	}catch (Exception $e) {
		$response = array(
			'status' => false,
			'message' => $e->getMessage()
		);
		return Response::json($response); 
	}
}

public function DeleteMaterialKpp(Request $request)
{
	try{
		$id = $request->get('id');

		db::connection('ympimis_2')->select("DELETE FROM kpp_materials WHERE id = '".$id."'");

		$response = array(
			'status' => true,
			'message' => 'Material berhasil dihapus.'
		);

		return Response::json($response);

	}catch (Exception $e) {
		$response = array(
			'status' => false,
			'message' => $e->getMessage()
		);
		return Response::json($response); 
	}
}

public function indexOperator()
{
	$loc = DB::connection('ympimis_2')
	->table('kpp_operators')
	->select('location')
	->distinct()
	->get();

	$emp = db::table('employee_syncs')->where('end_date', null)->get();

	return view('processes.initial.master_operator', array(
		'title' => 'Master Operator Material Process',
		'title_jp' => '',
		'loc' => $loc,
		'emp' => $emp,
		'loc1' => $loc
	))->with('page', 'Master Operator Material Process');

}

public function CreateNewOperator(Request $request)
{
	try{
		$select = $request->get('emp');
		$emp = explode( '/', $select);
		$tag = dechex($request->get('tag'));
		$loc = $request->get('loc');

		$cek_operator = db::connection('ympimis_2')->table('kpp_operators')->where('employee_id', $emp[0])->where('name', $emp[1])->get();

		if (count($cek_operator) > 0) {
			$response = array(
				'status' => false,
				'message' => 'Operator sudah ada di list.'
			);
			return Response::json($response); 
		}else{
			db::connection('ympimis_2')
			->table('kpp_operators')
			->insert([
				'tag' => strtoupper($tag),
				'employee_id' => $emp[0],
				'name' => strtoupper($emp[1]),
				'location' => $loc,
				'created_by' => Auth::id(),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			]);
			$response = array(
				'status' => true,
				'message' => 'Operator berhasil ditambahkan.'
			);

			return Response::json($response); 
		}

	}catch (Exception $e) {
		$response = array(
			'status' => false,
			'message' => $e->getMessage()
		);
		return Response::json($response); 
	}
}

public function UpdateTagOperator(Request $request)
{
	try{
		$nik = $request->get('nik');
		$name = $request->get('name');
		$tag = dechex($request->get('tag'));
		$location = $request->get('location');

		DB::connection('ympimis_2')->table('kpp_operators')->where('employee_id', $nik)->update(['tag' => strtoupper($tag), 'location' => $location]);

		$response = array(
			'status' => true,
			'message' => 'Operator berhasil ditambahkan.'
		);

		return Response::json($response);

	}catch (Exception $e) {
		$response = array(
			'status' => false,
			'message' => $e->getMessage()
		);
		return Response::json($response); 
	}
}

public function DeleteOperatorKpp(Request $request)
{
	try{
		$id = $request->get('id');

		db::connection('ympimis_2')->select("DELETE FROM kpp_operators WHERE id = '".$id."'");

		$response = array(
			'status' => true,
			'message' => 'Operator berhasil dihapus.'
		);

		return Response::json($response);

	}catch (Exception $e) {
		$response = array(
			'status' => false,
			'message' => $e->getMessage()
		);
		return Response::json($response); 
	}
}

public function fetchOperator(Request $request)
{
	$operator = db::connection('ympimis_2')
	->table('kpp_operators')
	->get();

	$response = array(
		'status' => true,
		'operator' => $operator,
	);
	return Response::json($response);

}

public function indexKanban()
{
	$hpl = DB::connection('ympimis_2')
	->table('kpp_materials')
	->select('hpl')
	->distinct()
	->get();

	$remark = DB::connection('ympimis_2')
	->table('kpp_tags')
	->select('remark')
	->distinct()
	->get();

	$material = DB::connection('ympimis_2')
	->table('kpp_materials')
	->select('material_number','material_description')
	->distinct()
	->get();

	$flow = db::connection('ympimis_2')->select('SELECT
		name_flow 
		FROM
		kpp_material_flows 
		GROUP BY
		name_flow');

	return view('processes.initial.master_kanban', array(
		'title' => 'Master Kanban Material Process',
		'title_jp' => '',
		'hpl' => $hpl,
		'remark' => $remark,
		'materials' => $material,
		'flow' => $flow
	))->with('page', 'Master Kanban Material Process');

}
public function fetchKanban(Request $request)
{
	$kanban = db::connection('ympimis_2')
	->table('kpp_tags')
	->get();

	$response = array(
		'status' => true,
		'kanban' => $kanban
	);
	return Response::json($response);
}

public function indexKanbanFlow()
{
	$material = DB::connection('ympimis_2')
	->table('kpp_materials')
	->select('material_number','material_description')
	->distinct()
	->get();

	$ws = DB::connection('ympimis_2')
	->table('kpps')
	->select('work_station')
	->distinct()
	->get();

	return view('processes.initial.master_kanban_flow', array(
		'title' => 'Master Kanban Material Process',
		'title_jp' => '',
		'materials' => $material,
		'ws' => $ws
	))->with('page', 'Master Kanban Material Process');

}

public function CreateNewKanban(Request $request)
{
	try{
		$add_tag = $request->get('add_tag');
		$urutan_flow = $request->get('urutan_flow');
		$material = explode('_', $request->get('add_material'));
		$add_hpl = $request->get('add_hpl');
		$add_remark = $request->get('add_remark');
		$add_kanban_no = $request->get('add_kanban_no');

		// for ($i=0; $i < count($urutan_flow); $i++) {
			// $flow = explode('_', $urutan_flow[$i]);

			// db::connection('ympimis_2')
			// ->table('kpp_material_flows')
			// ->insert([
			// 	'material_number' => $material[0],
			// 	'material_description' => $material[1],
			// 	'name_flow' => $flow[1],
			// 	'urutan' => $flow[0],
			// 	'created_by' => Auth::id(),
			// 	'created_at' => date('Y-m-d H:i:s'),
			// 	'updated_at' => date('Y-m-d H:i:s'),
			// ]);

			db::connection('ympimis_2')
			->table('kpp_tags')
			->insert([
				'tag' => $add_tag,
				'no_kanban' => $add_kanban_no,
				'material_number' => $material[0],
				'material_description' => $material[1],
				'hpl' => $add_hpl,
				'remark' => $add_remark,
				'created_by' => Auth::id(),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			]);
		// }

		$response = array(
			'status' => true,
			'message' => 'Kanban berhasil ditambahkan.'
		);

		return Response::json($response);

	}catch (Exception $e) {
		$response = array(
			'status' => false,
			'message' => $e->getMessage()
		);
		return Response::json($response); 
	}
}

public function UpdateKanban(Request $request)
{
	try{
		$id_edit = $request->get('id_edit');
		$edit_material = $request->get('edit_material');
		$edit_tag = $request->get('edit_tag');
		$edit_kanban_no = $request->get('edit_kanban_no');
		$edit_hpl = $request->get('edit_hpl');
		$edit_remark = $request->get('edit_remark');

		db::connection('ympimis_2')
		->table('kpp_tags')
		->where('id',$id_edit)
		->where('material_number', $edit_material)
		->update([
			'tag' => $edit_tag,
			'no_kanban' => $edit_kanban_no,
			'hpl' => $edit_hpl,
			'remark' => $edit_remark,
			'updated_at' => date('Y-m-d H:i:s')
		]);

		$response = array(
			'status' => true,
			'message' => 'Kanban berhasil diperbarui.'
		);

		return Response::json($response);

	}catch (Exception $e) {
		$response = array(
			'status' => false,
			'message' => $e->getMessage()
		);
		return Response::json($response); 
	}
}

public function fetchKanbanFlow(Request $request)
{
        // $kanban = db::connection('ympimis_2')
        // ->table('kpp_material_flows')
        // ->get();
	$kanban = db::connection('ympimis_2')->select("
		SELECT
		material_number,
		material_description,
		GROUP_CONCAT( COALESCE ( `name_flow`, ' ' )  ORDER BY urutan ASC) AS `flow`,
		GROUP_CONCAT( COALESCE ( `work_station`, ' ' )  ORDER BY urutan ASC) AS `ws`
		FROM
		kpp_material_flows
		GROUP BY
		material_number,
		material_description
		");

	$kanban_detail = db::connection('ympimis_2')->select("
		SELECT
		id,
		material_number,
		name_flow,
		urutan 
		FROM
		kpp_material_flows 
		WHERE
		material_number = '".$request->get('material')."'
		ORDER BY
		urutan ASC
		");

	$response = array(
		'status' => true,
		'kanban' => $kanban,
		'kanban_detail' => $kanban_detail
	);
	return Response::json($response);
}

public function indexKppBoard($loc, $num){
	if ($loc == 'Lathe') {
		$title = 'Lathe Process';
	} else if ($loc == 'MC1') {
		$title = 'MC 1 Process';
	} else if ($loc == 'MC2') {
		$title = 'MC 2nd Process';
	} else if ($loc == 'Press') {
		$title = 'Press Process';
	}

	return view('kpp.process.kpp_board', array(
		'title' => $title,
		'title_jp' => '',
		'loc' => $loc,
	))->with('page', 'KPP');
}

public function fetchKppBoard(Request $request){
	$mon = date('Y-m');
	$in = '';

	if ($request->get('loc') == 'Lathe') {
		$loc = "'NC lathe'";
		$loc3 = "'NC lathe'";
		$loc2 = 'SENBAN';

		if ($request->get('number') == '1') {
			$in = 'AND work_station in ("SB16R#1", "SB16R#2", "SB16R#3", "SB16R#4", "SB16R#5", "SB16R#6", "SR10J#26", "SR20J#27", "SR20J#28", "SR20J#29", "SR20J#30", "SR20J#31")';
		} else if ($request->get('number') == '2') {
			$in = 'AND work_station in ("SR10J#7", "SR10J#8", "SR10J#9", "SR10J#10", "SR10J#11", "SR10J#22", "SR10J#23", "SR10J#24", "SR10J#25")';
		} else if ($request->get('number') == '3') {
			$in = 'AND work_station in ("SR10J#12", "SR10J#13", "SR10J#14", "SR10J#15", "SR10J#16", "SR10J#17", "SR10J#18", "SR10J#19", "SR10J#20", "SR10J#21")';
		}
	} else if ($request->get('loc') == 'MC1'){
		$loc = '"MC 1st"';
		$loc3 = '"MC 1st"';
		$loc2 = 'MC 1';
	} else if ($request->get('loc') == 'MC2'){
		$loc = '"MC 2nd"';
		$loc3 = '"MC 2nd"';
		$loc2 = 'MC 2';
	} else if ($request->get('loc') == 'Press'){
		$loc = '"Oiling", "Blank Nuki", "Triming", "Bending", "Hiraoshi", "Forging", "Nuki", "Nuki Shibori"';
		$loc3 = '"Forging", "Piercing"';
		$loc2 = 'PRESS';
	}

	$materials = db::select("SELECT
		A.material_number,
		A.description,
		A.remark,
		A.quantity,	
		A.days
		FROM
		(
		SELECT
		inventories.material_number,
		inventories.description,
		COALESCE ( inventories.remark, 'UNIDENTIFIED' ) AS remark,
		inventories.quantity,
		inventories.quantity / stocks.quantity AS days
		FROM
		(
		SELECT
		kitto.inventories.material_number,
		kitto.materials.description,
		kitto.materials.remark,
		sum( kitto.inventories.lot ) AS quantity
		FROM
		kitto.inventories
		LEFT JOIN kitto.materials ON kitto.materials.material_number = kitto.inventories.material_number
		WHERE
		kitto.materials.location IN ( 'SXA0', 'SXA2', 'FLA0', 'FLA2', 'CLA0', 'CLA2', 'ZPA0', 'VNA0' )
		GROUP BY
		kitto.inventories.material_number,
		kitto.materials.remark,
		kitto.materials.description
		) AS inventories
		INNER JOIN ( SELECT initial_safety_stocks.material_number, initial_safety_stocks.quantity FROM initial_safety_stocks WHERE DATE_FORMAT( valid_date, '%Y-%m' ) = '".$mon."' AND initial_safety_stocks.quantity > 0 ) AS stocks ON stocks.material_number = inventories.material_number
		HAVING
		remark = '".$loc2."'
		ORDER BY
		days ASC
	) AS A");

	$att_materials = [];

	foreach ($materials as $mats) {
		array_push($att_materials, $mats->material_number);
	}

	$mat = "'".implode("','", $att_materials)."'";	

	$antrians = db::connection('ympimis_2')->select("SELECT material_number, material_description, work_station from kpp_material_flows 
		where name_flow in (".$loc.")
		and material_number in ( ".$mat." )
		".$in."
		order by FIELD(material_number, ".$mat.")");

	$ws = db::connection('ympimis_2')->select("SELECT work_station FROM `kpps` where flow in (".$loc3.") ".$in." GROUP BY work_station ORDER BY id asc");

	$ops = db::connection('ympimis_2')->select("SELECT work_station, device_type, employee_id, name, online_time, material_number, material_description, doing_timestamp FROM `kpps` where flow in (".$loc3.") ".$in);

	$sekarang = db::connection('ympimis_2')->select("SELECT NOW() as sekarang");

	$response = array(
		'status' => true,
		'loc' => $loc,
		'antrians' => $antrians,
		'ws' => $ws,
		'ops' => $ops,
		'now' => $sekarang[0]->sekarang,
	);
	return Response::json($response);
}

public function ReportKpp($id)
{
	if ($id == 'perolehan') {
		$title = 'Report Perolehan';
		$title_jp = '最初工程の在庫監視';

		return view('processes.initial.report_perolehan', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('head', 'Initial Process');
	}
}

public function fetchStatusKanban(Request $request)
{
	try {
		$datas = db::select("SELECT material_number, material_description, location, kanban_number, kanban_code, status, updated_at FROM initial_kanban_logs
			WHERE material_number = '".$request->get('material_number')."' AND location = '".$request->get('location')."' ORDER BY kanban_number ASC");
		
		$response = array(
			'status' => true,
			'datas' => $datas
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


public function indexMaterialProcessKensa($id){
	$ng_lists = DB::table('ng_lists')
	->where('location', '=', $id)
	->where('remark', '=', 'initial')
	->get();

	if($id == 'machining'){
		$title = 'Kensa Proses Machining';
		$title_jp= '';
	}else if($id == 'senban'){
		$title = 'Kensa Proses Senban';
		$title_jp= '';
	}else if($id == 'sanding'){
		$title = 'Kensa Proses Sanding';
		$title_jp= '';
	}

	return view('processes.initial.kensa', array(
		'ng_lists' => $ng_lists,
		'loc' => $id,
		'title' => $title,
		'title_jp' => $title_jp,
	))->with('page', 'Kensa Material Process')->with('head', 'Kensa Material Process');
}

public function scanMaterialProcessKensa(Request $request){
	try {
		$tag = $this->dec2hex($request->get('tag'));

		$zed_material = db::connection('ympimis_2')->table('kpp_tags')
		->where('kpp_tags.tag', '=', $tag)
		->first();

		if($zed_material == null){
			$response = array(
				'status' => false,
				'message' => 'Tag tidak ditemukan',
			);
			return Response::json($response);
		}

			// $zed_operator = db::connection('ympimis_2')->table('welding_details')
			// ->select('welding_details.*','welding_operators.name')
			// ->where('welding_details.tag', $tag)
			// ->leftjoin('welding_operators', 'welding_details.last_check', '=', 'welding_operators.employee_id')
			// ->first();

		$material = db::connection('ympimis_2')->table('kpp_materials')
		->where('kpp_materials.material_number', '=', $zed_material->material_number)
		->select('kpp_materials.material_number','kpp_materials.material_description','kpp_materials.qty_process', 'kpp_materials.qty_cs')
		->first();

		$response = array(
			'status' => true,
			'message' => 'Material ditemukan',
			'material' => $material,
				// 'opwelding' => $zed_operator,
			'started_at' => date('Y-m-d H:i:s')
		);
		return Response::json($response);

	} catch (\Exception $e) {
		$response = array(
			'status' => false,
			'message' => $e->getMessage()
		);
		return Response::json($response);
	}
}

function dec2hex($number){
	$hexvalues = array('0','1','2','3','4','5','6','7',
		'8','9','A','B','C','D','E','F');
	$hexval = '';
	while($number != '0')
	{
		$hexval = $hexvalues[bcmod($number,'16')].$hexval;
		$number = bcdiv($number,'16',0);
	}
	return $hexval;
}

public function inputMaterialProcessKensa(Request $request){

	$code_generator = CodeGenerator::where('note','=','kpp-kensa')->first();
	$code = $code_generator->index+1;
	$code_generator->index = $code;
	$code_generator->save();

	$tag = $this->dec2hex($request->get('tag'));

		//Jika Ada NG

	if($request->get('ng')){
		foreach ($request->get('ng') as $ng) {
			try{
				$kpp_ng_log = db::connection('ympimis_2')
				->table('kpp_ng_logs')
				->insert([
					'employee_id' => $request->get('employee_id'),
					'tag' => $tag,
					'material_number' => $request->get('material_number'),
					'ng_name' => $ng[0],
					'quantity' => $ng[1],
					'location' => $request->get('loc'),
					'started_at' => $request->get('started_at'),
					// 'operator_id' => $request->get('operator_id'),
					// 'welding_time' => $request->get('welding_time'),
					'remark' => $code,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				]);
			}
			catch(\Exception $e){
				$response = array(
					'status' => false,
					'message' => $e->getMessage(),
				);
				return Response::json($response);
			}
		}


		try{
			$kpp_check_logs = db::connection('ympimis_2')
			->table('kpp_check_logs')->insert([
				'employee_id' => $request->get('employee_id'),
				'tag' => $tag,
				'material_number' => $request->get('material_number'),
				'quantity' => $request->get('cek'),
				'location' => $request->get('loc'),
				// 'operator_id' => $request->get('operator_id'),
				// 'welding_time' => $request->get('welding_time'),
				'remark' => 'NG',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			]);

			$kpp_inventories = db::connection('ympimis_2')
			->table('kpp_inventories')->updateOrInsert(
				['tag' => $tag],
				['material_number' => $request->get('material_number'),
				'location' => $request->get('loc'),
				'quantity' => $request->get('cek'),
				'last_check' => $request->get('employee_id'),
				'remark' => 'kensa',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')]
			);

			$kpp_logs = db::connection('ympimis_2')
			->table('kpp_logs')
			->insert([
				'last_check' => $request->get('employee_id'),
				'tag' => $tag,
				'material_number' => $request->get('material_number'),
				'quantity' => $request->get('cek'),
				'location' => $request->get('loc'),
				'work_station' => strtoupper($request->get('loc')),
				'remark' => 'NG',
				'started_at' => date('Y-m-d H:i:s'),
				'finished_at' => date('Y-m-d H:i:s'),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			]);

			$response = array(
				'status' => true,
				'message' => 'NG has been recorded.',
			);
			return Response::json($response);
		}catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	} else {
		try{


			$kpp_flow = DB::connection('ympimis_2')
			->table('kpp_material_flows')
			->where('material_number',$request->get('material_number'))
			->where('name_flow','Kensa')
			->orderby('urutan','asc')
			->first();


			
			$next = '';
			$ws = '';

			if ($kpp_flow) {
				$kpp_next_flow = DB::connection('ympimis_2')
				->table('kpp_material_flows')
				->where('material_number',$request->get('material_number'))
				->where('urutan',$kpp_flow->urutan+1)
				->orderby('urutan','asc')
				->first();

				$next = $kpp_next_flow->name_flow;
				$ws = $kpp_next_flow->work_station;
			}


			$kpp_check_log = db::connection('ympimis_2')
			->table('kpp_check_logs')->insert([
				'employee_id' => $request->get('employee_id'),
				'tag' => $tag,
				'material_number' => $request->get('material_number'),
				'quantity' => $request->get('cek'),
				'location' => $request->get('loc'),
				// 'operator_id' => $request->get('operator_id'),
				// 'welding_time' => $request->get('welding_time'),
				'remark' => 'OK',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			]);

			$kpp_inventories = db::connection('ympimis_2')
			->table('kpp_inventories')->updateOrInsert(
				['tag' => $tag],
				['material_number' => $request->get('material_number'),
				'location' => $request->get('loc'),
				'location_next' => $next,
				'quantity' => $request->get('cek'),
				// 'barcode_number' => $request->get('barcode_number'),
				'last_check' => $request->get('employee_id'),
				'remark' => 'kensa',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')]
			);

			$kpp_logs = db::connection('ympimis_2')
			->table('kpp_logs')
			->insert([
				'last_check' => $request->get('employee_id'),
				'tag' => $tag,
				'material_number' => $request->get('material_number'),
				'quantity' => $request->get('cek'),
				'location' => $request->get('loc'),
				'work_station' => strtoupper($request->get('loc')),
				'remark' => 'OK',
				'started_at' => date('Y-m-d H:i:s'),
				'finished_at' => date('Y-m-d H:i:s'),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			]);

			//HAPUS Antrian

			$kpp_queues = db::connection('ympimis_2')
			->table('kpp_queues')
			->where('material_number',$request->get('material_number'))
			->where('flow','Kensa')
			->first();
			
			if ($kpp_queues) {
				$delete = db::connection('ympimis_2')
				->table('kpp_queues')
				->where('material_number',$request->get('material_number'))
				->where('location','Kensa')
				->orderby('created_at','desc')
				->delete();
			}

			$insert_queue = db::connection('ympimis_2')
			->table('kpp_queues')
			->insert([
				'tag' => $tag,
				'material_number' => $request->get('material_number'),
				'location' => $next,
				'work_station' => $ws,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			]);

			$response = array(
				'status' => true,
				'message' => 'Input material successfull.',
			);
			return Response::json($response);
		}
		catch(\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}
}


public function fetchKensaResult(Request $request){

	try {
		$location = $request->get('location');
		$employee_id = $request->get('employee_id');
		$now = date('Y-m-d');

		$query1 = "SELECT
		sum(kpp_check_logs.quantity) AS ok 
		FROM
		kpp_check_logs
		WHERE
		employee_id = '".$employee_id."' 
		AND date( kpp_check_logs.created_at ) = '".$now."' 
		AND kpp_check_logs.remark = 'OK' 
		AND location = '".$location."'";

		$oks = db::connection('ympimis_2')->select($query1);

		$query2 = "SELECT
		sum(kpp_ng_logs.quantity) AS ng 
		FROM
		kpp_ng_logs
		WHERE
		employee_id = '".$employee_id."' 
		AND date( kpp_ng_logs.created_at ) = '".$now."' 
		AND location = '".$location."'";

		$ngs = db::connection('ympimis_2')->select($query2);

		$response = array(
			'status' => true,
			'oks' => $oks,
			'ngs' => $ngs,
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

public function indexNgRate(){
	$title = 'NG Rate Material Process';
	$title_jp = '';
	$locations = $this->location;

	return view('processes.initial.display.ng_rate', array(
		'title' => 'NG Rate',
		'title_jp' => '不良率',
		'locations' => $locations
	))->with('page', 'Material Process');
}


public function fetchNgRate(Request $request){
	try {
		$now = date('Y-m-d');

		$addlocation = "";
		if($request->get('location') != null) {
			$locations = explode(",", $request->get('location'));
			$location = "";

			for($x = 0; $x < count($locations); $x++) {
				$location = $location."'".$locations[$x]."'";
				if($x != count($locations)-1){
					$location = $location.",";
				}
			}
			$addlocation = "and location in (".$location.") ";
		}
		else{
			$addlocation = "";
		}

		if(strlen($request->get('tanggal')) > 0){
			$tgl = date('Y-m-d',strtotime($request->get('tanggal')));
			$jam = date('Y-m-d H:i:s',strtotime($request->get('tanggal').date('H:i:s')));

			if ($jam > date('Y-m-d',strtotime($tgl)).' 00:00:01' && $jam < date('Y-m-d',strtotime($tgl)).' 02:00:00' && $tgl == date('Y-m-d',strtotime($tgl))) {
				$nextday =  date('Y-m-d', strtotime($tgl));
				$yesterday = date('Y-m-d',strtotime($tgl." -1 days"));
			}else{
				$nextday =  date('Y-m-d', strtotime($tgl . " +1 days"));
				$yesterday = date('Y-m-d',strtotime($tgl));
			}
		}else{
			$tgl = date("Y-m-d");
			$jam = date('Y-m-d H:i:s');
			if ($jam > date('Y-m-d',strtotime($tgl)).' 00:00:01' && $jam < date('Y-m-d',strtotime($tgl)).' 02:00:00') {
				$nextday = date('Y-m-d');
				$yesterday = date('Y-m-d',strtotime("-1 days"));
			}else{
				$nextday = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
				$yesterday = date('Y-m-d');
			}
		}

		$ng = db::connection('ympimis_2')
		->select("SELECT
			SUM( quantity ) AS jumlah,
			ng_name,
			SUM( quantity ) / ( SELECT SUM( kpp_check_logs.quantity ) AS total_check FROM kpp_check_logs WHERE deleted_at IS NULL ".$addlocation." AND kpp_check_logs.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$nextday." 02:00:00' ) * 100 AS rate 
			FROM
			kpp_ng_logs 
			WHERE
			created_at BETWEEN '".$yesterday." 06:00:00' 
			AND '".$nextday." 02:00:00' 
			".$addlocation."
			GROUP BY
			ng_name 
			ORDER BY
			jumlah DESC");

		$dateTitle = date("d M Y", strtotime($yesterday));

		$datastat = db::connection('ympimis_2')->select("select 
			COALESCE(SUM(kpp_check_logs.quantity),0) as total_check,

			COALESCE(
			SUM(kpp_check_logs.quantity)
			-
			(Select SUM(quantity) from kpp_ng_logs where deleted_at is null ".$addlocation." and kpp_ng_logs.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$nextday." 02:00:00'),0) as total_ok,

			COALESCE((select sum(quantity) from kpp_ng_logs where deleted_at is null ".$addlocation." and kpp_ng_logs.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$nextday." 02:00:00'),0) as total_ng,

			COALESCE((select sum(quantity) from kpp_ng_logs where deleted_at is null ".$addlocation." and kpp_ng_logs.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$nextday." 02:00:00')
			/ 
			(Select SUM(quantity) from kpp_check_logs where deleted_at is null ".$addlocation." and kpp_check_logs.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$nextday." 02:00:00') * 100,0) as ng_rate 

			from kpp_check_logs 
			where kpp_check_logs.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$nextday." 02:00:00' ".$addlocation." and deleted_at is null ");

		$location = "";
		if($request->get('location') != null) {
			$locations = explode(",", $request->get('location'));
			for($x = 0; $x < count($locations); $x++) {
				$location = $location." ".$locations[$x]." ";
				if($x != count($locations)-1){
					$location = $location."&";
				}
			}
		}else{
			$location = "";
		}
		$location = strtoupper($location);

		$response = array(
			'status' => true,
			'ng' => $ng,
			'dateTitle' => $dateTitle,
			'data' => $datastat,
			'title' => $location,
			'date' => $tgl
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

public function fetchNgRateDetail(Request $request)
{
	try {
		if(strlen($request->get('date')) > 0){
			$tgl = date('Y-m-d',strtotime($request->get('date')));
			$jam = date('Y-m-d H:i:s',strtotime($request->get('date').date('H:i:s')));
			if ($jam > date('Y-m-d',strtotime($tgl)).' 00:00:01' && $jam < date('Y-m-d',strtotime($tgl)).' 02:00:00' && $tgl == date('Y-m-d',strtotime($tgl))) {
				$nextday =  date('Y-m-d', strtotime($tgl));
				$yesterday = date('Y-m-d',strtotime($tgl." -1 days"));
			}else{
				$nextday =  date('Y-m-d', strtotime($tgl . " +1 days"));
				$yesterday = date('Y-m-d',strtotime($tgl));
			}
		}else{
			$tgl = date("Y-m-d");
			$jam = date('Y-m-d H:i:s');
			if ($jam > date('Y-m-d',strtotime($tgl)).' 00:00:01' && $jam < date('Y-m-d',strtotime($tgl)).' 02:00:00') {
				$nextday = date('Y-m-d');
				$yesterday = date('Y-m-d',strtotime("-1 days"));
			}else{
				$nextday = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
				$yesterday = date('Y-m-d');
			}
		}

		$addlocation = "";
		if($request->get('location') != null) {
			$locations = explode(",", $request->get('location'));
			$location = "";

			for($x = 0; $x < count($locations); $x++) {
				$location = $location."'".$locations[$x]."'";
				if($x != count($locations)-1){
					$location = $location.",";
				}
			}
			$addlocation = "and location in (".$location.") ";
		}
		else{
			$addlocation = "";
		}

		$emp = EmployeeSync::where('end_date',null)->get();

		if ($request->get('ng_name') != '') {
			$detail = DB::connection('ympimis_2')->select("
				SELECT
				kpp_ng_logs.*,
				kpp_tags.material_description 
				FROM
				`kpp_ng_logs`
				JOIN 
				kpp_tags ON kpp_tags.tag = kpp_ng_logs.tag 
				WHERE
				kpp_ng_logs.created_at >= '".$yesterday." 06:00:00'
				AND kpp_ng_logs.created_at <= '".$nextday." 02:00:00'
				".$addlocation."
				AND ng_name = '".$request->get('ng_name')."'");
		}else{

		}
		$response = array(
			'status' => true,
			'detail' => $detail,
			'emp' => $emp
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

public function postVisualCheckFollowUp(Request $request)
{
	try {
		$tahun = date('y');
		$bulan = date('m');

		$query = "SELECT form_number FROM `sanding_check_findings` where DATE_FORMAT(created_at, '%y') = '$tahun' and month(created_at) = '$bulan' order by id DESC LIMIT 1";
		$nomorurut = DB::select($query);

		if ($nomorurut != null) {
			$nomor = substr($nomorurut[0]->form_number, -3);
			$nomor = $nomor + 1;
			$nomor = sprintf('%03d', $nomor);
		} else {
			$nomor = "001";
		}

		$result['tahun'] = $tahun;
		$result['bulan'] = $bulan;
		$result['no_urut'] = $nomor;

		$form_number = 'FSND' . $result['tahun'] . $result['bulan'] . $result['no_urut'];

		$file1 = $request->file('ss_periodik');

		$name1 = $file1->getClientOriginalName();
		$extension1 = pathinfo($name1, PATHINFO_EXTENSION);

		$nama_file1 = 'SS_'.$form_number . '.' . $extension1;
		$file1->move('sanding/visual_check/FU', $nama_file1);

		$file2 = $request->file('foto_material');

		$name2 = $file2->getClientOriginalName();
		$extension2 = pathinfo($name2, PATHINFO_EXTENSION);

		$nama_file2 = 'MAT_'.$form_number . '.' . $extension2;
		$file2->move('sanding/visual_check/FU', $nama_file2);

		$insert = new SandingCheckFinding([
			'form_number' => $form_number,
			'material_number' => $request->get('material_number'),
			'material_description' => $request->get('material_description'),
			'point' => $request->get('point'),
			'point_description' => $request->get('point_description'),
			'check_point' => $request->get('check_point'),
			'molding_evidence' => $nama_file1,
			'material_evidence' => $nama_file2,
			'status' => 'Waiting',
			'created_by' => Auth::user()->username,
		]);
		$insert->save();

		SandingCheck::where('material_number', '=', $request->get('material_number'))
		->where('status', '=', 'NG')
		->whereRaw('(remark is null OR remark = "Rejected")')
		->update(['remark' => 'Waiting', 'fu_number' => $form_number]);

		$response = array(
			'status' => true
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

}
