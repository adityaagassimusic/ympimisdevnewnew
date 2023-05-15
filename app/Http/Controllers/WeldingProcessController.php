<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use App\CodeGenerator;
use App\Material;
use App\WeldingNgLog;
use App\WeldingCheckLog;
use App\WeldingReworkLog;
use App\WeldingTempLog;
use App\WeldingLog;
use App\WeldingInventory;
use App\MaterialPlantDataList;
use App\Employee;
use App\User;
use App\StandardTime;
use App\Jig;
use App\JigBom;
use App\JigSchedule;
use App\JigKensaCheck;
use App\JigKensa;
use App\EmployeeSync;
use App\JigKensaLog;
use App\JigRepair;
use App\JigRepairLog;
use App\JigPartStock;
use App\SolderingStandardTime;
use App\WorkshopJobOrderLog;
use App\WorkshopJobOrder;
use App\EmployeeGroup;
use App\WeeklyCalendar;
use Carbon\Carbon;
use DateTime;
use FTP;
use File;

use Storage;


class WeldingProcessController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
		$this->location_sx = [
			'phs-sx',
			'phs-visual-sx',
			'hsa-sx',
			'hsa-visual-sx',
			'hsa-dimensi-sx',
			'hts-stamp-sx'
		];
		$this->location_fl = [
			'phs-fl',
			'phs-visual-fl',
			'hsa-fl',
			'hsa-visual-fl',
			'hsa-dimensi-fl',
			'hts-stamp-fl'
		];
		$this->location_cl = [
			'phs-cl',
			'phs-visual-cl',
			'hsa-cl',
			'hsa-visual-cl',
			'hsa-dimensi-cl',
			'hts-stamp-cl'
		];
		$this->hpl = [
			'ASKEY',
			'TSKEY',
			'FLKEY',
			'CLKEY'
		];

		$this->category = [
			'HSA',
			'PHS',
			'BURNER',
			'SPOT',
			'HPP'
		];
		$this->type = [
			'Alto',
			'Tenor',
			'A82Z',
			'Flute',
			'Clarinet'
		];
		$this->fy = db::table('weekly_calendars')->select('fiscal_year')->distinct()->get();
	}

	public function indexWeldingTrend(){
		$emps = db::select("select eg.employee_id, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name` from employee_groups eg
			left join employees e on eg.employee_id = e.employee_id
			where eg.location = 'soldering'
			order by e.`name`");

		return view('processes.welding.display.welding_op_trend', array(
			'title' => 'Welding Operator Trends',
			'title_jp' => '',
			'emps' => $emps
		))->with('page', 'Operator Trends');
	}

	public function indexWeldingEff(){
		return view('processes.welding.display.welding_eff', array(
			'title' => 'Operator Efficiency',
			'title_jp' => '作業者能率',
		))->with('page', 'Operator Efficiency');
	}

	public function indexMasterOperator($location){
		// $title = 'Master Operator Welding';
		// $title_jp = '溶接作業者マスター';

		if ($location == 'sx') {
            $title = 'Master Operator Welding Saxophone';
            $title_jp = '溶接作業者マスター';
        } elseif ($location == 'fl') {
            $title = 'Master Operator Welding Flute';
            $title_jp = '溶接作業者マスター';
        } elseif ($location == 'cl') {
            $title = 'Master Operator Welding Clarinet';
            $title_jp = '溶接作業者マスター';
        }

		$emp = DB::SELECT("SELECT
			* 
			FROM
			`employee_syncs` 
			WHERE
			department = 'Woodwind Instrument - Welding Process (WI-WP) Department' 
		");

		return view('processes.welding.master_operator', array(
			'title' => $title,
			'title_jp' => $title_jp,
            'location' => $location,
            'emp' => $emp,
            'emp2' => $emp,
		))->with('page', 'Master Operator Welding');		
	}

	public function indexMasterKanban($location){

		$work_station = DB::connection('ympimis_2')->select("SELECT DISTINCT work_station FROM weldings");
		$material = DB::connection('ympimis_2')->table('welding_materials')->select('material_number','material_description','material_alias','material_category','material_type')->where('locations',$location)->distinct()->get();
		$material_category = DB::connection('ympimis_2')->table('welding_materials')->select('material_category')->where('locations',$location)->distinct()->get();
		$material_type = DB::connection('ympimis_2')->table('welding_materials')->select('material_type')->where('locations',$location)->distinct()->get();

		$title = "Master Kanban";
		$title_jp = "";

		return view('processes.welding.master_kanban', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'location' => $location,
				'material_category' => $material_category,
				'material_type' => $material_type,
				'material' => $material,
				'material2' => $material,
				'material3' => $material,
				'work_station' => $work_station,
			))->with('page', 'Master Kanban Welding');
	}

	public function indexCurrentWelding(){
		$title = 'Ongoing Welding';
		$title_jp = '溶接中';

		return view('processes.welding.display.current_welding', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Current Welding');		
	}

	public function indexWeldingJig(){
		$jigs = DB::connection('ympimis_2')->select("SELECT DISTINCT
				( jig_id ),
				jig_name 
			FROM
				`jig_processes` 
			WHERE
				category = 'KENSA'");

		$jig_repair = DB::connection('ympimis_2')->select("SELECT DISTINCT
				( jig_kensa_processes.jig_id ),
				jig_processes.jig_name 
			FROM
				`jig_kensa_processes`
				JOIN jig_processes ON jig_kensa_processes.jig_id = jig_processes.jig_id");
		return view('processes.welding.jig.index')->with('page', 'Welding Digital Jig Handling')->with('jigs', $jigs)->with('jig_repair', $jig_repair);
	}

	public function indexWeldingKensaJig(){
		$title = 'Welding Kensa Jig';
		$title_jp = '検査冶具溶接';

		return view('processes.welding.jig.kensa', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Welding Kensa Jig');
	}

	public function indexWeldingRepairJig(){
		$title = 'Welding Repair Jig';
		$title_jp = '溶接冶具の修正';

		return view('processes.welding.jig.repair', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Welding Repair Jig');
	}

	public function indexWeldingKensaJigProcess($jig_id,$from){
		$title = 'Welding Kensa Jig Process';
		$title_jp = 'ロー付け加工治具の検査';

		$jig = DB::connection('ympimis_2')->table('jig_processes')->where('jig_id', '=', $jig_id)->where('category','KENSA')->first();
		if (count($jig) > 0) {
			$part = DB::connection('ympimis_2')->table('jig_bom_processes')->where('jig_parent',$jig->jig_id)->get();
		}

		return view('processes.welding.jig.kensa_process', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employee_id' => Auth::user()->username,
			'name' => Auth::user()->name,
			'jig' => $jig,
			'part' => $part,
			'from' => $from,
		))->with('page', 'Welding Kensa Jig Process');
	}

	public function indexWeldingRepairJigProses($jig_id,$from){
		$title = 'Welding Repair Jig Process';
		$title_jp = 'ロー付け加工治具の修正';

		$jig = DB::connection('ympimis_2')->table('jig_processes')->join('jig_kensa_processes','jig_kensa_processes.jig_id','jig_processes.jig_id')
		->where('jig_processes.jig_id', '=', $jig_id)
		->where('jig_processes.category','KENSA')
		->where('jig_kensa_processes.status','Repair')
		->get();

		if (count($jig) > 0) {
			foreach ($jig as $key) {
				$jig_id = $key->jig_id;
			}

			$part = DB::connection('ympimis_2')->table('jig_bom_processes')->where('jig_bom_processes.jig_parent',$jig_id)->join('jig_part_stock_processes','jig_bom_processes.jig_child','jig_part_stock_processes.jig_id')->get();
		}

		return view('processes.welding.jig.repair_process', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'jig_id' => $jig_id,
			'jig' => $jig,
			'part' => $part,
			'from' => $from,
			'employee_id' => Auth::user()->username,
			'name' => Auth::user()->name,
		))->with('page', 'Welding Repair Jig Process');
	}

	public function indexWeldingJigData(){
		$title = "Welding Jig Data";
		$title_jp = "溶接冶具のデータ";

		return view('processes.welding.jig.data', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Welding Jig Data');
	}

	public function indexWeldingJigDataProcess(){
		$title = "Welding Jig Process Data";
		$title_jp = "ロー付け加工治具のデータ";

		return view('processes.welding.jig.data_process', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Welding Jig Process Data');
	}

	public function indexWeldingJigBom(){
		$title = "Welding Jig BOM";
		$title_jp = "溶接冶具のBOM";

		return view('processes.welding.jig.bom', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Welding Jig BOM');
	}

	public function indexWeldingJigBomProcess(){
		$title = "Welding Jig Process BOM";
		$title_jp = "ロー付け加工治具のBOM";

		return view('processes.welding.jig.bom_process', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Welding Jig Process BOM');
	}

	public function indexWeldingJigSchedule(){
		$title = "Welding Jig Schedule";
		$title_jp = "溶接冶具のスケジュール";

		return view('processes.welding.jig.schedule', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Welding Jig Schedule');
	}

	public function indexWeldingJigScheduleProcess(){
		$title = "Welding Jig Process Schedule";
		$title_jp = "ロー付け加工治具の日程";

		return view('processes.welding.jig.schedule_process', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Welding Jig Process Schedule');
	}

	public function indexWldJigMonitoring()
	{
		$title = 'Kensa Welding Jig Monitoring';
		$title_jp = '溶接冶具の検査の監視';

		return view('processes.welding.jig.monitoring', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Kensa Welding Jig Monitoring');
	}

	public function indexWldJigMonitoringProcess()
	{
		$title = 'Kensa Welding Jig Process Monitoring';
		$title_jp = 'ロー付け加工治具の検査モニタリング';

		return view('processes.welding.jig.monitoring_process', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Kensa Welding Jig Process Monitoring');
	}

	public function indexWeldingKensaPoint()
	{
		$title = 'Point Check Kensa Welding Jig';
		$title_jp = '溶接冶具の検査項目';

		$jig_parent = Jig::where('category','KENSA')->get();
		$jig_child = Jig::get();

		return view('processes.welding.jig.point', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'jig_parent' => $jig_parent,
			'jig_child' => $jig_child,
			'jig_parent2' => $jig_parent,
			'jig_child2' => $jig_child
		))->with('page', 'Point Check Kensa Welding Jig');
	}

	public function indexWeldingKensaPointProcess()
	{
		$title = 'Point Check Kensa Welding Jig Process';
		$title_jp = 'ロー付け加工治具の検査項目';

		$jig_parent = DB::connection('ympimis_2')->table('jig_processes')->where('category','KENSA')->get();
		$jig_child = DB::connection('ympimis_2')->table('jig_processes')->get();

		return view('processes.welding.jig.point_process', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'jig_parent' => $jig_parent,
			'jig_child' => $jig_child,
			'jig_parent2' => $jig_parent,
			'jig_child2' => $jig_child
		))->with('page', 'Point Check Kensa Welding Jig Process');
	}

	public function indexWeldingJigPart()
	{
		$title = 'Kensa Welding Jig Parts';
		$title_jp = '溶接冶具の部品検査';

		$jig_part = Jig::where('category','PART')->get();

		return view('processes.welding.jig.part', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'jig_part' => $jig_part,
			'jig_part2' => $jig_part,
		))->with('page', 'Kensa Welding Jig Parts');
	}

	public function indexWeldingJigPartProcess()
	{
		$title = 'Kensa Welding Jig Process Parts';
		$title_jp = 'ロー付け加工治具部品の検査';

		$jig_part = Jig::where('category','PART')->get();

		return view('processes.welding.jig.part_process', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'jig_part' => $jig_part,
			'jig_part2' => $jig_part,
		))->with('page', 'Kensa Welding Jig Process Parts');
	}

	public function indexKensaJigReport()
	{
		$title = 'Kensa Welding Jig Report';
		$title_jp = '溶接検査治の報告';

		return view('processes.welding.jig.kensa_report', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Kensa Welding Jig Report');
	}

	public function indexRepairJigReport()
	{
		$title = 'Repair Welding Jig Report';
		$title_jp = '溶接処理治具の報告';

		return view('processes.welding.jig.repair_report', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Repair Welding Jig Report');
	}

	public function indexKensaJigReportProcess()
	{
		$title = 'Kensa Welding Jig Process Report';
		$title_jp = 'ロー付け加工治具の検査報告';

		return view('processes.welding.jig.kensa_report_process', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Kensa Welding Jig Process Report');
	}

	public function indexRepairJigReportProcess()
	{
		$title = 'Repair Welding Jig Process Report';
		$title_jp = 'ロー付け加工治具の修正報告';

		return view('processes.welding.jig.repair_report_process', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Repair Welding Jig Process Report');
	}

	public function indexEffHandling(){
		$title = 'Average Working Time';
		$title_jp = '作業時間の平均';
		$locations = $this->location_sx;

		return view('processes.welding.display.eff_handling', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'locations' => $locations
		))->with('page', 'Welding Process');
	}

	public function indexWeldingAdjustment(){
		$title = 'Saxophone Welding Adjustment';
		$title_jp = 'サックス溶接かんばん調整';

		$workstations = db::connection('welding')->select("select distinct ws.ws_name from m_hsa hsa
			left join m_ws ws on ws.ws_id = hsa.ws_id
			order by ws.ws_id asc");

		$materials = db::connection('welding_controller')->select("select welding.id, welding.material_number, welding.type, welding.model, CONCAT(m.`key`,' ',m.model) as nickname, m.material_description from
			(select hsa_id as id, hsa_kito_code as material_number, 'HSA' as type, if(hsa_jenis = 0, 'ALTO', if(hsa_jenis = 1, 'TENOR', 'A82')) as model from m_hsa
			union
			select m_phs.phs_id as id, phs_code as material_number, if(m_phs.phs_ishpp = 1, 'HPP', 'PHS') as type, if(phs_jenis = 0, 'ALTO', if(phs_jenis = 1, 'TENOR', 'A82')) as model from m_phs
			where m_phs.phs_ishpp = 0 and m_phs.phs_code not like '%H%') welding
			left join ympimis.materials m on m.material_number = welding.material_number
			order by welding.type, nickname asc");

		return view('processes.welding.welding_adjustment', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'materials' => $materials,
			'workstations' => $workstations,
		))->with('page', 'welding-queue');
	}

	public function indexWeldingBoard($loc){

		$startA = '07:00:00';
		$finishA = '16:00:00';
		$startB = '15:55:00';
		$finishB = '00:15:00';
		$startC = '23:30:00';
		$finishC = '07:10:00';

		if ($loc == 'hpp-sx') {
			$title = 'HPP Saxophone Welding Board';
			$title_jp = 'HPP サックス溶接加工順';
			return view('processes.welding.display.welding_board_hpp', array(
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
		} elseif ($loc == 'cuci-solder'){
			$title = 'Cuci Asam Saxophone Welding Board';
			$title_jp = ' サックス溶接加工順';
			return view('processes.welding.display.welding_board_cuci_solder', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'loc' => $loc,
				'location' => 'sax',	
				'category' => 'CUCI',
				'startA' => $startA,
				'finishA' => $finishA,
				'startB' => $startB,
				'finishB' => $finishB,
				'startC' => $startC,
				'finishC' => $finishC,
			))->with('page', 'CUCI SOLDER');
		} elseif($loc == 'phs-sx'){
			$title = 'PHS Saxophone Welding Board';
			$title_jp = 'PHS サックス溶接加工順';
			return view('processes.welding.display.welding_board', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'loc' => $loc,
				'location' => 'sax',	
				'category' => 'PHS',
				'startA' => $startA,
				'finishA' => $finishA,
				'startB' => $startB,
				'finishB' => $finishB,
				'startC' => $startC,
				'finishC' => $finishC,
			))->with('page', 'PHS');
		} elseif($loc == 'hsa-sx'){
			$title = 'HSA Saxophone Welding Board';
			$title_jp = 'HSA サックス溶接加工順';
			return view('processes.welding.display.welding_board', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'loc' => $loc,
				'location' => 'sax',	
				'category' => 'HSA',
				'startA' => $startA,
				'finishA' => $finishA,
				'startB' => $startB,
				'finishB' => $finishB,
				'startC' => $startC,
				'finishC' => $finishC,
			))->with('page', 'HSA');
		} elseif ($loc == 'hpp-cl') {
			$title = 'HPP Clarinet Welding Board';
			$title_jp = '';
			return view('processes.welding.display.welding_board_hpp', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'loc' => $loc,
				'location' => 'cl',	
				'category' => 'HPP',
				'startA' => $startA,
				'finishA' => $finishA,
				'startB' => $startB,
				'finishB' => $finishB,
				'startC' => $startC,
				'finishC' => $finishC,
			))->with('page', 'HPP');
		} elseif ($loc == 'cuci-solder-cl'){
			$title = 'Cuci Asam Clarinet Welding Board';
			$title_jp = ' ';
			return view('processes.welding.display.welding_board_cuci_solder', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'loc' => $loc,
				'location' => 'cl',	
				'category' => 'CUCI',
				'startA' => $startA,
				'finishA' => $finishA,
				'startB' => $startB,
				'finishB' => $finishB,
				'startC' => $startC,
				'finishC' => $finishC,
			))->with('page', 'CUCI SOLDER');
		} elseif($loc == 'phs-cl'){
			$title = 'PHS Clarinet Welding Board';
			$title_jp = '';
			return view('processes.welding.display.welding_board', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'loc' => $loc,
				'location' => 'cl',	
				'category' => 'PHS',
				'startA' => $startA,
				'finishA' => $finishA,
				'startB' => $startB,
				'finishB' => $finishB,
				'startC' => $startC,
				'finishC' => $finishC,
			))->with('page', 'PHS');
		} elseif($loc == 'hsa-cl'){
			$title = 'HSA Clarinet Welding Board';
			$title_jp = '';
			return view('processes.welding.display.welding_board', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'loc' => $loc,
				'location' => 'cl',	
				'category' => 'HSA',
				'startA' => $startA,
				'finishA' => $finishA,
				'startB' => $startB,
				'finishB' => $finishB,
				'startC' => $startC,
				'finishC' => $finishC,
			))->with('page', 'HSA');
		} elseif ($loc == 'hpp-fl') {
			$title = 'HPP Flute Welding Board';
			$title_jp = '';
			return view('processes.welding.display.welding_board_hpp', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'loc' => $loc,
				'location' => 'fl',	
				'category' => 'HPP',
				'startA' => $startA,
				'finishA' => $finishA,
				'startB' => $startB,
				'finishB' => $finishB,
				'startC' => $startC,
				'finishC' => $finishC,
			))->with('page', 'HPP');
		} elseif ($loc == 'cuci-solder-fl'){
			$title = 'Cuci Asam Flute Welding Board';
			$title_jp = ' ';
			return view('processes.welding.display.welding_board_cuci_solder', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'loc' => $loc,
				'location' => 'fl',	
				'category' => 'CUCI',
				'startA' => $startA,
				'finishA' => $finishA,
				'startB' => $startB,
				'finishB' => $finishB,
				'startC' => $startC,
				'finishC' => $finishC,
			))->with('page', 'CUCI SOLDER');
		} elseif($loc == 'phs-fl'){
			$title = 'PHS Flute Welding Board';
			$title_jp = '';
			return view('processes.welding.display.welding_board', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'loc' => $loc,
				'location' => 'fl',	
				'category' => 'PHS',
				'startA' => $startA,
				'finishA' => $finishA,
				'startB' => $startB,
				'finishB' => $finishB,
				'startC' => $startC,
				'finishC' => $finishC,
			))->with('page', 'PHS');
		} elseif($loc == 'hsa-fl'){
			$title = 'HSA Flute Welding Board';
			$title_jp = '';
			return view('processes.welding.display.welding_board', array(
				'title' => $title,
				'title_jp' => $title_jp,
				'loc' => $loc,
				'location' => 'fl',	
				'category' => 'HSA',
				'startA' => $startA,
				'finishA' => $finishA,
				'startB' => $startB,
				'finishB' => $finishB,
				'startC' => $startC,
				'finishC' => $finishC,
			))->with('page', 'HSA');
		}
	}

	public function indexWeldingAchievement(){
		$title = 'Welding Group Achievement';
		$title_jp= 'HSAサックス寸法検査';

		$workstations = db::connection('welding')->select("select distinct ws.ws_name from m_hsa hsa
			left join m_ws ws on ws.ws_id = hsa.ws_id
			order by ws.ws_id asc");

		return view('processes.welding.display.welding_group_achievement', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'workstations' => $workstations,
		))->with('page', 'Welding Group Achievement')->with('head', 'Welding Process');
	}

	public function indexWeldingSX(){
		return view('processes.welding.index')->with('page', 'Welding Process SX');
	}

	public function indexWeldingFL(){
		return view('processes.welding.index_fl')->with('page', 'Welding Process FL');
	}

	public function indexWeldingCL(){
		return view('processes.welding.index_cl')->with('page', 'Welding Process CL');
	}

	public function indexWeldingKensa($id){
		$ng_lists = DB::table('ng_lists')->where('location', '=', $id)->where('remark', '=', 'welding')->get();

		if($id == 'hsa-visual-sx'){
			$title = 'HSA Kensa Visual Saxophone';
			$title_jp= 'HSAサックス外観検査';
		}else if($id == 'hsa-visual-fl'){
			$title = 'HSA Kensa Visual Flute';
			$title_jp= 'HSAサックス外観検査';
		}else if($id == 'hsa-visual-cl'){
			$title = 'HSA Kensa Visual Clarinet';
			$title_jp= 'HSAサックス外観検査';
		}

		if($id == 'phs-visual-sx'){
			$title = 'PHS Kensa Visual Saxophone';
			$title_jp= 'HSAサックス外観検査';
		}else if($id == 'phs-visual-fl'){
			$title = 'PHS Kensa Visual Flute';
			$title_jp= 'HSAサックス外観検査';
		}else if($id == 'phs-visual-cl'){
			$title = 'PHS Kensa Visual Clarinet';
			$title_jp= 'HSAサックス外観検査';
		}

		if($id == 'hsa-dimensi-sx'){
			$title = 'HSA Kensa Dimensi Saxophone';
			$title_jp= 'HSAサックス寸法検査';
		}else if($id == 'hsa-dimensi-fl'){
			$title = 'HSA Kensa Dimensi Flute';
			$title_jp= 'HSAサックス寸法検査';
		}else if($id == 'hsa-dimensi-cl'){
			$title = 'HSA Kensa Dimensi Clarinet';
			$title_jp= 'HSAサックス寸法検査';
		}

		return view('processes.welding.kensa', array(
			'ng_lists' => $ng_lists,
			'loc' => $id,
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Process Welding SX')->with('head', 'Welding Process');
	}

	public function indexWeldingResume($id){
		if($id == 'phs-visual-sx'){
			$title = 'PHS Saxophone NG Report';
			$title_jp = 'サックスサーブロー付け不良率のリポート';
		}
		if($id == 'hsa-visual-sx'){
			$title = 'HSA Saxophone NG Report';
			$title_jp = 'サックス集成ロー付け不良率のリポート';
		}
		if($id == 'hsa-dimensi-sx'){
			$title = 'Dimensi Saxophone NG Report';
			$title_jp = 'サックス寸法不良のリポート';
		}
		$fys = $this->fy;

		return view('processes.welding.report.resume', array(
			'loc' => $id,
			'fys' => $fys,
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Process Welding SX')->with('head', 'Welding Process');
	}

	public function indexDisplayProductionResult($id){

		if($id == 'sx'){
			$title = 'Welding Production Result Saxophone';
			$title_jp = '溶接生産高';
			$locations = $this->location_sx;
		}
		if($id == 'cl'){
			$title = 'Welding Production Result Clarinet';
			$title_jp = '溶接生産高';
			$locations = $this->location_cl;
		}
		if($id == 'fl'){
			$title = 'Welding Production Result Flute';
			$title_jp = '溶接生産高';
			$locations = $this->location_fl;
		}

		return view('processes.welding.display.production_result', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'id' => $id,
			'locations' => $locations
		))->with('page', 'Production Result');
	}

	public function indexReportNG($id){

		if ($id == 'sx') {
			$title = 'Not Good Record Saxophone';
			$title_jp = '不良内容';
			$locations = $this->location_sx;
        } elseif ($id == 'fl') {
            $title = 'Not Good Record Flute';
            $title_jp = '不良内容';
			$locations = $this->location_fl;
        } elseif ($id == 'cl') {
            $title = 'Not Good Record Clarinet';
            $title_jp = '不良内容';
			$locations = $this->location_cl;
        }

		return view('processes.welding.report.not_good', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'id' => $id,
			'locations' => $locations
		))->with('head', 'Welding Process');
	}

	public function indexReportHourly(){
		$locations = $this->location_sx;

		return view('processes.welding.report.hourly_report', array(
			'title' => 'Hourly Report',
			'title_jp' => '',
			'locations' => $locations
		))->with('page', 'Hourly Report');
	}

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

		return view('processes.welding.display.ng_rate', array(
			'title' => 'NG Rate',
			'title_jp' => '不良率',
			'locations' => $locations,
            'id' => $id
		))->with('page', 'Welding Process');
	}

	public function indexOpRate($id){
		if ($id == 'sx') {
			$title = 'NG Rate by Operator Saxophone';
			$title_jp = '作業者不良率';
			$locations = $this->location_sx;
        } elseif ($id == 'fl') {
        	$title = 'NG Rate by Operator Flute';
			$title_jp = '作業者不良率';
			$locations = $this->location_fl;
        } elseif ($id == 'cl') {
        	$title = 'NG Rate by Operator Clarinet';
			$title_jp = '作業者不良率';
			$locations = $this->location_cl;
        }

		return view('processes.welding.display.op_rate', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'id' => $id,
			'locations' => $locations
		))->with('page', 'Welding Process');
	}

	public function indexOpAnalysis(){
		$title = 'Welding OP Analysis';
		$title_jp = '溶接作業者の分析';

		$locations = $this->location_sx;

		return view('processes.welding.display.op_analysis', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'locations' => $locations
		))->with('page', 'Welding OP Analysis');
	}

	public function indexWeldingOpEff(){
		return view('processes.welding.display.welding_op_eff', array(
			'title' => 'Operator Overall Efficiency',
			'title_jp' => '作業者全体能率',
		))->with('page', 'Operator Overall Efficiency');
	}

	public function indexProductionResult(){
		$locations = $this->location_sx;

		return view('processes.welding.report.production_result', array(
			'title' => 'Production Result',
			'title_jp' => '生産実績',
			'locations' => $locations
		))->with('page', 'Welding Process');		
	}

	public function indexResumeKanban()
	{
		$title = 'Welding Resume Kanban';
		$title_jp = '半田付けかんばんのまとめ';

		return view('processes.welding.report.resume_kanban', array(
			'title' => $title,
			'title_jp' => $title_jp,
		))->with('page', 'Welding Resume Kanban')->with('head','Resume Kanban');
	}

	public function fetchWeldingData(Request $request){
		$jigs = Jig::orderBy('jig_id', 'asc')->get();

		return DataTables::of($jigs)
		->addColumn('action', function($jigs){
			return '
			<button class="btn btn-xs btn-info" data-toggle="tooltip" title="Details" onclick="modalView('.$materials->id.')">View</button>
			<button class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit" onclick="modalEdit('.$materials->id.')">Edit</button>
			<button class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete" onclick="modalDelete('.$materials->id.',\''.$materials->material_number.'\')">Delete</button>';
		})
		->rawColumns(['action' => 'action'])
		->make(true);
	}

	public function fetchMasterOperator(Request $request)
	{
		$lists = DB::connection('ympimis_2')->table('welding_operators')->select('welding_operators.*',DB::RAW('IF
						(
							LENGTH(
							CONV( tag, 16, 10 )) < 10,
							LPAD( CONV( tag, 16, 10 ), 10, 0 ),
						CONV( tag, 16, 10 )) as tags'))->where('location',$request->get('location'))->get();

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

			$check = DB::connection('ympimis_2')->table('welding_operators')->where('employee_id',$employee_id)->where('location',$location)->first();

			if ($check) {
				$response = array(
					'status' => false,
					'message' => 'Operator Sudah Ada di List'
				);
				return Response::json($response);
			}

			$emp = EmployeeSync::where('employee_id',$employee_id)->first();

			$input = DB::connection('ympimis_2')->table('welding_operators')->insert([
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
		// $welcon = DB::connection('welding_controller')
		// ->table('m_operator')
		// ->where('operator_nik','=',$employee_id)->delete();

		// $wel = DB::connection('welding')
		// ->table('m_operator')
		// ->where('operator_nik','=',$employee_id)->delete();

		$list = DB::connection('ympimis_2')
		->table('welding_operators')
		->where('employee_id','=',$employee_id)
		->delete();

		EmployeeGroup::where('employee_id', $employee_id)->where('location', 'soldering')->forceDelete();

		return redirect('index/welding/operator')->with('status','Success Delete Operator');
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

			$update = DB::connection('ympimis_2')->table('welding_operators')->where('id',$id)->update([
				'employee_id' => $employee_id,
				'name' => $emp->name,
				'tag' => $this->dec2hex($tag),
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

	public function updateKanban(Request $request){
		try{
			$id = $request->get('id');
			$material = $request->get('material');
			$tag = $request->get('tag');
			$no_kanban = $request->get('no_kanban');
			$barcode = $request->get('barcode');

			$welding_materials = DB::connection('ympimis_2')->table('welding_materials')->where('material_number',explode('_', $material)[0])->where('material_category',explode('_', $material)[1])->where('material_type',explode('_', $material)[2])->first();

			$update = DB::connection('ympimis_2')->table('welding_tags')->where('id',$id)->update([
				'material_number' => explode('_', $material)[0],
				'material_description' => $welding_materials->material_description,
				'material_category' => explode('_', $material)[1],
				'material_type' => explode('_', $material)[2],
				'tag' => $this->dec2hex($tag),
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

			$welding_materials = DB::connection('ympimis_2')->table('welding_materials')->where('material_number',explode('_', $material)[0])->where('material_category',explode('_', $material)[1])->where('material_type',explode('_', $material)[2])->first();

			$update = DB::connection('ympimis_2')->table('welding_tags')->insert([
				'material_number' => explode('_', $material)[0],
				'material_description' => $welding_materials->material_description,
				'material_category' => explode('_', $material)[1],
				'material_type' => explode('_', $material)[2],
				'tag' => $this->dec2hex($tag),
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
			$delete = DB::connection('ympimis_2')->table('welding_tags')->where('id',$id)->delete();
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

	public function fetchShowEdit(Request $request){
		$loc = $request->get('loc');
		if ($loc == 'hsa-sx') {
			$lists = DB::connection('welding_controller')->select('SELECT
				hsa_id AS id,
				hsa_kito_code AS gmc,
				hsa_name AS gmcdesc,
				m_ws.ws_id AS id_ws,
				ws_name AS ws_name,
				hsa_qty AS qty,
				hsa_jenis AS jenis,
				std.time AS std_time 
				FROM
				m_hsa
				LEFT JOIN m_ws ON m_ws.ws_id = m_hsa.ws_id
				LEFT JOIN ympimis.standard_times std ON std.material_number = m_hsa.hsa_kito_code
				where hsa_id = '. $request->get('id'));

		}elseif ($loc == 'phs-sx') {
			$lists = DB::connection('welding_controller')->select("SELECT
				phs_id AS id,
				phs_code AS gmc,
				phs_name AS gmcdesc,
				m_ws.ws_id AS id_ws,
				ws_name AS ws_name,
				phs_qty AS qty,
				phs_jenis AS jenis,
				std.time AS std_time 
				FROM
				m_phs
				LEFT JOIN m_ws ON m_ws.ws_id = m_phs.ws_id
				LEFT JOIN ympimis.standard_times std ON std.material_number = m_phs.phs_code
				WHERE
				phs_ishpp = 0 
				AND phs_code NOT LIKE '%H%'
				AND phs_id = ". $request->get('id'));

		}elseif ($loc == 'hpp-sx') {
			$lists = DB::connection('welding_controller')->select("SELECT
				phs_id AS id,
				phs_code AS gmc,
				phs_name AS gmcdesc,
				m_ws.ws_id AS id_ws,
				ws_name AS ws_name,
				phs_qty AS qty,
				phs_jenis AS jenis,
				std.time AS std_time 
				FROM
				m_phs
				LEFT JOIN m_ws ON m_ws.ws_id = m_phs.ws_id
				LEFT JOIN ympimis.standard_times std ON std.material_number = m_phs.phs_code
				WHERE
				phs_ishpp = 1
				AND phs_code NOT LIKE '%H%'
				AND phs_id = ". $request->get('id'));
		}

		$response = array(
			'status' => true,
			'lists' => $lists
		);
		return Response::json($response);
	}

	public function fetchMasterKanban(Request $request){
		try {
			$kanban = DB::connection('ympimis_2')->table('welding_tags')->select('welding_tags.*',DB::RAW('IF
						(
							LENGTH(
							CONV( tag, 16, 10 )) < 10,
							LPAD( CONV( tag, 16, 10 ), 10, 0 ),
						CONV( tag, 16, 10 )) as tags'))->where('location',$request->get('location'));

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

	public function fetchCurrentwelding(){
		$current = db::connection('welding_controller')->select("SELECT mesin.mesin_id, mesin.ws_id, ws.ws_name, mesin.mesin_nama, mesin.operator_nik, e.`name`, datas.part_type, datas.material_number, m.model,m.`key`, datas.sedang, ceil( s.time * v.lot_completion / 60 ) AS std FROM
			(SELECT * FROM m_mesin m WHERE m.mesin_nama like '%Sol#%') AS mesin
			LEFT JOIN
			(SELECT m.mesin_id, m.ws_id, m.mesin_nama, 'PHS' AS part_type, op.operator_nik, m.order_id_sedang_gmc AS material_number, p.proses_sedang_start_date AS sedang FROM m_mesin m
			LEFT JOIN t_proses p ON p.proses_id = m.order_id_sedang
			LEFT JOIN m_operator op ON op.operator_id = m.operator_id
			WHERE	m.flow_id = 1
			AND p.part_type = 1
			UNION
			SELECT m.mesin_id, m.ws_id, m.mesin_nama, 'HSA' AS part_type, op.operator_nik, m.order_id_sedang_gmc AS material_number, p.proses_sedang_start_date AS sedang FROM m_mesin m
			LEFT JOIN t_proses p ON p.proses_id = m.order_id_sedang
			LEFT JOIN m_operator op ON op.operator_id = m.operator_id
			WHERE m.flow_id = 1 
			AND p.part_type = 2) AS datas
			ON mesin.mesin_id = datas.mesin_id
			LEFT JOIN m_ws ws ON ws.ws_id = mesin.ws_id
			LEFT JOIN ympimis.materials m ON m.material_number = datas.material_number
			LEFT JOIN ympimis.employee_syncs e ON e.employee_id = mesin.operator_nik
			LEFT JOIN ympimis.standard_times s ON s.material_number = datas.material_number
			LEFT JOIN ympimis.material_volumes v ON v.material_number = datas.material_number 
			ORDER BY mesin.ws_id ASC");

		$response = array(
			'status' => true,
			'current' => $current,
		);
		return Response::json($response);
	}


	public function fetchWeldingBoardNew(Request $request){
		$loc = $request->get('loc');
		// $location = $request->get('location');
		$category = $request->get('category');
		$origin_group_code = "";

		if (str_contains($request->get('location'),'sax')) {
			$origin_group_code = '043';
		}else if (str_contains($request->get('location'),'fl')) {
			$origin_group_code = '041';
		}else if (str_contains($request->get('location'),'cl')) {
			$origin_group_code = '042';
		}

		$boards = array();
		$indexCuci1 = 0;

		if ($category != 'CUCI') {
			$work_stations = DB::connection('ympimis_2')->select("SELECT
				weldings.work_station,
				device_number,
				device_type,
				employee_id,
				`name`,
				online_time,
				weld_akan.material_number AS akan_material,
				CONCAT( weld_akan.model, ' ', weld_akan.`key` ) AS akan_desc,
				weld_akan.surface AS akan_surface,
				weld_akan.doing_timestamp AS waktu_akan,
				weld_sedang.material_number AS sedang_material,
				CONCAT( weld_sedang.model, ' ', weld_sedang.`key` ) AS sedang_desc,
				weld_sedang.surface AS sedang_surface,
				weld_sedang.doing_timestamp AS waktu_sedang
			FROM
				weldings
				LEFT JOIN (
				SELECT
					work_station,
					device_name,
					weldings.material_number,
					weldings.material_description,
					material_tag,
					ympimis.materials.model,
					ympimis.materials.`key`,
					ympimis.materials.`surface`,
					doing_timestamp
				FROM
					weldings
					LEFT JOIN ympimis.materials ON ympimis.materials.material_number = weldings.material_number 
				WHERE
					device_name LIKE '%Akan%' 
				) AS weld_akan ON weld_akan.work_station = weldings.work_station
				LEFT JOIN (
				SELECT
					work_station,
					device_name,
					weldings.material_number,
					weldings.material_description,
					material_tag,
					ympimis.materials.model,
					ympimis.materials.`key`,
					ympimis.materials.`surface`,
					doing_timestamp
				FROM
					weldings
					LEFT JOIN ympimis.materials ON ympimis.materials.material_number = weldings.material_number 
				WHERE
					device_name LIKE '%Sedang%' 
				) AS weld_sedang ON weld_sedang.work_station = weldings.work_station 
			WHERE
				active_status = 'Active' 
				AND device_type = '".$category."' 
				AND location = '".$loc."' 
			GROUP BY
				work_station,
				device_number,
				device_type,
				employee_id,
				`name`,
				online_time 
			ORDER BY
				display_queue
			");
			foreach ($work_stations as $ws) {
				$dt_now = new DateTime();

				$dt_akan = new DateTime($ws->waktu_akan);
				$akan_time = $dt_akan->diff($dt_now);

				$dt_sedang = new DateTime($ws->waktu_sedang);
				$sedang_time = $dt_sedang->diff($dt_now);

				$lists = '';
				$list_antrian = array();

				$lists = DB::connection('ympimis_2')->select("SELECT
						welding_queues.material_number,
						CONCAT( ympimis.materials.model, ' ', ympimis.materials.`key` ) AS material_description,
						welding_materials.work_station,
						welding_materials.material_category,
						welding_queues.created_at 
					FROM
						welding_queues
						JOIN ympimis.materials ON welding_queues.material_number = ympimis.materials.material_number
						JOIN welding_materials ON welding_materials.material_number = welding_queues.material_number 
					WHERE
						welding_materials.work_station = '".$ws->work_station."'
						AND welding_queues.location IN (
							'hsa-sx',
						'phs-sx',
						'hpp-sx')");

				if (count($lists) > 9) {
					foreach ($lists as $key) {
						if (isset($key)) {
							array_push($list_antrian, '('.$key->material_category.')'.'<br>'.$key->material_number.'<br>'.$key->material_description);
						}else{
							array_push($list_antrian, '<br>');
						}
					}
				}else{
					for ($i=0; $i < 10; $i++) {
						if (isset($lists[$i])) {
							array_push($list_antrian, '('.$lists[$i]->material_category.')'.'<br>'.$lists[$i]->material_number.'<br>'.$lists[$i]->material_description);
						}else{
							array_push($list_antrian, '<br>');
						}
					}
				}

				$board_sedang = '';
				if($ws->sedang_surface != null){
					if($ws->sedang_surface == 'HPP') {
						$board_sedang = $ws->sedang_material.'<br>'.$ws->sedang_desc;
					}else{
						$board_sedang = '('.$ws->sedang_surface.')'.'<br>'.$ws->sedang_material.'<br>'.$ws->sedang_desc;
					}
				}else{
					$board_sedang = '<br>';
				}

				$board_akan = '';		
				if($ws->akan_surface != null){
					$board_akan = '('.$ws->akan_surface.')'.'<br>'.$ws->akan_material.'<br>'.$ws->akan_desc;
				}else{
					$board_akan = '<br>';
				}

				array_push($boards, [
					'ws_name' => $ws->work_station,
					'mesin_name' => 'Sol#'.$ws->device_number,
					'ws' => 'Sol#'.$ws->device_number.'<br>'.$ws->work_station,
					'employee_id' => $ws->employee_id,
					'employee_name' => $ws->name,
					// 'shift' => $ws->shift,
					// 'jam_shift' => $ws->jam_shift,
					'sedang' => $board_sedang,
					'akan' => $board_akan,
					'akan_time' => $akan_time->format('%H:%i:%s'),
					'sedang_time' => $sedang_time->format('%H:%i:%s'),
					'queue_1' => $list_antrian[0],
					'queue_2' => $list_antrian[1],
					'queue_3' => $list_antrian[2],
					'queue_4' => $list_antrian[3],
					'queue_5' => $list_antrian[4],
					'queue_6' => $list_antrian[5],
					'queue_7' => $list_antrian[6],
					'queue_8' => $list_antrian[7],
					'queue_9' => $list_antrian[8],
					'queue_10' => $list_antrian[9],
					'jumlah_urutan' => count($lists)
				]);
			}
		}else{
			$lists = DB::connection('ympimis_2')->select("SELECT
				welding_queues.material_number,
				CONCAT( ympimis.materials.model, ' ', ympimis.materials.`key` ) AS material_description,
				ympimis.materials.surface,
				welding_queues.created_at 
			FROM
				welding_queues
				JOIN ympimis.materials ON welding_queues.material_number = ympimis.materials.material_number 
			WHERE
				welding_queues.location = 'cuci-asam' 
			ORDER BY
				created_at");
			foreach ($lists as $lists) {
				array_push($boards, [
					'queue' => $lists->material_number.'<br>'.$lists->material_description.'<br>'.$lists->created_at
				]);
				$indexCuci1++;
			}
		}

		$response = array(
			'status' => true,
			'loc' => $loc,
			'boards' => $boards,
		);
		return Response::json($response);
	}


	public function fetchWeldingBoard(Request $request){
		$loc = $request->get('loc');
		$boards = array();
		// $list_antrian = array();
		if ($loc == 'hsa-sx') {
			$work_stations = DB::connection('welding_controller')->select("SELECT
				m_mesin.mesin_nama,
				m_ws.ws_name,
				m_mesin.operator_id,
				m_mesin.operator_name,
				m_mesin.operator_nik,
				m_operator.`group` AS shift,
				TIME( m_mesin.operator_change_date ) AS jam_shift,
				m_sedang.surface AS surface_sedang,
				order_id_sedang_gmc AS gmcsedang,
				CONCAT( m_sedang.model, ' ', m_sedang.`key` ) AS sedang_name,
				order_id_sedang_name AS gmcdescsedang,
				order_id_sedang_date AS waktu_sedang,
				m_akan.surface AS surface_akan,
				order_id_akan_gmc AS gmcakan,
				CONCAT( m_akan.model, ' ', m_akan.`key` ) AS akan_name,
				order_id_akan_name AS gmcdescakan,
				order_id_akan_date AS waktu_akan 
				FROM
				m_mesin
				LEFT JOIN m_ws ON m_ws.ws_id = m_mesin.ws_id
				LEFT JOIN m_operator ON m_operator.operator_id = m_mesin.operator_id
				LEFT JOIN ympimis.materials m_akan ON m_akan.material_number = m_mesin.order_id_akan_gmc
				LEFT JOIN ympimis.materials m_sedang ON m_sedang.material_number = m_mesin.order_id_sedang_gmc 
				WHERE
				(m_mesin.mesin_type = 2 
				and m_ws.ws_active = 'Active')
				OR
				(m_ws.ws_name like '%Burner%'
				and m_ws.ws_active = 'Active')
				ORDER BY
				m_ws.ws_id");
		}elseif ($loc == 'phs-sx') {
			$work_stations = DB::connection('welding_controller')->select("SELECT
				m_mesin.mesin_nama,
				m_ws.ws_name,
				m_mesin.operator_id,
				m_mesin.operator_name,
				m_mesin.operator_nik,
				m_operator.`group` AS shift,
				TIME( m_mesin.operator_change_date ) AS jam_shift,
				m_sedang.surface AS surface_sedang,
				order_id_sedang_gmc AS gmcsedang,
				CONCAT( m_sedang.model, ' ', m_sedang.`key` ) AS sedang_name,
				order_id_sedang_name AS gmcdescsedang,
				order_id_sedang_date AS waktu_sedang,
				m_akan.surface AS surface_akan,
				order_id_akan_gmc AS gmcakan,
				CONCAT( m_akan.model, ' ', m_akan.`key` ) AS akan_name,
				order_id_akan_name AS gmcdescakan,
				order_id_akan_date AS waktu_akan
				FROM
				m_mesin
				LEFT JOIN m_ws ON m_ws.ws_id = m_mesin.ws_id
				LEFT JOIN m_operator ON m_operator.operator_id = m_mesin.operator_id
				LEFT JOIN ympimis.materials m_akan ON m_akan.material_number = m_mesin.order_id_akan_gmc
				LEFT JOIN ympimis.materials m_sedang ON m_sedang.material_number = m_mesin.order_id_sedang_gmc
				WHERE
				( m_mesin.mesin_type = 1 AND m_mesin.mesin_nama LIKE '%Sol#%' and m_ws.ws_active = 'Active') 
				OR ( m_mesin.mesin_type = 3 AND m_mesin.mesin_nama LIKE '%Sol#%' and m_ws.ws_active = 'Active') 
				ORDER BY
				m_ws.ws_id");
		}elseif ($loc == 'hpp-sx') {
			$work_stations = DB::connection('welding_controller')->select("SELECT
				m_mesin.mesin_nama,
				m_ws.ws_name,
				m_mesin.operator_id,
				m_mesin.operator_name,
				m_mesin.operator_nik,
				m_operator.`group` AS shift,
				TIME( m_mesin.operator_change_date ) AS jam_shift,
				m_sedang.surface AS surface_sedang,
				order_id_sedang_gmc AS gmcsedang,
				CONCAT( m_sedang.model, ' ', m_sedang.`key` ) AS sedang_name,
				order_id_sedang_name AS gmcdescsedang,
				order_id_sedang_date AS waktu_sedang,
				m_akan.surface AS surface_akan,
				order_id_akan_gmc AS gmcakan,
				CONCAT( m_akan.model, ' ', m_akan.`key` ) AS akan_name,
				order_id_akan_name AS gmcdescakan,
				order_id_akan_date AS waktu_akan 
				FROM
				m_mesin
				LEFT JOIN m_ws ON m_ws.ws_id = m_mesin.ws_id
				LEFT JOIN m_operator ON m_operator.operator_id = m_mesin.operator_id
				LEFT JOIN ympimis.materials m_akan ON m_akan.material_number = m_mesin.order_id_akan_gmc
				LEFT JOIN ympimis.materials m_sedang ON m_sedang.material_number = m_mesin.order_id_sedang_gmc 
				WHERE
				(m_mesin.mesin_type = 1 
				AND m_mesin.department_id = 4 and m_ws.ws_active = 'Active')
				OR
				(m_mesin.mesin_type = 1 
				AND m_ws.ws_id = 27 and m_ws.ws_active = 'Active')
				ORDER BY
				m_ws.department_id desc,m_ws.ws_id");
		}elseif ($loc == 'cuci-solder') {
			$work_stations = DB::connection('welding_controller')->select("SELECT
				a.date as store_date,
				TIME(a.datetime) as store_time,
				a.gmc,
				a.material_description as gmcdesc, 
				'0000-00-00 00:00:00' as waktu_akan,
				'0000-00-00 00:00:00' as waktu_sedang
				FROM
				(
				SELECT
				DATE( order_store_date ) AS date,
				order_store_date AS datetime,
				hsa_name AS material_description,
				hsa_kito_code AS gmc 
				FROM
				`t_before_cuci`
				LEFT JOIN m_hsa ON m_hsa.hsa_id = t_before_cuci.part_id 
				WHERE
				order_status = 0 
				AND part_type = 2 UNION ALL
				SELECT
				DATE( order_store_date ) AS date,
				order_store_date AS datetime,
				phs_name AS material_description,
				phs_code AS gmc 
				FROM
				`t_before_cuci`
				LEFT JOIN m_phs ON m_phs.phs_id = t_before_cuci.part_id 
				WHERE
				order_status = 0 
				AND part_type = 1 
			) a");
		}

		$indexCuci1 = 0;

		foreach ($work_stations as $ws) {
			$dt_now = new DateTime();

			$dt_akan = new DateTime($ws->waktu_akan);
			$akan_time = $dt_akan->diff($dt_now);

			$dt_sedang = new DateTime($ws->waktu_sedang);
			$sedang_time = $dt_sedang->diff($dt_now);

			$lists = '';
			$list_antrian = array();

			if ($loc == 'hsa-sx') {
				
				$lists = DB::connection('welding_controller')->select("SELECT
					queue.*,
					CONCAT( m.model, ' ', m.`key` ) AS `name`,
					if(queue.part_type = 1, 'PHS', 'HSA') as type
					FROM
					(
					SELECT
					proses_id,
					part_id,
					COALESCE ( m_hsa.hsa_kito_code, m_phs.phs_code ) AS hsa_kito_code,
					COALESCE ( m_hsa.hsa_name, m_phs.phs_name ) AS hsa_name,
					COALESCE ( ws_phs.ws_name, ws_hsa.ws_name ) AS ws_name,
					t_proses.part_type,
					antrian_date
					FROM
					t_proses
					LEFT JOIN m_hsa ON m_hsa.hsa_id = t_proses.part_id
					LEFT JOIN m_phs ON m_phs.phs_id = t_proses.part_id
					LEFT JOIN m_ws AS ws_phs ON m_phs.ws_id = ws_phs.ws_id
					LEFT JOIN m_ws AS ws_hsa ON m_hsa.ws_id = ws_hsa.ws_id 
					WHERE
					( t_proses.proses_status = 0 AND t_proses.part_type = 1 AND ws_phs.ws_name = '".$ws->ws_name."' ) 
					OR ( t_proses.proses_status = 0 AND t_proses.part_type = 2 AND ws_hsa.ws_name = '".$ws->ws_name."' ) 
					ORDER BY
					antrian_date ASC
					) queue
					LEFT JOIN ympimis.materials m ON m.material_number = queue.hsa_kito_code
					ORDER BY
					antrian_date ASC ");

				if (count($lists) > 9) {
					foreach ($lists as $key) {
						if (isset($key)) {
							$gmcdesc = explode(' ', $key->hsa_name);
							if (ISSET($gmcdesc[1])) {
								$desc = $gmcdesc[0].' '.$gmcdesc[1];
							}else{
								$desc = $gmcdesc[0];
							}
							array_push($list_antrian, '('.$key->type.')'.'<br>'.$key->hsa_kito_code.'<br>'.$key->name);
						}else{
							array_push($list_antrian, '<br>');
						}
					}
				}else{
					for ($i=0; $i < 10; $i++) {
						if (isset($lists[$i])) {
							$gmcdesc = explode(' ', $lists[$i]->hsa_name);
							if (ISSET($gmcdesc[1])) {
								$desc = $gmcdesc[0].' '.$gmcdesc[1];
							}else{
								$desc = $gmcdesc[0];
							}
							array_push($list_antrian, '('.$lists[$i]->type.')'.'<br>'.$lists[$i]->hsa_kito_code.'<br>'.$lists[$i]->name);
						}else{
							array_push($list_antrian, '<br>');
						}
					}
				}
			}elseif ($loc == 'phs-sx') {
				$lists = DB::connection('welding_controller')->select("SELECT
					queue.*,
					CONCAT( m.model, ' ', m.`key` ) AS `name`,
					if(queue.part_type = 1, 'PHS', 'HSA') as type
					FROM
					(
					SELECT
					proses_id,
					part_id,
					COALESCE ( m_hsa.hsa_kito_code, m_phs.phs_code ) AS phs_code,
					COALESCE ( m_hsa.hsa_name, m_phs.phs_name ) AS phs_name,
					COALESCE ( ws_phs.ws_name, ws_hsa.ws_name ) AS ws_name,
					t_proses.part_type,
					antrian_date
					FROM
					t_proses
					LEFT JOIN m_hsa ON m_hsa.hsa_id = t_proses.part_id
					LEFT JOIN m_phs ON m_phs.phs_id = t_proses.part_id
					LEFT JOIN m_ws AS ws_phs ON m_phs.ws_id = ws_phs.ws_id
					LEFT JOIN m_ws AS ws_hsa ON m_hsa.ws_id = ws_hsa.ws_id 
					WHERE
					( t_proses.proses_status = 0 AND t_proses.part_type = 1 AND ws_phs.ws_name = '".$ws->ws_name."' ) 
					OR ( t_proses.proses_status = 0 AND t_proses.part_type = 2 AND ws_hsa.ws_name = '".$ws->ws_name."' ) 
					ORDER BY
					antrian_date ASC 
					) queue
					LEFT JOIN ympimis.materials m ON m.material_number = queue.phs_code
					ORDER BY
					antrian_date ASC ");
				
				if (count($lists) > 9) {
					foreach ($lists as $key) {
						if (isset($key)) {
							$gmcdesc = explode(' ', $key->phs_name);
							if (ISSET($gmcdesc[1])) {
								$desc = $gmcdesc[0].' '.$gmcdesc[1];
							}else{
								$desc = $gmcdesc[0];
							}
							array_push($list_antrian, '('.$key->type.')'.'<br>'.$key->phs_code.'<br>'.$key->name);
						}else{
							array_push($list_antrian, '<br>');
						}
					}
				}else{
					for ($i=0; $i < 10; $i++) {
						if (isset($lists[$i])) {
							$gmcdesc = explode(' ', $lists[$i]->phs_name);
							if (ISSET($gmcdesc[1])) {
								$desc = $gmcdesc[0].' '.$gmcdesc[1];
							}else{
								$desc = $gmcdesc[0];
							}
							array_push($list_antrian, '('.$lists[$i]->type.')'.'<br>'.$lists[$i]->phs_code.'<br>'.$lists[$i]->name);
						}else{
							array_push($list_antrian, '<br>');
						}
					}
				}
			}elseif ($loc == 'hpp-sx') {
				$lists = DB::connection('welding_controller')->select("SELECT
					queue.*,
					IF(queue.phs_jenis = 0,CONCAT( 'Alto ', m.model, ' ', m.`key` ),IF(queue.phs_jenis = 1,CONCAT( 'Tenor ', m.model, ' ', m.`key`),CONCAT( 'A82Z ', m.model, ' ', m.`key` )) ) AS `name`
					FROM
					(
					SELECT
					proses_id,
					part_id,
					COALESCE ( m_hsa.hsa_kito_code, m_phs.phs_code ) AS phs_code,
					COALESCE ( m_hsa.hsa_name, m_phs.phs_name ) AS phs_name,
					COALESCE ( ws_phs.ws_name, ws_hsa.ws_name ) AS ws_name,
					COALESCE ( m_hsa.hsa_jenis, m_phs.phs_jenis ) AS phs_jenis,
					antrian_date
					FROM
					t_proses
					LEFT JOIN m_hsa ON m_hsa.hsa_id = t_proses.part_id
					LEFT JOIN m_phs ON m_phs.phs_id = t_proses.part_id
					LEFT JOIN m_ws AS ws_phs ON m_phs.ws_id = ws_phs.ws_id
					LEFT JOIN m_ws AS ws_hsa ON m_hsa.ws_id = ws_hsa.ws_id 
					WHERE
					( t_proses.proses_status = 0 AND t_proses.part_type = 1 AND ws_phs.ws_name = '".$ws->ws_name."' ) 
					OR ( t_proses.proses_status = 0 AND t_proses.part_type = 2 AND ws_hsa.ws_name = '".$ws->ws_name."' ) 
					ORDER BY
					antrian_date ASC 
					) queue
					LEFT JOIN ympimis.materials m ON m.material_number = queue.phs_code
					ORDER BY
					antrian_date ASC ");

				if (count($lists) > 9) {
					foreach ($lists as $key) {
						if (isset($key)) {
							$gmcdesc = explode(' ', $key->phs_name);
							if (ISSET($gmcdesc[1])) {
								$desc = $gmcdesc[0].' '.$gmcdesc[1];
							}else{
								$desc = $gmcdesc[0];
							}
							// $hsaname = explode(' ', $key->phs_name);
							// array_push($list_antrian, $key->phs_code.'<br>'.$key->phs_name);
							array_push($list_antrian, $key->phs_code.'<br>'.$key->name);
						}else{
							array_push($list_antrian, '<br>');
						}
					}
				}else{
					for ($i=0; $i < 10; $i++) {
						if (isset($lists[$i])) {
							$gmcdesc = explode(' ', $lists[$i]->phs_name);
							if (ISSET($gmcdesc[1])) {
								$desc = $gmcdesc[0].' '.$gmcdesc[1];
							}else{
								$desc = $gmcdesc[0];
							}
							// $hsaname = explode(' ', $lists[$i]->phs_name);
							// array_push($list_antrian, $lists[$i]->phs_code.'<br>'.$lists[$i]->phs_name);
							array_push($list_antrian, $lists[$i]->phs_code.'<br>'.$lists[$i]->name);
						}else{
							array_push($list_antrian, '<br>');
						}
					}
				}
			}

			if ($loc != 'cuci-solder') {
				$gmcdescsedang = explode(' ', $ws->gmcdescsedang);
				if (ISSET($gmcdescsedang[1])) {
					$descsedang = $gmcdescsedang[0].' '.$gmcdescsedang[1];
				}else{
					$descsedang = $gmcdescsedang[0];
				}

				$gmcdescakan = explode(' ', $ws->gmcdescakan);
				if (ISSET($gmcdescakan[1])) {
					$descakan = $gmcdescakan[0].' '.$gmcdescakan[1];
				}else{
					$descakan = $gmcdescakan[0];
				}

				$board_sedang = '';
				if($ws->surface_sedang != null){

					if($ws->surface_sedang == 'HPP') {
						$board_sedang = $ws->gmcsedang.'<br>'.$ws->sedang_name;
					}else{
						$board_sedang = '('.$ws->surface_sedang.')'.'<br>'.$ws->gmcsedang.'<br>'.$ws->sedang_name;
					}
				}else{
					$board_sedang = '<br>';
				}
				$board_akan = '';		
				if($ws->surface_akan != null){
					$board_akan = '('.$ws->surface_akan.')'.'<br>'.$ws->gmcakan.'<br>'.$ws->akan_name;
				}else{
					$board_akan = '<br>';
				}

				array_push($boards, [
					'ws_name' => $ws->ws_name,
					'mesin_name' => $ws->mesin_nama,
					'ws' => $ws->mesin_nama.'<br>'.$ws->ws_name,
					'employee_id' => $ws->operator_nik,
					'employee_name' => $ws->operator_name,
					'shift' => $ws->shift,
					'jam_shift' => $ws->jam_shift,
					'sedang' => $board_sedang,
					'akan' => $board_akan,
					'akan_time' => $akan_time->format('%H:%i:%s'),
					'sedang_time' => $sedang_time->format('%H:%i:%s'),
					'queue_1' => $list_antrian[0],
					'queue_2' => $list_antrian[1],
					'queue_3' => $list_antrian[2],
					'queue_4' => $list_antrian[3],
					'queue_5' => $list_antrian[4],
					'queue_6' => $list_antrian[5],
					'queue_7' => $list_antrian[6],
					'queue_8' => $list_antrian[7],
					'queue_9' => $list_antrian[8],
					'queue_10' => $list_antrian[9],
					'jumlah_urutan' => count($lists)
				]);
			}else{
				$gmcdesc = explode(' ', $ws->gmcdesc);
				if (ISSET($gmcdesc[1])) {
					$desc = $gmcdesc[0].' '.$gmcdesc[1];
				}else{
					$desc = $gmcdesc[0];
				}
				array_push($boards, [
					'queue' => $ws->gmc.'<br>'.$desc.'<br>'.$ws->store_date.'<br>'.$ws->store_time
				]);
				$indexCuci1++;
			}
		}

		$response = array(
			'status' => true,
			'loc' => $loc,
			'boards' => $boards,
		);
		return Response::json($response);
	}

	public function fetchDetailWeldingBoard(Request $request)
	{
		$loc = $request->get('loc');
		$ws_name = $request->get('ws_name');
		$list_antrian = array();
		if ($loc == 'hsa-sx') {
			$lists = DB::connection('welding_controller')->select("SELECT
				t_proses.proses_id,
				t_proses.part_id,
				COALESCE(m_hsa.hsa_kito_code,m_phs.phs_code) as gmc,
				COALESCE(m_hsa.hsa_name,m_phs.phs_name) as gmcdesc,
				COALESCE(ws_phs.ws_name ,ws_hsa.ws_name) as ws_name
				FROM
				t_proses
				LEFT JOIN m_hsa ON m_hsa.hsa_id = t_proses.part_id
				LEFT JOIN m_phs ON m_phs.phs_id = t_proses.part_id
				LEFT JOIN m_ws as ws_phs ON m_phs.ws_id = ws_phs.ws_id 
				LEFT JOIN m_ws as ws_hsa ON m_hsa.ws_id = ws_hsa.ws_id 
				WHERE
				(t_proses.proses_status = 0
				AND t_proses.part_type = 1 
				AND ws_phs.ws_name = '".$ws_name."' )
				OR
				(t_proses.proses_status = 0 
				AND t_proses.part_type = 2
				AND ws_hsa.ws_name = '".$ws_name."' )
				ORDER BY
				pesanan_id,proses_id ASC");
			
		}elseif ($loc == 'phs-sx') {
			$lists = DB::connection('welding_controller')->select("SELECT
				t_proses.proses_id,
				t_proses.part_id,
				COALESCE(m_hsa.hsa_kito_code,m_phs.phs_code) as gmc,
				COALESCE(m_hsa.hsa_name,m_phs.phs_name) as gmcdesc,
				COALESCE(ws_phs.ws_name ,ws_hsa.ws_name) as ws_name
				FROM
				t_proses
				LEFT JOIN m_hsa ON m_hsa.hsa_id = t_proses.part_id
				LEFT JOIN m_phs ON m_phs.phs_id = t_proses.part_id
				LEFT JOIN m_ws as ws_phs ON m_phs.ws_id = ws_phs.ws_id 
				LEFT JOIN m_ws as ws_hsa ON m_hsa.ws_id = ws_hsa.ws_id 
				WHERE
				(t_proses.proses_status = 0
				AND t_proses.part_type = 1 
				AND ws_phs.ws_name = '".$ws_name."' )
				OR
				(t_proses.proses_status = 0 
				AND t_proses.part_type = 2
				AND ws_hsa.ws_name = '".$ws_name."' )
				ORDER BY
				pesanan_id,proses_id ASC");
		}elseif ($loc == 'hpp-sx') {
			$lists = DB::connection('welding_controller')->select("SELECT
				t_proses.proses_id,
				t_proses.part_id,
				COALESCE(m_hsa.hsa_kito_code,m_phs.phs_code) as gmc,
				COALESCE(m_hsa.hsa_name,m_phs.phs_name) as gmcdesc,
				COALESCE(ws_phs.ws_name ,ws_hsa.ws_name) as ws_name
				FROM
				t_proses
				LEFT JOIN m_hsa ON m_hsa.hsa_id = t_proses.part_id
				LEFT JOIN m_phs ON m_phs.phs_id = t_proses.part_id
				LEFT JOIN m_ws as ws_phs ON m_phs.ws_id = ws_phs.ws_id 
				LEFT JOIN m_ws as ws_hsa ON m_hsa.ws_id = ws_hsa.ws_id 
				WHERE
				(t_proses.proses_status = 0
				AND t_proses.part_type = 1 
				AND ws_phs.ws_name = '".$ws_name."' )
				OR
				(t_proses.proses_status = 0 
				AND t_proses.part_type = 2
				AND ws_hsa.ws_name = '".$ws_name."' )
				ORDER BY
				pesanan_id,proses_id ASC");

		}

		foreach ($lists as $key) {
			array_push($list_antrian, [
				'ws_name' => $key->ws_name,
				'gmc' => $key->gmc,
				'gmcdesc' => $key->gmcdesc,
			]);
		}

		$response = array(
			'status' => true,
			'loc' => $loc,
			'ws_name' => $ws_name,
			'list_antrian' => $list_antrian,
		);
		return Response::json($response);
	}

	public function fetchEffHandling(Request $request){
		$date = '';
		$location = '';
		if(strlen($request->get("tanggal")) > 0){
			$date = date('Y-m-d', strtotime($request->get("tanggal")));
		}else{
			$date = date('Y-m-d');
		}

		if($request->get('location') == 'phs-sx'){
			$time = db::select("select material.material_number, material.hpl, result.phs_name as `key`, material.model, result.operator_nik, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name`, result.actual, result.std, (result.actual-result.std) as diff from 
				(SELECT phs.phs_code, phs.phs_name, op.operator_nik, ceil(avg(timestampdiff(second,p.perolehan_start_date,p.perolehan_finish_date))) as actual, avg(p.perolehan_jumlah * std.time) as std  FROM soldering_db.t_perolehan p
				left join soldering_db.m_phs phs on phs.phs_id = p.part_id
				left join standard_times std on std.material_number = phs.phs_code
				left join soldering_db.m_operator op on op.operator_id = p.operator_id
				where p.flow_id = '1'
				and p.part_type = '1'
				and date(perolehan_finish_date) = '".$date."'
				and p.operator_id <> 0
				group by phs.phs_code, phs.phs_name, op.operator_nik) result
				left join
				(select * from materials
				where mrpc = 's11' and surface like '%PHS%') material
				on material.material_number = result.phs_code
				left join employee_syncs e on e.employee_id = result.operator_nik
				where result.actual > 0
				order by diff desc, material.hpl, result.phs_name, material.model asc");

			$location = "PHS-SAX";

		}else if($request->get('location') == 'hsa-sx'){
			$time = db::select("select material.material_number, material.hpl, material.`key`, material.model, result.operator_nik, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name`, result.actual, result.std, (result.actual-result.std) as diff from
				(SELECT hsa.hsa_kito_code, op.operator_nik, ceil(avg(timestampdiff(second,p.perolehan_start_date,p.perolehan_finish_date))) as actual, avg(p.perolehan_jumlah * std.time) as std  FROM soldering_db.t_perolehan p
				left join soldering_db.m_hsa hsa on hsa.hsa_id = p.part_id
				left join standard_times std on std.material_number = hsa.hsa_kito_code
				left join soldering_db.m_operator op on op.operator_id = p.operator_id
				where p.flow_id = '1'
				and p.part_type = '2'
				and date(perolehan_finish_date) = '".$date."'
				and p.operator_id <> 0
				group by hsa.hsa_kito_code, op.operator_nik) result
				left join
				(select * from materials m
				where m.mrpc = 's21' and m.hpl like '%KEY%') material
				on material.material_number = result.hsa_kito_code
				left join employee_syncs e on e.employee_id = result.operator_nik
				where result.actual > 0
				order by diff desc, material.hpl, material.`key`, material.model asc");

			$location = "HSA-SAX";

		}else{
			$time = db::select("select material.material_number, material.hpl, material.`key`, material.model, result.operator_nik, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name`, result.actual, result.std, (result.actual-result.std) as diff from
				(SELECT hsa.hsa_kito_code, op.operator_nik, ceil(avg(timestampdiff(second,p.perolehan_start_date,p.perolehan_finish_date))) as actual, avg(p.perolehan_jumlah * std.time) as std  FROM soldering_db.t_perolehan p
				left join soldering_db.m_hsa hsa on hsa.hsa_id = p.part_id
				left join standard_times std on std.material_number = hsa.hsa_kito_code
				left join soldering_db.m_operator op on op.operator_id = p.operator_id
				where p.flow_id = '1'
				and p.part_type = '2'
				and date(perolehan_finish_date) = '".$date."'
				and p.operator_id <> 0
				group by hsa.hsa_kito_code, op.operator_nik) result
				left join
				(select * from materials m
				where m.mrpc = 's21' and m.hpl like '%KEY%') material
				on material.material_number = result.hsa_kito_code
				left join employee_syncs e on e.employee_id = result.operator_nik
				where result.actual > 0
				order by diff desc, material.hpl, material.`key`, material.model asc");

			$location = "HSA-SAX";
		}
		
		$response = array(
			'status' => true,
			'date' => $date,
			'time' => $time,
			'location' => $location,
		);
		return Response::json($response);

	}

	public function fetchProductionResult(Request $request){
		$date_from = date('Y-m-d', strtotime($request->get('datefrom')));
		$date_to = date('Y-m-d', strtotime($request->get('dateto')));

		$kensa = 'hsa-dimensi-sx,phs-visual-sx,hsa-visual-sx';

		try {
			if($request->get('location') == 'hts-stamp-sx'){
				$results = DB::SELECT("SELECT
						* 
					FROM
						log_processes 
					WHERE
						process_code = '1' 
						AND origin_group_code = '043' 
						AND DATE( created_at ) >= '".$date_from."' 
						AND DATE( created_at ) <= '".$date_to."'");
			}else if(str_contains($kensa,$request->get('location'))){
				$results = DB::connection('ympimis_2')->SELECT("SELECT
					welding_logs.last_check,
					welding_logs.tag,
					IF
					(
						LENGTH(
						CONV( welding_logs.tag, 16, 10 )) < 10,
						LPAD( CONV( welding_logs.tag, 16, 10 ), 10, 0 ),
					CONV( welding_logs.tag, 16, 10 )) AS tags,
					welding_logs.material_number,
					welding_logs.quantity,
					welding_logs.location,
					welding_logs.finished_at as created_at,
					welding_materials.material_description,
					welding_materials.quantity,
					ympimis.materials.model,
					ympimis.materials.surface,
					ympimis.materials.`key`
				FROM
					`welding_logs` 
					JOIN welding_tags ON welding_tags.tag = welding_logs.tag
					JOIN welding_materials ON welding_materials.material_number = welding_tags.material_number 
					AND welding_tags.material_category = welding_materials.material_category 
					AND welding_tags.material_type = welding_materials.material_type
					JOIN ympimis.materials ON ympimis.materials.material_number = welding_materials.material_number
				WHERE
					welding_logs.location = '".$request->get('location')."' 
					AND DATE( welding_logs.started_at ) >= '".$date_from."' 
					AND DATE( welding_logs.finished_at ) <= '".$date_to."'
					UNION ALL
				SELECT
					welding_details.last_check,
					welding_details.tag,
					IF
					(
						LENGTH(
						CONV( welding_details.tag, 16, 10 )) < 10,
						LPAD( CONV( welding_details.tag, 16, 10 ), 10, 0 ),
					CONV( welding_details.tag, 16, 10 )) AS tags,
					welding_details.material_number,
					welding_details.quantity,
					welding_details.location,
					welding_details.finished_at as created_at,
					welding_materials.material_description,
					welding_materials.quantity,
					ympimis.materials.model,
					ympimis.materials.surface,
					ympimis.materials.`key`
				FROM
					`welding_details` 
					JOIN welding_tags ON welding_tags.tag = welding_details.tag
					JOIN welding_materials ON welding_materials.material_number = welding_tags.material_number 
					AND welding_tags.material_category = welding_materials.material_category 
					AND welding_tags.material_type = welding_materials.material_type
					JOIN ympimis.materials ON ympimis.materials.material_number = welding_materials.material_number
				WHERE
					welding_details.location = '".$request->get('location')."' 
					AND DATE( welding_details.started_at ) >= '".$date_from."' 
					AND DATE( welding_details.finished_at ) <= '".$date_to."'");
			}else{
				$results = DB::connection('ympimis_2')->SELECT("SELECT
					welding_logs.last_check,
					welding_logs.tag,
					IF
					(
						LENGTH(
						CONV( welding_logs.tag, 16, 10 )) < 10,
						LPAD( CONV( welding_logs.tag, 16, 10 ), 10, 0 ),
					CONV( welding_logs.tag, 16, 10 )) AS tags,
					welding_logs.material_number,
					welding_logs.quantity,
					welding_logs.location,
					welding_logs.finished_at as created_at,
					welding_materials.material_description,
					welding_materials.quantity,
					ympimis.materials.model,
					ympimis.materials.surface,
					ympimis.materials.`key`
				FROM
					`welding_logs` 
					JOIN welding_tags ON welding_tags.tag = welding_logs.tag
					JOIN welding_materials ON welding_materials.material_number = welding_tags.material_number 
					AND welding_tags.material_category = welding_materials.material_category 
					AND welding_tags.material_type = welding_materials.material_type
					JOIN ympimis.materials ON ympimis.materials.material_number = welding_materials.material_number
				WHERE
					welding_materials.material_category = '".strtoupper(explode('-', $request->get('location'))[0])."' 
					AND DATE( welding_logs.started_at ) >= '".$date_from."' 
					AND DATE( welding_logs.finished_at ) <= '".$date_to."'
					AND (
					welding_logs.location = 'phs-sx' 
					OR welding_logs.location = 'hsa-sx')
					UNION ALL
				SELECT
					welding_details.last_check,
					welding_details.tag,
					IF
					(
						LENGTH(
						CONV( welding_details.tag, 16, 10 )) < 10,
						LPAD( CONV( welding_details.tag, 16, 10 ), 10, 0 ),
					CONV( welding_details.tag, 16, 10 )) AS tags,
					welding_details.material_number,
					welding_details.quantity,
					welding_details.location,
					welding_details.finished_at as created_at,
					welding_materials.material_description,
					welding_materials.quantity,
					ympimis.materials.model,
					ympimis.materials.surface,
					ympimis.materials.`key`
				FROM
					`welding_details` 
					JOIN welding_tags ON welding_tags.tag = welding_details.tag
					JOIN welding_materials ON welding_materials.material_number = welding_tags.material_number 
					AND welding_tags.material_category = welding_materials.material_category 
					AND welding_tags.material_type = welding_materials.material_type
					JOIN ympimis.materials ON ympimis.materials.material_number = welding_materials.material_number
				WHERE
					welding_materials.material_category = '".strtoupper(explode('-', $request->get('location'))[0])."' 
					AND DATE( welding_details.started_at ) >= '".$date_from."' 
					AND DATE( welding_details.finished_at ) <= '".$date_to."'
					AND (
					welding_details.location = 'phs-sx' 
					OR welding_details.location = 'hsa-sx')");
			}

			$emp = EmployeeSync::get();
			$user = User::get();
			$material = Material::get();
			$response = array(
				'status' => true,
				'datas' => $results,
				'emp' => $emp,
				'user' => $user,
				'material' => $material,
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}

		// return DataTables::of($results)
		// ->make(true);
	}

	public function fetchWeldingResume(Request $request){

		$loc = $request->get('loc');
		
		$bulan = date('m-Y');
		if(strlen($request->get('bulan')) > 0){
			$bulan = $request->get('bulan');
		}

		$fy = $request->get('fy');
		if(strlen($request->get('fy')) == 0){
			$date = db::table('weekly_calendars')
			->where('week_date', date('Y-m-d'))
			->first();

			$fy = $date->fiscal_year;
		}

		$hpl = "";
		if($request->get('fy') == "all"){
			$hpl = "";
		}else if($request->get('fy') == "askey"){
			$hpl = "and materials.hpl = 'ASKEY'";
		}else if($request->get('fy') == "tskey"){
			$hpl = "and materials.hpl = 'ASKEY'";
		}

		$monthly = db::select("select bulan.bulan, ng.qty as ng, cek.qty as cek, ROUND((coalesce(ng.qty, 0) / coalesce(cek.qty, 0) * 100),2) as ng_rate from 
			(select distinct date_format(week_date, '%Y-%m') as bulan from weekly_calendars
			where fiscal_year = '".$fy."'
			) as bulan
			left join
			(select date_format(welding_ng_logs.created_at, '%Y-%m') as bulan, sum(welding_ng_logs.quantity) as qty from welding_ng_logs
			left join materials on materials.material_number = welding_ng_logs.material_number
			where welding_ng_logs.location = '".$loc."'
			".$hpl."
			group by date_format(welding_ng_logs.created_at, '%Y-%m')
			) as ng
			on bulan.bulan = ng.bulan
			left join
			(select date_format(welding_check_logs.created_at, '%Y-%m') as bulan, sum(welding_check_logs.quantity) as qty from welding_check_logs
			left join materials on materials.material_number = welding_check_logs.material_number
			where welding_check_logs.location = '".$loc."'
			".$hpl."
			group by date_format(welding_check_logs.created_at, '%Y-%m')
			) as cek
			on bulan.bulan = cek.bulan
			order by bulan.bulan asc");


		$weekly = db::select("SELECT weeks.week_name, sum(ng.ng) as ng, sum(cek.cek) as cek, round((sum(ng.ng)/sum(cek.cek)*100),2) as ng_rate from
			(SELECT week_name, week_date from weekly_calendars
			where DATE_FORMAT(week_date,'%m-%Y') = '".$bulan."') weeks
			left join
			(SELECT date(welding_ng_logs.created_at) as tgl, sum(welding_ng_logs.quantity) ng from welding_ng_logs
			left join materials on materials.material_number = welding_ng_logs.material_number
			where location = '".$loc."'
			and DATE_FORMAT(welding_ng_logs.created_at,'%m-%Y') = '".$bulan."'
			".$hpl."
			GROUP BY tgl) ng
			on weeks.week_date = ng.tgl
			left join
			(SELECT date(welding_check_logs.created_at) as tgl, sum(welding_check_logs.quantity) cek from welding_check_logs
			left join materials on materials.material_number = welding_check_logs.material_number
			where location = '".$loc."'
			and DATE_FORMAT(welding_check_logs.created_at,'%m-%Y') = '".$bulan."'
			".$hpl."
			GROUP BY tgl) cek
			on weeks.week_date = cek.tgl
			GROUP BY weeks.week_name");

		$daily_alto = db::select("SELECT dates.week_date, ng.ng, cek.cek , round((COALESCE(ng.ng,0)/cek.cek*100),2) as ng_rate from
			(SELECT week_date from weekly_calendars
			where DATE_FORMAT(week_date,'%m-%Y') = '".$bulan."') dates
			left join
			(SELECT date(welding_ng_logs.created_at) as tgl, sum(welding_ng_logs.quantity) ng from welding_ng_logs
			left join materials on materials.material_number = welding_ng_logs.material_number
			where location = '".$loc."'
			and DATE_FORMAT(welding_ng_logs.created_at,'%m-%Y') = '".$bulan."'
			and materials.hpl = 'ASKEY'
			GROUP BY tgl) ng
			on dates.week_date = ng.tgl
			left join
			(SELECT date(welding_check_logs.created_at) as tgl, sum(welding_check_logs.quantity) cek from welding_check_logs
			left join materials on materials.material_number = welding_check_logs.material_number
			where location = '".$loc."'
			and DATE_FORMAT(welding_check_logs.created_at,'%m-%Y') = '".$bulan."'
			and materials.hpl = 'ASKEY'
			GROUP BY tgl) cek
			on dates.week_date = cek.tgl");

		$daily_tenor = db::select("SELECT dates.week_date, ng.ng, cek.cek , round((COALESCE(ng.ng,0)/cek.cek*100),2) as ng_rate from
			(SELECT week_date from weekly_calendars
			where DATE_FORMAT(week_date,'%m-%Y') = '".$bulan."') dates
			left join
			(SELECT date(welding_ng_logs.created_at) as tgl, sum(welding_ng_logs.quantity) ng from welding_ng_logs
			left join materials on materials.material_number = welding_ng_logs.material_number
			where location = '".$loc."'
			and DATE_FORMAT(welding_ng_logs.created_at,'%m-%Y') = '".$bulan."'
			and materials.hpl = 'TSKEY'
			GROUP BY tgl) ng
			on dates.week_date = ng.tgl
			left join
			(SELECT date(welding_check_logs.created_at) as tgl, sum(welding_check_logs.quantity) cek from welding_check_logs
			left join materials on materials.material_number = welding_check_logs.material_number
			where location = '".$loc."'
			and DATE_FORMAT(welding_check_logs.created_at,'%m-%Y') = '".$bulan."'
			and materials.hpl = 'TSKEY'
			GROUP BY tgl) cek
			on dates.week_date = cek.tgl");


		$response = array(
			'status' => true,
			'monthly' => $monthly,
			'weekly' => $weekly,
			'daily_alto' => $daily_alto,
			'daily_tenor' => $daily_tenor,
			'fy' => $fy,
			'bulan' => $bulan
		);
		return Response::json($response);
		
	}


	public function fetchWeldingKeyResume(Request $request){

		$loc = $request->get('loc');
		
		$bulan = date('m-Y');
		if(strlen($request->get('bulan')) > 0){
			$bulan = $request->get('bulan');
		}

		$askey = db::select("select cek.`key`, cek.hpl, cek.qty as cek, ng.qty as ng, round((coalesce(ng.qty,0)/cek.qty*100),2) as ng_rate from
			(select materials.`key`, materials.hpl, sum(welding_check_logs.quantity) as qty from welding_check_logs
			left join materials on materials.material_number = welding_check_logs.material_number
			where date_format(welding_check_logs.created_at, '%m-%Y') = '".$bulan."'
			and welding_check_logs.location = '".$loc."'
			and materials.hpl = 'ASKEY'
			group by materials.`key`, materials.hpl) cek
			left join
			(select materials.`key`, materials.hpl, sum(welding_ng_logs.quantity) as qty from welding_ng_logs
			left join materials on materials.material_number = welding_ng_logs.material_number
			where date_format(welding_ng_logs.created_at, '%m-%Y') = '".$bulan."'
			and welding_ng_logs.location = '".$loc."'
			and materials.hpl = 'ASKEY'
			group by materials.`key`, materials.hpl) ng
			on cek.`key` = ng.`key` and cek.hpl = ng.hpl
			order by ng desc
			limit 10");


		$askey_detail = db::select("select ng_name.`key`, ng_name.ng_name, COALESCE(ng.jml,0) as jml from  
			(select b.`key`, a.ng_name from
			(select ng_name from ng_lists
			where location = '".$loc."') a
			cross join
			(select distinct `key` from materials
			where `key` != ''
			and origin_group_code = '043') b
			order by `key` asc) ng_name
			left join
			(select materials.`key`, welding_ng_logs.ng_name, sum(welding_ng_logs.quantity) as jml from welding_ng_logs
			left join materials on welding_ng_logs.material_number = materials.material_number
			where DATE_FORMAT(welding_ng_logs.created_at,'%m-%Y') = '".$bulan."'
			and welding_ng_logs.location = '".$loc."'
			and materials.hpl = 'ASKEY'
			group by materials.`key`, welding_ng_logs.ng_name) ng
			on ng_name.ng_name = ng.ng_name and ng_name.`key` = ng.`key`
			order by `key` asc");


		$tskey = db::select("select cek.`key`, cek.hpl, cek.qty as cek, ng.qty as ng, round((coalesce(ng.qty,0)/cek.qty*100),2) as ng_rate from
			(select materials.`key`, materials.hpl, sum(welding_check_logs.quantity) as qty from welding_check_logs
			left join materials on materials.material_number = welding_check_logs.material_number
			where date_format(welding_check_logs.created_at, '%m-%Y') = '".$bulan."'
			and welding_check_logs.location = '".$loc."'
			and materials.hpl = 'TSKEY'
			group by materials.`key`, materials.hpl) cek
			left join
			(select materials.`key`, materials.hpl, sum(welding_ng_logs.quantity) as qty from welding_ng_logs
			left join materials on materials.material_number = welding_ng_logs.material_number
			where date_format(welding_ng_logs.created_at, '%m-%Y') = '".$bulan."'
			and welding_ng_logs.location = '".$loc."'
			and materials.hpl = 'TSKEY'
			group by materials.`key`, materials.hpl) ng
			on cek.`key` = ng.`key` and cek.hpl = ng.hpl
			order by ng desc
			limit 10");


		$tskey_detail = db::select("select ng_name.`key`, ng_name.ng_name, COALESCE(ng.jml,0) as jml from  
			(select b.`key`, a.ng_name from
			(select ng_name from ng_lists
			where location = '".$loc."') a
			cross join
			(select distinct `key` from materials
			where `key` != ''
			and origin_group_code = '043') b
			order by `key` asc) ng_name
			left join
			(select materials.`key`, welding_ng_logs.ng_name, sum(welding_ng_logs.quantity) as jml from welding_ng_logs
			left join materials on welding_ng_logs.material_number = materials.material_number
			where DATE_FORMAT(welding_ng_logs.created_at,'%m-%Y') = '".$bulan."'
			and welding_ng_logs.location = '".$loc."'
			and materials.hpl = 'TSKEY'
			group by materials.`key`, welding_ng_logs.ng_name) ng
			on ng_name.ng_name = ng.ng_name and ng_name.`key` = ng.`key`
			order by `key` asc");


		$response = array(
			'status' => true,
			'askey' => $askey,
			'tskey' => $tskey,
			'askey_detail' => $askey_detail,
			'tskey_detail' => $tskey_detail,
			'bulan' => $bulan
		);
		return Response::json($response);

	}


	public function fetchWeldingNgResume(Request $request){
		$loc = $request->get('loc');
		
		$bulan = date('m-Y');
		if(strlen($request->get('bulan')) > 0){
			$bulan = $request->get('bulan');
		}
		
		$askey_ng = db::select("select welding_ng_logs.ng_name, sum(welding_ng_logs.quantity) as qty from welding_ng_logs
			left join materials on materials.material_number = welding_ng_logs.material_number
			where date_format(welding_ng_logs.created_at, '%m-%Y') = '".$bulan."'
			and welding_ng_logs.location = '".$loc."'
			and materials.hpl = 'ASKEY'
			group by welding_ng_logs.ng_name
			order by qty desc");

		$tskey_ng = db::select("select welding_ng_logs.ng_name, sum(welding_ng_logs.quantity) as qty from welding_ng_logs
			left join materials on materials.material_number = welding_ng_logs.material_number
			where date_format(welding_ng_logs.created_at, '%m-%Y') = '".$bulan."'
			and welding_ng_logs.location = '".$loc."'
			and materials.hpl = 'TSKEY'
			group by welding_ng_logs.ng_name
			order by qty desc");

		$response = array(
			'status' => true,
			'askey_ng' => $askey_ng,
			'tskey_ng' => $tskey_ng,
			'bulan' => $bulan
		);
		return Response::json($response);

	}
	

	public function fetchNgRate(Request $request){
		try {
			$now = date('Y-m-d');

			// $ngs = WeldingNgLog::leftJoin('materials', 'materials.material_number', '=', 'welding_ng_logs.material_number')
			// ->orderBy('welding_ng_logs.created_at', 'asc');
			// $checks = WeldingCheckLog::leftJoin('materials', 'materials.material_number', '=', 'welding_check_logs.material_number')
			// ->orderBy('welding_check_logs.created_at', 'asc');
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
				SUM( quantity ) / ( SELECT SUM( welding_check_logs.quantity ) AS total_check FROM welding_check_logs WHERE deleted_at IS NULL ".$addlocation." AND welding_check_logs.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$nextday." 02:00:00' ) * 100 AS rate 
			FROM
				welding_ng_logs 
			WHERE
				created_at BETWEEN '".$yesterday." 06:00:00' 
				AND '".$nextday." 02:00:00' 
				".$addlocation."
			GROUP BY
				ng_name 
			ORDER BY
				jumlah DESC");

			$ngkey = db::connection('ympimis_2')->select("
				select rate.`key`, rate.`check`, rate.ng, rate.rate from (
				select c.`key`, c.jml as `check`, COALESCE(ng.jml,0) as ng,(COALESCE(ng.jml,0)/c.jml*100) as rate 
				from 
				(select mt.`key`, sum(w.quantity) as jml from welding_check_logs w
				left join ympimis.materials mt on mt.material_number = w.material_number
				where w.deleted_at is null
				".$addlocation."
				and w.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$nextday." 02:00:00'
				GROUP BY mt.`key`) c

				left join

				(select mt.`key`, sum(w.quantity) as jml from welding_ng_logs w
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
				COALESCE(SUM(welding_check_logs.quantity),0) as total_check,

				COALESCE(
				SUM(welding_check_logs.quantity)
				-
				(Select SUM(quantity) from welding_ng_logs where deleted_at is null ".$addlocation." and welding_ng_logs.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$nextday." 02:00:00'),0) as total_ok,

				COALESCE((select sum(quantity) from welding_ng_logs where deleted_at is null ".$addlocation." and welding_ng_logs.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$nextday." 02:00:00'),0) as total_ng,

				COALESCE((select sum(quantity) from welding_ng_logs where deleted_at is null ".$addlocation." and welding_ng_logs.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$nextday." 02:00:00')
				/ 
				(Select SUM(quantity) from welding_check_logs where deleted_at is null ".$addlocation." and welding_check_logs.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$nextday." 02:00:00') * 100,0) as ng_rate 

				from welding_check_logs 
				where welding_check_logs.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$nextday." 02:00:00' ".$addlocation." and deleted_at is null ");

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
				'ngkey' => $ngkey,
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
					welding_ng_logs.*,
					materials.material_description 
				FROM
					`welding_ng_logs`
					LEFT JOIN (
					SELECT
					IF
						(
							LENGTH(
							CONV( tag, 16, 10 )) < 10,
							LPAD( CONV( tag, 16, 10 ), 10, 0 ),
						CONV( tag, 16, 10 )) AS tag,
						welding_materials.material_description 
					FROM
						welding_tags
						JOIN welding_materials ON welding_materials.material_number = welding_tags.material_number 
						AND welding_materials.material_type = welding_tags.material_type 
					) materials ON materials.tag = welding_ng_logs.tag 
				WHERE
					created_at >= '".$yesterday." 06:00:00' 
					AND created_at <= '".$nextday." 02:00:00' 
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

		$ng_target = db::table("middle_targets")
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
				welding_operators
				LEFT JOIN ( SELECT operator_id, sum( quantity ) AS checks FROM welding_check_logs WHERE date( created_at ) = '".$now."' ".$addlocation." GROUP BY operator_id ) AS checks ON checks.operator_id = welding_operators.employee_id
				LEFT JOIN ( SELECT operator_id, sum( quantity ) AS ng FROM welding_ng_logs WHERE date( created_at ) = '".$now."' ".$addlocation." GROUP BY operator_id ) AS ng ON ng.operator_id = welding_operators.employee_id 
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

		$operator = db::connection('ympimis_2')->select("select g.`shift` as `group`, g.employee_id, g.`name` from welding_operators g where g.location like '%".$request->get('id')."%' order by g.`shift`, g.`name` asc");

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
				welding_check_logs.*,
				ympimis.materials.model,
				ympimis.materials.`key` 
			FROM
				welding_check_logs
				JOIN ympimis.materials ON ympimis.materials.material_number = welding_check_logs.material_number 
			WHERE
				welding_check_logs.operator_id = '".$request->get('employee_id')."' 
				AND DATE( welding_check_logs.created_at ) = '".$tgl."'
				AND remark = 'OK'");

		$ng = db::connection('ympimis_2')->select("SELECT
				welding_ng_logs.*,
				ympimis.materials.model,
				ympimis.materials.`key` 
			FROM
				welding_ng_logs
				JOIN ympimis.materials ON ympimis.materials.material_number = welding_ng_logs.material_number 
			WHERE
				welding_ng_logs.operator_id = '".$request->get('employee_id')."' 
				AND DATE( welding_ng_logs.created_at ) = '".$tgl."'");

		$cek = DB::connection('ympimis_2')->SELECT("SELECT
				welding_check_logs.*,
				ympimis.materials.model,
				ympimis.materials.`key` 
			FROM
				welding_check_logs
				JOIN ympimis.materials ON ympimis.materials.material_number = welding_check_logs.material_number 
			WHERE
				welding_check_logs.operator_id = '".$request->get('employee_id')."' 
				AND DATE( welding_check_logs.created_at ) = '".$tgl."' ");

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

	public function fetchOpAnalysis(Request $request){
		$date_from = $request->get('date_from');
		$date_to = $request->get('date_to');

		if($request->get('date_to') == null){
			if($request->get('date_from') == null){
				$from = date('Y-m')."-01";
				$now = date('Y-m-d');
			}
			elseif($request->get('date_from') != null){
				$from = $request->get('date_from');
				$now = date('Y-m-d');
			}
		}
		elseif($request->get('date_to') != null){
			if($request->get('date_from') == null){
				$from = date('Y-m')."-01";
				$now = $request->get('date_to');
			}
			elseif($request->get('date_from') != null){
				$from = $request->get('date_from');
				$now = $request->get('date_to');
			}
		}

		$actual = db::connection('welding')->select("
			SELECT
			DATE( d.tanggaljam ) AS tgl,
			SUM(IF(TIMESTAMPDIFF(MINUTE, d.perolehan_start_date, d.perolehan_finish_date) < 60, TIMESTAMPDIFF(MINUTE, d.perolehan_start_date, d.perolehan_finish_date), 0)) AS time,
			COUNT( DISTINCT d.operator_id ) AS op,
			ROUND(( SUM(IF(TIMESTAMPDIFF(MINUTE, d.perolehan_start_date, d.perolehan_finish_date) < 60, TIMESTAMPDIFF(MINUTE, d.perolehan_start_date, d.perolehan_finish_date), 0))) / COUNT( DISTINCT d.operator_id ), 2 ) AS act_time,
			ROUND(( SUM(IF(TIMESTAMPDIFF(MINUTE, d.perolehan_start_date, d.perolehan_finish_date) < 60, TIMESTAMPDIFF(MINUTE, d.perolehan_start_date, d.perolehan_finish_date), 0))), 2 ) AS all_time,
			( SELECT target FROM ympimis.middle_targets WHERE target_name = 'Normal Working Time' AND location = 'wld' ) AS normal_time,
			ROUND((
			SELECT
			target 
			FROM
			ympimis.middle_targets 
			WHERE
			target_name = 'Normal Working Time' 
			AND location = 'wld' 
			) - (
			SUM(IF(TIMESTAMPDIFF(MINUTE, d.perolehan_start_date, d.perolehan_finish_date) < 60, TIMESTAMPDIFF(MINUTE, d.perolehan_start_date, d.perolehan_finish_date), 0)))/ COUNT(DISTINCT d.operator_id),
			2 
			) AS loss_time,
			ROUND((
			SELECT
			(SUM( perolehan_jumlah * time.time )/ 60) + 
			(SELECT COALESCE(SUM((perolehan_jumlah * time.time)) / 60,0) 
			FROM
			t_perolehan
			LEFT JOIN m_phs ON m_phs.phs_id = t_perolehan.part_id
			JOIN ympimis.standard_times time ON time.material_number = m_phs.phs_code 
			WHERE
			DATE( tanggaljam ) = tgl
			AND time.location = 'soldering' 
			) 
			FROM
			t_perolehan
			LEFT JOIN m_hsa ON m_hsa.hsa_id = t_perolehan.part_id
			JOIN ympimis.standard_times time ON time.material_number = m_hsa.hsa_kito_code 
			WHERE
			DATE( tanggaljam ) = tgl
			AND time.location = 'soldering'
			)/ COUNT( DISTINCT d.operator_id ),
			2 
			) AS std_time,
			ROUND((
			SELECT
			target 
			FROM
			ympimis.middle_targets 
			WHERE
			target_name = 'Normal Working Time' 
			AND location = 'wld' 
			) - (
			SELECT
			(SUM( perolehan_jumlah * time.time )/ 60)+
			(SELECT COALESCE(SUM((perolehan_jumlah * time.time)) / 60,0) 
			FROM
			t_perolehan
			LEFT JOIN m_phs ON m_phs.phs_id = t_perolehan.part_id
			JOIN ympimis.standard_times time ON time.material_number = m_phs.phs_code 
			WHERE
			DATE( tanggaljam ) = tgl
			AND time.location = 'soldering' 
			) 
			FROM
			t_perolehan
			LEFT JOIN m_hsa ON m_hsa.hsa_id = t_perolehan.part_id
			JOIN ympimis.standard_times time ON time.material_number = m_hsa.hsa_kito_code 
			WHERE
			DATE( tanggaljam ) = tgl
			AND time.location = 'soldering'
			)/ COUNT( DISTINCT d.operator_id ),
			2 
			) AS loss_time_std,
			ROUND((
			SELECT
			(SUM( perolehan_jumlah * time.time )/ 60)+
			(SELECT COALESCE(SUM((perolehan_jumlah * time.time)) / 60,0) 
			FROM
			t_perolehan
			LEFT JOIN m_phs ON m_phs.phs_id = t_perolehan.part_id
			JOIN ympimis.standard_times time ON time.material_number = m_phs.phs_code 
			WHERE
			DATE( tanggaljam ) = tgl
			AND time.location = 'soldering' 
			) 
			FROM
			t_perolehan
			LEFT JOIN m_hsa ON m_hsa.hsa_id = t_perolehan.part_id
			JOIN ympimis.standard_times time ON time.material_number = m_hsa.hsa_kito_code 
			WHERE
			DATE( tanggaljam ) = tgl
			AND time.location = 'soldering'
			),
			2
			) AS all_time_std  
			FROM
			t_perolehan d
			LEFT JOIN ympimis.weekly_calendars ON weekly_calendars.week_date = DATE_FORMAT( d.tanggaljam, '%Y-%m-%d' )
			JOIN m_device ON m_device.device_id = d.device_id
			JOIN m_mesin ON m_device.mesin_id = m_mesin.mesin_id
			WHERE
			DATE( d.tanggaljam ) BETWEEN '".$from."' AND '".$now."' 
			AND d.flow_id = '1'
			AND d.perolehan_start_date != '2000-01-01 00:00:00'
			AND weekly_calendars.remark <> 'H' 
			AND m_mesin.department_id = '2'
			AND d.operator_id != 0
			GROUP BY tgl");

		// $op = db::connection('welding')->select("select DATE(d.tanggaljam_shift) as tgl, SUM(durasi) as act, count(distinct id_operator) as op from t_data_downtime d where DATE_FORMAT(d.tanggaljam_shift,'%Y-%m-%d') between '".$from."' and '".$now."' and  `status` = '1' GROUP BY tgl");

		// $emp = db::select("select g.employee_id, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name` from employee_groups g left join employees e on e.employee_id = g.employee_id
		// 	where g.location = 'soldering'");

		// $datastat = db::select(" ");

		
		$dateTitleNow = date("d-M-Y", strtotime($now));
		$dateTitleFrom = date("d-M-Y", strtotime($from));

		// $location = "";
		// if($request->get('location') != null) {
		// 	$locations = explode(",", $request->get('location'));
		// 	for($x = 0; $x < count($locations); $x++) {
		// 		$location = $location." ".$locations[$x]." ";
		// 		if($x != count($locations)-1){
		// 			$location = $location."&";
		// 		}
		// 	}
		// }else{
		// 	$location = "";
		// }
		// $location = strtoupper($location);
		
		$response = array(
			'status' => true,
			'actual' => $actual,
			'from' => $from,
			'now' => $now,
			'dateTitleNow' => $dateTitleNow,
			'dateTitleFrom' => $dateTitleFrom,
			// 'title' => $location
		);
		return Response::json($response);
	}


	public function fetchWeldingOpEff(Request $request){
		$date = '';
		if(strlen($request->get("tanggal")) > 0){
			$date = date('Y-m-d', strtotime($request->get("tanggal")));
		}else{
			$date = date('Y-m-d');
		}

		$eff_target = db::table("middle_targets")
		->where('location', '=', 'wld')
		->where('target_name', '=', 'Operator Efficiency')
		->select('target')
		->first();

		$rate = db::select("select op.employee_id, concat(SPLIT_STRING(op.`name`, ' ', 1),' ',SPLIT_STRING(op.`name`, ' ', 2)) as `name`, op.`group`, eff.eff, rate.post, (eff.eff * COALESCE(rate.post,1) * 100) as oof  from
			(select operator_nik as employee_id, concat(SPLIT_STRING(operator_name, ' ', 1), ' ', SPLIT_STRING(operator_name, ' ', 2)) as `name`, `group` from soldering_db.m_operator
			where operator_name not like '%PENGGANTI%'
			order by `group`, `name`) op
			left join
			(select solder.operator_nik, sum(solder.actual) as actual, sum(solder.std) as std, sum(solder.std)/sum(solder.actual) as eff from
			(select time.operator_nik, sum(time.actual) actual, sum(time.std) std from
			(select op.operator_nik, (TIMESTAMPDIFF(second,p.perolehan_start_date,p.perolehan_finish_date)) as  actual,
			(s.time * p.perolehan_jumlah) as std from soldering_db.t_perolehan p
			left join soldering_db.m_operator op on p.operator_id = op.operator_id
			left join soldering_db.m_hsa hsa on p.part_id = hsa.hsa_id
			left join ympimis.standard_times s on  s.material_number = hsa.hsa_kito_code
			where p.flow_id = '1'
			and p.part_type = '2'
			and date(p.tanggaljam) = '".$date."') time
			group by time.operator_nik
			union all
			select time.operator_nik, sum(time.actual) actual, sum(time.std) std from
			(select op.operator_nik, (TIMESTAMPDIFF(second,p.perolehan_start_date,p.perolehan_finish_date)) as  actual,
			(s.time * p.perolehan_jumlah) as std from soldering_db.t_perolehan p
			left join soldering_db.m_operator op on p.operator_id = op.operator_id
			left join soldering_db.m_phs phs on p.part_id = phs.phs_id
			left join ympimis.standard_times s on  s.material_number = phs.phs_code
			where p.flow_id = '1'
			and p.part_type = '1'
			and date(p.tanggaljam) = '".$date."') time
			group by time.operator_nik) solder
			group by solder.operator_nik) eff
			on op.employee_id = eff.operator_nik
			left join
			(select cek.operator_id, cek.cek, COALESCE(ng.ng,0) as ng, (cek.cek - COALESCE(ng.ng,0)) as good, ((cek.cek - COALESCE(ng.ng,0))/cek.cek) as post from
			(select operator_id, sum(quantity) as cek from ympimis.welding_check_logs
			where date(welding_time) = '".$date."'
			group by operator_id) cek
			left join
			(select operator_id, sum(quantity) as ng from ympimis.welding_ng_logs
			where date(welding_time) = '".$date."'
			group by operator_id) ng
			on cek.operator_id = ng.operator_id) rate
			on op.employee_id = rate.operator_id
			order by `group`, `name` asc");

		$response = array(
			'status' => true,
			'date' => $date,
			'rate' => $rate,
			'eff_target' => $eff_target->target,
		);
		return Response::json($response);

	}

	public function fetchWeldingEffOngoing(Request $request){
		$date = '';
		if(strlen($request->get("tanggal")) > 0){
			$date = date('Y-m-d', strtotime($request->get("tanggal")));
		}else{
			$date = date('Y-m-d');
		}

		$target = db::connection('welding_controller')->select("SELECT op.`group`, datas.operator_nik, e.`name`, datas.part_type, datas.material_number, m.model, m.`key`,	datas.sedang, ceil( s.time * v.lot_completion / 60 ) AS std FROM
			(SELECT g.employee_id, e.`name`, g.`group` FROM ympimis.employee_groups g
			LEFT JOIN ympimis.employees e ON e.employee_id = g.employee_id
			WHERE g.location = 'soldering' ) op
			LEFT JOIN
			(SELECT m.mesin_id, m.ws_id, m.mesin_nama, 'PHS' AS part_type, op.operator_nik, m.order_id_sedang_gmc AS material_number, p.proses_sedang_start_date AS sedang FROM m_mesin m
			LEFT JOIN t_proses p ON p.proses_id = m.order_id_sedang
			LEFT JOIN m_operator op ON op.operator_id = m.operator_id
			WHERE	m.flow_id = 1
			AND p.part_type = 1
			UNION
			SELECT m.mesin_id, m.ws_id, m.mesin_nama, 'HSA' AS part_type, op.operator_nik, m.order_id_sedang_gmc AS material_number, p.proses_sedang_start_date AS sedang FROM m_mesin m
			LEFT JOIN t_proses p ON p.proses_id = m.order_id_sedang
			LEFT JOIN m_operator op ON op.operator_id = m.operator_id
			WHERE m.flow_id = 1 
			AND p.part_type = 2) AS datas
			ON op.employee_id = datas.operator_nik
			LEFT JOIN ympimis.materials m ON m.material_number = datas.material_number
			LEFT JOIN ympimis.employee_syncs e ON e.employee_id = datas.operator_nik
			LEFT JOIN ympimis.standard_times s ON s.material_number = datas.material_number
			LEFT JOIN ympimis.material_volumes v ON v.material_number = datas.material_number
			ORDER BY op.`group`, op.`name` ASC");

		$response = array(
			'status' => true,
			'date' => $date,
			'target' => $target,
		);
		return Response::json($response);
	}

	public function scanWeldingOperatorToHex(Request $request){

		$tag = $this->dec2hex($request->get('employee_id'));

		$employee = db::connection('welding_controller')
		->table('m_operator')
		->where('operator_code', '=', $tag)
		->first();

		if($employee){
			$response = array(
				'status' => true,
				'message' => 'Logged In',
				'employee' => $employee,
			);
			return Response::json($response);
		}
		else{
			$response = array(
				'status' => false,
				'message' => 'Employee Tag Invalid',
			);
			return Response::json($response);
		}
	}

	public function fetchWeldingOpEffTarget(Request $request){
		$date = '';
		if(strlen($request->get("tanggal")) > 0){
			$date = date('Y-m-d', strtotime($request->get("tanggal")));
		}else{
			$date = date('Y-m-d');
		}

		$eff_target = db::table("middle_targets")
		->where('location', '=', 'wld')
		->where('target_name', '=', 'Operator Efficiency')
		->select('target')
		->first();

		$target = db::connection('welding_controller')->select("select op.`group`, op.employee_id, op.`name`, eff.material_number, CONCAT(m.model,' ',m.`key`) as `key`, eff.finish, eff.act, (std.time*eff.qty) as std, (std.time*eff.qty/eff.act) as eff, eff.`check` from
			(select operator_nik as employee_id, concat(SPLIT_STRING(operator_name, ' ', 1), ' ', SPLIT_STRING(operator_name, ' ', 2)) as `name`, `group` from m_operator
			where operator_name not like '%PENGGANTI%'
			order by `group`, `name` ) op
			left join
			(select op.operator_nik, dl.part_type, hsa.hsa_kito_code as hsa_material_number, phs.phs_code as phs_material_number, IF(dl.part_type = 1, phs.phs_code, hsa.hsa_kito_code) as material_number, dl.finish, dl.perolehan_jumlah, perolehan_jumlah as qty, dl.act, ((dl.perolehan_jumlah * hsa.hsa_timing)/dl.act) as eff, dl.`check` from
			(select a.operator_id, a.part_type, a.part_id, time(a.perolehan_finish_date) as finish, timestampdiff(second, a.perolehan_start_date, a.perolehan_finish_date) as act, a.perolehan_jumlah, a.`check` from
			(select * from t_perolehan
			where date(tanggaljam) = '".$date."'
			and flow_id = '1') a
			left join
			(select * from t_perolehan
			where date(tanggaljam) = '".$date."'
			and flow_id = '1') b
			on (a.operator_id = b.operator_id and a.perolehan_finish_date < b.perolehan_finish_date)
			where b.perolehan_finish_date is null
			order by a.operator_id asc) dl
			left join m_operator op on op.operator_id = dl.operator_id
			left join m_hsa hsa on hsa.hsa_id = dl.part_id
			left join m_phs phs on phs.phs_id = dl.part_id) eff
			on op.employee_id = eff.operator_nik
			left join ympimis.materials m on eff.material_number = m.material_number
			left join ympimis.standard_times std on std.material_number = eff.material_number
			order by op.`group`, op.`name` asc");

		$response = array(
			'status' => true,
			'date' => $date,
			'target' => $target,
			'eff_target' => $eff_target->target,
		);
		return Response::json($response);
	}

	public function fetchReportHourly(Request $request){
		$tanggal = '';
		if(strlen($request->get('date')) > 0){
			$date = date('Y-m-d', strtotime($request->get('date')));
			$tanggal = "DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$date."' and ";
		}else{		
			$date = date('Y-m-d');
			$tanggal = "DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$date."' and ";
		}

		$addlocation = "";
		if($request->get('location') != null) {
			$locations = $request->get('location');
			$location = "";

			for($x = 0; $x < count($locations); $x++) {
				$location = $location."'".$locations[$x]."'";
				if($x != count($locations)-1){
					$location = $location.",";
				}
			}
			$addlocation = "and l.location in (".$location.") ";
		}

		$key = db::select("select DISTINCT SUBSTRING(`key`, 1, 1) as kunci from materials where hpl = 'ASKEY' and issue_storage_location = 'SX21' ORDER BY `key` asc");

		$jam = [
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '00:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '01:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '01:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '03:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '03:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '05:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '05:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '07:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '07:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '09:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '09:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '11:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '11:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '14:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '14:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '16:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '16:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '18:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '18:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '20:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '20:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '22:00:00'",
			"DATE_FORMAT(l.created_at,'%H:%m:%s') >= '22:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '23:59:59'"
		];

		$jam2 = [
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '00:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '01:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '01:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '03:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '03:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '05:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '05:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '07:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '07:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '09:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '09:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '11:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '11:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '14:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '14:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '16:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '16:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '18:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '18:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '20:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '20:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '22:00:00'",
			"DATE_FORMAT(p.tanggaljam,'%H:%m:%s') >= '22:00:00' and DATE_FORMAT(p.tanggaljam,'%H:%m:%s') < '23:59:59'"
		];

		$dataShift3 = [];
		$dataShift1 = [];
		$dataShift2 = [];

		$z3 = [];
		$z1 = [];
		$z2 = [];

		$push_data = [];
		$push_data_z = [];

		if($request->get('location') == 'hsa-sx'){			
			for ($i=0; $i <= 3 ; $i++) {
				$push_data[$i] = db::connection('welding')->select("(select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
					left join m_hsa hsa on p.part_id = hsa.hsa_id
					left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
					where p.part_type = '2'
					and p.flow_id = '1'
					and date(p.tanggaljam) = '".$date."'
					and ".$jam2[$i]."
					and m.hpl = 'ASKEY'
					and m.issue_storage_location = 'SX21'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)
					union
					(select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
					left join m_hsa hsa on p.part_id = hsa.hsa_id
					left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
					where p.part_type = '2'
					and p.flow_id = '1'
					and date(p.tanggaljam) = '".$date."'
					and ".$jam2[$i]."
					and m.hpl = 'TSKEY'
					and m.issue_storage_location = 'SX21'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)");
				array_push($dataShift3, $push_data[$i]);

				$push_data_z[$i] = db::connection('welding')->select("select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
					left join m_hsa hsa on p.part_id = hsa.hsa_id
					left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
					where p.part_type = '2'
					and p.flow_id = '1'
					and date(p.tanggaljam) = '".$date."'
					and ".$jam2[$i]."
					and m.model = 'A82'
					and m.issue_storage_location = 'SX21'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci");
				array_push($z3, $push_data_z[$i]);
			}

			for ($i=4; $i <= 7 ; $i++) {
				$push_data[$i] = db::connection('welding')->select("(select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
					left join m_hsa hsa on p.part_id = hsa.hsa_id
					left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
					where p.part_type = '2'
					and p.flow_id = '1'
					and date(p.tanggaljam) = '".$date."'
					and ".$jam2[$i]."
					and m.hpl = 'ASKEY'
					and m.issue_storage_location = 'SX21'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)
					union
					(select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
					left join m_hsa hsa on p.part_id = hsa.hsa_id
					left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
					where p.part_type = '2'
					and p.flow_id = '1'
					and date(p.tanggaljam) = '".$date."'
					and ".$jam2[$i]."
					and m.hpl = 'TSKEY'
					and m.issue_storage_location = 'SX21'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)");
				array_push($dataShift1, $push_data[$i]);

				$push_data_z[$i] = db::connection('welding')->select("select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
					left join m_hsa hsa on p.part_id = hsa.hsa_id
					left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
					where p.part_type = '2'
					and p.flow_id = '1'
					and date(p.tanggaljam) = '".$date."'
					and ".$jam2[$i]."
					and m.model = 'A82'
					and m.issue_storage_location = 'SX21'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci");
				array_push($z1, $push_data_z[$i]);
			}

			for ($i=8; $i <= 11 ; $i++) {
				$push_data[$i] = db::connection('welding')->select("(select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
					left join m_hsa hsa on p.part_id = hsa.hsa_id
					left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
					where p.part_type = '2'
					and p.flow_id = '1'
					and date(p.tanggaljam) = '".$date."'
					and ".$jam2[$i]."
					and m.hpl = 'ASKEY'
					and m.issue_storage_location = 'SX21'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)
					union
					(select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
					left join m_hsa hsa on p.part_id = hsa.hsa_id
					left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
					where p.part_type = '2'
					and p.flow_id = '1'
					and date(p.tanggaljam) = '".$date."'
					and ".$jam2[$i]."
					and m.hpl = 'TSKEY'
					and m.issue_storage_location = 'SX21'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)");
				array_push($dataShift2, $push_data[$i]);

				$push_data_z[$i] = db::connection('welding')->select("select DATE_FORMAT(p.tanggaljam,'%Y-%m-%d') as tgl, SUBSTRING(m.`key`, 1, 1) as kunci, m.hpl, sum(p.perolehan_jumlah) as jml from t_perolehan p
					left join m_hsa hsa on p.part_id = hsa.hsa_id
					left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
					where p.part_type = '2'
					and p.flow_id = '1'
					and date(p.tanggaljam) = '".$date."'
					and ".$jam2[$i]."
					and m.model = 'A82'
					and m.issue_storage_location = 'SX21'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci");
				array_push($z2, $push_data_z[$i]);
			}
		}else{
			for ($i=0; $i <= 3 ; $i++) {
				$push_data[$i] = db::select("(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
					from welding_logs l left join materials m on l.material_number = m.material_number
					where ".$tanggal." ".$jam[$i]." ".$addlocation."
					and m.hpl = 'ASKEY' and m.model != 'A82'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)
					union
					(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
					from welding_logs l left join materials m on l.material_number = m.material_number
					where ".$tanggal." ".$jam[$i]." ".$addlocation."
					and m.hpl = 'TSKEY' and m.model != 'A82'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)");
				array_push($dataShift3, $push_data[$i]);

				$push_data_z[$i] = db::select("select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, m.model, sum(l.quantity) as jml
					from welding_logs l left join materials m on l.material_number = m.material_number 
					where  ".$tanggal." ".$jam[$i]." and m.model = 'A82' ".$addlocation."
					GROUP BY tgl, m.model");
				array_push($z3, $push_data_z[$i]);
			}

			for ($i=4; $i <= 7 ; $i++) {
				$push_data[$i] = db::select("(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
					from welding_logs l left join materials m on l.material_number = m.material_number
					where ".$tanggal." ".$jam[$i]." ".$addlocation."
					and m.hpl = 'ASKEY' and m.model != 'A82'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)
					union
					(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
					from welding_logs l left join materials m on l.material_number = m.material_number
					where ".$tanggal." ".$jam[$i]." ".$addlocation."
					and m.hpl = 'TSKEY' and m.model != 'A82'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)");
				array_push($dataShift1, $push_data[$i]);

				$push_data_z[$i] = db::select("select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, m.model, sum(l.quantity) as jml
					from welding_logs l left join materials m on l.material_number = m.material_number 
					where  ".$tanggal." ".$jam[$i]." and m.model = 'A82' ".$addlocation."
					GROUP BY tgl, m.model");
				array_push($z1, $push_data_z[$i]);
			}

			for ($i=8; $i <= 11 ; $i++) {
				$push_data[$i] = db::select("(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
					from welding_logs l left join materials m on l.material_number = m.material_number
					where ".$tanggal." ".$jam[$i]." ".$addlocation."
					and m.hpl = 'ASKEY' and m.model != 'A82'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)
					union
					(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
					from welding_logs l left join materials m on l.material_number = m.material_number
					where ".$tanggal." ".$jam[$i]." ".$addlocation."
					and m.hpl = 'TSKEY' and m.model != 'A82'
					GROUP BY tgl, kunci, m.hpl
					ORDER BY kunci)");
				array_push($dataShift2, $push_data[$i]);

				$push_data_z[$i] = db::select("select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, m.model, sum(l.quantity) as jml
					from welding_logs l left join materials m on l.material_number = m.material_number 
					where  ".$tanggal." ".$jam[$i]." and m.model = 'A82' ".$addlocation."
					GROUP BY tgl, m.model");
				array_push($z2, $push_data_z[$i]);
			}
		}



		$tanggal = substr($tanggal,40,10);

		$response = array(
			'status' => true,
			'tanggal' => $tanggal,
			'key' => $key,
			'dataShift3' => $dataShift3,
			'dataShift1' => $dataShift1,
			'dataShift2' => $dataShift2,
			'z3' => $z3, 
			'z1' => $z1, 
			'z2' => $z2, 
		);
		return Response::json($response);
	}

	public function fetchReportNG(Request $request){
		$report = DB::connection('ympimis_2')->table('welding_ng_logs')
		->leftJoin('ympimis.employee_syncs', 'ympimis.employee_syncs.employee_id', '=', 'welding_ng_logs.employee_id')
		->leftJoin('ympimis.materials', 'ympimis.materials.material_number', '=', 'welding_ng_logs.material_number');

		if(strlen($request->get('datefrom')) > 0){
			$date_from = date('Y-m-d', strtotime($request->get('datefrom')));
			$report = $report->where(db::raw('date_format(welding_ng_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
		}

		if(strlen($request->get('dateto')) > 0){
			$date_to = date('Y-m-d', strtotime($request->get('dateto')));
			$report = $report->where(db::raw('date_format(welding_ng_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
		}

		if($request->get('location') != null){
			$report = $report->whereIn('welding_ng_logs.location', $request->get('location'));
		}else{
			$report = $report->where('welding_ng_logs.location','like', '%'.$request->get('id').'%' );
		}

		$report = $report->select('welding_ng_logs.employee_id', 'ympimis.employee_syncs.name', 'welding_ng_logs.tag', 'welding_ng_logs.material_number', 'ympimis.materials.material_description', 'ympimis.materials.key', 'ympimis.materials.model', 'ympimis.materials.surface', 'welding_ng_logs.ng_name', 'welding_ng_logs.quantity', 'welding_ng_logs.location', 'welding_ng_logs.created_at')->get();

		// return Response::json($report);

		return DataTables::of($report)->make(true);
	}

	public function fetchDisplayProductionResult(Request $request){
		// $loc = $request->get('id');
		$tgl="";
		if(strlen($request->get('tgl')) > 0){
		  $tgl = date('Y-m-d',strtotime($request->get('tgl')));
		  $jam = date('Y-m-d H:i:s',strtotime($request->get('tgl').date('H:i:s')));
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

		$tanggal = "DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$yesterday."' and";

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
			$addlocation = "and l.location in (".$location.") ";
		}

		if($request->get('location') == 'hsa-sx'){
			$query1 = "SELECT a.`key`, a.model, COALESCE(s3.total,0) as shift3, COALESCE(s1.total,0) as shift1, COALESCE(s2.total,0) as shift2 from
			(select distinct `key`, model, CONCAT(`key`,model) as keymodel from ympimis.materials where hpl = 'ASKEY' and surface not like '%PLT%' and issue_storage_location = 'SX21' order by `key`) a
			left join
			(select m.`key`, m.model, CONCAT(m.`key`, m.model) as keymodel, sum(p.perolehan_jumlah) as total from t_perolehan p
			left join m_hsa hsa on p.part_id = hsa.hsa_id
			left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
			where p.part_type = '2'
			and p.flow_id = '1'
			and p.tanggaljam BETWEEN '".$yesterday." 00:00:00' AND '".$yesterday." 07:00:00'
			and m.hpl = 'ASKEY'
			and m.issue_storage_location = 'SX21'
			GROUP BY m.`key`, m.model) s3
			on a.keymodel = s3.keymodel
			left join
			(select m.`key`, m.model, CONCAT(m.`key`, m.model) as keymodel, sum(p.perolehan_jumlah) as total from t_perolehan p
			left join m_hsa hsa on p.part_id = hsa.hsa_id
			left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
			where p.part_type = '2'
			and p.flow_id = '1'
			and p.tanggaljam BETWEEN '".$yesterday." 06:00:00' AND '".$yesterday." 16:00:00'
			and m.hpl = 'ASKEY'
			and m.issue_storage_location = 'SX21'
			GROUP BY m.`key`, m.model) s1
			on a.keymodel = s1.keymodel
			left join
			(select m.`key`, m.model, CONCAT(m.`key`, m.model) as keymodel, sum(p.perolehan_jumlah) as total from t_perolehan p
			left join m_hsa hsa on p.part_id = hsa.hsa_id
			left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
			where p.part_type = '2'
			and p.flow_id = '1'
			and p.tanggaljam BETWEEN '".$yesterday." 16:30:00' AND '".$nextday." 01:00:00'
			and m.hpl = 'ASKEY'
			and m.issue_storage_location = 'SX21'
			GROUP BY m.`key`, m.model) s2
			on a.keymodel = s2.keymodel
			ORDER BY `key`";
			$alto = db::connection('welding')->select($query1);

			$query2 = "SELECT a.`key`, a.model, COALESCE(s3.total,0) as shift3, COALESCE(s1.total,0) as shift1, COALESCE(s2.total,0) as shift2 from
			(select distinct `key`, model, CONCAT(`key`,model) as keymodel from ympimis.materials where hpl = 'ASKEY' and surface not like '%PLT%' and issue_storage_location = 'SX21' order by `key`) a
			left join
			(select m.`key`, m.model, CONCAT(m.`key`, m.model) as keymodel, sum(p.perolehan_jumlah) as total from t_perolehan p
			left join m_hsa hsa on p.part_id = hsa.hsa_id
			left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
			where p.part_type = '2'
			and p.flow_id = '1'
			and p.tanggaljam BETWEEN '".$yesterday." 00:00:00' AND '".$yesterday." 07:00:00'
			and m.hpl = 'TSKEY'
			and m.issue_storage_location = 'SX21'
			GROUP BY m.`key`, m.model) s3
			on a.keymodel = s3.keymodel
			left join
			(select m.`key`, m.model, CONCAT(m.`key`, m.model) as keymodel, sum(p.perolehan_jumlah) as total from t_perolehan p
			left join m_hsa hsa on p.part_id = hsa.hsa_id
			left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
			where p.part_type = '2'
			and p.flow_id = '1'
			and p.tanggaljam BETWEEN '".$yesterday." 06:00:00' AND '".$yesterday." 16:00:00'
			and m.hpl = 'TSKEY'
			and m.issue_storage_location = 'SX21'
			GROUP BY m.`key`, m.model) s1
			on a.keymodel = s1.keymodel
			left join
			(select m.`key`, m.model, CONCAT(m.`key`, m.model) as keymodel, sum(p.perolehan_jumlah) as total from t_perolehan p
			left join m_hsa hsa on p.part_id = hsa.hsa_id
			left join ympimis.materials m on m.material_number = hsa.hsa_kito_code
			where p.part_type = '2'
			and p.flow_id = '1'
			and p.tanggaljam BETWEEN '".$yesterday." 16:30:00' AND '".$nextday." 01:00:00'
			and m.hpl = 'TSKEY'
			and m.issue_storage_location = 'SX21'
			GROUP BY m.`key`, m.model) s2
			on a.keymodel = s2.keymodel
			ORDER BY `key`";
			$tenor = db::connection('welding')->select($query2);
		}
		else{
			$query1 = "SELECT a.`key`, a.model, COALESCE(s3.total,0) as shift3, COALESCE(s1.total,0) as shift1, COALESCE(s2.total,0) as shift2 from
			(select distinct `key`, model, CONCAT(`key`,model) as keymodel from materials where hpl = 'ASKEY' and surface not like '%PLT%' and issue_storage_location = 'SX21' order by `key`) a
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from welding_logs l
			left join materials m on l.material_number = m.material_number
			WHERE l.created_at BETWEEN '".$yesterday." 00:00:00' AND '".$yesterday." 07:00:00' and m.hpl = 'ASKEY' and m.issue_storage_location = 'SX21' ".$addlocation."
			GROUP BY m.`key`, m.model) s3
			on a.keymodel = s3.keymodel
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from welding_logs l
			left join materials m on l.material_number = m.material_number
			WHERE l.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$yesterday." 16:00:00' and m.hpl = 'ASKEY' and m.issue_storage_location = 'SX21' ".$addlocation."
			GROUP BY m.`key`, m.model) s1
			on a.keymodel = s1.keymodel
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from welding_logs l
			left join materials m on l.material_number = m.material_number
			WHERE l.created_at BETWEEN '".$yesterday." 16:30:00' AND '".$nextday." 01:00:00' and m.hpl = 'ASKEY' and m.issue_storage_location = 'SX21' ".$addlocation."
			GROUP BY m.`key`, m.model) s2
			on a.keymodel = s2.keymodel
			ORDER BY `key`";
			$alto = db::connection('ympimis_2')->select($query1);

			$query2 = "SELECT a.`key`, a.model, COALESCE(s3.total,0) as shift3, COALESCE(s1.total,0) as shift1, COALESCE(s2.total,0) as shift2 from
			(select distinct `key`, model, CONCAT(`key`,model) as keymodel from materials where hpl = 'TSKEY' and surface not like '%PLT%' and issue_storage_location = 'SX21' order by `key`) a
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from welding_logs l
			left join materials m on l.material_number = m.material_number
			WHERE l.created_at BETWEEN '".$yesterday." 00:00:00' AND '".$yesterday." 07:00:00' and m.hpl = 'TSKEY' and m.issue_storage_location = 'SX21' ".$addlocation."
			GROUP BY m.`key`, m.model) s3
			on a.keymodel = s3.keymodel
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from welding_logs l
			left join materials m on l.material_number = m.material_number
			WHERE l.created_at BETWEEN '".$yesterday." 06:00:00' AND '".$yesterday." 16:00:00' and m.hpl = 'TSKEY' and m.issue_storage_location = 'SX21' ".$addlocation."
			GROUP BY m.`key`, m.model) s1
			on a.keymodel = s1.keymodel
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from welding_logs l
			left join materials m on l.material_number = m.material_number
			WHERE l.created_at BETWEEN '".$yesterday." 16:30:00' AND '".$nextday." 01:00:00' and m.hpl = 'TSKEY' and m.issue_storage_location = 'SX21' ".$addlocation."
			GROUP BY m.`key`, m.model) s2
			on a.keymodel = s2.keymodel
			ORDER BY `key`";
			$tenor = db::connection('ympimis_2')->select($query2);
		}

		$query3 = "select distinct `key` from materials where hpl = 'ASKEY' and issue_storage_location = 'SX21' order by `key`";
		$key =  db::connection('ympimis_2')->select($query3);

		
		if(strpos($addlocation, 'phs')){
			$query4 = "select distinct model from materials where hpl = 'ASKEY' and mrpc = 'S11' and surface = 'PHS' order by model";
			$query5 = "select distinct model from materials where hpl = 'TSKEY' and mrpc = 'S11' and surface = 'PHS' order by model";
		}else{
			$query4 = "select distinct model from materials where hpl = 'ASKEY' and mrpc = 'S21' order by model";
			$query5 = "select distinct model from materials where hpl = 'TSKEY' and mrpc = 'S21' order by model";
		}

		$model_alto =  db::connection('ympimis_2')->select($query4);
		$model_tenor =  db::connection('ympimis_2')->select($query5);



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
			'alto' => $alto,
			'tenor' => $tenor,
			'key' => $key,
			'model_tenor' => $model_tenor,
			'model_alto' => $model_alto,
			'title' => $location
		);
		return Response::json($response);
	}

	public function fetchDisplayProductionResult2(Request $request)
	{
		try {

			$tgl="";
			if(strlen($request->get('tgl')) > 0){
			  $tgl = date('Y-m-d',strtotime($request->get('tgl')));
			  $jam = date('Y-m-d H:i:s',strtotime($request->get('tgl').date('H:i:s')));
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

			$tanggal = "DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$yesterday."' and";

			$addlocation = "";
			$addlocation2 = "";
			if ($request->get('location') != '') {
				$addlocation = "and location = '".$request->get('location')."'";
				$addlocation2 = "WHERE welding_materials.material_category = '".strtoupper(explode('-', $request->get('location'))[0])."'";
			}

			// $addlocation = "";
			// if($request->get('location') != null) {
			// 	$locations = explode(",", $request->get('location'));
			// 	$location = "";

			// 	for($x = 0; $x < count($locations); $x++) {
			// 		$location = $location."'".$locations[$x]."'";
			// 		if($x != count($locations)-1){
			// 			$location = $location.",";
			// 		}
			// 	}
			// 	$addlocation = "and location in (".$location.") ";
			// }

			$material = DB::connection('ympimis_2')->SELECT("SELECT
				welding_materials.material_number,
				models.model,
				models.`key`,
				models.hpl 
			FROM
				welding_materials
				LEFT JOIN (
				SELECT
					material_number,
					`key`,
					model,
					hpl 
				FROM
					ympimis.materials 
				WHERE
					( issue_storage_location = 'SX21' AND hpl = 'ASKEY' ) 
					OR ( issue_storage_location = 'SX21' AND hpl = 'TSKEY' ) 
				GROUP BY
					material_number,
					`key`,
					model,
					hpl 
				ORDER BY
					`key`,
					material_number,
					model 
				) AS models ON models.material_number = welding_materials.material_number ".$addlocation2);

			$prod_result = DB::connection('ympimis_2')->select("SELECT
				a.material_number,
				sum( a.quantity ) AS quantity,
				a.shift 
			FROM
				(
				SELECT
					b.*,
					tags.quantity 
				FROM
					(
					SELECT
						tag,
						material_number,
						3 AS shift 
					FROM
						welding_details 
					WHERE
						created_at BETWEEN '".$yesterday." 00:00:00' 
						AND '".$yesterday." 07:00:00'
						".$addlocation."
						UNION ALL
					SELECT
						tag,
						material_number,
						1 AS shift 
					FROM
						welding_details 
					WHERE
						created_at BETWEEN '".$yesterday." 06:00:00' 
						AND '".$yesterday." 16:00:00' 
						".$addlocation."
						UNION ALL
					SELECT
						tag,
						material_number,
						2 AS shift 
					FROM
						welding_details 
					WHERE
						created_at BETWEEN '".$yesterday." 16:00:00' 
						AND '".$nextday." 01:00:00' 
						".$addlocation."
						UNION ALL
					SELECT
						tag,
						material_number,
						3 AS shift 
					FROM
						welding_logs 
					WHERE
						started_at BETWEEN '".$yesterday." 00:00:00' 
						AND '".$yesterday." 07:00:00'
						".$addlocation."
						UNION ALL
					SELECT
						tag,
						material_number,
						1 AS shift 
					FROM
						welding_logs 
					WHERE
						started_at BETWEEN '".$yesterday." 06:00:00' 
						AND '".$yesterday." 16:00:00' 
						".$addlocation."
						UNION ALL
					SELECT
						tag,
						material_number,
						2 AS shift 
					FROM
						welding_logs 
					WHERE
						started_at BETWEEN '".$yesterday." 16:00:00' 
						AND '".$nextday." 01:00:00' 
						".$addlocation."
					) b
					JOIN (
					SELECT
						tag,
						welding_tags.material_number,
						welding_tags.material_type,
						quantity 
					FROM
						welding_tags
						JOIN welding_materials ON welding_materials.material_number = welding_tags.material_number 
						AND welding_materials.material_type = welding_tags.material_type 
					) AS tags ON tags.tag = b.tag 
				) a 
			GROUP BY
				a.material_number,
				a.shift");

			$prods = [];

			for ($i=0; $i < count($prod_result); $i++) { 
				$hpl = '';
				$model = '';
				$key = '';
				for ($j=0; $j < count($material); $j++) { 
					if ($prod_result[$i]->material_number == $material[$j]->material_number) {
						$model = $material[$j]->model;
						$key = $material[$j]->key;
						$hpl = $material[$j]->hpl;
					}
				}
				$prod = array(
					'material_number' => $prod_result[$i]->material_number,
					'quantity' => $prod_result[$i]->quantity,
					'shift' => $prod_result[$i]->shift,
					'key' => $key,
					'model' => $model,
					'hpl' => $hpl,
				);
				array_push($prods, $prod);
			}

			$response = array(
				'status' => true,
				'material' => $material,
				'location' => $request->get('location'),
				'prods' => $prods
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

	public function scanWeldingOperator(Request $request){
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

	public function scanWeldingJig(Request $request){

		try {
			if ($request->get('status') == 'Repair') {
				$jig = Jig::join('jig_kensas','jig_kensas.jig_id','jigs.jig_id')
				->where('jigs.jig_tag', '=', $request->get('tag'))
				->where('category',$request->get('category'))
				->where('status',$request->get('status'))
				->get();

				if (count($jig) > 0) {
					foreach ($jig as $key) {
						$jig_id = $key->jig_id;
					}

					$part = JigBom::where('jig_boms.jig_parent',$jig_id)->join('jig_part_stocks','jig_boms.jig_child','jig_part_stocks.jig_id')->get();
				}
			}else{
				$jig = Jig::where('jigs.jig_tag', '=', $request->get('tag'))->where('category',$request->get('category'))->first();
				if (count($jig) > 0) {
					$part = JigBom::where('jig_boms.jig_parent',$jig->jig_id)->get();
				}
			}

			if (count($jig) > 0) {
				$response = array(
					'status' => true,
					'jig' => $jig,
					'part' => $part,
					'started_at' => date('Y-m-d H:i:s'),
				);
				return Response::json($response);
			}else{
				$response = array(
					'status' => false,
					'message' => 'Tag Tidak Ditemukan'
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


	public function fetchWeldingScheduleJig(Request $request){
		$query = "SELECT
		jigs.jig_id,
		jigs.jig_name,
		jigs.jig_index,
		jigs.category,
		jig_schedules.check_period - 5 AS check_period,
		jig_schedules.last_check,
		DATEDIFF( now( ), last_check ) AS age 
		FROM
		`jig_schedules`
		LEFT JOIN jigs ON jigs.jig_id = jig_schedules.jig_id 
		AND jigs.jig_index = jig_schedules.jig_index 
		HAVING
		check_period < age";

		$schedules = db::select($query);

		$response = array(
			'status' => true,
			'schedules' => $schedules,
		);
		return Response::json($response);
	}

	public function fetchJigCheck(Request $request)
	{
		try {
			$jig_id = $request->get('jig_id');

			$jig_check = JigKensaCheck::where('jig_id',$jig_id)->orderBy('check_index','asc')->get();

			$response = array(
				'status' => true,
				'jig_check' => $jig_check,
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

	public function fetchJigCheckProcess(Request $request)
	{
		try {
			$jig_id = $request->get('jig_id');

			$jig_check = DB::connection('ympimis_2')->table('jig_kensa_check_processes')->where('jig_id',$jig_id)->orderBy('check_index','asc')->get();

			$response = array(
				'status' => true,
				'jig_check' => $jig_check,
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

	public function fetchDrawingList(Request $request)
	{
		try {
			$jig_id = $request->get('jig_id');

			$drawing = DB::SELECT("SELECT
				a.file_name,
				a.jig_name,
				a.jig_child,
				a.jig_parent
				FROM
				(
				SELECT
				'".$jig_id."' AS jig_parent,
				'".$jig_id."' AS jig_child,
				file_name,
				jig_name 
				FROM
				jigs 
				WHERE
				jig_id = '".$jig_id."' UNION ALL
				SELECT
				jig_parent,
				jig_child,
				file_name,
				jig_name 
				FROM
				`jig_boms`
				JOIN jigs ON jigs.jig_id = jig_child 
				WHERE
				jig_parent = '".$jig_id."' 
			) a");

			$path = '/jig/drawing/';
			$file_path = asset($path);

			$response = array(
				'status' => true,
				'drawing' => $drawing,
				'file_path' => $file_path
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

	public function fetchDrawingListProcess(Request $request)
	{
		try {
			$jig_id = $request->get('jig_id');

			$drawing = DB::connection('ympimis_2')->SELECT("SELECT
				a.file_name,
				a.jig_name,
				a.jig_child,
				a.jig_parent
				FROM
				(
				SELECT
				'".$jig_id."' AS jig_parent,
				'".$jig_id."' AS jig_child,
				file_name,
				jig_name 
				FROM
				jig_processes 
				WHERE
				jig_id = '".$jig_id."' UNION ALL
				SELECT
				jig_parent,
				jig_child,
				file_name,
				jig_name 
				FROM
				`jig_bom_processes`
				JOIN jig_processes ON jig_processes.jig_id = jig_child 
				WHERE
				jig_parent = '".$jig_id."' 
			) a");

			$path = '/jig/drawing/';
			$file_path = asset($path);

			$response = array(
				'status' => true,
				'drawing' => $drawing,
				'file_path' => $file_path
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

	public function inputKensaJig(Request $request)
	{
		try {
			$operator_id = $request->get('operator_id');
			$jig_id = $request->get('jig_id');
			$jig_index = $request->get('jig_index');
			$started_at = $request->get('started_at');
			$check_indexes = $request->get('check_indexes');
			$check_index = $request->get('check_index');
			$check_name = $request->get('check_name');
			$upper_limit = $request->get('upper_limit');
			$lower_limit = $request->get('lower_limit');
			$value = $request->get('value');
			$result = $request->get('result');
			$jig_child = $request->get('jig_child');
			$jig_alias = $request->get('jig_alias');
			$started_at = $request->get('started_at');
			$finished_at = date('Y-m-d H:i:s');
			$now = date('Y-m-d');

			$id_user = Auth::id();

			$count_ng = 0;

			for ($j=0; $j < $check_indexes; $j++) { 
				if ($result[$j] == 'NG') {
					$count_ng++;
				}
			}

			if ($count_ng > 0) {
				for ($i=0; $i < $check_indexes; $i++) { 
					$kensa_jig = new JigKensa([
						'operator_id' => $operator_id,
						'jig_id' => $jig_id,
						'jig_index' => $jig_index,
						'check_index' => $check_index[$i],
						'check_name' => $check_name[$i],
						'upper_limit' => $upper_limit[$i],
						'lower_limit' => $lower_limit[$i],
						'value' => $value[$i],
						'result' => $result[$i],
						'jig_child' => $jig_child[$i],
						'jig_alias' => $jig_alias[$i],
						'status' => 'Repair',
						'started_at' => $started_at,
						'finished_at' => $finished_at,
						'created_by' => $id_user,
					]);
					$kensa_jig->save();
				}

				$status = 'Repair';
			}else{
				for ($k=0; $k < $check_indexes; $k++) { 
					$kensa_jig = new JigKensaLog([
						'operator_id' => $operator_id,
						'jig_id' => $jig_id,
						'jig_index' => $jig_index,
						'check_index' => $check_index[$k],
						'check_name' => $check_name[$k],
						'upper_limit' => $upper_limit[$k],
						'lower_limit' => $lower_limit[$k],
						'value' => $value[$k],
						'result' => $result[$k],
						'jig_child' => $jig_child[$k],
						'jig_alias' => $jig_alias[$k],
						'started_at' => $started_at,
						'finished_at' => $finished_at,
						'created_by' => $id_user,
					]);
					$kensa_jig->save();
				}

				$status = 'OK';
			}

			$jigs = Jig::where('jig_id',$jig_id)->first();
			$check_period = $jigs->check_period;

			$jig_schedule = JigSchedule::where('jig_id',$jig_id)->where('jig_index',$jig_index)->where('schedule_status','Open')->first();
			if (count($jig_schedule) > 0) {
				if ($status == 'OK') {
					// $jig_schedule->schedule_date = date('Y-m-d');
					$jig_schedule->kensa_time = $finished_at;
					$jig_schedule->kensa_status = 'Finish Kensa';
					$jig_schedule->kensa_pic = $operator_id;
					$jig_schedule->repair_status = 'No Repair';
					$jig_schedule->schedule_status = 'Close';
					$jig_schedule->save();

					$week_date = WeeklyCalendar::where('week_date',date('Y-m-d', strtotime($now. ' + '.$check_period.' days')))->first();
					if ($week_date->remark == 'H') {
						$new_week_date = WeeklyCalendar::where('week_date','>',date('Y-m-d', strtotime($now. ' + '.$check_period.' days')))->where('remark','H')->limit(1)->first();
						$new_schedule = $new_week_date->week_date;
					}else{
						$new_schedule = date('Y-m-d', strtotime($now. ' + '.$check_period.' days'));
					}

					// $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
		            $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
		            foreach ($weekly_calendars as $key) {
		                if ($key->week_date == $new_schedule) {
		                    if ($key->remark == 'H') {
		                        $new_schedule = date('Y-m-d', strtotime($new_schedule.' + 1 days'));
		                    }
		                }
		            }

					$schedule = new JigSchedule([
						'jig_id' => $jig_id,
						'jig_index' => $jig_index,
						'schedule_date' => $new_schedule,
						'schedule_status' => 'Open',
						'created_by' => $id_user,
					]);
					$schedule->save();
				}else{
					// $jig_schedule->schedule_date = date('Y-m-d');
					$jig_schedule->kensa_time = $finished_at;
					$jig_schedule->kensa_pic = $operator_id;
					$jig_schedule->save();
				}
			}else{
				if ($status == 'OK') {
					$new_schedule = date('Y-m-d');
					$weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
		            foreach ($weekly_calendars as $key) {
		                if ($key->week_date == $new_schedule) {
		                    if ($key->remark == 'H') {
		                        $new_schedule = date('Y-m-d', strtotime($new_schedule.' + 1 days'));
		                    }
		                }
		            }
					$schedule = new JigSchedule([
						'jig_id' => $jig_id,
						'jig_index' => $jig_index,
						'schedule_date' => $new_schedule,
						'kensa_time' => $finished_at,
						'kensa_pic' => $operator_id,
						'kensa_status' => 'Finish Kensa',
						'repair_status' => 'No Repair',
						'schedule_status' => 'Close',
						'created_by' => $id_user,
					]);
					$schedule->save();

					$week_date = WeeklyCalendar::where('week_date',date('Y-m-d', strtotime($now. ' + '.$check_period.' days')))->first();
					if ($week_date->remark == 'H') {
						$new_week_date = WeeklyCalendar::where('week_date','>',date('Y-m-d', strtotime($now. ' + '.$check_period.' days')))->where('remark','H')->limit(1)->first();
						$new_schedule = $new_week_date->week_date;
					}else{
						$new_schedule = date('Y-m-d', strtotime($now. ' + '.$check_period.' days'));
					}

					$weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
		            foreach ($weekly_calendars as $key) {
		                if ($key->week_date == $new_schedule) {
		                    if ($key->remark == 'H') {
		                        $new_schedule = date('Y-m-d', strtotime($new_schedule.' + 1 days'));
		                    }
		                }
		            }

					$schedule = new JigSchedule([
						'jig_id' => $jig_id,
						'jig_index' => $jig_index,
						'schedule_date' => $new_schedule,
						'schedule_status' => 'Open',
						'created_by' => $id_user,
					]);
					$schedule->save();
				}else{
					$new_schedule = date('Y-m-d');
					$weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
		            foreach ($weekly_calendars as $key) {
		                if ($key->week_date == $new_schedule) {
		                    if ($key->remark == 'H') {
		                        $new_schedule = date('Y-m-d', strtotime($new_schedule.' + 1 days'));
		                    }
		                }
		            }
					$schedule = new JigSchedule([
						'jig_id' => $jig_id,
						'jig_index' => $jig_index,
						'schedule_date' => $new_schedule,
						'kensa_time' => $finished_at,
						'kensa_pic' => $operator_id,
						'kensa_status' => 'Finish Kensa',
						'repair_status' => 'Unrepaired',
						'schedule_status' => 'Open',
						'created_by' => $id_user,
					]);
					$schedule->save();
				}
			}

			$response = array(
				'status' => true,
				'message' => 'Save Kensa Success',
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

	public function inputKensaJigProcess(Request $request)
	{
		try {
			$operator_id = $request->get('operator_id');
			$jig_id = $request->get('jig_id');
			$jig_index = $request->get('jig_index');
			$started_at = $request->get('started_at');
			$check_indexes = $request->get('check_indexes');
			$check_index = explode(',', $request->get('check_index'));
			$check_name = explode(',', $request->get('check_name'));
			$upper_limit = explode(',',$request->get('upper_limit'));
			$lower_limit = explode(',', $request->get('lower_limit'));
			$value = explode(',', $request->get('value'));
			$result = explode(',',$request->get('result'));
			$jig_child = explode(',', $request->get('jig_child'));
			$jig_alias = explode(',', $request->get('jig_alias'));
			$started_at = $request->get('started_at');
			$finished_at = date('Y-m-d H:i:s');
			$now = date('Y-m-d');

			$id_user = Auth::id();

			$count_ng = 0;

			$filename = "";
        	$file_destination = 'data_file/jig';

        	$file_before = $request->file('newAttachment_before');
            $filename_before = 'jig_process_before_'.$jig_id.'_'.date('YmdHisa').'.'.$request->input('extension_before');
            $file_before->move($file_destination, $filename_before);

            $file_after = $request->file('newAttachment_after');
            $filename_after = 'jig_process_after_'.$jig_id.'_'.date('YmdHisa').'.'.$request->input('extension_after');
            $file_after->move($file_destination, $filename_after);

			for ($j=0; $j < $check_indexes; $j++) { 
				if ($result[$j] == 'NG') {
					$count_ng++;
				}
			}

			if ($count_ng > 0) {
				for ($i=0; $i < $check_indexes; $i++) { 
					$kensa_jig = DB::connection('ympimis_2')
					->table('jig_kensa_processes')
					->insert([
						'operator_id' => $operator_id,
						'jig_id' => $jig_id,
						'jig_index' => $jig_index,
						'check_index' => $check_index[$i],
						'check_name' => $check_name[$i],
						'upper_limit' => $upper_limit[$i],
						'lower_limit' => $lower_limit[$i],
						'value' => $value[$i],
						'result' => $result[$i],
						'jig_child' => $jig_child[$i],
						'jig_alias' => $jig_alias[$i],
						'status' => 'Repair',
						'started_at' => $started_at,
						'finished_at' => $finished_at,
						'image_before' => $filename_before,
						'image_after' => $filename_after,
						'created_by' => $id_user,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
					]);
				}

				$status = 'Repair';
			}else{
				for ($k=0; $k < $check_indexes; $k++) { 
					$kensa_jig = DB::connection('ympimis_2')
					->table('jig_kensa_log_processes')
					->insert([
						'operator_id' => $operator_id,
						'jig_id' => $jig_id,
						'jig_index' => $jig_index,
						'check_index' => $check_index[$k],
						'check_name' => $check_name[$k],
						'upper_limit' => $upper_limit[$k],
						'lower_limit' => $lower_limit[$k],
						'value' => $value[$k],
						'result' => $result[$k],
						'jig_child' => $jig_child[$k],
						'jig_alias' => $jig_alias[$k],
						'started_at' => $started_at,
						'finished_at' => $finished_at,
						'image_before' => $filename_before,
						'image_after' => $filename_after,
						'created_by' => $id_user,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
					]);
				}

				$status = 'OK';
			}

			$jigs = DB::connection('ympimis_2')->table('jig_processes')->where('jig_id',$jig_id)->first();
			$check_period = $jigs->check_period;

			$jig_schedule = DB::connection('ympimis_2')->table('jig_schedule_processes')->where('jig_id',$jig_id)->where('jig_index',$jig_index)->where('schedule_status','Open')->first();
			if (count($jig_schedule) > 0) {
				if ($status == 'OK') {
					$jig_schedule = DB::connection('ympimis_2')->table('jig_schedule_processes')->where('jig_id',$jig_id)->where('jig_index',$jig_index)->where('schedule_status','Open')->update([
						'kensa_time' => $finished_at,
						'kensa_status' => 'Finish Kensa',
						'kensa_pic' => $operator_id,
						'repair_status' => 'No Repair',
						'schedule_status' => 'Close',
						'updated_at' => date('Y-m-d H:i:s'),
					]);

					$week_date = WeeklyCalendar::where('week_date',date('Y-m-d', strtotime($now. ' + '.$check_period.' days')))->first();
					if ($week_date->remark == 'H') {
						$new_week_date = WeeklyCalendar::where('week_date','>',date('Y-m-d', strtotime($now. ' + '.$check_period.' days')))->where('remark','H')->limit(1)->first();
						$new_schedule = $new_week_date->week_date;
					}else{
						$new_schedule = date('Y-m-d', strtotime($now. ' + '.$check_period.' days'));
					}

					// $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
		            $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
		            foreach ($weekly_calendars as $key) {
		                if ($key->week_date == $new_schedule) {
		                    if ($key->remark == 'H') {
		                        $new_schedule = date('Y-m-d', strtotime($new_schedule.' + 1 days'));
		                    }
		                }
		            }

					$schedule = DB::connection('ympimis_2')
					->table('jig_schedule_processes')
					->insert([
						'jig_id' => $jig_id,
						'jig_index' => $jig_index,
						'schedule_date' => $new_schedule,
						'schedule_status' => 'Open',
						'created_by' => $id_user,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
					]);
				}else{
					// $jig_schedule->schedule_date = date('Y-m-d');
					$jig_schedule = DB::connection('ympimis_2')->table('jig_schedule_processes')->where('jig_id',$jig_id)->where('jig_index',$jig_index)->where('schedule_status','Open')->update([
						'kensa_time' => $finished_at,
						'kensa_pic' => $operator_id,
						'updated_at' => date('Y-m-d H:i:s'),
					]);
				}
			}else{
				if ($status == 'OK') {
					$new_schedule = date('Y-m-d');
					$weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
		            foreach ($weekly_calendars as $key) {
		                if ($key->week_date == $new_schedule) {
		                    if ($key->remark == 'H') {
		                        $new_schedule = date('Y-m-d', strtotime($new_schedule.' + 1 days'));
		                    }
		                }
		            }
					$schedule = DB::connection('ympimis_2')
					->table('jig_schedule_processes')
					->insert([
						'jig_id' => $jig_id,
						'jig_index' => $jig_index,
						'schedule_date' => $new_schedule,
						'kensa_time' => $finished_at,
						'kensa_pic' => $operator_id,
						'kensa_status' => 'Finish Kensa',
						'repair_status' => 'No Repair',
						'schedule_status' => 'Close',
						'created_by' => $id_user,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
					]);

					$week_date = WeeklyCalendar::where('week_date',date('Y-m-d', strtotime($now. ' + '.$check_period.' days')))->first();
					if ($week_date->remark == 'H') {
						$new_week_date = WeeklyCalendar::where('week_date','>',date('Y-m-d', strtotime($now. ' + '.$check_period.' days')))->where('remark','H')->limit(1)->first();
						$new_schedule = $new_week_date->week_date;
					}else{
						$new_schedule = date('Y-m-d', strtotime($now. ' + '.$check_period.' days'));
					}

					$weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
		            foreach ($weekly_calendars as $key) {
		                if ($key->week_date == $new_schedule) {
		                    if ($key->remark == 'H') {
		                        $new_schedule = date('Y-m-d', strtotime($new_schedule.' + 1 days'));
		                    }
		                }
		            }

		            $schedule = DB::connection('ympimis_2')
					->table('jig_schedule_processes')
					->insert([
						'jig_id' => $jig_id,
						'jig_index' => $jig_index,
						'schedule_date' => $new_schedule,
						'schedule_status' => 'Open',
						'created_by' => $id_user,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
					]);
				}else{
					$new_schedule = date('Y-m-d');
					$weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
		            foreach ($weekly_calendars as $key) {
		                if ($key->week_date == $new_schedule) {
		                    if ($key->remark == 'H') {
		                        $new_schedule = date('Y-m-d', strtotime($new_schedule.' + 1 days'));
		                    }
		                }
		            }

		            $schedule = DB::connection('ympimis_2')
					->table('jig_schedule_processes')
					->insert([
						'jig_id' => $jig_id,
						'jig_index' => $jig_index,
						'schedule_date' => $new_schedule,
						'kensa_time' => $finished_at,
						'kensa_pic' => $operator_id,
						'kensa_status' => 'Finish Kensa',
						'repair_status' => 'Unrepaired',
						'schedule_status' => 'Open',
						'created_by' => $id_user,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
					]);
				}
			}

			$response = array(
				'status' => true,
				'message' => 'Save Kensa Success',
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

	

	public function inputRepairJig(Request $request)
	{
		try {
			$operator_id = $request->get('operator_id');
			$jig_id = $request->get('jig_id');
			$jig_index = $request->get('jig_index');
			$started_at = $request->get('started_at');
			$check_indexes = $request->get('check_indexes');
			$check_index = $request->get('check_index');
			$check_name = $request->get('check_name');
			$upper_limit = $request->get('upper_limit');
			$lower_limit = $request->get('lower_limit');
			$value = $request->get('value');
			$result = $request->get('result');
			$jig_child = $request->get('jig_child');
			$jig_alias = $request->get('jig_alias');
			$action = $request->get('action');
			$jig_parent = $request->get('jig_parent');
			$part = $request->get('part');
			$count = $request->get('count');
			$status = $request->get('status');
			$finished_at = date('Y-m-d H:i:s');
			$now = date('Y-m-d');
			$id_user = Auth::id();

			$jig_parents = [];
			$jig_childs = [];
			$jig_aliases = [];

			if ($status == 'Repaired') {
				for ($k=0; $k < $check_indexes; $k++) { 
					if ($result[$k] == 'NG') {
						$jig_aliases[] = $jig_alias[$k];
						$jig_childs[] = $jig_child[$k];
						$jig_parents[] = $jig_id;
					}
				}

				$usageserror = 0;

				for ($i = 0; $i < count($jig_aliases); $i++) {
					if (stripos($jig_childs[$i], '-0') == FALSE) {
						$jigbom = JigBom::where('jig_parent',$jig_parents[$i])->where('jig_child',$jig_childs[$i])->first();
						$usage = $jigbom->usage;

						$partsstock = JigPartStock::where('jig_id',$jig_aliases[$i])->get();

						foreach ($partsstock as $key) {
							$id_partstock = $key->id;
							$stock = $key->quantity;
							$min_order = $key->min_order;
							$material_jig = $key->material;
							$remarkjig = $key->remark;
						}

						if ($stock < $usage) {
							$usageserror++;
						}else{
							if ($remarkjig == null) {
								$date = date('Y-m-d');
								$prefix_now = 'WJO'.date("y").date("m");
								$code_generator = CodeGenerator::where('note','=','wjo')->first();
								if ($prefix_now != $code_generator->prefix){
									$code_generator->prefix = $prefix_now;
									$code_generator->index = '0';
									$code_generator->save();
								}

								$jigs = Jig::where('jig_id',$jig_childs[$i])->get();

								foreach ($jigs as $key) {
									$item_name = $key->jig_name;
								}

								$sub_section = 'Welding Process_Koshuha Solder';
								$item_name = $item_name;
								$category = 'Jig';
								$drawing_name = $item_name;
								$item_number = $jig_childs[$i];
								$part_number = $jig_aliases[$i];
								$quantity = $min_order;
								$priority = 'Normal';
								$type = 'Pembuatan Baru';
								$material = $material_jig;
								$problem_desc = 'Pembuatan Part Kensa Jig Welding';

								$remark;
								if($priority == 'Normal'){
									$remark = 1;
								}else{
									$remark = 0;
								}

								$request_date = date('Y-m-d', strtotime($date. ' + 14 days'));

								$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
								$order_no = $code_generator->prefix . $number;
								$code_generator->index = $code_generator->index+1;
								$code_generator->save();

								$file_name = $order_no.'.pdf';
								$path = public_path(). '/workshop';
								$file_path = public_path() . "/jig/drawing/" .$jig_parents[$i].'/'.$jig_childs[$i].'.pdf';

								$newplace  = $path.'/'.$order_no.'.pdf';
								copy($file_path,$newplace);

								$wjo = new WorkshopJobOrder([
									'order_no' => $order_no,
									'sub_section' => $sub_section,
									'item_name' => $item_name,
									'category' => $category,
									'drawing_name' => $drawing_name,
									'item_number' => $item_number,
									'part_number' => $part_number,
									'quantity' => $quantity,
									'target_date' => $request_date,
									'priority' => $priority,
									'type' => $type,
									'material' => $material,
									'automation' => 'auto',
									'problem_description' => $problem_desc,
									'remark' => $remark,
									'attachment' => $file_name,
									'created_by' => 'PI9902015',
								]);

								$wjo_log = new WorkshopJobOrderLog([
									'order_no' => $order_no,
									'remark' => $remark,
									'created_by' => 'PI9902015',
								]);

								$wjo->save();
								$wjo_log->save();

								$partsstocks = JigPartStock::find($id_partstock);
								$qtynew = $stock - $usage;
								$partsstocks->quantity = $qtynew;
								$partsstocks->quantity_order = $min_order;
								$partsstocks->remark = $order_no;
								$partsstocks->save();
							}
						}
					}
				}

				if ($usageserror > 0) {
					$status = false;
					$message = 'Part Tidak Tersedia';
				}else{

					$partsstocks = JigPartStock::find($id_partstock);
					$qtynew = $stock - $usage;
					$partsstocks->quantity = $qtynew;
					$partsstocks->save();

					for ($j=0; $j < $check_indexes; $j++) {

						$repair = new JigRepairLog([
							'operator_id' => $operator_id,
							'jig_id' => $jig_id,
							'jig_index' => $jig_index,
							'check_index' => $check_index[$j],
							'check_name' => $check_name[$j],
							'upper_limit' => $upper_limit[$j],
							'lower_limit' => $lower_limit[$j],
							'value' => $value[$j],
							'result' => $result[$j],
							'jig_child' => $jig_child[$j],
							'jig_alias' => $jig_alias[$j],
							'status' => 'Repaired',
							'action' => $action[$j],
							'started_at' => $started_at,
							'finished_at' => $finished_at,
							'created_by' => $id_user,
						]);
						$repair->save();
					}

					$jigKensa = JigKensa::where('jig_kensas.jig_id', '=', $jig_id)
					->where('status','Repair')
					->get();

					foreach ($jigKensa as $key) {
						$kensa_jig = new JigKensaLog([
							'operator_id' => $key->operator_id,
							'jig_id' => $key->jig_id,
							'jig_index' => $key->jig_index,
							'jig_alias' => $key->jig_alias,
							'check_index' => $key->check_index,
							'check_name' => $key->check_name,
							'upper_limit' => $key->upper_limit,
							'lower_limit' => $key->lower_limit,
							'value' => $key->value,
							'result' => $key->result,
							'status' => 'Repaired',
							'jig_child' => $key->jig_child,
							'started_at' => $key->started_at,
							'finished_at' => $key->finished_at,
							'created_by' => $id_user,
						]);
						$kensa_jig->save();
						$kensas = JigKensa::where('id',$key->id)->forceDelete();
					}

					$jigses = Jig::where('jig_id',$jig_id)->first();
					$check_period = $jigses->check_period;

					$jig_schedule = JigSchedule::where('jig_id',$jig_id)->where('jig_index',$jig_index)->where('schedule_status','Open')->first();
					if (count($jig_schedule) > 0) {
						$jig_schedule->repair_time = $finished_at;
						$jig_schedule->repair_pic = $operator_id;
						$jig_schedule->repair_status = 'Finish Repair';
						$jig_schedule->schedule_status = 'Close';
						$jig_schedule->save();

						$week_date = WeeklyCalendar::where('week_date',date('Y-m-d', strtotime($now. ' + '.$check_period.' days')))->first();
						if ($week_date->remark == 'H') {
							$new_week_date = WeeklyCalendar::where('week_date','>',date('Y-m-d', strtotime($now. ' + '.$check_period.' days')))->where('remark','H')->limit(1)->first();
							$new_schedule = $new_week_date->week_date;
						}else{
							$new_schedule = date('Y-m-d', strtotime($now. ' + '.$check_period.' days'));
						}

						$weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
			            foreach ($weekly_calendars as $key) {
			                if ($key->week_date == $new_schedule) {
			                    if ($key->remark == 'H') {
			                        $new_schedule = date('Y-m-d', strtotime($new_schedule.' + 1 days'));
			                    }
			                }
			            }
						
						$schedule = new JigSchedule([
							'jig_id' => $jig_id,
							'jig_index' => $jig_index,
							'schedule_date' => $new_schedule,
							'schedule_status' => 'Open',
							'created_by' => $id_user,
						]);
						$schedule->save();
					}

					$status = true;
					$message = 'Repair Jig Selesai';
				}
			}else{
				for ($k=0; $k < $check_indexes; $k++) {
					$kensa = JigKensa::where('jig_id',$jig_id)->where('jig_index',$jig_index)->where('check_index',$check_index[$k])->first();
					$kensa->action = $action[$k];
					$kensa->save();
				}

				$jigses = Jig::where('jig_id',$jig_id)->first();
				$check_period = $jigses->check_period;

				$jig_schedule = JigSchedule::where('jig_id',$jig_id)->where('jig_index',$jig_index)->where('schedule_status','Open')->first();
				if (count($jig_schedule) > 0) {
					$jig_schedule->repair_time = $finished_at;
					$jig_schedule->repair_pic = $operator_id;
					$jig_schedule->repair_status = 'Waiting Part';
					$jig_schedule->save();
				}

				$status = true;
				$message = 'Jig Belum di Repair. Menunggu Part';
			}

			$response = array(
				'status' => $status,
				'message' => $message,
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => $status,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function inputRepairJigProses(Request $request)
	{
		try {
			$operator_id = $request->get('operator_id');
			$jig_id = $request->get('jig_id');
			$jig_index = $request->get('jig_index');
			$started_at = $request->get('started_at');
			$check_indexes = $request->get('check_indexes');
			$check_index = explode(',', $request->get('check_index'));
			$check_name = explode(',', $request->get('check_name'));
			$upper_limit = explode(',',$request->get('upper_limit'));
			$lower_limit = explode(',', $request->get('lower_limit'));
			$value = explode(',', $request->get('value'));
			$result = explode(',',$request->get('result'));
			$jig_child = explode(',', $request->get('jig_child'));
			$jig_alias = explode(',', $request->get('jig_alias'));
			$action = explode(',', $request->get('action'));
			$jig_parent = $request->get('jig_parent');
			$part = $request->get('part');
			$count = $request->get('count');
			$status = $request->get('status');
			$finished_at = date('Y-m-d H:i:s');
			$now = date('Y-m-d');
			$id_user = Auth::id();

			$jig_parents = [];
			$jig_childs = [];
			$jig_aliases = [];

			$filename = "";
        	$file_destination = 'data_file/jig';

        	$file_before = $request->file('newAttachment_before');
            $filename_before = 'jig_process_before_'.$jig_id.'_'.date('YmdHisa').'.'.$request->input('extension_before');
            $file_before->move($file_destination, $filename_before);

            $file_after = $request->file('newAttachment_after');
            $filename_after = 'jig_process_after_'.$jig_id.'_'.date('YmdHisa').'.'.$request->input('extension_after');
            $file_after->move($file_destination, $filename_after);

			if ($status == 'Repaired') {
				for ($k=0; $k < $check_indexes; $k++) { 
					if ($result[$k] == 'NG') {
						$jig_aliases[] = $jig_alias[$k];
						$jig_childs[] = $jig_child[$k];
						$jig_parents[] = $jig_id;
					}
				}

				$usageserror = 0;

				for ($i = 0; $i < count($jig_aliases); $i++) {
					if (stripos($jig_childs[$i], '-0') == FALSE) {
						$jigbom = DB::connection('ympimis_2')->table('jig_bom_processes')->where('jig_parent',$jig_parents[$i])->where('jig_child',$jig_childs[$i])->first();
						$usage = $jigbom->usage;

						$partsstock = DB::connection('ympimis_2')->table('jig_part_stock_processes')->where('jig_id',$jig_aliases[$i])->get();

						foreach ($partsstock as $key) {
							$id_partstock = $key->id;
							$stock = $key->quantity;
							$min_order = $key->min_order;
							$material_jig = $key->material;
							$remarkjig = $key->remark;
						}

						if ($stock < $usage) {
							$usageserror++;
						}else{
							if ($remarkjig == null) {
								$date = date('Y-m-d');
								$prefix_now = 'WJO'.date("y").date("m");
								$code_generator = CodeGenerator::where('note','=','wjo')->first();
								if ($prefix_now != $code_generator->prefix){
									$code_generator->prefix = $prefix_now;
									$code_generator->index = '0';
									$code_generator->save();
								}

								$jigs = DB::connection('ympimis_2')->table('jig_processes')->where('jig_id',$jig_childs[$i])->get();

								foreach ($jigs as $key) {
									$item_name = $key->jig_name;
								}

								$sub_section = 'Welding Process_Koshuha Solder';
								$item_name = $item_name;
								$category = 'Jig';
								$drawing_name = $item_name;
								$item_number = $jig_childs[$i];
								$part_number = $jig_aliases[$i];
								$quantity = $min_order;
								$priority = 'Normal';
								$type = 'Pembuatan Baru';
								$material = $material_jig;
								$problem_desc = 'Pembuatan Part Proses Jig Welding';

								$remark;
								if($priority == 'Normal'){
									$remark = 1;
								}else{
									$remark = 0;
								}

								$request_date = date('Y-m-d', strtotime($date. ' + 14 days'));

								$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
								$order_no = $code_generator->prefix . $number;
								$code_generator->index = $code_generator->index+1;
								$code_generator->save();

								$file_name = $order_no.'.pdf';
								$path = public_path(). '/workshop';
								$file_path = public_path() . "/jig/drawing/" .$jig_parents[$i].'/'.$jig_childs[$i].'.pdf';

								$newplace  = $path.'/'.$order_no.'.pdf';
								copy($file_path,$newplace);

								$wjo = new WorkshopJobOrder([
									'order_no' => $order_no,
									'sub_section' => $sub_section,
									'item_name' => $item_name,
									'category' => $category,
									'drawing_name' => $drawing_name,
									'item_number' => $item_number,
									'part_number' => $part_number,
									'quantity' => $quantity,
									'target_date' => $request_date,
									'priority' => $priority,
									'type' => $type,
									'material' => $material,
									'automation' => 'auto',
									'problem_description' => $problem_desc,
									'remark' => $remark,
									'attachment' => $file_name,
									'created_by' => 'PI9902015',
								]);

								$wjo_log = new WorkshopJobOrderLog([
									'order_no' => $order_no,
									'remark' => $remark,
									'created_by' => 'PI9902015',
								]);

								$wjo->save();
								$wjo_log->save();

								$qtynew = $stock - $usage;

								$partsstocks = DB::connection('ympimis_2')->table('jig_part_stock_processes')->where('id',$id_partstock)->update([
									'quantity' => $qtynew,
									'quantity_order' => $min_order,
									'remark' => $order_no,
									'updated_at' => date('Y-m-d H:i:s'),
								]);
								// $partsstocks->quantity = $qtynew;
								// $partsstocks->quantity_order = $min_order;
								// $partsstocks->remark = $order_no;
								// $partsstocks->save();
							}
						}
					}
				}

				if ($usageserror > 0) {
					$status = false;
					$message = 'Part Tidak Tersedia';
				}else{
					$qtynew = $stock - $usage;
					$partsstocks = DB::connection('ympimis_2')->table('jig_part_stock_processes')->where('id',$id_partstock)->update([
						'quantity' => $qtynew,
						'updated_at' => date('Y-m-d H:i:s'),
					]);

					// $partsstocks = JigPartStock::find($id_partstock);
					// $partsstocks->quantity = $qtynew;
					// $partsstocks->save();

					for ($j=0; $j < $check_indexes; $j++) {

						$repair = DB::connection('ympimis_2')->table('jig_repair_log_processes')->insert([
							'operator_id' => $operator_id,
							'jig_id' => $jig_id,
							'jig_index' => $jig_index,
							'check_index' => $check_index[$j],
							'check_name' => $check_name[$j],
							'upper_limit' => $upper_limit[$j],
							'lower_limit' => $lower_limit[$j],
							'value' => $value[$j],
							'result' => $result[$j],
							'jig_child' => $jig_child[$j],
							'jig_alias' => $jig_alias[$j],
							'status' => 'Repaired',
							'action' => $action[$j],
							'started_at' => $started_at,
							'finished_at' => $finished_at,
							'image_before' => $filename_before,
							'image_before' => $filename_before,
							'created_by' => $id_user,
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s'),
						]);
						// $repair->save();
					}

					$jigKensa = DB::connection('ympimis_2')->table('jig_kensa_processes')->where('jig_kensa_processes.jig_id', '=', $jig_id)
					->where('status','Repair')
					->get();

					foreach ($jigKensa as $key) {
						$kensa_jig = DB::connection('ympimis_2')->table('jig_kensa_log_processes')->insert([
							'operator_id' => $key->operator_id,
							'jig_id' => $key->jig_id,
							'jig_index' => $key->jig_index,
							'jig_alias' => $key->jig_alias,
							'check_index' => $key->check_index,
							'check_name' => $key->check_name,
							'upper_limit' => $key->upper_limit,
							'lower_limit' => $key->lower_limit,
							'value' => $key->value,
							'result' => $key->result,
							'status' => 'Repaired',
							'jig_child' => $key->jig_child,
							'started_at' => $key->started_at,
							'finished_at' => $key->finished_at,
							'image_before' => $key->image_before,
							'image_after' => $key->image_after,
							'created_by' => $id_user,
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s'),
						]);
						// $kensa_jig->save();
						$kensas = DB::connection('ympimis_2')->table('jig_kensa_processes')->where('id',$key->id)->delete();
					}

					$jigses = DB::connection('ympimis_2')->table('jig_processes')->where('jig_id',$jig_id)->first();
					$check_period = $jigses->check_period;

					$jig_schedule = DB::connection('ympimis_2')->table('jig_schedule_processes')->where('jig_id',$jig_id)->where('jig_index',$jig_index)->where('schedule_status','Open')->first();
					if (count($jig_schedule) > 0) {
						$jig_schedule_update = DB::connection('ympimis_2')->table('jig_schedule_processes')->where('jig_id',$jig_id)->where('jig_index',$jig_index)->where('schedule_status','Open')->update([
							'repair_time' => $finished_at,
							'repair_pic' => $operator_id,
							'repair_status' => 'Finish Repair',
							'schedule_status' => 'Close',
							'updated_at' => date('Y-m-d H:i:s'),
						]);
						// $jig_schedule->repair_time = $finished_at;
						// $jig_schedule->repair_pic = $operator_id;
						// $jig_schedule->repair_status = 'Finish Repair';
						// $jig_schedule->schedule_status = 'Close';
						// $jig_schedule->save();

						$week_date = WeeklyCalendar::where('week_date',date('Y-m-d', strtotime($now. ' + '.$check_period.' days')))->first();
						if ($week_date->remark == 'H') {
							$new_week_date = WeeklyCalendar::where('week_date','>',date('Y-m-d', strtotime($now. ' + '.$check_period.' days')))->where('remark','H')->limit(1)->first();
							$new_schedule = $new_week_date->week_date;
						}else{
							$new_schedule = date('Y-m-d', strtotime($now. ' + '.$check_period.' days'));
						}

						$weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
			            foreach ($weekly_calendars as $key) {
			                if ($key->week_date == $new_schedule) {
			                    if ($key->remark == 'H') {
			                        $new_schedule = date('Y-m-d', strtotime($new_schedule.' + 1 days'));
			                    }
			                }
			            }
						
						$schedule = DB::connection('ympimis_2')->table('jig_schedule_processes')->insert([
							'jig_id' => $jig_id,
							'jig_index' => $jig_index,
							'schedule_date' => $new_schedule,
							'schedule_status' => 'Open',
							'created_by' => $id_user,
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s'),
						]);
						// $schedule->save();
					}

					$status = true;
					$message = 'Repair Jig Selesai';
				}
			}else{
				for ($k=0; $k < $check_indexes; $k++) {
					$kensa = DB::connection('ympimis_2')->table('jig_kensa_processes')->where('jig_id',$jig_id)->where('jig_index',$jig_index)->where('check_index',$check_index[$k])->update([
						'action' => $action[$k]
					]);
					// $kensa->action = $action[$k];
					// $kensa->save();
				}

				$jigses = DB::connection('ympimis_2')->table('jig_processes')->where('jig_id',$jig_id)->first();
				$check_period = $jigses->check_period;

				$jig_schedule = DB::connection('ympimis_2')->table('jig_schedule_processes')->where('jig_id',$jig_id)->where('jig_index',$jig_index)->where('schedule_status','Open')->first();
				if (count($jig_schedule) > 0) {
					$jig_schedule = DB::connection('ympimis_2')->table('jig_schedule_processes')->where('jig_id',$jig_id)->where('jig_index',$jig_index)->where('schedule_status','Open')->update([
						'repair_time' => $finished_at,
						'repair_pic' => $operator_id,
						'repair_status' => 'Waiting Part',
					]);
					// $jig_schedule->repair_time = $finished_at;
					// $jig_schedule->repair_pic = $operator_id;
					// $jig_schedule->repair_status = 'Waiting Part';
					// $jig_schedule->save();
				}

				$status = true;
				$message = 'Jig Belum di Repair. Menunggu Part';
			}

			$response = array(
				'status' => $status,
				'message' => $message,
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => $status,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function fetchWldJigMonitoring(Request $request)
	{
		try {

			$date_from = $request->get('date_from');
	          $date_to = $request->get('date_to');
	          if ($date_from == '') {
	               if ($date_to == '') {
	                    $first = "DATE_FORMAT( NOW(), '%Y-%m-01' )";
	                    $last = "DATE_FORMAT( LAST_DAY( NOW()), '%Y-%m-%d' )";
	                    $firstDateTitle = date('01 M Y');
	                    $lastDateTitle = date('t M Y');
	               }else{
	                    $first = "DATE_FORMAT( NOW(), '%Y-%m-01' )";
	                    $last = "'".$date_to."'";
	                    $firstDateTitle = date('01 M Y');
	                    $lastDateTitle = date('d M Y',strtotime($date_to));
	               }
	               $date_now = "NOW()";
	          }else{
	               if ($date_to == '') {
	                    $first = "'".$date_from."'";
	                    $last = "DATE_FORMAT( LAST_DAY( NOW()), '%Y-%m-%d' )";
	                    $firstDateTitle = date('d M Y',strtotime($date_from));
	                    $lastDateTitle = date('t M Y');
	               }else{
	                    $first = "'".$date_from."'";
	                    $last = "'".$date_to."'";
	                    $firstDateTitle = date('d M Y',strtotime($date_from));
	                    $lastDateTitle = date('d M Y',strtotime($date_to));
	               }
	               $date_now = "'".$date_from."'";
	          }

			$monitoring = DB::SELECT("SELECT
				b.week_date,
				SUM( b.before_kensa ) AS before_kensa,
				SUM( b.after_kensa ) AS after_kensa,
				SUM( b.before_repair ) AS before_repair,
				SUM( b.waiting_part ) AS waiting_part 
				FROM
				(
				SELECT DISTINCT
				( week_date ),
				(
				SELECT
				COUNT(
				DISTINCT ( id )) 
				FROM
				jig_schedules 
				WHERE
				jig_schedules.schedule_date = week_date 
				-- AND schedule_status = 'Open' 
				-- AND kensa_time IS NULL 
				-- AND repair_time IS NULL 
				) AS before_kensa,
				( SELECT COUNT( DISTINCT ( id )) FROM jig_schedules WHERE DATE(jig_schedules.kensa_time) = week_date AND schedule_status = 'Close' AND kensa_time IS NOT NULL ) AS after_kensa,
				(
				SELECT
				COUNT(
				DISTINCT ( id )) 
				FROM
				jig_schedules 
				WHERE
				DATE(jig_schedules.kensa_time) = week_date 
				AND schedule_status = 'Open' 
				AND kensa_time IS NOT NULL 
				AND repair_time IS NULL 
				) AS before_repair,
				(
				SELECT
				COUNT(
				DISTINCT ( id )) 
				FROM
				jig_schedules 
				WHERE
				DATE(jig_schedules.repair_time) = week_date 
				AND schedule_status = 'Open' 
				AND kensa_time IS NOT NULL 
				AND repair_time IS NOT NULL 
				) AS waiting_part 
				FROM
				weekly_calendars 
				WHERE
				week_date BETWEEN ".$first." 
				AND ".$last." UNION ALL
				SELECT
				jig_schedules.schedule_date,
				COUNT(
				DISTINCT ( id )) AS before_kensa,
				0 AS after_kensa,
				0 AS before_repair,
				0 AS waiting_part 
				FROM
				jig_schedules 
				WHERE
				schedule_date < ".$first."
				AND schedule_status = 'Open' 
				AND kensa_time IS NULL 
				GROUP BY
				jig_schedules.schedule_date
				) b 
				GROUP BY
				b.week_date 
				ORDER BY
				b.week_date ASC");

			$resume = DB::SELECT("SELECT
				a.week_date,
				a.before_kensa,
				a.after_kensa,
				a.before_repair,
				a.waiting_part 
			FROM
				(
				SELECT
					a.week_date,
					(
					SELECT
						COUNT(
						DISTINCT ( id )) 
					FROM
						jig_schedules 
					WHERE
						jig_schedules.schedule_date = a.week_date 
						AND schedule_status = 'Open' 
						AND kensa_time IS NULL 
						AND repair_time IS NULL 
					) AS before_kensa,
					( SELECT COUNT( DISTINCT ( id )) FROM jig_schedules WHERE jig_schedules.schedule_date = a.week_date AND schedule_status = 'Close' AND kensa_time IS NOT NULL ) AS after_kensa,
					(
					SELECT
						COUNT(
						DISTINCT ( id )) 
					FROM
						jig_schedules 
					WHERE
						jig_schedules.schedule_date = a.week_date 
						AND schedule_status = 'Open' 
						AND kensa_time IS NOT NULL 
						AND repair_time IS NULL 
					) AS before_repair,
					(
					SELECT
						COUNT(
						DISTINCT ( id )) 
					FROM
						jig_schedules 
					WHERE
						jig_schedules.schedule_date = a.week_date 
						AND schedule_status = 'Open' 
						AND kensa_time IS NOT NULL 
						AND repair_time IS NOT NULL 
					) AS waiting_part 
				FROM
					weekly_calendars a 
				WHERE
					a.week_date BETWEEN ".$first." 
					AND DATE(
					NOW()) UNION ALL
				SELECT
					jig_schedules.schedule_date AS week_date,
					COUNT(
					DISTINCT ( id )) AS before_kensa,
					0 AS after_kensa,
					0 AS before_repair,
					0 AS waiting_part 
				FROM
					jig_schedules 
				WHERE
					schedule_date < ".$first."
					AND schedule_status = 'Open' 
					AND kensa_time IS NULL 
				GROUP BY
					jig_schedules.schedule_date 
				) a 
			ORDER BY
				a.week_date");

			$outstanding = DB::SELECT("SELECT
				b.* 
			FROM
				(
				SELECT
					jig_schedules.jig_id,
					jigs.jig_name,
					COALESCE ( kensa_time, '' ) AS kensa_time,
					COALESCE ( empkensa.NAME, '' ) AS kensa_pic,
					COALESCE ( kensa_status, '' ) AS kensa_status,
					COALESCE ( repair_time, '' ) AS repair_time,
					COALESCE ( emprepair.NAME, '' ) AS repair_pic,
					COALESCE ( repair_status, '' ) AS repair_status,
					schedule_status,
					schedule_date 
				FROM
					`jig_schedules`
					LEFT JOIN employee_syncs empkensa ON empkensa.employee_id = jig_schedules.kensa_pic
					LEFT JOIN employee_syncs emprepair ON emprepair.employee_id = jig_schedules.repair_pic
					JOIN jigs ON jigs.jig_id = jig_schedules.jig_id 
				WHERE
					schedule_date BETWEEN ".$first." 
					AND ".$last." UNION
				SELECT
					jig_schedules.jig_id,
					jigs.jig_name,
					COALESCE ( kensa_time, '' ) AS kensa_time,
					COALESCE ( empkensa.NAME, '' ) AS kensa_pic,
					COALESCE ( kensa_status, '' ) AS kensa_status,
					COALESCE ( repair_time, '' ) AS repair_time,
					COALESCE ( emprepair.NAME, '' ) AS repair_pic,
					COALESCE ( repair_status, '' ) AS repair_status,
					schedule_status,
					schedule_date 
				FROM
					`jig_schedules`
					LEFT JOIN employee_syncs empkensa ON empkensa.employee_id = jig_schedules.kensa_pic
					LEFT JOIN employee_syncs emprepair ON emprepair.employee_id = jig_schedules.repair_pic
					JOIN jigs ON jigs.jig_id = jig_schedules.jig_id 
				WHERE
					schedule_status = 'Open' 
					AND schedule_date < ".$first.") b 
			ORDER BY
				b.schedule_date,
				b.repair_status,
				b.kensa_status");

			$monitoring_quartal = DB::SELECT("SELECT
				CONCAT(
					CONCAT( YEAR ( ".$date_now."), '-01' ),
					' - ',
					CONCAT( YEAR ( ".$date_now."), '-03' )) AS dates,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) AS before_kensa 
				FROM
					jig_schedules 
				WHERE
					jig_schedules.schedule_date BETWEEN CONCAT( YEAR ( ".$date_now."), '-01-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-03-01' ))) AS before_kensa,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedules 
				WHERE
					DATE( jig_schedules.kensa_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-01-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-03-01' )) 
					AND schedule_status = 'Close' 
					AND kensa_time IS NOT NULL 
				) AS after_kensa,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedules 
				WHERE
					DATE( jig_schedules.kensa_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-01-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-03-01' )) 
					AND schedule_status = 'Open' 
					AND kensa_time IS NOT NULL 
					AND repair_time IS NULL 
				) AS before_repair,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedules 
				WHERE
					DATE( jig_schedules.repair_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-01-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-03-01' )) 
					AND schedule_status = 'Open' 
					AND kensa_time IS NOT NULL 
					AND repair_time IS NOT NULL 
				) AS waiting_part UNION ALL
			SELECT
				CONCAT(
					CONCAT( YEAR ( ".$date_now."), '-04' ),
					' - ',
					CONCAT( YEAR ( ".$date_now."), '-06' )) AS dates,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) AS before_kensa 
				FROM
					jig_schedules 
				WHERE
					jig_schedules.schedule_date BETWEEN CONCAT( YEAR ( ".$date_now."), '-04-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-06-01' ))) AS before_kensa,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedules 
				WHERE
					DATE( jig_schedules.kensa_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-04-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-06-01' )) 
					AND schedule_status = 'Close' 
					AND kensa_time IS NOT NULL 
				) AS after_kensa,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedules 
				WHERE
					DATE( jig_schedules.kensa_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-04-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-06-01' )) 
					AND schedule_status = 'Open' 
					AND kensa_time IS NOT NULL 
					AND repair_time IS NULL 
				) AS before_repair,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedules 
				WHERE
					DATE( jig_schedules.repair_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-04-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-06-01' )) 
					AND schedule_status = 'Open' 
					AND kensa_time IS NOT NULL 
					AND repair_time IS NOT NULL 
				) AS waiting_part UNION ALL
			SELECT
				CONCAT(
					CONCAT( YEAR ( ".$date_now."), '-07' ),
					' - ',
					CONCAT( YEAR ( ".$date_now."), '-09' )) AS dates,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) AS before_kensa 
				FROM
					jig_schedules 
				WHERE
					jig_schedules.schedule_date BETWEEN CONCAT( YEAR ( ".$date_now."), '-07-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-09-01' ))) AS before_kensa,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedules 
				WHERE
					DATE( jig_schedules.kensa_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-07-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-09-01' )) 
					AND schedule_status = 'Close' 
					AND kensa_time IS NOT NULL 
				) AS after_kensa,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedules 
				WHERE
					DATE( jig_schedules.kensa_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-07-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-09-01' )) 
					AND schedule_status = 'Open' 
					AND kensa_time IS NOT NULL 
					AND repair_time IS NULL 
				) AS before_repair,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedules 
				WHERE
					DATE( jig_schedules.repair_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-07-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-09-01' )) 
					AND schedule_status = 'Open' 
					AND kensa_time IS NOT NULL 
					AND repair_time IS NOT NULL 
				) AS waiting_part UNION ALL
			SELECT
				CONCAT(
					CONCAT( YEAR ( ".$date_now."), '-10' ),
					' - ',
					CONCAT( YEAR ( ".$date_now."), '-12' )) AS dates,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) AS before_kensa 
				FROM
					jig_schedules 
				WHERE
					jig_schedules.schedule_date BETWEEN CONCAT( YEAR ( ".$date_now."), '-10-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-12-01' ))) AS before_kensa,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedules 
				WHERE
					DATE( jig_schedules.kensa_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-10-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-12-01' )) 
					AND schedule_status = 'Close' 
					AND kensa_time IS NOT NULL 
				) AS after_kensa,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedules 
				WHERE
					DATE( jig_schedules.kensa_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-10-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-12-01' )) 
					AND schedule_status = 'Open' 
					AND kensa_time IS NOT NULL 
					AND repair_time IS NULL 
				) AS before_repair,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedules 
				WHERE
					DATE( jig_schedules.repair_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-10-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-12-01' )) 
					AND schedule_status = 'Open' 
					AND kensa_time IS NOT NULL 
				AND repair_time IS NOT NULL 
				) AS waiting_part");

			$response = array(
				'status' => true,
				'monitoring' => $monitoring,
				'outstanding' => $outstanding,
				'resume' => $resume,
				'monitoring_quartal' => $monitoring_quartal,
				'firstDateTitle' => $firstDateTitle,
				'lastDateTitle' => $lastDateTitle,
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

	public function fetchWldJigMonitoringProcess(Request $request)
	{
		try {

			$date_from = $request->get('date_from');
	          $date_to = $request->get('date_to');
	          if ($date_from == '') {
	               if ($date_to == '') {
	                    $first = "DATE_FORMAT( NOW(), '%Y-%m-01' )";
	                    $last = "DATE_FORMAT( LAST_DAY( NOW()), '%Y-%m-%d' )";
	                    $firstDateTitle = date('01 M Y');
	                    $lastDateTitle = date('t M Y');
	               }else{
	                    $first = "DATE_FORMAT( NOW(), '%Y-%m-01' )";
	                    $last = "'".$date_to."'";
	                    $firstDateTitle = date('01 M Y');
	                    $lastDateTitle = date('d M Y',strtotime($date_to));
	               }
	               $date_now = "NOW()";
	          }else{
	               if ($date_to == '') {
	                    $first = "'".$date_from."'";
	                    $last = "DATE_FORMAT( LAST_DAY( NOW()), '%Y-%m-%d' )";
	                    $firstDateTitle = date('d M Y',strtotime($date_from));
	                    $lastDateTitle = date('t M Y');
	               }else{
	                    $first = "'".$date_from."'";
	                    $last = "'".$date_to."'";
	                    $firstDateTitle = date('d M Y',strtotime($date_from));
	                    $lastDateTitle = date('d M Y',strtotime($date_to));
	               }
	               $date_now = "'".$date_from."'";
	          }


	        $monitoring = [];
	        $resume = [];

	        $weekly_date = DB::select("SELECT DISTINCT
				( week_date ) 
			FROM
				weekly_calendars 
			WHERE
				week_date BETWEEN ".$first."
				AND ".$last);

	        for ($i=0; $i < count($weekly_date); $i++) { 
	        	$monitorings = DB::connection('ympimis_2')
	        	->select("SELECT
					'".$weekly_date[$i]->week_date."' AS week_date,
					sum( a.before_kensa ) AS before_kensa,
					sum( a.after_kensa ) AS after_kensa,
					sum( a.before_repair ) AS before_repair,
					sum( a.waiting_part ) AS waiting_part 
				FROM
					(
					SELECT
						COUNT(
						DISTINCT ( id )) AS before_kensa,
						0 AS after_kensa,
						0 AS before_repair,
						0 AS waiting_part 
					FROM
						jig_schedule_processes 
					WHERE
						jig_schedule_processes.schedule_date = '".$weekly_date[$i]->week_date."' UNION ALL
					SELECT
						0 AS before_kensa,
						COUNT(
						DISTINCT ( id )) AS after_kensa,
						0 AS before_repair,
						0 AS waiting_part 
					FROM
						jig_schedule_processes 
					WHERE
						DATE( jig_schedule_processes.kensa_time ) = '".$weekly_date[$i]->week_date."' 
						AND schedule_status = 'Close' 
						AND kensa_time IS NOT NULL UNION ALL
					SELECT
						0 AS before_kensa,
						0 AS after_kensa,
						COUNT(
						DISTINCT ( id )) AS before_repair,
						0 AS waiting_part 
					FROM
						jig_schedule_processes 
					WHERE
						DATE( jig_schedule_processes.kensa_time ) = '".$weekly_date[$i]->week_date."' 
						AND schedule_status = 'Open' 
						AND kensa_time IS NOT NULL 
						AND repair_time IS NULL UNION ALL
					SELECT
						0 AS before_kensa,
						0 AS after_kensa,
						0 AS before_repair,
						COUNT(
						DISTINCT ( id )) AS waiting_part 
					FROM
						jig_schedule_processes 
					WHERE
						DATE( jig_schedule_processes.repair_time ) = '".$weekly_date[$i]->week_date."' 
						AND schedule_status = 'Open' 
						AND kensa_time IS NOT NULL 
						AND repair_time IS NOT NULL 
					) a");

	        	array_push($monitoring, $monitorings);

	        	$resumes = DB::connection('ympimis_2')
	        	->select("SELECT
					'".$weekly_date[$i]->week_date."' AS week_date,
					sum( a.before_kensa ) AS before_kensa,
					sum( a.after_kensa ) AS after_kensa,
					sum( a.before_repair ) AS before_repair,
					sum( a.waiting_part ) AS waiting_part 
				FROM
					(
					SELECT
						COUNT(
						DISTINCT ( id )) AS before_kensa,
						0 AS after_kensa,
						0 AS before_repair,
						0 AS waiting_part 
					FROM
						jig_schedule_processes 
					WHERE
						jig_schedule_processes.schedule_date = '".$weekly_date[$i]->week_date."' 
						AND schedule_status = 'Open' 
						AND kensa_time IS NULL 
						AND repair_time IS NULL UNION ALL
					SELECT
						0 AS before_kensa,
						COUNT(
						DISTINCT ( id )) AS after_kensa,
						0 AS before_repair,
						0 AS waiting_part 
					FROM
						jig_schedule_processes 
					WHERE
						jig_schedule_processes.schedule_date = '".$weekly_date[$i]->week_date."' 
						AND schedule_status = 'Close' 
						AND kensa_time IS NOT NULL UNION ALL
					SELECT
						0 AS before_kensa,
						0 AS after_kensa,
						COUNT(
						DISTINCT ( id )) AS before_repair,
						0 AS waiting_part 
					FROM
						jig_schedule_processes 
					WHERE
						jig_schedule_processes.schedule_date = '".$weekly_date[$i]->week_date."' 
						AND schedule_status = 'Open' 
						AND kensa_time IS NOT NULL 
						AND repair_time IS NULL UNION ALL
					SELECT
						0 AS before_kensa,
						0 AS after_kensa,
						0 AS before_repair,
						COUNT(
						DISTINCT ( id )) AS waiting_part 
					FROM
						jig_schedule_processes 
					WHERE
						jig_schedule_processes.schedule_date = '".$weekly_date[$i]->week_date."' 
						AND schedule_status = 'Open' 
						AND kensa_time IS NOT NULL 
					AND repair_time IS NOT NULL 
					) a");

	        	array_push($resume, $resumes);
	        }

			$outstanding = DB::connection('ympimis_2')->SELECT("SELECT
				b.* 
			FROM
				(
				SELECT
					jig_schedule_processes.jig_id,
					jig_processes.jig_name,
					COALESCE ( kensa_time, '' ) AS kensa_time,
					COALESCE ( kensa_status, '' ) AS kensa_status,
					COALESCE ( repair_time, '' ) AS repair_time,
					COALESCE ( repair_status, '' ) AS repair_status,
					schedule_status,
					schedule_date,
					kensa_pic,
					repair_pic
				FROM
					`jig_schedule_processes`
					JOIN jig_processes ON jig_processes.jig_id = jig_schedule_processes.jig_id 
				WHERE
					schedule_status = 'Open' 
					AND schedule_date BETWEEN ".$first."
					AND ".$last." UNION
				SELECT
					jig_schedule_processes.jig_id,
					jig_processes.jig_name,
					COALESCE ( kensa_time, '' ) AS kensa_time,
					COALESCE ( kensa_status, '' ) AS kensa_status,
					COALESCE ( repair_time, '' ) AS repair_time,
					COALESCE ( repair_status, '' ) AS repair_status,
					schedule_status,
					schedule_date,
					kensa_pic,
					repair_pic
				FROM
					`jig_schedule_processes`
					JOIN jig_processes ON jig_processes.jig_id = jig_schedule_processes.jig_id 
				WHERE
					schedule_status = 'Open' 
					AND schedule_date < ".$first."
				) b 
			ORDER BY
				b.schedule_date,
				b.repair_status,
				b.kensa_status");

			$operator_outstanding = [];

			for ($i=0; $i < count($outstanding); $i++) { 
				$empkensas = '';
				$emprepaires = '';
				if ($outstanding[$i]->kensa_pic != null) {
					$empkensa = EmployeeSync::where('employee_id',$outstanding[$i]->kensa_pic)->first();
					$empkensas = $empkensa->name;
				}
				if ($outstanding[$i]->repair_pic != null) {
					$emprepair = EmployeeSync::where('employee_id',$outstanding[$i]->repair_pic)->first();
					$emprepaires = $emprepair->name;
				}
				array_push($operator_outstanding, $empkensas.'_'.$emprepaires);
			}

			$monitoring_quartal = DB::connection('ympimis_2')->SELECT("SELECT
				CONCAT(
					CONCAT( YEAR ( ".$date_now."), '-01' ),
					' - ',
					CONCAT( YEAR ( ".$date_now."), '-03' )) AS dates,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) AS before_kensa 
				FROM
					jig_schedule_processes 
				WHERE
					jig_schedule_processes.schedule_date BETWEEN CONCAT( YEAR ( ".$date_now."), '-01-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-03-01' ))) AS before_kensa,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedule_processes 
				WHERE
					DATE( jig_schedule_processes.kensa_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-01-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-03-01' )) 
					AND schedule_status = 'Close' 
					AND kensa_time IS NOT NULL 
				) AS after_kensa,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedule_processes 
				WHERE
					DATE( jig_schedule_processes.kensa_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-01-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-03-01' )) 
					AND schedule_status = 'Open' 
					AND kensa_time IS NOT NULL 
					AND repair_time IS NULL 
				) AS before_repair,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedule_processes 
				WHERE
					DATE( jig_schedule_processes.repair_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-01-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-03-01' )) 
					AND schedule_status = 'Open' 
					AND kensa_time IS NOT NULL 
					AND repair_time IS NOT NULL 
				) AS waiting_part UNION ALL
			SELECT
				CONCAT(
					CONCAT( YEAR ( ".$date_now."), '-04' ),
					' - ',
					CONCAT( YEAR ( ".$date_now."), '-06' )) AS dates,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) AS before_kensa 
				FROM
					jig_schedule_processes 
				WHERE
					jig_schedule_processes.schedule_date BETWEEN CONCAT( YEAR ( ".$date_now."), '-04-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-06-01' ))) AS before_kensa,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedule_processes 
				WHERE
					DATE( jig_schedule_processes.kensa_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-04-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-06-01' )) 
					AND schedule_status = 'Close' 
					AND kensa_time IS NOT NULL 
				) AS after_kensa,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedule_processes 
				WHERE
					DATE( jig_schedule_processes.kensa_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-04-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-06-01' )) 
					AND schedule_status = 'Open' 
					AND kensa_time IS NOT NULL 
					AND repair_time IS NULL 
				) AS before_repair,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedule_processes 
				WHERE
					DATE( jig_schedule_processes.repair_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-04-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-06-01' )) 
					AND schedule_status = 'Open' 
					AND kensa_time IS NOT NULL 
					AND repair_time IS NOT NULL 
				) AS waiting_part UNION ALL
			SELECT
				CONCAT(
					CONCAT( YEAR ( ".$date_now."), '-07' ),
					' - ',
					CONCAT( YEAR ( ".$date_now."), '-09' )) AS dates,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) AS before_kensa 
				FROM
					jig_schedule_processes 
				WHERE
					jig_schedule_processes.schedule_date BETWEEN CONCAT( YEAR ( ".$date_now."), '-07-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-09-01' ))) AS before_kensa,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedule_processes 
				WHERE
					DATE( jig_schedule_processes.kensa_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-07-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-09-01' )) 
					AND schedule_status = 'Close' 
					AND kensa_time IS NOT NULL 
				) AS after_kensa,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedule_processes 
				WHERE
					DATE( jig_schedule_processes.kensa_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-07-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-09-01' )) 
					AND schedule_status = 'Open' 
					AND kensa_time IS NOT NULL 
					AND repair_time IS NULL 
				) AS before_repair,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedule_processes 
				WHERE
					DATE( jig_schedule_processes.repair_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-07-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-09-01' )) 
					AND schedule_status = 'Open' 
					AND kensa_time IS NOT NULL 
					AND repair_time IS NOT NULL 
				) AS waiting_part UNION ALL
			SELECT
				CONCAT(
					CONCAT( YEAR ( ".$date_now."), '-10' ),
					' - ',
					CONCAT( YEAR ( ".$date_now."), '-12' )) AS dates,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) AS before_kensa 
				FROM
					jig_schedule_processes 
				WHERE
					jig_schedule_processes.schedule_date BETWEEN CONCAT( YEAR ( ".$date_now."), '-10-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-12-01' ))) AS before_kensa,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedule_processes 
				WHERE
					DATE( jig_schedule_processes.kensa_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-10-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-12-01' )) 
					AND schedule_status = 'Close' 
					AND kensa_time IS NOT NULL 
				) AS after_kensa,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedule_processes 
				WHERE
					DATE( jig_schedule_processes.kensa_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-10-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-12-01' )) 
					AND schedule_status = 'Open' 
					AND kensa_time IS NOT NULL 
					AND repair_time IS NULL 
				) AS before_repair,
				(
				SELECT
					COUNT(
					DISTINCT ( id )) 
				FROM
					jig_schedule_processes 
				WHERE
					DATE( jig_schedule_processes.repair_time ) BETWEEN CONCAT( YEAR ( ".$date_now."), '-10-01' ) 
					AND LAST_DAY(
					CONCAT( YEAR ( ".$date_now."), '-12-01' )) 
					AND schedule_status = 'Open' 
					AND kensa_time IS NOT NULL 
				AND repair_time IS NOT NULL 
				) AS waiting_part");

			$response = array(
				'status' => true,
				'monitoring' => $monitoring,
				'outstanding' => $outstanding,
				'resume' => $resume,
				'monitoring_quartal' => $monitoring_quartal,
				'operator_outstanding' => $operator_outstanding,
				'firstDateTitle' => $firstDateTitle,
				'lastDateTitle' => $lastDateTitle,
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

	public function fetchWldDetailJigMonitoring(Request $request)
	{
		try {
			$date = $request->get('date');
			$status = $request->get('status');
			$dateTitle = strtoupper(date("d M Y", strtotime($request->get('date'))));

			$detail = [];

			if ($status == 'Schedule Kensa') {
				$schedule = DB::SELECT("select jig_schedules.*,jigs.jig_name from jig_schedules join jigs on jigs.jig_id = jig_schedules.jig_id where schedule_date = '".$date."' ");

				foreach ($schedule as $key) {
					$detail[$key->jig_id] = DB::SELECT("select jig_schedules.*,jigs.jig_name from jig_schedules join jig_kensa_logs on jig_kensa_logs.jig_id = jig_schedules.jig_id and DATE(finished_at) = schedule_date join jigs on jigs.jig_id = jig_schedules.jig_id where schedule_date = '".$date."' and jig_schedules.jig_id = '".$key->jig_id."'");
				}

				$judul = 'DETAIL SCHEDULE WELDING KENSA JIG TANGGAL '.$dateTitle;
			}elseif ($status == 'Sudah Kensa') {
				$schedule = DB::SELECT("select * from jig_schedules join jigs on jigs.jig_id = jig_schedules.jig_id where DATE(kensa_time) = '".$date."' and schedule_status = 'Close'");

				foreach ($schedule as $key) {
					$detail[$key->jig_id] = DB::SELECT("SELECT
						*,
						CONCAT(
						SPLIT_STRING ( empkensa.NAME, ' ', 1 ),
						' ',
						SPLIT_STRING ( empkensa.NAME, ' ', 2 )) AS kensaemp 
						FROM
						jig_schedules
						JOIN jig_kensa_logs ON jig_kensa_logs.jig_id = jig_schedules.jig_id 
						AND DATE( finished_at ) = DATE(kensa_time)
						JOIN jigs ON jigs.jig_id = jig_schedules.jig_id
						LEFT JOIN employee_syncs empkensa ON empkensa.employee_id = jig_schedules.kensa_pic 
						WHERE
						DATE(kensa_time) = '".$date."' 
						AND schedule_status = 'Close' 
						AND jig_schedules.jig_id = '".$key->jig_id."'");
				}

				$judul = 'DETAIL WELDING KENSA JIG YANG SUDAH KENSA TANGGAL '.$dateTitle;
			}elseif ($status == 'Belum Repair') {
				$schedule = DB::SELECT("select * from jig_schedules join jigs on jigs.jig_id = jig_schedules.jig_id where DATE(kensa_time) = '".$date."' and schedule_status = 'Open' and kensa_time is not null and repair_time is null");

				foreach ($schedule as $key) {
					$detail[$key->jig_id] = DB::SELECT("SELECT
						*,
						CONCAT(
						SPLIT_STRING ( empkensa.NAME, ' ', 1 ),
						' ',
						SPLIT_STRING ( empkensa.NAME, ' ', 2 )) AS kensaemp 
						FROM
						jig_schedules
						JOIN jig_kensas ON jig_kensas.jig_id = jig_schedules.jig_id 
						AND DATE( finished_at ) = DATE(kensa_time)
						JOIN jigs ON jigs.jig_id = jig_schedules.jig_id 
						LEFT JOIN employee_syncs empkensa ON empkensa.employee_id = jig_schedules.kensa_pic 
						WHERE
						DATE(kensa_time) = '".$date."' 
						AND schedule_status = 'Open' 
						AND jig_schedules.jig_id = '".$key->jig_id."' 
						AND kensa_time IS NOT NULL 
						AND repair_time IS NULL");
				}

				$judul = 'DETAIL WELDING KENSA JIG YANG BELUM REPAIR TANGGAL '.$dateTitle;
			}elseif ($status == 'Menunggu Part') {
				$schedule = DB::SELECT("select * from jig_schedules join jigs on jigs.jig_id = jig_schedules.jig_id where DATE(repair_time) = '".$date."' and schedule_status = 'Open' and kensa_time is not null and repair_time is not null");

				foreach ($schedule as $key) {
					$detail[$key->jig_id] = DB::SELECT("SELECT
						* ,
						CONCAT(
						SPLIT_STRING ( empkensa.NAME, ' ', 1 ),
						' ',
						SPLIT_STRING ( empkensa.NAME, ' ', 2 )) AS kensaemp 
						FROM
						jig_schedules
						JOIN jig_kensas ON jig_kensas.jig_id = jig_schedules.jig_id 
						AND DATE( finished_at ) = DATE(repair_time)
						JOIN jigs ON jigs.jig_id = jig_schedules.jig_id 
						LEFT JOIN employee_syncs empkensa ON empkensa.employee_id = jig_schedules.kensa_pic 
						WHERE
						DATE(repair_time) = '".$date."' 
						AND schedule_status = 'Open' 
						AND jig_schedules.jig_id = '".$key->jig_id."' 
						AND kensa_time IS NOT NULL 
						AND repair_time IS NOT NULL");
				}

				$judul = 'DETAIL WELDING KENSA JIG YANG MENUNGGU PART TANGGAL '.$dateTitle;
			}

			$response = array(
				'status' => true,
				'message' => 'Success Get Data',
				'detail' => $detail,
				'schedule' => $schedule,
				'judul' => $judul,
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

	public function fetchWldDetailJigMonitoringProcess(Request $request)
	{
		try {
			$date = $request->get('date');
			$status = $request->get('status');
			$dateTitle = strtoupper(date("d F Y", strtotime($request->get('date'))));

			$detail = [];
			$operator = [];

			if ($status == 'Schedule Kensa') {
				$schedule = DB::connection('ympimis_2')->SELECT("SELECT
						* 
					FROM
						jig_schedule_processes
						JOIN jig_processes ON jig_processes.jig_id = jig_schedule_processes.jig_id 
					WHERE
						schedule_date = '".$date."' ");

				foreach ($schedule as $key) {
					$detail[$key->jig_id] = DB::connection('ympimis_2')->SELECT("SELECT
						* 
					FROM
						jig_schedule_processes
						JOIN jig_kensa_log_processes ON jig_kensa_log_processes.jig_id = jig_schedule_processes.jig_id 
						AND DATE( finished_at ) = schedule_date
						JOIN jig_processes ON jig_processes.jig_id = jig_schedule_processes.jig_id 
					WHERE
						schedule_date = '".$date."' 
						AND jig_schedule_processes.jig_id = '".$key->jig_id."'");
				}

				$judul = 'DETAIL SCHEDULE WELDING JIG PROCESS TANGGAL '.$dateTitle;
			}elseif ($status == 'Sudah Kensa') {
				$schedule = DB::connection('ympimis_2')->SELECT("SELECT
					* 
				FROM
					jig_schedule_processes
					JOIN jig_processes ON jig_processes.jig_id = jig_schedule_processes.jig_id 
				WHERE
					DATE( kensa_time ) = '".$date."' 
					AND schedule_status = 'Close'");

				foreach ($schedule as $key) {
					$detail[$key->jig_id] = DB::connection('ympimis_2')->SELECT("SELECT
						* 
					FROM
						jig_schedule_processes
						LEFT JOIN jig_kensa_log_processes ON jig_kensa_log_processes.jig_id = jig_schedule_processes.jig_id 
						AND DATE( finished_at ) = DATE( kensa_time )
						JOIN jig_processes ON jig_processes.jig_id = jig_schedule_processes.jig_id 
					WHERE
						DATE( kensa_time ) = '".$date."' 
						AND schedule_status = 'Close' 
						AND jig_schedule_processes.jig_id = '".$key->jig_id."'");

					$emp = EmployeeSync::where('employee_id',$detail[$key->jig_id][0]->kensa_pic)->first();
					$operator[$key->jig_id] = $emp->name;
				}

				$judul = 'DETAIL WELDING JIG PROCESS YANG SUDAH KENSA TANGGAL '.$dateTitle;
			}elseif ($status == 'Belum Repair') {
				$schedule = DB::connection('ympimis_2')->SELECT("SELECT
					* 
				FROM
					jig_schedule_processes
					JOIN jig_processes ON jig_processes.jig_id = jig_schedule_processes.jig_id 
				WHERE
					DATE( kensa_time ) = '".$date."' 
					AND schedule_status = 'Open' 
					AND kensa_time IS NOT NULL 
					AND repair_time IS NULL");

				foreach ($schedule as $key) {
					$detail[$key->jig_id] = DB::connection('ympimis_2')->SELECT("SELECT
						* 
					FROM
						jig_schedule_processes
						JOIN jig_kensa_processes ON jig_kensa_processes.jig_id = jig_schedule_processes.jig_id 
						AND DATE( finished_at ) = DATE( kensa_time )
						JOIN jig_processes ON jig_processes.jig_id = jig_schedule_processes.jig_id 
					WHERE
						DATE( kensa_time ) = '".$date."' 
						AND schedule_status = 'Open' 
						AND jig_schedule_processes.jig_id = '".$key->jig_id."' 
						AND kensa_time IS NOT NULL 
						AND repair_time IS NULL");

					$emp = EmployeeSync::where('employee_id',$detail[$key->jig_id][0]->kensa_pic)->first();
					$operator[$key->jig_id] = $emp->name;
				}

				$judul = 'DETAIL WELDING JIG PROCESS YANG BELUM REPAIR TANGGAL '.$dateTitle;
			}elseif ($status == 'Menunggu Part') {
				$schedule = DB::connection('ympimis_2')->SELECT("SELECT
						* 
					FROM
						jig_schedule_processes
						JOIN jig_processes ON jig_processes.jig_id = jig_schedule_processes.jig_id 
					WHERE
						DATE( repair_time ) = '".$date."' 
						AND schedule_status = 'Open' 
						AND kensa_time IS NOT NULL 
						AND repair_time IS NOT NULL");

				foreach ($schedule as $key) {
					$detail[$key->jig_id] = DB::connection('ympimis_2')->SELECT("SELECT
						* 
						FROM
						jig_schedule_processes
						JOIN jig_kensa_processes ON jig_kensa_processes.jig_id = jig_schedule_processes.jig_id 
						AND DATE( finished_at ) = DATE(repair_time)
						JOIN jig_processes ON jig_processes.jig_id = jig_schedule_processes.jig_id 
						WHERE
						DATE(repair_time) = '".$date."' 
						AND schedule_status = 'Open' 
						AND jig_schedule_processes.jig_id = '".$key->jig_id."' 
						AND kensa_time IS NOT NULL 
						AND repair_time IS NOT NULL");

					$emp = EmployeeSync::where('employee_id',$detail[$key->jig_id][0]->kensa_pic)->first();
					$operator[$key->jig_id] = $emp->name;
				}

				$judul = 'DETAIL WELDING JIG PROCESS YANG MENUNGGU PART TANGGAL '.$dateTitle;
			}

			$response = array(
				'status' => true,
				'message' => 'Success Get Data',
				'detail' => $detail,
				'schedule' => $schedule,
				'judul' => $judul,
				'operator' => $operator,
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

	public function fetchWldDetailJigMonitoringPeriode(Request $request)
	{
		try {
			$date = $request->get('date');
			$dates = explode(' - ', $date);

			$first = $dates[0].'-01';
			$last = date("Y-m-t", strtotime($dates[1]));

			$status = $request->get('status');
			$dateTitle1 = strtoupper(date("d F Y", strtotime($first)));
			$dateTitle2 = strtoupper(date("d F Y", strtotime($last)));

			$detail = [];

			if ($status == 'Schedule Kensa') {
				$schedule = DB::SELECT("select * from jig_schedules join jigs on jigs.jig_id = jig_schedules.jig_id where schedule_date BETWEEN '".$first."' and '".$last."'");

				foreach ($schedule as $key) {
					$detail[$key->jig_id] = DB::SELECT("select * from jig_schedules join jig_kensa_logs on jig_kensa_logs.jig_id = jig_schedules.jig_id and DATE(finished_at) = schedule_date join jigs on jigs.jig_id = jig_schedules.jig_id where schedule_date BETWEEN '".$first."' and '".$last."' and jig_schedules.jig_id = '".$key->jig_id."'");
				}

				$judul = 'DETAIL SCHEDULE WELDING KENSA JIG<br>PERIODE '.$dateTitle1.' - '.$dateTitle2;
			}elseif ($status == 'Sudah Kensa') {
				$schedule = DB::SELECT("select * from jig_schedules join jigs on jigs.jig_id = jig_schedules.jig_id where DATE(kensa_time) BETWEEN '".$first."' and '".$last."' and schedule_status = 'Close'");

				foreach ($schedule as $key) {
					$detail[$key->jig_id] = DB::SELECT("SELECT
						*,
						CONCAT(
						SPLIT_STRING ( empkensa.NAME, ' ', 1 ),
						' ',
						SPLIT_STRING ( empkensa.NAME, ' ', 2 )) AS kensaemp 
						FROM
						jig_schedules
						JOIN jig_kensa_logs ON jig_kensa_logs.jig_id = jig_schedules.jig_id 
						AND DATE( finished_at ) = DATE(kensa_time)
						JOIN jigs ON jigs.jig_id = jig_schedules.jig_id
						LEFT JOIN employee_syncs empkensa ON empkensa.employee_id = jig_schedules.kensa_pic 
						WHERE
						DATE(kensa_time) BETWEEN '".$first."' and '".$last."' 
						AND schedule_status = 'Close' 
						AND jig_schedules.jig_id = '".$key->jig_id."'");
				}

				$judul = 'DETAIL WELDING KENSA JIG YANG SUDAH KENSA<br>PERIODE '.$dateTitle1.' - '.$dateTitle2;
			}elseif ($status == 'Belum Repair') {
				$schedule = DB::SELECT("select * from jig_schedules join jigs on jigs.jig_id = jig_schedules.jig_id where DATE(kensa_time) BETWEEN '".$first."' and '".$last."' and schedule_status = 'Open' and kensa_time is not null and repair_time is null");

				foreach ($schedule as $key) {
					$detail[$key->jig_id] = DB::SELECT("SELECT
						*,
						CONCAT(
						SPLIT_STRING ( empkensa.NAME, ' ', 1 ),
						' ',
						SPLIT_STRING ( empkensa.NAME, ' ', 2 )) AS kensaemp 
						FROM
						jig_schedules
						JOIN jig_kensas ON jig_kensas.jig_id = jig_schedules.jig_id 
						AND DATE( finished_at ) = DATE(kensa_time)
						JOIN jigs ON jigs.jig_id = jig_schedules.jig_id 
						LEFT JOIN employee_syncs empkensa ON empkensa.employee_id = jig_schedules.kensa_pic 
						WHERE
						DATE(kensa_time) BETWEEN '".$first."' and '".$last."' 
						AND schedule_status = 'Open' 
						AND jig_schedules.jig_id = '".$key->jig_id."' 
						AND kensa_time IS NOT NULL 
						AND repair_time IS NULL");
				}

				$judul = 'DETAIL WELDING KENSA JIG YANG BELUM REPAIR<br>PERIODE '.$dateTitle1.' - '.$dateTitle2;
			}elseif ($status == 'Menunggu Part') {
				$schedule = DB::SELECT("select * from jig_schedules join jigs on jigs.jig_id = jig_schedules.jig_id where DATE(repair_time) BETWEEN '".$first."' and '".$last."' and schedule_status = 'Open' and kensa_time is not null and repair_time is not null");

				foreach ($schedule as $key) {
					$detail[$key->jig_id] = DB::SELECT("SELECT
						* ,
						CONCAT(
						SPLIT_STRING ( empkensa.NAME, ' ', 1 ),
						' ',
						SPLIT_STRING ( empkensa.NAME, ' ', 2 )) AS kensaemp 
						FROM
						jig_schedules
						JOIN jig_kensas ON jig_kensas.jig_id = jig_schedules.jig_id 
						AND DATE( finished_at ) = DATE(repair_time)
						JOIN jigs ON jigs.jig_id = jig_schedules.jig_id 
						LEFT JOIN employee_syncs empkensa ON empkensa.employee_id = jig_schedules.kensa_pic 
						WHERE
						DATE(repair_time) BETWEEN '".$first."' and '".$last."' 
						AND schedule_status = 'Open' 
						AND jig_schedules.jig_id = '".$key->jig_id."' 
						AND kensa_time IS NOT NULL 
						AND repair_time IS NOT NULL");
				}

				$judul = 'DETAIL WELDING KENSA JIG YANG MENUNGGU PART<br>PERIODE '.$dateTitle1.' - '.$dateTitle2;
			}

			$response = array(
				'status' => true,
				'message' => 'Success Get Data',
				'detail' => $detail,
				'schedule' => $schedule,
				'judul' => $judul,
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

	public function fetchWldDetailJigMonitoringPeriodeProcess(Request $request)
	{
		try {
			$date = $request->get('date');
			$dates = explode(' - ', $date);

			$first = $dates[0].'-01';
			$last = date("Y-m-t", strtotime($dates[1]));

			$status = $request->get('status');
			$dateTitle1 = strtoupper(date("d F Y", strtotime($first)));
			$dateTitle2 = strtoupper(date("d F Y", strtotime($last)));

			$detail = [];
			$operator = [];

			if ($status == 'Schedule Kensa') {
				$schedule = DB::connection('ympimis_2')->SELECT("SELECT
					* 
				FROM
					jig_schedule_processes
					JOIN jig_processes ON jig_processes.jig_id = jig_schedule_processes.jig_id 
				WHERE
					schedule_date BETWEEN '".$first."' 
					AND '".$last."'");

				foreach ($schedule as $key) {
					$detail[$key->jig_id] = DB::connection('ympimis_2')->SELECT("SELECT
						* 
					FROM
						jig_schedule_processes
						JOIN jig_kensa_log_processes ON jig_kensa_log_processes.jig_id = jig_schedule_processes.jig_id 
						AND DATE( finished_at ) = schedule_date
						JOIN jig_processes ON jig_processes.jig_id = jig_schedule_processes.jig_id 
					WHERE
						schedule_date BETWEEN '".$first."' 
						AND '".$last."' 
						AND jig_schedule_processes.jig_id = '".$key->jig_id."'");
				}

				$judul = 'DETAIL SCHEDULE WELDING JIG PROCESS<br>PERIODE '.$dateTitle1.' - '.$dateTitle2;
			}elseif ($status == 'Sudah Kensa') {
				$schedule = DB::connection('ympimis_2')->SELECT("SELECT
					* 
				FROM
					jig_schedule_processes
					JOIN jig_processes ON jig_processes.jig_id = jig_schedule_processes.jig_id 
				WHERE
					DATE( kensa_time ) BETWEEN '".$first."' 
					AND '".$last."' 
					AND schedule_status = 'Close'");

				foreach ($schedule as $key) {
					$detail[$key->jig_id] = DB::connection('ympimis_2')->SELECT("SELECT
						* 
					FROM
						jig_schedule_processes
						JOIN jig_kensa_log_processes ON jig_kensa_log_processes.jig_id = jig_schedule_processes.jig_id 
						AND DATE( finished_at ) = DATE( kensa_time )
						JOIN jig_processes ON jig_processes.jig_id = jig_schedule_processes.jig_id 
					WHERE
						DATE( kensa_time ) BETWEEN '".$first."' 
						AND '".$last."' 
						AND schedule_status = 'Close' 
						AND jig_schedule_processes.jig_id = '".$key->jig_id."'");

					$emp = EmployeeSync::where('employee_id',$detail[$key->jig_id][0]->kensa_pic)->first();
					$operator[$key->jig_id] = $emp->name;
				}

				$judul = 'DETAIL WELDING JIG PROCESS YANG SUDAH KENSA<br>PERIODE '.$dateTitle1.' - '.$dateTitle2;
			}elseif ($status == 'Belum Repair') {
				$schedule = DB::connection('ympimis_2')->SELECT("SELECT
					* 
				FROM
					jig_schedule_processes
					JOIN jig_processes ON jig_processes.jig_id = jig_schedule_processes.jig_id 
				WHERE
					DATE( kensa_time ) BETWEEN '".$first."' 
					AND '".$last."' 
					AND schedule_status = 'Open' 
					AND kensa_time IS NOT NULL 
					AND repair_time IS NULL");

				foreach ($schedule as $key) {
					$detail[$key->jig_id] = DB::connection('ympimis_2')->SELECT("SELECT
						* 
					FROM
						jig_schedule_processes
						JOIN jig_kensa_processes ON jig_kensa_processes.jig_id = jig_schedule_processes.jig_id 
						AND DATE( finished_at ) = DATE( kensa_time )
						JOIN jig_processes ON jig_processes.jig_id = jig_schedule_processes.jig_id 
					WHERE
						DATE( kensa_time ) BETWEEN '".$first."' 
						AND '".$last."' 
						AND schedule_status = 'Open' 
						AND jig_schedule_processes.jig_id = '".$key->jig_id."' 
						AND kensa_time IS NOT NULL 
						AND repair_time IS NULL");

					$emp = EmployeeSync::where('employee_id',$detail[$key->jig_id][0]->kensa_pic)->first();
					$operator[$key->jig_id] = $emp->name;
				}

				$judul = 'DETAIL WELDING JIG PROCESS YANG BELUM REPAIR<br>PERIODE '.$dateTitle1.' - '.$dateTitle2;
			}elseif ($status == 'Menunggu Part') {
				$schedule = DB::connection('ympimis_2')->SELECT("SELECT
					* 
				FROM
					jig_schedule_processes
					JOIN jig_processes ON jig_processes.jig_id = jig_schedule_processes.jig_id 
				WHERE
					DATE( repair_time ) BETWEEN '".$first."' 
					AND '".$last."' 
					AND schedule_status = 'Open' 
					AND kensa_time IS NOT NULL 
					AND repair_time IS NOT NULL");

				foreach ($schedule as $key) {
					$detail[$key->jig_id] = DB::connection('ympimis_2')->SELECT("SELECT
						* 
					FROM
						jig_schedule_processes
						JOIN jig_kensa_processes ON jig_kensa_processes.jig_id = jig_schedule_processes.jig_id 
						AND DATE( finished_at ) = DATE( repair_time )
						JOIN jig_processes ON jig_processes.jig_id = jig_schedule_processes.jig_id 
					WHERE
						DATE( repair_time ) BETWEEN '".$first."' 
						AND '".$last."' 
						AND schedule_status = 'Open' 
						AND jig_schedule_processes.jig_id = '".$key->jig_id."' 
						AND kensa_time IS NOT NULL 
						AND repair_time IS NOT NULL");

					$emp = EmployeeSync::where('employee_id',$detail[$key->jig_id][0]->kensa_pic)->first();
					$operator[$key->jig_id] = $emp->name;
				}

				$judul = 'DETAIL WELDING JIG PROCESS YANG MENUNGGU PART<br>PERIODE '.$dateTitle1.' - '.$dateTitle2;
			}

			$response = array(
				'status' => true,
				'message' => 'Success Get Data',
				'detail' => $detail,
				'schedule' => $schedule,
				'judul' => $judul,
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

	public function fetchWeldingJigData(Request $request)
	{
		try {

			$jigs = Jig::select('*','jigs.id as id_jig')->join('jig_boms','jig_boms.jig_child','jigs.jig_id')->orderby('jigs.id','asc')->get();
			$response = array(
				'status' => true,
				'message' => 'Success Get Data',
				'jigs' => $jigs
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

	public function fetchWeldingJigDataProcess(Request $request)
	{
		try {

			$jigs = DB::connection('ympimis_2')->table('jig_processes')->select('*','jig_processes.id as id_jig')->join('jig_bom_processes','jig_bom_processes.jig_child','jig_processes.jig_id')->orderby('jig_processes.id','asc')->get();
			$response = array(
				'status' => true,
				'message' => 'Success Get Data',
				'jigs' => $jigs
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

	public function editWeldingJigData(Request $request)
	{
		try {

			$jigs = Jig::select('*','jigs.id as id_jig')->join('jig_boms','jig_boms.jig_child','jigs.jig_id')->orderby('jigs.id','asc')->where('jigs.id',$request->get('id'))->get();

			$response = array(
				'status' => true,
				'message' => 'Success Get Data',
				'jigs' => $jigs
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

	public function editWeldingJigDataProcess(Request $request)
	{
		try {

			$jigs = DB::connection('ympimis_2')->table('jig_processes')->select('*','jig_processes.id as id_jig')->join('jig_bom_processes','jig_bom_processes.jig_child','jig_processes.jig_id')->orderby('jig_processes.id','asc')->where('jig_processes.id',$request->get('id'))->get();

			$response = array(
				'status' => true,
				'message' => 'Success Get Data',
				'jigs' => $jigs
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

	public function inputWeldingJigData(Request $request)
	{
		try {
			$fileData = $request->get('fileData');
			$jig_parent = $request->get('jig_parent');
			$jig_id = $request->get('jig_id');
			$jig_index = $request->get('jig_index');
			$jig_name = $request->get('jig_name');
			$jig_alias = $request->get('jig_alias');
			$category = $request->get('category');
			$jig_tag = $request->get('jig_tag');
			$check_period = $request->get('check_period');
			$type = $request->get('type');
			$usage = $request->get('usage');
			$file = $request->file('fileData');

			$tujuan_upload = 'jig/drawing/'.$jig_parent;

			$filename = $jig_id.'.'.$request->input('extension');
			$file->move($tujuan_upload,$filename);

			$jigs = Jig::firstOrNew(['jig_id' => $jig_id, 'jig_index' => $jig_index]);
			$jigs->jig_id = $jig_id;
			$jigs->jig_index = $jig_index;
			$jigs->jig_name = $jig_name;
			$jigs->jig_alias = $jig_alias;
			$jigs->category = $category;
			$jigs->jig_tag = $jig_tag;
			$jigs->check_period = $check_period;
			$jigs->type = $type;
			$jigs->file_name = $filename;
			$jigs->created_by = Auth::id();

			$jigbom = JigBom::firstOrNew(['jig_parent' => $jig_parent, 'jig_child' => $jig_id]);
			$jigbom->jig_parent = $jig_parent;
			$jigbom->jig_child = $jig_id;
			$jigbom->created_by = Auth::id();
			$jigbom->usage = $usage;

			$jigbom->save();
			$jigs->save();

			$response = array(
				'status' => true,
				'message' => 'Success Input Data'
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

	public function inputWeldingJigDataProcess(Request $request)
	{
		try {
			$fileData = $request->get('fileData');
			$jig_parent = $request->get('jig_parent');
			$jig_id = $request->get('jig_id');
			$jig_index = $request->get('jig_index');
			$jig_name = $request->get('jig_name');
			$jig_alias = $request->get('jig_alias');
			$category = $request->get('category');
			$jig_tag = $request->get('jig_tag');
			$check_period = $request->get('check_period');
			$type = $request->get('type');
			$usage = $request->get('usage');
			$file = $request->file('fileData');

			$tujuan_upload = 'jig/drawing/'.$jig_parent;

			$filename = $jig_id.'.'.$request->input('extension');
			$file->move($tujuan_upload,$filename);

			$cek = DB::connection('ympimis_2')->table('jig_processes')->where('jig_id',$jig_id)->where('jig_index',$jig_index)->first();
			if (count($cek) > 0) {
				$jigs = DB::connection('ympimis_2')->table('jig_processes')->where('jig_id',$jig_id)->where('jig_index',$jig_index)->update([
					'jig_name' => $jig_name,
					'jig_alias' => $jig_alias,
					'category' => $category,
					'jig_tag' => $jig_tag,
					'check_period' => $check_period,
					'type' => $type,
					'file_name' => $filename,
					'updated_at' => date('Y-m-d H:i:s'),
				]);
			}else{
				$jigs = DB::connection('ympimis_2')->table('jig_processes')->insert([
					'jig_id' => $jig_id,
					'jig_index' => $jig_index,
					'jig_name' => $jig_name,
					'jig_alias' => $jig_alias,
					'category' => $category,
					'jig_tag' => $jig_tag,
					'check_period' => $check_period,
					'type' => $type,
					'file_name' => $filename,
					'created_by' => Auth::id(),
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				]);
			}

			$cek_bom =  DB::connection('ympimis_2')->table('jig_bom_processes')->where('jig_child',$jig_id)->where('jig_parent',$jig_parent)->first();;
			if (count($cek_bom) > 0) {
				$jigbom = DB::connection('ympimis_2')->table('jig_bom_processes')->where('jig_child',$jig_id)->where('jig_parent',$jig_parent)->update([
					'jig_parent' => $jig_parent,
					'jig_child' => $jig_id,
					'created_by' => Auth::id(),
					'usage' => $usage,
					'updated_at' => date('Y-m-d H:i:s'),
				]);
			}else{
				$jigbom =  DB::connection('ympimis_2')->table('jig_bom_processes')->insert([
					'jig_parent' => $jig_parent,
					'jig_child' => $jig_id,
					'created_by' => Auth::id(),
					'usage' => $usage,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				]);
			}

			$response = array(
				'status' => true,
				'message' => 'Success Input Data'
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

	public function updateWeldingJigData(Request $request)
	{
		try {
			$fileData = $request->get('fileData');
			$jig_parent = $request->get('jig_parent');
			$jig_id = $request->get('jig_id');
			$id_jig = $request->get('id_jig');
			$jig_index = $request->get('jig_index');
			$jig_name = $request->get('jig_name');
			$jig_alias = $request->get('jig_alias');
			$category = $request->get('category');
			$jig_tag = $request->get('jig_tag');
			$check_period = $request->get('check_period');
			$type = $request->get('type');
			$usage = $request->get('usage');
			$file = $request->file('fileData');
			$file_name = $request->get('file_name');

			$jigs = Jig::find($id_jig);
			$jigs->jig_id = $jig_id;
			$jigs->jig_index = $jig_index;
			$jigs->jig_name = $jig_name;
			$jigs->jig_alias = $jig_alias;
			$jigs->category = $category;
			$jigs->jig_tag = $jig_tag;
			$jigs->check_period = $check_period;
			$jigs->type = $type;

			if ($file_name != null) {
				$tujuan_upload = 'jig/drawing/'.$jig_parent;
				$filename = $jig_id.'.'.$request->input('extension');
				$file->move($tujuan_upload,$filename);

				$jigs->file_name = $filename;
			}          	

			$jigbom = JigBom::firstOrNew(['jig_parent' => $jig_parent, 'jig_child' => $jig_id]);
			$jigbom->jig_parent = $jig_parent;
			$jigbom->jig_child = $jig_id;
			$jigbom->usage = $usage;

			$jigbom->save();
			$jigs->save();

			$response = array(
				'status' => true,
				'message' => 'Success Update Data'
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

	public function updateWeldingJigDataProcess(Request $request)
	{
		try {
			$fileData = $request->get('fileData');
			$jig_parent = $request->get('jig_parent');
			$jig_id = $request->get('jig_id');
			$id_jig = $request->get('id_jig');
			$jig_index = $request->get('jig_index');
			$jig_name = $request->get('jig_name');
			$jig_alias = $request->get('jig_alias');
			$category = $request->get('category');
			$jig_tag = $request->get('jig_tag');
			$check_period = $request->get('check_period');
			$type = $request->get('type');
			$usage = $request->get('usage');
			$file = $request->file('fileData');
			$file_name = $request->get('file_name');

			$jigs = DB::connection('ympimis_2')->table('jig_processes')->where('id',$id_jig)->first();
			$filename = $jigs->file_name;

			if ($file_name != null) {
				$tujuan_upload = 'jig/drawing/'.$jig_parent;
				$filename = $jig_id.'.'.$request->input('extension');
				$file->move($tujuan_upload,$filename);

				// $jigs->file_name = $filename;
			}

			$jigs_update = DB::connection('ympimis_2')->table('jig_processes')->where('id',$id_jig)->update([
				'jig_id' => $jig_id,
				'jig_index' => $jig_index,
				'jig_name' => $jig_name,
				'jig_alias' => $jig_alias,
				'category' => $category,
				'jig_tag' => $jig_tag,
				'check_period' => $check_period,
				'type' => $type,
				'file_name' => $filename,
				'updated_at' => date('Y-m-d H:i:s'),
			]);

			$cek_jig_bom = DB::connection('ympimis_2')->table('jig_bom_processes')->where('jig_child', $jig_id)->where('jig_parent', $jig_parent)->first();
			if (count($cek_jig_bom) > 0) {
				$jigbom = DB::connection('ympimis_2')->table('jig_bom_processes')->where('jig_child', $jig_id)->where('jig_parent', $jig_parent)->update([
					'jig_parent' => $jig_parent,
					'jig_child' => $jig_id,
					'usage' => $usage,
					'updated_at' => date('Y-m-d H:i:s'),
				]);
			}else{
				$jigbom = DB::connection('ympimis_2')->table('jig_bom_processes')->insert([
					'jig_parent' => $jig_parent,
					'jig_child' => $jig_id,
					'usage' => $usage,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				]);
			}

			$response = array(
				'status' => true,
				'message' => 'Success Update Data'
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

	public function deleteWeldingJigData($id,$jig_id,$jig_parent)
	{
		try {

			$jigs = DB::connection('ympimis_2')->table('jig_processes')->where('id',$id)->delete();

			$jigbom = DB::connection('ympimis_2')->table('jig_bom_processes')->where('jig_bom_processes.jig_child',$jig_id)->delete();

			File::delete('jig/drawing/'.$jig_parent.'/'.$jig_id.'.pdf');

			return redirect('index/welding/jig_data');
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function deleteWeldingJigDataProcess($id,$jig_id,$jig_parent)
	{
		try {

			$jigs = Jig::find($id);

			$jigbom = JigBom::where('jig_boms.jig_child',$jig_id)->forceDelete();

			File::delete('jig/drawing/'.$jig_parent.'/'.$jig_id.'.pdf');

			$jigs->forceDelete();

			return redirect('index/welding/jig_data');
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function fetchWeldingJigBom()
	{
		try {
			$jigbom = JigBom::select('*','jig_boms.id as id_jig_bom')->leftJoin('jigs','jigs.jig_id','jig_boms.jig_child')->get();

			$response = array(
				'status' => true,
				'message' => 'Success Get Data',
				'jig_bom' => $jigbom
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

	public function fetchWeldingJigBomProcess()
	{
		try {
			$jigbom = DB::connection('ympimis_2')->table('jig_bom_processes')->select('*','jig_bom_processes.id as id_jig_bom')->leftJoin('jig_processes','jig_processes.jig_id','jig_bom_processes.jig_child')->get();

			$response = array(
				'status' => true,
				'message' => 'Success Get Data',
				'jig_bom' => $jigbom
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

	public function inputWeldingJigBom(Request $request)
	{
		try {
			$jig_parent = $request->get('jig_parent');
			$jig_child = $request->get('jig_child');
			$usage = $request->get('usage');

			$jigs = JigBom::firstOrNew(['jig_parent' => $jig_parent, 'jig_child' => $jig_child]);
			$jigs->jig_parent = $jig_parent;
			$jigs->jig_child = $jig_child;
			$jigs->usage = $usage;
			$jigs->created_by = Auth::id();
			$jigs->save();

			$response = array(
				'status' => true,
				'message' => 'Success Input Data'
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

	public function inputWeldingJigBomProcess(Request $request)
	{
		try {
			$jig_parent = $request->get('jig_parent');
			$jig_child = $request->get('jig_child');
			$usage = $request->get('usage');

			$jigs = DB::connection('ympimis_2')->table('jig_bom_processes')->insert([
				'jig_parent' => $jig_parent,
				'jig_child' => $jig_child,
				'usage' => $usage,
				'created_by' => Auth::id(),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			]);

			$response = array(
				'status' => true,
				'message' => 'Success Input Data'
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

	public function editWeldingJigBom(Request $request)
	{
		try {
			$jigbom = JigBom::where('jig_boms.id',$request->get('id'))->get();

			$response = array(
				'status' => true,
				'message' => 'Success Get Data',
				'jig_bom' => $jigbom
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

	public function updateWeldingJigBom(Request $request)
	{
		try {
			$jig_parent = $request->get('jig_parent');
			$jig_child = $request->get('jig_child');
			$usage = $request->get('usage');
			$id_jig_bom = $request->get('id_jig_bom');

			$jigs = JigBom::find($id_jig_bom);
			$jigs->jig_parent = $jig_parent;
			$jigs->jig_child = $jig_child;
			$jigs->usage = $usage;
			$jigs->save();

			$response = array(
				'status' => true,
				'message' => 'Success Update Data'
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

	public function editWeldingJigBomProcess(Request $request)
	{
		try {
			$jigbom = DB::connection('ympimis_2')->table('jig_bom_processes')->where('jig_bom_processes.id',$request->get('id'))->get();

			$response = array(
				'status' => true,
				'message' => 'Success Get Data',
				'jig_bom' => $jigbom
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

	public function updateWeldingJigBomProcess(Request $request)
	{
		try {
			$jig_parent = $request->get('jig_parent');
			$jig_child = $request->get('jig_child');
			$usage = $request->get('usage');
			$id_jig_bom = $request->get('id_jig_bom');

			$jigs =  DB::connection('ympimis_2')->table('jig_bom_processes')->where('id',$id_jig_bom)->update([
				'jig_parent' => $jig_parent,
				'jig_child' => $jig_child,
				'usage' => $usage,
				'updated_at' => date('Y-m-d H:i:s'),
			]);

			$response = array(
				'status' => true,
				'message' => 'Success Update Data'
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

	public function deleteWeldingJigBom($id)
	{
		try {
			$jigbom = JigBom::find($id);

			$jigbom->forceDelete();

			return redirect('index/welding/jig_bom');
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function deleteWeldingJigBomProcess($id)
	{
		try {
			$jigbom =DB::connection('ympimis_2')->table('jig_bom_processes')->where('id',$id)->delete();
			return redirect('index/welding/jig_bom');
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function fetchWeldingJigSchedule()
	{
		try {
			$jigschedule = JigSchedule::select('*','jig_schedules.id as id_jig_schedule','namekensa.name as kensaname','namerepair.name as repairname')->leftJoin('jigs','jigs.jig_id','jig_schedules.jig_id')->leftJoin('employee_syncs AS namekensa','namekensa.employee_id','jig_schedules.kensa_pic')->leftJoin('employee_syncs as namerepair','namerepair.employee_id','jig_schedules.kensa_pic')->orderBy('schedule_status','desc')->orderBy('schedule_date','asc')->get();

			$response = array(
				'status' => true,
				'message' => 'Success Get Data',
				'jig_schedule' => $jigschedule
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

	public function fetchWeldingJigScheduleProcess()
	{
		try {
			$jigschedule = DB::connection('ympimis_2')->table('jig_schedule_processes')->select('*','jig_schedule_processes.id as id_jig_schedule')->leftJoin('jig_processes','jig_processes.jig_id','jig_schedule_processes.jig_id')->orderBy('schedule_status','desc')->orderBy('schedule_date','asc')->get();

			$opkensa = [];
			$oprepair = [];

			for ($i=0; $i < count($jigschedule); $i++) { 
				if ($jigschedule[$i]->kensa_pic == null) {
					array_push($opkensa, '');
				}else{
					$empkensa = EmployeeSync::where('employee_id',$jigschedule[$i]->kensa_pic)->first();
					array_push($opkensa, $empkensa->name);
				}

				if ($jigschedule[$i]->repair_pic == null) {
					array_push($oprepair, '');
				}else{
					$emprepair = EmployeeSync::where('employee_id',$jigschedule[$i]->repair_pic)->first();
					array_push($oprepair, $emprepair->name);
				}
			}

			$response = array(
				'status' => true,
				'message' => 'Success Get Data',
				'jig_schedule' => $jigschedule,
				'opkensa' => $opkensa,
				'oprepair' => $oprepair,
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

	public function editWeldingJigSchedule(Request $request)
	{
		try {
			$jigschedule = JigSchedule::where('id',$request->get('id'))->orderBy('schedule_status','desc')->orderBy('schedule_date','asc')->first();

			$response = array(
				'status' => true,
				'jig_schedule' => $jigschedule
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

	public function editWeldingJigScheduleProcess(Request $request)
	{
		try {
			$jigschedule = DB::connection('ympimis_2')->table('jig_schedule_processes')->where('id',$request->get('id'))->orderBy('schedule_status','desc')->orderBy('schedule_date','asc')->first();

			$response = array(
				'status' => true,
				'jig_schedule' => $jigschedule
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

	public function updateWeldingJigSchedule(Request $request)
	{
		try {
			$jigschedule = JigSchedule::where('id',$request->get('id'))->first();
			$jigschedule->schedule_date = $request->get('schedule_date');
			$jigschedule->save();

			$response = array(
				'status' => true,
				'message' => 'Update Data Success'
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

	public function deleteWeldingJigSchedule(Request $request)
	{
		try {
			$jigschedule = JigSchedule::where('id',$request->get('id'))->forceDelete();

			$response = array(
				'status' => true,
				'message' => 'Delete Data Success'
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

	public function updateWeldingJigScheduleProcess(Request $request)
	{
		try {
			$jigschedule = DB::connection('ympimis_2')->table('jig_schedule_processes')->where('id',$request->get('id'))->update([
				'schedule_date' => $request->get('schedule_date'),
				'updated_at' => date('Y-m-d H:i:s')
			]);

			$response = array(
				'status' => true,
				'message' => 'Update Data Success'
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

	public function fetchWeldingKensaPoint()
	{
		try {

			$jig_point = JigKensaCheck::orderBy('jig_id','asc')->orderBy('jig_child','asc')->orderBy('check_index','asc')->get();

			$response = array(
				'status' => true,
				'jig_point' => $jig_point,
				'message' => 'Update Data Success'
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

	public function fetchWeldingKensaPointProcess()
	{
		try {

			$jig_point = DB::connection('ympimis_2')->table('jig_kensa_check_processes')->orderBy('jig_id','asc')->orderBy('jig_child','asc')->orderBy('check_index','asc')->get();

			$response = array(
				'status' => true,
				'jig_point' => $jig_point,
				'message' => 'Update Data Success'
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

	public function inputWeldingKensaPoint(Request $request)
	{
		try {
			$jig_parent = $request->get('jig_parent');
			$jig_child = $request->get('jig_child');
			$check_name = $request->get('check_name');
			$lower_limit = $request->get('lower_limit');
			$upper_limit = $request->get('upper_limit');

			$jigalias = Jig::where('jig_id',$jig_child)->first();

			$checkindex = JigKensaCheck::where('jig_id',$jig_parent)->orderBy('check_index','desc')->first();

			$kensapoint = new JigKensaCheck([
				'jig_id' => $jig_parent,
				'jig_child' => $jig_child,
				'jig_alias' => $jigalias->jig_alias,
				'check_index' => $checkindex->check_index+1,
				'check_name' => $check_name,
				'upper_limit' => $upper_limit,
				'lower_limit' => $lower_limit,
				'created_by' => Auth::id(),
			]);
			$kensapoint->save();

			$response = array(
				'status' => true,
				'message' => 'Success Input Data'
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

	public function inputWeldingKensaPointProcess(Request $request)
	{
		try {
			$jig_parent = $request->get('jig_parent');
			$jig_child = $request->get('jig_child');
			$check_name = $request->get('check_name');
			$lower_limit = $request->get('lower_limit');
			$upper_limit = $request->get('upper_limit');

			$jigalias = DB::connection('ympimis_2')->table('jig_processes')->where('jig_id',$jig_child)->first();

			$checkindex = DB::connection('ympimis_2')->table('jig_kensa_check_processes')->where('jig_id',$jig_parent)->orderBy('check_index','desc')->first();

			$kensapoint = DB::connection('ympimis_2')->table('jig_kensa_check_processes')->insert([
				'jig_id' => $jig_parent,
				'jig_child' => $jig_child,
				'jig_alias' => $jigalias->jig_alias,
				'check_index' => $checkindex->check_index+1,
				'check_name' => $check_name,
				'upper_limit' => $upper_limit,
				'lower_limit' => $lower_limit,
				'created_by' => Auth::id(),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			]);

			$response = array(
				'status' => true,
				'message' => 'Success Input Data'
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

	public function editWeldingKensaPoint(Request $request)
	{
		try {
			$jig_point = JigKensaCheck::where('id',$request->get('id'))->first();

			$response = array(
				'status' => true,
				'jig_point' => $jig_point
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

	public function editWeldingKensaPointProcess(Request $request)
	{
		try {
			$jig_point = DB::connection('ympimis_2')->table('jig_kensa_check_processes')->where('id',$request->get('id'))->first();

			$response = array(
				'status' => true,
				'jig_point' => $jig_point
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

	public function updateWeldingKensaPoint(Request $request)
	{
		try {
			$jigpoint = JigKensaCheck::where('id',$request->get('id_jig_point'))->first();
			$jigpoint->jig_id = $request->get('jig_parent');
			if ($jigpoint->jig_child != $request->get('jig_child')) {
				$jigalias = Jig::where('jig_id',$request->get('jig_child'))->first();
				$jigpoint->jig_alias = $jigalias->jig_alias;
			}
			$jigpoint->jig_child = $request->get('jig_child');
			$jigpoint->check_index = $request->get('check_index');
			$jigpoint->check_name = $request->get('check_name');
			$jigpoint->lower_limit = $request->get('lower_limit');
			$jigpoint->upper_limit = $request->get('upper_limit');
			$jigpoint->save();

			$response = array(
				'status' => true,
				'message' => 'Update Data Success'
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

	public function updateWeldingKensaPointProcess(Request $request)
	{
		try {
			$jigpoint = DB::connection('ympimis_2')->table('jig_kensa_check_processes')->where('id',$request->get('id_jig_point'))->first();
			$jig_aliases = '';
			if ($jigpoint->jig_child != $request->get('jig_child')) {
				$jigalias = DB::connection('ympimis_2')->table('jig_processes')->where('jig_id',$request->get('jig_child'))->first();
				$jig_aliases = $jigalias->jig_alias;
			}
			$jigpoint = DB::connection('ympimis_2')->table('jig_kensa_check_processes')->where('id',$request->get('id_jig_point'))->update([
				'jig_id' => $request->get('jig_parent'),
				'jig_alias' => $jig_aliases,
				'jig_child' => $request->get('jig_child'),
				'check_index' => $request->get('check_index'),
				'check_name' => $request->get('check_name'),
				'lower_limit' => $request->get('lower_limit'),
				'upper_limit' => $request->get('upper_limit'),
				'updated_at' => date('Y-m-d H:i:s')
			]);

			$response = array(
				'status' => true,
				'message' => 'Update Data Success'
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

	public function deleteWeldingKensaPoint($id)
	{
		try {
			$jigpoint = JigKensaCheck::find($id);

			$jigpoint->forceDelete();

			return redirect('index/welding/kensa_point');
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function deleteWeldingKensaPointProcess($id)
	{
		try {
			$jigpoint = DB::connection('ympimis_2')->table('jig_kensa_check_processes')->where('id',$id)->delete();

			return redirect('index/welding/kensa_point');
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function fetchWeldingJigPart()
	{
		try {
			$jig_part = JigPartStock::select('jig_part_stocks.*','workshop_job_orders.target_date')->leftjoin('workshop_job_orders','workshop_job_orders.order_no','jig_part_stocks.remark')->get();

			$response = array(
				'status' => true,
				'jig_part' => $jig_part,
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

	public function fetchWeldingJigPartProcess()
	{
		try {
			$jig_part = DB::connection('ympimis_2')->table('jig_part_stock_processes')->select('jig_part_stock_processes.*')->get();

			$wjo_date = [];

			for ($i=0; $i < count($jig_part); $i++) { 
				if ($jig_part[$i]->remark != null) {
					$wjo = WorkshopJobOrder::where('order_no',$jig_part[$i]->remark)->first();
					array_push($wjo_date, $wjo->target_date);
				}else{
					array_push($wjo_date, '');
				}
			}

			$response = array(
				'status' => true,
				'jig_part' => $jig_part,
				'wjo_date' => $wjo_date,
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

	public function inputWeldingJigPartProcess(Request $request)
	{
		try {

			$jig_id = $request->get('jig_id');
			$quantity = $request->get('quantity');
			$min_stock = $request->get('min_stock');
			$min_order = $request->get('min_order');
			$quantity_order = $request->get('quantity_order');
			$material = $request->get('material');

			$jigs = DB::connection('ympimis_2')->table('jig_part_stock_processes')->where('jig_id',$jig_id)->first();

			if (count($jigs) > 0) {
				$jigs = DB::connection('ympimis_2')->table('jig_part_stock_processes')->where('jig_id',$jig_id)->update([
					'jig_id' => $jig_id,
					'quantity' => $quantity,
					'min_stock' => $min_stock,
					'min_order' => $min_order,
					'quantity_order' => $quantity_order,
					'material' => $material,
					'updated_at' => date('Y-m-d H:i:s')
				]);
			}else{
				$jigpart =  DB::connection('ympimis_2')->table('jig_part_stock_processes')->where('jig_id',$jig_id)->insert([
					'jig_id' => $jig_id,
					'quantity' => $quantity,
					'min_stock' => $min_stock,
					'min_order' => $min_order,
					'quantity_order' => $quantity_order,
					'material' => $material,
					'created_by' => Auth::id(),
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				]);
			}

			$response = array(
				'status' => true,
				'message' => 'Input Data Success',
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

	public function inputWeldingJigPart(Request $request)
	{
		try {

			$jig_id = $request->get('jig_id');
			$quantity = $request->get('quantity');
			$min_stock = $request->get('min_stock');
			$min_order = $request->get('min_order');
			$quantity_order = $request->get('quantity_order');
			$material = $request->get('material');

			$jigs = JigPartStock::where('jig_id',$jig_id)->first();

			if (count($jigs) > 0) {
				$jigs->jig_id = $jig_id;
				$jigs->quantity = $quantity;
				$jigs->min_stock = $min_stock;
				$jigs->min_order = $min_order;
				$jigs->quantity_order = $quantity_order;
				$jigs->material = $material;
				$jigs->save();
			}else{
				$jigpart = new JigPartStock([
					'jig_id' => $jig_id,
					'quantity' => $quantity,
					'min_stock' => $min_stock,
					'min_order' => $min_order,
					'quantity_order' => $quantity_order,
					'material' => $material,
					'created_by' => Auth::id(),
				]);
				$jigpart->save();
			}

			$response = array(
				'status' => true,
				'message' => 'Input Data Success',
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

	public function editWeldingJigPart(Request $request)
	{
		try {
			$jig_part = JigPartStock::where('id',$request->get('id'))->first();
			$response = array(
				'status' => true,
				'jig_part' => $jig_part
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

	public function editWeldingJigPartProcess(Request $request)
	{
		try {
			$jig_part = DB::connection('ympimis_2')->table('jig_part_stock_processes')->where('id',$request->get('id'))->first();
			$response = array(
				'status' => true,
				'jig_part' => $jig_part
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

	public function updateWeldingJigPart(Request $request)
	{
		try {
			$jigpart = JigPartStock::where('id',$request->get('id_jig_part'))->first();
			$jigpart->jig_id = $request->get('jig_id');
			$jigpart->quantity = $request->get('quantity');
			$jigpart->min_stock = $request->get('min_stock');
			$jigpart->min_order = $request->get('min_order');
			$jigpart->material = $request->get('material');
			$jigpart->save();

			$response = array(
				'status' => true,
				'message' => 'Update Data Success'
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

	public function updateWeldingJigPartProcess(Request $request)
	{
		try {
			$jigpart = DB::connection('ympimis_2')->table('jig_part_stock_processes')->where('id',$request->get('id_jig_part'))->update([
				'jig_id' => $request->get('jig_id'),
				'quantity' => $request->get('quantity'),
				'min_stock' => $request->get('min_stock'),
				'min_order' => $request->get('min_order'),
				'material' => $request->get('material'),
				'updated_at' => date('Y-m-d H:i:s')
			]);

			$response = array(
				'status' => true,
				'message' => 'Update Data Success'
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

	public function deleteWeldingJigPart($id)
	{
		try {
			$jigpart = JigPartStock::find($id);

			$jigpart->forceDelete();

			return redirect('index/welding/jig_part');
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function deleteWeldingJigPartProcess($id)
	{
		try {
			$jigpart =  DB::connection('ympimis_2')->table('jig_part_stock_processes')->where('id',$id)->delete();

			return redirect('index/welding/jig_part');
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function fetchKensaJigReport(Request $request)
	{
		try {

			// if ($request->get('month') == "") {
			// 	$now = 'DATE(
			// 	DATE_ADD( NOW(), INTERVAL - 6 MONTH ))';
			// }else{
			// 	$now = $request->get('month').'-01';
			// }

			if ($request->get('month') == "") {
				$now = date('Y-m');
			}else{
				$now = $request->get('month');
			}
			$jigs = DB::SELECT("SELECT DISTINCT
				( jig_kensa_logs.jig_id ) AS jig_id,
				started_at,
				finished_at,
				`name` AS operator,
				jigs.jig_name,
				operator_id,
			IF
				( GROUP_CONCAT( result ) LIKE '%NG%', 'NG', 'OK' ) AS result,
				COALESCE ( GROUP_CONCAT( DISTINCT ( `status` )), 'No Repair' ) AS `status`,
				'OK Kensa, No Need Action' AS action 
			FROM
				`jig_kensa_logs`
				JOIN employee_syncs ON operator_id = employee_id
				JOIN ( SELECT DISTINCT ( jigs.jig_id ), jig_name FROM jigs ) jigs ON jigs.jig_id = jig_kensa_logs.jig_id 
			WHERE
				DATE_FORMAT( finished_at, '%Y-%m' ) = '".$now."' 
			GROUP BY
				jig_kensa_logs.jig_id,
				started_at,
				finished_at,
				`name`,
				operator_id,
				jigs.jig_name");

			$response = array(
				'status' => true,
				'jig_report' => $jigs,
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

	public function fetchKensaJigReportProcess(Request $request)
	{
		try {

			// if ($request->get('month') == "") {
			// 	$now = 'DATE(
			// 	DATE_ADD( NOW(), INTERVAL - 6 MONTH ))';
			// }else{
			// 	$now = $request->get('month').'-01';
			// }

			if ($request->get('month') == "") {
				$now = date('Y-m');
			}else{
				$now = $request->get('month');
			}
			$jigs = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
				( jig_kensa_log_processes.jig_id ) AS jig_id,
				started_at,
				finished_at,
				jig_processes.jig_name,
				operator_id,
				image_before,
				image_after,
			IF
				( GROUP_CONCAT( result ) LIKE '%NG%', 'NG', 'OK' ) AS result,
				COALESCE ( GROUP_CONCAT( DISTINCT ( `status` )), 'No Repair' ) AS `status`,
				'OK Kensa, No Need Action' AS action 
			FROM
				`jig_kensa_log_processes`
				JOIN ( SELECT DISTINCT ( jig_processes.jig_id ), jig_name FROM jig_processes ) jig_processes ON jig_processes.jig_id = jig_kensa_log_processes.jig_id 
			WHERE
				DATE_FORMAT( finished_at, '%Y-%m' ) = '".$now."' 
			GROUP BY
				jig_kensa_log_processes.jig_id,
				started_at,
				finished_at,
				operator_id,
				image_before,
				image_after,
				jig_processes.jig_name");

			$operator = [];
			for ($i=0; $i < count($jigs); $i++) { 
				$emp = EmployeeSync::where('employee_id',$jigs[$i]->operator_id)->first();
				array_push($operator, $emp->employee_id.'_'.$emp->name);
			}

			$response = array(
				'status' => true,
				'jig_report' => $jigs,
				'operator' => $operator,
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

	public function fetchDetailKensaJigReport(Request $request)
	{
		try {
			$jig_id = $request->get('jig_id');
			$started_at = $request->get('started_at');
			$finished_at = $request->get('finished_at');

			$detail = DB::SELECT("SELECT
				* 
				FROM
				jig_kensa_logs a
				JOIN employee_syncs ON operator_id = employee_id
				JOIN jigs ON jigs.jig_id = a.jig_id 
				WHERE
				a.jig_id = '".$jig_id."' 
				AND a.started_at = '".$started_at."' 
				AND a.finished_at = '".$finished_at."'");

			$response = array(
				'status' => true,
				'detail' => $detail,
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

	public function fetchDetailKensaJigReportProcess(Request $request)
	{
		try {
			$jig_id = $request->get('jig_id');
			$started_at = $request->get('started_at');
			$finished_at = $request->get('finished_at');

			$detail = DB::connection('ympimis_2')->SELECT("SELECT
				* 
			FROM
				jig_kensa_log_processes
				JOIN jig_processes ON jig_processes.jig_id = jig_kensa_log_processes.jig_id 
			WHERE
				jig_kensa_log_processes.jig_id = '".$jig_id."' 
				AND jig_kensa_log_processes.started_at = '".$started_at."' 
				AND jig_kensa_log_processes.finished_at = '".$finished_at."'");

			$operator = [];
			for ($i=0; $i < count($detail); $i++) { 
				$emp = EmployeeSync::where('employee_id',$detail[$i]->operator_id)->first();
				array_push($operator, $emp->employee_id.'_'.$emp->name);
			}

			$response = array(
				'status' => true,
				'detail' => $detail,
				'operator' => $operator,
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

	public function fetchRepairJigReport(Request $request)
	{
		try {
			$month_from = $request->get('month_from');
	        $month_to = $request->get('month_to');
	        if ($month_from == "") {
	             if ($month_to == "") {
	                  $first = "DATE_FORMAT(NOW(), '%Y-%m' )";
	                  $last = "DATE_FORMAT(NOW(), '%Y-%m' )";
	             }else{
	                  $first = "DATE_FORMAT(NOW(), '%Y-%m' )";
	                  $last = "'".$month_to."'";
	             }
	        }else{
	             if ($month_to == "") {
	                  $first = "'".$month_from."'";
	                  $last = "DATE_FORMAT(NOW(), '%Y-%m' )";
	             }else{
	                  $first = "'".$month_from."'";
	                  $last = "'".$month_to."'";
	             }
	        }

			$jig_no_repair = DB::SELECT("	
				SELECT DISTINCT
				( a.jig_id ) AS jig_id,
				jigs.jig_name,
				started_at,
				finished_at,
				image_before,
				image_after,
				NAME AS operator,
				NOW(),
				IF
				((
				SELECT
				count(
				DISTINCT ( result )) 
				FROM
				jig_kensas 
				WHERE
				jig_kensas.jig_id = a.jig_id 
				AND jig_kensas.finished_at = a.finished_at 
				) > 1,
				'NG',
				'OK' 
				) AS result,
				COALESCE (( SELECT DISTINCT ( STATUS ) FROM jig_kensas WHERE jig_kensas.jig_id = a.jig_id AND jig_kensas.finished_at = a.finished_at ), 'No Repair' ) AS status,
				IF
				((
				SELECT
				COUNT(
				DISTINCT ( action )) 
				FROM
				jig_kensas 
				WHERE
				jig_kensas.jig_id = a.jig_id 
				AND jig_kensas.finished_at = a.finished_at 
				) > 1,
				'Open',
				'OK Kensa, No Action' 
				) AS action 
				FROM
				`jig_kensas` a
				JOIN employee_syncs ON operator_id = employee_id
				JOIN jigs ON jigs.jig_id = a.jig_id
				WHERE
				DATE_FORMAT( finished_at, '%Y-%m' ) BETWEEN ".$first." AND ".$last."");

			$jig_repaired = DB::SELECT("SELECT
				jig_repair_logs.jig_id,
				jigs.jig_name,
				jig_repair_logs.started_at,
				jig_repair_logs.finished_at,
				employee_syncs.employee_id,
				employee_syncs.`name`,
				jig_repair_logs.jig_child,
				jig_repair_logs.check_name,
				jig_repair_logs.lower_limit,
				jig_repair_logs.upper_limit,
				jig_repair_logs.image_before,
				jig_repair_logs.image_after,
				jig_repair_logs.`value`,
				jig_repair_logs.`result`,
				jig_repair_logs.`status` 
			FROM
				`jig_repair_logs`
				JOIN employee_syncs ON operator_id = employee_id
				JOIN jigs ON jigs.jig_id = jig_repair_logs.jig_id 
			WHERE
				result = 'NG' 
				AND DATE_FORMAT( finished_at, '%Y-%m' ) BETWEEN ".$first."
				AND ".$last."");

			$response = array(
				'status' => true,
				'jig_no_repair' => $jig_no_repair,
				'jig_repaired' => $jig_repaired,
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

	public function fetchRepairJigReportProcess(Request $request)
	{
		try {
			$month_from = $request->get('month_from');
	        $month_to = $request->get('month_to');
	        if ($month_from == "") {
	             if ($month_to == "") {
	                  $first = "DATE_FORMAT(NOW(), '%Y-%m' )";
	                  $last = "DATE_FORMAT(NOW(), '%Y-%m' )";
	             }else{
	                  $first = "DATE_FORMAT(NOW(), '%Y-%m' )";
	                  $last = "'".$month_to."'";
	             }
	        }else{
	             if ($month_to == "") {
	                  $first = "'".$month_from."'";
	                  $last = "DATE_FORMAT(NOW(), '%Y-%m' )";
	             }else{
	                  $first = "'".$month_from."'";
	                  $last = "'".$month_to."'";
	             }
	        }

			$jig_no_repair = DB::connection('ympimis_2')->SELECT("	
			SELECT DISTINCT
				( a.jig_id ) AS jig_id,
				jig_processes.jig_name,
				started_at,
				finished_at,
				operator_id,
				NOW(),
			IF
				((
					SELECT
						count(
						DISTINCT ( result )) 
					FROM
						jig_kensa_processes 
					WHERE
						jig_kensa_processes.jig_id = a.jig_id 
						AND jig_kensa_processes.finished_at = a.finished_at 
						) > 1,
					'NG',
					'OK' 
				) AS result,
				COALESCE (( SELECT DISTINCT ( STATUS ) FROM jig_kensa_processes WHERE jig_kensa_processes.jig_id = a.jig_id AND jig_kensa_processes.finished_at = a.finished_at ), 'No Repair' ) AS STATUS,
			IF
				((
					SELECT
						COUNT(
						DISTINCT ( action )) 
					FROM
						jig_kensa_processes 
					WHERE
						jig_kensa_processes.jig_id = a.jig_id 
						AND jig_kensa_processes.finished_at = a.finished_at 
						) > 1,
					'Open',
					'OK Kensa, No Action' 
				) AS action 
			FROM
				`jig_kensa_processes` a
				JOIN jig_processes ON jig_processes.jig_id = a.jig_id 
			WHERE
				DATE_FORMAT( finished_at, '%Y-%m' ) BETWEEN ".$first."
				AND ".$last."");


			$operator = [];
			for ($i=0; $i < count($jig_no_repair); $i++) { 
				$emp = EmployeeSync::where('employee_id',$jig_no_repair[$i]->operator_id)->first();
				array_push($operator, $emp->employee_id.'_'.$emp->name);
			}

			$jig_repaired = DB::connection('ympimis_2')->SELECT("SELECT
					jig_repair_log_processes.jig_id,
					jig_processes.jig_name,
					jig_repair_log_processes.started_at,
					jig_repair_log_processes.finished_at,
					jig_repair_log_processes.jig_child,
					jig_repair_log_processes.check_name,
					jig_repair_log_processes.lower_limit,
					jig_repair_log_processes.upper_limit,
					jig_repair_log_processes.`value`,
					jig_repair_log_processes.`result`,
					jig_repair_log_processes.`status`,
					jig_repair_log_processes.operator_id
				FROM
					`jig_repair_log_processes`
					JOIN jig_processes ON jig_processes.jig_id = jig_repair_log_processes.jig_id 
				WHERE
					result = 'NG' 
					AND DATE_FORMAT( finished_at, '%Y-%m' ) BETWEEN ".$first."
					AND ".$last."");

			$operator2 = [];
			for ($i=0; $i < count($jig_repaired); $i++) { 
				$emp = EmployeeSync::where('employee_id',$jig_repaired[$i]->operator_id)->first();
				array_push($operator2, $emp->employee_id.'_'.$emp->name);
			}

			$response = array(
				'status' => true,
				'jig_no_repair' => $jig_no_repair,
				'jig_repaired' => $jig_repaired,
				'operator' => $operator,
				'operator2' => $operator2,
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

	public function fetchDetailRepairJigReport(Request $request)
	{
		try {
			$stts = $request->get('stts');
			$jig_id = $request->get('jig_id');
			$started_at = $request->get('started_at');
			$finished_at = $request->get('finished_at');

			if ($stts == 'Repaired') {
				$detail = DB::SELECT("SELECT
					* 
					FROM
					jig_repair_logs a
					JOIN employee_syncs ON operator_id = employee_id
					JOIN jigs ON jigs.jig_id = a.jig_id 
					WHERE
					a.jig_id = '".$jig_id."' 
					AND a.started_at = '".$started_at."' 
					AND a.finished_at = '".$finished_at."'");
			}else{
				$detail = DB::SELECT("SELECT
					* 
					FROM
					jig_kensas a
					JOIN employee_syncs ON operator_id = employee_id
					JOIN jigs ON jigs.jig_id = a.jig_id 
					WHERE
					a.jig_id = '".$jig_id."' 
					AND a.started_at = '".$started_at."' 
					AND a.finished_at = '".$finished_at."'");
			}

			$response = array(
				'status' => true,
				'detail' => $detail,
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

	public function fetchDetailRepairJigReportProcess(Request $request)
	{
		try {
			$stts = $request->get('stts');
			$jig_id = $request->get('jig_id');
			$started_at = $request->get('started_at');
			$finished_at = $request->get('finished_at');

			if ($stts == 'Repaired') {
				$detail = DB::connection('ympimis_2')->SELECT("SELECT
					* 
				FROM
					jig_repair_log_processes a
					JOIN jig_processes ON jig_processes.jig_id = a.jig_id 
				WHERE
					a.jig_id = '".$jig_id."' 
					AND a.started_at = '".$started_at."' 
					AND a.finished_at = '".$finished_at."'");
			}else{
				$detail = DB::connection('ympimis_2')->SELECT("SELECT
					* 
					FROM
					jig_kensa_processes a
					JOIN jig_processes ON jig_processes.jig_id = a.jig_id 
					WHERE
					a.jig_id = '".$jig_id."' 
					AND a.started_at = '".$started_at."' 
					AND a.finished_at = '".$finished_at."'");
			}

			$operator = [];
			for ($i=0; $i < count($detail); $i++) { 
				$emp = EmployeeSync::where('employee_id',$detail[$i]->operator_id)->first();
				array_push($operator, $emp->employee_id.'_'.$emp->name);
			}

			$response = array(
				'status' => true,
				'detail' => $detail,
				'operator' => $operator,
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

	//END Kensa Jig

	public function fetchKensaResult(Request $request){

		try {
			$location = $request->get('location');
			$employee_id = $request->get('employee_id');
			$now = date('Y-m-d');
			
			$query1 = "SELECT
				sum( IF ( ympimis.materials.model <> 'A82' AND ympimis.materials.hpl = 'ASKEY', welding_check_logs.quantity, 0 ) ) AS askey,
				sum( IF ( ympimis.materials.model <> 'A82' AND ympimis.materials.hpl = 'TSKEY', welding_check_logs.quantity, 0 ) ) AS tskey,
				sum( IF ( ympimis.materials.model LIKE '%82%', welding_check_logs.quantity, 0 ) ) AS `z`,
				sum( IF ( ympimis.materials.hpl LIKE '%CLKEY%', welding_check_logs.quantity, 0 ) ) AS `cl`,
				sum( IF ( ympimis.materials.hpl LIKE '%FLKEY%', welding_check_logs.quantity, 0 ) ) AS `fl` 
			FROM
				welding_check_logs
				LEFT JOIN ympimis.materials ON ympimis.materials.material_number = welding_check_logs.material_number 
			WHERE
				employee_id = '".$employee_id."' 
				AND date( welding_check_logs.created_at ) = '".$now."' 
				AND welding_check_logs.remark = 'OK' 
				AND location = '".$location."'";

			$oks = db::connection('ympimis_2')->select($query1);

			$query2 = "SELECT
				sum( IF ( ympimis.materials.model <> 'A82' AND ympimis.materials.hpl = 'ASKEY', welding_ng_logs.quantity, 0 ) ) AS askey,
				sum( IF ( ympimis.materials.model <> 'A82' AND ympimis.materials.hpl = 'TSKEY', welding_ng_logs.quantity, 0 ) ) AS tskey,
				sum( IF ( ympimis.materials.model LIKE '%82%', welding_ng_logs.quantity, 0 ) ) AS `z`,
				sum( IF ( ympimis.materials.hpl LIKE '%CLKEY%', welding_ng_logs.quantity, 0 ) ) AS `cl`,
				sum( IF ( ympimis.materials.hpl LIKE '%FLKEY%', welding_ng_logs.quantity, 0 ) ) AS `fl` 
			FROM
				welding_ng_logs
				LEFT JOIN ympimis.materials ON ympimis.materials.material_number = welding_ng_logs.material_number 
			WHERE
				employee_id = '".$employee_id."' 
				AND date( welding_ng_logs.created_at ) = '".$now."' 
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

	public function fetchGroupAchievement(Request $request){
		if ($request->get('tanggal') == "") {
			$tanggal = date('Y-m-d');
		} else {
			$tanggal = date('Y-m-d',strtotime($request->get('tanggal')));
		}

		if (date('H:i:s',strtotime($request->get('time_from'))) == '00:00:00' || $request->get('time_from') == null) {
			if (date('H:i:s',strtotime($request->get('time_to'))) == '00:00:00' || $request->get('time_to') == null) {
				$time_from = date('00:00:01');
				$time_to = date('H:i:s');
			}else if(date('H:i:s',strtotime($request->get('time_to'))) != '00:00:00' || $request->get('time_to') != null){
				$time_from = date('00:00:01');
				$time_to = date('H:i:s',strtotime($request->get('time_to')));
			}
		}else if(date('H:i:s',strtotime($request->get('time_from'))) != '00:00:00' || $request->get('time_from') != null){
			if (date('H:i:s',strtotime($request->get('time_to'))) == '00:00:00' || $request->get('time_to') == null) {
				$time_from = date('H:i:s',strtotime($request->get('time_from')));
				$time_to = date('H:i:s');
			}else if(date('H:i:s',strtotime($request->get('time_to'))) != '00:00:00' || $request->get('time_to') != null){
				$time_from = date('H:i:s',strtotime($request->get('time_from')));
				$time_to = date('H:i:s',strtotime($request->get('time_to')));
			}
		}

		$data = db::select("select ws.ws_name, material.material_number, material.model, material.`key`, COALESCE(bff.jml,0) as bff, COALESCE(wld.jml,0) as wld from
			(select l.material_number, m.hpl, m.model, m.`key` from 
			(select distinct l.material_number from middle_request_logs l
			where l.created_at between '".$tanggal." ".$time_from."' and '".$tanggal." ".$time_to."'
			union
			select distinct w.material_number from welding_logs w
			where w.location = 'hsa-visual-sx'
			and w.created_at between '".$tanggal." ".$time_from."' and '".$tanggal." ".$time_to."') l
			left join materials m on l.material_number = m.material_number) as material
			left join
			(select l.material_number, count(l.material_number) as jml, 'bff' as remark from middle_request_logs l
			where l.created_at between '".$tanggal." ".$time_from."' and '".$tanggal." ".$time_to."'
			group by l.material_number) bff
			on material.material_number = bff.material_number
			left join
			(select w.material_number, count(w.material_number) as jml, 'wld' as remark from welding_logs w
			where w.location = 'hsa-visual-sx'
			and w.created_at between '".$tanggal." ".$time_from."' and '".$tanggal." ".$time_to."'
			group by w.material_number) wld
			on material.material_number = wld.material_number
			left join
			(select ws.ws_id, hsa.hsa_kito_code as material_number, ws.ws_name from soldering_db.m_hsa hsa
			left join soldering_db.m_ws ws on ws.ws_id = hsa.ws_id) ws
			on material.material_number = ws.material_number
			order by ws.ws_id, material.`key`, material.model asc");

		$ws = db::connection('welding')->select("select * from m_ws where ws_name in ('WS 1', 'WS 2', 'WS 3', 'WS 4', 'WS 5', 'WS 13', 'WS 14', 'WS 15', 'WS 16', 'WS 1T', 'WS 2T', 'Burner')");

		// $ws = db::connection('welding')->select("select DISTINCT m_hsa.ws_id, m_ws.ws_name  from m_hsa
		// 	left join m_ws on m_hsa.ws_id = m_ws.ws_id
		// 	order by m_ws.ws_id asc");

		$response = array(
			'status' => true,
			'data' => $data,
			'tanggal' => $tanggal,
			'time_from' => $time_from,
			'time_to' => $time_to,
			'ws' => $ws
		);
		return Response::json($response);
	}

	public function fetchWeldingTrend(Request $request){
		$operators = $request->get('operator');
		$operator = "";

		for($x = 0; $x < count($operators); $x++) {
			$operator = $operator."'".$operators[$x]."'";
			if($x != count($operators)-1){
				$operator = $operator.",";
			}
		}
		$where_op = " and eg.employee_id in (".$operator.") ";


		$condition_week = "date(week_date)";
		$condition_ng = "date(welding_time)";
		$condition_eff = "date(result.tgl)";

		if ($request->get('condition') == "month") {
			$condition_week = "DATE_FORMAT(week_date,'%m-%Y')";
			$condition_ng = "DATE_FORMAT(welding_time,'%m-%Y')";
			$condition_eff = "DATE_FORMAT(result.tgl, '%Y-%m')";
		}

		$op = db::select("select eg.employee_id, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name`, eg.`group` from employee_groups eg
			left join employee_syncs e on e.employee_id = eg.employee_id
			where eg.location = 'soldering' ".$where_op."
			order by eg.`group`, e.`name`");


		$ng = db::select("select series.series, series.employee_id, cek.cek, ng.ng, ROUND((COALESCE(ng.ng,0)/cek.cek)*100,2) as ng_rate from
			(select date.series, eg.employee_id from
			(select ".$condition_week." as series from weekly_calendars
			where week_date >= '".$request->get('datefrom')."'
			and week_date <= '".$request->get('dateto')."'
			group by ".$condition_week.") date
			cross join
			(select employee_id from employee_groups
			where location = 'soldering') eg
			) series
			left join
			(select ".$condition_ng." as series, cek.operator_id, sum(cek.quantity) as cek from welding_check_logs cek
			where date(cek.welding_time) >= '".$request->get('datefrom')."'
			and date(cek.welding_time) <= '".$request->get('dateto')."'
			group by ".$condition_ng.", cek.operator_id) cek
			on series.employee_id = cek.operator_id and series.series = cek.series
			left join
			(select ".$condition_ng." as series, ng.operator_id, sum(ng.quantity) as ng from welding_ng_logs ng
			where date(ng.welding_time) >= '".$request->get('datefrom')."'
			and date(ng.welding_time) <= '".$request->get('dateto')."'
			group by ".$condition_ng.", ng.operator_id) ng
			on series.employee_id = ng.operator_id and series.series = ng.series");
		

		$eff = db::connection('welding')->select("select series.series, series.employee_id, eff.eff from
			(select date.series, eg.employee_id from
			(select ".$condition_week." as series from ympimis.weekly_calendars
			where week_date >= '".$request->get('datefrom')."'
			and week_date <= '".$request->get('dateto')."'
			group by ".$condition_week.") date
			cross join
			(select employee_id from ympimis.employee_groups
			where location = 'soldering') eg) series
			left join
			(select date(result.tgl) as series, result.operator_nik, ROUND(sum(result.std)/sum(result.act)*100, 2) as eff from
			(select wld.tgl, wld.operator_nik, wld.material_number, wld.perolehan_jumlah, wld.act, (std.time * wld.perolehan_jumlah) as std from
			(select date(p.tanggaljam) as tgl, op.operator_nik, hsa.hsa_kito_code, phs.phs_code, if(hsa.hsa_kito_code is null,phs.phs_code,hsa.hsa_kito_code) as material_number, p.perolehan_jumlah, TIMESTAMPDIFF(second,p.perolehan_start_date,perolehan_finish_date) as act from t_perolehan p
			left join m_operator op on op.operator_id = p.operator_id
			left join m_hsa hsa on hsa.hsa_id = p.part_id
			left join m_phs phs on phs.phs_id = p.part_id
			where date(p.tanggaljam) >= '".$request->get('datefrom')."'
			and date(p.tanggaljam) <= '".$request->get('dateto')."'
			and p.flow_id = 1
			and op.operator_nik is not null
			) as wld
			left join ympimis.standard_times std on std.material_number = wld.material_number) as result
			group by ".$condition_eff.", result.operator_nik) eff
			on series.employee_id = eff.operator_nik and series.series = eff.series");

		$response = array(
			'status' => true,
			'op' => $op,
			'ng' => $ng,
			'eff' => $eff,
		);
		return Response::json($response);
	}

	public function fetchGroupAchievementDetail(Request $request){
		
		$bff = db::select("select ws.ws_name, l.material_number, m.model, m.`key`, count(l.material_number) as kanban, sum(l.quantity) as jml from middle_request_logs l
			left join
			(select hsa.hsa_kito_code as material_number, ws.ws_name from soldering_db.m_hsa hsa
			left join soldering_db.m_ws ws on ws.ws_id = hsa.ws_id) ws
			on ws.material_number = l.material_number
			left join materials m on m.material_number = l.material_number
			where DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$request->get('date')."'
			and ws.ws_name = '".$request->get('ws')."'
			group by ws.ws_name, l.material_number, m.model, m.`key`
			order by m.`key`, m.model asc");

		$wld = db::select("select ws.ws_name, l.material_number, m.model, m.`key`, count(l.material_number) as kanban, sum(l.quantity) as jml from welding_logs l
			left join
			(select hsa.hsa_kito_code as material_number, ws.ws_name from soldering_db.m_hsa hsa
			left join soldering_db.m_ws ws on ws.ws_id = hsa.ws_id) ws
			on ws.material_number = l.material_number
			left join materials m on m.material_number = l.material_number
			where l.location = 'hsa-visual-sx'
			and DATE_FORMAT(l.created_at,'%Y-%m-%d') = '".$request->get('date')."'
			and ws.ws_name = '".$request->get('ws')."'
			group by ws.ws_name, l.material_number, m.model, m.`key`
			order by m.`key`, m.model asc");


		$response = array(
			'status' => true,
			'bff' => $bff,
			'wld' => $wld
		);
		return Response::json($response);
	}

	public function fetchAccumulatedAchievement(Request $request){
		if ($request->get('tanggal') == "") {
			$tanggal = date('Y-m-d');
			$tahun = date('Y');
		} else {
			$tanggal = date('Y-m-d',strtotime($request->get('tanggal')));
			$tahun = date('Y',strtotime($request->get('tanggal')));
		}

		$akumulasi = db::select("select w.week_name, acc.tgl, acc.wld, acc.bff from
			(select wld.tgl, COALESCE(wld.jml,0) as wld, COALESCE(bff.jml,0) as bff from
			(select DATE_FORMAT(w.created_at,'%Y-%m-%d') as tgl, count(w.quantity) as jml from welding_logs w
			left join materials m on m.material_number = w.material_number
			where w.location = 'hsa-visual-sx'
			and DATE_FORMAT(w.created_at,'%Y-%m-%d') in
			(select week_date from weekly_calendars
			where week_name = (select week_name from weekly_calendars where week_date = '".$tanggal."')
			and DATE_FORMAT(week_date,'%Y') = '".$tahun."')
			group by tgl) wld
			left join
			(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, count(l.quantity) as jml from middle_request_logs l
			left join materials m on m.material_number = l.material_number
			where DATE_FORMAT(l.created_at,'%Y-%m-%d') in
			(select week_date from weekly_calendars
			where week_name = (select week_name from weekly_calendars where week_date = '".$tanggal."')
			and DATE_FORMAT(week_date,'%Y') = '".$tahun."')
			group by tgl) bff
			on wld.tgl = bff.tgl) acc
			left join weekly_calendars w on w.week_date = acc.tgl
			order by tgl asc");

		$response = array(
			'status' => true,
			'akumulasi' => $akumulasi,
			'tanggal' => $tanggal,
		);
		return Response::json($response);
	}

	public function fetchWeldingQueue(Request $request){
		$ws = "";
		if($request->get('grup') != null){
			$wss = $request->get('grup');
			$ws = "";

			for($x = 0; $x < count($wss); $x++) {
				$ws = $ws."'".$wss[$x]."'";
				if($x != count($wss)-1){
					$ws = $ws.",";
				}
			}
			$ws = "where m_ws.ws_name in (".$ws.") ";
		}

		$queue = db::connection('welding_controller')->select("select queue.proses_id, m_ws.ws_name, queue.material_number, m.material_description, m.surface, queue.antrian_date from
			(SELECT t_proses.proses_id, COALESCE(m_hsa.hsa_kito_code,m_phs.phs_code) as material_number, COALESCE(m_hsa.ws_id,m_phs.ws_id) as ws_id, antrian_date FROM t_proses
			left join m_hsa on m_hsa.hsa_id = t_proses.part_id
			left join m_phs on m_phs.phs_id = t_proses.part_id
			where t_proses.proses_status = 0
			and t_proses.proses_id not in (select order_id_akan from m_mesin)
			) as queue
			left join m_ws on m_ws.ws_id = queue.ws_id
			left join ympimis.materials m on m.material_number = queue.material_number
			".$ws."
			order by m_ws.ws_id, queue.antrian_date asc");

		return DataTables::of($queue)
		->addColumn('check', function($queue){
			return '<input type="checkbox" class="queue" id="'.$queue->proses_id.'#'.$queue->material_description.'" onclick="showSelected(this)">';
		})
		->rawColumns([ 'check' => 'check'])
		->make(true);
	}

	public function fetchWeldingStock(){


		$stores = db::select("select material.material_number, material.description, COALESCE(inventory.qty,0) as qty from
			(select m.material_number, m.description from kitto.materials m
			where m.location = 'SX21'
			and m.category = 'KEY') as material
			left join
			(select i.material_number, count(i.material_number) as qty from kitto.inventories i
			where i.lot > 0 
			group by i.material_number) as inventory
			on material.material_number = inventory.material_number
			order by material.description asc");

		$queues = db::connection('welding_controller')->select("SELECT m_hsa.hsa_kito_code as material_number, count(m_hsa.hsa_kito_code) as qty FROM t_proses
			left join m_hsa on m_hsa.hsa_id = t_proses.part_id
			where t_proses.proses_status = 0
			and t_proses.part_type = 2
			and t_proses.proses_id not in (select order_id_akan from m_mesin)
			group by m_hsa.hsa_kito_code");


		$wips = array();


		$server_18 = db::select("select materials.material_number, COALESCE(inventory.qty,0) as qty from materials
			left join
			(select material_number, count(material_number) as qty from welding_inventories
			where location like '%hsa%'
			group by material_number) inventory
			on materials.material_number = inventory.material_number
			where materials.mrpc = 's21'
			and materials.hpl like '%KEY%'");

		$server_13 = db::connection('welding_controller')->select("select material_number, sum(qty) as qty from
			(select material_number, count(material_number) as qty from
			(select order_id_sedang_gmc as material_number from m_mesin
			where order_id_sedang_gmc is not null
			and order_id_sedang_gmc <> ''
			union
			select order_id_akan_gmc as material_number from m_mesin
			where order_id_akan_gmc is not null
			and order_id_akan_gmc <> '') solder
			group by material_number
			union
			select m_hsa.hsa_kito_code as material_number, count(m_hsa.hsa_kito_code) as qty from t_before_cuci
			left join m_hsa on m_hsa.hsa_id = t_before_cuci.part_id
			where t_before_cuci.part_type = 2
			and t_before_cuci.order_status = 0
			group by m_hsa.hsa_kito_code
			union all
			select m_hsa.hsa_kito_code, count(m_hsa.hsa_kito_code) as qty from t_cuci
			left join m_hsa_kartu on m_hsa_kartu.hsa_kartu_code = t_cuci.kartu_code
			left join m_hsa on m_hsa.hsa_id = m_hsa_kartu.hsa_id
			where m_hsa.hsa_kito_code is not null
			group by m_hsa.hsa_kito_code) as wip
			group by material_number");

		foreach ($server_18 as $data1) {
			$wip_qty = $data1->qty;
			foreach ($server_13 as $data2) {
				if($data1->material_number == $data2->material_number){
					$wip_qty =  $wip_qty + $data2->qty;
				}
			}

			array_push($wips, [
				'material_number' => $data1->material_number,
				'qty' => $wip_qty
			]);
		}

		// $stock = db::connection('welding')->select("select material.material_number, material.material_description, COALESCE(antrian.qty,0) as antrian, COALESCE(wip.qty,0) as wip, COALESCE(store.qty,0) as store from
		// 	(select * from ympimis.materials
		// 	where mrpc = 's21'
		// 	and hpl like '%key%') material
		// 	left join
		// 	(select i.material_number, count(i.material_number) as qty from kitto.inventories i
		// 	left join kitto.materials m on i.material_number = m.material_number
		// 	where i.lot > 0 
		// 	and m.location = 'SX21'
		// 	and m.category = 'KEY'
		// 	group by i.material_number) store
		// 	on material.material_number = store.material_number
		// 	left join
		// 	(select p.hsa_kito_code, count(p.hsa_kito_code) as qty from t_pesanan p
		// 	where p.is_deleted = 0
		// 	group by p.hsa_kito_code) antrian
		// 	on material.material_number = antrian.hsa_kito_code
		// 	left join
		// 	(select hsa.hsa_kito_code as material_number, qty.qty from
		// 	(select part.part_id, count(part.part_id) as qty  from
		// 	(select m.order_id_sedang as order_id, o.part_type, o.part_id from m_mesin m
		// 	left join t_order o on m.order_id_sedang = o.order_id
		// 	where m.order_id_sedang <> ''
		// 	or m.order_id_sedang <> null
		// 	and o.part_type = '2'
		// 	union
		// 	select m.order_id_akan as order_id, o.part_type, o.part_id from m_mesin m
		// 	left join t_order o on m.order_id_akan = o.order_id
		// 	where m.order_id_akan <> ''
		// 	or m.order_id_akan <> null
		// 	and o.part_type = '2') part
		// 	group by part.part_id) qty
		// 	left join m_hsa hsa on hsa.hsa_id = qty.part_id) wip
		// 	on material.material_number = wip.material_number");
		

		// dd($wips);


		$stock = array();
		foreach ($stores as $store) {
			$queue_qty = 0;
			foreach ($queues as $queue) {
				if($store->material_number == $queue->material_number){
					$queue_qty = $queue->qty;
				}

			}

			$wip_qty = 0;
			foreach ($wips as $wip) {
				if($store->material_number == $wip['material_number']){
					$wip_qty = $wip['qty'];
				}
			}

			array_push($stock, [
				'material_number' => $store->material_number,
				'material_description' => $store->description,
				'store' => $store->qty,
				'wip' => $wip_qty,
				'antrian' => $queue_qty
			]);
		}

		return DataTables::of($stock)->make(true);
	}

	public function scanWeldingKensa(Request $request){
		try {
			$location = explode('-', $request->get('location'))[0];
			$loc = explode('-', $request->get('location'))[2];
			$tag = $this->dec2hex($request->get('tag'));

			if (str_contains($request->get('location'),'sx')) {
				$origin_group_code = '043';
			}else if (str_contains($request->get('location'),'fl')) {
				$origin_group_code = '041';
			}else if (str_contains($request->get('location'),'cl')) {
				$origin_group_code = '042';
			}

			if($location == 'phs'){
				$zed_material = db::connection('ympimis_2')->table('welding_tags')
				->where('welding_tags.tag', '=', $tag)
				// ->where('origin_group_code', '=', $origin_group_code)
				->where('location', '=', $loc)
				->first();

				if($zed_material == null){
					$response = array(
						'status' => false,
						'message' => 'Tag material PHS tidak ditemukan',
					);
					return Response::json($response);
				}
				
				$zed_operator = db::connection('ympimis_2')->table('welding_details')
				->select('welding_details.*','welding_operators.name')
				->where('welding_details.tag', $tag)
				// ->where('origin_group_code',$origin_group_code)
				// ->where('welding_details.location', $request->get('location'))
				->leftjoin('welding_operators', 'welding_details.last_check', '=', 'welding_operators.employee_id')
				->first();

				$material = db::table('materials')->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'materials.material_number')
				->where('materials.material_number', '=', $zed_material->material_number)
				->select('materials.model', 'materials.key', 'materials.surface', 'materials.material_number', 'materials.hpl', 'material_volumes.lot_completion')
				->first();
			}
			else if($location == 'hsa'){
				$zed_material = db::connection('ympimis_2')->table('welding_tags')
				->where('welding_tags.tag', '=', $tag)
				// ->where('origin_group_code', '=', $origin_group_code)
				->where('location', '=', $loc)
				->first();

				if($zed_material == null){
					$response = array(
						'status' => false,
						'message' => 'Tag material PHS tidak ditemukan',
					);
					return Response::json($response);
				}

				$zed_operator = db::connection('ympimis_2')->table('welding_details')
				->select('welding_details.*','welding_operators.name')
				->where('welding_details.tag', $tag)
				// ->where('origin_group_code',$origin_group_code)
				// ->where('welding_details.location', $request->get('location'))
				->leftjoin('welding_operators', 'welding_details.last_check', '=', 'welding_operators.employee_id')
				->first();

				$material = db::table('materials')->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'materials.material_number')
				->where('materials.material_number', '=', $zed_material->material_number)
				->select('materials.model', 'materials.key', 'materials.surface', 'materials.material_number', 'materials.hpl', 'material_volumes.lot_completion')
				->first();
			}

			// $delete = db::connection('welding_controller')
			// ->table('t_cuci')
			// ->where('kartu_code', $tag)
			// ->delete();

			$emp = EmployeeSync::where('end_date',null)->get();

			if(($request->get('location') == 'hsa-dimensi-sx') || ($request->get('location') == 'hsa-dimensi-cl') || ($request->get('location') == 'hsa-dimensi-fl')){
				$response = array(
					'status' => true,
					'message' => 'Material ditemukan',
					'material' => $material,
					'emp' => $emp,
					'opwelding' => $zed_operator,
					'started_at' => date('Y-m-d H:i:s'),
					'attention_point' => asset("/welding/attention_point/".$material->model." ".$material->key." ".$material->surface.".jpg"),
					'check_point' => asset("/welding/check_point/".$material->model." ".$material->key." ".$material->surface.".jpg"),
					'check_point_dimensi' => asset("/welding/check_point_dimensi/".$zed_material->material_number.".jpg")
				);
				return Response::json($response);
			}else{
				$response = array(
					'status' => true,
					'message' => 'Material ditemukan',
					'material' => $material,
					'emp' => $emp,
					'opwelding' => $zed_operator,
					'started_at' => date('Y-m-d H:i:s'),
					'attention_point' => asset("/welding/attention_point/".$material->model." ".$material->key." ".$material->surface.".jpg"),
					'check_point' => asset("/welding/check_point/".$material->model." ".$material->key." ".$material->surface.".jpg")
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

	public function inputWeldingRework(Request $request){
		// $welding_rework_log = new WeldingReworkLog([

		try{
			// $welding_rework_log->save();

			$ngs = [];

			if ($request->get('ng')) {
				foreach ($request->get('ng') as $ng) {
				// 	// $welding_ng_log = new WeldingNgLog([
				// 	try{
				// 		$welding_ng_log = db::connection('ympimis_2')->table('welding_ng_logs')->insert([
				// 			'employee_id' => $request->get('employee_id'),
				// 			'tag' => $request->get('tag'),
				// 			'material_number' => $request->get('material_number'),
				// 			'ng_name' => $ng[0],
				// 			'quantity' => $ng[1],
				// 			'location' => $request->get('loc'),
				// 			'operator_id' => $request->get('operator_id'),
				// 			'started_at' => $request->get('started_at'),welding_time
				// 			'welding_time' => $request->get('welding_time'),
				// 			'remark' => $code,
				// 			'created_at' => date('Y-m-d H:i:s'),
				// 			'updated_at' => date('Y-m-d H:i:s'),
				// 		]);
				// 		// $welding_ng_log->save();
				// 	}
				// 	catch(\Exception $e){
				// 		$response = array(
				// 			'status' => false,
				// 			'message' => $e->getMessage(),
				// 		);
				// 		return Response::json($response);
				// 	}
					array_push($ngs, $ng[0].'_'.$ng[1]);
				}
			}

			$welding_rework_log = db::connection('ympimis_2')->table('welding_rework_logs')->insert([
				'employee_id' => $request->get('employee_id'),
				'tag' => $request->get('tag'),
				'material_number' => $request->get('material_number'),
				'quantity' => $request->get('quantity'),
				'welding_time' => $request->get('welding_time'),
				'ng_name' => join(',',$ngs),
				'location' => $request->get('loc'),
				'started_at' => $request->get('started_at'),
				'operator_id' => $request->get('operator_id'),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			]);

			$welding_details = db::connection('ympimis_2')->table('welding_details')->insert([
				'last_check' => $request->get('employee_id'),
				'tag' => $request->get('tag'),
				'material_number' => $request->get('material_number'),
				'quantity' => $request->get('cek'),
				'location' => $request->get('loc'),
				'work_station' => strtoupper($request->get('loc')),
				'remark' => 'Rework',
				'started_at' => date('Y-m-d H:i:s'),
				'finished_at' => date('Y-m-d H:i:s'),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			]);

			// $code_generator = CodeGenerator::where('note','=','welding-kensa')->first();
			// $code = $code_generator->index+1;
			// $code_generator->index = $code;
			// $code_generator->save();

			// if ($request->get('ng')) {
			// 	foreach ($request->get('ng') as $ng) {
			// 		// $welding_ng_log = new WeldingNgLog([
			// 		try{
			// 			$welding_ng_log = db::connection('ympimis_2')->table('welding_ng_logs')->insert([
			// 				'employee_id' => $request->get('employee_id'),
			// 				'tag' => $request->get('tag'),
			// 				'material_number' => $request->get('material_number'),
			// 				'ng_name' => $ng[0],
			// 				'quantity' => $ng[1],
			// 				'location' => $request->get('loc'),
			// 				'operator_id' => $request->get('operator_id'),
			// 				'started_at' => $request->get('started_at'),welding_time
			// 				'welding_time' => $request->get('welding_time'),
			// 				'remark' => $code,
			// 				'created_at' => date('Y-m-d H:i:s'),
			// 				'updated_at' => date('Y-m-d H:i:s'),
			// 			]);
			// 			// $welding_ng_log->save();
			// 		}
			// 		catch(\Exception $e){
			// 			$response = array(
			// 				'status' => false,
			// 				'message' => $e->getMessage(),
			// 			);
			// 			return Response::json($response);
			// 		}
			// 	}
			// }

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

	public function inputWeldingKensa(Request $request){

		$code_generator = CodeGenerator::where('note','=','welding-kensa')->first();
		$code = $code_generator->index+1;
		$code_generator->index = $code;
		$code_generator->save();

		$tag = $this->dec2hex($request->get('tag'));

		if($request->get('ng')){
			foreach ($request->get('ng') as $ng) {
				// $welding_ng_log = new WeldingNgLog([
				try{
					$welding_ng_log = db::connection('ympimis_2')->table('welding_ng_logs')->insert([
						'employee_id' => $request->get('employee_id'),
						'tag' => $request->get('tag'),
						'material_number' => $request->get('material_number'),
						'ng_name' => $ng[0],
						'quantity' => $ng[1],
						'location' => $request->get('loc'),
						'operator_id' => $request->get('operator_id'),
						'started_at' => $request->get('started_at'),
						'welding_time' => $request->get('welding_time'),
						'remark' => $code,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
					]);
					// $welding_ng_log->save();
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

				// $welding_check_log = new WeldingCheckLog([
				$welding_check_log = db::connection('ympimis_2')->table('welding_check_logs')->insert([
					'employee_id' => $request->get('employee_id'),
					'tag' => $request->get('tag'),
					'material_number' => $request->get('material_number'),
					'quantity' => $request->get('cek'),
					'location' => $request->get('loc'),
					'operator_id' => $request->get('operator_id'),
					'welding_time' => $request->get('welding_time'),
					'remark' => 'NG',
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				]);

				$welding_details = db::connection('ympimis_2')->table('welding_details')->insert([
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

				$welding_inventory = db::connection('ympimis_2')->table('welding_inventories')->updateOrInsert(
					['tag' => $tag],
					['material_number' => $request->get('material_number'),
					'location' => $request->get('loc'),
					'quantity' => $request->get('cek'),
					// 'barcode_number' => $request->get('barcode_number'),
					'last_check' => $request->get('employee_id'),
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')]
				);
				// $welding_check_log->save();

				// $welding_temp_log = new WeldingTempLog([
				// $welding_temp_log = db::connection('ympimis_2')->table('welding_temp_logs')->insert([
				// 	'material_number' => $request->get('material_number'),
				// 	'operator_id' => $request->get('operator_id'),
				// 	'quantity' => $request->get('cek'),
				// 	'location' => $request->get('loc'),
				// 	'created_at' => date('Y-m-d H:i:s'),
				// 	'updated_at' => date('Y-m-d H:i:s')
				// ]);
				// $welding_temp_log->save();

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
			// if($request->get('loc') == 'hsa-visual-sx'){
			// 	try{
			// 		$m_hsa_kartu = db::connection('welding')->table('m_hsa_kartu')->where('m_hsa_kartu.hsa_kartu_code', '=', $tag)->first();

			// 		$order_id = db::connection('welding')->table('t_order')->where('part_type', '=', '2')
			// 		->where('part_id', '=', $m_hsa_kartu->hsa_id)
			// 		->where('t_order.kanban_no', '=', $m_hsa_kartu->hsa_kartu_no)
			// 		->first();

			// 		$t_order_detail = db::connection('welding')->table('t_order_detail')
			// 		->where('order_id', '=', $order_id->order_id)
			// 		->where('flow_id', '=', '3')
			// 		->where('order_status', '=', '1')
			// 		->update([
			// 			'order_sedang_start_date' => $request->get('started_at'),
			// 			'order_sedang_finish_date' => date('Y-m-d H:i:s'),
			// 			'order_status' => '6'
			// 		]);

			// 		$t_order = db::connection('welding')->table('t_order')->where('part_type', '=', '2')
			// 		->where('part_id', '=', $m_hsa_kartu->hsa_id)
			// 		->where('t_order.kanban_no', '=', $m_hsa_kartu->hsa_kartu_no)
			// 		->update([
			// 			'order_status' => '5'
			// 		]);
			// 	}
			// 	catch(\Exception $e){

			// 	}
			// }
			try{

				// $welding_log = new WeldingLog([
				// $welding_log = db::connection('ympimis_2')->table('welding_logs')->insert([
				// 	'employee_id' => $request->get('employee_id'),
				// 	'tag' => $tag,
				// 	'material_number' => $request->get('material_number'),
				// 	'quantity' => $request->get('quantity'),
				// 	'location' => $request->get('loc'),
				// 	'operator_id' => $request->get('operator_id'),
				// 	'welding_time' => $request->get('welding_time'),
				// 	'started_at' => $request->get('started_at'),
				// 	'created_at' => date('Y-m-d H:i:s'),
				// 	'updated_at' => date('Y-m-d H:i:s'),
				// 	'welding_time' => date('Y-m-d H:i:s')
				// ]);
				// $welding_log->save();

				// $temp = WeldingTempLog::where('material_number','=',$request->get('material_number'))
				// $temp = db::connection('ympimis_2')->table('welding_temp_logs')->where('material_number','=',$request->get('material_number'))
				// ->where('location','=',$request->get('loc'))
				// ->where('operator_id','=',$request->get('operator_id'))
				// ->first();

				// if(count($temp) > 0){
				// 	// $delete = WeldingTempLog::where('material_number','=',$request->get('material_number'))
				// 	$delete = db::connection('ympimis_2')->table('welding_temp_logs')->where('material_number','=',$request->get('material_number'))
				// 	->where('location','=',$request->get('loc'))
				// 	->where('operator_id','=',$request->get('operator_id'))
				// 	->delete();

				// 	// $delete->delete();
				// }else{
					// $welding_check_log = new WeldingCheckLog([

				$welding_materials = DB::connection('ympimis_2')->table('welding_materials')->where('material_number',$request->get('material_number'))->first();

				$welding_flow = DB::connection('ympimis_2')->table('welding_flows')->where('category',$welding_materials->material_category)->where('material_type',$welding_materials->material_type)->where('flow',$request->get('loc'))->orderby('ordering','asc')->first();
				$next = '';
				if ($welding_flow) {
					$welding_next_flow = DB::connection('ympimis_2')->table('welding_flows')->where('category',$welding_materials->material_category)->where('material_type',$welding_materials->material_type)->where('ordering',$welding_flow->ordering+1)->orderby('ordering','asc')->first();
					if ($welding_next_flow) {
						$next = $welding_next_flow->flow;
					}
				}

				$welding_check_log = db::connection('ympimis_2')->table('welding_check_logs')->insert([
					'employee_id' => $request->get('employee_id'),
					'tag' => $request->get('tag'),
					'material_number' => $request->get('material_number'),
					'quantity' => $request->get('cek'),
					'location' => $request->get('loc'),
					'operator_id' => $request->get('operator_id'),
					'welding_time' => $request->get('welding_time'),
					'remark' => 'OK',
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				]);
					// $welding_check_log->save();
				// }

				// $welding_inventory = WeldingInventory::updateOrCreate(
				$welding_inventory = db::connection('ympimis_2')->table('welding_inventories')->updateOrInsert(
					['tag' => $tag],
					['material_number' => $request->get('material_number'),
					'location' => $request->get('loc'),
					'location_next' => $next,
					'quantity' => $request->get('cek'),
					// 'barcode_number' => $request->get('barcode_number'),
					'last_check' => $request->get('employee_id'),
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')]
				);

				$welding_details = db::connection('ympimis_2')->table('welding_details')->insert([
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

				$welding_queue = db::connection('ympimis_2')->table('welding_queues')->where('material_number',$request->get('material_number'))->where('location',$request->get('loc'))->first();
				if ($welding_queue) {
					$delete = db::connection('ympimis_2')->table('welding_queues')->where('material_number',$request->get('material_number'))->where('location',$request->get('loc'))->orderby('created_at','desc')->delete();
				}

				$insert_queue = db::connection('ympimis_2')->table('welding_queues')->insert([
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

	public function inputWeldingQueue(Request $request){


		$material = $request->get('material');
		$material = explode('-', $material);
		$part_id = $material[0];
		$type = $material[1];
		$qty = $request->get('kanban');
		$date = $request->get('date');
		$time = $request->get('time');


		$part_type = '';
		if($type = "HSA"){
			$part_type = '2';
		}else{
			$part_type = '1';
		}


		try {
			for ($i=1; $i <= $qty; $i++) {
				$unik = base_convert(microtime(false), 8, 36);
				$proses_id = substr(str_replace("-","",$date), 2) . str_replace(":","",$time). $i . $unik;

				$queue = db::connection('welding_controller')
				->table('t_proses')
				->insert([
					'proses_id' => $proses_id,
					'part_type' => $part_type,
					'part_id' => $part_id,
					'antrian_date' => date('Y-m-d H:i:s', strtotime($date.' '.$time.':'.$i)),
					'proses_insert_date' => date('Y-m-d H:i:s', strtotime($date.' '.$time.':'.$i)),
					'proses_sedang_start_date' => date('Y-m-d H:i:s', strtotime('2000-01-01 00:00:00')),
					'proses_sedang_finish_date' => date('Y-m-d H:i:s', strtotime('2000-01-01 00:00:00')),
					'proses_status' => '0',
					'kanban_no' => 0,
					'operator_id' => 0,
					'mesin_id' => 0,
					'pesanan_id' => 0,
					'proses_isupdate' => '0',
					'proses_isdelete' => '0',
					'proses_isupload' => '0',
				]);

			}

			$response = array(
				'status' => true
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

	public function deleteWeldingQueue(Request $request){

		try{

			if($request->get('idx') != null) {
				$where_idx = "";		
				$idxs = $request->get('idx');
				$idx = "";
				for($x = 0; $x < count($idxs); $x++) {
					$idx = $idx."'".$idxs[$x]."'";
					if($x != count($idxs)-1){
						$idx = $idx.",";
					}

					$queue = db::connection('welding_controller')
					->table('t_proses')
					->where('proses_id', $idxs[$x])
					->delete();
				}
			}

			$response = array(
				'status' => true,
				'idx' => $idx,
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

	public function fetchResumeKanban()
	{
		try {
			$weldings = db::connection('welding_controller')->select("
				SELECT
				a.material_number,
				SUM( a.qty_solder ) AS qty_solder,
				SUM( a.qty_cuci ) AS qty_cuci,
				SUM( a.qty_queue ) AS qty_queue 
				FROM
				(
				SELECT
				material_number,
				count( material_number ) AS qty_solder,
				0 AS qty_cuci,
				0 AS qty_queue 
				FROM
				(
				SELECT
				order_id_sedang_gmc AS material_number 
				FROM
				m_mesin 
				WHERE
				order_id_sedang_gmc IS NOT NULL 
				AND order_id_sedang_gmc <> '' UNION ALL
				SELECT
				order_id_akan_gmc AS material_number 
				FROM
				m_mesin 
				WHERE
				order_id_akan_gmc IS NOT NULL 
				AND order_id_akan_gmc <> '' 
				) solder 
				GROUP BY
				material_number UNION ALL
				SELECT
				material_number,
				0 AS qty_solder,
				sum( qty ) AS qty_cuci,
				0 AS qty_queue 
				FROM
				(
				SELECT
				m_hsa.hsa_kito_code AS material_number,
				count( m_hsa.hsa_kito_code ) AS qty 
				FROM
				t_before_cuci
				LEFT JOIN m_hsa ON m_hsa.hsa_id = t_before_cuci.part_id 
				WHERE
				t_before_cuci.part_type = 2 
				AND t_before_cuci.order_status = 0 
				GROUP BY
				m_hsa.hsa_kito_code UNION ALL
				SELECT
				m_hsa.hsa_kito_code AS material_number,
				count( m_hsa.hsa_kito_code ) AS qty 
				FROM
				t_cuci
				LEFT JOIN m_hsa_kartu ON m_hsa_kartu.hsa_kartu_code = t_cuci.kartu_code
				LEFT JOIN m_hsa ON m_hsa.hsa_id = m_hsa_kartu.hsa_id 
				WHERE
				m_hsa.hsa_kito_code IS NOT NULL 
				GROUP BY
				m_hsa.hsa_kito_code 
				) cuci 
				GROUP BY
				material_number UNION ALL
				SELECT COALESCE
				( m_hsa.hsa_kito_code, m_phs.phs_code ) AS material_number,
				0 AS qty_solder,
				0 AS qty_cuci,
				count(
				COALESCE ( m_hsa.hsa_kito_code, m_phs.phs_code )) AS qty_queue 
				FROM
				t_proses
				LEFT JOIN m_hsa ON m_hsa.hsa_id = t_proses.part_id
				LEFT JOIN m_phs ON m_phs.phs_id = t_proses.part_id
				LEFT JOIN m_ws AS ws_phs ON m_phs.ws_id = ws_phs.ws_id
				LEFT JOIN m_ws AS ws_hsa ON m_hsa.ws_id = ws_hsa.ws_id 
				WHERE
				( t_proses.proses_status = 0 AND t_proses.part_type = 1 and t_proses.proses_isdelete = 0 and t_proses.pesanan_id != 0 ) 
				OR ( t_proses.proses_status = 0 AND t_proses.part_type = 2 and t_proses.proses_isdelete = 0 and t_proses.pesanan_id != 0) 
				GROUP BY
				material_number 
				) a
				LEFT JOIN ympimis.materials b ON b.material_number = a.material_number 
				GROUP BY
				a.material_number");

			$kensas = db::select("select material_number, count(material_number) as qty from welding_inventories
				where location like '%hsa%'
				group by material_number");

			$afters = db::connection('mysql2')->select("select * from inventories left join ympimis.material_volumes on ympimis.material_volumes.material_number = inventories.material_number where lot > 0 and issue_location = 'SX21'");

			$materials = db::connection('welding_controller')->select("SELECT * FROM `ympimis`.`materials` WHERE `issue_storage_location` LIKE '%SX21%' AND `hpl` NOT LIKE '%BODY%'");

			$wip_tigas = DB::CONNECTION('welding_controller')->SELECT("SELECT
				a.material_number,
				SUM( a.qty_tiga ) AS qty_tiga 
				FROM
				(
				SELECT
				material_number,
				count( material_number ) AS qty_tiga 
				FROM
				(
				SELECT
				order_id_sedang_gmc AS material_number 
				FROM
				m_mesin 
				WHERE
				order_id_sedang_gmc IS NOT NULL 
				AND order_id_sedang_gmc <> '' 
				AND DATE( order_id_sedang_date ) < DATE_ADD( DATE( NOW()), INTERVAL - 3 DAY ) UNION ALL
				SELECT
				order_id_akan_gmc AS material_number 
				FROM
				m_mesin 
				WHERE
				order_id_akan_gmc IS NOT NULL 
				AND order_id_akan_gmc <> '' 
				AND DATE( order_id_sedang_date ) < DATE_ADD( DATE( NOW()), INTERVAL - 3 DAY ) 
				) solder 
				GROUP BY
				material_number UNION ALL
				SELECT
				material_number,
				sum( qty ) AS qty_tiga 
				FROM
				(
				SELECT
				m_hsa.hsa_kito_code AS material_number,
				count( m_hsa.hsa_kito_code ) AS qty 
				FROM
				t_before_cuci
				LEFT JOIN m_hsa ON m_hsa.hsa_id = t_before_cuci.part_id 
				WHERE
				t_before_cuci.part_type = 2 
				AND t_before_cuci.order_status = 0 
				AND DATE( order_store_date ) < DATE_ADD( DATE( NOW()), INTERVAL - 3 DAY ) 
				GROUP BY
				m_hsa.hsa_kito_code 
				) cuci 
				GROUP BY
				material_number 
				) a
				LEFT JOIN ympimis.materials b ON b.material_number = a.material_number 
				GROUP BY
				a.material_number");


			$log_request = array();
			foreach ($materials as $material) {
				
				$qty_solder = 0;
				$qty_cuci = 0;
				$qty_queue = 0;
				$qty_after = 0;
				$qty_kensa = 0;
				$qty_tiga = 0;

				foreach ($weldings as $welding) {
					if($material->material_number == $welding->material_number){
						$qty_solder = $welding->qty_solder;
						$qty_cuci = $welding->qty_cuci;
						$qty_queue = $welding->qty_queue;
					}
				}

				foreach ($kensas as $kensa) {
					if($material->material_number == $kensa->material_number){
						$qty_kensa = $kensa->qty;
					}
				}

				foreach ($afters as $after) {
					if($material->material_number == $after->material_number){
						$qty_after = $after->lot / $after->lot_completion;
					}
				}

				foreach ($wip_tigas as $wip_tiga) {
					if($material->material_number == $wip_tiga->material_number){
						$qty_tiga = $wip_tiga->qty_tiga;
					}
				}


				array_push($log_request, [
					"material_number" => $material->material_number,
					"material_description" => $material->material_description,
					"model" => $material->model,
					"key" => $material->key,
					"surface" => $material->surface,
					"qty_queue" => $qty_queue,
					"qty_solder" => $qty_solder,
					"qty_cuci" => $qty_cuci,
					"qty_kensa" => $qty_kensa,
					"qty_after" => $qty_after,
					"wip_tiga" => $qty_tiga
				]);
			}

			$response = array(
				'status' => true,
				'datas' => $log_request,
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

	public function updateNgCheck(Request $request){
		$data = $request->get('key');

		try{
			$material = Material::where(db::raw('concat(model," ",`key`)'), '=', $data)
			->where('issue_storage_location', '=', 'SX21')
			->first();

			$ng_log = WeldingNgLog::where('operator_id', '=', $request->get('employee_id'))
			->where('material_number', '=', $material->material_number)
			->where(db::raw('date(welding_time)'), '=', $request->get('date'))
			->orderBy('welding_time', 'desc')
			->first();

			$update = db::table('welding_ng_logs')
			->where('remark', '=', $ng_log->remark)
			->update([
				'check' => Auth::id(),
				'check_time' => date('Y-m-d H:i:s')
			]);

			$response = array(
				'status' => true,
				'message' => 'Check NG Rate successful',
				'material' => $material,
				'ng_log' => $ng_log,
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

	public function updateEffCheck(Request $request){
		$id = $request->get('m_operator');

		try{			
			$perolehan = db::connection('welding_controller')
			->table('t_perolehan')
			->where('operator_id', $id)
			->orderBy('tanggaljam', 'DESC')
			->first();

			$update = db::connection('welding_controller')
			->table('t_perolehan')
			->where('perolehan_id', '=', $perolehan->perolehan_id)
			->update([
				'check' => Auth::id(),
				'check_time' => date('Y-m-d H:i:s')
			]);

			$response = array(
				'status' => true,
				'message' => 'Check Efficiency successful',
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

	public function indexResumeHandatsuke()
	{
		return view('processes.welding.report.hts_resume', array(
			'title' => 'Handatsuke',
			'title_jp' => '',
		))->with('page', 'Handatsuke');
	}

	public function fetchResumeHandatsuke(Request $request)
	{
		try {
			$stamp_detail = DB::SELECT("SELECT
			serial_number,
			model,
			status_material,
			log_processes.created_at,
			users.`name`,
			UPPER( users.`username` ) AS employee_id 
		FROM
			log_processes
			JOIN users ON users.id = log_processes.created_by 
		WHERE
			origin_group_code = '043' 
			AND process_code = '1'
			AND DATE( log_processes.created_at ) >= '".$request->get('datefrom')."' 
			AND DATE( log_processes.created_at ) <= '".$request->get('dateto')."'");
			$response = array(
				'status' => true,
				'stamp_detail' => $stamp_detail,
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

	public function indexMasterMaterial($location)
	{
		$work_station = DB::connection('ympimis_2')->table('welding_materials')->select('work_station')->distinct()->where('locations',$location)->get();
		$material_category = DB::connection('ympimis_2')->table('welding_materials')->select('material_category')->distinct()->where('locations',$location)->get();
		$material_type = DB::connection('ympimis_2')->table('welding_materials')->select('material_type')->distinct()->where('locations',$location)->get();
		$materials = Material::where('issue_storage_location','like','%21%')->get();
		$ws = DB::connection('ympimis_2')->table('weldings')->select('work_station')->distinct()->get();
		return view('processes.welding.master_materials', array(
			'title' => 'Master Material Welding',
			'title_jp' => '',
			'location' => $location,
			'ws' => $ws,
			'materials' => $materials,
			'work_station' => $work_station,
			'material_category' => $material_category,
			'material_type' => $material_type,
			'category' => $this->category,
			'type' => $this->type,
		))->with('page', 'Master Material Welding');
	}

	public function fetchMasterMaterial(Request $request)
	{
		try {
			$material = DB::connection('ympimis_2')->table('welding_materials')->where('locations',$request->get('location'));
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

			$welding_materials = DB::connection('ympimis_2')->table('welding_materials')->where('id',$id)->first();

			$update = DB::connection('ympimis_2')->table('welding_materials')->where('id',$id)->update([
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

			$tags = DB::connection('ympimis_2')->table('welding_tags')->where('material_number',$material)->where('material_category',$welding_materials->material_category)->where('material_type',$welding_materials->material_type)->get();

			if (count($tags) > 0) {
				$update_tags = DB::connection('ympimis_2')->table('welding_tags')->where('material_number',$material)->where('material_category',$welding_materials->material_category)->where('material_type',$welding_materials->material_type)->update([
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

	public function indexWashingWaitingTime()
	{
		return view('processes.welding.report.washing_time', array(
			'title' => 'Welding Washing Waiting Time',
			'title_jp' => '',
		))->with('page', 'Welding Washing Waiting Time');
	}

	public function fetchWashingWaitingTime(Request $request)
	{
		try {
			$date_from = $request->get('date_from');
	        $date_to = $request->get('date_to');
		    if ($date_from == "") {
	            if ($date_to == "") {
	                  $first = date('Y-m-d');
	                  $last = date('Y-m-d');
	            }else{
	                  $first = date('Y-m-d');
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
			$washing = DB::connection('ympimis_2')->select("SELECT
				a.*,
				IF
						(
							LENGTH(
							CONV( a.tag, 16, 10 )) < 10,
							LPAD( CONV( a.tag, 16, 10 ), 10, 0 ),
						CONV( a.tag, 16, 10 )) as tags,
				SEC_TO_TIME(TIMESTAMPDIFF( SECOND, a.phs_hsa, a.finished_at )) AS diff,
				welding_tags.material_type,
				welding_tags.no_kanban,
				welding_tags.material_category,
				welding_materials.material_description,
				welding_materials.material_alias
			FROM
				(
				SELECT
					cuci.id,
					cuci.tag,
					cuci.material_number,
					cuci.location,
					cuci.quantity,
					cuci.last_check,
					cuci.started_at,
					cuci.finished_at,
					cuci.work_station,
					cuci.remark,
					cuci.created_at,
					cuci.updated_at,
					(
					SELECT
						finished_at 
					FROM
						welding_details AS phs_hsa 
					WHERE
						( phs_hsa.location = 'phs-sx' AND phs_hsa.tag = cuci.tag AND phs_hsa.finished_at < cuci.started_at ) 
						OR ( phs_hsa.location = 'hsa-sx' AND phs_hsa.tag = cuci.tag AND phs_hsa.finished_at < cuci.started_at ) 
					ORDER BY
						id DESC 
						LIMIT 1 
					) AS phs_hsa 
				FROM
					welding_details AS cuci 
				WHERE
					DATE( cuci.finished_at ) >= '".$first."' 
					AND DATE( cuci.finished_at ) <= '".$last."' 
					AND cuci.location = 'cuci-asam' UNION ALL
				SELECT
					cuci.id,
					cuci.tag,
					cuci.material_number,
					cuci.location,
					cuci.quantity,
					cuci.last_check,
					cuci.started_at,
					cuci.finished_at,
					cuci.work_station,
					cuci.remark,
					cuci.created_at,
					cuci.updated_at,
					(
					SELECT
						finished_at 
					FROM
						welding_logs AS phs_hsa 
					WHERE
						( phs_hsa.location = 'phs-sx' AND phs_hsa.tag = cuci.tag AND phs_hsa.finished_at < cuci.started_at ) 
						OR ( phs_hsa.location = 'hsa-sx' AND phs_hsa.tag = cuci.tag AND phs_hsa.finished_at < cuci.started_at ) 
					ORDER BY
						id DESC 
						LIMIT 1 
					) AS phs_hsa 
				FROM
					welding_logs AS cuci 
				WHERE
					DATE( cuci.finished_at ) >= '".$first."' 
					AND DATE( cuci.finished_at ) <= '".$last."' 
					AND cuci.location = 'cuci-asam' 
				) a
				JOIN welding_tags ON welding_tags.tag = a.tag
				JOIN welding_materials ON welding_materials.material_number = welding_tags.material_number 
				AND welding_materials.material_category = welding_tags.material_category 
				AND welding_materials.material_type = welding_tags.material_type 
			WHERE
				a.phs_hsa IS NOT NULL 
				AND TIMESTAMPDIFF( MINUTE, a.phs_hsa, a.finished_at ) < 1000
			ORDER BY
				a.finished_at");
			$response = array(
				'status' => true,
				'washing' => $washing,
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

	public function fetchKanbanHistory(Request $request)
	{
		try {
			$data = DB::connection('ympimis_2')->select("SELECT
					a.*,
				IF
					(
						LENGTH(
						CONV( a.tag, 16, 10 )) < 10,
						LPAD( CONV( a.tag, 16, 10 ), 10, 0 ),
					CONV( a.tag, 16, 10 )) AS tags,
					welding_tags.no_kanban,
					COALESCE ( welding_tags.barcode, '' ) AS barcode,
					welding_tags.material_type,
					welding_tags.material_category,
					welding_materials.material_description,
					materials.model,
					materials.`key` 
				FROM
					((
						SELECT
							id,
							tag,
							material_number,
							location,
							quantity,
							last_check,
							work_station,
							started_at,
							finished_at,
							remark,
							deleted_at,
							created_at,
							updated_at,
							'Log' AS types,
							'' AS ng 
						FROM
							`welding_logs` 
						WHERE
						IF
							(
								LENGTH(
								CONV( tag, 16, 10 )) < 10,
								LPAD( CONV( tag, 16, 10 ), 10, 0 ),
							CONV( tag, 16, 10 )) = '".$request->get('tag')."' 
							AND location = 'store-hsa-sx' 
						ORDER BY
							id DESC 
							LIMIT 1 
							) UNION ALL
						(
						SELECT
							id,
							tag,
							material_number,
							location,
							quantity,
							last_check,
							work_station,
							started_at,
							finished_at,
							remark,
							deleted_at,
							created_at,
							updated_at,
							'Log' AS types,
							COALESCE ((
								SELECT
									GROUP_CONCAT( ng_name, '_', quantity ) 
								FROM
									welding_ng_logs 
								WHERE
									tag = '".$request->get('tag')."' 
									AND location = 'hsa-dimensi-sx' 
									AND welding_ng_logs.created_at BETWEEN DATE_ADD( welding_logs.created_at, INTERVAL - 1 HOUR ) 
									AND welding_logs.created_at 
									),
								'' 
							) AS ng 
						FROM
							`welding_logs` 
						WHERE
						IF
							(
								LENGTH(
								CONV( tag, 16, 10 )) < 10,
								LPAD( CONV( tag, 16, 10 ), 10, 0 ),
							CONV( tag, 16, 10 )) = '".$request->get('tag')."' 
							AND location = 'hsa-dimensi-sx' 
						ORDER BY
							id DESC 
							LIMIT 1 
						) UNION ALL
						(
						SELECT
							id,
							tag,
							material_number,
							location,
							quantity,
							last_check,
							work_station,
							started_at,
							finished_at,
							remark,
							deleted_at,
							created_at,
							updated_at,
							'Log' AS types,
							COALESCE ((
								SELECT
									GROUP_CONCAT( ng_name, '_', quantity ) 
								FROM
									welding_ng_logs 
								WHERE
									tag = '".$request->get('tag')."' 
									AND location = 'hsa-visual-sx' 
									AND welding_ng_logs.created_at BETWEEN DATE_ADD( welding_logs.created_at, INTERVAL - 1 HOUR ) 
									AND welding_logs.created_at 
									),
								'' 
							) AS ng 
						FROM
							`welding_logs` 
						WHERE
						IF
							(
								LENGTH(
								CONV( tag, 16, 10 )) < 10,
								LPAD( CONV( tag, 16, 10 ), 10, 0 ),
							CONV( tag, 16, 10 )) = '".$request->get('tag')."' 
							AND location = 'hsa-visual-sx' 
						ORDER BY
							id DESC 
							LIMIT 1 
						) UNION ALL
						(
						SELECT
							id,
							tag,
							material_number,
							location,
							quantity,
							last_check,
							work_station,
							started_at,
							finished_at,
							remark,
							deleted_at,
							created_at,
							updated_at,
							'Log' AS types,
							'' AS ng 
						FROM
							`welding_logs` 
						WHERE
						IF
							(
								LENGTH(
								CONV( tag, 16, 10 )) < 10,
								LPAD( CONV( tag, 16, 10 ), 10, 0 ),
							CONV( tag, 16, 10 )) = '".$request->get('tag')."' 
							AND location = 'cuci-asam' 
						ORDER BY
							id DESC 
							LIMIT 1 
						) UNION ALL
						(
						SELECT
							id,
							tag,
							material_number,
							location,
							quantity,
							last_check,
							work_station,
							started_at,
							finished_at,
							remark,
							deleted_at,
							created_at,
							updated_at,
							'Log' AS types,
							'' AS ng 
						FROM
							`welding_logs` 
						WHERE
							(
							IF
								(
									LENGTH(
									CONV( tag, 16, 10 )) < 10,
									LPAD( CONV( tag, 16, 10 ), 10, 0 ),
								CONV( tag, 16, 10 )) = '".$request->get('tag')."' 
								AND location = 'phs-sx' 
							) 
							OR (
							IF
								(
									LENGTH(
									CONV( tag, 16, 10 )) < 10,
									LPAD( CONV( tag, 16, 10 ), 10, 0 ),
								CONV( tag, 16, 10 )) = '".$request->get('tag')."' 
								AND location = 'hsa-sx' 
							) 
						ORDER BY
							id DESC 
							LIMIT 1 
						)) a
					JOIN welding_tags ON welding_tags.tag = a.tag
					JOIN welding_materials ON welding_materials.material_number = welding_tags.material_number 
					AND welding_materials.material_category = welding_tags.material_category 
					AND welding_materials.material_type = welding_tags.material_type
					JOIN ympimis.materials ON ympimis.materials.material_number = welding_materials.material_number");

			$emp = EmployeeSync::get();
			$response = array(
				'status' => true,
				'data' => $data,
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

	public function indexWeldingProductivity($location)
	{
		if ($location == 'sx') {
			$title = 'Saxophone';
		}else if($location == 'fl'){
			$title = 'Flute';
		}else if($location == 'cl'){
			$title = 'Clarinet';
		}
		return view('processes.welding.display.welding_productivity', array(
			'title' => 'Welding Operator Productivity ~ '.$title,
			'title_jp' => '',
			'location' => $location
		))->with('page', 'Welding Operator Productivity');
	}

	public function fetchWeldingProductivity(Request $request)
	{
		try {
			$now = date('Y-m-d');
			if ($request->get('date') != '') {
				$now = $request->get('date');
			}
			$operator = DB::connection('ympimis_2')->select("SELECT
					* 
				FROM
					welding_operators 
				WHERE
					location = '".$request->get('location')."'");

			$loc_detail = "WHERE
					( DATE( started_at ) = '".$now."' AND welding_details.location LIKE 'hsa-sx' ) 
					OR ( DATE( started_at ) = '".$now."' AND welding_details.location LIKE 'phs-sx' )";

			$loc_log = "WHERE
					( DATE( started_at ) = '".$now."' AND welding_logs.location LIKE 'hsa-sx' ) 
					OR ( DATE( started_at ) = '".$now."' AND welding_logs.location LIKE 'phs-sx' )";

			if ($request->get('location_select') != '') {
				$loc_detail = "WHERE
					( DATE( started_at ) = '".$now."' AND welding_details.location LIKE '".$request->get('location_select')."' ) ";

				$loc_log = "WHERE
					( DATE( started_at ) = '".$now."' AND welding_logs.location LIKE '".$request->get('location_select')."' ) ";
			}

			$productivity = DB::connection('ympimis_2')->select("SELECT
				a.*,
				IF
						(
							LENGTH(
							CONV( a.tag, 16, 10 )) < 10,
							LPAD( CONV( a.tag, 16, 10 ), 10, 0 ),
						CONV( a.tag, 16, 10 )) as tags,
				welding_materials.standard_time,
				welding_materials.material_description,
				welding_materials.quantity,
				ympimis.materials.model,
				ympimis.materials.`key`,
				upper(welding_operators.`name`) as `name`,
				IF(welding_operators.shift = '','A',welding_operators.shift) as shift
			FROM
				(
				SELECT
					welding_details.tag,
					welding_details.material_number,
					welding_details.location,
					started_at,
					finished_at,
					last_check,
					ROUND(TIMESTAMPDIFF( SECOND, started_at, finished_at )/60,2) AS diff 
				FROM
					`welding_details` 
				".$loc_detail." UNION ALL
				SELECT
					welding_logs.tag,
					welding_logs.material_number,
					welding_logs.location,
					started_at,
					finished_at,
					last_check,
					ROUND(TIMESTAMPDIFF( SECOND, started_at, finished_at )/60,2) AS diff 
				FROM
					`welding_logs` 
				".$loc_log.") a
				JOIN welding_tags ON welding_tags.tag = a.tag
				JOIN welding_materials ON welding_materials.material_number = welding_tags.material_number 
				AND welding_tags.material_category = welding_materials.material_category 
				AND welding_tags.material_type = welding_materials.material_type
				JOIN ympimis.materials ON ympimis.materials.material_number = welding_materials.material_number
				LEFT JOIN welding_operators ON welding_operators.employee_id = last_check
				WHERE
				a.last_check != NULL 
				OR a.last_check != 'null'");
			$response = array(
				'status' => true,
				'operator' => $operator,
				'productivity' => $productivity,
				'date' => $now,
				'dateTitle' => date('d M Y',strtotime($now))
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