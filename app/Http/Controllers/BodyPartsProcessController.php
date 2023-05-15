<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\CodeGenerator;
use App\Employee;
use App\EmployeeSync;
use App\Material;
use Carbon\Carbon;
use DataTables;
use Response;
use DateTime;
use Excel;

use Illuminate\Database\QueryException;
use App\BproNgLog;
use App\BproCheckLog;
use App\BproReworkLog;
use App\BproTempLog;

use App\MaterialPlantDataList;
use App\StandardTime;
use App\Jig;
use App\JigBom;
use App\JigSchedule;
use App\JigKensaCheck;
use App\JigKensa;
use App\JigKensaLog;
use App\JigRepair;
use App\JigRepairLog;
use App\JigPartStock;
use App\SolderingStandardTime;
use App\WorkshopJobOrderLog;
use App\WorkshopJobOrder;
use App\EmployeeGroup;
use App\WeeklyCalendar;
use FTP;
use File;

use Storage;


class BodyPartsProcessController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
		$this->location_sx = [			
			'asbody-kensa-sx',
			'asbell-kensa-sx',
			'asbow-kensa-sx',
			'asneck-kensa-sx',
			'tsneck-kensa-sx',
			'bellyds-kensa-sx',
			
		];
		$this->location_fl = [			
			'fheadfinish-kensa-fl',
			'fbodyfinish-kensa-fl',
			'ffootfinish-kensa-fl',
			'fbody-kensa-fl-pipe',
			'ffoot-kensa-fl-pipe',
			'fbody-kensa-fl-onko',
			'ffoot-kensa-fl-onko',

		];
		$this->location_cl = [
			'cl',			
		];
		$this->hpl = [
			'FLHEAD',
			'FLBODY',
			'ASBELL',
			'ASBODY',
			'ASBOW',
			'ASNECK',
			'TSNECK',
			'ASKEY',
			'TSKEY',
			'FLKEY'
		];

		$this->category = [
			'BODY',
			'HEAD',
			'FOOT',
			'BELL',
			'BOW',
			'NECK',															
			'BELLYDS',												
			'HEADFINISH',
			'BODYFINISH',						
			
		];
		$this->type = [
			'Alto',
			'Tenor',
			'A82Z'
		];
		$this->fy = db::table('weekly_calendars')->select('fiscal_year')->distinct()->get();
	}

    public function indexFL() {        
		$title = 'Body Process Flute';
		$title_jp = 'ルートの部品加工';

		return view('processes.bodyprocess.index_fl', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Body Process FL');
		
    }

    public function indexSX() {        
		$title = 'Body Process Saxophone';
		$title_jp = '??';

		return view('processes.bodyprocess.index_sx', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Body Process FL');
		
    }

	//Master Operator

    public function indexMasterOperator($location) {
        if ($location == 'sx') {
            $title = 'Master Operator Body Parts Process Saxophone';
            $title_jp = '??';
        } elseif ($location == 'fl') {
            $title = 'Master Operator Body Parts Process Flute';
            $title_jp = '??';
        } elseif ($location == 'cl') {
            $title = 'Master Operator Body Parts Process Clarinet';
            $title_jp = '??';
        }

        $emp = DB::SELECT("SELECT
			* 
			FROM
			`employee_syncs` 
			WHERE
			department = 'Woodwind Instrument - Body Parts Process (WI-BPP) Department' 
		");

        return view('processes.bodyprocess.master_operator', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'location' => $location,
            'emp' => $emp,
            'emp2' => $emp,
        ))->with('page', 'Master Operator Body Parts Process');		
    }

    public function fetchMasterOperator(Request $request)
	{
		$lists = DB::connection('ympimis_2')->table('bpro_operators')->select('bpro_operators.*','tag as tags')->where('location',$request->get('location'))->get();

		$response = array(
			'status' => true,
			'lists' => $lists
		);
		return Response::json($response);
	}

    public function inputOperator(Request $request)
	{
		try {
			$employee_id = $request->get('employee_id');
			$tag = $request->get('tag');
			$location = $request->get('location');
			$shift = $request->get('shift');
			$remark = $request->get('remark');

			$check = DB::connection('ympimis_2')->table('bpro_operators')->where('employee_id',$employee_id)->where('location',$location)->first();

			if ($check) {
				$response = array(
					'status' => false,
					'message' => 'Operator Sudah Ada di List'
				);
				return Response::json($response);
			}

			$emp = EmployeeSync::where('employee_id',$employee_id)->first();

			$input = DB::connection('ympimis_2')->table('bpro_operators')->insert([
				'employee_id' => $employee_id,
				'name' => $emp->name,
				'tag' => $tag,
				'location' => $location,
				'shift' => $shift,
				'remark' => $remark,
				'created_by' => Auth::user()->id,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			]);

			$response = array(
				'status' => true,
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

	public function destroyOperator($id,$employee_id)
	{		
		$list = DB::connection('ympimis_2')
		->table('bpro_operators')
		->where('employee_id','=',$employee_id)
		->delete();

		EmployeeGroup::where('employee_id', $employee_id)->where('location', 'BPP FL')->forceDelete();

		return redirect('index/body_parts_process/operator')->with('status','Success Delete Operator');
	}

	public function updateOperator(Request $request)
	{
		try {
			$id = $request->get('id');
			$employee_id = $request->get('employee_id');
			$tag = $request->get('tag');
			$shift = $request->get('shift');
			$remark = $request->get('remark');

			$emp = EmployeeSync::where('employee_id',$employee_id)->first();

			$update = DB::connection('ympimis_2')->table('bpro_operators')->where('id',$id)->update([
				'employee_id' => $employee_id,
				'name' => $emp->name,
				'tag' => $tag,
				'shift' => $shift,
				'remark' => $remark,
				'updated_at' => date('Y-m-d H:i:s')
			]);

			$response = array(
				'status' => true
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

	public function scanBproOperator(Request $request){

		if($request->get('employee_id') == null){
			$response = array(
				'status' => false,
				'message' => 'Tag karyawan tidak ditemukan',
			);
			return Response::json($response);
		}

		$pattern = '/PI/i';
		if (str_contains($request->get('employee_id'),'PI')) {
			$employee = db::table('employees')->where('employee_id', '=', $request->get('employee_id'))->first();
		}else{
			$employee = db::table('employees')->where('tag', '=', $request->get('employee_id'))->first();
		}

		if($employee == null){
			$response = array(
				'status' => false,
				'message' => 'Tag karyawan tidak ditemukan',
			);
			return Response::json($response);			
		}

		$response = array(
			'status' => true,
			'message' => 'Tag karyawan ditemukan',
			'employee' => $employee,
		);
		return Response::json($response);
	}

	//Master Kanban

    public function indexMasterKanban($location){

		$work_station = DB::connection('ympimis_2')->select("SELECT DISTINCT work_station FROM bpros");
		$material = DB::connection('ympimis_2')->table('bpro_materials')->select('material_number','material_description','material_alias','material_category','material_type')->where('locations',$location)->distinct()->get();
		$material_category = DB::connection('ympimis_2')->table('bpro_materials')->select('material_category')->where('locations',$location)->distinct()->get();
		$material_type = DB::connection('ympimis_2')->table('bpro_materials')->select('material_type')->where('locations',$location)->distinct()->get();

		$title = "Master Kanban";
		$title_jp = "";

		return view('processes.bodyprocess.master_kanban', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'location' => $location,
				'material_category' => $material_category,
				'material_type' => $material_type,
				'material' => $material,
				'material2' => $material,
				'material3' => $material,
				'work_station' => $work_station,
			))->with('page', 'Master Kanban Body Parts Process');
	}

	public function fetchMasterKanban(Request $request){
		try {
			$kanban = DB::connection('ympimis_2')->table('bpro_tags')->select('bpro_tags.*')->where('location',$request->get('location'));

			if ($request->get('material') != '') {
				$kanban = $kanban->where('material_number',$request->get('material'));
			}

			if ($request->get('material_type') != '') {
				$kanban = $kanban->where('material_type',$request->get('material_type'));
			}

			if ($request->get('material_category') != '') {
				$kanban = $kanban->where('material_category',$request->get('material_category'));
			}

			$kanban = $kanban->get();
			$response = array(
				'status' => true,
				'kanban' => $kanban
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

	public function updateKanban(Request $request){
		try{
			$id = $request->get('id');
			$material = $request->get('material');
			$tag = $request->get('tag');
			$no_kanban = $request->get('no_kanban');
			$barcode = $request->get('barcode');

			$bpro_materials = DB::connection('ympimis_2')->table('bpro_materials')->where('material_number',explode('_', $material)[0])->where('material_category',explode('_', $material)[1])->where('material_type',explode('_', $material)[2])->first();

			$update = DB::connection('ympimis_2')->table('bpro_tags')->where('id',$id)->update([
				'material_number' => explode('_', $material)[0],
				'material_description' => $bpro_materials->material_description,
				'material_category' => explode('_', $material)[1],
				'material_type' => explode('_', $material)[2],
				'tag' => $tag,
				'no_kanban' => $no_kanban,
				'barcode' => $barcode,
				'updated_at' => date('Y-m-d H:i:s'),
			]);

			$response = array(
				'status' => true
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

	public function inputKanban(Request $request)
	{
		try{
			$id = $request->get('id');
			$material = $request->get('material');
			$tag = $request->get('tag');
			$no_kanban = $request->get('no_kanban');
			$barcode = $request->get('barcode');
			$location = $request->get('location');

			$bpro_materials = DB::connection('ympimis_2')->table('bpro_materials')->where('material_number',explode('_', $material)[0])->where('material_category',explode('_', $material)[1])->where('material_type',explode('_', $material)[2])->first();

			$update = DB::connection('ympimis_2')->table('bpro_tags')->insert([
				'material_number' => explode('_', $material)[0],
				'material_description' => $bpro_materials->material_description,
				'material_category' => explode('_', $material)[1],
				'material_type' => explode('_', $material)[2],
				'tag' => $tag,
				'no_kanban' => $no_kanban,
				'barcode' => $barcode,
				'location' => $location,
				'created_by' => Auth::user()->id,
				'updated_at' => date('Y-m-d H:i:s'),
				'created_at' => date('Y-m-d H:i:s'),
			]);

			$response = array(
				'status' => true
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

	public function deleteKanban(Request $request)
	{
		try {
			$id = $request->get('id');
			$delete = DB::connection('ympimis_2')->table('bpro_tags')->where('id',$id)->delete();
			$response = array(
				'status' => true,
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

	// Master Material

	public function indexMasterMaterial($location)
	{
		$work_station = DB::connection('ympimis_2')->table('bpro_materials')->select('work_station')->distinct()->where('locations',$location)->get();
		$material_category = DB::connection('ympimis_2')->table('bpro_materials')->select('material_category')->distinct()->where('locations',$location)->get();
		$material_type = DB::connection('ympimis_2')->table('bpro_materials')->select('material_type')->distinct()->where('locations',$location)->get();		
		$materials = Material::where('issue_storage_location','SXA1')->orWhere('issue_storage_location','FLA1')->get();
		$ws = DB::connection('ympimis_2')->table('bpros')->select('work_station')->distinct()->get();
		return view('processes.bodyprocess.master_materials', array(
			'title' => 'Master Material Body Parts Process',
			'title_jp' => '',
			'location' => $location,
			'ws' => $ws,
			'materials' => $materials,
			'work_station' => $work_station,
			'material_category' => $material_category,
			'material_type' => $material_type,
			'category' => $this->category,
			'type' => $this->type,
		))->with('page', 'Master Material Body Parts Process');
	}

	public function fetchMasterMaterial(Request $request)
	{
		try {
			$material = DB::connection('ympimis_2')->table('bpro_materials')->where('locations',$request->get('location'));
			if ($request->get('work_station') != '') {
				$material = $material->where('work_station',$request->get('work_station'));
			}

			if ($request->get('material_category') != '') {
				$material = $material->where('material_category',$request->get('material_category'));
			}

			if ($request->get('material_type') != '') {
				$material = $material->where('material_type',$request->get('material_type'));
			}

			$material = $material->get();
			
			$response = array(
				'status' => true,
				'material' => $material
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

	public function updateMasterMaterial(Request $request)
	{
		try {
			$id = $request->get('id');
			$material = $request->get('material');
			$material_alias = $request->get('material_alias');
			$quantity = $request->get('quantity');
			$work_station = $request->get('work_station');
			$material_category = $request->get('material_category');
			$material_type = $request->get('material_type');
			$standard_time = $request->get('standard_time');

			$materials = Material::where('material_number',$material)->first();

			$bpro_materials = DB::connection('ympimis_2')->table('bpro_materials')->where('id',$id)->first();

			$update = DB::connection('ympimis_2')->table('bpro_materials')->where('id',$id)->update([
				'material_number' => $material,
				'material_description' => $materials->material_description,
				'material_alias' => $material_alias,
				'quantity' => $quantity,
				'work_station' => $work_station,
				'material_category' => $material_category,
				'material_type' => $material_type,
				'standard_time' => $standard_time,
				'updated_at' => date('Y-m-d H:i:s'),
			]);

			$tags = DB::connection('ympimis_2')->table('bpro_tags')->where('material_number',$material)->where('material_category',$bpro_materials->material_category)->where('material_type',$bpro_materials->material_type)->get();

			if (count($tags) > 0) {
				$update_tags = DB::connection('ympimis_2')->table('bpro_tags')->where('material_number',$material)->where('material_category',$bpro_materials->material_category)->where('material_type',$bpro_materials->material_type)->update([
					'material_category' => $material_category,
					'material_type' => $material_type,
				]);
			}
			$response = array(
				'status' => true,
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

	public function indexMasterFlow($loc){
		switch ($loc) {
			case 'fl':
				$title = "Master Flow";
				$title_jp = "";
				$origin_group_code = "041";
				$location = "flute";
				break;

			case 'sx':
				$title = "Master Flow";
				$title_jp = "";
				$origin_group_code = "043";
				$location = "saxophone";
				break;
			
			default:
				$title = "Master Flow BPRO";
				$title_jp = "";
				$origin_group_code = "000";
				$location = "Unknown";
				break;

				
		}

		return view('processes.bodyprocess.master_flow', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'location' => $location,						
			'origin_group_code' => $origin_group_code,						
		))->with('page', 'Master Flow Body Parts Process');
	}

	public function indexMasterFlows($loc){
		switch ($loc) {
			case 'fl':
				$title = "Master Flow";
				$title_jp = "";
				$origin_group_code = "041";
				$location = "flute";
				break;

			case 'sx':
				$title = "Master Flow";
				$title_jp = "";
				$origin_group_code = "043";
				$location = "saxophone";
				break;
			
			default:
				$title = "Master Flow BPRO";
				$title_jp = "";
				$origin_group_code = "000";
				$location = "Unknown";
				break;

				
		}

		return view('processes.bodyprocess.master_flows', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'location' => $location,						
			'origin_group_code' => $origin_group_code,						
		))->with('page', 'Master Flow Body Parts Process');
	}

	public function fetchMasterFlow(Request $request){				

		// $flowewe = db::connection('ympimis_2')->table('bpro_flows')
		// ->select('material_type','flow', db::raw("GROUP_CONCAT(COALESCE(flow, ' ') ORDER BY ordering ASC) as flow"),'bpro_flowkanban.barcode')		
		// ->where('origin_group_code', '=', $request->get('origin_group_code'))
		// ->groupBy('material_type')		
		// ->get();	

		

		$flow = DB::connection('ympimis_2')->table('bpro_flows')
		->leftJoin('bpro_materials', 'bpro_flows.category', '=', 'bpro_materials.material_category')
		// ->leftJoin('bpro_tags', 'bpro_tags.material_number', '=', 'bpro_materials.material_number')
		// ->select('bpro_flows.category', DB::raw('GROUP_CONCAT(DISTINCT flow ORDER BY bpro_flows.ordering ASC) AS flow'), DB::raw('GROUP_CONCAT(DISTINCT COALESCE(bpro_tags.barcode) ORDER BY CAST(SUBSTRING(bpro_tags.barcode FROM 10) AS UNSIGNED) ASC) AS kanban'))
		->select('bpro_flows.category', DB::raw('GROUP_CONCAT(DISTINCT flow ORDER BY bpro_flows.ordering ASC) AS flow'))
		->groupBy('bpro_flows.category')
		->where('bpro_flows.origin_group_code', '=', $request->get('origin_group_code'))		
		->get();		
		


		try {			
			$response = array(
				'status' => true,				
				'flow' => $flow,
			);

			return Response::json($response);			

		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
		}
		return Response::json($response);	
	}

	public function fetchSingleFlow(Request $request)
	{				

		$flow = db::connection('ympimis_2')->table('bpro_flows')
		->select('id','ordering','flow')
		->where('origin_group_code', '=', $request->get('origin_group_code'))
		->where('category', '=', $request->get('category'))
		->groupBy('id','ordering','flow')
		->orderBy('ordering', 'asc')
		->get();		

		try {			
			$response = array(
				'status' => true,				
				'singleFlow' => $flow,
			);

			return Response::json($response);			

		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
		}
		return Response::json($response);	
	}

	public function changeFlow(Request $request)
	{						
		$id = $request->get('id');
		$category = $request->get('category');		

		$material_type = db::connection('ympimis_2')->table('bpro_materials')
		->select('material_type')
		->where('material_category', '=', $category)
		->first();

		$material_type = $material_type->material_type;

		$flow = $request->get('flow');
		$ordering = $request->get('ordering');
		$move = $request->get('move');

		$query = db::connection('ympimis_2')->table('bpro_flows')
			->where('origin_group_code', '=', $request->get('origin_group_code'))
			->where('category', '=', $category);

		if ($move == 'up') {
			$query2 = clone $query;
			$query3 = clone $query;
			$previous_ordering = $ordering - 1;
			$previous_row = $query->where('ordering', '=', $previous_ordering)->first();
			if ($previous_row) {
				$previous_id = $previous_row->id;
				$query2->where('id', '=', $previous_id)
					->update(['ordering' => $ordering]);
				$query3->where('id', '=', $id)
					->update(['ordering' => $previous_ordering]);
			}

			$response = array(
				'status' => 'success',				
				'message' => 'Flow Updated',
			);

		} else if ($move == 'down') {
			$query2 = clone $query;
			$query3 = clone $query;
			$next_ordering = $ordering + 1;
			$next_row = $query->where('ordering', '=', $next_ordering)->first();
			if ($next_row) {
				$next_id = $next_row->id;
				$query2->where('id', '=', $id)
					->update(['ordering' => $next_ordering]);
				$query3->where('id', '=', $next_id)
					->update(['ordering' => $ordering]);
			}

			$response = array(
				'status' => 'success',				
				'message' => 'Flow Updated',
			);

		} else if ($move == 'delete') {
			try {
				$query2 = clone $query;
				$query3 = clone $query;
				$query2->where('ordering', '>', $ordering)
					->decrement('ordering');
				$query3->where('id', '=', $id)
					->delete();								

				$response = array(
					'status' => 'success',				
					'message' => 'Flow Deleted',
				);
			} catch (\Exception $e) {
				$response = array(
					'status' => 'error',				
					'message' => $e->getMessage(),
				);
			}

		} else if ($move == 'add') {			
			$query2 = clone $query;
			$query3 = clone $query;
			$query2->where('ordering', '>=', $ordering)
				->increment('ordering');			
			$query3->insert([
				'origin_group_code' => $request->get('origin_group_code'),
				'category' => $category,				
				'material_type' => $material_type,				
				'ordering' => $ordering,				
				'flow' => strtolower(str_replace(' ', '-', $flow)),
				'created_by' => 1,
				'updated_at' => date('Y-m-d H:i:s'),
				'created_at' => date('Y-m-d H:i:s'),				
			]);

			$response = array(
				'status' => 'success',				
				'message' => 'Flow Added',
			);
		}

		try {								

			return Response::json($response);			

		} catch (\Exception $e) {
			$response = array(
				'status' => 'error',
				'message' => $e->getMessage(),
			);
		}

			
	}


	public function indexBproTarget($loc) {
		$title = 'Bpro Target';
		$title_jp = '??';
		$loc = $loc;
		
		return view('processes.bodyprocess.master_target', array(
			'location' => $loc,
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Bpro Target');
	}

	public function fetchBproTarget(Request $request) {
		$loc = $request->get('location');
		$target = DB::connection('ympimis_2')->table('bpro_ng_targets')
		// ->where('location', 'REGEXP', '(-' . substr($loc, -2) . '$)|(' . substr($loc, -2) . '.+)')
		->where('location', 'like', '%'.$loc.'%')
		->get();


		$response = array(
			'status' => true,
			'target' => $target
		);
		return Response::json($response);
	}

	public function updateBproTarget(Request $request) {
		$id = $request->get('id');
		$target = $request->get('target');		

		try {
			$target = DB::connection('ympimis_2')->table('bpro_ng_targets')
			->where('id', $id)
			->update([				
				'target' => $target,				
				'updated_at' => date('Y-m-d H:i:s')
			]);

			$response = array(
				'status' => true,
				'message' => 'Target Updated'
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



	public function indexBproKensa($id)
	{
		$ng_lists = DB::table('ng_lists')->where('location', '=', $id)->where('remark', '=', 'bpro')->get();

		switch ($id) {
			case 'fheadfinish-kensa-fl':
				$title = 'Flute Head Finish Kensa ';
				$title_jp = '??';
				break;

			case 'fbodyfinish-kensa-fl':
				$title = 'Flute Body Finish Kensa ';
				$title_jp = '??';
				break;
				
			case 'ffootfinish-kensa-fl':
				$title = 'Flute Foot Finish Kensa ';
				$title_jp = '??';
				break;			

			case 'fbody-kensa-fl-pipe':
				$title = 'Flute Body Pipe Kensa ';
				$title_jp = '??';
				break;

			case 'ffoot-kensa-fl-pipe':
				$title = 'Flute Foot Pipe Kensa ';
				$title_jp = '??';
				break;

			case 'fbody-kensa-fl-onko':
				$title = 'Flute Body Onko Kensa ';
				$title_jp = '??';
				break;			

			case 'ffoot-kensa-fl-onko':
				$title = 'Flute Foot Onko Kensa ';
				$title_jp = '??';
				break;

			case 'asbody-kensa-sx':
				$title = 'Saxophone AS Body Kensa ';
				$title_jp = '??';
				break;			

			case 'asbell-kensa-sx':
				$title = 'Saxophone AS Bell Kensa ';
				$title_jp = '??';
				break;			

			case 'asbow-kensa-sx':
				$title = 'Saxophone AS Bow Kensa ';
				$title_jp = '??';
				break;			

			case 'asneck-kensa-sx':
				$title = 'Saxophone AS Neck Kensa ';
				$title_jp = '??';
				break;			

			case 'tsneck-kensa-sx':
				$title = 'Saxophone TS Neck Kensa ';
				$title_jp = '??';
				break;			

			case 'bellyds-kensa-sx':
				$title = 'Saxophone Bell YDS Kensa ';
				$title_jp = '??';
				break;			
			
			default:
				$title = 'Unknown';
				$title_jp = '??';
				break;
		}

		return view('processes.bodyprocess.kensa', array(
			'ng_lists' => $ng_lists,
			'loc' => $id,
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Body Parts Process')->with('head', 'Body Parts Process');
	}

	public function scanBproKensa(Request $request){
		try {
			$location = explode('-', $request->get('location'))[0];
			$loc = explode('-', $request->get('location'))[2];
			$tag = $request->get('tag');									

			
			if (str_contains($request->get('location'),'sx')) {
				$origin_group_code = '043';
			}else if (str_contains($request->get('location'),'fl')) {
				$origin_group_code = '041';
			}else if (str_contains($request->get('location'),'cl')) {
				$origin_group_code = '042';
			}			

			if($loc == 'fl'){
				$zed_material = db::connection('kitto')->table('completions')
				->leftjoin('materials', 'completions.material_id', '=', 'materials.id')
				->where('completions.barcode_number', '=', $tag)	
				->where('materials.location', '=', 'FLA1')
				->first();

				if($zed_material == null){
					$response = array(
						'status' => false,
						'message' => 'Tag material tidak ditemukan',
					);
					return Response::json($response);
				}
				
				$zed_operator = db::connection('ympimis_2')->table('bpro_details')
				->select('bpro_details.*','bpro_operators.name')
				->where('bpro_details.tag', $tag)
				// ->where('origin_group_code',$origin_group_code)
				// ->where('welding_details.location', $request->get('location'))
				->leftjoin('bpro_operators', 'bpro_details.last_check', '=', 'bpro_operators.employee_id')
				->first();

				$material = db::table('materials')->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'materials.material_number')
				->where('materials.material_number', '=', $zed_material->material_number)
				->select('materials.model', 'materials.key', 'materials.surface', 'materials.material_number', 'materials.hpl', 'material_volumes.lot_completion')
				->first();				
			}
			else if($loc == 'sx'){
				$zed_material = db::connection('kitto')->table('completions')
				->leftjoin('materials', 'completions.material_id', '=', 'materials.id')
				->where('completions.barcode_number', '=', $tag)
				->where('materials.location', '=', 'SXA1')
				->first();				

				if($zed_material == null){
					$response = array(
						'status' => false,
						'message' => 'Tag material tidak ditemukan',
					);
					return Response::json($response);
				}

				$zed_operator = db::connection('ympimis_2')->table('bpro_details')
				->select('bpro_details.*','bpro_operators.name')
				->where('bpro_details.tag', $tag)
				// ->where('origin_group_code',$origin_group_code)
				// ->where('welding_details.location', $request->get('location'))
				->leftjoin('bpro_operators', 'bpro_details.last_check', '=', 'bpro_operators.employee_id')
				->first();

				$material = db::table('materials')->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'materials.material_number')
				->where('materials.material_number', '=', $zed_material->material_number)
				->select('materials.model', 'materials.key', 'materials.surface', 'materials.material_number', 'materials.hpl', 'material_volumes.lot_completion')
				->first();

				// dd($material);
			}			

			$emp = EmployeeSync::where('end_date',null)->get();

			if(($request->get('location') == 'fheadfinish-kensa-fl') || ($request->get('location') == 'fbodyfinish-kensa-fl') || ($request->get('location') == 'fbody-kensa-fl-pipe') || ($request->get('location') == 'ffoot-kensa-fl-pipe') || ($request->get('location') == 'fbody-kensa-fl-onko') || ($request->get('location') == 'foot-kensa-fl-onko') || ($request->get('location') == 'ffootfinish-kensa-fl')){
				$response = array(
					'status' => true,
					'message' => 'Material ditemukan',
					'material' => $material,
					'emp' => $emp,
					'opbpro' => $zed_operator,
					'started_at' => date('Y-m-d H:i:s'),
					'attention_point' => asset("/bpro/attention_point/".$material->model." ".$material->key." ".$material->surface.".jpg"),
					'check_point' => asset("/bpro/check_point/".$material->model." ".$material->key." ".$material->surface.".jpg"),
					'check_point_dimensi' => asset("/bpro/check_point_dimensi/".$zed_material->material_number.".jpg")
				);				
				return Response::json($response);
			}else{
				$response = array(
					'status' => true,
					'message' => 'Material ditemukan',
					'material' => $material,
					'emp' => $emp,
					'opbpro' => $zed_operator,
					'started_at' => date('Y-m-d H:i:s'),
					'attention_point' => asset("/bpro/attention_point/".$material->model." ".$material->key." ".$material->surface.".jpg"),
					'check_point' => asset("/bpro/check_point/".$material->model." ".$material->key." ".$material->surface.".jpg")
				);
				return Response::json($response);
			}
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function scanBproKensaForTags(Request $request){
		try {
			$location = explode('-', $request->get('location'))[0];
			$loc = explode('-', $request->get('location'))[2];
			$tag = $request->get('tag');						
			
			if (str_contains($request->get('location'),'sx')) {
				$origin_group_code = '043';
			}else if (str_contains($request->get('location'),'fl')) {
				$origin_group_code = '041';
			}else if (str_contains($request->get('location'),'cl')) {
				$origin_group_code = '042';
			}

			if($loc == 'fl'){
				$zed_material = db::connection('ympimis_2')->table('bpro_tags')
				->where('bpro_tags.tag', '=', $tag)
				// ->where('origin_group_code', '=', $origin_group_code)
				->where('location', '=', $loc)
				->first();

				if($zed_material == null){
					$response = array(
						'status' => false,
						'message' => 'Tag material tidak ditemukan',
					);
					return Response::json($response);
				}
				
				$zed_operator = db::connection('ympimis_2')->table('bpro_details')
				->select('bpro_details.*','bpro_operators.name')
				->where('bpro_details.tag', $tag)
				// ->where('origin_group_code',$origin_group_code)
				// ->where('welding_details.location', $request->get('location'))
				->leftjoin('bpro_operators', 'bpro_details.last_check', '=', 'bpro_operators.employee_id')
				->first();

				$material = db::table('materials')->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'materials.material_number')
				->where('materials.material_number', '=', $zed_material->material_number)
				->select('materials.model', 'materials.key', 'materials.surface', 'materials.material_number', 'materials.hpl', 'material_volumes.lot_completion')
				->first();				
			}
			else if($loc == 'sx'){
				$zed_material = db::connection('ympimis_2')->table('bpro_tags')
				->where('bpro_tags.tag', '=', $tag)
				// ->where('origin_group_code', '=', $origin_group_code)
				->where('location', '=', $loc)
				->first();

				if($zed_material == null){
					$response = array(
						'status' => false,
						'message' => 'Tag material tidak ditemukan',
					);
					return Response::json($response);
				}

				$zed_operator = db::connection('ympimis_2')->table('bpro_details')
				->select('bpro_details.*','bpro_operators.name')
				->where('bpro_details.tag', $tag)
				// ->where('origin_group_code',$origin_group_code)
				// ->where('welding_details.location', $request->get('location'))
				->leftjoin('bpro_operators', 'bpro_details.last_check', '=', 'bpro_operators.employee_id')
				->first();

				$material = db::table('materials')->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'materials.material_number')
				->where('materials.material_number', '=', $zed_material->material_number)
				->select('materials.model', 'materials.key', 'materials.surface', 'materials.material_number', 'materials.hpl', 'material_volumes.lot_completion')
				->first();
			}			

			$emp = EmployeeSync::where('end_date',null)->get();

			if(($request->get('location') == 'fheadfinish-kensa-fl') || ($request->get('location') == 'fbodyfinish-kensa-fl') || ($request->get('location') == 'fbody-kensa-fl-pipe') || ($request->get('location') == 'ffoot-kensa-fl-pipe') || ($request->get('location') == 'fbody-kensa-fl-onko') || ($request->get('location') == 'ffoot-kensa-fl-onko') || ($request->get('location') == 'ffootfinish-kensa-fl')){
				$response = array(
					'status' => true,
					'message' => 'Material ditemukan',
					'material' => $material,
					'emp' => $emp,
					'opbpro' => $zed_operator,
					'started_at' => date('Y-m-d H:i:s'),
					'attention_point' => asset("/bpro/attention_point/".$material->model." ".$material->key." ".$material->surface.".jpg"),
					'check_point' => asset("/bpro/check_point/".$material->model." ".$material->key." ".$material->surface.".jpg"),
					'check_point_dimensi' => asset("/bpro/check_point_dimensi/".$zed_material->material_number.".jpg")
				);				
				return Response::json($response);
			}else{
				$response = array(
					'status' => true,
					'message' => 'Material ditemukan',
					'material' => $material,
					'emp' => $emp,
					'opbpro' => $zed_operator,
					'started_at' => date('Y-m-d H:i:s'),
					'attention_point' => asset("/bpro/attention_point/".$material->model." ".$material->key." ".$material->surface.".jpg"),
					'check_point' => asset("/bpro/check_point/".$material->model." ".$material->key." ".$material->surface.".jpg")
				);
				return Response::json($response);
			}
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

	public function fetchKensaResult(Request $request){

		try {
			$location = $request->get('location');
			$employee_id = $request->get('employee_id');
			$now = date('Y-m-d');
			
			$query1 = "SELECT
				sum( IF ( ympimis.materials.model <> 'A82' AND ympimis.materials.hpl = 'ASKEY', bpro_check_logs.quantity, 0 ) ) AS askey,
				sum( IF ( ympimis.materials.model <> 'A82' AND ympimis.materials.hpl = 'TSKEY', bpro_check_logs.quantity, 0 ) ) AS tskey,
				sum( IF ( ympimis.materials.model LIKE '%82%', bpro_check_logs.quantity, 0 ) ) AS `z` 
			FROM
				bpro_check_logs
				LEFT JOIN ympimis.materials ON ympimis.materials.material_number = bpro_check_logs.material_number 
			WHERE
				employee_id = '".$employee_id."' 
				AND date( bpro_check_logs.created_at ) = '".$now."' 
				AND bpro_check_logs.remark = 'OK' 
				AND location = '".$location."'";

			$oks = db::connection('ympimis_2')->select($query1);

			$query2 = "SELECT
				sum( IF ( ympimis.materials.model <> 'A82' AND ympimis.materials.hpl = 'ASKEY', bpro_ng_logs.quantity, 0 ) ) AS askey,
				sum( IF ( ympimis.materials.model <> 'A82' AND ympimis.materials.hpl = 'TSKEY', bpro_ng_logs.quantity, 0 ) ) AS tskey,
				sum( IF ( ympimis.materials.model LIKE '%82%', bpro_ng_logs.quantity, 0 ) ) AS `z` 
			FROM
				bpro_ng_logs
				LEFT JOIN ympimis.materials ON ympimis.materials.material_number = bpro_ng_logs.material_number 
			WHERE
				employee_id = '".$employee_id."' 
				AND date( bpro_ng_logs.created_at ) = '".$now."' 
				AND location = '".$location."'";

			$ngs = db::connection('ympimis_2')->select($query2);

			dd($oks, $ngs);

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

	public function inputBproKensa(Request $request){

		$code_generator = CodeGenerator::where('note','=','bpro-kensa')->first();
		$code = $code_generator->index+1;
		$code_generator->index = $code;
		$code_generator->save();

		$tag = $request->get('tag');		

		if($request->get('ng')){			
			foreach ($request->get('ng') as $ng) {				
				try{
					$bpro_ng_log = db::connection('ympimis_2')->table('bpro_ng_logs')->insert([
						'employee_id' => $request->get('employee_id'),
						'tag' => $request->get('tag'),
						'material_number' => $request->get('material_number'),
						'ng_name' => $ng[0],
						'quantity' => $ng[1],
						'location' => $request->get('loc'),
						'operator_id' => $request->get('operator_id'),
						'started_at' => $request->get('started_at'),
						'processing_time' => $request->get('processing_time'),
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
				
				$bpro_check_log = db::connection('ympimis_2')->table('bpro_check_logs')->insert([
					'employee_id' => $request->get('employee_id'),
					'tag' => $request->get('tag'),
					'material_number' => $request->get('material_number'),
					'quantity' => $request->get('quantity'),
					'location' => $request->get('loc'),
					'operator_id' => $request->get('operator_id'),
					'processing_time' => $request->get('processing_time'),
					'remark' => 'NG',
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				]);				

				$bpro_details = db::connection('ympimis_2')->table('bpro_details')->insert([
					'last_check' => $request->get('employee_id'),
					'tag' => $tag,
					'material_number' => $request->get('material_number'),
					'quantity' => $request->get('quantity'),
					'location' => $request->get('loc'),
					'work_station' => strtoupper($request->get('loc')),
					'remark' => 'NG',
					'started_at' => date('Y-m-d H:i:s'),
					'finished_at' => date('Y-m-d H:i:s'),
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				]);				

				$bpro_inventory = db::connection('ympimis_2')->table('bpro_inventories')->updateOrInsert(
					['tag' => $tag],
					['material_number' => $request->get('material_number'),
					'location' => $request->get('loc'),
					'quantity' => $request->get('quantity'),
					// 'barcode_number' => $request->get('barcode_number'),
					'last_check' => $request->get('employee_id'),
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')]
				);				

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
				$bpro_materials = DB::connection('ympimis_2')->table('bpro_materials')
				->where('material_number',$request
				->get('material_number'))
				->first();

				$bpro_flow = DB::connection('ympimis_2')->table('bpro_flows')->where('category',$bpro_materials->material_category)->where('material_type',$bpro_materials->material_type)->where('flow',$request->get('loc'))->orderby('ordering','asc')->first();
				$next = '';
				if ($bpro_flow) {
					$bpro_next_flow = DB::connection('ympimis_2')->table('bpro_flows')->where('category',$bpro_materials->material_category)->where('material_type',$bpro_materials->material_type)->where('ordering',$bpro_flow->ordering+1)->orderby('ordering','asc')->first();
					$next = $bpro_next_flow->flow;
				}

				$bpro_check_log = db::connection('ympimis_2')->table('bpro_check_logs')->insert([
					'employee_id' => $request->get('employee_id'),
					'tag' => $request->get('tag'),
					'material_number' => $request->get('material_number'),
					'quantity' => $request->get('quantity'),
					'location' => $request->get('loc'),
					'operator_id' => $request->get('operator_id'),
					'processing_time' => $request->get('processing_time'),
					'remark' => 'OK',
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				]);
					// $welding_check_log->save();
				// }

				// $welding_inventory = WeldingInventory::updateOrCreate(
				$bpro_inventory = db::connection('ympimis_2')->table('bpro_inventories')->updateOrInsert(
					['tag' => $tag],
					['material_number' => $request->get('material_number'),
					'location' => $request->get('loc'),
					'location_next' => $next,
					'quantity' => $request->get('quantity'),
					// 'barcode_number' => $request->get('barcode_number'),
					'last_check' => $request->get('employee_id'),
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')]
				);

				$bpro_details = db::connection('ympimis_2')->table('bpro_details')->insert([
					'last_check' => $request->get('employee_id'),
					'tag' => $tag,
					'material_number' => $request->get('material_number'),
					'quantity' => $request->get('quantity'),
					'location' => $request->get('loc'),
					'work_station' => strtoupper($request->get('loc')),
					'remark' => 'OK',
					'started_at' => date('Y-m-d H:i:s'),
					'finished_at' => date('Y-m-d H:i:s'),
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				]);

				$bpro_queue = db::connection('ympimis_2')->table('bpro_queues')->where('material_number',$request->get('material_number'))->where('location',$request->get('loc'))->first();
				if ($bpro_queue) {
					$delete = db::connection('ympimis_2')->table('bpro_queues')->where('material_number',$request->get('material_number'))->where('location',$request->get('loc'))->orderby('created_at','desc')->delete();
				}

				$insert_queue = db::connection('ympimis_2')->table('bpro_queues')->insert([
					'material_number' => $request->get('material_number'),
					'location' => $next,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				]);

				// if($request->get('loc') != 'hsa-visual-sx'){
				// 	if($request->get('kensa_id') != null){
				// 		$delete = db::connection('welding')
				// 		->table('t_kensa')
				// 		->where('kensa_id', $request->get('kensa_id'))
				// 		->delete();
				// 	}
				// }

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

	public function inputBproRework(Request $request){
		// $welding_rework_log = new WeldingReworkLog([

		try{
			// $welding_rework_log->save();

			$ngs = [];

			if ($request->get('ng')) {
				foreach ($request->get('ng') as $ng) {
				
					array_push($ngs, $ng[0].'_'.$ng[1]);
				}
			}

			$bpro_rework_log = db::connection('ympimis_2')->table('bpro_rework_logs')->insert([
				'employee_id' => $request->get('employee_id'),
				'tag' => $request->get('tag'),
				'material_number' => $request->get('material_number'),
				'quantity' => $request->get('quantity'),
				'processing_time' => $request->get('processing_time'),
				'ng_name' => join(',',$ngs),
				'location' => $request->get('loc'),
				'started_at' => $request->get('started_at'),
				'operator_id' => $request->get('operator_id'),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			]);

			$bpro_details = db::connection('ympimis_2')->table('bpro_details')->insert([
				'last_check' => $request->get('employee_id'),
				'tag' => $request->get('tag'),
				'material_number' => $request->get('material_number'),
				'quantity' => $request->get('quantity'),
				'location' => $request->get('loc'),
				'work_station' => strtoupper($request->get('loc')),
				'remark' => 'Rework',
				'started_at' => date('Y-m-d H:i:s'),
				'finished_at' => date('Y-m-d H:i:s'),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			]);


			$response = array(
				'status' => true,
				'message' => 'Waktu pengecekan material rework berhasil tercatat'
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

	
	//Mei yong guo
	// public function indexDisplayProductionResult($id){

	// 	if($id == 'sx'){
	// 		$title = 'Body Parts Process Production Result Saxophone';
	// 		$title_jp = '??';
	// 		$locations = $this->location_sx;
	// 	}		
	// 	if($id == 'fl'){
	// 		$title = 'Body Parts Process Production Result Flute';
	// 		$title_jp = '??';
	// 		$locations = $this->location_fl;
	// 	}

	// 	return view('processes.bodyprocess.display.production_result', array(
	// 		'title' => $title,
	// 		'title_jp' => $title_jp,
	// 		'id' => $id,
	// 		'locations' => $locations
	// 	))->with('page', 'Production Result');
	// }

	// public function fetchDisplayProductionResult(Request $request){
	// 	// $loc = $request->get('id');
	// 	$tgl="";
	// 	if(strlen($request->get('tgl')) > 0){
	// 	  $tgl = date('Y-m-d',strtotime($request->get('tgl')));
	// 	  $jam = date('Y-m-d H:i:s',strtotime($request->get('tgl').date('H:i:s')));
	// 	  if ($jam > date('Y-m-d',strtotime($tgl)).' 00:00:01' && $jam < date('Y-m-d',strtotime($tgl)).' 02:00:00' && $tgl == date('Y-m-d',strtotime($tgl))) {
	// 	    $nextday =  date('Y-m-d', strtotime($tgl));
	// 	    $yesterday = date('Y-m-d',strtotime($tgl." -1 days"));
	// 	  }else{
	// 	    $nextday =  date('Y-m-d', strtotime($tgl . " +1 days"));
	// 	    $yesterday = date('Y-m-d',strtotime($tgl));
	// 	  }
	// 	}else{
	// 	  $tgl = date("Y-m-d");
	// 	  $jam = date('Y-m-d H:i:s');
	// 	  if ($jam > date('Y-m-d',strtotime($tgl)).' 00:00:01' && $jam < date('Y-m-d',strtotime($tgl)).' 02:00:00') {
	// 	    $nextday = date('Y-m-d');
	// 	    $yesterday = date('Y-m-d',strtotime("-1 days"));
	// 	  }else{
	// 	    $nextday = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
	// 	    $yesterday = date('Y-m-d');
	// 	  }
	// 	}

	// 	$tanggal = "DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$yesterday."' and";

	// 	$addlocation = "";
	// 	if($request->get('location') != null) {
	// 		$locations = explode(",", $request->get('location'));
	// 		$location = "";

	// 		for($x = 0; $x < count($locations); $x++) {
	// 			$location = $location."'".$locations[$x]."'";
	// 			if($x != count($locations)-1){
	// 				$location = $location.",";
	// 			}
	// 		}
	// 		$addlocation = "and l.location in (".$location.") ";
	// 	}		

	// 	//query here
	// 	if ($location == 'fl') {
			
			
			
	// 	}
		

	// 	$location = "";
	// 	if($request->get('location') != null) {
	// 		$locations = explode(",", $request->get('location'));
	// 		for($x = 0; $x < count($locations); $x++) {
	// 			$location = $location." ".$locations[$x]." ";
	// 			if($x != count($locations)-1){
	// 				$location = $location."&";
	// 			}
	// 		}
	// 	}else{
	// 		$location = "";
	// 	}
	// 	$location = strtoupper($location);

	// 	$response = array(
	// 		'status' => true,			
	// 		'key' => $key,
	// 		'model' => $model,			
	// 		'title' => $location
	// 	);
	// 	return Response::json($response);
	// }

	// public function fetchDisplayProductionResult2(Request $request)
	// {
	// 	try {			
	// 		$tgl="";
	// 		if(strlen($request->get('tgl')) > 0){
	// 		  $tgl = date('Y-m-d',strtotime($request->get('tgl')));
	// 		  $jam = date('Y-m-d H:i:s',strtotime($request->get('tgl').date('H:i:s')));
	// 		  if ($jam > date('Y-m-d',strtotime($tgl)).' 00:00:01' && $jam < date('Y-m-d',strtotime($tgl)).' 02:00:00' && $tgl == date('Y-m-d',strtotime($tgl))) {
	// 		    $nextday =  date('Y-m-d', strtotime($tgl));
	// 		    $yesterday = date('Y-m-d',strtotime($tgl." -1 days"));
	// 		  }else{
	// 		    $nextday =  date('Y-m-d', strtotime($tgl . " +1 days"));
	// 		    $yesterday = date('Y-m-d',strtotime($tgl));
	// 		  }
	// 		}else{
	// 		  $tgl = date("Y-m-d");
	// 		  $jam = date('Y-m-d H:i:s');
	// 		  if ($jam > date('Y-m-d',strtotime($tgl)).' 00:00:01' && $jam < date('Y-m-d',strtotime($tgl)).' 02:00:00') {
	// 		    $nextday = date('Y-m-d');
	// 		    $yesterday = date('Y-m-d',strtotime("-1 days"));
	// 		  }else{
	// 		    $nextday = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
	// 		    $yesterday = date('Y-m-d');
	// 		  }
	// 		}

	// 		$tanggal = "DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$yesterday."' and";

	// 		$addlocation = "";
	// 		$addlocation2 = "";
	// 		if ($request->get('location') != '') {
	// 			$addlocation = "and location = '".$request->get('location')."'";				
	// 			$addlocation2 = "WHERE bpro_materials.material_category = '".strtoupper(explode('-', $request->get('location'))[0])."'";
	// 		}

	// 		// $addlocation = "";
	// 		// if($request->get('location') != null) {
	// 		// 	$locations = explode(",", $request->get('location'));
	// 		// 	$location = "";

	// 		// 	for($x = 0; $x < count($locations); $x++) {
	// 		// 		$location = $location."'".$locations[$x]."'";
	// 		// 		if($x != count($locations)-1){
	// 		// 			$location = $location.",";
	// 		// 		}
	// 		// 	}
	// 		// 	$addlocation = "and location in (".$location.") ";
	// 		// }

	// 		$material = DB::connection('ympimis_2')->SELECT("SELECT
	// 			bpro_materials.material_number,
	// 			models.model,
	// 			models.`key`,
	// 			models.hpl 
	// 		FROM
	// 			bpro_materials
	// 			LEFT JOIN (
	// 			SELECT
	// 				material_number,
	// 				`key`,
	// 				model,
	// 				hpl 
	// 			FROM
	// 				ympimis.materials 
	// 			WHERE
	// 				( hpl = 'BPRO' ) 
	// 				-- OR ( issue_storage_location = 'SXA1' AND hpl = 'BPRO' )
	// 				-- 	OR ( hpl = 'BPRO' )
	// 			GROUP BY
	// 				material_number,
	// 				`key`,
	// 				model,
	// 				hpl 
	// 			ORDER BY
	// 				`key` ASC,
	// 				material_number ,
	// 				model 
	// 			) AS models ON models.material_number = bpro_materials.material_number ".$addlocation2);

	// 		$prod_result = DB::connection('ympimis_2')->select("SELECT
	// 			a.material_number,
	// 			sum( a.quantity ) AS quantity,
	// 			a.shift 
	// 		FROM
	// 			(
	// 			SELECT
	// 				b.*,
	// 				tags.quantity 
	// 			FROM
	// 				(
	// 				SELECT
	// 					tag,
	// 					material_number,
	// 					3 AS shift 
	// 				FROM
	// 					bpro_details 
	// 				WHERE
	// 					created_at BETWEEN '".$yesterday." 00:00:00' 
	// 					AND '".$yesterday." 07:00:00'
	// 					".$addlocation."
	// 					UNION ALL
	// 				SELECT
	// 					tag,
	// 					material_number,
	// 					1 AS shift 
	// 				FROM
	// 					bpro_details 
	// 				WHERE
	// 					created_at BETWEEN '".$yesterday." 06:00:00' 
	// 					AND '".$yesterday." 16:00:00' 
	// 					".$addlocation."
	// 					UNION ALL
	// 				SELECT
	// 					tag,
	// 					material_number,
	// 					2 AS shift 
	// 				FROM
	// 					bpro_details 
	// 				WHERE
	// 					created_at BETWEEN '".$yesterday." 16:00:00' 
	// 					AND '".$nextday." 01:00:00' 
	// 					".$addlocation."
	// 					UNION ALL
	// 				SELECT
	// 					tag,
	// 					material_number,
	// 					3 AS shift 
	// 				FROM
	// 					bpro_logs 
	// 				WHERE
	// 					started_at BETWEEN '".$yesterday." 00:00:00' 
	// 					AND '".$yesterday." 07:00:00'
	// 					".$addlocation."
	// 					UNION ALL
	// 				SELECT
	// 					tag,
	// 					material_number,
	// 					1 AS shift 
	// 				FROM
	// 					bpro_logs 
	// 				WHERE
	// 					started_at BETWEEN '".$yesterday." 06:00:00' 
	// 					AND '".$yesterday." 16:00:00' 
	// 					".$addlocation."
	// 					UNION ALL
	// 				SELECT
	// 					tag,
	// 					material_number,
	// 					2 AS shift 
	// 				FROM
	// 					bpro_logs 
	// 				WHERE
	// 					started_at BETWEEN '".$yesterday." 16:00:00' 
	// 					AND '".$nextday." 01:00:00' 
	// 					".$addlocation."
	// 				) b
	// 				JOIN (
	// 				SELECT
	// 					tag,
	// 					bpro_tags.material_number,
	// 					bpro_tags.material_type,
	// 					quantity 
	// 				FROM
	// 					bpro_tags
	// 					JOIN bpro_materials ON bpro_materials.material_number = bpro_tags.material_number 
	// 					AND bpro_materials.material_type = bpro_tags.material_type 
	// 				) AS tags ON tags.tag = b.tag 
	// 			) a 
	// 		GROUP BY
	// 			a.material_number,
	// 			a.shift");

	// 		$prods = [];			

	// 		for ($i=0; $i < count($prod_result); $i++) { 
	// 			$hpl = '';
	// 			$model = '';
	// 			$key = '';
	// 			for ($j=0; $j < count($material); $j++) { 
	// 				if ($prod_result[$i]->material_number == $material[$j]->material_number) {
	// 					$model = $material[$j]->model;
	// 					$key = $material[$j]->key;
	// 					$hpl = $material[$j]->hpl;
	// 				}
	// 			}
	// 			$prod = array(
	// 				'material_number' => $prod_result[$i]->material_number,
	// 				'quantity' => $prod_result[$i]->quantity,
	// 				'shift' => $prod_result[$i]->shift,
	// 				'key' => $key,
	// 				'model' => $model,
	// 				'hpl' => $hpl,
	// 			);
	// 			array_push($prods, $prod);
	// 		}

	// 		$response = array(
	// 			'status' => true,
	// 			'material' => $material,
	// 			'location' => $request->get('location'),
	// 			'prods' => $prods
	// 		);
	// 		return Response::json($response);
	// 	} catch (\Exception $e) {
	// 		$response = array(
	// 			'status' => false,
	// 			'message' => $e->getMessage(),
	// 		);
	// 		return Response::json($response);
	// 	}
	// }

	public function indexBproBoard($loc){

		$startA = '07:00:00';
		$finishA = '16:00:00';
		$startB = '15:55:00';
		$finishB = '00:15:00';
		$startC = '23:30:00';
		$finishC = '07:10:00';

		switch ($loc) {
			case 'washing-fl':
				$title = 'Antrian Washing Flute';
				$title_jp = '??';
				return view('processes.bodyprocess.display.bpro_board_washing', array(
					'title' => $title,
					'title_jp' => $title_jp,
					'loc' => $loc,			
					'location' => 'fl',	
					'category' => 'washing',
					'startA' => $startA,
					'finishA' => $finishA,
					'startB' => $startB,
					'finishB' => $finishB,
					'startC' => $startC,
					'finishC' => $finishC,
				))->with('page', 'FL');
				break;
			
			default:
				$title = 'BPRO Board';
				$title_jp = '??';
				return view('processes.bodyprocess.display.bpro_board_washing', array(
					'title' => $title,
					'title_jp' => $title_jp,
					'loc' => $loc,			
					'location' => 'sax',	
					'category' => 'HPP',
					'startA' => $startA,
					'finishA' => $finishA,
					'startB' => $startB,
					'finishB' => $finishB,
					'startC' => $startC,
					'finishC' => $finishC,
				))->with('page', 'HPP');
				break;
		}		
	}

	// Left Join different table not working
	// public function fetchBproBoard(Request $request){
	// 	$loc = $request->get('loc');
	// 	// $location = $request->get('location');
	// 	$category = $request->get('category');
	// 	$origin_group_code = "";

	// 	if (str_contains($request->get('location'),'sax')) {
	// 		$origin_group_code = '043';
	// 	}else if (str_contains($request->get('location'),'fl')) {
	// 		$origin_group_code = '041';
	// 	}else if (str_contains($request->get('location'),'cl')) {
	// 		$origin_group_code = '042';
	// 	}

	// 	$boards = array();
	// 	$indexCuci1 = 0;

	// 	if ($category != 'CUCI') {
	// 		$work_stations = DB::connection('ympimis_2')->select("SELECT
	// 			bpros.work_station,
	// 			device_number,
	// 			device_type,
	// 			employee_id,
	// 			`name`,
	// 			online_time,
	// 			weld_akan.material_number AS akan_material,
	// 			CONCAT( weld_akan.model, ' ', weld_akan.`key` ) AS akan_desc,
	// 			weld_akan.surface AS akan_surface,
	// 			weld_akan.doing_timestamp AS waktu_akan,
	// 			weld_sedang.material_number AS sedang_material,
	// 			CONCAT( weld_sedang.model, ' ', weld_sedang.`key` ) AS sedang_desc,
	// 			weld_sedang.surface AS sedang_surface,
	// 			weld_sedang.doing_timestamp AS waktu_sedang
	// 		FROM
	// 			bpros
	// 			JOIN (
	// 			SELECT
	// 				work_station,
	// 				device_name,
	// 				bpros.material_number,
	// 				bpros.material_description,
	// 				material_tag,
	// 				ympimis.materials.model,
	// 				ympimis.materials.`key`,
	// 				ympimis.materials.`surface`,
	// 				doing_timestamp
	// 			FROM
	// 				bpros
	// 				LEFT JOIN ympimis.materials ON ympimis.materials.material_number = bpros.material_number 
	// 			WHERE
	// 				device_name LIKE '%Akan%' 
	// 			) AS weld_akan ON weld_akan.work_station = bpros.work_station
	// 			JOIN (
	// 			SELECT
	// 				work_station,
	// 				device_name,
	// 				bpros.material_number,
	// 				bpros.material_description,
	// 				material_tag,
	// 				ympimis.materials.model,
	// 				ympimis.materials.`key`,
	// 				ympimis.materials.`surface`,
	// 				doing_timestamp
	// 			FROM
	// 				bpros
	// 				LEFT JOIN ympimis.materials ON ympimis.materials.material_number = bpros.material_number 
	// 			WHERE
	// 				device_name LIKE '%Sedang%' 
	// 			) AS weld_sedang ON weld_sedang.work_station = bpros.work_station 
	// 		WHERE
	// 			active_status = 'Active' 
	// 			AND device_type = '".$category."' 
	// 			AND location = '".$loc."' 
	// 		GROUP BY
	// 			work_station,
	// 			device_number,
	// 			device_type,
	// 			employee_id,
	// 			`name`,
	// 			online_time 
	// 		ORDER BY
	// 			display_queue
	// 		");
	// 		foreach ($work_stations as $ws) {
	// 			$dt_now = new DateTime();

	// 			$dt_akan = new DateTime($ws->waktu_akan);
	// 			$akan_time = $dt_akan->diff($dt_now);

	// 			$dt_sedang = new DateTime($ws->waktu_sedang);
	// 			$sedang_time = $dt_sedang->diff($dt_now);

	// 			$lists = '';
	// 			$list_antrian = array();

	// 			$lists = DB::connection('ympimis_2')->select("SELECT
	// 					bpro_queues.material_number,
	// 					CONCAT( ympimis.materials.model, ' ', ympimis.materials.`key` ) AS material_description,
	// 					bpro_materials.work_station,
	// 					bpro_materials.material_category,
	// 					bpro_queues.created_at 
	// 				FROM
	// 					bpro_queues
	// 					JOIN ympimis.materials ON bpro_queues.material_number = ympimis.materials.material_number
	// 					JOIN bpro_materials ON bpro_materials.material_number = bpro_queues.material_number 
	// 				WHERE
	// 					bpro_materials.work_station = '".$ws->work_station."'
	// 					AND bpro_queues.location IN (
	// 					'store-fhf-fl',
	// 					'phs-sx',
	// 					'hpp-sx')");

	// 			if (count($lists) > 9) {
	// 				foreach ($lists as $key) {
	// 					if (isset($key)) {
	// 						array_push($list_antrian, '('.$key->material_category.')'.'<br>'.$key->material_number.'<br>'.$key->material_description);
	// 					}else{
	// 						array_push($list_antrian, '<br>');
	// 					}
	// 				}
	// 			}else{
	// 				for ($i=0; $i < 10; $i++) {
	// 					if (isset($lists[$i])) {
	// 						array_push($list_antrian, '('.$lists[$i]->material_category.')'.'<br>'.$lists[$i]->material_number.'<br>'.$lists[$i]->material_description);
	// 					}else{
	// 						array_push($list_antrian, '<br>');
	// 					}
	// 				}
	// 			}

	// 			$board_sedang = '';
	// 			if($ws->sedang_surface != null){
	// 				if($ws->sedang_surface == 'HPP') {
	// 					$board_sedang = $ws->sedang_material.'<br>'.$ws->sedang_desc;
	// 				}else{
	// 					$board_sedang = '('.$ws->sedang_surface.')'.'<br>'.$ws->sedang_material.'<br>'.$ws->sedang_desc;
	// 				}
	// 			}else{
	// 				$board_sedang = '<br>';
	// 			}

	// 			$board_akan = '';		
	// 			if($ws->akan_surface != null){
	// 				$board_akan = '('.$ws->akan_surface.')'.'<br>'.$ws->akan_material.'<br>'.$ws->akan_desc;
	// 			}else{
	// 				$board_akan = '<br>';
	// 			}

	// 			array_push($boards, [
	// 				'ws_name' => $ws->work_station,
	// 				'mesin_name' => 'Sol#'.$ws->device_number,
	// 				'ws' => 'Sol#'.$ws->device_number.'<br>'.$ws->work_station,
	// 				'employee_id' => $ws->employee_id,
	// 				'employee_name' => $ws->name,
	// 				// 'shift' => $ws->shift,
	// 				// 'jam_shift' => $ws->jam_shift,
	// 				'sedang' => $board_sedang,
	// 				'akan' => $board_akan,
	// 				'akan_time' => $akan_time->format('%H:%i:%s'),
	// 				'sedang_time' => $sedang_time->format('%H:%i:%s'),
	// 				'queue_1' => $list_antrian[0],
	// 				'queue_2' => $list_antrian[1],
	// 				'queue_3' => $list_antrian[2],
	// 				'queue_4' => $list_antrian[3],
	// 				'queue_5' => $list_antrian[4],
	// 				'queue_6' => $list_antrian[5],
	// 				'queue_7' => $list_antrian[6],
	// 				'queue_8' => $list_antrian[7],
	// 				'queue_9' => $list_antrian[8],
	// 				'queue_10' => $list_antrian[9],
	// 				'jumlah_urutan' => count($lists)
	// 			]);
	// 		}
	// 	}else{
	// 		$lists = DB::connection('ympimis_2')->select("SELECT
	// 			bpro_queues.material_number,
	// 			CONCAT( ympimis.materials.model, ' ', ympimis.materials.`key` ) AS material_description,
	// 			ympimis.materials.surface,
	// 			bpro_queues.created_at 
	// 		FROM
	// 			bpro_queues
	// 			JOIN ympimis.materials ON bpro_queues.material_number = ympimis.materials.material_number 
	// 		WHERE
	// 			bpro_queues.location = 'washing' 
	// 		ORDER BY
	// 			created_at");
	// 		foreach ($lists as $lists) {
	// 			array_push($boards, [
	// 				'queue' => $lists->material_number.'<br>'.$lists->material_description.'<br>'.$lists->created_at
	// 			]);
	// 			$indexCuci1++;
	// 		}
	// 	}

	// 	$response = array(
	// 		'status' => true,
	// 		'loc' => $loc,
	// 		'boards' => $boards,
	// 	);
	// 	return Response::json($response);
	// }

	//NG RATE
	public function indexNgRate($id){

		if ($id == 'sx') {
            $title = 'NG Rate Saxophone';
            $title_jp = '';
			$locations = $this->location_sx;
        } elseif ($id == 'fl') {
            $title = 'NG Rate Flute';
            $title_jp = '';
			$locations = $this->location_fl;
        } elseif ($id == 'cl') {
            $title = 'NG Rate Clarinet';
            $title_jp = '';
			$locations = $this->location_cl;
        }

		return view('processes.bodyprocess.display.ng_rate', array(
			'title' => 'NG Rate',
			'title_jp' => '不良率',
			'locations' => $locations,
            'id' => $id
		))->with('page', 'Body Parts Process');
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
				$addlocation = "and location like '%".$request->get('id')."%'";
			}

			// if(strlen($request->get('location'))>0){
			// 	$location = explode(",", $request->get('location'));
			// 	$ngs = $ngs->whereIn('welding_ng_logs.location', $location);
			// 	$checks = $checks->whereIn('welding_check_logs.location', $location);
			// }

			// if(strlen($request->get('tanggal'))>0){
			// 	$now = date('Y-m-d', strtotime($request->get('tanggal')));
			// 	// $ngs = $ngs->whereRaw('date(welding_ng_logs.created_at) = "'.$now.'"');
			// 	// $checks = $checks->whereRaw('date(welding_check_logs.created_at) = "'.$now.'"');
			// }
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

			$ng = db::connection('ympimis_2')->select("SELECT
				SUM( quantity ) AS jumlah,
				ng_name,
				SUM( quantity ) / ( SELECT SUM( bpro_check_logs.quantity ) AS total_check FROM bpro_check_logs WHERE deleted_at IS NULL ".$addlocation." AND bpro_check_logs.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$nextday." 02:00:00' ) * 100 AS rate 
			FROM
				bpro_ng_logs 
			WHERE
				created_at BETWEEN '".$yesterday." 06:00:00' 
				AND '".$nextday." 02:00:00' 
				".$addlocation."
			GROUP BY
				ng_name 
			ORDER BY
				jumlah DESC");

			$ngType = db::connection('ympimis_2')->select("
				select rate.`key`, rate.`check`, rate.ng, rate.rate from (
				select c.`key`, c.jml as `check`, COALESCE(ng.jml,0) as ng,(COALESCE(ng.jml,0)/c.jml*100) as rate 
				from 
				(select mt.`key`, sum(w.quantity) as jml from bpro_check_logs w
				left join ympimis.materials mt on mt.material_number = w.material_number
				where w.deleted_at is null
				".$addlocation."
				and w.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$nextday." 02:00:00'
				GROUP BY mt.`key`) c

				left join

				(select mt.`key`, sum(w.quantity) as jml from bpro_ng_logs w
				left join ympimis.materials mt on mt.material_number = w.material_number
				where w.deleted_at is null
				".$addlocation."
				and w.created_at BETWEEN '".$yesterday." 06:00:00' 
				AND '".$nextday." 02:00:00' 
				GROUP BY mt.`key`) ng

				on c.`key` = ng.`key`) rate
				where rate.ng != '0'
				ORDER BY rate.rate desc"
			);


			$dateTitle = date("d M Y", strtotime($yesterday));

			// $ngs = $ngs->get();
			// $checks = $checks->get();



			// COALESCE((SELECT sum(quantity) from welding_logs where deleted_at is null ".$addlocation." and welding_logs.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$nextday." 02:00:00'),0) as total_ok,

			$datastat = db::connection('ympimis_2')->select("select 
				COALESCE(SUM(bpro_check_logs.quantity),0) as total_check,

				COALESCE(
				SUM(bpro_check_logs.quantity)
				-
				(Select SUM(quantity) from bpro_ng_logs where deleted_at is null ".$addlocation." and bpro_ng_logs.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$nextday." 02:00:00'),0) as total_ok,

				COALESCE((select sum(quantity) from bpro_ng_logs where deleted_at is null ".$addlocation." and bpro_ng_logs.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$nextday." 02:00:00'),0) as total_ng,

				COALESCE((select sum(quantity) from bpro_ng_logs where deleted_at is null ".$addlocation." and bpro_ng_logs.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$nextday." 02:00:00')
				/ 
				(Select SUM(quantity) from bpro_check_logs where deleted_at is null ".$addlocation." and bpro_check_logs.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$nextday." 02:00:00') * 100,0) as ng_rate 

				from bpro_check_logs 
				where bpro_check_logs.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$nextday." 02:00:00' ".$addlocation." and deleted_at is null ");

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
				// 'checks' => $checks,
				// 'ngs' => $ngs,
				'ng' => $ng,
				'ngkey' => $ngType,
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
				$detail = DB::connection('ympimis_2')->select("SELECT
					bpro_ng_logs.*,
					materials.material_description
				FROM
					`bpro_ng_logs`
					LEFT JOIN (
					SELECT					
						bpro_tags.tag,
						bpro_materials.material_description
					FROM
						bpro_tags
						JOIN bpro_materials ON bpro_materials.material_number = bpro_tags.material_number
						AND bpro_materials.material_type = bpro_tags.material_type
					) materials ON materials.tag = bpro_ng_logs.tag 
				WHERE
					created_at >= '".$yesterday." 06:00:00' 
					AND created_at <= '".$nextday." 02:00:00' 
					".$addlocation."
					AND ng_name = '".$request->get('ng_name')."'");

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

	public function indexOpRate($id){

		switch ($id) {
			case 'fl':
				$title = 'NG Rate by Operator Flute';
				$title_jp = '';
				$locations = $this->location_fl;
				break;

			case 'sx':
				$title = 'NG Rate by Operator Saxophone';
				$title_jp = '';
				$locations = $this->location_sx;
				break;
			
			default:
				# code...
				break;
		}
		
		if ($id == 'sx') {
			$title = 'NG Rate by Operator Saxophone';
			$title_jp = '';
			$locations = $this->location_sx;
        } elseif ($id == 'fl') {
        	$title = 'NG Rate by Operator Flute';
			$title_jp = '';
			$locations = $this->location_fl;
		}        

		return view('processes.bodyprocess.display.op_rate', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'id' => $id,
			'locations' => $locations
		))->with('page', 'Body Parts Process');
	}

	public function fetchOpRate(Request $request){
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
			$addlocation = "and location like '%".$request->get('id')."%'";
		}

		if(strlen($request->get('tanggal'))>0){
			$now = date('Y-m-d', strtotime($request->get('tanggal')));
		}

		$ng_target = db::connection('ympimis_2')->table("bpro_ng_targets")
		->where('location', '=', 'wld')
		->where('target_name', '=', 'NG Rate')
		->select('target')
		->first();

		$ng_rate = db::connection('ympimis_2')->select("
			SELECT
				employee_id,
				`name`,
				IF(shift = '','A',shift) as shift,
				COALESCE ( checks.checks, 0 ) AS `check`,
				COALESCE ( ng.ng, 0 ) AS ng,
				COALESCE ( ROUND(( COALESCE ( ng.ng, 0 )/ checks.checks * 100 ), 1 ), 0 ) AS rate 
			FROM
				bpro_operators
				LEFT JOIN ( SELECT operator_id, sum( quantity ) AS checks FROM bpro_check_logs WHERE date( created_at ) = '".$now."' ".$addlocation." GROUP BY operator_id ) AS checks ON checks.operator_id = bpro_operators.employee_id
				LEFT JOIN ( SELECT operator_id, sum( quantity ) AS ng FROM bpro_ng_logs WHERE date( created_at ) = '".$now."' ".$addlocation." GROUP BY operator_id ) AS ng ON ng.operator_id = bpro_operators.employee_id 
			WHERE
				location = '".$request->get('id')."' 
				OR location = 'kensa' 
			ORDER BY
				shift");

		// $target = db::connection('ympimis_2')->select("select eg.`shift` as `group`, eg.employee_id, eg.`name`, ng.material_number, concat(m.model, ' ', m.`key`) as `key`, ng.ng_name, ng.quantity, ng.created_at,ng.check,ng.check_time from welding_operators eg left join 
		// 	(select * from welding_ng_logs where deleted_at is null ".$addlocation." and remark in 
		// 	(select remark.remark from
		// 	(select operator_id, max(remark) as remark from welding_ng_logs where DATE(welding_time) ='".$now."' ".$addlocation." group by operator_id) 
		// 	remark)
		// 	) ng 
		// 	on eg.employee_id = ng.operator_id
		// 	left join materials m on m.material_number = ng.material_number
		// 	where eg.location like '%".$request->get('id')."%'
		// 	order by eg.`shift`, eg.`name` asc");

		$operator = db::connection('ympimis_2')->select("select g.`shift` as `group`, g.employee_id, g.`name` from bpro_operators g where g.location like '%".$request->get('id')."%' order by g.`shift`, g.`name` asc");

		// $dateTitle = date("d M Y", strtotime($now));

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
			'ng_rate' => $ng_rate,
			// 'target' => $target,
			'operator' => $operator,
			// 'ng_target' => $ng_target->target,
			'dateTitle' => $now,
			'title' => $location
		);
		return Response::json($response);
	}

	public function fetchOpRateDetail(Request $request){
		$tgl = $request->get('tgl');
		// $nik = (explode(" - ",$request->get('nama')));

		$nama = EmployeeSync::where('employee_id','=',$request->get('employee_id'))->select('name')->first();

		$good = db::connection('ympimis_2')->select("SELECT
				bpro_check_logs.*,
				ympimis.materials.model,
				ympimis.materials.`key` 
			FROM
				bpro_check_logs
				JOIN ympimis.materials ON ympimis.materials.material_number = bpro_check_logs.material_number 
			WHERE
				bpro_check_logs.operator_id = '".$request->get('employee_id')."' 
				AND DATE( bpro_check_logs.created_at ) = '".$tgl."'
				AND remark = 'OK'");

		$ng = db::connection('ympimis_2')->select("SELECT
				bpro_ng_logs.*,
				ympimis.materials.model,
				ympimis.materials.`key` 
			FROM
				bpro_ng_logs
				JOIN ympimis.materials ON ympimis.materials.material_number = bpro_ng_logs.material_number 
			WHERE
				bpro_ng_logs.operator_id = '".$request->get('employee_id')."' 
				AND DATE( bpro_ng_logs.created_at ) = '".$tgl."'");

		$cek = DB::connection('ympimis_2')->SELECT("SELECT
				bpro_check_logs.*,
				ympimis.materials.model,
				ympimis.materials.`key` 
			FROM
				bpro_check_logs
				JOIN ympimis.materials ON ympimis.materials.material_number = bpro_check_logs.material_number 
			WHERE
				bpro_check_logs.operator_id = '".$request->get('employee_id')."' 
				AND DATE( bpro_check_logs.created_at ) = '".$tgl."' ");

		$emp = EmployeeSync::where('end_date',null)->get();

		$response = array(
			'status' => true,
			'nik' => $request->get('employee_id'),
			'nama' => $nama->name,
			'good' => $good,
			'ng' => $ng,
			'emp' => $emp,
			'cek' => $cek,
		);
		return Response::json($response);
	}

	public function indexReportNG($id){

		switch ($id) {
			case 'fl':
				$title = 'Not Good Record Flute';
				$title_jp = '不良内容';
				$locations = $this->location_fl;
				break;

			case 'sx':
				$title = 'Not Good Record Saxophone';
				$title_jp = '不良内容';
				$locations = $this->location_sx;	
				break;
			
			default:
				$title = '??';
				$title_jp = '??';
				$locations = $this->location_sx;
				break;
		}		

		return view('processes.bodyprocess.report.not_good', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'id' => $id,
			'locations' => $locations
		))->with('head', 'Body Parts Process');
	}

	public function fetchReportNG(Request $request){		

		$materials = db::table('materials')->select('material_number', 'material_description' ,'model', 'key')->get();
		
		$report = DB::connection('ympimis_2')->table('bpro_ng_logs')
		->select('bpro_ng_logs.*', 'bpro_operators.name', 'bpro_operators.employee_id')
		->Join('bpro_operators', 'bpro_operators.employee_id', '=', 'bpro_ng_logs.employee_id')
		->get();

		$temp = [];		
		foreach ($materials as $material) {
			$temp[$material->material_number] = [
				'material_number' => $material->material_number,
				'material_description' => $material->material_description,
				'model' => $material->model,
				'key' => $material->key
			];			
		}
		$materials = $temp;
		
		$reportData = [];
		foreach($report as $row){
			if (isset($materials[$row->material_number])) {
				$materialData = $materials[$row->material_number];				
				$reportData[] = array_merge((array) $row, $materialData);
			}
		}				

		if (strlen($request->get('datefrom')) > 0) {
			$date_from = date('Y-m-d', strtotime($request->get('datefrom')));
			$reportData = array_filter($reportData, function ($value) use ($date_from) {
				return date('Y-m-d', strtotime($value['created_at'])) >= $date_from;
			});
		}
		
		if (strlen($request->get('dateto')) > 0) {
			$date_to = date('Y-m-d', strtotime($request->get('dateto')));
			$reportData = array_filter($reportData, function ($value) use ($date_to) {				
				return date('Y-m-d', strtotime($value['created_at'])) <= $date_to;
			});
		}									

		if ($request->get('location') != null) {
			$location = $request->get('location');
			$reportData = array_filter($reportData, function ($value) use ($location) {						
				return $value['location'] == $location;
			});
		} else {
			// $id = $request->get('id');
			$reportData = array_filter($reportData, function ($value) use ($id) {								
				return strpos($value['location'], $location) !== false;
			});
		}

		// if ($request->get('location') != null) {
		// 	$location = $request->get('location');
		// 	$reportData = array_filter($reportData, function ($value) use ($location) {
		// 		return $value->location === $location;
		// 	});
		// } else {
		// 	$id = $request->get('id');
		// 	$reportData = array_filter($reportData, function ($value) use ($id) {
		// 		return strpos($value->location, $id) !== false;
		// 	});
		// }

		return Response::json([
			'status' => 200,
			'value' => $reportData
		]);				
	}

	//Resume
	public function indexTarget()
	{
		$fys = db::select("select DISTINCT fiscal_year from weekly_calendars");

		return view('processes.bodyprocess.target', array(
			'title' => 'Target',
			'title_jp' => '',
			'fys' => $fys,
		))->with('page', 'Target');
	}

	public function indexReportResumeNg($id)
    {
        $fys = db::select("select DISTINCT fiscal_year from weekly_calendars");

		switch ($id) {
			case 'fheadfinish-kensa-fl':
				$title = 'Flute Head Finish ';
				$title_jp = '??';			
				$group_loc = 'fl';
				break;

			case 'fbodyfinish-kensa-fl':
				$title = 'Flute Body Finish ';
				$title_jp = '??';
				$group_loc = 'fl';
				break;

			case 'ffootfinish-kensa-fl':
				$title = 'Flute Foot Finish ';
				$title_jp = '??';
				$group_loc = 'fl';
				break;			

			case 'fbody-kensa-fl-pipe':
				$title = 'Flute Body Foot Pipe ';
				$title_jp = '??';
				$group_loc = 'fl';
				break;

			case 'ffoot-kensa-fl-pipe':
				$title = 'Flute Body Foot Pipe ';
				$title_jp = '??';
				$group_loc = 'fl';
				break;

			case 'fbody-kensa-fl-onko':
				$title = 'Flute Body Foot Onko ';
				$title_jp = '??';
				$group_loc = 'fl';
				break;			
				
			case 'ffoot-kensa-fl-onko':
				$title = 'Flute Body Foot Onko ';
				$title_jp = '??';
				$group_loc = 'fl';
				break;			

			case 'asbody-kensa-sx':
				$title = 'Saxophone AS Body ';
				$title_jp = '??';
				$group_loc = 'sx';
				break;			

			case 'asbell-kensa-sx':
				$title = 'Saxophone AS Bell ';
				$title_jp = '??';
				$group_loc = 'sx';
				break;			

			case 'asbow-kensa-sx':
				$title = 'Saxophone AS Bow ';
				$title_jp = '??';
				$group_loc = 'sx';
				break;			

			case 'asneck-kensa-sx':
				$title = 'Saxophone AS Neck ';
				$title_jp = '??';
				$group_loc = 'sx';
				break;			

			case 'tsneck-kensa-sx':
				$title = 'Saxophone TS Neck ';
				$title_jp = '??';
				$group_loc = 'sx';
				break;			

			case 'bellyds-kensa-sx':
				$title = 'Saxophone Bell YDS ';
				$title_jp = '??';
				$group_loc = 'sx';
				break;			

			case 'bff-kensa-sx':
				$title = 'Works100 Saxophone Body Finish ';
				$title_jp = '??';
				$group_loc = 'sx';
				break;			
			
			default:
				$title = 'Unknown';
				$title_jp = '??';
				$group_loc = '??';
				break;
		}		

        return view('processes.bodyprocess.report.resume_ng', array(
            'title' =>  $title . ' Resume',
            'title_jp' => '',			
			'loc' => $id,			
			'group_loc' => $group_loc,
            'fys' => $fys,
        ))->with('page', 'NG Report');
    }

	public function fetchNgRateMonthly(Request $request)
    {
		$location = $request->get('loc');

        $fy = strtoupper($request->get('fy'));
		if(strlen($fy) <= 0){
			$this_year = db::table('weekly_calendars')
			->where('week_date', date('Y-m-d'))
			->first();
			$fy = strtoupper($this_year->fiscal_year);
		}       

		$months = db::table('weekly_calendars')
		->where('fiscal_year', $fy)
		->select(db::raw('DATE_FORMAT(week_date, "%Y-%m") AS month'))
		->distinct()
		->orderBy('month', 'ASC')
		->get();					

		
		$ng = DB::connection('ympimis_2')->select("SELECT month AS tgl, ng, `check` AS g FROM ympimis_2.bpro_monthly_resumes WHERE fiscal_year = ? AND location = ?", [$fy, $location]);		
		

		$monthly = array() ;
		foreach ($months as $m) {
			$month = $m->month;
			$month_ng = 0;
			$month_g = 0;
			$month_ng_rate = 0;

			foreach ($ng as $n) {
				if($month == $n->tgl){
					$month_ng = $n->ng;
					$month_g = $n->g;
					// if divided by zero return null $month_ng_rate = $month_ng / $month_g; 
					$month_ng_rate = $month_g == 0 ? null : $month_ng / $month_g;
					
				}
			}

			$monthly[] = array(
				"tgl" => $month,
				"ng" => $month_ng,
				"g" => $month_g,
				"ng_rate" => $month_ng_rate
			);
		}						

        $target = db::connection('ympimis_2')->table('bpro_ng_targets')			
			->where('location', $location)
			->where('target_name', 'NG Rate')
			->first();			        		

        $response = array(
            'status' => true,
            'monthly' => $monthly,
            'target' => $target,
            'fy' => $fy,
        );
        return Response::json($response);
    }	

	public function fetchNgRateWeekly(Request $request)
    {
		$location = $request->get('loc');

        $bulan = "";
        $bulanText = "";
        if (strlen($request->get('bulan')) > 0) {
            $bulan = date('Y-m', strtotime('01-' . $request->get('bulan')));
            $bulanText = date('M Y', strtotime('01-' . $request->get('bulan')));
        } else {
            $bulan = date('Y-m');
            $bulanText = date('M Y');
        }      						

        $fy = strtoupper($request->get('fy'));
		if(strlen($fy) <= 0){
			$this_year = db::table('weekly_calendars')
			->where('week_date', date('Y-m-d'))
			->first();
			$fy = strtoupper($this_year->fiscal_year);
		}       	

		$weeks = DB::table('weekly_calendars')
                ->where('week_date', 'like', $bulan . '%')
                ->select(DB::raw('week_name AS tgl','DATE_FORMAT(week_date, "%Y-%m") AS week'))        
				->distinct()								       
                ->orderBy('week_date', 'ASC')
                ->get();
				

		$ng = DB::connection('ympimis_2')->select("SELECT week AS tgl, ng, `check` AS g FROM ympimis_2.bpro_weekly_resumes WHERE fiscal_year = ? AND location = ?", [$fy, $location]);							
		

		$weekly = array() ;	
		foreach ($weeks as $w) {
			$week = $w->tgl;
			$week_ng = 0;
			$week_g = 0;
			$week_ng_rate = 0;

			foreach ($ng as $n) {
				if($week == $n->tgl){
					$week_ng = $n->ng;
					$week_g = $n->g;
					// if divided by zero return null $week_ng_rate = $week_ng / $week_g;
					$week_ng_rate = $week_g == 0 ? null : $week_ng / $week_g;
				}
			}			

			$weekly[] = array(
				"tgl" => $week,
				"ng" => $week_ng,
				"g" => $week_g,
				"ng_rate" => $week_ng_rate
			);						
		}		


        $target = db::connection('ympimis_2')->table('bpro_ng_targets')			
			->where('location', 'bff')
			->where('target_name', 'NG Rate')
			->first();			 

        $response = array(
            'status' => true,
            'weekly' => $weekly,
            'bulanText' => $bulanText,
        );
        return Response::json($response);
    }

	public function fetchNgMonthly(Request $request)
    {
		$location = $request->get('loc');

        $bulan = "";
        $bulanText = "";
        if (strlen($request->get('bulan')) > 0) {
            $bulan = date('Y-m', strtotime('01-' . $request->get('bulan')));
            $bulanText = date('M Y', strtotime('01-' . $request->get('bulan')));
        } else {
            $bulan = date('Y-m');
            $bulanText = date('M Y');
        }

        $ng_alto = db::connection('ympimis_2')->select("SELECT ng.hpl, ng.ng_name, ng.ng, cek.`check` FROM
			(SELECT hpl, ng_name, SUM(ng) as ng FROM bpro_monthly_ng_resumes
			WHERE hpl = 'Alto'
			AND `month` = '" . $bulan . "'
			AND location = '". $location ."'
			GROUP BY hpl, ng_name) AS ng
			LEFT JOIN
			(SELECT remark, SUM(`check`) as `check` FROM bpro_daily_resumes
			WHERE remark = 'Alto'
			AND DATE_FORMAT(date,'%Y-%m') = '" . $bulan . "'
			AND location = '". $location ."'
			GROUP BY remark) AS cek
			ON cek.remark = ng.hpl
			ORDER BY ng.ng DESC");

        $ng_tenor = db::connection('ympimis_2')->select("SELECT ng.hpl, ng.ng_name, ng.ng, cek.`check` FROM
			(SELECT hpl, ng_name, SUM(ng) as ng FROM bpro_monthly_ng_resumes
			WHERE hpl = 'Tenor'
			AND `month` = '" . $bulan . "'
			AND location = '". $location ."'
			GROUP BY hpl, ng_name) AS ng
			LEFT JOIN
			(SELECT remark, SUM(`check`) as `check` FROM bpro_daily_resumes
			WHERE remark = 'Tenor'
			AND DATE_FORMAT(date,'%Y-%m') = '" . $bulan . "'
			AND location = '". $location ."'
			GROUP BY remark) AS cek
			ON cek.remark = ng.hpl
			ORDER BY ng.ng DESC");

        $response = array(
            'status' => true,
            'ng_alto' => $ng_alto,
            'ng_tenor' => $ng_tenor,
            'bulanText' => $bulanText,
			'location' => $location
        );
        return Response::json($response);

    }

	public function fetchNgKeyMonthly(Request $request)
    {

		$location = $request->get('loc');

        $bulan = "";
        $bulanText = "";
        if (strlen($request->get('bulan')) > 0) {
            $bulan = date('Y-m', strtotime('01-' . $request->get('bulan')));
            $bulanText = date('M Y', strtotime('01-' . $request->get('bulan')));
        } else {
            $bulan = date('Y-m');
            $bulanText = date('M Y');
        }

        $ngKey_alto = db::connection('ympimis_2')->select("SELECT hpl, `key`, sum(ng) AS ng FROM bpro_monthly_ng_resumes
			WHERE hpl = 'ASKEY'
			AND location = '" . $location . "'
			AND `month` = '" . $bulan . "'
			GROUP BY hpl, `key`
			ORDER BY ng DESC
			LIMIT 10");

        $ngKey_tenor = db::connection('ympimis_2')->select("SELECT hpl, `key`, sum(ng) AS ng FROM bpro_monthly_ng_resumes
			WHERE hpl = 'TSKEY'
			AND location = '" . $location . "'
			AND `month` = '" . $bulan . "'
			GROUP BY hpl, `key`
			ORDER BY ng DESC
			LIMIT 10");

        $ngKey_alto_detail = db::connection('ympimis_2')->select(" SELECT hpl, `key`, ng_name, ng FROM bpro_monthly_ng_resumes
			WHERE hpl = 'ASKEY'
			AND location = '" . $location . "'
			AND `month` = '" . $bulan . "'");

        $ngKey_tenor_detail = db::connection('ympimis_2')->select(" SELECT hpl, `key`, ng_name, ng FROM bpro_monthly_ng_resumes
			WHERE hpl = 'TSKEY'
			AND location = '" . $location . "'
			AND `month` = '" . $bulan . "'");

        $response = array(
            'status' => true,
            'ngKey_alto' => $ngKey_alto,
            'ngKey_tenor' => $ngKey_tenor,
            'ngKey_alto_detail' => $ngKey_alto_detail,
            'ngKey_tenor_detail' => $ngKey_tenor_detail,
            'bulanText' => $bulanText,
        );
        return Response::json($response);
    }

	public function fetchNgDaily(Request $request)
    {
		$location = $request->get('loc');
        
        $bulan = "";
        $bulanText = "";
        if (strlen($request->get('bulan')) > 0) {
            $bulan = date('Y-m', strtotime('01-' . $request->get('bulan')));
            $bulanText = date('M Y', strtotime('01-' . $request->get('bulan')));
        } else {
            $bulan = date('Y-m');
            $bulanText = date('M Y');
        }      						

		$days = DB::table('weekly_calendars')                
                ->whereMonth('week_date', Carbon::parse($bulan)->month)
                ->whereYear('week_date', Carbon::parse($bulan)->year)
                ->select(DB::raw('week_Date AS week_date'))                                
                ->get();
		
				$ng = DB::connection('ympimis_2')->select("SELECT date AS tgl, ng, `check` AS g, remark AS hpl FROM ympimis_2.bpro_daily_resumes WHERE DATE_FORMAT(date, '%Y-%m') = ? AND location = ? ORDER BY date", [$bulan, $location]);
		
		$daily = array();
		foreach ($days as $day) {
			$tgl = $day->week_date;
			foreach ($ng as $n) {
				if ($n->tgl == $tgl) {
					$daily[] = array(
						'tgl' => $tgl,
						'ng' => $n->ng,
						'g' => $n->g,
						'hpl' => $n->hpl,
						'ng_rate' => $n->g == 0 ? 0 : round($n->ng/$n->g*100, 2),				
					);
				}
			}
		}

        $response = array(
            'status' => true,
            'daily' => $daily,
            'bulan' => $bulan,
            'bulanText' => $bulanText,
        );
        return Response::json($response);

    }	
}