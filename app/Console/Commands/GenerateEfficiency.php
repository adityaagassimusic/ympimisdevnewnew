<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateEfficiency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:efficiency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            echo date('Y-m-d H:i:s') . '----';
            $date_from = date('Y-m-d', strtotime('-7 Days'));
            $date_to = date('Y-m-d', strtotime('-1 Day'));
            // $date_from = '2023-04-01';
            // $date_to = '2023-04-30';

            $mp_date_from = date('Y-m-01');
            $mp_date_to = date('Y-m-t');
            $mp_period_before = date('Y-m-01');
            $mp_period_new = date('Y-m-01', strtotime('+1 Day'));

            $production_results = db::connection('ympimis_2')->table('production_results')
                ->where('result_date', '>=', $date_from . ' 00:00:00')
                ->where('result_date', '<=', $date_to . ' 23:59:59')
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

            if (date('Y-m-d') == date('Y-m-t')) {

                db::connection('ympimis_2')->table('efficiency_manpowers')
                    ->where('period', '=', $mp_period_new)
                    ->delete();

                $efficiency_manpowers = db::connection('ympimis_2')->table('efficiency_manpowers')
                    ->where('period', '=', $mp_period_before)
                    ->get();

                $employee_ids = array();

                foreach ($efficiency_manpowers as $efficiency_manpower) {
                    array_push($employee_ids, $efficiency_manpower->employee_id);
                    db::connection('ympimis_2')->table('efficiency_manpowers')
                        ->insert([
                            'period' => $mp_period_new,
                            'department' => $efficiency_manpower->department,
                            'remark' => $efficiency_manpower->remark,
                            'employee_id' => $efficiency_manpower->employee_id,
                            'employee_name' => $efficiency_manpower->employee_name,
                            'hire_date' => $efficiency_manpower->hire_date,
                            'position' => $efficiency_manpower->position,
                            'job_status' => $efficiency_manpower->job_status,
                            'employment_status' => $efficiency_manpower->employment_status,
                            'updated_by' => $efficiency_manpower->updated_by,
                            'updated_by_name' => $efficiency_manpower->updated_by_name,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }

                $new_employees = db::table('employee_syncs')
                    ->where('hire_date', '>=', $mp_date_from)
                    ->where('hire_date', '<=', $mp_date_to)
                    ->whereNull('end_date')
                    ->whereNotIn('employee_id', $employee_ids)
                    ->get();

                $end_employees = db::table('employee_syncs')
                    ->where('end_date', '>=', $mp_date_from)
                    ->where('end_date', '<=', $mp_date_to)
                    ->get();

                $end_employee_ids = array();

                foreach ($end_employees as $end_employee) {
                    array_push($end_employee_ids, $end_employee->employee_id);
                }

                foreach ($new_employees as $new_employee) {
                    db::connection('ympimis_2')->table('efficiency_manpowers')
                        ->insert([
                            'period' => $mp_period_new,
                            'department' => $new_employee->department,
                            'remark' => 'NEW',
                            'employee_id' => $new_employee->employee_id,
                            'employee_name' => $new_employee->name,
                            'hire_date' => $new_employee->hire_date,
                            'position' => $new_employee->position,
                            'job_status' => $new_employee->job_status,
                            'employment_status' => $new_employee->employment_status,
                            'updated_by' => 'System',
                            'updated_by_name' => 'System',
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }

                db::connection('ympimis_2')->table('efficiency_manpowers')
                    ->where('period', '=', $mp_period_new)
                    ->whereIn('employee_id', $end_employee_ids)
                    ->delete();
            };

            $calendars = db::table('weekly_calendars')->where('week_date', '>=', $date_from)
                ->where('week_date', '<=', $date_to)
                ->orderBy('week_date', 'ASC')
                ->get();

            $attendances = db::table('sunfish_shift_syncs')
                ->where('shift_date', '>=', $date_from)
                ->where('shift_date', '<=', $date_to)
                ->select(
                    'employee_id',
                    'shiftdaily_code',
                    'attend_code',
                    'shift_date AS result_date'
                )
                ->distinct()
                ->get();

            $overtimes = db::connection('sunfish')->table('VIEW_YMPI_Emp_OvertimePlan')
                ->where('ShiftStart', '>=', $date_from . " 00:00:00")
                ->where('ShiftStart', '<=', $date_to . " 23:59:59")
                ->select(
                    'emp_no AS employee_id',
                    db::raw("FORMAT(ShiftStart, 'yyyy-MM-dd') as result_date"),
                    db::raw("SUM(total_ot) as work_hour")
                )
                ->groupBy(
                    'emp_no',
                    db::raw("FORMAT(ShiftStart, 'yyyy-MM-dd')")
                )
                ->get();

            // $production_results = db::connection('ymes')->table('vd_mes0020')
            //     ->where('inout_date', '>=', $date_from)
            //     ->where('inout_date', '<=', $date_to)
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

            $inputs = array();
            $outputs = array();

            foreach ($calendars as $row) {
                $period = date('Y-m-01', strtotime($row->week_date));
                $result_date = $row->week_date;

                $manpowers = db::connection('ympimis_2')->table('efficiency_manpowers')
                    ->where('period', '=', $period)
                    ->where('job_status', '=', 'DIRECT')
                    ->orderBy('hire_date', 'ASC')
                    ->orderBy('job_status', 'ASC')
                    ->get();

                foreach ($manpowers as $row2) {
                    $employee_id = $row2->employee_id;
                    $employee_name = $row2->employee_name;
                    $department = $row2->department;
                    $remark = $row2->remark;
                    $position = $row2->position;

                    db::connection('ympimis_2')->table('efficiency_work_hours')
                        ->where('shift_date', '=', $result_date)
                        ->where('employee_id', '=', $employee_id)
                        ->where('category', '=', 'Indirect')
                        ->where('remark', '=', 'Sub Leader')
                        ->whereNotNull('department')
                        ->delete();

                    foreach ($attendances as $attendance) {
                        $input = 0;
                        $total_attendance = 0;
                        $total_overtime = 0;
                        $total_diversion = 0;
                        $sl_diversion = 0;

                        if ($attendance->result_date == $result_date && $attendance->employee_id == $employee_id) {
                            if (str_contains($attendance->attend_code, 'SAKIT')) {
                                $input = 0;
                                $sl_diversion = 0;
                            } else if (str_contains($attendance->attend_code, 'Izin') || str_contains($attendance->attend_code, 'IPU')) {
                                $input = 0;
                                $sl_diversion = 0;
                            } else if (str_contains($attendance->attend_code, 'CK') || str_contains($attendance->attend_code, 'CUTI') || str_contains($attendance->attend_code, 'UPL')) {
                                $input = 0;
                                $sl_diversion = 0;
                            } else if (str_contains($attendance->attend_code, 'Mangkir')) {
                                $input = 0;
                                $sl_diversion = 0;
                            } else if (str_contains($attendance->attend_code, 'OFF')) {
                                $input = 0;
                                $sl_diversion = 0;
                            } else {
                                if (str_contains($attendance->shiftdaily_code, 'OFF')) {
                                    $input = 0;
                                    $sl_diversion = 0;
                                } else if (str_contains($attendance->shiftdaily_code, '_Adj')) {
                                    $input = 5;
                                    $sl_diversion = -1;
                                } else if (str_contains($attendance->shiftdaily_code, '4G')) {
                                    $input = 7;
                                    $sl_diversion = -3;
                                } else if (str_contains($attendance->shiftdaily_code, 'Shift_1')) {
                                    $input = 8;
                                    $sl_diversion = -4;
                                } else if (str_contains($attendance->shiftdaily_code, 'Shift_2')) {
                                    $input = 7.5;
                                    $sl_diversion = -3.5;
                                } else if (str_contains($attendance->shiftdaily_code, 'Shift_3')) {
                                    $input = 7;
                                    $sl_diversion = -3;
                                } else {
                                    $input = 0;
                                    $sl_diversion = 0;
                                }
                            }

                            if ($position == 'Sub Leader' && $sl_diversion != 0) {
                                db::connection('ympimis_2')->table('efficiency_work_hours')
                                    ->insert([
                                        'employee_id' => $employee_id,
                                        'department' => $department,
                                        'remark_2' => $remark,
                                        'shift_date' => $result_date,
                                        'work_hour' => $sl_diversion,
                                        'category' => 'Indirect',
                                        'remark' => 'Sub Leader',
                                        'updated_by' => 'System',
                                        'updated_by_name' => 'System',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);

                            }
                            array_push($inputs, [
                                'employee_id' => $employee_id,
                                'employee_name' => $employee_name,
                                'department' => $department,
                                'remark' => $remark,
                                'result_date' => $result_date,
                                'attendance' => $input,
                                'overtime' => $total_overtime,
                                'diversion' => $total_diversion,
                                'input' => $input,
                                'category' => 'attendance',
                            ]);
                            break;
                        }
                    }

                    foreach ($overtimes as $overtime) {
                        $input = 0;
                        $total_attendance = 0;
                        $total_overtime = 0;
                        $total_diversion = 0;

                        if ($overtime->employee_id == $employee_id && $overtime->result_date == $result_date) {
                            $input = round(($overtime->work_hour / 60) * 2, 0) / 2;
                            array_push($inputs, [
                                'employee_id' => $employee_id,
                                'employee_name' => $employee_name,
                                'department' => $department,
                                'remark' => $remark,
                                'result_date' => $result_date,
                                'attendance' => $total_attendance,
                                'overtime' => $input,
                                'diversion' => $total_diversion,
                                'input' => $input,
                                'category' => 'overtime',
                            ]);
                            break;
                        }
                    }
                }

                $weekly_calendar = db::table('weekly_calendars')->where('week_date', '=', $period)->first();
                $fiscal_year = $weekly_calendar->fiscal_year;

                $materials = db::connection('ympimis_2')->select("SELECT
                	*
                FROM
                	efficiency_materials
                WHERE
                	period = '" . $fiscal_year . "'
                	AND material_number IN (
                	SELECT DISTINCT
                		material_number
                	FROM
                		production_results
                	WHERE
                	result_date >= '" . $result_date . "')");

                foreach ($materials as $row3) {
                    $material_number = $row3->material_number;
                    $material_description = $row3->material_description;
                    $department = $row3->department;
                    $remark = $row3->remark;
                    $standard_time = $row3->standard_time;
                    $quantity = 0;

                    foreach ($production_results as $production_result) {
                        if ($production_result->material_number == $material_number && $production_result->result_date == $result_date) {
                            $quantity = $production_result->quantity;
                            $output = ($standard_time * $quantity) / 60;

                            array_push($outputs, [
                                'material_number' => $material_number,
                                'material_description' => $material_description,
                                'department' => $department,
                                'remark' => $remark,
                                'result_date' => $result_date,
                                'standard_time' => $standard_time,
                                'quantity' => $quantity,
                                'output' => $output,
                            ]);
                            break;
                        }
                    }
                }
            }

            $diversions = db::connection('ympimis_2')->table('efficiency_work_hours')
                ->where('shift_date', '>=', $date_from)
                ->where('shift_date', '<=', $date_to)
                ->where('category', '<>', 'Indirect')
                ->whereNotNull('department')
                ->select(
                    'employee_id',
                    'department',
                    'remark_2',
                    'shift_date AS result_date',
                    db::raw('SUM(work_hour) as work_hour')
                )
                ->groupBy(
                    'employee_id',
                    'department',
                    'remark_2',
                    'shift_date'
                )
                ->get();

            foreach ($calendars as $row) {
                $period = date('Y-m-01', strtotime($row->week_date));
                $result_date = $row->week_date;

                $manpowers = db::connection('ympimis_2')->table('efficiency_manpowers')
                    ->where('period', '=', $period)
                    ->where('job_status', '=', 'DIRECT')
                    ->orderBy('hire_date', 'ASC')
                    ->orderBy('job_status', 'ASC')
                    ->get();

                foreach ($manpowers as $row2) {
                    $employee_id = $row2->employee_id;
                    $employee_name = $row2->employee_name;
                    $department = $row2->department;
                    $remark = $row2->remark;
                    $position = $row2->position;

                    foreach ($diversions as $diversion) {
                        $input = 0;
                        $total_attendance = 0;
                        $total_overtime = 0;
                        $total_diversion = 0;

                        if ($diversion->employee_id == $employee_id && $diversion->result_date == $result_date) {
                            $input = $diversion->work_hour;
                            array_push($inputs, [
                                'employee_id' => $employee_id,
                                'employee_name' => $employee_name,
                                'department' => $diversion->department,
                                'remark' => $diversion->remark_2,
                                'result_date' => $result_date,
                                'attendance' => $total_attendance,
                                'overtime' => $total_overtime,
                                'diversion' => $input,
                                'input' => $input,
                                'category' => 'diversion',
                            ]);
                            break;
                        }
                    }
                }
            }

            $resumes = array();

            foreach ($inputs as $input) {
                $key = $input['department'] . '#' . $input['remark'] . '#' . $input['result_date'];

                if (!array_key_exists($key, $resumes)) {
                    $row = array();
                    $row['department'] = $input['department'];
                    $row['remark'] = $input['remark'];
                    $row['result_date'] = $input['result_date'];
                    $row['total_input'] = $input['input'];
                    $row['total_output'] = 0;

                    $resumes[$key] = (object) $row;
                } else {
                    $resumes[$key]->total_input = $resumes[$key]->total_input + $input['input'];
                }
            }

            foreach ($outputs as $output) {
                $key = $output['department'] . '#' . $output['remark'] . '#' . $output['result_date'];

                if (!array_key_exists($key, $resumes)) {
                    $row = array();
                    $row['department'] = $output['department'];
                    $row['remark'] = $output['remark'];
                    $row['result_date'] = $output['result_date'];
                    $row['total_input'] = 0;
                    $row['total_output'] = $output['output'];

                    $resumes[$key] = (object) $row;
                } else {
                    $resumes[$key]->total_output = $resumes[$key]->total_output + $output['output'];
                }
            }

            $resume_delete = db::connection('ympimis_2')->table('efficiency_resumes')
                ->where('result_date', '>=', $date_from)
                ->where('result_date', '<=', $date_to)
                ->delete();

            $input_delete = db::connection('ympimis_2')->table('efficiency_inputs')
                ->where('result_date', '>=', $date_from)
                ->where('result_date', '<=', $date_to)
                ->delete();

            $output_delete = db::connection('ympimis_2')->table('efficiency_outputs')
                ->where('result_date', '>=', $date_from)
                ->where('result_date', '<=', $date_to)
                ->delete();

            foreach ($resumes as $resume) {
                $resume_insert = db::connection('ympimis_2')->table('efficiency_resumes')->insert([
                    'department' => $resume->department,
                    'remark' => $resume->remark,
                    'result_date' => $resume->result_date,
                    'total_input' => $resume->total_input,
                    'total_output' => $resume->total_output,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            foreach ($inputs as $input) {
                db::connection('ympimis_2')->table('efficiency_inputs')->insert([
                    'employee_id' => $input['employee_id'],
                    'employee_name' => $input['employee_name'],
                    'department' => $input['department'],
                    'remark' => $input['remark'],
                    'result_date' => $input['result_date'],
                    'attendance' => $input['attendance'],
                    'overtime' => $input['overtime'],
                    'diversion' => $input['diversion'],
                    'input' => $input['input'],
                    'category' => $input['category'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            foreach ($outputs as $output) {
                db::connection('ympimis_2')->table('efficiency_outputs')->insert([
                    'material_number' => $output['material_number'],
                    'material_description' => $output['material_description'],
                    'department' => $output['department'],
                    'remark' => $output['remark'],
                    'result_date' => $output['result_date'],
                    'standard_time' => $output['standard_time'],
                    'quantity' => $output['quantity'],
                    'output' => $output['output'],
                    'category' => '',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            echo date('Y-m-d H:i:s') . '----';
            exit;
        } catch (\Exception$e) {
            echo date('Y-m-d H:i:s') . '----' . $e->getMessage();
            exit;
        }
    }
}
