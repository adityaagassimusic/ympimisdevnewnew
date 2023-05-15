<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\InjectionScheduleTemp;
use App\InjectionScheduleLog;
use App\InjectionSchedule;
use App\InjectionScheduleMoldingLog;
use App\InjectionScheduleMolding;
use App\InjectionMachineCycleTime;
use App\InjectionMachineMaster;
use App\InjectionInventory;
use App\InjectionMoldingMaster;
use DateTime;
use DateInterval;

class InjectionScheduleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'injection:schedule';

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

        // $inventory = InjectionInventory::get();
        // foreach ($inventory as $key) {
        //     $invent = InjectionInventory::find($key->id);
        //     $invent->forceDelete();
        // }
        $schedule_all = InjectionScheduleLog::whereDate('created_at','<',date('Y-m-d'))->get();

        $id_schedule_new = [];
        $id_schedule_old = [];

        for ($u=0; $u < count($schedule_all); $u++) {
            array_push($id_schedule_old, $schedule_all[$u]->id);
            $menu = InjectionSchedule::updateOrCreate(
                [
                    'machine' => $schedule_all[$u]->machine,
                    'start_time' => $schedule_all[$u]->start_time,
                    'end_time' => $schedule_all[$u]->end_time,
                    'material_number' => $schedule_all[$u]->material_number,
                ],
                [
                    'id_schedule' => $schedule_all[$u]->id_schedule,
                    'machine' => $schedule_all[$u]->machine,
                    'start_time' => $schedule_all[$u]->start_time,
                    'end_time' => $schedule_all[$u]->end_time,
                    'material_number' => $schedule_all[$u]->material_number,
                    'material_description' => $schedule_all[$u]->material_description,
                    'part' => $schedule_all[$u]->part,
                    'color' => $schedule_all[$u]->color,
                    'qty' => $schedule_all[$u]->qty,
                    'created_by' => $schedule_all[$u]->created_by,
                    'reason' => $schedule_all[$u]->reason,
                    'molding' => $schedule_all[$u]->molding,
                    'created_by' => $schedule_all[$u]->created_by,
                ]
            );
            $menu->save();

            array_push($id_schedule_new, $menu->id);
        }

        $schedule_all = InjectionScheduleMoldingLog::whereDate('created_at','<',date('Y-m-d'))->get();

        for ($u=0; $u < count($schedule_all); $u++) {
            var_dump($schedule_all[$u]->id_schedule);
            if (in_array($schedule_all[$u]->id_schedule, $id_schedule_old)) {
                for ($i=0; $i < count($id_schedule_old); $i++) { 
                    if ($schedule_all[$u]->id_schedule == $id_schedule_old[$i]) {
                        $id_schedule_news = $id_schedule_new[$i];
                        var_dump('ada');
                    }
                }
            }
            $menu = InjectionScheduleMolding::updateOrCreate(
                [
                    'id_schedule' => $schedule_all[$u]->id_schedule,
                    'machine' => $schedule_all[$u]->machine,
                    'start_time' => $schedule_all[$u]->start_time,
                    'end_time' => $schedule_all[$u]->end_time,
                    'material_number' => $schedule_all[$u]->material_number,
                ],
                [
                    'id_schedule' => $id_schedule_news,
                    'machine' => $schedule_all[$u]->machine,
                    'start_time' => $schedule_all[$u]->start_time,
                    'end_time' => $schedule_all[$u]->end_time,
                    'material_number' => $schedule_all[$u]->material_number,
                    'material_description' => $schedule_all[$u]->material_description,
                    'part' => $schedule_all[$u]->part,
                    'color' => $schedule_all[$u]->color,
                    'qty' => $schedule_all[$u]->qty,
                    'created_by' => $schedule_all[$u]->created_by,
                    'reason' => $schedule_all[$u]->reason,
                    'molding' => $schedule_all[$u]->molding,
                    'created_by' => $schedule_all[$u]->created_by,
                ]
            );
            $menu->save();
        }

        InjectionScheduleTemp::truncate();
        InjectionScheduleLog::truncate();
        InjectionScheduleMoldingLog::truncate();
        $j = 7;
        $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
        $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
        foreach ($weekly_calendars as $key) {
            if ($key->week_date == $nextdayplus1) {
                if ($key->remark == 'H') {
                    $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays(++$j)));
                }
            }
        }
        // if (date('D')=='Fri' || date('D')=='Sat') {
        //     $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
        // }

        // $nextdayplus1 = date('Y-m-t');

        $first = date('Y-m-01');
        $now = date('Y-m-d');
        $j = 1;
        $tomorrow = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
        $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
        foreach ($weekly_calendars as $key) {
            if ($key->week_date == $tomorrow) {
                if ($key->remark == 'H') {
                    $tomorrow = date('Y-m-d', strtotime(carbon::now()->addDays(++$j)));
                }
            }
        }

        // $data = DB::SELECT("SELECT
        //     c.material_number,
        //     c.material_description,
        //     c.part_code,
        //     c.color,
        //     SUM( c.stock ) AS stock,
        //     SUM( c.plan ) AS plan,
        //     SUM( c.stock ) - SUM( c.plan ) AS diff,
        // IF
        //     (
        //         SUM( c.stock ) - SUM( c.plan ) >= 0,
        //         0,-(
        //         SUM( c.stock ) - SUM( c.plan ))) AS debt,
        //     c.due_date 
        // FROM
        //     (
        //     SELECT
        //         gmc AS material_number,
        //         part_name AS material_description,
        //         color,
        //         part_code,
        //         COALESCE (( SELECT SUM( quantity ) FROM injection_inventories WHERE location = 'RC11' AND material_number = gmc ), 0 ) AS stock,
        //         0 AS plan,
        //         '' AS due_date 
        //     FROM
        //         injection_parts 
        //     WHERE
        //         remark = 'injection' 
        //         AND deleted_at IS NULL UNION ALL
        //     SELECT
        //         a.material_number,
        //         a.material_description,
        //         a.color,
        //         a.part_code,
        //         0 AS stock,
        //         sum( a.plan )- sum( a.stamp ) AS plan,
        //         a.due_date 
        //     FROM
        //         (
        //         SELECT
        //             injection_part_details.gmc AS material_number,
        //             injection_part_details.part AS material_description,
        //             injection_part_details.color,
        //             injection_part_details.part_code,
        //             SUM( quantity ) AS plan,
        //             0 AS stamp,
        //             due_date 
        //         FROM
        //             production_schedules
        //             LEFT JOIN materials ON materials.material_number = production_schedules.material_number
        //             LEFT JOIN injection_part_details ON injection_part_details.model = materials.model 
        //         WHERE
        //             materials.category = 'FG' 
        //             AND materials.origin_group_code = '072' 
        //             AND production_schedules.due_date BETWEEN '".$first."' 
        //             AND '".$nextdayplus1."' 
        //         GROUP BY
        //             material_number,
        //             part,
        //             color,
        //             part_code,
        //             due_date UNION ALL
        //         SELECT
        //             injection_part_details.gmc AS material_number,
        //             injection_part_details.part AS material_description,
        //             injection_part_details.color,
        //             injection_part_details.part_code,
        //             0 AS plan,
        //             SUM( quantity ) AS stamp,
        //             DATE( flo_details.created_at ) AS due_date 
        //         FROM
        //             flo_details
        //             LEFT JOIN materials ON materials.material_number = flo_details.material_number
        //             LEFT JOIN injection_part_details ON injection_part_details.model = materials.model 
        //         WHERE
        //             materials.category = 'FG' 
        //             AND materials.origin_group_code = '072' 
        //             AND DATE( flo_details.created_at ) BETWEEN '".$first."' 
        //             AND '".$now."' 
        //         GROUP BY
        //             material_number,
        //             part,
        //             color,
        //             part_code 
        //         ) a 
        //     GROUP BY
        //         a.material_number,
        //         a.material_description,
        //         a.color,
        //         a.part_code,
        //         a.due_date 
        //     ) c 
        // WHERE
        //     c.due_date != '' 
        //     AND c.due_date >= '".$tomorrow."' 
        //     AND c.due_date <= '".$nextdayplus1."' 
        // GROUP BY
        //     c.material_number,
        //     c.material_description,
        //     c.color,
        //     c.part_code,
        //     c.due_date 
        // ORDER BY
        //     due_date");


        $data = DB::SELECT("SELECT
    injection_part_details.gmc AS material_number,
    injection_part_details.part AS material_description,
    injection_part_details.part_code,
    injection_part_details.color,
    (
    SELECT
        SUM( quantity ) 
    FROM
        injection_inventories 
    WHERE
        ( injection_inventories.location = 'RC11' AND injection_inventories.material_number = injection_part_details.gmc ) 
        OR ( injection_inventories.location = 'RC91' AND injection_inventories.material_number = injection_part_details.gmc ) 
    ) AS stock,
    sum( production_schedules.quantity ) AS plan,
    (
    SELECT
        sum( quantity ) 
    FROM
        injection_inventories 
    WHERE
        ( injection_inventories.location = 'RC11' AND injection_inventories.material_number = injection_part_details.gmc ) 
        OR ( injection_inventories.location = 'RC91' AND injection_inventories.material_number = injection_part_details.gmc ) 
    ) - sum( production_schedules.quantity ) AS diff,
IF
    ((
        SELECT
            SUM( quantity ) 
        FROM
            injection_inventories 
        WHERE
            ( injection_inventories.location = 'RC11' AND injection_inventories.material_number = injection_part_details.gmc ) 
            OR ( injection_inventories.location = 'RC91' AND injection_inventories.material_number = injection_part_details.gmc ) 
            ) - sum( production_schedules.quantity ) > 0,
        0,-((
            SELECT
                SUM( quantity ) 
            FROM
                injection_inventories 
            WHERE
                ( injection_inventories.location = 'RC11' AND injection_inventories.material_number = injection_part_details.gmc ) 
                OR ( injection_inventories.location = 'RC91' AND injection_inventories.material_number = injection_part_details.gmc ) 
            ) - sum( production_schedules.quantity ))) AS debt 
            FROM
            production_schedules
            LEFT JOIN materials ON materials.material_number = production_schedules.material_number
            LEFT JOIN injection_part_details ON injection_part_details.model = materials.model 
            WHERE
            DATE( due_date ) BETWEEN '".$tomorrow."' 
            AND '".$nextdayplus1."' 
            AND origin_group_code = 072 
            GROUP BY
            injection_part_details.gmc,
            injection_part_details.part,
            injection_part_details.part_code,
            injection_part_details.color");

        foreach ($data as $key) {
            if ($key->debt != 0) {
                // $partpart = explode(' ',$key->part_code);
                // $colorcolor = explode(')',$key->color);

                $schedule = InjectionScheduleTemp::create([
                    'material_number' => $key->material_number,
                    'date' => date('Y-m-d'),
                    // 'due_date' => $key->due_date,
                    'material_description' => $key->material_description,
                    'part' => $key->part_code,
                    'color' => $key->color,
                    'stock' => $key->stock,
                    'plan' => $key->plan,
                    'diff' => $key->diff,
                    'debt' => $key->debt,
                    'created_by' => '1930',
                ]);
                $schedule->save();
            }
        }

        // $j = 3;
        // $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
        // $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
        // foreach ($weekly_calendars as $key) {
        //     if ($key->week_date == $nextdayplus1) {
        //         if ($key->remark == 'H') {
        //             $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays(++$j)));
        //         }
        //     }
        // }
        // if (date('D')=='Fri' || date('D')=='Sat') {
        //     $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
        // }

        // // $nextdayplus1 = date('Y-m-t');

        // $first = date('Y-m-01');
        // $now = date('Y-m-d');

        // $tomorrow = date('Y-m-d', strtotime(carbon::now()->addDays(1)));

        $debttoday = DB::SELECT("SELECT
                date,
                due_date,
                material_number,
                material_description,
                b.part,
                b.color,
                stock,
                plan,
                diff,
                SUM( debt ) AS debt,
                cycle,
                shoot,
                qty,
                qty_hako,
                machine,
                CONCAT( 'Mesin ', SPLIT_STRING ( machine, ',', 1 ) ) AS mesin1,
                CONCAT( 'Mesin ', SPLIT_STRING ( machine, ',', 2 ) ) AS mesin2,
                CONCAT( 'Mesin ', SPLIT_STRING ( machine, ',', 3 ) ) AS mesin3,
                ROUND(((( b.debt / b.shoot )* b.cycle )/ 60 )/ 60 ) AS jam,
                ROUND((( b.debt / b.shoot )* b.cycle )/ 60 ) AS menit,
                ROUND(( b.debt / b.shoot )* b.cycle ) AS detik 
            FROM
                (
                SELECT
                    date,
                    due_date,
                    material_number,
                    material_description,
                    a.part,
                    a.color,
                    stock,
                    plan,
                    diff,
                    debt,
                    cycle,
                    shoot,
                    qty,
                    qty_hako,
                    ( SELECT DISTINCT ( machine ) FROM injection_machine_cycle_times WHERE injection_machine_cycle_times.part = a.part AND injection_machine_cycle_times.color = a.color ) AS machine,
                    CONCAT( 'Mesin ', SPLIT_STRING ( ( SELECT DISTINCT ( mesin ) FROM injection_molding_masters WHERE injection_molding_masters.product = a.part ), ',', 1 ) ) AS mesin1,
                    CONCAT( 'Mesin ', SPLIT_STRING ( ( SELECT DISTINCT ( mesin ) FROM injection_molding_masters WHERE injection_molding_masters.product = a.part ), ',', 2 ) ) AS mesin2,
                    CONCAT( 'Mesin ', SPLIT_STRING ( ( SELECT DISTINCT ( mesin ) FROM injection_molding_masters WHERE injection_molding_masters.product = a.part ), ',', 3 ) ) AS mesin3 
                FROM
                    injection_schedule_temps AS a
                    LEFT JOIN injection_machine_cycle_times ON injection_machine_cycle_times.part = a.part 
                    AND injection_machine_cycle_times.color = a.color 
                ) b 
            GROUP BY
                material_number 
            ORDER BY
                material_number");

        $mesin = DB::SELECT("SELECT
            mesin 
            FROM
            injection_machine_masters");

        $schedules = [];

        $count = 0;

        foreach ($debttoday as $key) {
            $firstDate = date("Y-m-d");
            foreach ($weekly_calendars as $weekly) {
                if ($weekly->week_date == $firstDate) {
                    if ($weekly->remark == 'H') {
                        $firstDate = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
                    }
                }
            }
            $lastDate = date("Y-m-d");
            foreach ($weekly_calendars as $weekly2) {
                if ($weekly2->week_date == $lastDate) {
                    if ($weekly2->remark == 'H') {
                        $lastDate = date('Y-m-d', strtotime(carbon::now()->addDays(1)));
                    }
                }
            }
            $schedules[] = array(
                'material_number' => $key->material_number,
                'material_description' => $key->material_description,
                'part' => $key->part,
                'color' => $key->color,
                'qty' => $key->debt,
                'start_time' => date("Y-m-d H:i:s",strtotime(date($firstDate.' 07:00:00'))),
                'end_time' => date("Y-m-d H:i:s",strtotime(date($lastDate.' 07:00:00'))+$key->detik),
                'machine' => $key->mesin1,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
        }

        for ($i=0; $i < count($schedules); $i++) {
            DB::table('injection_schedule_logs')->insert([
                $schedules[$i]
            ]);
        }

        $id_schedule = InjectionScheduleLog::get();
        for ($i=0; $i < count($id_schedule); $i++) { 
            $log = InjectionScheduleLog::where('id',$id_schedule[$i]->id)->first();
            $log->id_schedule = $log->id;
            $log->save();
        }

        $mesinsama = DB::SELECT("SELECT
            injection_schedule_logs.*,
            SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 1 ) AS machine_1,
            IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ), 0 ) AS machine_2,
            IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 3 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 3 ), 0 ) AS machine_3 
            FROM
            injection_schedule_logs
            INNER JOIN ( SELECT machine FROM injection_schedule_logs GROUP BY machine HAVING COUNT( machine ) > 1 ) temp ON injection_schedule_logs.machine = temp.machine
            JOIN injection_machine_cycle_times ON injection_machine_cycle_times.part = injection_schedule_logs.part 
            AND injection_machine_cycle_times.color = injection_schedule_logs.color 
            ORDER BY
            injection_schedule_logs.machine,
            IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ), 0 )");

        if (count($mesinsama) > 0) {
            $dandori = 0;
            $dandori_time = 0;
            foreach ($mesin as $key) {
                $mesins = [];
                for ($i=0; $i < count($mesinsama); $i++) { 
                    if ($mesinsama[$i]->machine == $key->mesin) {
                        array_push($mesins, $mesinsama[$i]);
                    }
                }
                for ($j=1; $j < count($mesins); $j++) { 
                    if ($mesins[$j]->machine_2 != 0) {
                        $log = InjectionScheduleLog::where('id',$mesins[$j]->id)->first();
                        $log->machine = 'Mesin '.$mesins[$j]->machine_2;
                        $log->save();
                    }
                }

                // for ($m=0; $m < count($mesins); $m++) {
                //     if ($mesins[$m]->start_time == date('Y-m-d 07:00:00')) {
                //         if ($dandori % 2 == 0) {
                //             $dandori_time = $dandori_time + 14400;
                //         }
                //         $log = InjectionScheduleLog::where('id',$mesins[$m]->id)->first();
                //         $ts1 = strtotime($log->start_time);
                //         $ts2 = strtotime($log->end_time);
                //         $seconds_diff = $ts2 - $ts1;
                //         $secondall = $seconds_diff+$dandori_time;
                //         $log->start_time = date("Y-m-d H:i:s",strtotime(date('Y-m-d 07:00:00'))+$dandori_time);
                //         $log->end_time = date("Y-m-d H:i:s",strtotime(date('Y-m-d 07:00:00'))+$secondall);
                //         $log->save();
                //         $dandori++;
                //     }
                // }
            }
        }

        // $mesinsamadandori = DB::SELECT("select * from injection_schedule_logs");

        // if (count($mesinsamadandori) > 0) {
        //     $dandori = 0;
        //     $dandori_time = 0;
        //     for ($m=0; $m < count($mesinsamadandori); $m++) {
        //         if ($dandori % 2 == 0) {
        //             $dandori_time = $dandori_time + 14400;
        //         }
        //         $log = InjectionScheduleLog::where('id',$mesinsamadandori[$m]->id)->first();
        //         $ts1 = strtotime($log->start_time);
        //         $ts2 = strtotime($log->end_time);
        //         $seconds_diff = $ts2 - $ts1;
        //         $secondall = $seconds_diff+$dandori_time;
        //         $log->start_time = date("Y-m-d H:i:s",strtotime(date('Y-m-d 07:00:00'))+$dandori_time);
        //         $log->end_time = date("Y-m-d H:i:s",strtotime(date('Y-m-d 07:00:00'))+$secondall);
        //         $log->save();
        //         $dandori++;
        //     }
        // }

        $mesinsama2 = DB::SELECT("SELECT
            injection_schedule_logs.*,
            SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 1 ) AS machine_1,
            IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ), 0 ) AS machine_2,
            IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 3 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 3 ), 0 ) AS machine_3 
            FROM
            injection_schedule_logs
            INNER JOIN ( SELECT machine FROM injection_schedule_logs GROUP BY machine HAVING COUNT( machine ) > 1 ) temp ON injection_schedule_logs.machine = temp.machine
            JOIN injection_machine_cycle_times ON injection_machine_cycle_times.part = injection_schedule_logs.part 
            AND injection_machine_cycle_times.color = injection_schedule_logs.color 
            ORDER BY
            injection_schedule_logs.machine,
            IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ), 0 )");

        if (count($mesinsama2) > 0) {
            foreach ($mesin as $key) {
                $mesins = [];
                for ($i=0; $i < count($mesinsama2); $i++) { 
                    if ($mesinsama2[$i]->machine == $key->mesin) {
                        array_push($mesins, $mesinsama2[$i]);
                    }
                }

                for ($j=1; $j < count($mesins); $j++) { 
                    if ($mesins[$j]->machine_2 == 0) {
                        $log = InjectionScheduleLog::where('machine',$mesins[$j]->machine)->get();
                        $end = $log[0]->end_time;
                        if (count($log) > 1) {
                            for ($k=1; $k < count($log); $k++) {
                                $firstDate = date("Y-m-d",strtotime($end)).' '.date('H:i:s',strtotime($end));
                                foreach ($weekly_calendars as $weekly) {
                                    if (str_contains($firstDate,$weekly->week_date)) {
                                        if ($weekly->remark == 'H') {
                                            $firstDate = date('Y-m-d', strtotime($firstDate .' +1 day')).' '.date('H:i:s',strtotime($firstDate));
                                        }
                                    }
                                }
                                $lastDate = date("Y-m-d",strtotime($end)).' '.date('H:i:s',strtotime($end));
                                foreach ($weekly_calendars as $weekly2) {
                                    if (str_contains($lastDate,$weekly2->week_date)) {
                                        if ($weekly2->remark == 'H') {
                                            $lastDate = date('Y-m-d', strtotime($lastDate .' +1 day')).' '.date('H:i:s',strtotime($lastDate));
                                        }
                                    }
                                }
                                $log2 = InjectionScheduleLog::where('id',$log[$k]->id)->first();
                                $ts1 = strtotime($log2->start_time);
                                $ts2 = strtotime($log2->end_time);
                                $seconds_diff = $ts2 - $ts1;
                                $secondall = $seconds_diff+14400;
                                $log2->start_time = date("Y-m-d H:i:s",strtotime($firstDate)+14400);
                                $end_time = date("Y-m-d H:i:s",strtotime($lastDate)+$secondall);
                                $log2->end_time = $end_time;
                                $log2->save();
                                $end = $end_time;
                            }
                        }
                    }else {
                        $log = InjectionScheduleLog::where('machine',$mesins[$j]->machine)->get();
                        $end = $log[0]->end_time;
                        if (count($log) > 1) {
                            for ($l=1; $l < count($log); $l++) {
                                $log2 = InjectionScheduleLog::where('id',$log[$l]->id)->first();
                                $ts1 = strtotime($log2->start_time);
                                $ts2 = strtotime($log2->end_time);
                                $seconds_diff = $ts2 - $ts1;
                                $secondall = $seconds_diff+14400;
                                $firstDate = date("Y-m-d",strtotime($end)).' '.date('H:i:s',strtotime($end));
                                foreach ($weekly_calendars as $weekly) {
                                    if (str_contains($firstDate,$weekly->week_date)) {
                                        if ($weekly->remark == 'H') {
                                            $firstDate = date('Y-m-d', strtotime($firstDate .' +1 day')).' '.date('H:i:s',strtotime($firstDate));
                                        }
                                    }
                                }
                                $lastDate = date("Y-m-d",strtotime($end)).' '.date('H:i:s',strtotime($end));
                                foreach ($weekly_calendars as $weekly2) {
                                    if (str_contains($lastDate,$weekly2->week_date)) {
                                        if ($weekly2->remark == 'H') {
                                            $lastDate = date('Y-m-d', strtotime($lastDate .' +1 day')).' '.date('H:i:s',strtotime($lastDate));
                                        }
                                    }
                                }
                                $log2->start_time = date("Y-m-d H:i:s",strtotime($firstDate)+14400);
                                $end_time = date("Y-m-d H:i:s",strtotime($lastDate)+$secondall);
                                $log2->end_time = $end_time;
                                $log2->save();
                                $end = $end_time;
                            }
                        }
                    }
                }
            }
        }

        $mesinsama3 = DB::SELECT("SELECT
            injection_schedule_logs.*,
            SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 1 ) AS machine_1,
            IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ), 0 ) AS machine_2,
            IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 3 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 3 ), 0 ) AS machine_3 
            FROM
            injection_schedule_logs
            INNER JOIN ( SELECT machine FROM injection_schedule_logs GROUP BY machine HAVING COUNT( machine ) > 1 ) temp ON injection_schedule_logs.machine = temp.machine
            JOIN injection_machine_cycle_times ON injection_machine_cycle_times.part = injection_schedule_logs.part 
            AND injection_machine_cycle_times.color = injection_schedule_logs.color 
            and IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ), 0 ) != 0
            ORDER BY
            injection_schedule_logs.machine,
            IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ), 0 )");

        if (count($mesinsama3) > 0) {
            foreach ($mesin as $key) {
                $mesins = [];
                for ($i=0; $i < count($mesinsama3); $i++) { 
                    if ($mesinsama3[$i]->machine == $key->mesin) {
                        array_push($mesins, $mesinsama3[$i]);
                    }
                }
                for ($j=1; $j < count($mesins); $j++) { 
                    $log = InjectionScheduleLog::where('machine','Mesin '.$mesins[$j]->machine_1)->orderBy('id','desc')->first();
                    if (count($log) > 0) {
                        $end = $log->end_time;
                        if ($mesins[$j]->start_time > $end) {
                            $log2 = InjectionScheduleLog::where('id',$mesins[$j]->id)->first();
                            $log2->machine = 'Mesin '.$mesins[$j]->machine_1;
                            $log2->save();
                        }
                    }
                }
            }
        }

        $mesinsama4 = DB::SELECT("SELECT
            injection_schedule_logs.*,
            SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 1 ) AS machine_1,
            IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ), 0 ) AS machine_2,
            IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 3 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 3 ), 0 ) AS machine_3 
            FROM
            injection_schedule_logs
            INNER JOIN ( SELECT machine FROM injection_schedule_logs GROUP BY machine HAVING COUNT( machine ) > 1 ) temp ON injection_schedule_logs.machine = temp.machine
            JOIN injection_machine_cycle_times ON injection_machine_cycle_times.part = injection_schedule_logs.part 
            AND injection_machine_cycle_times.color = injection_schedule_logs.color 
            and IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ), 0 ) != 0
            ORDER BY
            injection_schedule_logs.machine,
            IF
            ( SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ) != '', SPLIT_STRING ( injection_machine_cycle_times.machine, ',', 2 ), 0 )");

        if (count($mesinsama4) > 0) {
            foreach ($mesin as $key) {
                $end = "";
                $mesins = [];
                for ($i=0; $i < count($mesinsama4); $i++) { 
                    if ($mesinsama4[$i]->machine == $key->mesin) {
                        array_push($mesins, $mesinsama4[$i]);
                    }
                }
                if (count($mesins) > 0) {
                    $end = $mesins[0]->end_time;
                }
                for ($j=1; $j < count($mesins); $j++) { 
                    $log = InjectionScheduleLog::where('id',$mesins[$j]->id)->first();
                    if (count($log) > 0) {
                        $ts1 = strtotime($end);
                        $ts2 = strtotime($log->start_time);
                        $seconds_diff = $ts2 - $ts1;
                        if ($seconds_diff > 14400 || $seconds_diff < 14400) {
                            $ts1 = strtotime($log->start_time);
                            $ts2 = strtotime($log->end_time);
                            $seconds_diff = $ts2 - $ts1;
                            $secondall = $seconds_diff+14400;
                            $firstDate = date("Y-m-d",strtotime($end)).' '.date('H:i:s',strtotime($end));
                            foreach ($weekly_calendars as $weekly) {
                                if (str_contains($firstDate,$weekly->week_date) ) {
                                    if ($weekly->remark == 'H') {
                                        $firstDate = date('Y-m-d', strtotime($firstDate .' +1 day')).' '.date('H:i:s',strtotime($firstDate));
                                    }
                                }
                            }
                            $lastDate = date("Y-m-d",strtotime($end)).' '.date('H:i:s',strtotime($end));
                            foreach ($weekly_calendars as $weekly2) {
                                if (str_contains($lastDate,$weekly2->week_date)) {
                                    if ($weekly2->remark == 'H') {
                                        $lastDate = date('Y-m-d', strtotime($lastDate .' +1 day')).' '.date('H:i:s',strtotime($lastDate));
                                    }
                                }
                            }
                            $log->start_time = date("Y-m-d H:i:s",strtotime($firstDate)+14400);
                            $end_time = date("Y-m-d H:i:s",strtotime($lastDate)+$secondall);
                            $log->end_time = $end_time;
                            $log->save();
                        }else{
                            $end_time = $mesins[$j]->end_time;
                        }
                        $end = $end_time;
                    }
                }
            }
        }

        $mesinsamadandori = DB::SELECT("SELECT
            * 
            FROM
            injection_schedule_logs 
            WHERE
            start_time = CONCAT(
                DATE(
                    NOW()),
                ' 07:00:00')");

        if (count($mesinsamadandori) > 0) {
            $dandori = 0;
            $dandori_time = 0;
            for ($m=0; $m < count($mesinsamadandori); $m++) {
                if ($dandori % 2 == 0) {
                    $dandori_time = $dandori_time + 14400;
                }
                $log = InjectionScheduleLog::where('id',$mesinsamadandori[$m]->id)->first();
                $ts1 = strtotime($log->start_time);
                $ts2 = strtotime($log->end_time);
                $seconds_diff = $ts2 - $ts1;
                $secondall = $seconds_diff+$dandori_time;
                $log->start_time = date("Y-m-d H:i:s",strtotime(date('Y-m-d 07:00:00'))+$dandori_time);
                $end_time = date("Y-m-d H:i:s",strtotime(date('Y-m-d 07:00:00'))+$secondall);
                $log->end_time = $end_time;
                $log->save();
                $logs = InjectionScheduleLog::where('machine',$log->machine)->get();
                $end = $end_time;
                if (count($logs) > 0) {
                    for ($u=1; $u < count($logs); $u++) { 
                        $log2 = InjectionScheduleLog::where('id',$logs[$u]->id)->first();
                        $ts1 = strtotime($log2->start_time);
                        $ts2 = strtotime($log2->end_time);
                        $seconds_diff = $ts2 - $ts1;
                        $secondall = $seconds_diff+14400;
                        $firstDate = date("Y-m-d",strtotime($end)).' '.date('H:i:s',strtotime($end));
                        foreach ($weekly_calendars as $weekly) {
                            if (str_contains($firstDate,$weekly->week_date) ) {
                                if ($weekly->remark == 'H') {
                                    $firstDate = date('Y-m-d', strtotime($firstDate .' +1 day')).' '.date('H:i:s',strtotime($firstDate));
                                }
                            }
                        }
                        $lastDate = date("Y-m-d",strtotime($end)).' '.date('H:i:s',strtotime($end));
                        foreach ($weekly_calendars as $weekly2) {
                            if (str_contains($lastDate,$weekly2->week_date)) {
                                if ($weekly2->remark == 'H') {
                                    $lastDate = date('Y-m-d', strtotime($lastDate .' +1 day')).' '.date('H:i:s',strtotime($lastDate));
                                }
                            }
                        }
                        $log2->start_time = date("Y-m-d H:i:s",strtotime($firstDate)+14400);
                        $end_time = date("Y-m-d H:i:s",strtotime($lastDate)+$secondall);
                        $log2->end_time = $end_time;
                        $log2->save();
                        $end = $end_time;
                    }
                }
                $dandori++;
            }
        }

        $generatemolding = DB::SELECT("SELECT * FROM injection_schedule_logs where id_schedule is null");
        $schedules = [];
        for ($i=0; $i < count($generatemolding); $i++) { 
            $schedules[] = array(
                'id_schedule' => $generatemolding[$i]->id,
                'machine' => $generatemolding[$i]->machine,
                'material_number' => $generatemolding[$i]->material_number,
                'material_description' => $generatemolding[$i]->material_description,
                'part' => $generatemolding[$i]->part,
                'color' => $generatemolding[$i]->color,
                'qty' => $generatemolding[$i]->qty,
                'start_time' => date("Y-m-d H:i:s",strtotime($generatemolding[$i]->start_time)-14400),
                'end_time' => $generatemolding[$i]->start_time,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
        }

        for ($j=0; $j < count($schedules); $j++) {
            DB::table('injection_schedule_molding_logs')->insert([
                $schedules[$j]
            ]);
        }

        $checkshift = DB::SELECT("SELECT
            injection_schedule_logs.*,
            injection_machine_cycle_times.shoot,
            injection_machine_cycle_times.cycle,
            ROUND(( injection_schedule_logs.qty / injection_machine_cycle_times.shoot )* injection_machine_cycle_times.cycle / 3600, 2 ) AS jam,
            FLOOR( ( injection_schedule_logs.qty / injection_machine_cycle_times.shoot )* injection_machine_cycle_times.cycle / 64800 ) AS days,
            ROUND(( MOD ((( injection_schedule_logs.qty / injection_machine_cycle_times.shoot )* injection_machine_cycle_times.cycle ), 64800 ))/ 3600, 2 ) AS hours,
            ( injection_schedule_logs.qty / injection_machine_cycle_times.shoot ) AS jumlah_shoot,
            FLOOR( 64800 / injection_machine_cycle_times.cycle ) AS jumlah_sehari,
            ROUND((
                    injection_schedule_logs.qty / injection_machine_cycle_times.shoot 
                )% FLOOR( 64800 / injection_machine_cycle_times.cycle )) AS sisa 
        FROM
            `injection_schedule_logs`
            LEFT JOIN injection_machine_cycle_times ON injection_machine_cycle_times.part = injection_schedule_logs.part 
            AND injection_machine_cycle_times.color = injection_schedule_logs.color 
        ORDER BY
            injection_schedule_logs.machine,
            start_time ASC");

        $schedules = [];

        $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
        for ($i=0; $i < count($checkshift); $i++) { 
            $moldings = null;
            $now = date('Y-m-d');
            $nextday = date('Y-m-d',strtotime(' +1 day'));
            foreach ($weekly_calendars as $weekly) {
                if ($weekly->week_date == $now) {
                    if ($weekly->remark == 'H') {
                        $now = date('Y-m-d', strtotime($now .' +1 day'));
                        $nextday = date('Y-m-d', strtotime($nextday .' +1 day'));
                    }
                }
            }
            if ($checkshift[$i]->jam >= 18) {
                if ($checkshift[$i]->hours == 0) {
                    for ($j=0; $j < $checkshift[$i]->days; $j++) {
                        $molding = InjectionMoldingMaster::where('status_mesin',$checkshift[$i]->machine)->first();
                        if ((($molding->last_counter/$molding->qty_shot)+$checkshift[$i]->jumlah_sehari) > 15000) {
                            $moldings = 'Ganti Molding';
                        }else{
                            $moldings = null;
                        }
                        $schedules[] = array(
                            'material_number' => $checkshift[$i]->material_number,
                            'material_description' => $checkshift[$i]->material_description,
                            'part' => $checkshift[$i]->part,
                            'color' => $checkshift[$i]->color,
                            'qty' => $checkshift[$i]->jumlah_sehari,
                            'start_time' => date("Y-m-d H:i:s",strtotime($now.' 07:00:00')),
                            'end_time' => date("Y-m-d H:i:s",strtotime($now.' 07:00:00')+($checkshift[$i]->hours*3600)),
                            'machine' => $checkshift[$i]->machine,
                            'molding' => $moldings,
                            'created_by' => 1,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        );
                        $now = date('Y-m-d',strtotime($now .' +1 day'));
                        $nextday = date('Y-m-d',strtotime($nextday .' +1 day'));
                        foreach ($weekly_calendars as $weekly) {
                            if ($weekly->week_date == $now) {
                                if ($weekly->remark == 'H') {
                                    $now = date('Y-m-d', strtotime($now .' +1 day'));
                                    $nextday = date('Y-m-d', strtotime($nextday .' +1 day'));
                                }
                            }
                        }
                    }
                }else{
                    $last_index = $checkshift[$i]->days;
                    for ($j=0; $j < ($checkshift[$i]->days+1); $j++) { 
                        if ($j == $last_index) {
                            $molding = InjectionMoldingMaster::where('status_mesin',$checkshift[$i]->machine)->first();
                            if (count($molding) > 0) {
                                if ((($molding->last_counter/$molding->qty_shot)+$checkshift[$i]->sisa) > 15000) {
                                    $moldings = 'Ganti Molding';
                                }else{
                                    $moldings = null;
                                }
                                $schedules[] = array(
                                    'material_number' => $checkshift[$i]->material_number,
                                    'material_description' => $checkshift[$i]->material_description,
                                    'part' => $checkshift[$i]->part,
                                    'color' => $checkshift[$i]->color,
                                    'qty' => $checkshift[$i]->sisa,
                                    'start_time' => date("Y-m-d H:i:s",strtotime($now.' 07:00:00')),
                                    'end_time' => date("Y-m-d H:i:s",strtotime($now.' 07:00:00')+($checkshift[$i]->hours*3600)),
                                    'machine' => $checkshift[$i]->machine,
                                    'molding' => $moldings,
                                    'created_by' => 1,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                );
                                $now = date('Y-m-d',strtotime($now .' +1 day'));
                                $nextday = date('Y-m-d',strtotime($nextday .' +1 day'));
                                foreach ($weekly_calendars as $weekly) {
                                    if ($weekly->week_date == $now) {
                                        if ($weekly->remark == 'H') {
                                            $now = date('Y-m-d', strtotime($now .' +1 day'));
                                            $nextday = date('Y-m-d', strtotime($nextday .' +1 day'));
                                        }
                                    }
                                }
                            }
                        }else{
                            $molding = InjectionMoldingMaster::where('status_mesin',$checkshift[$i]->machine)->first();
                            if (count($molding) > 0) {
                                if ((($molding->last_counter/$molding->qty_shot)+$checkshift[$i]->jumlah_sehari) > 15000) {
                                    $moldings = 'Ganti Molding';
                                }else{
                                    $moldings = null;
                                }
                                $schedules[] = array(
                                    'material_number' => $checkshift[$i]->material_number,
                                    'material_description' => $checkshift[$i]->material_description,
                                    'part' => $checkshift[$i]->part,
                                    'color' => $checkshift[$i]->color,
                                    'qty' => $checkshift[$i]->jumlah_sehari,
                                    'start_time' => date("Y-m-d H:i:s",strtotime($now.' 07:00:00')),
                                    'end_time' => date("Y-m-d H:i:s",strtotime($nextday.' 01:00:00')),
                                    'machine' => $checkshift[$i]->machine,
                                    'molding' => $moldings,
                                    'created_by' => 1,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                );
                                $now = date('Y-m-d',strtotime($now .' +1 day'));
                                $nextday = date('Y-m-d',strtotime($nextday .' +1 day'));
                                foreach ($weekly_calendars as $weekly) {
                                    if ($weekly->week_date == $now) {
                                        if ($weekly->remark == 'H') {
                                            $now = date('Y-m-d', strtotime($now .' +1 day'));
                                            $nextday = date('Y-m-d', strtotime($nextday .' +1 day'));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }else{
                $schedules[] = array(
                    'material_number' => $checkshift[$i]->material_number,
                    'material_description' => $checkshift[$i]->material_description,
                    'part' => $checkshift[$i]->part,
                    'color' => $checkshift[$i]->color,
                    'qty' => $checkshift[$i]->sisa,
                    'start_time' => date("Y-m-d H:i:s",strtotime($now.' 07:00:00')),
                    'end_time' => date("Y-m-d H:i:s",strtotime($now.' 07:00:00')+($checkshift[$i]->hours*3600)),
                    'machine' => $checkshift[$i]->machine,
                    'created_by' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                );
            }
        }

        InjectionScheduleLog::truncate();

        for ($j=0; $j < count($schedules); $j++) {
            DB::table('injection_schedule_logs')->insert([
                $schedules[$j]
            ]);
        }

        $now = date('Y-m-d');
        $nextday = date('Y-m-d',strtotime(' +1 day'));
        foreach ($weekly_calendars as $weekly) {
            if ($weekly->week_date == $now) {
                if ($weekly->remark == 'H') {
                    $now = date('Y-m-d', strtotime($now .' +1 day'));
                    $nextday = date('Y-m-d', strtotime($nextday .' +1 day'));
                }
            }
        }

        $mesin_sama = DB::SELECT("SELECT
                machine
            FROM
                injection_schedule_logs 
            WHERE
                start_time = CONCAT( '".$now."', ' 07:00:00' ) 
            GROUP BY
                machine 
        HAVING
            count(*) > 1");

        $cek_mesin_sama = DB::SELECT("SELECT injection_schedule_logs.*, ROUND( injection_schedule_logs.qty* injection_machine_cycle_times.cycle 
            ) AS detik 
            FROM
                injection_schedule_logs
                LEFT JOIN injection_machine_cycle_times ON injection_machine_cycle_times.part = injection_schedule_logs.part 
                AND injection_machine_cycle_times.color = injection_schedule_logs.color 
            WHERE
                injection_schedule_logs.machine IN (
                SELECT
                    a.machine 
                FROM
                    injection_schedule_logs a 
                WHERE
                    a.start_time = CONCAT( '".$now."', ' 07:00:00' ) 
                GROUP BY
                    a.machine 
                HAVING
                    count(*) > 1 
                ) 
            ORDER BY
            injection_schedule_logs.machine,
            injection_schedule_logs.color DESC,
            injection_schedule_logs.part,
            injection_schedule_logs.qty DESC");

        $now = date('Y-m-d');
        $nextday = date('Y-m-d',strtotime(' +1 day'));
        foreach ($weekly_calendars as $weekly) {
            if ($weekly->week_date == $now) {
                if ($weekly->remark == 'H') {
                    $now = date('Y-m-d', strtotime($now .' +1 day'));
                    $nextday = date('Y-m-d', strtotime($nextday .' +1 day'));
                }
            }
        }

        for ($i=0; $i < count($mesin_sama); $i++) {
            $end_time = '';
            $mesin_index = 0;
            $nextday = date('Y-m-d',strtotime(' +1 day'));
            foreach ($weekly_calendars as $weekly) {
                if ($weekly->week_date == $nextday) {
                    if ($weekly->remark == 'H') {
                        $nextday = date('Y-m-d', strtotime($nextday .' +1 day'));
                    }
                }
            }
            // $start_time = ;
            for ($j=0; $j < count($cek_mesin_sama); $j++) { 
                if ($mesin_sama[$i]->machine == $cek_mesin_sama[$j]->machine) {
                    $end_time = date('Y-m-d H:i:s',strtotime($now.' 07:00:00')+$cek_mesin_sama[0]->detik);
                    if ($mesin_index == 0) {
                        $updates = InjectionScheduleLog::where('id',$cek_mesin_sama[$j]->id)->first();
                        $updates->start_time = date('Y-m-d H:i:s',strtotime($now.' 07:00:00'));
                        $updates->end_time = date('Y-m-d H:i:s',strtotime($now.' 07:00:00')+$cek_mesin_sama[$j]->detik);
                        $updates->save();
                    }else{
                        $updates = InjectionScheduleLog::where('id',$cek_mesin_sama[$j]->id)->first();
                        $updates->start_time = date('Y-m-d H:i:s',strtotime($nextday.' 07:00:00'));
                        $updates->end_time = date('Y-m-d H:i:s',strtotime($nextday.' 07:00:00')+$cek_mesin_sama[$j]->detik);
                        $updates->save();
                        $nextday = date('Y-m-d',strtotime($nextday.' +1 day'));
                        foreach ($weekly_calendars as $weekly) {
                            if ($weekly->week_date == $nextday) {
                                if ($weekly->remark == 'H') {
                                    $nextday = date('Y-m-d', strtotime($nextday .' +1 day'));
                                }
                            }
                        }
                    }
                    $mesin_index++;
                }
            }
        }


        $schedule_molding = [];

        $machine_log = DB::SELECT("SELECT DISTINCT
                ( machine ) 
            FROM
                injection_schedule_logs 
                -- where machine = 'Mesin 2'
            ORDER BY
                RIGHT (
                machine,
                2)");

        for ($i=0; $i < count($machine_log); $i++) {
            $schedule_log = DB::SELECT("SELECT
                    * 
                FROM
                    injection_schedule_logs 
                WHERE
                    injection_schedule_logs.machine = '".$machine_log[$i]->machine."'
                ORDER BY
                    start_time");

            for ($j=0; $j < count($schedule_log); $j++) {
                $next_schedule = $j+1;
                $datetime1 = new DateTime($schedule_log[$j]->end_time);
                $datetime2 = new DateTime(date("Y-m-d",strtotime($schedule_log[$j]->end_time.' +1 day')).' 01:00:00');
                $interval = $datetime1->diff($datetime2);
                if ($interval->format('%h') > 4 && $schedule_log[$j]->end_time != date("Y-m-d",strtotime($schedule_log[$j]->end_time)).' 01:00:00') {
                    if ($next_schedule < count($schedule_log)) {
                        $schedule_update = InjectionScheduleLog::where('id',$schedule_log[$next_schedule]->id)->first();
                        $datetime1update = new DateTime($schedule_update->start_time);
                        $datetime2update = new DateTime($schedule_update->end_time);
                        $intervalupdate = $datetime1update->diff($datetime2update);
                        $daysInSecs = $intervalupdate->format('%r%a') * 24 * 60 * 60;
                        $hoursInSecs = $intervalupdate->h * 60 * 60;
                        $minsInSecs = $intervalupdate->i * 60;

                        $seconds = $daysInSecs + $hoursInSecs + $minsInSecs + $intervalupdate->s;


                        if ($schedule_update->part == $schedule_log[$j]->part && $schedule_update->color == $schedule_log[$j]->color) {
                            $cek_schedule = InjectionScheduleLog::where('machine',$schedule_update->machine)->where('start_time',date('Y-m-d',strtotime($schedule_log[$j]->end_time)).' 07:00:00')->first();
                            if (count($cek_schedule) > 0) {
                                $firstDate = date('Y-m-d',strtotime($schedule_log[$j]->end_time)).' '.date('H:i:s',strtotime($schedule_log[$j]->end_time));
                                foreach ($weekly_calendars as $weekly) {
                                    if (str_contains($firstDate,$weekly->week_date)) {
                                        if ($weekly->remark == 'H') {
                                            $firstDate = date('Y-m-d', strtotime($firstDate .' +1 day')).' '.date('H:i:s',strtotime($firstDate));
                                        }
                                    }
                                }
                                $lastDate = date('Y-m-d H:i:s',strtotime($schedule_log[$j]->end_time)+$seconds);
                                foreach ($weekly_calendars as $weekly2) {
                                    if (str_contains($lastDate,$weekly2->week_date)) {
                                        if ($weekly2->remark == 'H') {
                                            $lastDate = date('Y-m-d', strtotime($lastDate .' +1 day')).' '.date('H:i:s',strtotime($lastDate));
                                        }
                                    }
                                }
                                $schedule_update->start_time = $firstDate;
                                $schedule_update->end_time = $lastDate;
                            }else{
                                $firstDate = date('Y-m-d',strtotime($schedule_log[$j]->end_time)).' 07:00:00';
                                foreach ($weekly_calendars as $weekly) {
                                    if (str_contains($firstDate,$weekly->week_date)) {
                                        if ($weekly->remark == 'H') {
                                            $firstDate = date('Y-m-d', strtotime($firstDate .' +1 day')).' '.date('H:i:s',strtotime($firstDate));
                                        }
                                    }
                                }
                                $lastDate = date('Y-m-d H:i:s',strtotime(date('Y-m-d',strtotime($schedule_log[$j]->end_time)).' 07:00:00')+$seconds);
                                foreach ($weekly_calendars as $weekly2) {
                                    if (str_contains($lastDate,$weekly2->week_date)) {
                                        if ($weekly2->remark == 'H') {
                                            $lastDate = date('Y-m-d', strtotime($lastDate .' +1 day')).' '.date('H:i:s',strtotime($lastDate));
                                        }
                                    }
                                }
                                $schedule_update->start_time = $firstDate;
                                $schedule_update->end_time = $lastDate;
                            }
                            $schedule_update->save();
                        }else{
                            $cek_schedule = InjectionScheduleLog::where('machine',$schedule_update->machine)->where('start_time',date('Y-m-d',strtotime($schedule_log[$j]->end_time)).' 07:00:00')->first();
                            if (count($cek_schedule) > 0) {
                                $firstDate = date('Y-m-d H:i:s',strtotime($schedule_log[$j]->end_time)+14400);
                                foreach ($weekly_calendars as $weekly) {
                                    if (str_contains($firstDate,$weekly->week_date)) {
                                        if ($weekly->remark == 'H') {
                                            $firstDate = date('Y-m-d', strtotime($firstDate .' +1 day')).' '.date('H:i:s',strtotime($firstDate));
                                        }
                                    }
                                }
                                $lastDate = date('Y-m-d H:i:s',strtotime($schedule_log[$j]->end_time)+($seconds+14400));
                                foreach ($weekly_calendars as $weekly2) {
                                    if (str_contains($lastDate,$weekly2->week_date)) {
                                        if ($weekly2->remark == 'H') {
                                            $lastDate = date('Y-m-d', strtotime($lastDate .' +1 day')).' '.date('H:i:s',strtotime($lastDate));
                                        }
                                    }
                                }
                                $schedule_update->start_time = $firstDate;
                                $schedule_update->end_time = $lastDate;
                                $start_molding = $firstDate;
                                $end_molding = $lastDate;
                            }else{
                                $firstDate = date('Y-m-d',strtotime($schedule_log[$j]->end_time)).' 07:00:00';
                                foreach ($weekly_calendars as $weekly) {
                                    if (str_contains($firstDate,$weekly->week_date)) {
                                        if ($weekly->remark == 'H') {
                                            $firstDate = date('Y-m-d', strtotime($firstDate .' +1 day')).' '.date('H:i:s',strtotime($firstDate));
                                        }
                                    }
                                }
                                $lastDate = date('Y-m-d H:i:s',strtotime(date('Y-m-d',strtotime($schedule_log[$j]->end_time)).' 07:00:00')+$seconds);
                                foreach ($weekly_calendars as $weekly2) {
                                    if (str_contains($lastDate,$weekly2->week_date)) {
                                        if ($weekly2->remark == 'H') {
                                            $lastDate = date('Y-m-d', strtotime($lastDate .' +1 day')).' '.date('H:i:s',strtotime($lastDate));
                                        }
                                    }
                                }
                                $schedule_update->start_time = $firstDate;
                                $schedule_update->end_time = $lastDate;
                                $start_molding = $firstDate;
                                $end_molding = $lastDate;
                            }
                            if (date('H:i:s',strtotime($start_molding)) == '07:00:00') {
                                $firstDate = date("Y-m-d H:i:s",strtotime($start_molding)+14400);
                                foreach ($weekly_calendars as $weekly) {
                                    if (str_contains($firstDate,$weekly->week_date)) {
                                        if ($weekly->remark == 'H') {
                                            $firstDate = date('Y-m-d', strtotime($firstDate .' +1 day')).' '.date('H:i:s',strtotime($firstDate));
                                        }
                                    }
                                }
                                $lastDate = date("Y-m-d H:i:s",strtotime($end_molding)+14400);
                                foreach ($weekly_calendars as $weekly2) {
                                    if (str_contains($lastDate,$weekly2->week_date)) {
                                        if ($weekly2->remark == 'H') {
                                            $lastDate = date('Y-m-d', strtotime($lastDate .' +1 day')).' '.date('H:i:s',strtotime($lastDate));
                                        }
                                    }
                                }
                                $schedule_update->start_time = $firstDate;
                                $schedule_update->end_time = $lastDate;
                                $start_molding = $firstDate;
                                $end_molding = $lastDate;
                            }
                            $molding_log = InjectionScheduleMoldingLog::where('id_schedule',$schedule_update->id)->first();
                            if (count($molding_log) > 0) {
                                $molding_log->forceDelete();
                            }
                            $schedule_molding[] = array(
                                'id_schedule' => $schedule_update->id,
                                'machine' => $schedule_update->machine,
                                'material_number' => $schedule_update->material_number,
                                'material_description' => $schedule_update->material_description,
                                'part' => $schedule_update->part,
                                'color' => $schedule_update->color,
                                'qty' => $schedule_update->qty,
                                'start_time' => date("Y-m-d H:i:s",strtotime($start_molding)-14400),
                                'end_time' => $start_molding,
                                'created_by' => 1,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            );
                            $schedule_update->save();
                        }
                    }
                }
            }
        }
        for ($j=0; $j < count($schedule_molding); $j++) {
            DB::table('injection_schedule_molding_logs')->insert([
                $schedule_molding[$j]
            ]);
        }


        

        $mesin45 = DB::SELECT("SELECT
                machine,
                count( machine ) AS jumlah
            FROM
                injection_schedule_logs 
            WHERE
                ( machine = 'Mesin 4' AND part = 'HJ' AND color = 'IVORY' ) 
                OR ( machine = 'Mesin 5' AND part = 'FJ' AND color = 'IVORY' ) 
            GROUP BY
                machine");

        for ($i=0; $i < count($mesin45); $i++) { 
            if ($mesin45[$i]->machine == 'Mesin 4') {
                if ($mesin45[$i]->jumlah > 10) {
                    $mesin4 = DB::SELECT("SELECT
                        * 
                    FROM
                        injection_schedule_logs 
                    WHERE
                        machine = 'Mesin 4' 
                        AND part = 'HJ' 
                        AND color = 'IVORY' 
                        LIMIT 10,
                        10000");
                    $cek_mesin = InjectionScheduleLog::where('machine','Mesin 11')->get();
                    if (count($cek_mesin)>0 && $cek_mesin[0]->start_time == date('Y-m-d').' 07:00:00') {
                        $last_mesin = $cek_mesin[count($cek_mesin)-1];
                        $firstDate = date('Y-m-d',strtotime($mesin4[0]->start_time));
                        $lastDate = date('Y-m-d',strtotime($last_mesin->end_time.' + 1 day'));
                        foreach ($weekly_calendars as $weekly) {
                            if ($weekly->week_date == $firstDate) {
                                if ($weekly->remark == 'H') {
                                    $firstDate = date('Y-m-d', strtotime($firstDate .' +1 day'));
                                    $lastDate = date('Y-m-d', strtotime($lastDate .' +1 day'));
                                }
                            }
                        }
                        $datetime1 = date_create(date('Y-m-d',strtotime($firstDate)));
                        $datetime2 = date_create(date('Y-m-d',strtotime($lastDate)));
                        $interval = date_diff($datetime1,$datetime2);
                        $minusday = $interval->format('%a')+1;
                    }else{
                        $firstDate = date('Y-m-d',strtotime($mesin4[0]->start_time));
                        $lastDate = date("Y-m-d");
                        foreach ($weekly_calendars as $weekly) {
                            if ($weekly->week_date == $firstDate) {
                                if ($weekly->remark == 'H') {
                                    $firstDate = date('Y-m-d', strtotime($firstDate .' +1 day'));
                                    $lastDate = date('Y-m-d', strtotime($lastDate .' +1 day'));
                                }
                            }
                        }
                        $datetime1 = date_create($firstDate);
                        $datetime2 = date_create($lastDate);
                        $interval = date_diff($datetime1,$datetime2);
                        $minusday = $interval->format('%a');
                    }
                    for ($j=0; $j < count($mesin4); $j++) { 
                        $schedules = InjectionScheduleLog::where('id',$mesin4[$j]->id)->first();
                        $schedules->machine = 'Mesin 11';
                        $firstDate = date("Y-m-d",strtotime($schedules->start_time.' -'.$minusday.' day')).' '.date("H:i:s",strtotime($schedules->start_time));
                        $lastDate = date("Y-m-d",strtotime($schedules->end_time.' -'.$minusday.' day')).' '.date("H:i:s",strtotime($schedules->end_time));
                        foreach ($weekly_calendars as $weekly) {
                            if (str_contains($firstDate,$weekly->week_date )) {
                                if ($weekly->remark == 'H') {
                                    $firstDate = date("Y-m-d",strtotime($firstDate.' +1 day')).' '.date("H:i:s",strtotime($firstDate));
                                    $lastDate = date("Y-m-d",strtotime($firstDate.' +1 day')).' '.date("H:i:s",strtotime($lastDate));
                                }
                            }
                        }
                        $schedules->start_time = date("Y-m-d",strtotime($firstDate)).' '.date("H:i:s",strtotime($firstDate));
                        $schedules->end_time = date("Y-m-d",strtotime($lastDate)).' '.date("H:i:s",strtotime($lastDate));
                        $schedules->save();
                    }
                }
            }

            if ($mesin45[$i]->machine == 'Mesin 5') {
                if ($mesin45[$i]->jumlah > 10) {
                    $mesin5 = DB::SELECT("SELECT
                        * 
                    FROM
                        injection_schedule_logs 
                    WHERE
                        machine = 'Mesin 5' 
                        AND part = 'FJ' 
                        AND color = 'IVORY' 
                        LIMIT 10,
                        10000");
                    $cek_mesin = InjectionScheduleLog::where('machine','Mesin 7')->get();
                    if (count($cek_mesin)>0 && $cek_mesin[0]->start_time == date('Y-m-d').' 07:00:00') {
                        $last_mesin = $cek_mesin[count($cek_mesin)-1];
                        $firstDate = date('Y-m-d',strtotime($mesin5[0]->start_time));
                        $lastDate = date('Y-m-d',strtotime($last_mesin->end_time.' + 1 day'));
                        foreach ($weekly_calendars as $weekly) {
                            if ($weekly->week_date == $firstDate) {
                                if ($weekly->remark == 'H') {
                                    $firstDate = date('Y-m-d', strtotime($firstDate .' +1 day'));
                                    $lastDate = date('Y-m-d', strtotime($lastDate .' +1 day'));
                                }
                            }
                        }
                        $datetime1 = date_create(date('Y-m-d',strtotime($firstDate)));
                        $datetime2 = date_create(date('Y-m-d',strtotime($lastDate)));
                        $interval = date_diff($datetime1,$datetime2);
                        $minusday = $interval->format('%a')+1;
                    }else{
                        $firstDate = date('Y-m-d',strtotime($mesin5[0]->start_time));
                        $lastDate = date("Y-m-d");
                        foreach ($weekly_calendars as $weekly) {
                            if ($weekly->week_date == $firstDate) {
                                if ($weekly->remark == 'H') {
                                    $firstDate = date('Y-m-d', strtotime($firstDate .' +1 day'));
                                    $lastDate = date('Y-m-d', strtotime($lastDate .' +1 day'));
                                }
                            }
                        }
                        $datetime1 = date_create($firstDate);
                        $datetime2 = date_create($lastDate);
                        $interval = date_diff($datetime1,$datetime2);
                        $minusday = $interval->format('%a');
                    }
                    for ($j=0; $j < count($mesin5); $j++) { 
                        $schedules = InjectionScheduleLog::where('id',$mesin5[$j]->id)->first();
                        $schedules->machine = 'Mesin 7';
                        $firstDate = date("Y-m-d",strtotime($schedules->start_time.' -'.$minusday.' day')).' '.date("H:i:s",strtotime($schedules->start_time));
                        $lastDate = date("Y-m-d",strtotime($schedules->end_time.' -'.$minusday.' day')).' '.date("H:i:s",strtotime($schedules->end_time));
                        foreach ($weekly_calendars as $weekly) {
                            if (str_contains($firstDate,$weekly->week_date )) {
                                if ($weekly->remark == 'H') {
                                    $firstDate = date("Y-m-d",strtotime($firstDate.' +1 day')).' '.date("H:i:s",strtotime($firstDate));
                                    $lastDate = date("Y-m-d",strtotime($firstDate.' +1 day')).' '.date("H:i:s",strtotime($lastDate));
                                }
                            }
                        }
                        $schedules->start_time = date("Y-m-d",strtotime($firstDate)).' '.date("H:i:s",strtotime($firstDate));
                        $schedules->end_time = date("Y-m-d",strtotime($lastDate)).' '.date("H:i:s",strtotime($lastDate));
                        $schedules->save();
                    }
                }
            }
        }

        // if (count($checkshift) > 0) {
        //     $mesin = '';
        //     for ($i=0; $i < count($checkshift); $i++) { 
        //         $start_time = $checkshift[$i]->start_time;
        //         $end_time = date('Y-m-d', strtotime("+1 day")).' 01:15:00';
        //         $update = InjectionScheduleLog::where('id',$checkshift[$i]->id)->first();
        //         $update->start_time = $start_time;
        //         $update->end_time = $end_time;
        //         $update->save();

        //         $start_time_new = date('Y-m-d', strtotime("+1 day")).' 07:00:00';
        //         $end_time_new = date("Y-m-d H:i:s",strtotime($checkshift[$i]->end_time)+20700);

        //         $schedules = array(
        //             'material_number' => $checkshift[$i]->material_number,
        //             'material_description' => $checkshift[$i]->material_description,
        //             'part' => $checkshift[$i]->part,
        //             'color' => $checkshift[$i]->color,
        //             'qty' => $checkshift[$i]->qty,
        //             'start_time' => $start_time_new,
        //             'end_time' => $end_time_new,
        //             'machine' => $checkshift[$i]->machine,
        //             'created_by' => 1,
        //             'created_at' => date('Y-m-d H:i:s'),
        //             'updated_at' => date('Y-m-d H:i:s'),
        //         );
        //         DB::table('injection_schedule_logs')->insert([
        //             $schedules
        //         ]);
        //     }
        // }

        // $nextdayplusoneweek = date('Y-m-d',strtotime($nextdayplus1." +7 days"));
        // $nextdayplusoneday = date('Y-m-d',strtotime($nextdayplus1." +1 days"));

        // $data = DB::SELECT("SELECT
        //     injection_part_details.gmc AS material_number,
        //     injection_part_details.part AS material_description,
        //     injection_part_details.part_code,
        //     injection_part_details.color,
        //     ( SELECT quantity FROM injection_inventories WHERE injection_inventories.location = 'RC11' AND injection_inventories.material_number = injection_part_details.gmc ) AS stock,
        //     sum( production_schedules.quantity ) AS plan,
        //     ( SELECT quantity FROM injection_inventories WHERE injection_inventories.location = 'RC11' AND injection_inventories.material_number = injection_part_details.gmc ) - sum( production_schedules.quantity ) AS diff,
        //     IF
        //     ((
        //         SELECT
        //         quantity 
        //         FROM
        //         injection_inventories 
        //         WHERE
        //         injection_inventories.location = 'RC11' 
        //         AND injection_inventories.material_number = injection_part_details.gmc 
        //         ) - sum( production_schedules.quantity ) > 0,
        //     0,-((
        //         SELECT
        //         quantity 
        //         FROM
        //         injection_inventories 
        //         WHERE
        //         injection_inventories.location = 'RC11' 
        //         AND injection_inventories.material_number = injection_part_details.gmc 
        //         ) - sum( production_schedules.quantity ))) AS debt 
        //     FROM
        //     production_schedules
        //     LEFT JOIN materials ON materials.material_number = production_schedules.material_number
        //     LEFT JOIN injection_part_details ON injection_part_details.model = materials.model 
        //     WHERE
        //     DATE( due_date ) BETWEEN '".$nextdayplusoneday."' 
        //     AND '".$nextdayplusoneweek."' 
        //     AND origin_group_code = 072 
        //     GROUP BY
        //     injection_part_details.gmc,
        //     injection_part_details.part,
        //     injection_part_details.part_code,
        //     injection_part_details.color");

        // InjectionScheduleTemp::truncate();

        // foreach ($data as $key) {
        //     if ($key->debt != 0) {
        //         // $partpart = explode(' ',$key->part_code);
        //         // $colorcolor = explode(')',$key->color);

        //         $schedule = InjectionScheduleTemp::create([
        //             'material_number' => $key->material_number,
        //             'date' => date('Y-m-d'),
        //             // 'due_date' => $key->due_date,
        //             'material_description' => $key->material_description,
        //             'part' => $key->part_code,
        //             'color' => $key->color,
        //             'stock' => $key->stock,
        //             'plan' => $key->plan,
        //             'diff' => $key->diff,
        //             'debt' => $key->debt,
        //             'created_by' => '1930',
        //         ]);
        //         $schedule->save();
        //     }
        // }

        // $debttoday = DB::SELECT("SELECT
        //     date,
        //     due_date,
        //     material_number,
        //     material_description,
        //     b.part,
        //     b.color,
        //     stock,
        //     plan,
        //     diff,
        //     SUM( debt ) AS debt,
        //     cycle,
        //     shoot,
        //     qty,
        //     qty_hako,
        //     machine,
        //     CONCAT( 'Mesin ', SPLIT_STRING ( machine, ',', 1 ) ) AS mesin1,
        //     CONCAT( 'Mesin ', SPLIT_STRING ( machine, ',', 2 ) ) AS mesin2,
        //     CONCAT( 'Mesin ', SPLIT_STRING ( machine, ',', 3 ) ) AS mesin3,
        //     ROUND(((( b.debt / b.shoot )* b.cycle )/ 60 )/ 60 ) AS jam,
        //     ROUND((( b.debt / b.shoot )* b.cycle )/ 60 ) AS menit,
        //     ROUND(( b.debt / b.shoot )* b.cycle ) AS detik 
        //     FROM
        //     (
        //         SELECT
        //         date,
        //         due_date,
        //         material_number,
        //         material_description,
        //         a.part,
        //         a.color,
        //         stock,
        //         plan,
        //         diff,
        //         debt,
        //         cycle,
        //         shoot,
        //         qty,
        //         qty_hako,
        //         ( SELECT DISTINCT ( mesin ) FROM injection_molding_masters WHERE injection_molding_masters.product = a.part ) AS machine,
        //         CONCAT( 'Mesin ', SPLIT_STRING ( ( SELECT DISTINCT ( mesin ) FROM injection_molding_masters WHERE injection_molding_masters.product = a.part ), ',', 1 ) ) AS mesin1,
        //         CONCAT( 'Mesin ', SPLIT_STRING ( ( SELECT DISTINCT ( mesin ) FROM injection_molding_masters WHERE injection_molding_masters.product = a.part ), ',', 2 ) ) AS mesin2,
        //         CONCAT( 'Mesin ', SPLIT_STRING ( ( SELECT DISTINCT ( mesin ) FROM injection_molding_masters WHERE injection_molding_masters.product = a.part ), ',', 3 ) ) AS mesin3 
        //         FROM
        //         injection_schedule_temps AS a
        //         LEFT JOIN injection_machine_cycle_times ON injection_machine_cycle_times.part = a.part 
        //         AND injection_machine_cycle_times.color = a.color 
        //         ) b 
        //     GROUP BY
        //     material_number 
        //     ORDER BY
        //     material_number");

        // $mesin = DB::SELECT("SELECT DISTINCT(machine) from injection_schedule_logs");

        // $machine_log = [];
        // foreach ($mesin as $key) {
        //     array_push($machine_log, $key->machine);
        // }

        // for ($i=0; $i < count($debttoday); $i++) { 
        //     if (in_array($debttoday[$i]->mesin1, $machine_log)) {
        //         $get_log = DB::SELECT("SELECT
        //                 * 
        //             FROM
        //             injection_schedule_logs 
        //             WHERE
        //             machine = '".$debttoday[$i]->mesin1."' 
        //             ORDER BY
        //             id DESC 
        //             LIMIT 1");

        //         if ($get_log[0]->material_number == $debttoday[$i]->material_number) {
        //             $start_time = $get_log[0]->end_time;
        //             $end_time = date("Y-m-d H:i:s",strtotime($get_log[0]->end_time)+$debttoday[$i]->detik);
        //             $schedules = array(
        //                 'material_number' => $debttoday[$i]->material_number,
        //                 'material_description' => $debttoday[$i]->material_description,
        //                 'part' => $debttoday[$i]->part,
        //                 'color' => $debttoday[$i]->color,
        //                 'qty' => $debttoday[$i]->qty,
        //                 'start_time' => $start_time,
        //                 'end_time' => $end_time,
        //                 'machine' => $debttoday[$i]->mesin1,
        //                 'created_by' => 1,
        //                 'created_at' => date('Y-m-d H:i:s'),
        //                 'updated_at' => date('Y-m-d H:i:s'),
        //             );
        //             DB::table('injection_schedule_logs')->insert([
        //                 $schedules
        //             ]);
        //         }else{
        //             $secondall = $debttoday[$i]->detik + 14400;
        //             $start_time = date("Y-m-d H:i:s",strtotime($get_log[0]->end_time)+14400);
        //             $end_time = date("Y-m-d H:i:s",strtotime($get_log[0]->end_time)+$secondall);
        //             $schedules = array(
        //                 'material_number' => $debttoday[$i]->material_number,
        //                 'material_description' => $debttoday[$i]->material_description,
        //                 'part' => $debttoday[$i]->part,
        //                 'color' => $debttoday[$i]->color,
        //                 'qty' => $debttoday[$i]->debt,
        //                 'start_time' => $start_time,
        //                 'end_time' => $end_time,
        //                 'machine' => $debttoday[$i]->mesin1,
        //                 'created_by' => 1,
        //                 'created_at' => date('Y-m-d H:i:s'),
        //                 'updated_at' => date('Y-m-d H:i:s'),
        //             );
        //             DB::table('injection_schedule_logs')->insert([
        //                 $schedules
        //             ]);
        //         }
        //     }
        // }

        // $id_schedule = InjectionScheduleLog::where('id_schedule',null)->get();
        // for ($i=0; $i < count($id_schedule); $i++) { 
        //     $log = InjectionScheduleLog::where('id',$id_schedule[$i]->id)->first();
        //     $log->id_schedule = $log->id;
        //     $log->save();
        // }

        // $generatemolding = DB::SELECT("SELECT * FROM injection_schedule_logs");
        // $schedules = [];
        // for ($i=0; $i < count($generatemolding); $i++) { 
        //     if ($generatemolding[$i]->start_time != date('Y-m-d', strtotime("+1 day")).' 07:00:00') {
        //         $schedules[] = array(
        //             'id_schedule' => $generatemolding[$i]->id,
        //             'machine' => $generatemolding[$i]->machine,
        //             'material_number' => $generatemolding[$i]->material_number,
        //             'material_description' => $generatemolding[$i]->material_description,
        //             'part' => $generatemolding[$i]->part,
        //             'color' => $generatemolding[$i]->color,
        //             'qty' => $generatemolding[$i]->qty,
        //             'start_time' => date("Y-m-d H:i:s",strtotime($generatemolding[$i]->start_time)-14400),
        //             'end_time' => $generatemolding[$i]->start_time,
        //             'created_by' => 1,
        //             'created_at' => date('Y-m-d H:i:s'),
        //             'updated_at' => date('Y-m-d H:i:s'),
        //         );
        //     }
        // }

        // for ($j=0; $j < count($schedules); $j++) {
        //     DB::table('injection_schedule_molding_logs')->insert([
        //         $schedules[$j]
        //     ]);
        // }


        
    }
}
