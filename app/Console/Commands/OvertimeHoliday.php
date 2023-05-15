<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\EmployeeSync;
use App\OvertimeExtrafoodAttendance;
use App\WeeklyCalendar;

class OvertimeHoliday extends Command
{

    protected $signature = 'reminder:overtimeholidays';
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

    //update from sunfish sync 4G day
        $day = date('Y-m-d', strtotime(date('Y-m-d')));


        $calendars = WeeklyCalendar::where('week_date', '=', $day)
        ->select('week_date', 'remark')
        ->first();

        if ($calendars != null) {

            if ($calendars->remark != "H") {

               $today_name = date('l');
               if ($today_name == 'Friday') {


                 $date_to1 = date('d-m-Y', strtotime(date('Y-m-d')));
                 $day_today = date('Y-m-d', strtotime(date('Y-m-d')));

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

                    WHEN SHIFT_OVTPLAN LIKE '%Shift_1%' 
                    AND DATEDIFF( MINUTE, ovtplanfrom, ovtplanto ) >= 150 THEN
                    'YA' ELSE '-' 
                    END AS food,
                    CASE

                    WHEN SHIFT_OVTPLAN LIKE '%Shift_2%' 
                    AND DATEDIFF( MINUTE, ovtplanfrom, ovtplanto ) >= 120 THEN
                    'YA' ELSE '-' 
                    END AS extra2,
                    CASE

                    WHEN SHIFT_OVTPLAN LIKE '%Shift_3%' THEN
                    'YA' ELSE '-' 
                    END AS extra3 
                    FROM
                    VIEW_YMPI_Emp_OvertimePlan
                    LEFT JOIN VIEW_YMPI_Emp_OrgUnit ON VIEW_YMPI_Emp_OrgUnit.Emp_no = VIEW_YMPI_Emp_OvertimePlan.emp_no 
                    WHERE
                    CONVERT ( VARCHAR, ovtplanfrom, 105 ) = '".$date_to1."' 
                    AND (
                    COALESCE ( VIEW_YMPI_Emp_OvertimePlan.ovttrans, '-' ) <> '-' 
                    OR
                    CASE

                    WHEN DATEDIFF( MINUTE, ovtplanfrom, ovtplanto ) >= 150 THEN
                    'YA' ELSE '-' 
                    END <> '-' 
                    ) 
                    ORDER BY
                    VIEW_YMPI_Emp_OvertimePlan.SHIFT_OVTPLAN ASC,
                    VIEW_YMPI_Emp_OvertimePlan.emp_no ASC
                    ");


                 if ($details != null) {

                     $date_hours = date("H");
                     if ($date_hours == "05") {
                        $jam1 = date('Y-m-d'. ' 08:32:01', strtotime($day_today . date('H:i:s')));
                    }else{
                        $jam1 = date('Y-m-d H:i:s', strtotime($day_today . date('H:i:s')));
                    }
                    for ($j=0; $j < count($details); $j++) { 
                    if ($details[$j]->SHIFT_OVTPLAN == "Shift_1" && $details[$j]->food == "YA") {
                        $data3 = OvertimeExtrafoodAttendance::firstOrNew(
                            [
                                'time_in' => $day_today,
                                'employee_id' => $details[$j]->emp_no,
                                'ot_from' => $details[$j]->ot_from,
                                'ot_to' => $details[$j]->ot_to,
                                'shift' => 'Shift 1',
                                'name' => $details[$j]->Full_name,
                                'section' => $details[$j]->Section,
                            ],
                            [
                                'shiftdaily_code' => null,
                                'dates' => $jam1,
                                'remark' => 'Order Makan Overtime',
                                'created_by' => "PI2101043",
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]
                        );


                    }else if ($details[$j]->SHIFT_OVTPLAN == "Shift_1" && $details[$j]->food == "-") {
                        $st = "";

                        $st = 'Shift 1';
                        $data3 = OvertimeExtrafoodAttendance::firstOrNew(
                            [
                                'time_in' => $day_today,
                                'employee_id' => $details[$j]->emp_no,
                                'ot_from' => $details[$j]->ot_from,
                                'ot_to' => $details[$j]->ot_to,
                                'shift' => $st,
                                'name' => $details[$j]->Full_name,
                                'section' => $details[$j]->Section,
                            ],
                            [
                                'shiftdaily_code' => null,
                                'dates' => $jam1,
                                'remark' => 'Overtime',
                                'created_by' => "PI2101043",
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]
                        );

                    }else if ($details[$j]->SHIFT_OVTPLAN == "Shift_2" && $details[$j]->extra2 == "YA") {
                        $st = 'Shift 2';

                        $data3 = OvertimeExtrafoodAttendance::firstOrNew(
                            [
                                'time_in' => $day_today,
                                'employee_id' => $details[$j]->emp_no,
                                'ot_from' => $details[$j]->ot_from,
                                'ot_to' => $details[$j]->ot_to,
                                'shift' => $st,
                                'name' => $details[$j]->Full_name,
                                'section' => $details[$j]->Section,
                            ],
                            [
                                'shiftdaily_code' => null,
                                'dates' => $jam1,
                                'remark' => 'Order Extra Food',
                                'created_by' => "PI2101043",
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]
                        );

                    }else if ($details[$j]->SHIFT_OVTPLAN == "Shift_3") {
                        $st = 'Shift 3';
                        $data3 = OvertimeExtrafoodAttendance::firstOrNew(
                            [
                                'time_in' => $day_today,
                                'employee_id' => $details[$j]->emp_no,
                                'ot_from' => $details[$j]->ot_from,
                                'ot_to' => $details[$j]->ot_to,
                                'shift' => $st,
                                'name' => $details[$j]->Full_name,
                                'section' => $details[$j]->Section,
                            ],
                            [
                                'shiftdaily_code' => null,
                                'dates' => $jam1,
                                'remark' => 'Order Extra Food',
                                'created_by' => "PI2101043",
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]
                        );

                    }else{

                        $st = "";
                        if ($details[$j]->SHIFT_OVTPLAN == "Shift_1") {
                            # code...
                            $st = "Shift 1";

                        }else if ($details[$j]->SHIFT_OVTPLAN == "Shift_2") {
                            $st = "Shift 2";

                        }else if ($details[$j]->SHIFT_OVTPLAN == "Shift_3") {
                            $st = "Shift 3";

                        }
                        else{
                            $st = $details[$j]->SHIFT_OVTPLAN;

                        }
                        $data3 = OvertimeExtrafoodAttendance::firstOrNew(
                            [
                                'time_in' => $day_today,
                                'employee_id' => $details[$j]->emp_no,
                                'ot_from' => $details[$j]->ot_from,
                                'ot_to' => $details[$j]->ot_to,
                                'shift' => $st,
                                'name' => $details[$j]->Full_name,
                                'section' => $details[$j]->Section,
                            ],
                            [
                                'shiftdaily_code' => null,
                                'dates' => $jam1,
                                'remark' => 'Overtime',
                                'created_by' => "PI2101043",
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]
                        );
                    }

                    $data3->save();
                }
            }

            $data_sunfish_day = DB::select('SELECT
    * 
                FROM
                `sunfish_shift_syncs` 
                WHERE
                (
                    attend_code IS NULL 
                    AND shiftdaily_code NOT LIKE "%OFF%" AND shiftdaily_code LIKE "%4G%"
                    AND shift_date = "'.$day_today.'")
                    OR (
                    shiftdaily_code LIKE "%Shift_3%" 
                    AND shiftdaily_code NOT LIKE "%OFF%" 
                    AND attend_code != " CUTI" 
                    AND shift_date = "'.$day_today.'")
                    OR (
                    shiftdaily_code LIKE "%Shift_3%"
                    AND attend_code IS NULL
                    AND shiftdaily_code NOT LIKE "%OFF%"
                    AND shift_date = "'.$day_today.'")');


                for ($k=0; $k < count($data_sunfish_day); $k++) { 
                   $employee_syncs = EmployeeSync::where('employee_id', $data_sunfish_day[$k]->employee_id)->first();
                   if (str_contains($data_sunfish_day[$k]->shiftdaily_code,'Shift_1')) {
                       $data4 = OvertimeExtrafoodAttendance::firstOrNew(
                        [
                            'time_in' => $day_today,
                            'employee_id' => $data_sunfish_day[$k]->employee_id,
                            'shift' => 'Shift 1',
                            'name' => $employee_syncs->name,
                            'section' => $employee_syncs->section,
                            'shiftdaily_code' => $data_sunfish_day[$k]->shiftdaily_code,
                        ],
                        [
                            'dates' => $jam1,
                            'remark' => '4 Group',
                            'created_by' => "PI2101043",
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]
                    );

                   }else if (str_contains($data_sunfish_day[$k]->shiftdaily_code,'Shift_2')) {
           # code...
                    $data4 = OvertimeExtrafoodAttendance::firstOrNew(
                        [
                            'time_in' => $day_today,
                            'employee_id' => $data_sunfish_day[$k]->employee_id,
                            'shift' => 'Shift 2',
                            'name' => $employee_syncs->name,
                            'section' => $employee_syncs->section,
                            'shiftdaily_code' => $data_sunfish_day[$k]->shiftdaily_code,
                        ],
                        [
                            'dates' => $jam1,
                            'remark' => '4 Group',
                            'created_by' => "PI2101043",
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]
                    );
                }else if (str_contains($data_sunfish_day[$k]->shiftdaily_code,'Shift_3')) {
                    $data4 = OvertimeExtrafoodAttendance::firstOrNew(
                        [
                            'time_in' => $day_today,
                            'employee_id' => $data_sunfish_day[$k]->employee_id,
                            'shift' => 'Shift 3',
                            'name' => $employee_syncs->name,
                            'section' => $employee_syncs->section,
                            'shiftdaily_code' => $data_sunfish_day[$k]->shiftdaily_code,
                        ],
                        [
                            'dates' => $jam1,
                            'remark' => 'Order Extra Food',
                            'created_by' => "PI2101043",
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]
                    );
                }
                $data4->save();
            }

            $times = ['+1 days','+2 days'];

            for ($m=0; $m < count($times); $m++) { 

                $nextday = date('Y-m-d', strtotime(date('Y-m-d') .$times[$m]));

                $date_to = date('d-m-Y', strtotime($nextday));
                $day = date('Y-m-d', strtotime($nextday));

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

                    WHEN SHIFT_OVTPLAN LIKE '%Shift_1%' 
                    AND DATEDIFF( MINUTE, ovtplanfrom, ovtplanto ) >= 150 THEN
                    'YA' ELSE '-' 
                    END AS food,
                    CASE

                    WHEN SHIFT_OVTPLAN LIKE '%Shift_2%' 
                    AND DATEDIFF( MINUTE, ovtplanfrom, ovtplanto ) >= 120 THEN
                    'YA' ELSE '-' 
                    END AS extra2,
                    CASE

                    WHEN SHIFT_OVTPLAN LIKE '%Shift_3%' THEN
                    'YA' ELSE '-' 
                    END AS extra3 
                    FROM
                    VIEW_YMPI_Emp_OvertimePlan
                    LEFT JOIN VIEW_YMPI_Emp_OrgUnit ON VIEW_YMPI_Emp_OrgUnit.Emp_no = VIEW_YMPI_Emp_OvertimePlan.emp_no 
                    WHERE
                    CONVERT ( VARCHAR, ovtplanfrom, 105 ) = '".$date_to."' 
                    AND (
                    COALESCE ( VIEW_YMPI_Emp_OvertimePlan.ovttrans, '-' ) <> '-' 
                    OR
                    CASE

                    WHEN DATEDIFF( MINUTE, ovtplanfrom, ovtplanto ) >= 150 THEN
                    'YA' ELSE '-' 
                    END <> '-' 
                    ) 
                    ORDER BY
                    VIEW_YMPI_Emp_OvertimePlan.SHIFT_OVTPLAN ASC,
                    VIEW_YMPI_Emp_OvertimePlan.emp_no ASC
                    ");



                if ($details != null) {
                    for ($j=0; $j < count($details); $j++) { 
                     $date_hours = date("H");

                     if ($date_hours == "05") {
                        $jam1 = date('Y-m-d'. ' 08:32:01', strtotime($nextday . date('H:i:s')));
                    }else{
                        $jam1 = date('Y-m-d H:i:s', strtotime($nextday . date('H:i:s')));
                    }


                    if ($details[$j]->SHIFT_OVTPLAN == "Shift_1" && $details[$j]->food == "YA") {
                        $data2 = OvertimeExtrafoodAttendance::firstOrNew(
                            [
                                'time_in' => $day,
                                'employee_id' => $details[$j]->emp_no,
                                'ot_from' => $details[$j]->ot_from,
                                'ot_to' => $details[$j]->ot_to,
                                'shift' => 'Shift 1',
                                'name' => $details[$j]->Full_name,
                                'section' => $details[$j]->Section,
                            ],
                            [
                                'shiftdaily_code' => null,
                                'dates' => $jam1,
                                'remark' => 'Order Makan Overtime',
                                'created_by' => "PI2101043",
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]
                        );


                    }else if ($details[$j]->SHIFT_OVTPLAN != "Shift_1" && $details[$j]->food == "YA") {
                        $data2 = OvertimeExtrafoodAttendance::firstOrNew(
                            [
                                'time_in' => $day,
                                'employee_id' => $details[$j]->emp_no,
                                'ot_from' => $details[$j]->ot_from,
                                'ot_to' => $details[$j]->ot_to,
                                'shift' => 'Shift 1',
                                'name' => $details[$j]->Full_name,
                                'section' => $details[$j]->Section,
                            ],
                            [
                                'shiftdaily_code' => null,
                                'dates' => $jam1,
                                'remark' => 'Order Makan Overtime',
                                'created_by' => "PI2101043",
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]
                        );
                    }

                    else if ($details[$j]->SHIFT_OVTPLAN == "Shift_1" && $details[$j]->food == "-") {
                        $st = "";

                        $st = 'Shift 1';
                        $data2 = OvertimeExtrafoodAttendance::firstOrNew(
                            [
                                'time_in' => $day,
                                'employee_id' => $details[$j]->emp_no,
                                'ot_from' => $details[$j]->ot_from,
                                'ot_to' => $details[$j]->ot_to,
                                'shift' => $st,
                                'name' => $details[$j]->Full_name,
                                'section' => $details[$j]->Section,
                            ],
                            [
                                'shiftdaily_code' => null,
                                'dates' => $jam1,
                                'remark' => 'Overtime',
                                'created_by' => "PI2101043",
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]
                        );

                    }else if ($details[$j]->SHIFT_OVTPLAN == "Shift_2" && $details[$j]->extra2 == "YA") {
                        $st = 'Shift 2';

                        $data2 = OvertimeExtrafoodAttendance::firstOrNew(
                            [
                                'time_in' => $day,
                                'employee_id' => $details[$j]->emp_no,
                                'ot_from' => $details[$j]->ot_from,
                                'ot_to' => $details[$j]->ot_to,
                                'shift' => $st,
                                'name' => $details[$j]->Full_name,
                                'section' => $details[$j]->Section,
                            ],
                            [
                                'shiftdaily_code' => null,
                                'dates' => $jam1,
                                'remark' => 'Order Extra Food',
                                'created_by' => "PI2101043",
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]
                        );

                    }else if ($details[$j]->SHIFT_OVTPLAN == "Shift_3") {
                        $st = 'Shift 3';
                        $data2 = OvertimeExtrafoodAttendance::firstOrNew(
                            [
                                'time_in' => $day,
                                'employee_id' => $details[$j]->emp_no,
                                'ot_from' => $details[$j]->ot_from,
                                'ot_to' => $details[$j]->ot_to,
                                'shift' => $st,
                                'name' => $details[$j]->Full_name,
                                'section' => $details[$j]->Section,
                            ],
                            [
                                'shiftdaily_code' => null,
                                'dates' => $jam1,
                                'remark' => 'Order Extra Food',
                                'created_by' => "PI2101043",
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]
                        );

                    }else{

                        $st = "";
                        if ($details[$j]->SHIFT_OVTPLAN == "Shift_1") {
                            # code...
                            $st = "Shift 1";

                        }else if ($details[$j]->SHIFT_OVTPLAN == "Shift_2") {
                            $st = "Shift 2";

                        }else if ($details[$j]->SHIFT_OVTPLAN == "Shift_3") {
                            $st = "Shift 3";

                        }
                        else{
                            $st = $details[$j]->SHIFT_OVTPLAN;

                        }
                        $data2 = OvertimeExtrafoodAttendance::firstOrNew(
                            [
                                'time_in' => $day,
                                'employee_id' => $details[$j]->emp_no,
                                'ot_from' => $details[$j]->ot_from,
                                'ot_to' => $details[$j]->ot_to,
                                'shift' => $st,
                                'name' => $details[$j]->Full_name,
                                'section' => $details[$j]->Section,
                            ],
                            [
                                'shiftdaily_code' => null,
                                'dates' => $jam1,
                                'remark' => 'Overtime',
                                'created_by' => "PI2101043",
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]
                        );
                    }

                    $data2->save();
                }
            }

            $data_sunfish = DB::select('SELECT
    * 
                FROM
                `sunfish_shift_syncs` 
                WHERE
                ( attend_code LIKE "%Shift_1%" AND shiftdaily_code NOT LIKE "%OFF%" AND shift_date = "'.$nextday.'" ) 
                    OR ( attend_code LIKE "%Shift_2%" AND shiftdaily_code NOT LIKE "%OFF%" AND shift_date = "'.$nextday.'" ) 
                    OR ( attend_code LIKE "%Shift_3%" AND shiftdaily_code NOT LIKE "%OFF%" AND shift_date = "'.$nextday.'" ) 
                    OR (attend_code IS NULL AND shiftdaily_code NOT LIKE "%OFF%" AND shift_date = "'.$nextday.'")');

                if ($data_sunfish != null) {
                    for ($i=0; $i < count($data_sunfish); $i++) {
                       $date_hours = date("H");

                       if ($date_hours == "05") {
                        $jam1 = date('Y-m-d'. ' 08:32:01', strtotime($data_sunfish[$i]->shift_date . date('H:i:s')));
                    }else{
                        $jam1 = date('Y-m-d H:i:s', strtotime($data_sunfish[$i]->shift_date . date('H:i:s')));
                    } 
                    $employee_syncs = EmployeeSync::where('employee_id', $data_sunfish[$i]->employee_id)->first();
                    if (str_contains($data_sunfish[$i]->shiftdaily_code,'Shift_1')) {
                      $data5 = OvertimeExtrafoodAttendance::firstOrNew(
                        [
                            'time_in' => $day,
                            'employee_id' => $data_sunfish[$i]->employee_id,
                            'shift' => 'Shift 1',
                            'name' => $employee_syncs->name,
                            'section' => $employee_syncs->section,
                            'shiftdaily_code' => $data_sunfish[$i]->shiftdaily_code,
                        ],
                        [
                            'dates' => $jam1,
                            'remark' => 'Order Extra Food',
                            'created_by' => "PI2101043",
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]
                    );

                  }else if (str_contains($data_sunfish[$i]->shiftdaily_code,'Shift_2')) {
                     $data5 = OvertimeExtrafoodAttendance::firstOrNew(
                        [
                            'time_in' => $day,
                            'employee_id' => $data_sunfish[$i]->employee_id,
                            'shift' => 'Shift 2',
                            'name' => $employee_syncs->name,
                            'section' => $employee_syncs->section,
                            'shiftdaily_code' => $data_sunfish[$i]->shiftdaily_code,
                        ],
                        [
                            'dates' => $jam1,
                            'remark' => 'Order Extra Food',
                            'created_by' => "PI2101043",
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]
                    );
                 }elseif (str_contains($data_sunfish[$i]->shiftdaily_code,'Shift_3')) {
                   $data5 = OvertimeExtrafoodAttendance::firstOrNew(
                    [
                        'time_in' => $day,
                        'employee_id' => $data_sunfish[$i]->employee_id,
                        'shift' => 'Shift 3',
                        'name' => $employee_syncs->name,
                        'section' => $employee_syncs->section,
                        'shiftdaily_code' => $data_sunfish[$i]->shiftdaily_code,
                    ],
                    [
                        'dates' => $jam1,
                        'remark' => 'Order Extra Food',
                        'created_by' => "PI2101043",
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                );

               }
               $data5->save();


           }
       }
   }


}else{


   $date_to1 = date('d-m-Y', strtotime(date('Y-m-d')));
   $day_today = date('Y-m-d', strtotime(date('Y-m-d')));

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

    WHEN SHIFT_OVTPLAN LIKE '%Shift_1%' 
    AND DATEDIFF( MINUTE, ovtplanfrom, ovtplanto ) >= 150 THEN
    'YA' ELSE '-' 
    END AS food,
    CASE

    WHEN SHIFT_OVTPLAN LIKE '%Shift_2%' 
    AND DATEDIFF( MINUTE, ovtplanfrom, ovtplanto ) >= 120 THEN
    'YA' ELSE '-' 
    END AS extra2,
    CASE

    WHEN SHIFT_OVTPLAN LIKE '%Shift_3%' THEN
    'YA' ELSE '-' 
    END AS extra3 
    FROM
    VIEW_YMPI_Emp_OvertimePlan
    LEFT JOIN VIEW_YMPI_Emp_OrgUnit ON VIEW_YMPI_Emp_OrgUnit.Emp_no = VIEW_YMPI_Emp_OvertimePlan.emp_no 
    WHERE
    CONVERT ( VARCHAR, ovtplanfrom, 105 ) = '".$date_to1."' 
    AND (
    COALESCE ( VIEW_YMPI_Emp_OvertimePlan.ovttrans, '-' ) <> '-' 
    OR
    CASE

    WHEN DATEDIFF( MINUTE, ovtplanfrom, ovtplanto ) >= 150 THEN
    'YA' ELSE '-' 
    END <> '-' 
    ) 
    ORDER BY
    VIEW_YMPI_Emp_OvertimePlan.SHIFT_OVTPLAN ASC,
    VIEW_YMPI_Emp_OvertimePlan.emp_no ASC
    ");


   if ($details != null) {
    for ($j=0; $j < count($details); $j++) { 
        $date_hours = date("H");

        if ($date_hours == "05") {
            $jam1 = date('Y-m-d'. ' 08:32:01', strtotime($day_today . date('H:i:s')));
        }else{
            $jam1 = date('Y-m-d H:i:s', strtotime($day_today . date('H:i:s')));
        } 

        if ($details[$j]->SHIFT_OVTPLAN == "Shift_1" && $details[$j]->food == "YA") {
            $data3 = OvertimeExtrafoodAttendance::firstOrNew(
                [
                    'time_in' => $day_today,
                    'employee_id' => $details[$j]->emp_no,
                    'ot_from' => $details[$j]->ot_from,
                    'ot_to' => $details[$j]->ot_to,
                    'shift' => 'Shift 1',
                    'name' => $details[$j]->Full_name,
                    'section' => $details[$j]->Section,
                ],
                [
                    'shiftdaily_code' => null,
                    'dates' => $jam1,
                    'remark' => 'Order Makan Overtime',
                    'created_by' => "PI2101043",
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );


        }else if ($details[$j]->SHIFT_OVTPLAN == "Shift_1" && $details[$j]->food == "-") {
            $st = "";

            $st = 'Shift 1';
            $data3 = OvertimeExtrafoodAttendance::firstOrNew(
                [
                    'time_in' => $day_today,
                    'employee_id' => $details[$j]->emp_no,
                    'ot_from' => $details[$j]->ot_from,
                    'ot_to' => $details[$j]->ot_to,
                    'shift' => $st,
                    'name' => $details[$j]->Full_name,
                    'section' => $details[$j]->Section,
                ],
                [
                    'shiftdaily_code' => null,
                    'dates' => $jam1,
                    'remark' => 'Overtime',
                    'created_by' => "PI2101043",
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );

        }else if ($details[$j]->SHIFT_OVTPLAN == "Shift_2" && $details[$j]->extra2 == "YA") {
            $st = 'Shift 2';

            $data3 = OvertimeExtrafoodAttendance::firstOrNew(
                [
                    'time_in' => $day_today,
                    'employee_id' => $details[$j]->emp_no,
                    'ot_from' => $details[$j]->ot_from,
                    'ot_to' => $details[$j]->ot_to,
                    'shift' => $st,
                    'name' => $details[$j]->Full_name,
                    'section' => $details[$j]->Section,
                ],
                [
                    'shiftdaily_code' => null,
                    'dates' => $jam1,
                    'remark' => 'Order Extra Food',
                    'created_by' => "PI2101043",
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );

        }else if ($details[$j]->SHIFT_OVTPLAN == "Shift_3") {
            $st = 'Shift 3';
            $data3 = OvertimeExtrafoodAttendance::firstOrNew(
                [
                    'time_in' => $day_today,
                    'employee_id' => $details[$j]->emp_no,
                    'ot_from' => $details[$j]->ot_from,
                    'ot_to' => $details[$j]->ot_to,
                    'shift' => $st,
                    'name' => $details[$j]->Full_name,
                    'section' => $details[$j]->Section,
                ],
                [
                    'shiftdaily_code' => null,
                    'dates' => $jam1,
                    'remark' => 'Order Extra Food',
                    'created_by' => "PI2101043",
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );

        }else{

            $st = "";
            if ($details[$j]->SHIFT_OVTPLAN == "Shift_1") {
                            # code...
                $st = "Shift 1";

            }else if ($details[$j]->SHIFT_OVTPLAN == "Shift_2") {
                $st = "Shift 2";

            }else if ($details[$j]->SHIFT_OVTPLAN == "Shift_3") {
                $st = "Shift 3";

            }
            else{
                $st = $details[$j]->SHIFT_OVTPLAN;

            }
            $data3 = OvertimeExtrafoodAttendance::firstOrNew(
                [
                    'time_in' => $day_today,
                    'employee_id' => $details[$j]->emp_no,
                    'ot_from' => $details[$j]->ot_from,
                    'ot_to' => $details[$j]->ot_to,
                    'shift' => $st,
                    'name' => $details[$j]->Full_name,
                    'section' => $details[$j]->Section,
                ],
                [
                    'shiftdaily_code' => null,
                    'dates' => $jam1,
                    'remark' => 'Overtime',
                    'created_by' => "PI2101043",
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );
        }

        $data3->save();
    }
}

$date_hours = date("H");

        if ($date_hours == "05") {
            $jam1 = date('Y-m-d'. ' 08:32:01', strtotime($day_today . date('H:i:s')));
        }else{
            $jam1 = date('Y-m-d H:i:s', strtotime($day_today . date('H:i:s')));
        } 


$data_sunfish_day = DB::select('SELECT
    * 
    FROM
    `sunfish_shift_syncs` 
    WHERE
    (
        attend_code IS NULL 
        AND shiftdaily_code NOT LIKE "%OFF%" AND shiftdaily_code LIKE "%4G%"
        AND shift_date = "'.$day_today.'")
        OR (
        shiftdaily_code LIKE "%Shift_3%" 
        AND shiftdaily_code NOT LIKE "%OFF%"
        AND attend_code != " CUTI" 
        AND shift_date = "'.$day_today.'")
        OR (
        shiftdaily_code LIKE "%Shift_3%"
        AND attend_code IS NULL
        AND shiftdaily_code NOT LIKE "%OFF%"
        AND shift_date = "'.$day_today.'")');


    for ($k=0; $k < count($data_sunfish_day); $k++) { 
       $employee_syncs = EmployeeSync::where('employee_id', $data_sunfish_day[$k]->employee_id)->first();
       if (str_contains($data_sunfish_day[$k]->shiftdaily_code,'Shift_1')) {
           $data4 = OvertimeExtrafoodAttendance::firstOrNew(
            [
                'time_in' => $day_today,
                'employee_id' => $data_sunfish_day[$k]->employee_id,
                'shift' => 'Shift 1',
                'name' => $employee_syncs->name,
                'section' => $employee_syncs->section,
                'shiftdaily_code' => $data_sunfish_day[$k]->shiftdaily_code,
            ],
            [
                'dates' => $jam1,
                'remark' => '4 Group',
                'created_by' => "PI2101043",
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        );

       }else if (str_contains($data_sunfish_day[$k]->shiftdaily_code,'Shift_2')) {
           # code...
        $data4 = OvertimeExtrafoodAttendance::firstOrNew(
            [
                'time_in' => $day_today,
                'employee_id' => $data_sunfish_day[$k]->employee_id,
                'shift' => 'Shift 2',
                'name' => $employee_syncs->name,
                'section' => $employee_syncs->section,
                'shiftdaily_code' => $data_sunfish_day[$k]->shiftdaily_code,
            ],
            [
                'dates' => $jam1,
                'remark' => '4 Group',
                'created_by' => "PI2101043",
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        );
    }else if (str_contains($data_sunfish_day[$k]->shiftdaily_code,'Shift_3')) {
        $data4 = OvertimeExtrafoodAttendance::firstOrNew(
            [
                'time_in' => $day_today,
                'employee_id' => $data_sunfish_day[$k]->employee_id,
                'shift' => 'Shift 3',
                'name' => $employee_syncs->name,
                'section' => $employee_syncs->section,
                'shiftdaily_code' => $data_sunfish_day[$k]->shiftdaily_code,
            ],
            [
                'dates' => $jam1,
                'remark' => 'Order Extra Food',
                'created_by' => "PI2101043",
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        );
    }
    $data4->save();
}
}
}
}

}
}

