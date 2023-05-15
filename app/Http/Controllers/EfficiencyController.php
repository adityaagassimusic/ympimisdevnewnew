<?php

namespace App\Http\Controllers;

use App\EfficiencyManpower;
use App\EfficiencyMonthly;
use App\EfficiencyNonProduction;
use App\EfficiencyTarget;
use App\Employee;
use App\EmployeeSync;
use App\Http\Controllers\Controller;
use App\OperatorLossTime;
use App\OperatorLossTimeLog;
use App\WeeklyCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

class EfficiencyController extends Controller
{

    public function __construct()
    {
        $this->remarks = array(
            [
                'department' => 'Educational Instrument (EI) Department',
                'remark' => [
                    'MANAGER',
                    'MOUTHPIECE ASSY',
                    'MOUTHPIECE PROCES GROUP',
                    'PIANICA ASSY',
                    'PIANICA PROCESS',
                    'PN ASSEMBLY GROUP',
                    'PN PART PROCESS GROUP',
                    'PN REED ADJUST GROUP',
                    'RC ASSEMBLY GROUP',
                    'RC INJECTION GROUP',
                    'RECORDER ASSY',
                    'RECORDER INJECTION',
                    'REED PLATE',
                ],
            ],
            [
                'department' => 'Woodwind Instrument - Parts Process (WI-PP) Department',
                'remark' => [
                    'MANAGER',
                    'FOREMAN',
                    'LOTTING',
                    'NC LATHE',
                    'NC MACHINING',
                    'PRESS PROCESS',
                    'SANDING PROCESS',
                    'STAFF',
                    'Z PRO',
                    'AS BELL',
                    'AS BODY',
                    'AS BOW',
                    'AS/TS NECK',
                    'FL BODY - FOOT',
                    'FL HEAD JOINT',
                    'YDS BODY PROCESS',
                    'INDIRECT',
                ],
            ],
            [
                'department' => 'Woodwind Instrument - Key Parts Process (WI-KPP) Department',
                'remark' => [
                    'FOREMAN',
                    'LOTTING',
                    'NC LATHE',
                    'NC MACHINING',
                    'PRESS PROCESS',
                    'SANDING PROCESS',
                    'STAFF',
                    'Z PRO',
                ],
            ],
            [
                'department' => 'Woodwind Instrument - Body Parts Process (WI-BPP) Department',
                'remark' => [
                    'AS BELL',
                    'AS BODY',
                    'AS BOW',
                    'AS/TS NECK',
                    'FL BODY - FOOT',
                    'FL HEAD JOINT',
                    'YDS BODY PROCESS',
                ],
            ],
            [
                'department' => 'Woodwind Instrument - Welding Process (WI-WP) Department',
                'remark' => [
                    'HANDATSUKE CL KP',
                    'HANDATSUKE FL',
                    'HANDATSUKE SAX',
                    'INDIRECT',
                    'SOLDER CL KEY',
                    'SOLDER FL KEY',
                    'SOLDER SAX KEY',
                ],
            ],
            [
                'department' => 'Woodwind Instrument - Surface Treatment (WI-ST) Department',
                'remark' => [
                    'BUFFING CL KEY',
                    'BUFFING FL BODY',
                    'BUFFING FL KEY',
                    'BUFFING SAX BODY',
                    'BUFFING SAX KEY',
                    'LACQUERING SAX BODY',
                    'LACQUERING SAX KEY',
                    'PLATING CL KEY',
                    'PLATING FL BODY',
                    'PLATING FL KEY',
                    'PLATING SAX BODY',
                    'PLATING SAX KEY',
                    'WST - OFFICE',
                ],
            ],
            [
                'department' => 'Woodwind Instrument - Assembly (WI-A) Department',
                'remark' => [
                    'ASSEMBLY ( WI - A )',
                    'ASSEMBLY PROCESS',
                    'ASSEMBLY PROCESS CONTROL',
                    'CASE',
                    'CL ASSY - SUB ASSY',
                    'CL BODY',
                    'FL ASSY - SUB ASSY',
                    'FL PAD',
                    'SAX ASSY - SUB ASSY',
                    'SAX PAD',
                ],
            ],
        );

    }

    public function indexEfficiencyMonitoring()
    {
        $title = "Efficiency Monitoring";
        $title_jp = "効率の監視";

        $weeks = db::select("SELECT DISTINCT
               fiscal_year,
               DATE_FORMAT( week_date, '%M' ) AS bulan,
               DATE_FORMAT( week_date, '%Y-%m' ) AS indek
               FROM
               weekly_calendars
               WHERE
               week_date >= '2022-04-01'
               AND week_date <= '" . date('Y-m-d') . "'
               ORDER BY
               week_date DESC");

        return view(
            'efficiency.monitoring',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'weeks' => $weeks,
            )
        );

    }

