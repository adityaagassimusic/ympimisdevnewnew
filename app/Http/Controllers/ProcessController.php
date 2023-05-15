<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Exception;
use Response;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Carbon\Carbon;
use App\Libraries\ActMLEasyIf;
use App\LogProcess;
use App\PlcCounter;
use App\CodeGenerator;
use App\StampInventory;
use App\StampSchedule;
use App\Material;
use App\Process;
use App\ErrorLog;
use App\AssemblyKeyInventory;
use App\MiddleLacqueringLog;
use App\MiddleLacqueringCheckLog;
use App\MiddleLacqueringNgLog;
use App\MiddlePlatingLog;
use App\MiddlePlatingCheckLog;
use App\MiddlePlatingNgLog;
use App\KittoTransactionCheck;
use App\ImageSax;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;



class ProcessController extends Controller
{
	public function __construct(){
		$this->middleware('auth');

		$this->kd_gmc = [
			'WY69490',
			'ZE92410',
			'WY8954P'
		];
	}

	public function indexLog(){
		return view('processes.assy_fl.log')->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function fetchLogTableFl(){
		$query = "select stamp_inventories.serial_number, stamp_inventories.model, max(if(log_processes.process_code = 1, log_processes.created_at, null)) as kariawase, max(if(log_processes.process_code = 2, log_processes.created_at, null)) as tanpoawase, max(if(log_processes.process_code = 3, log_processes.created_at, null)) as yuge, max(if(log_processes.process_code = 4, log_processes.created_at, null)) as chousei, if(stamp_inventories.status is null, 'InProcess', stamp_inventories.`status`) as `status` 
		from stamp_inventories 
		left join log_processes 
		on log_processes.serial_number = stamp_inventories.serial_number 
		where stamp_inventories.model like 'YFL%' 
		group by stamp_inventories.serial_number, stamp_inventories.model, stamp_inventories.status
		order by stamp_inventories.serial_number asc";

		$logs = DB::select($query);
		return DataTables::of($logs)->make(true);
	}

	public function indexRepairFl(){
		return view('processes.assy_fl.return')->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexAssyMonitoring(){
		return view('processes.assembly.index_monitoring')->with('page', 'Assembly Monitoring')->with('head', 'Assembly Monitoring');
	}

	public function indexProcessAssyFL(){
		$role = Auth::user()->role_code;
		return view('processes.assy_fl.index')->with('page', 'Process Assy FL')->with('head', 'Assembly Process')->with('role',$role);
	}

	public function indexProcessAssyFL1(){
		//$now = date('Y-m-d',strtotime('-4 days'));

		$model2 = StampInventory::orderBy('created_at', 'desc')
		->get();
		return view('processes.assy_fl.stamp',array(
			'model2' => $model2,
		))
		->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexProcessAssyFL0(){
		return view('processes.assy_fl.kariawase')->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexProcessAssyFL2(){
		return view('processes.assy_fl.tanpoawase')->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexProcessAssyFL3(){
		return view('processes.assy_fl.seasoning')->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexProcessAssyFL4(){
		return view('processes.assy_fl.choseikanggo')->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function fetchReturnTableFl(){
		$stamp_inventories = StampInventory::where('origin_group_code', '=', '041')
		->where('status', '=', 'return')
		->orderBy('updated_at', 'desc')
		->get();

		return DataTables::of($stamp_inventories)
		->make(true);
	}

	public function scanSerialNumberReturnFl(Request $request){
		$stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $request->get('serialNumber'))
		->where('stamp_inventories.origin_group_code', '=', $request->get('originGroupCode'));

		$stamp_inventory->update(['status' => 'return']);

		$response = array(
			'status' => true,
			'message' => 'Return success',
		);
		return Response::json($response);
	}



	public function indexDisplay(){
		return view('processes.assy_fl.display', array(
			// 'models' => $models,
		))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexDisplayWipFL(){
		return view('processes.assy_fl.display_all', array(
			// 'models' => $models,
		))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');		
	}

	// public function fetchwipflallstock(){
	// 	$first = date('Y-m-01');

	// 	if(date('D')=='Fri' || date('D')=='Wed' || date('D')=='Thu' || date('D')=='Sat'){
	// 		$h4 = date('Y-m-d', strtotime(carbon::now()->addDays(5)));
	// 	}
	// 	elseif(date('D')=='Sun'){
	// 		$h4 = date('Y-m-d', strtotime(carbon::now()->addDays(4)));
	// 	}
	// 	else{
	// 		$h4 = date('Y-m-d', strtotime(carbon::now()->addDays(3)));
	// 	}

	// 	$query = "
	// 	select result1.model, result1.process_code, result1.process_name, if(result2.quantity is null, 0, result2.quantity) as quantity from
	// 	(
	// 	select distinct model, processes.process_code, processes.process_name from materials left join processes on processes.remark = CONCAT('YFL',materials.origin_group_code) where processes.remark = 'YFL041' and processes.process_code <> '5' and materials.category = 'FG') as result1
	// 	left join
	// 	(
	// 	select stamp_inventories.model, stamp_inventories.process_code, sum(stamp_inventories.quantity) as quantity from stamp_inventories where stamp_inventories.status is null and stamp_inventories.deleted_at is null group by stamp_inventories.model, stamp_inventories.process_code
	// 	) as result2 
	// 	on result2.model = result1.model and result2.process_code = result1.process_code order by result1.model asc";

	// 	$query2 = "
	// 	select result1.model, if(result2.plan is null or result2.plan < 0, 0, result2.plan) as plan from
	// 	(
	// 	select distinct materials.model from materials where materials.origin_group_code = '041' and materials.category = 'FG'
	// 	) as result1
	// 	left join
	// 	(
	// 	select materials.model, sum(plan) as plan from
	// 	(
	// 	select material_number, sum(quantity) as plan from production_schedules where due_date >= '".$first."' and due_date <= '".$h4."' group by material_number
	// 	union all
	// 	select material_number, -(sum(quantity)) as plan from flo_details where date(created_at) >= '".$first."' and date(created_at) <= '".$h4."' group by material_number
	// 	) r
	// 	left join materials on materials.material_number = r.material_number
	// 	group by materials.model
	// 	) as result2
	// 	on result1.model = result2.model order by result1.model asc";

	// 	$inventory = DB::select($query);
	// 	$plan = DB::select($query2);

	// 	$response = array(
	// 		'status' => true,
	// 		'inventory' => $inventory,
	// 		'plan' => $plan,
	// 	);
	// 	return Response::json($response);
	// }

	public function fetchwipflallstock(){
		$first = date('Y-m-01');

		if(date('D')=='Fri' || date('D')=='Wed' || date('D')=='Thu' || date('D')=='Sat'){
			$h4 = date('Y-m-d', strtotime(carbon::now()->addDays(5)));
		}
		elseif(date('D')=='Sun'){
			$h4 = date('Y-m-d', strtotime(carbon::now()->addDays(4)));
		}
		else{
			$h4 = date('Y-m-d', strtotime(carbon::now()->addDays(3)));
		}

		$stampperakitan = DB::SELECT("SELECT
			a.model,
			'1,2' as process_code,
			SUM( a.quantity ) quantity,
			SUM( a.quantity_new ) quantity_new
			FROM
			(
			SELECT DISTINCT
			( model ),
			process_code,
			COALESCE ((
			SELECT
				count( id ) 
			FROM
				assembly_inventories 
			WHERE
				assembly_inventories.location = process_name 
				AND assembly_inventories.model = materials.model 
				and status_material is null
			GROUP BY
				location,
				model 
			ORDER BY
				model 
				),
			0 
		) AS quantity,
		COALESCE ((
			SELECT
				count( id ) 
			FROM
				assembly_inventories 
			WHERE
				assembly_inventories.location = process_name 
				AND assembly_inventories.model = materials.model 
				and status_material = 'TRIAL'
			GROUP BY
				location,
				model 
			ORDER BY
				model 
				),
			0 
		) AS quantity_new
			FROM
			materials
			LEFT JOIN processes ON processes.remark = materials.origin_group_code 
			WHERE
			category = 'FG' 
			AND origin_group_code = 041 
			AND process_code IN ( 1,2 ) 
			ORDER BY
			model 
			) a 
			GROUP BY
			a.model");

		$kariawase = DB::SELECT("SELECT
			a.model,
			'3,4,5,6' as process_code,
			SUM( a.quantity ) quantity,
			SUM( a.quantity_new ) quantity_new
			FROM
			(
			SELECT DISTINCT
			( model ),
			process_code,
			COALESCE ((
			SELECT
				count( id ) 
			FROM
				assembly_inventories 
			WHERE
				assembly_inventories.location = process_name 
				AND assembly_inventories.model = materials.model 
				and status_material is null
			GROUP BY
				location,
				model 
			ORDER BY
				model 
				),
			0 
		) AS quantity,
		COALESCE ((
			SELECT
				count( id ) 
			FROM
				assembly_inventories 
			WHERE
				assembly_inventories.location = process_name 
				AND assembly_inventories.model = materials.model 
				and status_material = 'TRIAL'
			GROUP BY
				location,
				model 
			ORDER BY
				model 
				),
			0 
		) AS quantity_new
			FROM
			materials
			LEFT JOIN processes ON processes.remark = materials.origin_group_code 
			WHERE
			category = 'FG' 
			AND origin_group_code = 041 
			AND process_code IN ( 3,4,5,6 ) 
			ORDER BY
			model 
			) a 
			GROUP BY
			a.model");

		$tanpoireperakitan = DB::SELECT("SELECT
			a.model,
			'7,8' as process_code,
			SUM( a.quantity ) quantity,
			SUM( a.quantity_new ) quantity_new
			FROM
			(
			SELECT DISTINCT
			( model ),
			process_code,
			COALESCE ((
			SELECT
				count( id ) 
			FROM
				assembly_inventories 
			WHERE
				assembly_inventories.location = process_name 
				AND assembly_inventories.model = materials.model 
				and status_material is null
			GROUP BY
				location,
				model 
			ORDER BY
				model 
				),
			0 
		) AS quantity,
		COALESCE ((
			SELECT
				count( id ) 
			FROM
				assembly_inventories 
			WHERE
				assembly_inventories.location = process_name 
				AND assembly_inventories.model = materials.model 
				and status_material = 'TRIAL'
			GROUP BY
				location,
				model 
			ORDER BY
				model 
				),
			0 
		) AS quantity_new
			FROM
			materials
			LEFT JOIN processes ON processes.remark = materials.origin_group_code 
			WHERE
			category = 'FG' 
			AND origin_group_code = 041 
			AND process_code IN ( 7,8 ) 
			ORDER BY
			model 
			) a 
			GROUP BY
			a.model");

		$tanpoawase = DB::SELECT("SELECT
			a.model,
			'9,10,11' as process_code,
			SUM( a.quantity ) quantity,
			SUM( a.quantity_new ) quantity_new
			FROM
			(
			SELECT DISTINCT
			( model ),
			process_code,
			COALESCE ((
			SELECT
				count( id ) 
			FROM
				assembly_inventories 
			WHERE
				assembly_inventories.location = process_name 
				AND assembly_inventories.model = materials.model 
				and status_material is null
			GROUP BY
				location,
				model 
			ORDER BY
				model 
				),
			0 
		) AS quantity,
		COALESCE ((
			SELECT
				count( id ) 
			FROM
				assembly_inventories 
			WHERE
				assembly_inventories.location = process_name 
				AND assembly_inventories.model = materials.model 
				and status_material = 'TRIAL'
			GROUP BY
				location,
				model 
			ORDER BY
				model 
				),
			0 
		) AS quantity_new
			FROM
			materials
			LEFT JOIN processes ON processes.remark = materials.origin_group_code 
			WHERE
			category = 'FG' 
			AND origin_group_code = 041 
			AND process_code IN ( 9,10,11 ) 
			ORDER BY
			model 
			) a 
			GROUP BY
			a.model");

		$seasoningkango = DB::SELECT("SELECT
			a.model,
			'12,13,14' as process_code,
			SUM( a.quantity ) quantity,
			SUM( a.quantity_new ) quantity_new
			FROM
			(
			SELECT DISTINCT
			( model ),
			process_code,
			COALESCE ((
			SELECT
				count( id ) 
			FROM
				assembly_inventories 
			WHERE
				assembly_inventories.location = process_name 
				AND assembly_inventories.model = materials.model 
				and status_material is null
			GROUP BY
				location,
				model 
			ORDER BY
				model 
				),
			0 
		) AS quantity,
		COALESCE ((
			SELECT
				count( id ) 
			FROM
				assembly_inventories 
			WHERE
				assembly_inventories.location = process_name 
				AND assembly_inventories.model = materials.model 
				and status_material = 'TRIAL'
			GROUP BY
				location,
				model 
			ORDER BY
				model 
				),
			0 
		) AS quantity_new
			FROM
			materials
			LEFT JOIN processes ON processes.remark = materials.origin_group_code 
			WHERE
			category = 'FG' 
			AND origin_group_code = 041 
			AND process_code IN ( 12,13,14 ) 
			ORDER BY
			model 
			) a 
			GROUP BY
			a.model");

		$renraku = DB::SELECT("SELECT
			a.model,
			'15,16' as process_code,
			SUM( a.quantity ) quantity,
			SUM( a.quantity_new ) quantity_new
			FROM
			(
			SELECT DISTINCT
			( model ),
			process_code,
			COALESCE ((
			SELECT
				count( id ) 
			FROM
				assembly_inventories 
			WHERE
				assembly_inventories.location = process_name 
				AND assembly_inventories.model = materials.model 
				and status_material is null
			GROUP BY
				location,
				model 
			ORDER BY
				model 
				),
			0 
		) AS quantity,
		COALESCE ((
			SELECT
				count( id ) 
			FROM
				assembly_inventories 
			WHERE
				assembly_inventories.location = process_name 
				AND assembly_inventories.model = materials.model 
				and status_material = 'TRIAL'
			GROUP BY
				location,
				model 
			ORDER BY
				model 
				),
			0 
		) AS quantity_new
			FROM
			materials
			LEFT JOIN processes ON processes.remark = materials.origin_group_code 
			WHERE
			category = 'FG' 
			AND origin_group_code = 041 
			AND process_code IN ( 15,16 ) 
			ORDER BY
			model 
			) a 
			GROUP BY
			a.model");

		$qafungsi = DB::SELECT("SELECT
			a.model,
			'17' as process_code,
			SUM( a.quantity ) quantity,
			SUM( a.quantity_new ) quantity_new
			FROM
			(
			SELECT DISTINCT
			( model ),
			process_code,
			COALESCE ((
			SELECT
				count( id ) 
			FROM
				assembly_inventories 
			WHERE
				assembly_inventories.location = process_name 
				AND assembly_inventories.model = materials.model 
				and status_material is null
			GROUP BY
				location,
				model 
			ORDER BY
				model 
				),
			0 
		) AS quantity,
		COALESCE ((
			SELECT
				count( id ) 
			FROM
				assembly_inventories 
			WHERE
				assembly_inventories.location = process_name 
				AND assembly_inventories.model = materials.model 
				and status_material = 'TRIAL'
			GROUP BY
				location,
				model 
			ORDER BY
				model 
				),
			0 
		) AS quantity_new
			FROM
			materials
			LEFT JOIN processes ON processes.remark = materials.origin_group_code 
			WHERE
			category = 'FG' 
			AND origin_group_code = 041 
			AND process_code IN ( 17 ) 
			ORDER BY
			model 
			) a 
			GROUP BY
			a.model");

		$fukiagerepair = DB::SELECT("SELECT
			a.model,
			'18,19,20,22' as process_code,
			SUM( a.quantity ) quantity,
			SUM( a.quantity_new ) quantity_new
			FROM
			(
			SELECT DISTINCT
			( model ),
			process_code,
			COALESCE ((
			SELECT
				count( id ) 
			FROM
				assembly_inventories 
			WHERE
				assembly_inventories.location = process_name 
				AND assembly_inventories.model = materials.model 
				and status_material is null
			GROUP BY
				location,
				model 
			ORDER BY
				model 
				),
			0 
		) AS quantity,
		COALESCE ((
			SELECT
				count( id ) 
			FROM
				assembly_inventories 
			WHERE
				assembly_inventories.location = process_name 
				AND assembly_inventories.model = materials.model 
				and status_material = 'TRIAL'
			GROUP BY
				location,
				model 
			ORDER BY
				model 
				),
			0 
		) AS quantity_new
			FROM
			materials
			LEFT JOIN processes ON processes.remark = materials.origin_group_code 
			WHERE
			category = 'FG' 
			AND origin_group_code = 041 
			AND process_code IN ( 18,19,20,22 ) 
			ORDER BY
			model 
			) a 
			GROUP BY
			a.model");

		$qavisual = DB::SELECT("SELECT
			a.model,
			'21,23' as process_code,
			SUM( a.quantity ) quantity,
			SUM( a.quantity_new ) quantity_new
			FROM
			(
			SELECT DISTINCT
			( model ),
			process_code,
			COALESCE ((
			SELECT
				count( id ) 
			FROM
				assembly_inventories 
			WHERE
				assembly_inventories.location = process_name 
				AND assembly_inventories.model = materials.model 
				and status_material is null
			GROUP BY
				location,
				model 
			ORDER BY
				model 
				),
			0 
		) AS quantity,
		COALESCE ((
			SELECT
				count( id ) 
			FROM
				assembly_inventories 
			WHERE
				assembly_inventories.location = process_name 
				AND assembly_inventories.model = materials.model 
				and status_material = 'TRIAL'
			GROUP BY
				location,
				model 
			ORDER BY
				model 
				),
			0 
		) AS quantity_new
			FROM
			materials
			LEFT JOIN processes ON processes.remark = materials.origin_group_code 
			WHERE
			category = 'FG' 
			AND origin_group_code = 041 
			AND process_code IN ( 21,23 ) 
			ORDER BY
			model 
			) a 
			GROUP BY
			a.model");

		$query = "
		SELECT DISTINCT
		( model ),
		process_code,
		process_name,
		COALESCE ((
		SELECT
		count( id ) 
		FROM
		assembly_inventories 
		WHERE
		assembly_inventories.location = process_name 
		AND assembly_inventories.model = materials.model 
		GROUP BY
		location,
		model 
		ORDER BY
		model 
		),
		0 
		) AS quantity 
		FROM
		materials
		LEFT JOIN processes ON processes.remark = materials.origin_group_code 
		WHERE
		category = 'FG' 
		AND origin_group_code = 041 
		ORDER BY
		model,
		CAST(
		process_code AS INT)";

		$query2 = "
		select result1.model, if(result2.plan is null or result2.plan < 0, 0, result2.plan) as plan from
		(
		select distinct materials.model from materials where materials.origin_group_code = '041' and materials.category = 'FG'
		) as result1
		left join
		(
		select materials.model, sum(plan) as plan from
		(
		select material_number, sum(quantity) as plan from production_schedules where due_date >= '".$first."' and due_date <= '".$h4."' group by material_number
		union all
		select material_number, -(sum(quantity)) as plan from flo_details where date(created_at) >= '".$first."' and date(created_at) <= '".$h4."' group by material_number
		) r
		left join materials on materials.material_number = r.material_number
		group by materials.model
		) as result2
		on result1.model = result2.model order by result1.model asc";

		$plan = DB::select($query2);

		$response = array(
			'status' => true,
			'stampperakitan' => $stampperakitan,
			'kariawase' => $kariawase,
			'tanpoireperakitan' => $tanpoireperakitan,
			'tanpoawase' => $tanpoawase,
			'seasoningkango' => $seasoningkango,
			'renraku' => $renraku,
			'qafungsi' => $qafungsi,
			'fukiagerepair' => $fukiagerepair,
			'qavisual' => $qavisual,
			'plan' => $plan,
		);
		return Response::json($response);
	}

	public function fetchProcessAssyFLActualChart(Request $request){
		$first = date('Y-m-01');
		$now = date('Y-m-d');

		$next_process = $request->get('processCode')+1;

		$query = "select model, sum(plan) as plan, sum(out_item) as out_item, sum(in_item) as in_item from
		(
		select model, quantity as plan, 0 as out_item, 0 as in_item from stamp_schedules where due_date = '" . $now . "'

		union all

		select model, 0 as plan, quantity as out_item, 0 as in_item from log_processes 
		where process_code = '" . $next_process . "' and date(created_at) = '" . $now . "'

		union all

		select model, 0 as plan, 0 as out_item, quantity as in_item from log_processes 
		where process_code = '" . $request->get('processCode') . "' and date(created_at) = '" . $now . "'

		) as plan
		group by model
		having model like 'YFL%'";

		$chartData = DB::select($query);

		if(date('D')=='Fri'){
			if(date('Y-m-d h:i:s') >= date('Y-m-d 09:30:00')){
				$deduction = 600;
			}
			elseif(date('Y-m-d h:i:s') >= date('Y-m-d 13:10:00')){
				$deduction = 4800;
			}
			elseif(date('Y-m-d h:i:s') >= date('Y-m-d 15:00:00')){
				$deduction = 5400;
			}
			elseif(date('Y-m-d h:i:s') >= date('Y-m-d 17:30:00')){
				$deduction = 5800;
			}
			elseif(date('Y-m-d h:i:s') >= date('Y-m-d 18:30:00')){
				$deduction = 7500;
			}
			else{
				$deduction = 0;
			}
		}
		else{
			if(date('Y-m-d h:i:s') >= date('Y-m-d 09:30:00')){
				$deduction = 600;
			}
			elseif(date('Y-m-d h:i:s') >= date('Y-m-d 12:40:00')){
				$deduction = 3000;
			}
			elseif(date('Y-m-d h:i:s') >= date('Y-m-d 14:30:00')){
				$deduction = 3600;
			}
			elseif(date('Y-m-d h:i:s') >= date('Y-m-d 17:00:00')){
				$deduction = 4200;
			}
			elseif(date('Y-m-d h:i:s') >= date('Y-m-d 18:30:00')){
				$deduction = 5700;
			}
			else{
				$deduction = 0;
			}
		}

		$query2 = "select date(log_processes.created_at) as due_date, sum(log_processes.quantity) as quantity, (select avg(manpower) from log_processes where log_processes.process_code = " . $request->get('processCode') . " and date(created_at) = '" . $now . "') as manpower, max(log_processes.created_at) as last_input,
		round(sum(log_processes.quantity*st_assemblies.st)*60) as std_time,
		(timestampdiff(second, '" . date('Y-m-d 07:05:00') . "', max(log_processes.created_at))-" . $deduction . ")*(select avg(manpower) from log_processes where log_processes.process_code = " . $request->get('processCode') . " and date(created_at) = '".$now."') as act_time 
		from log_processes 
		left join st_assemblies 
		on st_assemblies.model = log_processes.model 
		where log_processes.process_code = " . $next_process . " and st_assemblies.process_code = ".$request->get('processCode')." 
		and date(log_processes.created_at) = '" . $now . "' 
		group by date(log_processes.created_at)";

		$effData = DB::select($query2);

		$totalStock = StampInventory::where('process_code', '=', $request->get('processCode'))
		->whereNull('status')
		->sum('quantity');

		$response = array(
			'status' => true,
			'chartData' => $chartData,
			'effData' => $effData,
			'totalStock' => $totalStock
		);
		return Response::json($response);
	}

	public function fetchProcessAssyFLDisplayActualChart(){
		$first = date('Y-m-01');
		$now = date('Y-m-d');

		$query = "select due_date, sum(plan) as plan, sum(actual) as actual from
		(
		select due_date as due_date, quantity as plan, 0 as actual from stamp_schedules where due_date >= '" . $first . "' and due_date <= '" . $now . "' and model like 'YFL%'

		union all

		select date(created_at) as due_date, 0 as plan, quantity as actual from log_processes where process_code = '2' and date(created_at) >= '" . $first . "' and date(created_at) <= '" . $now . "' and model like 'YFL%'
		) as plan
		group by due_date";

		$planData = DB::select($query);


		$query2 = "select model, sum(plan) as plan, sum(actual) as actual from
		(
		select model, quantity as plan, 0 as actual from stamp_schedules where due_date = '" . $now . "'

		union all

		select model, 0 as plan, quantity as actual from log_processes where process_code = '2' and date(created_at) = '" . $now . "'
		) as plan
		group by model
		having model like 'YFL%'";

		$planTable = DB::select($query2);

		$response = array(
			'status' => true,
			'planData' => $planData,
			'planTable' => $planTable,
		);
		return Response::json($response);
	}

	public function inputProcessAssyFL(Request $request){
		$stamp = LogProcess::where('serial_number', '=', $request->get('serialNumber'))
		->where('model', 'like', 'YFL%')
		->first();

		try{
			$id = Auth::id();

			$log_process = LogProcess::updateOrCreate(
				[
					'process_code' => $request->get('processCode'), 
					'serial_number' => $request->get('serialNumber'),
					'origin_group_code' => $request->get('originGroupCode')
				],
				[
					'model' => $stamp->model,
					'manpower' => $request->get('manPower'),
					'quantity' => 1,
					'created_by' => $id,
					'created_at' => date('Y-m-d H:i:s')
				]
			);

			$inventory = StampInventory::where('serial_number', '=', $request->get('serialNumber'))
			->where('origin_group_code', '=', '041')
			->first();

			$inventory->status = null;
			$inventory->process_code = $request->get('processCode');

			$inventory->save();
			$log_process->save();

			$response = array(
				'status' => true,
				'message' => 'Input success',
			);
			return Response::json($response);
		}
		catch (QueryException $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}


	// public function tampPlan(){

	// 	$now = date('Y-m-d');

	// 	$query = "select model, sum(plan) as plan, sum(actual) as actual from
	// 	(
	// 	select model, quantity as plan, 0 as actual from stamp_schedules where due_date = '" . $now . "'

	// 	union all

	// 	select model, 0 as plan, quantity as actual from log_processes where process_code = '1' and date(created_at) = '" . $now . "'
	// 	) as plan
	// 	group by model
	// 	having model like 'YFL%'";

	// 	$planData = DB::select($query);
	// 	$materials = DB::table('materials')->where('model', 'like', 'YFL%')->select('model')->distinct()->get();

	// 	$response = array(
	// 		'status' => true,
	// 		'planData' => $planData,
	// 		'model' => $materials,
	// 	);
	// 	return Response::json($response);
	// }

	public function fetchSerialNumber(Request $request){
		$code_generator = DB::table('code_generators')->where('note', '=', $request->get('originGroupCode'))->first();
		$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index);
		$number2 = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

		$lastCounter = $code_generator->prefix.$number;
		$nextCounter = $code_generator->prefix.$number2;

		$response = array(
			'status' => true,
			'lastCounter' => $lastCounter,
			'nextCounter' => $nextCounter,
		);
		return Response::json($response);
	}

	// public function fetchResult(){
	// 	$now = date('Y-m-d');
	// 	$log_processes = db::table('log_processes')
	// 	->where('process_code', '=', '1')
	// 	->where('model', 'like', 'YFL%')
	// 	->where(db::raw('date(created_at)'), '=', $now)
	// 	->orderBy('created_at', 'desc')
	// 	->get();

	// 	$response = array(
	// 		'status' => true,
	// 		'resultData' => $log_processes,
	// 	);
	// 	return Response::json($response);
	// }

	public function adjust(Request $request){
		$code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();

		$prefix = $code_generator->prefix;
		$lastIndex = $code_generator->index;

		$response = array(
			'status' => true,
			'prefix' => $prefix,
			'lastIndex' => $lastIndex,
		);
		return Response::json($response);
	}

	public function adjustUpdate(Request $request){
		$code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();

		$code_generator->index = $request->get('lastIndex');
		$code_generator->prefix = $request->get('prefix');
		$code_generator->save();

		$response = array(
			'status' => true,
			'message' => 'Serial number adjustment success',
		);
		return Response::json($response);
	}

	public function adjustSerial(Request $request){
		if($request->get('adjust') == 'minus'){
			$code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();
			$code_generator->index = $code_generator->index-1;
			$code_generator->save();

			$response = array(
				'status' => true,
				'message' => 'Serial number adjusted minus',
			);
			return Response::json($response);
		}
		else{
			$code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();
			$code_generator->index = $code_generator->index+1;
			$code_generator->save();

			$response = array(
				'status' => true,
				'message' => 'Serial number adjusted plus',
			);
			return Response::json($response);
		}
	}

	public function editStamp(Request $request){
		$log_process = LogProcess::find($request->get('id'));

		$response = array(
			'status' => true,
			'logProcess' => $log_process,
		);
		return Response::json($response);
	}

	public function destroyStamp(Request $request){
		$stamp = LogProcess::find($request->get('id'));

		$log_process = LogProcess::where('log_processes.serial_number', '=', $stamp->serial_number)
		->where('log_processes.model', '=', $stamp->model);

		$stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $stamp->serial_number)
		->where('stamp_inventories.model', '=', $stamp->model);

		$id = Auth::id();
		$err = new ErrorLog([
			'error_message' => 'Hapus - '.$stamp->model.'-'.$stamp->serial_number.'-'.$stamp->origin_group_code, 
			'created_by' => $id,
		]);

		$err->save();

		$log_process->forceDelete();
		$stamp_inventory->forceDelete();

		$response = array(
			'status' => true,
			'message' => 'Delete Success',
		);
		return Response::json($response);
	}

	public function updateStamp(Request $request){
		$stamp = LogProcess::find($request->get('id'));

		$log_process = LogProcess::where('log_processes.serial_number', '=', $stamp->serial_number)
		->where('log_processes.model', '=', $stamp->model)
		->first();
		$log_process->model = $request->get('model');

		$stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $stamp->serial_number)
		->where('stamp_inventories.model', '=', $stamp->model)
		->where('stamp_inventories.origin_group_code', '=', $request->get('originGroupCode'));

		$stamp_inventory->update(['model' => $request->get('model')]);
		$log_process->save();

		$id = Auth::id();
		$err = new ErrorLog([
			'error_message' => 'Edit - '.$stamp->model.'-'.$stamp->serial_number.'-'.$stamp->origin_group_code.'- To - '.$request->get('model'), 
			'created_by' => $id,
		]);

		$err->save();

		$response = array(
			'status' => true,
			'message' => 'Update Success',
		);
		return Response::json($response);
	}


	public function reprint_stamp(Request $request)
	{
		$model = db::table('stamp_inventories')
		->where('model', 'like', 'YFL%')
		->where('serial_number', '=', $request->get('stamp_number_reprint'))
		->select ('model')
		->first();

		if ($request->get('stamp_number_reprint') != null){
			try {
				// $code_generator = CodeGenerator::where('note', '=', '041')->first();
				// $code_generator->index = $code_generator->index+1;
				// $code_generator->save();

				$printer_name = 'SUPERMAN';

				$connector = new WindowsPrintConnector($printer_name);
				$printer = new Printer($connector);

				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->setBarcodeWidth(2);
				$printer->setBarcodeHeight(64);
				$printer->barcode($request->get('stamp_number_reprint'), Printer::BARCODE_CODE39);
			// $printer->qrCode($request->get('serialNumber'));
				$printer->setTextSize(3, 1);
				$printer->text($request->get('stamp_number_reprint')."\n");
				$printer->feed(1);
				$printer->text($model->model."\n");
				$printer->setTextSize(1, 1);
				$printer->text(date("d-M-Y H:i:s")."\n");
				$printer->cut();
				$printer->close();

				return back()->with('status', 'Stamp has been reprinted.')->with('page', 'Assembly Process');
			}
			catch(\Exception $e){
				return back()->with("error", "Couldn't print to this printer " . $e->getMessage() . "\n");
			}
		}
		else{
			return back()->with('error', 'Serial number '. $request->get('stamp_number_reprint') . ' not found.');
		}
	}

	public function stamp(Request $request){
		try{
			if ($request->get('originGroupCode') =='041') {
				$plc = new ActMLEasyIf(0);
				$datas = $plc->read_data('D50', 5);
				$plc_counter = PlcCounter::where('origin_group_code', '=', $request->get('originGroupCode'))->first();	
			}else if ($request->get('originGroupCode') =='042') {
				$plc = new ActMLEasyIf(0);
				$datas = $plc->read_data('D70', 5);
				$plc_counter = PlcCounter::where('origin_group_code', '=', $request->get('originGroupCode'))->first();	
			}else if ($request->get('originGroupCode') =='043') {
				$plc = new ActMLEasyIf(0);
				$datas = $plc->read_data('D60', 15);
				$plc_counter = PlcCounter::where('origin_group_code', '=', $request->get('originGroupCode'))->first();	
			}
			$data = $datas[0];

			// $data = 38025;

			$code_generator = CodeGenerator::where('note', '=', $request->get('originGroupCode'))->first();
			$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);

			$serial_number = $code_generator->prefix.$number;

			if($plc_counter->plc_counter != $data){

				if(Auth::user()->role_code == "OP-SubAssy-FL"){

					$id = Auth::id();

					$plc_counter->plc_counter = $data;

					$log_process = LogProcess::updateOrCreate(
						[
							'process_code' => $request->get('processCode'), 
							'serial_number' => $serial_number,
							'origin_group_code' => $request->get('originGroupCode')
						],
						[
							'model' => $request->get('model'),
							'manpower' => $request->get('manPower'),
							'quantity' => 1,
							'created_by' => $id,
							'created_at' => date('Y-m-d H:i:s'),
							'remark' =>$request->get('category'),
						]
					);

					if ($request->get('category')=='FG'){

						$stamp_inventory = StampInventory::updateOrCreate(
							[
								'serial_number' => $serial_number,
								'origin_group_code' => $request->get('originGroupCode')
							],
							[
								'process_code' => $request->get('processCode'), 
								'model' => $request->get('model'),
								'quantity' => 1
							]
						);

						$stamp_inventory->save();
					}

					$code_generator->index = $code_generator->index+1;
					try{
						// DB::transaction(function() use ($code_generator, $plc_counter, $log_process){
						$code_generator->save();
						$plc_counter->save();
						$log_process->save();
						// });				
					}
					catch(\Exception $e){
						$response = array(
							'status' => false,
							'message' => $e->getMessage(),
						);
						return Response::json($response);
					}

					// if ($request->get('category')=='FG'){
					$printer_name = 'SUPERMAN';

					$connector = new WindowsPrintConnector($printer_name);
					$printer = new Printer($connector);

					$printer->setJustification(Printer::JUSTIFY_CENTER);
					$printer->setBarcodeWidth(2);
					$printer->setBarcodeHeight(64);
					$printer->barcode($serial_number, Printer::BARCODE_CODE39);
					$printer->setTextSize(3, 1);
					$printer->text($serial_number."\n");
					$printer->feed(1);
					$printer->text($request->get('model')."\n");
					$printer->setTextSize(1, 1);
					$printer->text(date("d-M-Y H:i:s")."\n");
					$printer->cut();
					$printer->close();
					// }

					$response = array(
						'status' => true,
						'statusCode' => 'stamp',
						'message' => 'Stamp success',
						'data' => $plc_counter->plc_counter
					);
					return Response::json($response);
				}
				else if(Auth::user()->role_code == "OP-Body-CL"){

					$id = Auth::id();

					$plc_counter->plc_counter = $data;

					$log_process = LogProcess::updateOrCreate(
						[
							'process_code' => $request->get('processCode'), 
							'serial_number' => $serial_number,
							'origin_group_code' => $request->get('originGroupCode')
						],
						[
							'model' => $request->get('model'),
							'manpower' => $request->get('manPower'),
							'quantity' => 1,
							'created_by' => $id,
							'created_at' => date('Y-m-d H:i:s')
						]
					);

					if ($request->get('category')=='fg'){

						$stamp_inventory = StampInventory::updateOrCreate(
							[
								'serial_number' => $serial_number,
								'origin_group_code' => $request->get('originGroupCode')
							],
							[
								'process_code' => $request->get('processCode'), 
								'model' => $request->get('model'),
								'quantity' => 1
							]
						);

						$stamp_inventory->save();
					}

					$code_generator->index = $code_generator->index+1;
					try{
						// DB::transaction(function() use ($code_generator, $plc_counter, $log_process){
						$code_generator->save();
						$plc_counter->save();
						$log_process->save();
						// });				
					}
					catch(\Exception $e){
						$response = array(
							'status' => false,
							'message' => $e->getMessage(),
						);
						return Response::json($response);
					}

					$response = array(
						'status' => true,
						'statusCode' => 'stamp',
						'message' => 'Stamp success',
						'data' => $plc_counter->plc_counter
					);
					return Response::json($response);

				}else if(str_contains(Auth::user()->role_code,'WP')){

					$id = Auth::id();

					$plc_counter->plc_counter = $data;

					$log_process = LogProcess::updateOrCreate(
						[
							'process_code' => $request->get('processCode'), 
							'serial_number' => $serial_number,
							'origin_group_code' => $request->get('originGroupCode')
						],
						[
							'model' => $request->get('model'),
							'manpower' => $request->get('manPower'),
							'quantity' => 1,
							'status_material' => $request->get('tipeItem'),
							'created_by' => $id,
							'created_at' => date('Y-m-d H:i:s'),
							'remark' =>$request->get('category'),
						]
					);

					if ($request->get('category')=='FG'){

						$stamp_inventory = StampInventory::updateOrCreate(
							[
								'serial_number' => $serial_number,
								'origin_group_code' => $request->get('originGroupCode')
							],
							[
								'process_code' => $request->get('processCode'), 
								'model' => $request->get('model'),
								'quantity' => 1
							]
						);

						$stamp_inventory->save();
					}
					
					$code_generator->index = $code_generator->index+1;
					try{
						// DB::transaction(function() use ($code_generator, $plc_counter, $log_process){
						$code_generator->save();
						$plc_counter->save();
						$log_process->save();
						// });				
					}
					catch(\Exception $e){
						$response = array(
							'status' => false,
							'message' => $e->getMessage(),
						);
						return Response::json($response);
					}

					$response = array(
						'status' => true,
						'statusCode' => 'stamp',
						'message' => 'Stamp success',
						'data' => $plc_counter->plc_counter
					);
					return Response::json($response);

				}
			}
			else{
				$response = array(
					'status' => true,
					'statusCode' => 'noStamp',
				);
				return Response::json($response);
			}
		}
		catch (\Exception $e){
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function filter_stamp_detail(Request $request){
		$flo_detailsTable = DB::table('log_processes')
		->leftJoin(db::raw('(select process_code, process_name from processes where remark = "YFL041") AS processes'), 'processes.process_code', '=', 'log_processes.process_code')
		->select('log_processes.serial_number', 'log_processes.model', 'log_processes.quantity','processes.process_name', db::raw('date_format(log_processes.created_at, "%d-%b-%Y") as st_date') );

		if(strlen($request->get('datefrom')) > 0){
			$date_from = date('Y-m-d', strtotime($request->get('datefrom')));
			$flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(log_processes.created_at, "%Y-%m-%d")'), '>=', $date_from);
		}

		if(strlen($request->get('code')) > 0){
			$code = $request->get('code');
			$flo_detailsTable = $flo_detailsTable->where('log_processes.process_code','=', $code );
		}

		if(strlen($request->get('dateto')) > 0){
			$date_to = date('Y-m-d', strtotime($request->get('dateto')));
			$flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(log_processes.created_at, "%Y-%m-%d")'), '<=', $date_to);
		}

		$stamp_detail = $flo_detailsTable->orderBy('log_processes.created_at', 'desc')->get();

		return DataTables::of($stamp_detail)
		->addColumn('action', function($stamp_detail){
			return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteConfirmation(id)" id="' . $stamp_detail->serial_number . '"><i class="glyphicon glyphicon-trash"></i></a>';
		})
		->make(true);
	}

	// public function fetchwipflallchart(Request $request){

	// 	$first = date('Y-m-01');
	// 	$now = date('Y-m-d');

	// 	$target = DB::table('production_schedules')
	// 	->leftJoin('materials', 'materials.material_number', '=', 'production_schedules.material_number')
	// 	->where('production_schedules.due_date', '=', $now)
	// 	->where('materials.category', '=', 'FG')
	// 	->where('materials.hpl', '=', 'FLFG');
	// 	$stock = DB::table('stamp_inventories');

	// 	$targetFL = $target->where('origin_group_code', '=', $request->get('originGroupCode'))->sum('production_schedules.quantity');
	// 	$stockFL = $stock->where('origin_group_code', '=', $request->get('originGroupCode'))->whereNull('status')->sum('stamp_inventories.quantity');

	// 	if($targetFL != 0){
	// 		$dayFL = floor($stockFL/$targetFL);
	// 		$addFL = ($stockFL/$targetFL)-$dayFL;
	// 		$currStock = round($stockFL/$targetFL,1);
	// 	}
	// 	else{
	// 		$dayFL = 2;
	// 		$addFL = 1;
	// 		$currStock = round($stockFL/1,1);
	// 	}

	// 	$last = date('Y-m-d', strtotime(carbon::now()->endOfMonth()));

	// 	if(date('D')=='Fri' || date('D')=='Wed' || date('D')=='Thu' || date('D')=='Sat'){
	// 		$hFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL+2)));
	// 		$aFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL+3)));
	// 	}
	// 	elseif(date('D')=='Sun'){
	// 		$hFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL+1)));
	// 		$aFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL+2)));
	// 	}
	// 	else{
	// 		$hFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL)));
	// 		$aFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL+1)));
	// 	}

	// 	$query = "select stamp_inventories.process_code, sum(stamp_inventories.quantity) as qty from stamp_inventories where stamp_inventories.status is null and stamp_inventories.origin_group_code = '041' and stamp_inventories.deleted_at is null group by stamp_inventories.process_code";

	// 	$query2 = "select model, sum(plan) as plan, sum(stock) as stock, sum(max_plan) as max_plan from
	// 	(
	// 	select materials.model, sum(plan) as plan, 0 as stock, sum(max_plan) as max_plan from
	// 	(
	// 	select material_number, sum(quantity) as plan, 0 as max_plan from production_schedules where due_date >= '".$first."' and due_date <= '".$hFL."' group by material_number

	// 	union all

	// 	select material_number, round(sum(quantity)*".$addFL.") as plan, 0 as max_plan from production_schedules where due_date = '".$aFL."' group by material_number

	// 	union all

	// 	select material_number, 0 as plan, sum(quantity) as max_plan from production_schedules where due_date >= '".$first."' and due_date <= '".$last."' group by material_number

	// 	union all

	// 	select material_number, -(sum(quantity)) as plan, -(sum(quantity)) as max_plan from flo_details where date(created_at) >= '".$first."' and date(created_at) <= '".$aFL."' group by material_number
	// 	) result1
	// 	left join materials on materials.material_number = result1.material_number
	// 	group by materials.model

	// 	union all

	// 	select model, 0 as plan, sum(quantity) as stock, 0 as max_plan from stamp_inventories where status is null and model like 'YFL%' group by model
	// 	) as result2
	// 	group by model having model like 'YFL%' and plan > 0 or stock > 0 order by model asc";

	// 	$stockData = DB::select($query);
	// 	$efficiencyData = DB::select($query2);

	// 	$response = array(
	// 		'status' => true,
	// 		'efficiencyData' => $efficiencyData,
	// 		'stockData' => $stockData,
	// 		'currStock' => $currStock,
	// 	);
	// 	return Response::json($response);
	// }

	public function fetchwipflallchart(Request $request){

		$first = date('Y-m-01');
		$now = date('Y-m-d');

		$target = DB::table('production_schedules')
		->leftJoin('materials', 'materials.material_number', '=', 'production_schedules.material_number')
		->where('production_schedules.due_date', '=', $now)
		->where('materials.category', '=', 'FG')
		->where('materials.hpl', '=', 'FLFG');
		$stock = DB::table('assembly_inventories');

		$targetFL = $target->where('origin_group_code', '=', $request->get('originGroupCode'))->sum('production_schedules.quantity');
		$stockFL = $stock->where('origin_group_code', '=', $request->get('originGroupCode'))->count('assembly_inventories.id');

		if($targetFL != 0){
			$dayFL = floor($stockFL/$targetFL);
			$addFL = ($stockFL/$targetFL)-$dayFL;
			$currStock = round($stockFL/$targetFL,1);
		}
		else{
			$dayFL = 2;
			$addFL = 1;
			$currStock = round($stockFL/1,1);
		}

		$last = date('Y-m-d', strtotime(carbon::now()->endOfMonth()));

		if(date('D')=='Fri' || date('D')=='Wed' || date('D')=='Thu' || date('D')=='Sat'){
			$hFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL+2)));
			$aFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL+3)));
		}
		elseif(date('D')=='Sun'){
			$hFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL+1)));
			$aFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL+2)));
		}
		else{
			$hFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL)));
			$aFL = date('Y-m-d', strtotime(carbon::now()->addDays($dayFL+1)));
		}

		$query = "SELECT
		processes.process_code,
		( SELECT count( id ) FROM assembly_inventories WHERE location = processes.process_name ) AS qty 
		FROM
		processes 
		WHERE
		processes.remark = 041";

		$query2 = "select model, sum(plan) as plan, sum(stock) as stock, sum(max_plan) as max_plan from
		(
		select materials.model, sum(plan) as plan, 0 as stock, sum(max_plan) as max_plan from
		(
		select material_number, sum(quantity) as plan, 0 as max_plan from production_schedules where due_date >= '".$first."' and due_date <= '".$hFL."' group by material_number

		union all

		select material_number, round(sum(quantity)*".$addFL.") as plan, 0 as max_plan from production_schedules where due_date = '".$aFL."' group by material_number

		union all

		select material_number, 0 as plan, sum(quantity) as max_plan from production_schedules where due_date >= '".$first."' and due_date <= '".$last."' group by material_number

		union all

		select material_number, -(sum(quantity)) as plan, -(sum(quantity)) as max_plan from flo_details where date(created_at) >= '".$first."' and date(created_at) <= '".$aFL."' group by material_number
		) result1
		left join materials on materials.material_number = result1.material_number
		group by materials.model

		union all

		select model, 0 as plan, count(id) as stock, 0 as max_plan from assembly_inventories where location = 'stamp-process' group by model
		) as result2
		group by model having model like 'YFL%' and plan > 0 or stock > 0 order by model asc";

		$stockData = DB::select($query);
		$efficiencyData = DB::select($query2);

		$response = array(
			'status' => true,
			'efficiencyData' => $efficiencyData,
			'stockData' => $stockData,
			'currStock' => $currStock,
		);
		return Response::json($response);
	}

