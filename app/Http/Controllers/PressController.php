<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\ActivityList;
use App\MpProcess;
use App\MpMachine;
use App\MpRecordProd;
use App\MpKanagata;
use App\MaterialPlantDataList;
use App\MpKanagataLog;
use App\MpTroubleLog;
use App\CodeGenerator;
use Response;
use DataTables;
use Excel;
use App\User;
use File;
use DateTime;
use Illuminate\Support\Arr;
use App\OriginGroup;

class PressController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
      $this->mesin = ['Amada 1',
      			'Amada 2',
      			'Amada 3',
      			'Amada 4',
      			'Amada 5',
      			'Amada 6',
      			'Amada 7'];
    }

    public function indexMasterKanagata()
    {
    	$title = 'Master Kanagata';
		$title_jp = '金型マスター';

		$product = OriginGroup::get();
        $product2 = OriginGroup::get();

		$mpdl = MaterialPlantDataList::get();
        $mpdl2 = MaterialPlantDataList::get();

		return view('press.master_kanagata', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'mpdl' => $mpdl,
            'mpdl2' => $mpdl2,
			'product' => $product,
            'product2' => $product2,
		))->with('page', 'Master Kanagata');
    }

    public function fetchMasterKanagata()
    {
    	$lists = MpKanagata::get();

		$response = array(
			'status' => true,
			'lists' => $lists
		);
		return Response::json($response);
    }

    public function addKanagata(Request $request)
	{
		$material = MaterialPlantDataList::where('material_number',$request->get('material_number'))->first();

		$material_description = $material->material_description;

		$lists = DB::table('mp_kanagatas')
		->insert([
			'material_number' => $request->get('material_number'),
			'material_description' => $material_description,
			'material_name' => $request->get('material_name'),
			'process' => 'Forging',
			'product' => $request->get('product'),
			'part' => $request->get('part'),
			'remark' => 'Press',
			'punch_die_number' => $request->get('punch_die_number'),
			'created_by' => Auth::id()]);

		$response = array(
			'status' => true
		);
		return Response::json($response);
	}

	public function destroyKanagata($id)
	{
		$mp_kanagata = MpKanagata::find($id);
      	$mp_kanagata->delete();

		return redirect('index/press/master_kanagata')->with('status', 'Kanagata has been deleted.');
	}

	public function getKanagata(Request $request)
	{
		$list = MpKanagata::find($request->get('id'));

		$response = array(
			'status' => true,
			'lists' => $list
		);
		return Response::json($response);
	}

	public function updateKanagata(Request $request)
	{
		$material = MaterialPlantDataList::where('material_number',$request->get('material_number'))->first();

		$material_description = $material->material_description;

		$kanagata = MpKanagata::find($request->get('id'));
		$kanagata->material_number = $request->get('material_number');
		$kanagata->material_description = $material_description;
		$kanagata->material_name = $request->get('material_name');
		$kanagata->part = $request->get('part');
		$kanagata->punch_die_number = $request->get('punch_die_number');
		$kanagata->product = $request->get('product');
		$kanagata->save();
		
		$response = array(
			'status' => true
		);
		return Response::json($response);
	}

	public function scanPressOperator(Request $request){

		$nik = $request->get('employee_id');

		if(strlen($nik) > 9){
			$nik = substr($nik,0,9);
		}

		$employee = db::table('employees')->where('employee_id', 'like', '%'.$nik.'%')->first();

		if(count($employee) > 0 ){
			$response = array(
				'status' => true,
				'message' => 'Logged In',
				'employee' => $employee
			);
			return Response::json($response);
		}
		else{
			$response = array(
				'status' => false,
				'message' => 'Employee ID Invalid'
			);
			return Response::json($response);
		}
	}

	public function fetchPressList(Request $request){
		try {
			$lists = DB::SELECT("SELECT
				mp_kanagatas.material_number,
				mp_kanagatas.material_name,
				mp_kanagatas.material_description,
				process,
				-- punch_die_number,
				product 
			FROM
				`mp_kanagatas`
				where process like '%".$request->get('process')."%'
				AND punch_die_number != '0'
				group by 
				mp_kanagatas.material_number,
				mp_kanagatas.material_name,
				mp_kanagatas.material_description,
				process,
				product ");

			$response = array(
				'status' => true,
				'lists' => $lists,
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

	public function fetchTroubleList(Request $request){

		$lists = MpTroubleLog::where('date', '=', $request->get('date'))
		->where('pic', '=', $request->get('pic'))
		->where('material_number', '=', $request->get('material_number'))
		->where('process', '=', $request->get('process'))
		->where('machine', '=', $request->get('machine'))
		->get();

		$response = array(
			'status' => true,
			'lists' => $lists,
		);
		return Response::json($response);
	}

	public function fetchProcess(Request $request){

		$count = MpProcess::where('mp_processes.process_name', '=', $request->get('process'))
		->get();

		$process_desc = '';
		foreach ($count as $count) {
            $process_desc .= '<option value="'.$count->process_desc.'">'.$count->process_desc.'</option>';
        }

		$response = array(
			'status' => true,
			'count' => $count,
			'process_desc' => $process_desc
		);
		return Response::json($response);
	}

	public function fetchMaterialList(Request $request){

		$kanagata_material_number = $request->get('material_number');
		$process = $request->get('process');
		$product = $request->get('product');
		$punch_die_number = $request->get('punch_die_number');
		$material_name = $request->get('material_name');

		$count = MpKanagata::where('material_number', '=', $kanagata_material_number)->where('process', '=', $process)->where('product', '=', $product)
		// ->where('punch_die_number', '=', $punch_die_number)
		->where('material_name', '=', $material_name)
		->first();

		$punch = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', 'PUNCH%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->get();

		$punch_first = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', 'PUNCH%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->first();

		$punch_data = '';
		foreach ($punch as $punch) {
            // $punch_data .= '<option value="'.$punch->punch_die_number.'">'.$punch->punch_die_number.'</option>';
            $punch_data = $punch->punch_die_number;
        }

		$dies = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', '=', 'DIE')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->get();

		$dies_first = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', '=', 'DIE')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->first();

		$dies_data = '';
		foreach ($dies as $dies) {
            // $dies_data .= '<option value="'.$dies->punch_die_number.'">'.$dies->punch_die_number.'</option>';
            $dies_data = $dies->punch_die_number;
        }

        $plate = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', '%PLATE%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->get();

		$plate_first = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', '%PLATE%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->first();

		$plate_data = '';
		foreach ($plate as $plate) {
            // $plate_data .= '<option value="'.$plate->punch_die_number.'">'.$plate->punch_die_number.'</option>';
            $plate_data = $plate->punch_die_number;
        }

        $ppl = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', '%PPL%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->get();

		$ppl_first = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', '%PPL%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->first();

		$ppl_data = '';
		foreach ($ppl as $ppl) {
            // $ppl_data .= '<option value="'.$ppl->punch_die_number.'">'.$ppl->punch_die_number.'</option>';
            $ppl_data = $ppl->punch_die_number;
        }

        $snap = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', '%SNAP%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->get();

        $snap_first = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', '%SNAP%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->first();

		$snap_data = '';
		foreach ($snap as $snap) {
            // $snap_data .= '<option value="'.$snap->punch_die_number.'">'.$snap->punch_die_number.'</option>';
            $snap_data = $snap->punch_die_number;
        }

        $dp = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', '%DP%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->get();

        $dp_first = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', '%DP%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->first();

		$dp_data = '';
		foreach ($dp as $dp) {
            // $dp_data .= '<option value="'.$dp->punch_die_number.'">'.$dp->punch_die_number.'</option>';
            $dp_data = $dp->punch_die_number;
        }

        $dd = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', '%DD%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->get();

        $dd_first = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', '%DD%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->first();

		$dd_data = '';
		foreach ($dd as $dd) {
            // $dd_data .= '<option value="'.$dd->punch_die_number.'">'.$dd->punch_die_number.'</option>';
            $dd_data = $dd->punch_die_number;
        }

        $lower = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', '%LOWER%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->get();

        $lower_first = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', '%LOWER%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->first();

		$lower_data = '';
		foreach ($lower as $lower) {
            // $lower_data .= '<option value="'.$lower->punch_die_number.'">'.$lower->punch_die_number.'</option>';
            $lower_data = $lower->punch_die_number;
        }

        $upper = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', '%UPPER%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->get();

        $upper_first = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', '%UPPER%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->first();

		$upper_data = '';
		foreach ($upper as $upper) {
            // $upper_data .= '<option value="'.$upper->punch_die_number.'">'.$upper->punch_die_number.'</option>';
            $upper_data = $upper->punch_die_number;
        }

        $half = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', '%HALF%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->get();

        $half_first = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', '%HALF%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->first();

		$half_data = '';
		foreach ($half as $half) {
            // $half_data .= '<option value="'.$half->punch_die_number.'">'.$half->punch_die_number.'</option>';
            $half_data = $half->punch_die_number;
        }

        $dinsert = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', '%DINSERT%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->get();

        $dinsert_first = MpKanagata::where('mp_kanagatas.material_number', '=', $kanagata_material_number)
		->where('part', 'like', '%DINSERT%')
		->where('process', '=', $process)
		->where('product', '=', $product)
		->where('material_name', '=', $material_name)
		// ->where('punch_die_number', '=', $punch_die_number)
		->select('mp_kanagatas.punch_die_number')
		->distinct()
		->first();

		$dinsert_data = '';
		foreach ($dinsert as $dinsert) {
            // $dinsert_data .= '<option value="'.$dinsert->punch_die_number.'">'.$dinsert->punch_die_number.'</option>';
            $dinsert_data = $dinsert->punch_die_number;
        }

		$response = array(
			'status' => true,
			'count' => $count,
			'punch' => $punch,
			'dies' => $dies,
			'plate' => $plate,
			'ppl' => $ppl,
			'dp' => $dp,
			'dd' => $dd,
			'snap' => $snap,
			'lower' => $lower,
			'upper' => $upper,
			'half' => $half,
			'dinsert' => $dinsert,
			'punch_first' => $punch_first,
			'dies_first' => $dies_first,
			'plate_first' => $plate_first,
			'ppl_first' => $ppl_first,
			'dp_first' => $dp_first,
			'dd_first' => $dd_first,
			'snap_first' => $snap_first,
			'lower_first' => $lower_first,
			'upper_first' => $upper_first,
			'half_first' => $half_first,
			'dinsert_first' => $dinsert_first,
			'punch_data' => $punch_data,
			'dies_data' => $dies_data,
			'plate_data' => $plate_data,
			'ppl_data' => $ppl_data,
			'dp_data' => $dp_data,
			'dd_data' => $dd_data,
			'snap_data' => $snap_data,
			'lower_data' => $lower_data,
			'upper_data' => $upper_data,
			'half_data' => $half_data,
			'dinsert_data' => $dinsert_data,
		);
		return Response::json($response);
	}

	public function fetchPunch(Request $request){
		$fetch_material_check = MpKanagata::where('material_number',$request->get('material_number'))->first();

		$kanagata_log_punch = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and punch_number = '".$request->get('punch_number')."' and punch_status = 'Running'");
		
		$total_punch = 0;
	      if(count($kanagata_log_punch) == 0){
	      	$total_punch = 0;
	      }else{
	      	foreach ($kanagata_log_punch as $kanagata_log_punch) {
		       $total_punch = $kanagata_log_punch->punch_total;
		    }
	      }

	    $ada_reminder = '';

	    // if ($total_punch != 0 && $fetch_material_check->qty_check != 0) {
	    // 	if ($total_punch % $fetch_material_check->qty_check < 2000) {
	    // 		$ada_reminder = 'REMINDER !!!<br>Saatnya pengambilan sample.';
	    // 	}
	    // }

		$response = array(
			'status' => true,
			'total_punch' => $total_punch,
			'ada_reminder' => $ada_reminder
		);
		return Response::json($response);
	}

	public function fetchDie(Request $request){

		$fetch_material_check = MpKanagata::where('material_number',$request->get('material_number'))->first();

		$kanagata_log_dies = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and die_number = '".$request->get('die_number')."' and die_status = 'Running'");
		$total_die = 0;
	      if(count($kanagata_log_dies) == 0){
	      	$total_die = 0;
	      }else{
	      	foreach ($kanagata_log_dies as $kanagata_log_dies) {
		       $total_die = $kanagata_log_dies->die_total;
		    }
	      }

	    $ada_reminder = '';

	    // if ($total_die != 0 && $fetch_material_check->qty_check != 0) {
	    // 	if ($total_die % $fetch_material_check->qty_check < 2000) {
	    // 		$ada_reminder = 'REMINDER !!!<br>Saatnya pengambilan sample.';
	    // 	}
	    // }

		$response = array(
			'status' => true,
			'total_die' => $total_die,
			'ada_reminder' => $ada_reminder,
		);
		return Response::json($response);
	}

	public function fetchPlate(Request $request){

		$fetch_material_check = MpKanagata::where('material_number',$request->get('material_number'))->first();

		$kanagata_log_plates = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and plate_number = '".$request->get('plate_number')."' and plate_status = 'Running'");
		$total_plate = 0;
	      if(count($kanagata_log_plates) == 0){
	      	$total_plate = 0;
	      }else{
	      	foreach ($kanagata_log_plates as $kanagata_log_plates) {
		       $total_plate = $kanagata_log_plates->plate_total;
		    }
	      }

	    $ada_reminder = '';

	    // if ($total_plate != 0 && $fetch_material_check->qty_check != 0) {
	    // 	if ($total_plate % $fetch_material_check->qty_check < 2000) {
	    // 		$ada_reminder = 'REMINDER !!!<br>Saatnya pengambilan sample.';
	    // 	}
	    // }

		$response = array(
			'status' => true,
			'total_plate' => $total_plate,
			'ada_reminder' => $ada_reminder,
		);
		return Response::json($response);
	}

	public function fetchPpl(Request $request){

		$fetch_material_check = MpKanagata::where('material_number',$request->get('material_number'))->first();

		$kanagata_log_ppls = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and ppl_number = '".$request->get('ppl_number')."' and ppl_status = 'Running'");
		$total_ppl = 0;
	      if(count($kanagata_log_ppls) == 0){
	      	$total_ppl = 0;
	      }else{
	      	foreach ($kanagata_log_ppls as $kanagata_log_ppls) {
		       $total_ppl = $kanagata_log_ppls->ppl_total;
		    }
	      }

	    $ada_reminder = '';

	    // if ($total_ppl != 0 && $fetch_material_check->qty_check != 0) {
	    // 	if ($total_ppl % $fetch_material_check->qty_check < 2000) {
	    // 		$ada_reminder = 'REMINDER !!!<br>Saatnya pengambilan sample.';
	    // 	}
	    // }

		$response = array(
			'status' => true,
			'total_ppl' => $total_ppl,
			'ada_reminder' => $ada_reminder,
		);
		return Response::json($response);
	}

	public function fetchDp(Request $request){

		$fetch_material_check = MpKanagata::where('material_number',$request->get('material_number'))->first();

		$kanagata_log_dps = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and dp_number = '".$request->get('dp_number')."' and dp_status = 'Running'");
		$total_dp = 0;
	      if(count($kanagata_log_dps) == 0){
	      	$total_dp = 0;
	      }else{
	      	foreach ($kanagata_log_dps as $kanagata_log_dps) {
		       $total_dp = $kanagata_log_dps->dp_total;
		    }
	      }

	    $ada_reminder = '';

	    // if ($total_dp != 0 && $fetch_material_check->qty_check != 0) {
	    // 	if ($total_dp % $fetch_material_check->qty_check < 2000) {
	    // 		$ada_reminder = 'REMINDER !!!<br>Saatnya pengambilan sample.';
	    // 	}
	    // }

		$response = array(
			'status' => true,
			'total_dp' => $total_dp,
			'ada_reminder' => $ada_reminder,
		);
		return Response::json($response);
	}

	public function fetchDd(Request $request){

		$fetch_material_check = MpKanagata::where('material_number',$request->get('material_number'))->first();

		$kanagata_log_dds = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and dd_number = '".$request->get('dd_number')."' and dd_status = 'Running'");
		$total_dd = 0;
	      if(count($kanagata_log_dds) == 0){
	      	$total_dd = 0;
	      }else{
	      	foreach ($kanagata_log_dds as $kanagata_log_dds) {
		       $total_dd = $kanagata_log_dds->dd_total;
		    }
	      }

	    $ada_reminder = '';

	    // if ($total_dd != 0 && $fetch_material_check->qty_check != 0) {
	    // 	if ($total_dd % $fetch_material_check->qty_check < 2000) {
	    // 		$ada_reminder = 'REMINDER !!!<br>Saatnya pengambilan sample.';
	    // 	}
	    // }

		$response = array(
			'status' => true,
			'total_dd' => $total_dd,
			'ada_reminder' => $ada_reminder,
		);
		return Response::json($response);
	}

	public function fetchSnap(Request $request){

		$fetch_material_check = MpKanagata::where('material_number',$request->get('material_number'))->first();

		$kanagata_log_snaps = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and snap_number = '".$request->get('snap_number')."' and snap_status = 'Running'");
		$total_snap = 0;
	      if(count($kanagata_log_snaps) == 0){
	      	$total_snap = 0;
	      }else{
	      	foreach ($kanagata_log_snaps as $kanagata_log_snaps) {
		       $total_snap = $kanagata_log_snaps->snap_total;
		    }
	      }

	    $ada_reminder = '';

	    // if ($total_snap != 0 && $fetch_material_check->qty_check != 0) {
	    // 	if ($total_snap % $fetch_material_check->qty_check < 2000) {
	    // 		$ada_reminder = 'REMINDER !!!<br>Saatnya pengambilan sample.';
	    // 	}
	    // }

		$response = array(
			'status' => true,
			'total_snap' => $total_snap,
			'ada_reminder' => $ada_reminder,
		);
		return Response::json($response);
	}

	public function fetchLower(Request $request){

		$fetch_material_check = MpKanagata::where('material_number',$request->get('material_number'))->first();

		$kanagata_log_lowers = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and lower_number = '".$request->get('lower_number')."' and lower_status = 'Running'");
		$total_lower = 0;
	      if(count($kanagata_log_lowers) == 0){
	      	$total_lower = 0;
	      }else{
	      	foreach ($kanagata_log_lowers as $kanagata_log_lowers) {
		       $total_lower = $kanagata_log_lowers->lower_total;
		    }
	      }

	    $ada_reminder = '';

	    // if ($total_lower != 0 && $fetch_material_check->qty_check != 0) {
	    // 	if ($total_lower % $fetch_material_check->qty_check < 2000) {
	    // 		$ada_reminder = 'REMINDER !!!<br>Saatnya pengambilan sample.';
	    // 	}
	    // }

		$response = array(
			'status' => true,
			'total_lower' => $total_lower,
			'ada_reminder' => $ada_reminder,
		);
		return Response::json($response);
	}

	public function fetchUpper(Request $request){

		$fetch_material_check = MpKanagata::where('material_number',$request->get('material_number'))->first();

		$kanagata_log_uppers = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and upper_number = '".$request->get('upper_number')."' and upper_status = 'Running'");
		$total_upper = 0;
	      if(count($kanagata_log_uppers) == 0){
	      	$total_upper = 0;
	      }else{
	      	foreach ($kanagata_log_uppers as $kanagata_log_uppers) {
		       $total_upper = $kanagata_log_uppers->upper_total;
		    }
	      }

	    $ada_reminder = '';

	    // if ($total_upper != 0 && $fetch_material_check->qty_check != 0) {
	    // 	if ($total_upper % $fetch_material_check->qty_check < 2000) {
	    // 		$ada_reminder = 'REMINDER !!!<br>Saatnya pengambilan sample.';
	    // 	}
	    // }

		$response = array(
			'status' => true,
			'total_upper' => $total_upper,
			'ada_reminder' => $ada_reminder,
		);
		return Response::json($response);
	}

	public function fetchHalf(Request $request){

		$fetch_material_check = MpKanagata::where('material_number',$request->get('material_number'))->first();

		$kanagata_log_halfs = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and half_number = '".$request->get('half_number')."' and half_status = 'Running'");
		$total_half = 0;
	      if(count($kanagata_log_halfs) == 0){
	      	$total_half = 0;
	      }else{
	      	foreach ($kanagata_log_halfs as $kanagata_log_halfs) {
		       $total_half = $kanagata_log_halfs->half_total;
		    }
	      }

	    $ada_reminder = '';

	    // if ($total_half != 0 && $fetch_material_check->qty_check != 0) {
	    // 	if ($total_half % $fetch_material_check->qty_check < 2000) {
	    // 		$ada_reminder = 'REMINDER !!!<br>Saatnya pengambilan sample.';
	    // 	}
	    // }

		$response = array(
			'status' => true,
			'total_half' => $total_half,
			'ada_reminder' => $ada_reminder,
		);
		return Response::json($response);
	}

	public function fetchDinsert(Request $request){

		$fetch_material_check = MpKanagata::where('material_number',$request->get('material_number'))->first();

		$kanagata_log_dinserts = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and dinsert_number = '".$request->get('dinsert_number')."' and dinsert_status = 'Running'");
		$total_dinsert = 0;
	      if(count($kanagata_log_dinserts) == 0){
	      	$total_dinsert = 0;
	      }else{
	      	foreach ($kanagata_log_dinserts as $kanagata_log_dinserts) {
		       $total_dinsert = $kanagata_log_dinserts->dinsert_total;
		    }
	      }

	    $ada_reminder = '';

	    // if ($total_dinsert != 0 && $fetch_material_check->qty_check != 0) {
	    // 	if ($total_dinsert % $fetch_material_check->qty_check < 2000) {
	    // 		$ada_reminder = 'REMINDER !!!<br>Saatnya pengambilan sample.';
	    // 	}
	    // }

		$response = array(
			'status' => true,
			'total_dinsert' => $total_dinsert,
			'ada_reminder' => $ada_reminder,
		);
		return Response::json($response);
	}

	public function create(){

		$process = DB::SELECT("SELECT DISTINCT(process_name) FROM `mp_processes` where remark = 'Press'");

		$machine = DB::SELECT("SELECT * FROM `mp_machines` where remark = 'Press'");

		// if($product == "Saxophone"){
		// 	$title_jp = "生産のプレス機　‐　サックス";
		// }
		// elseif($product == "Flute"){
		// 	$title_jp = "生産のプレス機　‐　フルート";
		// }
		// elseif($product == "Clarinet"){
			$title_jp = "生産のプレス機";
		// }

		$data = array(
                	'process' => $process,
                	'machine' => $machine);
		return view('press.create_press_data',$data)->with('page', 'Press Machine Production')->with('title_jp', $title_jp);
	}

	function store(Request $request)
    {
        	try{    
              $id_user = Auth::id();
              // $interview_id = $request->get('interview_id');
              // $electric_supply_time = new DateTime($request->get('electric_supply_time'));
              // $lepas_molding = new DateTime($request->get('lepas_molding'));
              // $mins = $electric_supply_time->diff($lepas_molding);
              // if($request->get('lepas_molding') == '0:00:00'){
              	$lepas_molding_new = $request->get('lepas_molding');
              // }else{
              // 	$lepas_molding_new = $mins->format('%H:%I:%S');
              // }

				$dp_value = $request->get('dp_value');
				$dd_value = $request->get('dd_value');
				$snap_value = $request->get('snap_value');
				$lower_value = $request->get('lower_value');
				$upper_value = $request->get('upper_value');
				$half_value = $request->get('half_value');
				$dinsert_value = $request->get('dinsert_value');

		      if ($request->get('process') != 'Nukishibori') {
				$dp_value = null;
				$dd_value = null;
				$snap_value = null;
				$lower_value = null;
				$upper_value = null;
				$half_value = null;
				$dinsert_value = null;
		      }
              
                MpRecordProd::create([
                    'date' => $request->get('date'),
                    'pic' => $request->get('pic'),
                    'product' => $request->get('product'),
                    'machine' => $request->get('machine'),
                    'shift' => $request->get('shift'),
                    'material_number' => $request->get('material_number'),
                    'process' => $request->get('process'),
                    'punch_number' => $request->get('punch_number'),
                    'plate_number' => $request->get('plate_number'),
                    'ppl_number' => $request->get('ppl_number'),
                    'die_number' => $request->get('die_number'),
                    'dp_number' => $request->get('dp_number'),
					'dd_number' => $request->get('dd_number'),
					'snap_number' => $request->get('snap_number'),
					'lower_number' => $request->get('lower_number'),
					'upper_number' => $request->get('upper_number'),
					'half_number' => $request->get('half_number'),
					'dinsert_number' => $request->get('dinsert_number'),
                    'start_time' => $request->get('start_time'),
                    'end_time' => $request->get('end_time'),
                    'lepas_molding' => $lepas_molding_new,
                    'pasang_molding' => $request->get('pasang_molding'),
                    'process_time' => $request->get('process_time'),
                    'kensa_time' => $request->get('kensa_time'),
                    'electric_supply_time' => $request->get('electric_supply_time'),
                    'data_ok' => $request->get('data_ok'),
                    'punch_value' => $request->get('punch_value'),
                    'die_value' => $request->get('die_value'),
                    'plate_value' => $request->get('plate_value'),
                    'ppl_value' => $request->get('ppl_value'),
                    'dp_value' => $dp_value,
					'dd_value' => $dd_value,
					'snap_value' => $snap_value,
					'lower_value' => $lower_value,
					'upper_value' => $upper_value,
					'half_value' => $half_value,
					'dinsert_value' => $dinsert_value,
                    'created_by' => $id_user
                ]);

               $kanagata_log_dies = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and die_number = '".$request->get('die_number')."' and die_status = 'Running'");

			   $kanagata_log_punch = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and punch_number = '".$request->get('punch_number')."' and punch_status = 'Running'");

			   $kanagata_log_plate = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and plate_number = '".$request->get('plate_number')."' and plate_status = 'Running'");

			   $kanagata_log_ppl = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and ppl_number = '".$request->get('ppl_number')."' and ppl_status = 'Running'");

			   	$kanagata_log_dp = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and dp_number = '".$request->get('dp_number')."' and dp_status = 'Running'");
				$kanagata_log_dd = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and dd_number = '".$request->get('dd_number')."' and dd_status = 'Running'");
				$kanagata_log_snap = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and snap_number = '".$request->get('snap_number')."' and snap_status = 'Running'");
				$kanagata_log_lower = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and lower_number = '".$request->get('lower_number')."' and lower_status = 'Running'");
				$kanagata_log_upper = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and upper_number = '".$request->get('upper_number')."' and upper_status = 'Running'");
				$kanagata_log_half = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and half_number = '".$request->get('half_number')."' and half_status = 'Running'");
				$kanagata_log_dinsert = DB::SELECT("SELECT * FROM `mp_kanagata_logs` where material_number = '".$request->get('material_number')."' and dinsert_number = '".$request->get('dinsert_number')."' and dinsert_status = 'Running'");



			  $total_punch = 0;
		      if(count($kanagata_log_punch) == 0){
		      	$total_punch = 0;
		      }else{
		      	foreach ($kanagata_log_punch as $kanagata_log_punch) {
			       $total_punch = $kanagata_log_punch->punch_total;
			    }
		      }

		      $total_die = 0;
		      if(count($kanagata_log_dies) == 0){
		      	$total_die = 0;
		      }else{
		      	foreach ($kanagata_log_dies as $kanagata_log_dies) {
			       $total_die = $kanagata_log_dies->die_total;
			    }
		      }

		      $total_plate = 0;
		      if(count($kanagata_log_plate) == 0){
		      	$total_plate = 0;
		      }else{
		      	foreach ($kanagata_log_plate as $kanagata_log_plate) {
			       $total_plate = $kanagata_log_plate->plate_total;
			    }
		      }

		      $total_ppl = 0;
		      if(count($kanagata_log_ppl) == 0){
		      	$total_ppl = 0;
		      }else{
		      	foreach ($kanagata_log_ppl as $kanagata_log_ppl) {
			       $total_ppl = $kanagata_log_ppl->ppl_total;
			    }
		      }

		     	$total_dp = 0;
		      if(count($kanagata_log_dp) == 0){
		      	$total_dp = 0;
		      }else{
		      	foreach ($kanagata_log_dp as $kanagata_log_dp) {
			       $total_dp = $kanagata_log_dp->dp_total;
			    }
		      }
				$total_dd = 0;
		      if(count($kanagata_log_dd) == 0){
		      	$total_dd = 0;
		      }else{
		      	foreach ($kanagata_log_dd as $kanagata_log_dd) {
			       $total_dd = $kanagata_log_dd->dd_total;
			    }
		      }
				$total_snap = 0;
		      if(count($kanagata_log_snap) == 0){
		      	$total_snap = 0;
		      }else{
		      	foreach ($kanagata_log_snap as $kanagata_log_snap) {
			       $total_snap = $kanagata_log_snap->snap_total;
			    }
		      }
				$total_lower = 0;
		      if(count($kanagata_log_lower) == 0){
		      	$total_lower = 0;
		      }else{
		      	foreach ($kanagata_log_lower as $kanagata_log_lower) {
			       $total_lower = $kanagata_log_lower->lower_total;
			    }
		      }
				$total_upper = 0;
		      if(count($kanagata_log_upper) == 0){
		      	$total_upper = 0;
		      }else{
		      	foreach ($kanagata_log_upper as $kanagata_log_upper) {
			       $total_upper = $kanagata_log_upper->upper_total;
			    }
		      }
				$total_half = 0;
		      if(count($kanagata_log_half) == 0){
		      	$total_half = 0;
		      }else{
		      	foreach ($kanagata_log_half as $kanagata_log_half) {
			       $total_half = $kanagata_log_half->half_total;
			    }
		      }
				$total_dinsert = 0;
		      if(count($kanagata_log_dinsert) == 0){
		      	$total_dinsert = 0;
		      }else{
		      	foreach ($kanagata_log_dinsert as $kanagata_log_dinsert) {
			       $total_dinsert = $kanagata_log_dinsert->dinsert_total;
			    }
		      }

		      $total_punch = $total_punch + $request->get('punch_value');
		      $total_die = $total_die + $request->get('die_value');
		      $die_status = 'Running';
		      $punch_status = 'Running';
		      if (str_contains($request->get('process'),'Trimming')) {
		      	$die_status = null;
		      	$total_die = null;
		      }

		      if (str_contains($request->get('process'),'Nukishibori')) {
		      	$punch_status = null;
		      	$total_punch = null;
		      }

		      $total_plate = $total_plate + $request->get('plate_value');
		      $plate_status = 'Running';
		      if (str_contains($request->get('process'),'Forging')) {
		      	$plate_status = null;
		      	$total_plate = null;
		      }

		      $total_ppl = null;
		      $ppl_status = null;

		      if (str_contains($request->get('process'),'Blank Nuki') || str_contains($request->get('process'),'Nukishibori')) {
		      	$total_ppl = $total_ppl + $request->get('ppl_value');
		      	$ppl_status = 'Running';
		      }

		      $total_dp = $total_dp + $request->get('dp_value');
		      	$dp_status = 'Running';
				$total_dd = $total_dd + $request->get('dd_value');
				$dd_status = 'Running';
				$total_snap = $total_snap + $request->get('snap_value');
				$snap_status = 'Running';
				$total_lower = $total_lower + $request->get('lower_value');
				$lower_status = 'Running';
				$total_upper = $total_upper + $request->get('upper_value');
				$upper_status = 'Running';
				$total_half = $total_half + $request->get('half_value');
				$half_status = 'Running';
				$total_dinsert = $total_dinsert + $request->get('dinsert_value');
				$dinsert_status = 'Running';

				$dp_value = $request->get('dp_value');
				$dd_value = $request->get('dd_value');
				$snap_value = $request->get('snap_value');
				$lower_value = $request->get('lower_value');
				$upper_value = $request->get('upper_value');
				$half_value = $request->get('half_value');
				$dinsert_value = $request->get('dinsert_value');

		      if ($request->get('process') != 'Nukishibori') {
		      	$total_dp = null;
		      	$dp_status = null;
				$total_dd = null;
				$dd_status = null;
				$total_snap = null;
				$snap_status = null;
				$total_lower = null;
				$lower_status = null;
				$total_upper = null;
				$upper_status = null;
				$total_half = null;
				$half_status = null;
				$total_dinsert = null;
				$dinsert_status = null;

				$dp_value = null;
				$dd_value = null;
				$snap_value = null;
				$lower_value = null;
				$upper_value = null;
				$half_value = null;
				$dinsert_value = null;
		      }
              
	          MpKanagataLog::create([
	                'date' => $request->get('date'),
	                'pic' => $request->get('pic'),
	                'product' => $request->get('product'),
	                'machine' => $request->get('machine'),
	                'shift' => $request->get('shift'),
	                'material_number' => $request->get('material_number'),
	                'process' => $request->get('process'),

	                'punch_number' => $request->get('punch_number'),
	                'die_number' => $request->get('die_number'),
	                'plate_number' => $request->get('plate_number'),
	                'ppl_number' => $request->get('ppl_number'),
	                'dp_number' => $request->get('dp_number'),
					'dd_number' => $request->get('dd_number'),
					'snap_number' => $request->get('snap_number'),
					'lower_number' => $request->get('lower_number'),
					'upper_number' => $request->get('upper_number'),
					'half_number' => $request->get('half_number'),
					'dinsert_number' => $request->get('dinsert_number'),

	                'start_time' => $request->get('start_time'),
	                'end_time' => $request->get('end_time'),

	                'punch_value' => $request->get('punch_value'),
	                'die_value' => $request->get('die_value'),
	                'plate_value' => $request->get('plate_value'),
	                'ppl_value' => $request->get('ppl_value'),
	                'dp_value' => $dp_value,
					'dd_value' => $dd_value,
					'snap_value' => $snap_value,
					'lower_value' => $lower_value,
					'upper_value' => $upper_value,
					'half_value' => $half_value,
					'dinsert_value' => $dinsert_value,

	                'punch_total' => $total_punch,
	                'die_total' => $total_die,
	                'plate_total' => $total_plate,
	                'ppl_total' => $total_ppl,
	                'dp_total' => $total_dp,
					'dd_total' => $total_dd,
					'snap_total' => $total_snap,
					'lower_total' => $total_lower,
					'upper_total' => $total_upper,
					'half_total' => $total_half,
					'dinsert_total' => $total_dinsert,

	                'punch_status' => $punch_status,
	                'die_status' => $die_status,
	                'plate_status' => $plate_status,
	                'ppl_status' => $ppl_status,
	                'dp_status' => $dp_status,
					'dd_status' => $dd_status,
					'snap_status' => $snap_status,
					'lower_status' => $lower_status,
					'upper_status' => $upper_status,
					'half_status' => $half_status,
					'dinsert_status' => $dinsert_status,

	                'created_by' => $id_user
	          ]);

	          $data_punch = MpKanagata::where('punch_die_number',$request->get('punch_number'))->where('part','like','%PUNCH%')->first();
	          $data_die = MpKanagata::where('punch_die_number',$request->get('die_number'))->where('part','like','%DIE%')->first();
	          $data_plate = MpKanagata::where('punch_die_number',$request->get('plate_number'))->where('part','like','%PLATE%')->first();
	          $data_ppl = MpKanagata::where('punch_die_number',$request->get('plate_number'))->where('part','like','%PPL%')->first();
	          $data_dp = MpKanagata::where('punch_die_number',$request->get('dp_number'))->where('part','like','%DP%')->first();
			  $data_dd = MpKanagata::where('punch_die_number',$request->get('dd_number'))->where('part','like','%DD%')->first();
			  $data_snap = MpKanagata::where('punch_die_number',$request->get('snap_number'))->where('part','like','%SNAP%')->first();
			  $data_lower = MpKanagata::where('punch_die_number',$request->get('lower_number'))->where('part','like','%LOWER%')->first();
			  $data_upper = MpKanagata::where('punch_die_number',$request->get('upper_number'))->where('part','like','%UPPER%')->first();
			  $data_half = MpKanagata::where('punch_die_number',$request->get('half_number'))->where('part','like','%HALF%')->first();
			  $data_dinsert = MpKanagata::where('punch_die_number',$request->get('dinsert_number'))->where('part','like','%DINSERT%')->first();

	          if ($data_punch) {
	          	$data_punch->qty_check = $data_punch->qty_check + $request->get('punch_value');
	          	$data_punch->save();
	          }
	          if ($data_die) {
	          	$data_die->qty_check = $data_die->qty_check + $request->get('die_value');
	          	$data_die->save();
	          }
	          if ($data_plate) {
	          	$data_plate->qty_check = $data_plate->qty_check + $request->get('plate_value');
	          	$data_plate->save();
	          }

	          if ($data_ppl) {
	          	$data_ppl->qty_check = $data_ppl->qty_check + $request->get('ppl_value');
	          	$data_ppl->save();
	          }

	          if ($data_dp) {
	          	$data_dp->qty_check = $data_dp->qty_check + $request->get('dp_value');
	          	$data_dp->save();
	          }
			  if ($data_dd) {
	          	$data_dd->qty_check = $data_dd->qty_check + $request->get('dd_value');
	          	$data_dd->save();
	          }
			  if ($data_snap) {
	          	$data_snap->qty_check = $data_snap->qty_check + $request->get('snap_value');
	          	$data_snap->save();
	          }
			  if ($data_lower) {
	          	$data_lower->qty_check = $data_lower->qty_check + $request->get('lower_value');
	          	$data_lower->save();
	          }
			  if ($data_upper) {
	          	$data_upper->qty_check = $data_upper->qty_check + $request->get('upper_value');
	          	$data_upper->save();
	          }
			  if ($data_half) {
	          	$data_half->qty_check = $data_half->qty_check + $request->get('half_value');
	          	$data_half->save();
	          }
			  if ($data_dinsert) {
	          	$data_dinsert->qty_check = $data_dinsert->qty_check + $request->get('dinsert_value');
	          	$data_dinsert->save();
	          }

              $response = array(
                'status' => true,
              );
              // return redirect('index/interview/details/'.$interview_id)
              // ->with('page', 'Interview Details')->with('status', 'New Participant has been created.');
              return Response::json($response);
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    function finish_trouble(Request $request)
    {
        	try{
              $id_user = Auth::id();
              // $interview_id = $request->get('interview_id');
              $trouble = MpTroubleLog::find($request->get('id'));
              $trouble->end_time = date('Y-m-d h:i:s');
              $trouble->save();

              $response = array(
                'status' => true,
              );
              // return redirect('index/interview/details/'.$interview_id)
              // ->with('page', 'Interview Details')->with('status', 'New Participant has been created.');
              return Response::json($response);
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    function store_trouble(Request $request)
    {
        	try{    
              $id_user = Auth::id();
              // $interview_id = $request->get('interview_id');
              
                MpTroubleLog::create([
                    'date' => $request->get('date'),
                    'pic' => $request->get('pic'),
                    'product' => $request->get('product'),
                    'machine' => $request->get('machine'),
                    'shift' => $request->get('shift'),
                    'material_number' => $request->get('material_number'),
                    'process' => $request->get('process'),
                    'start_time' => $request->get('start_time'),
                    'reason' => $request->get('reason'),
                    'created_by' => $id_user
                ]);

              $response = array(
                'status' => true,
              );
              // return redirect('index/interview/details/'.$interview_id)
              // ->with('page', 'Interview Details')->with('status', 'New Participant has been created.');
              return Response::json($response);
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    // Khusus Monitoring / Grafik

    public function monitoring() {
    	$title = 'Press Machine Monitoring';
		$title_jp = 'プレス機管理';

		$process = DB::table('mp_processes')->orderBy('id', 'ASC')->get();

		return view('press.monitoring_result', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'process' => $process
		))->with('page', 'Machine press')->with('head', 'Machine Press');
    }

    public function fetchMonitoring(Request $request){
		$date = '';
		$date_from = $request->get('tanggal_from');
		    $date_to = $request->get('tanggal_to');
		    if ($date_from == "") {
		     if ($date_to == "") {
		      $first = "DATE(NOW())";
		      $last = "DATE(NOW())";
		      $date = date('Y-m-d');
		      $monthTitle = date("d M Y", strtotime($date));
		      $dates_from =date('Y-m-d');
		      $dates_to =date('Y-m-d');
		    }else{
		      $first = "DATE(NOW())";
		      $last = "'".$date_to."'";
		      $date = date('Y-m-d');
		      $monthTitle = date("d M Y", strtotime($date)).' to '.date("d M Y", strtotime($date_to));
		      $dates_from =date('Y-m-d');
		      $dates_to =$date_to;
		    }
		  }else{
		   if ($date_to == "") {
		    $first = "'".$date_from."'";
		    $last = "DATE(NOW())";
		    $date = date('Y-m-d');
		    $monthTitle = date("d M Y", strtotime($date_from)).' to '.date("d M Y", strtotime($date));
		    $dates_from =$date_from;
		    $dates_to =date('Y-m-d');
		  }else{
		    $first = "'".$date_from."'";
		    $last = "'".$date_to."'";
		    $monthTitle = date("d M Y", strtotime($date_from)).' to '.date("d M Y", strtotime($date_to));
		    $dates_from =$date_from;
		    $dates_to =$date_to;
		  }
		}

		// $dateTitle = date("d M Y", strtotime($date));

		 // $process = $request->get('proses');

	  //     if ($process != null) {
	  //         $proses = json_encode($process);
	  //         $pro = str_replace(array("[","]"),array("(",")"),$proses);

	  //         $where = 'and mp_record_prods.process in'.$pro;
	  //     }else{
	  //         $where = '';
	  //     }

		// $data = db::select("select mp_machines.machine_name, COALESCE(sum(mp_record_prods.data_ok),0)  as actual_shoot, COALESCE(mp_record_prods.date,CURDATE()) as tgl , TRUNCATE(SUM(TIME_TO_SEC(mp_record_prods.process_time) / 60 ),2) as waktu_mesin from mp_machines left join mp_record_prods on mp_machines.machine_name = mp_record_prods.machine left join mp_processes on mp_record_prods.process = mp_processes.process_desc where DATE_FORMAT(COALESCE(mp_record_prods.date,CURDATE()),'%Y-%m-%d') = '".$date."' ".$where." GROUP BY mp_machines.machine_name,mp_record_prods.date");

	     $data = db::select("select machine_name, sum(data_ok) as actual_shoot, CURDATE() as tgl, sum(waktu) as waktu_mesin from (select machine_name, 0 as data_ok, CURDATE(), 0 as waktu from mp_machines UNION ALL select machine_name, data_ok, date, ROUND(TIME_TO_SEC(process_time) / 60,1) as waktu from mp_machines LEFT JOIN mp_record_prods on mp_machines.machine_name = mp_record_prods.machine where mp_record_prods.date >= ".$first." AND mp_record_prods.date <= ".$last." ) as aw GROUP BY machine_name");

		$operator = db::select("SELECT name,
				sum( data_ok ) AS actual_shot,
				CURDATE() AS date,
				sum( waktu ) AS waktu_total 
			FROM
				(
				SELECT
					employee_syncs.name,
					0 AS data_ok,
					CURDATE(),
					0 AS waktu 
				FROM
					employee_groups
					JOIN employee_syncs ON employee_syncs.employee_id = employee_groups.employee_id 
				WHERE
					location = 'Press'
					and employee_groups.deleted_at is null
					UNION ALL
				SELECT
					employee_syncs.name,
					data_ok,
					mp_record_prods.date,
					ROUND( TIME_TO_SEC( process_time ) / 60, 1 ) AS waktu 
				FROM
					employee_groups
					LEFT JOIN mp_record_prods ON employee_groups.employee_id = mp_record_prods.pic
					JOIN employee_syncs ON employee_syncs.employee_id = employee_groups.employee_id 
					AND location = 'Press' 
					AND mp_record_prods.date >= ".$first."
					AND mp_record_prods.date <= ".$last."
					and employee_groups.deleted_at is null
				) AS aw 
			GROUP BY
			name");


		$response = array(
			'status' => true,
			'datas' => $data,
			'date_from' => $date_from,
			'date_to' => $date_to,
			'operator' => $operator,
			'monthTitle' => $monthTitle,
			'dates_from' => $dates_from,
			'dates_to' => $dates_to,
		);
		return Response::json($response);
	}

	public function detail_press(Request $request){

      $machine = $request->get("mesin");
      $tanggal_from = $request->get("tanggal_from");
      $tanggal_to = $request->get("tanggal_to");

      $query = "select date, name, machine, material_number, data_ok from mp_record_prods join employees on mp_record_prods.pic = employees.employee_id where date >= '".$tanggal_from."' AND date <= '".$tanggal_to."' and machine='".$machine."'";

      $detail = db::select($query);

      return DataTables::of($detail)
      ->make(true);
    }

    public function detail_pic(Request $request){

      $pic = $request->get("pic");
      $tanggal_from = $request->get("tanggal_from");
      $tanggal_to = $request->get("tanggal_to");

      $query = "select date, name, machine, material_number, data_ok from mp_record_prods join employees on mp_record_prods.pic = employees.employee_id where date >= '".$tanggal_from."' AND date <= '".$tanggal_to."' and name='".$pic."'";

      $detail = db::select($query);

      return DataTables::of($detail)
      ->make(true);
    }

	public function monitoring2() {
    	$title = 'Press Machine Monitoring';
		$title_jp = 'プレス機管理';

		$process = DB::table('mp_processes')->orderBy('id', 'ASC')->get();

		return view('press.monitoring_result2', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'process' => $process
		))->with('page', 'Machine press')->with('head', 'Machine Press');
    }

	public function report_trouble(){

		$process = DB::SELECT("SELECT DISTINCT(process_name) FROM `mp_processes` where remark = 'Press'");

		$machine = DB::SELECT("SELECT * FROM `mp_machines` where remark = 'Press'");


		$report_trouble = db::select("select mp_trouble_logs.id,mp_materials.material_number,employees.employee_id,employees.name,date,mp_trouble_logs.product,mp_trouble_logs.process,machine,start_time,end_time,reason,mp_materials.material_name
			from mp_trouble_logs
			join employee_groups on employee_groups.employee_id = mp_trouble_logs.pic
			join employees on employee_groups.employee_id = employees.employee_id
			join mp_materials on mp_trouble_logs.material_number= mp_materials.material_number
			ORDER BY mp_trouble_logs.id desc");

		$data = array(
                	'process' => $process,
                	'report_trouble' => $report_trouble,
                	'machine' => $machine);
		return view('press.report_press_trouble',$data)->with('page', 'Press Machine Trouble Report')->with('title_jp', "プレス機トラブルリポート");
	}

	public function filter_report_trouble(Request $request){

		  $date_from = $request->get('date_from');
	      $date_to = $request->get('date_to');
	      $datenow = date('Y-m-d');

	      if($request->get('date_to') == null){
	        if($request->get('date_from') == null){
	          $date = "";
	        }
	        elseif($request->get('date_from') != null){
	          $date = "where date BETWEEN '".$date_from."' and '".$datenow."'";
	        }
	      }
	      elseif($request->get('date_to') != null){
	        if($request->get('date_from') == null){
	          $date = "where date <= '".$date_to."'";
	        }
	        elseif($request->get('date_from') != null){
	          $date = "where date BETWEEN '".$date_from."' and '".$date_to."'";
	        }
	      }

		$process = DB::SELECT("SELECT DISTINCT(process_name) FROM `mp_processes` where remark = 'Press'");

		$machine = DB::SELECT("SELECT * FROM `mp_machines` where remark = 'Press'");


		$report_trouble = db::select("select mp_trouble_logs.id,mp_materials.material_number,employees.employee_id,employees.name,date,mp_trouble_logs.product,mp_trouble_logs.process,machine,start_time,end_time,reason,mp_materials.material_name
			from mp_trouble_logs
			join employee_groups on employee_groups.employee_id = mp_trouble_logs.pic
			join employees on employee_groups.employee_id = employees.employee_id
			join mp_materials on mp_trouble_logs.material_number= mp_materials.material_number
			".$date."
			ORDER BY mp_trouble_logs.id desc");

		$data = array(
                	'process' => $process,
                	'report_trouble' => $report_trouble,
                	'machine' => $machine);
		return view('press.report_press_trouble',$data)->with('page', 'Press Machine Trouble Report')->with('title_jp', "??");
	}

	public function report_prod_result(){

		$process = DB::SELECT("SELECT DISTINCT(process_name) FROM `mp_processes` where remark = 'Press'");

		$machine = DB::SELECT("SELECT * FROM `mp_machines` where remark = 'Press'");

		$first = date('Y-m-01');
		$now = date('Y-m-d');

		// $prod_result = db::select("SELECT
		// 		*,
		// 		mp_record_prods.id AS prod_result_id 
		// 	FROM
		// 		mp_record_prods
		// 		JOIN employee_groups ON employee_groups.employee_id = mp_record_prods.pic
		// 		JOIN employees ON employee_groups.employee_id = employees.employee_id
		// 		JOIN mp_materials ON mp_record_prods.material_number = mp_materials.material_number 
		// 	WHERE
		// 		DATE( mp_record_prods.date ) BETWEEN '".$first."' 
		// 		AND '".$now."' 
		// 	ORDER BY
		// 		mp_record_prods.id DESC");

		$emp = DB::SELECT("SELECT
				* 
			FROM
				employee_groups
				JOIN employee_syncs ON employee_groups.employee_id = employee_syncs.employee_id 
			WHERE
				location = 'Press'");

		$data = array(
                	'process' => $process,
                	// 'prod_result' => $prod_result,
                	'mesin' => $this->mesin,
                	'emp' => $emp,
                	'machine' => $machine);
		return view('press.report_prod_result',$data)->with('page', 'Press Machine Production Result')->with('title_jp', "??");
	}

	public function filter_report_prod_result(Request $request){

		  $date_from = $request->get('date_from');
	      $date_to = $request->get('date_to');
	      $datenow = date('Y-m-d');

	      if($request->get('date_to') == null){
	        if($request->get('date_from') == null){
	          $date = "";
	        }
	        elseif($request->get('date_from') != null){
	          $date = "where date BETWEEN '".$date_from."' and '".$datenow."'";
	        }
	      }
	      elseif($request->get('date_to') != null){
	        if($request->get('date_from') == null){
	          $date = "where date <= '".$date_to."'";
	        }
	        elseif($request->get('date_from') != null){
	          $date = "where date BETWEEN '".$date_from."' and '".$date_to."'";
	        }
	      }

		$process = DB::SELECT("SELECT DISTINCT(process_name) FROM `mp_processes` where remark = 'Press'");

		$machine = DB::SELECT("SELECT * FROM `mp_machines` where remark = 'Press'");


		$prod_result = db::select("
			SELECT
				*,
				mp_record_prods.process AS process_asli,
				mp_record_prods.id AS prod_result_id 
			FROM
				mp_record_prods
				JOIN employee_syncs ON mp_record_prods.pic = employee_syncs.employee_id
			".$date."
			ORDER BY
				mp_record_prods.id DESC");

		$emp = DB::SELECT("SELECT
				* 
			FROM
				employee_groups
				JOIN employee_syncs ON employee_groups.employee_id = employee_syncs.employee_id 
			WHERE
				location = 'Press'");

		$materials = DB::TABLE('mp_materials')->get();

		$data = array(
                	'process' => $process,
                	'prod_result' => $prod_result,
                	'emp' => $emp,
                	'materials' => $materials,
                	'mesin' => $this->mesin,
                	'machine' => $machine);
		return view('press.report_prod_result',$data)->with('page', 'Press Machine Production Result')->with('title_jp', "??");
	}

	public function deleteProdResult(Request $request)
	{
		try {
			$id = $request->get('id');
			$record = MpRecordProd::where('id',$id)->first();
			if ($record) {
				$kanagata = MpKanagataLog::
				where('date',$record->date)->
				where('pic',$record->pic)->
				where('shift',$record->shift)->
				where('product',$record->product)->
				where('material_number',$record->material_number)->
				where('process',$record->process)->
				where('machine',$record->machine)->
				where('punch_number',$record->punch_number)->
				where('die_number',$record->die_number)->first();
				if ($kanagata) {
					$kanagata->forceDelete();
				}

				$record = MpRecordProd::where('id',$id)->forceDelete();
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

	public function report_kanagata_lifetime(){

		$process = DB::SELECT("SELECT DISTINCT(process_desc) FROM `mp_processes` where remark = 'Press'");

		$machine = DB::SELECT("SELECT * FROM `mp_machines` where remark = 'Press'");

		$first = date('Y-m-01');
		$now = date('Y-m-d');

		$username = Auth::user()->username;

		$kanagata = MpKanagata::
		select('mp_kanagatas.product','mp_kanagatas.material_number', 'mp_kanagatas.material_name', 'mp_kanagatas.material_description','mp_kanagatas.punch_die_number', 'mp_kanagatas.id', 'mp_kanagatas.part')
		->distinct()
		->get();

		// $kanagata_lifetime = db::select("SELECT
		// 	*,
		// 	mp_kanagata_logs.id AS kanagata_lifetime_id 
		// FROM
		// 	mp_kanagata_logs
		// 	JOIN employee_groups ON employee_groups.employee_id = mp_kanagata_logs.pic
		// 	JOIN employees ON employee_groups.employee_id = employees.employee_id
		// 	JOIN mp_materials ON mp_kanagata_logs.material_number = mp_materials.material_number 
		// WHERE
		// 	DATE( mp_kanagata_logs.date ) BETWEEN '".$first."' 
		// 	AND '".$now."' 
		// ORDER BY
		// 	mp_kanagata_logs.id DESC");

		$data = array(
                	'process' => $process,
                	'role_code' => Auth::user()->role_code,
                	// 'kanagata_lifetime' => $kanagata_lifetime,
                	'kanagata' => $kanagata,
                	'username' => $username,
                	'machine' => $machine);
		return view('press.report_kanagata_lifetime',$data)->with('page', 'Press Machine Kanagata Lifetime')->with('title_jp', "??");
	}

	public function filter_report_kanagata_lifetime(Request $request){

		  $date_from = $request->get('date_from');
	      $date_to = $request->get('date_to');
	      $datenow = date('Y-m-d');

	      if($request->get('date_to') == null){
	        if($request->get('date_from') == null){
	          $date = "";
	        }
	        elseif($request->get('date_from') != null){
	          $date = "where date BETWEEN '".$date_from."' and '".$datenow."'";
	        }
	      }
	      elseif($request->get('date_to') != null){
	        if($request->get('date_from') == null){
	          $date = "where date <= '".$date_to."'";
	        }
	        elseif($request->get('date_from') != null){
	          $date = "where date BETWEEN '".$date_from."' and '".$date_to."'";
	        }
	      }

	    $username = Auth::user()->username;

	    $kanagata = MpKanagata::where('process', 'like', '%Forging%')
		->select('mp_kanagatas.material_number', 'mp_kanagatas.material_name', 'mp_kanagatas.material_description','mp_kanagatas.punch_die_number', 'mp_kanagatas.id', 'mp_kanagatas.part')
		->distinct()
		->get();

		$process = DB::SELECT("SELECT DISTINCT(process_desc) FROM `mp_processes` where remark = 'Press'");

		$machine = DB::SELECT("SELECT * FROM `mp_machines` where remark = 'Press'");


		$kanagata_lifetime = db::select("select *,mp_kanagata_logs.id as kanagata_lifetime_id
			from mp_kanagata_logs
			join employee_syncs on mp_kanagata_logs.pic = employee_syncs.employee_id
			join mp_materials on mp_kanagata_logs.material_number= mp_materials.material_number
			".$date."
			ORDER BY mp_kanagata_logs.id desc");

		$data = array(
                	'process' => $process,
                	'role_code' => Auth::user()->role_code,
                	'kanagata_lifetime' => $kanagata_lifetime,
					'username' => $username,
					'kanagata' => $kanagata,
                	'machine' => $machine);
		return view('press.report_kanagata_lifetime',$data)->with('page', 'Press Machine Kanagata Lifetime')->with('title_jp', "??");
	}

	function getkanagatalifetime(Request $request)
    {
          try{
            if(str_contains($request->get('kanagata'),'Punch')){
            	$detail = MpKanagataLog::where('punch_number',$request->get("kanagata_number"))->orderBy('id', 'DESC')->first();
	            $data = array('kanagata_log_id' => $detail->id,
	            			  'date' => $detail->date,
	                          'pic' => $detail->pic,
	                          'pic_name' => $detail->employee_pic->name,
	                          'shift' => $detail->shift,
	                          'product' => $detail->product,
	                          'material_number' => $detail->material_number,
	                          'part' => $detail->material->material_name,
	                          'process' => $detail->process,
	                      	  'machine' => $detail->machine,
	                      		'punch_number' => $detail->punch_number,
	                      		'die_number' => $detail->die_number,
	                      		'punch_value' => $detail->punch_value,
	                      		'die_value' => $detail->die_value,
	                      		'punch_total' => $detail->punch_total,
	                      		'die_total' => $detail->die_total,
	                      		'start_time' => $detail->start_time,
	                      		'end_time' => $detail->end_time);
            }
            else{
            	$detail = MpKanagataLog::where('die_number',$request->get("kanagata_number"))->orderBy('id', 'DESC')->first();
	            $data = array('kanagata_log_id' => $detail->id,
	            			  'date' => $detail->date,
	                          'pic' => $detail->pic,
	                          'pic_name' => $detail->employee_pic->name,
	                          'shift' => $detail->shift,
	                          'product' => $detail->product,
	                          'material_number' => $detail->material_number,
	                          'part' => $detail->material->material_name,
	                          'process' => $detail->process,
	                      	  'machine' => $detail->machine,
	                      		'punch_number' => $detail->punch_number,
	                      		'die_number' => $detail->die_number,
	                      		'punch_value' => $detail->punch_value,
	                      		'die_value' => $detail->die_value,
	                      		'punch_total' => $detail->punch_total,
	                      		'die_total' => $detail->die_total,
	                      		'start_time' => $detail->start_time,
	                      		'end_time' => $detail->end_time);
            }

            $response = array(
              'status' => true,
              'data' => $data
            );
            return Response::json($response);

          }
          catch (QueryException $beacon){
            $error_code = $beacon->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'datas' => "Name already exist",
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => "Update  Error.",
            );
             return Response::json($response);
            }
        }
    }

    function getprodresult(Request $request)
    {
          try{
        	$detail = MpRecordProd::find($request->get("id"));
            $data = array('prod_result_id' => $detail->id,
            			  'date' => $detail->date,
                          // 'pic' => $detail->pic,
                          // 'pic_name' => $detail->employee_pic->name,
                          'shift' => $detail->shift,
                          'product' => $detail->product,
                          'material_number' => $detail->material_number,
                          'part' => $detail->material->material_name,
                          'process' => $detail->process,
                      	  'machine' => $detail->machine,
                      		'punch_number' => $detail->punch_number,
                      		'die_number' => $detail->die_number,
                      		'data_ok' => $detail->data_ok,
                      		'punch_value' => $detail->punch_value,
                      		'die_value' => $detail->die_value,
                      		'start_time' => $detail->start_time,
                      		'end_time' => $detail->end_time,
                      		'lepas_molding' => $detail->lepas_molding,
                      		'pasang_molding' => $detail->pasang_molding,
                      		'process_time' => $detail->process_time,
                      		'kensa_time' => $detail->kensa_time,
                      		'electric_supply_time' => $detail->electric_supply_time,);

            $response = array(
              'status' => true,
              'data' => $data
            );
            return Response::json($response);

          }
          catch (QueryException $beacon){
            $error_code = $beacon->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'datas' => "Name already exist",
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => "Update  Error.",
            );
             return Response::json($response);
            }
        }
    }

    function updateProdResult(Request $request,$id)
    {
        try{
                $prod_result = MpRecordProd::find($id);

                $date = $prod_result->date;
              	$pic = $prod_result->pic;
              	// $pic_name = $prod_result->employee_pic->name;
              	$shift = $prod_result->shift;
              	$product = $prod_result->product;
              	$material_number = $prod_result->material_number;
              	$part = $prod_result->material->material_name;
              	$process = $prod_result->process;
          	  	$machine = $prod_result->machine;
          		$punch_number = $prod_result->punch_number;
          		$die_number = $prod_result->die_number;
          		$punch_value = $prod_result->punch_value;
          		$die_value = $prod_result->die_value;

          		$kanagatas = DB::SELECT("SELECT
					* 
				FROM
					`mp_kanagata_logs` 
				WHERE
					date = '".$date."' 
					AND pic = '".$pic."' 
					AND shift = '".$shift."' 
					AND product = '".$product."' 
					AND material_number = '".$material_number."' 
					AND machine = '".$machine."' 
					AND punch_number = '".$punch_number."' 
					AND die_number = '".$die_number."' 
					AND punch_value = '".$punch_value."' 
					AND die_value = '".$die_value."'");

                foreach ($kanagatas as $key) {
                	$id_kanagata = $key->id;
                }

                $prod_result->date = $request->get('date');
                // $prod_result->pic = $request->get('pic');
                $prod_result->electric_supply_time = $request->get('electric_time');
                $prod_result->machine = $request->get('mesin');
                $prod_result->save();

                $kanagata = MpKanagataLog::find($id_kanagata);

                $kanagata->date = $request->get('date');
                // $kanagata->pic = $request->get('pic');
                $kanagata->machine = $request->get('mesin');
                $kanagata->save();

              $response = array(
                'status' => true,
              );

              return Response::json($response);
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    function updateKanagataLifetime(Request $request,$id)
    {
        try{
                $kanagata_lifetime = MpKanagataLog::find($id);
                $kanagata_lifetime->punch_total = $request->get('punch_total');
                $kanagata_lifetime->die_total = $request->get('die_total');
                $kanagata_lifetime->save();

               $response = array(
                'status' => true,
              );
              return Response::json($response);
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    function reset(Request $request)
    {
        try{
        	$kanagata = $request->get('kanagata');
        	$kanagata_number = $request->get('kanagata_number');
        	if ($kanagata == 'Punch') {
        		$kanagata_lifetime = MpKanagataLog::where('punch_number',$kanagata_number)->get();
	        	foreach ($kanagata_lifetime as $key) {
	        		$id_kanagata = $key->id;
	        		$kanagata_lifetime2 = MpKanagataLog::find($id_kanagata);
	                $kanagata_lifetime2->punch_status = 'Close';
	                $kanagata_lifetime2->save();
	        	}
        	}
        	elseif ($kanagata == 'Dies') {
        		$kanagata_lifetime = MpKanagataLog::where('die_number',$kanagata_number)->get();
	        	foreach ($kanagata_lifetime as $key) {
	        		$id_kanagata = $key->id;
	        		$kanagata_lifetime2 = MpKanagataLog::find($id_kanagata);
	                $kanagata_lifetime2->die_status = 'Close';
	                $kanagata_lifetime2->save();
	        	}
        	}
               $response = array(
                'status' => true,
              );
              return Response::json($response);
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    function resetPeriodik(Request $request)
    {
        try{
        	$part = $request->get('part');
        	$part_number = $request->get('part_number');
        	$reset = MpKanagata::where('part','like','%'.$part.'%')->where('punch_die_number',$part_number)->first();
        	$reset->qty_check = '0';
        	$reset->qty_maintenance = $reset->qty_maintenance + 1;
        	$reset->save();

            $response = array(
            	'status' => true,
            );
            return Response::json($response);
        }catch(\Exception $e){
          $response = array(
            'status' => false,
            'message' => $e->getMessage(),
          );
          return Response::json($response);
        }
    }

    public function create_kanagata_lifetime(Request $request)
    {
    	try {
    		$id_user = Auth::id();

    		MpKanagataLog::create([
                'date' => $request->get('date'),
                'pic' => $request->get('pic'),
                'product' => $request->get('product'),
                'machine' => $request->get('machine'),
                'shift' => $request->get('shift'),
                'material_number' => $request->get('material_number'),
                'process' => $request->get('process'),
                'punch_number' => $request->get('punch_number'),
                'die_number' => $request->get('die_number'),
                'plate_number' => $request->get('plate_number'),
                'start_time' => date('Y-m-d H:i:s'),
                'end_time' => date('Y-m-d H:i:s'),
                'punch_value' => $request->get('punch_value'),
                'die_value' => $request->get('die_value'),
                'plate_value' => $request->get('plate_value'),
                'punch_total' => $request->get('punch_total'),
                'die_total' => $request->get('die_total'),
                'plate_total' => $request->get('plate_total'),
                'punch_status' => 'Running',
                'die_status' => 'Running',
                'plate_status' => 'Running',
                'created_by' => $id_user
          ]);
			MpRecordProd::create([
                'date' => $request->get('date'),
                'pic' => $request->get('pic'),
                'product' => $request->get('product'),
                'machine' => $request->get('machine'),
                'shift' => $request->get('shift'),
                'material_number' => $request->get('material_number'),
                'process' => $request->get('process'),
                'punch_number' => $request->get('punch_number'),
                'die_number' => $request->get('die_number'),
                'plate_number' => $request->get('plate_number'),
                'start_time' => date('Y-m-d H:i:s'),
                'end_time' => date('Y-m-d H:i:s'),
                'lepas_molding' => '00:00:00',
                'pasang_molding' => '00:00:00',
                'process_time' => '00:00:00',
                'kensa_time' => '00:00:00',
                'electric_supply_time' => '00:00:00',
                'data_ok' => $request->get('punch_value'),
                'punch_value' => $request->get('punch_value'),
                'die_value' => $request->get('punch_value'),
                'plate_value' => $request->get('punch_value'),
                'created_by' => $id_user
            ]);

		  $response = array(
            'status' => true,
          );
          return Response::json($response);
    	} catch (QueryException $e) {
    		$response = array(
               'status' => false,
               'message' => $e->getMessage(),
            );
            return Response::json($response);
    	}
    }

    public function fetchKanagata(Request $request)
    {
    	try {
    		$date_from = $request->get('date_from');
		      $date_to = $request->get('date_to');
		      $datenow = date('Y-m-d');

		      if($request->get('date_to') == null){
		        if($request->get('date_from') == null){
		          $date = "";
		        }
		        elseif($request->get('date_from') != null){
		          $date = "AND date BETWEEN '".$date_from."' and '".$datenow."'";
		        }
		      }
		      elseif($request->get('date_to') != null){
		        if($request->get('date_from') == null){
		          $date = "AND date <= '".$date_to."'";
		        }
		        elseif($request->get('date_from') != null){
		          $date = "AND date BETWEEN '".$date_from."' and '".$date_to."'";
		        }
		      }

		      $whereprocess = "";
		      if ($request->get('process') != '') {
		      	$whereprocess = "AND mp_kanagata_logs.process like '%".$request->get("process")."%'";
		      }

		     $kanagata_lifetime = db::select("select *,mp_kanagata_logs.id as kanagata_lifetime_id,
		     	mp_kanagata_logs.process AS process_detail
			from mp_kanagata_logs
			join employee_syncs on mp_kanagata_logs.pic = employee_syncs.employee_id
			join mp_materials on mp_kanagata_logs.material_number= mp_materials.material_number
			WHERE mp_kanagata_logs.deleted_at is null
			".$date."
			".$whereprocess."
			ORDER BY mp_kanagata_logs.id desc");

    		$response = array(
               'status' => true,
               'message' => 'Success Get Data',
               'kanagata' => $kanagata_lifetime
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

    public function excelKanagataLastData(Request $request)
    {
    	// try {
		     $kanagata_lifetime = db::select("SELECT
				a.material_number,
				a.material_name,
				a.material_description,
				a.product,
				a.punch_die_number,
				a.part,
			COALESCE(IF
				(
					a.part LIKE '%PUNCH%',(
					SELECT
						punch_total 
					FROM
						mp_kanagata_logs 
					WHERE
						a.punch_die_number = punch_number 
					ORDER BY
						mp_kanagata_logs.id DESC 
						LIMIT 1 
						),(
					SELECT
						die_total 
					FROM
						mp_kanagata_logs 
					WHERE
						a.punch_die_number = die_number 
					ORDER BY
						mp_kanagata_logs.id DESC 
						LIMIT 1 
					)) , 0) as last_data
			FROM
				mp_kanagatas a");

		     $data = array(
				'kanagata_lifetime' => $kanagata_lifetime
			);

		     ob_clean();
			Excel::create('Latest Kanagata Lifetime Report', function($excel) use ($data){
				$excel->sheet('Kanagata Lifetime', function($sheet) use ($data) {
					return $sheet->loadView('press.excel_kanagata_lifetime', $data);
				});
			})->export('xlsx');

		     return view('press.excel_kanagata_lifetime',$data);

			// $response = array(
   //             'status' => true,
   //             'message' => 'Export Excel Success',
   //          );
   //          return Response::json($response);

   //  	} catch (\Exception $e) {
   //  		$response = array(
   //             'status' => false,
   //             'message' => $e->getMessage(),
   //          );
   //          return Response::json($response);
   //  	}
    }

    public function indexKanagataLifetime()
    {
    	$title = 'Kanagata Lifetime Monitoring';
		$title_jp = '';

		$product = OriginGroup::get();

        $kanagata = MpKanagata::select('punch_die_number')->distinct()->get();

		return view('press.kanagata_lifetime_monitoring', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'product' => $product,
            'kanagata' => $kanagata,
		))->with('page', 'Kanagata Lifetime Monitoring');
    }

    public function fetchKanagataLifetime(Request $request)
    {
    	try {
    		$product = "";
    		if ($request->get('product') != '') {
    			$product = "AND mp_kanagatas.product = '".$request->get('product')."'";
    		}

    		$process = "";
    		if ($request->get('process') != '') {
    			$process = "AND process like '%".$request->get('process')."%'";
    		}
    		$kanagata = DB::SELECT("SELECT DISTINCT
				( mp_kanagatas.punch_die_number ),
				SPLIT_STRING ( mp_kanagatas.part, ' ', 1 ) AS part,
				product,
				material_name,
				COALESCE ( a.lifetime, 0 ) AS lifetime,
				qty_check,
				qty_check_limit,
				lifetime_limit,
				location
			FROM
				mp_kanagatas
				LEFT JOIN ((
					SELECT DISTINCT
						( punch_number ) AS punch_die_number,
						'PUNCH' AS part,
						max( punch_total ) AS lifetime 
					FROM
						`mp_kanagata_logs` 
					WHERE
						punch_status = 'Running' 
						AND punch_number IS NOT NULL 
						".$process."
					GROUP BY
						punch_number,
						part 
					ORDER BY
						created_at ASC 
						) UNION ALL
					(
					SELECT DISTINCT
						( die_number ) AS punch_die_number,
						'DIE' AS part,
						max( die_total ) AS lifetime 
					FROM
						`mp_kanagata_logs` 
					WHERE
						die_status = 'Running' 
						AND die_number IS NOT NULL 
						".$process."
					GROUP BY
						die_number,
						part 
					ORDER BY
						created_at ASC 
					)) a ON a.punch_die_number = mp_kanagatas.punch_die_number 
				AND a.part = SPLIT_STRING ( mp_kanagatas.part, ' ', 1 )
			WHERE
				mp_kanagatas.status_kanagata is null
				".$process."
				".$product."
			ORDER BY
				qty_check DESC");
    		
    		$response = array(
               'status' => true,
               'kanagata' => $kanagata
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

    public function fetchMachine(Request $request)
    {
    	try {
    		$machine = MpMachine::where('machine_name',$request->get('machine'))->first();
    		$response = array(
               'status' => true,
               'machine' => $machine,
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

    public function inputSetupMolding(Request $request)
    {
    	try {
    		$machine = $request->get('machine');
    		$status = $request->get('status');

    		if ($status == 'PASANG') {
    			$molding = $request->get('molding');
    			$update = MpMachine::where('machine_name',$machine)->first();
    			$update->kanagata_status = $molding;
    			$update->save();
    		}else{
    			$update = MpMachine::where('machine_name',$machine)->first();
    			$update->kanagata_status = null;
    			$update->save();
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

    public function indexTransaction()
    {
    	$title = 'Kanagata Transaction';
		$title_jp = '';

		return view('press.transaction', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Kanagata Transaction');
    }

    public function inputTransaction(Request $request)
    {
    	try {
    		$kanagatas = $request->get('kanagatas');
    		$process = $request->get('process');
    		$submit_id = $request->get('submit_id');
    		$submit_name = $request->get('submit_name');
    		$receive_id = $request->get('receive_id');
    		$receive_name = $request->get('receive_name');
    		$kanagata = null;

    		if ($process == 'IN') {
    			for ($i=0; $i < count($kanagatas); $i++) { 
    				$kanagata = MpKanagata::where('punch_die_number',$kanagatas[$i]['part_number'])->where('part','like','%'.$kanagatas[$i]['part'].'%')->first();
    				$check = DB::connection('ympimis_2')->table('mp_kanagata_transactions')->where('part','like','%'.$kanagatas[$i]['part'].'%')->where('part_number',$kanagatas[$i]['part_number'])->where('remark',null)->first();

    				$code_generator = CodeGenerator::where('note', '=', 'kanagata')->first();
			        $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
			        $transaction_id = $code_generator->prefix.$number;
			        $code_generator->index = $code_generator->index+1;

			        $kanagata->location = 'Maintenance';

			        $input = DB::connection('ympimis_2')->table('mp_kanagata_transactions')->insert([
			        	'material_number' => $kanagata->material_number,
						'material_name' => $kanagata->material_name,
						'material_description' => $kanagata->material_description,
						'process' => $kanagata->process,
						'product' => $kanagata->product,
						'part' => $kanagata->part,
						'part_number' => $kanagata->punch_die_number,
						'last_counter' => $kanagata->qty_check,
			        	'transaction_id' => $transaction_id,
			        	'submit_id' => $submit_id,
			        	'submit_name' => $submit_name,
			        	'submit_maintenance_id' => $receive_id,
			        	'submit_maintenance_name' => $receive_name,
			        	'submit_note' => $kanagatas[$i]['notes'],
			        	'submit_note_detail' => $kanagatas[$i]['note'],
			        	'submit_datetime' => date('Y-m-d H:i:s'),
			        	'created_by' => Auth::user()->id,
			        	'created_at' => date('Y-m-d H:i:s'),
			        	'updated_at' => date('Y-m-d H:i:s'),
			        ]);

			        $kanagata->save();
			        $code_generator->save();
    			}
    			$response = array(
	               'status' => true,
	               'message' => 'Kanagata Siap Dikirim ke Molding'
	            );
	            return Response::json($response);
    		}else{
    			for ($i=0; $i < count($kanagatas); $i++) {
    				$check = DB::connection('ympimis_2')->table('mp_kanagata_transactions')->where('part','like','%'.$kanagatas[$i]['part'].'%')->where('part_number',$kanagatas[$i]['part_number'])->where('remark',null)->update([
						'receive_id' => $receive_id,
						'receive_name' => $receive_name,
						'receive_maintenance_id' => $submit_id,
						'receive_maintenance_name' => $submit_name,
						'receive_note' => $kanagatas[$i]['notes'],
						'receive_note_detail' => $kanagatas[$i]['note'],
						'remark' => 'Closed',
						'receive_datetime' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s')
					]);

					$kanagata = MpKanagata::where('punch_die_number',$kanagatas[$i]['part_number'])->where('part','like','%'.$kanagatas[$i]['part'].'%')->first();
					$kanagata->qty_check = '0';
					$kanagata->qty_maintenance = $kanagata->qty_maintenance + 1;
					$kanagata->location = 'Press';
					$kanagata->save();
    			}
    			$response = array(
	               'status' => true,
	               'message' => 'Kanagata Siap Dipakai di Press'
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

    public function fetchTransaction()
    {
    	try {
    		$transaction = DB::connection('ympimis_2')->table('mp_kanagata_transactions')->where('remark',null)->orderBy('updated_at','desc')->get();
    		$all_kanagata = MpKanagata::select('material_number','part','punch_die_number','qty_check')->where('qty_check','>=',DB::RAW('0.7 * qty_check_limit'))->where('location','=','Press')->orderby('qty_check','desc')->distinct()->get();
    		$response = array(
               'status' => true,
               'transaction' => $transaction,
               'all_kanagata' => $all_kanagata,
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

    public function indexMaintenance()
    {
    	$title = 'Kanagata Maintenance';
		$title_jp = '';

		return view('press.maintenance', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Kanagata Maintenance');
    }

    public function fetchMaintenance(Request $request)
    {
    	try {
    		$maintenance = DB::connection('ympimis_2')->table('mp_kanagata_transactions')->where('remark',null)->get();
    		$temp = DB::connection('ympimis_2')->table('mp_kanagata_maintenance_temps')->get();
    		$response = array(
               'status' => true,
               'maintenance' => $maintenance,
               'temp' => $temp,
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

    public function inputMaintenanceTemp(Request $request)
    {
    	try {
    		$transaction_id = $request->get('transaction_id');
			$start_time = $request->get('start_time');
			$employee_id = $request->get('employee_id');
			$name = $request->get('name');

			$maintenance = DB::connection('ympimis_2')->table('mp_kanagata_transactions')->where('transaction_id',$transaction_id)->first();

    		$input = DB::connection('ympimis_2')->table('mp_kanagata_maintenance_temps')->insert([
    			'transaction_id' => $transaction_id,
	        	'material_number' => $maintenance->material_number,
				'material_name' => $maintenance->material_name,
				'material_description' => $maintenance->material_description,
				'process' => $maintenance->process,
				'product' => $maintenance->product,
				'part' => $maintenance->part,
				'part_number' => $maintenance->part_number,
				'last_counter' => $maintenance->last_counter,
				'employee_id' => $employee_id,
				'start_time' => $start_time,
				'name' => $name,
				'note' => '',
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
               'message' => $e->getMessage(),
            );
            return Response::json($response);
    	}
    }

    public function inputMaintenance(Request $request)
    {
    	try {
    		$transaction_id = $request->get('transaction_id');
    		$note = $request->get('note');

			$transaction = DB::connection('ympimis_2')->table('mp_kanagata_transactions')->where('transaction_id',$transaction_id)->first();
			$maintenance = DB::connection('ympimis_2')->table('mp_kanagata_maintenance_temps')->where('transaction_id',$transaction_id)->first();

    		$input = DB::connection('ympimis_2')->table('mp_kanagata_maintenances')->insert([
    			'transaction_id' => $transaction_id,
	        	'material_number' => $maintenance->material_number,
				'material_name' => $maintenance->material_name,
				'material_description' => $maintenance->material_description,
				'process' => $maintenance->process,
				'product' => $maintenance->product,
				'part' => $maintenance->part,
				'part_number' => $maintenance->part_number,
				'last_counter' => $maintenance->last_counter,
				'employee_id' => $maintenance->employee_id,
				'name' => $maintenance->name,
				'start_time' => $maintenance->start_time,
				'end_time' => date('Y-m-d H:i:s'),
				'note' => $note,
				'created_by' => Auth::user()->id,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
    		]);

    		$transaction = DB::connection('ympimis_2')->table('mp_kanagata_transactions')->where('transaction_id',$transaction_id)->update([
    			'remark' => 'Repaired',
    			'maintenance_id' => $maintenance->employee_id,
    			'maintenance_name' => $maintenance->name,
    			'updated_at' => date('Y-m-d H:i:s')
    		]);

    		$delee = DB::connection('ympimis_2')->table('mp_kanagata_maintenance_temps')->where('transaction_id',$transaction_id)->delete();

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

    public function indexTransactionReport()
    {
    	$title = 'Kanagata Transaction Report';
		$title_jp = '';

		return view('press.transaction_report', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Kanagata Transaction Report');
    }

    public function fetchTransactionReport(Request $request)
    {
    	try {

    		$date_from = $request->get('date_from');
		    $date_to = $request->get('date_to');
		    if ($date_from == "") {
		     if ($date_to == "") {
		      $first = date('Y-m-01');
		      $last = date('Y-m-d');
		    }else{
		      $first = date('Y-m-01');
		      $last = $date_to;
		    }
		  }else{
		   if ($date_to == "") {
		    $first = $date_from;
		    $last = date('Y-m-d');
		  }else{
		    $first = $date_from;
		    $last = $date_to;
		  }
		}
    		$report = DB::connection('ympimis_2')
    		->table('mp_kanagata_transactions')
    		->where('remark','Closed')
    		->where(DB::RAW("DATE_FORMAT(created_at,'%Y-%m-%d')"),'>=',$first)
    		->where(DB::RAW("DATE_FORMAT(created_at,'%Y-%m-%d')"),'<=',$last)
    		->get();
    		$response = array(
               'status' => true,
               'report' => $report
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
}