    public function fetchEfficiencyMonitoringModal(Request $request)
    {
        $date_from = date('Y-m-01');
        $date_to = date('Y-m-t');

        if ($request->get('type') == 'daily') {
            $period = date('Y-m');
            if (strlen($request->get('period')) > 0) {
                $period = date('Y-m', strtotime($request->get('period')));
            }
            $date_from = $period . '-' . $request->get('date');
            $date_to = $period . '-' . $request->get('date');
        }

        if ($request->get('type') == 'monthly') {
            if (strlen($request->get('period')) > 0) {
                $date_from = date('Y-m-01', strtotime($request->get('period')));
                $date_to = date('Y-m-t', strtotime($request->get('period')));
            }
        }

        if ($request->get('remark') == 'EDUCATIONAL INSTRUMENT') {
            $where_department = "AND department = 'Educational Instrument (EI) Department'";
        }
        if ($request->get('remark') == 'KEY PARTS PROCESS') {
            $where_department = "AND department = 'Woodwind Instrument - Key Parts Process (WI-KPP) Department'";
        }
        if ($request->get('remark') == 'BODY PARTS PROCESS') {
            $where_department = "AND department = 'Woodwind Instrument - Body Parts Process (WI-BPP) Department'";
        }
        if ($request->get('remark') == 'WELDING PROCESS') {
            $where_department = "AND department = 'Woodwind Instrument - Welding Process (WI-WP) Department'";
        }
        if ($request->get('remark') == 'SURFACE TREATMENT') {
            $where_department = "AND department = 'Woodwind Instrument - Surface Treatment (WI-ST) Department'";
        }
        if ($request->get('remark') == 'ASSEMBLY') {
            $where_department = "AND department = 'Woodwind Instrument - Assembly (WI-A) Department' AND remark <> 'CASE'";
        }
        if ($request->get('remark') == 'CASE') {
            $where_department = "AND department = 'Woodwind Instrument - Assembly (WI-A) Department' AND remark = 'CASE'";
        }
        if ($request->get('remark') == 'PARTS PROCESS') {
            $where_department = "AND department = 'Woodwind Instrument - Parts Process (WI-PP) Department'";
        }
        if ($request->get('remark') == 'YMPI') {
            $where_department = "";
        }

        $inputs = db::connection('ympimis_2')->select("SELECT
        	efficiency_inputs.employee_id,
        	efficiency_inputs.employee_name,
        	efficiency_inputs.department,
        	efficiency_inputs.remark,
        	efficiency_inputs.result_date,
        	sum( efficiency_inputs.attendance ) AS attendance,
        	sum( efficiency_inputs.overtime ) AS overtime,
        	sum( efficiency_inputs.diversion ) AS diversion,
        	sum( efficiency_inputs.input ) AS input
        FROM
        	efficiency_inputs
        WHERE
        	result_date >= '" . $date_from . "'
        	AND result_date <= '" . $date_to . "'
            " . $where_department . "
        GROUP BY
        	efficiency_inputs.employee_id,
        	efficiency_inputs.employee_name,
        	efficiency_inputs.department,
        	efficiency_inputs.remark,
        	efficiency_inputs.result_date
        ORDER BY
        	remark ASC,
        	result_date ASC");

        $outputs = db::connection('ympimis_2')->select("SELECT
        	efficiency_outputs.material_number,
        	efficiency_outputs.material_description,
        	efficiency_outputs.department,
        	efficiency_outputs.remark,
        	efficiency_outputs.result_date,
        	efficiency_outputs.standard_time,
        	sum( efficiency_outputs.quantity ) AS quantity,
        	sum( efficiency_outputs.output ) AS output
        FROM
        	efficiency_outputs
        WHERE
        	result_date >= '" . $date_from . "'
        	AND result_date <= '" . $date_to . "'
            " . $where_department . "
        GROUP BY
        	efficiency_outputs.material_number,
        	efficiency_outputs.material_description,
        	efficiency_outputs.department,
        	efficiency_outputs.remark,
        	efficiency_outputs.result_date,
        	efficiency_outputs.standard_time
        ORDER BY
        	remark ASC,
        	result_date ASC");

        $response = array(
            'status' => true,
            'inputs' => $inputs,
            'outputs' => $outputs,
        );
        return Response::json($response);

    }

    public function fetchEfficiencyMonitoring(Request $request)
    {

        $date_from = date('Y-m-01');
        $date_to = date('Y-m-t');

        if (strlen($request->get('period')) > 0) {
            $date_from = date('Y-m-01', strtotime($request->get('period')));
            $date_to = date('Y-m-t', strtotime($request->get('period')));
        }

        $period_title = date('F Y', strtotime($date_from));

        $weekly_calendar = db::select("SELECT
            	fiscal_year,
            	min( week_date ) AS fiscal_from,
            	max( week_date ) AS fiscal_to
            FROM
            	weekly_calendars
            WHERE
            	fiscal_year = ( SELECT fiscal_year FROM weekly_calendars WHERE week_date = '" . $date_from . "' )
            GROUP BY
            	fiscal_year");

        $monthly = db::connection('ympimis_2')->select("SELECT
            IF
            	(
            		remark = 'CASE',
            		'CASE',
            	IF
            		(
            			department LIKE '%(EI)%',
            			'EDUCATIONAL INSTRUMENT',
            		IF
            			(
            				department LIKE '%(WI-A)%',
            				'ASSEMBLY',
            			IF
            				(
            					department LIKE '%(WI-PP)%',
            					'PARTS PROCESS',
                            IF
            				(
            					department LIKE '%(WI-BPP)%',
            					'BODY PARTS PROCESS',
							IF
            				(
            					department LIKE '%(WI-KPP)%',
            					'KEY PARTS PROCESS',
            				IF
            					(
            						department LIKE '%(WI-ST)%',
            						'SURFACE TREATMENT',
            					IF
            					( department LIKE '%(WI-WP)%', 'WELDING PROCESS', 'UNDEFINED' )))))))) AS remark,
            	date_format( result_date, '%Y-%m' ) AS mon,
            	sum( total_input ) AS total_input,
            	sum( total_output ) AS total_output
            FROM
            	efficiency_resumes
            WHERE
            	result_date >= '" . $weekly_calendar[0]->fiscal_from . "'
            	AND result_date <= '" . $weekly_calendar[0]->fiscal_to . "'
            GROUP BY
            IF
            	(
            		remark = 'CASE',
            		'CASE',
            	IF
            		(
            			department LIKE '%(EI)%',
            			'EDUCATIONAL INSTRUMENT',
            		IF
            			(
            				department LIKE '%(WI-A)%',
            				'ASSEMBLY',
            			IF
            				(
            					department LIKE '%(WI-PP)%',
            					'PARTS PROCESS',
                            IF
            				(
            					department LIKE '%(WI-BPP)%',
            					'BODY PARTS PROCESS',
							IF
            				(
            					department LIKE '%(WI-KPP)%',
            					'KEY PARTS PROCESS',
            				IF
            					(
            						department LIKE '%(WI-ST)%',
            						'SURFACE TREATMENT',
            					IF
            					( department LIKE '%(WI-WP)%', 'WELDING PROCESS', 'UNDEFINED' )))))))),
            	DATE_FORMAT(result_date, '%Y-%m')
            ORDER BY
            	remark ASC,
            	mon ASC");

        $daily = db::connection('ympimis_2')->select("SELECT
            IF
            	(
            		remark = 'CASE',
            		'CASE',
            	IF
            		(
            			department LIKE '%(EI)%',
            			'EDUCATIONAL INSTRUMENT',
            		IF
            			(
            				department LIKE '%(WI-A)%',
            				'ASSEMBLY',
            			IF
            				(
            					department LIKE '%(WI-PP)%',
            					'PARTS PROCESS',
                            IF
            				(
            					department LIKE '%(WI-BPP)%',
            					'BODY PARTS PROCESS',
							IF
            				(
            					department LIKE '%(WI-KPP)%',
            					'KEY PARTS PROCESS',
            				IF
            					(
            						department LIKE '%(WI-ST)%',
            						'SURFACE TREATMENT',
            					IF
            					( department LIKE '%(WI-WP)%', 'WELDING PROCESS', 'UNDEFINED' )))))))) AS remark,
            	result_date,
            	sum( total_input ) AS total_input,
            	sum( total_output ) AS total_output
            FROM
            	efficiency_resumes
            WHERE
            	result_date >= '" . $date_from . "'
            	AND result_date <= '" . $date_to . "'
            GROUP BY
            IF
            	(
            		remark = 'CASE',
            		'CASE',
            	IF
            		(
            			department LIKE '%(EI)%',
            			'EDUCATIONAL INSTRUMENT',
            		IF
            			(
            				department LIKE '%(WI-A)%',
            				'ASSEMBLY',
            			IF
            				(
            					department LIKE '%(WI-PP)%',
            					'PARTS PROCESS',
                            IF
            				(
            					department LIKE '%(WI-BPP)%',
            					'BODY PARTS PROCESS',
							IF
            				(
            					department LIKE '%(WI-KPP)%',
            					'KEY PARTS PROCESS',
            				IF
            					(
            						department LIKE '%(WI-ST)%',
            						'SURFACE TREATMENT',
            					IF
            					( department LIKE '%(WI-WP)%', 'WELDING PROCESS', 'UNDEFINED' )))))))),
            	result_date
            ORDER BY
            	remark ASC,
            	result_date ASC");

        $resume_yearly = array();
        $resume_yearly_2 = array();
        $resume_monthly = array();
        $resume_daily = array();

        $remarks = array();
        array_push($remarks, 'YMPI');

        foreach ($monthly as $row) {

            if (!in_array($row->remark, $remarks)) {
                array_push($remarks, $row->remark);
            }

            $key = $row->mon;
            if (!array_key_exists($key, $resume_yearly)) {
                $line = array();
                $line['categories'] = $row->mon;
                $line['total_input'] = $row->total_input;
                $line['total_output'] = $row->total_output;
                $percentage = 0;
                if ($row->total_input != 0) {
                    $percentage = round(($row->total_output / $row->total_input) * 100, 1);
                }
                $line['percentage'] = $percentage;

                $resume_yearly[$key] = (object) $line;
            } else {
                $resume_yearly[$key]->total_input = $resume_yearly[$key]->total_input + $row->total_input;
                $resume_yearly[$key]->total_output = $resume_yearly[$key]->total_output + $row->total_output;
                $percentage = 0;
                if ($resume_yearly[$key]->total_input != 0) {
                    $percentage = round(($resume_yearly[$key]->total_output / $resume_yearly[$key]->total_input) * 100, 1);
                }
                $resume_yearly[$key]->percentage = $percentage;
            }
        }

        foreach ($resume_yearly as $row) {
            array_push($resume_monthly, [
                'remark' => 'YMPI',
                'categories' => $row->categories,
                'total_input' => round($row->total_input, 2),
                'total_output' => round($row->total_output, 2),
                'percentage' => $row->percentage,
            ]);
        }

        foreach ($monthly as $row) {
            $percentage = 0;
            if ($row->total_input != 0) {
                $percentage = round(($row->total_output / $row->total_input) * 100, 1);
            }

            array_push($resume_monthly, [
                'remark' => $row->remark,
                'categories' => $row->mon,
                'total_input' => round($row->total_input, 2),
                'total_output' => round($row->total_output, 2),
                'percentage' => $percentage,
            ]);
        }

        foreach ($daily as $row) {
            $key = $row->result_date;
            if (!array_key_exists($key, $resume_yearly_2)) {
                $line = array();
                $line['categories'] = $row->result_date;
                $line['total_input'] = $row->total_input;
                $line['total_output'] = $row->total_output;
                $percentage = 0;
                if ($row->total_input != 0) {
                    $percentage = round(($row->total_output / $row->total_input) * 100, 1);
                }
                $line['percentage'] = $percentage;

                $resume_yearly_2[$key] = (object) $line;
            } else {
                $percentage = 0;
                if ($resume_yearly_2[$key]->total_input != 0) {
                    $percentage = round(($resume_yearly_2[$key]->total_output / $resume_yearly_2[$key]->total_input) * 100, 1);
                }
                $resume_yearly_2[$key]->total_input = $resume_yearly_2[$key]->total_input + $row->total_input;
                $resume_yearly_2[$key]->total_output = $resume_yearly_2[$key]->total_output + $row->total_output;
                $resume_yearly_2[$key]->percentage = $percentage;
            }

        }

        $sum_input = 0;
        $sum_output = 0;

        foreach ($resume_yearly_2 as $row) {
            $sum_input += $row->total_input;
            $sum_output += $row->total_output;
            $percentage = 0;
            if ($sum_input != 0) {
                $percentage = round(($sum_output / $sum_input) * 100, 1);
            }

            array_push($resume_daily, [
                'remark' => 'YMPI',
                'categories' => date('d', strtotime($row->categories)),
                'total_input' => round($sum_input, 2),
                'total_output' => round($sum_output, 2),
                'percentage' => $percentage,
            ]);
        }

        foreach ($remarks as $remark) {
            $sum_input = 0;
            $sum_output = 0;
            foreach ($daily as $row) {
                if ($row->remark == $remark) {
                    $sum_input += $row->total_input;
                    $sum_output += $row->total_output;
                    $percentage = 0;
                    if ($sum_input != 0) {
                        $percentage = round(($sum_output / $sum_input) * 100, 1);
                    }

                    array_push($resume_daily, [
                        'remark' => $row->remark,
                        'categories' => date('d', strtotime($row->result_date)),
                        'total_input' => round($sum_input, 2),
                        'total_output' => round($sum_output, 2),
                        'percentage' => $percentage,
                    ]);
                }
            }
        }

        $response = array(
            'status' => true,
            'fiscal_year' => $weekly_calendar[0]->fiscal_year,
            'period_title' => $period_title,
            'resume_monthly' => $resume_monthly,
            'resume_daily' => $resume_daily,
            'remarks' => $remarks,
        );
        return Response::json($response);

    }

    public function indexEfficiencyMonitoringDetail($id)
    {
        $title = "Efficiency Monitoring Detail";
        $title_jp = "";

        if ($id == 'EDUCATIONAL INSTRUMENT') {
            $department = "Educational Instrument (EI) Department";
        }
        if ($id == 'KEY PARTS PROCESS') {
            $department = "Woodwind Instrument - Key Parts Process (WI-KPP) Department";
        }
        if ($id == 'BODY PARTS PROCESS') {
            $department = "Woodwind Instrument - Body Parts Process (WI-BPP) Department";
        }
        if ($id == 'WELDING PROCESS') {
            $department = "Woodwind Instrument - Welding Process (WI-WP) Department";
        }
        if ($id == 'SURFACE TREATMENT') {
            $department = "Woodwind Instrument - Surface Treatment (WI-ST) Department";
        }
        if ($id == 'ASSEMBLY' || $id == 'CASE') {
            $department = "Woodwind Instrument - Assembly (WI-A) Department";
        }
        if ($id == 'PARTS PROCESS') {
            $department = "Woodwind Instrument - Parts Process (WI-PP) Department";
        }
        if ($id == 'YMPI') {
            exit;
        }

        $weeks = db::select("SELECT DISTINCT
               fiscal_year,
               DATE_FORMAT( week_date, '%M' ) AS bulan,
               DATE_FORMAT( week_date, '%Y-%m' ) AS indek
               FROM
               weekly_calendars
               WHERE
               week_date >= '2022-04-01'
               AND week_date <= '" . date('Y-m-d') . "'
               ORDER BY
               week_date DESC");

        return view(
            'efficiency.monitoring_detail',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'department' => $department,
                'weeks' => $weeks,
            )
        );
    }

    public function fetchEfficiencyMonitoringDetail(Request $request)
    {
        $date_from = date('Y-m-01');
        $date_to = date('Y-m-t');
        $department = $request->get('department');

        if (strlen($request->get('period')) > 0) {
            $date_from = date('Y-m-01', strtotime($request->get('period')));
            $date_to = date('Y-m-t', strtotime($request->get('period')));
        }

        $period_title = date('F Y', strtotime($date_from));

        $weekly_calendar = db::select("SELECT
            	fiscal_year,
            	min( week_date ) AS fiscal_from,
            	max( week_date ) AS fiscal_to
            FROM
            	weekly_calendars
            WHERE
            	fiscal_year = ( SELECT fiscal_year FROM weekly_calendars WHERE week_date = '" . $date_from . "' )
            GROUP BY
            	fiscal_year");

        $monthly = db::connection('ympimis_2')->select("
            SELECT
            	remark,
            	date_format( result_date, '%Y-%m' ) AS mon,
            	sum( total_input ) AS total_input,
            	sum( total_output ) AS total_output
            FROM
            	efficiency_resumes
            WHERE
            	result_date >= '" . $weekly_calendar[0]->fiscal_from . "'
            	AND result_date <= '" . $weekly_calendar[0]->fiscal_to . "'
                AND department = '" . $department . "'
            GROUP BY
            	remark,
            	DATE_FORMAT( result_date, '%Y-%m' )
            ORDER BY
            	mon ASC,
            	remark ASC");

        $daily = db::connection('ympimis_2')->select("
            SELECT
                remark,
            	result_date,
            	sum( total_input ) AS total_input,
            	sum( total_output ) AS total_output
            FROM
            	efficiency_resumes
            WHERE
            	result_date >= '" . $date_from . "'
            	AND result_date <= '" . $date_to . "'
                AND department = '" . $department . "'
            GROUP BY
                remark,
            	result_date
            ORDER BY
            	result_date ASC,
            	remark ASC");

        $resume_yearly = array();
        $resume_yearly_2 = array();
        $resume_monthly = array();
        $resume_daily = array();

        $remarks = array();
        array_push($remarks, $department);

        foreach ($monthly as $row) {

            if (!in_array($row->remark, $remarks)) {
                array_push($remarks, $row->remark);
            }

            $key = $row->mon;
            if (!array_key_exists($key, $resume_yearly)) {
                $line = array();
                $line['categories'] = $row->mon;
                $line['total_input'] = $row->total_input;
                $line['total_output'] = $row->total_output;
                $percentage = 0;
                if ($row->total_input != 0) {
                    $percentage = round(($row->total_output / $row->total_input) * 100, 1);
                }
                $line['percentage'] = $percentage;

                $resume_yearly[$key] = (object) $line;
            } else {
                $resume_yearly[$key]->total_input = $resume_yearly[$key]->total_input + $row->total_input;
                $resume_yearly[$key]->total_output = $resume_yearly[$key]->total_output + $row->total_output;
                $percentage = 0;
                if ($resume_yearly[$key]->total_input != 0) {
                    $percentage = round(($resume_yearly[$key]->total_output / $resume_yearly[$key]->total_input) * 100, 1);
                }
                $resume_yearly[$key]->percentage = $percentage;
            }
        }

        foreach ($resume_yearly as $row) {
            array_push($resume_monthly, [
                'remark' => $department,
                'categories' => $row->categories,
                'total_input' => round($row->total_input, 2),
                'total_output' => round($row->total_output, 2),
                'percentage' => $row->percentage,
            ]);
        }

        foreach ($monthly as $row) {
            $percentage = 0;
            if ($row->total_input != 0) {
                $percentage = round(($row->total_output / $row->total_input) * 100, 1);
            }

            array_push($resume_monthly, [
                'remark' => $row->remark,
                'categories' => $row->mon,
                'total_input' => round($row->total_input, 2),
                'total_output' => round($row->total_output, 2),
                'percentage' => $percentage,
            ]);
        }

        foreach ($daily as $row) {
            $key = $row->result_date;
            if (!array_key_exists($key, $resume_yearly_2)) {
                $line = array();
                $line['categories'] = $row->result_date;
                $line['total_input'] = $row->total_input;
                $line['total_output'] = $row->total_output;
                $percentage = 0;
                if ($row->total_input != 0) {
                    $percentage = round(($row->total_output / $row->total_input) * 100, 1);
                }
                $line['percentage'] = $percentage;

                $resume_yearly_2[$key] = (object) $line;
            } else {
                $percentage = 0;
                if ($resume_yearly_2[$key]->total_input != 0) {
                    $percentage = round(($resume_yearly_2[$key]->total_output / $resume_yearly_2[$key]->total_input) * 100, 1);
                }
                $resume_yearly_2[$key]->total_input = $resume_yearly_2[$key]->total_input + $row->total_input;
                $resume_yearly_2[$key]->total_output = $resume_yearly_2[$key]->total_output + $row->total_output;
                $resume_yearly_2[$key]->percentage = $percentage;
            }

        }

        $sum_input = 0;
        $sum_output = 0;

        foreach ($resume_yearly_2 as $row) {
            $sum_input += $row->total_input;
            $sum_output += $row->total_output;
            $percentage = 0;
            if ($sum_input != 0) {
                $percentage = round(($sum_output / $sum_input) * 100, 1);
            }

            array_push($resume_daily, [
                'remark' => $department,
                'categories' => date('d', strtotime($row->categories)),
                'total_input' => round($sum_input, 2),
                'total_output' => round($sum_output, 2),
                'percentage' => $percentage,
            ]);
        }

        foreach ($remarks as $remark) {
            $sum_input = 0;
            $sum_output = 0;
            foreach ($daily as $row) {
                if ($row->remark == $remark) {
                    $sum_input += $row->total_input;
                    $sum_output += $row->total_output;
                    $percentage = 0;
                    if ($sum_input != 0) {
                        $percentage = round(($sum_output / $sum_input) * 100, 1);
                    }

                    array_push($resume_daily, [
                        'remark' => $row->remark,
                        'categories' => date('d', strtotime($row->result_date)),
                        'total_input' => round($sum_input, 2),
                        'total_output' => round($sum_output, 2),
                        'percentage' => $percentage,
                    ]);
                }
            }
        }

        $response = array(
            'status' => true,
            'fiscal_year' => $weekly_calendar[0]->fiscal_year,
            'period_title' => $period_title,
            'resume_monthly' => $resume_monthly,
            'resume_daily' => $resume_daily,
            'remarks' => $remarks,
        );
        return Response::json($response);
    }

    public function indexEfficiencyDashboard()
    {
        $title = "Efficiency Control Dashboard";
        $title_jp = "効率制御";

        return view(
            'efficiency.dashboard',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        );

    }

    public function indexEfficiencyManpower($id)
    {

        $title = "Efficiency Control Manpower List";
        $title_jp = "";

        if ($id == 'EDUCATIONAL INSTRUMENT') {
            $department = "Educational Instrument (EI) Department";
        }
        if ($id == 'KEY PARTS PROCESS') {
            $department = "Woodwind Instrument - Key Parts Process (WI-KPP) Department";
        }
        if ($id == 'BODY PARTS PROCESS') {
            $department = "Woodwind Instrument - Body Parts Process (WI-BPP) Department";
        }
        if ($id == 'WELDING PROCESS') {
            $department = "Woodwind Instrument - Welding Process (WI-WP) Department";
        }
        if ($id == 'SURFACE TREATMENT') {
            $department = "Woodwind Instrument - Surface Treatment (WI-ST) Department";
        }
        if ($id == 'ASSEMBLY') {
            $department = "Woodwind Instrument - Assembly (WI-A) Department";
        }
        if ($id == 'PARTS PROCESS') {
            $department = "Woodwind Instrument - Parts Process (WI-PP) Department";
        }
        if ($id == 'YMPI') {
            exit;
        }

        return view(
            'efficiency.manpower',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'department' => $department,
                'remarks' => $this->remarks,
            )
        );

    }

    public function indexEfficiencyMaterial($id)
    {
        $title = "Efficiency Control Material List";
        $title_jp = "";
        if ($id == 'EDUCATIONAL INSTRUMENT') {
            $department = "Educational Instrument (EI) Department";
        }
        if ($id == 'KEY PARTS PROCESS') {
            $department = "Woodwind Instrument - Key Parts Process (WI-KPP) Department";
        }
        if ($id == 'BODY PARTS PROCESS') {
            $department = "Woodwind Instrument - Body Parts Process (WI-BPP) Department";
        }
        if ($id == 'WELDING PROCESS') {
            $department = "Woodwind Instrument - Welding Process (WI-WP) Department";
        }
        if ($id == 'SURFACE TREATMENT') {
            $department = "Woodwind Instrument - Surface Treatment (WI-ST) Department";
        }
        if ($id == 'ASSEMBLY') {
            $department = "Woodwind Instrument - Assembly (WI-A) Department";
        }
        if ($id == 'PARTS PROCESS') {
            $department = "Woodwind Instrument - Parts Process (WI-PP) Department";
        }
        if ($id == 'YMPI') {
            exit;
        }

        $periods = db::select("SELECT
	            fiscal_year,
	            min( week_date ) AS date_from,
	            max( week_date ) AS date_to
            FROM
	            weekly_calendars
            WHERE
	            week_date > '2022-04-01'
            GROUP BY
	            fiscal_year
            ORDER BY week_date DESC");

        $materials = db::table('material_plant_data_lists')
            ->whereIn('valcl', ['9010', '9030'])
            ->whereNull('spt')
            ->get();

        return view(
            'efficiency.material',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'department' => $department,
                'remarks' => $this->remarks,
                'periods' => $periods,
                'materials' => $materials,
            )
        );
    }

    public function indexEfficiencyInput($id)
    {
        $title = "Efficiency Control Input Hour";
        $title_jp = "";

        if ($id == 'EDUCATIONAL INSTRUMENT') {
            $department = "Educational Instrument (EI) Department";
        }
        if ($id == 'KEY PARTS PROCESS') {
            $department = "Woodwind Instrument - Key Parts Process (WI-KPP) Department";
        }
        if ($id == 'BODY PARTS PROCESS') {
            $department = "Woodwind Instrument - Body Parts Process (WI-BPP) Department";
        }
        if ($id == 'WELDING PROCESS') {
            $department = "Woodwind Instrument - Welding Process (WI-WP) Department";
        }
        if ($id == 'SURFACE TREATMENT') {
            $department = "Woodwind Instrument - Surface Treatment (WI-ST) Department";
        }
        if ($id == 'ASSEMBLY') {
            $department = "Woodwind Instrument - Assembly (WI-A) Department";
        }
        if ($id == 'PARTS PROCESS') {
            $department = "Woodwind Instrument - Parts Process (WI-PP) Department";
        }
        if ($id == 'YMPI') {
            exit;
        }

        $employees = db::table('employee_syncs')->whereNull('end_date')->get();

        return view(
            'efficiency.input',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'department' => $department,
                'remarks' => $this->remarks,
                'employees' => $employees,
            )
        );
    }

    public function indexEfficiencyOutput($id)
    {
        $title = "Efficiency Control Output Hour";
        $title_jp = "";

        if ($id == 'EDUCATIONAL INSTRUMENT') {
            $department = "Educational Instrument (EI) Department";
        }
        if ($id == 'KEY PARTS PROCESS') {
            $department = "Woodwind Instrument - Key Parts Process (WI-KPP) Department";
        }
        if ($id == 'BODY PARTS PROCESS') {
            $department = "Woodwind Instrument - Body Parts Process (WI-BPP) Department";
        }
        if ($id == 'WELDING PROCESS') {
            $department = "Woodwind Instrument - Welding Process (WI-WP) Department";
        }
        if ($id == 'SURFACE TREATMENT') {
            $department = "Woodwind Instrument - Surface Treatment (WI-ST) Department";
        }
        if ($id == 'ASSEMBLY') {
            $department = "Woodwind Instrument - Assembly (WI-A) Department";
        }
        if ($id == 'PARTS PROCESS') {
            $department = "Woodwind Instrument - Parts Process (WI-PP) Department";
        }
        if ($id == 'YMPI') {
            exit;
        }

        $materials = db::table('material_plant_data_lists')
            ->whereIn('valcl', ['9010', '9030'])
            ->whereNull('spt')
            ->get();

        return view(
            'efficiency.output',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'department' => $department,
                'remarks' => $this->remarks,
                'materials' => $materials,
            )
        );
    }

    public function fetchEfficiencyManpower(Request $request)
    {
        $period = date('Y-m-01');
        $department = $request->get('department');

        if (strlen($request->get('period')) > 0) {
            $period = date('Y-m-01', strtotime($request->get('period')));
        }

        $period_title = "(Periode " . date('F Y', strtotime($period)) . ")";

        $manpowers = db::connection('ympimis_2')->table('efficiency_manpowers')
            ->where('period', '=', $period)
            ->orderBy('hire_date', 'ASC')
            ->get();

        $response = array(
            'status' => true,
            'manpowers' => $manpowers,
            'period' => $period,
            'period_title' => $period_title,
        );
        return Response::json($response);
    }

    public function fetchEfficiencyMaterial(Request $request)
    {
        $period = $request->get('period');
        $department = $request->get('department');

        if (strlen($period) <= 0) {
            $weekly_calendar = db::table('weekly_calendars')->where('week_date', '=', date('Y-m-d'))->first();
            $period = $weekly_calendar->fiscal_year;
        }

        $period_title = "(Periode " . $period . ")";

        $materials = db::connection('ympimis_2')->table('efficiency_materials')
            ->where('period', '=', $period)
            ->where('department', '=', $department)
            ->orderBy('material_number', 'ASC')
            ->get();

        $response = array(
            'status' => true,
            'materials' => $materials,
            'period' => $period,
            'period_title' => $period_title,
        );
        return Response::json($response);

    }

    public function uploadEfficiencyMaterial(Request $request)
    {
        try {

            $rows = preg_split('/\r?\n/', $request->get('upload_material'));
            $department = $request->get('department');
            $period = $request->get('period');
            $upload_materials = array();

            if (!str_contains($period, 'FY')) {
                $weekly_calendar = db::table('weekly_calendars')->where('week_date', '=', date('Y-m-01', strtotime($period)))->first();
                $period = $weekly_calendar->fiscal_year;
            }

            foreach ($rows as $row) {
                $cols = preg_split('/\t/', $row);

                $remark = $cols[0];
                $material_number = $cols[1];
                $material_description = $cols[2];
                $issue_location = $cols[3];
                $standard_time = $cols[4];

                if (strlen($material_number) != 7) {
                    $response = array(
                        'status' => false,
                        'message' => 'Terdapat data GMC tidak sesuai, mohon cek kembali data yang akan diupload.',
                    );
                    return Response::json($response);
                }

                if (strlen($material_description) > 40) {
                    $response = array(
                        'status' => false,
                        'message' => 'Terdapat data Deskripsi tidak sesuai, mohon cek kembali data yang akan diupload.',
                    );
                    return Response::json($response);
                }

                if (strlen($material_description) > 40) {
                    $response = array(
                        'status' => false,
                        'message' => 'Terdapat data Deskripsi tidak sesuai, mohon cek kembali data yang akan diupload.',
                    );
                    return Response::json($response);
                }

                if (strlen($issue_location) != 4) {
                    $response = array(
                        'status' => false,
                        'message' => 'Terdapat data Storage Location tidak sesuai, mohon cek kembali data yang akan diupload.',
                    );
                    return Response::json($response);
                }

                if (str_contains($standard_time, ',')) {
                    $response = array(
                        'status' => false,
                        'message' => 'Gunakan simbol titik (.) untuk bilangan desimal, mohon cek kembali data yang akan diupload.',
                    );
                    return Response::json($response);
                }

                array_push($upload_materials, [
                    'period' => $period,
                    'remark' => strtoupper($remark),
                    'material_number' => strtoupper($material_number),
                    'material_description' => strtoupper(str_replace("'", "", $material_description)),
                    'department' => $department,
                    'issue_location' => strtoupper($issue_location),
                    'standard_time' => round($standard_time, 3),
                    'updated_by' => strtoupper(Auth::user()->username),
                    'updated_by_name' => Auth::user()->name,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            }

            db::connection('ympimis_2')->table('efficiency_materials')
                ->where('period', '=', $period)
                ->delete();

            foreach ($upload_materials as $row) {
                db::connection('ympimis_2')->table('efficiency_materials')
                    ->insert([
                        'period' => $row['period'],
                        'remark' => $row['remark'],
                        'material_number' => $row['material_number'],
                        'material_description' => $row['material_description'],
                        'department' => $row['department'],
                        'issue_location' => $row['issue_location'],
                        'standard_time' => $row['standard_time'],
                        'updated_by' => $row['updated_by'],
                        'updated_by_name' => $row['updated_by_name'],
                        'created_at' => $row['created_at'],
                        'updated_at' => $row['updated_at'],
                    ]);
            }

            $response = array(
                'status' => true,
                'production_results' => 'Data material berhasil diperbaharui.',
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

    public function fetchEfficiencyOutputDetail(Request $request)
    {

        $material_number = $request->get('material_number');
        $result_date = $request->get('result_date');
        $date_from = date('Y-m-01', strtotime($request->get('period')));
        $date_to = date('Y-m-t', strtotime($request->get('period')));

        // $production_results = db::connection('ymes')->table('vd_mes0020')
        //     ->where('item_code', '=', $material_number)
        //     ->where('inout_date', '>=', $date_from)
        //     ->where('inout_date', '<=', $date_to)
        //     ->whereIn('move_type', ['PR01', 'PR02']);

        // if (strlen($result_date) > 0) {
        //     $production_results = $production_results->where('inout_date', '=', $result_date);
        // }

        // $production_results = $production_results->select(
        //     'move_type AS category',
        //     'item_code AS material_number',
        //     'inout_qty AS quantity',
        //     'inout_date AS result_date'
        // )->get();

        $production_results = db::connection('ympimis_2')->table('production_results')
            ->where('material_number', '=', $material_number)
            ->where('result_date', '>=', $date_from)
            ->where('result_date', '<=', $date_to)
            ->where('category', '<>', 'production_result_error');

        if (strlen($result_date) > 0) {
            $production_results = $production_results->where(db::raw('date(result_date)'), '=', $result_date);
        }

        $production_results = $production_results->whereNull('deleted_at')->get();

        $response = array(
            'status' => true,
            'production_results' => $production_results,
        );
        return Response::json($response);

    }

    public function fetchEfficiencyOutput(Request $request)
    {

        $period = date('Y-m-01');

        if (strlen($request->get('period')) > 0) {
            $period = date('Y-m-01', strtotime($request->get('period')));
        }
        $period_title = "(Periode " . date('F Y', strtotime($period)) . ")";

        $date_from = date('Y-m-01', strtotime($period));
        $date_to = date('Y-m-t', strtotime($period));
        $department = $request->get('department');

        $weekly_calendar = db::table('weekly_calendars')->where('week_date', '=', $period)->first();
        $fiscal_year = $weekly_calendar->fiscal_year;

        // $materials = db::connection('ympimis_2')->table('efficiency_materials')
        //     ->where('department', '=', $department)
        //     ->where('period', '=', $fiscal_year)
        //     ->orderBy('material_number', 'ASC')
        //     ->get();

        $materials = db::connection('ympimis_2')->select("SELECT
                	*
                FROM
                	efficiency_materials
                WHERE
                	department = '" . $department . "'
                	AND period = '" . $fiscal_year . "'
                ORDER BY material_number ASC");

        $material_numbers = array();
        $resumes = array();

        foreach ($materials as $row) {
            if (!in_array($row->material_number, $material_numbers)) {
                array_push($material_numbers, $row->material_number);
            }

            $key = $row->remark;

            if (!array_key_exists($key, $resumes)) {
                $line = array();
                $line['remark'] = $row->remark;
                $line['total_result'] = 0;
                $line['total_hour'] = 0;

                $resumes[$key] = (object) $line;
            }

        }

        $calendars = db::table('weekly_calendars')->where('week_date', '>=', $date_from)
            ->where('week_date', '<=', $date_to)
            ->select(
                'week_date',
                db::raw('date_format(week_date, "%d") as header'),
                'remark'
            )
            ->get();

        // $production_results = db::connection('ymes')->table('vd_mes0020')
        //     ->where('inout_date', '>=', $date_from)
        //     ->where('inout_date', '<=', $date_to)
        //     ->whereIn('item_code', $material_numbers)
        //     ->whereIn('move_type', ['PR01', 'PR02'])
        //     ->select(
        //         'inout_date AS result_date',
        //         'item_code AS material_number',
        //         'issue_loc_code AS issue_location',
        //         db::raw("SUM(
        //         CASE
        //                 move_type
        //                 WHEN 'PR02' THEN
        //                 inout_qty *- 1 ELSE inout_qty
        //             END) AS quantity")
        //     )
        //     ->groupBy(
        //         'inout_date',
        //         'issue_loc_code',
        //         'item_code'
        //     )
        //     ->orderBy('item_code', 'ASC')
        //     ->orderBy('inout_date', 'ASC')
        //     ->get();

        $production_results = db::connection('ympimis_2')->table('production_results')
            ->where('result_date', '>=', $date_from . ' 00:00:00')
            ->where('result_date', '<=', $date_to . ' 23:59:59')
            ->whereIn('material_number', $material_numbers)
            ->where('category', '<>', 'production_result_error')
            ->whereNull('deleted_at')
            ->select(
                db::raw('DATE_FORMAT(result_date, "%Y-%m-%d") as result_date'),
                'material_number',
                'issue_location',
                db::raw('sum(quantity) as quantity')
            )
            ->groupBy(
                db::raw('DATE_FORMAT(result_date, "%Y-%m-%d")'),
                'material_number',
                'issue_location'
            )
            ->orderBy('material_number', 'ASC')
            ->orderBy('result_date', 'ASC')
            ->get();

        $outputs = array();

        foreach ($production_results as $row) {
            $remark = "";
            $department = "";
            $material_number = $row->material_number;
            $material_description = "";
            $issue_location = "";
            $standard_time = "";
            $result_date = $row->result_date;
            $quantity = $row->quantity;

            foreach ($materials as $row2) {
                if ($row2->material_number == $material_number) {
                    $remark = $row2->remark;
                    $department = $row2->department;
                    $material_description = $row2->material_description;
                    $issue_location = $row2->issue_location;
                    $standard_time = $row2->standard_time;
                    break;
                }
            }

            $resumes[$remark]->total_result = $resumes[$remark]->total_result + $quantity;
            $resumes[$remark]->total_hour = $resumes[$remark]->total_hour + round(($standard_time * $quantity) / 60, 1);

            array_push($outputs, [
                'result_date' => $result_date,
                'remark' => $remark,
                'department' => $department,
                'material_number' => $material_number,
                'material_description' => $material_description,
                'issue_location' => $issue_location,
                'standard_time' => (float) $standard_time,
                'quantity' => (float) $quantity,
                'output_hour' => round(($standard_time * $quantity) / 60, 1),
            ]);
        }

        $response = array(
            'status' => true,
            'period' => $period,
            'period_title' => $period_title,
            'materials' => $materials,
            'production_results' => $production_results,
            'outputs' => $outputs,
            'calendars' => $calendars,
            'resumes' => $resumes,
        );
        return Response::json($response);

    }

    public function fetchEfficiencyInput(Request $request)
    {
        $period = date('Y-m-01');

        if (strlen($request->get('period')) > 0) {
            $period = date('Y-m-01', strtotime($request->get('period')));
        }
        $period_title = "(Periode " . date('F Y', strtotime($period)) . ")";

        $date_from = date('Y-m-01', strtotime($period));
        $date_to = date('Y-m-t', strtotime($period));
        $department = $request->get('department');

        $manpowers = db::connection('ympimis_2')->table('efficiency_manpowers')
            ->where('department', '=', $department)
            ->where('period', '=', $period)
            ->orderBy('remark', 'ASC')
            ->orderBy('hire_date', 'ASC')
            ->orderBy('job_status', 'ASC')
            ->get();

        $employee_ids = array();
        $resumes = array();

        foreach ($manpowers as $row) {
            array_push($employee_ids, $row->employee_id);

            $key = $row->remark;

            if (!array_key_exists($key, $resumes)) {
                $line = array();
                $line['remark'] = $row->remark;
                $line['total_mp'] = 1;

                $line['total_mp_direct'] = 0;
                $line['total_mp_indirect'] = 0;
                $line['total_mp_permanent'] = 0;
                $line['total_mp_contract'] = 0;

                if ($row->job_status == 'DIRECT') {
                    $line['total_mp_direct'] = 1;
                }
                if ($row->job_status == 'INDIRECT') {
                    $line['total_mp_indirect'] = 1;
                }
                if ($row->employment_status == 'PERMANENT') {
                    $line['total_mp_permanent'] = 1;
                }
                if (str_contains($row->employment_status, 'CONTRACT')) {
                    $line['total_mp_contract'] = 1;
                }

                $line['total_work_hour'] = 0;
                $line['total_overtime_hour'] = 0;
                $line['total_diversion_hour'] = 0;

                $resumes[$key] = (object) $line;

            } else {
                $resumes[$key]->total_mp = $resumes[$key]->total_mp + 1;

                if ($row->job_status == 'DIRECT') {
                    $resumes[$key]->total_mp_direct = $resumes[$key]->total_mp_direct + 1;
                }
                if ($row->job_status == 'INDIRECT') {
                    $resumes[$key]->total_mp_indirect = $resumes[$key]->total_mp_indirect + 1;
                }
                if ($row->employment_status == 'PERMANENT') {
                    $resumes[$key]->total_mp_permanent = $resumes[$key]->total_mp_permanent + 1;
                }
                if (str_contains($row->employment_status, 'CONTRACT')) {
                    $resumes[$key]->total_mp_contract = $resumes[$key]->total_mp_contract + 1;
                }

            }
        }

        $calendars = db::table('weekly_calendars')->where('week_date', '>=', $date_from)
            ->where('week_date', '<=', $date_to)
            ->select(
                'week_date',
                db::raw('date_format(week_date, "%d") as header'),
                'remark'
            )
            ->get();

        $sunfish_shift_syncs = db::table('sunfish_shift_syncs')->where('shift_date', '>=', $date_from)
            ->where('shift_date', '<=', $date_to)
            ->whereIn('employee_id', $employee_ids)
            ->select(
                'employee_id',
                'shiftdaily_code',
                'attend_code',
                'shift_date'
            )
            ->distinct()
            ->get();

        $attendances = array();

        foreach ($sunfish_shift_syncs as $row) {
            $employee_id = $row->employee_id;
            $shift_date = $row->shift_date;
            $shiftdaily_code = $row->shiftdaily_code;
            $attend_code = $row->attend_code;
            $remark = "";
            $work_hour = "";

            if (str_contains($row->attend_code, 'SAKIT')) {
                $work_hour = "SD";
            } else if (str_contains($row->attend_code, 'Izin') || str_contains($row->attend_code, 'IPU')) {
                $work_hour = "I";
            } else if (str_contains($row->attend_code, 'CK')) {
                $work_hour = "CK";
            } else if (str_contains($row->attend_code, 'CK') || str_contains($row->attend_code, 'CUTI') || str_contains($row->attend_code, 'UPL')) {
                $work_hour = "CT";
            } else if (str_contains($row->attend_code, 'Mangkir') || str_contains($row->attend_code, 'ABS')) {
                $work_hour = "A";
            } else if (str_contains($row->attend_code, 'OFF')) {
                $work_hour = 0;
            } else {
                if (str_contains($row->shiftdaily_code, 'OFF')) {
                    $work_hour = 0;
                } else if (str_contains($row->shiftdaily_code, '_Adj')) {
                    $work_hour = 5;
                } else if (str_contains($row->shiftdaily_code, '4G')) {
                    $work_hour = 7;
                } else if (str_contains($row->shiftdaily_code, 'Shift_1')) {
                    $work_hour = 8;
                } else if (str_contains($row->shiftdaily_code, 'Shift_2')) {
                    $work_hour = 7.5;
                } else if (str_contains($row->shiftdaily_code, 'Shift_3')) {
                    $work_hour = 7;
                } else {
                    $work_hour = "X";
                }
            }

            foreach ($manpowers as $row2) {
                if ($employee_id == $row2->employee_id) {
                    $remark = $row2->remark;
                    break;
                }
            }

            if (is_numeric($work_hour)) {
                $resumes[$remark]->total_work_hour = $resumes[$remark]->total_work_hour + $work_hour;
            }

            array_push($attendances, [
                'employee_id' => $employee_id,
                'remark' => $remark,
                'shift_date' => $shift_date,
                'shiftdaily_code' => $shiftdaily_code,
                'attend_code' => $attend_code,
                'work_hour' => $work_hour,
            ]);

        }

        $VIEW_YMPI_Emp_OvertimePlan = db::connection('sunfish')->table('VIEW_YMPI_Emp_OvertimePlan')
            ->where('ShiftStart', '>', $date_from . " 00:00:00")
            ->where('ShiftStart', '<=', $date_to . " 23:59:59")
            ->whereIn('emp_no', $employee_ids)
            ->select(
                'emp_no',
                db::raw("FORMAT(ShiftStart, 'yyyy-MM-dd') as ShiftStart"),
                db::raw("SUM(total_ot) as total_ot")
            )
            ->groupBy(
                'emp_no',
                db::raw("FORMAT(ShiftStart, 'yyyy-MM-dd')")
            )
            ->get();

        $overtimes = array();

        foreach ($VIEW_YMPI_Emp_OvertimePlan as $row) {
            $employee_id = $row->emp_no;
            $shift_date = date('Y-m-d', strtotime($row->ShiftStart));
            $work_hour = round(($row->total_ot / 60) * 2, 0) / 2;
            $remark = "";

            foreach ($manpowers as $row2) {
                if ($employee_id == $row2->employee_id) {
                    $remark = $row2->remark;
                    break;
                }
            }

            if (is_numeric($work_hour)) {
                $resumes[$remark]->total_overtime_hour = $resumes[$remark]->total_overtime_hour + $work_hour;
            }

            array_push($overtimes, [
                'employee_id' => $employee_id,
                'remark' => $remark,
                'shift_date' => $shift_date,
                'work_hour' => $work_hour,
            ]);
        }

        $diversions = db::connection('ympimis_2')->table('efficiency_work_hours')
            ->whereIn('employee_id', $employee_ids)
            ->where('shift_date', '>=', $date_from)
            ->where('shift_date', '<=', $date_to)
            ->select(
                'employee_id',
                'shift_date',
                db::raw('SUM(work_hour) as work_hour')
            )
            ->groupBy(
                'employee_id',
                'shift_date'
            )
            ->get();

        foreach ($diversions as $row) {

            $employee_id = $row->employee_id;
            $work_hour = $row->work_hour;

            foreach ($manpowers as $row2) {
                if ($employee_id == $row2->employee_id) {
                    $remark = $row2->remark;
                    break;
                }
            }

            if (is_numeric($work_hour)) {
                $resumes[$remark]->total_diversion_hour = $resumes[$remark]->total_diversion_hour + $work_hour;
            }

        }

        $diversion_details = db::connection('ympimis_2')->select("
        SELECT
        	ewh.id,
        	em.remark,
        	em.employee_id,
        	em.employee_name,
        	em.job_status,
        	em.position,
        	ewh.category,
        	ewh.remark AS diversion,
        	ewh.shift_date,
        	ewh.work_hour,
        	ewh.updated_by,
        	ewh.updated_by_name
        FROM
        	efficiency_manpowers AS em
        	LEFT JOIN efficiency_work_hours AS ewh ON em.employee_id = ewh.employee_id
        WHERE
        	em.period = '" . $period . "'
        	AND em.department = '" . $department . "'
        	AND ewh.shift_date >= '" . $date_from . "'
        	AND ewh.shift_date <= '" . $date_to . "'
        ORDER BY
        	em.hire_date ASC,
        	ewh.shift_date ASC");

        $response = array(
            'status' => true,
            'period' => $period,
            'period_title' => $period_title,
            'manpowers' => $manpowers,
            'calendars' => $calendars,
            'attendances' => $attendances,
            'overtimes' => $overtimes,
            'diversions' => $diversions,
            'diversion_details' => $diversion_details,
            'resumes' => $resumes,
        );
        return Response::json($response);

    }

    public function fetchEfficiencyDiversion(Request $request)
    {
        $category = $request->get('category');
        $shift_date = $request->get('shift_date');
        $employee_id = $request->get('employee_id');

        if ($category == 'diversion') {
            $datas = db::connection('ympimis_2')->table('efficiency_work_hours')
                ->where('shift_date', '=', $shift_date)
                ->where('employee_id', '=', $employee_id)
                ->get();
        }

        $response = array(
            'status' => true,
            'datas' => $datas,
        );
        return Response::json($response);

    }

    public function deleteEfficiencyDiversion(Request $request)
    {
        $id = $request->get('id');

        try {
            db::connection('ympimis_2')->table('efficiency_work_hours')
                ->where('id', '=', $id)
                ->delete();

            $response = array(
                'status' => true,
                'message' => 'Data pengalihan berhasil dihapus',
                'id' => $id,
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

    public function uploadEfficiencyDiversion(Request $request)
    {

        try {

            $shift_date = explode(' - ', $request->get('shift_dates'));
            $category = $request->get('category');
            $remark = $request->get('remark');
            $work_hour = $request->get('work_hour');
            $employees = $request->get('employees');
            $department = $request->get('department');

            $weekly_calendars = db::table('weekly_calendars')->where('week_date', '>=', $shift_date[0])
                ->where('week_date', '<=', $shift_date[1])
                ->get();

            foreach ($weekly_calendars as $weekly_calendar) {
                foreach ($employees as $employee) {
                    db::connection('ympimis_2')->table('efficiency_work_hours')
                        ->insert([
                            'employee_id' => $employee['employee_id'],
                            'department' => $department,
                            'remark_2' => $employee['remark'],
                            'shift_date' => $weekly_calendar->week_date,
                            'work_hour' => $work_hour,
                            'category' => $category,
                            'remark' => $remark,
                            'updated_by' => Auth::user()->username,
                            'updated_by_name' => Auth::user()->name,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }
            }

            $response = array(
                'status' => true,
                'message' => 'Data pengalihan berhasil ditambahkan',
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

    public function inputEfficiencyDiversion(Request $request)
    {
        $shift_date = $request->get('shift_date');
        $employee_id = $request->get('employee_id');
        $category = $request->get('category');
        $remark = $request->get('remark');
        $work_hour = $request->get('work_hour');
        $department = $request->get('department');
        $remark_2 = $request->get('remark_2');

        try {

            db::connection('ympimis_2')->table('efficiency_work_hours')
                ->insert([
                    'employee_id' => $employee_id,
                    'department' => $department,
                    'remark_2' => $remark_2,
                    'shift_date' => $shift_date,
                    'work_hour' => $work_hour,
                    'category' => $category,
                    'remark' => $remark,
                    'updated_by' => Auth::user()->username,
                    'updated_by_name' => Auth::user()->name,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $datas = db::connection('ympimis_2')->table('efficiency_work_hours')
                ->where('shift_date', '=', $shift_date)
                ->where('employee_id', '=', $employee_id)
                ->get();

            $response = array(
                'status' => true,
                'datas' => $datas,
                'message' => 'Data pengalihan berhasil ditambahkan',
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

    public function editEfficiencyDiversion(Request $request)
    {
        try {
            db::connection('ympimis_2')->table('efficiency_work_hours')->where('id', '=', $request->get('id'))
                ->update([
                    'category' => $request->get('category'),
                    'remark' => $request->get('diversion'),
                    'work_hour' => $request->get('work_hour'),
                ]);

            $response = array(
                'status' => true,
                'message' => 'Data pengalihan berhasil diperbaharui.',
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

    public function editEfficiencyManpowerAdd(Request $request)
    {
        try {
            $employee_id = $request->get('employee_id');
            $department = $request->get('department');
            $weekly_calendar = db::table('weekly_calendars')->where('week_date', '=', $request->get('period'))->first();
            $period = $weekly_calendar->fiscal_year;
            $remark = $request->get('remark');

            $employee_sync = db::table('employee_syncs')->where('employee_id', '=', $employee_id)->first();
            $manpower = db::connection('ympimis_2')->table('efficiency_manpowers')
                ->where('department', '=', $department)
                ->where('period', '=', $period)
                ->where('employee_id', '=', $employee_id)
                ->first();

            if ($manpower) {
                $response = array(
                    'status' => false,
                    'message' => 'Manpower tersebut sudah ada.',
                );
                return Response::json($response);
            }

            db::connection('ympimis_2')->table('efficiency_manpowers')->insert([
                'period' => $period,
                'remark' => $remark,
                'employee_id' => $employee_sync->employee_id,
                'employee_name' => $employee_sync->name,
                'position' => $employee_sync->position,
                'hire_date' => $employee_sync->hire_date,
                'department' => $department,
                'job_status' => $employee_sync->job_status,
                'employment_status' => $employee_sync->employment_status,
                'updated_by' => Auth::user()->username,
                'updated_by_name' => Auth::user()->name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $response = array(
                'status' => true,
                'message' => 'Data manpower berhasil ditambahkan.',
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

    public function editEfficiencyMaterialAdd(Request $request)
    {
        try {
            $material_number = $request->get('material_number');
            $department = $request->get('department');
            $period = $request->get('period');
            $remark = $request->get('remark');
            $standard_time = $request->get('standard_time');

            $material_plant_data_list = db::table('material_plant_data_lists')->where('material_number', '=', $material_number)->first();

            db::connection('ympimis_2')->table('efficiency_materials')->insert([
                'period' => $period,
                'remark' => $remark,
                'material_number' => $material_number,
                'material_description' => $material_plant_data_list->material_description,
                'department' => $department,
                'issue_location' => $material_plant_data_list->storage_location,
                'standard_time' => $standard_time,
                'updated_by' => Auth::user()->username,
                'updated_by_name' => Auth::user()->name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $response = array(
                'status' => true,
                'message' => 'Data manpower berhasil ditambahkan.',
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

    public function editEfficiencyManpowerEdit(Request $request)
    {
        try {
            $id = $request->get('id');
            $employee_id = $request->get('employee_id');
            $job_status = $request->get('job_status');
            $remark = $request->get('remark');
            $department = $request->get('department');
            $period = $request->get('period');

            db::connection('ympimis_2')->table('efficiency_manpowers')
                ->where('id', '=', $id)
                ->update([
                    'job_status' => $job_status,
                    'remark' => $remark,
                    'updated_by' => Auth::user()->username,
                    'updated_by_name' => Auth::user()->name,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $response = array(
                'status' => true,
                'message' => 'Data manpower berhasil diubah.',
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

    public function editEfficiencyMaterialEdit(Request $request)
    {
        try {
            $id = $request->get('id');
            $material_number = $request->get('material_number');
            $standard_time = $request->get('standard_time');
            $remark = $request->get('remark');
            $department = $request->get('department');
            $weekly_calendar = db::table('weekly_calendars')->where('week_date', '=', $request->get('period'))->first();
            $period = $weekly_calendar->fiscal_year;

            db::connection('ympimis_2')->table('efficiency_materials')
                ->where('id', '=', $id)
                ->update([
                    'standard_time' => $standard_time,
                    'remark' => $remark,
                    'updated_by' => Auth::user()->username,
                    'updated_by_name' => Auth::user()->name,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $response = array(
                'status' => true,
                'message' => 'Data manpower berhasil diubah.',
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

    public function editEfficiencyManpowerRemove(Request $request)
    {
        try {
            $id = $request->get('id');

            $efficiency_manpower = db::connection('ympimis_2')->table('efficiency_manpowers')
                ->where('id', '=', $id)
                ->delete();

            $response = array(
                'status' => true,
                'message' => 'Data manpower berhasil dihapus.',
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

    public function editEfficiencyMaterialRemove(Request $request)
    {
        try {
            $id = $request->get('id');

            $efficiency_material = db::connection('ympimis_2')->table('efficiency_materials')
                ->where('id', '=', $id)
                ->delete();

            $response = array(
                'status' => true,
                'message' => 'Data material berhasil dihapus.',
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

    public function indexOperatorLossTime()
    {
        $title = "Operator Loss Time Record";
        $title_jp = "";

        $employees = db::select('SELECT
			employee_syncs.employee_id,
			employee_syncs.NAME,
			employee_syncs.department,
			employee_syncs.`section`,
			employee_syncs.`group`
			FROM
			employee_syncs');

        return view(
            'efficiency.operator_loss_time',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'employees' => $employees,
            )
        )->with('head', 'Operator Loss Time Record')->with('page', 'Operator Loss Time');
    }

    public function indexOperatorLossTimeChart()
    {
        $title = "Operator Loss Time Chart";
        $title_jp = "";

        return view(
            'efficiency.operator_loss_time_chart',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('head', 'Operator Loss Time Chart')->with('page', 'Operator Loss Time');
    }

    public function indexEfficiencyLeader()
    {
        $title = "Efficiency Control Leader";
        $title_jp = "";

        return view(
            'efficiency.index_efficiency_leader',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('head', 'Operator Loss Time Record')->with('page', 'Operator Loss Time');
    }

    public function indexReportEfficiencyHourly($location)
    {
        $loc = explode('-', $location);

        $title = 'Efisiensi ' . ucwords($loc[0]) . ' ' . strtoupper($loc[2]) . ' ' . ucwords($loc[1]);
        $title_jp = '???';

        $employees = db::select("SELECT
			es.employee_id,
			es.name
			FROM
			employee_syncs AS es
			LEFT JOIN cost_centers2 AS cc ON cc.cost_center = es.cost_center
			");

        return view(
            'efficiency.efficiency_leader_hourly',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'location' => $location,
                'employees' => $employees,
            )
        )->with('page', 'Middle Process Barrel Machine')->with('head', 'Middle Process');
    }

    public function inputTarget(Request $request)
    {
        $date_first = date('Y-m-01');

        if (strlen($request->get('date')) > 0) {
            $date_first = date('Y-m-01', strtotime($request->get('date')));
        }

        try {
            $target = EfficiencyTarget::where('valid_date', '=', $date_first)
                ->where('location', '=', $request->get('location'))
                ->where('category', '=', $request->get('category'))
                ->where('remark', '=', $request->get('hpl'))
                ->first();

            $target->target = $request->get('target');
            $target->save();

            $response = array(
                'status' => true,
                'message' => 'Target berhasil diperbaharui.',
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

    public function inputManpower(Request $request)
    {
        $date = date('Y-m-d');

        if (strlen($request->get('date')) > 0) {
            $date = date('Y-m-d', strtotime($request->get('date')));
        }

        $date_end = date('Y-m-t', strtotime($date));

        $weekly_calendars = db::select("SELECT
			*
			FROM
			weekly_calendars
			WHERE
			week_date >= '" . $date . "'
			AND week_date <= '" . $date_end . "'");

        $employee = explode('-', $request->get('manpower'));

        try {
            foreach ($weekly_calendars as $weekly_calendar) {
                DB::table('efficiency_manpowers')->insert([
                    'valid_date' => $weekly_calendar->week_date,
                    'employee_id' => $employee[0],
                    'employee_name' => $employee[1],
                    'location' => $request->get('location'),
                    'category' => $request->get('category'),
                    'remark' => $request->get('hpl'),
                    'created_by' => Auth::id(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $response = array(
                'status' => true,
                'message' => 'Manpower berhasil ditambahkan.',
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

    public function deleteManpower(Request $request)
    {
        $date = date('Y-m-d');

        if (strlen($request->get('date')) > 0) {
            $date = date('Y-m-d', strtotime($request->get('date')));
        }

        $date_end = date('Y-m-t', strtotime($date));

        $manpower = EfficiencyManpower::where('id', '=', $request->get('id'))->first();

        $employee_name = $manpower->employee_name;

        try {
            $delete = db::table('efficiency_manpowers')
                ->where('employee_id', '=', $manpower->employee_id)
                ->where('location', '=', $manpower->location)
                ->where('category', '=', $manpower->category)
                ->where('remark', '=', $manpower->remark)
                ->where('valid_date', '>=', $date)
                ->where('valid_date', '<=', $date_end)
                ->delete();

            $response = array(
                'status' => true,
                'message' => 'Manpower ' . $employee_name . ' berhasil dihapus dari ' . $date . ' hingga ' . $date_end,
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

    public function fetchReportEfficiencyHourly(Request $request)
    {
        $date = date('Y-m-d');
        $date_from = date('Y-m-d 05:00:00');
        $date_to = date('Y-m-d 02:00:00', strtotime($date . "+1 days"));

        if (strlen($request->get('date')) > 0) {
            $date = date('Y-m-d', strtotime($request->get('date')));
            $date_from = date('Y-m-d 05:00:00', strtotime($request->get('date')));
            $date_to = date('Y-m-d 03:00:00', strtotime($request->get('date') . "+1 days"));
        }

        $weekly_calendar = WeeklyCalendar::where('week_date', '=', $date)->first();

        $date_first = date('Y-m-01', strtotime($date));

        $target = EfficiencyTarget::where('valid_date', '=', $date_first)
            ->where('location', '=', $request->get('location'))
            ->where('category', '=', $request->get('category'))
            ->where('remark', '=', $request->get('hpl'))
            ->first();

        $monthly = EfficiencyMonthly::where('valid_date', '=', $date_first)
            ->where('location', '=', $request->get('location'))
            ->where('category', '=', $request->get('category'))
            ->where('remark', '=', $request->get('hpl'))
            ->first();

        $monthly_efficiency = 0;

        if ($monthly != "") {
            $monthly_efficiency = ($monthly->output / $monthly->input) * 100;
        }

        $hours = db::select("SELECT
			*
			FROM
			efficiency_hours
			WHERE
			deleted_at IS NULL
			AND valid_date = '" . $date_first . "'");

        $outputs = db::select("SELECT
			jam AS hour_from,
			jam + 1 AS hour_to,
			category,
			sum( lot ) AS quantity,
			IFNULL( sum( output ), 0 ) AS output
			FROM
			(
				SELECT
				date( h.created_at ) AS due,
				HOUR ( h.created_at ) AS jam,
				m.material_number,
				m.location,
				m.category,
				m.remark,
				h.lot,
				st.standard_time,
				st.standard_time * h.lot AS output
				FROM
				ympimis.efficiency_standard_times AS st
				LEFT JOIN kitto.materials AS m ON m.material_number = st.material_number
				LEFT JOIN kitto.histories AS h ON m.id = h.completion_material_id
				WHERE
				st.location = '" . $request->get('location') . "'
				AND st.category = '" . $request->get('category') . "'
				AND st.remark = '" . $request->get('hpl') . "'
				AND st.fiscal_year = '" . $weekly_calendar->fiscal_year . "'
				AND h.category IN ( 'completion', 'completion_adjustment', 'completion_adjustment_excel', 'completion_adjustment_manual', 'completion_cancel', 'completion_error', 'completion_return', 'completion_scrap', 'completion_repair', 'completion_after_repair', 'completion_temporary_delete' )
				AND h.created_at >= '" . $date_from . "'
				AND h.created_at <= '" . $date_to . "'
				) AS output
				GROUP BY
				jam,
				category
				ORDER BY
				jam ASC,
				remark ASC");

        $process_1 = array();
        $acc_quantity = 0;
        $acc_time = 0;
        $overtime_input = 0;

        $h = [];

        foreach ($hours as $hour) {
            if (!in_array($hour->jam, $h)) {
                array_push($h, $hour->jam);
            }
            $shift = $hour->shift;
            $jam = $hour->jam;
            $quantity = 0;
            $time = 0;
            $ot_output = 0;

            foreach ($outputs as $output) {
                if ($output->hour_from == $jam) {
                    $quantity = $output->quantity;
                    $time = $output->output;
                    $acc_quantity += $output->quantity;
                    $acc_time += $output->output;
                }
            }

            array_push(
                $process_1,
                [
                    'shift' => $shift,
                    'jam' => $jam,
                    'quantity' => $quantity,
                    'output' => $time,
                    'acc_quantity' => $acc_quantity,
                    'acc_output' => $acc_time,
                ]
            );
        }

        $manpowers = db::select("SELECT
				id,
				employee_id,
				employee_name
				FROM
				efficiency_manpowers AS ee
				WHERE
				ee.location = '" . $request->get('location') . "'
				AND ee.category = '" . $request->get('category') . "'
				AND ee.remark = '" . $request->get('hpl') . "'
				AND ee.valid_date = '" . $date . "'");

        if (count($manpowers) == 0) {
            $response = array(
                'status' => false,
                'message' => 'Terdapat data yang tidak lengkap.',
            );
            return Response::json($response);
        }

        $where_idx = "";
        $idx = "";
        for ($x = 0; $x < count($manpowers); $x++) {
            $idx = $idx . "'" . $manpowers[$x]->employee_id . "'";
            if ($x != count($manpowers) - 1) {
                $idx = $idx . ",";
            }
        }
        $where_idx = " AND emp_no in (" . $idx . ") ";
        $where_clinic = " AND cpd.employee_id in (" . $idx . ") ";
        $where_leave = " AND hlrd.employee_id in (" . $idx . ") ";

        $attendances = db::connection('sunfish')->select("SELECT
				shift,
				COUNT ( emp_no ) AS mp,
				IIF ( shift = 'Shift_1', 53.33, IIF ( shift = 'Shift_2', 50, 52.5 ) ) AS work_hour,
				COUNT ( emp_no ) * IIF ( shift = 'Shift_1', 53.33, IIF ( shift = 'Shift_2', 50, 52.5 ) ) AS work_per_hour
				FROM
				(
					SELECT
					emp_no,
					IIF ( shiftdaily_code LIKE '%Shift_1%', 'Shift_1', IIF ( shiftdaily_code LIKE '%Shift_2%', 'Shift_2', 'Other' ) ) AS shift,
					total_ot
					FROM
					VIEW_YMPI_ATTENDANCE
					WHERE
					Attend_Code LIKE '%PRS%'
					AND format ( shiftstarttime, 'yyyy-MM-dd' ) = '" . $date . "'
					" . $where_idx . "
					) AS attends
					GROUP BY
					shift");

        $results = array();
        $acc_wph = 0;

        $overtimes = db::connection('sunfish')->select("SELECT
					format ( ovtplanfrom, 'h:mm:ss' ) AS jam,
					SUM ( total_ot ) AS total_ot
					FROM
					VIEW_YMPI_ATTENDANCE
					WHERE
					format ( shiftstarttime, 'yyyy-MM-dd' ) = '2021-10-10'
					AND total_ot IS NOT NULL
					" . $where_idx . "
					GROUP BY
					format ( ovtplanfrom, 'h:mm:ss' )");

        foreach ($process_1 as $process) {

            $shift = $process['shift'];
            $jam = $process['jam'];
            $quantity = $process['quantity'];
            $output = $process['output'];
            $acc_quantity = $process['acc_quantity'];
            $acc_output = $process['acc_output'];

            $mp = 0;
            $wh = 0;
            $wph = 0;
            $ot = 0;

            foreach ($attendances as $attendance) {
                if ($attendance->shift == $shift) {
                    $mp = $attendance->mp;
                    $wh = $attendance->work_hour;
                    $wph = $attendance->work_per_hour;
                    $acc_wph += $attendance->work_per_hour;
                }
            }

            $eff_per_hour = 0;

            if ($wph > 0) {
                $eff_per_hour = $output / $wph;
            }

            $acc_eff_per_hour = 0;

            if ($acc_wph > 0) {
                $acc_eff_per_hour = $acc_output / $acc_wph;
            }

            array_push(
                $results,
                [
                    'shift' => $shift,
                    'jam' => $jam,
                    'quantity' => $quantity,
                    'output' => $output,
                    'acc_quantity' => $acc_quantity,
                    'acc_output' => $acc_output,
                    'manpower' => $mp,
                    'work_hour' => $wh,
                    'work_per_hour' => $wph,
                    'acc_work_per_hour' => $acc_wph,
                    'eff_per_hour' => $eff_per_hour,
                    'acc_eff_per_hour' => $acc_eff_per_hour,
                    'target' => $target->target,
                    'monthly' => $monthly_efficiency,
                ]
            );
        }

        $final = array();
        $awph = 0;
        $aeph = 0;

        foreach ($results as $result) {
            $shift = $result['shift'];
            $jam = $result['jam'];
            $quantity = $result['quantity'];
            $output = $result['output'];
            $acc_quantity = $result['acc_quantity'];
            $acc_output = $result['acc_output'];
            $manpower = $result['manpower'];
            $work_hour = $result['work_hour'];
            $work_per_hour = $result['work_per_hour'];
            $target = $result['target'];
            $monthly = $result['monthly'];

            $ot = 0;
            $eph = 0;
            $wph = 0;

            $awph += $work_per_hour;
            $wph = $work_per_hour;

            foreach ($overtimes as $overtime) {
                $h = explode(':', $overtime->jam);
                if ($h[0] == $jam) {
                    $ot = $overtime->total_ot;
                    $wph = $work_per_hour + $ot;
                    $awph = $overtime->total_ot;
                }
            }

            if ($wph > 0) {
                $eph = $output / $wph;
            }
            if ($awph > 0) {
                $aeph = $acc_output / $awph;
            }

            array_push(
                $final,
                [
                    'shift' => $shift,
                    'jam' => $jam,
                    'quantity' => $quantity,
                    'output' => $output,
                    'acc_quantity' => $acc_quantity,
                    'acc_output' => $acc_output,
                    'manpower' => $manpower,
                    'work_hour' => $work_hour,
                    'work_per_hour' => $work_per_hour,
                    'ot' => $ot,
                    'acc_work_per_hour' => $awph,
                    'eff_per_hour' => $eph,
                    'acc_eff_per_hour' => $aeph,
                    'target' => $target,
                    'monthly' => $monthly,
                ]
            );
        }

        $clinics = db::select("SELECT
					IFNULL( -(sum(pl.durasi_detik)/60), 0) AS duration
					FROM
					clinic_patient_details AS cpd
					LEFT JOIN ympi_klinik.patient_logs AS pl ON cpd.employee_id = pl.employee_id
					AND date( cpd.visited_at ) = pl.tanggal
					WHERE pl.tanggal = '" . $date . "'
					" . $where_clinic . "");

        $leaves = db::select("SELECT
					IFNULL( sum(
						TIMESTAMPDIFF( MINUTE, hlr.time_arrived, hlr.time_departure )), 0) AS duration
					FROM
					hr_leave_requests AS hlr
					LEFT JOIN hr_leave_request_details AS hlrd ON hlrd.request_id = hlr.request_id
					WHERE
					hlr.date = '" . $date . "'
					" . $where_leave . "");

        $non_productions = db::select("SELECT
					id,
					enp.activity,
					IFNULL( -(enp.duration), 0) AS duration
					FROM
					efficiency_non_productions AS enp
					WHERE
					enp.valid_date = '" . $date . "'
					AND enp.category = '" . $request->get('category') . "'
					AND enp.location = '" . $request->get('location') . "'
					AND enp.remark = '" . $request->get('hpl') . "'");

        $response = array(
            'status' => true,
            'efficiencies' => $final,
            'clinics' => $clinics,
            'leaves' => $leaves,
            'manpowers' => $manpowers,
            'non_productions' => $non_productions,
            'date' => date('l, d F Y', strtotime($date)),
        );
        return Response::json($response);
    }

    public function deleteNonProduction(Request $request)
    {
        try {
            $efficiency_non_production = EfficiencyNonProduction::where('id', '=', $request->get('id'))->first();
            $efficiency_non_production->forceDelete();

            $response = array(
                'status' => true,
                'message' => 'Aktifitas berhasil dihapus.',
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

    public function inputNonProduction(Request $request)
    {
        try {
            $efficiency_non_production = new EfficiencyNonProduction([
                'valid_date' => date('Y-m-d', strtotime($request->get('date'))),
                'activity' => $request->get('activity'),
                'note' => $request->get('note'),
                'duration' => $request->get('duration'),
                'location' => $request->get('location'),
                'category' => $request->get('category'),
                'remark' => $request->get('hpl'),
            ]);
            $efficiency_non_production->save();

            $response = array(
                'status' => true,
                'message' => 'Aktifitas berhasil ditambahkan.',
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

    public function scanEmployee(Request $request)
    {

        if (strlen($request->get('tag')) == 10) {
            $employee = Employee::where('tag', '=', $request->get('tag'))->orderBy('hire_date', 'desc')->limit(1)->first();

        } else {
            $employee = Employee::where('employee_id', '=', $request->get('tag'))->orderBy('hire_date', 'desc')->limit(1)->first();

        }

        if (!$employee) {

            $response = array(
                'status' => false,
                'message' => 'Tag karyawan tidak ditemukan.',
            );
            return Response::json($response);

        }

        $employee_sync = EmployeeSync::where('employee_id', '=', $employee->employee_id)->first();

        $operator_loss_time = OperatorLossTime::where('employee_id', '=', $employee->employee_id)->first();

        if ($operator_loss_time != null) {

            try {
                $operator_loss_time_log = new OperatorLossTimeLog([
                    'employee_id' => $operator_loss_time->employee_id,
                    'employee_name' => $operator_loss_time->employee_name,
                    'cost_center' => $operator_loss_time->cost_center,
                    'position' => $operator_loss_time->position,
                    'division' => $operator_loss_time->division,
                    'department' => $operator_loss_time->department,
                    'section' => $operator_loss_time->section,
                    'group' => $operator_loss_time->group,
                    'sub_group' => $operator_loss_time->sub_group,
                    'reason' => $operator_loss_time->reason,
                    'started_at' => $operator_loss_time->created_at,
                ]);
                $operator_loss_time_log->save();

                $operator_loss_time->forceDelete();

                $response = array(
                    'status' => true,
                    'code' => 'kembali',
                    'message' => 'Karyawan kembali bekerja',
                    'employee' => $employee,
                );
                return Response::json($response);
            } catch (\Exception$e) {
                $response = array(
                    'status' => true,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }

        }

        $response = array(
            'status' => true,
            'code' => 'pergi',
            'message' => 'Karyawan berhasil ditemukan',
            'employee' => $employee_sync,
        );
        return Response::json($response);
    }

    public function inputOperatorLossTime(Request $request)
    {
        try {
            $operator_loss_time = new OperatorLosstime([
                'employee_id' => $request->get('employee_id'),
                'employee_name' => $request->get('employee_name'),
                'cost_center' => $request->get('cost_center'),
                'position' => $request->get('position'),
                'division' => $request->get('division'),
                'department' => $request->get('department'),
                'section' => $request->get('section'),
                'group' => $request->get('group'),
                'sub_group' => $request->get('sub_group'),
                'reason' => $request->get('reason'),
            ]);
            $operator_loss_time->save();
        } catch (\Exception$e) {
            $response = array(
                'status' => true,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'code' => 'pergi',
            'message' => 'Karyawan meninggalkan pekerjaan',
        );
        return Response::json($response);
    }

    public function fetchOperatorLossTime()
    {
        $operator_loss_times = OperatorLossTime::orderBy('department', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $response = array(
            'status' => true,
            'operator_loss_times' => $operator_loss_times,
        );
        return Response::json($response);
    }

    public function fetchOperatorLossTimeLog(Request $request)
    {
        $operator_loss_time_logs = OperatorLossTimeLog::orderBy('employee_id', 'asc');

        $record_from = date('Y-m-01');
        $record_to = date('Y-m-t');
        if (strlen($request->get('record_from')) > 0) {
            $record_from = date('Y-m-d', strtotime($request->get('record_from')));
        }
        if (strlen($request->get('record_to')) > 0) {
            $record_to = date('Y-m-d', strtotime($request->get('record_to')));
        }

        if (strlen($record_from) > 0) {
            $operator_loss_time_logs = $operator_loss_time_logs->where(DB::raw('date(started_at)'), '>=', $record_from);
        }
        if (strlen($record_to) > 0) {
            $operator_loss_time_logs = $operator_loss_time_logs->where(DB::raw('date(started_at)'), '<=', $record_to);
        }
        if ($request->get('record_employee_id') != null) {
            $operator_loss_time_logs = $operator_loss_time_logs->whereIn('employee_id', $request->get('record_employee_id'));
        }
        if ($request->get('record_department') != null) {
            $operator_loss_time_logs = $operator_loss_time_logs->whereIn('department', $request->get('record_department'));
        }
        if ($request->get('record_section') != null) {
            $operator_loss_time_logs = $operator_loss_time_logs->whereIn('section', $request->get('record_section'));
        }
        if ($request->get('record_group') != null) {
            $operator_loss_time_logs = $operator_loss_time_logs->whereIn('group', $request->get('record_group'));
        }

        $operator_loss_time_logs = $operator_loss_time_logs
            ->leftJoin('departments', 'departments.department_name', '=', 'operator_loss_time_logs.department')
            ->select('employee_id', 'employee_name', 'operator_loss_time_logs.department', 'departments.department_shortname', 'section', 'group', 'sub_group', 'reason', 'operator_loss_time_logs.started_at', 'operator_loss_time_logs.created_at', db::raw('timestampdiff(second, operator_loss_time_logs.started_at, operator_loss_time_logs.created_at)/60 as duration'), db::raw('date_format(started_at, "%Y-%m-%d")as tanggal'))
            ->get();

        $departments = db::table('departments')->where('remark', '=', 'production')->orderBy('department_name', 'ASC')->get();

        $response = array(
            'status' => true,
            'operator_loss_time_logs' => $operator_loss_time_logs,
            'departments' => $departments,
            'record_from' => date('d F Y', strtotime($record_from)),
            'record_to' => date('d F Y', strtotime($record_to)),
        );
        return Response::json($response);
    }
}
