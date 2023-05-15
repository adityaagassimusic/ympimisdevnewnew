<?php

namespace App\Http\Controllers;

use App\BreakTime;
use App\CodeGenerator;
use App\EmployeeSync;
use App\Mutationlog;
use App\Overtime;
use App\OvertimeDetail;
use App\OvertimeEmployee;
use App\OvertimeExtrafoodAttendance;
use App\User;
use App\WeeklyCalendar;
use Carbon\Carbon;
use DataTables;
use Excel;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

class OvertimeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->transport = [
            '-',
            'Bangil',
            'Pasuruan',
        ];
        $this->day_status = [
            'Workday',
            'Holiday',
        ];
        $this->shift = [
            1,
            2,
            3,
        ];

        $purposes = db::table('overtime_purposes')->select('purpose')->get();
        $this->purpose = $purposes;
    }

    public function indexOvertimeConfirmation()
    {
        return view('overtimes.overtime_confirmation')->with('page', 'Overtime Confirmation');
    }

    public function indexOvertimeControl()
    {
        return view('overtimes.reports.control_report')->with('page', 'Overtime Control');
    }

    public function indexOvertimeCheck()
    {
        $title = 'Overtime Check';
        $title_jp = '残業の点検';

        return view('overtimes.reports.overtime_check', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Overtime Check');
    }

    public function fetchOvertimeCheck(Request $request)
    {

        if (strlen($request->get('date_from')) > 0 && strlen($request->get('date_to')) > 0) {
            $date_from = date('Y-m-d', strtotime($request->get('date_from')));
            $date_to = date('Y-m-d', strtotime($request->get('date_to')));
            $first = date('Y-m-01', strtotime($request->get('date_from')));
            $last = date('Y-m-t', strtotime($request->get('date_from')));
        } else {
            $date_from = date('Y-m-01');
            $date_to = date('Y-m-d');
            $first = date('Y-m-01');
            $last = date('Y-m-t');
        }

        // $response = array(
        //     'date_from' => $date_from,
        //     'date_to' => $date_to,
        //     'first' => $first,
        //     'last' => $last,
        // );
        // return Response::json($response);

        $ot_3 = db::connection('sunfish')->select("SELECT
			O.emp_no,
			E.Full_name,
			O.cost_center,
			O.section,
			O.ovtplanfrom,
			O.ovtplanto,
			IIF ( O.total_ot IS NULL, O.TOTAL_OVT_PLAN, O.total_ot ) / 60 AS ot
			FROM
			VIEW_YMPI_Emp_OvertimePlan O
			LEFT JOIN VIEW_YMPI_Emp_OrgUnit E ON E.Emp_no = O.Emp_no
			WHERE
			O.ovtplanfrom >= '" . $date_from . "'
			AND O.ovtplanfrom <= '" . $date_to . "'
			AND O.daytype = 'WD'
			AND IIF ( O.total_ot IS NULL, O.TOTAL_OVT_PLAN, O.total_ot ) > 180
			ORDER BY
			O.cost_center;");

        $ot_14 = db::connection('sunfish')->select("SELECT
			O.emp_no,
			E.Full_name,
			O.cost_center,
			O.section,
			DATEPART( week, O.ovtplanfrom ) AS w,
			SUM ( IIF ( O.total_ot IS NULL, O.TOTAL_OVT_PLAN, O.total_ot ) ) / 60 AS ot
			FROM
			VIEW_YMPI_Emp_OvertimePlan O
			LEFT JOIN VIEW_YMPI_Emp_OrgUnit E ON E.Emp_no = O.Emp_no
			WHERE
			O.ovtplanfrom >= '" . $first . "'
			AND O.ovtplanfrom <= '" . $last . "'
			AND O.daytype = 'WD'
			GROUP BY
			O.emp_no,
			E.Full_name,
			O.cost_center,
			O.section,
			DATEPART( week, O.ovtplanfrom )
			HAVING
			SUM ( IIF ( O.total_ot IS NULL, O.TOTAL_OVT_PLAN, O.total_ot ) ) > 840;");

        $ot_56 = db::connection('sunfish')->select("SELECT
			O.emp_no,
			E.Full_name,
			O.cost_center,
			O.section,
			SUM ( IIF ( O.total_ot IS NULL, O.TOTAL_OVT_PLAN, O.total_ot ) ) / 60 AS ot
			FROM
			VIEW_YMPI_Emp_OvertimePlan O
			LEFT JOIN VIEW_YMPI_Emp_OrgUnit E ON E.Emp_no = O.Emp_no
			WHERE
			O.ovtplanfrom >= '" . $first . "'
			AND O.ovtplanfrom <= '" . $last . "'
			AND O.daytype = 'WD'
			GROUP BY
			O.emp_no,
			E.Full_name,
			O.cost_center,
			O.section
			HAVING
			SUM ( IIF ( O.total_ot IS NULL, O.TOTAL_OVT_PLAN, O.total_ot ) ) > 3360;");

        $nsonsiabs = db::connection('sunfish')->select("SELECT
			A.emp_no,
			A.official_name,
			A.shiftdaily_code,
			A.shiftstarttime,
			A.shiftendtime,
			A.starttime,
			A.endtime,
			A.Attend_Code
			FROM
			VIEW_YMPI_Emp_Attendance A
			WHERE
			A.shiftstarttime >= '" . $date_from . "'
			AND A.shiftstarttime <= '" . $date_to . "'
			AND ( A.Attend_Code LIKE '%NSO%' OR A.Attend_Code LIKE '%NSI%' OR A.Attend_Code LIKE '%ABS%' )
			ORDER BY
			A.emp_no ASC, A.shiftstarttime ASC;");

        $response = array(
            'status' => true,
            'ot_3' => $ot_3,
            'ot_14' => $ot_14,
            'ot_56' => $ot_56,
            'nsonsiabs' => $nsonsiabs,
        );
        return Response::json($response);

    }

    public function indexReportSection()
    {

        $cost_centers = db::table('cost_centers2')->orderBy('cost_center', 'asc')->get();

        return view('overtimes.reports.overtime_section', array(
            'title' => 'Overtime by Section',
            'title_jp' => '残業 課別',
            'cost_centers' => $cost_centers,
        ))->with('page', 'Overtime by Section');
    }

    public function indexReportControlFq()
    {
        return view('overtimes.reports.overtime_monthly', array(
            'title' => 'Monthly Overtime Control',
            'title_jp' => '月次残業管理'))->with('page', 'Overtime Monthly Control Forecast');
    }

    public function indexReportControlBdg()
    {
        return view('overtimes.reports.overtime_monthly_budget', array(
            'title' => 'Monthly Overtime Control',
            'title_jp' => '月次残業管理'))->with('page', 'Overtime Monthly Control Budget');
    }

    public function indexReportOutsouce()
    {
        return view('overtimes.reports.overtime_outsource', array(
            'title' => 'Overtime Outsource Control',
            'title_jp' => '派遣社員の残業管理'))->with('page', 'Overtime Outsource Employee');
    }

    public function indexMonthlyResume()
    {
        $fiscal_years = db::select("select DISTINCT fiscal_year from weekly_calendars order by fiscal_year asc");
        $costcenters = db::select("select DISTINCT cost_center, cost_center_name from ympimis.cost_centers order by cost_center_name asc");

        return view('overtimes.reports.overtime_resume', array(
            'title' => 'Overtime Resume',
            'title_jp' => '残業のまとめ',
            'fiscal_years' => $fiscal_years,
            'costcenters' => $costcenters,
        ))->with('page', 'Overtime Monthly Resume');
    }

    public function indexOvertimeOutsource()
    {
        return view('overtimes.reports.overtime_data_outsource', array(
            'title' => 'Overtime Outsource Data',
            'title_jp' => '派遣社員の残業データ'))->with('page', 'Overtime Outsource');
    }

    public function indexPrint($id)
    {
        $ot = Overtime::leftJoin("overtime_details", "overtimes.overtime_id", "=", "overtime_details.overtime_id")
            ->leftJoin("employees", "employees.employee_id", "=", "overtime_details.employee_id")
            ->where("overtimes.overtime_id", "=", $id)
            ->whereNull("overtime_details.deleted_at")
            ->select("overtimes.overtime_id", db::raw("date_format(overtimes.overtime_date,'%d-%m-%Y') as overtime_date"), "overtimes.division", "overtimes.department", "overtimes.section", "overtimes.subsection", "overtimes.group", "overtime_details.employee_id", "employees.name", "overtime_details.food", "overtime_details.ext_food", "overtime_details.transport", "overtime_details.start_time", "overtime_details.end_time", "overtime_details.final_hour", "overtime_details.purpose", "overtime_details.remark", "overtime_details.cost_center")
            ->get();

        return view('overtimes.overtime_forms.index_print', array(
            'datas' => $ot,
        ));
    }

    public function indexOvertimeForm()
    {
        $code_generator = CodeGenerator::where('note', '=', 'OT')->first();
        if ($code_generator->prefix != date('ym')) {
            db::table('code_generators')->where('note', '=', 'OT')->update([
                'prefix' => strtoupper(date('ym')),
                'index' => 0,
            ]);
        }

        return view('overtimes.overtime_forms.index')->with('page', 'Overtime Form');
    }

    public function indexOvertimeData()
    {
        $title = 'Overtime Data';
        $title_jp = '残業データ';

        // $datas = db::table('employee_syncs')->select("select * from employee_syncs");

        $q = "select employee_syncs.employee_id, employee_syncs.name, employee_syncs.department, employee_syncs.`section`, employee_syncs.`group`, employee_syncs.cost_center, cost_centers2.cost_center_name from employee_syncs left join cost_centers2 on cost_centers2.cost_center = employee_syncs.cost_center";

        $datas = db::select($q);

        return view('overtimes.reports.overtime_data', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'datas' => $datas,
        ));
    }

    public function indexOvertimeByEmployee()
    {
        $title = 'Employee Monthly Overtime';
        $title_jp = '社員番号別残業管理';
        $department = db::select("select child_code from organization_structures where remark = '" . 'department' . "'");
        $section = db::select("select child_code from organization_structures where remark = '" . 'section' . "'");
        $nik = db::select("SELECT employee_id from employees where end_date is null");

        return view('overtimes.reports.overtime_by_employee', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'departments' => $department,
            'sections' => $section,
            'niks' => $nik,
        ));
    }

    public function indexGAReport()
    {
        $title = 'GA - Report';
        $title_jp = '';
        $user = EmployeeSync::where('employee_id', Auth::user()->username)->first();

        return view('overtimes.reports.ga_report', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'user' => $user,
        ))->with('page', 'GA Report')->with('head', 'Overtime Report');
    }

    public function fetchOvertimeControl(Request $request)
    {
        $dateFrom = date('Y-m-d', strtotime($request->get('dateFrom')));
        $dateTo = date('Y-m-d', strtotime($request->get('dateTo')));

        $overtimes = db::connection('sunfish')->select("SELECT
			A.requestno,
			A.emp_no,
			B.full_name,
			format ( A.ovtplanfrom, 'yyyy-MM-dd' ) AS date,
			format ( A.ovtplanfrom, 'HH:mm:ss' ) AS ovt_from,
			format ( A.ovtplanto, 'HH:mm:ss' ) AS ovt_to,
			CAST(ROUND(A.TOTAL_OVT_PLAN / 60.0, 2) AS FLOAT) AS ot_plan,
			A.daytype,
			format ( A.ActualStart, 'HH:mm:ss' ) AS log_from,
			format ( A.ActualEnd, 'HH:mm:ss' ) AS log_to,
			CAST(ROUND( A.total_ot / 60.0, 2) AS FLOAT) AS ot_actual
			FROM
			VIEW_YMPI_Emp_OvertimePlan A
			LEFT JOIN VIEW_YMPI_Emp_OrgUnit B ON B.Emp_no = A.Emp_no
			WHERE
			A.ovtplanfrom >= '" . $dateFrom . " 00:00:00' and A.ovtplanfrom <= '" . $dateTo . " 23:59:59'
			ORDER BY
			A.ovtplanfrom ASC,
			A.Emp_no ASC");

        return DataTables::of($overtimes)->make(true);

    }

    public function fetchReportOvertimeSection(Request $request)
    {
        if (strlen($request->get('month_from')) == 0 || strlen($request->get('month_to')) == 0 || strlen($request->get('cost_center')) == 0) {
            $response = array(
                'status' => false,
                'message' => 'Isikan semua parameter pencarian',
            );
            return Response::json($response);
        }

        $first_sunfish = date('Y-m-01', strtotime("01-" . $request->get('month_from')));
        $last_sunfish = date('Y-m-t', strtotime("01-" . $request->get('month_to')));
        $first = date('Y-m-01', strtotime("01-" . $request->get('month_from')));
        $last = date('Y-m-t', strtotime("01-" . $request->get('month_to')));

        $sunfishs = [];
        $mirais = [];

        // WHEN VIEW_YMPI_Emp_OvertimePlan.total_ot > 0 THEN
        //         floor( ( VIEW_YMPI_Emp_OvertimePlan.total_ot / 60.0 ) * 2 + 0.5 ) / 2 ELSE floor( ( VIEW_YMPI_Emp_OvertimePlan.TOTAL_OVT_PLAN / 60.0 ) * 2 + 0.5 ) / 2
        //         END

        if ($last_sunfish >= '2020-01-01') {
            if ($first_sunfish <= '2020-01-01') {
                $first_sunfish = '2020-01-01';
            }
            $sunfishs = db::connection('sunfish')->select("SELECT
				X.period,
				X.Emp_no,
				X.Full_name,
				X.cost_center_code,
				COALESCE ( jam, 0 ) AS jam
				FROM
				(
					SELECT
					P.period,
					O.Emp_no,
					O.Full_name,
					O.cost_center_code
					FROM
					VIEW_YMPI_Emp_OrgUnit O
					CROSS JOIN ( SELECT DISTINCT format ( ovtplanfrom, 'yyyy-MM' ) AS period FROM VIEW_YMPI_Emp_OvertimePlan WHERE ovtplanfrom >= '" . $first_sunfish . " 00:00:00' AND ovtplanto <= '" . $last_sunfish . " 23:59:59' ) P
						WHERE
						O.cost_center_code = '" . $request->get('cost_center') . "'
						AND O.end_date is null
						) AS X
						LEFT JOIN (
						SELECT
						VIEW_YMPI_Emp_OvertimePlan.Emp_no,
						FORMAT ( VIEW_YMPI_Emp_OvertimePlan.ovtplanfrom, 'yyyy-MM' ) AS period,
						SUM (
						CASE

						WHEN VIEW_YMPI_Emp_OvertimePlan.total_ot > 0 THEN
						floor( ( VIEW_YMPI_Emp_OvertimePlan.total_ot / 60.0 ) * 2 + 0.5 ) / 2 ELSE floor( ( VIEW_YMPI_Emp_OvertimePlan.TOTAL_OVT_PLAN / 60.0 ) * 2 + 0.5 ) / 2
						END
						) AS jam
						FROM
						VIEW_YMPI_Emp_OvertimePlan
						WHERE
						VIEW_YMPI_Emp_OvertimePlan.ovtplanfrom >= '" . $first_sunfish . " 00:00:00'
						AND VIEW_YMPI_Emp_OvertimePlan.ovtplanfrom <= '" . $last_sunfish . " 23:59:59'
						GROUP BY
						VIEW_YMPI_Emp_OvertimePlan.emp_no,
						FORMAT ( VIEW_YMPI_Emp_OvertimePlan.ovtplanfrom, 'yyyy-MM' )
						) AS Y ON Y.period = X.period
						AND Y.Emp_no = X.Emp_no
						ORDER BY
						X.period ASC,
						X.Emp_no ASC");
        }
        if ($first <= '2020-01-01') {
            if ($last >= '2020-01-01') {
                $last = '2019-12-31';
            }
            $mirais = db::connection('mysql3')->select("SELECT
						X.period,
						X.employee_id,
						X.cost_center,
						X.name,
						COALESCE ( Y.jam, 0 ) AS jam
						FROM
						(
							SELECT
							P.period,
							O.employee_id,
							O.name,
							O.cost_center
							FROM
							(
								SELECT
								A.employee_id,
								A.name,
								cc.cost_center
								FROM
								ympimis.employees A
								LEFT JOIN ( SELECT employee_id, cost_center FROM ympimis.mutation_logs WHERE valid_to IS NULL ) cc ON cc.employee_id = A.employee_id
								WHERE
								A.end_date IS NULL
								AND cc.cost_center = '" . $request->get('cost_center') . "'
								) AS O
								CROSS JOIN ( SELECT DISTINCT date_format( week_date, '%Y-%m' ) AS period FROM ympimis.weekly_calendars WHERE week_date >= '" . $first . "' AND week_date <= '" . $last . "' ) AS P
								ORDER BY
								P.period ASC,
								O.employee_id ASC
								) AS X
								LEFT JOIN (
								SELECT
								date_format( over_time.tanggal, '%Y-%m' ) AS period,
								over_time_member.nik,
								sum( IF ( over_time_member.STATUS = 0, over_time_member.jam, over_time_member.final ) ) AS jam
								FROM
								over_time_member
								LEFT JOIN over_time ON over_time.id = over_time_member.id_ot
								WHERE
								over_time.deleted_at IS NULL
								AND over_time.tanggal >= '" . $first . "'
								AND over_time.tanggal <= '" . $last . "'
								GROUP BY
								date_format( over_time.tanggal, '%Y-%m' ),
								over_time_member.nik
								) AS Y ON Y.period = X.period
								AND Y.nik = X.employee_id
								ORDER BY X.period asc, X.employee_id asc");
        }

        $overtimes = array();

        if ($mirais != null) {
            foreach ($mirais as $key) {
                array_push($overtimes,
                    [
                        "period" => $key->period,
                        "employee_id" => $key->employee_id,
                        "name" => $key->name,
                        "ot" => $key->jam,
                    ]);
            }
        }
        if ($sunfishs != null) {
            foreach ($sunfishs as $key) {
                array_push($overtimes,
                    [
                        "period" => $key->period,
                        "employee_id" => $key->Emp_no,
                        "name" => $key->Full_name,
                        "ot" => $key->jam,
                    ]);
            }
        }

        $response = array(
            'status' => true,
            'overtimes' => $overtimes,
        );
        return Response::json($response);
    }

    public function selectDivisionHierarchy(Request $request)
    {
        $hierarchies = db::table('organization_structures')
            ->where([['remark', '=', $request->get('remark')], ['parent_name', '=', $request->get('parent')]])
            ->get();
        $response = array(
            'status' => true,
            'hierarchies' => $hierarchies,
        );
        return Response::json($response);
    }

    public function fetchMonthlyResume(Request $request)
    {
        $start_date = "";
        $end_date = "";

        if ($request->get('fy') != null) {
            $calendar = db::select("select min(week_date) as start_date, max(week_date) as end_date from weekly_calendars where fiscal_year = '" . $request->get('fy') . "'");
        } else {
            $calendar = db::select("select min(week_date) as start_date, max(week_date) as end_date from weekly_calendars where fiscal_year = (select distinct fiscal_year from weekly_calendars where week_date = DATE_FORMAT(now(),'%Y-%m-%d'))");
        }

        $start_date = $calendar[0]->start_date;
        $end_date = $calendar[0]->end_date;

        $response = array(
            'status' => true,
            'ot_actual' => $start_date,
            'ot_budget' => $end_date,
        );
        return Response::json($response);

        // if($request->get('cc') != null) {
        //     $ccs = $request->get('cc');
        //     $cc = "";

        //     for($x = 0; $x < count($ccs); $x++) {
        //         $cc = $cc."'".$ccs[$x]."'";
        //         if($x != count($ccs)-1){
        //             $cc = $cc.",";
        //         }
        //     }
        //     $cc_ot = "and ml.cost_center in (".$cc.") ";
        //     $cc_all = "where cost_center in (".$cc.") ";
        // }

        // $query1 = "select wc.period, ot.total from
        // (SELECT DISTINCT DATE_FORMAT(week_date,'%m-%Y') as period from ympimis.weekly_calendars where fiscal_year = '".$fy."' order by week_date asc) wc
        // left join
        // (select DISTINCT DATE_FORMAT(ovr.tanggal,'%m-%Y') as period, sum(ovr.ot) as total from
        // (select tanggal, nik, SUM(IF(status = 0, jam, final)) ot from over_time
        // left join over_time_member on over_time.id = over_time_member.id_ot
        // where deleted_at is null and jam_aktual = 0 and nik not like 'osd%' group by tanggal, nik) ovr
        // left join
        // (select employee_id, cost_center from ympimis.mutation_logs where valid_to is null) ml
        // on ovr.nik = ml.employee_id
        // left join
        // (select distinct cost_center, cost_center_name from ympimis.cost_centers) cc on cc.cost_center = ml.cost_center
        // where ml.cost_center is not null ".$cc_ot."
        // group by period
        // order by period asc) ot
        // on wc.period = ot.period";
        // $ot_actual = db::connection('mysql3')->select($query1);

        $query2 = "select period, sum(budget) as total_budget from budgets where period >= '" . $start_date . "' and period <= '" . $end_date . "' group by period order by period asc";
        $ot_budget = db::select($query2);

        $query3 = "select date_format(date, '%Y-%m-01') as period, sum(hour) as total_forecast from forecasts where date >= '" . $end_date . "' and date <= '" . $end_date . "' group by date_format(date, '%Y-%m-01') order by period asc";
        $ot_forecast = db::select($query3);

        // $query4 = "select wc.period, mp.emp from
        // (SELECT DISTINCT DATE_FORMAT(week_date,'%Y-%m') as period from ympimis.weekly_calendars where fiscal_year = '".$fy."' order by week_date asc) wc
        // left join
        // (
        // select count(c.employee_id) as emp, mon from
        // (select * from
        // (
        // select employee_id, date_format(hire_date, '%Y-%m') as hire_month, date_format(end_date, '%Y-%m') as end_month, mon from employees
        // cross join (
        // select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where fiscal_year = '".$fy."' group by date_format(week_date, '%Y-%m')) s
        // ) m
        // where hire_month <= mon and (mon < end_month OR end_month is null)
        // ) as b
        // left join
        // (
        // select id, employment_logs.employee_id, employment_logs.status, date_format(employment_logs.valid_from, '%Y-%m') as mon_from, coalesce(date_format(employment_logs.valid_to, '%Y-%m'), date_format((select max(week_date) from weekly_calendars), '%Y-%m')) as mon_to from employment_logs
        // WHERE id IN (
        // SELECT MAX(id)
        // FROM employment_logs
        // GROUP BY employment_logs.employee_id, date_format(employment_logs.valid_from, '%Y-%m')
        // )
        // ) as c on b.employee_id = c.employee_id
        // left join
        // (select employee_id, cost_center from ympimis.mutation_logs where valid_to is null) ml
        // on c.employee_id = ml.employee_id
        // left join
        // (select distinct cost_center, cost_center_name from ympimis.cost_centers) cc on cc.cost_center = ml.cost_center
        // where mon_from <= mon and mon_to >= mon and ml.cost_center is not null ".$cc_ot."
        // group by mon
        // ) mp
        // on wc.period = mp.mon";
        // $mp_actual = db::select($query4);

        $query5 = "select period, sum(budget_mp) as total_budget_mp from budgets where period >= '" . $start_date . "' and period <= '" . $end_date . "' group by period order by period asc";
        $mp_budget = db::select($query5);

        $query6 = "select period, sum(forecast_mp) as total_forecast_mp from manpower_forecasts where period >= '" . $start_date . "' and period <= '" . $end_date . "' group by period order by period asc";
        $mp_forecast = db::select($query6);

        $response = array(
            'status' => true,
            'ot_actual' => $ot_actual,
            'ot_budget' => $ot_budget,
            'ot_forecast' => $ot_forecast,
            'mp_actual' => $mp_actual,
            'mp_budget' => $mp_budget,
            'mp_forecast' => $mp_forecast,
            'fy' => $fy,
        );
        return Response::json($response);
    }

    public function fetchEmployee(Request $request)
    {
        $employee = db::table('employees')->where('employee_id', '=', $request->get('employee_id'))->first();
        $transports = $this->transport;
        $purposes = $this->purpose;

        $response = array(
            'status' => true,
            'employee' => $employee,
            'transports' => $transports,
            'purposes' => $purposes,
        );
        return Response::json($response);
    }

    public function fetchBreak(Request $request)
    {
        $hari = date('w', strtotime($request->get('tgl')));
        $break = BreakTime::where('day', '=', $hari)
            ->where('break_times.shift', '=', $request->get('shift'))
            ->where('break_times.start', '>=', $request->get('from'))
            ->where('break_times.end', '<=', $request->get('to'))
            ->select(DB::raw("IFNULL(sum(TIME_TO_SEC(duration)),'0') as istirahat"))
            ->first();

        $response = array(
            'status' => true,
            'break' => $break,
        );
        return Response::json($response);
    }

    public function fetchOvertimeData(Request $request)
    {

        if (date('Y-m-d', strtotime($request->get('datefrom'))) <= '2019-12-31' && date('Y-m-d', strtotime($request->get('dateto'))) <= '2019-12-31') {
            $tanggal = "";
            $addcostcenter = "";
            $adddepartment = "";
            $addsection = "";
            $addgrup = "";
            $addnik = "";

            if (strlen($request->get('datefrom')) > 0) {
                $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
                $tanggal = "and tanggal >= '" . $datefrom . "' ";
                if (strlen($request->get('dateto')) > 0) {
                    $dateto = date('Y-m-d', strtotime($request->get('dateto')));
                    $tanggal = $tanggal . "and tanggal <= '" . $dateto . "' ";
                }
            }

            if ($request->get('cost_center_code') != null) {
                $costcenter = implode(",", $request->get('cost_center_code'));
                $addcostcenter = "and bagian.cost_center in (" . $costcenter . ") ";
            }

            if ($request->get('department') != null) {
                $departments = $request->get('department');
                $deptlength = count($departments);
                $department = "";

                for ($x = 0; $x < $deptlength; $x++) {
                    $department = $department . "'" . $departments[$x] . "'";
                    if ($x != $deptlength - 1) {
                        $department = $department . ",";
                    }
                }
                $adddepartment = "and bagian.department in (" . $department . ") ";
            }

            if ($request->get('section') != null) {
                $sections = $request->get('section');
                $sectlength = count($sections);
                $section = "";

                for ($x = 0; $x < $sectlength; $x++) {
                    $section = $section . "'" . $sections[$x] . "'";
                    if ($x != $sectlength - 1) {
                        $section = $section . ",";
                    }
                }
                $addsection = "and bagian.section in (" . $section . ") ";
            }

            if ($request->get('group') != null) {
                $groups = $request->get('group');
                $grplen = count($groups);
                $group = "";

                for ($x = 0; $x < $grplen; $x++) {
                    $group = $group . "'" . $groups[$x] . "'";
                    if ($x != $grplen - 1) {
                        $group = $group . ",";
                    }
                }
                $addgrup = "and bagian.group in (" . $group . ") ";
            }

            if ($request->get('employee_id') != null) {
                $niks = $request->get('employee_id');
                $niklen = count($niks);
                $nik = "";

                for ($x = 0; $x < $niklen; $x++) {
                    $nik = $nik . "'" . $niks[$x] . "'";
                    if ($x != $niklen - 1) {
                        $nik = $nik . ",";
                    }
                }
                $addnik = "and ovr.nik in (" . $nik . ") ";
            }
            $overtimeData = db::connection('mysql3')->select("select distinct ovr.tanggal, ovr.nik, ovr.id_overtime, emp.name, bagian.cost_center, bagian.department, bagian.section, bagian.group, ot, keperluan, code from
								(select tanggal, nik, SUM(IF(status = 0, jam, final)) ot, GROUP_CONCAT(id_ot) as id_overtime, GROUP_CONCAT(keperluan) keperluan from over_time_member left join over_time on over_time.id = over_time_member.id_ot
								where deleted_at is null and jam_aktual = 0 " . $tanggal . " group by tanggal, nik) ovr
								left join ympimis.employees as emp on emp.employee_id = ovr.nik
								left join (select employee_id, cost_center, division, department, section, sub_section, `group` from ympimis.mutation_logs where valid_to is null) bagian on bagian.employee_id = ovr.nik
								left join ympimis.cost_centers on ympimis.cost_centers.section = bagian.section and ympimis.cost_centers.sub_sec = bagian.sub_section and ympimis.cost_centers.group = bagian.group
								where ot > 0 " . $addcostcenter . "" . $adddepartment . "" . $addsection . "" . $addgrup . "" . $addnik . "
								order by ot asc
								");
        } else {
            $tanggal = "";
            $addcostcenter = "";
            $adddepartment = "";
            $addsection = "";
            $addgrup = "";
            $addnik = "";

            if (strlen($request->get('datefrom')) > 0) {
                $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
                $tanggal = "and A.ovtplanfrom >= '" . $datefrom . " 00:00:00' ";
                if (strlen($request->get('dateto')) > 0) {
                    $dateto = date('Y-m-d', strtotime($request->get('dateto')));
                    $tanggal = $tanggal . "and A.ovtplanto <= '" . $dateto . " 23:59:59' ";
                }
            }

            if ($request->get('cost_center_code') != null) {
                $costcenter = implode(",", $request->get('cost_center_code'));
                $addcostcenter = 'and B.cost_center_code in (\'' . $costcenter . '\') ';
            }

            if ($request->get('department') != null) {
                $departments = $request->get('department');
                $deptlength = count($departments);
                $department = "";

                for ($x = 0; $x < $deptlength; $x++) {
                    $department = $department . "'" . $departments[$x] . "'";
                    if ($x != $deptlength - 1) {
                        $department = $department . ",";
                    }
                }
                $adddepartment = "and B.Department in (" . $department . ") ";
            }

            if ($request->get('section') != null) {
                $sections = $request->get('section');
                $sectlength = count($sections);
                $section = "";

                for ($x = 0; $x < $sectlength; $x++) {
                    $section = $section . "'" . $sections[$x] . "'";
                    if ($x != $sectlength - 1) {
                        $section = $section . ",";
                    }
                }
                $addsection = "and B.[Section] in (" . $section . ") ";
            }

            if ($request->get('group') != null) {
                $groups = $request->get('group');
                $grplen = count($groups);
                $group = "";

                for ($x = 0; $x < $grplen; $x++) {
                    $group = $group . "'" . $groups[$x] . "'";
                    if ($x != $grplen - 1) {
                        $group = $group . ",";
                    }
                }
                $addgrup = "and B.Groups in (" . $group . ") ";
            }

            if ($request->get('employee_id') != null) {
                $niks = $request->get('employee_id');
                $niklen = count($niks);
                $nik = "";

                for ($x = 0; $x < $niklen; $x++) {
                    $nik = $nik . "'" . $niks[$x] . "'";
                    if ($x != $niklen - 1) {
                        $nik = $nik . ",";
                    }
                }
                $addnik = "and A.Emp_no in (" . $nik . ") ";
            }

            $overtimeData = db::connection('sunfish')->select("
								SELECT
								format ( A.ovtplanfrom, 'yyyy-MM-dd' ) AS tanggal,
								A.Emp_no AS nik,
								A.requestno AS id_overtime,
								B.Full_name AS name,
								B.cost_center_code AS cost_center,
								B.Department AS department,
								B.[Section] AS [section],
								B.Groups AS [group],
								IIF (
									total_ot IS NOT NULL,
									CAST( A.total_ot / 60.0 AS FLOAT),
									CAST( A.TOTAL_OVT_PLAN / 60.0 AS FLOAT)
									) AS ot,
								UPPER ( A.remark ) AS keperluan
								FROM
								VIEW_YMPI_Emp_OvertimePlan A
								LEFT JOIN VIEW_YMPI_Emp_OrgUnit B ON A.Emp_no = B.Emp_no
								where A.TOTAL_OVT_PLAN > 0 " . $tanggal . "" . $addcostcenter . "" . $adddepartment . "" . $addsection . "" . $addgrup . "" . $addnik . "
								ORDER BY
								A.ovtplanfrom ASC");
        }

        $response = array(
            'status' => true,
            'overtime' => $overtimeData,
        );
        return Response::json($response);

    }

    public function saveOvertimeHead(Request $request)
    {
        $ot_id = $request->get('ot_id');

        $org = db::select("select dep.child_code as department, divs.child_code as division from
							(SELECT  parent_name, child_code FROM `organization_structures` where remark='section') sec
							join (SELECT  parent_name, child_code, status FROM `organization_structures` where remark='department') dep
							on sec.parent_name = dep.status
							join (SELECT child_code, status FROM `organization_structures` where remark='division') divs
							on divs.status = dep.parent_name
							where sec.child_code = '" . $request->get('section') . "'");

        $section = $request->get('section');
        $sub_section = $request->get('sub_section');
        $group = $request->get('group');
        $ot_date = date('Y-m-d', strtotime($request->get('ot_date')));
        $ot_day = $request->get('ot_day');
        $shift = $request->get('shift');
        $remark = $request->get('remark');

        $overtime = new Overtime([
            'overtime_id' => $ot_id,
            'overtime_date' => $ot_date,
            'day_status' => $ot_day,
            'shift' => $shift,
            'division' => $org[0]->division,
            'department' => $org[0]->department,
            'section' => $section,
            'subsection' => $sub_section,
            'group' => $group,
            'remark' => $remark,
            'created_by' => 1,
        ]);
        $overtime->save();

        $response = array(
            'status' => true,
        );
        return Response::json($response);
    }

    public function saveOvertimeDetail(Request $request)
    {
        $ot_id = $request->get('ot_id');
        $emp_ids = $request->get('emp_ids');
        $ot_starts = $request->get('ot_starts');
        $ot_ends = $request->get('ot_ends');
        $ot_hours = $request->get('ot_hours');
        $ot_transports = $request->get('ot_transports');
        $ot_foods = $request->get('ot_foods');
        $ot_efoods = $request->get('ot_efoods');
        $ot_purposes = $request->get('ot_purposes');
        $ot_remarks = $request->get('ot_remarks');
        $ot_statuses = $request->get('ot_statuses');

        for ($i = 0; $i < sizeof($emp_ids); $i++) {
            $emp = db::table('mutation_logs')
                ->where('employee_id', '=', $emp_ids[$i])
                ->whereNull('valid_to')
                ->select('cost_center')
                ->get();

            $overtime_detail = new OvertimeDetail([
                'overtime_id' => $ot_id,
                'employee_id' => $emp_ids[$i],
                'cost_center' => $emp[0]->cost_center,
                'food' => $ot_foods[$i],
                'ext_food' => $ot_efoods[$i],
                'transport' => $ot_transports[$i],
                'start_time' => $ot_starts[$i],
                'end_time' => $ot_ends[$i],
                'purpose' => $ot_purposes[$i],
                'remark' => $ot_remarks[$i],
                'final_hour' => $ot_hours[$i],
                'final_overtime' => '0',
                'status' => '0',
                'ot_status' => $ot_statuses[$i],
                'created_by' => Auth::user()->username,
            ]);
            $overtime_detail->save();
        }

        $response = array(
            'status' => true,
        );
        return Response::json($response);
    }

    public function editOvertimeDetail(Request $request)
    {
        $ot_id = $request->get('ot_id');
        $emp_ids = $request->get('emp_ids');
        $ot_starts = $request->get('ot_starts');
        $ot_ends = $request->get('ot_ends');
        $ot_hours = $request->get('ot_hours');
        $ot_transports = $request->get('ot_transports');
        $ot_foods = $request->get('ot_foods');
        $ot_efoods = $request->get('ot_efoods');
        $ot_purposes = $request->get('ot_purposes');
        $ot_remarks = $request->get('ot_remarks');
        $ot_statuses = $request->get('ot_statuses');

        $emp_id2 = $emp_ids;

        $datas = OvertimeDetail::where('overtime_id', $ot_id)->get();
        foreach ($datas as $ot) {
            if (!in_array($ot->employee_id, $emp_ids)) {
                OvertimeDetail::where('overtime_id', $ot_id)->where('employee_id', $ot->employee_id)->delete();
            }
        }

        for ($i = 0; $i < sizeof($emp_ids); $i++) {
            $overtime_detail = OvertimeDetail::where('overtime_id', '=', $ot_id)
                ->where('employee_id', '=', $emp_ids[$i])
                ->update([
                    'food' => $ot_foods[$i],
                    'ext_food' => $ot_efoods[$i],
                    'transport' => $ot_transports[$i],
                    'start_time' => $ot_starts[$i],
                    'end_time' => $ot_ends[$i],
                    'purpose' => $ot_purposes[$i],
                    'remark' => $ot_remarks[$i],
                    'final_hour' => $ot_hours[$i],
                    'ot_status' => $ot_statuses[$i],
                ]);
        }

        $response = array(
            'status' => true,
        );
        return Response::json($response);
    }

    public function fetchOvertimeConfirmation()
    {

        $username = Auth::user()->username;
        $role = Auth::user()->role_code;

        $dep = db::connection('mysql3')->table('karyawan')
            ->leftJoin('posisi', 'posisi.nik', '=', 'karyawan.nik')
            ->where('karyawan.nik', '=', $username)
            ->select('id_dep')
            ->first();

        if ($role == 'HR-SPL' || $role == 'S') {
            $where = '';
        } else {
            $where = "and posisi.id_dep = '" . $dep->id_dep . "'";
        }

        $queryConfirmation = "select b.id, date_format(a.tanggal,'%d-%b-%y') as tanggal, a.nik, a.nama_karyawan as name, a.masuk, a.keluar, b.plan_ot, a.jam as act_log, a.jam-b.plan_ot as diff, a.section, b.hari, b.dari, b.sampai from
						( SELECT
						over.tanggal,
						over.nik,
						karyawan.namaKaryawan as nama_karyawan,
						section.nama as section,
						over.jam,
						presensi.masuk,
						presensi.keluar
						FROM
						over
						LEFT JOIN posisi ON posisi.nik = over.nik
						LEFT JOIN karyawan on karyawan.nik = over.nik
						left join section on section.id = posisi.id_sec
						left join presensi on presensi.nik = over.nik and presensi.tanggal = over.tanggal
						WHERE
						status_final = 0 " . $where . "
						) AS a
						LEFT JOIN (
						SELECT
						over_time.id,
						over_time.tanggal,
						over_time_member.nik,
						sum( over_time_member.jam ) AS plan_ot,
						dari,
						sampai,
						over_time.hari
						FROM
						over_time
						LEFT JOIN over_time_member ON over_time.id = over_time_member.id_ot
						WHERE
						over_time_member.nik IS NOT NULL
						AND deleted_at IS NULL
						AND over_time_member.STATUS = 0
						AND over_time_member.jam_aktual = 0
						GROUP BY
						over_time.id,
						over_time.tanggal,
						over_time.hari,
						over_time_member.nik,
						over_time_member.dari,
						over_time_member.sampai
						) AS b ON a.nik = b.nik
						AND a.tanggal = b.tanggal
						where b.id IS NOT NULL order by a.tanggal asc, a.nik asc";

        $overtimes = db::connection('mysql3')->select($queryConfirmation);

        return DataTables::of($overtimes)
            ->addColumn('ot', function ($overtimes) {
                return '<input type="radio" id="ot_act_radio" name="confirm+' . $overtimes->nik . '+' . $overtimes->tanggal . '+' . $overtimes->id . '+' . $overtimes->hari . '" value="' . $overtimes->plan_ot . '">';
            })
            ->addColumn('log', function ($overtimes) {
                return '<input type="radio" id="ot_log_radio" name="confirm+' . $overtimes->nik . '+' . $overtimes->tanggal . '+' . $overtimes->id . '+' . $overtimes->hari . '" value="' . $overtimes->act_log . '">';
            })
            ->addColumn('edit', function ($overtimes) {
                $tanggal = date('l, d F Y', strtotime($overtimes->tanggal));
                $tgl2 = date('Y-m-d', strtotime($overtimes->tanggal));
                $nama = str_replace("'", "", $overtimes->name);
                return '<input type="button" class="btn btn-warning btn-sm" id="edit+' . $overtimes->nik . '+' . $overtimes->id . '+' . $overtimes->plan_ot . '+' . $overtimes->act_log . '" onclick="editModal(this.id, \'' . $overtimes->masuk . '\', \'' . $overtimes->keluar . '\', \'' . $nama . '\', \'' . $overtimes->diff . '\', \'' . $tanggal . '\', \'' . $tgl2 . '\', \'' . $overtimes->hari . '\',  \'' . $overtimes->id . '\')" value="Edit">';
            })
            ->rawColumns(['ot_log' => 'ot', 'ot_plan' => 'log', 'edit' => 'edit'])
            ->make(true);
    }

    public function confirmOvertimeConfirmation(Request $request)
    {
        $datas = $request->get('confirm');

        if ($datas == null) {
            $response = array(
                'status' => false,
                'message' => "Please choose overtime to confirm",
            );
            return Response::json($response);
        }

        foreach ($datas as $data) {
            $tgl = date('Y-m-d', strtotime($data[1]));
            $nik = $data[0];
            $id_ot = $data[2];
            $jam = $data[3];
            $hari = $data[4];

            try {
                $over_time_member = DB::connection('mysql3')->table('over_time_member')
                    ->where('over_time_member.id_ot', '=', $id_ot)
                    ->where('over_time_member.nik', '=', $nik)
                    ->update([
                        'over_time_member.status' => 1,
                        'over_time_member.final' => $jam,
                    ]);

                $tes = DB::connection('mysql3')->select('CALL masukDataOverSPLAktual("' . $nik . '","' . $tgl . '", "' . $hari . '", "' . $jam . '")');
            } catch (\Exception$e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }

        }

        $response = array(
            'status' => true,
            'message' => "Overtime Confirmed",
        );
        return Response::json($response);
    }

    public function editOvertimeConfirmation(Request $request)
    {
        $jam = $request->get('jam');
        $nik = $request->get('nik');
        $tgl = date('Y-m-d', strtotime($request->get('tgl')));
        $status = $request->get('hari');

        try {
            $over = DB::connection('mysql3')->table('over')
                ->where('over.tanggal', '=', $tgl)
                ->where('over.nik', '=', $nik)
                ->first();

            if ($over == null) {
                $insert_over = DB::connection('mysql3')->table('over')
                    ->where('over.tanggal', '=', $tgl)
                    ->where('over.nik', '=', $nik)
                    ->insert([
                        'nik' => $nik,
                        'tgl' => $tgl,
                        'jam' => $jam,
                        'status' => $status,
                        'status_final' => 0,
                    ]);
            } else {
                $update_over = DB::connection('mysql3')->table('over')
                    ->where('over.tanggal', '=', $tgl)
                    ->where('over.nik', '=', $nik)
                    ->update([
                        'jam' => $jam,
                    ]);
            }
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'message' => "Overtime Changed",
        );
        return Response::json($response);
    }

    public function deleteOvertimeConfirmation(Request $request)
    {

        $over_time = DB::connection('mysql3')->table('over_time_member')
            ->where('over_time_member.id_ot', '=', $request->get('id_ot'))
            ->where('over_time_member.nik', '=', $request->get('nik'))
            ->update([
                'jam_aktual' => 1,
            ]);

        $response = array(
            'status' => true,
            'message' => 'Overtime deleted',
        );
        return Response::json($response);
    }

    public function overtimeReport()
    {
// ----------  Chart Overtime By Dep ----------
        $tanggal = date('Y-m');
        $tanggalMin = date("Y-m", strtotime("-3 months"));

        $fiskal = "select fiscal_year from weekly_calendars WHERE date_format(week_date,'%Y-%m') = '" . $tanggal . "' group by fiscal_year";

        $fy = db::select($fiskal);

        $ot_by_dep = "select mon, department, round(ot_hour / kar,2) as avg from
						(
						select em.mon ,em.department, IFNULL(sum(ovr.final),0) ot_hour, sum(jml) as kar from
						(
						select emp.*, bagian.department, 1 as jml from
						(select employee_id, mon from
						(
						select employee_id, date_format(hire_date, '%Y-%m') as hire_month, date_format(end_date, '%Y-%m') as end_month, mon from employees
						cross join (
						select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where fiscal_year = '" . $fy[0]->fiscal_year . "' and date_format(week_date, '%Y-%m') BETWEEN  '" . $tanggalMin . "' and  '" . $tanggal . "' group by date_format(week_date, '%Y-%m')) s
						) m
						where hire_month <= mon and (mon < end_month OR end_month is null)
						) emp
						left join (
						SELECT id, employee_id, department, date_format(valid_from, '%Y-%m') as mon_from, coalesce(date_format(valid_to, '%Y-%m'), date_format(DATE_ADD(now(), INTERVAL 1 MONTH),'%Y-%m')) as mon_to FROM mutation_logs
						WHERE id IN (SELECT MAX(id) FROM mutation_logs GROUP BY employee_id, DATE_FORMAT(valid_from,'%Y-%m'))
						) bagian on emp.employee_id = bagian.employee_id and emp.mon >= bagian.mon_from and emp.mon < mon_to
						where department is not null
						) as em
						left join (
						select nik, date_format(tanggal,'%Y-%m') as mon, sum(if(status = 0,om.jam,om.final)) as final from ftm.over_time as o left join ftm.over_time_member as om on o.id = om.id_ot
						where deleted_at is null and jam_aktual = 0 and DATE_FORMAT(tanggal,'%Y-%m') in (
						select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where fiscal_year = '" . $fy[0]->fiscal_year . "' and date_format(week_date, '%Y-%m') BETWEEN  '" . $tanggalMin . "' and '" . $tanggal . "' group by date_format(week_date, '%Y-%m')
						)
						group by date_format(tanggal,'%Y-%m'), nik
						) ovr on em.employee_id = ovr.nik and em.mon = ovr.mon
						group by department, em.mon
					) as semua";

        $report_by_dep = db::select($ot_by_dep);

        $response = array(
            'status' => true,
            'report_by_dep' => $report_by_dep,
        );

        return Response::json($response);
    }

    public function overtimeOver(Request $request)
    {
        $tgl = $request->get('tanggal');

        if ($tgl == '') {
            $tanggal = date('Y-m');
        } else {
            $tanggal = date('Y-m', strtotime('10-' . $tgl));
        }

        $report = "select kd.department, '" . $tanggal . "' month_name, COALESCE(tiga.tiga_jam,0) as tiga_jam, COALESCE(patblas.emptblas_jam,0) as emptblas_jam, COALESCE(tiga_patblas.tiga_patblas_jam,0) as tiga_patblas_jam, COALESCE(lima_nam.limanam_jam,0) as limanam_jam from
					(select child_code as department from organization_structures where remark = 'department') kd
					left join
					( select department, count(nik) tiga_jam from (
					select d.nik, karyawan.department from
					(select tanggal, nik, sum(IF(status = 1, final, jam)) as jam from ftm.over_time
					left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
					where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '" . $tanggal . "' and nik IS NOT NULL and jam_aktual = 0 and hari = 'N'
					group by nik, tanggal) d
					left join
					(
					select employee_id, department from mutation_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '" . $tanggal . "' and id IN (
					SELECT MAX(id)
					FROM mutation_logs
					where DATE_FORMAT(valid_from,'%Y-%m') <= '" . $tanggal . "'
					GROUP BY employee_id
					)
					) karyawan on karyawan.employee_id  = d.nik
					where jam > 3
					group by d.nik
					) tiga_jam
					group by department
					) as tiga on kd.department = tiga.department
					left join
					(
					select department, count(nik) as emptblas_jam from
					(select s.nik, department from
					(select nik, sum(jam) jam, week_name from
					(select tanggal, nik, sum(IF(status = 1, final, jam)) as jam, week(ftm.over_time.tanggal) as week_name from ftm.over_time
					left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
					where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '" . $tanggal . "' and nik IS NOT NULL and jam_aktual = 0 and hari = 'N'
					group by nik, tanggal) m
					group by nik, week_name) s
					left join
					(
					select employee_id, department from mutation_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '" . $tanggal . "' and id IN (
					SELECT MAX(id)
					FROM mutation_logs
					where DATE_FORMAT(valid_from,'%Y-%m') <= '" . $tanggal . "'
					GROUP BY employee_id
					)
					) employee on employee.employee_id = s.nik
					where jam > 14
					group by s.nik) l
					group by department
					) as patblas on kd.department = patblas.department
					left join
					(
					select employee.department, count(c.nik) as tiga_patblas_jam from
					( select z.nik from
					( select d.nik from
					(select tanggal, nik, sum(IF(status = 1, final, jam)) as jam from ftm.over_time
					left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
					where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '" . $tanggal . "' and nik IS NOT NULL and jam_aktual = 0 and hari = 'N'
					group by nik, tanggal) d
					where jam > 3
					group by d.nik ) z

					INNER JOIN

					(select s.nik from
					(select nik, sum(jam) jam, week_name from
					(select tanggal, nik, sum(IF(status = 1, final, jam)) as jam, week(ftm.over_time.tanggal) as week_name from ftm.over_time
					left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
					where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '" . $tanggal . "' and nik IS NOT NULL and jam_aktual = 0 and hari = 'N'
					group by nik, tanggal) m
					group by nik, week_name) s
					where jam > 14
					group by s.nik) x on z.nik = x.nik
					) c
					left join
					(
					select employee_id, department from mutation_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '" . $tanggal . "' and id IN (
					SELECT MAX(id)
					FROM mutation_logs
					where DATE_FORMAT(valid_from,'%Y-%m') <= '" . $tanggal . "'
					GROUP BY employee_id
					)
					) employee on employee.employee_id = c.nik
					group by employee.department
					) tiga_patblas on kd.department = tiga_patblas.department
					left join
					(
					select department, count(nik) as limanam_jam from
					( select d.nik, sum(jam) as jam, employee.department from
					(select tanggal, nik, sum(IF(status = 1, final, jam)) as jam from ftm.over_time
					left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
					where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '" . $tanggal . "' and nik IS NOT NULL and jam_aktual = 0 and hari = 'N'
					group by nik, tanggal) d
					left join
					(
					select employee_id, department from mutation_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '" . $tanggal . "' and id IN (
					SELECT MAX(id)
					FROM mutation_logs
					where DATE_FORMAT(valid_from,'%Y-%m') <= '" . $tanggal . "'
					GROUP BY employee_id
					)
					) employee on employee.employee_id = d.nik
					group by d.nik ) c
					where jam > 56
					group by department
				) lima_nam on lima_nam.department = kd.department";

        $report2 = db::select($report);

        $response = array(
            'status' => true,
            'report' => $report2,
        );

        return Response::json($response);
    }

    public function overtimeControl(Request $request)
    {
        if ($request->get('tgl') != "") {
            $tanggal1 = date('Y-m-d', strtotime($request->get('tgl')));
            $tanggal = date('Y-m-01', strtotime($request->get('tgl')));
        } else {
            $tanggal1 = date('Y-m-d');
            $tanggal = date('Y-m-01');
        }

        if ($tanggal >= '2020-01-01') {
            $ot = db::connection('sunfish')->select("SELECT
						A.Emp_no,
						A.cost_center,
						FORMAT ( A.ovtplanfrom, 'dd MMMM yyyy' ) dt,
						FORMAT ( A.ovtplanfrom, 'yyyy-MM-dd' ) dt_raw,
						A.total_ot ,
						A.TOTAL_OVT_PLAN,
						A.remark AS keperluan,
						emp.Full_name
						FROM
						VIEW_YMPI_Emp_OvertimePlan A
						LEFT JOIN VIEW_YMPI_Emp_OrgUnit as emp ON emp.Emp_no = A.Emp_no
						WHERE
						A.ovtplanfrom >= '" . $tanggal . " 00:00:00'
						AND A.ovtplanfrom <= '" . $tanggal1 . " 23:59:59'
						AND A.Emp_no NOT LIKE 'OS%'
						ORDER BY cost_center asc");

            // $main_q = "select semua.cost_center, cost_center_name, SUM(bdg) as bdg, SUM(fq) as fq, DATE_FORMAT('".$tanggal1."','%d %M %Y') as tanggal from
            // (select cost_center, round(budget / DAY(LAST_DAY('".$tanggal1."')) * DAY('".$tanggal1."'),1) as bdg, 0 as fq from budgets
            // where period = '".$tanggal."'
            // union all
            // select cost_center, 0 as bdg, round(SUM(`hour`),1) as fq from forecasts where date >= '".$tanggal."' and date <= '".$tanggal1."' GROUP BY cost_center) as semua
            // inner join cost_centers2 on cost_centers2.cost_center = semua.cost_center
            // group by cost_center, cost_center_name";

            $main_q = "select semua.cost_center, cost_center_name, cost_center_department, SUM(bdg) as bdg, SUM(fq) as fq, DATE_FORMAT('" . $tanggal1 . "','%d %M %Y') as tanggal from
					(select cost_center, round(budget / DAY(LAST_DAY('" . $tanggal1 . "')) * DAY('" . $tanggal1 . "'),1) as bdg, 0 as fq from budgets
					where period = '" . $tanggal . "'
					union all
					select cost_center, 0 as bdg, round(SUM(`hour`),1) as fq from forecasts where date >= '" . $tanggal . "' and date <= '" . $tanggal1 . "' GROUP BY cost_center) as semua
					inner join cost_centers2 on cost_centers2.cost_center = semua.cost_center
					group by cost_center, cost_center_name, cost_center_department";

            $main = db::select($main_q);

            $employee = EmployeeSync::whereNull('end_date')
                ->select(db::raw("count(employee_id) as jml"))
                ->first();

            $employee_fc = db::table('manpower_forecasts')
                ->where('period', '=', date('Y-m-01', strtotime($tanggal1)))
                ->select(db::raw("sum(forecast_mp) as jml_fc"))
                ->first();

            $employee_bdg = db::table('manpower_budgets')
                ->where('period', '=', date('Y-m-01', strtotime($tanggal1)))
                ->select(db::raw("sum(budget_mp) as jml_bdg"))
                ->first();

            $tmp_cc = db::select("SELECT cost_center_name, cost_center_department from cost_centers2");
        } else {
            $q = "SELECT
					over_time_member.nik AS emp_no,
					sum( IF ( over_time_member.STATUS = 0, over_time_member.jam, over_time_member.final ) ) AS total_ot,
					employees.cost_center AS cost_center,
					cost_centers.cost_center_name
					FROM
					over_time_member
					LEFT JOIN over_time ON over_time.id = over_time_member.id_ot
					LEFT JOIN ( SELECT employee_id, cost_center FROM ympimis.mutation_logs WHERE valid_to IS NULL ) employees ON employees.employee_id = over_time_member.nik
					LEFT JOIN (select distinct cost_center, cost_center_name from ympimis.cost_centers) as cost_centers ON cost_centers.cost_center = employees.cost_center
					WHERE
					over_time.deleted_at IS NULL
					AND over_time.tanggal >= '" . $tanggal . "'
					AND over_time.tanggal <= '" . $tanggal1 . "'
					GROUP BY
					over_time_member.nik,
					employees.cost_center,
					cost_centers.cost_center_name";

            $ot = db::connection('mysql3')->select($q);

            $main_q = "select semua.cost_center, cost_center_name, SUM(bdg) as bdg, SUM(fq) as fq, DATE_FORMAT('" . $tanggal1 . "','%d %M %Y') as tanggal from
					(select cost_center, round(budget / DAY(LAST_DAY('" . $tanggal1 . "')) * DAY('" . $tanggal1 . "'),1) as bdg, 0 as fq from budgets
					where period = '" . $tanggal . "'
					union all
					select cost_center, 0 as bdg, round(SUM(`hour`),1) as fq from forecasts where date >= '" . $tanggal . "' and date <= '" . $tanggal1 . "' GROUP BY cost_center) as semua
					inner join cost_centers2 on cost_centers2.cost_center = semua.cost_center
					group by cost_center, cost_center_name";

            $main = db::select($main_q);

            $employee = EmployeeSync::whereNull('end_date')
                ->select(db::raw("count(employee_id) as jml"))
                ->first();

            $employee_fc = db::table('manpower_forecasts')
                ->where('period', '=', date('Y-m-01', strtotime($tanggal1)))
                ->select(db::raw("sum(forecast_mp) as jml_fc"))
                ->first();

            $employee_bdg = db::table('manpower_budgets')
                ->where('period', '=', date('Y-m-01', strtotime($tanggal1)))
                ->select(db::raw("sum(budget_mp) as jml_bdg"))
                ->first();

            $tmp_cc = db::select("SELECT cost_center_name, cost_center_department from cost_centers2");

        }

        $response = array(
            'status' => true,
            'ot_detail' => $ot,
            'cc' => $main,
            'tmp_cc' => $tmp_cc,
            'emp_total' => $employee,
            'emp_fc' => $employee_fc,
            'emp_bdg' => $employee_bdg,
            'dt' => date('d M Y', strtotime($tanggal1)),
        );

        return Response::json($response);
    }

    public function overtimeReportDetail(Request $request)
    {
        $period_check = date('Y-m-01', strtotime($request->get('period')));
        $category = $request->get('category');
        $department = $request->get('department');

        if ($period_check >= '2020-01-01') {
            $period = $request->get('period');

            if ($category == '4Hours/Day') {
                $violations = db::connection('sunfish')->select("
							SELECT
							format ( A.ovtplanfrom, 'yyyy-MM-dd' ) AS period,
							A.Emp_no,
							B.Full_name,
							B.Section,
							ROUND( A.total_ot / 60.0, 2 ) AS ot
							FROM
							VIEW_YMPI_Emp_OvertimePlan A
							LEFT JOIN VIEW_YMPI_Emp_OrgUnit B ON B.Emp_no = A.Emp_no
							WHERE
							A.daytype = 'WD'
							AND FORMAT ( A.ovtplanfrom, 'yyyy-MM' ) = '" . $period . "'
							AND B.Department = '" . $department . "'
							AND ROUND( A.total_ot / 60.0, 2 ) > 4");
            }
            if ($category == '18Hours/Week') {
                $violations = db::connection('sunfish')->select("SELECT
							CONCAT('Week ',	DATEPART( week, A.ovtplanfrom )) AS period,
							A.Emp_no,
							B.Full_name,
							B.Section,
							SUM (
								ROUND( A.total_ot / 60.0, 2 )) AS ot
							FROM
							VIEW_YMPI_Emp_OvertimePlan A
							LEFT JOIN VIEW_YMPI_Emp_OrgUnit B ON B.Emp_no = A.Emp_no
							WHERE
							A.daytype = 'WD'
							AND FORMAT ( A.ovtplanfrom, 'yyyy-MM' ) = '" . $period . "'
							AND B.Department = '" . $department . "'
							GROUP BY
							A.Emp_no,
							B.Full_name,
							CONCAT('Week ',	DATEPART( week, A.ovtplanfrom )),
							B.Section
							HAVING
							SUM (ROUND( A.total_ot / 60.0, 2 )) > 18");
            }
            if ($category == 'Both') {

                $ot_3 = db::connection('sunfish')->select("
							SELECT
							format ( A.ovtplanfrom, 'yyyy-MM-dd' ) AS period,
							A.Emp_no,
							B.Full_name,
							B.Section,
							ROUND( A.total_ot / 60.0, 2 ) AS ot
							FROM
							VIEW_YMPI_Emp_OvertimePlan A
							LEFT JOIN VIEW_YMPI_Emp_OrgUnit B ON B.Emp_no = A.Emp_no
							WHERE
							A.daytype = 'WD'
							AND FORMAT ( A.ovtplanfrom, 'yyyy-MM' ) = '" . $period . "'
							AND B.Department = '" . $department . "'
							AND ROUND( A.total_ot / 60.0, 2 ) > 4");

                $ot_14 = db::connection('sunfish')->select("SELECT
							CONCAT('Week ',	DATEPART( week, A.ovtplanfrom )) AS period,
							A.Emp_no,
							B.Full_name,
							B.Section,
							SUM (
								ROUND( A.total_ot / 60.0, 2 )
								) AS ot
							FROM
							VIEW_YMPI_Emp_OvertimePlan A
							LEFT JOIN VIEW_YMPI_Emp_OrgUnit B ON B.Emp_no = A.Emp_no
							WHERE
							A.daytype = 'WD'
							AND FORMAT ( A.ovtplanfrom, 'yyyy-MM' ) = '" . $period . "'
							AND B.Department = '" . $department . "'
							GROUP BY
							A.Emp_no,
							B.Full_name,
							CONCAT('Week ',	DATEPART( week, A.ovtplanfrom )),
							B.Section
							HAVING
							SUM (ROUND( A.total_ot / 60.0, 2 )) > 18");

                $violations = array();

                if ($ot_3 != null && $ot_14 != null) {
                    foreach ($ot_3 as $key) {
                        array_push($violations, [
                            "period" => $key->period,
                            "Emp_no" => $key->Emp_no,
                            "Full_name" => $key->Full_name,
                            "Section" => $key->Section,
                            "ot" => $key->ot,
                        ]);
                    };

                    foreach ($ot_14 as $key) {
                        array_push($violations, [
                            "period" => $key->period,
                            "Emp_no" => $key->Emp_no,
                            "Full_name" => $key->Full_name,
                            "Section" => $key->Section,
                            "ot" => $key->ot,
                        ]);
                    };
                }
            }
            if ($category == '72Hours/Month') {
                $violations = db::connection('sunfish')->select("SELECT
							FORMAT ( A.ovtplanfrom, 'MMMM yyyy' ) AS period,
							A.Emp_no,
							B.Full_name,
							B.Section,
							SUM (
								ROUND( A.total_ot / 60.0, 2 )
								) AS ot
							FROM
							VIEW_YMPI_Emp_OvertimePlan A
							LEFT JOIN VIEW_YMPI_Emp_OrgUnit B ON B.Emp_no = A.Emp_no
							WHERE
							A.daytype = 'WD'
							AND FORMAT ( A.ovtplanfrom, 'yyyy-MM' ) = '" . $period . "'
							AND B.Department = '" . $department . "'
							GROUP BY
							FORMAT ( A.ovtplanfrom, 'MMMM yyyy' ),
							A.Emp_no,
							B.Full_name,
							B.Section
							HAVING
							SUM (ROUND( A.total_ot / 60.0, 2 )) > 72");
            }
        } else {
            $tgl = $request->get('period');
            $query = "";

            if ($category == '4Hours/Day') {
                $query = 'SELECT "' . $request->get('period') . '" as period, s.avg as ot, employees.employee_id as Emp_no, employees.name as Full_name, department, section as Section, `group` from
						(select d.nik, round(avg(jam),2) as avg from
						(select tanggal, nik, sum(IF(status = 1, final, jam)) as jam, ftm.over_time.hari from ftm.over_time
						left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
						where deleted_at IS NULL and date_format(ftm.over_time.tanggal, "%Y-%m") = "' . $tgl . '" and nik IS NOT NULL and jam_aktual = 0 and hari = "N"
						group by nik, tanggal, hari) d
						where jam > 4
						group by d.nik ) s
						left join employees on employees.employee_id = s.nik
						left join
						(
						select employee_id, `group`, department, section from mutation_logs where DATE_FORMAT(valid_from,"%Y-%m") <= "' . $tgl . '" and valid_to is null
						group by employee_id,`group`, department, section
						) employee on employee.employee_id = s.nik
						where department = "' . $department . '"';
            }
            if ($category == '18Hours/Week') {
                $query = 'SELECT "' . $request->get('period') . '" as period, s.nik as Emp_no, avg(jam) as ot, name as Full_name, section as Section, department, `group` from
						(select nik, sum(jam) jam, week_name from
						(select tanggal, nik, sum(IF(status = 1, final, jam)) as jam, ftm.over_time.hari, week(ftm.over_time.tanggal) as week_name from ftm.over_time
						left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
						where deleted_at IS NULL and date_format(ftm.over_time.tanggal, "%Y-%m") = "' . $tgl . '" and nik IS NOT NULL and jam_aktual = 0 and hari = "N"
						group by nik, tanggal, hari) m
						group by nik, week_name) s
						left join employees on employees.employee_id = s.nik
						left join
						(
						select employee_id, `group`, department, section from mutation_logs where DATE_FORMAT(valid_from,"%Y-%m") <= "' . $tgl . ' and valid_to is null"
						group by employee_id,`group`, department, section
						) employee on employee.employee_id = s.nik
						where jam > 18 and department = "' . $department . '"
						group by s.nik, name, section, department,`group`';
            }
            if ($category == 'Both') {
                $query = 'select "' . $request->get('period') . '" as period, c.nik as Emp_no, name as Full_name, department, section as Section, `group`, c.avg as ot from ( select z.nik, x.avg from
						( select d.nik, round(avg(jam),2) as avg from
						(select tanggal, nik, sum(IF(status = 1, final, jam)) as jam, ftm.over_time.hari from ftm.over_time
						left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
						where deleted_at IS NULL and date_format(ftm.over_time.tanggal, "%Y-%m") = "' . $tgl . '" and nik IS NOT NULL and jam_aktual = 0 and hari = "N"
						group by nik, tanggal, hari) d
						where jam > 4
						group by d.nik ) z

						INNER JOIN

						( select s.nik, avg(jam) as avg from
						(select nik, sum(jam) jam, week_name from
						(select tanggal, nik, sum(IF(status = 1, final, jam)) as jam, ftm.over_time.hari, week(ftm.over_time.tanggal) as week_name from ftm.over_time
						left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
						where deleted_at IS NULL and date_format(ftm.over_time.tanggal, "%Y-%m") = "' . $tgl . '" and nik IS NOT NULL and jam_aktual = 0 and hari = "N"
						group by nik, tanggal, hari) m
						group by nik, week_name) s
						where jam > 18
						group by s.nik) x on z.nik = x.nik
						) c
						left join employees on employees.employee_id = c.nik
						left join
						(
						select employee_id, `group`, department, section from mutation_logs where DATE_FORMAT(valid_from,"%Y-%m") <= "' . $tgl . ' and valid_to is null"
						group by employee_id,`group`, department, section
						) employee on employee.employee_id = c.nik
						where department = "' . $department . '"';
            }
            if ($category == '72Hours/Month') {
                $query = 'select "' . $request->get('period') . '" as period, semua.nik as Emp_no, employees.name as Full_name, department, section as Section, `group`, avg as ot from
						(select c.nik, c.jam as avg from
						(select d.nik, sum(jam) as jam from
						(select tanggal, nik, sum(IF(status = 1, final, jam)) as jam, ftm.over_time.hari from ftm.over_time
						left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
						where deleted_at IS NULL and date_format(ftm.over_time.tanggal, "%Y-%m") = "' . $tgl . '" and nik IS NOT NULL and jam_aktual = 0 and hari = "N"
						group by nik, tanggal, hari) d
						group by d.nik) c
						where jam > 72) semua
						left join employees on employees.employee_id = semua.nik
						left join
						(
						select employee_id, `group`, department, section from mutation_logs where DATE_FORMAT(valid_from,"%Y-%m") <= "' . $tgl . ' and valid_to is null"
						group by employee_id,`group`, department, section
						) employee on employee.employee_id = semua.nik
						where department = "' . $department . '"';

            }
            $violations = db::select($query);
        }

        $response = array(
            'status' => true,
            'violations' => $violations,
            'head' => $category,
        );
        return Response::json($response);
    }
// --------------------- END CHART REPORT OVERTIME -------------------

// --------------------- Start Employement ---------------------
    public function indexOvertimeDouble()
    {
        return view('overtimes.overtime_double')->with('page', 'overtimeDouble');
    }

    public function fetchDoubleSPL(Request $request)
    {
        $bulan = $request->get('bulan');

        $username = Auth::user()->username;
        $role = Auth::user()->role_code;

        $dep = db::connection('mysql3')->table('karyawan')
            ->leftJoin('posisi', 'posisi.nik', '=', 'karyawan.nik')
            ->where('karyawan.nik', '=', $username)
            ->select('id_dep')
            ->first();

        if ($role == 'HR-SPL' || $role == 'S') {
            $where = '';
        } else {
            $where = "where posisi.id_dep = '" . $dep->id_dep . "'";
        }
        $double = "select ov.id_ot, date_format(ov.tanggal,'%d %M %Y') as tanggal, ov.nik, namaKaryawan, section.nama as section, sub_section.nama as sub_sec, dari, sampai, jam, IF(ov.status = 1,'confirmed','not yet confirmed') as stat from
				( select id_ot, tanggal, nik, dari, sampai, jam, status from over_time left join over_time_member on over_time.id = over_time_member.id_ot where deleted_at is null and date_format(tanggal,'%Y-%m') = '" . $bulan . "' and nik is not null
				order by tanggal asc, nik asc
				) as ov join
				( select nik, tanggal from over_time left join over_time_member on over_time.id = over_time_member.id_ot where deleted_at is null and date_format(tanggal,'%Y-%m') = '" . $bulan . "' and nik is not null and jam_aktual = 0
				group by tanggal, nik
				having count(nik) > 1
				) a on ov.nik = a.nik and ov.tanggal = a.tanggal
				left join karyawan on karyawan.nik = ov.nik
				left join posisi on posisi.nik = ov.nik
				join section on section.id = posisi.id_sec
				join sub_section on sub_section.id = posisi.id_sub_sec
				" . $where . "
				order by ov.tanggal asc, a.nik asc";

        $get_double = db::connection('mysql3')->select($double);

        return DataTables::of($get_double)
            ->addColumn('action', function ($get_double) {
                return '<a href="javascript:void(0)" class="btn btn-xs btn-danger" onClick="delete_emp(this.id)" id="' . $get_double->id_ot . '+' . $get_double->nik . '"><i class="fa fa-trash"></i> Delete</a>';
            })
            ->rawColumns(['action' => 'action'])
            ->make(true);
    }
// --------------------- End Employement -----------------------

    public function fetchOvertime()
    {
        $username = Auth::user()->username;

        $bagian = Mutationlog::where("employee_id", "=", $username)
            ->whereNull("valid_to")
            ->select("department")
            ->first();

        if ($bagian) {
            $get_overtime = Overtime::whereNull('deleted_at')
                ->where("department", "=", $bagian->department)
                ->select('overtime_date', 'overtime_id', 'department', 'section', 'subsection', 'group')
                ->get();
        } else {
            $get_overtime = Overtime::whereNull('deleted_at')
                ->select('overtime_date', 'overtime_id', 'department', 'section', 'subsection', 'group')
                ->get();
        }

        return DataTables::of($get_overtime)
            ->addColumn('action', function ($get_overtime) {
                return '
					<a href="javascript:void(0)" class="btn btn-xs btn-warning" onClick="edit(this.id)" id="' . $get_overtime->overtime_id . '"><i class="fa fa-pencil"></i></a>
					&nbsp;
					<a href="javascript:void(0)" class="btn btn-xs btn-primary" onClick="details(this.id)" id="' . $get_overtime->overtime_id . '">Detail</a>
					&nbsp;
					<a href="javascript:void(0)" class="btn btn-xs btn-danger" onClick="delete_ot(this.id)" id="' . $get_overtime->overtime_id . '"><i class="fa fa-trash"></i></a>';
            })
            ->addColumn('libur', function ($get_overtime) {
                return '
					<button class="btn btn-xs btn-success" onClick="multi(' . $get_overtime->overtime_id . ')" id="add' . $get_overtime->overtime_id . '">Add <i class="fa fa-level-up"></i></button>';
            })
            ->rawColumns(['action' => 'action', 'libur' => 'libur'])
            ->make(true);
    }

    public function fetchOvertimeDetail(Request $request)
    {
        $ot_details = Overtime::leftJoin("overtime_details", "overtimes.overtime_id", "=", "overtime_details.overtime_id")
            ->leftJoin("employees", "employees.employee_id", "=", "overtime_details.employee_id")
            ->where("overtimes.overtime_id", "=", $request->get('overtime_id'))
            ->whereNull("overtime_details.deleted_at")
            ->select("overtimes.overtime_id", db::raw("date_format(overtimes.overtime_date,'%d-%m-%Y') as overtime_date"), "overtimes.division", "overtimes.department", "overtimes.section", "overtimes.subsection", "overtimes.group", "overtime_details.employee_id", "employees.name", "overtime_details.food", "overtime_details.ext_food", "overtime_details.transport", "overtime_details.start_time", "overtime_details.end_time", "overtime_details.final_hour", "overtime_details.purpose", "overtime_details.remark", "overtime_details.cost_center")
            ->get();

        $response = array(
            'status' => true,
            'data_details' => $ot_details,
        );
        return Response::json($response);
    }

    public function fetchOvertimeEdit($id)
    {
        $ot_details = Overtime::leftJoin("overtime_details", "overtimes.overtime_id", "=", "overtime_details.overtime_id")
            ->leftJoin("employees", "employees.employee_id", "=", "overtime_details.employee_id")
            ->where("overtimes.overtime_id", "=", $id)
            ->whereNull("overtime_details.deleted_at")
            ->select("overtimes.overtime_id", db::raw("date_format(overtimes.overtime_date,'%d-%m-%Y') as overtime_date"), "overtimes.division", "overtimes.department", "overtimes.section", "overtimes.subsection", "overtimes.group", "overtime_details.employee_id", "employees.name", "overtime_details.food", "overtime_details.ext_food", "overtime_details.transport", "overtime_details.start_time", "overtime_details.end_time", "overtime_details.final_hour", "overtime_details.purpose", "overtime_details.remark", "overtime_details.cost_center", "overtimes.day_status", "overtimes.shift", "overtime_details.ot_status")
            ->get();

        return view('overtimes.overtime_forms.edit', array(
            'datas' => $ot_details,
            'transports' => $this->transport,
            'purposes' => $this->purpose,
        ));
    }

    public function graphPrint(Request $request)
    {
        $dt = date('Y-m-d', strtotime($request->get("tanggal")));
        $query = '
				select DATE_FORMAT(cal.week_date,"%d/%m") week_date, COALESCE(jam,0) as final, (select round(budget_total / DAY(LAST_DAY("' . $dt . '")) , 2) as bdg_day from ftm.cost_center_budget where DATE_FORMAT(period,"%Y-%m") = DATE_FORMAT("' . $dt . '","%Y-%m") and id_cc = "' . $request->get("cc") . '") as day_bdg from
				(select week_date from weekly_calendars where DATE_FORMAT(week_date,"%Y-%m") = DATE_FORMAT("' . $dt . '","%Y-%m")) cal
				left join (
				select overtimes.overtime_date, overtime_details.cost_center, SUM(IF(status = 0,final_hour,final_overtime)) jam from overtimes
				join overtime_details on overtimes.overtime_id = overtime_details.overtime_id
				where DATE_FORMAT(overtimes.overtime_date,"%Y-%m") = DATE_FORMAT("' . $dt . '","%Y-%m") and
				overtime_details.cost_center = "' . $request->get("cc") . '"
				and overtime_details.deleted_at is null
				and overtimes.deleted_at is null
				group by overtimes.overtime_date, overtime_details.cost_center
				) act on cal.week_date = act.overtime_date
				where cal.week_date <= "' . $dt . '"';

        $get_graph = db::select($query);

        $response = array(
            'status' => true,
            'datas' => $get_graph,
        );
        return Response::json($response);
    }

    public function deleteOvertime(Request $request)
    {
        Overtime::where('overtime_id', $request->get('id'))->delete();
        $response = array(
            'status' => true,
        );
        return Response::json($response);
    }

    public function indexReportOvertimeAll()
    {
        return view('overtimes.reports.overtime_monthly')->with('page', 'Overtime Management by NIK');
    }

    public function fetchCostCenterBudget(Request $request)
    {
        $tgl = date('Y-m', strtotime($request->get('tgl')));
        $query = "select budgets.cost_center, period, budget from budgets
				left join cost_centers2 on budgets.cost_center = cost_centers2.cost_center
				where DATE_FORMAT(period,'%Y-%m') = '" . $tgl . "' and cost_centers2.cost_center_department = '" . $request->get('cc') . "' limit 1";

        $datas = DB::select($query);

        $response = array(
            'status' => true,
            'datas' => $datas,
        );

        return Response::json($response);
    }

    public function overtimeDetail(Request $request)
    {
        $from = date('Y-m-01', strtotime($request->get('tgl')));
        $to = date('Y-m-d', strtotime($request->get('tgl')));

        if ($from >= '2020-01-01') {

            $cost_center = db::table('cost_centers2')->where('cost_center_name', $request->get('cc'))
                ->select('cost_center')->first();

            $datas = db::connection('sunfish')->select("SELECT
						ot.Emp_no as nik,
						VIEW_YMPI_Emp_OrgUnit.Full_name as name,
						ot.jam,
						ot.kep
						FROM
						(
							SELECT
							VIEW_YMPI_Emp_OvertimePlan.emp_no,
							ROUND(SUM (
								CASE

								WHEN COALESCE ( VIEW_YMPI_Emp_OvertimePlan.total_ot, 0 ) > 0 THEN
								cast(VIEW_YMPI_Emp_OvertimePlan.total_ot as float)/ 60 ELSE cast(VIEW_YMPI_Emp_OvertimePlan.TOTAL_OVT_PLAN as float) / 60
								END
								), 2) AS jam,
							STUFF((
								SELECT distinct ',' + T.remark
								FROM VIEW_YMPI_Emp_OvertimePlan T
								WHERE VIEW_YMPI_Emp_OvertimePlan.emp_no = T.emp_no
								and T.ShiftStart >= '" . $from . " 00:00:00'
								and T.ShiftEnd <= '" . $to . " 23:59:59'
								FOR XML PATH('')), 1, 1, '') as kep
								FROM
								VIEW_YMPI_Emp_OvertimePlan
								WHERE
								VIEW_YMPI_Emp_OvertimePlan.ShiftStart >= '" . $from . " 00:00:00'
								AND VIEW_YMPI_Emp_OvertimePlan.ShiftEnd <= '" . $to . " 23:59:59'
								AND VIEW_YMPI_Emp_OvertimePlan.emp_no NOT LIKE 'OS%'
								GROUP BY
								VIEW_YMPI_Emp_OvertimePlan.emp_no
								) AS ot
								LEFT JOIN VIEW_YMPI_Emp_OrgUnit ON VIEW_YMPI_Emp_OrgUnit.Emp_no = ot.emp_no
								where
								VIEW_YMPI_Emp_OrgUnit.cost_center_code  = '" . $cost_center->cost_center . "'");
        } else {
            $cost_center = db::table('cost_centers')->where('cost_center_name', $request->get('cc'))
                ->select('cost_center')->first();

            $q = "SELECT
							over_time_member.nik,
							ympimis.employees.name,
							sum( IF ( over_time_member.STATUS = 0, over_time_member.jam, over_time_member.final ) ) AS jam,
							GROUP_CONCAT(over_time.keperluan) as kep
							FROM
							over_time_member
							LEFT JOIN over_time ON over_time.id = over_time_member.id_ot
							LEFT JOIN ( SELECT employee_id, cost_center FROM ympimis.mutation_logs WHERE valid_to IS NULL ) cc ON cc.employee_id = over_time_member.nik
							LEFT JOIN ympimis.employees ON ympimis.employees.employee_id = over_time_member.nik
							WHERE
							over_time.deleted_at IS NULL
							AND over_time.tanggal >= '" . $from . "'
							AND over_time.tanggal <= '" . $to . "'
							and cc.cost_center = '" . $cost_center->cost_center . "'
							GROUP BY
							over_time_member.nik, ympimis.employees.name";

            $datas = db::connection('mysql3')->select($q);
        }

        $response = array(
            'status' => true,
            'datas' => $datas,
            'cc' => $cost_center,
            'dt' => $to,
        );

        return Response::json($response);
    }

    public function fetchReportSection(Request $request)
    {
        $bulan = date('Y-m');

        $queryDate = "select DATE_FORMAT(week_date,'%Y-%m') as bulan from weekly_calendars where fiscal_year = '" . $request->get('tahun') . "' GROUP BY DATE_FORMAT(week_date,'%Y-%m')";

        $date = db::select($queryDate);

        $query = "select em.employee_id, name, mon, cost_center, COALESCE(jam,0) as jam from(
						select emp.*, bagian.cost_center from
						(select employee_id, name, mon from
						(
						select employee_id, name, date_format(hire_date, '%Y-%m') as hire_month, date_format(end_date, '%Y-%m') as end_month, mon from employees
						cross join (
						select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where fiscal_year = '" . $request->get('tahun') . "' and date_format(week_date, '%Y-%m') <= '" . $bulan . "' group by date_format(week_date, '%Y-%m')) s
						) m
						where hire_month <= mon and (mon < end_month OR end_month is null)
						) emp
						left join (
						SELECT id, employee_id, cost_center, date_format(valid_from, '%Y-%m') as mon_from, coalesce(date_format(valid_to, '%Y-%m'), date_format(DATE_ADD(now(), INTERVAL 1 MONTH),'%Y-%m')) as mon_to FROM mutation_logs
						WHERE id IN (SELECT MAX(id) FROM mutation_logs GROUP BY employee_id, DATE_FORMAT(valid_from,'%Y-%m'))
						) bagian on emp.employee_id = bagian.employee_id and emp.mon >= bagian.mon_from and emp.mon < mon_to
						where cost_center = '" . $request->get('section') . "') em left join

						(select DATE_FORMAT(tanggal,'%Y-%m') as bulan, nik, SUM(IF(status=1,final, jam)) as jam from ftm.over_time_member left join ftm.over_time on ftm.over_time.id = ftm.over_time_member.id_ot
						where deleted_at is null and jam_aktual = 0
						group by nik, DATE_FORMAT(tanggal,'%Y-%m')) ovr on em.employee_id = ovr.nik and em.mon = ovr.bulan";

        $datas = db::select($query);

        $queryBudget = "select cost_center, budget, budget_mp from budgets where cost_center = '" . $request->get('section') . "' and date_format(period,'%Y-%m') in (
						select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where fiscal_year = '" . $request->get('tahun') . "' group by date_format(week_date, '%Y-%m'))";

        $data_budgets = db::select($queryBudget);

        $response = array(
            'status' => true,
            'datas' => $datas,
            'budgets' => $data_budgets,
            'date' => $date,
        );

        return Response::json($response);
    }

    public function fetchOvertimeHead(Request $request)
    {
        if ($request->get('tgl') == "") {
            $tgl2 = date('Y-m');
            $tgl = date('Y-m-d', strtotime($tgl2 . "-01"));
        } else {
            $tgl2 = date('Y-m', strtotime($request->get('tgl')));
            $tgl = date('Y-m-d', strtotime($tgl2 . "-01"));
        }

        $spl = explode(",", $request->get('id'));

        $ot_grup = Overtime::leftJoin('overtime_details', 'overtime_details.overtime_id', '=', 'overtimes.overtime_id')
            ->whereIn('overtimes.overtime_id', $spl)
            ->whereNull('overtime_details.deleted_at')
            ->select('overtime_date', 'overtimes.overtime_id', db::raw('concat(section," - ",subsection," - ",`group`) as bagian'), db::raw('GROUP_CONCAT(DISTINCT overtime_details.remark) as reason'), db::raw('count(employee_id) as count_member'), db::raw('sum(final_hour) as total_hour'))
            ->groupBy('overtimes.overtime_id', 'overtime_date', 'section', 'subsection', 'group')
            ->get();

        $response = array(
            'status' => true,
            'datas' => $ot_grup,
        );
        return Response::json($response);
    }

    public function indexPrintHead(Request $request)
    {
        $ids = explode(",", $request->get('id'));

        $anggota = Overtime::leftJoin('overtime_details', 'overtime_details.overtime_id', '=', 'overtimes.overtime_id')
            ->whereIn('overtimes.overtime_id', $ids)
            ->whereNull('overtime_details.deleted_at')
            ->select('overtime_date', 'overtimes.overtime_id', db::raw('concat(section," - ",subsection," - ",`group`) as bagian'), db::raw('GROUP_CONCAT(DISTINCT overtime_details.remark) as reason'), db::raw('count(employee_id) as count_member'), db::raw('sum(final_hour) as total_hour'))
            ->groupBy('overtimes.overtime_id', 'overtime_date', 'section', 'subsection', 'group')
            ->orderBy('overtime_date')
            ->get();

        $tgl = $anggota[0]->overtime_date;
        $mon = date('Y-m', strtotime($anggota[0]->overtime_date));

        $cc = "select DISTINCT ovr.cost_center, cost_centers.cost_center_name, round(budget / DAY(LAST_DAY('" . $tgl . "')) * DAY('" . $tgl . "'),1) bdg, act, round((budget / DAY(LAST_DAY('" . $tgl . "')) * DAY('" . $tgl . "')) - act,1) as diff  from
						(select cost_center from overtimes left join overtime_details on overtimes.overtime_id = overtime_details.overtime_id where overtimes.overtime_id in (" . $request->get('id') . ") and overtime_details.deleted_at is null group by cost_center, overtime_date) ovr
						left join (select cost_center, period, budget from budgets where date_format(period,'%Y-%m') = '" . $mon . "') budgets on budgets.cost_center = ovr.cost_center
						left join (select cost_center, SUM(IF(status = 1, final_overtime, final_hour)) as act from overtimes left join overtime_details on overtimes.overtime_id = overtime_details.overtime_id where date_format(overtime_date,'%Y-%m') = '" . $mon . "' and overtime_date <= '" . $tgl . "' and overtimes.deleted_at is null and overtime_details.deleted_at is null group by cost_center) as act on act.cost_center = ovr.cost_center
						left join (select DISTINCT cost_center, cost_center_name from cost_centers) cost_centers on cost_centers.cost_center = ovr.cost_center";

        $cost_center = db::select($cc);

        return view('overtimes.overtime_forms.index_print_head', array(
            'anggota' => $anggota,
            'cc' => $cost_center,
        ));
    }

    public function fetchReportOutsource(Request $request)
    {
        if (strlen($request->get('bulan')) > 0) {
            $tgl = $request->get("bulan");
        } else {
            $tgl = date("m-Y");
        }

        $ot_outsource_q = "select ovr.period, ovr.nik, emp.namaKaryawan, ovr.jam from
						(select DATE_FORMAT(tanggal,'%m-%Y') as period, nik, SUM(IF(status = 0,jam, final)) jam
						from over_time left join over_time_member
						on over_time.id = over_time_member.id_ot
						where deleted_at is null and jam_aktual = 0 and nik like 'os%' and DATE_FORMAT(tanggal,'%m-%Y') = '" . $tgl . "'
						group by period, nik) ovr
						left join
						(select nik, namaKaryawan from karyawan where nik like 'os%' and tanggalKeluar is null) emp
						on ovr.nik = emp.nik";

        $ot_outsource = db::connection('mysql3')->select($ot_outsource_q);

        $response = array(
            'status' => true,
            'datas' => $ot_outsource,
            'bulan' => $tgl,
        );
        return Response::json($response);
    }

    public function fetchDetailOutsource(Request $request)
    {
        $period = $request->get("period");
        $nama = $request->get("nama");

        $query = "select ovr.tanggal, ovr.nik, emp.namaKaryawan, ovr.ot, ovr.reason from
						(select tanggal, nik, SUM(IF(status = 0,jam, final)) ot, GROUP_CONCAT(keperluan) reason
						from over_time left join over_time_member
						on over_time.id = over_time_member.id_ot
						where deleted_at is null and jam_aktual = 0 and nik like 'os%' and DATE_FORMAT(tanggal,'%m-%Y') = '" . $period . "'
						group by tanggal, nik) ovr
						left join
						(select nik, namaKaryawan from karyawan where nik like 'os%' and tanggalKeluar is null) emp
						on ovr.nik = emp.nik
						where namaKaryawan = '" . $nama . "'";

        $detail = db::connection('mysql3')->select($query);

        return DataTables::of($detail)->make(true);

    }

    public function fetchOvertimeDataOutsource(Request $request)
    {
        $dateto = date('Y-m-d');
        $datefrom = $request->get('datefrom');

        if ($request->get('dateto') != "") {
            $dateto = $request->get('dateto');
        }

        $query = "select ovr.tanggal, ovr.nik, karyawan.namaKaryawan, jam, reason from (select tanggal, nik, SUM(IF(status = 0,jam, final)) jam, GROUP_CONCAT(keperluan) reason from over_time left join over_time_member on over_time.id = over_time_member.id_ot
						where deleted_at is null and jam_aktual = 0 and nik like 'os%'
						group by nik, tanggal) ovr
						left join karyawan on ovr.nik = karyawan.nik";

        $os_ot = db::connection('mysql3')->select($query);

        $response = array(
            'status' => true,
            'datas' => $os_ot,
        );
        return DataTables::of($os_ot)->make(true);
    }

    public function fetchOvertimeByEmployee(Request $request)
    {
        $tanggal = "";
        $adddepartment = '';
        $addsection = '';
        $addnik = '';

        if (strlen($request->get('datefrom')) > 0) {
            $tanggal = "and DATE_FORMAT(tanggal,'%m-%Y') >= '" . $request->get('datefrom') . "' ";
            if (strlen($request->get('dateto')) > 0) {
                $tanggal = $tanggal . "and DATE_FORMAT(tanggal,'%m-%Y') <= '" . $request->get('dateto') . "' ";
            }
        }

        if ($request->get('department') != null) {
            $departments = $request->get('department');
            $department = "";
            for ($x = 0; $x < count($departments); $x++) {
                $department = $department . "'" . $departments[$x] . "'";
                if ($x != count($departments) - 1) {
                    $department = $department . ",";
                }
            }
            $adddepartment = "and bagian.department in (" . $department . ") ";
        }

        if ($request->get('section') != null) {
            $sections = $request->get('section');
            $section = "";
            for ($x = 0; $x < count($sections); $x++) {
                $section = $section . "'" . $sections[$x] . "'";
                if ($x != count($sections) - 1) {
                    $section = $section . ",";
                }
            }
            $addsection = "and bagian.section in (" . $section . ") ";
        }

        if ($request->get('nik') != null) {
            $niks = $request->get('nik');
            $nik = "";
            for ($x = 0; $x < count($niks); $x++) {
                $nik = $nik . "'" . $niks[$x] . "'";
                if ($x != count($niks) - 1) {
                    $nik = $nik . ",";
                }
            }
            $addnik = "and ovr.nik in (" . $nik . ") ";
        }

        $query = "select DATE_FORMAT(tanggal,'%m-%Y') as period, ovr.nik, emp.name, bagian.department, bagian.section, SUM(ot) as total from (select tanggal, nik, SUM(IF(status = 0, jam, final)) ot from over_time left join over_time_member on over_time.id = over_time_member.id_ot
						where deleted_at is null and jam_aktual = 0 " . $tanggal . " group by nik, tanggal) ovr
						left join ympimis.employees as emp on emp.employee_id = ovr.nik
						left join (select employee_id, department, section from ympimis.mutation_logs where valid_to is null) bagian on bagian.employee_id = ovr.nik
						where ot > 0 " . $addsection . "" . $addnik . "" . $adddepartment . "
						group by period, nik, ympimis.emp.name
						order by period, total asc";

        $data = db::connection('mysql3')->select($query);

        return DataTables::of($data)
            ->addColumn('detail', function ($data) {
                return '<input type="button" class="btn btn-success btn-sm" id="detail+' . $data->nik . '+' . $data->period . '" onclick="showModal( \'' . $data->nik . '\', \'' . $data->period . '\', \'' . $data->name . '\')" value="Detail">';
            })
            ->rawColumns(['action' => 'detail'])
            ->make(true);

    }

    public function detailOvertimeByEmployee(Request $request)
    {
        $query = "select distinct ovr.tanggal, ovr.nik, emp.name, bagian.department, bagian.section, ot, ovr.keperluan from
						(select tanggal, nik, SUM(IF(status = 0, jam, final)) ot, GROUP_CONCAT(keperluan) keperluan from over_time_member left join over_time on over_time.id = over_time_member.id_ot
						where deleted_at is null and jam_aktual = 0 and DATE_FORMAT(tanggal,'%m-%Y') = '" . $request->get('period') . "' group by tanggal, nik) ovr
						left join ympimis.employees as emp on emp.employee_id = ovr.nik
						left join (select employee_id, department, section from ympimis.mutation_logs where valid_to is null) bagian on bagian.employee_id = ovr.nik
						where ot > 0 and nik = '" . $request->get('nik') . "'
						order by ovr.tanggal asc";

        $data = db::connection('mysql3')->select($query);

        return DataTables::of($data)->make(true);
    }

    public function fetchGAReport(Request $request)
    {
        $now = date("Y-m-d", strtotime($request->get('tanggal')));
        $weekly_calendars = WeeklyCalendar::where('week_date', '=', $now)->first();

        if ($weekly_calendars->remark == 'H') {
            $details = db::connection('sunfish')->select("
								SELECT CONVERT
								( VARCHAR, VIEW_YMPI_Emp_OvertimePlan.ovtplanfrom, 108 ) AS ot_from,
								CONVERT ( VARCHAR, VIEW_YMPI_Emp_OvertimePlan.ovtplanto, 108 ) AS ot_to,
								VIEW_YMPI_Emp_OvertimePlan.SHIFT_OVTPLAN,
								VIEW_YMPI_Emp_OvertimePlan.emp_no,
								VIEW_YMPI_Emp_OvertimePlan.modified_date,
								VIEW_YMPI_Emp_OrgUnit.Full_name,
								VIEW_YMPI_Emp_OrgUnit.Section,
								COALESCE ( VIEW_YMPI_Emp_OvertimePlan.ovttrans, '-' ) AS trans,
								CASE

								WHEN DATEDIFF( MINUTE, ovtplanfrom, ovtplanto ) >= 300 THEN
								'Ya' ELSE '-'
								END AS food
								FROM
								VIEW_YMPI_Emp_OvertimePlan
								LEFT JOIN VIEW_YMPI_Emp_OrgUnit ON VIEW_YMPI_Emp_OrgUnit.Emp_no = VIEW_YMPI_Emp_OvertimePlan.emp_no
								WHERE
								CONVERT ( VARCHAR, ovtplanfrom, 105 ) = '" . $request->get('tanggal') . "'
								AND (COALESCE ( VIEW_YMPI_Emp_OvertimePlan.ovttrans, '-' ) <> '-'
								or
								CASE

								WHEN DATEDIFF( MINUTE, ovtplanfrom, ovtplanto ) >= 300 THEN
								'Ya' ELSE '-'
								END <> '-') order by VIEW_YMPI_Emp_OvertimePlan.SHIFT_OVTPLAN asc, VIEW_YMPI_Emp_OvertimePlan.emp_no asc
								");
            $ot = db::connection('sunfish')->select("
								select ot_from, ot_to, coalesce(sum(makan1),0) as makan1, coalesce(sum(makan2),0) as makan2, coalesce(sum(makan3),0) as makan3, coalesce(sum(extra2),0) as extra2, coalesce(sum(extra3),0) as extra3, coalesce(sum(trn_bgl),0) as trn_bgl, coalesce(sum(trn_psr),0) as trn_psr from
								(
								SELECT
								convert(varchar, ovtplanfrom, 108) as ot_from, convert(varchar, ovtplanto, 108) as ot_to,
								CASE
								WHEN
								SHIFT_OVTPLAN LIKE '%Shift_1%' and DATEDIFF(minute, ovtplanfrom, ovtplanto) >= 300
								THEN 1
								ELSE null
								END AS makan1,

								CASE
								WHEN
								SHIFT_OVTPLAN LIKE '%Shift_2%' and DATEDIFF(minute, ovtplanfrom, ovtplanto) >= 300
								THEN 1
								ELSE null
								END AS makan2,

								CASE
								WHEN
								SHIFT_OVTPLAN LIKE '%Shift_3%' and DATEDIFF(minute, ovtplanfrom, ovtplanto) >= 300
								THEN 1
								ELSE null
								END AS makan3,

								CASE
								WHEN
								SHIFT_OVTPLAN LIKE 'Shift_2%' and DATEDIFF(minute, ovtplanfrom, ovtplanto) >= 300
								THEN 1
								ELSE null
								END AS extra2,

								CASE
								WHEN
								SHIFT_OVTPLAN LIKE 'Shift_3%'
								THEN 1
								ELSE null
								END AS extra3,

								CASE
								WHEN
								ovttrans = 'TRNBGL'
								THEN 1
								ELSE null
								END AS trn_bgl,

								CASE
								WHEN
								ovttrans = 'TRNPSR'
								THEN 1
								ELSE null
								END AS trn_psr

								FROM
								VIEW_YMPI_Emp_OvertimePlan
								where convert(varchar, ovtplanfrom, 105) = '" . $request->get('tanggal') . "'
								) as ga_report group by ot_from, ot_to order by ot_to asc
								");
        } else {
            $details = db::connection('sunfish')->select("
								SELECT CONVERT
								( VARCHAR, VIEW_YMPI_Emp_OvertimePlan.ovtplanfrom, 108 ) AS ot_from,
								CONVERT ( VARCHAR, VIEW_YMPI_Emp_OvertimePlan.ovtplanto, 108 ) AS ot_to,
								VIEW_YMPI_Emp_OvertimePlan.SHIFT_OVTPLAN,
								VIEW_YMPI_Emp_OvertimePlan.emp_no,
								VIEW_YMPI_Emp_OrgUnit.Full_name,
								VIEW_YMPI_Emp_OrgUnit.Section,
								COALESCE ( VIEW_YMPI_Emp_OvertimePlan.ovttrans, '-' ) AS trans,
								CASE

								WHEN DATEDIFF( MINUTE, ovtplanfrom, ovtplanto ) >= 150 THEN
								'Ya' ELSE '-'
								END AS food
								FROM
								VIEW_YMPI_Emp_OvertimePlan
								LEFT JOIN VIEW_YMPI_Emp_OrgUnit ON VIEW_YMPI_Emp_OrgUnit.Emp_no = VIEW_YMPI_Emp_OvertimePlan.emp_no
								WHERE
								CONVERT ( VARCHAR, ovtplanfrom, 105 ) = '" . $request->get('tanggal') . "'
								AND (COALESCE ( VIEW_YMPI_Emp_OvertimePlan.ovttrans, '-' ) <> '-'
								or
								CASE

								WHEN DATEDIFF( MINUTE, ovtplanfrom, ovtplanto ) >= 150 THEN
								'Ya' ELSE '-'
								END <> '-') order by VIEW_YMPI_Emp_OvertimePlan.SHIFT_OVTPLAN asc, VIEW_YMPI_Emp_OvertimePlan.emp_no asc
								");
            $ot = db::connection('sunfish')->select("
								select ot_from, ot_to, coalesce(sum(makan1),0) as makan1, coalesce(sum(makan2),0) as makan2, coalesce(sum(makan3),0) as makan3, coalesce(sum(extra2),0) as extra2, coalesce(sum(extra3),0) as extra3, coalesce(sum(trn_bgl),0) as trn_bgl, coalesce(sum(trn_psr),0) as trn_psr from
								(
								SELECT
								convert(varchar, ovtplanfrom, 108) as ot_from, convert(varchar, ovtplanto, 108) as ot_to,
								CASE
								WHEN
								SHIFT_OVTPLAN LIKE '%Shift_1%' and DATEDIFF(minute, ovtplanfrom, ovtplanto) >= 150
								THEN 1
								ELSE null
								END AS makan1,

								CASE
								WHEN
								SHIFT_OVTPLAN LIKE '%Shift_2%' and DATEDIFF(minute, ovtplanfrom, ovtplanto) >= 150
								THEN 1
								ELSE null
								END AS makan2,

								CASE
								WHEN
								SHIFT_OVTPLAN LIKE '%Shift_3%' and DATEDIFF(minute, ovtplanfrom, ovtplanto) >= 150
								THEN 1
								ELSE null
								END AS makan3,

								CASE
								WHEN
								SHIFT_OVTPLAN LIKE '%Shift_2%' and DATEDIFF(minute, ovtplanfrom, ovtplanto) >= 120
								THEN 1
								ELSE null
								END AS extra2,

								CASE
								WHEN
								SHIFT_OVTPLAN LIKE '%Shift_3%'
								THEN 1
								ELSE null
								END AS extra3,

								CASE
								WHEN
								ovttrans = 'TRNBGL'
								THEN 1
								ELSE null
								END AS trn_bgl,

								CASE
								WHEN
								ovttrans = 'TRNPSR'
								THEN 1
								ELSE null
								END AS trn_psr

								FROM
								VIEW_YMPI_Emp_OvertimePlan
								where convert(varchar, ovtplanfrom, 105) = '" . $request->get('tanggal') . "'
								) as ga_report group by ot_from, ot_to order by ot_to asc
								");
        }

        $response = array(
            'status' => true,
            'datas' => $ot,
            'details' => $details,
        );
        return $response;
    }

    public function indexYearlyResume()
    {
        $title = 'Overtime Fiscal Year Resume';
        $title_jp = '年度残業まとめ';

        return view('overtimes.reports.overtime_yearly', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Overtime by Fiscal');
    }

    public function fetchYearlyResume(Request $request)
    {
        if ($request->get('fiscal') != "") {
            $fy = $request->get('fiscal');
        } else {
            $fy = 'FY197';
        }

        $fy_cal = DB::table('weekly_calendars')->where('fiscal_year', '=', $fy)->select(db::raw('DATE_FORMAT(week_date,"%Y-%m") as mon'))->groupBy(db::raw('DATE_FORMAT(week_date,"%Y-%m")'))->get();
        $fy_arr = [];

        foreach ($fy_cal as $fy2) {
            array_push($fy_arr, $fy2->mon);
        }

        $from = $fy_arr[0];
        $to = end($fy_arr);

        $act_data = db::connection('sunfish')->select("SELECT mon, ROUND(jam, 2) as jam_fix from
							(select FORMAT(VIEW_YMPI_Emp_OvertimePlan.ShiftStart, 'yyyy-MM') as mon, SUM (
								CASE
								WHEN COALESCE ( VIEW_YMPI_Emp_OvertimePlan.total_ot, 0 ) > 0 THEN
								cast(VIEW_YMPI_Emp_OvertimePlan.total_ot as float)/ 60 ELSE cast(VIEW_YMPI_Emp_OvertimePlan.TOTAL_OVT_PLAN as float) / 60
								END
								) AS jam from VIEW_YMPI_Emp_OvertimePlan
							where FORMAT(VIEW_YMPI_Emp_OvertimePlan.ShiftStart, 'yyyy-MM') >= '" . $from . "'
							and FORMAT(VIEW_YMPI_Emp_OvertimePlan.ShiftStart, 'yyyy-MM') <= '" . $to . "'
							GROUP BY FORMAT(VIEW_YMPI_Emp_OvertimePlan.ShiftStart, 'yyyy-MM')) mstr");

        $fq_data = db::select("SELECT dt_bdg as dt, SUM(budget) as budget, SUM(forecast) as fq from
								(select cost_centers2.cost_center, cost_center_name, bdg.dt as dt_bdg, fq.dt as dt_fq, ROUND(SUM(budget), 2) as budget, forecast from cost_centers2
									left join
									(select cost_center, DATE_FORMAT(period, '%Y-%m') as dt, budget from budgets
										where DATE_FORMAT(period, '%Y-%m') >= '" . $from . "'
										AND DATE_FORMAT(period, '%Y-%m') <= '" . $to . "') as bdg
										on bdg.cost_center = cost_centers2.cost_center
										left join
										(select cost_center, DATE_FORMAT(`date`, '%Y-%m') as dt, ROUND(SUM(`hour`), 2) as forecast from forecasts
										where DATE_FORMAT(`date`, '%Y-%m') >= '" . $from . "'
										AND DATE_FORMAT(`date`, '%Y-%m') <= '" . $to . "'
										group by DATE_FORMAT(`date`, '%Y-%m'), cost_center) as fq
										on fq.cost_center = cost_centers2.cost_center AND fq.dt = bdg.dt
										group by cost_centers2.cost_center, cost_center_name, bdg.dt, fq.dt, forecast
										) as mstr
										group by dt_bdg
										order by dt_bdg asc, cost_center asc");

        $response = array(
            'status' => true,
            'act' => $act_data,
            'fq' => $fq_data,
            'fy' => $fy,
        );

        return $response;
    }

    public function uploadOvertimeEat(Request $request)
    {
        try {

            if ($request->get('sts') == "ramadhan") {
                // code...
                $shift = $request->get('shift');

                $upload = $request->get('upload');
                $uploadRows = preg_split("/\r?\n/", $upload);
                $request_id = [];
                $names = [];
                $employees = [];

                $request_date = explode(' - ', $request->get('request_date'));
                $date = date('Y-m-d', strtotime($request_date[0]));
                $nik_error = [];
                $nik_no_request = [];

                $niks = [];

                for ($i = 0; $i < count($uploadRows); $i++) {
                    $get_user = EmployeeSync::where('employee_id', '=', $uploadRows[$i])->first();

                    if (count($get_user) > 0 && $get_user->gender == "P") {
                        if (in_array($get_user->employee_id, $employees)) {

                        } else {
                            array_push($employees, $get_user->employee_id);
                            array_push($names, [$get_user->employee_id, $get_user->name, $shift]);
                        }

                    } else {
                        if ($get_user->gender == "P") {
                            array_push($nik_error, strtoupper($uploadRows[$i]));
                        } else {
                            array_push($nik_no_request, [strtoupper($uploadRows[$i]), $get_user->gender]);
                        }
                    }

                }

                $response = array(
                    'status' => true,
                    'getData' => $employees,
                    'names' => $names,
                    'nik_error' => $nik_error,
                    'nik_no_request' => $nik_no_request,

                );
                return Response::json($response);
            } else {
                $shift = $request->get('shift');

                $upload = $request->get('upload');
                $uploadRows = preg_split("/\r?\n/", $upload);
                $request_id = [];
                $names = [];
                $employees = [];

                $request_date = explode(' - ', $request->get('request_date'));
                $date = date('Y-m-d', strtotime($request_date[0]));
                $nik_error = [];

                $niks = [];

                for ($i = 0; $i < count($uploadRows); $i++) {
                    $get_user = EmployeeSync::where('employee_id', '=', $uploadRows[$i])->first();

                    if (count($get_user) > 0) {
                        if (in_array($get_user->employee_id, $employees)) {

                        } else {
                            array_push($employees, $get_user->employee_id);
                            array_push($names, [$get_user->employee_id, $get_user->name, $shift]);
                        }

                    }

                }

                $response = array(
                    'status' => true,
                    'getData' => $employees,
                    'names' => $names,
                    'nik_error' => $nik_error,

                );
                return Response::json($response);

            }
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }
    public function create_overtime()
    {
        $title = 'Order Makan Overtime';
        $title_jp = '産業用の食事発注申請';
        $user = EmployeeSync::where('employee_id', Auth::user()->username)->first();
        $role_user = User::where('username', Auth::user()->username)->first();

        return view('overtimes.reports.ga_report_new', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'userss' => $user,
            'role_user' => $role_user,

        ))->with('page', 'Order Makan');
    }
    public function createOvertime(Request $request)
    {
        try
        {

            $employees = $request->get('employees');
            $shift = $request->get('shift');
            $time_in = $request->get('time_in');
            $time_out = $request->get('time_out');
            $makan = $request->get('food');
            $extra_food = $request->get('extra_food');
            $tranport = $request->get('tranport');
            $food = "";
            $ext_food = "";

            for ($i = 0; $i < count($employees); $i++) {
                if ($makan[$i] == "NO") {
                    $food = 0;
                } else {
                    $food = 1;
                }

                if ($extra_food[$i] == "NO") {
                    $ext_food = 0;

                } else {
                    $ext_food = 1;
                }

                $overtimes_save = new OvertimeEmployee([
                    'date' => date('Y-m-d'),
                    'employee_id' => $employees[$i],
                    'shift' => $shift[$i],
                    'start_time' => $time_in[$i],
                    'end_time' => $time_out[$i],
                    'food' => $food,
                    'ext_food' => $ext_food,
                    'transport' => $tranport[$i]['transport_kota'],
                    'created_by' => Auth::user()->username,
                ]);
                $overtimes_save->save();

                $user_ext = EmployeeSync::where('employee_id', $employees[$i])->first();

                if ($ext_food == 1) {
                    $attendance = OvertimeExtrafoodAttendance::create([
                        'employee_id' => $employees[$i],
                        'name' => $user_ext->name,
                        'time_in' => $time_in[$i],
                        'section' => $user_ext->section,
                        'created_by' => Auth::user()->username,
                    ]);
                    $attendance->save();

                }

                if ($food == 1) {
                    $date = date('Y-m-d', strtotime($time_in[$i]));

                    $attendance_scan = OvertimeExtrafoodAttendance::create([
                        'employee_id' => $employees[$i],
                        'name' => $user_ext->name,
                        'time_in' => $time_in[$i],
                        'section' => $user_ext->section,
                        'created_by' => Auth::user()->username,
                    ]);
                    $attendance_scan->save();
                }

            }

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

    public function createOrderFood(Request $request)
    {
        try
        {
            $employees = $request->get('employees');
            $shift = $request->get('shift');
            $dates = $request->get('dates');
            $statu = $request->get('sta');

            $jam = date('Y-m-d H:i:s', strtotime($request->get('dates') . date('H:i:s')));

            for ($i = 0; $i < count($employees); $i++) {
                $user_ext = EmployeeSync::where('employee_id', $employees[$i])->first();

                if ($statu == "Order Extra Food") {
                    $attendance = OvertimeExtrafoodAttendance::firstOrNew(
                        [
                            'employee_id' => $employees[$i],
                            'time_in' => $dates,

                        ],
                        [
                            'dates' => $jam,
                            'shift' => $shift[$i],
                            'name' => $user_ext->name,
                            'section' => $user_ext->section,
                            'remark' => 'Order Extra Food',
                            'created_by' => Auth::user()->username,
                        ]
                    );

                    $attendance->save();

                } else if ($statu == "Order Makan Ramadhan") {
                    $attendance_scan = OvertimeExtrafoodAttendance::firstOrNew(
                        [
                            'employee_id' => $employees[$i],
                            'time_in' => $dates,

                        ],
                        [
                            'dates' => $jam,
                            'shift' => $shift[$i],
                            'name' => $user_ext->name,
                            'section' => $user_ext->section,
                            'remark' => 'Order Makan Ramadhan',
                            'created_by' => Auth::user()->username,
                        ]
                    );
                    $attendance_scan->save();

                }

            }

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
    public function fetch_report_overtime(Request $request)
    {
        try {

            if (strlen($request->get('date')) > 0) {
                $tgl = date('Y-m-d', strtotime($request->get('date')));
                $jam = date('Y-m-d H:i:s', strtotime($request->get('date') . date('H:i:s')));
                if ($jam > date('Y-m-d', strtotime($tgl)) . ' 00:00:01' && $jam < date('Y-m-d', strtotime($tgl)) . ' 02:00:00' && $tgl == date('Y-m-d', strtotime($tgl))) {
                    $nextday = date('Y-m-d', strtotime($tgl));
                    $yesterday = date('Y-m-d', strtotime($tgl . " -1 days"));
                } else {
                    $nextday = date('Y-m-d', strtotime($tgl . " +1 days"));
                    $yesterday = date('Y-m-d', strtotime($tgl));
                }
            } else {
                $tgl = date("Y-m-d");
                $jam = date('Y-m-d H:i:s');
                if ($jam > date('Y-m-d', strtotime($tgl)) . ' 00:00:01' && $jam < date('Y-m-d', strtotime($tgl)) . ' 02:00:00') {
                    $nextday = date('Y-m-d');
                    $yesterday = date('Y-m-d', strtotime("-1 days"));
                } else {
                    $nextday = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
                    $yesterday = date('Y-m-d');
                }
            }

            $role_user = User::where('username', Auth::user()->username)->first();

            $datas = DB::select("SELECT
											*
											FROM
											`overtime_extrafood_attendances`
											WHERE dates >= '" . $yesterday . " 00:00:01' && dates <= '" . $nextday . " 02:30:00'
											ORDER BY updated_at DESC
											");

            $response = array(
                'status' => true,
                'role_user' => $role_user,
                'datas' => $datas,
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function indexOvertimeAttend()
    {
        $title = 'Attendance Order Makan';
        $title_jp = '??';
        $user = EmployeeSync::where('employee_id', Auth::user()->username)->first();
        $role_user = User::where('username', Auth::user()->username)->first();
        $tgl = date("Y-m-d");
        $jam = date('Y-m-d H:i:s');
        if ($jam > date('Y-m-d', strtotime($tgl)) . ' 00:00:01' && $jam < date('Y-m-d', strtotime($tgl)) . ' 08:30:00') {
            $yesterday = date('Y-m-d', strtotime("-1 days"));
        } else {
            $yesterday = date('Y-m-d');
        }

        $data_emp = DB::select("SELECT
										employee_syncs.employee_id,
										employee_syncs.`name`,
										sunfish_shift_syncs.shiftdaily_code,
										sunfish_shift_syncs.shift_date,
										employee_syncs.section,
										sunfish_shift_syncs.attend_code
										FROM
										employee_syncs
										LEFT JOIN sunfish_shift_syncs ON sunfish_shift_syncs.employee_id = employee_syncs.employee_id
										AND sunfish_shift_syncs.shift_date = '" . $yesterday . "'
										WHERE
										sunfish_shift_syncs.shiftdaily_code != '%OFF%'
										AND sunfish_shift_syncs.shiftdaily_code LIKE '%4G%'
										AND sunfish_shift_syncs.shiftdaily_code != '4G_Shift_2'
										AND sunfish_shift_syncs.shift_date = '" . $yesterday . "'
										AND sunfish_shift_syncs.shiftdaily_code NOT LIKE '%Shift_1%'
										OR ( sunfish_shift_syncs.shiftdaily_code LIKE '%Shift_3%' AND sunfish_shift_syncs.shift_date = '" . $yesterday . "' AND sunfish_shift_syncs.shiftdaily_code NOT LIKE '%OFF%' )
										OR ( employee_syncs.`group` = 'Security Group' AND employee_syncs.end_date IS NULL )
										OR (
										employee_syncs.`department` = 'Management Information System Department'
										AND employee_syncs.end_date IS NULL
										)
										");

        return view('overtimes.reports.attendance_overtime', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'data_emp' => $data_emp,
            'role_user' => $role_user,
            'user' => $user,

        ))->with('page', 'Order Makan');
    }

    public function fetchListOvertime(Request $request)
    {
        try {
            $tgl = date("Y-m-d");
            $jam = date('Y-m-d H:i:s');
            if ($jam > date('Y-m-d', strtotime($tgl)) . ' 00:00:01' && $jam < date('Y-m-d', strtotime($tgl)) . ' 08:30:00') {
                $nextday = date('Y-m-d');
                $yesterday = date('Y-m-d', strtotime("-1 days"));
            } else {

                $nextday = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
                $yesterday = date('Y-m-d');
            }

            $datas = DB::select("SELECT
											*
											FROM
											`overtime_extrafood_attendances`
											WHERE remark = 'Order Extra Food' && status IS NULL && dates >= '" . $yesterday . " 00:00:01' && dates <= '" . $nextday . " 08:30:00'
											ORDER BY attend_date DESC
											");

            $datas_rmd = DB::select("SELECT
											*
											FROM
											`overtime_extrafood_attendances`
											WHERE remark = 'Order Makan Ramadhan' && status IS NULL && dates >= '" . $yesterday . " 00:00:01' && dates <= '" . $nextday . " 08:30:00'
											ORDER BY attend_date DESC
											");
            $datas_rov = DB::select("SELECT
											*
											FROM
											`overtime_extrafood_attendances`
											WHERE remark = 'Order Makan Overtime' && status IS NULL && dates >= '" . $yesterday . " 00:00:01' && dates <= '" . $nextday . " 08:30:00'
											ORDER BY attend_date DESC
											");

            $response = array(
                'status' => true,
                'datas' => $datas,
                'datas_rmd' => $datas_rmd,
                'datas_rov' => $datas_rov,
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchOvertimeAttendance(Request $request)
    {

        $tgl = date("Y-m-d");
        $jam = date('Y-m-d H:i:s');
        if ($jam > date('Y-m-d', strtotime($tgl)) . ' 00:00:01' && $jam < date('Y-m-d', strtotime($tgl)) . ' 08:30:00') {
            $nextday = date('Y-m-d');
            $yesterday = date('Y-m-d', strtotime("-1 days"));

        } else {
            $nextday = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
            $yesterday = date('Y-m-d');
        }

        if (is_numeric($request->get('tag'))) {
            $nik = $request->get('tag');

            if (strlen($nik) > 9) {
                $nik = substr($nik, 0, 9);
            }

            $employee = OvertimeExtrafoodAttendance::leftJoin("employees", "employees.employee_id", "=", "overtime_extrafood_attendances.employee_id")
                ->where("overtime_extrafood_attendances.time_in", "=", $yesterday)
                ->where("employees.tag", "=", $request->get('tag'))
                ->whereNull("overtime_extrafood_attendances.deleted_at")
                ->select("overtime_extrafood_attendances.id", "overtime_extrafood_attendances.employee_id", "overtime_extrafood_attendances.name", "overtime_extrafood_attendances.section", "employees.tag", "overtime_extrafood_attendances.shift", "overtime_extrafood_attendances.time_in", "overtime_extrafood_attendances.attend_date", "overtime_extrafood_attendances.remark", "overtime_extrafood_attendances.created_by")
                ->first();

        } else {
            $nik = $request->get('tag');
            $employee = db::table('overtime_extrafood_attendances')->where('employee_id', 'like', '%' . $nik . '%')
                ->where('time_in', '=', $yesterday)->first();

        }

        if (count($employee) > 0) {
            if ($employee->attend_date != null) {
                $response = array(
                    'status' => false,
                    'message' => 'Already attended / Sudah Pernah Scan',
                );
                return Response::json($response);
            } else {
                $attend_update = OvertimeExtrafoodAttendance::where('employee_id', '=', $employee->employee_id)
                    ->where("remark", "=", "Order Extra Food")
                    ->where("time_in", "=", $yesterday)
                    ->update([
                        'attend_date' => date('Y-m-d H:i:s'),

                    ]);
                $response = array(
                    'status' => true,
                    'message' => 'Data Berhasil Disimpan',
                    'employee' => $employee,
                );
                return Response::json($response);
            }

        } else {
            $response = array(
                'status' => false,
                'message' => 'ID Tag tidak terdapat pada list',
            );
            return Response::json($response);
        }
    }

    public function fetchOvertimeAttendance2(Request $request)
    {

        $tgl = date("Y-m-d");
        $jam = date('Y-m-d H:i:s');
        if ($jam > date('Y-m-d', strtotime($tgl)) . ' 00:00:01' && $jam < date('Y-m-d', strtotime($tgl)) . ' 08:30:00') {
            $nextday = date('Y-m-d');
            $yesterday = date('Y-m-d', strtotime("-1 days"));

        } else {
            $nextday = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
            $yesterday = date('Y-m-d');
        }

        if (is_numeric($request->get('tag2'))) {
            $nik = $request->get('tag2');

            if (strlen($nik) > 9) {
                $nik = substr($nik, 0, 9);
            }

            $employee = OvertimeExtrafoodAttendance::leftJoin("employees", "employees.employee_id", "=", "overtime_extrafood_attendances.employee_id")
                ->where("overtime_extrafood_attendances.time_in", "=", $yesterday)
                ->where("employees.tag", "=", $request->get('tag2'))
                ->whereNull("overtime_extrafood_attendances.deleted_at")
                ->select("overtime_extrafood_attendances.id", "overtime_extrafood_attendances.employee_id", "overtime_extrafood_attendances.name", "overtime_extrafood_attendances.section", "employees.tag", "overtime_extrafood_attendances.shift", "overtime_extrafood_attendances.time_in", "overtime_extrafood_attendances.attend_date", "overtime_extrafood_attendances.remark", "overtime_extrafood_attendances.created_by")
                ->first();

        } else {
            $nik = $request->get('tag2');
            $employee = db::table('overtime_extrafood_attendances')->where('employee_id', 'like', '%' . $nik . '%')
                ->where('time_in', '=', $yesterday)->first();
        }

        if (count($employee) > 0) {
            if ($employee->attend_date != null) {
                $response = array(
                    'status' => false,
                    'message' => 'Already attended / Sudah Pernah Scan',
                );
                return Response::json($response);
            } else {
                $attend_update = OvertimeExtrafoodAttendance::where('employee_id', '=', $employee->employee_id)
                    ->where("remark", "=", "Order Makan Overtime")
                    ->where("time_in", "=", $yesterday)
                    ->update([
                        'attend_date' => date('Y-m-d H:i:s'),
                    ]);
                $response = array(
                    'status' => true,
                    'message' => 'Data Berhasil Disimpan',
                    'employee' => $employee,
                );
                return Response::json($response);
            }

        } else {
            $response = array(
                'status' => false,
                'message' => 'ID Tag tidak terdapat pada list',
            );
            return Response::json($response);
        }
    }

    public function exportOvertimeAll(Request $request)
    {
        $time = date('d-m-Y H;i;s');

        $tanggal = "";

        if ($request->get('tanggal_hid') == null) {
            $tanggal = date('Y-m-d');

        } else {
            $tanggal = $request->get('tanggal_hid');

        }

        $detail = db::select('SELECT
										DATE_FORMAT( start_time, "%Y-%m-%d" ) AS tanggal,
										employee_syncs.name,
										employee_syncs.employee_id,
										employee_syncs.section,
										shift,
										DATE_FORMAT( start_time, "%H:%i:%s" ) AS time_in,
										DATE_FORMAT( end_time, "%H:%i:%s" ) AS time_end,
										food,
										ext_food,
										transport,
										general_attendances.attend_date
										FROM
										`overtime_employees`
										LEFT JOIN employee_syncs ON employee_syncs.employee_id = overtime_employees.employee_id
										LEFT JOIN general_attendances ON employee_syncs.employee_id = general_attendances.employee_id
										WHERE
										DATE_FORMAT( start_time, "%Y-%m-%d" ) = "' . $tanggal . '" AND general_attendances.due_date = "' . $tanggal . '" ');

        $data = array(
            'detail' => $detail,
        );

        ob_clean();

        Excel::create('Report Overtime Makan & Transport', function ($excel) use ($data) {
            $excel->sheet('Data', function ($sheet) use ($data) {
                return $sheet->loadView('overtimes.reports.overtime_food_excel', $data);
            });

            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:K' . $lastrow)->getAlignment()->setWrapText(true);

        })->export('xlsx');
    }

    public function deleteOvertimeRequest(Request $request)
    {
        try
        {

            $delete = DB::table('overtime_extrafood_attendances')
                ->where('id', '=', $request->get('id'))
                ->delete();

            $response = array(
                'status' => true,
            );

            return Response::json($response);

        } catch (QueryException $e) {
            $response = array(
                'status' => false,
            );

            return Response::json($response);

        }
    }

    public function fetchResumeFood(Request $request)
    {
        try {
            $datefrom = $request->get('datefrom');
            $dateto = $request->get('dateto');

            if ($datefrom == '') {
                $datefrom = date('Y-m-d');
            } else {
                $datefrom = $request->get('datefrom');
            }

            if ($dateto == '') {
                $dateto = date('Y-m-d');
            } else {
                $dateto = $request->get('dateto');
            }

            $detail = DB::select("
											SELECT
											*
											FROM
											overtime_extrafood_attendances
											WHERE
											time_in >= '" . $datefrom . "'
											AND time_in <= '" . $dateto . "'
											order by attend_date DESC
											");

            $response = array(
                'status' => true,
                'datas' => $detail,
            );
            return Response::json($response);

        } catch (\Exception$e) {

            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);

        }
    }

    public function fetchOrderFoodList(Request $request)
    {

        if (Auth::user()->role_code == 'GA' || Auth::user()->role_code == 'HR' || Auth::user()->role_code == 'S-MIS') {
            $period = date('Y-m', strtotime($request->get('resume')));

            $calendars = WeeklyCalendar::where(db::raw('date_format(week_date, "%Y-%m")'), '=', $period)
                ->select('week_date', db::raw('date_format(week_date, "%d") as header'), 'remark')
                ->get();

            $foods = db::select("
											SELECT
											employee_id,
											NAME AS employee_name,
											time_in,
											count( id ) AS qty
											FROM
											`overtime_extrafood_attendances`
											WHERE
											date_format( time_in, '%Y-%m' ) = '" . $period . "'
											AND deleted_at IS NULL
											AND attend_date IS NOT NULL
											GROUP BY
											employee_id,
											NAME,
											time_in");

            $response = array(
                'status' => true,
                'foods' => $foods,
                'calendars' => $calendars,
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'message' => 'You do not have permission to access this data',
            );
            return Response::json($response);
        }

    }

    public function createEmpExtraFood(Request $request)
    {
        try
        {
            $employees = $request->get('employees');
            $name = $request->get('name');
            $shift = $request->get('shift');
            $section = $request->get('section');

            $tgl = date("Y-m-d");
            $jam = date('Y-m-d H:i:s');
            if ($jam > date('Y-m-d', strtotime($tgl)) . ' 00:00:01' && $jam < date('Y-m-d', strtotime($tgl)) . ' 08:30:00') {
                $nextday = date('Y-m-d');
                $yesterday = date('Y-m-d', strtotime("-1 days"));

            } else {
                $nextday = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
                $yesterday = date('Y-m-d');
            }

            $attendance = OvertimeExtrafoodAttendance::firstOrNew(
                [
                    'employee_id' => $employees,
                    'time_in' => $yesterday,

                ],
                [
                    'dates' => $yesterday . ' ' . date('H:i:s'),
                    'shift' => $shift,
                    'shiftdaily_code' => $shift,
                    'name' => $name,
                    'section' => $section,
                    'remark' => 'Order Extra Food',
                    'status' => 'tambahan',
                    'attend_date' => date('Y-m-d H:i:s'),
                    'created_by' => Auth::user()->username,
                ]
            );

            $attendance->save();

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

    public function fetchListExtraTam(Request $request)
    {
        try {
            $tgl = date("Y-m-d");
            $jam = date('Y-m-d H:i:s');
            if ($jam > date('Y-m-d', strtotime($tgl)) . ' 00:00:01' && $jam < date('Y-m-d', strtotime($tgl)) . ' 08:30:00') {
                $nextday = date('Y-m-d');
                $yesterday = date('Y-m-d', strtotime("-1 days"));
            } else {

                $nextday = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
                $yesterday = date('Y-m-d');
            }

            $datas_tam = DB::select("SELECT
											*
											FROM
											`overtime_extrafood_attendances`
											WHERE remark = 'Order Extra Food' && status = 'tambahan' && dates >= '" . $yesterday . " 00:00:01' && dates <= '" . $nextday . " 08:30:00'
											ORDER BY attend_date DESC
											");

            $response = array(
                'status' => true,
                'datas_tam' => $datas_tam,
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function deleteDataExtraTam(Request $request)
    {
        try {
            $request_id = $request->get('id');

            $jobs = db::table('overtime_extrafood_attendances')->where('id', '=', $request_id)->delete();

            $response = array(
                'status' => true,
                'message' => 'Success Hapus Data',
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

}
