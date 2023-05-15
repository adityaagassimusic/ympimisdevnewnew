<?php

namespace App\Http\Controllers;

use App\Barrel;
use App\BarrelLog;
use App\BarrelMachine;
use App\BarrelMachineLog;
use App\BarrelQueue;
use App\BarrelQueueInactive;
use App\CodeGenerator;
use App\Employee;
use App\EmployeeSync;
use App\ErrorLog;
use App\Mail\SendEmail;
use App\Material;
use App\MiddleBuffingCheckLog;
use App\MiddleBuffingLog;
use App\MiddleBuffingNgLog;
use App\MiddleInventory;
use App\MiddleLacqueringCheckLog;
use App\MiddleLacqueringLog;
use App\MiddleLacqueringNgLog;
use App\MiddleMaterialRequest;
use App\MiddlePlatingCheckLog;
use App\MiddlePlatingLog;
use App\MiddlePlatingNgLog;
use App\MiddleRequestHelper;
use App\MiddleRequestLog;
use App\MiddleReturnLog;
use App\MiddleReworkLog;
use App\MiddleTarget;
use App\MiddleTempLog;
use App\RfidBuffingDataLog;
use App\RfidLogEfficiency;
use Carbon\Carbon;
use DataTables;
use DateTime;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Response;
use Yajra\DataTables\Exception;

class MiddleProcessController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->location = [
            'bff-kensa-cl',
            'bff-kensa-sx',
            'bff-kensa-sx-body',

            'lcq-incoming',
            'lcq-incoming-acc',
            'lcq-kensa',
            'lcq-kensa-acc',

            'plt-incoming-acc',
            'plt-incoming-cl',
            'plt-incoming-fl',
            'plt-incoming-sx',

            'plt-kensa-acc',
            'plt-kensa-cl',
            'plt-kensa-fl',
            'plt-kensa-sx',

            'subassy-incoming-sx',
        ];
        $this->before_incoming = [
            'new',
            'barrel',
            'bff-kensa-cl',
            'bff-kensa-sx',
            'bff-kensa-sx-body',
        ];
        $this->completions = [
            'lcq-incoming',
            'lcq-incoming-acc',
            'plt-incoming-fl',
            'plt-incoming-cl',
            'plt-incoming-sx',
            'plt-incoming-acc',
        ];
    }

    public function indexBuffingInOut()
    {
        $title = 'In Out Material Store After Buffing';
        $title_jp = '??';

        return view(
            'processes.middle.buffing_in_out',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', 'In Out Material Store After Buffing')->with('head', 'In Out Material Store After Buffing');
    }

    public function indexResumeKonseling()
    {
        $title = 'Middle Resume Operator Counseling';
        $title_jp = '??';

        return view(
            'processes.middle.display.resume_konseling',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', 'Middle Resume Operator Counseling')->with('head', 'Resume Operator Counseling');
    }

    public function indexResumeKanban($location)
    {

        if ($location == 'clarinet_key') {
            $title = 'Resume of Middle Process Kanban';
            $title_jp = '中間工程用かんばんのまとめ';

            return view(
                'processes.middle.resume_kanban',
                array(
                    'title' => $title,
                    'title_jp' => $title_jp,
                    'storage_location' => 'CL51',
                    'origin_group' => 'Clarinet',
                )
            )
                ->with('page', 'Resume of Clarinet Middle Process Kanban')
                ->with('head2', 'Middle Resume Kanban')
                ->with('head', 'Resume Kanban');
        }

        if ($location == 'saxophone_key') {
            $title = 'Resume of Middle Process Kanban';
            $title_jp = '中間工程用かんばんのまとめ';

            return view(
                'processes.middle.resume_kanban',
                array(
                    'title' => $title,
                    'title_jp' => $title_jp,
                    'storage_location' => 'SX51',
                    'origin_group' => 'Saxophone',
                )
            )
                ->with('page', 'Resume of Saxophone Middle Process Kanban')
                ->with('head2', 'Middle Resume Kanban')
                ->with('head', 'Resume Kanban');
        }
    }

    public function indexResumeKanbanBuffing($location)
    {

        if ($location == 'saxophone_key') {
            $title = 'Resume of Buffing Process Kanban';
            $title_jp = 'バフ工程用かんばんのまとめ';

            return view(
                'processes.middle.resume_kanban_buffing',
                array(
                    'title' => $title,
                    'title_jp' => $title_jp,
                    'storage_location' => 'SX51',
                    'origin_group' => 'Saxophone',
                )
            )
                ->with('page', 'Resume of Saxophone Buffing Process Kanban')
                ->with('head2', 'Middle Resume Kanban')
                ->with('head', 'Resume Kanban');
        }

    }

    public function indexOpAnalysis()
    {
        $title = 'Buffing Operator Analysis';
        $title_jp = 'バフ作業者の分析';

        return view(
            'processes.middle.display.op_analysis',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', 'Buffing Operator Analysis');
    }

    public function indexOpAssesment()
    {
        $title = 'Operator Evaluation';
        $title_jp = '作業者の評価';

        return view(
            'processes.middle.display.buffing_op_assesment',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', 'Operator Evaluation');
    }

    public function indexReportBuffingCancelled()
    {
        $title = 'Buffing Cancelled Log';
        $title_jp = 'バフキャンセル記録';

        return view(
            'processes.middle.report.buffing_canceled_log',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', 'buffing-cancel');
    }

    public function indexBuffingOperator($loc)
    {

        $employees = db::select("SELECT * FROM employee_syncs
			WHERE end_date is null
			AND employee_id NOT IN (
			SELECT DISTINCT employee_id FROM employee_groups
			WHERE location = 'bff')");

        return view(
            'processes.middle.buffing_operator',
            array(
                'employees' => $employees,
            )
        )->with('page', 'queue')->with('head', 'Buffing Operator');
    }

    public function indexBuffingTarget($loc)
    {
        if ($loc == 'bff') {
            return view('processes.middle.buffing_target')->with('page', 'queue')->with('head', 'Buffing target');
        }
        if ($loc == 'lcq') {
            return view('processes.middle.lcq_target')->with('page', 'queue')->with('head', 'Lacquering target');
        }
        if ($loc == 'plt') {
            return view('processes.middle.plt_target')->with('page', 'queue')->with('head', 'Plating target');
        }
        if ($loc == 'wld') {
            return view('processes.welding.welding_target')->with('page', 'queue')->with('head', 'Welding target');
        }
        if ($loc == 'assy_fl') {
            return view('processes.assembly.flute.assy_fl_target')->with('page', 'queue')->with('head', 'Assembly Flute Target');
        }
    }

    public function indexReportPltNg($id)
    {
        $fys = db::select("select DISTINCT fiscal_year from weekly_calendars");

        if ($id == 'sax') {
            $title = 'NG Plating Saxophone Report';
            $title_jp = 'サックスメッキ不良リポート';
        } elseif ($id == 'fl') {
            $title = 'NG Plating Flute Report';
            $title_jp = 'フルートメッキ不良リポート';
        } elseif ($id == 'cl') {
            $title = 'NG Plating Clarinet Report';
            $title_jp = 'クラリネットメッキ不良リポート';
        }

        return view(
            'processes.middle.report.ng_plating',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'id' => $id,
                'fys' => $fys,
            )
        )->with('page', 'NG Lacquering');
    }

    public function indexReportPltNgClarinet()
    {

        $title = 'NG Plating Clarinet Report';
        $title_jp = 'クラリネットメッキ不良リポート';

        $fys = db::select("select DISTINCT fiscal_year from weekly_calendars");

        return view(
            'processes.middle.report.ng_plating_clarinet',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'id' => 'cl',
                'fys' => $fys,
            )
        )->with('page', 'NG Plating Clarinet');
    }

    public function indexReportPltNgFlute()
    {

        $title = 'NG Plating Flute Report';
        $title_jp = 'フルートメッキ不良リポート';

        $fys = db::select("select DISTINCT fiscal_year from weekly_calendars");

        return view(
            'processes.middle.report.ng_plating_flute',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'id' => 'fl',
                'fys' => $fys,
            )
        )->with('page', 'NG Plating Flute');
    }

    public function indexBuffingOpRanking()
    {
        $title = 'Resume NG Rate & Productivity';
        $title_jp = '生産性と不良率のまとめ';

        return view(
            'processes.middle.display.buffing_op_ranking',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', 'Resume NG Rate & Productivity');
    }

    public function indexBuffingCanceled()
    {
        return view('processes.middle.buffing_cancel')->with('page', 'Buffing Canceled')->with('head', 'Buffing Canceled');
    }

    public function indexBuffingKanban($sloc)
    {

        $materials = db::table('materials')
            ->where('issue_storage_location', $sloc)
            ->where('hpl', 'LIKE', '%KEY%')
            ->whereIn('mrpc', ['S41', 'L51', 'F51'])
            ->get();

        return view(
            'processes.middle.buffing_kanban',
            array(
                'sloc' => $sloc,
                'materials' => $materials,
            )
        )->with('page', 'Buffing Kanban')->with('head', 'Buffing Kanban');
    }

    public function indexReportOpTime()
    {
        $title = 'Buffing Operator Time';
        $title_jp = '';

        return view(
            'processes.middle.report.operator_time',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', 'Operator Buffing Time');
    }

    public function indexReportTrainingOpNg()
    {
        $title = 'Buffing Training NG Operator';
        $title_jp = '';

        return view(
            'processes.middle.report.training_ng_operator',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', 'Operator Buffing Time');
    }

    public function indexReportTrainingOpEff()
    {
        $title = 'Buffing Training Efficiency Operator';
        $title_jp = '';

        return view(
            'processes.middle.report.training_eff_operator',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', 'Operator Buffing Time');
    }

    public function indexBuffingAdjustment()
    {
        $title = 'Saxophone Buffing Adjustment';
        $title_jp = 'サックスバフかんばん調整';

        $materials = Material::where('mrpc', '=', 's41')
            ->select('material_number', 'hpl', 'model', 'key', 'material_description')
            ->orderBy('key', 'asc')
            ->orderBy('model', 'asc')
            ->get();

        return view(
            'processes.middle.buffing_adjustment',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'materials' => $materials,
            )
        )->with('page', 'buffing-queue')->with('head', 'Middle Process Adjustment');
    }

    public function indexTrendBuffingOpEff()
    {
        $title = 'Daily Buffing Efficiency & NG Rate';
        $title_jp = 'バフ能率と不良率日報';

        $emps = db::select("select eg.employee_id, e.`name` from employee_groups eg
			left join employees e on eg.employee_id = e.employee_id
			where eg.location = 'bff'
			order by e.`name`");

        return view(
            'processes.middle.display.buffing_trend_eff',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'emps' => $emps,
            )
        )->with('page', 'Daily Buffing Efficiency & NG Rate')->with('head', 'Middle Process');
    }

    public function indexBuffingIcAtokotei()
    {
        $title = 'Incoming Check Atokotei';
        $title_jp = '後工程受入検査';

        $origin_groups = DB::table('origin_groups')->orderBy('origin_group_code', 'ASC')->get();

        return view(
            'processes.middle.display.ic_atokotei',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'origin_groups' => $origin_groups,
            )
        )->with('page', 'Incoming Check Atokotei')->with('head', 'Middle Process');
    }

    public function indexIcAtokoteiSubassy()
    {
        $title = 'Incoming Check Atokotei';
        $title_jp = '後工程受入検査';

        $location = $_GET['loc'];

        if ($location == 'plating') {
            return view(
                'processes.middle.display.ic_atokotei_subassy_plt',
                array(
                    'title' => $title,
                    'title_jp' => $title_jp,
                )
            )->with('page', 'Incoming Check Atokotei')->with('head', 'Middle Process');
        } elseif ($location == 'lacquering') {
            return view(
                'processes.middle.display.ic_atokotei_subassy_lcq',
                array(
                    'title' => $title,
                    'title_jp' => $title_jp,
                )
            )->with('page', 'Incoming Check Atokotei')->with('head', 'Middle Process');
        }
    }

    public function indexIcAtokoteiSubassyOp()
    {
        $title = 'NG Incoming Check Atokotei By Operator';
        $title_jp = '不良率後工程受入検査';

        return view(
            'processes.middle.display.ic_atokotei_subassy_op',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', 'Incoming Check Atokotei')->with('head', 'Middle Process');
    }

    public function indexBuffingGroupBalance()
    {
        return view(
            'processes.middle.display.buffing_group_balance',
            array(
                'title' => 'Buffing Group Balance',
                'title_jp' => 'バフグループバランス',
            )
        )->with('page', 'Buffing Group Balance');
    }

    public function indexBuffingGroupAchievement()
    {
        return view(
            'processes.middle.display.buffing_group_achievement',
            array(
                'title' => 'Buffing Group Achievements',
                'title_jp' => 'バフグループ達成度',
            )
        )->with('page', 'Buffing Group Achievement');
    }

    public function indexBuffingOpNg()
    {
        $title = 'NG Rate by Operator';
        $title_jp = '作業者不良率';

        $origin_groups = DB::table('origin_groups')->orderBy('origin_group_code', 'ASC')->get();

        return view(
            'processes.middle.display.buffing_ng_op',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'origin_groups' => $origin_groups,
            )
        )->with('page', 'NG Rate by Operator')->with('head', 'Middle Process');
    }

    public function indexBuffingOpNgRate()
    {
        return view(
            'processes.middle.display.buffing_daily_ng_op',
            array(
                'title' => 'Daily NG Rate by Operator',
                'title_jp' => '作業者日次不良率',
            )
        )->with('page', 'Daily NG Rate by Operator');
    }

    public function indexBuffingOpEff()
    {
        return view(
            'processes.middle.display.buffing_op_eff',
            array(
                'title' => 'Operator Overall Efficiency',
                'title_jp' => '作業者全体能率',
            )
        )->with('page', 'Operator Overall Efficiency');
    }

    public function indexBuffingNgRate()
    {
        return view(
            'processes.middle.display.buffing_daily_ng',
            array(
                'title' => 'Daily Buffing NG Rate',
                'title_jp' => 'バフ日次不良率',
            )
        )->with('page', 'Daily NG Buffing');
    }

    public function indexDisplayMonitoring()
    {
        $locs = db::select("select distinct location from middle_inventories order by location");

        return view(
            'processes.middle.display.monitoring',
            array(
                'title' => 'Middle Process Monitoring',
                'title_jp' => '中間工程監視',
                'locs' => $locs,
            )
        )->with('page', 'Middle Process Monitoring');

    }

    public function indexMizusumashi()
    {
        return view(
            'processes.middle.display.mizusumashi_monitoring',
            array(
                'title' => 'Mizusumashi Monitoring',
                'title_jp' => 'みずすまし監視',
            )
        );
    }

    public function indexRequestDisplay($id)
    {
        return view(
            'processes.middle.display.buffing_request',
            array(
                'title' => 'Middle Request Material',
                'title_jp' => '中間工程ワーク',
                'option' => $id
            )
        )
            ->with('page', 'Middle Request Material Soldering');
    }

    public function indexReportHourlyLcq()
    {
        $locations = $this->location;

        return view(
            'processes.middle.report.hourly_report',
            array(
                'title' => 'Hourly Lacquering Report',
                'title_jp' => 'メッキ毎時記録',
                'locations' => $locations,
            )
        )->with('page', 'Hourly Report');
    }

    public function indexReportBuffingNg()
    {
        $fys = db::select("select DISTINCT fiscal_year from weekly_calendars");

        return view(
            'processes.middle.report.ng_buffing',
            array(
                'title' => 'NG Buffing Report',
                'title_jp' => 'バフ不良報告',
                'fys' => $fys,
            )
        )->with('page', 'NG Buffing');
    }

    public function indexReportBuffingNgAll($id)
    {
        $fys = db::select("select DISTINCT fiscal_year from weekly_calendars");

        if ($id == 'fl') {
            $title = 'NG Buffing Flute Report';
            $title_jp = '';
        } elseif ($id == 'cl') {
            $title = 'NG Buffing Clarinet Report';
            $title_jp = '';
        }

        return view(
            'processes.middle.report.ng_buffing_all',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'id' => $id,
                'fys' => $fys,
            )
        )->with('page', 'NG Buffing');
    }

    public function indexReportLcqNg()
    {
        $fys = db::select("select DISTINCT fiscal_year from weekly_calendars");

        return view(
            'processes.middle.report.ng_lacquering',
            array(
                'title' => 'NG Lacquering Report',
                'title_jp' => '塗装不良率',
                'fys' => $fys,
            )
        )->with('page', 'NG Lacquering');
    }

    public function indexDisplayProductionResult()
    {
        $locations = $this->location;

        return view(
            'processes.middle.display.production_result',
            array(
                'title' => 'Middle Production Result',
                'title_jp' => '中間工程生産実績',
                'locations' => $locations,
            )
        )->with('page', 'Production Result');
    }

    public function indexDisplayKensaTime()
    {
        $locations = $this->location;

        return view(
            'processes.middle.display.kensa_time',
            array(
                'title' => 'Middle Kensa Time',
                'title_jp' => '中間検査時間',
                'locations' => $locations,
            )
        )->with('page', 'Production Result');
    }

    public function indexReportNG()
    {
        $title = 'Not Good Record';
        $title_jp = '不良内容';
        $locations = $this->location;

        return view(
            'processes.middle.report.not_good',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'locations' => $locations,
            )
        )->with('head', 'Middle Process');
    }

    public function indexReportProductionResult()
    {
        $title = 'Production Result Record';
        $title_jp = '生産実績';
        $locations = $this->location;

        return view(
            'processes.middle.report.production_result',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'locations' => $locations,
            )
        )->with('head', 'Middle Process');
    }

    public function indexStockMonitoring()
    {
        $title = 'Middle Process Stock Monitoring';
        $title_jp = '中間工程の在庫監視';
        return view(
            'processes.middle.display.stock_monitoring',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('head', 'Middle Process');
    }

    public function indexBarrelLog()
    {
        $title = 'Barrel Log';
        $title_jp = 'バレルログ';

        $origin_groups = db::table('origin_groups')->get();

        return view(
            'processes.middle.report.barrel_log',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'origin_groups' => $origin_groups,
            )
        )->with('page', 'Middle Process Barrel Machine')->with('head', 'Middle Process');
    }

    public function indexProcessMiddleSX()
    {
        return view('processes.middle.index_sx')->with('page', 'Middle Process SX')->with('head', 'Middle Process');
    }

    public function indexProcessMiddleACC()
    {
        return view('processes.middle.index_acc')->with('page', 'Middle Process ACC')->with('head', 'Middle Process');
    }

    public function indexProcessMiddleCL()
    {
        return view('processes.middle.index_cl')->with('page', 'Middle Process CL');
    }

    public function indexRequest($id)
    {
        return view(
            'processes.middle.request',
            array(
                'title' => 'Middle Request Material ' . $id,
                'title_jp' => '中間要求ワーク',
                'option' => $id
            )
        )->with('page', 'Middle Request Material Soldering');
    }

    public function indexProcessMiddleFL()
    {
        return view('processes.middle.index_fl')->with('page', 'Middle Process FL');
    }

    public function indexProcessBarrelMachine()
    {
        $title = 'Saxophone Barrel Machine';
        $title_jp = 'サックスのバレル機';

        return view(
            'processes.middle.barrel_machine',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', 'Middle Process Barrel Machine')->with('head', 'Middle Process');
    }

    public function indexProcessBarrelBoard($id)
    {

        if ($id == 'barrel-sx') {
            $title = 'Saxophone Barrel Board';
            $title_jp = 'サックスのバレル加工用モニター';
            $mrpc = 'S51';
            $hpl = 'ASKEY,TSKEY';
        }

        return view(
            'processes.middle.barrel_board',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'mrpc' => $mrpc,
                'hpl' => $hpl,
            )
        )->with('page', 'Middle Process Barrel Board')->with('head', 'Middle Process');
    }

    public function indexBuffingNg()
    {
        $title = 'Buffing NG Rate';
        $title_jp = 'バフ不良率';

        $origin_groups = DB::table('origin_groups')->orderBy('origin_group_code', 'ASC')->get();

        return view(
            'processes.middle.display.buffing_ng',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'origin_groups' => $origin_groups,
            )
        )->with('page', 'Middle Process Buffing Performance')->with('head', 'Middle Process');

    }

    public function indexBuffingBoard($id)
    {
        if ($id == 'buffing-sx') {
            $title = 'Saxophone Buffing Board';
            $title_jp = 'サックスバフボード';
            $mrpc = 'S41';
            $hpl = 'ASKEY,TSKEY';

            return view(
                'processes.middle.buffing_board',
                array(
                    'title' => $title,
                    'title_jp' => $title_jp,
                    'mrpc' => $mrpc,
                    'hpl' => $hpl,
                )
            )->with('page', 'Middle Process Buffing Board')->with('head', 'Middle Process');

        } elseif ($id == 'buffing-cl') {
            $title = 'Clarinet Buffing Board';
            $title_jp = '??';

            return view(
                'processes.middle.buffing_board_cl',
                array(
                    'title' => $title,
                    'title_jp' => $title_jp,
                )
            )->with('page', 'Middle Process Buffing Board')->with('head', 'Middle Process');
        }

    }

    public function indexBuffingWorkOrder($id)
    {
        if ($id == 'bff-sx') {
            $title = 'Saxophone Buffing Work Order';
            $title_jp = 'サックスバフ作業順番';
            $mrpc = 'S41';
            $hpl = 'ASKEY,TSKEY';
        }

        return view(
            'processes.middle.buffing_work_order',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'mrpc' => $mrpc,
                'hpl' => $hpl,
            )
        )->with('page', 'Buffing Work Order')->with('head', 'Middle Process');
    }

    public function indexReportMiddle($id)
    {
        if ($id == 'slip-fulfillment') {
            $title = "";
            $title_jp = "";

            return view(
                'processes.middle.report.slip_fulfillment',
                array(
                    'title' => $title,
                    'title_jp' => $title_jp,
                )
            )->with('page', 'Middle Process Barrel Machine')->with('head', 'Middle Process');
        }
    }

    public function indexProcessMiddleBarrel($id)
    {
        if ($id == 'barrel-sx-lcq') {
            $title = 'Saxophone Tumbling-Barrel For Lacquering';
            $mrpc = 'S51';
            $hpl = 'ASKEY,TSKEY';
            $surface = 'LCQ';
            return view(
                'processes.middle.barrel_lcq',
                array(
                    'title' => $title,
                    'mrpc' => $mrpc,
                    'hpl' => $hpl,
                    'surface' => $surface,
                )
            )->with('page', 'Process Middle SX')->with('head', 'Middle Process');
        }

        if ($id == 'barrel-sx-plt') {
            $title = 'Saxophone Tumbling-Barrel For Plating';
            $mrpc = 'S51';
            $hpl = 'ASKEY,TSKEY';
            $surface = 'PLT';
            return view(
                'processes.middle.barrel_plt',
                array(
                    'title' => $title,
                    'mrpc' => $mrpc,
                    'hpl' => $hpl,
                    'surface' => $surface,
                )
            )->with('page', 'Process Middle SX')->with('head', 'Middle Process');
        }

        if ($id == 'barrel-sx-flanel') {
            $title = 'Saxophone Flanel';
            $mrpc = 'S51';
            $hpl = 'ASKEY,TSKEY';
            $surface = 'FLANEL';
            return view(
                'processes.middle.barrel_flanel',
                array(
                    'title' => $title,
                    'mrpc' => $mrpc,
                    'hpl' => $hpl,
                    'surface' => $surface,
                )
            )->with('page', 'Process Middle SX')->with('head', 'Middle Process');
        }
    }

    public function indexProcessMiddleReturn($id)
    {
        if ($id == 'buffing') {
            $title = 'Return Material to Buffing';
            $mrpc = 'S51';
            $hpl = 'ASKEY,TSKEY';
            return view(
                'processes.middle.return',
                array(
                    'title' => $title,
                    'mrpc' => $mrpc,
                    'hpl' => $hpl,
                )
            )->with('page', 'Process Middle SX')->with('head', 'Middle Process');
        }
    }

    public function indexProcessMiddleReturnBody($id)
    {
        if ($id == 'buffing') {
            $title = 'Return Material Body to Buffing';
            return view(
                'processes.middle.return_body',
                array(
                    'title' => $title,
                )
            )->with('page', 'Process Middle SX')->with('head', 'Middle Process');
        }
    }

    public function indexProcessBuffingKensa($id)
    {
        $ng_lists = DB::table('ng_lists')->where('location', '=', $id)->get();

        $title = 'Buffing Kensa';
        $title_jp = 'バフ検査';

        return view(
            'processes.middle.buffing_kensa',
            array(
                'ng_lists' => $ng_lists,
                'loc' => $id,
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', 'Buffing Kensa')->with('head', 'Middle Process');

    }

    public function indexProcessMiddleKensa($id)
    {
        $ng_lists = DB::table('ng_lists')->where('location', '=', $id)->where('remark', '=', 'middle')->get();

        //SAX
        if ($id == 'lcq-incoming') {
            $title = 'I.C. Saxophone Key Lacquering';
            $title_jp = 'サックスキィ塗装の受入検査';
        }
        if ($id == 'lcq-incoming2') {
            $title = 'I.C. Saxophone Key After Treatment Lacquering';
            $title_jp = '塗装処理後サックスキィの受入検査';
        }
        if ($id == 'plt-incoming-sx') {
            $title = 'I.C. Saxophone Key Plating';
            $title_jp = 'サックスキィメッキの受入検査';
        }
        if ($id == 'lcq-kensa') {
            $title = 'Kensa Saxophone Key Lacquering';
            $title_jp = 'サックスキィ塗装検査';
        }
        if ($id == 'plt-kensa-sx') {
            $title = 'Kensa Saxophone Key Plating';
            $title_jp = 'サックスキィメッキ検査';
        }

        //CL
        if ($id == 'plt-incoming-cl') {
            $title = 'I.C. Clarinet Key Plating';
            $title_jp = '';
        }
        if ($id == 'plt-kensa-cl') {
            $title = 'Kensa Clarinet Key Plating';
            $title_jp = '';
        }

        //FL
        if ($id == 'plt-incoming-fl') {
            $title = 'I.C. Flute Key Plating';
            $title_jp = '';
        }
        if ($id == 'plt-kensa-fl') {
            $title = 'Kensa Flute Key Plating';
            $title_jp = '';
        }

        //ACC
        if ($id == 'plt-incoming-acc') {
            $title = 'I.C. Accessories Plating';
            $title_jp = '';
        }
        if ($id == 'plt-kensa-acc') {
            $title = 'Kensa Accessories Plating';
            $title_jp = '';
        }
        if ($id == 'lcq-incoming-acc') {
            $title = 'I.C. Accessories Lacquering';
            $title_jp = '';
        }
        if ($id == 'lcq-kensa-acc') {
            $title = 'Kensa Accessories Lacquering';
            $title_jp = '';
        }

        return view(
            'processes.middle.kensa',
            array(
                'ng_lists' => $ng_lists,
                'loc' => $id,
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', 'Process Middle SX')->with('head', 'Middle Process');
    }

    public function indexBarrelAdjustment()
    {
        $title = 'Saxophone Barrel Adjustment';
        $title_jp = 'サックスバレル調整';
        $mrpc = 'S51';
        $hpl = 'ASKEY,TSKEY';

        return view(
            'processes.middle.barrel_adjustment',
            array(
                'title' => $title,
                'mrpc' => $mrpc,
                'hpl' => $hpl,
            )
        )->with('page', 'barrel-queue')->with('head', 'Middle Process Adjustment');
    }

    public function indexWIPAdjustment()
    {
        $title = 'WIP Adjustment';
        $title_jp = '仕掛品調整';
        $mrpc = 'S51';
        $hpl = 'ASKEY,TSKEY';

        return view(
            'processes.middle.wip_adjustment',
            array(
                'title' => $title,
                'mrpc' => $mrpc,
                'hpl' => $hpl,
            )
        )->with('page', 'wip')->with('head', 'Middle Process Adjustment');
    }

    public function updateNgCheck(Request $request)
    {
        $data = (explode(" ", $request->get('key')));
        $key = $data[1];
        $model = $data[0];

        try {
            $material = Material::where('model', '=', $model)
                ->where('key', '=', $key)
                ->where('mrpc', '=', 's41')
                ->first();

            $ng_log = MiddleBuffingNgLog::where('operator_id', '=', $request->get('employee_id'))
                ->where('material_number', '=', $material->material_number)
                ->where(db::raw('date(buffing_time)'), '=', $request->get('date'))
                ->orderBy('buffing_time', 'desc')
                ->first();

            $update = db::table('middle_buffing_ng_logs')
                ->where('remark', '=', $ng_log->remark)
                ->update([
                    'check' => Auth::id(),
                    'check_time' => date('Y-m-d H:i:s'),
                ]);

            $response = array(
                'status' => true,
                'message' => 'Check NG Rate successful',
                'material' => $material,
                'ng_log' => $ng_log,
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

    public function updateEffCheck(Request $request)
    {
        $data = (explode(" ", $request->get('key')));
        $key = $data[1];
        $model = $data[0];

        try {
            $material = Material::where('model', '=', $model)
                ->where('key', '=', $key)
                ->where('mrpc', '=', 's41')
                ->first();

            $data_log = RfidBuffingDataLog::where('operator_id', '=', $request->get('employee_id'))
                ->where('material_number', '=', $material->material_number)
                ->where(db::raw('date(selesai_start_time)'), '=', $request->get('date'))
                ->orderBy('selesai_start_time', 'desc')
                ->first();

            $update = db::connection('digital_kanban')->table('data_log')
                ->where('idx', '=', $data_log->idx)
                ->update([
                    'check' => Auth::id(),
                    'check_time' => date('Y-m-d H:i:s'),
                ]);

            $response = array(
                'status' => true,
                'message' => 'Check Efficiency successful',
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

    public function addBuffingQueue(Request $request)
    {
        $rack = $request->get('rack');
        $material = explode('-', $request->get('material'));
        $kanban = $request->get('kanban');
        $date = $request->get('date');
        $time = $request->get('time');

        $material_number = $material[0];
        $model = $material[1];

        $qty = 0;
        if ($model != 'A82Z') {
            if ($model[0] == 'A') {
                $qty = 15;
            } else if ($model[0] == 'T') {
                $qty = 8;
            }
        } else {
            $qty = 10;
        }

        try {
            for ($i = 0; $i < $kanban; $i++) {
                $queue = db::connection('digital_kanban')->table('buffing_queues')->insert(
                    array(
                        'rack' => $rack,
                        'material_num' => $material_number,
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d H:i:s', strtotime($date . ' ' . $time)),
                        'updated_at' => date('Y-m-d H:i:s', strtotime($date . ' ' . $time)),
                        'material_qty' => $qty,
                    )
                );
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

    public function deleteBuffingQueue(Request $request)
    {
        try {

            if ($request->get('idx') != null) {
                $where_idx = "";
                $idxs = $request->get('idx');
                $idx = "";
                for ($x = 0; $x < count($idxs); $x++) {
                    $idx = $idx . "'" . $idxs[$x] . "'";
                    if ($x != count($idxs) - 1) {
                        $idx = $idx . ",";
                    }
                }
                $where_idx = "where idx in (" . $idx . ") ";

                $delete = db::connection('digital_kanban')->delete("DELETE FROM buffing_queues " . $where_idx);

            }

            $response = array(
                'status' => true,
                'idx' => $idx,
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

    public function deleteBuffingCanceled(Request $request)
    {

        $model = $request->get('model');
        $key = $request->get('key');

        $rack = '';
        if ($model != 'A82Z') {
            $rack = 'SXKEY-' . $key[0];
        } else {
            $rack = 'SXKEY-82';
        }

        try {
            $data = db::connection('digital_kanban')->select("SELECT * FROM data_log where idx = " . $request->get('idx'));

            $date = db::connection('digital_kanban')->select("select * from buffing_queues order by created_at asc limit 1");

            $queue = db::connection('digital_kanban')->table('buffing_queues')->insert(
                array(
                    'rack' => $rack,
                    'material_num' => $data[0]->material_number,
                    'created_by' => Auth::id(),
                    'created_at' => date('Y-m-d H:i:s', strtotime($date[0]->created_at)),
                    'updated_at' => date('Y-m-d H:i:s', strtotime($date[0]->created_at)),
                    'material_qty' => $data[0]->material_qty,
                )
            );

            $delete = db::connection('digital_kanban')->delete("DELETE FROM data_log where idx = " . $request->get('idx'));

            $middle_return_log = new MiddleReturnLog([
                'tag' => $request->get('tag'),
                'employee_id' => Auth::id(),
                'material_number' => $data[0]->material_number,
                'quantity' => $request->get('qty'),
                'location' => 'bff',
            ]);
            $middle_return_log->save();

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

    public function insertBuffingOperator(Request $request)
    {
        try {
            $emp = DB::table('employees')
                ->where('employee_id', '=', $request->get('employee_id'))
                ->update([
                    'tag' => $request->get('tag'),
                ]);

            $employee_groups = DB::table('employee_groups')
                ->insert([
                    'employee_id' => $request->get('employee_id'),
                    'location' => 'bff',
                    'group' => $request->get('group'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $employee_groups = DB::connection('digital_kanban')
                ->table('employee_groups')
                ->insert([
                    'employee_id' => $request->get('employee_id'),
                    'location' => 'bff',
                    'group' => $request->get('group'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $response = array(
                'status' => true,
                'message' => 'update successful',
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

    public function updateBuffingOperator(Request $request)
    {
        try {
            $emp = DB::table('employees')
                ->where('employee_id', '=', $request->get('employee_id'))
                ->update([
                    'tag' => $request->get('new_tag'),
                ]);

            $employee_groups = DB::table('employee_groups')
                ->where('employee_id', '=', $request->get('employee_id'))
                ->update([
                    'employee_id' => $request->get('employee_id'),
                    'group' => $request->get('group'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $employee_groups = DB::connection('digital_kanban')
                ->table('employee_groups')
                ->where('employee_id', '=', $request->get('employee_id'))
                ->update([
                    'employee_id' => $request->get('employee_id'),
                    'group' => $request->get('group'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $response = array(
                'status' => true,
                'message' => 'update successful',
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

    public function deleteBuffingOperator(Request $request)
    {
        try {
            $delete = DB::table('employee_groups')
                ->where('employee_id', '=', $request->get('employee_id'))
                ->delete();

            $delete_op = DB::connection('digital_kanban')
                ->table('employee_groups')
                ->where('employee_id', '=', $request->get('employee_id'))
                ->delete();

            $response = array(
                'status' => true,
                'message' => 'update successful',
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

    public function updateBuffingTarget(Request $request)
    {
        try {
            $update = MiddleTarget::where('id', '=', $request->get('id'))->update([
                'target' => $request->get('target'),
            ]);

            $response = array(
                'status' => true,
                'message' => 'update successful',
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

    public function fetchBuffingStore()
    {
        $data = db::connection('digital_kanban')
            ->select("SELECT inv.material_num, m.material_description, m.model, m.`key`, inv.material_qty, SUM(inv.material_qty) AS quantity, GROUP_CONCAT(inv.no_kanban) AS kanban FROM buffing_inventories inv
			LEFT JOIN materials m ON m.material_number = inv.material_num
			WHERE lokasi = 'STORE'
			GROUP BY inv.material_num, m.material_description, m.model, m.`key`, inv.material_qty, inv.material_qty
			ORDER BY m.`key`, m.model");

        return DataTables::of($data)
            ->addColumn('kanbans', function ($data) {
                $cell = '';
                if ($data->kanban) {
                    $kanban = explode(',', $data->kanban);
                    sort($kanban);
                    for ($i = 0; $i < count($kanban); $i++) {
                        $cell .= '<span class="label" style="margin-right: 1%; color: black; background-color: #4189FC; border: 1px solid black;">' . $kanban[$i] . '</span>';
                    }

                    $additional_lop = ($data->quantity / $data->material_qty) - count($kanban);
                    for ($i = 0; $i < $additional_lop; $i++) {
                        $cell .= '<span class="label" style="margin-right: 1%; color: black; background-color: #4189FC; border: 1px solid black;">&nbsp;</span>';
                    }

                } else {
                    $lop = $data->quantity / $data->material_qty;
                    for ($i = 0; $i < $lop; $i++) {
                        $cell .= '<span class="label" style="margin-right: 1%; color: black; background-color: #4189FC; border: 1px solid black;">&nbsp;</span>';
                    }
                }

                return $cell;

                dd($cell);
            })
            ->rawColumns([
                'kanbans' => 'kanbans',
            ])
            ->make(true);

    }

    public function fetchCheckKanban(Request $request)
    {
        $sloc = $request->get('sloc');
        $tag = $request->get('tag');
        $other = substr($tag, 1);

        $kanban = db::connection('digital_kanban')->table('buffing_inventories')
            ->whereIn('material_tag_id', [$tag, $other])
            ->first();

        if (!$kanban) {
            $response = array(
                'status' => false,
                'message' => 'RFID Invalid',
            );
            return Response::json($response);
        }

        $material = Material::where("material_number", "=", $kanban->material_num)->first();

        if (!str_contains($material->issue_storage_location, strtoupper($sloc))) {
            $response = array(
                'status' => false,
                'message' => 'Wrong Location',
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'kanban' => $kanban,
            'material' => $material,
        );
        return Response::json($response);

    }

    public function fetchBuffingKanban(Request $request)
    {
        $location = $request->get('location');
        $sloc = $request->get('sloc');

        if ($location == 'key') {

            $data = db::connection('digital_kanban')
                ->table('buffing_inventories')
                ->leftJoin('materials', 'materials.material_number', '=', 'buffing_inventories.material_num')
                ->where('materials.issue_storage_location', $sloc)
                ->select(
                    'buffing_inventories.idx',
                    'buffing_inventories.material_num',
                    'materials.material_description',
                    'materials.model',
                    'materials.key',
                    'buffing_inventories.lokasi',
                    'buffing_inventories.material_tag_id',
                    'buffing_inventories.no_kanban',
                    'buffing_inventories.status',
                    'buffing_inventories.updated_at'
                )
                ->orderBy('materials.key', 'ASC')
                ->orderBy('materials.model', 'ASC')
                ->orderBy('buffing_inventories.no_kanban', 'no_kanban')
                ->get();

            return DataTables::of($data)
                ->addColumn('loc', function ($data) {
                    if (str_contains($data->lokasi, 'BUFFING')) {
                        return 'BUFFING';
                    } else {
                        return $data->lokasi;
                    }
                })
                ->addColumn('action', function ($data) {
                    return '<a href="javascript:void(0)" style="padding-top: 2%; padding-bottom: 2%; margin-top: 2%; margin-bottom: 2%;" class="btn btn-sm btn-default" onClick="showEdit(id)" id="' . $data->idx . '"><i class="fa  fa-pencil-square-o"></i>&nbsp;&nbsp;Edit</a>';
                })
                ->rawColumns([
                    'loc' => 'loc',
                    'action' => 'action',
                ])
                ->make(true);

        }
    }

    public function fetchResumeKonseling(Request $request)
    {

        $dateFrom = date('Y-m-d', strtotime('-1 Months'));
        $dateTo = date('Y-m-d');

        if (strlen($request->get('datefrom')) > 0) {
            $dateFrom = date('Y-m-d', strtotime($request->get('datefrom')));
        }
        if (strlen($request->get('dateto')) > 0) {
            $dateTo = date('Y-m-d', strtotime($request->get('dateto')));
        }

        $ng_target = MiddleTarget::where('target_name', 'NG Rate')
            ->where('location', 'bff')
            ->first();

        $ng = db::select("SELECT date.week_date, report.target, report.cek FROM
			(SELECT week_date FROM weekly_calendars
			WHERE week_date BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "') AS date
			LEFT JOIN
			(SELECT date, COUNT(ng_rate) target, SUM(cek) AS cek FROM
			(SELECT resume.*, IF(cek.remark IS NOT NULL, 1, 0) AS cek FROM
			(SELECT resume.*, resume.ng/resume.quantity AS ng_rate FROM
			(SELECT ng.*, IF(m.model = 'A82Z', 10, IF(m.hpl = 'ASKEY', 15, 8)) AS quantity FROM
			(SELECT DATE(ng.created_at) AS date, ng.material_number, ng.remark, SUM(ng.quantity) AS ng FROM middle_buffing_ng_logs ng
			WHERE DATE(ng.created_at) BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "'
			GROUP BY date, ng.material_number, ng.remark) AS ng
			LEFT JOIN materials m ON m.material_number = ng.material_number) resume
			HAVING ng_rate > " . ($ng_target->target / 100) . ") AS resume
			LEFT JOIN
			(SELECT DISTINCT ng.remark FROM middle_buffing_ng_logs ng
			WHERE DATE(ng.created_at) BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "'
			AND ng.`check` IS NOT NULL) AS cek
			ON cek.remark = resume.remark) AS report
			GROUP BY date) AS report
			ON report.date = date.week_date
			ORDER BY date.week_date ASC");

        $eff = '';

        $response = array(
            'status' => true,
            'datefrom' => date('d M Y', strtotime($dateFrom)),
            'dateto' => date('d M Y', strtotime($dateTo)),
            'ng' => $ng,
            'eff' => $eff,
        );
        return Response::json($response);
    }

    public function fetchResumeKanban($storage_location)
    {
        $data = db::select("SELECT resume.material_number, materials.material_description, materials.model, materials.`key`, materials.surface, resume.queue, resume.wip, resume.stockroom, (resume.queue+resume.wip+resume.stockroom) AS total_edar, resume.wip_tiga, resume.inactive FROM
			(SELECT kanban.material_number, SUM(queue) AS queue, SUM(wip) AS wip, SUM(wip_tiga) AS wip_tiga, SUM(stockroom) AS stockroom, SUM(inactive) AS inactive FROM
			(SELECT material_number, quantity, 1 AS queue, 0 AS wip, 0 AS wip_tiga, 0 AS stockroom, 0 AS inactive FROM barrel_queues
			UNION ALL
			SELECT material_number, quantity, 0 AS queue, 1 AS wip, 0 AS wip_tiga, 0 AS stockroom, 0 AS inactive FROM middle_inventories
			WHERE location <> 'stockroom'
			UNION ALL
			SELECT material_number, quantity, 0 AS queue, 0 AS wip, 1 AS wip_tiga, 0 AS stockroom, 0 AS inactive FROM middle_inventories
			WHERE TIMESTAMPDIFF(DAY,created_at,NOW()) > 3
			AND location <> 'stockroom'
			UNION ALL
			SELECT inventories.material_number, inventories.lot, 0 AS queue, 0 AS wip, 0 AS wip_tiga, 1 AS stockroom, 0 AS inactive FROM kitto.inventories
			LEFT JOIN kitto.materials ON materials.material_number = inventories.material_number
			WHERE materials.category = 'KEY'
			AND materials.location like '%51%'
			AND inventories.lot > 0
			UNION ALL
			SELECT material_number, quantity, 0 AS queue, 0 AS wip, 0 AS wip_tiga, 0 AS stockroom, 1 AS inactive FROM barrel_queue_inactives
			UNION ALL
			SELECT inventories.material_number, inventories.lot, 0 AS queue, 1 AS wip, 0 AS wip_tiga, 0 AS stockroom, 0 AS inactive FROM kitto.inventories
			LEFT JOIN kitto.completions ON kitto.completions.barcode_number = kitto.inventories.barcode_number
			LEFT JOIN kitto.materials ON kitto.materials.material_number = kitto.inventories.material_number
			WHERE kitto.completions.active = 1
			AND kitto.inventories.lot = 0
			AND kitto.inventories.issue_location = 'CL51'
			AND kitto.materials.category = 'KEY'
			UNION ALL
			SELECT inventories.material_number, inventories.lot, 0 AS queue, 0 AS wip, 1 AS wip_tiga, 0 AS stockroom, 0 AS inactive FROM kitto.inventories
			LEFT JOIN kitto.completions ON kitto.completions.barcode_number = kitto.inventories.barcode_number
			LEFT JOIN kitto.materials ON kitto.materials.material_number = kitto.inventories.material_number
			WHERE kitto.completions.active = 1
			AND kitto.inventories.lot = 0
			AND kitto.inventories.issue_location = 'CL51'
			AND TIMESTAMPDIFF(DAY,kitto.inventories.updated_at,NOW()) > 3
			AND kitto.materials.category = 'KEY'
			) AS kanban
			GROUP BY kanban.material_number) AS resume
			LEFT JOIN materials ON materials.material_number = resume.material_number
			WHERE materials.issue_storage_location = '" . $storage_location . "'
			AND materials.hpl LIKE '%KEY%'
			ORDER BY materials.`key`, materials.model, materials.surface");

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);
    }

    public function fetchResumeKanbanBuffing($storage_location)
    {
        $data = db::select("SELECT materials.model, materials.`key`, materials.material_description, resume.*, IF((resume.queue + resume.wip + resume.store) <> resume.total, 0, 1) AS `check` FROM
			(SELECT material_num, SUM(queue) AS queue, SUM(wip) AS wip, SUM(store) AS store, SUM(total) AS total, SUM(wip_lebih) AS wip_lebih FROM
			(SELECT material_num, 1 AS queue, 0 AS wip, 0 AS store, 0 AS total, 0 AS wip_lebih FROM db_ympi_rack.buffing_queues queue
			UNION ALL
			SELECT material_num, 0 AS queue, 1 AS wip, 0 AS store, 0 AS total, 0 AS wip_lebih FROM db_ympi_rack.buffing_inventories inv
			WHERE lokasi LIKE '%BUFFING%'
			UNION ALL
			SELECT material_num, 0 AS queue, 0 AS wip, 1 AS store, 0 AS total, 0 AS wip_lebih FROM db_ympi_rack.buffing_inventories inv
			WHERE lokasi = 'STORE'
			UNION ALL
			SELECT material_num, 0 AS queue, 0 AS wip, 0 AS store, 1 AS total, 0 AS wip_lebih FROM db_ympi_rack.buffing_inventories inv
			UNION ALL
			SELECT material_num, 0 AS queue, 0 AS wip, 0 AS store, 0 AS total, 1 AS wip_lebih FROM db_ympi_rack.buffing_inventories inv
			WHERE lokasi LIKE '%BUFFING%'
			AND TIMESTAMPDIFF(DAY,updated_at,NOW()) > 3
			)AS kanban
			GROUP BY material_num
			) AS resume
			LEFT JOIN materials
			ON materials.material_number = resume.material_num
			WHERE materials.issue_storage_location = '" . $storage_location . "'
			AND materials.hpl LIKE '%KEY%'
			ORDER BY materials.`key`, materials.model");

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);
    }

    public function fetchOpAnalysisDetail(Request $request)
    {
        $date = date('Y-m-d', strtotime($request->get('date')));

        $details = db::connection('digital_kanban')->select("SELECT
			data_log.operator_id,
			ympimis.employee_syncs.`name`,
			sum( data_log.material_qty ) AS result,
			sum( TIMESTAMPDIFF( MINUTE, sedang_start_time, selesai_start_time ) ) AS actual,
			sum( ( data_log.material_qty * ympimis.standard_times.time ) / 60 ) AS standard
			FROM
			data_log
			LEFT JOIN ympimis.standard_times ON ympimis.standard_times.material_number = data_log.material_number
			LEFT JOIN ympimis.employee_syncs ON ympimis.employee_syncs.employee_id = data_log.operator_id
			WHERE
			date( data_log.sedang_start_time ) = '" . $date . "'
			GROUP BY
			data_log.operator_id,
			ympimis.employee_syncs.`name`");

        $response = array(
            'status' => true,
            'details' => $details,
        );
        return Response::json($response);
    }

    public function fetchOpAnalysis(Request $request)
    {
        $dateFrom = date('Y-m-01');
        $dateTo = date('Y-m-d');

        if (strlen($request->get('dateFrom')) > 0) {
            $dateFrom = date('Y-m-d', strtotime($request->get('dateFrom')));
        }
        if (strlen($request->get('dateTo')) > 0) {
            $dateTo = date('Y-m-d', strtotime($request->get('dateTo')));
        }

        $data_logs = db::connection('digital_kanban')->select("SELECT
			DATE_FORMAT( data_logs.date, '%d-%b-%y' ) AS cat,
			data_logs.*,
			( SELECT target FROM ympimis.middle_targets WHERE target_name = 'Normal Working Time' AND location = 'bff' ) AS target
			FROM
			(
			SELECT
			date( sedang_start_time ) AS date,
			count( DISTINCT operator_id ) AS divider,
			sum( TIMESTAMPDIFF( MINUTE, sedang_start_time, selesai_start_time ) ) AS actual,
			sum( ( data_log.material_qty * ympimis.standard_times.time ) / 60 ) AS standard
			FROM
			data_log
			LEFT JOIN ympimis.standard_times ON standard_times.material_number = data_log.material_number
			WHERE
			sedang_start_time >= '" . $dateFrom . " 00:00:00'
			AND sedang_start_time <= '" . $dateTo . " 23:59:59'
			GROUP BY
			date( sedang_start_time )
			) AS data_logs
			LEFT JOIN ympimis.weekly_calendars ON weekly_calendars.week_date = data_logs.date
			WHERE
			weekly_calendars.remark <> 'H'
			ORDER BY
			date ASC");

        $response = array(
            'status' => true,
            'datas' => $data_logs,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        );
        return Response::json($response);

    }

    public function fetchReportBuffingCancelled(Request $request)
    {
        $tanggal = "";
        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
            $tanggal = "and date(l.created_at) >= '" . $datefrom . "' ";
            if (strlen($request->get('dateto')) > 0) {
                $dateto = date('Y-m-d', strtotime($request->get('dateto')));
                $tanggal = $tanggal . "and date(l.created_at) <= '" . $dateto . "' ";
            }
        }

        $data = db::select("select u.username, u.`name`, l.tag, l.material_number, m.model, m.`key`, l.quantity, l.created_at  from middle_return_logs l
			left join users u on l.employee_id = u.id
			left join materials m on m.material_number = l.material_number
			where l.location = 'bff' " . $tanggal);

        return DataTables::of($data)->make(true);
    }

    public function fetchTarget(Request $request)
    {
        $target = MiddleTarget::where('target_name', '=', $request->get('target_name'))
            ->where('location', '=', $request->get('location'))
            ->get();

        $response = array(
            'status' => true,
            'target' => $target,
        );
        return Response::json($response);
    }

    public function fetchBuffingTarget($loc)
    {
        $target = MiddleTarget::where('location', '=', $loc)->get();

        $response = array(
            'status' => true,
            'target' => $target,
        );
        return Response::json($response);
    }

    public function fetchBuffingOperator($loc)
    {
        $operator = DB::table('employee_groups')
            ->leftJoin('employees', 'employees.employee_id', '=', 'employee_groups.employee_id')
            ->where('employee_groups.location', $loc)
            ->select(
                'employee_groups.employee_id',
                'employees.name',
                'employee_groups.group',
                'employees.tag'
            )
            ->orderBy('employee_groups.group', 'ASC')
            ->orderBy('employees.name', 'ASC')
            ->get();

        $response = array(
            'status' => true,
            'operator' => $operator,
        );
        return Response::json($response);
    }

    public function fetchBuffingCanceled(Request $request)
    {
        try {

            $cancel = db::connection('digital_kanban')->select("SELECT d.idx, d.operator_id, d.material_tag_id, d.material_number, m.model, m.`key`, d.selesai_start_time, d.material_qty FROM data_log d
				left join materials m on m.material_number = d.material_number
				where material_tag_id = '" . $request->get('tag') . "'
				order by idx desc
				limit 1");

            $operator = db::select("select name from employees where employee_id in ('" . $cancel[0]->operator_id . "')");

            $response = array(
                'status' => true,
                'cancel' => $cancel,
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

    public function fetchReportOpTime(Request $request)
    {
        $tanggal = "";
        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
            $tanggal = "and date(selesai_start_time) >= '" . $datefrom . "' ";
            if (strlen($request->get('dateto')) > 0) {
                $dateto = date('Y-m-d', strtotime($request->get('dateto')));
                $tanggal = $tanggal . "and date(selesai_start_time) <= '" . $dateto . "' ";
            }
        }

        $data = db::connection('digital_kanban')->select("select d.operator_id, IFNULL(u.`name`, 'Not Found') `name`, m.`key`, m.model, d.sedang_start_time, d.selesai_start_time, TIMESTAMPDIFF(SECOND,sedang_start_time,selesai_start_time) as act_time, s.time * d.material_qty as std_time from data_log d
			left join users u on d.operator_id = u.username
			left join materials m on m.material_number = d.material_number
			left join standard_times s on s.material_number = d.material_number
			where TIMESTAMPDIFF(SECOND,sedang_start_time,selesai_start_time) < (s.time * d.material_qty * " . $request->get('condition') . ") " . $tanggal);

        return DataTables::of($data)->make(true);
    }

    public function fetchReportTrainingOpNg(Request $request)
    {
        $tanggal = "";
        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
            $tanggal = "and date(check_time) >= '" . $datefrom . "' ";
            if (strlen($request->get('dateto')) > 0) {
                $dateto = date('Y-m-d', strtotime($request->get('dateto')));
                $tanggal = $tanggal . "and date(check_time) <= '" . $dateto . "' ";
            }
        }

        $data = db::select("SELECT ng.operator_id, op.`name` AS operator, ng.material_number, m.model, m.`key`, ng.ng, ng.buffing_time, trainer.`name` AS trainer, kensa.`name` AS op_kensa FROM
			(SELECT employee_id, tag, material_number, operator_id, `check`, buffing_time, GROUP_CONCAT(ng_name, '[',quantity,']',' ') AS ng FROM middle_ng_logs
			WHERE location = 'bff-kensa'
			AND check_time IS NOT NULL
			" . $tanggal . "
			GROUP BY employee_id, tag, material_number, operator_id, `check`, buffing_time) AS ng
			LEFT JOIN materials m ON m.material_number = ng.material_number
			LEFT JOIN employees op ON op.employee_id = ng.operator_id
			LEFT JOIN employees kensa ON kensa.employee_id = ng.employee_id
			LEFT JOIN users trainer ON trainer.id = ng.`check`
			ORDER BY ng.buffing_time ASC");

        return DataTables::of($data)->make(true);
    }

    public function fetchReportTrainingOpEff(Request $request)
    {
        $tanggal = "";
        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
            $tanggal = "and date(check_time) >= '" . $datefrom . "' ";
            if (strlen($request->get('dateto')) > 0) {
                $dateto = date('Y-m-d', strtotime($request->get('dateto')));
                $tanggal = $tanggal . "and date(check_time) <= '" . $dateto . "' ";
            }
        }

        $data = db::connection('digital_kanban')->select("SELECT l.operator_id,
			op.`name`,
			l.material_number,
			m.`key`, m.model,
			std.time,
			l.sedang_start_time,
			l.selesai_start_time,
			ROUND((std.time * l.material_qty/60),2) AS std,
			ROUND((TIMESTAMPDIFF(SECOND,l.sedang_start_time,l.selesai_start_time)/60),2) AS actual,
			ROUND(((std.time * l.material_qty/60)/(TIMESTAMPDIFF(SECOND,l.sedang_start_time,l.selesai_start_time)/60)*100),2) AS eff,
			trainer.`name` AS trainer
			FROM data_log l
			LEFT JOIN ympimis.materials m ON m.material_number = l.material_number
			LEFT JOIN standard_times std ON std.material_number = l.material_number
			LEFT JOIN ympimis.employees op ON op.employee_id = l.operator_id
			LEFT JOIN ympimis.users trainer ON trainer.id = l.`check`
			WHERE check_time IS NOT NULL
			" . $tanggal . "
			ORDER BY l.sedang_start_time ASC");

        return DataTables::of($data)->make(true);
    }

    public function fetchReportOpTimeQty(Request $request)
    {
        $tanggal = "";
        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
            $tanggal = "and date(selesai_start_time) >= '" . $datefrom . "' ";
            if (strlen($request->get('dateto')) > 0) {
                $dateto = date('Y-m-d', strtotime($request->get('dateto')));
                $tanggal = $tanggal . "and date(selesai_start_time) <= '" . $dateto . "' ";
            }
        }

        $qty = db::connection('digital_kanban')->select("select d.operator_id, u.`name`, count(idx) as jml from data_log d
			left join users u on d.operator_id = u.username left join standard_times s on s.material_number = d.material_number
			where TIMESTAMPDIFF(SECOND,sedang_start_time,selesai_start_time) < (s.time * d.material_qty * " . $request->get('condition') . ") " . $tanggal . " GROUP BY d.operator_id, u.`name` ORDER BY jml desc");

        return DataTables::of($qty)->make(true);
    }

    public function fetchBuffingAdjustment(Request $request)
    {

        $rack = "";
        if ($request->get('grup') != null) {
            $grups = $request->get('grup');
            $grup = "";

            for ($x = 0; $x < count($grups); $x++) {
                $grup = $grup . "'" . $grups[$x] . "'";
                if ($x != count($grups) - 1) {
                    $grup = $grup . ",";
                }
            }
            $rack = "where rack in (" . $grup . ") ";
        }

        $queue = db::connection('digital_kanban')->select("SELECT q.idx, q.rack, q.material_num, m.material_description, q.material_qty, q.created_at FROM buffing_queues q left join materials m on q.material_num = m.material_number " . $rack . "
			order by created_at asc");

        return DataTables::of($queue)
            ->addColumn('check', function ($queue) {
                return '<input type="checkbox" class="queue" id="' . $queue->idx . '+' . $queue->material_description . '" onclick="showSelected(this)">';
            })
            ->rawColumns(['check' => 'check'])
            ->make(true);

    }

    public function fetchBuffingOpEffDetail(Request $request)
    {
        $tgl = $request->get('tgl');
        $nik = (explode(" - ", $request->get('nama')));

        $nama = Employee::where('employee_id', '=', $nik[0])->select('name')->first();

        $data_log = db::connection('digital_kanban')->select("select d.material_number, m.model, m.`key`, akan_start_time as akan, sedang_start_time as sedang, selesai_start_time as selesai, material_qty, ROUND(TIMESTAMPDIFF(SECOND,sedang_start_time,selesai_start_time)/60,2) as act, ROUND((s.time * material_qty)/60,2) as std from data_log d
			left join materials m on d.material_number = m.material_number
			left join standard_times s on d.material_number = s.material_number
			where operator_id = '" . $nik[0] . "'
			and date(selesai_start_time) = '" . $tgl . "' order by selesai_start_time asc");

        $good = db::select("select l.buffing_time, l.material_number, m.model, m.`key`, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as op_kensa, quantity from middle_buffing_logs l
			left join materials m on m.material_number = l.material_number
			left join employees e on e.employee_id = l.employee_id
			where l.operator_id = '" . $nik[0] . "'
			and date(l.buffing_time) = '" . $tgl . "'
			order by buffing_time asc");

        $ng = db::select("SELECT a.buffing_time, a.material_number, a.model, a.`key`, a.remark, concat(SPLIT_STRING(a.op_kensa, ' ', 1), ' ', SPLIT_STRING(a.op_kensa, ' ', 2)) as op_kensa, SUM(quantity) as quantity from (
			select l.buffing_time, l.material_number, m.model, m.`key`, l.remark, e.`name` as op_kensa, l.quantity as quantity from middle_buffing_ng_logs l
			left join materials m on m.material_number = l.material_number
			left join employees e on e.employee_id = l.employee_id
			where l.operator_id = '" . $nik[0] . "'
			and date(l.buffing_time) = '" . $tgl . "') a   GROUP BY remark order by buffing_time asc");

        $cek = db::select("SELECT l.buffing_time, l.material_number, m.model, m.`key`, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as op_kensa, quantity FROM middle_buffing_check_logs l
			left join materials m on l.material_number = m.material_number
			left join employees e on e.employee_id = l.employee_id
			where operator_id = '" . $nik[0] . "'
			and date(l.buffing_time) = '" . $tgl . "'
			order by buffing_time asc");

        $ng_ng = db::select("select l.buffing_time, l.material_number, m.model, m.`key`, l.ng_name, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as op_kensa, l.quantity as quantity from middle_buffing_ng_logs l
			left join materials m on m.material_number = l.material_number
			left join employees e on e.employee_id = l.employee_id
			where l.operator_id = '" . $nik[0] . "'
			and date(l.buffing_time) = '" . $tgl . "'");

        $ng_qty = db::Select("select ng_name, sum(quantity) as qty from middle_buffing_ng_logs
			where operator_id = '" . $nik[0] . "'
			and date(buffing_time) = '" . $tgl . "'
			GROUP BY ng_name
			Order by qty desc");

        $response = array(
            'status' => true,
            'nik' => $nik[0],
            'nama' => $nama['name'],
            'data_log' => $data_log,
            'good' => $good,
            'ng' => $ng,
            'cek' => $cek,
            'ng_ng' => $ng_ng,
            'ng_qty' => $ng_qty,
        );
        return Response::json($response);

    }

    public function fetchBuffingOpResult(Request $request)
    {
        $date = '';
        if (strlen($request->get("tanggal")) > 0) {
            $date = date('Y-m-d', strtotime($request->get("tanggal")));
        } else {
            $date = date('Y-m-d');
        }

        $group = '';
        if ($request->get('group') != null) {
            $groups = explode(",", $request->get('group'));
            for ($i = 0; $i < count($groups); $i++) {
                $group = $group . "'" . $groups[$i] . "'";
                if ($i != (count($groups) - 1)) {
                    $group = $group . ',';
                }
            }
            $group = " and e.`group` in (" . $group . ") ";
        }

        $op_result = db::connection('digital_kanban')->select("select e.`group`, l.operator_id, sum(material_qty) as qty from data_log l left join employee_groups e on e.employee_id = l.operator_id
			where DATE_FORMAT(l.selesai_start_time,'%Y-%m-%d') = '" . $date . "' " . $group . "
			GROUP BY e.`group`, l.operator_id");

        $emp_name = Employee::select('employee_id', db::raw('concat(SPLIT_STRING(employees.name, " ", 1), " ", SPLIT_STRING(employees.name, " ", 2)) as name'))->get();

        $response = array(
            'status' => true,
            'date' => $date,
            'emp_name' => $emp_name,
            'op_result' => $op_result,
        );
        return Response::json($response);

    }

    public function fetchBuffingNgDaily(Request $request)
    {
        $bulan = "";
        if (strlen($request->get('bulan')) > 0) {
            $bulan = $request->get('bulan');
        } else {
            $bulan = date('m-Y');
        }

        $daily = db::select("SELECT tgl.week_date, tgl.hpl, resume.ng, resume.`check` AS g, (resume.ng/resume.`check`*100) as ng_rate from
			(select week_date, hpl from
			(select week_date from weekly_calendars where DATE_FORMAT(week_date,'%m-%Y') = '" . $bulan . "') date
			cross join
			(select DISTINCT hpl from materials where hpl in ('ASKEY','TSKEY')) hpl ) tgl
			left join
			(select date, remark as hpl, ng, `check` from middle_buffing_daily_resumes
			where DATE_FORMAT(date,'%m-%Y') = '" . $bulan . "' AND location = 'bff-kensa-sx') resume
			on resume.date = tgl.week_date AND tgl.hpl = resume.hpl
			ORDER BY tgl.week_date;");

        $response = array(
            'status' => true,
            'daily' => $daily,
            'bulan' => $bulan,
        );
        return Response::json($response);

    }

    public function fetchBuffingNgDailyAll(Request $request, $id)
    {
        $bulan = "";

        $kensa = '';
        $hpl = '';

        if ($id == 'sax') {
            $kensa = 'bff-kensa-sx';
        } elseif ($id == 'fl') {
            $kensa = 'bff-kensa-fl';
            $hpl = 'FLKEY';
        } elseif ($id == 'cl') {
            $kensa = 'bff-kensa-cl';
            $hpl = 'CLKEY';
        }

        if (strlen($request->get('bulan')) > 0) {
            $bulan = $request->get('bulan');
        } else {
            $bulan = date('m-Y');
        }

        $daily = db::select("SELECT tgl.week_date, tgl.hpl, resume.ng, resume.`check` AS g, (resume.ng/resume.`check`*100) as ng_rate from
            (select week_date, hpl from
            (select week_date from weekly_calendars where DATE_FORMAT(week_date,'%m-%Y') = '" . $bulan . "') date
            cross join
            (select DISTINCT hpl from materials where hpl in ('" . $hpl . "')) hpl ) tgl
            left join
            (select date, remark as hpl, ng, `check` from middle_buffing_daily_resumes
            where DATE_FORMAT(date,'%m-%Y') = '" . $bulan . "' AND location = '" . $kensa . "') resume
            on resume.date = tgl.week_date AND tgl.hpl = resume.hpl
            ORDER BY tgl.week_date");

        $response = array(
            'status' => true,
            'daily' => $daily,
            'bulan' => $bulan,
        );
        return Response::json($response);

    }

    public function fetchBuffingNgKeyMonthly(Request $request)
    {

        $bulan = "";
        $bulanText = "";
        if (strlen($request->get('bulan')) > 0) {
            $bulan = date('Y-m', strtotime('01-' . $request->get('bulan')));
            $bulanText = date('M Y', strtotime('01-' . $request->get('bulan')));
        } else {
            $bulan = date('Y-m');
            $bulanText = date('M Y');
        }

        $ngKey_alto = db::select("SELECT hpl, `key`, sum(ng) AS ng FROM middle_buffing_monthly_ng_resumes
			WHERE hpl = 'ASKEY'
			AND location = 'bff-kensa-sx'
			AND `month` = '" . $bulan . "'
			GROUP BY hpl, `key`
			ORDER BY ng DESC
			LIMIT 10");

        $ngKey_tenor = db::select("SELECT hpl, `key`, sum(ng) AS ng FROM middle_buffing_monthly_ng_resumes
			WHERE hpl = 'TSKEY'
			AND location = 'bff-kensa-sx'
			AND `month` = '" . $bulan . "'
			GROUP BY hpl, `key`
			ORDER BY ng DESC
			LIMIT 10");

        $ngKey_alto_detail = db::select(" SELECT hpl, `key`, ng_name, ng FROM middle_buffing_monthly_ng_resumes
			WHERE hpl = 'ASKEY'
			AND location = 'bff-kensa-sx'
			AND `month` = '" . $bulan . "'");

        $ngKey_tenor_detail = db::select(" SELECT hpl, `key`, ng_name, ng FROM middle_buffing_monthly_ng_resumes
			WHERE hpl = 'TSKEY'
			AND location = 'bff-kensa-sx'
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

    public function fetchBuffingNgKeyMonthlyAll(Request $request, $id)
    {

        $bulan = "";
        $bulanText = "";

        $kensa = '';
        $hpl = '';

        if ($id == 'sax') {
            $kensa = 'bff-kensa-sx';
        } elseif ($id == 'fl') {
            $kensa = 'bff-kensa-fl';
            $hpl = 'FLKEY';
        } elseif ($id == 'cl') {
            $kensa = 'bff-kensa-cl';
            $hpl = 'CLKEY';
        }

        if (strlen($request->get('bulan')) > 0) {
            $bulan = date('Y-m', strtotime('01-' . $request->get('bulan')));
            $bulanText = date('M Y', strtotime('01-' . $request->get('bulan')));
        } else {
            $bulan = date('Y-m');
            $bulanText = date('M Y');
        }

        $ngKey = db::select("SELECT hpl, `key`, sum(ng) AS ng FROM middle_buffing_monthly_ng_resumes
            WHERE hpl = '" . $hpl . "'
            AND location = '" . $kensa . "'
            AND `month` = '" . $bulan . "'
            GROUP BY hpl, `key`
            ORDER BY ng DESC
            LIMIT 10");

        $ngKey_detail = db::select(" SELECT hpl, `key`, ng_name, ng FROM middle_buffing_monthly_ng_resumes
            WHERE hpl = '" . $hpl . "'
            AND location = '" . $kensa . "'
            AND `month` = '" . $bulan . "'");

        $response = array(
            'status' => true,
            'ngKey' => $ngKey,
            'ngKey_detail' => $ngKey_detail,
            'bulanText' => $bulanText,
        );
        return Response::json($response);
    }

    public function fetchBuffingNgMonthly(Request $request)
    {
        $bulan = "";
        $bulanText = "";
        if (strlen($request->get('bulan')) > 0) {
            $bulan = date('Y-m', strtotime('01-' . $request->get('bulan')));
            $bulanText = date('M Y', strtotime('01-' . $request->get('bulan')));
        } else {
            $bulan = date('Y-m');
            $bulanText = date('M Y');
        }

        $ng_alto = db::select("SELECT ng.hpl, ng.ng_name, ng.ng, cek.`check` FROM
			(SELECT hpl, ng_name, SUM(ng) as ng FROM middle_buffing_monthly_ng_resumes
			WHERE hpl = 'ASKEY'
			AND `month` = '" . $bulan . "'
			AND location = 'bff-kensa-sx'
			GROUP BY hpl, ng_name) AS ng
			LEFT JOIN
			(SELECT remark, SUM(`check`) as `check` FROM middle_buffing_daily_resumes
			WHERE remark = 'ASKEY'
			AND DATE_FORMAT(date,'%Y-%m') = '" . $bulan . "'
			AND location = 'bff-kensa-sx'
			GROUP BY remark) AS cek
			ON cek.remark = ng.hpl
			ORDER BY ng.ng DESC");

        $ng_tenor = db::select("SELECT ng.hpl, ng.ng_name, ng.ng, cek.`check` FROM
			(SELECT hpl, ng_name, SUM(ng) as ng FROM middle_buffing_monthly_ng_resumes
			WHERE hpl = 'TSKEY'
			AND `month` = '" . $bulan . "'
			AND location = 'bff-kensa-sx'
			GROUP BY hpl, ng_name) AS ng
			LEFT JOIN
			(SELECT remark, SUM(`check`) as `check` FROM middle_buffing_daily_resumes
			WHERE remark = 'TSKEY'
			AND DATE_FORMAT(date,'%Y-%m') = '" . $bulan . "'
			AND location = 'bff-kensa-sx'
			GROUP BY remark) AS cek
			ON cek.remark = ng.hpl
			ORDER BY ng.ng DESC");

        $response = array(
            'status' => true,
            'ng_alto' => $ng_alto,
            'ng_tenor' => $ng_tenor,
            'bulanText' => $bulanText,
        );
        return Response::json($response);

    }

    public function fetchBuffingNgMonthlyAll(Request $request, $id)
    {
        $bulan = "";
        $bulanText = "";

        $kensa = '';
        $hpl = '';

        if ($id == 'sax') {
            $kensa = 'bff-kensa-sx';
        } elseif ($id == 'fl') {
            $kensa = 'bff-kensa-fl';
            $hpl = 'FLKEY';
        } elseif ($id == 'cl') {
            $kensa = 'bff-kensa-cl';
            $hpl = 'CLKEY';
        }

        if (strlen($request->get('bulan')) > 0) {
            $bulan = date('Y-m', strtotime('01-' . $request->get('bulan')));
            $bulanText = date('M Y', strtotime('01-' . $request->get('bulan')));
        } else {
            $bulan = date('Y-m');
            $bulanText = date('M Y');
        }

        $ng = db::select("SELECT ng.hpl, ng.ng_name, ng.ng, cek.`check` FROM
            (SELECT hpl, ng_name, SUM(ng) as ng FROM middle_buffing_monthly_ng_resumes
            WHERE hpl = '" . $hpl . "'
            AND `month` = '" . $bulan . "'
            AND location = '" . $kensa . "'
            GROUP BY hpl, ng_name) AS ng
            LEFT JOIN
            (SELECT remark, SUM(`check`) as `check` FROM middle_buffing_daily_resumes
            WHERE remark = '" . $hpl . "'
            AND DATE_FORMAT(date,'%Y-%m') = '" . $bulan . "'
            AND location = '" . $kensa . "'
            GROUP BY remark) AS cek
            ON cek.remark = ng.hpl
            ORDER BY ng.ng DESC");

        $response = array(
            'status' => true,
            'ng' => $ng,
            'bulanText' => $bulanText,
        );
        return Response::json($response);

    }

    public function fetchBuffingOpWorkMonthlyDetail(Request $request)
    {
        $detail = db::connection('digital_kanban')->select("select d.operator_id, DATE(d.selesai_start_time) as tgl, SUM(TIMESTAMPDIFF(MINUTE,d.sedang_start_time,d.selesai_start_time)) as act, SUM((s.time*d.material_qty))/60 as std from data_log d
			left join standard_times s on s.material_number = d.material_number
			where DATE_FORMAT(d.selesai_start_time,'%m-%Y') = '" . $request->get('bulan') . "'
			and operator_id = '" . $request->get('nik') . "'
			GROUP BY d.operator_id, tgl
			HAVING act > 60");

        $emp = Employee::where('employee_id', '=', $request->get('nik'))->select('employee_id', db::raw('concat(SPLIT_STRING(employees.name, " ", 1), " ", SPLIT_STRING(employees.name, " ", 2)) as name'))->get();

        $response = array(
            'status' => true,
            'detail' => $detail,
            'emp' => $emp,
        );
        return Response::json($response);
    }

    public function fetchBuffingOpEffMonthly(Request $request)
    {
        $datefrom = date("Y-m-01");
        $dateto = date("Y-m-d");
        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
        }
        if (strlen($request->get('dateto')) > 0) {
            $dateto = date('Y-m-d', strtotime($request->get('dateto')));
        }

        $ng = db::select("SELECT g.operator_id, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name`, COALESCE(ng.ng,0) as ng, COALESCE(g.g,0) as g, (ng.ng / g.g) as ng_rate, ((g.g - ng.ng) / g.g) as post_rate FROM
			(SELECT l.operator_id, sum(l.quantity) g from middle_buffing_check_logs l
			where l.location = 'bff-kensa-sx'
			and DATE_FORMAT(l.buffing_time,'%Y-%m-%d') between '" . $datefrom . "' and '" . $dateto . "'
			and DATE_FORMAT(l.buffing_time,'%a') != 'Sun'
			and DATE_FORMAT(l.buffing_time,'%a') != 'Sat'
			GROUP BY l.operator_id) g
			left join
			(SELECT l.operator_id, sum(l.quantity) ng from middle_buffing_ng_logs l
			where l.location = 'bff-kensa-sx'
			and DATE_FORMAT(l.buffing_time,'%Y-%m-%d') between '" . $datefrom . "' and '" . $dateto . "'
			and DATE_FORMAT(l.buffing_time,'%a') != 'Sun'
			and DATE_FORMAT(l.buffing_time,'%a') != 'Sat'
			GROUP BY l.operator_id) ng
			on ng.operator_id = g.operator_id
			left join employees e on e.employee_id = g.operator_id
			where g.operator_id in (select eg.employee_id from employee_groups eg where location = 'bff')
			order by operator_id asc;");

        $eff = db::connection("digital_kanban")->select("select operator_id, act, std, std/act as eff from (
			select d.operator_id, SUM(TIMESTAMPDIFF(MINUTE,d.sedang_start_time,d.selesai_start_time)) as act, SUM((s.time*d.material_qty))/60 as std from data_log d
			left join standard_times s on s.material_number = d.material_number
			where DATE_FORMAT(d.selesai_start_time,'%Y-%m-%d') between '" . $datefrom . "' and '" . $dateto . "'
			GROUP BY d.operator_id
			HAVING act > 60) eff
			where operator_id in (select employee_id from employee_groups where location = 'bff')
			order by operator_id asc;");

        $response = array(
            'status' => true,
            'ng' => $ng,
            'eff' => $eff,
            'datefrom' => $datefrom,
            'dateto' => $dateto,
        );
        return Response::json($response);

    }

    public function fetchBuffingOpWorkMonthly(Request $request, $id)
    {
        $bulan = "";
        $datefrom = date("Y-m-01");
        $dateto = date("Y-m-d");

        if ($id == 'assesment') {
            if (strlen($request->get('datefrom')) > 0) {
                $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
            }
            if (strlen($request->get('dateto')) > 0) {
                $dateto = date('Y-m-d', strtotime($request->get('dateto')));
            }

            $act = db::connection('digital_kanban')->select("SELECT prod.operator_id, e.`name`, AVG(prod.act) as act, AVG(prod.std) as std FROM
				(select d.operator_id, DATE(d.selesai_start_time) as tgl, SUM(TIMESTAMPDIFF(MINUTE,d.sedang_start_time,d.selesai_start_time)) as act, SUM((s.time*d.material_qty))/60 as std from data_log d
				left join standard_times s on s.material_number = d.material_number
				where DATE_FORMAT(d.selesai_start_time,'%Y-%m-%d') between '" . $datefrom . "' and '" . $dateto . "'
				and d.operator_id not like '%PG%'
				GROUP BY d.operator_id, tgl
				HAVING act > 60) prod
				LEFT JOIN ympimis.employees e ON e.employee_id = prod.operator_id
				GROUP BY prod.operator_id, e.`name`
				ORDER BY std DESC");
        } else {
            if (strlen($request->get('bulan')) > 0) {
                $bulan = $request->get('bulan');
            } else {
                $bulan = date('m-Y');
            }

            $act = db::connection('digital_kanban')->select("SELECT prod.operator_id, e.`name`, AVG(prod.act) as act, AVG(prod.std) as std FROM
				(select d.operator_id, DATE(d.selesai_start_time) as tgl, SUM(TIMESTAMPDIFF(MINUTE,d.sedang_start_time,d.selesai_start_time)) as act, SUM((s.time*d.material_qty))/60 as std from data_log d
				left join standard_times s on s.material_number = d.material_number
				where DATE_FORMAT(d.selesai_start_time,'%m-%Y') = '" . $bulan . "'
				and d.operator_id not like '%PG%'
				GROUP BY d.operator_id, tgl
				HAVING act > 60) prod
				LEFT JOIN ympimis.employees e ON e.employee_id = prod.operator_id
				GROUP BY prod.operator_id, e.`name`
				ORDER BY std DESC");
        }

        $emp = db::select("select g.employee_id, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name` from employee_groups g left join employees e on e.employee_id = g.employee_id
			where g.location = 'bff'");

        $work_target = MiddleTarget::where('location', 'bff')
            ->where('target_name', 'Operator Productivity')
            ->first();

        $response = array(
            'status' => true,
            'act' => $act,
            'emp' => $emp,
            'target' => $work_target->target,
            'bulan' => $bulan,
            'datefrom' => $datefrom,
            'dateto' => $dateto,
        );
        return Response::json($response);
    }

    public function fetchBuffingOpNgMonthlyDetail(Request $request)
    {
        $ng = db::select("SELECT g.date, g.operator_id, concat(SPLIT_STRING(g.`name`, ' ', 1), ' ', SPLIT_STRING(g.`name`, ' ', 2)) as `name`, COALESCE(ng.ng,0) as ng, COALESCE(g.g,0) as g, (ng.ng / g.g) as ng_rate FROM
			(SELECT date(l.buffing_time) as date, l.operator_id, e.`name`, sum(l.quantity) ng from middle_buffing_ng_logs l
			left join employees e on e.employee_id = l.operator_id
			left join materials m on l.material_number = m.material_number
			where l.location = 'bff-kensa-sx'
			and DATE_FORMAT(l.buffing_time,'%m-%Y') = '" . $request->get('bulan') . "'
			and l.operator_id = '" . $request->get('nik') . "'
			and DATE_FORMAT(l.buffing_time,'%a') != 'Sun'
			and DATE_FORMAT(l.buffing_time,'%a') != 'Sat'
			GROUP BY date, l.operator_id, e.`name`) ng
			left join
			(SELECT date(l.buffing_time) as date, l.operator_id, e.`name`, sum(l.quantity) g from middle_buffing_check_logs l
			left join employees e on e.employee_id = l.operator_id
			left join materials m on l.material_number = m.material_number
			where l.location = 'bff-kensa-sx'
			and DATE_FORMAT(l.buffing_time,'%m-%Y') = '" . $request->get('bulan') . "'
			and l.operator_id = '" . $request->get('nik') . "'
			and DATE_FORMAT(l.buffing_time,'%a') != 'Sun'
			and DATE_FORMAT(l.buffing_time,'%a') != 'Sat'
			GROUP BY date, l.operator_id, e.`name`) g
			on ng.date = g.date and ng.operator_id = g.operator_id
			order by g.date asc");

        $response = array(
            'status' => true,
            'ng' => $ng,
        );
        return Response::json($response);

    }

    public function fetchBuffingOpNgMonthly(Request $request, $id)
    {
        $bulan = "";
        $datefrom = date("Y-m-01");
        $dateto = date("Y-m-d");

        if ($id == 'assesment') {
            if (strlen($request->get('datefrom')) > 0) {
                $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
            }
            if (strlen($request->get('dateto')) > 0) {
                $dateto = date('Y-m-d', strtotime($request->get('dateto')));
            }

            $op_ng = db::select("SELECT rate.operator_id, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name`, rate.ng as ng, rate.g as g, rate.ng_rate as ng_rate FROM
				(SELECT g.operator_id, COALESCE(ng.ng,0) as ng, COALESCE(g.g,0) as g, (ng.ng / g.g) as ng_rate FROM
				(SELECT l.operator_id, sum(l.quantity) ng from middle_buffing_ng_logs l
				where l.location = 'bff-kensa-sx'
				and DATE_FORMAT(l.buffing_time,'%Y-%m-%d') between '" . $datefrom . "' and '" . $dateto . "'
				and DATE_FORMAT(l.buffing_time,'%a') != 'Sun'
				and DATE_FORMAT(l.buffing_time,'%a') != 'Sat'
				GROUP BY l.operator_id) ng
				left join
				(SELECT l.operator_id, sum(l.quantity) g from middle_buffing_check_logs l
				where l.location = 'bff-kensa-sx'
				and DATE_FORMAT(l.buffing_time,'%Y-%m-%d') between '" . $datefrom . "' and '" . $dateto . "'
				and DATE_FORMAT(l.buffing_time,'%a') != 'Sun'
				and DATE_FORMAT(l.buffing_time,'%a') != 'Sat'
				GROUP BY l.operator_id) g
				on ng.operator_id = g.operator_id) rate
				left join employees e on e.employee_id = rate.operator_id
				order by rate.ng_rate DESC");

        } else {
            if (strlen($request->get('bulan')) > 0) {
                $bulan = $request->get('bulan');
            } else {
                $bulan = date('m-Y');
            }

            $op_ng = db::select("SELECT rate.operator_id, concat(SPLIT_STRING(e.`name`, ' ', 1), ' ', SPLIT_STRING(e.`name`, ' ', 2)) as `name`, rate.ng as ng, rate.g as g, rate.ng_rate as ng_rate FROM
				(SELECT g.operator_id, COALESCE(ng.ng,0) as ng, COALESCE(g.g,0) as g, (ng.ng / g.g) as ng_rate FROM
				(SELECT l.operator_id, sum(l.quantity) ng from middle_buffing_ng_logs l
				where l.location = 'bff-kensa-sx'
				and DATE_FORMAT(l.buffing_time,'%m-%Y') = '" . $bulan . "'
				and DATE_FORMAT(l.buffing_time,'%a') != 'Sun'
				and DATE_FORMAT(l.buffing_time,'%a') != 'Sat'
				and l.operator_id not like 'PG%'
				GROUP BY l.operator_id) ng
				left join
				(SELECT l.operator_id, sum(l.quantity) g from middle_buffing_check_logs l
				where l.location = 'bff-kensa-sx'
				and DATE_FORMAT(l.buffing_time,'%m-%Y') = '" . $bulan . "'
				and DATE_FORMAT(l.buffing_time,'%a') != 'Sun'
				and DATE_FORMAT(l.buffing_time,'%a') != 'Sat'
				and l.operator_id not like 'PG%'
				GROUP BY l.operator_id) g
				on ng.operator_id = g.operator_id) rate
				left join employees e on e.employee_id = rate.operator_id
				order by rate.ng_rate DESC");
        }

        $target_ng = db::table('middle_targets')
            ->where('target_name', 'NG Rate')
            ->where('location', 'bff')
            ->first();

        $response = array(
            'status' => true,
            'target_ng' => $target_ng->target,
            'op_ng' => $op_ng,
            'bulan' => $bulan,
            'datefrom' => $datefrom,
            'dateto' => $dateto,
        );
        return Response::json($response);

    }

    public function fetchBuffingNgRateWeekly(Request $request)
    {
        $bulan = "";
        $bulanText = "";
        if (strlen($request->get('bulan')) > 0) {
            $bulan = date('Y-m', strtotime('01-' . $request->get('bulan')));
            $bulanText = date('M Y', strtotime('01-' . $request->get('bulan')));
        } else {
            $bulan = date('Y-m');
            $bulanText = date('M Y');
        }

        $weekly = db::select("SELECT tgl.week_name, tgl.min, COALESCE (resume.ng, 0) AS ng, COALESCE (resume.g, 0) AS g, (resume.ng / resume.g) AS ng_rate FROM
			(SELECT week_name AS week_name, MIN(week_date) AS min FROM weekly_calendars
			WHERE DATE_FORMAT(week_date, '%Y-%m') = '" . $bulan . "'
			GROUP BY week_name
			ORDER BY min ASC) tgl
			LEFT JOIN
			(SELECT `month`, `week`, ng, `check` as g FROM middle_buffing_weekly_resumes
			WHERE `month` = '" . $bulan . "'
			AND location = 'bff-kensa-sx') resume
			ON tgl.week_name = resume.`week`
			ORDER BY tgl.min ASC");

        $response = array(
            'status' => true,
            'weekly' => $weekly,
            'bulanText' => $bulanText,
        );
        return Response::json($response);
    }

    public function fetchBuffingNgRateWeeklyAll(Request $request, $id)
    {
        $bulan = "";
        $bulanText = "";

        $kensa = '';

        if ($id == 'sax') {
            $kensa = 'bff-kensa-sx';
        } elseif ($id == 'fl') {
            $kensa = 'bff-kensa-fl';
        } elseif ($id == 'cl') {
            $kensa = 'bff-kensa-cl';
        }

        if (strlen($request->get('bulan')) > 0) {
            $bulan = date('Y-m', strtotime('01-' . $request->get('bulan')));
            $bulanText = date('M Y', strtotime('01-' . $request->get('bulan')));
        } else {
            $bulan = date('Y-m');
            $bulanText = date('M Y');
        }

        $weekly = db::select("SELECT tgl.week_name, tgl.min, COALESCE (resume.ng, 0) AS ng, COALESCE (resume.g, 0) AS g, (resume.ng / resume.g) AS ng_rate FROM
            (SELECT week_name AS week_name, MIN(week_date) AS min FROM weekly_calendars
            WHERE DATE_FORMAT(week_date, '%Y-%m') = '" . $bulan . "'
            GROUP BY week_name
            ORDER BY min ASC) tgl
            LEFT JOIN
            (SELECT `month`, `week`, ng, `check` as g FROM middle_buffing_weekly_resumes
            WHERE `month` = '" . $bulan . "'
            AND location = '" . $kensa . "') resume
            ON tgl.week_name = resume.`week`
            ORDER BY tgl.min ASC");

        $response = array(
            'status' => true,
            'weekly' => $weekly,
            'bulanText' => $bulanText,
        );
        return Response::json($response);
    }

    public function fetchBuffingNgRateMonthly(Request $request)
    {
        $fy = '';
        if ($request->get('fy') != null) {
            $fys = explode(",", $request->get('fy'));
            for ($i = 0; $i < count($fys); $i++) {
                $fy = $fy . "'" . $fys[$i] . "'";
                if ($i != (count($fys) - 1)) {
                    $fy = $fy . ',';
                }
            }
        } else {
            $get_fy = db::select("select fiscal_year from weekly_calendars where week_date = DATE_FORMAT(now(),'%Y-%m-%d')");
            foreach ($get_fy as $key) {
                $fy = "'" . $key->fiscal_year . "'";
            }
        }

        $monthly = db::select("SELECT tgl.tgl, COALESCE (resume.ng, 0) AS ng, COALESCE (resume.g, 0) AS g, (resume.ng / resume.g) AS ng_rate FROM
			(SELECT DATE_FORMAT( week_date, '%Y-%m' ) AS tgl FROM weekly_calendars
			WHERE fiscal_year IN (" . $fy . ")
			GROUP BY tgl) tgl
			LEFT JOIN
			(SELECT `month` AS tgl, ng AS ng, `check` AS g FROM middle_buffing_monthly_resumes
			WHERE fiscal_year IN (" . $fy . ") AND location = 'bff-kensa-sx') resume
			ON tgl.tgl = resume.tgl
			ORDER BY tgl.tgl ASC");

        $target = MiddleTarget::where('location', 'bff')
            ->where('target_name', 'NG Rate')
            ->first();

        $fy = "";
        if ($request->get('fy') != null) {
            $fys = explode(",", $request->get('fy'));
            for ($x = 0; $x < count($fys); $x++) {
                $fy = $fy . " " . $fys[$x] . " ";
                if ($x != count($fys) - 1) {
                    $fy = $fy . "&";
                }
            }
        } else {
            $get_fy = db::select("select fiscal_year from weekly_calendars where week_date = DATE_FORMAT(now(),'%Y-%m-%d')");
            foreach ($get_fy as $key) {
                $fy = $key->fiscal_year;
            }
        }

        $response = array(
            'status' => true,
            'monthly' => $monthly,
            'target' => $target,
            'fy' => $fy,
        );
        return Response::json($response);
    }

    public function fetchBuffingNgRateMonthlyAll(Request $request, $id)
    {
        $fy = '';
        $kensa = '';

        if ($id == 'sax') {
            $kensa = 'bff-kensa-sx';
        } elseif ($id == 'fl') {
            $kensa = 'bff-kensa-fl';
        } elseif ($id == 'cl') {
            $kensa = 'bff-kensa-cl';
        }

        if ($request->get('fy') != null) {
            $fys = explode(",", $request->get('fy'));
            for ($i = 0; $i < count($fys); $i++) {
                $fy = $fy . "'" . $fys[$i] . "'";
                if ($i != (count($fys) - 1)) {
                    $fy = $fy . ',';
                }
            }
        } else {
            $get_fy = db::select("select fiscal_year from weekly_calendars where week_date = DATE_FORMAT(now(),'%Y-%m-%d')");
            foreach ($get_fy as $key) {
                $fy = "'" . $key->fiscal_year . "'";
            }
        }

        $monthly = db::select("SELECT tgl.tgl, COALESCE (resume.ng, 0) AS ng, COALESCE (resume.g, 0) AS g, (resume.ng / resume.g) AS ng_rate FROM
            (SELECT DATE_FORMAT( week_date, '%Y-%m' ) AS tgl FROM weekly_calendars
            WHERE fiscal_year IN (" . $fy . ")
            GROUP BY tgl) tgl
            LEFT JOIN
            (SELECT `month` AS tgl, ng AS ng, `check` AS g FROM middle_buffing_monthly_resumes
            WHERE fiscal_year IN (" . $fy . ") AND location = '" . $kensa . "' ) resume
            ON tgl.tgl = resume.tgl
            ORDER BY tgl.tgl ASC");

        $target = MiddleTarget::where('location', 'bff')
            ->where('target_name', 'NG Rate')
            ->first();

        $fy = "";
        if ($request->get('fy') != null) {
            $fys = explode(",", $request->get('fy'));
            for ($x = 0; $x < count($fys); $x++) {
                $fy = $fy . " " . $fys[$x] . " ";
                if ($x != count($fys) - 1) {
                    $fy = $fy . "&";
                }
            }
        } else {
            $get_fy = db::select("select fiscal_year from weekly_calendars where week_date = DATE_FORMAT(now(),'%Y-%m-%d')");
            foreach ($get_fy as $key) {
                $fy = $key->fiscal_year;
            }
        }

        $response = array(
            'status' => true,
            'monthly' => $monthly,
            'target' => $target,
            'fy' => $fy,
        );
        return Response::json($response);
    }

    public function fetchBuffingIcAtokotei(Request $request)
    {
        $date = '';
        if (strlen($request->get("tanggal")) > 0) {
            $date = date('Y-m-d', strtotime($request->get("tanggal")));
        } else {
            $date = date('Y-m-d');
        }

        $where = "";
        if ($request->get('code') != null) {
            $codes = $request->get('code');
            $code = "";

            for ($x = 0; $x < count($codes); $x++) {
                $code = $code . "'" . substr($codes[$x], 0, 3) . "'";
                if ($x != count($codes) - 1) {
                    $code = $code . ",";
                }
            }
            $where = "and m.origin_group_code in (" . $code . ") ";
        }

        $ng_name = db::select("select l.ng_name, sum(l.quantity) as jml from middle_lacquering_ng_logs l
			left join materials m on l.material_number = m.material_number
			where DATE_FORMAT(l.created_at,'%Y-%m-%d') = '" . $date . "' " . $where . "
			and location = 'lcq-incoming'
			group by l.ng_name order by jml desc");

        $key = db::select("select m.`key`, sum(l.quantity) as jml from middle_lacquering_ng_logs l
			left join materials m on l.material_number = m.material_number
			where DATE_FORMAT(l.created_at,'%Y-%m-%d') = '" . $date . "' " . $where . "
			and location = 'lcq-incoming'
			group by m.`key` order by jml desc limit 10");

        $detail_key = db::select("SELECT resume.`key`, resume.ng_name, SUM(resume.jml) AS jml FROM
            (select b.`key`, a.ng_name, 0 AS jml from
            (select ng_name from ng_lists
            where location = 'lcq-incoming') a
            cross join
            (select distinct `key` from materials where hpl like '%KEY%' and issue_storage_location = 'SX51' order by `key` asc) b
            UNION ALL
            select m.`key`, l.ng_name, sum(l.quantity) as jml from middle_lacquering_ng_logs l
            left join materials m on l.material_number = m.material_number
            where DATE_FORMAT(l.created_at,'%Y-%m-%d') = '" . $date . "' " . $where . "
            and location = 'lcq-incoming'
            group by m.`key`, l.ng_name) AS resume
            GROUP BY resume.`key`, resume.ng_name");

        $response = array(
            'status' => true,
            'date' => $date,
            'ng_name' => $ng_name,
            'key' => $key,
            'detail_key' => $detail_key,
        );
        return Response::json($response);
    }

    public function fetchIcAtokoteiSubassy(Request $request)
    {
        $date = '';
        if (strlen($request->get("date")) > 0) {
            $date = date('Y-m-d', strtotime($request->get("date")));
        } else {
            $date = date('Y-m-d');
        }

        $where = "";
        $where_key = "";
        if (strlen($request->get('key')) > 0) {
            $where_key = $request->get('key');
            $where = "and m.hpl = '" . $where_key . "' ";
        }

        $resume = db::select("SELECT `check`.date, `check`.`check`, ng.ng FROM
			(select date(l.created_at) AS date, sum(l.quantity) as `check` from middle_" . $request->get("loc") . "_check_logs l
			left join materials m on l.material_number = m.material_number
			where date(l.created_at) = '" . $date . "' " . $where . "
			and location = 'subassy-incoming-sx'
			group by date) `check`
			LEFT JOIN
			(select date(l.created_at) AS date, sum(l.quantity) as ng from middle_" . $request->get("loc") . "_ng_logs l
			left join materials m on l.material_number = m.material_number
			where date(l.created_at) = '" . $date . "' " . $where . "
			and location = 'subassy-incoming-sx'
			group by date) ng
			ON `check`.date = ng.date");

        $ng_name = db::select("select l.ng_name, sum(l.quantity) as jml from middle_" . $request->get("loc") . "_ng_logs l
			left join materials m on l.material_number = m.material_number
			where DATE_FORMAT(l.created_at,'%Y-%m-%d') = '" . $date . "' " . $where . "
			and location = 'subassy-incoming-sx'
			group by l.ng_name order by jml desc");

        $key = db::select("select m.`key`, sum(l.quantity) as jml from middle_" . $request->get("loc") . "_ng_logs l
			left join materials m on l.material_number = m.material_number
			where DATE_FORMAT(l.created_at,'%Y-%m-%d') = '" . $date . "' " . $where . "
			and location = 'subassy-incoming-sx'
			group by m.`key` order by jml desc limit 10");

        $detail_key = db::select("select ng_name.`key`, ng_name.ng_name, COALESCE(ng.jml,0) as jml from
			(select b.`key`, a.ng_name from
			(select ng_name from ng_lists
			where location = 'subassy-incoming-sx'
			and remark = '" . $request->get("loc") . "') a
			cross join
			(select distinct `key` from materials where hpl like '%KEY%' and issue_storage_location = 'SX51' order by `key` asc) b
			order by `key` asc) ng_name
			left join
			(select m.`key`, l.ng_name, sum(l.quantity) as jml from middle_" . $request->get("loc") . "_ng_logs l
			left join materials m on l.material_number = m.material_number
			where DATE_FORMAT(l.created_at,'%Y-%m-%d') = '" . $date . "' " . $where . "
			and location = 'subassy-incoming-sx'
			group by m.`key`, l.ng_name) ng
			on ng_name.ng_name = ng.ng_name and ng_name.`key` = ng.`key`
			order by `key` asc");

        $response = array(
            'status' => true,
            'resume' => $resume,
            'where_key' => $where_key,
            'date' => $date,
            'ng_name' => $ng_name,
            'key' => $key,
            'detail_key' => $detail_key,
        );
        return Response::json($response);
    }

    public function fetchIcAtokoteiSubassyOp(Request $request)
    {
        $date = '';
        if (strlen($request->get("tanggal")) > 0) {
            $date = date('Y-m-d', strtotime($request->get("tanggal")));
        } else {
            $date = date('Y-m-d');
        }

        $detail = db::select("SELECT ng.operator_id, concat(SPLIT_STRING(emp.`name`, ' ', 1), ' ', SPLIT_STRING(emp.`name`, ' ', 2)) as `name`, ng.ng_name, SUM(ng.quantity) AS quantity  FROM `middle_lacquering_ng_logs` ng
			LEFT JOIN employee_syncs emp ON emp.employee_id = ng.operator_id
			WHERE DATE(ng.created_at) = '" . $date . "'
			AND ng.location = 'subassy-incoming-sx'
			GROUP BY ng.operator_id, emp.`name`, ng.ng_name");

        $resume = array();
        for ($i = 0; $i < count($detail); $i++) {

            if (count($resume) > 0) {
                $index = 0;
                $new = true;
                for ($j = 0; $j < count($resume); $j++) {
                    if ($resume[$j]['operator_id'] == $detail[$i]->operator_id) {
                        $index = $j;
                        $new = false;
                        break;
                    }
                }

                if ($new) {
                    $row = array();
                    $row['operator_id'] = $detail[$i]->operator_id;
                    $row['name'] = $detail[$i]->name;
                    $row['quantity'] = $detail[$i]->quantity;

                    $resume[] = $row;
                } else {
                    $resume[$index]['quantity'] = $resume[$index]['quantity'] + $detail[$i]->quantity;
                }

            } else {
                $row = array();
                $row['operator_id'] = $detail[$i]->operator_id;
                $row['name'] = $detail[$i]->name;
                $row['quantity'] = $detail[$i]->quantity;

                $resume[] = $row;
            }
        }

        usort($resume, function ($a, $b) {
            return $b['quantity'] - $a['quantity'];
        });

        $response = array(
            'status' => true,
            'date' => $date,
            'resume' => $resume,
            'detail' => $detail,
        );
        return Response::json($response);

    }

    public function fetchBuffingHourlyNg(Request $request)
    {
        $date = '';
        if (strlen($request->get("tanggal")) > 0) {
            $date = date('Y-m-d', strtotime($request->get("tanggal")));
        } else {
            $date = date('Y-m-d');
        }

        $jam = [
            "DATE_FORMAT(m.buffing_time,'%H:%m:%s') >= '00:00:00' and DATE_FORMAT(m.buffing_time,'%H:%m:%s') < '01:00:00'",
            "DATE_FORMAT(m.buffing_time,'%H:%m:%s') >= '01:00:00' and DATE_FORMAT(m.buffing_time,'%H:%m:%s') < '03:00:00'",
            "DATE_FORMAT(m.buffing_time,'%H:%m:%s') >= '03:00:00' and DATE_FORMAT(m.buffing_time,'%H:%m:%s') < '05:00:00'",
            "DATE_FORMAT(m.buffing_time,'%H:%m:%s') >= '05:00:00' and DATE_FORMAT(m.buffing_time,'%H:%m:%s') < '07:00:00'",
            "DATE_FORMAT(m.buffing_time,'%H:%m:%s') >= '07:00:00' and DATE_FORMAT(m.buffing_time,'%H:%m:%s') < '09:00:00'",
            "DATE_FORMAT(m.buffing_time,'%H:%m:%s') >= '09:00:00' and DATE_FORMAT(m.buffing_time,'%H:%m:%s') < '11:00:00'",
            "DATE_FORMAT(m.buffing_time,'%H:%m:%s') >= '11:00:00' and DATE_FORMAT(m.buffing_time,'%H:%m:%s') < '14:00:00'",
            "DATE_FORMAT(m.buffing_time,'%H:%m:%s') >= '14:00:00' and DATE_FORMAT(m.buffing_time,'%H:%m:%s') < '16:00:00'",
            "DATE_FORMAT(m.buffing_time,'%H:%m:%s') >= '16:00:00' and DATE_FORMAT(m.buffing_time,'%H:%m:%s') < '18:00:00'",
            "DATE_FORMAT(m.buffing_time,'%H:%m:%s') >= '18:00:00' and DATE_FORMAT(m.buffing_time,'%H:%m:%s') < '20:00:00'",
            "DATE_FORMAT(m.buffing_time,'%H:%m:%s') >= '20:00:00' and DATE_FORMAT(m.buffing_time,'%H:%m:%s') < '22:00:00'",
            "DATE_FORMAT(m.buffing_time,'%H:%m:%s') >= '22:00:00' and DATE_FORMAT(m.buffing_time,'%H:%m:%s') < '23:59:59'",
        ];

        $ng = [];
        $detail = [];

        for ($i = 0; $i < count($jam); $i++) {
            $ng[$i] = db::select("select rate.shift, rate.operator_id, concat(SPLIT_STRING(e.name, ' ', 1), ' ', SPLIT_STRING(e.name, ' ', 2)) as `name`, rate.tot, rate.ng, rate.rate from
				(select c.shift, c.operator_id, c.jml as tot, COALESCE(ng.jml,0) as ng, (COALESCE(ng.jml,0)/c.jml) as rate from
				(select eg.`group` as shift, m.operator_id, sum(m.quantity) as jml from middle_check_logs m
				left join materials mt on mt.material_number = m.material_number
				left join employee_groups eg on eg.employee_id = m.operator_id
				where m.location = 'bff-kensa'
				and m.operator_id is not null
				and DATE_FORMAT(m.buffing_time,'%Y-%m-%d') = '" . $date . "'
				and " . $jam[$i] . "
				GROUP BY shift, m.operator_id) c
				left join
				(select eg.`group` as shift, m.operator_id, sum(m.quantity) as jml from middle_ng_logs m
				left join materials mt on mt.material_number = m.material_number
				left join employee_groups eg on eg.employee_id = m.operator_id
				where m.location = 'bff-kensa'
				and m.operator_id is not null
				and DATE_FORMAT(m.buffing_time,'%Y-%m-%d') = '" . $date . "'
				and " . $jam[$i] . "
				GROUP BY shift, m.operator_id) ng
				on c.shift = ng.shift and c.operator_id = ng.operator_id) rate
				left join employees e on e.employee_id = rate.operator_id
				ORDER BY shift, rate.rate desc
				limit 5");

            $detail[$i] = db::select("select m.operator_id, m.ng_name, sum(m.quantity) as qty from middle_ng_logs m
				where date(m.buffing_time) = '" . $date . "'
				and " . $jam[$i] . "
				and location = 'bff-kensa'
				GROUP BY m.operator_id, m.ng_name");

        }

        $response = array(
            'status' => true,
            'ng' => $ng,
            'detail' => $detail,
        );
        return Response::json($response);

    }

    public function fetchBuffingDailyOpEff(Request $request)
    {
        $datefrom = date("Y-m-d", strtotime("-3 Months"));
        $dateto = date("Y-m-d");

        $where_op = "";
        $where_op2 = "";

        $where_op = "";
        if ($request->get('condition') != null) {
            if ($request->get('condition') == 'ng') {
                $operators = db::select("select g.operator_id, g.jml as g, ng.jml as ng, (ng.jml/g.jml) as rate from
					(select m.operator_id, sum(m.quantity) as jml from middle_check_logs m
					left join materials mt on mt.material_number = m.material_number
					where location = 'bff-kensa'
					and mt.origin_group_code = '043'
					and DATE_FORMAT(m.buffing_time,'%Y-%m-%d') = '" . date("Y-m-d") . "'
					GROUP BY m.operator_id) g
					left join
					(select m.operator_id, sum(m.quantity) as jml from middle_ng_logs m
					left join materials mt on mt.material_number = m.material_number
					where location = 'bff-kensa'
					and mt.origin_group_code = '043'
					and DATE_FORMAT(m.buffing_time,'%Y-%m-%d') = '" . date("Y-m-d") . "'
					GROUP BY m.operator_id) ng
					on ng.operator_id = g.operator_id
					order by rate desc
					limit 5");
                $operator = "";

                for ($x = 0; $x < count($operators); $x++) {
                    $operator = $operator . "'" . $operators[$x]->operator_id . "'";
                    if ($x != count($operators) - 1) {
                        $operator = $operator . ",";
                    }
                }
                $where_op = " m.operator_id in (" . $operator . ") ";
                $where_op2 = " and operator_id in (" . $operator . ") ";

            } else if ($request->get('condition') == 'eff') {
                $operators = db::connection('digital_kanban')->select("select l.operator_id, sum(TIMESTAMPDIFF(SECOND,l.sedang_start_time,l.selesai_start_time)) as act, sum(material_qty * t.time) as std, (sum(material_qty * t.time)/sum(TIMESTAMPDIFF(SECOND,l.sedang_start_time,l.selesai_start_time))) as eff
					from data_log l left join standard_times t on l.material_number = t.material_number
					where DATE_FORMAT(l.selesai_start_time,'%Y-%m-%d') = '" . date("Y-m-d") . "'
					GROUP BY l.operator_id
					ORDER BY eff asc
					limit 5");
                $operator = "";

                for ($x = 0; $x < count($operators); $x++) {
                    $operator = $operator . "'" . $operators[$x]->operator_id . "'";
                    if ($x != count($operators) - 1) {
                        $operator = $operator . ",";
                    }
                }
                $where_op = " m.operator_id in (" . $operator . ") ";
                $where_op2 = " and operator_id in (" . $operator . ") ";

            }

        } else {
            if ($request->get('operator') != null) {
                $operators = $request->get('operator');
                $operator = "";

                for ($x = 0; $x < count($operators); $x++) {
                    $operator = $operator . "'" . $operators[$x] . "'";
                    if ($x != count($operators) - 1) {
                        $operator = $operator . ",";
                    }
                }
                $where_op = " m.operator_id in (" . $operator . ") ";
                $where_op2 = " and operator_id in (" . $operator . ") ";
            } else {
                $where_op = " m.operator_id is not null ";
            }
        }

        $rate = db::select("select date.week_date, date.operator_id, e.`name`, COALESCE(pr.rate,0) as rate  from
			(select week_date, operator_id from
			(select week_date from weekly_calendars where week_date BETWEEN '" . $datefrom . "' and '" . $dateto . "') tgl
			cross join
			(select DISTINCT m.operator_id from middle_check_logs m
			where " . $where_op . "
			and DATE_FORMAT(m.buffing_time,'%Y-%m-%d') BETWEEN '" . $datefrom . "' and '" . $dateto . "') op) date
			left join
			(select rate.tgl, rate.operator_id, rate.tot, rate.ng, rate.rate from
			(select c.tgl, c.operator_id, c.jml as tot, COALESCE(ng.jml,0) as ng, ((c.jml-COALESCE(ng.jml,0))/c.jml) as rate from
			(select DATE_FORMAT(m.buffing_time,'%Y-%m-%d') as tgl, m.operator_id, sum(m.quantity) as jml from middle_check_logs m
			left join materials mt on mt.material_number = m.material_number
			where location = 'bff-kensa' and " . $where_op . "
			and mt.origin_group_code = '043'
			and DATE_FORMAT(m.buffing_time,'%Y-%m-%d') BETWEEN '" . $datefrom . "' and '" . $dateto . "'
			GROUP BY tgl, m.operator_id) c
			left join
			(select DATE_FORMAT(m.buffing_time,'%Y-%m-%d') as tgl, m.operator_id, sum(m.quantity) as jml from middle_ng_logs m
			left join materials mt on mt.material_number = m.material_number
			where location = 'bff-kensa' and " . $where_op . "
			and mt.origin_group_code = '043'
			and DATE_FORMAT(m.buffing_time,'%Y-%m-%d') BETWEEN '" . $datefrom . "' and '" . $dateto . "'
			GROUP BY tgl, m.operator_id) ng
			on c.tgl = ng.tgl and c.operator_id = ng.operator_id) rate
			ORDER BY tgl desc) pr
			on date.week_date = pr.tgl and date.operator_id = pr.operator_id
			left join employees e on e.employee_id = date.operator_id
			order by week_date asc");

        $time_eff = db::connection('digital_kanban')->select("select DATE_FORMAT(l.selesai_start_time,'%Y-%m-%d') as tgl, l.operator_id, sum(TIMESTAMPDIFF(SECOND,l.sedang_start_time,l.selesai_start_time)) as act, sum(material_qty * t.time) as std, (sum(material_qty * t.time)/sum(TIMESTAMPDIFF(SECOND,l.sedang_start_time,l.selesai_start_time))) as eff
			from data_log l left join standard_times t on l.material_number = t.material_number
			where DATE_FORMAT(l.selesai_start_time,'%Y-%m-%d') BETWEEN '" . $datefrom . "' and '" . $dateto . "' " . $where_op2 . "
			GROUP BY tgl, l.operator_id");

        $op = db::select("select DISTINCT m.operator_id, e.`name` from middle_check_logs m left join employees e on m.operator_id = e.employee_id where DATE_FORMAT(m.buffing_time,'%Y-%m-%d') BETWEEN '" . $datefrom . "' and '" . $dateto . "'");

        $response = array(
            'status' => true,
            'rate' => $rate,
            'time_eff' => $time_eff,
            'op' => $op,
        );
        return Response::json($response);
    }

    public function fetchBuffingGroupBalance(Request $request)
    {
        if ($request->get('tanggal') == "") {
            $tanggal = date('Y-m-d');
        } else {
            $tanggal = date('Y-m-d', strtotime($request->get('tanggal')));
        }

        $data = db::select("select plan.`key`, plan.plan, COALESCE(result.result,0) as result from
			(select LEFT(m.`key`,1) as `key`, sum(plan.plan) as plan from
			(select b.material_child, ROUND(sum(a.quantity * t.time / 60),2) as plan from assy_picking_schedules a
			left join bom_components b on a.material_number = b.material_parent
			left join standard_times t on b.material_child = t.material_number
			where a.quantity > 0
			and a.due_date = '" . $tanggal . "'
			and a.remark = 'SX51'
			group by b.material_child) plan
			left join materials m on plan.material_child = m.material_number
			group by LEFT(m.`key`,1)) plan
			left join
			(select left(m.`key`,1) as `key`, ROUND(sum(l.quantity * s.time / 60),2) as result from middle_buffing_logs l
			left join materials m on m.material_number = l.material_number
			left join standard_times s on s.material_number = l.material_number
			where l.location = 'bff-kensa-sx'
			and DATE_FORMAT(l.created_at,'%Y-%m-%d') = '" . $tanggal . "'
			GROUP BY left(m.`key`,1)) result
			on plan.`key` = result.`key`");

        $key = db::connection('digital_kanban')->select("select RIGHT(dev_name,1) as `key`, (count(dev_name)*2) as jml from dev_list
			where dev_name in ('SXKEY-C','SXKEY-D','SXKEY-E','SXKEY-F','SXKEY-G','SXKEY-H','SXKEY-J')
			and date(dev_online_time) = '" . date('Y-m-d') . "'
			GROUP BY dev_name");

        $response = array(
            'status' => true,
            'data' => $data,
            'key' => $key,
            'tanggal' => $tanggal,
        );
        return Response::json($response);

    }

    public function fetchDailyGroupAchievement()
    {
        $datefrom = date("Y-m-d", strtotime("-3 Months"));
        $dateto = date("Y-m-d");

        $data = db::select("select date.week_date, COALESCE(plan1.plan,0) as plan, COALESCE(result.result,0) as result from
			(select week_date from weekly_calendars
			where week_date BETWEEN '" . $datefrom . "' and '" . $dateto . "') date
			left join
			(select due_date, sum(plan.plan) as plan from
			(select a.due_date, b.material_child, ROUND(sum(a.quantity * t.time / 60),2) as plan from assy_picking_schedules a
			left join bom_components b on a.material_number = b.material_parent
			left join standard_times t on b.material_child = t.material_number
			where quantity > 0
			and a.due_date BETWEEN '" . $datefrom . "' and '" . $dateto . "'
			group by a.due_date, b.material_child
			order by a.due_date) plan
			group by due_date) plan1
			on date.week_date = plan1.due_date
			left join
			(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, ROUND(sum(l.quantity * s.time / 60),2) as result from middle_logs l
			left join materials m on m.material_number = l.material_number
			left join standard_times s on s.material_number = l.material_number
			where l.location = 'bff-kensa'
			and DATE_FORMAT(l.created_at,'%Y-%m-%d') BETWEEN '" . $datefrom . "' and '" . $dateto . "'
			GROUP BY tgl) result
			on date.week_date =  result.tgl
			order by date.week_date");

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);
    }

    public function fetchAccumulatedAchievement(Request $request)
    {
        if ($request->get('tanggal') == "") {
            $tanggal = date('Y-m-d');
            $tahun = date('Y');
        } else {
            $tanggal = date('Y-m-d', strtotime($request->get('tanggal')));
            $tahun = date('Y', strtotime($request->get('tanggal')));
        }

        $akumulasi = db::select("select w.week_name, acc.tgl, acc.barrel, acc.bff from
			(select barrel.tgl, COALESCE(barrel.jml,0) as barrel, COALESCE(bff.jml,0) as bff from
			(select DATE_FORMAT(b.created_at,'%Y-%m-%d') as tgl, sum(b.qty) as jml from barrel_logs b
			left join materials m on m.material_number = b.material
			where (b.`status` = 'reset' or b.`status` = 'plt')
			and DATE_FORMAT(b.created_at,'%Y-%m-%d') in (select week_date from weekly_calendars
			where week_name = (select week_name from weekly_calendars where week_date = '" . $tanggal . "')
			and DATE_FORMAT(week_date,'%Y') = '" . $tahun . "')
			group by tgl) barrel
			left join
			(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, sum(l.quantity) as jml from middle_buffing_logs l
			left join materials m on m.material_number = l.material_number
			where l.location = 'bff-kensa-sx'
			and DATE_FORMAT(l.created_at,'%Y-%m-%d') in (select week_date from weekly_calendars
			where week_name = (select week_name from weekly_calendars where week_date = '" . $tanggal . "')
			and DATE_FORMAT(week_date,'%Y') = '" . $tahun . "')
			group by tgl) bff
			on barrel.tgl = bff.tgl ) acc
			left join weekly_calendars w on w.week_date = acc.tgl
			order by tgl asc");

        $response = array(
            'status' => true,
            'akumulasi' => $akumulasi,
            'tanggal' => $tanggal,
        );
        return Response::json($response);
    }

    public function fetchBuffingGroupAchievement(Request $request)
    {
        if ($request->get('tanggal') == "") {
            $tanggal = date('Y-m-d');
        } else {
            $tanggal = date('Y-m-d', strtotime($request->get('tanggal')));
        }

        $data = db::select("select barrel.kunci, COALESCE(barrel.jml,0) as barrel, COALESCE(bff.jml,0) as bff from
			(select LEFT(m.`key`,1) as kunci, sum(b.qty) as jml from barrel_logs b
			left join materials m on m.material_number = b.material
			where (b.`status` = 'reset' or b.`status` = 'plt')
			and DATE_FORMAT(b.created_at,'%Y-%m-%d') = '" . $tanggal . "'
			group by kunci) barrel
			left join
			(select LEFT(m.`key`,1) as kunci, sum(l.quantity) as jml from middle_buffing_logs l
			left join materials m on m.material_number = l.material_number
			where l.location = 'bff-kensa-sx'
			and DATE_FORMAT(l.created_at,'%Y-%m-%d') = '" . $tanggal . "'
			group by kunci) bff
			on barrel.kunci = bff.kunci");

        $bff = db::connection('digital_kanban')->select("select LEFT(m.`key`,1) as kunci, sum(d.material_qty) as jml from data_log d left join materials m on d.material_number = m.material_number
			where date(d.selesai_start_time) = '" . $tanggal . "'
			group by kunci");

        $repair = db::connection('digital_kanban')->select("SELECT LEFT(m.`key`,1) as kunci, SUM(material_qty) as qty FROM buffing_inventories i left join materials m on m.material_number = i.material_num
			where date(i.created_at) = '" . $tanggal . "'
			and lokasi = 'BUFFING-KENSA'
			GROUP BY LEFT(m.`key`,1)");

        $response = array(
            'status' => true,
            'data' => $data,
            'bff' => $bff,
            'repair' => $repair,
            'tanggal' => $tanggal,
        );
        return Response::json($response);
    }

    public function fetchBuffingGroupAchievement_backup(Request $request)
    {
        if ($request->get('tanggal') == "") {
            $tanggal = date('Y-m-d');
        } else {
            $tanggal = date('Y-m-d', strtotime($request->get('tanggal')));
        }

        $query = "select plan.`key`, plan.plan, COALESCE(result.result,0) as result from
		(select LEFT(m.`key`,1) as `key`, sum(plan.plan) as plan from
		(select b.material_child, ROUND(sum(a.quantity * t.time / 60),2) as plan from assy_picking_schedules a
		left join bom_components b on a.material_number = b.material_parent
		left join standard_times t on b.material_child = t.material_number
		where quantity > 0
		and due_date = '" . $tanggal . "'
		group by b.material_child) plan
		left join materials m on plan.material_child = m.material_number
		group by LEFT(m.`key`,1)) plan
		left join
		(select left(m.`key`,1) as `key`, ROUND(sum(l.quantity * s.time / 60),2) as result from middle_logs l
		left join materials m on m.material_number = l.material_number
		left join standard_times s on s.material_number = l.material_number
		where l.location = 'bff-kensa'
		and DATE_FORMAT(l.created_at,'%Y-%m-%d') = '" . $tanggal . "'
		GROUP BY left(m.`key`,1)) result
		on plan.`key` = result.`key`";

        $data = db::select($query);

        $response = array(
            'status' => true,
            'data' => $data,
            'tanggal' => $tanggal,
        );
        return Response::json($response);
    }

    public function fetchBuffingNgKey(Request $request)
    {
        $date = '';
        if (strlen($request->get("tanggal")) > 0) {
            $date = date('Y-m-d', strtotime($request->get("tanggal")));
        } else {
            $date = date('Y-m-d');
        }

        $ngKey = db::select("SELECT m.`key`, sum(ng.quantity) AS ng FROM middle_buffing_ng_logs ng
			LEFT JOIN materials m ON m.material_number = ng.material_number
			WHERE ng.location = 'bff-kensa-sx'
			AND date(ng.created_at) = '" . $date . "'
			GROUP BY m.`key`
			ORDER BY ng DESC
			LIMIT 10");

        $ngKey_detail = db::select("SELECT m.`key`, ng.ng_name, sum(ng.quantity) AS ng FROM middle_buffing_ng_logs ng
			LEFT JOIN materials m ON m.material_number = ng.material_number
			WHERE ng.location = 'bff-kensa-sx'
			AND date(ng.created_at) = '" . $date . "'
			GROUP BY m.`key`, ng.ng_name");

        $response = array(
            'status' => true,
            'date' => $date,
            'ngKey' => $ngKey,
            'ngKey_detail' => $ngKey_detail,
        );
        return Response::json($response);
    }

    public function fetchBuffingOpEffTarget(Request $request)
    {
        $date = '';
        if (strlen($request->get("tanggal")) > 0) {
            $date = date('Y-m-d', strtotime($request->get("tanggal")));
        } else {
            $date = date('Y-m-d');
        }

        $group = '';
        if ($request->get('group') != null) {
            $groups = explode(",", $request->get('group'));
            for ($i = 0; $i < count($groups); $i++) {
                $group = $group . "'" . $groups[$i] . "'";
                if ($i != (count($groups) - 1)) {
                    $group = $group . ',';
                }
            }
            $group = " and e.`group` in (" . $group . ") ";
        }

        $eff_target = db::table("middle_targets")->where('location', '=', 'bff')->where('target_name', '=', 'Operator Efficiency')->select('target')->first();

        $target = db::connection('digital_kanban')->select("select e.`group`, e.employee_id, dl.material_number, CONCAT(m.model,' ',m.`key`) as `key`, dl.finish, dl.act, (dl.material_qty*s.time/60) as std, (dl.material_qty*s.time/60)/dl.act as eff, dl.`check` from employee_groups e
			left join
			(SELECT a.operator_id, a.material_number, time(a.selesai_start_time) as finish,
			TIMESTAMPDIFF(SECOND,a.sedang_start_time,a.selesai_start_time)/60 as act,
			a.material_qty, a.`check`
			FROM (select * from data_log l
			where date(l.selesai_start_time) = '" . $date . "') a
			LEFT JOIN
			(select * from data_log l
			where date(l.selesai_start_time) = '" . $date . "') b
			ON (a.operator_id = b.operator_id AND a.selesai_start_time < b.selesai_start_time)
			WHERE b.selesai_start_time IS NULL
			order by a.operator_id asc) dl
			on dl.operator_id = e.employee_id
			left join standard_times s on s.material_number = dl.material_number
			left join materials m on m.material_number = dl.material_number
			where e.location = 'bff'
			" . $group . "
			order by e.`group`, e.employee_id");

        $emp_name = Employee::select('employee_id', db::raw('concat(SPLIT_STRING(employees.name, " ", 1), " ", SPLIT_STRING(employees.name, " ", 2)) as name'))->get();

        $response = array(
            'status' => true,
            'date' => $date,
            'emp_name' => $emp_name,
            'target' => $target,
            'group' => $group,
            'eff_target' => $eff_target->target,
        );
        return Response::json($response);

    }

    public function fetchBuffingOpWorking(Request $request)
    {
        $date = '';
        if (strlen($request->get("tanggal")) > 0) {
            $date = date('Y-m-d', strtotime($request->get("tanggal")));
        } else {
            $date = date('Y-m-d');
        }

        $group = '';
        if ($request->get('group') != null) {
            $groups = explode(",", $request->get('group'));
            for ($i = 0; $i < count($groups); $i++) {
                $group = $group . "'" . $groups[$i] . "'";
                if ($i != (count($groups) - 1)) {
                    $group = $group . ',';
                }
            }
            $group = " where e.`group` in (" . $group . ") ";
        }

        $working_time = db::connection('digital_kanban')->select("select e.`group`, e.employee_id, dl.act, dl.std from employee_groups e left join (select l.operator_id, sum(TIMESTAMPDIFF(SECOND,l.sedang_start_time,l.selesai_start_time))/60 as act, sum((l.material_qty*t.time))/60 as std from data_log l
			left join standard_times t on l.material_number = t.material_number
			where DATE_FORMAT(l.selesai_start_time,'%Y-%m-%d') = '" . $date . "'
			GROUP BY l.operator_id) dl on dl.operator_id = e.employee_id
			" . $group . "
			ORDER BY e.`group`, e.employee_id asc");

        $emp_name = Employee::select('employee_id', db::raw('concat(SPLIT_STRING(employees.name, " ", 1), " ", SPLIT_STRING(employees.name, " ", 2)) as name'))->get();

        $response = array(
            'status' => true,
            'date' => $date,
            'emp_name' => $emp_name,
            'working_time' => $working_time,
        );
        return Response::json($response);
    }

    public function fetchBuffingOpNgRate(Request $request)
    {
        $datefrom = date("Y-m-d", strtotime("-3 Months"));
        $dateto = date("Y-m-d");

        $where_op = "";
        if ($request->get('condition') != null) {
            if ($request->get('condition') == 'ng') {
                $operators = db::select("select g.operator_id, g.jml as g, ng.jml as ng, (ng.jml/g.jml) as rate from
					(select m.operator_id, sum(m.quantity) as jml from middle_check_logs m
					left join materials mt on mt.material_number = m.material_number
					where location = 'bff-kensa'
					and mt.origin_group_code = '043'
					and DATE_FORMAT(m.buffing_time,'%Y-%m-%d') = '" . date("Y-m-d") . "'
					GROUP BY m.operator_id) g
					left join
					(select m.operator_id, sum(m.quantity) as jml from middle_ng_logs m
					left join materials mt on mt.material_number = m.material_number
					where location = 'bff-kensa'
					and mt.origin_group_code = '043'
					and DATE_FORMAT(m.buffing_time,'%Y-%m-%d') = '" . date("Y-m-d") . "'
					GROUP BY m.operator_id) ng
					on ng.operator_id = g.operator_id
					order by rate desc
					limit 5");
                $operator = "";

                for ($x = 0; $x < count($operators); $x++) {
                    $operator = $operator . "'" . $operators[$x]->operator_id . "'";
                    if ($x != count($operators) - 1) {
                        $operator = $operator . ",";
                    }
                }
                $where_op = " and m.operator_id in (" . $operator . ") ";

            } else if ($request->get('condition') == 'eff') {
                $operators = db::connection('digital_kanban')->select("select l.operator_id, sum(TIMESTAMPDIFF(SECOND,l.sedang_start_time,l.selesai_start_time)) as act, sum(material_qty * t.time) as std, (sum(material_qty * t.time)/sum(TIMESTAMPDIFF(SECOND,l.sedang_start_time,l.selesai_start_time))) as eff
					from data_log l left join standard_times t on l.material_number = t.material_number
					where DATE_FORMAT(l.selesai_start_time,'%Y-%m-%d') = '" . date("Y-m-d") . "'
					GROUP BY l.operator_id
					ORDER BY eff asc
					limit 5");
                $operator = "";

                for ($x = 0; $x < count($operators); $x++) {
                    $operator = $operator . "'" . $operators[$x]->operator_id . "'";
                    if ($x != count($operators) - 1) {
                        $operator = $operator . ",";
                    }
                }
                $where_op = " and m.operator_id in (" . $operator . ") ";
            }

        } else {
            if ($request->get('operator') != null) {
                $operators = $request->get('operator');
                $operator = "";

                for ($x = 0; $x < count($operators); $x++) {
                    $operator = $operator . "'" . $operators[$x] . "'";
                    if ($x != count($operators) - 1) {
                        $operator = $operator . ",";
                    }
                }
                $where_op = " and m.operator_id in (" . $operator . ") ";
            } else {
                $where_op = " and m.operator_id is not null ";
            }
        }

        $ng_rate = db::select("select date.week_date, date.operator_id, date.`name`, (COALESCE(rate.rate,0)*100) as ng_rate  from
			(select * from (select week_date from weekly_calendars
			WHERE week_date BETWEEN '" . $datefrom . "' and '" . $dateto . "') calender
			cross join
			(select distinct m.operator_id, e.`name` from middle_check_logs m
			left join materials mt on mt.material_number = m.material_number
			left join employees e on e.employee_id = m.operator_id
			where location = 'bff-kensa' " . $where_op . "
			and mt.origin_group_code = '043'
			and DATE_FORMAT(m.buffing_time,'%Y-%m-%d') BETWEEN '" . $datefrom . "' and '" . $dateto . "') op
			order by week_date, operator_id asc) date
			left join
			(select g.tgl, g.operator_id, g.jml as g, ng.jml as ng, (ng.jml/g.jml) as rate from
			(select DATE_FORMAT(m.created_at,'%Y-%m-%d') as tgl, m.operator_id, sum(m.quantity) as jml from middle_check_logs m
			left join materials mt on mt.material_number = m.material_number
			where location = 'bff-kensa' " . $where_op . "
			and mt.origin_group_code = '043'
			and DATE_FORMAT(m.buffing_time,'%Y-%m-%d') BETWEEN '" . $datefrom . "' and '" . $dateto . "'
			GROUP BY tgl, m.operator_id) g
			left join
			(select DATE_FORMAT(m.created_at,'%Y-%m-%d') as tgl, m.operator_id, sum(m.quantity) as jml from middle_ng_logs m
			left join materials mt on mt.material_number = m.material_number
			where location = 'bff-kensa' " . $where_op . "
			and mt.origin_group_code = '043'
			and DATE_FORMAT(m.buffing_time,'%Y-%m-%d') BETWEEN '" . $datefrom . "' and '" . $dateto . "'
			GROUP BY tgl, m.operator_id) ng
			on ng.tgl = g.tgl and ng.operator_id = g.operator_id) rate
			on date.week_date = rate.tgl and date.operator_id = rate.operator_id
			ORDER BY week_date, operator_id");

        $op = db::select("select distinct m.operator_id, e.`name` from middle_check_logs m
			left join materials mt on mt.material_number = m.material_number
			left join employees e on e.employee_id = m.operator_id
			where location = 'bff-kensa'
			and m.operator_id is not null
			and mt.origin_group_code = '043'
			and DATE_FORMAT(m.created_at,'%Y-%m-%d') BETWEEN '" . $datefrom . "' and '" . $dateto . "'");

        $response = array(
            'status' => true,
            'ng_rate' => $ng_rate,
            'op' => $op,
        );
        return Response::json($response);

    }

    public function fetchBuffingOpEff(Request $request)
    {
        $date = '';
        if (strlen($request->get("tanggal")) > 0) {
            $date = date('Y-m-d', strtotime($request->get("tanggal")));
        } else {
            $date = date('Y-m-d');
        }

        $eff_target = db::table("middle_targets")->where('location', '=', 'bff')->where('target_name', '=', 'Operator Efficiency')->select('target')->first();

        $rate = db::select("select rate.shift, rate.operator_id, concat(SPLIT_STRING(e.name, ' ', 1), ' ', SPLIT_STRING(e.name, ' ', 2)) as `name`, rate.tot, rate.ng, rate.rate from
			(select c.shift, c.operator_id, c.jml as tot, COALESCE(ng.jml,0) as ng, ((c.jml-COALESCE(ng.jml,0))/c.jml) as rate from
			(select eg.`group` as shift, m.operator_id, sum(m.quantity) as jml from middle_buffing_check_logs m
			left join materials mt on mt.material_number = m.material_number
			left join employee_groups eg on eg.employee_id = m.operator_id
			where m.location = 'bff-kensa-sx'
			and m.operator_id is not null
			and mt.origin_group_code = '043'
			and DATE_FORMAT(m.buffing_time,'%Y-%m-%d') = '" . $date . "'
			GROUP BY shift, m.operator_id) c
			left join
			(select eg.`group` as shift, m.operator_id, sum(m.quantity) as jml from middle_buffing_ng_logs m
			left join materials mt on mt.material_number = m.material_number
			left join employee_groups eg on eg.employee_id = m.operator_id
			where m.location = 'bff-kensa-sx'
			and m.operator_id is not null
			and mt.origin_group_code = '043'
			and DATE_FORMAT(m.buffing_time,'%Y-%m-%d') = '" . $date . "'
			GROUP BY shift, m.operator_id) ng
			on c.shift = ng.shift and c.operator_id = ng.operator_id) rate
			left join employees e on e.employee_id = rate.operator_id
			ORDER BY shift, rate.rate desc");

        $time_eff = db::connection('digital_kanban')->select("select e.`group`, e.employee_id, dl.act, dl.std, dl.std/dl.act as eff  from employee_groups e left join
			(select l.operator_id, sum(TIMESTAMPDIFF(SECOND,l.sedang_start_time,l.selesai_start_time))/60 as act, sum((l.material_qty*t.time))/60 as std from data_log l
			left join standard_times t on l.material_number = t.material_number
			where DATE_FORMAT(l.selesai_start_time,'%Y-%m-%d') = '" . $date . "'
			GROUP BY l.operator_id) dl on dl.operator_id = e.employee_id
			WHERE e.location = 'bff'
			ORDER BY e.`group`, e.employee_id asc;");

        $emp_name = Employee::select('employee_id', db::raw('concat(SPLIT_STRING(employees.name, " ", 1), " ", SPLIT_STRING(employees.name, " ", 2)) as name'))->get();

        $response = array(
            'status' => true,
            'date' => $date,
            'rate' => $rate,
            'time_eff' => $time_eff,
            'emp_name' => $emp_name,
            'eff_target' => $eff_target->target,
        );
        return Response::json($response);

    }

    public function fetchBuffingNg(Request $request)
    {
        $date = '';
        if (strlen($request->get("tanggal")) > 0) {
            $date = date('Y-m-d', strtotime($request->get("tanggal")));
        } else {
            $date = date('Y-m-d');
        }

        $ng = db::select("select ng_name, sum(quantity) as jml
			from middle_buffing_ng_logs
			where location = 'bff-kensa-sx'
			and DATE_FORMAT(created_at,'%Y-%m-%d') = '" . $date . "'
			GROUP BY ng_name order by jml desc");

        $response = array(
            'status' => true,
            'date' => $date,
            'ng' => $ng,
        );
        return Response::json($response);
    }

    public function fetchBuffingNgRate()
    {
        $datefrom = date("Y-m-d", strtotime("-3 Months"));
        $dateto = date("Y-m-d");

        $alto = db::select("SELECT date.week_date, rate.rate FROM
			(SELECT week_date FROM weekly_calendars
			WHERE week_date BETWEEN '" . $datefrom . "' and '" . $dateto . "') date
			LEFT JOIN
			(SELECT date, ( ng / `check` * 100 ) AS rate FROM middle_buffing_daily_resumes
			WHERE date BETWEEN '" . $datefrom . "' and '" . $dateto . "'
			AND remark = 'ASKEY'
			AND location = 'bff-kensa-sx') rate
			ON rate.date = date.week_date");

        $tenor = db::select("SELECT date.week_date, rate.rate FROM
			(SELECT week_date FROM weekly_calendars
			WHERE week_date BETWEEN '" . $datefrom . "' and '" . $dateto . "') date
			LEFT JOIN
			(SELECT date, ( ng / `check` * 100 ) AS rate FROM middle_buffing_daily_resumes
			WHERE date BETWEEN '" . $datefrom . "' and '" . $dateto . "'
			AND remark = 'TSKEY'
			AND location = 'bff-kensa-sx') rate
			ON rate.date = date.week_date");

        $daily_by_ng = db::select("select ng.created_at, ng.ng_name, sum(ng) as ng, sum(result) as result, round((sum(ng)/sum(result))*100,2) as percentage from
			(
			select date(created_at) as created_at, material_number, ng_name, sum(quantity) as ng from middle_buffing_ng_logs where location = 'bff-kensa-sx' and date(created_at) >= '" . $datefrom . "' and date(created_at) <= '" . $dateto . "' group by date(created_at), material_number, ng_name
			) as ng
			left join
			(
			select date(created_at) as created_at, material_number, sum(quantity) as result from middle_buffing_logs where location = 'bff-kensa-sx' and date(created_at) >= '" . $datefrom . "' and date(created_at) <= '" . $dateto . "' group by date(created_at), material_number
			) as result
			on result.created_at = ng.created_at and result.material_number = ng.material_number where result is not null group by ng.created_at, ng.ng_name");

        $response = array(
            'status' => true,
            'alto' => $alto,
            'tenor' => $tenor,
            'daily_by_ng' => $daily_by_ng,
        );
        return Response::json($response);
    }

    public function fetchBuffingOpNgTarget(Request $request)
    {
        $tanggal = '';
        if (strlen($request->get("tanggal")) > 0) {
            $tanggal = date('Y-m-d', strtotime($request->get("tanggal")));
        } else {
            $tanggal = date('Y-m-d');
        }

        $ng_target = db::table("middle_targets")->where('location', '=', 'bff')->where('target_name', '=', 'NG Rate')->select('target')->first();

        $target = db::select("select eg.`group`, eg.employee_id, concat(SPLIT_STRING(e.name, ' ', 1), ' ', SPLIT_STRING(e.name, ' ', 2)) as `name`, ng.material_number, CONCAT(m.model,' ',m.`key`) as `key`, ng.ng_name, ng.quantity, ng.buffing_time, ng.`check` from employee_groups eg left join
			(select * from middle_buffing_ng_logs
			where location = 'bff-kensa-sx'
			and remark in
			(select remark.remark from
			(select operator_id, max(remark) as remark from middle_buffing_ng_logs
			where location = 'bff-kensa-sx'
			and date(buffing_time) = '" . $tanggal . "'
			group by operator_id) remark)) ng
			on eg.employee_id = ng.operator_id
			left join materials m on m.material_number = ng.material_number
			left join employees e on e.employee_id = eg.employee_id
			where eg.location = 'bff'
			order by eg.`group`, eg.employee_id asc");

        $operator = db::select("select * from employee_groups where location = 'bff' order by `group`, employee_id asc");

        $response = array(
            'status' => true,
            'target' => $target,
            'operator' => $operator,
            'date' => $tanggal,
            'ng_target' => $ng_target->target,
        );
        return Response::json($response);

    }

    public function fetchBuffingOpNg(Request $request)
    {
        $tanggal = '';
        if (strlen($request->get("tanggal")) > 0) {
            $tanggal = date('Y-m-d', strtotime($request->get("tanggal")));
        } else {
            $tanggal = date('Y-m-d');
        }

        $ng_target = db::table("middle_targets")->where('location', '=', 'bff')->where('target_name', '=', 'NG Rate')->select('target')->first();

        $ng_rate = db::select("select eg.`group` as shift, eg.employee_id as operator_id, concat(SPLIT_STRING(e.name, ' ', 1), ' ', SPLIT_STRING(e.name, ' ', 2)) as `name`, rate.tot, rate.ng, rate.rate from employee_groups eg
			left join
			(select c.operator_id, c.jml as tot, COALESCE(ng.jml,0) as ng, (COALESCE(ng.jml,0)/c.jml*100) as rate from
			(select m.operator_id, sum(m.quantity) as jml from middle_buffing_check_logs m
			left join materials mt on mt.material_number = m.material_number
			where m.location = 'bff-kensa-sx'
			and m.operator_id is not null
			and DATE_FORMAT(m.buffing_time,'%Y-%m-%d') = '" . $tanggal . "'
			GROUP BY m.operator_id) c
			left join
			(select m.operator_id, sum(m.quantity) as jml from middle_buffing_ng_logs m
			left join materials mt on mt.material_number = m.material_number
			where m.location = 'bff-kensa-sx'
			and m.operator_id is not null
			and DATE_FORMAT(m.buffing_time,'%Y-%m-%d') = '" . $tanggal . "'
			GROUP BY m.operator_id) ng
			on c.operator_id = ng.operator_id) rate
			on rate.operator_id = eg.employee_id
			left join employees e on e.employee_id = eg.employee_id
			where eg.location = 'bff'
			ORDER BY eg.`group`, eg.employee_id asc");

        $response = array(
            'status' => true,
            'ng_rate' => $ng_rate,
            'date' => $tanggal,
            'ng_target' => $ng_target->target,
        );
        return Response::json($response);
    }

    public function fetchDisplayMonitoring(Request $request)
    {
        $addlocation = "";
        if ($request->get('location') != null) {
            $locations = explode(",", $request->get('location'));
            $location = "";

            for ($x = 0; $x < count($locations); $x++) {
                $location = $location . "'" . $locations[$x] . "'";
                if ($x != count($locations) - 1) {
                    $location = $location . ",";
                }
            }
            $addlocation = "where location in (" . $location . ") ";
        }

        $stock = db::select("select a.diff, a.location, COALESCE(b.jml,0) as jml from
			(select diff, location from
			(select distinct location from middle_inventories) loc
			cross join
			(select distinct DATEDIFF(CURRENT_TIMESTAMP, middle_inventories.created_at) as diff from middle_inventories
			where DATEDIFF(CURRENT_TIMESTAMP, middle_inventories.created_at) <= 6
			) diff
			order by diff, location asc) a
			left join
			(select location, count(id) as jml, if(DATEDIFF(CURRENT_TIMESTAMP, middle_inventories.created_at)>=6, 6, DATEDIFF(CURRENT_TIMESTAMP, middle_inventories.created_at)) as diff from middle_inventories " . $addlocation . "
			group by location, diff order by diff, location asc) b
			on (a.diff = b.diff and a.location = b.location)
			ORDER BY a.diff, a.location");
        $loc = db::select("select distinct location from middle_inventories order by location asc");
        $diff = db::select("select distinct DATEDIFF(CURRENT_TIMESTAMP, middle_inventories.created_at) as diff from middle_inventories order by diff asc");

        $response = array(
            'status' => true,
            'stock' => $stock,
            'loc' => $loc,
            'diff' => $diff,
        );
        return Response::json($response);
    }

    public function fetchDetailStockMonitoring(Request $request)
    {

        $diff = $request->get('diff');
        if ($diff[0] == '0') {
            $diff = '= 0';
        } else if ($diff[0] == '<') {
            $diff = '= ' . substr($diff, 1, 1);
        } else {
            $diff = substr($diff, 0, 2);
        }

        $location = strtolower($request->get('loc'));

        $detail = db::select("select i.tag, i.material_number, m.material_description, i.location, i.quantity
			from middle_inventories i left join materials m
			on i.material_number = m.material_number
			where DATEDIFF(CURRENT_TIMESTAMP, i.created_at) " . $diff . "
			and location = '" . $location . "'");

        $response = array(
            'status' => true,
            'detail' => $detail,
        );
        return Response::json($response);

    }

    public function fetchDisplayKensaTime(Request $request)
    {
        $date = "";
        if (strlen($request->get('tgl')) > 0) {
            $date = date('Y-m-d', strtotime($request->get("tgl")));
        } else {
            $date = date("Y-m-d");
        }

        $location = "";
        $loc = "";
        if ($request->get('location') != null) {
            $locations = explode(",", $request->get('location'));
            $loc = $loc . "and (";
            for ($x = 0; $x < count($locations); $x++) {
                $loc = $loc . "location like '%" . $locations[$x] . "%'";
                if ($x != count($locations) - 1) {
                    $loc = $loc . " or ";
                }
            }
            $loc = $loc . ")";
        }

        $kensa_time = db::select("
			select kensa_time.employee_id, concat(SPLIT_STRING(employees.name, ' ', 1), ' ', SPLIT_STRING(employees.name, ' ', 2)) as employee_name, sum(time_min) as kensa_time from
			(select employee_id, 0 as remark, timestampdiff(second, started_at, created_at)/60 as time_min from middle_rework_logs where date(created_at) = '" . $date . "' " . $loc . "
			union all
			select employee_id, 0 as remark, timestampdiff(second, started_at, created_at)/60 as time_min from middle_logs where date(created_at) = '" . $date . "' " . $loc . "
			union all
			select employee_id, remark, timestampdiff(second, started_at, max(created_at))/60 as time_min from middle_ng_logs where date(created_at) = '" . $date . "' " . $loc . "
			group by employee_id, remark, started_at
			) as kensa_time
			left join employees on employees.employee_id = kensa_time.employee_id
			group by kensa_time.employee_id, employees.name");

        $response = array(
            'status' => true,
            'date' => $date,
            'kensa_time' => $kensa_time,
            'title' => $request->get('location'),
        );
        return Response::json($response);
    }

    public function fetchDisplayProductionResult(Request $request)
    {
        $tgl = "";
        $until = "";
        $hour = (int) date('H');

        if (strlen($request->get('tgl')) > 0) {
            if ($tgl == date('Y-m-d')) {
                if ($hour > 1) {
                    $tgl = date('Y-m-d');
                    $until = date('Y-m-d', strtotime("tomorrow"));
                } else {
                    $tgl = date('Y-m-d', strtotime("yesterday"));
                    $until = date('Y-m-d');
                }
            } else {
                $tgl = date('Y-m-d', strtotime($request->get("tgl")));
                $until = date('Y-m-d', strtotime('tomorrow', strtotime($request->get("tgl"))));
            }
        } else {
            if ($hour > 1) {
                $tgl = date('Y-m-d');
                $until = date('Y-m-d', strtotime("tomorrow"));
            } else {
                $tgl = date('Y-m-d', strtotime("yesterday"));
                $until = date('Y-m-d');
            }
        }

        $tanggal = "l.created_at >= '" . $tgl . " 01:15:01' and l.created_at <= '" . $tgl . " 07:00:00'";
        $tanggal1 = "l.created_at >= '" . $tgl . " 07:00:01' and l.created_at <= '" . $tgl . " 16:00:00'";
        $tanggal2 = "l.created_at >= '" . $tgl . " 16:00:01' and l.created_at <= '" . $until . " 01:15:00'";

        // if(strlen($request->get('tgl')) > 0){
        //     $tgl = date('Y-m-d',strtotime($request->get("tgl")));
        // }else{
        //     $tgl = date('Y-m-d');
        // }

        // $tanggal1 = "l.created_at >= '".$tgl." 07:00:01' and l.created_at <= '".$tgl." 16:00:00'";
        // $tanggal2 = "l.created_at >= '".$tgl." 16:00:01' and l.created_at <= '".$tgl." 23:59:59'";
        // $tanggal = "l.created_at >= '".$tgl." 00:00:01' and l.created_at <= '".$tgl." 07:00:00'";

        $addlocation = "and l.location in ('" . $request->get('location') . "') ";

        $tabel = '';
        if (str_contains($addlocation, 'lcq')) {
            $tabel = 'middle_lacquering_logs';
        } else if (str_contains($addlocation, 'plt')) {
            $tabel = 'middle_plating_logs';
        }

        if ($request->get('location') == 'bff') {
            $tanggal = "DATE_FORMAT(l.selesai_start_time,'%Y-%m-%d') = '" . $tgl . "' and";

            $query1 = "SELECT a.`key`, a.model, COALESCE(s3.total,0) as shift3, COALESCE(s1.total,0) as shift1, COALESCE(s2.total,0) as shift2 from
			(select distinct `key`, model, CONCAT(`key`,model) as keymodel from materials where issue_storage_location = 'SX51' and hpl = 'ASKEY' order by `key`) a
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.material_qty) as total from data_log l
			left join materials m on l.material_number = m.material_number
			WHERE " . $tanggal . " and m.hpl = 'ASKEY' and m.issue_storage_location = 'SX51'
			GROUP BY m.`key`, m.model) s3
			on a.keymodel = s3.keymodel
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.material_qty) as total from data_log l
			left join materials m on l.material_number = m.material_number
			WHERE " . $tanggal1 . " and m.hpl = 'ASKEY' and m.issue_storage_location = 'SX51'
			GROUP BY m.`key`, m.model) s1
			on a.keymodel = s1.keymodel
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.material_qty) as total from data_log l
			left join materials m on l.material_number = m.material_number
			WHERE " . $tanggal2 . " and m.hpl = 'ASKEY' and m.issue_storage_location = 'SX51'
			GROUP BY m.`key`, m.model) s2
			on a.keymodel = s2.keymodel
			ORDER BY `key`";
            $alto = db::connection('digital_kanban')->select($query1);

            $query2 = "SELECT a.`key`, a.model, COALESCE(s3.total,0) as shift3, COALESCE(s1.total,0) as shift1, COALESCE(s2.total,0) as shift2 from
			(select distinct `key`, model, CONCAT(`key`,model) as keymodel from materials where issue_storage_location = 'SX51' and hpl = 'TSKEY' order by `key`) a
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.material_qty) as total from data_log l
			left join materials m on l.material_number = m.material_number
			WHERE " . $tanggal . " and m.hpl = 'TSKEY' and m.issue_storage_location = 'SX51'
			GROUP BY m.`key`, m.model) s3
			on a.keymodel = s3.keymodel
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.material_qty) as total from data_log l
			left join materials m on l.material_number = m.material_number
			WHERE " . $tanggal1 . " and m.hpl = 'TSKEY' and m.issue_storage_location = 'SX51'
			GROUP BY m.`key`, m.model) s1
			on a.keymodel = s1.keymodel
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.material_qty) as total from data_log l
			left join materials m on l.material_number = m.material_number
			WHERE " . $tanggal2 . " and m.hpl = 'TSKEY' and m.issue_storage_location = 'SX51'
			GROUP BY m.`key`, m.model) s2
			on a.keymodel = s2.keymodel
			ORDER BY `key`";
            $tenor = db::connection('digital_kanban')->select($query2);
        } else {
            $query1 = "SELECT a.`key`, a.model, COALESCE(s3.total,0) as shift3, COALESCE(s1.total,0) as shift1, COALESCE(s2.total,0) as shift2 from
			(select distinct `key`, model, CONCAT(`key`,model) as keymodel from materials where issue_storage_location = 'SX51' and hpl = 'ASKEY' order by `key`) a
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from " . $tabel . " l
			left join materials m on l.material_number = m.material_number
			WHERE " . $tanggal . " and m.hpl = 'ASKEY' and m.issue_storage_location = 'SX51' " . $addlocation . "
			GROUP BY m.`key`, m.model) s3
			on a.keymodel = s3.keymodel
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from " . $tabel . " l
			left join materials m on l.material_number = m.material_number
			WHERE " . $tanggal1 . " and m.hpl = 'ASKEY' and m.issue_storage_location = 'SX51' " . $addlocation . "
			GROUP BY m.`key`, m.model) s1
			on a.keymodel = s1.keymodel
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from " . $tabel . " l
			left join materials m on l.material_number = m.material_number
			WHERE " . $tanggal2 . " and m.hpl = 'ASKEY' and m.issue_storage_location = 'SX51' " . $addlocation . "
			GROUP BY m.`key`, m.model) s2
			on a.keymodel = s2.keymodel
			ORDER BY `key`";
            $alto = db::select($query1);

            $query2 = "SELECT a.`key`, a.model, COALESCE(s3.total,0) as shift3, COALESCE(s1.total,0) as shift1, COALESCE(s2.total,0) as shift2 from
			(select distinct `key`, model, CONCAT(`key`,model) as keymodel from materials where issue_storage_location = 'SX51' and hpl = 'TSKEY' order by `key`) a
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from " . $tabel . " l
			left join materials m on l.material_number = m.material_number
			WHERE " . $tanggal . " and m.hpl = 'TSKEY' and m.issue_storage_location = 'SX51' " . $addlocation . "
			GROUP BY m.`key`, m.model) s3
			on a.keymodel = s3.keymodel
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from " . $tabel . " l
			left join materials m on l.material_number = m.material_number
			WHERE " . $tanggal1 . " and m.hpl = 'TSKEY' and m.issue_storage_location = 'SX51' " . $addlocation . "
			GROUP BY m.`key`, m.model) s1
			on a.keymodel = s1.keymodel
			left join
			(select m.`key`, m.model, CONCAT(`key`,model) as keymodel, sum(l.quantity) as total from " . $tabel . " l
			left join materials m on l.material_number = m.material_number
			WHERE " . $tanggal2 . " and m.hpl = 'TSKEY' and m.issue_storage_location = 'SX51' " . $addlocation . "
			GROUP BY m.`key`, m.model) s2
			on a.keymodel = s2.keymodel
			ORDER BY `key`";
            $tenor = db::select($query2);
        }

        $query3 = "select distinct `key` from materials where hpl = 'ASKEY' and issue_storage_location = 'SX51' and surface not like '%PLT%' order by `key`";
        $key = db::select($query3);

        $query4 = "select distinct model from materials where hpl = 'ASKEY' and issue_storage_location = 'SX51' and surface not like '%PLT%' order by model";
        $model_alto = db::select($query4);

        $query5 = "select distinct model from materials where hpl = 'TSKEY' and issue_storage_location = 'SX51' and surface not like '%PLT%' order by model";
        $model_tenor = db::select($query5);

        $response = array(
            'status' => true,
            'alto' => $alto,
            'tenor' => $tenor,
            'key' => $key,
            'model_tenor' => $model_tenor,
            'model_alto' => $model_alto,
            'title' => strtoupper($request->get('location')),
        );
        return Response::json($response);

    }

    public function fetchReportHourlyLcq(Request $request)
    {
        $tanggal = '';
        if (strlen($request->get('date')) > 0) {
            $date = date('Y-m-d', strtotime($request->get('date')));
            $tanggal = "DATE_FORMAT(l.created_at,'%Y-%m-%d') = '" . $date . "' and ";
        } else {
            $date = date('Y-m-d');
            $tanggal = "DATE_FORMAT(l.created_at,'%Y-%m-%d') = '" . $date . "' and ";
        }

        $addlocation = "";
        if ($request->get('location') != null) {
            $locations = $request->get('location');
            $location = "";

            for ($x = 0; $x < count($locations); $x++) {
                $location = $location . "'" . $locations[$x] . "'";
                if ($x != count($locations) - 1) {
                    $location = $location . ",";
                }
            }
            $addlocation = "and l.location in (" . $location . ") ";
        }

        $key = db::select("select DISTINCT SUBSTRING(`key`, 1, 1) as kunci from materials where hpl = 'ASKEY' and surface not like '%PLT%' and issue_storage_location = 'SX51' ORDER BY `key` asc");

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
            "DATE_FORMAT(l.created_at,'%H:%m:%s') >= '22:00:00' and DATE_FORMAT(l.created_at,'%H:%m:%s') < '23:59:59'",
        ];

        $dataShift3 = [];
        $dataShift1 = [];
        $dataShift2 = [];

        $z3 = [];
        $z1 = [];
        $z2 = [];

        $push_data = [];
        $push_data_z = [];

        for ($i = 0; $i <= 3; $i++) {
            $push_data[$i] = db::select("(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
				from middle_logs l left join materials m on l.material_number = m.material_number
				where " . $tanggal . " " . $jam[$i] . " " . $addlocation . "
				and m.hpl = 'ASKEY' and m.model != 'A82Z'
				GROUP BY tgl, kunci, m.hpl
				ORDER BY kunci)
				union
				(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
				from middle_logs l left join materials m on l.material_number = m.material_number
				where " . $tanggal . " " . $jam[$i] . " " . $addlocation . "
				and m.hpl = 'TSKEY' and m.model != 'A82Z'
				GROUP BY tgl, kunci, m.hpl
				ORDER BY kunci)");
            array_push($dataShift3, $push_data[$i]);

            $push_data_z[$i] = db::select("select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, m.model, sum(l.quantity) as jml
				from middle_logs l left join materials m on l.material_number = m.material_number
				where  " . $tanggal . " " . $jam[$i] . " and m.model = 'A82Z' " . $addlocation . "
				GROUP BY tgl, m.model");
            array_push($z3, $push_data_z[$i]);
        }

        for ($i = 4; $i <= 7; $i++) {
            $push_data[$i] = db::select("(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
				from middle_logs l left join materials m on l.material_number = m.material_number
				where " . $tanggal . " " . $jam[$i] . " " . $addlocation . "
				and m.hpl = 'ASKEY' and m.model != 'A82Z'
				GROUP BY tgl, kunci, m.hpl
				ORDER BY kunci)
				union
				(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
				from middle_logs l left join materials m on l.material_number = m.material_number
				where " . $tanggal . " " . $jam[$i] . " " . $addlocation . "
				and m.hpl = 'TSKEY' and m.model != 'A82Z'
				GROUP BY tgl, kunci, m.hpl
				ORDER BY kunci)");
            array_push($dataShift1, $push_data[$i]);

            $push_data_z[$i] = db::select("select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, m.model, sum(l.quantity) as jml
				from middle_logs l left join materials m on l.material_number = m.material_number
				where  " . $tanggal . " " . $jam[$i] . " and m.model = 'A82Z' " . $addlocation . "
				GROUP BY tgl, m.model");
            array_push($z1, $push_data_z[$i]);
        }

        for ($i = 8; $i <= 11; $i++) {
            $push_data[$i] = db::select("(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
				from middle_logs l left join materials m on l.material_number = m.material_number
				where " . $tanggal . " " . $jam[$i] . " " . $addlocation . "
				and m.hpl = 'ASKEY' and m.model != 'A82Z'
				GROUP BY tgl, kunci, m.hpl
				ORDER BY kunci)
				union
				(select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, SUBSTRING(`key`, 1, 1) as kunci, m.hpl, sum(l.quantity) as jml
				from middle_logs l left join materials m on l.material_number = m.material_number
				where " . $tanggal . " " . $jam[$i] . " " . $addlocation . "
				and m.hpl = 'TSKEY' and m.model != 'A82Z'
				GROUP BY tgl, kunci, m.hpl
				ORDER BY kunci)");
            array_push($dataShift2, $push_data[$i]);

            $push_data_z[$i] = db::select("select DATE_FORMAT(l.created_at,'%Y-%m-%d') as tgl, m.model, sum(l.quantity) as jml
				from middle_logs l left join materials m on l.material_number = m.material_number
				where  " . $tanggal . " " . $jam[$i] . " and m.model = 'A82Z' " . $addlocation . "
				GROUP BY tgl, m.model");
            array_push($z2, $push_data_z[$i]);
        }

        $tanggal = substr($tanggal, 40, 10);

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

    public function fetchLcqNgRateWeekly(Request $request)
    {
        $bulan = "";
        $bulanText = "";
        if (strlen($request->get('bulan')) > 0) {
            $bulan = date('Y-m', strtotime('01-' . $request->get('bulan')));
            $bulanText = date('M Y', strtotime('01-' . $request->get('bulan')));
        } else {
            $bulan = date('Y-m');
            $bulanText = date('M Y');
        }

        $weekly_ic = db::select("SELECT tgl.week_name, tgl.min, COALESCE (resume.ng, 0) AS ng, COALESCE (resume.g, 0) AS g, (resume.ng / resume.g) AS ng_rate FROM
			(SELECT week_name AS week_name, MIN(week_date) AS min FROM weekly_calendars
			WHERE DATE_FORMAT(week_date, '%Y-%m') = '" . $bulan . "'
			GROUP BY week_name
			ORDER BY min ASC) tgl
			LEFT JOIN
			(SELECT `month`, `week`, ng, `check` as g FROM middle_lacquering_weekly_resumes
			WHERE `month` = '" . $bulan . "'
			AND location = 'lcq-incoming') resume
			ON tgl.week_name = resume.`week`
			ORDER BY tgl.min ASC");

        $weekly_kensa = db::select("SELECT tgl.week_name, tgl.min, COALESCE (resume.ng, 0) AS ng, COALESCE (resume.g, 0) AS g, (resume.ng / resume.g) AS ng_rate FROM
			(SELECT week_name AS week_name, MIN(week_date) AS min FROM weekly_calendars
			WHERE DATE_FORMAT(week_date, '%Y-%m') = '" . $bulan . "'
			GROUP BY week_name
			ORDER BY min ASC) tgl
			LEFT JOIN
			(SELECT `month`, `week`, ng, `check` as g FROM middle_lacquering_weekly_resumes
			WHERE `month` = '" . $bulan . "'
			AND location = 'lcq-kensa') resume
			ON tgl.week_name = resume.`week`
			ORDER BY tgl.min ASC");

        $response = array(
            'status' => true,
            'weekly_ic' => $weekly_ic,
            'weekly_kensa' => $weekly_kensa,
            'bulanText' => $bulanText,
        );
        return Response::json($response);
    }

    public function fetchPltNgRateWeekly(Request $request, $id)
    {
        $incoming = '';
        $kensa = '';

        if ($id == 'sax') {
            $incoming = 'plt-incoming-sx';
            $kensa = 'plt-kensa-sx';
        } elseif ($id == 'fl') {
            $incoming = 'plt-incoming-fl';
            $kensa = 'plt-kensa-fl';
        } elseif ($id == 'cl') {
            $incoming = 'plt-incoming-cl';
            $kensa = 'plt-kensa-cl';
        }

        $bulan = "";
        $bulanText = "";
        if (strlen($request->get('bulan')) > 0) {
            $bulan = date('Y-m', strtotime('01-' . $request->get('bulan')));
            $bulanText = date('M Y', strtotime('01-' . $request->get('bulan')));
        } else {
            $bulan = date('Y-m');
            $bulanText = date('M Y');
        }

        $weekly_ic = db::select("SELECT tgl.week_name, tgl.min, COALESCE (resume.ng, 0) AS ng, COALESCE (resume.g, 0) AS g, (resume.ng / resume.g) AS ng_rate FROM
			(SELECT week_name AS week_name, MIN(week_date) AS min FROM weekly_calendars
			WHERE DATE_FORMAT(week_date, '%Y-%m') = '" . $bulan . "'
			GROUP BY week_name
			ORDER BY min ASC) tgl
			LEFT JOIN
			(SELECT `month`, `week`, ng, `check` as g FROM middle_plating_weekly_resumes
			WHERE `month` = '" . $bulan . "'
			AND location = '" . $incoming . "') resume
			ON tgl.week_name = resume.`week`
			ORDER BY tgl.min ASC");

        $weekly_kensa = db::select("SELECT tgl.week_name, tgl.min, COALESCE (resume.ng, 0) AS ng, COALESCE (resume.g, 0) AS g, (resume.ng / resume.g) AS ng_rate FROM
			(SELECT week_name AS week_name, MIN(week_date) AS min FROM weekly_calendars
			WHERE DATE_FORMAT(week_date, '%Y-%m') = '" . $bulan . "'
			GROUP BY week_name
			ORDER BY min ASC) tgl
			LEFT JOIN
			(SELECT `month`, `week`, ng, `check` as g FROM middle_plating_weekly_resumes
			WHERE `month` = '" . $bulan . "'
			AND location = '" . $kensa . "') resume
			ON tgl.week_name = resume.`week`
			ORDER BY tgl.min ASC");

        $response = array(
            'status' => true,
            'weekly_ic' => $weekly_ic,
            'weekly_kensa' => $weekly_kensa,
            'bulanText' => $bulanText,
        );
        return Response::json($response);
    }

    public function fetchLcqNgRateMonthly(Request $request)
    {
        $fy = '';
        if ($request->get('fy') != null) {
            $fys = explode(",", $request->get('fy'));
            for ($i = 0; $i < count($fys); $i++) {
                $fy = $fy . "'" . $fys[$i] . "'";
                if ($i != (count($fys) - 1)) {
                    $fy = $fy . ',';
                }
            }
        } else {
            $get_fy = db::select("select fiscal_year from weekly_calendars where week_date = DATE_FORMAT(now(),'%Y-%m-%d')");
            foreach ($get_fy as $key) {
                $fy = "'" . $key->fiscal_year . "'";
            }
        }

        $monthly_ic = db::select("SELECT tgl.tgl, COALESCE (resume.ng, 0) AS ng, COALESCE (resume.g, 0) AS g, (resume.ng / resume.g) AS ng_rate FROM
			(SELECT DATE_FORMAT( week_date, '%Y-%m' ) AS tgl FROM weekly_calendars
			WHERE fiscal_year IN (" . $fy . ")
			GROUP BY tgl) tgl
			LEFT JOIN
			(SELECT `month` AS tgl, ng AS ng, `check` AS g FROM middle_lacquering_monthly_resumes
			WHERE fiscal_year IN (" . $fy . ")
			AND location = 'lcq-incoming') resume
			ON tgl.tgl = resume.tgl
			ORDER BY tgl.tgl ASC");

        $monthly_kensa = db::select("SELECT tgl.tgl, COALESCE (resume.ng, 0) AS ng, COALESCE (resume.g, 0) AS g, (resume.ng / resume.g) AS ng_rate FROM
			(SELECT DATE_FORMAT( week_date, '%Y-%m' ) AS tgl FROM weekly_calendars
			WHERE fiscal_year IN (" . $fy . ")
			GROUP BY tgl) tgl
			LEFT JOIN
			(SELECT `month` AS tgl, ng AS ng, `check` AS g FROM middle_lacquering_monthly_resumes
			WHERE fiscal_year IN (" . $fy . ")
			AND location = 'lcq-kensa') resume
			ON tgl.tgl = resume.tgl
			ORDER BY tgl.tgl ASC");

        $fy = "";
        if ($request->get('fy') != null) {
            $fys = explode(",", $request->get('fy'));
            for ($x = 0; $x < count($fys); $x++) {
                $fy = $fy . " " . $fys[$x] . " ";
                if ($x != count($fys) - 1) {
                    $fy = $fy . "&";
                }
            }
        } else {
            $get_fy = db::select("select fiscal_year from weekly_calendars where week_date = DATE_FORMAT(now(),'%Y-%m-%d')");
            foreach ($get_fy as $key) {
                $fy = $key->fiscal_year;
            }
        }

        $target_ic = db::table('middle_targets')
            ->where('target_name', 'NG Rate Incoming Check')
            ->where('location', '=', 'lcq')
            ->first();

        $target_kensa = db::table('middle_targets')
            ->where('target_name', 'NG Rate Kensa')
            ->where('location', '=', 'lcq')
            ->first();

        $response = array(
            'status' => true,
            'monthly_ic' => $monthly_ic,
            'monthly_kensa' => $monthly_kensa,
            'target_ic' => $target_ic->target,
            'target_kensa' => $target_kensa->target,
            'fy' => $fy,
        );
        return Response::json($response);
    }

    public function fetchPltNgRateMonthly(Request $request, $id)
    {
        $incoming = '';
        $kensa = '';

        if ($id == 'sax') {
            $incoming = 'plt-incoming-sx';
            $kensa = 'plt-kensa-sx';
        } elseif ($id == 'fl') {
            $incoming = 'plt-incoming-fl';
            $kensa = 'plt-kensa-fl';
        } elseif ($id == 'cl') {
            $incoming = 'plt-incoming-cl';
            $kensa = 'plt-kensa-cl';
        }

        $fy = '';
        if ($request->get('fy') != null) {
            $fys = explode(",", $request->get('fy'));
            for ($i = 0; $i < count($fys); $i++) {
                $fy = $fy . "'" . $fys[$i] . "'";
                if ($i != (count($fys) - 1)) {
                    $fy = $fy . ',';
                }
            }
        } else {
            $get_fy = db::select("select fiscal_year from weekly_calendars where week_date = DATE_FORMAT(now(),'%Y-%m-%d')");
            foreach ($get_fy as $key) {
                $fy = "'" . $key->fiscal_year . "'";
            }
        }

        $monthly_ic = db::select("SELECT tgl.tgl, COALESCE (resume.ng, 0) AS ng, COALESCE (resume.g, 0) AS g, (resume.ng / resume.g) AS ng_rate FROM
			(SELECT DATE_FORMAT( week_date, '%Y-%m' ) AS tgl FROM weekly_calendars
			WHERE fiscal_year IN (" . $fy . ")
			GROUP BY tgl) tgl
			LEFT JOIN
			(SELECT `month` AS tgl, ng AS ng, `check` AS g FROM middle_plating_monthly_resumes
			WHERE fiscal_year IN (" . $fy . ")
			AND location = '" . $incoming . "') resume
			ON tgl.tgl = resume.tgl
			ORDER BY tgl.tgl ASC");

        $monthly_kensa = db::select("SELECT tgl.tgl, COALESCE (resume.ng, 0) AS ng, COALESCE (resume.g, 0) AS g, (resume.ng / resume.g) AS ng_rate FROM
			(SELECT DATE_FORMAT( week_date, '%Y-%m' ) AS tgl FROM weekly_calendars
			WHERE fiscal_year IN (" . $fy . ")
			GROUP BY tgl) tgl
			LEFT JOIN
			(SELECT `month` AS tgl, ng AS ng, `check` AS g FROM middle_plating_monthly_resumes
			WHERE fiscal_year IN (" . $fy . ")
			AND location = '" . $kensa . "') resume
			ON tgl.tgl = resume.tgl
			ORDER BY tgl.tgl ASC");

        $fy = "";
        if ($request->get('fy') != null) {
            $fys = explode(",", $request->get('fy'));
            for ($x = 0; $x < count($fys); $x++) {
                $fy = $fy . " " . $fys[$x] . " ";
                if ($x != count($fys) - 1) {
                    $fy = $fy . "&";
                }
            }
        } else {
            $get_fy = db::select("select fiscal_year from weekly_calendars where week_date = DATE_FORMAT(now(),'%Y-%m-%d')");
            foreach ($get_fy as $key) {
                $fy = $key->fiscal_year;
            }
        }

        $target_ic = db::table('middle_targets')
            ->where('target_name', 'NG Rate Incoming Check')
            ->where('location', '=', 'plt')
            ->first();

        $target_kensa = db::table('middle_targets')
            ->where('target_name', 'NG Rate Kensa')
            ->where('location', '=', 'plt')
            ->first();

        $response = array(
            'status' => true,
            'monthly_ic' => $monthly_ic,
            'target_ic' => $target_ic->target,
            'monthly_kensa' => $monthly_kensa,
            'target_kensa' => $target_kensa->target,
            'fy' => $fy,
        );
        return Response::json($response);
    }

    public function fetchLcqNg(Request $request)
    {

        $bulan = "";
        $bulanText = "";
        if (strlen($request->get('bulan')) > 0) {
            $bulan = date('Y-m', strtotime('01-' . $request->get('bulan')));
            $bulanText = date('M Y', strtotime('01-' . $request->get('bulan')));
        } else {
            $bulan = date('Y-m');
            $bulanText = date('M Y');
        }

        //Highest NG
        $ic_ng_alto = db::select("SELECT ng.hpl, ng.ng_name, ng.ng, cek.`check` FROM
			(SELECT hpl, ng_name, SUM(ng) as ng FROM middle_lacquering_monthly_ng_resumes
			WHERE hpl = 'ASKEY'
			AND `month` = '" . $bulan . "'
			AND location = 'lcq-incoming'
			GROUP BY hpl, ng_name) AS ng
			LEFT JOIN
			(SELECT remark, SUM(`check`) as `check` FROM middle_lacquering_daily_resumes
			WHERE remark = 'ASKEY'
			AND DATE_FORMAT(date,'%Y-%m') = '" . $bulan . "'
			AND location = 'lcq-incoming'
			GROUP BY remark) AS cek
			ON cek.remark = ng.hpl
			ORDER BY ng.ng DESC");

        $ic_ng_tenor = db::select("SELECT ng.hpl, ng.ng_name, ng.ng, cek.`check` FROM
			(SELECT hpl, ng_name, SUM(ng) as ng FROM middle_lacquering_monthly_ng_resumes
			WHERE hpl = 'TSKEY'
			AND `month` = '" . $bulan . "'
			AND location = 'lcq-incoming'
			GROUP BY hpl, ng_name) AS ng
			LEFT JOIN
			(SELECT remark, SUM(`check`) as `check` FROM middle_lacquering_daily_resumes
			WHERE remark = 'TSKEY'
			AND DATE_FORMAT(date,'%Y-%m') = '" . $bulan . "'
			AND location = 'lcq-incoming'
			GROUP BY remark) AS cek
			ON cek.remark = ng.hpl
			ORDER BY ng.ng DESC");

        $kensa_ng_alto = db::select("SELECT ng.hpl, ng.ng_name, ng.ng, cek.`check` FROM
			(SELECT hpl, ng_name, SUM(ng) as ng FROM middle_lacquering_monthly_ng_resumes
			WHERE hpl = 'ASKEY'
			AND `month` = '" . $bulan . "'
			AND location = 'lcq-kensa'
			GROUP BY hpl, ng_name) AS ng
			LEFT JOIN
			(SELECT remark, SUM(`check`) as `check` FROM middle_lacquering_daily_resumes
			WHERE remark = 'ASKEY'
			AND DATE_FORMAT(date,'%Y-%m') = '" . $bulan . "'
			AND location = 'lcq-kensa'
			GROUP BY remark) AS cek
			ON cek.remark = ng.hpl
			ORDER BY ng.ng DESC");

        $kensa_ng_tenor = db::select("SELECT ng.hpl, ng.ng_name, ng.ng, cek.`check` FROM
			(SELECT hpl, ng_name, SUM(ng) as ng FROM middle_lacquering_monthly_ng_resumes
			WHERE hpl = 'TSKEY'
			AND `month` = '" . $bulan . "'
			AND location = 'lcq-kensa'
			GROUP BY hpl, ng_name) AS ng
			LEFT JOIN
			(SELECT remark, SUM(`check`) as `check` FROM middle_lacquering_daily_resumes
			WHERE remark = 'TSKEY'
			AND DATE_FORMAT(date,'%Y-%m') = '" . $bulan . "'
			AND location = 'lcq-kensa'
			GROUP BY remark) AS cek
			ON cek.remark = ng.hpl
			ORDER BY ng.ng DESC");

        //Highest Key
        $ic_ng_key_alto = db::select("SELECT hpl, `key`, sum(ng) AS ng FROM middle_lacquering_monthly_ng_resumes
			WHERE hpl = 'ASKEY'
			AND location = 'lcq-incoming'
			AND `month` = '" . $bulan . "'
			GROUP BY hpl, `key`
			ORDER BY ng DESC
			LIMIT 10");

        $ic_ng_key_tenor = db::select("SELECT hpl, `key`, sum(ng) AS ng FROM middle_lacquering_monthly_ng_resumes
			WHERE hpl = 'TSKEY'
			AND location = 'lcq-incoming'
			AND `month` = '" . $bulan . "'
			GROUP BY hpl, `key`
			ORDER BY ng DESC
			LIMIT 10");

        $ic_ng_key_alto_detail = db::select("SELECT hpl, `key`, ng_name, ng FROM middle_lacquering_monthly_ng_resumes
			WHERE hpl = 'ASKEY'
			AND location = 'lcq-incoming'
			AND `month` = '" . $bulan . "'");

        $ic_ng_key_tenor_detail = db::select("SELECT hpl, `key`, ng_name, ng FROM middle_lacquering_monthly_ng_resumes
			WHERE hpl = 'TSKEY'
			AND location = 'lcq-incoming'
			AND `month` = '" . $bulan . "'");

        $kensa_ng_key_alto = db::select("SELECT hpl, `key`, sum(ng) AS ng FROM middle_lacquering_monthly_ng_resumes
			WHERE hpl = 'ASKEY'
			AND location = 'lcq-kensa'
			AND `month` = '" . $bulan . "'
			GROUP BY hpl, `key`
			ORDER BY ng DESC
			LIMIT 10");

        $kensa_ng_key_tenor = db::select("SELECT hpl, `key`, sum(ng) AS ng FROM middle_lacquering_monthly_ng_resumes
			WHERE hpl = 'TSKEY'
			AND location = 'lcq-kensa'
			AND `month` = '" . $bulan . "'
			GROUP BY hpl, `key`
			ORDER BY ng DESC
			LIMIT 10");

        $kensa_ng_key_alto_detail = db::select("SELECT hpl, `key`, ng_name, ng FROM middle_lacquering_monthly_ng_resumes
			WHERE hpl = 'ASKEY'
			AND location = 'lcq-kensa'
			AND `month` = '" . $bulan . "'");

        $kensa_ng_key_tenor_detail = db::select("SELECT hpl, `key`, ng_name, ng FROM middle_lacquering_monthly_ng_resumes
			WHERE hpl = 'TSKEY'
			AND location = 'lcq-kensa'
			AND `month` = '" . $bulan . "'");

        $response = array(
            'status' => true,

            'ic_ng_alto' => $ic_ng_alto,
            'ic_ng_tenor' => $ic_ng_tenor,
            'kensa_ng_alto' => $kensa_ng_alto,
            'kensa_ng_tenor' => $kensa_ng_tenor,

            'ic_ng_key_alto' => $ic_ng_key_alto,
            'ic_ng_key_alto_detail' => $ic_ng_key_alto_detail,
            'ic_ng_key_tenor' => $ic_ng_key_tenor,
            'ic_ng_key_tenor_detail' => $ic_ng_key_tenor_detail,

            'kensa_ng_key_alto' => $kensa_ng_key_alto,
            'kensa_ng_key_alto_detail' => $kensa_ng_key_alto_detail,
            'kensa_ng_key_tenor' => $kensa_ng_key_tenor,
            'kensa_ng_key_tenor_detail' => $kensa_ng_key_tenor_detail,

            'bulanText' => $bulanText,
        );
        return Response::json($response);
    }

    public function fetchPltNg(Request $request, $id)
    {
        $incoming = '';
        $kensa = '';

        if ($id == 'sax') {
            $incoming = 'plt-incoming-sx';
            $kensa = 'plt-kensa-sx';
        } elseif ($id == 'fl') {
            $incoming = 'plt-incoming-fl';
            $kensa = 'plt-kensa-fl';
        } elseif ($id == 'cl') {
            $incoming = 'plt-incoming-cl';
            $kensa = 'plt-kensa-cl';
        }

        $bulan = "";
        $bulanText = "";
        if (strlen($request->get('bulan')) > 0) {
            $bulan = date('Y-m', strtotime('01-' . $request->get('bulan')));
            $bulanText = date('M Y', strtotime('01-' . $request->get('bulan')));
        } else {
            $bulan = date('Y-m');
            $bulanText = date('M Y');
        }

        //Highest NG
        $ic_ng_alto = db::select("SELECT ng.hpl, ng.ng_name, ng.ng, cek.`check` FROM
			(SELECT hpl, ng_name, SUM(ng) as ng FROM middle_plating_monthly_ng_resumes
			WHERE hpl = 'ASKEY'
			AND `month` = '" . $bulan . "'
			AND location = '" . $incoming . "'
			GROUP BY hpl, ng_name) AS ng
			LEFT JOIN
			(SELECT remark, SUM(`check`) as `check` FROM middle_plating_daily_resumes
			WHERE remark = 'ASKEY'
			AND DATE_FORMAT(date,'%Y-%m') = '" . $bulan . "'
			AND location = '" . $incoming . "'
			GROUP BY remark) AS cek
			ON cek.remark = ng.hpl
			ORDER BY ng.ng DESC");

        $ic_ng_tenor = db::select("SELECT ng.hpl, ng.ng_name, ng.ng, cek.`check` FROM
			(SELECT hpl, ng_name, SUM(ng) as ng FROM middle_plating_monthly_ng_resumes
			WHERE hpl = 'TSKEY'
			AND `month` = '" . $bulan . "'
			AND location = '" . $incoming . "'
			GROUP BY hpl, ng_name) AS ng
			LEFT JOIN
			(SELECT remark, SUM(`check`) as `check` FROM middle_plating_daily_resumes
			WHERE remark = 'TSKEY'
			AND DATE_FORMAT(date,'%Y-%m') = '" . $bulan . "'
			AND location = '" . $incoming . "'
			GROUP BY remark) AS cek
			ON cek.remark = ng.hpl
			ORDER BY ng.ng DESC");

        $kensa_ng_alto = db::select("SELECT ng.hpl, ng.ng_name, ng.ng, cek.`check` FROM
			(SELECT hpl, ng_name, SUM(ng) as ng FROM middle_plating_monthly_ng_resumes
			WHERE hpl = 'ASKEY'
			AND `month` = '" . $bulan . "'
			AND location = '" . $kensa . "'
			GROUP BY hpl, ng_name) AS ng
			LEFT JOIN
			(SELECT remark, SUM(`check`) as `check` FROM middle_plating_daily_resumes
			WHERE remark = 'ASKEY'
			AND DATE_FORMAT(date,'%Y-%m') = '" . $bulan . "'
			AND location = '" . $kensa . "'
			GROUP BY remark) AS cek
			ON cek.remark = ng.hpl
			ORDER BY ng.ng DESC");

        $kensa_ng_tenor = db::select("SELECT ng.hpl, ng.ng_name, ng.ng, cek.`check` FROM
			(SELECT hpl, ng_name, SUM(ng) as ng FROM middle_plating_monthly_ng_resumes
			WHERE hpl = 'TSKEY'
			AND `month` = '" . $bulan . "'
			AND location = '" . $kensa . "'
			GROUP BY hpl, ng_name) AS ng
			LEFT JOIN
			(SELECT remark, SUM(`check`) as `check` FROM middle_plating_daily_resumes
			WHERE remark = 'TSKEY'
			AND DATE_FORMAT(date,'%Y-%m') = '" . $bulan . "'
			AND location = '" . $kensa . "'
			GROUP BY remark) AS cek
			ON cek.remark = ng.hpl
			ORDER BY ng.ng DESC");

        //Highest Key
        $ic_ng_key_alto = db::select("SELECT hpl, `key`, sum(ng) AS ng FROM middle_plating_monthly_ng_resumes
			WHERE hpl = 'ASKEY'
			AND location = '" . $incoming . "'
			AND `month` = '" . $bulan . "'
			GROUP BY hpl, `key`
			ORDER BY ng DESC
			LIMIT 10");

        $ic_ng_key_tenor = db::select("SELECT hpl, `key`, sum(ng) AS ng FROM middle_plating_monthly_ng_resumes
			WHERE hpl = 'TSKEY'
			AND location = '" . $incoming . "'
			AND `month` = '" . $bulan . "'
			GROUP BY hpl, `key`
			ORDER BY ng DESC
			LIMIT 10");

        $ic_ng_key_alto_detail = db::select("SELECT hpl, `key`, ng_name, ng FROM middle_plating_monthly_ng_resumes
			WHERE hpl = 'ASKEY'
			AND location = '" . $incoming . "'
			AND `month` = '" . $bulan . "'");

        $ic_ng_key_tenor_detail = db::select("SELECT hpl, `key`, ng_name, ng FROM middle_plating_monthly_ng_resumes
			WHERE hpl = 'TSKEY'
			AND location = '" . $incoming . "'
			AND `month` = '" . $bulan . "'");

        $kensa_ng_key_alto = db::select("SELECT hpl, `key`, sum(ng) AS ng FROM middle_plating_monthly_ng_resumes
			WHERE hpl = 'ASKEY'
			AND location = '" . $kensa . "'
			AND `month` = '" . $bulan . "'
			GROUP BY hpl, `key`
			ORDER BY ng DESC
			LIMIT 10");

        $kensa_ng_key_tenor = db::select("SELECT hpl, `key`, sum(ng) AS ng FROM middle_plating_monthly_ng_resumes
			WHERE hpl = 'TSKEY'
			AND location = '" . $kensa . "'
			AND `month` = '" . $bulan . "'
			GROUP BY hpl, `key`
			ORDER BY ng DESC
			LIMIT 10");

        $kensa_ng_key_alto_detail = db::select("SELECT hpl, `key`, ng_name, ng FROM middle_plating_monthly_ng_resumes
			WHERE hpl = 'ASKEY'
			AND location = '" . $kensa . "'
			AND `month` = '" . $bulan . "'");

        $kensa_ng_key_tenor_detail = db::select("SELECT hpl, `key`, ng_name, ng FROM middle_plating_monthly_ng_resumes
			WHERE hpl = 'TSKEY'
			AND location = '" . $kensa . "'
			AND `month` = '" . $bulan . "'");

        $response = array(
            'status' => true,

            'ic_ng_alto' => $ic_ng_alto,
            'ic_ng_tenor' => $ic_ng_tenor,
            'kensa_ng_alto' => $kensa_ng_alto,
            'kensa_ng_tenor' => $kensa_ng_tenor,

            'ic_ng_key_alto' => $ic_ng_key_alto,
            'ic_ng_key_alto_detail' => $ic_ng_key_alto_detail,
            'ic_ng_key_tenor' => $ic_ng_key_tenor,
            'ic_ng_key_tenor_detail' => $ic_ng_key_tenor_detail,

            'kensa_ng_key_alto' => $kensa_ng_key_alto,
            'kensa_ng_key_alto_detail' => $kensa_ng_key_alto_detail,
            'kensa_ng_key_tenor' => $kensa_ng_key_tenor,
            'kensa_ng_key_tenor_detail' => $kensa_ng_key_tenor_detail,

            'bulanText' => $bulanText,
        );
        return Response::json($response);
    }

    public function fetchPltNgClarinet(Request $request)
    {
        $incoming = 'plt-incoming-cl';
        $kensa = 'plt-kensa-cl';

        $bulan = "";
        $bulanText = "";
        if (strlen($request->get('bulan')) > 0) {
            $bulan = date('Y-m', strtotime('01-' . $request->get('bulan')));
            $bulanText = date('M Y', strtotime('01-' . $request->get('bulan')));
        } else {
            $bulan = date('Y-m');
            $bulanText = date('M Y');
        }

        //Highest NG
        $ic_ng = db::select("SELECT ng.hpl, ng.ng_name, ng.ng, cek.`check` FROM
            (SELECT hpl, ng_name, SUM(ng) as ng FROM middle_plating_monthly_ng_resumes
            WHERE hpl = 'CLKEY'
            AND `month` = '" . $bulan . "'
            AND location = '" . $incoming . "'
            GROUP BY hpl, ng_name) AS ng
            LEFT JOIN
            (SELECT remark, SUM(`check`) as `check` FROM middle_plating_daily_resumes
            WHERE remark = 'CLKEY'
            AND DATE_FORMAT(date,'%Y-%m') = '" . $bulan . "'
            AND location = '" . $incoming . "'
            GROUP BY remark) AS cek
            ON cek.remark = ng.hpl
            ORDER BY ng.ng DESC");

        $kensa_ng = db::select("SELECT ng.hpl, ng.ng_name, ng.ng, cek.`check` FROM
            (SELECT hpl, ng_name, SUM(ng) as ng FROM middle_plating_monthly_ng_resumes
            WHERE hpl = 'CLKEY'
            AND `month` = '" . $bulan . "'
            AND location = '" . $kensa . "'
            GROUP BY hpl, ng_name) AS ng
            LEFT JOIN
            (SELECT remark, SUM(`check`) as `check` FROM middle_plating_daily_resumes
            WHERE remark = 'CLKEY'
            AND DATE_FORMAT(date,'%Y-%m') = '" . $bulan . "'
            AND location = '" . $kensa . "'
            GROUP BY remark) AS cek
            ON cek.remark = ng.hpl
            ORDER BY ng.ng DESC");

        //Highest Key
        $ic_ng_key = db::select("SELECT hpl, `key`, sum(ng) AS ng FROM middle_plating_monthly_ng_resumes
            WHERE hpl = 'CLKEY'
            AND location = '" . $incoming . "'
            AND `month` = '" . $bulan . "'
            GROUP BY hpl, `key`
            ORDER BY ng DESC
            LIMIT 10");

        $ic_ng_key_detail = db::select("SELECT hpl, `key`, ng_name, ng FROM middle_plating_monthly_ng_resumes
            WHERE hpl = 'CLKEY'
            AND location = '" . $incoming . "'
            AND `month` = '" . $bulan . "'");

        $kensa_ng_key = db::select("SELECT hpl, `key`, sum(ng) AS ng FROM middle_plating_monthly_ng_resumes
            WHERE hpl = 'CLKEY'
            AND location = '" . $kensa . "'
            AND `month` = '" . $bulan . "'
            GROUP BY hpl, `key`
            ORDER BY ng DESC
            LIMIT 10");

        $kensa_ng_key_detail = db::select("SELECT hpl, `key`, ng_name, ng FROM middle_plating_monthly_ng_resumes
            WHERE hpl = 'CLKEY'
            AND location = '" . $kensa . "'
            AND `month` = '" . $bulan . "'");

        $response = array(
            'status' => true,

            'ic_ng' => $ic_ng,
            'ic_ng_key' => $ic_ng_key,
            'ic_ng_key_detail' => $ic_ng_key_detail,

            'kensa_ng' => $kensa_ng,
            'kensa_ng_key' => $kensa_ng_key,
            'kensa_ng_key_detail' => $kensa_ng_key_detail,

            'bulanText' => $bulanText,
        );
        return Response::json($response);
    }

    public function fetchPltNgFlute(Request $request)
    {
        $incoming = 'plt-incoming-fl';
        $kensa = 'plt-kensa-fl';

        $bulan = "";
        $bulanText = "";
        if (strlen($request->get('bulan')) > 0) {
            $bulan = date('Y-m', strtotime('01-' . $request->get('bulan')));
            $bulanText = date('M Y', strtotime('01-' . $request->get('bulan')));
        } else {
            $bulan = date('Y-m');
            $bulanText = date('M Y');
        }

        //Highest NG
        $ic_ng = db::select("SELECT ng.hpl, ng.ng_name, ng.ng, cek.`check` FROM
            (SELECT hpl, ng_name, SUM(ng) as ng FROM middle_plating_monthly_ng_resumes
            WHERE hpl = 'FLKEY'
            AND `month` = '" . $bulan . "'
            AND location = '" . $incoming . "'
            GROUP BY hpl, ng_name) AS ng
            LEFT JOIN
            (SELECT remark, SUM(`check`) as `check` FROM middle_plating_daily_resumes
            WHERE remark = 'FLKEY'
            AND DATE_FORMAT(date,'%Y-%m') = '" . $bulan . "'
            AND location = '" . $incoming . "'
            GROUP BY remark) AS cek
            ON cek.remark = ng.hpl
            ORDER BY ng.ng DESC");

        $kensa_ng = db::select("SELECT ng.hpl, ng.ng_name, ng.ng, cek.`check` FROM
            (SELECT hpl, ng_name, SUM(ng) as ng FROM middle_plating_monthly_ng_resumes
            WHERE hpl = 'FLKEY'
            AND `month` = '" . $bulan . "'
            AND location = '" . $kensa . "'
            GROUP BY hpl, ng_name) AS ng
            LEFT JOIN
            (SELECT remark, SUM(`check`) as `check` FROM middle_plating_daily_resumes
            WHERE remark = 'FLKEY'
            AND DATE_FORMAT(date,'%Y-%m') = '" . $bulan . "'
            AND location = '" . $kensa . "'
            GROUP BY remark) AS cek
            ON cek.remark = ng.hpl
            ORDER BY ng.ng DESC");

        //Highest Key
        $ic_ng_key = db::select("SELECT hpl, `key`, sum(ng) AS ng FROM middle_plating_monthly_ng_resumes
            WHERE hpl = 'FLKEY'
            AND location = '" . $incoming . "'
            AND `month` = '" . $bulan . "'
            GROUP BY hpl, `key`
            ORDER BY ng DESC
            LIMIT 10");

        $ic_ng_key_detail = db::select("SELECT hpl, `key`, ng_name, ng FROM middle_plating_monthly_ng_resumes
            WHERE hpl = 'FLKEY'
            AND location = '" . $incoming . "'
            AND `month` = '" . $bulan . "'");

        $kensa_ng_key = db::select("SELECT hpl, `key`, sum(ng) AS ng FROM middle_plating_monthly_ng_resumes
            WHERE hpl = 'FLKEY'
            AND location = '" . $kensa . "'
            AND `month` = '" . $bulan . "'
            GROUP BY hpl, `key`
            ORDER BY ng DESC
            LIMIT 10");

        $kensa_ng_key_detail = db::select("SELECT hpl, `key`, ng_name, ng FROM middle_plating_monthly_ng_resumes
            WHERE hpl = 'FLKEY'
            AND location = '" . $kensa . "'
            AND `month` = '" . $bulan . "'");

        $response = array(
            'status' => true,

            'ic_ng' => $ic_ng,
            'ic_ng_key' => $ic_ng_key,
            'ic_ng_key_detail' => $ic_ng_key_detail,

            'kensa_ng' => $kensa_ng,
            'kensa_ng_key' => $kensa_ng_key,
            'kensa_ng_key_detail' => $kensa_ng_key_detail,

            'bulanText' => $bulanText,
        );
        return Response::json($response);
    }

    public function fetchPltNgRate(Request $request, $id)
    {
        $incoming = '';
        $kensa = '';

        if ($id == 'sax') {
            $incoming = 'plt-incoming-sx';
            $kensa = 'plt-kensa-sx';
        } elseif ($id == 'fl') {
            $incoming = 'plt-incoming-fl';
            $kensa = 'plt-kensa-fl';
        } elseif ($id == 'cl') {
            $incoming = 'plt-incoming-cl';
            $kensa = 'plt-kensa-cl';
        }

        $bulan = "";
        if (strlen($request->get('bulan')) > 0) {
            $bulan = $request->get('bulan');
        } else {
            $bulan = date('m-Y');
        }

        $ic = db::select("SELECT tgl.week_date, tgl.hpl, resume.ng, resume.`check` AS g, (resume.ng/resume.`check`*100) as ng_rate from
			(select week_date, hpl from
			(select week_date from weekly_calendars where DATE_FORMAT(week_date,'%m-%Y') = '" . $bulan . "') date
			cross join
			(select DISTINCT hpl from materials where hpl in ('ASKEY','TSKEY')) hpl ) tgl
			left join
			(select date, remark as hpl, ng, `check` from middle_plating_daily_resumes
			where DATE_FORMAT(date,'%m-%Y') = '" . $bulan . "'
			AND location = '" . $incoming . "') resume
			on resume.date = tgl.week_date AND tgl.hpl = resume.hpl
			ORDER BY tgl.week_date;");

        $kensa = db::select("SELECT tgl.week_date, tgl.hpl, resume.ng, resume.`check` AS g, (resume.ng/resume.`check`*100) as ng_rate from
			(select week_date, hpl from
			(select week_date from weekly_calendars where DATE_FORMAT(week_date,'%m-%Y') = '" . $bulan . "') date
			cross join
			(select DISTINCT hpl from materials where hpl in ('ASKEY','TSKEY')) hpl ) tgl
			left join
			(select date, remark as hpl, ng, `check` from middle_plating_daily_resumes
			where DATE_FORMAT(date,'%m-%Y') = '" . $bulan . "'
			AND location = '" . $kensa . "') resume
			on resume.date = tgl.week_date AND tgl.hpl = resume.hpl
			ORDER BY tgl.week_date;");

        $response = array(
            'status' => true,
            'ic' => $ic,
            'kensa' => $kensa,
            'bulan' => $bulan,
        );
        return Response::json($response);

    }

    public function fetchPltNgRateClarinet(Request $request)
    {
        $incoming = 'plt-incoming-cl';
        $kensa = 'plt-kensa-cl';

        $bulan = "";

        if (strlen($request->get('bulan')) > 0) {
            $bulan = $request->get('bulan');
        } else {
            $bulan = date('m-Y');
        }

        $ic = db::select("SELECT tgl.week_date, tgl.hpl, resume.ng, resume.`check` AS g, (resume.ng/resume.`check`*100) as ng_rate from
            (select week_date, hpl from
            (select week_date from weekly_calendars where DATE_FORMAT(week_date,'%m-%Y') = '" . $bulan . "') date
            cross join
            (select DISTINCT hpl from materials where hpl in ('CLKEY')) hpl ) tgl
            left join
            (select date, remark as hpl, ng, `check` from middle_plating_daily_resumes
            where DATE_FORMAT(date,'%m-%Y') = '" . $bulan . "'
            AND location = '" . $incoming . "') resume
            on resume.date = tgl.week_date AND tgl.hpl = resume.hpl
            ORDER BY tgl.week_date;");

        $kensa = db::select("SELECT tgl.week_date, tgl.hpl, resume.ng, resume.`check` AS g, (resume.ng/resume.`check`*100) as ng_rate from
            (select week_date, hpl from
            (select week_date from weekly_calendars where DATE_FORMAT(week_date,'%m-%Y') = '" . $bulan . "') date
            cross join
            (select DISTINCT hpl from materials where hpl in ('CLKEY')) hpl ) tgl
            left join
            (select date, remark as hpl, ng, `check` from middle_plating_daily_resumes
            where DATE_FORMAT(date,'%m-%Y') = '" . $bulan . "'
            AND location = '" . $kensa . "') resume
            on resume.date = tgl.week_date AND tgl.hpl = resume.hpl
            ORDER BY tgl.week_date;");

        $response = array(
            'status' => true,
            'ic' => $ic,
            'kensa' => $kensa,
            'bulan' => $bulan,
        );
        return Response::json($response);

    }

    public function fetchPltNgRateFlute(Request $request)
    {
        $incoming = 'plt-incoming-fl';
        $kensa = 'plt-kensa-fl';

        $bulan = "";

        if (strlen($request->get('bulan')) > 0) {
            $bulan = $request->get('bulan');
        } else {
            $bulan = date('m-Y');
        }

        $ic = db::select("SELECT tgl.week_date, tgl.hpl, resume.ng, resume.`check` AS g, (resume.ng/resume.`check`*100) as ng_rate from
            (select week_date, hpl from
            (select week_date from weekly_calendars where DATE_FORMAT(week_date,'%m-%Y') = '" . $bulan . "') date
            cross join
            (select DISTINCT hpl from materials where hpl in ('FLKEY')) hpl ) tgl
            left join
            (select date, remark as hpl, ng, `check` from middle_plating_daily_resumes
            where DATE_FORMAT(date,'%m-%Y') = '" . $bulan . "'
            AND location = '" . $incoming . "') resume
            on resume.date = tgl.week_date AND tgl.hpl = resume.hpl
            ORDER BY tgl.week_date;");

        $kensa = db::select("SELECT tgl.week_date, tgl.hpl, resume.ng, resume.`check` AS g, (resume.ng/resume.`check`*100) as ng_rate from
            (select week_date, hpl from
            (select week_date from weekly_calendars where DATE_FORMAT(week_date,'%m-%Y') = '" . $bulan . "') date
            cross join
            (select DISTINCT hpl from materials where hpl in ('FLKEY')) hpl ) tgl
            left join
            (select date, remark as hpl, ng, `check` from middle_plating_daily_resumes
            where DATE_FORMAT(date,'%m-%Y') = '" . $bulan . "'
            AND location = '" . $kensa . "') resume
            on resume.date = tgl.week_date AND tgl.hpl = resume.hpl
            ORDER BY tgl.week_date;");

        $response = array(
            'status' => true,
            'ic' => $ic,
            'kensa' => $kensa,
            'bulan' => $bulan,
        );
        return Response::json($response);

    }

    public function fetchLcqNgRate(Request $request)
    {

        $bulan = "";
        if (strlen($request->get('bulan')) > 0) {
            $bulan = $request->get('bulan');
        } else {
            $bulan = date('m-Y');
        }

        $ic = db::select("SELECT tgl.week_date, tgl.hpl, resume.ng, resume.`check` AS g, (resume.ng/resume.`check`*100) as ng_rate from
			(select week_date, hpl from
			(select week_date from weekly_calendars where DATE_FORMAT(week_date,'%m-%Y') = '" . $bulan . "') date
			cross join
			(select DISTINCT hpl from materials where hpl in ('ASKEY','TSKEY')) hpl ) tgl
			left join
			(select date, remark as hpl, ng, `check` from middle_lacquering_daily_resumes
			where DATE_FORMAT(date,'%m-%Y') = '" . $bulan . "'
			AND location = 'lcq-incoming') resume
			on resume.date = tgl.week_date AND tgl.hpl = resume.hpl
			ORDER BY tgl.week_date;");

        $kensa = db::select("SELECT tgl.week_date, tgl.hpl, resume.ng, resume.`check` AS g, (resume.ng/resume.`check`*100) as ng_rate from
			(select week_date, hpl from
			(select week_date from weekly_calendars where DATE_FORMAT(week_date,'%m-%Y') = '" . $bulan . "') date
			cross join
			(select DISTINCT hpl from materials where hpl in ('ASKEY','TSKEY')) hpl ) tgl
			left join
			(select date, remark as hpl, ng, `check` from middle_lacquering_daily_resumes
			where DATE_FORMAT(date,'%m-%Y') = '" . $bulan . "'
			AND location = 'lcq-kensa') resume
			on resume.date = tgl.week_date AND tgl.hpl = resume.hpl
			ORDER BY tgl.week_date;");

        $response = array(
            'status' => true,
            'ic' => $ic,
            'kensa' => $kensa,
            'bulan' => $bulan,
        );
        return Response::json($response);
    }

    public function fetchReportNG(Request $request)
    {

        $loc = $request->get('location');
        $report = [];

        if (str_contains($loc, 'bff')) {
            $report = MiddleBuffingNgLog::leftJoin('materials', 'materials.material_number', '=', 'middle_buffing_ng_logs.material_number')
                ->leftJoin(db::raw('employee_syncs AS operator'), 'operator.employee_id', '=', 'middle_buffing_ng_logs.operator_id')
                ->leftJoin(db::raw('employee_syncs AS kensa'), 'kensa.employee_id', '=', 'middle_buffing_ng_logs.employee_id');

            if (strlen($request->get('datefrom')) > 0) {
                $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
                $report = $report->where(db::raw('date_format(middle_buffing_ng_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
            }

            if (strlen($request->get('dateto')) > 0) {
                $date_to = date('Y-m-d', strtotime($request->get('dateto')));
                $report = $report->where(db::raw('date_format(middle_buffing_ng_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
            }

            if ($request->get('location') != null) {
                $report = $report->where('middle_buffing_ng_logs.location', $request->get('location'));
            }

            $report = $report->select(
                'middle_buffing_ng_logs.employee_id',
                db::raw('concat(SPLIT_STRING(kensa.`name`, " ", 1), " ", SPLIT_STRING(kensa.`name`, " ", 2)) AS `check`'),
                'middle_buffing_ng_logs.operator_id',
                db::raw('concat(SPLIT_STRING(operator.`name`, " ", 1), " ", SPLIT_STRING(operator.`name`, " ", 2)) AS operator'),
                'middle_buffing_ng_logs.tag',
                'middle_buffing_ng_logs.material_number',
                'materials.material_description',
                'materials.key',
                'materials.model',
                'materials.surface',
                'middle_buffing_ng_logs.ng_name',
                'middle_buffing_ng_logs.quantity',
                'middle_buffing_ng_logs.location',
                'middle_buffing_ng_logs.created_at'
            )->get();

        } else if (str_contains($loc, 'lcq')) {
            $report = MiddleLacqueringNgLog::leftJoin('materials', 'materials.material_number', '=', 'middle_lacquering_ng_logs.material_number')
                ->leftJoin(db::raw('employee_syncs AS operator'), 'operator.employee_id', '=', 'middle_lacquering_ng_logs.operator_id')
                ->leftJoin(db::raw('employee_syncs AS kensa'), 'kensa.employee_id', '=', 'middle_lacquering_ng_logs.employee_id');

            if (strlen($request->get('datefrom')) > 0) {
                $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
                $report = $report->where(db::raw('date_format(middle_lacquering_ng_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
            }

            if (strlen($request->get('dateto')) > 0) {
                $date_to = date('Y-m-d', strtotime($request->get('dateto')));
                $report = $report->where(db::raw('date_format(middle_lacquering_ng_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
            }

            if ($request->get('location') != null) {
                $report = $report->where('middle_lacquering_ng_logs.location', $request->get('location'));
            }

            $report = $report->select(
                'middle_lacquering_ng_logs.employee_id',
                db::raw('concat(SPLIT_STRING(kensa.`name`, " ", 1), " ", SPLIT_STRING(kensa.`name`, " ", 2)) AS `check`'),
                'middle_lacquering_ng_logs.operator_id',
                db::raw('concat(SPLIT_STRING(operator.`name`, " ", 1), " ", SPLIT_STRING(operator.`name`, " ", 2)) AS operator'),
                'middle_lacquering_ng_logs.tag',
                'middle_lacquering_ng_logs.material_number',
                'materials.material_description',
                'materials.key',
                'materials.model',
                'materials.surface',
                'middle_lacquering_ng_logs.ng_name',
                'middle_lacquering_ng_logs.quantity',
                'middle_lacquering_ng_logs.location',
                'middle_lacquering_ng_logs.created_at'
            )->get();

        } else if (str_contains($loc, 'plt')) {
            $report = MiddlePlatingNgLog::leftJoin('materials', 'materials.material_number', '=', 'middle_plating_ng_logs.material_number')
                ->leftJoin(db::raw('employee_syncs AS operator'), 'operator.employee_id', '=', 'middle_plating_ng_logs.operator_id')
                ->leftJoin(db::raw('employee_syncs AS kensa'), 'kensa.employee_id', '=', 'middle_plating_ng_logs.employee_id');

            if (strlen($request->get('datefrom')) > 0) {
                $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
                $report = $report->where(db::raw('date_format(middle_plating_ng_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
            }

            if (strlen($request->get('dateto')) > 0) {
                $date_to = date('Y-m-d', strtotime($request->get('dateto')));
                $report = $report->where(db::raw('date_format(middle_plating_ng_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
            }

            if ($request->get('location') != null) {
                $report = $report->where('middle_plating_ng_logs.location', $request->get('location'));
            }

            $report = $report->select(
                'middle_plating_ng_logs.employee_id',
                db::raw('concat(SPLIT_STRING(kensa.`name`, " ", 1), " ", SPLIT_STRING(kensa.`name`, " ", 2)) AS `check`'),
                'middle_plating_ng_logs.operator_id',
                db::raw('concat(SPLIT_STRING(operator.`name`, " ", 1), " ", SPLIT_STRING(operator.`name`, " ", 2)) AS operator'),
                'middle_plating_ng_logs.tag',
                'middle_plating_ng_logs.material_number',
                'materials.material_description',
                'materials.key',
                'materials.model',
                'materials.surface',
                'middle_plating_ng_logs.ng_name',
                'middle_plating_ng_logs.quantity',
                'middle_plating_ng_logs.location',
                'middle_plating_ng_logs.created_at'
            )->get();

        } else if (str_contains($loc, 'subassy')) {

            $report_lcq = MiddleLacqueringNgLog::leftJoin('materials', 'materials.material_number', '=', 'middle_lacquering_ng_logs.material_number')
                ->leftJoin(db::raw('employee_syncs AS operator'), 'operator.employee_id', '=', 'middle_lacquering_ng_logs.operator_id')
                ->leftJoin(db::raw('employee_syncs AS kensa'), 'kensa.employee_id', '=', 'middle_lacquering_ng_logs.employee_id');

            if (strlen($request->get('datefrom')) > 0) {
                $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
                $report_lcq = $report_lcq->where(db::raw('date_format(middle_lacquering_ng_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
            }

            if (strlen($request->get('dateto')) > 0) {
                $date_to = date('Y-m-d', strtotime($request->get('dateto')));
                $report_lcq = $report_lcq->where(db::raw('date_format(middle_lacquering_ng_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
            }

            if ($request->get('location') != null) {
                $report_lcq = $report_lcq->where('middle_lacquering_ng_logs.location', $request->get('location'));
            }

            $report_lcq = $report_lcq->select(
                'middle_lacquering_ng_logs.employee_id',
                db::raw('concat(SPLIT_STRING(kensa.`name`, " ", 1), " ", SPLIT_STRING(kensa.`name`, " ", 2)) AS `check`'),
                'middle_lacquering_ng_logs.operator_id',
                db::raw('concat(SPLIT_STRING(operator.`name`, " ", 1), " ", SPLIT_STRING(operator.`name`, " ", 2)) AS operator'),
                'middle_lacquering_ng_logs.tag',
                'middle_lacquering_ng_logs.material_number',
                'materials.material_description',
                'materials.key',
                'materials.model',
                'materials.surface',
                'middle_lacquering_ng_logs.ng_name',
                'middle_lacquering_ng_logs.quantity',
                'middle_lacquering_ng_logs.location',
                'middle_lacquering_ng_logs.created_at'
            )->get();

            $report_plt = MiddlePlatingNgLog::leftJoin('materials', 'materials.material_number', '=', 'middle_plating_ng_logs.material_number')
                ->leftJoin(db::raw('employee_syncs AS operator'), 'operator.employee_id', '=', 'middle_plating_ng_logs.operator_id')
                ->leftJoin(db::raw('employee_syncs AS kensa'), 'kensa.employee_id', '=', 'middle_plating_ng_logs.employee_id');

            if (strlen($request->get('datefrom')) > 0) {
                $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
                $report_plt = $report_plt->where(db::raw('date_format(middle_plating_ng_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
            }

            if (strlen($request->get('dateto')) > 0) {
                $date_to = date('Y-m-d', strtotime($request->get('dateto')));
                $report_plt = $report_plt->where(db::raw('date_format(middle_plating_ng_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
            }

            if ($request->get('location') != null) {
                $report_plt = $report_plt->where('middle_plating_ng_logs.location', $request->get('location'));
            }

            $report_plt = $report_plt->select(
                'middle_plating_ng_logs.employee_id',
                db::raw('concat(SPLIT_STRING(kensa.`name`, " ", 1), " ", SPLIT_STRING(kensa.`name`, " ", 2)) AS `check`'),
                'middle_plating_ng_logs.operator_id',
                db::raw('concat(SPLIT_STRING(operator.`name`, " ", 1), " ", SPLIT_STRING(operator.`name`, " ", 2)) AS operator'),
                'middle_plating_ng_logs.tag',
                'middle_plating_ng_logs.material_number',
                'materials.material_description',
                'materials.key',
                'materials.model',
                'materials.surface',
                'middle_plating_ng_logs.ng_name',
                'middle_plating_ng_logs.quantity',
                'middle_plating_ng_logs.location',
                'middle_plating_ng_logs.created_at'
            )->get();

            foreach ($report_lcq as $rt) {
                array_push($report, $rt);
            }

            foreach ($report_plt as $rt) {
                array_push($report, $rt);
            }

        }

        return DataTables::of($report)
            ->addColumn('concat_check', function ($report) {
                return $report->employee_id . '<br>' . $report->check;
            })
            ->addColumn('concat_operator', function ($report) {
                return $report->operator_id . '<br>' . $report->operator;
            })
            ->rawColumns([
                'concat_check' => 'concat_check',
                'concat_operator' => 'concat_operator',
            ])
            ->make(true);
    }

    public function fetchReportProductionResult(Request $request)
    {

        $loc = $request->get('location');
        $report = [];

        if (str_contains($loc, 'bff')) {
            $report = MiddleBuffingCheckLog::leftJoin('materials', 'materials.material_number', '=', 'middle_buffing_check_logs.material_number')
                ->leftJoin(db::raw('employee_syncs AS operator'), 'operator.employee_id', '=', 'middle_buffing_check_logs.operator_id')
                ->leftJoin(db::raw('employee_syncs AS kensa'), 'kensa.employee_id', '=', 'middle_buffing_check_logs.employee_id');

            if (strlen($request->get('datefrom')) > 0) {
                $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
                $report = $report->where(db::raw('date_format(middle_buffing_check_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
            }

            if (strlen($request->get('dateto')) > 0) {
                $date_to = date('Y-m-d', strtotime($request->get('dateto')));
                $report = $report->where(db::raw('date_format(middle_buffing_check_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
            }

            if ($request->get('location') != null) {
                $report = $report->where('middle_buffing_check_logs.location', $request->get('location'));
            }

            $report = $report->select(
                'middle_buffing_check_logs.employee_id',
                db::raw('concat(SPLIT_STRING(kensa.`name`, " ", 1), " ", SPLIT_STRING(kensa.`name`, " ", 2)) AS `check`'),
                'middle_buffing_check_logs.operator_id',
                db::raw('concat(SPLIT_STRING(operator.`name`, " ", 1), " ", SPLIT_STRING(operator.`name`, " ", 2)) AS operator'),
                'middle_buffing_check_logs.tag',
                'middle_buffing_check_logs.material_number',
                'materials.material_description',
                'materials.key',
                'materials.model',
                'materials.surface',
                'middle_buffing_check_logs.quantity',
                'middle_buffing_check_logs.location',
                'middle_buffing_check_logs.created_at'
            )->get();

        } else if (str_contains($loc, 'lcq')) {
            $report = MiddleLacqueringCheckLog::leftJoin('materials', 'materials.material_number', '=', 'middle_lacquering_check_logs.material_number')
                ->leftJoin(db::raw('employee_syncs AS operator'), 'operator.employee_id', '=', 'middle_lacquering_check_logs.operator_id')
                ->leftJoin(db::raw('employee_syncs AS kensa'), 'kensa.employee_id', '=', 'middle_lacquering_check_logs.employee_id');

            if (strlen($request->get('datefrom')) > 0) {
                $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
                $report = $report->where(db::raw('date_format(middle_lacquering_check_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
            }

            if (strlen($request->get('dateto')) > 0) {
                $date_to = date('Y-m-d', strtotime($request->get('dateto')));
                $report = $report->where(db::raw('date_format(middle_lacquering_check_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
            }

            if ($request->get('location') != null) {
                $report = $report->where('middle_lacquering_check_logs.location', $request->get('location'));
            }

            $report = $report->select(
                'middle_lacquering_check_logs.employee_id',
                db::raw('concat(SPLIT_STRING(kensa.`name`, " ", 1), " ", SPLIT_STRING(kensa.`name`, " ", 2)) AS `check`'),
                'middle_lacquering_check_logs.operator_id',
                db::raw('concat(SPLIT_STRING(operator.`name`, " ", 1), " ", SPLIT_STRING(operator.`name`, " ", 2)) AS operator'),
                'middle_lacquering_check_logs.tag',
                'middle_lacquering_check_logs.material_number',
                'materials.material_description',
                'materials.key',
                'materials.model',
                'materials.surface',
                'middle_lacquering_check_logs.quantity',
                'middle_lacquering_check_logs.location',
                'middle_lacquering_check_logs.created_at'
            )->get();

        } else if (str_contains($loc, 'plt')) {
            $report = MiddlePlatingCheckLog::leftJoin('materials', 'materials.material_number', '=', 'middle_plating_check_logs.material_number')
                ->leftJoin(db::raw('employee_syncs AS operator'), 'operator.employee_id', '=', 'middle_plating_check_logs.operator_id')
                ->leftJoin(db::raw('employee_syncs AS kensa'), 'kensa.employee_id', '=', 'middle_plating_check_logs.employee_id');

            if (strlen($request->get('datefrom')) > 0) {
                $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
                $report = $report->where(db::raw('date_format(middle_plating_check_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
            }

            if (strlen($request->get('dateto')) > 0) {
                $date_to = date('Y-m-d', strtotime($request->get('dateto')));
                $report = $report->where(db::raw('date_format(middle_plating_check_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
            }

            if ($request->get('location') != null) {
                $report = $report->where('middle_plating_check_logs.location', $request->get('location'));
            }

            $report = $report->select(
                'middle_plating_check_logs.employee_id',
                db::raw('concat(SPLIT_STRING(kensa.`name`, " ", 1), " ", SPLIT_STRING(kensa.`name`, " ", 2)) AS `check`'),
                'middle_plating_check_logs.operator_id',
                db::raw('concat(SPLIT_STRING(operator.`name`, " ", 1), " ", SPLIT_STRING(operator.`name`, " ", 2)) AS operator'),
                'middle_plating_check_logs.tag',
                'middle_plating_check_logs.material_number',
                'materials.material_description',
                'materials.key',
                'materials.model',
                'materials.surface',
                'middle_plating_check_logs.quantity',
                'middle_plating_check_logs.location',
                'middle_plating_check_logs.created_at'
            )->get();

        } else if (str_contains($loc, 'subassy')) {

            $report_lcq = MiddleLacqueringCheckLog::leftJoin('materials', 'materials.material_number', '=', 'middle_lacquering_check_logs.material_number')
                ->leftJoin(db::raw('employee_syncs AS operator'), 'operator.employee_id', '=', 'middle_lacquering_check_logs.operator_id')
                ->leftJoin(db::raw('employee_syncs AS kensa'), 'kensa.employee_id', '=', 'middle_lacquering_check_logs.employee_id');

            if (strlen($request->get('datefrom')) > 0) {
                $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
                $report_lcq = $report_lcq->where(db::raw('date_format(middle_lacquering_check_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
            }

            if (strlen($request->get('dateto')) > 0) {
                $date_to = date('Y-m-d', strtotime($request->get('dateto')));
                $report_lcq = $report_lcq->where(db::raw('date_format(middle_lacquering_check_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
            }

            if ($request->get('location') != null) {
                $report_lcq = $report_lcq->where('middle_lacquering_check_logs.location', $request->get('location'));
            }

            $report_lcq = $report_lcq->select(
                'middle_lacquering_check_logs.employee_id',
                db::raw('concat(SPLIT_STRING(kensa.`name`, " ", 1), " ", SPLIT_STRING(kensa.`name`, " ", 2)) AS `check`'),
                'middle_lacquering_check_logs.operator_id',
                db::raw('concat(SPLIT_STRING(operator.`name`, " ", 1), " ", SPLIT_STRING(operator.`name`, " ", 2)) AS operator'),
                'middle_lacquering_check_logs.tag',
                'middle_lacquering_check_logs.material_number',
                'materials.material_description',
                'materials.key',
                'materials.model',
                'materials.surface',
                'middle_lacquering_check_logs.quantity',
                'middle_lacquering_check_logs.location',
                'middle_lacquering_check_logs.created_at'
            )->get();

            $report_plt = MiddlePlatingCheckLog::leftJoin('materials', 'materials.material_number', '=', 'middle_plating_check_logs.material_number')
                ->leftJoin(db::raw('employee_syncs AS operator'), 'operator.employee_id', '=', 'middle_plating_check_logs.operator_id')
                ->leftJoin(db::raw('employee_syncs AS kensa'), 'kensa.employee_id', '=', 'middle_plating_check_logs.employee_id');

            if (strlen($request->get('datefrom')) > 0) {
                $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
                $report_plt = $report_plt->where(db::raw('date_format(middle_plating_check_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
            }

            if (strlen($request->get('dateto')) > 0) {
                $date_to = date('Y-m-d', strtotime($request->get('dateto')));
                $report_plt = $report_plt->where(db::raw('date_format(middle_plating_check_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
            }

            if ($request->get('location') != null) {
                $report_plt = $report_plt->where('middle_plating_check_logs.location', $request->get('location'));
            }

            $report_plt = $report_plt->select(
                'middle_plating_check_logs.employee_id',
                db::raw('concat(SPLIT_STRING(kensa.`name`, " ", 1), " ", SPLIT_STRING(kensa.`name`, " ", 2)) AS `check`'),
                'middle_plating_check_logs.operator_id',
                db::raw('concat(SPLIT_STRING(operator.`name`, " ", 1), " ", SPLIT_STRING(operator.`name`, " ", 2)) AS operator'),
                'middle_plating_check_logs.tag',
                'middle_plating_check_logs.material_number',
                'materials.material_description',
                'materials.key',
                'materials.model',
                'materials.surface',
                'middle_plating_check_logs.quantity',
                'middle_plating_check_logs.location',
                'middle_plating_check_logs.created_at'
            )->get();

            foreach ($report_lcq as $rt) {
                array_push($report, $rt);
            }

            foreach ($report_plt as $rt) {
                array_push($report, $rt);
            }

        }

        return DataTables::of($report)
            ->addColumn('concat_check', function ($report) {
                return $report->employee_id . '<br>' . $report->check;
            })
            ->addColumn('concat_operator', function ($report) {
                return $report->operator_id . '<br>' . $report->operator;
            })
            ->rawColumns([
                'concat_check' => 'concat_check',
                'concat_operator' => 'concat_operator',
            ])
            ->make(true);

    }

    public function fetchBarrelLog(Request $request)
    {

        $barrel_logs = BarrelLog::leftJoin('materials', 'materials.material_number', '=', 'barrel_logs.material');

        if (strlen($request->get('datefrom')) > 0) {
            $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
            $barrel_logs = $barrel_logs->where(db::raw('date_format(barrel_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
        }

        if (strlen($request->get('dateto')) > 0) {
            $date_to = date('Y-m-d', strtotime($request->get('dateto')));
            $barrel_logs = $barrel_logs->where(db::raw('date_format(barrel_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
        }

        if ($request->get('code') != null) {
            $barrel_logs = $barrel_logs->whereIn('materials.origin_group_code', $request->get('code'));
        }

        $barrel_logs = $barrel_logs->select('barrel_logs.tag', 'barrel_logs.material', 'materials.material_description', 'barrel_logs.qty', 'barrel_logs.status', 'barrel_logs.created_at')->get();

        return DataTables::of($barrel_logs)->make(true);
        // return Response::json($barrel_logs);

    }

    public function fetchBuffingBoardClDetail(Request $request)
    {
        $rack = $request->get('rack');

        $queues = db::connection('digital_kanban')->select("SELECT q.material_num, m.material_description, m.model, m.`key`, q.created_at FROM buffing_queues q
			LEFT JOIN materials m ON m.material_number = q.material_num
			WHERE q.rack = '" . strtoupper($rack) . "'
			ORDER BY q.created_at ASC");

        return DataTables::of($queues)->make(true);
    }

    public function fetchBuffingBoardCl(Request $request)
    {
        $tmp = [];

        $work_stations = db::connection('digital_kanban')->table('dev_list')
            ->whereRaw('SPLIT_STRING(dev_name, "-", 1) = "CLKEY"')
            ->where('enable_antrian', '!=', 'RPR')
            ->orderBy('dev_name', 'asc')
            ->get();

        $boards = array();
        foreach ($work_stations as $work_station) {
            $employee = EmployeeSync::where('employee_id', '=', $work_station->dev_operator_id)->select('name')->first();
            if ($employee != null) {
                $employee_name = $employee->name;
            } else {
                $employee_name = "NotFound";
            }

            if ($work_station->dev_selesai_detected == 1) {
                $selesai = $work_station->dev_selesai_num;
                array_push($tmp, $work_station->dev_selesai_num);
            } else {
                $selesai = "";
            }

            $queues = db::connection('digital_kanban')
                ->table('buffing_queues')
                ->where('rack', '=', $work_station->dev_name)
                ->orderBy('created_at', 'asc')
                ->get();

            $lists = array();
            for ($i = 0; $i < 10; $i++) {
                if (isset($queues[$i])) {
                    array_push($lists, $queues[$i]->material_num);
                    array_push($tmp, $queues[$i]->material_num);
                } else {
                    array_push($lists, "");
                }
            }

            array_push($boards, [
                'ws' => $work_station->dev_name,
                'employee_id' => $work_station->dev_operator_id,
                'employee_name' => $employee_name,
                'dev_akan_detected' => $work_station->dev_akan_detected,
                'dev_sedang_detected' => $work_station->dev_sedang_detected,
                'dev_selesai_detected' => $work_station->dev_selesai_detected,
                'sedang' => $work_station->dev_sedang_num,
                'akan' => $work_station->dev_akan_num,
                'akan_time' => $work_station->dev_akan_time,
                'sedang_time' => $work_station->dev_sedang_time,
                'selesai_time' => $work_station->dev_selesai_time,
                'selesai' => $selesai,
                'queue_1' => $lists[0],
                'queue_2' => $lists[1],
                'queue_3' => $lists[2],
                'queue_4' => $lists[3],
                'queue_5' => $lists[4],
                'queue_6' => $lists[5],
                'queue_7' => $lists[6],
                'queue_8' => $lists[7],
                'queue_9' => $lists[8],
                'queue_10' => $lists[9],
                'jumlah' => count($queues),
            ]);
        }

        $tmp = array_unique($tmp);

        $materials = Material::where('issue_storage_location', 'CL51')
            ->select(
                'material_number',
                db::raw("IF(surface LIKE '%N%', concat('<span style=\"color : #FFFF66; font-weight : bold;\">', surface, '</span><br>', material_number, '<br>', model, '<br>', '<span style=\"color : #FFFF66; font-weight : bold;\">',`key`,'</span>'), concat('<span style=\"color : #FF99CC; font-weight : bold;\">', surface, '</span><br>', material_number, '<br>' ,model, '<br>', '<span style=\"color : #FF99CC; font-weight : bold;\">', `key`, '</span>')) AS isi")
            )
            ->get();

        for ($i = 0; $i < count($boards); $i++) {
            foreach ($materials as $material) {
                if ($boards[$i]['sedang'] == $material->material_number) {
                    $boards[$i]['sedang'] = $material->isi;
                }

                if ($boards[$i]['akan'] == $material->material_number) {
                    $boards[$i]['akan'] = $material->isi;
                }

                if ($boards[$i]['queue_1'] == $material->material_number) {
                    $boards[$i]['queue_1'] = $material->isi;
                }

                if ($boards[$i]['queue_2'] == $material->material_number) {
                    $boards[$i]['queue_2'] = $material->isi;
                }

                if ($boards[$i]['queue_3'] == $material->material_number) {
                    $boards[$i]['queue_3'] = $material->isi;
                }

                if ($boards[$i]['queue_4'] == $material->material_number) {
                    $boards[$i]['queue_4'] = $material->isi;
                }

                if ($boards[$i]['queue_5'] == $material->material_number) {
                    $boards[$i]['queue_5'] = $material->isi;
                }

                if ($boards[$i]['queue_6'] == $material->material_number) {
                    $boards[$i]['queue_6'] = $material->isi;
                }

                if ($boards[$i]['queue_7'] == $material->material_number) {
                    $boards[$i]['queue_7'] = $material->isi;
                }

                if ($boards[$i]['queue_8'] == $material->material_number) {
                    $boards[$i]['queue_8'] = $material->isi;
                }

                if ($boards[$i]['queue_9'] == $material->material_number) {
                    $boards[$i]['queue_9'] = $material->isi;
                }

                if ($boards[$i]['queue_10'] == $material->material_number) {
                    $boards[$i]['queue_10'] = $material->isi;
                }

                if ($boards[$i]['selesai'] == $material->material_number) {
                    $boards[$i]['selesai'] = $material->isi;
                }
            }
        }

        $response = array(
            'status' => true,
            'boards' => $boards,
            'materials' => $materials,
        );
        return Response::json($response);

    }
    public function fetchBuffingBoard(Request $request)
    {
        $tmp = [];

        $work_stations = db::connection('digital_kanban')->table('dev_list')
            ->whereRaw('SPLIT_STRING(dev_name, "-", 1) = "SXKEY"')
            ->where('enable_antrian', '!=', 'RPR')
            // ->where('enable_antrian', '!=', 'FR')
            ->orderBy('dev_name', 'asc')
            ->get();

        $boards = array();
        foreach ($work_stations as $work_station) {
            $employee = Employee::where('employee_id', '=', $work_station->dev_operator_id)->select('name')->first();
            if ($employee != null) {
                $employee_name = $employee->name;
            } else {
                $employee_name = "NotFound";
            }

            if ($work_station->dev_selesai_detected == 1) {
                $selesai = $work_station->dev_selesai_num;
                array_push($tmp, $work_station->dev_selesai_num);
            } else {
                $selesai = "";
            }

            $queues = db::connection('digital_kanban')->table('buffing_queues')
                // ->where('rack', '=', $work_station->dev_name)
                ->whereRaw('rack = concat(SPLIT_STRING("' . $work_station->dev_name . '", "-", 1), "-",SPLIT_STRING("' . $work_station->dev_name . '", "-", 2))')
                ->orderBy('created_at', 'asc')
                ->get();

            $lists = array();
            for ($i = 0; $i < 10; $i++) {
                if (isset($queues[$i])) {
                    array_push($lists, $queues[$i]->material_num);
                    array_push($tmp, $queues[$i]->material_num);
                } else {
                    array_push($lists, "");
                }
            }

            $dt_now = new DateTime();

            $dt_akan = new DateTime($work_station->dev_akan_time);
            $akan_time = $dt_akan->diff($dt_now);

            $dt_sedang = new DateTime($work_station->dev_sedang_time);
            $sedang_time = $dt_sedang->diff($dt_now);

            $dt_selesai = new DateTime($work_station->dev_selesai_time);
            $selesai_time = $dt_selesai->diff($dt_now);

            array_push($tmp, $work_station->dev_sedang_num);
            array_push($tmp, $work_station->dev_akan_num);

            array_push($boards, [
                'ws' => $work_station->dev_name,
                'employee_id' => $work_station->dev_operator_id,
                'employee_name' => $employee_name,
                'dev_akan_detected' => $work_station->dev_akan_detected,
                'dev_sedang_detected' => $work_station->dev_sedang_detected,
                'dev_selesai_detected' => $work_station->dev_selesai_detected,
                'sedang' => $work_station->dev_sedang_num,
                'akan' => $work_station->dev_akan_num,
                'akan_time' => $akan_time->format('%H:%i:%s'),
                'sedang_time' => $sedang_time->format('%H:%i:%s'),
                'selesai_time' => $selesai_time->format('%H:%i:%s'),
                'selesai' => $selesai,
                'queue_1' => $lists[0],
                'queue_2' => $lists[1],
                'queue_3' => $lists[2],
                'queue_4' => $lists[3],
                'queue_5' => $lists[4],
                'queue_6' => $lists[5],
                'queue_7' => $lists[6],
                'queue_8' => $lists[7],
                'queue_9' => $lists[8],
                'queue_10' => $lists[9],
                'jumlah' => count($queues),
            ]);
        }

        $tmp = array_unique($tmp);

        $materials = Material::where('materials.mrpc', '=', $request->get('mrpc'))
            ->whereIn('materials.hpl', $request->get('hpl'))
            ->whereIn('materials.material_number', $tmp)
            ->select('material_number', db::raw("concat(material_number,'<br>',model,'_',`key`) as isi"))
            ->get();

        for ($i = 0; $i < count($boards); $i++) {
            foreach ($materials as $material) {
                if ($boards[$i]['sedang'] == $material->material_number) {
                    $boards[$i]['sedang'] = $material->isi;
                }

                if ($boards[$i]['akan'] == $material->material_number) {
                    $boards[$i]['akan'] = $material->isi;
                }

                if ($boards[$i]['queue_1'] == $material->material_number) {
                    $boards[$i]['queue_1'] = $material->isi;
                }

                if ($boards[$i]['queue_2'] == $material->material_number) {
                    $boards[$i]['queue_2'] = $material->isi;
                }

                if ($boards[$i]['queue_3'] == $material->material_number) {
                    $boards[$i]['queue_3'] = $material->isi;
                }

                if ($boards[$i]['queue_4'] == $material->material_number) {
                    $boards[$i]['queue_4'] = $material->isi;
                }

                if ($boards[$i]['queue_5'] == $material->material_number) {
                    $boards[$i]['queue_5'] = $material->isi;
                }

                if ($boards[$i]['queue_6'] == $material->material_number) {
                    $boards[$i]['queue_6'] = $material->isi;
                }

                if ($boards[$i]['queue_7'] == $material->material_number) {
                    $boards[$i]['queue_7'] = $material->isi;
                }

                if ($boards[$i]['queue_8'] == $material->material_number) {
                    $boards[$i]['queue_8'] = $material->isi;
                }

                if ($boards[$i]['queue_9'] == $material->material_number) {
                    $boards[$i]['queue_9'] = $material->isi;
                }

                if ($boards[$i]['queue_10'] == $material->material_number) {
                    $boards[$i]['queue_10'] = $material->isi;
                }

                if ($boards[$i]['selesai'] == $material->material_number) {
                    $boards[$i]['selesai'] = $material->isi;
                }
            }
        }

        $response = array(
            'status' => true,
            'boards' => $boards,
            'materials' => $materials,
        );
        return Response::json($response);
    }

    public function fetchBuffingReverse(Request $request)
    {
        $tmp = [];
        $boards = [];

        $work_stations = db::connection('digital_kanban')->table('dev_list')
            ->select('idx', 'dev_name', 'dev_operator_id', 'dev_akan_num', 'dev_sedang_num', 'dev_selesai_num', 'dev_selesai_detected')
            ->whereRaw('SPLIT_STRING(dev_name, "-", 1) = "SXKEY"')
            ->where('enable_antrian', '!=', 'RPR')
            ->orderBy('dev_name', 'asc')
            ->get();

        foreach ($work_stations as $ws) {
            $employee = Employee::where('employee_id', '=', $ws->dev_operator_id)->select('name')->first();

            if ($employee != null) {
                $employee_name = $employee->name;
            } else {
                $employee_name = "NotFound";
            }

            if ($ws->dev_selesai_detected == 1) {
                $selesai = $ws->dev_selesai_num;
                array_push($tmp, $ws->dev_selesai_num);
            } else {
                $selesai = "";
            }

            $queues_q = db::connection('digital_kanban')->table('buffing_queues')
                // ->where('rack', '=', $work_station->dev_name)
                ->whereRaw('rack = concat(SPLIT_STRING("' . $ws->dev_name . '", "-", 1), "-",SPLIT_STRING("' . $ws->dev_name . '", "-", 2))')
                ->orderBy('created_at', 'asc')
                ->limit(50)
                ->get();

            $queues = array();
            for ($i = 0; $i < count($queues_q); $i++) {
                if (isset($queues_q[$i])) {
                    array_push($queues, $queues_q[$i]->material_num);
                    array_push($tmp, $queues_q[$i]->material_num);
                } else {
                    array_push($queues, "");
                }
            }

            array_push($tmp, $ws->dev_sedang_num);
            array_push($tmp, $ws->dev_akan_num);

            array_push($boards, [
                'id' => $ws->idx,
                'ws' => $ws->dev_name,
                'employee_id' => $ws->dev_operator_id,
                'employee_name' => $employee_name,
                'sedang' => $ws->dev_sedang_num,
                'akan' => $ws->dev_akan_num,
                'selesai' => $selesai,
                'queues' => $queues,
            ]);

        }

        $tmp = array_unique($tmp);

        $materials = Material::where('materials.mrpc', '=', $request->get('mrpc'))
            ->whereIn('materials.hpl', $request->get('hpl'))
            ->whereIn('materials.material_number', $tmp)
            ->select('material_number', db::raw("concat(material_number,'<br>',model,'_',`key`) as isi"))
            ->get();

        $response = array(
            'status' => true,
            'boards' => $boards,
            'materials' => $materials,
        );
        return Response::json($response);
    }

    public function fetchMiddleBarrelReprint(Request $request)
    {
        if ($request->get('surface') == 'LCQ') {
            $barrels = Barrel::leftJoin('materials', 'materials.material_number', '=', 'barrels.material_number')
                ->where('materials.category', '=', 'WIP')
                ->where('materials.mrpc', '=', $request->get('mrpc'))
                ->whereIn('materials.hpl', $request->get('hpl'))
                ->where('materials.surface', 'like', '%LCQ')
                ->select('barrels.tag', 'barrels.remark', 'materials.key', 'materials.model', 'materials.surface', 'barrels.machine')
                ->get();
        }
        if ($request->get('surface') == 'PLT') {
            $barrels = MiddleInventory::leftJoin('materials', 'materials.material_number', '=', 'middle_inventories.material_number')
                ->where('materials.category', '=', 'WIP')
                ->where('materials.mrpc', '=', $request->get('mrpc'))
                ->whereIn('materials.hpl', $request->get('hpl'))
                ->where('materials.surface', 'like', '%PLT')
                ->where('middle_inventories.location', '=', 'barrel')
                ->select('middle_inventories.tag', 'materials.key', 'materials.model', 'materials.surface', 'middle_inventories.quantity')
                ->get();
        }

        $response = array(
            'status' => true,
            'barrels' => $barrels,
        );
        return Response::json($response);

    }

    public function scanMiddleOperator(Request $request)
    {

        $nik = $request->get('employee_id');

        if (strlen($nik) > 9) {
            $nik = substr($nik, 0, 9);
        }

        $employee = db::table('employees')->where('employee_id', 'like', '%' . $nik . '%')->first();

        if (count($employee) > 0) {
            $response = array(
                'status' => true,
                'message' => 'Logged In',
                'employee' => $employee,
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'message' => 'Employee ID Invalid',
            );
            return Response::json($response);
        }
    }

    public function scanMiddleOperatorKensa(Request $request)
    {
        $employee = db::table('employees')->where('tag', '=', $request->get('employee_id'))->first();

        if (count($employee) > 0) {
            $response = array(
                'status' => true,
                'message' => 'Logged In',
                'employee' => $employee,
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'message' => 'Employee Tag Invalid',
            );
            return Response::json($response);
        }
    }

    public function printMiddleBarrelReprint(Request $request)
    {

        if ($request->get('id') == 'PLT') {
            if ($request->get('tagMaterial') == null) {
                $response = array(
                    'status' => false,
                    'message' => 'No material selected',
                );
                return Response::json($response);
            }

            $middle_inventories = MiddleInventory::leftJoin('materials', 'materials.material_number', '=', 'middle_inventories.material_number')
                ->where('middle_inventories.location', '=', 'barrel');
            if ($request->get('tagMaterial') != null) {
                $middle_inventories = $middle_inventories->whereIn('middle_inventories.tag', $request->get('tagMaterial'));
            }

            $middle_inventories = $middle_inventories->select('middle_inventories.tag', 'middle_inventories.material_number', 'middle_inventories.quantity', 'materials.key', 'materials.model', 'materials.surface', 'materials.material_description', 'materials.hpl')->get();

            if ($middle_inventories == null) {
                $response = array(
                    'status' => false,
                    'message' => 'Material not found in barrel location.',
                );
                return Response::json($response);
            }

            foreach ($middle_inventories as $middle_inventory) {

                self::printSlipMaterial('PLATING', $middle_inventory->hpl, 'Reprint', $middle_inventory->model, $middle_inventory->key, $middle_inventory->surface, $middle_inventory->tag, $middle_inventory->material_number, $middle_inventory->material_description, $middle_inventory->quantity, '-', '-', '-');

            }

            $response = array(
                'status' => true,
                'message' => 'Qr code PLATING has been printed.',
                'tes' => $middle_inventories,
            );
            return Response::json($response);
        } else {
            if ($request->get('tagMaterial') == null && $request->get('tagMachine') == null) {
                $response = array(
                    'status' => false,
                    'message' => 'No material or machine selected',
                );
                return Response::json($response);
            }

            $barrels = Barrel::leftJoin('materials', 'materials.material_number', '=', 'barrels.material_number');
            if ($request->get('tagMaterial') != null) {
                $barrels = $barrels->whereIn('barrels.tag', $request->get('tagMaterial'));
            }
            if ($request->get('tagMachine') != null) {
                $barrels = $barrels->whereIn('barrels.remark', $request->get('tagMachine'));
            }

            $barrels = $barrels->select('barrels.tag', 'barrels.material_number', 'barrels.qty', 'materials.key', 'materials.model', 'materials.surface', 'materials.material_description', 'barrels.machine', 'barrels.jig', 'barrels.remark', 'materials.hpl')->get();

            if ($barrels == null) {
                $response = array(
                    'status' => false,
                    'message' => 'Material not found in barrel location.',
                );
                return Response::json($response);
            }

            if ($request->get('id') == 'material') {
                foreach ($barrels as $barrel) {
                    if ($barrel->machine == 'FLANEL') {
                        self::printSlipMaterial('LACQUERING', $barrel->hpl, 'Reprint', $barrel->model, $barrel->key, $barrel->surface, $barrel->tag, $barrel->material_number, $barrel->material_description, $barrel->quantity, '-', 'FLANEL', '-');
                    } else {
                        self::printSlipMaterial('LACQUERING', $barrel->hpl, 'Reprint', $barrel->model, $barrel->key, $barrel->surface, $barrel->tag, $barrel->material_number, $barrel->material_description, $barrel->quantity, $barrel->remark, $barrel->machine, $barrel->jig);
                    }
                }
                $response = array(
                    'status' => true,
                    'message' => 'Qr code has been printed.',
                );
                return Response::json($response);
            }
            if ($request->get('id') == 'machine') {
                $m = array();
                foreach ($barrels as $barrel) {
                    if (!in_array($barrel->remark, $m) && $barrel->remark != 'FLANEL') {
                        self::printSlipMachine($barrel->machine, $barrel->remark);
                        array_push($m, $barrel->remark);
                    }
                }
                $response = array(
                    'status' => true,
                    'message' => 'Qr code has been printed.',
                    'tes' => $barrels,
                );
                return Response::json($response);
            }
        }
    }

    public function fetchMiddleBarrelBoard(Request $request)
    {

        $hour = (int) date('H');

        if ($hour >= 0 && $hour <= 5) {
            $now = date('Y-m-d');
            $yesterday = date('Y-m-d', strtotime("yesterday"));

            $from = $yesterday . " 06:00:00";
            $to = $now . " 04:00:00";

            $barrel_board = DB::table('barrel_logs')
                ->leftJoin('materials', 'materials.material_number', '=', 'barrel_logs.material')
                ->where('barrel_logs.created_at', '>=', $from)
                ->where('barrel_logs.created_at', '<=', $to)
                ->where('materials.category', '=', 'WIP')
                ->where('materials.mrpc', '=', $request->get('mrpc'))
                ->whereIn('materials.hpl', $request->get('hpl'))
                ->select(
                    'materials.hpl',
                    'barrel_logs.status',
                    db::raw('sum(barrel_logs.qty) as qty'),
                    db::raw('IF(TIME(barrel_logs.created_at) > "00:00:00" and TIME(barrel_logs.created_at) < "04:00:00", 2, IF(TIME(barrel_logs.created_at) > "06:00:00" and TIME(barrel_logs.created_at) < "16:00:00", 1, IF(TIME(barrel_logs.created_at) > "16:00:00" and TIME(barrel_logs.created_at) < "23:59:59", 2, "ERROR"))) AS shift')
                )
                ->groupBy('materials.hpl', 'barrel_logs.status', 'barrel_logs.created_at')
                ->get();

        } else {
            $now = date('Y-m-d');
            $barrel_board = DB::table('barrel_logs')
                ->leftJoin('materials', 'materials.material_number', '=', 'barrel_logs.material')
                ->where(DB::raw('DATE_FORMAT(barrel_logs.created_at,"%Y-%m-%d")'), '=', $now)
                ->where('materials.category', '=', 'WIP')
                ->where('materials.mrpc', '=', $request->get('mrpc'))
                ->whereIn('materials.hpl', $request->get('hpl'))
                ->select('materials.hpl', 'barrel_logs.status', db::raw('sum(barrel_logs.qty) as qty'), db::raw('IF(TIME(barrel_logs.created_at) > "05:00:00" and TIME(barrel_logs.created_at) < "05:00:01", 3, IF(TIME(barrel_logs.created_at) > "06:00:00" and TIME(barrel_logs.created_at) < "16:00:00", 1, IF(TIME(barrel_logs.created_at) > "16:00:00" and TIME(barrel_logs.created_at) < "23:59:59", 2, "ERROR"))) AS shift'))
                ->groupBy('materials.hpl', 'barrel_logs.status', 'barrel_logs.created_at')
                ->get();
        }

        $barrel_queues = BarrelQueue::leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
            ->where('materials.category', '=', 'WIP')
            ->where('materials.mrpc', '=', $request->get('mrpc'))
            ->whereIn('materials.hpl', $request->get('hpl'))
            ->select('materials.model', 'materials.key', 'materials.surface', 'barrel_queues.quantity', 'barrel_queues.created_at', db::raw('coalesce(barrel_queues.remark, "-") as remark'))
            ->orderBy('barrel_queues.created_at', 'asc')
            ->get();

        $flanels = Barrel::leftJoin('materials', 'materials.material_number', '=', 'barrels.material_number')
            ->where('barrels.machine', '=', 'FLANEL')
            ->select('barrels.tag', 'materials.material_number', 'materials.model', 'materials.key', 'barrels.created_at')
            ->orderBy('barrels.created_at', 'asc')
            ->get();

        $response = array(
            'status' => true,
            'barrel_board' => $barrel_board,
            'barrel_queues' => $barrel_queues,
            'flanels' => $flanels,
        );
        return Response::json($response);
    }

    public function fetchMiddleBarrel(Request $request)
    {
        if ($request->get('surface') == 'LCQ') {
            $queues = BarrelQueue::leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
                ->leftJoin('barrel_jigs', function ($join) {
                    $join->on('barrel_jigs.key', '=', 'materials.key');
                    $join->on('barrel_jigs.hpl', '=', 'materials.hpl');
                })
                ->leftJoin(db::raw('(select bom_components.material_parent, bom_components.material_child, materials.material_description from bom_components left join materials on materials.material_number = bom_components.material_child) as bom_components'), 'bom_components.material_parent', '=', 'barrel_queues.material_number')
                ->where('materials.category', '=', 'WIP')
                ->where('materials.mrpc', '=', $request->get('mrpc'))
                ->whereIn('materials.hpl', $request->get('hpl'))
                ->where('materials.surface', 'not like', '%PLT')
                ->select('barrel_queues.tag', 'barrel_queues.created_at', 'materials.model', 'materials.hpl', 'materials.material_number', 'materials.key', 'materials.surface', 'barrel_queues.quantity', 'barrel_jigs.spring', 'bom_components.material_child', 'bom_components.material_description', 'barrel_jigs.lot')
                ->orderBy('barrel_queues.created_at', 'asc')
                ->get();
            if ($queues[0]->spring == 'FLANEL') {
                $code = 'FLANEL';
            } else {
                $code = 'BARREL';
            }
        } elseif ($request->get('surface') == 'PLT') {
            $queues = BarrelQueue::leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
                ->leftJoin(db::raw('(select bom_components.material_parent, bom_components.material_child, materials.material_description from bom_components left join materials on materials.material_number = bom_components.material_child) as bom_components'), 'bom_components.material_parent', '=', 'barrel_queues.material_number')
                ->where('materials.category', '=', 'WIP')
                ->where('materials.mrpc', '=', $request->get('mrpc'))
                ->whereIn('materials.hpl', $request->get('hpl'))
                ->where('materials.surface', 'like', '%PLT')
                ->select('barrel_queues.tag', 'materials.key', 'materials.model', 'materials.surface', 'bom_components.material_child', 'bom_components.material_description', 'barrel_queues.quantity', 'barrel_queues.created_at')
                ->orderBy('barrel_queues.created_at', 'asc')
                // ->limit(60)
                ->get();
            $code = 'PLT';
        } elseif ($request->get('surface') == 'FLANEL') {
            $queues = BarrelQueue::leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
                ->leftJoin(db::raw('(select bom_components.material_parent, bom_components.material_child, materials.material_description from bom_components left join materials on materials.material_number = bom_components.material_child) as bom_components'), 'bom_components.material_parent', '=', 'barrel_queues.material_number')
                ->where('materials.category', '=', 'WIP')
                ->where('materials.mrpc', '=', $request->get('mrpc'))
                ->whereIn('materials.hpl', $request->get('hpl'))
                ->where('materials.surface', 'not like', '%PLT')
                ->select('barrel_queues.tag', 'materials.key', 'materials.model', 'materials.surface', 'bom_components.material_child', 'bom_components.material_description', 'barrel_queues.quantity', 'barrel_queues.created_at')
                ->orderBy('barrel_queues.created_at', 'asc')
                // ->limit(30)
                ->get();
            $code = 'FLANEL';
        }

        $response = array(
            'status' => true,
            'code' => $code,
            'queues' => $queues,
        );
        return Response::json($response);
    }

    public function fetchMiddleBarrelMachine(Request $request)
    {
        $queue = db::table('barrel_queues')
            ->leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
            ->where('materials.category', '=', 'WIP')
            ->where('materials.hpl', '=', $request->get('hpl'))
            ->where('materials.mrpc', '=', $request->get('mrpc'))
            ->orderBy('barrel_queues.created_at', 'asc')
            ->first();

        if ($queue != null) {
            if (strpos($queue->surface, 'PLT') !== false) {
                $no_machine = 'Direct To Plating';
                $capacity = '0';
            } else {
                $machine = db::table('barrel_machines')->where('status', '=', 'idle')->orderBy('updated_at', 'asc')->first();

                if ($machine != null) {
                    $no_machine = $machine->machine;
                    $capacity = db::table('barrel_jigs')->where('hpl', '=', $request->get('hpl'))->count();
                } else {
                    $no_machine = 'FULL';
                    $capacity = '0';
                }
            }

            $response = array(
                'status' => true,
                'no_machine' => $no_machine,
                'capacity' => $capacity,
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => true,
                'no_machine' => 'No Queue',
                'capacity' => 0,
            );
            return Response::json($response);
        }

    }

    public function printMiddleBarrel(Request $request)
    {
        $id = Auth::id();
        $tags = $request->get('tag');

        $count = 0;
        foreach ($tags as $tag) {
            $check = BarrelQueue::where('barrel_queues.tag', '=', $tag[0])->first();
            $count += 1;

            if ($check == null) {
                $response = array(
                    'status' => false,
                    'message' => 'Selected tag not in queue, please refresh page',
                );
                return Response::json($response);
            }
        }

        $check2 = BarrelQueue::leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')->count();

        if ($check2 >= 64 && ($check2 - $count) < 64) {
            // self::sendEmailMinQueue();
        }

        DB::beginTransaction();

        if ($request->get('surface') == 'LCQ') {
            if ($request->get('code') == 'FLANEL') {
                try {
                    foreach ($tags as $tag) {
                        $barrel = new Barrel([
                            'machine' => $request->get('code'),
                            'jig' => 0,
                            'tag' => $tag[0],
                            'status' => $request->get('code'),
                            'remark' => $request->get('code'),
                            'created_by' => $id,
                        ]);
                        $barrel->save();
                    }
                    foreach ($tags as $tag) {
                        DB::statement("update barrels left join barrel_queues on barrel_queues.tag = barrels.tag left join materials on materials.material_number = barrel_queues.material_number set barrels.key = materials.key, barrels.material_number = barrel_queues.material_number, barrels.qty = barrel_queues.quantity, barrels.remark2 = barrel_queues.remark where barrels.tag = '" . $tag[0] . "'");
                    }
                    foreach ($tags as $tag) {
                        $barrel = Barrel::leftJoin('materials', 'materials.material_number', '=', 'barrels.material_number')
                            ->where('barrels.tag', '=', $tag[0])
                            ->select('materials.hpl', 'barrels.remark2', 'materials.model', 'materials.key', 'materials.surface', 'barrels.tag', 'barrels.material_number', 'materials.material_description', 'barrels.qty')
                            ->first();

                        $group = explode('-', $barrel->key);
                        $rack = 'SXKEY-' . $group[0];

                        if (preg_match("/82/", $barrel->model) != true) {

                            $buffing = db::table('bom_components')->where('material_parent', '=', $barrel->material_number)->first();

                            $buffing_queue = DB::connection('digital_kanban')
                                ->table('buffing_queues')
                                ->insert([
                                    'rack' => $rack,
                                    'material_num' => $buffing->material_child,
                                    'created_by' => $id,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'material_qty' => $barrel->qty,
                                    'material_tag_id' => $tag[0],
                                ]);
                        } else {
                            $buffing = db::table('bom_components')->where('material_parent', '=', $barrel->material_number)->first();

                            $buffing_queue = DB::connection('digital_kanban')
                                ->table('buffing_queues')
                                ->insert([
                                    'rack' => 'SXKEY-82',
                                    'material_num' => $buffing->material_child,
                                    'created_by' => $id,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'material_qty' => $barrel->qty,
                                    'material_tag_id' => $tag[0],
                                ]);
                        }

                        MiddleInventory::firstOrcreate(
                            [
                                'tag' => $tag[0],
                            ],
                            [
                                'tag' => $tag[0],
                                'material_number' => $barrel->material_number,
                                'quantity' => $barrel->qty,
                                'location' => 'barrel',
                            ]
                        );

                        self::printSlipMaterial('LACQUERING', $barrel->hpl, $barrel->remark2, $barrel->model, $barrel->key, $barrel->surface, $barrel->tag, $barrel->material_number, $barrel->material_description, $barrel->qty, '-', $request->get('code'), '-');
                    }
                    foreach ($tags as $tag) {
                        BarrelQueue::where('tag', '=', $tag[0])->forceDelete();
                    }

                    DB::commit();
                    $response = array(
                        'status' => true,
                        'message' => 'ID Slip for FLANEL has been printed',
                    );
                    return Response::json($response);

                } catch (\Exception $e) {
                    DB::rollback();
                    $error_log = new ErrorLog([
                        'error_message' => $e->getMessage(),
                        'created_by' => $id,
                    ]);
                    $error_log->save();
                    return Response::json($response);
                }
            } else {
                $code_generator = CodeGenerator::where('note', '=', 'barrel_machine')->first();
                $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
                $qr_machine = $code_generator->prefix . $number;
                $code_generator->index = $code_generator->index + 1;
                $code_generator->save();

                try {

                    foreach ($tags as $tag) {
                        $barrel = new Barrel([
                            'machine' => $request->get('no_machine'),
                            'jig' => $tag[1],
                            'tag' => $tag[0],
                            'status' => 'racking',
                            'remark' => $qr_machine,
                            'created_by' => $id,
                        ]);
                        $barrel->save();
                    }

                    foreach ($tags as $tag) {
                        DB::statement("update barrels left join barrel_queues on barrel_queues.tag = barrels.tag left join materials on materials.material_number = barrel_queues.material_number set barrels.key = materials.key, barrels.material_number = barrel_queues.material_number, barrels.qty = barrel_queues.quantity, barrels.remark2 = barrel_queues.remark where barrels.tag = '" . $tag[0] . "'");
                    }

                    foreach ($tags as $tag) {
                        $barrel = Barrel::leftJoin('materials', 'materials.material_number', '=', 'barrels.material_number')
                            ->where('barrels.tag', '=', $tag[0])
                            ->select('materials.hpl', 'barrels.remark2', 'materials.model', 'materials.key', 'materials.surface', 'barrels.tag', 'barrels.material_number', 'materials.material_description', 'barrels.qty', 'barrels.remark', 'barrels.machine', 'barrels.jig')
                            ->first();

                        $group = explode('-', $barrel->key);
                        $rack = 'SXKEY-' . $group[0];

                        if (preg_match("/82/", $barrel->model) != true) {

                            $buffing = db::table('bom_components')->where('material_parent', '=', $barrel->material_number)->first();

                            $buffing_queue = DB::connection('digital_kanban')
                                ->table('buffing_queues')
                                ->insert([
                                    'rack' => $rack,
                                    'material_num' => $buffing->material_child,
                                    'created_by' => $id,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'material_qty' => $barrel->qty,
                                    'material_tag_id' => $tag[0],
                                ]);

                        } else {
                            $buffing = db::table('bom_components')->where('material_parent', '=', $barrel->material_number)->first();

                            $buffing_queue = DB::connection('digital_kanban')
                                ->table('buffing_queues')
                                ->insert([
                                    'rack' => 'SXKEY-82',
                                    'material_num' => $buffing->material_child,
                                    'created_by' => $id,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'material_qty' => $barrel->qty,
                                    'material_tag_id' => $tag[0],
                                ]);
                        }

                        MiddleInventory::firstOrcreate(
                            [
                                'tag' => $tag[0],
                            ],
                            [
                                'tag' => $tag[0],
                                'material_number' => $barrel->material_number,
                                'quantity' => $barrel->qty,
                                'location' => 'barrel',
                            ]
                        );

                        self::printSlipMaterial('LACQUERING', $barrel->hpl, $barrel->remark2, $barrel->model, $barrel->key, $barrel->surface, $barrel->tag, $barrel->material_number, $barrel->material_description, $barrel->qty, $barrel->remark, $barrel->machine, $barrel->jig);
                    }

                    foreach ($tags as $tag) {
                        BarrelQueue::where('tag', '=', $tag[0])->forceDelete();
                    }

                    DB::commit();
                    $response = array(
                        'status' => true,
                        'message' => 'ID Slip for LACUQERING has been printed',
                    );
                    return Response::json($response);

                } catch (\Exception $e) {
                    DB::rollback();
                    $error_log = new ErrorLog([
                        'error_message' => $e->getMessage(),
                        'created_by' => $id,
                    ]);
                    $error_log->save();
                    return Response::json($response);
                }
            }

        } elseif ($request->get('surface') == 'PLT') {
            try {
                foreach ($tags as $tag) {
                    $barrel = BarrelQueue::leftJoin('materials', 'materials.material_number', 'barrel_queues.material_number')
                        ->where('barrel_queues.tag', '=', $tag[0])
                        ->select('barrel_queues.tag', 'barrel_queues.material_number', 'barrel_queues.quantity', 'materials.model', 'materials.hpl', 'materials.key', 'materials.surface', 'materials.material_description', db::raw('SPLIT_STRING(barrel_queues.remark, "+", 1) as remark'))
                        ->first();

                    $group = explode('-', $barrel->key);
                    $rack = 'SXKEY-' . $group[0];

                    if (preg_match("/82/", $barrel->model) != true) {

                        $buffing = db::table('bom_components')->where('material_parent', '=', $barrel->material_number)->first();

                        $buffing_queue = DB::connection('digital_kanban')
                            ->table('buffing_queues')
                            ->insert([
                                'rack' => $rack,
                                'material_num' => $buffing->material_child,
                                'created_by' => $id,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                                'material_qty' => $barrel->quantity,
                                'material_tag_id' => $tag[0],
                            ]);

                    } else {
                        $buffing = db::table('bom_components')->where('material_parent', '=', $barrel->material_number)->first();

                        $buffing_queue = DB::connection('digital_kanban')
                            ->table('buffing_queues')
                            ->insert([
                                'rack' => 'SXKEY-82',
                                'material_num' => $buffing->material_child,
                                'created_by' => $id,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                                'material_qty' => $barrel->quantity,
                                'material_tag_id' => $tag[0],
                            ]);
                    }

                    $barrel_log = new BarrelLog([
                        'machine' => $request->get('surface'),
                        'tag' => $tag[0],
                        'material' => $barrel->material_number,
                        'qty' => $barrel->quantity,
                        'status' => $request->get('surface'),
                        'started_at' => date('Y-m-d H:i:s'),
                        'created_by' => $id,
                    ]);
                    $barrel_log->save();

                    MiddleInventory::firstOrcreate(
                        [
                            'tag' => $tag[0],
                        ],
                        [
                            'tag' => $tag[0],
                            'material_number' => $barrel->material_number,
                            'quantity' => $barrel->quantity,
                            'location' => 'barrel',
                        ]
                    );

                    self::printSlipMaterial('PLATING', $barrel->hpl, $barrel->remark, $barrel->model, $barrel->key, $barrel->surface, $tag[0], $barrel->material_number, $barrel->material_description, $barrel->quantity, '-', '-', '-');
                }
                foreach ($tags as $tag) {
                    BarrelQueue::where('tag', '=', $tag[0])->forceDelete();
                }

                DB::commit();
                $response = array(
                    'status' => true,
                    'message' => 'ID Slip for PLATING has been printed',
                );
                return Response::json($response);
            } catch (\Exception $e) {
                DB::rollback();
                $error_log = new ErrorLog([
                    'error_message' => $e->getMessage(),
                    'created_by' => $id,
                ]);
                $error_log->save();
                return Response::json($response);
            }

        } elseif ($request->get('surface') == 'FLANEL') {
            try {
                foreach ($tags as $tag) {
                    $barrel = new Barrel([
                        'machine' => $request->get('code'),
                        'jig' => 0,
                        'tag' => $tag[0],
                        'status' => $request->get('code'),
                        'remark' => $request->get('code'),
                        'created_by' => $id,
                    ]);
                    $barrel->save();
                }
                foreach ($tags as $tag) {
                    DB::statement("update barrels left join barrel_queues on barrel_queues.tag = barrels.tag left join materials on materials.material_number = barrel_queues.material_number set barrels.key = materials.key, barrels.material_number = barrel_queues.material_number, barrels.qty = barrel_queues.quantity, barrels.remark2 = barrel_queues.remark where barrels.tag = '" . $tag[0] . "'");
                }
                foreach ($tags as $tag) {
                    $barrel = Barrel::leftJoin('materials', 'materials.material_number', '=', 'barrels.material_number')
                        ->where('barrels.tag', '=', $tag[0])
                        ->select('materials.hpl', 'barrels.remark2', 'materials.model', 'materials.key', 'materials.surface', 'barrels.tag', 'barrels.material_number', 'materials.material_description', 'barrels.qty')
                        ->first();

                    $group = explode('-', $barrel->key);
                    $rack = 'SXKEY-' . $group[0];

                    if (preg_match("/82/", $barrel->model) != true) {

                        $buffing = db::table('bom_components')->where('material_parent', '=', $barrel->material_number)->first();

                        $buffing_queue = DB::connection('digital_kanban')
                            ->table('buffing_queues')
                            ->insert([
                                'rack' => $rack,
                                'material_num' => $buffing->material_child,
                                'created_by' => $id,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                                'material_qty' => $barrel->qty,
                                'material_tag_id' => $tag[0],
                            ]);
                    } else {
                        $buffing = db::table('bom_components')->where('material_parent', '=', $barrel->material_number)->first();

                        $buffing_queue = DB::connection('digital_kanban')
                            ->table('buffing_queues')
                            ->insert([
                                'rack' => 'SXKEY-82',
                                'material_num' => $buffing->material_child,
                                'created_by' => $id,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                                'material_qty' => $barrel->qty,
                                'material_tag_id' => $tag[0],
                            ]);
                    }

                    MiddleInventory::firstOrcreate(
                        [
                            'tag' => $tag[0],
                        ],
                        [
                            'tag' => $tag[0],
                            'material_number' => $barrel->material_number,
                            'quantity' => $barrel->qty,
                            'location' => 'barrel',
                        ]
                    );

                    self::printSlipMaterial('LACQUERING', $barrel->hpl, $barrel->remark2, $barrel->model, $barrel->key, $barrel->surface, $barrel->tag, $barrel->material_number, $barrel->material_description, $barrel->qty, '-', $request->get('code'), '-');
                }
                foreach ($tags as $tag) {
                    BarrelQueue::where('tag', '=', $tag[0])->forceDelete();
                }

                DB::commit();
                $response = array(
                    'status' => true,
                    'message' => 'ID Slip for FLANEL has been printed',
                );
                return Response::json($response);
            } catch (\Exception $e) {
                DB::rollback();
                $error_log = new ErrorLog([
                    'error_message' => $e->getMessage(),
                    'created_by' => $id,
                ]);
                $error_log->save();
                return Response::json($response);
            }

        }
    }

    public function updateKanban(Request $request)
    {
        $idx = $request->get('idx');
        $no_kanban = $request->get('no_kanban');

        try {
            $kanban = db::connection('digital_kanban')
                ->table('buffing_inventories')
                ->where('idx', $idx)
                ->update([
                    'no_kanban' => $no_kanban,
                ]);

            $response = array(
                'status' => true,
                'message' => 'Update kanban success',
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

    public function scanMiddleBarrel(Request $request)
    {
        $id = Auth::id();

        if (substr($request->get('qr'), 0, 3) == 'MCB' || substr($request->get('qr'), 0, 3) == 'mcb') {
            $barrels = Barrel::where('remark', '=', $request->get('qr'))->get();

            if ($barrels->count() > 0) {
                $barrel_machine = BarrelMachine::where('machine', '=', $barrels[0]->machine)->first();
                if ($barrel_machine->status == 'idle' && $barrels[0]->status == 'queue') {
                    try {
                        $update_barrel = Barrel::where('remark', '=', $request->get('qr'))->update([
                            'status' => 'running',
                            'finish_queue' => date('Y-m-d H:i:s'),
                        ]);

                        $insert_machine_log = new BarrelMachineLog([
                            'machine' => $barrels[0]->machine,
                            'status' => 'idle',
                            'started_at' => date('Y-m-d H:i:s', strtotime($barrel_machine->updated_at)),
                            'created_by' => $id,
                        ]);
                        $insert_machine_log->save();

                        $update_barrel_machine = BarrelMachine::where('machine', '=', $barrels[0]->machine)->update([
                            'status' => 'running',
                        ]);

                        $response = array(
                            'status' => true,
                            'message' => 'Machine running.',
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
                if ($barrel_machine->status == 'running' && $barrels[0]->status == 'running') {
                    try {
                        $insert_machine_log = new BarrelMachineLog([
                            'machine' => $barrels[0]->machine,
                            'status' => 'running',
                            'started_at' => date('Y-m-d H:i:s', strtotime($barrel_machine->updated_at)),
                            'created_by' => $id,
                        ]);
                        $insert_machine_log->save();

                        foreach ($barrels as $barrel) {
                            $insert_log = [
                                'machine' => $barrel->machine,
                                'tag' => $barrel->tag,
                                'material' => $barrel->material_number,
                                'qty' => $barrel->qty,
                                'status' => 'reset',
                                'started_at' => date('Y-m-d H:i:s', strtotime($barrel->finish_queue)),
                                'created_by' => $id,
                            ];

                            $barrel_log = new BarrelLog($insert_log);

                            $delete_barrel = Barrel::where('tag', '=', $barrel->tag)->where('machine', '=', $barrel->machine)->forceDelete();
                            $barrel_log->save();
                        }

                        $update_barrel_machine = BarrelMachine::where('machine', '=', $barrels[0]->machine)->update([
                            'status' => 'idle',
                        ]);

                        $response = array(
                            'status' => true,
                            'message' => 'Material has been resetted',
                        );
                        return Response::json($response);

                    } catch (\Exception $e) {
                        $response = array(
                            'status' => false,
                            'message' => $e->getMessage(),
                        );
                        return Response::json($response);
                    }
                } else {
                    $response = array(
                        'status' => false,
                        'message' => 'Machine status invalid.',
                    );
                    return Response::json($response);
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Qr code cycle is done.',
                );
                return Response::json($response);
            }
        } else {
            $barrel = Barrel::where('tag', '=', $request->get('qr'))->first();

            if ($barrel == null) {
                $response = array(
                    'status' => false,
                    'message' => 'Qr code not registered.',
                );
                return Response::json($response);
            }

            if ($barrel->status == 'racking') {
                try {
                    $barrel->finish_racking = date('Y-m-d H:i:s');
                    $barrel->status = 'queue';

                    $insert_log = [
                        'machine' => $barrel->machine,
                        'tag' => $barrel->tag,
                        'material' => $barrel->material_number,
                        'qty' => $barrel->qty,
                        'status' => 'set',
                        'started_at' => date('Y-m-d H:i:s', strtotime($barrel->created_at)),
                        'created_by' => $id,
                    ];

                    $barrel_log = new BarrelLog($insert_log);

                    DB::transaction(function () use ($barrel, $barrel_log) {
                        $barrel->save();
                        $barrel_log->save();
                    });

                    $check_barrels = Barrel::where('remark', '=', $barrel->remark)->where('status', '<>', 'queue')->get();

                    if ($check_barrels->count() == 0) {

                        self::printSlipMachine($barrel->machine, $barrel->remark);

                        $response = array(
                            'status' => true,
                            'message' => 'All material has been racked, printing machine label.',
                        );
                        return Response::json($response);
                    }

                    $response = array(
                        'status' => true,
                        'message' => 'Material has been racked',
                    );
                    return Response::json($response);

                } catch (\Exception $e) {
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                    return Response::json($response);
                }
            } elseif ($barrel->status == 'FLANEL') {
                try {
                    $insert_log = [
                        'machine' => 'FLANEL',
                        'tag' => $barrel->tag,
                        'material' => $barrel->material_number,
                        'qty' => $barrel->qty,
                        'status' => 'reset',
                        'started_at' => date('Y-m-d H:i:s', strtotime($barrel->created_at)),
                        'created_by' => $id,
                    ];

                    $barrel_log = new BarrelLog($insert_log);

                    $barrel_log->save();
                    $delete_barrel = Barrel::where('tag', '=', $barrel->tag)->where('machine', '=', 'FLANEL')->forceDelete();

                    $response = array(
                        'status' => true,
                        'message' => 'Material has been resetted',
                    );
                    return Response::json($response);
                } catch (\Exception $e) {
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                    return Response::json($response);
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'QR code status invalid.',
                );
                return Response::json($response);
            }
        }
    }

    public function fetchMiddleKensa(Request $request)
    {

        $emp = $request->get('employee_id');
        $loc = $request->get('location');

        $result = [];
        $ng = [];

        if (str_contains($loc, 'bff')) {
            $result = DB::select("select SUM(quantity) as qty, hpl from
				(select material_number, sum(quantity) as quantity from middle_buffing_logs where employee_id = '" . $emp . "' and DATE_FORMAT(created_at,'%Y-%m-%d') = '" . date('Y-m-d') . "' and location = '" . $request->get('location') . "' group by material_number) as base
				left join materials on materials.material_number = base.material_number
				group by hpl");

            $ng = DB::select("select SUM(quantity) as qty, hpl from
				(select material_number, sum(quantity) as quantity from middle_buffing_ng_logs where employee_id = '" . $emp . "' and DATE_FORMAT(created_at,'%Y-%m-%d') = '" . date('Y-m-d') . "' and location = '" . $request->get('location') . "' group by material_number) as base
				left join materials on materials.material_number = base.material_number
				group by hpl");

        } elseif (str_contains($loc, 'lcq')) {
            $result = DB::select("select SUM(quantity) as qty, hpl from
				(select material_number, sum(quantity) as quantity from middle_lacquering_logs where employee_id = '" . $emp . "' and DATE_FORMAT(created_at,'%Y-%m-%d') = '" . date('Y-m-d') . "' and location = '" . $request->get('location') . "' group by material_number) as base
				left join materials on materials.material_number = base.material_number
				group by hpl");

            $ng = DB::select("select SUM(quantity) as qty, hpl from
				(select material_number, sum(quantity) as quantity from middle_lacquering_ng_logs where employee_id = '" . $emp . "' and DATE_FORMAT(created_at,'%Y-%m-%d') = '" . date('Y-m-d') . "' and location = '" . $request->get('location') . "' group by material_number) as base
				left join materials on materials.material_number = base.material_number
				group by hpl");

        } elseif (str_contains($loc, 'plt')) {
            $result = DB::select("select SUM(quantity) as qty, hpl from
				(select material_number, sum(quantity) as quantity from middle_plating_logs where employee_id = '" . $emp . "' and DATE_FORMAT(created_at,'%Y-%m-%d') = '" . date('Y-m-d') . "' and location = '" . $request->get('location') . "' group by material_number) as base
				left join materials on materials.material_number = base.material_number
				group by hpl");

            $ng = DB::select("select SUM(quantity) as qty, hpl from
				(select material_number, sum(quantity) as quantity from middle_plating_ng_logs where employee_id = '" . $emp . "' and DATE_FORMAT(created_at,'%Y-%m-%d') = '" . date('Y-m-d') . "' and location = '" . $request->get('location') . "' group by material_number) as base
				left join materials on materials.material_number = base.material_number
				group by hpl");
        } else {
            $result = DB::select("SELECT resume.surface, resume.hpl, SUM(resume.quantity) AS quantity FROM
				(SELECT m.surface, m.hpl, l.quantity AS quantity FROM middle_lacquering_check_logs l
				LEFT JOIN materials m ON m.material_number = l.material_number
				WHERE l.employee_id = '" . $emp . "'
				AND l.location = '" . $request->get('location') . "'
				AND DATE(l.created_at) = '" . date('Y-m-d') . "'
				UNION ALL
				SELECT m.surface, m.hpl, l.quantity AS quantity FROM middle_plating_check_logs l
				LEFT JOIN materials m ON m.material_number = l.material_number
				WHERE l.employee_id = '" . $emp . "'
				AND l.location = '" . $request->get('location') . "'
				AND DATE(l.created_at) = '" . date('Y-m-d') . "'
				) AS resume
				GROUP BY resume.surface, resume.hpl");

            $ng = DB::select("SELECT resume.surface, resume.hpl, SUM(resume.quantity) AS quantity FROM
				(SELECT m.surface, m.hpl, l.quantity AS quantity FROM middle_lacquering_ng_logs l
				LEFT JOIN materials m ON m.material_number = l.material_number
				WHERE l.employee_id = '" . $emp . "'
				AND l.location = '" . $request->get('location') . "'
				AND DATE(l.created_at) = '" . date('Y-m-d') . "'
				UNION ALL
				SELECT m.surface, m.hpl, l.quantity AS quantity FROM middle_plating_ng_logs l
				LEFT JOIN materials m ON m.material_number = l.material_number
				WHERE l.employee_id = '" . $emp . "'
				AND l.location = '" . $request->get('location') . "'
				AND DATE(l.created_at) = '" . date('Y-m-d') . "'
				) AS resume
				GROUP BY resume.surface, resume.hpl");
        }

        $response = array(
            'status' => true,
            'result' => $result,
            'ng' => $ng,
        );
        return Response::json($response);
    }

    public function ScanMiddleKensa(Request $request)
    {
        $id = Auth::id();
        $started_at = date('Y-m-d H:i:s');

        $middle_inventory = MiddleInventory::where('tag', '=', $request->get('tag'))
            ->leftJoin('materials', 'materials.material_number', '=', 'middle_inventories.material_number')
            ->select(
                'materials.material_description',
                'materials.model',
                'materials.key',
                'materials.surface',
                'materials.issue_storage_location',
                'middle_inventories.material_number',
                'middle_inventories.location',
                'middle_inventories.quantity',
                'middle_inventories.tag',
                'middle_inventories.updated_at'
            )
            ->first();

        $enthole_inventory = db::connection('ympimis_2')->table('middle_enthols')->where('tag', $request->get('tag'))->where('location', 'enthole')->first();
        if (count($enthole_inventory) > 0) {
            if ($enthole_inventory->quantity > 0) {
                $response = array(
                    'status' => false,
                    'message' => 'Material Belum Selesai Cuci Enthole',
                );
                return Response::json($response);
            }
        }

        if ($middle_inventory) {
            // if( str_contains($request->get('loc'), 'lcq') && str_contains($middle_inventory->surface, 'plt') ){
            //     $response = array(
            //         'status' => false,
            //         'message' => 'ID slip location for LACQUERING, please check ID slip.',
            //     );
            //     return Response::json($response);
            // }

            // if( str_contains($request->get('loc'), 'plt') && (str_contains(strtolower($middle_inventory->surface), 'lcq') || str_contains(strtolower($middle_inventory->surface), 'w')) ){
            //     $response = array(
            //         'status' => false,
            //         'message' => 'ID slip location for PLATING, please check ID slip.',
            //     );
            //     return Response::json($response);
            // }

            if (str_contains($request->get('loc'), 'plt') && !str_contains($request->get('loc'), 'acc')) {
                $location = explode('-', $request->get('loc'));
                if (!str_contains($middle_inventory->issue_storage_location, strtoupper($location[2]))) {
                    $response = array(
                        'status' => false,
                        'message' => 'Wrong Location',
                    );
                    return Response::json($response);
                }
            }

            // if($request->get('loc') == $middle_inventory->location){
            //     $response = array(
            //         'status' => false,
            //         'message' => 'Wrong process flow'
            //     );
            //     return Response::json($response);
            // }

            if (str_contains($request->get('loc'), 'incoming')) {
                if (!in_array($middle_inventory->location, $this->before_incoming)) {

                    $d1 = strtotime($started_at);
                    $d2 = strtotime($middle_inventory->updated_at);
                    $totalMinuteDiff = abs($d1 - $d2) / 60;

                    if ($totalMinuteDiff < 30) {
                        $response = array(
                            'status' => false,
                            'message' => 'Kanban tidak dapat di CS, tunggu 30 menit lagi',
                        );
                        return Response::json($response);
                    }
                }
            }

            $response = array(
                'status' => true,
                'message' => 'ID slip found.',
                'middle_inventory' => $middle_inventory,
                'started_at' => $started_at,
            );
            return Response::json($response);
        } else {

            $completion = db::connection('mysql2')
                ->table('completions')
                ->leftJoin('materials', 'materials.id', '=', 'completions.material_id')
                ->where('completions.barcode_number', $request->get('tag'))
                ->first();

            if ($completion) {

                try {
                    $insert = new MiddleInventory([
                        'tag' => $completion->barcode_number,
                        'material_number' => $completion->material_number,
                        'location' => 'new',
                        'quantity' => $completion->lot_completion,
                    ]);
                    $insert->save();

                } catch (Exception $e) {
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                    return Response::json($response);
                }

                $middle_inventory = MiddleInventory::where('tag', '=', $request->get('tag'))
                    ->leftJoin('materials', 'materials.material_number', '=', 'middle_inventories.material_number')
                    ->select(
                        'materials.material_description',
                        'materials.model',
                        'materials.key',
                        'materials.surface',
                        'materials.issue_storage_location',
                        'middle_inventories.material_number',
                        'middle_inventories.location',
                        'middle_inventories.quantity',
                        'middle_inventories.tag'
                    )
                    ->first();

                if ($request->get('loc') == $middle_inventory->location) {
                    $response = array(
                        'status' => false,
                        'message' => 'Wrong process flow',
                    );
                    return Response::json($response);
                }

                $response = array(
                    'status' => true,
                    'message' => 'ID slip found.',
                    'middle_inventory' => $middle_inventory,
                    'started_at' => $started_at,
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'ID slip not found.',
                );
                return Response::json($response);
            }
        }
    }

    public function inputMiddleRework(Request $request)
    {

        $middle_rework_log = new MiddleReworkLog([
            'employee_id' => $request->get('employee_id'),
            'tag' => $request->get('tag'),
            'material_number' => $request->get('material_number'),
            'quantity' => $request->get('quantity'),
            'location' => $request->get('loc'),
            'started_at' => $request->get('started_at'),
        ]);

        try {
            $middle_rework_log->save();
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'message' => 'Rework time has been recorded.',
        );
        return Response::json($response);
    }

    public function inputMiddleKensa(Request $request)
    {

        $loc = $request->get('loc');
        $code_generator = CodeGenerator::where('note', '=', 'middle-kensa')->first();
        $code = $code_generator->index + 1;
        $code_generator->index = $code;
        $code_generator->save();

        if ($request->get('ng')) {
            foreach ($request->get('ng') as $ng) {

                $middle_ng_log;
                if (str_contains($loc, 'lcq')) {
                    $middle_ng_log = new MiddleLacqueringNgLog([
                        'employee_id' => $request->get('employee_id'),
                        'tag' => $request->get('tag'),
                        'material_number' => $request->get('material_number'),
                        'ng_name' => $ng[0],
                        'quantity' => $ng[1],
                        'location' => $request->get('loc'),
                        'started_at' => $request->get('started_at'),
                        'remark' => $code,
                    ]);
                } else if (str_contains($loc, 'plt')) {
                    $middle_ng_log = new MiddlePlatingNgLog([
                        'employee_id' => $request->get('employee_id'),
                        'tag' => $request->get('tag'),
                        'material_number' => $request->get('material_number'),
                        'ng_name' => $ng[0],
                        'quantity' => $ng[1],
                        'location' => $request->get('loc'),
                        'started_at' => $request->get('started_at'),
                        'remark' => $code,
                    ]);
                }

                try {
                    $middle_ng_log->save();
                } catch (\Exception $e) {
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                    return Response::json($response);
                }
            }

            $middle_check_log = [];
            if (str_contains($loc, 'lcq')) {
                $middle_check_log = new MiddleLacqueringCheckLog([
                    'employee_id' => $request->get('employee_id'),
                    'tag' => $request->get('tag'),
                    'material_number' => $request->get('material_number'),
                    'quantity' => $request->get('quantity'),
                    'location' => $request->get('loc'),
                ]);
            } else if (str_contains($loc, 'plt')) {
                $middle_check_log = new MiddlePlatingCheckLog([
                    'employee_id' => $request->get('employee_id'),
                    'tag' => $request->get('tag'),
                    'material_number' => $request->get('material_number'),
                    'quantity' => $request->get('quantity'),
                    'location' => $request->get('loc'),
                ]);
            }

            $middle_temp_log = new MiddleTempLog([
                'material_number' => $request->get('material_number'),
                'quantity' => $request->get('quantity'),
                'location' => $request->get('loc'),
            ]);

            try {
                DB::transaction(function () use ($middle_check_log, $middle_temp_log) {
                    $middle_check_log->save();
                    $middle_temp_log->save();
                });

                $response = array(
                    'status' => true,
                    'message' => 'NG has been recorded.',
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

        if (!$request->get('ng')) {
            $middle_inventory = MiddleInventory::where('tag', '=', $request->get('tag'))->first();
            $middle_inventory->location = $request->get('loc');
            $middle_inventory->last_check = $request->get('employee_id');
            $middle_inventory->updated_at = date('Y-m-d H:i:s');

            $middle_log = [];
            if (str_contains($loc, 'lcq')) {
                $middle_log = new MiddleLacqueringLog([
                    'employee_id' => $request->get('employee_id'),
                    'tag' => $request->get('tag'),
                    'material_number' => $request->get('material_number'),
                    'quantity' => $request->get('quantity'),
                    'location' => $request->get('loc'),
                    'started_at' => $request->get('started_at'),
                ]);
            } else if (str_contains($loc, 'plt')) {
                $middle_log = new MiddlePlatingLog([
                    'employee_id' => $request->get('employee_id'),
                    'tag' => $request->get('tag'),
                    'material_number' => $request->get('material_number'),
                    'quantity' => $request->get('quantity'),
                    'location' => $request->get('loc'),
                    'started_at' => $request->get('started_at'),
                ]);
            }

            try {
                DB::transaction(function () use ($middle_log, $middle_inventory) {
                    $middle_log->save();
                    $middle_inventory->save();
                });

                //Completion
                // if(in_array($request->get('loc'), $this->completions)){

                //     $bom = BomTransaction::where('material_parent', $request->get('material_number'))->first();

                //     if($bom){
                //         $material = db::connection('mysql2')
                //         ->table('materials')
                //         ->where('material_number', '=', $bom->material_child)
                //         ->first();

                //         if(!$material){
                //             $mpdl = MaterialPlantDataList::where('material_number', $bom->material_child)->first();

                //             if($mpdl){
                //                 $material = db::connection('mysql2')
                //                 ->table('materials')
                //                 ->insert([
                //                     "material_number" => $mpdl->material_number,
                //                     "description" => $mpdl->material_description,
                //                     "location" => $mpdl->storage_location,
                //                     "lead_time" => 90,
                //                     "user_id" => 1,
                //                     'created_at' => date("Y-m-d H:i:s"),
                //                     'updated_at' => date("Y-m-d H:i:s")
                //                 ]);

                //                 $material = db::connection('mysql2')
                //                 ->table('materials')
                //                 ->where('material_number', '=', $bom->material_child)
                //                 ->first();

                //             }else{
                //                 $response = array(
                //                     'status' => false,
                //                     'message' => 'Material child not found',
                //                 );
                //                 return Response::json($response);
                //             }

                //         }

                //         $completion = db::connection('mysql2')
                //         ->table('histories')
                //         ->insert([
                //             "category" => "completion",
                //             "completion_barcode_number" => $middle_inventory->tag,
                //             "completion_description" => $material->description,
                //             "completion_location" => $material->location,
                //             "completion_issue_plant" => "8190",
                //             "completion_material_id" => $material->id,
                //             "completion_reference_number" => "",
                //             "lot" => $request->get('quantity'),
                //             "synced" => 0,
                //             'user_id' => "1",
                //             'created_at' => date("Y-m-d H:i:s"),
                //             'updated_at' => date("Y-m-d H:i:s")
                //         ]);

                //     }else{
                //         try{
                //             $error_log = new ErrorLog([
                //                 'error_message' => 'ERRORBOM_'. $request->get('material_number') .'_'. $request->get('quantity'),
                //                 'created_by' => Auth::id()
                //             ]);
                //             $error_log->save();

                //         }catch(\Exception $e){
                //             $response = array(
                //                 'status' => false,
                //                 'message' => $e->getMessage(),
                //             );
                //             return Response::json($response);
                //         }
                //     }
                // }

                $temp = MiddleTempLog::where('material_number', '=', $request->get('material_number'))
                    ->where('location', '=', $request->get('loc'))
                    ->first();

                if (count($temp) > 0) {
                    $delete = MiddleTempLog::where('material_number', '=', $request->get('material_number'))
                        ->where('location', '=', $request->get('loc'))
                        ->first();

                    $delete->delete();
                } else {
                    $middle_check_log = [];
                    if (str_contains($loc, 'lcq')) {
                        $middle_check_log = new MiddleLacqueringCheckLog([
                            'employee_id' => $request->get('employee_id'),
                            'tag' => $request->get('tag'),
                            'material_number' => $request->get('material_number'),
                            'quantity' => $request->get('quantity'),
                            'location' => $request->get('loc'),
                        ]);
                    } else if (str_contains($loc, 'plt')) {
                        $middle_check_log = new MiddlePlatingCheckLog([
                            'employee_id' => $request->get('employee_id'),
                            'tag' => $request->get('tag'),
                            'material_number' => $request->get('material_number'),
                            'quantity' => $request->get('quantity'),
                            'location' => $request->get('loc'),
                        ]);
                    }

                    try {
                        $middle_check_log->save();
                    } catch (\Exception $e) {
                        $response = array(
                            'status' => false,
                            'message' => $e->getMessage(),
                        );
                        return Response::json($response);
                    }
                }

                $response = array(
                    'status' => true,
                    'message' => 'Input material successfull.',
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

    public function fetchProcessBarrelMachine()
    {
        $data = DB::table('barrel_machines')->select('machine', 'status', 'updated_at', DB::raw('now() as now'), DB::raw('TIMEDIFF(now(),updated_at) as duration'), DB::raw('ROUND(TIME_TO_SEC(TIMEDIFF(now(),updated_at)) / 60 / 60, 1) as hour'))->get();

        $data2 = DB::table('barrels')
            ->leftJoin('materials', 'barrels.material_number', '=', 'materials.material_number')
            ->select('barrels.key', 'barrels.machine', DB::raw('CONCAT(materials.model," ",materials.surface) as content'))
            ->get();

        $response = array(
            'status' => true,
            'datas' => $data,
            'contents' => $data2,
        );
        return Response::json($response);
    }

    public function fetchProcessBarrelMachineContent()
    {
        $data = DB::table('barrels')
            ->leftJoin('materials', 'barrels.material_number', '=', 'materials.material_number')
            ->select('barrels.key', 'barrels.machine', DB::raw('CONCAT(materials.model," ",materials.surface) as content'))
            ->get();

        $response = array(
            'status' => true,
            'contents' => $data,
        );
        return Response::json($response);
    }

    public function fetchProcessBarrel()
    {
        $data = DB::table('barrels')
            ->leftJoin('materials', 'barrels.material_number', '=', 'materials.material_number')
            ->select('barrels.machine', 'barrels.jig', 'barrels.key', DB::raw('SUM(qty) as qty'), 'barrels.status', 'materials.model', DB::raw('GROUP_CONCAT(barrels.tag) as tag'))
            ->groupBy('barrels.machine', 'barrels.jig', 'barrels.key', 'barrels.status', 'materials.model')
            ->orderBy('remark', 'asc')
            ->orderBy('jig', 'asc')
            ->get();

        $barrel_machine = DB::table('barrel_machines')
            ->select('machine', 'status', DB::raw('hour(TIMEDIFF(now(),updated_at)) as jam'), DB::raw('minute(TIMEDIFF(now(),updated_at)) as menit'), DB::raw('SECOND(TIMEDIFF(now(),updated_at)) as detik'), DB::raw('hour(TIMEDIFF(DATE_ADD(updated_at, INTERVAL 3 HOUR),now())) as jam_cd'), DB::raw('minute(TIMEDIFF(DATE_ADD(updated_at, INTERVAL 3 HOUR),now())) as menit_cd'), DB::raw('second(TIMEDIFF(DATE_ADD(updated_at, INTERVAL 3 HOUR),now())) as detik_cd'))
            ->get();

        $response = array(
            'status' => true,
            'datas' => $data,
            'machine_stat' => $barrel_machine,
        );
        return Response::json($response);
    }

    public function fetchMachine()
    {
        $barrel_machine = DB::table('barrel_machines')
            ->select('machine', 'status', DB::raw('hour(TIMEDIFF(now(),updated_at)) as jam'), DB::raw('minute(TIMEDIFF(now(),updated_at)) as menit'), DB::raw('SECOND(TIMEDIFF(now(),updated_at)) as detik'))
            ->get();

        $response = array(
            'status' => true,
            'machine_stat' => $barrel_machine,
        );
        return Response::json($response);
    }

    public function postProcessMiddleReturn(Request $request)
    {
        $tag = $request->get('qr');
        $barrel_inventories = DB::table('middle_inventories')
            ->select('tag', 'material_number', 'location', 'quantity', 'last_check')
            ->where('tag', '=', $tag)
            ->get();

        $created = DB::table('barrel_queues')
            ->select(DB::raw("created_at - INTERVAL 5 SECOND as created_at"))
            ->orderBy('created_at', 'asc')
            ->limit(1)
            ->get();

        DB::table('barrel_queues')->insert([
            'tag' => $request->get('qr'),
            'material_number' => $barrel_inventories[0]->material_number,
            'remark' => "return+" . $barrel_inventories[0]->location,
            'quantity' => $barrel_inventories[0]->quantity,
            'created_at' => $created[0]->created_at,
            'updated_at' => $created[0]->created_at,
        ]);

        $middle_return_log = new MiddleReturnLog([
            'tag' => $request->get('qr'),
            'material_number' => $barrel_inventories[0]->material_number,
            'quantity' => $barrel_inventories[0]->quantity,
            'location' => $barrel_inventories[0]->location,
            'employee_id' => $barrel_inventories[0]->last_check,
        ]);

        $middle_return_log->save();

        DB::table('middle_inventories')->where('tag', '=', $tag)->delete();

        try {
            DB::table('barrels')->where('tag', '=', $tag)->delete();
        } catch (\Exception $e) {

        }

        $response = array(
            'status' => true,
        );
        return Response::json($response);
    }

    public function fetchProcessMiddleReturnBody(Request $request)
    {
        $arr = ['BELL', 'BELLBOW', 'BODY', 'BOW', 'FOOT', 'HEAD', 'NECK'];
        $logs = MiddleReturnLog::leftJoin('materials', 'materials.material_number', 'middle_return_logs.material_number')
            ->whereIn('middle_return_logs.remark', $arr)
            ->orderBy('middle_return_logs.created_at', 'desc')
            ->select('middle_return_logs.tag', 'middle_return_logs.material_number', 'materials.material_description', 'materials.model', 'materials.key', 'materials.surface', 'middle_return_logs.quantity', 'middle_return_logs.created_at')
            ->limit(200)
            ->get();

        $response = array(
            'status' => true,
            'datas' => $logs,
        );
        return Response::json($response);
    }

    public function fetchProcessMiddleReturn(Request $request)
    {
        $barrel_queues = DB::table('barrel_queues')->leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
            ->select('barrel_queues.tag', 'materials.model', 'materials.key', 'materials.surface', 'materials.material_number', 'materials.material_description', 'barrel_queues.quantity', 'barrel_queues.created_at', 'barrel_queues.remark')
            ->where('materials.category', '=', 'WIP')
            ->where('materials.mrpc', '=', $request->get('mrpc'))
            ->whereIn('materials.hpl', $request->get('hpl'))
            ->where('barrel_queues.remark', 'LIKE', 'return%')
            ->orderBy('created_at', 'asc')
            ->get();

        $response = array(
            'status' => true,
            'datas' => $barrel_queues,
        );
        return Response::json($response);
    }

    public function postReturnInventory(Request $request)
    {
        $tag = $request->get('tag');
        $inventory = new MiddleInventory([
            'tag' => $tag,
            'material_number' => $request->get('material'),
            'location' => $request->get('location'),
            'quantity' => $request->get('quantity'),
        ]);

        $inventory->save();

        DB::table('barrel_queues')->where('tag', '=', $tag)->delete();

        $response = array(
            'status' => true,
        );
        return Response::json($response);
    }

    public function fetchBarrelAdjustment()
    {
        $adjust = BarrelQueue::leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
            ->select('barrel_queues.tag', 'barrel_queues.material_number', 'materials.material_description', 'barrel_queues.quantity', 'barrel_queues.created_at')
            ->orderBy('barrel_queues.created_at', 'asc')
            ->get();

        return DataTables::of($adjust)
            ->addColumn('check', function ($adjust) {
                return '<input type="checkbox" class="queue" id="' . $adjust->tag . '+' . $adjust->material_number . '+' . $adjust->quantity . '+inactive" onclick="inactive(this)">';
            })
            ->rawColumns(['check' => 'check'])
            ->make(true);
    }

    public function fetchBarrelInactive($id)
    {
        $inactive = BarrelQueueInactive::leftJoin('materials', 'materials.material_number', '=', 'barrel_queue_inactives.material_number')
            ->select('barrel_queue_inactives.tag', 'barrel_queue_inactives.material_number', 'materials.material_description', 'barrel_queue_inactives.quantity', 'barrel_queue_inactives.created_at')
            ->orderBy('barrel_queue_inactives.created_at', 'asc')
            ->get();

        if ($id == "kanban") {
            return DataTables::of($inactive)
                ->addColumn('check', function ($inactive) {
                    return '<input type="checkbox" class="aktif" id="' . $inactive->tag . '+' . $inactive->material_number . '+' . $inactive->quantity . '+active" onclick="active(this)">';
                })
                ->rawColumns(['check' => 'check'])
                ->make(true);
        } else {
            return DataTables::of($inactive)
                ->make(true);
        }
    }

    public function postInactive(Request $request)
    {

        $datas = $request->get('data')[0];

        for ($i = 0; $i < count($datas['tag']); $i++) {
            if ($datas['stat'][$i] == 'inactive') {
                DB::table('barrel_queues')->where('tag', '=', $datas['tag'][$i])->delete();

                $inactive = new BarrelQueueInactive([
                    'tag' => $datas['tag'][$i],
                    'material_number' => $datas['material'][$i],
                    'quantity' => $datas['qty'][$i],
                ]);
                $inactive->save();
            } else if ($datas['stat'][$i] == "active") {
                DB::table('barrel_queue_inactives')->where('tag', '=', $datas['tag'][$i])->delete();

                $active = db::table('barrel_queues')->insert([
                    'tag' => $datas['tag'][$i],
                    'material_number' => $datas['material'][$i],
                    'quantity' => $datas['qty'][$i],
                    'remark' => 'up',
                    'created_at' => $datas['created_at'][$i],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_by' => Auth::id(),
                ]);
            }
        }

        $response = array(
            'status' => true,
            // 'tes' => $datas['tag']
        );
        return Response::json($response);
    }

    public function fetchBarrelBoardDetails(Request $request)
    {
        $sif = $request->get('shift');
        $now = date('Y-m-d');

        if ($sif == 1) {
            $awal = '07:00:00';
            $akhir = '16:00:00';
        } elseif ($sif == 2) {
            $awal = '16:00:00';
            $akhir = '23:59:59';
        } elseif ($sif == 3) {
            $awal = '00:00:00';
            $akhir = '07:00:00';
        }

        $detailPerolehan = db::table('materials')
            ->leftJoin('barrel_logs', 'materials.material_number', '=', 'barrel_logs.material')
            ->where('materials.category', '=', 'WIP')
            ->where('materials.mrpc', '=', $request->get('mrpc'))
            ->whereIn('materials.hpl', $request->get('hpl'))
            ->where('materials.surface', 'like', '%' . $request->get('surface'))
            ->where('materials.hpl', $request->get('key'))
            ->where(db::raw("DATE_FORMAT(barrel_logs.created_at,'%Y-%m-%d')"), "=", $now)
            ->where(db::raw("DATE_FORMAT(barrel_logs.created_at,'%H:%i:%s')"), '>=', $awal)
            ->where(db::raw("DATE_FORMAT(barrel_logs.created_at,'%H:%i:%s')"), '<', $akhir)
            ->select('materials.model', 'materials.key', db::raw("SUM(IF(barrel_logs.`status`='set',qty,0)) as `set`"), db::raw("SUM(IF(barrel_logs.`status`='reset',qty,0)) as `reset`"), db::raw("SUM(IF(barrel_logs.`status`='plt',qty,0)) as `plt`"))
            ->groupBy('materials.model', 'materials.key')
            ->get();

        $response = array(
            'status' => true,
            'datas' => $detailPerolehan,
        );
        return Response::json($response);
    }

    public function CreateInactive(Request $request)
    {
        try {
            $inactive = new BarrelQueueInactive([
                'tag' => $request->get('tag'),
                'material_number' => $request->get('material'),
                'quantity' => $request->get('quantity'),
            ]);
            $inactive->save();
            $status = true;
        } catch (Exception $e) {
            $status = false;
        }

        $response = array(
            'status' => $status,
        );
        return Response::json($response);
    }

    public function importInactive(Request $request)
    {
        if ($request->hasFile('inactive_material')) {
            $file = $request->file('inactive_material');

            $data = file_get_contents($file);

            $rows = explode("\r\n", $data);
            foreach ($rows as $row) {
                if (strlen($row) > 0) {
                    $row = explode("\t", $row);
                    $inactive = new BarrelQueueInactive([
                        'tag' => $row[0],
                        'material_number' => $row[1],
                        'quantity' => $row[2],
                    ]);

                    $inactive->save();
                }
            }
            return redirect('/index/middle/barrel_adjustment')->with('status', 'New Inactive materials has been imported.')->with('page', 'Middle Process');
        } else {
            return redirect('/index/middle/barrel_adjustment')->with('error', 'Please select a file.')->with('page', 'Middle Process');
        }
    }

    public function fetchWIP()
    {
        $adjust = MiddleInventory::leftJoin('materials', 'materials.material_number', '=', 'middle_inventories.material_number')
            ->where('middle_inventories.location', '!=', 'stockroom')
            ->select('middle_inventories.tag', 'middle_inventories.material_number', 'materials.material_description', 'middle_inventories.quantity', 'middle_inventories.location', 'middle_inventories.created_at')
            ->orderBy('middle_inventories.created_at', 'desc')
            ->get();

        return DataTables::of($adjust)
            ->addColumn('check', function ($adjust) {
                return '<input type="checkbox" class="queue" id="' . $adjust->tag . '+' . $adjust->material_number . '+' . $adjust->quantity . '+inactive" onclick="inactive(this)">';
            })
            ->rawColumns(['check' => 'check'])
            ->make(true);
    }

    public function postInactiveWIP(Request $request)
    {

        $datas = $request->get('data')[0];

        for ($i = 0; $i < count($datas['tag']); $i++) {
            DB::table('middle_inventories')->where('tag', '=', $datas['tag'][$i])->delete();

            $inactive = new BarrelQueueInactive([
                'tag' => $datas['tag'][$i],
                'material_number' => $datas['material'][$i],
                'quantity' => $datas['qty'][$i],
            ]);
            $inactive->save();
        }

        $response = array(
            'status' => true,
        );
        return Response::json($response);
    }

    public function sendEmailMinQueue()
    {
        $mail_to = db::table('send_emails')
            ->where('remark', '=', 'middle')
            ->WhereNull('deleted_at')
            ->orWhere('remark', '=', 'superman')
            ->WhereNull('deleted_at')
            ->select('email')
            ->get();

        $barrel_queues = BarrelQueue::leftJoin('materials', 'materials.material_number', '=', 'barrel_queues.material_number')
            ->where('materials.category', '=', 'WIP')
            ->where('materials.mrpc', '=', 'S51')
            ->whereIn('materials.hpl', ['ASKEY', 'TSKEY'])
            ->select('barrel_queues.tag', 'barrel_queues.material_number', 'materials.model', 'materials.key', 'materials.surface', 'barrel_queues.quantity', 'barrel_queues.created_at', db::raw('coalesce(barrel_queues.remark, "-") as remark'))
            ->orderBy('barrel_queues.created_at', 'asc')
            ->get();

        $queues = [
            'barrel_queues' => $barrel_queues,
            'barrel_count' => count($barrel_queues),
        ];

        Mail::to($mail_to)->send(new SendEmail($queues, 'min_queue'));
    }

    public function printKanban($sloc, $material_number, $no_kanban)
    {

        if ($sloc == 'CL51') {
            $material = db::table('materials')
                ->where('material_number', $material_number)
                ->first();

            $bom = db::table('bom_transactions')
                ->leftJoin('material_plant_data_lists', 'material_plant_data_lists.material_number', '=', 'bom_transactions.material_child')
                ->where('bom_transactions.material_parent', $material_number)
                ->select(
                    'bom_transactions.material_child',
                    'material_plant_data_lists.material_description'
                )
                ->get();

            // return view('processes.middle.buffing_kanban_cl_print', array(
            //     'sloc' => $sloc,
            //     'bom' => $bom,
            //     'material' => $material,
            //     'no_kanban' => $no_kanban,
            // ));

            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('CR-80', 'portrait');
            $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

            $pdf->loadView(
                'processes.middle.buffing_kanban_cl_print',
                array(
                    'sloc' => $sloc,
                    'bom' => $bom,
                    'material' => $material,
                    'no_kanban' => $no_kanban,
                )
            );
            return $pdf->stream("kanban.pdf");

        } else {
            return view('404');
        }

    }

    public function printSlipMaterial($location, $hpl, $remark, $model, $key, $surface, $tag, $material_number, $material_description, $quantity, $qr_machine, $machine, $jig)
    {
        if (Auth::user()->role_code == "OP-Barrel-SX") {
            $printer_name = 'Barrel-Printer';
        }
        if (in_array(Auth::user()->username, ['PI2009022'])) {
            $printer_name = 'MIS';
        }

        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(1, 1);
        $printer->text('ID SLIP ' . date('d-M-Y H:i:s') . " " . $remark . "\n");
        $printer->setTextSize(4, 4);
        $printer->setUnderline(true);
        $printer->text($location . "\n\n");
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(5, 2);
        if ($hpl == 'TSKEY') {
            $printer->setEmphasis(true);
            $printer->setReverseColors(true);
        }
        $printer->text($model . " " . $key . "\n");
        $printer->text($surface . "\n\n");
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->qrCode($tag, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
        $printer->text($tag . "\n\n");
        $printer->initialize();
        $printer->setTextSize(1, 1);
        $printer->text("GMC : " . $material_number . "                           " . $qr_machine . "\n");
        $printer->text("DESC: " . $material_description . "\n");
        $printer->text("QTY : " . $quantity . " PC(S)                 MACHINE: " . $machine . " JIG: " . $jig . "\n");
        $printer->cut(Printer::CUT_PARTIAL, 50);
        $printer->close();
    }

    public function printSlipMachine($machine, $qr_machine)
    {
        if (Auth::user()->role_code == "OP-Barrel-SX") {
            $printer_name = 'Barrel-Printer';
        }
        if (in_array(Auth::user()->username, ['PI2009022'])) {
            $printer_name = 'MIS';
        }

        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(4, 4);
        $printer->text('BARREL' . "\n");
        $printer->text("MACHINE_" . $machine . "\n");
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->qrCode($qr_machine, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
        $printer->text($qr_machine . "\n\n");
        $printer->cut();
        $printer->close();
    }

    public function fetchScanBuffingInOut(Request $request)
    {
        $now = date('Y-m-d H:i:s');
        $kanban = $request->get("tag");
        $other = substr($kanban, 1);
        $status = $request->get("trx_status");

        $location = '';
        if (strtoupper($status) == 'IN') {
            $location = 'STORE';
        } elseif (strtoupper($status) == 'OUT') {
            $location = 'QUEUE';
        }

        $tags = db::connection('digital_kanban')->table('buffing_inventories')
            ->whereIn('material_tag_id', [$kanban, $other])
            ->first();

        if (!$tags) {
            $response = array(
                'status' => false,
                'message' => 'RFID Invalid',
            );
            return Response::json($response);
        }

        $material = Material::where("material_number", "=", $tags->material_num)->first();

        try {

            $buffing_inventory = db::connection('digital_kanban')
                ->table('buffing_inventories')
                ->where('idx', '=', $tags->idx)
                ->update([
                    'lokasi' => $location,
                    'updated_at' => $now,
                ]);

            $response = array(
                'status' => true,
                'now' => $now,
                'material' => $material,
                'tags' => $tags,
            );
            return Response::json($response);

        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'datas' => $e->getMessage(),
                'message' => 'RFID Invalid',
            );
            return Response::json($response);
        }

    }

    public function fetchBuffing(Request $request)
    {

        $started_at = date('Y-m-d H:i:s');
        $kanban = $request->get("tag");
        $other = substr($kanban, 1);
        $loc = explode('-', $request->get("location"));
        $loc = $loc[2];

        $tags = db::connection('digital_kanban')->table('buffing_inventories')
            ->whereIn('material_tag_id', [$kanban, $other])
            ->first();

        if (!$tags) {
            $response = array(
                'status' => false,
                'message' => 'RFID Invalid',
            );
            return Response::json($response);
        }

        $material = Material::where("material_number", "=", $tags->material_num)->first();

        if (!str_contains($material->issue_storage_location, strtoupper($loc))) {
            $response = array(
                'status' => false,
                'message' => 'Wrong Location',
            );
            return Response::json($response);
        }

        $operator = Employee::select("name")
            ->where("employee_id", "=", $tags->operator_id)
            ->first();

        try {

            $buffing_inventory = db::connection('digital_kanban')->table('buffing_inventories')
                ->where('idx', '=', $tags->idx)
                ->update([
                    'lokasi' => 'BUFFING-KENSA',
                ]);

            $response = array(
                'status' => true,
                'datas' => $tags,
                'material' => $material,
                'operator' => $operator,
                'started_at' => $started_at,
            );
            return Response::json($response);

        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'datas' => $e->getMessage(),
                'message' => 'RFID Invalid',
            );
            return Response::json($response);
        }
    }

    public function inputBodyReturn(Request $request)
    {

        $now = date('Y-m-d H:i:s');
        $completion = db::connection('mysql2')
            ->table('completions')
            ->leftJoin('materials', 'materials.id', '=', 'completions.material_id')
            ->where('barcode_number', '=', $request->get('qr'))
            ->select(
                'completions.*',
                'materials.material_number',
                'materials.remark'
            )
            ->first();

        if (!$completion) {
            $response = array(
                'status' => false,
                'message' => 'Barcode Invalid',
            );
            return Response::json($response);
        }

        $control = [
            'BELL',
            'BELLBOW',
            'BODY',
            'BOW',
            'FOOT',
            'HEAD',
            'NECK',
        ];

        if (!in_array($completion->remark, $control)) {
            $response = array(
                'status' => false,
                'message' => 'Material bukan Body, Neck, Head, Foot, Bell, Bellbow, Bow',
            );
            return Response::json($response);
        }

        $inv = db::table('middle_inventories')
            ->where('tag', '=', $request->get('qr'))
            ->first();

        $location = '';
        if ($inv) {
            $location = $inv->location;
        }

        DB::beginTransaction();
        try {
            $delete_inv = db::table('middle_inventories')
                ->where('tag', '=', $request->get('qr'))
                ->delete();

            $insert_log = db::table('middle_return_logs')
                ->insert([
                    'tag' => $request->get('qr'),
                    'material_number' => $completion->material_number,
                    'quantity' => $completion->lot_completion,
                    'location' => $location,
                    'remark' => $completion->remark,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

            DB::commit();
            $response = array(
                'status' => true,
                'message' => 'Return Success',
            );
            return Response::json($response);

        } catch (\Exception $e) {

            DB::rollback();

            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);

        }

    }

    public function inputBuffingKensa(Request $request)
    {
        $code_generator = CodeGenerator::where('note', '=', 'middle-kensa')->first();
        $code = $code_generator->index + 1;
        $code_generator->index = $code;
        $code_generator->save();

        if ($request->get('ng')) {
            foreach ($request->get('ng') as $ng) {
                $middle_ng_log = new MiddleBuffingNgLog([
                    'employee_id' => $request->get('employee_id'),
                    'tag' => $request->get('tag'),
                    'material_number' => $request->get('material_number'),
                    'ng_name' => $ng[0],
                    'quantity' => $ng[1],
                    'location' => $request->get('loc'),
                    'buffing_time' => $request->get('buffing_time'),
                    'operator_id' => $request->get('operator_id'),
                    'started_at' => $request->get('started_at'),
                    'remark' => $code,
                ]);

                try {
                    $middle_ng_log->save();
                } catch (\Exception $e) {
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                    return Response::json($response);
                }
            }

            $middle_check_log = new MiddleBuffingCheckLog([
                'employee_id' => $request->get('employee_id'),
                'tag' => $request->get('tag'),
                'material_number' => $request->get('material_number'),
                'quantity' => $request->get('cek'),
                'location' => $request->get('loc'),
                'operator_id' => $request->get('operator_id'),
                'buffing_time' => $request->get('buffing_time'),
            ]);

            $middle_temp_log = new MiddleTempLog([
                'material_number' => $request->get('material_number'),
                'operator_id' => $request->get('operator_id'),
                'quantity' => $request->get('cek'),
                'location' => $request->get('loc'),
            ]);

            try {
                DB::transaction(function () use ($middle_check_log, $middle_temp_log) {
                    $middle_check_log->save();
                    $middle_temp_log->save();
                });

                $response = array(
                    'status' => true,
                    'message' => 'NG has been recorded.',
                );
                return Response::json($response);
            } catch (\Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        } else {
            try {
                $buffing_inventory = db::connection('digital_kanban')->table('buffing_inventories')
                    ->where('material_tag_id', '=', $request->get('tag'))
                    ->update([
                        'lokasi' => 'BUFFING-AFTER',
                    ]);

                $middle_log = new MiddleBuffingLog([
                    'employee_id' => $request->get('employee_id'),
                    'tag' => $request->get('tag'),
                    'material_number' => $request->get('material_number'),
                    'quantity' => $request->get('quantity'),
                    'location' => $request->get('loc'),
                    'operator_id' => $request->get('operator_id'),
                    'buffing_time' => $request->get('buffing_time'),
                    'started_at' => $request->get('started_at'),
                ]);
                $middle_log->save();

                $temp = MiddleTempLog::where('material_number', '=', $request->get('material_number'))
                    ->where('location', '=', $request->get('loc'))
                    ->where('operator_id', '=', $request->get('operator_id'))
                    ->first();

                if (count($temp) > 0) {
                    $delete = MiddleTempLog::where('material_number', '=', $request->get('material_number'))
                        ->where('location', '=', $request->get('loc'))
                        ->where('operator_id', '=', $request->get('operator_id'))
                        ->first();

                    $delete->delete();
                } else {
                    $middle_check_log = new MiddleBuffingCheckLog([
                        'employee_id' => $request->get('employee_id'),
                        'tag' => $request->get('tag'),
                        'material_number' => $request->get('material_number'),
                        'quantity' => $request->get('cek'),
                        'location' => $request->get('loc'),
                        'operator_id' => $request->get('operator_id'),
                        'buffing_time' => $request->get('buffing_time'),
                    ]);
                    $middle_check_log->save();
                }

                $response = array(
                    'status' => true,
                    'message' => 'Input material successfull.',
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

    public function scanRequestTag(Request $request)
    {

        $material_number = substr($request->get('tag'), 2, 7);
        $material = Material::where('materials.material_number', '=', $material_number)->first();

        if ($material) {
            if ($material->hpl == 'ASKEY' && preg_match("/82/", $material->model) != true) {
                $quantity = 15;
            } else if ($material->hpl == 'TSKEY' && preg_match("/82/", $material->model) != true) {
                $quantity = 8;
            } else if ($material->hpl == 'ASKEY' && preg_match("/82/", $material->model) == true) {
                $quantity = 10;
            } else {
                $response = array(
                    'status' => false,
                    'message' => "Material Belum Didukung ",
                );
                return Response::json($response);
            }

            try {
                $helper = MiddleRequestHelper::where("material_tag", "=", $request->get('tag'))->first();

                if ($helper) {
                    // $last = new DateTime($helper->updated_at);
                    // $now = new DateTime();

                    $time = new DateTime($helper->updated_at);
                    $diff = $time->diff(new DateTime());
                    $interval = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;

                    // echo $minutes;

                    if ($interval < 10) {
                        $response = array(
                            'status' => false,
                            'message' => "Interval scan terlalu cepat",
                        );
                        return Response::json($response);
                    }
                }

                $helper_log = MiddleRequestHelper::updateOrCreate(
                    ['material_tag' => $request->get('tag')],
                    ['material_tag' => $request->get('tag'), 'material_number' => $material_number, 'created_by' => Auth::id(), 'updated_at' => Carbon::now()]
                );

                $material_request = MiddleMaterialRequest::firstOrNew([
                    'material_number' => $material_number,
                ]);
                $material_request->quantity = ($material_request->quantity + $quantity);

                $log = new MiddleRequestLog([
                    'material_number' => $material_number,
                    'material_tag' => $request->get('tag'),
                    'quantity' => $quantity,
                    'created_by' => Auth::id(),
                ]);

                DB::transaction(function () use ($material_request, $log) {
                    $material_request->save();
                    $log->save();
                });

                $response = array(
                    'status' => true,
                    'message' => 'Material berhasil di request ke maekotei.',
                );
                return Response::json($response);

            } catch (QueryException $e) {
                $response = array(
                    'status' => false,
                    'message' => $e,
                );
                return Response::json($response);
            }
        } else {
            $response = array(
                'status' => false,
                'message' => "Material Tidak Ditemukan ",
            );
            return Response::json($response);
        }
    }

    public function fetchRequest(Request $request)
    {
        $filter = "";

        if ($request->get('filter') != '') {
            $filter = " and materials.hpl = '" . $request->get('filter') . "'";
        }
        if ($request->get('filter') == '82Z') {
            $filter = " and materials.model like '%82%'";
        }

        $solders = db::connection('ympimis_2')->select("
            SELECT material_number, count(material_number) as qty FROM weldings
            WHERE LENGTH(material_number) > 0
            GROUP BY material_number");

        $cucis = db::connection('ympimis_2')->select("
            SELECT material_number, count(material_number) as qty FROM welding_inventories
            WHERE location = 'cuci-asam'
            AND date(updated_at) = '" . date('Y-m-d') . "'
            GROUP BY material_number");

        $kensas = db::select("select material_number, count(material_number) as qty from welding_inventories
			where location like '%hsa%'
			group by material_number");

        $requests = db::select("SELECT
			middle_material_requests.material_number,
			materials.model,
			materials.key,
			material_volumes.lot_transfer,
			middle_material_requests.quantity / material_volumes.lot_transfer AS kanban,
			middle_material_requests.quantity,
			COALESCE ( kitto.kanban, 0 ) AS inventory_kanban,
			COALESCE ( kitto.quantity, 0 ) AS inventory_quantity
			FROM
			ympimis.middle_material_requests
			LEFT JOIN (
			SELECT
			kitto.inventories.material_number,
			count( kitto.inventories.material_number ) AS kanban,
			sum( kitto.inventories.lot ) AS quantity
			FROM
			kitto.inventories
			WHERE
			kitto.inventories.lot > 0
			GROUP BY
			kitto.inventories.material_number
			) AS kitto ON kitto.material_number = ympimis.middle_material_requests.material_number
			LEFT JOIN material_volumes ON material_volumes.material_number = middle_material_requests.material_number
			LEFT JOIN materials ON materials.material_number = middle_material_requests.material_number
			WHERE
			middle_material_requests.quantity <> 0
			AND materials.origin_group_code = '" . $request->get('origin_group_code') . "'" . $filter . "
			ORDER BY
			middle_material_requests.quantity / material_volumes.lot_transfer DESC,
			materials.key ASC,
			materials.model ASC");

        $log_request = array();
        foreach ($requests as $request) {

            $qty_solder = 0;
            foreach ($solders as $solder) {
                if ($request->material_number == $solder->material_number) {
                    $qty_solder = $solder->qty;
                }
            }

            $qty_cuci = 0;
            foreach ($cucis as $cuci) {
                if ($request->material_number == $cuci->material_number) {
                    $qty_cuci = $cuci->qty;
                }
            }

            $qty_kensa = 0;
            foreach ($kensas as $kensa) {
                if ($request->material_number == $kensa->material_number) {
                    $qty_kensa = $kensa->qty;
                }
            }

            array_push($log_request, [
                "material_number" => $request->material_number,
                "model" => $request->model,
                "key" => $request->key,
                "kanban" => $request->kanban,
                "quantity" => $request->quantity,
                "inventory_kanban" => $request->inventory_kanban,
                "inventory_quantity" => $request->inventory_quantity,
                "solder" => $qty_solder * $request->lot_transfer,
                "cuci" => $qty_cuci * $request->lot_transfer,
                "kensa" => $qty_kensa * $request->lot_transfer,
            ]);
        }

        $response = array(
            'status' => true,
            'datas' => $log_request,
        );
        return Response::json($response);
    }

    //PENCATATAN LOG BUFFING
    public function insertLogBuffing(Request $request)
    {
        $log = RfidLogEfficiency::where('operator_id', "=", $request->get('employee_id'))
            ->where('status', "=", "kosong")
            ->orderBy('updated_at', 'DESC')
            ->limit(1)
            ->get();

        if ($log->isEmpty()) {
            RfidLogEfficiency::insert(
                ['operator_id' => $request->get('employee_id'), 'status' => "kosong", 'time_log' => new DateTime(), 'created_at' => new DateTime(), 'updated_at' => new DateTime()]
            );

            $response = array(
                'status' => true,
                'datas' => 'Success Insert',
            );
            return Response::json($response);
        }

        $response = array(
            'status' => false,
            'datas' => 'Not Inserted',
        );
        return Response::json($response);
    }

    public function updateLogBuffing(Request $request)
    {
        $log = RfidLogEfficiency::where('operator_id', "=", $request->get('employee_id'))
            ->where('status', "=", "kosong")
            ->orderBy('updated_at', 'DESC')
            ->limit(1)
            ->get();

        if ($log->isNotEmpty()) {
            RfidLogEfficiency::where('operator_id', "=", $request->get('employee_id'))
                ->orderBy('updated_at', 'DESC')
                ->limit(1)
                ->update(
                    ['operator_id' => $request->get('employee_id'), 'status' => "tidak kosong", 'time_log' => new DateTime()]
                );

            $response = array(
                'status' => true,
                'datas' => 'Success Update',
            );
            return Response::json($response);
        }
    }

    public function fetchMisuzumashi(Request $request)
    {
        if ($request->get('date') != "") {
            $date = $request->get('date');
            $date2 = date("Y-m", strtotime($request->get('date')));
        } else {
            $date = date("Y-m-d");
            $date2 = date("Y-m");
        }

        $mz_data = RfidLogEfficiency::whereRaw("DATE_FORMAT(created_at,'%Y-%m-%d') = '" . $date . "'")
            ->select('operator_id', 'grup', db::raw("SUM(IF(remark = 1, TIME_TO_SEC(TIMEDIFF(created_at,time_filled)), 0)) / 60 as nganggur_min"), db::raw("SUM(IF(remark = 2, TIME_TO_SEC(TIMEDIFF(created_at,time_filled)), 0))  / 60 as selesai_min"), db::raw("SUM(IF(remark = 3, TIME_TO_SEC(TIMEDIFF(created_at,time_filled)), 0))  / 60 as akan_min"))
            ->groupBy('operator_id', 'grup')
            ->get();

        $op = [];

        foreach ($mz_data as $key) {
            $operator = Employee::select("name", "employee_id")
                ->where("employee_id", "=", $key->operator_id)
                ->first();

            array_push($op, $operator);
        }

        //OVERALL CHART

        $mz_overall = RfidLogEfficiency::whereRaw("DATE_FORMAT(created_at,'%Y-%m') = '" . $date2 . "'")
            ->whereRaw("DATE_FORMAT(created_at,'%Y-%m-%d') <= '" . $date . "'")
            ->select(db::raw('DATE_FORMAT(created_at,"%Y-%m-%d") as tanggal'), 'grup', db::raw("SUM(IF(remark = 1, TIME_TO_SEC(TIMEDIFF(created_at,time_filled)), 0)) / 60 as nganggur_min"), db::raw("SUM(IF(remark = 2, TIME_TO_SEC(TIMEDIFF(created_at,time_filled)), 0))  / 60 as selesai_min"), db::raw("SUM(IF(remark = 3, TIME_TO_SEC(TIMEDIFF(created_at,time_filled)), 0))  / 60 as akan_min"))
            ->groupBy(db::raw('DATE_FORMAT(created_at,"%Y-%m-%d")'), 'grup')
            ->get();

        $response = array(
            'status' => true,
            'datas' => $mz_data,
            'op' => $op,
            'overall' => $mz_overall,
        );
        return Response::json($response);
    }

}