	//tambah ali stamp sax and cl

	public function indexProcessAssyFLCla1(){
		//$now = date('Y-m-d',strtotime('-4 days'));

		$model2 = StampInventory::where('origin_group_code','=','042')->orderBy('created_at', 'desc')
		->get();
		return view('processes.assy_fl_cla.stamp',array(
			'model2' => $model2,
		))
		->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexProcessAssyFLSaxT1(){
		//$now = date('Y-m-d',strtotime('-4 days'));

		$model2 = StampInventory::where('origin_group_code','=','043')->orderBy('created_at', 'desc')
		->get();
		return view('processes.welding.stamp',array(
			'model2' => $model2,
		))
		->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexProcessStampSX(){
		return view('processes.welding.index')->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}

	public function indexProcessStampSXassy(){
		$role = Auth::user()->role_code;
		return view('processes.assy_fl_saxT.index')->with('page', 'Process Assy FL')->with('head', 'Assembly Process')->with('role',$role);
	}
	public function indexProcessStampCl(){
		$role = Auth::user()->role_code;
		return view('processes.assy_fl_cla.index')->with('page', 'Process Assy FL')->with('head', 'Assembly Process')->with('role',$role);
	}

	public function indexResumesCL(){
		return view('processes.assy_fl_cla.resumes')
		->with('page', 'Process Assy CL')->with('head', 'Assembly Process');
	}


	public function indexProcessKensa($id){
		if($id == 'subassy-incoming-sx'){
			$title = 'I.C. Saxophone Key';
			$title_jp= '';
		}
		
		return view('processes.assy_fl_saxT.kensa', array(
			'loc' => $id,
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Process Middle SX')->with('head', 'Middle Process');
	}

	public function scanAssemblyKensa(Request $request){
		$id = Auth::id();
		$started_at = date('Y-m-d H:i:s');

		$inventory = AssemblyKeyInventory::where('tag', '=', $request->get('tag'))
		->leftJoin('materials', 'materials.material_number', '=', 'assembly_key_inventories.material_number')
		->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'assembly_key_inventories.last_check')
		->select(
			'materials.model',
			'materials.key',
			'materials.surface',
			'assembly_key_inventories.material_number',
			'assembly_key_inventories.quantity',
			'assembly_key_inventories.tag',
			'employee_syncs.employee_id',
			'employee_syncs.name'
		)
		->first();

		if(count($inventory) > 0){
			$surface = '';
			if(str_contains(strtolower($inventory->surface), 'lcq')){
				$surface = 'lacquering';
			}elseif(str_contains(strtolower($inventory->surface), 'plt')){
				$surface = 'plating';
			}

			$ng_lists = DB::table('ng_lists')
			->where('location', '=', $request->get('loc'))
			->where('remark', 'LIKE', '%'. $surface . '%')
			->get();

			$response = array(
				'status' => true,
				'message' => 'ID slip found.',
				'ng_lists' => $ng_lists,
				'middle_inventory' => $inventory,
				'started_at' => $started_at,
			);
			return Response::json($response);
		}
		else{
			$response = array(
				'status' => false,
				'message' => 'ID slip not found.',
			);
			return Response::json($response);
		}
	}

	public function inputAssemblyKensa(Request $request){

		$loc = $request->get('loc');
		$code_generator = CodeGenerator::where('note','=','middle-kensa')->first();
		$code = $code_generator->index+1;
		$code_generator->index = $code;
		$code_generator->save();

		$material = Material::where('material_number', $request->get('material_number'))->first();

		if($request->get('ng')){
			foreach ($request->get('ng') as $ng) {

				$middle_ng_log;
				if(str_contains($material->surface, 'PLT')){
					$middle_ng_log = new MiddlePlatingNgLog([
						'employee_id' => $request->get('employee_id'),
						'tag' => $request->get('tag'),
						'material_number' => $request->get('material_number'),
						'ng_name' => $ng[0],
						'quantity' => $ng[1],
						'location' => $request->get('loc'),
						'started_at' => $request->get('started_at'),
						'operator_id' => $request->get('operator_id'),
						'remark' => $code
					]);
				}else{
					$middle_ng_log = new MiddleLacqueringNgLog([
						'employee_id' => $request->get('employee_id'),
						'tag' => $request->get('tag'),
						'material_number' => $request->get('material_number'),
						'ng_name' => $ng[0],
						'quantity' => $ng[1],
						'location' => $request->get('loc'),
						'started_at' => $request->get('started_at'),
						'operator_id' => $request->get('operator_id'),
						'remark' => $code
					]);
				}


				try{
					$middle_ng_log->save();
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


		$inventory = AssemblyKeyInventory::where('tag', '=', $request->get('tag'))->first();

		$middle_log;
		if(str_contains($material->surface, 'PLT')){
			$middle_log = new MiddlePlatingLog([
				'employee_id' => $request->get('employee_id'),
				'tag' => $request->get('tag'),
				'material_number' => $request->get('material_number'),
				'quantity' => $request->get('quantity'),
				'location' => $request->get('loc'),
				'started_at' => $request->get('started_at'),
				'operator_id' => $request->get('operator_id')
			]);			
		}else{
			$middle_log = new MiddleLacqueringLog([
				'employee_id' => $request->get('employee_id'),
				'tag' => $request->get('tag'),
				'material_number' => $request->get('material_number'),
				'quantity' => $request->get('quantity'),
				'location' => $request->get('loc'),
				'started_at' => $request->get('started_at'),
				'operator_id' => $request->get('operator_id')
			]);
		}

		$middle_check_log;
		if(str_contains($material->surface, 'PLT')){
			$middle_check_log = new MiddlePlatingCheckLog([
				'employee_id' => $request->get('employee_id'),
				'tag' => $request->get('tag'),
				'material_number' => $request->get('material_number'),
				'quantity' => $request->get('quantity'),
				'location' => $request->get('loc'),
				'operator_id' => $request->get('operator_id')
			]);
		}else{
			$middle_check_log = new MiddleLacqueringCheckLog([
				'employee_id' => $request->get('employee_id'),
				'tag' => $request->get('tag'),
				'material_number' => $request->get('material_number'),
				'quantity' => $request->get('quantity'),
				'location' => $request->get('loc'),
				'operator_id' => $request->get('operator_id')
			]);
		}

		try{
			DB::transaction(function() use ($middle_check_log, $middle_log, $inventory){
				$middle_check_log->save();
				$middle_log->save();
				$inventory->forceDelete();
			});


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


	public function fetchStampPlan($id){
		$id_all = $id."%";

		$now = date('Y-m-d');

		$query = "select model, sum(plan) as plan, sum(actual) as actual from
		(
		select model, quantity as plan, 0 as actual from stamp_schedules where due_date = '" . $now . "'

		union all

		select model, 0 as plan, quantity as actual from log_processes where process_code = '1' and date(created_at) = '" . $now . "'
		) as plan
		group by model
		having model like '".$id_all."'";


		$query3 = "select model, sum(plan) as plan, sum(actual) as actual from
		(
		select model, quantity as plan, 0 as actual from stamp_schedules where due_date = '" . $now . "'

		union all

		select model, 0 as plan, quantity as actual from log_processes where process_code = '2' and date(created_at) = '" . $now . "'
		) as plan
		group by model
		having model like '".$id_all."'";



		$query2 = "select model, sum(plan) as plan, sum(actual) as actual from
		(
		select model, quantity as plan, 0 as actual from stamp_schedules where due_date = '" . $now . "'

		union all

		select model, 0 as plan, quantity as actual from log_processes where process_code = '1' and date(created_at) = '" . $now . "'
		) as plan
		WHERE MODEL in ('INDONESIA','CHINA')
		group by model
		";




		if ($id =="YAS") {
			$materials = DB::table('materials')->where('model', 'like', 'YAS%')->where('issue_storage_location', '=', 'sx21')->where('hpl', '=', 'ASBODY')
			->where('category', '=', 'wip')->select('model')->distinct()->get();
		}

		if ($id =="YTS") {
			$materials = DB::table('materials')->where('model', 'like', 'YTS%')->where('issue_storage_location', '=', 'sx21')->where('hpl', '=', 'TSBODY')
			->where('category', '=', 'wip')->select('model')->distinct()->get();
		}
		
		if ($id =="YFL") {
			$materials = DB::table('materials')->where('model', 'like', $id_all)->select('model')->distinct()->get();
			$planData ="";

		}

		if($id =="YCL"){
			$planData = DB::select($query2);
			$materials = "";
		}else{
			$planData = DB::select($query);
		}



		$response = array(
			'status' => true,
			'planData' => $planData,
			'model' => $materials,

		);
		return Response::json($response);
	}


	public function fetchResult($id){
		$now = date('Y-m-d');
		$yesterday = date('Y-m-01', strtotime("-1 months",strtotime($now)));
		$id_all = $id."%";
		$now = date('Y-m-d');
		if($id =="YCL"){
			$query="SELECT * FROM log_processes WHERE model IN ('INDONESIA','CHINA') and DATE_FORMAT(log_processes.created_at,'%Y-%m-%d') ='".$now."' ORDER BY created_at desc";
			$log_processes = db::select($query);

		}elseif($id =="YTS"){
		// 	$query="SELECT a.*, users.`name` FROM (
		// 	SELECT serial_number,model,created_at,id,created_by,remark FROM log_processes WHERE model LIKE 'YTS%' and process_code ='1'  AND DATE_FORMAT(created_at,'%Y-%m-%d')  >='".$yesterday."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$now."'
		// 	UNION ALL
		// 	SELECT serial_number,model,created_at,id,created_by,remark FROM log_processes WHERE model LIKE 'YAS%'  and process_code ='1' AND DATE_FORMAT(created_at,'%Y-%m-%d')  >='".$yesterday."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$now."'
		// ) A 
		// LEFT JOIN users on a.created_by = users.id			
		// ORDER BY serial_number DESC";

			$query = "
			SELECT serial_number,model,log_processes.created_at,log_processes.id,log_processes.created_by,remark,users.`name` from log_processes 
			LEFT JOIN 
			users on log_processes.created_by = users.id
			WHERE process_code ='1' and 
			DATE_FORMAT(log_processes.created_at,'%Y-%m-%d') ='".$now."' and model REGEXP 'YAS|YTS' ORDER BY log_processes.serial_number DESC
			";
			$log_processes = db::select($query);
		}elseif($id =="YTS2"){

			$query = "SELECT a.*,users.`name` from (
			SELECT serial_number,model,log_processes.created_at,log_processes.id from log_processes 	WHERE process_code ='2' and DATE_FORMAT(log_processes.created_at,'%Y-%m-%d') ='".$now."' and model REGEXP 'YAS|YTS' 
			) a LEFT JOIN (
			SELECT serial_number,model,log_processes.created_at,log_processes.id,log_processes.created_by,remark from log_processes 	WHERE process_code ='1' and model REGEXP 'YAS|YTS' 
			) b on a.serial_number = b.serial_number
			LEFT JOIN users on b.created_by = users.id
			ORDER BY created_at DESC
			";
			$log_processes = db::select($query);
		}elseif($id =="YTS3"){
			$query="SELECT * FROM (
			SELECT * FROM stamp_inventories WHERE model LIKE 'YTS%' and process_code ='3' 
			UNION ALL
			SELECT * FROM stamp_inventories WHERE model LIKE 'YAS%'  and process_code ='3'
		) A ORDER BY updated_at DESC";
		$log_processes = db::select($query);
	}else{
		$log_processes = db::table('log_processes')
		->where('process_code', '=', '1')
		->where('model', 'like', $id_all)
		->where(db::raw('date(created_at)'), '=', $now)
		->orderBy('created_at', 'desc')
		->get();
	}
	$response = array(
		'status' => true,
		'resultData' => $log_processes,
	);
	return Response::json($response);
}



//edit reprint ali

public function getModelReprintAll2(Request $request)
{
	$query ="SELECT material_number,serial_number from materials 
	LEFT JOIN log_processes 
	on materials.material_description = log_processes.model
	where serial_number ='".$request->get('sn')."' and process_code='4' and log_processes.origin_group_code='043'";

	$reprint = DB::select($query);
	$response = array(
		'status' => true,
		'reprint' => $reprint,
	);
	return Response::json($response);
}

public function getsnsax2(Request $request)
{
	$sn = StampInventory::where('process_code', '=', $request->get('code'))
	->where('origin_group_code','=' ,$request->get('origin'))
	->where('serial_number','=' ,$request->get('sn2'))
	->select('model', 'serial_number')
	->first();

	$sn2 = LogProcess::where('process_code', '=', '4')
	->where('origin_group_code','=' ,$request->get('origin'))
	->where('serial_number','=' ,$request->get('sn2'))
	->select('model', 'serial_number')
	->first();


	if ($sn != null) {
		$response = array(
			'status' => true,
			'message' => '1',
			'model' => $sn->model,
			'sn' => $sn->serial_number,
		);
		return Response::json($response);
	}
	elseif ($sn2 != null) {
		$response = array(
			'status' => true,
			'message' => '2',
			'model' => $sn2->model,
			'sn' => $sn2->serial_number,
		);
		return Response::json($response);
	}else{
		$response = array(
			'status' => false,
			'message' => 'Serial Number not registered',
		);
		return Response::json($response);
	}
}

public function label_kecil($id,$remark){
	$remark2 = $remark;
	$sn = $id;
	$date = date('Y-m-d');
	
	$query = " SELECT week_date,date_code from weekly_calendars WHERE week_date = 
	(
	SELECT DATE_FORMAT(created_at,'%Y-%m-%d')  from log_processes WHERE serial_number='".$sn."' and process_code='4' and origin_group_code='043'
)";
$barcode = DB::select($query);

return view('processes.assy_fl_saxT.print_label_kecil',array(
	'barcode' => $barcode,
	'sn' => $sn,
	'remark' => $remark2,
))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function label_besar($id,$gmc,$remark){

	$date = date('Y-m-d');
	if ($remark =="J") {
		$query ="select stamp_inventories.serial_number,finished,janean,upc,date_code,remark,stamp_inventories.model from (
		select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
		INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
		WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and stamp_hierarchies.finished='".$gmc."'  and stamp_hierarchies.remark ='J' and log_processes.origin_group_code ='043'
		) a INNER JOIN (
		SELECT week_date,date_code from weekly_calendars WHERE week_date='".$date."')b
		on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
		INNER JOIN stamp_inventories on a.serial_number = stamp_inventories.serial_number";
	}

	elseif ($remark =="NJ"){
		$query ="select stamp_inventories.serial_number,finished,janean,upc,date_code,remark,stamp_inventories.model from (
		select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
		INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
		WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and stamp_hierarchies.finished='".$gmc."'  and stamp_hierarchies.remark !='J' and log_processes.origin_group_code ='043'
		) a INNER JOIN (
		SELECT week_date,date_code from weekly_calendars WHERE week_date='".$date."')b
		on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
		INNER JOIN stamp_inventories on a.serial_number = stamp_inventories.serial_number";
	}

	elseif ($remark =="JR") {
		$query ="select stamp_inventories.serial_number,finished,janean,upc,date_code,remark,stamp_inventories.model from (
		select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
		INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
		WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and stamp_hierarchies.finished='".$gmc."'  and stamp_hierarchies.remark ='J' and log_processes.origin_group_code ='043'
		) a INNER JOIN (
		SELECT week_date,date_code from weekly_calendars WHERE week_date=(select DATE_FORMAT(created_at,'%Y-%m-%d')as a  from log_processes WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and log_processes.origin_group_code ='043'))b
		on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
		INNER JOIN stamp_inventories on a.serial_number = stamp_inventories.serial_number";
	}

	elseif ($remark =="NJR"){
		$query ="select stamp_inventories.serial_number,finished,janean,upc,date_code,remark,stamp_inventories.model from (
		select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
		INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
		WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and stamp_hierarchies.finished='".$gmc."'  and stamp_hierarchies.remark !='J' and log_processes.origin_group_code ='043'
		) a INNER JOIN (
		SELECT week_date,date_code from weekly_calendars WHERE week_date=(select DATE_FORMAT(created_at,'%Y-%m-%d')as a  from log_processes WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and log_processes.origin_group_code ='043'))b
		on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
		INNER JOIN stamp_inventories on a.serial_number = stamp_inventories.serial_number";
	}elseif ($remark =="JRB") {
		$query ="select serial_number,finished,janean,upc,date_code, remark,c.model_2 as model from (
		select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
		INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
		WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and stamp_hierarchies.finished='".$gmc."'  and stamp_hierarchies.remark ='J' and log_processes.origin_group_code ='043'
		) a INNER JOIN (
		SELECT week_date,date_code from weekly_calendars WHERE week_date=(select DATE_FORMAT(created_at,'%Y-%m-%d')as a  from log_processes WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and log_processes.origin_group_code ='043'))b
		on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
		LEFT JOIN 
		(
		SELECT model as model_2, serial_number as sn2 from log_processes WHERE serial_number='".$id."' and process_code='4' and log_processes.origin_group_code ='043'
	) c on a.serial_number = c.sn2";
}

elseif ($remark =="NJRB"){
	$query ="select serial_number,finished,janean,upc,date_code, remark,c.model_2 as model from (
	select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
	INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
	WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and stamp_hierarchies.finished='".$gmc."'  and stamp_hierarchies.remark !='J' and log_processes.origin_group_code ='043'
	) a INNER JOIN (
	SELECT week_date,date_code from weekly_calendars WHERE week_date=(select DATE_FORMAT(created_at,'%Y-%m-%d')as a  from log_processes WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and log_processes.origin_group_code ='043'))b
	on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
	LEFT JOIN 
	(
	SELECT model as model_2, serial_number as sn2 from log_processes WHERE serial_number='".$id."' and process_code='4' and log_processes.origin_group_code ='043'
) c on a.serial_number = c.sn2";
}

$barcode = DB::select($query);

$date = date('Y-m-d');
$querydate = "SELECT week_date,date_code from weekly_calendars WHERE week_date = 
(
	SELECT DATE_FORMAT(created_at,'%Y-%m-%d')  from log_processes WHERE serial_number='".$id."' and process_code='4' and origin_group_code='043'
)";
$date2 = DB::select($querydate);

return view('processes.assy_fl_saxT.print_label_besar',array(
	'barcode' => $barcode,
	'date2' => $date2,

	'remark' => $remark,
))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

//end edit reprint ali


// print saxophone
public function getsnsax(Request $request)
{
	$sn = StampInventory::where('process_code', '=', $request->get('code'))
	->where('origin_group_code','=' ,$request->get('origin'))
	->where('serial_number','=' ,$request->get('sn'))
	->select('model', 'serial_number','status')
	->first();

	$sn2 = StampInventory::where('origin_group_code','=' ,$request->get('origin'))
	->where('serial_number','=' ,$request->get('sn'))
	->whereIn('process_code', ['2', '3'])
	->select('model', 'serial_number','status')
	->first();

	$code="";

	if ($sn != null) {

		if ($sn->status =="return") {
			$msg ="3";
		}
		else{
			$msg ="1";
		}	

		$response = array(
			'status' => true,
			'message' => $msg,
			'model' => $sn->model,
			'sn' => $sn->serial_number,

		);
		return Response::json($response);
	}elseif ($sn2 != null) {

		if ($sn2->status =="return") {
			$msg ="3";
		}
		else{
			$msg ="2";
		}

		$response = array(
			'status' => true,
			'message' => $msg,
			'model' => $sn2->model,
			'sn' => $sn2->serial_number,
		);
		return Response::json($response);

	}else{
		$logProcess = logProcess::where('process_code', '=', '2')
		->where('origin_group_code','=' ,$request->get('origin'))
		->where('serial_number','=' ,$request->get('sn'))
		->select('model', 'serial_number')
		->first();

		if ($logProcess == null) {
			$code = "input";
		}else{
			$code = "update";
			$id = Auth::id();
			self::mailSax($request->get('sn'), $request->get('model'),$id,'2');
		}

		$response = array(
			'status' => false,
			'message' => 'Serial Number not registered',
			'code' => $code,
		);
		return Response::json($response);
	}
}


public function print_sax(Request $request){
	$stamp = LogProcess::where('process_code', '=', $request->get('code'))
	->where('origin_group_code','=' ,$request->get('origin'))
	->where('serial_number','=' ,$request->get('sn'))
	->first();

	$stamp2 = LogProcess::where('process_code', '=', '2')
	->where('origin_group_code','=' ,$request->get('origin'))
	->where('serial_number','=' ,$request->get('sn'))
	->first();

	$return = StampInventory::where('origin_group_code','=' ,$request->get('origin'))
	->where('serial_number','=' ,$request->get('sn'))
	->first();

	if ($return->status =='return') {

		$id = Auth::id();
		$err = new ErrorLog([
			'error_message' => 'Return - Back - '.$request->get('sn').'-'.$return->model.'-'.$request->get('origin'), 
			'created_by' => $id,
		]);

		$err->save();

		$inventoryReturn = StampInventory::where('origin_group_code','=' ,$request->get('origin'))
		->where('serial_number','=' ,$request->get('sn'));
		$inventoryReturn->update(['status' => null]);
	}


	try{
		$id = Auth::id();
		if ($request->get('status') =="update") {
			if ($stamp != null) {
				$model = $stamp->model;
			}else{
				$model = $stamp2->model;
			}
			

			$log_process = LogProcess::updateOrCreate(
				[
					'process_code' => '2', 
					'serial_number' => $request->get('sn'),
					'origin_group_code' => $request->get('origin')
				],
				[
					'process_code' => '2', 
					'serial_number' => $request->get('sn'),
					'origin_group_code' => $request->get('origin'),
					'model' => $model,
					'quantity' => 1,
					'created_by' => $id,
					'created_at' => date('Y-m-d H:i:s')
				]
			);

			$inventory = StampInventory::where('process_code', '=', $request->get('code'))
			->where('origin_group_code','=' ,$request->get('origin'))
			->where('serial_number','=' ,$request->get('sn'))
			->first();

			$inventory2 = StampInventory::where('process_code', '=', '2')
			->where('origin_group_code','=' ,$request->get('origin'))
			->where('serial_number','=' ,$request->get('sn'))
			->first();

			if ($inventory != null) {
				$inventory->status = null;
				$inventory->process_code = '2';
				$inventory->save();
			} elseif ($inventory2 != null) {
				$inventory2->status = null;
				$inventory2->process_code = '2';
				$inventory2->save();
			}

			

			
			$log_process->save();

			$printer_name = 'Barcode Printer Sax';

			$connector = new WindowsPrintConnector($printer_name);
			$printer = new Printer($connector);

			$printer->setJustification(Printer::JUSTIFY_CENTER);
			$printer->setBarcodeWidth(2);
			$printer->setBarcodeHeight(64);
			$printer->barcode($request->get('sn'), Printer::BARCODE_CODE39);
				// $printer->qrCode($request->get('sn'));
			$printer->setTextSize(3, 1);
			$printer->text($request->get('sn')."\n");
			$printer->feed(1);
			$printer->text($model."\n");
			$printer->setTextSize(1, 1);
			$printer->text(date("d-M-Y H:i:s")."\n");
			$printer->cut();
			$printer->close();

		}else{
			$invent = new StampInventory([
				'process_code' => '2', 
				'serial_number' => $request->get('sn'),
				'origin_group_code' => $request->get('origin'),
				'model' => $request->get('snmodel'),
				'quantity' => 1,
				'created_by' => $id,
				'created_at' => date('Y-m-d H:i:s')
			]);

			$log = new LogProcess([
				'process_code' => '2', 
				'serial_number' => $request->get('sn'),
				'origin_group_code' => $request->get('origin'),
				'model' => $request->get('snmodel'),
				'quantity' => 1,
				'created_by' => $id,
				'created_at' => date('Y-m-d H:i:s')
			]);
			$invent->save();
			$log->save();

			$printer_name = 'Barcode Printer Sax';

			$connector = new WindowsPrintConnector($printer_name);
			$printer = new Printer($connector);

			$printer->setJustification(Printer::JUSTIFY_CENTER);
			$printer->setBarcodeWidth(2);
			$printer->setBarcodeHeight(64);
			$printer->barcode($request->get('sn'), Printer::BARCODE_CODE39);
				// $printer->qrCode($request->get('sn'));
			$printer->setTextSize(3, 1);
			$printer->text($request->get('sn')."\n");
			$printer->feed(1);
			$printer->text($request->get('snmodel')."\n");
			$printer->setTextSize(1, 1);
			$printer->text(date("d-M-Y H:i:s")."\n");
			$printer->cut();
			$printer->close();
		}


		$response = array(
			'status' => true,
			'message' => 'Print success',
		);
		return Response::json($response);
	}
	catch (QueryException $e){
		$response = array(
			'status' => false,
			'message' => $e->getMessage(),
		);
		return Response::json($response);
	}
}

public function fetchStampPlansax2($id){

	$id_all = $id."%";

	$now = date('Y-m-d');	

	$query3 = "select model, sum(plan) as plan, sum(actual) as actual from
	(
	select model, quantity as plan, 0 as actual from stamp_schedules where due_date = '" . $now . "'

	union all

	select model, 0 as plan, quantity as actual from log_processes where process_code = '2' and date(created_at) = '" . $now . "'
	) as plan
	group by model
	having model like '".$id_all."'";

	$planData = DB::select($query3);

	if ($id =="YAS") {
		$materials = DB::table('materials')->where('model', 'like', 'YAS%')->where('issue_storage_location', '=', 'sx21')->where('hpl', '=', 'ASBODY')
		->where('category', '=', 'wip')->select('model')->distinct()->get();
	}

	if ($id =="YTS") {
		$materials = DB::table('materials')->where('model', 'like', 'YTS%')->where('issue_storage_location', '=', 'sx21')->where('hpl', '=', 'TSBODY')
		->where('category', '=', 'wip')->select('model')->distinct()->get();
	}

	$response = array(
		'status' => true,
		'planData' => $planData,
		'model' => $materials,

	);
	return Response::json($response);
}

public function reprint_stamp2(Request $request)
{
	$model = db::table('stamp_inventories')	
	->where('serial_number', '=', $request->get('stamp_number_reprint'))
	->select ('model')
	->first();

	if ($request->get('stamp_number_reprint') != null){
		try {
			$code_generator = CodeGenerator::where('note', '=', '043')->first();
			$code_generator->index = $code_generator->index+1;
			$code_generator->save();

			$printer_name = 'Barcode Printer Sax';

			$connector = new WindowsPrintConnector($printer_name);
			$printer = new Printer($connector);

			$printer->setJustification(Printer::JUSTIFY_CENTER);
			$printer->setBarcodeWidth(2);
			$printer->setBarcodeHeight(64);
			$printer->barcode($request->get('stamp_number_reprint'), Printer::BARCODE_CODE39);
			// $printer->qrCode($request->get('serialNumber'));
			$printer->setTextSize(3, 1);
			$printer->text($request->get('stamp_number_reprint')."\n");
			$printer->feed(1);
			$printer->text($model->model."\n");
			$printer->setTextSize(1, 1);
			$printer->text(date("d-M-Y H:i:s")."\n");
			$printer->cut();
			$printer->close();

			return back()->with('status', 'Stamp has been reprinted.')->with('page', 'Assembly Process');
		}
		catch(\Exception $e){
			return back()->with("error", "Couldn't print to this printer " . $e->getMessage() . "\n");
		}
	}
	else{
		return back()->with('error', 'Serial number '. $request->get('stamp_number_reprint') . ' not found.');
	}
}


// end print saxophone

// print saxophone label

public function indexProcessAssyFLSaxTCheck(){
	$model2 = StampInventory::where('origin_group_code','=','043')->orderBy('created_at', 'desc')
	->get();
	return view('processes.assy_fl_saxT.print2',array(
		'model2' => $model2,
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function indexProcessCheckTransaction(){
	return view('processes.assy_fl_saxT.check_transaction')
	->with('page', 'Check Transaction')
	->with('head', 'Assembly Process');
}

public function fetchTodayTransaction(){

	$today = date('Y-m-d');
	$now = date('Y-m-d H:i:s');

	$data = DB::SELECT("SELECT material_number, material_description, SUM(1) AS total, SUM(IF(checked_at IS NULL, 1, 0)) AS uncheck, SUM(IF(checked_at IS NULL, 0, 1)) AS `check` FROM `kitto_transaction_checks`
		WHERE DATE(created_at) = '".$today."'
		GROUP BY material_number, material_description
		ORDER BY uncheck DESC");

	$response = array(
		'status' => true,
		'now' => $now,
		'data' => $data
	);
	return Response::json($response);
	
}

public function fetchHistoryTransaction(Request $request) {
	$datefrom = $request->get('datefrom');
	$dateto = $request->get('dateto');

	if(strlen($datefrom) <= 0){
		$datefrom = date('Y-m-01');
	}

	if(strlen($dateto) <= 0){
		$dateto = date('Y-m-d');
	}

	$data = DB::SELECT("SELECT created_at, material_number, material_description, IF(checked_at IS NULL, 'UNCHECK', 'CHECKED') AS `status`, checked_at, remark FROM `kitto_transaction_checks`
		WHERE DATE(created_at) BETWEEN '".$datefrom."' AND '".$dateto."'
		ORDER BY created_at ASC");

	$response = array(
		'status' => true,
		'data' => $data
	);
	return Response::json($response);

}

public function fetchCheckTransaction(){

	$now = date('Y-m-d H:i:s');

	$data = DB::SELECT("SELECT material_number, material_description, SUM(IF(checked_at IS NULL, 1, 0)) AS uncheck FROM `kitto_transaction_checks`
		WHERE checked_at IS NULL
		GROUP BY material_number, material_description
		ORDER BY uncheck DESC");

	$response = array(
		'status' => true,
		'now' => $now,
		'data' => $data
	);
	return Response::json($response);
	
}

public function scanCheckTransaction(Request $request) {

	$stamp = StampInventory::where('serial_number', $request->get('serial_number'))
	->where('origin_group_code', '043')
	->first();

	if(!$stamp){
		$response = array(
			'status' => false,
			'message' => 'Stamp record not found'
		);
		return Response::json($response);
	}

	$update = KittoTransactionCheck::where('material_number', $request->get('material_number'))
	->whereNull('checked_at')
	->orderBy('created_at', 'ASC')
	->first();

	try {
		$update->checked_at = date('Y-m-d H:i:s');
		$update->remark = $request->get('serial_number');
		$update->save();

		$response = array(
			'status' => true,
			'message' => 'Transaction has been verified'
		);
		return Response::json($response);

	} catch (Exception $e) {
		$response = array(
			'status' => false,
			'message' => $e->getMessage()
		);
		return Response::json($response);
	}
}



public function indexProcessAssyFLSaxT2(){
	$model2 = StampInventory::where('origin_group_code','=','043')->orderBy('created_at', 'desc')
	->get();
	return view('processes.assy_fl_saxT.print',array(
		'model2' => $model2,
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function indexProcessAssyFLSaxT3(){
	$model2 = StampInventory::where('origin_group_code','=','043')->orderBy('created_at', 'desc')
	->get();
	return view('processes.assy_fl_saxT.print_label',array(
		'model2' => $model2,
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function fetchStampPlansax3($id){
	
	$id_all = $id."%";

	$now = date('Y-m-d');	

	// $query3 = "select model, sum(plan) as plan, sum(actual) as actual from
	// (
	// select model, quantity as plan, 0 as actual from stamp_schedules where due_date = '" . $now . "'

	// union all

	// select model, 0 as plan, quantity as actual from log_processes where process_code = '3' and date(created_at) = '" . $now . "'
	// ) as plan
	// group by model
	// having model like '".$id_all."'";

	$query3 ="select model, COUNT(model) as actual from stamp_inventories where process_code='3' and origin_group_code='043' and model like '".$id_all."' and DATE_FORMAT(updated_at,'%Y-%m-%d') ='" . $now . "' GROUP BY model";

	$query4 ="SELECT COUNT(quantity) as actual,  b.model from (
	SELECT serial_number from log_processes WHERE origin_group_code='043' and model like '".$id_all."' and DATE_FORMAT(created_at,'%Y-%m-%d')  ='" . $now . "' and process_code='3' ) a
	LEFT JOIN (
	SELECT model,serial_number,quantity from stamp_inventories WHERE model like '".$id_all."' and DATE_FORMAT(updated_at,'%Y-%m-%d')  ='" . $now . "' and process_code='3') b on a.serial_number = b.serial_number GROUP BY b.model";

	$query5 ="select model, COUNT(model) as actual from log_processes where process_code='4' and origin_group_code='043' and model like '".$id_all."' and DATE_FORMAT(created_at,'%Y-%m-%d') ='" . $now . "' GROUP BY model";

	$planData = DB::select($query5);

	$response = array(
		'status' => true,
		'planData' => $planData,

	);
	return Response::json($response);
}

public function getModel(Request $request)
{
	if ($request->get('log')==3) {
		$query ="select material_number,material_description,remark from materials
		LEFT JOIN stamp_hierarchies on materials.material_number = stamp_hierarchies.finished
		WHERE stamp_hierarchies.model in ( SELECT model from log_processes WHERE serial_number='".$request->get('sn')."' and origin_group_code='043')
		";
	}else{
		$query ="select material_number,material_description,remark from materials
		LEFT JOIN stamp_hierarchies on materials.material_number = stamp_hierarchies.finished
		WHERE stamp_hierarchies.model in ( SELECT model from stamp_inventories WHERE serial_number='".$request->get('sn')."' and origin_group_code='043')
		";	
	}

	$planData = DB::select($query);

	$response = array(
		'status' => true,
		'planData' => $planData,

	);
	return Response::json($response);
}

// public function getsnsax2(Request $request)
// {
// 	$sn = StampInventory::where('process_code', '=', $request->get('code'))
// 	->where('origin_group_code','=' ,$request->get('origin'))
// 	->where('serial_number','=' ,$request->get('sn2'))
// 	->select('model', 'serial_number')
// 	->first();

// 	$sn2 = StampInventory::where('process_code', '=', '3')
// 	->where('origin_group_code','=' ,$request->get('origin'))
// 	->where('serial_number','=' ,$request->get('sn2'))
// 	->select('model', 'serial_number')
// 	->first();


// 	if ($sn != null) {
// 		$response = array(
// 			'status' => true,
// 			'message' => '1',
// 			'model' => $sn->model,
// 			'sn' => $sn->serial_number,
// 		);
// 		return Response::json($response);
// 	}
// 	elseif ($sn2 != null) {
// 		$response = array(
// 			'status' => true,
// 			'message' => '2',
// 			'model' => $sn2->model,
// 			'sn' => $sn2->serial_number,
// 		);
// 		return Response::json($response);
// 	}else{
// 		$response = array(
// 			'status' => false,
// 			'message' => 'Serial Number not registered',
// 		);
// 		return Response::json($response);
// 	}
// }

public function print_sax2(Request $request){
	$stamp = LogProcess::where('process_code', '=', $request->get('code'))
	->where('origin_group_code','=' ,$request->get('origin'))
	->where('serial_number','=' ,$request->get('sn'))
	->first();

	$stamp2 = LogProcess::where('process_code', '=', '3')
	->where('origin_group_code','=' ,$request->get('origin'))
	->where('serial_number','=' ,$request->get('sn'))
	->first();


	try{
		$id = Auth::id();
		if ($request->get('status') =="update") {
			if ($stamp != null) {
				$model = $stamp->model;
			}else{
				$model = $stamp2->model;
			}
			
			$log_process = LogProcess::updateOrCreate(
				[
					'process_code' => '3', 
					'serial_number' => $request->get('sn'),
					'origin_group_code' => $request->get('origin')
				],
				[
					'process_code' => '3', 
					'serial_number' => $request->get('sn'),
					'origin_group_code' => $request->get('origin'),
					'status' => $request->get('jpn'),
					'model' => $model,
					'quantity' => 1,
					'created_by' => $id,
					'created_at' => date('Y-m-d H:i:s')
				]
			);

			$log_process4 = LogProcess::updateOrCreate(
				[
					'process_code' => '4', 
					'serial_number' => $request->get('sn'),
					'origin_group_code' => $request->get('origin')
				],
				[
					'process_code' => '4', 
					'serial_number' => $request->get('sn'),
					'origin_group_code' => $request->get('origin'),
					'status' => $request->get('jpn'),
					'model' => $request->get('snmodel'),
					'quantity' => 1,
					'created_by' => $id,
					'created_at' => date('Y-m-d H:i:s')
				]
			);

			$inventory = StampInventory::where('process_code', '=', $request->get('code'))
			->where('origin_group_code','=' ,$request->get('origin'))
			->where('serial_number','=' ,$request->get('sn'))
			->first();

			$inventory2 = StampInventory::where('process_code', '=', '3')
			->where('origin_group_code','=' ,$request->get('origin'))
			->where('serial_number','=' ,$request->get('sn'))
			->first();

			if ($inventory != null) {
				$inventory->status = null;
				$inventory->process_code = '3';
				$inventory->model = $request->get('snmodel');
				$inventory->status =  $request->get('jpn');
				$inventory->save();
			} elseif ($inventory2 != null) {
				$inventory2->status = null;
				$inventory2->process_code = '3';
				$inventory2->model = $request->get('snmodel');
				$inventory->status =  $request->get('jpn');
				$inventory2->save();
			}
			
			$log_process->save();
			$log_process4->save();

		}

		$response = array(
			'status' => true,
			'message' => 'Print success',
		);
		return Response::json($response);
	}
	catch (QueryException $e){
		$response = array(
			'status' => false,
			'message' => $e->getMessage(),
		);
		return Response::json($response);
	}
}


// public function label_besar($id,$gmc,$remark){

// 	$date = date('Y-m-d');
// 	if ($remark =="J") {
// 		$query ="select stamp_inventories.serial_number,finished,janean,upc,date_code,remark,stamp_inventories.model from (
// 		select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
// 		INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
// 		WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and stamp_hierarchies.finished='".$gmc."'  and stamp_hierarchies.remark ='J'
// 		) a INNER JOIN (
// 		SELECT week_date,date_code from weekly_calendars WHERE week_date='".$date."')b
// 		on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
// 		INNER JOIN stamp_inventories on a.serial_number = stamp_inventories.serial_number";
// 	}

// 	elseif ($remark =="NJ"){
// 		$query ="select stamp_inventories.serial_number,finished,janean,upc,date_code,remark,stamp_inventories.model from (
// 		select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
// 		INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
// 		WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and stamp_hierarchies.finished='".$gmc."'  and stamp_hierarchies.remark !='J'
// 		) a INNER JOIN (
// 		SELECT week_date,date_code from weekly_calendars WHERE week_date='".$date."')b
// 		on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
// 		INNER JOIN stamp_inventories on a.serial_number = stamp_inventories.serial_number";
// 	}

// 	elseif ($remark =="JR") {
// 		$query ="select stamp_inventories.serial_number,finished,janean,upc,date_code,remark,stamp_inventories.model from (
// 		select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
// 		INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
// 		WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and stamp_hierarchies.finished='".$gmc."'  and stamp_hierarchies.remark ='J'
// 		) a INNER JOIN (
// 		SELECT week_date,date_code from weekly_calendars WHERE week_date=(select DATE_FORMAT(created_at,'%Y-%m-%d')as a  from log_processes WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."'))b
// 		on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
// 		INNER JOIN stamp_inventories on a.serial_number = stamp_inventories.serial_number";
// 	}

// 	elseif ($remark =="NJR"){
// 		$query ="select stamp_inventories.serial_number,finished,janean,upc,date_code,remark,stamp_inventories.model from (
// 		select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
// 		INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
// 		WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and stamp_hierarchies.finished='".$gmc."'  and stamp_hierarchies.remark !='J'
// 		) a INNER JOIN (
// 		SELECT week_date,date_code from weekly_calendars WHERE week_date=(select DATE_FORMAT(created_at,'%Y-%m-%d')as a  from log_processes WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."'))b
// 		on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
// 		INNER JOIN stamp_inventories on a.serial_number = stamp_inventories.serial_number";
// 	}elseif ($remark =="JRB") {
// 		$query ="select stamp_inventories.serial_number,finished,janean,upc,date_code,remark,stamp_inventories.model from (
// 		select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
// 		INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
// 		WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and stamp_hierarchies.finished='".$gmc."'  and stamp_hierarchies.remark ='J'
// 		) a INNER JOIN (
// 		SELECT week_date,date_code from weekly_calendars WHERE week_date=(select DATE_FORMAT(created_at,'%Y-%m-%d')as a  from log_processes WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."'))b
// 		on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
// 		INNER JOIN stamp_inventories on a.serial_number = stamp_inventories.serial_number";
// 	}

// 	elseif ($remark =="NJRB"){
// 		$query ="select stamp_inventories.serial_number,finished,janean,upc,date_code,remark,stamp_inventories.model from (
// 		select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
// 		INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
// 		WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."' and stamp_hierarchies.finished='".$gmc."'  and stamp_hierarchies.remark !='J'
// 		) a INNER JOIN (
// 		SELECT week_date,date_code from weekly_calendars WHERE week_date=(select DATE_FORMAT(created_at,'%Y-%m-%d')as a  from log_processes WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."'))b
// 		on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
// 		INNER JOIN stamp_inventories on a.serial_number = stamp_inventories.serial_number";
// 	}

// 	$barcode = DB::select($query);

// 	$date = date('Y-m-d');
// 	$querydate = "SELECT week_date,date_code from weekly_calendars WHERE week_date='".$date."'";
// 	$date2 = DB::select($querydate);

// 	return view('processes.assy_fl_saxT.print_label_besar',array(
// 		'barcode' => $barcode,
// 		'date2' => $date2,

// 		'remark' => $remark,
// 	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
// }

// public function label_kecil($id,$remark){
// 	$remark2 = $remark;
// 	$sn = $id;
// 	$date = date('Y-m-d');
// 	// if ($remark =="RP") {
// 	// 	$query ="SELECT a.serial_number,b.date_code from stamp_inventories as a
// 	// 	INNER JOIN (
// 	// 	SELECT week_date,date_code from weekly_calendars WHERE week_date=(select DATE_FORMAT(created_at,'%Y-%m-%d')as a  from log_processes WHERE log_processes.process_code='3' and log_processes.serial_number='".$id."'))b
// 	// 	on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
// 	// 	WHERE a.serial_number='".$id."'";
// 	// }else{
// 	// 	$query ="SELECT a.serial_number,b.date_code from stamp_inventories as a
// 	// 	INNER JOIN (
// 	// 	SELECT week_date,date_code from weekly_calendars WHERE week_date='".$date."')b
// 	// 	on DATE_FORMAT(a.created_at,'%Y-%m-%d') = b.week_date
// 	// 	WHERE a.serial_number='".$id."'";
// 	// }

// 	$query = "SELECT week_date,date_code from weekly_calendars WHERE week_date='".$date."'";
// 	$barcode = DB::select($query);

// 	return view('processes.assy_fl_saxT.print_label_kecil',array(
// 		'barcode' => $barcode,
// 		'sn' => $sn,
// 		'remark' => $remark2,
// 	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
// }

public function label_des($id){
	
	$query ="select model from log_processes where process_code ='4' and serial_number='".$id."' and origin_group_code='043'";
	$barcode = DB::select($query);
	
	return view('processes.assy_fl_saxT.print_label_description',array(
		'barcode' => $barcode,
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function editStampLabel(Request $request){
	$stamp = StampInventory::find($request->get('id'));

	$response = array(
		'status' => true,
		'stamp' => $stamp,
	);
	return Response::json($response);
}

public function updateStampLabel(Request $request){
	$stamp = StampInventory::where('serial_number',$request->get('id'))->get()->first();

	$log = LogProcess::where('serial_number',$stamp->serial_number)
	->where('process_code', '=', "4")
	->get()->first();	

	$stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $stamp->serial_number)
	->where('stamp_inventories.model', '=', $stamp->model)
	->where('stamp_inventories.origin_group_code', '=', $request->get('originGroupCode'));

	

	$stamp_inventory->update([
		'status' => $request->get('jpn'),
		'model' => $request->get('model')]);

	$log->update([
		'model' => $request->get('model')]);

	$response = array(
		'status' => true,
		'message' => 'Update Success',
		'log' => $log,
	);
	return Response::json($response);
}

public function getModelReprintAll(Request $request)
{
	$query ="SELECT material_number,serial_number,status from materials 
	LEFT JOIN stamp_inventories 
	on materials.material_description = stamp_inventories.model
	where serial_number ='".$request->get('sn')."'";

	$reprint = DB::select($query);
	$response = array(
		'status' => true,
		'reprint' => $reprint,
	);
	return Response::json($response);
}

// print saxophone label

public function filter_stamp_detail_cl(Request $request){
	$flo_detailsTable = DB::table('log_processes')
	
	->select('log_processes.serial_number', 'log_processes.model', 'log_processes.quantity','log_processes.process_code', db::raw('date_format(log_processes.created_at, "%d-%b-%Y") as st_date') );

	if(strlen($request->get('datefrom')) > 0){
		$date_from = date('Y-m-d', strtotime($request->get('datefrom')));
		$flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(log_processes.created_at, "%Y-%m-%d")'), '>=', $date_from);
	}


	if(strlen($request->get('dateto')) > 0){
		$date_to = date('Y-m-d', strtotime($request->get('dateto')));
		$flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(log_processes.created_at, "%Y-%m-%d")'), '<=', $date_to);
	}

	$stamp_detail = $flo_detailsTable->orderBy('log_processes.created_at', 'desc')->where('origin_group_code','=','042')->get();

	return DataTables::of($stamp_detail)
	->addColumn('action', function($stamp_detail){
		return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteConfirmation(id)" id="' . $stamp_detail->serial_number . '"><i class="glyphicon glyphicon-trash"></i></a>';
	})
	->make(true);
}



public function indexResumesSX(){

	$code = Process::where('remark','=','043')->orderBy('process_code', 'asc')
	->get();


	return view('processes.assy_fl_saxT.resumes',array(
		'code' => $code,
	))->with('page', 'Process Assy CL')->with('head', 'Assembly Process');
}

public function indexResumes(){

	$code = Process::where('remark','=','YFL041')->orderBy('process_code', 'asc')
	->get();
	return view('processes.assy_fl.resumes',array(
		'code' => $code,
	))

	->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function filter_stamp_detail_sx(Request $request){
	$flo_detailsTable = DB::table('log_processes')
	
	->select('log_processes.serial_number', 'log_processes.model', 'log_processes.quantity','log_processes.process_code', db::raw('date_format(log_processes.created_at, "%d-%b-%Y") as st_date') );

	if(strlen($request->get('datefrom')) > 0){
		$date_from = date('Y-m-d', strtotime($request->get('datefrom')));
		$flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(log_processes.created_at, "%Y-%m-%d")'), '>=', $date_from);
	}

	if(strlen($request->get('code')) > 0){
		$code = $request->get('code');
		$flo_detailsTable = $flo_detailsTable->where('log_processes.process_code','=', $code );
	}

	if(strlen($request->get('dateto')) > 0){
		$date_to = date('Y-m-d', strtotime($request->get('dateto')));
		$flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(log_processes.created_at, "%Y-%m-%d")'), '<=', $date_to);
	}

	$stamp_detail = $flo_detailsTable->orderBy('log_processes.created_at', 'desc')->where('origin_group_code','=','043')->get();

	return DataTables::of($stamp_detail)
	->addColumn('action', function($stamp_detail){
		return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteConfirmation(id)" id="' . $stamp_detail->serial_number . '"><i class="glyphicon glyphicon-trash"></i></a>';
	})
	->make(true);
}

public function fetch_plan_labelsax($id){

	$id_all = $id."%";
	
	$hpl = "where materials.category = 'FG' and materials.origin_group_code = '043'";		
	$first = date('Y-m-01');
	if(date('Y-m-d') != date('Y-m-01')){
		$last = date('Y-m-d', strtotime(Carbon::yesterday()));
	}
	else{
		$last = date('Y-m-d');
	}
	$now = date('Y-m-d');

	if($first != $now){
		$debt = "union all

		select material_number, sum(debt) as debt, 0 as plan, 0 as actual from
		(
		select material_number, -(sum(quantity)) as debt from production_schedules where due_date >= '". $first ."' and due_date <= '". $last ."' group by material_number

		union all

		select material_number, sum(quantity) as debt from flo_details where date(created_at) >= '". $first ."' and date(created_at) <= '". $last ."' group by material_number
		) as debt
		group by material_number";
	}
	else{
		$debt= "";
	}
	

	$query = " SELECT a.model,a.debt,a.plan, COALESCE(b.act1,0) as act  from (

	select a.model, a.debt,a.plan,COALESCE(b.act,0) as actual  from (
	select result.material_number, materials.material_description as model, sum(result.debt) as debt, sum(result.plan) as plan, sum(result.actual) as actual from
	(
	select material_number, 0 as debt, sum(quantity) as plan, 0 as actual 
	from production_schedules 
	where due_date = '". $now ."' 
	group by material_number

	union all

	select material_number, 0 as debt, 0 as plan, sum(quantity) as actual 
	from flo_details 
	where date(created_at) = '". $now ."'  
	group by material_number

	".$debt."

	) as result
	left join materials on materials.material_number = result.material_number
	". $hpl ."
	group by result.material_number, materials.material_description
	having sum(result.debt) <> 0 or sum(result.plan) <> 0 or sum(result.actual) <> 0 ) a
	
	LEFT JOIN (
	select model, count(MODEL)AS act from stamp_inventories where process_code='3' AND origin_group_code='043' and  date(updated_at) = '". $now ."' GROUP BY model) b
	on a.model = b.model 
	)
	as a
	LEFT JOIN (
	SELECT COUNT(quantity) as act1, model from stamp_inventories where process_code='3' and origin_group_code='043' and DATE_FORMAT(updated_at,'%Y-%m-%d') = '". $now ."' and model like '".$id_all."'  GROUP BY model
	) b on a.model = b.model where a.model like '".$id_all."' ORDER BY a.model asc

	";

	$tableData = DB::select($query);


	$response = array(
		'status' => true,
		'tableData' => $tableData,
		
	);
	return Response::json($response);
}
	//end tambah ali

// print Flute

public function indexLabelFL(){
	return view('processes.assy_fl.print_label')->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function fetchResultFL5(){
	$now = date('Y-m-d');
	$log_processes = db::table('log_processes')
	->where('process_code', '=', '6')
	->where('model', 'like', 'YFL%')
	->where(db::raw('date(created_at)'), '=', $now)
	->orderBy('created_at', 'desc')
	->get();

	$response = array(
		'status' => true,
		'resultData' => $log_processes,
	);
	return Response::json($response);
}

public function getModelfl(Request $request)
{
	if ($request->get('log')==4) {
		// $query ="select material_number,material_description,remark from materials
		// LEFT JOIN stamp_hierarchies on materials.material_number = stamp_hierarchies.finished
		// WHERE stamp_hierarchies.model in ( SELECT model from log_processes WHERE serial_number='".$request->get('sn')."' )
		// ";

		$query = "SELECT
		material_number,
		material_description,
		remark 
		FROM
		materials
		LEFT JOIN stamp_hierarchies ON materials.material_number = stamp_hierarchies.finished 
		WHERE
		stamp_hierarchies.model IN (
		SELECT model FROM log_processes 
		WHERE origin_group_code = '041'
		and	serial_number = '".$request->get('sn')."')";
		
	}else{
		$query ="select material_number,material_description,remark from materials
		LEFT JOIN stamp_hierarchies on materials.material_number = stamp_hierarchies.finished
		WHERE stamp_hierarchies.model in ( SELECT model from stamp_inventories WHERE serial_number='".$request->get('sn')."' )
		";	
	}

	$planData = DB::select($query);

	$response = array(
		'status' => true,
		'planData' => $planData,

	);
	return Response::json($response);
}

public function getsnsaxfl(Request $request)
{
	$sn = LogProcess::where('process_code', '=', $request->get('code'))
	->where('origin_group_code','=' ,$request->get('origin'))
	->where('serial_number','=' ,$request->get('sn2'))
	->select('model', 'serial_number')
	->first();

	$sn2 = LogProcess::where('origin_group_code','=' ,$request->get('origin'))
	->where('serial_number','=' ,$request->get('sn2'))
	->where('process_code', '=', '6')
	->select('model', 'serial_number')
	->first();


	if ($sn != null && $sn2 == null) {
		//Print
		$response = array(
			'status' => true,
			'message' => '1',
			'model' => $sn->model,
			'sn' => $sn->serial_number,
		);
		return Response::json($response);
	}
	elseif ($sn2 != null && $sn != null) {
		//Reprint
		$response = array(
			'status' => true,
			'message' => '2',
			'model' => $sn2->model,
			'sn' => $sn2->serial_number,
		);
		return Response::json($response);
	}else{
		$response = array(
			'status' => false,
			'message' => 'Serial Number not registered',
		);
		return Response::json($response);
	}
}

public function print_FL(Request $request){

	try{
		$id = Auth::id();
		if ($request->get('status') =="update") {

			
			$log_process = LogProcess::updateOrCreate(
				[
					'process_code' => '6', 
					'serial_number' => $request->get('sn'),
					'origin_group_code' => $request->get('origin')
				],
				[
					'process_code' => '6', 
					'serial_number' => $request->get('sn'),
					'origin_group_code' => $request->get('origin'),
					'status' => $request->get('jpn'),
					'model' => $request->get('snmodel'),
					'quantity' => 1,
					'created_by' => $id,
					'created_at' => date('Y-m-d H:i:s')
				]
			);

			$log_process->save();		

		}

		$response = array(
			'status' => true,
			'message' => 'Print success',
		);
		return Response::json($response);
	}
	catch (QueryException $e){
		$response = array(
			'status' => false,
			'message' => $e->getMessage(),
		);
		return Response::json($response);
	}
}

public function getModelReprintAllFL(Request $request){
	$query ="SELECT material_number,serial_number from materials 
	LEFT JOIN log_processes 
	on materials.material_description = log_processes.model
	where serial_number ='".$request->get('sn')."' and process_code='6' and log_processes.origin_group_code='041'";

	$reprint = DB::select($query);
	$response = array(
		'status' => true,
		'reprint' => $reprint,
	);
	return Response::json($response);
}

public function fetchCheckCarb(Request $request){
	$model = db::select("SELECT * FROM log_processes
		WHERE process_code = '6'
		AND origin_group_code = '041'
		AND serial_number = '".$request->get('sn')."'");

	$response = array(
		'status' => true,
		'model' => $model
	);
	return Response::json($response);
}

public function label_carb_fl($id){

	// $date = db::select("SELECT DATE_FORMAT( created_at, '%m-%Y' ) AS tgl FROM log_processes 
	// 	WHERE process_code = '6'
	// 	AND origin_group_code = '041'
	// 	AND serial_number = '".$id."'");

	$date = db::select("SELECT DATE_FORMAT( created_at, '%m-%Y' ) AS tgl FROM assembly_logs 
		WHERE location = 'packing'
		AND origin_group_code = '041'
		AND serial_number = '".$id."'");

	return view('processes.assembly.flute.label.label_carb_new',array(
		'date' => $date,
		'sn' => $id,
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function label_carb_fl2($id){

	// $date = db::select("SELECT DATE_FORMAT( created_at, '%m-%Y' ) AS tgl FROM log_processes 
	// 	WHERE process_code = '6'
	// 	AND origin_group_code = '041'
	// 	AND serial_number = '".$id."'");

	$date = db::select("SELECT DATE_FORMAT( created_at, '%m-%Y' ) AS tgl FROM assembly_logs 
		WHERE location = 'packing'
		AND origin_group_code = '041'
		AND serial_number = '".$id."'");

	if (str_contains($id,'ZE')) {
		$date = db::select("SELECT DATE_FORMAT( week_date, '%m-%Y' ) AS tgl FROM weekly_calendars where week_date = '".date('Y-m-d')."'");
	}

	return view('processes.assembly.flute.label.label_carb_new2',array(
		'date' => $date,
		'sn' => $id
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}


public function label_besar_outer_fl($id,$gmc,$remark){

	$date = date('Y-m-d');
	if ($remark =="P") {
		$query ="SELECT serial_number,finished,janean,upc, remark,c.model_2 as model FROM (
		select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
		INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
		WHERE log_processes.process_code='4' and log_processes.serial_number='".$id."' and log_processes.origin_group_code='041' and stamp_hierarchies.finished='".$gmc."') a		
		LEFT JOIN
		(SELECT model as model_2, serial_number as sn2 from log_processes WHERE serial_number='".$id."' and process_code='6') c on a.serial_number = c.sn2";
	}elseif ($remark =="RP"){
		$query ="SELECT serial_number,finished,janean,upc, remark,c.model_2 as model FROM (
		select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
		INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
		WHERE log_processes.process_code='4' and log_processes.serial_number='".$id."' and log_processes.origin_group_code='041' and stamp_hierarchies.finished='".$gmc."') a		
		LEFT JOIN 
		(SELECT model as model_2, serial_number as sn2 from log_processes WHERE serial_number='".$id."' and process_code='6') c on a.serial_number = c.sn2";
	}

	$barcode = DB::select($query);

	// $date = date('Y-m-d');
	$querydate = "SELECT week_date,date_code from weekly_calendars
	WHERE week_date = (SELECT DATE_FORMAT(created_at,'%Y-%m-%d') from log_processes
	WHERE serial_number='".$id."'
	and process_code='6'
	and origin_group_code='041')";

	$date = DB::select($querydate);

	return view('processes.assy_fl.label_temp.label_besar_outer',array(
		'barcode' => $barcode,
		'date' => $date,
		'remark' => $remark,
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}


public function label_besar_fl($id,$gmc,$remark){

	$date = date('Y-m-d');
	if ($remark =="P") {
		$query ="SELECT serial_number,finished,janean,upc, remark,c.model_2 as model FROM (
		select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
		INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
		WHERE log_processes.process_code='4' and log_processes.serial_number='".$id."' and log_processes.origin_group_code='041' and stamp_hierarchies.finished='".$gmc."') a		
		LEFT JOIN
		(SELECT model as model_2, serial_number as sn2 from log_processes WHERE serial_number='".$id."' and process_code='6') c on a.serial_number = c.sn2";
	}elseif ($remark =="RP"){
		$query ="SELECT serial_number,finished,janean,upc, remark,c.model_2 as model FROM (
		select log_processes.serial_number,stamp_hierarchies.model,stamp_hierarchies.finished,stamp_hierarchies.janean,stamp_hierarchies.upc,stamp_hierarchies.remark,log_processes.created_at  from log_processes 
		INNER JOIN stamp_hierarchies on log_processes.model = stamp_hierarchies.model
		WHERE log_processes.process_code='4' and log_processes.serial_number='".$id."' and log_processes.origin_group_code='041' and stamp_hierarchies.finished='".$gmc."') a		
		LEFT JOIN 
		(SELECT model as model_2, serial_number as sn2 from log_processes WHERE serial_number='".$id."' and process_code='6') c on a.serial_number = c.sn2";
	}

	$barcode = DB::select($query);

	$querydate = "SELECT week_date, date_code from weekly_calendars
	WHERE week_date = (SELECT DATE_FORMAT(created_at,'%Y-%m-%d') from log_processes
	WHERE serial_number='".$id."'
	and process_code='6'
	and origin_group_code='041')";
	$date = DB::select($querydate);

	return view('processes.assy_fl.label_temp.label_besar',array(
		'barcode' => $barcode,
		'date' => $date,
		'remark' => $remark,
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function label_kecil_fl($id,$remark){
	$remark2 = $remark;
	$sn = $id;
	$date = date('Y-m-d');
	
	$query = "SELECT week_date, date_code FROM weekly_calendars
	WHERE week_date = (SELECT DATE_FORMAT(created_at,'%Y-%m-%d') FROM log_processes
	WHERE serial_number = '".$sn."'
	AND process_code = '6'
	AND origin_group_code = '041')";
	$barcode = DB::select($query);


	$query2="SELECT serial_number, model FROM log_processes
	WHERE serial_number = '".$sn."'
	AND process_code = '6'
	AND origin_group_code = '041'";
	$des = DB::select($query2);


	return view('processes.assy_fl.label_temp.label_kecil',array(
		'barcode' => $barcode,
		'sn' => $sn,
		'remark' => $remark2,
		'des' => $des,
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function label_kecil2_fl($id,$remark){
	$remark2 = $remark;
	$sn = $id;
	$date = date('Y-m-d');
	
	$query = "SELECT week_date, date_code FROM weekly_calendars
	WHERE week_date = (SELECT DATE_FORMAT(created_at,'%Y-%m-%d') FROM log_processes
	WHERE serial_number = '".$sn."'
	AND process_code = '6'
	AND origin_group_code = '041')";
	$barcode = DB::select($query);


	$query2="SELECT serial_number, model FROM log_processes
	WHERE serial_number = '".$sn."'
	AND process_code = '6'
	AND origin_group_code = '041'";
	$des = DB::select($query2);


	return view('processes.assy_fl.label_temp.label_kecil2',array(
		'barcode' => $barcode,
		'sn' => $sn,
		'remark' => $remark2,
		'des' => $des,
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function label_des_fl($id,$remark){
	
	$query ="select model from log_processes where process_code ='6' and serial_number='".$id."'";
	$barcode = DB::select($query);
	
	// return view('processes.assembly.flute.label.label_desc',array(
	return view('processes.assy_fl.label_temp.label_desc',array(
		'barcode' => $barcode,
		'sn' => $id,
		'remark' => $remark,
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function editStampLabelFL(Request $request){
	$stamp = logProcess::find($request->get('id'));

	$response = array(
		'status' => true,
		'stamp' => $stamp,
	);
	return Response::json($response);
}

public function updateStampLabelFL(Request $request){
	$stamp = logProcess::where('serial_number',$request->get('id'))->get()->first();

	$log = LogProcess::where('serial_number',$stamp->serial_number)
	->where('process_code', '=', "6")
	->get()->first();	
	
	$log->update([
		'model' => $request->get('model')]);

	$response = array(
		'status' => true,
		'message' => 'Update Success',
		'log' => $log,
	);
	return Response::json($response);
}

public function fetchStampPlanFL5(Request $request){
	
	$now = date('Y-m-d');	

	$query5 ="select model, COUNT(model) as actual from log_processes where process_code='6' and origin_group_code='041' and model like 'YFL%' and DATE_FORMAT(created_at,'%Y-%m-%d') ='" . $now . "' GROUP BY model";

	$planData = DB::select($query5);

	$response = array(
		'status' => true,
		'planData' => $planData,

	);
	return Response::json($response);
}

// end print label fl

// email double serial sax

public function mailSax($sn, $model, $id,$log){
	$mail_to = db::table('send_emails')
	->where('remark', '=', 'ali')
	->select('email')
	->get();

	$query = "SELECT a.*, users.`name` as user3, '".$log."' as log from (
	SELECT serial_number, model, log_processes.updated_at, NOW() as input, '".$model."' as model2, users.`name` as user1 , '".$id."' as user2
	from log_processes 
	LEFT JOIN users on log_processes.created_by = users.id
	WHERE serial_number='".$sn."' and process_code='".$log."'
	) a
	LEFT JOIN users on a.user2 = users.id";

	$datas = db::select($query);

	if($datas != null){
		Mail::to('anton.budi.santoso@music.yamaha.com')->send(new SendEmail($datas, 'duobleserialnumber'));
	}
}

//end email


// start Ambil Gambar Checksheet Sax
public function indexProcessAssyFLSaxT4($model, $sn){
	
	return view('processes.assy_fl_saxT.print_check_sheet',array(
		'model' => $model,
		'sn' => $sn,
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}
public function fetchImageSax(Request $request)
{

	$model = $request->get('modelp');
	$img = ImageSax::where('model', '=', $model)			
	->first();
	$response = array(
		'status' => true,
		'img' => $img,

	);
	return Response::json($response);
}
// end Ambil Gambar Checksheet Sax 

// return sax

public function indexRepairSx(){
	return view('processes.assy_fl_saxT.return')->with('page', 'Process Assy FL')->with('head', 'Assembly Process');

}

public function returnfgStamp(Request $request){
	$stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $request->get('id'))
	->where('stamp_inventories.origin_group_code', '=', $request->get('originGroupCode'));

	$stamp_inventory->update(['status' => 'return']);

	$id = Auth::id();
	$err = new ErrorLog([
		'error_message' => 'Return - '.$request->get('id').'-'.$request->get('model').'-'.$request->get('originGroupCode'), 
		'created_by' => $id,
	]);

	$err->save();

	$response = array(
		'status' => true,
		'message' => 'Return success',
	);
	return Response::json($response);
}

public function fetchReturnTableSx(){
	$stamp_inventories = StampInventory::where('origin_group_code', '=', '043')
	->where('status', '=', 'return')
	->orderBy('updated_at', 'desc')
	->get();

	return DataTables::of($stamp_inventories)
	->make(true);
}

public function scanSerialNumberReturnSx(Request $request){
	$stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $request->get('serialNumber'))
	->where('stamp_inventories.origin_group_code', '=', $request->get('originGroupCode'));

	$stamp_inventory->update(['status' => 'return']);

	$id = Auth::id();
	$err = new ErrorLog([
		'error_message' => 'Return - '.$request->get('serialNumber').' - '.$request->get('originGroupCode'), 
		'created_by' => $id,
	]);

	$err->save();

	$response = array(
		'status' => true,
		'message' => 'Return success',
	);
	return Response::json($response);
}


// end return sax


// return cl

public function indexRepairCl(){
	return view('processes.assy_fl_cla.return')->with('page', 'Process Assy FL')->with('head', 'Assembly Process');

}

public function returnClStamp(Request $request){
	$stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $request->get('id'))
	->where('stamp_inventories.origin_group_code', '=', $request->get('originGroupCode'));

	$stamp_inventory->update(['status' => 'return']);

	$id = Auth::id();
	$err = new ErrorLog([
		'error_message' => 'Return - '.$request->get('id').'-'.$request->get('model').'-'.$request->get('originGroupCode'), 
		'created_by' => $id,
	]);

	$err->save();

	$response = array(
		'status' => true,
		'message' => 'Return success',
	);
	return Response::json($response);
}

public function fetchReturnTableCl(){
	$stamp_inventories = StampInventory::where('origin_group_code', '=', '042')
	->where('status', '=', 'return')
	->orderBy('updated_at', 'desc')
	->get();

	return DataTables::of($stamp_inventories)
	->make(true);
}

public function scanSerialNumberReturnCl(Request $request){
	$stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $request->get('serialNumber'))
	->where('stamp_inventories.origin_group_code', '=', $request->get('originGroupCode'));

	$stamp_inventory->update(['status' => 'return']);

	$id = Auth::id();
	$err = new ErrorLog([
		'error_message' => 'Return - '.$request->get('serialNumber').' - '.$request->get('originGroupCode'), 
		'created_by' => $id,
	]);

	$err->save();

	$response = array(
		'status' => true,
		'message' => 'Return success',
	);
	return Response::json($response);
}


// end return cl


// ng sax

public function indexngSx(){
	return view('processes.assy_fl_saxT.ng')->with('page', 'Process Assy FL')->with('head', 'Assembly Process');

}

public function ngsxStamp(Request $request){
	$stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $request->get('id'))
	->where('stamp_inventories.origin_group_code', '=', $request->get('originGroupCode'));

	$stamp_inventory->update(['status' => 'NG']);

	$id = Auth::id();
	$err = new ErrorLog([
		'error_message' => 'NG - '.$request->get('id').'-'.$request->get('model').'-'.$request->get('originGroupCode'), 
		'created_by' => $id,
	]);

	$err->save();

	$response = array(
		'status' => true,
		'message' => 'Return success',
	);
	return Response::json($response);
}

public function fetchngTableSx(){
	$stamp_inventories = StampInventory::where('origin_group_code', '=', '043')
	->where('status', '=', 'NG')
	->orderBy('updated_at', 'desc')
	->get();

	return DataTables::of($stamp_inventories)
	->make(true);
}

public function scanSerialNumberngSx(Request $request){
	$stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $request->get('serialNumber'))
	->where('stamp_inventories.origin_group_code', '=', $request->get('originGroupCode'));

	$stamp_inventory->update(['status' => 'NG']);

	$id = Auth::id();
	$err = new ErrorLog([
		'error_message' => 'NG - '.$request->get('serialNumber').' - '.$request->get('originGroupCode'), 
		'created_by' => $id,
	]);

	$err->save();

	$response = array(
		'status' => true,
		'message' => 'Return success',
	);
	return Response::json($response);
}


// end ng sax


// ng FL

public function indexngFL(){
	return view('processes.assy_fl.ng')->with('page', 'Process Assy FL')->with('head', 'Assembly Process');

}

public function ngFLStamp(Request $request){
	$stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $request->get('id'))
	->where('stamp_inventories.origin_group_code', '=', $request->get('originGroupCode'));

	$stamp_inventory->update(['status' => 'NG']);

	$id = Auth::id();
	$err = new ErrorLog([
		'error_message' => 'NG - '.$request->get('id').'-'.$request->get('model').'-'.$request->get('originGroupCode'), 
		'created_by' => $id,
	]);

	$err->save();

	$response = array(
		'status' => true,
		'message' => 'Return success',
	);
	return Response::json($response);
}

public function fetchngTableFL(){
	$stamp_inventories = StampInventory::where('origin_group_code', '=', '041')
	->where('status', '=', 'NG')
	->orderBy('updated_at', 'desc')
	->get();

	return DataTables::of($stamp_inventories)
	->make(true);
}

public function scanSerialNumberngFL(Request $request){
	$stamp_inventory = StampInventory::where('stamp_inventories.serial_number', '=', $request->get('serialNumber'))
	->where('stamp_inventories.origin_group_code', '=', $request->get('originGroupCode'));

	$stamp_inventory->update(['status' => 'NG']);

	$id = Auth::id();
	$err = new ErrorLog([
		'error_message' => 'NG - '.$request->get('serialNumber').' - '.$request->get('originGroupCode'), 
		'created_by' => $id,
	]);

	$err->save();

	$response = array(
		'status' => true,
		'message' => 'Return success',
	);
	return Response::json($response);
}


// end ng FL

	//result sax new

public function indexfetchResultSaxnew(){
	return view('processes.assy_fl_saxT.picking_schedule', array(
		'title' => 'Picking Schedule Saxophone',
		'title_jp' => '(??)',
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');

}

public function fetchResultSaxnew(Request $request)
{
	if(date('D')=='Fri' ){
		$nextday = date('Y-m-d', strtotime(carbon::now()->addDays(2)));
	}else if(date('D')=='Sat' ){
		$nextday = date('Y-m-d', strtotime(carbon::now()->addDays(2)));
	}		
	else{
		$nextday = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
	}

	$first = date('Y-m-01');
	if(date('Y-m-d') != date('Y-m-01')){
		$last = date('Y-m-d', strtotime(Carbon::yesterday()));
	}
	else{
		$last = date('Y-m-d');
	}
	$now = date('Y-m-d');

	if($first != $now){
		$debt = "union all

		select material_number, sum(debt) as debt, 0 as plan, 0 as actual from
		(
		select material_number, -(sum(quantity)) as debt from production_schedules where due_date >= '". $first ."' and due_date <= '". $last ."' group by material_number

		union all

		select material_number, sum(quantity) as debt from flo_details where date(created_at) >= '". $first ."' and date(created_at) <= '". $last ."' group by material_number
		) as debt
		group by material_number";
	}
	else{
		$debt= "";
	}


	$query = "
	select model, sum(debt) as debt, sum(plan) as plan, sum(actual) as actual, sum(wip) as wip, sum(ng) as ng, sum(plan)-sum(debt)-sum(wip)-sum(actual) as target_assy, sum(stamp) as stamp, sum(h1)/2 as h1  from
	(
	select materials.model, sum(assy.debt) as debt, sum(assy.plan) as plan, sum(assy.actual) as actual, 0 as wip, 0 as ng, 0 as stamp, 0 as h1 from
	(
	select material_number, 0 as debt, sum(quantity) as plan, 0 as actual from production_schedules where due_date = '".$now."' group by material_number

	union all

	select material_number, 0 as debt, 0 as plan, sum(quantity) as actual from flo_details where date(created_at) = '".$now."' group by material_number

	union all

	select material_number, sum(debt) as debt, 0 as plan, 0 as actual from
	(
	select material_number, -(sum(quantity)) as debt from production_schedules where due_date >= '".$first."' and due_date <= '".$last."' group by material_number

	union all

	select material_number, sum(quantity) as debt from flo_details where date(created_at) >= '".$first."' and date(created_at) <= '".$last."' group by material_number
	) as debt
	group by material_number
	) as assy left join materials on materials.material_number = assy.material_number where materials.category = 'FG' and materials.origin_group_code = '043' group by materials.model

	union all

	select model, 0 as debt, 0 as plan, 0 as actual, sum(wip) as wip, 0 as ng, 0 as stamp, 0 as h1 from
	(
	select model, 0 as debt, 0 as plan, 0 as actual, sum(quantity) as wip, 0 as ng, 0 as stamp, 0 as h1 from stamp_inventories where origin_group_code = '043' and process_code = 2 and (stamp_inventories.status is null or stamp_inventories.status = 'ng') group by model

	union all

	select materials.model, 0 as debt, 0 as plan, 0 as actual, sum(quantity) as wip, 0 as ng, 0 as stamp, 0 as h1 from stamp_inventories left join materials on materials.material_description = stamp_inventories.model where stamp_inventories.origin_group_code = '043' and process_code = 3 and stamp_inventories.status <> 'return' group by materials.model
	) as wip group by model

	union all

	select model, 0 as debt, 0 as plan, 0 as actual, 0 as wip, sum(quantity) as ng, 0 as stamp, 0 as h1 from stamp_inventories where origin_group_code = '043' and `status` = 'ng' group by model

	union all

	select model, 0 as debt, 0 as plan, 0 as actual, 0 as wip, 0 as ng, sum(quantity) as stamp, 0 as h1 from log_processes where origin_group_code = '043' and process_code = '2' and date(created_at) = '".$now."' group by model

	union all

	select model, 0 as debt, 0 as plan, 0 as actual, 0 as wip, 0 as ng, 0 as stamp, sum(quantity) as h1 from production_schedules left join materials on materials.material_number = production_schedules.material_number where due_date = '".$nextday."' and materials.category = 'FG' and materials.origin_group_code = '043' group by materials.model
	) as picking group by model
	";

	$tableData = DB::select($query);

	$response = array(
		'status' => true,
		'tableData' => $tableData,
	);
	return Response::json($response);
}

//------flute
public function fetchResultFlStamp(Request $request)
{
	// if(date('D')=='Fri' ){
	// 	$nextday = date('Y-m-d', strtotime(carbon::now()->addDays(3)));
	// 	$ceknextday = DB::SELECT("SELECT * FROM `weekly_calendars` where week_date = '".$nextday."'");
 //        foreach ($ceknextday as $key) {
 //            if ($key->remark == 'H') {
 //                $nextday = date('Y-m-d', strtotime(carbon::now()->addDays(4)));
 //            }
 //        }
	// }
	// else if(date('D')=='Sat' ){
	// 	$nextday = date('Y-m-d', strtotime(carbon::now()->addDays(2)));
	// 	$ceknextday = DB::SELECT("SELECT * FROM `weekly_calendars` where week_date = '".$nextday."'");
 //        foreach ($ceknextday as $key) {
 //            if ($key->remark == 'H') {
 //                $nextday = date('Y-m-d', strtotime(carbon::now()->addDays(3)));
 //            }
 //        }
	// }
	// else if(date('D')=='Thu' ){
	// 	$nextday = date('Y-m-d', strtotime(carbon::now()->addDays(4)));
	// 	$ceknextday = DB::SELECT("SELECT * FROM `weekly_calendars` where week_date = '".$nextday."'");
 //        foreach ($ceknextday as $key) {
 //            if ($key->remark == 'H') {
 //                $nextday = date('Y-m-d', strtotime(carbon::now()->addDays(5)));
 //            }
 //        }
	// }
	// else if(date('D')=='Wed' ){
	// 	$nextday = date('Y-m-d', strtotime(carbon::now()->addDays(4)));
	// 	$ceknextday = DB::SELECT("SELECT * FROM `weekly_calendars` where week_date = '".$nextday."'");
 //        foreach ($ceknextday as $key) {
 //            if ($key->remark == 'H') {
 //                $nextday = date('Y-m-d', strtotime(carbon::now()->addDays(6)));
 //            }
 //        }
	// }
	// else{
	// 	$nextday = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
	// }

	$i = 1;
	$nextday = date('Y-m-d', strtotime(carbon::now()->addDays($i)));
	$weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
	foreach ($weekly_calendars as $key) {
		if ($key->week_date == $nextday) {
			if ($key->remark == 'H') {
				$nextday = date('Y-m-d', strtotime(carbon::now()->addDays(++$i)));
			}
		}
	}

	$j = 2;
	$nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
	$weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
	foreach ($weekly_calendars as $key) {
		if ($key->week_date == $nextdayplus1) {
			if (str_contains($key->remark,'H')) {
				$nextdayplus1 = date('Y-m-d', strtotime($nextdayplus1 . ' +1 day'));
			}
		}
	}
	// if (date('D')=='Fri' || date('D')=='Sat') {
	// 	$nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
	// }

	$first = date('Y-m-01');
	if(date('Y-m-d') != date('Y-m-01')){
		$last = date('Y-m-d', strtotime(Carbon::yesterday()));
	}
	else{
		$last = date('Y-m-d');
	}
	$now = date('Y-m-d');

	if($first != $now){
		$debt = "union all

		select material_number, sum(debt) as debt, 0 as plan, 0 as actual from
		(
		select material_number, -(sum(quantity)) as debt from production_schedules where due_date >= '". $first ."' and due_date <= '". $last ."' group by material_number

		union all

		select material_number, sum(quantity) as debt from flo_details where date(created_at) >= '". $first ."' and date(created_at) <= '". $last ."' group by material_number
		) as debt
		group by material_number";
	}
	else{
		$debt= "";
	}

	// $act_lama = "select material_number, 0 as debt, 0 as plan, sum(quantity) as actual from flo_details where date(created_at) = '".$now."' and created_by != 2967 group by material_number";
	// $stamp_lama = "select model, 0 as debt, 0 as plan, 0 as actual, 0 as wip, 0 as ng, count(serial_number) as stamp,0 as stamp_kd,0 as h1,0 as h2,0 as adj from log_processes where origin_group_code = '041' and process_code = '1' and date(created_at) = '".$now."' and remark = 'FG' group by model";


	$queryAll = "select model,
	sum(debt) as debt,
	sum(plan) as plan,
	sum(actual) as actual,
	(sum(plan) + (-sum(debt)))-sum(adj) as targetToday,
	((sum(plan) + (-sum(debt)))-sum(actual))-(sum(wip)-sum(ng)) as sisaToday,
	sum(wip) as wip,
	sum(ng) as ng,
	sum(stamp) as stamp,
	sum(stamp_kd) as stamp_kd,
	sum(h1) as h1,
	-- IF(((sum(plan) + (-sum(debt)))-sum(actual))-(sum(wip)-sum(ng)) <= 0,
	-- sum(h1) - (-(((sum(plan) + (-sum(debt)))-sum(actual))-(sum(wip)-sum(ng)))),
	-- -sum(h1))
	((sum(plan) + (-sum(debt)))-sum(actual))-(sum(wip)-sum(ng))+sum(h1)
	as sisaH1,
	sum(h2) as h2,
	IF((sum(h1) - (-(((sum(plan) + (-sum(debt)))-sum(actual))-(sum(wip)-sum(ng))))) <= 0,
	sum(h2) + (sum(h1) - (-(((sum(plan) + (-sum(debt)))-sum(actual))-(sum(wip)-sum(ng))))),
	sum(h2)) 
	as sisaH2,
	sum(adj) as adj
	from
	(
	select materials.model, sum(assy.debt) as debt, sum(assy.plan) as plan, sum(assy.actual) as actual, 0 as wip, 0 as ng, 0 as stamp,0 as stamp_kd, 0 as h1,0 as h2,0 as adj from
	(
	select material_number, 0 as debt, sum(quantity) as plan, 0 as actual from production_schedules where due_date = '".$now."' group by material_number

	union all

	SELECT
		materials.material_number, 0 as debt, 0 as plan, count(assembly_logs.id) as actual
	FROM
		assembly_logs 
		join materials on materials.material_description = assembly_logs.model
	WHERE
		assembly_logs.origin_group_code = '041' 
		AND date( assembly_logs.created_at ) = '".$now."' 
		AND assembly_logs.location = 'packing'
		GROUP BY materials.material_number

	union all

	select material_number, sum(debt) as debt, 0 as plan, 0 as actual from
	(
	select material_number, -(sum(quantity)) as debt from production_schedules where due_date >= '".$first."' and due_date <= '".$last."' group by material_number

	union all

	select material_number, sum(quantity) as debt from flo_details where date(created_at) >= '".$first."' and date(created_at) <= '".$last."' and created_by != 2967 group by material_number
	) as debt
	group by material_number
	) as assy left join materials on materials.material_number = assy.material_number where materials.category = 'FG' and materials.origin_group_code = '041' group by materials.model

	union all

	select model, 0 as debt, 0 as plan, 0 as actual, count(serial_number) as wip, 0 as ng, 0 as stamp,0 as stamp_kd, 0 as h1,0 as h2,0 as adj from assembly_inventories where origin_group_code = '041' and deleted_at is null AND serial_number != 'null' AND after_packing is null group by model

	union all

	select model, 0 as debt, 0 as plan, 0 as actual, 0 as wip, sum(quantity) as ng, 0 as stamp,0 as stamp_kd, 0 as h1,0 as h2,0 as adj from stamp_inventories where origin_group_code = '041' and deleted_at is null and `status` = 'ng' group by model

	union all

	select model, 0 as debt, 0 as plan, 0 as actual, 0 as wip, 0 as ng, 0 as stamp, sum(quantity) as stamp_kd, 0 as h1,0 as h2,0 as adj from log_processes where origin_group_code = '041' and process_code = '1' and date(created_at) = '".$now."' and remark = 'KD' group by model

	union all

	SELECT
		a.model,
		0 AS debt,
		0 AS plan,
		0 AS actual,
		0 AS wip,
		0 AS ng,
		count( serial_number ) AS stamp,
		0 AS stamp_kd,
		0 AS h1,
		0 AS h2,
		0 AS adj 
	FROM
		(
		SELECT
			model,
			serial_number 
		FROM
			assembly_logs 
		WHERE
			origin_group_code = '041' 
			AND location = 'stamp-process' 
			AND date( sedang_start_date ) = '".$now."' 
		GROUP BY
			model,
			serial_number UNION ALL
		SELECT
			model,
			serial_number 
		FROM
			assembly_details 
		WHERE
			origin_group_code = '041' 
			AND location = 'stamp-process' 
			AND date( sedang_start_date ) = '".$now."' 
		GROUP BY
			model,
			serial_number 
		) a 
	GROUP BY
		a.model

	union all

	select model, 0 as debt, 0 as plan, 0 as actual, 0 as wip, 0 as ng, 0 as stamp,0 as stamp_kd, sum(quantity) as h1,0 as h2,0 as adj from production_schedules left join materials on materials.material_number = production_schedules.material_number where due_date = '".$nextday."' and materials.category = 'FG' and materials.origin_group_code = '041' group by materials.model

	union all
	
	select model, 0 as debt, 0 as plan, 0 as actual, 0 as wip, 0 as ng, 0 as stamp,0 as stamp_kd,0 as h1, sum(quantity) as h2,0 as adj from production_schedules left join materials on materials.material_number = production_schedules.material_number where due_date = '".$nextdayplus1."' and materials.category = 'FG' and materials.origin_group_code = '041' group by materials.model

	union all
	select model, 0 as debt, 0 as plan, 0 as actual, 0 as wip, 0 as ng, 0 as stamp,0 as stamp_kd,0 as h1, 0 as h2, sum(quantity) as adj from assembly_adjustments where origin_group_code = 041 and date >= '".$first."' and date <= '".$now."' GROUP BY model
	) as picking group by model

	";

	$materials = DB::table('materials')->where('model', 'like', 'YFL%')->select('model')->distinct()
	->orderBy('model')->get();

	$tableData = DB::select($queryAll);

	$response = array(
		'status' => true,
		'planData' => $tableData,
		'nextday' => $nextday,
		'nextdayplus1' => $nextdayplus1,
		'model' => $materials,
	);
	return Response::json($response);
}

public function indexfetchResultFlStamp(){
	return view('processes.assy_fl.picking_schedule', array(
		'title' => 'Picking Schedule Flute',
		'title_jp' => '',
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');

}

public function kd_label_besar_fl($gmc){

	if($gmc == 'ZE92410'){
		$barcode = db::select("SELECT 'serial_number' AS serial_number, stamp_hierarchies.finished, stamp_hierarchies.janean, stamp_hierarchies.upc, 'remark' AS remark, materials.material_description as model FROM stamp_hierarchies
			LEFT JOIN materials ON materials.material_number = stamp_hierarchies.finished 
			WHERE stamp_hierarchies.finished = '".$gmc."'");

		return view('processes.assy_fl.label_temp.label_besar',array(
			'barcode' => $barcode,
			'remark' => 'RP'
		))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');	
	}else{
		return view('processes.assy_fl.label_temp.label_besar_kd',array(
			'barcode' => $gmc
		))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
	}
	
	
}

public function kd_label_besar_outer_fl($gmc){
	
	$barcode = db::select("SELECT 'serial_number' AS serial_number, stamp_hierarchies.finished, stamp_hierarchies.janean, stamp_hierarchies.upc, 'remark' AS remark, materials.material_description as model FROM stamp_hierarchies
		LEFT JOIN materials ON materials.material_number = stamp_hierarchies.finished 
		WHERE stamp_hierarchies.finished = '".$gmc."'");

	$date = DB::select("SELECT week_date, date_code from weekly_calendars
		WHERE week_date = '".date('Y-m-d')."'");

	return view('processes.assy_fl.label_temp.label_besar_outer_kd',array(
		'barcode' => $barcode,
		'date' => $date,
		'remark' => 'RP'
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function kd_label_des_fl($gmc){

	if($gmc == 'ZE92410'){
		$barcode = DB::select("select material_description as model from materials where material_number = '".$gmc."'");
	}else{
		$barcode = DB::select("select kd_name as model from materials where material_number = '".$gmc."'");
	}

	return view('processes.assy_fl.label_temp.label_desc_kd',array(
		'barcode' => $barcode,
		'sn' => $gmc,
		'remark' => 'RP',
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function kd_label_carb_fl($id){
	$date = db::select("SELECT DATE_FORMAT( week_date, '%m-%Y' ) AS tgl FROM weekly_calendars where week_date = '".date('Y-m-d')."'");

	return view('processes.assembly.flute.label.label_carb_new',array(
		'date' => $date,
		'sn' => $id
	))->with('page', 'Process Assy FL')->with('head', 'Assembly Process');
}

public function fetchCheckKd(Request $request){
	
	$gmc = $request->get('gmc');

	if(in_array($gmc, $this->kd_gmc)){
		if($gmc == 'ZE92410'){
			$material = Material::where('material_number', $gmc)
			->select('material_number', 'material_description')
			->first();
		}else{
			$material = Material::where('material_number', $gmc)
			->select('material_number', db::raw('kd_name AS material_description'))
			->first();
		}

		$response = array(
			'status' => true,
			'material' => $material
		);
		return Response::json($response);
	}else{
		$response = array(
			'status' => false
		);
		return Response::json($response);
	}
}

}


